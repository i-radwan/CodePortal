<?php

namespace App\Services;

use Log;
use App\Models\Problem;
use App\Models\Judge;
use App\Models\Tag;
use App\Utilities\Constants;
use App\Services\JudgeSyncService;
use App\Services\CodeforcesSyncService as Codeforces;

class CodeforcesSyncService extends JudgeSyncService
{
    /**
     * The name of the online judge
     *
     * @var string
     */
    protected $judgeName = "Codeforces";

    /**
     * The base url link of the online judge
     *
     * @var string
     */
    protected $judgeLink = "http://codeforces.com/";

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
        //"tags" => "implementation"
    ];

    /**
     * The submissions API's url link
     *
     * @var string
     */
    protected $apiBaseSubmissionsUrl;

    /**
     * The submissions API's url parameters
     *
     * @var array
     */
    protected $apiSubmissionsParams = [

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
    const SUBMISSIONS = "submissions";


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
            Log::alert(Codeforces::class . "::" . $data[Codeforces::RESPONSE_COMMENT]);
            return false;
        }

        // Get the judge model in order to associate it with the problems
        $codeforces = $this->getJudgeModel();

        // Get the main objects from the response data
        $result = $data[Codeforces::RESPONSE_RESULT];
        $problems = $result[Codeforces::PROBLEMS];
        $problemStatistics = $result[Codeforces::PROBLEM_STATISTICS];
        $len = sizeof($problems);

        // Loop through each problem in the return data
        for ($i = 0; $i < $len; ++$i) {
            // Extract problem info
            $contestId = $problems[$i][Codeforces::PROBLEM_CONTEST_ID];
            $problemIdx =  $problems[$i][Codeforces::PROBLEM_INDEX];
            $problemName = $problems[$i][Codeforces::PROBLEM_NAME];
            $problemSolvedCount = $problemStatistics[$i][Codeforces::PROBLEM_SOLVED_COUNT];
            $problemDifficulty = array_key_exists(Codeforces::PROBLEM_POINTS, $problems[$i]) ? $problems[$i][Codeforces::PROBLEM_POINTS] : $this->calculateProblemDifficulty($problemSolvedCount);
            $problemTags = $problems[$i][Codeforces::PROBLEM_TAGS];

            // Search for the problem in the local database, if it does not exists then create a new instance of it
            $problem = Problem::firstOrNew([
                Constants::FLD_PROBLEMS_JUDGE_ID => $codeforces->id,
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

                $codeforces->problems()->save($problem);
                $this->attachProblemTags($problem, $problemTags);
            }
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
        // ToDo
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
     * Parse the fetched raw submissions data from the online judge's api and sync
     * them with our local database
     *
     * @return bool whether the synchronization process completed successfully or not
     */
    protected function syncSubmissionsWithDatabase()
    {
        // ToDo
        return true;
    }
}