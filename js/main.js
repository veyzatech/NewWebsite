(function ($) {
    "use stict";

    //Fix for Skills fill
    skillsFill();

    //Fix for Big Number Counter
    var k = 1;
    $(".big-number-start").each(function () {
        $(this).attr('id', 'count' + k);
        k++;
    });

    $(window).on('scroll', function () {
		animateElement();
        animateCounterUp();
    });

    //Portfolio Item Hover Fix
    $(".grid-item a.item-link").on('hover', function () {
        $(this).toggleClass("highlighted");
    });


    //Text slider
    $(".text-slider").each(function () {
        var id = $(this).attr('id');

        var auto_value = window[id + '_auto'];
        var hover_pause = window[id + '_hover'];
        var speed_value = window[id + '_speed'];

        auto_value = (auto_value === 'true') ? true : false;
        hover_pause = (hover_pause === 'true') ? true : false;

        $('#' + id).slick({
            arrows: true,
            dots: false,
            infinite: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            speed: 750,
            autoplay: auto_value,
            autoplaySpeed: speed_value,
            pauseOnHover: hover_pause,
            fade: true,
            draggable: true,
            adaptiveHeight: true
        });
    });

    //Image slider
    $(".image-slider").each(function () {
        var id = $(this).attr('id');

        var auto_value = window[id + '_auto'];
        var hover_pause = window[id + '_hover'];
        var speed_value = window[id + '_speed'];

        auto_value = (auto_value === 'true') ? true : false;
        hover_pause = (hover_pause === 'true') ? true : false;

        $('#' + id).slick({
            arrows: false,
            dots: true,
            infinite: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            speed: 750,
            autoplay: auto_value,
            autoplaySpeed: speed_value,
            pauseOnHover: hover_pause,
            fade: true,
            draggable: true,
            adaptiveHeight: true
        });
    });

//PrettyPhoto initial
    $('a[data-rel]').each(function () {
        $(this).attr('rel', $(this).data('rel'));
    });

    $("a[rel^='prettyPhoto']").prettyPhoto({
        slideshow: false, /* false OR interval time in ms */
        overlay_gallery: false, /* If set to true, a gallery will overlay the fullscreen image on mouse over */
        default_width: 1280,
        default_height: 720,
        deeplinking: false,
        social_tools: false,
        iframe_markup: '<iframe src ="{path}" width="{width}" height="{height}" frameborder="no" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>'
    });

//Portfolio
    var grid = $('.grid').imagesLoaded(function () {
        grid.isotope({
            itemSelector: '.grid-item',
            masonry: {
                columnWidth: '.grid-sizer'
            }
        });
        $('.filters-button-group').on('click', '.button', function () {
            var filterValue = $(this).attr('data-filter');
            grid.isotope({filter: filterValue});
            grid.on('arrangeComplete', function () {
                $(".grid-item:visible a[rel^='prettyPhoto']").prettyPhoto({
                    slideshow: false, /* false OR interval time in ms */
                    overlay_gallery: false, /* If set to true, a gallery will overlay the fullscreen image on mouse over */
                    default_width: 1280,
                    default_height: 720,
                    deeplinking: false,
                    social_tools: false,
                    iframe_markup: '<iframe src ="{path}" width="{width}" height="{height}" frameborder="no" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>'
                });
            });
        });

        // change is-checked class on buttons
        $('.button-group').each(function (i, buttonGroup) {
            var $buttonGroup = $(buttonGroup);
            $buttonGroup.on('click', '.button', function () {
                $buttonGroup.find('.is-checked').removeClass('is-checked');
                $(this).addClass('is-checked');
            });
        });
    });
   

    //Fix for No-Commnets
    $("#comments").each(function () {
        if ($.trim($(this).html()) === '')
        {
            $(this).remove();
        }
    });

    //Fix for Menu
    $(".header-holder").sticky({topSpacing: 0});

    //Slow Scroll
    $('#header-main-menu ul li a, .scroll').on("click", function (e) {
        if ($(this).attr('href') === '#')
        {
            e.preventDefault();
        } else {
            if ($(window).width() < 1024) {
                if (!$(e.target).is('.sub-arrow'))
                {
                    $('html, body').animate({scrollTop: $(this.hash).offset().top - 77}, 1500);
                    $('.menu-holder').removeClass('show');
                    $('#toggle').removeClass('on');
                    return false;
                }
            } else
            {
                $('html, body').animate({scrollTop: $(this.hash).offset().top - 77}, 1500);
                return false;
            }
        }
    });

    //Logo Click Fix
    $('.header-logo').on("click", function (e) {
        if ($(".page-template-onepage").length) {
            e.preventDefault();
            $('html, body').animate({scrollTop: 0}, 1500);
        }
    });

    $(window).scrollTop(1);
    $(window).scrollTop(0);

    $('.single-post .num-comments a, .single-portfolio .num-comments a').on('click', function (e) {
        e.preventDefault();
        $('html, body').animate({scrollTop: $(this.hash).offset().top}, 2000);
        return false;
    });

    //Add before and after "blockquote" custom class
    $('blockquote.inline-blockquote').prev('p').addClass('wrap-blockquote');
    $('blockquote.inline-blockquote').next('p').addClass('wrap-blockquote');
    $('blockquote.inline-blockquote').css('display', 'table');

    //Placeholder show/hide
    $('input, textarea').on("focus", function () {
        $(this).data('placeholder', $(this).attr('placeholder'));
        $(this).attr('placeholder', '');
    });
    $('input, textarea').on("blur", function () {
        $(this).attr('placeholder', $(this).data('placeholder'));
    });

    //Fit Video
    $(".site-content").fitVids();

    //Fix for Default menu
    $(".default-menu ul:first").addClass('sm sm-clean main-menu');

    //Set menu
    $('.main-menu').smartmenus({
        subMenusSubOffsetX: 1,
        subMenusSubOffsetY: -8,
        markCurrentTree: true
    });

    var $mainMenu = $('.main-menu').on('click', 'span.sub-arrow', function (e) {
        var obj = $mainMenu.data('smartmenus');
        if (obj.isCollapsible()) {
            var $item = $(this).parent(),
                    $sub = $item.parent().dataSM('sub');
            $sub.dataSM('arrowClicked', true);
        }
    }).bind({
        'beforeshow.smapi': function (e, menu) {
            var obj = $mainMenu.data('smartmenus');
            if (obj.isCollapsible()) {
                var $menu = $(menu);
                if (!$menu.dataSM('arrowClicked')) {
                    return false;
                }
                $menu.removeDataSM('arrowClicked');
            }
        }
    });

    //Show-Hide header sidebar
    $('#toggle').on('click', multiClickFunctionStop);

    
    $('.contact-form [type="submit"]').on('click',function(){
        SendMail(); 
    });
	
    contactFormWidthFix();




$(window).on('load', function () {
    handleStickyHeader();
    animateCounterUp();

    // Animate the elemnt if is allready visible on load
    animateElement();

    //Fix for hash
    var hash = location.hash;
    if ((hash != '') && ($(hash).length))
    {
        $('html, body').animate({scrollTop: $(hash).offset().top - 77}, 1);
    }

    $('.doc-loader').fadeOut(600);


});


$(window).on('resize', function () {
    contactFormWidthFix();
    handleStickyHeader();
});

//------------------------------------------------------------------------
//Helper Methods -->
//------------------------------------------------------------------------


function animateElement(e) {

    $(".animate").each(function (i) {

        var top_of_object = $(this).offset().top;
        var bottom_of_window = $(window).scrollTop() + $(window).height();
        if ((bottom_of_window - 70) > top_of_object) {
            $(this).addClass('show-it');
        }

    });

};

function skillsFill() {
    $(".v-skill-fill").each(function (i) {
        $(this).width($(this).data("fill")).height($(this).data("fill"));
    });
};

function contactFormWidthFix() {
    $('.wpcf7 input[type=text], .wpcf7 input[type=email], .wpcf7 textarea').innerWidth($('.wpcf7-form').width());
};

function multiClickFunctionStop(e) {
    $('#toggle').off("click");
    $('#toggle').toggleClass("on");
    if ($('#toggle').hasClass("on"))
    {
        $('.menu-holder').addClass('show');
        $('#toggle').on("click", multiClickFunctionStop);
    } else
    {
        $('.menu-holder').removeClass('show');
        $('#toggle').on("click", multiClickFunctionStop);
    }
};


$(window).on('scroll resize', function () {
    var currentSection = null;
    $('.section').each(function () {
        var element = $(this).attr('id');
        if ($('#' + element).is('*')) {
            if ($(window).scrollTop() >= $('#' + element).offset().top - 115)
            {
                currentSection = element;
            }
        }
    });

    $('#header-main-menu ul li').removeClass('active').find('a[href*="#' + currentSection + '"]').parent().addClass('active');
});

function is_touch_device() {
    return !!('ontouchstart' in window);
}

function animateCounterUp(e) {
    $(".big-number-content:not(.animate-done)").each(function () {
        var top_of_object = $(this).offset().top;
        var bottom_of_window = $(window).scrollTop() + $(window).height();
        if ((bottom_of_window - 70) > top_of_object) {

            $(this).addClass("animate-done");
            //Big Number Count Up
            var options = {
                useEasing: true,
                useGrouping: true,
                separator: ' ',
                decimal: '.'
            };

            var start = parseInt($(this).find(".big-number-start").html().replace(/\s+/g, ''));
            var stop = parseInt($(this).find(".big-number-stop").html().replace(/\s+/g, ''));
            var duration = parseInt($(this).find(".big-number-duration").html().replace(/\s+/g, ''));


            var demo = new CountUp($(this).find(".big-number-start").attr("id"), start, stop, 0, duration, options);
            if (!demo.error) {
                demo.start();
            } else {
                console.error(demo.error);
            }
        }
    });
};

function isValidEmailAddress(emailAddress) {
    var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
    return pattern.test(emailAddress);
}

function SendMail() {

    var emailVal = $('#contact-email').val();

    if (isValidEmailAddress(emailVal)) {
        var params = {
            'action': 'SendMessage',
            'name': $('#name').val(),
            'email': $('#contact-email').val(),
            'subject': $('#subject').val(),
            'message': $('#message').val()
        };
        $.ajax({
            type: "POST",
            url: "sendMail.php",
            data: params,
            success: function (response) {
                if (response) {
                    var responseObj = $.parseJSON(response);
                    if (responseObj.ResponseData)
                    {
                        alert(responseObj.ResponseData);
                    }
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                //xhr.status : 404, 303, 501...
                var error = null;
                switch (xhr.status)
                {
                    case "301":
                        error = "Redirection Error!";
                        break;
                    case "307":
                        error = "Error, temporary server redirection!";
                        break;
                    case "400":
                        error = "Bad request!";
                        break;
                    case "404":
                        error = "Page not found!";
                        break;
                    case "500":
                        error = "Server is currently unavailable!";
                        break;
                    default:
                        error = "Unespected error, please try again later.";
                }
                if (error) {
                    alert(error);
                }
            }
        });
    } else
    {
        alert('Your email is not in valid format');
    }
};

function handleStickyHeader() {
    const width = $(window).width();
    const height = $(window).height();

    // Remove sticky for screens smaller than iPad 12.9"
    if (width < 1024 || (width === 1024 && height < 1366)) {
        $('.menu-wrapper').removeClass('sticky-header');
    } else {
        $('.menu-wrapper').addClass('sticky-header');
    }
}


})(jQuery);