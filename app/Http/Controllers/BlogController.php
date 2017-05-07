<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Utilities\Constants;
use Illuminate\Http\Request;
use Auth;
use Session;
use DB;


class BlogController extends Controller
{
    /**
     * Show the blogs page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        //Getting Posts
        $posts = Post::ofContent(request('q'))->orderBy(Constants::FLD_POSTS_CREATED_AT, 'desc')->paginate(7);
        $index = 0;
        foreach ($posts as $post) {
            $posts[$index++] = $this->getPostInfo($post, true);
        }
        return view('blogs.index')
            ->with('posts', $posts)
            ->with('q', request('q'))
            ->with('topContributors', $this->getTopContributors())
            ->with('post_like_url', url("/blogs/up_vote/entry"))
            ->with('post_unlike_url', url("blogs/down_vote/entry"))
            ->with('comment_like_url', url("blogs/up_vote/comment"))
            ->with('comment_unlike_url', url("blogs/down_vote/comment"))
            ->with('pageTitle', config('app.name') . ' | Blogs');

    }

    /**
     * Shows a certain post page
     * @param $post
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function displayPost($post)
    {

        //Get Current Signed In User
        $user = Auth::user();
        //Get Post
        $Post = Post::find($post);
        //Get Post Info
        $postInfo = $this->getPostInfo($Post);
        //Get Comments Of This Post
        $comments = $this->getPostComments($Post);
        //Return View
        return view("blogs.post")
            ->with('post', $postInfo)
            ->with('comments', $comments)
            ->with('post_like_url', url("/blogs/up_vote/entry"))
            ->with('post_unlike_url', url("blogs/down_vote/entry"))
            ->with('comment_like_url', url("blogs/up_vote/comment"))
            ->with('comment_unlike_url', url("blogs/down_vote/comment"))
            ->with('comment_form_url', url('blogs/add/comment/' . $post))
            ->with('pageTitle', config('app.name') . ' | ' . $Post[Constants::FLD_POSTS_TITLE]);
    }

    /**
     * Shows certain user posts
     * @param $user
     */
    public function displayUserPosts($user)
    {
        dd($user);
    }

    /**
     * Shows Add/Edit Post Page
     * @param $post the post id used in editing a saved post
     * @return \Illuminate\View\View
     */
    public function addEditPost($post = null)
    {
        if ($post) {
            $Post = Post::find($post);
            return view("blogs.add_edit")
                ->with('postID', $post)
                ->with('postTitle', $Post[Constants::FLD_POSTS_TITLE])
                ->with('postBody', $Post[Constants::FLD_POSTS_BODY])
                ->with('pageTitle', config('app.name') . ' | ' . 'Edit Post');
        } else {
            return view("blogs.add_edit")
                ->with('pageTitle', config('app.name') . ' | ' . 'Add Post');
        }
    }

    /**
     * Add new Post
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addPost(Request $request)
    {
        //Create new Post
        $post = new Post($request->all());
        //Add the owner_id
        $post->owner()->associate(Auth::user());
        //Verify Saving
        if ($post->save()) {
            // Return success message
            Session::flash("messages", ["Post Added Successfully"]);
            return redirect()->action(
                'BlogController@displayPost', ['id' => $post[Constants::FLD_POSTS_ID]]
            );
        } else {    // return error message
            Session::flash("messages", ["Sorry, Post was not added. Please retry later"]);
            return redirect()->action('BlogController@index');
        }
    }

    /**
     * Edit a Post
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Post $post
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editPost(Request $request, Post $post)
    {
            //Update Post
            $post->update($request->all());
            //Flash Success Message
            Session::flash('messages', ['Your post was edited successfully']);
            return redirect()->action('BlogController@displayPost', ['id' => $post[Constants::FLD_POSTS_ID]]);
    }

    public function deletePost(Request $request, Post $post)
    {
            $post->delete();
            //Flash Success Message
            Session::flash('messages', ['Your post was deleted successfully']);
            return redirect()->action('BlogController@index');
    }

    /**
     * Add new comment to a post
     * @param \Illuminate\Http\Request $request
     * @param  $post
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addComment(Request $request, $post)
    {
        //Check if the parent comment has null parents "To Avoid Multiple Levels)

            $comment = new Comment($request->all());
            $comment->owner()->associate(Auth::user());
            $comment->post()->associate($request->get(Constants::FLD_COMMENTS_POST_ID));
            if ($request->has('parent_id'))
                $comment->parent()->associate($request->get(Constants::FLD_COMMENTS_PARENT_ID));

            if ($comment->save()) {
                // Return success message
                Session::flash("messages", ["Comment Added Successfully"]);
                return redirect()->action(
                    'BlogController@displayPost', ['id' => $post]
                );
            } else {    // return error message
                Session::flash("messages", ["Sorry, Comment was not added. Please retry later"]);
                return redirect()->action(
                    'BlogController@displayPost', ['id' => $post]
                );
            }

    }

    /**
     * Edit comment
     * @param Request $request
     */
    public function editComment(Request $request)
    {
        $comment = Comment::find($request->get('comment_id'));
        $comment[Constants::FLD_COMMENTS_BODY] = $request->get('body');
        $comment->save();
    }

    /**
     * Deletes a comment via Ajax Request
     * @param \Illuminate\Http\Request $request
     */
    public function deleteComment(Request $request)
    {
        $comment = Comment::find($request['comment_id']);
        //Check if the current user is the owner of the comment to be deleted
        if (Auth::user()[Constants::FLD_USERS_ID] == $comment['owner'][Constants::FLD_USERS_ID]) {
            $comment->delete();
        }
    }


    /**
     * Get Single post info
     * @param Post &$Post the current Post model
     * @param bool $minimal if you want to return part of text or not
     * @return array
     */
    public function getPostInfo(&$Post, $minimal = false)
    {
        $postInfo = [];
        //Get Post title
        $postInfo[Constants::FLD_POSTS_TITLE] = $Post[Constants::FLD_POSTS_TITLE];
        //Get Post id
        $postInfo[Constants::FLD_POSTS_ID] = $Post[Constants::FLD_POSTS_ID];
        //Get Post Full Body if minimal is false and part of the body string when minimal is true, the previous case is
        //used in the index page
        if ($minimal)
            $postInfo[Constants::FLD_POSTS_BODY] = substr($Post[Constants::FLD_POSTS_BODY], 0, 100);
        else
            $postInfo[Constants::FLD_POSTS_BODY] = $Post[Constants::FLD_POSTS_BODY];
        //Get Post UP Votes
        $postInfo[Constants::FLD_POSTS_UP_VOTES] = $Post['upVotes']->count();
        //Get Post Down Votes
        $postInfo[Constants::FLD_POSTS_DOWN_VOTES] = $Post['downVotes']->count();
        //Get TimeStamp
        $postInfo[Constants::FLD_COMMENTS_CREATED_AT] = $Post[Constants::FLD_POSTS_CREATED_AT];
        //Get Post Owner user name
        $postInfo["username"] = $Post['owner'][Constants::FLD_USERS_USERNAME];
        //if there is a user signed in display his votes
        //Get Current User
        $user = Auth::user();
        if ($user) {
            //1 means he voted Up 0 means Voted Down -1 means no votes
            $postInfo["user_vote"] = ($Post->isUpVoted()) ? 1 : ($Post->isDownVoted() ? 0 : -1);

        }
        //Add If the current user is the Owner or not
        $postInfo["isOwner"] = ($user[Constants::FLD_USERS_ID] == $Post[Constants::FLD_POSTS_OWNER_ID]);
        //Return Post Info
        return $postInfo;
    }

    /**
     * * Get the Post Comments "till now the first two levels"
     * @param Post &$Post the current Post model
     * @return mixed
     */
    public function getPostComments(&$Post)
    {
        $postComments = [];
        //First Level Comment
        $comments = $Post[Constants::TBL_COMMENTS];
        //Second level Comments(Replies)
        $index = 0;
        foreach ($comments as $comment) {
            $postComments[$index] = $this->getCommentInfo($comment);
            //Get Comment Replies (2nd Level Only)
            //The result array
            $commentReplies = [];
            //the replies to the current Comment
            $replies = $comment->replies;
            //index For 2nd Level
            $index2 = 0;
            foreach ($replies as $reply) {
                $commentReplies[$index2++] = $this->getCommentInfo($reply);
            }
            //put the replies to the comment in the minimal form
            $postComments[$index]['replies'] = $commentReplies;
            $index++;
        }
        return $postComments;
    }

    /**
     * Get Single Comment info
     * @param $Comment
     *
     * @return array
     */
    public function getCommentInfo(&$Comment)
    {
        $commentInfo = [];

        //Get Comment title
        $commentInfo[Constants::FLD_COMMENTS_TITLE] = $Comment[Constants::FLD_COMMENTS_TITLE];
        //Get Comment ID
        $commentInfo[Constants::FLD_COMMENTS_ID] = $Comment[Constants::FLD_COMMENTS_ID];
        //Get Comment Body
        $commentInfo[Constants::FLD_COMMENTS_BODY] = $Comment[Constants::FLD_COMMENTS_BODY];
        //Get Comment Timestamp
        $commentInfo[Constants::FLD_COMMENTS_CREATED_AT] = $Comment[Constants::FLD_COMMENTS_CREATED_AT];
        //Get Comment Down Votes
        $commentInfo[Constants::FLD_COMMENTS_DOWN_VOTES] = $Comment['downVotes']->count();
        //Get Comment Up Votes
        $commentInfo[Constants::FLD_COMMENTS_UP_VOTES] = $Comment['upVotes']->count();
        //Get Comment owner user name
        $commentInfo["username"] = $Comment['owner'][Constants::FLD_USERS_USERNAME];
        //If there is a user signed in display hos votes
        //Get the Current User
        $user = Auth::user();
        if ($user) {
            //1 means he voted Up 0 means Voted Down -1 means no votes
            $commentInfo["user_vote"] = ($Comment->isUpVoted()) ? 1 : ($Comment->isDownVoted() ? 0 : -1);
        }
        //Add If the current user is the Owner or not
        $commentInfo["isOwner"] = ($Comment[Constants::FLD_COMMENTS_USER_ID] == $user[Constants::FLD_USERS_ID]);
        //Return Comment Info
        return $commentInfo;
    }

    /*
     * Get Top Contributors
     * @return mixed
     */
    public function getTopContributors()
    {
        return $users = Post::select(DB::raw('count(*) as contributions ,' . Constants::TBL_USERS . '.' . Constants::FLD_USERS_USERNAME))
            ->join(Constants::TBL_USERS, Constants::TBL_USERS . '.' . Constants::FLD_USERS_ID, '=', Constants::TBL_POSTS . '.' . Constants::FLD_POSTS_OWNER_ID)
            ->groupby(Constants::FLD_POSTS_OWNER_ID)
            ->orderby('contributions', 'desc')->pluck("contributions", Constants::FLD_USERS_USERNAME);
    }

}
