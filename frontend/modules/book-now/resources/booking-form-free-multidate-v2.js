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
	var totalservicecost = 0;
	var scost = '';
	var sid = '';
	var date = '';
	var mysetdate = '';
	var oldzipcode = '';
	var totalhours = 0;
	var daynumarr = [];
	var datearr = [];
	var bookedarr = [];
	var disabledates = [];
	var region_flag = 1;
	var servicearr = '';
	var service_flag = 0;
	var member_flag = 1;
	var serviceslot = '';
	var singleservicehour = 0;
	provider_id = jQuery('#provider').attr('data-provider');
	
	jQuery('body').on('change', '#servicedate-Modal select[name="members_list"]', function(){
		var memberid = jQuery(this).val();
		var avatarurl = jQuery('#servicedate-Modal select[name="members_list"] option:selected').data('avatar');
		if(avatarurl != "" && avatarurl != "undefined" && avatarurl != undefined){
			jQuery("#sf-bookingmember-image").show();	
			jQuery("#sf-bookingmember-image").html('<img src="'+avatarurl+'">');	
		}else{
			jQuery("#sf-bookingmember-image").hide();	
			jQuery("#sf-bookingmember-image").html('');
		}
	});
	
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
	
	function loadservicecalendar(){
		
		jQuery('.dow-clickable').removeClass("selected");
		service_finder_deleteCookie('setselecteddate');
		jQuery('.timeslots').html('');
		
		jQuery("#service-calendar").zabuto_calendar({

						today: true,

						show_previous: false,

						mode : 'add',

						daynum : daynumarr,

						datearr : datearr,

						bookedarr : bookedarr,
						
						show_next: disablemonths,
						
						disabledates : disabledates,

                        action: function () {
							
							jQuery('.alert').remove();
							
							jQuery('.dow-clickable').removeClass("selected");

							jQuery(this).addClass("selected");

							date = jQuery("#" + this.id).data("date");

							service_finder_setCookie('setselecteddate', date); 

							if(jQuery.inArray("availability", caps) > -1 && jQuery.inArray("staff-members", caps) > -1 && staffmember == 'yes'){

								return service_finder_timeslotCallback(this.id, provider_id, totalhours, sid, datearr, daynumarr, bookedarr);

							}else if(jQuery.inArray("availability", caps) > -1 && jQuery.inArray("staff-members", caps) > -1 && (staffmember == 'no' || staffmember == "")){

								return service_finder_timeslotCallback(this.id, provider_id, totalhours, sid, datearr, daynumarr, bookedarr);

							}else if(jQuery.inArray("availability", caps) > -1 && (jQuery.inArray("staff-members", caps) == -1 || (staffmember == 'no' || staffmember == ""))){

								return service_finder_timeslotCallback(this.id, provider_id, totalhours, sid, datearr, daynumarr, bookedarr);

							}else if(jQuery.inArray("availability", caps) == -1 && jQuery.inArray("staff-members", caps) > -1 && staffmember == 'yes'){

								return service_finder_memberCallback(this.id, provider_id);	

							}else if(jQuery.inArray("availability", caps) == -1 && (jQuery.inArray("staff-members", caps) == -1 || (staffmember == 'no' || staffmember == ""))){

								jQuery('#selecteddate').attr('data-seldate',date);

								jQuery('#selecteddate').val(date);

							}

                        },

                    });		

	}

	jQuery('#servicedate-Modal').on('click', 'input[name="nextstepbox"]', function(){
		jQuery(".alert-success,.alert-danger").remove();																				   
		var costtype = jQuery("#serbx-" + sid).data("costtype");
		var providerhours = jQuery("#serbx-" + sid).data('hours');
		var oldproviderhours = jQuery("#serbx-" + sid).attr('data-hours');
		if(costtype == 'hourly' || costtype == 'perperson'){
			singleservicehour = jQuery('#servicedate-Modal input[name="number_of_hours"]').val();	
			if(!oldproviderhours > 0 && (!singleservicehour > 0 || !jQuery.isNumeric(singleservicehour))){
				jQuery( "<div class='alert alert-danger'>"+param.valid_number+"</div>" ).insertBefore( "#memberslist" );
				return false;
			}
			jQuery('#servicedate-Modal input[name="providerhours"]').val(singleservicehour);
			if(singleservicehour > 0){
			jQuery("#serbx-" + sid).data("hours",singleservicehour);
			}
		}else if(!oldproviderhours > 0 && costtype == 'days'){
			var utype = jQuery('input[name="unavl_type"]:checked').val();
			var nod = jQuery('input[name="number_of_days"]').val();
			
			if(utype == "" || (!nod > 0 || !jQuery.isNumeric(nod))){
				jQuery( "<div class='alert alert-danger'>"+param.valid_number+"</div>" ).insertBefore( "#memberslist" );
				return false;	
			}
		}
		
		if(members_available == true){
		var memberid = jQuery('#servicedate-Modal select[name="members_list"]').val();		
		jQuery('#serbx-'+sid).data('memberid',memberid);
		
		//reset_calendar(memberid);
		}
		
		show_hide_stepbox('stepbox1','hide');
		show_hide_stepbox('stepbox2','show');
		
		afternextstep();
																						 
	});
	
	jQuery('#servicedate-Modal').on('click', 'input[name="backstepbox"]', function(){
		
		show_hide_stepbox('stepbox1','show');
		show_hide_stepbox('stepbox2','hide');
		//jQuery("#serbx-" + sid).data("hours",0);
																						 
	});
	
	function show_hide_stepbox(id,visibility){
		if(visibility == 'show'){
			jQuery('#'+id).show();	
			
			if(id == 'stepbox1'){
				jQuery('#servicedate-Modal .add-service-date').hide();
				jQuery('#servicedate-Modal input[name="nextstepbox"]').show();
				jQuery('#servicedate-Modal input[name="backstepbox"]').hide();
			}else if(id == 'stepbox2'){
				jQuery('#servicedate-Modal .add-service-date').show();
				jQuery('#servicedate-Modal input[name="nextstepbox"]').hide();
				jQuery('#servicedate-Modal input[name="backstepbox"]').show();
			}
		}else if(visibility == 'hide'){
			jQuery('#'+id).hide();
			
			if(id == 'stepbox1'){
				jQuery('#servicedate-Modal .add-service-date').show();
				jQuery('#servicedate-Modal input[name="backstepbox"]').show();
				jQuery('#servicedate-Modal input[name="nextstepbox"]').hide();
			}else if(id == 'stepbox2'){
				jQuery('#servicedate-Modal .add-service-date').hide();
				jQuery('#servicedate-Modal input[name="backstepbox"]').hide();
				jQuery('#servicedate-Modal input[name="nextstepbox"]').show();
			}
		}
	}
	
	function afternextstep(){
		jQuery('#servicedate-Modal').modal('show'); 

		jQuery("#servicedate-Modal .servicedate-error-bx").html('');

		loadservicecalendar();

		getservices();
	}
	
	function load_members_list(){
		var zipcode = jQuery('input[name="zipcode"]').val();
		var region = jQuery('select[name="region"]').val();
		var provider_id = jQuery('#provider').data('provider');
	
		var data = {
					  "action": "load_members_list",
					  "zipcode": zipcode,
					  "region": region,
					  "provider_id": provider_id,
					  "sid": sid,
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
							jQuery("#sf-bookingmember-image").hide();
							jQuery("#sf-bookingmember-image").html('');
							jQuery('select[name="members_list"]').html(data['members']);
							jQuery('.sf-select-box').selectpicker('refresh');
						}

					}
			}

		});		
	}

	/*Update booking price according to services selected*/

	jQuery('body').on('click', '#bookingservices .aon-service-bx', function(){
		jQuery(".alert-success,.alert-danger").remove();
		
		var serviceid = jQuery(this).data('id');

		var costtype = jQuery(this).data('costtype');

		var providerhours = jQuery(this).data('hours');
		
		var oldhours = jQuery(this).attr('data-hours');
		
		oldhours = parseFloat(oldhours);

		sid = serviceid;
		show_hide_stepbox('stepbox1','hide');
		show_hide_stepbox('stepbox2','hide');
		

		if(jQuery('#serbx-'+sid).hasClass('selected')) { 

		jQuery('#servicedate-Modal input[name="serviceid"]').val(serviceid);

		jQuery('#servicedate-Modal input[name="costtype"]').val(costtype);
		
		if(members_available == true){
		
			var zipcode = jQuery('input[name="zipcode"]').val();
			var region = jQuery('select[name="region"]').val();
			
			if(booking_basedon == 'zipcode' && zipcode == ""){
				jQuery( "<div class='alert alert-danger'>"+param.insert_zipcode+"</div>" ).insertBefore( "#book-now-section" );
				jQuery(this).removeClass('selected').addClass('unselected');
				jQuery("html, body").animate({
					scrollTop: jQuery(".alert-danger").offset().top
				}, 1000);
				return false;	
			}else if(booking_basedon == 'region' && region == ""){
				jQuery( "<div class='alert alert-danger'>"+param.select_region+"</div>" ).insertBefore( "#book-now-section" );
				jQuery(this).removeClass('selected').addClass('unselected');
				jQuery("html, body").animate({
					scrollTop: jQuery(".alert-danger").offset().top
				}, 1000);
				return false;
			}
			
			jQuery('#memberslist').show();
			jQuery('#numberofdays').hide();
			jQuery('#numberofhours').hide();
		
			load_members_list();	
			show_hide_stepbox('stepbox1','show');
		}

		if(!oldhours > 0 && (costtype == "hourly" || costtype == "perperson")){

		show_hide_stepbox('stepbox1','show');
		jQuery('#numberofdays').hide();
		jQuery('#numberofhours').show();
		
		afternextstep();
		
		}else if(!oldhours > 0 && costtype == "days"){

		show_hide_stepbox('stepbox1','show');
		jQuery('#numberofdays').show();
		jQuery('#numberofhours').hide();
		
		afternextstep();
		
		}else{

		singleservicehour = providerhours;	

		jQuery('#servicedate-Modal input[name="providerhours"]').val(singleservicehour);
		if(singleservicehour > 0){
		jQuery("#serbx-" + sid).data("hours",singleservicehour);
		}

		if(members_available == false){
		show_hide_stepbox('stepbox2','show');
		
		if(oldhours > 0 || costtype == "fixed"){
		jQuery('#servicedate-Modal input[name="backstepbox"]').hide();
		}
		
		jQuery('#numberofhours').hide();
		jQuery('#numberofdays').hide();
		}
		
		afternextstep();

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

			getservices(true);

		}

	});

	jQuery('#servicedate-Modal').on('hide.bs.modal', function() {
		
		var costtype = jQuery('#serbx-'+sid).data('costtype');
		if(costtype == 'days'){
			var datesval = jQuery('#servicedate-Modal input[name="dates"]').val();
			if(datesval == ''){
	
			jQuery('#serbx-'+sid).removeClass('selected');
	
			}
			
		}else{
		if(serviceslot == ''){

		jQuery('#serbx-'+sid).removeClass('selected');

		}
		}

	});

	jQuery('#servicedate-Modal').on('click', '.add-service-date', function(){

		jQuery('.alert').remove();

		var serviceid = jQuery('#servicedate-Modal input[name="serviceid"]').val();

		var costtype = jQuery('#servicedate-Modal input[name="costtype"]').val();

		var providerhours = jQuery('#servicedate-Modal input[name="providerhours"]').val();
		
		if(jQuery("#serbx-" + sid).data("date") == undefined){
			jQuery( "<div class='alert alert-danger'>"+param.booking_dates+"</div>" ).insertBefore( "#loadservicecalendar" );
			return false;	
		}
		
		if(costtype == 'days'){
			
			jQuery('#bookingslot-box').hide();
			jQuery("#servicedate-Modal .servicedate-error-bx").html('');	
			jQuery('#servicedate-Modal').modal('hide');
		}else{
		if(serviceslot == ''){

			jQuery( "<div class='alert alert-danger'>"+param.booking_dates+"</div>" ).insertBefore( "#loadservicecalendar" );

			return false;

		}else{

			jQuery("#servicedate-Modal .servicedate-error-bx").html('');	

			jQuery('#servicedate-Modal').modal('hide'); 

		}
		}

		

		if(jQuery('#serbx-'+sid).hasClass('selected') && (costtype == 'hourly' || costtype == 'perperson' || costtype == 'days')) { 

			if(providerhours > 0){

				//jQuery('#hours-outer-bx-'+serviceid).show();

				//jQuery('#hours-'+serviceid).show();

				jQuery('#hours-'+serviceid).val(providerhours);

				jQuery('#hours-'+serviceid).attr('readonly','readonly');	

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
		
		jQuery('#serbx-'+sid).data('serviceslot',serviceslot);

		getservices(true);

	});
	
	function hasservice()
	{
		service_flag = 0;
		
		jQuery("#bookingservices .aon-service-bx").each( function() {

			if(jQuery(this).hasClass('selected')) {
				service_flag = 1;	
			}

        });
		
		return service_flag;
	}

	/*Save services to a variable*/
	function getservices($param = false){

		servicearr = '';

		service_flag = 0;	

		var servicehours = 0;

		jQuery("#bookingservices .aon-service-bx").each( function() {

			if(jQuery(this).hasClass('selected')) { 

			service_flag = 1;

				var costtype = jQuery(this).data('costtype');

				var serviceid = jQuery(this).data('id');
				
				var memberid = jQuery(this).data('memberid');
				
				var discount = jQuery('#serbx-'+serviceid).attr('data-discount');
				
				var couponcode = jQuery('#serbx-'+serviceid).attr('data-couponcode');

				var hours;

				if(costtype == 'fixed'){

					var hours = 0;
					var date = jQuery(this).data("date");
					var serviceslot = jQuery(this).data("serviceslot");
					
					reset_calendar(memberid);

					servicearr = addto_service_array(servicearr,serviceid,hours,date,serviceslot,memberid,couponcode,discount);

				}else if(costtype == 'hourly' || costtype == 'perperson'){

					var hours = jQuery(this).data('hours');
					var date = jQuery(this).data("date");
					var serviceslot = jQuery(this).data("serviceslot");
					
					reset_calendar(memberid);

					servicearr = addto_service_array(servicearr,serviceid,hours,date,serviceslot,memberid,couponcode,discount);

					servicehours = parseFloat(servicehours) + parseFloat(hours);

				}else if(costtype == 'days'){

					var dates = jQuery(this).data("date");
					
					reset_calendar(memberid,serviceid);

					servicearr = addto_service_array(servicearr,serviceid,'',dates,'',memberid,couponcode,discount);

					servicehours = parseFloat(servicehours) + parseFloat(hours);

				}

			}

        });

		jQuery('#servicearr').val(servicearr);

		date = '';

		serviceslot = '';

		jQuery("#service-calendar").html();

		totalhours = servicehours;

		if($param == true){
			calculate_servicecost();
		}
	}
	
	jQuery('body').on('click', '.addcouponcode', function(){
		jQuery('.alert').remove();
		jQuery('#addcouponcode input[name="couponcode"]').val('');
		
		var sid = jQuery(this).data('sid');														  
		jQuery('#addcouponcode,.sf-couponcode-popup-overlay').fadeIn("slow");
		jQuery('#addcouponcode input[name="couponcode"]').attr('id','couponcode-'+sid);
		jQuery('.verifycoupon').attr('data-sid',sid);
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
	
	function calculate_discount(coupon,discounttype,discountvalue,cost){
		var discount = 0; 
		if(coupon == 'verified'){
					
			if(discounttype == 'percentage'){
				discount = parseFloat(cost) * (parseFloat(discountvalue)/100);
			}else if(discounttype == 'fixed'){
				discount = parseFloat(discountvalue);	
			}
			
		}
		return discount.toFixed(2);
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
					
					servicecost = parseFloat(servicecost) + parseFloat(cost);
					jQuery('#serbx-'+serviceid).attr('data-discount',discount);

				}else if(costtype == 'hourly'){
					var hrmin = 0;
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
				
					}else{
					
					var discount = calculate_discount(coupon,discounttype,discountvalue,cost);	
					cost = calculate_discount_cost(discount,cost);
					servicecost = parseFloat(servicecost) + (parseFloat(cost));	

					}

					jQuery('#serbx-'+serviceid).attr('data-discount',discount);
					servicehours = parseFloat(servicehours) + parseFloat(hrmin);

				}else if(costtype == 'perperson' || costtype == 'days'){
					
					var $hourflag = jQuery(this).data('hours');
					

					if($hourflag > 0){

					var tcost = parseFloat(cost) * parseFloat($hourflag);	
					var discount = calculate_discount(coupon,discounttype,discountvalue,tcost);	
					cost = calculate_discount_cost(discount,tcost);
					servicecost = parseFloat(servicecost) + parseFloat(cost);
				
					}else{

					var discount = calculate_discount(coupon,discounttype,discountvalue,cost);	
					cost = calculate_discount_cost(discount,cost);
					servicecost = parseFloat(servicecost) + (parseFloat(cost));	

					}

					jQuery('#serbx-'+serviceid).attr('data-discount',discount);
					servicehours = parseFloat(servicehours) + parseFloat($hourflag);

				}

			}

			

        });

		//jQuery('#servicearr').val(servicearr);

		totalservicecost = servicecost;

		totalhours = servicehours;

		calculate_totalcost();

	}
	
	function addto_service_array(servicearr,serviceid = '',hours = '',date  = '',serviceslot  = '',memberid  = '',couponcode  = '',discount = 0){
		servicearr = servicearr + serviceid +'||'+ hours +'||'+ date +'||'+ serviceslot + '||'+ memberid + '||'+ discount + '||'+ couponcode + '%%';
		return servicearr;
	}

	function calculate_totalcost(){

		totalcost = parseFloat(mincost) + parseFloat(totalservicecost);

		totalcost = totalcost.toFixed(2);

		jQuery('#bookingamount').html(currencysymbol+totalcost);

		jQuery("#totalcost").val(totalcost);																			

	}

	
	/*Get Time Slots*/

	jQuery('ul.timelist').on('click', 'li', function(){
													 
		jQuery('.alert').remove();															 

		jQuery(this).addClass('active').siblings().removeClass('active');

		service_finder_resetMembers();

		var slot = jQuery(this).attr('data-source');

		serviceslot = jQuery(this).attr('data-source');
		
		var t = jQuery(this).find("span").html();

		jQuery("#boking-slot").attr('data-slot',t);

		jQuery("#boking-slot").val(slot);

		jQuery("#servicedate-Modal .servicedate-error-bx").html('');

	});	

	
	/*Add to favorite*/
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
	/*Remove from favorite*/
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
	
	//reset_calendar();
	function reset_calendar(memberid = '',serviceid = 0){
	provider_id = jQuery('#provider').attr('data-provider');
	
	jQuery("#loadservicecalendar").html('<div id="service-calendar"></div>');

	var data = {

				  "action": "reset_bookingcalendar",

				  "provider_id": provider_id,
				  
				  "member_id": memberid,
				  
				  "serviceid": serviceid

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
					
					jQuery("#loadservicecalendar").html('<div id="service-calendar"></div>');

					jQuery("#service-calendar").zabuto_calendar({

						today: true,

						show_previous: false,

						mode : 'add',

						daynum : daynumarr,

						datearr : datearr,

						bookedarr : bookedarr,
						
						show_next: disablemonths,
						
						disabledates : disabledates,

                        action: function () {
							
							jQuery('.alert').remove();
							
							jQuery('.dow-clickable').removeClass("selected");

							jQuery(this).addClass("selected");

							date = jQuery("#" + this.id).data("date");

							service_finder_setCookie('setselecteddate', date); 

							

							if(jQuery.inArray("availability", caps) > -1 && jQuery.inArray("staff-members", caps) > -1 && staffmember == 'yes'){

								return service_finder_timeslotCallback(this.id, provider_id, totalhours, sid, datearr, daynumarr, bookedarr);

							}else if(jQuery.inArray("availability", caps) > -1 && jQuery.inArray("staff-members", caps) > -1 && (staffmember == 'no' || staffmember == "")){

								return service_finder_timeslotCallback(this.id, provider_id, totalhours, sid, datearr, daynumarr, bookedarr);

							}else if(jQuery.inArray("availability", caps) > -1 && (jQuery.inArray("staff-members", caps) == -1 || (staffmember == 'no' || staffmember == ""))){

								return service_finder_timeslotCallback(this.id, provider_id, totalhours, sid, datearr, daynumarr, bookedarr);

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
	}
	
	/*Adjust Iframe Height*/
	function service_finder_adjustIframeHeight() {
        var $body   = jQuery('body'),
                $iframe = $body.data('iframe.fv');
        if ($iframe) {
            // Adjust the height of iframe
            $iframe.height($body.height());
        }
    }
	
	
	/*Booking Process*/
	 jQuery('.book-now')
        .bootstrapValidator({
          message: param.not_valid,
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'fa fa-refresh fa-spin'
            },
            fields: {
				zipcode: {
					validators: {
						notEmpty: {
								message: param.postal_code
						},
						remote: {
                        type: 'POST',
                        url: datavalidate+'?provider_id='+provider_id,
                        message: param.postcode_not_avl
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
				firstname: {
					validators: {
						notEmpty: {
							message: param.signup_first_name
						}
					}
				},
				lastname: {
					validators: {
						notEmpty: {
							message: param.signup_last_name
						}
					}
				},
				email: {
                validators: {
                    notEmpty: {
														message: param.req
													},
					emailAddress: {
                        message: param.signup_user_email
                    }
					}
				},
				fillotp: {
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
				},
				phone: {
                validators: {
                    notEmpty: {
														message: param.req
													},
                    digits: {message: param.only_digits},
                }
	            },
				address: {
					validators: {
						notEmpty: {
							message: param.signup_address
						}
					}
				},
				city: {
					validators: {
						notEmpty: {
							message: param.city
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
            }
        })
		.on('error.field.bv', function(e, data) {
            data.bv.disableSubmitButtons(false); // disable submit buttons on errors
	    })
		.on('status.field.bv', function(e, data) {
            data.bv.disableSubmitButtons(false); // disable submit buttons on valid
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
																message: param.otp_right,
																callback: function(value, validator, $field) {
																	if(service_finder_getCookie('otppass') == value){
																	return true;
																	}else{
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
																message: 'Please re-confirm the email address',
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
		.bootstrapWizard({
            tabClass: 'nav nav-pills',
            onTabClick: function(tab, navigation, index) {
				return false;
				if(booking_basedon == 'region'){
					if(index == 0){
						if(jQuery('.book-now select[name="region"] option:selected').val()==""){
							region_flag = 1;
							jQuery('.book-now select[name="region"]').parent('div').addClass('has-error').removeClass('has-success'); 
						}else{
							region_flag = 0;
							jQuery('.book-now select[name="region"]').parent('div').removeClass('has-error').addClass('has-success'); 
						}
						if(region_flag==1){return false;}
					}
				}
				jQuery("#step1").find(".alert").remove();
				hasservice();
				if(service_flag == 0 && booking_charge_on_service == 'yes' && (checkjobauthor == 0 && checkquoteauthor == 0 )){
					jQuery("#step1").find('.tab-service-area').append('<div class="col-md-12 clearfix"><div class="alert alert-danger">'+param.select_service+'</div></div>');
					return false;
				}
				if(index == 1 || index == 3){
				
			  	var $validator = jQuery('.book-now').data('bootstrapValidator').validate();
					 if($validator.isValid()){
					jQuery("#step2").find(".alert").remove();
						
						var date = jQuery('#selecteddate').attr('data-seldate');
						if(jQuery.inArray("availability", caps) > -1){
							var getslot = jQuery("#boking-slot").attr("data-slot");
							if(getslot != ""){
								if(member_flag == 1){
								return true;
								}else{
								jQuery("#step2").find('.tab-pane-inr').append('<div class="col-md-12 clearfix"><div class="alert alert-danger">'+param.member_select+'</div></div>');
								return false;	
								}
							}else{
								jQuery("#step2").find('.tab-pane-inr').append('<div class="col-md-12 clearfix"><div class="alert alert-danger">'+param.timeslot+'</div></div>');
								return false;
							}	
						}else{
							return true;	
						}
					 }else{
						return false;	 
					 }
			  
			  }else{
				var $validator = jQuery('.book-now').data('bootstrapValidator').validate();
                                   return $validator.isValid();  
			  }
            },
            onNext: function(tab, navigation, index) {
              var numTabs    = jQuery('#rootwizard').find('.tab-pane').length;
             jQuery("html, body").animate({
				scrollTop: jQuery(".form-wizard").offset().top
			}, 1000);

			if(booking_basedon == 'region'){
				if(index == 1){
					if(jQuery('.book-now select[name="region"] option:selected').val()==""){
						region_flag = 1;
						jQuery('.book-now select[name="region"]').parent('div').addClass('has-error').removeClass('has-success'); 
					}else{
						region_flag = 0;
						jQuery('.book-now select[name="region"]').parent('div').removeClass('has-error').addClass('has-success'); 
					}
					if(region_flag==1){return false;}
				}
			}
			jQuery("#step1").find(".alert").remove();
			hasservice();
			if(service_flag == 0 && booking_charge_on_service == 'yes' && (checkjobauthor == 0 && checkquoteauthor == 0 )){
					jQuery("#step1").find('.tab-service-area').append('<div class="col-md-12 clearfix"><div class="alert alert-danger">'+param.select_service+'</div></div>');
					return false;
				}	
			 if(index  == 2){
					var $validator = jQuery('.book-now').data('bootstrapValidator').validate();
					if($validator.isValid()){		
						if(offersystem == true && offermethod == 'booking'){
								
								bootbox.dialog({
									title: "",
									message: '<div class="viewcoupon-bx">' +
										'<button class="btn btn-primary btn-sm" data-toggle="collapse" data-target="#addwoobookingcoupon"><i class="fa fa-arrow-circle-down"></i> '+param.have_coupon+'</button> ' +
										'<div id="addwoobookingcoupon" class="collapse">' +
										'<input type="text" name="woocouponcode" id="woocouponcode" class="form-control sf-form-control">' +
										'<a href="javascropt:;" class="verifywoobookingcoupon btn btn-custom">'+param.verify+'</a>' +
										'</div> ' +
										'</div> ',
									buttons: {
										success: {
											label: "Continue",
											className: "btn-primary",
											callback: function () {
												goto_freecheckout();
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
								goto_freecheckout();	
							}
					}else{
						return false;	
					}
						
			  }else{
				var $validator = jQuery('.book-now').data('bootstrapValidator').validate();
                                   return $validator.isValid();  
			  }
			  
            },
            onPrevious: function(tab, navigation, index) {
				jQuery("html, body").animate({
				scrollTop: jQuery(".form-wizard").offset().top
			}, 1000);
                return true;
            },
            onTabShow: function(tab, navigation, index) {
                // Update the label of Next button when we are at the last tab
                var numTabs = jQuery('#rootwizard').find('.tab-pane').length;
                jQuery('#rootwizard')
                    .find('.next')
                        .removeClass('disabled')    // Enable the Next button
                        .find('a')
                        .html(index === numTabs - 1 ? param.submit_now : param.next_text+' <i class="fa fa-arrow-right"></i>');

                // You don't need to care about it
                // It is for the specific demo
                service_finder_adjustIframeHeight();
				var $total = navigation.find('li').length;
			var $current = index+1;
			var $percent = ($current/$total) * 100;
			jQuery('#rootwizard').find('.progress-bar').css({width:$percent+'%'});
            }
        });

	function goto_freecheckout(){
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
					jQuery( "<div class='alert alert-success'>"+data['suc_message']+"</div>" ).insertBefore( "#book-now-section" );	
					jQuery("html, body").animate({
						scrollTop: jQuery(".alert-success").offset().top
					}, 1000);
					if(data['redirecturl'] != ''){
					window.location = data['redirecturl'];	
					}else{
					jQuery("#panel-3 .tab-pane-inr").html('<h3>'+param.booking_suc+'</h3>');
					}
					
							
				}else if(data['status'] == 'error'){
					jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "#book-now-section" );
					jQuery("html, body").animate({
						scrollTop: jQuery(".alert-danger").offset().top
					}, 1000);
				}
				
			}

		});		  
  }
  
  function service_finder_get_bookingdays(id,unavl_type,numberofdays,date,paramsid,datearr = '',daynumarr = '',bookedarr = '') {
	   
	  var data = {
			  "action": "get_bookingdays",
			  "unavl_type": unavl_type,
			  "numberofdays": numberofdays,
			  "startdate": date,
			  "datearr": datearr,
			  "daynumarr": daynumarr,
			  "bookedarr": bookedarr,
			};
		var formdata = jQuery.param(data);
		  
		jQuery.ajax({

			type: 'POST',

			url: ajaxurl,

			data: formdata,
			
			dataType: "json",
			
			beforeSend: function() {
				jQuery('.loading-area').show();
				jQuery(".alert-success,.alert-danger").remove();
			},

			success:function (data, textStatus) {
				jQuery('.dow-clickable').removeClass("selected");
				if(data['status'] == 'success'){
				singleservicehour = data['bookingdates'].length;
				
				jQuery('#servicedate-Modal input[name="providerhours"]').val(singleservicehour);
				if(singleservicehour > 0){
				jQuery("#serbx-" + paramsid).data("hours",singleservicehour);
				}
				var dates = '';
				
				for (var key in data['bookingdates']) {
				if (data['bookingdates'].hasOwnProperty(key)) {
					dates = dates + data['bookingdates'][key] + '##';
					var sdate = id.replace(date, data['bookingdates'][key]);
					jQuery("#" + sdate).addClass("selected");
				}
				}
				jQuery('#servicedate-Modal input[name="dates"]').val(dates);
				jQuery("#serbx-" + paramsid).data("date",dates);
				jQuery('.loading-area').hide();	
				}else if(data['status'] == 'error'){
					jQuery('.loading-area').hide();	
					jQuery( "<div class='alert alert-danger'>"+data['err_message']+"</div>" ).insertBefore( "#loadservicecalendar" );
				}
				
			}

		}); 
   }

  /*Timeslot callback function*/
  function service_finder_timeslotCallback(id, provider_id, totalhours, paramsid = '', datearr = '', daynumarr = '', bookedarr = '') {

	  	service_finder_resetMembers();

		var date = jQuery("#" + id).data("date");
		
		var unavl_type = jQuery('input[name="unavl_type"]:checked').val();
		var numberofdays = jQuery('input[name="number_of_days"]').val();
		
		var costtype = jQuery("#serbx-" + paramsid).data("costtype");
		var days = jQuery("#serbx-" + paramsid).data("hours");
		var cost = jQuery("#serbx-" + paramsid).data("cost");
		var memberid = jQuery("#serbx-" + paramsid).data("memberid");

		if(costtype == 'days'){
		if(days > 0){
			service_finder_get_bookingdays(id,'days',days,date,paramsid,datearr,daynumarr,bookedarr);
		}else{
			service_finder_get_bookingdays(id,unavl_type,numberofdays,date,paramsid,datearr,daynumarr,bookedarr);
		}
		
		}else{
		jQuery('#selecteddate').attr('data-seldate',date);
		jQuery("#serbx-" + paramsid).data("date",date);
		
		var data = {

			  "action": "get_bookingtimeslot",

			  "seldate": date,

			  "provider_id": provider_id,
			  
			  "member_id": memberid,

			  "totalhours": days,
			  
			  "serviceid": paramsid,

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

		}
		return true;

	}
  
  /*Callback fucntion to get members*/
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
					jQuery("#step2").find(".alert").remove();
					 if(data != null){
						if(data['status'] == 'success'){
							jQuery("#step2").find("#members").html(data['members']);
							jQuery("#step2").find("#members").append('<div class="col-lg-12"><div class="row"><div class="checkbox text-left"><input id="anymember" class="anymember" type="checkbox" name="anymember[]" value="yes" checked><label for="anymember">'+param.anyone+'</label></div></div></div>');
							jQuery('.display-ratings').rating();
							jQuery('.sf-show-rating').show();
						}
					}
			}

		});	

		  return true;
	}	
	/*Reset Members*/
	function service_finder_resetMembers(){
		jQuery("#step2").find("#members").html('');
		jQuery("#memberid").val('');	
	}

	
  });
  
  