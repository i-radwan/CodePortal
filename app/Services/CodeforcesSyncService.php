<?php

namespace App\Services;

use Log;
use App\Models\User;
use App\Models\Problem;
use App\Models\Submission;
use App\Models\Judge;
use App\Models\Tag;
use App\Models\Language;
use App\Utilities\Constants;
use App\Services\CodeforcesSyncService as Codeforces;

class CodeforcesSyncService extends JudgeSyncService
{
    /**
     * The name of the online judge
     *
     * @var string
     */
    protected $judgeName = Constants::CODEFORCES_NAME;

    /**
     * The base url link of the online judge
     *
     * @var string
     */
    protected $judgeLink = Constants::CODEFORCES_LINK;

    /**
     * The base url link of the online judge's API
     *
     * @var string
     */
    protected $judgeApiLink = "http://codeforces.com/api/";

    /**
     * The problems API's url link
     *
     * @var string
     */
    protected $apiBaseProblemsUrl = "http://codeforces.com/api/problemset.problems";

    /**
     * The problems API's url parameters
     *
     * @var array
     */
    protected $apiProblemsParams = [
        //"tags" => "matrices"
    ];

    /**
     * The submissions API's url link
     *
     * @var string
     */
    protected $apiBaseSubmissionsUrl = "http://codeforces.com/api/user.status";

    /**
     * The submissions API's url parameters
     *
     * @var array
     */
    protected $apiSubmissionsParams = [
        //"handle" => "Momentum",
        //"from" => "1",
        //"count" => "100"
    ];

    //
    // Codeforces response constants
    //

    // Response detail
    const RESPONSE_STATUS = "status";
    const RESPONSE_STATUS_OK = "OK";
    const RESPONSE_STATUS_FAILED = "FAILED";
    const RESPONSE_COMMENT = "comment";
    const RESPONSE_RESULT = "result";

    // Problem object
    const PROBLEMS = "problems";
    const PROBLEM_STATISTICS = "problemStatistics";
    const PROBLEM_CONTEST_ID = "contestId";
    const PROBLEM_INDEX = "index";
    const PROBLEM_NAME = "name";
    const PROBLEM_POINTS = "points";
    const PROBLEM_TAGS = "tags";
    const PROBLEM_SOLVED_COUNT = "solvedCount";

    // Submission object
    const SUBMISSION_ID = "id";
    const SUBMISSION_PROBLEM = "problem";
    const SUBMISSION_LANGUAGE = "programmingLanguage";
    const SUBMISSION_TIME = "creationTimeSeconds";              // In seconds unix-format
    const SUBMISSION_VERDICT = "verdict";
    const SUBMISSION_EXECUTION_TIME = "timeConsumedMillis";     // In milliseconds
    const SUBMISSION_CONSUMED_MEMORY = "memoryConsumedBytes";   // In bytes


    /**
     * Parse the fetched raw problems data from the online judge's api and sync
     * them with our local database
     *
     * @return bool whether the synchronization process completed successfully or not
     */
    protected function syncProblemsWithDatabase()
    {
        $data = json_decode($this->rawDataString, true);

        // Check the response status
        if ($data[Codeforces::RESPONSE_STATUS] == Codeforces::RESPONSE_STATUS_FAILED) {
            Log::alert("$this->judgeName response comment: " . $data[Codeforces::RESPONSE_COMMENT]);
            return false;
        }

        // Get the main objects from the response data
        $result = $data[Codeforces::RESPONSE_RESULT];
        $problems = $result[Codeforces::PROBLEMS];
        $problemStatistics = $result[Codeforces::PROBLEM_STATISTICS];

        // Loop through each problem in the return data
        for ($i = sizeof($problems) - 1; $i >= 0; --$i) {
            $problemData = $problems[$i];
            $problemData[Codeforces::PROBLEM_SOLVED_COUNT] = $problemStatistics[$i][Codeforces::PROBLEM_SOLVED_COUNT];
            $this->saveProblem($problemData);
        }

        return true;
    }

    /**
     * Parse the given problem data and save it into the database, if it is already exists then
     * update its info
     *
     * @param array $problemData
     * @return void
     */
    protected function saveProblem($problemData)
    {
        // Extract problem info
        $contestId = $problemData[Codeforces::PROBLEM_CONTEST_ID];
        $problemIdx =  $problemData[Codeforces::PROBLEM_INDEX];
        $problemName = $problemData[Codeforces::PROBLEM_NAME];
        $problemSolvedCount = array_key_exists(Codeforces::PROBLEM_SOLVED_COUNT, $problemData) ? $problemData[Codeforces::PROBLEM_SOLVED_COUNT] : 0;
        $problemDifficulty = array_key_exists(Codeforces::PROBLEM_POINTS, $problemData) ? $problemData[Codeforces::PROBLEM_POINTS] : $this->calculateProblemDifficulty($problemSolvedCount);
        $problemTags = $problemData[Codeforces::PROBLEM_TAGS];

        // Search for the problem in the local database, if it does not exists then create a new instance of it
        $problem = $this->judge->problems()->firstOrNew([
            Constants::FLD_PROBLEMS_JUDGE_FIRST_KEY => $contestId,
            Constants::FLD_PROBLEMS_JUDGE_SECOND_KEY => $problemIdx
        ]);

        // If the problem already exists then just update its info
        if ($problem->exists) {
            $problem->update([
                Constants::FLD_PROBLEMS_DIFFICULTY => $problemDifficulty,
                Constants::FLD_PROBLEMS_ACCEPTED_SUBMISSIONS_COUNT => $problemSolvedCount
            ]);
        }
        // Else then fill in the problem's data and save it to our local database
        else {
            $problem->fill([
                Constants::FLD_PROBLEMS_NAME => $problemName,
                Constants::FLD_PROBLEMS_DIFFICULTY => $problemDifficulty,
                Constants::FLD_PROBLEMS_ACCEPTED_SUBMISSIONS_COUNT => $problemSolvedCount
            ]);

            // TODO: need to find a way to call store method for input validation
            $this->judge->problems()->save($problem);
            $this->attachProblemTags($problem, $problemTags);
        }
    }

    /**
     * Calculate the difficulty of the problem based on the number of
     * accepted submissions
     *
     * @param int $solvedCount number of accepted submissions
     * @return int the calculated difficulty
     */
    protected function calculateProblemDifficulty($solvedCount)
    {
        // TODO:
        return -1;
    }

    /**
     * Attach the tags to the given problem, if a tag does not exists then
     * create it first
     *
     * @param Problem $problem
     * @param array $problemTags array of problem tag names
     */
    protected function attachProblemTags(Problem $problem, $problemTags)
    {
        foreach ($problemTags as $tagName) {
            $tag = Tag::firstOrCreate([Constants::FLD_TAGS_NAME => $tagName]);
            $problem->tags()->attach($tag->id);
        }
    }

    /**
     * Fetch submissions data from the online judge's API
     * and synchronize them with our local database
     *
     * @param User $user
     * @return bool whether the submissions synchronization process completed successfully
     */
    public function syncSubmissions(User $user)
    {
        $handle = $user
            ->handles()
            ->where(Constants::FLD_USER_HANDLES_JUDGE_ID, $this->judge->id)
            ->first();

        if (!$handle) {
            Log::warning("$user->username has no handle on $this->judgeName.");
            return false;
        }

        $this->apiSubmissionsParams["handle"] = $handle->pivot->handle;
        $this->apiSubmissionsParams["from"] = '1';
        $this->apiSubmissionsParams["count"] = '1000000';

        return parent::syncSubmissions($user);
    }

    /**
     * Parse the fetched raw submissions data from the online judge's api and sync
     * them with our local database
     *
     * @param User $user
     * @return bool whether the synchronization process completed successfully or not
     */
    protected function syncSubmissionsWithDatabase(User $user)
    {
        $data = json_decode($this->rawDataString, true);

        // Check the response status
        if ($data[Codeforces::RESPONSE_STATUS] == Codeforces::RESPONSE_STATUS_FAILED) {
            Log::alert("$this->judgeName response comment: " . $data[Codeforces::RESPONSE_COMMENT]);
            return false;
        }

        // Get the main objects from the response data
        $result = $data[Codeforces::RESPONSE_RESULT];

        // Loop through each submission in the return data
        for ($i = sizeof($result) - 1; $i >= 0; --$i) {
            $this->saveSubmission($user, $result[$i]);
        }

        return true;
    }

    /**
     * Parse the given submission data and save it into the database, if it is already exists then
     * update its info
     *
     * @param User $user
     * @param array $submissionData
     * @return void
     */
    protected function saveSubmission(User $user, $submissionData)
    {
        // Extract submission info
        $submissionId = $submissionData[Codeforces::SUBMISSION_ID];
        $submissionProblem = $submissionData[Codeforces::SUBMISSION_PROBLEM];
        $submissionLanguage = $submissionData[Codeforces::SUBMISSION_LANGUAGE];
        $submissionTime =  $submissionData[Codeforces::SUBMISSION_TIME];
        $submissionExecutionTime = $submissionData[Codeforces::SUBMISSION_EXECUTION_TIME];
        $submissionConsumedMemory = $submissionData[Codeforces::SUBMISSION_CONSUMED_MEMORY];
        $submissionVerdict = Constants::SUBMISSION_VERDICT[array_key_exists(Codeforces::SUBMISSION_VERDICT, $submissionData) ? $submissionData[Codeforces::SUBMISSION_VERDICT] : "UNKNOWN"];

        // Find if submission already exists
        $submission = $this->judge->submissions()->firstOrNew([
            Constants::FLD_SUBMISSIONS_USER_ID => $user->id,
            Constants::FLD_SUBMISSIONS_JUDGE_SUBMISSION_ID => $submissionId
        ]);

        if ($submission->exists) {
            $submission->update([
                Constants::FLD_SUBMISSIONS_EXECUTION_TIME => $submissionExecutionTime,
                Constants::FLD_SUBMISSIONS_CONSUMED_MEMORY => $submissionConsumedMemory,
                Constants::FLD_SUBMISSIONS_VERDICT => $submissionVerdict
            ]);
            return;
        }

        // Find submission problem
        $problem = $this->judge->problems()->firstOrNew([
            Constants::FLD_PROBLEMS_JUDGE_FIRST_KEY => $submissionProblem[Codeforces::PROBLEM_CONTEST_ID],
            Constants::FLD_PROBLEMS_JUDGE_SECOND_KEY => $submissionProblem[Codeforces::PROBLEM_INDEX]
        ]);

        if (!$problem->exists) {
            // TODO:
            return;
        }

        // Get language model or create it if it does not exist
        $language = Language::firstOrCreate([Constants::FLD_LANGUAGES_NAME => $submissionLanguage]);

        $submission->fill([
            Constants::FLD_SUBMISSIONS_PROBLEM_ID => $problem->id,
            Constants::FLD_SUBMISSIONS_LANGUAGE_ID => $language->id,
            Constants::FLD_SUBMISSIONS_SUBMISSION_TIME => $submissionTime,
            Constants::FLD_SUBMISSIONS_EXECUTION_TIME => $submissionExecutionTime,
            Constants::FLD_SUBMISSIONS_CONSUMED_MEMORY => $submissionConsumedMemory,
            Constants::FLD_SUBMISSIONS_VERDICT => $submissionVerdict
        ]);

        $submission->store();
    }
}