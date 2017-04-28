<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Utilities\Constants;
use App\Http\Controllers\ProblemController;
use App\Models\Problem;
use App\Models\User;
use Carbon\Carbon;
use Charts;
use Image;
use Symfony\Component\Intl\Intl;
use Illuminate\Support\Facades\Validator;

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

        return view('profile.index', ['userName' => $user])
            ->with('pageTitle', config('app.name') . ' | ' . $user)
            ->withDate(UserController::userDate($user))
            ->with('problems', UserController::userWrongSubmissions($user))
            ->with('counter', UserController::userNumberOfSolvedProblems($user))
            ->with('chart', UserController::statistics())
            ->with('userData', $userData);
    }

    /**
     * calculates statistics (undone)
     *
     *
     * @return $chart
     */
    public function statistics()
    {

        $weekDays = array(
            Carbon::now()->format('l')
        , Carbon::now()->subDays(1)->format('l')
        , Carbon::now()->subDays(2)->format('l')
        , Carbon::now()->subDays(3)->format('l')
        , Carbon::now()->subDays(4)->format('l')
        , Carbon::now()->subDays(5)->format('l')
        , Carbon::now()->subDays(6)->format('l'));

        $chart = Charts::multi('areaspline', 'highcharts')
            ->title('User Activity')
            ->colors(['#ff0000', '#00FFFF '])
            ->labels([$weekDays['6'], $weekDays['5'], $weekDays['4'], $weekDays['3'], $weekDays['2'], $weekDays['1'], $weekDays['0']])
            ->dataset('submitted porblems', [3, 4, 3, 5, 4, 10, 15])
            ->dataset('problems solved', [1, 3, 4, 3, 3, 5, 4]);
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
        \Locale::setDefault('en');
        $countries = Intl::getRegionBundle()->getCountryNames();
        // dd($countries);
        $user = \Auth::user();
        return view('profile.edit')
            ->with('pageTitle', config('app.name') . '|' . $user->username)
            ->with('user', $user)
            ->with('country', $countries);

    }

    /**
     * handling data request in edit profile page
     *
     * @param $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function editProfile(Request $request)
    {
        //TODO @Abzo image cropping
        //TODO delete old images of the same user
        //TODO verficcation issue(picture and the pass)
        //TODO show edited new info in the view
        //TODO add missing fillable att in User model
        $user = \Auth::user();
        //temporarly validation of picture and pass(should be in model)

        $this->validate($request, array(
            'profile_picture' => 'nullable|mimes:jpg,jpeg,png|max:2500',
            'password' => 'nullable|min:6',
            'oldPassword' => 'min:6|old',));

        //saving picture in database
        if ($request->hasFile('profile_picture')) {
            $image = $request->file('profile_picture');
            $fileName = $user->id . '.' . $image->getClientOriginalExtension();
            $fileLocation = public_path('images/' . $fileName);
            Image::make($image)->save($fileLocation);
            $user->profile_picture = $fileName;
        }

        //changes date format to be saved in DB

        if (($request->input('birthdate') != null)) {
            if (strpos($request->input('birthdate'), '-') !== false) {

                $user->birthdate = $request->input('birthdate');

            } else {

                $dateOfBirth = explode('/', $request->input('birthdate'));

                if ((array_key_exists("2", $dateOfBirth)) && (array_key_exists("1", $dateOfBirth)) && (array_key_exists("0", $dateOfBirth))) {

                    $formattedBirth = $dateOfBirth['2'] . '-' . $dateOfBirth['1'] . '-' . $dateOfBirth['0'];
                    $user->birthdate = $formattedBirth;

                }
            }
        }

        //saving pass,email,username,first,last names and gender in database
        //dd($request->input('password'));
        if(strlen($request->input('password'))>= 6)
        { 
          $user->password = Hash::make($request->input('password'));
        }
        $user->email = $request->input('email');
        $user->first_name = $request->input('FirstName');
        $user->last_name = $request->input('LastName');
        $user->country = $request->input('country');

        if ($request->input('gender') == 'Male') {
            $user->gender = '0';
        } else {
            $user->gender = '1';
        }

        //saving in the database
        $user->save();


        //redirecting with the id of the current/modified username
        return redirect(route(Constants::ROUTES_PROFILE, $user->username));
    }

    /**
     * makes formatted joined date of the user
     * @param $username
     * @return $date
     */
    public function userDate($user)
    {
        $userData = User::where('username', $user)->first();
        $userInfo = $userData->toArray();
        $dateCreated = $userInfo['created_at'];
        if ($dateCreated != Null) {
            $dateCreatedArr = preg_split("/[\s-]+/", $dateCreated);
            $dt = Carbon::create($dateCreatedArr['0'], $dateCreatedArr['1'], $dateCreatedArr['2']);
            $date = $dt->toFormattedDateString();
        } else {
            $date = NULL;
        }
        return $date;
    }

    /**
     * gets user wrong submissions
     *
     * @param $username
     * @return array of problems paginated
     */
    public function userWrongSubmissions($user)
    {
        $problemsArr = User::getWrongAnswerProblems($user);
        return Problem::whereIn('id', $problemsArr)->paginate(5);
    }

    /**
     * gets count of solved problems
     *
     * @param $username
     * @return $int
     */
    public function userNumberOfSolvedProblems($user)
    {
        return count(User::getSolvedProblems($user));
    }


}