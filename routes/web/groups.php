<?php

Route::group(['middleware' => 'auth'], function () {

    // Groups + Sheets routes...
    Route::get('sheet/solution/{sheet}/{problemID}', 'SheetController@retrieveProblemSolution')->middleware(['can:owner-or-member-group,sheet']);
    Route::get('sheet/new/{group}', 'SheetController@addSheetView')->middleware(['can:owner-group,group']);
    Route::get('sheet/edit/{sheet}', 'SheetController@editSheetView')->middleware(['can:owner-group,sheet']);
    Route::get('sheet/{sheet}', 'SheetController@displaySheet')->middleware(['can:owner-or-member-group,sheet']);

    Route::get('group/{group}/invitees_auto_complete', 'GroupController@usersAutoComplete')->middleware(['can:owner-group,group']);
    Route::get('group/{group}/contest/new', 'ContestController@addGroupContestView')->middleware(['can:owner-group,group']);
    Route::get('group/new', 'GroupController@addGroupView');
    Route::get('group/edit/{group}', 'GroupController@editGroupView')->middleware(['can:owner-group,group']);
    Route::get('group/{group}', 'GroupController@displayGroup');

    Route::post('sheet/add/sheet_tags_judges_filters_sync', 'SheetController@applyProblemsFilters');
    Route::post('sheet/add/sheet_tags_judges_filters_detach', 'SheetController@clearProblemsFilters');

    Route::post('sheet/problem/solution', 'SheetController@saveProblemSolution');
    Route::post('sheet/edit/{sheet}', 'SheetController@editSheet')->middleware(['can:owner-group,sheet']);
    Route::post('sheet/new/{group}', 'SheetController@addSheet')->middleware(['can:owner-group,group']);

    Route::post('group/member/invite/{group}', 'GroupController@inviteMember')->middleware(['can:owner-group,group']);
    Route::post('group/join/{group}', 'GroupController@joinGroup');
    Route::post('group/edit/{group}', 'GroupController@editGroup')->middleware(['can:owner-group,group']);
    Route::post('group/new', 'GroupController@addGroup');

    Route::put('group/request/accept/{group}/{user}', 'GroupController@acceptRequest')->middleware(['can:owner-group,group']);
    Route::put('group/request/reject/{group}/{user}', 'GroupController@rejectRequest')->middleware(['can:owner-group,group']);
    Route::put('group/leave/{group}', 'GroupController@leaveGroup')->middleware(['can:member-group,group']);

    Route::delete('group/member/{group}/{user}', 'GroupController@removeMember')->middleware(['can:owner-group,group', 'can:member-group,group,user']);
    Route::delete('group/{group}', 'GroupController@deleteGroup')->middleware(['can:owner-group,group']);
    Route::delete('sheet/{sheet}', 'SheetController@deleteSheet')->middleware(['can:owner-group,sheet']);
});


// Groups routes...
Route::get('groups', 'GroupController@index');