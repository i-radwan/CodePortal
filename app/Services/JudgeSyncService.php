<?php

namespace App\Services;

use Log;
use App\Models\Judge;
use App\Utilities\Constants;
use Illuminate\Http\Response;

abstract class JudgeSyncService
{
    /**
     * The name of the online judge
     *
     * @var string
     */
    protected $judgeName;

    /**
     * The base url link of the online judge
     *
     * @var string
     */
    protected $judgeLink;

    /**
     * The base url link of the online judge's API
     *
     * @var string
     */
    protected $judgeApiLink;

    /**
     * The problems API's url link
     *
     * @var string
     */
    protected $apiBaseProblemsUrl;

    /**
     * The problems API's url parameters
     *
     * @var array
     */
    protected $apiProblemsParams;

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
    protected $apiSubmissionsParams;

    /**
     * The retrieved raw data response from the judge's API
     *
     * @var string
     */
    protected $rawDataString;

    /**
     * Fetch problems data from the online judge's API
     * and synchronize them with our local database
     *
     * @return void
     */
    public function syncProblems()
    {
        if ($this->fetchProblems() != Response::HTTP_OK) {
            Log::alert(JudgeSyncService::class . "::Failed to fetch problems from" . $this->apiBaseProblemsUrl);
            return;
        }

        if (!$this->syncProblemsWithDatabase()) {
            Log::alert(JudgeSyncService::class . "::Failed to sync problems with database");
            return;
        }
    }

    /**
     * Fetch submissions data from the online judge's API
     * and synchronize them with our local database
     *
     * @return void
     */
    public function syncSubmissions()
    {
        // ToDo
    }

    /**
     * Fetch the data from the online judge by making a request with
     * a certain parameters to the given url.
     * The fetched raw data will be assigned to $rawDataString
     *
     * @param string $url
     * @param array $params
     * @return int the HTTP request status code
     */
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

        return $http_status;
    }

    /**
     * Request and fetch problems data from the online judge's API
     *
     * @return int the HTTP request status code
     */
    protected function fetchProblems()
    {
        return $this->fetchDataFromApi($this->apiBaseProblemsUrl, $this->apiProblemsParams);
    }

    /**
     * Request and fetch submissions data from the online judge's API
     *
     * @return int the HTTP request status code
     */
    protected function fetchSubmissions()
    {
        return $this->fetchDataFromApi($this->apiBaseSubmissionsUrl, $this->apiSubmissionsParams);
    }

    /**
     * Parse the fetched raw problems data from the online judge's api and sync
     * them with our local database
     *
     * @return bool whether the synchronization process completed successfully or not
     */
    abstract protected function syncProblemsWithDatabase();

    /**
     * Parse the fetched raw submissions data from the online judge's api and sync
     * them with our local database
     *
     * @return bool whether the synchronization process completed successfully or not
     */
    abstract protected function syncSubmissionsWithDatabase();

    /**
     * Return an instance of the judge model, if it does not exists then create it first
     *
     * @return Judge
     */
    protected function getJudgeModel()
    {
        $judge = Judge::firstOrNew([Constants::FLD_JUDGES_NAME => $this->judgeName]);

        if (!$judge->exists) {
            $judge->fill([
                Constants::FLD_JUDGES_LINK => $this->judgeLink,
                Constants::FLD_JUDGES_API_LINK => $this->judgeApiLink
            ])->store();
        }

        return $judge;
    }
}