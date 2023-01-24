/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/

var dataTable = '';

jQuery('body').on('click', '.addtowallet', function(){
			var user_id = jQuery(this).attr('data-id');
			bootbox.dialog({
                title: param.bank_account_details,
                message: '<div class="row">  ' +
                    '<div class="col-md-12"> ' +
                    '<form class="form-horizontal addtowallet-form"> ' +
                    '<div class="form-group"> ' +
                    '<label class="col-md-4 control-label" for="name">'+param.wallet_enter_amount+'</label> ' +
                    '<div class="col-md-4"> ' +
                    '<input name="amount" type="text" placeholder="'+param.wallet_add_balance+'" class="form-control input-md"> ' +
                    '</div> ' +
					
					'<div class="form-group"> ' +
                    '<div class="col-md-4"> ' +
                    '<input name="submit" type="submit" class="form-control btn btn-success" value="'+param.add_to_wallet+'"> ' +
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
                jQuery('.addtowallet-form')
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
								  "action": "addtowallet",
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
													jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.addtowallet-form" );	
													window.setTimeout(function(){
														bootbox.hideAll();
													}, 3000); // 3 seconds expressed in milliseconds
													
													/*Reaload datatable after add new service*/
													dataTable.ajax.reload(updatepopupover, false);
															
												}else if(data['status'] == 'error'){
													jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.addtowallet-form" );
													
												}
												
												
											
											}
					
										});
								
							});	

            });
	 });	

/*Approive Feature Request*/
jQuery('body').on('click', 'input[name="makefeatured"]', function(){
	proid = jQuery(this).val();
			if(jQuery(this).is(':checked')) { 
                
			
			bootbox.dialog({
                title: param.approve_request,
				message: '<form class="free-feature-form bv-form"><button type="submit" class="bv-hidden-submit" style="display: none; width: 0px; height: 0px;"></button><div class="form-group has-feedback"><label for="name" class="control-label">Please Enter Number of Days</label></div><div class="form-group bootstrap-touchspin"><div class="input-group bootstrap-touchspin"><span class="input-group-addon bootstrap-touchspin-prefix" style="display: none;"></span><input type="text" value="'+jQuery('#minvalue').val()+'" class="form-control input-md" placeholder="Number of Days" name="days" id="days" style="display: block;" data-bv-field="days"><span class="input-group-addon bootstrap-touchspin-postfix" style="display: none;"></span></div></div><i style="display: none;" class="form-control-feedback bv-icon-input-group" data-bv-icon-for="days"></i><div class="form-group"><input type="submit" value="Make Featured" class="form-control btn btn-success" name="submit"></div><small style="display: none;" class="help-block" data-bv-validator="notEmpty" data-bv-for="days" data-bv-result="NOT_VALIDATED">Please enter a value</small><small style="display: none;" class="help-block" data-bv-validator="digits" data-bv-for="days" data-bv-result="NOT_VALIDATED">Please enter only digits</small></form>',
                buttons: {
                    success: {
                        label: "Cancel",
                        className: "btn-danger",
                        callback: function () {
							jQuery('#makefeatured-'+proid).prop('checked', false);  	
                        }
                    }
                }
            })
			.on('shown.bs.modal',function () {
				var minval = jQuery('#minvalue').val();
				var maxval = jQuery('#maxvalue').val();						   
				jQuery("input[name='days']").TouchSpin({
				  verticalbuttons: true,
				  verticalupclass: 'glyphicon glyphicon-plus',
				  verticaldownclass: 'glyphicon glyphicon-minus',
				   min: minval,
					max: maxval
				});	
				
                jQuery('.free-feature-form')
							.bootstrapValidator({
								message: 'This value is not valid',
								feedbackIcons: {
									valid: 'glyphicon glyphicon-ok',
									invalid: 'glyphicon glyphicon-remove',
									validating: 'glyphicon glyphicon-refresh'
								},
								fields: {
									days: {
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
								  "action": "free_featured",
								  "proid": proid
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
													jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.free-feature-form" );	
													window.setTimeout(function(){
														bootbox.hideAll();
													}, 3000); // 5 seconds expressed in milliseconds
													
													/*Reaload datatable after add new service*/
													dataTable.ajax.reload(updatepopupover, false);
															
												}else if(data['status'] == 'error'){
													jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.free-feature-form" );
													
												}
												
												
											
											}
					
										});
								
							});	

            });
	 
            }else{
				bootbox.confirm(param.make_unfeatured, function(result) {
				  if(result){
					  var data = {
								  "action": "make_unfeatured",
								  "proid": proid
								};
								
								var formdata = jQuery.param(data);
								
								jQuery.ajax({
					
											type: 'POST',
					
											url: ajaxurl,
											
											dataType: "json",
											
											beforeSend: function() {
												jQuery(".alert-success,.alert-danger").remove();
											},
											
											data: formdata,
					
											success:function (data, textStatus) {
												if(data['status'] == 'success'){
																			
													/*Reaload datatable after add new service*/
													dataTable.ajax.reload(updatepopupover, false);
															
												}else if(data['status'] == 'error'){
													
													jQuery('#makefeatured-'+proid).prop('checked', true);  
												}
												
												
											
											}
					
										});
				  }else{
					jQuery('#makefeatured-'+proid).prop('checked', true);  
				  }
				}); 
			}																	
});	

	/*Search By Featured Provider*/
	jQuery('#byfeatured').change(function(){

		dataTable.column(5).search(this.value).ajax.reload(null, false);
	
	});
	
	/*Search By Need Approval*/
	jQuery('#byapproval').change(function(){

		dataTable.column(7).search(this.value).ajax.reload(null, false);
	
	});
	
	
/*Block User*/ 
  jQuery('body').on('click', '.blockaccount', function(){
			uid = jQuery(this).attr('data-id');
			bootbox.dialog({
                title: "Are you sure you want to block this user?",
                message: '<form class="blockuser-form bv-form"> ' +
                    '<button type="submit" class="bv-hidden-submit" style="display: none; width: 0px; height: 0px;"></button>' +
					'<div class="form-group"> ' +
                    '<label class="control-label" for="name">Comments</label> </div>' +
                    '<div class="form-group"> ' +
                    '<textarea class="form-control input-md" name="comment" id="comment"></textarea>' +
                    '</div> ' +
					
					'<div class="form-group"> ' +
                    '<input name="submit" type="submit" class="form-control btn btn-success" value="Block"> ' +
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
                jQuery('.blockuser-form')
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
								  "action": "block_user",
								  "uid": uid
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
													jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.blockuser-form" );	
													window.setTimeout(function(){
														bootbox.hideAll();
													}, 3000); // 3 seconds expressed in milliseconds
													
													/*Reaload datatable after add new service*/
													dataTable.ajax.reload( updatepopupover,false );
													
													
															
												}else if(data['status'] == 'error'){
													jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.blockuser-form" );
													
												}
												
												
											
											}
					
										});
								
							});	

            });
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

/*Un-Block User*/ 
  jQuery('body').on('click', '.unblockaccount', function(){
			uid = jQuery(this).attr('data-id');
			
				bootbox.confirm(param.make_unbloacked, function(result) {
				  if(result){
					  var data = {
								  "action": "unblock_user",
								  "uid": uid
								};
								
								var formdata = jQuery.param(data);
								
								jQuery.ajax({
					
											type: 'POST',
					
											url: ajaxurl,
											
											dataType: "json",
											
											beforeSend: function() {
												jQuery(".alert-success,.alert-danger").remove();
											},
											
											data: formdata,
					
											success:function (data, textStatus) {
												if(data['status'] == 'success'){
																			
													/*Reaload datatable after add new service*/
													dataTable.ajax.reload(updatepopupover, false);
															
												}else if(data['status'] == 'error'){
													
													jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.rejectuser-form" );
												}
												
												
											
											}
					
										});
				  }
				}); 
			
	 });
  
/*Block User*/ 
  jQuery('body').on('click', '.viewbankinfo', function(){
			var uid = jQuery(this).attr('data-id');
			var data = {
			  "action": "get_bank_account_info",
			  "uid": uid
			};
			
			var formdata = jQuery.param(data);
			
			jQuery.ajax({

						type: 'POST',

						url: ajaxurl,
						
						dataType: "json",
						
						beforeSend: function() {
							jQuery(".alert-success,.alert-danger").remove();
						},
						
						data: formdata,

						success:function (data, textStatus) {
							if(data['status'] == 'success'){
									if(data['flag'] == 1){
										bootbox.dialog({
												title: param.bank_account_details,
												message: '<table class="table table-striped table-bordered"> ' +
													'<tr>' +
													'<td>'+param.bank_account_holder+'</td>' +
													'<td>'+data['bank_account_holder_name']+'</td>' +
													'</tr>' +
													'<tr>' +
													'<td>'+param.bank_account_number+'</td>' +
													'<td>'+data['bank_account_number']+'</td>' +
													'</tr>' +
													'<tr>' +
													'<td>'+param.bank_account_swiftcode+'</td>' +
													'<td>'+data['swift_code']+'</td>' +
													'</tr>' +
													'<tr>' +
													'<td>'+param.bank_name+'</td>' +
													'<td>'+data['bank_name']+'</td>' +
													'</tr>' +
													'<tr>' +
													'<td>'+param.bank_branch_city+'</td>' +
													'<td>'+data['bank_branch_city']+'</td>' +
													'</tr>' +
													'<tr>' +
													'<td>'+param.bank_branch_country+'</td>' +
													'<td>'+data['bank_branch_country']+'</td>' +
													'</tr>' +
													'</table>',
											})
									}else if(data['flag'] == 0){
										bootbox.dialog({
											title: param.bank_account_details,
											message: param.bank_details_not_avl
										})
									}
							}
						}

					});
			
	 });  

/*Approved User*/ 
  jQuery('body').on('click', '.approveprovider', function(){
			uid = jQuery(this).attr('data-id');
			
				bootbox.confirm(param.provider_approve_request, function(result) {
				  if(result){
					  var data = {
								  "action": "approved_user",
								  "uid": uid
								};
								
								var formdata = jQuery.param(data);
								
								jQuery.ajax({
					
											type: 'POST',
					
											url: ajaxurl,
											
											dataType: "json",
											
											beforeSend: function() {
												jQuery(".alert-success,.alert-danger").remove();
											},
											
											data: formdata,
					
											success:function (data, textStatus) {
												if(data['status'] == 'success'){
																			
													/*Reaload datatable after add new service*/
													dataTable.ajax.reload(updatepopupover, false);
															
												}
												
												
											
											}
					
										});
				  }
				}); 
			
	 });

/*Reject User*/ 
  jQuery('body').on('click', '.rejectprovider', function(){
			uid = jQuery(this).attr('data-id');
			bootbox.dialog({
                title: "Are you sure you want to reject this user?",
                message: '<form class="rejectuser-form"> ' +
                    '<button type="submit" class="bv-hidden-submit" style="display: none; width: 0px; height: 0px;"></button>' +
					'<div class="form-group"> ' +
                    '<label class="control-label" for="name">Comments</label> </div>' +
                    '<div class="form-group"> ' +
                    '<textarea class="form-control input-md" name="rejectcomment" id="rejectcomment"></textarea>' +
                    '</div> ' +
					
					'<div class="form-group"> ' +
                    '<input name="submit" type="submit" class="form-control btn btn-success" value="Reject"> ' +
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
                jQuery('.rejectuser-form')
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
								  "action": "reject_user",
								  "uid": uid
								};
								
								var formdata = jQuery($form).serialize() + "&" + jQuery.param(data);
								
								jQuery.ajax({
					
											type: 'POST',
					
											url: ajaxurl,
											
											dataType: "json",
											
											beforeSend: function() {
												jQuery(".alert-success,.alert-danger").remove();
											},
											
											data: formdata,
					
											success:function (data, textStatus) {
												$form.find('input[type="submit"]').prop('disabled', false);
												if(data['status'] == 'success'){
													jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.rejectuser-form" );	
													window.setTimeout(function(){
														bootbox.hideAll();
													}, 3000); // 3 seconds expressed in milliseconds
													
													/*Reaload datatable after add new service*/
													dataTable.ajax.reload(updatepopupover, false);
															
												}else if(data['status'] == 'error'){
													jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.rejectuser-form" );
													
												}
												
												
											
											}
					
										});
								
							});	

            });
	 });

// When the browser is ready...
  jQuery(function() {
  'use strict';
  
  var identitydataTable;
  
  /*Un-Block User*/ 
  jQuery('body').on('click', '.mekeitvendors', function(){
			
			bootbox.confirm(param.are_you_sure, function(result) {
			  if(result){
				  var data = {
							  "action": "make_it_vendors",
							};
							
							var formdata = jQuery.param(data);
							
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
											if(data['status'] == 'success'){
																		
												/*Reaload datatable after add new service*/
												dataTable.ajax.reload(updatepopupover, false);
														
											}else if(data['status'] == 'error'){
												
												jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( ".table-responsive" );
											}
											
											
										
										}
				
									});
			  }
			}); 
			
	 });
  
	//Start Display Providers in Data Table
	dataTable = jQuery('#providers-grid').DataTable({
		"processing": true,
		"serverSide": true,
		"dom": '<"fixed-table-toolbar clearfix"lf><"table-responsive"t><"fixed-table-pagination clearfix"pi>',
		"order": [[ 0, "desc" ]],
		"columns": [
			{ "data": "providerid", "visible": false},
			{
				"class":          'delete-control',
				"orderable":      false,
				"data":           "delete",
				"defaultContent": ''
			},
			{ "data": "providername" },
			{ "data": "email" },
			{ "data": "membership" },
			{ "data": "featured" },
			{ "data": "paymentmethod" },
			{ "data": "status" },
			{ "data": "actions" }
		],
		"columnDefs": [
			{
				"targets": [ 1 ],
				"searchable": false,
				"orderable": false,
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
			data: {"action": "get_providers"},
			error: function(){
			},
		},
		"initComplete": function( settings, json ) {
			var rowCount = jQuery(this).find('>tbody >tr').length;
			if(rowCount == 1)
			{
				 jQuery(this).find('>tbody >tr').addClass('sf-single-tablerow');
			}else{
				jQuery(this).find('>tbody >tr').removeClass('sf-single-tablerow');
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
			var rowCount = jQuery(this).find('>tbody >tr').length;
			if(rowCount == 1)
			{
				 jQuery(this).find('>tbody >tr').addClass('sf-single-tablerow');
			}else{
				jQuery(this).find('>tbody >tr').removeClass('sf-single-tablerow');
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
		}
	});
	
	//Providers identity check in Data Table
	identitydataTable = jQuery('#providers-identity-check-grid').DataTable({
		"processing": true,
		"serverSide": true,
		"dom": '<"fixed-table-toolbar clearfix"lf><"table-responsive"t><"fixed-table-pagination clearfix"pi>',
		"order": [[ 0, "desc" ]],
		"columns": [
			{ "data": "identityid", "visible": false},
			{ "data": "providername" },
			{ "data": "phone" },
			{ "data": "email" },
			{ "data": "identity" },
			{ "data": "status" },
			{ "data": "actions" }
		],
		"columnDefs": [
			{
				"targets": [ 1 ],
				"searchable": false,
				"orderable": false,
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
			data: {"action": "get_providers_identity"},
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
	
	//Approve identity check
	jQuery("body").on("click", ".approveidentity", function(event){ // triggering delete one by one
		
		var providerid = jQuery(this).data('id');
		
		bootbox.confirm(param.are_you_sure, function(result) {

		  if(result){

            jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data: {action: "approve_provider_identity", providerid:providerid},
                success: function(result) {
                    identitydataTable.ajax.reload(updatepopupover, false);
                },
                async:false
            });

        

		}

		});

    });
	
	//UnApprove identity check
	jQuery("body").on("click", ".unapproveidentity", function(event){ // triggering delete one by one
		
		var providerid = jQuery(this).data('id');
		
		bootbox.prompt({
			title: param.declinereason,
			inputType: 'textarea',
			callback: function (result) {
				if(result){
					var data = {
						action    : 'unapprove_provider_identity',
						providerid    : providerid,
						reason 	  : result
					};
					
					var formdata = jQuery.param(data);
					
					jQuery.ajax({
						type : "post",
						dataType : "json",
						url : ajaxurl,
						data : formdata,
						success: function(response) {
							identitydataTable.ajax.reload(updatepopupover, false);
						}
				   }) 
				}
			}
		});
		
    });
	
	//Bulk Providers Delete
	jQuery("#bulkProvidersDelete").on('click',function() { // bulk checked
        var status = this.checked;
        jQuery(".deleteProvidersRow").each( function() {
            jQuery(this).prop("checked",status);
        });
    });
     
    //Single Providers Delete
	jQuery('#deleteProvidersTriger').on("click", function(event){ // triggering delete one by one
        if( jQuery('.deleteProvidersRow:checked').length > 0 ){  // at-least one checkbox checked
		
		bootbox.confirm(param.are_you_sure, function(result) {

		  if(result){

           var ids = [];
            jQuery('.deleteProvidersRow').each(function(){
                if(jQuery(this).is(':checked')) { 
                    ids.push(jQuery(this).val());
                }
            });
            var ids_string = ids.toString();  // array to string conversion 
            jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data: {action: "delete_providers", data_ids:ids_string},
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