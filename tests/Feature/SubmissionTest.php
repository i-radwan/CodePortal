<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Validation\ValidationException;
use App\Models\Submission;
use App\Models\User;
use App\Models\Problem;
use App\Models\Language;
use App\Models\Judge;

class SubmissionTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSubmission()
    {
        $judge = new Judge(['name' => 'Codeforces1', 'link' => 'http://www.judge2.com', 'api_link' => 'http://www.judge.com']);
        $judge->save();
        $problem = new Problem(['name' => 'Problem1', 'difficulty' => '10', 'accepted_count' => '20']);
        $problem->judge()->associate($judge);
        $problem->save();
        $user = new User(['name' => 'user12', 'email' => 'a2@a.a', 'password' => 'aaaaaa', 'handle' => 'aaa2']);
        $user->save();
        $language = new Language(['name' => 'C+++']);
        $language->save();


        $initialCount = Submission::count();
        // insert valid submission and check for count
        $validSubmission = $this->insertSubmission('2', '200', '20', '0', $problem, $user, $language);
        $this->assertTrue(Submission::count() == $initialCount + 1);
        $validSubmission->delete();
        $this->assertTrue(Submission::count() == $initialCount); // test deleting

        // insert invalid models
        // missing data
        try {
            ($this->insertSubmission('', '200', '20', '0', $problem, $user, $language));
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - missing data");
        } catch (ValidationException $e) {
        }
        // invalid data - execution time
        try {
            ($this->insertSubmission('12', 'a', '20', '0', $problem, $user, $language));
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - invalid execution time");
        } catch (ValidationException $e) {
        }
        // invalid data - used memory
        try {
            ($this->insertSubmission('12', '10', '  ', '0', $problem, $user, $language));
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - invalid used memory");
        } catch (ValidationException $e) {
        }
        // invalid data - verdict
        try {
            ($this->insertSubmission('12', '10', '12', '-1', $problem, $user, $language));
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - invalid used memory");
        } catch (ValidationException $e) {
        }

        // invalid data - verdict
        try {
            ($this->insertSubmission('12', '10', '12', '19', $problem, $user, $language));
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - invalid verdict");
        } catch (ValidationException $e) {
        }
        // invalid data - no user
        try {
            ($this->insertSubmission('12', '10', '12', '19', $problem, User::find(12), $language));
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - invalid user id");
        } catch (ValidationException $e) {
        }
        // invalid data - no problem
        try {
            ($this->insertSubmission('12', '10', '12', '19', Problem::find(12), $user, $language));
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - invalid problem id");
        } catch (ValidationException $e) {
        }

        // invalid data - no language
        try {
            ($this->insertSubmission('12', '10', '12', '19', $problem, $user, Language::find(0)));
            $this->fail("Shouldn't reach here w/out throwing Validation Exception - invalid language id");
        } catch (ValidationException $e) {
        }
        $this->assertTrue(Submission::count() == $initialCount); // not inserted
    }


    public function insertSubmission($submission_id, $execution_time, $used_memory, $verdict, $problem, $user, $language)
    {
        $submission = new Submission(['submission_id' => $submission_id, 'execution_time' => $execution_time, 'used_memory' => $used_memory, 'verdict' => $verdict]);
        $submission->problem()->associate($problem);
        $submission->user()->associate($user);
        $submission->language()->associate($language);
        $submission->store();
        return $submission;
    }
}