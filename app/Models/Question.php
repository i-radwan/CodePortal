<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;
use App\Exceptions\UnknownContestException;
use App\Exceptions\UnknownAdminException;
use App\Exceptions\UnknownUserException;

class Question extends Model
{
    protected $fillable = ['title', 'content', 'answer', 'status'];

    public function contest()
    {
        return $this->belongsTo('App\Models\Contest');
    }

    public function admin()
    {
        return $this->belongsTo('App\Models\User', 'admin_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function save(array $options = [])
    {

        return parent::save($options);
    }

    public function store()
    {
        $v = Validator::make($this->attributes, config('rules.question.store_validation_rules'));
        $v->validate();

        if (!$this->contest()) {
            throw new UnknownContestException;
        }
        if ($this->user_id && !$this->user()) {
            throw new UnknownUserException();
        }
        $this->save();
    }

    public function saveAnswer($newAnswer, $admin, $status = '0')
    {
        if ($admin->role == config('constants.USER_ROLE.ADMIN')) {
            $this->admin()->associate($admin);
            $this->answer = $newAnswer;
            $this->status = $status;
            $v = Validator::make($this->attributes, config('rules.question.store_answer_validation_rules'));
            $v->validate();
            if ($this->admin_id && !$this->admin()) {
                throw new UnknownAdminException;
            }
            return parent::save([]);
        }
    }
}
