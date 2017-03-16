<?php

namespace App\Http\Controllers;

use App\Models\Problem;
use App\Models\Tag;
use App\Models\Judge;
use Illuminate\Http\Request;

class ProblemController extends Controller
{
    /**
     * Show the problems page.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProblemsPagination($page = 1){
        $problems = Problem::index($page);
        return $problems;
    }
    public function index(Request $request)
    {

        //If No Tag Filter is applied
        if( $request->get('tag') == ""){

            $data = $this->getProblemsPagination($request->get('page'));
        }
        //If tag filter is applied
        else {
//            Tag::find(id);
            $data =  Tag::getTagProblems($request->get('tag'),$request->get('page'));
        }
        // TODOSAMRA Get the judgesInfo , We need here something to tell us if judges are needed

        $data = (json_decode($data));
        $data->tags = json_decode(Tag::index());
        $data->judges = json_decode(Judge::index());
//        dd($data);
        return view('problems.index')->with('data', $data);
    }


}
