<?php

namespace App\Http\Controllers\Contest;

use Auth;
use Session;
use App\Models\User;
use App\Models\Tag;
use App\Models\Judge;
use App\Models\Contest;
use App\Models\Group;
use App\Utilities\Constants;
use App\Http\Controllers\RetrievesProblems;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContestController extends Controller
{
    use SavesContests, RetrievesProblems;

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
     * Show single contest problems page
     *
     * Authorization happens in the defined Gate
     *
     * @param Contest $contest
     * @return \Illuminate\View\View
     */
    public function displayContestProblems(Contest $contest)
    {
        if (!$contest) {
            return redirect(route(Constants::ROUTES_CONTESTS_INDEX)); // contest doesn't exist
        }

        $problems = $contest->problemStatistics()->get();

        return view('contests.contest')
            ->with('contest', $contest)
            ->with('problems', $problems)
            ->with('view', 'problems')
            ->with('pageTitle', config('app.name') . ' | ' . $contest[Constants::FLD_CONTESTS_NAME]);
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
        if (!$contest) {
            return redirect(route(Constants::ROUTES_CONTESTS_INDEX)); // contest doesn't exist
        }

        $problems = $contest->problemStatistics()->get();
        $standings = $this->getStandingsInfo($contest);

        return view('contests.contest')
            ->with('contest', $contest)
            ->with('standings', $standings)
            ->with('problems', $problems)
            ->with('view', 'standings')
            ->with('pageTitle', config('app.name') . ' | ' . $contest[Constants::FLD_CONTESTS_NAME]);
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
        if (!$contest) {
            return redirect(route(Constants::ROUTES_CONTESTS_INDEX)); // contest doesn't exist
        }

        $submissions = $contest
            ->submissions()
            ->paginate(Constants::CONTEST_SUBMISSIONS_PER_PAGE);

        return view('contests.contest')
            ->with('contest', $contest)
            ->with('submissions', $submissions)
            ->with('view', 'submissions')
            ->with('pageTitle', config('app.name') . ' | ' . $contest[Constants::FLD_CONTESTS_NAME]);
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
        if (!$contest) {
            return redirect(route(Constants::ROUTES_CONTESTS_INDEX)); // contest doesn't exist
        }

        $participants = $contest
            ->participants()
            ->select(Constants::PARTICIPANTS_DISPLAYED_FIELDS)
            ->paginate(Constants::CONTEST_PARTICIPANTS_PER_PAGE);

        return view('contests.contest')
            ->with('contest', $contest)
            ->with('participants', $participants)
            ->with('view', 'participants')
            ->with('pageTitle', config('app.name') . ' | ' . $contest[Constants::FLD_CONTESTS_NAME]);
    }

    /**
     * Return add contest view
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        return $this->addEditContestView($request, null);
    }

    /**
     * Add contest function
     *
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        return $this->saveContest($request, null, null);
    }

    /**
     * Return edit contest view
     *
     * @param Request $request
     * @param Contest $contest
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Contest $contest)
    {
        return $this->addEditContestView($request, $contest);
    }

    /**
     * Edit contest function
     *
     * @param Request $request
     * @param Contest $contest
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Contest $contest)
    {
        return $this->saveContest($request, $contest->groups()->first(), $contest);
    }

    /**
     * Reorder problems in a contest
     *
     * Authorization happens in the defined Gate (owner-contest)
     *
     * @param Request $request
     * @param Contest $contest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reorderProblems(Request $request, Contest $contest)
    {
        $problemsIDsNewOrder = $request->get('problems_order');
        $this->updateContestProblemsOrder($contest, $problemsIDsNewOrder);
        return response()->json(['status' => 204], 200);
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
        $user->participatingContests()->syncWithoutDetaching([$contest[Constants::FLD_CONTESTS_ID]]);
        return back();
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
     * Remove participant from the contest (by organiser/owner)
     *
     * @param Contest $contest
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeParticipant(Contest $contest, User $user)
    {
        $user->participatingContests()->detach($contest);
        return back();
    }

    /**
     * Delete a certain contest if you're owner
     *
     * Authorization happens in the defined Gate (owner-contest)
     *
     * @param Contest $contest
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Contest $contest)
    {
        $contest->delete();
        return redirect(route(Constants::ROUTES_CONTESTS_INDEX));
    }

    /**
     * Show add/edit contest page
     *
     * @param \Illuminate\Http\Request $request
     * @param Contest $contest
     * @return \Illuminate\View\View
     */
    private function addEditContestView(Request $request, Contest $contest = null)
    {
        // Check server sessions for saved filters data (i.e. tags, organisers, judges)
        $tags = $judges = [];

        $problems = $this->getProblemsWithSessionFilters(
            $request, $tags, $judges,
            Constants::CONTEST_PROBLEMS_SELECTED_FILTERS,
            Constants::CONTEST_PROBLEMS_SELECTED_JUDGES,
            Constants::CONTEST_PROBLEMS_SELECTED_TAGS
        );

        // Are filters applied (to inform user that there're filters applied from previous visit)
        $areFiltersApplied = count($tags) || count($judges);

        $view = view('contests.add_edit')
            ->with('problems', $problems)
            ->with('judges', Judge::all())
            ->with('checkBoxes', 'true')
            ->with('filtersApplied', $areFiltersApplied)
            ->with('formURL', route(Constants::ROUTES_CONTESTS_INDEX))
            ->with('syncFiltersURL', route(Constants::ROUTES_CONTESTS_FILTERS_SYNC))
            ->with('detachFiltersURL', route(Constants::ROUTES_CONTESTS_FILTERS_DETACH))
            ->with(Constants::CONTEST_PROBLEMS_SELECTED_TAGS, $tags)
            ->with(Constants::CONTEST_PROBLEMS_SELECTED_JUDGES, $judges)
            ->with('pageTitle', config('app.name') . ' | ' . ((isset($contest)) ? $contest[Constants::FLD_CONTESTS_NAME] : 'Contest'));

        // When editing
        if ($contest) {
            $view->with('contest', $contest)
                ->with('group', $contest->groups()->first())
                ->with('formURL', route(Constants::ROUTES_CONTESTS_UPDATE, $contest[Constants::FLD_CONTESTS_ID]));
        }
        return $view;
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

        $problems = $this->getProblemsWithSessionFilters(
            $request, $tags, $judges,
            Constants::CONTEST_PROBLEMS_SELECTED_FILTERS,
            Constants::CONTEST_PROBLEMS_SELECTED_JUDGES,
            Constants::CONTEST_PROBLEMS_SELECTED_TAGS
        );

        return view('contests.add_edit')
            ->with('problems', $problems)
            ->with('judges', Judge::all())
            ->with('checkBoxes', 'true')
            ->with('group', $group)
            ->with('formURL', route(Constants::ROUTES_GROUPS_CONTEST_STORE, $group[Constants::FLD_GROUPS_ID]))
            ->with('syncFiltersURL', route(Constants::ROUTES_CONTESTS_FILTERS_SYNC))
            ->with('detachFiltersURL', route(Constants::ROUTES_CONTESTS_FILTERS_DETACH))
            ->with(Constants::CONTEST_PROBLEMS_SELECTED_TAGS, $tags)
            ->with(Constants::CONTEST_PROBLEMS_SELECTED_JUDGES, $judges)
            ->with('pageTitle', config('app.name') . ' | Add Contest');
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
     * Retrieve tags by name for auto complete
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function tagsAutoComplete()
    {
        $tags = Tag::select(Constants::FLD_TAGS_NAME)->get();
        return response()->json($tags);
    }

    /**
     * Retrieve usernames for auto complete
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function usersAutoComplete(Request $request)
    {
        $user = Auth::user();
        $query = $request->get('query');
        $users = User::select([Constants::FLD_USERS_USERNAME . ' as name'])
            ->where(Constants::FLD_USERS_USERNAME, 'LIKE', "%$query%")
            ->where(Constants::FLD_USERS_USERNAME, '!=', $user[Constants::FLD_USERS_USERNAME])
            ->get();

        return response()->json($users);
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
     * Get contest standings data
     *
     * TODO: to be completely edited when fixing standings query
     *
     * @param Contest $contest
     * @return array
     */
    private function getStandingsInfo($contest)
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

        return $standings;
    }
}
