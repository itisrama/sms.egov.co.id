jQuery.noConflict();
(function($) {
	$(window).load(function() {

		/* SET COSTS & RECEIPT
		 ---------------------- */
		var tax 	 		= $('*[name*="[tax]"]');
		var agreement 	 	= $('*[name*="[agreement_value]"]');
		var taxBase 	 	= $('*[name*="[tax_base]"]');
		var totalTax 		= $('*[name*="[total_tax]"]');
		var revenutAT 		= $('*[name*="[revenue_at]"]');
		var totalExpense	= $('*[name*="[total_expense]"]');
		var grossProfit		= $('*[name*="[gross_profit]"]');

		
		var agreementVal, isTaxed, taxBaseVal, totalTaxVal, revenutATVal, totalExpenseVal, grossProfitVal;
		
		countCosts();
		
		agreement.keyup(function() {
			countCosts();
		});
		agreement.change(function() {
			countCosts();
		});

		tax.change(function() {
			countCosts();
		});

		$("[id$='receipts_table']").on('click', '.delItem', function(e) {
			countCosts();
		});

		$("[id$='receipts_form']").on('click', '.addItem, .saveItem', function(e) {
			countCosts();
		});

		function countCosts() {
			agreementVal 	= agreement.asNumber({region: 'custom'});
			// Is Taxed?
			isTaxed 		= tax.filter(':checked').val() == 0;

			// Count Taxbase
			taxBaseVal = isTaxed ? agreementVal * (100/110) : 0;
			taxBase.val(taxBaseVal || 0).formatCurrency({ region: 'custom' });

			// Count Total Tax
			taxBaseVal	= taxBase.asNumber({region: 'custom'});
			totalTaxVal = 0;
			$("[id$='receipts_table'] .type input[value^=tax]").each(function() {
				var taxVal		= $('.value input', $(this).closest('tr'));
				var taxValType	= $('.value_type input', $(this).closest('tr')).val();
				totalTaxVal		+= taxValType.indexOf('percentage') > -1 ? taxBaseVal * (taxVal.val()/100) : taxVal.asNumber({region: 'custom'});
			});
			totalTaxVal = isTaxed ? totalTaxVal : 0;
			totalTax.val(totalTaxVal || 0).formatCurrency({ region: 'custom' });
			
			// Count Revenue AT
			revenutATVal = agreementVal - totalTaxVal;
			revenutAT.val(revenutATVal || 0).formatCurrency({ region: 'custom' });

			// Count Total Expense
			totalExpenseVal = 0;
			$("[id$='receipts_table'] .type input[value^=expense]").each(function() {
				var expenseVal		= $('.value input', $(this).closest('tr'));
				var expenseValType	= $('.value_type input', $(this).closest('tr')).val();
				
				totalExpenseVal		+= expenseValType.indexOf('percentage') > -1 ? agreementVal * (expenseVal.val()/100) : expenseVal.asNumber({region: 'custom'});
			});
			totalExpense.val(totalExpenseVal || 0).formatCurrency({ region: 'custom' });

			// Count Gross Profit
			grossProfitVal = revenutATVal - totalExpenseVal;
			grossProfit.val(grossProfitVal || 0).formatCurrency({ region: 'custom' });
		}

		/* FORM VALUE TYPE HANDLER
		 ---------------------- */
		function checkValueType(el) {
			var target = el.closest('.form-horizontal').find('.inputField *[name*="value]"]');
			var fieldset = el.closest('fieldset');
			if (el.val() == 'currency') {
				target.formatCurrency({ region: 'custom' });
				target.removeClass('numeric');
				target.addClass('currency');
				target.next('.input-percentage').remove();
				target.after('<input type="text" class="input-mini input-percentage" style="margin-left:10px" />');
				target.parent().on('keyup', '.input-percentage', function(e) {
					$(this).formatNumber(e);
					var form = $(this).closest(".form-horizontal");

					var tipe = form.find('*[name*="[type]"]').filter(':checked').val();
					var inputTarget = $(this).prev('input');
					var inputVal = $(this).val();
					$(this).formatCurrency({
						region: 'numeric'
					});
					if (tipe == 'tax') {
						inputTarget.val(isTaxed ? taxBaseVal * (inputVal / 100) : 0).formatCurrency({ region: 'custom' });
					} else {
						currencyVal = fieldset.hasClass('dist_contracts') ? agreementVal : grossProfitVal;
						inputTarget.val(currencyVal * (inputVal / 100)).formatCurrency({ region: 'custom' });
					}
				});
				target.parent().on('keydown', '.input-percentage', function(e) {
					$(this).preventKey(e);
				});
			} else {
				target.addClass('numeric');
				target.removeClass('currency')
				target.toNumber({ region: 'custom' });
				target.formatCurrency({ region: 'numeric' });
				target.next('.input-percentage').remove();
			}
		}

		checkValueType($('.inputField *[name*="value_type]"]'));
		$('.inputField *[name*="value_type]"]').change(function() {
			checkValueType($(this));
		});


		/* Nomor Document */
		function toggleGenerateButton() {
			if($('#jform_name').val() != '' && $('#jform_kickoff_date').val() != '' && $('#jform_no_document').val() == '') {
				$('#generate_no').removeAttr('disabled');
			} else {
				$('#generate_no').attr('disabled', true);
			}
		}

		function generateNoDocument(brand, name, date) {
			var postdata = {
				option: "com_gtdocumentnum",
				task: "item.generateNum",
				id: "0",
				disposition: encodeURIComponent(brand),
				title: encodeURIComponent("Job Order " + name),
				division: "BDCE",
				doc_type: "JO",
				doc_date: date,
				doc_num_type: "auto"
			};
			$.post( "index.php", postdata).done(function( data ) {
				$('#jform_no_document').val(data);
				$('#jform_no_document').removeAttr('disabled');
				$('#generate_no i').removeClass('fa-spin');
				toggleGenerateButton();			
			});
		}
		
		$('#jform_no_document').after('<button id="generate_no" class="btn btn-primary"><i class="fa fa-refresh"></i></button>')
		
		toggleGenerateButton();
		$('#jform_name, #jform_no_document, #jform_kickoff_date').blur(function(){
			toggleGenerateButton();
		});

		$('#generate_no').on('click', function(){
			var brand = $('#jform_brand_id option:selected').html();
			var name = $('#jform_name').val();
			var date = $('#jform_kickoff_date').val().split('-').reverse().join('-');
			$('i', $(this)).addClass('fa-spin');
			$('#jform_no_document').attr('disabled', 'true');
			generateNoDocument(brand, name, date);
		});
	});
})(jQuery);