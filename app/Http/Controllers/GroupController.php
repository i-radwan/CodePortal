<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Show the groups page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('groups.index')->with('pageTitle', config('app.name'). ' | Groups');
    }
}
