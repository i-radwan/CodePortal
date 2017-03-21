<?php

namespace App\Models;

use App\Utilities\Constants;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Constants::TBL_USERS;

    /**
     * The primary key of the table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = Constants::FLD_USERS_ID;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        Constants::FLD_USERS_NAME,
        Constants::FLD_USERS_EMAIL,
        Constants::FLD_USERS_PASSWORD,
        Constants::FLD_USERS_USERNAME,
        Constants::FLD_USERS_GENDER,
        Constants::FLD_USERS_AGE,
        Constants::FLD_USERS_PROFILE_PIC,
        Constants::FLD_USERS_COUNTRY
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        Constants::FLD_USERS_PASSWORD,
        Constants::FLD_USERS_REMEMBER_TOKEN
    ];

    public function handles()
    {
        // ToDo: should it be hasMany?
        return $this->belongsToMany(
            Judge::class,
            Constants::TBL_USER_HANDLES,
            Constants::FLD_USER_HANDLES_USER_ID,
            Constants::FLD_USER_HANDLES_JUDGE_ID
        )->withPivot(Constants::FLD_USER_HANDLES_HANDLE);
    }

    /**
     * Return user's handle corresponding to the given judge, if not found then null is returned
     *
     * @param Judge $judge
     * @return string|null
     */
    public function handle(Judge $judge)
    {
        $judgeHandle = $this->handles()->where(Constants::FLD_USER_HANDLES_JUDGE_ID, $judge->id)->first();

        if (!$judgeHandle) {
            return null;
        }

        return $judgeHandle->pivot->handle;
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, Constants::FLD_SUBMISSIONS_USER_ID);
    }

    public function participatingContests()
    {
        return $this->belongsToMany(
            Contest::class,
            Constants::TBL_CONTEST_PARTICIPANTS,
            Constants::FLD_CONTEST_PARTICIPANTS_USER_ID,
            Constants::FLD_CONTEST_PARTICIPANTS_CONTEST_ID
        )->withTimestamps();
    }

    public function organizingContests()
    {
        return $this->belongsToMany(
            Contest::class,
            Constants::TBL_CONTEST_ADMINS,
            Constants::FLD_CONTEST_ADMINS_ADMIN_ID,
            Constants::FLD_CONTEST_ADMINS_CONTEST_ID
        );
    }

    public function owningContests()
    {
        return $this->hasMany(
            Contest::class,
            Constants::FLD_CONTESTS_OWNER_ID
        );
    }

    public function questions()
    {
        return $this->hasMany(Question::class, Constants::FLD_QUESTIONS_USER_ID);
    }

    public function contest_questions($contestId)
    {
        // ToDo: rename function name
        return $this->hasMany(Question::class, Constants::FLD_QUESTIONS_USER_ID)
            ->where(Constants::FLD_QUESTIONS_CONTEST_ID, '=', $contestId);
    }

    public function answered_questions()
    {
        // ToDo: rename function name + recheck the logic of this function
        return $this->hasMany(Question::class)->where(Constants::FLD_QUESTIONS_ADMIN_ID, '=', $this->id);
    }
}
