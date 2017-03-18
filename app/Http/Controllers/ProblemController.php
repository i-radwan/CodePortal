<?php

namespace App\Http\Controllers;

use App\Models\Problem;
use App\Models\Tag;
use App\Models\Judge;
use Illuminate\Http\Request;
use App\Utilities\Constants;
use Session;

class ProblemController extends Controller
{
    /**
     * Show the problems page.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProblemsPagination($page = 1, $sortby = [])
    {
        $problems = Problem::index($page, $sortby);
        return $problems;
    }

    public function index(Request $request)
    {
        // Get problems sorting mode and check validity
        $sortbyMode = $request->get('order');
        if ($sortbyMode && $sortbyMode != 'asc' && $sortbyMode != 'desc') {
            $sortbyMode = 'desc';
        }
        if ($request->get('sortby')) {

            // ToDo THIS MUST NOT BE HARDCODED LIKE THAT
            if ($request->get('sortby') == "Name")
                $sortByParameter = Constants::FLD_PROBLEMS_NAME;
            else if ($request->get('sortby') == "Difficulty")
                $sortByParameter = Constants::FLD_PROBLEMS_DIFFICULTY;
            else if ($request->get('sortby') == "# Accepted submissions")
                $sortByParameter = Constants::FLD_PROBLEMS_ACCEPTED_SUBMISSIONS_COUNT;
            else if ($request->get('sortby') == "ID")
                $sortByParameter = Constants::FLD_PROBLEMS_ID;
            else if ($request->get('sortby') == "Judge")
                $sortByParameter = Constants::FLD_PROBLEMS_JUDGE_ID;
            else
                $sortByParameter = Constants::FLD_PROBLEMS_NAME;

            $sortby = [[
                "column" => Constants::TBL_PROBLEMS . '.' . $sortByParameter,
                "mode" => $sortbyMode
            ]];
        } else {
            $sortby = [];
        }
        //If search or filters are applied
        if (($request->get('tags') || $request->get('q') || $request->get('judges'))) {
            $data = Problem::filter($request->get('q'), (($request->get('tags')) ? $request->get('tags') : []), (($request->get('judges')) ? $request->get('judges') : []), $request->get('page'), $sortby);
        } //If a single Tag is applied
        else if ($request->get('tag') != "") {
            $data = Tag::getTagProblems($request->get('tag'), $request->get('page'), $sortby);
        } //No Filters are applied
        else {
            $data = $this->getProblemsPagination($request->get('page'), $sortby);
        }
        $data = (json_decode($data));
        $data->tags = json_decode(Tag::index());
        $data->judges = json_decode(Judge::index());
        $data->sortbyMode = $sortbyMode;
        $data->sortbyParam = $request->get('sortby');
        return view('problems.index')->with('data', $data);
    }
}