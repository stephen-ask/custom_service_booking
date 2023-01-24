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
	jQuery('body').on('click','.import-jobcategory',function(){
		var cattype = jQuery(this).data('type');
		var data = {
			  "action": "import_to_category",
			  "cattype": cattype
			};
			
		var formdata = jQuery.param(data);	
			
		jQuery.ajax({
            type:'POST',
            url:ajaxurl,
            data:formdata,
			dataType: "json",
			beforeSend: function() {
				jQuery('.alert').remove();
				jQuery('.provider-loading-image').show();
				jQuery('.provider-form-overlay').show();
			},
            success:function(data){
			   jQuery('.provider-loading-image').hide();
			   jQuery('.provider-form-overlay').hide();
			   if(data['status'] == 'success'){
				 jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( ".provider-import-wrap" );		   
			   }else{
				 jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( ".provider-import-wrap" );		     
			   }

            }
        });
		
	});	
});

	