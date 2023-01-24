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
	var $servicedata, $bookingdata, $bookingdate, $bookingslot, $stripepublickey = jsdata.stripepublickey, $radiussearchunit = jsdata.radiussearchunit, walletamount, walletamountwithcurrency, walletsystem, adminfee, adminfeetype, adminfeefixed, adminfeepercentage, defaultlocation, service_flag = 0, totalservicecost = 0, totalhours = 0, member_flag = 1, servicearr, oldzipcode = '';
	
	var size_li = jQuery("#bookingservices li").size();
    var x = 3;
	var numberofnext = 2;
    jQuery('#bookingservices li:lt('+x+')').show();
    jQuery( 'body' ).on( 'click', '#loadMore', function (){
        x= (x+numberofnext <= size_li) ? x+numberofnext : size_li;
        jQuery('#bookingservices li:lt('+x+')').show();
         jQuery('#showLess').show();
        if(x == size_li){
            jQuery('#loadMore').hide();
        }
    });
	jQuery( 'body' ).on( 'click', '#showLess', function (){
        x=(x-numberofnext<0) ? 3 : x-numberofnext;
        jQuery('#bookingservices li').not(':lt('+x+')').hide();
        jQuery('#loadMore').show();
         jQuery('#showLess').show();
        if(x == 3){
            jQuery('#showLess').hide();
        }
    });
	
	jQuery( 'body' ).on( 'click', '.sf-suumery-close', function ()
	{
		jQuery('#bookingsmry .sf-summery-inr').toggle();
	});	
	
	jQuery( 'body' ).on( 'click', '.addthisservice', function ()
	{
		$servicedata = jQuery( this ).data('servicedata');
		$bookingdata = jQuery( this ).data('bookingdata');
		
		var serviceid = $servicedata.serviceid;
		
		jQuery('#serviceid-'+serviceid).toggleClass('selected');
		
		calculate_servicecost();
		
	});
	
	/*Reset job booking form*/
	function reset_booking_form()
	{
		jQuery('#customerinfoheader').attr("data-toggle", '');
		jQuery('#paymentheader').attr("data-toggle", '');
		
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
	
	jQuery('.sf-serach-result-close ').click(function(){
		jQuery("body").removeClass('sf-booking-popup-open');
		jQuery(".booking-panel-wrap").animate({"top":"100%"}, "slow");
		jQuery(".booking-panel-overlay").fadeOut(100);
    });
	
	function calculate_servicecost(){
		var servicecost = 0;
		var servicehours = 0;
		service_flag = 0;
		servicearr = '';
		var smry = '';
		jQuery("#bookingservices .servicebox").each( function() {
            if(jQuery(this).hasClass('selected')) { 
				service_flag = 1;
				//alert(JSON.stringify(servicearr, null, 4));
				$servicedata = jQuery( this ).find('.addthisservice').data('servicedata');
				
				//alert(JSON.stringify($servicedata, null, 4));
				
				var costtype = $servicedata.costtype;
				var cost = $servicedata.cost;
				var serviceid = $servicedata.serviceid;
				
				var servicename = $servicedata.servicename;
				
				var discounttype = $servicedata.discounttype;
				var discountvalue = $servicedata.discountvalue;
				var coupon = $servicedata.coupon;
				var couponcode = $servicedata.couponcode;
				
				if(costtype == 'fixed'){
					var hours = 0;
					var discount = calculate_discount(coupon,discounttype,discountvalue,cost);	
					cost = calculate_discount_cost(discount,cost);
					servicearr = addto_service_array(servicearr,serviceid,hours,couponcode,discount);
					servicecost = parseFloat(servicecost) + parseFloat(cost);
					servicehours = parseFloat(servicehours) + parseFloat(hours);
					
					smry += '<li><div class="sf-sum-cel-one"><strong>'+servicename+'</strong><span>-</span><span>'+param.stype_fixed+'</span></div><div class="sf-sum-cel-four">'+currencysymbol+cost.toFixed(2)+'</div></li>';
				}else if(costtype == 'hourly'){
					var hrmin = 0;
					var hours = jQuery('#hours-'+serviceid).val();
					var $hourflag = $servicedata.hours;
					if($hourflag > 0){
					hrmin = $hourflag;	
					var temphr = $hourflag.toString().split('.');
					
					var hours = temphr[0];
					var minutes = (temphr[1] == 'undefined' || temphr[1] == undefined) ? 0 : temphr[1];
					
					if(parseInt(minutes) < 10){
						var minutescost = (parseFloat(minutes) * 10) * (parseFloat(cost)/60);	
					}else{
						var minutescost = (parseFloat(minutes)) * (parseFloat(cost)/60);	
					}
					
					var tcost = parseFloat(cost) * parseFloat(hours);	
					
					tcost = parseFloat(tcost) + parseFloat(minutescost);
					var discount = calculate_discount(coupon,discounttype,discountvalue,tcost);	
					cost = calculate_discount_cost(discount,tcost);
					servicecost = parseFloat(servicecost) + parseFloat(cost);
					servicearr = addto_service_array(servicearr,serviceid,$hourflag,couponcode,discount);
					
					smry += '<li><div class="sf-sum-cel-one"><strong>'+servicename+'</strong><span>'+$servicedata.hours+' '+param.shours+'</span><span>'+param.stype_hourly+'</span></div><div class="sf-sum-cel-four">'+currencysymbol+cost.toFixed(2)+'</div></li>';
					}else if(hours > 0){
					/*hrmin = hours;	
					var temphr = hours.toString().split('.');
					
					var hours = temphr[0];
					var minutes = (temphr[1] == 'undefined' || temphr[1] == undefined) ? 0 : temphr[1];
					
					
					if(parseInt(minutes) < 10){
						var minutescost = (parseFloat(minutes) * 10) * (parseFloat(cost)/60);	
					}else{
						var minutescost = (parseFloat(minutes)) * (parseFloat(cost)/60);	
					}
					
					var tcost = parseFloat(cost) * parseFloat(hours);	
					
					tcost = parseFloat(tcost) + parseFloat(minutescost);*/
					
					var tcost = parseFloat(cost) * parseFloat(hours);
					
					var discount = calculate_discount(coupon,discounttype,discountvalue,tcost);	
					cost = calculate_discount_cost(discount,tcost);
					servicecost = parseFloat(servicecost) + (parseFloat(cost));	
					servicearr = addto_service_array(servicearr,serviceid,hours,couponcode,discount);
					
					smry += '<li><div class="sf-sum-cel-one"><strong>'+servicename+'</strong><span>'+hours+' '+param.shours+'</span><span>'+param.stype_hourly+'</span></div><div class="sf-sum-cel-four">'+currencysymbol+cost.toFixed(2)+'</div></li>';
					
					}else{
					var discount = calculate_discount(coupon,discounttype,discountvalue,cost);	
					cost = calculate_discount_cost(discount,cost);
					servicecost = parseFloat(servicecost) + (parseFloat(cost));	
					servicearr = addto_service_array(servicearr,serviceid,hours,couponcode,discount);
					
					smry += '<li><div class="sf-sum-cel-one"><strong>'+servicename+'</strong><span>-</span><span>'+param.stype_hourly+'</span></div><div class="sf-sum-cel-four">'+currencysymbol+cost.toFixed(2)+'</div></li>';
					}
					servicehours = parseFloat(servicehours) + parseFloat(hrmin);
					
					
				}else if(costtype == 'perperson'){
					
					var hours = jQuery('#hours-'+serviceid).val();
					var $hourflag = $servicedata.hours;
					if($hourflag > 0){
					var tcost = parseFloat(cost) * parseFloat($hourflag);	
					var discount = calculate_discount(coupon,discounttype,discountvalue,tcost);	
					cost = calculate_discount_cost(discount,tcost);
					servicecost = parseFloat(servicecost) + parseFloat(cost);
					servicearr = addto_service_array(servicearr,serviceid,$hourflag,couponcode,discount);
					
					smry += '<li><div class="sf-sum-cel-one"><strong>'+servicename+'</strong><span>'+$servicedata.hours+' '+param.sitems+'</span><span>'+param.stype_item+'</span></div><div class="sf-sum-cel-four">'+currencysymbol+cost.toFixed(2)+'</div></li>';
					
					}else if(hours > 0){
					var tcost = parseFloat(cost) * parseFloat(hours);	
					var discount = calculate_discount(coupon,discounttype,discountvalue,tcost);	
					cost = calculate_discount_cost(discount,tcost);
					servicecost = parseFloat(servicecost) + (parseFloat(cost));	
					servicearr = addto_service_array(servicearr,serviceid,hours,couponcode,discount);
					
					smry += '<li><div class="sf-sum-cel-one"><strong>'+servicename+'</strong><span>'+hours+' '+param.sitems+'</span><span>'+param.stype_item+'</span></div><div class="sf-sum-cel-four">'+currencysymbol+cost.toFixed(2)+'</div></li>';
					
					}else{
					var discount = calculate_discount(coupon,discounttype,discountvalue,cost);	
					cost = calculate_discount_cost(discount,cost);
					servicecost = parseFloat(servicecost) + (parseFloat(cost));	
					servicearr = addto_service_array(servicearr,serviceid,hours,couponcode,discount);
					
					smry += '<li><div class="sf-sum-cel-one"><strong>'+servicename+'</strong><span>-</span><span>'+param.stype_item+'</span></div><div class="sf-sum-cel-four">'+currencysymbol+cost.toFixed(2)+'</div></li>';
					}
					servicehours = parseFloat(servicehours) + parseFloat(hours);
				}
			}
			
        });
		jQuery('#servicearr').val(servicearr);
		jQuery('#bookingsmry').show();
		jQuery('#summarywrap').html(smry);
		totalservicecost = servicecost;
		totalhours = servicehours;
		calculate_totalcost();
	}
	
	function calculate_totalcost(){
		totalcost = parseFloat($bookingdata.mincost) + parseFloat(totalservicecost);
		totalcost = totalcost.toFixed(2);
		jQuery('#bookingamount').html(currencysymbol+totalcost);
		calculate_commisionfee(totalcost);
	}
	
	function calculate_commisionfee(totalcost,dicountchk = ''){
		if($bookingdata.adminfeetype == 'fixed'){
				adminfee = parseFloat($bookingdata.adminfeefixed);	
				adminfee = adminfee.toFixed(2);
			}else if($bookingdata.adminfeetype == 'percentage'){
				adminfee = parseFloat(totalcost) * (parseFloat($bookingdata.adminfeepercentage)/100);
				adminfee = adminfee.toFixed(2);
			}
			if(dicountchk != 'discount'){
			jQuery("#totalcost").val(totalcost);
			}
			jQuery("#bookingfee").html(currencysymbol+totalcost);
			jQuery("#bookingadminfee").html(currencysymbol+adminfee);
			jQuery("#totalbookingfee").html(currencysymbol+(parseFloat(totalcost) + parseFloat(adminfee)));	
			
			
			var amountsmryhtml = jQuery(".sf-adminfee-bx").html();
			
			var amountsmry = '<li><div class="sf-sum-amount">'+amountsmryhtml+'</div></li>';
			jQuery('#summarywrap').append(amountsmry);
			
			if(adminfee == '' || adminfee == undefined || adminfee == 'undefined' || adminfee == null || adminfee == 'null'){
				adminfee = 0;	
			}

			if(charge_admin_fee_from == 'provider'){
				jQuery('#smrytotalamount').html(currencysymbol+(parseFloat(totalcost)));	
			}else{
				jQuery('#smrytotalamount').html(currencysymbol+(parseFloat(totalcost) + parseFloat(adminfee)));
			}
			
			
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
	
	function addto_service_array(servicearr,serviceid,hours,coupon,discount){
		servicearr = servicearr + serviceid +'-'+ hours+'-'+ discount +'-'+ coupon + '%%';
		return servicearr;
	}
	
	function calculate_discount(coupon,discounttype,discountvalue,cost){
		var discount = 0; 
		if(coupon == 'verified'){
					
			if(discounttype == 'percentage'){
				discount = parseFloat(cost) * (parseFloat(discountvalue)/100);
			}else if(discounttype == 'fixed'){
				discount = parseFloat(discountvalue);	
			}
			
		}
		return discount;
	}
	
	function calculate_discount_cost(discount,cost){
		
		if(parseFloat(cost) >= parseFloat(discount)){
		cost = parseFloat(cost) - parseFloat(discount);
		}
		
		return cost;
	}
	
	/*Back to date/time box*/
	jQuery( 'body' ).on( 'click', '.backtodatetimebox', function (event)
	{
		remove_alerts();
		
		jQuery('#customerinfobox').collapse('toggle');
		jQuery('#datetimebox').collapse('toggle');
	});
	
	jQuery( 'body' ).on( 'click', '.bookthisservices', function ()
	{
		reset_booking_form();
		
		$bookingdata = jQuery( this ).data('bookingdata');
		
		if(service_flag == 0 && $bookingdata.booking_charge_on_service == 'yes'){
			jQuery( "<div class='alert alert-danger sf-custom-error'>"+param.select_service+"</div>" ).insertBefore( "form.book-now" );	
			return false;
		}
		
		jQuery("body").addClass('sf-booking-popup-open');
		
		jQuery("#provider").val($bookingdata.providerid);
		
		if($bookingdata.is_booking_free_paid == 'free')
		{
			jQuery("#jobbooking-paid-panel").hide();	
		}
		
		if($bookingdata.pay_booking_amount_to == 'provider')
		{
			jQuery("#sf-payment-options").html($bookingdata.paymentoptions);
			
			$stripepublickey = $bookingdata.stripepublickey;
			
			if($bookingdata.paymentoptionsavl == 0)
			{
				jQuery("#sf-bookform-submitarea").hide();	
			}else{
				jQuery("#sf-bookform-submitarea").show();
			}
		}
		
		walletamount = $bookingdata.walletamount;
		walletamountwithcurrency = $bookingdata.walletamountwithcurrency;
		walletsystem = $bookingdata.walletsystem;
		adminfeetype = $bookingdata.adminfeetype;
		adminfeepercentage = $bookingdata.adminfeepercentage;
		adminfeefixed = $bookingdata.adminfeefixed;
		
		open_booking_panel();
		
		load_jobbooking_datepicker();
		
	});
	
	/*Open Booking panel*/
	function open_booking_panel()
	{
		jQuery(".booking-panel-wrap").animate({"top":"0"}, "slow");
		jQuery(".booking-panel-wrap").fadeIn(100);
		jQuery(".booking-panel-overlay").fadeIn(100);
	}
	
	/*Load booking calendar*/
	function load_jobbooking_datepicker()
	{
		var data = {
				  "action": "reset_bookingcalendar",
				  "provider_id": $bookingdata.providerid
				};
		
		var formdata = jQuery.param(data);
		
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
						if(data['status'] == 'success'){
						var daysOfWeekDisabled = jQuery.parseJSON(data['daysOfWeekDisabled']);
						var alldisableddates = jQuery.parseJSON(data['alldisableddates']);
						
						var date = new Date();
						date.setDate(date.getDate());
						jQuery('.jobbookingdate').datepicker({
							multidate: false,
							daysOfWeekDisabled: daysOfWeekDisabled,
							datesDisabled: alldisableddates,
							weekStart: 1,
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
					},
					error:function (data, textStatus) {
						jQuery('.loading-area').hide();
					}
	
				});
	}
	
	/*Load booking timeslots*/
	function load_time_slots(current_date = '')
	{
		var data = {
			action       : 'get_bookingtimeslot',
			seldate		 : current_date,
			provider_id	 : $bookingdata.providerid,
			totalhours	 : totalhours,
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
	
	function remove_alerts() 
	{
		jQuery('.alert').remove();	
	}
	
	converttotouchspin();
	
	function converttotouchspin(){
	if(jQuery('.servicehourspin').length){
		
		/*if($servicedata.costtype == 'perperson'){
			var str = param.perpersion_short;
			var step = 1;
			var maxlimit = 500;
		}else{
			var str = param.perhour_short;
			var step = 0.5;
			var maxlimit = 12;
		}*/
		jQuery('.servicehourspin').TouchSpin({
			min: 1,
			initval: 1,
			decimals: 1,
		}).on('change', function() {
			if(service_flag == 1){
				calculate_servicecost();	
			}
		});	
	}
	}
	
	/*Continue to date time box*/
	jQuery( 'body' ).on( 'click', '.continuetodatetime', function (event)
	{
		remove_alerts();
		
		var zipcode = jQuery('input[name="zipcode"]').val();
		var region = jQuery('select[name="region"]').val();
		
		if(booking_basedon == 'zipcode'){
			if(zipcode != ""){	
				var data = {
					  "action": "check_zipcode",
					  "zipcode": zipcode,
					  "provider_id": $bookingdata.providerid
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
						jQuery("#panel-1").find(".alert").remove();
						if(data['status'] != 'success'){
							jQuery( "<div class='alert alert-danger sf-custom-error'>"+param.service_not_avl+"</div>" ).insertBefore( "form.book-now" );	
							return false;
						}else{
							continuetodatetimearea();	
						}
					}
	
				});	
			}else{
				jQuery( "<div class='alert alert-danger sf-custom-error'>"+param.enterzip+"</div>" ).insertBefore( "form.book-now" );	
				return false;
			}
		}else if(booking_basedon == 'region'){
			
			if(region == ""){
				jQuery( "<div class='alert alert-danger sf-custom-error'>"+param.selectregion+"</div>" ).insertBefore( "form.book-now" );	
				return false;
			}else{
				continuetodatetimearea();	
			}
				
		}else{
			continuetodatetimearea();
		}
	});
	
	jQuery('body').on('click','.set-marker-popup-close',function(){
		jQuery('.set-marker-popup').hide();
	});															   
	jQuery('body').on('click','#viewmylocation',function(){
     jQuery('.set-marker-popup').show();
	 
	 var providerlat = jQuery(this).data('providerlat');
	 var providerlng = jQuery(this).data('providerlng');
  	 var zooml = jQuery(this).data('locationzoomlevel');
	 
	 if(zooml == ""){
		zooml = 14;	 
	 }
	 
	 if(providerlat != "" && providerlng != ""){
	 initMap(providerlat,providerlng,zooml);
	 }else{
	 initMap(parseFloat(defaultlat),parseFloat(defaultlng),parseInt(defaultzoomlevel));		
	 }
	 
	 });
	
	function initMap(lat,lng,zoom) {
	var map = new google.maps.Map(document.getElementById('marker-map'), {
	  zoom: zoom,
	  center: {lat: lat, lng: lng}
	});
	
	marker = new google.maps.Marker({
	  map: map,
	  draggable: true,
	  animation: google.maps.Animation.DROP,
	  position: {lat: lat, lng: lng}
	});

	}
	
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
		}else if(member_flag == 0)
		{
			jQuery( "<div class='alert alert-danger sf-custom-error'>"+param.member_select+"</div>" ).insertBefore( "form.book-now" );	
			return false;
		}else
		{
			jQuery('#datetimebox').collapse('toggle');
			jQuery('#customerinfobox').collapse('toggle');
			
			jQuery('#customerinfoheader').attr("data-toggle", "collapse");
			
			var datetimesmry = '<li><span class="sf-sum-date"><i class="fa fa-calendar"></i> '+$bookingdate+'</span> <span class="sf-sum-time"><i class="fa fa-clock-o"></i> '+$bookingslot+'</span></li>';
			
			jQuery('#summarywrap').append(datetimesmry);
		}
	});
	
	/*Continue to payment box*/
	jQuery( 'body' ).on( 'click', '.continuetopayment', function (event)
	{
		remove_alerts();
		
		reset_customerinfo_validation();
		
		var $validator = jQuery('.book-now').data('bootstrapValidator').validate();
		
		if($validator.isValid()){
			
			var zipcode = jQuery('input[name="zipcode"]').val();
			var region = jQuery('select[name="region"]').val();
			
			if(booking_basedon == 'zipcode'){
			if(zipcode != ""){	
				var data = {
					  "action": "check_zipcode",
					  "zipcode": zipcode,
					  "provider_id": $bookingdata.providerid
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
						jQuery("#panel-1").find(".alert").remove();
						if(data['status'] != 'success'){
							jQuery( "<div class='alert alert-danger sf-custom-error'>"+param.service_not_avl+"</div>" ).insertBefore( "form.book-now" );	
							return false;
						}else{
							continuetopaymentarea();	
						}
					}
	
				});	
			}else{
				jQuery( "<div class='alert alert-danger sf-custom-error'>"+param.enterzip+"</div>" ).insertBefore( "form.book-now" );	
				return false;
			}
		}else if(booking_basedon == 'region'){
			
				if(region == ""){
					jQuery( "<div class='alert alert-danger sf-custom-error'>"+param.selectregion+"</div>" ).insertBefore( "form.book-now" );	
					return false;
				}else{
					continuetopaymentarea();	
				}
					
			}else{
				continuetopaymentarea();
			}
		}
	});
	
	function continuetopaymentarea(){
		if($bookingdata.is_booking_free_paid == 'free')
		{
			booking_free_checkout();
			return false;
		}
		
		if(woopayment){
						
		if(walletsystem == true || $bookingdata.skipoption == true){

			var $html = '';
			
			if(walletsystem == true || $bookingdata.skipoption == true){
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
			if($bookingdata.skipoption == true){		
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
						label: param.continue_lable,
						className: "btn-primary",
						callback: function () {
							var woooption = jQuery('#bookingwalletpayment').find('input[name=booking_woopayment]:checked').val();
							
							if(woooption == undefined){
								woooption = '';
							}
							
							if(woooption == '' && (walletsystem == true || $jobsdata.bookingdata == true)){
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
		.bootstrapValidator('addField', 'zipcode', {
			validators: {
				notEmpty: {
					message: param.enterzip
				}
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
	
	/*Update Price*/
	jQuery( 'body' ).on( 'click', 'ul.timelist li', function ()
	{
		jQuery(this).addClass('active').siblings().removeClass('active');
		var slot = jQuery(this).data('source');
		$bookingslot = slot;
		var t = jQuery(this).find("span").html();
		jQuery("#boking-slot").data('slot',t);
		jQuery("#boking-slot").val(slot);
		
		if($bookingdata.loadmembers == 'yes'){
		load_members();
		}
		remove_alerts();
	});
	
	function load_members(){
		//var zipcode = jQuery('input[name="zipcode"]').val();
		//var region = jQuery('select[name="region"]').val();
		//region = Encoder.htmlEncode(region);
		var data = {
			  "action": "load_members",
			  "provider_id": $bookingdata.providerid,
			  "date": $bookingdate,
			  "slot": $bookingslot,
			  /*"zipcode": zipcode,
			  "region": region,*/
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
					 if(data != null){
						if(data['status'] == 'success'){
							jQuery("#members").html(data['members']);
							if(data['totalmember'] > 0){
							jQuery("#members").append('<div class="col-lg-12"><div class="row"><div class="checkbox text-left"><input id="anymember" class="anymember" type="checkbox" name="anymember[]" value="yes" checked><label for="anymember">'+param.anyone+'</label></div></div></div>');
							member_flag = 1;
							}else{
							member_flag = 0;								
							}
							jQuery('.display-ratings').rating();
							jQuery('.sf-show-rating').show();
						}
					}
			}

		});	
	}
	
	/*Staff member click event*/ 
	jQuery('body').on('click', '.staff-member .sf-element-bx', function(){																		
		var memberid = jQuery(this).data("id");																
		jQuery("#memberid").attr('data-memid',memberid);
		jQuery("#memberid").val(memberid);
		jQuery(".staff-member .sf-element-bx").removeClass('selected');
		jQuery(this).addClass('selected');
		jQuery(this).prop("checked",status);
		jQuery('.anymember').prop("checked",false);
		jQuery(".anymember").removeAttr("disabled");
	});
	/*Add Any member*/
	jQuery('body').on('click', '.anymember', function(){				
		jQuery(".staff-member .sf-element-bx").removeClass('selected');
		jQuery("#memberid").val('');
		jQuery("#memberid").attr('data-memid','');
		
		jQuery(".anymember").prop("disabled", this.checked).prop("checked", this.checked);
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
	
	function booking_free_checkout() 
	{
		var data = {
		  "action": "freecheckout",
		  "provider": $bookingdata.providerid,
		  "totalcost": totalcost,
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
		if(parseFloat(walletamount) < (parseFloat(totalcost) + parseFloat(adminfee))){
			jQuery( "<div class='alert alert-danger sf-custom-error'>"+param.insufficient_wallet_amount+"</div>" ).insertBefore( "#bookingjobform" );
				jQuery("html, body").animate({
				scrollTop: jQuery(".alert-danger").offset().top
			}, 1000);	
			return false;	
		}
		var data = {
		  "action": "walletcheckout",
		  "provider": $bookingdata.providerid,
		  "totalcost": totalcost,
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
						}else{
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
		  "provider": $bookingdata.providerid,
		  "totalcost": totalcost,
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
					  "provider": $bookingdata.providerid,
					  "stripeToken": token,
					  "totalcost": totalcost,
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
		
	jQuery('body').on('click', '.addcouponcode', function(){
		jQuery('.alert').remove();
		jQuery('#addcouponcode input[name="couponcode"]').val('');
		
		var sid = jQuery(this).data('sid');														  
		jQuery('#addcouponcode,.sf-couponcode-popup-overlay').fadeIn("slow");
		jQuery('#addcouponcode input[name="couponcode"]').attr('id','couponcode-'+sid);
		jQuery('.verifycoupon').attr('data-sid',sid);
		
		var $this = jQuery('#servicebtn-'+sid).data('servicedata');
		
		$bookingdata = jQuery( '.bookthisservices' ).data('bookingdata');
		
		var serviceid = $this.serviceid;

		var costtype = $this.costtype;

		var providerhours = $this.hours;

		jQuery('#serviceid-'+serviceid).removeClass('unselected').addClass('selected');

		calculate_servicecost();
	})
	
	jQuery('body').on('click', '.verifycoupon', function(){
		jQuery('.alert').remove();
		var sid = jQuery(this).data('sid');	
		var userid = jQuery(this).data('userid');
		var couponcode = jQuery('#couponcode-'+sid).val();
		
		var $this = jQuery('#servicebtn-'+sid).data('servicedata');;
		var cost = $this.cost;
		var costtype = $this.costtype;
		var hours = $this.hours;
		
		if(couponcode == ""){
			jQuery( "<div class='alert alert-danger'>"+param.req+"</div>" ).insertAfter( "#addcouponcode" );	
			return false;
		}else{
			var data = {
					  "action": "verify_couponcode",
					  "serviceid": sid,
					  "userid": userid,
					  "couponcode": couponcode,
					  "cost": cost,
					  "costtype": costtype,
					  "hours": hours,
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
							jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertAfter( "#addcouponcode" );	
							jQuery('#addcouponcode,.sf-couponcode-popup-overlay').fadeOut("slow");
							
							$this.discounttype = data['discount_type'];
							$this.discountvalue = data['discount_value'];
							$this.coupon = 'verified';
							$this.couponcode = couponcode;
							
							jQuery('#servicebtn-'+$this.serviceid).data('servicedata',$this);
							
							if(costtype == 'fixed'){
							jQuery('#serviceid-'+$this.serviceid+' .sf-provi-service-price').html(data['discountedcost']);
							}
							calculate_servicecost();
						}else{
							jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertAfter( "#addcouponcode" );	
						}

						return false;
					}

				});		
		}
		return false;
	});		
		
	jQuery('body').on('click', '.verifybookingcoupon', function(){
		jQuery('.alert').remove();
		var userid = jQuery(this).data('userid');	
		var couponcode = jQuery('#couponcode').val();
		
		if(couponcode == ""){
			jQuery( "<div class='alert alert-danger'>"+param.req+"</div>" ).insertAfter( "#addbookingcoupon" );	
			return false;
		}else{
			var data = {
					  "action": "verify_booking_couponcode",
					  "userid": userid,
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
							jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertAfter( "#addbookingcoupon" );	
							var updatedtotalcost = data['updatedtotalcost'];
							totaldiscount = data['discount'];
							jQuery('#totaldiscount').val(totaldiscount);
							calculate_commisionfee(updatedtotalcost,'discount');
						}else{
							jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertAfter( "#addbookingcoupon" );	
						}

						return false;
					}

				});		
		}
		return false;
	});
		
});
  
  