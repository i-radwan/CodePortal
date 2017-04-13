<?php

namespace App\Http\Controllers;

use App\Models\Problem;
use App\Models\Tag;
use App\Models\Judge;
use App\Utilities\Utilities;
use App\Utilities\Constants;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class ProblemController extends Controller
{
    //Constants
    const URL_QUERY_NAME_KEY = 'q';
    const URL_QUERY_TAG_KEY = 'tag';
    const URL_QUERY_JUDGE_KEY = 'judge'; //To see how it's gonna be modified
    const JUDGES_ID = [
        "codeforces" => Constants::JUDGE_CODEFORCES_ID
    ];

    /**
     * Show the problems page.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $searchStr = self::getSearchStringFilter();
        $judgesIDs = self::getJudgesFilter();
        $tagsIDs = self::getTagsFilter();
        $sortBy = self::getSortByFilter();
        $problems = self::filterProblems($searchStr, $judgesIDs, $tagsIDs, $sortBy);

        return view('problems.index')
            ->with('problems', $problems)
            ->with('tags', Tag::all())
            ->with('judges', Judge::all())
            ->with('pageTitle', config('app.name') . ' | Problems');
    }

    public function getSearchStringFilter()
    {
        return Utilities::makeInputSafe(request()->get(Constants::APPLIED_FILTERS_SEARCH_STRING));
    }

    public function getJudgesFilter()
    {
        return request()->get(Constants::APPLIED_FILTERS_JUDGES_IDS);
    }

    public function getTagsFilter()
    {
        if (request()->has(Constants::APPLIED_FILTERS_TAG_ID)) {
            return [request()->get(Constants::APPLIED_FILTERS_TAG_ID)];
        }

        return request()->get(Constants::APPLIED_FILTERS_TAGS_IDS);
    }

    public function getSortByFilter()
    {
        $sortByMode = request()->get(Constants::APPLIED_FILTERS_SORT_BY_MODE);

        if ($sortByMode && $sortByMode != 'asc' && $sortByMode != 'desc') {
            $sortByMode = 'desc';
        }

        $sortByParameter = request()->get(Constants::APPLIED_FILTERS_SORT_BY_PARAMETER, '');

        return [Constants::PROBLEMS_SORT_BY[$sortByParameter] => $sortByMode];
    }

    /**
     * Return paginated filtered problems according to the given filters.
     * To skip a filter just pass null to the corresponding filter in the parameter list.
     *
     * @param string|null $name
     * @param array|null $judgesIDs
     * @param array|null $tagsIDs
     * @param array|null $sortBy
     * @return Collection list of problem models
     */
    public static function filterProblems($name = null, $judgesIDs = null, $tagsIDs = null, $sortBy = null)
    {
        // Filter the problems
        $problems = Problem::ofName($name)->ofJudges($judgesIDs)->hasTags($tagsIDs);

        // Sort the problems
        if ($sortBy != null) {
            foreach ($sortBy as $column => $mode) {
                if ($column != "" && $mode != "") {
                    $problems->orderBy($column, $mode);
                }
            }
        }

        // Execute the problems paginated query
        return $problems->paginate(Constants::PROBLEMS_COUNT_PER_PAGE);
    }
}
