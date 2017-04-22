<?php

namespace App\Models;

use DB;
use App\Utilities\Constants;
use App\Models\User;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\VarDumper\Caster\ConstStub;

class Comment extends Model
{
    //Add Validation
    use ValidateModelData;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Constants::TBL_COMMENTS;

    /**
     * The primary key of the table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = Constants::FLD_COMMENTS_COMMENT_ID;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        Constants::FLD_COMMENTS_POST_ID,
        Constants::FLD_COMMENTS_USER_ID,
//        Constants::FLD_COMMENTS_TITLE,
        Constants::FLD_COMMENTS_BODY,
        Constants::FLD_COMMENTS_PARENT_ID
    ];

    /**
     * The rules to check against before saving the model
     *
     * @var array
     */
    protected $rules = [

        Constants::FLD_COMMENTS_BODY => 'required|min:3',
//        Constants::FLD_POSTS_TITLE => 'required|min:0',
        Constants::FLD_COMMENTS_USER_ID => 'required|exists:'. Constants::TBL_USERS. ','. Constants::FLD_USERS_ID,
        Constants::FLD_COMMENTS_POST_ID => 'required|exists:'. Constants::TBL_POSTS . ','. Constants::FLD_POSTS_POST_ID,
        Constants::FLD_COMMENTS_PARENT_ID => 'nullable|exists' . Constants::TBL_COMMENTS . ','. Constants::FLD_COMMENTS_COMMENT_ID,
    ];

    /*
     * Get all replies to that Comment
     * @return Comments Collection
     */
    public function replies(){
        return $this->hasMany(Comment::class, Constants::FLD_COMMENTS_PARENT_ID);
    }

    /**
     * Get Comment Owner User Name
     */
    public function owner(){
        return $this->belongsTo(User::class,Constants::FLD_COMMENTS_USER_ID);
    }

}
