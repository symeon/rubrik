$(document).ready(function(){
	
/*
 * ============================================================================
 * General dynamic stuff here, mostly applicable to all pages
 * ============================================================================
 */
	
	// XHTML-compliant substitute for "target=_blank"
	$('a[rel=external]').attr('target','_blank');

	// Initialize all tooltips
	$(function () {
	    $('[data-tt="tooltip"]').tooltip()
	})
	
});
