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

	jQuery('.add-new-labels')
	.bootstrapValidator({
	message: param.not_valid,
	feedbackIcons: {
		valid: 'glyphicon glyphicon-ok',
		invalid: 'glyphicon glyphicon-remove',
		validating: 'glyphicon glyphicon-refresh'
	},
	fields: {
				category: {
					validators: {
						notEmpty: {
							message: param.req
						}
					}
				},
				labelname: {
					validators: {
						notEmpty: {
							message: param.req
						}
					}
				},
			}
	})
	.on('error.field.bv', function(e, data) {
	data.bv.disableSubmitButtons(false); // disable submit buttons on errors
	})
	.on('status.field.bv', function(e, data) {
	data.bv.disableSubmitButtons(false); // disable submit buttons on valid
	})
	.on('change', 'select[name="category"]', function() {
			var categoryid = jQuery(this).val();
			
			var data = {
			  "action": "load_labels",
			  "categoryid": categoryid,
			};
			
			var formdata = jQuery.param(data);
			
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
					if(data['status'] == 'success'){
						 jQuery("input[name='labelname']").val('');
						 jQuery("#labels-list").html(data['html']);
					}
				
				}
	
			});
	})
	.on('success.form.bv', function(form) {
	// Prevent form submission
	form.preventDefault();	
	
	var $form = jQuery(form.target);
	// Get the BootstrapValidator instance
	var bv = $form.data('bootstrapValidator');
	
	var catid = jQuery("select[name='category']").val();
	
	var data = {
	  "action": "add_labels",
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
						jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.add-new-labels .modal-body" );	
						 jQuery("#labels-list").html(data['html']);
						 jQuery('.add-new-labels').bootstrapValidator('resetForm', true);
						 jQuery("select[name='category']").val(catid);
								
					}else if(data['status'] == 'error'){
						jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.add-new-labels .modal-body" );
						jQuery('.add-new-labels').bootstrapValidator('resetForm', true);
						
					}
				
				}
	
			});
	});
	
	//Delete Group
	jQuery('body').on('click', '.delete-label', function(){
												  
		var lid = jQuery(this).data('id');
		
		var $this = jQuery(this);
		
		bootbox.confirm(param.are_you_sure, function(result) {
		  if(result){
			  var data = {
			  "action": "delete_label",
			  "labelid": lid,
			};
			
			var data = jQuery.param(data);
			
			jQuery.ajax({

						type: 'POST',

						url: ajaxurl,
						
						data: data,
						
						dataType: "json",
						
						beforeSend: function() {
							jQuery('.loading-area').show();
							jQuery('.alert').remove();
						},

						success:function (data, textStatus) {
							jQuery('.loading-area').hide();

							if(data['status'] == 'success'){

								$this.closest('li').remove();
								
								jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.add-new-labels .modal-body" );

							}
						}

					});
			  }
		}); 
		
    });

	jQuery('#addratinglabels').on('hide.bs.modal', function (event) {
	  jQuery("#addratinglabels .alert").remove();
	  jQuery("input[name='labelname']").val('');
	  jQuery("select[name='category']").val('');
	  jQuery("#labels-list").html('');
	  /*Reaload datatable after add new city*/
	  dataTable.ajax.reload(updatepopupover, false);
	}); 

	dataTable = jQuery('#ratinglabels-grid').DataTable({
		"processing": true,
		"dom": '<"fixed-table-toolbar clearfix"lf><"table-responsive"t><"fixed-table-pagination clearfix"pi>',
		"order": [[ 0, "desc" ]],
		"columns": [
			{ "data": "labelid", "visible": false},
			{
				"class":          'delete-control',
				"orderable":      false,
				"data":           "delete",
				"defaultContent": ''
			},
			{ "data": "labelname"},
			{ "data": "category" }
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
				"orderable": false
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
			data: {"action": "get_labels"},
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
	
	jQuery("#bulkAdminRatingLabelsDelete").on('click',function() { // bulk checked
		var status = this.checked;
		jQuery(".deleteRatingLabelsRow").each( function() {
			jQuery(this).prop("checked",status);
		});
	});
	 
	jQuery('#deleteRatingLabelsTriger').on("click", function(event){ // triggering delete one by one
		if( jQuery('.deleteRatingLabelsRow:checked').length > 0 ){  // at-least one checkbox checked
			
		  bootbox.confirm(param.are_you_sure, function(result) {
	
		  if(result){
	
		   var ids = [];
			jQuery('.deleteRatingLabelsRow').each(function(){
				if(jQuery(this).is(':checked')) { 
					ids.push(jQuery(this).val());
				}
			});
			var ids_string = ids.toString();  // array to string conversion 
			jQuery.ajax({
				type: "POST",
				url: ajaxurl,
				data: {action: "delete_labels", data_ids:ids_string},
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
	/*End City Table*/

});