<?php

namespace App\Http\Controllers\Contests;

use Auth;
use Gate;
use Session;
use Redirect;
use App\Models\User;
use App\Models\Contest;
use App\Models\Question;
use App\Utilities\Constants;
use Illuminate\Http\Request;

class QuestionController
{

    /**
     * Show single contest questions page
     *
     * Authorization happens in the defined Gate
     *
     * @param Contest $contest
     * @return \Illuminate\View\View
     */
    public function displayContestQuestions(Contest $contest)
    {
        if (!$contest) {
            return redirect(route(Constants::ROUTES_CONTESTS_INDEX)); // contest doesn't exist
        }

        $problems = $contest->problems()->get();
        $questions = $this->getQuestionsInfo($contest, Auth::user());

        return view('contests.contest')
            ->with('contest', $contest)
            ->with('questions', $questions)
            ->with('problems', $problems)
            ->with('view', 'questions')
            ->with('pageTitle', config('app.name') . ' | ' . $contest[Constants::FLD_CONTESTS_NAME]);
    }

    /**
     * Ask question related to the contest problems
     *
     * @param Request $request
     * @param Contest $contest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function askQuestion(Request $request, Contest $contest)
    {
        $user = Auth::user();
        $problem = $contest->problems()->find($request->get(Constants::FLD_QUESTIONS_PROBLEM_ID));

        if ($contest->isRunning()) {
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
        if ($question && Gate::allows('owner-organizer-contest-question', $question)) {
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
        $question[Constants::FLD_QUESTIONS_STATUS] = Constants::QUESTION_STATUS_ANNOUNCEMENT;
        $question->save();
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
        $question[Constants::FLD_QUESTIONS_STATUS] = Constants::QUESTION_STATUS_NORMAL;
        $question->save();
        return Redirect::back();
    }

    /**
     * Get contest questions info
     *
     * @param Contest $contest
     * @param User $user
     * return mixed
     */
    private function getQuestionsInfo(Contest $contest, User $user = null)
    {
        $announcements = $contest->announcements()->get();

        if (!$user) {
            return $announcements;
        }

        $isOwnerOrOrganizer = Gate::forUser($user)->allows('owner-organizer-contest', [$contest]);

        // If admin get un-answered questions
        if ($isOwnerOrOrganizer) {
            $questions = $contest->questions()
                ->where(Constants::FLD_QUESTIONS_STATUS, '!=', Constants::QUESTION_STATUS_ANNOUNCEMENT)
                ->get();
        }
        // Else get user specific questions
        else {
            $questions = $user->questions($contest[Constants::FLD_CONTESTS_ID])->get();
        }

        return $announcements->merge($questions);
    }
}