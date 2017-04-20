<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
| NOTE:: Routes order MATTERS
|
*/

// Homepage routes...
Route::get('/', 'HomeController@index');

// Profile routes...
Route::get('profile/{user}', 'UserController@index');
Route::get('edit', 'UserController@edit'); // ToDo: @Abzo auth middleware required, choose better route
Route::post('edit', 'UserController@editProfile');  // ToDo: @Abzo auth middleware required, choose better route

// Teams routes...
// TODO: to be merged with profile routes and to add middlewares
Route::get('profile/{user}/teams', 'TeamController@index');
Route::get('teams/create', 'TeamController@create');
Route::get('teams/{team}/edit', 'TeamController@edit');
Route::post('teams', 'TeamController@store');
Route::post('teams/{team}', 'TeamController@update');
Route::post('teams/{team}/invite', 'TeamController@inviteMember');
Route::delete('teams/{team}/remove/{user}', 'TeamController@removeMember');
Route::delete('teams/{team}/invitations/{user}/cancel', 'TeamController@cancelInvitation');
Route::put('teams/{team}/invitations/{user}/accept', 'TeamController@acceptInvitation');
Route::put('teams/{team}/invitations/{user}/reject', 'TeamController@rejectInvitation');
Route::delete('teams/{team}', 'TeamController@destroy');

// Contest routes...
Route::get('contests', 'ContestController@index');

Route::group(['middleware' => 'auth'], function () {

    // Contests routes
    Route::get('contest/add', 'ContestController@addEditContestView');
    Route::get('contest/edit', 'ContestController@addEditContestView');  // ToDo may need authorization
    Route::get('tags_auto_complete', 'ContestController@tagsAutoComplete');
    Route::get('contest/add/organisers_auto_complete', 'ContestController@organisersAutoComplete');

    Route::post('contest/add', 'ContestController@addContest');
    Route::post('contest/add/tags_judges_filters_sync', 'ContestController@applyProblemsFilters');
    Route::post('contest/add/tags_judges_filters_detach', 'ContestController@clearProblemsFilters');
    Route::post('contest/edit', 'ContestController@editContest');  // ToDo may need authorization
    Route::post('contest/join/{contest}', 'ContestController@joinContest')->middleware(['contestAccessAuth:view-join-contest,contest']);

    Route::put('contest/reorder/{contest}', 'ContestController@reorderContest')->middleware(['can:owner-contest,contest']);
    Route::put('contest/leave/{contest}', 'ContestController@leaveContest');

    Route::delete('contest/delete/{contest}', 'ContestController@deleteContest');

    // Question routes...
    Route::put('contest/question/announce/{question}', 'ContestController@announceQuestion');
    Route::put('contest/question/renounce/{question}', 'ContestController@renounceQuestion');

    Route::post('contest/question/answer', 'ContestController@answerQuestion');
    Route::post('contest/question/{contestID}', 'ContestController@addQuestion');

    // Notifications routes...
    Route::put('notifications/mark_all_read', 'NotificationController@markAllUserNotificationsRead');

    Route::delete('notification/{notification}', 'NotificationController@deleteNotification');

    // Groups + Sheets routes...
    Route::get('sheet/solution/{sheet}/{problemID}', 'SheetController@retrieveProblemSolution')->middleware(['can:owner-or-member-group,sheet']);
    Route::get('sheet/new/{group}', 'SheetController@addSheetView')->middleware(['can:owner-group,group']);
    Route::get('sheet/edit/{sheet}', 'SheetController@editSheetView')->middleware(['can:owner-group,sheet']);
    Route::get('sheet/{sheet}', 'SheetController@displaySheet')->middleware(['can:owner-or-member-group,sheet']);

    Route::get('group/contest/new/{group}', 'ContestController@addGroupContestView');
    Route::get('group/new', 'GroupController@addGroupView');
    Route::get('group/edit/{group}', 'GroupController@editGroupView')->middleware(['can:owner-group,group']);
    Route::get('group/{group}', 'GroupController@displayGroup');

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

    Route::delete('group/member/{group}/{user}', 'GroupController@removeMember')->middleware(['can:owner-group,group'])->middleware(['can:member-group,group,user']);
    Route::delete('group/{group}', 'GroupController@deleteGroup')->middleware(['can:owner-group,group']);
    Route::delete('sheet/{sheet}', 'SheetController@deleteSheet')->middleware(['can:owner-group,sheet']);
});

Route::get('contest/{contest}', 'ContestController@displayContest')->middleware(['contestAccessAuth:view-join-contest,contest']);

// Problems routes...
Route::get('problems', 'ProblemController@index');

// Blogs routes...
Route::get('blogs', 'BlogController@index');

// Groups routes...
Route::get('groups', 'GroupController@index');


// Authentication route definitions copied from function 'Auth::routes()'
// so we can easily edit them later if needed

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');

// Errors Routes...
Route::get('errors/404', function () {
    return view('errors.404')->with('pageTitle', 'CodePortal | 404');
});
Route::get('errors/401', function () {
    return view('errors.401')->with('pageTitle', 'CodePortal | 401');
});
