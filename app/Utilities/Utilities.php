<?php

namespace App\Utilities;

use App\Models\Problem;
use Symfony\Component\VarDumper\Cloner\Data;
use \DateTime;
class Utilities
{
    /**
     * Add a new query to the saved ones and overwrites if needed
     *
     * @param $key the query key to be replaced/added
     * @param $value the query value
     * @param $defaultURL
     * @param $fullUrl request full url
     * @return string
     */
    public static function getURL($key, $value, $defaultURL, $fullUrl, $unsetOrder = true)
    {
        $url_parts = parse_url($fullUrl);
        if (isset($url_parts['query'])) {
            parse_str($url_parts['query'], $params);
            if ($unsetOrder) unset($params['order']);
            $params[$key] = $value; //overwriting if page parameter exists
            $url_parts['query'] = http_build_query($params);
            $url = $url_parts['scheme'] . '://' . $url_parts['host'] . ':' . $url_parts['port'] . $url_parts['path'] . '?' . $url_parts['query'];
        } else {
            $url = $defaultURL . "?" . $key . "=" . $value;
        }
        return $url;
    }

    /**
     * Generate the number of the problem based on the hosting judge
     *
     * @param Problem $problem problem model object
     * @return string the id of the problem
     */
    public static function generateProblemNumber($problem)
    {
        // Get judge data from constants file
        $judge = Constants::JUDGES[$problem->judge_id];
        $number = $judge[Constants::JUDGE_PROBLEM_NUMBER_FORMAT_KEY];
        $replacingArray = $judge[Constants::JUDGE_PROBLEM_NUMBER_FORMAT_ATTRIBUTES_KEY];

        foreach ($replacingArray as $key => $value) {
            $number = str_replace($key, $problem->$value, $number);
        }

        return $number;
    }

    /**
     * Generate the link of the problem based on the hosting judge
     *
     * @param Problem $problem problem model object
     * @return string url the problem link to the online judge
     */
    public static function generateProblemLink($problem)
    {
        // Get judge data from constants file
        $judge = Constants::JUDGES[$problem->judge_id];
        $link = $judge[Constants::JUDGE_PROBLEM_LINK_KEY];
        $replacingArray = $judge[Constants::JUDGE_PROBLEM_LINK_ATTRIBUTES_KEY];

        foreach ($replacingArray as $key => $value) {
            $link = str_replace($key, $problem->$value, $link);
        }

        return $link;
    }

    /**
     * This function applies the sortBy array to the problems collection and paginate it
     * then it adds the extra info like headings and return the json encoded string
     *
     * @param $problems
     * @param $sortBy
     * @return string
     */
    public static function prepareProblemsOutput($problems, $sortBy)
    {
        // Apply sorting
        foreach ($sortBy as $sortField) {
            $problems->orderBy($sortField["column"], $sortField["mode"]);
        }
        // Paginate
        $problems = $problems->paginate(Constants::PROBLEMS_COUNT_PER_PAGE);
        // Assign data
        $ret = [
            "headings" => ["ID", "Name", /*"Difficulty",*/
                "# Acc.", "Judge", "Tags"],
            "problems" => $problems,
            "extra" => [
                "checkbox" => "no",
                "checkboxPosition" => "-1",
            ]
        ];
        return json_encode($ret);
    }


    /**
     * This function takes the given sortBy array and if it's empty it generates the
     * basic sort by condition
     *
     * @param $sortBy
     * @return array
     */
    public static function initializeProblemsSortByArray($sortBy)
    {
        if (count($sortBy) == 0) {
            $sortBy = [
                [
                    "column" => Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_ID,
                    "mode" => "asc"
                ]
            ];
        }
        return $sortBy;
    }

    /**
     * Convert given minutes count to hours:minutes format
     * @param $time
     * @param string $format
     * @return string|void
     */
    public static function convertMinsToHoursMins($time, $format = '%02d:%02d')
    {
        if ($time < 1) {
            return;
        }
        $hours = floor($time / 60);
        $minutes = ($time % 60);
        return sprintf($format, $hours, $minutes);
    }

}
