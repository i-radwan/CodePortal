<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Utilities\Constants;
use App\Utilities\Utilities;
use Illuminate\Http\Request;
use App\Models\Contest;
use App\Models\Problem;
use App\Models\User;
use Auth;
use Session;
use Illuminate\Pagination\LengthAwarePaginator;

class ContestController extends Controller
{
    /**
     * Show all contests page
     * @param Request $request
     * @return $this
     */
    public function index(Request $request)
    {
        $data = [];
        // Get public contests
        $contests = Contest::getPublicContests();

        // Get request page number
        $page = $request->get('page');
        if(!$page) $page = 1;

        // Paginate the retrieved contests
        $paginator = new LengthAwarePaginator($contests->forPage($page, Constants::CONTESTS_COUNT_PER_PAGE), $contests->count(), Constants::CONTESTS_COUNT_PER_PAGE,
            $page);
        
        // Save current page contests to data variable
        $data[Constants::CONTESTS_CONTESTS_KEY] = $paginator->getCollection();

        // Set pagination data
        $data[Constants::CONTESTS_PAGINATOR_KEY] = Utilities::getPaginatorData($paginator);

        // Get all public contests from database
        return view('contests.index')
            ->with('data', $data)
            ->with('pageTitle', config('app.name') . ' | Contests');
    }

    /**
     * Show single contest page
     *
     * @param Contest $contest
     * @return \Illuminate\View\View $this
     */
    public function displayContest(Contest $contest)
    {
        $currentUser = Auth::user();

        if (!$contest) return redirect('contests/');
        $data = [];

        // Check if user is participating or owning the contest to show btns
        $this->getUserOwnerOrParticipant($currentUser, $contest, $data);

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

    /**
     * Delete a certain contest if you're organizer
     * @param $contestID
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function deleteContest($contestID)
    {
        $user = Auth::user();
        $contest = $user->owningContests()->find($contestID);
        if ($contest) $contest->delete();
        return redirect('contests/');
    }

    /**
     * Cancel user participation in a contest
     * @param $contestID
     * @return \Illuminate\Http\RedirectResponse
     */

    // ToDo detach non participated-in contest
    public function leaveContest($contestID)
    {
        $user = Auth::user();
        $contest = $user->participatingContests()->find($contestID);
        if ($contest)
            $user->participatingContests()->detach($contestID);
        return back();
    }

    /**
     * Register user participation in a contest
     * @param Contest $contest
     * @return \Illuminate\Http\RedirectResponse
     */
    // ToDo check re-participating
    public function joinContest(Contest $contest)
    {
        $user = Auth::user();
        if ($contest)
            $user->participatingContests()->save($contest);
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
                // ToDo move to Question model
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
     * Mark question as announcement
     * @param $questionID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function announceQuestion($questionID)
    {
        $question = Question::find($questionID);
        $user = Auth::user();

        // Check if question exists
        if ($question) {

            // Check if organizer is the one who fired this request
            $contest = $user->organizingContests()->find($question->contest_id);
            if ($contest) {
                $question->status = Constants::QUESTION_STATUS[Constants::QUESTION_STATUS_ANNOUNCEMENT_KEY];
                $question->save();
            }
        }
        return back();
    }

    /**
     * Un-mark question as announcement
     * @param $questionID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function renounceQuestion($questionID)
    {
        $question = Question::find($questionID);
        $user = Auth::user();

        // Check if question exists
        if ($question) {

            // Check if organizer is the one who fired this request
            $contest = $user->organizingContests()->find($question->contest_id);
            if ($contest) {
                $question->status = Constants::QUESTION_STATUS[Constants::QUESTION_STATUS_NORMAL_KEY];
                $question->save();
            }
        }
        return back();
    }

    /**
     * Save question answer (provided by contest organizers)
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function answerQuestion(Request $request)
    {
        $questionID = $request->get('question_id');
        $questionAnswer = $request->get('question_answer');

        $question = Question::find($questionID);
        $user = Auth::user();

        // Check if question exists
        if ($question) {

            // Check if organizer is the one who fired this request
            $contest = $user->organizingContests()->find($question->contest_id);
            if ($contest) {
                $question->saveAnswer($questionAnswer, $user);
            }
        }
        return back();
    }

    /**
     * Prepare contests to match contests table format by providing the required
     * headers and data formatting
     * @return array of data for view (contest table input)
     */
    // ToDo to be removed
    private function prepareContestsTableData()
    {
        return Contest::getPublicContests();
    }

    /**
     * Check if the user owns the contest and
     * check if the user is participating in this contest
     * @param $user
     * @param $contest
     * @param $data
     */
    private function getUserOwnerOrParticipant($user, $contest, &$data)
    {
        $isUserParticipating = false;
        $isUserOwner = false;

        if ($user && $contest->owner) {
            // Check if the user has joined this contest
            $isUserParticipating =
                ($contest->participatingUsers()->find($user->id) != null);
            // Check if the user is the owner of this contest
            $isUserOwner =
                ($contest->owner->id == $user->id);
        }

        // Set data values
        $data[Constants::SINGLE_CONTEST_EXTRA_KEY]
        [Constants::SINGLE_CONTEST_IS_USER_PARTICIPATING] = $isUserParticipating;

        $data[Constants::SINGLE_CONTEST_EXTRA_KEY]
        [Constants::SINGLE_CONTEST_IS_USER_OWNER] = $isUserOwner;
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

        // Is user an organizer?
        $data[Constants::SINGLE_CONTEST_EXTRA_KEY]
        [Constants::SINGLE_CONTEST_IS_USER_AN_ORGANIZER] =
            (Auth::user()->organizingContests()->find($contest->id) != null);

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

        // Get user specific questions
        $questions = $user->contestQuestions($contest->id)
            ->get();

        // Get contest announcements ToDo check for better select required fields only
        $announcements = $contest->announcements()
            ->get();

        // Merge announcements and user questions
        $announcements = $announcements->merge($questions);

        // Get extra data from foreign keys
        foreach ($announcements as $announcement) {
            // Get admin username from id
            $announcement[Constants::FLD_QUESTIONS_ADMIN_ID] =
                User::find($announcement[Constants::FLD_QUESTIONS_ADMIN_ID])->username;

            // Get problem number from id
            $announcement[Constants::FLD_QUESTIONS_PROBLEM_ID] =
                Utilities::generateProblemNumber(Problem::find($announcement[Constants::FLD_QUESTIONS_PROBLEM_ID]));
        }

        // Set contest questions
        $data[Constants::SINGLE_CONTEST_QUESTIONS_KEY] = $announcements->toArray();
    }

}
