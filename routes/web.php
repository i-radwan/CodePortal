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
// TODO: to be merged with profile routes
Route::get('profile/{user}/teams', 'TeamController@index');
Route::group(['middleware' => 'auth'], function () {
    Route::get('teams/create', 'TeamController@create');
    Route::get('teams/{team}/edit', 'TeamController@edit')->middleware(['can:member-team,team']);
    Route::get('teams/{team}/invitees_auto_complete', 'TeamController@usersAutoComplete')->middleware(['can:member-team,team']);

    Route::post('teams', 'TeamController@store');
    Route::post('teams/{team}', 'TeamController@update')->middleware(['can:member-team,team']);
    Route::post('teams/{team}/invite', 'TeamController@inviteMember')->middleware(['can:member-team,team']);

    Route::delete('teams/{team}/remove/{user}', 'TeamController@removeMember')->middleware(['can:member-team,team']);
    Route::delete('teams/{team}/invitations/cancel/{user}', 'TeamController@cancelInvitation')->middleware(['can:member-team,team']);

    Route::put('teams/{team}/invitations/accept', 'TeamController@acceptInvitation')->middleware(['can:invitee-team,team']);
    Route::put('teams/{team}/invitations/reject', 'TeamController@rejectInvitation')->middleware(['can:invitee-team,team']);
    Route::delete('teams/{team}', 'TeamController@destroy')->middleware(['can:member-team,team']);
});

// Contest routes...
Route::get('contests', 'ContestController@index');
Route::get('tags_auto_complete', 'ContestController@tagsAutoComplete');

Route::group(['middleware' => 'auth'], function () {

    // Contests routes
    Route::get('contest/add', 'ContestController@addEditContestView');
    Route::get('contest/{contest}/edit', 'ContestController@addEditContestView')->middleware(['can:owner-contest,contest']);
    Route::get('contest/add/organisers_auto_complete', 'ContestController@usersAutoComplete');
    Route::get('contest/add/invitees_auto_complete', 'ContestController@usersAutoComplete');

    Route::post('contest/add', 'ContestController@addContest');
    Route::post('group/{group}/contest/add', 'ContestController@addGroupContest')->middleware(['can:owner-group,group']);
    Route::post('contest/add/contest_tags_judges_filters_sync', 'ContestController@applyProblemsFilters');
    Route::post('contest/add/contest_tags_judges_filters_detach', 'ContestController@clearProblemsFilters');
    Route::post('contest/{contest}/edit', 'ContestController@editContest')->middleware(['can:owner-contest,contest']);
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

    //Blogs Routes
    Route::get('blogs/add', 'BlogController@addEditPost');
    Route::post('blogs/add', 'BlogController@addPost');
    Route::post('blogs/entry/{post}', 'BlogController@addComment');

    //Likes Routes
    Route::get('blogs/up_vote/entry/{post}', 'VoteController@upVotePost');
    Route::get('blogs/up_vote/comment/{comment}', 'VoteController@upVoteComment');
    Route::get('blogs/down_vote/entry/{post}', 'VoteController@downVotePost');
    Route::get('blogs/down_vote/comment/{comment}', 'VoteController@downVoteComment');

});
Route::group(['middleware' => 'contestAccessAuth:view-join-contest,contest'], function () {

    Route::get('contest/{contest}', 'ContestController@displayContestProblems');
    Route::get('contest/{contest}/problems', 'ContestController@displayContestProblems');
    Route::get('contest/{contest}/standings', 'ContestController@displayContestStandings');
    Route::get('contest/{contest}/status', 'ContestController@displayContestStatus');
    Route::get('contest/{contest}/participants', 'ContestController@displayContestParticipants');
    Route::get('contest/{contest}/questions', 'ContestController@displayContestQuestions');

});
// Problems routes...
Route::get('problems', 'ProblemController@index');

// Blogs routes...
Route::get('blogs', 'BlogController@index');
Route::get('blogs/entries/{user}', 'BlogController@displayUserPosts');
Route::get('blogs/entry/{post}', 'BlogController@displayPost');


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
