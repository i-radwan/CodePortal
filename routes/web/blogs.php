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
        ->middleware(['can:owner-post,post']);

    // Add new blog post
    Route::post('blogs', 'BlogController@addPost')
        ->name(Constants::ROUTES_BLOGS_POST_STORE);

    // Update blog post
    Route::post('blogs/{post}', 'BlogController@editPost')
        ->name(Constants::ROUTES_BLOGS_POST_UPDATE)
        ->middleware(['can:owner-post,post']);

    // Add new comment
    Route::post('blogs/{post}/comment', 'BlogController@addComment')
        ->name(Constants::ROUTES_BLOGS_COMMENT_STORE);

    // Update comment
    Route::post('comments/{comment}', 'BlogController@editComment')
        ->name(Constants::ROUTES_BLOGS_COMMENT_UPDATE)
        ->middleware(['can:owner-comment,comment']);

    // Delete blog post
    Route::delete('blogs/{post}', 'BlogController@deletePost')
        ->name(Constants::ROUTES_BLOGS_POST_DELETE)
        ->middleware(['can:owner-post,post']);

    // Delete comment
    Route::delete('comments/{comment}', 'BlogController@deleteComment')
        ->name(Constants::ROUTES_BLOGS_COMMENT_DELETE)
        ->middleware(['can:owner-comment,comment']);

    //Likes Routes
    Route::get('blogs/up_vote/entry/{post}', 'VoteController@upVotePost');
    Route::get('blogs/up_vote/comment/{comment}', 'VoteController@upVoteComment');
    Route::get('blogs/down_vote/entry/{post}', 'VoteController@downVotePost');
    Route::get('blogs/down_vote/comment/{comment}', 'VoteController@downVoteComment');
});

// Get all blog posts
Route::get('blogs', 'BlogController@index')
    ->name(Constants::ROUTES_BLOGS_INDEX);

// Get certain post
Route::get('blogs/{post}', 'BlogController@displayPost')
    ->name(Constants::ROUTES_BLOGS_POST_DISPLAY);


