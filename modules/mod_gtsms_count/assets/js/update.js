jQuery.noConflict();
(function($) {
	$(function() {
		getCount();
		setInterval(getCount, 1000);

		var mailboxLink = $('.t3-megamenu a[href="'+$('#alertMessages').attr('href')+'"]');

		mailboxLink.append($('#countUnread'));
		if($('#countUnread').html() > 0) {
			$('#countUnread').show();
		}
	});

	function getCount() {
		var countMsg		= $('#countMsg').val();
		var countSent		= $('#countSent').val();
		var countFailed		= $('#countFailed').val();
		var msgUrl			= $('#msgUrl').val();
		var scrollBottom	= $('#scrollBottom').val();

		$.getJSON(count_url, function( data ) {
			if(data.user_id > 0) {
				$('#countMessages .received').html(data.received);
				$('#countMessages .sent').html(data.sent);

				if(data.new > countMsg) {
					var alertMsg = Joomla.JText._('MOD_GTSMS_COUNT_N_NEW_MESSAGES').replace('%s', data.new);

					$('#alertMessages .msg').html(alertMsg);
					$('#countUnread').html(data.unread);
					if(data.unread > 0) {
						$('#countUnread').show();
					} else {
						$('#countUnread').hide();
					}

					$('#alertMessages').show();
					$('#countMessages').hide();
				}

				if(!data.new) {
					$('#alertMessages').hide();
					$('#countMessages').show();
				}

				var isUpdate = false;
				isUpdate = data.new > countMsg ? true : isUpdate;
				isUpdate = data.sent > countSent ? true : isUpdate;
				isUpdate = data.failed > countFailed ? true : isUpdate;
				isUpdate = $('#messageTable').length ? isUpdate : false;

				if(isUpdate) {
					updateRows(window.location.href, $('#messageTable').hasClass('scrollBottom'));
				}
				
				$('#countMsg').val(data.new);
				$('#countSent').val(data.sent);
				$('#countFailed').val(data.failed);
			} else {
				var alertMsg = Joomla.JText._('MOD_GTSMS_COUNT_LOGIN');
				$('#alertMessages .msg').html(alertMsg);

				$('#alertMessages').show();
				$('#countMessages').hide();
			}
		});
	}

	function updateRows(url, scrollBottom) {
		var tbodyData = $('#messageTable tbody.rowData');
		var tbodyNull = $('#messageTable tbody.rowNull');
		$.getJSON(url, {json: "1"}).done(function(data) {
			if(data.length) {
				var row = $('tr:first', tbodyData);
				tbodyData.html(row);
				$.each(data, function(i, item){
					var rowItem = row.clone();
					$('.id', rowItem).html(item.id);
					$('.type', rowItem).html(item.type);
					$('.contact', rowItem).html(item.contact);
					$('.message', rowItem).html(item.message);
					$('.date', rowItem).html(item.date);
					$('.count', rowItem).html(item.count);
					$('.button', rowItem).html(item.button);
					rowItem.show();
					tbodyData.append(rowItem);
				});
				tbodyData.show();
				tbodyNull.hide();

				if(scrollBottom) {
					$("html, body").animate({ scrollTop: $(document).height() }, "slow");
				}
				$('.hasTooltip', tbodyData).tooltip({container: 'body'});
			} else {
				tbodyData.hide();
				tbodyNull.show();
			}
			
		});
	}
})(jQuery);