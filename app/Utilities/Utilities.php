<?php

namespace App\Utilities;

use App\Models\Problem;

class Utilities
{
    /**
     * This function adds a new query to the saved ones and overwrites if needed
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
     * This function takes the problem object and returns the problem link
     * depending on the specified judge
     *
     * @param $problem problem model object
     * @return string url the problem link to the online judge
     */
    public static function generateProblemLink($problem)
    {
        // Get judge data from constants file
        $judge = Constants::JUDGES[$problem->judge_id];

        $link = $judge['problemLink'];
        $replacingArray = $judge['toBeReplaced'];

        foreach ($replacingArray as $key => $value) {
            $link = str_replace($key, $problem->$value, $link);
        }

        return $link;
    }
}