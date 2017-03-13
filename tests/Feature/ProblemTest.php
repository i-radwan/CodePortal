<?php

namespace Tests\Feature;

use Illuminate\Validation\ValidationException;
use App\Models\Problem;

class ProblemTest extends DatabaseTest
{

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testProblem()
    {
        $judge = $this->insertJudge('Codeforces', 'http://www.judge.com', 'http://www.judge.com');
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

        // Test pagination
        for ($i = 0; $i < 100; $i++) $this->insertProblem('Problem1', 10, 20, $judge);
        $problems = Problem::index(2);
        $this->assertEquals(count(json_decode($problems, true)['problems']['data']), config('constants.PROBLEMS_COUNT_PER_PAGE'));

        // Test problems filtering
        $judge1 = $this->insertJudge('Judge1', 'http://www.link.com', 'http://www.apilink.com');
        $judge2 = $this->insertJudge('Judge2', 'http://www.link2.com', 'http://www.apilink2.com');
        $validTag1 = $this->insertTag("NewTag1");
        $validTag2 = $this->insertTag("NewTag2");

        $name = "Problem";

        $judge1Count = 0;
        $judge2Count = 0;
        $tag1Count = 0;
        $tag2Count = 0;

        for ($i = 0; $i < 100; $i++) {
            if ($i % 2 == 0) {
                $judge1Count++;
                $problem = $this->insertProblem('Problem' . $i, 10, 20, $judge1);
            } else {
                $judge2Count++;
                $problem = $this->insertProblem('Problem' . $i, 10, 20, $judge2);
            }
            if ($i % 5 == 0) {
                $tag1Count++;
                $problem->tags()->sync([$validTag1->id], false);
            } else {
                $tag2Count++;
                $problem->tags()->sync([$validTag2->id], false);
            }
        }

        $problems = Problem::filter($name, [$validTag1->id, $validTag2->id], [$judge1->id, $judge2->id]);
        $this->assertEquals(json_decode($problems, true)['problems']['total'], 100);
        $problems = Problem::filter($name, [$validTag1->id], [$judge1->id, $judge2->id]);
        $this->assertEquals(json_decode($problems, true)['problems']['total'], 20);
        $problems = Problem::filter($name, [$validTag1->id, $validTag2->id], [$judge1->id]);
        $this->assertEquals(json_decode($problems, true)['problems']['total'], 50);
        $problems = Problem::filter($name, [$validTag1->id, $validTag2->id], [$judge2->id]);
        $this->assertEquals(json_decode($problems, true)['problems']['total'], 50);
        \Log::info("Filtered Problems :: " . $problems);


    }
}