<?php
/**
 * Created by PhpStorm.
 * User: ibrahimradwan
 * Date: 3/10/17
 * Time: 10:52 AM
 */
return [
    "contest" => [
        "store_validation_rules" => [
            'name' => 'required|max:100',
            'time' => 'required|date_format:Y-m-d H:i:s|after:today',
            'duration' => 'required|greater_than:0',
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
            'judge_id' => 'required',
            'difficulty' => 'required|greater_than:0',
            'accepted_count' => 'required|greater_than:0',
        ]
    ],
];