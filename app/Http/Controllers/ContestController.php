<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Auth;
use Session;
use Redirect;
use URL;
use App\Models\User;
use App\Models\Problem;
use App\Models\Tag;
use App\Models\Judge;
use App\Models\Contest;
use App\Models\Question;
use App\Utilities\Constants;
use App\Utilities\Utilities;
use Illuminate\Http\Request;

class ContestController extends Controller
{
    /**
     * Show all contests page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $data = [];

        $data[Constants::CONTESTS_CONTESTS_KEY] =
            Contest::ofPublic()
                ->orderByDesc(Constants::FLD_CONTESTS_TIME)
                ->paginate(Constants::CONTESTS_COUNT_PER_PAGE);

        // Get all public contests from database
        return view('contests.index')
            ->with('data', $data)
            ->with('pageTitle', config('app.name') . ' | Contests');
    }

    /**
     * Show single contest page
     *
     * Authorization happens in the defined Gate
     *
     * @param Contest $contest
     * @return \Illuminate\View\View
     */
    public function displayContest(Contest $contest)
    {
        $currentUser = Auth::user();

        if (!$contest) {
            return redirect('contests/');
        }

        $data = [];

        // Check if user is participating or owning the contest to show buttons
        $this->getUserOwnerOrParticipant($currentUser, $contest, $data);
        $this->getBasicContestInfo($contest, $data);
        $this->getProblemsInfo($contest, $data);
        $this->getStandingsInfo($contest, $data);
        $this->getStatusInfo($contest, $data);
        $this->getParticipantsInfo($contest, $data);
        $this->getQuestionsInfo($currentUser, $contest, $data);

        return view('contests.contest')
            ->with('data', $data)
            ->with('pageTitle', config('app.name') . ' | ' . $contest->name);
    }

    /**
     * Show add/edit contest page
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function addEditContestView(Request $request)
    {
        // Check server sessions for saved filters data (i.e. tags, organisers, judges)
        $tags = $judges = [];
        // Search and fill session data to send with request
//        dd(Session::has(Constants::CONTESTS_SELECTED_JUDGES));
        if (Session::has(Constants::CONTESTS_SELECTED_FILTERS)) {
            if (isset(Session::get(Constants::CONTESTS_SELECTED_FILTERS)[Constants::CONTESTS_SELECTED_JUDGES])) {
                $judges = Session::get(Constants::CONTESTS_SELECTED_FILTERS)[Constants::CONTESTS_SELECTED_JUDGES];
            }
            if (isset(Session::get(Constants::CONTESTS_SELECTED_FILTERS)[Constants::CONTESTS_SELECTED_TAGS])) {
                $tags = Session::get(Constants::CONTESTS_SELECTED_FILTERS)[Constants::CONTESTS_SELECTED_TAGS];
            }
        }

        // Get problems with applied filters
        $problems = self::getProblemsWithFilters($request, $tags, $judges);


        return view('contests.add_edit')
            ->with('problems', $problems)
            ->with('judges', Judge::all())
            ->with('checkBoxes', 'true')
            ->with(Constants::CONTESTS_SELECTED_TAGS, $tags)
            ->with(Constants::CONTESTS_SELECTED_JUDGES, $judges)
            ->with('pageTitle', config('app.name') . ' | Contest');
    }

    /**
     * Show add group contest page
     *
     * @param Group $group
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addGroupContestView(Group $group)
    {
        // ToDo: after samir, create private contest for the group
        // ToDo: Set the contest owner to group admin
        // ToDo: Send invitations to members to join
        return view('contests.add_edit')->with('pageTitle', config('app.name') . ' | Contest');
    }

    /**
     * Add new contest to database
     *
     * @param Request $request
     * @return mixed
     */
    public function addContest(Request $request)
    {
        // Create contest object
        $contest = new Contest($request->all());

        // Assign owner
        $contest->owner()->associate(Auth::user());

        if ($contest->save()) {

            //Get Organisers and problems

            //Save Organisers
            $organisers = explode(",", $request->get('organisers'));
            $organisers = User::whereIn('username', $organisers)->get(); //It's a Collection but a Model is needed
            foreach ($organisers as $organiser) {
                $contest->organizers()->save($organiser);
            }

            //Add Problems
            $problems = explode(",", $request->get('problems_ids'));
            $contest->problems()->syncWithoutDetaching($problems);

            // Set initial problems order
            $this->updateContestProblemsOrder($contest, $problems);

            // Flush sessions
            Session::forget([Constants::CONTESTS_SELECTED_FILTERS]);

            // Return success message
            Session::flash("messages", ["Contest Added Successfully"]);
            return redirect()->action(
                'ContestController@displayContest', ['id' => $contest->id]
            );
        } else {        // return error message
            Session::flash("messages", ["Sorry, Contest was not added. Please retry later"]);
            return redirect()->action('ContestController@index');
        }
    }

    /**
     * Update contest in database
     *
     * @param Request $request
     */
    public function editContest(Request $request)
    {

    }

    /**
     * Retrieve tags by name for auto complete
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function tagsAutoComplete()
    {
        $data = Tag::select('name')->get();
        return response()->json($data);
    }

    /**
     * Retrieve usernames for auto complete
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function organisersAutoComplete(Request $request)
    {
        $query = $request->get('query');
        $data = User::select([Constants::FLD_USERS_USERNAME . ' as name'])
            ->where(Constants::FLD_USERS_USERNAME, 'LIKE', "%$query%")
            ->get();
        return response()->json($data);
    }

    /**
     * Save problems filters (tags, judges) into server session to later retrieval
     *
     * @param Request $request
     */
    public function applyProblemsFilters(Request $request)
    {
        Session::put(Constants::CONTESTS_SELECTED_FILTERS, $request->get(Constants::CONTESTS_SELECTED_FILTERS));
    }

    /**
     * Clear problems filters (tags, judges) from server session
     */
    public function clearProblemsFilters()
    {
        Session::forget(Constants::CONTESTS_SELECTED_FILTERS);
        return;
    }

    /**
     * Delete a certain contest if you're owner
     *
     * @param Contest $contest
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function deleteContest(Contest $contest)
    {
        // Check if current auth. user is the owner of the contest
        // TODO: delete contest data from all tables
        if (Auth::check() && $contest->owner[Constants::FLD_USERS_ID]
            == Auth::user()[Constants::FLD_USERS_ID]
        ) {
            $contest->delete();
        }
        return redirect('contests/');
    }

    /**
     * Cancel user participation in a contest
     *
     * @param Contest $contest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function leaveContest(Contest $contest)
    {
        $user = Auth::user();
        $user->participatingContests()->detach($contest);
        return back();
    }

    /**
     * Register user participation in a contest
     *
     * Authorization happens in the defined Gate
     *
     * @param Contest $contest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function joinContest(Contest $contest)
    {
        $user = Auth::user();
        $user->participatingContests()->syncWithoutDetaching([$contest->id]);
        return back();
    }

    /**
     * Reorder problems in a contest
     *
     * Authorization happens in the defined Gate (owner-group)
     *
     * @param Request $request
     * @param Contest $contest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reorderContest(Request $request, Contest $contest)
    {
        $problemsIDsNewOrder = $request->get('problems_order');
        $this->updateContestProblemsOrder($contest, $problemsIDsNewOrder);
        return response()->json(['status' => 204], 200);
    }

    /**
     * Ask question related to the contest problems
     *
     * @param Request $request
     * @param int $contestID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addQuestion(Request $request, $contestID)
    {
        $user = Auth::user();

        // Check if user is a participant
        $contest = $user->participatingContests()->find($contestID);
        $problem = $contest->problems()->find($request->get(Constants::FLD_QUESTIONS_PROBLEM_ID));

        // Check if contest exists (user participating in it) and the contest is running now
        if ($contest && $contest->isRunning()) {
            Question::askQuestion($request->all(), $user, $contest, $problem);
            return Redirect::to(URL::previous() . "#questions");
        }

        Session::flash('question-error', 'Sorry, you cannot perform this action right now!');
        return Redirect::to(URL::previous() . "#questions");
    }

    /**
     * Mark question as announcement
     *
     * @param Question $question
     * @return \Illuminate\Http\RedirectResponse
     */
    public function announceQuestion(Question $question)
    {
        // Check if question exists
        if ($question) {
            if (\Gate::allows('owner-organizer-contest', $question[Constants::FLD_QUESTIONS_CONTEST_ID])) {
                $question[Constants::FLD_QUESTIONS_STATUS] = Constants::QUESTION_STATUS[Constants::QUESTION_STATUS_ANNOUNCEMENT_KEY];
                $question->save();
            }
        }

        return Redirect::to(URL::previous() . "#questions");
    }

    /**
     * Un-mark question as announcement
     *
     * @param Question $question
     * @return \Illuminate\Http\RedirectResponse
     */
    public function renounceQuestion(Question $question)
    {
        // Check if question exists
        if ($question) {
            if (\Gate::allows('owner-organizer-contest', $question[Constants::FLD_QUESTIONS_CONTEST_ID])) {
                $question[Constants::FLD_QUESTIONS_STATUS] = Constants::QUESTION_STATUS[Constants::QUESTION_STATUS_NORMAL_KEY];
                $question->save();
            }
        }

        return Redirect::to(URL::previous() . "#questions");
    }

    /**
     * Save question answer (provided by contest organizers)
     *
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
            if (\Gate::allows('owner-organizer-contest', $question->contest_id)) {
                $question->saveAnswer($questionAnswer, $user);
            }
        }

        return Redirect::to(URL::previous() . "#questions");
    }

    /**
     * Check if the user owns the contest and
     * check if the user is participating in this contest
     *
     * @param User $user
     * @param Contest $contest
     * @param array $data
     */
    private function getUserOwnerOrParticipant($user, $contest, &$data)
    {
        $isUserOwner = false;
        $isUserParticipating = false;

        if ($user && $contest->owner) {
            // Check if the user is the owner of this contest
            $isUserOwner = ($contest->owner[Constants::FLD_USERS_ID] == $user[Constants::FLD_USERS_ID]);

            // Check if the user has joined this contest
            $isUserParticipating = ($contest->participants()->find($user[Constants::FLD_USERS_ID]) != null);
        }

        // Set data values
        $data[Constants::SINGLE_CONTEST_EXTRA_KEY][Constants::SINGLE_CONTEST_IS_USER_OWNER] = $isUserOwner;
        $data[Constants::SINGLE_CONTEST_EXTRA_KEY][Constants::SINGLE_CONTEST_IS_USER_PARTICIPATING] = $isUserParticipating;
    }

    /**
     * Get contest basic info (owner, organizers, time, duration)
     *
     * @param Contest $contest
     * @param array $data
     */
    private function getBasicContestInfo($contest, &$data)
    {
        $contestInfo = [];

        // Get contest id
        $contestInfo[Constants::SINGLE_CONTEST_ID_KEY] = $contest[Constants::FLD_CONTESTS_ID];

        // Get contest name
        $contestInfo[Constants::SINGLE_CONTEST_NAME_KEY] = $contest[Constants::FLD_CONTESTS_NAME];

        // Get owner name
        $contestInfo[Constants::SINGLE_CONTEST_OWNER_KEY] = $contest->owner[Constants::FLD_USERS_USERNAME];

        // Get organizers array
        $contestInfo[Constants::SINGLE_CONTEST_ORGANIZERS_KEY] =
            $contest->organizers()->pluck(Constants::FLD_USERS_USERNAME);

        // Get duration in hrs:mins format
        $contestInfo[Constants::SINGLE_CONTEST_DURATION_KEY] =
            Utilities::convertMinsToHoursMins($contest[Constants::FLD_CONTESTS_DURATION]);

        // Get time and convert to familiar format
        $contestInfo[Constants::SINGLE_CONTEST_TIME_KEY] =
            date('D M d, H:i', strtotime($contest[Constants::FLD_CONTESTS_TIME]));

        // Get contest running status
        $data[Constants::SINGLE_CONTEST_EXTRA_KEY][Constants::SINGLE_CONTEST_RUNNING_STATUS]
            = $contest->isRunning();

        // Is user an organizer?
        $data[Constants::SINGLE_CONTEST_EXTRA_KEY][Constants::SINGLE_CONTEST_IS_USER_AN_ORGANIZER]
            = Auth::check() ? (Auth::user()->organizingContests()->find($contest[Constants::FLD_CONTESTS_ID]) != null) : false;

        // Set contest info
        $data[Constants::SINGLE_CONTEST_CONTEST_KEY] = $contestInfo;
    }

    /**
     * Get contest problems data
     *
     * @param Contest $contest
     * @param array $data
     */
    private function getProblemsInfo($contest, &$data)
    {
//        \DB::enableQueryLog();
        $problems = $contest->problemStatistics()->get();
//        dd(\DB::getQueryLog());

        // Set contest problems
        $data[Constants::SINGLE_CONTEST_PROBLEMS_KEY] = $problems;
    }

    /**
     * Get contest standings data
     *
     * ToDo comment function content
     *
     * @param Contest $contest
     * @param array $data
     */
    private function getStandingsInfo($contest, &$data)
    {
        //\DB::enableQueryLog();

        //TODO: fix pagination
        $rawData = $contest
            ->standings()
            ->get();
        //->paginate(Constants::CONTEST_STANDINGS_PER_PAGE, ['*'], 'standings_page');

        //dd(\DB::getQueryLog());

        $standings = [];
        $idx = 0;
        $len = count($rawData);

        for ($i = 0; $i < $len; ++$i) {
            $standings[$idx] = [];
            $cur = &$standings[$idx];
            $curData = (array)$rawData[$i];
            $cur[Constants::FLD_SUBMISSIONS_USER_ID] = $curData[Constants::FLD_SUBMISSIONS_USER_ID];
            $cur[Constants::FLD_USERS_USERNAME] = $curData[Constants::FLD_USERS_USERNAME];
            $cur[Constants::FLD_USERS_SOLVED_COUNT] = $curData[Constants::FLD_USERS_SOLVED_COUNT];
            $cur[Constants::FLD_USERS_TRAILS_COUNT] = $curData[Constants::FLD_USERS_TRAILS_COUNT];
            $cur[Constants::FLD_USERS_PENALTY] = $curData[Constants::FLD_USERS_PENALTY];

            $cur[Constants::TBL_PROBLEMS] = [];
            $problems = &$cur[Constants::TBL_PROBLEMS];

            do {
                $problems[] = [
                    Constants::FLD_SUBMISSIONS_PROBLEM_ID => $curData[Constants::FLD_SUBMISSIONS_PROBLEM_ID],
                    Constants::FLD_PROBLEMS_SOLVED_COUNT => $curData[Constants::FLD_PROBLEMS_SOLVED_COUNT],
                    Constants::FLD_PROBLEMS_TRAILS_COUNT => $curData[Constants::FLD_PROBLEMS_TRAILS_COUNT]
                ];

                if (++$i < $len) {
                    $curData = (array)$rawData[$i];
                }
            } while ($i < $len && $cur[Constants::FLD_SUBMISSIONS_USER_ID] == $curData[Constants::FLD_SUBMISSIONS_USER_ID]);

            --$i;
            ++$idx;
        }

        // Set contest status
        $data[Constants::SINGLE_CONTEST_STANDINGS_KEY] = $standings;
    }

    /**
     * Get contest status data
     *
     * @param Contest $contest
     * @param array $data
     */
    private function getStatusInfo($contest, &$data)
    {
        $submissions = $contest
            ->submissions()
            ->paginate(Constants::CONTEST_SUBMISSIONS_PER_PAGE, ['*'], 'status_page');

        // Set contest status
        $data[Constants::SINGLE_CONTEST_STATUS_KEY] = $submissions;
    }

    /**
     * Get contest participants specific data
     *
     * @param Contest $contest
     * @param array $data
     */
    private function getParticipantsInfo($contest, &$data)
    {
        $participants = $contest
            ->participants()
            ->select(Constants::PARTICIPANTS_DISPLAYED_FIELDS)
            ->paginate(Constants::CONTEST_PARTICIPANTS_PER_PAGE, ['*'], 'participants_page');

        // Set contest participants
        $data[Constants::SINGLE_CONTEST_PARTICIPANTS_KEY] = $participants;
    }

    /**
     * Get contest questions info
     *
     * @param User $user
     * @param Contest $contest
     * @param $data
     * @return array
     */
    private function getQuestionsInfo($user, $contest, &$data)
    {
        // Get contest announcements
        $announcements = $contest->announcements()->get();

        // If user is logged in, get his questions too
        if ($user) {
            // Get user specific questions
            $questions = $user->contestQuestions($contest->id)->get();

            // Merge announcements and user questions
            $announcements = $announcements->merge($questions);
        }

        // Get extra data from foreign keys
        foreach ($announcements as $announcement) {
            // Get admin username from id if answer is provided
            if ($announcement[Constants::FLD_QUESTIONS_ADMIN_ID])
                $announcement[Constants::FLD_QUESTIONS_ADMIN_ID] =
                    User::find($announcement[Constants::FLD_QUESTIONS_ADMIN_ID])->username;

            // Get problem number from id
            $announcement[Constants::FLD_QUESTIONS_PROBLEM_ID] =
                Utilities::generateProblemNumber(Problem::find($announcement[Constants::FLD_QUESTIONS_PROBLEM_ID]));
        }

        // Set contest questions
        $data[Constants::SINGLE_CONTEST_QUESTIONS_KEY] = $announcements;
    }

    /**
     * Get the problems filtered by tags and judges
     *
     * @param $request
     * @param $tagNames
     * @param $judgesIDs
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getProblemsWithFilters($request, $tagNames, $judgesIDs)
    {
        if (count($tagNames) > 0)
            $tagNames = explode(",", $tagNames);
        if (count($judgesIDs) > 0)
            $judgesIDs = explode(",", $judgesIDs);
        return ProblemController::getProblemsToContestController($request, $tagNames, $judgesIDs); // Returning the Problems Data
    }


    /**
     * Update contest problems order in DB
     *
     * @param Contest $contest
     * @param $problemIDs
     */
    private function updateContestProblemsOrder(Contest $contest, $problemIDs)
    {
        $i = 1;
        foreach ($problemIDs as $problemID) {
            $problemPivot = $contest->problems()->find($problemID)->pivot;
            $problemPivot[Constants::FLD_CONTEST_PROBLEMS_PROBLEM_ORDER] = $i;
            $problemPivot->save();
            $i++;
        }
    }
}
