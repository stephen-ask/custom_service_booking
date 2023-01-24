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
	
	var provider_id= '';
	var date = '';
	var olddate = '';
	var slots = [];
	var datearr = '';
	var bookedarr = '';
	var allocatedarr = '';
	
	var daynumarr = [];
	var datearr = [];
	var bookedarr = [];
	var allocatedarr = [];
	
	
	/*Modal popup show for set unavailbility*/
	jQuery('#setunavailability').on('show.bs.modal', function() {
		jQuery('select[name="avlmemberid"]').val('');
		jQuery('.sf-select-box').selectpicker('refresh');	
		reset_calendar();
		
	});
	
	function reset_calendar(memberid = ''){
		provider_id = jQuery('#setunavailability').attr('data-proid');
		
		jQuery("#loadavlcalendar").html('<div id="availability-calendar"></div>');
		jQuery(".protimelist").html(param.timeslot);
		slots = [];
		date = '';
		jQuery('[name="wholeday"][value="yes"]').prop('checked', false);
		
		var data = {
				  "action": "reset_calendar",
				  "provider_id": provider_id,
				  "user_id": user_id,
				  "memberid": memberid
				};
		
		var formdata = jQuery.param(data);
		
		jQuery.ajax({
	
					type: 'POST',
	
					url: ajaxurl,
					
					dataType: "json",
					
					beforeSend: function() {
						jQuery('.loading-area').show();
					},
					
					data: formdata,
	
					success:function (data, textStatus) {
						jQuery('.loading-area').hide();
						if(data['status'] == 'success'){
						daynumarr = jQuery.parseJSON(data['daynum']);
						datearr = jQuery.parseJSON(data['dates']);
						bookedarr = jQuery.parseJSON(data['bookeddates']);
						allocatedarr = jQuery.parseJSON(data['allocateddates']);
						service_finder_deleteCookie('setselecteddate');
						/*Call the bootstrap calendar*/
						jQuery("#availability-calendar").zabuto_calendar({
						today: true,
						show_previous: false,
						mode : 'add',
						daynum : daynumarr,
						datearr : datearr,
						bookedarr : bookedarr,
						allocatedarr : allocatedarr,
                        action: function () {
							jQuery('.dow-clickable').removeClass("selected");
							jQuery(this).addClass("selected");
							date = jQuery("#" + this.id).data("date");
							service_finder_setCookie('setselecteddate', date);
							slots = [];
							return service_finder_getTimeslots(this.id, provider_id);
                        },
                    });	
						}else if(data['status'] == 'error'){
						}
						
						
					
					}
	
				});	
	}
	
	/*Edit UnAvilabilityButton Popup box*/
	jQuery('body').on('click', '.editUnAvilabilityButton', function(){
		jQuery("#loadcalendar").html('<div id="editavailability-calendar"></div>');													
		jQuery('.loading-area').show();												
        // Get the record's ID via attribute

        var unavilabilitydate = jQuery(this).data('id');
		var memberid = jQuery(this).data('memberid');

		

		var data = {

			  "action": "load_unavilability",

			  "unavilabilitydate": unavilabilitydate,
			  
			  "user_id": user_id,
			  
			  "memberid": memberid,
			  

			};

			

	  var formdata = jQuery.param(data);

	  

	  jQuery.ajax({



						type: 'POST',



						url: ajaxurl,



						data: formdata,

						

						dataType: "json",



						success:function (data, textStatus) {

							var provider_id = jQuery('#setunavailability').attr('data-proid');	
							// Populate the form fields with the data returned from server
							olddate = data['date'];
							service_finder_setCookie('setselecteddate', data['date']);
							date = data['date'];
							if(data['wholeday'] == 'yes'){
							var $status = true;
							}else{
							var $status = false;	
							}
							
							slots = [];
							jQuery('#editunavailability')
							.find('[name="wholeday"][value="yes"]').prop('checked', $status).end()
							.find('input[name="editavlmemberid"]').val(data['memberid']).end()
							.find('#editedmembername strong').html(data['membername']).end()
							.find('.protimelist').html(data['slots']).end();
							
							jQuery('.sf-select-box').selectpicker('refresh');
							
							jQuery('ul.protimelist li').each(function(){
								if(jQuery(this).hasClass('active')){
									slots.push(jQuery(this).attr('data-source'));
								}else{
									var removeItem = jQuery(this).attr('data-source');  
									slots = jQuery.grep(slots, function(value) {
									  return value != removeItem;
									});
								}
							});
							daynumarr = jQuery.parseJSON(data['dayavlnum']);
							datearr = jQuery.parseJSON(data['dates']);
							bookedarr = jQuery.parseJSON(data['bookeddates']);
							allocatedarr = jQuery.parseJSON(data['allocateddates']);
							
							
							jQuery("#editavailability-calendar").zabuto_calendar({
								today: true,
								show_previous: true,
								date:data['date'],
								selectedday:data['daynum'],
								month : data['month'],
								mode : 'edit',
								year: data['year'],
								daynum : daynumarr,
								datearr : datearr,
								bookedarr : bookedarr,
								allocatedarr : allocatedarr,
								action: function () {
									jQuery('.dow-clickable').removeClass("selected");
									jQuery(this).addClass("selected");
									date = jQuery("#" + this.id).data("date");
									service_finder_setCookie('setselecteddate', date); 
									slots = [];
									return service_finder_getTimeslots(this.id, provider_id);
								},
							});	

							
				

							// Show the dialog

							bootbox

								.dialog({

									title: param.edit_unavl,

									message: jQuery('#editunavailability'),

									show: false // We will show it manually later

								})
								
								.on('shown.bs.modal', function() {
								
									jQuery('.loading-area').hide();

									jQuery('#editunavailability')

										.show()                             // Show the login form

										.bootstrapValidator('resetForm'); // Reset form

								})

								.on('hide.bs.modal', function(e) {

									// Bootbox will remove the modal (including the body which contains the login form)
									// after hiding the modal
									// Therefor, we need to backup the form
									jQuery('#editunavailability').hide().appendTo('body');
								})
								.modal('show');
					}
				});

    });
				  
  /*Get Time Slots*/
	jQuery('ul.protimelist').on('click', 'li', function(){
		jQuery(this).toggleClass('active');
		if(jQuery(this).hasClass('active')){
			slots.push(jQuery(this).attr('data-source'));
		}else{
			var removeItem = jQuery(this).attr('data-source');  
			slots = jQuery.grep(slots, function(value) {
			  return value != removeItem;
			});
		}
	});		

	jQuery('body').on('click', 'input[name="wholeday"]', function(){	
		var status = this.checked;
		if(status){
		jQuery('ul.protimelist li').addClass('active');
		}else{
		jQuery('ul.protimelist li').removeClass('active');
		slots = [];
		}
	});
  
/*Save Unavailability*/
    jQuery('.set-new-unavailability')
	.bootstrapValidator({
			message: param.not_valid,
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {}
    })
	.on('change', 'select[name="avlmemberid"]', function() {
               var memberid = jQuery(this).val();
			   
			   reset_calendar(memberid);
    })
	.on('success.form.bv', function(form) {
            // Prevent form submission
			jQuery('.set-new-unavailability').find('input[type="submit"]').prop('disabled', false);
            form.preventDefault();
			if(date == ""){
			jQuery(".alert-success,.alert-danger").remove();
			jQuery( "<div class='alert alert-danger'>"+param.select_date+"</div>" ).insertAfter( "form.set-new-unavailability .modal-header" );
			return false;
			}
			
			if(unavl_type == 'slots' || (unavl_type == 'days' && unavl_type == 1)){
			if(slots == "" && !jQuery('input[name="wholeday"]').prop("checked")){
			jQuery(".alert-success,.alert-danger").remove();
			jQuery( "<div class='alert alert-danger'>"+param.select_timeslot+"</div>" ).insertAfter( "form.set-new-unavailability .modal-header" );
			return false;
			}
			}
            // Get the form instance
            var $form = jQuery(form.target);
            // Get the BootstrapValidator instance
            var bv = $form.data('bootstrapValidator');
			
			var data = {
			  "action": "set_unavailability",
			  "date": date,
			  "slots": slots,
			  "provider_id": provider_id,
			  "unavl_type": unavl_type,
			  "numberofdays": numberofdays
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
								jQuery('#setunavailability').modal('hide');
								/*Reaload datatable after add new service*/
								dataTable.ajax.reload(null, false);
							}else if(data['status'] == 'error'){
								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertAfter( "form.set-new-unavailability .modal-header" );
							}
							
							
						
						}

					});
			
        });
  
/*Update Unavailability*/
    jQuery('.edit-unavailability')
	.bootstrapValidator({
			message: param.not_valid,
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {}
    })
	.on('success.form.bv', function(form) {
            // Prevent form submission
            jQuery('.edit-unavailability').find('input[type="submit"]').prop('disabled', false);
            form.preventDefault();
			if(date == ""){
			jQuery(".alert-success,.alert-danger").remove();
			jQuery( "<div class='alert alert-danger'>"+param.select_date+"</div>" ).insertBefore( "form.edit-unavailability" );
			return false;
			}
			if(slots == "" && !jQuery('input[name="wholeday"]').prop("checked")){
			jQuery(".alert-success,.alert-danger").remove();
			jQuery( "<div class='alert alert-danger'>"+param.select_timeslot+"</div>" ).insertBefore( "form.edit-unavailability" );
			return false;
			}

            // Get the form instance
            var $form = jQuery(form.target);
            // Get the BootstrapValidator instance
            var bv = $form.data('bootstrapValidator');
			
			var data = {
			  "action": "edit_unavailability",
			  "olddate": olddate,
			  "date": date,
			  "slots": slots,
			  "user_id": user_id,
			  "unavl_type": unavl_type,
			  "numberofdays": numberofdays
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
								$form.parents('.bootbox').modal('hide');
								/*Reaload datatable after add new service*/
								dataTable.ajax.reload(null, false);
							}else if(data['status'] == 'error'){
								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.edit-unavailability" );
							}
							
							
						
						}

					});
			
        });

	//Tabbing on My Account Page
	jQuery("#myTab a").click(function(e){
		e.preventDefault();
		jQuery(this).tab('show');
		var tabid = jQuery(this).attr('href');
		if(tabid == '#unavailability'){
			if ( ! jQuery.fn.DataTable.isDataTable( '#unavilability-grid' ) ) {
			dataTable = jQuery('#unavilability-grid').DataTable( {

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

		data: {"action": "get_unavilability","user_id": user_id},

		error: function(){  // error handling

			jQuery(".unavilability-grid-error").html("");

			jQuery("#unavilability-grid").append('<tbody class="unavilability-grid-error"><tr><th colspan="3">'+param.no_data+'</th></tr></tbody>');

			jQuery("#unavilability-grid_processing").css("display","none");

			

		}

	}

	} );
			jQuery('.sf-select-box').selectpicker('refresh');
			}
		}
	});
	
	
	jQuery("#bulkUnAvilabilityDelete").on('click',function() { // bulk checked

        var status = this.checked;

        jQuery(".deleteUnAvilabilityRow").each( function() {

            jQuery(this).prop("checked",status);

        });

    });

     

    jQuery('#deleteUnAvilabilityTriger').on("click", function(event){ // triggering delete one by one

		

			  if( jQuery('.deleteUnAvilabilityRow:checked').length > 0 ){

				  bootbox.confirm(param.are_you_sure, function(result) {

		  if(result){

				  // at-least one checkbox checked

            var ids = [];

            jQuery('.deleteUnAvilabilityRow').each(function(){

                if(jQuery(this).is(':checked')) { 

                    ids.push(jQuery(this).val());

                }

            });

            var ids_string = ids.toString();  // array to string conversion 

            jQuery.ajax({

                type: "POST",

                url: ajaxurl,

                data: {action: "delete_unavilability", data_ids:ids_string,"user_id": user_id},

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
	
	/*Callback function to get timeslots*/
  function service_finder_getTimeslots(id, provider_id) {
		
		date = jQuery("#" + id).data("date");
		
		// Show the dialog

		bootbox
		.dialog({
			title: param.unavl_days,
			message: jQuery('#multidatepopover'),
			show: false, // We will show it manually later
			autodestroy:true,
			buttons: {
			cancel: {
				label: "Cancel",
				className: 'btn-danger',
			},
			ok: {
				label: "Continue",
				className: 'btn-primary',
				callback: function(){
				unavl_type = jQuery('input[name="unavl_type"]:checked').val();
				numberofdays = jQuery('input[name="number_of_days"]').val();
				jQuery('#timeslotbox').show();
				if(((unavl_type == 'months' || unavl_type == 'weeks') && numberofdays > 0) || (unavl_type == 'days' && numberofdays > 1)){
				jQuery('#multidatepopover').parents('.bootbox').modal('hide');
				jQuery('[name="wholeday"][value="yes"]').prop('checked', true);
				jQuery('#timeslotbox').hide();
				var data = {
					  "action": "get_offdays",
					  "unavl_type": unavl_type,
					  "numberofdays": numberofdays,
					  "startdate": date,
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
						jQuery('.dow-clickable').removeClass("selected");
						for (var key in data) {
						if (data.hasOwnProperty(key)) {
						  	var sdate = id.replace(date, data[key]);
							jQuery("#" + sdate).addClass("selected");
						}
						}
						jQuery('.loading-area').hide();
					}
		
				});	
				}else if(unavl_type == 'slots'){
				jQuery('#multidatepopover').parents('.bootbox').modal('hide');
				jQuery( ".set-new-unavailability .protimelist" ).html('');
				jQuery( "#multidatepopover .protimelist" ).clone().appendTo( ".set-new-unavailability .protimelist" );
				
				}else if(unavl_type == 'days' && numberofdays == 1){
					jQuery('#multidatepopover').parents('.bootbox').modal('hide');
					jQuery('[name="wholeday"][value="yes"]').prop('checked', true);
					jQuery('#timeslotbox').hide();	
				}else{
					jQuery(".alert-success,.alert-danger").remove();
					jQuery( "<div class='alert alert-danger'>"+param.select_option+"</div>" ).insertBefore( "form.multidatepopover" );
				}
				
				return true;
				}
			}
			}
		})
		.on('shown.bs.modal', function() {
									   
			jQuery('.loading-area').hide();
			jQuery('#multidatepopover')
				.show()                             // Show the login form
				.bootstrapValidator('resetForm'); // Reset form
				jQuery('[name="unavl_type"][value="days"]').prop('checked', true);
				jQuery('#numdays').show();
				jQuery('#timeslotpopover').hide();
				
			jQuery('#multidatepopover').on('click', 'input[name="unavl_type"]', function(){	
				if(jQuery(this).val() == 'slots'){
					jQuery('#numdays').hide();
					
					var data = {
					  "action": "get_timeslot",
					  "seldate": date,
					  "provider_id": provider_id,
					};
				var formdata = jQuery.param(data);
				  
				jQuery.ajax({
		
					type: 'POST',
		
					url: ajaxurl,
		
					data: formdata,
					
					beforeSend: function() {
						jQuery('.loading-area').show();
					},
		
					success:function (data, textStatus) {
						jQuery('.loading-area').hide();
						jQuery('#timeslotpopover').show();
						jQuery('#multidatepopover .protimelist').html(data);
					}
		
				});
				}else{
					jQuery('#timeslotpopover').hide();
					jQuery('#numdays').show();
				}
			});
		})
		.on('hide.bs.modal', function(e) {
			jQuery('#multidatepopover').hide().appendTo('body');
		})
		.on('hidden.bs.modal', function(e) {
			jQuery('body').addClass('modal-open');
		})
		.modal('show');
		
	}	
  
  });
  
	
