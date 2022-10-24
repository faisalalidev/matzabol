
(function ($) {
    "use strict";

    $('.stop-pro').on('click', function(e){
        e.stopPropagation();
        e.preventDefault();
    });

    /*[ Load page ]
    ===========================================================*/
    $(".animsition").animsition({
        inClass: 'fade-in',
        outClass: 'fade-out',
        inDuration: 1500,
        outDuration: 800,
        linkElement: '.animsition-link',
        loading: true,
        loadingParentElement: 'html',
        loadingClass: 'animsition-loading-1',
        loadingInner: '<div class="cp-spinner cp-meter"></div>',
        timeout: false,
        timeoutCountdown: 5000,
        onLoadEvent: true,
        browser: [ 'animation-duration', '-webkit-animation-duration'],
        overlay : false,
        overlayClass : 'animsition-overlay-slide',
        overlayParentElement : 'html',
        transition: function(url){ window.location.href = url; }
    });
    
    
    /*[ Top bar ]
    ===========================================================*/
    var menu = $('.item-menu1');
    var sub_menu_is_showed = -1;

    for(var i=0; i<menu.length; i++){
        $(menu[i]).on('click', function(){ 
            
                if(jQuery.inArray( this, menu ) == sub_menu_is_showed){
                    $(this).toggleClass('show-sub-menu1');
                    sub_menu_is_showed = -1;
                }
                else {
                    for (var i = 0; i < menu.length; i++) {
                        $(menu[i]).removeClass("show-sub-menu1");
                        //console.log(i);
                    }
                    $(this).toggleClass('show-sub-menu1'); 
                    sub_menu_is_showed = jQuery.inArray( this, menu );
                }
        });
    }

    $(".item-menu1, .sub-menu1").click(function(event){
        event.stopPropagation();
    });

    $(window).on("click", function(){
        for (var i = 0; i < menu.length; i++) {
            menu[i].classList.remove("show-sub-menu1");
        }
        sub_menu_is_showed = -1;
    });


    /*[ Right bar ]
    ===========================================================*/
    var menuRight = $('.have-sub-menu-rightbar');

    $('.have-sub-menu-rightbar a').on('click', function(e){
        e.preventDefault();
    });

    $(menuRight).on('click',function(){
        $(this).children('.sub-menu-rightbar').slideToggle();
        $(this).toggleClass('show-sub-menu-rightbar');
    });
    
    /* ------------------------------------ */
    $('.btn-hide-rightbar').on('click',function(){
        $('.rightbar').addClass('hide-rightbar');
        $('.rightbar').removeClass('show-rightbar');
    });

    $('.btn-show-rightbar').on('click',function(){
        $('.rightbar').removeClass('hide-rightbar');
        $('.rightbar').addClass('show-rightbar');
    });

    $(window).on("resize", function(){
        $('.rightbar').removeClass('hide-rightbar');
        $('.rightbar').removeClass('show-rightbar');
    });


     /*[ Left bar ]
    ===========================================================*/
 
     /* ------------------------------------ */
    $('.btn-show-leftbar').on('click',function(){
        $('.leftbar').toggleClass('show-leftbar');
        $(this).toggleClass('ti-close');
        $(this).toggleClass('ti-align-left');
    });

    $(window).on("resize", function(){
        $('.leftbar').removeClass('show-leftbar');
    });

    $('.list-member-chat ul a').on('click',function(e){
        e.preventDefault();
        $('.wrap-chat').addClass('show-message-chat');
    });

    $('.btn-hide-message-chat').on('click',function(){
        $('.wrap-chat').removeClass('show-message-chat');
    });



    /*[ Show / Hide popup1 ]
    ===========================================================*/
    var btnShowPopup1 = $('.js-show-popup1');
    var btnHidePopup1 = $('.js-hide-popup1');
    var popup1 = $('.js-popup1');

    $(btnShowPopup1).on('click', function(){
        $(popup1).addClass('show-popup1');
    })

    $(btnHidePopup1).on('click', function(){
        $(popup1).removeClass('show-popup1');
    })


    /*[ Show / Hide madal chat ]
    ===========================================================*/

    $('.item-quick-chat a').on('click', function(e){
        e.preventDefault();
        var modalChat = $('.modal-chat');

        for(var i=0; i<modalChat.length; i++){
            $(modalChat[i]).removeClass('show-modal-chat');
        }

        $(this).parent().find('.modal-chat').addClass('show-modal-chat');
    });

    $('.btn-hide-element-chat').on('click', function(){
        
        var modalChat = $('.modal-chat');

        for(var i=0; i<modalChat.length; i++){
            $(modalChat[i]).removeClass('show-modal-chat');
        }
    });
    

})(jQuery);