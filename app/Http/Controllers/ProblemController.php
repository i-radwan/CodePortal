<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\User;
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
            $sortBy = [ Constants::TBL_PROBLEMS . '.' . $sortByType => $sortByMode ];
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
        // Set page
        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });
        //ToDO: Fix the string to array comversion
        //ToDo: Fix the Paginator
        //If tags are checked / judges are checked / problemSearch is found
        if (($tags || $q || $judges)){
            $data = self::prepareProblemsTableData(self::filterProblems($q,$judges,$tags,$sortBy));
//            $data = Problem::filter($q, $tags, $judges, $page, $sortBy);
        }
        //If a single Tag is applied
        else if ($tag != ""){
            $data = self::prepareProblemsTableData(self::prepareProblemsTableData(null, null, $tag, $sortBy));
//          $data = Tag::getTagProblems($tag, $page, $sortBy);
        }
        //Nothing is applied
        else {
            $data = self::prepareProblemsTableData(self::prepareProblemsTableData(null, null, $tag, $sortBy));
//            $data = $this->getProblemsWithPagination($page, $sortBy);
        }
        return $data;
    }

    /**
     * @param $data
     */
    public function supplyTagsAndJudges(&$data){
        //Add All Tags
        $data->tags = json_encode(Tag::all());
        //Add All Judges
        $data->judges = json_decode(Judge::all());
    }

    /**
     * @param $sortByMode sort Mode (asc/ dsc)
     * @param $sortByParameter sort by what
     * @param $setJudges the current checked judges
     * @param $setProblemSearchString
     * @param $setTags the current checked tags
     */
    public function getMetaData( $sortByMode, $sortByParameter, $setJudges, $setProblemSearchString, $setTags){
        //Add SortByMode
        $data->sortbyMode = $sortByMode;
        $data->sortbyParam = $sortByParameter;
        // Set pagination limits
//        $this->getPaginationLimits($data, $startPage, $endPage ,$data->rows->current_page ,$data->rows->last_page);
        // Send query filters data to view (to maintain selected filters status as selected)
//        $data->judgesIDs = ($setJudges) ? $setJudges : [];
//        $data->tagsIDs = ($setTags) ? $setTags : [];
//        $data->q = $setProblemSearchString;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        //Get SortBy Parameters
        $sortByMode = $request->get('order');
        $sortByParameter = $request->get('sortby');
        $sortBy = [$sortByMode => $sortByParameter ];

        $data = self::prepareProblemsTableData(self::filterProblems($request->get(self::URL_QUERY_NAME_KEY), null, null, $sortBy) ); //this will get headings and data without pagination limits

        //Add Sort //See What it does Now I've Forgotten
//        $this->applySortByParameter($sortByParameter, $sortByMode, $sortBy);

        //Get problems Data
//        $data = json_decode($this->applyFilters($request->get('tag'), $request->get('q'), $request->get('tags'), $request->get('judges'),$request->get('page'), $sortBy));

        //Supply Tags and Judges
//        $this->supplyTagsAndJudges($data);

        //Supply MetaData
//        $this->supplyMetaData($data, $sortByMode, $sortByParameter, $request->get('judges'), $request->get('q'), $request->get('tags'));

        //Return result
        return view('problems.index')->with('data', $data)->with('pageTitle', config('app.name'). ' | Problems');
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
        if ($sortBy != null ) {
            foreach ($sortBy as $column => $mode) {
                if( $column != "" and $mode != "") {
                    $problems->orderBy($column, $mode);
                }
            }
        }
        // Execute the problems paginated query
        return  $problems->paginate(Constants::PROBLEMS_COUNT_PER_PAGE);
    }

    /**
     * Prepare problems table output data in table protocol format
     *
     * @param Collection $problems
     * @return array the formatted table data
     */
    public static function prepareProblemsTableData($problems)
    {
        // Get the currently logged in user
        $user = Auth::user();

        $rows = [];
        //Get Paginator Data
        $paginatorData = Utilities::getPaginatorData($problems);

        // Prepare problems data for table according to the table protocol
        foreach ($problems as $problem) {
            $rows[] = [
                Constants::TABLE_DATA_KEY => self::getProblemRowData($problem),
                Constants::TABLE_META_DATA_KEY => self::getProblemRowMetaData($problem, $user)
            ];
        }

        // Return problems table data: headings & rows
        return [
            Constants::TABLE_HEADINGS_KEY => Constants::PROBLEMS_TABLE_HEADINGS,
            Constants::TABLE_ROWS_KEY => $rows,
            Constants::TABLE_PAGINATION_KEY => $paginatorData,
        ];
    }

    /**
     * Return table row data of the given problem in table protocol format
     *
     * @param Problem $problem
     * @return array the formatted row data
     */
    public static function getProblemRowData($problem)
    {
        // Note that they should be in the same order of the headings
        return [
            [   // ID
                Constants::TABLE_DATA_KEY => Utilities::generateProblemNumber($problem)
            ],
            [   // Name
                Constants::TABLE_DATA_KEY => $problem->name,
                Constants::TABLE_EXTERNAL_LINK_KEY => Utilities::generateProblemLink($problem)
            ],
            [   // # Accepted
                Constants::TABLE_DATA_KEY => $problem->solved_count,
            ],
            [   // Judge
                Constants::TABLE_DATA_KEY => Constants::JUDGES[$problem->judge_id][Constants::JUDGE_NAME_KEY],
            ],
            [   // Tags
                Constants::TABLE_DATA_KEY => self::getProblemTagsRowData($problem),
            ]
        ];
    }

    /**
     * Return the tags of the given problem in table protocol format
     *
     * @param Problem $problem
     * @return array the formatted row data
     */
    public static function getProblemTagsRowData($problem)
    {
        $tags = $problem->tags()->get();

        $ret = [];

        foreach ($tags as $tag) {
            $ret[] = [
                Constants::TABLE_DATA_KEY => $tag->name,
                Constants::TABLE_LINK_KEY =>  Utilities::getURL(Constants::PROBLEMS_TABLE_HEADINGS[4][Constants::TABLE_DATA_KEY],$tag->id,"/problems","")       // TODO: Make it More Generic
            ];
        }
        return $ret;
    }

    /**
     * Return table row meta-data of the given problem in table protocol format
     *
     * @param Problem $problem
     * @param User $user
     * @return array the formatted row meta-data
     */
    public static function getProblemRowMetaData($problem, $user)
    {
        return [
            Constants::TABLE_ROW_STATE_KEY => $problem->simpleVerdict($user),
            Constants::TABLE_ROW_CHECKBOX_KEY => false,
            Constants::TABLE_ROW_DISABLED_KEY => false
        ];
    }

}
