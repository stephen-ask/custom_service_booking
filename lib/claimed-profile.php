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
$service_finder_options = get_option('service_finder_options');
$wpdb = service_finder_plugin_global_vars('wpdb');
$paypal = service_finder_plugin_global_vars('paypal');
$service_finder_Errors = service_finder_plugin_global_vars('service_finder_Errors');
$service_finder_Tables = service_finder_plugin_global_vars('service_finder_Tables');
$registerErrors = service_finder_plugin_global_vars('registerErrors');
$registerMessages = service_finder_plugin_global_vars('registerMessages');

/*Define paypal credentials*/
$creds = array();
$paypalCreds['USER'] = (isset($service_finder_options['paypal-username'])) ? $service_finder_options['paypal-username'] : '';
$paypalCreds['PWD'] = (isset($service_finder_options['paypal-password'])) ? $service_finder_options['paypal-password'] : '';
$paypalCreds['SIGNATURE'] = (isset($service_finder_options['paypal-signatue'])) ? $service_finder_options['paypal-signatue'] : '';
$paypalType = (isset($service_finder_options['paypal-type']) && $service_finder_options['paypal-type'] == 'live') ? '' : 'sandbox.';


$paypalTypeBool = (!empty($paypalType)) ? true : false;

$paypal = new Paypal($paypalCreds,$paypalTypeBool);

/**
 * Claimed Business
 */
$payment_mode = (isset($_POST['claim_payment_mode'])) ? esc_html($_POST['claim_payment_mode']) : '';
$freemode = (isset($_POST['freemode'])) ? esc_html($_POST['freemode']) : '';
$claimedprofile = (isset($_POST['claimedprofile'])) ? esc_html($_POST['claimedprofile']) : '';
$claimedbusinessid = (isset($_POST['claimedbusinessid'])) ? esc_html($_POST['claimedbusinessid']) : '';
$profileid = (isset($_POST['profileid'])) ? esc_html($_POST['profileid']) : '';
$wootype = (isset($_POST['wootype'])) ? $_POST['wootype'] : '';

if($wootype != 'claimbusiness' && (isset($payment_mode) || (isset($freemode) && $freemode == 'yes')) && ($payment_mode == 'paypal' || $payment_mode == 'wired' || (isset($freemode) && $freemode == 'yes')) && isset($claimedprofile) && $claimedbusinessid > 0 && $profileid > 0) {

	$currentRole =  get_user_meta($profileid,'provider_role',true);
	$currentPayType = get_user_meta($profileid,'pay_type',true);
	if($currentPayType == 'single'){
		$paidAmount =  get_user_meta($profileid,'profile_amt',true);
	}
	$userId = $profileid;
	
	$roleNum = 1;
	$rolePrice = '0';
	$free = true;
	$price = '0';
	$packageName = '';
	// set role
	$get_provider_role = (isset($_POST['provider-role'])) ? esc_html($_POST['provider-role']) : '';
	if(isset($get_provider_role) || $freemode == 'yes'){
		$role = $get_provider_role;
		if (($role == "package_1") || ($role == "package_2") || ($role == "package_3") || ($freemode == 'yes')){
			$roleNum = intval(substr($role, 8));
			switch ($role) {
				case "package_1":
					if(isset($service_finder_options['package1-price']) && trim($service_finder_options['package1-price']) !== '0') {
						$rolePrice = $service_finder_options['package1-price'];
						$free = false;
						$packageName = $service_finder_options['package1-name'];
						$price = trim($service_finder_options['package1-price']);								
					}
					break;
				case "package_2":
					if(isset($service_finder_options['package2-price']) && trim($service_finder_options['package2-price']) !== '0') {
						$rolePrice = $service_finder_options['package2-price'];
						$free = false;
						$packageName = $service_finder_options['package2-name'];
						
						$price = trim($service_finder_options['package2-price']);								
					}
					break;
				case "package_3":
					if(isset($service_finder_options['package3-price']) && trim($service_finder_options['package3-price']) !== '0') {
						$rolePrice = $service_finder_options['package3-price'];
						$free = false;
						$packageName = $service_finder_options['package3-name'];
						
						$price = trim($service_finder_options['package3-price']);								
					}
					break;
				default:
					break;
			}
			$type = '';
			// non free
			if( isset($service_finder_options['enable-paypal']) && (!$free) && $payment_mode != 'wired' ){

				$currencyCode = service_finder_currencycode();
				$sandbox = (isset($service_finder_options['paypal-type']) && $service_finder_options['paypal-type'] == 'live') ? '' : 'sandbox.';
				$paymentName = (isset($service_finder_options['paypal-payment-name'])) ? $service_finder_options['paypal-payment-name'] : esc_html__('Payment via Paypal','service-finder');
				$paymentDescription = $packageName;

				$returnUrl = add_query_arg( array('claim-profile' => 'success','role' => $role), home_url() ); 
				$cancelUrl = add_query_arg( array('claim-profile' => 'cancel'), home_url() );
				$urlParams = array(
					'RETURNURL' => $returnUrl,
					'CANCELURL' => $cancelUrl
				);
				

				if (isset($service_finder_options['payment-type']) && ($service_finder_options['payment-type'] == 'recurring')) {
					
					$billingPeriod = esc_html__('year','service-finder');
					switch ($service_finder_options['package'.$roleNum.'-billing-period']) {
						case 'Year':
							$billingPeriod = esc_html__('year','service-finder');
							break;
						case 'Month':
							$billingPeriod = esc_html__('month','service-finder');
							break;
						case 'Week':
							$billingPeriod = esc_html__('week','service-finder');
							break;
						case 'Day':
							$billingPeriod = esc_html__('day','service-finder');
							break;
					}
					$recurringDescription = $rolePrice.' '.$currencyCode.' '.esc_html__('per','service-finder').' '.$billingPeriod;
					$recurringDescriptionFull = $rolePrice.' '.$currencyCode.' '.esc_html__('per','service-finder').' '.$billingPeriod.' '.esc_html__('for','service-finder').' '.$packageName;
					// Recurring payments
					$recurring = array(
						'L_BILLINGTYPE0' => 'RecurringPayments',
						'L_BILLINGAGREEMENTDESCRIPTION0' => $recurringDescriptionFull
					);
					$params = $urlParams + $recurring;

				} else {
					// Single payments
					$orderParams = array(
						'PAYMENTREQUEST_0_AMT' => $price,
						'PAYMENTREQUEST_0_SHIPPINGAMT' => '0',
						'PAYMENTREQUEST_0_CURRENCYCODE' => $currencyCode,
						'PAYMENTREQUEST_0_ITEMAMT' => $price
					);
					$itemParams = array(
						'L_PAYMENTREQUEST_0_NAME0' => $paymentName,
						'L_PAYMENTREQUEST_0_DESC0' => $paymentDescription,
						'L_PAYMENTREQUEST_0_AMT0' => $price,
						'L_PAYMENTREQUEST_0_QTY0' => '1'
					);
					$params = $urlParams + $orderParams + $itemParams;

				}
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
					update_user_meta($userId, 'paypal_token', $token);
					update_user_meta($userId, 'reg_paypal_role', $role);

					// write recurring data
					if (isset($service_finder_options['payment-type']) && ($service_finder_options['payment-type'] == 'recurring')) {

						
						if($currentPayType == 'single'){
							delete_user_meta($userId, 'expire_limit');
							delete_user_meta($userId, 'profile_amt');
						}
						
						update_user_meta( $userId, 'provider_activation_time', array( 'role' => $role, 'time' => time()) );
						
						$type = 'upgrade';
						update_user_meta($userId, 'recurring_profile_type',$type);
						
						update_user_meta( $userId, 'pay_type', 'recurring' );

						update_user_meta($userId, 'recurring_profile_amt',$rolePrice);
						update_user_meta($userId, 'recurring_profile_init_amt',$price);
						update_user_meta($userId, 'recurring_profile_period',$service_finder_options['package'.$roleNum.'-billing-period']);
						update_user_meta($userId, 'recurring_profile_desc_full',$recurringDescriptionFull); 
						update_user_meta($userId, 'recurring_profile_desc',$recurringDescription); 
						update_user_meta( $userId, 'payment_mode', $payment_mode );

					}
					// go to payment site
					header( 'Location: https://www.'.$sandbox.'paypal.com/webscr?cmd=_express-checkout&token=' . urlencode($token) );
					die();

				} else {
					$errorMessage = esc_html__( 'ERROR: Bad paypal API settings! Check paypal api credentials in admin settings!', 'service-finder' );
					$detailErrorMessage = (isset($response['L_LONGMESSAGE0'])) ? $response['L_LONGMESSAGE0'] : '';
					$errors->add( 'bad_paypal_api', $errorMessage . ' ' . $detailErrorMessage );
					$registerErrors = $errors;
				}

			} else {
				// free
				$userdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$wpdb->users.' WHERE `ID` = %d',$userId));
				$userInfo = service_finder_getUserInfo($userId);
				
				$expire_limit = $service_finder_options['package'.$roleNum.'-expday'];
				
				if($payment_mode == 'wired'){
					$invoiceid = '';
					$paymentvia = 'wired';
					$invoiceid = strtoupper(uniqid('REG-'));
					update_user_meta( $userId, 'wired_invoiceid', $invoiceid );

					$wiredclaimed = array();
			
					$wiredclaimed['payment_type'] = 'local';
					$wiredclaimed['payment_mode'] = $paymentvia;
					
					$wiredclaimed['rolePrice'] = $rolePrice;
					$wiredclaimed['pay_type'] = 'single';
					$wiredclaimed['roleNum'] = $roleNum;
					$wiredclaimed['role'] = $role;
					$wiredclaimed['price'] = $price;
					
					$wiredclaimed['claimedbusinessid'] = $claimedbusinessid;
					
					$wiredclaimed['time'] = time();
					
					$wiredclaimed['wired_invoiceid'] = $invoiceid;
					
					$wiredclaimed['recurring_profile_type'] = $type;
					
					if($expire_limit > 0){
					$wiredclaimed['expire_limit'] = $expire_limit;
					}
					
					update_user_meta($userId, 'claimed_request',$wiredclaimed);
					update_user_meta($userId, 'claimed_request_status','pending');
					update_user_meta($userId, 'claimed_order_id', $invoiceid);
					
					$data = array(
					'payment_mode' => 'wired',
					'payment_type' => 'local',
					'payment_status' => 'on-hold',
					'txnid' => $invoiceid,
					);
					
					$where = array(
					'id' => $claimedbusinessid
					);
					
					$wpdb->update($service_finder_Tables->claim_business,wp_unslash($data),$where);
					
					$paymentstatus = 'Wire Transfer';
					
					$roleName = (!empty($service_finder_options['package'.$roleNum.'-name'])) ? $service_finder_options['package'.$roleNum.'-name'] : '';
					
					$args = array(
						'username' => $userdata->user_login,
						'email' => $userdata->user_email,
						'package_name' => $roleName,
						'payment_type' => $paymentstatus
						);
					
					service_finder_after_claimedpayment_user_via_wiretransfer($profileid,$claimedbusinessid,$invoiceid);
					service_finder_after_claimedpayment_admin($args,$claimedbusinessid,$invoiceid);
					
					$pageid = (!empty($service_finder_options['claimed-redirect-option'])) ? $service_finder_options['claimed-redirect-option'] : '';
					if($pageid == 'no'){
					$redirect = add_query_arg( array('claimed' => 'success'), service_finder_get_url_by_shortcode('[service_finder_success_message]') );
					}else{
					$redirect = add_query_arg( array('claimed' => 'success'), get_permalink($pageid) );
					}
					
					wp_redirect($redirect);
					die;
					
				}else{
				$paymentvia = 'free';
				
				update_user_meta( $userId, 'payment_mode', $paymentvia );
				
				update_user_meta($userId, 'recurring_profile_type',$type);
				update_user_meta( $userId, 'provider_role', $role );
				if($expire_limit > 0){
				update_user_meta($userId, 'expire_limit', $expire_limit);
				}
				update_user_meta( $userId, 'provider_activation_time', array( 'role' => $role, 'time' => time()) );
				
				$roleNum = intval(substr($role, 8));
				$roleName = (!empty($service_finder_options['package'.$roleNum.'-name'])) ? $service_finder_options['package'.$roleNum.'-name'] : '';
				
				$userInfo = service_finder_getUserInfo($userId);
				if($payment_mode == 'wired'){
					$paymentstatus = 'Wire Transfer';
				}else{
					$paymentstatus = 'Free';
				}
				$args = array(
						'username' => $userdata->user_login,
						'email' => $userdata->user_email,
						'package_name' => $roleName,
						'payment_type' => $paymentstatus
						);
				
				service_finder_update_job_limit($userId);
				
				service_finder_after_claimedpayment_user($profileid,$claimedbusinessid);
				service_finder_after_claimedpayment_admin($args,$claimedbusinessid);
				
				$pageid = (!empty($service_finder_options['claimed-redirect-option'])) ? $service_finder_options['claimed-redirect-option'] : '';
				if($pageid == 'no'){
				$redirect = add_query_arg( array('claimed' => 'success'), service_finder_get_url_by_shortcode('[service_finder_success_message]') );
				}else{
				$redirect = add_query_arg( array('claimed' => 'success'), get_permalink($pageid) );
				}
				
				wp_redirect($redirect);
				die;
				}
				
			}
		}
	}
	
	unset($_POST);
}

// check token (paypal merchant authorization) and Do Payment
$userregister = isset($_GET['claim-profile']) ? esc_html($_GET['claim-profile']) : '';
if(isset($_GET['claim-profile']) && ($userregister == 'success') && !empty($_GET['token'])) {
	// find token
	$token = isset($_GET['token']) ? esc_html($_GET['token']) : '';
	$currentPayType = '';
	$tokenRow = $wpdb->get_row( $wpdb->prepare("SELECT * FROM ".$wpdb->usermeta." WHERE meta_value = '%s'",$token) );
	if(!empty($tokenRow)){
		
		// get user id
		$userId = $tokenRow->user_id;
		$userdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$wpdb->users.' WHERE `ID` = %d',$userId));
		$userInfo = service_finder_getUserInfo($userId);
		// delete token from DB
		
		// get role
		$role = get_user_meta($userId,'reg_paypal_role',true);

		// get checkout details from token
		$checkoutDetails = $paypal -> request('GetExpressCheckoutDetails', array('TOKEN' => $token));
		if( is_array($checkoutDetails) && ($checkoutDetails['ACK'] == 'Success') ) {
			
			// check if payment is recurring
			if (isset($checkoutDetails['BILLINGAGREEMENTACCEPTEDSTATUS']) && $checkoutDetails['BILLINGAGREEMENTACCEPTEDSTATUS']) {

				// Cancel old profile
				$oldProfile = get_user_meta($userId,'recurring_profile_id',true);
				if (!empty($oldProfile)) {
					$cancelParams = array(
						'PROFILEID' => $oldProfile,
						'ACTION' => 'Cancel'
					);
					$res = $paypal -> request('ManageRecurringPaymentsProfileStatus',$cancelParams);
					
					if($res['ACK'] != 'Success'){
						$redirect = add_query_arg( array('claimed' => 'failed'), service_finder_get_url_by_shortcode('[service_finder_claimbusiness_payment]') );
						wp_redirect(esc_url($redirect));
						die;
					}
				}
				$oldStripeProfile = get_user_meta($userId,'subscription_id',true);
				$merchantOrderId = get_user_meta($userId,'merchantOrderId',true);
				$orderNumber = get_user_meta($userId,'orderNumber',true);
				if (!empty($oldStripeProfile)) {
						require_once(SERVICE_FINDER_PAYMENT_GATEWAY_DIR.'/stripe/init.php');
						if( isset($service_finder_options['stripe-type']) && $service_finder_options['stripe-type'] == 'test' ){
							$secret_key = $service_finder_options['stripe-test-secret-key'];
							$public_key = $service_finder_options['stripe-test-public-key'];
						}else{
							$secret_key = $service_finder_options['stripe-live-secret-key'];
							$public_key = $service_finder_options['stripe-live-public-key'];
						}
						\Stripe\Stripe::setApiKey($secret_key);
						try {
						$subID = get_user_meta($userId, 'subscription_id',true);
						$cusID = get_user_meta($userId, 'stripe_customer_id',true);
						
						$currentcustomer = \Stripe\Customer::retrieve($cusID);
						
						$res = $currentcustomer->subscriptions->retrieve($subID)->cancel();
						
						if($res->status != 'canceled'){
							$redirect = add_query_arg( array('claimed' => 'failed'), service_finder_get_url_by_shortcode('[service_finder_claimbusiness_payment]') );
							wp_redirect(esc_url($redirect));
							die;
						}
						
						} catch (Exception $e) {
						}
				}elseif($merchantOrderId != "" && $orderNumber != ""){
					require_once(SERVICE_FINDER_PAYMENT_GATEWAY_DIR.'/2checkout/lib/Twocheckout.php');
					
					$twocheckout_api_username = $service_finder_options['twocheckout-api-username'];
					$twocheckout_api_password = $service_finder_options['twocheckout-api-password'];
					
					Twocheckout::username($twocheckout_api_username);
					Twocheckout::password($twocheckout_api_password);
					
					if($twocheckouttype == 'test'){
						Twocheckout::verifySSL(false);
						Twocheckout::sandbox(true);
					}
					
					$args = array(
						'sale_id' => $orderNumber
					);
					try {
						$result = Twocheckout_Sale::stop($args);

						delete_user_meta($userId, 'merchantOrderId');
						delete_user_meta($userId, 'orderNumber');

					} catch (Twocheckout_Error $e) {
						$e->getMessage();
						echo sprintf( esc_html__('%s', 'service-finder'), $e->getMessage() );
					}
				}
				
				$type = get_user_meta($userId,'recurring_profile_type',true);
				if (!empty($type)) {
					$initAmt = get_user_meta($userId,'recurring_profile_init_amt',true);
				} else {
					$initAmt = get_user_meta($userId,'recurring_profile_amt',true);
				}
				$amt = get_user_meta($userId,'recurring_profile_amt',true);
				$currencyCode = service_finder_currencycode();
				$description = get_user_meta($userId,'recurring_profile_desc_full',true);
				$desc = get_user_meta($userId,'recurring_profile_desc',true);
				$period = get_user_meta($userId,'recurring_profile_period',true);

				$periodNum = (60 * 60 * 24 * 365);
				switch ($period) {
					case 'Year':
						$periodNum = (60 * 60 * 24 * 365);
						break;
					case 'Month':
						$periodNum = (60 * 60 * 24 * 30);
						break;
					case 'Week':
						$periodNum = (60 * 60 * 24 * 7);
						break;
					case 'Day':
						$periodNum = (60 * 60 * 24);
						break;
				}

				$timeToBegin = time() + $periodNum;
				$begins = date('Y-m-d',$timeToBegin).'T'.'00:00:00Z';

				// Recurring payment
				$recurringParams = array(
					'TOKEN' => $checkoutDetails['TOKEN'],
					'PAYERID' => $checkoutDetails['PAYERID'],
					'INITAMT' => $initAmt,
					'AMT' => $amt,
					'CURRENCYCODE' => $currencyCode,
					'DESC' => $description,
					'BILLINGPERIOD' => $period,
					'BILLINGFREQUENCY' => '1',
					'PROFILESTARTDATE' => $begins,
					'FAILEDINITAMTACTION' => 'CancelOnFailure',
					'AUTOBILLOUTAMT' => 'NoAutoBill',
					'MAXFAILEDPAYMENTS' => '0'
				);
				$recurringPayment = $paypal -> request('CreateRecurringPaymentsProfile', $recurringParams);

				// recurring profile created
				if( is_array($recurringPayment) && $recurringPayment['ACK'] == 'Success' ) {
					
					// write profile id to DB
					update_user_meta( $userId, 'recurring_profile_id', $recurringPayment['PROFILEID'] );

					// set role
					$user = new WP_User( $userId );
					$user->set_role('Provider');
					
					update_user_meta( $userId, 'provider_role', $role );
					// write description to DB
					update_user_meta( $userId, 'recurring_profile_active_desc', $desc );
					
					$roleNum = intval(substr($role, 8));
					$roleName = $service_finder_options['package'.$roleNum.'-name'];
					
					// write activation time only for info
					$paymode = $payment_mode;
					$userInfo = service_finder_getUserInfo($userId);
					$args = array(
							'username' => $userdata->user_login,
							'email' => $userdata->user_email,
							'package_name' => $roleName,
							'payment_type' => $paymode
							);
					
					service_finder_update_job_limit($userId);
					
					service_finder_after_claimedpayment_user($profileid,$claimedbusinessid);
					service_finder_after_claimedpayment_admin($args,$claimedbusinessid);
					
					$pageid = (!empty($service_finder_options['claimed-redirect-option'])) ? $service_finder_options['claimed-redirect-option'] : '';
					if($pageid == 'no'){
					$redirect = add_query_arg( array('claimed' => 'success'), service_finder_get_url_by_shortcode('[service_finder_success_message]') );
					}else{
					$redirect = add_query_arg( array('claimed' => 'success'), get_permalink($pageid) );
					}
					
					wp_redirect(esc_url($redirect));
					die;
				}

			} else {
				 
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
					// set role
					$user = new WP_User( $userId );
					$user->set_role('Provider');
					
					if($currentPayType == 'recurring'){

					delete_user_meta($userId, 'recurring_profile_amt');
					delete_user_meta($userId, 'recurring_profile_period');
					delete_user_meta($userId, 'recurring_profile_desc_full'); 
					delete_user_meta($userId, 'recurring_profile_desc'); 
					delete_user_meta($userId, 'recurring_profile_type');

					}
					$roleNum = intval(substr($role, 8));
					$rolePrice = $service_finder_options['package'.$roleNum.'-price'];
					$roleName = $service_finder_options['package'.$roleNum.'-name'];
					$expire_limit = $service_finder_options['package'.$roleNum.'-expday'];
					
					update_user_meta( $userId, 'provider_activation_time', array( 'role' => $role, 'time' => time()) );
					update_user_meta( $userId, 'provider_role', $role );
					update_user_meta($userId, 'expire_limit', $expire_limit);
					update_user_meta($userId, 'profile_amt',$rolePrice);
					
					$payment_mode = (isset($payment_mode)) ? $payment_mode : '';
					update_user_meta( $userId, 'payment_mode', $payment_mode );

					// We'll fetch the transaction ID for internal bookkeeping
					$transactionId = $singlePayment['PAYMENTINFO_0_TRANSACTIONID'];
					update_user_meta( $userId, 'txn_id', $transactionId );
					
					
					$paymode = $payment_mode;
					$userInfo = service_finder_getUserInfo($userId);
					$args = array(
							'username' => (!empty($userdata->user_login)) ? $userdata->user_login : '',
							'email' => (!empty($userdata->user_email)) ? $userdata->user_email : '',
							'package_name' => $roleName,
							'payment_type' => $paymode
							);
					
					service_finder_update_job_limit($userId);
					
					service_finder_after_claimedpayment_user($profileid,$claimedbusinessid);
					service_finder_after_claimedpayment_admin($args,$claimedbusinessid);
					
					$pageid = (!empty($service_finder_options['claimed-redirect-option'])) ? $service_finder_options['claimed-redirect-option'] : '';
					if($pageid == 'no'){
					$redirect = add_query_arg( array('claimed' => 'success'), service_finder_get_url_by_shortcode('[service_finder_success_message]') );
					}else{
					$redirect = add_query_arg( array('claimed' => 'success'), get_permalink($pageid) );
					}
					
					wp_redirect(esc_url($redirect));
					die;

				}

			}

		}

	}
}

// delete token and show messages if user cancel payment 
$userregister = isset($_GET['claim-profile']) ? esc_html($_GET['claim-profile']) : '';
if(isset($_GET['claim-profile']) && ($userregister == 'cancel') && isset($_GET['token'])){
	
// delete token from DB
$token = (isset($_GET['token'])) ? esc_html($_GET['token']) : '';
$tokenRow = $wpdb->get_row( $wpdb->prepare("SELECT * FROM ".$wpdb->usermeta." WHERE meta_value = '%s'",$token) );
if($tokenRow){
	
	// get user id
	$userId = $tokenRow->user_id;
	
	// show message
	$errors = new WP_Error();
	
	$message = esc_html__("You canceled payment. Your account wasn't claimed","service-finder");
	$errors->add( 'cancel_payment', $message);
	$registerErrors = $errors;
}	
}

/*User register and upgrade via payu money start*/
if(isset($payment_mode) && $payment_mode == 'payumoney' && isset($claimedprofile) && $claimedbusinessid > 0 && $profileid > 0) {

if( isset($service_finder_options['payumoney-type']) && $service_finder_options['payumoney-type'] == 'test' ){
	$MERCHANT_KEY = $service_finder_options['payumoney-key-test'];
	$SALT = $service_finder_options['payumoney-salt-test'];
	$PAYU_BASE_URL = "https://test.payu.in";
}else{
	$MERCHANT_KEY = $service_finder_options['payumoney-key-live'];
	$SALT = $service_finder_options['payumoney-salt-live'];
	$PAYU_BASE_URL = "https://secure.payu.in";
}

if($MERCHANT_KEY != "" && $SALT != "" && $PAYU_BASE_URL != ""){
// register user with minimal role

$currentRole =  get_user_meta($profileid,'provider_role',true);
$currentPayType = get_user_meta($profileid,'pay_type',true);
if($currentPayType == 'single'){
	$paidAmount =  get_user_meta($profileid,'profile_amt',true);
}
$userId = $profileid;

$roleNum = 1;
$rolePrice = '0';
$price = '0';
$packageName = '';
// set role
$get_provider_role = (isset($_POST['provider-role'])) ? esc_html($_POST['provider-role']) : '';
if(isset($get_provider_role)){
	$role = $get_provider_role;
	if (($role == "package_1") || ($role == "package_2") || ($role == "package_3") || ($freemode == 'yes')){
		$roleNum = intval(substr($role, 8));
		switch ($role) {
			case "package_1":
				if(isset($service_finder_options['package1-price']) && trim($service_finder_options['package1-price']) !== '0') {
					$rolePrice = $service_finder_options['package1-price'];
					$packageName = $service_finder_options['package1-name'];
					
					$price = trim($service_finder_options['package1-price']);								
				}
				break;
			case "package_2":
				if(isset($service_finder_options['package2-price']) && trim($service_finder_options['package2-price']) !== '0') {
					$rolePrice = $service_finder_options['package2-price'];
					$packageName = $service_finder_options['package2-name'];
					
					$price = trim($service_finder_options['package2-price']);								
				}
				break;
			case "package_3":
				if(isset($service_finder_options['package3-price']) && trim($service_finder_options['package3-price']) !== '0') {
					$rolePrice = $service_finder_options['package3-price'];
					$packageName = $service_finder_options['package3-name'];
					
					$price = trim($service_finder_options['package3-price']);								
				}
				break;
			default:
				break;
		}
		$type = '';

			$currencyCode = service_finder_currencycode();
			$paymentName = esc_html__('Payment via PayU Money','service-finder');
			$paymentDescription = esc_html__('Upgrade to ','service-finder') . $packageName;

			$paymentName .= esc_html__(' Upgrade','service-finder');
			
			$surl = add_query_arg( array('claimviapayumoney' => 'success','upgrade' => '1','payutransactionforreg' => 'success'), service_finder_get_url_by_shortcode('[service_finder_claimbusiness_payment]') );
			
			$furl = add_query_arg( array('claimviapayumoney' => 'failed','payutransactionforreg' => 'failed','upgrade' => '1'), service_finder_get_url_by_shortcode('[service_finder_claimbusiness_payment]') );
			
			$txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
			
			update_user_meta( $userId, 'txn_id', $txnid );
			
			$action = $PAYU_BASE_URL . '/_payment';
			
			$productinfo = $paymentDescription;
			
			$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' where wp_user_id = %d',$userId));
			
			$first_name = get_user_meta($userId,'first_name',true);
			$user_email = $row->email;
			$phone = $row->phone;
			
			$str = "$MERCHANT_KEY|$txnid|$price|$productinfo|$first_name|$user_email|$userId|$role|||||||||$SALT";
			
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
				'udf1' 			=> $userId,
				'udf2' 			=> $role,
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
}

}else{
		global $registerErrors;
		$errors = new WP_Error();
		$message = esc_html__("Please set PayU Money Credentials","service-finder");
		$errors->add( 'set_credentials', $message);
		$registerErrors = $errors;
}
}

if(isset($_GET['claimviapayumoney']) && $_GET['claimviapayumoney'] == 'success' && $_GET['payutransactionforreg'] == 'success' && isset($_GET['payutransactionforreg']) && isset($_POST['mihpayid']) && isset($_POST['status'])){

$upgrade = false;
$getupgrade = isset($_GET['upgrade']) ? esc_html($_GET['upgrade']) : '';
if($getupgrade == 1){
	$upgrade = true;
}
$userId = (isset($_POST['udf1'])) ? esc_html($_POST['udf1']) : '';
$role = (isset($_POST['udf2'])) ? esc_html($_POST['udf2']) : '';
$txnid = (isset($_POST['txnid'])) ? esc_html($_POST['txnid']) : '';
$payuMoneyId = (isset($_POST['mihpayid'])) ? esc_html($_POST['mihpayid']) : '';
$status = (isset($_POST['status'])) ? esc_html($_POST['status']) : '';
$currentPayType = get_user_meta($userId,'pay_type',true);

if($status == 'success' && $payuMoneyId != ""){

// Cancel old profile
$oldProfile = get_user_meta($userId,'recurring_profile_id',true);
if (!empty($oldProfile)) {
	$cancelParams = array(
		'PROFILEID' => $oldProfile,
		'ACTION' => 'Cancel'
	);
	$res = $paypal -> request('ManageRecurringPaymentsProfileStatus',$cancelParams);
	
	if($res['ACK'] != 'Success'){
		$redirect = add_query_arg( array('upgrade' => 'failed'), service_finder_get_url_by_shortcode('[service_finder_claimbusiness_payment]') );
		wp_redirect(esc_url($redirect));
		die;
	}
}

if($upgrade){
		$oldStripeProfile = get_user_meta($userId,'subscription_id',true);
		$merchantOrderId = get_user_meta($userId,'merchantOrderId',true);
		$orderNumber = get_user_meta($userId,'orderNumber',true);
		if (!empty($oldStripeProfile)) {
				require_once(SERVICE_FINDER_PAYMENT_GATEWAY_DIR.'/stripe/init.php');
				if( isset($service_finder_options['stripe-type']) && $service_finder_options['stripe-type'] == 'test' ){
					$secret_key = $service_finder_options['stripe-test-secret-key'];
					$public_key = $service_finder_options['stripe-test-public-key'];
				}else{
					$secret_key = $service_finder_options['stripe-live-secret-key'];
					$public_key = $service_finder_options['stripe-live-public-key'];
				}
				\Stripe\Stripe::setApiKey($secret_key);
				try {
				$subID = get_user_meta($userId, 'subscription_id',true);
				$cusID = get_user_meta($userId, 'stripe_customer_id',true);
				
				$currentcustomer = \Stripe\Customer::retrieve($cusID);
				
				$res = $currentcustomer->subscriptions->retrieve($subID)->cancel();
				
				if($res->status != 'canceled'){
					$redirect = add_query_arg( array('upgrade' => 'failed'), service_finder_get_url_by_shortcode('[service_finder_claimbusiness_payment]') );
					wp_redirect(esc_url($redirect));
					die;
				}
				
				} catch (Exception $e) {
				}
		}elseif($merchantOrderId != "" && $orderNumber != ""){
			require_once(SERVICE_FINDER_PAYMENT_GATEWAY_DIR.'/2checkout/lib/Twocheckout.php');
			
			$twocheckout_api_username = $service_finder_options['twocheckout-api-username'];
			$twocheckout_api_password = $service_finder_options['twocheckout-api-password'];
			
			Twocheckout::username($twocheckout_api_username);
			Twocheckout::password($twocheckout_api_password);
			
			if($twocheckouttype == 'test'){
				Twocheckout::verifySSL(false);
				Twocheckout::sandbox(true);
			}
			
			$args = array(
				'sale_id' => $orderNumber
			);
			try {
				$result = Twocheckout_Sale::stop($args);

				delete_user_meta($userId, 'merchantOrderId');
				delete_user_meta($userId, 'orderNumber');

			} catch (Twocheckout_Error $e) {
				$e->getMessage();
				echo sprintf( esc_html__('%s', 'service-finder'), $e->getMessage() );
			}
		}
}

$userdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$wpdb->users.' WHERE `ID` = %d',$userId));
$userInfo = service_finder_getUserInfo($userId);



$user = new WP_User( $userId );
$user->set_role('Provider');

if($upgrade && $currentPayType == 'recurring'){

delete_user_meta($userId, 'recurring_profile_amt');
delete_user_meta($userId, 'recurring_profile_period');
delete_user_meta($userId, 'recurring_profile_desc_full'); 
delete_user_meta($userId, 'recurring_profile_desc'); 
delete_user_meta($userId, 'recurring_profile_type');

}
$roleNum = intval(substr($role, 8));
$rolePrice = $service_finder_options['package'.$roleNum.'-price'];
$roleName = $service_finder_options['package'.$roleNum.'-name'];
$expire_limit = $service_finder_options['package'.$roleNum.'-expday'];

update_user_meta( $userId, 'provider_activation_time', array( 'role' => $role, 'time' => time()) );
update_user_meta( $userId, 'provider_role', $role );
update_user_meta($userId, 'expire_limit', $expire_limit);
update_user_meta($userId, 'profile_amt',$rolePrice);

$pay_mode = 'payumoney_upgrade';
$payment_mode = 'payumoney';
if($upgrade){
update_user_meta( $userId, 'payment_mode', 'payumoney_upgrade' );
}else{
update_user_meta( $userId, 'payment_mode', 'payumoney' );
}

$paymode = ($upgrade) ? $pay_mode : $payment_mode;
$userInfo = service_finder_getUserInfo($userId);
$args = array(
		'username' => (!empty($userdata->user_login)) ? $userdata->user_login : '',
		'email' => (!empty($userdata->user_email)) ? $userdata->user_email : '',
		'package_name' => $roleName,
		'payment_type' => $paymode
		);

if($upgrade){
service_finder_update_job_limit($userId);

service_finder_after_claimedpayment_user($profileid,$claimedbusinessid);
service_finder_after_claimedpayment_admin($args,$claimedbusinessid);

$ulogin = (!empty($userdata->user_login)) ? $userdata->user_login : '';
$uemail = (!empty($userdata->user_email)) ? $userdata->user_email : '';
	
	$registerMessages = (!empty($service_finder_options['claimed-business-successfull'])) ? $service_finder_options['claimed-business-successfull'] : esc_html__('Your payment for this claimed business successfully.', 'service-finder');
	
	$current_user = wp_get_current_user(); 
	$redirect = add_query_arg( array('claimed' => 'success'), service_finder_get_url_by_shortcode('[service_finder_claimbusiness_payment]') );
	
}
wp_redirect(esc_url($redirect));
die;

}

}

if(isset($_GET['claimviapayumoney']) && $_GET['claimviapayumoney'] == 'failed' && $_GET['payutransactionforreg'] == 'failed'){
//print_r($_REQUEST);
$txnid = (isset($_POST['txnid'])) ? esc_html($_POST['txnid']) : '';
$upgrade = isset($_GET['upgrade']) ? esc_html($_GET['upgrade']) : '';
// delete token from DB
$txnRow = $wpdb->get_row( $wpdb->prepare("SELECT * FROM ".$wpdb->usermeta." WHERE meta_value = '%s'",$txnid) );
if(!empty($txnRow)){
	
// get user id
$userId = $txnRow->user_id;

// show message
$errors = new WP_Error();
if ($upgrade) {
	$message = esc_html__("You canceled payment. Your account wasn't changed.","service-finder");
	$errors->add( 'cancel_payment', $message);
	$registerErrors = $errors;
}

}	

}
/*User register and upgrade via payu money end*/

/* Provider Signup via ajax with stripe */
add_action('wp_ajax_claimed', 'service_finder_claimed');
add_action('wp_ajax_nopriv_claimed', 'service_finder_claimed');
function service_finder_claimed(){
global $wpdb, $service_finder_Errors, $service_finder_options, $paypal;
require_once(SERVICE_FINDER_PAYMENT_GATEWAY_DIR.'/stripe/init.php');

$payment_mode = (isset($_POST['claim_payment_mode'])) ? esc_html($_POST['claim_payment_mode']) : '';
$freemode = (isset($_POST['freemode'])) ? esc_html($_POST['freemode']) : '';
$claimedprofile = (isset($_POST['claimedprofile'])) ? esc_html($_POST['claimedprofile']) : '';
$claimedbusinessid = (isset($_POST['claimedbusinessid'])) ? esc_html($_POST['claimedbusinessid']) : '';
$profileid = (isset($_POST['profileid'])) ? esc_html($_POST['profileid']) : '';

$service_finder_options = get_option('service_finder_options');

$creds = array();
$paypalCreds['USER'] = (isset($service_finder_options['paypal-username'])) ? $service_finder_options['paypal-username'] : '';
$paypalCreds['PWD'] = (isset($service_finder_options['paypal-password'])) ? $service_finder_options['paypal-password'] : '';
$paypalCreds['SIGNATURE'] = (isset($service_finder_options['paypal-signatue'])) ? $service_finder_options['paypal-signatue'] : '';
$paypalType = (isset($service_finder_options['paypal-type']) && $service_finder_options['paypal-type'] == 'live') ? '' : 'sandbox.';

$paypalTypeBool = (!empty($paypalType)) ? true : false;

$paypal = new Paypal($paypalCreds,$paypalTypeBool);

	
$currentRole =  get_user_meta($profileid,'provider_role',true);
$currentPayType = get_user_meta($profileid,'pay_type',true);
if($currentPayType == 'single'){
	$paidAmount =  get_user_meta($profileid,'profile_amt',true);
}
$userId = $profileid;	

$signup_user_role = (isset($_POST['signup_user_role'])) ? esc_html($_POST['signup_user_role']) : '';
	
$roleNum = 1;
$rolePrice = '0';
$free = true;
$price = '0';
$packageName = '';
$get_provider_role = (isset($_POST['provider-role'])) ? esc_html($_POST['provider-role']) : '';
if(isset($get_provider_role)){
	$userdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$wpdb->users.' WHERE `ID` = %d',$userId));
	$userInfo = service_finder_getUserInfo($userId);
	// set role
	
	$role = $get_provider_role;
	if (($role == "package_1") || ($role == "package_2") || ($role == "package_3")){
		$roleNum = intval(substr($role, 8));
		switch ($role) {
			case "package_1":
				if(isset($service_finder_options['package1-price']) && trim($service_finder_options['package1-price']) !== '0') {
					$token = esc_html($_POST['stripeToken']);
					$rolePrice = $service_finder_options['package1-price'];
					$free = false;
					$packageName = $service_finder_options['package1-name'];
					if($service_finder_options['payment-type'] == 'single'){
					$expire_limit = $service_finder_options['package1-expday'];
					}
					$price = trim($service_finder_options['package1-price']);								
				}
				break;
			case "package_2":
				if(isset($service_finder_options['package2-price']) && trim($service_finder_options['package2-price']) !== '0') {
					$token = esc_html($_POST['stripeToken']);
					if($service_finder_options['payment-type'] == 'single'){
					$expire_limit = $service_finder_options['package2-expday'];
					}
					$rolePrice = $service_finder_options['package2-price'];
					$free = false;
					$packageName = $service_finder_options['package2-name'];
					$price = trim($service_finder_options['package2-price']);								
				}
				break;
			case "package_3":
				if(isset($service_finder_options['package3-price']) && trim($service_finder_options['package3-price']) !== '0') {
					$token = esc_html($_POST['stripeToken']);
					if($service_finder_options['payment-type'] == 'single'){
					$expire_limit = $service_finder_options['package3-expday'];
					}
					$rolePrice = $service_finder_options['package3-price'];
					$free = false;
					$packageName = $service_finder_options['package3-name'];
					$price = trim($service_finder_options['package3-price']);								
				}
				break;
			default:
				break;
		}
		// non free
		if( isset($service_finder_options['enable-stripe']) && (!$free) ){

			$currencyCode = service_finder_currencycode();
			if( isset($service_finder_options['stripe-type']) && $service_finder_options['stripe-type'] == 'test' ){
				$secret_key = $service_finder_options['stripe-test-secret-key'];
				$public_key = $service_finder_options['stripe-test-public-key'];
			}else{
				$secret_key = $service_finder_options['stripe-live-secret-key'];
				$public_key = $service_finder_options['stripe-live-public-key'];
			}
			
			$paymentName = (isset($service_finder_options['stripe-payment-name'])) ? $service_finder_options['stripe-payment-name'] : esc_html__('Payment via Credit Card','service-finder');
			$paymentDescription = $packageName;

			if (isset($service_finder_options['payment-type']) && ($service_finder_options['payment-type'] == 'recurring')) {
				
				$billingPeriod = esc_html__('year','service-finder');
				switch ($service_finder_options['package'.$roleNum.'-billing-period']) {
					case 'Year':
						$billingPeriod = esc_html__('year','service-finder');
						break;
					case 'Month':
						$billingPeriod = esc_html__('month','service-finder');
						break;
					case 'Week':
						$billingPeriod = esc_html__('week','service-finder');
						break;
					case 'Day':
						$billingPeriod = esc_html__('day','service-finder');
						break;
				}
				$recurringDescription = $rolePrice.' '.$currencyCode.' '.esc_html__('per','service-finder').' '.$billingPeriod;
				$recurringDescriptionFull = $rolePrice.' '.$currencyCode.' '.esc_html__('per','service-finder').' '.$billingPeriod.' '.esc_html__('for','service-finder').' '.$packageName;
				
				
	// recurring payment setup will go here
	\Stripe\Stripe::setApiKey($secret_key);
	try {			
		$customer = \Stripe\Customer::create(array(
							'card' => $token,
							'email' => $userdata->user_email,
							'description' => $recurringDescriptionFull
						)
					);
					
		
		$subscription_amount = $rolePrice * 100;
		

		$interval = $billingPeriod;
		$interval_count = 1;
			

			$plan = $role;
			
			
			if($currentPayType == 'recurring'){
						// Cancel old profile
						$oldProfile = get_user_meta($userId,'recurring_profile_id',true);
						$merchantOrderId = get_user_meta($userId,'merchantOrderId',true);
						$orderNumber = get_user_meta($userId,'orderNumber',true);
						$payulatam_planid = get_user_meta($userId,'payulatam_planid',true);
						$subscription_id = get_user_meta($userId,'subscription_id',true);
						if (!empty($oldProfile)) {
							$cancelParams = array(
								'PROFILEID' => $oldProfile,
								'ACTION' => 'Cancel'
							);
							$res = $paypal -> request('ManageRecurringPaymentsProfileStatus',$cancelParams);
							
							if($res['ACK'] != 'Success'){
								$error = array(
										'status' => 'error',
										'err_message' => $res['L_SHORTMESSAGE0']
										);
								echo json_encode($error);
								exit;
							}
							
							$cu = \Stripe\Customer::retrieve($customer->id);
							$getsubs = $cu->subscriptions->create(array("plan" => $plan));
							update_user_meta($userId, 'subscription_id',$getsubs->id);
							
							delete_user_meta($userId, 'recurring_profile_id');
							delete_user_meta($userId, 'recurring_profile_amt');
							delete_user_meta($userId, 'recurring_profile_period');
							delete_user_meta($userId, 'recurring_profile_desc_full'); 
							delete_user_meta($userId, 'recurring_profile_desc'); 
							delete_user_meta($userId, 'recurring_profile_type');
							delete_user_meta($userId, 'paypal_token');
							delete_user_meta($userId, 'reg_paypal_role');
						
						}elseif($payulatam_planid != "" && $subscription_id != ""){
						
						try {
						$parameters = array(
							// Enter the subscription ID here.
							PayUParameters::SUBSCRIPTION_ID => $subscription_id,
						);
						
						$response = PayUSubscriptions::cancel($parameters);
						
						if($response){
						
							delete_user_meta($userId, 'subscription_id');
							delete_user_meta($userId, 'payulatam_planid');
							delete_user_meta($userId, 'payulatam_customer_id');
							
							delete_user_meta($userId, 'recurring_profile_id');
							delete_user_meta($userId, 'recurring_profile_amt');
							delete_user_meta($userId, 'recurring_profile_period');
							delete_user_meta($userId, 'recurring_profile_desc_full'); 
							delete_user_meta($userId, 'recurring_profile_desc'); 
							delete_user_meta($userId, 'recurring_profile_type');
							delete_user_meta($userId, 'paypal_token');
							delete_user_meta($userId, 'reg_paypal_role');
							
						}
						} catch (Exception $e) {
							$error = array(
									'status' => 'error',
									'err_message' => $e->getMessage()
									);
							echo json_encode($error);
							exit;
						}
					
					
						}elseif($merchantOrderId != "" && $orderNumber != ""){
							require_once(SERVICE_FINDER_PAYMENT_GATEWAY_DIR.'/2checkout/lib/Twocheckout.php');
							$twocheckout_api_username = $service_finder_options['twocheckout-api-username'];
							$twocheckout_api_password = $service_finder_options['twocheckout-api-password'];
							
							Twocheckout::username($twocheckout_api_username);
							Twocheckout::password($twocheckout_api_password);
							
							if($twocheckouttype == 'test'){
								Twocheckout::verifySSL(false);
								Twocheckout::sandbox(true);
							}
							
							$args = array(
								'sale_id' => $orderNumber
							);
							try {
								$result = Twocheckout_Sale::stop($args);
								
								$cu = \Stripe\Customer::retrieve($customer->id);
								$getsubs = $cu->subscriptions->create(array("plan" => $plan));
								update_user_meta($userId, 'subscription_id',$getsubs->id);

								delete_user_meta($userId, 'merchantOrderId');
								delete_user_meta($userId, 'orderNumber');

							} catch (Twocheckout_Error $e) {
								$e->getMessage();
								$error = array(
										'status' => 'error',
										'err_message' => sprintf( esc_html__('%s', 'service-finder'), $e->getMessage() )
										);
								echo json_encode($error);
								exit;
							}
						}else{
						
						$subID = get_user_meta($userId, 'subscription_id',true);
						$cusID = get_user_meta($userId, 'stripe_customer_id',true);

						$currentcustomer = \Stripe\Customer::retrieve($cusID);
						$subscription = $currentcustomer->subscriptions->retrieve($subID);
						$subscription->plan = $plan;
						$subscription->save();
						update_user_meta($userId, 'subscription_id',$subID);
						}
			}else{
						$cu = \Stripe\Customer::retrieve($customer->id);
						$getsubs = $cu->subscriptions->create(array("plan" => $plan));
						update_user_meta($userId, 'subscription_id',$getsubs->id);
						update_user_meta($userId, 'stripe_customer_id', $customer->id);
			}
			
						$user = new WP_User( $userId );
						$user->set_role('Provider');
						
						
						if($currentPayType == 'single'){
						delete_user_meta($userId, 'expire_limit');
						delete_user_meta($userId, 'profile_amt');
						}
						
						
						
						update_user_meta( $userId, 'provider_activation_time', array( 'role' => $role, 'time' => time()) );
						update_user_meta($userId, 'stripe_token', $token);
						update_user_meta( $userId, 'provider_role', $role );
						update_user_meta( $userId, 'pay_type', 'recurring' );
						
						update_user_meta( $userId, 'payment_mode', $payment_mode );
						
						$type = 'upgrade';
						update_user_meta($userId, 'recurring_profile_type',$type);
						$roleNum = intval(substr($role, 8));
						$roleName = $service_finder_options['package'.$roleNum.'-name'];
						
						update_user_meta($userId, 'recurring_profile_amt',$rolePrice);
						update_user_meta($userId, 'recurring_profile_period',$service_finder_options['package'.$roleNum.'-billing-period']);
						update_user_meta($userId, 'recurring_profile_desc_full',$recurringDescriptionFull); 
						update_user_meta($userId, 'recurring_profile_desc',$recurringDescription); 
						$paymode = $payment_mode;
						$userInfo = service_finder_getUserInfo($userId);
						$args = array(
								'username' => $userdata->user_login,
								'email' => $userdata->user_email,
								'package_name' => $roleName,
								'payment_type' => $paymode
								);
						
						service_finder_update_job_limit($userId);
						
						service_finder_after_claimedpayment_user($profileid,$claimedbusinessid);
						service_finder_after_claimedpayment_admin($args,$claimedbusinessid);
						
						$registerMessages = (!empty($service_finder_options['claimed-business-successfull'])) ? $service_finder_options['claimed-business-successfull'] : esc_html__('Your payment for this claimed business successfully.', 'service-finder');
						
						$pageid = (!empty($service_finder_options['claimed-redirect-option'])) ? $service_finder_options['claimed-redirect-option'] : '';
						if($pageid == 'no'){
						$redirectURL = '';
						}else{
						$redirectURL = get_permalink($pageid);
						}

						$success = array(
									'status' => 'success',
									'redirecturl' => $redirectURL,
									'suc_message' => $registerMessages,
									);
						echo json_encode($success);	
						
					} catch (Exception $e) {
						$body = $e->getJsonBody();
						$err  = $body['error'];
						$error = array(
								'status' => 'error',
								'err_message' => sprintf( esc_html__('%s', 'service-finder'), $err['message'] )
								);
						echo json_encode($error);
					}

			} else {
				
			
				\Stripe\Stripe::setApiKey($secret_key);
				$signup_user_email = service_finder_getProviderEmail($userId);
				
				try {			
					$customer = \Stripe\Customer::create(array(
							'card' => $token,
							'email' => $signup_user_email,
							'description' => $paymentDescription
						)
					);	
		
					$charge = \Stripe\Charge::create(array(
								  "amount" => $price * 100,
								  "currency" => strtolower($currencyCode),
								  "customer" => $customer->id, // obtained with Stripe.js
								  "description" => "Charge for ".$paymentDescription
								));
		
					if ($charge->paid == true && $charge->status == "succeeded") { 
					
						// set role
						$user = new WP_User( $userId );
						$user->set_role('Provider');
						
						if($currentPayType == 'recurring'){
						
						// Cancel old profile
						$oldProfile = get_user_meta($userId,'recurring_profile_id',true);
						$payulatam_planid = get_user_meta($userId,'payulatam_planid',true);
						$subscription_id = get_user_meta($userId,'subscription_id',true);
						if (!empty($oldProfile)) {
							$cancelParams = array(
								'PROFILEID' => $oldProfile,
								'ACTION' => 'Cancel'
							);
							$res = $paypal -> request('ManageRecurringPaymentsProfileStatus',$cancelParams);
							
							if($res['ACK'] != 'Success'){
								$error = array(
										'status' => 'error',
										'err_message' => $res['L_SHORTMESSAGE0']
										);
								echo json_encode($error);
								exit;
							}
						}elseif($payulatam_planid != "" && $subscription_id != ""){
						
						try {
						$parameters = array(
							// Enter the subscription ID here.
							PayUParameters::SUBSCRIPTION_ID => $subscription_id,
						);
						
						$response = PayUSubscriptions::cancel($parameters);
						
						if($response){
						
							delete_user_meta($userId, 'subscription_id');
							delete_user_meta($userId, 'payulatam_planid');
							delete_user_meta($userId, 'payulatam_customer_id');
							
						}
						} catch (Exception $e) {
							$error = array(
									'status' => 'error',
									'err_message' => $e->getMessage()
									);
							echo json_encode($error);
							exit;
						}
					
					
						}else{
						
						$subID = get_user_meta($userId, 'subscription_id',true);
						$cusID = get_user_meta($userId, 'stripe_customer_id',true);
						
						$currentcustomer = \Stripe\Customer::retrieve($cusID);
						
						$res = $currentcustomer->subscriptions->retrieve($subID)->cancel();
						
						if($res->status != 'canceled'){
							$error = array(
									'status' => 'error',
									'err_message' => esc_html__('Previous subscription not canceled.', 'service-finder')
									);
							echo json_encode($error);
							exit;
						}
						
						}
						
						delete_user_meta($userId, 'subscription_id');
						delete_user_meta($userId, 'recurring_profile_id');
						delete_user_meta($userId, 'recurring_profile_amt');
						delete_user_meta($userId, 'recurring_profile_period');
						delete_user_meta($userId, 'recurring_profile_desc_full'); 
						delete_user_meta($userId, 'recurring_profile_desc'); 
						delete_user_meta($userId, 'recurring_profile_type');
						delete_user_meta($userId, 'paypal_token');
						delete_user_meta($userId, 'reg_paypal_role');
						}
						
						update_user_meta( $userId, 'provider_activation_time', array( 'role' => $role, 'time' => time()) );
						
						update_user_meta($userId, 'txn_id', $charge->balance_transaction);
						update_user_meta($userId, 'expire_limit', $expire_limit);
						update_user_meta($userId, 'stripe_token', $token);
						update_user_meta($userId, 'stripe_customer_id', $customer->id);
						update_user_meta( $userId, 'provider_role', $role );
						update_user_meta($userId, 'profile_amt',$rolePrice);
						update_user_meta( $userId, 'pay_type', 'single' );
						$roleNum = intval(substr($role, 8));
						$roleName = $service_finder_options['package'.$roleNum.'-name'];
						update_user_meta( $userId, 'payment_mode', $payment_mode );
						
						$paymode = $payment_mode;
						$userInfo = service_finder_getUserInfo($userId);
						$args = array(
								'username' => (!empty($userdata->user_login)) ? $userdata->user_login : '',
								'email' => (!empty($userdata->user_email)) ? $userdata->user_email : '',
								'package_name' => $roleName,
								'payment_type' => $paymode
								);
						service_finder_update_job_limit($userId);
						
						service_finder_after_claimedpayment_user($profileid,$claimedbusinessid);
						service_finder_after_claimedpayment_admin($args,$claimedbusinessid);
						
						$registerMessages = (!empty($service_finder_options['claimed-business-successfull'])) ? $service_finder_options['claimed-business-successfull'] : esc_html__('Your payment for this claimed business successfully.', 'service-finder');
						
						$pageid = (!empty($service_finder_options['claimed-redirect-option'])) ? $service_finder_options['claimed-redirect-option'] : '';
						if($pageid == 'no'){
						$redirectURL = '';
						}else{
						$redirectURL = get_permalink($pageid);
						}
						
						$success = array(
									'status' => 'success',
									'redirecturl' => $redirectURL,
									'suc_message' => $registerMessages,
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
				

			}
		
		} else {
			
			// free
			$user = new WP_User( $userId );
			$user->set_role('Provider');
			
			update_user_meta( $userId, 'provider_activation_time', array( 'role' => $role, 'time' => time()) );
			$roleNum = intval(substr($role, 8));
			$roleName = $service_finder_options['package'.$roleNum.'-name'];
			update_user_meta($userId, 'expire_limit', $expire_limit);
			update_user_meta( $userId, 'provider_role', $role );
			$userInfo = service_finder_getUserInfo($userId);
			$args = array(
					'username' => $userdata->user_login,
					'email' => $userdata->user_email,
					'package_name' => $roleName,
					'payment_type' => 'Free'
					);
			$registerMessages = (!empty($service_finder_options['claimed-business-successfull'])) ? $service_finder_options['claimed-business-successfull'] : esc_html__('Your payment for this claimed business successfully.', 'service-finder');
						
			$pageid = (!empty($service_finder_options['claimed-redirect-option'])) ? $service_finder_options['claimed-redirect-option'] : '';
			if($pageid == 'no'){
			$redirectURL = '';
			}else{
			$redirectURL = get_permalink($pageid);
			}
			
			$success = array(
						'status' => 'success',
						'redirecturl' => $redirectURL,
						'suc_message' => $registerMessages,
						);
			echo json_encode($success);	
			

		}
	}
}
	
	
exit;
}

/* Provider Signup via ajax with payulatam */
add_action('wp_ajax_payulatam_claimed', 'service_finder_payulatam_claimed');
add_action('wp_ajax_nopriv_payulatam_claimed', 'service_finder_payulatam_claimed');
function service_finder_payulatam_claimed(){
global $wpdb, $service_finder_Errors, $service_finder_options, $paypal;
require_once(SERVICE_FINDER_PAYMENT_GATEWAY_DIR.'/stripe/init.php');

$payment_mode = (isset($_POST['claim_payment_mode'])) ? esc_html($_POST['claim_payment_mode']) : '';
$freemode = (isset($_POST['freemode'])) ? esc_html($_POST['freemode']) : '';
$claimedprofile = (isset($_POST['claimedprofile'])) ? esc_html($_POST['claimedprofile']) : '';
$claimedbusinessid = (isset($_POST['claimedbusinessid'])) ? esc_html($_POST['claimedbusinessid']) : '';
$profileid = (isset($_POST['profileid'])) ? esc_html($_POST['profileid']) : '';

$service_finder_options = get_option('service_finder_options');

$creds = array();
$paypalCreds['USER'] = (isset($service_finder_options['paypal-username'])) ? $service_finder_options['paypal-username'] : '';
$paypalCreds['PWD'] = (isset($service_finder_options['paypal-password'])) ? $service_finder_options['paypal-password'] : '';
$paypalCreds['SIGNATURE'] = (isset($service_finder_options['paypal-signatue'])) ? $service_finder_options['paypal-signatue'] : '';
$paypalType = (isset($service_finder_options['paypal-type']) && $service_finder_options['paypal-type'] == 'live') ? '' : 'sandbox.';

$paypalTypeBool = (!empty($paypalType)) ? true : false;

$paypal = new Paypal($paypalCreds,$paypalTypeBool);

	
$upgrade = true;
$currentRole =  get_user_meta($profileid,'provider_role',true);
$currentPayType = get_user_meta($profileid,'pay_type',true);
if($currentPayType == 'single'){
	$paidAmount =  get_user_meta($profileid,'profile_amt',true);
}
$userId = $profileid;
	
$signup_user_role = (isset($_POST['signup_user_role'])) ? esc_html($_POST['signup_user_role']) : '';

$roleNum = 1;
$rolePrice = '0';
$free = true;
$price = '0';
$packageName = '';
$get_provider_role = (isset($_POST['provider-role'])) ? esc_html($_POST['provider-role']) : '';
if(isset($get_provider_role)){
	$userdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$wpdb->users.' WHERE `ID` = %d',$userId));
	$userInfo = service_finder_getUserInfo($userId);
	// set role
	
	$role = $get_provider_role;
	if (($role == "package_1") || ($role == "package_2") || ($role == "package_3")){
		$roleNum = intval(substr($role, 8));
		switch ($role) {
			case "package_1":
				if(isset($service_finder_options['package1-price']) && trim($service_finder_options['package1-price']) !== '0') {
					$token = $_POST['stripeToken'];
					$rolePrice = $service_finder_options['package1-price'];
					$free = false;
					$packageName = $service_finder_options['package1-name'];
					if($service_finder_options['payment-type'] == 'single'){
					$expire_limit = $service_finder_options['package1-expday'];
					}
					$price = trim($service_finder_options['package1-price']);								
				}
				break;
			case "package_2":
				if(isset($service_finder_options['package2-price']) && trim($service_finder_options['package2-price']) !== '0') {
					$token = $_POST['stripeToken'];
					if($service_finder_options['payment-type'] == 'single'){
					$expire_limit = $service_finder_options['package2-expday'];
					}
					$rolePrice = $service_finder_options['package2-price'];
					$free = false;
					$packageName = $service_finder_options['package2-name'];
					$price = trim($service_finder_options['package2-price']);								
				}
				break;
			case "package_3":
				if(isset($service_finder_options['package3-price']) && trim($service_finder_options['package3-price']) !== '0') {
					$token = $_POST['stripeToken'];
					if($service_finder_options['payment-type'] == 'single'){
					$expire_limit = $service_finder_options['package3-expday'];
					}
					$rolePrice = $service_finder_options['package3-price'];
					$free = false;
					$packageName = $service_finder_options['package3-name'];
					$price = trim($service_finder_options['package3-price']);								
				}
				break;
			default:
				break;
		}
		// non free
		if( !$free ){
		
			$cardtype = (isset($_POST['payulatam_signup_cardtype'])) ? esc_html($_POST['payulatam_signup_cardtype']) : '';
			$card_number = (isset($_POST['payulatam_cd_number'])) ? esc_html($_POST['payulatam_cd_number']) : '';
			$card_cvc = (isset($_POST['payulatam_cd_cvc'])) ? esc_html($_POST['payulatam_cd_cvc']) : '';
			$card_month = (isset($_POST['payulatam_cd_month'])) ? esc_html($_POST['payulatam_cd_month']) : '';
			$card_year = (isset($_POST['payulatam_cd_year'])) ? esc_html($_POST['payulatam_cd_year']) : '';

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
				
				$fullname = $userdata->user_login;
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
				
			$paymentName = (isset($service_finder_options['payulatam-payment-name'])) ? $service_finder_options['payulatam-payment-name'] : esc_html__('Payment via PayU Latam','service-finder');
			$paymentDescription = esc_html__('Upgrade to ','service-finder') . $packageName;

			$paymentName .= esc_html__(' Upgrade','service-finder');
			$reference = 'upgrade_'.$userId.'_'.time();
			
			$value = $price;

			if (isset($service_finder_options['payment-type']) && ($service_finder_options['payment-type'] == 'recurring')) {
				
				$billingPeriod = esc_html__('year','service-finder');
				switch ($service_finder_options['package'.$roleNum.'-billing-period']) {
					case 'Year':
						$billingPeriod = esc_html__('YEAR','service-finder');
						break;
					case 'Month':
						$billingPeriod = esc_html__('MONTH','service-finder');
						break;
					case 'Week':
						$billingPeriod = esc_html__('WEEK','service-finder');
						break;
					case 'Day':
						$billingPeriod = esc_html__('DAY','service-finder');
						break;
				}
				$recurringDescription = $rolePrice.' '.$currencyCode.' '.esc_html__('per','service-finder').' '.$billingPeriod;
				$recurringDescriptionFull = $rolePrice.' '.$currencyCode.' '.esc_html__('per','service-finder').' '.$billingPeriod.' '.esc_html__('for','service-finder').' '.$packageName;
				
				
	// recurring payment setup will go here
	$phone = (!empty($userInfo['phone'])) ? $userInfo['phone'] : '';
	$plan = $role;		
	try {
				$parameters = array(
					
					//Enter the number of installments here.
					PayUParameters::INSTALLMENTS_NUMBER => "1",
				
					// -- Client parameters --
					// Enter the costumer name here. 
					PayUParameters::CUSTOMER_NAME => $fullname,
					// Enter the costumer email here. 
					PayUParameters::CUSTOMER_EMAIL => $userdata->user_email,
				
					//-- Credit card parameters --
					// Enter the payer's name here.
					PayUParameters::PAYER_NAME => $fullname,
					// Enter the number of the credit card here
					PayUParameters::CREDIT_CARD_NUMBER => $card_number,
					// Enter expiration date of the credit card here
					PayUParameters::CREDIT_CARD_EXPIRATION_DATE => $card_year.'/'.$card_month,
					// "MASTERCARD" || "AMEX" || "ARGENCARD" || "CABAL" || "NARANJA" || "CENCOSUD" || "SHOPPING"
					PayUParameters::PAYMENT_METHOD => $cardtype, 
					
					// (OPTIONAL) Enter the contact phone here. 
					PayUParameters::PAYER_PHONE => $phone,
				
					// -- Plan parameters --
					// Enter the identification code of the plan here.
					PayUParameters::PLAN_CODE => $plan,
					
					);
				
				
				if($currentPayType == 'recurring'){
					// Cancel old profile
					$oldProfile = get_user_meta($userId,'recurring_profile_id',true);
					$merchantOrderId = get_user_meta($userId,'merchantOrderId',true);
					$orderNumber = get_user_meta($userId,'orderNumber',true);
					$payulatam_planid = get_user_meta($userId,'payulatam_planid',true);
					$subscription_id = get_user_meta($userId,'subscription_id',true);
					
					if (!empty($oldProfile)) {
						$cancelParams = array(
							'PROFILEID' => $oldProfile,
							'ACTION' => 'Cancel'
						);
						$res = $paypal -> request('ManageRecurringPaymentsProfileStatus',$cancelParams);
						
						if($res['ACK'] != 'Success'){
							$error = array(
									'status' => 'error',
									'err_message' => $res['L_SHORTMESSAGE0']
									);
							echo json_encode($error);
							exit;
						}
						
						$cu = \Stripe\Customer::retrieve($customer->id);
						$getsubs = $cu->subscriptions->create(array("plan" => $plan));
						update_user_meta($userId, 'subscription_id',$getsubs->id);
						
						delete_user_meta($userId, 'recurring_profile_id');
						delete_user_meta($userId, 'recurring_profile_amt');
						delete_user_meta($userId, 'recurring_profile_period');
						delete_user_meta($userId, 'recurring_profile_desc_full'); 
						delete_user_meta($userId, 'recurring_profile_desc'); 
						delete_user_meta($userId, 'recurring_profile_type');
						delete_user_meta($userId, 'paypal_token');
						delete_user_meta($userId, 'reg_paypal_role');
					
					}elseif($payulatam_planid != "" && $subscription_id != ""){
					try {
					$parameters = array(
						// Enter the subscription ID here.
						PayUParameters::SUBSCRIPTION_ID => $subscription_id,
					);
					
					$response = PayUSubscriptions::cancel($parameters);
					
					if($response){
					
						delete_user_meta($userId, 'subscription_id');
						delete_user_meta($userId, 'payulatam_planid');
						delete_user_meta($userId, 'payulatam_customer_id');
						
						delete_user_meta($userId, 'recurring_profile_id');
						delete_user_meta($userId, 'recurring_profile_amt');
						delete_user_meta($userId, 'recurring_profile_period');
						delete_user_meta($userId, 'recurring_profile_desc_full'); 
						delete_user_meta($userId, 'recurring_profile_desc'); 
						delete_user_meta($userId, 'recurring_profile_type');
						delete_user_meta($userId, 'paypal_token');
						delete_user_meta($userId, 'reg_paypal_role');
					
					}
					} catch (Exception $e) {
						$error = array(
								'status' => 'error',
								'err_message' => $e->getMessage()
								);
						echo json_encode($error);
						exit;
					}
					
					}elseif($merchantOrderId != "" && $orderNumber != ""){
						require_once(SERVICE_FINDER_PAYMENT_GATEWAY_DIR.'/2checkout/lib/Twocheckout.php');
						$twocheckout_api_username = $service_finder_options['twocheckout-api-username'];
						$twocheckout_api_password = $service_finder_options['twocheckout-api-password'];
						
						Twocheckout::username($twocheckout_api_username);
						Twocheckout::password($twocheckout_api_password);
						
						if($twocheckouttype == 'test'){
							Twocheckout::verifySSL(false);
							Twocheckout::sandbox(true);
						}
						
						$args = array(
							'sale_id' => $orderNumber
						);
						try {
							$result = Twocheckout_Sale::stop($args);
							
							$cu = \Stripe\Customer::retrieve($customer->id);
							$getsubs = $cu->subscriptions->create(array("plan" => $plan));
							update_user_meta($userId, 'subscription_id',$getsubs->id);

							delete_user_meta($userId, 'merchantOrderId');
							delete_user_meta($userId, 'orderNumber');

						} catch (Twocheckout_Error $e) {
							$e->getMessage();
							$error = array(
									'status' => 'error',
									'err_message' => sprintf( esc_html__('%s', 'service-finder'), $e->getMessage() )
									);
							echo json_encode($error);
							exit;
						}
					}else{
					
					
					$subID = get_user_meta($userId, 'subscription_id',true);
					$cusID = get_user_meta($userId, 'stripe_customer_id',true);
					
					$currentcustomer = \Stripe\Customer::retrieve($cusID);
					
					$res = $currentcustomer->subscriptions->retrieve($subID)->cancel();
					
					if($res->status != 'canceled'){
						$error = array(
								'status' => 'error',
								'err_message' => esc_html__('Previous subscription not canceled.', 'service-finder')
								);
						echo json_encode($error);
						exit;
					}
					
					delete_user_meta($userId, 'subscription_id');
					delete_user_meta($userId, 'recurring_profile_id');
					delete_user_meta($userId, 'recurring_profile_amt');
					delete_user_meta($userId, 'recurring_profile_period');
					delete_user_meta($userId, 'recurring_profile_desc_full'); 
					delete_user_meta($userId, 'recurring_profile_desc'); 
					delete_user_meta($userId, 'recurring_profile_type');
					delete_user_meta($userId, 'paypal_token');
					delete_user_meta($userId, 'reg_paypal_role');
					
					}
					}else{
							
					$response = PayUSubscriptions::createSubscription($parameters);

					if($response){
						$subscriptionid = $response->id;   
						$planid = $response->plan->id;
						$customerid = $response->customer->id;   
						
						update_user_meta($userId, 'subscription_id',$subscriptionid);
						update_user_meta($userId, 'payulatam_planid',$planid);
						update_user_meta($userId, 'payulatam_customer_id', $customerid);
							
					}	
				} 
				
				$user = new WP_User( $userId );
				$user->set_role('Provider');
				
				
				if($currentPayType == 'single'){
				delete_user_meta($userId, 'expire_limit');
				delete_user_meta($userId, 'profile_amt');
				} 
				
				update_user_meta( $userId, 'provider_activation_time', array( 'role' => $role, 'time' => time()) );
				update_user_meta( $userId, 'provider_role', $role );
				update_user_meta( $userId, 'pay_type', 'recurring' );
				
				update_user_meta( $userId, 'payment_mode', $payment_mode );
				
				$type = 'upgrade';
				update_user_meta($userId, 'recurring_profile_type',$type);
				$roleNum = intval(substr($role, 8));
				$roleName = $service_finder_options['package'.$roleNum.'-name'];
				
				update_user_meta($userId, 'recurring_profile_amt',$rolePrice);
				update_user_meta($userId, 'recurring_profile_period',$service_finder_options['package'.$roleNum.'-billing-period']);
				update_user_meta($userId, 'recurring_profile_desc_full',$recurringDescriptionFull); 
				update_user_meta($userId, 'recurring_profile_desc',$recurringDescription); 
				$paymode = $payment_mode;
				$userInfo = service_finder_getUserInfo($userId);
				$args = array(
						'username' => $userdata->user_login,
						'email' => $userdata->user_email,
						'package_name' => $roleName,
						'payment_type' => $paymode
						);
				
				service_finder_update_job_limit($userId);
				
				service_finder_after_claimedpayment_user($profileid,$claimedbusinessid);
				service_finder_after_claimedpayment_admin($args,$claimedbusinessid);
					
				$registerMessages = (!empty($service_finder_options['claimed-business-successfull'])) ? $service_finder_options['claimed-business-successfull'] : esc_html__('Your payment for this claimed business successfully.', 'service-finder');
				
				$pageid = (!empty($service_finder_options['claimed-redirect-option'])) ? $service_finder_options['signup-redirect-option'] : '';
				if($pageid == 'no'){
				$redirectURL = '';
				}else{
				$redirectURL = get_permalink($pageid);
				}

				$success = array(
							'status' => 'success',
							'redirecturl' => $redirectURL,
							'suc_message' => $registerMessages,
							);
				echo json_encode($success);	

				} catch (Exception $e) {
					$error = array(
							'status' => 'error',
							'err_message' => $e->getMessage()
							);
					echo json_encode($error);
					
				}
			} else {
			
				$phone = (!empty($userInfo['phone'])) ? $userInfo['phone'] : '';
				
				try {
				$parameters = array(
					//Enter the account’s identifier here
					PayUParameters::ACCOUNT_ID => $payulatamaccountid,
					// Enter the reference code here.
					PayUParameters::REFERENCE_CODE => $reference,
					// Enter the description here.
					PayUParameters::DESCRIPTION => $paymentDescription,
					
					// -- Values --
					// Enter the value here.       
					PayUParameters::VALUE => $value,
					// Enter the currency here.
					PayUParameters::CURRENCY => $currencyCode,
					
				
					// -- Payer --
				   ///Enter the payer's name here
					PayUParameters::PAYER_NAME => $fullname,//"APPROVED"
					//Enter the payer's email here
					PayUParameters::PAYER_EMAIL => $userdata->user_email,
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
					
						// set role
						$user = new WP_User( $userId );
						$user->set_role('Provider');
						
						if($currentPayType == 'recurring'){
						
						// Cancel old profile
						$oldProfile = get_user_meta($userId,'recurring_profile_id',true);
						$payulatam_planid = get_user_meta($userId,'payulatam_planid',true);
						$subscription_id = get_user_meta($userId,'subscription_id',true);
						
						
						if (!empty($oldProfile)) {
							$cancelParams = array(
								'PROFILEID' => $oldProfile,
								'ACTION' => 'Cancel'
							);
							$res = $paypal -> request('ManageRecurringPaymentsProfileStatus',$cancelParams);
							
							if($res['ACK'] != 'Success'){
								$error = array(
										'status' => 'error',
										'err_message' => $res['L_SHORTMESSAGE0']
										);
								echo json_encode($error);
								exit;
							}
						}elseif($payulatam_planid != "" && $subscription_id != ""){
						
						try {
						$parameters = array(
							// Enter the subscription ID here.
							PayUParameters::SUBSCRIPTION_ID => $subscription_id,
						);
						
						$response = PayUSubscriptions::cancel($parameters);
						
						if($response){
						
							delete_user_meta($userId, 'subscription_id');
							delete_user_meta($userId, 'payulatam_planid');
							delete_user_meta($userId, 'payulatam_customer_id');
							
						}
						} catch (Exception $e) {
							$error = array(
									'status' => 'error',
									'err_message' => $e->getMessage()
									);
							echo json_encode($error);
							exit;
						}
					
					
						}else{
						
						$subID = get_user_meta($userId, 'subscription_id',true);
						$cusID = get_user_meta($userId, 'stripe_customer_id',true);
						
						$currentcustomer = \Stripe\Customer::retrieve($cusID);
						
						$res = $currentcustomer->subscriptions->retrieve($subID)->cancel();
						
						if($res->status != 'canceled'){
							$error = array(
									'status' => 'error',
									'err_message' => esc_html__('Previous subscription not canceled.', 'service-finder')
									);
							echo json_encode($error);
							exit;
						}
						
						}
						
						delete_user_meta($userId, 'subscription_id');
						delete_user_meta($userId, 'recurring_profile_id');
						delete_user_meta($userId, 'recurring_profile_amt');
						delete_user_meta($userId, 'recurring_profile_period');
						delete_user_meta($userId, 'recurring_profile_desc_full'); 
						delete_user_meta($userId, 'recurring_profile_desc'); 
						delete_user_meta($userId, 'recurring_profile_type');
						delete_user_meta($userId, 'paypal_token');
						delete_user_meta($userId, 'reg_paypal_role');
						}
						
						update_user_meta( $userId, 'provider_activation_time', array( 'role' => $role, 'time' => time()) );
						
						update_user_meta($userId, 'txn_id', $txnid);
						update_user_meta($userId, 'expire_limit', $expire_limit);
						update_user_meta( $userId, 'provider_role', $role );
						update_user_meta($userId, 'profile_amt',$rolePrice);
						update_user_meta( $userId, 'pay_type', 'single' );
						$roleNum = intval(substr($role, 8));
						$roleName = $service_finder_options['package'.$roleNum.'-name'];
						update_user_meta( $userId, 'payment_mode', $payment_mode );
						
						$paymode = $payment_mode;
						$userInfo = service_finder_getUserInfo($userId);
						$args = array(
								'username' => (!empty($userdata->user_login)) ? $userdata->user_login : '',
								'email' => (!empty($userdata->user_email)) ? $userdata->user_email : '',
								'package_name' => $roleName,
								'payment_type' => $paymode
								);
						
						service_finder_update_job_limit($userId);
						
						service_finder_after_claimedpayment_user($profileid,$claimedbusinessid);
						service_finder_after_claimedpayment_admin($args,$claimedbusinessid);
					
						$registerMessages = (!empty($service_finder_options['claimed-business-successfull'])) ? $service_finder_options['claimed-business-successfull'] : esc_html__('Your payment for this claimed business successfully.', 'service-finder');
						
						$pageid = (!empty($service_finder_options['signup-redirect-option'])) ? $service_finder_options['signup-redirect-option'] : '';
						if($pageid == 'no'){
						$redirectURL = '';
						}else{
						$redirectURL = get_permalink($pageid);
						}
						
						$success = array(
									'status' => 'success',
									'redirecturl' => $redirectURL,
									'suc_message' => $registerMessages,
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
				
			}
		
		}
	}
}

	
exit;
}


/* Provider Signup via ajax with twocheckout */
add_action('wp_ajax_twocheckout_claimed', 'service_finder_twocheckout_claimed');
add_action('wp_ajax_nopriv_twocheckout_claimed', 'service_finder_twocheckout_claimed');

function service_finder_twocheckout_claimed(){
global $wpdb, $service_finder_Errors, $service_finder_options, $paypal;
require_once(SERVICE_FINDER_PAYMENT_GATEWAY_DIR.'/stripe/init.php');
require_once(SERVICE_FINDER_PAYMENT_GATEWAY_DIR.'/2checkout/lib/Twocheckout.php');

$payment_mode = (isset($_POST['claim_payment_mode'])) ? esc_html($_POST['claim_payment_mode']) : '';
$freemode = (isset($_POST['freemode'])) ? esc_html($_POST['freemode']) : '';
$claimedprofile = (isset($_POST['claimedprofile'])) ? esc_html($_POST['claimedprofile']) : '';
$claimedbusinessid = (isset($_POST['claimedbusinessid'])) ? esc_html($_POST['claimedbusinessid']) : '';
$profileid = (isset($_POST['profileid'])) ? esc_html($_POST['profileid']) : '';

$token = (!empty($_POST['twocheckouttoken'])) ? esc_html($_POST['twocheckouttoken']) : '';

$service_finder_options = get_option('service_finder_options');

$creds = array();
$paypalCreds['USER'] = (isset($service_finder_options['paypal-username'])) ? $service_finder_options['paypal-username'] : '';
$paypalCreds['PWD'] = (isset($service_finder_options['paypal-password'])) ? $service_finder_options['paypal-password'] : '';
$paypalCreds['SIGNATURE'] = (isset($service_finder_options['paypal-signatue'])) ? $service_finder_options['paypal-signatue'] : '';
$paypalType = (isset($service_finder_options['paypal-type']) && $service_finder_options['paypal-type'] == 'live') ? '' : 'sandbox.';

$paypalTypeBool = (!empty($paypalType)) ? true : false;

$paypal = new Paypal($paypalCreds,$paypalTypeBool);

$currentRole =  get_user_meta($profileid,'provider_role',true);
$currentPayType = get_user_meta($profileid,'pay_type',true);
if($currentPayType == 'single'){
	$paidAmount =  get_user_meta($profileid,'profile_amt',true);
}
$userId = $profileid;

$signup_user_role = (isset($_POST['signup_user_role'])) ? $_POST['signup_user_role'] : '';

$roleNum = 1;
$rolePrice = '0';
$free = true;
$price = '0';
$packageName = '';
$get_provider_role = (isset($_POST['provider-role'])) ? $_POST['provider-role'] : '';
if(isset($get_provider_role)){
	$userdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$wpdb->users.' WHERE `ID` = %d',$userId));
	$userInfo = service_finder_getUserInfo($userId);
	// set role

	$role = $get_provider_role;
	if (($role == "package_1") || ($role == "package_2") || ($role == "package_3")){
		$roleNum = intval(substr($role, 8));
		switch ($role) {
			case "package_1":
				if(isset($service_finder_options['package1-price']) && trim($service_finder_options['package1-price']) !== '0') {
					$rolePrice = $service_finder_options['package1-price'];
					$free = false;
					$packageName = $service_finder_options['package1-name'];
					if($service_finder_options['payment-type'] == 'single'){
					$expire_limit = $service_finder_options['package1-expday'];
					}
					$price = trim($service_finder_options['package1-price']);								
				}
				break;
			case "package_2":
				if(isset($service_finder_options['package2-price']) && trim($service_finder_options['package2-price']) !== '0') {
					if($service_finder_options['payment-type'] == 'single'){
					$expire_limit = $service_finder_options['package2-expday'];
					}
					$rolePrice = $service_finder_options['package2-price'];
					$free = false;
					$packageName = $service_finder_options['package2-name'];
					$price = trim($service_finder_options['package2-price']);								
				}
				break;
			case "package_3":
				if(isset($service_finder_options['package3-price']) && trim($service_finder_options['package3-price']) !== '0') {
					if($service_finder_options['payment-type'] == 'single'){
					$expire_limit = $service_finder_options['package3-expday'];
					}
					$rolePrice = $service_finder_options['package3-price'];
					$free = false;
					$packageName = $service_finder_options['package3-name'];
					$price = trim($service_finder_options['package3-price']);								
				}
				break;
			default:
				break;
		}
		// non free
		if( !$free ){

		$currencyCode = service_finder_currencycode();

		$twocheckouttype = (!empty($service_finder_options['twocheckout-type'])) ? esc_html($service_finder_options['twocheckout-type']) : '';

		if($twocheckouttype == 'live'){
			$private_key = (!empty($service_finder_options['twocheckout-live-private-key'])) ? esc_html($service_finder_options['twocheckout-live-private-key']) : '';
			$twocheckoutaccountid = (!empty($service_finder_options['twocheckout-live-account-id'])) ? esc_html($service_finder_options['twocheckout-live-account-id']) : '';
		}else{
			$private_key = (!empty($service_finder_options['twocheckout-test-private-key'])) ? esc_html($service_finder_options['twocheckout-test-private-key']) : '';
			$twocheckoutaccountid = (!empty($service_finder_options['twocheckout-test-account-id'])) ? esc_html($service_finder_options['twocheckout-test-account-id']) : '';
		}
			
			$paymentName = esc_html__('Payment via 2Checkout','service-finder');
			$paymentDescription = ($upgrade) ? esc_html__('Upgrade to ','service-finder') . $packageName : $packageName;

			if($upgrade){
				$paymentName .= esc_html__(' Upgrade','service-finder');
			}

			if (isset($service_finder_options['payment-type']) && ($service_finder_options['payment-type'] == 'recurring')) {
				
				$billingPeriod = esc_html__('year','service-finder');
				switch ($service_finder_options['package'.$roleNum.'-billing-period']) {
					case 'Year':
						$billingPeriod = esc_html__('1 Year','service-finder');
						break;
					case 'Month':
						$billingPeriod = esc_html__('1 Month','service-finder');
						break;
					case 'Week':
						$billingPeriod = esc_html__('1 Week','service-finder');
						break;
					case 'Day':
						$billingPeriod = esc_html__('1 Day','service-finder');
						break;
				}
				$recurringDescription = $rolePrice.' '.$currencyCode.' '.esc_html__('per','service-finder').' '.$billingPeriod;
				$recurringDescriptionFull = $rolePrice.' '.$currencyCode.' '.esc_html__('per','service-finder').' '.$billingPeriod.' '.esc_html__('for','service-finder').' '.$packageName;
				
				Twocheckout::privateKey($private_key);
				Twocheckout::sellerId($twocheckoutaccountid);
				
				if($twocheckouttype == 'test'){
				Twocheckout::verifySSL(false);
				Twocheckout::sandbox(true);
				}

				$signup_address = $userInfo['address'];
				$signup_city = $userInfo['city'];
				$signup_first_name = $userInfo['fname'];
				$signup_last_name = $userInfo['lname'];
				$signup_state = $userInfo['state'];
				$signup_country = $userInfo['country'];
				$signup_zipcode = $userInfo['zipcode'];
				$signup_user_email = $userInfo['email'];
				$signup_user_phone = $userInfo['phone'];
				
				try {
				
				$carduserinfo = service_finder_getUserInfo($userId);

				$charge = Twocheckout_Charge::auth(array(
					"sellerId" => $twocheckoutaccountid,
					"privateKey" => $private_key,
					"merchantOrderId" => time(),
					"token" => $token,
					"currency" => strtoupper(service_finder_currencycode()),
					"lineItems" => array(
						array(
							"type"        => 'registration',
							"price"       => $rolePrice,
							"productId"   => $userId,
							"name"        => "Registration",
							"quantity"    => "1",
							"tangible"    => "N",
							"recurrence"  => $billingPeriod,
							"description" => $recurringDescriptionFull
						)
					),
					"billingAddr" => array(
						"name" => $signup_first_name.' '.$signup_last_name,
						"addrLine1" => $signup_address,
						"city" => $signup_city,
						"state" => $signup_state,
						"zipCode" => $signup_zipcode,
						"country" => $signup_country,
						"email" => $signup_user_email,
						"phoneNumber" => $signup_user_phone
					)
					), 'array');
					
				if ($charge['response']['responseCode'] == 'APPROVED') {
					$transactionid = $charge['response']['transactionId'];
					$merchantOrderId = $charge['response']['merchantOrderId'];
					$orderNumber = $charge['response']['orderNumber'];
					
					$user = new WP_User( $userId );
					$user->set_role('Provider');
			
			
					if($currentPayType == 'recurring'){
			
						// Cancel old profile
						$oldProfile = get_user_meta($userId,'recurring_profile_id',true);
						$merchantOrderId = get_user_meta($userId,'merchantOrderId',true);
						$orderNumber = get_user_meta($userId,'orderNumber',true);
						$payulatam_planid = get_user_meta($userId,'payulatam_planid',true);
						$subscription_id = get_user_meta($userId,'subscription_id',true);
						if (!empty($oldProfile)) {
							$cancelParams = array(
								'PROFILEID' => $oldProfile,
								'ACTION' => 'Cancel'
							);
							$res = $paypal -> request('ManageRecurringPaymentsProfileStatus',$cancelParams);
							
							if($res['ACK'] != 'Success'){
								$error = array(
										'status' => 'error',
										'err_message' => $res['L_SHORTMESSAGE0']
										);
								echo json_encode($error);
								exit;
							}
							
							
							$cu = \Stripe\Customer::retrieve($customer->id);
							$getsubs = $cu->subscriptions->create(array("plan" => $plan));
							update_user_meta($userId, 'subscription_id',$getsubs->id);
							
							delete_user_meta($userId, 'subscription_id');
							delete_user_meta($userId, 'recurring_profile_id');
							delete_user_meta($userId, 'recurring_profile_amt');
							delete_user_meta($userId, 'recurring_profile_period');
							delete_user_meta($userId, 'recurring_profile_desc_full'); 
							delete_user_meta($userId, 'recurring_profile_desc'); 
							delete_user_meta($userId, 'recurring_profile_type');
							delete_user_meta($userId, 'paypal_token');
							delete_user_meta($userId, 'reg_paypal_role');
						}elseif($payulatam_planid != "" && $subscription_id != ""){
							
							try {
							$parameters = array(
								// Enter the subscription ID here.
								PayUParameters::SUBSCRIPTION_ID => $subscription_id,
							);
							
							$response = PayUSubscriptions::cancel($parameters);
							
							if($response){
							
								delete_user_meta($userId, 'subscription_id');
								delete_user_meta($userId, 'payulatam_planid');
								delete_user_meta($userId, 'payulatam_customer_id');
								
								delete_user_meta($userId, 'recurring_profile_id');
								delete_user_meta($userId, 'recurring_profile_amt');
								delete_user_meta($userId, 'recurring_profile_period');
								delete_user_meta($userId, 'recurring_profile_desc_full'); 
								delete_user_meta($userId, 'recurring_profile_desc'); 
								delete_user_meta($userId, 'recurring_profile_type');
								delete_user_meta($userId, 'paypal_token');
								delete_user_meta($userId, 'reg_paypal_role');
								
							}
							} catch (Exception $e) {
								$error = array(
										'status' => 'error',
										'err_message' => $e->getMessage()
										);
								echo json_encode($error);
								exit;
							}
						
						
							}elseif($merchantOrderId != "" && $orderNumber != ""){
							$twocheckout_api_username = $service_finder_options['twocheckout-api-username'];
							$twocheckout_api_password = $service_finder_options['twocheckout-api-password'];
							
							Twocheckout::username($twocheckout_api_username);
							Twocheckout::password($twocheckout_api_password);
							
							if($twocheckouttype == 'test'){
								Twocheckout::verifySSL(false);
								Twocheckout::sandbox(true);
							}
							
							$args = array(
								'sale_id' => $orderNumber
							);
							try {
								$result = Twocheckout_Sale::stop($args);
							} catch (Twocheckout_Error $e) {
								$e->getMessage();
								$error = array(
										'status' => 'error',
										'err_message' => sprintf( esc_html__('%s', 'service-finder'), $e->getMessage() )
										);
								echo json_encode($error);
								exit;
							}
							
						}else{
						
						$subID = get_user_meta($userId, 'subscription_id',true);
						$cusID = get_user_meta($userId, 'stripe_customer_id',true);
						
						$currentcustomer = \Stripe\Customer::retrieve($cusID);
						
						$res = $currentcustomer->subscriptions->retrieve($subID)->cancel();
						
						if($res->status != 'canceled'){
							$error = array(
									'status' => 'error',
									'err_message' => esc_html__('Previous subscription not canceled.', 'service-finder')
									);
							echo json_encode($error);
							exit;
						}
						
						delete_user_meta($userId, 'subscription_id');
						delete_user_meta($userId, 'recurring_profile_id');
						delete_user_meta($userId, 'recurring_profile_amt');
						delete_user_meta($userId, 'recurring_profile_period');
						delete_user_meta($userId, 'recurring_profile_desc_full'); 
						delete_user_meta($userId, 'recurring_profile_desc'); 
						delete_user_meta($userId, 'recurring_profile_type');
						delete_user_meta($userId, 'paypal_token');
						delete_user_meta($userId, 'reg_paypal_role');
						}
			}
						
						if($currentPayType == 'single'){
						delete_user_meta($userId, 'expire_limit');
						delete_user_meta($userId, 'profile_amt');
						}
						
						
						
						update_user_meta( $userId, 'provider_activation_time', array( 'role' => $role, 'time' => time()) );
						update_user_meta( $userId, 'provider_role', $role );
						update_user_meta( $userId, 'pay_type', 'recurring' );
						
						update_user_meta( $userId, 'payment_mode', $payment_mode );
						
						$type = 'upgrade';
						update_user_meta($userId, 'recurring_profile_type',$type);
						$roleNum = intval(substr($role, 8));
						$roleName = $service_finder_options['package'.$roleNum.'-name'];
						
						update_user_meta($userId, 'txn_id', $transactionid);
						update_user_meta($userId, 'merchantOrderId', $merchantOrderId);
						update_user_meta($userId, 'orderNumber', $orderNumber);
						update_user_meta($userId, 'recurring_profile_amt',$rolePrice);
						update_user_meta($userId, 'recurring_profile_period',$service_finder_options['package'.$roleNum.'-billing-period']);
						update_user_meta($userId, 'recurring_profile_desc_full',$recurringDescriptionFull); 
						update_user_meta($userId, 'recurring_profile_desc',$recurringDescription); 
						$paymode = $payment_mode;
						$userInfo = service_finder_getUserInfo($userId);
						$args = array(
								'username' => $userdata->user_login,
								'email' => $userdata->user_email,
								'package_name' => $roleName,
								'payment_type' => $paymode
								);
						
						service_finder_update_job_limit($userId);
						
						service_finder_after_claimedpayment_user($profileid,$claimedbusinessid);
						service_finder_after_claimedpayment_admin($args,$claimedbusinessid);
						
						$pageid = (!empty($service_finder_options['claimed-redirect-option'])) ? $service_finder_options['claimed-redirect-option'] : '';
						if($pageid == 'no'){
						$redirect = add_query_arg( array('claimed' => 'success'), service_finder_get_url_by_shortcode('[service_finder_success_message]') );
						}else{
						$redirect = add_query_arg( array('claimed' => 'success'), get_permalink($pageid) );
						}
						
						$success = array(
									'status' => 'success',
									'redirecturl' => $redirectURL,
									'suc_message' => $registerMessages,
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

			} else {
				
				Twocheckout::privateKey($private_key);
				Twocheckout::sellerId($twocheckoutaccountid);
				
				if($twocheckouttype == 'test'){
				Twocheckout::verifySSL(false);
				Twocheckout::sandbox(true);
				}

				$signup_address = $userInfo['address'];
				$signup_city = $userInfo['city'];
				$signup_first_name = $userInfo['fname'];
				$signup_last_name = $userInfo['lname'];
				$signup_state = $userInfo['state'];
				$signup_country = $userInfo['country'];
				$signup_zipcode = ($userInfo['zipcode']) ? $userInfo['zipcode'] : '302020';
				$signup_user_email = $userInfo['email'];
				$signup_user_phone = $userInfo['phone'];
				
				try {
				
				$charge = Twocheckout_Charge::auth(array(
					"sellerId" => $twocheckoutaccountid,
					"privateKey" => $private_key,
					"merchantOrderId" => time(),
					"token" => $token,
					"currency" => strtoupper(service_finder_currencycode()),
					"total" => $price,
					"tangible"    => "N",
					"billingAddr" => array(
						"name" => $signup_first_name.' '.$signup_last_name,
						"addrLine1" => $signup_address,
						"city" => $signup_city,
						"state" => $signup_state,
						"zipCode" => $signup_zipcode,
						"country" => $signup_country,
						"email" => $signup_user_email,
						"phoneNumber" => $signup_user_phone
					)
				));
				
				if ($charge['response']['responseCode'] == 'APPROVED') {
				
					$transactionid = $charge['response']['transactionId'];
						
					// set role
					$user = new WP_User( $userId );
					$user->set_role('Provider');
					
					if($currentPayType == 'recurring'){
					
					// Cancel old profile
					$oldProfile = get_user_meta($userId,'recurring_profile_id',true);
					$merchantOrderId = get_user_meta($userId,'merchantOrderId',true);
					$orderNumber = get_user_meta($userId,'orderNumber',true);
					$payulatam_planid = get_user_meta($userId,'payulatam_planid',true);
					$subscription_id = get_user_meta($userId,'subscription_id',true);
					if (!empty($oldProfile)) {
						$cancelParams = array(
							'PROFILEID' => $oldProfile,
							'ACTION' => 'Cancel'
						);
						$res = $paypal -> request('ManageRecurringPaymentsProfileStatus',$cancelParams);
						
						if($res['ACK'] != 'Success'){
							$error = array(
									'status' => 'error',
									'err_message' => $res['L_SHORTMESSAGE0']
									);
							echo json_encode($error);
							exit;
						}
					}elseif($payulatam_planid != "" && $subscription_id != ""){
							
							try {
							$parameters = array(
								// Enter the subscription ID here.
								PayUParameters::SUBSCRIPTION_ID => $subscription_id,
							);
							
							$response = PayUSubscriptions::cancel($parameters);
							
							if($response){
							
								delete_user_meta($userId, 'subscription_id');
								delete_user_meta($userId, 'payulatam_planid');
								delete_user_meta($userId, 'payulatam_customer_id');
								
							}
							} catch (Exception $e) {
								$error = array(
										'status' => 'error',
										'err_message' => $e->getMessage()
										);
								echo json_encode($error);
								exit;
							}
						
						
							}elseif($merchantOrderId != "" && $orderNumber != ""){
						
						$twocheckout_api_username = $service_finder_options['twocheckout-api-username'];
						$twocheckout_api_password = $service_finder_options['twocheckout-api-password'];
						
						Twocheckout::username($twocheckout_api_username);
						Twocheckout::password($twocheckout_api_password);
						
						if($twocheckouttype == 'test'){
							Twocheckout::verifySSL(false);
							Twocheckout::sandbox(true);
						}
						
						$args = array(
							'sale_id' => $orderNumber
						);
						try {
							$result = Twocheckout_Sale::stop($args);
						} catch (Twocheckout_Error $e) {
							$e->getMessage();
							$error = array(
									'status' => 'error',
									'err_message' => sprintf( esc_html__('%s', 'service-finder'), $e->getMessage() )
									);
							echo json_encode($error);
							exit;
						}
					}else{
					
					$subID = get_user_meta($userId, 'subscription_id',true);
					$cusID = get_user_meta($userId, 'stripe_customer_id',true);
					
					$currentcustomer = \Stripe\Customer::retrieve($cusID);
					
					$res = $currentcustomer->subscriptions->retrieve($subID)->cancel();
					
					if($res->status != 'canceled'){
						$error = array(
								'status' => 'error',
								'err_message' => esc_html__('Previous subscription not canceled.', 'service-finder')
								);
						echo json_encode($error);
						exit;
					}
					}
					
					delete_user_meta($userId, 'merchantOrderId');
					delete_user_meta($userId, 'orderNumber');
					delete_user_meta($userId, 'subscription_id');
					delete_user_meta($userId, 'recurring_profile_id');
					delete_user_meta($userId, 'recurring_profile_amt');
					delete_user_meta($userId, 'recurring_profile_period');
					delete_user_meta($userId, 'recurring_profile_desc_full'); 
					delete_user_meta($userId, 'recurring_profile_desc'); 
					delete_user_meta($userId, 'recurring_profile_type');
					delete_user_meta($userId, 'paypal_token');
					delete_user_meta($userId, 'reg_paypal_role');
					}
					
					update_user_meta( $userId, 'provider_activation_time', array( 'role' => $role, 'time' => time()) );
					
					update_user_meta($userId, 'txn_id', $transactionid);
					update_user_meta($userId, 'expire_limit', $expire_limit);
					update_user_meta($userId, 'provider_role', $role );
					update_user_meta($userId, 'profile_amt',$rolePrice);
					update_user_meta( $userId, 'pay_type', 'single' );
					$roleNum = intval(substr($role, 8));
					$roleName = $service_finder_options['package'.$roleNum.'-name'];
					update_user_meta( $userId, 'payment_mode', $payment_mode );
					
					$paymode = $payment_mode;
					$userInfo = service_finder_getUserInfo($userId);
					$args = array(
							'username' => (!empty($userdata->user_login)) ? $userdata->user_login : '',
							'email' => (!empty($userdata->user_email)) ? $userdata->user_email : '',
							'package_name' => $roleName,
							'payment_type' => $paymode
							);
					
					service_finder_update_job_limit($userId);
					
					service_finder_after_claimedpayment_user($profileid,$claimedbusinessid);
					service_finder_after_claimedpayment_admin($args,$claimedbusinessid);
					
					$registerMessages = (!empty($service_finder_options['claimed-business-successfull'])) ? $service_finder_options['claimed-business-successfull'] : esc_html__('Your payment for this claimed business successfully.', 'service-finder');
					
					$pageid = (!empty($service_finder_options['claimed-redirect-option'])) ? $service_finder_options['claimed-redirect-option'] : '';
					if($pageid == 'no'){
					$redirectURL = '';
					}else{
					$redirectURL = get_permalink($pageid);
					}
					
					$success = array(
								'status' => 'success',
								'redirecturl' => $redirectURL,
								'suc_message' => $registerMessages,
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
				
			}
		
		}
	}
}

	
exit;
}

/*After claimed payment mail to user via wire transfer*/
function service_finder_after_claimedpayment_user_via_wiretransfer($provider_id,$cid,$invoiceid){
global $wpdb, $service_finder_Tables, $service_finder_options;

$claiminfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->claim_business.' WHERE id = %d',$cid));

$subject = esc_html__('Payment for Claimed Business via Wired Transfer', 'service-finder');

$wiretransfermailinstructions = (!empty($service_finder_options['wire-transfer-mail-instructions'])) ? $service_finder_options['wire-transfer-mail-instructions'] : '';
if($wiretransfermailinstructions != ''){
	$message = $wiretransfermailinstructions;
}else{
	$message = 'Use following invoice ID When transfer amount in bank.';
}
$message .= 'Invoice ID:'.$invoiceid;

$userinfo = get_userdata($provider_id);
$username = $userinfo->user_login;
$password = wp_generate_password( 8, false );
wp_set_password( $password, $provider_id );

wp_update_user( array( 'ID' => $provider_id, 'user_email' => $claiminfo->email ) );
	
$cdata = array(
	'email' => $claiminfo->email,
);
$cwhere = array(
	'wp_user_id' => $provider_id
);
					
$wpdb->update($service_finder_Tables->providers,wp_unslash($cdata),$cwhere);

$tokens = array('%USERNAME%','%PASSWORD%');
$replacements = array($username,$password);
$msg_body = str_replace($tokens,$replacements,$message);

if(service_finder_wpmailer($claiminfo->email,$subject,$msg_body)) {
		
	$success = array(
			'status' => 'success',
			'suc_message' => sprintf(esc_html__('Payment for %s via wire transfer', 'service-finder'),$claimbusinessstr)
			);
	return json_encode($success);
}else{
	$error = array(
			'status' => 'error',
			'err_message' => esc_html__('Couldn&#8217;t send mail', 'service-finder')
			);
	return json_encode($error);
}
			
}

/*After claimed payment mail to user*/
function service_finder_after_claimedpayment_user($provider_id,$cid){
global $wpdb, $service_finder_Tables, $service_finder_options;

$claiminfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->claim_business.' WHERE id = %d',$cid));

if($service_finder_options['after-claimedpayment-subject-user'] != ""){
	$subject = $service_finder_options['after-claimedpayment-subject-user'];
}else{
	$subject = esc_html__('Payment for Claimed Business Completed', 'service-finder');
}

if($invoiceid != ""){
	$wiretransfermailinstructions = (!empty($service_finder_options['wire-transfer-mail-instructions'])) ? $service_finder_options['wire-transfer-mail-instructions'] : '';
	if($wiretransfermailinstructions != ''){
		$message = $wiretransfermailinstructions;
	}else{
		$message = 'Use following invoice ID When transfer amount in bank.';
	}
	$message .= 'Invoice ID:'.$invoiceid;
}else{
$message = '';
}

if(!empty($service_finder_options['after-claimedpayment-message-user'])){
	$message .= $service_finder_options['after-claimedpayment-message-user'];
}else{
	$message .= 'Congratulations! Your payment for claimed Business has been made successfully. Please use following credentials for login.

	Username: %USERNAME%
	
	Password: %PASSWORD%';
	
}

$userinfo = get_userdata($provider_id);
$username = $userinfo->user_login;
$password = wp_generate_password( 8, false );
wp_set_password( $password, $provider_id );

wp_update_user( array( 'ID' => $provider_id, 'user_email' => $claiminfo->email ) );
	
$cdata = array(
	'email' => $claiminfo->email,
);
$cwhere = array(
	'wp_user_id' => $provider_id
);
					
$wpdb->update($service_finder_Tables->providers,wp_unslash($cdata),$cwhere);

$tokens = array('%USERNAME%','%PASSWORD%');
$replacements = array($username,$password);
$msg_body = str_replace($tokens,$replacements,$message);			

if(service_finder_wpmailer($claiminfo->email,$subject,$msg_body)) {
		
	$success = array(
			'status' => 'success',
			'suc_message' => sprintf(esc_html__('Payment for %s successfully and send mail with login credentials to user', 'service-finder'),$claimbusinessstr)
			);
	return json_encode($success);
}else{
	$error = array(
			'status' => 'error',
			'err_message' => esc_html__('Couldn&#8217;t send mail', 'service-finder')
			);
	return json_encode($error);
}
			
}


/*After claimed payment mail to admin*/
function service_finder_after_claimedpayment_admin($args,$cid,$invoiceid = ''){
global $wpdb, $service_finder_Tables, $service_finder_options;			

$wpdb->query($wpdb->prepare('UPDATE '.$service_finder_Tables->claim_business.' SET `status` = "claimed" WHERE `id` = %d',$cid));
			
$admin_email = get_option( 'admin_email' );

$claiminfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->claim_business.' WHERE id = %d',$cid));

if($invoiceid != ''){
$message = 'Invoice ID:'.$invoiceid;
}else{
$message = '';
}

if($service_finder_options['after-claimedpayment-message-admin'] != ""){
	$message .= $service_finder_options['after-claimedpayment-message-admin'];
}else{
	$message .= 'Hello Admin,

				Provider have made payment for claimed business 
				
				Provider Details are:
				
				Username: %USERNAME%
				
				Email: %EMAIL%
				
				Package Name: %PACKAGENAME%
				
				Payment Type: %PAYMENTTYPE%
				
				User Details are:
				
				Fullname: %CUSTOMERNAME%
				
				User Email: %CUSTOMEREMAIL%';
}				

if($args['payment_type'] == 'stripe_upgrade'){
$paytype = 'Stripe';
}elseif($args['payment_type'] == 'paypal_upgrade'){
$paytype = 'Paypal';
}else{
$paytype = $args['payment_type'];
}

$tokens = array('%USERNAME%','%EMAIL%','%PACKAGENAME%','%PAYMENTTYPE%','%CUSTOMERNAME%','%CUSTOMEREMAIL%');
$replacements = array($args['username'],$args['email'],$args['package_name'],$paytype,$claiminfo->fullname,$claiminfo->email);
$msg_body = str_replace($tokens,$replacements,$message);

if($service_finder_options['after-claimedpayment-subject-admin'] != ""){
	$msg_subject = $service_finder_options['after-claimedpayment-subject-admin'];
}else{
	$msg_subject = esc_html__('Claimed Payment Completed', 'service-finder');
}

if(service_finder_wpmailer($admin_email,$msg_subject,$msg_body)) {

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
