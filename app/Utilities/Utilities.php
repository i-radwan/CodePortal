<?php

namespace App\Utilities;

use App\Models\Problem;

class Utilities
{
    /**
     * Add a new query to the saved ones and overwrites if needed
     *
     * @param string $key the query key to be replaced/added
     * @param string $value the query value
     * @param string $defaultURL
     * @param string $fullUrl request full url
     * @param bool $unsetOrder
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
     * Generate table heading sorting url based on the current request url and
     * the given sort parameter
     *
     * @param string $sortParam
     * @return string The sorting url of the given sort parameter
     */
    public static function getSortURL($sortParam)
    {
        if ($sortParam != request()->get(Constants::URL_QUERY_SORT_PARAM_KEY))
            $order = 'asc';
        else
            $order = request()->get(Constants::URL_QUERY_SORT_ORDER_KEY, 'asc') == 'asc' ? 'desc' : 'asc';

        $params = request()->all();
        $params[Constants::URL_QUERY_SORT_PARAM_KEY] = $sortParam;
        $params[Constants::URL_QUERY_SORT_ORDER_KEY] = $order;

        return request()->url() . '?' . http_build_query($params);
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
     * Convert given minutes count to hours:minutes format
     *
     * @param int $time
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

    /**
     * Format past date time to user-friendly format (Today, Yesterday, ..etc)
     *
     * @param $dateTime
     * @return false|string
     */
    public static function formatPastDateTime($dateTime)
    {
        $dateTime = strtotime($dateTime);

        // If notification date is today, display hrs/mins count
        if ($dateTime >= strtotime("today")) {
            $curTime = time();
            $timeElapsed = $curTime - $dateTime;
            $seconds = $timeElapsed;
            $minutes = round($timeElapsed / 60);
            $hours = round($timeElapsed / 3600);
            if ($minutes == 0 && $hours == 0 && $seconds > 0) {
                return '1 min ago';
            } else if ($minutes < 60) {
                return $minutes . ' min(s) ago';
            } else {
                return $hours . ' hr(s) ago';
            }
        } else if ($dateTime >= strtotime("yesterday"))
            return "Yesterday " . date('H:i', $dateTime);
        return date('M d, H:i', $dateTime);
    }

    /**
     * This function makes the input form data safe for SQL
     *
     * @param string $data input data
     * @return string safe data
     */
    public static function makeInputSafe($data)
    {
        return htmlspecialchars(stripslashes(trim($data)));
    }
}
