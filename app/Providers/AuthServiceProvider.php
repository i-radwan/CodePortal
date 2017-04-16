<?php

namespace App\Providers;

use App\Models\Group;
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
        // ToDo : Generalize this gate to handle teams and groups too
        Gate::define("view-join-contest", function ($user, $contest) {
            $canViewAndJoin = false;
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

        // Owner or organizer of contest
        Gate::define("owner-organizer-contest", function ($user, $contest) {
            // Check if user is organizer or owner
            if ($user->organizingContests()->find($contest->id) ||
                $user->owningContests()->find($contest->id)
            ) return true;
            return false;
        });

        // Owner of group
        Gate::define("owner-group", function ($user, $group) {
            // Check if user is owner
            if ($user->owningGroups()->find($group->id)
            ) return true;
            return false;
        });

        // Owner or member of group
        Gate::define("owner-or-member-group", function ($user, $group) {
            // Check if user is member or owner
            if ($user->owningGroups()->find($group->id)
                || $user->joiningGroups()->find($group->id)
            ) return true;
            return false;
        });

        // Member of group
        Gate::define("member-group", function (User $user, Group $group, User $member) {
            // Check if user is member and not owner
            if (!$member->owningGroups()->find($group->id)
                && $member->joiningGroups()->find($group->id)
            ) return true;
            return false;
        });

        // Owner of sheet group
        Gate::define("owner-sheet", function ($user, $sheet) {
            if ($group = Group::find($sheet[Constants::FLD_SHEETS_GROUP_ID])) {
                // Check if user is organizer or owner
                if ($user->owningGroups()->find($group->id)) return true;
            }
            return false;
        });

        // Owner or member of sheet's group
        Gate::define("owner-or-member-sheet", function ($user, $sheet) {
            // Check if user is organizer or owner
            if ($user->owningGroups()->find($sheet->group_id->id)
                || $user->joiningGroups()->find($sheet->group_id->id)
            ) return true;
            return false;
        });


    }
}
