var app = {
    // ==================================================
    //                    Constants
    // ==================================================

    // Add Contest Session Constants

    problemsIDsSessionKey: 'problems_ids_session_key',
    tagsSessionKey: 'tags_session_key',
    judgesSessionKey: 'judges_session_key',
    organizersSessionKey: 'organizers_session_key',
    inviteesSessionKey: 'invitees_session_key',
    contestNameSessionKey: 'contest_name_session_key',
    contestTimeSessionKey: 'contest_time_session_key',
    contestDurationSessionKey: 'contest_duration_session_key',
    contestPrivateVisibilitySessionKey: 'contest_private_visibility_session_key',

    contestProblemsMaxCount: 10,

    // ==================================================
    //                    Variables
    // ==================================================


    //region Code Editor Vars

    // This var is used to store the problems solution once fetched from the Ajax
    // request, then every owner modification to the code is also reserved here.
    // When the user refreshes the page, the non-saved modifications are gone...
    currentProblemsSolutions: {},

    // These vars store the current state if user is owner or member
    isOwner: false,
    isMember: false,
    editor: {},

    //endregion

    //region Add/Edit Contest Vars

    tagsList: {},
    organisersList: {},
    inviteesList: {},

    //endregion


    //region Contest problems sortable table

    // Flag to indicate if the table is in sort mode or not
    isTableSortable: false,
    // Array to store the last contest problem IDs order
    newSortedIDsArray: [],

    //endregion

    // Retrieve all tags from db, such that auto complete doesn't touch the database
    // each time a new letter is typed
    allTagsList: [],
    // ==================================================
    //                 MAIN FUNCTIONS
    // ==================================================
    /**
     * Start the app and run the common code
     * then execute detectCurrentRoute to run this specific html page functions
     */
    justLetItGo: function () {

        // Run common functions (all pages need)
        this.commonPagesConfigurations();

        // Run current route functions
        this.detectCurrentRoute();
    },
    /**
     * Run the basic configurations which aren't affected by page variations
     */
    commonPagesConfigurations: function () {

        //birth Date picker
        $("#datepicker").datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: "-100:+0"
        });

        //region Date time pickers

        // Enable date time pickers
        $('.datetimepicker').datetimepicker({
            format: 'Y-m-d H:i:s',
            minDate: 0, // for after today limitation
            maxDate: '+1970/01/30' // for max 1 month
        });

        //endregion

        //<editor-fold desc="File upload event">
        $(document).on('change', ':file', function () {
            var input = $(this),
                label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
            input.trigger('fileselect', [label]);
        });

        // Echo file name in p element
        $(':file').on('fileselect', function (event, label) {
            $("#profile-pic-name").text(label);
        });

        //</editor-fold>

        //region Maintain bootstrap tabs

        // Maintain bootstrap selected tabs on page reload
        // i.e. keep the selected tab before reloading the page selected
        // after the reloading finishes

        // if the link contains has, look for the link who should open this tab
        // and show the related tab
        if (location.hash) {
            $("a[href='" + location.hash + "']").trigger("click");
        }

        // When a tab link is clicked attach the hash of the tab to the url
        // (to use it in the previous if statement)
        $(document.body).on("click", "a[data-toggle]", function (event) {
            location.hash = this.getAttribute("href");
        });

        //endregion

        //region Auto complete fields configurations (used for invitees forms)
        var autoCompleteFields = $('.autocomplete-input');

        for (var i = 0; i < autoCompleteFields.length; i++) {
            // Get single field info
            var field = $(autoCompleteFields[i]);

            // Get data bounded to the field
            var fieldID = field.attr('id');
            var autoCompletePath = field.data('path');
            var sessionKey = field.data('session-key');
            var listID = field.data('list-id');

            // Configure lists and autocomplete typeahead
            $("#" + fieldID).typeahead(app.autoCompleteList(autoCompletePath, document.getElementById(listID), sessionKey));

            // Flush previous session values
            sessionStorage.setItem(sessionKey, '');
        }
        //endregion

    },
    /**
     * Search for the page-distinguishing-element (invisible element that has an
     * id and using this id we will know which page are we browsing now)
     */
    detectCurrentRoute: function () {

        // Homepage
        if ($('#home-page-hidden-element').length) {
            this.executeHomePageAnimations(); // run way-point animations
        }

        // Sheet page
        if ($('#sheet-page-hidden-element').length) {
            app.initializeCodeEdit();
        }

        // Add/Edit contest page
        if ($("#add-edit-contest-page-hidden-element").length) {

            // Define lists
            this.tagsList = document.getElementById("tags-list");
            this.organisersList = document.getElementById("organisers-list");
            this.inviteesList = document.getElementById("invitees-list");

            // If edit page is on let's fill some sessions first
            if ($("#add-edit-contest-page-hidden-element").data('name')) {
                app.fillSessionWithContestData();
            }

            // Sync server session filters with client session filters
            app.getFiltersFromElementAndFormat($("#add-edit-contest-page-hidden-element"));

            // Fetch all tags
            app.fetchAllTagsFromDB();

            // Configure lists and autocomplete typeahead
            $('#tags-auto').typeahead(app.autoCompleteList($("#tags-auto").data('tags-path'), app.tagsList, app.tagsSessionKey, app.allTagsList));
            $('#organisers-auto').typeahead(app.autoCompleteList($("#organisers-auto").data('organisers-path'), app.organisersList, app.organizersSessionKey));
            $('#invitees-auto').typeahead(app.autoCompleteList($("#invitees-auto").data('invitees-path'), app.inviteesList, app.inviteesSessionKey));

            // Render saved data from session into form
            app.fillContestFormFromSession();

            // Enable duration picker
            $(".timing").timingfield();

        }

        // Add/Edit group page
        if ($("#add-edit-group-page-hidden-element").length) {

            this.organisersList = document.getElementById("admins-list");
            app.organizersSessionKey = 'group_admins_session_key';

            // If edit page is on let's fill some sessions first
            if ($("#add-edit-group-page-hidden-element").data('admins')) {
                app.fillSessionWithGroupData();

                // Fill admins list
                app.retrieveListsFromSession(app.organizersSessionKey, app.organisersList);
            }
            $('#admins-auto').typeahead(app.autoCompleteList($("#admins-auto").data('admins-path'), app.organisersList, app.organizersSessionKey));
        }

        // Add/Edit sheet page
        if ($("#add-edit-sheet-page-hidden-element").length) {
            // Set the session keys for sheets problems
            app.problemsIDsSessionKey = 'sheets_problems_ids_session_key';
            app.tagsSessionKey = 'sheets_tags_ids_session_key';
            app.judgesSessionKey = 'sheets_judges_ids_session_key';

            app.tagsList = document.getElementById("tags-list");

            // If edit page is on let's fill some sessions first
            if ($("#add-edit-sheet-page-hidden-element").data('name')) {
                app.fillSessionWithSheetData();
            }

            // Sync server session filters with client session filters
            app.getFiltersFromElementAndFormat($("#add-edit-sheet-page-hidden-element"));

            // Fetch all tags
            app.fetchAllTagsFromDB();

            // Configure lists and autocomplete typeahead
            $('#tags-auto').typeahead(app.autoCompleteList($("#tags-auto").data('tags-path'), app.tagsList, app.tagsSessionKey, app.allTagsList));

            // Fill judges checkboxes
            app.fillJudgesCheckboxes();

            // Fill tags
            app.retrieveListsFromSession(app.tagsSessionKey, app.tagsList);

            // Fill problems checkboxes
            app.fillProblemsTableCheckboxes();
        }

        // Problems filters
        if ($("#problems-page-hidden-element").length) {
            // Set different tagsSessionKey for problems page
            // to maintain the tags stored for add/edit contest
            app.tagsSessionKey = 'problems_filters_tags_session_key';

            app.tagsList = document.getElementById("tags-list");

            // Fill url filters into session
            var urlTags = app.getUrlVars()['tag'];
            if (urlTags && urlTags.trim().length)
                sessionStorage.setItem(app.tagsSessionKey, '["' + app.getUrlVars()['tag'].replace(/%2C/g, '","') + '"]');
            else
                sessionStorage.setItem(app.tagsSessionKey, '');


            // Fetch all tags
            app.fetchAllTagsFromDB();

            // Configure lists and autocomplete typeahead
            $('#tags-auto').typeahead(app.autoCompleteList($("#tags-auto").data('tags-path'), app.tagsList, app.tagsSessionKey, app.allTagsList));

            // Retrieve tags from session to view
            app.retrieveListsFromSession(app.tagsSessionKey, app.tagsList);

            // Toggle filters more div if query contains tags or judges
            app.toggleFiltersPanel();
        }

        //Blogs Add Post page
        if ($("#add-edit-post-page-hidden-element").length) {

            // For testing purposes, we will need to disable this feature (simpleMDE)
            // So we've this flag stored in the session to determine weather to enable/disable
            // this feature
            if (!sessionStorage.getItem('disableMDE')) {

                // Get the textarea element (post body)
                var element = document.getElementById("edit-post-body");

                new SimpleMDE({
                    element: element,
                    // Enables Auto Save which is removed when the form is submitted
                    autosave: {
                        enabled: $(element).data('autosave-enable'),
                        uniqueId: "edit_post", // Unique id for identifying saving purposes
                        delay: 1000, // Time between saves milli seconds
                    },
                    spellChecker: false, // Disable Spell Checker
                });
            }
        }

        //Blogs View Single Post page
        if ($("#view-post-page-hidden-element").length) {

            // Render the post body in markdown
            var bostBodyElement = document.getElementById('current_post_body');
            bostBodyElement.innerHTML = marked(bostBodyElement.innerHTML);

            // Add the comment markdown editor
            if (!sessionStorage.getItem('disableMDE')) {
                new SimpleMDE({
                    // Get the text area element
                    element: document.getElementsByClassName("add-comment-text")[0],
                    spellChecker: false, // Disable Spell Checker
                });
            }

            // Render the comments in markdown
            var comments = document.getElementsByClassName("comment-body");

            // Loop over them and render each one in markdown
            for (var i = 0; i < comments.length; i++) {
                comments[i].innerHTML = marked(comments[i].innerHTML);
            }
        }

        // Blogs Home Page
        if ($("#blogs-home-page-hidden-element").length) {

            // Get all the blogs paragraph in the index page
            var posts = document.getElementsByClassName('post-small-paragraph');

            //Loop over the paragraphs in the blog index page
            //Change the text in each paragraph to a marked version
            for (var i = 0; i < posts.length; i++) {
                posts[i].innerHTML = marked(posts[i].innerHTML);
            }
        }
    },

    // ==================================================
    //              ANIMATIONS FUNCTIONS
    // ==================================================

    /**
     * Add animations to homepage views
     */
    executeHomePageAnimations: function () {

        $('.js-wp-1').waypoint(function (direction) {
            $('.js-wp-1').addClass('animated fadeInDown');
        }, {
            offset: '50%'
        });

        $('.js-wp-2').waypoint(function (direction) {
            $('.js-wp-2').addClass('animated fadeInUp');
        }, {
            offset: '80%'
        });

        $('.js-wp-3').waypoint(function (direction) {
            $('.js-wp-3').addClass('animated fadeIn');
        }, {
            offset: '80%'
        });

        $('.js-wp-4').waypoint(function (direction) {
            $('.js-wp-4').addClass('animated zoomIn');
        }, {
            offset: '95%'
        });
    },

    // ==================================================
    //              CODE EDITOR FUNCTIONS
    // ==================================================

    /**
     * Initialize page code editor for owner/members
     */
    initializeCodeEdit: function () {

        //region Ace Code Editor

        // The following section handles Ace code editor
        // Tasks:
        // 1. Fill the solution modal with problem previous solution if available
        // 2. Reserve user modifications to code even if he closes the modal
        // 3. Allow user to save the new answer
        // 4. Allow members to view the code only

        // User status check
        if ($("#code-editor").length) { // Check if owner
            app.isOwner = true;
        } else if ($("#code-editor-members").length) { // Or member
            app.isMember = true;
        }

        // Here I initialize the code editor for both owner and members views
        if (app.isOwner) {
            app.editor = ace.edit("code-editor"); // Initialize Ace editor with code editor container ID editor.getSession().on('change', function () {

            // Catch owner input in code editor
            // Save the modifications to the currentProblemsSolutions storage
            // which is indexed by problemID
            app.editor.getSession().on('change', function () {
                if ($('#problem-id').val().length)
                    app.currentProblemsSolutions[$('#problem-id').val()] = app.editor.getValue();
            });
        } else if (app.isMember) {
            app.editor = ace.edit("code-editor-members"); // Initialize Ace editor with code editor container ID
        }

        if (app.editor) {
            app.editor.setTheme("ace/theme/twilight"); // Set the editor theme
            app.editor.session.setMode("ace/mode/c_cpp"); // Set initial mode to c_cpp
        }

        // When user clicks save answer button
        // Before the form submission, the hidden textarea gets filled with
        // code editor value, such that the textarea value (i.e. code editor value)
        // gets transmitted with the form request to the controller
        // THIS happens because we cannot catch code editor value easily

        $("#answer-model-submit-button").click(function () {
            if (app.editor.getValue().trim() != "")
                $('#problem-solution').val(app.editor.getValue());
            return true;
        });
        //endregion
    },

    /**
     * Retrieve problem solution from url
     *
     * @param int problemID used for storing solution once retrieved
     * @param url used to contact backend
     */
    getSolutionFromFile: function (problemID, url) {
        $.ajax({
            type: "GET",
            url: url,
            async: false,
            success: function (problemSolution) {
                // Fill editors with solution
                app.editor.focus(); // get user focus

                if (app.isOwner) {
                    // Store retrieved solution in the currentProblemsSolutions
                    app.currentProblemsSolutions[problemID] = problemSolution;

                    // Set the editor value and move cursor
                    app.editor.setValue(problemSolution, -1);
                } else if (app.isMember) {
                    // Set value and make editor read only for members
                    // If no solution, print no solution message
                    app.editor.setValue((problemSolution.length) ? problemSolution : 'No solution provided!', -1);
                    app.editor.setReadOnly(true);
                }
            },
            error: function (result) {
                console.log(result.responseText);
            }
        });
    },

    /**
     * Fill problem answer modal fields once Solution button is clicked
     *
     * @param int problemID
     * @param int sheetID
     * @param string url for solution file
     * @param solution_lang
     */
    fillAnswerModal: function (problemID, sheetID, url, solutionLang) {
        var solution_lang = '';
        // If no solution_lang, means no previous solution is provided
        // Assume that c_cpp is going to be used
        if (!solutionLang.length) {
            solution_lang = "c_cpp";
        }

        // Set selected language in solution_lang menu
        $("#solution_lang").val(solution_lang);

        // Set editor mode to match selected solution_lang (retrieved from database)
        app.editor.getSession().setMode("ace/mode/" + solution_lang);

        // Set form values (problem id and sheet id, used in form submission to store solution)
        if ($('#problem-id').length && $('#sheet-id').length) {
            $('#problem-id').val(problemID);
            $('#sheet-id').val(sheetID);
        }
        // Check if the solution of this problem hasn't retrieved yet
        // get it (and inside this fn call, the solution will be stored in currentProblemsSolutions)
        if (!app.currentProblemsSolutions[problemID]) {
            app.getSolutionFromFile(problemID, url);
        } else {
            // If solution exists, render it in the editor
            app.editor.setValue(app.currentProblemsSolutions[problemID], -1);
        }
    },

    // ==================================================
    //              NOTIFICATIONS FUNCTIONS
    // ==================================================

    /**
     * Send Ajax request to mark all user notifications as READ once the user clicks the
     * notifications icon in the header
     *
     * @param string csrf_token
     * @param string url
     */
    markAllNotificationsRead: function (token, url) {
        $.ajax({
            type: "POST",
            url: url,
            data: {_token: token, _method: "PUT"},
            success: function () {
                // Change icon to light bell
                $("#notifications-icon").removeClass("fa-bell");
                $("#notifications-icon").removeClass("dark-red");
                $("#notifications-icon").addClass("fa-bell-o");

                // Prevent future clicks to execute this function
                $("#notifications-icon").parent()[0].onclick = null;
            },
            error: function (result) {
                console.log(result.responseText);
            }
        });
    },

    /**
     * Lazy delete certain notification
     * @param e
     * @param token
     * @param url
     * @param element
     * @returns {boolean}
     */
    cancelNotification: function (e, token, url, element) {
        var event = e;
        // Prevent click event propagation (such that dropdown menu doesn't
        // close after deleting) notification
        if (!event) {
            event = window.event;
        }
        //IE9 & Other Browsers
        if (event.stopPropagation) {
            event.stopPropagation();
        }
        //IE8 and Lower
        else {
            event.cancelBubble = true;
        }
        // Send lazy delete request
        $.ajax({
            type: "POST",
            url: url,
            data: {_token: token, _method: "DELETE"},
            success: function () {
                app.hideNotificationElement(element);
            }
        });
        return false;
    },

    /**
     * Hide the notification element from notifications panel once deleted successfully
     *
     * @param object element
     */
    hideNotificationElement: function (element) {
        // Remove UI notification element + separator
        // In not next/prev means only this one notification visible -> hide notification icon
        if ($(element).parent().next().length == 0
            && $(element).parent().prev().length == 0) {
            $(".notifications-dropdown").hide();
        }
        // If prev but not next means the user deleted last one in the table ->
        // Hide the separator before it
        else if ($(element).parent().next().length == 0) {
            $(element).parent().prev().remove();
        }
        // User deleted one in the middle -> remove its separator only
        else {
            // Hide separator after notification
            $(element).parent().next().remove();
        }
        // Remove notification li element
        $(element).parent().remove();
    },


    // ==================================================
    //               GROUP FUNCTIONS
    // ==================================================

    /**
     * Set hidden inputs values from sessions, then clear sessions
     */
    moveGroupSessionDataToHiddenFields: function () {
        // Set value
        if (sessionStorage.getItem(app.organizersSessionKey))
            $("#admins-ids-hidden").val(JSON.parse(sessionStorage.getItem(app.organizersSessionKey)).join());

        // Clear sessions
        app.clearSession();
    },

    /**
     * Fill session with the sheet data bound to element
     * #add-edit-sheet-page-hidden-element
     */
    fillSessionWithSheetData: function () {
        // Set sessionKey to editMode
        app.problemsIDsSessionKey = 'edit_sheet_problems_ids_session_key';

        // Fetch contest data
        var element = $("#add-edit-sheet-page-hidden-element");

        var sheetProblems = '';

        if (element.data('problems').length)
            sheetProblems = '["' + element.data('problems').toString().replace(/,/g, '","') + '"]';

        // Fill sessions
        sessionStorage.setItem(app.problemsIDsSessionKey, sheetProblems);

    },

    /**
     * Fill session with the sheet data bound to element
     * #add-edit-sheet-page-hidden-element
     */
    fillSessionWithGroupData: function () {
        // Fetch contest data
        var element = $("#add-edit-group-page-hidden-element");

        var groupAdmins = '';
        if (element.data('admins').length)
            groupAdmins = '["' + element.data('admins').toString().replace(/,/g, '","') + '"]';

        // Fill sessions
        sessionStorage.setItem(app.organizersSessionKey, groupAdmins);

    },
    // ==================================================
    //            ADD/EDIT CONTEST FUNCTIONS
    // ==================================================

    /**
     * Fill session with the contest data bound to element
     * #add-edit-contest-page-hidden-element
     */
    fillSessionWithContestData: function () {
        // Set sessionKey to editMode
        app.contestNameSessionKey = 'edit_contest_name_session_key';
        app.contestTimeSessionKey = 'edit_contest_time_session_key';
        app.contestDurationSessionKey = 'edit_contest_duration_session_key';
        app.contestPrivateVisibilitySessionKey = 'edit_contest_private_visibility_session_key';
        app.problemsIDsSessionKey = 'edit_contest_problems_ids_session_key';
        app.organizersSessionKey = 'edit_organizers_session_key';

        // Fetch contest data
        var element = $("#add-edit-contest-page-hidden-element");

        var contestName = element.data('name');
        var contestTime = element.data('time');
        var contestDuration = element.data('duration');
        var contestVisibility = element.data('visibility');
        var contestOrganizers = '';
        var contestProblems = '';

        if (element.data('organizers').length)
            contestOrganizers = '["' + element.data('organizers').toString().replace(/,/g, '","') + '"]';
        if (element.data('problems').length)
            contestProblems = '["' + element.data('problems').toString().replace(/,/g, '","') + '"]';
        if (contestVisibility == '1') {
            $("#invitees-input-div").show();
        }
        // Fill sessions
        sessionStorage.setItem(app.contestNameSessionKey, contestName);
        sessionStorage.setItem(app.contestTimeSessionKey, contestTime);
        sessionStorage.setItem(app.contestDurationSessionKey, contestDuration);
        sessionStorage.setItem(app.contestPrivateVisibilitySessionKey, contestVisibility);
        sessionStorage.setItem(app.organizersSessionKey, contestOrganizers);
        sessionStorage.setItem(app.problemsIDsSessionKey, contestProblems);

    },

    /**
     * Fetch all tags from db and save to local variable
     * to avoid database touching each time
     */
    fetchAllTagsFromDB: function () {
        // Send request to path in tags-path data attr
        $.get($("#tags-auto").data('tags-path'), function (data) {
            app.allTagsList = data;
        });
    },
    /**
     * Fill checkboxes of problems selector from session
     */
    fillProblemsTableCheckboxes: function () {

        // Recheck selected problems IDs checkboxes
        var savedProblemsIDs = sessionStorage.getItem(app.problemsIDsSessionKey);

        if (savedProblemsIDs) { // check if there're any stored IDs

            // Convert to array
            var savedProblemsIDsArray = JSON.parse(savedProblemsIDs);

            // Loop over IDs
            savedProblemsIDsArray.forEach(function (element) {
                $("#problem-checkbox-" + element).prop('checked', true);
            });
        }
    },
    /**
     * Fill checkboxes of judges selector from session
     */
    fillJudgesCheckboxes: function () {

        // Recheck selected judges IDs checkboxes
        var savedJudgesIDs = sessionStorage.getItem(app.judgesSessionKey);

        if (savedJudgesIDs) { // check if there're any stored IDs

            // Convert to array
            var savedJudgesIDsArray = JSON.parse(savedJudgesIDs);

            // Loop over IDs
            savedJudgesIDsArray.forEach(function (element) {
                $("#judge-checkbox-" + element).prop('checked', true);
            });
        }
    },
    /**
     * Fill contest add/edit fields from the stored session
     */
    fillContestFormFromSession: function () {
        // Render saved data from session
        if ($("#add-edit-contest-form").length) { // Check if in add/edit contest view

            // Fill problems checkboxes
            app.fillProblemsTableCheckboxes();

            // Fill judges checkboxes
            app.fillJudgesCheckboxes();

            // Fill tags, organisers lists
            app.retrieveListsFromSession(app.tagsSessionKey, app.tagsList);
            app.retrieveListsFromSession(app.organizersSessionKey, app.organisersList);
            app.retrieveListsFromSession(app.inviteesSessionKey, app.inviteesList);

            // Fill form basic fields
            $("#name").val(sessionStorage.getItem(app.contestNameSessionKey));
            $("#duration").attr("value", sessionStorage.getItem(app.contestDurationSessionKey));

            // ===============================================================
            // Fill contest time
            // ===============================================================
            if (sessionStorage.getItem(app.contestTimeSessionKey)) {
                var offset = new Date().getTimezoneOffset();
                // Set date time field to server timezone
                var m = moment(sessionStorage.getItem(app.contestTimeSessionKey)).format(); // User input (user timezone)
                var d = new Date(m); // Convert to date

                // Get time in server time zone
                m = moment(d.toISOString()).add('m', -offset).format('YYYY-MM-DD HH:mm:ss');

                // Send to server to check and then server will store in UTC
                $("#time").val(m);
            }

            // ===============================================================

            // Set form fields on change listeners
            $("#name").change(function () {
                sessionStorage.setItem(app.contestNameSessionKey, $("#name").val());
            });
            $("#time").change(function () {
                sessionStorage.setItem(app.contestTimeSessionKey, $("#time").val());
            });
            $("#duration").change(function () {
                sessionStorage.setItem(app.contestDurationSessionKey, $("#duration").val());
            });
            if (sessionStorage.getItem(app.contestPrivateVisibilitySessionKey) == 1) {
                $("#private_visibility").prop('checked', true);
                $("#invitees-input-div").show();
            } else {
                $("#public_visibility").prop('checked', true);
            }
            $("#private_visibility").change(function () {
                $("#invitees-input-div").show();
                sessionStorage.setItem(app.contestPrivateVisibilitySessionKey, 1);
            });
            $("#public_visibility").change(function () {
                $("#invitees-input-div").hide();
                sessionStorage.setItem(app.contestPrivateVisibilitySessionKey, 0);
            });
        }
    },
    /**
     * This function saves the selected filters (selected Tags and Selected judges) to the session
     * It takes url of syncing filters, the token
     * @param url
     * @param token
     * @param redirectURL
     */
    applyFilters: function (url, token, redirectURL) {
        // Get the current filters from the view
        var selected_tags = [];
        var selected_judges = [];

        // Fill filters arrays if available
        if (sessionStorage.getItem(app.judgesSessionKey))
            selected_judges = JSON.parse(sessionStorage.getItem(app.judgesSessionKey)).join();
        if (sessionStorage.getItem(app.tagsSessionKey))
            selected_tags = JSON.parse(sessionStorage.getItem(app.tagsSessionKey)).join();

        var filters =
            {
                'selected_tags': selected_tags,
                'selected_judges': selected_judges
            };

        // Send request to server in order to save filters to server session
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: token,
                'selected_filters': filters,
            },
            success: function (data) {
                // Clear other sorting in URL queries
                window.location.replace(redirectURL);
            }, error: function (data) {
                console.log(data);
            }
        });
    },

    /**
     * Clear problems filters from the server and client session
     * @param url
     * @param token
     * @param redirectURL
     */
    clearProblemsFilters: function (url, token, redirectURL) {

        // Confirm first
        if (confirm("Are you sure?\nThis will clear all saved data including organizers,tags, ..etc!")) {

            // Clear session
            app.clearSession();

            // Send clear request
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: token,
                },
                success: function () {
                    window.location.replace(redirectURL);
                }
            });
        }
    },
    /**
     * the typeahead autoComplete Function
     *
     * @param path the url to get the data fot autocompletion
     * @param list the list from the view
     * @param sessionKey to store selected items
     * @param localDataList to search locally instead of touching the server
     * @returns {{source: source, updater: updater}}
     */
    autoCompleteList: function (path, list, sessionKey, localDataList) {
        return ({
            source: function (query, process) {
                // if localData available, just process the saved array
                if (localDataList && localDataList.length) {
                    return process(localDataList);
                }
                // if not, request the array from server
                else {
                    // Use this threshold to prevent looking for username
                    // using the first letter only (a lot of possibilities exist)
                    if (query.length >= 2) {
                        console.log(path);
                        return $.get(path, {query: query}, function (data) {
                            console.log(data);
                            return process(data);
                        });
                    }
                }
            },
            updater: function (item) {
                // Get selected item name
                var itemName = item.name;

                // Sync auto-completed element with session
                var isFound = app.syncDataWithSession(sessionKey, itemName, false);

                // Add the element to view
                if (!isFound) {
                    app.renderListElements(itemName, list, sessionKey);
                }
                //Don't return the item name back in order not to keep it in the text box field

            }
        });
    },
    /**
     * Sync given data with the session stored by the given key (if not exists, else if exists,
     * remove the element from session)
     *
     * @param sessionKey
     * @param elementValue
     * @param checkbox
     * @return boolean isFound: true if the element was fond before
     */
    syncDataWithSession: function (sessionKey, elementValue, detaching, checkbox) {
        var isFound = false;
        // Get saved problems ids
        var savedValues = sessionStorage.getItem(sessionKey);

        if (savedValues) { // check if there're any stored IDs

            // Convert to array
            var savedValuesArray = JSON.parse(savedValues);

            // Check for elementValue existance
            var idx = savedValuesArray.indexOf(elementValue);

            if (savedValuesArray.indexOf(elementValue) == -1) { // Add elementValue
                // Check if adding problems that problems count doesn't exceed limit
                if (sessionKey == app.problemsIDsSessionKey) {
                    if (savedValuesArray.length < app.contestProblemsMaxCount) {
                        savedValuesArray.push(elementValue);
                    } else {
                        // Un-check the box
                        $(checkbox).prop('checked', false);

                        alert("Contest cannot have more than " + app.contestProblemsMaxCount + " problems!");
                    }
                } else { // if not problems, keep adding
                    savedValuesArray.push(elementValue);
                }
            }
            else {      // Item Found
                if (detaching)
                    savedValuesArray.splice(idx, 1);
                isFound = true;
            }
        } else { // if null create array and push element
            savedValuesArray = [];
            savedValuesArray.push(elementValue);
        }

        // Save to session
        sessionStorage.setItem(sessionKey, JSON.stringify(savedValuesArray));

        return isFound;
    },

    /**
     * Retrieve lists (e.g. tags, organizers) from session in order to render them
     *
     * @param sessionKey
     * @param list
     */
    retrieveListsFromSession: function (sessionKey, list) {
        if (!list) return;
        var savedValues = sessionStorage.getItem(sessionKey);

        if (savedValues) { // check if there're any stored IDs

            // Convert to array
            var savedValuesArray = JSON.parse(savedValues);

            // Loop and render
            savedValuesArray.forEach(function (itemName) {
                app.renderListElements(itemName, list, sessionKey);
            });
        }
    },
    /**
     * Append new element to the DOM tree
     *
     * @param itemName
     * @param list
     * @param sessionKey
     */
    renderListElements: function (itemName, list, sessionKey) {

        // Create new DOM element and assign basic attributes
        var entry = document.createElement('span');

        entry.className += ' element label label-success';

        // Add element content and append to view
        entry.innerHTML = itemName + '<span onclick="app.removeListItemButtonClick(this, \'' + sessionKey + '\')" class="remove-btn" data-role="remove" data-name="' + itemName + '"></span>';

        list.appendChild(entry);

    },
    /**
     * Set hidden inputs values from sessions, then clear sessions
     */
    moveContestSessionDataToHiddenFields: function () {
        // Set value
        if (sessionStorage.getItem(app.organizersSessionKey))
            $("#organisers-ids-hidden").val(JSON.parse(sessionStorage.getItem(app.organizersSessionKey)).join());

        if (sessionStorage.getItem(app.inviteesSessionKey))
            $("#invitees-ids-hidden").val(JSON.parse(sessionStorage.getItem(app.inviteesSessionKey)).join());

        if (sessionStorage.getItem(app.problemsIDsSessionKey))
            $("#problems-ids-hidden").val(JSON.parse(sessionStorage.getItem(app.problemsIDsSessionKey)).join());

        // Set date time field to server timezone
        var m = moment($("#time").val()).format(); // User input (user timezone)
        var d = new Date(m); // Convert to date
        var timezoneDiff = d.getTimezoneOffset(); // Get client-server tz diff

        // Get time in server time zone
        m = moment(d.toISOString()).add('m', timezoneDiff).format('YYYY-MM-DD HH:mm:ss');

        // Send to server to check and then server will store in UTC
        $("#time").val(m);

        // Clear sessions
        app.clearSession();
    },
    /**
     * Bind close button to clear item from given list in sessionStorage
     *
     * @param element
     * @param sessionKey
     */
    removeListItemButtonClick: function (element, sessionKey) {
        // Remove view
        $(element).parent().remove();

        // Get element type
        var elementName = $(element).data('name');
        app.syncDataWithSession(sessionKey, elementName, true);

    },
    /**
     * Clear client side sessions
     */
    clearSession: function () {
        sessionStorage.setItem(app.problemsIDsSessionKey, "");
        sessionStorage.setItem(app.tagsSessionKey, "");
        sessionStorage.setItem(app.judgesSessionKey, "");
        sessionStorage.setItem(app.organizersSessionKey, "");
        sessionStorage.setItem(app.inviteesSessionKey, "");
        sessionStorage.setItem(app.contestNameSessionKey, "");
        sessionStorage.setItem(app.contestTimeSessionKey, "");
        sessionStorage.setItem(app.contestDurationSessionKey, "");
        sessionStorage.setItem(app.contestPrivateVisibilitySessionKey, "");
    },

    // ==================================================
    //        SHEET PAGE FILTERS FUNCTIONS
    // ==================================================

    /**
     * Set problems filters hidden inputs values from sessions, then clear sessions
     */
    moveProblemsIDsSessionDataToHiddenField: function () {
        // Set value
        $("#problems-ids-hidden").val(JSON.parse(sessionStorage.getItem(app.problemsIDsSessionKey)).join());

        // Clear sessions
        sessionStorage.setItem(app.problemsIDsSessionKey, '');
        sessionStorage.setItem(app.tagsSessionKey, '');
        sessionStorage.setItem(app.judgesSessionKey, '');
    },
    // ==================================================
    //        PROBLEMS PAGE FILTERS FUNCTIONS
    // ==================================================

    /**
     * Set problems filters hidden inputs values from sessions, then clear sessions
     */
    moveProblemsFiltersSessionDataToHiddenFields: function () {
        // Set value
        $("#tags").val(JSON.parse(sessionStorage.getItem(app.tagsSessionKey)).join());
    },
    /**
     * Check if the url query contains judges/tags, then toggle the panel
     */
    toggleFiltersPanel: function () {
        var queries = app.getUrlVars();
        // If the URL queries contain judges/tags
        // or the session of applied tags still holds some tags (so the problems now
        // are actually filtered)
        // , show more filters panel
        var areTagsApplied = (queries['tag'] && queries['tag'] != '');
        var areJudgesApplied = (queries['judges%5B%5D'] && queries['judges%5B%5D'] != '');
        var tagsInSession = sessionStorage.getItem('problems_filters_tags_session_key');
        var doesSessionContainTags = (tagsInSession) ? (tagsInSession != '[]' && tagsInSession != '') : false;

        if (areTagsApplied || areJudgesApplied || doesSessionContainTags) {
            $('#more-filters-button').html('less');
            $('#hidden-filters').slideToggle();
        }
    },
    // ==================================================
    //        CONTEST PROBLEMS SORT FUNCTIONS
    // ==================================================

    /**
     * Toggle all views related to reordering contest problems
     */
    toggleSortableStatus: function () {

        $('.problems-reorder-view').fadeToggle();

        if (app.isTableSortable) {
            $('#contest-problems-tbody').sortable("disable");
        } else {
            $('#contest-problems-tbody').sortable({
                helper: app.fixHelperModified,
                stop: app.updateIndex,
                handle: 'td.problems-reorder-view > .fa-bars',
                cursor: 'move',
            }).disableSelection();
        }
    },

    /**
     * Keep element width the same while dragging
     *
     * @param e
     * @param tr
     */
    fixHelperModified: function (e, tr) {
        var $originals = tr.children();
        var $helper = tr.clone();
        $helper.children().each(function (index) {
            $(this).width($originals.eq(index).width())
        });
        return $helper;
    },
    /**
     * Update row index in the array by clearing it first then refill with the new order
     * The problem-id is fetched from html data binding
     *
     * @param e
     * @param ui
     */
    updateIndex: function (e, ui) {
        app.newSortedIDsArray = [];
        $('td.index', ui.item.parent()).each(function () {
            app.newSortedIDsArray.push($(this).data('problem-id'));
        });
    },

    /**
     * Send save problems order request to backend
     *
     * @param url
     * @param token
     */
    saveProblemsOrderToDB: function (url, token) {
        if (app.newSortedIDsArray.length) {
            $.ajax({
                type: "POST",
                url: url,
                data: {_token: token, _method: "PUT", problems_order: app.newSortedIDsArray},
                success: function (result) {
                    if (result.status == 204)
                        location.reload();
                    else alert('Something went wrong!')
                },
                error: function () {
                    alert('Something went wrong!');
                }
            });
        } else {
            alert('No changes have been made!');
        }
    },

    // ==================================================
    //            BLOGS FUNCTIONS
    // ==================================================

    /**
     * Sends Ajax Request to delete a comment
     * @param element the delete icon element
     * @param commentID the Comment ID
     * @param url the Delete  URL
     * @param token the CSRF Token
     */
    deleteSinglePostComment: function (element, url, token) {

        if (confirm('Are you sure you want to delete this comment?')) {

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: token,
                    _method: "DELETE"
                },
                success: function () {
                    // Remove comment
                    $(element).parent().parent().parent().remove();
                },
                error: function (result) {
                }
            });
        }
    },

    /**
     * Send update element request to server
     *
     * @param element
     * @param url
     * @param token
     */
    updateComment: function (element, url, token) {
        var commentRootNode = $(element).parent().parent();
        var commentBody = $(commentRootNode.find('.comment-body p')[0]);

        // Get comment new body text
        var commentNewValue = $(commentRootNode.find('.comment-edit-textarea')[0]).val();

        // Send Ajax Request
        $.ajax({
            url: url,
            type: 'post',
            data: {
                _token: token,
                method: 'PUT',
                body: commentNewValue,
            },
            success: function () {
                commentBody.html(commentNewValue);

                // Hide comment editor and show comment div
                commentBody.show();

                // Call cancel function to hide the editor div
                app.cancelEditComment(commentRootNode.find('.edit-comment-icon')[0]);

            },
            error: function (result) {
            }
        });
    },

    /**
     * Show edit comment view
     *
     * @param element
     */
    showAddCommentSection: function (element) {

        var commentRootNode = $(element).parent().parent();

        var addCommentSection = commentRootNode.find('.add-comment-section')[0];

        // Show add comment section
        $(addCommentSection).show();

        // Associate textarea with its SimpleMDE
        app.associateTextAreaWithSimpleMDE($(addCommentSection.find('.comment-edit-textarea'))[0]);

    },

    /**
     * Show edit comment view
     *
     * @param element
     */
    editCommentClick: function (element) {

        // Get comment value
        var commentRootNode = $(element).parent().parent();
        var commentBody = commentRootNode.find('.comment-body p')[0];
        var commentValue = $(commentBody).html();

        // Hide comment paragraph
        $(commentBody).hide();

        // Add/Show textarea
        if ($(commentRootNode.find('.comment-edit-textarea')).length == 0) { // no textarea for editing the comment

            var newEditCommentEditor = $('<div class="comment-editor"></div>');
            var newEditCommentTextarea = $('<textarea class="comment-edit-textarea">' + commentValue + '</textarea>');

            newEditCommentEditor.append(newEditCommentTextarea);
            $(commentBody).parent().append(newEditCommentEditor);

            // SimpleMDE
            app.associateTextAreaWithSimpleMDE($(commentRootNode.find('.comment-edit-textarea'))[0]);

        } else { // Textarea exists
            $($(commentRootNode.find('.comment-editor'))[0]).show();
        }

        // Show cancel icon and hide edit icon
        $(commentRootNode.find('.cancel-edit-comment-icon')).show();
        $(commentRootNode.find('.save-comment-icon')).show();
        $(commentRootNode.find('.edit-comment-icon')).hide();
    },

    /**
     * Hide edit comment view
     *
     * @param element
     */
    cancelEditComment: function (element) {
        // Get comment value
        var commentRootNode = $(element).parent().parent();
        var commentBody = $(commentRootNode.find('.comment-body p')[0]);
        var commentEditor = $(commentRootNode.find('.comment-editor'))[0];

        // Hide comment paragraph
        $(commentEditor).hide();
        $(commentBody).show();

        // Show edit icon and hide save/cancel icons
        $(commentRootNode.find('.cancel-edit-comment-icon')).hide();
        $(commentRootNode.find('.save-comment-icon')).hide();
        $(commentRootNode.find('.edit-comment-icon')).show();
    },
    // ==================================================
    //              UTILITIES FUNCTIONS
    // ==================================================

    /**
     * Associate the give textarea with simple mde
     * such that any changes in the simple mde, reflect to the textarea
     *
     * @param textarea
     */
    associateTextAreaWithSimpleMDE: function (textarea) {

        // SimpleMDE
        var simplemde = new SimpleMDE({
            element: textarea,
            spellChecker: false, //Disable Spell Checker
        });

        // Set on change to update textarea whenever the smde changes
        simplemde.codemirror.on("change", function () {
            $(textarea).html(simplemde.value());
        });
    },

    /**
     * Move data from session to hidden field
     * @param fldID
     * @param sessionKey
     * @param clear
     */
    moveDataFromSessionToField: function (fldID, sessionKey, clear) {
        // Set value
        $("#" + fldID).val(JSON.parse(sessionStorage.getItem(sessionKey)).join());

        // Clear sessions
        if (clear) {
            sessionStorage.setItem(sessionKey, '');
        }
    },

    /**
     * Read a page's GET URL variables and return them as an associative array.
     */
    getUrlVars: function () {
        var queries = {};
        $.each(document.location.search.substr(1).split('&'), function (c, q) {
            if (q.length > 0) {
                var i = q.split('=');
                queries[i[0].toString()] = i[1].toString();
            }
        });
        return queries;
    },

    /**
     * When the data provided by php contains data (e.g. filters) that should be
     * in sync with local session, but the user removed this session, we've to
     * make sure that the sync happens in this function
     *
     * @param sessionKey
     * @param array
     */
    syncDataFromRequestToSession: function (sessionKey, array) {
        // Set to session
        sessionStorage.setItem(sessionKey, array);
    },

    /**
     * Get the filters stored in server session (via php binding to data-X attributes)
     * and then format these filters to match javascript session format
     *
     * @param element
     */
    getFiltersFromElementAndFormat: function (element) {

        // Get php selected tags,judges from the data binding attribute
        // and convert to javascript format
        var tags = element.data('selected-tags');
        var judges = element.data('selected-judges');
        if (tags) {
            var selectedTags = '["' + tags.replace(',', '","') + '"]';
            // Sync with session
            app.syncDataFromRequestToSession(app.tagsSessionKey, selectedTags);
        }
        if (judges) {
            var selectedJudges;
            try {
                selectedJudges = '["' + judges.replace(',', '","') + '"]';
            } catch (e) {
                selectedJudges = '["' + judges + '"]';
            }
            // Sync with session
            app.syncDataFromRequestToSession(app.judgesSessionKey, selectedJudges);
        }
    }
};

// Run the app when the document gets fully loaded
$(document).ready(function () {
    app.justLetItGo();
});
