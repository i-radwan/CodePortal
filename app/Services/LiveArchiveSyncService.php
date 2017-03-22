<?php

namespace App\Services;

use App\Utilities\Constants;

class LiveArchiveSyncService extends UHuntSyncService
{
    /**
     * The id of the online judge
     *
     * @var string
     */
    protected $judgeId = Constants::JUDGE_LIVE_ARCHIVE_ID;

    /**
     * The problems API's url link
     *
     * @var string
     */
    protected $apiBaseProblemsUrl = "https://icpcarchive.ecs.baylor.edu/uhunt/api/p";

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
    protected $apiBaseSubmissionsUrl = "https://icpcarchive.ecs.baylor.edu/uhunt/api/subs-user/";

    /**
     * The submissions API's url parameters
     *
     * @var array
     */
    protected $apiSubmissionsParams = [

    ];

    /**
     * The API's url link from which we can get the user id used in uHunt online judge
     *
     * @var string
     */
    protected $apiBaseUsernameToIdUrl = "https://icpcarchive.ecs.baylor.edu/uhunt/api/uname2uid/";
}