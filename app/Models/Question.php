<?php

namespace App\Models;

use Validator;
use App\Utilities\Constants;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use ValidateModelData;

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
        Constants::FLD_QUESTIONS_CONTEST_ID => 'required|integer|exists:' . Constants::TBL_CONTESTS . ',' . Constants::FLD_CONTESTS_ID,
        Constants::FLD_QUESTIONS_PROBLEM_ID => 'required|integer|exists:' . Constants::TBL_PROBLEMS . ',' . Constants::FLD_PROBLEMS_ID,
        Constants::FLD_QUESTIONS_USER_ID => 'required|integer|exists:' . Constants::TBL_USERS . ',' . Constants::FLD_USERS_ID,
        Constants::FLD_QUESTIONS_TITLE => 'required|max:255',
        Constants::FLD_QUESTIONS_CONTENT => 'required|min:50',
        Constants::FLD_QUESTIONS_STATUS => 'Regex:/([01])/',
    ];

    /**
     * The rules to check against before saving the answer of a question
     *
     * @var array
     */
    protected $answerQuestionRules = [
        Constants::FLD_QUESTIONS_ANSWER => 'required',
        Constants::FLD_QUESTIONS_ADMIN_ID => 'required|integer|exists:' . Constants::TBL_USERS . ',' . Constants::FLD_USERS_ID
    ];

    /**
     * Ask a new question and associate user, contest and problem to it
     *
     * @param array $attributes
     * @param User $user
     * @param Contest $contest
     * @param Problem $problem
     */
    public static function askQuestion(array $attributes = [], $user = null, $contest = null, $problem = null)
    {
        if (!$user || !$contest || !$problem) {
            return;
        }

        $q = new Question($attributes);

        $q->user()->associate($user);
        $q->contest()->associate($contest);
        $q->problem()->associate($problem);
        $q->save();
    }

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

    /**
     * Save question answer
     *
     * @param $newAnswer
     * @param $admin
     * @return bool
     */
    public function saveAnswer($newAnswer, $admin)
    {
        // Associate organizer who answered the question
        $this->admin()->associate($admin);

        // Save the provided answer
        $this->attributes[Constants::FLD_QUESTIONS_ANSWER] = $newAnswer;

        // Validate against rules
        Validator::make($this->attributes, $this->answerQuestionRules)->validate();

        // Save the answer
        $this->save();
    }
}
