<?php

namespace App\Http\Controllers\Blogs;

use Auth;
use Session;
use DB;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Utilities\Constants;
use App\Http\Controllers\Controller;
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
        // Getting posts
        $posts = Post::ofContent(request('q'))
            ->orderByDesc(Constants::FLD_BLOGS_BLOG_ID)
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
            ->with('commentFormURL', route(Constants::ROUTES_BLOGS_COMMENT_STORE, $post[Constants::FLD_POSTS_ID]))
            ->with('pageTitle', config('app.name') . ' | ' . $post[Constants::FLD_POSTS_TITLE]);
    }

    /**
     * Shows certain user posts
     *
     * @param User $user
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function displayUserPosts(User $user)
    {
        // Getting posts
        $posts = Post::ofUser($user)
            ->orderByDesc(Constants::FLD_BLOGS_BLOG_ID)
            ->paginate(Constants::POSTS_COUNT_PER_PAGE);

        return view('blogs.index')
            ->with('posts', $posts)
            ->with('user', $user)
            ->with('topContributors', $this->getTopContributors())
            ->with('postUpVoteURL', url("/blogs/up_vote/entry"))
            ->with('postDownVoteURL', url("blogs/down_vote/entry"))
            ->with('pageTitle', config('app.name') . ' | Blogs');
    }

    /**
     * Shows add/edit post page
     *
     * @param Post $post
     * @return \Illuminate\View\View
     */
    public function edit(Post $post)
    {
        return view("blogs.add_edit")
            ->with('post', $post)
            ->with('pageTitle', config('app.name') . ' | ' . $post[Constants::FLD_POSTS_TITLE]);
    }

    /**
     * Shows edit post page
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view("blogs.add_edit")
            ->with('pageTitle', config('app.name') . ' | Add Post');
    }

    /**
     * Add new Post to db
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function add(Request $request)
    {
        // Create new Post
        $post = new Post($request->all());

        // Add the owner_id
        $post->owner()->associate(Auth::user());

        // Verify Saving
        if ($post->save()) {
            // Return success message
            Session::flash("messages", ["Post added successfully!"]);
            return redirect(route(Constants::ROUTES_BLOGS_POST_DISPLAY, $post[Constants::FLD_POSTS_ID]));
        } else { // return error message
            Session::flash("messages", ["Sorry, post was not added. Please retry later!"]);
            return back();
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
    public function update(Request $request, Post $post)
    {
        // Update Post
        $post->update($request->all());

        // Flash Success Message
        Session::flash('messages', ['Your post was edited successfully']);
        return redirect(route(Constants::ROUTES_BLOGS_POST_DISPLAY, $post[Constants::FLD_POSTS_ID]));
    }

    /**
     * Delete post
     *
     * @param Post $post
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Post $post)
    {
        $post->delete();

        // Flash Success Message
        Session::flash('messages', ['Your post has been deleted successfully!']);
        return redirect(route(Constants::ROUTES_BLOGS_INDEX));
    }

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
            ->limit(10)
            ->orderby('contributions', 'desc')->pluck("contributions", Constants::FLD_USERS_USERNAME);
    }
}
