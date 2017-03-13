<?php
/**
 * Created by PhpStorm.
 * User: ibrahimradwan
 * Date: 3/13/17
 * Time: 8:20 PM
 */

namespace App\Utilities;


class Constants
{
    const USER_ROLE = [
        "USER" => 0,
        "ADMIN" => 1
    ];
    const PARTICIPANT_ROLE = [
        "USER" => 0,
        "ORGANIZER" => 1
    ];
    const USER_GENDER = [
        "MALE" => 0,
        "FEMALE" => 1
    ];
    const CONTEST_VISIBILITY = [
        "PUBLIC" => '0',
        "PRIVATE" => '1'
    ];
    const QUESTION_STATUS = [
        "NORMAL" => 0,
        "ANNOUNCEMENT" => 1
    ];
    const SUBMISSION_VERDICT = [
        "FAILED" => 0,
        "OK" => 1,
        "PARTIAL" => 2,
        "COMPILATION_ERROR" => 3,
        "RUNTIME_ERROR" => 4,
        "WRONG_ANSWER" => 5,
        "PRESENTATION_ERROR" => 6,
        "TIME_LIMIT_EXCEEDED" => 7,
        "MEMORY_LIMIT_EXCEEDED" => 8,
        "IDLENESS_LIMIT_EXCEEDED" => 9,
        "SECURITY_VIOLATED" => 10,
        "CRASHED" => 11,
        "INPUT_PREPARATION_CRASHED" => 12,
        "CHALLENGED" => 13,
        "SKIPPED" => 14,
        "TESTING" => 15,
        "REJECTED" => 16,
        "UNKNOWN" => 17
    ];
    const PROBLEMS_COUNT_PER_PAGE = 30;

    // Database constants

    // Tables
    const TBL_USERS = "users";
    const TBL_PASSWORD_RESETS = "password_resets";
    const TBL_CONTESTS = "contests";
    const TBL_PROBLEMS = "problems";
    const TBL_QUESTIONS = "questions";
    const TBL_JUDGES = "judges";
    const TBL_TAGS = "tags";
    const TBL_USER_HANDLES = "user_handles";
    const TBL_LANGUAGES = "languages";
    const TBL_SUBMISSIONS = "submissions";
    const TBL_PARTICIPANTS = "participants";
    const TBL_CONTEST_PROBLEM = "contest_problem";
    const TBL_CONTEST_ADMIN = "contest_admin";
    const TBL_PROBLEM_TAG = "problem_tag";

    // Fields

    // Users
    const FLD_USERS_ID = "id";
    const FLD_USERS_NAME = "name";
    const FLD_USERS_EMAIL = "email";
    const FLD_USERS_PASSWORD = "password";
    const FLD_USERS_USERNAME = "username";
    const FLD_USERS_GENDER = "gender";
    const FLD_USERS_AGE = "age";
    const FLD_USERS_PROFILE_PIC = "profile_pic";
    const FLD_USERS_COUNTRY = "country";
    const FLD_USERS_ROLE = "role";
    // Password resets
    const FLD_PASSWORD_RESETS_EMAIL = "email";
    const FLD_PASSWORD_RESETS_TOKEN = "token";
    const FLD_PASSWORD_RESETS_CREATED_AT = "created_at";
    // Problem tag
    const FLD_PROBLEM_TAG_PROBLEM_ID = "problem_id";
    const FLD_PROBLEM_TAG_TAG_ID = "tag_id";
    // Contest admin
    const FLD_CONTEST_ADMIN_ADMIN_ID = "user_id";
    const FLD_CONTEST_ADMIN_CONTEST_ID = "contest_id";
    // Contest problem
    const FLD_CONTEST_PROBLEM_PROBLEM_ID = "problem_id";
    const FLD_CONTEST_PROBLEM_CONTEST_ID = "contest_id";
    // Participants
    const FLD_PARTICIPANTS_USER_ID = "user_id";
    const FLD_PARTICIPANTS_CONTEST_ID = "contest_id";
    // Submissions
    const FLD_SUBMISSIONS_ID = "id";
    const FLD_SUBMISSIONS_PROBLEM_ID = "problem_id";
    const FLD_SUBMISSIONS_USER_ID = "user_id";
    const FLD_SUBMISSIONS_SUBMISSION_ID = "submission_id";
    const FLD_SUBMISSIONS_LANGUAGE_ID = "language_id";
    const FLD_SUBMISSIONS_EXECUTION_TIME = "execution_time";
    const FLD_SUBMISSIONS_CONSUMED_MEMORY = "consumed_memory";
    const FLD_SUBMISSIONS_VERDICT = "verdict";
    // User handles
    const FLD_USER_HANDLES_USER_ID = "user_id";
    const FLD_USER_HANDLES_JUDGE_ID = "judge_id";
    const FLD_USER_HANDLES_HANDLE = "handle";
    // Tags
    const FLD_TAGS_ID = "id";
    const FLD_TAGS_NAME = "name";
    // Judges
    const FLD_JUDGES_ID = "id";
    const FLD_JUDGES_NAME = "name";
    const FLD_JUDGES_LINK = "link";
    const FLD_JUDGES_API_LINK = "api_link";
    // Languages
    const FLD_LANGUAGES_ID = "id";
    const FLD_LANGUAGES_NAME = "name";
    // Contests
    const FLD_CONTESTS_ID = "id";
    const FLD_CONTESTS_NAME = "name";
    const FLD_CONTESTS_TIME = "time";
    const FLD_CONTESTS_DURATION = "duration";
    const FLD_CONTESTS_VISIBILITY = "visibility";
    // Problems
    const FLD_PROBLEMS_ID = "id";
    const FLD_PROBLEMS_JUDGE_ID = "judge_id";
    const FLD_PROBLEMS_NAME = "name";
    const FLD_PROBLEMS_DIFFICULTY = "difficulty";
    const FLD_PROBLEMS_ACCEPTED_SUBMISSIONS_COUNT = "accepted_submissions_count";
    // Questions
    const FLD_QUESTIONS_ID = "id";
    const FLD_QUESTIONS_TITLE = "title";
    const FLD_QUESTIONS_CONTENT = "content";
    const FLD_QUESTIONS_ANSWER = "answer";
    const FLD_QUESTIONS_STATUS = "status";
    const FLD_QUESTIONS_ADMIN_ID = "admin_id";
    const FLD_QUESTIONS_CONTEST_ID = "contest_id";
    const FLD_QUESTIONS_USER_ID = "user_id";
}