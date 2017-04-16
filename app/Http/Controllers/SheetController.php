<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Sheet;
use Redirect;
use URL;
use Illuminate\Http\Request;
use App\Utilities\Constants;
use Auth;
use Response;

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
        if (!$sheet) return back();

        $data = [];

        $this->getProblemsInfo($sheet, $data);
        $this->getBasicContestInfo($sheet, $data);

        return view('groups.sheet_views.sheet')
            ->with('data', $data)
            ->with('pageTitle', config('app.name') . ' | ' . $sheet->name);
    }

    /**
     * Show add sheet page
     *
     * @param Group $group
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addSheetView(Group $group)
    {
        return view('groups.sheet_views.add_edit')
            ->with('action', 'Add')
            ->with('url', 'sheet/new/' . $group->id)
            ->with('pageTitle', config('app.name') . ' | Sheet');
    }

    /**
     * Show edit sheet page
     *
     * @param Sheet $sheet
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editSheetView(Sheet $sheet)
    {
        // Show edit sheet view with sheet info attached
        return view('groups.sheet_views.add_edit')
            ->with('action', 'Edit')
            ->with('sheetName', $sheet->name)
            ->with('problemsIDs', implode(",", $sheet->problems()->pluck(Constants::FLD_SHEETS_PROBLEMS_PROBLEM_ID)->toArray()))
            ->with('url', 'sheet/edit/' . $sheet->id)
            ->with('pageTitle', config('app.name') . ' | Sheet');
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
        $sheet->group_id = $group->id;

        // Save sheet
        $sheet->save();

        // Fetch problems and ToDo replace with samir tbl
        $problemsIDs = explode(",", $request->get('problems'));
        $sheet->problems()->sync($problemsIDs);

        // Return to sheets
        return redirect('group/' . $group->id . '#sheets');
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

        // Fetch problems and ToDo replace with samir tbl
        $problemsIDs = explode(",", $request->get('problems'));
        $sheet->problems()->sync($problemsIDs);

        // Return to sheets
        return redirect('group/' . $sheet->group_id . '#sheets');
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
        if (\Gate::allows("owner-sheet", [$sheet])) {

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

                // Write to code file of this solution
                $codeFile = fopen("code/" . $problem->pivot->solution, "w") or die("Unable to open file!");
                fwrite($codeFile, $solution);
                fclose($codeFile);
            }
        }
        return back();
    }

    public
    function retrieveProblemSolution(Sheet $sheet, $problemID)
    {
        // Get solution file name
        $solutionFile = $sheet->problems()->find($problemID)->pivot->solution;

        // Read and return file contents
        $codeFile = fopen("code/$solutionFile", "r");
        $codeFileContest = fread($codeFile, filesize("code/" . $solutionFile));
        fclose($codeFile);
        return $codeFileContest;
    }

    /**
     * Get sheet basic info
     *
     * @param Sheet $sheet
     * @param array $data
     */
    private
    function getBasicContestInfo(Sheet $sheet, &$data)
    {
        $sheetInfo = [];

        // Get sheet id
        $sheetInfo[Constants::SINGLE_SHEET_ID_KEY] = $sheet[Constants::FLD_SHEETS_ID];

        // Get sheet name
        $sheetInfo[Constants::SINGLE_SHEET_NAME_KEY] = $sheet[Constants::FLD_SHEETS_NAME];

        // Get sheet group id
        $sheetInfo[Constants::SINGLE_SHEET_GROUP_ID_KEY] = $sheet[Constants::FLD_SHEETS_GROUP_ID];


        // Is user an organizer?
        $data[Constants::SINGLE_SHEET_EXTRA_KEY][Constants::SINGLE_GROUP_IS_USER_OWNER]
            = Auth::check() ? (Auth::user()->owningGroups()->find($sheet[Constants::FLD_SHEETS_GROUP_ID]) != null) : false;

        // Set contest info
        $data[Constants::SINGLE_SHEET_SHEET_KEY] = $sheetInfo;
    }

    /**
     * Get sheets problems
     *
     * @param Sheet $sheet
     * @param array $data
     */
    private
    function getProblemsInfo(Sheet $sheet, &$data)
    {
        $problems = $sheet->problems()->get();
        // Set group members
        $data[Constants::SINGLE_SHEET_PROBLEMS_KEY] = $problems;
    }


}
