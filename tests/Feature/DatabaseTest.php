<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Models\Question;
use App\Models\Submission;
use App\Models\Tag;
use App\Models\Language;
use App\Models\Judge;
use App\Models\User;
use App\Models\Contest;
use App\Models\Problem;
use Tests\CreatesApplication;
use Artisan;

abstract class DatabaseTest extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp()
    {
        putenv('DB_CONNECTION=mysql_testing');
        parent::setUp();
        Artisan::call('migrate');
    }

    public function insertProblem($name, $difficulty, $acceptedSubmissionsCount, $judge)
    {
        $problem = new Problem([
            config('db_constants.FIELDS.FLD_PROBLEMS_NAME') => $name,
            config('db_constants.FIELDS.FLD_PROBLEMS_DIFFICULTY') => $difficulty,
            config('db_constants.FIELDS.FLD_PROBLEMS_ACCEPTED_SUBMISSIONS_COUNT') => $acceptedSubmissionsCount]);
        $problem->judge()->associate($judge);
        $problem->store();
        return $problem;
    }

    public function insertJudge($name, $link, $api_link)
    {
        $judge = new Judge([
            config('db_constants.FIELDS.FLD_JUDGES_NAME') => $name,
            config('db_constants.FIELDS.FLD_JUDGES_LINK') => $link,
            config('db_constants.FIELDS.FLD_JUDGES_API_LINK') => $api_link]);
        $judge->store();
        return $judge;
    }

    public function insertQuestion($title, $content, $answer, $contest, $user, $status = '0')
    {
        $question = new Question([
            config('db_constants.FIELDS.FLD_QUESTIONS_TITLE') => $title,
            config('db_constants.FIELDS.FLD_QUESTIONS_CONTENT') => $content,
            config('db_constants.FIELDS.FLD_QUESTIONS_ANSWER') => $answer,
            config('db_constants.FIELDS.FLD_QUESTIONS_STATUS') => $status]);
        $question->contest()->associate($contest);
        $question->user()->associate($user);
        $question->store();
        return $question;
    }

    public function insertUser($name, $email, $password, $username, $role = '0')
    {
        $user = new User([
            config('db_constants.FIELDS.FLD_USERS_NAME') => $name,
            config('db_constants.FIELDS.FLD_USERS_EMAIL') => $email,
            config('db_constants.FIELDS.FLD_USERS_PASSWORD') => $password,
            config('db_constants.FIELDS.FLD_USERS_USERNAME') => $username]);
        $user->role = $role;
        $user->save();
        return $user;
    }


    public function insertContest($name, $time, $duration, $visilibty)
    {
        $contest = new Contest(array(
            config('db_constants.FIELDS.FLD_CONTESTS_NAME') => $name,
            config('db_constants.FIELDS.FLD_CONTESTS_TIME') => $time,
            config('db_constants.FIELDS.FLD_CONTESTS_DURATION') => $duration,
            config('db_constants.FIELDS.FLD_CONTESTS_VISIBILITY') => $visilibty));
        $contest->store();
        return $contest;
    }

    public function insertSubmission($submission_id, $execution_time, $used_memory, $verdict, $problem, $user, $language)
    {
        $submission = new Submission(['submission_id' => $submission_id, 'execution_time' => $execution_time, 'consumed_memory' => $used_memory, 'verdict' => $verdict]);
        $submission->problem()->associate($problem);
        $submission->user()->associate($user);
        $submission->language()->associate($language);
        $submission->store();
        return $submission;
    }


    public function insertLanguage($name)
    {
        $language = new Language([config('db_constants.FIELDS.FLD_LANGUAGES_NAME') => $name]);
        $language->store();
        return $language;
    }

    public function insertTag($name)
    {
        $tag = new Tag([config('db_constants.FIELDS.FLD_TAGS_NAME') => $name]);
        $tag->store();
        return $tag;
    }

    protected function tearDown()
    {
        Artisan::call('migrate:reset');
        parent::tearDown();
        putenv('DB_CONNECTION=mysql');
    }
}
