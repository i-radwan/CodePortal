<?php

namespace Tests\Browser;

use App\Models\Problem;
use App\Models\User;
use App\Utilities\Constants;
use App\Utilities\Utilities;
use Facebook\WebDriver\Exception\UnexpectedAlertOpenException;
use Tests\Browser\Pages\AddContestPage;
use Tests\Browser\Pages\Contests;
use Tests\Browser\Pages\HomePage;
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

        $this->browse(function (Browser $browser, Browser $browser2) use ($faker) {

            // Visit contests page
            $browser->visit(new Contests)
                ->assertSee('Contests')
                ->assertVisible(".pagination");


            // Add new contest for non user
            // ToDo to be removed after removing new button for non-signed in
            $browser->clickLink('New')
                ->assertSee('401')
                ->assertPathIs('/errors/401');

            // Login
            $browser->visit(new Login)
                ->loginUser('asd', 'asdasd');


            // Visit new contest page and move to add new contest page
            $browser->visit(new Contests)
                ->clickLink('New')
                ->assertSee('Add')
                ->assertPathIs('/contest/add')
                ->on(new AddContestPage);

            // empty data
            $browser->press('Save')
                ->on(new AddContestPage);

            // invalid data (empty name)
            $browser->saveContest('', '2017-05-02 12:12:12', 1000, 0);
            $browser->on(new AddContestPage);

            // invalid data (invalid date)
            $browser->saveContest('ADS2', '2017-04-02 12:12:12', 1000, 0);
            $browser->on(new AddContestPage);

            // valid data
            $browser->saveContest('ADS2', '2017-05-02 12:12:12', 1000, 0);
            $browser->on(new AddContestPage);

            $lastContest = \App\Models\Contest::query()->orderBy(Constants::FLD_CONTESTS_ID, 'desc')->first();

            // Get last id to check path
            $browser->assertPathIs('/contest/' . $lastContest[Constants::FLD_CONTESTS_ID]);

            // ================================================================
            // Insert contest with organizers and invite users and select problems after
            // ================================================================

            $browser->visit(new Contests)
                ->clickLink('New')
                ->on(new AddContestPage);

            $organizers = User::find([2, 3, 4])->pluck(Constants::FLD_USERS_USERNAME);
            $browser->saveContest('ADS2', '2017-05-02 12:12:12', 1000, 0, [], $organizers, []);

            // ToDo Test auto complete

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

            // Select problems
            $contestName = $faker->sentence(2);
            $browser->saveContest($contestName, '2017-05-02 12:12:12', 1000, 0, [1, 2, 3], $organizers, []);

            // Get last id to check path
            $lastContest = \App\Models\Contest::query()->orderBy(Constants::FLD_CONTESTS_ID, 'desc')->first();
            $browser->assertPathIs('/contest/' . $lastContest[Constants::FLD_CONTESTS_ID]);

            // Check if in contests table
            $browser->visit(new Contests);
            $page = 1;
            while (true) {
                try {
                    $browser->assertSee($lastContest[Constants::FLD_CONTESTS_ID]);
                    break;
                } catch (\PHPUnit_Framework_ExpectationFailedException $e) {
                    $browser->visit('http://127.0.0.1:8000/contests?page=' . $page);
                    $page++;
                }
            }

            // Check using other browser
            // Login browser 2
            $browser2->visit(new Login)
                ->loginUser('dsa', 'asdasd');

            // User 2 can see th contest cuz it's public
            $browser2->visit('http://127.0.0.1:8000/contest/' . $lastContest[Constants::FLD_CONTESTS_ID]);

            // Check contest name
            $browser2->assertSee($contestName);
            // Check organizers added
            $browser2->assertSeeIn('.organizers-p', User::find(2)[Constants::FLD_USERS_USERNAME])
                ->assertSeeIn('.organizers-p', User::find(3)[Constants::FLD_USERS_USERNAME])
                ->assertSeeIn('.organizers-p', User::find(4)[Constants::FLD_USERS_USERNAME])
                ->assertSeeIn('.owner-p', 'asd')
                ->assertSeeIn('.duration-p', Utilities::convertMinsToHoursMins(1000));

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

            // No error should happened, we just have to check that I'm not listed as organizer in browser 2
            $browser->visit(new AddContestPage)
                ->saveContest($contestName, '2017-04-23 12:33:12', 10000, 0, [1, 2, 3], ['asd', 'mitchell.otha'], []);

            $publicContest = \App\Models\Contest::query()->orderBy(Constants::FLD_CONTESTS_ID, 'desc')->first();
            $browser->assertPathIs('/contest/' . $publicContest[Constants::FLD_CONTESTS_ID]);

            // Use browser 2 to check

            $browser2->visit('http://127.0.0.1:8000/contest/' . $publicContest[Constants::FLD_CONTESTS_ID]);
            // Check contest name
            $browser2->assertSee($contestName)
                ->assertDontSeeIn('.organizers-p', 'asd');

            //===========================================================
            // Select more than 10 check boxes
            //===========================================================
            // Add myself as organizer
            $contestName = $faker->sentence(2);
            $problems = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];

            // Alert for more than 10
            try {
                $browser->visit(new AddContestPage)
                    ->saveContest($contestName, '2017-05-02 12:12:12', 1000, 0, $problems, ['asd', 'mitchell.otha'], []);
                $this->fail("Shouldn't reach here without alertbox!");
            } catch (UnexpectedAlertOpenException $e) {
                $browser->driver->switchTo()->alert()->accept();
            }

            //===========================================================


            // ==========================================================
            // Test private contests
            // ==========================================================

            $browser->visit(new AddContestPage)
                ->saveContest($contestName, '2017-05-02 12:12:12', 1000, 1, [1, 2, 3, 4], ['asd', 'mitchell.otha'], ['cummings.antonetta', 'lizeth80']);

            $privateContest = \App\Models\Contest::query()->orderBy(Constants::FLD_CONTESTS_ID, 'desc')->first();
            $browser->assertPathIs('/contest/' . $privateContest[Constants::FLD_CONTESTS_ID]);

            // Check invitations sent (in notification panel and
            // the user in browser 2 can see the contest page)
            $browser2->visit('http://127.0.0.1:8000/contest/' . $privateContest[Constants::FLD_CONTESTS_ID]);
            $browser2->assertPathIs('/errors/401');


            // Logout
            $browser2->visit(new HomePage)
                ->clickLink('Logout');


            // Login as cummings.antonetta
            $browser2->visit(new Login)
                ->loginUser('cummings.antonetta', 'asdasd');

            // Visit page again

            $browser2->visit('http://127.0.0.1:8000/contest/' . $privateContest[Constants::FLD_CONTESTS_ID]);
            $browser2->assertPathIs('/contest/' . $privateContest[Constants::FLD_CONTESTS_ID]);


            // Check notification panel
            $browser2->click('#testing-notification-link');

            $browser2->assertSeeIn('.notification-text', $contestName);


            // ==========================================================
            // Contest requests/notifications/participation test
            // browser1 -> asd
            // browser2 -> cummings.antonetta (invited)
            // ==========================================================

            // Join public contest

            // Logout asd
            $browser->visit(new HomePage)
                ->clickLink('Logout');

            // visit login page and login this normal user with id 13
            $browser->visit(new Login)
                ->loginUser('lizeth80', 'asdasd');

            // Visit contest page
            $browser->visit('http://127.0.0.1:8000/contest/' . $publicContest[Constants::FLD_CONTESTS_ID])
                ->assertPathIs('/contest/' . $publicContest[Constants::FLD_CONTESTS_ID]);

            // Click join link
            $browser->click('#testing-contest-join-btn');

            $browser->assertSee('Leave'); // Check if Leave link appears

            // Check if participant
            $browser->clickLink('Participants');

            // Check if is participant
            $browser->assertSee('lizeth80');

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
                $browser->assertDontSeeIn('.testing-participant-username', 'lizeth80');
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
            $browser->assertPathIs('/contest/' . $privateContest[Constants::FLD_CONTESTS_ID]);

            // Click join link
            $browser->click('#testing-contest-join-btn');

            $browser->assertSee('Leave'); // Check if Leave link appears

            // Check if participant
            $browser->clickLink('Participants');

            // Check if is participant
            $browser->assertSee('lizeth80');

            // ==========================================================
            // Contest questions test
            // ==========================================================

            // Ask question

            // Organizer answers question

            // Organizer marks question as announcement


            // ==========================================================
            // Editing contest test OMG!
            // ==========================================================

            // ==========================================================
            // Reorder contest test
            // ==========================================================

            // ==========================================================
            // Delete contest test
            // ==========================================================
            $browser->pause(100000);

        });
    }
}
