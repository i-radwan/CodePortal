<?php

namespace Tests\Feature;

use Illuminate\Validation\ValidationException;
use App\Models\Submission;
use App\Models\User;
use App\Models\Problem;
use App\Models\Language;

class SubmissionTest extends DatabaseTest
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSubmission()
    {
        $judge = $this->insertJudge('Codeforces1', 'http://www.judge2.com', 'http://www.judge.com');
        $problem = $this->insertProblem('Problem1', '10', '20', $judge);
        $user = $this->insertUser('user12', 'a2@a.a', 'aaaaaa', 'aaa2');
        $language = new Language([config('db_constants.FIELDS.FLD_LANGUAGES_NAME') => 'C+++']);
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
}