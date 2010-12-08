jQuery( function($) {
	
	jQuery('a').click( function(e) {
		e.preventDefault();
		e.stopPropagation();
		window.location = jQuery(this).attr('href');
	});
	
	/*
	$.extend( $.mobile, {
		defaultTransition: 'none',
		ajaxLinksEnabled: false,
		ajaxFormsEnabled: false
	});
	
	$(document).bind("mobileinit", function() {
		$.extend( $.mobile, {
			defaultTransition: 'none',
			ajaxLinksEnabled: false,
			ajaxFormsEnabled: false
		});
	});
	*/
});