/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/
var dataTable = '';

// When the browser is ready...
  jQuery(function() {
  'use strict';
  var dataTable = '';
  
  /*Approive Feature Request*/
  jQuery('body').on('click', '#approve-bx', function(){
			var fid = jQuery(this).attr('data-id');
			bootbox.dialog({
                title: "Are you sure you want to approve?",
                message: '<div class="row">  ' +
                    '<div class="col-md-12"> ' +
                    '<form class="form-horizontal featured-approve-form"> ' +
                    '<div class="form-group"> ' +
                    '<label class="col-md-4 control-label" for="name">Please Enter Amount</label> ' +
                    '<div class="col-md-4"> ' +
                    '<input id="featured_amount" name="featured_amount" type="text" placeholder="Amount for Featured" class="form-control input-md"> ' +
                    '</div> ' +
					
					'<div class="form-group"> ' +
                    '<div class="col-md-4"> ' +
                    '<input name="submit" type="submit" class="form-control btn btn-success" value="Approve"> ' +
                    '</div> ' +
					
                    '</div> ' +
                    '</form> </div>  </div>',
                buttons: {
                    success: {
                        label: "Cancel",
                        className: "btn-danger",
                        callback: function () {
                        }
                    }
                }
            })
			.on('shown.bs.modal',function () {
                jQuery('.featured-approve-form')
							.bootstrapValidator({
								message: 'This value is not valid',
								feedbackIcons: {
									valid: 'glyphicon glyphicon-ok',
									invalid: 'glyphicon glyphicon-remove',
									validating: 'glyphicon glyphicon-refresh'
								},
								fields: {
									featured_amount: {
										validators: {
											notEmpty: {
												message: param.req
											},
											digits: {message: param.only_digits},
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
							.on('success.form.bv', function(form) {
								// Prevent form submission
								form.preventDefault();	

								var $form = jQuery(form.target);
								// Get the BootstrapValidator instance
								var bv = $form.data('bootstrapValidator');
								
								var data = {
								  "action": "featured_approve",
								  "fid": fid
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
													jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.featured-approve-form" );	
													window.setTimeout(function(){
														bootbox.hideAll();
													}, 3000); // 3 seconds expressed in milliseconds
													
													/*Reaload datatable after add new service*/
													dataTable.ajax.reload(updatepopupover, false);
															
												}else if(data['status'] == 'error'){
													jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.featured-approve-form" );
													
												}
												
												
											
											}
					
										});
								
							});	

            });
	 });	
  
 /*Edit Feature Price*/
  jQuery('body').on('click', '.editfeaturedprice', function(){
			var fid = jQuery(this).data('id');
			var amount = jQuery(this).data('amount');
			bootbox.dialog({
                title: param.edit_featured_price,
                message: '<div class="row">  ' +
                    '<div class="col-md-12"> ' +
                    '<form class="form-horizontal featured-edit-form"> ' +
                    '<div class="form-group"> ' +
                    '<label class="col-md-4 control-label" for="name">Please Enter Amount</label> ' +
                    '<div class="col-md-4"> ' +
                    '<input id="featured_amount" name="featured_amount" type="text" placeholder="Amount for Featured" value="'+amount+'" class="form-control input-md"> ' +
                    '</div> ' +
					
					'<div class="form-group"> ' +
                    '<div class="col-md-4"> ' +
                    '<input name="submit" type="submit" class="form-control btn btn-success" value="Update"> ' +
                    '</div> ' +
					
                    '</div> ' +
                    '</form> </div>  </div>',
                buttons: {
                    success: {
                        label: "Cancel",
                        className: "btn-danger",
                        callback: function () {
                        }
                    }
                }
            })
			.on('shown.bs.modal',function () {
                jQuery('.featured-edit-form')
							.bootstrapValidator({
								message: 'This value is not valid',
								feedbackIcons: {
									valid: 'glyphicon glyphicon-ok',
									invalid: 'glyphicon glyphicon-remove',
									validating: 'glyphicon glyphicon-refresh'
								},
								fields: {
									featured_amount: {
										validators: {
											notEmpty: {
												message: param.req
											},
											digits: {message: param.only_digits},
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
							.on('success.form.bv', function(form) {
								// Prevent form submission
								form.preventDefault();	

								var $form = jQuery(form.target);
								// Get the BootstrapValidator instance
								var bv = $form.data('bootstrapValidator');
								
								var data = {
								  "action": "featured_edit_price",
								  "fid": fid
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
													jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.featured-edit-form" );	
													window.setTimeout(function(){
														bootbox.hideAll();
													}, 3000); // 3 seconds expressed in milliseconds
													
													/*Reaload datatable after add new service*/
													dataTable.ajax.reload(updatepopupover, false);
															
												}else if(data['status'] == 'error'){
													jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.featured-edit-form" );
													
												}
												
												
											
											}
					
										});
								
							});	

            });
	 });	 
  
/*Decline Feature Request*/ 
  jQuery('body').on('click', '#decline-bx', function(){
			var fid = jQuery(this).attr('data-id');
			bootbox.dialog({
                title: "Are you sure you want to decline?",
                message: '<form class="featured-decline-form"> ' +
                    '<button type="submit" class="bv-hidden-submit" style="display: none; width: 0px; height: 0px;"></button>' +
					'<div class="form-group"> ' +
                    '<label class="control-label" for="name">Comments</label> </div>' +
                    '<div class="form-group"> ' +
                    '<textarea class="form-control input-md" name="comment" id="comment"></textarea>' +
                    '</div> ' +
					
					'<div class="form-group"> ' +
                    '<input name="submit" type="submit" class="form-control btn btn-success" value="Decline"> ' +
                    '</div> ' +
                    '</form>',
                buttons: {
                    success: {
                        label: "Cancel",
                        className: "btn-danger",
                        callback: function () {
                        }
                    }
                }
            })
			.on('shown.bs.modal',function () {
                jQuery('.featured-decline-form')
							.bootstrapValidator({
								message: 'This value is not valid',
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
								  "action": "featured_decline",
								  "fid": fid
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
													jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.featured-decline-form" );	
													window.setTimeout(function(){
														bootbox.hideAll();
													}, 3000); // 3 seconds expressed in milliseconds
													
													/*Reaload datatable after add new service*/
													dataTable.ajax.reload(updatepopupover, false);
															
												}else if(data['status'] == 'error'){
													jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.featured-decline-form" );
													
												}
												
												
											
											}
					
										});
								
							});	

            });
	 });
  
	/*Start Featured Providers Table*/
	dataTable = jQuery('#featured-requests-grid').DataTable({
		"processing": true,
		"dom": '<"fixed-table-toolbar clearfix"lf><"table-responsive"t><"fixed-table-pagination clearfix"pi>',
		"order": [[ 0, "desc" ]],
		"columns": [
			{ "data": "featuredid", "visible": false},
			{ "data": "providername" },
			{ "data": "numberofdays" },
			{ "data": "startdate" },
			{ "data": "enddate" },
			{ "data": "amount" },
			{ "data": "status" },
			{ "data": "actions" }
		],
		"columnDefs": [
			{
				"targets": [ 1 ],
				"searchable": true,
				"orderable": true,
			},
			{
				"targets": [ 2 ],
				"searchable": true,
				"orderable": true,
			},
			{
				"targets": [ 3 ],
				"searchable": true,
				"orderable": true,
			},
			{
				"targets": [ 4 ],
				"searchable": true,
				"orderable": true,
			},
			{
				"targets": [ 5 ],
				"searchable": true,
				"orderable": true,
			},
			{
				"targets": [ 6 ],
				"searchable": true,
				"orderable": true,
			},
			{
				"targets": [ 7 ],
				"searchable": true,
				"orderable": false,
			}
		],
		"language": {
				"processing": "<div></div><div></div><div></div><div></div><div></div>",
				"paginate": {
					  "next": "<i class='fa fa-angle-right'></i>",
					  "previous": "<i class='fa fa-angle-left'></i>"
				},
				"sEmptyTable":     param.sEmptyTable,
				"sInfo":           param.sInfo,
				"sInfoEmpty":      param.sInfoEmpty,
				"sInfoFiltered":   param.sInfoFiltered,
				"sInfoPostFix":    "",
				"sInfoThousands":  ",",
				"sLengthMenu":     param.sLengthMenu,
				"sLoadingRecords": param.sLoadingRecords,
				"sProcessing":     param.sProcessing,
				"sSearch":         param.sSearch,
				"sZeroRecords":    param.sZeroRecords,
				"oPaginate": {
					"sFirst":    param.sFirst,
					"sLast":     param.sLast,
					"sNext":     param.sNext,
					"sPrevious": param.sPrevious,
				},
				"oAria": {
					"sSortAscending":  param.sSortAscending,
					"sSortDescending": param.sSortDescending
				}
				
		},
		"ajax":{
			url :ajaxurl,
			type: "post",
			data: {"action": "get_featured"},
			error: function(){
			},
		},
		"initComplete": function( settings, json ) {
			var rowCount = jQuery(this).find('>tbody >tr').length;
			if(rowCount == 1)
			{
				 jQuery(this).find('>tbody >tr').addClass('sf-single-tablerow');
			}
			jQuery('[data-toggle="tooltip"]').tooltip();
			jQuery('select').selectpicker();
			jQuery("[data-toggle=popover]").each(function() {
				jQuery(this).popover({
				  html: true,
				  content: function() {
					var id = jQuery(this).attr('id')
					return jQuery('#popover-content-' + id).html();
				  }
				});
			});
		},
		"drawCallback": function( settings, json ) {
			jQuery('[data-toggle="tooltip"]').tooltip();
			jQuery('select').selectpicker();
			jQuery("[data-toggle=popover]").each(function() {
				jQuery(this).popover({
				  html: true,
				  content: function() {
					var id = jQuery(this).attr('id')
					return jQuery('#popover-content-' + id).html();
				  }
				});
			});
		}
	});
	
	function updatepopupover()
	{
		jQuery("[data-toggle=popover]").each(function() {
			jQuery(this).popover({
			  html: true,
			  content: function() {
				var id = jQuery(this).attr('id')
				return jQuery('#popover-content-' + id).html();
			  }
			});
		});	
	}
	
	jQuery('body').on('click', '#approve-wired', function(){
	
	var featureid = jQuery(this).data('id');

	var data = {
	   action: 'approve_after_wire_transfer',
	   featureid: featureid, 
	};
	
	bootbox.confirm("Are you sure you want to change payment status from hold to paid?", function(result) {

	if(result){
	
	jQuery.ajax({
	
		type: 'POST',

		url: ajaxurl,

		data: data,
		
		dataType: "json",
		
		beforeSend: function() {
			jQuery('.loading-area').show();
		},

		success:function (data, textStatus) {
				jQuery('.loading-area').hide();
				if(data['status'] == 'success'){
					jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "#featured-requests-grid_wrapper" );	
					dataTable.ajax.reload(updatepopupover, false);
				}else if(data['status'] == 'error'){
					jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "#featured-requests-grid_wrapper" );
				}
		}

	});
	
	}

	});

	});
	
	//Bulk Providers Delete
	jQuery("#bulkFeaturedDelete").on('click',function() { // bulk checked
        var status = this.checked;
        jQuery(".deleteFeaturedRow").each( function() {
            jQuery(this).prop("checked",status);
        });
    });
     
    //Single Providers Delete
	jQuery('#deleteFeaturedTriger').on("click", function(event){ // triggering delete one by one
        if( jQuery('.deleteFeaturedRow:checked').length > 0 ){  // at-least one checkbox checked
            
			bootbox.confirm(param.are_you_sure, function(result) {

		  if(result){

           var ids = [];
            jQuery('.deleteFeaturedsRow').each(function(){
                if(jQuery(this).is(':checked')) { 
                    ids.push(jQuery(this).val());
                }
            });
            var ids_string = ids.toString();  // array to string conversion 
            jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data: {action: "delete_featured", data_ids:ids_string},
                success: function(result) {
                    dataTable.ajax.reload(null, false);
                },
                async:false
            });

        

		}

		});
			
			
        }else{

				bootbox.alert(param.select_checkbox);

		}
    });
	/*End Providers Table*/
	
	/*Toggle Column*/
	jQuery('a.toggle-vis').on( 'click', function (e) {
        e.preventDefault();
 
        // Get the column API object
        var column = dataTable.column( jQuery(this).attr('data-column') );
 
        // Toggle the visibility
        column.visible( ! column.visible() );
    } );
	
	
  });