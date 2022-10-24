$(document).ready(function(){


    var wow = new WOW(
        {
            boxClass:     'wow',      // animated element css class (default is wow)
            animateClass: 'animated', // animation css class (default is animated)
            offset:       0,          // distance to the element when triggering the animation (default is 0)
            mobile:       true,       // trigger animations on mobile devices (default is true)
            live:         true,       // act on asynchronously loaded content (default is true)
            callback:     function(box) {
                // the callback is fired every time an animation is started
                // the argument that is passed in is the DOM node being animated
            },
            scrollContainer: null,    // optional scroll container selector, otherwise use window,
            resetAnimation: true,     // reset animation on end (default is true)
        }
    );
    wow.init();

    $('.main-slider').slick({
        speed: 300,
        slidesToShow: 1,
    });
    $('.search-btn a').click(function(){

        $('.search.display').toggle(1000);
    });


    $(".tabs-menu a").click(function(event) {
        event.preventDefault();
        $(this).parent().addClass("current");
        $(this).parent().siblings().removeClass("current");
        var tab = $(this).attr("href");
        $(".tab-content").not(tab).css("display", "none");
        $(tab).fadeIn();
    });

    $(".tabs-left a").click(function(event) {
        event.preventDefault();
        $(this).parent().addClass("current");
        $(this).parent().siblings().removeClass("current");
        var tab = $(this).attr("href");
        $(".tabsleft-content").not(tab).css("display", "none");
        $(tab).fadeIn();
    });

    $('.equal-img').matchHeight();
    $('.equal-cart').matchHeight();
    $('.equal-cart2').matchHeight();
    $('.equal-cart3').matchHeight();
    $('.equal-single').matchHeight();
    $('.listing-bx').matchHeight();
    $('.listing-text h3').matchHeight();



    /*-----------------------Popup-----------------*/

    $('.shipping-overlay').click(function(){
        $('.shipping-overlay').fadeOut();
        $('.shipping-popup').hide();
    });
    $('#closethis').click(function(){
        $('.shipping-overlay').fadeOut();
        $('.shipping-popup').hide();
    });
    $('#reg-pop').click(function() {
        $('.shipping-overlay').show(300);
        $('.shipping-popup').fadeIn(1100);
    });


    /*-----------------------Dropdown-----------------*/

    $('#t-drop').on('click', function(e){
        e.stopPropagation();
        $('ul.top-dropdown').toggle();
    });
    $(document).click( function(){
        $('.top-dropdown').hide();
    });

    /*    $('#my-input2').tokenizer();
     $('#my-input3').tokenizer();*/


});


