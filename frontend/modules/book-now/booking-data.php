<?php
ob_start();
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/

$wpdb = service_finder_plugin_global_vars('wpdb');
$service_finder_Params = service_finder_plugin_global_vars('service_finder_Params');

/*Boking via paypal*/
if(isset($_POST['bookingpayment_mode']) && ($_POST['bookingpayment_mode'] == 'paypal')){
$service_finder_options = get_option('service_finder_options');

$paypalmethod = (!empty($service_finder_options['paypal-method'])) ? $service_finder_options['paypal-method'] : '';

require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/book-now/express-checkout.php';
}

// check token (paypal merchant authorization) and Do Payment
if(isset($_GET['booking_made']) && ($_GET['booking_made'] == 'success') && !empty($_GET['token'])) {

$service_finder_options = get_option('service_finder_options');
$wpdb = service_finder_plugin_global_vars('wpdb');
$service_finder_Tables = service_finder_plugin_global_vars('service_finder_Tables');

$paypaltoken = (!empty($_GET['token'])) ? esc_html($_GET['token']) : '';

$bookingdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `paypal_token` = "%s"',$paypaltoken),ARRAY_A);

$settings = service_finder_getProviderSettings($bookingdata['provider_id']);
/*Initialize Paypal Credentials*/
$creds = array();
$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? esc_html($service_finder_options['pay_booking_amount_to']) : '';
if($pay_booking_amount_to == 'admin'){

	$paypalusername = (!empty($service_finder_options['paypal-username'])) ? $service_finder_options['paypal-username'] : '';
	$paypalpassword = (!empty($service_finder_options['paypal-password'])) ? $service_finder_options['paypal-password'] : '';
	$paypalsignatue = (!empty($service_finder_options['paypal-signatue'])) ? $service_finder_options['paypal-signatue'] : '';

}elseif($pay_booking_amount_to == 'provider'){

	$paypalusername = (isset($settings['paypalusername'])) ? $settings['paypalusername'] : '';
	$paypalpassword = (isset($settings['paypalpassword'])) ? $settings['paypalpassword'] : '';
	$paypalsignatue = (isset($settings['paypalsignatue'])) ? $settings['paypalsignatue'] : '';

}

$paypalCreds['USER'] = esc_html($paypalusername);
$paypalCreds['PWD'] = esc_html($paypalpassword);
$paypalCreds['SIGNATURE'] = esc_html($paypalsignatue);

$sandbox = (isset($service_finder_options['paypal-type']) && $service_finder_options['paypal-type'] == 'live') ? '' : 'sandbox.';
$paypalType = (isset($service_finder_options['paypal-type']) && $service_finder_options['paypal-type'] == 'live') ? '' : 'sandbox.';

$paypalTypeBool = (!empty($paypalType)) ? true : false;

$paypal = new Paypal($paypalCreds,$paypalTypeBool);

	$checkoutDetails = $paypal->request('GetExpressCheckoutDetails', array('TOKEN' => $_GET['token']));
	if( is_array($checkoutDetails) && ($checkoutDetails['ACK'] == 'Success') ) {
				//  Single payment
				$params = array(
					'TOKEN' => $checkoutDetails['TOKEN'],
					'PAYERID' => $checkoutDetails['PAYERID'],
					'PAYMENTACTION' => 'Sale',
					'PAYMENTREQUEST_0_AMT' => $checkoutDetails['PAYMENTREQUEST_0_AMT'], // Same amount as in the original request
					'PAYMENTREQUEST_0_CURRENCYCODE' => $checkoutDetails['CURRENCYCODE'] // Same currency as the original request
				);
				$singlePayment = $paypal -> request('DoExpressCheckoutPayment',$params);
				// IF PAYMENT OK
				if( is_array($singlePayment) && $singlePayment['ACK'] == 'Success') {
					require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/book-now/BookNow.php';
					// We'll fetch the transaction ID for internal bookkeeping
					$transactionId = $singlePayment['PAYMENTINFO_0_TRANSACTIONID'];
					
					$bookdata = array(
							'status' => 'Pending',
							'txnid' => $transactionId,
							);
					
					$where = array(
							'paypal_token' => $paypaltoken, 
							);
					$wpdb->update($service_finder_Tables->bookings,$bookdata,$where);
					
					$senMail = new SERVICE_FINDER_BookNow();
					
					
					$senMail->service_finder_SendBookingMailToProvider($bookingdata,'',$bookingdata['adminfee']);
					$senMail->service_finder_SendBookingMailToCustomer($bookingdata,'',$bookingdata['adminfee']);
					$senMail->service_finder_SendBookingMailToAdmin($bookingdata,'',$bookingdata['adminfee']);
					
					
					
					$userLink = service_finder_get_author_url($bookingdata['provider_id']);
					$redirectOption = $service_finder_options['redirect-option'];
					$redirectURL = (!empty($service_finder_options['thankyou-page-url'])) ? $service_finder_options['thankyou-page-url'] : '';
					if($redirectOption == 'thankyou-page'){
						if($redirectURL != ""){
						$redirect = add_query_arg( array('bookingcompleted' => 'success'), $redirectURL );
						}else{
						$redirect = add_query_arg( array('bookingcompleted' => 'success'), service_finder_get_url_by_shortcode('[service_finder_thank_you]') );
						}
					}else{
					
					$redirect = add_query_arg( array('bookingcompleted' => 'success'), $userLink );
					}
					wp_redirect($redirect);
					die;

				}

		}

}

// delete token and show messages if user cancel payment 
if(isset($_GET['booking_made']) && ($_GET['booking_made'] == 'cancel') && !empty($_GET['token'])){
	// delete token from DB
	$wpdb = service_finder_plugin_global_vars('wpdb');
	$registerErrors = service_finder_plugin_global_vars('registerErrors');
	$token = $_GET['token'];
	$tokenRow = $wpdb->get_row($wpdb->prepare( "SELECT * FROM ".$service_finder_Tables->bookings." WHERE paypal_token = '%s'",$token ));
	if($tokenRow){
		
		$booking_customer_id = $tokenRow->booking_customer_id;
		$wpdb->query( $wpdb->prepare( "DELETE FROM ".$service_finder_Tables->customers." WHERE id = %d", $booking_customer_id ) );
		$wpdb->query( $wpdb->prepare( "DELETE FROM ".$service_finder_Tables->bookings." WHERE paypal_token = '%s'", $token ) );
		$wpdb->query( $wpdb->prepare( "DELETE FROM ".$service_finder_Tables->notifications." WHERE topic = 'Booking' AND target_id = %d", $tokenRow->id ) );
		
		// show message
		$errors = new WP_Error();
		$message = esc_html__("You canceled payment. Your booking wasn't made","aone");
		$errors->add( 'cancel_payment', $message);
		$registerErrors = $errors;
	}	
	
}
/*Booking via paypal END*/

/*Booking via payu money start*/
if(isset($_POST['bookingpayment_mode']) && $_POST['bookingpayment_mode'] == 'payumoney'){
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/book-now/BookNow.php';

$service_finder_options = get_option('service_finder_options');
$service_finder_Tables = service_finder_plugin_global_vars('service_finder_Tables');
$service_finder_Errors = service_finder_plugin_global_vars('service_finder_Errors');
$registerErrors = service_finder_plugin_global_vars('registerErrors');
$registerMessages = service_finder_plugin_global_vars('registerMessages');

$provider = isset($_POST['provider']) ? esc_html($_POST['provider']) : '';
$totalcost = isset($_POST['totalcost']) ? esc_html($_POST['totalcost']) : '';
$totaldiscount = (!empty($_POST['totaldiscount'])) ? esc_html($_POST['totaldiscount']) : 0;
		
if(floatval($totalcost) >= floatval($totaldiscount)){
$totalcost = floatval($totalcost) - floatval($totaldiscount);
}else{
$totalcost = floatval($totalcost);
}

$settings = service_finder_getProviderSettings($provider);

$admin_fee_type = (!empty($service_finder_options['admin-fee-type'])) ? $service_finder_options['admin-fee-type'] : 0;
$admin_fee_percentage = (!empty($service_finder_options['admin-fee-percentage'])) ? $service_finder_options['admin-fee-percentage'] : 0;
$admin_fee_fixed = (!empty($service_finder_options['admin-fee-fixed'])) ? $service_finder_options['admin-fee-fixed'] : 0;

$charge_admin_fee = (!empty($service_finder_options['charge-admin-fee'])) ? $service_finder_options['charge-admin-fee'] : '';
$charge_admin_fee_from = (!empty($service_finder_options['charge-admin-fee-from'])) ? $service_finder_options['charge-admin-fee-from'] : '';

$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? esc_html($service_finder_options['pay_booking_amount_to']) : '';

if($charge_admin_fee && $pay_booking_amount_to == 'admin' && (($admin_fee_type == 'fixed' && $admin_fee_fixed > 0) || ($admin_fee_type == 'percentage' && $admin_fee_percentage > 0)) && $charge_admin_fee_from == 'customer'){

	if($admin_fee_type == 'fixed'){
		$adminfee = $admin_fee_fixed;
	}elseif($admin_fee_type == 'percentage'){
		$adminfee = $totalcost * ($admin_fee_percentage/100);	
	}
	
	$totalcost = $totalcost + $adminfee;
}elseif($charge_admin_fee && $pay_booking_amount_to == 'admin' && (($admin_fee_type == 'fixed' && $admin_fee_fixed > 0) || ($admin_fee_type == 'percentage' && $admin_fee_percentage > 0)) && $charge_admin_fee_from == 'provider'){
	if($admin_fee_type == 'fixed'){
		$adminfee = $admin_fee_fixed;
	}elseif($admin_fee_type == 'percentage'){
		$adminfee = $totalcost * ($admin_fee_percentage/100);	
	}
	
}else{
	$adminfee = 0;
}

$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? esc_html($service_finder_options['pay_booking_amount_to']) : '';
if($pay_booking_amount_to == 'admin'){

	if( isset($service_finder_options['payumoney-type']) && $service_finder_options['payumoney-type'] == 'test' ){
		$MERCHANT_KEY = $service_finder_options['payumoney-key-test'];
		$SALT = $service_finder_options['payumoney-salt-test'];
		$PAYU_BASE_URL = "https://test.payu.in";
	}else{
		$MERCHANT_KEY = $service_finder_options['payumoney-key-live'];
		$SALT = $service_finder_options['payumoney-salt-live'];
		$PAYU_BASE_URL = "https://secure.payu.in";
	}

}elseif($pay_booking_amount_to == 'provider'){

		$MERCHANT_KEY = (isset($settings['payumoneykey'])) ? $settings['payumoneykey'] : '';
		$SALT = (isset($settings['payumoneysalt'])) ? $settings['payumoneysalt'] : '';
		
		if( isset($service_finder_options['payumoney-type']) && $service_finder_options['payumoney-type'] == 'test' ){
		$PAYU_BASE_URL = "https://test.payu.in";
		}else{
		$PAYU_BASE_URL = "https://secure.payu.in";
		}
}

$userLink = service_finder_get_author_url($provider);
$surl = add_query_arg( array('booking_made' => 'success','payutransaction' => 'success'), $userLink );
$furl = add_query_arg( array('booking_made' => 'failed','payutransaction' => 'failed'), $userLink );

$txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
$action = $PAYU_BASE_URL . '/_payment';

if(service_finder_has_pay_only_admin_fee() && $adminfee > 0)
{
	$price = $adminfee;
}else
{
	$price = $totalcost;
}


$productinfo = 'Payment for Booking';

$firstname = isset($_POST['firstname']) ? esc_html($_POST['firstname']) : '';
$email = isset($_POST['email']) ? esc_html($_POST['email']) : '';
$phone = isset($_POST['phone']) ? esc_html($_POST['phone']) : '';

$saveBooking = new SERVICE_FINDER_BookNow();
$saveBooking->service_finder_SaveBooking($_POST,'',$txnid,$adminfee);

$str = "$MERCHANT_KEY|$txnid|$price|$productinfo|$firstname|$email|||||||||||$SALT";

$hash = strtolower(hash('sha512', $str));

$payuindia_args = array(
	'key' 			=> $MERCHANT_KEY,
	'hash' 			=> $hash,
	'txnid' 		=> $txnid,
	'amount' 		=> $price,
	'firstname'		=> $firstname,
	'email' 		=> $email,
	'phone'			=> $phone,
	'productinfo'	=> $productinfo,
	'surl' 			=> $surl,
	'furl' 			=> $furl,
	'curl'			=> '',
	'address1' 		=> '',
	'address2' 		=> '',
	'city' 			=> '',
	'state' 		=> '',
	'country' 		=> '',
	'zipcode' 		=> '',
	'curl'			=> '',
	'pg' 			=> '',
	'service_provider'	=> 'payu_paisa'
);
$payuindia_args_array = array();
foreach($payuindia_args as $key => $value){
	$payuindia_args_array[] = "<input type='hidden' name='$key' value='$value'/>";
}

echo '<form action="'.$action.'" method="post" id="payuForm" name="payuForm">
	' . implode('', $payuindia_args_array) . '
	<input type="submit" class="button-alt hidebutton" id="submit_payuindia_payment_form" value="'.esc_html__('Pay via PayU', 'service-finder').'" style="display:none;"/> 
	</form>
	<script>
	document.getElementById("payuForm").submit();
	</script>';
		
				
}

if(isset($_GET['booking_made']) && $_GET['booking_made'] == 'success' && $_GET['payutransaction'] == 'success' && isset($_GET['payutransaction']) && isset($_POST['mihpayid']) && isset($_POST['status'])){
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/book-now/BookNow.php';
$service_finder_options = get_option('service_finder_options');
$wpdb = service_finder_plugin_global_vars('wpdb');
$service_finder_Tables = service_finder_plugin_global_vars('service_finder_Tables');

$txnid = (isset($_POST['txnid'])) ? esc_html($_POST['txnid']) : '';
$payuMoneyId = (isset($_POST['mihpayid'])) ? esc_html($_POST['mihpayid']) : '';
$status = (isset($_POST['status'])) ? esc_html($_POST['status']) : '';

if($status == 'success' && $payuMoneyId != ""){

	$bookingdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `txnid` = "%s"',$txnid),ARRAY_A);

	$bookdata = array(
			'status' => 'Pending',
			'payumoneyid' => $payuMoneyId,
			);
	
	$where = array(
			'txnid' => $txnid, 
			);
	$wpdb->update($service_finder_Tables->bookings,$bookdata,$where);
	
	if(function_exists('service_finder_add_notices')) {
		$multidate = $bookingdata['multi_date'];
		$booking_id = $bookingdata['id'];
		
		$customername = service_finder_getCustomerName($bookingdata['customer_id']);
		
		if($bookingdata['jobid'] == '' && $bookingdata['quoteid'] == ''){
		if($multidate == 'yes'){
		$noticedata = array(
				'provider_id' => $bookingdata['provider_id'],
				'target_id' => $booking_id, 
				'topic' => 'Booking',
				'title' => esc_html__('Booking', 'service-finder'),
				'notice' => sprintf( esc_html__('You have new booking. Booking Ref id is #%d', 'service-finder'), $booking_id ),
				);
		service_finder_add_notices($noticedata);
		}else{
		
		$noticedata = array(
				'provider_id' => $bookingdata['provider_id'],
				'target_id' => $booking_id, 
				'topic' => 'Booking',
				'title' => esc_html__('Booking', 'service-finder'),
				'notice' => sprintf( esc_html__('You have new booking on %s at %s by %s. Booking Ref id is #%d', 'service-finder'), $bookingdata['date'],$bookingdata['start_time'],$customername,$booking_id ),
				);
		service_finder_add_notices($noticedata);
		}
		}else{
		$noticedata = array(
				'provider_id' => $bookingdata['provider_id'],
				'target_id' => $booking_id, 
				'topic' => 'Booking',
				'title' => esc_html__('Booking', 'service-finder'),
				'notice' => sprintf( esc_html__('You have new booking on %s at %s by %s. Booking Ref id is #%d', 'service-finder'), $bookingdata['date'],$bookingdata['start_time'],$customername,$booking_id ),
				);
		service_finder_add_notices($noticedata);
		}
		
	}
	
	$senMail = new SERVICE_FINDER_BookNow();
	
	$senMail->service_finder_SendBookingMailToProvider($bookingdata,'',$bookingdata['adminfee']);
	$senMail->service_finder_SendBookingMailToCustomer($bookingdata,'',$bookingdata['adminfee']);
	$senMail->service_finder_SendBookingMailToAdmin($bookingdata,'',$bookingdata['adminfee']);
	
	
	
	$userLink = service_finder_get_author_url($bookingdata['provider_id']);
	$redirectOption = $service_finder_options['redirect-option'];
	$redirectURL = (!empty($service_finder_options['thankyou-page-url'])) ? $service_finder_options['thankyou-page-url'] : '';
	if($redirectOption == 'thankyou-page'){
		if($redirectURL != ""){
		$redirect = add_query_arg( array('bookingcompleted' => 'success'), $redirectURL );
		}else{
		$redirect = add_query_arg( array('bookingcompleted' => 'success'), service_finder_get_url_by_shortcode('[service_finder_thank_you]') );
		}
	}else{
	
	$redirect = add_query_arg( array('bookingcompleted' => 'success'), $userLink );
	}
	wp_redirect($redirect);
	die;

}

}

if(isset($_GET['booking_made']) && $_GET['booking_made'] == 'failed' && $_GET['payutransaction'] == 'failed'){

	$wpdb = service_finder_plugin_global_vars('wpdb');
	$registerErrors = service_finder_plugin_global_vars('registerErrors');
	$txnid = (isset($_POST['txnid'])) ? esc_html($_POST['txnid']) : '';
	$row = $wpdb->get_row($wpdb->prepare( "SELECT * FROM ".$service_finder_Tables->bookings." WHERE `txnid` = '%s'",$txnid ));
	if($row){
		
		$booking_customer_id = $row->booking_customer_id;
		$wpdb->query( $wpdb->prepare( "DELETE FROM ".$service_finder_Tables->customers." WHERE id = %d", $booking_customer_id ) );
		$wpdb->query( $wpdb->prepare( "DELETE FROM ".$service_finder_Tables->bookings." WHERE txnid = '%s'", $txnid ) );
		
		// show message
		$errors = new WP_Error();
		$message = esc_html__("You canceled payment. Your booking wasn't made","aone");
		$errors->add( 'cancel_payment', $message);
		$registerErrors = $errors;
	}

}
/*Booking via payu money end*/

/*Booking Checkout Process*/
add_action('wp_ajax_twocheckout', 'service_finder_twocheckout');
add_action('wp_ajax_nopriv_twocheckout', 'service_finder_twocheckout');

function service_finder_twocheckout(){
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/book-now/BookNow.php';
global $wpdb, $stripe_options, $service_finder_options, $service_finder_Tables;

$providerid = (!empty($_POST['provider'])) ? esc_html($_POST['provider']) : '';
$token = (!empty($_POST['twocheckouttoken'])) ? esc_html($_POST['twocheckouttoken']) : '';
$totalcost = (!empty($_POST['totalcost'])) ? esc_html($_POST['totalcost']) : '';
$totaldiscount = (!empty($_POST['totaldiscount'])) ? esc_html($_POST['totaldiscount']) : 0;
		
if(floatval($totalcost) >= floatval($totaldiscount)){
$totalcost = floatval($totalcost) - floatval($totaldiscount);
}else{
$totalcost = floatval($totalcost);
}

$admin_fee_type = (!empty($service_finder_options['admin-fee-type'])) ? $service_finder_options['admin-fee-type'] : 0;
$admin_fee_percentage = (!empty($service_finder_options['admin-fee-percentage'])) ? $service_finder_options['admin-fee-percentage'] : 0;
$admin_fee_fixed = (!empty($service_finder_options['admin-fee-fixed'])) ? $service_finder_options['admin-fee-fixed'] : 0;

$charge_admin_fee = (!empty($service_finder_options['charge-admin-fee'])) ? $service_finder_options['charge-admin-fee'] : '';
$charge_admin_fee_from = (!empty($service_finder_options['charge-admin-fee-from'])) ? $service_finder_options['charge-admin-fee-from'] : '';

$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? esc_html($service_finder_options['pay_booking_amount_to']) : '';

if($charge_admin_fee && $pay_booking_amount_to == 'admin' && (($admin_fee_type == 'fixed' && $admin_fee_fixed > 0) || ($admin_fee_type == 'percentage' && $admin_fee_percentage > 0)) && $charge_admin_fee_from == 'customer'){
	if($admin_fee_type == 'fixed'){
		$adminfee = $admin_fee_fixed;
	}elseif($admin_fee_type == 'percentage'){
		$adminfee = $totalcost * ($admin_fee_percentage/100);	
	}

	$totalcost = $totalcost + $adminfee;
}elseif($charge_admin_fee && $pay_booking_amount_to == 'admin' && (($admin_fee_type == 'fixed' && $admin_fee_fixed > 0) || ($admin_fee_type == 'percentage' && $admin_fee_percentage > 0)) && $charge_admin_fee_from == 'provider'){
	
	if($admin_fee_type == 'fixed'){
		$adminfee = $admin_fee_fixed;
	}elseif($admin_fee_type == 'percentage'){
		$adminfee = $totalcost * ($admin_fee_percentage/100);	
	}
	
}else{
	$adminfee = 0;
}

$settings = service_finder_getProviderSettings($providerid);
		
$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? esc_html($service_finder_options['pay_booking_amount_to']) : '';
$twocheckouttype = (!empty($service_finder_options['twocheckout-type'])) ? esc_html($service_finder_options['twocheckout-type']) : '';
if($pay_booking_amount_to == 'admin'){
	if($twocheckouttype == 'live'){
		$private_key = (!empty($service_finder_options['twocheckout-live-private-key'])) ? esc_html($service_finder_options['twocheckout-live-private-key']) : '';
		$twocheckoutaccountid = (!empty($service_finder_options['twocheckout-live-account-id'])) ? esc_html($service_finder_options['twocheckout-live-account-id']) : '';
	}else{
		$private_key = (!empty($service_finder_options['twocheckout-test-private-key'])) ? esc_html($service_finder_options['twocheckout-test-private-key']) : '';
		$twocheckoutaccountid = (!empty($service_finder_options['twocheckout-test-account-id'])) ? esc_html($service_finder_options['twocheckout-test-account-id']) : '';
	}
}elseif($pay_booking_amount_to == 'provider'){
	$private_key = esc_html($settings['twocheckoutprivatekey']);
	$twocheckoutaccountid = esc_html($settings['twocheckoutaccountid']);
}

require_once(SERVICE_FINDER_PAYMENT_GATEWAY_DIR.'/2checkout/lib/Twocheckout.php');
Twocheckout::privateKey($private_key);
Twocheckout::sellerId($twocheckoutaccountid);

if($twocheckouttype == 'test'){
Twocheckout::verifySSL(false);
Twocheckout::sandbox(true);
}

try {

$firstname = (!empty($_POST['firstname'])) ? esc_html($_POST['firstname']) : '';
$lastname = (!empty($_POST['lastname'])) ? esc_html($_POST['lastname']) : '';

$address = (!empty($_POST['address'])) ? esc_html($_POST['address']) : '';
$city = (!empty($_POST['city'])) ? esc_html($_POST['city']) : '';
$state = (!empty($_POST['state'])) ? esc_html($_POST['state']) : '';
$zipcode = (!empty($_POST['zipcode'])) ? esc_html($_POST['zipcode']) : '302020';
$country = (!empty($_POST['country'])) ? esc_html($_POST['country']) : '';
$phone = (!empty($_POST['phone'])) ? esc_html($_POST['phone']) : '';
$email = (!empty($_POST['email'])) ? esc_html($_POST['email']) : '';
	
	if(service_finder_has_pay_only_admin_fee() && $adminfee > 0)
	{
		$totalcost = $adminfee;
	}

	$charge = Twocheckout_Charge::auth(array(
        "sellerId" => $twocheckoutaccountid,
		"privateKey" => $private_key,
	    "merchantOrderId" => time(),
        "token" => $token,
        "currency" => strtoupper(service_finder_currencycode()),
        "total" => $totalcost,
		"tangible"    => "N",
		"billingAddr" => array(
			"name" => $firstname.' '.$lastname,
			"addrLine1" => $address,
			"city" => $city,
			"state" => $state,
			"zipCode" => $zipcode,
			"country" => $country,
			"email" => $email,
			"phoneNumber" => $phone
		)
    ));
    if ($charge['response']['responseCode'] == 'APPROVED') {
	
		$transactionid = $charge['response']['transactionId'];
			
		$saveBooking = new SERVICE_FINDER_BookNow();
		$saveBooking->service_finder_SaveBooking($_POST,'',$transactionid,$adminfee);
		
		$senMail = new SERVICE_FINDER_BookNow();
			
		$bookingdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `txnid` = "%s"',$transactionid),ARRAY_A);
					
		$senMail->service_finder_SendBookingMailToProvider($bookingdata,'',$adminfee);
		$senMail->service_finder_SendBookingMailToCustomer($bookingdata,'',$adminfee);
		$senMail->service_finder_SendBookingMailToAdmin($bookingdata,'',$adminfee);
		
		$redirectOption = $service_finder_options['redirect-option'];
		$redirectURL = (!empty($service_finder_options['thankyou-page-url'])) ? $service_finder_options['thankyou-page-url'] : '';
		if($redirectOption == 'thankyou-page'){
			if($redirectURL != ""){
			$url = $redirectURL.'?bookingcompleted=success';
			}else{
			$url = service_finder_get_url_by_shortcode('[service_finder_thank_you]').'?bookingcompleted=success';
			}
		}else{
		$url = '';
		}
		$msg = (!empty($service_finder_options['provider-booked'])) ? $service_finder_options['provider-booked'] : esc_html__('Provider has been booked successfully', 'service-finder');
		$success = array(
				'status' => 'success',
				'redirecturl' => $url,
				'suc_message' => $msg,
				);
		echo json_encode($success);
	
    }

} catch (Twocheckout_Error $e) {
    $e->getMessage();
	
	$error = array(
			'status' => 'error',
			'err_message' => sprintf( esc_html__('%s', 'service-finder'), $e->getMessage() )
			);
	echo json_encode($error);
}

exit;
}

/*Booking PayU Latam Checkout Process*/
add_action('wp_ajax_payulatam_checkout', 'service_finder_payulatam_checkout');
add_action('wp_ajax_nopriv_payulatam_checkout', 'service_finder_payulatam_checkout');
function service_finder_payulatam_checkout(){
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/book-now/BookNow.php';
global $wpdb, $stripe_options, $service_finder_options, $service_finder_Tables;
$totalcost = (!empty($_POST['totalcost'])) ? esc_html($_POST['totalcost']) : '';
$provider = (isset($_POST['provider'])) ? esc_html($_POST['provider']) : '';
$cd_number = (isset($_POST['payulatam_card_number'])) ? esc_html($_POST['payulatam_card_number']) : '';
$cd_cvc = (isset($_POST['payulatam_card_cvc'])) ? esc_html($_POST['payulatam_card_cvc']) : '';
$cd_month = (isset($_POST['payulatam_card_month'])) ? esc_html($_POST['payulatam_card_month']) : '';
$cd_year = (isset($_POST['payulatam_card_year'])) ? esc_html($_POST['payulatam_card_year']) : '';
$cardtype = (isset($_POST['payulatam_cardtype'])) ? esc_html($_POST['payulatam_cardtype']) : '';
$totaldiscount = (!empty($_POST['totaldiscount'])) ? esc_html($_POST['totaldiscount']) : 0;
		
if(floatval($totalcost) >= floatval($totaldiscount)){
$totalcost = floatval($totalcost) - floatval($totaldiscount);
}else{
$totalcost = floatval($totalcost);
}


$currencyCode = service_finder_currencycode();
$locale = get_locale(); 
$temp = explode('_',$locale);
if(!empty($temp)){
$langcode = strtoupper($temp[0]);
define('LANGCODE', $langcode);
}else{
$langcode = 'EN';
define('LANGCODE', $langcode);
}

$userdata = service_finder_getUserInfo($provider);

$fullname = $userdata['fname'].' '.$userdata['lname'];
$user_email = $userdata['email'];
$phone = $userdata['phone'];

$admin_fee_type = (!empty($service_finder_options['admin-fee-type'])) ? $service_finder_options['admin-fee-type'] : 0;
$admin_fee_percentage = (!empty($service_finder_options['admin-fee-percentage'])) ? $service_finder_options['admin-fee-percentage'] : 0;
$admin_fee_fixed = (!empty($service_finder_options['admin-fee-fixed'])) ? $service_finder_options['admin-fee-fixed'] : 0;

$charge_admin_fee = (!empty($service_finder_options['charge-admin-fee'])) ? $service_finder_options['charge-admin-fee'] : '';
$charge_admin_fee_from = (!empty($service_finder_options['charge-admin-fee-from'])) ? $service_finder_options['charge-admin-fee-from'] : '';

$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? esc_html($service_finder_options['pay_booking_amount_to']) : '';

if($charge_admin_fee && $pay_booking_amount_to == 'admin' && (($admin_fee_type == 'fixed' && $admin_fee_fixed > 0) || ($admin_fee_type == 'percentage' && $admin_fee_percentage > 0)) && $charge_admin_fee_from == 'customer'){
	if($admin_fee_type == 'fixed'){
		$adminfee = $admin_fee_fixed;
	}elseif($admin_fee_type == 'percentage'){
		$adminfee = $totalcost * ($admin_fee_percentage/100);	
	}

	$totalcost = $totalcost + $adminfee;
}elseif($charge_admin_fee && $pay_booking_amount_to == 'admin' && (($admin_fee_type == 'fixed' && $admin_fee_fixed > 0) || ($admin_fee_type == 'percentage' && $admin_fee_percentage > 0)) && $charge_admin_fee_from == 'provider'){
	
	if($admin_fee_type == 'fixed'){
		$adminfee = $admin_fee_fixed;
	}elseif($admin_fee_type == 'percentage'){
		$adminfee = $totalcost * ($admin_fee_percentage/100);	
	}
	
}else{
	$adminfee = 0;
}

if(service_finder_has_pay_only_admin_fee() && $adminfee > 0)
{
	$totalcost = $adminfee;
}else
{
	$totalcost = $totalcost;
}

require_once(SERVICE_FINDER_PAYMENT_GATEWAY_DIR.'/payulatam/lib/PayU.php');

$settings = service_finder_getProviderSettings($provider);

$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? esc_html($service_finder_options['pay_booking_amount_to']) : '';
if($pay_booking_amount_to == 'admin'){
	if( isset($service_finder_options['payulatam-type']) && $service_finder_options['payulatam-type'] == 'test' ){
		$payulatammerchantid = (isset($service_finder_options['payulatam-merchantid-test'])) ? $service_finder_options['payulatam-merchantid-test'] : '';
		$payulatamapilogin = (isset($service_finder_options['payulatam-apilogin-test'])) ? $service_finder_options['payulatam-apilogin-test'] : '';
		$payulatamapikey = (isset($service_finder_options['payulatam-apikey-test'])) ? $service_finder_options['payulatam-apikey-test'] : '';
		$payulatamaccountid = (isset($service_finder_options['payulatam-accountid-test'])) ? $service_finder_options['payulatam-accountid-test'] : '';
		
		
	}else{
		$payulatammerchantid = (isset($service_finder_options['payulatam-merchantid-live'])) ? $service_finder_options['payulatam-merchantid-live'] : '';
		$payulatamapilogin = (isset($service_finder_options['payulatam-apilogin-live'])) ? $service_finder_options['payulatam-apilogin-live'] : '';
		$payulatamapikey = (isset($service_finder_options['payulatam-apikey-live'])) ? $service_finder_options['payulatam-apikey-live'] : '';
		$payulatamaccountid = (isset($service_finder_options['payulatam-accountid-live'])) ? $service_finder_options['payulatam-accountid-live'] : '';
		
	}
		
}elseif($pay_booking_amount_to == 'provider'){

		$payulatammerchantid = (isset($settings['payulatammerchantid'])) ? $settings['payulatammerchantid'] : '';
		$payulatamapilogin = (isset($settings['payulatamapilogin'])) ? $settings['payulatamapilogin'] : '';
		$payulatamapikey = (isset($settings['payulatamapikey'])) ? $settings['payulatamapikey'] : '';
		$payulatamaccountid = (isset($settings['payulatamaccountid'])) ? $settings['payulatamaccountid'] : '';
		
}

		if( isset($service_finder_options['payulatam-type']) && $service_finder_options['payulatam-type'] == 'test' ){

		$testmode = true;
		
		$paymenturl = "https://sandbox.api.payulatam.com/payments-api/4.0/service.cgi";
		$reportsurl = "https://sandbox.api.payulatam.com/reports-api/4.0/service.cgi";
		$subscriptionurl = "https://sandbox.api.payulatam.com/payments-api/rest/v4.3/";
		
		$fullname = 'APPROVED';

		}else{

		$testmode = false;
		
		$paymenturl = "https://api.payulatam.com/payments-api/4.0/service.cgi";
		$reportsurl = "https://api.payulatam.com/reports-api/4.0/service.cgi";
		$subscriptionurl = "https://api.payulatam.com/payments-api/rest/v4.3/";
		
		}

		$country = (isset($service_finder_options['payulatam-country'])) ? $service_finder_options['payulatam-country'] : '';
		
PayU::$apiKey = $payulatamapikey; //Enter your own apiKey here.
PayU::$apiLogin = $payulatamapilogin; //Enter your own apiLogin here.
PayU::$merchantId = $payulatammerchantid; //Enter your commerce Id here.
PayU::$language = SupportedLanguages::EN; //Select the language.
PayU::$isTest = $testmode; //Leave it True when testing.


// Payments URL
Environment::setPaymentsCustomUrl($paymenturl);
// Queries URL
Environment::setReportsCustomUrl($reportsurl);
// Subscriptions for recurring payments URL
Environment::setSubscriptionsCustomUrl($subscriptionurl);

$reference = 'booking_'.time();
$value = $totalcost;		
		
try {			
	$parameters = array(
	//Enter the account’s identifier here
	PayUParameters::ACCOUNT_ID => $payulatamaccountid,
	// Enter the reference code here.
	PayUParameters::REFERENCE_CODE => $reference,
	// Enter the description here.
	PayUParameters::DESCRIPTION => "Payment for Booking via PayU Latam",
	
	// -- Values --
	// Enter the value here.       
	PayUParameters::VALUE => $value,
	// Enter the currency here.
	PayUParameters::CURRENCY => $currencyCode,
	

	// -- Payer --
   ///Enter the payer's name here
	PayUParameters::PAYER_NAME => $fullname,//"APPROVED"
	//Enter the payer's email here
	PayUParameters::PAYER_EMAIL => $user_email,
	//Enter the payer's contact phone here.
	PayUParameters::PAYER_CONTACT_PHONE => $phone,
	
	// -- Credit card data -- 
		// Enter the number of the credit card here
	PayUParameters::CREDIT_CARD_NUMBER => $cd_number,
	// Enter expiration date of the credit card here
	PayUParameters::CREDIT_CARD_EXPIRATION_DATE => $cd_year.'/'.$cd_month,
	//Enter the security code of the credit card here
	PayUParameters::CREDIT_CARD_SECURITY_CODE=> $cd_cvc,
	//Enter the name of the credit card here
	// "MASTERCARD" || "AMEX" || "ARGENCARD" || "CABAL" || "NARANJA" || "CENCOSUD" || "SHOPPING"
	PayUParameters::PAYMENT_METHOD => $cardtype, 
	
	// Enter the number of installments here.
	PayUParameters::INSTALLMENTS_NUMBER => "1",
	// Enter the name of the country here.
	PayUParameters::COUNTRY => $country,
	
	);
	
	$response = PayUPayments::doAuthorizationAndCapture($parameters);

	if ($response->transactionResponse->state == 'APPROVED') {  
		
		$txnid = $response->transactionResponse->transactionId;
	
		$saveBooking = new SERVICE_FINDER_BookNow();
		$saveBooking->service_finder_SaveBooking($_POST,'',$txnid,$adminfee);
		
		$senMail = new SERVICE_FINDER_BookNow();
			
		$bookingdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `txnid` = "%s"',$txnid),ARRAY_A);
					
		$senMail->service_finder_SendBookingMailToProvider($bookingdata,'',$adminfee);
		$senMail->service_finder_SendBookingMailToCustomer($bookingdata,'',$adminfee);
		$senMail->service_finder_SendBookingMailToAdmin($bookingdata,'',$adminfee);
		
		$redirectOption = $service_finder_options['redirect-option'];
		$redirectURL = (!empty($service_finder_options['thankyou-page-url'])) ? $service_finder_options['thankyou-page-url'] : '';
		if($redirectOption == 'thankyou-page'){
			if($redirectURL != ""){
			$url = $redirectURL.'?bookingcompleted=success';
			}else{
			$url = service_finder_get_url_by_shortcode('[service_finder_thank_you]').'?bookingcompleted=success';
			}
		}else{
		$url = '';
		}
		$msg = (!empty($service_finder_options['provider-booked'])) ? $service_finder_options['provider-booked'] : esc_html__('Provider has been booked successfully', 'service-finder');
		
		$success = array(
				'status' => 'success',
				'redirecturl' => $url,
				'suc_message' => $msg,
				);
		echo json_encode($success);
	
	}else{
	
		$msg = $response->transactionResponse->state.': '.$response->transactionResponse->responseCode;
		
		$error = array(
				'status' => 'error',
				'err_message' => $msg
				);
		echo json_encode($error);
	}
		
					
} catch (Exception $e) {

	$error = array(
			'status' => 'error',
			'err_message' => $e->getMessage()
			);
	echo json_encode($error);
}

exit;
}

/*Booking Checkout Process*/
add_action('wp_ajax_checkout', 'service_finder_checkout');
add_action('wp_ajax_nopriv_checkout', 'service_finder_checkout');

function service_finder_checkout(){
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/book-now/BookNow.php';
global $wpdb, $stripe_options, $service_finder_options, $service_finder_Tables;
		$token = (!empty($_POST['stripeToken'])) ? esc_html($_POST['stripeToken']) : '';
		$totalcost = (!empty($_POST['totalcost'])) ? esc_html($_POST['totalcost']) : '';
		$totaldiscount = (!empty($_POST['totaldiscount'])) ? esc_html($_POST['totaldiscount']) : 0;
		$providerid = (!empty($_POST['provider'])) ? esc_html($_POST['provider']) : 0;
		
		
		if(floatval($totalcost) >= floatval($totaldiscount)){
		$totalcost = floatval($totalcost) - floatval($totaldiscount);
		}else{
		$totalcost = floatval($totalcost);
		}
		
		$admin_fee_type = (!empty($service_finder_options['admin-fee-type'])) ? $service_finder_options['admin-fee-type'] : 0;
		$admin_fee_percentage = (!empty($service_finder_options['admin-fee-percentage'])) ? $service_finder_options['admin-fee-percentage'] : 0;
		$admin_fee_fixed = (!empty($service_finder_options['admin-fee-fixed'])) ? $service_finder_options['admin-fee-fixed'] : 0;

		$charge_admin_fee = (!empty($service_finder_options['charge-admin-fee'])) ? $service_finder_options['charge-admin-fee'] : '';
		$charge_admin_fee_from = (!empty($service_finder_options['charge-admin-fee-from'])) ? $service_finder_options['charge-admin-fee-from'] : '';
		
		$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? esc_html($service_finder_options['pay_booking_amount_to']) : '';
		
		if($charge_admin_fee && $pay_booking_amount_to == 'admin' && (($admin_fee_type == 'fixed' && $admin_fee_fixed > 0) || ($admin_fee_type == 'percentage' && $admin_fee_percentage > 0)) && $charge_admin_fee_from == 'customer'){
			if($admin_fee_type == 'fixed'){
				$adminfee = $admin_fee_fixed;
			}elseif($admin_fee_type == 'percentage'){
				$adminfee = $totalcost * ($admin_fee_percentage/100);	
			}
			
			$destinationamount = $totalcost;
			
			$totalcost = $totalcost + $adminfee;
		}elseif($charge_admin_fee && $pay_booking_amount_to == 'admin' && (($admin_fee_type == 'fixed' && $admin_fee_fixed > 0) || ($admin_fee_type == 'percentage' && $admin_fee_percentage > 0)) && $charge_admin_fee_from == 'provider'){
			
			if($admin_fee_type == 'fixed'){
				$adminfee = $admin_fee_fixed;
			}elseif($admin_fee_type == 'percentage'){
				$adminfee = $totalcost * ($admin_fee_percentage/100);	
			}
			
			$destinationamount = $totalcost - $adminfee;
			
		}else{
			$adminfee = 0;
			$destinationamount = 0;
		}
		
		if(service_finder_has_pay_only_admin_fee() && $adminfee > 0)
		{
			$totalcost = $adminfee * 100;
		}else
		{
			$totalcost = $totalcost * 100;
		}
		require_once(SERVICE_FINDER_PAYMENT_GATEWAY_DIR.'/stripe/init.php');
		
		$settings = service_finder_getProviderSettings($_POST['provider']);
		
		$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? esc_html($service_finder_options['pay_booking_amount_to']) : '';
		if($pay_booking_amount_to == 'admin'){
			$stripetype = (!empty($service_finder_options['stripe-type'])) ? esc_html($service_finder_options['stripe-type']) : '';
			if($stripetype == 'live'){
				$secret_key = (!empty($service_finder_options['stripe-live-secret-key'])) ? esc_html($service_finder_options['stripe-live-secret-key']) : '';
			}else{
				$secret_key = (!empty($service_finder_options['stripe-test-secret-key'])) ? esc_html($service_finder_options['stripe-test-secret-key']) : '';
			}
		}elseif($pay_booking_amount_to == 'provider'){
			$secret_key = esc_html($settings['stripesecretkey']);
		}

		\Stripe\Stripe::setApiKey($secret_key);
 
		try {			
			$customer = \Stripe\Customer::create(array(
					'card' => $token,
					'name' => $_POST['firstname']." ".$_POST['lastname'],
					'address' => [
						'line1' => service_finder_get_data( $_POST, 'address' ),
						'postal_code' => service_finder_get_data( $_POST, 'zipcode' ),
						'city' => service_finder_get_data( $_POST, 'city' ),
						'state' => service_finder_get_data( $_POST, 'state' ),
					],
					'email' => $_POST['email'],
					'description' => "Provider booked by ".$_POST['firstname']." ".$_POST['lastname']
				)
			);	
			
			$acct_id = service_finder_get_stripe_connect_id($providerid);
			
			if($acct_id != "" && $destinationamount > 0 && get_user_meta($providerid,'stripe_connect_custom_account_id',true) != ''){
			
			$destinationamount = $destinationamount * 100;
			
			$charge = \Stripe\Charge::create(array(
						  "amount" => $totalcost,
						  "currency" => strtolower(service_finder_currencycode()),
						  "customer" => $customer->id, // obtained with Stripe.js
						  "description" => "Charge for Booking",
						  "transfer_data" => array(
							"amount" => $destinationamount,
							"destination" => $acct_id,
						  )
						));
			}else{
			$charge = \Stripe\Charge::create(array(
						  "amount" => $totalcost,
						  "currency" => strtolower(service_finder_currencycode()),
						  "customer" => $customer->id, // obtained with Stripe.js
						  "description" => "Charge for Booking",
						));
			}			

			if ($charge->paid == true && $charge->status == "succeeded") { 
			
				$saveBooking = new SERVICE_FINDER_BookNow();
				$saveBooking->service_finder_SaveBooking($_POST,$customer->id,$charge->balance_transaction,$adminfee);
				
				$senMail = new SERVICE_FINDER_BookNow();
					
				$bookingdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `stripe_customer_id` = "%s"',$customer->id),ARRAY_A);
							
				$senMail->service_finder_SendBookingMailToProvider($bookingdata,'',$adminfee);
				$senMail->service_finder_SendBookingMailToCustomer($bookingdata,'',$adminfee);
				$senMail->service_finder_SendBookingMailToAdmin($bookingdata,'',$adminfee);
				
				$redirectOption = $service_finder_options['redirect-option'];
				$redirectURL = (!empty($service_finder_options['thankyou-page-url'])) ? $service_finder_options['thankyou-page-url'] : '';
				if($redirectOption == 'thankyou-page'){
					if($redirectURL != ""){
					$url = $redirectURL.'?bookingcompleted=success';
					}else{
					$url = service_finder_get_url_by_shortcode('[service_finder_thank_you]').'?bookingcompleted=success';
					}
				}else{
				$url = '';
				}
				$msg = (!empty($service_finder_options['provider-booked'])) ? $service_finder_options['provider-booked'] : esc_html__('Provider has been booked successfully', 'service-finder');
				
				$success = array(
						'status' => 'success',
						'redirecturl' => $url,
						'suc_message' => $msg,
						);
				echo json_encode($success);
			
			}
				
							
		} catch (Exception $e) {
		
			$body = $e->getJsonBody();
  			$err  = $body['error'];
  
			$error = array(
					'status' => 'error',
					'err_message' => sprintf( esc_html__('%s', 'service-finder'), $err['message'] )
					);
			echo json_encode($error);
		}

exit;
}

/*Booking Free Checkout Process*/
add_action('wp_ajax_freecheckout', 'service_finder_freecheckout');
add_action('wp_ajax_nopriv_freecheckout', 'service_finder_freecheckout');

function service_finder_freecheckout(){
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/book-now/BookNow.php';
global $wpdb, $service_finder_options;
				
				$totalcost = isset($_POST['totalcost']) ? $_POST['totalcost'] : '';
				$totaldiscount = (!empty($_POST['totaldiscount'])) ? esc_html($_POST['totaldiscount']) : 0;
		
				if(floatval($totalcost) >= floatval($totaldiscount)){
				$totalcost = floatval($totalcost) - floatval($totaldiscount);
				}else{
				$totalcost = floatval($totalcost);
				}
				
				$admin_fee_type = (!empty($service_finder_options['admin-fee-type'])) ? $service_finder_options['admin-fee-type'] : 0;
				$admin_fee_percentage = (!empty($service_finder_options['admin-fee-percentage'])) ? $service_finder_options['admin-fee-percentage'] : 0;
				$admin_fee_fixed = (!empty($service_finder_options['admin-fee-fixed'])) ? $service_finder_options['admin-fee-fixed'] : 0;
				
				$charge_admin_fee = (!empty($service_finder_options['charge-admin-fee'])) ? $service_finder_options['charge-admin-fee'] : '';
				$charge_admin_fee_from = (!empty($service_finder_options['charge-admin-fee-from'])) ? $service_finder_options['charge-admin-fee-from'] : '';
				
				$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? esc_html($service_finder_options['pay_booking_amount_to']) : '';
				
				if($charge_admin_fee && $pay_booking_amount_to == 'admin' && (($admin_fee_type == 'fixed' && $admin_fee_fixed > 0) || ($admin_fee_type == 'percentage' && $admin_fee_percentage > 0)) && $charge_admin_fee_from == 'customer'){
					if($admin_fee_type == 'fixed'){
						$adminfee = $admin_fee_fixed;
					}elseif($admin_fee_type == 'percentage'){
						$adminfee = $totalcost * ($admin_fee_percentage/100);	
					}
					$totalcost = $totalcost + $adminfee;
				}elseif($charge_admin_fee && $pay_booking_amount_to == 'admin' && (($admin_fee_type == 'fixed' && $admin_fee_fixed > 0) || ($admin_fee_type == 'percentage' && $admin_fee_percentage > 0)) && $charge_admin_fee_from == 'provider'){
					if($admin_fee_type == 'fixed'){
						$adminfee = $admin_fee_fixed;
					}elseif($admin_fee_type == 'percentage'){
						$adminfee = $totalcost * ($admin_fee_percentage/100);	
					}
				}else{
					$adminfee = 0;
				}

				$saveBooking = new SERVICE_FINDER_BookNow();
				$saveBooking->service_finder_SaveBooking($_POST,'','',$adminfee);
				
				$redirectOption = $service_finder_options['redirect-option'];
				$redirectURL = (!empty($service_finder_options['thankyou-page-url'])) ? $service_finder_options['thankyou-page-url'] : '';
				if($redirectOption == 'thankyou-page'){
					if($redirectURL != ""){
					$url = $redirectURL.'?bookingcompleted=success';
					}else{
					$url = service_finder_get_url_by_shortcode('[service_finder_thank_you]').'?bookingcompleted=success';
					}
				}else{
				$url = '';
				}
				$msg = (!empty($service_finder_options['provider-booked'])) ? $service_finder_options['provider-booked'] : esc_html__('Provider has been booked successfully', 'service-finder');
				$success = array(
						'status' => 'success',
						'redirecturl' => $url,
						'suc_message' => $msg,
						);
				echo json_encode($success);
exit;
}

/*Booking Wallet Checkout Process*/
add_action('wp_ajax_walletcheckout', 'service_finder_walletcheckout');
add_action('wp_ajax_nopriv_walletcheckout', 'service_finder_walletcheckout');

function service_finder_walletcheckout(){
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/book-now/BookNow.php';
global $wpdb, $service_finder_options, $current_user;
				
$totalcost = isset($_POST['totalcost']) ? $_POST['totalcost'] : '';

$totaldiscount = (!empty($_POST['totaldiscount'])) ? esc_html($_POST['totaldiscount']) : 0;
		
if(floatval($totalcost) >= floatval($totaldiscount)){
$totalcost = floatval($totalcost) - floatval($totaldiscount);
}else{
$totalcost = floatval($totalcost);
}

$admin_fee_type = (!empty($service_finder_options['admin-fee-type'])) ? $service_finder_options['admin-fee-type'] : 0;
$admin_fee_percentage = (!empty($service_finder_options['admin-fee-percentage'])) ? $service_finder_options['admin-fee-percentage'] : 0;
$admin_fee_fixed = (!empty($service_finder_options['admin-fee-fixed'])) ? $service_finder_options['admin-fee-fixed'] : 0;

$charge_admin_fee = (!empty($service_finder_options['charge-admin-fee'])) ? $service_finder_options['charge-admin-fee'] : '';
$charge_admin_fee_from = (!empty($service_finder_options['charge-admin-fee-from'])) ? $service_finder_options['charge-admin-fee-from'] : '';

$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? esc_html($service_finder_options['pay_booking_amount_to']) : '';

if($charge_admin_fee && $pay_booking_amount_to == 'admin' && (($admin_fee_type == 'fixed' && $admin_fee_fixed > 0) || ($admin_fee_type == 'percentage' && $admin_fee_percentage > 0)) && $charge_admin_fee_from == 'customer'){
	if($admin_fee_type == 'fixed'){
		$adminfee = $admin_fee_fixed;
	}elseif($admin_fee_type == 'percentage'){
		$adminfee = $totalcost * ($admin_fee_percentage/100);	
	}
	$totalcost = $totalcost + $adminfee;
}elseif($charge_admin_fee && $pay_booking_amount_to == 'admin' && (($admin_fee_type == 'fixed' && $admin_fee_fixed > 0) || ($admin_fee_type == 'percentage' && $admin_fee_percentage > 0)) && $charge_admin_fee_from == 'provider'){
	if($admin_fee_type == 'fixed'){
		$adminfee = $admin_fee_fixed;
	}elseif($admin_fee_type == 'percentage'){
		$adminfee = $totalcost * ($admin_fee_percentage/100);	
	}
}else{
	$adminfee = 0;
}

$saveBooking = new SERVICE_FINDER_BookNow();
$saveBooking->service_finder_SaveBooking($_POST,'','',$adminfee);

if(service_finder_has_pay_only_admin_fee() && $adminfee > 0)
{
	$totalcost = $adminfee;
}

$walletamount = service_finder_get_wallet_amount($current_user->ID);

$remaining_wallet_amount = floatval($walletamount) - floatval($totalcost); 

$args = array(
	'user_id' => $current_user->ID,
	'amount' => $totalcost,
	'action' => 'debit',
	'debit_for' => esc_html__('For Booking', 'service-finder'),
	'payment_mode' => 'local',
	'payment_method' => 'wallet',
	'payment_status' => 'completed'
	);
	
service_finder_add_wallet_history($args);

$cashbackamount = service_finder_cashback_amount('booking');

if(floatval($cashbackamount['amount']) > 0){
$remaining_wallet_amount = floatval($remaining_wallet_amount) + floatval($cashbackamount['amount']);

$args = array(
	'user_id' => $current_user->ID,
	'amount' => $cashbackamount['amount'],
	'action' => 'credit',
	'debit_for' => $cashbackamount['description'],
	'payment_mode' => '',
	'payment_method' => '',
	'payment_status' => 'completed'
	);
	
service_finder_add_wallet_history($args);

}

update_user_meta($current_user->ID,'_sf_wallet_amount',$remaining_wallet_amount);

$redirectOption = $service_finder_options['redirect-option'];
$redirectURL = (!empty($service_finder_options['thankyou-page-url'])) ? $service_finder_options['thankyou-page-url'] : '';
if($redirectOption == 'thankyou-page'){
	if($redirectURL != ""){
	$url = $redirectURL.'?bookingcompleted=success';
	}else{
	$url = service_finder_get_url_by_shortcode('[service_finder_thank_you]').'?bookingcompleted=success';
	}
}else{
$url = '';
}
$msg = (!empty($service_finder_options['provider-booked'])) ? $service_finder_options['provider-booked'] : esc_html__('Provider has been booked successfully', 'service-finder');
$success = array(
		'status' => 'success',
		'redirecturl' => $url,
		'suc_message' => $msg,
		);
echo json_encode($success);
exit;
}

/*Check Zipcodes*/
add_action('wp_ajax_check_zipcode', 'service_finder_check_zipcode');
add_action('wp_ajax_nopriv_check_zipcode', 'service_finder_check_zipcode');

function service_finder_check_zipcode(){
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/book-now/BookNow.php';
global $wpdb;

$checkZipcode = new SERVICE_FINDER_BookNow();
$checkZipcode = $checkZipcode->service_finder_checkZipcode($_POST);
exit;
}

/*Load Members*/
add_action('wp_ajax_load_members', 'service_finder_load_members');
add_action('wp_ajax_nopriv_load_members', 'service_finder_load_members');

function service_finder_load_members(){
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/book-now/BookNow.php';
global $wpdb;

$loadMembers = new SERVICE_FINDER_BookNow();
echo $loadMembers = $loadMembers->service_finder_loadMembers($_POST);
exit;
}

/*Load Members*/
add_action('wp_ajax_load_members_list', 'service_finder_load_members_list');
add_action('wp_ajax_nopriv_load_members_list', 'service_finder_load_members_list');

function service_finder_load_members_list(){
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/book-now/BookNow.php';
global $wpdb;

$loadMembers = new SERVICE_FINDER_BookNow();
echo $loadMembers = $loadMembers->service_finder_loadMembersList($_POST);
exit;
}

/*Get Timeslots based on date*/
add_action('wp_ajax_get_bookingtimeslot', 'service_finder_get_bookingtimeslot');
add_action('wp_ajax_nopriv_get_bookingtimeslot', 'service_finder_get_bookingtimeslot');

function service_finder_get_bookingtimeslot(){
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/book-now/BookNow.php';
global $wpdb;

$provider_id = (!empty($_POST['provider_id'])) ? esc_html($_POST['provider_id']) : '';

$getBookingTimeSlot = new SERVICE_FINDER_BookNow();
if(service_finder_availability_method($provider_id) == 'timeslots'){
	echo $getBookingTimeSlot->service_finder_getBookingTimeSlot($_POST);
}elseif(service_finder_availability_method($provider_id) == 'starttime'){
	echo $getBookingTimeSlot->service_finder_getBookingStartTime($_POST);
}else{
	echo $getBookingTimeSlot->service_finder_getBookingTimeSlot($_POST);
}
exit;
}

/*Iner Login*/
add_action('wp_ajax_innerlogin', 'service_finder_innerlogin');
add_action('wp_ajax_nopriv_innerlogin', 'service_finder_innerlogin');

function service_finder_innerlogin(){
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/book-now/BookNow.php';
global $wpdb;
$innerLogin = new SERVICE_FINDER_BookNow();
$innerLogin->service_finder_innerLogin($_POST);
exit;
}

/*Add to Favorite*/
add_action('wp_ajax_addtofavorite', 'service_finder_addtofavorite');
add_action('wp_ajax_nopriv_addtofavorite', 'service_finder_addtofavorite');

function service_finder_addtofavorite(){
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/book-now/BookNow.php';
global $wpdb;
$addfavorite = new SERVICE_FINDER_BookNow();
$addfavorite->service_finder_addtofavorite($_POST);
exit;
}

/*Remove From Favorite*/
add_action('wp_ajax_removefromfavorite', 'service_finder_removefromfavorite');
add_action('wp_ajax_nopriv_removefromfavorite', 'service_finder_removefromfavorite');

function service_finder_removefromfavorite(){
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/book-now/BookNow.php';
global $wpdb;
$removefavorite = new SERVICE_FINDER_BookNow();
$removefavorite->service_finder_removeFromFavorite($_POST);
exit;
}

/*Reset Booking Calendar*/
add_action('wp_ajax_reset_bookingcalendar', 'service_finder_reset_bookingcalendar');
add_action('wp_ajax_nopriv_reset_bookingcalendar', 'service_finder_reset_bookingcalendar');

function service_finder_reset_bookingcalendar(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/book-now/BookNow.php';
$resetBookingCalender = new SERVICE_FINDER_BookNow();

$provider_id = (!empty($_POST['provider_id'])) ? esc_html($_POST['provider_id']) : '';

if(service_finder_availability_method($provider_id) == 'timeslots'){
	$resetBookingCalender->service_finder_resetBookingCalender($_POST);
}elseif(service_finder_availability_method($provider_id) == 'starttime'){
	$resetBookingCalender->service_finder_resetStartTimeBookingCalender($_POST);
}else{
	$resetBookingCalender->service_finder_resetBookingCalender($_POST);
}

exit;
}

/*Send Ivitation*/
add_action('wp_ajax_sendinvitation', 'service_finder_sendinvitation');

function service_finder_sendinvitation(){
global $wpdb,$service_finder_options,$service_finder_Tables;

$invitedjob = service_finder_get_data( $_POST, 'invitedjob' );
$provider_id = service_finder_get_data( $_POST, 'provider_id' );
$job = get_post($invitedjob);

$data = array(
	'created' => date('Y-m-d H:i:s'),
	'customer_id' => get_post_field( 'post_author', $invitedjob ),
	'provider_id' => $provider_id,
	'jobid' => $invitedjob,
);

$wpdb->insert($service_finder_Tables->job_invitations,wp_unslash($data));

if($service_finder_options['invitejob-to-provider-subject'] != ""){
	$msg_subject = $service_finder_options['invitejob-to-provider-subject'];
}else{
	$msg_subject = esc_html__('Job Invitation');
}

$provider = get_user_by('ID',$provider_id);

if(!empty($service_finder_options['invitejob-to-provider'])){
	$message = $service_finder_options['invitejob-to-provider'];
}else{
	$message = 'Congratulations, You have been invited for following job. Please go to job link and apply for the job.

	Job Title: %JOBTITLE%
	
	Job Link: %JOBLINK%';
}

$tokens = array('%JOBTITLE%','%JOBLINK%');
$replacements = array($job->post_title,'<a href="'.esc_url(get_permalink($invitedjob)).'">'.get_permalink($invitedjob).'</a>');
$msg_body = str_replace($tokens,$replacements,$message);

if(class_exists('aonesms'))
{
if(service_finder_get_data($service_finder_options,'is-active-provider-job-invite-sms') == true)
{
$smsbody = service_finder_get_data($service_finder_options,'template-provider-job-invite-sms');
if($smsbody != '')
{
$providerInfo = service_finder_get_provier_info($provider_id);

$smsreplacements = array($job->post_title,'<a href="'.esc_url(get_permalink($invitedjob)).'">'.get_permalink($invitedjob).'</a>');

$smsbody = str_replace($tokens,$smsreplacements,$smsbody);

aonesms_send_sms_notifications($providerInfo->mobile,$smsbody);
}
}
}

if(function_exists('service_finder_add_notices')) {
		
	$noticedata = array(
			'provider_id' => $provider_id,
			'target_id' => $invitedjob, 
			'topic' => 'Job Invitation',
			'title' => esc_html__('Job Invitation', 'service-finder'),
			'notice' => sprintf( esc_html__('You have been invited for job. Job title is %s', 'service-finder'), get_the_title( $invitedjob ) ),
			);
	service_finder_add_notices($noticedata);
	
}

if(service_finder_wpmailer($provider->user_email,$msg_subject,$msg_body)) {

	$success = array(
			'status' => 'success',
			'suc_message' => esc_html__('Invitation has been sent', 'service-finder'),
			);
	$service_finder_Success = json_encode($success);
	echo $service_finder_Success;
	
	
} else {
		
	$error = array(
			'status' => 'error',
			'err_message' => esc_html__('Invitation could not be sent.', 'service-finder'),
			);
	$service_finder_Errors = json_encode($error);
	echo $service_finder_Errors;
}
exit;
}

/*Verify service coupon code*/
add_action('wp_ajax_verify_couponcode', 'service_finder_verify_couponcode');
add_action('wp_ajax_nopriv_verify_couponcode', 'service_finder_verify_couponcode');
function service_finder_verify_couponcode(){
global $wpdb,$service_finder_Tables;

$serviceid = (!empty($_POST['serviceid'])) ? intval($_POST['serviceid']) : 0;
$userid = (!empty($_POST['userid'])) ? intval($_POST['userid']) : 0;
$couponcode = (!empty($_POST['couponcode'])) ? esc_attr($_POST['couponcode']) : '';
$cost = (!empty($_POST['cost'])) ? esc_attr($_POST['cost']) : '';
$costtype = (!empty($_POST['costtype'])) ? esc_attr($_POST['costtype']) : '';
$hours = (!empty($_POST['hours'])) ? esc_attr($_POST['hours']) : 0;
$discount = 0;

$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->services.' where id = %d AND coupon_code = %s',$serviceid,$couponcode));

if(!empty($row)){

	if(strtotime($row->expiry_date) < strtotime(date('Y-m-d'))){
		$error = array(
				'status' => 'error',
				'err_message' => esc_html__('Sorry, this coupon code has been expired.', 'service-finder'),
				);
		$service_finder_Errors = json_encode($error);
		echo $service_finder_Errors;
		exit;
	}
	
	/*$totalused = service_finder_total_service_coupon($couponcode,$userid,$serviceid);
	if($totalused >= $row->max_coupon){
		$error = array(
				'status' => 'error',
				'err_message' => esc_html__('Sorry, this coupon code has been used maximum time.', 'service-finder'),
				);
		$service_finder_Errors = json_encode($error);
		echo $service_finder_Errors;
		exit;
	}*/
	
	$totalusedbycustomer = service_finder_check_is_service_couponcode_used($couponcode,$userid,$serviceid);
	if($totalusedbycustomer > 0){
		$error = array(
				'status' => 'error',
				'err_message' => esc_html__('Sorry, you have already used this coupon code.', 'service-finder'),
				);
		$service_finder_Errors = json_encode($error);
		echo $service_finder_Errors;
		exit;
	}
	
	if($costtype == 'fixed'){
		$cost = floatval($cost);
	}else if($costtype == 'hourly' || $costtype == 'perperson'){
		$hours = ($hours > 0) ? $hours : 1;
		$cost = floatval($cost) * floatval($hours);
	}
	
	if($row->discount_type == 'percentage'){
		$discount = floatval($cost) * (floatval($row->discount_value)/100);
	}elseif($row->discount_type == 'fixed'){
		$discount = floatval($row->discount_value);	
	}
	
	if(floatval($cost) >= floatval($discount)){
	$discountedcost = floatval($cost) - floatval($discount);
	}else{
	$discountedcost = floatval($cost);
	}

	$success = array(
			'status' => 'success',
			'discountedcost' => service_finder_money_format($discountedcost),
			'discount_type' => $row->discount_type,
			'discount_value' => $row->discount_value,
			'suc_message' => esc_html__('Coupon code verified successfully.', 'service-finder'),
			);
	$service_finder_Success = json_encode($success);
	echo $service_finder_Success;
}else{
	$error = array(
			'status' => 'error',
			'err_message' => esc_html__('Coupon code not verified.', 'service-finder'),
			);
	$service_finder_Errors = json_encode($error);
	echo $service_finder_Errors;
}
exit;
}

/*Verify service coupon code*/
add_action('wp_ajax_verify_booking_couponcode', 'service_finder_verify_booking_couponcode');
add_action('wp_ajax_nopriv_verify_booking_couponcode', 'service_finder_verify_booking_couponcode');
function service_finder_verify_booking_couponcode(){
global $wpdb,$service_finder_Tables;

$userid = (!empty($_POST['userid'])) ? intval($_POST['userid']) : 0;
$couponcode = (!empty($_POST['couponcode'])) ? esc_attr($_POST['couponcode']) : '';
$totalcost = (!empty($_POST['totalcost'])) ? esc_attr($_POST['totalcost']) : 0;
$discount = 0;

$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->offers.' where wp_user_id = %d AND coupon_code = %s',$userid,$couponcode));

if(!empty($row)){

	if(strtotime($row->expiry_date) < strtotime(date('Y-m-d'))){
		$error = array(
				'status' => 'error',
				'err_message' => esc_html__('Sorry, this coupon code has been expired.', 'service-finder'),
				);
		$service_finder_Errors = json_encode($error);
		echo $service_finder_Errors;
		exit;
	}
	
	$totalused = service_finder_total_booking_coupon($couponcode,$userid);
	if($totalused >= $row->max_coupon){
		$error = array(
				'status' => 'error',
				'err_message' => esc_html__('Sorry, this coupon code has been used maximum time.', 'service-finder'),
				);
		$service_finder_Errors = json_encode($error);
		echo $service_finder_Errors;
		exit;
	}
	
	$totalusedbycustomer = service_finder_check_is_couponcode_used($couponcode,$userid);
	if($totalusedbycustomer > 0){
		$error = array(
				'status' => 'error',
				'err_message' => esc_html__('Sorry, you have already used this coupon code.', 'service-finder'),
				);
		$service_finder_Errors = json_encode($error);
		echo $service_finder_Errors;
		exit;
	}

	if($row->discount_type == 'percentage'){
		$discount = floatval($totalcost) * (floatval($row->discount_value)/100);
	}elseif($row->discount_type == 'fixed'){
		$discount = floatval($row->discount_value);	
	}
	
	if(floatval($totalcost) >= floatval($discount)){
	$discountedcost = floatval($totalcost) - floatval($discount);
	}else{
	$discountedcost = floatval($totalcost);
	}

	$success = array(
			'status' => 'success',
			'discountedcost' => service_finder_money_format($discountedcost),
			'discount' => $discount,
			'updatedtotalcost' => $discountedcost,
			'discount_type' => $row->discount_type,
			'discount_value' => $row->discount_value,
			'suc_message' => esc_html__('Coupon code verified successfully.', 'service-finder'),
			);
	$service_finder_Success = json_encode($success);
	echo $service_finder_Success;
}else{
	$error = array(
			'status' => 'error',
			'err_message' => esc_html__('Coupon code not verified.', 'service-finder'),
			);
	$service_finder_Errors = json_encode($error);
	echo $service_finder_Errors;
}
exit;
}

/*Load Members*/
add_action('wp_ajax_load_customer_data', 'service_finder_load_customer_data');
add_action('wp_ajax_nopriv_load_customer_data', 'service_finder_load_customer_data');

function service_finder_load_customer_data(){
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/book-now/BookNow.php';
global $wpdb;

$load_customer_data = new SERVICE_FINDER_BookNow();
echo $load_customer_data = $load_customer_data->service_finder_load_customer_data($_POST);
exit;
}