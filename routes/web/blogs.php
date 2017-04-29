<?php

Route::group(['middleware' => 'auth'], function () {
    //Blogs Routes
    Route::get('blogs/add', 'BlogController@addEditPost');
    Route::post('blogs/add', 'BlogController@addPost');

    Route::get('blogs/edit/post/{post}', 'BlogController@addEditPost');
    Route::post('blogs/edit/post/{post}', 'BlogController@editPost');


    Route::delete('blogs/delete/post/{post}', 'BlogController@deletePost');

    Route::post('blogs/add/comment/{post}', 'BlogController@addComment');
    Route::post('blogs/edit/comment/{post}', 'BlogController@editComment');
    Route::delete('blogs/delete/comment/{post}', 'BlogController@deleteComment');

    //Likes Routes
    Route::get('blogs/up_vote/entry/{post}', 'VoteController@upVotePost');
    Route::get('blogs/up_vote/comment/{comment}', 'VoteController@upVoteComment');
    Route::get('blogs/down_vote/entry/{post}', 'VoteController@downVotePost');
    Route::get('blogs/down_vote/comment/{comment}', 'VoteController@downVoteComment');
});

// Blogs routes...
Route::get('blogs', 'BlogController@index');
Route::get('blogs/entries/{user}', 'BlogController@displayUserPosts'); // i should call a different name
Route::get('blogs/entry/{post}', 'BlogController@displayPost');


