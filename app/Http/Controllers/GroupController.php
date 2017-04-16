<?php

namespace App\Http\Controllers;

use App\Exceptions\GroupInvitationException;
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
     * @param Group $group
     * @return \Illuminate\View\View $this
     */
    public function displayGroup(Group $group)
    {
        $currentUser = Auth::user();

        if (!$group) return redirect('groups/');

        $data = [];

        $this->getBasicGroupInfo($currentUser, $group, $data);
        $this->getMembersInfo($group, $data);
        $this->getRequestsInfo($group, $data);
        $this->getSheetsInfo($group, $data);

        return view('groups.group')
            ->with('data', $data)
            ->with('pageTitle', config('app.name') . ' | ' . $group->name);
    }

    /**
     * Show add group page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addGroupView()
    {
        return view('groups.add_edit')
            ->with('formAction', 'group/add')
            ->with('btnText', 'Add')
            ->with('pageTitle', config('app.name') . ' | Group');
    }

    /**
     * Show edit group page
     *
     * @param Group $group
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editGroupView(Group $group)
    {
        return view('groups.add_edit')
            ->with('formAction', 'group/edit/' . $group->id)
            ->with('btnText', 'Edit')
            ->with('group', $group)
            ->with('pageTitle', config('app.name') . ' | ' . $group->name);
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
        return redirect('group/' . $group->id);
    }

    /**
     * Update group in database
     *
     * @param Request $request
     * @param Group $group
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function editGroup(Request $request, Group $group)
    {
        $group->name = $request->get('name');
        $group->save();
        return redirect('group/' . $group->id);
    }

    /**
     * Delete a certain group if you're owner
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
     * @param Request $request
     * @param Group $group
     * @return \Illuminate\Http\RedirectResponse
     */
    public function inviteMember(Request $request, Group $group)
    {
        // Get user
        $user = User::where(Constants::FLD_USERS_USERNAME, '=', $request->get('username'))->first();

        // Check if already member or owner
        if (\Gate::allows("owner-or-member-group", [$group, $user])) {
            return back()->withErrors([$request->get('username') . ' is already member!']);
        }

        if ($group && $user && \Gate::denies("member-group", [$group, $user])) {

            // Create new notification if user isn't already invited
            try {
                new Notification($request->all(), Auth::user(), $user, $group,
                    Constants::NOTIFICATION_TYPE[Constants::NOTIFICATION_TYPE_GROUP], false);

            } catch (GroupInvitationException $e) {
                return back()->withErrors([$request->get('username') . ' is already invited!']);
            }
        }
        return back()->with('messages', [$request->get('username') . ' invited successfully!']);
    }

    /**
     * Cancel user membership in a group
     *
     * @param Group $group
     * @return \Illuminate\Http\RedirectResponse
     */
    public function leaveGroup(Group $group)
    {
        $user = Auth::user();

        // Leave if and only if not owner
        if ($group->owner->id != $user->id)
            $user->joiningGroups()->detach($group);
        return back();
    }

    /**
     * Register user membership in a group
     *
     * Another way to join is via requests acceptance (by owner)
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
        $groupsInvitation = $user->userDisplayableReceivedNotifications()
            ->where(Constants::FLD_NOTIFICATIONS_TYPE, '=', Constants::NOTIFICATION_TYPE[Constants::NOTIFICATION_TYPE_GROUP])
            ->where(Constants::FLD_NOTIFICATIONS_RESOURCE_ID, '=', $group->id)->first();

        // Else, check if the user hasn't sent joining request (if sent stop, else send one)

        if ($groupsInvitation) {
            // Join the group
            $user->joiningGroups()->syncWithoutDetaching([$group->id]);
            // Delete user joining invitation once joined the group
            // Because if the user left the group and the then rejoined
            // (if the invitation still exists), he will join, and we
            // should prevent this
            $groupsInvitation->update([Constants::FLD_NOTIFICATIONS_STATUS =>
                Constants::NOTIFICATION_STATUS[Constants::NOTIFICATION_STATUS_DELETED]]);
        } else if (!$user->seekingJoinGroups()->find($group->id)) { // Send joining request
            $user->seekingJoinGroups()->syncWithoutDetaching([$group->id]);
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
            $group->membershipSeekers()->detach($user);
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
        if ($user) {
            $group->membershipSeekers()->detach($user);
        }
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
        $groupInfo[Constants::SINGLE_GROUP_ID_KEY] = $group->id;

        // Get group name
        $groupInfo[Constants::SINGLE_GROUP_NAME_KEY] = $group->name;

        // Get owner name
        $groupInfo[Constants::SINGLE_GROUP_OWNER_KEY] = $group->owner->username;

        // Is current user an owner?
        $data[Constants::SINGLE_GROUP_EXTRA_KEY][Constants::SINGLE_GROUP_IS_USER_OWNER]
            = ($user->owningGroups()->find($group->id) != null);

        // Is current user an member?
        $isMember = ($user->joiningGroups()->find($group->id) != null);
        $data[Constants::SINGLE_GROUP_EXTRA_KEY][Constants::SINGLE_GROUP_IS_USER_MEMBER]
            = $isMember;

        // Is current user not a member and he has already sent joining request ?
        $hasUserSentRequest = false;
        if (!$isMember && $user->seekingJoinGroups()->find($group->id)) {
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
