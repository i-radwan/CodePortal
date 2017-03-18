<?php

namespace App\Utilities;

class Constants
{
    const PROBLEMS_COUNT_PER_PAGE = 30;

    const CODEFORCES_NAME = "Codeforces";
    const CODEFORCES_LINK = "http://codeforces.com/";
    const CODEFORCES_PROBLEM_LINK = "http://codeforces.com/problemset/problem/{contestId}/{contestIndex}";

    const UVA_NAME = "UVa Online Judge";
    const UVA_LINK = "https://uva.onlinejudge.org/";
    const UVA_PROBLEM_LINK = "https://uva.onlinejudge.org/index.php?option=com_onlinejudge&Itemid=8&page=show_problem&problem={problemId}";

    const LIVE_ARCHIVE_NAME = "Live Archive";
    const LIVE_ARCHIVE_LINK = "https://icpcarchive.ecs.baylor.edu/";
    const LIVE_ARCHIVE_PROBLEM_LINK = "https://icpcarchive.ecs.baylor.edu/index.php?option=com_onlinejudge&Itemid=8&page=show_problem&problem={problemId}";

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
        "FAILED" => '0',
        "OK" => '1',
        "PARTIAL" => '2',
        "COMPILATION_ERROR" => '3',
        "RUNTIME_ERROR" => '4',
        "WRONG_ANSWER" => '5',
        "PRESENTATION_ERROR" => '6',
        "TIME_LIMIT_EXCEEDED" => '7',
        "MEMORY_LIMIT_EXCEEDED" => '8',
        "IDLENESS_LIMIT_EXCEEDED" => '9',
        "SECURITY_VIOLATED" => '10',
        "CRASHED" => '11',
        "INPUT_PREPARATION_CRASHED" => '12',
        "CHALLENGED" => '13',
        "SKIPPED" => '14',
        "TESTING" => '15',
        "REJECTED" => '16',
        "UNKNOWN" => '17'
    ];

    //
    // Database constants
    //
    // ==================

    //
    // Tables
    //

    // Model tables
    const TBL_USERS = "users";
    const TBL_PASSWORD_RESETS = "password_resets";
    const TBL_CONTESTS = "contests";
    const TBL_PROBLEMS = "problems";
    const TBL_SUBMISSIONS = "submissions";
    const TBL_QUESTIONS = "questions";
    const TBL_JUDGES = "judges";
    const TBL_TAGS = "tags";
    const TBL_LANGUAGES = "languages";

    // Pivot tables
    const TBL_USER_HANDLES = "user_handles";
    const TBL_CONTEST_PROBLEMS = "contest_problems";
    const TBL_CONTEST_PARTICIPANTS = "contest_participants";
    const TBL_CONTEST_ADMINS = "contest_admins";
    const TBL_PROBLEM_TAGS = "problem_tags";


    //
    // Fields
    //

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
    const FLD_USERS_REMEMBER_TOKEN = "remember_token";

    // Password resets
    const FLD_PASSWORD_RESETS_EMAIL = "email";
    const FLD_PASSWORD_RESETS_TOKEN = "token";
    const FLD_PASSWORD_RESETS_CREATED_AT = "created_at";

    // Contests
    const FLD_CONTESTS_ID = "id";
    const FLD_CONTESTS_NAME = "name";
    const FLD_CONTESTS_TIME = "time";
    const FLD_CONTESTS_DURATION = "duration";
    const FLD_CONTESTS_VISIBILITY = "visibility";

    // Problems
    const FLD_PROBLEMS_ID = "id";
    const FLD_PROBLEMS_JUDGE_ID = "judge_id";
    const FLD_PROBLEMS_JUDGE_FIRST_KEY = "judge_first_key";
    const FLD_PROBLEMS_JUDGE_SECOND_KEY = "judge_second_key";
    const FLD_PROBLEMS_NAME = "name";
    const FLD_PROBLEMS_DIFFICULTY = "difficulty";
    const FLD_PROBLEMS_ACCEPTED_SUBMISSIONS_COUNT = "accepted_submissions_count";

    // Submissions
    const FLD_SUBMISSIONS_ID = "id";
    const FLD_SUBMISSIONS_USER_ID = "user_id";
    const FLD_SUBMISSIONS_PROBLEM_ID = "problem_id";
    const FLD_SUBMISSIONS_JUDGE_SUBMISSION_ID = "judge_submission_id";
    const FLD_SUBMISSIONS_LANGUAGE_ID = "language_id";
    const FLD_SUBMISSIONS_SUBMISSION_TIME = "submission_time";
    const FLD_SUBMISSIONS_EXECUTION_TIME = "execution_time";
    const FLD_SUBMISSIONS_CONSUMED_MEMORY = "consumed_memory";
    const FLD_SUBMISSIONS_VERDICT = "verdict";

    // Questions
    const FLD_QUESTIONS_ID = "id";
    const FLD_QUESTIONS_TITLE = "title";
    const FLD_QUESTIONS_CONTENT = "content";
    const FLD_QUESTIONS_ANSWER = "answer";
    const FLD_QUESTIONS_STATUS = "status";
    const FLD_QUESTIONS_ADMIN_ID = "admin_id";
    const FLD_QUESTIONS_CONTEST_ID = "contest_id";
    const FLD_QUESTIONS_USER_ID = "user_id";

    // Judges
    const FLD_JUDGES_ID = "id";
    const FLD_JUDGES_NAME = "name";
    const FLD_JUDGES_LINK = "link";
    const FLD_JUDGES_API_LINK = "api_link";

    // Tags
    const FLD_TAGS_ID = "id";
    const FLD_TAGS_NAME = "name";

    // Languages
    const FLD_LANGUAGES_ID = "id";
    const FLD_LANGUAGES_NAME = "name";

    // User handles
    const FLD_USER_HANDLES_USER_ID = "user_id";
    const FLD_USER_HANDLES_JUDGE_ID = "judge_id";
    const FLD_USER_HANDLES_HANDLE = "handle";

    // Contest problems
    const FLD_CONTEST_PROBLEMS_PROBLEM_ID = "problem_id";
    const FLD_CONTEST_PROBLEMS_CONTEST_ID = "contest_id";

    // Contest participants
    const FLD_CONTEST_PARTICIPANTS_USER_ID = "user_id";
    const FLD_CONTEST_PARTICIPANTS_CONTEST_ID = "contest_id";

    // Contest admins
    const FLD_CONTEST_ADMINS_ADMIN_ID = "user_id";
    const FLD_CONTEST_ADMINS_CONTEST_ID = "contest_id";

    // Problem tags
    const FLD_PROBLEM_TAGS_PROBLEM_ID = "problem_id";
    const FLD_PROBLEM_TAGS_TAG_ID = "tag_id";
}