<?php

namespace Tests\Feature;

use Tests\TestCase;


class UserFlowTest extends TestCase
{
    /**
     * A basic test for user flow.
     *
     * After each step, check DB for correct sync
     *
     * @return void
     */
    public function testApplication()
    {
        // Visit main page
        $this->visit('/')
            ->see('Code Portal');
        // Signup

        // Login

        // ToDo forget password

        //
        $this->assertTrue(true);
    }
}
