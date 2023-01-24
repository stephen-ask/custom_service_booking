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
	var regiondataTable;
	//Add Regions
    jQuery('.add-service-region')
        .bootstrapValidator({
            message: param.not_valid,
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
				region: {
					validators: {
						notEmpty: {
							message: param.region
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
			  "action": "add_service_region",
			  "user_id": user_id
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
							$form.find('input[type="submit"]').prop('disabled', false);
							if(data['status'] == 'success'){
								/*Close the popup window*/
								jQuery('#arearegion').text(data['regions']);
								
								jQuery('#addserviceregions').modal('hide');
								
								/*Reaload datatable after add new service*/
								regiondataTable.ajax.reload(null, false);
										
							}else if(data['status'] == 'error'){
								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.add-service-region" );
							}
							
							
						
						}

					});
			
    });
		
	//Change Status for region
	jQuery('body').on('click', '.changeRegionStatus', function(){
												  
		var rid = jQuery(this).data('id');
		var status = jQuery(this).data('status');
		
		bootbox.confirm(param.change_status, function(result) {
		  if(result){
			  var data = {
			  "action": "change_region_status",
			  "regionid": rid,
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
							regiondataTable.ajax.reload(null, false);
						}

					});
			  }
		}); 
		
    });	
	
	//Change Status for zipode
	jQuery('body').on('click', '.changeZipcodeStatus', function(){
												  
		var zid = jQuery(this).data('id');
		var status = jQuery(this).data('status');
		
		bootbox.confirm(param.change_status, function(result) {
		  if(result){
			  var data = {
			  "action": "change_zipcode_status",
			  "zipcodeid": zid,
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
		
	//Tabbing on My Account Page
	jQuery("#myTab a").click(function(e){
		e.preventDefault();
		jQuery(this).tab('show');
		var tabid = jQuery(this).attr('href');
		if(tabid == '#service-regions'){
			if ( ! jQuery.fn.DataTable.isDataTable( '#regions-grid' ) ) {
			regiondataTable = jQuery('#regions-grid').DataTable( {
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
		data: {"action": "get_serviceregions","user_id": user_id},
		error: function(){  // error handling
			jQuery(".regions-grid-error").html("");
			jQuery("#regions-grid").append('<tbody class="regions-grid-error"><tr><th colspan="3">'+param.no_data+'</th></tr></tbody>');
			jQuery("#regions-grid_processing").css("display","none");
			
		}
	}
	} );
			jQuery('.sf-select-box').selectpicker('refresh');
			}
		}
	});
	
	jQuery("#bulkRegionsDelete").on('click',function() { // bulk checked
        var status = this.checked;
        jQuery(".deleteRegionRow").each( function() {
            jQuery(this).prop("checked",status);
        });
    });
     
    jQuery('#deleteRegionTriger').on("click", function(event){ // triggering delete one by one
         
			  if( jQuery('.deleteRegionRow:checked').length > 0 ){
				  bootbox.confirm(param.are_you_sure, function(result) {
		  if(result){
				  // at-least one checkbox checked
            var ids = [];
            jQuery('.deleteRegionRow').each(function(){
                if(jQuery(this).is(':checked')) { 
                    ids.push(jQuery(this).val());
                }
            });
            var ids_string = ids.toString();  // array to string conversion 
            jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data: {action: "delete_serviceregion", data_ids:ids_string, "user_id": user_id},
				dataType: "json",
                success: function(data, textStatus) {
					jQuery('#arearegion').val(data['regions']);
                    regiondataTable.draw(); // redrawing datatable
                },
                async:false
            });
        
		 }
		});
		}else{
				bootbox.alert(param.select_checkbox);
		}
		  
    });	
	
	// Add New Service Area
    jQuery('.add-service-area')
        .bootstrapValidator({
            message: param.not_valid,
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
				zipcode: {
					validators: {
						notEmpty: {
							message: param.postal_code
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
			  "action": "add_service_area",
			  "user_id": user_id
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
							$form.find('input[type="submit"]').prop('disabled', false);
							if(data['status'] == 'success'){
								jQuery('#areazipcode').text(data['zipcodes']);
								/*Close the popup window*/
								jQuery('#addservicearea').modal('hide');
								
								/*Reaload datatable after add new service*/
								dataTable.ajax.reload(null, false);
										
							}else if(data['status'] == 'error'){
								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.add-service-area" );
							}
							
							
						
						}

					});
			
        });
	
	//Tabbing on My Account Page
	jQuery("#myTab a").click(function(e){
		e.preventDefault();
		jQuery(this).tab('show');
		var tabid = jQuery(this).attr('href');
		if(tabid == '#postal-codes'){
			if ( ! jQuery.fn.DataTable.isDataTable( '#zipcodes-grid' ) ) {
			dataTable = jQuery('#zipcodes-grid').DataTable( {
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
		data: {"action": "get_serviceareas","user_id": user_id},
		error: function(){  // error handling
			jQuery(".zipcodes-grid-error").html("");
			jQuery("#zipcodes-grid").append('<tbody class="zipcodes-grid-error"><tr><th colspan="3">'+param.no_data+'</th></tr></tbody>');
			jQuery("#zipcodes-grid_processing").css("display","none");
			
		}
	}
	} );
			jQuery('.sf-select-box').selectpicker('refresh');
			}
		}
	});
	
	
	jQuery("#bulkZipcodeDelete").on('click',function() { // bulk checked
        var status = this.checked;
        jQuery(".deleteZipcodeRow").each( function() {
            jQuery(this).prop("checked",status);
        });
    });
     
    jQuery('#deleteZipcodeTriger').on("click", function(event){ // triggering delete one by one
         
			  if( jQuery('.deleteZipcodeRow:checked').length > 0 ){
				  bootbox.confirm(param.are_you_sure, function(result) {
		  if(result){
				  // at-least one checkbox checked
            var ids = [];
            jQuery('.deleteZipcodeRow').each(function(){
                if(jQuery(this).is(':checked')) { 
                    ids.push(jQuery(this).val());
                }
            });
            var ids_string = ids.toString();  // array to string conversion 
            jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data: {action: "delete_servicearea", data_ids:ids_string, "user_id": user_id},
				dataType: "json",
                success: function(data, textStatus) {
					jQuery('#areazipcode').val(data['zipcodes']);
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