<?php

namespace App\Services;

use Exception;
use Log;
use App\Models\User;
use App\Models\Problem;
use App\Models\Submission;
use App\Models\Judge;
use App\Models\Tag;
use App\Models\Language;
use App\Utilities\Constants;
use App\Services\UHuntSyncService as UHunt;

abstract class UHuntSyncService extends JudgeSyncService
{
    /**
     * The API's url link from which we can get the user id used in uHunt online judge
     *
     * @var string
     */
    protected $apiBaseUsernameToIdUrl;

    //
    // uHunt response constants
    //

    // Problem object
    const PROBLEM_ID = 0;
    const PROBLEM_NUMBER = 1;
    const PROBLEM_TITLE = 2;
    const PROBLEM_ACCEPTED_COUNT = 18;
    const PROBLEM_ACCEPTED_DISTINCT_COUNT = 3;
    const PROBLEM_WA_COUNT = 16;            // Wrong answer
    const PROBLEM_RTE_COUNT = 12;           // Runtime error
    const PROBLEM_TLE_COUNT = 14;           // Time limit exceeded
    const PROBLEM_MLE_COUNT = 15;           // Memory limit exceeded

    // Submission object
    const SUBMISSION_RESPONSE_NAME = "name";
    const SUBMISSION_RESPONSE_USERNAME = "uname";
    const SUBMISSION_RESPONSE_RESULT = "subs";
    const SUBMISSION_ID = 0;
    const SUBMISSION_PROBLEM_ID = 1;
    const SUBMISSION_LANGUAGE = 5;          // 1=ANSI C, 2=Java, 3=C++, 4=Pascal, 5=C++11
    const SUBMISSION_TIME = 4;              // In seconds unix-format
    const SUBMISSION_VERDICT = 2;
    const SUBMISSION_EXECUTION_TIME = 3;    // In milliseconds


    /**
     * Parse the fetched raw problems data from the online judge's api and sync
     * them with our local database
     *
     * @return bool Whether the synchronization process completed successfully or not
     */
    protected function syncProblemsWithDatabase()
    {
        $data = json_decode($this->rawDataString, true);

        // Loop through each problem in the return data
        foreach ($data as $problemData) {
            $this->saveProblem($problemData);
        }

        return true;
    }

    /**
     * Parse the given problem data and save the problem into the database, if it is already exists then
     * update its info
     *
     * @param array $problemData
     * @return void
     */
    protected function saveProblem($problemData)
    {
        // Search for the problem in the local database, if it does not exists then create a new instance of it
        $problem = $this->judge->problems()->firstOrNew([
            Constants::FLD_PROBLEMS_JUDGE_FIRST_KEY => $problemData[UHunt::PROBLEM_ID]
        ]);

        // Extract problem info
        $problemSolvedCount = $problemData[UHunt::PROBLEM_ACCEPTED_COUNT];
        $dacuCount = $problemData[UHunt::PROBLEM_ACCEPTED_DISTINCT_COUNT]; // DACU: Distinct Accepted User
        $waCount = $problemData[UHunt::PROBLEM_WA_COUNT];
        $rteCount = $problemData[UHunt::PROBLEM_RTE_COUNT];
        $tleCount = $problemData[UHunt::PROBLEM_TLE_COUNT];
        $mleCount = $problemData[UHunt::PROBLEM_MLE_COUNT];
        $problemDifficulty = $this->calculateProblemDifficulty($dacuCount, $waCount, $rteCount, $tleCount, $mleCount);

        // If the problem already exists then just update its info
        if ($problem->exists) {
            $problem->update([
                Constants::FLD_PROBLEMS_DIFFICULTY => $problemDifficulty,
                Constants::FLD_PROBLEMS_SOLVED_COUNT => $problemSolvedCount
            ]);
        }

        // Fill the problem's data and save it to our local database
        $problem->fill([
            Constants::FLD_PROBLEMS_JUDGE_SECOND_KEY => $problemData[UHunt::PROBLEM_NUMBER],
            Constants::FLD_PROBLEMS_NAME => $problemData[UHunt::PROBLEM_TITLE],
            Constants::FLD_PROBLEMS_DIFFICULTY => $problemDifficulty,
            Constants::FLD_PROBLEMS_SOLVED_COUNT => $problemSolvedCount
        ]);

        $this->judge->problems()->save($problem);
    }

    /**
     * Fetch submissions data from the online judge's API
     * and synchronize them with our local database
     * @param User $user
     * @return bool Whether the submissions synchronization process completed successfully
     */
    public function syncSubmissions(User $user = null)
    {
        try {
            $handle = $user->handle($this->judge);

            if (!$handle) {
                Log::warning("$user->username has no handle on $this->judgeName.");
                return false;
            }

            if (!$this->getJudgeUserId($handle)) {
                Log::alert("Failed to fetch user id from $this->judgeName.");
                return false;
            }

            $this->apiBaseSubmissionsUrl = $this->apiBaseSubmissionsUrl . $this->rawDataString;

            return parent::syncSubmissions($user);
        }
        catch (Exception $ex) {
            Log::error("Exception occurred while syncing $this->judgeName submissions: " . $ex->getMessage());
            return false;
        }
    }

    /**
     * Fetch the user id used in uHunt online judge in order to be able to fetch the submissions.
     * The fetched id will be assigned to $rawDataString
     *
     * @param string $handle
     * @return bool Whether the id was fetched successfully
     */
    protected function getJudgeUserId($handle)
    {
        return $this->fetchDataFromApi($this->apiBaseUsernameToIdUrl . $handle);
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
        $submissions = $data[UHunt::SUBMISSION_RESPONSE_RESULT];

        foreach ($submissions as $submissionData) {
            $this->saveSubmission($user, $submissionData);
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
            Constants::FLD_SUBMISSIONS_JUDGE_SUBMISSION_ID => $submissionData[UHunt::SUBMISSION_ID]
        ]);

        // Extract submission info
        $submissionExecutionTime = $submissionData[UHunt::SUBMISSION_EXECUTION_TIME];
        $submissionVerdict = $this->getVerdict($submissionData[UHunt::SUBMISSION_VERDICT]);

        // If submission already exists then just update its verdict and execution time
        if ($submission->exists) {
            $submission->update([
                Constants::FLD_SUBMISSIONS_EXECUTION_TIME => $submissionExecutionTime,
                Constants::FLD_SUBMISSIONS_VERDICT => $submissionVerdict
            ]);
            return;
        }

        // Get submission problem model
        $problem = $this->judge->problems()->firstOrNew([
            Constants::FLD_PROBLEMS_JUDGE_FIRST_KEY => $submissionData[UHunt::SUBMISSION_PROBLEM_ID]
        ]);
        if (!$problem->exists) {
            // If the problem is not found in our database then skip saving this submission
            return;
        }

        // Get language model or create it if it does not exist
        $language = Language::firstOrCreate([Constants::FLD_LANGUAGES_NAME => $this->getLanguageName($submissionData[UHunt::SUBMISSION_LANGUAGE])]);

        // Fill in submission data and store it in our local database
        $submission->fill([
            Constants::FLD_SUBMISSIONS_PROBLEM_ID => $problem->id,
            Constants::FLD_SUBMISSIONS_LANGUAGE_ID => $language->id,
            Constants::FLD_SUBMISSIONS_SUBMISSION_TIME => $submissionData[UHunt::SUBMISSION_TIME],
            Constants::FLD_SUBMISSIONS_EXECUTION_TIME => $submissionExecutionTime,
            Constants::FLD_SUBMISSIONS_CONSUMED_MEMORY => '0',  //TODO:
            Constants::FLD_SUBMISSIONS_VERDICT => $submissionVerdict
        ]);
        $submission->save();
    }

    /**
     * Calculate the difficulty of the problem based on the statistics of
     * the users submissions of the problem
     *
     * @param int $distinctSolvedCount
     * @param int $wrongAnswerCount
     * @param int $runtimeErrorCount
     * @param int $timeLimitExceededCount
     * @param int $memoryLimitExceededCount
     * @return int the calculated difficulty
     */
    protected function calculateProblemDifficulty($distinctSolvedCount, $wrongAnswerCount, $runtimeErrorCount, $timeLimitExceededCount, $memoryLimitExceededCount)
    {
        //TODO:
        return 0;
    }

    /**
     * Map uHunt verdict id to our own verdict id
     *
     * @param string $verdictId
     * @return int
     */
    protected function getVerdict($verdictId)
    {
        //TODO:
        /*
            10 : Submission error       #?!
            15 : Can't be judged        #?!
            20 : In queue
            30 : Compile error
            35 : Restricted function    #*
            40 : Runtime error
            45 : Output limit           #*
            50 : Time limit
            60 : Memory limit
            70 : Wrong answer
            80 : PresentationE
            90 : Accepted
         */

        switch ($verdictId) {
            case '10':
                return Constants::SUBMISSION_VERDICT["IDLENESS_LIMIT_EXCEEDED"];
            case '15':
                return Constants::SUBMISSION_VERDICT["REJECTED"];
            case '20':
                return Constants::SUBMISSION_VERDICT["TESTING"];
            case '30':
                return Constants::SUBMISSION_VERDICT["COMPILATION_ERROR"];
            case '35':
                return Constants::SUBMISSION_VERDICT["SECURITY_VIOLATED"];
            case '40':
                return Constants::SUBMISSION_VERDICT["RUNTIME_ERROR"];
            case '45':
                return Constants::SUBMISSION_VERDICT["INPUT_PREPARATION_CRASHED"];
            case '50':
                return Constants::SUBMISSION_VERDICT["TIME_LIMIT_EXCEEDED"];
            case '60':
                return Constants::SUBMISSION_VERDICT["MEMORY_LIMIT_EXCEEDED"];
            case '70':
                return Constants::SUBMISSION_VERDICT["WRONG_ANSWER"];
            case '80':
                return Constants::SUBMISSION_VERDICT["PRESENTATION_ERROR"];
            case '90':
                return Constants::SUBMISSION_VERDICT["OK"];
            default:
                return Constants::SUBMISSION_VERDICT["UNKNOWN"];
        }
    }

    /**
     * Map uHunt language id to our own language name
     *
     * @param $languageId
     * @return string
     */
    protected function getLanguageName($languageId)
    {
        //TODO:
        // 1=ANSI C, 2=Java, 3=C++, 4=Pascal, 5=C++11
        switch ($languageId) {
            case '1':
                return "ANSI C";
            case '2':
                return "Java";
            case '3':
                return "C++";
            case '4':
                return "Pascal";
            case '5':
                return "C++11";
            default:
                return "Unknown";
        }
    }
}