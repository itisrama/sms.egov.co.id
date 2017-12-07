jQuery.noConflict();
(function($) {
	$(function() {
		$('.modem.modal').each(function(){
			var el = $(this).detach();
			$('body > div').append(el);
		});

		getStatus();
		setInterval(getStatus, 5000);
	});

	function getStatus() {
		$.getJSON(json_url, function( data ) {
			$.each( data.result, function( key, val ) {
				var el = $('.modem-'+val.name);
				var el2 = $('.modem.'+val.name);
				var className = 'modem '+val.name+' status'+val.rating;
				el2.attr('class', className);

				$('.strength', el).html(val.strength);
				$('.quality', el).html(val.quality);
				$('.datetime', el).html(val.datetime);
				$('.activity', el).html(val.activity);

				switch(val.activity) {
					case 'Receiving':
					case 'Sending':
						$('span', el2).show();
						break;
					default:
						$('span', el2).hide();
						break;
				}
			});
		});
	}

})(jQuery);