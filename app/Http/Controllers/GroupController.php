<?php

namespace App\Http\Controllers;

use App\Exceptions\InvitationException;
use App\Models\Group;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Auth;
use App\Utilities\Constants;
use Redirect;
use URL;
use App\Utilities\Utilities;

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

        // Get search filter
        $searchStr = Utilities::makeInputSafe(request()->get('name'));

        $data[Constants::GROUPS_GROUPS_KEY] =
            Group::ofName($searchStr)->paginate(Constants::GROUPS_COUNT_PER_PAGE);

        return view('groups.index')->with('data', $data)
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
        $currentUser = Auth::user();

        $data = [];

        $this->getBasicGroupInfo($currentUser, $group, $data);
        $this->getMembersInfo($group, $data);
        $this->getRequestsInfo($group, $data);
        $this->getSheetsInfo($group, $data);
        $this->getContestsInfo($group, $data);

        return view('groups.group')
            ->with('data', $data)
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
            ->with('formAction', 'group/new')
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
            ->with('formAction', 'group/edit/' . $group[Constants::FLD_GROUPS_ID])
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

        $group->save();

        return redirect('group/' . $group[Constants::FLD_GROUPS_ID]);
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
        $group->save();

        return redirect('group/' . $group[Constants::FLD_GROUPS_ID]);
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
        return redirect('groups/');
    }

    /**
     * Remove user membership from a group
     *
     * @protected by auth & can:owner-group middleware
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
     * @protected by auth & can:owner-group middleware
     * @protected by ModalNotFoundException for Group modal
     *
     * @param Request $request
     * @param Group $group
     * @return \Illuminate\Http\RedirectResponse
     */
    public function inviteMember(Request $request, Group $group)
    {
        // Get user
        $user = User::where(Constants::FLD_USERS_USERNAME, '=', $request->get('username'))->first();

        // If user doesn't exist
        if (!$user)
            return back()->withErrors([$request->get('username') . " doesn't exist!"]);

        // Check if already member or owner
        if (\Gate::allows("owner-or-member-group", [$group, $user])) {
            return back()->withErrors([$request->get('username') . ' is already a member!']);
        }

        // Create new notification if user isn't already invited
        try {
            Notification::make($request->all(), Auth::user(), $user, $group,
                Constants::NOTIFICATION_TYPE[Constants::NOTIFICATION_TYPE_GROUP], false);

            // Check if user has already requested to join (if so, add him)
            if ($user->seekingJoinGroups()->find($group->id)) {
                // Remove user join request
                $group->membershipSeekers()->detach($user);

                // Save user to members
                $group->members()->save($user);
            }
            return back()->with('messages', [$request->get('username') . ' invited successfully!']);

        } catch (InvitationException $e) {
            // If the user is alreay invited the make function throws this exception
            return back()->withErrors([$request->get('username') . ' is already invited!']);
        }

        return back()->withErrors("Please try again later!");
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
        $groupsInvitation = $user->displayableReceivedNotifications()
            ->where(Constants::FLD_NOTIFICATIONS_TYPE, '=', Constants::NOTIFICATION_TYPE[Constants::NOTIFICATION_TYPE_GROUP])
            ->where(Constants::FLD_NOTIFICATIONS_RESOURCE_ID, '=', $group[Constants::FLD_GROUPS_ID])->first();

        // Invitation exists
        if ($groupsInvitation) {

            // Join the group
            $user->joiningGroups()->syncWithoutDetaching([$group[Constants::FLD_GROUPS_ID]]);

            // Delete user joining invitation once joined the group
            // Because if the user left the group and the then rejoined
            // (if the invitation still exists), he will join, and we
            // should prevent this
            $groupsInvitation->update([Constants::FLD_NOTIFICATIONS_STATUS =>
                Constants::NOTIFICATION_STATUS[Constants::NOTIFICATION_STATUS_DELETED]]);

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
            $user->displayableReceivedNotifications()
                ->where(Constants::FLD_NOTIFICATIONS_TYPE, '=', Constants::NOTIFICATION_TYPE[Constants::NOTIFICATION_TYPE_GROUP])
                ->where(Constants::FLD_NOTIFICATIONS_RESOURCE_ID, '=', $group[Constants::FLD_GROUPS_ID])
                ->update([Constants::FLD_NOTIFICATIONS_STATUS =>
                    Constants::NOTIFICATION_STATUS[Constants::NOTIFICATION_STATUS_DELETED]]);

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
     * Get group basic info (owner)
     *
     * @protected by auth middleware
     *
     * @param User $user
     * @param Group $group
     * @param array $data
     */
    private function getBasicGroupInfo(User $user, Group $group, &$data)
    {
        $groupInfo = [];

        // Get group id
        $groupInfo[Constants::SINGLE_GROUP_ID_KEY] = $group[Constants::FLD_GROUPS_ID];

        // Get group name
        $groupInfo[Constants::SINGLE_GROUP_NAME_KEY] = $group[Constants::FLD_GROUPS_NAME];

        // Get owner name
        $groupInfo[Constants::SINGLE_GROUP_OWNER_KEY] = $group->owner[Constants::FLD_USERS_USERNAME];

        // Is current user an owner?
        $data[Constants::SINGLE_GROUP_EXTRA_KEY][Constants::SINGLE_GROUP_IS_USER_OWNER]
            = ($user->owningGroups()->find($group[Constants::FLD_GROUPS_ID]) != null);

        // Is current user an member?
        $isMember = ($user->joiningGroups()->find($group[Constants::FLD_GROUPS_ID]) != null);
        $data[Constants::SINGLE_GROUP_EXTRA_KEY][Constants::SINGLE_GROUP_IS_USER_MEMBER]
            = $isMember;

        // Is current user not a member and he has already sent joining request ?
        $hasUserSentRequest = false;
        if (!$isMember && $user->seekingJoinGroups()->find($group[Constants::FLD_GROUPS_ID])) {
            $hasUserSentRequest = true;
        }

        $data[Constants::SINGLE_GROUP_EXTRA_KEY][Constants::SINGLE_GROUP_USER_SENT_REQUEST]
            = $hasUserSentRequest;

        // Set group info
        $data[Constants::SINGLE_GROUP_GROUP_KEY] = $groupInfo;
    }


    /**
     * Get group members data
     *
     * @param Group $group
     * @param array $data
     */
    private function getMembersInfo(Group $group, &$data)
    {
        $members = $group
            ->members()
            ->select(Constants::MEMBERS_DISPLAYED_FIELDS)
            ->get();

        // Set group members
        $data[Constants::SINGLE_GROUP_MEMBERS_KEY] = $members;
    }

    /**
     * Get group contests data
     *
     * @param Group $group
     * @param array $data
     */
    private function getContestsInfo(Group $group, &$data)
    {
        $contests = $group
            ->contests()
            ->select(Constants::CONTESTS_DISPLAYED_FIELDS)
            ->paginate(Constants::CONTESTS_COUNT_PER_PAGE);

        // Set group members
        $data[Constants::SINGLE_GROUP_CONTESTS_KEY] = $contests;
    }

    /**
     * Get group joining requests data
     *
     * @param Group $group
     * @param array $data
     */
    private function getRequestsInfo(Group $group, &$data)
    {
        $seekers = $group
            ->membershipSeekers()
            ->select(Constants::REQUESTS_DISPLAYED_FIELDS)
            ->get();

        // Set group members
        $data[Constants::SINGLE_GROUP_REQUESTS_KEY] = $seekers;
    }

    /**
     * Get group sheets
     *
     * @param Group $group
     * @param array $data
     */
    private function getSheetsInfo(Group $group, &$data)
    {
        $sheets = $group
            ->sheets()
            ->select(Constants::SHEETS_DISPLAYED_FIELDS)
            ->get();

        // Set group members
        $data[Constants::SINGLE_GROUP_SHEETS_KEY] = $sheets;
    }


}
