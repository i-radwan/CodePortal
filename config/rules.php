<?php

use App\Utilities\Constants;

return [
    "user" => [
        "store_validation_rules" => [
            Constants::FLD_USERS_USERNAME => 'required|unique:' . Constants::TBL_USERS,
            Constants::FLD_USERS_EMAIL => 'required|email|max:50|unique:' . Constants::TBL_USERS,
            Constants::FLD_USERS_PASSWORD => 'required|min:6|confirmed',
            Constants::FLD_USERS_NAME => 'required|max:20',
            Constants::FLD_USERS_FIRST_NAME => 'max:20',
            Constants::FLD_USERS_LAST_NAME => 'max:20',
            Constants::FLD_USERS_GENDER => 'Regex:/([01])/',
            Constants::FLD_USERS_BIRTHDATE => 'date',       //TODO: add more validation on birthdate
            Constants::FLD_USERS_ROLE => 'Regex:/([01])/',
        ]
    ],
    "contest" => [
        "store_validation_rules" => [
            Constants::FLD_CONTESTS_NAME => 'required|max:100',
            Constants::FLD_CONTESTS_OWNER_ID => 'required|exists:' . Constants::TBL_USERS . ',' . Constants::FLD_USERS_ID,
            Constants::FLD_CONTESTS_TIME => 'required|date_format:Y-m-d H:i:s|after:today',
            Constants::FLD_CONTESTS_DURATION => 'integer|required|min:1',
            Constants::FLD_CONTESTS_VISIBILITY => 'required|Regex:/([01])/'
        ]
    ],
    "problem" => [
        "store_validation_rules" => [
            Constants::FLD_PROBLEMS_NAME => 'required|max:255',
            Constants::FLD_PROBLEMS_JUDGE_ID => 'integer|required|exists:' . Constants::TBL_JUDGES . ',' . Constants::FLD_JUDGES_ID,
            Constants::FLD_PROBLEMS_JUDGE_FIRST_KEY => 'required|min:0',
            Constants::FLD_PROBLEMS_JUDGE_SECOND_KEY => 'required|max:10',
            Constants::FLD_PROBLEMS_SOLVED_COUNT => 'integer|required|min:0'
        ]
    ],
    "submission" => [
        "store_validation_rules" => [
            Constants::FLD_SUBMISSIONS_USER_ID => 'required|integer|exists:' . Constants::TBL_USERS . ',' . Constants::FLD_USERS_ID,
            Constants::FLD_SUBMISSIONS_PROBLEM_ID => 'required|integer|exists:' . Constants::TBL_PROBLEMS . ',' . Constants::FLD_PROBLEMS_ID,
            Constants::FLD_SUBMISSIONS_JUDGE_SUBMISSION_ID => 'required|integer|unique:' . Constants::TBL_SUBMISSIONS,
            Constants::FLD_SUBMISSIONS_LANGUAGE_ID => 'required|integer|exists:' . Constants::TBL_LANGUAGES . ',' . Constants::FLD_LANGUAGES_ID,
            Constants::FLD_SUBMISSIONS_SUBMISSION_TIME => 'required|integer|min:0',
            Constants::FLD_SUBMISSIONS_EXECUTION_TIME => 'required|integer|min:0',
            Constants::FLD_SUBMISSIONS_CONSUMED_MEMORY => 'required|integer|min:0',
            Constants::FLD_SUBMISSIONS_VERDICT => 'integer|required|min:0|max:' . Constants::VERDICT_COUNT
        ]
    ],
    "question" => [
        "store_validation_rules" => [
            Constants::FLD_QUESTIONS_CONTEST_ID => 'required|integer|exists:' . Constants::TBL_CONTESTS . ',' . Constants::FLD_CONTESTS_ID,
            Constants::FLD_QUESTIONS_PROBLEM_ID => 'required|integer|exists:' . Constants::TBL_PROBLEMS . ',' . Constants::FLD_PROBLEMS_ID,
            Constants::FLD_QUESTIONS_USER_ID => 'required|integer|exists:' . Constants::TBL_USERS . ',' . Constants::FLD_USERS_ID,
            Constants::FLD_QUESTIONS_TITLE => 'required|max:255',
            Constants::FLD_QUESTIONS_CONTENT => 'required|min:50',
            Constants::FLD_QUESTIONS_STATUS => 'Regex:/([01])/'
        ],
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
    "judge" => [
        "store_validation_rules" => [
            Constants::FLD_JUDGES_ID => 'required|unique:' . Constants::TBL_JUDGES . '|integer|min:0',
            Constants::FLD_JUDGES_NAME => 'required|unique:' . Constants::TBL_JUDGES . '|max:100',
            Constants::FLD_JUDGES_LINK => 'required|unique:' . Constants::TBL_JUDGES . '|max:100|url'
        ]
    ],
    "tag" => [
        "store_validation_rules" => [
            Constants::FLD_TAGS_NAME => 'required|unique:' . Constants::TBL_TAGS . '|max:50'
        ]
    ],
    "language" => [
        "store_validation_rules" => [
            Constants::FLD_LANGUAGES_NAME => 'required|unique:' . Constants::TBL_LANGUAGES . '|max:50'
        ]
    ],
];