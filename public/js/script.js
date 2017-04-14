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
        url: './notifications/mark_all_read',
        data: "",
        success: function () {
            // Change icon to light bell
            $("#notifications-icon").removeClass("fa-bell");
            $("#notifications-icon").removeClass("dark-red");
            $("#notifications-icon").addClass("fa-bell-o");

            // Prevent future clicks to execute this function
            $("#notifications-icon").parent()[0].onclick = null;
        }
    })
}

/**************************************/
/*</editor-fold>*/

