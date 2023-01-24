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
	var certificatesdataTable;
	
	jQuery('#addcertificates').on('show.bs.modal', function (event) {
		jQuery('body').addClass('bs-modal-open');
	});
	
	jQuery('#addcertificates').on('hidden.bs.modal', function (event) {
		jQuery('body').removeClass('bs-modal-open');
	});
	
	//Add Certificates
    jQuery('.add-certificates')
        .bootstrapValidator({
            message: param.not_valid,
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
				certificates_title: {
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
			  "action": "add_certificates",
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
								
								jQuery('#addcertificates').modal('hide');
								
								/*Reaload datatable after add new certificates*/
								certificatesdataTable.ajax.reload(null, false);
										
							}else if(data['status'] == 'error'){
								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.add-certificates" );
							}
							
							
						
						}

					});
			
    });
		
	jQuery('.certificate_issue_date').datepicker({
		format: 'yyyy-mm-dd',			
		language: langcode
	})
	.on('changeDate', function(evt) {
		// Revalidate the date field
	}).on('hide', function(event) {
		event.preventDefault();
		event.stopPropagation();
	});	
		
	jQuery('#addcertificates').on('hide.bs.modal', function() {
		jQuery('.add-certificates').bootstrapValidator('resetForm',true); // Reset form
		jQuery("form.add-certificates").trigger("reset");
		jQuery('ul.rwmb-uploaded').html('');
		jQuery('#certificate-dragdrop').removeClass('hidden');
	});	
		
	//Edit Certificates
    jQuery('.edit-certificates')
        .bootstrapValidator({
            message: param.not_valid,
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
				certificates_title: {
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
			
            tinyMCE.triggerSave();
			form.preventDefault();

            // Get the form instance
            var $form = jQuery(form.target);
            // Get the BootstrapValidator instance
            var bv = $form.data('bootstrapValidator');
			
			var data = {
			  "action": "update_certificates",
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
								certificatesdataTable.ajax.reload(null, false);
										
							}else if(data['status'] == 'error'){
								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.edit-certificates" );
							}
							
							
						
						}

					});
			
    });
		
	//Tabbing on My Account Page
	jQuery("#myTab a").click(function(e){
		e.preventDefault();
		jQuery(this).tab('show');
		var tabid = jQuery(this).attr('href');
		if(tabid == '#certificates'){
			if ( ! jQuery.fn.DataTable.isDataTable( '#certificates-grid' ) ) {
			certificatesdataTable = jQuery('#certificates-grid').DataTable( {
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
				data: {"action": "get_certificates","user_id": user_id},
				error: function(){  // error handling
					jQuery(".certificates-grid-error").html("");
					jQuery("#certificates-grid").append('<tbody class="certificates-grid-error"><tr><th colspan="3">'+param.no_data+'</th></tr></tbody>');
					jQuery("#certificates-grid_processing").css("display","none");
					
				}
			}
			} );
			jQuery('.sf-select-box').selectpicker('refresh');
			}
		}
	});
	
	jQuery("#bulkCertificatesDelete").on('click',function() { // bulk checked
        var status = this.checked;
        jQuery(".deleteCertificatesRow").each( function() {
            jQuery(this).prop("checked",status);
        });
    });
     
    jQuery('#deleteCertificatesTriger').on("click", function(event){ // triggering delete one by one
         
			  if( jQuery('.deleteCertificatesRow:checked').length > 0 ){
				  bootbox.confirm(param.are_you_sure, function(result) {
		  if(result){
				  // at-least one checkbox checked
            var ids = [];
            jQuery('.deleteCertificatesRow').each(function(){
                if(jQuery(this).is(':checked')) { 
                    ids.push(jQuery(this).val());
                }
            });
            var ids_string = ids.toString();  // array to string conversion 
            jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data: {action: "delete_certificates", data_ids:ids_string, "user_id": user_id},
				dataType: "json",
                success: function(data, textStatus) {
                    certificatesdataTable.draw(); // redrawing datatable
                },
                async:false
            });
        
		 }
		});
		}else{
				bootbox.alert(param.select_checkbox);
		}
		  
    });	
	
	jQuery('body').on('click', '.editCertificate', function(){
															
		jQuery('.loading-area').show();												

        // Get the record's ID via attribute
        var certificatesid = jQuery(this).attr('data-id');

		var data = {
			  "action": "load_certificates",
			  "certificatesid": certificatesid
			};

	  var formdata = jQuery.param(data);

	  jQuery.ajax({
		type: 'POST',
		url: ajaxurl,
		data: formdata,
		dataType: "json",
		success:function (data, textStatus) {
		jQuery('#editcertificates')
			.find('[name="certificatesid"]').val(certificatesid).end()
			.find('[name="certificate_title"]').val(data['certificate_title']).end()
			.find('[name="issue_date"]').val(data['issue_date']).end()
			.find('.rwmb-uploaded').html(data['imagehtml']).end()
			.find('#certificateedit-dragdrop').removeClass('hidden').end()
			.find('#certificateedit-dragdrop').addClass(data['hiddenclass']).end()
			.find('[name="description"]').val(data['description']).end();

		// Show the dialog
		bootbox
			.dialog({
				title: param.edit_certificates,
				message: jQuery('#editcertificates'),
				show: false // We will show it manually later
			})
			.on('shown.bs.modal', function() {
			tinymce.EditorManager.execCommand('mceAddEditor', true, "edit_certificates_description");
				jQuery('.loading-area').hide();
				jQuery('#editcertificates')
					.show()                             // Show the login form
					.bootstrapValidator('resetForm'); // Reset form
			})
			.on('hide.bs.modal', function(e) {
				// Therefor, we need to backup the form
				tinymce.EditorManager.execCommand('mceRemoveEditor', true, "edit_certificates_description");

				jQuery('#editcertificates').hide().appendTo('body');
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
	
  });