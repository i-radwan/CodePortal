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

//Tags AutoComplete parameters
var tagsList = document.getElementById("tagsList");
$('input.tagsAuto').typeahead(autoComplete($("#tagsAuto").data('tags-path'), tagsList, "tags[]", 0));

function applyFilters(url, token) {
    var filters = getCurrentFilters();
    $.ajax({
        // url: "{{Request::url()}}/TagsJudgesFSync",
        url: url,
        type: 'POST',
        data: {
            _token: token,
            cProblemsFilters: filters,
        },
        success: function (data) {
        }
    });
//        location.reload();
    document.getElementById("clearTableLink").click();
}
//Wait for deletion key
$(document).on('mousedown', '.tags-close-icon', function (item) {
    $(this).parent().remove();
});
//get Current Entered Form Data
//    function getEnteredFormData() {
//        var conName = document.getElementById("name").value;
//        var conDate = document.getElementById("time").value;
//        var conDur = document.getElementById("duration").value;
//        var conVis = document.getElementById("private").value;
//        return {
//            name : conName,
//            date : conDate,
//            duration: conDur,
//            visibility : conVis
//        };
//    }

//Tags AutoComplete parameters
function autoComplete(path, list, arrName, type) {
    return ({
        source: function (query, process) {
            return $.get(path, {query: query}, function (data) {
                return process(data);
            })
        },
        updater: function (item) {
            //Get the current values of list items in the unordered list 'tagsList'
            var currentItems = list.getElementsByTagName('li');
            var itemName = (item.name) ? item.name : item;
            //check if it's already included
            var notFound = true;
            console.log(currentItems, itemName);
            for (var i = 0; i < currentItems.length; i++) {
                if (currentItems[i].textContent.trim() == itemName.trim()) {
                    notFound = false;
                }
            }
            if (notFound) {
                //Create a new list item li
                var entry = document.createElement('li');
                entry.setAttribute("name", arrName);
                entry.setAttribute("value", itemName);
                //Add the item name and the delete button
                if (type == 1)
                    var text = '<button class="organiser-close-icon "></button>';
                else
                    var text = '<button class="tags-close-icon "></button>';
                entry.innerHTML = text + itemName;
                list.appendChild(entry);
                if (type == 1) {
                    applyOrganisers();
                }

            }
            //Don't return the item name back in order not to keep it in the text box field
            return;
        }
    });
}
function getListInfo(list) {
    //Reading list elements
    var currentItems = list.getElementsByTagName('li');
    var elements = [];
    for (var i = 0; i < currentItems.length; i++) {
        elements[i] = currentItems[i].textContent;
    }
    return elements;
}
function getCurrentFilters() {
    //Reading Tags
    var tags = getListInfo(tagsList);
    //Reading Judges info
    var judges = [];
    var j = 0;
    var checkboxes = document.getElementsByClassName('judgeState');
    for (var i = 0; checkboxes[i]; ++i) {
        if (checkboxes[i].checked) {
            judges[j] = checkboxes[i].value;
            j = j + 1;
        }
    }
    //Then you have now judges and tags
    return ({'cTags': tags, 'cJudges': judges});
}

// ToDo re-polishing needed
$('.pagination > li').click(function () {
    // ToDo save form
    sessionStorage.setItem("name", $("#name").val());
});
/**************************************/
/*</editor-fold>*/