<?php

namespace App\Http\Controllers;

use App\Models\Problem;
use App\Models\Tag;
use App\Utilities\Constants;
use Session;

trait RetrieveProblems
{
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
    public function filterProblems($name = null, $judgesIDs = null, $tagsIDs = null, $sortParams = null)
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
     * Get the problems with the applied filters
     * @param $request
     * @param $tagsNames array of tagsNames
     * @param $judgesIDs array of JudgesIds
     * @param array $sortParam sort By Parameters
     * @return Collection
     */
    public function prepareProblemsFiltersForQuery($request, $tagsNames, $judgesIDs, $sortParam = [])
    {
        //ToDO (Samir) improve the function "do something towards sortParams
        $searchStr = "";
        $tagsIDs = (count(Tag::whereIn(Constants::FLD_TAGS_NAME, $tagsNames)->get()) == 0) ? null : Tag::whereIn(Constants::FLD_TAGS_NAME, $tagsNames)->get()->pluck(Constants::FLD_TAGS_ID)->toArray();
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
        $problems = $this->filterProblems($searchStr, $judgesIDs, $tagsIDs, $sortParams);
        return $problems;
    }


    /**
     * Get the problems filtered by sheet tags and judges
     *
     * @param $request
     * @param $tags
     * @param $judges
     * @param $filtersSessionKey
     * @param $judgesSessionKey
     * @param $tagsSessionKey
     * @return Collection
     */
    public function getProblemsWithSessionFilters($request, &$tags, &$judges, $filtersSessionKey, $judgesSessionKey, $tagsSessionKey)
    {
        // Check server sessions for saved filters data (i.e. tags, organisers, judges)
        if (Session::has($filtersSessionKey)) {
            if (isset(Session::get($filtersSessionKey)[$judgesSessionKey])) {
                $judges = Session::get($filtersSessionKey)[$judgesSessionKey];
            }
            if (isset(Session::get($filtersSessionKey)[$tagsSessionKey])) {
                $tags = Session::get($filtersSessionKey)[$tagsSessionKey];
            }
        }

        $tagsNames = $judgesIDs = [];
        // Get arrays of tags names and judges IDs to fetch problems with
        // these filters applied
        if (count($tags) > 0)
            $tagsNames = explode(",", $tags);
        if (count($judges) > 0)
            $judgesIDs = explode(",", $judges);

        // Get problems with applied filters
        return $this->prepareProblemsFiltersForQuery($request, $tagsNames, $judgesIDs);
    }

}
