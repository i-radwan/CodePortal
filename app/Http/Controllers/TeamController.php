<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\User;
use App\Models\Team;
use App\Models\Notification;
use App\Utilities\Constants;
use App\Exceptions\InvitationException;
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
        return view('teams.add_edit')
            ->with('actionTitle', 'Edit Team')
            ->with('actionUrl', url('teams/' . $team->id))
            ->with('actionBtnTitle', 'Save')
            ->with('teamName', $team->name)
            ->with('pageTitle', config('app.name') . ' | Teams');
    }

    /**
     * Store a newly created team in database
     *
     * TODO: auth and validate
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
        return redirect('profile/' . $user->id . '/teams')->with('messages', [$team->name . ' created successfully!']);
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
        $user = Auth::user();
        $team->name = $request->get(Constants::FLD_TEAMS_NAME);
        $team->save();
        return redirect('profile/' . $user->id . '/teams')->with('messages', [$team->name . ' updated successfully!']);
    }

    /**
     * Invite a user to join the specified team
     *
     * @param \Illuminate\Http\Request $request
     * @param Team $team
     * @return mixed
     */
    public function inviteMember(Request $request, Team $team)
    {
        // Get user
        $username = $request->get(Constants::FLD_USERS_USERNAME);
        $user = User::where(Constants::FLD_USERS_USERNAME, $username)->first();

        // If user doesn't exist
        if (!$user) {
            return back()->withErrors([$username . " doesn't exist!"]);
        }

        // TODO: validate and add limits to max number of members per team

        // Create new notification if user isn't already invited
        try {
            Notification::make($request->all(), Auth::user(), $user, $team, Constants::NOTIFICATION_TYPE[Constants::NOTIFICATION_TYPE_TEAM], false);

            return back()->with('messages', [$username . ' invited successfully!']);
        }
        // If the user is already invited the make function throws this exception
        catch (InvitationException $e) {
            return back()->with([$username . ' is already invited!']);
        }
    }

    /**
     * Remove the specified member from the given team
     *
     * @param Team $team
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function removeMember(Team $team, User $user)
    {
        $team->members()->detach($user);

        if ($team->members()->count() == 0) {
            $team->delete();
        }

        return back()->with('messages', [$user->username . ' was removed successfully from ' . $team->name . '!']);
    }

    /**
     * Cancel the invitation to the specified user in the given team
     *
     * @param Team $team
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function cancelInvitation(Team $team, User $user)
    {
        // TODO

        return back();
    }

    /**
     * Accept the invitation to join the specified team
     *
     * @param Team $team
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function acceptInvitation(Team $team, User $user)
    {
        // TODO

        return redirect('profile/' . $user->id . '/teams');
    }

    /**
     * Reject the invitation to join the specified team
     *
     * @param Team $team
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function rejectInvitation(Team $team, User $user)
    {
        // TODO

        return back();
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
        return back()->with('messages', [$team->name . ' deleted successfully!']);
    }
}
