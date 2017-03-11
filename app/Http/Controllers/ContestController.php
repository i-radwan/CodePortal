<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContestController extends Controller
{
    /**
     * Show the contests page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('contests.index');
    }
}
