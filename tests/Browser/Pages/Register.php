<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;

class Register extends BasePage
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return 'http://127.0.0.1:8000/register';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  Browser $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs('/register');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@element' => '#selector',
        ];
    }

    /**
     * Perform user register action
     *
     * @param Browser $browser
     * @param $username
     * @param $email
     * @param $pass1
     * @param $pass2
     */
    public function registerUser(Browser $browser, $username, $email, $pass1, $pass2)
    {
        $browser->type('username', $username)
            ->type('email', $email)
            ->type('password', $pass1)
            ->type('#password-confirm', $pass2)
            ->press('Register');
    }
}
