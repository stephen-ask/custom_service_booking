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
	var quotedataTable;
	
	if(quotationtab == "yes"){
		load_quotation_datatable('#quotation');
		if(viewquoteid > 0)
		{
		view_quote_details(viewquoteid);
		}
	}
	
	function view_quote_details(quoteid){
		
		jQuery('#quotation-grid_wrapper').addClass('hidden');
		jQuery('#quotation-details').removeClass('hidden');
		
		var data = {
		  "action": "quotation_details",
		  "quoteid": quoteid,
		  "user_id": user_id
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
	jQuery('body').on('click', '.viewquotation', function(){
												  
		var quoteid = jQuery(this).data('quoteid');
		view_quote_details(quoteid);
		
    });
	
	//Close details
	jQuery('body').on('click', '.closequotedetails', function(){
		jQuery('#quotation-details').addClass('hidden fade in');
		jQuery('#quotation-grid_wrapper').removeClass('hidden');
	});
	
	
	//Tabbing on My Account Page
	jQuery("#myTab a").click(function(e){
		e.preventDefault();
		jQuery(this).tab('show');
		var tabid = jQuery(this).attr('href');
		load_quotation_datatable(tabid);
	});
	
	function load_quotation_datatable(tabid){
		if(tabid == '#quotation'){
			if ( ! jQuery.fn.DataTable.isDataTable( '#quotation-grid' ) ) {
			quotedataTable = jQuery('#quotation-grid').DataTable( {
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
				data: {"action": "get_quotations","user_id": user_id},
				error: function(){  // error handling
					jQuery(".quotation-grid-error").html("");
					jQuery("#quotation-grid").append('<tbody class="quotation-grid-error"><tr><th colspan="3">'+param.no_data+'</th></tr></tbody>');
					jQuery("#quotation-grid_processing").css("display","none");
					
				}
			}
			} );
			jQuery('.sf-select-box').selectpicker('refresh');
			}
		}
	}
	
	jQuery('body').on('click', '.quotereply', function(){
															
		jQuery('.loading-area').show();												

        // Get the record's ID via attribute
        var quoteid = jQuery(this).data('quoteid');
		var userid = jQuery(this).data('userid');

		var data = {
			  "action": "load_quote_reply",
			  "quoteid": quoteid,
			  "userid": userid
			};

	  var formdata = jQuery.param(data);

	  jQuery.ajax({
		type: 'POST',
		url: ajaxurl,
		data: formdata,
		dataType: "json",
		success:function (data, textStatus) {
		jQuery('#editquotationreply')
			.find('[name="quoteid"]').val(quoteid).end()
			.find('[name="quote_reply"]').val(data['reply']).end()
			.find('[name="quote_price"]').val(data['quote_price']).end();

		// Show the dialog
		bootbox
			.dialog({
				title: param.quote_reply,
				message: jQuery('#editquotationreply'),
				show: false // We will show it manually later
			})
			.on('shown.bs.modal', function() {
			tinymce.EditorManager.execCommand('mceAddEditor', true, "quote_reply");
				jQuery('.loading-area').hide();
				jQuery('#editquotationreply')
					.show()                             // Show the login form
					.bootstrapValidator('resetForm'); // Reset form
			})
			.on('hide.bs.modal', function(e) {
				// Therefor, we need to backup the form
				tinymce.EditorManager.execCommand('mceRemoveEditor', true, "quote_reply");

				jQuery('#editquotationreply').hide().appendTo('body');
			})
			.modal('show');
			}

		});

    });
	
	//Edit Experience
    jQuery('.quotation-reply')
        .bootstrapValidator({
            message: param.not_valid,
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
				quote_price: {
					validators: {
						notEmpty: {
							message: param.req
						}
					}
				},
				quote_reply: {
					validators: {
						notEmpty: {
							message: param.req
						}
					}
				},
            }
        })
        .on('success.form.bv', function(form) {
            // Prevent form submission
			
            tinyMCE.triggerSave();
			form.preventDefault();

            // Get the form instance
            var $form = jQuery(form.target);
            // Get the BootstrapValidator instance
            var bv = $form.data('bootstrapValidator');
			
			var data = {
			  "action": "update_quote_reply",
			  "userid": user_id
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
							if(data['status'] == 'success'){
								/*Close the popup window*/
								$form.parents('.bootbox').modal('hide');

								/*Reaload datatable after add new service*/
								quotedataTable.ajax.reload(null, false);
										
							}else if(data['status'] == 'error'){
								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.quotation-reply" );
							}
							
							
						
						}

					});
			
    });
	
  });

