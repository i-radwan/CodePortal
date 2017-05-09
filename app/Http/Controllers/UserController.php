<?php

namespace App\Http\Controllers;

use Charts;
use Image;
use App\Models\User;
use App\Models\Problem;
use App\Utilities\Constants;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Intl\Intl;



class UserController extends Controller
{
    /**
     * Show the user profile page.
     *
     * @param $user
     * @return \Illuminate\View\View
     */
    public function index(User $user)
    {
        return view('profile.index')
            ->with('pageTitle', config('app.name') . ' | ' . $user[Constants::FLD_USERS_USERNAME])
            ->with('user', $user)
            ->with('chart', UserController::statistics());
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
            Carbon::now()->format('l'),
            Carbon::now()->subDays(1)->format('l'),
            Carbon::now()->subDays(2)->format('l'),
            Carbon::now()->subDays(3)->format('l'),
            Carbon::now()->subDays(4)->format('l'),
            Carbon::now()->subDays(5)->format('l'),
            Carbon::now()->subDays(6)->format('l')
        );

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
        //making an array of countries
        \Locale::setDefault('en');
        $countries = Intl::getRegionBundle()->getCountryNames();
        $countries=array( "AA" => '')+$countries; 
       
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
    public function update(Request $request)
    {
        //TODO @Abzo image cropping
        //TODO delete old images of the same user
        $user = \Auth::user();
        $this->validate($request, array(
            Constants::FLD_USERS_PROFILE_PICTURE => 'nullable|mimes:jpg,jpeg,png|max:2500',
            'password' => 'nullable|min:6',
            'oldPassword' => 'min:6|old',
            Constants::FLD_USERS_BIRTHDATE => 'nullable|date|before:2005-1-1'
        ));

        //saving picture in database
        if ($request->hasFile('profile_picture')) {
            $image = $request->file('profile_picture');
            $fileName = $user->id . '.' . $image->getClientOriginalExtension();
            $fileLocation = public_path('images/' . $fileName);
            Image::make($image)->save($fileLocation);
            $user->profile_picture = $fileName;
        }

        //changes Date format to be saved in DB
        if (($request->input('birthdate') != null)) {
            if (strpos($request->input('birthdate'), '-') !== false) {

                $user->birthdate = $request->input('birthdate');

            } else {

                $dateOfBirth = explode('/', $request->input('birthdate'));

                if ((array_key_exists("2", $dateOfBirth)) && (array_key_exists("1", $dateOfBirth)) && (array_key_exists("0", $dateOfBirth))) {

                    $formattedBirth = $dateOfBirth['2'] . '-' . $dateOfBirth['0'] . '-' . $dateOfBirth['1'];
                    $user->birthdate = $formattedBirth;

                }
            }
        }

        //saving user handles in DB
        if ($request[Constants::FLD_USERS_CODEFORCES_HANDLE] && ($request[Constants::FLD_USERS_CODEFORCES_HANDLE] != $user->getHandle(Constants::JUDGE_CODEFORCES_ID) )) {

            $user->addHandle(Constants::JUDGE_CODEFORCES_ID, $request[Constants::FLD_USERS_CODEFORCES_HANDLE]);
        }

        if ($request[Constants::FLD_USERS_UVA_HANDLE] && ($request[Constants::FLD_USERS_UVA_HANDLE] != $user->getHandle(Constants::JUDGE_UVA_ID)  )) {
            $user->addHandle(Constants::JUDGE_UVA_ID, $request[Constants::FLD_USERS_UVA_HANDLE]);
        }

        if ($request[Constants::FLD_USERS_LIVE_ARCHIVE_HANDLE] && ($request[Constants::FLD_USERS_LIVE_ARCHIVE_HANDLE] != $user->getHandle(Constants::JUDGE_LIVE_ARCHIVE_ID) )) {
            $user->addHandle(Constants::JUDGE_LIVE_ARCHIVE_ID, $request[Constants::FLD_USERS_LIVE_ARCHIVE_HANDLE]);
        }

        //saving pass,email,username,first,last names and gender in database
        if (strlen($request->input('password')) >= 6) {
            $user->password = bcrypt($request->input('password'));
        }

        if($request->input('country') != "")
        $user->country = $request->input('country');

        if ($request->input('gender') == 'Male') {
            $user->gender = '0';
        } else {
            $user->gender = '1';
        }
        $user->email = $request->input('email');
        $user->first_name = $request->input('FirstName');
        $user->last_name = $request->input('LastName');
        
        //saving in the database
        $user->save();

        //redirecting with the id of the current/modified username
        return redirect(route(Constants::ROUTES_PROFILE, $user[Constants::FLD_USERS_USERNAME]));
    }
}