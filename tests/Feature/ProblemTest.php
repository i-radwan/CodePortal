<?php

namespace Tests\Feature;


use App\Http\Controllers\RetrieveProblems;
use Illuminate\Validation\ValidationException;
use App\Models\Problem;
use App\Utilities\Constants;

class ProblemTest extends DatabaseTest
{
    use RetrieveProblems;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testProblem()
    {
        $judge = $this->insertJudge('1', 'Codeforces', 'http://www.judge.com');
        $initialCount = Problem::count();
        // insert valid contest and check for count
        $validProblem = $this->insertProblem('Problem1', 20, $judge, '123', '213');
        $this->assertTrue(Problem::count() == $initialCount + 1);
        $validProblem->delete();
        $this->assertTrue(Problem::count() == $initialCount); // test deleting

        // insert invalid models
        try {
            $this->insertProblem('', 20, $judge, '1233', '2132');
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - missing data");
        } catch (ValidationException $e) {
        }
        try {
            $this->insertProblem('Problem1Problem1Problem1Problem1Problem1Problem1Problem1Problem1Problem1Problem1Problem1Problem1Problem1Problem1Problem1Problem1Problem1Problem1Problem1Problem1Problem1Problem1Problem1Problem1Problem1Problem1Problem1Problem1Problem1Problem1Problem1Problem1Prob', 10, 20, $judge, '123', '213');
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - name too long");
        } catch (ValidationException $e) {
        }
        try {
            $this->insertProblem('Problem2', -20, $judge, '123', '213');
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - accepted count -ve");
        } catch (ValidationException $e) {
        }

        // Test judge keys uniqueness
        $p = $this->insertProblem('Problem1', 20, $judge, '123', '213');
        try {
            $this->insertProblem('Problem1', 20, $judge, '123', '213');
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - duplicated judge keys");
        } catch (ValidationException $e) {
        }
        $p->delete();
        $this->assertTrue(Problem::count() == $initialCount); // not inserted

        // Test pagination
        for ($i = 0; $i < 100; $i++) $this->insertProblem('Problem1', 10, 20, $judge, '123' . $i, '213' . $i);
        $problems = $this->filterProblems();
        $this->assertEquals($problems->count(), Constants::PROBLEMS_COUNT_PER_PAGE);

        // Test problems filtering
        $judge1 = $this->insertJudge('2', 'Judge1', 'http://www.link.com');
        $judge2 = $this->insertJudge('3', 'Judge2', 'http://www.link2.com');
        $validTag1 = $this->insertTag("NewTag1");
        $validTag2 = $this->insertTag("NewTag2");

        $name = "Problem";

        $judge1Count = 0;
        $judge2Count = 0;
        $tag1Count = 0;

        for ($i = 0; $i < 100; $i++) {
            if ($i % 2 == 0) {
                $judge1Count++;
                $problem = $this->insertProblem('Problem' . $i, 20, $judge1, '123' . $i, '213' . $i);
            } else {
                $judge2Count++;
                $problem = $this->insertProblem('Problem' . $i, 20, $judge2, '123' . $i, '213' . $i);
            }
            if ($i % 5 == 0) {
                $tag1Count++;
                $problem->tags()->sync([$validTag1->id, $validTag2->id], false);
            }
        }

        // Tags are Anded
        $problems = Problem::ofName($name)->ofJudges([$judge1->id, $judge2->id])->hasTags([$validTag1->id, $validTag2->id])->get();
        $this->assertEquals($problems->count(), 20);
        $problems = Problem::ofName($name)->ofJudges([$judge1->id, $judge2->id])->hasTags([$validTag1->id])->get();
        $this->assertEquals($problems->count(), 20);
        \Log::info("Filtered Problems :: " . $problems->toJson());

        // Sorted problems
        $problems = Problem::ofName($name)->ofJudges([$judge2->id, $judge1->id])->hasTags( [$validTag1->id, $validTag2->id])->get();
        \Log::info("Sorted Problems :: " . $problems->toJson());

    }
}