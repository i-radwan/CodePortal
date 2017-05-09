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
        // Getting posts
        $posts = Post::ofContent(request('q'))
            ->paginate(Constants::POSTS_COUNT_PER_PAGE);

        return view('blogs.index')
            ->with('posts', $posts)
            ->with('q', request('q'))
            ->with('topContributors', $this->getTopContributors())
            ->with('postUpVoteURL', url("/blogs/up_vote/entry"))
            ->with('postDownVoteURL', url("blogs/down_vote/entry"))
            ->with('pageTitle', config('app.name') . ' | Blogs');
    }

    /**
     * Shows a certain post page
     *
     * @param Post $post
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function displayPost(Post $post)
    {
        return view("blogs.post")
            ->with('post', $post)
            ->with('comments', $post->comments())
            ->with('topContributors', $this->getTopContributors())
            ->with('postUpVoteURL', url("/blogs/up_vote/entry"))
            ->with('postDownVoteURL', url("blogs/down_vote/entry"))
            ->with('commentUpVoteURL', url("blogs/up_vote/comment"))
            ->with('commentDownVoteURL', url("blogs/down_vote/comment"))
            ->with('commentFormURL', url('blogs/add/comment/' . $post[Constants::FLD_POSTS_ID]))
            ->with('pageTitle', config('app.name') . ' | ' . $post[Constants::FLD_POSTS_TITLE]);
    }

    /**
     * Shows certain user posts
     *
     * ToDo finish this function
     *
     * @param $user
     */
    public function displayUserPosts($user)
    {
        dd($user);
    }

    /**
     * Shows add/edit post page
     *
     * @param Post $post
     * @return \Illuminate\View\View
     */
    public function addEditPost(Post $post = null)
    {
        if ($post) {
            return view("blogs.add_edit")
                ->with('post', $post)
                ->with('pageTitle', config('app.name') . ' | ' . $post[Constants::FLD_POSTS_TITLE]);
        } else {
            return view("blogs.add_edit")
                ->with('pageTitle', config('app.name') . ' | Add Post');
        }
    }

    /**
     * Add new Post to db
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addPost(Request $request)
    {
        // Create new Post
        $post = new Post($request->all());

        // Add the owner_id
        $post->owner()->associate(Auth::user());

        // Verify Saving
        if ($post->save()) {

            // Return success message
            Session::flash("messages", ["Post added successfully!"]);
            return redirect()->action(
                'BlogController@displayPost', [Constants::FLD_POSTS_ID => $post[Constants::FLD_POSTS_ID]]);

        } else { // return error message

            Session::flash("messages", ["Sorry, post was not added. Please retry later!"]);
            return redirect()->action('BlogController@index');
        }
    }

    /**
     * Edit a Post
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Post $post
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editPost(Request $request, Post $post)
    {
        // Update Post
        $post->update($request->all());

        // Flash Success Message
        Session::flash('messages', ['Your post was edited successfully']);
        return redirect()->action('BlogController@displayPost', [Constants::FLD_POSTS_ID => $post[Constants::FLD_POSTS_ID]]);
    }

    /**
     * Delete post
     *
     * @param Post $post
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deletePost(Post $post)
    {
        $post->delete();

        // Flash Success Message
        Session::flash('messages', ['Your post has been deleted successfully!']);
        return redirect()->action('BlogController@index');
    }

    /**
     * Add new comment to a post
     *
     * @param \Illuminate\Http\Request $request
     * @param  Post $post
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addComment(Request $request, Post $post)
    {
        // Create the new comment
        $comment = new Comment($request->all());

        $comment->owner()->associate(Auth::user());
        $comment->post()->associate($post);

        if ($request->has('parent_id'))
            $comment->parent()->associate($request->get(Constants::FLD_COMMENTS_PARENT_ID));

        if ($comment->save()) {

            // Return success message
            Session::flash("messages", ["Comment Added Successfully"]);
            return redirect(url('blogs/entry/' . $post[Constants::FLD_POSTS_ID]));
        } else {    // return error message
            Session::flash("messages", ["Sorry, Comment was not added. Please retry later"]);
            return redirect(url('blogs/entry/' . $post[Constants::FLD_POSTS_ID]));
        }
        return back()->withErrors('Sorry, something went wrong!');
    }

    /**
     * Edit comment
     *
     * @param Request $request
     * @param Comment $comment
     */
    public function editComment(Request $request, Comment $comment)
    {
        $comment[Constants::FLD_COMMENTS_BODY] = $request->get('body');

        $comment->save();
    }

    /**
     * Deletes a comment via Ajax Request
     *
     * @param Comment $comment
     */
    public function deleteComment(Comment $comment)
    {
        $comment->delete();
    }
//
//    /**
//     * Get Single post info
//     *
//     * @param Post &$Post the current Post model
//     * @param bool $minimal if you want to return part of text or not
//     * @return array
//     */
//    public function getPostInfo(&$Post, $minimal = false)
//    {
//        $postInfo = [];
//        //Get Post title
//        $postInfo[Constants::FLD_POSTS_TITLE] = $Post[Constants::FLD_POSTS_TITLE];
//        //Get Post id
//        $postInfo[Constants::FLD_POSTS_ID] = $Post[Constants::FLD_POSTS_ID];
//        //Get Post Full Body if minimal is false and part of the body string when minimal is true, the previous case is
//        //used in the index page
//        if ($minimal)
//            $postInfo[Constants::FLD_POSTS_BODY] = substr($Post[Constants::FLD_POSTS_BODY], 0, 100);
//        else
//            $postInfo[Constants::FLD_POSTS_BODY] = $Post[Constants::FLD_POSTS_BODY];
//        //Get Post UP Votes
//        $postInfo[Constants::FLD_POSTS_UP_VOTES] = $Post['upVotes']->count();
//        //Get Post Down Votes
//        $postInfo[Constants::FLD_POSTS_DOWN_VOTES] = $Post['downVotes']->count();
//        //Get TimeStamp
//        $postInfo[Constants::FLD_COMMENTS_CREATED_AT] = $Post[Constants::FLD_POSTS_CREATED_AT];
//        //Get Post Owner user name
//        $postInfo["username"] = $Post['owner'][Constants::FLD_USERS_USERNAME];
//        //if there is a user signed in display his votes
//        //Get Current User
//        $user = Auth::user();
//        if ($user) {
//            //1 means he voted Up 0 means Voted Down -1 means no votes
//            $postInfo["user_vote"] = ($Post->isUpVoted()) ? 1 : ($Post->isDownVoted() ? 0 : -1);
//
//        }
//        //Add If the current user is the Owner or not
//        $postInfo["isOwner"] = ($user[Constants::FLD_USERS_ID] == $Post[Constants::FLD_POSTS_OWNER_ID]);
//        //Return Post Info
//        return $postInfo;
//    }
//
//    /**
//     * Get the Post Comments "till now the first two levels"
//     *
//     * @param Post $post the current post model
//     * @return mixed
//     */
//    public function getPostComments($post)
//    {
//        $postComments = [];
//
//        // First Level Comment
//        $comments = $post->comments();
//
//        // Second level Comments(Replies)
//
//        $index = 0;
//        foreach ($comments as $comment) {
//            $postComments[$index] = $this->getCommentInfo($comment);
//            //Get Comment Replies (2nd Level Only)
//            //The result array
//            $commentReplies = [];
//            //the replies to the current Comment
//            $replies = $comment->replies;
//            //index For 2nd Level
//            $index2 = 0;
//            foreach ($replies as $reply) {
//                $commentReplies[$index2++] = $this->getCommentInfo($reply);
//            }
//            //put the replies to the comment in the minimal form
//            $postComments[$index]['replies'] = $commentReplies;
//            $index++;
//        }
//        return $postComments;
//    }
//
//    /**
//     * Get Single Comment info
//     * @param $Comment
//     *
//     * @return array
//     */
//    public function getCommentInfo(&$Comment)
//    {
//        $commentInfo = [];
//
//        //Get Comment title
//        $commentInfo[Constants::FLD_COMMENTS_TITLE] = $Comment[Constants::FLD_COMMENTS_TITLE];
//        //Get Comment ID
//        $commentInfo[Constants::FLD_COMMENTS_ID] = $Comment[Constants::FLD_COMMENTS_ID];
//        //Get Comment Body
//        $commentInfo[Constants::FLD_COMMENTS_BODY] = $Comment[Constants::FLD_COMMENTS_BODY];
//        //Get Comment Timestamp
//        $commentInfo[Constants::FLD_COMMENTS_CREATED_AT] = $Comment[Constants::FLD_COMMENTS_CREATED_AT];
//        //Get Comment Down Votes
//        $commentInfo[Constants::FLD_COMMENTS_DOWN_VOTES] = $Comment['downVotes']->count();
//        //Get Comment Up Votes
//        $commentInfo[Constants::FLD_COMMENTS_UP_VOTES] = $Comment['upVotes']->count();
//        //Get Comment owner user name
//        $commentInfo["username"] = $Comment['owner'][Constants::FLD_USERS_USERNAME];
//        //If there is a user signed in display hos votes
//        //Get the Current User
//        $user = Auth::user();
//        if ($user) {
//            //1 means he voted Up 0 means Voted Down -1 means no votes
//            $commentInfo["user_vote"] = ($Comment->isUpVoted()) ? 1 : ($Comment->isDownVoted() ? 0 : -1);
//        }
//        //Add If the current user is the Owner or not
//        $commentInfo["isOwner"] = ($Comment[Constants::FLD_COMMENTS_USER_ID] == $user[Constants::FLD_USERS_ID]);
//        //Return Comment Info
//        return $commentInfo;
//    }

    /**
     * Get top contributors
     *
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
