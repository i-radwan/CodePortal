<?php

namespace Tests\Browser;

use App\Models\User;
use App\Utilities\Constants;
use Tests\Browser\Pages\Blogs;
use Tests\Browser\Pages\Contests;
use Tests\Browser\Pages\Groups;
use Tests\Browser\Pages\HomePage;
use Tests\Browser\Pages\Login;
use Tests\Browser\Pages\Problems;
use Tests\Browser\Pages\Register;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserFlowTest extends DuskTestCase
{
    /**
     * A Dusk test
     * @group users
     *
     * @return void
     */
    public function testUserFlow()
    {
        sleep(1);

        $this->browse(function (Browser $browser) {

            // Go to main page
            $browser->visit(new HomePage)
                ->assertSee('Code Portal');

            // Go to login
            $browser->clickLink('Log In')
                ->assertSee('Login')->on(new Login);

            // Go to sign up
            $browser->back()->clickLink('Sign Up')
                ->assertSee('Register')->on(new Register);

            // Go to homepage from code portal link
            $browser->back()->clickLink('Code Portal')
                ->assertSee('Code Portal')->on(new HomePage);

            // Go to contests
            $browser->clickLink('Contests')
                ->assertSee('Contests')->on(new Contests);

            // Go to Problems
            $browser->back()->clickLink('Problems')
                ->assertSee('Problems')->on(new Problems);

            // Go to Blogs
            $browser->back()->clickLink('Blogs')
                ->assertSee('Blogs')->on(new Blogs);

            // Go to groups
            $browser->back()->clickLink('Groups')
                ->assertSee('Groups')->on(new Groups);


            // Invalid login
            $browser->visit(new Login)
                ->assertSee('Login');

            // invalid data
            $browser->type('username', 'asd2')
                ->type('password', 'asd2asd2')
                ->press('Login')
                ->on(new Login)
                ->assertSee('These credentials do not match our records.');

            // empty data
            $browser->press('Login')->on(new Login);

            // ToDo Go to forgot password


            // Sign up

            // invalid and empty data
            $browser->visit(new Register)
                ->assertSee('Register')
                ->registerUser('asd2', 'asd2@asd2', 'asd2asd2', 'asd2asd2')
                ->assertSee('The email must be a valid email address')
                ->on(new Register);

            $browser->registerUser('asd2', 'asd2@asd2.asd2', 'asd2', 'asd2asd2')
                ->assertSee('The password confirmation does not match.')
                ->on(new Register);


            $browser->registerUser('', 'asd2@asd2.asd2', 'asd2asd2', 'asd2asd2')
                ->on(new Register);

            $browser->registerUser('asd2', '', 'asd2asd2', 'asd2asd2')
                ->on(new Register);

            $browser->registerUser('asd2', 'asd2@asd2.asd2', '', '')
                ->on(new Register);


            // valid data
            $browser->registerUser('asd2', 'asd2@asd2.asd2', 'asd2asd2', 'asd2asd2')
                ->on(new HomePage);

            // Logout
            $browser->visit(new HomePage)
                ->clickLink('Logout')
                ->on(new HomePage)
                ->assertSee('Log In')
                ->assertSee('Sign Up');

            // duplicate data
            $browser->visit(new Register)
                ->registerUser('asd2', 'asd2@asd2.asd2', 'asd2asd2', 'asd2asd2')
                ->assertSee('The username has already been taken.')
                ->assertSee('The email has already been taken.')
                ->on(new Register);

            // Log in again
            $browser->visit(new Login)
                ->loginUser('asd2', 'asd2asd2')
                ->on(new HomePage)
                ->assertSee('asd2');

        });
        
        // Delete user
        User::where(Constants::FLD_USERS_USERNAME, '=', 'asd2')->delete();
    }
}
