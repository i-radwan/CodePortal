<?php

Route::group(['middleware' => 'auth'], function () {
    Route::get('teams/create', 'TeamController@create');
    Route::get('teams/{team}/edit', 'TeamController@edit')->middleware(['can:member-team,team']);
    Route::get('teams/{team}/invitees_auto_complete', 'TeamController@usersAutoComplete')->middleware(['can:member-team,team']);
    Route::post('teams', 'TeamController@store');
    Route::post('teams/{team}', 'TeamController@update')->middleware(['can:member-team,team']);
    Route::post('teams/{team}/invite', 'TeamController@inviteMember')->middleware(['can:member-team,team']);
    // TODO: check that the user we want to remove is really a team member
    Route::delete('teams/{team}/remove/{user}', 'TeamController@removeMember')->middleware(['can:member-team,team']);
    Route::delete('teams/{team}/invitations/cancel/{user}', 'TeamController@cancelInvitation')->middleware(['can:member-team,team']);
    Route::put('teams/{team}/invitations/accept', 'TeamController@acceptInvitation')->middleware(['can:invitee-team,team']);
    Route::put('teams/{team}/invitations/reject', 'TeamController@rejectInvitation')->middleware(['can:invitee-team,team']);
    Route::delete('teams/{team}', 'TeamController@destroy')->middleware(['can:member-team,team']);
});