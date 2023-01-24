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
var dataTable = '';

jQuery('body').on('click', '.app_claimbusiness_request', function(){
				var uid = jQuery(this).data('id');
			
				bootbox.confirm(param.approve_request, function(result) {
				  if(result){
					  var data = {
								  "action": "approve_claimbusiness_request",
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

/*Start Claim Table*/
	dataTable = jQuery('#claim-grid').DataTable({
		"processing": true,
		"dom": '<"fixed-table-toolbar clearfix"lf><"table-responsive"t><"fixed-table-pagination clearfix"pi>',
		"order": [[ 0, "desc" ]],
		"columns": [
			{ "data": "claimid", "visible": false},
			{
				"class":          'delete-control',
				"orderable":      false,
				"data":           "delete",
				"defaultContent": ''
			},
			{ "data": "providername" },
			{ "data": "customername" },
			{ "data": "date" },
			{ "data": "email" },
			{ "data": "message" },
			{ "data": "paymentstatus" },
			{ "data": "cliamstatus" },
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
				"orderable": true,
			},
			{
				"targets": [ 8 ],
				"searchable": true,
				"orderable": true,
			},
			{
				"targets": [ 9 ],
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
			data: {"action": "get_claimbusiness"},
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

//Bulk Claim Delete
jQuery("#bulkAdminClaimDelete").on('click',function() { // bulk checked
	var status = this.checked;
	jQuery(".deleteClaimRow").each( function() {
		jQuery(this).prop("checked",status);
	});
});
 
//Single Claim Delete
jQuery('#deleteAdminClaimTriger').on("click", function(event){ // triggering delete one by one
	if( jQuery('.deleteClaimRow:checked').length > 0 ){  // at-least one checkbox checked
		
	  bootbox.confirm(param.are_you_sure, function(result) {

	  if(result){

	   var ids = [];
		jQuery('.deleteClaimRow').each(function(){
			if(jQuery(this).is(':checked')) { 
				ids.push(jQuery(this).val());
			}
		});
		var ids_string = ids.toString();  // array to string conversion 
		jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: {action: "delete_claimbusiness", data_ids:ids_string},
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


/*Approive Claim Request*/
jQuery('body').on('click', '#approveclaim', function(){
var cid = jQuery(this).data('id');
var pid = jQuery(this).data('providerid');

bootbox.confirm(param.are_you_sure, function(result) {

  if(result){
	var data = {
	  "action": "approveclaim",
	  "cid": cid,
	  "pid": pid,
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
						jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "#claim-grid" );	
						/*Reaload datatable after add new city*/
						dataTable.ajax.reload(updatepopupover, false);
								
					}else if(data['status'] == 'error'){
						jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "#claim-grid" );
						
					}
				
				}

			});	  
  }

});												  
});	

/*Decline Claim Request*/
jQuery('body').on('click', '#declineclaim', function(){
var cid = jQuery(this).data('id');
var pid = jQuery(this).data('providerid');

bootbox.confirm(param.are_you_sure, function(result) {

  if(result){
	var data = {
	  "action": "declineclaim",
	  "cid": cid,
	  "pid": pid,
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
						jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "#claim-grid" );	
						/*Reaload datatable after add new city*/
						dataTable.ajax.reload(updatepopupover, false);
								
					}else if(data['status'] == 'error'){
						jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "#claim-grid" );
						
					}
				
				}

			});	  
  }

});												  
});	

	
});