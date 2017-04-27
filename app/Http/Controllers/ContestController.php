<?php

namespace App\Http\Controllers;

use App\Exceptions\InvitationException;
use Auth;
use Carbon\Carbon;
use Session;
use Redirect;
use URL;
use App\Models\User;
use App\Models\Problem;
use App\Models\Tag;
use App\Models\Judge;
use App\Models\Contest;
use App\Models\Question;
use App\Models\Group;
use App\Models\Notification;
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
        $endedContests = Contest::ofPublic()
            ->ofEnded()
            ->orderByDesc(Constants::FLD_CONTESTS_TIME)
            ->paginate(Constants::CONTESTS_COUNT_PER_PAGE, ['*'], 'past');

        $upcomingContests = Contest::ofPublic()
            ->ofUpcoming()
            ->orderByDesc(Constants::FLD_CONTESTS_TIME)
            ->paginate(Constants::CONTESTS_COUNT_PER_PAGE, ['*'], 'upcoming');

        $runningContests = Contest::ofPublic()
            ->ofRunning()
            ->orderByDesc(Constants::FLD_CONTESTS_TIME)
            ->paginate(Constants::CONTESTS_COUNT_PER_PAGE, ['*'], 'running');

        return view('contests.index')
            ->with('endedContests', $endedContests)
            ->with('upcomingContests', $upcomingContests)
            ->with('runningContests', $runningContests)
            ->with('pageTitle', config('app.name') . ' | Contests');
    }


    /**
     * Display specific contest view
     *
     * @param Contest $contest
     * @param $displayProblems
     * @param $displayStandings
     * @param $displaySubmissions
     * @param $displayParticipants
     * @param $displayQuestions
     * @return \Illuminate\View\View
     */
    private function displayContestView(Contest $contest, $displayProblems, $displayStandings, $displaySubmissions, $displayParticipants, $displayQuestions)
    {

        // Get contest common info (shared by all contest views [problems, participants, ...etc])
        if (!$this->getContestCommonInfo($contest, $isOwner, $isParticipant, $isUserOrganizer, $contestInfo)) {
            return redirect('contests/'); // contest doesn't exist
        }

        $isContestRunning = $contestInfo[Constants::SINGLE_CONTEST_RUNNING_STATUS];
        $isContestEnded = $contestInfo[Constants::SINGLE_CONTEST_ENDED_STATUS];

        // Get common view
        $view = view('contests.contest')
            ->with('isOwner', $isOwner)
            ->with('isUserOrganizer', $isUserOrganizer)
            ->with('isParticipant', $isParticipant)
            ->with('contestInfo', $contestInfo)
            ->with('isContestRunning', $isContestRunning)
            ->with('isContestEnded', $isContestEnded)
            ->with('view', 'problems')
            ->with('pageTitle', config('app.name') . ' | ' . $contest[Constants::FLD_CONTESTS_NAME]);

        // Get specific contest view data and attach to the view
        if ($displayProblems) {

            $problems = [];

            if ($isContestRunning || $isContestEnded) {
                $this->getProblemsInfo($contest, $problems);
            }

            $view->with('problems', $problems)
                ->with('view', 'problems');

        } else if ($displayStandings) {

            $standings = $problems = [];

            if ($isContestRunning || $isContestEnded) {
                $this->getStandingsInfo($contest, $standings);
                $this->getProblemsInfo($contest, $problems);
            }

            $view->with('standings', $standings)
                ->with('problems', $problems)
                ->with('view', 'standings');

        } else if ($displaySubmissions) {

            $this->getStatusInfo($contest, $submissions);

            $view->with('submissions', $submissions)
                ->with('view', 'submissions');

        } else if ($displayParticipants) {

            $this->getParticipantsInfo($contest, $participants);

            $view->with('participants', $participants)
                ->with('view', 'participants');

        } else if ($displayQuestions) {

            $this->getQuestionsInfo(Auth::user(), $contest, $questions);

            $view->with('questions', $questions)
                ->with('view', 'questions');

        }

        return $view;
    }

    /**
     * Show single contest problems page
     *
     * Authorization happens in the defined Gate
     *
     * @param Contest $contest
     * @return \Illuminate\View\View
     */
    public function displayContestProblems(Contest $contest)
    {
        return $this->displayContestView($contest, true, false, false, false, false);
    }

    /**
     * Show single contest standings page
     *
     * Authorization happens in the defined Gate
     *
     * @param Contest $contest
     * @return \Illuminate\View\View
     */
    public function displayContestStandings(Contest $contest)
    {
        return $this->displayContestView($contest, false, true, false, false, false);
    }

    /**
     * Show single contest standings page
     *
     * Authorization happens in the defined Gate
     *
     * @param Contest $contest
     * @return \Illuminate\View\View
     */
    public function displayContestStatus(Contest $contest)
    {
        return $this->displayContestView($contest, false, false, true, false, false);
    }

    /**
     * Show single contest standings page
     *
     * Authorization happens in the defined Gate
     *
     * @param Contest $contest
     * @return \Illuminate\View\View
     */
    public function displayContestParticipants(Contest $contest)
    {
        return $this->displayContestView($contest, false, false, false, true, false);
    }

    /**
     * Show single contest questions page
     *
     * Authorization happens in the defined Gate
     *
     * @param Contest $contest
     * @return \Illuminate\View\View
     */
    public function displayContestQuestions(Contest $contest)
    {
        return $this->displayContestView($contest, false, false, false, false, true);
    }

    /**
     * Prepare contest common info (needed by all contest tabs) for contest views
     *
     * @param Contest $contest
     * @param $isOwner
     * @param $isParticipant
     * @param $isUserOrganizer
     * @param $contestInfo
     * @return bool
     */
    private function getContestCommonInfo(Contest $contest, &$isOwner, &$isParticipant, &$isUserOrganizer, &$contestInfo)
    {
        $currentUser = Auth::user();

        if (!$contest) {
            return false;
        }

        // Check if user is participating or owning the contest to show buttons
        $this->getUserOwnerOrParticipant($currentUser, $contest, $isOwner, $isParticipant, $isUserOrganizer);
        $this->getBasicContestInfo($contest, $contestInfo);

        return true;
    }

    /**
     * Show add/edit contest page
     *
     * @param \Illuminate\Http\Request $request
     * @param Contest $contest
     *
     * @return \Illuminate\View\View
     */
    public function addEditContestView(Request $request, Contest $contest = null)
    {
        // Check server sessions for saved filters data (i.e. tags, organisers, judges)
        $tags = $judges = [];

        $problems = self::getProblemsWithSessionFilters($request, $tags, $judges);

        // Are filters applied (to inform user that there're filters applied from previous visit)
        $areFiltersApplied = count($tags) || count($judges);
        return view('contests.add_edit')
            ->with('contest', $contest)
            ->with('group', $contest->groups()->first())
            ->with('problems', $problems)
            ->with('judges', Judge::all())
            ->with('checkBoxes', 'true')
            ->with('filtersApplied', $areFiltersApplied)
            ->with('formURL', (!$contest[Constants::FLD_CONTESTS_ID]) ? url('contest/add') : url('contest/' . $contest[Constants::FLD_CONTESTS_ID] . '/edit'))
            ->with('syncFiltersURL', url('/contest/add/contest_tags_judges_filters_sync'))
            ->with('detachFiltersURL', url('/contest/add/contest_tags_judges_filters_detach'))
            ->with(Constants::CONTEST_PROBLEMS_SELECTED_TAGS, $tags)
            ->with(Constants::CONTEST_PROBLEMS_SELECTED_JUDGES, $judges)
            ->with('pageTitle', config('app.name') . ' | ' . (isset($contest)) ? $contest[Constants::FLD_CONTESTS_NAME] : 'Contest');
    }

    /**
     * Show add group contest page
     *
     * @param Group $group
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addGroupContestView(Request $request, Group $group)
    {

        // Check server sessions for saved filters data (i.e. tags, organisers, judges)
        $tags = $judges = [];

        $problems = self::getProblemsWithSessionFilters($request, $tags, $judges);

        return view('contests.add_edit')
            ->with('problems', $problems)
            ->with('judges', Judge::all())
            ->with('checkBoxes', 'true')
            ->with('group', $group)
            ->with('formURL', url('group/' . $group[Constants::FLD_GROUPS_ID] . '/contest/add'))
            ->with('syncFiltersURL', url('/contest/add/contest_tags_judges_filters_sync'))
            ->with('detachFiltersURL', url('/contest/add/contest_tags_judges_filters_detach'))
            ->with(Constants::CONTEST_PROBLEMS_SELECTED_TAGS, $tags)
            ->with(Constants::CONTEST_PROBLEMS_SELECTED_JUDGES, $judges)
            ->with('pageTitle', config('app.name') . ' | Contest');
    }

    /**
     * Save contest to database
     *
     * @param Request $request
     * @param Group $group
     * @param Contest $contest
     *
     * @return mixed
     */
    public function saveContest(Request $request, Group $group = null, Contest $contest = null)
    {
        $editingContest = true;
        if (!$contest) {
            // Create contest object
            $contest = new Contest($request->all());

            // Assign owner
            $contest->owner()->associate(Auth::user());

            $editingContest = false;
        } else {
            $contest[Constants::FLD_CONTESTS_NAME] = $request->get('name');
            $contest[Constants::FLD_CONTESTS_TIME] = $request->get('time');
            $contest[Constants::FLD_CONTESTS_DURATION] = floor($request->get('duration') / 60);
            $contest[Constants::FLD_CONTESTS_VISIBILITY] = $request->get('visibility');
        }

        // Set visibility to private (group only) and unset these values if found
        if ($group) {
            unset($request['organizers']);
            unset($request['invitees']);
            $contest[Constants::FLD_CONTESTS_VISIBILITY] = Constants::CONTEST_VISIBILITY_PRIVATE;
        }

        // Check date if in allowed period
        if (!Carbon::now()->addDays(Constants::CONTESTS_MAX_START_DATETIME)->gte(Carbon::parse($request->get('time')))) {
            return back()->withErrors(['The start date time must be in less than ' . Constants::CONTESTS_MAX_START_DATETIME . ' days']);
        }

        if ($contest->save()) {

            //Get Organisers and problems
            if (!$group) {
                if ($editingContest)
                    $contest->organizers()->detach();

                //Save Organisers if not group contest // ToDo add group admins later
                $organisers = explode(",", $request->get('organisers'));
                $organisers = User::whereIn('username', $organisers)->get(); //It's a Collection but a Model is needed
                foreach ($organisers as $organiser) {
                    if ($organiser[Constants::FLD_USERS_ID] != Auth::user()[Constants::FLD_USERS_ID])
                        $contest->organizers()->save($organiser);
                }
            }
            // Send notifications to Invitees if private contest and not for specific group
            if (!$group && $request->get('visibility') == Constants::CONTEST_VISIBILITY_PRIVATE) {
                // Get invitees
                $invitees = explode(",", $request->get('invitees'));
                $invitees = User::whereIn('username', $invitees)->get(); //It's a Collection but a Model is needed

                foreach ($invitees as $invitee) {
                    // Send notifications
                    try {
                        Notification::make(Auth::user(), $invitee, $contest, Constants::NOTIFICATION_TYPE_CONTEST, false);
                    } catch (InvitationException $e) {
                    }
                }
            } else if ($group) { // Send group members invitations
                // Get invitees
                foreach ($group->members()->get() as $member) {
                    // Send notifications
                    try {
                        Notification::make(Auth::user(), $member, $contest, Constants::NOTIFICATION_TYPE_CONTEST, false);
                    } catch (InvitationException $e) {
                    }
                }

                // Associate contest with group
                if (!$editingContest)
                    $group->contests()->save($contest);
            }

            // Add Problems
            $problems = explode(",", $request->get('problems_ids'));

            // Limit problems array to limit
            $problems = array_slice($problems, 0, Constants::CONTESTS_PROBLEMS_MAX_COUNT);

            // Sync problems
            $contest->problems()->sync($problems);

            // Set initial problems order
            $this->updateContestProblemsOrder($contest, $problems);

            // Flush sessions
            Session::forget([Constants::CONTEST_PROBLEMS_SELECTED_FILTERS]);

            // Return success message
            Session::flash("messages", ["Contest Saved Successfully"]);
            return redirect()->action(
                'ContestController@displayContestProblems', ['id' => $contest[Constants::FLD_CONTESTS_ID]]
            );
        } else {        // return error message
            Session::flash("messages", ["Sorry, Contest was not saved. Please retry later"]);
            return redirect()->action('ContestController@index');
        }
    }

    /**
     * Add contest function
     *
     * @param Request $request
     *
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function addContest(Request $request)
    {
        return $this->saveContest($request, null, null);
    }

    /**
     * Add group contest function
     *
     * @param Request $request
     * @param Group $group
     *
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function addGroupContest(Request $request, Group $group)
    {
        return $this->saveContest($request, $group, null);
    }

    /**
     * Edit contest function
     *
     * @param Request $request
     * @param Contest $contest
     *
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function editContest(Request $request, Contest $contest)
    {
        return $this->saveContest($request, $contest->groups()->first(), $contest);
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
    public function usersAutoComplete(Request $request)
    {
        $query = $request->get('query');
        $data = User::select([Constants::FLD_USERS_USERNAME . ' as name'])
            ->where(Constants::FLD_USERS_USERNAME, 'LIKE', "%$query%")
            ->where(Constants::FLD_USERS_USERNAME, '!=', Auth::user()[Constants::FLD_USERS_USERNAME])
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
        Session::put(Constants::CONTEST_PROBLEMS_SELECTED_FILTERS, $request->get('selected_filters'));
    }

    /**
     * Clear problems filters (tags, judges) from server session
     */
    public function clearProblemsFilters()
    {
        Session::forget(Constants::CONTEST_PROBLEMS_SELECTED_FILTERS);
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
        $user->participatingContests()
            ->syncWithoutDetaching([$contest[Constants::FLD_CONTESTS_ID]]);
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
                $question[Constants::FLD_QUESTIONS_STATUS] = Constants::QUESTION_STATUS_ANNOUNCEMENT;
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
                $question[Constants::FLD_QUESTIONS_STATUS] = Constants::QUESTION_STATUS_NORMAL;
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
     * @param $isOwner
     * @param $isParticipant
     * @param $isUserOrganizer
     */
    private function getUserOwnerOrParticipant($user, $contest, &$isOwner, &$isParticipant, &$isUserOrganizer)
    {
        $isOwner = $isParticipant = $isUserOrganizer = false;

        if ($user && $contest->owner) {
            // Check if the user is the owner of this contest
            $isOwner = ($contest->owner[Constants::FLD_USERS_ID] == $user[Constants::FLD_USERS_ID]);

            // Check if the user has joined this contest
            $isParticipant = ($contest->participants()->find($user[Constants::FLD_USERS_ID]) != null);

            // Check if user is organizer
            $isUserOrganizer = ($user->organizingContests()->find($contest[Constants::FLD_CONTESTS_ID]) != null);
        }
    }

    /**
     * Get contest basic info (owner, organizers, time, duration)
     *
     * @param Contest $contest
     * @param $contestInfo
     */
    private function getBasicContestInfo($contest, &$contestInfo)
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

        // Check if contest has ended
        $contestInfo[Constants::SINGLE_CONTEST_ENDED_STATUS]
            = $contest->isEnded();

        // Get contest running status
        $contestInfo[Constants::SINGLE_CONTEST_RUNNING_STATUS]
            = $contest->isRunning();
    }

    /**
     * Get contest problems data
     *
     * @param Contest $contest
     * @param $problems
     * @param array $data
     */
    private function getProblemsInfo($contest, &$problems)
    {
//        \DB::enableQueryLog();
        $problems = $contest->problemStatistics()->get();
//        dd(\DB::getQueryLog());

    }

    /**
     * Get contest standings data
     *
     * ToDo comment function content
     *
     * @param Contest $contest
     * @param array $standings
     */
    private function getStandingsInfo($contest, &$standings)
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
    }

    /**
     * Get contest status data
     *
     * @param Contest $contest
     * @param $submissions
     */
    private function getStatusInfo($contest, &$submissions)
    {
        $submissions = $contest
            ->submissions()
            ->paginate(Constants::CONTEST_SUBMISSIONS_PER_PAGE);

    }

    /**
     * Get contest participants specific data
     *
     * @param Contest $contest
     * @param $participants
     */
    private function getParticipantsInfo($contest, &$participants)
    {
        $participants = $contest
            ->participants()
            ->select(Constants::PARTICIPANTS_DISPLAYED_FIELDS)
            ->paginate(Constants::CONTEST_PARTICIPANTS_PER_PAGE);
        // Set contest participants
    }

    /**
     * Get contest questions info
     *
     * @param User $user
     * @param Contest $contest
     * @param $announcements
     */
    private function getQuestionsInfo($user, $contest, &$announcements)
    {
        $isOwnerOrOrganizer = \Gate::forUser($user)->allows('owner-organizer-contest', [$contest[Constants::FLD_CONTESTS_ID]]);

        // Get contest announcements
        $announcements = $contest->announcements()->get();

        // If user is logged in and not organizer, get his questions too
        if ($user && !$isOwnerOrOrganizer) {
            // Get user specific questions
            $questions = $user->contestQuestions($contest[Constants::FLD_CONTESTS_ID])->get();

            // Merge announcements and user questions
            $announcements = $announcements->merge($questions);
        } else if ($user && $isOwnerOrOrganizer) {

            // If admin get all questions
            $questions = $contest->questions()
                ->where(Constants::FLD_QUESTIONS_STATUS, '!=', Constants::QUESTION_STATUS_ANNOUNCEMENT);

            // Merge announcements and all questions
            $announcements = $announcements->merge($questions->get());
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
            if (!$problemID) continue;
            $problemPivot = $contest->problems()->find($problemID)->pivot;
            $problemPivot[Constants::FLD_CONTEST_PROBLEMS_PROBLEM_ORDER] = $i;
            $problemPivot->save();
            $i++;
        }
    }


    /**
     * Get the problems filtered by contest tags and judges
     *
     * @param $request
     * @param $tags
     * @param $judges
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getProblemsWithSessionFilters($request, &$tags, &$judges)
    {
        // Check server sessions for saved filters data (i.e. tags, organisers, judges)
        if (Session::has(Constants::CONTEST_PROBLEMS_SELECTED_FILTERS)) {
            if (isset(Session::get(Constants::CONTEST_PROBLEMS_SELECTED_FILTERS)[Constants::CONTEST_PROBLEMS_SELECTED_JUDGES])) {
                $judges = Session::get(Constants::CONTEST_PROBLEMS_SELECTED_FILTERS)[Constants::CONTEST_PROBLEMS_SELECTED_JUDGES];
            }
            if (isset(Session::get(Constants::CONTEST_PROBLEMS_SELECTED_FILTERS)[Constants::CONTEST_PROBLEMS_SELECTED_TAGS])) {
                $tags = Session::get(Constants::CONTEST_PROBLEMS_SELECTED_FILTERS)[Constants::CONTEST_PROBLEMS_SELECTED_TAGS];
            }
        }

        // Get problems with applied filters
        return ProblemController::getProblemsWithFilters($request, $tags, $judges);
    }
}
