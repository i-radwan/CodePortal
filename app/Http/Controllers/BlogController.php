<?php

namespace App\Http\Controllers;

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
            $posts[$index++] = $this->getPostInfo($post);
        }
        return view('blogs.index')->with('pageTitle', config('app.name'). ' | Blogs')->with('posts', $posts );
    }

    /**
     * Shows a certain post page
     * @param $post
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function displayPost($post){
        //Get Post Info
        $Post = Post::find($post);
        $postInfo = $this->getPostInfo($Post);
        $comments = $this->getPostComments($Post);

        return view("blogs.post")
            ->with('post',$postInfo)
            ->with('comments', $comments)
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
     * @return $this
     */
    public function addEditPost(){
        return view("blogs.add_edit")
            ->with('pageTitle', config('app.name'). ' |'. 'Add Post');
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
                'BlogController@displayPost', ['id' => $post[Constants::FLD_POSTS_POST_ID]]
            );
        }
        else {    // return error message
            Session::flash("messages", ["Sorry, Post was not added. Please retry later"]);
            return redirect()->action('BlogController@index');
        }
    }

    /**
     * Get the post info
     * @param Post &$Post the current Post model
     * @param bool $minimal if you want to return part of text or not
     * @return array
     */
    public function getPostInfo(&$Post, $minimal = false){
        $postInfo = [];
        //Get Post title, Body, created at, up votes and down votes
        $postInfo[Constants::FLD_POSTS_TITLE] = $Post[Constants::FLD_POSTS_TITLE];
        $postInfo[Constants::FLD_POSTS_POST_ID] = $Post[Constants::FLD_POSTS_POST_ID];
        $postInfo[Constants::FLD_POSTS_BODY] = $Post[Constants::FLD_POSTS_BODY];
        $postInfo[Constants::FLD_POSTS_UP_VOTES] = $Post[Constants::FLD_POSTS_UP_VOTES];
        $postInfo[Constants::FLD_POSTS_DOWN_VOTES] = $Post[Constants::FLD_POSTS_DOWN_VOTES];
        $postInfo[Constants::FLD_COMMENTS_CREATED_AT] = $Post[Constants::FLD_POSTS_CREATED_AT];
        //Get Post Owner user name
        $postInfo["username"] = $Post['owner'][Constants::FLD_USERS_USERNAME];
        return $postInfo;
    }

    /**
     * * Get the Post Comments "till now the first two levels" (ToDo @ Samir Add More depth to the comments replies "recursive Query using Baum)
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
            $commentReplies = []; //The result array
            $replies = $comment->replies; //the replies to the current Comment
            $index2 = 0; //index For 2nd Level
            foreach ($replies as $reply){
                $commentReplies[$index2++] = $this->getCommentInfo($reply);
            }
            $postComments[$index]['replies'] = $commentReplies; //put the replies to the comment in the minimal form
            $index++;
        }
        return $postComments;
    }

    public function getCommentInfo(&$Comment)
    {
        $commentInfo = [];
        //Get Comment Title , Comment ID, User Name, Body, Up votes, Down Votes and Created At
        $commentInfo[Constants::FLD_COMMENTS_TITLE] = $Comment[Constants::FLD_COMMENTS_TITLE];
        $commentInfo[Constants::FLD_COMMENTS_COMMENT_ID] = $Comment[Constants::FLD_COMMENTS_COMMENT_ID];
        $commentInfo[Constants::FLD_COMMENTS_BODY] = $Comment[Constants::FLD_COMMENTS_BODY];
        $commentInfo[Constants::FLD_COMMENTS_CREATED_AT] = $Comment[Constants::FLD_COMMENTS_CREATED_AT];
        $commentInfo[Constants::FLD_COMMENTS_DOWN_VOTES] = $Comment[Constants::FLD_COMMENTS_DOWN_VOTES];
        $commentInfo[Constants::FLD_COMMENTS_UP_VOTES] = $Comment[Constants::FLD_COMMENTS_UP_VOTES];
        $commentInfo["username"] = $Comment['owner'][Constants::FLD_USERS_USERNAME];
        return $commentInfo;
    }

}
