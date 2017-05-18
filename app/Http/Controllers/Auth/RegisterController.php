<?php

namespace App\Http\Controllers\Auth;

use Validator;
use App\Models\User;
use App\Utilities\Constants;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        // Extra validation on user model
        return Validator::make($data, [
           Constants::FLD_USERS_PASSWORD => 'confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $user = User::create([
            Constants::FLD_USERS_USERNAME => $data[Constants::FLD_USERS_USERNAME],
            Constants::FLD_USERS_EMAIL => $data[Constants::FLD_USERS_EMAIL],
            Constants::FLD_USERS_PASSWORD => bcrypt($data[Constants::FLD_USERS_PASSWORD]),
        ]);
        return $user;
    }
}
