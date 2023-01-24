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
  
    jQuery('#customers-grid').on('click', '.addtowallet', function(){
			var user_id = jQuery(this).attr('data-id');
			bootbox.dialog({
                title: "Add Balance to Wallet",
                message: '<div class="row">  ' +
                    '<div class="col-md-12"> ' +
                    '<form class="form-horizontal addtowallet-form"> ' +
                    '<div class="form-group"> ' +
                    '<label class="col-md-4 control-label" for="name">Please Enter Amount</label> ' +
                    '<div class="col-md-4"> ' +
                    '<input name="amount" type="text" placeholder="Add balance to wallet" class="form-control input-md"> ' +
                    '</div> ' +
					
					'<div class="form-group"> ' +
                    '<div class="col-md-4"> ' +
                    '<input name="submit" type="submit" class="form-control btn btn-success" value="Add to Wallet"> ' +
                    '</div> ' +
					
                    '</div> ' +
                    '</form> </div>  </div>',
                buttons: {
                    success: {
                        label: "Cancel",
                        className: "btn-danger",
                        callback: function () {
                        }
                    }
                }
            })
			.on('shown.bs.modal',function () {
                jQuery('.addtowallet-form')
							.bootstrapValidator({
								message: 'This value is not valid',
								feedbackIcons: {
									valid: 'glyphicon glyphicon-ok',
									invalid: 'glyphicon glyphicon-remove',
									validating: 'glyphicon glyphicon-refresh'
								},
								fields: {
									featured_amount: {
										validators: {
											notEmpty: {
												message: param.req
											},
											digits: {message: param.only_digits},
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
							.on('success.form.bv', function(form) {
								// Prevent form submission
								form.preventDefault();	

								var $form = jQuery(form.target);
								// Get the BootstrapValidator instance
								var bv = $form.data('bootstrapValidator');
								
								var data = {
								  "action": "addtowallet",
								  "user_id": user_id
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
													jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.addtowallet-form" );	
													window.setTimeout(function(){
														bootbox.hideAll();
													}, 3000); // 3 seconds expressed in milliseconds
													
													/*Reaload datatable after add new service*/
													dataTable.ajax.reload(updatepopupover, false);
															
												}else if(data['status'] == 'error'){
													jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.addtowallet-form" );
													
												}
												
												
											
											}
					
										});
								
							});	

            });
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
  
	/*Start Customers Table*/
	//Display Customers in Data Table
	var dataTable = jQuery('#customers-grid').DataTable({
		"processing": true,
		"serverSide": true,
		"dom": '<"fixed-table-toolbar clearfix"lf><"table-responsive"t><"fixed-table-pagination clearfix"pi>',
		"order": [[ 0, "desc" ]],
		"columns": [
			{ "data": "customerid", "visible": false},
			{
				"class":          'delete-control',
				"orderable":      false,
				"data":           "delete",
				"defaultContent": ''
			},
			{ "data": "customername" },
			{ "data": "phone" },
			{ "data": "email" },
			{ "data": "city" },
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
				"orderable": false,
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
			data: {"action": "get_customers"},
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
	
	//Bulk Customers Delete
	jQuery("#bulkCustomersDelete").on('click',function() { // bulk checked
        var status = this.checked;
        jQuery(".deleteCustomersRow").each( function() {
            jQuery(this).prop("checked",status);
        });
    });
     
    //Single Customers Delete
	jQuery('#deleteCustomersTriger').on("click", function(event){ // triggering delete one by one
        if( jQuery('.deleteCustomersRow:checked').length > 0 ){  // at-least one checkbox checked
		
		bootbox.confirm(param.are_you_sure, function(result) {

		  if(result){

               var ids = [];
            jQuery('.deleteCustomersRow').each(function(){
                if(jQuery(this).is(':checked')) { 
                    ids.push(jQuery(this).val());
                }
            });
            var ids_string = ids.toString();  // array to string conversion 
            jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data: {action: "delete_customers", data_ids:ids_string},
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
	/*End Customers Table*/
	
	/*Toggle Column*/
	jQuery('a.toggle-vis').on( 'click', function (e) {
        e.preventDefault();
 
        // Get the column API object
        var column = dataTable.column( jQuery(this).attr('data-column') );
 
        // Toggle the visibility
        column.visible( ! column.visible() );
    } );
	
	
  });