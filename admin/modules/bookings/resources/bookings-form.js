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
  jQuery('select').selectpicker();
  //View Bookings
  jQuery('body').on('click', '.viewBookings', function(){
											  
	var bid = jQuery(this).attr('data-id');
	var upcoming = jQuery(this).attr('data-upcoming');
	if(upcoming == 'yes'){
		var flag = 1;	
	}else{
		var flag = 0;	
	}
	viewBookings(bid,flag,'yes');
	
  });
  
  //Close details
  jQuery('body').on('click', '.closeDetails', function(){
		jQuery('#booking-details').addClass('hidden fade in');
		jQuery('#admin-bookings-grid_wrapper').removeClass('hidden');
  });
  
  function viewBookings(bid,flag = 1,isadmin){
		
		jQuery('#admin-bookings-grid_wrapper').addClass('hidden');
		jQuery('#booking-details').removeClass('hidden');
		
		var data = {
		  "action": "booking_details",
		  "bookingid": bid,
		  "flag": flag,
		  "isadmin": isadmin,
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
						
						jQuery('#booking-details').html(data);
						
						jQuery('.display-ratings').rating();
						jQuery('.sf-show-rating').show();
					}
				});		
	}
  
	jQuery('body').on('click', '.paytoprovider', function(){
	
	var bookingid = jQuery(this).data('bookingid');
	var providerid = jQuery(this).data('providerid');
	var amount = jQuery(this).data('amount');
	var data = {
	   action: 'pay_via_masspay',
	   
	   amount: amount,
	   providerid: providerid, 
	   bookingid: bookingid 
	};
	
	bootbox.confirm(param.payto_provider_confirm + " ("+currencysymbol+amount+")?", function(result) {
	if(result){
	
	jQuery('.paytoprovider').attr('disabled', 'true');
	
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
					jQuery('.paytoprovider').removeAttr('disabled');
					bootbox.alert(data['err_message']);
				}
		}
	});
	
	}
	});
	});
	
	jQuery('body').on('click', '.paytoproviderviastripe', function(){
	
	var bookingid = jQuery(this).data('bookingid');
	var providerid = jQuery(this).data('providerid');
	var amount = jQuery(this).data('amount');
	var data = {
	   action: 'pay_via_stripe_connect',
	   providerid: providerid, 
	   bookingid: bookingid, 
	   amount: amount
	};
	
	bootbox.confirm(param.payto_provider_confirm + " ("+currencysymbol+amount+")?", function(result) {
	if(result){
	
	jQuery('.paytoproviderviastripe').attr('disabled', 'true');
	
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
	
	jQuery('body').on('click', '.paytoproviderviamangopay', function(){
	var bookingid = jQuery(this).data('bookingid');
	var providerid = jQuery(this).data('providerid');
	var amount = jQuery(this).data('amount');
	var orderid = jQuery(this).data('orderid');
	var data = {
	   action: 'pay_via_mangopay',
	   providerid: providerid, 
	   bookingid: bookingid,
	   orderid: orderid,
	   amount: amount
	};
	
	bootbox.confirm(param.payto_provider_confirm + " ("+currencysymbol+amount+")?", function(result) {
	if(result){
	
	jQuery('.paytoproviderviastripe').attr('disabled', 'true');
	
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
	
	jQuery('body').on('click', '.statuspaytoprovider', function(){
	
	var bookingid = jQuery(this).data('bookingid');
	var data = {
	   action: 'status_pay_to_provider',
	   bookingid: bookingid, 
	};
	
	bootbox.confirm(param.payto_provider_change_status, function(result) {
	if(result){
	
	jQuery('.statuspaytoprovider').attr('disabled', 'true');
	
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
					jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "#admin-bookings-grid_wrapper" );	
					dataTable.ajax.reload(updatepopupover, false);
				}else if(data['status'] == 'error'){
					jQuery('.statuspaytoprovider').removeAttr('disabled');
					jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "#admin-bookings-grid_wrapper" );
				}
		}
	});
	
	}
	});
	});
	
  //Approve wired booking
	jQuery('body').on('click', '.adminapprovewiredbooking', function(){
		var bookingid = jQuery(this).data('bookingid');													 
		
		var data = {
					  "action": "wired_booking_admin_approval",
					  "bookingid": bookingid,
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
							jQuery('.loading-area').hide();
							if(data['status'] == 'success'){
								jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "#admin-bookings-grid_wrapper" );	
								dataTable.ajax.reload(updatepopupover, false);
							}else if(data['status'] == 'error'){
								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "#admin-bookings-grid_wrapper" );
							}
					}
	
				});
	});
  
	/*Start Admin Booking Table*/
	/* Admin Bookings Datatable */
	dataTable = jQuery('#admin-bookings-grid').DataTable({
		"processing": true,
		"serverSide": true,
		"dom": '<"fixed-table-toolbar clearfix"lf><"table-responsive"t><"fixed-table-pagination clearfix"pi>',
		"order": [[ 0, "desc" ]],
		"columns": [
			{ "data": "bookingid", "visible": false},
			{
				"class":          'delete-control',
				"orderable":      false,
				"data":           "delete",
				"defaultContent": ''
			},
			{ "data": "bookingrefid" },
			{ "data": "datetime" },
			{ "data": "providername" },
			{ "data": "customername" },
			{ "data": "upcomingpast" },
			{ "data": "bookingtype" },
			{ "data": "paymentstatus" },
			{ "data": "bookingamout" },
			{ "data": "bookingstatus" },
			{ "data": "payviabanktransfer" },
			{ "data": "payviabankpaypal" },
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
				"orderable": true,
			},
			{
				"targets": [ 12 ],
				"searchable": true,
				"orderable": true,
			},
			{
				"targets": [ 13 ],
				"searchable": true,
				"orderable": false,
			},
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
			data: {"action": "get_admin_bookings","filterbookings": filterbookings},
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
	
	//Bulk Bookings Delete
	jQuery("#bulkAdminBookingsDelete").on('click',function() { // bulk checked
        var status = this.checked;
        jQuery(".deleteAdminBookingRow").each( function() {
            jQuery(this).prop("checked",status);
        });
    });
     
    //Single Booking Delete
	jQuery('#deleteAdminBookingTriger').on("click", function(event){ // triggering delete one by one
        if( jQuery('.deleteAdminBookingRow:checked').length > 0 ){  // at-least one checkbox checked
            
			bootbox.confirm(param.are_you_sure, function(result) {
		  if(result){
           var ids = [];
            jQuery('.deleteAdminBookingRow').each(function(){
                if(jQuery(this).is(':checked')) { 
                    ids.push(jQuery(this).val());
                }
            });
            var ids_string = ids.toString();  // array to string conversion 
            jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data: {action: "delete_admin_bookings", data_ids:ids_string},
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
		
		return false;
    });
	/*End Admin Booking Table*/
	
	/*Search By Date*/
	jQuery('#bydate').change(function(){
		
		var pageurl = jQuery(this).data('pageurl');
		var val = jQuery(this).val();
		
		window.location.href = pageurl+'&filterbookings='+val;
	
	});
	
	/*Search By Provider*/
	jQuery('#byprovider').change(function(){
		dataTable.column(4).search(this.value).ajax.reload(null, false);
	
	});
	
	/*Toggle Column*/
	jQuery('a.toggle-vis').on( 'click', function (e) {
        e.preventDefault();
 
        // Get the column API object
        var column = dataTable.column( jQuery(this).attr('data-column') );
 
        // Toggle the visibility
        column.visible( ! column.visible() );
    } );
	
	
  });