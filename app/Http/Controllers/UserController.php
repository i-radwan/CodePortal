<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Utilities\Constants;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Problem;
use App\Http\Controllers\ProblemController;
use Illuminate\Validation\Rule;
use Charts;
use Image;
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
      $userData = User::where('username', $user)->first();
    	return view('profile.index', ['userName' => $user])->with('pageTitle', config('app.name'). ' | '. $user)->withDate(UserController::userDate($user))->with('problems', UserController::userWrongSubmissions($user))->with('counter',UserController::userNumberOfSolvedProblems($user))->with('chart',UserController::statistics())->with('userData',$userData);

    }
    public function statistics()
    {

     $weekDays=array(
       Carbon::now()->format('l')
      ,Carbon::now()->subDays(1)->format('l')
      ,Carbon::now()->subDays(2)->format('l')
      ,Carbon::now()->subDays(3)->format('l')
      ,Carbon::now()->subDays(4)->format('l')
      ,Carbon::now()->subDays(5)->format('l')
      ,Carbon::now()->subDays(6)->format('l'));
      
     $chart = Charts::multi('areaspline', 'highcharts')
     ->title('User Activity')
     ->colors(['#ff0000', '#00FFFF '])
     ->labels([$weekDays['6'], $weekDays['5'], $weekDays['4'], $weekDays['3'], $weekDays['2'],$weekDays['1'], $weekDays['0']])
     ->dataset('submitted porblems', [3, 4, 3, 5, 4, 10, 12])
     ->dataset('problems solved',  [1, 3, 4, 3, 3, 5, 4]);
     return $chart;
   }

    /**
     * Show the edit profile page.
     *
     * 
     * @return \Illuminate\View\View
     */
    public function edit()
    {

      $user=\Auth::user();
      return view('profile.edit')->with('pageTitle', config('app.name').'|'.$user->username)->with('user',$user);

    }

    //TODO @Abzo image cropping
    //TODO delete old images of the same user
    public function editProfile(Request $request)
    {

        //FirstName ,LastName,Country,Email,Username
        //ToDO gender ,country drop down ,password,picture,birthdate

      $user= \Auth::user();

      if($request->hasFile('imageFile'))
      {
        $image=$request->file('imageFile');
        $fileName=time().'.'.$image->getClientOriginalExtension();
        $fileLocation=public_path('images/'.$fileName);
        Image::make($image)->save($fileLocation);
        $user->profile_picture=$fileName;
      }


      $this->validate($request,array(
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
        //save image

      $id=$user->username;
      return redirect('profile/'.$id);

    }

    //TODO condition ,in case created_at is NULL
    public function userDate($user)
    {
      $userData = User::where('username', $user)->first();
      $userInfo = $userData->toArray();
      $dateCreated = $userInfo['created_at'];
      $dateCreatedArr = preg_split("/[\s-]+/", $dateCreated);
      $dt = Carbon::create($dateCreatedArr['0'], $dateCreatedArr['1'], $dateCreatedArr['2']);
      $date = $dt->toFormattedDateString();
      return $date;
    }

    public function userWrongSubmissions($user)
    {
      $problemsArr = User::getWrongAnswerProblems($user);
      return Problem::whereIn('id', $problemsArr)->paginate(4);
    }

    public function userNumberOfSolvedProblems($user)
    {
      return count(User::getSolvedProblems($user));
    }


  }