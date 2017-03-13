<?php
/**
 * Created by PhpStorm.
 * User: ibrahimradwan
 * Date: 3/10/17
 * Time: 10:52 AM
 */
return [
    "user" => [
        "store_validation_rules" => [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
            'username' => 'required|unique:users',
            'age' => 'integer',
            'gender' => 'Regex:/([01])/',
            'role' => 'Regex:/([01])/',
        ]
    ],
    "contest" => [
        "store_validation_rules" => [
            'name' => 'required|max:100',
            'time' => 'required|date_format:Y-m-d H:i:s|after:today',
            'duration' => 'required|greater_than:0|integer',
            'visibility' => 'required|Regex:/([01])/',
        ]
    ],
    "judge" => [
        "store_validation_rules" => [
            'name' => 'required|unique:judges|max:100',
            'link' => 'required|unique:judges|max:100|url',
            'api_link' => 'required|max:255|url',
        ]
    ],
    "language" => [
        "store_validation_rules" => [
            'name' => 'required|unique:languages|max:50',
        ]
    ],
    "tag" => [
        "store_validation_rules" => [
            'name' => 'required|unique:tags|max:50',
        ]
    ],
    "problem" => [
        "store_validation_rules" => [
            'name' => 'required|max:100',
            'judge_id' => 'integer|required',
            'difficulty' => 'integer|required|greater_than:0',
            'accepted_submissions_count' => 'integer|required|greater_than:0',
        ]
    ],
    "question" => [
        "store_validation_rules" => [
            'title' => 'required|max:255',
            'content' => 'required',
            'contest_id' => 'required|integer',
            'user_id' => 'required|integer',
            'status' => 'Regex:/([01])/',
        ],
        "store_answer_validation_rules" => [
            'title' => 'required|max:255',
            'content' => 'required',
            'answer' => 'required',
            'contest_id' => 'required|integer',
            'user_id' => 'required|integer',
            'admin_id' => 'required|integer',
            'status' => 'Regex:/([01])/',
        ]
    ],
    "submission" => [
        "store_validation_rules" => [
            'problem_id' => 'required|integer',
            'user_id' => 'required|integer',
            'submission_id' => 'required',
            'language_id' => 'required|integer',
            'execution_time' => 'required|integer',
            'consumed_memory' => 'required|integer',
            'verdict' => 'integer|required|greater_than:-1|less_than:' . count(\App\Utilities\Constants::SUBMISSION_VERDICT),
        ]
    ],
];