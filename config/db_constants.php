<?php
/**
 * Created by PhpStorm.
 * User: ibrahimradwan
 * Date: 3/12/17
 * Time: 4:27 PM
 */
return [
    "TABLES" => [
        "TBL_USERS" => "users",
        "TBL_PASSWORD_RESETS" => "password_resets",
        "TBL_CONTESTS" => "contests",
        "TBL_PROBLEMS" => "problems",
        "TBL_QUESTIONS" => "questions",
        "TBL_JUDGES" => "judges",
        "TBL_TAGS" => "tags",
        "TBL_USER_HANDLES" => "user_handles",
        "TBL_LANGUAGES" => "languages",
        "TBL_SUBMISSIONS" => "submissions",
        "TBL_PARTICIPANTS" => "participants",
        "TBL_CONTEST_PROBLEM" => "contest_problem",
        "TBL_CONTEST_ADMIN" => "contest_admin",
        "TBL_PROBLEM_TAG" => "problem_tag",
    ],
    "FIELDS" => [
        // Users
        "FLD_USERS_ID" => "id",
        "FLD_USERS_NAME" => "name",
        "FLD_USERS_EMAIL" => "email",
        "FLD_USERS_PASSWORD" => "password",
        "FLD_USERS_USERNAME" => "username",
        "FLD_USERS_GENDER" => "gender",
        "FLD_USERS_AGE" => "age",
        "FLD_USERS_PROFILE_PIC" => "profile_pic",
        "FLD_USERS_COUNTRY" => "country",
        "FLD_USERS_ROLE" => "role",
        // Password resets
        "FLD_PASSWORD_RESETS_EMAIL" => "email",
        "FLD_PASSWORD_RESETS_TOKEN" => "token",
        "FLD_PASSWORD_RESETS_CREATED_AT" => "created_at",
        // Problem tag
        "FLD_PROBLEM_TAG_PROBLEM_ID" => "problem_id",
        "FLD_PROBLEM_TAG_TAG_ID" => "tag_id",
        // Contest admin
        "FLD_CONTEST_ADMIN_ADMIN_ID" => "user_id",
        "FLD_CONTEST_ADMIN_CONTEST_ID" => "contest_id",
        // Contest problem
        "FLD_CONTEST_PROBLEM_PROBLEM_ID" => "problem_id",
        "FLD_CONTEST_PROBLEM_CONTEST_ID" => "contest_id",
        // Participants
        "FLD_PARTICIPANTS_USER_ID" => "user_id",
        "FLD_PARTICIPANTS_CONTEST_ID" => "contest_id",
        // Submissions
        "FLD_SUBMISSIONS_ID" => "id",
        "FLD_SUBMISSIONS_PROBLEM_ID" => "problem_id",
        "FLD_SUBMISSIONS_USER_ID" => "user_id",
        "FLD_SUBMISSIONS_SUBMISSION_ID" => "submission_id",
        "FLD_SUBMISSIONS_LANGUAGE_ID" => "language_id",
        "FLD_SUBMISSIONS_EXECUTION_TIME" => "execution_time",
        "FLD_SUBMISSIONS_CONSUMED_MEMORY" => "consumed_memory",
        "FLD_SUBMISSIONS_VERDICT" => "verdict",
        // User handles
        "FLD_USER_HANDLES_USER_ID" => "user_id",
        "FLD_USER_HANDLES_JUDGE_ID" => "judge_id",
        "FLD_USER_HANDLES_HANDLE" => "handle",
        // Tags
        "FLD_TAGS_ID" => "id",
        "FLD_TAGS_NAME" => "name",
        // Judges
        "FLD_JUDGES_ID" => "id",
        "FLD_JUDGES_NAME" => "name",
        "FLD_JUDGES_LINK" => "link",
        "FLD_JUDGES_API_LINK" => "api_link",
        // Languages
        "FLD_LANGUAGES_ID" => "id",
        "FLD_LANGUAGES_NAME" => "name",
        // Contests
        "FLD_CONTESTS_ID" => "id",
        "FLD_CONTESTS_NAME" => "name",
        "FLD_CONTESTS_TIME" => "time",
        "FLD_CONTESTS_DURATION" => "duration",
        "FLD_CONTESTS_VISIBILITY" => "visibility",
        // Problems
        "FLD_PROBLEMS_ID" => "id",
        "FLD_PROBLEMS_JUDGE_ID" => "judge_id",
        "FLD_PROBLEMS_NAME" => "name",
        "FLD_PROBLEMS_DIFFICULTY" => "difficulty",
        "FLD_PROBLEMS_ACCEPTED_SUBMISSIONS_COUNT" => "accepted_submissions_count",
        // Questions
        "FLD_QUESTIONS_ID" => "id",
        "FLD_QUESTIONS_TITLE" => "title",
        "FLD_QUESTIONS_CONTENT" => "content",
        "FLD_QUESTIONS_ANSWER" => "answer",
        "FLD_QUESTIONS_STATUS" => "status",
        "FLD_QUESTIONS_ADMIN_ID" => "admin_id",
        "FLD_QUESTIONS_CONTEST_ID" => "contest_id",
        "FLD_QUESTIONS_USER_ID" => "user_id",
    ]
];