<?php

namespace Tests\Browser;

use App\Models\Team;
use App\Models\User;
use App\Utilities\Constants;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Browser\Pages\Login;
use Tests\Browser\Pages\Register;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class TeamsFlowTest extends DuskTestCase
{
    /**
     * Test teams flow
     *
     * @group teams
     * @return void
     */
    public function testTeamsFlow()
    {
        sleep(1);

        $faker = \Faker\Factory::create();

        $this->browse(function (Browser $browser, Browser $browser2) use ($faker) {

            // login
            $browser->visit(new Login)
                ->loginUser('asd', 'asdasd');

            // ====================================================
            // • User can create new team
            // ====================================================
            $browser
                ->visit(route(Constants::ROUTES_TEAMS_CREATE))
                ->type('#name', 'TeamAlpha')
                ->press('Create')
                ->assertSee('TeamAlpha created successfully!');

            // get model
            $team = Team::query()->orderByDesc('id')->first();
            $teamID = $team[Constants::FLD_TEAMS_ID];
            $teamName = $team[Constants::FLD_TEAMS_NAME];

            $browser->assertPathIs(route(Constants::ROUTES_PROFILE_TEAMS, "asd", false))
                ->assertSee($teamName);

            // ====================================================
            // • User can edit team
            // ====================================================
            $browser->click('#testing-edit-team-' . $teamID)
                ->assertInputValue('#name', $teamName)
                ->type('#name', $teamName = $faker->unique()->sentence(2))
                ->press('Save')
                ->assertSee($teamName . ' updated successfully!');

            // ====================================================
            // • User can invite others to join his team
            // • Check using browser 2
            // ====================================================
            $invitee = User::find(13)[Constants::FLD_USERS_USERNAME];
            $invitee2 = User::find(14)[Constants::FLD_USERS_USERNAME];

            $browser->script(["$('#testing-team-username-$teamID').show();",
                "$('#testing-team-username-$teamID').attr('type', 'text');"]);

            $browser->type('#testing-team-username-' . $teamID, $invitee)
                ->click('#testing-team-send-' . $teamID)
                ->assertSee('Users were invited successfully!');

            // Check invitation using browser 2
            $browser2->visit(new Login)
                ->loginUser($invitee, 'asdasd')
                ->click('#testing-notification-link')
                ->assertSeeIn('.notification-text', $teamName);

            // ====================================================
            // • Member can cancel invitation sent to another user
            // ====================================================
            $browser->click("#testing-cancel-invitation-$teamID-13")
                ->acceptDialog()
                ->assertDontSeeIn("#testing-team-panel-$teamID", $invitee);

            // Confirm with browser 2
            $browser2->refresh();
            if ($browser2->element('#testing-notification-link')) {
                $browser2->click('#testing-notification-link')
                    ->assertDontSeeIn('.notification-text', $teamName);
            }
            // ====================================================
            // • Invitee can accept the invitation to join the team
            // ====================================================
            // Re-invite again
            $browser->script(["$('#testing-team-username-$teamID').show();",
                "$('#testing-team-username-$teamID').attr('type', 'text');"]);

            $browser->type('#testing-team-username-' . $teamID, $invitee)
                ->click('#testing-team-send-' . $teamID)
                ->assertSee('Users were invited successfully!');

            // Click notification to join
            $browser2->refresh()
                ->click('#testing-notification-link');
            $allNotifications = $browser2->elements('.notification-text');
            foreach ($allNotifications as $notification) {
                if (str_contains($notification->getText(),
                    $teamName)) {
                    $notification->click();
                    break;
                }
            }

            $browser2->assertPathIs(route(Constants::ROUTES_PROFILE_TEAMS, "asd", false))
                ->click('#testing-reject-team-' . $teamID)
                ->acceptDialog();// reject first

            // Check with browser 1
            $browser
                ->pause(1000)
                ->refresh()
                ->assertDontSeeIn("#testing-team-panel-$teamID", $invitee);

            // Invite new user
            $browser->script(["$('#testing-team-username-$teamID').show();",
                "$('#testing-team-username-$teamID').attr('type', 'text');"]);

            $browser->type('#testing-team-username-' . $teamID, $invitee2)
                ->click('#testing-team-send-' . $teamID)
                ->assertSee('Users were invited successfully!');

            // Check invitation using browser 2
            $browser2->clickLink('Logout')
                ->visit(new Login)
                ->loginUser($invitee2, 'asdasd')
                ->click('#testing-notification-link')
                ->assertSeeIn('.notification-text', $teamName);

            // Click notification to join
            $allNotifications = $browser2->elements('.notification-text');
            foreach ($allNotifications as $notification) {
                if (str_contains($notification->getText(),
                    $teamName)) {
                    $notification->click();
                    break;
                }
            }
            $browser2->assertPathIs(route(Constants::ROUTES_PROFILE_TEAMS, "asd", false))
                ->click('#testing-accept-team-' . $teamID)// accept
                ->assertPathIs(route(Constants::ROUTES_PROFILE_TEAMS, $invitee2, false))
                ->assertSee($teamName);

            // Check with browser 1
            $browser->refresh()
                ->assertSeeIn("#testing-team-panel-$teamID", $invitee2);

            // ====================================================
            // • Team can join contests
            // ====================================================

            // ====================================================
            // • Member can remove someone from the team
            // ====================================================
            // remove member(invitee2)
            $browser->click("#testing-remove-member-team-$teamID-" . $invitee2)
                ->acceptDialog()
                ->assertSee($invitee2 . ' was removed successfully from ' . $teamName . '!');

            // Check with browser 2
            $browser2->refresh()->assertDontSee($teamName);
            // ====================================================
            // • Member can delete the team
            // ====================================================
            $browser->click("#testing-delete-team-$teamID")
                ->acceptDialog()
                ->assertSee("$teamName deleted successfully!");

        });
    }
}
