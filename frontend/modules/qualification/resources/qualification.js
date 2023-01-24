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

	var qualificationdataTable;

	//Add Qualification

    jQuery('.add-qualification')

        .bootstrapValidator({

            message: param.not_valid,

            feedbackIcons: {

                valid: 'glyphicon glyphicon-ok',

                invalid: 'glyphicon glyphicon-remove',

                validating: 'glyphicon glyphicon-refresh'

            },

            fields: {

				qualification_title: {

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

			  "action": "add_qualification",

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

								

								jQuery('#addqualification').modal('hide');

								

								/*Reaload datatable after add new qualification*/

								qualificationdataTable.ajax.reload(null, false);

										

							}else if(data['status'] == 'error'){

								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.add-qualification" );

							}

							

							

						

						}



					});

			

    });

		

	jQuery('#addqualification').on('hide.bs.modal', function() {

		jQuery('.add-qualification').bootstrapValidator('resetForm',true); // Reset form

		jQuery("form.add-qualification").trigger("reset");

		jQuery('select[name="from_year"]').val('');

		jQuery('select[name="to_year"]').val('');

		jQuery('.sf-select-box').selectpicker('refresh');

	});

		

	//Edit Qualification

    jQuery('.edit-qualification')

        .bootstrapValidator({

            message: param.not_valid,

            feedbackIcons: {

                valid: 'glyphicon glyphicon-ok',

                invalid: 'glyphicon glyphicon-remove',

                validating: 'glyphicon glyphicon-refresh'

            },

            fields: {

				qualification_title: {

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

			  "action": "update_qualification",

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

								qualificationdataTable.ajax.reload(null, false);

										

							}else if(data['status'] == 'error'){

								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.edit-qualification" );

							}

							

							

						

						}



					});

			

    });

		

	//Tabbing on My Account Page

	jQuery("#myTab a").click(function(e){

		e.preventDefault();

		jQuery(this).tab('show');

		var tabid = jQuery(this).attr('href');

		if(tabid == '#qualification'){

			if ( ! jQuery.fn.DataTable.isDataTable( '#qualification-grid' ) ) {

			qualificationdataTable = jQuery('#qualification-grid').DataTable( {

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

				data: {"action": "get_qualification","user_id": user_id},

				error: function(){  // error handling

					jQuery(".qualification-grid-error").html("");

					jQuery("#qualification-grid").append('<tbody class="qualification-grid-error"><tr><th colspan="3">'+param.no_data+'</th></tr></tbody>');

					jQuery("#qualification-grid_processing").css("display","none");

					

				}

			}

			} );

			jQuery('.sf-select-box').selectpicker('refresh');

			}

		}

	});

	

	jQuery("#bulkQualificationDelete").on('click',function() { // bulk checked

        var status = this.checked;

        jQuery(".deleteQualificationRow").each( function() {

            jQuery(this).prop("checked",status);

        });

    });

     

    jQuery('#deleteQualificationTriger').on("click", function(event){ // triggering delete one by one

         

			  if( jQuery('.deleteQualificationRow:checked').length > 0 ){

				  bootbox.confirm(param.are_you_sure, function(result) {

		  if(result){

				  // at-least one checkbox checked

            var ids = [];

            jQuery('.deleteQualificationRow').each(function(){

                if(jQuery(this).is(':checked')) { 

                    ids.push(jQuery(this).val());

                }

            });

            var ids_string = ids.toString();  // array to string conversion 

            jQuery.ajax({

                type: "POST",

                url: ajaxurl,

                data: {action: "delete_qualification", data_ids:ids_string, "user_id": user_id},

				dataType: "json",

                success: function(data, textStatus) {

                    qualificationdataTable.ajax.reload(null, false);

                },

                async:false

            });

        

		 }

		});

		}else{

				bootbox.alert(param.select_checkbox);

		}

		  

    });	

	

	jQuery('body').on('click', '.editQualification', function(){

															

		jQuery('.loading-area').show();												



        // Get the record's ID via attribute

        var qualificationid = jQuery(this).attr('data-id');



		var data = {

			  "action": "load_qualification",

			  "qualificationid": qualificationid

			};



	  var formdata = jQuery.param(data);



	  jQuery.ajax({

		type: 'POST',

		url: ajaxurl,

		data: formdata,

		dataType: "json",

		success:function (data, textStatus) {

		jQuery('#editqualification')

			.find('[name="qualificationid"]').val(qualificationid).end()

			.find('[name="degree_name"]').val(data['degree_name']).end()

			.find('[name="institute_name"]').val(data['institute_name']).end()

			.find('[name="from_year"]').val(data['from_year']).end()

			.find('[name="to_year"]').val(data['to_year']).end()

			.find('[name="description"]').val(data['description']).end();

			

			jQuery('.sf-select-box').selectpicker('refresh');



		// Show the dialog

		bootbox

			.dialog({

				title: param.edit_qualification,

				message: jQuery('#editqualification'),

				show: false // We will show it manually later

			})

			.on('shown.bs.modal', function() {

			tinymce.EditorManager.execCommand('mceAddEditor', true, "edit_qualification_description");

				jQuery('.loading-area').hide();

				jQuery('#editqualification')

					.show()                             // Show the login form

					.bootstrapValidator('resetForm'); // Reset form

			})

			.on('hide.bs.modal', function(e) {

				// Therefor, we need to backup the form

				tinymce.EditorManager.execCommand('mceRemoveEditor', true, "edit_qualification_description");



				jQuery('#editqualification').hide().appendTo('body');

			})

			.modal('show');

			}



		});



    });

	

  });