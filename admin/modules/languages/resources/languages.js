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

jQuery('.update-languages')
.bootstrapValidator({
	message: param.not_valid,
	feedbackIcons: {
		valid: 'glyphicon glyphicon-ok',
		invalid: 'glyphicon glyphicon-remove',
		validating: 'glyphicon glyphicon-refresh'
	},
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
	  "action": "update_languages",
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
					 jQuery("html, body").animate({
						scrollTop: jQuery(".sedate-title").offset().top
					 }, 1000);
					if(data['status'] == 'success'){
						jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.update-languages" );	
					}else if(data['status'] == 'error'){
						jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.update-languages" );
						
					}
				
				}

			});
});

});