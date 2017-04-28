<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Utilities\Constants;
use Illuminate\Http\Request;
use Auth;
use Session;

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
        $posts = Post::orderBy(Constants::FLD_POSTS_CREATED_AT, 'desc')->paginate(7);
        $index = 0;
        foreach ($posts as $post){
            $posts[$index++] = $this->getPostInfo($post, true);
        }
        return view('blogs.index')
            ->with('posts', $posts )
            ->with('topContributors', [])
            ->with('post_like_url', url("/blogs/up_vote/entry"))
            ->with('post_unlike_url', url("blogs/down_vote/entry"))
            ->with('comment_like_url', url("blogs/up_vote/comment"))
            ->with('comment_unlike_url', url("blogs/down_vote/comment"))
            ->with('pageTitle', config('app.name'). ' | Blogs');

    }

    /**
     * Shows a certain post page
     * @param $post
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function displayPost($post){

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
            ->with('post',$postInfo)
            ->with('comments', $comments)
            ->with('post_like_url', url("/blogs/up_vote/entry"))
            ->with('post_unlike_url', url("blogs/down_vote/entry"))
            ->with('comment_like_url', url("blogs/up_vote/comment"))
            ->with('comment_unlike_url', url("blogs/down_vote/comment"))
            ->with('comment_form_url', url('blogs/entry/'. $post))
            ->with('pageTitle', config('app.name'). ' |'.$Post[Constants::FLD_POSTS_TITLE]);
    }

    /**
     * Shows certain user posts
     * @param $user
     */
    public function displayUserPosts($user){
        dd($user);
    }

    /**
     * Shows Add/Edit Post Page
     * @param $post the post id used in editing a saved post

     * @return \Illuminate\View\View
     */
    public function addEditPost($post = null){
        if($post){
            $Post = Post::find($post);
            return view("blogs.add_edit")
                ->with('postID', $post)
                ->with('postTitle', $Post[Constants::FLD_POSTS_TITLE])
                ->with('postBody', $Post[Constants::FLD_POSTS_BODY])
                ->with('pageTitle', config('app.name') . ' |' . 'Edit Post');
        }else {
            return view("blogs.add_edit")
                ->with('pageTitle', config('app.name') . ' |' . 'Add Post');
        }
    }

    /**
     * Add new Post
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addPost(Request $request){
        //Create new Post
        $post = new Post($request->all());
        //Add the owner_id
        $post->owner()->associate(Auth::user());
        //Verify Saving
        if( $post->save()){
            // Return success message
            Session::flash("messages", ["Post Added Successfully"]);
            return redirect()->action(
                'BlogController@displayPost', ['id' => $post[Constants::FLD_POSTS_ID]]
            );
        }
        else {    // return error message
            Session::flash("messages", ["Sorry, Post was not added. Please retry later"]);
            return redirect()->action('BlogController@index');
        }
    }

    /**
     * Edit a Post
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Post         $post
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editPost(Request $request,Post $post){
        //Get The Current User
        $user = Auth::user();
        //Check Validation
        if( $user == $post['owner']) {
            //Update Post
            $post->update($request->all());
            //Flash Success Message
            Session::flash('messages', ['Your post was edited successfully']);
            return redirect()->action('BlogController@displayPost', ['id' => $post[Constants::FLD_POSTS_ID]]);
        }
    }

    public function deletePost(Request $request, Post $post){
        //Check For Validation
        if( Auth::user() == $post['owner']) {
            $post->delete();
            //Flash Success Message
            Session::flash('messages', ['Your post was deleted successfully']);
            return redirect()->action('BlogController@index');
        }
    }

    public function editComment(Request $request){
        dd("edit comment", $request);
    }

    /**
     * Add new comment to a post
     * @param \Illuminate\Http\Request $request
     * @param  $post
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addComment(Request $request, $post){
        $comment = new Comment($request->all());
        $comment->owner()->associate(Auth::user());
        if( $comment->save()){
            // Return success message
            Session::flash("messages", ["Comment Added Successfully"]);
            return redirect()->action(
                'BlogController@displayPost', ['id' => $post]
            );
        }
        else {    // return error message
            Session::flash("messages", ["Sorry, Comment was not added. Please retry later"]);
            return redirect()->action(
                'BlogController@displayPost', ['id' => $post]
            );
        }
    }

    /**
     * Get Single post info
     * @param Post &$Post the current Post model
     * @param bool $minimal if you want to return part of text or not
     * @return array
     */
    public function getPostInfo(&$Post, $minimal = false){
        $postInfo = [];
        //Get Post title
        $postInfo[Constants::FLD_POSTS_TITLE] = $Post[Constants::FLD_POSTS_TITLE];
        //Get Post id
        $postInfo[Constants::FLD_POSTS_ID] = $Post[Constants::FLD_POSTS_ID];
        //Get Post Full Body if minimal is false and part of the body string when minimal is true, the previous case is
        //used in the index page
        if($minimal)
            $postInfo[Constants::FLD_POSTS_BODY] = substr($Post[Constants::FLD_POSTS_BODY],0,100);
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
        if($user){
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
     * (ToDo @ Samir Add More depth to the comments replies "recursive Query using Baum)
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
            foreach ($replies as $reply){
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
        $commentInfo[Constants::FLD_COMMENTS_COMMENT_ID] = $Comment[Constants::FLD_COMMENTS_COMMENT_ID];
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
        //if there is a user signed in display hos votes
        if($user = Auth::user()){
            //1 means he voted Up 0 means Voted Down -1 means no votes
            $commentInfo["user_vote"] = ($Comment->isUpVoted()) ? 1 : ($Comment->isDownVoted() ? 0 : -1);
        }

        return $commentInfo;
    }

}
