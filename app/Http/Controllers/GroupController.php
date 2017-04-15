<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Auth;
use App\Utilities\Constants;

class GroupController extends Controller
{
    /**
     * Show the groups page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $data = [];

        $data[Constants::GROUPS_GROUPS_KEY] =
            Group::paginate(Constants::GROUPS_COUNT_PER_PAGE);

        return view('groups.index')->with('data', $data)
            ->with('pageTitle', config('app.name') . ' | Groups');
    }


    /**
     * Show add/edit group page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addEditGroupView()
    {
        return view('groups.add_edit')->with('pageTitle', config('app.name') . ' | Group');
    }

    /**
     * Add new group to database
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function addGroup(Request $request)
    {
        $group = new Group($request->all());
        $group->owner_id = Auth::user()->id;
        $group->save();
        return redirect('groups/'); // ToDo
        // return redirect('group/' . $group->id);
    }

}
