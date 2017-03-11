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
        $validContest = $this->insertValidContest();
        $this->assertTrue(Contest::count() == $initialCount+1);
        $validContest->delete();
        $this->assertTrue(Contest::count() == $initialCount); // test deleting


        // insert invalid models
        try {
            ($this->insertInvalidContestsWithMissingData());
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - missing data");
        } catch (ValidationException $e){
        }
        try {
            ($this->insertInvalidContestTime());
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - invalid dateTime value");
        } catch (ValidationException $e){
        }
        try {
            $this->insertInvalidContestVisibility();
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - contest visibility");
        } catch (ValidationException $e){
        }
        try {
            $this->insertInvalidContestDuration();
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - -ve duration");
        } catch (ValidationException $e){
        }
        try {
            $this->insertInvalidContestName();
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - long name");
        } catch (ValidationException $e){
        }
        try {
            $this->insertInvalidContestDate();
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - before date");
        } catch (ValidationException $e){
        }
        $this->assertTrue(Contest::count() == $initialCount); // not inserted

        // ToDo another file
        // get contest problems
        // get contest admins
        // get contest participants

    }

    public function insertValidContest()
    {
        $contest = new Contest(array('name' => 'Contest1', 'time' => '2017-10-10 12:12:12', 'duration' => 100, 'visibility' => config('constants.CONTEST_VISIBILITY.PUBLIC')));
        $contest->save();
        return $contest;
    }

    public function insertInvalidContestsWithMissingData()
    {
        $contest = new Contest(array('time' => '2017-10-10 12:12:12', 'duration' => 100, 'visibility' => config('constants.CONTEST_VISIBILITY.PUBLIC')));
        $contest->save();
    }
    public function insertInvalidContestTime()
    {
        $contest = new Contest(array('name' => 'Contest1', 'time' => '2017-10-10 12:12s:12', 'duration' => 100, 'visibility' => config('constants.CONTEST_VISIBILITY.PUBLIC')));
        $contest->save();
    }
    public function insertInvalidContestVisibility()
    {
        $contest = new Contest(array('name' => 'Contest1', 'time' => '2017-10-10 12:12:12', 'duration' => 100, 'visibility' => '2'));
        $contest->save();
    }
    public function insertInvalidContestDuration()
    {
        $contest = new Contest(array('name' => 'Contest1', 'time' => '2017-10-10 12:12:12', 'duration' => -1, 'visibility' => config('constants.CONTEST_VISIBILITY.PUBLIC')));
        $contest->save();
    }
    public function insertInvalidContestName()
    {
        $contest = new Contest(array('name' => 'Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1Contest1', 'time' => '2017-10-10 12:12:12', 'duration' => 100, 'visibility' => '2'));
        $contest->save();
    }
    public function insertInvalidContestDate()
    {
        $contest = new Contest(array('name' => 'Contest1', 'time' => '2017-03-8 10:56:05', 'duration' => 100, 'visibility' => config('constants.CONTEST_VISIBILITY.PUBLIC')));
        $contest->save();
    }
}
