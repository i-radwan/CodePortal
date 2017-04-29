<?php

namespace App\Http\Controllers;

use App\Utilities\Constants;
use Auth;

use App\Models\Vote;
use App\Models\Post;
use App\Models\Comment;


class VoteController extends Controller
{
    /**
     * @param $post
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function upVotePost($post){
        $this->processVote(Post::class, $post, 1);
        return redirect()->back();
    }

    /**
     * @param $comment
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function upVoteComment($comment){
        $this->processVote(Comment::class, $comment, 1);
        return redirect()->back();
    }

    /**
     * @param $comment
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function downVoteComment($comment){
        $this->processVote(Comment::class, $comment, 0);
        return redirect()->back();
    }

    /**
     * @param $post
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function downVotePost($post){
        $this->processVote(Post::class, $post, 0);
        return redirect()->back();
    }

    /**
     * Handle the Up/Down Vote with removing the Down/Up Vote if it's already found active
     * @param $type
     * @param $id
     * @param $voteType
     */
    public function processVote($type, $id, $voteType){
        //Check for the previous up and down votes for the single post with $id
        //UP
        $previousVote = Vote::withTrashed()->whereVotesType($type)->whereVotesId($id)->whereUserId(Auth::id())->whereType($voteType)->first();
        //DOWN
        $previousComplementaryVote = Vote::withTrashed()->whereVotesType($type)->whereVotesId($id)->whereUserId(Auth::id())->whereType(1 - $voteType)->first();

        if(is_null($previousVote)){ //No Previous Up Vote applied for this post with that id
            //Add New Up Vote
            $upVote = new Vote([
                Constants::FLD_VOTES_USER_ID=> Auth::id(),
                Constants::FLD_VOTES_VOTED_ID   => $id,
                Constants::FLD_VOTES_VOTED_TYPE => $type,
                Constants::FLD_VOTES_TYPE => $voteType,
            ]);
            //Check if Saved Successfully
            if($upVote->save()){
                //Remove the down Vote if it's found and is not soft deleted
                if(!is_null($previousComplementaryVote) and is_null($previousComplementaryVote[Constants::FLD_VOTES_DELETED_AT]) ){
                    $previousComplementaryVote->delete();
                }
            }
        }
        else { //There Exists A Previous Vote, So we need to reverse it's state between(Soft deleted or Not)
            if (is_null($previousVote->deleted_at)) { //It's UpVoted
                $previousVote->delete(); //Soft Delete the Vote
            } else { //restore the Vote
                $previousVote->restore();
                //Remove the down Vote if it's found and is not soft deleted
                if(!is_null($previousComplementaryVote) and is_null($previousComplementaryVote[Constants::FLD_VOTES_DELETED_AT]) ){
                    $previousComplementaryVote->delete();
                }
            }
        }
    }

}
