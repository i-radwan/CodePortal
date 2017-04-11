<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Utilities\Constants;
use App\Utilities\Utilities;
use Illuminate\Http\Request;
use App\Models\Contest;
use App\Models\User;
use Auth;
use Session;

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
        return view('contests.index', compact('data', $contestsData))->with('pageTitle', config('app.name') . ' | Contests');
    }

    /**
     * Show single contest page
     * @param $contestID
     * @return $this
     */
    public function displayContest($contestID)
    {
        $contest = Contest::find($contestID);
        $currentUser = Auth::user();

        if (!$contest) return redirect('contests/');
        $data = [];

        // Check if user is participating or owning the contest to show btns
        $this->getLeaveAndDeleteButtonsVisibility($currentUser, $contest, $data);

        // Get basic contest info
        $this->getBasicContestInfo($contest, $data);

        // Get participants data
        $this->getParticipantsInfo($contest, $data);

        // Get questions data
        $this->getQuestionsInfo($currentUser, $contest, $data);

        return view('contests.contest')->with('data', $data)->with('pageTitle', config('app.name') . ' | ' . $contest->name);
    }

    /**
     * Show add/edit contest page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addEditContestView()
    {
        return view('contests.add_edit')->with('pageTitle', config('app.name') . ' | Contest');
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

    public function deleteContest($contestID)
    {
        $user = Auth::user();
        $contest = $user->owningContests()->find($contestID);
        if ($contest) $contest->delete();
        return redirect('contests/');
    }

    public function leaveContest($contestID)
    {
        $user = Auth::user();
        $contest = $user->owningContests()->find($contestID);
        if ($contest)
            $user->participatingContests()->detach($contestID);
        return back();
    }

    public function joinContest($contestID)
    {
        $user = Auth::user();
        $contest = $user->owningContests()->find($contestID);
        if ($contest)
            $user->participatingContests()->save(Contest::find($contestID));
        return back();
    }

    /**
     * Ask question related to the contest problems
     * ToDo add problem id after @Wael gets problems
     * @param Request $request
     * @param $contestID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addQuestion(Request $request, $contestID)
    {
        $user = Auth::user();

        // Check if user is signed in
        if ($user) {
            $contest = $user->participatingContests()->find($contestID);

            // Check if contest exists (user participating in it) and the contest is running now
            if ($contest && $contest->isContestRunning()) {
                $question = new Question($request->all());
                $question->user()->associate($user);
                $question->contest()->associate($contest);
                $question->save();
                return back();
            }
        }
        Session::flash('question-error', 'Sorry, you cannot perform this action right now!');
        return back();
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
                Constants::TABLE_DATA_KEY => $this->getContestRowData($contest)
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
                Constants::TABLE_LINK_KEY => url('contest/' . $contest->id) // ToDo add contest page link
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
        return User::find($ownerID)->username;
    }

    /**
     * Check if the user owns the contest, then set delete contest button to visible
     * check if the user is participating in this contest, then set leave contest
     * button to visible
     * @param $user
     * @param $contest
     * @param $data
     */
    private function getLeaveAndDeleteButtonsVisibility($user, $contest, &$data)
    {
        $leaveBtnVisible = false;
        $deleteBtnVisible = false;

        if ($user) {
            // Check if the user has joined this contest (to show leave link)
            $leaveBtnVisible =
                ($contest->participatingUsers()->find($user->id) != null);
            // Check if the user is the owner of this contest (to show delete link)
            $deleteBtnVisible =
                ($contest->owner->id == $user->id);
        }

        // Set btns visibility values
        $data[Constants::SINGLE_CONTEST_EXTRA_KEY]
        [Constants::SINGLE_CONTEST_LEAVE_BTN_VISIBLE_KEY] = $leaveBtnVisible;

        $data[Constants::SINGLE_CONTEST_EXTRA_KEY]
        [Constants::SINGLE_CONTEST_DELETE_BTN_VISIBLE_KEY] = $deleteBtnVisible;
    }

    /**
     * Get contest basic info (owner, organizers, time, duration)
     * @param $contest
     * @param $data
     */
    private function getBasicContestInfo($contest, &$data)
    {
        $contestInfo = [];
        // Get contest id
        $contestInfo[Constants::SINGLE_CONTEST_ID_KEY] = $contest->id;

        // Get contest name
        $contestInfo[Constants::SINGLE_CONTEST_NAME_KEY] = $contest->name;

        // Get owner name
        $contestInfo[Constants::SINGLE_CONTEST_OWNER_KEY] = $contest->owner->username;

        // Get organizers array
        $contestInfo[Constants::SINGLE_CONTEST_ORGANIZERS_KEY] =
            $contest->organizingUsers()->pluck(Constants::FLD_USERS_USERNAME)->toArray();

        // Get duration in hrs:mins format
        $contestInfo[Constants::SINGLE_CONTEST_DURATION_KEY] =
            Utilities::convertMinsToHoursMins($contest->duration);

        // Get time and convert to familiar format
        $contestInfo[Constants::SINGLE_CONTEST_TIME_KEY] =
            date('D M y, H:i', strtotime($contest->time));

        // Get contest running status
        $data[Constants::SINGLE_CONTEST_EXTRA_KEY]
        [Constants::SINGLE_CONTEST_RUNNING_STATUS] = $contest->isContestRunning();

        // Set contest info
        $data[Constants::SINGLE_CONTEST_CONTEST_KEY] = $contestInfo;
    }

    /**
     * Get contest participants specific data
     * @param $contest
     * @param $data
     */
    private function getParticipantsInfo($contest, &$data)
    {
        $participants =
            $contest
                ->participatingUsers()
                ->select(Constants::PARTICIPANTS_DISPLAYED_FIELDS)
                ->get()->toArray();

        // Set contest participants
        $data[Constants::SINGLE_CONTEST_PARTICIPANTS_KEY] = $participants;
    }

    /**
     * Get contest questions related to currently signed in user
     * @param $user
     * @param $contest
     * @param $data
     */
    private function getQuestionsInfo($user, $contest, &$data)
    {
        if (!$user) return;

        $questions = $user->contestQuestions($contest->id)
            ->get()->toArray();

        // Set contest questions
        $data[Constants::SINGLE_CONTEST_QUESTIONS_KEY] = $questions;
    }

}
