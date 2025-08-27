(function ($) {
    "use strict";

/*--------------------------
    Preloader
---------------------------- */

    $(window).on("load", function () {
        var pre_loader = $("#preloader");
        pre_loader.fadeOut("slow", function () {
            $(this).remove();
        });
    });

/*---------------------
    TOP Menu Stick
--------------------- */

    var windows = $(window);
    var sticky = $("#sticker");

    windows.on("scroll", function () {
        var scroll = windows.scrollTop();
        if (scroll < 300) {
            sticky.removeClass("stick");
        } else {
            sticky.addClass("stick");
        }
    });

/*----------------------------
    jQuery MeanMenu
------------------------------ */

    var mean_menu = $("nav#dropdown");
    mean_menu.meanmenu();

    // Nice Select JS
    $("select").niceSelect();

/*---------------------
    wow .js
--------------------- */

    if (typeof WOW === "function") {
        new WOW({
            offset: 100,
            mobile: true,
        }).init();
    }

/*--------------------------
    scrollUp
---------------------------- */

    // Initialize the scrollUp plugin
    $(window).scroll(function() {
        if ($(this).scrollTop() > 200) {
            $('.scrollUp').fadeIn();
        } else {
            $('.scrollUp').fadeOut();
        }
    });
    
    // Scroll to top on button click
    $('.scrollUp').click(function() {
        $('html, body').animate({scrollTop: 0}, 800);
        return false;
    });

/*--------------------------
 collapse
---------------------------- */

    /*--------------------------
    MagnificPopup
    ---------------------------- */
    if (typeof $.fn.magnificPopup === "function") {
        $(".video-play").magnificPopup({
            type: "iframe",
        });
    }

    /*--------------------------
 Parallax
---------------------------- */
    var parallaxeffect = $(window);
    parallaxeffect.stellar({
        responsive: true,
        positionProperty: "position",
        horizontalScrolling: false,
    });

    /*---------------------
 Testimonial carousel
---------------------*/

    var review = $(".testimonial-carousel");
    review.owlCarousel({
        loop: true,
        nav: true,
        margin: 20,
        dots: false,
        navText: [
            "<i class='ti-angle-left'></i>",
            "<i class='ti-angle-right'></i>",
        ],
        autoplay: false,
        responsive: {
            0: {
                items: 1,
            },
            768: {
                items: 2,
            },
            1000: {
                items: 4,
            },
        },
    });
    /*--------------------------
     Payments carousel
---------------------------- */
    var payment_carousel = $(".payment-carousel");
    payment_carousel.owlCarousel({
        loop: true,
        nav: false,
        autoplay: false,
        margin: 30,
        dots: false,
        responsive: {
            0: {
                items: 2,
            },
            700: {
                items: 4,
            },
            1000: {
                items: 6,
            },
        },
    });

   
})(jQuery);
