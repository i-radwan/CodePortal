<?php

namespace App\Http\Controllers;

class ProfileController extends Controller
{
    /**
     * Show the problems page.
     * @param $user
     * @return \Illuminate\Http\Response
     */
    public function index($user)
    {
        return view('profile.index', ['userName' => $user]);
    }
}
