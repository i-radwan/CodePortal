<?php

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
});

Route::group(['middleware' => 'contestAccessAuth:view-join-contest,contest'], function () {

    Route::get('contest/{contest}', 'ContestController@displayContestProblems');
    Route::get('contest/{contest}/problems', 'ContestController@displayContestProblems');
    Route::get('contest/{contest}/standings', 'ContestController@displayContestStandings');
    Route::get('contest/{contest}/status', 'ContestController@displayContestStatus');
    Route::get('contest/{contest}/participants', 'ContestController@displayContestParticipants');
    Route::get('contest/{contest}/questions', 'ContestController@displayContestQuestions');
});