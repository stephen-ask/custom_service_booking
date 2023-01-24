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
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$payment_mode = (isset($_POST['payment_mode'])) ? $_POST['payment_mode'] : '';
/*Featured via paypal*/
if(isset($_POST['payment_mode']) && $payment_mode == 'paypal' && isset($_POST['feature-payment'])){
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/upgrade/ProfileUpgrade.php';

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
$feature_id = (isset($_POST['feature_id'])) ? $_POST['feature_id'] : '';
$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->feature.' WHERE `id` = %d',$feature_id));
$userdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$wpdb->users.' WHERE `ID` = %d',$row->provider_id));
$currencyCode = service_finder_currencycode();
	$returnUrl = home_url("?featurepayment_made=success");
	$cancelUrl = home_url("?featurepayment_made=cancel");
	$urlParams = array(
		'RETURNURL' => $returnUrl,
		'CANCELURL' => $cancelUrl
	);
					
	$orderParams = array(
		'PAYMENTREQUEST_0_AMT' => $row->amount,
		'PAYMENTREQUEST_0_SHIPPINGAMT' => '0',
		'PAYMENTREQUEST_0_CURRENCYCODE' => strtoupper($currencyCode),
		'PAYMENTREQUEST_0_ITEMAMT' => $row->amount
	);
	$itemParams = array(
		'L_PAYMENTREQUEST_0_NAME0' => 'Payment via paypal',
		'L_PAYMENTREQUEST_0_DESC0' => 'Payment made for feature account',
		'L_PAYMENTREQUEST_0_AMT0' => $row->amount,
		'L_PAYMENTREQUEST_0_QTY0' => '1'
	);
	$params = $urlParams + $orderParams + $itemParams;
	$response = $paypal -> request('SetExpressCheckout',$params);
	$errors = new WP_Error();
	if(!$response){
		$errorMessage = esc_html__( 'ERROR:', 'service-finder' );
		$detailErrorMessage = reset($paypal->getErrors());
		$errors->add( 'bad_paypal_api', $errorMessage . ' ' . $detailErrorMessage );
		$registerErrors = $errors;
	}
	
	// Request successful
	if(is_array($response) && $response['ACK'] == 'Success') {
		// write token to DB
		$token = $response['TOKEN'];
		$makePayment = new SERVICE_FINDER_ProfileUpgrade();
		$makePayment->service_finder_makePaypalPayment($_POST,$token);
		// go to payment site
		header( 'Location: https://www.'.$sandbox.'paypal.com/webscr?cmd=_express-checkout&token=' . urlencode($token) );
		die();

	} else {
		$errorMessage = esc_html__( 'ERROR:', 'service-finder' );
		$detailErrorMessage = (isset($response['L_LONGMESSAGE0'])) ? $response['L_LONGMESSAGE0'] : '';
		$errors->add( 'bad_paypal_api', $errorMessage . ' ' . $detailErrorMessage );
		$registerErrors = $errors;
	}
}

// check token (paypal merchant authorization) and Do Payment
if(isset($_GET['featurepayment_made']) && ($_GET['featurepayment_made'] == 'success') && !empty($_GET['token'])) {


	// find token
	$service_finder_options = get_option('service_finder_options');
	$service_finder_Tables = service_finder_plugin_global_vars('service_finder_Tables');
	$registerErrors = service_finder_plugin_global_vars('registerErrors');
	$registerMessages = service_finder_plugin_global_vars('registerMessages');

	$token = $_GET['token'];
	$tokenRow = $wpdb->get_row( $wpdb->prepare("SELECT * FROM ".$service_finder_Tables->feature." WHERE `paypal_token` = '%s'",$token) );
	if(!empty($tokenRow)){
		
		// get checkout details from token
		$checkoutDetails = $paypal -> request('GetExpressCheckoutDetails', array('TOKEN' => $_GET['token']));
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
					
					$date = date('Y-m-d H:i:s');
					$data = array(
							'paymenttype' => 'paypal',
							'paypal_transaction_id' => $transactionId,
							'status' => 'Paid',
							'feature_status' => 'active',
							'date' => $date,
							);
					$where = array(
							'paypal_token' => esc_attr($_GET['token'])
					);
					$res = $wpdb->update($service_finder_Tables->feature,wp_unslash($data),$where);
					
					$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->feature.' WHERE `paypal_token` = %s',$_GET['token']));
					
					$data = array(
							'featured' => 1,
							);
					$where = array(
							'wp_user_id' => $row->provider_id,
							);
					$wpdb->update($service_finder_Tables->providers,wp_unslash($data),$where);
					
					// show messages
					$registerMessages = esc_html__('Your payment have been made successfully.','service-finder');
					$redirect = service_finder_get_url_by_shortcode('[service_finder_thank_you]').'?featured=success';
					wp_redirect($redirect);
					die;

				}

			}

		}
}

// delete token and show messages if user cancel payment 
if(isset($_GET['featurepayment_made']) && ($_GET['featurepayment_made'] == 'cancel') && !empty($_GET['token'])){
	// delete token from DB
	$registerErrors = service_finder_plugin_global_vars('registerErrors');
	
	$token = $_GET['token'];
	$tokenRow = $wpdb->get_row( $wpdb->prepare("SELECT * FROM ".$service_finder_Tables->feature." WHERE `paypal_token` = '%s'",$token) );
	if($tokenRow){
		
		$wpdb->query($wpdb->prepare("UPDATE ".$service_finder_Tables->feature." SET `paypal_token` = '' WHERE `paypal_token` = '%s'",$token));
		
		$errors = new WP_Error();
		$message = esc_html__("You canceled payment. Your payment wasn't made","aone");
		$errors->add( 'cancel_payment', $message);
		$registerErrors = $errors;
	}	
	
}
/*Featured via paypal end*/

/*Featured via payu money start*/
if(isset($_POST['payment_mode']) && $payment_mode == 'payumoney' && isset($_POST['feature-payment'])){
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

$surl = home_url("?featurepayment=success&payutransaction=success");
$furl = home_url("?featurepayment=failed&payutransaction=failed");

$txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
$action = $PAYU_BASE_URL . '/_payment';

$feature_id = (isset($_POST['feature_id'])) ? esc_html($_POST['feature_id']) : '';
$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->feature.' WHERE `id` = %d',$feature_id));
$userdata = service_finder_getUserInfo($row->provider_id);

$price = $row->amount;

$productinfo = 'Payment for Featured';
$first_name = $userdata['fname'];
$user_email = $userdata['email'];
$phone = $userdata['phone'];

$str = "$MERCHANT_KEY|$txnid|$price|$productinfo|$first_name|$user_email|$feature_id||||||||||$SALT";

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
	'udf1' 			=> $feature_id,
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

if(isset($_GET['featurepayment']) && $_GET['featurepayment'] == 'success' && $_GET['payutransaction'] == 'success' && isset($_GET['payutransaction']) && isset($_POST['mihpayid']) && isset($_POST['status'])){

$feature_id = (isset($_POST['udf1'])) ? esc_html($_POST['udf1']) : '';
$txnid = (isset($_POST['txnid'])) ? esc_html($_POST['txnid']) : '';
$payuMoneyId = (isset($_POST['mihpayid'])) ? esc_html($_POST['mihpayid']) : '';
$status = (isset($_POST['status'])) ? esc_html($_POST['status']) : '';

if($status == 'success' && $payuMoneyId != ""){

$date = date('Y-m-d H:i:s');
$data = array(
		'paymenttype' => 'payumoney',
		'txnid' => $txnid,
		'payumoneyid' => $payuMoneyId,
		'status' => 'Paid',
		'feature_status' => 'active',
		'date' => $date,
		);
$where = array(
		'id' => esc_attr($feature_id)
);
$res = $wpdb->update($service_finder_Tables->feature,wp_unslash($data),$where);

$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->feature.' WHERE `id` = %d',$feature_id));
					
$data = array(
		'featured' => 1,
		);
$where = array(
		'wp_user_id' => $row->provider_id,
		);
$wpdb->update($service_finder_Tables->providers,wp_unslash($data),$where);

$redirect = service_finder_get_url_by_shortcode('[service_finder_thank_you]').'?featured=success';
wp_redirect($redirect);
die;

}

}

if(isset($_GET['featurepayment']) && $_GET['featurepayment'] == 'failed' && $_GET['payutransaction'] == 'failed'){

$registerErrors = service_finder_plugin_global_vars('registerErrors');

$errors = new WP_Error();
$message = esc_html__("You canceled payment. Your payment wasn't made","aone");
$errors->add( 'cancel_payment', $message);
$registerErrors = $errors;

}
/*Featured via payu money end*/

/*Featured via wire transfer*/
if(isset($_POST['payment_mode']) && $payment_mode == 'wired' && isset($_POST['feature-payment'])){

$feature_id = (isset($_POST['feature_id'])) ? sanitize_text_field($_POST['feature_id']) : '';
$invoiceid = strtoupper(uniqid('BK-'));
$date = date('Y-m-d H:i:s');
$data = array(
		'paymenttype' => 'wire-transfer',
		'txnid' => $invoiceid,
		'status' => 'on-hold',
		'date' => $date,
		);
$where = array(
		'id' => esc_attr($feature_id)
);
$res = $wpdb->update($service_finder_Tables->feature,wp_unslash($data),$where);

service_finder_send_wiretansfer_mail($feature_id,$invoiceid);

$redirect = service_finder_get_url_by_shortcode('[service_finder_thank_you]').'?featured=wired';
wp_redirect($redirect);
exit;
}

/*Featured via wire transfer*/
function service_finder_send_wiretansfer_mail($feature_id,$invoiceid){
global $wpdb, $service_finder_Tables, $service_finder_options;

$subject = esc_html__('Invoice ID for Featured via Wire Transfer', 'service-finder');

$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->feature.' WHERE `id` = %d',$feature_id));

if(!empty($row)){
$userid = $row->provider_id;
$email = service_finder_getProviderEmail($userid);

$wiretransfermailinstructions = (!empty($service_finder_options['wire-transfer-mail-instructions'])) ? $service_finder_options['wire-transfer-mail-instructions'] : '';
if($wiretransfermailinstructions != ''){
	$message = $wiretransfermailinstructions;
}else{
	$message = 'Use following invoice ID When transfer amount in bank.';
}

$message .= esc_html__('Invoice ID:', 'service-finder').$invoiceid;

service_finder_wpmailer($email,$subject,$message);

}
}

/*Delete Declined request*/
add_action('wp_ajax_delete_decline_request', 'service_finder_delete_decline_request');
add_action('wp_ajax_nopriv_delete_decline_request', 'service_finder_delete_decline_request');

function service_finder_delete_decline_request(){
global $wpdb, $service_finder_Tables;

$providerid = (isset($_POST['providerid'])) ? esc_attr($_POST['providerid']) : '';

$where = array(
		'provider_id' => $providerid,
		);

$wpdb->delete($service_finder_Tables->feature,$where);

exit;
}

/*Cancel membership*/
add_action('wp_ajax_cancel_membership', 'service_finder_cancel_membership');
add_action('wp_ajax_nopriv_cancel_membership', 'service_finder_cancel_membership');

function service_finder_cancel_membership(){
global $wpdb, $service_finder_Tables, $current_user;

$providerid = (isset($_POST['providerid'])) ? esc_attr($_POST['providerid']) : '';

$data = array(
		'account_blocked' => 'yes',
		'status' => 'draft',
		);

$where = array(
		'wp_user_id' => $providerid,
		);
		
$wpdb->update($service_finder_Tables->providers,wp_unslash($data),$where);
$redirect_uri = '';
if(service_finder_getUserRole($current_user->ID) == 'Provider'){
	wp_logout();
	$redirect_uri = add_query_arg( array('cancel_membership_id' => $providerid), home_url() );
}

$success = array(
		'status' => 'success',
		'suc_message' => esc_html__('Your membership has been cancelled successfully.', 'service-finder'),
		'display_message' => service_finder_getUserRole($providerid).esc_html__('Your membership has been cancelled.', 'service-finder'),
		'redirect' => $redirect_uri,
		);
echo json_encode($success);

exit;
}

/*Approve after wire transfer*/
add_action('wp_ajax_approve_after_wire_transfer', 'service_finder_approve_after_wire_transfer');
add_action('wp_ajax_nopriv_approve_after_wire_transfer', 'service_finder_approve_after_wire_transfer');

function service_finder_approve_after_wire_transfer(){
global $wpdb, $service_finder_Tables;

$feature_id = (isset($_POST['featureid'])) ? sanitize_text_field($_POST['featureid']) : '';
$date = date('Y-m-d H:i:s');
$data = array(
		'status' => 'Paid',
		'date' => $date,
		'feature_status' => 'active',
		);
$where = array(
		'id' => esc_attr($feature_id)
);
$res = $wpdb->update($service_finder_Tables->feature,wp_unslash($data),$where);

$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->feature.' WHERE `id` = %d',$feature_id));
					
$data = array(
		'featured' => 1,
		);
$where = array(
		'wp_user_id' => $row->provider_id,
		);
$wpdb->update($service_finder_Tables->providers,wp_unslash($data),$where);

$success = array(
		'status' => 'success',
		'suc_message' => esc_html__('Approved successfully.', 'service-finder'),
		);
$service_finder_Success = json_encode($success);
echo $service_finder_Success;
exit;
}

/*Make feature request*/
add_action('wp_ajax_make_feature', 'service_finder_make_feature');
add_action('wp_ajax_nopriv_make_feature', 'service_finder_make_feature');

function service_finder_make_feature(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/upgrade/ProfileUpgrade.php';
$reqFeature = new SERVICE_FINDER_ProfileUpgrade();
$reqFeature->service_finder_FeatureRequest($_POST);
exit;
}

/*Make payment for feature via stripe*/
add_action('wp_ajax_feature_payment', 'service_finder_feature_payment');
add_action('wp_ajax_nopriv_feature_payment', 'service_finder_feature_payment');

function service_finder_feature_payment(){
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/upgrade/ProfileUpgrade.php';
global $wpdb, $stripe_options, $service_finder_options, $service_finder_Tables;
		$feature_id = (isset($_POST['feature_id'])) ? $_POST['feature_id'] : '';
		$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->feature.' WHERE `id` = %d',$feature_id));
		$userdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$wpdb->users.' WHERE `ID` = %d',$row->provider_id));
		
		
		$token = $_POST['stripeToken'];
		$totalcost = $row->amount * 100;
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
					'description' => "Payment made for feature"
				)
			);	

			$charge = \Stripe\Charge::create(array(
						  "amount" => $totalcost,
						  "currency" => strtolower(service_finder_currencycode()),
						  "customer" => $customer->id, // obtained with Stripe.js
						  "description" => "Charge to be make feature"
						));

			if ($charge->paid == true && $charge->status == "succeeded") { 
			
				$makePayment = new SERVICE_FINDER_ProfileUpgrade();
				
				$txnid = $charge->balance_transaction;
				
				$makePayment->service_finder_makePayment($_POST,$customer->id,$txnid,'stripe');
				$msg = (!empty($service_finder_options['feature-payment'])) ? $service_finder_options['feature-payment'] : esc_html__('Payment made successfully to be featured', 'service-finder');
				$feature_id = (!empty($_POST['feature_id'])) ? esc_attr($_POST['feature_id']) : '';
				$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->feature.' WHERE `id` = %d',$feature_id));
				
				$limit = floatval($row->days);
				
				$displaymsg = (!empty($service_finder_options['featured-account'])) ? $service_finder_options['featured-account'] : esc_html__('Now you are a featured member. You have %REMAININGDAYS% days remaining to expire your feature account.', 'service-finder');
				
				$displaymsg = str_replace('%REMAININGDAYS%',$limit,$displaymsg);
														
				$success = array(
						'status' => 'success',
						'suc_message' => $msg,
						'display_message' => $displaymsg,
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

/*Make payment for feature via wallet*/
add_action('wp_ajax_feature_wallet', 'service_finder_feature_wallet');
add_action('wp_ajax_nopriv_feature_wallet', 'service_finder_feature_wallet');

function service_finder_feature_wallet(){
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/upgrade/ProfileUpgrade.php';
global $wpdb, $stripe_options, $service_finder_options, $service_finder_Tables;
$feature_id = (isset($_POST['feature_id'])) ? $_POST['feature_id'] : '';
$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->feature.' WHERE `id` = %d',$feature_id));

$walletamount = service_finder_get_wallet_amount($row->provider_id);

if(floatval($walletamount) < floatval($row->amount)){
$error = array(
'status' => 'error',
'err_message' => 'insufficient_amount'
);
echo json_encode($error);
exit(0);
}
		
$makePayment = new SERVICE_FINDER_ProfileUpgrade();

$makePayment->service_finder_makePayment($_POST,'','','wallet');

$remaining_wallet_amount = floatval($walletamount) - floatval($row->amount); 


$args = array(
	'user_id' => $row->provider_id,
	'amount' => $row->amount,
	'action' => 'debit',
	'debit_for' => esc_html__('For Featured', 'service-finder'),
	'payment_mode' => 'local',
	'payment_method' => 'wallet',
	'payment_status' => 'completed'
	);
	
service_finder_add_wallet_history($args);

$cashbackamount = service_finder_cashback_amount('featured');

if(floatval($cashbackamount['amount']) > 0){
$remaining_wallet_amount = floatval($remaining_wallet_amount) + floatval($cashbackamount['amount']);

$args = array(
	'user_id' => $row->provider_id,
	'amount' => $cashbackamount['amount'],
	'action' => 'credit',
	'debit_for' => $cashbackamount['description'],
	'payment_mode' => '',
	'payment_method' => '',
	'payment_status' => 'completed'
	);
	
service_finder_add_wallet_history($args);

}

update_user_meta($row->provider_id,'_sf_wallet_amount',$remaining_wallet_amount);

$msg = (!empty($service_finder_options['feature-payment'])) ? $service_finder_options['feature-payment'] : esc_html__('Payment made successfully to be featured', 'service-finder');

$limit = floatval($row->days);
	
$displaymsg = (!empty($service_finder_options['featured-account'])) ? $service_finder_options['featured-account'] : esc_html__('Now you are a featured member. You have %REMAININGDAYS% days remaining to expire your feature account.', 'service-finder');

$displaymsg = str_replace('%REMAININGDAYS%',$limit,$displaymsg);
									
$success = array(
	'status' => 'success',
	'suc_message' => $msg,
	'display_message' => $displaymsg,
	);
echo json_encode($success);

exit;
} 

/*Make payment for feature via PayU Latam*/
add_action('wp_ajax_payulatam_feature_payment', 'service_finder_payulatam_feature_payment');
add_action('wp_ajax_nopriv_payulatam_feature_payment', 'service_finder_payulatam_feature_payment');

function service_finder_payulatam_feature_payment(){
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/upgrade/ProfileUpgrade.php';
global $wpdb, $stripe_options, $service_finder_options, $service_finder_Tables;

$feature_id = (isset($_POST['feature_id'])) ? esc_html($_POST['feature_id']) : '';
$fcd_number = (isset($_POST['payulatam_fcd_number'])) ? esc_html($_POST['payulatam_fcd_number']) : '';
$fcd_cvc = (isset($_POST['payulatam_fcd_cvc'])) ? esc_html($_POST['payulatam_fcd_cvc']) : '';
$fcd_month = (isset($_POST['payulatam_fcd_month'])) ? esc_html($_POST['payulatam_fcd_month']) : '';
$fcd_year = (isset($_POST['payulatam_fcd_year'])) ? esc_html($_POST['payulatam_fcd_year']) : '';
$cardtype = (isset($_POST['payulatam_f_cardtype'])) ? esc_html($_POST['payulatam_f_cardtype']) : '';
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


$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->feature.' WHERE `id` = %d',$feature_id));

$userdata = service_finder_getUserInfo($row->provider_id);

$fullname = $userdata['fname'].' '.$userdata['lname'];
$user_email = $userdata['email'];
$phone = $userdata['phone'];

require_once(SERVICE_FINDER_PAYMENT_GATEWAY_DIR.'/payulatam/lib/PayU.php');

if( isset($service_finder_options['payulatam-type']) && $service_finder_options['payulatam-type'] == 'test' ){
	$testmode = true;
	$payulatammerchantid = (isset($service_finder_options['payulatam-merchantid-test'])) ? $service_finder_options['payulatam-merchantid-test'] : '';
	$payulatamapilogin = (isset($service_finder_options['payulatam-apilogin-test'])) ? $service_finder_options['payulatam-apilogin-test'] : '';
	$payulatamapikey = (isset($service_finder_options['payulatam-apikey-test'])) ? $service_finder_options['payulatam-apikey-test'] : '';
	$payulatamaccountid = (isset($service_finder_options['payulatam-accountid-test'])) ? $service_finder_options['payulatam-accountid-test'] : '';
	
	$paymenturl = "https://sandbox.api.payulatam.com/payments-api/4.0/service.cgi";
	$reportsurl = "https://sandbox.api.payulatam.com/reports-api/4.0/service.cgi";
	$subscriptionurl = "https://sandbox.api.payulatam.com/payments-api/rest/v4.3/";
	
	$fullname = 'APPROVED';
	
}else{
	$testmode = false;
	$payulatammerchantid = (isset($service_finder_options['payulatam-merchantid-live'])) ? $service_finder_options['payulatam-merchantid-live'] : '';
	$payulatamapilogin = (isset($service_finder_options['payulatam-apilogin-live'])) ? $service_finder_options['payulatam-apilogin-live'] : '';
	$payulatamapikey = (isset($service_finder_options['payulatam-apikey-live'])) ? $service_finder_options['payulatam-apikey-live'] : '';
	$payulatamaccountid = (isset($service_finder_options['payulatam-accountid-live'])) ? $service_finder_options['payulatam-accountid-live'] : '';
	
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

$reference = $feature_id.'_'.time();
$value = $row->amount;

try {

$parameters = array(
	//Enter the account’s identifier here
	PayUParameters::ACCOUNT_ID => $payulatamaccountid,
	// Enter the reference code here.
	PayUParameters::REFERENCE_CODE => $reference,
	// Enter the description here.
	PayUParameters::DESCRIPTION => "Payment for Featured via PayU Latam",
	
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
	PayUParameters::CREDIT_CARD_NUMBER => $fcd_number,
	// Enter expiration date of the credit card here
	PayUParameters::CREDIT_CARD_EXPIRATION_DATE => $fcd_year.'/'.$fcd_month,
	//Enter the security code of the credit card here
	PayUParameters::CREDIT_CARD_SECURITY_CODE=> $fcd_cvc,
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
	
	$makePayment = new SERVICE_FINDER_ProfileUpgrade();
	
	$txnid = $response->transactionResponse->transactionId;
	
	//$makePayment->service_finder_makePayment($_POST,'',$txnid,'payulatam');
	$msg = (!empty($service_finder_options['feature-payment'])) ? $service_finder_options['feature-payment'] : esc_html__('Payment made successfully to be featured', 'service-finder');
	
	$limit = floatval($row->days);
	
	$displaymsg = (!empty($service_finder_options['featured-account'])) ? $service_finder_options['featured-account'] : esc_html__('Now you are a featured member. You have %REMAININGDAYS% days remaining to expire your feature account.', 'service-finder');
	
	$displaymsg = str_replace('%REMAININGDAYS%',$limit,$displaymsg);
											
	$success = array(
			'status' => 'success',
			'suc_message' => $msg,
			'display_message' => $displaymsg,
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

/*Make payment for feature via twocheckout*/
add_action('wp_ajax_twocheckout_feature_payment', 'service_finder_twocheckout_feature_payment');
add_action('wp_ajax_nopriv_twocheckout_feature_payment', 'service_finder_twocheckout_feature_payment');

function service_finder_twocheckout_feature_payment(){
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/upgrade/ProfileUpgrade.php';
global $wpdb, $stripe_options, $service_finder_options, $service_finder_Tables, $current_user;
$feature_id = (isset($_POST['feature_id'])) ? $_POST['feature_id'] : '';
$token = (!empty($_POST['twocheckouttoken'])) ? esc_html($_POST['twocheckouttoken']) : '';
$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->feature.' WHERE `id` = %d',$feature_id));
$userdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$wpdb->users.' WHERE `ID` = %d',$row->provider_id));

$totalcost = $row->amount;

$twocheckouttype = (!empty($service_finder_options['twocheckout-type'])) ? esc_html($service_finder_options['twocheckout-type']) : '';

if($twocheckouttype == 'live'){
	$private_key = (!empty($service_finder_options['twocheckout-live-private-key'])) ? esc_html($service_finder_options['twocheckout-live-private-key']) : '';
	$twocheckoutaccountid = (!empty($service_finder_options['twocheckout-live-account-id'])) ? esc_html($service_finder_options['twocheckout-live-account-id']) : '';
}else{
	$private_key = (!empty($service_finder_options['twocheckout-test-private-key'])) ? esc_html($service_finder_options['twocheckout-test-private-key']) : '';
	$twocheckoutaccountid = (!empty($service_finder_options['twocheckout-test-account-id'])) ? esc_html($service_finder_options['twocheckout-test-account-id']) : '';
}

require_once(SERVICE_FINDER_PAYMENT_GATEWAY_DIR.'/2checkout/lib/Twocheckout.php');
Twocheckout::privateKey($private_key);
Twocheckout::sellerId($twocheckoutaccountid);

if($twocheckouttype == 'test'){
Twocheckout::verifySSL(false);
Twocheckout::sandbox(true);
}

try {
	
	
	$carduserinfo = service_finder_getUserInfo($current_user->ID);

	$charge = Twocheckout_Charge::auth(array(
        "sellerId" => $twocheckoutaccountid,
		"privateKey" => $private_key,
	    "merchantOrderId" => time(),
        "token" => $token,
        "currency" => strtoupper(service_finder_currencycode()),
        "total" => $totalcost,
		"tangible"    => "N",
		"billingAddr" => array(
			"name" => $carduserinfo['fname'].' '.$carduserinfo['lname'],
			"addrLine1" => $carduserinfo['address'],
			"city" => $carduserinfo['city'],
			"state" => $carduserinfo['state'],
			"zipCode" => $carduserinfo['zipcode'],
			"country" => $carduserinfo['country'],
			"email" => $current_user->user_email,
			"phoneNumber" => $carduserinfo['phone']
		),
    ));
    if ($charge['response']['responseCode'] == 'APPROVED') {
	
		$transactionid = $charge['response']['transactionId'];
			
		$makePayment = new SERVICE_FINDER_ProfileUpgrade();
		
		//$makePayment->service_finder_makePayment($_POST,'',$transactionid,'twocheckout');
		$msg = (!empty($service_finder_options['feature-payment'])) ? $service_finder_options['feature-payment'] : esc_html__('Payment made successfully to be featured', 'service-finder');
		$success = array(
				'status' => 'success',
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

/*Cancel Featured/Featured Request*/
add_action('wp_ajax_cancel_provider_featured', 'service_finder_cancel_provider_featured');
add_action('wp_ajax_nopriv_cancel_provider_featured', 'service_finder_cancel_provider_featured');

function service_finder_cancel_provider_featured(){
global $wpdb, $service_finder_options, $service_finder_Tables, $current_user;
$userid = (isset($_POST['userid'])) ? $_POST['userid'] : '';

if(service_finder_getUserRole($current_user->ID) == 'administrator'){
$manageprofilelink = add_query_arg( array('manageaccountby' => 'admin','manageproviderid' => esc_attr($userid),'cancelfeatured' => 'success'), service_finder_get_url_by_shortcode('[service_finder_my_account') );
}else{
$manageprofilelink = add_query_arg( array('cancelfeatured' => 'success'), service_finder_get_url_by_shortcode('[service_finder_my_account') );
}

$where = array(
		'provider_id' => esc_attr($userid),
		);

$wpdb->delete($service_finder_Tables->feature,$where);

$msg = (!empty($service_finder_options['featured-cancel'])) ? $service_finder_options['featured-cancel'] : esc_html__('Featured/Featured Request Cancelled successfully', 'service-finder');

$subject = esc_html__('Featured/Featured Request Cancelled', 'service-finder');

$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('Provider', 'service-finder');

$name = service_finder_getProviderName($userid);
$email = service_finder_getProviderEmail($userid);
$message = '<h4>Featured/Featured Request has been cancelled by '.$providerreplacestring.'</h4>

<h3>'.$providerreplacestring.' Details</h3>

'.$providerreplacestring.' Name: '.service_finder_get_providername_with_link($userid).'

'.$providerreplacestring.' Email: '.$email;

service_finder_wpmailer(get_option('admin_email'),$subject,$message);

$success = array(
		'status' => 'success',
		'suc_message' => $msg,
		'redirect' => $manageprofilelink,
		);
echo json_encode($success);

exit;

}

/*Cancel Subscription*/
add_action('wp_ajax_cancel_provider_subscription', 'service_finder_cancel_provider_subscription');
add_action('wp_ajax_nopriv_cancel_provider_subscription', 'service_finder_cancel_provider_subscription');

function service_finder_cancel_provider_subscription(){
global $wpdb, $service_finder_Errors, $service_finder_options, $paypal;
$userid = (isset($_POST['userid'])) ? $_POST['userid'] : '';

$creds = array();
$paypalCreds['USER'] = (isset($service_finder_options['paypal-username'])) ? $service_finder_options['paypal-username'] : '';
$paypalCreds['PWD'] = (isset($service_finder_options['paypal-password'])) ? $service_finder_options['paypal-password'] : '';
$paypalCreds['SIGNATURE'] = (isset($service_finder_options['paypal-signatue'])) ? $service_finder_options['paypal-signatue'] : '';
$paypalType = (isset($service_finder_options['paypal-type']) && $service_finder_options['paypal-type'] == 'live') ? '' : 'sandbox.';

$paypalTypeBool = (!empty($paypalType)) ? true : false;

$paypal = new Paypal($paypalCreds,$paypalTypeBool);

$subscription_id = get_user_meta($userid,'subscription_id',true);
$cusID = get_user_meta($userid,'stripe_customer_id',true);
$orderNumber = get_user_meta($userid,'orderNumber',true);
$merchantOrderId = get_user_meta($userid,'merchantOrderId',true);
$payment_mode = get_user_meta($userid,'payment_mode',true);
$oldProfile = get_user_meta($userid,'recurring_profile_id',true);

$payulatam_planid = get_user_meta($userId,'payulatam_planid',true);

if($subscription_id != "" && ($payment_mode == 'stripe' || $payment_mode == 'stripe_upgrade')){
require_once(SERVICE_FINDER_PAYMENT_GATEWAY_DIR.'/stripe/init.php');

if( isset($service_finder_options['stripe-type']) && $service_finder_options['stripe-type'] == 'test' ){
	$secret_key = (!empty($service_finder_options['stripe-test-secret-key'])) ? $service_finder_options['stripe-test-secret-key'] : '';
	$public_key = (!empty($service_finder_options['stripe-test-public-key'])) ? $service_finder_options['stripe-test-public-key'] : '';
}else{
	$secret_key = (!empty($service_finder_options['stripe-live-secret-key'])) ? $service_finder_options['stripe-live-secret-key'] : '';
	$public_key = (!empty($service_finder_options['stripe-live-public-key'])) ? $service_finder_options['stripe-live-public-key'] : '';
}

	\Stripe\Stripe::setApiKey($secret_key);
	try {			

		$currentcustomer = \Stripe\Customer::retrieve($cusID);
		$res = $currentcustomer->subscriptions->retrieve($subscription_id)->cancel();
		if($res->status == 'canceled'){
		
		service_finder_cancel_subscription($userid,'manually');
		$msg = (!empty($service_finder_options['subscription-cancel'])) ? $service_finder_options['subscription-cancel'] : esc_html__('Subscription Cancelled successfully', 'service-finder');
		$success = array(
				'status' => 'success',
				'suc_message' => $msg,
				);
		echo json_encode($success);
		}else{
			$error = array(
					'status' => 'error',
					'err_message' => esc_html__('Subscription Cancel failed.', 'service-finder')
					);
			echo json_encode($error);
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
}elseif($payulatam_planid != "" && $subscription_id != "" && ($payment_mode == 'payulatam' || $payment_mode == 'payulatam_upgrade')){

require_once(SERVICE_FINDER_PAYMENT_GATEWAY_DIR.'/payulatam/lib/PayU.php');

if( isset($service_finder_options['payulatam-type']) && $service_finder_options['payulatam-type'] == 'test' ){
	$testmode = true;
	$payulatammerchantid = (isset($service_finder_options['payulatam-merchantid-test'])) ? $service_finder_options['payulatam-merchantid-test'] : '';
	$payulatamapilogin = (isset($service_finder_options['payulatam-apilogin-test'])) ? $service_finder_options['payulatam-apilogin-test'] : '';
	$payulatamapikey = (isset($service_finder_options['payulatam-apikey-test'])) ? $service_finder_options['payulatam-apikey-test'] : '';
	$payulatamaccountid = (isset($service_finder_options['payulatam-accountid-test'])) ? $service_finder_options['payulatam-accountid-test'] : '';
	
	$paymenturl = "https://sandbox.api.payulatam.com/payments-api/4.0/service.cgi";
	$reportsurl = "https://sandbox.api.payulatam.com/reports-api/4.0/service.cgi";
	$subscriptionurl = "https://sandbox.api.payulatam.com/payments-api/rest/v4.3/";
	
	$fullname = 'APPROVED';
	
}else{
	$testmode = false;
	$payulatammerchantid = (isset($service_finder_options['payulatam-merchantid-live'])) ? $service_finder_options['payulatam-merchantid-live'] : '';
	$payulatamapilogin = (isset($service_finder_options['payulatam-apilogin-live'])) ? $service_finder_options['payulatam-apilogin-live'] : '';
	$payulatamapikey = (isset($service_finder_options['payulatam-apikey-live'])) ? $service_finder_options['payulatam-apikey-live'] : '';
	$payulatamaccountid = (isset($service_finder_options['payulatam-accountid-live'])) ? $service_finder_options['payulatam-accountid-live'] : '';
	
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


try {
$parameters = array(
	// Enter the subscription ID here.
	PayUParameters::SUBSCRIPTION_ID => $subscription_id,
);

$response = PayUSubscriptions::cancel($parameters);

if($response){

	service_finder_cancel_subscription($userid,'manually');
	$msg = (!empty($service_finder_options['subscription-cancel'])) ? $service_finder_options['subscription-cancel'] : esc_html__('Subscription Cancelled successfully', 'service-finder');
	$success = array(
			'status' => 'success',
			'suc_message' => $msg,
			);
	echo json_encode($success);
	}else{
		$error = array(
				'status' => 'error',
				'err_message' => esc_html__('Subscription Cancel failed.', 'service-finder')
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

}elseif($merchantOrderId != "" && $orderNumber != "" && ($payment_mode == 'twocheckout' || $payment_mode == 'twocheckout_upgrade')){

	$twocheckouttype = (!empty($service_finder_options['twocheckout-type'])) ? esc_html($service_finder_options['twocheckout-type']) : '';
	
	require_once(SERVICE_FINDER_PAYMENT_GATEWAY_DIR.'/2checkout/lib/Twocheckout.php');
	
	Twocheckout::username('vikasapi085');
	Twocheckout::password('Demo#1985');
	
	if($twocheckouttype == 'test'){
	Twocheckout::verifySSL(false);
	Twocheckout::sandbox(true);
	}
	
	$args = array(
		 'sale_id' => $orderNumber
	);
	try {
		$result = Twocheckout_Sale::stop($args);
		
		service_finder_cancel_subscription($userid,'manually');
		$msg = (!empty($service_finder_options['subscription-cancel'])) ? $service_finder_options['subscription-cancel'] : esc_html__('Subscription Cancelled successfully', 'service-finder');
		$success = array(
				'status' => 'success',
				'suc_message' => $msg,
				);
		echo json_encode($success);
		
	} catch (Twocheckout_Error $e) {
		$e->getMessage();
		
		$error = array(
				'status' => 'error',
				'err_message' => sprintf( esc_html__('%s', 'service-finder'), $e->getMessage() )
				);
		echo json_encode($error);
	}

}elseif(!empty($oldProfile)) {
	$cancelParams = array(
		'PROFILEID' => $oldProfile,
		'ACTION' => 'Cancel'
	);
	$res = $paypal -> request('ManageRecurringPaymentsProfileStatus',$cancelParams);
	//echo '<pre>';print_r($res);echo '</pre>';
	if($res['ACK'] == 'Success'){
		service_finder_cancel_subscription($userid,'manually');
		$msg = (!empty($service_finder_options['subscription-cancel'])) ? $service_finder_options['subscription-cancel'] : esc_html__('Subscription Cancelled successfully', 'service-finder');
		$success = array(
				'status' => 'success',
				'suc_message' => $msg,
				);
		echo json_encode($success);
	}else{
		$error = array(
				'status' => 'error',
				'err_message' => $res['L_SHORTMESSAGE0']
				);
		echo json_encode($error);
	}
}else{
	service_finder_cancel_subscription($userid,'manually');
	$msg = (!empty($service_finder_options['subscription-cancel'])) ? $service_finder_options['subscription-cancel'] : esc_html__('Subscription Cancelled successfully', 'service-finder');
	$success = array(
			'status' => 'success',
			'suc_message' => $msg,
			);
	echo json_encode($success);
}

exit;
}

function service_finder_featured_payment_mail($providerid = 0){
global $wpdb, $service_finder_options, $service_finder_Tables;

$providerinfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$providerid));
$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->feature.' WHERE `provider_id` = %d',$providerid));

if($service_finder_options['provider-featured-payment-subject'] != ""){
	$msg_subject = $service_finder_options['provider-featured-payment-subject'];
}else{
	$msg_subject = esc_html__('Featured payament completed', 'service-finder');
}

if($service_finder_options['admin-featured-payment-subject'] != ""){
	$admin_msg_subject = $service_finder_options['admin-featured-payment-subject'];
}else{
	$admin_msg_subject = esc_html__('Featured payament completed', 'service-finder');
}

if($service_finder_options['provider-featured-payment-message'] != ""){
	$message = $service_finder_options['provider-featured-payment-message'];
}else{
	$message = '
	<h3>Featured payament completed</h3>
	<br>
	Now you are a featured member. You have %REMAININGDAYS% days remaining to expire your feature account..<br/>';
}

if($service_finder_options['admin-featured-payment-message'] != ""){
	$adminmessage = $service_finder_options['admin-featured-payment-message'];
}else{
	$adminmessage = '
	<h3>Featured payament completed</h3>
	<br>
	Provier has completed their featured payment.<br/>
	
	Provider Info:<br/>
	Provider Name: %PROVIDERNAME%
	Provider Email: %PROVIDEREMAIL%
	Provider Phone: %PROVIDERPHONE%
	';
}

$activationtimeInSec = strtotime($row->date);
$differenceInSec = time() - $activationtimeInSec;
$differenceInDays = floor($differenceInSec / 60 / 60 / 24);

$limit = floatval($row->days);

$remainingdays = $limit - $differenceInDays;

$admin_email = get_option( 'admin_email' );
$provider_email = service_finder_getProviderEmail($providerid);

$tokens = array('%PROVIDERNAME%','%PROVIDEREMAIL%','%PROVIDERPHONE%','%REMAININGDAYS%');
$replacements = array(service_finder_get_providername_with_link($providerinfo->wp_user_id),$provider_email,service_finder_get_contact_info($providerinfo->phone,$providerinfo->mobile),$remainingdays);
$adminmessage = str_replace($tokens,$replacements,$adminmessage);
$providermessage = str_replace($tokens,$replacements,$message);

service_finder_wpmailer($admin_email,$admin_msg_subject,$adminmessage);
service_finder_wpmailer($provider_email,$msg_subject,$providermessage);
}