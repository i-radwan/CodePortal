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
        $this->processVote(Post::class, $postID, 1);
        return redirect()->back();
    }

    /**
     * @param $commentID
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function upVoteComment($commentID)
    {
        $this->processVote(Comment::class, $commentID, 1);
        return redirect()->back();
    }

    /**
     * @param $comment
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function downVoteComment($comment)
    {
        $this->processVote(Comment::class, $comment, Constants::RESOURCE_VOTE_TYPE_UP);
        return redirect()->back();
    }

    /**
     * @param $post
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function downVotePost($post)
    {
        $this->processVote(Post::class, $post, Constants::RESOURCE_VOTE_TYPE_DOWN);
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
        $previousVote = Vote::withTrashed()
            ->where(Constants::FLD_VOTES_TYPE, $type)
            ->where(Constants::FLD_VOTES_RESOURCE_ID, $id)
            ->where(Constants::FLD_VOTES_RESOURCE_TYPE, $voteType)
            ->where(Constants::FLD_VOTES_USER_ID, Auth::user()[Constants::FLD_USERS_ID])
            ->first();

        // Down/Up vote check
        $previousComplementaryVote = Vote::withTrashed()
            ->where(Constants::FLD_VOTES_TYPE, $type)
            ->where(Constants::FLD_VOTES_RESOURCE_ID, $id)
            ->where(Constants::FLD_VOTES_RESOURCE_TYPE, Constants::RESOURCE_VOTE_TYPE_UP - $voteType)
            ->where(Constants::FLD_VOTES_USER_ID, Auth::user()[Constants::FLD_USERS_ID])
            ->first();

        if (is_null($previousVote)) { // No previous vote

            // Add new vote
            $vote = new Vote([
                Constants::FLD_VOTES_USER_ID => Auth::user()[Constants::FLD_USERS_ID],
                Constants::FLD_VOTES_RESOURCE_ID => $id,
                Constants::FLD_VOTES_RESOURCE_TYPE => $type,
                Constants::FLD_VOTES_TYPE => $voteType,
            ]);

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
