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

	var dataTable = '';

	var mpdataTable = '';

	

	jQuery('body').on('change', 'select[name=bank_country]', function(){

		var country = jQuery(this).val();

		if(country == 'US' || country == 'AU' || country == 'BR' || country == 'CA' || country == 'HK' || country == 'JP' || country == 'SG')

		{

			jQuery('#routing-field').show();

			jQuery('#routing-field-label').html(param.routing_number);

			jQuery('#acount-field-label').html(param.account_number);

		}else if(country == 'MX' || country == 'NZ' || country == 'MY'){

			jQuery('#routing-field').hide();

			jQuery('#routing-field-label').html(param.routing_number);

			jQuery('#acount-field-label').html(param.account_number);

		}else if(country == 'GB'){

			jQuery('#routing-field').show();

			jQuery('#routing-field-label').html(param.sort_code);

			jQuery('#acount-field-label').html(param.account_number);

		}else if(country == 'AT' || country == 'BE' || country == 'BG' || country == 'HR' || country == 'CY' || country == 'CZ' || country == 'DK' || country == 'EE' || country == 'FI' || country == 'FR' || country == 'DE' || country == 'GR' || country == 'HU' || country == 'IE' || country == 'IT' || country == 'LV' || country == 'LT' || country == 'LU' || country == 'MT' || country == 'NL' || country == 'PL' || country == 'PT' || country == 'RO' || country == 'SK' || country == 'SI' || country == 'ES' || country == 'SE'){

			jQuery('#routing-field').hide();

			jQuery('#routing-field-label').html(param.routing_number);

			jQuery('#acount-field-label').html(param.iban_number);

		}else if(country == 'IN'){

			jQuery('#routing-field').show();

			jQuery('#routing-field-label').html(param.ifsc_code);

			jQuery('#acount-field-label').html(param.account_number);

		}

	});

	

	/*Get stripe payout history*/

	dataTable = jQuery('#payout-history-grid').DataTable( {

	"serverSide": true,

	"bAutoWidth": false,

	"order": [[ 2, "desc" ]],

	"columnDefs": [ {

		  "targets": 0,

		  "orderable": false,

		  "searchable": false

		   

		},

		],

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

		data: {"action": "get_payout_history","user_id": user_id},

		error: function(){  // error handling

			jQuery(".payout-history-grid-error").html("");

			jQuery("#payout-history-grid").append('<tbody class="payout-history-grid-error"><tr><th colspan="3">'+param.no_data+'</th></tr></tbody>');

			jQuery("#payout-history-grid_processing").css("display","none");

			

		}

	}

	} );

	

	/*Get mangopay payout history*/

	mpdataTable = jQuery('#mp-payout-history-grid').DataTable( {

	"serverSide": true,

	"bAutoWidth": false,

	"order": [[ 2, "desc" ]],

	"columnDefs": [ {

		  "targets": 0,

		  "orderable": false,

		  "searchable": false

		   

		},

		],

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

		data: {"action": "get_mp_payout_history","user_id": user_id},

		error: function(){  // error handling

			jQuery(".mp-payout-history-grid-error").html("");

			jQuery("#mp-payout-history-grid").append('<tbody class="mp-payout-history-grid-error"><tr><th colspan="3">'+param.no_data+'</th></tr></tbody>');

			jQuery("#mp-payout-history-grid_processing").css("display","none");

			

		}

	}

	} );

	

	

	/*stripe connect custom account creation with gereral fields*/

    jQuery('.payout-settings')

    .bootstrapValidator({

            message: param.not_valid,

            feedbackIcons: {

                valid: 'glyphicon glyphicon-ok',

                invalid: 'glyphicon glyphicon-remove',

                validating: 'glyphicon glyphicon-refresh'

            },

            fields: {

				mp_dob: {

					validators: {

						notEmpty: {

							message: param.req

						}

					}

				},

				mp_nationality: {

					validators: {

						notEmpty: {

							message: param.req

						}

					}

				},

				mp_country: {

					validators: {

						notEmpty: {

							message: param.req

						}

					}

				},

            }

        })

    .on('success.form.bv', function(form) {

            // Prevent form submission

			form.preventDefault();

			

			var mp_nationality = jQuery('.payout-settings select[name="mp_nationality"]').val();	

			var mp_country = jQuery('.payout-settings select[name="mp_country"]').val();	

			

			if(mp_nationality == ''){

				jQuery( "<div class='alert alert-danger'>"+param.nationality_req+"</div>" ).insertBefore( "form.payout-settings" );

				return false;

			}

			

			if(mp_country == ''){

				jQuery( "<div class='alert alert-danger'>"+param.signup_country+"</div>" ).insertBefore( "form.payout-settings" );

				return false;

			}



            // Get the form instance

            var $form = jQuery(form.target);

            // Get the BootstrapValidator instance

            var bv = $form.data('bootstrapValidator');

			

			var data = {

			  "action": "create_mp_account",

			  "user_id": user_id

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

						jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.payout-settings" );

								

					}else if(data['status'] == 'error'){

						jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.payout-settings" );

					}

				

				}



			});

			

    });

	

	/*stripe connect custom account creation with gereral fields*/

    jQuery('.payout-general')

    .bootstrapValidator({

            message: param.not_valid,

            feedbackIcons: {

                valid: 'glyphicon glyphicon-ok',

                invalid: 'glyphicon glyphicon-remove',

                validating: 'glyphicon glyphicon-refresh'

            },

            fields: {

				first_name: {

					validators: {

						notEmpty: {

							message: param.req

						}

					}

				},

				last_name: {

					validators: {

						notEmpty: {

							message: param.req

						}

					}

				},

				email: {

					validators: {

						notEmpty: {

							message: param.req

						},

						emailAddress: {

							message: param.signup_user_email

						}

					}

				},

				dob: {

					validators: {

						notEmpty: {

							message: param.req

						}

					}

				},

				address: {

					validators: {

						notEmpty: {

							message: param.req

						}

					}

				},

				postal_code: {

					validators: {

						notEmpty: {

							message: param.req

						}

					}

				},

				city: {

					validators: {

						notEmpty: {

							message: param.req

						}

					}

				},

				state: {

					validators: {

						notEmpty: {

							message: param.req

						}

					}

				},

				currency: {

					validators: {

						notEmpty: {

							message: param.req

						}

					}

				},

				bank_country: {

					validators: {

						notEmpty: {

							message: param.req

						}

					}

				},

				routing_number: {

					validators: {

						notEmpty: {

							message: param.req

						}

					}

				},

				account_number: {

					validators: {

						notEmpty: {

							message: param.req

						}

					}

				}

            }

        })

    .on('success.form.bv', function(form) {

            // Prevent form submission

			form.preventDefault();

			jQuery('input[type="submit"]').prop('disabled', false);	

            // Get the form instance

            var $form = jQuery(form.target);

            // Get the BootstrapValidator instance

            var bv = $form.data('bootstrapValidator');

			

			var data = {

			  "action": "create_custom_payout_account",

			  "user_id": user_id

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

						jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.payout-general" );

						window.setTimeout(function(){

							window.location.href = data['redirect'];

						}, 2000);		

					}else if(data['status'] == 'error'){

						jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.payout-general" );

					}

				

				},

				error:function (data) {

					$form.find('input[type="submit"]').prop('disabled', false);					

					jQuery('.loading-area').hide();

				}



			});

			

    });

	

	jQuery('.payout_customer_dob').datepicker({

		format: 'yyyy-mm-dd',

		language: langcode

	})

	.on('changeDate', function(evt) {

		// Revalidate the date field

	}).on('hide', function(event) {

		event.preventDefault();

		event.stopPropagation();

	});

	

	jQuery('.mp_dob').datepicker({

		format: 'yyyy-mm-dd',	

		language: langcode

	})

	.on('changeDate', function(evt) {

		// Revalidate the date field

	}).on('hide', function(event) {

		event.preventDefault();

		event.stopPropagation();

	});

	

	/*stripe connect custom account identity verification*/

	jQuery('.stripe-identity-verification')

    .bootstrapValidator({

            message: param.not_valid,

            feedbackIcons: {

                valid: 'glyphicon glyphicon-ok',

                invalid: 'glyphicon glyphicon-remove',

                validating: 'glyphicon glyphicon-refresh'

            },

            fields: {

				personal_id_number: {

					validators: {

						notEmpty: {

							message: param.req

						}

					}

				},

            }

        })

    .on('success.form.bv', function(form) {

            // Prevent form submission

			form.preventDefault();

			jQuery('input[type="submit"]').prop('disabled', false);

            // Get the form instance

            var $form = jQuery(form.target);

            // Get the BootstrapValidator instance

            var bv = $form.data('bootstrapValidator');

			if (jQuery('form.stripe-identity-verification input[name="stripeidentityattachmentid"]').length < 1){
			jQuery( "<div class='alert alert-danger sf-custom-error'>"+param.req_stripe_identity+"</div>" ).insertBefore( "form.stripe-identity-verification" );	
			return false;
			}

			var data = {

			  "action": "stripe_identity_verification",

			  "user_id": user_id

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

						jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.stripe-identity-verification" );

						window.setTimeout(function(){

							window.location.href = data['redirect'];

						}, 2000);		

					}else if(data['status'] == 'error'){

						jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.stripe-identity-verification" );

					}

				

				},

				error:function () {

					jQuery('input[type="submit"]').prop('disabled', false);

					jQuery('.loading-area').hide();

				}



			});

			

    });

	

	/*stripe connect custom account update with extrnal bank account details*/

	jQuery('.stripe-external-account')

    .bootstrapValidator({

            message: param.not_valid,

            feedbackIcons: {

                valid: 'glyphicon glyphicon-ok',

                invalid: 'glyphicon glyphicon-remove',

                validating: 'glyphicon glyphicon-refresh'

            },

            fields: {

				currency: {

					validators: {

						notEmpty: {

							message: param.req

						}

					}

				},

				bank_country: {

					validators: {

						notEmpty: {

							message: param.req

						}

					}

				},

				routing_number: {

					validators: {

						notEmpty: {

							message: param.req

						}

					}

				},

				account_number: {

					validators: {

						notEmpty: {

							message: param.req

						}

					}

				},

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

			  "action": "update_stripe_external_account",

			  "user_id": user_id

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

						jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.stripe-external-account" );

						window.setTimeout(function(){

							window.location.href = data['redirect'];

						}, 2000);

								

					}else if(data['status'] == 'error'){

						jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.stripe-external-account" );

					}

				

				}



			});

			

    });

	

	/*stripe connect custom account update with extrnal bank account details*/

	jQuery('.paypal-masspay')

    .bootstrapValidator({

            message: param.not_valid,

            feedbackIcons: {

                valid: 'glyphicon glyphicon-ok',

                invalid: 'glyphicon glyphicon-remove',

                validating: 'glyphicon glyphicon-refresh'

            },

            fields: {

				paypal_email_id: {

					validators: {

						notEmpty: {

							message: param.req

						},

						emailAddress: {

							message: param.signup_user_email

						}

					}

				}

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

			  "action": "update_masspay_email",

			  "user_id": user_id

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

						jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.paypal-masspay" );

					}else if(data['status'] == 'error'){

						jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.paypal-masspay" );

					}

				

				}



			});

			

    });

		

  });