<?php

namespace App\Providers;

use App\Models\Group;
use App\Models\Sheet;
use App\Models\User;
use App\Utilities\Constants;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // View or join contest gate
        // User can view and join contest if and only if the contest
        // is public, or the user has non-deleted invitation regarding this contest
        Gate::define("view-join-contest", function ($user, $contest) {
            $canViewAndJoin = false;

            // Check if owner
            if ($contest->owner[Constants::FLD_USERS_ID] == $user[Constants::FLD_USERS_ID]) return true;

            // Check if contest is public
            $canViewAndJoin |= ($contest->visibility == Constants::CONTEST_VISIBILITY[Constants::CONTEST_VISIBILITY_PUBLIC_KEY]);

            if ($canViewAndJoin) return true; // To avoid next query if already true

            // Check if user is invited to private contest
            $contestsInvitations = $user->userDisplayableReceivedNotifications()
                ->where(Constants::FLD_NOTIFICATIONS_TYPE, '=', Constants::NOTIFICATION_TYPE[Constants::NOTIFICATION_TYPE_CONTEST])
                ->where(Constants::FLD_NOTIFICATIONS_RESOURCE_ID, '=', $contest->id)->get();

            $canViewAndJoin |= (count($contestsInvitations) > 0);

            return $canViewAndJoin;
        });

        // Owner of contest
        Gate::define("owner-contest", function ($user, $contestID) {
            // Check if user is owner
            return ($user->owningContests()->find($contestID));
        });

        // Owner or organizer of contest
        Gate::define("owner-organizer-contest", function ($user, $contestID) {
            // Check if user is organizer or owner
            return (($user->organizingContests()->find($contestID) ||
                $user->owningContests()->find($contestID)));
        });

        // Owner of group/sheet
        Gate::define("owner-group", function ($user, $resource) {

            // If resource is sheet
            if ($resource instanceof Sheet) {

                // Get sheet group
                if ($group = Group::find($resource[Constants::FLD_SHEETS_GROUP_ID])) {

                    // Check if user is organizer or owner
                    if ($user->owningGroups()->find($group[Constants::FLD_GROUPS_ID])) return true;
                }
                return false;
            } // If resource is group
            else if ($resource instanceof Group) {

                // Check if user is owner
                return ($user->owningGroups()->find($resource[Constants::FLD_GROUPS_ID]));
            }
            return false;
        });

        // Member of group
        Gate::define("member-group", function (User $user, Group $group, User $member = null) {
            $member = ($member) ? $member : $user;
            // Check if user is member and not owner
            if (!$member->owningGroups()->find($group[Constants::FLD_GROUPS_ID])
                && $member->joiningGroups()->find($group[Constants::FLD_GROUPS_ID])
            ) return true;
            return false;

        });
        // Owner or member of group
        Gate::define("owner-or-member-group", function ($currentUser, $resource, $user = null) {
            // If not user is specified, use the system injected currentUser
            if (!$user) $user = $currentUser;

            // If resource is sheet
            if ($resource instanceof Sheet) {
                // Check if user is owner of sheet's group
                if ($user->owningGroups()->find($resource[Constants::FLD_SHEETS_GROUP_ID])
                    || $user->joiningGroups()->find($resource[Constants::FLD_SHEETS_GROUP_ID])
                ) return true;
                return false;
            } else if ($resource instanceof Group) { // If resource is group
                // Check if user is member or owner
                if ($user->owningGroups()->find($resource[Constants::FLD_GROUPS_ID])
                    || $user->joiningGroups()->find($resource[Constants::FLD_GROUPS_ID])
                ) return true;
            }
            return false;
        });
    }
}
