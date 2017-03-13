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
use App\Utilities\Constants;

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
            Constants::FLD_PROBLEMS_NAME => $name,
            Constants::FLD_PROBLEMS_DIFFICULTY => $difficulty,
            Constants::FLD_PROBLEMS_ACCEPTED_SUBMISSIONS_COUNT => $acceptedSubmissionsCount]);
        $problem->judge()->associate($judge);
        $problem->store();
        return $problem;
    }

    public function insertJudge($name, $link, $api_link)
    {
        $judge = new Judge([
            Constants::FLD_JUDGES_NAME => $name,
            Constants::FLD_JUDGES_LINK => $link,
            Constants::FLD_JUDGES_API_LINK => $api_link]);
        $judge->store();
        return $judge;
    }

    public function insertQuestion($title, $content, $answer, $contest, $user, $status = '0')
    {
        $question = new Question([
            Constants::FLD_QUESTIONS_TITLE => $title,
            Constants::FLD_QUESTIONS_CONTENT => $content,
            Constants::FLD_QUESTIONS_ANSWER => $answer,
            Constants::FLD_QUESTIONS_STATUS => $status]);
        $question->contest()->associate($contest);
        $question->user()->associate($user);
        $question->store();
        return $question;
    }

    public function insertUser($name, $email, $password, $username, $role = '0')
    {
        $user = new User([
            Constants::FLD_USERS_NAME => $name,
            Constants::FLD_USERS_EMAIL => $email,
            Constants::FLD_USERS_PASSWORD => $password,
            Constants::FLD_USERS_USERNAME => $username]);
        $user->role = $role;
        $user->save();
        return $user;
    }


    public function insertContest($name, $time, $duration, $visilibty)
    {
        $contest = new Contest(array(
            Constants::FLD_CONTESTS_NAME => $name,
            Constants::FLD_CONTESTS_TIME => $time,
            Constants::FLD_CONTESTS_DURATION => $duration,
            Constants::FLD_CONTESTS_VISIBILITY => $visilibty));
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
        $language = new Language([Constants::FLD_LANGUAGES_NAME => $name]);
        $language->store();
        return $language;
    }

    public function insertTag($name)
    {
        $tag = new Tag([Constants::FLD_TAGS_NAME => $name]);
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
