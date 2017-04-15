<?php

namespace App\Utilities;

class Constants
{
    //region Problems page

    //
    // Problems page constants
    //

    const PROBLEMS_COUNT_PER_PAGE = 30;

    const URL_QUERY_SEARCH_KEY = "q";
    const URL_QUERY_JUDGES_KEY = "judges";
    const URL_QUERY_TAGS_KEY = "tags";
    const URL_QUERY_TAG_KEY = "tag";
    const URL_QUERY_SORT_PARAM_KEY = "sort";
    const URL_QUERY_SORT_ORDER_KEY = "order";
    const URL_QUERY_PAGE_KEY = "page";

    const URL_QUERY_SORT_PARAM_ID_KEY = "id";
    const URL_QUERY_SORT_PARAM_NAME_KEY = "name";
    const URL_QUERY_SORT_PARAM_ACCEPTED_COUNT_KEY = "acceptedCount";
    const URL_QUERY_SORT_PARAM_JUDGE_KEY = "judge";

    const PROBLEMS_SORT_PARAMS = [
        self::URL_QUERY_SORT_PARAM_ID_KEY => self::FLD_PROBLEMS_ID,
        self::URL_QUERY_SORT_PARAM_NAME_KEY => self::FLD_PROBLEMS_NAME,
        self::URL_QUERY_SORT_PARAM_ACCEPTED_COUNT_KEY => self::FLD_PROBLEMS_SOLVED_COUNT,
        self::URL_QUERY_SORT_PARAM_JUDGE_KEY => self::FLD_PROBLEMS_JUDGE_ID,
        '' => self::FLD_PROBLEMS_ID
    ];
    // ============================================================
    //endregion

    //region contests page

    //
    // Contests page constants
    //

    const CONTESTS_COUNT_PER_PAGE = 30;

    const CONTESTS_CONTESTS_KEY = 'contests';

    //
    // Single contest page constants
    //

    // Main keys
    const SINGLE_CONTEST_CONTEST_KEY = "contest";
    const SINGLE_CONTEST_PROBLEMS_KEY = "problems";
    const SINGLE_CONTEST_STANDINGS_KEY = "standings";
    const SINGLE_CONTEST_STATUS_KEY = "status";
    const SINGLE_CONTEST_PARTICIPANTS_KEY = "participants";
    const SINGLE_CONTEST_QUESTIONS_KEY = "questions";
    const SINGLE_CONTEST_EXTRA_KEY = "extra";

    // Details keys

    // Contest
    const SINGLE_CONTEST_ID_KEY = "id";
    const SINGLE_CONTEST_NAME_KEY = "name";
    const SINGLE_CONTEST_OWNER_KEY = "owner";
    const SINGLE_CONTEST_ORGANIZERS_KEY = "organizers";
    const SINGLE_CONTEST_TIME_KEY = "time";
    const SINGLE_CONTEST_DURATION_KEY = "duration";

    // Participants
    const PARTICIPANTS_DISPLAYED_FIELDS = [
        self::FLD_USERS_USERNAME,
        self::FLD_USERS_COUNTRY
    ];

    // Extra
    const SINGLE_CONTEST_IS_USER_PARTICIPATING = "user_is_participant";
    const SINGLE_CONTEST_IS_USER_OWNER = "user_is_owner";
    const SINGLE_CONTEST_IS_USER_AN_ORGANIZER = "user_is_organizer";
    const SINGLE_CONTEST_RUNNING_STATUS = "contest_running_status";
    // ============================================================
    //endregion


    //region groups page

    //
    // Groups page constants
    //

    const GROUPS_COUNT_PER_PAGE = 30;

    const GROUPS_GROUPS_KEY = 'groups';

    //
    // Single group page constants
    //

    // Main keys
    const SINGLE_GROUP_GROUP_KEY = "group";
    const SINGLE_GROUP_EXTRA_KEY = "extra";
    const SINGLE_GROUP_MEMBERS_KEY = "members";

    // Details keys

    // Group
    const SINGLE_GROUP_ID_KEY = "id";
    const SINGLE_GROUP_NAME_KEY = "name";
    const SINGLE_GROUP_OWNER_KEY = "owner";

    // Extra
    const SINGLE_GROUP_IS_USER_OWNER = "user_is_owner";
    const SINGLE_GROUP_IS_USER_MEMBER = "user_is_member";
    const SINGLE_GROUP_USER_SENT_REQUEST = "user_sent_request";


    // Members displayable fields
    const MEMBERS_DISPLAYED_FIELDS = [
        self::FLD_USERS_USERNAME,
        self::FLD_USERS_EMAIL,
        self::FLD_USERS_COUNTRY
    ];
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
    const VERDICT_COUNT = 18;   // Note: To be incremented manually

    const VERDICT_NAMES = [
        self::VERDICT_FAILED => '0',
        self::VERDICT_ACCEPTED => 'Accepted',
        self::VERDICT_PARTIAL_ACCEPTED => 'Partial Accepted',
        self::VERDICT_COMPILATION_ERROR => 'Compilation Error',
        self::VERDICT_RUNTIME_ERROR => 'Runtime Error',
        self::VERDICT_WRONG_ANSWER => 'Wrong Answer',
        self::VERDICT_PRESENTATION_ERROR => 'Presentation Error',
        self::VERDICT_TIME_LIMIT_EXCEEDED => 'Time Limit Exceeded',
        self::VERDICT_MEMORY_LIMIT_EXCEEDED => 'Memory Limit Exceeded',
        self::VERDICT_IDLENESS_LIMIT_EXCEEDED => 'Idleness Limit Exceeded',
        self::VERDICT_SECURITY_VIOLATED => 'Security Violated',
        self::VERDICT_CRASHED => 'Crashed',
        self::VERDICT_INPUT_PREPARATION_CRASHED => 'Input Preparation Crashed',
        self::VERDICT_CHALLENGED => 'Challenged',
        self::VERDICT_SKIPPED => 'Skipped',
        self::VERDICT_TESTING => 'Testing',
        self::VERDICT_REJECTED => 'Rejected',
        self::VERDICT_UNKNOWN => 'Unknown',
    ];

    // Simple list of verdicts
    const SIMPLE_VERDICT_NOT_SOLVED = 0;
    const SIMPLE_VERDICT_ACCEPTED = 1;
    const SIMPLE_VERDICT_WRONG_SUBMISSION = 2;

    // Codeforces submission verdicts
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

    // uHunt submission verdicts
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

    // uHunt submission languages
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

    const NOTIFICATION_STATUS_READ = "READ";
    const NOTIFICATION_STATUS_UNREAD = "UNREAD";
    const NOTIFICATION_STATUS_DELETED = "DELETED";
    const NOTIFICATION_STATUS = [
        self::NOTIFICATION_STATUS_UNREAD => '0',
        self::NOTIFICATION_STATUS_READ => '1',
        self::NOTIFICATION_STATUS_DELETED => '2'
    ];

    const NOTIFICATION_TYPE_CONTEST = "CONTEST";
    const NOTIFICATION_TYPE_GROUP = "GROUP";
    const NOTIFICATION_TYPE_TEAM = "TEAM";
    const NOTIFICATION_TYPE = [
        self::NOTIFICATION_TYPE_CONTEST => '0',
        self::NOTIFICATION_TYPE_GROUP => '1',
        self::NOTIFICATION_TYPE_TEAM => '2'
    ];

    const NOTIFICATION_TEXT = [
        self::NOTIFICATION_TYPE[self::NOTIFICATION_TYPE_CONTEST] => "You're invited to join the private contest: ",
        self::NOTIFICATION_TYPE[self::NOTIFICATION_TYPE_GROUP] => "You're invited to join the private group: ",
        self::NOTIFICATION_TYPE[self::NOTIFICATION_TYPE_TEAM] => "You're invited to join the private team: ",
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
    const TBL_NOTIFICATIONS = "notifications";
    const TBL_GROUPS = "groups";

    // Pivot tables
    const TBL_USER_HANDLES = "user_handles";
    const TBL_CONTEST_PROBLEMS = "contest_problems";
    const TBL_CONTEST_PARTICIPANTS = "contest_participants";
    const TBL_CONTEST_ADMINS = "contest_admins";
    const TBL_PROBLEM_TAGS = "problem_tags";
    const TBL_GROUP_MEMBERS = "groups_members";
    const TBL_GROUPS_JOIN_REQUESTS = "groups_join_requests";


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
    const FLD_SUBMISSIONS_PROBLEM_NAME = "problem_name";            // Derived attribute
    const FLD_SUBMISSIONS_LANGUAGE_NAME = "language_name";          // Derived attribute

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

    // Groups
    const FLD_GROUPS_ID = "id";
    const FLD_GROUPS_NAME = "name";
    const FLD_GROUPS_OWNER_ID = "owner_id";

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


    //Notifications
    const FLD_NOTIFICATIONS_ID = "id";
    const FLD_NOTIFICATIONS_SENDER_ID = "sender_id";
    const FLD_NOTIFICATIONS_RECEIVER_ID = "receiver_id";
    const FLD_NOTIFICATIONS_RESOURCE_ID = "resource_id";    // Group id, contest id, ...etc
    const FLD_NOTIFICATIONS_TYPE = "type";                  // From group, contest, team, ...etc
    const FLD_NOTIFICATIONS_STATUS = "status";              // Read, unread


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

    // Group members
    const FLD_GROUP_MEMBERS_USER_ID = "user_id";
    const FLD_GROUP_MEMBERS_GROUP_ID = "group_id";

    // Groups join requests
    const FLD_GROUPS_JOIN_REQUESTS_USER_ID = "user_id";
    const FLD_GROUPS_JOIN_REQUESTS_GROUP_ID = "group_id";

    // ============================================================
    //endregion
}
