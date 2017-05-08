<?php

namespace App\Providers;

use Auth;
use App\Models\Contest;
use App\Models\Group;
use App\Utilities\Constants;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;


class ValidatorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
     
        // Exists in the given table (which is another input e.g. notification type) validation rule
        // 'exists_in' rule
        Validator::extend('resource_exists_in_table', function ($attribute, $value, $parameters, $validator) {
            $validatorData = $validator->getData(); // Contains data in request

            $type = $validatorData[Constants::FLD_NOTIFICATIONS_TYPE];
            $resourceID = $validatorData[Constants::FLD_NOTIFICATIONS_RESOURCE_ID];

            if ($type == 0) { // Check if exists in contests
                return Contest::find($resourceID);
            }
            if ($type == 1) { // Check if exists in groups
                return Group::find($resourceID);
            }

            return false;
        }, "Resource id doesn't exist!");
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
