<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Validation\ValidationException;
use App\Models\Problem;
use App\Models\Judge;

class ProblemTest extends TestCase
{

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testProblem()
    {
        $judge = new Judge(['name' => 'Codeforces', 'link' => 'http://www.judge.com', 'api_link' => 'http://www.judge.com']);
        $judge->save();
        $initialCount = Problem::count();
        // insert valid contest and check for count
        $validProblem = $this->insertProblem('Problem1', 10, 20, $judge);
        $this->assertTrue(Problem::count() == $initialCount + 1);
        $validProblem->delete();
        $this->assertTrue(Problem::count() == $initialCount); // test deleting

        // insert invalid models
        try {
            $this->insertProblem('', 10, 20, $judge);
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - missing data");
        } catch (ValidationException $e) {
        }
        try {
            $this->insertProblem('Problem1Problem1Problem1Problem1Problem1Problem1Problem1Problem1Problem1Problem1Problem1Problem1Problem1Problem1Problem1Problem1Problem1', 10, 20, $judge);
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - name too long");
        } catch (ValidationException $e) {
        }
        try {
            $this->insertProblem('Problem 1', -10, 20, $judge);
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - diff -ve");
        } catch (ValidationException $e) {
        }
        try {
            $this->insertProblem('Problem2', 10, -20, $judge);
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - accepted count -ve");
        } catch (ValidationException $e) {
        }

        $this->assertTrue(Problem::count() == $initialCount); // not inserted
    }


    public function insertProblem($name, $difficulty, $accepted_count, $judge)
    {
        $problem = new Problem(['name' => $name, 'difficulty' => $difficulty, 'accepted_count' => $accepted_count]);
        $problem->judge()->associate($judge);
        $problem->store();
        return $problem;
    }

}