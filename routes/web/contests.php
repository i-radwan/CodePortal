<?php

use App\Utilities\Constants;

// Contest routes...

// Index all contests
Route::get('contests', 'ContestController@index')
    ->name(Constants::ROUTES_CONTESTS_INDEX);

// Filters tags auto complete
Route::get('tags_auto_complete', 'ContestController@tagsAutoComplete')
    ->name(Constants::ROUTES_CONTESTS_TAGS_AUTO_COMPLETE);

Route::group(['middleware' => 'auth'], function () {

    // Add new contest view
    Route::get('contests/create', 'ContestController@addContestView')
        ->name(Constants::ROUTES_CONTESTS_CREATE);

    // Edit contest view
    Route::get('contests/{contest}/edit', 'ContestController@editContestView')
        ->name(Constants::ROUTES_CONTESTS_EDIT)
        ->middleware(['can:owner-contest,contest']);

    // Organizers auto complete
    Route::get('contests/organisers_auto_complete', 'ContestController@usersAutoComplete')
        ->name(Constants::ROUTES_CONTESTS_ORGANIZERS_AUTO_COMPLETE);

    // Invitees auto complete
    Route::get('contests/invitees_auto_complete', 'ContestController@usersAutoComplete')
        ->name(Constants::ROUTES_CONTESTS_INVITEES_AUTO_COMPLETE);

    // Add contest
    Route::post('contests', 'ContestController@addContest')
        ->name(Constants::ROUTES_CONTESTS_STORE);

    // Add group contest
    Route::post('groups/{group}/contests', 'ContestController@addGroupContest')
        ->name(Constants::ROUTES_GROUPS_CONTEST_STORE)
        ->middleware(['can:owner-group,group']);

    // Sync filters with server session
    Route::post('contests/create/contest_tags_judges_filters_sync', 'ContestController@applyProblemsFilters')
        ->name(Constants::ROUTES_CONTESTS_FILTERS_SYNC);

    // Detach filters from server session
    Route::post('contests/create/contest_tags_judges_filters_detach', 'ContestController@clearProblemsFilters')
        ->name(Constants::ROUTES_CONTESTS_FILTERS_DETACH);

    // Update contest
    Route::post('contests/{contest}', 'ContestController@editContest')
        ->name(Constants::ROUTES_CONTESTS_UPDATE)
        ->middleware(['can:owner-contest,contest']);

    // Join contest
    Route::post('contests/{contest}/join', 'ContestController@joinContest')
        ->name(Constants::ROUTES_CONTESTS_JOIN)
        ->middleware(['contestAccessAuth:view-join-contest,contest']);

    // Save contest new order
    Route::put('contests/{contest}/reorder', 'ContestController@reorderContest')
        ->name(Constants::ROUTES_CONTESTS_REORDER)
        ->middleware(['can:owner-contest,contest']);

    // Leave contest
    Route::put('contests/{contest}/leave', 'ContestController@leaveContest')
        ->name(Constants::ROUTES_CONTESTS_LEAVE);

    // Delete contest
    Route::delete('contests/{contest}/delete', 'ContestController@deleteContest')
        ->name(Constants::ROUTES_CONTESTS_DELETE);

    // Question routes...
    // Announce questions
    Route::put('contests/{question}/announce', 'ContestController@announceQuestion')
        ->name(Constants::ROUTES_CONTESTS_QUESTIONS_ANNOUNCE);

    // Renounce question
    Route::put('contests/{question}/renounce', 'ContestController@renounceQuestion')
        ->name(Constants::ROUTES_CONTESTS_QUESTIONS_RENOUNCE);

    // Save question answer
    Route::post('contests/question/answer', 'ContestController@answerQuestion')
        ->name(Constants::ROUTES_CONTESTS_QUESTIONS_ANSWER_STORE);

    // Post new question
    Route::post('contests/question/{contestID}', 'ContestController@addQuestion')
        ->name(Constants::ROUTES_CONTESTS_QUESTIONS_STORE);

});

Route::group(['middleware' => 'contestAccessAuth:view-join-contest,contest'], function () {

    // Get contest views
    Route::get('contests/{contest}', 'ContestController@displayContestProblems')
        ->name(Constants::ROUTES_CONTESTS_DISPLAY);

    Route::get('contests/{contest}/problems', 'ContestController@displayContestProblems')
        ->name(Constants::ROUTES_CONTESTS_PROBLEMS);

    Route::get('contests/{contest}/standings', 'ContestController@displayContestStandings')
        ->name(Constants::ROUTES_CONTESTS_STANDINGS);

    Route::get('contests/{contest}/status', 'ContestController@displayContestStatus')
        ->name(Constants::ROUTES_CONTESTS_STATUS);

    Route::get('contests/{contest}/participants', 'ContestController@displayContestParticipants')
        ->name(Constants::ROUTES_CONTESTS_PARTICIPANTS);

    Route::get('contests/{contest}/questions', 'ContestController@displayContestQuestions')
        ->name(Constants::ROUTES_CONTESTS_QUESTIONS);
});