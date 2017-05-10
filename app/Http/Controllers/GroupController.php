<?php

namespace App\Http\Controllers;

use Auth;
use Redirect;
use URL;
use App\Models\User;
use App\Models\Group;
use App\Models\Notification;
use App\Utilities\Constants;
use App\Utilities\Utilities;
use App\Exceptions\InvitationException;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Show the groups page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get search filter
        $searchStr = Utilities::makeInputSafe(request()->get('name'));

        // Get public groups and paginate (apply filters too)
        $groups = Group::ofName($searchStr)->paginate(Constants::GROUPS_COUNT_PER_PAGE);

        return view('groups.index')
            ->with('groups', $groups)
            ->with('pageTitle', config('app.name') . ' | Groups');
    }

    /**
     * Show single group page
     *
     * @protected by auth middleware
     * @protected by ModelNotFoundException (in case user asked for non-existing group in URL)
     *
     * @param Group $group
     * @return \Illuminate\View\View $this
     */
    public function displayGroup(Group $group)
    {
        $this->getMembersInfo($group, $members);
        $this->getAdminsInfo($group, $admins);
        $this->getRequestsInfo($group, $seekers);
        $this->getSheetsInfo($group, $sheets);
        $this->getContestsInfo($group, $contests);

        return view('groups.group')
            ->with('group', $group)
            ->with('members', $members)
            ->with('admins', $admins)
            ->with('seekers', $seekers)
            ->with('sheets', $sheets)
            ->with('contests', $contests)
            ->with('pageTitle', config('app.name') . ' | ' . $group[Constants::FLD_GROUPS_NAME]);
    }

    /**
     * Show add group page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addGroupView()
    {
        return view('groups.add_edit')
            ->with('formAction', route(Constants::ROUTES_GROUPS_STORE))
            ->with('btnText', 'Add')
            ->with('pageTitle', config('app.name') . ' | Group');
    }

    /**
     * Show edit group page
     *
     * @protected by auth & can:owner-group middleware
     *
     * @param Group $group
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editGroupView(Group $group)
    {
        return view('groups.add_edit')
            ->with('formAction', route(Constants::ROUTES_GROUPS_UPDATE, $group[Constants::FLD_GROUPS_ID]))
            ->with('btnText', 'Edit')
            ->with('group', $group)
            ->with('pageTitle', config('app.name') . ' | ' . $group[Constants::FLD_GROUPS_NAME]);
    }

    /**
     * Add new group to database
     *
     * @protected by auth middleware
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function addGroup(Request $request)
    {
        // Create new group
        $group = new Group($request->all());

        // Assign group owner to current user
        $group[Constants::FLD_GROUPS_OWNER_ID] = Auth::user()[Constants::FLD_USERS_ID];

        if ($group->save()) {

            // Assign admins
            $admins = explode(",", $request->get('admins'));
            $admins = User::whereIn('username', $admins)->get(); //It's a Collection but a Model is needed
            foreach ($admins as $admin) {
                if ($admin[Constants::FLD_USERS_ID] != Auth::user()[Constants::FLD_USERS_ID])
                    $group->admins()->save($admin);
            }

        } else {        // return error message
            \Session::flash("messages", ["Sorry, Group was not saved. Please retry later"]);
            return redirect(route(Constants::ROUTES_GROUPS_INDEX));
        }

        return redirect(route(Constants::ROUTES_GROUPS_DISPLAY, $group[Constants::FLD_GROUPS_ID]));
    }

    /**
     * Update group in database
     *
     * @protected by auth & can:owner-group middleware
     *
     * @param Request $request
     * @param Group $group
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function editGroup(Request $request, Group $group)
    {
        // Update name and save
        $group[Constants::FLD_GROUPS_NAME] = $request->get('name');

        if ($group->save()) {

            // Remove add admins then reattach
            $group->admins()->detach();

            // Update admins
            $admins = explode(",", $request->get('admins'));

            $admins = User::whereIn('username', $admins)->get(); //It's a Collection but a Model is needed
            foreach ($admins as $admin) {
                if ($admin[Constants::FLD_USERS_ID] != Auth::user()[Constants::FLD_USERS_ID])
                    $group->admins()->save($admin);
            }
        } else {        // return error message
            \Session::flash("messages", ["Sorry, Group was not saved. Please retry later"]);
            return redirect(route(Constants::ROUTES_GROUPS_INDEX));
        }

        return redirect(route(Constants::ROUTES_GROUPS_DISPLAY, $group[Constants::FLD_GROUPS_ID]));
    }

    /**
     * Delete a certain group if you're owner
     *
     * @protected by auth & can:owner-group middleware
     *
     * @param Group $group
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function deleteGroup(Group $group)
    {
        $group->delete();
        return redirect(route(Constants::ROUTES_GROUPS_INDEX));
    }

    /**
     * Remove user membership from a group
     *
     * @protected by auth & can:owner-admin-group middleware
     *
     * @param Group $group
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeMember(Group $group, User $user)
    {
        $user->joiningGroups()->detach($group);

        return back();
    }

    /**
     * Invite user to a group
     *
     * @protected by auth & can:owner-admin-group middleware
     * @protected by ModalNotFoundException for Group modal
     *
     * @param Request $request
     * @param Group $group
     * @return \Illuminate\Http\RedirectResponse
     */
    public function inviteMember(Request $request, Group $group)
    {
        // Flags to tackle errors
        $errors = '';

        // Iterate over all invitees
        $usernames = explode(",", $request->get('usernames'));
        foreach ($usernames as $username) {
            // Get user
            $user = User::where(Constants::FLD_USERS_USERNAME, '=', $username)->first();

            // If user doesn't exist
            if (!$user) {
                $errors .= "$username doesn't exist!\n";
                continue;
            }
            // Check if already member or owner
            if (\Gate::forUser($user)->allows("owner-or-member-group", [$group])) {
                $errors .= "$username is already member exist!\n";
                continue;
            }

            // Create new notification if user isn't already invited
            try {
                Notification::make(Auth::user(), $user, $group, Constants::NOTIFICATION_TYPE_GROUP, false);

                // Check if user has already requested to join (if so, add him)
                if ($user->seekingJoinGroups()->find($group[Constants::FLD_GROUPS_ID])) {
                    // Remove user join request
                    $group->membershipSeekers()->detach($user);

                    // Save user to members
                    $group->members()->save($user);
                }
            } // If the user is already invited the make function throws this exception
            catch (InvitationException $e) {
                $errors .= "$username is already invited\n";
                continue;
            }
        }

        // Handle response errors/messages
        if ($errors != '') {
            return back()->withErrors($errors);
        }
        return back()->with('messages', ['Users are invited successfully!']);
    }

    /**
     * Cancel user membership in a group
     *
     * @protected by member-group middleware so only members can leave
     *
     * @param Group $group
     * @return \Illuminate\Http\RedirectResponse
     */
    public function leaveGroup(Group $group)
    {
        Auth::user()->joiningGroups()->detach($group);

        return back();
    }

    /**
     * Register user membership in a group
     *
     * Authorization happens in the defined Gate
     *
     * @param Group $group
     * @return \Illuminate\Http\RedirectResponse
     */
    public function joinGroup(Group $group)
    {
        $user = Auth::user();

        // Check if user has valid (non-deleted) invitation for joining this group
        $groupInvitations = $group->notifications()->ofReceiver($user[Constants::FLD_USERS_ID]);

        // Invitation exists
        if ($groupInvitations->count() > 0) {

            // Join the group
            $user->joiningGroups()->syncWithoutDetaching([$group[Constants::FLD_GROUPS_ID]]);

            // Delete user joining invitation once joined the group
            // Because if the user left the group and the then rejoined
            // (if the invitation still exists), he will join, and we
            // should prevent this
            $groupInvitations->delete();

        } // Else, check if the user hasn't sent joining request (if sent stop, else send one)
        else if (!$user->seekingJoinGroups()->find($group[Constants::FLD_GROUPS_ID])) {
            // Send joining request
            $user->seekingJoinGroups()->syncWithoutDetaching([$group[Constants::FLD_GROUPS_ID]]);
        }

        return back();
    }

    /**
     * Accept group join request
     *
     * @protected by middleware (to allow owners only)
     *
     * @param Group $group
     * @param User $user
     * @return mixed
     */
    public function acceptRequest(Group $group, User $user)
    {
        if ($user) {
            // Remove user invitation for the same reason in the joinGroup function
            $group->notifications()->ofReceiver($user[Constants::FLD_USERS_ID])->delete();

            // Remove user join request
            $group->membershipSeekers()->detach($user);

            // Save user to members
            $group->members()->save($user);
        }

        return Redirect::to(URL::previous() . "#requests");
    }

    /**
     * Reject group join request
     *
     * @protected by middleware (to allow owners only)
     *
     * @param Group $group
     * @param User $user
     * @return mixed
     */
    public function rejectRequest(Group $group, User $user)
    {
        $group->membershipSeekers()->detach($user);

        return Redirect::to(URL::previous() . "#requests");
    }

    /**
     * Get group members data
     *
     * @param Group $group
     * @param array $members
     */
    private function getMembersInfo(Group $group, &$members)
    {
        $members = $group
            ->members()
            ->select(Constants::MEMBERS_DISPLAYED_FIELDS)
            ->get();

    }

    /**
     * Get group members data
     *
     * @param Group $group
     * @param array $admins
     */
    private function getAdminsInfo(Group $group, &$admins)
    {
        $admins = $group
            ->admins()
            ->select(Constants::MEMBERS_DISPLAYED_FIELDS)
            ->get();

    }

    /**
     * Get group contests data
     *
     * @param Group $group
     * @param array $contests
     */
    private function getContestsInfo(Group $group, &$contests)
    {
        $contests = $group
            ->contests()
            ->select(Constants::CONTESTS_DISPLAYED_FIELDS)
            ->paginate(Constants::CONTESTS_COUNT_PER_PAGE);
    }

    /**
     * Get group joining requests data
     *
     * @param Group $group
     * @param array $seekers
     */
    private function getRequestsInfo(Group $group, &$seekers)
    {
        $seekers = $group
            ->membershipSeekers()
            ->select(Constants::REQUESTS_DISPLAYED_FIELDS)
            ->get();

    }

    /**
     * Get group sheets
     *
     * @param Group $group
     * @param array $sheets
     */
    private function getSheetsInfo(Group $group, &$sheets)
    {
        $sheets = $group
            ->sheets()
            ->select(Constants::SHEETS_DISPLAYED_FIELDS)
            ->get();

    }

    /**
     * Retrieve usernames for auto complete
     *
     * @param Request $request
     * @param Group $group
     * @return \Illuminate\Http\JsonResponse
     */
    public function usersAutoComplete(Request $request, Group $group)
    {
        $query = $request->get('query');

        // Get users who aren't members
        $data = User::select([Constants::FLD_USERS_USERNAME . ' as name'])
            ->where(Constants::FLD_USERS_USERNAME, 'LIKE', "%$query%")
            ->where(Constants::FLD_USERS_USERNAME, '!=', Auth::user()[Constants::FLD_USERS_USERNAME])
            ->whereDoesntHave('joiningGroups', function ($query) use ($group) {
                $query->whereId($group[Constants::FLD_GROUPS_ID]);
            })
            ->get();
        return response()->json($data);
    }


    /**
     * Retrieve usernames for auto complete
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function adminsAutoComplete(Request $request)
    {
        $query = $request->get('query');
        $users = User::select([Constants::FLD_USERS_USERNAME . ' as name'])
            ->where(Constants::FLD_USERS_USERNAME, 'LIKE', "%$query%")
            ->where(Constants::FLD_USERS_USERNAME, '!=', Auth::user()[Constants::FLD_USERS_USERNAME])
            ->get();
        return response()->json($users);
    }
}
