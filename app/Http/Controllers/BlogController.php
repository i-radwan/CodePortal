<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Utilities\Constants;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Show the blogs page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('blogs.index')->with('pageTitle', config('app.name'). ' | Blogs')->with('posts', POST::all() );
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
        $owner = $Post->owner[Constants::FLD_USERS_USERNAME];

        return view("blogs.post")
            ->with('post',$Post)
            ->with('owner', $owner)
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
     * @param $postID
     */
    public function getPostData($postID){

    }
}
