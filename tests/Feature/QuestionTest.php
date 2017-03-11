<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Validation\ValidationException;
use App\Models\Question;
use App\Models\User;
use App\Models\Contest;

class QuestionTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testQuestion()
    {
        $user = new User(['name' => 'user1', 'email' => 'a@a.a', 'password' => 'aaaaaa', 'handle' => 'aaa']);
        $user->role = ('1');
        $user->save();
        $contest = new Contest(['name' => 'Contest1', 'time' => '2017-12-12 12:12:12', 'duration' => '10', 'visibility' => '0']);
        $contest->save();

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


    public function insertQuestion($title, $content, $answer, $contest, $user, $status = '0')
    {
        $question = new Question(['title' => $title, 'content' => $content, 'answer' => $answer, 'status' => $status]);
        $question->contest()->associate($contest);
        $question->user()->associate($user);
        $question->store();
        return $question;
    }
}