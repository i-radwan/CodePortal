<?php

namespace App\Http\Controllers;

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
use App\Http\Controllers\ProblemController;

class ContestController extends Controller
{
    /**
     * Show all contests page
     *
     * @return \Illuminate\View\View $this
     */
    public function index()
    {
        //ForgetCheckedProblems
        //Forget roblemsFilters
        Session::forget(Constants::CHECKED_PROBLEMS);
        Session::forget(Constants::CONTESTS_PROBLEMS_FILTERS);
        Session::forget(Constants::CONTESTS_MENTIONED_ORGANISERS);

        $data = [];

        $data[Constants::CONTESTS_CONTESTS_KEY] =
            Contest::ofPublic()->paginate(Constants::CONTESTS_COUNT_PER_PAGE);

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
     * @return \Illuminate\View\View $this
     */
    public function displayContest(Contest $contest)
    {
        $currentUser = Auth::user();

        if (!$contest) return redirect('contests/');

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
     * @param \Illuminate\Http\Request $request
     *
     * @return $this
     */
    public function addEditContestView(Request $request)
    {
        if(Session::has(Constants::CONTESTS_MENTIONED_ORGANISERS))
            $mOrganisers = Session::get(Constants::CONTESTS_MENTIONED_ORGANISERS);
        else $mOrganisers = [];
        if(Session::has(Constants::CONTESTS_PROBLEMS_FILTERS)){
            $cjudges = isset(Session::get(Constants::CONTESTS_PROBLEMS_FILTERS)['cJudges']) ? Session::get(Constants::CONTESTS_PROBLEMS_FILTERS)['cJudges'] : [];
            $ctags = isset(Session::get(Constants::CONTESTS_PROBLEMS_FILTERS)['cTags']) ? Session::get(Constants::CONTESTS_PROBLEMS_FILTERS)['cTags']: [];
        }
        else{
            $cjudges = [];
            $ctags = [];
        }
        $problems = self::getProblemsWithFilters($request, $ctags, $cjudges);
        return view('contests.add_edit')
            ->with('problems', $problems)
            ->with('judges', Judge::all())
            ->with('checkBoxes', 'true')
            ->with(Constants::CHECKED_PROBLEMS,Session::get(Constants::CHECKED_PROBLEMS))
            ->with(Constants::CONTESTS_CHECKED_TAGS, $ctags)
            ->with(Constants::CONTESTS_CHECKED_JUDGES, $cjudges)
            ->with(Constants::CONTESTS_MENTIONED_ORGANISERS, $mOrganisers)
            ->with('pageTitle', config('app.name') . ' | Contest');
    }

    /**
     * Add new contest to database
     *
     * @param Request $request
     */
    public function addContest(Request $request)
    {
        dd($request->all());
        $contest = new Contest($request->all());
        $contest->save();
    }

    /**
     * Update contest in database
     *
     * @param Request $request
     */
    public function editContest(Request $request)
    {

    }

    public function tagsAutoComplete(Request $request){
        $data  = Tag::select('name')->get();
        return response()->json($data);

    }

    public function organisersAutoComplete(Request $request){
        $data = User::select([Constants::FLD_USERS_USERNAME.' as name'])->get([]);
        return response()->json($data);
    }

    public function applyProblemsCheckBoxes(Request $request){

        if(Session::has(Constants::CHECKED_PROBLEMS))
            $currentInSession = Session::get(Constants::CHECKED_PROBLEMS);
        else
            $currentInSession = [];
        $j = count($currentInSession);
        $checkedRows = $request->get('checkedRows');
        $checkedStates = $request->get('checkedStates');
        for($i = 0; $i < count($checkedRows); $i = $i + 1){
            $isFound = in_array($checkedRows[$i], $currentInSession);
            if($checkedStates[$i]){//If it's checked
                if(!($isFound)){
                    $currentInSession[$j] = $checkedRows[$i];
                    $j = $j +1;
                }
            }
            else{ //Check if it's in the current session
                if($isFound){ //Remove
                    $currentInSession = array_diff($currentInSession, [$checkedRows[$i]]);
                }
            }
        }
        Session::put(Constants::CHECKED_PROBLEMS, $currentInSession);
        return $currentInSession ;
    }

    public function applyProblemsFilters(Request $request){
        Session::put(Constants::CONTESTS_PROBLEMS_FILTERS, $request->get(Constants::CONTESTS_PROBLEMS_FILTERS));
        return ;

    }

    public function applyOrganisers(Request $request){
        Session::put(Constants::CONTESTS_MENTIONED_ORGANISERS, $request->get(Constants::CONTESTS_MENTIONED_ORGANISERS));
        return ;
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
        if (Auth::check() && $contest->owner->id == Auth::user()->id) {
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
     * Ask question related to the contest problems
     *
     * ToDo add problem id after @Wael gets problems
     *
     * @param Request $request
     * @param int $contestID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addQuestion(Request $request, $contestID)
    {
        $user = Auth::user();
        $problem = 1; // ToDo get problem from request

        // Check if user is a participant
        $contest = $user->participatingContests()->find($contestID);

        // Check if contest exists (user participating in it) and the contest is running now
        if ($contest && $contest->isRunning()) {
            // TODO: I think static function is better than the constructor
            new Question($request->all(), $user, $contest, $problem);
            return back();
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
            if (\Gate::allows('owner-organizer-contest', $question->contest_id)) {
                $question->status = Constants::QUESTION_STATUS[Constants::QUESTION_STATUS_ANNOUNCEMENT_KEY];
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
            if (\Gate::allows('owner-organizer-contest', $question->contest_id)) {
                $question->status = Constants::QUESTION_STATUS[Constants::QUESTION_STATUS_NORMAL_KEY];
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
            $isUserOwner = ($contest->owner->id == $user->id);

            // Check if the user has joined this contest
            $isUserParticipating = ($contest->participants()->find($user->id) != null);
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
        $contestInfo[Constants::SINGLE_CONTEST_ID_KEY] = $contest->id;

        // Get contest name
        $contestInfo[Constants::SINGLE_CONTEST_NAME_KEY] = $contest->name;

        // Get owner name
        $contestInfo[Constants::SINGLE_CONTEST_OWNER_KEY] = $contest->owner->username;

        // Get organizers array
        $contestInfo[Constants::SINGLE_CONTEST_ORGANIZERS_KEY] =
            $contest->organizers()->pluck(Constants::FLD_USERS_USERNAME);

        // Get duration in hrs:mins format
        $contestInfo[Constants::SINGLE_CONTEST_DURATION_KEY] =
            Utilities::convertMinsToHoursMins($contest->duration);

        // Get time and convert to familiar format
        $contestInfo[Constants::SINGLE_CONTEST_TIME_KEY] =
            date('D M d, H:i', strtotime($contest->time));

        // Get contest running status
        $data[Constants::SINGLE_CONTEST_EXTRA_KEY][Constants::SINGLE_CONTEST_RUNNING_STATUS]
            = $contest->isRunning();

        // Is user an organizer?
        $data[Constants::SINGLE_CONTEST_EXTRA_KEY][Constants::SINGLE_CONTEST_IS_USER_AN_ORGANIZER]
            = Auth::check() ? (Auth::user()->organizingContests()->find($contest->id) != null) : false;

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
        //TODO: paginate problems
        $problems = $contest->problems()->get();

        // Set contest problems
        $data[Constants::SINGLE_CONTEST_PROBLEMS_KEY] = $problems;
    }

    /**
     * Get contest standings data
     *
     * @param Contest $contest
     * @param array $data
     */
    private function getStandingsInfo($contest, &$data)
    {
        //TODO: paginate standings
//        \DB::enableQueryLog();
        $rawData = $contest->standings()->get();
//        dd(\DB::getQueryLog());

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
            } while($i < $len && $cur[Constants::FLD_SUBMISSIONS_USER_ID] == $curData[Constants::FLD_SUBMISSIONS_USER_ID]);

            --$i;
            ++$idx;
        }

//        dd($standings);

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
        //TODO: paginate submissions
//        \DB::enableQueryLog();
        $submissions = $contest->submissions()->get();
//        dd(\DB::getQueryLog());

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
            ->get();

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


    public static function getProblemsWithFilters($request,$tagNames, $judgesIDs){
        return ProblemController::getProblemsToContestController($request, $tagNames, $judgesIDs); //Returning the Problems Data
    }

}
