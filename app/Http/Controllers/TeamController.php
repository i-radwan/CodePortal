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
            ->with('actionUrl', route(Constants::ROUTES_TEAMS_STORE))
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
            ->with('actionUrl', route(Constants::ROUTES_TEAMS_UPDATE, $team[Constants::FLD_TEAMS_ID]))
            ->with('actionBtnTitle', 'Save')
            ->with('teamName', $team[Constants::FLD_TEAMS_NAME])
            ->with('pageTitle', config('app.name') . ' | ' . $team[Constants::FLD_TEAMS_NAME]);
    }

    /**
     * Store a newly created team in database
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Create new team
        $team = new Team($request->all());

        // Attach team creator as a team member
        $user = Auth::user();
        $team->save();
        $team->members()->attach($user[Constants::FLD_USERS_ID]);

        return redirect(route(Constants::ROUTES_PROFILE_TEAMS, $user[Constants::FLD_USERS_USERNAME]))
            ->with('messages', [$team[Constants::FLD_TEAMS_NAME] . ' created successfully!']);
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
        $team[Constants::FLD_TEAMS_NAME] = $request->get(Constants::FLD_TEAMS_NAME);
        $team->save();
        return redirect(route(Constants::ROUTES_PROFILE_TEAMS, $user[Constants::FLD_USERS_USERNAME]))
            ->with('messages', [$team[Constants::FLD_TEAMS_NAME] . ' updated successfully!']);
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
        $errors = '';

        // Get users
        $usernames = explode(",", $request->get(Constants::FLD_USERS_USERNAME));

        foreach ($usernames as $username) {
            $user = User::where(Constants::FLD_USERS_USERNAME, $username)->first();

            // Check if user doesn't exist
            if (!$user) {
                $errors .= $username . " doesn't exist!\n";
                continue;
            }

            // Check if user is already a member
            if ($team->members()->find($user[Constants::FLD_USERS_ID])) {
                $errors .= $username . " is already a member in the team!\n";
                continue;
            }

            $membersCount = $team->members()->count() + $team->invitedUsers()->count();

            if ($membersCount >= Constants::TEAM_MEMBERS_MAX_COUNT) {
                $errors .= "The  team is full!";
                continue;
            }

            // Create new notification if user isn't already invited
            try {

                Notification::make(Auth::user(), $user, $team, Constants::NOTIFICATION_TYPE_TEAM, false);

            } // If the user is already invited the make function throws this exception
            catch (InvitationException $e) {

                $errors .= "$username is already invited\n";
                continue;
            }
        }
        if ($errors == '') {
            return back()->with('messages', ['Users are invited successfully!']);
        } else {
            return back()->withErrors($errors);
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

        // If no members exist then delete the entire team
        if ($team->members()->count() == 0) {
            $team->delete();
        }

        return back()->with('messages', [$user[Constants::FLD_USERS_USERNAME] . ' was removed successfully from ' . $team[Constants::FLD_TEAMS_NAME] . '!']);
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
        // Delete the invitation from the database
        $team->notifications()->ofReceiver($user[Constants::FLD_USERS_ID])->delete();

        return back();
    }

    /**
     * Accept the invitation to join the specified team
     *
     * @param Team $team
     * @return \Illuminate\Http\Response
     */
    public function acceptInvitation(Team $team)
    {
        $user = Auth::user();

        // Delete the invitation from the database
        $team->notifications()->ofReceiver($user[Constants::FLD_USERS_ID])->delete();

        // Add the user as a team member
        $team->members()->attach($user);

        return redirect(route(Constants::ROUTES_PROFILE_TEAMS, $user[Constants::FLD_USERS_USERNAME]));
    }

    /**
     * Reject the invitation to join the specified team
     *
     * @param Team $team
     * @return \Illuminate\Http\Response
     */
    public function rejectInvitation(Team $team)
    {
        $user = Auth::user();

        // Delete the invitation from the database
        $team->notifications()->ofReceiver($user[Constants::FLD_USERS_ID])->delete();

        return back();
    }

    /**
     * Retrieve usernames for auto complete (invitees)
     *
     * @param Request $request
     * @param Team $team
     * @return \Illuminate\Http\JsonResponse
     */
    public function usersAutoComplete(Request $request, Team $team)
    {
        $query = $request->get('query');

        // Get users who aren't members
        $data = User::select([Constants::FLD_USERS_USERNAME . ' as name'])
            ->where(Constants::FLD_USERS_USERNAME, 'LIKE', "%$query%")
            ->where(Constants::FLD_USERS_USERNAME, '!=', Auth::user()[Constants::FLD_USERS_USERNAME])
            ->whereDoesntHave('joiningTeams', function ($query) use ($team) {
                $query->whereId($team[Constants::FLD_TEAMS_ID]);
            })
            ->get();

        return response()->json($data);
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
        return back()->with('messages', [$team[Constants::FLD_TEAMS_NAME] . ' deleted successfully!']);
    }
}
