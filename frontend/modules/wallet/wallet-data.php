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

$payment_mode = (isset($_POST['payment_mode'])) ? esc_html($_POST['payment_mode']) : '';
if(isset($_POST['payment_mode']) && $payment_mode == 'paypal' && isset($_POST['wallet-payment'])){
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

$provider_id = (!empty($_POST['user_id'])) ? base64_decode($_POST['user_id']) : '';
$amount = (!empty($_POST['amount'])) ? esc_attr($_POST['amount']) : 0;
$amount = floatval($amount);

$userdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$wpdb->users.' WHERE `ID` = %d',$provider_id));

$currencyCode = service_finder_currencycode();

	$returnUrl = add_query_arg( array('walletpayment_made' => 'success'), service_finder_get_url_by_shortcode('[service_finder_my_account') );
	$cancelUrl = add_query_arg( array('walletpayment_made' => 'cancel'), service_finder_get_url_by_shortcode('[service_finder_my_account') );
	$urlParams = array(
		'RETURNURL' => $returnUrl,
		'CANCELURL' => $cancelUrl
	);
					
	$orderParams = array(
		'PAYMENTREQUEST_0_AMT' => $amount,
		'PAYMENTREQUEST_0_SHIPPINGAMT' => '0',
		'PAYMENTREQUEST_0_CURRENCYCODE' => strtoupper($currencyCode),
		'PAYMENTREQUEST_0_ITEMAMT' => $amount
	);
	$itemParams = array(
		'L_PAYMENTREQUEST_0_NAME0' => 'Payment via paypal',
		'L_PAYMENTREQUEST_0_DESC0' => 'Payment made for increase wallet amount',
		'L_PAYMENTREQUEST_0_AMT0' => $amount,
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
		update_user_meta($provider_id,'add_to_wallet_paypal_token',$token);
		update_user_meta($provider_id,'add_to_wallet_paypal_amount',$amount);
		
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

// check token (paypal merchant authorization) and Do Payment
if(isset($_GET['walletpayment_made']) && ($_GET['walletpayment_made'] == 'success') && !empty($_GET['token'])) {

	// find token
	$service_finder_options = get_option('service_finder_options');
	$service_finder_Tables = service_finder_plugin_global_vars('service_finder_Tables');
	$registerErrors = service_finder_plugin_global_vars('registerErrors');
	$registerMessages = service_finder_plugin_global_vars('registerMessages');

	$token = (isset($_GET['token'])) ? esc_html($_GET['token']) : '';
	$tokenRow = $wpdb->get_row( $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."usermeta WHERE `meta_key` = 'add_to_wallet_paypal_token' AND `meta_value` = '%s'",$token) );
	
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
					
					$provider_id = $tokenRow->user_id;
					
					$amount = get_user_meta($provider_id,'add_to_wallet_paypal_amount',true);
					
					service_finder_add_wallet_amount($provider_id,$amount);
		
					$args = array(
						'user_id' => $provider_id,
						'amount' => $amount,
						'txn_id' => $transactionId,
						'action' => 'credit',
						'payment_mode' => 'local',
						'payment_method' => 'paypal',
						'payment_status' => 'completed'
						);
						
					service_finder_add_wallet_history($args);
					
					delete_user_meta($provider_id,'add_to_wallet_paypal_token');
					delete_user_meta($provider_id,'add_to_wallet_paypal_amount');
					
					if(service_finder_getUserRole($currUser->ID) == 'administrator'){
					$redirect = add_query_arg( array('manageaccountby' => 'admin','manageproviderid' => $provider_id,'tabname' => 'wallet','walletpaymentupdate' => 'success'), $currentpageurl );
					}elseif(service_finder_getUserRole($currUser->ID) == 'Provider'){
					$redirect = add_query_arg( array('tabname' => 'wallet','walletpaymentupdate' => 'success'), $currentpageurl );
					}elseif(service_finder_getUserRole($currUser->ID) == 'Customer'){
					$redirect = add_query_arg( array('action' => 'wallet','walletpaymentupdate' => 'success'), $currentpageurl );
					}
					
					wp_redirect($redirect);
					die;

				}

			}

		}
}

// delete token and show messages if user cancel payment 
if(isset($_GET['walletpayment_made']) && ($_GET['walletpayment_made'] == 'cancel') && !empty($_GET['token'])){
	// delete token from DB
	$registerErrors = service_finder_plugin_global_vars('registerErrors');
	
	// delete token from DB
	$token = (isset($_GET['token'])) ? esc_html($_GET['token']) : '';
	$tokenRow = $wpdb->get_row( $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."usermeta WHERE `meta_key` = 'add_to_wallet_paypal_token' AND `meta_value` = '%s'",$token) );
	if($tokenRow){
		
		$provider_id = $tokenRow->user_id;
		delete_user_meta($provider_id,'add_to_wallet_paypal_token');
		delete_user_meta($provider_id,'add_to_wallet_paypal_amount');
		
		$errors = new WP_Error();
		$message = esc_html__("You canceled payment. Your payment wasn't made","service-finder");
		$errors->add( 'cancel_payment', $message);
		$registerErrors = $errors;
	}	
	
}

/*Add amount to wallet via skip payment*/
if(isset($_POST['payment_mode']) && $payment_mode == 'skippayment' && isset($_POST['wallet-payment'])){
global $wpdb, $service_finder_options, $service_finder_Tables;

$provider_id = (!empty($_POST['user_id'])) ? base64_decode($_POST['user_id']) : '';
$amount = (!empty($_POST['amount'])) ? esc_attr($_POST['amount']) : 0;
$amount = floatval($amount);

service_finder_add_wallet_amount($provider_id,$amount);

$args = array(
	'user_id' => $provider_id,
	'amount' => $amount,
	'txn_id' => '-',
	'action' => 'credit',
	'payment_mode' => 'local',
	'payment_method' => 'skippayment',
	'payment_status' => 'completed'
	);
	
service_finder_add_wallet_history($args);
// show messages
$redirect = add_query_arg( array('manageaccountby' => 'admin','manageproviderid' => $provider_id,'tabname' => 'wallet','walletpaymentupdate' => 'success'), service_finder_get_url_by_shortcode('[service_finder_my_account') );
wp_redirect($redirect);
die;
}

/*Add amount to wallet via payu money start*/
if(isset($_POST['payment_mode']) && $payment_mode == 'payumoney' && isset($_POST['wallet-payment'])){
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

$provider_id = (!empty($_POST['user_id'])) ? base64_decode($_POST['user_id']) : '';
$amount = (!empty($_POST['amount'])) ? esc_attr($_POST['amount']) : 0;
$amount = floatval($amount);

$surl = add_query_arg( array('walletpayment_made' => 'success','payutransaction' => 'success'), service_finder_get_url_by_shortcode('[service_finder_my_account') );
$furl = add_query_arg( array('walletpayment_made' => 'failed','payutransaction' => 'failed'), service_finder_get_url_by_shortcode('[service_finder_my_account') );

$txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
$action = $PAYU_BASE_URL . '/_payment';

$userdata = service_finder_getUserInfo($provider_id);

$price = $amount;

$productinfo = 'Payment for Purchase Wallet Amount';
$first_name = $userdata['fname'];
$user_email = $userdata['email'];
$phone = $userdata['phone'];

$str = "$MERCHANT_KEY|$txnid|$price|$productinfo|$first_name|$user_email|$price|$provider_id|||||||||$SALT";

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
	'udf1' 			=> $price,
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

if(isset($_GET['walletpayment_made']) && $_GET['walletpayment_made'] == 'success' && $_GET['payutransaction'] == 'success' && isset($_GET['payutransaction']) && isset($_POST['mihpayid']) && isset($_POST['status'])){

$service_finder_options = get_option('service_finder_options');
$service_finder_Tables = service_finder_plugin_global_vars('service_finder_Tables');
$registerErrors = service_finder_plugin_global_vars('registerErrors');
$registerMessages = service_finder_plugin_global_vars('registerMessages');

$amount = (isset($_POST['udf1'])) ? esc_html($_POST['udf1']) : '';
$provider_id = (isset($_POST['udf2'])) ? esc_html($_POST['udf2']) : '';
$txnid = (isset($_POST['txnid'])) ? esc_html($_POST['txnid']) : '';
$payuMoneyId = (isset($_POST['mihpayid'])) ? esc_html($_POST['mihpayid']) : '';
$status = (isset($_POST['status'])) ? esc_html($_POST['status']) : '';

if($status == 'success' && $payuMoneyId != ""){

service_finder_add_wallet_amount($provider_id,$amount);
		
$args = array(
	'user_id' => $provider_id,
	'amount' => $amount,
	'txn_id' => $txnid,
	'action' => 'credit',
	'payment_mode' => 'local',
	'payment_method' => 'payumoney',
	'payment_status' => 'completed'
	);
	
service_finder_add_wallet_history($args);

// show messages
$redirect = add_query_arg( array('walletpaymentupdate' => 'success'), service_finder_get_url_by_shortcode('[service_finder_my_account') );
wp_redirect($redirect);
die;

}

}

if(isset($_GET['walletpayment_made']) && $_GET['walletpayment_made'] == 'failed' && $_GET['payutransaction'] == 'failed'){

$registerErrors = service_finder_plugin_global_vars('registerErrors');

$errors = new WP_Error();
$message = esc_html__("You canceled payment. Your payment wasn't made","service-finder");
$errors->add( 'cancel_payment', $message);
$registerErrors = $errors;

}
/*Add amount to wallet via payu money end*/

/*Add amount to wallet via wire transfer*/
if(isset($_POST['payment_mode']) && $payment_mode == 'wired' && isset($_POST['wallet-payment'])){
global $wpdb, $service_finder_options, $service_finder_Tables;

$wired_array = array();

$provider_id = (!empty($_POST['user_id'])) ? base64_decode($_POST['user_id']) : '';
$amount = (!empty($_POST['amount'])) ? esc_attr($_POST['amount']) : 0;
$amount = floatval($amount);

$invoiceid = strtoupper(uniqid('WALLET-'));

$args = array(
	'user_id' => $provider_id,
	'amount' => $amount,
	'txn_id' => $invoiceid,
	'action' => 'credit',
	'payment_mode' => 'local',
	'payment_method' => 'wired',
	'payment_status' => 'pending'
	);
	
service_finder_add_wallet_history($args);

service_finder_send_wallet_amount_wiretansfer_mail($provider_id,$invoiceid);

// show messages
$redirect = add_query_arg( array('walletpaymentupdate' => 'wired'), service_finder_get_url_by_shortcode('[service_finder_my_account') );
wp_redirect($redirect);
die;

}

/*Add amount to wallet via wire transfer invoice mail*/
function service_finder_send_wallet_amount_wiretansfer_mail($provider_id,$invoiceid){
global $wpdb, $service_finder_Tables, $service_finder_options;

$subject = esc_html__('Invoice ID for add amount to wallet via Wire Transfer', 'service-finder');

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

/*Add amount to wallet*/
add_action('wp_ajax_process_wallet_amount', 'service_finder_process_wallet_amount');
function service_finder_process_wallet_amount(){
global $wpdb, $stripe_options, $service_finder_options, $service_finder_Tables;
$user_id = (!empty($_POST['user_id'])) ? base64_decode($_POST['user_id']) : '';
$amount = (!empty($_POST['amount'])) ? esc_attr($_POST['amount']) : 0;

$currUser = wp_get_current_user(); 

$userdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'users WHERE `ID` = %d',$user_id));

$token = (isset($_POST['stripeToken'])) ? esc_attr($_POST['stripeToken']) : '';
$totalcost = $amount * 100;
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
			'description' => "Payment will add to customer wallet"
		)
	);	

	$charge = \Stripe\Charge::create(array(
				  "amount" => $totalcost,
				  "currency" => strtolower(service_finder_currencycode()),
				  "customer" => $customer->id, // obtained with Stripe.js
				  "description" => "Charge to add amount towallet"
				));

	if ($charge->paid == true && $charge->status == "succeeded") { 
	
		$txnid = $charge->balance_transaction;
		
		service_finder_add_wallet_amount($user_id,$amount);
		
		$args = array(
			'user_id' => $user_id,
			'amount' => $amount,
			'txn_id' => $txnid,
			'action' => 'credit',
			'payment_mode' => 'local',
			'payment_method' => 'stripe',
			'payment_status' => 'completed'
			);
			
		service_finder_add_wallet_history($args);
		
		$currentpageurl = service_finder_get_url_by_shortcode('[service_finder_my_account]');
		if(service_finder_getUserRole($currUser->ID) == 'administrator'){
		$currentpageurl = add_query_arg( array('manageaccountby' => 'admin','manageproviderid' => $user_id,'tabname' => 'wallet'), $currentpageurl );
		}elseif(service_finder_getUserRole($currUser->ID) == 'Provider'){
		$currentpageurl = add_query_arg( array('tabname' => 'wallet'), $currentpageurl );
		}elseif(service_finder_getUserRole($currUser->ID) == 'Customer'){
		$currentpageurl = add_query_arg( array('action' => 'wallet'), $currentpageurl );
		}
		
		$msg = esc_html__('Amount has been added to wallet successfully', 'service-finder');
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
			'err_message' => sprintf( esc_html__('%s', 'service-finder'), $err['message'] )
			);
	echo json_encode($error);
}

exit;
}

/*Add amount to wallet*/
function service_finder_add_wallet_amount($user_id,$amount){

	$currentamount = service_finder_get_wallet_amount($user_id);
	$currentamount = (!empty($currentamount) && $currentamount > 0) ? $currentamount : 0;
	$totalamount = floatval($currentamount) + floatval($amount);
	
	update_user_meta($user_id,'_sf_wallet_amount',$totalamount);
	
}

/*Add to wallet history*/
function service_finder_add_wallet_history($args){
global $wpdb, $service_finder_Tables;	
	
	$paydate = date('Y-m-d h:i:s');
	$txndata = array(
			'user_id' => (!empty($args['user_id'])) ? $args['user_id'] : 0,
			'payment_date' => $paydate,
			'amount' => (!empty($args['amount'])) ? $args['amount'] : 0,
			'action' => (!empty($args['action'])) ? $args['action'] : '',
			'debit_for' => (!empty($args['debit_for'])) ? $args['debit_for'] : '',
			'txn_id' => (!empty($args['txn_id'])) ? $args['txn_id'] : '',
			'payment_mode' => (!empty($args['payment_mode'])) ? $args['payment_mode'] : '',
			'payment_method' => (!empty($args['payment_method'])) ? $args['payment_method'] : '',
			'payment_status' => (!empty($args['payment_status'])) ? $args['payment_status'] : '',
			);
	$wpdb->insert($service_finder_Tables->wallet_transaction,wp_unslash($txndata));
}

/*Add to wallet history*/
function service_finder_update_wallet_history($args,$where_id){
global $wpdb, $service_finder_Tables;	
	
	$paydate = date('Y-m-d h:i:s');
	$txndata = array(
			'user_id' => (!empty($args['user_id'])) ? $args['user_id'] : 0,
			'payment_date' => $paydate,
			'amount' => (!empty($args['amount'])) ? $args['amount'] : 0,
			'action' => 'credit',
			'txn_id' => (!empty($args['txn_id'])) ? $args['txn_id'] : '',
			'payment_mode' => (!empty($args['payment_mode'])) ? $args['payment_mode'] : '',
			'payment_method' => (!empty($args['payment_method'])) ? $args['payment_method'] : '',
			'payment_status' => (!empty($args['payment_status'])) ? $args['payment_status'] : '',
			);
	
	$where = array(
					'txn_id' => $where_id
			);
			
	$wpdb->update($service_finder_Tables->wallet_transaction,wp_unslash($txndata),$where);
}

/*Get wallet History*/
add_action('wp_ajax_get_wallet_history', 'service_finder_get_wallet_history');
function service_finder_get_wallet_history(){
global $wpdb, $service_finder_Tables, $service_finder_options;
$requestData= $_REQUEST;
$user_id = (!empty($_POST['user_id'])) ? esc_attr($_POST['user_id']) : '';
$columns = array( 
	0 =>'payment_date', 
	1 =>'txn_id', 
	2 =>'payment_method', 
	3 =>'payment_status', 
	4 =>'amount', 
	5 =>'action', 
	6 =>'debit_for', 
);

// getting total number records without any search
$sql = $wpdb->prepare("SELECT * FROM ".$service_finder_Tables->wallet_transaction." WHERE `user_id` = %d",$user_id);
$query=$wpdb->get_results($sql);
$totalData = count($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

$sql = "SELECT * FROM ".$service_finder_Tables->wallet_transaction." WHERE `user_id` = ".$user_id;
if( !empty($requestData['search']['value']) ) {   
	$sql.=" AND (( `txn_id` LIKE '".$requestData['search']['value']."%' )";    
	$sql.=" OR ( `payment_method` LIKE '".$requestData['search']['value']."%' )";    
	$sql.=" OR ( `payment_status` LIKE '".$requestData['search']['value']."%' )";    
	$sql.=" OR ( `amount` LIKE '".$requestData['search']['value']."%' )";    
	$sql.=" OR ( `action` LIKE '".$requestData['search']['value']."%' )";    
	$sql.=" OR ( `debit_for` LIKE '".$requestData['search']['value']."%' )";    
	$sql.=" OR ( `payment_date` LIKE '".$requestData['search']['value']."%' ))";    
}

$query=$wpdb->get_results($sql);
$totalFiltered = count($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." DESC LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
$query=$wpdb->get_results($sql);
$data = array();

foreach($query as $result){
	$nestedData=array(); 

	$nestedData[] = date('d-m-Y',strtotime($result->payment_date));
	$nestedData[] = $result->txn_id;
	$nestedData[] = $result->payment_method;
	$nestedData[] = service_finder_translate_static_status_string($result->payment_status);
	$nestedData[] = service_finder_money_format($result->amount);
	$nestedData[] = ucfirst($result->action);
	$nestedData[] = $result->debit_for;
	
	$data[] = $nestedData;
}



$json_data = array(
			"draw"            => intval( $requestData['draw'] ),
			"recordsTotal"    => intval( $totalData ),
			"recordsFiltered" => intval( $totalFiltered ),
			"data"            => $data
			);

echo json_encode($json_data);
exit(0);

}

