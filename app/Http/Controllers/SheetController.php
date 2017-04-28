<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Utilities\Constants;
use App\Models\Group;
use App\Models\Sheet;
use App\Models\Judge;
use Redirect;
use URL;
use Auth;
use Session;
use Storage;

class SheetController extends Controller
{
    /**
     * Show single sheet page which contains problems
     *
     * @param Sheet $sheet
     * @return \Illuminate\View\View $this
     */
    public function displaySheet(Sheet $sheet)
    {
        $this->getProblemsInfo($sheet, $problems);

        return view('groups.sheet_views.sheet')
            ->with('problems', $problems)
            ->with('sheet', $sheet)
            ->with('pageTitle', config('app.name') . ' | ' . $sheet[Constants::FLD_SHEETS_NAME]);
    }

    /**
     * Show add sheet page
     *
     * @param Group $group
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addSheetView(Request $request, Group $group)
    {

        // Check server sessions for saved filters data (i.e. tags, organisers, judges)
        $tags = $judges = [];

        $problems = self::getProblemsWithSessionFilters($request, $tags, $judges);

        return view('groups.sheet_views.add_edit')
            ->with('problems', $problems)
            ->with('judges', Judge::all())
            ->with('checkBoxes', 'true')
            ->with(Constants::SHEET_PROBLEMS_SELECTED_TAGS, $tags)
            ->with(Constants::SHEET_PROBLEMS_SELECTED_JUDGES, $judges)
            ->with('syncFiltersURL', url('sheets/create/sheet_tags_judges_filters_sync'))
            ->with('detachFiltersURL', url('sheets/create/sheet_tags_judges_filters_detach'))
            ->with('action', 'Add')
            ->with('url', 'groups/' . $group[Constants::FLD_GROUPS_ID] . '/sheets/store')
            ->with('pageTitle', config('app.name') . ' | Sheet');
    }

    /**
     * Show edit sheet page
     *
     * @param Request $request
     * @param Sheet $sheet
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editSheetView(Request $request, Sheet $sheet)
    {
        // Check server sessions for saved filters data (i.e. tags, organisers, judges)
        $tags = $judges = [];

        $problems = self::getProblemsWithSessionFilters($request, $tags, $judges);

        // Show edit sheet view with sheet info attached
        return view('groups.sheet_views.add_edit')
            ->with('sheet', $sheet)
            ->with('sheetName', $sheet[Constants::FLD_SHEETS_NAME])
            ->with('problems', $problems)
            ->with('judges', Judge::all())
            ->with('checkBoxes', 'true')
            ->with(Constants::SHEET_PROBLEMS_SELECTED_TAGS, $tags)
            ->with(Constants::SHEET_PROBLEMS_SELECTED_JUDGES, $judges)
            ->with('syncFiltersURL', url('sheets/create/sheet_tags_judges_filters_sync'))
            ->with('detachFiltersURL', url('sheets/create/sheet_tags_judges_filters_detach'))
            ->with('action', 'Edit')
            ->with('url', 'sheets/' . $sheet[Constants::FLD_SHEETS_ID] . '/edit')
            ->with('pageTitle', config('app.name') . ' | ' . $sheet[Constants::FLD_SHEETS_NAME]);
    }


    /**
     * Add new sheet to database
     *
     * @param Request $request
     * @param Group $group
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function addSheet(Request $request, Group $group)
    {
        $sheet = new Sheet($request->all());

        // Set group id
        $sheet[Constants::FLD_SHEETS_GROUP_ID] = $group[Constants::FLD_GROUPS_ID];

        // Save sheet
        $sheet->save();

        // Fetch problems and sync with sheet problems
        $problemsIDs = explode(",", $request->get('problems'));
        $sheet->problems()->sync($problemsIDs);

        // Flush sessions
        Session::forget([Constants::SHEET_PROBLEMS_SELECTED_FILTERS]);

        // Return to sheets
        return redirect('groups/' . $group[Constants::FLD_GROUPS_ID] . '#sheets');
    }

    /**
     * Update sheet in database
     *
     * @param Request $request
     * @param Sheet $sheet
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function editSheet(Request $request, Sheet $sheet)
    {
        $sheet[Constants::FLD_SHEETS_NAME] = $request->get('name');
        // Save sheet
        $sheet->save();

        // Fetch problems and sync problems
        $problemsIDs = explode(",", $request->get('problems'));
        $sheet->problems()->sync($problemsIDs);

        // Return to sheets
        return redirect('sheets/' . $sheet[Constants::FLD_SHEETS_ID]);
    }


    /**
     * Delete a certain sheet if you're its group owner
     *
     * @param Sheet $sheet
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function deleteSheet(Sheet $sheet)
    {
        $sheet->delete();
        return Redirect::to(URL::previous() . "#sheets");
    }


    /**
     * Save problem solution (provided by group owner)
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveProblemSolution(Request $request)
    {
        $problemID = $request->get('problem_id');
        $sheetID = $request->get('sheet_id');
        $solution = $request->get('problem_solution');
        $solution_lang = $request->get('solution_lang');

        // Check if solution is empty
        if (!strlen(trim($solution))) return back()->withErrors(['You cannot provide empty solution!']);

        // Get sheet model
        $sheet = Sheet::find($sheetID);

        // Check if group owner
        if (\Gate::allows("owner-group", [$sheet])) {

            // Get problem model with pivot to add solution
            $problem = $sheet->problems()->find($problemID);

            // Check if problem exists then update solution
            if ($problem) {
                // Check if solution file already created
                if (!$problem->pivot->solution) {
                    $problem->pivot->solution = uniqid();
                }

                // Save the solution language to DB
                $problem->pivot->solution_lang = $solution_lang;
                $problem->pivot->save();

                // Write the code file of this solution to storage
                Storage::disk('code')->put($problem->pivot->solution, $solution);

            }
        }
        return back();
    }

    public function retrieveProblemSolution(Sheet $sheet, $problemID)
    {
        // Get solution file name
        $solutionFile = $sheet->problems()->find($problemID)->pivot->solution;

        // Read and return file contents
        return Storage::disk('code')->get($solutionFile);
    }

    /**
     * Get sheets problems
     *
     * @param Sheet $sheet
     * @param $problems
     */
    private function getProblemsInfo(Sheet $sheet, &$problems)
    {
        $problems = $sheet->problems()->get();
    }

    /**
     * Get the problems filtered by sheet tags and judges
     *
     * @param $request
     * @param $tags
     * @param $judges
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getProblemsWithSessionFilters($request, &$tags, &$judges)
    {
        // Check server sessions for saved filters data (i.e. tags, organisers, judges)
        if (Session::has(Constants::SHEET_PROBLEMS_SELECTED_FILTERS)) {
            if (isset(Session::get(Constants::SHEET_PROBLEMS_SELECTED_FILTERS)[Constants::SHEET_PROBLEMS_SELECTED_JUDGES])) {
                $judges = Session::get(Constants::SHEET_PROBLEMS_SELECTED_FILTERS)[Constants::SHEET_PROBLEMS_SELECTED_JUDGES];
            }
            if (isset(Session::get(Constants::SHEET_PROBLEMS_SELECTED_FILTERS)[Constants::SHEET_PROBLEMS_SELECTED_TAGS])) {
                $tags = Session::get(Constants::SHEET_PROBLEMS_SELECTED_FILTERS)[Constants::SHEET_PROBLEMS_SELECTED_TAGS];
            }
        }

        // Get problems with applied filters
        return ProblemController::getProblemsWithFilters($request, $tags, $judges);
    }


    /**
     * Save problems filters (tags, judges) into server session to later retrieval
     *
     * @param Request $request
     */
    public function applyProblemsFilters(Request $request)
    {
        Session::put(Constants::SHEET_PROBLEMS_SELECTED_FILTERS, $request->get('selected_filters'));
    }


    /**
     * Clear problems filters (tags, judges) from server session
     */
    public function clearProblemsFilters()
    {
        Session::forget(Constants::SHEET_PROBLEMS_SELECTED_FILTERS);
    }


}
