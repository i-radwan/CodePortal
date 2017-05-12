<?php

namespace App\Http\Controllers\Contest;

use Auth;
use Gate;
use Session;
use Redirect;
use App\Models\User;
use App\Models\Problem;
use App\Models\Contest;
use App\Models\Question;
use App\Utilities\Constants;
use App\Utilities\Utilities;
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

        $problems = $contest->problemStatistics()->get();
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

    /**
     * Get contest questions info
     *
     * @param Contest $contest
     * @param User $user
     * return mixed
     */
    private function getQuestionsInfo(Contest $contest, User $user)
    {
        $isOwnerOrOrganizer = \Gate::forUser($user)->allows('owner-organizer-contest', [$contest[Constants::FLD_CONTESTS_ID]]);

        // Get contest announcements
        $announcements = $contest->announcements()->get();

        // If user is logged in and not organizer, get his questions too
        if ($user && !$isOwnerOrOrganizer) {
            // Get user specific questions
            $questions = $user->questions($contest[Constants::FLD_CONTESTS_ID])->get();

            // Merge announcements and user questions
            $announcements = $announcements->merge($questions);
        } else if ($user && $isOwnerOrOrganizer) {

            // If admin get all questions
            $questions = $contest->questions()
                ->where(Constants::FLD_QUESTIONS_STATUS, '!=', Constants::QUESTION_STATUS_ANNOUNCEMENT);

            // Merge announcements and all questions
            $announcements = $announcements->merge($questions->get());
        }

        // Get extra data from foreign keys
        foreach ($announcements as $announcement) {
            // Get admin username from id if answer is provided
            if ($announcement[Constants::FLD_QUESTIONS_ADMIN_ID])
                $announcement[Constants::FLD_QUESTIONS_ADMIN_ID] =
                    User::find($announcement[Constants::FLD_QUESTIONS_ADMIN_ID])->username;

            // Get problem number from id
            $announcement[Constants::FLD_QUESTIONS_PROBLEM_ID] =
                Utilities::generateProblemNumber(Problem::find($announcement[Constants::FLD_QUESTIONS_PROBLEM_ID]));
        }

        return $announcements;
    }
}