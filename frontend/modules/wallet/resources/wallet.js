/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/

// When the browser is ready...
jQuery(function() {
'use strict';

var wallettxndataTable;

if ( ! jQuery.fn.DataTable.isDataTable( '#wallet-history-grid' ) ) {
wallettxndataTable = jQuery('#wallet-history-grid').DataTable( {

"serverSide": true,

"bAutoWidth": false,

"columnDefs": [ {

	  "targets": 0,

	  "orderable": false,

	  "searchable": false

	   

	} ],

"processing": true,

"language": {

				"processing": "<div></div><div></div><div></div><div></div><div></div>",
				"emptyTable":     param.empty_table,
				"search":         param.dt_search+":",
				"lengthMenu":     param.dt_show + " _MENU_ " + param.dt_entries,
				"info":           param.dt_showing + " _START_ " + param.dt_to + " _END_ " + param.dt_of + " _TOTAL_ " + param.dt_entries,
				"infoEmpty":      param.dt_showing + " _START_ " + param.dt_to + " _END_ " + param.dt_of + " _TOTAL_ " + param.dt_entries,
				"paginate": {
					first:      param.dt_first,
					previous:   param.dt_previous,
					next:       param.dt_next,
					last:       param.dt_last,
				},

			},

"ajax":{

	url :ajaxurl, // json datasource

	type: "post",  // method  , by default get

	data: {"action": "get_wallet_history","user_id": user_id},

	error: function(){  // error handling

		jQuery(".wallet-history-grid-error").html("");

		jQuery("#wallet-history-grid").append('<tbody class="wallet-history-grid-error"><tr><th colspan="3">'+param.no_data+'</th></tr></tbody>');

		jQuery("#wallet-history-grid_processing").css("display","none");

		

	}

}

} );
}

jQuery('body').on('click', '.view-wallet-history', function(){
	  jQuery( "#sf-wallet-bx" ).slideToggle( "slow" );
	  jQuery( "#sf-wallet-history" ).slideToggle( "slow" );	
	  jQuery( "#sf-wallet-top-balance" ).slideToggle( "slow" );
});	

/*Back to wallet*/
jQuery('body').on('click', '.close-wallet-history', function(){
	  jQuery( "#sf-wallet-bx" ).slideToggle( "slow" );
	  jQuery( "#sf-wallet-history" ).slideToggle( "slow" );	
	  jQuery( "#sf-wallet-top-balance" ).slideToggle( "slow" );
});

//Make payment for wallet
jQuery('.wallet-payment-form')
.bootstrapValidator({
	message: param.not_valid,
	feedbackIcons: {
		valid: 'glyphicon glyphicon-ok',
		invalid: 'glyphicon glyphicon-remove',
		validating: 'glyphicon glyphicon-refresh'
	},
	fields: {
		payment_mode: {
			validators: {
				notEmpty: {
					message: param.select_payment
				}
			}
		},
		amount: {
			validators: {
				notEmpty: {
					message: param.req
				},
				numeric: {message: param.only_numeric},
				callback: {
					message: param.amount_range,
					callback: function(value, validator, $field) {
						if(parseFloat(value) > parseFloat(maxamount) || parseFloat(value) < parseFloat(minamount) ){
						return false;
						}else{
						return true;	
						}
					}
				}
			}
		},
	}
})
.on('error.field.bv', function(e, data) {
	data.bv.disableSubmitButtons(false); // disable submit buttons on errors
})
.on('status.field.bv', function(e, data) {
	data.bv.disableSubmitButtons(false); // disable submit buttons on valid
})
.on('change', 'input[name="payment_mode"]', function() {
	var paymode = jQuery(this).val();
	if(paymode == 'stripe'){
		jQuery('#walletcardinfo').show();
		jQuery('#walletwiredinfo').hide();
		jQuery('#twocheckout_walletcardinfo').hide();
									jQuery('.wallet-payment-form')
									.bootstrapValidator('addField', 'wallet_number', {
										validators: {
											notEmpty: {
													message: param.req
												},
											digits: {message: param.only_digits},
										}
									})
									.bootstrapValidator('addField', 'wallet_cvc', {
										validators: {
											notEmpty: {
													message: param.req
												},
											digits: {message: param.only_digits},
										}
									})
									.bootstrapValidator('addField', 'wallet_month', {
										validators: {
											notEmpty: {
													message: param.req
												},
											digits: {message: param.only_digits},
										}
									})
									.bootstrapValidator('addField', 'wallet_year', {
										validators: {
											notEmpty: {
													message: param.req
												},
											digits: {message: param.only_digits},
										}
									});
									
									jQuery('.wallet-payment-form')
									.bootstrapValidator('removeField', 'twocheckout_wallet_number')
									.bootstrapValidator('removeField', 'twocheckout_wallet_cvc')
									.bootstrapValidator('removeField', 'twocheckout_wallet_month')
									.bootstrapValidator('removeField', 'twocheckout_wallet_year');
	}else if(paymode == 'twocheckout'){
		jQuery('#walletcardinfo').hide();
		jQuery('#walletwiredinfo').hide();
		jQuery('#twocheckout_walletcardinfo').show();
									jQuery('.wallet-payment-form')
									.bootstrapValidator('addField', 'twocheckout_wallet_number', {
										validators: {
											notEmpty: {
													message: param.req
												},
											digits: {message: param.only_digits},
										}
									})
									.bootstrapValidator('addField', 'twocheckout_wallet_cvc', {
										validators: {
											notEmpty: {
													message: param.req
												},
											digits: {message: param.only_digits},
										}
									})
									.bootstrapValidator('addField', 'twocheckout_wallet_month', {
										validators: {
											notEmpty: {
													message: param.req
												},
											digits: {message: param.only_digits},
										}
									})
									.bootstrapValidator('addField', 'twocheckout_wallet_year', {
										validators: {
											notEmpty: {
													message: param.req
												},
											digits: {message: param.only_digits},
										}
									});
									
									jQuery('.wallet-payment-form')
									.bootstrapValidator('removeField', 'wallet_number')
									.bootstrapValidator('removeField', 'wallet_cvc')
									.bootstrapValidator('removeField', 'wallet_month')
									.bootstrapValidator('removeField', 'wallet_year');
									
	}else{
		if(paymode == 'wired'){
		jQuery('#walletwiredinfo').show();
		}else{
		jQuery('#walletwiredinfo').hide();
		}
		jQuery('#walletcardinfo').hide();
		jQuery('#twocheckout_walletcardinfo').hide();
									jQuery('.wallet-payment-form')
									.bootstrapValidator('removeField', 'wallet_number')
									.bootstrapValidator('removeField', 'wallet_cvc')
									.bootstrapValidator('removeField', 'wallet_month')
									.bootstrapValidator('removeField', 'wallet_year')
									.bootstrapValidator('removeField', 'twocheckout_wallet_number')
									.bootstrapValidator('removeField', 'twocheckout_wallet_cvc')
									.bootstrapValidator('removeField', 'twocheckout_wallet_month')
									.bootstrapValidator('removeField', 'twocheckout_wallet_year');
	}
})
.on('success.form.bv', function(form) {
		
		jQuery('form.wallet-payment-form').find('input[type="submit"]').prop('disabled', false);	
		var payment_mode = jQuery(this).find('input[name=payment_mode]:checked').val();
		var skipoption = jQuery(this).find('input[name=wallet_skipforadmin]:checked').val();
		
		if(payment_mode == undefined){
			payment_mode = '';
		}
		if(skipoption == undefined){
			skipoption = '';
		}
		
		if(woopayment && skipoption != "yes"){
			
				var data = {
				  "action": "sf_add_to_woo_cart",
				  "wootype": "wallet"
				};
					
				var formdata = jQuery('form.wallet-payment-form').serialize() + "&" + jQuery.param(data);
				
				jQuery.ajax({
					type        : 'POST',
					url         : ajaxurl,
					data        : formdata,
					beforeSend: function() {
						jQuery(".alert-success,.alert-danger").remove();
						jQuery('.loading-area').show();
					},
					dataType    : 'json',
					xhrFields   : { withCredentials: true },
					crossDomain : 'withCredentials' in new XMLHttpRequest(),
					success     : function (response) {
						jQuery('.loading-area').hide();	
						if (response['success']) {
							window.location.href = cart_url;
						} else {
							jQuery(".alert-success,.alert-danger").remove();
							jQuery( "<div class='alert alert-danger'>"+response.error+"</div>" ).insertBefore( "form.wallet-payment-form" );
							jQuery("html, body").animate({
									scrollTop: jQuery(".alert-danger").offset().top
								}, 1000);
						}
					}
				});  
				return false;	
			
		}else{
		
		if(payment_mode == 'wired' || payment_mode == 'paypal' || payment_mode == 'payumoney' || payment_mode == 'skippayment'){
			return true;
		}else if(payment_mode == 'stripe'){
			// Prevent form submission
			form.preventDefault();
			var crd_number = jQuery('#wallet_number').val();
			var crd_cvc = jQuery('#wallet_cvc').val();
			var crd_month = jQuery('#wallet_month').val();
			var crd_year = jQuery('#wallet_year').val();
			if(crd_number != "" && crd_cvc != "" && crd_month != "" && crd_year != ""){	
			jQuery('.loading-area').show();
			var data = {
			  "action": "get_adminstripekey",
			};
			var formdata = jQuery.param(data);
			jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: formdata,
					dataType: "json",
					success:function (data, textStatus) {
					if(data['secret_key'] != '' && data['public_key'] != ''){								
					Stripe.setPublishableKey(data['public_key']);
						 Stripe.card.createToken({
					  number: jQuery('#wallet_number').val(),
					  cvc: jQuery('#wallet_cvc').val(),
					  exp_month: jQuery('#wallet_month').val(),
					  exp_year: jQuery('#wallet_year').val(),
					}, stripe_wallet_pament);
					}else{
							jQuery('.loading-area').hide();
							jQuery(".alert-success,.alert-danger").remove();
							jQuery( "<div class='alert alert-danger'>"+param.set_key+"</div>" ).insertBefore( "form.wallet-payment-form" );
							return false;
					}
					}
			});		
				
				 
			}

		
		}
		}
});

//Stripe handler for payment to make wallet payment
function stripe_wallet_pament(status, response) {
  
if (response.error) {
  // Show the errors on the form
  jQuery('.loading-area').hide();
  jQuery(".alert-success,.alert-danger").remove();
   jQuery(".alert-bx").remove();
  jQuery( "<div class='alert alert-danger'>"+response.error.message+"</div>" ).insertBefore( "form.wallet-payment-form" );

} else {
// response contains id and card, which contains additional card details
var token = response.id;

var data = {
	  "action": "process_wallet_amount",
	  "stripeToken": token,
	};
	
var formdata = jQuery('form.wallet-payment-form').serialize() + "&" + jQuery.param(data);

jQuery.ajax({
		type: 'POST',
		url: ajaxurl,
		dataType: "json",
		beforeSend: function() {
			jQuery(".alert-success,.alert-danger").remove();
		},
		data: formdata,
		success:function (data, textStatus) {
			jQuery('.loading-area').hide();
			jQuery('form.wallet-payment-form').find('input[type="submit"]').prop('disabled', false);
			if(data['status'] == 'success'){
				jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.wallet-payment-form" );	
				window.setTimeout(function(){
					window.location.href = data['redirect_url'];
				}, 2000);
			}else{
				jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.wallet-payment-form" );
			}
		}
	});
}
}

});