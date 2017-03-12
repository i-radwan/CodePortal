<?php

namespace Tests\Feature;

use App\Models\Language;
use App\Models\Tag;
use Log;

class ModelsRelationsTest extends DatabaseTest
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRelations()
    {
        // Create models
        $user = $this->insertUser('user121', 'a12@a.a', 'aaaaaa', 'aa31a');
        $admin = $this->insertUser('use12r121', 'a1312@a.a', 'aaaaaa', 'aa131a', '1');
        $language = new Language([config('db_constants.FIELDS.FLD_LANGUAGES_NAME') => 'C+++1']);
        $language->store();
        $judge = $this->insertJudge('Codeforces3', 'http://www.judge3.com', 'http://www.judge.com');
        $contest = $this->insertContest('Contest1', '2017-12-12 12:12:12', '10', '0');
        $problem1 = $this->insertProblem('Problem1', '10', '20', $judge);
        $question = $this->insertQuestion('Question1', "Hello", 'Answer1', $contest, $user);
        $question->saveAnswer("Ansert1", $admin);
        $submission = $this->insertSubmission('123', 100, 200, '1', $problem1, $user, $language);
        $tag = new Tag([config('db_constants.FIELDS.FLD_TAGS_NAME') => 'Tag0123']);
        $tag->store();

        // Tags + problems
        $problem1->tags()->sync([$tag->id], false);
        $tag = new Tag([config('db_constants.FIELDS.FLD_TAGS_NAME') => 'Tag01232']);
        $tag->store();
        $problem1->tags()->sync([$tag->id], false);
        $problem = $this->insertProblem('Problem1', '10', '20', $judge);
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
        $user->handles()->save($judge, [config('db_constants.FIELDS.FLD_USER_HANDLES_HANDLE') => 'asd']);

        // User + Contest
        $user->participatingContests()->save($contest);
        $admin->organizingContests()->save($contest);
        $this->assertEquals(count($user->participatingContests()), 1);
        $this->assertEquals(count($admin->organizingContests()), 1);
        Log::info($contest->organizingUsers()->getResults());
        Log::info($contest->participatingUsers()->getResults());
    }

}
