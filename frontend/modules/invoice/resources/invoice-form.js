/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/
	var invoicedataTable = '';

// When the browser is ready...
  jQuery(function() {
	'use strict';
	
	var formID = '';
	
	if(invoicetab == "yes"){
		load_invoice_datatable('#invoice');
		view_invoice_details(viewinvoiceid);
	}
	
	//View Bookings
	jQuery('body').on('click', '.viewbookingdeatils', function(){
												  
		jQuery('#invoice-grid_wrapper').addClass('hidden');
		jQuery('#invoice-booking-details').removeClass('hidden');
		
		
		
		var bid = jQuery(this).attr('data-id');
		var upcoming = jQuery(this).attr('data-upcoming');
		if(upcoming == 'yes'){
			var flag = 1;	
		}else{
			var flag = 0;	
		}
		
		var data = {
			  "action": "booking_details",
			  "bookingid": bid,
			  "flag": flag,
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
							
							jQuery('#invoice-booking-details').html(data);
							
							jQuery('.display-ratings').rating();
							jQuery('.sf-show-rating').show();
						}

					});
    });
	
	//Close details
	jQuery('body').on('click', '.closeDetails', function(){
		jQuery('#invoice-grid_wrapper').removeClass('hidden');
		
		jQuery('#invoice-booking-details').addClass('hidden fade in');
	});
	
	//Hide Event on invoice modal popup box
	jQuery('#invoice-Modal').on('hide.bs.modal', function() {
		jQuery('#addinvoice').bootstrapValidator('resetForm',true); // Reset form
		servicetotal = 0;
		discount = 0;
		tax = 0;
		total = 0;
		grand_total = 0;
		jQuery(".alert-success,.alert-danger").remove();
		jQuery('.add-invoice input[name="refno"]').val('');
		jQuery('.add-invoice input[name="dueDate"]').val('');
		jQuery('.add-invoice select[name="customer"]').val('');
		jQuery('.add-invoice select[name="status"]').val('');
		jQuery('.add-invoice input[name="discount"]').val('');
		jQuery('.add-invoice input[name="tax"]').val('');
		jQuery('.add-invoice #total_amount').html(currencysymbol+'0.00');
		jQuery('.add-invoice #total_discount').html(currencysymbol+'0.00');
		jQuery('.add-invoice #total_tax').html(currencysymbol+'0.00');
		jQuery('.add-invoice #grand_total').html(currencysymbol+'0.00');
		jQuery('.add-invoice textarea[name="short-desc"]').val('');
		
		jQuery('.add-invoice select[name="service_title[0]"]').val('');
		jQuery('.add-invoice input[name="service_desc[0]"]').val('');
		jQuery('.add-invoice input[name="service_price[0]"]').val('');
		jQuery('.add-invoice .additional-element').remove();
		jQuery('.add-invoice .num-hours').hide('');
		jQuery('.sf-select-box').selectpicker('refresh');
		
		jQuery('.add-invoice select[name="customer"]').parent('div').removeClass('has-error');
		jQuery('.add-invoice select[name="status"]').parent('div').removeClass('has-error');
	});
	
	var servicetotal = 0;
	var discount = 0;
	var tax = 0;
	var total = 0;
	var grand_total = 0;
	var temprice = 0;
	var invoiceid = '';
	var serviceEditIndex = '';
	var customer_flag = 1;
	var status_flag = 1;
	
	//Show invoice modal popup box
	jQuery('#invoice-Modal').on('show.bs.modal', function (e) {
	  formID = jQuery(this).find('form').attr('id');
	});

	//Edit invoice	
	jQuery('body').on('click', '.editInvoice', function(){
	jQuery('.loading-area').show();													
	 invoiceid = jQuery(this).attr('data-id');
	 
	 formID = 'editInvoice';
		
	 var data = {
			  "action": "edit_invoice",
			  "invoiceid": invoiceid,
			  "user_id": user_id
			};
			
	  var formdata = jQuery.param(data);
	  
	  jQuery.ajax({

						type: 'POST',

						url: ajaxurl,

						data: formdata,
						
						dataType: "json",

						success:function (data, textStatus) {
							
							jQuery('#editInvoice')
								.find('[name="refno"]').val(data['reference_no']).end()
								.find('[name="customer"]').val(data['customer_email']).end()
								.find('[name="status"]').val(data['status']).end()
								.find('[name="dueDate"]').val(data['duedate']).end()
								.find('[name="discount-type"][value="'+data['discount_type']+'"]').prop('checked', true).end()
								.find('[name="tax-type"][value="'+data['tax_type']+'"]').prop('checked', true).end()
								.find('[name="tax"]').val(data['tax']).end()
								.find('[name="discount"]').val(data['discount']).end()
								.find('#editservicedata').html(data['services']).end()
								.find('#total_amount').html(currencysymbol+data['total']).end()
								.find('#total_discount').html(currencysymbol+data['discountamount']).end()
								.find('#total_tax').html(currencysymbol+data['taxamount']).end()
								.find('textarea[name="short-desc"]').val(data['description']).end()
								.find('#grand_total').html(currencysymbol+data['grand_total']).end();
								serviceEditIndex = data['serviceskey'];
								
								total = data['total'];
								grand_total = data['grand_total'];
								
								jQuery('.sf-select-box').selectpicker('refresh');
								
								jQuery("#editservicedata").find('.num_hours_al').TouchSpin({
								  verticalbuttons: true,
								  verticalupclass: 'glyphicon glyphicon-plus',
								  verticaldownclass: 'glyphicon glyphicon-minus',
								   min: 1,
									max: 12
								}).on('change', function() {
            // Revalidate the date field
				var nmhrs = jQuery(this).val();
				var indexVal = jQuery(this).attr('data-index');						
				temprice = jQuery('input[name="service_price[' + indexVal + ']"]').val();						
				service_finder_calculateTotal(formID);
        });
							// Show the dialog
							bootbox
								.dialog({
									title: param.edit_invoice,
									message: jQuery('#editInvoice'),
									show: false // We will show it manually later
								})
								.on('shown.bs.modal', function() {
									jQuery('.loading-area').hide();
									jQuery('#editInvoice')
										.show()                             // Show the login form
										.bootstrapValidator('resetForm'); // Reset form
									jQuery('.bootbox').find('.modal-dialog').addClass('modal-xlg');
									jQuery('.bootbox').find('.modal-body').addClass('clearfix row');											
								})
								.on('hide.bs.modal', function(e) {
									// Bootbox will remove the modal (including the body which contains the login form)
									// after hiding the modal
									// Therefor, we need to backup the form
									jQuery('#editInvoice').hide().appendTo('body');
								})
								.modal('show');
							
							
						
						}

					});														
											   
	});				  
	
	//Tabbing on My Account Page
	jQuery("#myTab a").click(function(e){
		e.preventDefault();
		jQuery(this).tab('show');
		var tabid = jQuery(this).attr('href');
		load_invoice_datatable(tabid);
	});
	
	function load_invoice_datatable(tabid){
		if(tabid == '#invoice'){
			if ( ! jQuery.fn.DataTable.isDataTable( '#invoice-grid' ) ) {
			invoicedataTable = jQuery('#invoice-grid').DataTable( {
	"serverSide": true,
	"bAutoWidth": false,
	"order": [[ 0, "desc" ]],
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
		data: {"action": "get_invoice","user_id": user_id},
		error: function(){  // error handling
			jQuery(".invoice-grid-error").html("");
			jQuery("#invoice-grid").append('<tbody class="invoice-grid-error"><tr><th colspan="3">'+param.not_valid+'</th></tr></tbody>');
			jQuery("#invoice-grid_processing").css("display","none");
			
		}
	}
	} );
			jQuery('.sf-select-box').selectpicker('refresh');
			}
		}
	}
	
	
	//Bulk Invoice Delete
	jQuery("#bulkInvoiceDelete").on('click',function() { // bulk checked
        var status = this.checked;
        jQuery(".deleteInvoiceRow").each( function() {
            jQuery(this).prop("checked",status);
        });
    });
	
	
     
    //Single invoice Delete
	jQuery('#deleteInvoiceTriger').on("click", function(event){ // triggering delete one by one
        
			  if( jQuery('.deleteInvoiceRow:checked').length > 0 ){
				  bootbox.confirm(param.are_you_sure, function(result) {
		  if(result){
				  // at-least one checkbox checked
            var ids = [];
            jQuery('.deleteInvoiceRow').each(function(){
                if(jQuery(this).is(':checked')) { 
                    ids.push(jQuery(this).val());
                }
            });
            var ids_string = ids.toString();  // array to string conversion 
            jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data: {action: "delete_invoice", data_ids:ids_string},
                success: function(result) {
                    invoicedataTable.draw(); // redrawing invoicedataTable
                },
                async:false
            });
        
		}
		}); 
		}else{
				bootbox.alert(param.select_checkbox);
		}
		  
		
    });
	
	//View Invoice
	jQuery('body').on('click', '.viewInvoice', function(){
												  
		var invoiceid = jQuery(this).attr('data-id');
		view_invoice_details(invoiceid);
    });
	
	function view_invoice_details(invoiceid){
		jQuery('#invoice-grid_wrapper').addClass('hidden');
		jQuery('#invoice-details').removeClass('hidden');
		
		var data = {
			  "action": "invoice_details",
			  "invoiceid": invoiceid
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
							
							jQuery('#invoice-details').html(data);
						}

					});	
	}
	
	//Send Reminder
	 jQuery('body').on('click', '.sendReminder', function(){
			invoiceid = jQuery(this).attr('data-id');
			bootbox.dialog({
                title: param.reminder_mail,
                message: '<div class="row">  ' +
                    '<div class="col-md-12"> ' +
                    '<form class="form-horizontal send-reminder-form"> ' +
                    '<div class=""> ' +
                    '<label class="control-label" for="name">'+param.comment_text+'</label> ' +
                    '<div class="reminder-text-area"> ' +
                    '<textarea class="form-control sf-form-control input-md" name="comment" id="comment"></textarea>' +
                    '</div> ' +
					
					'<div class="reminder-btn"> ' +
                    '<div class=""> ' +
                    '<input name="submit" type="submit" class="btn btn-success" value="'+param.send_reminder+'"> ' +
                    '</div> ' +
					
                    '</div> ' +
                    '</form> </div>  </div>',
                buttons: {
                    success: {
                        label: param.cancel,
                        className: "btn-danger",
                        callback: function () {
                        }
                    }
                }
            })
			.on('shown.bs.modal',function () {
                jQuery('.send-reminder-form')
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
							.on('success.form.bv', function(form) {
								// Prevent form submission
								form.preventDefault();	

								var $form = jQuery(form.target);
								// Get the BootstrapValidator instance
								var bv = $form.data('bootstrapValidator');
								
								var data = {
								  "action": "send_reminder",
								  "invoiceid": invoiceid
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
													jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.send-reminder-form" );	
													window.setTimeout(function(){
														bootbox.hideAll();
													}, 2000); // 2 seconds expressed in milliseconds
													
													/*Reaload datatable after add new service*/
													invoicedataTable.ajax.reload(null, false);
															
												}else if(data['status'] == 'error'){
													jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.send-reminder-form" );
													
												}
												
												
											
											}
					
										});
								
							});	

            });
	 });	
	//Close invoice details section
	jQuery('body').on('click', '.closeInvoiceDetails', function(){
		jQuery('#invoice-details').addClass('hidden fade in');
		jQuery('#invoice-grid_wrapper').removeClass('hidden');
	});

	
	//TouchSpin Script for max min number in textbox
	/*jQuery("input[name='num_hours[0]']").TouchSpin({
      verticalbuttons: true,
      verticalupclass: 'glyphicon glyphicon-plus',
      verticaldownclass: 'glyphicon glyphicon-minus',
	   min: 1,
        max: 12
    });	*/
	
	
	
				jQuery('.gen_ref').click(function(){
					jQuery(this).parent('.input-group').children('input').val(service_finder_getRandomRef());
					jQuery(this).parent('.input-group').children('input').change();
					jQuery('.add-invoice').bootstrapValidator('revalidateField', jQuery(this).parent('.input-group').children('input'));
				});
				function service_finder_getRandomRef(){var min=1000000000000000,max=9999999999999999;return Math.floor(Math.random()*(max- min+ 1))+ min;}
				
				var date = new Date();
                date.setDate(date.getDate());
				
				jQuery('.invoicedueDatePicker').datepicker({
					format: dateformat,													
					startDate: date,
					language: langcode
				})
				.on('changeDate', function(evt) {
					// Revalidate the date field
					
					jQuery('.add-invoice').bootstrapValidator('revalidateField', jQuery(this).find('[name="dueDate"]'));
					jQuery('.edit-invoice').bootstrapValidator('revalidateField', jQuery(this).find('[name="dueDate"]'));
				}).on('hide', function(event) {
				event.preventDefault();
				event.stopPropagation();
				});
				
	jQuery('.form-group').on('click', 'input[type=radio]', function(){
		service_finder_calculateTotal(formID);
	});
	
	jQuery('.form-group').on('change', 'input[name=tax]', function(){
		service_finder_calculateTotal(formID);
	});
	
	jQuery('.form-group').on('change', 'input[name=discount]', function(){
		service_finder_calculateTotal(formID);
	});
	
	jQuery('.form-group').on('change', '.col-xs-2 input[type=text]', function(){
		service_finder_calculateTotal(formID);
	});
	
	
	var descValidators = {
            row: '.col-xs-3',   // The title is placed inside a <div class="col-xs-4"> element
            validators: {
                notEmpty: {
                    message: param.desc_req
                }
            }
        };
     var priceValidators = {
            row: '.col-xs-2',
		    trigger: 'change keyup',
            validators: {
                notEmpty: {
                    message: param.price
                },
            }
        };
    
	var serviceIndex = 0;
   	jQuery('.add-invoice')
        .bootstrapValidator({
            message: param.not_valid,
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
				refno: {
					validators: {
						notEmpty: {
						}
					}
				},
				dueDate: {
                    validators: {
                        notEmpty: {
                            message: param.due_date
                        },
                    }
                },
				
				'service_desc[0]': descValidators,
                'service_price[0]': priceValidators
            }
        })
		.on('click',  'input[type="submit"]', function(e) {
            if(jQuery('.add-invoice select[name="customer"] option:selected').val()==""){customer_flag = 1;jQuery('.add-invoice select[name="customer"]').parent('div').addClass('has-error').removeClass('has-success'); jQuery('form.add-invoice').find('input[type="submit"]').prop('disabled', false);}else{customer_flag = 0;jQuery('.add-invoice select[name="customer"]').parent('div').removeClass('has-error').addClass('has-success'); jQuery('form.add-invoice').find('input[type="submit"]').prop('disabled', false);}
			 if(jQuery('.add-invoice select[name="status"] option:selected').val()==""){status_flag = 1;jQuery('.add-invoice select[name="status"]').parent('div').addClass('has-error').removeClass('has-success'); jQuery('form.add-invoice').find('input[type="submit"]').prop('disabled', false);}else{status_flag = 0;jQuery('.add-invoice select[name="status"]').parent('div').removeClass('has-error').addClass('has-success'); jQuery('form.add-invoice').find('input[type="submit"]').prop('disabled', false);}
	    })
		.on('error.field.bv', function(e, data) {
            data.bv.disableSubmitButtons(false); // disable submit buttons on errors
	    })
		.on('status.field.bv', function(e, data) {
            data.bv.disableSubmitButtons(false); // disable submit buttons on valid
        })
		// Add button click handler
        .on('click', '.addButton', function() {
			serviceIndex++;
            var $template = jQuery('#serviceTemplate'),
                $clone    = $template
                                .clone()
                                .addClass('additional-element')
								.removeClass('hide')
                                .removeAttr('id')
                                .attr('data-service-index', serviceIndex)
                                .insertBefore($template);
								$clone.find('.bootstrap-select').remove();
        $clone.find('select').selectpicker().change(function(e) {
                
				var sid = jQuery(this).val();
				var indexVal = jQuery(this).attr('data-index');
				jQuery('input[name="cost_type[' + indexVal + ']"]').closest('div.form-group').find('.num-hours [type="text"]').val(1);	
				var data = {
					  "action": "getServiceDetails",
					  "serviceid": sid,
					};
					
				  var formdata = jQuery.param(data);
				  
				  jQuery.ajax({
			
									type: 'POST',
			
									url: ajaxurl,
			
									data: formdata,
									
									dataType: "json",
									
									success:function (data, textStatus) {
										
										if(data['status'] == 'success'){
											jQuery('input[name="service_price[' + indexVal + ']"]').val(data['cost']);
											jQuery('input[name="service_price[' + indexVal + ']"]').change();
											if(data['cost_type'] == 'hourly'){
												jQuery('input[name="cost_type[' + indexVal + ']"][value="hourly"]').prop('checked', true);
												jQuery('input[name="cost_type[' + indexVal + ']"]').closest('div.form-group').find('.num-hours').show();
											}else if(data['cost_type'] == 'fixed'){
												jQuery('input[name="cost_type[' + indexVal + ']"][value="fix"]').prop('checked', true);
												jQuery('input[name="cost_type[' + indexVal + ']"]').closest('div.form-group').find('.num-hours').hide();
											}
											
											service_finder_calculateTotal(formID);
											
										}
		
									}
			
								});
				
            });
		
		$clone.find('[type="radio"]').change(function(e) {
			var ctype = jQuery(this).val();
			var indexVal = jQuery(this).attr('data-index');
			if(ctype == 'hourly'){
				jQuery(this).closest('div.form-group').find('.num-hours').show();
			}else{
				jQuery(this).closest('div.form-group').find('.num-hours').hide();
			}
		});
		
		
		  
		   $clone.find('.col-xs-2 input[type=text]').change(function(e) {
					service_finder_calculateTotal(formID);											
			});
		   
		   

            // Update the name attributes
            $clone
                .find('[name="service_title"]').attr('name', 'service_title[' + serviceIndex + ']').attr('data-index', serviceIndex ).end()
                .find('[name="cost_type"][value="fix"]').attr('name', 'cost_type[' + serviceIndex + ']').attr('data-index', serviceIndex ).attr('id', 'fix-price[' + serviceIndex + ']' ).append('<label for="fix-price[' + serviceIndex + ']">Fix</label>').end()
				.find('[name="cost_type"][value="hourly"]').attr('name', 'cost_type[' + serviceIndex + ']').attr('data-index', serviceIndex ).attr('id', 'hourly-price[' + serviceIndex + ']' ).end()
				.find('label[for="fix-price"]').attr('for', 'fix-price[' + serviceIndex + ']').end()
				.find('label[for="hourly-price"]').attr('for', 'hourly-price[' + serviceIndex + ']').end()
				.find('[name="num_hours"]').attr('name', 'num_hours[' + serviceIndex + ']').attr('data-index', serviceIndex ).end()
                .find('[name="service_desc"]').attr('name', 'service_desc[' + serviceIndex + ']').attr('data-index', serviceIndex ).end()
				.find('[name="service_price"]').attr('name', 'service_price[' + serviceIndex + ']').attr('data-index', serviceIndex ).end();
				
				 $clone.find('.num_hours2').TouchSpin({
                verticalbuttons: true,
      verticalupclass: 'glyphicon glyphicon-plus',
      verticaldownclass: 'glyphicon glyphicon-minus',
	  min: 1,
        max: 12
            }).on('change', function() {
            // Revalidate the date field
				var nmhrs = jQuery(this).val();
				var indexVal = jQuery(this).attr('data-index');						
				temprice = jQuery('input[name="service_price[' + indexVal + ']"]').val();						
				service_finder_calculateTotal(formID);
        });

            // Add new fields
            // Note that we also pass the validator rules for new field as the third parameter
            jQuery('.add-invoice')
                .bootstrapValidator('addField', 'service_desc[' + serviceIndex + ']', descValidators)
				.bootstrapValidator('addField', 'service_price[' + serviceIndex + ']', priceValidators);
        })
		.on('change', '[name="cost_type[0]"]', function() {
			var ctype = jQuery(this).val();
			var indexVal = jQuery(this).attr('data-index');
			if(ctype == 'hourly'){
				jQuery(this).closest('div.form-group').find('.num-hours').show();
			}else{
				jQuery(this).closest('div.form-group').find('.num-hours').hide();
			}
		})
		.on('change', '[name="service_title[0]"]', function() {
				var sid = jQuery(this).val();
				var indexVal = jQuery(this).attr('data-index');
				var data = {
					  "action": "getServiceDetails",
					  "serviceid": sid,
					};
					jQuery('input[name="cost_type[' + indexVal + ']"]').closest('div.form-group').find('.num-hours [type="text"]').val(1);	
				  var formdata = jQuery.param(data);
				  
				  jQuery.ajax({
			
									type: 'POST',
			
									url: ajaxurl,
			
									data: formdata,
									
									dataType: "json",
									
									success:function (data, textStatus) {
										
										if(data['status'] == 'success'){
											jQuery('input[name="service_price[' + indexVal + ']"]').val(data['cost']);
											jQuery('input[name="service_price[' + indexVal + ']"]').change();
											
											if(data['cost_type'] == 'hourly'){
												jQuery('input[name="cost_type[' + indexVal + ']"][value="hourly"]').prop('checked', true);
												jQuery('input[name="cost_type[' + indexVal + ']"]').closest('div.form-group').find('.num-hours').show();
											}else if(data['cost_type'] == 'fixed'){
												jQuery('input[name="cost_type[' + indexVal + ']"][value="fix"]').prop('checked', true);
												jQuery('input[name="cost_type[' + indexVal + ']"]').closest('div.form-group').find('.num-hours').hide();
											}
											
											
											service_finder_calculateTotal(formID);
											
										}
		
									}
			
								});							   
		})
		
		.on('change', '[name="num_hours[0]"]', function() {
																		
				var nmhrs = jQuery(this).val();
				var indexVal = jQuery(this).attr('data-index');						
				temprice = jQuery('input[name="service_price[' + indexVal + ']"]').val();						
				service_finder_calculateTotal(formID);
		})
		
		// Remove button click handler
        .on('click', '.removeButton', function() {
            var $row  = jQuery(this).parents('.form-group'),
                index = $row.attr('data-service-index');

            // Remove fields
            jQuery('.add-invoice')
                .bootstrapValidator('removeField', $row.find('[name="service_desc[' + index + ']"]'))
                .bootstrapValidator('removeField', $row.find('[name="service_price[' + index + ']"]'));

            // Remove element containing the fields
            $row.remove();
			service_finder_calculateTotal(formID);
        })
        .on('success.form.bv', function(form) {
			jQuery('form.add-invoice').find('input[type="submit"]').prop('disabled', false);										
            // Prevent form submission
            form.preventDefault();
			
			if(customer_flag==1 || status_flag==1){return false;}
			jQuery('#serviceTemplate').remove();
			// Get the form instance
            var $form = jQuery(form.target);
            // Get the BootstrapValidator instance
            var bv = $form.data('bootstrapValidator');
			
			var data = {
			  "action": "add_invoice",
			  "total": total,
			  "gtotal": grand_total,
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
								jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.add-invoice" );	

								window.setTimeout(function(){
									/*Close the popup window*/
									jQuery('#invoice-Modal').modal('hide');
								}, 1000); // 1 seconds expressed in milliseconds
								
								/*Reaload datatable after add new service*/
								invoicedataTable.ajax.reload(null, false);
							}else if(data['status'] == 'error'){
								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.add-invoice" );
							}
							
						}

					});
			
        });
		
	
	
	
	
	
	
	
	
	
	
	
	
	
	/*Update Invoice*/
	
	jQuery('.edit-invoice')
        .bootstrapValidator({
            message: param.not_valid,
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
				refno: {
					validators: {
						notEmpty: {
						}
					}
				},
				dueDate: {
                    validators: {
                        notEmpty: {
                            message: param.due_date
                        },
                    }
                },
			
				'service_desc[0]': descValidators,
                'service_price[0]': priceValidators
            }
        })
	
		.on('error.field.bv', function(e, data) {
            data.bv.disableSubmitButtons(false); // disable submit buttons on errors
	    })
		.on('status.field.bv', function(e, data) {
            data.bv.disableSubmitButtons(false); // disable submit buttons on valid
        })
		// Add button click handler
        .on('click', '.addButton', function() {
			serviceEditIndex++;
			
            var $template = jQuery('#serviceEditTemplate'),
                $clone    = $template
                                .clone()
                                .removeClass('hide')
                                .removeAttr('id')
                                .attr('data-service-index', serviceEditIndex)
                                .insertBefore($template);
								$clone.find('.bootstrap-select').remove();
        $clone.find('select').selectpicker().change(function(e) {
                
				var sid = jQuery(this).val();
				var indexVal = jQuery(this).attr('data-index');
				var data = {
					  "action": "getServiceDetails",
					  "serviceid": sid,
					};
					
				  var formdata = jQuery.param(data);
				  
				  jQuery.ajax({
			
									type: 'POST',
			
									url: ajaxurl,
			
									data: formdata,
									
									dataType: "json",
									
									success:function (data, textStatus) {
										
										if(data['status'] == 'success'){
											
											jQuery('input[name="service_price[' + indexVal + ']"]').val(data['cost']);
											jQuery('input[name="service_price[' + indexVal + ']"]').change();
											if(data['cost_type'] == 'hourly'){
												jQuery('input[name="cost_type[' + indexVal + ']"][value="hourly"]').prop('checked', true);
												jQuery('input[name="cost_type[' + indexVal + ']"]').closest('div.form-group').find('.num-hours').show();
											}else if(data['cost_type'] == 'fixed'){
												jQuery('input[name="cost_type[' + indexVal + ']"][value="fix"]').prop('checked', true);
												jQuery('input[name="cost_type[' + indexVal + ']"]').closest('div.form-group').find('.num-hours').hide();
											}
											service_finder_calculateTotal(formID);
											
										}
		
									}
			
								});
				
            });
		
		$clone.find('[type="radio"]').change(function(e) {
			var ctype = jQuery(this).val();
			var indexVal = jQuery(this).attr('data-index');
			if(ctype == 'hourly'){
				jQuery(this).closest('div.form-group').find('.num-hours').show();
			}else{
				jQuery(this).closest('div.form-group').find('.num-hours').hide();
			}
		});
		
		   
		   $clone.find('.col-xs-2 input[type=text]').change(function(e) {
					service_finder_calculateTotal(formID);											
			});
            // Update the name attributes
            $clone
                .find('[name="service_title"]').attr('name', 'service_title[' + serviceEditIndex + ']').attr('data-index', serviceEditIndex ).end()
                .find('[name="cost_type"][value="fix"]').attr('name', 'cost_type[' + serviceEditIndex + ']').attr('data-index', serviceEditIndex ).attr('id', 'editfix-price[' + serviceEditIndex + ']' ).end()
				.find('[name="cost_type"][value="hourly"]').attr('name', 'cost_type[' + serviceEditIndex + ']').attr('data-index', serviceEditIndex ).attr('id', 'edithourly-price[' + serviceEditIndex + ']' ).end()
				.find('label[for="editfix-price"]').attr('for', 'editfix-price[' + serviceEditIndex + ']').attr('id', 'editfix-price[' + serviceIndex + ']' ).end()
				.find('label[for="edithourly-price"]').attr('for', 'edithourly-price[' + serviceEditIndex + ']').attr('id', 'edithourly-price[' + serviceIndex + ']' ).end()
				.find('[name="num_hours"]').attr('name', 'num_hours[' + serviceEditIndex + ']').attr('data-index', serviceEditIndex ).end()
                .find('[name="service_desc"]').attr('name', 'service_desc[' + serviceEditIndex + ']').attr('data-index', serviceEditIndex ).end()
				.find('[name="service_price"]').attr('name', 'service_price[' + serviceEditIndex + ']').attr('data-index', serviceEditIndex ).end();

			$clone.find('[name="num_hours[' + serviceEditIndex + ']"]').TouchSpin({
                verticalbuttons: true,
      verticalupclass: 'glyphicon glyphicon-plus',
      verticaldownclass: 'glyphicon glyphicon-minus',
	  min: 1,
        max: 12
            }).on('change', function() {
						
							var nmhrs = jQuery(this).val();
							var indexVal = jQuery(this).attr('data-index');						
							temprice = jQuery('input[name="service_price[' + indexVal + ']"]').val();						
							service_finder_calculateTotal(formID);
					});
	
            // Add new fields
            // Note that we also pass the validator rules for new field as the third parameter
            jQuery('.edit-invoice')
                .bootstrapValidator('addField', 'service_desc[' + serviceEditIndex + ']', descValidators)
				.bootstrapValidator('addField', 'service_price[' + serviceEditIndex + ']', priceValidators);
        })
		.on('change', '[name="cost_type['+ serviceEditIndex +']"]', function() {
			var ctype = jQuery(this).val();
			var indexVal = jQuery(this).attr('data-index');
			if(ctype == 'hourly'){
				jQuery(this).closest('div.form-group').find('.num-hours').show();
				service_finder_calculateTotal(formID);
			}else{
				jQuery(this).closest('div.form-group').find('.num-hours').hide();
				service_finder_calculateTotal(formID);
			}
		})
		.on('change', '#editservicedata [type="radio"]', function() {
			var ctype = jQuery(this).val();
			var indexVal = jQuery(this).attr('data-index');
			if(ctype == 'hourly'){
				jQuery(this).closest('div.form-group').find('.num-hours').show();
				
				service_finder_calculateTotal(formID);
			}else{
				jQuery(this).closest('div.form-group').find('.num-hours').hide();
				service_finder_calculateTotal(formID);
			}
		})
		.on('change', '[name="service_title['+ serviceEditIndex +']"]', function() {
				var sid = jQuery(this).val();
				
				var indexVal = jQuery(this).attr('data-index');
				var data = {
					  "action": "getServiceDetails",
					  "serviceid": sid,
					};
					
				  var formdata = jQuery.param(data);
				  
				  jQuery.ajax({
			
									type: 'POST',
			
									url: ajaxurl,
			
									data: formdata,
									
									dataType: "json",
									
									success:function (data, textStatus) {
										
										if(data['status'] == 'success'){
											jQuery('input[name="service_price[' + indexVal + ']"]').val(data['cost']);
											jQuery('input[name="service_price[' + indexVal + ']"]').change();
											
											if(data['cost_type'] == 'hourly'){
												jQuery('input[name="cost_type[' + indexVal + ']"][value="hourly"]').prop('checked', true);
												jQuery('input[name="cost_type[' + indexVal + ']"]').closest('div.form-group').find('.num-hours').show();
												
												jQuery('input[name="cost_type[' + indexVal + ']"]').closest('div.form-group').find('.num-hours [type="text"]').TouchSpin({
												verticalbuttons: true,
												  verticalupclass: 'glyphicon glyphicon-plus',
												  verticaldownclass: 'glyphicon glyphicon-minus',
												  min: 1,
													max: 12
														}).on('change', function() {
														
															var nmhrs = jQuery(this).val();
															var indexVal = jQuery(this).attr('data-index');						
															temprice = jQuery('input[name="service_price[' + indexVal + ']"]').val();						
															service_finder_calculateTotal(formID);
													});
												
												
											}else if(data['cost_type'] == 'fixed'){
												jQuery('input[name="cost_type[' + indexVal + ']"][value="fix"]').prop('checked', true);
												jQuery('input[name="cost_type[' + indexVal + ']"]').closest('div.form-group').find('.num-hours').hide();
											}
											service_finder_calculateTotal(formID);
											
										}
		
									}
			
								});							   
		})
		.on('change', '#editservicedata select', function() {
				var sid = jQuery(this).val();
				var indexVal = jQuery(this).attr('data-index');
				var data = {
					  "action": "getServiceDetails",
					  "serviceid": sid,
					};
				jQuery('input[name="cost_type[' + indexVal + ']"]').closest('div.form-group').find('.num-hours [type="text"]').val(1);	
				  var formdata = jQuery.param(data);
				  
				  jQuery.ajax({
			
									type: 'POST',
			
									url: ajaxurl,
			
									data: formdata,
									
									dataType: "json",
									
									success:function (data, textStatus) {
										
										if(data['status'] == 'success'){
											jQuery('input[name="service_price[' + indexVal + ']"]').val(data['cost']);
											jQuery('input[name="service_price[' + indexVal + ']"]').change();
											
											if(data['cost_type'] == 'hourly'){
												jQuery('input[name="cost_type[' + indexVal + ']"][value="hourly"]').prop('checked', true);
												jQuery('input[name="cost_type[' + indexVal + ']"]').closest('div.form-group').find('.num-hours').show();
											}else if(data['cost_type'] == 'fixed'){
												jQuery('input[name="cost_type[' + indexVal + ']"][value="fix"]').prop('checked', true);
												jQuery('input[name="cost_type[' + indexVal + ']"]').closest('div.form-group').find('.num-hours').hide();
											}
											
											service_finder_calculateTotal(formID);
											
										}
		
									}
			
								});							   
		})
		
		// Remove button click handler
        .on('click', '.removeButton', function() {
            var $row  = jQuery(this).parents('.form-group'),
                index = $row.attr('data-service-index');

            // Remove fields
            jQuery('.edit-invoice')
                .bootstrapValidator('removeField', $row.find('[name="service_desc[' + index + ']"]'))
                .bootstrapValidator('removeField', $row.find('[name="service_price[' + index + ']"]'));

            // Remove element containing the fields
            $row.remove();
			service_finder_calculateTotal(formID);
        })
        .on('success.form.bv', function(form) {
            // Prevent form submission
			
            form.preventDefault();
			
			jQuery('#serviceEditTemplate').remove();
			// Get the form instance
            var $form = jQuery(form.target);
            // Get the BootstrapValidator instance
            var bv = $form.data('bootstrapValidator');
			
			var data = {
			  "action": "update_invoice",
			  "invoiceid": invoiceid,
			  "total": total,
			  "gtotal": grand_total,
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
								jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.edit-invoice" );	
								
								window.setTimeout(function(){
									/*Close the popup window*/
									$form.parents('.bootbox').modal('hide');
								}, 1000); // 1 seconds expressed in milliseconds
								
								/*Reaload invoicedataTable after add new service*/
								invoicedataTable.ajax.reload(null, false);
							}else if(data['status'] == 'error'){
								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.edit-invoice" );
							}
							
						}

					});
			
        });	
		//Calculate Total Amount
		function service_finder_calculateTotal(formid){
			var taxtype = jQuery('#'+formid).find('input[name=tax-type]:checked').val();
			var discounttype = jQuery('#'+formid).find('input[name=discount-type]:checked').val();
			
			var discountval = jQuery('#'+formid).find('input[name=discount]').val();
			var taxval = jQuery('#'+formid).find('input[name=tax]').val();
			servicetotal = 0;
			
			
			jQuery('#'+formid).find('.col-xs-2 input[type=text]').each(function(){
				   var price = parseFloat(jQuery(this).val());
				   var hrs = jQuery(this).closest('div.form-group').find('.num-hours').find('input').val();
				   var type = jQuery(this).closest('div.form-group').find('input[type=radio]:checked').val();
				   if(type == 'hourly' && price > 0){
					   servicetotal = parseFloat(servicetotal) + (parseFloat(price) * parseFloat(hrs));	
				   }else if(price > 0){
					   servicetotal = parseFloat(servicetotal) + parseFloat(price);	
				   }
				   
			})

			total = servicetotal;
			
			if(discounttype == 'fix'){
				if(discountval > 0){
					discount = parseFloat(discountval);
				}else{
					discount = 0;	
				}
			}else if(discounttype == 'percentage'){
				if(discountval > 0){
					discount = (parseFloat(discountval)/100) * parseFloat(total);
				}else{
					discount = 0;	
				}
			}
			
			if(taxtype == 'fix'){
				
				if(taxval > 0){
					tax = parseFloat(taxval);
				}else{
					tax = 0;	
				}
			}else if(taxtype == 'percentage'){
				if(taxval > 0){
					var taxtotal = total - discount;
					tax = (parseFloat(taxval)/100) * parseFloat(taxtotal);
				}else{
					tax = 0;	
				}
			}
			
			grand_total = (parseFloat(total) - parseFloat(discount)) + parseFloat(tax);

			jQuery('#'+formid).find('#total_discount').html(currencysymbol+discount.toFixed(2));
			jQuery('#'+formid).find('#total_tax').html(currencysymbol+tax.toFixed(2));
			jQuery('#'+formid).find('#total_amount').html(currencysymbol+total.toFixed(2));
			jQuery('#'+formid).find('#grand_total').html(currencysymbol+grand_total.toFixed(2));
		}
		
	
  });