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
	
	var month_flag = 1;
	var year_flag = 1;
	
	// > Countdown for time function by = countdown.min.js =================== //		

		  var _gaq = _gaq || [];

		  _gaq.push(['_setAccount', 'UA-36251023-1']);

		  _gaq.push(['_setDomainName', 'jqueryscript.net']);

		  _gaq.push(['_trackPageview']);


	
	jQuery('body').on('click', '.hideinfomsg', function(){
		
		var providerid = jQuery(this).attr("data-id");
		var data = {
					  "action": "delete_decline_request",
					  "providerid": providerid
				};
		var formdata = jQuery.param(data);
		jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: formdata,
				success:function (data, textStatus) {
					jQuery("#feature-req-bx").find(".alert-bx").remove();		
				}
		});
	});
	
	jQuery('body').on('click', '.cancelmembership', function(){
		var providerid = jQuery(this).data("providerid");
		
		bootbox.confirm(param.are_you_sure_cancel_membership, function(result) {
		if(result){
			var data = {
						  "action": "cancel_membership",
						  "providerid": providerid
					};
			var formdata = jQuery.param(data);
			
			jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: formdata,
					dataType : 'json',
					beforeSend: function() {
						jQuery(".alert-success,.alert-danger").remove();
						jQuery('.loading-area').show();
					},
					success:function (data, textStatus) {
						jQuery('.loading-area').hide();	
						jQuery( ".sf-cancel-membership" ).remove();
						jQuery( "<div class='alert alert-warning'>"+data['display_message']+"</div>" ).insertBefore( "form.upgrade-form" );
						jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.upgrade-form" );
						if(data['redirect'] != ""){
						window.location.href= data['redirect'];
						}
					}
			});			
		}
		}); 
		
	});
	
	/*Upgrade provider account*/
	jQuery('.upgrade-form')
        .bootstrapValidator({
            message: param.not_valid,
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
				pay_mode: {
					validators: {
						notEmpty: {
							message: param.select_payment	
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
		.on('click',  'input[type="submit"]', function(e) {
            
			if(jQuery('.upgrade-form select[name="crd_month"] option:selected').val()==""){month_flag = 1;jQuery('.upgrade-form select[name="crd_month"]').parent('div').addClass('has-error').removeClass('has-success'); jQuery('form.upgrade-form').find('input[type="submit"]').prop('disabled', false);}else{month_flag = 0;jQuery('.upgrade-form select[name="crd_month"]').parent('div').removeClass('has-error').addClass('has-success'); jQuery('form.upgrade-form').find('input[type="submit"]').prop('disabled', false);}
			
			if(jQuery('.upgrade-form select[name="crd_year"] option:selected').val()==""){year_flag = 1;jQuery('.upgrade-form select[name="crd_year"]').parent('div').addClass('has-error').removeClass('has-success'); jQuery('form.upgrade-form').find('input[type="submit"]').prop('disabled', false);}else{year_flag = 0;jQuery('.upgrade-form select[name="crd_year"]').parent('div').removeClass('has-error').addClass('has-success'); jQuery('form.upgrade-form').find('input[type="submit"]').prop('disabled', false);}
			
	    })
		.on('click',  'ul#sf-upgrade-plans li', function(e) {
			
			jQuery('.renewpackage i').removeClass('fa-check');
			
			jQuery('.renewpackage').removeClass('btn-renew-active');
			
			var packageid = jQuery(this).data('packageid');
			
			jQuery('#provider-role').val(packageid);
			
			jQuery('ul#sf-upgrade-plans li').removeClass('selected');
			jQuery(this).addClass('selected');																				   
			
			if(!woopayment){
			if(jQuery(this).hasClass("selected-plan") || jQuery(this).hasClass("free")){
				jQuery('#payment_method').hide();
				jQuery('#stripeinfo').hide();
				jQuery('#upgradewiredinfo').hide();
				jQuery('#twocheckoutinfo').hide();
				jQuery('#payulataminfo').hide();
				if(jQuery(this).hasClass("free")){
				jQuery('#upgradefreemode').val('yes');
				}else{
				jQuery('#upgradefreemode').val('no');	
				}
				
				jQuery('.upgrade-form')
				.bootstrapValidator('removeField', 'cd_number')
				.bootstrapValidator('removeField', 'cd_cvc')
				.bootstrapValidator('removeField', 'cd_month')
				.bootstrapValidator('removeField', 'cd_year')
				.bootstrapValidator('removeField', 'twocheckout_crd_number')
				.bootstrapValidator('removeField', 'twocheckout_crd_cvc')
				.bootstrapValidator('removeField', 'twocheckout_crd_month')
				.bootstrapValidator('removeField', 'twocheckout_crd_year')
				.bootstrapValidator('removeField', 'payulatam_upgrade_cardtype')
				.bootstrapValidator('removeField', 'payulatam_crd_number')
				.bootstrapValidator('removeField', 'payulatam_crd_cvc')
				.bootstrapValidator('removeField', 'payulatam_crd_month')
				.bootstrapValidator('removeField', 'payulatam_crd_year');
				
				
				jQuery('input[name="pay_mode"]').prop('checked', false);
					if(jQuery(this).hasClass("free") && !jQuery(this).hasClass("current")){
					jQuery('#proupgrade').show();
					}else{
					jQuery('#proupgrade').hide();	
					}
			}else{
				
				jQuery('#payment_method').show();
				jQuery('#proupgrade').show();
				jQuery('#upgradefreemode').val('no');
			}
			}else{
				if(jQuery(this).hasClass("selected-plan")){
				jQuery('#payment_method').hide();
				jQuery('#proupgrade').hide();
				jQuery('#upgradefreemode').val('no');
				jQuery('#skipoption').hide();
				}else{
				jQuery('#payment_method').show();
				jQuery('#proupgrade').show();
				if(jQuery(this).hasClass("free")){
					jQuery('#upgradefreemode').val('yes');
					jQuery('#skipoption').hide();
				}else{
					jQuery('#upgradefreemode').val('no');	
					jQuery('#skipoption').show();
				}
				}
			}
			
													   
		})
		.on('click',  '.renewpackage', function(e) {
			
			jQuery(this).find('i').addClass('fa-check');
			jQuery(this).addClass('btn-renew-active');
			var packageid = jQuery(this).data('packageid');
			
			jQuery('#provider-role').val(packageid);
			
			jQuery('ul#sf-upgrade-plans li').removeClass('selected');
			
			if(!woopayment){
			if(jQuery(this).hasClass("free")){
				jQuery('#payment_method').hide();
				jQuery('#stripeinfo').hide();
				jQuery('#upgradewiredinfo').hide();
				jQuery('#twocheckoutinfo').hide();
				jQuery('#payulataminfo').hide();
				if(jQuery(this).hasClass("free")){
				jQuery('#upgradefreemode').val('yes');
				}else{
				jQuery('#upgradefreemode').val('no');	
				}
				
				jQuery('.upgrade-form')
				.bootstrapValidator('removeField', 'cd_number')
				.bootstrapValidator('removeField', 'cd_cvc')
				.bootstrapValidator('removeField', 'cd_month')
				.bootstrapValidator('removeField', 'cd_year')
				.bootstrapValidator('removeField', 'twocheckout_crd_number')
				.bootstrapValidator('removeField', 'twocheckout_crd_cvc')
				.bootstrapValidator('removeField', 'twocheckout_crd_month')
				.bootstrapValidator('removeField', 'twocheckout_crd_year')
				.bootstrapValidator('removeField', 'payulatam_upgrade_cardtype')
				.bootstrapValidator('removeField', 'payulatam_crd_number')
				.bootstrapValidator('removeField', 'payulatam_crd_cvc')
				.bootstrapValidator('removeField', 'payulatam_crd_month')
				.bootstrapValidator('removeField', 'payulatam_crd_year');
				
				
				jQuery('input[name="pay_mode"]').prop('checked', false);
					if(jQuery(this).hasClass("free") && !jQuery(this).hasClass("current")){
					jQuery('#proupgrade').show();
					}else{
					jQuery('#proupgrade').hide();	
					}
			}else{
				
				jQuery('#payment_method').show();
				jQuery('#proupgrade').show();
				jQuery('#upgradefreemode').val('no');
			}
			}else{
				jQuery('#payment_method').show();
				jQuery('#proupgrade').show();
				if(jQuery(this).hasClass("free")){
					jQuery('#upgradefreemode').val('yes');
					jQuery('#skipoption').hide();
				}else{
					jQuery('#upgradefreemode').val('no');	
					jQuery('#skipoption').show();
				}
			}
			
													   
		})
		.on('change', 'input[name="pay_mode"]', function() {
			var paymode = jQuery(this).val();
			if(paymode == 'stripe_upgrade'){
				jQuery('#stripeinfo').show();
				jQuery('#upgradewiredinfo').hide();
				jQuery('#twocheckoutinfo').hide();
				jQuery('#payulataminfo').hide();
											jQuery('.upgrade-form')
											.bootstrapValidator('addField', 'crd_number', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											})
											.bootstrapValidator('addField', 'crd_cvc', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											})
											.bootstrapValidator('addField', 'crd_month', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											})
											.bootstrapValidator('addField', 'crd_year', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											});
											
											jQuery('.upgrade-form')
											.bootstrapValidator('removeField', 'twocheckout_crd_number')
											.bootstrapValidator('removeField', 'twocheckout_crd_cvc')
											.bootstrapValidator('removeField', 'twocheckout_crd_month')
											.bootstrapValidator('removeField', 'twocheckout_crd_year')
											.bootstrapValidator('removeField', 'payulatam_upgrade_cardtype')
											.bootstrapValidator('removeField', 'payulatam_crd_number')
											.bootstrapValidator('removeField', 'payulatam_crd_cvc')
											.bootstrapValidator('removeField', 'payulatam_crd_month')
											.bootstrapValidator('removeField', 'payulatam_crd_year');
											
			}else if(paymode == 'twocheckout_upgrade'){
					jQuery('#twocheckoutinfo').show();
					jQuery('#upgradewiredinfo').hide();
					jQuery('#stripeinfo').hide();
					jQuery('#payulataminfo').hide();
											jQuery('.upgrade-form')
											.bootstrapValidator('addField', 'twocheckout_crd_number', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											})
											.bootstrapValidator('addField', 'twocheckout_crd_cvc', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											})
											.bootstrapValidator('addField', 'twocheckout_crd_month', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											})
											.bootstrapValidator('addField', 'twocheckout_crd_year', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											});
											
											jQuery('.upgrade-form')
											.bootstrapValidator('removeField', 'crd_number')
											.bootstrapValidator('removeField', 'crd_cvc')
											.bootstrapValidator('removeField', 'crd_month')
											.bootstrapValidator('removeField', 'crd_year')
											.bootstrapValidator('removeField', 'payulatam_upgrade_cardtype')
											.bootstrapValidator('removeField', 'payulatam_crd_number')
											.bootstrapValidator('removeField', 'payulatam_crd_cvc')
											.bootstrapValidator('removeField', 'payulatam_crd_month')
											.bootstrapValidator('removeField', 'payulatam_crd_year');
				
			}else if(paymode == 'payulatam_upgrade'){
					jQuery('#twocheckoutinfo').hide();
					jQuery('#stripeinfo').hide();
					jQuery('#upgradewiredinfo').hide();
					jQuery('#payulataminfo').show();
											jQuery('.upgrade-form')
											.bootstrapValidator('addField', 'payulatam_upgrade_cardtype', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											})
											.bootstrapValidator('addField', 'payulatam_crd_number', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											})
											.bootstrapValidator('addField', 'payulatam_crd_cvc', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											})
											.bootstrapValidator('addField', 'payulatam_crd_month', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											})
											.bootstrapValidator('addField', 'payulatam_crd_year', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											});
											
											jQuery('.upgrade-form')
											.bootstrapValidator('removeField', 'crd_number')
											.bootstrapValidator('removeField', 'crd_cvc')
											.bootstrapValidator('removeField', 'crd_month')
											.bootstrapValidator('removeField', 'crd_year')
											.bootstrapValidator('removeField', 'twocheckout_crd_number')
											.bootstrapValidator('removeField', 'twocheckout_crd_cvc')
											.bootstrapValidator('removeField', 'twocheckout_crd_month')
											.bootstrapValidator('removeField', 'twocheckout_crd_year');
				
			}else{
				if(paymode == 'wired_upgrade'){
				jQuery('#upgradewiredinfo').show();					
				}else{
				jQuery('#upgradewiredinfo').hide();	
				}
				jQuery('#stripeinfo').hide();
				jQuery('#twocheckoutinfo').hide();
				jQuery('#payulataminfo').hide();
											jQuery('.upgrade-form')
											.bootstrapValidator('removeField', 'crd_number')
											.bootstrapValidator('removeField', 'crd_cvc')
											.bootstrapValidator('removeField', 'crd_month')
											.bootstrapValidator('removeField', 'crd_year')
											.bootstrapValidator('removeField', 'twocheckout_crd_number')
											.bootstrapValidator('removeField', 'twocheckout_crd_cvc')
											.bootstrapValidator('removeField', 'twocheckout_crd_month')
											.bootstrapValidator('removeField', 'twocheckout_crd_year')
											.bootstrapValidator('removeField', 'payulatam_upgrade_cardtype')
											.bootstrapValidator('removeField', 'payulatam_crd_number')
											.bootstrapValidator('removeField', 'payulatam_crd_cvc')
											.bootstrapValidator('removeField', 'payulatam_crd_month')
											.bootstrapValidator('removeField', 'payulatam_crd_year');
			}
		})
        .on('success.form.bv', function(form) {
				jQuery('form.upgrade-form').find('input[type="submit"]').prop('disabled', false);	
				var payment_mode = jQuery(this).find('input[name=pay_mode]:checked').val();
				var woooption = jQuery(this).find('input[name=upgrade_woopayment]:checked').val();
				
				if(payment_mode == undefined){
					payment_mode = '';
				}
				if(woooption == undefined){
					woooption = '';
				}
				
				if(woopayment && woooption == '' && jQuery(this).find('input[name=upgrade_woopayment]').length && jQuery('#upgradefreemode').val() == 'no'){
					jQuery('.alert').remove();	
					jQuery( "<div class='alert alert-danger'>"+param.payment_method_req+"</div>" ).insertAfter( "form.upgrade-form" );
					jQuery('form.upgrade-form').find('input[type="submit"]').prop('disabled', false);	
					return false;
				}
				
				if(woopayment && !jQuery('input[name=provider-role]:checked').hasClass("free") && payment_mode == "" && woooption != "wallet" && woooption != "skippayment" && jQuery('#upgradefreemode').val() != "yes"){
					var data = {
					  "action": "sf_add_to_woo_cart",
					  "wootype": "upgrade"
					};
						
					var formdata = jQuery('form.upgrade-form').serialize() + "&" + jQuery.param(data);
					
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
								jQuery( "<div class='alert alert-danger'>"+response.error+"</div>" ).insertBefore( "form.upgrade-form" );
								jQuery("html, body").animate({
										scrollTop: jQuery(".alert-danger").offset().top
									}, 1000);
							}
						}
					});  
					return false;						  
				
				}else{
				if(payment_mode == 'paypal_upgrade' || payment_mode == 'payumoney' || payment_mode == 'skippayment' || jQuery('input[name=provider-role]:checked').hasClass("free")){
					return true;
				}else if(payment_mode == 'payulatam_upgrade'){
					// Prevent form submission
					form.preventDefault();
					var crd_type = jQuery('#payulatam_upgrade_cardtype').val();
					var crd_number = jQuery('#payulatam_crd_number').val();
					var crd_cvc = jQuery('#payulatam_crd_cvc').val();
					var crd_month = jQuery('#payulatam_crd_month').val();
					var crd_year = jQuery('#payulatam_crd_year').val();
					if(crd_type != "" && crd_number != "" && crd_cvc != "" && crd_month != "" && crd_year != ""){	
					jQuery('.loading-area').show();
					
					var data = {
							  "action": "payulatam_signup",
							};
							
					var formdata = jQuery('form.upgrade-form').serialize() + "&" + jQuery.param(data);
					
					jQuery.ajax({
							type: 'POST',
							url: ajaxurl,
							data: formdata,
							dataType: "json",
							beforeSend: function() {
								jQuery(".alert-success,.alert-danger").remove();
							},
							success:function (data, textStatus) {
								jQuery('.loading-area').hide();
								jQuery('form.upgrade-form').find('input[type="submit"]').prop('disabled', false);
								if(data['status'] == 'success'){
									jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.upgrade-form" );	
								}else{
									jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.upgrade-form" );
								}
							
							}
					});		
						
						 
					}
				}else if(payment_mode == 'twocheckout_upgrade'){
					// Prevent form submission
					form.preventDefault();
					jQuery('.loading-area').show();
					if(twocheckoutpublishkey != ""){
						
						try {
							TCO.loadPubKey(twocheckoutmode);
							upgradetokenRequest();
						} catch(e) {
							jQuery('.loading-area').hide();
							jQuery(".alert-success,.alert-danger").remove();
							jQuery( "<div class='alert alert-danger'>"+e.toSource()+"</div>" ).insertBefore( "form.upgrade-form" );
							jQuery("html, body").animate({
									scrollTop: jQuery(".alert-danger").offset().top
								}, 1000);
	
						}
					
					}else{
						jQuery('.loading-area').hide();
						jQuery( "<div class='alert alert-danger'>"+param.pub_key+"</div>" ).insertBefore( "form.upgrade-form" );
						jQuery("html, body").animate({
								scrollTop: jQuery(".alert-danger").offset().top
							}, 1000);
					}	
					
				
				}else if(payment_mode == 'stripe_upgrade'){
					// Prevent form submission
					form.preventDefault();
					var crd_number = jQuery('#crd_number').val();
					var crd_cvc = jQuery('#crd_cvc').val();
					var crd_month = jQuery('#crd_month').val();
					var crd_year = jQuery('#crd_year').val();
					if(month_flag==1 || year_flag==1){return false;}
					if(crd_number != "" && crd_cvc != "" && crd_month != "" && crd_year != ""){	
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
									  number: jQuery('#crd_number').val(),
									  cvc: jQuery('#crd_cvc').val(),
									  exp_month: jQuery('#crd_month').val(),
									  exp_year: jQuery('#crd_year').val(),
									}, service_finder_stripeUpgradeResponseHandler);		
							}else{
									jQuery('.loading-area').hide();
								    jQuery(".alert-success,.alert-danger").remove();
									jQuery( "<div class='alert alert-danger'>"+param.set_key+"</div>" ).insertBefore( "form.upgrade-form" );
									return false;
							}
							}
					});	
						
						 
					}
		
				}else if(payment_mode == 'wallet' || woooption == "wallet"){
					
						form.preventDefault();
						var data = {
							  "action": "wallet_upgrade",
							};
							
						var formdata = jQuery('form.upgrade-form').serialize() + "&" + jQuery.param(data);
						
						jQuery.ajax({
			
									type: 'POST',
			
									url: ajaxurl,
									
									dataType: "json",
									
									beforeSend: function() {
										jQuery(".alert-success,.alert-danger").remove();
										jQuery('.loading-area').show();
									},
									
									data: formdata,
			
									success:function (data, textStatus) {
										jQuery('.loading-area').hide();
										jQuery('form.upgrade-form').find('input[type="submit"]').prop('disabled', false);
										if(data['status'] == 'success'){
											jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.upgrade-form" );	
										}else{
											jQuery( "<div class='alert alert-danger'>"+param.insufficient_wallet_amount+"</div>" ).insertBefore( "form.upgrade-form" );
											jQuery("html, body").animate({
												scrollTop: jQuery(".alert-danger").offset().top
											}, 1000);	
										}
									
									}
			
								});
						
					}
				}
		});
	
	var minval = jQuery('#minvalue').val();
	var maxval = jQuery('#maxvalue').val();
	jQuery("input[name='featuredays']").TouchSpin({
      verticalbuttons: true,
      verticalupclass: 'glyphicon glyphicon-plus',
      verticaldownclass: 'glyphicon glyphicon-minus',
	   min: minval,
        max: maxval
    });	
	
	//Request to make feature
	jQuery('.feature-form')
        .bootstrapValidator({
            message: param.not_valid,
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
				
            }
        })
		.on('error.field.bv', function(e, data) {
            data.bv.disableSubmitButtons(false); // disable submit buttons on errors
	    })
		.on('status.field.bv', function(e, data) {
            data.bv.disableSubmitButtons(false); // disable submit buttons on valid
        })
		.on('change', 'input[name="make-feature"]', function() {
			var feature = jQuery(this).val();
			if(jQuery(this).is(':checked')) { 
                jQuery('#feature-bx').show();
            }else{
				jQuery('#feature-bx').hide();
			}
		})
        .on('success.form.bv', function(form) {
            // Prevent form submission
			
            form.preventDefault();

            // Get the form instance
            var $form = jQuery(form.target);
            // Get the BootstrapValidator instance
            var bv = $form.data('bootstrapValidator');
			
			var data = {
			  "action": "make_feature",
			};
			
			var formdata = jQuery($form).serialize() + "&" + jQuery.param(data);
			
			jQuery.ajax({

						type: 'POST',

						url: ajaxurl,
						
						dataType: "json",
						
						beforeSend: function() {
							jQuery(".alert-success,.alert-danger").remove();
							jQuery('.loading-area').show();
						},
						
						data: formdata,

						success:function (data, textStatus) {
							jQuery('.loading-area').hide();
							$form.find('input[type="submit"]').prop('disabled', false);
							if(data['status'] == 'success'){
								jQuery( "<div class='alert alert-info'>"+data['suc_message']+"</div>" ).insertBefore( "#feature-req-bx" );
								jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.upgrade-form" );
								jQuery( "#feature-req-bx>" ).remove();
							}else if(data['status'] == 'error'){
								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "#feature-req-bx" );
							}
							
							
						
						}

					});
			
        });	
		
		//Make payment for feature
		jQuery('.feature-payment-form')
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
				jQuery('#featurecardinfo').show();
				jQuery('#featuredwiredinfo').hide();
				jQuery('#twocheckout_featurecardinfo').hide();
				jQuery('#payulatam_featurecardinfo').hide();
											jQuery('.feature-payment-form')
											.bootstrapValidator('addField', 'fcd_number', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											})
											.bootstrapValidator('addField', 'fcd_cvc', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											})
											.bootstrapValidator('addField', 'fcd_month', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											})
											.bootstrapValidator('addField', 'fcd_year', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											});
											
											jQuery('.feature-payment-form')
											.bootstrapValidator('removeField', 'twocheckout_fcd_number')
											.bootstrapValidator('removeField', 'twocheckout_fcd_cvc')
											.bootstrapValidator('removeField', 'twocheckout_fcd_month')
											.bootstrapValidator('removeField', 'twocheckout_fcd_year')
											.bootstrapValidator('removeField', 'payulatam_f_cardtype')
											.bootstrapValidator('removeField', 'payulatam_fcd_number')
											.bootstrapValidator('removeField', 'payulatam_fcd_cvc')
											.bootstrapValidator('removeField', 'payulatam_fcd_month')
											.bootstrapValidator('removeField', 'payulatam_fcd_year');
			}else if(paymode == 'twocheckout'){
				jQuery('#featurecardinfo').hide();
				jQuery('#featuredwiredinfo').hide();
				jQuery('#twocheckout_featurecardinfo').show();
				jQuery('#payulatam_featurecardinfo').hide();
											jQuery('.feature-payment-form')
											.bootstrapValidator('addField', 'twocheckout_fcd_number', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											})
											.bootstrapValidator('addField', 'twocheckout_fcd_cvc', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											})
											.bootstrapValidator('addField', 'twocheckout_fcd_month', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											})
											.bootstrapValidator('addField', 'twocheckout_fcd_year', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											});
											
											jQuery('.feature-payment-form')
											.bootstrapValidator('removeField', 'fcd_number')
											.bootstrapValidator('removeField', 'fcd_cvc')
											.bootstrapValidator('removeField', 'fcd_month')
											.bootstrapValidator('removeField', 'fcd_year')
											.bootstrapValidator('removeField', 'payulatam_f_cardtype')
											.bootstrapValidator('removeField', 'payulatam_fcd_number')
											.bootstrapValidator('removeField', 'payulatam_fcd_cvc')
											.bootstrapValidator('removeField', 'payulatam_fcd_month')
											.bootstrapValidator('removeField', 'payulatam_fcd_year');
											
			}else if(paymode == 'payulatam'){
				jQuery('#featurecardinfo').hide();
				jQuery('#featuredwiredinfo').hide();
				jQuery('#twocheckout_featurecardinfo').hide();
				jQuery('#payulatam_featurecardinfo').show();
											jQuery('.feature-payment-form')
											.bootstrapValidator('addField', 'payulatam_f_cardtype', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											})
											.bootstrapValidator('addField', 'payulatam_fcd_number', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											})
											.bootstrapValidator('addField', 'payulatam_fcd_cvc', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											})
											.bootstrapValidator('addField', 'payulatam_fcd_month', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											})
											.bootstrapValidator('addField', 'payulatam_fcd_year', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											});
											
											jQuery('.feature-payment-form')
											.bootstrapValidator('removeField', 'fcd_number')
											.bootstrapValidator('removeField', 'fcd_cvc')
											.bootstrapValidator('removeField', 'fcd_month')
											.bootstrapValidator('removeField', 'fcd_year')
											.bootstrapValidator('removeField', 'twocheckout_fcd_number')
											.bootstrapValidator('removeField', 'twocheckout_fcd_cvc')
											.bootstrapValidator('removeField', 'twocheckout_fcd_month')
											.bootstrapValidator('removeField', 'twocheckout_fcd_year');
											
			}else{
				if(paymode == 'wired'){
				jQuery('#featuredwiredinfo').show();					
				}else{
				jQuery('#featuredwiredinfo').hide();	
				}
				jQuery('#featurecardinfo').hide();
				jQuery('#twocheckout_featurecardinfo').hide();
				jQuery('#payulatam_featurecardinfo').hide();
											jQuery('.feature-payment-form')
											.bootstrapValidator('removeField', 'fcd_number')
											.bootstrapValidator('removeField', 'fcd_cvc')
											.bootstrapValidator('removeField', 'fcd_month')
											.bootstrapValidator('removeField', 'fcd_year')
											.bootstrapValidator('removeField', 'twocheckout_fcd_number')
											.bootstrapValidator('removeField', 'twocheckout_fcd_cvc')
											.bootstrapValidator('removeField', 'twocheckout_fcd_month')
											.bootstrapValidator('removeField', 'twocheckout_fcd_year')
											.bootstrapValidator('removeField', 'payulatam_f_cardtype')
											.bootstrapValidator('removeField', 'payulatam_fcd_number')
											.bootstrapValidator('removeField', 'payulatam_fcd_cvc')
											.bootstrapValidator('removeField', 'payulatam_fcd_month')
											.bootstrapValidator('removeField', 'payulatam_fcd_year');
			}
		})
        .on('success.form.bv', function(form) {
				
				jQuery('form.feature-payment-form').find('input[type="submit"]').prop('disabled', false);	
				var payment_mode = jQuery(this).find('input[name=payment_mode]:checked').val();
				var woooption = jQuery(this).find('input[name=feature_woopayment]:checked').val();
				
				if(woooption == undefined){
					woooption = '';
				}
				
				if(woopayment && woooption == '' && jQuery(this).find('input[name=feature_woopayment]').length){
					jQuery('.alert').remove();	
					jQuery( "<div class='alert alert-danger'>"+param.payment_method_req+"</div>" ).insertAfter( "form.feature-payment-form" );
					jQuery('form.feature-payment-form').find('input[type="submit"]').prop('disabled', false);	
					return false;
				}
				
				if(woopayment && woooption != "wallet"){
					var data = {
					  "action": "sf_add_to_woo_cart",
					  "wootype": "featured"
					};
						
					var formdata = jQuery('form.feature-payment-form').serialize() + "&" + jQuery.param(data);
					
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
								jQuery( "<div class='alert alert-danger'>"+response.error+"</div>" ).insertBefore( "form.feature-payment-form" );
								jQuery("html, body").animate({
										scrollTop: jQuery(".alert-danger").offset().top
									}, 1000);
							}
						}
					});  
					return false;	
				}else{
				if(payment_mode == 'paypal' || payment_mode == 'payumoney' || payment_mode == 'wired'){
					return true;
				}else if(payment_mode == 'stripe'){
					// Prevent form submission
					form.preventDefault();
					var crd_number = jQuery('#fcd_number').val();
					var crd_cvc = jQuery('#fcd_cvc').val();
					var crd_month = jQuery('#fcd_month').val();
					var crd_year = jQuery('#fcd_year').val();
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
							  number: jQuery('#fcd_number').val(),
							  cvc: jQuery('#fcd_cvc').val(),
							  exp_month: jQuery('#fcd_month').val(),
							  exp_year: jQuery('#fcd_year').val(),
							}, service_finder_stripeFeatureResponseHandler);
							}else{
									jQuery('.loading-area').hide();
								    jQuery(".alert-success,.alert-danger").remove();
									jQuery( "<div class='alert alert-danger'>"+param.set_key+"</div>" ).insertBefore( "form.feature-payment-form" );
									return false;
							}
							}
					});		
						
						 
					}
		
				
				}else if(payment_mode == 'payulatam'){
					// Prevent form submission
					form.preventDefault();
					var crd_type = jQuery('#payulatam_f_cardtype').val();
					var crd_number = jQuery('#payulatam_fcd_number').val();
					var crd_cvc = jQuery('#payulatam_fcd_cvc').val();
					var crd_month = jQuery('#payulatam_fcd_month').val();
					var crd_year = jQuery('#payulatam_fcd_year').val();
					if(crd_type != "" && crd_number != "" && crd_cvc != "" && crd_month != "" && crd_year != ""){	
					jQuery('.loading-area').show();
					
					var data = {
						  "action": "payulatam_feature_payment",
						};
						
					var formdata = jQuery('form.feature-payment-form').serialize() + "&" + jQuery.param(data);
					
					jQuery.ajax({
							type: 'POST',
							url: ajaxurl,
							data: formdata,
							dataType: "json",
							success:function (data, textStatus) {
							jQuery('.loading-area').hide();	
							jQuery(".alert-success,.alert-danger").remove();
							if(data['status'] == 'success'){								
								jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "#feature-req-bx" );	
								jQuery( "<div class='alert alert-info'>"+data['display_message']+"</div>" ).insertBefore( "#feature-req-bx" );	
								jQuery( "#feature-req-bx>" ).hide();
							}else{
								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.feature-payment-form" );
							}
							}
					});		
						
						 
					}
		
				
				}else if(payment_mode == 'twocheckout'){
					// Prevent form submission
					form.preventDefault();
					jQuery('.loading-area').show();
					if(twocheckoutpublishkey != ""){
						
						try {
							TCO.loadPubKey(twocheckoutmode);
							tokenRequest();
						} catch(e) {
							jQuery('.loading-area').hide();
							jQuery(".alert-success,.alert-danger").remove();
							jQuery( "<div class='alert alert-danger'>"+e.toSource()+"</div>" ).insertBefore( "form.feature-payment-form" );
							jQuery("html, body").animate({
									scrollTop: jQuery(".alert-danger").offset().top
								}, 1000);
	
						}
					
					}else{
						jQuery('.loading-area').hide();
						jQuery( "<div class='alert alert-danger'>"+param.pub_key+"</div>" ).insertBefore( "form.feature-payment-form" );
						jQuery("html, body").animate({
								scrollTop: jQuery(".alert-danger").offset().top
							}, 1000);
					}	
					
				}else if(payment_mode == 'wallet' || woooption == "wallet"){
					
						form.preventDefault();
						var data = {
							  "action": "feature_wallet",
							};
							
						var formdata = jQuery('form.feature-payment-form').serialize() + "&" + jQuery.param(data);
						
						jQuery.ajax({
			
									type: 'POST',
			
									url: ajaxurl,
									
									dataType: "json",
									
									beforeSend: function() {
										jQuery(".alert-success,.alert-danger").remove();
										jQuery('.loading-area').show();
									},
									
									data: formdata,
			
									success:function (data, textStatus) {
										jQuery('.loading-area').hide();
										jQuery('form.feature-payment-form').find('input[type="submit"]').prop('disabled', false);
										if(data['status'] == 'success'){
											jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "#feature-req-bx" );	
											jQuery( "<div class='alert alert-info'>"+data['display_message']+"</div>" ).insertBefore( "#feature-req-bx" );	
									jQuery( "#feature-req-bx>" ).hide();
										}else{
											jQuery( "<div class='alert alert-danger'>"+param.insufficient_wallet_amount+"</div>" ).insertBefore( "form.feature-payment-form" );
											jQuery("html, body").animate({
												scrollTop: jQuery(".alert-danger").offset().top
											}, 1000);	
										}
									
									}
			
								});
						
					}
				}
		});
		
		//Stripe handler for payment of account upgrade
		function service_finder_stripeUpgradeResponseHandler(status, response) {
  
			  if (response.error) {
				  // Show the errors on the form
				  jQuery('.loading-area').hide();
				  jQuery(".alert-success,.alert-danger").remove();
				  jQuery( "<div class='alert alert-danger'>"+response.error.message+"</div>" ).insertBefore( "form.upgrade-form" );
				
			  } else {
				// response contains id and card, which contains additional card details
				var token = response.id;
				
				var data = {
					  "action": "signup",
					  "stripeToken": token,
					};
					
				var formdata = jQuery('form.upgrade-form').serialize() + "&" + jQuery.param(data);
				
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
								jQuery('form.upgrade-form').find('input[type="submit"]').prop('disabled', false);
								if(data['status'] == 'success'){
									jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.upgrade-form" );	
									window.location.reload();
								}else{
									jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.upgrade-form" );
								}
							
							}
	
						});
				
				}
		}
		
		//Stripe handler for payment to make feature
		function service_finder_stripeFeatureResponseHandler(status, response) {
  
			  if (response.error) {
				  // Show the errors on the form
				  jQuery('.loading-area').hide();
				  jQuery(".alert-success,.alert-danger").remove();
				   jQuery(".alert-bx").remove();
				  jQuery( "<div class='alert alert-danger'>"+response.error.message+"</div>" ).insertBefore( "form.feature-payment-form" );
				
			  } else {
				// response contains id and card, which contains additional card details
				var token = response.id;
				
				var data = {
					  "action": "feature_payment",
					  "stripeToken": token,
					};
					
				var formdata = jQuery('form.feature-payment-form').serialize() + "&" + jQuery.param(data);
				
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
								jQuery('form.feature-payment-form').find('input[type="submit"]').prop('disabled', false);
								if(data['status'] == 'success'){
									jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "#feature-req-bx" );	
									jQuery( "<div class='alert alert-info'>"+data['display_message']+"</div>" ).insertBefore( "#feature-req-bx" );	
									jQuery( "#feature-req-bx>" ).hide();
								}else{
									jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.feature-payment-form" );
								}
							
							}
	
						});
				
				}
		}
		
	//Cancel Subscription
	jQuery('body').on('click', '.cancel-subscription', function(){
												  
		var userid = jQuery(this).data('userid');
		var currenturl = jQuery(this).data('url');
		
		bootbox.confirm(param.cancel_sub, function(result) {
		  if(result){
			  var data = {
			  "action": "cancel_provider_subscription",
			  "userid": userid,
			};
			
			var data = jQuery.param(data);
			
			jQuery.ajax({

						type: 'POST',

						url: ajaxurl,
						
						data: data,
						
						dataType: "json",
						
						beforeSend: function() {
							jQuery('.loading-area').show();
						},

						success:function (data, textStatus) {
							jQuery('.loading-area').hide();
							if(data['status'] == 'success'){
								window.location.href= currenturl+'?cancelsubscription=success';	
							}else{
								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.upgrade-form" );
							}
							
						}

					});
			  }
		}); 
		
    });	
	
	//Cancel Featured/Featured Request
	jQuery('body').on('click', '.cancel-featured', function(){
												  
		var userid = jQuery(this).data('userid');
		
		bootbox.confirm(param.cancel_featured, function(result) {
		  if(result){
			  var data = {
			  "action": "cancel_provider_featured",
			  "userid": userid,
			};
			
			var data = jQuery.param(data);
			
			jQuery.ajax({

						type: 'POST',

						url: ajaxurl,
						
						data: data,
						
						dataType: "json",
						
						beforeSend: function() {
							jQuery('.loading-area').show();
						},

						success:function (data, textStatus) {
							jQuery('.loading-area').hide();
							if(data['status'] == 'success'){
								window.location.href= data['redirect'];	
							}else{
								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.upgrade-form" );
							}
							
						}

					});
			  }
		}); 
		
    });	
				  
  });
  
  /*Featured Payment via twocheckout Start*/
  	var tokenRequest = function() {
		
	var twocheckout_card_number = jQuery('input[name="twocheckout_fcd_number"]').val();
	var twocheckout_card_cvc = jQuery('input[name="twocheckout_fcd_cvc"]').val();
	var twocheckout_card_month = jQuery('select[name="twocheckout_fcd_month"]').val();
	var twocheckout_card_year = jQuery('select[name="twocheckout_fcd_year"]').val();
	
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
					  "action": "twocheckout_feature_payment",
					  "twocheckouttoken": token,
					};
					
			var formdata = jQuery('form.feature-payment-form').serialize() + "&" + jQuery.param(data);
			
			jQuery.ajax({
	
					type: 'POST',
	
					url: ajaxurl,
					
					dataType: "json",
					
					beforeSend: function() {
						jQuery(".alert-success,.alert-danger").remove();
						jQuery('.loading-area').show();
					},
					
					data: formdata,
	
					success:function (data, textStatus) {
						jQuery('.loading-area').hide();
						jQuery('form.feature-payment-form').find('input[type="submit"]').prop('disabled', false);
						if(data['status'] == 'success'){
							jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.feature-payment-form" );	
							jQuery( "<div class='alert alert-info'>"+data['display_message']+"</div>" ).insertBefore( "#feature-req-bx" );	
							jQuery( "#feature-req-bx>" ).hide();
						}else{
							jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.feature-payment-form" );
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
		  jQuery( "<div class='alert alert-danger'>"+data.errorMsg+"</div>" ).insertBefore( "form.feature-payment-form" );
		  jQuery("html, body").animate({
								scrollTop: jQuery(".alert-danger").offset().top
							}, 1000);
		}
	  };
  /*Featured Payment via twocheckout End*/	  
	  
  /*Upgrade Payment via twocheckout Start*/	  
	var upgradetokenRequest = function() {
		
	var twocheckout_card_number = jQuery('input[name="twocheckout_crd_number"]').val();
	var twocheckout_card_cvc = jQuery('input[name="twocheckout_crd_cvc"]').val();
	var twocheckout_card_month = jQuery('select[name="twocheckout_crd_month"]').val();
	var twocheckout_card_year = jQuery('select[name="twocheckout_crd_year"]').val();
	
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
	TCO.requestToken(upgradesuccessCallback, upgradeerrorCallback, args);
	};
	
	// Called when token created successfully.
	var upgradesuccessCallback = function(data) {
		// Set the token as the value for the token input
		var token = data.response.token.token;
			
			/*To Add Service cost also in minimum cost*/
			
			var data = {
					  "action": "twocheckout_signup",
					  "twocheckouttoken": token,
					};
					
			var formdata = jQuery('form.upgrade-form').serialize() + "&" + jQuery.param(data);
			
			jQuery.ajax({
	
					type: 'POST',
	
					url: ajaxurl,
					
					dataType: "json",
					
					beforeSend: function() {
						jQuery(".alert-success,.alert-danger").remove();
						jQuery('.loading-area').show();
					},
					
					data: formdata,
	
					success:function (data, textStatus) {
						jQuery('.loading-area').hide();
						jQuery('form.upgrade-form').find('input[type="submit"]').prop('disabled', false);
						if(data['status'] == 'success'){
							jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.upgrade-form" );	
						}else{
							jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.upgrade-form" );
						}
						
					}
	
				});
	
	  };
	
	// Called when token creation fails.
	var upgradeerrorCallback = function(data) {
		if (data.errorCode === 200) {
		  // This error code indicates that the ajax call failed. We recommend that you retry the token request.
		} else {
		  jQuery('.loading-area').hide();
		  jQuery('form.upgrade-form').find('input[type="submit"]').prop('disabled', false);
		  jQuery( "<div class='alert alert-danger'>"+data.errorMsg+"</div>" ).insertBefore( "form.upgrade-form" );
		  jQuery("html, body").animate({
								scrollTop: jQuery(".alert-danger").offset().top
							}, 1000);
		}
	  };  
  /*Upgrade Payment via twocheckout End*/	  	  
	  