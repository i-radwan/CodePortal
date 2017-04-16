<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Utilities\Constants;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Problem;
use App\Http\Controllers\ProblemController;
use DB;


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
    	return view('profile.index', ['userName' => $user])->with('pageTitle', config('app.name'). ' | '. $user)->withDate(UserController::userDate($user))->with('problems', UserController::userWrongSubmissions($user))->with('counter',UserController::userNumberOfSolvedProblems($user));
    }

    public function edit()
    {
        $user=\Auth::user();
        return view('profile.edit')->with('pageTitle', config('app.name').'|'.$user->username)->with('user',$user);
    }
    public function editProfile(Request $request)
    {
        dd($request);  
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
 public function userWrongSubmissions($user){
    $problemsArr = User::getWrongAnswerProblems($user);
    return   Problem::whereIn('id', $problemsArr)->paginate(4);
}
public function userNumberOfSolvedProblems($user){
    return  count(User::getSolvedProblems($user));
}


}