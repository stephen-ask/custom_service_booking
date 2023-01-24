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

//Close details
jQuery('body').on('click', '.closequotedetails', function(){
	jQuery('#quotation-details').addClass('hidden fade in');
	jQuery('#quotation-grid_wrapper').removeClass('hidden');
});

//Display Invoice in Data Table
dataTable = jQuery('#quotation-grid').DataTable( {
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
	data: {"action": "get_customer_quotation","user_id": user_id},
	error: function(){  // error handling
		jQuery(".quotation-grid-error").html("");
		jQuery("#quotation-grid").append('<tbody class="quotation-grid-error"><tr><th colspan="3">'+param.no_data+'</th></tr></tbody>');
		jQuery("#quotation-grid_processing").css("display","none");
		
	},
}
} );

jQuery('body').on('click', '.quote_description', function(){
	
	var providerid = jQuery(this).data('providerid');
	var quoteid = jQuery(this).data('quoteid');

	var data = {
	   action: 'get_quote_reply_description',
	   providerid: providerid, 
	   quoteid: quoteid 
	};
	
	jQuery.ajax({
	
		type: 'POST',

		url: ajaxurl,

		data: data,
		
		beforeSend: function() {
			jQuery('.loading-area').show();
		},

		success:function (data, textStatus) {
			jQuery('.loading-area').hide();
			bootbox.alert(data);
		}

	});
	
	});

//Close details
jQuery('body').on('click', '.closereplieslisting', function(){
	jQuery('#replies-listing').addClass('hidden fade in');
	jQuery('#quotation-grid_wrapper').removeClass('hidden');
});

//View quote
jQuery('body').on('click', '.viewquotation', function(){
											  
	var quoteid = jQuery(this).data('quoteid');
	view_quote_details(quoteid);
	
});

function view_quote_details(quoteid){
		
		jQuery('#quotation-grid_wrapper').addClass('hidden');
		jQuery('#quotation-details').removeClass('hidden');
		
		var data = {
		  "action": "quotation_details",
		  "quoteid": quoteid,
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
						
						jQuery('#quotation-details').html(data);
						
					}

				});		
	}
	
//View quote
jQuery('body').on('click', '.showreplies', function(){
											  
	var quoteid = jQuery(this).data('quoteid');
	view_replies_listing(quoteid);
	
});

function view_replies_listing(quoteid){
		
		jQuery('#quotation-grid_wrapper').addClass('hidden');
		jQuery('#replies-listing').removeClass('hidden');
		
		var data = {
		  "action": "replies_listing",
		  "quoteid": quoteid,
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
						
						jQuery('#replies-listing').html(data);
						
					}

				});		
	}
	
});