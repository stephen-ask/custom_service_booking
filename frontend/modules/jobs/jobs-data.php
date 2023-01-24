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

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$wpdb = service_finder_plugin_global_vars('wpdb');
$service_finder_Params = service_finder_plugin_global_vars('service_finder_Params');

$payment_mode = (isset($_POST['payment_mode'])) ? esc_html($_POST['payment_mode']) : '';
/*Job Limit via paypal*/
if(isset($_POST['payment_mode']) && $payment_mode == 'paypal' && isset($_POST['joblimit-payment'])){
$service_finder_options = get_option('service_finder_options');
$paypal = service_finder_plugin_global_vars('paypal');
$service_finder_Errors = service_finder_plugin_global_vars('service_finder_Errors');
$service_finder_Tables = service_finder_plugin_global_vars('service_finder_Tables');
$registerErrors = service_finder_plugin_global_vars('registerErrors');
$registerMessages = service_finder_plugin_global_vars('registerMessages');

$service_finder_options = get_option('service_finder_options');
$creds = array();
/*Assign papal credentials*/
$paypalCreds['USER'] = (isset($service_finder_options['paypal-username'])) ? $service_finder_options['paypal-username'] : '';
$paypalCreds['PWD'] = (isset($service_finder_options['paypal-password'])) ? $service_finder_options['paypal-password'] : '';
$paypalCreds['SIGNATURE'] = (isset($service_finder_options['paypal-signatue'])) ? $service_finder_options['paypal-signatue'] : '';
$sandbox = (isset($service_finder_options['paypal-type']) && $service_finder_options['paypal-type'] == 'live') ? '' : 'sandbox.';
$paypalType = (isset($service_finder_options['paypal-type']) && $service_finder_options['paypal-type'] == 'live') ? '' : 'sandbox.';

$paypalTypeBool = (!empty($paypalType)) ? true : false;

$paypal = new Paypal($paypalCreds,$paypalTypeBool);

$paypalTypeBool = (!empty($paypalType)) ? true : false;

$paypal = new Paypal($paypalCreds,$paypalTypeBool);

$provider_id = (isset($_POST['provider_id'])) ? esc_html($_POST['provider_id']) : '';
$plan = (isset($_POST['plan'])) ? esc_html($_POST['plan']) : '';

$planprice = (!empty($service_finder_options['plan'.$plan.'-price'])) ? $service_finder_options['plan'.$plan.'-price'] : '';

$userdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$wpdb->users.' WHERE `ID` = %d',$provider_id));

$currencyCode = service_finder_currencycode();

	$returnUrl = add_query_arg( array('joblimitpayment_made' => 'success','plan' => $plan), service_finder_get_url_by_shortcode('[service_finder_my_account') );
	$cancelUrl = add_query_arg( array('joblimitpayment_made' => 'cancel'), service_finder_get_url_by_shortcode('[service_finder_my_account') );
	$urlParams = array(
		'RETURNURL' => $returnUrl,
		'CANCELURL' => $cancelUrl
	);
					
	$orderParams = array(
		'PAYMENTREQUEST_0_AMT' => $planprice,
		'PAYMENTREQUEST_0_SHIPPINGAMT' => '0',
		'PAYMENTREQUEST_0_CURRENCYCODE' => strtoupper($currencyCode),
		'PAYMENTREQUEST_0_ITEMAMT' => $planprice
	);
	$itemParams = array(
		'L_PAYMENTREQUEST_0_NAME0' => 'Payment via paypal',
		'L_PAYMENTREQUEST_0_DESC0' => 'Payment made for job apply limit',
		'L_PAYMENTREQUEST_0_AMT0' => $planprice,
		'L_PAYMENTREQUEST_0_QTY0' => '1'
	);
	$params = $urlParams + $orderParams + $itemParams;
	$response = $paypal -> request('SetExpressCheckout',$params);
	$errors = new WP_Error();
	if(!$response){
		$errorMessage = esc_html__( 'ERROR: Bad paypal API settings! Check paypal api credentials in admin settings!', 'service-finder' );
		$detailErrorMessage = reset($paypal->getErrors());
		$errors->add( 'bad_paypal_api', $errorMessage . ' ' . $detailErrorMessage );
		$registerErrors = $errors;
	}
	
	// Request successful
	if(is_array($response) && $response['ACK'] == 'Success') {
		// write token to DB
		$token = $response['TOKEN'];
		$data = array(
					'paypal_token' => $token
					);
		$where = array(
					'provider_id' => $provider_id
			);
		$res = $wpdb->update($service_finder_Tables->job_limits,wp_unslash($data),$where);
		// go to payment site
		header( 'Location: https://www.'.$sandbox.'paypal.com/webscr?cmd=_express-checkout&token=' . urlencode($token) );
		die();

	} else {
		$errorMessage = esc_html__( 'ERROR: Bad paypal API settings! Check paypal api credentials in admin settings!', 'service-finder' );
		$detailErrorMessage = (isset($response['L_LONGMESSAGE0'])) ? $response['L_LONGMESSAGE0'] : '';
		$errors->add( 'bad_paypal_api', $errorMessage . ' ' . $detailErrorMessage );
		$registerErrors = $errors;
	}
}


/*Job Limit via skip payment*/
if(isset($_POST['payment_mode']) && $payment_mode == 'skippayment' && isset($_POST['joblimit-payment'])){
global $wpdb, $service_finder_options, $service_finder_Tables;

$provider_id = (isset($_POST['provider_id'])) ? esc_html($_POST['provider_id']) : '';
$plan = (isset($_POST['plan'])) ? esc_html($_POST['plan']) : '';

$planlimit = (!empty($service_finder_options['plan'.$plan.'-limit'])) ? $service_finder_options['plan'.$plan.'-limit'] : 0;
$planprice = (!empty($service_finder_options['plan'.$plan.'-price'])) ? $service_finder_options['plan'.$plan.'-price'] : 0;

$row = $wpdb->get_row('SELECT * FROM '.$service_finder_Tables->job_limits.' WHERE `provider_id` = "'.$provider_id.'"');
if(!empty($row)){
	$paidlimit = $planlimit + $row->paid_limits;
	$available_limits = $planlimit + $row->available_limits;
}else{
	$paidlimit = $planlimit;
	$available_limits = $planlimit;
}

$data = array(
		'paid_limits' => $paidlimit,
		'available_limits' => $available_limits,
		'txn_id' => '-',
		'payment_method' => 'skippayment',
		'payment_status' => 'paid',
		'current_plan' => $plan,
		);
$where = array(
		'provider_id' => $provider_id
);
$res = $wpdb->update($service_finder_Tables->job_limits,wp_unslash($data),$where);

$paydate = date('Y-m-d h:i:s');
$txndata = array(
		'provider_id' => $provider_id,
		'payment_date' => $paydate,
		'txn_id' => '-',
		'plan' => $plan,
		'amount' => $planprice,
		'limit' => $planlimit,
		'payment_method' => 'skippayment',
		'payment_status' => 'paid',
		);
$wpdb->insert($service_finder_Tables->transaction,wp_unslash($txndata));

send_mail_after_joblimit_connect_purchase( $provider_id );
// show messages
$currentpageurl = service_finder_get_my_account_url($provider_id);
$currentpageurl = add_query_arg( array('tabname' => 'job-limits','joblimitplanupdate' => 'success'), $currentpageurl );

wp_redirect($currentpageurl);
die;

}

// check token (paypal merchant authorization) and Do Payment
if(isset($_GET['joblimitpayment_made']) && ($_GET['joblimitpayment_made'] == 'success') && !empty($_GET['token'])) {

	// find token
	$service_finder_options = get_option('service_finder_options');
	$service_finder_Tables = service_finder_plugin_global_vars('service_finder_Tables');
	$registerErrors = service_finder_plugin_global_vars('registerErrors');
	$registerMessages = service_finder_plugin_global_vars('registerMessages');

	$token = (isset($_GET['token'])) ? esc_html($_GET['token']) : '';
	$tokenRow = $wpdb->get_row( $wpdb->prepare("SELECT * FROM ".$service_finder_Tables->job_limits." WHERE `paypal_token` = '%s'",$token) );
	if(!empty($tokenRow)){
		
		// get checkout details from token
		$checkoutDetails = $paypal -> request('GetExpressCheckoutDetails', array('TOKEN' => $token));
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
					
					// We'll fetch the transaction ID for internal bookkeeping
					$transactionId = $singlePayment['PAYMENTINFO_0_TRANSACTIONID'];
					
					$provider_id = $tokenRow->provider_id;
					$plan = (isset($_GET['plan'])) ? esc_html($_GET['plan']) : '';
					
					$planlimit = (!empty($service_finder_options['plan'.$plan.'-limit'])) ? $service_finder_options['plan'.$plan.'-limit'] : 0;
					$planprice = (!empty($service_finder_options['plan'.$plan.'-price'])) ? $service_finder_options['plan'.$plan.'-price'] : 0;
					
					$row = $wpdb->get_row('SELECT * FROM '.$service_finder_Tables->job_limits.' WHERE `provider_id` = "'.$provider_id.'"');
					if(!empty($row)){
						$paidlimit = $planlimit + $row->paid_limits;
						$available_limits = $planlimit + $row->available_limits;
					}else{
						$paidlimit = $planlimit;
						$available_limits = $planlimit;
					}
					
					
					
					$data = array(
							'paid_limits' => $paidlimit,
							'available_limits' => $available_limits,
							'txn_id' => $transactionId,
							'payment_method' => 'paypal',
							'payment_status' => 'paid',
							'current_plan' => $plan,
							);
					$where = array(
							'provider_id' => $provider_id
					);
					$res = $wpdb->update($service_finder_Tables->job_limits,wp_unslash($data),$where);
					
					$paydate = date('Y-m-d h:i:s');
					$txndata = array(
							'provider_id' => $provider_id,
							'payment_date' => $paydate,
							'txn_id' => $transactionId,
							'plan' => $plan,
							'amount' => $planprice,
							'limit' => $planlimit,
							'payment_method' => 'paypal',
							'payment_status' => 'paid',
							);
					$wpdb->insert($service_finder_Tables->transaction,wp_unslash($txndata));
					
					send_mail_after_joblimit_connect_purchase( $provider_id );
					
					// show messages
					$currentpageurl = service_finder_get_my_account_url($provider_id);
					$currentpageurl = add_query_arg( array('tabname' => 'job-limits','joblimitplanupdate' => 'success'), $currentpageurl );
					wp_redirect($currentpageurl);
					die;

				}

			}

		}
}

// delete token and show messages if user cancel payment 
if(isset($_GET['joblimitpayment_made']) && ($_GET['joblimitpayment_made'] == 'cancel') && !empty($_GET['token'])){
	// delete token from DB
	$registerErrors = service_finder_plugin_global_vars('registerErrors');
	
	// delete token from DB
	$token = (isset($_GET['token'])) ? esc_html($_GET['token']) : '';
	$tokenRow = $wpdb->get_row( $wpdb->prepare("SELECT * FROM ".$service_finder_Tables->job_limits." WHERE `paypal_token` = '%s'",$token) );
	if($tokenRow){
		
		$wpdb->query($wpdb->prepare("UPDATE ".$service_finder_Tables->job_limits." SET `paypal_token` = '' WHERE `paypal_token` = '%s'",$token));
		
		$errors = new WP_Error();
		$message = esc_html__("You canceled payment. Your payment wasn't made","service-finder");
		$errors->add( 'cancel_payment', $message);
		$registerErrors = $errors;
	}	
	
}
/*Featured via paypal end*/

/*Job Limit via payu money start*/
if(isset($_POST['payment_mode']) && $payment_mode == 'payumoney' && isset($_POST['joblimit-payment'])){
global $wpdb, $service_finder_options, $service_finder_Tables;

if( isset($service_finder_options['payumoney-type']) && $service_finder_options['payumoney-type'] == 'test' ){
	$MERCHANT_KEY = $service_finder_options['payumoney-key-test'];
	$SALT = $service_finder_options['payumoney-salt-test'];
	$PAYU_BASE_URL = "https://test.payu.in";
}else{
	$MERCHANT_KEY = $service_finder_options['payumoney-key-live'];
	$SALT = $service_finder_options['payumoney-salt-live'];
	$PAYU_BASE_URL = "https://secure.payu.in";
}

$provider_id = (isset($_POST['provider_id'])) ? esc_html($_POST['provider_id']) : '';
$plan = (isset($_POST['plan'])) ? esc_html($_POST['plan']) : '';

$planprice = (!empty($service_finder_options['plan'.$plan.'-price'])) ? $service_finder_options['plan'.$plan.'-price'] : '';

$surl = add_query_arg( array('joblimitpaymentmade' => 'success','payutransaction' => 'success'), service_finder_get_url_by_shortcode('[service_finder_my_account') );
$furl = add_query_arg( array('joblimitpaymentmade' => 'failed','payutransaction' => 'failed'), service_finder_get_url_by_shortcode('[service_finder_my_account') );

$txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
$action = $PAYU_BASE_URL . '/_payment';

$userdata = service_finder_getUserInfo($provider_id);

$price = $planprice;

$productinfo = 'Payment for Purchase Job Limit Plan';
$first_name = $userdata['fname'];
$user_email = $userdata['email'];
$phone = $userdata['phone'];

$str = "$MERCHANT_KEY|$txnid|$price|$productinfo|$first_name|$user_email|$plan|$provider_id|||||||||$SALT";

$hash = strtolower(hash('sha512', $str));

$payuindia_args = array(
	'key' 			=> $MERCHANT_KEY,
	'hash' 			=> $hash,
	'txnid' 		=> $txnid,
	'amount' 		=> $price,
	'firstname'		=> $first_name,
	'email' 		=> $user_email,
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
	'udf1' 			=> $plan,
	'udf2' 			=> $provider_id,
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

if(isset($_GET['joblimitpaymentmade']) && $_GET['joblimitpaymentmade'] == 'success' && $_GET['payutransaction'] == 'success' && isset($_GET['payutransaction']) && isset($_POST['mihpayid']) && isset($_POST['status'])){

$service_finder_options = get_option('service_finder_options');
$service_finder_Tables = service_finder_plugin_global_vars('service_finder_Tables');
$registerErrors = service_finder_plugin_global_vars('registerErrors');
$registerMessages = service_finder_plugin_global_vars('registerMessages');

$plan = (isset($_POST['udf1'])) ? esc_html($_POST['udf1']) : '';
$provider_id = (isset($_POST['udf2'])) ? esc_html($_POST['udf2']) : '';
$txnid = (isset($_POST['txnid'])) ? esc_html($_POST['txnid']) : '';
$payuMoneyId = (isset($_POST['mihpayid'])) ? esc_html($_POST['mihpayid']) : '';
$status = (isset($_POST['status'])) ? esc_html($_POST['status']) : '';

if($status == 'success' && $payuMoneyId != ""){

$planlimit = (!empty($service_finder_options['plan'.$plan.'-limit'])) ? $service_finder_options['plan'.$plan.'-limit'] : 0;
$planprice = (!empty($service_finder_options['plan'.$plan.'-price'])) ? $service_finder_options['plan'.$plan.'-price'] : 0;

$row = $wpdb->get_row('SELECT * FROM '.$service_finder_Tables->job_limits.' WHERE `provider_id` = "'.$provider_id.'"');
if(!empty($row)){
	$paidlimit = $planlimit + $row->paid_limits;
	$available_limits = $planlimit + $row->available_limits;
}else{
	$paidlimit = $planlimit;
	$available_limits = $planlimit;
}

$data = array(
		'paid_limits' => $paidlimit,
		'available_limits' => $available_limits,
		'txn_id' => $txnid,
		'payment_method' => 'payumoney',
		'payment_status' => 'paid',
		'current_plan' => $plan,
		);
$where = array(
		'provider_id' => $provider_id
);
$res = $wpdb->update($service_finder_Tables->job_limits,wp_unslash($data),$where);

$paydate = date('Y-m-d h:i:s');
$txndata = array(
		'provider_id' => $provider_id,
		'payment_date' => $paydate,
		'txn_id' => $txnid,
		'plan' => $plan,
		'amount' => $planprice,
		'limit' => $planlimit,
		'payment_method' => 'payumoney',
		'payment_status' => 'paid',
		);
$wpdb->insert($service_finder_Tables->transaction,wp_unslash($txndata));

send_mail_after_joblimit_connect_purchase( $provider_id );
// show messages
$currentpageurl = service_finder_get_my_account_url($provider_id);
$currentpageurl = add_query_arg( array('tabname' => 'job-limits','joblimitplanupdate' => 'success'), $currentpageurl );
wp_redirect($currentpageurl );
die;

}

}

if(isset($_GET['joblimitpaymentmade']) && $_GET['joblimitpaymentmade'] == 'failed' && $_GET['payutransaction'] == 'failed'){

$registerErrors = service_finder_plugin_global_vars('registerErrors');

$errors = new WP_Error();
$message = esc_html__("You canceled payment. Your payment wasn't made","service-finder");
$errors->add( 'cancel_payment', $message);
$registerErrors = $errors;

}
/*Job Limit via payu money end*/

/*Add new service ajax call*/
add_action('wp_ajax_get_applied_jobs', 'service_finder_get_applied_jobs');
function service_finder_get_applied_jobs(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/jobs/MyJobs.php';
$getjobs = new SERVICE_FINDER_MyJobs();
$getjobs->service_finder_getjobs($_POST);
exit;
}

/*Approve wired booking*/
add_action('wp_ajax_wired_job_approval', 'service_finder_wired_job_approval');
function service_finder_wired_job_approval(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/jobs/MyJobs.php';
$approvebooking = new SERVICE_FINDER_MyJobs();
$approvebooking->service_finder_booking_approve($_POST);
exit;
}

/*Change Job Status without booking*/
add_action('wp_ajax_change_job_status', 'service_finder_change_job_status');
function service_finder_change_job_status(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/jobs/MyJobs.php';
$jobstatus = new SERVICE_FINDER_MyJobs();
$jobstatus->service_finder_job_status($_POST);
exit;
}

/*Make payment for job limit via stripe*/
add_action('wp_ajax_joblimit_payment', 'service_finder_joblimit_payment');
function service_finder_joblimit_payment(){
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/jobs/MyJobs.php';
global $wpdb, $stripe_options, $service_finder_options, $service_finder_Tables;
		$provider_id = (isset($_POST['provider_id'])) ? $_POST['provider_id'] : '';
		$plan = (isset($_POST['plan'])) ? $_POST['plan'] : '';
		
		$planprice = (!empty($service_finder_options['plan'.$plan.'-price'])) ? $service_finder_options['plan'.$plan.'-price'] : '';
		
		$userdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'users WHERE `ID` = %d',$provider_id));
		
		$token = (isset($_POST['stripeToken'])) ? $_POST['stripeToken'] : '';
		$totalcost = $planprice * 100;
		require_once(SERVICE_FINDER_PAYMENT_GATEWAY_DIR.'/stripe/init.php');
		
		if( isset($service_finder_options['stripe-type']) && $service_finder_options['stripe-type'] == 'test' ){
			$secret_key = (!empty($service_finder_options['stripe-test-secret-key'])) ? $service_finder_options['stripe-test-secret-key'] : '';
		}else{
			$secret_key = (!empty($service_finder_options['stripe-live-secret-key'])) ? $service_finder_options['stripe-live-secret-key'] : '';
		}
		
		\Stripe\Stripe::setApiKey($secret_key);
 
		try {			
			$customer = \Stripe\Customer::create(array(
					'card' => $token,
					'email' => $userdata->user_email,
					'description' => "Payment made for increase job limits"
				)
			);	

			$charge = \Stripe\Charge::create(array(
						  "amount" => $totalcost,
						  "currency" => strtolower(service_finder_currencycode()),
						  "customer" => $customer->id, // obtained with Stripe.js
						  "description" => "Charge to increase job limits"
						));

			if ($charge->paid == true && $charge->status == "succeeded") { 
			
				$makePayment = new SERVICE_FINDER_MyJobs();
				
				$txnid = $charge->balance_transaction;
				
				$makePayment->service_finder_makePayment($_POST,$customer->id,$txnid,'stripe');
				$msg = esc_html__('Payment has been made successfully', 'service-finder');
				
				$currentpageurl = service_finder_get_my_account_url($provider_id);
				$currentpageurl = add_query_arg( array('tabname' => 'job-limits'), $currentpageurl );
				
				$success = array(
						'status' => 'success',
						'suc_message' => $msg,
						'redirect_url' => $currentpageurl,
						);
				echo json_encode($success);
			}
				
							
		} catch (Exception $e) {
			$body = $e->getJsonBody();
  			$err  = $body['error'];
  
			$error = array(
					'status' => 'error',
					'err_message' => $e->getMessage()
					);
			echo json_encode($error);
		}

exit;
} 

/*Make payment for job limit via wallet*/
add_action('wp_ajax_joblimit_wallet_payment', 'service_finder_joblimit_wallet_payment');
function service_finder_joblimit_wallet_payment(){
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/jobs/MyJobs.php';
global $wpdb, $stripe_options, $service_finder_options, $service_finder_Tables;
$provider_id = (isset($_POST['provider_id'])) ? $_POST['provider_id'] : '';
$plan = (isset($_POST['plan'])) ? $_POST['plan'] : '';

$planprice = (!empty($service_finder_options['plan'.$plan.'-price'])) ? $service_finder_options['plan'.$plan.'-price'] : '';

$walletamount = service_finder_get_wallet_amount($provider_id);

if(floatval($walletamount) < floatval($planprice)){
$error = array(
		'status' => 'error',
		'err_message' => 'insufficient_amount'
		);
echo json_encode($error);
exit(0);
}

$userdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'users WHERE `ID` = %d',$provider_id));

$makePayment = new SERVICE_FINDER_MyJobs();
$makePayment->service_finder_makePayment($_POST,'','','wallet');

$remaining_wallet_amount = floatval($walletamount) - floatval($planprice); 

$args = array(
	'user_id' => $provider_id,
	'amount' => $planprice,
	'action' => 'debit',
	'debit_for' => esc_html__('Purchase Job Limit', 'service-finder'),
	'payment_mode' => 'local',
	'payment_method' => 'wallet',
	'payment_status' => 'completed'
	);
	
service_finder_add_wallet_history($args);

$cashbackamount = service_finder_cashback_amount('job-apply-limit');

if(floatval($cashbackamount['amount']) > 0){
$remaining_wallet_amount = floatval($remaining_wallet_amount) + floatval($cashbackamount['amount']);

$args = array(
	'user_id' => $provider_id,
	'amount' => $cashbackamount['amount'],
	'action' => 'credit',
	'debit_for' => $cashbackamount['description'],
	'payment_mode' => '',
	'payment_method' => '',
	'payment_status' => 'completed'
	);
	
service_finder_add_wallet_history($args);

}

update_user_meta($provider_id,'_sf_wallet_amount',$remaining_wallet_amount);

$currentpageurl = service_finder_get_my_account_url($provider_id);
$currentpageurl = add_query_arg( array('tabname' => 'job-limits'), $currentpageurl );

$msg = esc_html__('Payment has been made successfully', 'service-finder');
$success = array(
		'status' => 'success',
		'suc_message' => $msg,
		'redirect_url' => $currentpageurl
		);
echo json_encode($success);

exit;
} 

/*Get job limit transactions*/
add_action('wp_ajax_get_joblimits_txn', 'service_finder_get_joblimits_txn');
function service_finder_get_joblimits_txn(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/jobs/MyJobs.php';
$gettxns = new SERVICE_FINDER_MyJobs();
$gettxns->service_finder_getjoblimits_txn($_POST);
exit;
}

/*Load Applied Job*/
add_action('wp_ajax_load_applied_job', 'service_finder_load_applied_job');
function service_finder_load_applied_job(){
global $wpdb;
$current_user = wp_get_current_user();
$jobid = (!empty($_POST['jobid'])) ? $_POST['jobid'] : '';
$user_id = (!empty($_POST['user_id'])) ? $_POST['user_id'] : '';
$cost = get_user_meta($user_id,'job_applications_cost',true);
$description = get_user_meta($user_id,'job_applications_description',true);

$alljobdescs = explode('%NEXT%',$description);
if(!empty($alljobdescs)){
foreach($alljobdescs as $desc){
	$temp = explode('%SEP%',$desc);
	if(!empty($temp)){
		if($temp[0] == $jobid){
		$editdesc = $temp[1];
		break;
		}
	}
}
}

$alljobcosts = explode(',',$cost);
if(!empty($alljobcosts)){
foreach($alljobcosts as $cost){
	$temp2 = explode('-',$cost);
	if(!empty($temp2)){
		if($temp2[0] == $jobid){
		$editcost = $temp2[1];
		break;
		}
	}
}
}

if(!empty($editdesc) || !empty($editcost)){

		$result = array(
				'editdesc' => $editdesc,
				'editcost' => $editcost,
		);

}
echo json_encode($result);

exit;
}

/*Job Limit via wire transfer*/
if(isset($_POST['payment_mode']) && $payment_mode == 'wired' && isset($_POST['joblimit-payment'])){
global $wpdb, $service_finder_options, $service_finder_Tables;

$wired_array = array();

$provider_id = (isset($_POST['provider_id'])) ? esc_html($_POST['provider_id']) : '';
$plan = (isset($_POST['plan'])) ? esc_html($_POST['plan']) : '';

$planlimit = (!empty($service_finder_options['plan'.$plan.'-limit'])) ? $service_finder_options['plan'.$plan.'-limit'] : 0;
$planprice = (!empty($service_finder_options['plan'.$plan.'-price'])) ? $service_finder_options['plan'.$plan.'-price'] : 0;

$row = $wpdb->get_row('SELECT * FROM '.$service_finder_Tables->job_limits.' WHERE `provider_id` = "'.$provider_id.'"');
if(!empty($row)){
	$paidlimit = $planlimit + $row->paid_limits;
	$available_limits = $planlimit + $row->available_limits;
}else{
	$paidlimit = $planlimit;
	$available_limits = $planlimit;
}

$paydate = date('Y-m-d h:i:s');

$wired_array['date'] = $paydate;
$wired_array['paid_limits'] = $paidlimit;
$wired_array['available_limits'] = $available_limits;
$wired_array['current_plan'] = $plan;
$wired_array['planprice'] = $planprice;
$wired_array['limit'] = $planlimit;

$invoiceid = strtoupper(uniqid('JOB-'));
$wired_array['wired_invoiceid'] = $invoiceid;

delete_user_meta($provider_id, 'job_connect_request');
delete_user_meta($provider_id, 'job_connect_request_status');

update_user_meta($provider_id, 'job_connect_request',$wired_array);
update_user_meta($provider_id, 'job_connect_request_status','pending');

$data = array(
		'payment_type' => 'local',
		'payment_method' => 'wire-transfer',
		'payment_status' => 'on-hold',
		'txn_id' => $invoiceid,
		);
$where = array(
		'provider_id' => $provider_id
);
$res = $wpdb->update($service_finder_Tables->job_limits,wp_unslash($data),$where);

$paydate = date('Y-m-d h:i:s');
$txndata = array(
		'provider_id' => $provider_id,
		'payment_date' => $paydate,
		'plan' => $plan,
		'amount' => $planprice,
		'limit' => $planlimit,
		'txn_id' => $invoiceid,
		'payment_type' => 'local',
		'payment_method' => 'wire-transfer',
		'payment_status' => 'on-hold',
		);
$wpdb->insert($service_finder_Tables->transaction,wp_unslash($txndata));

service_finder_send_job_apply_wiretansfer_mail($provider_id,$invoiceid);

// show messages
$currentpageurl = service_finder_get_my_account_url($provider_id);
$currentpageurl = add_query_arg( array('tabname' => 'job-limits','joblimitplanupdate' => 'success'), $currentpageurl );
wp_redirect($currentpageurl );
die;

}

/*Job apply limit via wire transfer*/
function service_finder_send_job_apply_wiretansfer_mail($provider_id,$invoiceid){
global $wpdb, $service_finder_Tables, $service_finder_options;

$subject = esc_html__('Invoice ID for Job Apply Limit via Wire Transfer', 'service-finder');

$email = service_finder_getProviderEmail($provider_id);

$wiretransfermailinstructions = (!empty($service_finder_options['wire-transfer-mail-instructions'])) ? $service_finder_options['wire-transfer-mail-instructions'] : '';
if($wiretransfermailinstructions != ''){
	$message = $wiretransfermailinstructions;
}else{
	$message = 'Use following invoice ID When transfer amount in bank.';
}

$message .= esc_html__('Invoice ID:', 'service-finder').$invoiceid;

service_finder_wpmailer($email,$subject,$message);
}