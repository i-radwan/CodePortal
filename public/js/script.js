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

        //region Date time pickers

        // Enable date time pickers
        $('.datetimepicker').datetimepicker({
            format: 'Y-m-d H:i:s',
            minDate: 0 // for after today limitation
        });
        //endregion


        //region Maintain bootstrap tabs

        // Maintain bootstrap selected tabs on page reload
        // i.e. keep the selected tab before reloading the page selected
        // after the reloading finishes

        // if the link contains has, look for the link who should open this tab
        // and show the related tab
        if (location.hash) {
            $("a[href='" + location.hash + "']").tab("show");
        }

        // When a tab link is clicked attach the hash of the tab to the url
        // (to use it in the previous if statement)
        $(document.body).on("click", "a[data-toggle]", function (event) {
            location.hash = this.getAttribute("href");
        });

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

            // Fetch all tags
            app.fetchAllTagsFromDB();

            // Configure lists and autocomplete typeahead
            app.configureAutoCompleteLists(true, true, true);

            // Render saved data from session into form
            app.fillContestFormFromSession();
        }

        // Add/Edit sheet page
        if ($("#add-edit-sheet-page-hidden-element").length) {

            // Fetch all tags
            app.fetchAllTagsFromDB();

            // Configure lists and autocomplete typeahead
            app.configureAutoCompleteLists(true, false, false);

            // Set the session keys for sheets problems
            app.problemsIDsSessionKey = 'sheets_problems_ids_session_key';
            app.tagsSessionKey = 'sheets_tags_ids_session_key';
            app.judgesSessionKey = 'sheets_judges_ids_session_key';

            // Fill judges checkboxes
            app.fillJudgesCheckboxes();

            // Fill tags, organisers lists
            app.retrieveListsFromSession(app.tagsSessionKey, app.tagsList, 0);

            // Fill problems checkboxes
            app.fillProblemsTableCheckboxes();
        }

        // Problems filters
        if ($("#problems-page-hidden-element").length) {
            // Set different tagsSessionKey for problems page
            // to maintain the tags stored for add/edit contest
            app.tagsSessionKey = 'problems_filters_tags_session_key';

            // Fetch all tags
            app.fetchAllTagsFromDB();

            // Configure lists and autocomplete typeahead
            app.configureAutoCompleteLists(true, false, false);

            // Retrieve tags from session to view
            app.retrieveListsFromSession(app.tagsSessionKey, app.tagsList, 0);

            // Toggle filters more div if query contains tags or judges
            app.toggleFiltersPanel();
        }

        // Group page
        if ($("#single-group-page-hidden-element").length) {

            // Configure lists and autocomplete typeahead
            app.configureAutoCompleteLists(false, false, true);

            app.inviteesSessionKey = 'group_invitees_session_key';

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
    fillAnswerModal: function (problemID, sheetID, url, solution_lang) {
        // If no solution_lang, means no previous solution is provided
        // Assume that c_cpp is going to be used
        if (!solution_lang.length) solution_lang = "c_cpp";

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
        // Prevent click event propagation (such that dropdown menu doesn't
        // close after deleting) notification
        if (!e)
            e = window.event;
        //IE9 & Other Browsers
        if (e.stopPropagation) {
            e.stopPropagation();
        }
        //IE8 and Lower
        else {
            e.cancelBubble = true;
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
    //            ADD/EDIT CONTEST FUNCTIONS
    // ==================================================
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
     * Configure lists and allow auto complete
     *
     * @param tags: bind tags with auto complete
     * @param organisers: bind organisers with auto complete
     */
    configureAutoCompleteLists: function (tags, organisers, invitees) {

        // Tags AutoComplete
        if (tags) {
            // Define tag lists and apply autocomplete to it
            this.tagsList = document.getElementById("tags-list");
            // Call typeahead for Tags autoCompletion
            $('#tags-auto').typeahead(app.autoComplete($("#tags-auto").data('tags-path'), app.tagsList, 0));
        }

        // Organisers AutoComplete
        if (organisers) {
            //Organisers List
            this.organisersList = document.getElementById("organisers-list");

            //Call typeahead for Organisers autoCompletion
            $('#organisers-auto').typeahead(app.autoComplete($("#organisers-auto").data('organisers-path'), app.organisersList, 1));
        }

        // Invitees AutoComplete
        if (invitees) {
            //Organisers List
            this.inviteesList = document.getElementById("invitees-list");

            //Call typeahead for Organisers autoCompletion
            $('#invitees-auto').typeahead(app.autoComplete($("#invitees-auto").data('invitees-path'), app.inviteesList, 2));
        }
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
            app.retrieveListsFromSession(app.tagsSessionKey, app.tagsList, 0);
            app.retrieveListsFromSession(app.organizersSessionKey, app.organisersList, 1);
            app.retrieveListsFromSession(app.inviteesSessionKey, app.inviteesList, 2);

            // Fill form basic fields
            $("#name").val(sessionStorage.getItem(app.contestNameSessionKey));
            $("#time").val(sessionStorage.getItem(app.contestTimeSessionKey));
            $("#duration").val(sessionStorage.getItem(app.contestDurationSessionKey));
            $("#private_visibility").val(sessionStorage.getItem(app.contestPrivateVisibilitySessionKey));

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
                $("#private").prop('checked', true);
            } else {
                $("#public").prop('checked', true);
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
        console.log(filters, url);
        // Send request to server in order to save filters to server session
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: token,
                'selected_filters': filters,
            },
            success: function (data) {
                console.log(data);
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
     * @param list the list from th view
     * @param type 0:Means Tags autoCompletion, 1: Means  Organisers autoCompletion, 2: Invitees list
     * @returns {{source: source, updater: updater}}
     */
    autoComplete: function (path, list, type) {
        return ({
            source: function (query, process) {
                // if tags auto complete, just process the saved array
                if (type == 0) {
                    return process(app.allTagsList);
                }
                // if organizers, request the array from server
                else if (type == 1 || type == 2) {
                    // Use this threshold to prevent looking for username
                    // using the first letter only (a lot of possibilities exist)
                    if (query.length >= 2) {
                        return $.get(path, {query: query}, function (data) {
                            return process(data);
                        });
                    }
                }
            },
            updater: function (item) {
                // Get selected item name
                var itemName = item.name;

                var isFound;
                // Sync auto-completed element with session
                if (type == 2) { // Invitees
                    isFound = app.syncDataWithSession(app.inviteesSessionKey, itemName, false);
                }
                // Sync auto-completed element with session
                else if (type == 1) { // Organizers
                    isFound = app.syncDataWithSession(app.organizersSessionKey, itemName, false);
                }
                else if (type == 0) { // Tags
                    isFound = app.syncDataWithSession(app.tagsSessionKey, itemName, false);
                }

                // Add the element to view
                if (!isFound) {
                    app.renderElementsFromSession(itemName, list, type);
                }

                //Don't return the item name back in order not to keep it in the text box field
                return;
            }
        });
    },

    /**
     * Sync given data with the session stored by the given key (if not exists, else if exists,
     * remove the element from session)
     *
     * @param sessionKey
     * @param elementValue
     * @return boolean isFound: true if the element was fond before
     */
    syncDataWithSession: function (sessionKey, elementValue, detaching) {
        var isFound = false;
        // Get saved problems ids
        var savedValues = sessionStorage.getItem(sessionKey);

        if (savedValues) { // check if there're any stored IDs

            // Convert to array
            var savedValuesArray = JSON.parse(savedValues);

            // Check for elementValue existance
            var idx = savedValuesArray.indexOf(elementValue);

            if (savedValuesArray.indexOf(elementValue) == -1) // Add elementValue
                savedValuesArray.push(elementValue);
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
     * @param type
     */
    retrieveListsFromSession: function (sessionKey, list, type) {
        var savedValues = sessionStorage.getItem(sessionKey);

        if (savedValues) { // check if there're any stored IDs

            // Convert to array
            var savedValuesArray = JSON.parse(savedValues);

            // Loop and render
            savedValuesArray.forEach(function (itemName) {
                app.renderElementsFromSession(itemName, list, type);
            });
        }
    },
    /**
     * Append new element to the DOM tree
     *
     * @param itemName
     * @param list
     * @param type
     */
    renderElementsFromSession: function (itemName, list, type) {

        // Create new DOM element and assign basic attributes
        var entry = document.createElement('span');
        entry.className += ' element label label-success';

        // Add element content and append to view
        entry.innerHTML = itemName + '<span onclick="app.closeButtonClick(this)" data-role="remove" data-name="' + itemName + '" data-type="' + type + '"></span>';

        list.appendChild(entry);

    },
    /**
     * Set hidden inputs values from sessions, then clear sessions
     */
    moveSessionDataToHiddenFields: function () {
        // Set value
        if (sessionStorage.getItem(app.organizersSessionKey))
            $("#organisers-ids-hidden").val(JSON.parse(sessionStorage.getItem(app.organizersSessionKey)).join());

        if (sessionStorage.getItem(app.inviteesSessionKey))
            $("#invitees-ids-hidden").val(JSON.parse(sessionStorage.getItem(app.inviteesSessionKey)).join());

        if (sessionStorage.getItem(app.problemsIDsSessionKey))
            $("#problems-ids-hidden").val(JSON.parse(sessionStorage.getItem(app.problemsIDsSessionKey)).join());

        // Clear sessions
        app.clearSession();
    },
    /**
     * Bind close button to clear item from given list in sessionStorage
     */
    closeButtonClick: function (element) {
        // Remove view
        $(element).parent().remove();

        // Get element type
        var type = $(element).data('type');
        var elementName = $(element).data('name');

        // Detach from session
        if (type == 2) { // Organizers
            app.syncDataWithSession(app.inviteesSessionKey, elementName, true);
        }// Detach from session
        else if (type == 1) { // Organizers
            app.syncDataWithSession(app.organizersSessionKey, elementName, true);
        }
        else if (type == 0) { // Tags
            app.syncDataWithSession(app.tagsSessionKey, elementName, true);
        }
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
    //          GROUP PAGE FUNCTIONS
    // ==================================================

    /**
     * Move group invitees from session to field
     * @param fldID
     * @param sessionKey
     * @param clear
     */
    moveInviteesFromSessionToField: function (fldID, sessionKey, clear) {
        // Set value
        $("#" + fldID).val(JSON.parse(sessionStorage.getItem(sessionKey)).join());

        // Clear sessions
        if (clear) {
            sessionStorage.setItem(sessionKey, '');
        }
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
    //              UTILITIES FUNCTIONS
    // ==================================================

    /**
     * Read a page's GET URL variables and return them as an associative array.
     */
    getUrlVars: function () {
        var queries = {};
        $.each(document.location.search.substr(1).split('&'), function (c, q) {
            var i = q.split('=');
            queries[i[0].toString()] = i[1].toString();
        });
        return queries;
    }
};

// Run the app when the document gets fully loaded
$(document).ready(function () {
    app.justLetItGo();
});
