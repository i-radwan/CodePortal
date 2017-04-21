<?php

namespace App\Http\Controllers;

use App\Models\Post;
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
     */
    public function displayPost($postName){
        dd($postName);
    }
}
