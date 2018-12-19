jQuery(document).ready(function($) {
    "use strict";
    //For Pretty Photo Validation
    $('a[data-rel]').each(function() {
        $(this).attr('rel', $(this).data('rel'));
    });

    //Side Bar Menu Js
    if ($('#cp_side-menu-btn, #cp-close-btn').length) {
        $('#cp_side-menu-btn, #cp-close-btn').on('click', function(e) {
            var $navigacia = $('body, #cp_side-menu'),
                val = $navigacia.css('left') === '410px' ? '0px' : '410px';
            $navigacia.animate({
                left: val
            }, 410)
        });
    }

    //SCROLL FOR SIDEBAR NAVIGATION
    if ($('#content-1').length) {
        $("#content-1").mCustomScrollbar({
            horizontalScroll: false
        });
        $(".content.inner").mCustomScrollbar({
            scrollButtons: {
                enable: true
            }
        });
    }

    //Pretty Photo
    if ($('.gallery').length) {
        $(".gallery:first a[rel^='prettyPhoto']").prettyPhoto({
            animation_speed: 'normal',
            theme: 'light_square',
            slideshow: 3000,
            autoplay_slideshow: true
        });
        $(".gallery:gt(0) a[rel^='prettyPhoto']").prettyPhoto({
            animation_speed: 'fast',
            slideshow: 10000,
            hideflash: true
        });
    }

    //BANNER ZOOM IN ZOOM OUT
    if ($('.element').length) {
        $(".element").kenburnsy({
            fullscreen: true
        });
    }

    //TESTIMONIALS SLIDER
    if ($('#testimonials-slider').length) {
        $("#testimonials-slider").owlCarousel({

            navigation: true, // Show next and prev buttons
            slideSpeed: 300,
            paginationSpeed: 400,
            singleItem: true

            // "singleItem:true" is a shortcut for:
            // items : 1, 
            // itemsDesktop : false,
            // itemsDesktopSmall : false,
            // itemsTablet: false,
            // itemsMobile : false

        });
    }

    //POPULAR JOB CATEGPRIES SLIDER
    if ($('#popular-job-slider').length) {
        var owl = $("#popular-job-slider");

        owl.owlCarousel({

            itemsCustom: [
                [0, 1],
                [450, 1],
                [600, 2],
                [700, 2],
                [1000, 3],
                [1200, 4],
                [1400, 4],
                [1600, 4]
            ],
            navigation: true

        });
    }
	
	    //POPULAR Companies SLIDER
    if ($('#popular-companies-slider').length) {
        var owl = $("#popular-companies-slider");

        owl.owlCarousel({

            itemsCustom: [
                [0, 1],
                [450, 1],
                [600, 2],
                [700, 2],
                [1000, 3],
                [1200, 4],
                [1400, 4],
                [1600, 4]
            ],
            navigation: true

        });
    }

    //STICKY HEADER
    if ($('.header-2').length) {
        var stickyNavTop = $('.header-2').offset().top;
        var stickyNav = function() {
            var scrollTop = $(window).scrollTop();
            if (scrollTop > stickyNavTop) {
                $('.header-2').addClass('cp_sticky');
            } else {
                $('.header-2').removeClass('cp_sticky');
            }
        };
        stickyNav();
        $(window).scroll(function() {
            stickyNav();
        });
    }


    //FACT AND FIGURES SECTION COUNTER
    if ($('.counter').length) {
        $('.counter').counterUp({
            delay: 10,
            time: 1000
        });
    }

    //BLOG SLIDER POST
    if ($('#blog-slider-post').length) {
        $("#blog-slider-post").owlCarousel({

            navigation: true, // Show next and prev buttons
            slideSpeed: 300,
            paginationSpeed: 400,
            singleItem: true

            // "singleItem:true" is a shortcut for:
            // items : 1, 
            // itemsDesktop : false,
            // itemsDesktopSmall : false,
            // itemsTablet: false,
            // itemsMobile : false

        });
    }
    //TESTIMIONALS STYLE 3
    if ($('#testimonials-style-3').length) {
        $("#testimonials-style-3").owlCarousel({

            autoPlay: 3000, //Set AutoPlay to 3 seconds

            items: 3,
            autoPlay: true,
            itemsDesktop: [1199, 3],
            itemsDesktopSmall: [979, 3]

        });
    }

    //CONTACT MAP
    if ($('#map_contact_4').length) {
        var map;
        var myLatLng = new google.maps.LatLng(48.85661, 2.35222);
        //Initialize MAP
        var myOptions = {
            zoom: 12,
            center: myLatLng,
            //disableDefaultUI: true,
            zoomControl: true,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            mapTypeControl: false,
            styles: [{
               stylers: [{
                   hue: '#abe3b6'
                }, {
                   saturation: -30
                }, {
                   lightness: 10
                }]
            }],
        }
        map = new google.maps.Map(document.getElementById('map_contact_4'), myOptions);
        //End Initialize MAP
        //Set Marker
       var marker = new google.maps.Marker({
            position: map.getCenter(),
            map: map,
            icon: 'images/map-icon.png'
        });
        marker.getPosition();
        //End marker
        //Set info window
        // var infowindow = new google.maps.InfoWindow({
        // content: '',
        // position: myLatLng
        //});
       //infowindow.open(map);
    }
	
		//MASONARY BLOG

    if ($('#blog-masonrywrap').length) {
        jQuery(function($) {
        function attWookGrid() {
            var options = {
               itemWidth: 260, // Optional min width of a grid item
               autoResize: true, // This will auto-update the layout when the browser window is resized.
                container: $('#blog-masonrywrap'), // Optional, used for some extra CSS styling
                offset: 30, // Optional, the distance between grid items
                flexibleWidth: 260 // Optional, the maximum width of a grid item
            };
            var handler = $('#blog-masonrywrap li');
            handler.wookmark(options);
        }
		$(window).load(function() {
			attWookGrid();
		});	
        });	

    }
	
	//TEXT EDITOR
	if ($('.txtEditor').length) {
	$(".txtEditor").Editor();
	}
	
	    //Accordion 

    $.fn.slideFadeToggle = function(speed, easing, callback) {
       return this.animate({
           opacity: 'toggle',
            height: 'toggle'
        }, speed, easing, callback);
    };

    if ($('.accordion_cp').length) {
        $('.accordion_cp').accordion({
            defaultOpen: 'section1',
            cookieName: 'nav',
            speed: 'slow',
            animateOpen: function(elem, opts) { //replace the standard slideUp with custom function
               elem.next().stop(true, true).slideFadeToggle(opts.speed);
            },
            animateClose: function(elem, opts) { //replace the standard slideDown with custom function
                elem.next().stop(true, true).slideFadeToggle(opts.speed);
            }
        });
    }



    //Function End
});

if ($('#myList li').length) {
    $(document).ready(function() {
        size_li = $("#myList li").size();
        x = 8;
        $('#myList li:lt(' + x + ')').show();
        $('#loadMore').click(function() {
            x = (x + 5 <= size_li) ? x + 5 : size_li;
            $('#myList li:lt(' + x + ')').show();
        });

    });
}