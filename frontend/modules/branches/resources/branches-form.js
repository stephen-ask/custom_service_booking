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
	var countryflag = 0;
	var cityflag = 1;
	var mycountry = 0;
	var mycity;
	var singlecountry;
	var branchlat = '';
	var branchlng = '';
	var map = null;
	var marker = null;
	var branchzoomlevel;
	var branchid;
	var dataTable;
	
	if (jQuery('form.add-new-branch').length){
	if(totalcountry == 1){
	singlecountry = jQuery('.add-new-branch select[name="signup_country"] option:selected').val();	
	mycountry = jQuery('.add-new-branch select[name="signup_country"]').find(':selected').data('code');
	
	if(signupautosuggestion == false){
		var defaultcountry = jQuery('.add-new-branch select[name="signup_country"]').find(':selected').val();
		service_finder_get_cities(defaultcountry);
	}
	}
	}
	
	jQuery.fn.cityAutocomplete = function(options) {
        var autocompleteService = new google.maps.places.AutocompleteService();
        var predictionsDropDown = jQuery('<div class="city-autocomplete"></div>').appendTo('#autocity');
        var input = this;

        input.keyup(function() {
            var searchStr = jQuery(this).val();
            if (searchStr.length > 0) {
                var params = {
                    input: searchStr,
                    types: ['(cities)']
                };

				if(mycountry != 0){

					params.componentRestrictions = {country: mycountry}	
					
				}

                autocompleteService.getPlacePredictions(params, updatePredictions);
            } else {
                predictionsDropDown.hide();
            }
        });

        predictionsDropDown.delegate('div', 'click', function() {
			mycity = jQuery(this).text();
            input.val(jQuery(this).text());
            predictionsDropDown.hide();
        });

        jQuery(document).mouseup(function (e) {
            if (!predictionsDropDown.is(e.target) && predictionsDropDown.has(e.target).length === 0) {
                predictionsDropDown.hide();
            }
        });

        jQuery(window).resize(function() {
            updatePredictionsDropDownDisplay(predictionsDropDown, input);
        });

        updatePredictionsDropDownDisplay(predictionsDropDown, input);

        function updatePredictions(predictions, status) {
            if (google.maps.places.PlacesServiceStatus.OK != status) {
                predictionsDropDown.hide();
                return;
            }

            predictionsDropDown.empty();
            jQuery.each(predictions, function(i, prediction) {
                predictionsDropDown.append('<div>' + jQuery.fn.cityAutocomplete.transliterate(prediction.terms[0].value) + '</div');
            });

            predictionsDropDown.show();
        }
        return input;
    };
	
	jQuery.fn.cityAutocomplete.transliterate = function (s2) {
        s2 = String(s2);

        return s2;
    };

	function updatePredictionsDropDownDisplay(dropDown, input) {
        dropDown.css({
            'width': input.outerWidth(),
            'left': input.offset().left,
            'top': input.offset().top + input.outerHeight()
        });
    }
	
	jQuery('#addbranch').on('show.bs.modal', function (event) {
		if (jQuery('#signup_address').length && siteautosuggestion == true){
		service_finder_initSignupAutoComplete();
		}
		if (jQuery('#signup_city').length && siteautosuggestion == true){
		jQuery('#signup_city').cityAutocomplete();
		}
	});														 
	
	function service_finder_initSignupAutoComplete(){
	
	var city = '';
	var state = '';
	
				var address = document.getElementById('signup_address');
				if(!parseInt(allcountry)){
					if(countrycount == 1){
						var options = {
						  componentRestrictions: {country: allowedcountry}
						 };
						
						var my_address = new google.maps.places.Autocomplete(address, options);
					}else{
						var my_address = new google.maps.places.Autocomplete(address);
					}
				}else{
						var my_address = new google.maps.places.Autocomplete(address);
				}
		
				google.maps.event.addListener(my_address, 'place_changed', function() {
            var place = my_address.getPlace();
            
            // if no location is found
            if (!place.geometry) {
                return;
            }
            
			var $city = jQuery("#signup_city");
            var $state = jQuery("#signup_state");
            var $zipcode = jQuery("#signup_zipcode");
			var $country = jQuery("#signup_country");
			
            var country_long_name = '';
            var country_short_name = '';
            
            for(var i=0; i<place.address_components.length; i++){
                var address_component = place.address_components[i];
                var ty = address_component.types;

                for (var k = 0; k < ty.length; k++) {
                    if (ty[k] === 'locality' || ty[k] === "sublocality" || ty[k] === "sublocality_level_1"  || ty[k] === 'postal_town') {
                        city = address_component.long_name;
                    } else if (ty[k] === "administrative_area_level_1" || ty[k] === "administrative_area_level_2") {
                        $state.val(address_component.long_name);
						state = address_component.long_name;
                    } else if (ty[k] === 'postal_code') {
                        $zipcode.val(address_component.short_name);
                    } else if(ty[k] === 'country'){
						var countrycode = address_component.short_name;

						if(!parseInt(allcountry)){
							if(countrycount > 1){
								if(jQuery.inArray(countrycode,allowedcountries) > -1){
								countryflag = 0;
								}else{
								countryflag = 1;
								}
								jQuery('.add-new-branch').bootstrapValidator('revalidateField', 'signup_address');
							}
						}
                        country_long_name = address_component.long_name;
                        country_short_name = address_component.short_name;
						if(countryflag == 0){
						mycountry = address_component.short_name;
						$country.val(address_component.long_name);
						$city.val(city);
						mycity = city;
						jQuery('.add-new-branch').bootstrapValidator('revalidateField', 'signup_city');
						jQuery('.add-new-branch').bootstrapValidator('revalidateField', 'signup_country');
						jQuery('.sf-select-box').selectpicker('refresh');
						$city.removeAttr('readonly');
						$city.attr('placeholder',param.signup_city);
						}
                    }
                }
            }
			
            var address = jQuery("#signup_address").val();
			var new_address = address.replace(city,"");
            new_address = new_address.replace(state,"");
			
			new_address = new_address.replace(country_long_name,"");
            new_address = new_address.replace(country_short_name,"");
            new_address = jQuery.trim(new_address);
            
            
            new_address = new_address.replace(/,/g, '');
            new_address = new_address.replace(/ +/g," ");
			jQuery("#signup_address").val(new_address);
			jQuery('.add-new-branch').bootstrapValidator('revalidateField', 'signup_address');
			
        
         });
			}

	jQuery( "#signup_address" ).focus(function() {
	  jQuery('.pac-container').css('z-index','9999');
	  jQuery('.city-autocomplete').css('z-index','9999');
	});
	
	jQuery(document).on('click','.set-marker-popup-close',function(){
		jQuery('.set-marker-popup').css('display','none');
	});															   
	jQuery(document).on('click','.setmarker',function(){
     jQuery('.set-marker-popup').css('display','block');
	 
	 branchid = jQuery(this).data('id');
	 var zooml = jQuery(this).data('branchzoomlevel');

	 if(zooml == ""){
		zooml = 14;	 
	 }
	 
	 var data = {
	  "action": "get_branch_location",
	  "branchid": branchid,
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
						branchlat = parseFloat(data['lat']);
						branchlng = parseFloat(data['lng']);
						if(branchlat != "" && branchlng != ""){
						initMap(branchlat,branchlng,zooml);
						}else{
						defaultlat = parseFloat(defaultlat);
						defaultlng = parseFloat(defaultlng);
						initMap(defaultlat,defaultlng,defaultzoomlevel);	
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
	  branchzoomlevel = map.getZoom();
	  savemarkerposition();
	});
	
	google.maps.event.addListener(marker, 'dragend', function (event) {
		branchlat = event.latLng.lat();
		branchlng = event.latLng.lng();
		branchzoomlevel = map.getZoom();
		savemarkerposition();
	});
	}
	
	function toggleBounce() {
	if (marker.getAnimation() !== null) {
	  marker.setAnimation(null);
	} else {
	  marker.setAnimation(google.maps.Animation.BOUNCE);
	}
	}
	
	function savemarkerposition() {
		var data = {
		  "action": "save_marker_position",
		  "branchlat": branchlat,
		  "branchlng": branchlng,
		  "branchzoomlevel": branchzoomlevel,
		  "branchid": branchid,
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

							jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( ".branches-grid_wrapper" );	
							jQuery("html, body").animate({
								scrollTop: 0
							}, 1000);
							
							/*Reaload datatable after add new branch*/
							dataTable.ajax.reload(null, false);

						}else if(data['status'] == 'error'){

							jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( ".branches-grid_wrapper" );

						}
					}

				});
	}
	
	jQuery('#addbranch').on('hide.bs.modal', function() {
		service_finder_resetform();
	});
	
	function service_finder_resetform(){
		jQuery('.add-new-branch').bootstrapValidator('resetForm',true); // Reset form
		
		jQuery('.add-new-branch select[name="signup_city"]').parent('div').removeClass('has-error');
		jQuery('.add-new-branch select[name="signup_country"]').parent('div').removeClass('has-error');
		
		jQuery('.add-new-branch input[name="signup_apt"]').val('');
		jQuery('.add-new-branch input[name="signup_state"]').val('');
		jQuery('.add-new-branch input[name="signup_zipcode"]').val('');
		
		jQuery('.sf-select-box').selectpicker('refresh');
		
		jQuery(".alert-success,.alert-danger").remove();
	}
				  
	/*Provider Signup*/
	jQuery('.add-new-branch')
        .bootstrapValidator({
            message: param.not_valid,
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
				signup_address: {
					validators: {
						notEmpty: {
										message: param.signup_address	
									},
									callback: {
										message: param.allowed_country,
										callback: function(value, validator, $field) {
											if(countrycount > 1){
												if(countryflag == 1){
												return false;
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
				signup_city: {
					validators: {
						notEmpty: {
							message: param.signup_city
						}
					}
				},
				signup_country: {
					validators: {
						notEmpty: {
							message: param.signup_country
						}
					}
				},
            }
        })
		.on('click',  'input[name="add-service"]', function(e) {
			if(!parseInt(allcountry)){
			if(totalcountry == 1){
			jQuery('.add-new-branch select[name="signup_country"]').val(singlecountry);	
			}
			}

			if(signupautosuggestion == false){
			if(jQuery('.add-new-branch select[name="signup_city"] option:selected').val()==""){cityflag = 1;jQuery('.add-new-branch select[name="signup_city"]').parent('div').addClass('has-error').removeClass('has-success'); jQuery('form.add-new-branch').find('input[type="submit"]').prop('disabled', false);}else{cityflag = 0;jQuery('.add-new-branch select[name="signup_city"]').parent('div').removeClass('has-error').addClass('has-success'); jQuery('form.add-new-branch').find('input[type="submit"]').prop('disabled', false);}	
			}
			
			if(jQuery('.add-new-branch select[name="signup_country"] option:selected').val()==""){countryflag = 1;jQuery('.add-new-branch select[name="signup_country"]').parent('div').addClass('has-error').removeClass('has-success'); jQuery('form.add-new-branch').find('input[type="submit"]').prop('disabled', false);}else{countryflag = 0;jQuery('.add-new-branch select[name="signup_country"]').parent('div').removeClass('has-error').addClass('has-success'); jQuery('form.add-new-branch').find('input[type="submit"]').prop('disabled', false);}
			
	    })
		.on('error.field.bv', function(e, data) {
            data.bv.disableSubmitButtons(false); // disable submit buttons on errors
	    })
		.on('status.field.bv', function(e, data) {
            data.bv.disableSubmitButtons(false); // disable submit buttons on valid
        })
		.on('change', 'select[name="signup_country"]', function() {
			mycountry = jQuery(this).find(':selected').data('code');
			jQuery('#signup_city').val('');
			mycity = '';
			if(signupautosuggestion == false){
				var selectedcountry = jQuery(this).val();
				service_finder_get_cities(selectedcountry);
				
			}else{
			jQuery('#signup_city').removeAttr('readonly');
			}
			jQuery('#signup_city').attr('placeholder',param.signup_city);
			jQuery('.add-new-branch').bootstrapValidator('revalidateField', 'signup_city');
		})
        .on('success.form.bv', function(form) {
				if(signupautosuggestion == true){
				jQuery("#signup_city").val(mycity);
				jQuery('.add-new-branch').bootstrapValidator('revalidateField', 'signup_city');	
				if(mycity == ''){
					return false;	 
				}
				}
				jQuery('form.add-new-branch').find('input[type="submit"]').prop('disabled', false);
				 
				if(signupautosuggestion == false){
					if(cityflag==1){form.preventDefault();return false;}	
				}
				
				 // Prevent form submission
				form.preventDefault();
	
				// Get the form instance
				var $form = jQuery(form.target);
	
				// Get the BootstrapValidator instance
				var bv = $form.data('bootstrapValidator');

				var data = {
				  "action": "add_new_branch",
				  "user_id": user_id,
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
	
								if(data['status'] == 'success'){
	
									jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.add-new-branch" );	
									jQuery("html, body").animate({
										scrollTop: 0
									}, 1000);
									
									jQuery("#addbranch").modal('hide');
									
									/*Reaload datatable after add new branch*/
									dataTable.ajax.reload(null, false);
	
								}else if(data['status'] == 'error'){
	
									jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.add-new-branch" );
	
								}
							}
	
						});
				
		});
		
		function service_finder_get_cities(selectedcountry){

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
								jQuery('#signup_city').html(data);
								jQuery('#signup_city').removeAttr('readonly');
								jQuery('#signup_city').removeAttr('disabled');
								jQuery('.sf-select-box').selectpicker('refresh');
							}
	
						});
		}
		
		//Tabbing on My Account Page
		jQuery("#myTab a").click(function(e){
			e.preventDefault();
			jQuery(this).tab('show');
			var tabid = jQuery(this).attr('href');
			if(tabid == '#our-branches'){
				if ( ! jQuery.fn.DataTable.isDataTable( '#branches-grid' ) ) {	
				dataTable = jQuery('#branches-grid').DataTable( {

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
	
			data: {"action": "get_branches","user_id": user_id},
	
			error: function(){  // error handling
	
				jQuery(".branches-grid-error").html("");
	
				jQuery("#branches-grid").append('<tbody class="branches-grid-error"><tr><th colspan="3">'+param.no_data+'</th></tr></tbody>');
	
				jQuery("#branches-grid_processing").css("display","none");
	
				
	
			}
	
		}
	
		} );
				jQuery('.sf-select-box').selectpicker('refresh');
				}
			}
		});
		
		
		jQuery("#bulkBranchDelete").on('click',function() { // bulk checked

			var status = this.checked;
	
			jQuery(".deleteBranchRow").each( function() {
	
				jQuery(this).prop("checked",status);
	
			});
	
		});
	
		 
	
		jQuery('#deleteBranchTriger').on("click", function(event){ // triggering delete one by one
	
			
	
				  if( jQuery('.deleteBranchRow:checked').length > 0 ){
	
					  bootbox.confirm(param.are_you_sure, function(result) {
	
				  if(result){
		
					// at-least one checkbox checked
					var ids = [];
		
					jQuery('.deleteBranchRow').each(function(){
		
						if(jQuery(this).is(':checked')) { 
		
							ids.push(jQuery(this).val());
		
						}
		
					});
		
					var ids_string = ids.toString();  // array to string conversion 
		
					jQuery.ajax({
		
						type: "POST",
		
						url: ajaxurl,
		
						data: {action: "delete_branches", data_ids:ids_string},
		
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
	
});