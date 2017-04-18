$(document).ready(function () {

    /*<editor-fold desc="Animations">*/
    /*====================================*/
    /*========== animate.css   ===========*/
    /*====================================*/

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

    /**************************************/
    /*</editor-fold>*/

    $('.datetimepicker').datetimepicker({
        format: 'Y-m-d H:i:s',
        minDate: 0 // for after today limitation
    });
});

/*<editor-fold desc="Maintain selected bootstrap tab">*/

// Used to keep bootstrap selected tab visible after refreshing the page
$(document).ready(function () {
    if (location.hash) {
        $("a[href='" + location.hash + "']").tab("show");
    }
    $(document.body).on("click", "a[data-toggle]", function (event) {
        location.hash = this.getAttribute("href");
    });
});

$(window).on("popstate", function () {
    var anchor = location.hash || $("a[data-toggle='tab']").first().attr("href");
    $("a[href='" + anchor + "']").tab("show");
});

/**************************************/
/*</editor-fold>*/


/*<editor-fold desc="Code editor">*/

// The following section handles Ace code editor
// Tasks:
// 1. Fill the solution modal with problem previous solution if available
// 2. Reserve user modifications to code even if he closes the modal
// 3. Allow user to save the new answer
// 4. Allow members to view the code only

// This var is used to store the problems solution once fetched from the Ajax
// request, then every owner modification to the code is also reserved here.
// When the user refreshes the page, the non-saved modifications are gone...
var currentProblemsSolutions = {};

// These vars store the current state if user is owner or member
var isOwner = false;
var isMember = false;
var editor;
if ($("#code-editor").length) { // Check if owner
    isOwner = true;
} else if ($("#code-editor-members").length) {
    isMember = true;
}

// Here I initialize the code editor for both owner and members views
if (isOwner) {
    editor = ace.edit("code-editor"); // Initialize Ace editor with code editor container ID editor.getSession().on('change', function () {

    // Catch owner input in code editor
    // Save the modifications to the currentProblemsSolutions storage
    // which is indexed by problemID
    editor.getSession().on('change', function () {
        if ($('#problem-id').val().length)
            currentProblemsSolutions[$('#problem-id').val()] = editor.getValue();
    });
} else if (isMember) {
    editor = ace.edit("code-editor-members"); // Initialize Ace editor with code editor container ID
}

if (editor) {
    editor.setTheme("ace/theme/twilight"); // Set the editor theme
    editor.session.setMode("ace/mode/c_cpp"); // Set initial mode to c_cpp
}

// When user clicks save answer button
// Before the form submission, the hidden textarea gets filled with
// code editor value, such that the textarea value (i.e. code editor value)
// gets transmitted with the form request to the controller
// THIS happens because we cannot catch code editor value easily
$("#answer-model-submit-button").click(function () {
    $('#problem-solution').val(editor.getValue());
    return true;
});

/**
 * Retrieve problem solution from url
 *
 * @param int problemID used for storing solution once retrieved
 * @param url used to contact backend
 */
function getSolutionFromFile(problemID, url) {
    $.ajax({
        type: "GET",
        url: url,
        async: false,
        success: function (problemSolution) {
            // Fill editors with solution
            editor.focus(); // get user focus

            if (isOwner) {
                // Store retrieved solution in the currentProblemsSolutions
                currentProblemsSolutions[problemID] = problemSolution;

                // Set the editor value and move cursor
                editor.setValue(problemSolution, -1);
            } else if (isMember) {
                // Set value and make editor read only for members
                // If no solution, print no solution message
                editor.setValue((problemSolution.length) ? problemSolution : 'No solution provided!', -1);
                editor.setReadOnly(true);
            }
        },
        error: function (result) {
            console.log(result.responseText);
        }
    });
}
/**
 * Fill problem answer modal fields once Solution button is clicked
 *
 * @param int problemID
 * @param int sheetID
 * @param string url for solution file
 * @param solution_lang
 */
function fillAnswerModal(problemID, sheetID, url, solution_lang) {
    // If no solution_lang, means no previous solution is provided
    // Assume that c_cpp is going to be used
    if (!solution_lang.length) solution_lang = "c_cpp";

    // Set selected language in solution_lang menu
    $("#solution_lang").val(solution_lang);

    // Set editor mode to match selected solution_lang (retrieved from database)
    editor.getSession().setMode("ace/mode/" + solution_lang);

    // Set form values (problem id and sheet id, used in form submission to store solution)
    if ($('#problem-id').length && $('#sheet-id').length) {
        $('#problem-id').val(problemID);
        $('#sheet-id').val(sheetID);
    }

    // Check if the solution of this problem hasn't retrieved yet
    // get it (and inside this fn call, the solution will be stored in currentProblemsSolutions)
    if (!currentProblemsSolutions[problemID]) {
        getSolutionFromFile(problemID, url);
    } else {
        // If solution exists, render it in the editor
        editor.setValue(currentProblemsSolutions[problemID], -1);
    }
}

/**************************************/
/*</editor-fold>*/



/*<editor-fold desc="Notifications">*/

/**
 * Send Ajax request to mark all user notifications as READ once the user clicks the
 * notifications icon in the header
 *
 * @param string csrf token
 */
function markAllNotificationsRead(token, url) {
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
}

/**
 * Lazy delete certain notification
 *
 * @param string csrf token
 * @param int notificationID
 * @param object element clicked element
 */
function cancelNotification(e, token, url, element) {
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
            hideNotificationElement(element);
        }
    });
    return false;
}

/**
 * Hide the notification element from notifications panel once deleted successfully
 *
 * @param object element
 */
function hideNotificationElement(element) {
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
}
/**************************************/
/*</editor-fold>*/

/*<editor-fold desc="Add contest">*/

// Session constants
const problemsIDsSessionKey = 'problems_ids_session_key';
const tagsSessionKey = 'tags_session_key';
const judgesSessionKey = 'judges_session_key';
const organizersSessionKey = 'organizers_session_key';
const contestNameSessionKey = 'contest_name_session_key';
const contestTimeSessionKey = 'contest_time_session_key';
const contestDurationSessionKey = 'contest_duration_session_key';
const contestPrivateVisibilitySessionKey = 'contest_private_visibility_session_key';

//Tags AutoComplete

//First : get the tagsList from the view
var tagsList = document.getElementById("tagsList");

//Call typeahead for Tags autoCompletion
$('input.tagsAuto').typeahead(autoComplete($("#tagsAuto").data('tags-path'), tagsList, 0));

//Organisers AutoComplete

//Organisers List
var organisersList = document.getElementById("organisers-list");

//Call typeahead for Organisers autoCompletion
$('input.organisers-auto').typeahead(autoComplete($("#organisers-auto").data('organisers-path'), organisersList, 1));


/**
 * This function saves the selected filters (selected Tags and Selected judges) to the session
 * It takes url of syncing filters, the token
 * @param url
 * @param token
 */
function applyFilters(url, token) {
    //Get the current filters from the view
    var filters =
        {
            'selected_tags': JSON.parse(sessionStorage.getItem(tagsSessionKey)).join(),
            'selected_judges': JSON.parse(sessionStorage.getItem(judgesSessionKey)).join()
        };
    $.ajax({
        url: url,
        type: 'POST',
        data: {
            _token: token,
            'selected_filters': filters,
        },
        success: function (data) {
        }
    });
    //Clear other filtering in URL queries
    document.getElementById("clearTableLink").click();
}

//Auto Complete Functions
/**
 * the typeahead autoComplete Function
 *
 * @param path the url to get the data fot autocompletion
 * @param list the list from th view
 * @param type 0:Means Tags autoCompletion, 1: Means  Organisers autoCompletion
 * @returns {{source: source, updater: updater}}
 */
function autoComplete(path, list, type) {
    return ({
        source: function (query, process) {
            return $.get(path, {query: query}, function (data) {
                return process(data);
            })
        },
        updater: function (item) {
            // Get selected item name
            var itemName = item.name;

            var isFound;
            // Sync auto-completed element with session
            if (type == 1) { // Organizers
                isFound = syncDataWithSession(organizersSessionKey, itemName, false);
            }
            else if (type == 0) { // Tags
                isFound = syncDataWithSession(tagsSessionKey, itemName, false);
            }

            // Add the element to view
            if (!isFound) {
                renderElementsFromSession(itemName, list, type);
            }

            //Don't return the item name back in order not to keep it in the text box field
            return;
        }
    });
}

//Wait for organisers deletion icon in the selected organisers list
$(document).on('mousedown', '.organiser-close-icon, .tag-close-icon', function (event) {

    // Remove view
    $(this).parent().remove();

    // Get element type
    var type = $(event.target).data('type');
    var elementName = $(event.target).data('name');

    // Detach from session
    if (type == 1) { // Organizers
        syncDataWithSession(organizersSessionKey, elementName, true);
    }
    else if (type == 0) { // Tags
        syncDataWithSession(tagsSessionKey, elementName, true);
    }
});

/**
 * Sync given data with the session stored by the given key (if not exists, else if exists,
 * remove the element from session)
 *
 * @param sessionKey
 * @param elementValue
 * @return boolean isFound: true if the element was fond before
 */
function syncDataWithSession(sessionKey, elementValue, detaching) {
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
}

/**
 * Retrieve lists (e.g. tags, organizers) from session in order to render them
 *
 * @param sessionKey
 * @param list
 * @param type
 */
function retrieveListsFromSession(sessionKey, list, type) {
    var savedValues = sessionStorage.getItem(sessionKey);

    if (savedValues) { // check if there're any stored IDs

        // Convert to array
        var savedValuesArray = JSON.parse(savedValues);

        // Loop and render
        savedValuesArray.forEach(function (itemName) {
            renderElementsFromSession(itemName, list, type);
        });
    }
}
/**
 * Append new element to the DOM tree
 *
 * @param itemName
 * @param list
 * @param type
 */
function renderElementsFromSession(itemName, list, type) {

    // Create new DOM element and assign basic attributes
    var entry = document.createElement('li');
    entry.setAttribute("value", itemName);

    // Add the item name and the delete button according to the send type
    // (tag or organizer)
    if (type == 1) {
        var text = '<button class="organiser-close-icon" data-name="' + itemName + '" data-type="1"></button>';
    } else if (type == 0) {
        var text = '<button class="tag-close-icon" data-name="' + itemName + '" data-type="0"></button>';
    }

    // Add element content and append to view
    entry.innerHTML = text + itemName;
    list.appendChild(entry);
}

$(document).ready(function () {

    // Render saved data from session
    if ($("#add-edit-contest-form").length) { // Check if in add/edit contest view

        // Recheck selected problems IDs checkboxes
        var savedProblemsIDs = sessionStorage.getItem(problemsIDsSessionKey);

        if (savedProblemsIDs) { // check if there're any stored IDs

            // Convert to array
            var savedProblemsIDsArray = JSON.parse(savedProblemsIDs);

            // Loop over IDs
            savedProblemsIDsArray.forEach(function (element) {
                $("#problem-checkbox-" + element).prop('checked', true);
            });
        }

        // Recheck selected judges IDs checkboxes
        var savedJudgesIDs = sessionStorage.getItem(judgesSessionKey);

        if (savedJudgesIDs) { // check if there're any stored IDs

            // Convert to array
            var savedJudgesIDsArray = JSON.parse(savedJudgesIDs);

            // Loop over IDs
            savedJudgesIDsArray.forEach(function (element) {
                $("#judge-checkbox-" + element).prop('checked', true);
            });
        }

        // Fill tags, organisers lists
        retrieveListsFromSession(tagsSessionKey, tagsList, 0);
        retrieveListsFromSession(organizersSessionKey, organisersList, 1);

        // Fill form basic fields
        $("#name").val(sessionStorage.getItem(contestNameSessionKey));
        $("#time").val(sessionStorage.getItem(contestTimeSessionKey));
        $("#duration").val(sessionStorage.getItem(contestDurationSessionKey));
        $("#private").val(sessionStorage.getItem(contestPrivateVisibilitySessionKey));

        // Set form fields on change listeners
        $("#name").change(function () {
            sessionStorage.setItem(contestNameSessionKey, $("#name").val());
        });
        $("#time").change(function () {
            sessionStorage.setItem(contestTimeSessionKey, $("#time").val());
        });
        $("#duration").change(function () {
            sessionStorage.setItem(contestDurationSessionKey, $("#duration").val());
        });
        if (sessionStorage.getItem(contestPrivateVisibilitySessionKey) == 1) {
            $("#private").prop('checked', true);
        } else {
            $("#public").prop('checked', true);
        }
        $("#private").change(function () {
            sessionStorage.setItem(contestPrivateVisibilitySessionKey, 1);
        });
        $("#public").change(function () {
            sessionStorage.setItem(contestPrivateVisibilitySessionKey, 0);
        });
    }
});

/**
 * Set hidden inputs values from sessions, then clear sessions
 */
function moveSessionDataToHiddenFields() {
    // Set value
    $("#organisers-ids-hidden").val(JSON.parse(sessionStorage.getItem(organizersSessionKey)).join());
    $("#problems-ids-hidden").val(JSON.parse(sessionStorage.getItem(problemsIDsSessionKey)).join());

    // Clear sessions
    sessionStorage.setItem(problemsIDsSessionKey, "");
    sessionStorage.setItem(tagsSessionKey, "");
    sessionStorage.setItem(judgesSessionKey, "");
    sessionStorage.setItem(organizersSessionKey, "");
    sessionStorage.setItem(contestNameSessionKey, "");
    sessionStorage.setItem(contestTimeSessionKey, "");
    sessionStorage.setItem(contestDurationSessionKey, "");
    sessionStorage.setItem(contestPrivateVisibilitySessionKey, "");
}
/**************************************/
/*</editor-fold>*/

/*<editor-fold desc="Contest problems sort">*/

// Flag to indicate if the table is in sort mode or not
var isTableSortable = false;
// Array to store the last contest problem IDs order
var newSortedIDsArray = [];

/**
 * Toggle all views related to reordering contest problems
 */
function toggleSortableStatus() {

    $('.problems-reorder-view').fadeToggle();

    if (isTableSortable) {
        $('#contest-problems-tbody').sortable("disable");
    } else {
        $('#contest-problems-tbody').sortable({
            helper: fixHelperModified,
            stop: updateIndex,
            handle: 'td.problems-reorder-view > .fa-bars',
            cursor: 'move',
        }).disableSelection();
    }
}

/**
 * Keep element width the same while dragging
 *
 * @param e
 * @param tr
 */
var fixHelperModified = function (e, tr) {
    var $originals = tr.children();
    var $helper = tr.clone();
    $helper.children().each(function (index) {
        $(this).width($originals.eq(index).width())
    });
    return $helper;
};
/**
 * Update row index in the array by clearing it first then refill with the new order
 * The problem-id is fetched from html data binding
 *
 * @param e
 * @param ui
 */
var updateIndex = function (e, ui) {
    newSortedIDsArray = [];
    $('td.index', ui.item.parent()).each(function () {
        newSortedIDsArray.push($(this).data('problem-id'));
    });
};

/**
 * Send save problems order request to backend
 *
 * @param url
 * @param token
 */
function saveProblemsOrderToDB(url, token) {
    if (newSortedIDsArray.length) {
        $.ajax({
            type: "POST",
            url: url,
            data: {_token: token, _method: "PUT", problems_order: newSortedIDsArray},
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
}

/**************************************/
/*</editor-fold>*/
