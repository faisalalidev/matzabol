// JavaScript Document

function selectTabById(id){
	$('.with-nav-tabs a[href="'+id+'"]').trigger('click')
}

// Side Nav
function openNav() {
    document.getElementById("mySidenav").style.width = "350px";
	//$('.overlay').show().css({'width':'100%','margin-left':'350px'});
	$('.overlay-bg').fadeIn('slow');
}

function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
	//$('.overlay').hide().css({'width':'0','margin-left':'0'});
	$('.overlay-bg').fadeOut('fast');
}

$('div.overlay-bg').click(function(e) {
    $('.closebtn').trigger('click');
});
// Side Nav End