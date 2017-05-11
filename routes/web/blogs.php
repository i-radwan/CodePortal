<?php
use App\Utilities\Constants;

//Blogs Routes
Route::group(['middleware' => 'auth'], function () {

    // Create blog post view
    Route::get('blogs/create', 'BlogController@addEditPost')
        ->name(Constants::ROUTES_BLOGS_POST_CREATE);

    // Edit blog post view
    Route::get('blogs/{post}/edit', 'BlogController@addEditPost')
        ->name(Constants::ROUTES_BLOGS_POST_EDIT)
        ->middleware(['canGateForUser:owner-post,post']);

    // Add new blog post
    Route::post('blogs', 'BlogController@addPost')
        ->name(Constants::ROUTES_BLOGS_POST_STORE);


    // Add new comment
    Route::post('blogs/{post}/comment', 'BlogController@addComment')
        ->name(Constants::ROUTES_BLOGS_COMMENT_STORE);

    // Update blog post
    Route::put('blogs/{post}', 'BlogController@editPost')
        ->name(Constants::ROUTES_BLOGS_POST_UPDATE)
        ->middleware(['canGateForUser:owner-post,post']);

    // Update comment
    Route::post('comments/{comment}', 'BlogController@editComment')
        ->name(Constants::ROUTES_BLOGS_COMMENT_UPDATE)
        ->middleware(['canGateForUser:owner-comment,comment']);

    // Upvote blog post
    Route::put('blogs/{post}/up_vote', 'VoteController@upVotePost')
        ->name(Constants::ROUTES_BLOGS_UPVOTE);

    // Downvote blog post
    Route::put('blogs/{post}/down_vote', 'VoteController@downVotePost')
        ->name(Constants::ROUTES_BLOGS_DOWNVOTE);

    // Upvote comment
    Route::put('comments/{comment}/up_vote', 'VoteController@upVoteComment')
        ->name(Constants::ROUTES_BLOGS_COMMENT_UPVOTE);

    // Downvote comment
    Route::put('comments/{comment}/down_vote', 'VoteController@downVoteComment')
        ->name(Constants::ROUTES_BLOGS_COMMENT_DOWNVOTE);

    // Delete blog post
    Route::delete('blogs/{post}', 'BlogController@deletePost')
        ->name(Constants::ROUTES_BLOGS_POST_DELETE)
        ->middleware(['canGateForUser:owner-post,post']);

    // Delete comment
    Route::delete('comments/{comment}', 'BlogController@deleteComment')
        ->name(Constants::ROUTES_BLOGS_COMMENT_DELETE)
        ->middleware(['canGateForUser:owner-comment,comment']);
});

// Get all blog posts
Route::get('blogs', 'BlogController@index')
    ->name(Constants::ROUTES_BLOGS_INDEX);

// Get certain post
Route::get('blogs/{post}', 'BlogController@displayPost')
    ->name(Constants::ROUTES_BLOGS_POST_DISPLAY);


