<?php

namespace App\Services;

use App\Utilities\Constants;

class UVaSyncService extends UHuntSyncService
{
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
     * The base url link of the online judge's API
     *
     * @var string
     */
    protected $judgeApiLink = "http://uhunt.felix-halim.net/api/";

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
}