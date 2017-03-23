<?php

namespace Tests\Feature;

use App\Models\Contest;
use Illuminate\Validation\ValidationException;
use App\Utilities\Constants;

class ContestTest extends DatabaseTest
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testContestModel()
    {
        $initialCount = Contest::count();
        $user = $this->insertUser('user121', 'a12@a.a', 'aaaaaa');
        // insert valid contest and check for count
        $validContest = $this->insertContest('Contest1', '2017-10-10 12:12:12', 100, Constants::CONTEST_VISIBILITY["PUBLIC"], $user);
        $this->assertTrue(Contest::count() == $initialCount + 1);
        $validContest->delete();
        $this->assertTrue(Contest::count() == $initialCount); // test deleting

        // insert invalid models
        try {
            $this->insertContest('', '2017-10-10 12:12:12', 100, Constants::CONTEST_VISIBILITY["PUBLIC"], $user);
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - missing data");
        } catch (ValidationException $e) {
        }
        try {
            $this->insertContest('Contest1', '2017-10-10 12:1s2:12', 100, Constants::CONTEST_VISIBILITY["PUBLIC"], $user);
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - invalid dateTime value");
        } catch (ValidationException $e) {
        }
        try {
            $this->insertContest('Contest1', '2017-10-10 12:12:12', 100, '2', $user);
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - contest visibility");
        } catch (ValidationException $e) {
        }
        try {
            $this->insertContest('Contest1', '2017-10-10 12:12:12', -1, Constants::CONTEST_VISIBILITY["PUBLIC"], $user);
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - -ve duration");
        } catch (ValidationException $e) {
        }
        try {
            $this->insertContest('Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1', '2017-10-10 12:12:12', 100, Constants::CONTEST_VISIBILITY["PUBLIC"], $user);
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - long name");
        } catch (ValidationException $e) {
        }
        try {
            $this->insertContest('Contest1', '2017-03-8 10:56:05', 100, Constants::CONTEST_VISIBILITY["PUBLIC"], $user);
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - before date");
        } catch (ValidationException $e) {
        }
        try {
            $this->insertContest('Contest1', '2017-10-10 12:12:12', 100, Constants::CONTEST_VISIBILITY["PUBLIC"], null);
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - no owner");
        } catch (ValidationException $e) {
        }
        // Recheck that nothing is inserted
        $this->assertTrue(Contest::count() == $initialCount);

    }

}
