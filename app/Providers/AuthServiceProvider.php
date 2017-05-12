<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\Post;
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

        //
        // CONTESTs
        //

        // View or join contest gate
        // User can view and join contest if and only if the contest
        // is public, or the user has non-deleted invitation regarding this contest
        Gate::define("view-join-contest", function (User $user, Contest $contest) {
            // Check if contest is public
            if ($contest[Constants::FLD_CONTESTS_VISIBILITY] == Constants::CONTEST_VISIBILITY_PUBLIC) {
                return true;
            }

            // Check if owner
            if ($contest->owner[Constants::FLD_USERS_ID] == $user[Constants::FLD_USERS_ID]) {
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
        Gate::define("owner-contest", function (User $user, Contest $contest) {
            // Check if user is owner
            return ($contest[Constants::FLD_CONTESTS_OWNER_ID] == $user[Constants::FLD_USERS_ID]);
        });

        // Owner or organizer of contest
        Gate::define("owner-organizer-contest", function (User $user, Contest $contest) {
            // Check if user is organizer or owner
            return (
                $contest[Constants::FLD_CONTESTS_OWNER_ID] == $user[Constants::FLD_USERS_ID] ||
                $user->organizingContests()->find($contest[Constants::FLD_CONTESTS_ID])
            );
        });

        // Participant in contest
        Gate::define("contest-participant", function (User $user, Contest $contest) {
            // Check if user is member and not owner
            return ($user->participatingContests()->find($contest[Constants::FLD_CONTESTS_ID]));
        });


        //
        // GROUPs
        //

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

        // Owner or admin of group/sheet
        Gate::define("owner-admin-group", function (User $user, $resource) {

            // If resource is sheet
            if ($resource instanceof Sheet) {

                // Get sheet group
                if ($group = Group::find($resource[Constants::FLD_SHEETS_GROUP_ID])) {

                    // Check if user is organizer or owner
                    if ($user->owningGroups()->find($group[Constants::FLD_GROUPS_ID])
                        || $user->administratingGroups()->find($group[Constants::FLD_GROUPS_ID])
                    ) {
                        return true;
                    }
                }
                return false;
            }

            // If resource is group
            if ($resource instanceof Group) {

                // Check if user is owner
                return ($user->owningGroups()->find($resource[Constants::FLD_GROUPS_ID])
                    || $user->administratingGroups()->find($resource[Constants::FLD_GROUPS_ID]));
            }

            return false;
        });

        // Member of group
        // TODO: fix the error
        Gate::define("member-group", function (User $user, Group $group) {
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
                    $user->joiningGroups()->find($resource[Constants::FLD_SHEETS_GROUP_ID]) ||
                    $user->administratingGroups()->find($resource[Constants::FLD_SHEETS_GROUP_ID])
                );
            }

            if ($resource instanceof Group) { // If resource is group
                // Check if user is member or owner
                return (
                    $user->owningGroups()->find($resource[Constants::FLD_GROUPS_ID]) ||
                    $user->joiningGroups()->find($resource[Constants::FLD_GROUPS_ID]) ||
                    $user->administratingGroups()->find($resource[Constants::FLD_GROUPS_ID])
                );
            }

            return false;
        });

        //
        // TEAMs
        //

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

        //
        // BLOGs
        //

        // Post owner
        Gate::define("owner-post", function (User $user, Post $post) {
            // Check if user is owner of the post
            return $post->owner[Constants::FLD_USERS_ID] == $user[Constants::FLD_USERS_ID];
        });

        // Comment owner
        Gate::define("owner-comment", function (User $user, Comment $comment) {
            // Check if user is owner of the post
            return $comment->owner[Constants::FLD_USERS_ID] == $user[Constants::FLD_USERS_ID];
        });
    }
}
