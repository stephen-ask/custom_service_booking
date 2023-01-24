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
				  
  	var map = null;
	var marker = null;
	var provider_id = '';
	var scost = '';
	var sid = '';
	var totalservicecost = 0;
	var totalhours = 0;
	var date = '';
	var mysetdate = '';
	var oldzipcode = '';
	var oldregion = '';
	var member_flag = 1;
	var adminfee;
	var totaldiscount;
	
	var month_flag = 1;
	var year_flag = 1;
	var twocheckout_month_flag = 1;
	var twocheckout_year_flag = 1;
	
	var datearr = [];
	var bookedarr = [];
	var allocatedarr = [];
	var daynumarr = [];
	var disabledates = [];
	var service_flag = 0;
	var servicearr = '';
	
	/*Display Services*/
	jQuery('form.book-now').on('change', 'select[name="region"]', function(){
		var region = jQuery('select[name="region"]').val();
		if(region != ""){
			jQuery("#bookingservices").show();	
		}else{
			jQuery("#bookingservices").hide();	
		}
	});
	
	jQuery(document).on('click','.set-marker-popup-close',function(){
		jQuery('.set-marker-popup').hide();
	});															   
	jQuery(document).on('click','#viewmylocation',function(){
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
	
	/*Update booking price according to services selected*/
	jQuery('body').on('click', '#bookingservices .aon-service-bx', function(){
		var serviceid = jQuery(this).data('id');
		var costtype = jQuery(this).data('costtype');
		var providerhours = jQuery(this).data('hours');
		
		if(jQuery(this).hasClass('selected') && (costtype == 'hourly' || costtype == 'perperson')) { 
			if(providerhours > 0){
				jQuery('#hours-outer-bx-'+serviceid).show();
				jQuery('#hours-'+serviceid).show();
				jQuery('#hours-'+serviceid).val(providerhours);
				jQuery('#hours-'+serviceid).attr('readonly','readonly');	
			}else{
				jQuery('#hours-'+serviceid).closest('.bootstrap-touchspin').show();
				converttoslider(serviceid,costtype);
			}
		}else{ 
			if(providerhours > 0){
				jQuery('#hours-outer-bx-'+serviceid).hide();
				jQuery('#hours-'+serviceid).hide();	
				jQuery('#hours-'+serviceid).val('');
				jQuery('#hours-'+serviceid).removeAttr('readonly','readonly');	
			}else{
				jQuery('#hours-'+serviceid).closest('.bootstrap-touchspin').hide();
			}
		}
		calculate_servicecost();
	});
	
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
	
	function calculate_servicecost(){
		var servicecost = 0;
		var servicehours = 0;
		service_flag = 0;
		servicearr = '';
		jQuery("#bookingservices .aon-service-bx").each( function() {
            if(jQuery(this).hasClass('selected')) { 
			service_flag = 1;
				var service = jQuery(this).val();
				var costtype = jQuery(this).data('costtype');
				var cost = jQuery(this).data('cost');
				var serviceid = jQuery(this).data('id');
				
				var discounttype = jQuery('#serbx-'+serviceid).attr('data-discounttype');
				var discountvalue = jQuery('#serbx-'+serviceid).attr('data-discountvalue');
				var coupon = jQuery('#serbx-'+serviceid).attr('data-coupon');
				var couponcode = jQuery('#serbx-'+serviceid).attr('data-couponcode');
				
				if(costtype == 'fixed'){
					var hours = 0;
					var discount = calculate_discount(coupon,discounttype,discountvalue,cost);	
					cost = calculate_discount_cost(discount,cost);
					servicearr = addto_service_array(servicearr,serviceid,hours,couponcode,discount);
					servicecost = parseFloat(servicecost) + parseFloat(cost);
				}else if(costtype == 'hourly'){
					var hrmin = 0;
					var hours = jQuery('#hours-'+serviceid).val();
					var $hourflag = jQuery(this).data('hours');
					
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
					}else{
					var discount = calculate_discount(coupon,discounttype,discountvalue,cost);	
					cost = calculate_discount_cost(discount,cost);
					servicecost = parseFloat(servicecost) + (parseFloat(cost));	
					servicearr = addto_service_array(servicearr,serviceid,hours,couponcode,discount);
					}
					servicehours = parseFloat(servicehours) + parseFloat(hrmin);
				}else if(costtype == 'perperson'){
					
					var hours = jQuery('#hours-'+serviceid).val();
					var $hourflag = jQuery(this).data('hours');
					
					if($hourflag > 0){
					var tcost = parseFloat(cost) * parseFloat($hourflag);	
					var discount = calculate_discount(coupon,discounttype,discountvalue,tcost);	
					cost = calculate_discount_cost(discount,tcost);
					servicecost = parseFloat(servicecost) + parseFloat(cost);
					servicearr = addto_service_array(servicearr,serviceid,$hourflag,couponcode,discount);
					}else if(hours > 0){
					var tcost = parseFloat(cost) * parseFloat(hours);	
					var discount = calculate_discount(coupon,discounttype,discountvalue,tcost);	
					cost = calculate_discount_cost(discount,tcost);
					servicecost = parseFloat(servicecost) + (parseFloat(cost));	
					servicearr = addto_service_array(servicearr,serviceid,hours,couponcode,discount);
					}else{
					var discount = calculate_discount(coupon,discounttype,discountvalue,cost);	
					cost = calculate_discount_cost(discount,cost);
					servicecost = parseFloat(servicecost) + (parseFloat(cost));	
					servicearr = addto_service_array(servicearr,serviceid,hours,couponcode,discount);
					}
					servicehours = parseFloat(servicehours) + parseFloat(hours);
				}
			}
			
        });
		jQuery('#servicearr').val(servicearr);
		totalservicecost = servicecost;
		totalhours = servicehours;
		calculate_totalcost();
	}
	
	function addto_service_array(servicearr,serviceid,hours,coupon,discount){
		servicearr = servicearr + serviceid +'-'+ hours+'-'+ discount +'-'+ coupon + '%%';
		return servicearr;
	}
	
	function calculate_totalcost(){
		totalcost = parseFloat(mincost) + parseFloat(totalservicecost);
		totalcost = totalcost.toFixed(2);
		jQuery('#bookingamount').html(currencysymbol+totalcost);
		jQuery("#totalcost").val(totalcost);	
		calculate_commisionfee(totalcost);
	}
	
	function converttoslider(serviceid,costtype){
	if(costtype == 'perperson'){
		var str = param.perpersion_short;
		var step = 1;
		var maxlimit = 500;
	}else{
		var str = param.perhour_short;
		var step = 0.5;
		var maxlimit = 12;
	}	
	//Apply touch spin js on cleaning hours
	jQuery('#hours-'+serviceid).TouchSpin({
        min: 1,
		max: maxlimit,
		initval: 1,
		step: step,
		decimals: 1,
		postfix: str
	}).on('change', function() {
		calculate_servicecost();
	});
	}
	
	jQuery('body').on('click', '.addcouponcode', function(){
		jQuery('.alert').remove();
		jQuery('#addcouponcode input[name="couponcode"]').val('');
		
		var sid = jQuery(this).data('sid');														  
		jQuery('#addcouponcode,.sf-couponcode-popup-overlay').fadeIn("slow");
		jQuery('#addcouponcode input[name="couponcode"]').attr('id','couponcode-'+sid);
		jQuery('.verifycoupon').attr('data-sid',sid);
		
		var $this = jQuery('#serbx-'+sid);
		
		
		var serviceid = jQuery($this).data('id');

		var costtype = jQuery($this).data('costtype');

		var providerhours = jQuery($this).data('hours');

		jQuery($this).removeClass('unselected').addClass('selected');

		if(jQuery($this).hasClass('selected') && (costtype == 'hourly' || costtype == 'perperson')) { 

			if(providerhours > 0){

				jQuery('#hours-outer-bx-'+serviceid).show();

				jQuery('#hours-'+serviceid).show();

				jQuery('#hours-'+serviceid).val(providerhours);

				jQuery('#hours-'+serviceid).attr('readonly','readonly');	

			}else{

				jQuery('#hours-'+serviceid).closest('.bootstrap-touchspin').show();

				converttoslider(serviceid,costtype);

			}

		}else{ 

			

			if(providerhours > 0){

				jQuery('#hours-outer-bx-'+serviceid).hide();

				jQuery('#hours-'+serviceid).hide();	

				jQuery('#hours-'+serviceid).val('');

				jQuery('#hours-'+serviceid).removeAttr('readonly','readonly');	

			}else{

				jQuery('#hours-'+serviceid).closest('.bootstrap-touchspin').hide();

			}

		}

		calculate_servicecost();
	})
	
	jQuery('body').on('click', '.verifycoupon', function(){
		jQuery('.alert').remove();
		var sid = jQuery(this).attr('data-sid');
		var userid = jQuery(this).data('userid');
		var couponcode = jQuery('#couponcode-'+sid).val();
		var cost = jQuery('#serbx-'+sid).data('cost');
		var costtype = jQuery('#serbx-'+sid).data('costtype');
		var hours = jQuery('#serbx-'+sid).data('hours');
		
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
							jQuery('#serbx-'+sid).attr('data-discounttype',data['discount_type']);
							jQuery('#serbx-'+sid).attr('data-discountvalue',data['discount_value']);
							jQuery('#serbx-'+sid).attr('data-coupon','verified');
							jQuery('#serbx-'+sid).attr('data-couponcode',couponcode);
							if(costtype == 'fixed'){
							jQuery('#serbx-'+sid+' .aon-service-price').html(data['discountedcost']);
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
	
	function calculate_commisionfee(totalcost,dicountchk = ''){
		if(adminfeetype == 'fixed'){
				adminfee = parseFloat(adminfeefixed);	
				adminfee = adminfee.toFixed(2);
			}else if(adminfeetype == 'percentage'){
				adminfee = parseFloat(totalcost) * (parseFloat(adminfeepercentage)/100);
				adminfee = adminfee.toFixed(2);
			}
			if(dicountchk != 'discount'){
			jQuery("#totalcost").val(totalcost);
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
	
			/*Get Time Slots*/
			jQuery('ul.timelist').on('click', 'li', function(){
				jQuery(this).addClass('active').siblings().removeClass('active');
				service_finder_resetMembers();
				var slot = jQuery(this).attr('data-source');
				var t = jQuery(this).find("span").html();
				jQuery("#boking-slot").attr('data-slot',t);
				jQuery("#boking-slot").val(slot);
				
				if(jQuery.inArray("availability", caps) > -1 && jQuery.inArray("staff-members", caps) > -1 && staffmember == 'yes'){
				var zipcode = jQuery('input[name="zipcode"]').val();
				var region = jQuery('select[name="region"]').val();
				var provider_id = jQuery('#provider').attr('data-provider');
				var date = jQuery('#selecteddate').attr('data-seldate');
				region = Encoder.htmlEncode(region);
				var data = {
					  "action": "load_members",
					  "zipcode": zipcode,
					  "region": region,
					  "provider_id": provider_id,
					  "date": date,
					  "slot": slot,
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
							jQuery("#panel-2").find(".alert").remove();
							 if(data != null){
								if(data['status'] == 'success'){
									jQuery("#panel-2").find("#members").html(data['members']);
									if(data['totalmember'] > 0){
									jQuery("#panel-2").find("#members").append('<div class="col-lg-12"><div class="row"><div class="checkbox text-left"><input id="anymember" class="anymember" type="checkbox" name="anymember[]" value="yes" checked><label for="anymember">'+param.anyone+'</label></div></div></div>');
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
				
				
			});	
			/*Staff Member click event*/
			jQuery('body').on('click', '.staff-member .sf-element-bx', function(){																		
				var memberid = jQuery(this).attr("data-id");																
				jQuery("#memberid").val(memberid);
				jQuery(".staff-member .sf-element-bx").removeClass('selected');
				jQuery(this).addClass('selected');
				jQuery(this).prop("checked",status);
				jQuery('.anymember').prop("checked",false);
				jQuery(".anymember").removeAttr("disabled");
			});
			/*Select any staff member*/
			jQuery('body').on('click', '.anymember', function(){				
				jQuery(".staff-member .sf-element-bx").removeClass('selected');
				jQuery("#memberid").val('');
				jQuery("#memberid").attr('data-memid','');
				
				jQuery(".anymember")
				 .prop("disabled", this.checked)
				 .prop("checked", this.checked);
			});
			/*Step 1 Start*/
			jQuery('body').on('click', '#panel-1 button.edit', function(){
				
				jQuery(this).parent("h6").parent("div").find(".panel-summary").html('');
				jQuery("#panel-2").find(".f-row").addClass('hidden');
				jQuery("#panel-3").find(".f-row").addClass('hidden');
				jQuery("#panel-4").find(".f-row").addClass('hidden');
				jQuery("#panel-1 .f-row").removeClass('hidden');
				
				jQuery("#panel-2 h6").find("button.edit").remove();
				jQuery("#panel-3 h6").find("button.edit").remove();
				jQuery("#panel-4 h6").find("button.edit").remove();
				
			});
			/*Staep 2 Start*/
			jQuery('body').on('click', '#panel-2 button.edit', function(){
				
				jQuery(this).parent("h6").parent("div").find(".panel-summary").html('');
				jQuery("#panel-1").find(".f-row").addClass('hidden');
				jQuery("#panel-3").find(".f-row").addClass('hidden');
				jQuery("#panel-4").find(".f-row").addClass('hidden');
				jQuery("#panel-2 .f-row").removeClass('hidden');
				
				jQuery("#panel-3 h6").find("button.edit").remove();
				jQuery("#panel-4 h6").find("button.edit").remove();
				
			});
			/*Staep 3 Start*/
			jQuery('body').on('click', '#panel-3 button.edit', function(){
				
				jQuery(this).parent("h6").parent("div").find(".panel-summary").html('');
				jQuery("#panel-1").find(".f-row").addClass('hidden');
				jQuery("#panel-2").find(".f-row").addClass('hidden');
				jQuery("#panel-4").find(".f-row").addClass('hidden');
				jQuery("#panel-3 .f-row").removeClass('hidden');
				
				jQuery("#panel-4 h6").find("button.edit").remove();
				
			});
			/*Step 4 Start*/
			jQuery('body').on('click', '#panel-4 button.edit', function(){
				
				jQuery(this).parent("h6").parent("div").find(".panel-summary").html('');
				jQuery("#panel-1").find(".f-row").addClass('hidden');
				jQuery("#panel-2").find(".f-row").addClass('hidden');
				jQuery("#panel-3").find(".f-row").addClass('hidden');
				jQuery("#panel-4 .f-row").removeClass('hidden');
				
			});
	
	/*Gallery*/
			jQuery('.gallery-thums').on('click', 'div.item a', function(){
				jQuery('.loading-cover').show();																		
				var src = jQuery(this).attr('data-src');	 
				jQuery('.galley-details').find('.item a img').load(function() {
				  jQuery('.loading-cover').hide();
				}).attr('src',src);
				

			});
			
	/*Add to Favorite*/
	jQuery('body').on('click', '.add-favorite', function(){

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
								
								jQuery( '<a href="javascript:;" class="remove-favorite" data-proid="'+providerid+'" data-userid="'+userid+'"><i class="fa fa-heart"></i>'+param.my_fav+'</a>' ).insertBefore( ".add-favorite" );
								jQuery('.add-favorite').remove();

							}

							
						}

					});																
	});
	/*Remove from Favorite*/
	jQuery('body').on('click', '.remove-favorite', function(){

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
								
								jQuery( '<a href="javascript:;" class="add-favorite" data-proid="'+providerid+'" data-userid="'+userid+'"><i class="fa fa-heart"></i>'+param.add_to_fav+'</a>' ).insertBefore( ".remove-favorite" );
								jQuery('.remove-favorite').remove();

							}

							
						}

					});																
	});
	
	provider_id = jQuery('#provider').attr('data-provider');
	
	
	var data = {
				  "action": "reset_bookingcalendar",
				  "provider_id": provider_id
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
					daynumarr = jQuery.parseJSON(data['daynum']);
					datearr = jQuery.parseJSON(data['dates']);
					bookedarr = jQuery.parseJSON(data['bookeddates']);
					disabledates = jQuery.parseJSON(data['disabledates']);
					service_finder_deleteCookie('setselecteddate');
					service_finder_deleteCookie('otppass'); 
					service_finder_deleteCookie('vaildemail');
					jQuery("#my-calendar").zabuto_calendar({
						today: true,
						show_previous: false,
						mode : 'add',
						daynum : daynumarr,
						datearr : datearr,
						show_next: disablemonths,
						disabledates : disabledates,
						bookedarr : bookedarr,
                        action: function () {
							jQuery('.dow-clickable').removeClass("selected");
							jQuery(this).addClass("selected");
							date = jQuery("#" + this.id).data("date");
							service_finder_setCookie('setselecteddate', date); 
							
							if(jQuery.inArray("availability", caps) > -1 && jQuery.inArray("staff-members", caps) > -1 && staffmember == 'yes'){
								return service_finder_timeslotCallback(this.id, provider_id, totalhours);
							}else if(jQuery.inArray("availability", caps) > -1 && jQuery.inArray("staff-members", caps) > -1 && (staffmember == 'no' || staffmember == "")){
								return service_finder_timeslotCallback(this.id, provider_id, totalhours);
							}else if(jQuery.inArray("availability", caps) > -1 && (jQuery.inArray("staff-members", caps) == -1 || (staffmember == 'no' || staffmember == ""))){
								return service_finder_timeslotCallback(this.id, provider_id, totalhours);
							}else if(jQuery.inArray("availability", caps) == -1 && jQuery.inArray("staff-members", caps) > -1 && staffmember == 'yes'){
								return service_finder_memberCallback(this.id, provider_id);	
							}else if(jQuery.inArray("availability", caps) == -1 && (jQuery.inArray("staff-members", caps) == -1 || (staffmember == 'no' || staffmember == ""))){
								jQuery('#selecteddate').attr('data-seldate',date);	
							}
                        },
                    });
					
					}else if(data['status'] == 'error'){
					}
					
					
				
				}

			});
	

	/*Booknow wizard continue action*/
	jQuery('.book-now')
        .bootstrapValidator({
            message: param.not_valid,
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
				zipcode: {
					validators: {
						notEmpty: {
							message: param.postal_code
						}
					}
				},
				region: {
					validators: {
						notEmpty: {
							message: param.region
						}
					}
				},
				'service[]': {
					validators: {
						choice: {
							min: 1,
							max: 100,
							message: param.select_service
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
		.on('click',  '#save-booking', function(e) {
           if(jQuery('.book-now select[name="card_month"] option:selected').val()==""){month_flag = 1;jQuery('.book-now select[name="card_month"]').parent('div').addClass('has-error').removeClass('has-success'); jQuery('form.book-now').find('input[type="submit"]').prop('disabled', false);}else{month_flag = 0;jQuery('.book-now select[name="card_month"]').parent('div').removeClass('has-error').addClass('has-success'); jQuery('form.book-now').find('input[type="submit"]').prop('disabled', false);}
			
			if(jQuery('.book-now select[name="card_year"] option:selected').val()==""){year_flag = 1;jQuery('.book-now select[name="card_year"]').parent('div').addClass('has-error').removeClass('has-success'); jQuery('form.book-now').find('input[type="submit"]').prop('disabled', false);}else{year_flag = 0;jQuery('.book-now select[name="card_year"]').parent('div').removeClass('has-error').addClass('has-success'); jQuery('form.book-now').find('input[type="submit"]').prop('disabled', false);}
			
			if(jQuery('.book-now select[name="twocheckout_card_month"] option:selected').val()==""){twocheckout_month_flag = 1;jQuery('.book-now select[name="twocheckout_card_month"]').parent('div').addClass('has-error').removeClass('has-success'); jQuery('form.book-now').find('input[type="submit"]').prop('disabled', false);}else{twocheckout_month_flag = 0;jQuery('.book-now select[name="twocheckout_card_month"]').parent('div').removeClass('has-error').addClass('has-success'); jQuery('form.book-now').find('input[type="submit"]').prop('disabled', false);}
			
			if(jQuery('.book-now select[name="twocheckout_card_year"] option:selected').val()==""){twocheckout_year_flag = 1;jQuery('.book-now select[name="twocheckout_card_year"]').parent('div').addClass('has-error').removeClass('has-success'); jQuery('form.book-now').find('input[type="submit"]').prop('disabled', false);}else{twocheckout_year_flag = 0;jQuery('.book-now select[name="twocheckout_card_year"]').parent('div').removeClass('has-error').addClass('has-success'); jQuery('form.book-now').find('input[type="submit"]').prop('disabled', false);}
			
	    })
		.on('change', 'input[name="bookingpayment_mode"]', function() {
			var paymode = jQuery(this).val();
			if(paymode == 'stripe'){
				jQuery('#bookingcardinfo').show();
				jQuery('#bookingtwocheckoutcardinfo').hide();
				jQuery('#wiredinfo').hide();
				jQuery('#bookingpayulatamcardinfo').hide();
											jQuery('.book-now')
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
											});
											
											jQuery('.book-now')
											.bootstrapValidator('removeField', 'payulatam_cardtype')
											.bootstrapValidator('removeField', 'payulatam_card_number')
											.bootstrapValidator('removeField', 'payulatam_card_cvc')
											.bootstrapValidator('removeField', 'payulatam_card_month')
											.bootstrapValidator('removeField', 'payulatam_card_year')
											.bootstrapValidator('removeField', 'twocheckout_card_number')
											.bootstrapValidator('removeField', 'twocheckout_card_cvc')
											.bootstrapValidator('removeField', 'twocheckout_card_month')
											.bootstrapValidator('removeField', 'twocheckout_card_year');
			}else if(paymode == 'twocheckout'){
				jQuery('#bookingtwocheckoutcardinfo').show();
				jQuery('#bookingcardinfo').hide();
				jQuery('#wiredinfo').hide();
				jQuery('#bookingpayulatamcardinfo').hide();
											jQuery('.book-now')
											.bootstrapValidator('addField', 'twocheckout_card_number', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											})
											.bootstrapValidator('addField', 'twocheckout_card_cvc', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											})
											.bootstrapValidator('addField', 'twocheckout_card_month', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											})
											.bootstrapValidator('addField', 'twocheckout_card_year', {
												validators: {
													notEmpty: {
														message: param.req
													},
													digits: {message: param.only_digits},
												}
											});
											
											jQuery('.book-now')
											.bootstrapValidator('removeField', 'payulatam_cardtype')
											.bootstrapValidator('removeField', 'payulatam_card_number')
											.bootstrapValidator('removeField', 'payulatam_card_cvc')
											.bootstrapValidator('removeField', 'payulatam_card_month')
											.bootstrapValidator('removeField', 'payulatam_card_year')
											.bootstrapValidator('removeField', 'card_number')
											.bootstrapValidator('removeField', 'card_cvc')
											.bootstrapValidator('removeField', 'card_month')
											.bootstrapValidator('removeField', 'card_year');
			
			}else if(paymode == 'payulatam'){
				jQuery('#bookingtwocheckoutcardinfo').hide();
				jQuery('#bookingcardinfo').hide();
				jQuery('#wiredinfo').hide();
				jQuery('#bookingpayulatamcardinfo').show();
											jQuery('.book-now')
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
											});
											
											jQuery('.book-now')
											.bootstrapValidator('removeField', 'card_number')
											.bootstrapValidator('removeField', 'card_cvc')
											.bootstrapValidator('removeField', 'card_month')
											.bootstrapValidator('removeField', 'card_year')
											.bootstrapValidator('removeField', 'twocheckout_card_number')
											.bootstrapValidator('removeField', 'twocheckout_card_cvc')
											.bootstrapValidator('removeField', 'twocheckout_card_month')
											.bootstrapValidator('removeField', 'twocheckout_card_year');
			
			}else if(paymode == 'wired'){
				jQuery('#wiredinfo').show();
				jQuery('#bookingtwocheckoutcardinfo').hide();
				jQuery('#bookingcardinfo').hide();
				jQuery('#bookingpayulatamcardinfo').hide();
											jQuery('.book-now')
											.bootstrapValidator('removeField', 'card_number')
											.bootstrapValidator('removeField', 'card_cvc')
											.bootstrapValidator('removeField', 'card_month')
											.bootstrapValidator('removeField', 'card_year')
											.bootstrapValidator('removeField', 'payulatam_cardtype')
											.bootstrapValidator('removeField', 'payulatam_card_number')
											.bootstrapValidator('removeField', 'payulatam_card_cvc')
											.bootstrapValidator('removeField', 'payulatam_card_month')
											.bootstrapValidator('removeField', 'payulatam_card_year')
											.bootstrapValidator('removeField', 'twocheckout_card_number')
											.bootstrapValidator('removeField', 'twocheckout_card_cvc')
											.bootstrapValidator('removeField', 'twocheckout_card_month')
											.bootstrapValidator('removeField', 'twocheckout_card_year');
			}else{
				jQuery('#bookingcardinfo').hide();
				jQuery('#bookingtwocheckoutcardinfo').hide();
				jQuery('#wiredinfo').hide();
				jQuery('#bookingpayulatamcardinfo').hide();
											jQuery('.book-now')
											.bootstrapValidator('removeField', 'card_number')
											.bootstrapValidator('removeField', 'card_cvc')
											.bootstrapValidator('removeField', 'card_month')
											.bootstrapValidator('removeField', 'card_year')
											.bootstrapValidator('removeField', 'payulatam_cardtype')
											.bootstrapValidator('removeField', 'payulatam_card_number')
											.bootstrapValidator('removeField', 'payulatam_card_cvc')
											.bootstrapValidator('removeField', 'payulatam_card_month')
											.bootstrapValidator('removeField', 'payulatam_card_year')
											.bootstrapValidator('removeField', 'twocheckout_card_number')
											.bootstrapValidator('removeField', 'twocheckout_card_cvc')
											.bootstrapValidator('removeField', 'twocheckout_card_month')
											.bootstrapValidator('removeField', 'twocheckout_card_year');
			}
		})
		.on('click', '.otp', function() {
				
				var emailid = jQuery("#email").val();
				jQuery(".alert-danger").remove();
				if(emailid == ''){
					jQuery( '<div class="alert alert-danger">'+param.email_req+'</div>' ).insertAfter( ".otp-section .input-group" );
					return false;
				}
				var data = {
						  "action": "sendotp",
						  "emailid": emailid,
						};
						
				var formdata = jQuery.param(data);
				
				jQuery.ajax({

						type: 'POST',

						url: ajaxurl,
						
						beforeSend: function() {
							jQuery('.loading-area').show();
						},
						
						data: formdata,

						success:function (data, textStatus) {
							service_finder_clearconsole();
							jQuery('.loading-area').hide();
							jQuery( '<div class="alert alert-success padding-5 otpsuccess">'+param.otp_mail+'</div>' ).insertAfter( ".otp-section .input-group" );
							service_finder_setCookie('otppass', data); 
							service_finder_setCookie('vaildemail',emailid);
							jQuery(".otp").remove();
							
											jQuery('.book-now')
											.bootstrapValidator('addField', 'fillotp', {
												validators: {
															notEmpty: {
																message: param.otp_pass
															},
															callback: {
																message: param.right,
																callback: function(value, validator, $field) {
																	if(service_finder_getCookie('otppass') == value){
																	jQuery(".otpsuccess").remove();	
																	return true;
																	}else{
																	jQuery(".otpsuccess").remove();	
																	return false;	
																	}
																}
															}
														}
											})
											.bootstrapValidator('addField', 'email', {
												validators: {
															emailAddress: {
																message: param.signup_user_email
															},
															callback: {
																message: param.reconfirm_email,
																callback: function(value, validator, $field) {
																	if(service_finder_getCookie('vaildemail') == value){
																	return true;
																	}else{
																	jQuery(".otp").remove();
																	jQuery(".otpsuccess").remove();	
																	jQuery( '<a href="javascript:;" class="otp">'+param.gen_otp+'</a>' ).insertAfter( ".otp-section .input-group" );
																	
																	return false;	
																	}
																}
															}
														}
											});
						}

					});					  
		})
		.on('click', '#save-zipcodes', function() {
												
						jQuery('.book-now').bootstrapValidator('validate');
						
						var zipcode = jQuery('input[name="zipcode"]').val();
						var region = jQuery('select[name="region"]').val();
						
						if(booking_basedon == 'zipcode'){
						if(zipcode != ""){	
							var data = {
								  "action": "check_zipcode",
								  "zipcode": zipcode,
								  "provider_id": provider_id,
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
									if(data['status'] == 'success' && ((service_flag == 1 || checkjobauthor == 1 || checkquoteauthor == 1) || booking_charge_on_service == 'no')){
										jQuery("#panel-1 .f-row").addClass('hidden');
										jQuery("#panel-1").find(".panel-summary").html(zipcode);
										jQuery("#panel-1 h6").append('<button class="btn btn-border btn-xs edit"><i class="fa fa-pencil"></i>'+param.edit_text+'</button>');
										jQuery("#panel-2 .f-row").removeClass('hidden');
										jQuery("#panel-2").find(".panel-summary").html('');
										if(oldzipcode != zipcode){
											
										
										
										jQuery("#panel-3").find(".panel-summary").html('');
										jQuery("#panel-4").find(".panel-summary").html('');
										
										jQuery('.timeslots').html('');
										jQuery('#members').html('');
										
										jQuery('#boking-slot').val('');
										jQuery('#memberid').val('');
										jQuery('#selecteddate').val('');
										}
										
										oldzipcode = zipcode;
										
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
										.bootstrapValidator('addField', 'fillotp', {
											validators: {
												notEmpty: {
														message: param.req
													},
												callback: {
																message: param.edit_text,
																callback: function(value, validator, $field) {
																	if(service_finder_getCookie('otppass') == value && service_finder_getCookie('otppass') != ""){
																	return true;
																	}else{
																	return false;	
																	}
																}
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
										});
										
									}else{
										jQuery(".f-row").addClass('hidden');
										jQuery("#panel-1 .f-row").removeClass('hidden');
										jQuery(".panel-summary").html('');
										jQuery("button.edit").remove();
										if(checkquoteauthor == 1 || checkjobauthor == 1 || booking_charge_on_service == 'no'){
										jQuery("#panel-1").find('.form-step-bx').append('<div class="col-md-12 clearfix"><div class="alert alert-danger">'+param.service_not_avl+'</div></div>');
										}else{
										jQuery("#panel-1").find('.form-step-bx').append('<div class="col-md-12 clearfix"><div class="alert alert-danger">'+param.notavl_select_service+'</div></div>');	
										}
									}
								}
				
							});	
						}
						}else if(booking_basedon == 'region'){
							
									jQuery("#panel-1").find(".alert").remove();
									
									if(region != "" && ((service_flag == 1 || checkjobauthor == 1 || checkquoteauthor == 1) || booking_charge_on_service == 'no')){
										jQuery("#panel-1 .f-row").addClass('hidden');
										jQuery("#panel-1").find(".panel-summary").html(region);
										jQuery("#panel-1 h6").append('<button class="btn btn-border btn-xs edit"><i class="fa fa-pencil"></i>'+param.edit_text+'</button>');
										jQuery("#panel-2 .f-row").removeClass('hidden');
										jQuery("#panel-2").find(".panel-summary").html('');
										if(oldregion != region){
											
										
										
										jQuery("#panel-3").find(".panel-summary").html('');
										jQuery("#panel-4").find(".panel-summary").html('');
										
										jQuery('.timeslots').html('');
										jQuery('#members').html('');
										
										jQuery('#boking-slot').val('');
										jQuery('#memberid').val('');
										jQuery('#selecteddate').val('');
										}
										
										oldregion = region;
										
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
										.bootstrapValidator('addField', 'fillotp', {
											validators: {
												notEmpty: {
														message: param.req
													},
												callback: {
																message: param.otp_right,
																callback: function(value, validator, $field) {
																	if(service_finder_getCookie('otppass') == value && service_finder_getCookie('otppass') != ""){
																	return true;
																	}else{
																	return false;	
																	}
																}
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
										});
										
									}else{
										jQuery(".f-row").addClass('hidden');
										jQuery("#panel-1 .f-row").removeClass('hidden');
										jQuery(".panel-summary").html('');
										jQuery("button.edit").remove();
										if(checkquoteauthor == 1 || checkjobauthor == 1 || booking_charge_on_service == 'no'){
										jQuery("#panel-1").find('.form-step-bx').append('<div class="col-md-12 clearfix"><div class="alert alert-danger">'+param.region+'</div></div>');
										}else{
										jQuery("#panel-1").find('.form-step-bx').append('<div class="col-md-12 clearfix"><div class="alert alert-danger">'+param.region_and_service+'</div></div>');			
										}
									}
								
						}else if(booking_basedon == 'open'){
							
						if(((service_flag == 1 || checkjobauthor == 1 || checkquoteauthor == 1) || booking_charge_on_service == 'no')){
										jQuery("#panel-1 .f-row").addClass('hidden');
										jQuery("#panel-1").find(".panel-summary").html(zipcode);
										jQuery("#panel-1 h6").append('<button class="btn btn-border btn-xs edit"><i class="fa fa-pencil"></i>'+param.edit_text+'</button>');
										jQuery("#panel-2 .f-row").removeClass('hidden');
										jQuery("#panel-2").find(".panel-summary").html('');
										if(oldzipcode != zipcode){
											
										
										
										jQuery("#panel-3").find(".panel-summary").html('');
										jQuery("#panel-4").find(".panel-summary").html('');
										
										jQuery('.timeslots').html('');
										jQuery('#members').html('');
										
										jQuery('#boking-slot').val('');
										jQuery('#memberid').val('');
										jQuery('#selecteddate').val('');
										}
										
										oldzipcode = zipcode;
										
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
										.bootstrapValidator('addField', 'fillotp', {
											validators: {
												notEmpty: {
														message: param.req
													},
												callback: {
																message: param.otp_right,
																callback: function(value, validator, $field) {
																	if(service_finder_getCookie('otppass') == value && service_finder_getCookie('otppass') != ""){
																	return true;
																	}else{
																	return false;	
																	}
																}
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
										});
										
									}else{
										jQuery(".f-row").addClass('hidden');
										jQuery("#panel-1 .f-row").removeClass('hidden');
										jQuery(".panel-summary").html('');
										jQuery("button.edit").remove();
										if(checkquoteauthor == 1 || checkjobauthor == 1 || booking_charge_on_service == 'no'){
										jQuery("#panel-1").find('.form-step-bx').append('<div class="col-md-12 clearfix"><div class="alert alert-danger">'+param.service_not_avl+'</div></div>');
										}else{
										jQuery("#panel-1").find('.form-step-bx').append('<div class="col-md-12 clearfix"><div class="alert alert-danger">'+param.notavl_select_service+'</div></div>');	
										}
									}
						}
		})
		.on('click', '#save-timeslot', function() {
						jQuery("#panel-2").find(".alert").remove();
						var getslot = jQuery("#boking-slot").attr("data-slot");
						var date = jQuery('#selecteddate').attr('data-seldate');
						jQuery("#selecteddate").val(date);

						if(jQuery.inArray("availability", caps) > -1 && jQuery.inArray("staff-members", caps) > -1 && staffmember == 'yes'){
							jQuery("#panel-2").find(".panel-summary").html(date+ ' ' +getslot);
						}else if(jQuery.inArray("availability", caps) > -1 && (jQuery.inArray("staff-members", caps) == -1 || (staffmember == 'no' || staffmember == ""))){
							jQuery("#panel-2").find(".panel-summary").html(date+ ' ' +getslot);
						}else if(jQuery.inArray("availability", caps) == -1 && jQuery.inArray("staff-members", caps) > -1 && staffmember == 'yes'){
							jQuery("#panel-2").find(".panel-summary").html(date);
						}else if(jQuery.inArray("availability", caps) == -1 && (jQuery.inArray("staff-members", caps) == -1 || (staffmember == 'no' || staffmember == ""))){
							jQuery("#panel-2").find(".panel-summary").html(date);
						}
										jQuery("#panel-3 h6").remove('button.edit');
										jQuery("#panel-4 h6").remove('button.edit');
										
										
										jQuery("#panel-3").find(".panel-summary").html('');
										jQuery("#panel-4").find(".panel-summary").html('');
										
						if(jQuery.inArray("availability", caps) > -1){
						if(getslot != ""){
							if(member_flag == 1){
							jQuery("#panel-2 .f-row").addClass('hidden');
							jQuery("#panel-2 h6.mainheading").append('<button class="btn btn-border btn-xs edit"><i class="fa fa-pencil"></i>'+param.edit_text+'</button>');
							jQuery("#panel-3 .f-row").removeClass('hidden');	
							}else{
							jQuery("#step2").find('.tab-pane-inr').append('<div class="col-md-12 clearfix"><div class="alert alert-danger">'+param.member_select+'</div></div>');
							}
						}else{
							jQuery("#panel-2").find('.form-step-bx').append('<div class="col-md-12 clearfix"><div class="alert alert-danger">'+param.timeslot+'</div></div>');
						}											   
						}else{
							jQuery("#panel-2 .f-row").addClass('hidden');
							jQuery("#panel-2 h6.mainheading").append('<button class="btn btn-border btn-xs edit"><i class="fa fa-pencil"></i>'+param.edit_text+'</button>');
							jQuery("#panel-3 .f-row").removeClass('hidden');	
						}
		})
		.on('click', '#save-cusinfo', function() {
						
						var $validator = jQuery('.book-now').data('bootstrapValidator').validate();
						
						jQuery("#panel-4").find(".panel-summary").html('');
						
						calculate_commisionfee(totalcost);
						
						var firstname = jQuery('input[name="firstname"]').val();
						var lastname = jQuery('input[name="lastname"]').val();
						var email = jQuery('input[name="email"]').val();
						var fillotp = jQuery('input[name="fillotp"]').val();
						var phone = jQuery('input[name="phone"]').val();
						var address = jQuery('input[name="address"]').val();
						var city = jQuery('input[name="city"]').val();
						var state = jQuery('input[name="state"]').val();
						var country = jQuery('input[name="country"]').val();

						if($validator.isValid()){
							if(totalcost > 0){
							if(woopayment){
							
							if(walletsystem == true || skipoption == true || (offersystem == true && offermethod == 'booking')){

								var $html = '';
								if(offersystem == true && offermethod == 'booking'){
								$html += '<div class="viewcoupon-bx">' +
										'<button class="btn btn-primary btn-sm" data-toggle="collapse" data-target="#addwoobookingcoupon"><i class="fa fa-arrow-circle-down"></i> '+param.have_coupon+'</button> ' +
										'<div id="addwoobookingcoupon" class="collapse">' +
										'<input type="text" name="woocouponcode" id="woocouponcode" class="form-control sf-form-control">' +
										'<a href="javascropt:;" class="verifywoobookingcoupon btn btn-custom">'+param.verify+'</a>' +
										'</div> ' +
										'</div> ';
								}
								if(walletsystem == true || skipoption == true){
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
										  '<img src="'+imgpath+'/payment/wallet.jpg" title="Wallet" alt="Wallet">'+
										'</div> ';
								}
								$html += '<div class="radio sf-radio-checkbox sf-payments-outer">'+
										  '<input type="radio" value="woopayment" name="booking_woopayment" id="booking_woopayment" >'+
										  '<label for="booking_woopayment">'+param.checkout+'</label>'+
										  '<img src="'+imgpath+'/payment/woopayment.jpg" title="'+param.checkout+'" alt="'+param.checkout+'">'+
										'</div> ';
								if(skipoption == true){		
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
												
												if(woooption == '' && (walletsystem == true || skipoption == true)){
													jQuery('.alert').remove();	
													jQuery( "<div class='alert alert-danger'>"+param.payment_method_req+"</div>" ).insertAfter( "#bookingwalletpayment" );
													return false;
												}
												if(woooption == "wallet"){
													if(charge_admin_fee_from == 'customer')
													{
														var walletchargeamount = parseFloat(totalcost) + parseFloat(adminfee);
													}else{
														var walletchargeamount = parseFloat(totalcost);
													}
													if(parseFloat(walletamount) < parseFloat(walletchargeamount)){
														jQuery( "<div class='alert alert-danger'>"+param.insufficient_wallet_amount+"</div>" ).insertBefore( "#bookingwalletpayment" );
															jQuery("html, body").animate({
															scrollTop: jQuery(".alert-danger").offset().top
														}, 1000);	
														return false;	
													}
													var data = {
													  "action": "walletcheckout",
													  "provider": provider_id,
													  "totalcost": parseFloat(totalcost),
													  "bookingdate": date,
													  "bookingpayment_mode": 'wallet',
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
																	if(data['redirecturl'] != ''){
																	window.location = data['redirecturl'];	
																	}else{
																	jQuery("#step4 .tab-pane-inr").html('<h3>'+param.booking_suc+'</h3>');
																	jQuery(".wizard-actions").remove();
																	}
																}else if(data['status'] == 'error'){
																	jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.book-now" );
																	jQuery("html, body").animate({
																	scrollTop: jQuery(".alert-danger").offset().top
																}, 1000);
																}
															}
														});	
												}else if(woooption == "skippayment"){
													var data = {
													  "action": "freecheckout",
													  "provider": provider_id,
													  "totalcost": totalcost,
													  "bookingdate": date,
													  "bookingpayment_mode": 'skippayment'
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
																		}else{
																		jQuery("#panel-4 .tab-pane-inr").html('<h3>'+param.booking_suc+'</h3>');
																		}
																		
																				
																	}else if(data['status'] == 'error'){
																		jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.book-now" );
																		jQuery("html, body").animate({
																			scrollTop: jQuery(".alert-danger").offset().top
																		}, 1000);
																	}
																	
																}
										
															});	
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
										jQuery( "<div class='alert alert-danger'>"+param.req+"</div>" ).insertAfter( "#addwoobookingcoupon" );	
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
														jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertAfter( "#addwoobookingcoupon" );	
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
							}else{
							
						jQuery("#panel-3 .f-row").addClass('hidden');	
						jQuery("#panel-3 h6").append('<button class="btn btn-border btn-xs edit"><i class="fa fa-pencil"></i>'+param.edit_text+'</button>');
						jQuery("#panel-4 .f-row").removeClass('hidden');
						
						jQuery('.book-now')
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
						});
					
							}
							}else{
							var data = {
							  "action": "freecheckout",
							  "provider": provider_id,
							  "totalcost": totalcost,
							  "bookingdate": date,
							  "bookingpayment_mode": 'skippayment'
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
												jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "#book-now-section" );	
												jQuery("html, body").animate({
													scrollTop: jQuery(".alert-success").offset().top
												}, 1000);
												if(data['redirecturl'] != ''){
												window.location = data['redirecturl'];	
												}else{
												jQuery("#panel-4 .tab-pane-inr").html('<h3>'+param.booking_suc+'</h3>');
												}
												
														
											}else if(data['status'] == 'error'){
												jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "#book-now-section" );
												jQuery("html, body").animate({
													scrollTop: jQuery(".alert-danger").offset().top
												}, 1000);
											}
											
										}
				
									});		
									return false;
							}
							}
						})
		.on('click', '#save-booking', function(form) {
					var $validator = jQuery('.book-now').data('bootstrapValidator').validate();
					if($validator.isValid()){		
					var paymode = jQuery('input[name="bookingpayment_mode"]:checked').val();
					jQuery("#totalcost").val(totalcost);
					if(paymode == 'stripe'){
						var card_number = jQuery('input[name="card_number"]').val();
						var card_cvc = jQuery('input[name="card_cvc"]').val();
						var card_month = jQuery('input[name="card_month"]').val();
						var card_year = jQuery('input[name="card_year"]').val();
						
						if(month_flag==1 || year_flag==1){return false;}
						if(card_number != "" && card_cvc != "" && card_month != "" && card_year != ""){	
						jQuery('.loading-area').show();
						if(stripepublickey != ""){
						Stripe.setPublishableKey(stripepublickey);
									 Stripe.card.createToken({
								  number: jQuery('#card_number').val(),
								  cvc: jQuery('#card_cvc').val(),
								  exp_month: jQuery('#card_month').val(),
								  exp_year: jQuery('#card_year').val()
								}, service_finder_stripeResponseHandler);	
						}else{
							jQuery('.loading-area').hide();
							jQuery('form.book-now').find('input[type="submit"]').prop('disabled', false);
							jQuery( "<div class='alert alert-danger'>"+param.pub_key+"</div>" ).insertBefore( "form.book-now" );
							jQuery("html, body").animate({
									scrollTop: jQuery(".alert-danger").offset().top
								}, 1000);
						}
						
							 
						}

					}else if(paymode == 'paypal' || paymode == 'payumoney'){
											jQuery('.book-now')
											.on('success.form.bv', function(form) {
												return true;
											});
					
					}else if(paymode == 'twocheckout'){
						var twocheckout_card_number = jQuery('input[name="twocheckout_card_number"]').val();
						var twocheckout_card_cvc = jQuery('input[name="twocheckout_card_cvc"]').val();
						var twocheckout_card_month = jQuery('select[name="twocheckout_card_month"]').val();
						var twocheckout_card_year = jQuery('select[name="twocheckout_card_year"]').val();
						if(twocheckout_month_flag==1 || twocheckout_year_flag==1){return false;}
						//jQuery('.loading-area').show();
						if(twocheckoutpublishkey != ""){
						
						try {
                            TCO.loadPubKey(twocheckoutmode);
							jQuery('.loading-area').show();
							tokenRequest();
                        } catch(e) {
							jQuery('.loading-area').hide();
							jQuery(".alert-success,.alert-danger").remove();
							jQuery( "<div class='alert alert-danger'>"+e.toSource()+"</div>" ).insertBefore( "form.book-now" );
							jQuery("html, body").animate({
									scrollTop: jQuery(".alert-danger").offset().top
								}, 1000);
							return false;
                        }
						
						}else{
							jQuery('.loading-area').hide();
							jQuery( "<div class='alert alert-danger'>"+param.pub_key+"</div>" ).insertBefore( "form.book-now" );
							jQuery("html, body").animate({
									scrollTop: jQuery(".alert-danger").offset().top
								}, 1000);
							return false;
						}	 
						return false;					
			
					}else if(paymode == 'payulatam'){
					// Prevent form submission
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
									}else{
									jQuery("#step4 .tab-pane-inr").html('<h3>'+param.booking_suc+'</h3>');
									jQuery(".wizard-actions").remove();
									}
									
											
								}else if(data['status'] == 'error'){
									jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.book-now" );
									jQuery("html, body").animate({
									scrollTop: jQuery(".alert-danger").offset().top
								}, 1000);
								}
								
							}
					});		
						
						 
					}
		
				
					
					}else if(paymode == 'wired' || paymode == 'cod' || paymode == 'skippayment'){
					
											var data = {
											  "action": "freecheckout",
											  "provider": provider_id,
											  "totalcost": totalcost,
											  "bookingdate": date,
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
																}else{
																jQuery("#panel-4 .tab-pane-inr").html('<h3>'+param.booking_suc+'</h3>');
																}
																
																		
															}else if(data['status'] == 'error'){
																jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.book-now" );
																jQuery("html, body").animate({
																	scrollTop: jQuery(".alert-danger").offset().top
																}, 1000);
															}
															
														}
								
													});
					
					}else if(paymode == 'wallet'){
					
						if(charge_admin_fee_from == 'customer')
						{
							var walletchargeamount = parseFloat(totalcost) + parseFloat(adminfee);
						}else{
							var walletchargeamount = parseFloat(totalcost);
						}
						if(parseFloat(walletamount) < parseFloat(walletchargeamount)){
							jQuery( "<div class='alert alert-danger'>"+param.insufficient_wallet_amount+"</div>" ).insertBefore( "form.book-now" );
								jQuery("html, body").animate({
								scrollTop: jQuery(".alert-danger").offset().top
							}, 1000);	
							return false;	
						}
						var data = {
						  "action": "walletcheckout",
						  "provider": provider_id,
						  "totalcost": parseFloat(totalcost),
						  "bookingdate": date,
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
										if(data['redirecturl'] != ''){
										window.location = data['redirecturl'];	
										}else{
										jQuery("#step4 .tab-pane-inr").html('<h3>'+param.booking_suc+'</h3>');
										jQuery(".wizard-actions").remove();
										}
									}else if(data['status'] == 'error'){
										jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.book-now" );
										jQuery("html, body").animate({
										scrollTop: jQuery(".alert-danger").offset().top
									}, 1000);
									}
								}
							});
						
					}
					}else{
						return false;	
					}
				
		})
		.on('success.form.bv', function(form) {
            // Prevent form submission
	        var paymode = jQuery('input[name="bookingpayment_mode"]:checked').val();
			if(paymode == 'stripe' || paymode == 'wired' || paymode == 'twocheckout' || paymode == 'payulatam' || paymode == 'cod' || paymode == 'wallet' || paymode == 'skippayment'){
			form.preventDefault();
			}
		});
     	

	function addto_woo_payment(){

		var data = {
		  "action": "sf_add_to_woo_cart",
		  "wootype": "booking",
		  "provider": provider_id,
		  "totalcost": totalcost,
		  "bookingdate": date
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
					jQuery( "<div class='alert alert-danger'>"+response.error+"</div>" ).insertBefore( "form.book-now" );
					jQuery("html, body").animate({
							scrollTop: jQuery(".alert-danger").offset().top
						}, 1000);
				}
			}
		});  
		return false;	
	}
	
	/*Stripe Handler*/
	function service_finder_stripeResponseHandler(status, response) {
  
			  if (response.error) {
				  // Show the errors on the form
				  jQuery('.loading-area').hide();
				  jQuery( "<div class='alert alert-danger'>"+response.error.message+"</div>" ).insertBefore( "form.book-now" );
				  jQuery("html, body").animate({
										scrollTop: jQuery(".alert-danger").offset().top
									}, 1000);
				
			  } else {
				// response contains id and card, which contains additional card details
				var token = response.id;
				
				/*To Add Service cost also in minimum cost*/
				
				var data = {
						  "action": "checkout",
						  "provider": provider_id,
						  "stripeToken": token,
						  "totalcost": totalcost,
						  "bookingdate": date
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
								}
								
								jQuery("#panel-4").find(".panel-summary").html("<div class='alert alert-success'>"+param.booking_suc+"</div>");
								jQuery("#panel-4 .f-row").hide();
								
								
										
							}else if(data['status'] == 'error'){
								jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.book-now" );
								jQuery("html, body").animate({
										scrollTop: jQuery(".alert-danger").offset().top
									}, 1000);
							}
							
						}

					});
    
 				}
		}
		
	var tokenRequest = function() {
		
		var twocheckout_card_number = jQuery('input[name="twocheckout_card_number"]').val();
		var twocheckout_card_cvc = jQuery('input[name="twocheckout_card_cvc"]').val();
		var twocheckout_card_month = jQuery('select[name="twocheckout_card_month"]').val();
		var twocheckout_card_year = jQuery('select[name="twocheckout_card_year"]').val();
		
		// Setup token request arguments
		var args = {
		  sellerId: twocheckoutaccountid,
		  publishableKey: twocheckoutpublishkey,
		  ccNo: twocheckout_card_number,
		  cvv: twocheckout_card_cvc,
		  expMonth: twocheckout_card_month,
		  expYear: twocheckout_card_year
		};

		// Make the token request
		TCO.requestToken(successCallback, errorCallback, args);
	  };
		
	 // Called when token created successfully.
	  var successCallback = function(data) {
		// Set the token as the value for the token input
		var token = data.response.token.token;
			
			/*To Add Service cost also in minimum cost*/
			
			var data = {
					  "action": "twocheckout",
					  "provider": provider_id,
					  "twocheckouttoken": token,
					  "totalcost": totalcost,
					  "bookingdate": date
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
							jQuery("#step4").find(".panel-summary").html("<div class='alert alert-success'>"+param.booking_suc+"</div>");
							if(data['redirecturl'] != ''){
							window.location = data['redirecturl'];	
							}else{
							jQuery("#panel-4").find(".panel-summary").html("<div class='alert alert-success'>"+param.booking_suc+"</div>");	
							jQuery(".wizard-actions").remove();
							}
							
									
						}else if(data['status'] == 'error'){
							jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "form.book-now" );
							jQuery("html, body").animate({
									scrollTop: jQuery(".alert-danger").offset().top
								}, 1000);
						}
						
					}
	
				});
	
	  };
	
	  // Called when token creation fails.
	  var errorCallback = function(data) {
		if (data.errorCode === 200) {
		  // This error code indicates that the ajax call failed. We recommend that you retry the token request.
		} else {
		  jQuery('.loading-area').hide();
		  jQuery('form.book-now').find('input[type="submit"]').prop('disabled', false);
			  jQuery( "<div class='alert alert-danger'>"+data.errorMsg+"</div>" ).insertBefore( "form.book-now" );
			  jQuery("html, body").animate({
									scrollTop: jQuery(".alert-danger").offset().top
								}, 1000);
		}
	  };	
	  
	  /*Timeslot callback function*/
  function service_finder_timeslotCallback(id, provider_id, totalhours) {
	  	service_finder_resetMembers();
		var date = jQuery("#" + id).data("date");
		jQuery('#selecteddate').attr('data-seldate',date);
		var data = {
			  "action": "get_bookingtimeslot",
			  "seldate": date,
			  "totalhours": totalhours,
			  "provider_id": provider_id,
			};
		var formdata = jQuery.param(data);
		  
		jQuery.ajax({

			type: 'POST',

			url: ajaxurl,

			data: formdata,
			
			beforeSend: function() {
				jQuery('.loading-area').show();
			},

			success:function (data, textStatus) {
				jQuery('.loading-area').hide();
				jQuery('.timeslots').html(data);
				jQuery("#panel-3 h6").remove('button.edit');
				jQuery("#panel-4 h6").remove('button.edit');
			}

		});

		  return true;
	}
	
	/*Member callback function*/
	function service_finder_memberCallback(id, provider_id) {
	  	service_finder_resetMembers();
		var zipcode = jQuery('input[name="zipcode"]').val();
		var region = jQuery('select[name="region"]').val();
		var provider_id = jQuery('#provider').attr('data-provider');
		var date = jQuery("#" + id).data("date");
		region = Encoder.htmlEncode(region);
		var data = {
			  "action": "load_members",
			  "zipcode": zipcode,
			  "region": region,
			  "provider_id": provider_id,
			  "date": date,
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
							jQuery("#panel-2").find(".alert").remove();
							 if(data != null){
								if(data['status'] == 'success'){
									jQuery("#panel-2").find("#members").html(data['members']);
									jQuery("#panel-2").find("#members").append('<div class="col-lg-12"><div class="row"><div class="checkbox text-left"><input id="anymember" class="anymember" type="checkbox" name="anymember[]" value="yes" checked><label for="anymember">'+param.anyone+'</label></div></div></div>');
									jQuery('.display-ratings').rating();
									jQuery('.sf-show-rating').show();
								}
							}
					}

		});	

		  return true;
	}	
	
	/*Reset member function*/
	function service_finder_resetMembers(){
		jQuery("#panel-2").find("#members").html('');
		jQuery("#memberid").val('');	
	}

		
		
  });
  
  