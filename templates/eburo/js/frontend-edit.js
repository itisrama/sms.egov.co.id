/** 
 *------------------------------------------------------------------------------
 * @package       T3 Framework for Joomla!
 *------------------------------------------------------------------------------
 * @copyright     Copyright (C) 2004-2013 JoomlArt.com. All Rights Reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 * @authors       JoomlArt, JoomlaBamboo, (contribute to this project at github
 *                & Google group to become co-author)
 * @Google group: https://groups.google.com/forum/#!forum/t3fw
 * @Link:         http://t3-framework.org
 *------------------------------------------------------------------------------
 */

! function($) {

	$(document).ready(function() {
		// Fix What T3 Done Wrong to Radios!
		radios = $('fieldset.radio');
		radios.removeClass('radio');
		radios.removeClass('t3onoff');
		radios.children('input').each(function(){
			if($(this).parent().hasClass('inline')) {
				$(this).next('label').addClass('inline');
			}
			$(this).next('label').removeClass('on');
			$(this).next('label').removeClass('off');
			$(this).next('label').addClass('radio');
			$(this).next('label').prepend($(this));
		});
		
		var getLabelClass = function(value) {
			return value == '0' ? 'btn-danger' : value == '1' ? 'btn-success' : 'btn-primary';
		}
		
		// HANDLE ALL BOOTSTRAP RADIOS
		var setRadioClass = function(label) {
			var input = $('#' + label.attr('for'));
			if (input.prop('checked')) {
				$('label', label.parent()).removeClass(function(index, css) {
					return (css.match(/\bbtn-\S+/g) || []).join(' ');
				}).addClass('btn-default');
				label.addClass(function() {
					return getLabelClass(input.val());
				});
			}
		}

		var bootstrapRadios = $('fieldset.bootstrap-radio');

		bootstrapRadios.addClass('btn-group');
		bootstrapRadios.attr('data-toggle', 'buttons');
		bootstrapRadios.children('label').addClass('btn btn-default');
		bootstrapRadios.children('label').removeClass('radio');
		bootstrapRadios.children('label.active').addClass(function() {
			return getLabelClass($('input', this).val());
		});
		bootstrapRadios.children('label').click(function() {
			setRadioClass($(this));
		});

		bootstrapRadios.children('input').click(function() {
			alert('test');
		})

		
		
		// Fix Checkboxes
		var checkboxes = $('fieldset.checkboxes');
		checkboxes.find('li').each(function(){
			$($(this).html()).appendTo(checkboxes);
		});
		checkboxes.children('ul').remove();
		checkboxes.children('input').each(function(){
			if($(this).parent().hasClass('inline')) {
				$(this).next('label').addClass('inline');
			}
			$(this).next('label').addClass('checkbox');
			$(this).next('label').prepend($(this));
		});

		// HANDLE ALL BOOTSTRAP CHECKBOXES
		var setCheckboxClass = function(label) {
			var input = $('#' + label.attr('for'));
			label.toggleClass(function() {
				return getLabelClass(input.val());
			});
		}

		var bootstrapCBoxes = $('fieldset.bootstrap-checkbox');

		bootstrapCBoxes.addClass('btn-group');
		bootstrapCBoxes.attr('data-toggle', 'buttons');
		bootstrapCBoxes.children('label').addClass('btn btn-default');
		bootstrapCBoxes.children('label').removeClass('checkbox');
		bootstrapCBoxes.children('label.active').addClass(function() {
			return getLabelClass($('input', this).val());
		});
		bootstrapCBoxes.children('label').click(function() {
			setCheckboxClass($(this));
		});
	});

}(jQuery);