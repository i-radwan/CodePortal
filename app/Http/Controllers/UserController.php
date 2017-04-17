<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Utilities\Constants;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Problem;
use App\Http\Controllers\ProblemController;
use Illuminate\Validation\Rule;
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
        //FirstName ,LastName,Country,Email,Username
        //ToDO gender ,country drop down ,password,picture,birthdate
        $user= User::find($request->all()['id']);
          $this->validate($request,array('FirstName'=> 'alpha','LastName'=>'alpha',
          'email' =>

           [ Rule::unique('users')->ignore($user->id)
            ],'username' => 
           [ Rule::unique('users')->ignore($user->id)]
            ));
        

        $user->email = $request->input('email');
        $user->username=$request->input('username');
        $user->first_name=$request->input('FirstName');
        // dd($request->input('LastName'));
        $user->last_name =$request->input('LastName');
        $user->save();


        //return redirect()->route('profile.index');
        //dd($request->all()['id']);  
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