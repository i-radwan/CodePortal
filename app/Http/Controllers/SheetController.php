<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Sheet;
use Redirect;
use URL;
use Illuminate\Http\Request;

class SheetController extends Controller
{

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
        foreach ($problemsIDs as $problemID) {
            $sheet->problems()->attach($problemID);
        }

        // Return to sheets
        return redirect('group/' . $group->id . '#sheets');
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

}
