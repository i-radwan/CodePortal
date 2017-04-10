<?php

namespace App\Http\Controllers;

use App\Utilities\Constants;
use App\Utilities\Utilities;
use Illuminate\Http\Request;
use App\Models\Contest;
use App\Models\User;

class ContestController extends Controller
{
    /**
     * Show the contests page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get all public contests from database
        $contestsData = $this->prepareContestsTableData();


        return view('contests.index', compact('data', $contestsData));
    }

    /**
     * Show add/edit contest page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addEditContestView()
    {
        return view('contests.add_edit');
    }

    /**
     * Add new contest to database
     * @param Request $request
     */

    public function addContest(Request $request)
    {
        $contest = new Contest($request->all());
        $contest->save();
    }

    /**
     * Update contest in database
     * @param Request $request
     */
    public function editContest(Request $request)
    {

    }

    /**
     * Prepare contests to match contests table format by providing the required
     * headers and data formatting
     * @return array of data for view (contest table input)
     */
    private function prepareContestsTableData()
    {
        $contests = Contest::getPublicContests();

        $rows = [];

        // Prepare problems data for table according to the table protocol
        foreach ($contests as $contest) {
            $rows[] = [
                Constants::TABLE_DATA_KEY => self::getContestRowData($contest)
            ];
        }
        // Return problems table data: headings & rows
        return [
            Constants::TABLE_HEADINGS_KEY => Constants::CONTESTS_TABLE_HEADINGS,
            Constants::TABLE_ROWS_KEY => $rows
        ];
    }

    /**
     * Get specific contest data
     * @param $contest
     * @return array that holds contest data to be showm
     */
    private function getContestRowData($contest)
    {
        // Note that they should be in the same order of the headings
        return [
            [   // ID
                Constants::TABLE_DATA_KEY => $contest->id
            ],
            [   // Name
                Constants::TABLE_DATA_KEY => $contest->name,
                Constants::TABLE_LINK_KEY => "" // ToDo add contest page link
            ],
            [   // Time
                Constants::TABLE_DATA_KEY => $contest->time
            ],
            [   // Duration
                Constants::TABLE_DATA_KEY => Utilities::convertMinsToHoursMins($contest->duration)
            ],
            [   // Owner name
                Constants::TABLE_DATA_KEY => $this->getContestOwnerName($contest->owner_id),
                Constants::TABLE_LINK_KEY => "" // ToDo add owner profile link
            ]
        ];
    }

    /**
     * Get contest owner Name
     * ToDo, if no more functionality, inline this function !
     * @param $ownerID
     * @return mixed
     */
    private function getContestOwnerName($ownerID)
    {
        return User::find($ownerID)->name;
    }
}
