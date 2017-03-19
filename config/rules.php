<?php

use App\Utilities\Constants;

return [
    "user" => [
        "store_validation_rules" => [
            Constants::FLD_USERS_NAME => 'required|max:255',
            Constants::FLD_USERS_USERNAME => 'required|unique:users',
            Constants::FLD_USERS_EMAIL => 'required|email|max:255|unique:users',
            Constants::FLD_USERS_PASSWORD => 'required|min:6|confirmed',
            Constants::FLD_USERS_AGE => 'integer',
            Constants::FLD_USERS_GENDER => 'Regex:/([01])/',
            Constants::FLD_USERS_ROLE => 'Regex:/([01])/',
        ]
    ],
    "contest" => [
        "store_validation_rules" => [
            Constants::FLD_CONTESTS_NAME => 'required|max:100',
            Constants::FLD_CONTESTS_TIME => 'required|date_format:Y-m-d H:i:s|after:today',
            Constants::FLD_CONTESTS_DURATION => 'integer|required|min:1',
            Constants::FLD_CONTESTS_VISIBILITY => 'required|Regex:/([01])/',
        ]
    ],
    "problem" => [
        "store_validation_rules" => [
            Constants::FLD_PROBLEMS_NAME => 'required|max:100',
            Constants::FLD_PROBLEMS_JUDGE_ID => 'integer|required',
            Constants::FLD_PROBLEMS_JUDGE_FIRST_KEY => 'required',
            Constants::FLD_PROBLEMS_JUDGE_SECOND_KEY => 'required',
            Constants::FLD_PROBLEMS_DIFFICULTY => 'integer|required|min:0',
            Constants::FLD_PROBLEMS_SOLVED_COUNT => 'integer|required|min:0',
        ]
    ],
    "submission" => [
        "store_validation_rules" => [
            Constants::FLD_SUBMISSIONS_USER_ID => 'required|integer',
            Constants::FLD_SUBMISSIONS_PROBLEM_ID => 'required|integer',
            Constants::FLD_SUBMISSIONS_JUDGE_SUBMISSION_ID => 'required|integer',
            Constants::FLD_SUBMISSIONS_LANGUAGE_ID => 'required|integer',
            Constants::FLD_SUBMISSIONS_SUBMISSION_TIME => 'required|integer',
            Constants::FLD_SUBMISSIONS_EXECUTION_TIME => 'required|integer',
            Constants::FLD_SUBMISSIONS_CONSUMED_MEMORY => 'required|integer',
            Constants::FLD_SUBMISSIONS_VERDICT => 'integer|required|min:0|max:' . count(\App\Utilities\Constants::SUBMISSION_VERDICT),
        ]
    ],
    "question" => [
        "store_validation_rules" => [
            Constants::FLD_QUESTIONS_TITLE => 'required|max:255',
            Constants::FLD_QUESTIONS_CONTENT => 'required',
            Constants::FLD_QUESTIONS_CONTEST_ID => 'required|integer',
            Constants::FLD_QUESTIONS_USER_ID => 'required|integer',
            Constants::FLD_QUESTIONS_STATUS => 'Regex:/([01])/',
        ],
        "store_answer_validation_rules" => [
            Constants::FLD_QUESTIONS_TITLE => 'required|max:255',
            Constants::FLD_QUESTIONS_CONTENT => 'required',
            Constants::FLD_QUESTIONS_ANSWER => 'required',
            Constants::FLD_QUESTIONS_CONTEST_ID => 'required|integer',
            Constants::FLD_QUESTIONS_USER_ID => 'required|integer',
            Constants::FLD_QUESTIONS_ADMIN_ID => 'required|integer',
            Constants::FLD_QUESTIONS_STATUS => 'Regex:/([01])/',
        ]
    ],
    "judge" => [
        "store_validation_rules" => [
            Constants::FLD_JUDGES_ID => 'required|unique:judges|integer|min:0',
            Constants::FLD_JUDGES_NAME => 'required|unique:judges|max:100',
            Constants::FLD_JUDGES_LINK => 'required|unique:judges|max:100|url',
        ]
    ],
    "tag" => [
        "store_validation_rules" => [
            Constants::FLD_TAGS_NAME => 'required|unique:tags|max:50',
        ]
    ],
    "language" => [
        "store_validation_rules" => [
            Constants::FLD_LANGUAGES_NAME => 'required|unique:languages|max:50',
        ]
    ],
];