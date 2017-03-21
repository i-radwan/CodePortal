<?php

namespace Tests\Feature;

use App\Models\Judge;
use Illuminate\Validation\ValidationException;

class JudgeTest extends DatabaseTest
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testJudge()
    {
        $initialCount = Judge::count();
        // insert valid contest and check for count
        $validJudge = $this->insertJudge('1', 'Judge1', 'http://www.link.com');
        $this->assertTrue(Judge::count() == $initialCount + 1);
        $validJudge->delete();
        $this->assertTrue(Judge::count() == $initialCount); // test deleting


        // insert invalid models
        try {
            $this->insertJudge('1', '', 'http://www.link.com');
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - missing data");
        } catch (ValidationException $e) {
        }
        try {
            $this->insertJudge('1', 'Judge1', 'link.com');
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - invalid link");
        } catch (ValidationException $e) {
        }
        $validJudge = $this->insertJudge('1', 'Judge1', 'http://www.link.com');
        try {
            $this->insertJudge('1', 'Judge1', 'http://www.link2.com');
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - name duplicate");
        } catch (ValidationException $e) {
        }
        try {
            $this->insertJudge('2', 'Judge2', 'http://www.link.com');
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - link duplicate");
        } catch (ValidationException $e) {
        }

        $validJudge->delete();

        $this->assertTrue(Judge::count() == $initialCount); // not inserted

        // Get jduge problems paginated
        $judge1 = $this->insertJudge('1', 'Judge1', 'http://www.link.com');
        $judge2 = $this->insertJudge('2', 'Judge2', 'http://www.link2.com');

        // Insert a lot of problems for counting statistics
        for ($i = 0; $i < 100; $i++) {
            if ($i % 2 == 0)
                $this->insertProblem('Problem' . $i, 10, 20, $judge1, '12' . $i, '21' . $i);
            else
                $this->insertProblem('Problem' . $i, 10, 20, $judge2, '2' . $i, '2' . $i);
        }
        // Get problems of judge 1 and check if the count = 50
        $problems = Judge::getJudgeProblems($judge1->id);
        $this->assertEquals(json_decode($problems, true)['problems']['total'], 50);

        \Log::info("Judge's Problems :: " . $problems);
    }
}
