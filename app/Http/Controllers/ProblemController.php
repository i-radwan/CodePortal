<?php

namespace App\Http\Controllers;

use App\Models\Problem;
use App\Models\Tag;
use App\Models\Judge;
use App\Utilities\Utilities;
use App\Utilities\Constants;
use Illuminate\Database\Eloquent\Collection;

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
        $sortParams = $this->getSortFilter();
        $problems = $this->filterProblems($searchStr, $judgesIDs, $tagsIDs, $sortParams);

        return view('problems.index')
            ->with('problems', $problems)
            ->with('tags', Tag::all())
            ->with('judges', Judge::all())
            ->with('pageTitle', config('app.name') . ' | Problems');
    }

    /**
     * Parse search string from the url query and return it as safe string for SQL queries
     *
     * @return string
     */
    public function getSearchStringFilter()
    {
        return Utilities::makeInputSafe(request()->get(Constants::URL_QUERY_SEARCH_KEY));
    }

    /**
     * Return parsed judge filters from the url query
     *
     * @return array Array of judges ids
     */
    public function getJudgesFilter()
    {
        return request()->get(Constants::URL_QUERY_JUDGES_KEY);
    }

    /**
     * Return parsed tag filters from the url query
     *
     * @return array Array of tags ids
     */
    public function getTagsFilter()
    {
        if (request()->has(Constants::URL_QUERY_TAG_KEY)) {
            return [request()->get(Constants::URL_QUERY_TAG_KEY)];
        }

        return request()->get(Constants::URL_QUERY_TAGS_KEY);
    }

    /**
     * Return parsed sort filters from the url query
     *
     * @return array Array of sorting parameters where the array key represents the column to sort by
     * and the value represents the sorting mode (asc, desc)
     */
    public function getSortFilter()
    {
        $sortParam = request()->get(Constants::URL_QUERY_SORT_PARAM_KEY);

        if ($sortParam && !array_key_exists($sortParam, Constants::PROBLEMS_SORT_PARAMS)) {
            $sortParam = Constants::URL_QUERY_SORT_PARAM_ID_KEY;
        }

        $sortOrder = request()->get(Constants::URL_QUERY_SORT_ORDER_KEY);

        if ($sortOrder && $sortOrder != 'asc' && $sortOrder != 'desc') {
            $sortOrder = 'asc';
        }

        return [Constants::PROBLEMS_SORT_PARAMS[$sortParam] => $sortOrder];
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

    /**
     * This Function gets the problems to the ContestController with the applied filters
     * @param $request
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getProblemsToContestController($request, $tagsNames, $judgesIDs, $sortParam = []){
        //ToDO (Samir) improve the function "do something towards sortParams
        $searchStr = "";
        $tagsIDs = (count(Tag::whereIn('name', $tagsNames)->get()) == 0) ? null : Tag::whereIn('name', $tagsNames)->get();
        $judgesIDs = count($judgesIDs) == 0 ? null : $judgesIDs;
        $sortParam = $request->get(Constants::URL_QUERY_SORT_PARAM_KEY);
        if ($sortParam && !array_key_exists($sortParam, Constants::PROBLEMS_SORT_PARAMS)) {
            $sortParam = Constants::URL_QUERY_SORT_PARAM_ID_KEY;
        }
        $sortOrder = $request->get(Constants::URL_QUERY_SORT_ORDER_KEY);
        if ($sortOrder && $sortOrder != 'asc' && $sortOrder != 'desc') {
            $sortOrder = 'asc';
        }
        $sortParams = [Constants::PROBLEMS_SORT_PARAMS[$sortParam] => $sortOrder];
        $problems = self::filterProblems($searchStr, $judgesIDs, $tagsIDs, $sortParams);
        return $problems;
    }
}
