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
	
	var dataTable;
	var txndataTable;
	
	jQuery('body').on('click', '.viewapplyjob', function(){
														
		jQuery('.loading-area').show();												
        // Get the record's ID via attribute
        var jobid = jQuery(this).data('jobid');

		var data = {
			  "action": "load_applied_job",
			  "jobid": jobid
			};

		var formdata = jQuery.param(data);

	  jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: formdata,
						dataType: "json",
						success:function (data, textStatus) {
							// Populate the form fields with the data returned from server
							jQuery('#applyforjobedit')
								.find('[name="costing"]').val(data['editcost']).end()
								.find('[name="description"]').val(data['editdesc']).end()
								.find('[name="jobid"]').val(jobid).end();

							// Show the dialog
							bootbox
								.dialog({
									title: param.view_applied_job,
									message: jQuery('#applyforjobedit'),
									show: false // We will show it manually later
								})
								.on('shown.bs.modal', function() {
									
									jQuery('.loading-area').hide();

									jQuery('#applyforjobedit')

										.show()                             // Show the login form

										.bootstrapValidator('resetForm'); // Reset form

								})
								.on('hide.bs.modal', function(e) {

									jQuery('#applyforjobedit').hide().appendTo('body');

								})
								.modal('show');
						}
					});
    });
	
	jQuery('ul#sf-plans').on('click', 'li', function(){
		var planid = jQuery(this).data('planid');
		jQuery('#planid').val(planid);
		jQuery('ul#sf-plans li').removeClass('selected');
		jQuery(this).addClass('selected');																				   
		jQuery('#sf-selectplan').remove();
	});	
	
	
	if ( ! jQuery.fn.DataTable.isDataTable( '#joblimits-grid' ) ) {
	txndataTable = jQuery('#joblimits-grid').DataTable( {

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

		data: {"action": "get_joblimits_txn","user_id": user_id},

		error: function(){  // error handling

			jQuery(".joblimits-grid-error").html("");

			jQuery("#joblimits-grid").append('<tbody class="joblimits-grid-error"><tr><th colspan="3">'+param.no_data+'</th></tr></tbody>');

			jQuery("#joblimits-grid_processing").css("display","none");

			

		}

	}

	} );
	}
		
	
	
	jQuery('body').on('click', '.addlimits', function(){
		  jQuery( "#sf-paybox" ).slideToggle( "slow" );												  
	});	
	
	jQuery('body').on('click', '.viewjoblimittxn', function(){
		  jQuery( "#sf-limit-bx" ).slideToggle( "slow" );
		  jQuery( "#sf-txn-bx" ).slideToggle( "slow" );												  
	});	
	
	jQuery('body').on('click', '.closeJoblimitTxnDetails', function(){
		  jQuery( "#sf-limit-bx" ).slideToggle( "slow" );
		  jQuery( "#sf-txn-bx" ).slideToggle( "slow" );												  
	});	
	
	//Make payment for feature
	jQuery('.joblimit-payment-form')
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
			jQuery('#joblimitcardinfo').show();
			jQuery('#joblimitwiredinfo').hide();
			jQuery('#twocheckout_jobcardinfo').hide();
										jQuery('.joblimit-payment-form')
										.bootstrapValidator('addField', 'jcd_number', {
											validators: {
												notEmpty: {
														message: param.req
													},
												digits: {message: param.only_digits},
											}
										})
										.bootstrapValidator('addField', 'jcd_cvc', {
											validators: {
												notEmpty: {
														message: param.req
													},
												digits: {message: param.only_digits},
											}
										})
										.bootstrapValidator('addField', 'jcd_month', {
											validators: {
												notEmpty: {
														message: param.req
													},
												digits: {message: param.only_digits},
											}
										})
										.bootstrapValidator('addField', 'jcd_year', {
											validators: {
												notEmpty: {
														message: param.req
													},
												digits: {message: param.only_digits},
											}
										});
										
										jQuery('.joblimit-payment-form')
										.bootstrapValidator('removeField', 'twocheckout_jcd_number')
										.bootstrapValidator('removeField', 'twocheckout_jcd_cvc')
										.bootstrapValidator('removeField', 'twocheckout_jcd_month')
										.bootstrapValidator('removeField', 'twocheckout_jcd_year');
		}else if(paymode == 'twocheckout'){
			jQuery('#joblimitcardinfo').hide();
			jQuery('#joblimitwiredinfo').hide();
			jQuery('#twocheckout_jobcardinfo').show();
										jQuery('.joblimit-payment-form')
										.bootstrapValidator('addField', 'twocheckout_jcd_number', {
											validators: {
												notEmpty: {
														message: param.req
													},
												digits: {message: param.only_digits},
											}
										})
										.bootstrapValidator('addField', 'twocheckout_jcd_cvc', {
											validators: {
												notEmpty: {
														message: param.req
													},
												digits: {message: param.only_digits},
											}
										})
										.bootstrapValidator('addField', 'twocheckout_jcd_month', {
											validators: {
												notEmpty: {
														message: param.req
													},
												digits: {message: param.only_digits},
											}
										})
										.bootstrapValidator('addField', 'twocheckout_jcd_year', {
											validators: {
												notEmpty: {
														message: param.req
													},
												digits: {message: param.only_digits},
											}
										});
										
										jQuery('.joblimit-payment-form')
										.bootstrapValidator('removeField', 'jcd_number')
										.bootstrapValidator('removeField', 'jcd_cvc')
										.bootstrapValidator('removeField', 'jcd_month')
										.bootstrapValidator('removeField', 'jcd_year');
										
		}else{
			if(paymode == 'wired'){
			jQuery('#joblimitwiredinfo').show();
			}else{
			jQuery('#joblimitwiredinfo').hide();
			}
			jQuery('#joblimitcardinfo').hide();
			jQuery('#twocheckout_jobcardinfo').hide();
										jQuery('.joblimit-payment-form')
										.bootstrapValidator('removeField', 'jcd_number')
										.bootstrapValidator('removeField', 'jcd_cvc')
										.bootstrapValidator('removeField', 'jcd_month')
										.bootstrapValidator('removeField', 'jcd_year')
										.bootstrapValidator('removeField', 'twocheckout_jcd_number')
										.bootstrapValidator('removeField', 'twocheckout_jcd_cvc')
										.bootstrapValidator('removeField', 'twocheckout_jcd_month')
										.bootstrapValidator('removeField', 'twocheckout_jcd_year');
		}
	})
	.on('success.form.bv', function(form) {
			
			var planid = jQuery('#planid').val();
			if(planid == ""){
				jQuery('#sf-selectplan').remove();
				jQuery( "<div class='alert alert-danger' id='sf-selectplan'>"+param.select_plan+"</div>" ).insertBefore( "form.joblimit-payment-form" );
				jQuery('form.joblimit-payment-form').find('input[type="submit"]').prop('disabled', false);	
				return false;	
			}
			
			jQuery('form.joblimit-payment-form').find('input[type="submit"]').prop('disabled', false);	
			var payment_mode = jQuery(this).find('input[name=payment_mode]:checked').val();
			var woooption = jQuery(this).find('input[name=joblimit_woopayment]:checked').val();
			
			if(payment_mode == undefined){
				payment_mode = '';
			}
			if(woooption == undefined){
				woooption = '';
			}
			
			if(woopayment && woooption == '' && jQuery(this).find('input[name=joblimit_woopayment]').length){
				jQuery('.alert').remove();	
				jQuery( "<div class='alert alert-danger'>"+param.payment_method_req+"</div>" ).insertAfter( "form.joblimit-payment-form" );
				jQuery('form.joblimit-payment-form').find('input[type="submit"]').prop('disabled', false);	
				return false;
			}
			
			if(woopayment && woooption != "wallet"){
				
					var data = {
					  "action": "sf_add_to_woo_cart",
					  "wootype": "jobpostlimit"
					};
						
					var formdata = jQuery('form.joblimit-payment-form').serialize() + "&" + jQuery.param(data);
					
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
								jQuery( "<div class='alert alert-danger'>"+response.error+"</div>" ).insertBefore( "form.joblimit-payment-form" );
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
				var crd_number = jQuery('#jcd_number').val();
				var crd_cvc = jQuery('#jcd_cvc').val();
				var crd_month = jQuery('#jcd_month').val();
				var crd_year = jQuery('#jcd_year').val();
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
						  number: jQuery('#jcd_number').val(),
						  cvc: jQuery('#jcd_cvc').val(),
						  exp_month: jQuery('#jcd_month').val(),
						  exp_year: jQuery('#jcd_year').val(),
						}, service_finder_stripeJobLimitResponseHandler);
						}else{
								jQuery('.loading-area').hide();
								jQuery(".alert-success,.alert-danger").remove();
								jQuery( "<div class='alert alert-danger'>"+param.set_key+"</div>" ).insertBefore( "form.joblimit-payment-form" );
								return false;
						}
						}
				});		
					
					 
				}
	
			
			}else if(payment_mode == 'twocheckout'){
				// Prevent form submission
				form.preventDefault();
				/*jQuery('.loading-area').show();
				if(twocheckoutpublishkey != ""){
					
					try {
						TCO.loadPubKey(twocheckoutmode);
						tokenRequest();
					} catch(e) {
						jQuery('.loading-area').hide();
						jQuery(".alert-success,.alert-danger").remove();
						jQuery( "<div class='alert alert-danger'>"+e.toSource()+"</div>" ).insertBefore( "form.joblimit-payment-form" );
						jQuery("html, body").animate({
								scrollTop: jQuery(".alert-danger").offset().top
							}, 1000);

					}
				
				}else{
					jQuery('.loading-area').hide();
					jQuery( "<div class='alert alert-danger'>"+param.pub_key+"</div>" ).insertBefore( "form.joblimit-payment-form" );
					jQuery("html, body").animate({
							scrollTop: jQuery(".alert-danger").offset().top
						}, 1000);
				}*/	
				
			}else if(payment_mode == 'wallet' || woooption == "wallet"){
					
						form.preventDefault();
						
						var data = {
						  "action": "joblimit_wallet_payment",
						};
						
						var formdata = jQuery('form.joblimit-payment-form').serialize() + "&" + jQuery.param(data);
						
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
								jQuery('form.joblimit-payment-form').find('input[type="submit"]').prop('disabled', false);
								if(data['status'] == 'success'){
									jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.joblimit-payment-form" );	
								}else{
									jQuery( "<div class='alert alert-danger'>"+param.insufficient_wallet_amount+"</div>" ).insertBefore( "form.joblimit-payment-form" );
									jQuery("html, body").animate({
										scrollTop: jQuery(".alert-danger").offset().top
									}, 1000);	
								}
							
							}
	
						});
						
					}
			}
	});
	
	//Stripe handler for payment to make feature
	function service_finder_stripeJobLimitResponseHandler(status, response) {
  
			  if (response.error) {
				  // Show the errors on the form
				  jQuery('.loading-area').hide();
				  jQuery(".alert-success,.alert-danger").remove();
				   jQuery(".alert-bx").remove();
				  jQuery( "<div class='alert alert-danger'>"+response.error.message+"</div>" ).insertBefore( "form.joblimit-payment-form" );
				
			  } else {
				// response contains id and card, which contains additional card details
				var token = response.id;
				
				var data = {
					  "action": "joblimit_payment",
					  "stripeToken": token,
					};
					
				var formdata = jQuery('form.joblimit-payment-form').serialize() + "&" + jQuery.param(data);
				
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
								jQuery('form.joblimit-payment-form').find('input[type="submit"]').prop('disabled', false);
								if(data['status'] == 'success'){
									jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.joblimit-payment-form" );	
								}else{
									jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.joblimit-payment-form" );
								}
							
							}
	
						});
				
				}
		}
	
	//Change Status
	jQuery('body').on('click', '.changeBookingJobStatus', function(){
												  
		var bid = jQuery(this).attr('data-id');
		
		bootbox.confirm(param.change_complete_status, function(result) {
		  if(result){
			  var data = {
			  "action": "change_status",
			  "bookingid": bid,
			};
			
			var data = jQuery.param(data);
			
			jQuery.ajax({

						type: 'POST',

						url: ajaxurl,
						
						data: data,
						
						beforeSend: function() {
							jQuery('.loading-area').show();
						},

						success:function (data, textStatus) {
							jQuery('.loading-area').hide();
							dataTable.ajax.reload(null, false);
						}

					});
			  }
		}); 
		
    });
	
	//Change Status
	jQuery('body').on('click', '.changeJobStatus', function(){
												  
		var jobid = jQuery(this).attr('data-id');
		
		bootbox.confirm(param.change_complete_status, function(result) {
		  if(result){
			  var data = {
			  "action": "change_job_status",
			  "jobid": jobid,
			};
			
			var data = jQuery.param(data);
			
			jQuery.ajax({

						type: 'POST',

						url: ajaxurl,
						
						data: data,
						
						beforeSend: function() {
							jQuery('.loading-area').show();
						},

						success:function (data, textStatus) {
							jQuery('.loading-area').hide();
							dataTable.ajax.reload(null, false);
						}

					});
			  }
		}); 
		
    });
	
	jQuery('body').on('click', '.approvewiredjob', function(){
		var bookingid = jQuery(this).data('bookingid');													 
		
		var data = {
					  "action": "wired_job_approval",
					  "bookingid": bookingid,
					};
				var formdata = jQuery.param(data);
				  
				jQuery.ajax({
	
					type: 'POST',
	
					url: ajaxurl,
	
					data: formdata,
					
					dataType: "json",
					
					beforeSend: function() {
						jQuery('.loading-area').show();
					},
	
					success:function (data, textStatus) {
							jQuery('.loading-area').hide();
							if(data['status'] == 'success'){
								jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( ".jobs-grid_wrapper" );	
								dataTable.ajax.reload(null, false);
							}else if(data['status'] == 'error'){
								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( ".jobs-grid_wrapper" );
							}
					}
	
				});
	});
	
	//Display Jobs in Data Table
	
	//Tabbing on My Account Page
	jQuery("#myTab a").click(function(e){
		e.preventDefault();
		jQuery(this).tab('show');
		var tabid = jQuery(this).attr('href');
		if(tabid == '#my-jobs'){
			if ( ! jQuery.fn.DataTable.isDataTable( '#jobs-grid' ) ) {
			dataTable = jQuery('#jobs-grid').DataTable( {

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

		data: {"action": "get_applied_jobs","user_id": user_id},

		error: function(){  // error handling

			jQuery(".jobs-grid-error").html("");

			jQuery("#jobs-grid").append('<tbody class="jobs-grid-error"><tr><th colspan="3">'+param.no_data+'</th></tr></tbody>');

			jQuery("#jobs-grid_processing").css("display","none");

			

		}

	}

	} );
			jQuery('.sf-select-box').selectpicker('refresh');
			}
		}
	});

  });