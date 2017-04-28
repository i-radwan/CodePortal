<?php
namespace App\Models;

use DB;
use App\Utilities\Constants;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\VarDumper\Caster\ConstStub;

class Post extends Model
{
    //Add Validation
    use ValidateModelData;

    //Use Like Trait
    use GetUserVotes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Constants::TBL_POSTS;

    /**
     * The primary key of the table associated with the model.
     *
     * @var string
     */
    protected $primaryKey = Constants::FLD_POSTS_ID;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        Constants::FLD_POSTS_TITLE,
        Constants::FLD_POSTS_BODY,
        Constants::FLD_POSTS_OWNER_ID
    ];

    /**
     * The rules to check against before saving the model
     *
     * @var array
     */
    protected $rules = [
        Constants::FLD_POSTS_BODY => 'required|min:50',
        Constants::FLD_POSTS_TITLE => 'required|min:6',
        Constants::FLD_POSTS_OWNER_ID => 'required|exists:'. Constants::TBL_USERS. ','. Constants::FLD_USERS_ID
    ];

    /*
     * Get Comments for this Post with hierarchy
     */
    public function comments(){
        return  $this->hasMany(Comment::class)->where(Constants::FLD_COMMENTS_PARENT_ID, null); //ToDo: Samir Change that to  a more efficient Way
    }

    /*
     * Get Owner of the post
     */
    public function owner(){
        return $this->belongsTo(User::class, Constants::FLD_POSTS_OWNER_ID);
    }

    public function scopeOfBody(Builder $query, $word = null){
        $query =  $this->select();
        //Check if name is empty or null
        if( $word == null || $word == ""){
            return $query;
        }

        return $query->where(Constants::TBL_POSTS . '.' . Constants::FLD_POSTS_BODY,
            'LIKE',
            "*$word*"
        );
    }


}
