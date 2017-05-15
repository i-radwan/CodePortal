<?php

use App\Utilities\Constants;

// Groups + sheets routes
Route::group(['middleware' => 'auth'], function () {

    // Create new sheet for the given group
    Route::get('groups/{group}/sheets/create', 'Groups\SheetController@addSheetView')
        ->name(Constants::ROUTES_GROUPS_SHEET_CREATE)
        ->middleware(['canGateForUser:owner-admin-group,group']);

    // Get sheet problem solution
    Route::get('sheets/{sheet}/solution/{problemID}', 'Groups\SheetController@retrieveProblemSolution')
        ->name(Constants::ROUTES_GROUPS_SHEET_SOLUTION_DISPLAY)
        ->middleware(['canGateForUser:owner-or-member-group,sheet']);

    // Get edit sheet view
    Route::get('sheets/{sheet}/edit', 'Groups\SheetController@editSheetView')
        ->name(Constants::ROUTES_GROUPS_SHEET_EDIT)
        ->middleware(['canGateForUser:owner-admin-group,sheet']);

    // Get sheet
    Route::get('sheets/{sheet}', 'Groups\SheetController@displaySheet')
        ->name(Constants::ROUTES_GROUPS_SHEET_DISPLAY)
        ->middleware(['canGateForUser:owner-or-member-group,sheet']);

    // Get group invitees auto complete users list
    Route::get('groups/{group}/invitees_auto_complete', 'Groups\GroupController@usersAutoComplete')
        ->name(Constants::ROUTES_GROUPS_INVITEES_AUTO_COMPLETE)
        ->middleware(['canGateForUser:owner-admin-group,group']);

    // Create group private contest
    Route::get('groups/{group}/contests/create', 'Contests\ContestController@addGroupContestView')
        ->name(Constants::ROUTES_GROUPS_CONTEST_CREATE)
        ->middleware(['canGateForUser:owner-admin-group,group']);

    // Create new group
    Route::get('groups/create', 'Groups\GroupController@addGroupView')
        ->name(Constants::ROUTES_GROUPS_CREATE);

    // Edit group
    Route::get('groups/{group}/edit', 'Groups\GroupController@editGroupView')
        ->name(Constants::ROUTES_GROUPS_EDIT)
        ->middleware(['canGateForUser:owner-group,group']);

    // Admins auto complete
    Route::get('groups/admins_auto_complete', 'Groups\GroupController@adminsAutoComplete')
        ->name(Constants::ROUTES_GROUPS_ADMINS_AUTO_COMPLETE);

    // Get group view
    Route::get('groups/{group}', 'Groups\GroupController@displayGroup')
        ->name(Constants::ROUTES_GROUPS_DISPLAY);


    // Save sheets problems filters to server session
    Route::post('sheets/create/sheet_tags_judges_filters_sync', 'Groups\SheetController@applyProblemsFilters')
        ->name(Constants::ROUTES_GROUPS_SHEET_SYNC_FILTERS);

    // Remove sheets problems filters to server session
    Route::post('sheets/create/sheet_tags_judges_filters_detach', 'Groups\SheetController@clearProblemsFilters')
        ->name(Constants::ROUTES_GROUPS_SHEET_DETACH_FILTERS);

    // Add sheet solution
    Route::post('sheets/problem/solution', 'Groups\SheetController@saveProblemSolution')
        ->name(Constants::ROUTES_GROUPS_SHEET_SOLUTION_STORE);

    // Update sheet
    Route::post('sheets/{sheet}/edit', 'Groups\SheetController@editSheet')
        ->name(Constants::ROUTES_GROUPS_SHEET_UPDATE)
        ->middleware(['canGateForUser:owner-admin-group,sheet']);

    // Create new group sheet
    Route::post('groups/{group}/sheets', 'Groups\SheetController@addSheet')
        ->name(Constants::ROUTES_GROUPS_SHEET_STORE)
        ->middleware(['canGateForUser:owner-admin-group,group']);

    // Invite member to group
    Route::post('groups/{group}/members/invite', 'Groups\GroupController@inviteMember')
        ->name(Constants::ROUTES_GROUPS_INVITATION_STORE)
        ->middleware(['canGateForUser:owner-admin-group,group']);

    // Join group
    Route::post('groups/{group}/join', 'Groups\GroupController@joinGroup')
        ->name(Constants::ROUTES_GROUPS_REQUEST_STORE);

    // Update group
    Route::post('groups/{group}', 'Groups\GroupController@editGroup')
        ->name(Constants::ROUTES_GROUPS_UPDATE)
        ->middleware(['canGateForUser:owner-group,group']);

    // Add new group
    Route::post('groups', 'Groups\GroupController@addGroup')
        ->name(Constants::ROUTES_GROUPS_STORE);

    // Accept user request
    Route::put('groups/{group}/requests/accept/{user}', 'Groups\GroupController@acceptRequest')
        ->name(Constants::ROUTES_GROUPS_REQUEST_ACCEPT)
        ->middleware(['canGateForUser:owner-admin-group,group']);

    // Reject user request
    Route::put('groups/{group}/requests/reject/{user}', 'Groups\GroupController@rejectRequest')
        ->name(Constants::ROUTES_GROUPS_REQUEST_REJECT)
        ->middleware(['canGateForUser:owner-admin-group,group']);

    // Leave group
    Route::put('groups/{group}/leave', 'Groups\GroupController@leaveGroup')
        ->name(Constants::ROUTES_GROUPS_LEAVE)
        ->middleware(['canGateForUser:member-group,group']);

    // Remove group member
    Route::delete('groups/{group}/members/{user}', 'Groups\GroupController@removeMember')
        ->name(Constants::ROUTES_GROUPS_MEMBER_REMOVE)
        ->middleware(['canGateForUser:owner-admin-group,group', 'canGateForUser:member-group,group,user']);

    // Delete group
    Route::delete('groups/{group}', 'Groups\GroupController@deleteGroup')
        ->name(Constants::ROUTES_GROUPS_DELETE)
        ->middleware(['canGateForUser:owner-group,group']);

    // Delete sheet
    Route::delete('sheets/{sheet}', 'Groups\SheetController@deleteSheet')
        ->name(Constants::ROUTES_GROUPS_SHEET_DELETE)
        ->middleware(['canGateForUser:owner-admin-group,sheet']);
});

// Get all groups
Route::get('groups', 'Groups\GroupController@index')
    ->name(Constants::ROUTES_GROUPS_INDEX);