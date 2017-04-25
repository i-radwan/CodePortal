<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;

class AddBlogPage extends BasePage
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return 'http://127.0.0.1:8000/blogs/add';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  Browser $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
    }

    /**
     * Submit new blog form
     * @param Browser $browser
     * @param $title
     * @param $body
     */
    public function addBlog(Browser $browser, $title, $body)
    {
        $body = addslashes($body);

        $browser->type('#post-title', $title)
            ->type('#edit-post-body', $body)
            ->press('Submit');
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
}
