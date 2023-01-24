/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/
	
//Load services for editing
  jQuery('body').on('click', '.editServiceButton', function(){
															
		jQuery('.loading-area').show();												


        // Get the record's ID via attribute

        var serviceid = jQuery(this).attr('data-id');

		

		var data = {

			  "action": "load_service",

			  "serviceid": serviceid

			};

			

	  var formdata = jQuery.param(data);

	  

	  jQuery.ajax({



						type: 'POST',



						url: ajaxurl,



						data: formdata,

						

						dataType: "json",



						success:function (data, textStatus) {

							// Populate the form fields with the data returned from server

							jQuery('#editservice')

								.find('[name="service_name"]').val(data['service_name']).end()

								.find('[name="cost_type"][value="'+data['cost_type']+'"]').prop('checked', true).end()

								.find('[name="service_cost"]').val(data['cost']).end()

								.find('[name="serviceid"]').val(serviceid).end()
								
								.find('[name="service_hours"]').val(data['hours']).end()
								
								.find('[name="service_persons"]').val(data['persons']).end()
								
								.find('[name="service_days"]').val(data['days']).end()
								
								.find('[name="monday_status"]').bootstrapToggle(data['days_availability']['monday']['day_status']).end()
								.find('[name="tuesday_status"]').bootstrapToggle(data['days_availability']['tuesday']['day_status']).end()
								.find('[name="wednesday_status"]').bootstrapToggle(data['days_availability']['wednesday']['day_status']).end()
								.find('[name="thursday_status"]').bootstrapToggle(data['days_availability']['thursday']['day_status']).end()
								.find('[name="friday_status"]').bootstrapToggle(data['days_availability']['friday']['day_status']).end()
								.find('[name="saturday_status"]').bootstrapToggle(data['days_availability']['saturday']['day_status']).end()
								.find('[name="sunday_status"]').bootstrapToggle(data['days_availability']['sunday']['day_status']).end()
								
								.find('[name="monday_max_booking"]').val(data['days_availability']['monday']['max_booking']).end()
								.find('[name="tuesday_max_booking"]').val(data['days_availability']['tuesday']['max_booking']).end()
								.find('[name="wednesday_max_booking"]').val(data['days_availability']['wednesday']['max_booking']).end()
								.find('[name="thursday_max_booking"]').val(data['days_availability']['thursday']['max_booking']).end()
								.find('[name="friday_max_booking"]').val(data['days_availability']['friday']['max_booking']).end()
								.find('[name="saturday_max_booking"]').val(data['days_availability']['saturday']['max_booking']).end()
								.find('[name="sunday_max_booking"]').val(data['days_availability']['sunday']['max_booking']).end()
								
								.find('[name="before_padding_time"]').val(data['before_padding_time']).end()
								
								.find('[name="after_padding_time"]').val(data['after_padding_time']).end()
								
								.find('#edit_grouparea').html(data['html']).end()

								.find('[name="editdesc"]').val(data['description']).end()
								
								.find('#edit_offers').prop("checked",data['offer']).end()
								.find('[name="offer_title"]').val(data['offer_title']).end()
								.find('[name="coupon_code"]').val(data['coupon_code']).end()
								.find('[name="expiry_date"]').val(data['expiry_date']).end()
								.find('[name="max_coupon"]').val(data['max_coupon']).end()
								.find('[name="discount_type"][value="'+data['discount_type']+'"]').prop('checked', true).end()
								.find('[name="discount_value"]').val(data['discount_value']).end()
								.find('[name="edit_discount_description"]').val(data['discount_description']).end();
								
								if(data['offer'] == true){
									jQuery('#editofferson').show();	
								}else{
									jQuery('#editofferson').hide();	
								}
								
								jQuery('.sf-select-box').selectpicker('refresh');

							
							if(data['cost_type'] == 'hourly'){
								jQuery('#edit_service_persons_bx').hide();
								jQuery('#edit_service_hours_bx').show();
								jQuery('#edit_service_days_bx').hide();
								jQuery('#edit_paddingtime').show();
							}else if(data['cost_type'] == 'perperson'){
								jQuery('#edit_service_hours_bx').hide();
								jQuery('#edit_service_persons_bx').show();
								jQuery('#edit_service_days_bx').hide();
								jQuery('#edit_paddingtime').show();
							}else if(data['cost_type'] == 'days'){
								jQuery('#edit_service_days_bx').show();
								jQuery('#edit_service_hours_bx').hide();
								jQuery('#edit_service_persons_bx').hide();
								jQuery('#edit_paddingtime').hide();
							}else{
								jQuery('#edit_service_hours_bx').hide();
								jQuery('#edit_service_persons_bx').hide();
								jQuery('#edit_service_days_bx').hide();
								jQuery('#edit_paddingtime').show();
							}

							// Show the dialog

							bootbox

								.dialog({

									title: param.edit_service,

									message: jQuery('#editservice'),

									show: false // We will show it manually later

								})
								
								.on('shown.bs.modal', function() {
									
								tinymce.EditorManager.execCommand('mceAddEditor', true, "editdesc");
								tinymce.EditorManager.execCommand('mceAddEditor', true, "edit_discount_description");

									jQuery('.loading-area').hide();

									jQuery('#editservice')

										.show()                             // Show the login form

										.bootstrapValidator('resetForm'); // Reset form

								})

								.on('hide.bs.modal', function(e) {

									// Bootbox will remove the modal (including the body which contains the login form)

									// after hiding the modal

									// Therefor, we need to backup the form
									tinymce.EditorManager.execCommand('mceRemoveEditor', true, "editdesc");
									tinymce.EditorManager.execCommand('mceRemoveEditor', true, "edit_discount_description");
									jQuery('#editservice').hide().appendTo('body');

								})
								
								.on('show.bs.modal', function() {
								jQuery('body').addClass('bs-modal-open');
								})
								.on('hidden.bs.modal', function() {
								jQuery('body').removeClass('bs-modal-open');
								})

								.modal('show');

							

							

						

						}



					});



    });



// When the browser is ready...

  jQuery(function() {
	'use strict';
	var dataTable;
	
	jQuery('#addservice').on('show.bs.modal', function (event) {
		jQuery('body').addClass('bs-modal-open');
	});
	
	jQuery('#addservice').on('hidden.bs.modal', function (event) {
		jQuery('body').removeClass('bs-modal-open');
	});
	
	//Change Status for service
	jQuery('body').on('click', '.changeServiceStatus', function(){
												  
		var rid = jQuery(this).data('id');
		var status = jQuery(this).data('status');
		
		bootbox.confirm(param.change_status, function(result) {
		  if(result){
			  var data = {
			  "action": "change_service_status",
			  "serviceid": rid,
			  "status": status,
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
	

	jQuery('#addservice').on('hide.bs.modal', function() {
		jQuery('.add-new-service').bootstrapValidator('resetForm',true); // Reset form
	});
	
	jQuery('body').on('click', 'input[name="offers"]', function(){
		if(jQuery(this).is(':checked')) { 
			jQuery('#offerson').show('slow');
			jQuery('#editofferson').show('slow');
		}else{
			jQuery('#offerson').hide('slow');
			jQuery('#editofferson').hide('slow');
		}
	});
	
	var date = new Date();
	date.setDate(date.getDate()+1);
	
	jQuery('input[name="expiry_date"]').datepicker({
		format: 'yyyy-mm-dd',													
		startDate: date,
		language: langcode
	})
	.on('changeDate', function(evt) {
	}).on('hide', function(event) {
	event.preventDefault();
	event.stopPropagation();
	});
	
	//Delete Group
	jQuery('body').on('click', '.delete-group', function(){
												  
		var gid = jQuery(this).data('id');
		
		var $this = jQuery(this);
		
		bootbox.confirm(param.are_you_sure, function(result) {
		  if(result){
			  var data = {
			  "action": "delete_group",
			  "groupid": gid,
			};
			
			var data = jQuery.param(data);
			
			jQuery.ajax({

						type: 'POST',

						url: ajaxurl,
						
						data: data,
						
						dataType: "json",
						
						beforeSend: function() {
							jQuery('.loading-area').show();
							jQuery('.alert').remove();
						},

						success:function (data, textStatus) {
							jQuery('.loading-area').hide();

							if(data['status'] == 'success'){

								$this.closest('li').remove();
								
								jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.add-new-group .modal-body" );

										

							}else if(data['status'] == 'error'){

								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.add-new-group .modal-body" );

							}
						}

					});
			  }
		}) 
		.on('hidden.bs.modal', function(e) {
			jQuery('body').addClass('modal-open');
		});
		
    });
	

	

	// Add New service

	jQuery('.add-new-service')

        .bootstrapValidator({

            message: param.not_valid,

            feedbackIcons: {

                valid: 'glyphicon glyphicon-ok',

                invalid: 'glyphicon glyphicon-remove',

                validating: 'glyphicon glyphicon-refresh'

            },

            fields: {

				service_name: {

					validators: {

						notEmpty: {

							message: param.service_name

						}

					}

				},
				
				service_cost: {

					validators: {

						notEmpty: {

							message: param.req

						},
						numeric: {message: param.only_numeric},
					}

				},
				
				service_hours: {

					validators: {

						numeric: {message: param.only_numeric},

					}

				},
				
				service_persons: {

					validators: {

						digits: {message: param.only_digits},

					}

				},
				
				service_days: {

					validators: {

						digits: {message: param.only_digits},

					}

				},
				
				group_name: {

					validators: {

						notEmpty: {

							message: param.group_req

						}

					}

				},

				description: {

					validators: {

						notEmpty: {

							message: param.req

						}

					}

	            },

            }

        })

		
		.on('change', 'input[name="cost_type"]', function() {
			var ctype = jQuery(this).val();
			if(ctype == 'hourly'){
				jQuery('#service_persons_bx').hide();
				jQuery('#service_hours_bx').show();
				jQuery('#service_days_bx').hide();
				jQuery('#paddingtime').show();
			}else if(ctype == 'perperson'){
				jQuery('#service_hours_bx').hide();
				jQuery('#service_persons_bx').show();
				jQuery('#service_days_bx').hide();
				jQuery('#paddingtime').show();
			}else if(ctype == 'days'){
				jQuery('#service_days_bx').show();
				jQuery('#service_hours_bx').hide();
				jQuery('#service_persons_bx').hide();
				jQuery('#paddingtime').hide();
			}else{
				jQuery('#service_hours_bx').hide();
				jQuery('#service_persons_bx').hide();
				jQuery('#service_days_bx').hide();
				jQuery('#paddingtime').show();
			}
			
		})
		
		.on('click', '.togglenewgroup', function() {
			jQuery('.service_group_bx').toggle();
			jQuery('input[name="group_name"]').val('');
		})
		.on('click', '.addnewgroup', function() {
			
			var group_name = jQuery('input[name="group_name"]').val();
			
			if(group_name == "" || group_name == undefined){
				jQuery('.add-new-service').bootstrapValidator('revalidateField', 'group_name');
				return false;
			}
			
			var data = {
			  "action": "add_new_group",
			  "group_name": group_name,
			  "user_id": user_id
			};
			
			var data = jQuery.param(data);
			
			jQuery.ajax({

				type: 'POST',

				url: ajaxurl,
				
				data: data,
				
				dataType: "json",
				
				beforeSend: function() {
					jQuery('.loading-area').show();
					jQuery('.alert').remove();
				},

				success:function (data, textStatus) {
					jQuery('.loading-area').hide();
					
					if(data['status'] == 'success'){

						jQuery('.service_group_bx').toggle();
			
						jQuery('#grouparea').html(data['html']);
						
						jQuery('input[name="group_name"]').val('');
						
						jQuery('.sf-group-list').append(data['list']);
						
						jQuery('#grouparea').fadeOut( 1000, function() {
							jQuery('#grouparea').css("padding", '5px');
							jQuery('#grouparea').css("background-color", '#FFFF00');
						});
						jQuery('#grouparea').fadeIn( 1000, function() {
							jQuery('#grouparea').css("padding", '0px');																		
							jQuery('#grouparea').css("background-color", '#fff');
						});
						
						jQuery('.sf-select-box').selectpicker('refresh');
						
					}else if(data['status'] == 'error'){

						jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "input[name='group_name']" );

					}
				}

			});
		})

        .on('success.form.bv', function(form) {

            // Prevent form submission

			tinyMCE.triggerSave();

            form.preventDefault();

			var gname = jQuery('.add-new-service select[name="group_id"] option:selected').text();

            // Get the form instance

            var $form = jQuery(form.target);

            // Get the BootstrapValidator instance

            var bv = $form.data('bootstrapValidator');

			

			var data = {

			  "action": "add_new_service",
			  "user_id": user_id,
			  "gname": gname

			};

			

			var formdata = jQuery($form).serialize() + "&" + jQuery.param(data);

			

			jQuery.ajax({



						type: 'POST',



						url: ajaxurl,

						

						dataType: "json",

						

						beforeSend: function() {

							jQuery(".success").remove();

							jQuery(".error").remove();

							jQuery('.loading-area').show();

						},

						

						data: formdata,



						success:function (data, textStatus) {

							jQuery('.loading-area').hide();

							if(data['status'] == 'success'){

								jQuery("#service_name").val('');

								jQuery("#service_cost").val('');
								
								jQuery("#service_hours").val('');
								
								jQuery('.add-new-service input[name="monday_status"]').bootstrapToggle(true);
								jQuery('.add-new-service input[name="tuesday_status"]').bootstrapToggle(true);
								jQuery('.add-new-service input[name="wednesday_status"]').bootstrapToggle(true);
								jQuery('.add-new-service input[name="thursday_status"]').bootstrapToggle(true);
								jQuery('.add-new-service input[name="friday_status"]').bootstrapToggle(true);
								jQuery('.add-new-service input[name="saturday_status"]').bootstrapToggle(true);
								jQuery('.add-new-service input[name="sunday_status"]').bootstrapToggle(true);
								
								jQuery('.add-new-service input[name="monday_max_booking"]').val('1');
								jQuery('.add-new-service input[name="tuesday_max_booking"]').val('1');
								jQuery('.add-new-service input[name="wednesday_max_booking"]').val('1');
								jQuery('.add-new-service input[name="thursday_max_booking"]').val('1');
								jQuery('.add-new-service input[name="friday_max_booking"]').val('1');
								jQuery('.add-new-service input[name="saturday_max_booking"]').val('1');
								jQuery('.add-new-service input[name="sunday_max_booking"]').val('1');
								
								jQuery("#offer_title").val('');
								
								jQuery("#offer_title").val('');
								jQuery("#coupon_code").val('');
								jQuery("#expiry_date").val('');
								jQuery("#max_coupon").val('');
								jQuery("#discount_value").val('');
								jQuery("#offerson").hide();
								jQuery("#offers").prop("checked",false);

								tinyMCE.activeEditor.setContent('');

								/*Close the popup window*/

								jQuery('#addservice').modal('hide');

								
								
								

								/*Reaload datatable after add new service*/

								dataTable.ajax.reload(null, false);

										

							}else if(data['status'] == 'error'){

								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.add-new-service" );

							}

							

							

						

						}



					});

			

        });
		
	jQuery('#addservice').on('hide.bs.modal', function() {
		
			jQuery("#service_name").val('');
			jQuery("#service_cost").val('');
			jQuery("#service_hours").val('');
			jQuery("#description").val('');
			
			jQuery('.add-new-service input[name="monday_status"]').bootstrapToggle(true);
			jQuery('.add-new-service input[name="tuesday_status"]').bootstrapToggle(true);
			jQuery('.add-new-service input[name="wednesday_status"]').bootstrapToggle(true);
			jQuery('.add-new-service input[name="thursday_status"]').bootstrapToggle(true);
			jQuery('.add-new-service input[name="friday_status"]').bootstrapToggle(true);
			jQuery('.add-new-service input[name="saturday_status"]').bootstrapToggle(true);
			jQuery('.add-new-service input[name="sunday_status"]').bootstrapToggle(true);
			
			jQuery('.add-new-service input[name="monday_max_booking"]').val('1');
			jQuery('.add-new-service input[name="tuesday_max_booking"]').val('1');
			jQuery('.add-new-service input[name="wednesday_max_booking"]').val('1');
			jQuery('.add-new-service input[name="thursday_max_booking"]').val('1');
			jQuery('.add-new-service input[name="friday_max_booking"]').val('1');
			jQuery('.add-new-service input[name="saturday_max_booking"]').val('1');
			jQuery('.add-new-service input[name="sunday_max_booking"]').val('1');
			
			jQuery("#offer_title").val('');
			
			jQuery("#offer_title").val('');
			jQuery("#coupon_code").val('');
			jQuery("#expiry_date").val('');
			jQuery("#max_coupon").val('');
			jQuery("#discount_value").val('');
			jQuery("#offerson").hide();
			jQuery("#offers").prop("checked",false);
			
			jQuery('.sf-select-box').selectpicker('refresh');
			tinyMCE.activeEditor.setContent('');
	});	

		
		// Add New service

	jQuery('.add-new-group')

        .bootstrapValidator({

            message: param.not_valid,

            feedbackIcons: {

                valid: 'glyphicon glyphicon-ok',

                invalid: 'glyphicon glyphicon-remove',

                validating: 'glyphicon glyphicon-refresh'

            },

            fields: {

				group_name: {

					validators: {

						notEmpty: {

							message: param.group_req

						}

					}

				},

            }

        })
        .on('success.form.bv', function(form) {

            // Prevent form submission

            form.preventDefault();
			
			jQuery('form.add-new-group').find('input[type="submit"]').prop('disabled', false);

            // Get the form instance

            var $form = jQuery(form.target);

            // Get the BootstrapValidator instance

            var bv = $form.data('bootstrapValidator');

			

			var data = {
			  "action": "add_new_group",
			  "user_id": user_id
			};

			

			var formdata = jQuery($form).serialize() + "&" + jQuery.param(data);

			

			jQuery.ajax({



						type: 'POST',



						url: ajaxurl,

						

						dataType: "json",

						

						beforeSend: function() {

							jQuery('.alert').remove();

							jQuery('.loading-area').show();

						},

						

						data: formdata,



						success:function (data, textStatus) {

							jQuery('.loading-area').hide();

							if(data['status'] == 'success'){
								
								jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.add-new-group .modal-body" );
								
								jQuery('.sf-group-list').append(data['list']);
								
								jQuery('#grouparea').html(data['html']);
								
								jQuery('.sf-select-box').selectpicker('refresh');
										

							}else if(data['status'] == 'error'){

								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.add-new-group .modal-body" );

							}

							

							

						

						}



					});

			

        });
	

	

    

	//Tabbing on My Account Page
	jQuery("#myTab a").click(function(e){
		e.preventDefault();
		jQuery(this).tab('show');
		var tabid = jQuery(this).attr('href');
		if(tabid == '#my-services'){
			if ( ! jQuery.fn.DataTable.isDataTable( '#service-grid' ) ) {
			dataTable = jQuery('#service-grid').DataTable( {

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

		data: {"action": "get_services","user_id": user_id},

		error: function(){  // error handling

			jQuery(".service-grid-error").html("");

			jQuery("#service-grid").append('<tbody class="service-grid-error"><tr><th colspan="3">'+param.no_data+'</th></tr></tbody>');

			jQuery("#service-grid_processing").css("display","none");

			

		}

	}

	} );
			jQuery('.sf-select-box').selectpicker('refresh');
			}
		}
	});

	

	// Edit service

	jQuery('.edit-service')

        .bootstrapValidator({

            message: param.not_valid,

            feedbackIcons: {

                valid: 'glyphicon glyphicon-ok',

                invalid: 'glyphicon glyphicon-remove',

                validating: 'glyphicon glyphicon-refresh'

            },
			
            fields: {

				service_name: {

					validators: {

						notEmpty: {

							message: param.service_name

						}

					}

				},
				service_cost: {

					validators: {

						notEmpty: {

							message: param.req

						},
						numeric: {message: param.only_numeric},

					}

				},
				
				service_hours: {

					validators: {

						numeric: {message: param.only_numeric},

					}

				},
				
				service_persons: {

					validators: {

						digits: {message: param.only_digits},

					}

				},
				
				service_days: {

					validators: {

						digits: {message: param.only_digits},

					}

				},
				
				group_name: {
	
					validators: {
	
						notEmpty: {
	
							message: param.group_req
	
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
		
		.on('click', '.togglenewgroup', function() {
			jQuery('.edit_service_group_bx').toggle();
		})
		.on('click', '.addnewgroup', function() {
			
			var group_name = jQuery('input[name="edit_group_name"]').val();
			
			if(group_name == "" || group_name == undefined){
				jQuery('.edit-service').bootstrapValidator('revalidateField', 'group_name');
				return false;
			}
			
			var data = {
			  "action": "add_new_group",
			  "group_name": group_name,
			  "user_id": user_id
			};
			
			var data = jQuery.param(data);
			
			jQuery.ajax({

				type: 'POST',

				url: ajaxurl,
				
				data: data,
				
				dataType: "json",
				
				beforeSend: function() {
					jQuery('.loading-area').show();
					jQuery('.alert').remove();
				},

				success:function (data, textStatus) {
					jQuery('.loading-area').hide();
					
					if(data['status'] == 'success'){

						jQuery('.service_group_bx').toggle();
						
						jQuery('input[name="edit_group_name"]').val('');
						
						jQuery('.sf-group-list').append(data['list']);
			
						jQuery('#edit_grouparea').html(data['html']);
						
						jQuery('#edit_grouparea').fadeOut( 1000, function() {
							jQuery('#edit_grouparea').css("padding", '5px');
							jQuery('#edit_grouparea').css("background-color", '#FFFF00');
						});
						jQuery('#edit_grouparea').fadeIn( 1000, function() {
							jQuery('#edit_grouparea').css("padding", '0px');																		
							jQuery('#edit_grouparea').css("background-color", '#fff');
						});
						
						jQuery('.sf-select-box').selectpicker('refresh');

					}else if(data['status'] == 'error'){

						jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "input[name='group_name']" );

					}
				}

			});
		})
		
		.on('change', 'input[name="cost_type"]', function() {
			var ctype = jQuery(this).val();
			var gethours = jQuery('#editservice #service_hours').val();
			var getpersons = jQuery('#editservice #service_persons').val();
			var getdays = jQuery('#editservice #service_days').val();
			
			
			if(ctype == 'hourly'){
				if(gethours == 0){
				jQuery('#editservice #service_hours').val('');
				}	
				jQuery('#edit_service_persons_bx').hide();
				jQuery('#edit_service_days_bx').hide();
				jQuery('#edit_service_hours_bx').show();
				jQuery('#edit_paddingtime').show();
			}else if(ctype == 'perperson'){
				if(getpersons == 0){
				jQuery('#editservice #service_persons').val('');
				}
				jQuery('#edit_service_hours_bx').hide();
				jQuery('#edit_service_days_bx').hide();
				jQuery('#edit_service_persons_bx').show();
				jQuery('#edit_paddingtime').show();
			}else if(ctype == 'days'){
				if(getdays == 0){
				jQuery('#editservice #service_days').val('');
				}
				jQuery('#edit_service_hours_bx').hide();
				jQuery('#edit_service_persons_bx').hide();
				jQuery('#edit_service_days_bx').show();
				jQuery('#edit_paddingtime').hide();
			}else{
				jQuery('#edit_service_hours_bx').hide();
				jQuery('#edit_service_persons_bx').hide();
				jQuery('#edit_service_days_bx').hide();
				jQuery('#edit_paddingtime').show();
			}
			
		})

        .on('success.form.bv', function(form) {

            // Prevent form submission

			tinyMCE.triggerSave();

            form.preventDefault();

			var gname = jQuery('.edit-service select[name="group_id"] option:selected').text();

            // Get the form instance

            var $form = jQuery(form.target);

            // Get the BootstrapValidator instance

            var bv = $form.data('bootstrapValidator');

			

			var data = {

			  "action": "edit_service",
			  "user_id": user_id,
			  "gname": gname

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

								/*Close the popup window*/

								$form.parents('.bootbox').modal('hide');

								/*Reaload datatable after add new service*/

								dataTable.ajax.reload(null, false);

										

							}else if(data['status'] == 'error'){

								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.edit-service" );

							}

							

							

						

						}



					});

			

        });

	

	jQuery("#bulkDelete").on('click',function() { // bulk checked

        var status = this.checked;

        jQuery(".deleteRow").each( function() {

            jQuery(this).prop("checked",status);

        });

    });

     

    jQuery('#deleteTriger').on("click", function(event){ // triggering delete one by one

		

			  if( jQuery('.deleteRow:checked').length > 0 ){

				  bootbox.confirm(param.are_you_sure, function(result) {

		  if(result){

				  // at-least one checkbox checked

            var ids = [];

            jQuery('.deleteRow').each(function(){

                if(jQuery(this).is(':checked')) { 

                    ids.push(jQuery(this).val());

                }

            });

            var ids_string = ids.toString();  // array to string conversion 

            jQuery.ajax({

                type: "POST",

                url: ajaxurl,

                data: {action: "delete_services", data_ids:ids_string},

                success: function(result) {

                    dataTable.draw(); // redrawing datatable

                },

                async:false

            });

        

		}

		});

		}else{

				bootbox.alert(param.select_checkbox);

		}

		   

    });

	

  });