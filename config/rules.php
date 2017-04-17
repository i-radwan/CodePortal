<?php

use App\Utilities\Constants;

return [
    "question" => [
        "store_answer_validation_rules" => [
            Constants::FLD_QUESTIONS_CONTEST_ID => 'required|integer|exists:' . Constants::TBL_CONTESTS . ',' . Constants::FLD_CONTESTS_ID,
            Constants::FLD_QUESTIONS_PROBLEM_ID => 'required|integer|exists:' . Constants::TBL_PROBLEMS . ',' . Constants::FLD_PROBLEMS_ID,
            Constants::FLD_QUESTIONS_USER_ID => 'required|integer|exists:' . Constants::TBL_USERS . ',' . Constants::FLD_USERS_ID,
            Constants::FLD_QUESTIONS_TITLE => 'required|max:255',
            Constants::FLD_QUESTIONS_CONTENT => 'required|min:50',
            Constants::FLD_QUESTIONS_STATUS => 'Regex:/([01])/',
            Constants::FLD_QUESTIONS_ANSWER => 'required',
            Constants::FLD_QUESTIONS_ADMIN_ID => 'required|integer|exists:' . Constants::TBL_USERS . ',' . Constants::FLD_USERS_ID
        ]
    ],
];