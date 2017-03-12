<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProblemController extends Controller
{
    /**
     * Show the problems page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('problems.index');
    }
}
