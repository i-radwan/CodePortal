<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Contest;
use App\Models\Problem;
use App\Models\Language;
use App\Models\Tag;
use App\Models\Question;
use App\Models\Submission;
use App\Models\Judge;
use Log;

class ModelsRelationsTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRelations()
    {
        // Create models
        $user = new User(['name' => 'user121', 'email' => 'a12@a.a', 'password' => 'aaaaaa', 'handle' => 'aa31a']);
        $user->save();
        $admin = new User(['name' => 'use12r121', 'email' => 'a1312@a.a', 'password' => 'aaaaaa', 'handle' => 'aa131a']);
        $admin->role = '1';
        $admin->save();
        $language = new Language(['name' => 'C+++1']);
        $language->store();
        $judge = new Judge(['name' => 'Codeforces3', 'link' => 'http://www.judge3.com', 'api_link' => 'http://www.judge.com']);
        $judge->store();
        $contest = new Contest(['name' => 'Contest1', 'time' => '2017-12-12 12:12:12', 'duration' => '10', 'visibility' => '0']);
        $contest->store();
        $problem1 = new Problem(['name' => 'Problem1', 'difficulty' => '10', 'accepted_count' => '20']);
        $problem1->judge()->associate($judge);
        $problem1->store();
        $question = new Question(['title' => 'Question1', 'content' => "Hello", 'answer' => 'Answer1', 'status' => '0']);
        $question->contest()->associate($contest);
        $question->user()->associate($user);
        $question->store();
        $question->saveAnswer("Ansert1", $admin);
        $submission = new Submission(['submission_id' => '123', 'execution_time' => 100, 'used_memory' => 200, 'verdict' => '1']);
        $submission->problem()->associate($problem1);
        $submission->user()->associate($user);
        $submission->language()->associate($language);
        $submission->store();
        $tag = new Tag(['name' => 'Tag0123']);
        $tag->store();

        // Tags + problems
        $problem1->tags()->sync([$tag->id], false);
        $tag = new Tag(['name' => 'Tag01232']);
        $tag->store();
        $problem1->tags()->sync([$tag->id], false);
        $problem = new Problem(['name' => 'Problem1', 'difficulty' => '10', 'accepted_count' => '20']);
        $problem->judge()->associate($judge);
        $problem->store();
        $problem->tags()->sync([$tag->id], false);
        $problem->tags()->sync([$tag->id], false);
        $problem->tags()->sync([$tag->id], false);
        $problem->tags()->sync([$tag->id], false);
        $problem->tags()->sync([$tag->id], false);
        $problem->tags()->sync([$tag->id], false);

        $this->assertEquals(count($tag->problems()->get()), 2);

        // Problem + Submissions
        $this->assertEquals(count($problem1->submissions), 1);
        $this->assertEquals($submission->problem()->getResults()->id, $problem1->id);

        // Problem + Judge
        $this->assertEquals(count($judge->problems()), 1);
        $this->assertEquals($problem1->judge()->getResults()->id, $judge->id);

        // Problem + contest
        $contest->problems()->sync([$problem1->id], false);

        $this->assertEquals(count($contest->problems()->get()), 1);
        $this->assertEquals(count($problem1->contests()->get()), 1);

        // Submission + Language
        $this->assertEquals(count($language->submissions()), 1);

        // Contest + Question
        $this->assertEquals(count($contest->questions()), 1);
        $this->assertEquals($question->contest()->getResults()->id, $contest->id);

        // User + Submission
        $this->assertEquals(count($user->submissions), 1);
        $this->assertEquals($submission->user()->getResults()->id, $user->id);

        // User + Question
        $this->assertEquals(count($user->questions()), 1);
        $this->assertEquals(count($admin->answered_questions()), 1);
        $this->assertEquals($question->user()->getResults()->id, $user->id);
        $this->assertEquals($question->admin()->getResults()->id, $admin->id);

        // User + Judge
        $user->handles()->save($judge, ['handle' => 'asd']);

        // User + Contest
        $user->participating_in_contests()->save($contest);
        $admin->organizing_contests()->save($contest);
        $this->assertEquals(count($user->participating_in_contests()), 1);
        $this->assertEquals(count($admin->organizing_contests()), 1);
        Log::info($contest->organizing_users()->getResults());
        Log::info($contest->participating_users()->getResults());
    }
}
