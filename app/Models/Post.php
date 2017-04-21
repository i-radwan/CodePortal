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
    protected $primaryKey = Constants::FLD_POSTS_POST_ID;

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
        Constants::FLD_POSTS_OWNER_ID => 'required|exists:'. Constants::TBL_USERS. ','. Constants::FLD_USER_HANDLES_USER_ID
    ];

    /*
     * Get Comments for this Post with hierarchy
     */
    public function comments(){
        return  $this->hasMany(Comment::class)->where(Constants::FLD_COMMENTS_PARENT_ID, null); //ToDo: Samir Change that to  a more efficient Way
    }


}
