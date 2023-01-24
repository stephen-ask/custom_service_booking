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
	var dataTable = '';
	//Display favorites in Data Table
	dataTable = jQuery('#favorites-grid').DataTable( {
	"processing": true,
	"serverSide": true,
	"bAutoWidth": false,
	"columnDefs": [ {
		  "targets": 0,
		  "orderable": false,
		  "searchable": false
		   
		},
		{
		  "targets": 1,
		  "orderable": true,
		  "searchable": false
		   
		},
		{
		  "targets": 2,
		  "orderable": false,
		  "searchable": false
		   
		},
		{
		  "targets": 3,
		  "orderable": false,
		  "searchable": false
		   
		}],
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
		data: {"action": "get_my_favorites"},
		error: function(){  // error handling
			jQuery(".favorites-grid-error").html("");
			jQuery("#favorites-grid").append('<tbody class="favorites-grid-error"><tr><th colspan="3">'+param.no_data+'</th></tr></tbody>');
			jQuery("#favorites-grid_processing").css("display","none");
			
		},
	}
	} );
	
	
	
	jQuery("#bulkFavoritesDelete").on('click',function() { // bulk checked
        var status = this.checked;
        jQuery(".deleteFavoritesRow").each( function() {
            jQuery(this).prop("checked",status);
        });
    });
    
    jQuery('#deleteFavoritesTriger').on("click", function(event){ // triggering delete one by one
		
		
			  if( jQuery('.deleteFavoritesRow:checked').length > 0 ){  // at-least one checkbox checked
           
		   bootbox.confirm(param.are_you_sure, function(result) {
		  if(result){
		   var ids = [];
            jQuery('.deleteFavoritesRow').each(function(){
                if(jQuery(this).is(':checked')) { 
                    ids.push(jQuery(this).val());
                }
            });
            var ids_string = ids.toString();  // array to string conversion 
            jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data: {action: "delete_favorites", data_ids:ids_string},
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
	
  });