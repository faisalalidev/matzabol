var $ = jQuery.noConflict();
(function($) {
    "use strict";


//////////////////////////////////////////////////////
  // NAVIGATION PAGES DROPDOWN MENU SCRIPT
  //////////////////////////////////////////////////////
  function mymenu() {
    var myTarget = $(".main_menu_cont > ul > li");
    var childname = '.sub-menu';
    myTarget.each(function() {
      if ( $(this).children(childname).length > 0 ) {
        if($(this).children("i.showSMenu").length < 1) {
          $(this).append('<i class="showSMenu"></i>');
        }
      }   
    });
    $(".main_menu_cont > ul > li > i.showSMenu").on( "click", function(e){
      e.preventDefault();
      $(this).prev("ul").stop().slideToggle(200);
    });
  }
  

    //////////////////////////////////////////////////////
    // calling dropdown navigation menu function
    //////////////////////////////////////////////////////
    mymenu();
    

    //////////////////////////////////////////////////////
    // NAVIGATION SEARCH SCRIPT
    //////////////////////////////////////////////////////
    $(".nav_search>.searchBTN").on("click", function(){
      $(this).parent(".nav_search").find(".mini-search").stop().slideToggle(200);
      return false;
    });
    $(document).mouseup(function (e) {
      var popup = $(".mini-search");
      if (!$('.mini-search').is(e.target) && !popup.is(e.target) && popup.has(e.target).length === 0) {
        popup.slideUp(200);
      }
      return false;
    });
  
    //////////////////////////////////////////////////////
    // MOBILE MENU SCRIPT
    //////////////////////////////////////////////////////
    $('.mbmenu').on( "click", function(e) {
      $(this).next('div').children('ul').slideToggle(400);
      e.preventDefault();
      return false;
    });
})(jQuery);