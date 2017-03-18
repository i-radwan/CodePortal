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
use App\Services\UHuntSyncService as UHunt;

abstract class UHuntSyncService extends JudgeSyncService
{
    //
    // uHunt response constants
    //

    // Problem object
    const PROBLEM_ID = 0;
    const PROBLEM_NUMBER = 1;
    const PROBLEM_TITLE = 2;
    const PROBLEM_ACCEPTED_COUNT = 18;
    const PROBLEM_ACCEPTED_DISTINCT_COUNT = 3;
    const PROBLEM_WA_COUNT = 16;    // Wrong answer
    const PROBLEM_RTE_COUNT = 12;   // Runtime error
    const PROBLEM_TLE_COUNT = 14;   // Time limit exceeded
    const PROBLEM_MLE_COUNT = 15;   // Memory limit exceeded


    /**
     * Parse the fetched raw problems data from the online judge's api and sync
     * them with our local database
     *
     * @return bool whether the synchronization process completed successfully or not
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
        // Extract problem info
        $problemId = $problemData[UHunt::PROBLEM_ID];
        $problemNumber =  $problemData[UHunt::PROBLEM_NUMBER];
        $problemName = $problemData[UHunt::PROBLEM_TITLE];
        $problemSolvedCount = $problemData[UHunt::PROBLEM_ACCEPTED_COUNT];

        $dacuCount = $problemData[UHunt::PROBLEM_ACCEPTED_DISTINCT_COUNT]; // DACU: Distinct Accepted User
        $waCount = $problemData[UHunt::PROBLEM_WA_COUNT];
        $rteCount = $problemData[UHunt::PROBLEM_RTE_COUNT];
        $tleCount = $problemData[UHunt::PROBLEM_TLE_COUNT];
        $mleCount = $problemData[UHunt::PROBLEM_MLE_COUNT];

        $problemDifficulty = $this->calculateProblemDifficulty($dacuCount, $waCount, $rteCount, $tleCount, $mleCount);

        // Search for the problem in the local database, if it does not exists then create a new instance of it
        $problem = $this->judge->problems()->firstOrNew([
            Constants::FLD_PROBLEMS_JUDGE_FIRST_KEY => $problemId,
            Constants::FLD_PROBLEMS_JUDGE_SECOND_KEY => $problemNumber
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
        }
    }

    /**
     * Calculate the difficulty of the problem based on the statistics of
     * the user submissions of the problem
     *
     * @param $distinctSolvedCount
     * @param $wrongAnswerCount
     * @param $runtimeErrorCount
     * @param $timeLimitExceededCount
     * @param $memoryLimitExceededCount
     * @return int the calculated difficulty
     */
    protected function calculateProblemDifficulty($distinctSolvedCount, $wrongAnswerCount, $runtimeErrorCount, $timeLimitExceededCount, $memoryLimitExceededCount)
    {
        return -1;
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
        return true;
    }
}