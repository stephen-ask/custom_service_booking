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
	
	var category_flag = 1;
	var countryflag = 0;
	var providerlat = '';
	var providerlng = '';
	var map = null;
	var marker = null;
	var videosarr = [];
	
	// Delete user avatar via Ajax
	jQuery( 'body' ).on( 'click', '.delete-user-avatar', function ()
	{
		 var userid = jQuery(this).data('userid');
		 
		 var data = {
				action  : 'delete_user_avatar',
				userid 	: userid,
			};
			
		 bootbox.confirm(param.are_you_sure, function(result) {

		 if(result){

		 jQuery.ajax({
       		type : "post",
         	dataType : "json",
         	url : ajaxurl,
         	data : data,
			beforeSend: function() {
				jQuery(".alert-success,.alert-danger").remove();
				jQuery('.loading-area').show();
			},
         	success: function(response) {
				jQuery('.loading-area').hide();
				if( response.success )
				{
					jQuery('.profileavtthumb').attr('src',response.data.defaultavatar);
					jQuery('.delete-user-avatar').addClass('hide');
					jQuery( "<div class='alert alert-success'>"+response.data.message+"</div>" ).insertBefore( "form.user-update" );
					return false;
				}else
				{
					jQuery( "<div class='alert alert-danger'>"+response.data.message+"</div>" ).insertBefore( "form.user-update" );
					return false;
				}
			}
         })
		 
		 }
		 
		 });
		
	});
	
	jQuery( 'body' ).on( 'click', '.delete-cover-image', function ()
	{
		 var userid = jQuery(this).data('userid');
		 
		 var data = {
				action  : 'delete_cover_image',
				userid 	: userid,
			};
			
		 bootbox.confirm(param.are_you_sure, function(result) {

		 if(result){

		 jQuery.ajax({
       		type : "post",
         	dataType : "json",
         	url : ajaxurl,
         	data : data,
			beforeSend: function() {
				jQuery(".alert-success,.alert-danger").remove();
				jQuery('.loading-area').show();
			},
         	success: function(response) {
				jQuery('.loading-area').hide();
				if( response.success )
				{
					jQuery('.profilecoverthumb').attr('src',response.data.defaultcoverthumb);
					jQuery('.delete-cover-image').addClass('hide');
					jQuery( "<div class='alert alert-success'>"+response.data.message+"</div>" ).insertBefore( "form.user-update" );
					return false;
				}else
				{
					jQuery( "<div class='alert alert-danger'>"+response.data.message+"</div>" ).insertBefore( "form.user-update" );
					return false;
				}
			}
         })
		 
		 }
		 
		 });
		
	});
	
	if(addressalert == false){
	jQuery("#sf-address-alert").delay(5000).fadeIn(500);
	}
	
	jQuery('body').on('click', '.membership-reactivate', function(){
		var providerid = jQuery(this).data("providerid");
		
		bootbox.confirm(param.are_you_sure_reactivate_membership, function(result) {
		if(result){
			var data = {
						  "action": "reactivate_membership",
						  "providerid": providerid
					};
			var formdata = jQuery.param(data);
			
			jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: formdata,
					dataType : 'json',
					beforeSend: function() {
						jQuery(".alert-success,.alert-danger").remove();
						jQuery('.loading-area').show();
					},
					success:function (data, textStatus) {
						jQuery('.loading-area').hide();	
						jQuery( ".membership-reactivate" ).remove();
						jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.user-update" );
						window.location.reload();
					}
			});			
		}
		}); 
		
	});
	
	// With JQuery
	jQuery("#serviceradius").bootstrapSlider({});
	
	jQuery('form.user-update input[name="password"]').val('');
	
	jQuery(document).on('click','.claimbusinessaction',function(){
		var providerid = jQuery(this).data('providerid');
		var claimstatus = jQuery(this).data('status');
		claimbusiness(claimstatus,providerid);
	});
	
	function claimbusiness(status,providerid){
		var data = {
		  "action": "claimbusiness",
		  "status": status,
		  "providerid": providerid,
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
							jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.user-update" );	
							if(status == 'enable'){
								jQuery( ".claimbusinessaction" ).data( "status","disable" );
								jQuery( ".claimbusinessaction" ).text( param.disbalebusiness );
							}else{
								jQuery( ".claimbusinessaction" ).data( "status","enable" );
								jQuery( ".claimbusinessaction" ).text( param.enablebusiness );
							}
						}
					
					}
	
				});
	}
	
	jQuery(document).on('click','.set-marker-popup-close',function(){
		jQuery('.set-marker-popup').css('display','none');
	});															   
	jQuery(document).on('click','#showmylocation',function(){
     jQuery('.set-marker-popup').css('display','block');
	 
	 var providerid = jQuery(this).data('providerid');
	 var address = jQuery('input[name="my_location"]').val();
	 var zooml = jQuery('#locationzoomlevel').val();	
	 if(zooml == ""){
		zooml = 14;	 
	 }
	 
	 var data = {
	  "action": "get_mycurrent_location",
	  "providerid": providerid,
	  "address": address,
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
						var providerlat = data['lat'];
						var providerlng = data['lng'];
						if(providerlat != "" && providerlng != ""){
						initMap(parseFloat(providerlat),parseFloat(providerlng)	,zooml);
						}else{
						initMap(parseFloat(defaultlat),parseFloat(defaultlng),2);
						}
						
					}
				
				}

			});
	 
	});
	
	function initMap(lat,lng,zoom) {
	var map = new google.maps.Map(document.getElementById('marker-map'), {
	  zoom: parseInt(zoom),
	  center: {lat: lat, lng: lng}
	});
	
	marker = new google.maps.Marker({
	  map: map,
	  draggable: true,
	  animation: google.maps.Animation.DROP,
	  position: {lat: lat, lng: lng}
	});
	marker.addListener('click', toggleBounce);
	
	map.addListener('zoom_changed', function() {
	  var locationzoomlevel = map.getZoom();
	  jQuery('#locationzoomlevel').val(locationzoomlevel);
	});
	
	google.maps.event.addListener(marker, 'dragend', function (event) {
		providerlat = event.latLng.lat();
		providerlng = event.latLng.lng();
	});
	}
	
	function toggleBounce() {
	if (marker.getAnimation() !== null) {
	  marker.setAnimation(null);
	} else {
	  marker.setAnimation(google.maps.Animation.BOUNCE);
	}
	}
	
	function create_video_array(){
		videosarr = [];
		jQuery('ul.rwmb-video-thumb li').each(function(){
				videosarr.push(jQuery(this).data('url'));
		});	
	}
	
	
	// User Registration Validation and Sublit Form For Provider
	jQuery('.user-update')
        .bootstrapValidator({
            message: param.not_valid,
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
				user_name: {
					validators: {
						notEmpty: {
							message: param.signup_user_name
						}
					}
				},
				first_name: {
					validators: {
						notEmpty: {
							message: param.signup_first_name
						}
					}
				},
				primary_category: {
					validators: {
						notEmpty: {
							message: param.primary_category
						}
					}
				},
				category: {
					validators: {
						notEmpty: {
							message: param.category
						}
					}
				},
				last_name: {
					validators: {
						notEmpty: {
							message: param.signup_last_name
						}
					}
				},
				user_email: {
					validators: {
						notEmpty: {
														message: param.req
													},
						emailAddress: {
							message: param.signup_user_email
						}
					}
				},
				address: {
					validators: {
						notEmpty: {
										message: param.signup_address	
									},
									callback: {
										message: param.allowed_country,
										callback: function(value, validator, $field) {
											if(signupautosuggestion == true)
											{
											if(countrycount > 1){
												if(countryflag == 1){
												return false;
												}else{
												return true;	
												}
											}else{
												return true;	
											}
											}else{
												return true;
											}
										}
									}
					}
				},
				city: {
					validators: {
						notEmpty: {
							message: param.signup_city
						}
					}
				},
				country: {
					validators: {
						notEmpty: {
							message: param.signup_country
						}
					}
				},
				password: {
					validators: {
						identical: {
							field: 'confirm_password',
							message: param.signup_password_confirm
						},
					}
				},
				confirm_password: {
					validators: {
						identical: {
							field: 'password',
							message: param.signup_password_confirm
						},
					}
				},
            }
        })
		.on('click',  'button[name="update-profile"]', function(e) {
			
			if(userrole == 'administrator'){
			jQuery('.user-update')
			.bootstrapValidator('removeField', 'user_email');
			}
															   
			var getaddress = jQuery("#address").val();
			var getcity = jQuery("#city").val();
			var getstate = jQuery("#state").val();
			var getcountry = jQuery("#country").val();
				
			getaddress = getaddress.replace(getcity,"");
			getaddress = getaddress.replace(getstate,"");
			getaddress = getaddress.replace(getcountry,"");
			getaddress = jQuery.trim(getaddress);
			
			
			getaddress = getaddress.replace(/,/g, '');
			getaddress = getaddress.replace(/ +/g," ");
			jQuery("#address").val(getaddress);															   
			
			jQuery('.user-update').bootstrapValidator('revalidateField', 'address');
			jQuery('.user-update').bootstrapValidator('revalidateField', 'city');
			jQuery('.user-update').bootstrapValidator('revalidateField', 'country');
			
		
			if(jQuery('.user-update select[name="country"] option:selected').val()==""){
				countryflag = 1;
				jQuery('.user-update select[name="country"]').parent('div').addClass('has-error').removeClass('has-success'); jQuery('form.user-update').find('button[type="submit"]').prop('disabled', false);
				return false;
			}else{
				countryflag = 0;
				jQuery('.user-update select[name="country"]').parent('div').removeClass('has-error').addClass('has-success'); jQuery('form.user-update').find('button[type="submit"]').prop('disabled', false);
			}
			
	    })
		.on('error.field.bv', function(e, data) {
            data.bv.disableSubmitButtons(false); // disable submit buttons on errors
	    })
		.on('status.field.bv', function(e, data) {
            data.bv.disableSubmitButtons(false); // disable submit buttons on valid
        })
		.on('change', 'input[type="radio"][name="service_perform"]', function() {
                var service_perform = jQuery(this).val();
                if (service_perform == 'provider_location' || service_perform == 'both') {
                    jQuery('#providerlocation_bx').show();
				} else if(service_perform == 'customer_location') {
					jQuery('#providerlocation_bx').hide();
				} 
        })
		.on('click', 'input[name="addvideo"]', function() {
               var embeded_code = jQuery('#embeded_code').val();
			   
			   if(embeded_code != '' && embeded_code != undefined){
			   	 var selectedcountry = jQuery(this).val();
			   var data = {
						  "action": "identify_video_type",
						  "embeded_code": embeded_code
						};
			
				var formdata = jQuery.param(data);

				jQuery.ajax({
									type: 'POST',
									url: ajaxurl,
									data: formdata,
									dataType: "json",
									success:function (data, textStatus) {
										if(data['videotype'] == 'youtube'){
										   var thumb = data['thumburl'];
										   jQuery('.sf-videothumbs ul.rwmb-video-thumb').append('<li data-url="'+embeded_code+'"><img src="'+thumb+'" width="150"><div class="rwmb-thumb-bar rwmb-image-bar"><a title="Delete" class="rwmb-delete-vthumb rwmb-delete-file" href="javascript:;">x</a></div></li>');
										   jQuery('#embeded_code').val('');
										   create_video_array();
										}else if(data['videotype'] == 'vimeo'){
											var thumb = data['thumburl'];
										   jQuery('.sf-videothumbs ul.rwmb-video-thumb').append('<li data-url="'+embeded_code+'"><img src="'+thumb+'" width="150"><div class="rwmb-thumb-bar rwmb-image-bar"><a title="Delete" class="rwmb-delete-vthumb rwmb-delete-file" href="javascript:;">x</a></div></li>');
										   jQuery('#embeded_code').val('');
										   create_video_array();
										}/*else if(data['videotype'] == 'facebook'){
											var thumb = data['thumburl'];
										   jQuery('.sf-videothumbs ul.rwmb-video-thumb').append('<li data-url="'+embeded_code+'"><img src="//graph.facebook.com/'+thumb+'/picture" width="150"><div class="rwmb-thumb-bar rwmb-image-bar"><a title="Delete" class="rwmb-delete-vthumb rwmb-delete-file" href="javascript:;">x</a></div></li>');
										   jQuery('#embeded_code').val('');
										   create_video_array();
										}*/		

									}
			
								});
				
				
			   }else{
				alert(param.video_req);   
			   }
        })
		.on('click', '.rwmb-thumb-bar a', function() {
               jQuery(this).closest('li').remove();
			   
			   create_video_array();
        })
		.on('change', 'input[type="radio"][name="google_calendar"]', function() {
                var google_calendar = jQuery(this).val();
                if (google_calendar == 'on') {
                    jQuery('#google_calendar_options').show();
					jQuery('.user-update')
					.bootstrapValidator('addField', 'google_calendar_id', {
						validators: {
							notEmpty: {
								message: param.req
							},
						}
					});

				} else if(google_calendar == 'off') {
					jQuery('#google_calendar_options').hide();
					jQuery('.user-update')
					.bootstrapValidator('removeField', 'google_calendar_id');
				} 
         })
		.on('change', 'select[name="category[]"]', function() {
				var provider_categories = '';															
				var primaryid = jQuery(this).data('primaryid');

				jQuery('select[name="category[]"] option:selected').each(function(){
					var catid = jQuery(this).val();
					var catname = jQuery(this).text();
					if(primaryid == catid){
						var checked = 'checked';	
					}else{
						var checked = '';
					}
					provider_categories += '<div class="radio sf-radio-checkbox"><input id="cat-'+catid+'" '+checked+' type="radio" name="primary_category" value="'+catid+'"><label for="cat-'+catid+'">'+catname+'</label></div>'																				  	
				});
				jQuery('#providers-category-bx').html(provider_categories)
				
				jQuery('.user-update')
				.bootstrapValidator('addField', 'primary_category', {
					validators: {
						notEmpty: {
														message: param.req
													},
					}
				});
         })
		.on('change', 'select[name="country"]', function() {
				var selectedcountry = jQuery(this).val();
				var data = {
						  "action": "load_cities_by_country",
						  "country": selectedcountry
						};
			
				var formdata = jQuery.param(data);

				jQuery.ajax({
									type: 'POST',
									url: ajaxurl,
									data: formdata,
									dataType: "text",
									success:function (data, textStatus) {
										jQuery('#city').html(data);
										jQuery('.sf-select-box').selectpicker('refresh');
									}
			
								});
		})
		.on('click', '.updategcal', function() {
				var google_client_id = jQuery('input[name="google_client_id"]').val();
				var google_client_secret = jQuery('input[name="google_client_secret"]').val();
				var providerid = jQuery(this).data('providerid');
				
				if(google_client_id == ""){
					alert(param.google_client_id_req);	
					return false;
				}
				
				if(google_client_secret == ""){
					alert(param.google_client_secret_req);	
					return false;
				}
				
				var data = {
						  "action": "update_gcal_info",
						  "google_client_id": google_client_id,
						  "google_client_secret": google_client_secret,
						  "providerid": providerid
						};
			
				var formdata = jQuery.param(data);

				jQuery.ajax({
									type: 'POST',
									url: ajaxurl,
									data: formdata,
									dataType: "json",
									beforeSend: function() {
										jQuery(".alert-success,.alert-danger").remove();
										jQuery('.loading-area').show();
									},
									success:function (data, textStatus) {
										jQuery('.loading-area').hide();
										if(data['status'] == 'success'){
											jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.user-update" );	
											jQuery( "#connectbtn" ).html(data['connectlink']);	
											jQuery( "#gcallist" ).remove();
											window.location = window.location.href.split("?")[0];
										}else if(data['status'] == 'error'){
											jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.user-update" );
										}
									}
			
								});
		})
        .on('success.form.bv', function(form) {
            // Prevent form submission
			
			tinyMCE.triggerSave();
			
            form.preventDefault();
			
			// Get the form instance
            var $form = jQuery(form.target);
			
			if(jQuery('select[name="category[]"] option:selected').val() > 0){
				category_flag = 0;
				jQuery('select[name="category[]"]').parent('div').removeClass('has-error').addClass('has-success'); 
			}else{
				category_flag = 1;
				jQuery('select[name="category[]"]').parent('div').addClass('has-error').removeClass('has-success'); 
			}
			if(category_flag==1){
				$form.find('button[type="submit"]').prop('disabled', false);
				return false;
			}
			
			//tinyMCE.triggerSave();
            
            // Get the BootstrapValidator instance
            var bv = $form.data('bootstrapValidator');
			
			var videocount = 1;	
			if (!jQuery('ul.rwmb-video-thumb li').length){
				var videocount = 0;	
			}
			
			jQuery('form.user-update input[name="providerlat"]').val(providerlat);
			jQuery('form.user-update input[name="providerlng"]').val(providerlng);
			jQuery('form.user-update input[name="videosarr"]').val(videosarr);
			jQuery('form.user-update input[name="videocount"]').val(videocount);
			
			jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						beforeSend: function() {
							jQuery(".alert-success,.alert-danger").remove();
							jQuery('.loading-area').show();
						},
						data:  new FormData(this),
						contentType: false,
						cache: false,
						processData:false,
						type: "POST",
						success:function (response, textStatus) {
							jQuery('.loading-area').hide();
							$form.find('button[type="submit"]').prop('disabled', false);
							if(response.data.status == 'success'){
								jQuery('.profileavtthumb').attr('src',response.data.profilethumb);
								jQuery('.profilecoverthumb').attr('src',response.data.coverthumb);
								jQuery( "<div class='alert alert-success'>"+response.data.suc_message+"</div>" ).insertBefore( "form.user-update" );	
								jQuery('select[name="category[]"]').attr('data-primaryid',response.data.primarycatid);
								jQuery('.sf-select-box').selectpicker('refresh');
								jQuery("html, body").animate({
										scrollTop: jQuery("#my-profile").offset().top
									}, 1000);
							}else if(response.data.status == 'error'){
								jQuery( "<div class='alert alert-danger'>"+response.data.err_message+"</div>" ).insertBefore( "form.user-update" );
								jQuery("html, body").animate({
										scrollTop: jQuery("#my-profile").offset().top
									}, 1000);
							}
						
						},
						error:function (data, textStatus) {
							jQuery('.loading-area').hide();
						}

					});
			
        });
	
	//Customer Update section
	jQuery('.customer-update')
        .bootstrapValidator({
            message: param.not_valid,
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
				first_name: {
					validators: {
						notEmpty: {
							message: param.signup_first_name
						}
					}
				},
				last_name: {
					validators: {
						notEmpty: {
							message: param.signup_last_name
						}
					}
				},
				user_email: {
					validators: {
						notEmpty: {
														message: param.req
													},
						emailAddress: {
							message: param.signup_user_email
						}
					}
				},
				password: {
					validators: {
						identical: {
							field: 'confirm_password',
							message: param.signup_password_confirm
						},
					}
				},
				confirm_password: {
					validators: {
						identical: {
							field: 'password',
							message: param.signup_password_confirm
						},
					}
				},
            }
        })
		 .on('success.form.bv', function(form) {
            // Prevent form submission
			
            form.preventDefault();
			
            // Get the form instance
            var $form = jQuery(form.target);
            // Get the BootstrapValidator instance
            var bv = $form.data('bootstrapValidator');
			
			var data = {
			  "action": "update_customer"
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
							$form.find('button[type="submit"]').prop('disabled', false);
							
							if(data['status'] == 'success'){
								jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.customer-update" );	
							}else if(data['status'] == 'error'){
								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.customer-update" );
							}
						
						}

					});
        });
	
	jQuery("#myTab a").on('shown.bs.tab', function(event){
		jQuery('.loading-area').hide();
	});
	
	jQuery(".sub-menu a").on('shown.bs.tab', function(event){
		jQuery('.loading-area').hide();
	});
	
	//Tabbing on My Account Page
	jQuery('body').on('click','#myTab a',function(e){
		e.preventDefault();
		jQuery("#myTab li").removeClass('active');
		var tabid = jQuery(this).attr('href');
		if(service_finder_getCookie('tabid') != tabid && tabid != '#schedule'){
			jQuery('.loading-area').show();
		}
		service_finder_setCookie('tabid', tabid); 
		
		jQuery(this).tab('show');
	});
	
	//Tabbing on My Account Page
	jQuery('body').on('click','.openidentitychk',function(e){
		 jQuery("#identityCheck").modal({

            backdrop: "static",

            keyboard: false

        });
	});
	
	//Identity Check
	jQuery('.identitycheck')
        .bootstrapValidator({
            message: param.not_valid,
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
				identityattachmentid: {
					validators: {
						notEmpty: {}
					}
				},
            }
        })
		 .on('success.form.bv', function(form) {
            // Prevent form submission
			
            form.preventDefault();
			
            // Get the form instance
            var $form = jQuery(form.target);
            // Get the BootstrapValidator instance
            var bv = $form.data('bootstrapValidator');
			
			var data = {
			  "action": "upload_identity"
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
								jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.identitycheck" );
								jQuery( "<div class='alert alert-info clear'>"+data['alert_message']+"</div>" ).insertAfter( "#sfidentityuploader-dragdrop" );
								jQuery('#sfidentityuploader-dragdrop').hide();
								jQuery('#identityCheck input[type="submit"]').hide();
							}else if(data['status'] == 'error'){
								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.identitycheck" );
							}
						
						},
						error:function (data, textStatus) {
							jQuery('.loading-area').hide();
							$form.find('input[type="submit"]').prop('disabled', false);
						}

					});
        });
	
  
  
  if (jQuery('#submit-fixed').length){
 /* var top = jQuery('#submit-fixed').offset().top - parseFloat(jQuery('#submit-fixed').css('marginTop').replace(/auto/, 0));
  var footTop = jQuery('#footer').offset().top - parseFloat(jQuery('#footer').css('marginTop').replace(/auto/, 0));
 
  var maxY = footTop - jQuery('#submit-fixed').outerHeight();
 
  jQuery(window).scroll(function(evt) {
   var y = jQuery(this).scrollTop();
   if (y > top) {
    if (y < maxY) {
     jQuery('#submit-fixed').addClass('fixed').removeAttr('style');
    } else {
     jQuery('#submit-fixed').removeClass('fixed').css({
      position: 'absolute',
      top: (maxY - top) + 'px'
     });
    }
   } else {
    jQuery('#submit-fixed').removeClass('fixed');
   }
  });*/
  }
  
  // grab the initial top offset of the navigation 
	if(jQuery('.address-alert').length){
		var stickyNavTop = jQuery('.address-alert').offset().top;
	}
	
	// our function that decides weather the navigation bar should have "fixed" css position or not.
	var stickyNav = function(){
	var scrollTop = jQuery(window).scrollTop(); // our current vertical position from the top
			 
		// if we've scrolled more than the navigation, change its position to fixed to stick to top,
		// otherwise change it back to relative
		if (scrollTop > stickyNavTop) { 
			jQuery('.address-alert').addClass('alert-fixed');
		} else {
			jQuery('.address-alert').removeClass('alert-fixed'); 
		}
	};

	stickyNav();
	// and run it again every time you scroll
	jQuery(window).scroll(function() {
		stickyNav();
	});
  
  
  });
  
  