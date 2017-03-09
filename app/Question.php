<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['title', 'content', 'answer', 'status'];

    public function contest()
    {
        return $this->belongsTo('App\Contest');
    }

    public function admin()
    {
        return $this->hasOne('App\User', 'admin_id');
    }

    public function user()
    {
        return $this->hasOne('App\User', 'user_id');
    }
}
