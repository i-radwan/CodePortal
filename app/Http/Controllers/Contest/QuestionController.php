<?php

namespace App\Http\Controllers\Contest;

use Auth;
use Gate;
use Session;
use Redirect;
use App\Models\Question;
use App\Utilities\Constants;
use Illuminate\Http\Request;

class QuestionController
{
    /**
     * Ask question related to the contest problems
     *
     * @param Request $request
     * @param int $contestID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function askQuestion(Request $request, $contestID)
    {
        $user = Auth::user();

        // Check if user is a participant
        $contest = $user->participatingContests()->find($contestID);
        $problemID = $request->get(Constants::FLD_QUESTIONS_PROBLEM_ID);
        $problem = $contest->problems()->find($problemID);

        // Check if contest exists (user participating in it) and the contest is running now
        if ($contest && $contest->isRunning()) {
            Question::askQuestion($request->all(), $user, $contest, $problem);
        }
        else {
            Session::flash('question-error', 'Sorry, you cannot perform this action right now!');
        }

        return Redirect::back();
    }

    /**
     * Save question answer (provided by contest organizers)
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function answerQuestion(Request $request)
    {
        $questionID = $request->get('question_id');
        $questionAnswer = $request->get('question_answer');
        $question = Question::find($questionID);
        $user = Auth::user();

        // Check if question exists
        if ($question && Gate::allows('owner-organizer-contest', $question[Constants::FLD_QUESTIONS_CONTEST_ID])) {
            $question->saveAnswer($questionAnswer, $user);
        }

        return Redirect::back();
    }

    /**
     * Mark question as announcement
     *
     * @param Question $question
     * @return \Illuminate\Http\RedirectResponse
     */
    public function announceQuestion(Question $question)
    {
        // Check if question exists
        if ($question && Gate::allows('owner-organizer-contest', $question[Constants::FLD_QUESTIONS_CONTEST_ID])) {
            $question[Constants::FLD_QUESTIONS_STATUS] = Constants::QUESTION_STATUS_ANNOUNCEMENT;
            $question->save();
        }

        return Redirect::back();
    }

    /**
     * Un-mark question as announcement
     *
     * @param Question $question
     * @return \Illuminate\Http\RedirectResponse
     */
    public function renounceQuestion(Question $question)
    {
        // Check if question exists
        if ($question && Gate::allows('owner-organizer-contest', $question[Constants::FLD_QUESTIONS_CONTEST_ID])) {
            $question[Constants::FLD_QUESTIONS_STATUS] = Constants::QUESTION_STATUS_NORMAL;
            $question->save();
        }

        return Redirect::back();
    }
}