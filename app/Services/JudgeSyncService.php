<?php

namespace App\Services;

use Illuminate\Http\Response;

abstract class JudgeSyncService
{
    protected $apiBaseProblemsUrl;
    protected $apiProblemsParams;
    protected $apiBaseSubmissionsUrl;
    protected $apiSubmissionsParams;

    protected $rawDataString;
    protected $parsedData;

    public function syncProblems()
    {
        if (!$this->fetchProblems()) {
            return -1;
        }

        if (!$this->parseProblemsRawData()) {
            return -2;
        }

        if (!$this->syncProblemsWithDatabase()) {
            return -3;
        }

        return $this->parsedData;
    }

    public function syncSubmissions()
    {

    }

    protected function fetchDataFromApi($url, $params)
    {
        // Create a new cURL resource handler
        $ch = curl_init();

        // Build request url
        $requestUrl = $url . "?" . http_build_query($params);

        // Set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Grab URL and pass it to the browser
        $this->rawDataString = curl_exec($ch);

        // Get the status of the http request
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Close cURL resource, and free up system resources
        curl_close($ch);

        return ($http_status == Response::HTTP_OK);
    }

    protected function fetchProblems()
    {
        return $this->fetchDataFromApi($this->apiBaseProblemsUrl, $this->apiProblemsParams);
    }

    protected function fetchSubmissions()
    {
        return $this->fetchDataFromApi($this->apiBaseSubmissionsUrl, $this->apiSubmissionsParams);
    }

    abstract protected function parseProblemsRawData();

    abstract protected function parseSubmissionsRawData();

    protected function syncProblemsWithDatabase()
    {
        return true;
    }

    protected function syncSubmissionsWithDatabase()
    {
        return true;
    }
}