<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contest;

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

    public function addEdit()
    {
        return view('contests.add_edit');
    }

    public function addContest(Request $request)
    {
        $contest = new Contest($request->all());
        $contest->save();
    }

    public function editContest(Request $request)
    {

    }
}
