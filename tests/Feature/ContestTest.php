<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Contest;
use Illuminate\Validation\ValidationException;

class ContestTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testContestModel()
    {
        $initialCount = Contest::count();
        // insert valid contest and check for count
        $validContest = $this->insertContest('Contest1', '2017-10-10 12:12:12', 100, config('constants.CONTEST_VISIBILITY.PUBLIC'));
        $this->assertTrue(Contest::count() == $initialCount + 1);
        $validContest->delete();
        $this->assertTrue(Contest::count() == $initialCount); // test deleting


        // insert invalid models
        try {
            $this->insertContest('', '2017-10-10 12:12:12', 100, config('constants.CONTEST_VISIBILITY.PUBLIC'));
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - missing data");
        } catch (ValidationException $e) {
        }
        try {
            $this->insertContest('Contest1', '2017-10-10 12:1s2:12', 100, config('constants.CONTEST_VISIBILITY.PUBLIC'));
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - invalid dateTime value");
        } catch (ValidationException $e) {
        }
        try {
            $this->insertContest('Contest1', '2017-10-10 12:12:12', 100, '2');
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - contest visibility");
        } catch (ValidationException $e) {
        }
        try {
            $this->insertContest('Contest1', '2017-10-10 12:12:12', -1, config('constants.CONTEST_VISIBILITY.PUBLIC'));
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - -ve duration");
        } catch (ValidationException $e) {
        }
        try {
            $this->insertContest('Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1', '2017-10-10 12:12:12', 100, config('constants.CONTEST_VISIBILITY.PUBLIC'));
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - long name");
        } catch (ValidationException $e) {
        }
        try {
            $this->insertContest('Contest1', '2017-03-8 10:56:05', 100, config('constants.CONTEST_VISIBILITY.PUBLIC'));
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - before date");
        } catch (ValidationException $e) {
        }
        $this->assertTrue(Contest::count() == $initialCount); // not inserted

        // ToDo another file
        // get contest problems
        // get contest admins
        // get contest participants

    }

    public function insertContest($name, $time, $duration, $visilibty)
    {
        $contest = new Contest(array('name' => $name, 'time' => $time, 'duration' => $duration, 'visibility' => $visilibty));
        $contest->store();
        return $contest;
    }
}
