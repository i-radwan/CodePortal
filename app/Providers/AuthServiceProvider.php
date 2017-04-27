<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Contest;
use App\Models\Group;
use App\Models\Sheet;
use App\Models\Team;
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
        Gate::define("view-join-contest", function (User $user, Contest $contest) {
            // Check if owner
            if ($contest->owner[Constants::FLD_USERS_ID] == $user[Constants::FLD_USERS_ID]) {
                return true;
            }

            // Check if contest is public
            if ($contest[Constants::FLD_CONTESTS_VISIBILITY] == Constants::CONTEST_VISIBILITY_PUBLIC) {
                return true;
            }

            // if organizer
            if ($contest->organizers()->find($user[Constants::FLD_USERS_ID])) {
                return true;
            }

            // Check if user is invited to private contest
            if ($contest->notifications()->ofReceiver($user->id)->count() > 0) {
                return true;
            }

            return false;
        });

        // Owner of contest
        Gate::define("owner-contest", function (User $user, $contestID) {
            // Check if user is owner
            return ($user->owningContests()->find($contestID));
        });

        // Owner or organizer of contest
        Gate::define("owner-organizer-contest", function (User $user, $contestID) {
            // Check if user is organizer or owner
            // TODO: what about sending the Contest Model to check its owner?
            return (
                $user->organizingContests()->find($contestID) ||
                $user->owningContests()->find($contestID)
            );
        });

        // Owner of group/sheet
        Gate::define("owner-group", function (User $user, $resource) {

            // If resource is sheet
            if ($resource instanceof Sheet) {

                // Get sheet group
                if ($group = Group::find($resource[Constants::FLD_SHEETS_GROUP_ID])) {

                    // Check if user is organizer or owner
                    if ($user->owningGroups()->find($group[Constants::FLD_GROUPS_ID])) {
                        return true;
                    }
                }
                return false;
            }

            // If resource is group
            if ($resource instanceof Group) {

                // Check if user is owner
                return ($user->owningGroups()->find($resource[Constants::FLD_GROUPS_ID]));
            }

            return false;
        });

        // Member of group
        // TODO: fix the error
        Gate::define("member-group", function (User $user, Group $group) {
            //dump($user);
            //dump(request()->attributes);
            //dd($group);

            dd(request());

            // Check if user is member and not owner
            return (
                !$user->owningGroups()->find($group[Constants::FLD_GROUPS_ID]) &&
                $user->joiningGroups()->find($group[Constants::FLD_GROUPS_ID])
            );
        });

        // Owner or member of group
        Gate::define("owner-or-member-group", function (User $user, $resource) {
            // If resource is sheet
            if ($resource instanceof Sheet) {
                // Check if user is owner or member of sheet's group
                return (
                    $user->owningGroups()->find($resource[Constants::FLD_SHEETS_GROUP_ID]) ||
                    $user->joiningGroups()->find($resource[Constants::FLD_SHEETS_GROUP_ID])
                );
            }

            if ($resource instanceof Group) { // If resource is group
                // Check if user is member or owner
                return (
                    $user->owningGroups()->find($resource[Constants::FLD_GROUPS_ID]) ||
                    $user->joiningGroups()->find($resource[Constants::FLD_GROUPS_ID])
                );
            }

            return false;
        });

        // Member of team
        Gate::define("member-team", function (User $user, Team $team) {
            // Check if user is member of the team
            return ($team->members()->find($user[Constants::FLD_USERS_ID]));
        });

        // Invitee of team
        Gate::define("invitee-team", function (User $user, Team $team) {
            // Check if user is invited to join the team
            return ($team->invitedUsers()->find($user[Constants::FLD_USERS_ID]));
        });
    }
}
