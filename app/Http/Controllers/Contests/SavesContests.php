<?php

namespace App\Http\Controllers\Contests;

use Auth;
use Session;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Contest;
use App\Models\Group;
use App\Models\Notification;
use App\Utilities\Constants;
use Illuminate\Http\Request;

trait SavesContests
{
    /**
     * Save contest to database
     *
     * @param Request $request
     * @param Group $group
     * @param Contest $contest
     * @return mixed
     */
    public function saveContest(Request $request, Group $group = null, Contest $contest = null)
    {
        // Validate contest time at first:
        // Start datetime isn't in the past
        // End date is within the allowed period
        $this->validate($request, [
            Constants::FLD_CONTESTS_TIME => 'required|date_format:Y-m-d H:i:s' .
                '|after:' . Carbon::now() .
                '|before:' . Carbon::now()->addDays(Constants::CONTESTS_MAX_START_DATETIME)
        ]);

        // Flag to indicate that we're saving old contest
        $editingContest = ($contest != null);

        // Adding new contest and assign the owner
        if (!$contest) {
            // Create contest object
            $contest = new Contest($request->all());

            // Assign owner
            $contest->owner()->associate(Auth::user());
        } else {
            // Update contest
            $contest[Constants::FLD_CONTESTS_NAME] = $request->get('name');
            $contest[Constants::FLD_CONTESTS_TIME] = $request->get('time');
            $contest[Constants::FLD_CONTESTS_DURATION] = floor($request->get('duration'));
            $contest[Constants::FLD_CONTESTS_VISIBILITY] = $request->get('visibility');
        }

        // Automatically set visibility to private for groups
        if ($group) {
            $contest[Constants::FLD_CONTESTS_VISIBILITY] = Constants::CONTEST_VISIBILITY_PRIVATE;
        }

        // Fill relations
        if ($contest->save()) {

            // Detach contest old organisers
            if ($editingContest)
                $contest->organizers()->detach();

            // Assign organisers
            if ($request->has('organisers'))
                $this->associateContestOrganisers($contest, $request->get('organisers'), $group);

            // Send notifications
            $this->sendPrivateContestInvitations($contest, $request->get('invitees'), $request->get('visibility'), $group);

            // Add problems
            $this->associateContestProblems($contest, $request->get('problems_ids'));

            // Flush sessions
            Session::forget([Constants::CONTEST_PROBLEMS_SELECTED_FILTERS]);

            // Return success message
            Session::flash("messages", ["Contest Saved Successfully"]);
            return redirect(route(Constants::ROUTES_CONTESTS_DISPLAY, $contest[Constants::FLD_CONTESTS_ID]));

        } else {   // return error message
            Session::flash("messages", ["Sorry, Contest was not saved. Please retry later"]);
            return redirect(route(Constants::ROUTES_CONTESTS_INDEX));
        }
    }

    /**
     * Associate the given contest with the given organisers
     *
     * @param Contest $contest
     * @param $organisers
     * @param Group $group
     */
    private function associateContestOrganisers(Contest $contest, $organisers, Group $group = null)
    {
        // For private contests (not in groups) we set the organisers as received
        // from the request
        if (!$group) {
            $organisers = explode(",", $organisers);
            $organisers = User::whereIn('username', $organisers)->get();
            foreach ($organisers as $organiser) {
                if ($organiser[Constants::FLD_USERS_ID] != Auth::user()[Constants::FLD_USERS_ID])
                    $contest->organizers()->save($organiser);
            }
        }
        // Private group contest
        // Add group admins and owner as contest organisers
        else {
            // Set group owner as organiser if not already the owner
            if ($group->owner() != Auth::user()) {
                $contest->organizers()->attach($group->owner());
            }

            // Set group admins as organisers
            foreach ($group->admins() as $admin) {
                if ($admin[Constants::FLD_USERS_ID] != Auth::user()[Constants::FLD_USERS_ID])
                    $contest->organizers()->save($admin);
            }
        }
    }

    /**
     * Associate problems to contest
     *
     * @param Contest $contest
     * @param $problemsIDs
     */
    private function associateContestProblems(Contest $contest, $problemsIDs)
    {
        // Add Problems
        $problems = explode(",", $problemsIDs);

        // Limit problems array to limit
        $problems = array_slice($problems, 0, Constants::CONTESTS_PROBLEMS_MAX_COUNT);

        // Sync problems
        $contest->problems()->sync($problems);

        // Set initial problems order
        $this->updateContestProblemsOrder($contest, $problems);
    }

    /**
     * Update contest problems order in DB
     *
     * @param Contest $contest
     * @param $problemIDs
     */
    private function updateContestProblemsOrder(Contest $contest, $problemIDs)
    {
        $i = 1;
        foreach ($problemIDs as $problemID) {
            if (!$problemID) continue;
            $problemPivot = $contest->problems()->find($problemID)->pivot;
            $problemPivot[Constants::FLD_CONTEST_PROBLEMS_PROBLEM_ORDER] = $i;
            $problemPivot->save();
            $i++;
        }
    }

    /**
     * Send private contest invitations to invitees
     * If group contest, the invitations will be sent to group members
     *
     * @param Contest $contest
     * @param $invitees
     * @param $visibility
     * @param Group $group
     */
    private function sendPrivateContestInvitations(Contest $contest, $invitees, $visibility, Group $group = null)
    {
        // Send notifications to Invitees if private contest and not for specific group
        if (!$group && $visibility == Constants::CONTEST_VISIBILITY_PRIVATE) {

            // Get invitees
            $invitees = explode(",", $invitees);
            $invitees = User::whereIn('username', $invitees)->get();
            foreach ($invitees as $invitee) {
                // Send notifications
                Notification::make(Auth::user(), $invitee, $contest, Constants::NOTIFICATION_TYPE_CONTEST, false);
            }
        } else if ($group) { // Send group members invitations
            // Get invitees
            foreach ($group->members()->get() as $member) {
                Notification::make(Auth::user(), $member, $contest, Constants::NOTIFICATION_TYPE_CONTEST, false);
            }

            // Associate contest with group
            $group->contests()->syncWithoutDetaching($contest);
        }
    }
}