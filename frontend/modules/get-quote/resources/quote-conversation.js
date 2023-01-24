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
	
	var fromid;
	var toid;
	var quoteid;
	
  	/*Close send conversation popup*/
    jQuery('.sf-serach-result-close').click(function(){
		jQuery(".messenger-popup-wrap").animate({"top":"100%"}, "slow");
		jQuery(".messenger-popup-overlay").fadeOut(100);
    });
	
	/*Scroller for mesage window*/
	jQuery(".messenger-popup-body").mCustomScrollbar({
		theme: "minimal"
	});
	
	/*Click on start conversation button*/
	jQuery( 'body' ).on( 'click', '.quoteconversation', function ()
	{
		jQuery(".messenger-popup-wrap").animate({"top":"0"}, "slow");
		jQuery(".messenger-popup-overlay").fadeIn(100);
		
		fromid = jQuery( this ).data('fromid');
		toid = jQuery( this ).data('toid');
		quoteid = jQuery( this ).data('quoteid');
		
		empty_message_box();
		
		load_conversation();
	});
	
	/*Load conversation*/
	function empty_message_box()
	{
		jQuery('textarea[name="message"]').val('');
	}
	
	/*Load conversation*/
	function load_conversation()
	{
		var data = {
		  action: 'load_conversation',
		  fromid: fromid,
		  toid: toid,
		  quoteid: quoteid
		};
		
		var formdata = jQuery.param(data);
		
		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			dataType: "json",
			beforeSend: function() {
				//jQuery(".alert-success,.alert-danger").remove();
				//jQuery('.loading-area').show();
			},
			data: formdata,
			success:function (response) {
				//jQuery('.loading-area').hide();
				if( response.success )
				{
					jQuery('#conversationdata').html(response.data.html);
				}
				
			},
			error:function (data, textStatus) {
				//jQuery('.loading-area').hide();
			}
		});	
	}
	
	/*Submit message form*/
	jQuery('#conversationform')
	.bootstrapValidator({
		message: param.not_valid,
		feedbackIcons: {
			valid: 'glyphicon glyphicon-ok',
			invalid: 'glyphicon glyphicon-remove',
			validating: 'glyphicon glyphicon-refresh'
		},
		fields: {
			message: {
				validators: {
					notEmpty: {}
				}
			},
		}
	})
	.on('error.field.bv', function(e, data) {
		data.bv.disableSubmitButtons(false);
	})
	.on('status.field.bv', function(e, data) {
		data.bv.disableSubmitButtons(false);
	})
	.on('success.form.bv', function(form) {
		form.preventDefault();
		
		var $form = jQuery(form.target);
		
		$form.find('input[type="submit"]').prop('disabled', false);
		
		var $message = jQuery('textarea[name="message"]').val();
		
		jQuery('#conversationdata').append('<li class="messenger-popup-user-chat">'+$message+'</li>');
		
		var data = {
		  action: 'send_quote_message',
		  fromid: fromid,
		  toid: toid,
		  quoteid: quoteid
		};
		
		var formdata = jQuery($form).serialize() + "&" + jQuery.param(data);
		
		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			dataType: "json",
			beforeSend: function() {
				//jQuery(".alert-success,.alert-danger").remove();
				//jQuery('.loading-area').show();
			},
			data: formdata,
			success:function (response) {
				empty_message_box();
				$form.find('input[type="submit"]').prop('disabled', false);
				jQuery('.loading-area').hide();
			},
			error:function (data, textStatus) {
				//jQuery('.loading-area').hide();
				$form.find('input[type="submit"]').prop('disabled', false);
			}
		});
	});
});/*= Window Load END ========================================================*/