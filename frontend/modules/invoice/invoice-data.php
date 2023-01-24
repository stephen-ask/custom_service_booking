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

/*Invoice Pay via paypal*/
if(isset($_POST['invoicepayment_mode']) && ($_POST['invoicepayment_mode'] == 'paypal')){

$service_finder_options = get_option('service_finder_options');
$wpdb = service_finder_plugin_global_vars('wpdb');
$paypal = service_finder_plugin_global_vars('paypal');
$service_finder_Tables = service_finder_plugin_global_vars('service_finder_Tables');
$service_finder_Params = service_finder_plugin_global_vars('service_finder_Params');
$service_finder_Errors = service_finder_plugin_global_vars('service_finder_Errors');
$registerErrors = service_finder_plugin_global_vars('registerErrors');
$registerMessages = service_finder_plugin_global_vars('registerMessages');
$getprovider = isset($_POST['provider']) ? esc_html($_POST['provider']) : '';
$settings = service_finder_getProviderSettings($getprovider);
/*Assign Paypal Credentials*/
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
	if(!is_user_logged_in()){
	$userLink = service_finder_get_author_url($getprovider);
	$invoiceid = (isset($_POST['invoiceid'])) ? $_POST['invoiceid'] : '';
	$returnUrl = $userLink.'?invoice_paid=success&invoiceid='.service_finder_encrypt($invoiceid, 'Developer#@)!%').'&providerid='.service_finder_encrypt($getprovider, 'Developer#@)!%');
	$myaccount = $userLink;
	if(service_finder_using_permalink()){
	$myaccount = $myaccount.'?';
	}else{
	$myaccount = $myaccount.'&';
	}
	}else{
	
	$accounturl = service_finder_get_url_by_shortcode('[service_finder_my_account]');
	$myaccount = $accounturl;
	if(service_finder_using_permalink()){
	$myaccount = $myaccount.'?';
	}else{
	$myaccount = $myaccount.'&';
	}
	$invoiceid = (isset($_POST['invoiceid'])) ? $_POST['invoiceid'] : '';
	$returnUrl = $myaccount.'action=invoice&invoice_paid=success&invoiceid='.service_finder_encrypt($invoiceid, 'Developer#@)!%').'&providerid='.service_finder_encrypt($getprovider, 'Developer#@)!%');
	
	}
	// Single payments
	$cancelUrl = $myaccount.'action=invoice&invoice_paid=cancel';
	$urlParams = array(
		'RETURNURL' => $returnUrl,
		'CANCELURL' => $cancelUrl
	);
					
	$orderParams = array(
		'PAYMENTREQUEST_0_AMT' => $_POST['amount'],
		'PAYMENTREQUEST_0_SHIPPINGAMT' => '0',
		'PAYMENTREQUEST_0_CURRENCYCODE' => strtoupper(service_finder_currencycode()),
		'PAYMENTREQUEST_0_ITEMAMT' => $_POST['amount']
	);
	$itemParams = array(
		'L_PAYMENTREQUEST_0_NAME0' => 'Payment via paypal',
		'L_PAYMENTREQUEST_0_DESC0' => 'Invoice Paid',
		'L_PAYMENTREQUEST_0_AMT0' => $_POST['amount'],
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
if(isset($_GET['invoice_paid']) && ($_GET['invoice_paid'] == 'success') && !empty($_GET['token'])) {
$service_finder_options = get_option('service_finder_options');
$wpdb = service_finder_plugin_global_vars('wpdb');
$service_finder_Tables = service_finder_plugin_global_vars('service_finder_Tables');

		$invoiceid = service_finder_decrypt($_GET['invoiceid'], 'Developer#@)!%');
		$providerid = service_finder_decrypt($_GET['providerid'], 'Developer#@)!%');



$settings = service_finder_getProviderSettings($providerid);
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
				
				$transactionId = $singlePayment['PAYMENTINFO_0_TRANSACTIONID'];
				
				$data = array(
					'payment_type' => 'paypal',
					'paypal_token' =>  $_GET['token'],
					'status' => 'paid',
					'txnid' => esc_html($transactionId),
					);
					
				$where = array(
				'id' => $invoiceid,
				);
				
				service_finder_SendInvoicePaidMailToProvider($invoiceid);
				service_finder_SendInvoicePaidMailToCustomer($invoiceid);
					
				$wpdb->update($service_finder_Tables->invoice,wp_unslash($data),$where);
				
				if(function_exists('service_finder_add_notices')) {

					$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->invoice.' WHERE `id` = %d',$invoiceid));
					$noticedata = array(
							'provider_id' => $providerid,
							'target_id' => $invoiceid, 
							'topic' => 'Invoice Paid',
							'title' => esc_html__('Invoice Paid', 'service-finder'),
							'notice' => sprintf( esc_html__('Invoice paid by %s', 'service-finder'), $row->customer_email ),
							);
					service_finder_add_notices($noticedata);
				
				}
				
				if(!is_user_logged_in()){

				$userLink = service_finder_get_author_url($providerid);
				$redirect = $userLink.'?invoicepaidcompleted=success';
	
				}else{
				
				$accounturl = service_finder_get_url_by_shortcode('[service_finder_my_account]');
				$myaccount = $accounturl;
				if(service_finder_using_permalink()){
				$myaccount = $myaccount.'?';
				}else{
				$myaccount = $myaccount.'&';
				}
				
				$redirect = $myaccount.'action=invoice&invoicepaidcompleted=success';
				
				}
				
				wp_redirect($redirect);
				die;
				
				}

		}

}

// delete token and show messages if user cancel payment 
if(isset($_GET['invoice_paid']) && ($_GET['invoice_paid'] == 'cancel') && !empty($_GET['token'])){
	// delete token from DB
	$wpdb = service_finder_plugin_global_vars('wpdb');
	$registerErrors = service_finder_plugin_global_vars('registerErrors');
	$token = $_GET['token'];
	// show message
		global $registerErrors;
		$errors = new WP_Error();
		$message = esc_html__("You canceled payment. Your invoice wasn't paid","service-finder");
		$errors->add( 'cancel_payment', $message);
		$registerErrors = $errors;
	
}
/*Invoice Pay via paypal END*/

/*Invoice Pay via payu money start*/
if(isset($_POST['invoicepayment_mode']) && $_POST['invoicepayment_mode'] == 'payumoney'){

$service_finder_options = get_option('service_finder_options');
$wpdb = service_finder_plugin_global_vars('wpdb');
$service_finder_Tables = service_finder_plugin_global_vars('service_finder_Tables');
$service_finder_Params = service_finder_plugin_global_vars('service_finder_Params');
$service_finder_Errors = service_finder_plugin_global_vars('service_finder_Errors');
$registerErrors = service_finder_plugin_global_vars('registerErrors');
$registerMessages = service_finder_plugin_global_vars('registerMessages');
$getprovider = isset($_POST['provider']) ? esc_html($_POST['provider']) : '';
$settings = service_finder_getProviderSettings($getprovider);
/*Assign Paypal Credentials*/

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

$invoiceid = (isset($_POST['invoiceid'])) ? esc_html($_POST['invoiceid']) : '';
if(!is_user_logged_in()){
$userLink = service_finder_get_author_url($getprovider);
$surl = add_query_arg( array('invoicepaid' => 'success','payutransaction' => 'success'), $userLink );
$myaccount = $userLink;
}else{

$accounturl = service_finder_get_url_by_shortcode('[service_finder_my_account]');
$myaccount = $accounturl;
$surl = add_query_arg( array('invoicepaid' => 'success','payutransaction' => 'success','action' => 'invoice'), $myaccount );

}
// Single payments
$furl = add_query_arg( array('invoicepaid' => 'failed','payutransaction' => 'failed','action' => 'invoice'), $myaccount );

$sql = $wpdb->prepare("SELECT invoice.id, invoice.provider_id, invoice.reference_no, invoice.duedate, invoice.booking_id, invoice.discount_type, invoice.tax_type, invoice.discount, invoice.tax, invoice.services, invoice.description, invoice.total, invoice.grand_total, invoice.status, customers.name, customers.phone as cusphone, customers.phone2 as cusphone2, customers.email as cusemail, customers.address as cusaddress, customers.apt as cusapt, customers.city as cuscity, customers.state as cusstate, customers.zipcode as cuszipcdoe, customers.description, providers.full_name, providers.phone, providers.email, providers.mobile, providers.fax, providers.address, providers.apt, providers.city, providers.state, providers.zipcode, providers.country FROM ".$service_finder_Tables->invoice." as invoice INNER JOIN ".$service_finder_Tables->customers." as customers on invoice.customer_email = customers.email LEFT JOIN ".$service_finder_Tables->providers." as providers on invoice.provider_id = providers.wp_user_id WHERE invoice.id = %d",$invoiceid);
	
$row = $wpdb->get_row($sql);

$txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
$action = $PAYU_BASE_URL . '/_payment';

$price = (isset($_POST['amount'])) ? esc_html($_POST['amount']) : '';

$productinfo = 'Pay for Invoice';
$first_name = $row->name;
$user_email = $row->cusemail;
$phone = $row->cusphone;

$udf1 = service_finder_encrypt($invoiceid, 'Developer#@)!%');
$udf2 = service_finder_encrypt($getprovider, 'Developer#@)!%');

$str = "$MERCHANT_KEY|$txnid|$price|$productinfo|$first_name|$user_email|$udf1|$udf2|||||||||$SALT";

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
	'udf1' 			=> $udf1,
	'udf2' 			=> $udf2,
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

if(isset($_GET['invoicepaid']) && $_GET['invoicepaid'] == 'success' && $_GET['payutransaction'] == 'success' && isset($_GET['payutransaction']) && isset($_POST['mihpayid']) && isset($_POST['status'])){

$service_finder_options = get_option('service_finder_options');
$wpdb = service_finder_plugin_global_vars('wpdb');
$service_finder_Tables = service_finder_plugin_global_vars('service_finder_Tables');

$invoiceid = (isset($_POST['udf1'])) ? esc_html($_POST['udf1']) : '';
$providerid = (isset($_POST['udf2'])) ? esc_html($_POST['udf2']) : '';
$txnid = (isset($_POST['txnid'])) ? esc_html($_POST['txnid']) : '';
$payuMoneyId = (isset($_POST['mihpayid'])) ? esc_html($_POST['mihpayid']) : '';
$status = (isset($_POST['status'])) ? esc_html($_POST['status']) : '';

$invoiceid = service_finder_decrypt($invoiceid, 'Developer#@)!%');
$providerid = service_finder_decrypt($providerid, 'Developer#@)!%');

if($status == 'success' && $payuMoneyId != ""){

$data = array(
	'payment_type' => 'payumoney',
	'paypal_token' =>  '',
	'status' => 'paid',
	'txnid' => esc_html($txnid),
	);
	
$where = array(
'id' => $invoiceid,
);

service_finder_SendInvoicePaidMailToProvider($invoiceid);
service_finder_SendInvoicePaidMailToCustomer($invoiceid);
	
$wpdb->update($service_finder_Tables->invoice,wp_unslash($data),$where);

if(function_exists('service_finder_add_notices')) {

	$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->invoice.' WHERE `id` = %d',$invoiceid));
	$noticedata = array(
			'provider_id' => $providerid,
			'target_id' => $invoiceid, 
			'topic' => 'Invoice Paid',
			'title' => esc_html__('Invoice Paid', 'service-finder'),
			'notice' => sprintf( esc_html__('Invoice paid by %s', 'service-finder'), $row->customer_email ),
			);
	service_finder_add_notices($noticedata);

}

if(!is_user_logged_in()){

$userLink = service_finder_get_author_url($providerid);
$redirect = add_query_arg( array('invoicepaidcompleted' => 'success'), $userLink );

}else{

$accounturl = service_finder_get_url_by_shortcode('[service_finder_my_account]');
$myaccount = $accounturl;

$redirect = add_query_arg( array('action' => 'invoice','invoicepaidcompleted' => 'success'), $myaccount );

}

wp_redirect($redirect);
die;

}

}

if(isset($_GET['invoicepaid']) && $_GET['invoicepaid'] == 'failed' && $_GET['payutransaction'] == 'failed'){

$wpdb = service_finder_plugin_global_vars('wpdb');
$registerErrors = service_finder_plugin_global_vars('registerErrors');
$errors = new WP_Error();
$message = esc_html__("You canceled payment. Your invoice wasn't paid","service-finder");
$errors->add( 'cancel_payment', $message);
$registerErrors = $errors;

}
/*Invoice Pay via payu money end*/

/*Get Service Detais*/
add_action('wp_ajax_getServiceDetails', 'service_finder_getServiceDetails');
add_action('wp_ajax_nopriv_getServiceDetails', 'service_finder_getServiceDetails');

function service_finder_getServiceDetails(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/invoice/Invoice.php';
$getData = new SERVICE_FINDER_Invoice();
$getData->service_finder_getServiceDetails($_POST);
exit;
}

/*Edit invoice ajax call*/
add_action('wp_ajax_edit_invoice', 'service_finder_edit_invoice');
add_action('wp_ajax_nopriv_edit_invoice', 'service_finder_edit_invoice');

function service_finder_edit_invoice(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/invoice/Invoice.php';
$getData = new SERVICE_FINDER_Invoice();
$getData->service_finder_editInvoiceData($_POST);
exit;
}

/*Add Invoice AJax Call*/
add_action('wp_ajax_add_invoice', 'service_finder_add_invoice');
add_action('wp_ajax_nopriv_add_invoice', 'service_finder_add_invoice');

function service_finder_add_invoice(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/invoice/Invoice.php';
$addData = new SERVICE_FINDER_Invoice();
$addData->service_finder_addInvoiceData($_POST);
exit;
}

/*Update Invoice Data*/
add_action('wp_ajax_update_invoice', 'service_finder_update_invoice');
add_action('wp_ajax_nopriv_update_invoice', 'service_finder_update_invoice');

function service_finder_update_invoice(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/invoice/Invoice.php';
$updateData = new SERVICE_FINDER_Invoice();
$updateData->service_finder_updateInvoiceData($_POST);
exit;
}

/*Get Invoice*/
add_action('wp_ajax_get_invoice', 'service_finder_get_invoice');
add_action('wp_ajax_nopriv_get_invoice', 'service_finder_get_invoice');

function service_finder_get_invoice(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/invoice/Invoice.php';
$getData = new SERVICE_FINDER_Invoice();
$getData->service_finder_getInvoice($_POST);
exit;
}

/*Get Customer Invoice*/
add_action('wp_ajax_get_customer_invoice', 'service_finder_get_customer_invoice');
add_action('wp_ajax_nopriv_get_customer_invoice', 'service_finder_get_customer_invoice');

function service_finder_get_customer_invoice(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/invoice/Invoice.php';
$getData = new SERVICE_FINDER_Invoice();
$getData->service_finder_getCustomerInvoice($_POST);
exit;
}

/*Delete Invoice*/
add_action('wp_ajax_delete_invoice', 'service_finder_delete_invoice');
add_action('wp_ajax_nopriv_delete_invoice', 'service_finder_delete_invoice');

function service_finder_delete_invoice(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/invoice/Invoice.php';
$deleteInvoice = new SERVICE_FINDER_Invoice();
$deleteInvoice->service_finder_deleteInvoice();
exit;
}

/*View Invoice Details*/
add_action('wp_ajax_invoice_details', 'service_finder_invoice_details');
add_action('wp_ajax_nopriv_invoice_details', 'service_finder_invoice_details');

function service_finder_invoice_details(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/invoice/Invoice.php';
$viewInvoice = new SERVICE_FINDER_Invoice();
$viewInvoice->service_finder_viewInvoice();
exit;
}

/*View Invoice Customer Details*/
add_action('wp_ajax_invoice_customer_details', 'service_finder_invoice_customer_details');
add_action('wp_ajax_nopriv_invoice_customer_details', 'service_finder_invoice_customer_details');

function service_finder_invoice_customer_details(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/invoice/Invoice.php';
$viewInvoice = new SERVICE_FINDER_Invoice();
$viewInvoice->service_finder_viewCustomerInvoice();
exit;
}

/*Invoice payment process*/
add_action('wp_ajax_paynow', 'service_finder_paynow');
add_action('wp_ajax_nopriv_paynow', 'service_finder_paynow');
function service_finder_paynow(){
global $wpdb, $service_finder_Tables, $service_finder_options;
		$token = $_POST['stripeToken'];
		$totalcost = $_POST['amount'] * 100;
		require_once(SERVICE_FINDER_PAYMENT_GATEWAY_DIR.'/stripe/init.php');
		
		$provider_id = (isset($_POST['provider_id'])) ? esc_attr($_POST['provider_id']) : '';
		$email = (isset($_POST['email'])) ? esc_attr($_POST['email']) : '';
		$invoiceid = (isset($_POST['invoiceid'])) ? esc_attr($_POST['invoiceid']) : '';
		
		$settings = service_finder_getProviderSettings($_POST['provider_id']);
		$secret_key = $settings['stripesecretkey'];
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
					'email' => $_POST['email'],
					'description' => "Paid by ".$_POST['email']
				)
			);	
			$charge = \Stripe\Charge::create(array(
						  "amount" => $totalcost,
						  "currency" => strtolower(service_finder_currencycode()),
						  "customer" => $customer->id, // obtained with Stripe.js
						  "description" => "Invoice Paid"
						));
			if ($charge->paid == true && $charge->status == "succeeded") { 
			
				$data = array(
				'payment_type' => 'stripe',
				'stripe_customer_id' =>  $customer->id,
				'stripe_token' => $token, 
				'status' => 'paid',
				'txnid' => $charge->balance_transaction,
				);
				
				$where = array(
				'id' => $_POST['invoiceid']
				);
				
				$wpdb->update($service_finder_Tables->invoice,wp_unslash($data),$where);
				
				if(function_exists('service_finder_add_notices')) {
		
					$noticedata = array(
							'provider_id' => $provider_id,
							'target_id' => $invoiceid, 
							'topic' => 'Invoice Paid',
							'title' => esc_html__('Invoice Paid', 'service-finder'),
							'notice' => sprintf( esc_html__('Invoice paid by %s', 'service-finder'), $email ),
							);
					service_finder_add_notices($noticedata);
				
				}
				$msg = (!empty($service_finder_options['pay-invoice'])) ? $service_finder_options['pay-invoice'] : esc_html__('Invoice paid successfully', 'service-finder');
				$success = array(
						'status' => 'success',
						'suc_message' => $msg
						);
				echo json_encode($success);
				
				service_finder_SendInvoicePaidMailToProvider($_POST['invoiceid']);
				service_finder_SendInvoicePaidMailToCustomer($_POST['invoiceid']);
			
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

/*Invoice payment process via wallet*/
add_action('wp_ajax_wallet_paynow', 'service_finder_wallet_paynow');
add_action('wp_ajax_nopriv_wallet_paynow', 'service_finder_wallet_paynow');
function service_finder_wallet_paynow(){
global $wpdb, $service_finder_Tables, $service_finder_options, $current_user;

$provider_id = (isset($_POST['provider_id'])) ? esc_attr($_POST['provider_id']) : '';
$amount = (isset($_POST['amount'])) ? esc_attr($_POST['amount']) : '';
$invoiceid = (isset($_POST['invoiceid'])) ? esc_attr($_POST['invoiceid']) : '';
$email = (isset($_POST['email'])) ? esc_attr($_POST['email']) : '';

$walletamount = service_finder_get_wallet_amount($current_user->ID);

if(floatval($walletamount) < floatval($amount)){
$error = array(
		'status' => 'error',
		'err_message' => 'insufficient_amount'
		);
echo json_encode($error);
exit(0);
}

$data = array(
'payment_type' => 'wallet',
'stripe_customer_id' =>  '',
'stripe_token' => '', 
'status' => 'paid',
'txnid' => '',
);

$where = array(
'id' => $invoiceid
);

$wpdb->update($service_finder_Tables->invoice,wp_unslash($data),$where);

$remaining_wallet_amount = floatval($walletamount) - floatval($amount); 

$args = array(
	'user_id' => $current_user->ID,
	'amount' => $amount,
	'action' => 'debit',
	'debit_for' => esc_html__('Invoice Pay', 'service-finder'),
	'payment_mode' => 'local',
	'payment_method' => 'wallet',
	'payment_status' => 'completed'
	);
	
service_finder_add_wallet_history($args);

$cashbackamount = service_finder_cashback_amount('invoice');

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

if(function_exists('service_finder_add_notices')) {

	$noticedata = array(
			'provider_id' => $provider_id,
			'target_id' => $invoiceid, 
			'topic' => 'Invoice Paid',
			'title' => esc_html__('Invoice Paid', 'service-finder'),
			'notice' => sprintf( esc_html__('Invoice paid by %s', 'service-finder'), $email ),
			);
	service_finder_add_notices($noticedata);

}
$msg = (!empty($service_finder_options['pay-invoice'])) ? $service_finder_options['pay-invoice'] : esc_html__('Invoice paid successfully', 'service-finder');
$success = array(
		'status' => 'success',
		'suc_message' => $msg
		);
echo json_encode($success);

service_finder_SendInvoicePaidMailToProvider($invoiceid);
service_finder_SendInvoicePaidMailToCustomer($invoiceid);

exit;
}


/*Invoice payment process*/
add_action('wp_ajax_payulatam_paynow', 'service_finder_payulatam_paynow');
add_action('wp_ajax_nopriv_payulatam_paynow', 'service_finder_payulatam_paynow');

function service_finder_payulatam_paynow(){
global $wpdb, $stripe_options, $service_finder_options, $service_finder_Tables;

$amount = (isset($_POST['amount'])) ? esc_html($_POST['amount']) : '';
$provider_id = (isset($_POST['provider_id'])) ? esc_html($_POST['provider_id']) : '';
$invoiceid = (isset($_POST['invoiceid'])) ? esc_html($_POST['invoiceid']) : '';

$cardtype = (isset($_POST['payulatam_invoice_cardtype'])) ? esc_html($_POST['payulatam_invoice_cardtype']) : '';
$card_number = (isset($_POST['payulatam_card_number'])) ? esc_html($_POST['payulatam_card_number']) : '';
$card_cvc = (isset($_POST['payulatam_card_cvc'])) ? esc_html($_POST['payulatam_card_cvc']) : '';
$card_month = (isset($_POST['payulatam_card_month'])) ? esc_html($_POST['payulatam_card_month']) : '';
$card_year = (isset($_POST['payulatam_card_year'])) ? esc_html($_POST['payulatam_card_year']) : '';

$settings = service_finder_getProviderSettings($provider_id);

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

$sql = $wpdb->prepare("SELECT invoice.id, invoice.provider_id, invoice.reference_no, invoice.duedate, invoice.booking_id, invoice.discount_type, invoice.tax_type, invoice.discount, invoice.tax, invoice.services, invoice.description, invoice.total, invoice.grand_total, invoice.status, customers.name, customers.phone as cusphone, customers.phone2 as cusphone2, customers.email as cusemail, customers.address as cusaddress, customers.apt as cusapt, customers.city as cuscity, customers.state as cusstate, customers.zipcode as cuszipcdoe, customers.description, providers.full_name, providers.phone, providers.email, providers.mobile, providers.fax, providers.address, providers.apt, providers.city, providers.state, providers.zipcode, providers.country FROM ".$service_finder_Tables->invoice." as invoice INNER JOIN ".$service_finder_Tables->customers." as customers on invoice.customer_email = customers.email LEFT JOIN ".$service_finder_Tables->providers." as providers on invoice.provider_id = providers.wp_user_id WHERE invoice.id = %d",$invoiceid);
	
$row = $wpdb->get_row($sql);

$fullname = $row->name;
$user_email = $row->cusemail;
$phone = $row->cusphone;

require_once(SERVICE_FINDER_PAYMENT_GATEWAY_DIR.'/payulatam/lib/PayU.php');

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

$reference = $invoiceid.'_'.time();
$value = $amount;

try {
$parameters = array(
	//Enter the account’s identifier here
	PayUParameters::ACCOUNT_ID => $payulatamaccountid,
	// Enter the reference code here.
	PayUParameters::REFERENCE_CODE => $reference,
	// Enter the description here.
	PayUParameters::DESCRIPTION => "Payment for invoice via PayU Latam",
	
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
	PayUParameters::CREDIT_CARD_NUMBER => $card_number,
	// Enter expiration date of the credit card here
	PayUParameters::CREDIT_CARD_EXPIRATION_DATE => $card_year.'/'.$card_month,
	//Enter the security code of the credit card here
	PayUParameters::CREDIT_CARD_SECURITY_CODE=> $card_cvc,
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
				
				$data = array(
				'payment_type' => 'payulatam',
				'status' => 'paid',
				'txnid' => $txnid,
				);
				
				$where = array(
				'id' => $invoiceid
				);
				
				$wpdb->update($service_finder_Tables->invoice,wp_unslash($data),$where);
				
				if(function_exists('service_finder_add_notices')) {
		
					$noticedata = array(
							'provider_id' => $provider_id,
							'target_id' => $invoiceid, 
							'topic' => 'Invoice Paid',
							'title' => esc_html__('Invoice Paid', 'service-finder'),
							'notice' => sprintf( esc_html__('Invoice paid by %s', 'service-finder'), $user_email ),
							);
					service_finder_add_notices($noticedata);
				
				}
				$msg = (!empty($service_finder_options['pay-invoice'])) ? $service_finder_options['pay-invoice'] : esc_html__('Invoice paid successfully', 'service-finder');
				$success = array(
						'status' => 'success',
						'suc_message' => $msg
						);
				echo json_encode($success);
				
				service_finder_SendInvoicePaidMailToProvider($invoiceid);
				service_finder_SendInvoicePaidMailToCustomer($invoiceid);
			
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

/*Invoice payment process*/
add_action('wp_ajax_twocheckout_paynow', 'service_finder_twocheckout_paynow');
add_action('wp_ajax_nopriv_twocheckout_paynow', 'service_finder_twocheckout_paynow');

function service_finder_twocheckout_paynow(){
global $wpdb, $service_finder_Tables, $service_finder_options;

$token = (!empty($_POST['twocheckouttoken'])) ? esc_html($_POST['twocheckouttoken']) : '';
$totalcost = (!empty($_POST['amount'])) ? esc_html($_POST['amount']) : '';
$provider_id = (!empty($_POST['provider_id'])) ? esc_html($_POST['provider_id']) : '';

$invoiceid = (!empty($_POST['invoiceid'])) ? esc_html($_POST['invoiceid']) : '';

$settings = service_finder_getProviderSettings($provider_id);

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

$invoiceidata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->invoice.' WHERE `id` = "%d"',$invoiceid),ARRAY_A);

$customerinfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `email` = "%s"',$invoiceidata['customer_email']),ARRAY_A);

$charge = Twocheckout_Charge::auth(array(
	"sellerId" => $twocheckoutaccountid,
	"privateKey" => $private_key,
	"merchantOrderId" => time(),
	"token" => $token,
	"currency" => strtoupper(service_finder_currencycode()),
	"total" => $totalcost,
	"tangible"    => "N",
	"billingAddr" => array(
			"name" => $customerinfo['name'],
			"addrLine1" => $customerinfo['address'],
			"city" => $customerinfo['city'],
			"state" => $customerinfo['state'],
			"zipCode" => ($customerinfo['zipcode'] != "") ? $customerinfo['zipcode'] : '302020',
			"country" => $customerinfo['country'],
			"email" => $customerinfo['email'],
			"phoneNumber" => $customerinfo['phone']
		),
));
if ($charge['response']['responseCode'] == 'APPROVED') {

	$transactionid = $charge['response']['transactionId'];
		
	$data = array(
	'payment_type' => 'twocheckout',
	'stripe_customer_id' =>  '',
	'stripe_token' => '', 
	'status' => 'paid',
	'txnid' => $transactionid,
	);
	
	$where = array(
	'id' => $invoiceid
	);
	
	$wpdb->update($service_finder_Tables->invoice,wp_unslash($data),$where);
	$msg = (!empty($service_finder_options['pay-invoice'])) ? $service_finder_options['pay-invoice'] : esc_html__('Invoice paid successfully', 'service-finder');	
	$success = array(
			'status' => 'success',
			'suc_message' => $msg
			);
	echo json_encode($success);
	
	service_finder_SendInvoicePaidMailToProvider($invoiceid);
	service_finder_SendInvoicePaidMailToCustomer($invoiceid);

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

/*Send Invoice Paid mail to provider*/
function service_finder_SendInvoicePaidMailToProvider($invoiceid = ''){
global $wpdb, $service_finder_Tables, $service_finder_options;

			if(!empty($service_finder_options['invoice-to-provider'])){
				$message = $service_finder_options['invoice-to-provider'];
			}else{
				$message = 'Invoice Paid Notification



Reference No: %REFERENCENO%

Due date: %DUEDATE%

Customer Email: %CUSTOMEREMAIL%

Discount Type: %DISCOUNTTYPE%

Discount: %DISCOUNT%

Tax Type: %TAXTYPE%

Tax: %TAX%

Description: %DESCRIPTION%

Total: %TOTAL%

Grand Total: %GRANDTOTAL%

Status: Paid';
			}
								
			$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->invoice.' WHERE `id` = %d',$invoiceid));
			$prorow = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$row->provider_id));
			$customerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `email` = "%s" GROUP BY email',$row->customer_email));
			
			
			
			if($row->description != ""){
			$description = $row->description;
			}else{
			$description = 'N/A';
			}
			
			$discount_type = $row->discount_type;

			$tax_type = $row->tax_type;

			if($row->discount > 0){

			if($discount_type == 'fix'){

				$displaydiscount = $row->discount;

			}elseif($discount_type == 'percentage'){

				$displaydiscount = $row->total * ($row->discount/100);

			}

			}else{

				$displaydiscount = '0.00';

			}

			

			if($row->tax > 0){

			if($tax_type == 'fix'){

				$displaytax = $row->tax;

			}elseif($tax_type == 'percentage'){

				$displaytax = $row->total * ($row->tax/100);

			}

			}else{

				$displaytax = '0.00';

			}
			
			
			$tokens = array('%REFERENCENO%','%DUEDATE%','%CUSTOMEREMAIL%','%DISCOUNTTYPE%','%DISCOUNT%','%TAXTYPE%','%TAX%','%DESCRIPTION%','%TOTAL%','%GRANDTOTAL%','%CUSTOMERNAME%','%CUSTOMEREMAIL%','%CUSTOMERPHONE%','%CUSTOMERPHONE2%','%ADDRESS%','%APT%','%CITY%','%STATE%','%ZIPCODE%','%COUNTRY%');
			$replacements = array($row->reference_no,$row->duedate,$row->customer_email,$discounttype,$displaydiscount,$taxtype,$displaytax,$description,service_finder_money_format($row->total),service_finder_money_format($row->grand_total),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country);
			$msg_body = str_replace($tokens,$replacements,$message);
			
			if($service_finder_options['invoice-to-provider-subject'] != ""){
				$msg_subject = $service_finder_options['invoice-to-provider-subject'];
			}else{
				$msg_subject = esc_html__('Invoice Notification', 'service-finder');
			}
			
			if(service_finder_wpmailer($prorow->email,$msg_subject,$msg_body)) {

				$success = array(
						'status' => 'success',
						'suc_message' => esc_html__('Message has been sent', 'service-finder'),
						);
				$service_finder_Success = json_encode($success);
				return $service_finder_Success;
				
				
			} else {
				$error = array(
						'status' => 'error',
						'err_message' => esc_html__('Message could not be sent.', 'service-finder'),
						);
				$service_finder_Errors = json_encode($error);
				return $service_finder_Errors;
			}
		
	
}

/*Send Invoice Paid mail to customer*/
function service_finder_SendInvoicePaidMailToCustomer($invoiceid = ''){
global $wpdb, $service_finder_Tables, $service_finder_options;

				if(!empty($service_finder_options['invoice-to-customer-paid'])){
				$message = $service_finder_options['invoice-to-customer-paid'];
			}else{
				$message = 'Invoice Paid Notification

Reference No: %REFERENCENO%

Due date: %DUEDATE%

Provider Email: %PROVIDEREMAIL%

Discount Type: %DISCOUNTTYPE%

Discount: %DISCOUNT%

Tax Type: %TAXTYPE%

Tax: %TAX%

Description: %DESCRIPTION%

Total: %TOTAL%

Grand Total: %GRANDTOTAL%

Status: Paid';
			}

								
			$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->invoice.' WHERE `id` = %d',$invoiceid));
			$prorow = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$row->provider_id));
			$customerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `email` = "%s" GROUP BY email',$row->customer_email));
			
			$discount_type = $row->discount_type;

			$tax_type = $row->tax_type;

			if($row->discount > 0){

			if($discount_type == 'fix'){

				$displaydiscount = $row->discount;

			}elseif($discount_type == 'percentage'){

				$displaydiscount = $row->total * ($row->discount/100);

			}

			}else{

				$displaydiscount = '0.00';

			}

			

			if($row->tax > 0){

			if($tax_type == 'fix'){

				$displaytax = $row->tax;

			}elseif($tax_type == 'percentage'){

				$displaytax = $row->total * ($row->tax/100);

			}

			}else{

				$displaytax = '0.00';

			}
			
			if($row->description != ""){
			$description = $row->description;
			}else{
			$description = 'N/A';
			}
			
			
			$tokens = array('%REFERENCENO%','%DUEDATE%','%PROVIDEREMAIL%','%DISCOUNTTYPE%','%DISCOUNT%','%TAXTYPE%','%TAX%','%DISCRIPTION%','%TOTAL%','%GRANDTOTAL%','%CUSTOMERNAME%','%CUSTOMEREMAIL%','%CUSTOMERPHONE%','%CUSTOMERPHONE2%','%ADDRESS%','%APT%','%CITY%','%STATE%','%ZIPCODE%','%COUNTRY%');
			$replacements = array($row->reference_no,$row->duedate,$prorow->full_name,$discounttype,$discount,$taxtype,$tax,$description,service_finder_money_format($row->total),service_finder_money_format($row->grand_total),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country);
			$msg_body = str_replace($tokens,$replacements,$message);
			
			if($service_finder_options['invoice-to-customer-paid-subject'] != ""){
				$msg_subject = $service_finder_options['invoice-to-customer-paid-subject'];
			}else{
				$msg_subject = esc_html__('Invoice Notification', 'service-finder');
			}
			
			if(service_finder_wpmailer($row->customer_email,$msg_subject,$msg_body)) {

				$success = array(
						'status' => 'success',
						'suc_message' => esc_html__('Message has been sent', 'service-finder'),
						);
				$service_finder_Success = json_encode($success);
				return $service_finder_Success;
				
				
			} else {
					
				$error = array(
						'status' => 'error',
						'err_message' => esc_html__('Message could not be sent.', 'service-finder'),
						);
				$service_finder_Errors = json_encode($error);
				return $service_finder_Errors;
			}
		
	
}

/*Send Invoice Reminder Mail*/
add_action('wp_ajax_send_reminder', 'service_finder_send_reminder');
add_action('wp_ajax_nopriv_send_reminder', 'service_finder_send_reminder');

function service_finder_send_reminder(){
global $wpdb, $service_finder_Tables, $service_finder_options;
			$currUser = wp_get_current_user(); 
			
			$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->invoice.' WHERE `id` = %d',$_POST['invoiceid']));
			$customerinfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `email` = "%s" GROUP BY email',$row->customer_email));
			$customername = (!empty($customerinfo->name)) ? $customerinfo->name : '';
			$customeremail = (!empty($row->customer_email)) ? $row->customer_email : '';
			$provider_id = (!empty($row->provider_id)) ? $row->provider_id : '';
			$message = 'Hello '.$customername.' ('.$customeremail.')<br/>';

			$curdate = time();
			$todaydate = strtotime($row->duedate);
			
			if($curdate > $todaydate)
			{
				$message .= 'You have recieved a invoice with reference no: '.$row->reference_no.' which due date was '.$row->duedate.'. That is already expire. Please pay it now.';
			}else{
				$message .= 'You have recieved a invoice with reference no: '.$row->reference_no.' which due date is '.$row->duedate.'. Please pay soon.';
			}

				$message .= '<br/>
<br/>
'.$_POST['comment'];
			
			$users = $wpdb->prefix . 'users';
			$res = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$users.' WHERE `user_email` = "%s"',$row->customer_email));
			if(!empty($res)){
			$urole = service_finder_getUserRole($res->ID);
			}
			
			if(empty($res)){
			$userLink = service_finder_get_invoice_author_url($provider_id,'',$_POST['invoiceid']);
			}elseif($urole == 'Provider'){
			$userLink = service_finder_get_invoice_author_url($provider_id,'',$_POST['invoiceid']);
			}else{
			$userLink = '';
			}
			
			if($userLink != ""){
				
				$message .= '<br/>
<br/>
<a href="'.esc_url($userLink).'">'.esc_html__('Pay Now','service-finder').'</a>';
			
			}
			
			$msg_body = $message;
			$msg_subject = 'Invoice Reminder Mail';
			
			if(service_finder_wpmailer($row->customer_email,$msg_subject,$msg_body)) {

				$success = array(
						'status' => 'success',
						'suc_message' => esc_html__('Message has been sent', 'service-finder'),
						);
				echo json_encode($success);
				
			} else {
					
				$error = array(
						'status' => 'error',
						'err_message' => esc_html__('Message could not be sent.', 'service-finder'),
						);
				echo json_encode($error);
			}
			exit;
} 

if(isset($_POST['invoicepayment_mode']) && ($_POST['invoicepayment_mode'] == 'wired')){

$service_finder_options = get_option('service_finder_options');
$wpdb = service_finder_plugin_global_vars('wpdb');
$service_finder_Tables = service_finder_plugin_global_vars('service_finder_Tables');

	$invoiceid = (isset($_POST['invoiceid'])) ? $_POST['invoiceid'] : '';
	$providerid = isset($_POST['provider']) ? esc_html($_POST['provider']) : '';

	$wired_invoiceid = strtoupper(uniqid('INV-'));

	$data = array(
	'payment_type' => 'wire-transfer',
	'status' => 'on-hold',
	'txnid' => $wired_invoiceid,
	);
	
	$where = array(
	'id' => $invoiceid
	);
	
	$wpdb->update($service_finder_Tables->invoice,wp_unslash($data),$where);
	
	if(!is_user_logged_in()){

	$userLink = service_finder_get_author_url($providerid);
	$redirect = $userLink.'?invoicepaidcompleted=wired';

	}else{
	
	$accounturl = service_finder_get_url_by_shortcode('[service_finder_my_account]');
	$myaccount = $accounturl;
	if(service_finder_using_permalink()){
	$myaccount = $myaccount.'?';
	}else{
	$myaccount = $myaccount.'&';
	}
	
	$redirect = $myaccount.'action=invoice&invoicepaidcompleted=wired';
	
	}
	
	service_finder_send_invoice_amount_wiretansfer_mail($invoiceid,$wired_invoiceid);
	
	wp_redirect($redirect);
	die;
}

function service_finder_send_invoice_amount_wiretansfer_mail($invoiceid,$wired_invoiceid){
global $wpdb, $service_finder_Tables, $service_finder_options;

$subject = esc_html__('Invoice ID for pay invoice amount via Wire Transfer', 'service-finder');

$invoiceidata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->invoice.' WHERE `id` = "%d"',$invoiceid));
$email = $invoiceidata->customer_email;

$wiretransfermailinstructions = (!empty($service_finder_options['wire-transfer-mail-instructions'])) ? $service_finder_options['wire-transfer-mail-instructions'] : '';
if($wiretransfermailinstructions != ''){
	$message = $wiretransfermailinstructions;
}else{
	$message = 'Use following invoice ID When transfer amount in bank.';
}

$message .= esc_html__('Invoice ID:', 'service-finder').$wired_invoiceid;

service_finder_wpmailer($email,$subject,$message);
}