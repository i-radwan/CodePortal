<?php

namespace App\Http\Controllers;

use App\Utilities\Constants;
use Auth;
use Session;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    /**
     * Display a list of the team that the passed user is a member of
     *
     * @param User $user
     * @return \Illuminate\View\View
     */
    public function index(User $user)
    {
        return view('teams.index')
            ->with('user', $user)
            ->with('pageTitle', config('app.name') . ' | Teams');
    }

    /**
     * Show the form for creating a new team
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('teams.add_edit')
            ->with('actionTitle', 'New Team')
            ->with('actionUrl', url('teams'))
            ->with('actionBtnTitle', 'Create')
            ->with('teamName', '')
            ->with('teamMembers', '')
            ->with('pageTitle', config('app.name') . ' | Teams');
    }

    /**
     * Show the form for editing the specified team.
     *
     * @param Team $team
     * @return \Illuminate\View\View
     */
    public function edit(Team $team)
    {
        $membersIDs = $team->members()->get()->pluck(Constants::FLD_USERS_ID)->toArray();

        return view('teams.add_edit')
            ->with('actionTitle', 'Edit Team')
            ->with('actionUrl', url('teams/' . $team->id))
            ->with('actionBtnTitle', 'Save')
            ->with('teamName', $team->name)
            ->with('teamMembers', implode(',', $membersIDs))
            ->with('pageTitle', config('app.name') . ' | Teams');
    }

    /**
     * Store a newly created team in database
     *
     * TODO: auth and validate and use auto-complete and send invitations
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $team = new Team($request->all());
        $team->save();
        $team->members()->attach($user->id);

        $membersIDs = explode(',', $request->get('members', ''));
        $team->members()->sync($membersIDs);

        return redirect('teams/' . $user->id);
    }

    /**
     * Update the specified team in storage
     *
     * @param \Illuminate\Http\Request $request
     * @param Team $team
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Team $team)
    {
        //
    }

    /**
     * Remove the specified team from storage
     *
     * @param Team $team
     * @return \Illuminate\Http\Response
     */
    public function destroy(Team $team)
    {
        $team->delete();
        return back();
    }
}
