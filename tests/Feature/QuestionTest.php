<?php

namespace Tests\Feature;

use Illuminate\Validation\ValidationException;
use App\Models\Question;

class QuestionTest extends DatabaseTest
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testQuestion()
    {
        $judge = $this->insertJudge('1', 'Codeforces', 'http://www.judge.com');
        $user = $this->insertUser('user1', 'a@a.a', 'aaaaaa', '1');
        $contest = $this->insertContest('Contest1', '2017-12-12 12:12:12', '10', '0', $user);
        $problem = $this->insertProblem('Problem1', 20, $judge, '123', '213');

        $initialCount = Question::count();
        // insert valid question and check for count
        $validQuestion = $this->insertQuestion('Question1', 'HelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHello', '', $contest, $user, $problem);
        $this->assertTrue(Question::count() == $initialCount + 1);
        $validQuestion->delete();
        $this->assertTrue(Question::count() == $initialCount); // test deleting

        // insert invalid models
        try {
            $this->insertQuestion('', 'HelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHello', '', $contest, $user, $problem);
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - missing data");
        } catch (ValidationException $e) {
        }
        try {
            $this->insertQuestion('HelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHello', 'Hello', '', $contest, $user, $problem);
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - title too long");
        } catch (ValidationException $e) {
        }
        try {
            $this->insertQuestion('Title', 'HelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHello', '', $contest, $user, $problem, '3');
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - invalid status");
        } catch (ValidationException $e) {
        }
        // Answer questions

        // Valid answer
        $validQuestion = $this->insertQuestion('Question1', 'HelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHello', '', $contest, $user, $problem);
        $validQuestion->saveAnswer("Answer to Q1", $user);

        // Remove question
        $validQuestion->delete();
        $this->assertTrue(Question::count() == $initialCount); // not inserted
    }

}