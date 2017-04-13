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
    /**
     * Show the problems page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $searchStr = $this->getSearchStringFilter();
        $judgesIDs = $this->getJudgesFilter();
        $tagsIDs = $this->getTagsFilter();
        $sortParams = $this->getSortByFilter();
        $problems = $this->filterProblems($searchStr, $judgesIDs, $tagsIDs, $sortParams);

        return view('problems.index')
            ->with('problems', $problems)
            ->with('tags', Tag::all())
            ->with('judges', Judge::all())
            ->with('pageTitle', config('app.name') . ' | Problems');
    }

    /**
     * Parse search string from the url query and return it as safe string
     *
     * @return String
     */
    public function getSearchStringFilter()
    {
        return Utilities::makeInputSafe(request()->get(Constants::URL_QUERY_SEARCH_KEY));
    }

    public function getJudgesFilter()
    {
        return request()->get(Constants::URL_QUERY_JUDGES_KEY);
    }

    public function getTagsFilter()
    {
        if (request()->has(Constants::URL_QUERY_TAG_KEY)) {
            return [request()->get(Constants::URL_QUERY_TAG_KEY)];
        }

        return request()->get(Constants::URL_QUERY_TAGS_KEY);
    }

    public function getSortByFilter()
    {
        $sortByMode = request()->get(Constants::URL_QUERY_SORT_ORDER_KEY);

        if ($sortByMode && $sortByMode != 'asc' && $sortByMode != 'desc') {
            $sortByMode = 'desc';
        }

        $sortByParameter = request()->get(Constants::URL_QUERY_SORT_PARAM_KEY, '');

        return [Constants::PROBLEMS_SORT_PARAMS[$sortByParameter] => $sortByMode];
    }

    /**
     * Return paginated filtered problems according to the given filters.
     * To skip a filter just pass null to the corresponding filter in the parameter list.
     *
     * @param string|null $name
     * @param array|null $judgesIDs
     * @param array|null $tagsIDs
     * @param array|null $sortParams
     * @return Collection list of problem models
     */
    public static function filterProblems($name = null, $judgesIDs = null, $tagsIDs = null, $sortParams = null)
    {
        // Filter the problems
        $problems = Problem::ofName($name)->ofJudges($judgesIDs)->hasTags($tagsIDs);

        // Sort the problems
        if ($sortParams != null) {
            foreach ($sortParams as $column => $mode) {
                if ($column != "" && $mode != "") {
                    $problems->orderBy($column, $mode);
                }
            }
        }

        // Execute the problems paginated query
        return $problems->paginate(Constants::PROBLEMS_COUNT_PER_PAGE);
    }
}
