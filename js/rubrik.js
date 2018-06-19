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
	function calculate_running_total() {
		var running_total = 0;
		$("input[type=radio]:checked").each(function() {
			running_total += parseFloat($(this).attr('value'));
		});
		$('#running_total').html(running_total);
	}
	
	// Calculate running total on load
	calculate_running_total();
		
	// Calculate running total on every radio button change
	$('.container').on('change', 'input[type=radio]', function() {
		calculate_running_total();
	});
});
