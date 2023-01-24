// JavaScript Document
jQuery(document).ready(function() {
'use strict';								
	
	var countryflag = 0;
	var mycountry = 0;
	
	var role = jQuery('#role').val();
	if(role == 'Provider'){
		jQuery('.bx-cleaner').show();
	}else{
		jQuery('.bx-cleaner').hide();
	}
	
	if(signupautosuggestion == false){
		var defaultcountry = jQuery('select[name="signup_country"]').find(':selected').val();
		service_finder_get_cities(defaultcountry);
	}
	
	//Add/edit new user for providers extra fields show/hide
	jQuery('#createuser,#your-profile') .on('change', '#role', function() {
		var role = jQuery(this).val();
		if(role == 'Provider'){
			jQuery('.bx-cleaner').show();
		}else{
			jQuery('.bx-cleaner').hide();
		}
		if(role == 'Provider' || role == 'Customer'){
			jQuery('.profilepic-bx').show();
		}else{
			jQuery('.profilepic-bx').hide();
		}
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
							var selectedcity = jQuery('#signup_city').data('selectedcity');
							jQuery('#signup_city').val(selectedcity);
							jQuery('#signup_city').removeAttr('readonly');
							jQuery('#signup_city').removeAttr('disabled');
						}

					});
	}
	
	jQuery('#createuser,#your-profile').on('click', '#createusersub,#submit', function(){
		
		if(signupautosuggestion == true){
		jQuery("#signup_city").val(mycity);
		}
		
		var signup_category = jQuery("#signup_category").val();
		var signup_city = jQuery("#signup_city").val();
		var signup_country = jQuery("#signup_country").val();
		var role = jQuery("#role").val();
		if(quicksignup == false){
			if(role == "Provider"){
			if(signup_city == ""){
				alert('Please select city from suggestion');
				return false;	
			}else if(signup_country == ""){
				alert('Please select country');
				return false;
			}else if(!signup_category > 0){
				alert('Please select category');
				return false;
			}else if(role == "" && (currentrole == 'Provider' || currentrole == 'Customer')){
				alert('Please select role');
				return false;	
			}else{
				return true;			
			}
			}
		}else
		{
			
			if(role == "Provider"){
			if(!signup_category > 0){
	
				alert('Please select category');
	
				return false;
	
			}else if(role == "" && (currentrole == 'Provider' || currentrole == 'Customer')){
	
				alert('Please select role');
	
				return false;	
	
			}else{
	
				return true;			
	
			}
	
			}
			
		}
		
		if(role == ""){
			alert('Please select role');
			return false;	
		}else{
			return true;			
		}
		
	});
	
	//Add/edit new user for cleaners extra fields show/hide
	jQuery('select[name="signup_country"]') .on('change', function() {
		mycountry = jQuery(this).find(':selected').data('code');
		jQuery('#signup_city').val('');
		mycity = '';
		if(signupautosuggestion == false){
			var selectedcountry = jQuery(this).val();
			service_finder_get_cities(selectedcountry);
		}else{
		jQuery('#signup_city').removeAttr('readonly');
		jQuery('#signup_city').attr('placeholder','City (Please select city from auto suggestion)');
		}
		
	});
	
	jQuery.fn.cityAutocomplete = function(options) {
        var autocompleteService = new google.maps.places.AutocompleteService();
        var predictionsDropDown = jQuery('<div class="city-autocomplete"></div>').appendTo('.sf-admin-city');
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
	
	jQuery.fn.cityAutocomplete.transliterate = function (s) {
        s = String(s);
        return s;
    };
	function updatePredictionsDropDownDisplay(dropDown, input) {
        dropDown.css({
            'width': input.outerWidth(),
            'left': input.offset().left,
            'top': input.offset().top + input.outerHeight()
        });
    }
	
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
							}
						}
                        country_long_name = address_component.long_name;
                        country_short_name = address_component.short_name;
						if(countryflag == 0){
						mycountry = address_component.short_name;
						//$country.val(address_component.long_name);
						jQuery("#signup_country option:contains(" + address_component.long_name + ")").attr('selected', 'selected');
						$city.val(city);
						mycity = city;
						$city.removeAttr('readonly');
						$city.attr('placeholder','City (Please select city from auto suggestion)');
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
			
        
         });
			}
	if(signupautosuggestion == true && siteautosuggestion == true){
	  if (jQuery('#signup_address').length){
	  service_finder_initSignupAutoComplete();
	  }
	  
	  if (jQuery('#signup_city').length){
	  jQuery('#signup_city').cityAutocomplete();
	  }
	}
	
});// Document.ready END====================================================//
