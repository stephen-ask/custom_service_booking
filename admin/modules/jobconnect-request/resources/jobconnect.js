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
  
  jQuery('body').on('click', '.app_jobconnect_request', function(){
			var uid = jQuery(this).data('id');
			
				bootbox.confirm(param.approve_request, function(result) {
				  if(result){
					  var data = {
								  "action": "approve_jobconnect_request",
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
  
	//Start Display Providers in Data Table
	dataTable = jQuery('#jobconnect-requests-grid').DataTable({
		"processing": true,
		"dom": '<"fixed-table-toolbar clearfix"lf><"table-responsive"t><"fixed-table-pagination clearfix"pi>',
		"order": [[ 0, "desc" ]],
		"columns": [
			{ "data": "requestid", "visible": false},
			{ "data": "username"},
			{ "data": "userrole" },
			{ "data": "transactiondate" },
			{ "data": "currentplan" },
			{ "data": "upgraderequestplan" },
			{ "data": "amount" },
			{ "data": "paymentstatus" },
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
				"orderable": true
			},
			{
				"targets": [ 4 ],
				"searchable": true,
				"orderable": true
			},
			{
				"targets": [ 5 ],
				"searchable": true,
				"orderable": true
			},
			{
				"targets": [ 6 ],
				"searchable": true,
				"orderable": true
			},
			{
				"targets": [ 7 ],
				"searchable": true,
				"orderable": true
			},
			{
				"targets": [ 8 ],
				"searchable": true,
				"orderable": false
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
			data: {"action": "get_jobconnect_requests"},
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
	
  });