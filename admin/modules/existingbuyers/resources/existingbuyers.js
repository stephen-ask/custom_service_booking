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
	jQuery('#manageshortcodes').bootstrapToggle();
	
	jQuery('#manageshortcodes').change(function() {
        var option = jQuery(this).prop('checked');
		
		if(option == true)
		{
			option = 'yes';	
		}else{
			option = 'no';	
		}
	  
	    var data = {
		  "action": "manage_shortcodes",
		  option: option
		};
		
		var formdata = jQuery.param(data);
		
		jQuery.ajax({
	
					type: 'POST',
	
					url: ajaxurl,
					
					dataType: "json",
					
					beforeSend: function() {
						jQuery('.provider-loading-image').show();
						jQuery('.provider-form-overlay').show();
					},
					
					data: formdata,
	
					success:function (data, textStatus) {
						 jQuery('.provider-loading-image').hide();
				 		 jQuery('.provider-form-overlay').hide();
						if(data['status'] == 'success'){
							jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.existing-buyers1" );	
						}else if(data['status'] == 'error'){
							jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.existing-buyers1" );
							
						}
					
					}
	
				});
    });
	
	jQuery('body').on('click', '.updatecitytaxonomy', function(){
		var data = {
		  "action": "updatecitytaxonomy",
		};
		
		var formdata = jQuery.param(data);
		
		jQuery.ajax({
	
					type: 'POST',
	
					url: ajaxurl,
					
					dataType: "json",
					
					beforeSend: function() {
						jQuery('.provider-loading-image').show();
						jQuery('.provider-form-overlay').show();
					},
					
					data: formdata,
	
					success:function (data, textStatus) {
						jQuery('.provider-loading-image').hide();
				 		jQuery('.provider-form-overlay').hide();
						if(data['status'] == 'success'){
							jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.existing-buyers2" );	
						}else if(data['status'] == 'error'){
							jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.existing-buyers2" );
							
						}
					
					}
	
				});
	});
});

	