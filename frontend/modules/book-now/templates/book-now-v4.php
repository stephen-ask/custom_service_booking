<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<?php
$wpdb = service_finder_plugin_global_vars('wpdb');
$service_finder_Params = service_finder_plugin_global_vars('service_finder_Params');

wp_enqueue_script('service_finder-js-booking-form-v4');

$radiussearchunit = (isset($service_finder_options['radius-search-unit'])) ? esc_attr($service_finder_options['radius-search-unit']) : 'mi';

$jsdata = 'var jsdata = { 
			"radiussearchunit": "'.$radiussearchunit.'",
			"stripepublickey": "'.service_finder_get_stripe_public_key().'"
			};';
wp_add_inline_script('service_finder-js-booking-form-v4', $jsdata, 'before');

$payment_methods = (!empty($service_finder_options['payment-methods'])) ? $service_finder_options['payment-methods'] : '';
$show_booking_otp = (!empty($service_finder_options['show-booking-otp'])) ? $service_finder_options['show-booking-otp'] : '';
$paid_booking = (!empty($service_finder_options['paid-booking'])) ? $service_finder_options['paid-booking'] : '';

/*Include Book Now Class*/
$userInfo = service_finder_getCurrentUserInfo();
$userCap = service_finder_get_capability($author);
$settings = service_finder_getProviderSettings($author);

$future_bookings_availability = (!empty($settings['future_bookings_availability'])) ? $settings['future_bookings_availability'] : 365;

$number_of_months = (intval($future_bookings_availability)/30);

if($number_of_months < 1){
$lastdate = date('n', strtotime("+".$future_bookings_availability." days", time()));
$currentmonth = date('n');
$number_of_months = intval($lastdate) - intval($currentmonth);
}

$capability = '';
if(!empty($userCap)){
foreach($userCap as $cap){
$capability .= '"'.$cap.'",';
}
}
$capability = rtrim($capability,',');

$jobid = (!empty($_GET['jobid'])) ? $_GET['jobid']  : '';
$quoteid = (!empty($_GET['quoteid'])) ? esc_attr($_GET['quoteid'])  : '';
if($jobid != ""){
$jobpost = get_post($jobid);
if(!empty($jobpost)){
$jobauthor = $jobpost->post_author;
}
}

$quoteauthor = '';
$quoteprice = 0;

if($quoteid != ""){
$quoteauthor = service_finder_get_quote_author($quoteid);
$quoteprice = service_finder_get_quote_price($quoteid,$author);
}


$bookingcost = get_post_meta($jobid,'_job_cost',true);
if(is_user_logged_in() && service_finder_getUserRole($current_user->ID) == 'Customer' && $jobid > 0 && $jobauthor == $current_user->ID){
$quoteprice = service_finder_get_job_quote_price($author,$jobid);
if($quoteprice > 0){
$bookingcost = $quoteprice;
}else{
$bookingcost = get_post_meta($jobid,'_job_cost',true);
$bookingcost = ($bookingcost > 0) ? $bookingcost : 0;
}
}elseif(is_user_logged_in() && service_finder_getUserRole($current_user->ID) == 'Customer' && $quoteid > 0 && $quoteauthor == $current_user->ID && $quoteprice > 0){
$bookingcost = ($quoteprice > 0) ? $quoteprice : 0;
}else{
$bookingcost = ($settings['mincost'] > 0) ? $settings['mincost'] : 0;
}

$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? esc_html($service_finder_options['pay_booking_amount_to']) : '';
if($pay_booking_amount_to == 'admin'){
	$stripetype = (!empty($service_finder_options['stripe-type'])) ? esc_html($service_finder_options['stripe-type']) : '';
	if($stripetype == 'live'){
		$stripepublickey = (!empty($service_finder_options['stripe-live-public-key'])) ? esc_html($service_finder_options['stripe-live-public-key']) : '';
	}else{
		$stripepublickey = (!empty($service_finder_options['stripe-test-public-key'])) ? esc_html($service_finder_options['stripe-test-public-key']) : '';
	}
}elseif($pay_booking_amount_to == 'provider'){
	$stripepublickey = esc_html($settings['stripepublickey']);
}

$twocheckouttype = (!empty($service_finder_options['twocheckout-type'])) ? esc_html($service_finder_options['twocheckout-type']) : '';
if($twocheckouttype == 'live'){
	$twocheckoutmode = 'production';
}else{
	$twocheckoutmode = 'sandbox';
}
if($pay_booking_amount_to == 'admin'){
	if($twocheckouttype == 'live'){
		$twocheckoutpublishkey = (!empty($service_finder_options['twocheckout-live-publish-key'])) ? esc_html($service_finder_options['twocheckout-live-publish-key']) : '';
		$twocheckoutaccountid = (!empty($service_finder_options['twocheckout-live-account-id'])) ? esc_html($service_finder_options['twocheckout-live-account-id']) : '';
	}else{
		$twocheckoutpublishkey = (!empty($service_finder_options['twocheckout-test-publish-key'])) ? esc_html($service_finder_options['twocheckout-test-publish-key']) : '';
		$twocheckoutaccountid = (!empty($service_finder_options['twocheckout-test-account-id'])) ? esc_html($service_finder_options['twocheckout-test-account-id']) : '';
	}
}elseif($pay_booking_amount_to == 'provider'){
	$twocheckoutpublishkey = esc_html($settings['twocheckoutpublishkey']);
	$twocheckoutaccountid = esc_html($settings['twocheckoutaccountid']);
}

$skip_service = 0;

if(service_finder_is_job_author($jobid,$jobauthor)){
$checkjobauthor = 1;
$skip_service = 1;
}else{
$checkjobauthor = 0;
}

if(service_finder_is_quotation_author($quoteid,$quoteauthor)){
$checkquoteauthor = 1;
$skip_service = 1;
}else{
$checkquoteauthor = 0;
}


$admin_fee_type = (!empty($service_finder_options['admin-fee-type'])) ? $service_finder_options['admin-fee-type'] : 0;
$admin_fee_percentage = (!empty($service_finder_options['admin-fee-percentage'])) ? $service_finder_options['admin-fee-percentage'] : 0;
$admin_fee_fixed = (!empty($service_finder_options['admin-fee-fixed'])) ? $service_finder_options['admin-fee-fixed'] : 0;

$admin_fee_label = (!empty($service_finder_options['admin-fee-label'])) ? $service_finder_options['admin-fee-label'] : esc_html__('Admin Fee', 'service-finder');
$charge_admin_fee = (!empty($service_finder_options['charge-admin-fee'])) ? $service_finder_options['charge-admin-fee'] : '';
$charge_admin_fee_from = (!empty($service_finder_options['charge-admin-fee-from'])) ? $service_finder_options['charge-admin-fee-from'] : '';

if($charge_admin_fee && $pay_booking_amount_to == 'admin' && (($admin_fee_type == 'fixed' && $admin_fee_fixed > 0) || ($admin_fee_type == 'percentage' && $admin_fee_percentage > 0)) && $charge_admin_fee_from == 'customer'){
$showadminfee = '<li>'.esc_html__('Booking Amount', 'service-finder').': <strong><span id="bookingfee"></span></strong> </li>';
$showadminfee .= '<li>'.sprintf( esc_html__('%s', 'service-finder'), $admin_fee_label ).': <strong><span id="bookingadminfee"></span></strong> </li>';
$showadminfee .= '<li>'.esc_html__('Total Amount', 'service-finder').': <strong><span id="totalbookingfee"></span></strong> </li>';
$showadminfee = '<ul class="sf-adminfee-bx">'.$showadminfee.'</ul>';
}else{
$totalamount = $bookingcost;
$showadminfee = '';

$showadminfee = '<li>'.esc_html__('Booking Amount', 'service-finder').': <strong><span id="bookingfee"></span></strong> </li>';
$showadminfee = '<ul class="sf-adminfee-bx">'.$showadminfee.'</ul>';

}

$members_available = false;
if(!empty($userCap)){
if(in_array('staff-members',$userCap) && in_array('bookings',$userCap)){
if($settings['members_available'] == 'yes'){
$members_available = true;
}
}
}

$walletamount = service_finder_get_wallet_amount($current_user->ID);
$offersystem = service_finder_check_offer_system();
$walletsystem = service_finder_check_wallet_system();
$offermethod = service_finder_offers_method($author);

if(service_finder_getUserRole($current_user->ID) == 'Provider' || service_finder_getUserRole($current_user->ID) == 'administrator'){
$skipoption = true;
}else{
$skipoption = false;
}

if(service_finder_booking_date_method($author) == 'multidate'){
wp_add_inline_script( 'service_finder-js-booking-form-v4', '/*Declare global variable*/
var staffmember = "'.$settings['members_available'].'";
var skipoption = "'.$skipoption.'";
var jobid = "'.$jobid.'";
var charge_admin_fee_from = "'.$charge_admin_fee_from.'";
var disablemonths = '.$number_of_months.';
var quoteid = "'.$quoteid.'";
var walletamount = "'.$walletamount.'";
var walletamountwithcurrency = "'.service_finder_money_format($walletamount).'";
var walletsystem = "'.$walletsystem.'";
var offersystem = "'.$offersystem.'";
var offermethod = "'.$offermethod.'";
var members_available = "'.$members_available.'";
var skip_service = "'.$skip_service.'";
var checkjobauthor = "'.$checkjobauthor.'";
var checkquoteauthor = "'.$checkquoteauthor.'";
var totalcost = "'.$bookingcost.'";
var adminfeetype = "'.$admin_fee_type.'";
var adminfeefixed = "'.$admin_fee_fixed.'";
var adminfeepercentage = "'.$admin_fee_percentage.'";
var mincost = "'.$bookingcost.'";
var booking_basedon = "'.$settings['booking_basedon'].'";
var stripepublickey = "'.$stripepublickey.'";
var twocheckoutaccountid = "'.$twocheckoutaccountid.'";
var twocheckoutpublishkey = "'.$twocheckoutpublishkey.'";
var twocheckouttype = "'.$twocheckouttype.'";
var twocheckoutmode = "'.$twocheckoutmode.'";
var booking_charge_on_service = "yes";
var caps = ['.$capability.'];', 'after' );
}else{
wp_add_inline_script( 'service_finder-js-booking-form-v4', '/*Declare global variable*/
var staffmember = "'.$settings['members_available'].'";
var skipoption = "'.$skipoption.'";
var jobid = "'.$jobid.'";
var charge_admin_fee_from = "'.$charge_admin_fee_from.'";
var disablemonths = '.$number_of_months.';
var quoteid = "'.$quoteid.'";
var walletamount = "'.$walletamount.'";
var walletamountwithcurrency = "'.service_finder_money_format($walletamount).'";
var walletsystem = "'.$walletsystem.'";
var offersystem = "'.$offersystem.'";
var offermethod = "'.$offermethod.'";
var members_available = "'.$members_available.'";
var skip_service = "'.$skip_service.'";
var checkjobauthor = "'.$checkjobauthor.'";
var checkquoteauthor = "'.$checkquoteauthor.'";
var totalcost = "'.$bookingcost.'";
var adminfeetype = "'.$admin_fee_type.'";
var adminfeefixed = "'.$admin_fee_fixed.'";
var adminfeepercentage = "'.$admin_fee_percentage.'";
var mincost = "'.$bookingcost.'";
var booking_basedon = "'.$settings['booking_basedon'].'";
var stripepublickey = "'.$stripepublickey.'";
var twocheckoutaccountid = "'.$twocheckoutaccountid.'";
var twocheckoutpublishkey = "'.$twocheckoutpublishkey.'";
var twocheckouttype = "'.$twocheckouttype.'";
var twocheckoutmode = "'.$twocheckoutmode.'";
var booking_charge_on_service = "yes";
var caps = ['.$capability.'];', 'after' );
}

wp_add_inline_script( 'google-map', 'jQuery(function() {

/*Autofill address script by google 3rd step*/
function service_finder_initBookingAddressAutoComplete(){
	
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

if (jQuery("#booking-address").length && siteautosuggestion == true){	
google.maps.event.addDomListener(window, "load", service_finder_initBookingAddressAutoComplete);
}
});', 'after' );
?>

<form class="book-now" method="post">
<input type="hidden" id="provider" name="provider" value="" />
<input type="hidden" id="jobid" name="jobid" value="" />
<input type="hidden" id="quoteid" name="quoteid" value="" />
<input type="hidden" id="boking-slot" data-slot="" name="boking-slot" value="" />
<input type="hidden" id="memberid" data-memid="" name="memberid" value="" />
<input type="hidden" id="totalcost" name="totalcost" value="" />
<input type="hidden" id="totaldiscount" name="totaldiscount" value="" />
<input type="hidden" id="servicearr" name="servicearr" value="" />
<input type="hidden" id="selecteddate" data-seldate="" name="selecteddate" />
	
  <ul class="sf-provi-service-list" id="bookingservices">
    <?php
	$providerid = $author;
	$walletamount = service_finder_get_wallet_amount($current_user->ID);
	$walletsystem = service_finder_check_wallet_system();
	
	$settings = service_finder_getProviderSettings($providerid);
	
	$mincost = ($settings['mincost'] > 0) ? $settings['mincost'] : 0;
	
	if(service_finder_getUserRole($current_user->ID) == 'Provider' || service_finder_getUserRole($current_user->ID) == 'administrator'){
	$skipoption = true;
	}else{
	$skipoption = false;
	}
	
	$paymentoptions = '';
	$payflag = 0;
	$stripepublickey = '';

	if(service_finder_get_payment_goes_to() == 'provider')
	{
		ob_start();
		$stripepublickey = $settings['stripepublickey'];
		if(!empty($settings['paymentoption']))
		{
			foreach($settings['paymentoption'] as $paymentoption)
			{
			$payflag = 1;
			?>
			<div class="radio sf-radio-checkbox">
			  <input type="radio" value="<?php echo esc_attr($paymentoption); ?>" name="bookingpayment_mode" id="paymentvia<?php echo esc_attr($paymentoption); ?>" >
			  <label for="paymentvia<?php echo esc_attr($paymentoption); ?>"><?php echo '<img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/'.$paymentoption.'.jpg" title="'.esc_attr(ucfirst($paymentoption)).'" alt="'.esc_attr(ucfirst($paymentoption)).'">'; ?></label>
			</div>
			<?php
			}
		}elseif(!service_finder_check_wallet_system()){
			$payflag = 0;
			echo '<p>';
			echo esc_html__('There is no payment method available.','service-finder');
			echo '</p>';
		}
		
		echo service_finder_add_wallet_option('bookingpayment_mode','paymentvia');
		echo service_finder_add_skip_option('bookingpayment_mode','paymentvia');
		
		$paymentoptions = ob_get_clean();
	}
	
	if(in_array('staff-members',$userCap) && service_finder_get_data($settings,'members_available') == 'yes' && in_array('availability',$userCap)){
		$loadmembers = 'yes';
	}else{
		$loadmembers = 'no';
	}
	
	$bookingdata = array(
		'skipoption' 	=> $skipoption,
		'providerid' 	=> $providerid,
		'walletamount' 	=> $walletamount,
		'walletamountwithcurrency' 	=> service_finder_money_format($walletamount),
		'walletsystem' 	=> $walletsystem,
		'adminfeetype' 	=> service_finder_get_data($service_finder_options,'admin-fee-type'),
		'adminfeefixed' 	=> service_finder_get_data($service_finder_options,'admin-fee-fixed'),
		'adminfeepercentage' 	=> service_finder_get_data($service_finder_options,'admin-fee-percentage'),
		'pay_booking_amount_to' 	=> service_finder_get_payment_goes_to(),
		'paymentoptions' 	=> $paymentoptions,
		'paymentoptionsavl' 	=> $payflag,
		'stripepublickey' 	=> $stripepublickey,
		'mincost' 	=> $mincost,
		'booking_charge_on_service' 	=> 'yes',
		'is_booking_free_paid' 	=> service_finder_is_booking_free_paid($providerid),
		'loadmembers' 	=> $loadmembers
	);
	
	$numberofservices = 2;
    $allservices = service_finder_getAllServices($author);
	$services = service_finder_getServices($author,'active');
	$totalservices = count($allservices);
	if(!empty($allservices)){
	$servicecnt = 1;
	foreach($services as $service){
	
	if(service_finder_booking_date_method($author) != 'multidate' && $service->cost_type == 'days'){
		continue;
	}
	
	if($service->cost_type == 'hourly'){ 
		$hours = $service->hours;
	}elseif($service->cost_type == 'perperson'){
		$hours = $service->persons;
	}elseif($service->cost_type == 'days'){
		$hours = $service->days;
	}else{
		$hours = 0;
	}
	
	if(service_finder_booking_date_method($author) == 'multidate'){
	
	if(($service->cost_type == 'hourly' && $service->hours == 0) || ($service->cost_type == 'perperson' && $service->persons == 0)){
		$hoursfield = 'yes';
	}else{
		$hoursfield = 'no';
	}
	$servicedata = array(
		'providerid' 	=> $author,
		'serviceid' 	=> $service->id,
		'servicename' 	=> $service->service_name,
		'hours' 	=> $hours,
		'hoursfield' 	=> $hoursfield,
		'date' 	=> '',
		'slot' 	=> '',
		'cost' 	=> $service->cost,
		'costtype' 	=> $service->cost_type,
		'memberid' 	=> 0,
		'discount' 	=> '',
		'discounttype' 	=> '',
		'discountvalue' 	=> '',
		'coupon' 	=> '',
		'couponcode' 	=> ''
	);
	}else{
	$servicedata = array(
		'providerid' 	=> $author,
		'serviceid' 	=> $service->id,
		'servicename' 	=> $service->service_name,
		'hours' 	=> $hours,
		'cost' 	=> $service->cost,
		'costtype' 	=> $service->cost_type,
		'discounttype' 	=> '',
		'discountvalue' 	=> '',
		'coupon' 	=> '',
		'couponcode' 	=> ''
	);
	}
	
	//$servicedisplay = ($servicecnt > $numberofservices) ? 'none' : 'block';
	?>
	<li class="sf-provi-service-box servicebox" id="serviceid-<?php echo esc_attr($service->id); ?>">
      <div class="sf-provi-service-top">
        <div class="sf-provi-service-left">
          <h4 class="sf-provi-service-ttle">
            <buttom class="sf-provi-toggle-btn">+</buttom>
            <?php echo esc_html($service->service_name); ?> 
            <?php if($service->offer == 'yes' && service_finder_offers_method($author) == 'services'){ ?>
            <span  class="sf-service-offer-label sf-booking-payment-info" data-toggle="popover" data-container="body" data-placement="top" type="button" data-html="true" id="offers-<?php echo $service->id; ?>" data-trigger="hover">
            <?php echo esc_html__('offer', 'service-finder'); ?>
            </span>
            <div id="popover-content-offers-<?php echo $service->id; ?>" class="hide">
                    <ul class="list-unstyled margin-0 booking-payment-data">
                        <li><strong><?php echo esc_html($service->offer_title); ?></strong></li>
                        <li><?php printf($service->discount_description); ?></li>
                    </ul>
                </div>
            <?php } ?>
          </h4>
          <div class="sf-provi-service-price"><?php echo service_finder_money_format($service->cost); ?>
          <?php if($service->cost_type == 'hourly'){ 
			  echo esc_html__('/hour', 'service-finder');
		  }elseif($service->cost_type == 'perperson'){
		  	echo esc_html__('/item', 'service-finder');
		  }elseif($service->cost_type == 'days'){
		  	echo esc_html__('/day', 'service-finder');
		  }
		  ?>
          </div>
          <?php if($service->cost_type == 'hourly' && $service->hours > 0){ ?>
          <div class="sf-provi-service-hour"><i class="fa fa-clock-o"></i><?php echo esc_html__('Hours', 'service-finder'); ?>: <?php echo esc_html($service->hours); ?></div>
          <?php }elseif($service->cost_type == 'perperson' && $service->persons){ ?>
          <div class="sf-provi-service-hour"><i class="fa fa-user"></i><?php echo esc_html__('Item', 'service-finder'); ?>: <?php echo esc_html($service->persons); ?></div>
          <?php }elseif($service->cost_type == 'days' && $service->days){ ?>
          <div class="sf-provi-service-hour"><i class="fa fa-calendar"></i><?php echo esc_html__('Days', 'service-finder'); ?>: <?php echo esc_html($service->days); ?></div>
		  <?php } ?>
          <?php echo service_finder_have_coupon_code_button($service->id,$author) ?>
        </div>
        <div class="sf-provi-service-right">
       	  <div class="sf-provi-service-count">
          	<?php if(service_finder_booking_date_method($author) != 'multidate'){ ?>
			<?php if(($service->cost_type == 'hourly' && $service->hours == 0) || ($service->cost_type == 'perperson' && $service->persons == 0)){ ?>
            
            <?php
            if($service->cost_type == 'perperson'){
				$str = 'item';
				$step = 1;
				$maxlimit = 500;
			}else{
				$str = 'hr';
				$step = 0.5;
				$maxlimit = 12;
			}
			?>
            <input id="hours-<?php echo esc_attr($service->id); ?>" data-postfix="<?php echo esc_attr($str); ?>" data-step="<?php echo esc_attr($step); ?>" data-max="<?php echo esc_attr($maxlimit); ?>" data-serviceid="<?php echo esc_attr($service->id); ?>" class="form-control servicehourspin" type="text" name="hours[]" value="<?php echo esc_attr($hours); ?>">
            <?php }else{
			?>
			<input id="hours-<?php echo esc_attr($service->id); ?>" data-serviceid="<?php echo esc_attr($service->id); ?>" class="form-control" type="hidden" name="hours[]" value="<?php echo esc_attr($hours); ?>">
			<?php } ?>
            <?php } ?>
          </div>
          <button type="button" class="btn btn-primary btn-schedules addthisservice" id="servicebtn-<?php echo esc_attr($service->id); ?>" data-bookingdata="<?php echo esc_attr(wp_json_encode( $bookingdata )) ?>" data-servicedata="<?php echo esc_attr(wp_json_encode( $servicedata )) ?>"><?php echo esc_html__('Select Service', 'service-finder'); ?></button>
        </div>
      </div>
      <div class="sf-provi-service-bottom">
        <div class="sf-provi-descriptio"><?php echo esc_html($service->description); ?></div>
      </div>
    </li>
	<?php
	$servicecnt++;
	}
	
	$groups = service_finder_getServiceGroups($author);
	if(!empty($groups)){
		foreach($groups as $group){
			$services = service_finder_getServices($author,'active',$group->id);
			if(!empty($services)){
			$servicecnt = 1;
			echo '<li class="sf-grp-title">'.$group->group_name.'</li>';
			foreach($services as $service){
			
			if(service_finder_booking_date_method($author) != 'multidate' && $service->cost_type == 'days'){
				continue;
			}
			
			if($service->cost_type == 'hourly'){ 
				$hours = $service->hours;
			}elseif($service->cost_type == 'perperson'){
				$hours = $service->persons;
			}elseif($service->cost_type == 'days'){
				$hours = $service->days;
			}else{
				$hours = 0;
			}
			
			if(service_finder_booking_date_method($author) == 'multidate'){
			
			if(($service->cost_type == 'hourly' && $service->hours == 0) || ($service->cost_type == 'perperson' && $service->persons == 0)){
				$hoursfield = 'yes';
			}else{
				$hoursfield = 'no';
			}
			$servicedata = array(
				'providerid' 	=> $author,
				'serviceid' 	=> $service->id,
				'servicename' 	=> $service->service_name,
				'hours' 	=> $hours,
				'hoursfield' 	=> $hoursfield,
				'date' 	=> '',
				'slot' 	=> '',
				'cost' 	=> $service->cost,
				'costtype' 	=> $service->cost_type,
				'memberid' 	=> 0,
				'discount' 	=> '',
				'discounttype' 	=> '',
				'discountvalue' 	=> '',
				'coupon' 	=> '',
				'couponcode' 	=> ''
			);
			}else{
			$servicedata = array(
				'providerid' 	=> $author,
				'serviceid' 	=> $service->id,
				'servicename' 	=> $service->service_name,
				'hours' 	=> $hours,
				'cost' 	=> $service->cost,
				'costtype' 	=> $service->cost_type,
				'discounttype' 	=> '',
				'discountvalue' 	=> '',
				'coupon' 	=> '',
				'couponcode' 	=> ''
			);
			}
			
			//$servicedisplay = ($servicecnt > $numberofservices) ? 'none' : 'block';
			?>
			<li class="sf-provi-service-box servicebox" id="serviceid-<?php echo esc_attr($service->id); ?>">
			  <div class="sf-provi-service-top">
				<div class="sf-provi-service-left">
				  <h4 class="sf-provi-service-ttle">
					<buttom class="sf-provi-toggle-btn">+</buttom>
					<?php echo esc_html($service->service_name); ?> 
                     <?php if($service->offer == 'yes' && service_finder_offers_method($author) == 'services'){ ?>
                    <span  class="sf-service-offer-label sf-booking-payment-info" data-toggle="popover" data-container="body" data-placement="top" type="button" data-html="true" id="offers-<?php echo $service->id; ?>" data-trigger="hover">
                    <?php echo esc_html__('offer', 'service-finder'); ?>
                    </span>
                    <div id="popover-content-offers-<?php echo $service->id; ?>" class="hide">
                            <ul class="list-unstyled margin-0 booking-payment-data">
                                <li><strong><?php echo esc_html($service->offer_title); ?></strong></li>
                                <li><?php printf($service->discount_description); ?></li>
                            </ul>
                        </div>
                    <?php } ?>
                  </h4>
				  <div class="sf-provi-service-price"><?php echo service_finder_money_format($service->cost); ?>
				  <?php if($service->cost_type == 'hourly'){ 
					  echo esc_html__('/hour', 'service-finder');
				  }elseif($service->cost_type == 'perperson'){
					echo esc_html__('/item', 'service-finder');
				  }elseif($service->cost_type == 'days'){
					echo esc_html__('/day', 'service-finder');
				  }
				  ?>
				  </div>
				  <?php if($service->cost_type == 'hourly' && $service->hours > 0){ ?>
				  <div class="sf-provi-service-hour"><i class="fa fa-clock-o"></i><?php echo esc_html__('Hours', 'service-finder'); ?>: <?php echo esc_html($service->hours); ?></div>
				  <?php }elseif($service->cost_type == 'perperson' && $service->persons){ ?>
				  <div class="sf-provi-service-hour"><i class="fa fa-user"></i><?php echo esc_html__('Item', 'service-finder'); ?>: <?php echo esc_html($service->persons); ?></div>
				  <?php }elseif($service->cost_type == 'days' && $service->days){ ?>
				  <div class="sf-provi-service-hour"><i class="fa fa-calendar"></i><?php echo esc_html__('Days', 'service-finder'); ?>: <?php echo esc_html($service->days); ?></div>
				  <?php } ?>
				  <?php echo service_finder_have_coupon_code_button($service->id,$author) ?>
				</div>
				<div class="sf-provi-service-right">
				  <div class="sf-provi-service-count">
					<?php if(service_finder_booking_date_method($author) != 'multidate'){ ?>
					<?php if(($service->cost_type == 'hourly' && $service->hours == 0) || ($service->cost_type == 'perperson' && $service->persons == 0)){ ?>
					
					<?php
					if($service->cost_type == 'perperson'){
						$str = 'item';
						$step = 1;
						$maxlimit = 500;
					}else{
						$str = 'hr';
						$step = 0.5;
						$maxlimit = 12;
					}
					?>
					<input id="hours-<?php echo esc_attr($service->id); ?>" data-postfix="<?php echo esc_attr($str); ?>" data-step="<?php echo esc_attr($step); ?>" data-max="<?php echo esc_attr($maxlimit); ?>" data-serviceid="<?php echo esc_attr($service->id); ?>" class="form-control servicehourspin" type="text" name="hours[]" value="<?php echo esc_attr($hours); ?>">
					<?php }else{
					?>
					<input id="hours-<?php echo esc_attr($service->id); ?>" data-serviceid="<?php echo esc_attr($service->id); ?>" class="form-control" type="hidden" name="hours[]" value="<?php echo esc_attr($hours); ?>">
					<?php } ?>
					<?php } ?>
				  </div>
				  <button type="button" class="btn btn-primary btn-schedules addthisservice" id="servicebtn-<?php echo esc_attr($service->id); ?>" data-bookingdata="<?php echo esc_attr(wp_json_encode( $bookingdata )) ?>" data-servicedata="<?php echo esc_attr(wp_json_encode( $servicedata )) ?>"><?php echo esc_html__('Select Service', 'service-finder'); ?></button>
				</div>
			  </div>
			  <div class="sf-provi-service-bottom">
				<div class="sf-provi-descriptio"><?php echo esc_html($service->description); ?></div>
			  </div>
			</li>
			<?php
			$servicecnt++;
			}
			}
		}
	}
	
	echo service_finder_coupon_code_section($author);
	}
	?>
  </ul>
  <div class="servi-leRi-btn d-flex flex-wrap justify-content-between">

        <?php if($totalservices > $numberofservices){ ?>
        <div class="servi-le-btn">

            <button class="btn btn-custom" id="showLess" type="button"><i class="fa fa-arrow-up"></i></button>

            <button class="btn btn-custom" id="loadMore" type="button"><i class="fa fa-arrow-down"></i></button>

        </div>
        <?php } ?>

        <div class="servi-Ri-btn">

            <button class="btn btn-custom bookthisservices" type="button" data-bookingdata="<?php echo esc_attr(wp_json_encode( $bookingdata )) ?>"><?php echo esc_html__('Continue', 'service-finder'); ?></button>

        </div>  

    </div>
  
  <?php if(service_finder_booking_date_method($author) == 'multidate'){ ?>
    <div class="booking-panel-wrap aonpopupbooking multibookingpopup">
	<span class="sf-serach-result-close"><i class="fa fa-close"></i></span>
	<div class="booking-panel-cell">
        <div class="sf-custom-accordion" id="jobbookingaccordion">
			
            <div class="panel memberspanel hidden">
				<div class="acod-head">
				 <h5 class="acod-title">
					<a data-toggle="collapse"  href="#selectmembersbox" data-parent="#jobbookingaccordion" aria-expanded="false" id="selectmemberheader">
					<?php esc_html_e('Select Member', 'service-finder'); ?>
					<span class="indicator"><i class="fa fa-plus"></i></span>
					</a>
				 </h5>
				</div>
				<div id="selectmembersbox" class="acod-body collapse">
					<div class="acod-content">
						<div class="booking-panel-step-three">
                            <div class="booking-panel-hours-wrap">
                                <select name="members_list" class="form-control sf-form-control sf-select-box">
                				</select>
                                
                                <div id="sf-bookingmember-image" style="display:none">
            					</div>
                            </div>
                            <div class="booking-panel-btn-wrap"> 
                                <button type="button" class="btn btn-primary closepanel"><?php esc_html_e('Cancel', 'service-finder'); ?></button>
                                <button type="button" class="btn btn-custom continuefrommember"><?php esc_html_e('Continue', 'service-finder'); ?></button> 
                            </div>            
                        </div>
					</div>
				</div>
			</div>
            
            <div class="panel hourspanel hidden">
				<div class="acod-head">
				 <h5 class="acod-title">
					<a data-toggle="collapse"  href="#selecthoursbox" data-parent="#jobbookingaccordion" aria-expanded="false" id="selecthourheader">
					<span id="hrperlabel"><?php esc_html_e('Select hours', 'service-finder'); ?></span>
					<span class="indicator"><i class="fa fa-plus"></i></span>
					</a>
				 </h5>
				</div>
				<div id="selecthoursbox" class="acod-body collapse">
					<div class="acod-content">
						<div class="booking-panel-step-three">
                            <div class="booking-panel-hours-wrap">
                                <input class="form-control servicehourspin" type="text" name="customhours" value="1">
                            </div>
                            <div class="booking-panel-btn-wrap"> 
                                <button type="button" class="btn btn-primary closepanel"><?php esc_html_e('Cancel', 'service-finder'); ?></button>
                                <button type="button" class="btn btn-custom continuetocalendar"><?php esc_html_e('Continue', 'service-finder'); ?></button> 
                            </div>            
                        </div>
					</div>
				</div>
			</div>
            
            <div class="panel">
				<div class="acod-head">
				 <h5 class="acod-title">
					<a data-toggle="collapse" href="#datetimebox" data-parent="#jobbookingaccordion" aria-expanded="false" id="datetimeheader">
					<?php esc_html_e('Delivery Date/Time', 'service-finder'); ?>
					<span class="indicator"><i class="fa fa-plus"></i></span>
					</a>
				 </h5>
				</div>
				<div id="datetimebox" class="acod-body collapse">
					<div class="acod-content">
						<div class="booking-panel-step-three">
                            <div class="booking-panel-calender-wrap">
                                <div class="jobbookingdate"></div>
                            </div>
                            <div class="booking-panel-time-slot">
                                <div class="booking-slots-outer">
                                  <ul class="list-inline clearfix timeslots timelist">
                                  </ul>
                                </div>            	
                            </div>
                            <div class="booking-panel-btn-wrap"> 
                                <button type="button" class="btn btn-primary closepanel"><?php esc_html_e('Cancel', 'service-finder'); ?></button>
                                <button type="button" class="btn btn-custom continuetomoreservices"><?php esc_html_e('Continue', 'service-finder'); ?></button> 
                            </div>            
                        </div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
	<div class="booking-panel-overlay"></div> 
	<div class="checkout-panel-wrap">
	<span class="sf-serach-result-close"><i class="fa fa-close"></i></span>
	<div class="booking-panel-cell">
		<?php
		if(service_finder_has_pay_only_admin_fee()){
		?>
		<div class="sf-adminfee-outer" style="display:none" id="adminfee-outer">
		<div class="sf-payonly-adminfee"><span><?php echo esc_html__('Admin Fee:', 'service-finder'); ?></span> <span id="onlyadminfee"></span></div>
		<div class="sf-payonly-adminfee"><?php echo esc_html__('You need to pay only admin fee at the time of booking.', 'service-finder'); ?></div>
		</div>
		<?php
		}
		?>
        <div class="sf-custom-accordion" id="jobbookingaccordion">
            <div class="panel">
				<div class="acod-head">
				 <h5 class="acod-title">
					<a data-toggle="collapse" href="#customerinfobox" data-parent="#jobbookingaccordion" aria-expanded="false" id="customerinfoheader">
					<?php esc_html_e('Customer Info', 'service-finder'); ?>
					<span class="indicator"><i class="fa fa-plus"></i></span>
					</a>
				 </h5>
				</div>
				<div id="customerinfobox" class="acod-body collapse">
					<div class="acod-content">
						<div class="booking-panel-step-four">
                            <div class="m-b30 clearfix sf-aad-booking-backbtn">
                                <h4><span class="sf-heading-icon text-blue"><i class="fa fa-user"></i></span> <?php esc_html_e('Your Details', 'service-finder'); ?></h4>
                                <div class="row">
                                    <?php if(service_finder_getUserRole($current_user->ID) == 'Provider' || service_finder_getUserRole($current_user->ID) == 'administrator'){ ?>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                          <label>
                                          <?php esc_html_e('Select Customer', 'service-finder'); ?>
                                          </label>
                                          <div class="input-group"> 
                                            <select name="choose_customer" id="choose_customer" class="form-control sf-form-control sf-select-box">
                                                <option value=""><?php esc_html_e('New Customer', 'service-finder'); ?></option>
                                                <?php
                                                $customers = service_finder_get_providers_customer($author);
                                                if(!empty($customers)){
                                                    foreach($customers as $customer){
                                                        echo '<option value="'.esc_attr($customer->customerid).'">'.esc_html($customer->customer_name).'</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                          </div>
                                        </div>
                                      </div>
                                     <?php } ?> 
                                    <div class="col-md-6">
                                      <div class="form-group">
                                        <label>
                                        <?php esc_html_e('First Name', 'service-finder'); ?>
                                        </label>
                                        <div class="input-group"> <i class="input-group-addon fa fa-user"></i>
                                          <input name="firstname" id="firstname" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo['fname'])) ? esc_attr($userInfo['fname']) : ''; ?>">
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-6">
                                      <div class="form-group">
                                        <label>
                                        <?php esc_html_e('Last Name', 'service-finder'); ?>
                                        </label>
                                        <div class="input-group"> <i class="input-group-addon fa fa-user"></i>
                                          <input name="lastname" id="lastname" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo['lname'])) ? esc_attr($userInfo['lname']) : ''; ?>">
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-6">
                                      <div class="form-group">
                                        <label>
                                        <?php esc_html_e('Email', 'service-finder'); ?>
                                        </label>
                                        <div class="input-group"> <i class="input-group-addon fa fa-envelope"></i>
                                          <input id="email" name="email" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo[0]->user_email)) ? esc_attr($userInfo[0]->user_email) : ''; ?>">
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-6">
                                      <div class="form-group">
                                        <label>
                                        <?php esc_html_e('Phone', 'service-finder'); ?>
                                        </label>
                                        <div class="input-group"> <i class="input-group-addon fa fa-phone"></i>
                                          <input id="phone" name="phone" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo['phone'])) ? esc_attr($userInfo['phone']) : ''; ?>">
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-6">
                                      <div class="form-group">
                                        <label>
                                        <?php esc_html_e('Alt. Phone', 'service-finder'); ?>
                                        </label>
                                        <div class="input-group"> <i class="input-group-addon fa fa-phone"></i>
                                          <input name="phone2" id="phone2" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo['phone2'])) ? esc_attr($userInfo['phone2']) : ''; ?>">
                                        </div>
                                      </div>
                                    </div>
                                    
                                    <?php 
                                    $service_perform = get_user_meta($author,'service_perform',true); 
                                    if($service_perform == 'both' && $service_finder_options['show-address-info'] && service_finder_check_address_info_access()){
                                    ?>
                                    <div class="col-lg-12">
                                      <label>
                                      <?php esc_html_e('Service Perform At', 'service-finder'); ?>
                                      </label>
                                      <div class="form-group form-inline">
                                        <div class="radio sf-radio-checkbox">
                                          <input id="booking_provider_location" type="radio" name="service_perform_at" value="provider_location">
                                          <label for="booking_provider_location">
                                          <?php echo sprintf(esc_html__('%s Location', 'service-finder'),service_finder_provider_replace_string()); ?>
                                          </label>
                                        </div>
                                        <div class="radio sf-radio-checkbox">
                                          <input id="booking_customer_location" checked="checked" type="radio" name="service_perform_at" value="customer_location">
                                          <label for="booking_customer_location">
                                          <?php esc_html_e('Your Location', 'service-finder'); ?>
                                          </label>
                                        </div>
                                      </div>
                                    </div>		
                                    <?php
                                    }elseif($service_perform == 'provider_location')
                                    {
                                        echo '<input type="hidden" name="service_perform_at" value="provider_location">';
                                    }elseif($service_perform == 'customer_location')
                                    {
                                        echo '<input type="hidden" name="service_perform_at" value="customer_location">';
                                    }
                                    
                                    if(($service_perform == 'provider_location' || $service_perform == 'both') && $service_finder_options['show-address-info'] && service_finder_check_address_info_access()){
                                    $my_location = get_user_meta($author,'my_location',true); 
                                    $providerlat = get_user_meta($author,'providerlat',true); 
                                    $providerlng = get_user_meta($author,'providerlng',true); 
                                    $locationzoomlevel = get_user_meta($author,'locationzoomlevel',true);
                                    $defaulthide = ($service_perform == 'both') ? 'display:none' : '';
                                    ?>
                                    <div class="sf-service-location-area" id="bookingproviderlocation" style=" <?php echo esc_attr($defaulthide); ?>">
                                          <h6 class="sf-title">
                                          <?php esc_html_e('Service Location', 'service-finder'); ?>
                                          </h6>
                                          <div class="sf-location-text"><i class="fa fa-map-marker"></i> <?php echo $my_location; ?></div>
                                          <button class="btn btn-primary btn-sm" data-tool="tooltip" id="viewmylocation" data-locationzoomlevel="<?php echo esc_attr($locationzoomlevel); ?>" data-providerlat="<?php echo esc_attr($providerlat); ?>" data-providerlng="<?php echo esc_attr($providerlng); ?>" type="button">
                                          <i class="fa fa-map-o"></i> <?php echo esc_html__('View Map','servide-finder'); ?>
                                          </button>
                                      </div>
                                    <?php } ?>
                                    <?php 
                                    if($settings['booking_basedon'] == 'zipcode'){ 
                                    ?>
                                    <div class="col-md-12" id="bookingcustomerzipcode">
                                        <div class="form-group sf-zipcode-area">
                                          <label>
                                          <?php esc_html_e('Enter Your Zip code', 'service-finder'); ?>
                                          </label>
                                          <div class="input-group"> <i class="input-group-addon fa fa-map-marker"></i>
                                            <input id="zipcode"  name="zipcode" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo['zipcode'])) ? esc_attr($userInfo['zipcode']) : ''; ?>">
                                          </div>
                                        </div>
                                      </div>
                                    <?php
                                    }elseif($settings['booking_basedon'] == 'region'){
                                    ?>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                          <div class="input-group"> 
                                            <select name="region" id="region" class="form-control sf-form-control sf-select-box">
                                                <option value=""><?php esc_html_e('Select Region', 'service-finder'); ?></option>
                                                <?php
                                                $regions = service_finder_getServiceRegions($author);
                                                if(!empty($regions)){
                                                    foreach($regions as $region){
                                                        echo '<option value="'.esc_attr($region->region).'">'.esc_html($region->region).'</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                          </div>
                                        </div>
                                      </div>
                                    <?php
                                    }
                                    ?>
                                    
                                    <div class="col-md-8">
                                      <div class="form-group">
                                        <label>
                                        <?php esc_html_e('Address', 'service-finder'); ?>
                                        </label>
                                        <div class="input-group"> <i class="input-group-addon fa fa-globe"></i>
                                          <input id="booking-address" name="address" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo['address'])) ? esc_attr($userInfo['address']) : ''; ?>">
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-4">
                                      <div class="form-group">
                                        <label>
                                        <?php esc_html_e('Apt/Suite #', 'service-finder'); ?>
                                        </label>
                                        <div class="input-group"> <i class="input-group-addon fa fa-building-o"></i>
                                          <input name="apt" id="apt" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo['apt'])) ? esc_attr($userInfo['apt']) : ''; ?>">
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-6">
                                      <div class="form-group">
                                        <label>
                                        <?php esc_html_e('City', 'service-finder'); ?>
                                        </label>
                                        <div class="input-group"> <i class="input-group-addon fa fa-map-marker"></i>
                                          <input id="bookingcity" name="city" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo['city'])) ? esc_attr($userInfo['city']) : ''; ?>">
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-6">
                                      <div class="form-group">
                                        <label>
                                        <?php esc_html_e('State', 'service-finder'); ?>
                                        </label>
                                        <div class="input-group"> <i class="input-group-addon fa fa-map-marker"></i>
                                          <input id="bookingstate" name="state" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo['state'])) ? esc_attr($userInfo['state']) : ''; ?>">
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-6">
                                      <div class="form-group">
                                        <label>
                                        <?php esc_html_e('Country', 'service-finder'); ?>
                                        </label>
                                        <div class="input-group"> <i class="input-group-addon fa fa-map-marker"></i>
                                          <input id="bookingcountry" name="country" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo['country'])) ? esc_attr($userInfo['country']) : ''; ?>">
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-12">
                                      <div class="form-group">
                                        <label>
                                        <?php esc_html_e('Describe Your Task', 'service-finder'); ?>
                                        </label>
                                        <div class="input-group"> <span class="input-group-addon v-align-t"><i class="fa fa-pencil"></i></span>
                                          <textarea id="shortdesc" name="shortdesc" class="form-control sf-form-control" placeholder="<?php esc_html_e('Please insert short description of your task', 'service-finder'); ?>"></textarea>
                                        </div>
                                      </div>
                                    </div>
                                </div>
                                <div class="booking-panel-btn-wrap"> 
                                    <button type="button" class="btn btn-primary backtodatetimebox"><?php esc_html_e('Back', 'service-finder'); ?></button>
                                    <button type="button" class="btn btn-custom continuetopayment"> <?php esc_html_e('Proceed to Checkout', 'service-finder'); ?></button> 
                                </div>
                            </div>
						</div>
					</div>
				</div>
			</div>
			<div class="panel" id="jobbooking-paid-panel">
				<div class="acod-head">
				 <h5 class="acod-title">
					<a data-toggle="collapse"  href="#paymentbox" data-parent="#jobbookingaccordion" aria-expanded="false" id="paymentheader">
					<?php esc_html_e('Payment', 'service-finder'); ?>
					<span class="indicator"><i class="fa fa-plus"></i></span>
					</a>
				 </h5>
				</div>
				<div id="paymentbox" class="acod-body collapse">
					<div class="acod-content">
						<div class="booking-panel-step-four">
		
                        <div class="clearfix f-row">
                          <?php echo service_finder_multidate_coupon_code($author); ?>
						  <?php echo $showadminfee; ?>
                          <?php echo service_finder_display_wallet_amount($current_user->ID); ?> 
                            <div class="col-md-12">
                              <div class="form-group form-inline">
                                <div class="col-md-12">
                                  <div class="form-group form-inline sf-card-group" id="sf-payment-options">
                                    <?php
									$payflag = 0;
                                    if(service_finder_get_payment_goes_to() == 'admin')
									{
										$paymentmethods = service_finder_get_payment_methods();
										
										if(!empty($paymentmethods))
										{
											foreach($paymentmethods as $key => $paymentmethod)
											{
												if($paymentmethod == 1)
												{
												$payflag = 1;
												?>
												<div class="radio sf-radio-checkbox">
                                                  <input type="radio" value="<?php echo esc_attr($key); ?>" name="bookingpayment_mode" id="paymentvia<?php echo esc_attr($key); ?>" >
                                                  <label for="paymentvia<?php echo esc_attr($key); ?>"><?php echo '<img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/'.$key.'.jpg" title="'.esc_attr(ucfirst($key)).'" alt="'.esc_attr(ucfirst($key)).'">'; ?></label>
                                                </div>
												<?php
												}
											}
										}
									}
									?>
                                    <?php 
                                    echo service_finder_add_wallet_option('bookingpayment_mode','paymentvia');
                                    echo service_finder_add_skip_option('bookingpayment_mode','paymentvia');
                                    ?>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div id="bookingcardinfo" class="default-hidden">
                              <div class="col-md-8">
                                <div class="form-group">
                                  <label>
                                  <?php esc_html_e('Card Number', 'service-finder'); ?>
                                  </label>
                                  <div class="input-group"> <i class="input-group-addon fa fa-credit-card"></i>
                                    <input type="text" id="card_number" name="card_number" class="form-control sf-form-control">
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-4">
                                <div class="form-group">
                                  <label>
                                  <?php esc_html_e('CVC', 'service-finder'); ?>
                                  </label>
                                  <div class="input-group"> <i class="input-group-addon fa fa-ellipsis-h"></i>
                                    <input type="text" id="card_cvc" name="card_cvc" class="form-control sf-form-control">
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group has-select">
                                  <label>
                                  <?php esc_html_e('Select Month', 'service-finder'); ?>
                                  </label>
                                  <select id="card_month" name="card_month" class="form-control sf-form-control sf-select-box" title="Select Month">
                                      <option value="1"><?php echo esc_html__('January', 'service-finder') ?></option>
                                      <option value="2"><?php echo esc_html__('February', 'service-finder')?></option>
                                      <option value="3"><?php echo esc_html__('March', 'service-finder')?></option>
                                      <option value="4"><?php echo esc_html__('April', 'service-finder')?></option>
                                      <option value="5"><?php echo esc_html__('May', 'service-finder')?></option>
                                      <option value="6"><?php echo esc_html__('June', 'service-finder')?></option>
                                      <option value="7"><?php echo esc_html__('July', 'service-finder')?></option>
                                      <option value="8"><?php echo esc_html__('August', 'service-finder')?></option>
                                      <option value="9"><?php echo esc_html__('September', 'service-finder')?></option>
                                      <option value="10"><?php echo esc_html__('October', 'service-finder')?></option>
                                      <option value="11"><?php echo esc_html__('November', 'service-finder')?></option>
                                      <option value="12"><?php echo esc_html__('December', 'service-finder')?></option>
                                  </select>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group has-select">
                                  <label>
                                  <?php esc_html_e('Select Year', 'service-finder'); ?>
                                  </label>
                                  <select id="card_year" name="card_year" class="form-control sf-form-control sf-select-box"  title="Select Year">
                                    <?php
                                                                $year = date('Y');
                                                                for($i = $year;$i<=$year+50;$i++){
                                                                    echo '<option value="'.esc_attr($i).'">'.$i.'</option>';
                                                                }
                                                                ?>
                                  </select>
                                </div>
                              </div>
                            </div>
                            <div id="bookingtwocheckoutcardinfo" class="default-hidden">
                                <div class="col-md-8">
                                  <div class="form-group">
                                    <label>
                                    <?php esc_html_e('Card Number', 'service-finder'); ?>
                                    </label>
                                    <div class="input-group"> <i class="input-group-addon fa fa-credit-card"></i>
                                      <input type="text" id="twocheckout_card_number" name="twocheckout_card_number" class="form-control sf-form-control">
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-4">
                                  <div class="form-group">
                                    <label>
                                    <?php esc_html_e('CVC', 'service-finder'); ?>
                                    </label>
                                    <div class="input-group"> <i class="input-group-addon fa fa-ellipsis-h"></i>
                                      <input type="text" id="twocheckout_card_cvc" name="twocheckout_card_cvc" class="form-control sf-form-control">
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-select">
                                    <label>
                                    <?php esc_html_e('Select Month', 'service-finder'); ?>
                                    </label>
                                    <select id="twocheckout_card_month" name="twocheckout_card_month" class="form-control sf-form-control sf-select-box" title="Select Month">
                                      <option value="1"><?php echo esc_html__('January', 'service-finder') ?></option>
                                      <option value="2"><?php echo esc_html__('February', 'service-finder')?></option>
                                      <option value="3"><?php echo esc_html__('March', 'service-finder')?></option>
                                      <option value="4"><?php echo esc_html__('April', 'service-finder')?></option>
                                      <option value="5"><?php echo esc_html__('May', 'service-finder')?></option>
                                      <option value="6"><?php echo esc_html__('June', 'service-finder')?></option>
                                      <option value="7"><?php echo esc_html__('July', 'service-finder')?></option>
                                      <option value="8"><?php echo esc_html__('August', 'service-finder')?></option>
                                      <option value="9"><?php echo esc_html__('September', 'service-finder')?></option>
                                      <option value="10"><?php echo esc_html__('October', 'service-finder')?></option>
                                      <option value="11"><?php echo esc_html__('November', 'service-finder')?></option>
                                      <option value="12"><?php echo esc_html__('December', 'service-finder')?></option>
                                    </select>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-select">
                                    <label>
                                    <?php esc_html_e('Select Year', 'service-finder'); ?>
                                    </label>
                                    <select id="twocheckout_card_year" name="twocheckout_card_year" class="form-control sf-form-control sf-select-box"  title="Select Year">
                                      <?php
                                                                $year = date('Y');
                                                                for($i = $year;$i<=$year+50;$i++){
                                                                    echo '<option value="'.$i.'">'.$i.'</option>';
                                                                }
                                                                ?>
                                    </select>
                                  </div>
                                </div>
                              </div>
                            <div id="bookingpayulatamcardinfo" class="default-hidden">
                                <div class="col-md-12">
                                  <div class="form-group">
                                    <label>
                                    <?php esc_html_e('Select Card', 'service-finder'); ?>
                                    </label>
                                    <select id="payulatam_cardtype" name="payulatam_cardtype" class="form-control sf-form-control sf-select-box"  title="<?php esc_html_e('Select Card', 'service-finder'); ?>">
                                      <?php
                                      $country = (isset($service_finder_options['payulatam-country'])) ? $service_finder_options['payulatam-country'] : '';
                                      $cards = service_finder_get_cards($country);
                                      foreach($cards as $card){
                                        echo '<option value="'.esc_attr($card).'">'.$card.'</option>';
                                      }
                                                                
                                                                ?>
                                    </select>
                                  </div>
                                </div>
                                <div class="col-md-8">
                                  <div class="form-group">
                                    <label>
                                    <?php esc_html_e('Card Number', 'service-finder'); ?>
                                    </label>
                                    <div class="input-group"> <i class="input-group-addon fa fa-credit-card"></i>
                                      <input type="text" id="payulatam_card_number" name="payulatam_card_number" class="form-control sf-form-control">
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-4">
                                  <div class="form-group">
                                    <label>
                                    <?php esc_html_e('CVC', 'service-finder'); ?>
                                    </label>
                                    <div class="input-group"> <i class="input-group-addon fa fa-ellipsis-h"></i>
                                      <input type="text" id="payulatam_card_cvc" name="payulatam_card_cvc" class="form-control sf-form-control">
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-select">
                                    <label>
                                    <?php esc_html_e('Select Month', 'service-finder'); ?>
                                    </label>
                                    <select id="payulatam_card_month" name="payulatam_card_month" class="form-control sf-form-control sf-select-box" title="<?php esc_html_e('Select Month', 'service-finder'); ?>">
                                      <option value="01"><?php echo esc_html__('January', 'service-finder') ?></option>
                                      <option value="02"><?php echo esc_html__('February', 'service-finder')?></option>
                                      <option value="03"><?php echo esc_html__('March', 'service-finder')?></option>
                                      <option value="04"><?php echo esc_html__('April', 'service-finder')?></option>
                                      <option value="05"><?php echo esc_html__('May', 'service-finder')?></option>
                                      <option value="06"><?php echo esc_html__('June', 'service-finder')?></option>
                                      <option value="07"><?php echo esc_html__('July', 'service-finder')?></option>
                                      <option value="08"><?php echo esc_html__('August', 'service-finder')?></option>
                                      <option value="09"><?php echo esc_html__('September', 'service-finder')?></option>
                                      <option value="10"><?php echo esc_html__('October', 'service-finder')?></option>
                                      <option value="11"><?php echo esc_html__('November', 'service-finder')?></option>
                                      <option value="12"><?php echo esc_html__('December', 'service-finder')?></option>
                                    </select>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-select">
                                    <label>
                                    <?php esc_html_e('Select Year', 'service-finder'); ?>
                                    </label>
                                    <select id="payulatam_card_year" name="payulatam_card_year" class="form-control sf-form-control sf-select-box"  title="<?php esc_html_e('Select Year', 'service-finder'); ?>">
                                      <?php
                                                                $year = date('Y');
                                                                for($i = $year;$i<=$year+50;$i++){
                                                                    echo '<option value="'.$i.'">'.$i.'</option>';
                                                                }
                                                                ?>
                                    </select>
                                  </div>
                                </div>
                              </div>
                            <div id="wiredinfo" class="default-hidden">
                                <div class="col-md-12">
                                    <?php
                                    $pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? $service_finder_options['pay_booking_amount_to'] : '';
                                    if($pay_booking_amount_to == 'admin'){
                                    $description = (!empty($service_finder_options['wire-transfer-description'])) ? $service_finder_options['wire-transfer-description'] : '';
                                    echo $description;
                                    }elseif($pay_booking_amount_to == 'provider'){
                                    echo (!empty($settings['wired_description'])) ? $settings['wired_description'] : '';
                                    }
                                    ?>
                                </div>
                              </div>
                            <?php if($payflag == 1 || service_finder_check_wallet_system() || service_finder_get_payment_goes_to() == 'provider'){ ?>  
                            <div class="col-md-12" id="sf-bookform-submitarea">
                              <div class="form-group">
                                <input name="book-now" id="save-booking" type="submit" value="<?php esc_html_e('Pay Now', 'service-finder'); ?>" class="btn btn-primary center-block">
                              </div>
                            </div>
                            <?php }else{
                            echo '<p>';
                            echo esc_html__('There is no payment method available.','service-finder');
                            echo '</p>';
                            } ?>
                          </div> 
                    </div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
    <div class="checkout-panel-overlay"></div>
    
    <div class="sf-summery-box" id="bookingsmry" style="display:none">
            <button type="button" class="sf-suumery-close">
                <i class="fa fa-expand"></i>
            </button>
            <div class="sf-summery-total">
                <span class="sf-sum-cel-one"><?php esc_html_e('Total Amount', 'service-finder'); ?></span>
                <span class="sf-sum-cel-four" id="smrytotalamount"></span>
            </div>
            <div class="sf-summery-inr">
                <ul id="summarywrap">
                </ul>
            </div>
        </div>
  <?php }else{ ?>
  	<div class="booking-panel-wrap aonpopupbooking">
	<span class="sf-serach-result-close"><i class="fa fa-close"></i></span>
	<div class="booking-panel-cell">
		<?php
		if(service_finder_has_pay_only_admin_fee()){
		?>
		<div class="sf-adminfee-outer" style="display:none" id="adminfee-outer">
		<div class="sf-payonly-adminfee"><span><?php echo esc_html__('Admin Fee:', 'service-finder'); ?></span> <span id="onlyadminfee"></span></div>
		<div class="sf-payonly-adminfee"><?php echo esc_html__('You need to pay only admin fee at the time of booking.', 'service-finder'); ?></div>
		</div>
		<?php
		}
		?>
        <div class="sf-custom-accordion" id="jobbookingaccordion">
            <div class="panel">
				<div class="acod-head">
				 <h5 class="acod-title">
					<a data-toggle="collapse" href="#datetimebox" data-parent="#jobbookingaccordion" aria-expanded="false" id="datetimeheader">
					<?php esc_html_e('Delivery Date/Time', 'service-finder'); ?>
					<span class="indicator"><i class="fa fa-plus"></i></span>
					</a>
				 </h5>
				</div>
				<div id="datetimebox" class="acod-body collapse in">
					<div class="acod-content">
						<div class="booking-panel-step-three">
                            <div class="booking-panel-calender-wrap">
                                <div class="jobbookingdate"></div>
                            </div>
                            <div class="booking-panel-time-slot">
                                <div class="booking-slots-outer">
                                  <ul class="list-inline clearfix timeslots timelist">
                                  </ul>
                                </div>     
                                <?php if($loadmembers == 'yes'){ ?>
                                  <div class="staff-member clear">
                                    <div class="col-md-12" id="members"> </div>
                                  </div>
                                <?php } ?> 
                            </div>
                            <div class="booking-panel-btn-wrap"> 
                                <button type="button" class="btn btn-custom continuetocustomerinfo"><?php esc_html_e('Continue', 'service-finder'); ?></button> 
                            </div>            
                        </div>
					</div>
				</div>
			</div>
			<div class="panel">
				<div class="acod-head">
				 <h5 class="acod-title">
					<a data-toggle="collapse"  href="#customerinfobox" data-parent="#jobbookingaccordion" aria-expanded="false" id="customerinfoheader">
					<?php esc_html_e('Customer Info', 'service-finder'); ?>
					<span class="indicator"><i class="fa fa-plus"></i></span>
					</a>
				 </h5>
				</div>
				<div id="customerinfobox" class="acod-body collapse">
					<div class="acod-content">
						<div class="booking-panel-step-four">
                            <div class="m-b30 clearfix sf-aad-booking-backbtn">
                                <h4><span class="sf-heading-icon text-blue"><i class="fa fa-user"></i></span> <?php esc_html_e('Your Details', 'service-finder'); ?></h4>
                                <div class="row">
                                    <?php if(service_finder_getUserRole($current_user->ID) == 'Provider' || service_finder_getUserRole($current_user->ID) == 'administrator'){ ?>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                          <label>
                                          <?php esc_html_e('Select Customer', 'service-finder'); ?>
                                          </label>
                                          <div class="input-group"> 
                                            <select name="choose_customer" id="choose_customer" class="form-control sf-form-control sf-select-box">
                                                <option value=""><?php esc_html_e('New Customer', 'service-finder'); ?></option>
                                                <?php
                                                $customers = service_finder_get_providers_customer($author);
                                                if(!empty($customers)){
                                                    foreach($customers as $customer){
                                                        echo '<option value="'.esc_attr($customer->customerid).'">'.esc_html($customer->customer_name).'</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                          </div>
                                        </div>
                                      </div>
                                     <?php } ?> 
                                    <div class="col-md-6">
                                      <div class="form-group">
                                        <label>
                                        <?php esc_html_e('First Name', 'service-finder'); ?>
                                        </label>
                                        <div class="input-group"> <i class="input-group-addon fa fa-user"></i>
                                          <input name="firstname" id="firstname" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo['fname'])) ? esc_attr($userInfo['fname']) : ''; ?>">
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-6">
                                      <div class="form-group">
                                        <label>
                                        <?php esc_html_e('Last Name', 'service-finder'); ?>
                                        </label>
                                        <div class="input-group"> <i class="input-group-addon fa fa-user"></i>
                                          <input name="lastname" id="lastname" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo['lname'])) ? esc_attr($userInfo['lname']) : ''; ?>">
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-6">
                                      <div class="form-group">
                                        <label>
                                        <?php esc_html_e('Email', 'service-finder'); ?>
                                        </label>
                                        <div class="input-group"> <i class="input-group-addon fa fa-envelope"></i>
                                          <input id="email" name="email" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo[0]->user_email)) ? esc_attr($userInfo[0]->user_email) : ''; ?>">
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-6">
                                      <div class="form-group">
                                        <label>
                                        <?php esc_html_e('Phone', 'service-finder'); ?>
                                        </label>
                                        <div class="input-group"> <i class="input-group-addon fa fa-phone"></i>
                                          <input id="phone" name="phone" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo['phone'])) ? esc_attr($userInfo['phone']) : ''; ?>">
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-6">
                                      <div class="form-group">
                                        <label>
                                        <?php esc_html_e('Alt. Phone', 'service-finder'); ?>
                                        </label>
                                        <div class="input-group"> <i class="input-group-addon fa fa-phone"></i>
                                          <input name="phone2" id="phone2" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo['phone2'])) ? esc_attr($userInfo['phone2']) : ''; ?>">
                                        </div>
                                      </div>
                                    </div>
                                    
                                    
                                    <?php 
                                    $service_perform = get_user_meta($author,'service_perform',true); 
                                    if($service_perform == 'both' && $service_finder_options['show-address-info'] && service_finder_check_address_info_access()){
                                    ?>
                                    <div class="col-lg-12">
                                      <label>
                                      <?php esc_html_e('Service Perform At', 'service-finder'); ?>
                                      </label>
                                      <div class="form-group form-inline">
                                        <div class="radio sf-radio-checkbox">
                                          <input id="booking_provider_location" type="radio" name="service_perform_at" value="provider_location">
                                          <label for="booking_provider_location">
                                          <?php echo sprintf(esc_html__('%s Location', 'service-finder'),service_finder_provider_replace_string()); ?>
                                          </label>
                                        </div>
                                        <div class="radio sf-radio-checkbox">
                                          <input id="booking_customer_location" checked="checked" type="radio" name="service_perform_at" value="customer_location">
                                          <label for="booking_customer_location">
                                          <?php esc_html_e('Your Location', 'service-finder'); ?>
                                          </label>
                                        </div>
                                      </div>
                                    </div>		
                                    <?php
                                    }elseif($service_perform == 'provider_location')
                                    {
                                        echo '<input type="hidden" name="service_perform_at" value="provider_location">';
                                    }elseif($service_perform == 'customer_location')
                                    {
                                        echo '<input type="hidden" name="service_perform_at" value="customer_location">';
                                    }
                                    
                                    if(($service_perform == 'provider_location' || $service_perform == 'both') && $service_finder_options['show-address-info'] && service_finder_check_address_info_access()){
                                    $my_location = get_user_meta($author,'my_location',true); 
                                    $providerlat = get_user_meta($author,'providerlat',true); 
                                    $providerlng = get_user_meta($author,'providerlng',true); 
                                    $locationzoomlevel = get_user_meta($author,'locationzoomlevel',true);
                                    $defaulthide = ($service_perform == 'both') ? 'display:none' : '';
                                    ?>
                                      <div class="sf-service-location-area" id="bookingproviderlocation" style=" <?php echo esc_attr($defaulthide); ?>">
                                          <h6 class="sf-title">
                                          <?php esc_html_e('Service Location', 'service-finder'); ?>
                                          </h6>
                                          <div class="sf-location-text"><i class="fa fa-map-marker"></i> <?php echo $my_location; ?></div>
                                          <button class="btn btn-primary btn-sm" data-tool="tooltip" id="viewmylocation" data-locationzoomlevel="<?php echo esc_attr($locationzoomlevel); ?>" data-providerlat="<?php echo esc_attr($providerlat); ?>" data-providerlng="<?php echo esc_attr($providerlng); ?>" type="button">
                                          <i class="fa fa-map-o"></i> <?php echo esc_html__('View Map','servide-finder'); ?>
                                          </button>
                                      </div>
                                    <?php } ?>
                                    <?php 
                                    if($settings['booking_basedon'] == 'zipcode'){ 
                                    ?>
                                    <div class="col-md-12" id="bookingcustomerzipcode">
                                        <div class="form-group sf-zipcode-area">
                                          <label>
                                          <?php esc_html_e('Enter Your Zip code', 'service-finder'); ?>
                                          </label>
                                          <div class="input-group"> <i class="input-group-addon fa fa-map-marker"></i>
                                            <input id="zipcode"  name="zipcode" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo['zipcode'])) ? esc_attr($userInfo['zipcode']) : ''; ?>">
                                          </div>
                                        </div>
                                      </div>
                                    <?php
                                    }elseif($settings['booking_basedon'] == 'region'){
                                    ?>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                          <div class="input-group"> 
                                            <select name="region" id="region" class="form-control sf-form-control sf-select-box">
                                                <option value=""><?php esc_html_e('Select Region', 'service-finder'); ?></option>
                                                <?php
                                                $regions = service_finder_getServiceRegions($author);
                                                if(!empty($regions)){
                                                    foreach($regions as $region){
                                                        echo '<option value="'.esc_attr($region->region).'">'.esc_html($region->region).'</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                          </div>
                                        </div>
                                      </div>
                                    <?php
                                    }
                                    ?>
                                    
                                    <div class="col-md-8">
                                      <div class="form-group">
                                        <label>
                                        <?php esc_html_e('Address', 'service-finder'); ?>
                                        </label>
                                        <div class="input-group"> <i class="input-group-addon fa fa-globe"></i>
                                          <input id="booking-address" name="address" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo['address'])) ? esc_attr($userInfo['address']) : ''; ?>">
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-4">
                                      <div class="form-group">
                                        <label>
                                        <?php esc_html_e('Apt/Suite #', 'service-finder'); ?>
                                        </label>
                                        <div class="input-group"> <i class="input-group-addon fa fa-building-o"></i>
                                          <input name="apt" id="apt" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo['apt'])) ? esc_attr($userInfo['apt']) : ''; ?>">
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-6">
                                      <div class="form-group">
                                        <label>
                                        <?php esc_html_e('City', 'service-finder'); ?>
                                        </label>
                                        <div class="input-group"> <i class="input-group-addon fa fa-map-marker"></i>
                                          <input id="bookingcity" name="city" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo['city'])) ? esc_attr($userInfo['city']) : ''; ?>">
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-6">
                                      <div class="form-group">
                                        <label>
                                        <?php esc_html_e('State', 'service-finder'); ?>
                                        </label>
                                        <div class="input-group"> <i class="input-group-addon fa fa-map-marker"></i>
                                          <input id="bookingstate" name="state" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo['state'])) ? esc_attr($userInfo['state']) : ''; ?>">
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-6">
                                      <div class="form-group">
                                        <label>
                                        <?php esc_html_e('Country', 'service-finder'); ?>
                                        </label>
                                        <div class="input-group"> <i class="input-group-addon fa fa-map-marker"></i>
                                          <input id="bookingcountry" name="country" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo['country'])) ? esc_attr($userInfo['country']) : ''; ?>">
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-12">
                                      <div class="form-group">
                                        <label>
                                        <?php esc_html_e('Describe Your Task', 'service-finder'); ?>
                                        </label>
                                        <div class="input-group"> <span class="input-group-addon v-align-t"><i class="fa fa-pencil"></i></span>
                                          <textarea id="shortdesc" name="shortdesc" class="form-control sf-form-control" placeholder="<?php esc_html_e('Please insert short description of your task', 'service-finder'); ?>"></textarea>
                                        </div>
                                      </div>
                                    </div>
                                </div>
                                <div class="booking-panel-btn-wrap"> 
                                    <button type="button" class="btn btn-primary backtodatetimebox"><?php esc_html_e('Back', 'service-finder'); ?></button>
                                    <button type="button" class="btn btn-custom continuetopayment"> <?php esc_html_e('Proceed to Checkout', 'service-finder'); ?></button> 
                                </div>
                            </div>
						</div>
					</div>
				</div>
			</div>
			<div class="panel" id="jobbooking-paid-panel">
				<div class="acod-head">
				 <h5 class="acod-title">
					<a data-toggle="collapse"  href="#paymentbox" data-parent="#jobbookingaccordion" aria-expanded="false" id="paymentheader">
					<?php esc_html_e('Payment', 'service-finder'); ?>
					<span class="indicator"><i class="fa fa-plus"></i></span>
					</a>
				 </h5>
				</div>
				<div id="paymentbox" class="acod-body collapse">
					<div class="acod-content">
						<div class="booking-panel-step-four">
		
                        <div class="clearfix f-row">
                          <?php echo service_finder_multidate_coupon_code($author); ?> 
						  <?php echo $showadminfee; ?>
                          <?php echo service_finder_display_wallet_amount($current_user->ID); ?> 
                            <div class="col-md-12">
                              <div class="form-group form-inline">
                                <div class="col-md-12">
                                  <div class="form-group form-inline sf-card-group" id="sf-payment-options">
                                    <?php
									$payflag = 0;
                                    if(service_finder_get_payment_goes_to() == 'admin')
									{
										$paymentmethods = service_finder_get_payment_methods();
										
										if(!empty($paymentmethods))
										{
											foreach($paymentmethods as $key => $paymentmethod)
											{
												if($paymentmethod == 1)
												{
												$payflag = 1;
												?>
												<div class="radio sf-radio-checkbox">
                                                  <input type="radio" value="<?php echo esc_attr($key); ?>" name="bookingpayment_mode" id="paymentvia<?php echo esc_attr($key); ?>" >
                                                  <label for="paymentvia<?php echo esc_attr($key); ?>"><?php echo '<img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/'.$key.'.jpg" title="'.esc_attr(ucfirst($key)).'" alt="'.esc_attr(ucfirst($key)).'">'; ?></label>
                                                </div>
												<?php
												}
											}
										}
										
									}
									?>
                                    <?php 
                                    echo service_finder_add_wallet_option('bookingpayment_mode','paymentvia');
                                    echo service_finder_add_skip_option('bookingpayment_mode','paymentvia');
                                    ?>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div id="bookingcardinfo" class="default-hidden">
                              <div class="col-md-8">
                                <div class="form-group">
                                  <label>
                                  <?php esc_html_e('Card Number', 'service-finder'); ?>
                                  </label>
                                  <div class="input-group"> <i class="input-group-addon fa fa-credit-card"></i>
                                    <input type="text" id="card_number" name="card_number" class="form-control sf-form-control">
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-4">
                                <div class="form-group">
                                  <label>
                                  <?php esc_html_e('CVC', 'service-finder'); ?>
                                  </label>
                                  <div class="input-group"> <i class="input-group-addon fa fa-ellipsis-h"></i>
                                    <input type="text" id="card_cvc" name="card_cvc" class="form-control sf-form-control">
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group has-select">
                                  <label>
                                  <?php esc_html_e('Select Month', 'service-finder'); ?>
                                  </label>
                                  <select id="card_month" name="card_month" class="form-control sf-form-control sf-select-box" title="Select Month">
                                      <option value="1"><?php echo esc_html__('January', 'service-finder') ?></option>
                                      <option value="2"><?php echo esc_html__('February', 'service-finder')?></option>
                                      <option value="3"><?php echo esc_html__('March', 'service-finder')?></option>
                                      <option value="4"><?php echo esc_html__('April', 'service-finder')?></option>
                                      <option value="5"><?php echo esc_html__('May', 'service-finder')?></option>
                                      <option value="6"><?php echo esc_html__('June', 'service-finder')?></option>
                                      <option value="7"><?php echo esc_html__('July', 'service-finder')?></option>
                                      <option value="8"><?php echo esc_html__('August', 'service-finder')?></option>
                                      <option value="9"><?php echo esc_html__('September', 'service-finder')?></option>
                                      <option value="10"><?php echo esc_html__('October', 'service-finder')?></option>
                                      <option value="11"><?php echo esc_html__('November', 'service-finder')?></option>
                                      <option value="12"><?php echo esc_html__('December', 'service-finder')?></option>
                                  </select>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group has-select">
                                  <label>
                                  <?php esc_html_e('Select Year', 'service-finder'); ?>
                                  </label>
                                  <select id="card_year" name="card_year" class="form-control sf-form-control sf-select-box"  title="Select Year">
                                    <?php
                                                                $year = date('Y');
                                                                for($i = $year;$i<=$year+50;$i++){
                                                                    echo '<option value="'.esc_attr($i).'">'.$i.'</option>';
                                                                }
                                                                ?>
                                  </select>
                                </div>
                              </div>
                            </div>
                            <div id="bookingtwocheckoutcardinfo" class="default-hidden">
                                <div class="col-md-8">
                                  <div class="form-group">
                                    <label>
                                    <?php esc_html_e('Card Number', 'service-finder'); ?>
                                    </label>
                                    <div class="input-group"> <i class="input-group-addon fa fa-credit-card"></i>
                                      <input type="text" id="twocheckout_card_number" name="twocheckout_card_number" class="form-control sf-form-control">
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-4">
                                  <div class="form-group">
                                    <label>
                                    <?php esc_html_e('CVC', 'service-finder'); ?>
                                    </label>
                                    <div class="input-group"> <i class="input-group-addon fa fa-ellipsis-h"></i>
                                      <input type="text" id="twocheckout_card_cvc" name="twocheckout_card_cvc" class="form-control sf-form-control">
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-select">
                                    <label>
                                    <?php esc_html_e('Select Month', 'service-finder'); ?>
                                    </label>
                                    <select id="twocheckout_card_month" name="twocheckout_card_month" class="form-control sf-form-control sf-select-box" title="Select Month">
                                      <option value="1"><?php echo esc_html__('January', 'service-finder') ?></option>
                                      <option value="2"><?php echo esc_html__('February', 'service-finder')?></option>
                                      <option value="3"><?php echo esc_html__('March', 'service-finder')?></option>
                                      <option value="4"><?php echo esc_html__('April', 'service-finder')?></option>
                                      <option value="5"><?php echo esc_html__('May', 'service-finder')?></option>
                                      <option value="6"><?php echo esc_html__('June', 'service-finder')?></option>
                                      <option value="7"><?php echo esc_html__('July', 'service-finder')?></option>
                                      <option value="8"><?php echo esc_html__('August', 'service-finder')?></option>
                                      <option value="9"><?php echo esc_html__('September', 'service-finder')?></option>
                                      <option value="10"><?php echo esc_html__('October', 'service-finder')?></option>
                                      <option value="11"><?php echo esc_html__('November', 'service-finder')?></option>
                                      <option value="12"><?php echo esc_html__('December', 'service-finder')?></option>
                                    </select>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-select">
                                    <label>
                                    <?php esc_html_e('Select Year', 'service-finder'); ?>
                                    </label>
                                    <select id="twocheckout_card_year" name="twocheckout_card_year" class="form-control sf-form-control sf-select-box"  title="Select Year">
                                      <?php
                                                                $year = date('Y');
                                                                for($i = $year;$i<=$year+50;$i++){
                                                                    echo '<option value="'.$i.'">'.$i.'</option>';
                                                                }
                                                                ?>
                                    </select>
                                  </div>
                                </div>
                              </div>
                            <div id="bookingpayulatamcardinfo" class="default-hidden">
                                <div class="col-md-12">
                                  <div class="form-group">
                                    <label>
                                    <?php esc_html_e('Select Card', 'service-finder'); ?>
                                    </label>
                                    <select id="payulatam_cardtype" name="payulatam_cardtype" class="form-control sf-form-control sf-select-box"  title="<?php esc_html_e('Select Card', 'service-finder'); ?>">
                                      <?php
                                      $country = (isset($service_finder_options['payulatam-country'])) ? $service_finder_options['payulatam-country'] : '';
                                      $cards = service_finder_get_cards($country);
                                      foreach($cards as $card){
                                        echo '<option value="'.esc_attr($card).'">'.$card.'</option>';
                                      }
                                                                
                                                                ?>
                                    </select>
                                  </div>
                                </div>
                                <div class="col-md-8">
                                  <div class="form-group">
                                    <label>
                                    <?php esc_html_e('Card Number', 'service-finder'); ?>
                                    </label>
                                    <div class="input-group"> <i class="input-group-addon fa fa-credit-card"></i>
                                      <input type="text" id="payulatam_card_number" name="payulatam_card_number" class="form-control sf-form-control">
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-4">
                                  <div class="form-group">
                                    <label>
                                    <?php esc_html_e('CVC', 'service-finder'); ?>
                                    </label>
                                    <div class="input-group"> <i class="input-group-addon fa fa-ellipsis-h"></i>
                                      <input type="text" id="payulatam_card_cvc" name="payulatam_card_cvc" class="form-control sf-form-control">
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-select">
                                    <label>
                                    <?php esc_html_e('Select Month', 'service-finder'); ?>
                                    </label>
                                    <select id="payulatam_card_month" name="payulatam_card_month" class="form-control sf-form-control sf-select-box" title="<?php esc_html_e('Select Month', 'service-finder'); ?>">
                                      <option value="01"><?php echo esc_html__('January', 'service-finder') ?></option>
                                      <option value="02"><?php echo esc_html__('February', 'service-finder')?></option>
                                      <option value="03"><?php echo esc_html__('March', 'service-finder')?></option>
                                      <option value="04"><?php echo esc_html__('April', 'service-finder')?></option>
                                      <option value="05"><?php echo esc_html__('May', 'service-finder')?></option>
                                      <option value="06"><?php echo esc_html__('June', 'service-finder')?></option>
                                      <option value="07"><?php echo esc_html__('July', 'service-finder')?></option>
                                      <option value="08"><?php echo esc_html__('August', 'service-finder')?></option>
                                      <option value="09"><?php echo esc_html__('September', 'service-finder')?></option>
                                      <option value="10"><?php echo esc_html__('October', 'service-finder')?></option>
                                      <option value="11"><?php echo esc_html__('November', 'service-finder')?></option>
                                      <option value="12"><?php echo esc_html__('December', 'service-finder')?></option>
                                    </select>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group has-select">
                                    <label>
                                    <?php esc_html_e('Select Year', 'service-finder'); ?>
                                    </label>
                                    <select id="payulatam_card_year" name="payulatam_card_year" class="form-control sf-form-control sf-select-box"  title="<?php esc_html_e('Select Year', 'service-finder'); ?>">
                                      <?php
                                                                $year = date('Y');
                                                                for($i = $year;$i<=$year+50;$i++){
                                                                    echo '<option value="'.$i.'">'.$i.'</option>';
                                                                }
                                                                ?>
                                    </select>
                                  </div>
                                </div>
                              </div>
                            <div id="wiredinfo" class="default-hidden">
                                <div class="col-md-12">
                                    <?php
                                    $pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? $service_finder_options['pay_booking_amount_to'] : '';
                                    if($pay_booking_amount_to == 'admin'){
                                    $description = (!empty($service_finder_options['wire-transfer-description'])) ? $service_finder_options['wire-transfer-description'] : '';
                                    echo $description;
                                    }elseif($pay_booking_amount_to == 'provider'){
                                    echo (!empty($settings['wired_description'])) ? $settings['wired_description'] : '';
                                    }
                                    ?>
                                </div>
                              </div>
                            <?php if($payflag == 1 || service_finder_check_wallet_system() || service_finder_get_payment_goes_to() == 'provider'){ ?>  
                            <div class="col-md-12" id="sf-bookform-submitarea">
                              <div class="form-group">
                                <input name="book-now" id="save-booking" type="submit" value="<?php esc_html_e('Pay Now', 'service-finder'); ?>" class="btn btn-primary center-block">
                              </div>
                            </div>
                            <?php }else{
                            echo '<p>';
                            echo esc_html__('There is no payment method available.','service-finder');
                            echo '</p>';
                            } ?>
                          </div> 
                    </div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
	<div class="booking-panel-overlay"></div> 
    
    <div class="sf-summery-box" id="bookingsmry" style="display:none">
            <button type="button" class="sf-suumery-close">
                <i class="fa fa-expand"></i>
            </button>
            <div class="sf-summery-total">
                <span class="sf-sum-cel-one"><?php esc_html_e('Total Amount', 'service-finder'); ?></span>
                <span class="sf-sum-cel-four" id="smrytotalamount"></span>
            </div>
            <div class="sf-summery-inr">
                <ul id="summarywrap">
                </ul>
            </div>
        </div>
  <?php } ?>
</form>
