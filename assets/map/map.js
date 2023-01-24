(function ( $ )
{
	'use strict';
	var countryflag = 0;
	var mycountry = 0;
	var mycity;
	var findflag = 0;
	var zoomlevel;
	
	mycity = jQuery("#city").val();
	
	if(signupautosuggestion){
	//Add/edit new user for cleaners extra fields show/hide
	jQuery('select[name="country"]') .on('change', function() {
		mycountry = jQuery(this).find(':selected').data('code');
		jQuery('#city').val('');
		mycity = '';
		jQuery('#city').removeAttr('readonly');
		jQuery('#city').attr('placeholder','City (Please select city from auto suggestion)');
	});
	
	jQuery('input[name="update-profile"]') .on('click', function() {
		jQuery("#city").val(mycity);	
		if(mycity == ''){
		jQuery('.user-update input[name="city"]').parent('div').addClass('has-error').removeClass('has-success');	
		return false;	 
		}
	});
	}
	
	jQuery.fn.cityAutocomplete = function(options) {
        var autocompleteService = new google.maps.places.AutocompleteService();
        var predictionsDropDown = jQuery('<div class="city-autocomplete"></div>').appendTo('#cityautosuggestion');
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
	
	jQuery("#city").cityAutocomplete();

	// Use function construction to store map & DOM elements separately for each instance
	var MapField = function ( $container )
	{
		this.$container = $container;
	};

	// Use prototype for better performance
	MapField.prototype = {
		// Initialize everything
		init              : function ()
		{
			this.initDomElements();
			this.initMapElements();

			this.initMarkerPosition();
			this.addListeners();
			this.autocomplete();
		},

		// Initialize DOM elements
		initDomElements   : function ()
		{
			this.canvas = this.$container.find( '.rwmb-map-canvas' )[0];
			this.$coordinate = this.$container.find( '.rwmb-map-coordinate' );
			this.$findButton = this.$container.find( '.rwmb-map-goto-address-button' );
			this.addressField = this.$findButton.val();
		},

		// Initialize map elements
		initMapElements   : function ()
		{
			var defaultLoc = $( this.canvas ).data( 'default-loc' ),
				latLng;
			var currentdefaultlat = jQuery('#lat').val();
			var currentdefaultlng = jQuery('#long').val();
			var providerzoomlevel = jQuery('#zoomlevel').val();
			if(providerzoomlevel == ""){

				providerzoomlevel = 14;	
			}

			defaultLoc = defaultLoc ? defaultLoc.split( ',' ) : [defaultlat, defaultlng];
			if(currentdefaultlat == "" && currentdefaultlng == ""){
				var currentdefaultlat = defaultlat;
				var currentdefaultlng = defaultlng;
			}
			latLng = new google.maps.LatLng( currentdefaultlat, currentdefaultlng ); // Initial position for map
			
			this.map = new google.maps.Map( this.canvas, {
				center           : latLng,
				zoom             : parseInt(providerzoomlevel),
				streetViewControl: 0,
				mapTypeId        : google.maps.MapTypeId.ROADMAP
			} );
			this.marker = new google.maps.Marker( { position: latLng, map: this.map, draggable: true } );
			google.maps.event.addListener(this.marker, 'dragend', function (event) {
				var addlat = event.latLng.lat();
				var addlng = event.latLng.lng();
				jQuery('#lat').val(addlat);
				jQuery('#long').val(addlng);
			});
			this.map.addListener('zoom_changed', function() {
			  zoomlevel = this.getZoom();
			  jQuery('#zoomlevel').val(zoomlevel);
			});
			this.geocoder = new google.maps.Geocoder();
		},

		// Initialize marker position
		initMarkerPosition: function ()
		{
			var coord = this.$coordinate.val(),
				l,
				zoom;

			if ( coord )
			{
				l = coord.split( ',' );
				this.marker.setPosition( new google.maps.LatLng( l[0], l[1] ) );

				zoom = l.length > 2 ? parseInt( l[2], 10 ) : 14;

				this.map.setCenter( this.marker.position );
				this.map.setZoom( zoom );
			}
			else if ( this.addressField )
			{
				this.geocodeAddress();
			}
		},

		// Add event listeners for 'click' & 'drag'
		addListeners      : function ()
		{
			var that = this;
			google.maps.event.addListener( this.map, 'click', function ( event )
			{
				that.marker.setPosition( event.latLng );
				that.updateCoordinate( event.latLng );
			} );
			google.maps.event.addListener( this.marker, 'drag', function ( event )
			{
				that.updateCoordinate( event.latLng );
			} );

			this.$findButton.on( 'click', function ()
			{
				findflag = 1;
				jQuery( '#city' ).val(mycity);
				var $ctiy = mycity;	
				var getcountry = jQuery( '#country' ).val();	
				
				
				if(mycity == "" && getcountry == ""){
					jQuery('.user-update input[name="city"]').parent('div').addClass('has-error').removeClass('has-success');
					jQuery('.user-update select[name="country"]').parent('div').addClass('has-error').removeClass('has-success');
					return false;
				}else if(mycity == "" && getcountry != ""){
					jQuery('.user-update select[name="country"]').parent('div').addClass('has-success').removeClass('has-error');
					jQuery('.user-update input[name="city"]').parent('div').addClass('has-error').removeClass('has-success');
					return false;	
				}else if(mycity != "" && getcountry == ""){
					jQuery('.user-update input[name="city"]').parent('div').addClass('has-success').removeClass('has-error');
					jQuery('.user-update .city-outer-bx').addClass('has-success').removeClass('has-error');
					jQuery('.user-update .city-outer-bx i.glyphicon-remove').remove();
					jQuery('.user-update .city-outer-bx small').remove();
					jQuery('.user-update select[name="country"]').parent('div').addClass('has-error').removeClass('has-success');
					return false;	
				}else if(mycity != "" && getcountry != ""){
					jQuery('.user-update input[name="city"]').parent('div').addClass('has-success').removeClass('has-error');
					jQuery('.user-update .city-outer-bx').addClass('has-success').removeClass('has-error');
					jQuery('.user-update .city-outer-bx i.glyphicon-remove').remove();
					jQuery('.user-update .city-outer-bx small').remove();
					jQuery('.user-update select[name="country"]').parent('div').addClass('has-success').removeClass('has-error');
					that.geocodeAddress();
					return false;	
				}
				
			} );

			/**
			 * Add a custom event that allows other scripts to refresh the maps when needed
			 * For example: when maps is in tabs or hidden div (this is known issue of Google Maps)
			 *
			 * @see https://developers.google.com/maps/documentation/javascript/reference
			 *      ('resize' Event)
			 */
			$( window ).on( 'rwmb_map_refresh', function()
			{
				if ( that.map )
				{
					google.maps.event.trigger( that.map, 'resize' );
				}
			} );
		},

		// Autocomplete address
		autocomplete      : function ()
		{
			var that = this;
			
			// No address field or more than 1 address fields, ignore
			if ( !this.addressField || this.addressField.split( ',' ).length > 1 )
			{
				return;
			}
			
			var address = document.getElementById('address');

			if(allowedcountry != ''){
			var options = {

					  componentRestrictions: {country: allowedcountry}

					 };
			var my_address = new google.maps.places.Autocomplete(address, options);
			}else{
			var my_address = new google.maps.places.Autocomplete(address);	
			}
			
			google.maps.event.addListener(my_address, 'place_changed', function() {

				var place = my_address.getPlace();
				// if no location is found
				if (!place.geometry) {
					return;
				}
				var city = '';
				var state = '';
				
				var latLng = new google.maps.LatLng( place.geometry.location.lat(), place.geometry.location.lng() );
				jQuery('#lat').val(place.geometry.location.lat());
				jQuery('#long').val(place.geometry.location.lng());
				that.map.setCenter( latLng );
				that.marker.setPosition( latLng );
				that.updateCoordinate( latLng );
				
				var $city = jQuery("#city");
				var $state = jQuery("#state");
				var $zipcode = jQuery("#zipcode");
				var $country = jQuery("#country");
				
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
						jQuery("#country option:contains(" + address_component.long_name + ")").attr('selected', 'selected'); 
						$city.val(city);
						mycity = city;
						jQuery('.user-update input[name="city"]').parent('div').addClass('has-success').removeClass('has-error');
						jQuery('.user-update select[name="country"]').parent('div').addClass('has-success').removeClass('has-error');
						jQuery('.sf-select-box').selectpicker('refresh');
						$city.removeAttr('readonly');
						$city.attr('placeholder','Please select city from suggestion');
						}
					
					}
				}
			}
			
			var address = jQuery("#address").val();
			
			address = address.toLowerCase();
			city = city.toLowerCase();
			state = state.toLowerCase();
			country_long_name = country_long_name.toLowerCase();
			country_short_name = country_short_name.toLowerCase();
			
			var new_address = address.replace(city,"");
			new_address = new_address.replace(state,"");
			
			new_address = new_address.replace(country_long_name,"");
			new_address = new_address.replace(country_short_name,"");
			new_address = jQuery.trim(new_address);
			
			
			new_address = new_address.replace(/,/g, '');
			new_address = new_address.replace(/ +/g," ");
			jQuery("#address").val(new_address);
			
			if(new_address == ''){
			jQuery('.user-update input[name="address"]').parent('div').addClass('has-error').removeClass('has-success');	
			return false;	 
			}else{
			jQuery('.user-update input[name="address"]').parent('div').removeClass('has-error').addClass('has-success');
			return true
			}
		 });

		},

		// Update coordinate to input field
		updateCoordinate  : function ( latLng )
		{
			this.$coordinate.val( latLng.lat() + ',' + latLng.lng() );
		},

		// Find coordinates by address
		// Find coordinates by address
		geocodeAddress    : function ()
		{
			var address,
				addressList = [],
				fieldList = this.addressField.split( ',' ),
				loop,
				that = this;

			for ( loop = 0; loop < fieldList.length; loop++ )
			{

				addressList[loop] = jQuery( '#' + fieldList[loop] ).val();
			}
			if(signupautosuggestion){
			addressList[loop + 1] = jQuery( '#city' ).val();	
			addressList[loop + 2] = jQuery( '#country' ).val();	
			}else{
			addressList[loop + 1] = jQuery( '#address' ).val();	
			addressList[loop + 2] = jQuery( '#city' ).val();	
			addressList[loop + 3] = jQuery( '#customcountry' ).val();		
			}
			
			address = addressList.join( ',' ).replace( /\n/g, ',' ).replace( /,,/g, ',' );
			
			if ( address )
			{
				this.geocoder.geocode( { 'address': address }, function ( results, status )
				{
					if ( status === google.maps.GeocoderStatus.OK )
					{
						if(!signupautosuggestion || findflag == 1){
						jQuery('#lat').val(results[0].geometry.location.lat);
						jQuery('#long').val(results[0].geometry.location.lng);
						
						jQuery('.sf-select-box').selectpicker('refresh');
						that.map.setCenter( results[0].geometry.location );
						that.marker.setPosition( results[0].geometry.location );
						that.updateCoordinate( results[0].geometry.location );
						}
					}
				} );
			}
		}
	};

	$( function ()
	{
		$( '.rwmb-map-field' ).each( function ()
		{
			var field = new MapField( $( this ) );
			field.init();

			$( this ).data( 'mapController', field );

		} );

		$( '.rwmb-input' ).on( 'clone', function ()
		{
			$( '.rwmb-map-field' ).each( function ()
			{
				var field = new MapField( $( this ) );
				field.init();

				$( this ).data( 'mapController', field );
			} );
		} );
	} );

})( jQuery );
