<?php

namespace App\Http\Controllers;

use App\Utilities\Constants;
use Auth;
use Session;
use App\Models\UpVote;
use App\Models\DownVote;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    /**
     * @param $post
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function upVotePost($post){
        $this->processUpVote(Post::class, $post);
        return redirect()->back();
    }

    /**
     * @param $comment
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function upVoteComment($comment){
        $this->processUpVote(Comment::class, $comment);
        return redirect()->back();
    }

    /**
     * @param $comment
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function downVoteComment($comment){
        $this->processDownVote(Comment::class, $comment);
        return redirect()->back();
    }

    /**
     * @param $post
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function downVotePost($post){
        $this->processDownVote(Post::class, $post);
        return redirect()->back();
    }

    /**
     * Handle the Up Vote with removing the Down Vote if it's already found active
     * @param $type
     * @param $id
     */
    public function processUpVote($type, $id){
        //Check for the previous up and down votes for the single post with $id
        $previousUpVote = UpVote::withTrashed()->whereUpVotesType($type)->whereUpVotesId($id)->whereUserId(Auth::id())->first();
        $previousDownVote = DownVote::withTrashed()->whereDownVotesType($type)->whereDownVotesId($id)->whereUserId(Auth::id())->first();

        if(is_null($previousUpVote)){ //No Previous Up Vote applied for this post with that id
            //Add New Up Vote
            $upVote = new UpVote([
                Constants::FLD_UP_VOTES_USER_ID=> Auth::id(),
                Constants::FLD_UP_VOTES_VOTED_ID   => $id,
                Constants::FLD_UP_VOTES_VOTED_TYPE => $type
            ]);
            //Check if Saved Successfully
            if($upVote->save()){
                //Remove the down Vote if it's found and is not soft deleted
                if(!is_null($previousDownVote) and is_null($previousDownVote[Constants::FLD_DOWN_VOTES_DELETED_AT]) ){
                    $previousDownVote->delete();
                }
            }
        }
        else { //There Exists A Previous UpVote, So we need to reverse it's state between(Soft deleted or Not)
            if (is_null($previousUpVote->deleted_at)) { //It's UpVoted
                $previousUpVote->delete(); //Soft Delete the UpVote
            } else { //restore the UpVote
                $previousUpVote->restore();
                //Remove the down Vote if it's found and is not soft deleted
                if(!is_null($previousDownVote) and is_null($previousDownVote[Constants::FLD_DOWN_VOTES_DELETED_AT]) ){
                    $previousDownVote->delete();
                }
            }
        }
    }

    /**
     * Handle the Down Vote with removing the Up Vote if it's already found active
     * @param $type
     * @param $id
     */
    public function processDownVote($type, $id){
        //Check for the previous up and down votes for the single post with $id
        $previousDownVote = DownVote::withTrashed()->whereDownVotesType($type)->whereDownVotesId($id)->whereUserId(Auth::id())->first();
        $previousUpVote = UpVote::withTrashed()->whereUpVotesType($type)->whereUpVotesId($id)->whereUserId(Auth::id())->first();

        if(is_null($previousDownVote)){ //No Previous Down Vote applied for this post with that id
            //Add New Down Vote
            $downVote = new DownVote([
                Constants::FLD_DOWN_VOTES_USER_ID=> Auth::id(),
                Constants::FLD_DOWN_VOTES_VOTED_ID   => $id,
                Constants::FLD_DOWN_VOTES_VOTED_TYPE => $type
            ]);
            //Check if Saved Successfully
            if($downVote->save()){
                //Remove the up Vote if it's found and is not soft deleted
                if(!is_null($previousUpVote) and is_null($previousUpVote[Constants::FLD_UP_VOTES_DELETED_AT]) ){
                    $previousUpVote->delete();
                }
            }
        }
        else { //There Exists A Previous UpVote, So we need to reverse it's state between(Soft deleted or Not)
            if (is_null($previousDownVote->deleted_at)) { //It's UpVoted
                $previousDownVote->delete(); //Soft Delete the UpVote
            } else { //restore the UpVote
                $previousDownVote->restore();
                //Remove the down Vote if it's found and is not soft deleted
                if(!is_null($previousUpVote) and is_null($previousUpVote[Constants::FLD_UP_VOTES_DELETED_AT]) ){
                    $previousUpVote->delete();
                }
            }
        }
    }
}
