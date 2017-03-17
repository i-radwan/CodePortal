<?php

namespace App\Services;

use App\Utilities\Constants;

class LiveArchiveSyncService extends UHuntSyncService
{
    /**
     * The name of the online judge
     *
     * @var string
     */
    protected $judgeName = Constants::LIVE_ARCHIVE_NAME;

    /**
     * The base url link of the online judge
     *
     * @var string
     */
    protected $judgeLink = Constants::LIVE_ARCHIVE_LINK;

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
    protected $apiProblemsParams = [

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
}