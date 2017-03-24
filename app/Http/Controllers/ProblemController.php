<?php

namespace App\Http\Controllers;

use App\Models\Problem;
use App\Models\Tag;
use App\Models\Judge;
use Illuminate\Http\Request;
use App\Utilities\Constants;

class ProblemController extends Controller
{
    /**
     * @param int $page problems table page
     * @param array $sortBy sort by parameter
     * @return string
     */
    public function getProblemsWithPagination($page = 1, $sortBy = [])
    {
        $problems = Problem::getAllProblems($page, $sortBy);
        return $problems;
    }

    /**
     * @param $data the problems response
     * @param $startPage The start page to be calculated in the pagination bar
     * @param $endPage  The end page to be calculated in the pagination bar
     * @param $currentPage The current page in the request
     * @param $lastPage The last page of the problems list in the request
     */
    public function getPaginationLimits(&$data, &$startPage, &$endPage, $currentPage, $lastPage){
        if ($currentPage < 7) {
            $endPage = 13;
            $startPage = 1;
        } else {
            $startPage = $currentPage - 6;
            $endPage = $currentPage + 6;
        }
        $endPage = ($endPage > $lastPage) ? $lastPage : $endPage;
        $data->initialPage = $startPage;
        $data->pagesLimit = $endPage;
    }

    /**
     * @param $sortByParameter The SortBy parameter in the request body
     * @param $sortByMode The SortBy mode (asc or dsc) in the request body
     * @param $sortBy The array to be returned after the end of the function having the right format
     */
    public function applySortByParameter($sortByParameter, &$sortByMode,  &$sortBy){
        //Validation of SortByMode if it's not assigned
        if ($sortByMode && $sortByMode != 'asc' && $sortByMode != 'desc') $sortByMode = 'desc';
        if ($sortByParameter) {
            if (Constants::PROBLEMS_SORT_BY[$sortByParameter])
                $sortByType = Constants::PROBLEMS_SORT_BY[$sortByParameter];
            else
                $sortByType = Constants::PROBLEMS_SORT_BY['Name'];
            $sortBy = [[ "column" => Constants::TBL_PROBLEMS . '.' . $sortByType, "mode" => $sortByMode ]];
        } else $sortBy = [];
    }

    /**
     * @param $tag single selected tag
     * @param $q the problem search string
     * @param $tags the checked tags
     * @param $judges the checked judges
     * @param $page the selected page
     * @param $sortBy the sort by parameter
     * @return string
     */
    public function applyFilters($tag, $q, $tags, $judges, $page, $sortBy){
        //If tags are checked / judges are checked / problemSearch is found
        if (($tags || $q || $judges))
            $data = Problem::filter($q, $tags, $judges, $page, $sortBy);
        //If a single Tag is applied
        else if ($tag != "")
            $data = Tag::getTagProblems($tag, $page, $sortBy);
        //Nothing is applied
        else
            $data = $this->getProblemsWithPagination($page, $sortBy);
        return $data;
    }

    /**
     * @param $data
     */
    public function supplyTagsAndJudges(&$data){
        //Add All Tags
        $data->tags = json_decode(Tag::all());
        //Add All Judges
        $data->judges = json_decode(Judge::all());
    }

    /**
     * @param $data the problems data
     * @param $sortByMode sort Mode (asc/ dsc)
     * @param $sortByParameter sort by what
     * @param $setJudges the current checked judges
     * @param $setProblemSearchString
     * @param $setTags the current checked tags
     */
    public function supplyMetaData(&$data, $sortByMode, $sortByParameter, $setJudges, $setProblemSearchString, $setTags){
        //Add SortByMode
        $data->sortbyMode = $sortByMode;
        $data->sortbyParam = $sortByParameter;
        // Set pagination limits
        $this->getPaginationLimits($data, $startPage, $endPage ,$data->problems->current_page ,$data->problems->last_page);
        // Send query filters data to view (to maintain selected filters status as selected)
        $data->judgesIDs = ($setJudges) ? $setJudges : [];
        $data->tagsIDs = ($setTags) ? $setTags : [];
        $data->q = $setProblemSearchString;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return $this
     */
    public function index(Request $request)
    {
        //Get SortBy Parameters
        $sortByMode = $request->get('order');
        $sortByParameter = $request->get('sortby');
        //Add Sort
        $this->applySortByParameter($sortByParameter, $sortByMode, $sortBy);
        //Get problems Data
        $data = json_decode($this->applyFilters($request->get('tag'), $request->get('q'), $request->get('tags'), $request->get('judges'),$request->get('page'), $sortBy));
        //Supply Tags and Judges
        $this->supplyTagsAndJudges($data);
        //Supply MetaData
        $this->supplyMetaData($data, $sortByMode, $sortByParameter, $request->get('judges'), $request->get('q'), $request->get('tags'));
        //Return result
        return view('problems.index')->with('data', $data);
    }
}
