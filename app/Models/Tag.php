<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Integer;
use Validator;
use DB;

class Tag extends Model
{
    public function __construct(array $attributes = [])
    {
        $this->fillable = [
            config('db_constants.FIELDS.FLD_TAGS_NAME'),
        ];
        parent::__construct($attributes);
    }

    public function problems()
    {
        return $this->belongsToMany(Problem::class, config('db_constants.TABLES.TBL_PROBLEM_TAG'));
    }

    public function store()
    {
        $v = Validator::make($this->attributes, config('rules.tag.store_validation_rules'));
        $v->validate();
        $this->save();
    }

    public static function index($count = 15)
    {
        return json_encode(
            DB::table(config('db_constants.TABLES.TBL_TAGS'))
                ->select(
                    config('db_constants.FIELDS.FLD_TAGS_ID'),
                    config('db_constants.FIELDS.FLD_TAGS_NAME'))
                ->take($count)->get());
    }

}
