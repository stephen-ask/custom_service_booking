// JavaScript Document

jQuery(document).ready(function() {

'use strict';								


	/*Package 0(Trial Package)*/

	if(!jQuery('#service_finder_options_package0-capabilities_booking_0').is(':checked')) { 

		jQuery('#service_finder_options_package0-subcapabilities_invoice_0').prop("checked", false);

		jQuery('#service_finder_options_package0-subcapabilities_invoice_0').attr("disabled", true);

		

		jQuery('#service_finder_options_package0-subcapabilities_availability_1').prop("checked", false);

		jQuery('#service_finder_options_package0-subcapabilities_availability_1').attr("disabled", true);

		

		jQuery('#service_finder_options_package0-subcapabilities_staff-members_2').prop("checked", false);

		jQuery('#service_finder_options_package0-subcapabilities_staff-members_2').attr("disabled", true);

	}

	/*Package 1*/

	if(!jQuery('#service_finder_options_package1-capabilities_booking_0').is(':checked')) { 

		jQuery('#service_finder_options_package1-subcapabilities_invoice_0').prop("checked", false);

		jQuery('#service_finder_options_package1-subcapabilities_invoice_0').attr("disabled", true);

		

		jQuery('#service_finder_options_package1-subcapabilities_availability_1').prop("checked", false);

		jQuery('#service_finder_options_package1-subcapabilities_availability_1').attr("disabled", true);

		

		jQuery('#service_finder_options_package1-subcapabilities_staff-members_2').prop("checked", false);

		jQuery('#service_finder_options_package1-subcapabilities_staff-members_2').attr("disabled", true);

	}

	/*Package 2*/

	if(!jQuery('#service_finder_options_package2-capabilities_booking_0').is(':checked')) { 

		jQuery('#service_finder_options_package2-subcapabilities_invoice_0').prop("checked", false);

		jQuery('#service_finder_options_package2-subcapabilities_invoice_0').attr("disabled", true);

		

		jQuery('#service_finder_options_package2-subcapabilities_availability_1').prop("checked", false);

		jQuery('#service_finder_options_package2-subcapabilities_availability_1').attr("disabled", true);

		

		jQuery('#service_finder_options_package2-subcapabilities_staff-members_2').prop("checked", false);

		jQuery('#service_finder_options_package2-subcapabilities_staff-members_2').attr("disabled", true);

	}

	/*Package 3*/

	if(!jQuery('#service_finder_options_package3-capabilities_booking_0').is(':checked')) { 

		jQuery('#service_finder_options_package3-subcapabilities_invoice_0').prop("checked", false);

		jQuery('#service_finder_options_package3-subcapabilities_invoice_0').attr("disabled", true);

		

		jQuery('#service_finder_options_package3-subcapabilities_availability_1').prop("checked", false);

		jQuery('#service_finder_options_package3-subcapabilities_availability_1').attr("disabled", true);

		

		jQuery('#service_finder_options_package3-subcapabilities_staff-members_2').prop("checked", false);

		jQuery('#service_finder_options_package3-subcapabilities_staff-members_2').attr("disabled", true);

	}

	

	/*Package 0(Trial Package)*/

	jQuery('body').on('click', '#service_finder_options_package0-capabilities_booking_0', function(){

		if(jQuery(this).is(':checked')) { 

			jQuery('#service_finder_options_package0-subcapabilities_invoice_0').removeAttr("disabled");

			

			jQuery('#service_finder_options_package0-subcapabilities_availability_1').removeAttr("disabled");

			

			jQuery('#service_finder_options_package0-subcapabilities_staff-members_2').removeAttr("disabled");

		}else{

			jQuery('#service_finder_options_package0-subcapabilities_invoice_0').prop("checked", false);
			jQuery('input[name="service_finder_options[package0-subcapabilities][invoice]"]').val(0);

			jQuery('#service_finder_options_package0-subcapabilities_invoice_0').attr("disabled", true);

			

			jQuery('#service_finder_options_package0-subcapabilities_availability_1').prop("checked", false);
			jQuery('input[name="service_finder_options[package0-subcapabilities][availability]"]').val(0);

			jQuery('#service_finder_options_package0-subcapabilities_availability_1').attr("disabled", true);

			

			jQuery('#service_finder_options_package0-subcapabilities_staff-members_2').prop("checked", false);
			jQuery('input[name="service_finder_options[package0-subcapabilities][staff-members]"]').val(0);

			jQuery('#service_finder_options_package0-subcapabilities_staff-members_2').attr("disabled", true);

		}

	});

	

	/*Package 1*/

	jQuery('body').on('click', '#service_finder_options_package1-capabilities_booking_0', function(){

		if(jQuery(this).is(':checked')) { 

			jQuery('#service_finder_options_package1-subcapabilities_invoice_0').removeAttr("disabled");

			

			jQuery('#service_finder_options_package1-subcapabilities_availability_1').removeAttr("disabled");

			

			jQuery('#service_finder_options_package1-subcapabilities_staff-members_2').removeAttr("disabled");

		}else{

			jQuery('#service_finder_options_package1-subcapabilities_invoice_0').prop("checked", false);
			jQuery('input[name="service_finder_options[package1-subcapabilities][invoice]"]').val(0);

			jQuery('#service_finder_options_package1-subcapabilities_invoice_0').attr("disabled", true);

			

			jQuery('#service_finder_options_package1-subcapabilities_availability_1').prop("checked", false);
			jQuery('input[name="service_finder_options[package1-subcapabilities][availability]"]').val(0);

			jQuery('#service_finder_options_package1-subcapabilities_availability_1').attr("disabled", true);

			

			jQuery('#service_finder_options_package1-subcapabilities_staff-members_2').prop("checked", false);
			jQuery('input[name="service_finder_options[package1-subcapabilities][staff-members]"]').val(0);

			jQuery('#service_finder_options_package1-subcapabilities_staff-members_2').attr("disabled", true);

		}

	});

	

	/*Package 2*/

	jQuery('body').on('click', '#service_finder_options_package2-capabilities_booking_0', function(){

		if(jQuery(this).is(':checked')) { 

			jQuery('#service_finder_options_package2-subcapabilities_invoice_0').removeAttr("disabled");

			

			jQuery('#service_finder_options_package2-subcapabilities_availability_1').removeAttr("disabled");

			

			jQuery('#service_finder_options_package2-subcapabilities_staff-members_2').removeAttr("disabled");

		}else{

			jQuery('#service_finder_options_package2-subcapabilities_invoice_0').prop("checked", false);
			jQuery('input[name="service_finder_options[package2-subcapabilities][invoice]"]').val(0);

			jQuery('#service_finder_options_package2-subcapabilities_invoice_0').attr("disabled", true);

			

			jQuery('#service_finder_options_package2-subcapabilities_availability_1').prop("checked", false);
			jQuery('input[name="service_finder_options[package2-subcapabilities][availability]"]').val(0);

			jQuery('#service_finder_options_package2-subcapabilities_availability_1').attr("disabled", true);

			

			jQuery('#service_finder_options_package2-subcapabilities_staff-members_2').prop("checked", false);
			jQuery('input[name="service_finder_options[package2-subcapabilities][staff-members]"]').val(0);

			jQuery('#service_finder_options_package2-subcapabilities_staff-members_2').attr("disabled", true);

		}

	});

	

	/*Package 3*/

	jQuery('body').on('click', '#service_finder_options_package3-capabilities_booking_0', function(){

		if(jQuery(this).is(':checked')) { 

			jQuery('#service_finder_options_package3-subcapabilities_invoice_0').removeAttr("disabled");

			

			jQuery('#service_finder_options_package3-subcapabilities_availability_1').removeAttr("disabled");

			

			jQuery('#service_finder_options_package3-subcapabilities_staff-members_2').removeAttr("disabled");

		}else{

			jQuery('#service_finder_options_package3-subcapabilities_invoice_0').prop("checked", false);
			jQuery('input[name="service_finder_options[package3-subcapabilities][invoice]"]').val(0);

			jQuery('#service_finder_options_package3-subcapabilities_invoice_0').attr("disabled", true);

			

			jQuery('#service_finder_options_package3-subcapabilities_availability_1').prop("checked", false);
			jQuery('input[name="service_finder_options[package3-subcapabilities][availability]"]').val(0);

			jQuery('#service_finder_options_package3-subcapabilities_availability_1').attr("disabled", true);

			

			jQuery('#service_finder_options_package3-subcapabilities_staff-members_2').prop("checked", false);
			jQuery('input[name="service_finder_options[package3-subcapabilities][staff-members]"]').val(0);

			jQuery('#service_finder_options_package3-subcapabilities_staff-members_2').attr("disabled", true);

		}

	});

	

	/*Load City on country onchange for theme options*/

	jQuery('body').on('change', '#default-country-select', function(){

        // Get the record's ID via attribute

        var country = jQuery(this).val();

		

		var data = {

			  "action": "load_cities",

			  "country": country

			};

			

	  var formdata = jQuery.param(data);

	  

	  jQuery.ajax({



						type: 'POST',



						url: ajaxurl,



						data: formdata,

						

						dataType: "json",

						

						beforeSend: function() {

							jQuery('.loading-area').show();

						},



						success:function (data, textStatus) {

							jQuery('.loading-area').hide();

							if(data['status'] == 'success'){

								

								jQuery("#service_finder_options-default-city").html('<select id="default-city-select form-control sf-form-control sf-select-box" class="redux-select-item " rows="6" style="width: 40%;" name="service_finder_options[default-city]" data-placeholder="Select an item" tabindex="-1" title="">'+data['cityhtml']+'</select>');

							}

						

						}



					});

		

	

	});

	

});// Document.ready END====================================================//



	var equalheight = function(container) {

		var currentTallest = 0,

			currentRowStart = 0,

			rowDivs = new Array(),

			$el,

			topPosition = 0;

		jQuery(container).each(function() {

	

			$el = jQuery(this);

			jQuery($el).height('auto')

			topPostion = $el.position().top;

	

			if (currentRowStart != topPostion) {

				for (currentDiv = 0; currentDiv < rowDivs.length; currentDiv++) {

					rowDivs[currentDiv].height(currentTallest);

				}

				rowDivs.length = 0; // empty the array

				currentRowStart = topPostion;

				currentTallest = $el.height();

				rowDivs.push($el);

			} else {

				rowDivs.push($el);

				currentTallest = (currentTallest < $el.height()) ? ($el.height()) : (currentTallest);

			}

			for (currentDiv = 0; currentDiv < rowDivs.length; currentDiv++) {

				rowDivs[currentDiv].height(currentTallest);

			}

		});

	}

	

	jQuery(window).resize(function() {

		equalheight('.equal-col-outer .equal-col');

	});

