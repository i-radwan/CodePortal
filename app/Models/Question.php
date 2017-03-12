<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;
use App\Exceptions\UnknownContestException;
use App\Exceptions\UnknownAdminException;
use App\Exceptions\UnknownUserException;
use Log;
class Question extends Model
{
    public function __construct(array $attributes = [])
    {
        $this->fillable =  [
            config('db_constants.FIELDS.FLD_QUESTIONS_TITLE'),
            config('db_constants.FIELDS.FLD_QUESTIONS_CONTENT'),
            config('db_constants.FIELDS.FLD_QUESTIONS_ANSWER'),
            config('db_constants.FIELDS.FLD_QUESTIONS_STATUS'),
        ];
        parent::__construct($attributes);
    }
    public function contest()
    {
        return $this->belongsTo(Contest::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, config('db_constants.FIELDS.FLD_QUESTIONS_ADMIN_ID'));
    }

    public function user()
    {
        return $this->belongsTo(User::class, config('db_constants.FIELDS.FLD_QUESTIONS_USER_ID'));
    }

    public function store()
    {
        $v = Validator::make($this->attributes, config('rules.question.store_validation_rules'));
        $v->validate();

        if (!$this->contest()) {
            throw new UnknownContestException;
        }
        if ($this->attributes[config('db_constants.FIELDS.FLD_QUESTIONS_USER_ID')] && !$this->user()) {
            throw new UnknownUserException();
        }
        $this->save();
    }

    public function saveAnswer($newAnswer, $admin, $status = '0')
    {
        if ($admin->attributes[config('db_constants.FIELDS.FLD_USERS_ROLE')] == config('constants.USER_ROLE.ADMIN')) {
            $this->admin()->associate($admin);
            $this->attributes[config('db_constants.FIELDS.FLD_QUESTIONS_ANSWER')] = $newAnswer;
            $this->attributes[config('db_constants.FIELDS.FLD_QUESTIONS_STATUS')] = $status;
            Log::info($this->attributes);
            $v = Validator::make($this->attributes, config('rules.question.store_answer_validation_rules'));
            $v->validate();
            if ($this->attributes[config('db_constants.FIELDS.FLD_QUESTIONS_ADMIN_ID')] && !$this->admin()) {
                throw new UnknownAdminException;
            }
            return parent::save([]);
        }
    }
}
