/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/

// When the browser is ready...


jQuery(document).ready( function(){
	jQuery('#categoryimport').on('submit', function(e){
        e.preventDefault();
        if(document.getElementById("categorycsv").value == "" && jQuery( "#upload_catfile" ).is(":visible") ) {
			var fileMessage = param.file_message;
			alert(fileMessage);
			return false;
		}

		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			beforeSend: function() {
				jQuery('.category-loading-image').show();
				jQuery('.category-form-overlay').show();
			},
			data:  new FormData(this),
			contentType: false,
			cache: false,
			processData:false,
			type: "POST",
			success:function (response, textStatus) {
				jQuery('.category-loading-image').hide();
				jQuery('.category-form-overlay').hide();
				
				if(response.data.status == 'success'){
					 jQuery("#success").removeClass("alert alert-danger");
				 	 jQuery("#success").addClass("alert alert-success");
					 jQuery("#success").html(param.import_categories_success);
					 jQuery("#categorycsv").val(''); 
					 return false;
				}
			
			},
			error:function (data, textStatus) {
				jQuery('.category-loading-image').hide();
				jQuery('.category-form-overlay').hide();
			}

		});
		
    });
	
	jQuery('#upload_csv').on('submit', function(e){
        e.preventDefault();
        if(document.getElementById("uploadfiles").value == "" && jQuery( "#upload_file" ).is(":visible") ) {
			var fileMessage = param.file_message;
			alert(fileMessage);
			return false;
		}
        var fd = new FormData();
		var file = jQuery(document).find('#upload_csv input[type="file"]');
		var individual_file = file[0].files[0];
		var update_existing_users = jQuery("#update_existing_users").val();
		var no_records = jQuery("#no_records").val();
		var csv_num_rows = jQuery("#csv_num_rows").val();
		fd.append("file", individual_file);
		fd.append("update_existing_users", update_existing_users); 
		fd.append("no_records", no_records); 
		fd.append("csv_num_rows", csv_num_rows); 
		fd.append('action', 'import_providers'); 
			jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: fd,
			contentType: false,
			processData: false,
			beforeSend: function() {
				jQuery('.provider-loading-image').show();
				jQuery('.provider-form-overlay').show();
			},
			success: function(response){
				 var response = jQuery.parseJSON(response);
				 jQuery('.provider-loading-image').hide();
				 jQuery('.provider-form-overlay').hide();
				 
				 if(typeof(response.csv) != "undefined" && response.csv == "File must be a CSV") {
					  jQuery("#success").addClass("alert alert-danger");
					  jQuery("#success").html(param.csv);
					  return false;
				 }
				 if(typeof(response.csv_num_rows) != "undefined" && response.csv_num_rows !== null) {
					 var csv_num_rows = response.csv_num_rows;
				 }
				 if(typeof(response.url) != "undefined" && response.url !== null) {
					 var url = response.url;
				 }
				 if(typeof(response.records) != "undefined" && response.records !== null) {
					 var records = response.records;
				 }
				 if(typeof(response.total) != "undefined" && response.total == "final") {
				  	 jQuery("#success").removeClass("alert alert-danger");
				 	 jQuery("#success").addClass("alert alert-success");
					 jQuery("#success").html(param.import_success);
					 jQuery("#uploadfiles").val(''); 
					 return false;
				 }
				 setTimeout(function(){
							 keepSessionAlive(records, url);
					 }, 500);
			},
			error: function(e){
			
				 setTimeout(function(){
						keepSessionAlive("", "");
				 }, 1000);	
				return false;
			}
		});
		
    });
    //var keepAliveTimeout = 1000 * 10;

	function keepSessionAlive(records, url)
	{
		var fd = new FormData();
		var file = jQuery(document).find('input[type="file"]');
		var individual_file = file[0].files[0];
		fd.append("url", url);
		fd.append("file", individual_file);
		fd.append("no_records", records);
		fd.append("update_existing_users", update_existing_users);
		fd.append('action', 'keep_session_alive'); 
		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: fd,
			contentType: false,
			processData: false,
			beforeSend: function() {
				jQuery('.provider-loading-image').show();
				jQuery('.provider-form-overlay').show();
			},
			success: function(response){
				 jQuery('.provider-loading-image').hide();	
				 jQuery('.provider-form-overlay').hide();
				 var response = jQuery.parseJSON(response);
				 if(typeof(response.csv_num_rows) != "undefined" && response.csv_num_rows !== null) {
					 jQuery('#csv_num_rows').val(response.csv_num_rows);
				 }
				 if(typeof(response.url) != "undefined" && response.url !== null) {
					 var url = response.url;
				 }
				 if(typeof(response.records) != "undefined" && response.records !== null) {
					 var records = response.records;
				 }
				 if(typeof(response.total) != "undefined" && response.total == "final") {
				 	 jQuery("#success").removeClass("alert alert-danger");
				     jQuery("#success").addClass("alert alert-success");
					 jQuery("#success").html(param.import_success);
					 jQuery("#uploadfiles").val(''); 
					 return false;
				 }
				 setTimeout(function(){
							 keepSessionAlive(records, url);
					 }, 500);
			},
			error: function(e){
			
				 setTimeout(function(){
						keepSessionAlive(records, url);
				 }, 1000);	
				return false;
			}
		});
	}
	
});

	