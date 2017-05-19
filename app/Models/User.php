<?php

namespace App\Models;

use DB;
use Validator;
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
        Constants::FLD_USERS_EMAIL,
        Constants::FLD_USERS_PASSWORD,
        Constants::FLD_USERS_USERNAME,
        Constants::FLD_USERS_FIRST_NAME,
        Constants::FLD_USERS_LAST_NAME,
        Constants::FLD_USERS_GENDER,
        Constants::FLD_USERS_BIRTHDATE,
        Constants::FLD_USERS_PROFILE_PICTURE,
        Constants::FLD_USERS_COUNTRY
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        Constants::FLD_USERS_PASSWORD,
        Constants::FLD_USERS_REMEMBER_TOKEN,
    ];

    /**
     * The rules to check against before saving the model
     *
     * @var array
     */
    protected $rules = [
        //TODO: @Abdo add any needed validation rules
        Constants::FLD_USERS_USERNAME => 'required|max:20|unique:' . Constants::TBL_USERS,
        Constants::FLD_USERS_EMAIL => 'required|email|max:50|unique:' . Constants::TBL_USERS,
        Constants::FLD_USERS_PASSWORD => 'required|min:6',
        Constants::FLD_USERS_FIRST_NAME => 'nullable|max:20',
        Constants::FLD_USERS_LAST_NAME => 'nullable|max:20',
        Constants::FLD_USERS_GENDER => 'nullable|Regex:/([01])/',
        //Constants::FLD_USERS_BIRTHDATE => 'nullable|date|before:2005-1-1',       //TODO: add more validation on birthdate and why organizers save check it ???!!!
        Constants::FLD_USERS_ROLE => 'Regex:/([012])/'
    ];

    /**
     * Validate the rules then save the model to the database
     *
     * @param array $options
     * @return bool
     */
    public function save(array $options = [])
    {
        $rules = $this->rules;

        if ($this->exists) {
            $rules[Constants::FLD_USERS_USERNAME] = 'required|max:20|unique:' .
                Constants::TBL_USERS . ',' . Constants::FLD_USERS_USERNAME . ',' . $this[Constants::FLD_USERS_ID];
            $rules[Constants::FLD_USERS_EMAIL] = 'required|email|max:50|unique:' .
                Constants::TBL_USERS . ',' . Constants::FLD_USERS_EMAIL . ',' . $this[Constants::FLD_USERS_ID];
        }

        Validator::make($this->attributes, $rules)->validate();
        return parent::save($options);
    }

    /**
     * Get the route key for the model
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return Constants::FLD_USERS_USERNAME;
    }

    /**
     * Return the handles on different online judges of the current user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function handles()
    {
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
     * @param int $judgeId
     * @return string|null
     */
    public function handle($judgeId)
    {
        $judgeHandle = $this->handles()->where(Constants::FLD_USER_HANDLES_JUDGE_ID, $judgeId)->first();

        if (!$judgeHandle) {
            return null;
        }

        return $judgeHandle->pivot[Constants::FLD_USER_HANDLES_HANDLE];
    }

    /**
     * Attach the given online judge handle to the current user
     *
     * @param int $judgeId
     * @param string $handle
     */
    public function addHandle($judgeId, $handle)
    {
        // If adding the same handle as before then no need to proceed further
        if ($this->handle($judgeId) == $handle) {
            return;
        }

        $this->handles()->syncWithoutDetaching([
            $judgeId => [Constants::FLD_USER_HANDLES_HANDLE => $handle]
        ]);

        // Delete previous record of the current user if exits
        DB::table(Constants::TBL_HANDLES_SYNC_QUEUE)
            ->where(
                Constants::FLD_HANDLES_SYNC_QUEUE_USER_ID,
                $this[Constants::FLD_USERS_ID]
            )
            ->where(
                Constants::FLD_HANDLES_SYNC_QUEUE_JUDGE_ID,
                $judgeId
            )
            ->delete();

        // Add new handle to sync queue
        DB::table(Constants::TBL_HANDLES_SYNC_QUEUE)
            ->insert([
                Constants::FLD_HANDLES_SYNC_QUEUE_USER_ID => $this[Constants::FLD_USERS_ID],
                Constants::FLD_HANDLES_SYNC_QUEUE_JUDGE_ID => $judgeId
            ]);
    }

    /**
     * Return all the submission of the current user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function submissions()
    {
        return $this->hasMany(Submission::class, Constants::FLD_SUBMISSIONS_USER_ID);
    }

    /**
     * Return the latest offset-th submission id related to the given online judge id.
     * If no submission was found null will be returned
     *
     * @param $judgeId
     * @param $offset
     * @return int|null
     */
    public function latestSubmissionID($judgeId, $offset)
    {
        $query = DB::table(Constants::TBL_SUBMISSIONS)
            ->select(Constants::FLD_SUBMISSIONS_JUDGE_SUBMISSION_ID)
            ->join(
                Constants::TBL_PROBLEMS,
                Constants::TBL_PROBLEMS . '.'  . Constants::FLD_PROBLEMS_ID,
                '=',
                Constants::TBL_SUBMISSIONS . '.' . Constants::FLD_SUBMISSIONS_PROBLEM_ID
            )
            ->where(
                Constants::TBL_PROBLEMS . '.'  . Constants::FLD_PROBLEMS_JUDGE_ID,
                '=',
                $judgeId
            )
            ->where(
                Constants::TBL_SUBMISSIONS . '.'  . Constants::FLD_SUBMISSIONS_USER_ID,
                '=',
                $this[Constants::FLD_USERS_ID]
            )
            ->orderByDesc(Constants::FLD_SUBMISSIONS_JUDGE_SUBMISSION_ID)
            ->limit(1)
            ->offset($offset);
        $submissionId = (array) $query->first();

        if ($submissionId != null && isset($submissionId)) {
            return $submissionId[Constants::FLD_SUBMISSIONS_JUDGE_SUBMISSION_ID];
        }

        return null;
    }

    /**
     * Return all problems correctly or wrongly solved
     *
     * TODO: fix problems that is both correctly & wrongly solved
     *
     * @param bool $accepted Whether to return correctly or wrongly solved problems
     * @return \Illuminate\Database\Query\Builder
     */
    public function problems($accepted = true)
    {
        $query = Problem::select(Problem::$basicProblemsQueryCols)
            ->join(
                Constants::TBL_SUBMISSIONS,
                Constants::TBL_PROBLEMS . '.' . Constants::FLD_PROBLEMS_ID,
                '=',
                Constants::TBL_SUBMISSIONS . '.' . Constants::FLD_SUBMISSIONS_PROBLEM_ID
            )
            ->join(
                Constants::TBL_USERS,
                Constants::TBL_SUBMISSIONS . '.' . Constants::FLD_SUBMISSIONS_USER_ID,
                '=',
                Constants::TBL_USERS . '.' . Constants::FLD_USERS_ID
            )
            ->where(
                Constants::TBL_USERS . '.' . Constants::FLD_USERS_ID,
                '=',
                $this[Constants::FLD_USERS_ID]
            )
            ->distinct();

        $query->where(
            Constants::FLD_SUBMISSIONS_VERDICT,
            ($accepted ? '=' : '!='),
            Constants::VERDICT_ACCEPTED
        );

        return $query;
    }

    /**
     * Return the contests that the current user owns
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function owningContests()
    {
        return $this->hasMany(Contest::class, Constants::FLD_CONTESTS_OWNER_ID);
    }

    /**
     * Return the contests that the current user organized as admin
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function organizingContests()
    {
        return $this->belongsToMany(
            Contest::class,
            Constants::TBL_CONTEST_ADMINS,
            Constants::FLD_CONTEST_ADMINS_ADMIN_ID,
            Constants::FLD_CONTEST_ADMINS_CONTEST_ID
        );
    }

    /**
     * Return the contests that the current user participated in
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function participatingContests()
    {
        return $this->belongsToMany(
            Contest::class,
            Constants::TBL_CONTEST_PARTICIPANTS,
            Constants::FLD_CONTEST_PARTICIPANTS_USER_ID,
            Constants::FLD_CONTEST_PARTICIPANTS_CONTEST_ID
        )->withTimestamps();
    }

    /**
     * Return all the contests that the current user has been invited to join
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function invitingContests()
    {
        return
            $this->belongsToMany(
                Team::class,
                Constants::TBL_NOTIFICATIONS,
                Constants::FLD_NOTIFICATIONS_RECEIVER_ID,
                Constants::FLD_NOTIFICATIONS_RESOURCE_ID
            )->where(
                Constants::FLD_NOTIFICATIONS_TYPE,
                '=',
                Constants::NOTIFICATION_TYPE_CONTEST
            );
    }

    /**
     * Return all questions asked by the current user in the given contest.
     * If no contest is passed then all questions in all contests will be returned
     *
     * @param int $contestId
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questions($contestId = null)
    {
        $query = $this->hasMany(Question::class, Constants::FLD_QUESTIONS_USER_ID);

        if ($contestId != null) {
            $query->where(Constants::FLD_QUESTIONS_CONTEST_ID, '=', $contestId);
        }

        return $query;
    }

    /**
     * Return all questions answered by the current user
     *
     * ToDo: Remove if not used
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function answeredQuestions()
    {
        return $this->questions()
            ->where(Constants::FLD_QUESTIONS_ADMIN_ID, '=', $this[Constants::FLD_USERS_ID]);
    }

    /**
     * Return the groups that the current user owns
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function owningGroups()
    {
        return $this->hasMany(Group::class, Constants::FLD_GROUPS_OWNER_ID);
    }

    /**
     * Return the groups that the current user is administrating
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function administratingGroups()
    {
        return $this->belongsToMany(
            Group::class,
            Constants::TBL_GROUP_ADMINS,
            Constants::FLD_GROUP_ADMINS_ADMIN_ID,
            Constants::FLD_GROUP_ADMINS_GROUP_ID
        );
    }

    /**
     * Return the groups that the current user has joined
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function joiningGroups()
    {
        return $this->belongsToMany(
            Group::class,
            Constants::TBL_GROUP_MEMBERS,
            Constants::FLD_GROUP_MEMBERS_USER_ID,
            Constants::FLD_GROUP_MEMBERS_GROUP_ID
        )->withTimestamps();
    }

    /**
     * Return all the groups that the current user has been invited to join
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function invitingGroups()
    {
        return
            $this->belongsToMany(
                Team::class,
                Constants::TBL_NOTIFICATIONS,
                Constants::FLD_NOTIFICATIONS_RECEIVER_ID,
                Constants::FLD_NOTIFICATIONS_RESOURCE_ID
            )->where(
                Constants::FLD_NOTIFICATIONS_TYPE,
                '=',
                Constants::NOTIFICATION_TYPE_GROUP
            );
    }

    /**
     * Return the groups that the current user has sent a join request to its admin
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function seekingJoinGroups()
    {
        return $this->belongsToMany(
            Group::class,
            Constants::TBL_GROUP_JOIN_REQUESTS,
            Constants::FLD_GROUPS_JOIN_REQUESTS_USER_ID,
            Constants::FLD_GROUPS_JOIN_REQUESTS_GROUP_ID
        )->withTimestamps();
    }

    /**
     * Return all the teams that the current user has joined
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function joiningTeams()
    {
        return $this->belongsToMany(
            Team::class,
            Constants::TBL_TEAM_MEMBERS,
            Constants::FLD_TEAM_MEMBERS_USER_ID,
            Constants::FLD_TEAM_MEMBERS_TEAM_ID
        )->withTimestamps();
    }

    /**
     * Return all the teams that the current user has been invited to join
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function invitingTeams()
    {
        return
            $this->belongsToMany(
                Team::class,
                Constants::TBL_NOTIFICATIONS,
                Constants::FLD_NOTIFICATIONS_RECEIVER_ID,
                Constants::FLD_NOTIFICATIONS_RESOURCE_ID
            )->where(
                Constants::FLD_NOTIFICATIONS_TYPE,
                '=',
                Constants::NOTIFICATION_TYPE_TEAM
            );
    }

    /**
     * Return the notifications sent by this user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sentNotifications()
    {
        return $this->hasMany(Notification::class, Constants::FLD_NOTIFICATIONS_SENDER_ID);
    }

    /**
     * Return the notifications sent to this user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function receivedNotifications()
    {
        return $this->hasMany(Notification::class, Constants::FLD_NOTIFICATIONS_RECEIVER_ID);
    }

    /**
     * Return user received notifications sorted by id in descending order
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function displayableNotifications()
    {
        return $this->receivedNotifications()->orderByDesc(Constants::FLD_NOTIFICATIONS_ID);
    }

    /**
     * Return user unread notifications
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function unreadNotifications()
    {
        return $this->receivedNotifications()->ofStatus(Constants::NOTIFICATION_STATUS_UNREAD);
    }

    /**
     * Get user posts
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class, Constants::FLD_POSTS_OWNER_ID);
    }
}
