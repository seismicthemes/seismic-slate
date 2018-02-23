jQuery(document).ready(function($) {
	var scrolled = false;

	$(window).scroll(
	    function(){
		var b = $(window).scrollTop();
		if (b == 0) {
		    $('#access').removeClass('scroll').addClass('normal');
		    scrolled = false;
		}
		else if (b > 0) {
		    $('#access').removeClass('normal').addClass('scroll');
		    scrolled = true;
		}
	    });

	$('#access').hover(
	    function(){
		if (scrolled) {
		    // window scrolled, hence access class should be 'scroll'
		    $('#access').removeClass('scroll').addClass('normal');
		}
	    },
	    function(){
		if (scrolled) {
		    // window scrolled, hence access class should be 'normal' (from mouseenter)
		    $('#access').removeClass('normal').addClass('scroll');
		}
	    });

	
});
