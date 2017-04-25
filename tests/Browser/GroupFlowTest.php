<?php

namespace Tests\Browser;

use App\Models\Contest;
use App\Models\Group;
use App\Models\Problem;
use App\Models\User;
use App\Utilities\Constants;
use App\Utilities\Utilities;
use Faker\Factory;
use Tests\Browser\Pages\Groups;
use Tests\Browser\Pages\Login;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class GroupFlowTest extends DuskTestCase
{
    /**
     * Group flow test
     *
     * @group groups
     * @return void
     */
    public function testGroupFlow()
    {
        sleep(1);
        $faker = Factory::create();

        $this->browse(function (Browser $browser, Browser $browser2) use ($faker) {
            // login
            $browser->visit(new Login)
                ->loginUser('asd', 'asdasd');

            $browser->visit(new Groups)
                ->assertSee('Groups');
            $groupName = $faker->sentence(3);

            //================================================
            // Add group
            //================================================
            // Add group
            // Try invalid (empty) name first
            $browser->visit('http://127.0.0.1:8000/group/new')
                ->type('name', '')
                ->press('Add')
                ->assertPathIs('/group/new')
                ->type('name', $groupName)
                ->press('Add');

            $lastGroup = Group::query()->orderByDesc(Constants::FLD_GROUPS_ID)
                ->first();

            $browser->assertPathIs('/group/' . $lastGroup[Constants::FLD_GROUPS_ID]);

            // Check if group added
            $browser->visit(new Groups);
            $page = 1;
            while (true) {
                try {
                    $browser->assertSee($lastGroup[Constants::FLD_GROUPS_ID]);
                    break;
                } catch (\PHPUnit_Framework_ExpectationFailedException $e) {
                    $browser->visit('http://127.0.0.1:8000/groups?page=' . $page);
                    $page++;
                }
            }

            //================================================
            // Search
            //================================================
            $browser
                ->visit(new Groups)
                ->click('.group-search-icon')
                ->pause(300)
                ->type('#name', $groupName)
                ->press('Search')
                ->on(new Groups)
                ->assertQueryStringHas('name', $groupName)
                ->assertSee($groupName);

            //================================================
            // Edit group
            //================================================
            $browser->visit('http://127.0.0.1:8000/group/' . $lastGroup[Constants::FLD_GROUPS_ID])
                ->assertSee(':: asd')// Check owner
                ->clickLink('Edit')
                ->assertPathIs('/group/edit/' . $lastGroup[Constants::FLD_GROUPS_ID])
                ->assertInputValue('#name', $groupName)
                ->type('#name', 'NewGroupName')
                ->press('Edit')
                ->assertPathIs('/group/' . $lastGroup[Constants::FLD_GROUPS_ID])
                ->assertSee('NewGroupName');

            //================================================
            // Invite members and check if invited using
            // browser 2, then accept and check with b1
            // for members table change
            //================================================
            $username1 = User::find(34)[Constants::FLD_USERS_USERNAME];
            $browser->script([
                "$('#usernames').attr('type', 'text');"
            ]);
            $browser->pause(500)->type('#usernames', $username1);

            $browser->press('Invite');
            $browser2->visit(new Login)
                ->loginUser($username1, 'asdasd');

            // Check notification panel
            $browser2->click('#testing-notification-link');

            $browser2->assertSeeIn('.notification-text', 'NewGroupName');

            // Click notification to join
            $allNotifications = $browser2->elements('.notification-text');
            foreach ($allNotifications as $notification) {
                if (str_contains($notification->getText(),
                    'NewGroupName')) {
                    $notification->click();
                    break;
                }
            }
            $browser2->assertPathIs('/group/' . $lastGroup[Constants::FLD_GROUPS_ID]);
            $browser2->press('Join');

            // Should be member now
            $browser->refresh()
                ->assertSee($username1);

            //================================================
            // Add group contest and check with browser 2 for
            // group contests table, and check it visibility
            // and check invitations sent to group members
            //================================================

            $browser->click('#testing-contests-link')
                ->click('#testing-group-new-contest-link')
                ->type('name', 'NewGroupContest')
                ->type('time', '2017-04-24 08:24:00')
                ->script([
                    "$('#duration').show()",
                    "$('#duration').val(10000)"
                ]);
            $browser->check('#problem-checkbox-1');
            $browser->press('Add');

            $browser2->refresh()
                ->click('#testing-contests-link')
                ->assertSee('NewGroupContest');

            // Check invitations for member terrill49
            $browser2->click('#testing-notification-link');

            $browser2->assertSeeIn('.notification-text', 'NewGroupContest');

            $groupContest = Contest::query()->orderBy(Constants::FLD_CONTESTS_ID, 'desc')->first();
            $browser2
                ->visit('http://127.0.0.1:8000/contest/' . $groupContest[Constants::FLD_CONTESTS_ID])
                ->assertPathIs('/contest/' . $groupContest[Constants::FLD_CONTESTS_ID]);
            $browser2->assertSee('Join');

            //================================================
            // Add sheet
            //================================================
            $browser
                ->visit('http://127.0.0.1:8000/group/' . $lastGroup[Constants::FLD_GROUPS_ID])
                ->click('#testing-sheets-link')
                ->clickLink('New Sheet')
                ->assertPathIs('/sheet/new/' . $lastGroup[Constants::FLD_GROUPS_ID])
                ->type('name', 'NewGroupSheet')
                ->check('#problem-checkbox-1')
                ->press('Add')
                ->assertPathIs('/group/' . $lastGroup[Constants::FLD_GROUPS_ID])
                ->click('#testing-sheets-link')
                ->assertSee('NewGroupSheet');

            //================================================
            // Edit sheet
            //================================================
            $browser->click('.testing-edit-sheet')
                ->assertInputValue('#name', 'NewGroupSheet')
                ->assertChecked('#problem-checkbox-1')
                ->type('#name', 'NewGroupSheetEdited')
                ->uncheck('#problem-checkbox-1')
                ->check('#problem-checkbox-3')
                ->check('#problem-checkbox-4')
                ->press('Edit')
                ->assertSee('NewGroupSheetEdited')
                ->assertSee(Utilities::generateProblemNumber(Problem::find(3)))
                ->assertSee(Utilities::generateProblemNumber(Problem::find(4)));

            //================================================
            // Edit sheet solution and check using browser 2
            //================================================
            $browser->click('#testing-solution-btn-problem-3')
                ->script(['$("#problem-solution").show()']);

            $browser->type('#problem-solution', 'Solution123321')
                ->type('#code-editor', 'Solution123321')
                ->select('#solution_lang', 'java')
                ->click('#answer-model-submit-button')
                ->click('#testing-solution-btn-problem-3')
                ->assertSee('Solution123321')
                ->assertSelected('#solution_lang', 'java');

            // check using browser 2
            $browser2->visit('http://127.0.0.1:8000/group/' . $lastGroup[Constants::FLD_GROUPS_ID])
                ->click('#testing-sheets-link')
                ->clickLink('NewGroupSheet')
                ->click('#testing-solution-btn-problem-3')
                ->assertSee('Solution123321');

            //================================================
            // Delete sheet
            //================================================
            $browser
                ->visit('http://127.0.0.1:8000/group/' . $lastGroup[Constants::FLD_GROUPS_ID])
                ->click('#testing-sheets-link')
                ->click('.testing-delete-sheet')
                ->acceptDialog()
                ->assertDontSee('NewGroupSheetEdited');

            //================================================
            // Ask to join -> send invite
            // send invite -> ask to join
            // Send request -> get rejected
            //================================================
            $username2 = User::find(45)[Constants::FLD_USERS_USERNAME];
            $browser2
                ->clickLink('Logout')
                ->visit(new Login)
                ->loginUser($username2, 'asdasd');

            $browser2->visit('http://127.0.0.1:8000/group/' . $lastGroup[Constants::FLD_GROUPS_ID])
                ->press('Join');

            $browser->visit('http://127.0.0.1:8000/group/' . $lastGroup[Constants::FLD_GROUPS_ID])
                ->script([
                    "$('#usernames').attr('type', 'text');"
                ]);
            $browser->pause(500)
                ->type('#usernames', $username2);
            $browser->press('Invite');

            // Check to see Leave button, to make sure already member
            $browser2->refresh()->assertSee('Leave');

            // Test leaving
            $browser2->press('Leave')
                ->acceptDialog()
                ->assertSee('Join');

            // Send invite first then request to join
            $browser->script([
                "$('#usernames').attr('type', 'text');"
            ]);
            $browser->type('#usernames', $username2);
            $browser->press('Invite');
            $browser2->visit('http://127.0.0.1:8000/group/' . $lastGroup[Constants::FLD_GROUPS_ID])
                ->press('Join')
                ->assertSee('Leave');
            $browser2->press('Leave')
                ->acceptDialog()
                ->assertSee('Join')
                ->press('Join')
                ->assertSee('Request Sent');

            // Refuse request
            $browser->refresh()
                ->click('#testing-requests-link')
                ->click('#testing-reject-request-45');

            // Sent request again
            $browser2->refresh()
                ->waitForText('Join')
                ->assertSee('Join')
                ->press('Join')->assertSee('Request Sent');

            // Accept request
            $browser->refresh()->clickLink('Requests')
                ->click('#testing-accept-request-45');

            $browser2->refresh()->assertSee('Leave');

            //================================================
            // Remove group member and check 401 for b2
            //================================================
            $browser
                ->refresh()
                ->click('#testing-members-link')
                ->click('#testing-remove-member-45')
                ->acceptDialog()
                ->assertDontSee($username2);
            $browser2
                ->refresh()
                ->waitForText('Join');

            //================================================
            // Delete group
            //================================================
            $browser->press('Delete')
                ->acceptDialog()
                ->visit('http://127.0.0.1:8000/group/' . $lastGroup[Constants::FLD_GROUPS_ID])
                ->assertPathIs('/errors/404');

        });
    }
}
