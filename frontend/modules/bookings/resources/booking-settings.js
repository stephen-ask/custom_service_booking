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
	
	/*stripe connect custom account creation with gereral fields*/
    jQuery('.booking-settings')
    .bootstrapValidator({
            message: param.not_valid,
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
    })
	.on('change', 'input[type="radio"][name="slot_interval"]', function() {
			bootbox.alert(param.change_interval_warning);																			
		})																			  
	.on('change', 'input[type="radio"][name="booking_process"]', function() {
                var bookingProcess   = jQuery(this).val();
				var bookingOption   = jQuery('input[type="radio"][name="booking_option"]:checked').val();
				var bookingAssignment   = jQuery('input[type="radio"][name="booking_assignment"]:checked').val();
                if (bookingProcess == 'off') {
                    jQuery('#bookingalert').hide();
					jQuery('#bookingchargeamount').hide();
					jQuery('#bookingbasedon').hide();
					jQuery('#bookingOption').hide();
					jQuery('#minCost').hide();
					jQuery('#futureavailability').hide();
					jQuery('#paypalemail').hide();
					jQuery('#payoptions').hide();
					jQuery('#stripekey').hide();
					jQuery('#twocheckoutkey').hide();
					jQuery('#wiredescription').hide();
					jQuery('#wireinstructions').hide();
					jQuery('#bookingAssignment').hide();
					jQuery('#membersAvailable').hide();

                } else if(bookingProcess == 'on' && bookingOption == 'paid') {
					jQuery('#bookingalert').show();
					jQuery('#bookingchargeamount').show();
					jQuery('#bookingbasedon').show();
                    jQuery('#bookingOption').show();
					jQuery('#minCost').show();
					jQuery('#futureavailability').show();
					jQuery('#paypalemail').show();
					jQuery('#stripekey').show();
					jQuery('#twocheckoutkey').show();
					jQuery('#wiredescription').show();
					jQuery('#wireinstructions').show();
					jQuery('#payoptions').hide();
					jQuery('#bookingAssignment').show();
					jQuery('#membersAvailable').show();
					
				} else if(bookingProcess == 'on') {
					jQuery('#bookingalert').show();
					jQuery('#bookingchargeamount').show();		
					jQuery('#bookingbasedon').show();
                    jQuery('#bookingOption').show();
					jQuery('#bookingAssignment').show();
					jQuery('#membersAvailable').show();
					jQuery('#paypalemail').hide();
					jQuery('#stripekey').hide();
					jQuery('#twocheckoutkey').hide();
					jQuery('#wiredescription').hide();
					jQuery('#wireinstructions').hide();
					jQuery('#minCost').show();
					jQuery('#futureavailability').show();
				} 
				if(bookingProcess == 'on' && bookingAssignment == 'automatically') {
					jQuery('#membersAvailable').show();
				 }else if(bookingProcess == 'on' && bookingAssignment == 'manually') {
					jQuery('#membersAvailable').hide();
				 } 
         })
	.on('change', 'input[type="radio"][name="booking_assignment"]', function() {
                var bookingAssignment = jQuery(this).val();
                if (bookingAssignment == 'manually') {
                    jQuery('#membersAvailable').hide();

				} else if(bookingAssignment == 'automatically') {
					jQuery('#membersAvailable').show();
				} 
         })
	.on('click', 'input[type="checkbox"][name="pay_options[]"]', function() {
				var option = jQuery(this).val();
				if(jQuery(this).is(':checked')) { 
					if(option == 'paypal'){
						jQuery('#paypalemail').show();
											jQuery('.user-update')
											.bootstrapValidator('addField', 'paypalusername', {
												validators: {
													notEmpty: {
														message: param.req
													},
												}
											})
											.bootstrapValidator('addField', 'paypalpassword', {
												validators: {
													notEmpty: {
														message: param.req
													},
												}
											})
											.bootstrapValidator('addField', 'paypalsignatue', {
												validators: {
													notEmpty: {
														message: param.req
													},
												}
											});
											
						
					}else if(option == 'stripe'){
						jQuery('#stripekey').show();
						
										    jQuery('.user-update')
											.bootstrapValidator('addField', 'stripesecretkey', {
												validators: {
													notEmpty: {
														message: param.req
													},
												}
											})
											.bootstrapValidator('addField', 'stripepublickey', {
												validators: {
													notEmpty: {
														message: param.req
													},
												}
											});
					}else if(option == 'twocheckout'){
						jQuery('#twocheckoutkey').show();
						
										    jQuery('.user-update')
											.bootstrapValidator('addField', 'twocheckoutaccountid', {
												validators: {
													notEmpty: {
														message: param.req
													},
												}
											})
											.bootstrapValidator('addField', 'twocheckoutpublishkey', {
												validators: {
													notEmpty: {
														message: param.req
													},
												}
											})
											.bootstrapValidator('addField', 'twocheckoutprivatekey', {
												validators: {
													notEmpty: {
														message: param.req
													},
												}
											});
					}else if(option == 'wired'){
						jQuery('#wiredescription').show();
						jQuery('#wireinstructions').show();
						
										    jQuery('.user-update')
											.bootstrapValidator('addField', 'wired_description', {
												validators: {
													notEmpty: {
														message: param.req
													},
												}
											})
											.bootstrapValidator('addField', 'wired_instructions', {
												validators: {
													notEmpty: {
														message: param.req
													},
												}
											});
					}else if(option == 'payumoney'){
						jQuery('#payumoneyinfo').show();
						
										    jQuery('.user-update')
											.bootstrapValidator('addField', 'payumoneymid', {
												validators: {
													notEmpty: {
														message: param.req
													},
												}
											})
											.bootstrapValidator('addField', 'payumoneykey', {
												validators: {
													notEmpty: {
														message: param.req
													},
												}
											})
											.bootstrapValidator('addField', 'payumoneysalt', {
												validators: {
													notEmpty: {
														message: param.req
													},
												}
											});
					}else if(option == 'payulatam'){
						jQuery('#payulataminfo').show();
						
										    jQuery('.user-update')
											.bootstrapValidator('addField', 'payulatammerchantid', {
												validators: {
													notEmpty: {
														message: param.req
													},
												}
											})
											.bootstrapValidator('addField', 'payulatamapilogin', {
												validators: {
													notEmpty: {
														message: param.req
													},
												}
											})
											.bootstrapValidator('addField', 'payulatamapikey', {
												validators: {
													notEmpty: {
														message: param.req
													},
												}
											})
											.bootstrapValidator('addField', 'payulatamaccountid', {
												validators: {
													notEmpty: {
														message: param.req
													},
												}
											});
					}
					
                }else{
					if(option == 'paypal'){
						jQuery('#paypalemail').hide();
											jQuery('.user-update')
											.bootstrapValidator('removeField', 'paypalusername')
											.bootstrapValidator('removeField', 'paypalpassword')
											.bootstrapValidator('removeField', 'paypalsignatue');
						
					}else if(option == 'stripe'){
						jQuery('#stripekey').hide();
											jQuery('.user-update')
											.bootstrapValidator('removeField', 'stripesecretkey')
											.bootstrapValidator('removeField', 'stripepublickey');
					}else if(option == 'twocheckout'){
						jQuery('#twocheckoutkey').hide();
											jQuery('.user-update')
											.bootstrapValidator('removeField', 'twocheckoutaccountid')
											.bootstrapValidator('removeField', 'twocheckoutpublishkey')
											.bootstrapValidator('removeField', 'twocheckoutprivatekey');
					}else if(option == 'wired'){
						jQuery('#wiredescription').hide();
						jQuery('#wireinstructions').hide();
											jQuery('.user-update')
											.bootstrapValidator('removeField', 'wired_description')
											.bootstrapValidator('removeField', 'wired_instructions');
					}else if(option == 'payumoney'){
						jQuery('#payumoneyinfo').hide();
											jQuery('.user-update')
											.bootstrapValidator('removeField', 'payumoneymid')
											.bootstrapValidator('removeField', 'payumoneykey')
											.bootstrapValidator('removeField', 'payumoneysalt');
					}else if(option == 'payulatam'){
						jQuery('#payulataminfo').hide();
											jQuery('.user-update')
											.bootstrapValidator('removeField', 'payulatammerchantid')
											.bootstrapValidator('removeField', 'payulatamapilogin')
											.bootstrapValidator('removeField', 'payulatamapikey')
											.bootstrapValidator('removeField', 'payulatamaccountid');
					}
				}																			
         })
	.on('change', 'input[type="radio"][name="booking_option"]', function() {
                var bookingOption   = jQuery(this).val();

                if (bookingOption == 'free') {
					jQuery('#payoptions').hide();
					jQuery('#allpaymentinfo').hide();
					
					jQuery('.user-update')
                       .bootstrapValidator('removeField', 'mincost');

                } else if(bookingOption == 'paid') {
                    jQuery('#minCost').show();
					jQuery('#payoptions').show();
					jQuery('#allpaymentinfo').show();
					
					jQuery('.user-update')
                       .bootstrapValidator('addField', 'mincost', {
                            validators: {
                                notEmpty: {
                                    message: param.min_cost
                                }
                            }
                        });
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
			  "action": "update_booking_settings",
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
						jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.booking-settings" );
						if(data['reloadflag'] == 1){
						window.setTimeout(function(){
							if(data['redirect'] != ""){
							window.location.href= data['redirect'];
							}
						}, 2000);
						}		
					}else if(data['status'] == 'error'){
						jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.booking-settings" );
					}
				
				}

			});
			
    });
	
  });