$(document).ready(function () {


    /*<editor-fold desc="Single Contest Page">*/


    /**************************************/
    /*</editor-fold>*/

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

// ToDo Comment this hell block
if ($("#code-editor").length) {
    var editor = ace.edit("code-editor");
    editor.setTheme("ace/theme/twilight");
    editor.session.setMode("ace/mode/javascript");
} else if ($("#code-editor-members").length) {
    var editor_members = ace.edit("code-editor-members");
    editor_members.setTheme("ace/theme/twilight");
    editor_members.session.setMode("ace/mode/javascript");
}
/**
 *
 */
$("#answer-model-submit-button").click(function () {
    $('#problem-solution').val(editor.getValue());
    return true;
});

/**
 *
 *
 * @param problemID
 * @param sheetID
 * @param url
 */
function getSolutionFromFile(url) {
    $.ajax({
        type: "GET",
        url: url,
        success: function (problemSolution) {
            // Fill editors with solution
            if (editor) {
                editor.setValue(problemSolution);
                editor.gotoLine(1);
            } else if (editor_members) {
                editor_members.setValue((problemSolution.length) ? problemSolution : 'No solution provided!');
                editor_members.setReadOnly(true);
            }
        },
        error: function (result) {
            console.log(result.responseText);
        }
    });
}
/**
 * Fill problem answer form/p fields
 *
 * @param int problemID
 * @param int sheetID
 * @param string url for solution file
 * @param problemSolution
 */
function fillAnswerModal(problemID, sheetID, url) {
    if ($('#problem-id').length && $('#sheet-id').length) {
        $('#problem-id').val(problemID);
        $('#sheet-id').val(sheetID);
    }
    getSolutionFromFile(url);
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

