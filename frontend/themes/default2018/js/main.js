
$(document).ready(function () {

    scrollShow = false;

    $(window).scroll(function () {
        scrollTop = $(window).scrollTop();

        if (scrollTop > 0) {
            if (scrollShow)
                return;
            console.log(scrollShow);
            scrollShow = true;
            $('.navbar').addClass('scrollNav');
            $('.navbar').css('opacity', 0)
                .slideDown('slow').animate({ opacity: 1 },
                    { queue: false, duration: 'slow' }
                );
        }
        else {
            $('.navbar').removeClass('scrollNav');
            /*console.log('false');*/
            scrollShow = false;
        }
    });

    /* call slick nav*/
    $('.mobile-menu').slicknav({
        prependTo: '.navbar-header',
        parentTag: 'liner',
        allowParentLisnks: true,
        duplicate: true,
        label: '',
        closedSymbol: '<i class="lni-chevron-right"></i>',
        openedSymbol: '<i class="lni-chevron-down"></i>',
    });


/**Call Carousel*/
$('.categOwl').owlCarousel({
    loop:true,
    autoplay:true,
    dots:false,
    margin:10,
    autoplayHoverPause:true,
    smartSpeed:350,
    responsiveClass:true,
    responsive:{
        0:{
            items:1,
            nav:true
        },
        575:{
            items:2,
            nav:true
        },
        
        800:{
            items:2,
            nav:true
        },
        1025:{
            items:5,
            nav:true,
            loop:true
        }
    }
})
$('.FeaturesOwl').owlCarousel({
    loop:true,
    autoplay:true,
    dots:false,
    margin:10,
    autoplayHoverPause:true,
    smartSpeed:350,
    responsiveClass:true,
    responsive:{
        0:{
            items:1,
            nav:true,
            loop:true
        },
        575:{
            items:2,
            nav:false,
            loop:true
        },
        991:{
            items:3,
            nav:true,
            loop:true,
        }
    }
})
    /**Call Carousel*/
    $('.testemonialOwl').owlCarousel({
        loop: true,
        dots: false,
        autoplay: true,
        margin: 10,
        autoplayHoverPause: true,
        smartSpeed: 350,
        left: true,
        responsiveClass: true,
        responsive: {
            0:{
                items: 1,
                nav: true,
                loop:true,
            },
            1025:{
                items:2,
                nav: true,
                loop: true
            }

        }
    });
    $('.listPageOwl').owlCarousel({
        autoplay: true,
        loop: true,
        dots: true,
        nav: true,
        responsiveClass: true,
        responsive: {
            0: {
                items: 1,
                nav: true,
                loop: true,
            }
        }
    })
    new WOW().init();

    /**Editor */
    $('#summernote').summernote({
        tabsize: 2,
        height: 250
    });

    /********************FAQ callapse********************************/
    $('.accordion-panel').click(function () {
        $('.accordion-panel').not(this).find('span:first').removeClass(' fas fa-minus  ').addClass('fas fa-plus ');
        $(this).find('span:first').toggleClass('fas fa-minus fas fa-plus');
    })

    $('.faqHeader').click(function () {
        $(this).parent().find('.faqbody').slideToggle(400);
    });
    $(".faqHeader").on("click", "a", function(e) { e.preventDefault() });


});
