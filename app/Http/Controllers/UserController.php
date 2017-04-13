<?php

namespace App\Http\Controllers;

class UserController extends Controller
{
    /**
     * Show the user profile page.
     *
     * @param $user
     * @return \Illuminate\View\View
     */
    public function index($user)
    {
        return view('profile.index', ['userName' => $user])->with('pageTitle', config('app.name'). ' | '. $user);
    }
}
