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
     * @param $postID
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function upVotePost($postID)
    {
        $this->processVote(Constants::RESOURCE_VOTE_POST, $postID, Constants::RESOURCE_VOTE_TYPE_UP);
        return redirect()->back();
    }

    /**
     * @param $commentID
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function upVoteComment($commentID)
    {
        $this->processVote(Constants::RESOURCE_VOTE_COMMENT, $commentID, Constants::RESOURCE_VOTE_TYPE_UP);
        return redirect()->back();
    }

    /**
     * @param $comment
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function downVoteComment($comment)
    {
        $this->processVote(Constants::RESOURCE_VOTE_COMMENT, $comment, Constants::RESOURCE_VOTE_TYPE_DOWN);
        return redirect()->back();
    }

    /**
     * @param $post
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function downVotePost($post)
    {
        $this->processVote(Constants::RESOURCE_VOTE_POST, $post, Constants::RESOURCE_VOTE_TYPE_DOWN);
        return redirect()->back();
    }

    /**
     * Handle the Up/Down Vote with removing the Down/Up Vote if it's already found active
     *
     * @param $type
     * @param $id
     * @param $voteType
     */
    public function processVote($type, $id, $voteType)
    {
        // Check for the previous up and down votes for the single resource with $id

        // Up/Down vote check
        $previousVote = Vote::ofResourceType($type)
            ->ofResource($id)
            ->ofType($voteType)
            ->ofUser(Auth::user()[Constants::FLD_USERS_ID])
            ->withTrashed()
            ->first();

        // Down/Up vote check
        $previousComplementaryVote = Vote::ofResourceType($type)
            ->ofResource($id)
            ->ofType((string)(intval(Constants::RESOURCE_VOTE_TYPE_UP) - intval($voteType)))
            ->ofUser(Auth::user()[Constants::FLD_USERS_ID])
            ->withTrashed()
            ->first();

        if (is_null($previousVote)) { // No previous vote
            // Add new vote
            $vote = new Vote([
                Constants::FLD_VOTES_TYPE => (string)$voteType
            ]);
            // Associate user
            $vote->user()->associate(Auth::user());

            // Associate resource
            $vote[Constants::FLD_VOTES_RESOURCE_TYPE] = $type;

            if ($type == Constants::RESOURCE_VOTE_POST) {
                $vote->resource()->associate(Post::find($id));
            } else if ($type == Constants::RESOURCE_VOTE_COMMENT) {
                $vote->resource()->associate(Comment::find($id));
            }

            // Check if Saved Successfully
            if ($vote->save()) {

                // Remove the complement vote if it's found and is not soft deleted
                if (!is_null($previousComplementaryVote) and is_null($previousComplementaryVote[Constants::FLD_VOTES_DELETED_AT])) {
                    $previousComplementaryVote->delete();
                }
            }
        } else { // There Exists A Previous Vote, So we need to reverse it's state between(Soft deleted or Not)

            if (is_null($previousVote[Constants::FLD_VOTES_DELETED_AT])) {
                $previousVote->delete(); // Soft Delete the Vote

            } else { // restore the Vote

                $previousVote->restore();

                // Remove the down Vote if it's found and is not soft deleted
                if (!is_null($previousComplementaryVote) and is_null($previousComplementaryVote[Constants::FLD_VOTES_DELETED_AT])) {
                    $previousComplementaryVote->delete();
                }
            }
        }
    }
}
