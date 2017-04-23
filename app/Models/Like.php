<?php

namespace App;

use App\Models\Post;
use App\Models\Comment;
use App\Utilities\Constants;
use Illuminate\Database\Eloquent\Model;
use DB;
use Symfony\Component\VarDumper\Caster\ConstStub;

class Like extends Model
{
    //Add Validation
    use ValidateModelData;

    //Use Soft Delete
    use SoftDeletes;


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Constants::TBL_LIKEABLES;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        Constants::FLD_LIKEABLES_POST_ID,
        Constants::FLD_LIKEABLES_USER_ID,
        Constants::FLD_LIKEABLES_LIKEABLE_TYPE
    ];

    /*
     * Get likes for all Posts (we may not use this)
     */
    public function posts(){
        return $this->morphedByMany(Comment::class, 'likeable');
    }

    /*
     * Get likes for all Comments (we may not use this)
     */
    public function comments(){
        return $this->morphedByMany(Post::class, 'likeable');
    }



}
