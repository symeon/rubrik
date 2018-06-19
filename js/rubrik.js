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
	
/*
 * ============================================================================
 * Populate the running total box
 * ============================================================================
 */
	$('.container').on('change', 'input[type=radio]', function() {
		var running_total = 0;
		$("input[type=radio]:checked").each(function() {
			running_total += parseFloat($(this).attr('value'));
		});
		$('#running_total').html(running_total);
	});
});
