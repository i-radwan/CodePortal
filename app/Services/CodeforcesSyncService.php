<?php

namespace App\Services;

use Log;
use Exception;
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
     * The id of the online judge
     *
     * @var string
     */
    protected $judgeId = Constants::CODEFORCES_ID;

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
        "handle" => "Momentum", // Just an example will be replaced during runtime
        "from" => "1",
        "count" => "1000000"
    ];

    //
    // Codeforces response constants
    //

    // Request constants
    const REQUEST_RECENT_SUBMISSIONS = "http://codeforces.com/api/problemset.recentStatus?count=1000";
    const REQUEST_PROBLEM_TAG_PARAM = "tag";
    const REQUEST_SUBMISSION_HANDLE_PARAM = "handle";
    const REQUEST_SUBMISSION_FROM_PARAM = "from";
    const REQUEST_SUBMISSION_COUNT_PARAM = "count";

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
    const SUBMISSION_AUTHOR = "author";
    const SUBMISSION_AUTHOR_MEMBERS = "members";
    const SUBMISSION_AUTHOR_MEMBERS_HANDLE = "handle";
    const SUBMISSION_LANGUAGE = "programmingLanguage";
    const SUBMISSION_TIME = "creationTimeSeconds";              // In seconds unix-format
    const SUBMISSION_VERDICT = "verdict";
    const SUBMISSION_EXECUTION_TIME = "timeConsumedMillis";     // In milliseconds
    const SUBMISSION_CONSUMED_MEMORY = "memoryConsumedBytes";   // In bytes


    /**
     * Parse the fetched raw problems data from the online judge's api and sync
     * them with our local database
     *
     * @return bool Whether the synchronization process completed successfully or not
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
        // Search for the problem in the local database, if it does not exists then create a new instance of it
        $problem = $this->judge->problems()->firstOrNew([
            Constants::FLD_PROBLEMS_JUDGE_FIRST_KEY => $problemData[Codeforces::PROBLEM_CONTEST_ID],
            Constants::FLD_PROBLEMS_JUDGE_SECOND_KEY => $problemData[Codeforces::PROBLEM_INDEX]
        ]);

        // Extract problem info
        $problemSolvedCount = $problemData[Codeforces::PROBLEM_SOLVED_COUNT];
        $problemDifficulty = $this->calculateProblemDifficulty(
            array_key_exists(Codeforces::PROBLEM_POINTS, $problemData) ? $problemData[Codeforces::PROBLEM_POINTS] : 0,
            $problemSolvedCount
        );

        // If the problem already exists then just update its solved count and difficulty
        if ($problem->exists) {
            $problem->update([
                Constants::FLD_PROBLEMS_DIFFICULTY => $problemDifficulty,
                Constants::FLD_PROBLEMS_SOLVED_COUNT => $problemSolvedCount
            ]);
            return;
        }

        // Fill the problem's data and save it to our local database
        $problem->fill([
            Constants::FLD_PROBLEMS_NAME => $problemData[Codeforces::PROBLEM_NAME],
            Constants::FLD_PROBLEMS_DIFFICULTY => $problemDifficulty,
            Constants::FLD_PROBLEMS_SOLVED_COUNT => $problemSolvedCount
        ]);

        $this->judge->problems()->save($problem);
        $this->attachProblemTags($problem, $problemData[Codeforces::PROBLEM_TAGS]);
    }

    /**
     * Fetch submissions data from the online judge's API
     * and synchronize them with our local database
     *
     * @param User $user The user to fetch his submissions, null to fetch last 1000 recent submissions from Codeforces
     * @return bool Whether the submissions synchronization process completed successfully
     */
    public function syncSubmissions(User $user = null)
    {
        try {
            if ($user) {
                $handle = $user->handle($this->judge);

                if (!$handle) {
                    Log::warning("$user->username has no handle on $this->judgeName.");
                    return false;
                }

                $this->apiSubmissionsParams[Codeforces::REQUEST_SUBMISSION_HANDLE_PARAM] = $handle;
            }
            else {
                $this->apiBaseSubmissionsUrl = Codeforces::REQUEST_RECENT_SUBMISSIONS;
                $this->apiSubmissionsParams = null;
            }

            return parent::syncSubmissions($user);
        }
        catch (Exception $ex) {
            Log::error("Exception occurred while syncing $this->judgeName submissions: " . $ex->getMessage());
            return false;
        }
    }

    /**
     * Parse the fetched raw submissions data from the online judge's api and sync
     * them with our local database
     *
     * @param User $user null to sync last 1000 recent submissions from Codeforces
     * @return bool Whether the synchronization process completed successfully or not
     */
    protected function syncSubmissionsWithDatabase(User $user = null)
    {
        $data = json_decode($this->rawDataString, true);

        // Check the response status
        if ($data[Codeforces::RESPONSE_STATUS] == Codeforces::RESPONSE_STATUS_FAILED) {
            Log::alert("$this->judgeName response comment: " . $data[Codeforces::RESPONSE_COMMENT]);
            return false;
        }

        // Get the main object from the response data
        $result = $data[Codeforces::RESPONSE_RESULT];

        if ($user) {
            // Loop through each submission in the return data
            for ($i = sizeof($result) - 1; $i >= 0; --$i) {
                $this->saveSubmission($user, $result[$i]);
            }
        }
        else {
            // Loop through each submission in the return data trying to match the user
            for ($i = sizeof($result) - 1; $i >= 0; --$i) {
                $members = $result[$i][Codeforces::SUBMISSION_AUTHOR][Codeforces::SUBMISSION_AUTHOR_MEMBERS];

                for ($j = sizeof($members) - 1; $j >= 0; --$j) {
                    $user = $this->judge->user($members[$j][Codeforces::SUBMISSION_AUTHOR_MEMBERS_HANDLE]);

                    if ($user) {
                        $this->saveSubmission($user, $result[$i]);
                    }
                }
            }
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
        // Find if submission already exists
        $submission = $this->judge->submissions()->firstOrNew([
            Constants::FLD_SUBMISSIONS_USER_ID => $user->id,
            Constants::FLD_SUBMISSIONS_JUDGE_SUBMISSION_ID => $submissionData[Codeforces::SUBMISSION_ID]
        ]);

        // Extract submission info
        $submissionExecutionTime = $submissionData[Codeforces::SUBMISSION_EXECUTION_TIME];
        $submissionConsumedMemory = $submissionData[Codeforces::SUBMISSION_CONSUMED_MEMORY];
        $submissionVerdict = Constants::SUBMISSION_VERDICT[array_key_exists(Codeforces::SUBMISSION_VERDICT, $submissionData) ? $submissionData[Codeforces::SUBMISSION_VERDICT] : "UNKNOWN"];

        // If submission already exists then just update its verdict, execution time and memory
        if ($submission->exists) {
            $submission->update([
                Constants::FLD_SUBMISSIONS_EXECUTION_TIME => $submissionExecutionTime,
                Constants::FLD_SUBMISSIONS_CONSUMED_MEMORY => $submissionConsumedMemory,
                Constants::FLD_SUBMISSIONS_VERDICT => $submissionVerdict
            ]);
            return;
        }

        // Get submission problem model
        $problem = $this->getSubmissionProblem($submissionData[Codeforces::SUBMISSION_PROBLEM]);

        // Get language model or create it if it does not exist
        $language = Language::firstOrCreate([Constants::FLD_LANGUAGES_NAME => $submissionData[Codeforces::SUBMISSION_LANGUAGE]]);

        // Fill in submission data and store it in our local database
        $submission->fill([
            Constants::FLD_SUBMISSIONS_PROBLEM_ID => $problem->id,
            Constants::FLD_SUBMISSIONS_LANGUAGE_ID => $language->id,
            Constants::FLD_SUBMISSIONS_SUBMISSION_TIME => $submissionData[Codeforces::SUBMISSION_TIME],
            Constants::FLD_SUBMISSIONS_EXECUTION_TIME => $submissionExecutionTime,
            Constants::FLD_SUBMISSIONS_CONSUMED_MEMORY => $submissionConsumedMemory,
            Constants::FLD_SUBMISSIONS_VERDICT => $submissionVerdict
        ]);
        $submission->save();
    }

    /**
     * Return the submission from our local database, if not found then create and insert
     * it first
     *
     * @param $problemData
     * @return \Illuminate\Database\Eloquent\Model The problem related to the submission
     */
    protected function getSubmissionProblem($problemData)
    {
        $problem = $this->judge->problems()->firstOrNew([
            Constants::FLD_PROBLEMS_JUDGE_FIRST_KEY => $problemData[Codeforces::PROBLEM_CONTEST_ID],
            Constants::FLD_PROBLEMS_JUDGE_SECOND_KEY => $problemData[Codeforces::PROBLEM_INDEX]
        ]);

        // If the problem was not found then save it to our database
        if (!$problem->exists) {
            $problemDifficulty = $this->calculateProblemDifficulty(array_key_exists(Codeforces::PROBLEM_POINTS, $problemData) ? $problemData[Codeforces::PROBLEM_POINTS] : -1);

            $problem->fill([
                Constants::FLD_PROBLEMS_NAME => $problemData[Codeforces::PROBLEM_NAME],
                Constants::FLD_PROBLEMS_DIFFICULTY => $problemDifficulty,
                Constants::FLD_PROBLEMS_SOLVED_COUNT => 1
            ]);

            $this->judge->problems()->save($problem);
            $this->attachProblemTags($problem, $problemData[Codeforces::PROBLEM_TAGS]);
        }

        return $problem;
    }

    /**
     * Attach the tags to the given problem, if a tag does not exists then
     * create it first
     *
     * @param Problem $problem
     * @param array $problemTags Array of problem tag names
     */
    protected function attachProblemTags(Problem $problem, $problemTags)
    {
        foreach ($problemTags as $tagName) {
            $tag = Tag::firstOrCreate([Constants::FLD_TAGS_NAME => $tagName]);
            $problem->tags()->attach($tag->id);
        }
    }

    /**
     * Calculate the difficulty of the problem based on the number of
     * accepted submissions
     *
     * @oaram int $problemPoints Problem points given by Codeforces, -1 if not present
     * @param int $solvedCount Number of accepted submissions, -1 if not present
     * @return int The calculated difficulty
     */
    protected function calculateProblemDifficulty($problemPoints = -1, $solvedCount = -1)
    {
        // TODO:
        return $problemPoints;
    }

    /**
     * Map Codeforces tag name to our own tag name
     *
     * @param $tagName
     * @return string
     */
    protected function getTagName($tagName)
    {
        //TODO:
    }

    /**
     * Map Codeforces verdict name to our own verdict id
     *
     * @param string $verdictName
     * @return int
     */
    protected function getVerdict($verdictName)
    {
        //TODO:
    }

    /**
     * Map Codeforces language name to our own language name
     *
     * @param $languageName
     * @return string
     */
    protected function getLanguageName($languageName)
    {
        //TODO:
    }
}
