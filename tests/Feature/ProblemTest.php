<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use App\Models\Problem;
use App\Models\Judge;
use App\Exceptions\UnknownJudgeException;

class ProblemTest extends TestCase
{

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testProblem()
    {
        $initialCount = Problem::count();
        // insert valid contest and check for count
        $validProblem = $this->insertValidProblem();
        $this->assertTrue(Problem::count() == $initialCount + 1);
        $validProblem->delete();
        $this->assertTrue(Problem::count() == $initialCount); // test deleting

        // insert invalid models
        try {
            ($this->insertInvalidProblemMissingData());
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - missing data");
        } catch (ValidationException $e) {
        }
        try {
            ($this->insertInvalidProblemName());
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - name too long");
        } catch (ValidationException $e) {
        }
        try {
            ($this->insertInvalidProblemDiff());
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - diff -ve");
        } catch (ValidationException $e) {
        }
        try {
            ($this->insertInvalidProblemAcceptedCount());
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - accepted count -ve");
        } catch (ValidationException $e) {
        }

        $this->assertTrue(Problem::count() == $initialCount); // not inserted
    }


    public function insertValidProblem()
    {
        $problem = new Problem(['name' => 'Problem1', 'difficulty' => 10, 'accepted_count' => 20]);
        $problem->judge()->associate(Judge::first());
        $problem->save();
        return $problem;
    }

    public function insertInvalidProblemMissingData()
    {
        $problem = new Problem(['name' => '', 'difficulty' => 10, 'accepted_count' => 20]);
        $problem->judge()->associate(Judge::first());
        $problem->save();
        return $problem;
    }

    public function insertInvalidProblemName()
    {
        $problem = new Problem(['name' => 'em1Problem1Problem1em1Problem1Problem1em1Problem1Problem1em1Problem1Problem1em1Problem1Problem1em1Problem1Problem1em1Problem1Problem1em1Problem1Problem1em1Problem1Problem1em1Problem1Problem1em1Problem1Problem1em1Problem1P', 'difficulty' => 10, 'accepted_count' => 20]);
        $problem->judge()->associate(Judge::first());
        $problem->save();
        return $problem;
    }

    public function insertInvalidProblemDiff()
    {
        $problem = new Problem(['name' => 'em1P', 'difficulty' => -1, 'accepted_count' => 20]);
        $problem->judge()->associate(Judge::first());
        $problem->save();
        return $problem;
    }

    public function insertInvalidProblemAcceptedCount()
    {
        $problem = new Problem(['name' => 'em1Problem1Pr', 'difficulty' => 10, 'accepted_count' => -1]);
        $problem->judge()->associate(Judge::first());
        $problem->save();
        return $problem;
    }
}