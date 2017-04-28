<?php

Route::group(['middleware' => 'auth'], function () {
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

// Blogs routes...
Route::get('blogs', 'BlogController@index');
Route::get('blogs/entries/{user}', 'BlogController@displayUserPosts');
Route::get('blogs/entry/{post}', 'BlogController@displayPost');
