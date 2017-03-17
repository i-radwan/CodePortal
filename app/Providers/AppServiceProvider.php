<?php

namespace App\Providers;

use Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // greater than validation
        Validator::extend('greater_than', function ($attribute, $value, $parameters, $validator) {
            return $value > (int)$parameters[0];
        });
        // greater than validation
        Validator::extend('less_than', function ($attribute, $value, $parameters, $validator) {
            return $value < (int)$parameters[0];
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
