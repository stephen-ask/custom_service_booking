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
	var offersdataTable;
	jQuery('#addoffers').on('show.bs.modal', function (event) {
		jQuery('body').addClass('bs-modal-open');
	});
	
	jQuery('#addoffers').on('hidden.bs.modal', function (event) {
		jQuery('body').removeClass('bs-modal-open');
	});
	
	//Add Offers
    jQuery('.add-offers')
        .bootstrapValidator({
            message: param.not_valid,
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
				offer_title: {
					validators: {
						notEmpty: {
							message: param.req
						}
					}
				},
				coupon_code: {
					validators: {
						notEmpty: {
							message: param.req
						}
					}
				},
				expiry_date: {
					validators: {
						notEmpty: {
							message: param.req
						}
					}
				},
				max_coupon: {
					validators: {
						notEmpty: {
							message: param.req
						}
					}
				},
				discount_type: {
					validators: {
						notEmpty: {
							message: param.req
						}
					}
				},
				discount_value: {
					validators: {
						notEmpty: {
							message: param.req
						},
						numeric: {message: param.only_digits},
					}
				},
            }
        })
        .on('success.form.bv', function(form) {
            // Prevent form submission
			
            tinyMCE.triggerSave();
			form.preventDefault();

            // Get the form instance
            var $form = jQuery(form.target);
            // Get the BootstrapValidator instance
            var bv = $form.data('bootstrapValidator');
			
			var data = {
			  "action": "add_offers",
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
								/*Close the popup window*/
								
								jQuery('#addoffers').modal('hide');
								
								/*Reaload datatable after add new offers*/
								offersdataTable.ajax.reload(null, false);
										
							}else if(data['status'] == 'error'){
								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.add-offers" );
							}
							
							
						
						}

					});
			
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
	
	jQuery('#addoffers').on('hide.bs.modal', function() {
		jQuery('.add-offers').bootstrapValidator('resetForm',true); // Reset form
		jQuery("form.add-offers").trigger("reset");
	});
	
	//Edit Offers
    jQuery('.edit-offers')
        .bootstrapValidator({
            message: param.not_valid,
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
				offer_title: {
					validators: {
						notEmpty: {
							message: param.req
						}
					}
				},
				coupon_code: {
					validators: {
						notEmpty: {
							message: param.req
						}
					}
				},
				expiry_date: {
					validators: {
						notEmpty: {
							message: param.req
						}
					}
				},
				max_coupon: {
					validators: {
						notEmpty: {
							message: param.req
						}
					}
				},
				discount_type: {
					validators: {
						notEmpty: {
							message: param.req
						}
					}
				},
				discount_value: {
					validators: {
						notEmpty: {
							message: param.req
						},
						numeric: {message: param.only_digits},
					}
				},
            }
        })
        .on('success.form.bv', function(form) {
            // Prevent form submission
			
            tinyMCE.triggerSave();
			form.preventDefault();

            // Get the form instance
            var $form = jQuery(form.target);
            // Get the BootstrapValidator instance
            var bv = $form.data('bootstrapValidator');
			
			var data = {
			  "action": "update_offers",
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
								offersdataTable.ajax.reload(null, false);
										
							}else if(data['status'] == 'error'){
								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.edit-offers" );
							}
							
							
						
						}

					});
			
    });
		
	//Tabbing on My Account Page
	jQuery("#myTab a").click(function(e){
		e.preventDefault();
		jQuery(this).tab('show');
		var tabid = jQuery(this).attr('href');
		if(tabid == '#offers'){
			if ( ! jQuery.fn.DataTable.isDataTable( '#offers-grid' ) ) {
			offersdataTable = jQuery('#offers-grid').DataTable( {
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
				data: {"action": "get_offers","user_id": user_id},
				error: function(){  // error handling
					jQuery(".offers-grid-error").html("");
					jQuery("#offers-grid").append('<tbody class="offers-grid-error"><tr><th colspan="3">'+param.no_data+'</th></tr></tbody>');
					jQuery("#offers-grid_processing").css("display","none");
					
				}
			}
			} );
			jQuery('.sf-select-box').selectpicker('refresh');
			}
		}
	});
	
	jQuery("#bulkOffersDelete").on('click',function() { // bulk checked
        var status = this.checked;
        jQuery(".deleteOffersRow").each( function() {
            jQuery(this).prop("checked",status);
        });
    });
	
	jQuery('#deleteOffersTriger').on("click", function(event){ // triggering delete one by one
         
			  if( jQuery('.deleteOffersRow:checked').length > 0 ){
				  bootbox.confirm(param.are_you_sure, function(result) {
		  if(result){
				  // at-least one checkbox checked
            var ids = [];
            jQuery('.deleteOffersRow').each(function(){
                if(jQuery(this).is(':checked')) { 
                    ids.push(jQuery(this).val());
                }
            });
            var ids_string = ids.toString();  // array to string conversion 
            jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data: {action: "delete_offers", data_ids:ids_string, "user_id": user_id},
				dataType: "json",
                success: function(data, textStatus) {
                    offersdataTable.draw(); // redrawing datatable
                },
                async:false
            });
        
		 }
		});
		}else{
				bootbox.alert(param.select_checkbox);
		}
		  
    });	
	
	jQuery('body').on('click', '.editOffers', function(){
															
		jQuery('.loading-area').show();												

        // Get the record's ID via attribute
        var offerid = jQuery(this).data('id');

		var data = {
			  "action": "load_offers",
			  "offerid": offerid
			};

	  var formdata = jQuery.param(data);

	 jQuery.ajax({
		type: 'POST',
		url: ajaxurl,
		data: formdata,
		dataType: "json",
		success:function (data, textStatus) {
		jQuery('#editoffers')
			.find('[name="offerid"]').val(offerid).end()
			.find('[name="offer_title"]').val(data['offer_title']).end()
			.find('[name="coupon_code"]').val(data['coupon_code']).end()
			.find('[name="expiry_date"]').val(data['expiry_date']).end()
			.find('[name="max_coupon"]').val(data['max_coupon']).end()
			.find('[name="discount_type"][value="'+data['discount_type']+'"]').prop('checked', true).end()
			.find('[name="discount_value"]').val(data['discount_value']).end()
			.find('[name="edit_discount_description"]').val(data['discount_description']).end();

		// Show the dialog
		bootbox
			.dialog({
				title: param.edit_offers,
				message: jQuery('#editoffers'),
				show: false // We will show it manually later
			})
			.on('shown.bs.modal', function() {
			tinymce.EditorManager.execCommand('mceAddEditor', true, "edit_discount_description");
				jQuery('.loading-area').hide();
				jQuery('#editoffers')
					.show()                             // Show the login form
					.bootstrapValidator('resetForm'); // Reset form
			})
			.on('hide.bs.modal', function(e) {
				// Therefor, we need to backup the form
				tinymce.EditorManager.execCommand('mceRemoveEditor', true, "edit_discount_description");

				jQuery('#editoffers').hide().appendTo('body');
			})
			.modal('show');
			}

		});

    });
	
  });
