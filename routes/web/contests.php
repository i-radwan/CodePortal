<?php

use App\Utilities\Constants;

// Contests routes...

// Index all contests
Route::get('contests', 'Contests\ContestController@index')
    ->name(Constants::ROUTES_CONTESTS_INDEX);

// Filters tags auto complete
Route::get('tags_auto_complete', 'Contests\ContestController@tagsAutoComplete')
    ->name(Constants::ROUTES_CONTESTS_TAGS_AUTO_COMPLETE);

Route::group(['middleware' => 'auth'], function () {

    // Create new contest page
    Route::get('contests/create', 'Contests\ContestController@create')
        ->name(Constants::ROUTES_CONTESTS_CREATE);

    // Store newly create contest route
    Route::post('contests', 'Contests\ContestController@store')
        ->name(Constants::ROUTES_CONTESTS_STORE);


    // Edit contest page
    Route::get('contests/{contest}/edit', 'Contests\ContestController@edit')
        ->name(Constants::ROUTES_CONTESTS_EDIT)
        ->middleware(['canGateForUser:owner-contest,contest']);

    // Update contest
    Route::post('contests/{contest}', 'Contests\ContestController@update')
        ->name(Constants::ROUTES_CONTESTS_UPDATE)
        ->middleware(['canGateForUser:owner-contest,contest']);

    // Save contest new order
    Route::put('contests/{contest}/reorder_problems', 'Contests\ContestController@reorderProblems')
        ->name(Constants::ROUTES_CONTESTS_REORDER_PROBLEMS)
        ->middleware(['canGateForUser:owner-contest,contest']);


    // Join contest
    Route::post('contests/{contest}/join', 'Contests\ContestController@joinContest')
        ->name(Constants::ROUTES_CONTESTS_JOIN)
        ->middleware(['contestAccessAuth:view-join-contest,contest']);

    // Leave contest
    Route::put('contests/{contest}/leave', 'Contests\ContestController@leaveContest')
        ->name(Constants::ROUTES_CONTESTS_LEAVE);

    // Remove participant
    Route::delete('contests/{contest}/remove/{user}', 'Contests\ContestController@removeParticipant')
        ->name(Constants::ROUTES_CONTESTS_PARTICIPANTS_DELETE)
        ->middleware(['canGateForUser:owner-organizer-contest,contest', 'canGateForUser:contest-participant,contest,user']);


    // Delete contest
    Route::delete('contests/{contest}', 'Contests\ContestController@destroy')
        ->name(Constants::ROUTES_CONTESTS_DELETE)
        ->middleware(['canGateForUser:owner-contest,contest']);




    // Add group contest
    Route::post('groups/{group}/contests', 'Contests\ContestController@addGroupContest')
        ->name(Constants::ROUTES_GROUPS_CONTEST_STORE)
        ->middleware(['canGateForUser:owner-admin-group,group']);



    // Organizers auto complete
    Route::get('contests/organisers_auto_complete', 'Contests\ContestController@usersAutoComplete')
        ->name(Constants::ROUTES_CONTESTS_ORGANIZERS_AUTO_COMPLETE);

    // Invitees auto complete
    Route::get('contests/invitees_auto_complete', 'Contests\ContestController@usersAutoComplete')
        ->name(Constants::ROUTES_CONTESTS_INVITEES_AUTO_COMPLETE);

    // Sync filters with server session
    Route::post('contests/create/contest_tags_judges_filters_sync', 'Contests\ContestController@applyProblemsFilters')
        ->name(Constants::ROUTES_CONTESTS_FILTERS_SYNC);

    // Detach filters from server session
    Route::post('contests/create/contest_tags_judges_filters_detach', 'Contests\ContestController@clearProblemsFilters')
        ->name(Constants::ROUTES_CONTESTS_FILTERS_DETACH);


    //
    // Question routes...
    //

    // Post new question
    Route::post('contests/{contest}/questions/store', 'Contests\QuestionController@askQuestion')
        ->name(Constants::ROUTES_CONTESTS_QUESTIONS_STORE)
        ->middleware('canGateForUser:contest-participant,contest');

    // Save question answer
    Route::post('contests/questions/answer', 'Contests\QuestionController@answerQuestion')
        ->name(Constants::ROUTES_CONTESTS_QUESTIONS_ANSWER_STORE);

    // Announce questions
    Route::put('contests/questions/{question}/announce', 'Contests\QuestionController@announceQuestion')
        ->name(Constants::ROUTES_CONTESTS_QUESTIONS_ANNOUNCE)
        ->middleware('canGateForUser:owner-organizer-contest-question,question');

    // Renounce question
    Route::put('contests/questions/{question}/renounce', 'Contests\QuestionController@renounceQuestion')
        ->name(Constants::ROUTES_CONTESTS_QUESTIONS_RENOUNCE)
        ->middleware('canGateForUser:owner-organizer-contest-question,question');
});

Route::group(['middleware' => 'contestAccessAuth:view-join-contest,contest'], function () {

    Route::get('contests/{contest}', 'Contests\ContestController@displayContestProblems')
        ->name(Constants::ROUTES_CONTESTS_DISPLAY);

    Route::get('contests/{contest}/problems', 'Contests\ContestController@displayContestProblems')
        ->name(Constants::ROUTES_CONTESTS_PROBLEMS);

    Route::get('contests/{contest}/standings', 'Contests\ContestController@displayContestStandings')
        ->name(Constants::ROUTES_CONTESTS_STANDINGS);

    Route::get('contests/{contest}/status', 'Contests\ContestController@displayContestStatus')
        ->name(Constants::ROUTES_CONTESTS_STATUS);

    Route::get('contests/{contest}/participants', 'Contests\ContestController@displayContestParticipants')
        ->name(Constants::ROUTES_CONTESTS_PARTICIPANTS);

    Route::get('contests/{contest}/questions', 'Contests\QuestionController@displayContestQuestions')
        ->name(Constants::ROUTES_CONTESTS_QUESTIONS);
});