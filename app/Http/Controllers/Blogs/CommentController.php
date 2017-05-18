<?php

namespace App\Http\Controllers\Blogs;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Utilities\Constants;
use Illuminate\Http\Request;

class CommentController extends Controller
{

    /**
     * Add new comment to a post
     *
     * @param \Illuminate\Http\Request $request
     * @param  Post $post
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function add(Request $request, Post $post)
    {
        // Create the new comment
        $comment = new Comment($request->all());

        $comment->owner()->associate(\Auth::user());
        $comment->post()->associate($post);

        if ($request->has('parent_id'))
            $comment->parent()->associate($request->get(Constants::FLD_COMMENTS_PARENT_ID));

        if ($comment->save()) {
            // Return success message
            \Session::flash("messages", ["Comment Added Successfully"]);
            return redirect(route(Constants::ROUTES_BLOGS_POST_DISPLAY, $post[Constants::FLD_POSTS_ID]));
        }

        return back()->withErrors('Sorry, something went wrong!');
    }

    /**
     * Update comment
     *
     * @param Request $request
     * @param Comment $comment
     */
    public function update(Request $request, Comment $comment)
    {
        $comment[Constants::FLD_COMMENTS_BODY] = $request->get('body');

        $comment->save();
    }

    /**
     * Deletes a comment via Ajax Request
     *
     * @param Comment $comment
     */
    public function delete(Comment $comment)
    {
        $comment->delete();
    }

}
