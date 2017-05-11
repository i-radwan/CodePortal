<?php

use App\Utilities\Constants;

// Teams routes
Route::group(['middleware' => 'auth'], function () {
    // Create new team route
    Route::get('teams/create', 'TeamController@create')
        ->name(Constants::ROUTES_TEAMS_CREATE);

    // Edit exist team route
    Route::get('teams/{team}/edit', 'TeamController@edit')
        ->name(Constants::ROUTES_TEAMS_EDIT)
        ->middleware(['canGateForUser:member-team,team']);

    // Teams invitees auto complete route
    Route::get('teams/{team}/invitees_auto_complete', 'TeamController@usersAutoComplete')
        ->name(Constants::ROUTES_TEAMS_INVITEES_AUTO_COMPLETE)
        ->middleware(['canGateForUser:member-team,team']);

    // Store route
    Route::post('teams', 'TeamController@store')
        ->name(Constants::ROUTES_TEAMS_STORE);

    // Update route
    Route::post('teams/{team}', 'TeamController@update')
        ->name(Constants::ROUTES_TEAMS_UPDATE)
        ->middleware(['canGateForUser:member-team,team']);

    // Invite user route
    Route::post('teams/{team}/invite', 'TeamController@inviteMember')
        ->name(Constants::ROUTES_TEAMS_INVITE)
        ->middleware(['canGateForUser:member-team,team']);

    // TODO: check that the user we want to remove is really a team member
    // Remove team member
    Route::delete('teams/{team}/remove/{user}', 'TeamController@removeMember')
        ->name(Constants::ROUTES_TEAMS_MEMBERS_REMOVE)
        ->middleware(['canGateForUser:member-team,team']);

    // Cancel invitation
    Route::delete('teams/{team}/invitations/cancel/{user}', 'TeamController@cancelInvitation')
        ->name(Constants::ROUTES_TEAMS_INVITATIONS_CANCEL)
        ->middleware(['canGateForUser:member-team,team']);

    // Accept invitation
    Route::put('teams/{team}/invitations/accept', 'TeamController@acceptInvitation')
        ->name(Constants::ROUTES_TEAMS_INVITATIONS_ACCEPT)
        ->middleware(['canGateForUser:invitee-team,team']);

    // Reject invitation
    Route::put('teams/{team}/invitations/reject', 'TeamController@rejectInvitation')
        ->name(Constants::ROUTES_TEAMS_INVITATIONS_REJECT)
        ->middleware(['canGateForUser:invitee-team,team']);

    // Delete team
    Route::delete('teams/{team}', 'TeamController@destroy')
        ->name(Constants::ROUTES_TEAMS_DELETE)
        ->middleware(['canGateForUser:member-team,team']);
});