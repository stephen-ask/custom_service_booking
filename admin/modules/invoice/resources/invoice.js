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
  
  jQuery('#invoice-booking-modal').on('show.bs.modal', function (event) {
																 
	   var button = jQuery(event.relatedTarget);
	   var bookingid = button.data('bookingid');																 
	   var data = {
		  "action": "booking_details",
		  "bookingid": bookingid,
		  "calendar": true
		};
		
		var formdata = jQuery.param(data);
		
		jQuery.ajax({

					type: 'POST',

					url: ajaxurl,
					
					beforeSend: function() {
						jQuery('.loading-area').show();
					},
					
					data: formdata,

					success:function (data, textStatus) {
						jQuery('.loading-area').hide();
						jQuery('#invoice-booking-modal .modal-body').html(data);
					}

				});
  });	  
  
  jQuery('body').on('click', '.invoicepaytoprovider', function(){
	
	var invoiceid = jQuery(this).data('invoiceid');
	var providerid = jQuery(this).data('providerid');
	var amount = jQuery(this).data('amount');

	var data = {
	   action: 'invoice_pay_via_masspay',
	   invoiceid: invoiceid, 
	   providerid: providerid, 
	   amount: amount 
	};
	
	bootbox.confirm(param.payto_provider_confirm + " ("+currencysymbol+amount+")?", function(result) {

	if(result){
	
	jQuery('.invoicepaytoprovider').attr('disabled', 'true');
	
	jQuery.ajax({
	
		type: 'POST',

		url: ajaxurl,

		data: data,
		
		dataType: "json",
		
		beforeSend: function() {
			jQuery('.loading-area').show();
		},

		success:function (data, textStatus) {
				
				if(data['status'] == 'success'){
					jQuery('.loading-area').hide();
					bootbox.alert(data['suc_message']);
					dataTable.ajax.reload(updatepopupover, false);
				}else if(data['status'] == 'error'){
					jQuery('.loading-area').hide();
					jQuery('.invoicepaytoprovider').removeAttr('disabled');
					bootbox.alert(data['err_message']);
				}
		}

	});
	
	}

	});

	});
  
  jQuery('body').on('click', '.invoicepaytoproviderviastripe', function(){
	
	var invoiceid = jQuery(this).data('invoiceid');
	var providerid = jQuery(this).data('providerid');
	var amount = jQuery(this).data('amount');
	var data = {
	   action: 'invoicepay_via_stripe_connect',
	   providerid: providerid, 
	   invoiceid: invoiceid, 
	   amount: amount
	};
	
	bootbox.confirm(param.payto_provider_confirm + " ("+currencysymbol+amount+")?", function(result) {
	if(result){
	
	jQuery('.invoicepaytoproviderviastripe').attr('disabled', 'true');
	
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
					bootbox.alert(data['suc_message']);
					dataTable.ajax.reload(updatepopupover, false);
				}else if(data['status'] == 'error'){
					jQuery('.paytoproviderviastripe').removeAttr('disabled');
					bootbox.alert(data['err_message']);
				}
		}
	});
	
	}
	});
	});
  
  jQuery('body').on('click', '.statusinvoicepaytoprovider', function(){
	
	var invoiceid = jQuery(this).data('invoiceid');

	var data = {
	   action: 'status_invoice_pay_to_provider',
	   invoiceid: invoiceid, 
	};
	
	bootbox.confirm(param.payto_provider_change_status, function(result) {

	if(result){
	
	jQuery('.statusinvoicepaytoprovider').attr('disabled', 'true');
	
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
					jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "#invoice-requests-grid_wrapper" );	
					dataTable.ajax.reload(updatepopupover, false);
				}else if(data['status'] == 'error'){
					jQuery('.statusinvoicepaytoprovider').removeAttr('disabled');
					jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "#invoice-requests-grid_wrapper" );
				}
		}

	});
	
	}

	});

	});
  
  jQuery('body').on('click', '.approve_wiredinvoice', function(){
			var invoiceid = jQuery(this).data('id');
			
				bootbox.confirm(param.approve_request, function(result) {
				  if(result){
					  var data = {
								  "action": "approve_wired_invoice",
								  "invoiceid": invoiceid
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
  
	/*Start Featured Providers Table*/
	dataTable = jQuery('#invoice-requests-grid').DataTable({
		"processing": true,
		"dom": '<"fixed-table-toolbar clearfix"lf><"table-responsive"t><"fixed-table-pagination clearfix"pi>',
		"order": [[ 0, "desc" ]],
		"columns": [
			{ "data": "invoiceid", "visible": false},
			{
				"class":          'delete-control',
				"orderable":      false,
				"data":           "delete",
				"defaultContent": ''
			},
			{ "data": "refno" },
			{ "data": "providername" },
			{ "data": "customername" },
			{ "data": "duedate" },
			{ "data": "amount" },
			{ "data": "status" },
			{ "data": "bookingid" },
			{ "data": "payviabank" },
			{ "data": "payviapaypal" },
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
				"orderable": true,
			},
			{
				"targets": [ 10 ],
				"searchable": true,
				"orderable": true,
			},
			{
				"targets": [ 11 ],
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
			data: {"action": "get_admin_invoice"},
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
	
	/*Search By Provider*/
	jQuery('#byproviderinvoice').change(function(){

		dataTable.column(3).search(this.value).ajax.reload(null, false);
	
	});
	
	//Bulk Providers Delete
	jQuery("#bulkAdminInvoiceDelete").on('click',function() { // bulk checked
        var status = this.checked;
        jQuery(".deleteInvoiceRow").each( function() {
            jQuery(this).prop("checked",status);
        });
    });
     
    //Single Providers Delete
	jQuery('#deleteAdminInvoiceTriger').on("click", function(event){ // triggering delete one by one
        if( jQuery('.deleteInvoiceRow:checked').length > 0 ){  // at-least one checkbox checked
            
			bootbox.confirm(param.are_you_sure, function(result) {

		  if(result){

           var ids = [];
            jQuery('.deleteInvoiceRow').each(function(){
                if(jQuery(this).is(':checked')) { 
                    ids.push(jQuery(this).val());
                }
            });
            var ids_string = ids.toString();  // array to string conversion 
            jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data: {action: "delete_admin_invoice", data_ids:ids_string},
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
	
  });