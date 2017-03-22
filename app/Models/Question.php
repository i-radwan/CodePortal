<?php

namespace App\Models;

use Log;
use Validator;
use App\Utilities\Constants;
use App\Exceptions\UnknownContestException;
use App\Exceptions\UnknownAdminException;
use App\Exceptions\UnknownUserException;

class Question extends ValidatorModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Constants::TBL_QUESTIONS;

    /**
     * The primary key of the table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = Constants::FLD_QUESTIONS_ID;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        Constants::FLD_QUESTIONS_TITLE,
        Constants::FLD_QUESTIONS_CONTENT,
        Constants::FLD_QUESTIONS_ANSWER,
        Constants::FLD_QUESTIONS_STATUS
    ];

    /**
     * The rules to check against before saving the model
     *
     * @var array
     */
    protected $rules = [
        //TODO: validate that the problem belongs to the contest
        Constants::FLD_QUESTIONS_CONTEST_ID => 'required|integer|exists:' . Constants::TBL_CONTESTS . ',' . Constants::FLD_CONTESTS_ID,
        Constants::FLD_QUESTIONS_PROBLEM_ID => 'required|integer|exists:' . Constants::TBL_PROBLEMS . ',' . Constants::FLD_PROBLEMS_ID,
        Constants::FLD_QUESTIONS_USER_ID => 'required|integer|exists:' . Constants::TBL_USERS . ',' . Constants::FLD_USERS_ID,
        Constants::FLD_QUESTIONS_TITLE => 'required|max:255',
        Constants::FLD_QUESTIONS_CONTENT => 'required|min:50',
        Constants::FLD_QUESTIONS_STATUS => 'Regex:/([01])/'
    ];

    /**
     * Return the user who asked the current question
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, Constants::FLD_QUESTIONS_USER_ID);
    }

    /**
     * Return the admin who answered the current question
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function admin()
    {
        return $this->belongsTo(User::class, Constants::FLD_QUESTIONS_ADMIN_ID);
    }

    /**
     * Return the contest at which the current question was asked
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contest()
    {
        return $this->belongsTo(Contest::class, Constants::FLD_QUESTIONS_CONTEST_ID);
    }

    /**
     * Return the problem that the current question was asked about
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function problem()
    {
        return $this->belongsTo(Problem::class, Constants::FLD_QUESTIONS_PROBLEM_ID);
    }

    public function save(array $options = [])
    {
        if (!$this->contest()) {
            throw new UnknownContestException;
        }
        if (array_key_exists(Constants::FLD_QUESTIONS_USER_ID, $this->attributes) && !$this->user()) {
            throw new UnknownUserException();
        }

        return parent::save($options);
    }

    // ToDo: recheck function logic @IAR
    public function saveAnswer($newAnswer, $admin, $status = '0')
    {
        if ($admin->attributes[Constants::FLD_USERS_ROLE] == Constants::USER_ROLE["ADMIN"]) {
            $this->admin()->associate($admin);
            $this->attributes[Constants::FLD_QUESTIONS_ANSWER] = $newAnswer;
            $this->attributes[Constants::FLD_QUESTIONS_STATUS] = $status;
            Log::info($this->attributes);
            $v = Validator::make($this->attributes, config('rules.question.store_answer_validation_rules'));
            $v->validate();
            if ($this->attributes[Constants::FLD_QUESTIONS_ADMIN_ID] && !$this->admin()) {
                throw new UnknownAdminException;
            }
            return parent::save([]);
        }
    }
}
