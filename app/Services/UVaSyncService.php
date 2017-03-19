<?php

namespace App\Services;

use App\Utilities\Constants;

class UVaSyncService extends UHuntSyncService
{
    /**
     * The id of the online judge
     *
     * @var string
     */
    protected $judgeId = Constants::UVA_ID;

    /**
     * The name of the online judge
     *
     * @var string
     */
    protected $judgeName = Constants::UVA_NAME;

    /**
     * The base url link of the online judge
     *
     * @var string
     */
    protected $judgeLink = Constants::UVA_LINK;

    /**
     * The problems API's url link
     *
     * @var string
     */
    protected $apiBaseProblemsUrl = "http://uhunt.felix-halim.net/api/p";

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
    protected $apiBaseSubmissionsUrl = "http://uhunt.felix-halim.net/api/subs-user/";

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
    protected $apiBaseUsernameToIdUrl = "http://uhunt.felix-halim.net/api/uname2uid/";
}