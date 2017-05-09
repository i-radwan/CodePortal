<?php
use App\Utilities\Constants;

Route::group(['middleware' => 'auth'], function () {
    //Blogs Routes
    Route::get('blogs/add', 'BlogController@addEditPost');
    Route::post('blogs/add', 'BlogController@addPost');

    Route::get('blogs/edit/post/{post}', 'BlogController@addEditPost')
        ->middleware(['can:owner-post,post']);
    Route::post('blogs/edit/post/{post}', 'BlogController@editPost')
        ->name(Constants::ROUTES_BLOGS_POST_EDIT)
        ->middleware(['can:owner-post,post']);

    Route::delete('blogs/delete/post/{post}', 'BlogController@deletePost')
        ->name(Constants::ROUTES_BLOGS_POST_DELETE)
        ->middleware(['can:owner-post,post']);

    Route::post('blogs/add/comment/{post}', 'BlogController@addComment');
    Route::post('blogs/edit/{comment}', 'BlogController@editComment')
        ->name(Constants::ROUTES_BLOGS_COMMENT_EDIT)
        ->middleware(['can:owner-comment,comment']);

    Route::delete('blogs/delete/comment/{comment}', 'BlogController@deleteComment')
        ->name(Constants::ROUTES_BLOGS_COMMENT_DELETE)
        ->middleware(['can:owner-comment,comment']);

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


