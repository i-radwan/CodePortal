<?php

namespace Tests\Feature;

use App\Models\Contest;
use Illuminate\Validation\ValidationException;
use App\Utilities\Constants;

class ContestTest extends DatabaseTest
{
    /**
     * Test contest DB interaction
     *
     * @return void
     */
    public function testContestModel()
    {

        $initialCount = Contest::count();

        $user = $this->insertUser('user121', 'a12@a.a', 'aaaaaa');
        // insert valid contest and check for count
        $validContest = $this->insertContest('Contest1', '2017-10-10 12:12:12', 100, Constants::CONTEST_VISIBILITY_PUBLIC, $user);

        // Check if contest is inserted
        $this->assertTrue(Contest::count() == $initialCount + 1);

        // Check owner
        $this->assertTrue($validContest->owner[Constants::FLD_USERS_ID] == $user[Constants::FLD_USERS_ID]);


        // insert invalid models
        try {
            $this->insertContest('', '2017-10-10 12:12:12', 100, Constants::CONTEST_VISIBILITY_PUBLIC, $user);
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - missing data");
        } catch (ValidationException $e) {
        }
        try {
            $this->insertContest('Contest1', '2017-10-10 12:1s2:12', 100, Constants::CONTEST_VISIBILITY_PUBLIC, $user);
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - invalid dateTime value");
        } catch (ValidationException $e) {
        }
        try {
            $this->insertContest('Contest1', '2017-10-10 12:12:12', 100, '2', $user);
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - contest visibility");
        } catch (ValidationException $e) {
        }
        try {
            $this->insertContest('Contest1', '2017-10-10 12:12:12', -1, Constants::CONTEST_VISIBILITY_PUBLIC, $user);
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - -ve duration");
        } catch (ValidationException $e) {
        }
        try {
            $this->insertContest('Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1', '2017-10-10 12:12:12', 100, Constants::CONTEST_VISIBILITY_PUBLIC, $user);
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - long name");
        } catch (ValidationException $e) {
        }
        try {
            $this->insertContest('Contest1', '2017-03-8 10:56:05', 100, Constants::CONTEST_VISIBILITY_PUBLIC, $user);
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - before date");
        } catch (ValidationException $e) {
        }
        try {
            $this->insertContest('Contest1', '2017-10-10 12:12:12', 100, Constants::CONTEST_VISIBILITY_PUBLIC, null);
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - no owner");
        } catch (ValidationException $e) {
        }


        // Assign contest organizer
        $organizer = $this->insertUser('user1212', '2a12@a.a', 'aaa2aaa');
        $validContest->organizers()->save($organizer);
        $this->assertTrue($organizer->organizingContests()->first()[Constants::FLD_CONTESTS_ID] == $validContest[Constants::FLD_CONTESTS_ID]);

        // Assign participant


        // Assign problems

        // Assign teams

        // Attach to group

        // Update contest to violate rules (visibility)

        // Attach contest notification

        // Join contest

        // Leave contest

        // Invite users

        // Check deletion


        // Check if contest is deleted
        $validContest->delete();

        // Check if contest organizers are detached

        // Check contest participants are detached

        // Check contest problems are detached

        // Check contest teams are detached

        $this->assertTrue(Contest::count() == $initialCount); // test deleting

    }

}
