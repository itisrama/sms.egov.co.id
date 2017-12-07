jQuery.noConflict();
(function($) {
	$(function() {
		$(document).on('click', '.nav-tabs a', function(e) {
			$('#tab_position').val($(this).attr('href').replace('#', ''));
		});

		$('.select_all').click(function(event) {  //on click 
			if(this.checked) { // check select status
				$('.show_fields').each(function() { //loop through each checkbox
					this.checked = true;  //select all checkboxes with class "checkbox1"               
				});
			} else{
				$('.show_fields').each(function() { //loop through each checkbox
					this.checked = false; //deselect all checkboxes with class "checkbox1"                       
				});
			}
		});
	});

	
})(jQuery);