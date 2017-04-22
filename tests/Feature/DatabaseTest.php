<?php

namespace Tests\Feature;

use Artisan;
use App\Models\User;
use App\Models\Contest;
use App\Models\Problem;
use App\Models\Submission;
use App\Models\Question;
use App\Models\Judge;
use App\Models\Tag;
use App\Models\Language;
use App\Utilities\Constants;
use Tests\CreatesApplication;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class DatabaseTest extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp()
    {
        putenv('DB_CONNECTION=mysql_testing');
        parent::setUp();
        Artisan::call('migrate');
    }

    public function insertUser($username, $email, $password, $role = '0')
    {
        $user = new User([
            Constants::FLD_USERS_USERNAME => $username,
            Constants::FLD_USERS_EMAIL => $email,
            Constants::FLD_USERS_PASSWORD => $password
        ]);
        $user->role = $role;
        $user->save();
        return $user;
    }

    public function insertContest($name, $time, $duration, $visibility, $owner)
    {
        $contest = new Contest([
            Constants::FLD_CONTESTS_NAME => $name,
            Constants::FLD_CONTESTS_TIME => $time,
            Constants::FLD_CONTESTS_DURATION => $duration,
            Constants::FLD_CONTESTS_VISIBILITY => $visibility
        ]);
        $contest->owner()->associate($owner);
        $contest->save();
        return $contest;
    }

    public function insertProblem($name, $acceptedSubmissionsCount, $judge, $judgeFirstKey, $judgeSecondKey)
    {
        $problem = new Problem([
            Constants::FLD_PROBLEMS_NAME => $name,
            Constants::FLD_PROBLEMS_JUDGE_FIRST_KEY => $judgeFirstKey,
            Constants::FLD_PROBLEMS_JUDGE_SECOND_KEY => $judgeSecondKey,
            Constants::FLD_PROBLEMS_SOLVED_COUNT => $acceptedSubmissionsCount
        ]);
        $problem->judge()->associate($judge);
        $problem->save();
        return $problem;
    }

    public function insertSubmission($submissionID, $executionTime, $usedMemory, $verdict, $problem, $user, $language)
    {
        $submission = new Submission([
            Constants::FLD_SUBMISSIONS_JUDGE_SUBMISSION_ID => $submissionID,
            Constants::FLD_SUBMISSIONS_SUBMISSION_TIME => 123,
            Constants::FLD_SUBMISSIONS_EXECUTION_TIME => $executionTime,
            Constants::FLD_SUBMISSIONS_CONSUMED_MEMORY => $usedMemory,
            Constants::FLD_SUBMISSIONS_VERDICT => $verdict
        ]);
        $submission->user()->associate($user);
        $submission->problem()->associate($problem);
        $submission->language()->associate($language);
        $submission->save();
        return $submission;
    }

    public function insertQuestion($title, $content, $answer, $contest, $user, $problem, $status = '0')
    {
        $question = Question::askQuestion([
            Constants::FLD_QUESTIONS_TITLE => $title,
            Constants::FLD_QUESTIONS_CONTENT => $content,
            Constants::FLD_QUESTIONS_ANSWER => $answer,
            Constants::FLD_QUESTIONS_STATUS => $status
        ], $user, $contest, $problem);
        
        return $question;
    }

    public function insertJudge($id, $name, $link)
    {
        $judge = new Judge([
            Constants::FLD_JUDGES_ID => $id,
            Constants::FLD_JUDGES_NAME => $name,
            Constants::FLD_JUDGES_LINK => $link
        ]);
        $judge->save();
        return $judge;
    }

    public function insertTag($name)
    {
        $tag = new Tag([Constants::FLD_TAGS_NAME => $name]);
        $tag->save();
        return $tag;
    }

    public function insertLanguage($name)
    {
        $language = new Language([Constants::FLD_LANGUAGES_NAME => $name]);
        $language->save();
        return $language;
    }

    protected function tearDown()
    {
        Artisan::call('migrate:reset');
        parent::tearDown();
        putenv('DB_CONNECTION=mysql');
    }
}
