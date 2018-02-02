<?php

namespace App\Http\Controllers;

use Image;
use App\Models\User;
use App\Utilities\Constants;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\Intl\Intl;
use ConsoleTVs\Charts\Facades\Charts;

class UserController extends Controller
{
    /**
     * Show the user basic info profile page.
     *
     * @param $user
     * @return \Illuminate\View\View
     */
    public function displayUserInfo(User $user)
    {
        return view('profile.index')
            ->with('pageTitle', config('app.name') . ' | ' . $user[Constants::FLD_USERS_USERNAME])
            ->with('user', $user)
            ->with('solvedProblems', $user->problems(true)->paginate(Constants::PROBLEMS_COUNT_PER_PAGE))
            ->with('view', 'info')
            ->with('chart', $this->submissionsStatistics($user));
    }

    /**
     * Show the user solved problems
     *
     * @param $user
     * @return \Illuminate\View\View
     */
    public function displayUserSolvedProblems(User $user)
    {
        return view('profile.index')
            ->with('pageTitle', config('app.name') . ' | ' . $user[Constants::FLD_USERS_USERNAME])
            ->with('user', $user)
            ->with('problems', $user->problems(true)->paginate(Constants::PROBLEMS_COUNT_PER_PAGE))
            ->with('view', 'problems')
            ->with('type', 'solved');
    }

    /**
     * Show the user un-solved problems
     *
     * @param $user
     * @return \Illuminate\View\View
     */
    public function displayUserUnSolvedProblems(User $user)
    {
        return view('profile.index')
            ->with('pageTitle', config('app.name') . ' | ' . $user[Constants::FLD_USERS_USERNAME])
            ->with('user', $user)
            ->with('problems', $user->problems(false)->paginate(Constants::PROBLEMS_COUNT_PER_PAGE))
            ->with('view', 'problems')
            ->with('type', 'unsolved');
    }

    /**
     * Show the user contests
     *
     * @param $user
     * @return \Illuminate\View\View
     */
    public function displayUserContests(User $user)
    {
        return view('profile.index')
            ->with('pageTitle', config('app.name') . ' | ' . $user[Constants::FLD_USERS_USERNAME])
            ->with('user', $user)
            ->with('owningContests', $user->owningContests()->get())
            ->with('participatedContests', $user->participatingContests()->get())
            ->with('organizingContests', $user->organizingContests()->get())
            ->with('disablePagination', true)
            ->with('view', 'contests');
    }

    /**
     * Show the user groups
     *
     * @param $user
     * @return \Illuminate\View\View
     */
    public function displayUserGroups(User $user)
    {
        return view('profile.index')
            ->with('pageTitle', config('app.name') . ' | ' . $user[Constants::FLD_USERS_USERNAME])
            ->with('user', $user)
            ->with('joiningGroups', $user->joiningGroups()->get())
            ->with('administratingGroups', $user->administratingGroups()->get())
            ->with('owningGroups', $user->owningGroups()->get())
            ->with('disablePagination', true)
            ->with('view', 'groups');
    }

    /**
     * Calculates user submissions statistics
     *
     * @param User $user
     * @return $chart
     */
    public function submissionsStatistics(User $user)
    {
        // Generate days array
        $weekDays = [];
        for ($i = 6; $i >= 0; $i--)
            array_push($weekDays, Carbon::now()->subDays($i)->format('l'));

        $this->getSubmittedProblemsCount($weekDays, $user, $totalSubmissionsCount, $acceptedSubmissionsCount);

        $chart = Charts::multi('areaspline', 'highcharts')
            ->title('User Activity')
            ->colors(['#ff0000', '#00FFFF'])
            ->labels($weekDays)
            ->dataset('Submitted Problems', $totalSubmissionsCount)
            ->dataset('Solved Problems', $acceptedSubmissionsCount);

        return $chart;
    }

    /**
     * Get user submissions count
     *
     * @param $weekDays
     * @param $user
     * @param &$totalSubmissionsCount
     * @param &$acceptedSubmissionsCount
     */
    public function getSubmittedProblemsCount($weekDays, $user, &$totalSubmissionsCount, &$acceptedSubmissionsCount)
    {
        $totalSubmissionsCount = $acceptedSubmissionsCount = [];

        $endDateTime = Carbon::now()->timestamp;
        $startDateTime = Carbon::now()->subDay(6)->timestamp;

        // Get all submissions within past 6-days perios
        $submissions = $user->submissions()
            ->whereBetween(Constants::FLD_SUBMISSIONS_SUBMISSION_TIME, [$startDateTime, $endDateTime])
            ->get();

        // Group submissions by day names
        $totalSubmissions = $submissions->groupBy(function ($submission) {
            return Carbon::createFromTimestamp($submission[Constants::FLD_SUBMISSIONS_SUBMISSION_TIME])->format('l');
        });

        // Group accepted submissions by day names
        $acceptedSubmissions = $submissions
            ->whereIn(Constants::FLD_SUBMISSIONS_VERDICT, [Constants::VERDICT_ACCEPTED, Constants::VERDICT_PARTIAL_ACCEPTED])
            ->groupBy(function ($submission) {
                return Carbon::createFromTimestamp($submission[Constants::FLD_SUBMISSIONS_SUBMISSION_TIME])->format('l');
            });

        // Fill submissions counts arrays
        foreach ($weekDays as $day) {
            array_push($totalSubmissionsCount, (isset($totalSubmissions[$day]) ? $totalSubmissions[$day]->count() : 0));
            array_push($acceptedSubmissionsCount, (isset($acceptedSubmissions[$day]) ? $acceptedSubmissions[$day]->count() : 0));
        }
    }

    /**
     * Show the edit profile page.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        // making an array of countries
        \Locale::setDefault('en');
        $countries = Intl::getRegionBundle()->getCountryNames();

        $user = \Auth::user();

        return view('profile.edit')
            ->with('pageTitle', config('app.name') . ' | ' . $user[Constants::FLD_USERS_USERNAME])
            ->with('user', $user)
            ->with('countries', $countries);
    }

    /**
     * Handling data request in edit profile page
     *
     * @param $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request)
    {
        $user = \Auth::user();

        // Validate request
        $this->validate($request, [
            Constants::FLD_USERS_PROFILE_PICTURE => 'nullable|mimes:jpg,jpeg,png|max:2500',
            Constants::FLD_USERS_EMAIL => 'required|email|max:50|unique:' . Constants::TBL_USERS . ',' . Constants::FLD_USERS_EMAIL . ',' . $user[Constants::FLD_USERS_ID] . ',' . Constants::FLD_USERS_ID,
            Constants::FLD_USERS_PASSWORD => 'nullable|min:6',
            'oldPassword' => 'min:6|old',
            Constants::FLD_USERS_GENDER => 'Regex:/([01])/',
            Constants::FLD_USERS_FIRST_NAME => 'nullable|max:20',
            Constants::FLD_USERS_LAST_NAME => 'nullable|max:20',
            Constants::FLD_USERS_BIRTHDATE => 'nullable|date|before:' . Carbon::today()->subYears(10)->toDateString()
        ]);

        // Saving picture in folder and DB
        // Remove user old photo
        if ($request->hasFile('profile_picture')) {
            $oldFile = public_path('profile_pics/' . $user[Constants::FLD_USERS_PROFILE_PICTURE]);

            // Create and save new image file
            $image = $request->file('profile_picture');
            $fileName = $user[Constants::FLD_USERS_ID] . '.' . $image->getClientOriginalExtension();
            $fileLocation = public_path('profile_pics/' . $fileName);
            Image::make($image)->save($fileLocation);

            $user[Constants::FLD_USERS_PROFILE_PICTURE] = $fileName;

            // Delete user old file
            // unlink($oldFile);
        }


        // Saving user handles in DB
        if ($request[Constants::FLD_USERS_CODEFORCES_HANDLE]) {
            $user->addHandle(Constants::JUDGE_CODEFORCES_ID, $request[Constants::FLD_USERS_CODEFORCES_HANDLE]);
        }

        if ($request[Constants::FLD_USERS_UVA_HANDLE]) {
            $user->addHandle(Constants::JUDGE_UVA_ID, $request[Constants::FLD_USERS_UVA_HANDLE]);
        }

        if ($request[Constants::FLD_USERS_LIVE_ARCHIVE_HANDLE]) {
            $user->addHandle(Constants::JUDGE_LIVE_ARCHIVE_ID, $request[Constants::FLD_USERS_LIVE_ARCHIVE_HANDLE]);
        }

        if ($request->get('password')) {
            $user[Constants::FLD_USERS_PASSWORD] = \Hash::make($request->get('password'));
        }

        // Save user data
        $user[Constants::FLD_USERS_BIRTHDATE] = $request->input('birthdate');
        $user[Constants::FLD_USERS_COUNTRY] = $request->input('country');
        $user[Constants::FLD_USERS_GENDER] = $request->input('gender');
        $user[Constants::FLD_USERS_EMAIL] = $request->input('email');
        $user[Constants::FLD_USERS_FIRST_NAME] = $request->input('first_name');
        $user[Constants::FLD_USERS_LAST_NAME] = $request->input('last_name');

        // Saving in the database
        $user->save();

        // Redirecting with the id of the current/modified username
        return redirect(route(Constants::ROUTES_PROFILE, $user[Constants::FLD_USERS_USERNAME]));
    }
}