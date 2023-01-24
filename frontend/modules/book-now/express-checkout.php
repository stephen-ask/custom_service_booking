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


require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/book-now/BookNow.php';

$service_finder_options = get_option('service_finder_options');
$paypal = service_finder_plugin_global_vars('paypal');
$service_finder_Tables = service_finder_plugin_global_vars('service_finder_Tables');
$service_finder_Errors = service_finder_plugin_global_vars('service_finder_Errors');
$registerErrors = service_finder_plugin_global_vars('registerErrors');
$registerMessages = service_finder_plugin_global_vars('registerMessages');

$provider = isset($_POST['provider']) ? $_POST['provider'] : '';
$totalcost = isset($_POST['totalcost']) ? $_POST['totalcost'] : '';
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
	$userLink = service_finder_get_author_url($provider);
	$returnUrl = add_query_arg( array('booking_made' => 'success'), $userLink );
	
	if(service_finder_has_pay_only_admin_fee() && $adminfee > 0)
	{
		$totalcost = $adminfee;
	}
	
	// Single payments
	$cancelUrl = add_query_arg( array('booking_made' => 'cancel'), $userLink );
	
	$getMincost = new SERVICE_FINDER_BookNow();

	
	$urlParams = array(
		'RETURNURL' => $returnUrl,
		'CANCELURL' => $cancelUrl
	);
					
	$orderParams = array(
		'PAYMENTREQUEST_0_AMT' => $totalcost,
		'PAYMENTREQUEST_0_SHIPPINGAMT' => '0',
		'PAYMENTREQUEST_0_CURRENCYCODE' => strtoupper(service_finder_currencycode()),
		'PAYMENTREQUEST_0_ITEMAMT' => $totalcost
	);
	$itemParams = array(
		'L_PAYMENTREQUEST_0_NAME0' => 'Payment via paypal',
		'L_PAYMENTREQUEST_0_DESC0' => 'Booking Made',
		'L_PAYMENTREQUEST_0_AMT0' => $totalcost,
		'L_PAYMENTREQUEST_0_QTY0' => '1'
	);
	$params = $urlParams + $orderParams + $itemParams;
	$response = $paypal -> request('SetExpressCheckout',$params);
	$errors = new WP_Error();
	if(!$response){
		$errorMessage = esc_html__( 'ERROR: Bad paypal API settings! Check paypal api credentials in admin settings!', 'service-finder' );
		$detailErrorMessage = $paypal->getErrors();
		$errors->add( 'bad_paypal_api', $errorMessage . ' ' . $detailErrorMessage );
		$registerErrors = $errors;
	}
	
	// Request successful
	if(is_array($response) && $response['ACK'] == 'Success') {
		// write token to DB
		$token = $response['TOKEN'];

		$saveBooking = new SERVICE_FINDER_BookNow();
		$saveBooking->service_finder_SaveBooking($_POST,$token,'',$adminfee);
		// go to payment site
		header( 'Location: https://www.'.$sandbox.'paypal.com/webscr?cmd=_express-checkout&token=' . urlencode($token) );
		die();

	} else {
		$errorMessage = esc_html__( 'ERROR: Bad paypal API settings! Check paypal api credentials in admin settings!', 'service-finder' );
		$detailErrorMessage = (isset($response['L_LONGMESSAGE0'])) ? $response['L_LONGMESSAGE0'] : '';
		$errors->add( 'bad_paypal_api', $errorMessage . ' ' . $detailErrorMessage );
		$registerErrors = $errors;
	}
