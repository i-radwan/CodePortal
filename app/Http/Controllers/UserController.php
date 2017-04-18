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
      $chart = Charts::multi('bar', 'material')
            // Setup the chart settings
       ->title("My Cool Chart")
            // A dimension of 0 means it will take 100% of the space
            ->dimensions(0, 400) // Width x Height
            // This defines a preset of colors already done:)
            ->template("material")
            // You could always set them manually
            ->colors(['#2196F3', '#F44336', '#FFC107'])
            // Setup the diferent datasets (this is a multi chart)
            ->dataset('Element 1', [5,20,100])
            ->dataset('Element 2', [15,30,80])
            ->dataset('Element 3', [25,10,40])
            // Setup what the values mean
            ->labels(['One', 'Two', 'Three']);
            

       // $chart = Charts::multi('bar', 'material')
       //      // Setup the chart settings
       // ->title("My Cool Chart")
       //      // A dimension of 0 means it will take 100% of the space
       //      ->dimensions(0, 400) // Width x Height
       //      // This defines a preset of colors already done:)
       //      ->template("material")
       //      // You could always set them manually
       //      // ->colors(['#2196F3', '#F44336', '#FFC107'])
       //      // Setup the diferent datasets (this is a multi chart)
       //      ->dataset('Element 1', [5,20,100])
       //      ->dataset('Element 2', [15,30,80])
       //      ->dataset('Element 3', [25,10,40])
       //      // Setup what the values mean
       //      ->labels(['One', 'Two', 'Three']);
       //      // dd($chart);
            

    	return view('profile.index', ['userName' => $user])->with('pageTitle', config('app.name'). ' | '. $user)->withDate(UserController::userDate($user))->with('problems', UserController::userWrongSubmissions($user))->with('counter',UserController::userNumberOfSolvedProblems($user))->with('chart',$chart);
    }
    public function statistics()
    {
       $chart = Charts::multi('bar', 'material')
            // Setup the chart settings
       ->title("My Cool Chart")
            // A dimension of 0 means it will take 100% of the space
            ->dimensions(0, 400) // Width x Height
            // This defines a preset of colors already done:)
            ->template("material")
            // You could always set them manually
            ->colors(['#2196F3', '#F44336', '#FFC107'])
            // Setup the diferent datasets (this is a multi chart)
            ->dataset('Element 1', [5,20,100])
            ->dataset('Element 2', [15,30,80])
            ->dataset('Element 3', [25,10,40])
            // Setup what the values mean
            ->labels(['One', 'Two', 'Three']);
            // dd($chart);
            return view('profile.index',['chart'=>$chart]);
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
    public function editProfile(Request $request)
    {
        //FirstName ,LastName,Country,Email,Username
        //ToDO gender ,country drop down ,password,picture,birthdate

        $user= \Auth::user();
        
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