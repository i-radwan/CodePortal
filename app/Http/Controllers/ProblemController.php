<?php

namespace App\Http\Controllers;


use App\Models\Tag;
use App\Models\Judge;
use App\Utilities\Utilities;
use App\Utilities\Constants;


class ProblemController extends Controller
{
    use RetrieveProblems;

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
     * @return array Array of tags name
     */
    public function getTagsFilter()
    {
        if (request()->has(Constants::URL_QUERY_TAG_KEY)) {

            // Get tags names as array
            $tagsNames = explode(",", request()->get(Constants::URL_QUERY_TAG_KEY));
            // Get tags IDs from names
            $tagsIDs = Tag::whereIn(Constants::FLD_TAGS_NAME, $tagsNames)
                ->get()
                ->pluck(Constants::FLD_TAGS_ID)
                ->toArray();
            return $tagsIDs;
        }

        return request()->get(Constants::URL_QUERY_TAG_KEY);
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

}
