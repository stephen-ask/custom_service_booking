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
	var experiencedataTable;
	//Add Experience
    jQuery('.add-experience')
        .bootstrapValidator({
            message: param.not_valid,
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
				job_title: {
					validators: {
						notEmpty: {
							message: param.req
						}
					}
				},
				start_date: {
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
			  "action": "add_experience",
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
								
								jQuery('#addexperience').modal('hide');
								
								/*Reaload datatable after add new experience*/
								experiencedataTable.ajax.reload(null, false);
										
							}else if(data['status'] == 'error'){
								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.add-experience" );
							}
							
							
						
						}

					});
			
    });
		
	jQuery('.job_period_date').datepicker({
		format: 'yyyy-mm-dd',			
		language: langcode
	})
	.on('changeDate', function(evt) {
		// Revalidate the date field
		jQuery('.add-experience').bootstrapValidator('revalidateField', 'start_date');
		jQuery('.edit-experience').bootstrapValidator('revalidateField', 'start_date');
	}).on('hide', function(event) {
		event.preventDefault();
		event.stopPropagation();
	});		
		
	jQuery('#addexperience').on('hide.bs.modal', function() {
		jQuery('.add-experience').bootstrapValidator('resetForm',true); // Reset form
		jQuery("form.add-experience").trigger("reset");
	});		
		
	//Edit Experience
    jQuery('.edit-experience')
        .bootstrapValidator({
            message: param.not_valid,
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
				experience_title: {
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
			  "action": "update_experience",
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
								$form.parents('.bootbox').modal('hide');

								/*Reaload datatable after add new service*/
								experiencedataTable.ajax.reload(null, false);
										
							}else if(data['status'] == 'error'){
								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.edit-experience" );
							}
							
							
						
						}

					});
			
    });
		
	//Tabbing on My Account Page
	jQuery("#myTab a").click(function(e){
		e.preventDefault();
		jQuery(this).tab('show');
		var tabid = jQuery(this).attr('href');
		if(tabid == '#experience'){
			if ( ! jQuery.fn.DataTable.isDataTable( '#experience-grid' ) ) {
			experiencedataTable = jQuery('#experience-grid').DataTable( {
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
				data: {"action": "get_experience","user_id": user_id},
				error: function(){  // error handling
					jQuery(".experience-grid-error").html("");
					jQuery("#experience-grid").append('<tbody class="experience-grid-error"><tr><th colspan="3">'+param.no_data+'</th></tr></tbody>');
					jQuery("#experience-grid_processing").css("display","none");
					
				}
			}
			} );
			jQuery('.sf-select-box').selectpicker('refresh');
			}
		}
	});
	
	jQuery("#bulkExperienceDelete").on('click',function() { // bulk checked
        var status = this.checked;
        jQuery(".deleteExperienceRow").each( function() {
            jQuery(this).prop("checked",status);
        });
    });
     
    jQuery('#deleteExperienceTriger').on("click", function(event){ // triggering delete one by one
         
			  if( jQuery('.deleteExperienceRow:checked').length > 0 ){
				  bootbox.confirm(param.are_you_sure, function(result) {
		  if(result){
				  // at-least one checkbox checked
            var ids = [];
            jQuery('.deleteExperienceRow').each(function(){
                if(jQuery(this).is(':checked')) { 
                    ids.push(jQuery(this).val());
                }
            });
            var ids_string = ids.toString();  // array to string conversion 
            jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data: {action: "delete_experience", data_ids:ids_string, "user_id": user_id},
				dataType: "json",
                success: function(data, textStatus) {
                    experiencedataTable.draw(); // redrawing datatable
                },
                async:false
            });
        
		 }
		});
		}else{
				bootbox.alert(param.select_checkbox);
		}
		  
    });	
	
	jQuery('body').on('click', '.editExperience', function(){
															
		jQuery('.loading-area').show();												

        // Get the record's ID via attribute
        var experienceid = jQuery(this).attr('data-id');

		var data = {
			  "action": "load_experience",
			  "experienceid": experienceid
			};

	  var formdata = jQuery.param(data);

	  jQuery.ajax({
		type: 'POST',
		url: ajaxurl,
		data: formdata,
		dataType: "json",
		success:function (data, textStatus) {
		jQuery('#editexperience')
			.find('[name="experienceid"]').val(experienceid).end()
			.find('[name="job_title"]').val(data['job_title']).end()
			.find('[name="company_name"]').val(data['company_name']).end()
			.find('[name="start_date"]').val(data['start_date']).end()
			.find('[name="end_date"]').val(data['end_date']).end()
			.find('[name="description"]').val(data['description']).end()
			.find('[name="current_job"][value="yes"]').prop('checked', data['current_job']).end()
			.find('[name="job_description"]').val(data['description']).end();

		// Show the dialog
		bootbox
			.dialog({
				title: param.edit_experience,
				message: jQuery('#editexperience'),
				show: false // We will show it manually later
			})
			.on('shown.bs.modal', function() {
			tinymce.EditorManager.execCommand('mceAddEditor', true, "edit_experience_description");
				jQuery('.loading-area').hide();
				jQuery('#editexperience')
					.show()                             // Show the login form
					.bootstrapValidator('resetForm'); // Reset form
			})
			.on('hide.bs.modal', function(e) {
				// Therefor, we need to backup the form
				tinymce.EditorManager.execCommand('mceRemoveEditor', true, "edit_experience_description");

				jQuery('#editexperience').hide().appendTo('body');
			})
			.modal('show');
			}

		});

    });
	
  });