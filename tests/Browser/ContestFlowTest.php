<?php

namespace Tests\Browser;

use App\Models\Contest;
use App\Models\Problem;
use App\Models\User;
use App\Utilities\Constants;
use App\Utilities\Utilities;
use Carbon\Carbon;
use Facebook\WebDriver\Exception\NoAlertOpenException;
use Facebook\WebDriver\Exception\UnexpectedAlertOpenException;
use Tests\Browser\Pages\AddContestPage;
use Tests\Browser\Pages\Contests;
use Tests\Browser\Pages\Login;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class ContestFlowTest extends DuskTestCase
{
    /**
     * Test contest flow
     *
     * @group contests
     * @return void
     */
    public function testContestFlow()
    {
        sleep(1);
        $faker = \Faker\Factory::create();
        $validContestDate = Carbon::createFromFormat("Y-m-d H:i:s", Carbon::now()->addDays(10))->toDateTimeString();

        $this->browse(function (Browser $browser, Browser $browser2) use ($faker, $validContestDate) {
            // Get some users for testing
            $username0 = User::find(2)[Constants::FLD_USERS_USERNAME];
            $username1 = User::find(33)[Constants::FLD_USERS_USERNAME];
            $username2 = User::find(44)[Constants::FLD_USERS_USERNAME];
            $username3 = User::find(23)[Constants::FLD_USERS_USERNAME];
            $username4 = User::find(32)[Constants::FLD_USERS_USERNAME];
            $username5 = User::find(12)[Constants::FLD_USERS_USERNAME];
            $username6 = User::find(14)[Constants::FLD_USERS_USERNAME];
            $asd = 'asd';

            // Visit contests page
            $browser->visit(new Contests)
                ->assertSee('Contests');

            // Add new contest for non user
            $browser->clickLink('New')
                ->assertPathIs(route(Constants::ROUTES_AUTH_LOGIN, [], false));

            // Login
            $browser->visit(new Login)
                ->loginUser($asd, 'asdasd');


            // Visit new contest page and move to add new contest page
            $browser->visit(new Contests)
                ->clickLink('New')
                ->assertSee('Add')
                ->assertPathIs(route(Constants::ROUTES_CONTESTS_CREATE, [], false))
                ->on(new AddContestPage);

            // empty data
            $browser->press('Add')
                ->on(new AddContestPage);

            // invalid data (empty name)
            $browser->saveContest('', $validContestDate, 1000, 0);
            $browser->on(new AddContestPage);

            // invalid data (invalid date)
            $browser->saveContest('ADS2', '2017-04-02 12:12:12', 1000, 0);
            $browser->on(new AddContestPage);

            $browser->saveContest('ADS2', '2017-04-02 12:12:12', -10, 0);
            $browser->on(new AddContestPage);

            // valid data
            $browser->saveContest('ADS2', $validContestDate, 1000, 0);
            $browser->on(new AddContestPage);

            $lastContest = Contest::query()->orderBy(Constants::FLD_CONTESTS_ID, 'desc')->first();

            // Get last id to check path
            $browser->assertPathIs(route(Constants::ROUTES_CONTESTS_DISPLAY, $lastContest[Constants::FLD_CONTESTS_ID], false));

            // ================================================================
            // Insert contest with organizers and select problems
            // ================================================================

            $browser->visit(new Contests)
                ->clickLink('New')
                ->on(new AddContestPage);

            $organizers = User::find([2, 3, 4])->pluck(Constants::FLD_USERS_USERNAME);
            $browser->saveContest('ADS2', $validContestDate, 1000, 0, [], $organizers, []);

            // Get last id to check path
            $lastContest = Contest::query()->orderBy(Constants::FLD_CONTESTS_ID, 'desc')->first();
            $browser->assertPathIs(route(Constants::ROUTES_CONTESTS_DISPLAY, $lastContest[Constants::FLD_CONTESTS_ID], false));

            // =================================================================
            // Full Contest with filters applied
            // =================================================================

            // Test apply filters
            $browser->visit(new AddContestPage)
                ->on(new AddContestPage)
                ->applyFilters(["math"], [1])
                // Check that filters is maintained
                ->on(new AddContestPage)
                ->pause(100)
                ->assertSeeIn(".problems-table-tag-link", "math")
                ->assertSeeIn("#tags-list", "math");

            // Select problems and save
            $contestName = $faker->sentence(2);
            $browser->saveContest($contestName, $validContestDate, 1000, 0, [1, 2, 3], $organizers, []);

            // Get last id to check path
            $lastContest = Contest::query()->orderBy(Constants::FLD_CONTESTS_ID, 'desc')->first();
            $browser->assertPathIs(route(Constants::ROUTES_CONTESTS_DISPLAY, $lastContest[Constants::FLD_CONTESTS_ID], false));

            // Check if in contests table
            $browser->visit(new Contests)
                ->clickLink('Upcoming');
            $page = 1;
            while (true) {
                try {
                    $browser->assertSee($lastContest[Constants::FLD_CONTESTS_ID]);
                    break;
                } catch (\PHPUnit_Framework_ExpectationFailedException $e) {
                    $browser->visit(route(Constants::ROUTES_CONTESTS_INDEX) . '?page=' . $page);
                    $page++;
                }
            }

            // Check using other browser
            // Login browser 2
            $browser2->visit(new Login)
                ->loginUser($username0, 'asdasd');

            // User 2 can see th contest cuz it's public
            $browser2->visit(route(Constants::ROUTES_CONTESTS_DISPLAY, $lastContest[Constants::FLD_CONTESTS_ID]));

            // Check contest name
            $browser2->assertSee($contestName);
            // Check organizers added
            $browser2->assertSeeIn('.organizers-p', User::find(2)[Constants::FLD_USERS_USERNAME])
                ->assertSeeIn('.organizers-p', User::find(3)[Constants::FLD_USERS_USERNAME])
                ->assertSeeIn('.organizers-p', User::find(4)[Constants::FLD_USERS_USERNAME])
                ->assertSeeIn('.owner-p', $asd)
                ->assertSeeIn('.duration-p', Utilities::convertSecondsToDaysHoursMins(1000));

            // Check contest problems
            $problem1Number = Utilities::generateProblemNumber(Problem::find(1));
            $problem2Number = Utilities::generateProblemNumber(Problem::find(2));
            $problem3Number = Utilities::generateProblemNumber(Problem::find(3));

            $browser2->assertSee($problem1Number)
                ->assertSee($problem2Number)
                ->assertSee($problem3Number);

            // ToDo team has to join and check for it

            //===========================================================
            // Try invalid combinations in adding contest
            //===========================================================

            // Add myself as organizer
            $contestName = $faker->sentence(2);

            // No error should happen, we just have to check that I'm not listed as organizer in browser 2
            $browser->visit(new AddContestPage)
                ->saveContest($contestName, $validContestDate, 20 * 24 * 3600, 0, [1, 2, 3], [$asd, $username1], []);

            $publicContest = Contest::query()->orderBy(Constants::FLD_CONTESTS_ID, 'desc')->first();
            $browser->assertPathIs(route(Constants::ROUTES_CONTESTS_DISPLAY, $publicContest[Constants::FLD_CONTESTS_ID], false));

            // Use browser 2 to check

            $browser2->visit(route(Constants::ROUTES_CONTESTS_DISPLAY, $publicContest[Constants::FLD_CONTESTS_ID]));
            // Check contest name
            $browser2->assertSee($contestName);
            if ($browser->element('.organizers-p'))
                $browser->assertDontSeeIn('.organizers-p', $asd);

            //===========================================================
            // Select more than 10 check boxes
            //===========================================================
            $contestName = $faker->sentence(2);
            $problems = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];

            // Alert for more than 10
            try {
                $browser->visit(new AddContestPage)
                    ->saveContest($contestName, $validContestDate, 1000, 0, $problems, [$asd, $username1], []);
                $this->fail("Shouldn't reach here without alertbox!");
            } catch (UnexpectedAlertOpenException $e) {
                $browser->acceptDialog();
            }

            //===========================================================

            // clear all sessions (some session is left from the last attempt to save invalid contest)
            $browser->script(['app.clearSession();']);

            // ==========================================================
            // Test private contests
            // ==========================================================
            $browser->visit(new AddContestPage)
                ->saveContest($contestName, $validContestDate, 20 * 24 * 3600, 1, [1, 2, 3, 4], [$asd, $username1], [$username2, $username3]);
            sleep(1);
            $privateContest = Contest::query()->orderBy(Constants::FLD_CONTESTS_ID, 'desc')->first();
            $browser->assertPathIs(route(Constants::ROUTES_CONTESTS_DISPLAY, $privateContest[Constants::FLD_CONTESTS_ID], false));

            // Check invitations sent (in notification panel and
            // the user in browser 2 can see the contest page)

            // normal user cannot see it
            $browser2->visit(route(Constants::ROUTES_CONTESTS_DISPLAY, $privateContest[Constants::FLD_CONTESTS_ID]));
            $browser2->assertPathIs(route(Constants::ROUTES_ERRORS_401, [], false));

            // Logout
            $browser2->clickLink('Logout');


            // Login as cummings.antonetta
            $browser2->visit(new Login)
                ->loginUser($username2, 'asdasd');

            // Visit page again

            $browser2->visit(route(Constants::ROUTES_CONTESTS_DISPLAY, $privateContest[Constants::FLD_CONTESTS_ID]));
            $browser2->assertPathIs(route(Constants::ROUTES_CONTESTS_DISPLAY, $privateContest[Constants::FLD_CONTESTS_ID], false));

            // Check notification panel
            $browser2->refresh()
                ->click('#testing-notification-link');

            $browser2->assertSeeIn('.notification-text', $contestName);

            // ==========================================================
            // Contest requests/notifications/participation test
            // browser1 -> asd
            // browser2 -> cummings.antonetta (invited)
            // ==========================================================

            // Join public contest

            // Logout asd
            $browser->clickLink('Logout');

            // visit login page and login this normal user with id 13
            $browser->visit(new Login)
                ->loginUser($username3, 'asdasd');

            // Visit contest page
            $browser->visit(route(Constants::ROUTES_CONTESTS_DISPLAY, $publicContest[Constants::FLD_CONTESTS_ID]))
                ->assertPathIs(route(Constants::ROUTES_CONTESTS_DISPLAY, $publicContest[Constants::FLD_CONTESTS_ID], false));

            // Click join link
            $browser->click('#testing-contest-join-btn');

            $browser->assertSee('Leave'); // Check if Leave link appears

            // Check if participant
            $browser->clickLink('Participants');

            // Check if is participant
            $browser->assertSee($username3);

            // ==========================================================

            // Leave contest
            $browser->click('#testing-contest-leave-btn');

            // Dismiss leave process
            $browser->driver->switchTo()->alert()->dismiss();

            $browser->assertSee('Leave'); // Check if Leave link still appears
            $browser->click('#testing-contest-leave-btn');
            $browser->driver->switchTo()->alert()->accept();

            // Check if leaved
            $browser->assertSee('Join');

            // Check if not participant anymore
            $browser->clickLink('Participants');
            if ($browser->element('.testing-participant-username'))
                $browser->assertDontSeeIn('.testing-participant-username', $username3);
            // ==========================================================

            // Accept invitation for private contest
            $browser->click('#testing-notification-link');

            $browser->assertSeeIn('.notification-text', $contestName);

            $allNotifications = $browser->elements('.notification-text');
            foreach ($allNotifications as $notification) {
                if (str_contains($notification->getText(),
                    $privateContest[Constants::FLD_CONTESTS_NAME])) {
                    $notification->click();
                    break;
                }
            }
            $browser->assertPathIs(route(Constants::ROUTES_CONTESTS_DISPLAY, $privateContest[Constants::FLD_CONTESTS_ID], false));

            // Click join link
            $browser->click('#testing-contest-join-btn');

            $browser->assertSee('Leave'); // Check if Leave link appears

            // Check if participant
            $browser->clickLink('Participants');

            // Check if is participant
            $browser->assertSee($username3);

            // ==========================================================
            // Contest questions test
            // ==========================================================
            // move the contest back in time such that the question part is seen
            \DB::table(Constants::TBL_CONTESTS)
                ->where(Constants::FLD_CONTESTS_ID, $privateContest[Constants::FLD_CONTESTS_ID])
                ->update(['time' => '2017-04-23 12:12:12']);

            // Ask question
            $browser->refresh()
                ->clickLink('Questions')
                ->type('#title', $title = $faker->sentence())
                ->select('#problem_id', '2')
                ->type('#content', $content = $faker->realText())
                ->click('#testing-ask-question-btn')
                ->clickLink('Questions')
                ->assertSee($title)
                ->assertSee($content)
                ->assertSee(Utilities::generateProblemNumber(Problem::find(2)))
                // Not organizer, must not see the actions buttons
                ->assertMissing('.testing-question-action-button');

            // Organizer answers question
            // Logout first
            $browser2->clickLink('Logout');

            // Organizer
            $browser2->visit(new Login)
                ->loginUser($username1, 'asdasd')
                ->visit(route(Constants::ROUTES_CONTESTS_DISPLAY, $privateContest[Constants::FLD_CONTESTS_ID]))
                ->clickLink('Questions')
                ->assertSee($title)
                ->assertSee($content)
                ->assertSee(Utilities::generateProblemNumber(Problem::find(2)))
                ->assertVisible('.testing-question-action-button.answer')
                ->click('.testing-question-action-button.answer')
                ->waitFor('#question-answer')
                ->type('#question-answer', $answer = $faker->realText())
                ->click('#answer-model-submit-button')
                ->clickLink('Questions')
                ->assertSee($answer);

            // Check on user side
            $browser->refresh()
                ->pause(300)
                ->clickLink('Questions')
                ->assertSee($answer);

            // Organizer edits question answer as announcement
            $browser2->click('.testing-question-action-button.answer')
                ->pause(1000)
                ->type('#question-answer', $answer = $faker->realText())
                ->click('#answer-model-submit-button')
                ->clickLink('Questions')
                ->assertSee($answer);

            // Check on user side again
            $browser->refresh()
                ->pause(300)
                ->clickLink('Questions')
                ->assertSee($answer);

            // Organizer marks question as announcement
            $browser2->waitFor('.testing-question-action-button.announce')
                ->click('.testing-question-action-button.announce');

            // Check on user side again
            $browser->refresh()
                ->pause(300)
                ->clickLink('Questions')
                ->assertVisible('.announcement');

            // Renounce
            $browser2->click('.testing-question-action-button.renounce');

            // Check on user side again
            $browser->refresh()
                ->pause(300)
                ->clickLink('Questions')
                ->assertMissing('.announcement');

            // Leave private contest
            $browser->click('#testing-contest-leave-btn');
            $browser->driver->switchTo()->alert()->accept();

            $browser->visit(route(Constants::ROUTES_CONTESTS_DISPLAY, $privateContest[Constants::FLD_CONTESTS_ID]));
            $browser->assertPathIsNot(route(Constants::ROUTES_ERRORS_401, [], false));

            // ==========================================================
            // Editing contest test OMG!
            // I will be editing privateContest only (which covers the public
            // part)
            // ==========================================================

            // Logout
            $browser->clickLink('Logout');

            // Login as asd
            $browser->visit(new Login)
                ->loginUser($asd, 'asdasd');

            // Visit private contest page
            $browser->visit(route(Constants::ROUTES_CONTESTS_DISPLAY, $privateContest[Constants::FLD_CONTESTS_ID]));
            $browser->assertPathIs(route(Constants::ROUTES_CONTESTS_DISPLAY, $privateContest[Constants::FLD_CONTESTS_ID], false));

            // Click edit
            $browser->clickLink('Edit');

            // Check autoFill (that contest data is available here)
            //saveContest($contestName, $validContestDate, 10000, 1, [1, 2, 3, 4], [$asd, $username1], [$username2, $username3]);
            $browser->assertInputValue('#name', $contestName)
                ->assertInputValue('#time', '2017-04-23 12:12:12')
                ->script(["$('#duration').show();"]);
            $browser
                ->waitFor('#duration')
                ->pause(1000)
                ->assertInputValue('#duration', 10000 * 60)
                ->assertRadioSelected('visibility', 1)
                ->assertSeeIn('#organisers-list', $username1);

            // ==========================================================================
            // Save empty data
            // ==========================================================================

            $browser->type('#name', '')
                ->press('Save');

            $browser->type('#name', 'NewContestName');

            // Check not saved via other browser
            // Logout
            $browser2->clickLink('Logout');

            // Login as other user
            $browser2->visit(new Login)
                ->loginUser($username1, 'asdasd');

            // Visit page again
            $browser2->visit(route(Constants::ROUTES_CONTESTS_DISPLAY, $privateContest[Constants::FLD_CONTESTS_ID]));
            $browser2->assertPathIs(route(Constants::ROUTES_CONTESTS_DISPLAY, $privateContest[Constants::FLD_CONTESTS_ID], false));
            $browser2->assertSee($contestName);


            // ==========================================================================
            // Save invalid dateTime
            // ==========================================================================

            $browser->type('#time', '2017-03-23 14:00:12')
                ->press('Save');

            $browser->type('#time', $validContestDate);

            // Visit page again
            $browser2->visit(route(Constants::ROUTES_CONTESTS_DISPLAY, $privateContest[Constants::FLD_CONTESTS_ID]));
            $browser2->assertPathIs(route(Constants::ROUTES_CONTESTS_DISPLAY, $privateContest[Constants::FLD_CONTESTS_ID], false));
            $browser2->assertSee(date('D M d, H:i', strtotime('2017-04-23 12:12:12')));

            // ==========================================================================
            // Add($username4)/remove(mitchell.otha) organizers (check with browser 2)
            // ==========================================================================
            $browser->click('.remove-btn');

            $browser->script(['sessionStorage.setItem(app.organizersSessionKey, \'["' . $username4 . '"]\')']);

            $browser->press('Save');
            $browser->visit(route(Constants::ROUTES_CONTESTS_EDIT, $privateContest[Constants::FLD_CONTESTS_ID]));

            // Check saved via b2
            $browser2->refresh()
                ->assertPathIs(route(Constants::ROUTES_ERRORS_401, [], false))
                ->clickLink('Logout')
                // Login as mitchell.otha
                ->visit(new Login)
                ->loginUser($username4, 'asdasd');

            // Visit page again to check if user can reach
            $browser2->visit(route(Constants::ROUTES_CONTESTS_DISPLAY, $privateContest[Constants::FLD_CONTESTS_ID]));
            $browser2->assertPathIs(route(Constants::ROUTES_CONTESTS_DISPLAY, $privateContest[Constants::FLD_CONTESTS_ID], false));

            // Check organizers
            $browser2
                ->assertDontSeeIn('.organizers-p', $username1)
                ->assertSeeIn('.organizers-p', $username4);


            // ==========================================================================
            // Apply filters -> check if maintained
            // ==========================================================================

            // Click judges
            $browser->check('#judge-checkbox-1');

            // Save to session
            $browser->script(['sessionStorage.setItem(app.tagsSessionKey, \'["math","dp"]\')']);
            // Apply filters
            $browser->click('#apply-filters');
            $browser
                ->assertSee("math")
                ->assertSee("dp")
                ->assertSeeIn("#tags-list", "math")
                ->assertSeeIn("#tags-list", "dp");

            // Clear filters
            // Clear judges
            $browser->uncheck('#judge-checkbox-1')
                ->uncheck('#judge-checkbox-2')
                ->uncheck('#judge-checkbox-3');

            // Clear tags session
            $browser->script(['sessionStorage.setItem(app.tagsSessionKey, \'\')']);
            $browser->click('#apply-filters');

            // ==========================================================================
            // change problems
            // ==========================================================================

            $browser->waitFor('#problem-checkbox-1')
                ->uncheck('#problem-checkbox-1')
                ->check('#problem-checkbox-6');

            $browser->press('Save');

            $browser->visit(route(Constants::ROUTES_CONTESTS_EDIT, $privateContest[Constants::FLD_CONTESTS_ID]));

            // check using b 2 to see if problems have changed
            $problem1Number = Utilities::generateProblemNumber(Problem::find(1));
            $problem6Number = Utilities::generateProblemNumber(Problem::find(6));

            $browser2->refresh()
                ->assertDontSee($problem1Number)
                ->assertSee($problem6Number);

            // ==========================================================================
            // Select more than 10 problems
            // ==========================================================================
            $problems = [17, 16, 15, 14, 13, 7, 8, 9, 10, 11, 12];

            // Check problems boxes
            foreach ($problems as $problemsID) {
                $browser->check('#problem-checkbox-' . $problemsID);
                try {
                    $browser->acceptDialog();
                } catch (NoAlertOpenException $e) {

                }
            }
            foreach ($problems as $problemsID) {
                $browser->uncheck('#problem-checkbox-' . $problemsID);
            }
            // ==========================================================================
            // invite others (maximus.okon) and change name check with browser 2
            // ==========================================================================
            $browser->script(['sessionStorage.setItem(app.inviteesSessionKey, \'["' . $username5 . '"]\')']);
            $browser->type('#name', 'NewContestName');
            $browser->press('Save');

            $browser->assertSee('NewContestName');

            $browser->visit(route(Constants::ROUTES_CONTESTS_EDIT, $privateContest[Constants::FLD_CONTESTS_ID]));

            // Check not saved via other browser
            // Logout
            $browser2->clickLink('Logout');

            $browser2->visit(new Login)
                ->loginUser($username5, 'asdasd');

            // Visit page again
            $browser2->visit(route(Constants::ROUTES_CONTESTS_DISPLAY, $privateContest[Constants::FLD_CONTESTS_ID]));
            $browser2->assertPathIs(route(Constants::ROUTES_CONTESTS_DISPLAY, $privateContest[Constants::FLD_CONTESTS_ID], false));


            // Check notification panel
            $browser2->click('#testing-notification-link');

            $browser2->assertSeeIn('.notification-text', 'NewContestName');

            // ==========================================================================
            // change visibility
            // ==========================================================================
            $browser->script(['$("#private_visibility").prop(\'checked\', false);$("#public_visibility").prop(\'checked\', true);']);
            $browser->pause(300)->press('Save');
            $browser->visit(route(Constants::ROUTES_CONTESTS_EDIT, $privateContest[Constants::FLD_CONTESTS_ID]));

            // ==========================================================================
            // try to join by (georgette.daniel)
            // ==========================================================================
            // Logout
            $browser2->clickLink('Logout');

            $browser2->visit(new Login)
                ->loginUser($username6, 'asdasd');

            // Visit page again
            $browser2->visit(route(Constants::ROUTES_CONTESTS_DISPLAY, $privateContest[Constants::FLD_CONTESTS_ID]));
            $browser2->assertPathIs(route(Constants::ROUTES_CONTESTS_DISPLAY, $privateContest[Constants::FLD_CONTESTS_ID], false));

            // ==========================================================
            // Reorder contest test
            // ==========================================================
            $browser->visit(route(Constants::ROUTES_CONTESTS_DISPLAY, $privateContest[Constants::FLD_CONTESTS_ID]));
            $browser->assertPathIs(route(Constants::ROUTES_CONTESTS_DISPLAY, $privateContest[Constants::FLD_CONTESTS_ID], false));
            $browser->click('#testing-reorder-btn');

            $browser->dragDown('#testing-drag-problem-2', 40);

            $browser->press('Save');

            $browser->waitFor('.testing-problem-order-2');

            // Check problem order as third
            $this->assertTrue($browser->text('.testing-problem-order-2') ==
                Utilities::generateProblemNumber(Problem::find(2)));

            // ==========================================================
            // Delete contest test
            // ==========================================================
            $browser->press('Delete');
            $browser->acceptDialog();
            $browser->visit(route(Constants::ROUTES_CONTESTS_DISPLAY, $privateContest[Constants::FLD_CONTESTS_ID]));
            $browser->assertPathIs(route(Constants::ROUTES_ERRORS_404, [], false));

        });
    }
}
