<?php

namespace Tests\Browser\Pages;

use App\Utilities\Constants;
use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;

class AddContestPage extends BasePage
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return 'http://127.0.0.1:8000/contest/add';
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
     * Save contest to db
     * @param Browser $browser
     * @param $name
     * @param $time
     * @param $duration
     * @param $visibility
     * @param array $problemsIDs
     * @param array $organizers
     * @param array $invitees
     * @return mixed
     */
    public function saveContest(Browser $browser, $name, $time, $duration, $visibility, $problemsIDs = [], $organizers = [], $invitees = [])
    {
        // Clear problems session
        $browser->script(['sessionStorage.setItem(app.problemsIDsSessionKey, "")']);

        // Type basic info
        $browser
            ->type('name', $name)
            ->type('time', $time)
            ->script([
                "$('#duration').show()",
                "$('#duration').val($duration)"
            ]);

        // Check problems boxes
        foreach ($problemsIDs as $problemsID) {
            $browser->check('#problem-checkbox-' . $problemsID);
        }
        $organizers = $this->formatArrayToJavascriptFormat($organizers);
        $invitees = $this->formatArrayToJavascriptFormat($invitees);

        // Save organizers and invitees to session (cannot do better no support for auto complete)
        $browser->script(['sessionStorage.setItem(app.organizersSessionKey, \'' . $organizers . '\')']);
        $browser->script(['sessionStorage.setItem(app.inviteesSessionKey, \'' . $invitees . '\')']);
        if ($visibility) {
            $browser->script(['$("#private_visibility").prop(\'checked\', true);$("#public_visibility").prop(\'checked\', false);']);
        } else
            $browser->script(['$("#public_visibility").prop(\'checked\', true);$("#private_visibility").prop(\'checked\', false);']);

        // Save and return new model
        $browser->press('Save');
    }

    /**
     * Apply filters to problems
     *
     * @param Browser $browser
     * @param array $tagsNames
     * @param array $judgesIDs
     */
    public function applyFilters(Browser $browser, $tagsNames = [], $judgesIDs = [])
    {
        // Click judges
        foreach ($judgesIDs as $judgeID) {
            $browser->check('#judge-checkbox-' . $judgeID);
        }

        // Store tags after formatting to match javascript
        $tagsNames = $this->formatArrayToJavascriptFormat($tagsNames);

        // Save to session
        $browser->script(['sessionStorage.setItem(app.tagsSessionKey, \'' . $tagsNames . '\')']);

        // Apply filters
        $browser->click('#apply-filters');
    }

    /**
     * Format php array to javascript session format
     *
     * @param $array
     * @return string
     */
    public function formatArrayToJavascriptFormat($array)
    {
        $arr = '';
        if (count($array) > 0) {
            $arr = '["';
            foreach ($array as $element) {
                $arr .= $element;

                if ($element !== end($array))
                    $arr .= '","';
            }
            $arr .= '"]';
        }
        return $arr;
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
