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
	var articlesdataTable;
	
	jQuery('#addarticles').on('show.bs.modal', function (event) {
		jQuery('body').addClass('bs-modal-open');
	});
	
	jQuery('#addarticles').on('hidden.bs.modal', function (event) {
		jQuery('body').removeClass('bs-modal-open');
	});
	
	//Add Article
    jQuery('.add-articles')
        .bootstrapValidator({
            message: param.not_valid,
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
				article_title: {
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
			  "action": "add_articles",
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
								/*Close the popup window*/
								
								jQuery('#addarticles').modal('hide');
								
								/*Reaload datatable after add new article*/
								articlesdataTable.ajax.reload(null, false);
										
							}else if(data['status'] == 'error'){
								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.add-articles" );
							}
							
							
						
						}

					});
			
    });
		
	//Edit Article
    jQuery('.edit-article')
        .bootstrapValidator({
            message: param.not_valid,
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
				article_title: {
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
			  "action": "update_article",
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
								articlesdataTable.ajax.reload(null, false);
										
							}else if(data['status'] == 'error'){
								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.edit-article" );
							}
							
							
						
						}

					});
			
    });
		
	//Tabbing on My Account Page
	jQuery("#myTab a").click(function(e){
		e.preventDefault();
		jQuery(this).tab('show');
		var tabid = jQuery(this).attr('href');
		if(tabid == '#articles'){
			//jQuery('.loading-area').hide();
			if ( ! jQuery.fn.DataTable.isDataTable( '#articles-grid' ) ) {
			articlesdataTable = jQuery('#articles-grid').DataTable( {
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
				data: {"action": "get_articles","user_id": user_id},
				error: function(){  // error handling
					jQuery(".articles-grid-error").html("");
					jQuery("#articles-grid").append('<tbody class="articles-grid-error"><tr><th colspan="3">'+param.no_data+'</th></tr></tbody>');
					jQuery("#articles-grid_processing").css("display","none");
					
				}
			}
			} );
			jQuery('.sf-select-box').selectpicker('refresh');
			}
		}
	});
	
	jQuery("#bulkArticlesDelete").on('click',function() { // bulk checked
        var status = this.checked;
        jQuery(".deleteArticleRow").each( function() {
            jQuery(this).prop("checked",status);
        });
    });
     
    jQuery('#deleteArticlesTriger').on("click", function(event){ // triggering delete one by one
         
			  if( jQuery('.deleteArticleRow:checked').length > 0 ){
				  bootbox.confirm(param.are_you_sure, function(result) {
		  if(result){
				  // at-least one checkbox checked
            var ids = [];
            jQuery('.deleteArticleRow').each(function(){
                if(jQuery(this).is(':checked')) { 
                    ids.push(jQuery(this).val());
                }
            });
            var ids_string = ids.toString();  // array to string conversion 
            jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data: {action: "delete_article", data_ids:ids_string, "user_id": user_id},
				dataType: "json",
                success: function(data, textStatus) {
                    articlesdataTable.draw(); // redrawing datatable
                },
                async:false
            });
        
		 }
		});
		}else{
				bootbox.alert(param.select_checkbox);
		}
		  
    });	
	
	jQuery('#addarticles').on('hide.bs.modal', function() {
		jQuery('.add-articles').bootstrapValidator('resetForm',true); // Reset form
		jQuery('select[name="categoryid"]').val('');
		jQuery('ul.rwmb-uploaded').html('');
		jQuery('#articlefeatured-dragdrop').removeClass('hidden');
		tinyMCE.activeEditor.setContent('');
		jQuery('.sf-select-box').selectpicker('refresh');
	});
	
	jQuery('body').on('click', '.editArticle', function(){
															
		jQuery('.loading-area').show();												

        // Get the record's ID via attribute
        var articleid = jQuery(this).attr('data-id');

		var data = {
			  "action": "load_article",
			  "articleid": articleid
			};

	  var formdata = jQuery.param(data);

	  jQuery.ajax({
		type: 'POST',
		url: ajaxurl,
		data: formdata,
		dataType: "json",
		success:function (data, textStatus) {
		jQuery('#editarticle')
			.find('[name="article_title"]').val(data['article_title']).end()
			.find('[name="articleid"]').val(articleid).end()
			.find('select[name="categoryid"]').val(data['category_id']).end()
			.find('.rwmb-uploaded').html(data['imagehtml']).end()
			.find('#articlefeatureedit-dragdrop').removeClass('hidden').end()
			.find('#articlefeatureedit-dragdrop').addClass(data['hiddenclass']).end()
			.find('[name="edit_article_description"]').val(data['article_descrption']).end();
			
		jQuery('.sf-select-box').selectpicker('refresh');

		// Show the dialog
		bootbox
			.dialog({
				title: param.edit_article,
				message: jQuery('#editarticle'),
				show: false // We will show it manually later
			})
			.on('shown.bs.modal', function() {
			tinymce.EditorManager.execCommand('mceAddEditor', true, "edit_article_description");
				jQuery('.loading-area').hide();
				jQuery('#editarticle')
					.show()                             // Show the login form
					.bootstrapValidator('resetForm'); // Reset form
			})
			.on('hide.bs.modal', function(e) {
				// Therefor, we need to backup the form
				tinymce.EditorManager.execCommand('mceRemoveEditor', true, "edit_article_description");

				jQuery('#editarticle').hide().appendTo('body');
			})
			.on('show.bs.modal', function() {
			jQuery('body').addClass('bs-modal-open');
			})
			.on('hidden.bs.modal', function() {
			jQuery('body').removeClass('bs-modal-open');
			})
			.modal('show');
			}

		});

    });
	
  });