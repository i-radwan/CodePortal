<?php

use App\Utilities\Constants;

// Groups + sheets routes
Route::group(['middleware' => 'auth'], function () {

    // Create new sheet for the given group
    Route::get('groups/{group}/sheets/create', 'SheetController@addSheetView')
        ->name(Constants::ROUTES_GROUPS_SHEET_CREATE)
        ->middleware(['can:owner-group,group']);

    // Get sheet problem solution
    Route::get('sheets/{sheet}/solution/{problemID}', 'SheetController@retrieveProblemSolution')
        ->name(Constants::ROUTES_GROUPS_SHEET_SOLUTION_DISPLAY)
        ->middleware(['can:owner-or-member-group,sheet']);

    // Get edit sheet view
    Route::get('sheets/{sheet}/edit', 'SheetController@editSheetView')
        ->name(Constants::ROUTES_GROUPS_SHEET_EDIT)
        ->middleware(['can:owner-group,sheet']);

    // Get sheet
    Route::get('sheets/{sheet}', 'SheetController@displaySheet')
        ->name(Constants::ROUTES_GROUPS_SHEET_DISPLAY)
        ->middleware(['can:owner-or-member-group,sheet']);

    // Get group invitees auto complete users list
    Route::get('groups/{group}/invitees_auto_complete', 'GroupController@usersAutoComplete')
        ->name(Constants::ROUTES_GROUPS_INVITEES_AUTO_COMPLETE)
        ->middleware(['can:owner-group,group']);

    // Create group private contest
    Route::get('groups/{group}/contests/create', 'ContestController@addGroupContestView')
        ->name(Constants::ROUTES_GROUPS_CONTEST_CREATE)
        ->middleware(['can:owner-group,group']);

    // Create new group
    Route::get('groups/create', 'GroupController@addGroupView')
        ->name(Constants::ROUTES_GROUPS_CREATE);

    // Edit group
    Route::get('groups/{group}/edit', 'GroupController@editGroupView')
        ->name(Constants::ROUTES_GROUPS_EDIT)
        ->middleware(['can:owner-group,group']);

    // Get group view
    Route::get('groups/{group}', 'GroupController@displayGroup')
        ->name(Constants::ROUTES_GROUPS_DISPLAY);

    // Save sheets problems filters to server session
    Route::post('sheets/create/sheet_tags_judges_filters_sync', 'SheetController@applyProblemsFilters')
        ->name(Constants::ROUTES_GROUPS_SHEET_SYNC_FILTERS);

    // Remove sheets problems filters to server session
    Route::post('sheets/create/sheet_tags_judges_filters_detach', 'SheetController@clearProblemsFilters')
        ->name(Constants::ROUTES_GROUPS_SHEET_DETACH_FILTERS);

    // Add sheet solution
    Route::post('sheets/problem/solution', 'SheetController@saveProblemSolution')
        ->name(Constants::ROUTES_GROUPS_SHEET_SOLUTION_STORE);

    // Edit sheet
    Route::post('sheets/{sheet}/edit', 'SheetController@editSheet')
        ->name(Constants::ROUTES_GROUPS_SHEET_UPDATE)
        ->middleware(['can:owner-group,sheet']);

    // Create new group sheet
    Route::post('groups/{group}/sheets/store', 'SheetController@addSheet')
        ->name(Constants::ROUTES_GROUPS_SHEET_STORE)
        ->middleware(['can:owner-group,group']);

    // Invite member to group
    Route::post('groups/{group}/members/invite', 'GroupController@inviteMember')
        ->name(Constants::ROUTES_GROUPS_INVITATION_STORE)
        ->middleware(['can:owner-group,group']);

    // Join group
    Route::post('groups/{group}/join', 'GroupController@joinGroup')
        ->name(Constants::ROUTES_GROUPS_REQUEST_STORE);

    // Update group
    Route::post('groups/{group}/edit', 'GroupController@editGroup')
        ->name(Constants::ROUTES_GROUPS_UPDATE)
        ->middleware(['can:owner-group,group']);

    // Add new group
    Route::post('groups/create', 'GroupController@addGroup')
        ->name(Constants::ROUTES_GROUPS_STORE);

    // Accept user request
    Route::put('groups/{group}/requests/accept/{user}', 'GroupController@acceptRequest')
        ->name(Constants::ROUTES_GROUPS_REQUEST_ACCEPT)
        ->middleware(['can:owner-group,group']);

    // Reject user request
    Route::put('groups/{group}/requests/reject/{user}', 'GroupController@rejectRequest')
        ->name(Constants::ROUTES_GROUPS_REQUEST_REJECT)
        ->middleware(['can:owner-group,group']);

    // Leave group
    Route::put('groups/{group}/leave', 'GroupController@leaveGroup')
        ->name(Constants::ROUTES_GROUPS_LEAVE)
        ->middleware(['can:member-group,group']);

    // Remove group member
    Route::delete('groups/{group}/members/{user}', 'GroupController@removeMember')
        ->name(Constants::ROUTES_GROUPS_MEMBER_REMOVE)
        ->middleware(['can:owner-group,group', 'can:member-group,group,user']);

    // Delete group
    Route::delete('groups/{group}', 'GroupController@deleteGroup')
        ->name(Constants::ROUTES_GROUPS_DELETE)
        ->middleware(['can:owner-group,group']);

    // Delete sheet
    Route::delete('sheets/{sheet}', 'SheetController@deleteSheet')
        ->name(Constants::ROUTES_GROUPS_SHEET_DELETE)
        ->middleware(['can:owner-group,sheet']);
});

// Get all groups
Route::get('groups', 'GroupController@index')
    ->name(Constants::ROUTES_GROUPS_INDEX);