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
	var flag = 1;
	var package_flag = 1;
	var month_flag = 1;
	var year_flag = 1;
	
	/*Provider Signup*/
	jQuery('.claimbusiness_payment')
        .bootstrapValidator({
            message: param.not_valid,
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {}
        })
		.on('error.field.bv', function(e, data) {
            data.bv.disableSubmitButtons(false); // disable submit buttons on errors
	    })
		.on('status.field.bv', function(e, data) {
            data.bv.disableSubmitButtons(false); // disable submit buttons on valid
        })
		.on('change', 'select[name="provider-role"]', function() {
		if(!woopayment){
		var free = jQuery('.claimbusiness_payment select[name="provider-role"] option:selected').attr('class');
		var paymode = jQuery('.claimbusiness_payment input[name="claim_payment_mode"]:checked').val();
			if(free == 'free' || free == 'blank'){
				jQuery('#paymethod_bx').hide();	
				jQuery('#stripeinfo').hide();
				jQuery('#twocheckoutstripeinfo').hide();
				jQuery('#payulatampageinfo').hide();
				jQuery('#signuppagewiredinfo').hide();
				jQuery('#freemode_bx').val('yes');
				
											jQuery('.claimbusiness_payment')
											.bootstrapValidator('removeField', 'claim_payment_mode');
											
				
			}else{
				jQuery('#paymethod_bx').show();
				if(paymode == 'stripe'){
					jQuery('#stripeinfo').show();
					jQuery('#signuppagewiredinfo').hide();
					jQuery('#twocheckoutstripeinfo').hide();
					jQuery('#payulatampageinfo').hide();
				}else if(paymode == 'twocheckout'){
					jQuery('#stripeinfo').hide();
					jQuery('#signuppagewiredinfo').hide();
					jQuery('#twocheckoutstripeinfo').show();
					jQuery('#payulatampageinfo').hide();
				}else if(paymode == 'payulatam'){
					jQuery('#stripeinfo').hide();
					jQuery('#signuppagewiredinfo').hide();
					jQuery('#twocheckoutstripeinfo').hide();
					jQuery('#payulatampageinfo').show();
				}else if(paymode == 'wired'){
					jQuery('#stripeinfo').hide();
					jQuery('#signuppagewiredinfo').hide();
					jQuery('#twocheckoutstripeinfo').show();		
					jQuery('#payulatampageinfo').hide();
				}
				jQuery('#freemode_bx').val('no');
											jQuery('.claimbusiness_payment')
											.bootstrapValidator('addField', 'claim_payment_mode', {
												validators: {
													notEmpty: {
												message: param.req
											},
												}
											});
			}
		}
		
		})
		.on('change', 'input[name="claim_payment_mode"]', function() {
															 
			var paymode = jQuery('.claimbusiness_payment input[name="claim_payment_mode"]:checked').val();
			if(paymode == 'stripe'){
				jQuery('#stripeinfo').show();
				jQuery('#signuppagewiredinfo').hide();
				jQuery('#payulatampageinfo').hide();
				jQuery('#twocheckoutstripeinfo').hide();
											jQuery('.claimbusiness_payment')
											.bootstrapValidator('addField', 'scd_number', {
												validators: {
													notEmpty: {
												message: param.req
											},
													digits: {message: param.only_digits},
												}
											})
											.bootstrapValidator('addField', 'scd_cvc', {
												validators: {
													notEmpty: {
												message: param.req
											},
													digits: {message: param.only_digits},
												}
											})
											.bootstrapValidator('addField', 'scd_month', {
												validators: {
													notEmpty: {
												message: param.req
											},
													digits: {message: param.only_digits},
												}
											})
											.bootstrapValidator('addField', 'scd_year', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											});
											
											jQuery('.claimbusiness_payment')
											.bootstrapValidator('removeField', 'twocheckout_scd_number')
											.bootstrapValidator('removeField', 'twocheckout_scd_cvc')
											.bootstrapValidator('removeField', 'twocheckout_scd_month')
											.bootstrapValidator('removeField', 'twocheckout_scd_year')
											.bootstrapValidator('removeField', 'payulatam_page_cardtype')
											.bootstrapValidator('removeField', 'payulatam_scd_number')
											.bootstrapValidator('removeField', 'payulatam_scd_cvc')
											.bootstrapValidator('removeField', 'payulatam_scd_month')
											.bootstrapValidator('removeField', 'payulatam_scd_year');
			}else if(paymode == 'twocheckout'){
				jQuery('#twocheckoutstripeinfo').show();
				jQuery('#stripeinfo').hide();
				jQuery('#payulatampageinfo').hide();
				jQuery('#signuppagewiredinfo').hide();
											jQuery('.claimbusiness_payment')
											.bootstrapValidator('addField', 'twocheckout_scd_number', {
												validators: {
													notEmpty: {
												message: param.req
											},
													digits: {message: param.only_digits},
												}
											})
											.bootstrapValidator('addField', 'twocheckout_scd_cvc', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											})
											.bootstrapValidator('addField', 'twocheckout_scd_month', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											})
											.bootstrapValidator('addField', 'twocheckout_scd_year', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											});
											jQuery('.claimbusiness_payment')
											.bootstrapValidator('removeField', 'scd_number')
											.bootstrapValidator('removeField', 'scd_cvc')
											.bootstrapValidator('removeField', 'scd_month')
											.bootstrapValidator('removeField', 'scd_year')
											.bootstrapValidator('removeField', 'payulatam_page_cardtype')
											.bootstrapValidator('removeField', 'payulatam_scd_number')
											.bootstrapValidator('removeField', 'payulatam_scd_cvc')
											.bootstrapValidator('removeField', 'payulatam_scd_month')
											.bootstrapValidator('removeField', 'payulatam_scd_year');
			
			}else if(paymode == 'payulatam'){
				jQuery('#twocheckoutstripeinfo').hide();
				jQuery('#stripeinfo').hide();
				jQuery('#payulatampageinfo').show();
				jQuery('#signuppagewiredinfo').hide();
											jQuery('.claimbusiness_payment')
											.bootstrapValidator('addField', 'payulatam_page_cardtype', {
												validators: {
													notEmpty: {
												message: param.req
											},
													digits: {message: param.only_digits},
												}
											})
											.bootstrapValidator('addField', 'payulatam_scd_number', {
												validators: {
													notEmpty: {
												message: param.req
											},
													digits: {message: param.only_digits},
												}
											})
											.bootstrapValidator('addField', 'payulatam_scd_cvc', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											})
											.bootstrapValidator('addField', 'payulatam_scd_month', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											})
											.bootstrapValidator('addField', 'payulatam_scd_year', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											});
											jQuery('.claimbusiness_payment')
											.bootstrapValidator('removeField', 'scd_number')
											.bootstrapValidator('removeField', 'scd_cvc')
											.bootstrapValidator('removeField', 'scd_month')
											.bootstrapValidator('removeField', 'scd_year')
											.bootstrapValidator('removeField', 'twocheckout_scd_number')
											.bootstrapValidator('removeField', 'twocheckout_scd_cvc')
											.bootstrapValidator('removeField', 'twocheckout_scd_month')
											.bootstrapValidator('removeField', 'twocheckout_scd_year');
			
			}else if(paymode == 'wired'){
				jQuery('#stripeinfo').hide();
				jQuery('#twocheckoutstripeinfo').hide();
				jQuery('#payulatampageinfo').hide();
				jQuery('#signuppagewiredinfo').show();
											jQuery('.claimbusiness_payment')
											.bootstrapValidator('removeField', 'scd_number')
											.bootstrapValidator('removeField', 'scd_cvc')
											.bootstrapValidator('removeField', 'scd_month')
											.bootstrapValidator('removeField', 'scd_year')
											.bootstrapValidator('removeField', 'twocheckout_scd_number')
											.bootstrapValidator('removeField', 'twocheckout_scd_cvc')
											.bootstrapValidator('removeField', 'twocheckout_scd_month')
											.bootstrapValidator('removeField', 'twocheckout_scd_year')
											.bootstrapValidator('removeField', 'payulatam_page_cardtype')
											.bootstrapValidator('removeField', 'payulatam_scd_number')
											.bootstrapValidator('removeField', 'payulatam_scd_cvc')
											.bootstrapValidator('removeField', 'payulatam_scd_month')
											.bootstrapValidator('removeField', 'payulatam_scd_year');
			}else{
				jQuery('#stripeinfo').hide();
				jQuery('#twocheckoutstripeinfo').hide();
				jQuery('#payulatampageinfo').hide();
				jQuery('#signuppagewiredinfo').hide();
											jQuery('.claimbusiness_payment')
											.bootstrapValidator('removeField', 'scd_number')
											.bootstrapValidator('removeField', 'scd_cvc')
											.bootstrapValidator('removeField', 'scd_month')
											.bootstrapValidator('removeField', 'scd_year')
											.bootstrapValidator('removeField', 'twocheckout_scd_number')
											.bootstrapValidator('removeField', 'twocheckout_scd_cvc')
											.bootstrapValidator('removeField', 'twocheckout_scd_month')
											.bootstrapValidator('removeField', 'twocheckout_scd_year')
											.bootstrapValidator('removeField', 'payulatam_page_cardtype')
											.bootstrapValidator('removeField', 'payulatam_scd_number')
											.bootstrapValidator('removeField', 'payulatam_scd_cvc')
											.bootstrapValidator('removeField', 'payulatam_scd_month')
											.bootstrapValidator('removeField', 'payulatam_scd_year');
			}
		})
        .on('success.form.bv', function(form) {
				
				jQuery('form.claimbusiness_payment').find('input[type="submit"]').prop('disabled', false);
				//if(package_flag==1){form.preventDefault();return false;}
				
				var payment_mode = jQuery('.claimbusiness_payment input[name=claim_payment_mode]:checked').val();
				var freemode = jQuery('#freemode_bx').val();
				
				var freechk = jQuery('.claimbusiness_payment select[name="provider-role"] option:selected').attr('class');
				
				if(woopayment && freechk != 'free' && freechk != 'blank'){
						var data = {
						  "action": "sf_add_to_woo_cart",
						  "wootype": "claimbusiness"
						};
							
						var formdata = jQuery('form.claimbusiness_payment').serialize() + "&" + jQuery.param(data);
						
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
									jQuery( "<div class='alert alert-danger'>"+response.error+"</div>" ).insertBefore( "form.claimbusiness_payment" );
									jQuery("html, body").animate({
											scrollTop: jQuery(".alert-danger").offset().top
										}, 1000);
								}
							}
						});  
						return false;						  
					  	
				}else{
				
				if(payment_mode == 'paypal' || freemode == 'yes' || payment_mode == 'wired'){
					return true;
				}else if(payment_mode == 'stripe'){
				
					// Prevent form submission
					form.preventDefault();
					
					var cd_number = jQuery('input[name="scd_number"]').val();
					var cd_cvc = jQuery('input[name="scd_cvc"]').val();
					var cd_month = jQuery('input[name="scd_month"]').val();
					var cd_year = jQuery('input[name="scd_year"]').val();
					//if(month_flag==1 || year_flag==1){return false;}
					
					if(cd_number != "" && cd_cvc != "" && cd_month != "" && cd_year != ""){	
					jQuery('.loading-area').show();
						
					var data = {
					  "action": "get_adminstripekey",
					};
					var formdata = jQuery.param(data);
					jQuery.ajax({
							type: 'POST',
							url: ajaxurl,
							dataType: "json",
							data: formdata,
							success:function (data, textStatus) {
							service_finder_clearconsole();	
							if(data['secret_key'] != '' && data['public_key'] != ''){
								Stripe.setPublishableKey(data['public_key']);
									 Stripe.card.createToken({
								  number: jQuery('#scd_number').val(),
								  cvc: jQuery('#scd_cvc').val(),
								  exp_month: jQuery('#scd_month').val(),
								  exp_year: jQuery('#scd_year').val()
								}, service_finder_stripeRegPageHandler);
							}else{
									jQuery('.loading-area').hide();
								    jQuery(".alert-success,.alert-danger").remove();
									jQuery( "<div class='alert alert-danger'>"+param.set_key+"</div>" ).insertBefore( "form.claimbusiness_payment" );
									return false;
							}	 
							}
					});
						
						 
					}
		
				
				}else if(payment_mode == 'payulatam'){
					// Prevent form submission
					form.preventDefault();
					var crd_type = jQuery('#payulatam_page_cardtype').val();
					var crd_number = jQuery('#payulatam_scd_number').val();
					var crd_cvc = jQuery('#payulatam_scd_cvc').val();
					var crd_month = jQuery('#payulatam_scd_month').val();
					var crd_year = jQuery('#payulatam_scd_year').val();
					if(crd_type != "" && crd_number != "" && crd_cvc != "" && crd_month != "" && crd_year != ""){	
					jQuery('.loading-area').show();
					
					var data = {
						  "action": "payulatam_signup",
						};
						
					var formdata = jQuery('form.claimbusiness_payment').serialize() + "&" + jQuery.param(data);
					
					jQuery.ajax({
							type: 'POST',
							url: ajaxurl,
							data: formdata,
							beforeSend: function() {
								jQuery(".alert-success,.alert-danger").remove();
							},
							dataType: "json",
							success:function (data, textStatus) {
								jQuery('.loading-area').hide();
								jQuery('form.claimbusiness_payment').find('input[type="submit"]').prop('disabled', false);
								if(data['status'] == 'success'){
								jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.claimbusiness_payment" );	
								jQuery("html, body").animate({
										scrollTop: jQuery(".alert").offset().top
									}, 1000);
								if(data['redirecturl'] != ''){
									window.location = data['redirecturl'];
								}
								}else{
								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.claimbusiness_payment" );
								jQuery("html, body").animate({
										scrollTop: jQuery(".alert").offset().top
									}, 1000);
								}
							
							}
					});		
						
						 
					}
		
				
					
				
				}else if(payment_mode == 'twocheckout'){
					// Prevent form submission
					form.preventDefault();
					
					if(twocheckoutpublishkey != ""){
						
						try {
							TCO.loadPubKey(twocheckoutmode);
							jQuery('.loading-area').show();
							tokenRequest();
						} catch(e) {
							jQuery('.loading-area').hide();
							jQuery('.alert').remove();
							jQuery( "<div class='alert alert-danger'>"+e.toSource()+"</div>" ).insertBefore( "form.claimbusiness_payment" );
							jQuery("html, body").animate({
									scrollTop: jQuery(".alert-danger").offset().top
								}, 1000);
							return false;
	
						}
					
					}else{
						jQuery('.loading-area').hide();
						jQuery( "<div class='alert alert-danger'>You did not set a valid publishable key.</div>" ).insertBefore( "form.claimbusiness_payment" );
						jQuery("html, body").animate({
								scrollTop: jQuery(".alert-danger").offset().top
							}, 1000);
						return false;
					}	
					
				}
				
				}
		});
		
		//Stripe handler for singup
		function service_finder_stripeRegPageHandler(status, response) {
  
			  if (response.error) {
				  // Show the errors on the form
				  jQuery('.loading-area').hide();
				  jQuery(".alert-success,.alert-danger").remove();
				  jQuery('form.claimbusiness_payment').find('input[type="submit"]').prop('disabled', false);
				  jQuery( "<div class='alert alert-danger'>"+response.error.message+"</div>" ).insertBefore( "form.claimbusiness_payment" );
				
			  } else {
				// response contains id and card, which contains additional card details
				var token = response.id;
				
				var data = {
					  "action": "claimed",
					  "stripeToken": token,
					};
					
				var formdata = jQuery('form.claimbusiness_payment').serialize() + "&" + jQuery.param(data);
				
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
								jQuery('form.claimbusiness_payment').find('input[type="submit"]').prop('disabled', false);
								if(data['status'] == 'success'){
								jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.claimbusiness_payment" );	
								jQuery("html, body").animate({
										scrollTop: jQuery(".alert").offset().top
									}, 1000);
								if(data['redirecturl'] != ''){
									window.location = data['redirecturl'];
								}
								}else{
								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.claimbusiness_payment" );
								jQuery("html, body").animate({
										scrollTop: jQuery(".alert").offset().top
									}, 1000);
								}
							
							}
	
						});
				
				}
		}
		
  });
  
var tokenRequest = function() {

var twocheckout_card_number = jQuery('input[name="twocheckout_scd_number"]').val();
var twocheckout_card_cvc = jQuery('input[name="twocheckout_scd_cvc"]').val();
var twocheckout_card_month = jQuery('select[name="twocheckout_scd_month"]').val();
var twocheckout_card_year = jQuery('select[name="twocheckout_scd_year"]').val();

// Setup token request arguments
var args = {
  sellerId: twocheckoutaccountid,
  publishableKey: twocheckoutpublishkey,
  ccNo: twocheckout_card_number,
  cvv: twocheckout_card_cvc,
  expMonth: twocheckout_card_month,
  expYear: twocheckout_card_year
};

// Make the token request
TCO.requestToken(successCallback, errorCallback, args);
};

// Called when token created successfully.
var successCallback = function(data) {
	// Set the token as the value for the token input
	var token = data.response.token.token;
		
		/*To Add Service cost also in minimum cost*/
		
		var data = {
				  "action": "twocheckout_signup",
				  "twocheckouttoken": token,
				};
				
		var formdata = jQuery('form.claimbusiness_payment').serialize() + "&" + jQuery.param(data);
		
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
					jQuery('form.claimbusiness_payment').find('input[type="submit"]').prop('disabled', false);
					if(data['status'] == 'success'){
						jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.claimbusiness_payment" );	
					}else{
						jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.claimbusiness_payment" );
					}
					
				}

			});

  };

// Called when token creation fails.
var errorCallback = function(data) {
	if (data.errorCode === 200) {
	  // This error code indicates that the ajax call failed. We recommend that you retry the token request.
	} else {
	  jQuery('.loading-area').hide();
	  jQuery('.alert').remove();
	  jQuery('form.claimbusiness_payment').find('input[type="submit"]').prop('disabled', false);
	  jQuery( "<div class='alert alert-danger'>"+data.errorMsg+"</div>" ).insertBefore( "form.claimbusiness_payment" );
	  jQuery("html, body").animate({
							scrollTop: jQuery(".alert-danger").offset().top
						}, 1000);
	}
  };  