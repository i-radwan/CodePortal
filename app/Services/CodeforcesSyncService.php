<?php

namespace App\Services;

use App\Utilities\Constants;
use App\Services\JudgeSyncService;

class CodeforcesSyncService extends JudgeSyncService
{
    protected $apiBaseProblemsUrl = "http://codeforces.com/api/problemset.problems";
    protected $apiProblemsParams = [
        //"tags" => "implementation"
    ];
    protected $apiBaseSubmissionsUrl;
    protected $apiSubmissionsParams = [];

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

    protected function parseProblemsRawData()
    {
        $data = json_decode($this->rawDataString, true);

        if ($data[CodeforcesSyncService::RESPONSE_STATUS] == CodeforcesSyncService::RESPONSE_STATUS_FAILED) {
            dd(CodeforcesSyncService::RESPONSE_COMMENT);
            return false;
        }

        $result = $data[CodeforcesSyncService::RESPONSE_RESULT];
        $problems = $result[CodeforcesSyncService::PROBLEMS];
        $problemStatistics = $result[CodeforcesSyncService::PROBLEM_STATISTICS];

        $len = sizeof($problems);

        $this->parsedData = array();

        for ($i = 0; $i < $len; ++$i) {
            $this->parsedData[$i] = array(
                "ContestID" => $problems[$i][CodeforcesSyncService::PROBLEM_CONTEST_ID],
                "Index" => $problems[$i][CodeforcesSyncService::PROBLEM_INDEX],
                "Name" => $problems[$i][CodeforcesSyncService::PROBLEM_NAME],
                "Points" => array_key_exists(CodeforcesSyncService::PROBLEM_POINTS, $problems[$i])
                    ? $problems[$i][CodeforcesSyncService::PROBLEM_POINTS] : 0,
                "Tags" => $problems[$i][CodeforcesSyncService::PROBLEM_TAGS],
                "SolvedCount" => $problemStatistics[$i][CodeforcesSyncService::PROBLEM_SOLVED_COUNT]
            );

            if ($problems[$i]["type"] == "QUESTION") {
                dd($problems[$i]);
            }
        }

        dd($this->parsedData);

        return true;
    }

    protected function parseSubmissionsRawData()
    {
        return true;
    }
}