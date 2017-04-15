<?php

namespace App\Http\Controllers;

use App\Utilities\Constants;
use App\Models\User;
use Carbon\Carbon;
use DB;


class UserController extends Controller
{
    /**
     * Show the problems page.
     * @param $user
     * @return \Illuminate\Http\Response
     */
    public function index($user)
    {
		//dd(\Auth::user());
    	
    	//echo User::where('username',$user)->get()->first()->submissions();

    	//dd( $userData->submissions->where('verdict','0'));
    	User::getWrongAnswerProblems($user);
    	return view('profile.index', ['userName' => $user])->with('pageTitle', config('app.name'). ' | '. $user)->withDate(UserController::userDate($user));
    }

    public function userDate($user)
    {
    	$userData= User::where('username',$user)->first();
    	$userInfo=$userData->toArray();
    	$dateCreated = $userInfo['created_at']; 
    	$dateCreatedArr = preg_split ("/[\s-]+/", $dateCreated); 
    	$dt = Carbon::create($dateCreatedArr['0'], $dateCreatedArr['1'],$dateCreatedArr['2']);
    	$date= $dt->toFormattedDateString(); 
    	return $date;
    }
   
}