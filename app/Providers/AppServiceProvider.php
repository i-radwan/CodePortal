<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\DuskServiceProvider;
use Illuminate\Support\Facades\Hash;

use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //dont change Hash to bycrypt
        Validator::extend('old', function ($attribute, $value, $parameters) {
            //dd(\Auth::user()->password);
            return Hash::check($value, \Auth::user()->password);
        });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register dusk service provider for testing
        if ($this->app->environment('local', 'testing')) {
            $this->app->register(DuskServiceProvider::class);
        }
    }
}
