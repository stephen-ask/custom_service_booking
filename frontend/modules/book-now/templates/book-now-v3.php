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
$wpdb = service_finder_plugin_global_vars('wpdb');
$service_finder_Params = service_finder_plugin_global_vars('service_finder_Params');

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

if(!empty($userCap)){
$capability = '';
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

wp_add_inline_script( 'service_finder-js-booking-form-v2', '/*Declare global variable*/
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

wp_add_inline_script( 'service_finder-js-booking-form-free-v2', '/*Declare global variable*/
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

wp_add_inline_script( 'google-map', 'jQuery(function() {
/*Autofill address script by google 1st step*/
function service_finder_initBookingAutoComplete(){
			var address = document.getElementById("booking-location");
			var my_address = new google.maps.places.Autocomplete(address);
	
			google.maps.event.addListener(my_address, "place_changed", function() {
		var place = my_address.getPlace();
		
		// if no location is found
		if (!place.geometry) {
			return;
		}
		if(booking_basedon == "zipcode"){
		var $zipcode = jQuery("#zipcode");
		
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
				} else if (ty[k] === "postal_code") {
					$zipcode.val(address_component.short_name);
					//jQuery(".book-now").bootstrapValidator("revalidateField", "zipcode");
				}
			}
		}
		
		var address = jQuery("#booking-location").val();
		var new_address = address.replace(city,"");
		new_address = new_address.replace(statename,"");
		
		new_address = new_address.replace(country_long_name,"");
		new_address = new_address.replace(country_short_name,"");
		new_address = jQuery.trim(new_address);
		
		
		new_address = new_address.replace(/,/g, "");
		new_address = new_address.replace(/ +/g," ");
		jQuery("#booking-location").val(address);
		jQuery("#booking-address").val(address);
		jQuery("#booking-location").change();
		}else if(booking_basedon == "open"){
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
						//jQuery(".book-now").bootstrapValidator("revalidateField", "state");
						var statename = address_component.long_name;
					} else if(ty[k] === "country"){
						country_long_name = address_component.long_name;
						country_short_name = address_component.short_name;
						$country.val(address_component.long_name);
						//jQuery(".book-now").bootstrapValidator("revalidateField", "country");
					}
				}
			}
			
			var address = jQuery("#booking-location").val();
			var new_address = address.replace(cityname,"");
			new_address = new_address.replace(statename,"");
			
			new_address = new_address.replace(country_long_name,"");
			new_address = new_address.replace(country_short_name,"");
			new_address = jQuery.trim(new_address);
			
			
			new_address = new_address.replace(/,/g, "");
			new_address = new_address.replace(/ +/g," ");
			jQuery("#booking-location").val(address);
			jQuery("#booking-address").val(address);
		}
		
		
	
	 });
		}
if (jQuery("#booking-location").length && siteautosuggestion == true){		
google.maps.event.addDomListener(window, "load", service_finder_initBookingAutoComplete);
}

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

<div id="rootwizard" class="form-wizard sf-profile-layout-3">
  <!--Book Now Form Template Start Version 2-->
  <form class="book-now" method="post">
    <!--navbar starts -->
    <?php 
	$step = 1;
	?>
    <div class="form-nav">
      <ul class="nav nav-pills nav-justified steps">
        <li><a href="#step1" data-toggle="tab">
          <?php echo (!empty($service_finder_options['label-task-location'])) ? esc_html($service_finder_options['label-task-location']) : esc_html__('Choose Service', 'service-finder'); ?>
          </a></li>
        <?php if(service_finder_booking_date_method($author) != 'multidate' || service_finder_is_job_author($jobid,$jobauthor) || service_finder_is_quotation_author($quoteid,$quoteauthor)){ ?>
        <li><a href="#step2" data-toggle="tab">
          <?php echo (!empty($service_finder_options['label-date-time'])) ? esc_html($service_finder_options['label-date-time']) : esc_html__('Date & Time', 'service-finder'); ?>
          </a></li>
        <?php } ?>  
        <li><a href="#step3" data-toggle="tab">
          <?php echo (!empty($service_finder_options['label-customer-info'])) ? esc_html($service_finder_options['label-customer-info']) : esc_html__('Customer info', 'service-finder'); ?>
          </a></li>
        <?php if(service_finder_is_booking_free_paid($author) == 'paid'){ ?>
        <li><a href="#step4" data-toggle="tab">
          <?php echo (!empty($service_finder_options['label-payment'])) ? esc_html($service_finder_options['label-payment']) : esc_html__('Payment', 'service-finder'); ?>
          </a></li>
        <?php } ?>
      </ul>
    </div>
    <!--navbar ends -->
    
    <!-- progress bar for step1 ends -->
    <!-- form wizard content starts -->
    <div class="tab-content">
      <!-- wizard step 1 starts-->
      <div class="tab-pane" id="step1">
        <div class="padding-40 tab-pane-inr clearfix sf-custom-inputbox">
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
		  <?php if($settings['booking_basedon'] == 'zipcode'){ 
		  if($service_perform != 'provider_location' || !$service_finder_options['show-address-info']){
		  ?>
          <div class="col-md-6" id="bookingcustomerlocation">
            <div class="form-group">
              <label>
              <?php esc_html_e('Enter Location', 'service-finder'); ?>
              </label>
              <div class="input-group"> <i class="input-group-addon fa fa-location-arrow"></i>
                <input id="booking-location" name="location" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo['address'])) ? esc_attr($userInfo['address']) : ''; ?>">
              </div>
            </div>
          </div>
          <?php }
		  ?>
          <div class="col-md-6" id="bookingcustomerzipcode">
            <div class="form-group sf-zipcode-area">
              <label>
              <?php esc_html_e('Enter Your Zip code', 'service-finder'); ?>
              </label>
              <div class="input-group"> <i class="input-group-addon fa fa-map-marker"></i>
                <input id="zipcode"  name="zipcode" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo['zipcode'])) ? esc_attr($userInfo['zipcode']) : ''; ?>">
              </div>
            </div>
          </div>
          <?php }elseif($settings['booking_basedon'] == 'region'){ ?>
          <div class="col-md-6">
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
          <?php }elseif($settings['booking_basedon'] == 'open' && ($service_perform != 'provider_location' || !$service_finder_options['show-address-info'])){ ?>
            <div class="col-md-12" id="bookingcustomerlocation">
              <div class="form-group">
                <label>
                <?php esc_html_e('Enter Location', 'service-finder'); ?>
                </label>
                <div class="input-group"> <i class="input-group-addon fa fa-location-arrow"></i>
                  <input id="booking-location" name="location" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo['address'])) ? esc_attr($userInfo['address']) : ''; ?>">
                </div>
              </div>
            </div>
          <?php }?>
        </div>
        <?php 
		if(service_finder_booking_date_method($author) == 'multidate'){
			if(!service_finder_is_job_author($jobid,$jobauthor) && !service_finder_is_quotation_author($quoteid,$quoteauthor)){ ?>
          <div id="bookingservices" style=" <?php echo ($settings['booking_basedon'] == 'block') ? 'display:none;' : ''; ?>">
          <div class="padding-40 tab-pane-inr tab-service-area clearfix equal-col-outer">
		  <?php do_action('service_finder_multidate_services',$author,2); ?>
          </div>
          </div>
          <?php }
		}else{
		if(!service_finder_is_job_author($jobid,$jobauthor) && !service_finder_is_quotation_author($quoteid,$quoteauthor)){ ?>
          <div id="bookingservices" style=" <?php echo ($settings['booking_basedon'] == 'block') ? 'display:none;' : ''; ?>">
          <div class="padding-40 tab-pane-inr tab-service-area clearfix equal-col-outer">
		  <?php do_action('service_finder_singledate_services',$author,2); ?>
          </div>
          </div>
          <?php } 
		}  
		?>
      </div>
      <!-- wizard step 1 ends-->
      <!-- wizard step 2 starts-->
      <?php if(service_finder_booking_date_method($author) != 'multidate' || service_finder_is_job_author($jobid,$jobauthor) || service_finder_is_quotation_author($quoteid,$quoteauthor)){ ?>
      <div class="tab-pane" id="step2">
        <div class="padding-40 tab-pane-inr clearfix">
          <div class="col-md-12">
            <div id="my-calendar"></div>
          </div>
          <div class="col-md-12">
            <ul class="indiget-booking">
              <li class="allbooked"><b></b>
                <?php esc_html_e('All Booked', 'service-finder'); ?>
              </li>
              <li class="unavailable"><b></b>
                <?php esc_html_e('Unavailable', 'service-finder'); ?>
              </li>
            </ul>
          </div>
          <?php
			if(!empty($userCap)){
			if(in_array('availability',$userCap) && in_array('bookings',$userCap)){
			?>
          <div class="col-md-12">
            <ul class="timeslots timelist list-inline">
              <span class="notavail">
                <?php esc_html_e('Please select date to show timeslot.', 'service-finder'); ?>
              </span>
            </ul>
          </div>
          <?php 
										}
										}
										?>
          <?php
                                        if(!empty($userCap)){
										if(in_array('staff-members',$userCap) && in_array('bookings',$userCap)){
										if($settings['members_available'] == 'yes'){
										?>
          <div class="staff-member clear">
            <div class="col-md-12" id="members"> </div>
          </div>
          <?php 
										}
										}
										} 
										?>
        </div>
      </div>
      <?php } ?>
      <!-- wizard step 2 ends-->
      <!-- wizard step 3 starts-->
      <div class="tab-pane" id="step3">
        <div class="padding-40 tab-pane-inr clearfix">
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
          <div class=" <?php echo ($show_booking_otp) ? 'col-md-6' : 'col-md-12'; ?>">
            <div class="form-group">
              <label>
              <?php esc_html_e('Email', 'service-finder'); ?>
              </label>
              <div class="input-group"> <i class="input-group-addon fa fa-envelope"></i>
                <input id="email" name="email" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo[0]->user_email)) ? esc_attr($userInfo[0]->user_email) : ''; ?>">
              </div>
            </div>
          </div>
          <?php if($show_booking_otp){ ?>
          <div class="col-md-6">
            <div class="form-group otp-section">
              <label>
              <?php esc_html_e('One Time Password', 'service-finder'); ?>
              </label>
              <div class="input-group"> <i class="input-group-addon fa fa-lock"></i>
                <input id="fillotp" name="fillotp" type="text" class="form-control sf-form-control" value="">
              </div>
              <a href="javascript:;" class="otp">
              <?php esc_html_e('Generate One time Password to Confirm Email', 'service-finder'); ?>
              </a> </div>
          </div>
          <?php } ?>
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
                <textarea id="shortdesc" name="shortdesc" class="form-control sf-form-control" placeholder="<?php esc_html_e('Please insert short description of your task', 'service-finder'); ?>"><?php if(is_user_logged_in() && service_finder_getUserRole($current_user->ID) == 'Customer' && $jobid > 0 && $jobauthor == $current_user->ID) {
			   echo strip_tags($jobpost->post_content); 
			   }elseif(service_finder_is_quotation_author($quoteid,$quoteauthor)){
			   echo strip_tags(service_finder_get_quote_reply($quoteid,$author));
			   } ?></textarea>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- wizard step 3 ends-->
      <?php 
	  $checkpaypal = false;
	  $checkwired = false;
	  $checktwocheckout = false;
	  $checkstripe = false;
	  $checkpayumoney = false;
	  $checkcod = false;
	  if(service_finder_is_booking_free_paid($author) == 'paid'){ 
	  ?>
      <!-- wizard step 4 starts-->
      <div class="tab-pane" id="step4">
        <div class="padding-40 tab-pane-inr clearfix">
          <?php echo service_finder_multidate_coupon_code($author); ?> 
		  <?php echo $showadminfee; ?>
          <?php echo service_finder_display_wallet_amount($current_user->ID); ?> 
          <div class="col-md-12">
            <div class="form-group form-inline">
              <div class="col-md-12">
                <div class="form-group form-inline sf-card-group">
                  <?php  
				  if($pay_booking_amount_to == 'admin'){
				  	if($payment_methods['paypal']){
						$checkpaypal = true;
					}else{
						$checkpaypal = false;
					}
				  }elseif($pay_booking_amount_to == 'provider'){
				  	if(!empty($settings['paymentoption'])){
					if(in_array('paypal',$settings['paymentoption'])){
						$checkpaypal = true;
					}else{
						$checkpaypal = false;
					}
					}else{
						$checkpaypal = false;
					}
				  }
				  
				  if($checkpaypal){ 
				  ?>
                  <div class="radio sf-radio-checkbox">
                    <input type="radio" value="paypal" name="bookingpayment_mode" id="paymentviapaypal" >
                    <label for="paymentviapaypal"><?php echo '<img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/paypal.jpg" title="'.esc_html__('Paypal','service-finder').'" alt="'.esc_html__('paypal','service-finder').'">'; ?></label>
                  </div>
                  <?php } ?>
                  <?php  
				  if($pay_booking_amount_to == 'admin'){
				  	if($payment_methods['stripe']){
						$checkstripe = true;
					}else{
						$checkstripe = false;
					}
				  }elseif($pay_booking_amount_to == 'provider'){
				  	if(!empty($settings['paymentoption'])){
					if(in_array('stripe',$settings['paymentoption'])){
						$checkstripe = true;
					}else{
						$checkstripe = false;
					}
					}else{
						$checkstripe = false;
					}
				  }
				  
				  if($checkstripe){
				  ?>
                  <div class="radio sf-radio-checkbox">
                    <input type="radio" value="stripe" name="bookingpayment_mode" id="paymentviastripe">
                    <label for="paymentviastripe"><?php echo '<img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/mastercard.jpg" title="'.esc_html__('Stripe','service-finder').'" alt="'.esc_html__('mastercard','service-finder').'"><img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/payment.jpg" title="'.esc_html__('Stripe','service-finder').'" alt="'.esc_html__('american express','service-finder').'"><img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/discover.jpg" title="'.esc_html__('Stripe','service-finder').'" alt="'.esc_html__('discover','service-finder').'"><img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/visa.jpg" title="'.esc_html__('Stripe','service-finder').'" alt="'.esc_html__('visa','service-finder').'">'; ?></label>
                  </div>
                  <?php } ?>
                  
                   <?php  
				   $checktwocheckout = '';
				  if($pay_booking_amount_to == 'admin'){
				  	if(isset($payment_methods['twocheckout'])){
					if($payment_methods['twocheckout']){
						$checktwocheckout = true;
					}else{
						$checktwocheckout = false;
					}
					}else{
						$checktwocheckout = false;
					}
				  }elseif($pay_booking_amount_to == 'provider'){
				  	if(!empty($settings['paymentoption'])){
					if(in_array('twocheckout',$settings['paymentoption'])){
						$checktwocheckout = true;
					}else{
						$checktwocheckout = false;
					}
					}else{
						$checktwocheckout = false;
					}
				  }
				  
				  if($checktwocheckout){
				  ?>
                  <div class="radio sf-radio-checkbox">
                    <input type="radio" value="twocheckout" name="bookingpayment_mode" id="paymentviatwocheckout">
                    <label for="paymentviatwocheckout"><?php echo '<img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/twocheckout.jpg" alt="'.esc_html__('2Checkout','service-finder').'">'; ?></label>
                  </div>
                  <?php } ?>
                  
                  <?php  
				  if($pay_booking_amount_to == 'admin'){
				  	if($payment_methods['wired']){
						$checkwired = true;
					}else{
						$checkwired = false;
					}
				  }elseif($pay_booking_amount_to == 'provider'){
				  	if(!empty($settings['paymentoption'])){
					if(in_array('wired',$settings['paymentoption'])){
						$checkwired = true;
					}else{
						$checkwired = false;
					}
					}else{
						$checkwired = false;
					}
				  }
				  
				  if($checkwired){
				  ?>
                  <div class="radio sf-radio-checkbox">
                    <input type="radio" value="wired" name="bookingpayment_mode" id="paymentviawired">
                    <label for="paymentviawired"><?php echo '<img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/wired.jpg" title="'.esc_html__('Wire Transfer','service-finder').'" alt="'.esc_html__('Wire Transfer','service-finder').'">'; ?></label>
                  </div>
                  <?php } ?>
                  
                  <?php  
				  if($pay_booking_amount_to == 'admin'){
				  	if($payment_methods['payumoney']){
						$checkpayumoney = true;
					}else{
						$checkpayumoney = false;
					}
				  }elseif($pay_booking_amount_to == 'provider'){
				  	if(!empty($settings['paymentoption'])){
					if(in_array('payumoney',$settings['paymentoption'])){
						$checkpayumoney = true;
					}else{
						$checkpayumoney = false;
					}
					}else{
						$checkpayumoney = false;
					}
				  }
				  
				  if($checkpayumoney){
				  ?>
                  <div class="radio sf-radio-checkbox">
                    <input type="radio" value="payumoney" name="bookingpayment_mode" id="paymentviapayumoney">
                    <label for="paymentviapayumoney"><?php echo '<img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/payumoney.jpg" title="'.esc_html__('PayU Money','service-finder').'" alt="'.esc_html__('PayU Money','service-finder').'">'; ?></label>
                  </div>
                  <?php } ?>
                  
                  <?php  
				  if($pay_booking_amount_to == 'admin'){
				  	if($payment_methods['payulatam']){
						$checkpayulatam = true;
					}else{
						$checkpayulatam = false;
					}
				  }elseif($pay_booking_amount_to == 'provider'){
				  	if(!empty($settings['paymentoption'])){
					if(in_array('payulatam',$settings['paymentoption'])){
						$checkpayulatam = true;
					}else{
						$checkpayulatam = false;
					}
					}else{
						$checkpayulatam = false;
					}
				  }
				  
				  if($checkpayulatam){
				  ?>
                  <div class="radio sf-radio-checkbox">
                    <input type="radio" value="payulatam" name="bookingpayment_mode" id="paymentviapayulatam">
                    <label for="paymentviapayulatam"><?php echo '<img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/payulatam.jpg" title="'.esc_html__('PayU Latam','service-finder').'" alt="'.esc_html__('PayU Latam','service-finder').'">'; ?></label>
                  </div>
                  <?php } ?>
                  <?php  
				  if($pay_booking_amount_to == 'admin'){
				  	if($payment_methods['cod']){
						$checkcod = true;
					}else{
						$checkcod = false;
					}
				  }elseif($pay_booking_amount_to == 'provider'){
				  	if(!empty($settings['paymentoption'])){
					if(in_array('cod',$settings['paymentoption'])){
						$checkcod = true;
					}else{
						$checkcod = false;
					}
					}else{
						$checkcod = false;
					}
				  }
				  
				  if($checkcod){ 
				  ?>
                <div class="radio sf-radio-checkbox">
                  <input type="radio" value="cod" name="bookingpayment_mode" id="paymentviacod" >
                  <label for="paymentviacod"><?php echo '<img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/cod.jpg" title="'.esc_html__('Cash on Delevery','service-finder').'" alt="'.esc_html__('Cash on Delevery','service-finder').'">'; ?></label>
                </div>
                <?php } ?>
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
												echo '<option value="'.$i.'">'.$i.'</option>';
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
        </div>
      </div>
      <!-- wizard step 4 ends-->
      <?php } ?>
      <!-- wizard prev & next btn starts-->
      <ul class="wizard-actions clearfix wizard">
        <li class="previous first" style="display:none"><a href="javascript:void(0);">
          <?php esc_html_e('First', 'service-finder'); ?>
          </a></li>
        <li class="previous"><a href="javascript:void(0);" class="btn btn-primary pull-left"><i class="fa fa-arrow-left"></i>
          <?php esc_html_e('Previous', 'service-finder'); ?>
          </a></li>
        <li class="next last" style="display:none"><a href="javascript:void(0);">
          <?php esc_html_e('Last', 'service-finder'); ?>
          </a></li>
        <?php if((!is_user_logged_in() && !$service_finder_options['guest-booking'])){ ?>
        <li>
        <a href="javascript:;" class="btn btn-primary  pull-right" data-action="login" data-redirect="no" data-toggle="modal" data-target="#login-Modal">
          <?php esc_html_e('Next', 'service-finder'); ?>
          <i class="fa fa-arrow-right"></i></a>
          </li>
          <?php }else{ ?>  
          <li class="next" id="submitlink">
	        <a href="javascript:void(0);" class="btn btn-primary  pull-right">
			  <?php esc_html_e('Next', 'service-finder'); ?>
              <i class="fa fa-arrow-right"></i></a>
              </li>
          <?php } ?>
            
        <li id="submitbtn" style="display:none">
        <?php if($checkpaypal || $checkwired || $checktwocheckout || $checkstripe || $checkpayumoney || $checkcod || service_finder_check_wallet_system()){ ?>
          <input name="book-now" id="save-booking" type="submit" value="<?php esc_html_e('Pay Now', 'service-finder'); ?>" class="btn btn-primary  pull-right">
        <?php }else{
		echo '<p>';
		echo esc_html__('There is no payment method available.','service-finder');
		echo '</p>';
		} ?>  
        </li>
        
      </ul>
      <!-- wizard prev & next btn ends-->
    </div>
    <!-- form wizard content end -->
    <input type="hidden" id="provider" name="provider" data-provider="<?php echo esc_attr($author) ?>" value="<?php echo esc_attr($author) ?>" />
    <input type="hidden" id="jobid" name="jobid" value="<?php echo esc_attr($jobid) ?>" />
    <input type="hidden" id="quoteid" name="quoteid" value="<?php echo esc_attr($quoteid) ?>" />
    <input type="hidden" id="boking-slot" data-slot="" name="boking-slot" value="" />
    <input type="hidden" id="memberid" data-memid="" name="memberid" value="" />
    <input type="hidden" id="totalcost" name="totalcost" value="" />
    <input type="hidden" id="totaldiscount" name="totaldiscount" value="" />
    <input type="hidden" id="servicearr" name="servicearr" value="" />
    <input type="hidden" id="selecteddate" value="" data-seldate="" name="selecteddate" />
  </form>
  <!--Book Now Form Template Start Version 2-->
</div>
