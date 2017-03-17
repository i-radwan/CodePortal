<?php

namespace App\Models;

use Log;
use Validator;
use App\Utilities\Constants;
use App\Exceptions\UnknownContestException;
use App\Exceptions\UnknownAdminException;
use App\Exceptions\UnknownUserException;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
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

    public function user()
    {
        return $this->belongsTo(User::class, Constants::FLD_QUESTIONS_USER_ID);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, Constants::FLD_QUESTIONS_ADMIN_ID);
    }

    public function contest()
    {
        return $this->belongsTo(Contest::class, Constants::FLD_QUESTIONS_CONTEST_ID);
    }

    public function store()
    {
        $v = Validator::make($this->attributes, config('rules.question.store_validation_rules'));
        $v->validate();

        if (!$this->contest()) {
            throw new UnknownContestException;
        }
        if ($this->attributes[Constants::FLD_QUESTIONS_USER_ID] && !$this->user()) {
            throw new UnknownUserException();
        }

        $this->save();
    }

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
