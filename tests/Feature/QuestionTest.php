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
        $user = $this->insertUser('user1', 'a@a.a', 'aaaaaa', 'aaa', '1');
        $contest = $this->insertContest('Contest1', '2017-12-12 12:12:12', '10', '0', $user);
        $initialCount = Question::count();
        // insert valid question and check for count
        $validQuestion = $this->insertQuestion('Question1', 'Hello', '', $contest, $user);
        $this->assertTrue(Question::count() == $initialCount + 1);
        $validQuestion->delete();
        $this->assertTrue(Question::count() == $initialCount); // test deleting

        // insert invalid models
        try {
            $this->insertQuestion('', 'Hello', '', $contest, $user);
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - missing data");
        } catch (ValidationException $e) {
        }
        try {
            $this->insertQuestion('HelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHelloHello', 'Hello', '', $contest, $user);
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - title too long");
        } catch (ValidationException $e) {
        }
        try {
            $this->insertQuestion('Title', 'Hello', '', $contest, $user, '3');
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - invalid status");
        } catch (ValidationException $e) {
        }
        // Answer questions

        // Valid answer
        $validQuestion = $this->insertQuestion('Question1', 'Hello', '', $contest, $user);
        $validQuestion->saveAnswer("Answer to Q1", $user);
        try {
            $validQuestion->saveAnswer("Answer to Q1", $user, '3');
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - invalid status");
        } catch (ValidationException $e) {
        }
        try {
            $validQuestion->saveAnswer("", $user, '1');
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - invalid answer");
        } catch (ValidationException $e) {
        }

        // Remove question
        $validQuestion->delete();
        $this->assertTrue(Question::count() == $initialCount); // not inserted
    }

}