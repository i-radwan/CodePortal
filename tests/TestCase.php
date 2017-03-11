<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Artisan;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp()
    {
        putenv('DB_CONNECTION=mysql_testing');
        parent::setUp();
        Artisan::call('migrate');
    }


    protected function tearDown()
    {
        Artisan::call('migrate:reset');
        parent::tearDown();
        putenv('DB_CONNECTION=mysql');
    }

}
