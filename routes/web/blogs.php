<?php
use App\Utilities\Constants;

//Blogs Routes
Route::group(['middleware' => 'auth'], function () {

    // Create blog post view
    Route::get('blogs/create', 'Blogs\BlogController@create')
        ->name(Constants::ROUTES_BLOGS_POST_CREATE);

    // Edit blog post view
    Route::get('blogs/{post}/edit', 'Blogs\BlogController@edit')
        ->name(Constants::ROUTES_BLOGS_POST_EDIT)
        ->middleware(['canGateForUser:owner-post,post']);

    // Add new blog post
    Route::post('blogs', 'Blogs\BlogController@add')
        ->name(Constants::ROUTES_BLOGS_POST_STORE);

    // Add new comment
    Route::post('blogs/{post}/comment', 'Blogs\CommentController@add')
        ->name(Constants::ROUTES_BLOGS_COMMENT_STORE);

    // Update blog post
    Route::put('blogs/{post}', 'Blogs\BlogController@update')
        ->name(Constants::ROUTES_BLOGS_POST_UPDATE)
        ->middleware(['canGateForUser:owner-post,post']);

    // Update comment
    Route::post('comments/{comment}', 'Blogs\CommentController@update')
        ->name(Constants::ROUTES_BLOGS_COMMENT_UPDATE)
        ->middleware(['canGateForUser:owner-comment,comment']);

    // Up vote blog post
    Route::put('blogs/{post}/up_vote', 'Blogs\VoteController@upVotePost')
        ->name(Constants::ROUTES_BLOGS_UPVOTE);

    // Down vote blog post
    Route::put('blogs/{post}/down_vote', 'Blogs\VoteController@downVotePost')
        ->name(Constants::ROUTES_BLOGS_DOWNVOTE);

    // Up vote comment
    Route::put('comments/{comment}/up_vote', 'Blogs\VoteController@upVoteComment')
        ->name(Constants::ROUTES_BLOGS_COMMENT_UPVOTE);

    // Down vote comment
    Route::put('comments/{comment}/down_vote', 'Blogs\VoteController@downVoteComment')
        ->name(Constants::ROUTES_BLOGS_COMMENT_DOWNVOTE);

    // Delete blog post
    Route::delete('blogs/{post}', 'Blogs\BlogController@delete')
        ->name(Constants::ROUTES_BLOGS_POST_DELETE)
        ->middleware(['canGateForUser:owner-post,post']);

    // Delete comment
    Route::delete('comments/{comment}', 'Blogs\CommentController@delete')
        ->name(Constants::ROUTES_BLOGS_COMMENT_DELETE)
        ->middleware(['canGateForUser:owner-comment,comment']);
});

// Get all blog posts
Route::get('blogs', 'Blogs\BlogController@index')
    ->name(Constants::ROUTES_BLOGS_INDEX);

// Get certain post
Route::get('blogs/{post}', 'Blogs\BlogController@displayPost')
    ->name(Constants::ROUTES_BLOGS_POST_DISPLAY);
