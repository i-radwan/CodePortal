<?php

namespace App\Http\Controllers;

class UserController extends Controller
{
    /**
     * Show the problems page.
     * @param $user
     * @return \Illuminate\Http\Response
     */
    public function index($user)
    {
        return view('profile.index', ['userName' => $user])->with('pageTitle', config('app.name'). ' | '. $user);
    }
}
