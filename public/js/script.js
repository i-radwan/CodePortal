// Constants
const URL = '127.0.0.1:8000/';

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


/*<editor-fold desc="Notifications">*/

/**
 * Send Ajax request to mark all user notifications as READ once the user clicks the
 * notifications icon in the header
 */
function markAllNotificationsRead() {
    $.ajax({
        type: "GET",
        url: 'notifications/mark_all_read',
        data: "",
        success: function () {
            // Change icon to light bell
            $("#notifications-icon").removeClass("fa-bell");
            $("#notifications-icon").removeClass("dark-red");
            $("#notifications-icon").addClass("fa-bell-o");

            // Prevent future clicks to execute this function
            $("#notifications-icon").parent()[0].onclick = null;
        }
    });
}

/**
 * Lazy delete certain notification
 * @param string csrf token
 * @param int notificationID
 * @param object element clicked element
 */
function cancelNotification(e, token, notificationID, element) {
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
        type: "DELETE",
        url: 'notification/' + notificationID,
        data: {"_token": token},
        success: function (result) {
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
    });
    return false;
}
/**************************************/
/*</editor-fold>*/

