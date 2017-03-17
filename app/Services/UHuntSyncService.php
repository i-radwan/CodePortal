<?php

namespace App\Services;

use Log;
use Exception;
use App\Models\User;
use App\Models\Problem;
use App\Models\Submission;
use App\Models\Tag;
use App\Models\Language;
use App\Utilities\Constants;
use App\Services\UHuntSyncService as UHunt;

abstract class UHuntSyncService extends JudgeSyncService
{
    //
    // uHunt response constants
    //


    /**
     * Parse the fetched raw problems data from the online judge's api and sync
     * them with our local database
     *
     * @return bool whether the synchronization process completed successfully or not
     */
    protected function syncProblemsWithDatabase()
    {
        try {

        }
        catch (Exception $ex) {
            Log::error("Exception occurred while syncing $this->judgeName problems: " . $ex->getMessage());
            return false;
        }

        return true;
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

    }

    /**
     * Parse the fetched raw submissions data from the online judge's api and sync
     * them with our local database
     *
     * @return bool whether the synchronization process completed successfully or not
     */
    protected function syncSubmissionsWithDatabase()
    {
        try {

        }
        catch (Exception $ex) {
            Log::error("Exception occurred while syncing $this->judgeName submissions: " . $ex->getMessage());
            return false;
        }

        return true;
    }
}