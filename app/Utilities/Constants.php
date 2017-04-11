<?php

namespace App\Utilities;

class Constants
{
    //region Problems page

    //
    // Problems page constants
    //

    const PROBLEMS_COUNT_PER_PAGE = 30;

    // TODO: add links

    const PROBLEMS_TABLE_HEADINGS = [
        [
            Constants::TABLE_DATA_KEY => "ID",
            Constants::TABLE_HEADINGS_LINK_UP_KEY => "",
            Constants::TABLE_HEADINGS_LINK_DOWN_KEY => "",
        ],
        [
            Constants::TABLE_DATA_KEY => "Name",
            Constants::TABLE_HEADINGS_LINK_UP_KEY => "",
            Constants::TABLE_HEADINGS_LINK_DOWN_KEY => "",
        ],
        [
            Constants::TABLE_DATA_KEY => "# Acc.",
            Constants::TABLE_HEADINGS_LINK_UP_KEY => "",
            Constants::TABLE_HEADINGS_LINK_DOWN_KEY => "",
        ],
        [
            Constants::TABLE_DATA_KEY => "Judge",
            Constants::TABLE_HEADINGS_LINK_UP_KEY => "",
            Constants::TABLE_HEADINGS_LINK_DOWN_KEY => "",
        ],
        [
            Constants::TABLE_DATA_KEY => "Tags",
            Constants::TABLE_HEADINGS_LINK_UP_KEY => "",
            Constants::TABLE_HEADINGS_LINK_DOWN_KEY => "",
        ],
    ];

    const PROBLEMS_SORT_BY = [
        "Name" => self::FLD_PROBLEMS_NAME,
        "Difficulty" => self::FLD_PROBLEMS_DIFFICULTY,
        "# Acc." => self::FLD_PROBLEMS_SOLVED_COUNT,
        "ID" => self::FLD_PROBLEMS_ID,
        "Judge" => self::FLD_PROBLEMS_JUDGE_ID
    ];
    // ============================================================
    //endregion

    //region Contests page

    //
    // Contests page constants
    //

    const CONTESTS_COUNT_PER_PAGE = 30;

    // TODO: add links
    const CONTESTS_TABLE_HEADINGS = [
        [
            Constants::TABLE_DATA_KEY => "ID",
            Constants::TABLE_HEADINGS_LINK_UP_KEY => "",
            Constants::TABLE_HEADINGS_LINK_DOWN_KEY => "",
        ],
        [
            Constants::TABLE_DATA_KEY => "Name",
            Constants::TABLE_HEADINGS_LINK_UP_KEY => "",
            Constants::TABLE_HEADINGS_LINK_DOWN_KEY => "",
        ],
        [
            Constants::TABLE_DATA_KEY => "Time",
            Constants::TABLE_HEADINGS_LINK_UP_KEY => "",
            Constants::TABLE_HEADINGS_LINK_DOWN_KEY => "",
        ],
        [
            Constants::TABLE_DATA_KEY => "Duration",
            Constants::TABLE_HEADINGS_LINK_UP_KEY => "",
            Constants::TABLE_HEADINGS_LINK_DOWN_KEY => "",
        ],
        [
            Constants::TABLE_DATA_KEY => "Owner",
            Constants::TABLE_HEADINGS_LINK_UP_KEY => "",
            Constants::TABLE_HEADINGS_LINK_DOWN_KEY => "",
        ],
    ];

    const Contest_SORT_BY = [
        "ID" => self::FLD_CONTESTS_ID,
        "Name" => self::FLD_CONTESTS_NAME,
        "Time" => self::FLD_CONTESTS_TIME,
        "Owner" => self::FLD_CONTESTS_OWNER_ID
    ];
    // ============================================================
    //endregion

    //region SingleContest page

    //
    // SingleContest page constants
    //

    // Keys
    // Main keys
    const SINGLE_CONTEST_CONTEST_KEY = "contest";
    const SINGLE_CONTEST_PARTICIPANTS_KEY = "participants";
    const SINGLE_CONTEST_EXTRA_KEY = "extra";

    // Details keys

    // contest
    const SINGLE_CONTEST_ID_KEY = "id";
    const SINGLE_CONTEST_NAME_KEY = "name";
    const SINGLE_CONTEST_OWNER_KEY = "owner";
    const SINGLE_CONTEST_ORGANIZERS_KEY = "organizers";
    const SINGLE_CONTEST_TIME_KEY = "time";
    const SINGLE_CONTEST_DURATION_KEY = "duration";

    // participants
    const PARTICIPANTS_DISPLAYED_FIELDS = [
        self::FLD_USERS_USERNAME,
        self::FLD_USERS_COUNTRY
    ];
    // extra
    const SINGLE_CONTEST_LEAVE_BTN_VISIBLE_KEY = "leave_btn_visible";
    const SINGLE_CONTEST_DELETE_BTN_VISIBLE_KEY = "delete_btn_visible";


    // ============================================================
    //endregion


    //region Table protocol

    //
    // Table Protocol constants
    //

    // Keys
    const TABLE_HEADINGS_KEY = "headings";
    const TABLE_ROWS_KEY = "rows";
    const TABLE_DATA_KEY = "data";
    const TABLE_META_DATA_KEY = "meta_data";
    const TABLE_LINK_KEY = "link";
    const TABLE_EXTERNAL_LINK_KEY = "external_link";
    const TABLE_HEADINGS_LINK_UP_KEY = "link_up";
    const TABLE_HEADINGS_LINK_DOWN_KEY = "link_down";
    const TABLE_ROW_STATE_KEY = "state";
    const TABLE_ROW_DISABLED_KEY = "disabled";
    const TABLE_ROW_CHECKBOX_KEY = "checkbox";

    //Pagination In Generic Table
    const TABLE_PAGINATION_KEY = "paginator";
    const PAGINATOR_TOTAL = "total";
    const PAGINATOR_LAST_PAGE= "lastPage";
    const PAGINATOR_PER_PAGE= "perPage";
    const PAGINATOR_CURRENT_PAGE= "currentPage";
    const PAGINATOR_PATH= "path";
    const PAGINATOR_NEXT_URL= "next_page_url";
    const PAGINATOR_PREV_URL= "prev_page_url";
    const PAGINATOR_START_LIMIT= "initialPage";
    const PAGINATOR_END_LIMIT= "pagesLimit";
    //Previous Filters Accompanied with the the Generic Table
    const PREVIOUS_TABLE_FILTERS = "applied_filters";
    const APPLIED_FILTERS_JUDGES_IDS = "judges";
    const APPLIED_FILTERS_TAGS_IDS = "tags";
    const APPLIED_FILTERS_SEARCH_STRING = "q";

    // Values
    const TABLE_ROW_STATE_NORMAL = 0;
    const TABLE_ROW_STATE_SUCCESS = 1;
    const TABLE_ROW_STATE_DANGER = 2;
    // ============================================================
    //endregion

    //region Judges

    //
    // Judges constants
    //
    const JUDGE_CODEFORCES_ID = 1;
    const JUDGE_UVA_ID = 2;
    const JUDGE_LIVE_ARCHIVE_ID = 3;

    const JUDGE_NAME_KEY = "name";
    const JUDGE_LINK_KEY = "link";
    const JUDGE_PROBLEM_LINK_KEY = "problemLink";
    const JUDGE_PROBLEM_LINK_ATTRIBUTES_KEY = "problemLinkAttr";
    const JUDGE_PROBLEM_NUMBER_FORMAT_KEY = "problemNumberFormat";
    const JUDGE_PROBLEM_NUMBER_FORMAT_ATTRIBUTES_KEY = "problemNumberFormatAttr";

    const JUDGES = [
        self::JUDGE_CODEFORCES_ID => [
            self::JUDGE_NAME_KEY => "Codeforces",
            self::JUDGE_LINK_KEY => "http://codeforces.com/",
            self::JUDGE_PROBLEM_LINK_KEY => "http://codeforces.com/problemset/problem/{contestId}/{problemIndex}",
            self::JUDGE_PROBLEM_LINK_ATTRIBUTES_KEY => [
                "{contestId}" => self::FLD_PROBLEMS_JUDGE_FIRST_KEY,
                "{problemIndex}" => self::FLD_PROBLEMS_JUDGE_SECOND_KEY
            ],
            self::JUDGE_PROBLEM_NUMBER_FORMAT_KEY => "{contestId}{problemIndex}",
            self::JUDGE_PROBLEM_NUMBER_FORMAT_ATTRIBUTES_KEY => [
                "{contestId}" => self::FLD_PROBLEMS_JUDGE_FIRST_KEY,
                "{problemIndex}" => self::FLD_PROBLEMS_JUDGE_SECOND_KEY
            ]
        ],
        self::JUDGE_UVA_ID => [
            self::JUDGE_NAME_KEY => "UVa Online Judge",
            self::JUDGE_LINK_KEY => "https://uva.onlinejudge.org/",
            self::JUDGE_PROBLEM_LINK_KEY => "https://uva.onlinejudge.org/index.php?option=com_onlinejudge&Itemid=8&page=show_problem&problem={problemId}",
            self::JUDGE_PROBLEM_LINK_ATTRIBUTES_KEY => [
                "{problemId}" => self::FLD_PROBLEMS_JUDGE_FIRST_KEY
            ],
            self::JUDGE_PROBLEM_NUMBER_FORMAT_KEY => "P{problemNumber}",
            self::JUDGE_PROBLEM_NUMBER_FORMAT_ATTRIBUTES_KEY => [
                "{problemNumber}" => self::FLD_PROBLEMS_JUDGE_SECOND_KEY
            ]
        ],
        self::JUDGE_LIVE_ARCHIVE_ID => [
            self::JUDGE_NAME_KEY => "Live Archive",
            self::JUDGE_LINK_KEY => "https://icpcarchive.ecs.baylor.edu/",
            self::JUDGE_PROBLEM_LINK_KEY => "https://icpcarchive.ecs.baylor.edu/index.php?option=com_onlinejudge&Itemid=8&page=show_problem&problem={problemId}",
            self::JUDGE_PROBLEM_LINK_ATTRIBUTES_KEY => [
                "{problemId}" => self::FLD_PROBLEMS_JUDGE_FIRST_KEY
            ],
            self::JUDGE_PROBLEM_NUMBER_FORMAT_KEY => "P{problemNumber}",
            self::JUDGE_PROBLEM_NUMBER_FORMAT_ATTRIBUTES_KEY => [
                "{problemNumber}" => self::FLD_PROBLEMS_JUDGE_SECOND_KEY
            ]
        ]
    ];

    //
    // Submission verdicts & languages
    //

    // Full list verdicts
    const VERDICT_FAILED = '0';
    const VERDICT_ACCEPTED = '1';
    const VERDICT_PARTIAL_ACCEPTED = '2';
    const VERDICT_COMPILATION_ERROR = '3';
    const VERDICT_RUNTIME_ERROR = '4';
    const VERDICT_WRONG_ANSWER = '5';
    const VERDICT_PRESENTATION_ERROR = '6';
    const VERDICT_TIME_LIMIT_EXCEEDED = '7';
    const VERDICT_MEMORY_LIMIT_EXCEEDED = '8';
    const VERDICT_IDLENESS_LIMIT_EXCEEDED = '9';
    const VERDICT_SECURITY_VIOLATED = '10';
    const VERDICT_CRASHED = '11';
    const VERDICT_INPUT_PREPARATION_CRASHED = '12';
    const VERDICT_CHALLENGED = '13';
    const VERDICT_SKIPPED = '14';
    const VERDICT_TESTING = '15';
    const VERDICT_REJECTED = '16';
    const VERDICT_UNKNOWN = '17';
    const VERDICT_COUNT = 18;   // To be incremented manually

    // Simple list of verdicts
    const SIMPLE_VERDICT_NOT_SOLVED = 0;
    const SIMPLE_VERDICT_ACCEPTED = 1;
    const SIMPLE_VERDICT_WRONG_SUBMISSION = 2;


    const CODEFORCES_SUBMISSION_VERDICTS = [
        "FAILED" => self::VERDICT_FAILED,
        "OK" => self::VERDICT_ACCEPTED,
        "PARTIAL" => self::VERDICT_PARTIAL_ACCEPTED,
        "COMPILATION_ERROR" => self::VERDICT_COMPILATION_ERROR,
        "RUNTIME_ERROR" => self::VERDICT_RUNTIME_ERROR,
        "WRONG_ANSWER" => self::VERDICT_WRONG_ANSWER,
        "PRESENTATION_ERROR" => self::VERDICT_PRESENTATION_ERROR,
        "TIME_LIMIT_EXCEEDED" => self::VERDICT_TIME_LIMIT_EXCEEDED,
        "MEMORY_LIMIT_EXCEEDED" => self::VERDICT_MEMORY_LIMIT_EXCEEDED,
        "IDLENESS_LIMIT_EXCEEDED" => self::VERDICT_IDLENESS_LIMIT_EXCEEDED,
        "SECURITY_VIOLATED" => self::VERDICT_SECURITY_VIOLATED,
        "CRASHED" => self::VERDICT_CRASHED,
        "INPUT_PREPARATION_CRASHED" => self::VERDICT_INPUT_PREPARATION_CRASHED,
        "CHALLENGED" => self::VERDICT_CHALLENGED,
        "SKIPPED" => self::VERDICT_SKIPPED,
        "TESTING" => self::VERDICT_TESTING,
        "REJECTED" => self::VERDICT_REJECTED,
        "UNKNOWN" => self::VERDICT_UNKNOWN
    ];

    const UHUNT_SUBMISSION_VERDICTS = [
        "10" => self::VERDICT_FAILED,
        "15" => self::VERDICT_REJECTED,
        "20" => self::VERDICT_TESTING,
        "30" => self::VERDICT_COMPILATION_ERROR,
        "35" => self::VERDICT_SECURITY_VIOLATED,
        "40" => self::VERDICT_RUNTIME_ERROR,
        "45" => self::VERDICT_CRASHED,
        "50" => self::VERDICT_TIME_LIMIT_EXCEEDED,
        "60" => self::VERDICT_MEMORY_LIMIT_EXCEEDED,
        "70" => self::VERDICT_WRONG_ANSWER,
        "80" => self::VERDICT_PRESENTATION_ERROR,
        "90" => self::VERDICT_ACCEPTED,
    ];

    const UHUNT_SUBMISSION_LANGUAGES = [
        "1" => "ANSI C",
        "2" => "Java",
        "3" => "C++",
        "4" => "Pascal",
        "5" => "C++11"
    ];
    // ============================================================
    //endregion

    //region Database

    //
    // Database constants
    //

    const ACCOUNT_ROLE_USER_KEY = "USER";
    const ACCOUNT_ROLE_ADMIN_KEY = "ADMIN";
    const ACCOUNT_ROLE_SUPER_ADMIN_KEY = "SUPER_ADMIN";
    const ACCOUNT_ROLE = [
        self::ACCOUNT_ROLE_USER_KEY => '0',
        self::ACCOUNT_ROLE_ADMIN_KEY => '1',
        self::ACCOUNT_ROLE_SUPER_ADMIN_KEY => '2'
    ];

    const GENDER_MALE_KEY = "MALE";
    const GENDER_FEMALE_KEY = "FEMALE";
    const USER_GENDER = [
        self::GENDER_MALE_KEY => '0',
        self::GENDER_FEMALE_KEY => '1'
    ];

    const CONTEST_PARTICIPANT_ROLE_USER_KEY = "USER";
    const CONTEST_PARTICIPANT_ROLE_OWNER_KEY = "OWNER";
    const CONTEST_PARTICIPANT_ROLE_ADMIN_KEY = "ADMIN";
    const CONTEST_PARTICIPANT_ROLE = [
        self::CONTEST_PARTICIPANT_ROLE_USER_KEY => '0',
        self::CONTEST_PARTICIPANT_ROLE_OWNER_KEY => '1',
        self::CONTEST_PARTICIPANT_ROLE_ADMIN_KEY => '2'
    ];

    const CONTEST_VISIBILITY_PUBLIC_KEY = "PUBLIC";
    const CONTEST_VISIBILITY_PRIVATE_KEY = "PRIVATE";
    const CONTEST_VISIBILITY = [
        self::CONTEST_VISIBILITY_PUBLIC_KEY => '0',
        self::CONTEST_VISIBILITY_PRIVATE_KEY => '1'
    ];

    const QUESTION_STATUS_NORMAL_KEY = "NORMAL";
    const QUESTION_STATUS_ANNOUNCEMENT_KEY = "ANNOUNCEMENT";
    const QUESTION_STATUS = [
        self::QUESTION_STATUS_NORMAL_KEY => '0',
        self::QUESTION_STATUS_ANNOUNCEMENT_KEY => '1'
    ];


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
    const FLD_USERS_USERNAME = "username";
    const FLD_USERS_EMAIL = "email";
    const FLD_USERS_PASSWORD = "password";
    const FLD_USERS_FIRST_NAME = "first_name";
    const FLD_USERS_LAST_NAME = "last_name";
    const FLD_USERS_GENDER = "gender";
    const FLD_USERS_BIRTHDATE = "birthdate";
    const FLD_USERS_COUNTRY = "country";
    const FLD_USERS_PROFILE_PICTURE = "profile_picture";
    const FLD_USERS_ROLE = "role";
    const FLD_USERS_REMEMBER_TOKEN = "remember_token";
    const FLD_USERS_CODEFORCES_HANDLE = "codeforces_handle";        // Used in sign up & profile pages
    const FLD_USERS_UVA_HANDLE = "uva_handle";                      // Used in sign up & profile pages
    const FLD_USERS_LIVE_ARCHIVE_HANDLE = "live_archive_handle";    // Used in sign up & profile pages

    // Password resets
    const FLD_PASSWORD_RESETS_EMAIL = "email";
    const FLD_PASSWORD_RESETS_TOKEN = "token";
    const FLD_PASSWORD_RESETS_CREATED_AT = "created_at";

    // Contests
    const FLD_CONTESTS_ID = "id";
    const FLD_CONTESTS_OWNER_ID = "owner_id";
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
    const FLD_PROBLEMS_SOLVED_COUNT = "solved_count";
    const FLD_PROBLEMS_JUDGE_NAME = "judge_name";                   // Derived attribute
    const FLD_PROBLEMS_TAGS = "tag_names";                          // Derived attribute

    // Submissions
    const FLD_SUBMISSIONS_ID = "id";
    const FLD_SUBMISSIONS_JUDGE_SUBMISSION_ID = "judge_submission_id";
    const FLD_SUBMISSIONS_USER_ID = "user_id";
    const FLD_SUBMISSIONS_PROBLEM_ID = "problem_id";
    const FLD_SUBMISSIONS_LANGUAGE_ID = "language_id";
    const FLD_SUBMISSIONS_SUBMISSION_TIME = "submission_time";
    const FLD_SUBMISSIONS_EXECUTION_TIME = "execution_time";
    const FLD_SUBMISSIONS_CONSUMED_MEMORY = "consumed_memory";
    const FLD_SUBMISSIONS_VERDICT = "verdict";

    // Questions
    const FLD_QUESTIONS_ID = "id";
    const FLD_QUESTIONS_USER_ID = "user_id";
    const FLD_QUESTIONS_CONTEST_ID = "contest_id";
    const FLD_QUESTIONS_PROBLEM_ID = "problem_id";
    const FLD_QUESTIONS_TITLE = "title";
    const FLD_QUESTIONS_CONTENT = "content";
    const FLD_QUESTIONS_STATUS = "status";
    const FLD_QUESTIONS_ANSWER = "answer";
    const FLD_QUESTIONS_ADMIN_ID = "admin_id";

    // Judges
    const FLD_JUDGES_ID = "id";
    const FLD_JUDGES_NAME = "name";
    const FLD_JUDGES_LINK = "link";

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
    // ============================================================
    //endregion
}
