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
	
	var $quotedata;
	var $bookingprice;
	var $bookingdate;
	var $bookingslot;
	var $stripepublickey = jsdata.stripepublickey;
	var walletamount;
	var walletamountwithcurrency;
	var walletsystem;
	var adminfee;
	var adminfeetype;
	var adminfeefixed;
	var adminfeepercentage;
	
	function initAddressAutoComplete(){
		var address = document.getElementById("booking-address");

		var my_address = new google.maps.places.Autocomplete(address);
		google.maps.event.addListener(my_address, "place_changed", function() {
		var place = my_address.getPlace();
		
		// if no location is found
		if (!place.geometry) {
			return;
		}
		
		var $city =jQuery("#bookingcity");
		var $state = jQuery("#bookingstate");
		var $country = jQuery("#bookingcountry");
		
		var country_long_name = "";
		var country_short_name = "";
		
		for(var i=0; i<place.address_components.length; i++){
			var address_component = place.address_components[i];
			var ty = address_component.types;

			for (var k = 0; k < ty.length; k++) {
			   if (ty[k] === "locality" || ty[k] === "sublocality" || ty[k] === "sublocality_level_1"  || ty[k] === "postal_town") {
					$city.val(address_component.long_name);
					//jQuery(".book-now").bootstrapValidator("revalidateField", "city");
					var cityname = address_component.long_name;
				} else if (ty[k] === "administrative_area_level_1" || ty[k] === "administrative_area_level_2") {
					$state.val(address_component.long_name);
					var statename = address_component.long_name;
				} else if(ty[k] === "country"){
					country_long_name = address_component.long_name;
					country_short_name = address_component.short_name;
					$country.val(address_component.long_name);
					//jQuery(".book-now").bootstrapValidator("revalidateField", "country");
				}
			}
		}
		
		var address = jQuery("#booking-address").val();
		var new_address = address.replace(cityname,"");
		new_address = new_address.replace(statename,"");
		
		new_address = new_address.replace(country_long_name,"");
		new_address = new_address.replace(country_short_name,"");
		new_address = jQuery.trim(new_address);
		
		
		new_address = new_address.replace(/,/g, "");
		new_address = new_address.replace(/ +/g," ");
		jQuery("#booking-address").val(address);
	
	 });	
	}
	
	if (jQuery('#booking-address').length && siteautosuggestion == true){
	google.maps.event.addDomListener(window, 'load', initAddressAutoComplete);
	}
	
	/*View quote description*/
	jQuery( 'body' ).on( 'click', '.viewquoteinfo', function (event)
	{
		var providerid = jQuery(this).data('providerid');
		var quoteid = jQuery(this).data('quoteid');
	
		var data = {
		   action: 'view_quote_description',
		   providerid: providerid, 
		   quoteid: quoteid 
		};
		
		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: data,
			beforeSend: function() {
				jQuery('.loading-area').show();
			},
			success:function (data, textStatus) {
				jQuery('.loading-area').hide();
				bootbox.alert(data);
			}
		});
	});
	
	/*View quote reply*/
	jQuery( 'body' ).on( 'click', '.viewquotereply', function (event)
	{
		var quoterepply = jQuery(this).data('quoterepply');
		bootbox.alert(quoterepply);
	});
	
	jQuery('.sf-serach-result-close ').click(function(){
		jQuery("body").removeClass('sf-booking-popup-open');
		jQuery(".booking-panel-wrap").animate({"top":"100%"}, "slow");
		jQuery(".booking-panel-overlay").fadeOut(100);
    });
	
	/*Click on Book now button*/
	jQuery( 'body' ).on( 'click', '.bookthisprovider', function ()
	{
		jQuery("body").addClass('sf-booking-popup-open');
		reset_jobbooking_form();
		$quotedata = jQuery( this ).data('params');
		
		jQuery("#quoteid").val($quotedata.quoteid);
		jQuery("#provider").val($quotedata.providerid);
		
		if($quotedata.is_booking_free_paid == 'free')
		{
			jQuery("#quotebooking-paid-panel").hide();	
		}
		
		if($quotedata.pay_booking_amount_to == 'provider')
		{
			jQuery("#sf-payment-options").html($quotedata.paymentoptions);
			
			$stripepublickey = $quotedata.stripepublickey;
			
			if($quotedata.paymentoptionsavl == 0)
			{
				jQuery("#sf-bookform-submitarea").hide();	
			}else{
				jQuery("#sf-bookform-submitarea").show();
			}
		}
		
		walletamount = $quotedata.walletamount;
		walletamountwithcurrency = $quotedata.walletamountwithcurrency;
		walletsystem = $quotedata.walletsystem;
		adminfeetype = $quotedata.adminfeetype;
		adminfeepercentage = $quotedata.adminfeepercentage;
		adminfeefixed = $quotedata.adminfeefixed;
		
		open_booking_panel();
		
		update_first_box();
		
		load_jobbooking_datepicker();
		
	});
	
	/*Click on edit price*/
	jQuery( 'body' ).on( 'click', '.editprice', function ()
	{
		jQuery( '#editpricebox' ).toggle();
	});
	
	/*Update Price*/
	jQuery( 'body' ).on( 'click', '.updateprice', function (event)
	{
		event.preventDefault();
		$bookingprice = jQuery( 'input[name="customprice"]' ).val();
		jQuery('#job-booking-price').html(currencysymbol + $bookingprice);
		jQuery( '#editpricebox' ).toggle();
		
		update_booking_price();
	});
	
	function calculate_commisionfee(totalcost){
		if(adminfeetype == 'fixed'){
				adminfee = parseFloat(adminfeefixed);	
				adminfee = adminfee.toFixed(2);
			}else if(adminfeetype == 'percentage'){
				adminfee = parseFloat(totalcost) * (parseFloat(adminfeepercentage)/100);
				adminfee = adminfee.toFixed(2);
			}
			
			jQuery("#bookingfee").html(currencysymbol+totalcost);
			jQuery("#bookingadminfee").html(currencysymbol+adminfee);
			jQuery("#totalbookingfee").html(currencysymbol+(parseFloat(totalcost) + parseFloat(adminfee)));	
			
			if(adminfee > 0)
			{
			jQuery("#adminfee-outer").show();
			jQuery("#onlyadminfee").html(currencysymbol+adminfee);
			}else
			{
			jQuery("#adminfee-outer").hide();
			jQuery("#onlyadminfee").html(currencysymbol+adminfee);	
			}
	}
	
	/*Continue to date/time box*/
	jQuery( 'body' ).on( 'click', '.continuetodatetime', function (event)
	{
		remove_alerts();
		
		if($bookingprice > 0){
			jQuery('#pricebox').collapse('toggle');
			jQuery('#datetimebox').collapse('toggle');
			
			jQuery('#datetimeheader').attr("data-toggle", "collapse");
		}else
		{
			jQuery( "<div class='alert alert-danger sf-custom-error'>"+param.price_req+"</div>" ).insertBefore( "form.book-now" );	
			return false;
		}
	});
	
	/*Continue to customer info box*/
	jQuery( 'body' ).on( 'click', '.continuetocustomerinfo', function (event)
	{
		remove_alerts();
		if($bookingdate == '' || $bookingdate === undefined || $bookingdate === 'undefined' || $bookingdate === null && $bookingdate === 'null')
		{
			jQuery( "<div class='alert alert-danger sf-custom-error'>"+param.select_date+"</div>" ).insertBefore( "form.book-now" );	
			return false;
		}else if($bookingslot == '' || $bookingslot === undefined || $bookingslot === 'undefined' || $bookingslot === null || $bookingslot === 'null')
		{
			jQuery( "<div class='alert alert-danger sf-custom-error'>"+param.select_timeslot_req+"</div>" ).insertBefore( "form.book-now" );	
			return false;
		}else
		{
			jQuery('#datetimebox').collapse('toggle');
			jQuery('#customerinfobox').collapse('toggle');
			
			jQuery('#customerinfoheader').attr("data-toggle", "collapse");
		}
	});
	
	/*Continue to payment box*/
	jQuery( 'body' ).on( 'click', '.continuetopayment', function (event)
	{
		remove_alerts();
		
		reset_customerinfo_validation();
		
		var $validator = jQuery('.book-now').data('bootstrapValidator').validate();
		
		if($validator.isValid()){
			
			if($quotedata.is_booking_free_paid == 'free')
			{
				booking_free_checkout();
				return false;
			}
			
			if(woopayment){
							
			if(walletsystem == true || $quotedata.skipoption == true){

				var $html = '';
				
				if(walletsystem == true || $quotedata.skipoption == true){
				$html += '<div id="bookingwalletpayment" class="bookingwallet-bx">';
				if(walletsystem == true){
				$html += '<ul class="list-unstyled clear">'+
							'<li>'+
									'<h5>'+param.wallet_balance+'</h5>'+
									'<strong>'+walletamountwithcurrency+'</strong>'+
								'</li>'+
							'</ul>';
				$html += '<div class="radio sf-radio-checkbox sf-payments-outer">'+
						  '<input type="radio" value="wallet" name="booking_woopayment" id="booking_wallet" >'+
						  '<label for="booking_wallet">'+param.wallet+'</label>'+
						  '<img src="'+imgpath+'/payment/wallet.jpg" title="'+param.wallet+'" alt="'+param.wallet+'">'+
						'</div> ';
				}
				$html += '<div class="radio sf-radio-checkbox sf-payments-outer">'+
						  '<input type="radio" value="woopayment" name="booking_woopayment" id="booking_woopayment" >'+
						  '<label for="booking_woopayment">'+param.checkout+'</label>'+
						  '<img src="'+imgpath+'/payment/woopayment.jpg" title="'+param.checkout+'" alt="'+param.checkout+'">'+
						'</div> ';
				if($quotedata.skipoption == true){		
				$html += '<div class="radio sf-radio-checkbox sf-payments-outer">'+
						  '<input type="radio" value="skippayment" name="booking_woopayment" id="booking_skippayment" >'+
						  '<label for="booking_skippayment">'+param.skip_payment+'</label>'+
						'</div> ';		
				}
				$html += '</div>';
				}
				
				bootbox.dialog({
					title: "",
					message: $html,
					buttons: {
						success: {
							label: "Continue",
							className: "btn-primary",
							callback: function () {
								var woooption = jQuery('#bookingwalletpayment').find('input[name=booking_woopayment]:checked').val();
								
								if(woooption == undefined){
									woooption = '';
								}
								
								if(woooption == '' && (walletsystem == true || $quotedata.skipoption == true)){
									jQuery('.alert').remove();	
									jQuery( "<div class='alert alert-danger sf-custom-error'>"+param.payment_method_req+"</div>" ).insertAfter( "#bookingwalletpayment" );
									return false;
								}
								if(woooption == "wallet"){
									booking_wallet_checkout();	
								}else if(woooption == "skippayment"){
									booking_free_checkout();	
								}else{
									addto_woo_payment();	
								}
							}
						}
					}
				})
				.on('shown.bs.modal',function () {
					jQuery('body').on('click', '.verifywoobookingcoupon', function(){
					jQuery('.alert').remove();
					var couponcode = jQuery('#woocouponcode').val();
					
					if(couponcode == ""){
						jQuery( "<div class='alert alert-danger sf-custom-error'>"+param.req+"</div>" ).insertAfter( "#addwoobookingcoupon" );	
						return false;
					}else{
						var data = {
								  "action": "verify_booking_couponcode",
								  "userid": provider_id,
								  "couponcode": couponcode,
								  "totalcost": totalcost,
								};
								
						var formdata = jQuery.param(data);
						
						jQuery.ajax({
			
								type: 'POST',
			
								url: ajaxurl,
								
								beforeSend: function() {
									jQuery('.loading-area').show();
									jQuery('.alert').remove();
								},
								
								data: formdata,
								
								dataType: "json",
			
								success:function (data, textStatus) {
									
									jQuery('.loading-area').hide();
									if(data['status'] == 'success'){
										jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertAfter( "#addwoobookingcoupon" );	
										var updatedtotalcost = data['updatedtotalcost'];
										totaldiscount = data['discount'];
										jQuery('#totaldiscount').val(totaldiscount);
										jQuery('#couponcode').val(couponcode);
										calculate_commisionfee(updatedtotalcost,'discount');
										
									}else{
										jQuery( "<div class='alert alert-danger sf-custom-error'>"+data['err_message']+"</div>" ).insertAfter( "#addwoobookingcoupon" );	
									}
			
									return false;
								}
			
							});		
					}
					return false;
				});							   
				});	
			}else{
				addto_woo_payment();	
			}
			return false;
			
			}else{
			jQuery('#customerinfobox').collapse('toggle');
			jQuery('#paymentbox').collapse('toggle');
			
			jQuery('#paymentheader').attr("data-toggle", "collapse");
			}
		}
	});
	
	/*Back to price box*/
	jQuery( 'body' ).on( 'click', '.backtopricebox', function (event)
	{
		remove_alerts();
		
		jQuery('#pricebox').collapse('toggle');
		jQuery('#datetimebox').collapse('toggle');
	});
	
	/*Back to date/time box*/
	jQuery( 'body' ).on( 'click', '.backtodatetimebox', function (event)
	{
		remove_alerts();
		
		jQuery('#customerinfobox').collapse('toggle');
		jQuery('#datetimebox').collapse('toggle');
	});
	
	/*Reset job booking form*/
	function reset_jobbooking_form()
	{
		var $bookingprice = '';
		var $bookingdate = '';
		var $bookingslot = '';
		
		jQuery('#datetimeheader').attr("data-toggle", '');
		jQuery('#customerinfoheader').attr("data-toggle", '');
		jQuery('#paymentheader').attr("data-toggle", '');
		
		jQuery('#pricebox').collapse('show');
		jQuery('#datetimebox').collapse('hide');
		jQuery('#customerinfobox').collapse('hide');
		jQuery('#paymentbox').collapse('hide');
		
		jQuery('.timeslots').html('');
		jQuery("#boking-slot").data('slot','');
		jQuery("#boking-slot").val('');
		jQuery("#totalcost").val('');
		jQuery("#selecteddate").val('');
		//jQuery('form.book-now input[type="text"]').val('');
		remove_alerts();
	}
	
	/*Reset form validation*/
	function reset_customerinfo_validation()
	{
		jQuery('.book-now')
		.bootstrapValidator('addField', 'firstname', {
			validators: {
				notEmpty: {
					message: param.signup_first_name
				}
			}
		})
		.bootstrapValidator('addField', 'lastname', {
			validators: {
				notEmpty: {
					message: param.signup_last_name
				}
			}
		})
		.bootstrapValidator('addField', 'email', {
			validators: {
				notEmpty: {
						message: param.req
					},
				emailAddress: {
					message: param.signup_user_email
				}
			}
		})
		.bootstrapValidator('addField', 'phone', {
			validators: {
				notEmpty: {
						message: param.req
					},
				digits: {message: param.only_digits},
			}
		})
		.bootstrapValidator('addField', 'address', {
			validators: {
				notEmpty: {
					message: param.signup_address
				}
			}
		})
		.bootstrapValidator('addField', 'city', {
			validators: {
				notEmpty: {
					message: param.city
				}
			}
		})
		.bootstrapValidator('addField', 'country', {
			validators: {
				notEmpty: {
					message: param.signup_country
				}
			}
		})
		.bootstrapValidator('addField', 'bookingpayment_mode', {
			validators: {
				notEmpty: {
					message: param.select_payment
				}
			}
		})
		.bootstrapValidator('addField', 'card_number', {
			validators: {
				notEmpty: {
					message: param.req
				},
				digits: {message: param.only_digits},
			}
		})
		.bootstrapValidator('addField', 'card_cvc', {
			validators: {
				notEmpty: {
					message: param.req
				},
				digits: {message: param.only_digits},
			}
		})
		.bootstrapValidator('addField', 'card_month', {
			validators: {
				notEmpty: {
					message: param.req
				},
				digits: {message: param.only_digits},
			}
		})
		.bootstrapValidator('addField', 'card_year', {
			validators: {
				notEmpty: {
					message: param.req
				},
				digits: {message: param.only_digits},
			}
		})
		.bootstrapValidator('addField', 'payulatam_cardtype', {
			validators: {
				notEmpty: {
					message: param.req
				},
				digits: {message: param.only_digits},
			}
		})
		.bootstrapValidator('addField', 'payulatam_card_number', {
			validators: {
				notEmpty: {
					message: param.req
				},
				digits: {message: param.only_digits},
			}
		})
		.bootstrapValidator('addField', 'payulatam_card_cvc', {
			validators: {
				notEmpty: {
					message: param.req
				},
				digits: {message: param.only_digits},
			}
		})
		.bootstrapValidator('addField', 'payulatam_card_month', {
			validators: {
				notEmpty: {
					message: param.req
				},
				digits: {message: param.only_digits},
			}
		})
		.bootstrapValidator('addField', 'payulatam_card_year', {
			validators: {
				notEmpty: {
					message: param.req
				},
				digits: {message: param.only_digits},
			}
		})
		.on('success.form.bv', function(form) {
		jQuery('form.book-now').find('input[type="submit"]').prop('disabled', false);
		
		var paymode = jQuery('input[name="bookingpayment_mode"]:checked').val();
		if(paymode == 'stripe'){
		form.preventDefault();
		var card_number = jQuery('input[name="card_number"]').val();
		var card_cvc = jQuery('input[name="card_cvc"]').val();
		var card_month = jQuery('select[name="card_month"]').val();
		var card_year = jQuery('select[name="card_year"]').val();
		
		if(card_number != "" && card_cvc != "" && card_month != "" && card_year != ""){	
			jQuery('.loading-area').show();
			if($stripepublickey != ""){
			Stripe.setPublishableKey($stripepublickey);
						 Stripe.card.createToken({
					  number: card_number,
					  cvc: card_cvc,
					  exp_month: card_month,
					  exp_year: card_year
					}, stripe_response_handler);	
			}else{
				jQuery('.loading-area').hide();
				jQuery('form.book-now').find('input[type="submit"]').prop('disabled', false);
				jQuery( "<div class='alert alert-danger sf-custom-error'>"+param.pub_key+"</div>" ).insertBefore( "form.book-now" );
				jQuery("html, body").animate({
						scrollTop: jQuery(".alert-danger").offset().top
					}, 1000);
			}
			
				 
			}
		}else if(paymode == 'wired' || paymode == 'cod' || paymode == 'skippayment'){
			form.preventDefault();
			booking_free_checkout();
		}else if(paymode == 'payulatam'){
			form.preventDefault();
			var crd_type = jQuery('#payulatam_cardtype').val();
			var crd_number = jQuery('#payulatam_card_number').val();
			var crd_cvc = jQuery('#payulatam_card_cvc').val();
			var crd_month = jQuery('#payulatam_card_month').val();
			var crd_year = jQuery('#payulatam_card_year').val();
			
			if(crd_type != "" && crd_number != "" && crd_cvc != "" && crd_month != "" && crd_year != ""){	
			jQuery('.loading-area').show();
			
			var data = {
				  "action": "payulatam_checkout",
				};
			var formdata = jQuery('form.book-now').serialize() + "&" + jQuery.param(data);
			
			jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: formdata,
					dataType: "json",
					success:function (data, textStatus) {
						jQuery('.loading-area').hide();
						jQuery('.alert').remove();
						jQuery('form.book-now').find('input[type="submit"]').prop('disabled', false);
						if(data['status'] == 'success'){
							jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.book-now" );	
							if(data['redirecturl'] != ''){
							window.location = data['redirecturl'];	
							}else
							{
								window.location.reload();	
							}
						}else if(data['status'] == 'error'){
							jQuery( "<div class='alert alert-danger sf-custom-error'>"+data['err_message']+"</div>" ).insertBefore( "form.book-now" );
							jQuery("html, body").animate({
							scrollTop: jQuery(".alert-danger").offset().top
						}, 1000);
						}
					}
			});		
				
				 
			}
		}else if(paymode == 'wallet'){
			form.preventDefault();
			booking_wallet_checkout();
		}else
		{
			return true;	
		}
		});
	}
	
	/*Open Booking panel*/
	function update_booking_price()
	{
		jQuery("#totalcost").val($bookingprice);
		calculate_commisionfee($bookingprice);
	}
	
	/*Open Booking panel*/
	function open_booking_panel()
	{
		jQuery(".booking-panel-wrap").animate({"top":"0"}, "slow");
		jQuery(".booking-panel-overlay").fadeIn(100);
	}
	
	/*Open Booking panel*/
	function update_first_box()
	{
		$bookingprice = $quotedata.quoteprice;
		
		jQuery('#job-booking-price').html(currencysymbol + $bookingprice);
		
		update_booking_price();
	}
	
	/*Load booking calendar*/
	function load_jobbooking_datepicker()
	{
		var date = new Date();
        date.setDate(date.getDate());
		jQuery('.jobbookingdate').datepicker({
			multidate: false,
			toggleActive: true,
			format: dateformat,
			startDate: date,
			language: langcode
		})
		.on('changeDate', function(e) {
			
			var current_date = jQuery(this).data('datepicker').getFormattedDate('yyyy-mm-dd');
			
			$bookingdate = current_date;
			
			jQuery("#selecteddate").val($bookingdate);
			
			$bookingslot = '';
			jQuery("#boking-slot").data('slot','');
			jQuery("#boking-slot").val('');
			remove_alerts();
			
			if( current_date != '' && current_date !== undefined && current_date !== 'undefined' && current_date !== null && current_date !== 'null' )
			{
			
			load_time_slots(current_date);
			
			}
		});
		
		jQuery('.jobbookingdate').datepicker('setDate', null);
	}
	
	/*Load booking timeslots*/
	function load_time_slots(current_date = '')
	{
		var data = {
			action       : 'get_bookingtimeslot',
			seldate		 : current_date,
			provider_id	 : $quotedata.providerid,
			totalhours	 : 0,
		};
		var formdata = jQuery.param(data);
		jQuery.ajax({
       		type : "post",
         	url : ajaxurl,
			beforeSend : function() {
				jQuery( '.loading-area' ).show();
			},
         	data : formdata,
         	success: function(data, textStatus) {
				jQuery( '.loading-area' ).hide();
				jQuery('.timeslots').html(data);
			},
			error: function(data) {
				jQuery( '.loading-area' ).hide();
			}
       })   
	}
	
	/*Update Price*/
	jQuery( 'body' ).on( 'click', 'ul.timelist li', function ()
	{
		jQuery(this).addClass('active').siblings().removeClass('active');
		var slot = jQuery(this).data('source');
		$bookingslot = slot;
		var t = jQuery(this).find("span").html();
		jQuery("#boking-slot").data('slot',t);
		jQuery("#boking-slot").val(slot);
		remove_alerts();
	});
	
	/*Payment method onchange event*/
	jQuery( 'body' ).on( 'change', 'input[name="bookingpayment_mode"]', function ()
	{
		var paymode = jQuery(this).val();
		if(paymode == 'stripe'){
			jQuery('#bookingcardinfo').show();
			jQuery('#wiredinfo').hide();
			jQuery('#bookingpayulatamcardinfo').hide();
		
		}else if(paymode == 'payulatam'){
			jQuery('#bookingtwocheckoutcardinfo').hide();
			jQuery('#bookingcardinfo').hide();
			jQuery('#wiredinfo').hide();
			jQuery('#bookingpayulatamcardinfo').show();
		
		}else if(paymode == 'wired'){
			jQuery('#wiredinfo').show();
			jQuery('#bookingtwocheckoutcardinfo').hide();
			jQuery('#bookingcardinfo').hide();
			jQuery('#bookingpayulatamcardinfo').hide();
		}else{
			jQuery('#bookingcardinfo').hide();
			jQuery('#bookingtwocheckoutcardinfo').hide();
			jQuery('#wiredinfo').hide();
			jQuery('#bookingpayulatamcardinfo').hide();
		}
	});
	
	/*Submit booking form*/
	jQuery( 'body' ).on( 'submit', '#bookingjobform', function (event)
	{
		
	});
	
	function booking_free_checkout() 
	{
		var data = {
		  "action": "freecheckout",
		  "provider": $quotedata.providerid,
		  "totalcost": $bookingprice,
		  "bookingdate": $bookingdate
		};
		
		var formdata = jQuery('form.book-now').serialize() + "&" + jQuery.param(data);
		
		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			dataType: "json",
			beforeSend: function() {
			jQuery('.loading-area').show();
			},
			data: formdata,
			success:function (data, textStatus) {
				jQuery('.loading-area').hide();
				jQuery('.alert').remove();
				jQuery('form.book-now').find('input[type="submit"]').prop('disabled', false);
				if(data['status'] == 'success'){
					jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "form.book-now" );	
					jQuery("html, body").animate({
						scrollTop: jQuery(".alert-success").offset().top
					}, 1000);
					if(data['redirecturl'] != ''){
					window.location = data['redirecturl'];	
					}else
					{
						window.location.reload();	
					}
				}else if(data['status'] == 'error'){
					jQuery( "<div class='alert alert-danger sf-custom-error'>"+data['err_message']+"</div>" ).insertBefore( "form.book-now" );
					jQuery("html, body").animate({
						scrollTop: jQuery(".alert-danger").offset().top
					}, 1000);
				}
			}
		});
	}
	
	function booking_wallet_checkout() 
	{
		if(parseFloat(walletamount) < (parseFloat($bookingprice) + parseFloat(adminfee))){
			jQuery( "<div class='alert alert-danger sf-custom-error'>"+param.insufficient_wallet_amount+"</div>" ).insertBefore( "#bookingjobform" );
				jQuery("html, body").animate({
				scrollTop: jQuery(".alert-danger").offset().top
			}, 1000);	
			return false;	
		}
		var data = {
		  "action": "walletcheckout",
		  "provider": $quotedata.providerid,
		  "totalcost": $bookingprice,
		  "bookingdate": $bookingdate,
		};
		
		var formdata = jQuery('form.book-now').serialize() + "&" + jQuery.param(data);
		
		jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				dataType: "json",
				beforeSend: function() {
				jQuery('.loading-area').show();
				},
				data: formdata,
				success:function (data, textStatus) {
					jQuery('.loading-area').hide();
					jQuery('.alert').remove();
					jQuery('form.book-now').find('input[type="submit"]').prop('disabled', false);
					if(data['status'] == 'success'){
						jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "#bookingjobform" );	
						if(data['redirecturl'] != ''){
						window.location = data['redirecturl'];	
						}else
						{
							window.location.reload();	
						}
					}else if(data['status'] == 'error'){
						jQuery( "<div class='alert alert-danger sf-custom-error'>"+data['err_message']+"</div>" ).insertBefore( "#bookingjobform" );
						jQuery("html, body").animate({
						scrollTop: jQuery(".alert-danger").offset().top
					}, 1000);
					}
				}
			});
	}
	
	function addto_woo_payment(){

		var data = {
		  "action": "sf_add_to_woo_cart",
		  "wootype": "booking",
		  "provider": $quotedata.providerid,
		  "totalcost": $bookingprice,
		  "bookingdate": $bookingdate,
		};
		
		var formdata = jQuery('form.book-now').serialize() + "&" + jQuery.param(data);
		
		jQuery.ajax({
			type        : 'POST',
			url         : ajaxurl,
			data        : formdata,
			dataType    : 'json',
			xhrFields   : { withCredentials: true },
			crossDomain : 'withCredentials' in new XMLHttpRequest(),
			success     : function (response) {
				if (response['success']) {
					window.location.href = cart_url;
				} else {
					jQuery(".alert-success,.alert-danger").remove();
					jQuery( "<div class='alert alert-danger sf-custom-error'>"+response.error+"</div>" ).insertBefore( "#bookingjobform" );
					jQuery("html, body").animate({
							scrollTop: jQuery(".alert-danger").offset().top
						}, 1000);
				}
			}
		});  
		return false;	
	}
	
	function remove_alerts() 
	{
		jQuery('.alert').remove();	
	}
	
	/*Stripe Handler*/
	function stripe_response_handler(status, response) {
  
		  if (response.error) {
			  // Show the errors on the form
			  jQuery('.loading-area').hide();
			  jQuery( "<div class='alert alert-danger sf-custom-error'>"+response.error.message+"</div>" ).insertBefore( "form.book-now" );
			  jQuery("html, body").animate({
									scrollTop: jQuery(".alert-danger").offset().top
								}, 1000);
			
		  } else {
			// response contains id and card, which contains additional card details
			var token = response.id;
			
			var data = {
					  "action": "checkout",
					  "provider": $quotedata.providerid,
					  "stripeToken": token,
					  "totalcost": $bookingprice,
					  "bookingdate": $bookingdate
					};
					
			var formdata = jQuery('form.book-now').serialize() + "&" + jQuery.param(data);
			
			jQuery.ajax({

					type: 'POST',

					url: ajaxurl,
					
					dataType: "json",
					
					beforeSend: function() {
					},
					
					data: formdata,

					success:function (data, textStatus) {
						jQuery('.loading-area').hide();
						jQuery('.alert').remove();
						jQuery('form.book-now').find('input[type="submit"]').prop('disabled', false);
						if(data['status'] == 'success'){
							if(data['redirecturl'] != ''){
								window.location = data['redirecturl'];	
							}else
							{
								window.location.reload();	
							}
							jQuery( "<div class='alert alert-success'>"+param.booking_suc+"</div>" ).insertBefore( "form.book-now" );
						}else if(data['status'] == 'error'){
							jQuery( "<div class='alert alert-danger sf-custom-error'>"+data['err_message']+"</div>" ).insertBefore( "form.book-now" );
							jQuery("html, body").animate({
									scrollTop: jQuery(".alert-danger").offset().top
								}, 1000);
						}
						
					}

				});

			}
		}
	
	/*Add to Favorite*/
	jQuery('body').on('click', '.add-job-favorite', function(){
			var providerid = jQuery(this).attr('data-proid');
			var userid = jQuery(this).attr('data-userid');
			var data = {
					  "action": "addtofavorite",
					  "userid": userid,
					  "providerid": providerid
					};
					
			var formdata = jQuery.param(data);
			
			jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					
					beforeSend: function() {
						jQuery('.loading-area').show();
					},
					
					data: formdata,
					dataType: "json",
					success:function (data, textStatus) {
						jQuery('.loading-area').hide();
						if(data['status'] == 'success'){
							jQuery( '<a href="javascript:;" id="removefave-'+providerid+'" class="remove-job-favorite sf-serach-addToFav" data-proid="'+providerid+'" data-userid="'+userid+'"><i class="fa fa-heart"></i></a>' ).insertBefore( '#addfave-'+providerid );
							jQuery('#addfave-'+providerid).remove();
						}
						
					}
				});																
	});
	
	/*Remove from Favorite*/
	jQuery('body').on('click', '.remove-job-favorite', function(){
			var providerid = jQuery(this).attr('data-proid');
			var userid = jQuery(this).attr('data-userid');
			var data = {
					  "action": "removefromfavorite",
					  "userid": userid,
					  "providerid": providerid
					};
					
			var formdata = jQuery.param(data);
			
			jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					
					beforeSend: function() {
						jQuery('.loading-area').show();
					},
					
					data: formdata,
					
					dataType: "json",
					success:function (data, textStatus) {
						
						jQuery('.loading-area').hide();
						if(data['status'] == 'success'){
							
							jQuery( '<a href="javascript:;" id="addfave-'+providerid+'" class="add-job-favorite sf-serach-addToFav" data-proid="'+providerid+'" data-userid="'+userid+'"><i class="fa fa-heart-o"></i></a>' ).insertBefore( '#removefave-'+providerid );
							jQuery('#removefave-'+providerid).remove();
						}
						
					}
				});																
	});
});