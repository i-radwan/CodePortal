<?php

use App\Utilities\Constants;

// Contest routes...

// Index all contests
Route::get('contests', 'Contest\ContestController@index')
    ->name(Constants::ROUTES_CONTESTS_INDEX);

// Filters tags auto complete
Route::get('tags_auto_complete', 'Contest\ContestController@tagsAutoComplete')
    ->name(Constants::ROUTES_CONTESTS_TAGS_AUTO_COMPLETE);

Route::group(['middleware' => 'auth'], function () {

    // Create new contest page
    Route::get('contests/create', 'Contest\ContestController@create')
        ->name(Constants::ROUTES_CONTESTS_CREATE);

    // Store newly create contest route
    Route::post('contests', 'Contest\ContestController@store')
        ->name(Constants::ROUTES_CONTESTS_STORE);



    // Edit contest view
    Route::get('contests/{contest}/edit', 'Contest\ContestController@editContestView')
        ->name(Constants::ROUTES_CONTESTS_EDIT)
        ->middleware(['canGateForUser:owner-contest,contest']);

    // Organizers auto complete
    Route::get('contests/organisers_auto_complete', 'Contest\ContestController@usersAutoComplete')
        ->name(Constants::ROUTES_CONTESTS_ORGANIZERS_AUTO_COMPLETE);

    // Invitees auto complete
    Route::get('contests/invitees_auto_complete', 'Contest\ContestController@usersAutoComplete')
        ->name(Constants::ROUTES_CONTESTS_INVITEES_AUTO_COMPLETE);

    // Add group contest
    Route::post('groups/{group}/contests', 'Contest\ContestController@addGroupContest')
        ->name(Constants::ROUTES_GROUPS_CONTEST_STORE)
        ->middleware(['canGateForUser:owner-admin-group,group']);

    // Sync filters with server session
    Route::post('contests/create/contest_tags_judges_filters_sync', 'Contest\ContestController@applyProblemsFilters')
        ->name(Constants::ROUTES_CONTESTS_FILTERS_SYNC);

    // Detach filters from server session
    Route::post('contests/create/contest_tags_judges_filters_detach', 'Contest\ContestController@clearProblemsFilters')
        ->name(Constants::ROUTES_CONTESTS_FILTERS_DETACH);

    // Update contest
    Route::post('contests/{contest}', 'Contest\ContestController@editContest')
        ->name(Constants::ROUTES_CONTESTS_UPDATE)
        ->middleware(['canGateForUser:owner-contest,contest']);

    // Join contest
    Route::post('contests/{contest}/join', 'Contest\ContestController@joinContest')
        ->name(Constants::ROUTES_CONTESTS_JOIN)
        ->middleware(['contestAccessAuth:view-join-contest,contest']);

    // Save contest new order
    Route::put('contests/{contest}/reorder', 'Contest\ContestController@reorderContest')
        ->name(Constants::ROUTES_CONTESTS_REORDER)
        ->middleware(['canGateForUser:owner-contest,contest']);

    // Leave contest
    Route::put('contests/{contest}/leave', 'Contest\ContestController@leaveContest')
        ->name(Constants::ROUTES_CONTESTS_LEAVE);

    // Remove participant
    Route::delete('contests/{contest}/remove/{user}', 'Contest\ContestController@removeParticipant')
        ->name(Constants::ROUTES_CONTESTS_PARTICIPANTS_DELETE)
        ->middleware(['canGateForUser:owner-organizer-contest,contest', 'canGateForUser:contest-participant,contest,user']);

    // Delete contest
    Route::delete('contests/{contest}/delete', 'Contest\ContestController@deleteContest')
        ->name(Constants::ROUTES_CONTESTS_DELETE)
        ->middleware(['canGateForUser:owner-contest,contest']);;

    //
    // Question routes...
    //
    // Announce questions
    Route::put('contests/{question}/announce', 'Contest\QuestionController@announceQuestion')
        ->name(Constants::ROUTES_CONTESTS_QUESTIONS_ANNOUNCE);

    // Renounce question
    Route::put('contests/{question}/renounce', 'Contest\QuestionController@renounceQuestion')
        ->name(Constants::ROUTES_CONTESTS_QUESTIONS_RENOUNCE);

    // Save question answer
    Route::post('contests/question/answer', 'Contest\QuestionController@answerQuestion')
        ->name(Constants::ROUTES_CONTESTS_QUESTIONS_ANSWER_STORE);

    // Post new question
    Route::post('contests/question/{contestID}', 'Contest\QuestionController@askQuestion')
        ->name(Constants::ROUTES_CONTESTS_QUESTIONS_STORE);
});

Route::group(['middleware' => 'contestAccessAuth:view-join-contest,contest'], function () {

    // Get contest views
    Route::get('contests/{contest}', 'Contest\ContestController@displayContestProblems')
        ->name(Constants::ROUTES_CONTESTS_DISPLAY);

    Route::get('contests/{contest}/problems', 'Contest\ContestController@displayContestProblems')
        ->name(Constants::ROUTES_CONTESTS_PROBLEMS);

    Route::get('contests/{contest}/standings', 'Contest\ContestController@displayContestStandings')
        ->name(Constants::ROUTES_CONTESTS_STANDINGS);

    Route::get('contests/{contest}/status', 'Contest\ContestController@displayContestStatus')
        ->name(Constants::ROUTES_CONTESTS_STATUS);

    Route::get('contests/{contest}/participants', 'Contest\ContestController@displayContestParticipants')
        ->name(Constants::ROUTES_CONTESTS_PARTICIPANTS);

    Route::get('contests/{contest}/questions', 'Contest\ContestController@displayContestQuestions')
        ->name(Constants::ROUTES_CONTESTS_QUESTIONS);
});