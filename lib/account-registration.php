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
 * Register or upgrade user
 */
$payment_mode = (isset($_POST['payment_mode'])) ? $_POST['payment_mode'] : '';
$pay_mode = (isset($_POST['pay_mode'])) ? $_POST['pay_mode'] : '';
$skip_pay_mode = (isset($_POST['skip_pay_mode'])) ? $_POST['skip_pay_mode'] : '';
$freemode = (isset($_POST['freemode'])) ? $_POST['freemode'] : '';
$user_register = (isset($_POST['userregister'])) ? $_POST['userregister'] : '';
$wootype = (isset($_POST['wootype'])) ? $_POST['wootype'] : '';

if($skip_pay_mode == 'skippayment'){
$pay_mode = 'skippayment';
}
if($wootype != 'signup' && $wootype != 'upgrade' && (isset($_POST['payment_mode']) || isset($_POST['pay_mode']) || isset($_POST['skip_pay_mode']) || (isset($_POST['freemode']) && $freemode == 'yes')) && ($payment_mode == 'paypal' || $payment_mode == 'wired' || (isset($_POST['freemode']) && $freemode == 'yes') || ($pay_mode == 'paypal_upgrade' || $pay_mode == 'skippayment' || $pay_mode == 'wired_upgrade')) && isset($_POST['userregister'])) {

	// register user with minimal role
	$upgrade = false;
	$pay_mode = (isset($_POST['pay_mode'])) ? $_POST['pay_mode'] : '';
	$skip_pay_mode = (isset($_POST['skip_pay_mode'])) ? $_POST['skip_pay_mode'] : '';
	if($skip_pay_mode == 'skippayment'){
	$pay_mode = 'skippayment';
	}
	if($pay_mode == 'paypal_upgrade' || $pay_mode == 'skippayment' || $pay_mode == 'wired_upgrade' || ($freemode == 'yes' && $user_register == 'upgrade')){
		$upgrade = true;
		$currentRole =  get_user_meta($_POST['user_id'],'provider_role',true);
		$currentPayType = get_user_meta($_POST['user_id'],'pay_type',true);
		if($currentPayType == 'single'){
			$paidAmount =  get_user_meta($_POST['user_id'],'profile_amt',true);
		}
		$userId = $_POST['user_id'];
	} else {
		$userId = service_finder_sedateUserRegistration($_POST);
		$currentPayType = '';
	}
	// if errors
	if(is_wp_error( $userId )){
		$registerErrors = $userId;

	} else {
		$roleNum = 1;
		$rolePrice = '0';
		$free = true;
		$price = '0';
		$packageName = '';
		// set role
		$get_provider_role = (isset($_POST['provider-role'])) ? $_POST['provider-role'] : '';
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
							
							if($service_finder_options['payment-type'] == 'single' && $currentPayType == 'single' && $upgrade){
							$price = floatval($service_finder_options['package1-price']) - floatval($paidAmount);							
							}else{
							$price = trim($service_finder_options['package1-price']);								
							}
						}
						break;
					case "package_2":
						if(isset($service_finder_options['package2-price']) && trim($service_finder_options['package2-price']) !== '0') {
							$rolePrice = $service_finder_options['package2-price'];
							$free = false;
							$packageName = $service_finder_options['package2-name'];
							
							if($service_finder_options['payment-type'] == 'single' && $currentPayType == 'single' && $upgrade){
							$price = floatval($service_finder_options['package2-price']) - floatval($paidAmount);							
							}else{
							$price = trim($service_finder_options['package2-price']);								
							}
						}
						break;
					case "package_3":
						if(isset($service_finder_options['package3-price']) && trim($service_finder_options['package3-price']) !== '0') {
							$rolePrice = $service_finder_options['package3-price'];
							$free = false;
							$packageName = $service_finder_options['package3-name'];
							
							if($service_finder_options['payment-type'] == 'single' && $currentPayType == 'single' && $upgrade){
							$price = floatval($service_finder_options['package3-price']) - floatval($paidAmount);							
							}else{
							$price = trim($service_finder_options['package3-price']);								
							}
						}
						break;
					default:
						break;
				}
				$type = '';
				// non free
				if( isset($service_finder_options['enable-paypal']) && (!$free) && $pay_mode != 'skippayment' && $pay_mode != 'wired_upgrade' && $payment_mode != 'wired' ){

					$currencyCode = service_finder_currencycode();
					$sandbox = (isset($service_finder_options['paypal-type']) && $service_finder_options['paypal-type'] == 'live') ? '' : 'sandbox.';
					$paymentName = (isset($service_finder_options['paypal-payment-name'])) ? $service_finder_options['paypal-payment-name'] : esc_html__('Payment via Paypal','service-finder');
					$paymentDescription = ($upgrade) ? esc_html__('Upgrade to ','service-finder') . $packageName : $packageName;

					if($upgrade){
						$paymentName .= esc_html__(' Upgrade','service-finder');
					}

					$returnUrl = ($upgrade) ? home_url("/?user-register=success&upgrade=1&role=".$role) : home_url("/?user-register=success&role=".$role);
					$cancelUrl = ($upgrade) ? home_url("/?user-register=cancel&upgrade=1") : home_url("/?user-register=cancel");
					$urlParams = array(
						'RETURNURL' => $returnUrl,
						'CANCELURL' => $cancelUrl
					);
					
					if(!$upgrade){
					$ipn_page = SERVICE_FINDER_BOOKING_LIB_URL.'/paypal_ipn.php?signup=done&userid='.$userId;
					}else{
					$ipn_page = SERVICE_FINDER_BOOKING_LIB_URL.'/paypal_ipn.php?upgrade=done&userid='.$userId;
					}

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
							'AMT' => $price,
							'CURRENCYCODE' => $currencyCode,
							'PAYMENTACTION' => 'Sale',
							'L_BILLINGTYPE0' => 'RecurringPayments',
							'L_BILLINGAGREEMENTDESCRIPTION0' => urlencode($recurringDescriptionFull),
							'PAYMENTREQUEST_0_NOTIFYURL' => $ipn_page
						);
						$params = $urlParams + $recurring;

					} else {
						// Single payments
						$orderParams = array(
							'PAYMENTREQUEST_0_AMT' => $price,
							'PAYMENTREQUEST_0_SHIPPINGAMT' => '0',
							'PAYMENTREQUEST_0_CURRENCYCODE' => $currencyCode,
							'PAYMENTREQUEST_0_ITEMAMT' => $price,
							'PAYMENTREQUEST_0_NOTIFYURL' => $ipn_page
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
						if(!$upgrade){
						$wpdb->query($wpdb->prepare('DELETE FROM '.$wpdb->users.' WHERE `ID` = %d',$userId));
						$wpdb->query($wpdb->prepare('DELETE FROM '.$wpdb->usermeta.' WHERE `user_id` = %d',$userId));
		  			    service_finder_deleteProvidersData($userId);
						}
						$errorMessage = esc_html__( 'ERROR:', 'service-finder' );
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

							
							if($upgrade && $currentPayType == 'single'){
								delete_user_meta($userId, 'expire_limit');
								delete_user_meta($userId, 'profile_amt');
							}
							
							update_user_meta( $userId, 'provider_activation_time', array( 'role' => $role, 'time' => time()) );
							
							$type = ($upgrade) ? 'upgrade' : 'register';
							update_user_meta($userId, 'recurring_profile_type',$type);
							
							update_user_meta( $userId, 'pay_type', 'recurring' );

							update_user_meta($userId, 'recurring_profile_amt',$rolePrice);
							update_user_meta($userId, 'recurring_profile_init_amt',$price);
							update_user_meta($userId, 'recurring_profile_period',$service_finder_options['package'.$roleNum.'-billing-period']);
							update_user_meta($userId, 'recurring_profile_desc_full',$recurringDescriptionFull); 
							update_user_meta($userId, 'recurring_profile_desc',$recurringDescription); 
							if($upgrade){
							update_user_meta( $userId, 'payment_mode', $pay_mode );
							}else{
							update_user_meta( $userId, 'payment_mode', $payment_mode );
							}

						}
						// go to payment site
						header( 'Location: https://www.'.$sandbox.'paypal.com/webscr?cmd=_express-checkout&token=' . urlencode($token) );
						die();

					} else {
						if(!$upgrade){
						$wpdb->query($wpdb->prepare('DELETE FROM '.$wpdb->users.' WHERE `ID` = %d',$userId));
						$wpdb->query($wpdb->prepare('DELETE FROM '.$wpdb->usermeta.' WHERE `user_id` = %d',$userId));
		  			    service_finder_deleteProvidersData($userId);
						}
						$errorMessage = esc_html__( 'ERROR:', 'service-finder' );
						$detailErrorMessage = (isset($response['L_LONGMESSAGE0'])) ? $response['L_LONGMESSAGE0'] : '';
						$errors->add( 'bad_paypal_api', $errorMessage . ' ' . $detailErrorMessage );
						$registerErrors = $errors;
					}

				} else {
					$wiredupgrade = array();
					// free
					$user = new WP_User( $userId );
					$user->set_role('Provider');
					
					$userdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$wpdb->users.' WHERE `ID` = %d',$userId));
					$userInfo = service_finder_getUserInfo($userId);
					
					$expire_limit = $service_finder_options['package'.$roleNum.'-expday'];
					$invoiceid = '';
					
					if($pay_mode == 'wired_upgrade'){
					$wiredupgrade['payment_mode'] = 'wired';
					
					$wiredupgrade['current_package'] = $currentRole;
					
					$wiredupgrade['price'] = $price;
					$wiredupgrade['time'] = time();
					
					$invoiceid = strtoupper(uniqid('REG-'));
					
					$wiredupgrade['wired_invoiceid'] = $invoiceid;
					
					$wiredupgrade['recurring_profile_type'] = $type;
					$wiredupgrade['provider_role'] = $role;
					
					if($expire_limit > 0){
					$wiredupgrade['expire_limit'] = $expire_limit;
					}
					//update_user_meta( $userId, 'provider_activation_time', array( 'role' => $role, 'time' => time()) );
					
					$roleNum = intval(substr($role, 8));
					$roleName = (!empty($service_finder_options['package'.$roleNum.'-name'])) ? $service_finder_options['package'.$roleNum.'-name'] : '';
					
					if($roleNum == 0){
						update_user_meta($userId, 'trial_package', 'yes');
						$wiredupgrade['trial_package'] = 'yes';
					}
					
					$userInfo = service_finder_getUserInfo($userId);
					$paymentstatus = 'Wire Transfer';
					
					update_user_meta($userId, 'upgrade_request',$wiredupgrade);
					update_user_meta($userId, 'upgrade_request_status','pending');

					$primarycategory = get_user_meta($userId, 'primary_category',true);
					$args = array(
							'username' => $userdata->user_login,
							'email' => $userdata->user_email,
							'phone' => $userInfo['phone'],
							'address' => $userInfo['address'],
							'city' => $userInfo['city'],
							'country' => $userInfo['country'],
							'zipcode' => $userInfo['zipcode'],
							'category' => service_finder_getCategoryNameviaSql($primarycategory),
							'package_name' => $roleName,
							'payment_type' => $paymentstatus
							);
					
					if($upgrade){
						//service_finder_update_job_limit($userId);
						service_finder_sendWiredUpgradeMailToProvider($userdata->user_login,$userdata->user_email,$args,$invoiceid);
						service_finder_sendProviderWiredUpgradeEmail($args,$invoiceid);
						// upgrade
						$registerMessages = (!empty($service_finder_options['provider-upgrade-successfull'])) ? $service_finder_options['provider-upgrade-successfull'] : esc_html__('Your provider account was upgraded', 'service-finder');
						$redirect = service_finder_get_url_by_shortcode('[service_finder_success_message]').'?upgrade=pending';
						$current_user = wp_get_current_user(); 
						if(service_finder_get_url_by_shortcode('[service_finder_success_message]') == '' && service_finder_getUserRole($current_user->ID) == 'administrator'){
							$redirect = add_query_arg( array('manageaccountby' => 'admin','manageproviderid' => $userId,'upgrade' => 'pending','tabname' => 'upgrade'), service_finder_get_url_by_shortcode('[service_finder_my_account]') );
						}else{
							$redirect = add_query_arg( array('upgrade' => 'pending','tabname' => 'upgrade'), service_finder_get_url_by_shortcode('[service_finder_success_message]') );
						}
					}
					wp_redirect($redirect);
					}else{
					if($pay_mode == 'skippayment'){
						update_user_meta( $userId, 'payment_mode', 'by_admin' );
					}else{
						if($payment_mode == 'wired'){
							$paymentvia = 'wired';
							$invoiceid = strtoupper(uniqid('REG-'));
							update_user_meta( $userId, 'wired_invoiceid', $invoiceid );
						}else{
							$paymentvia = 'free';
						}
						update_user_meta( $userId, 'payment_mode', $paymentvia );
					}
					
					update_user_meta($userId, 'recurring_profile_type',$type);
					update_user_meta( $userId, 'provider_role', $role );
					if($expire_limit > 0){
						update_user_meta($userId, 'expire_limit', $expire_limit);
					}else{
						delete_user_meta($userId, 'expire_limit');
					}
					update_user_meta( $userId, 'provider_activation_time', array( 'role' => $role, 'time' => time()) );
					
					$roleNum = intval(substr($role, 8));
					$roleName = (!empty($service_finder_options['package'.$roleNum.'-name'])) ? $service_finder_options['package'.$roleNum.'-name'] : '';
					
					if($roleNum == 0){
						update_user_meta($userId, 'trial_package', 'yes');
					}
					
					$userInfo = service_finder_getUserInfo($userId);
					if($payment_mode == 'wired'){
						$paymentstatus = 'Wire Transfer';
					}elseif($pay_mode == 'skippayment'){
						$paymentstatus = 'By Admin';
					}else{
						$paymentstatus = 'Free';
					}
					$signupcategory = isset($_POST['signup_category']) ? esc_html($_POST['signup_category']) : '';
					$args = array(
							'username' => $userdata->user_login,
							'email' => $userdata->user_email,
							'phone' => $userInfo['phone'],
							'address' => $userInfo['address'],
							'city' => $userInfo['city'],
							'country' => $userInfo['country'],
							'zipcode' => $userInfo['zipcode'],
							'category' => service_finder_getCategoryNameviaSql($signupcategory),
							'package_name' => $roleName,
							'payment_type' => $paymentstatus
							);
					
					if($upgrade){
						delete_user_meta($userId, 'current_provider_status');
						service_finder_update_job_limit($userId);
						service_finder_sendUpgradeMailToUser($userdata->user_login,$userdata->user_email,$args);
						service_finder_sendProviderUpgradeEmail($args);
						// upgrade
						$registerMessages = (!empty($service_finder_options['provider-upgrade-successfull'])) ? $service_finder_options['provider-upgrade-successfull'] : esc_html__('Your provider account was upgraded', 'service-finder');
						$redirect = service_finder_get_url_by_shortcode('[service_finder_success_message]').'?upgrade=success';
						if(service_finder_get_url_by_shortcode('[service_finder_success_message]') == '' && $pay_mode == 'skippayment'){
							$redirect = add_query_arg( array('manageaccountby' => 'admin','manageproviderid' => $userId,'upgrade' => 'success','tabname' => 'upgrade'), service_finder_get_url_by_shortcode('[service_finder_my_account]') );
						}else{
							$redirect = add_query_arg( array('upgrade' => 'success','tabname' => 'upgrade'), service_finder_get_url_by_shortcode('[service_finder_my_account]') );
						}
					} else {
						service_finder_sendRegMailToUser($userdata->user_login,$userdata->user_email,$invoiceid);
						service_finder_sendProviderEmail($args,$invoiceid);
						$registerMessages = (!empty($service_finder_options['provider-signup-successfull'])) ? $service_finder_options['provider-signup-successfull'] : esc_html__('Your provider account created', 'service-finder');

						$pageid = (!empty($service_finder_options['signup-redirect-option'])) ? $service_finder_options['signup-redirect-option'] : '';
						if($pageid == 'no' || $pageid == ''){
						$redirect = add_query_arg( array('created' => 'success'), service_finder_get_url_by_shortcode('[service_finder_success_message]') );
						}else{
						$redirect = add_query_arg( array('created' => 'success'), get_permalink($pageid) );
						}
					}
					wp_redirect($redirect);
					}
					die;

				}
			}
		}
	}
	unset($_POST);
}

// check token (paypal merchant authorization) and Do Payment
$userregister = isset($_GET['user-register']) ? $_GET['user-register'] : '';
if(isset($_GET['user-register']) && ($userregister == 'success') && !empty($_GET['token'])) {
	// find token
	$token = isset($_GET['token']) ? $_GET['token'] : '';
	$currentPayType = '';
	$tokenRow = $wpdb->get_row( $wpdb->prepare("SELECT * FROM ".$wpdb->usermeta." WHERE meta_value = '%s'",$token) );
	if(!empty($tokenRow)){
		
		// get user id
		$userId = $tokenRow->user_id;
		$userdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$wpdb->users.' WHERE `ID` = %d',$userId));
		$userInfo = service_finder_getUserInfo($userId);
		// delete token from DB
		
		$upgrade = false;
		$getupgrade = isset($_GET['upgrade']) ? $_GET['upgrade'] : '';
		if($getupgrade == 1){
			$upgrade = true;
		}
		
		// get role
		$role = get_user_meta($userId,'reg_paypal_role',true);

		// get checkout details from token
		$checkoutDetails = $paypal -> request('GetExpressCheckoutDetails', array('TOKEN' => $_GET['token']));
		$errors = new WP_Error();
		if(!$checkoutDetails){
			$errorMessage = esc_html__( 'ERROR:', 'service-finder' );
			$detailErrorMessage = $paypal->getErrors();
			$errors->add( 'bad_paypal_api', $errorMessage . ' ' . $detailErrorMessage );
			$registerErrors = $errors;
		}
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
						$redirect = add_query_arg( array('upgrade' => 'failed','tabname' => 'upgrade'), service_finder_get_url_by_shortcode('[service_finder_my_account]') );
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
									$redirect = add_query_arg( array('upgrade' => 'failed','tabname' => 'upgrade'), service_finder_get_url_by_shortcode('[service_finder_my_account]') );
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
				
				$type = get_user_meta($userId,'recurring_profile_type',true);
				if (!empty($type) && ($type == 'upgrade')) {
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
					'SUBSCRIBERNAME' => $userInfo['fname'].' '.$userInfo['lname'],
					'FIRSTNAME' => $userInfo['fname'],
					'LASTNAME' => $userInfo['lname'],
					'INITAMT' => $initAmt,
					'AMT' => $amt,
					'CURRENCYCODE' => $currencyCode,
					'DESC' => urlencode($description),
					'BILLINGPERIOD' => $period,
					'BILLINGFREQUENCY' => '1',
					'PROFILESTARTDATE' => $begins,
					'FAILEDINITAMTACTION' => 'CancelOnFailure',
					'AUTOBILLAMT' => 'AddToNextBilling',
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
					$paymode = ($upgrade) ? $_POST['pay_mode'] : $_POST['payment_mode'];
					$userInfo = service_finder_getUserInfo($userId);
					$args = array(
							'username' => $userdata->user_login,
							'email' => $userdata->user_email,
							'phone' => $userInfo['phone'],
							'address' => $userInfo['address'],
							'city' => $userInfo['city'],
							'country' => $userInfo['country'],
							'zipcode' => $userInfo['zipcode'],
							'category' => $userInfo['categoryname'],
							'package_name' => $roleName,
							'payment_type' => $paymode
							);
					
					// show messages
					if(isset($_GET['upgrade'])){
						delete_user_meta($userId, 'current_provider_status');
						service_finder_update_job_limit($userId);
						service_finder_sendUpgradeMailToUser($userdata->user_login,$userdata->user_email,$args);
						service_finder_sendProviderUpgradeEmail($args);
						$registerMessages = (!empty($service_finder_options['provider-recurring-upgrade-successfull'])) ? $service_finder_options['provider-recurring-upgrade-successfulll'] : esc_html__('PayPal recurring payments profile created. Your provider account was upgraded', 'service-finder');
						$redirect = service_finder_get_url_by_shortcode('[service_finder_success_message]').'?upgrade=success';
						$current_user = wp_get_current_user(); 
						if(service_finder_get_url_by_shortcode('[service_finder_success_message]') == '' && service_finder_getUserRole($current_user->ID) == 'administrator'){
							$redirect = add_query_arg( array('manageaccountby' => 'admin','manageproviderid' => $userId,'upgrade' => 'success','tabname' => 'upgrade'), service_finder_get_url_by_shortcode('[service_finder_my_account]') );
						}else{
							$redirect = add_query_arg( array('upgrade' => 'success','tabname' => 'upgrade'), service_finder_get_url_by_shortcode('[service_finder_my_account]') );
						}
					} else {
						service_finder_sendRegMailToUser($userdata->user_login,$userdata->user_email);
						service_finder_sendProviderEmail($args);
						$registerMessages = (!empty($service_finder_options['provider-recurring-signup-successfull'])) ? $service_finder_options['provider-recurring-signup-successfull'] : esc_html__('Your provider account created', 'service-finder');
						$pageid = (!empty($service_finder_options['signup-redirect-option'])) ? $service_finder_options['signup-redirect-option'] : '';
						if($pageid == 'no' || $pageid == ''){
						$redirect = add_query_arg( array('created' => 'success'), service_finder_get_url_by_shortcode('[service_finder_success_message]') );
						}else{
						$redirect = add_query_arg( array('created' => 'success'), get_permalink($pageid) );
						}
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
					if($expire_limit > 0){
						update_user_meta($userId, 'expire_limit', $expire_limit);
					}else{
						delete_user_meta($userId, 'expire_limit');
					}
					update_user_meta($userId, 'profile_amt',$rolePrice);
					
					$pay_mode = (isset($_POST['pay_mode'])) ? $_POST['pay_mode'] : '';
					$payment_mode = (isset($_POST['payment_mode'])) ? $_POST['payment_mode'] : '';
					if($upgrade){
					update_user_meta( $userId, 'payment_mode', $pay_mode );
					}else{
					update_user_meta( $userId, 'payment_mode', $payment_mode );
					}

					// We'll fetch the transaction ID for internal bookkeeping
					$transactionId = $singlePayment['PAYMENTINFO_0_TRANSACTIONID'];
					update_user_meta( $userId, 'txn_id', $transactionId );
					
					
					$paymode = ($upgrade) ? $pay_mode : $payment_mode;
					$userInfo = service_finder_getUserInfo($userId);
					$args = array(
							'username' => (!empty($userdata->user_login)) ? $userdata->user_login : '',
							'email' => (!empty($userdata->user_email)) ? $userdata->user_email : '',
							'address' => (!empty($userInfo['address'])) ? $userInfo['address'] : '',
							'city' => (!empty($userInfo['city'])) ? $userInfo['city'] : '',
							'country' => (!empty($userInfo['country'])) ? $userInfo['country'] : '',
							'zipcode' => (!empty($userInfo['zipcode'])) ? $userInfo['zipcode'] : '',
							'category' => (!empty($userInfo['categoryname'])) ? $userInfo['categoryname'] : '',
							'package_name' => $roleName,
							'payment_type' => $paymode
							);
					
					// show messages
					if(isset($_GET['upgrade'])){
					delete_user_meta($userId, 'current_provider_status');
					service_finder_update_job_limit($userId);
					$ulogin = (!empty($userdata->user_login)) ? $userdata->user_login : '';
					$uemail = (!empty($userdata->user_email)) ? $userdata->user_email : '';
						service_finder_sendUpgradeMailToUser($ulogin,$uemail,$args);
						service_finder_sendProviderUpgradeEmail($args);
						$registerMessages = (!empty($service_finder_options['provider-upgrade-successfull'])) ? $service_finder_options['provider-upgrade-successfull'] : esc_html__('Your provider account was upgraded', 'service-finder');
						$redirect = service_finder_get_url_by_shortcode('[service_finder_success_message]').'?upgrade=success';
						
						$current_user = wp_get_current_user(); 
						if(service_finder_get_url_by_shortcode('[service_finder_success_message]') == '' && service_finder_getUserRole($current_user->ID) == 'administrator'){
							$redirect = add_query_arg( array('manageaccountby' => 'admin','manageproviderid' => $userId,'upgrade' => 'success','tabname' => 'upgrade'), service_finder_get_url_by_shortcode('[service_finder_my_account]') );
						}else{
							$redirect = add_query_arg( array('upgrade' => 'success','tabname' => 'upgrade'), service_finder_get_url_by_shortcode('[service_finder_my_account]') );
						}
						
					} else {
						service_finder_sendRegMailToUser($userdata->user_login,$userdata->user_email);
						service_finder_sendProviderEmail($args);
						$registerMessages = (!empty($service_finder_options['provider-signup-successfull'])) ? $service_finder_options['provider-signup-successfull'] : esc_html__('Your provider account created', 'service-finder');
						
						$pageid = (!empty($service_finder_options['signup-redirect-option'])) ? $service_finder_options['signup-redirect-option'] : '';
						if($pageid == 'no' || $pageid == ''){
						$redirect = add_query_arg( array('created' => 'success'), service_finder_get_url_by_shortcode('[service_finder_success_message]') );
						}else{
						$redirect = add_query_arg( array('created' => 'success'), get_permalink($pageid) );
						}
					}
					wp_redirect(esc_url($redirect));
					die;

				}

			}

		}else{
			$errorMessage = esc_html__( 'ERROR:', 'service-finder' );
			$detailErrorMessage = (isset($response['L_LONGMESSAGE0'])) ? $response['L_LONGMESSAGE0'] : '';
			$errors->add( 'bad_paypal_api', $errorMessage . ' ' . $detailErrorMessage );
			$registerErrors = $errors;
		}

	}
}

// delete token and show messages if user cancel payment 
$userregister = isset($_GET['user-register']) ? $_GET['user-register'] : '';
if(isset($_GET['user-register']) && ($userregister == 'cancel') && isset($_GET['token'])){
	
// delete token from DB
$token = (isset($_GET['token'])) ? $_GET['token'] : '';
$tokenRow = $wpdb->get_row( $wpdb->prepare("SELECT * FROM ".$wpdb->usermeta." WHERE meta_value = '%s'",$token) );
if($tokenRow){
	
	// get user id
	$userId = $tokenRow->user_id;
	
	// show message
	$errors = new WP_Error();
	if (isset($_GET['upgrade'])) {
		$message = esc_html__("You canceled payment. Your account wasn't changed.","service-finder");
		$errors->add( 'cancel_payment', $message);
		$registerErrors = $errors;
	} else {
	
		$wpdb->query( $wpdb->prepare( "DELETE FROM ".$wpdb->usermeta." WHERE user_id = %d", $userId ) );
		$wpdb->query( $wpdb->prepare( "DELETE FROM ".$wpdb->users." WHERE ID = %d", $userId ) );
		$wpdb->query( $wpdb->prepare( "DELETE FROM ".$service_finder_Tables->providers." WHERE `wp_user_id` = %d", $userId ) );
	
		$message = esc_html__("You canceled payment. Your account wasn't created","service-finder");
		$errors->add( 'cancel_payment', $message);
		$registerErrors = $errors;
	}
}	
}

/*User register and upgrade via payu money start*/
if((isset($_POST['payment_mode']) || isset($_POST['pay_mode'])) && ($payment_mode == 'payumoney' || $pay_mode == 'payumoney_upgrade') && isset($_POST['userregister'])) {

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
$upgrade = false;
$pay_mode = (isset($_POST['pay_mode'])) ? $_POST['pay_mode'] : '';
if($pay_mode == 'payumoney_upgrade'){
	$upgrade = true;
	$currentRole =  get_user_meta($_POST['user_id'],'provider_role',true);
	$currentPayType = get_user_meta($_POST['user_id'],'pay_type',true);
	if($currentPayType == 'single'){
		$paidAmount =  get_user_meta($_POST['user_id'],'profile_amt',true);
	}
	$userId = esc_html($_POST['user_id']);
} else {
	$userId = service_finder_sedateUserRegistration($_POST);
	$currentPayType = '';
}
// if errors
if(is_wp_error( $userId )){
	$registerErrors = $userId;

} else {
	$roleNum = 1;
	$rolePrice = '0';
	$price = '0';
	$packageName = '';
	// set role
	$get_provider_role = (isset($_POST['provider-role'])) ? $_POST['provider-role'] : '';
	if(isset($get_provider_role)){
		$role = $get_provider_role;
		if (($role == "package_1") || ($role == "package_2") || ($role == "package_3") || ($freemode == 'yes')){
			$roleNum = intval(substr($role, 8));
			switch ($role) {
				case "package_1":
					if(isset($service_finder_options['package1-price']) && trim($service_finder_options['package1-price']) !== '0') {
						$rolePrice = $service_finder_options['package1-price'];
						$packageName = $service_finder_options['package1-name'];
						
						if($service_finder_options['payment-type'] == 'single' && $currentPayType == 'single' && $upgrade){
						$price = floatval($service_finder_options['package1-price']) - floatval($paidAmount);							
						}else{
						$price = trim($service_finder_options['package1-price']);								
						}
					}
					break;
				case "package_2":
					if(isset($service_finder_options['package2-price']) && trim($service_finder_options['package2-price']) !== '0') {
						$rolePrice = $service_finder_options['package2-price'];
						$packageName = $service_finder_options['package2-name'];
						
						if($service_finder_options['payment-type'] == 'single' && $currentPayType == 'single' && $upgrade){
						$price = floatval($service_finder_options['package2-price']) - floatval($paidAmount);							
						}else{
						$price = trim($service_finder_options['package2-price']);								
						}
					}
					break;
				case "package_3":
					if(isset($service_finder_options['package3-price']) && trim($service_finder_options['package3-price']) !== '0') {
						$rolePrice = $service_finder_options['package3-price'];
						$packageName = $service_finder_options['package3-name'];
						
						if($service_finder_options['payment-type'] == 'single' && $currentPayType == 'single' && $upgrade){
						$price = floatval($service_finder_options['package3-price']) - floatval($paidAmount);							
						}else{
						$price = trim($service_finder_options['package3-price']);								
						}
					}
					break;
				default:
					break;
			}
			$type = '';

				$currencyCode = service_finder_currencycode();
				$paymentName = esc_html__('Payment via PayU Money','service-finder');
				$paymentDescription = ($upgrade) ? esc_html__('Upgrade to ','service-finder') . $packageName : $packageName;

				if($upgrade){
					$paymentName .= esc_html__(' Upgrade','service-finder');
				}
				
				$surl = ($upgrade) ? add_query_arg( array('registerviapayumoney' => 'success','upgrade' => '1','payutransactionforreg' => 'success','tabname' => 'upgrade'), home_url('/my-account/') ) : add_query_arg( array('registerviapayumoney' => 'success','payutransactionforreg' => 'success'), home_url('/my-account/') );
				
				$furl = ($upgrade) ? add_query_arg( array('registerviapayumoney' => 'failed','payutransactionforreg' => 'failed','upgrade' => '1','tabname' => 'upgrade'), home_url('/my-account/') ) : add_query_arg( array('registerviapayumoney' => 'failed','payutransactionforreg' => 'failed'), home_url('/my-account/') );
				
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
}

}else{
		global $registerErrors;
		$errors = new WP_Error();
		$message = esc_html__("Please set PayU Money Credentials","service-finder");
		$errors->add( 'set_credentials', $message);
		$registerErrors = $errors;
}
}

//echo '<pre>';print_r($_REQUEST);echo '</pre>';

if(isset($_GET['registerviapayumoney']) && $_GET['registerviapayumoney'] == 'success' && $_GET['payutransactionforreg'] == 'success' && isset($_GET['payutransactionforreg']) && isset($_POST['mihpayid']) && isset($_POST['status'])){

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
		$redirect = add_query_arg( array('upgrade' => 'failed','tabname' => 'upgrade'), service_finder_get_url_by_shortcode('[service_finder_my_account]') );
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
					$redirect = add_query_arg( array('upgrade' => 'failed','tabname' => 'upgrade'), service_finder_get_url_by_shortcode('[service_finder_my_account]') );
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
if($expire_limit > 0){
	update_user_meta($userId, 'expire_limit', $expire_limit);
}else{
	delete_user_meta($userId, 'expire_limit');
}
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
		'address' => (!empty($userInfo['address'])) ? $userInfo['address'] : '',
		'city' => (!empty($userInfo['city'])) ? $userInfo['city'] : '',
		'country' => (!empty($userInfo['country'])) ? $userInfo['country'] : '',
		'zipcode' => (!empty($userInfo['zipcode'])) ? $userInfo['zipcode'] : '',
		'category' => (!empty($userInfo['categoryname'])) ? $userInfo['categoryname'] : '',
		'package_name' => $roleName,
		'payment_type' => $paymode
		);

if($upgrade){
delete_user_meta($userId, 'current_provider_status');
service_finder_update_job_limit($userId);
$ulogin = (!empty($userdata->user_login)) ? $userdata->user_login : '';
$uemail = (!empty($userdata->user_email)) ? $userdata->user_email : '';
	service_finder_sendUpgradeMailToUser($ulogin,$uemail,$args);
	service_finder_sendProviderUpgradeEmail($args);
	$registerMessages = (!empty($service_finder_options['provider-upgrade-successfull'])) ? $service_finder_options['provider-upgrade-successfull'] : esc_html__('Your provider account was upgraded', 'service-finder');
	
	$current_user = wp_get_current_user(); 
	if(service_finder_get_url_by_shortcode('[service_finder_success_message]') == '' && service_finder_getUserRole($current_user->ID) == 'administrator'){
		$redirect = add_query_arg( array('manageaccountby' => 'admin','manageproviderid' => $userId,'upgrade' => 'success','tabname' => 'upgrade'), service_finder_get_url_by_shortcode('[service_finder_my_account]') );
	}else{
		$redirect = add_query_arg( array('upgrade' => 'success','tabname' => 'upgrade'), service_finder_get_url_by_shortcode('[service_finder_my_account]') );
	}
	
} else {
	service_finder_sendRegMailToUser($userdata->user_login,$userdata->user_email);
	service_finder_sendProviderEmail($args);
	$registerMessages = (!empty($service_finder_options['provider-signup-successfull'])) ? $service_finder_options['provider-signup-successfull'] : esc_html__('Your provider account created', 'service-finder');
	
	$pageid = (!empty($service_finder_options['signup-redirect-option'])) ? $service_finder_options['signup-redirect-option'] : 'no';
	if($pageid == 'no' || $pageid == ''){
		if(service_finder_get_url_by_shortcode('[service_finder_success_message]') != ""){
		$redirect = add_query_arg( array('created' => 'success'), service_finder_get_url_by_shortcode('[service_finder_success_message]') );
		}else{
		$redirect = add_query_arg( array('created' => 'success'), home_url() );
		}
	}else{
	$redirect = add_query_arg( array('created' => 'success'), get_permalink($pageid) );
	}
}
wp_redirect(esc_url($redirect));
die;

}

}

if(isset($_GET['registerviapayumoney']) && $_GET['registerviapayumoney'] == 'failed' && $_GET['payutransactionforreg'] == 'failed'){
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
} else {

	if($userId > 1){
	$wpdb->query( $wpdb->prepare( "DELETE FROM ".$wpdb->usermeta." WHERE user_id = %d", $userId ) );
	$wpdb->query( $wpdb->prepare( "DELETE FROM ".$wpdb->users." WHERE ID = %d", $userId ) );
	$wpdb->query( $wpdb->prepare( "DELETE FROM ".$service_finder_Tables->providers." WHERE `wp_user_id` = %d", $userId ) );
	}

	$message = esc_html__("You canceled payment. Your account wasn't created","service-finder");
	$errors->add( 'cancel_payment', $message);
	$registerErrors = $errors;
	
	$redirect = add_query_arg( array('registration' => 'cancelled'), home_url() );
	wp_redirect(esc_url($redirect));
	die;
}
}	

}
/*User register and upgrade via payu money end*/

// get recurring payment details
if(isset($_GET['dir-recurring-check'])) {
	$registerMessages = (aitCheckPayPalSubscription($_GET['dir-recurring-check'])) ? esc_html__('PayPal recurring payments profile is active.','service-finder') : esc_html__("PayPal recurring payments profile isn't active.",'service-finder');
}

// write activation time
add_action('set_user_role', 'service_finder_update_activationTime',1,2);
function service_finder_update_activationTime($id = 0, $role = '') {

	global $wpdb;
	
	if($role == 'Provider'){
	$provider_role = get_user_meta( $id, 'provider_role', true );
		if($provider_role == 'package_1' || $provider_role == 'package_2' || $provider_role == 'package_3'){
			update_user_meta( $id, 'user_activation_time', array( 'role' => $provider_role, 'time' => time()) );
		}
	}

}

// check if recurring payment profile is active in paypal
function service_finder_CheckProviderPayPalSubscription($profileId = '') {
	global $paypal;
	try {
	$recurringCheck = $paypal -> request('GetRecurringPaymentsProfileDetails',array('PROFILEID' => $profileId));
	if( is_array($recurringCheck) && ($recurringCheck['ACK'] == 'Success') && ($recurringCheck['STATUS'] == 'Active' || $recurringCheck['STATUS'] == 'Pending')) {
		return true;
	} else {
		return false;
	}
	}catch (Exception $e) {
	return $e->getMessage();
	}
}

// check if recurring payment profile is active in stripe
function service_finder_CheckProviderStripeSubscription($customerID = '',$subscriptionID = '') {
global $service_finder_options;
	require_once(SERVICE_FINDER_PAYMENT_GATEWAY_DIR.'/stripe/init.php');
	if( isset($service_finder_options['stripe-type']) && $service_finder_options['stripe-type'] == 'test' ){
		$secret_key = (!empty( $service_finder_options['stripe-test-secret-key'])) ? $service_finder_options['stripe-test-secret-key'] : '';
		$public_key = (!empty($service_finder_options['stripe-test-public-key'])) ? $service_finder_options['stripe-test-public-key'] : '';
	}else{
		$secret_key = (!empty($service_finder_options['stripe-live-public-key'])) ? $service_finder_options['stripe-live-secret-key'] : '';
		$public_key = (!empty($service_finder_options['stripe-live-public-key'])) ? $service_finder_options['stripe-live-public-key'] : '';
	}
	\Stripe\Stripe::setApiKey($secret_key);
	try {

	$currentcustomer = \Stripe\Customer::retrieve($customerID);
	
	$response = $currentcustomer->subscriptions->retrieve($subscriptionID);
	if($response->status == 'active') {
		return true;
	} else {
		return false;
	}
	}catch (Exception $e) {
	return $e->getMessage();
	}
}

// check if recurring payment profile is active in PayULatam
function service_finder_CheckProviderPayULatamSubscription($subscription_id = '') {
global $service_finder_options;

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
	return true;
}else{
	return false;
}
	
} catch (Exception $e) {
	return $e->getMessage();
}

}

// check if recurring payment profile is active in twocheckout
function service_finder_CheckProviderTwocheckoutSubscription($orderNumber = '') {
	global $wpdb, $service_finder_options;
	
	$twocheckouttype = (!empty($service_finder_options['twocheckout-type'])) ? esc_html($service_finder_options['twocheckout-type']) : '';
	
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
		$result = Twocheckout_Sale::active($args);
		return true;
		
	} catch (Twocheckout_Error $e) {
		return $e->getMessage();
	}

}

add_action( 'service_finder_check_provider_expirations', 'service_finder_CheckProviderExpirations' );
function service_finder_CheckProviderExpirations() {
	global $service_finder_options, $wpdb;
		// recurring payments - expire inactive subscriptions for Paypal
		$users = $wpdb->get_results("SELECT user_id, meta_value FROM ".$wpdb->usermeta." WHERE meta_key = 'recurring_profile_id'");
		if(!empty($users)){
			foreach ($users as $user) {
				if (!service_finder_CheckProviderPayPalSubscription($user->meta_value)) {
					service_finder_expireProviderUser($user->user_id);
				}
			}
		}
		
		// recurring payments - expire inactive subscriptions for Stripe
		$users = $wpdb->get_results("SELECT user_id, meta_value FROM ".$wpdb->usermeta." WHERE meta_key = 'subscription_id'");
		if(!empty($users)){
			foreach ($users as $user) {
				$customerID = get_user_meta($user->user_id,'stripe_customer_id',true);
				if (!service_finder_CheckProviderStripeSubscription($customerID,$user->meta_value)) {
					service_finder_expireProviderUser($user->user_id);
				}
			}
		}
		
		// recurring payments - expire inactive subscriptions for PayU Latam
		$users = $wpdb->get_results("SELECT user_id, meta_value FROM ".$wpdb->usermeta." WHERE meta_key = 'payulatam_planid'");
		if(!empty($users)){
			foreach ($users as $user) {
				
				$subscription_id = get_user_meta($userid,'subscription_id',true);
				
				if (!service_finder_CheckProviderPayULatamSubscription($subscription_id)) {
					service_finder_expireProviderUser($user->user_id);
				}
			}
		}
		
		// recurring payments - expire inactive subscriptions for Twocheckout
		$users = $wpdb->get_results("SELECT user_id, meta_value FROM ".$wpdb->usermeta." WHERE meta_key = 'orderNumber'");
		if(!empty($users)){
			foreach ($users as $user) {
				$orderNumber = get_user_meta($user->user_id,'orderNumber',true);
				if (!service_finder_CheckProviderTwocheckoutSubscription($customerID,$user->meta_value)) {
					service_finder_expireProviderUser($user->user_id);
				}
			}
		}
		
		// single payments
		$times = $wpdb->get_results("SELECT user_id, meta_value FROM ".$wpdb->usermeta." WHERE meta_key = 'expire_limit'");
		foreach ($times as $time) {
			$limit = floatval($time->meta_value);
			if($limit > 0){
			$activationtime = get_user_meta($time->user_id,'provider_activation_time',true);
			$timeInSec = $activationtime['time'];
			$role = $activationtime['role'];
			$differenceInSec = time() - $timeInSec;
			$differenceInDays = floor($differenceInSec / 60 / 60 / 24);
			
			if(!empty($service_finder_options['expiry-mail-notification-days'])){
				foreach($service_finder_options['expiry-mail-notification-days'] as $days){
					if($limit - $differenceInDays == $days){
						service_finder_SendSubscriptionNotificationMail($time->user_id,$days);
					}			
				}
			}

			
			if($differenceInDays >= $limit){
				service_finder_expireProviderUser($time->user_id);
			}
			
			}
		}
}

/*Send Subscription expiry notification mail*/
function service_finder_SendSubscriptionNotificationMail($userid = 0, $days = '',$by = ''){
global $wpdb, $service_finder_options, $service_finder_Tables;

$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$wpdb->users.' WHERE `ID` = %d',$userid));
$providerinfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$userid));
$admin_email = get_option( 'admin_email' );
if($by == 'manually'){

if($service_finder_options['provider-subscription-cancel'] != ""){
	$msg_subject = $service_finder_options['provider-subscription-cancel'];
}else{
	$msg_subject = esc_html__('Subscription Cancelled', 'service-finder');
}

if($service_finder_options['admin-subscription-cancel'] != ""){
	$admin_msg_subject = $service_finder_options['admin-subscription-cancel'];
}else{
	$admin_msg_subject = esc_html__('Subscription Cancelled', 'service-finder');
}

if($service_finder_options['send-to-subscription-cancel-provider'] != ""){
	$message = $service_finder_options['send-to-subscription-cancel-provider'];
}else{
	$message = '
	<h3>Subscription Cancellation Notification</h3>
	<br>
	Your subcription has been cancelled.<br/>';
}

if($service_finder_options['send-to-subscription-cancel-admin'] != ""){
	$adminmessage = $service_finder_options['send-to-subscription-cancel-admin'];
}else{
	$adminmessage = '
	<h3>Subscription Cancellation Notification</h3>
	<br>
	Provier has cancelled their subscription.<br/>
	
	Provider Info:<br/>
	Provider Name: %PROVIDERNAME%
	Provider Email: %PROVIDEREMAIL%
	Provider Phone: %PROVIDERPHONE%
	';
}

$tokens = array('%PROVIDERNAME%','%PROVIDEREMAIL%','%PROVIDERPHONE%');
$replacements = array(service_finder_get_providername_with_link($providerinfo->wp_user_id),$row->user_email,service_finder_get_contact_info($providerinfo->phone,$providerinfo->mobile));
$adminmessage = str_replace($tokens,$replacements,$adminmessage);

service_finder_wpmailer($admin_email,$admin_msg_subject,$adminmessage);
}else{

$msg_subject = 'Subscription Expiry Notification';

if($service_finder_options['provider-subscription-expire-subject'] != ""){
	$msg_subject = $service_finder_options['provider-subscription-expire-subject'];
}else{
	$msg_subject = esc_html__('Subscription Expiry Notification', 'service-finder');
}

if($service_finder_options['admin-subscription-expire-subject'] != ""){
	$admin_msg_subject = $service_finder_options['admin-subscription-expire-subject'];
}else{
	$admin_msg_subject = esc_html__('Subscription Expiry Notification', 'service-finder');
}

if($days == 0){

if($service_finder_options['send-to-subscription-expire-provider'] != ""){
	$message = $service_finder_options['send-to-subscription-expire-provider'];
}else{
	$message = '
	<h3>Subscription Expire Notification</h3>
	<br>
	Your subcription has been expired. Please upgrade it now.<br/>';
}

if($service_finder_options['send-to-subscription-expire-admin'] != ""){
	$adminmessage = $service_finder_options['send-to-subscription-expire-admin'];
}else{
	$adminmessage = '
	<h3>Subscription Expire Notification</h3>
	<br>
	Provier subcription has been expired.<br/>
	
	Provider Info:<br/>
	Provider Name: %PROVIDERNAME%
	Provider Email: %PROVIDEREMAIL%
	Provider Phone: %PROVIDERPHONE%
	';
}

$tokens = array('%PROVIDERNAME%','%PROVIDEREMAIL%','%PROVIDERPHONE%');
$replacements = array(service_finder_get_providername_with_link($providerinfo->wp_user_id),$row->user_email,service_finder_get_contact_info($providerinfo->phone,$providerinfo->mobile));
$adminmessage = str_replace($tokens,$replacements,$adminmessage);

service_finder_wpmailer($admin_email,$admin_msg_subject,$adminmessage);
}else{
if($service_finder_options['provider-subscription-expire-reminder-subject'] != ""){
	$msg_subject = $service_finder_options['provider-subscription-expire-reminder-subject'];
}else{
	$msg_subject = esc_html__('Subscription Expiry Reminder Notification', 'service-finder');
}

if($service_finder_options['send-to-subscription-expire-reminder-provider'] != ""){
	$message = $service_finder_options['send-to-subscription-expire-reminder-provider'];
}else{
	$message = '
	<h3>Subscription Expire Notification</h3>
	<br>
	Your subscription will be expire after %NUMBEROFDAYS% days. Please upgrade it now.<br/>';
}

$tokens = array('%NUMBEROFDAYS%');
$replacements = array($days);
$message = str_replace($tokens,$replacements,$message);

}
}
			
			
			$msg_body = $message;
			
			
			if(service_finder_wpmailer($row->user_email,$msg_subject,$msg_body)) {

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


// chcek paypal subscription at startup
add_action('admin_init','service_finder_CheckAccountLogedUser');
function service_finder_CheckAccountLogedUser() {
	global $service_finder_options, $current_user;
	if (service_finder_isProviderUser() && isset($service_finder_options['payment-type']) && ($service_finder_options['payment-type'] == 'recurring')) {
		$profileId = get_user_meta($current_user->ID,'recurring_profile_id',true);
		if ((!empty($profileId)) && (!aitCheckPayPalSubscription($profileId))) {
			aitDirExpireUser($current_user->ID);
		}
	}
}

/*Check if provider user is exist or not*/
function service_finder_isProviderUser($userToTest = null) {
	global $current_user;
	$user = (isset($userToTest)) ? $userToTest : $current_user;
	if( isset( $user->roles ) && is_array( $user->roles ) ) {
		if( in_array('package_1', $user->roles) || in_array('package_1', $user->roles) || in_array('package_1', $user->roles) ) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

/*Expire the provider user*/
function service_finder_expireProviderUser($userId = 0) {
	global $wpdb, $service_finder_options, $service_finder_Tables;
	service_finder_SendSubscriptionNotificationMail($userId,0);
	
	update_user_meta( $userId, 'current_provider_status', 'expire' );
	delete_user_meta($userId,'provider_role' );
	
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

	delete_user_meta($userId, 'expire_limit');
	delete_user_meta($userId, 'profile_amt');
	delete_user_meta($userId, 'stripe_customer_id');
	delete_user_meta($userId, 'stripe_token');
	delete_user_meta($userId, 'subscription_id');
	delete_user_meta($userId, 'payment_mode');
	delete_user_meta($userId, 'pay_type');
	
	delete_user_meta($userId, 'expire_limit');
	delete_user_meta($userId, 'provider_activation_time');
	
	$wpdb->query($wpdb->prepare('DELETE FROM '.$service_finder_Tables->feature.' WHERE `provider_id` = %d',$userId));
	
	$primarycategory = get_user_meta($userId, 'primary_category',true);
	
	/*Update Primary category*/
	$data = array(
			'featured' => 0,
			'category_id' => $primarycategory,
			);
	
	$where = array(
			'wp_user_id' => $userId,
			);
	$wpdb->update($service_finder_Tables->providers,wp_unslash($data),$where);
	
	$data = array(
			'free_limits' => 0,
			'available_limits' => 0,
			'paid_limits' => 0,
			);
	$where = array(
			'provider_id' => $userId,
			);		
	
	$wpdb->update($service_finder_Tables->job_limits,wp_unslash($data),$where);
}

/*Get the remaining days*/
function service_finder_getDaysLeft($userIdToTest = null) {
	global $wpdb, $current_user, $service_finder_options;

	$userId = (isset($userIdToTest)) ? intval($userIdToTest) : $current_user->ID;

	$data = $wpdb->get_row($wpdb->prepare("SELECT meta_value FROM ".$wpdb->usermeta." WHERE meta_key = 'user_activation_time' AND user_id = %d",$userId));
	$data = (isset($data->meta_value)) ? unserialize($data->meta_value) : array();

	if (!empty($data)) {
		$roleNumber = substr($data['role'], 8);
		$limit = (isset($service_finder_options['package'.$roleNumber.'-expday'])) ? 
			intval($service_finder_options['package'.$roleNumber.'-expday']) : 0;
		if($limit > 0){
			$timeInSec = $data['time'];
			$differenceInSec = ($limit * 60 * 60 * 24) - (time() - $timeInSec);
			$differenceInDays = ceil($differenceInSec / 60 / 60 / 24);
			if($differenceInDays <= 0){
				$differenceInDays = esc_html__('Expired','service-finder');
			}
		} else {
			$differenceInDays = esc_html__('Unlimited','service-finder');
		}
	} else {
		$differenceInDays = esc_html__('Unlimited','service-finder');
	}

	return $differenceInDays;
}

/* User Login */
add_action('wp_ajax_login', 'service_finder_login');
add_action('wp_ajax_nopriv_login', 'service_finder_login');

function service_finder_login(){
	global $wpdb, $user, $service_finder_options;
	
	/*Call the user login function*/
	
	$user = get_user_by('login',$_POST['login_user_name']);
	$uid = $user->ID;
	
	if(service_finder_getUserRole($user->ID) == 'Provider'){
	
	$access_only_claimed_users = get_user_meta( $user->ID, 'access_only_claimed_users', true );
	if($access_only_claimed_users == 'yes'){
		$claimed = get_user_meta( $user->ID, 'claimed', true );
		if($claimed == 'yes'){
			$result = service_finder_sedateUserLogin($_POST['login_user_name'],$_POST['login_password']);	
		}else{
			$error = array(
					'status' => 'error',
					'err_message' => esc_html__('You are not allowed to login, please claim if this profile belongs to you.','service-finder'),
					);
			echo json_encode($error);
			exit;
		}
	}else{
	$result = service_finder_sedateUserLogin($_POST['login_user_name'],$_POST['login_password']);
	}
	
	}else{
	$result = service_finder_sedateUserLogin($_POST['login_user_name'],$_POST['login_password']);
	}
	
	if(is_wp_error($result)){
		$pos = strpos($user->get_error_message(), 'Lost your password');
		if (is_int($pos)) {
			$error = explode('<a href',$user->get_error_message());
			$srrmsg = $error[0];
		}else{
			$srrmsg = $user->get_error_message();
		}
		
		
		$error = array(
				'status' => 'error',
				'err_message' => $srrmsg,
				);
		echo json_encode($error);

	}else{
		
		if ( in_array( 'administrator', $user->roles ) ) {
			$redirect = admin_url();
		} elseif(in_array( 'Provider', $user->roles )){
			
			$pageid = (!empty($service_finder_options['login-redirect-provider-option'])) ? $service_finder_options['login-redirect-provider-option'] : '';
			if($pageid != ""){
			if($pageid == 'no' || $pageid == ''){
			$redirect = '';
			}else{
			$redirect = get_permalink($pageid);
			}
			}else{
			$redirect = service_finder_get_url_by_shortcode('[service_finder_my_account]');
			}
			
		} elseif(in_array( 'Customer', $user->roles )){
			
			$pageid = (!empty($service_finder_options['login-redirect-customer-option'])) ? $service_finder_options['login-redirect-customer-option'] : '';
			if($pageid != ""){
			if($pageid == 'no' || $pageid == ''){
			$redirect = '';
			}else{
			$redirect = get_permalink($pageid);
			}
			}else{
			$redirect = service_finder_get_url_by_shortcode('[service_finder_my_account]');
			}
					
		} else{
			$redirect = home_url('/');
		}
		$redirectnonce = (!empty($_POST['redirectnonce'])) ? $_POST['redirectnonce'] : '';
		$login_successfull = (!empty($service_finder_options['login-successfull'])) ? $service_finder_options['login-successfull'] : esc_html__('Login Successful.', 'service-finder');
		
		if($redirectnonce == 'no'){
			$successmsg = $login_successfull;
		}else{
			$successmsg = $login_successfull;
		}
		
		$success = array(
				'status' => 'success',
				'redirect' => $redirect,
				'suc_message' => $successmsg,
				);
		echo json_encode($success);		
	}

exit;
}

/* User Forgot password */
add_action('wp_ajax_forgotpassword', 'service_finder_forgotpassword');
add_action('wp_ajax_nopriv_forgotpassword', 'service_finder_forgotpassword');

function service_finder_forgotpassword(){
	global $service_finder_Errors, $wpdb;

	$result = service_finder_sedateForgotPassword($_POST['fp_user_login']);
	
	$password_reset = (!empty($service_finder_options['password-reset-successfull'])) ? $service_finder_options['password-reset-successfull'] : esc_html__('Password Reset Successful. Check your email address for your new password.', 'service-finder');
	
	if(is_wp_error($result)){
		$error = array(
				'status' => 'error',
				'err_message' => $service_finder_Errors->get_error_message(),
				);
		echo json_encode($error);

	}else{
		$success = array(
				'status' => 'success',
				'suc_message' => $password_reset
				);
		echo json_encode($success);		
	}

exit;
}

/* Reset New password */
add_action('wp_ajax_resetnewpassword', 'service_finder_resetnewpassword');
add_action('wp_ajax_nopriv_resetnewpassword', 'service_finder_resetnewpassword');
function service_finder_resetnewpassword(){
	global $service_finder_Errors, $wpdb;

	$new_pass = (isset($_POST['new_pass'])) ? sanitize_text_field($_POST['new_pass']) : '';
	$confirm_new_pass = (isset($_POST['confirm_new_pass'])) ? sanitize_text_field($_POST['confirm_new_pass']) : '';
	
	$key = (isset($_POST['key'])) ? sanitize_text_field($_POST['key']) : '';
	$login = (isset($_POST['login'])) ? sanitize_text_field($_POST['login']) : '';
	
	if ( empty( $key ) || empty( $login ) ) {
		$error = array(
				'status' => 'error',
				'err_message' => esc_html__( 'The reset link is not valid.', 'service-finder' )
				);
		echo json_encode($error);
		exit;
	}
	
	$user = check_password_reset_key( $key, $login );

	if ( is_wp_error( $user ) ) {

		if ( $user->get_error_code() === 'expired_key' ) {

			$error = array(
					'status' => 'error',
					'err_message' => esc_html__( 'Sorry, that key has expired. Please reset your password again.', 'service-finder' )
					);
			echo json_encode($error);
			exit;

		} else {

			$error = array(
					'status' => 'error',
					'err_message' => esc_html__( 'Sorry, that key does not appear to be valid. Please reset your password again.', 'service-finder' )
					);
			echo json_encode($error);
			exit;

		}

	}

	do_action( 'validate_password_reset', new WP_Error(), $user );

	reset_password( $user, $new_pass );
	
	$success = array(
			'status' => 'success',
			'suc_message' => esc_html__( 'Password reset successfully.', 'service-finder' )
			);
	echo json_encode($success);
	exit;
}

/* Provider Signup via ajax with stripe */
add_action('wp_ajax_signup', 'service_finder_signup');
add_action('wp_ajax_nopriv_signup', 'service_finder_signup');
function service_finder_signup(){
	global $wpdb, $service_finder_Errors, $service_finder_options, $paypal;
	require_once(SERVICE_FINDER_PAYMENT_GATEWAY_DIR.'/stripe/init.php');

$service_finder_options = get_option('service_finder_options');

$creds = array();
$paypalCreds['USER'] = (isset($service_finder_options['paypal-username'])) ? $service_finder_options['paypal-username'] : '';
$paypalCreds['PWD'] = (isset($service_finder_options['paypal-password'])) ? $service_finder_options['paypal-password'] : '';
$paypalCreds['SIGNATURE'] = (isset($service_finder_options['paypal-signatue'])) ? $service_finder_options['paypal-signatue'] : '';
$paypalType = (isset($service_finder_options['paypal-type']) && $service_finder_options['paypal-type'] == 'live') ? '' : 'sandbox.';

$paypalTypeBool = (!empty($paypalType)) ? true : false;

$paypal = new Paypal($paypalCreds,$paypalTypeBool);

	
	$upgrade = false;
	$pay_mode = (isset($_POST['pay_mode'])) ? $_POST['pay_mode'] : '';
	if($pay_mode == 'stripe_upgrade'){
		$upgrade = true;
		$currentRole =  get_user_meta($_POST['user_id'],'provider_role',true);
		$currentPayType = get_user_meta($_POST['user_id'],'pay_type',true);
		if($currentPayType == 'single'){
			$paidAmount =  get_user_meta($_POST['user_id'],'profile_amt',true);
		}
		$userId = $_POST['user_id'];
	} else {
		$userId = service_finder_sedateUserRegistration($_POST);
		$currentPayType = '';
	}
	
	$signup_user_role = (isset($_POST['signup_user_role'])) ? $_POST['signup_user_role'] : '';
	
	if(is_wp_error( $userId )){

		$error = array(
				'status' => 'error',
				'err_message' => $service_finder_Errors->get_error_message()
				);
		echo json_encode($error);

	}elseif($signup_user_role == 'Customer'){
	
		$registerMessages = (!empty($service_finder_options['customer-signup-successfull'])) ? $service_finder_options['customer-signup-successfull'] : esc_html__('Your customer account was created successfully.', 'service-finder');
		
		$pageid = (!empty($service_finder_options['signup-redirect-customer-option'])) ? $service_finder_options['signup-redirect-customer-option'] : '';
		if($pageid == 'no' || $pageid == ''){
		$redirectURL = '';
		}else{
		$redirectURL = add_query_arg( array('signup' => 'success'), get_permalink($pageid) );
		}
								
		$success = array(
					'status' => 'success',
					'redirecturl' => $redirectURL,
					'suc_message' => $registerMessages,
					);
		echo json_encode($success);	
	
	}else{
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
							$token = $_POST['stripeToken'];
							$rolePrice = $service_finder_options['package1-price'];
							$free = false;
							$packageName = $service_finder_options['package1-name'];
							if($service_finder_options['payment-type'] == 'single'){
							$expire_limit = $service_finder_options['package1-expday'];
							}
							if($service_finder_options['payment-type'] == 'single' && $currentPayType == 'single' && $upgrade){
							$price = floatval($service_finder_options['package1-price']) - floatval($paidAmount);							
							}else{
							$price = trim($service_finder_options['package1-price']);								
							}
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
							if($service_finder_options['payment-type'] == 'single' && $currentPayType == 'single' && $upgrade){
							$price = floatval($service_finder_options['package2-price']) - floatval($paidAmount);							
							}else{
							$price = trim($service_finder_options['package2-price']);								
							}
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
							if($service_finder_options['payment-type'] == 'single' && $currentPayType == 'single' && $upgrade){
							$price = floatval($service_finder_options['package3-price']) - floatval($paidAmount);							
							}else{
							$price = trim($service_finder_options['package3-price']);								
							}
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
					$paymentDescription = ($upgrade) ? esc_html__('Upgrade to ','service-finder') . $packageName : $packageName;

					if($upgrade){
						$paymentName .= esc_html__(' Upgrade','service-finder');
					}

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
			try {			
				\Stripe\Stripe::setApiKey($secret_key);
				$subscription_amount = $rolePrice * 100;
				

				$interval = $billingPeriod;
				$interval_count = 1;
					

					$plan = $role;
					$stripe = new \Stripe\StripeClient($secret_key);
					
					if($upgrade && $currentPayType == 'recurring'){
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
									
									$customer = \Stripe\Customer::create(array(
											'card' => $token,
											'email' => $userdata->user_email,
											'description' => $recurringDescriptionFull
										)
									);
									
									$cu = \Stripe\Customer::retrieve($customer->id);
									$getsubs = $cu->subscriptions->create(array("plan" => $plan));
									update_user_meta($userId, 'subscription_id',$getsubs->id);
									update_user_meta($userId, 'stripe_customer_id', $customer->id);
									
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
								
								$customer = \Stripe\Customer::create(array(
										'card' => $token,
										'email' => $userdata->user_email,
										'description' => $recurringDescriptionFull
									)
								);
								
								$cu = \Stripe\Customer::retrieve($customer->id);
								$getsubs = $cu->subscriptions->create(array("plan" => $plan));
								update_user_meta($userId, 'subscription_id',$getsubs->id);
								update_user_meta($userId, 'stripe_customer_id', $customer->id);
								
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
										
										$customer = \Stripe\Customer::create(array(
												'card' => $token,
												'email' => $userdata->user_email,
												'description' => $recurringDescriptionFull
											)
										);
										
										$cu = \Stripe\Customer::retrieve($customer->id);
										$getsubs = $cu->subscriptions->create(array("plan" => $plan));
										update_user_meta($userId, 'subscription_id',$getsubs->id);
										update_user_meta($userId, 'stripe_customer_id', $customer->id);
	
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
								
								if($cusID != ""){
								
								$currentcustomer = \Stripe\Customer::retrieve($cusID);
								$currentcustomer->source = $token;
								$currentcustomer->save();

								$currentcustomer = \Stripe\Customer::retrieve($cusID);
								$subscription = $currentcustomer->subscriptions->retrieve($subID);
								$subscription->plan = $plan;
								$subscription->save();
								update_user_meta($userId, 'subscription_id',$subID);
								update_user_meta($userId, 'stripe_customer_id', $cusID);
								}else{
									$customer = \Stripe\Customer::create(array(
											'card' => $token,
											'email' => $userdata->user_email,
											'description' => $recurringDescriptionFull
										)
									);
									
									$cu = \Stripe\Customer::retrieve($customer->id);
									$getsubs = $cu->subscriptions->create(array("plan" => $plan));
									update_user_meta($userId, 'subscription_id',$getsubs->id);
									update_user_meta($userId, 'stripe_customer_id', $customer->id);
								}
								}
					}else{
								$customer = \Stripe\Customer::create(array(
										'card' => $token,
										'email' => $userdata->user_email,
										'description' => $recurringDescriptionFull
									)
								);
							
								$cu = \Stripe\Customer::retrieve($customer->id);
								$getsubs = $stripe->subscriptions->create(array("plan" => $plan,"customer" => $customer->id));
								
								// For latest Stripe APi Version
								/*$getsubs = \Stripe\Subscription::create([
								  'customer' => $customer->id,
								  'items' => [
									['price' => $plan],
								  ],
								]);*/
								
								update_user_meta($userId, 'subscription_id',$getsubs->id);
								update_user_meta($userId, 'stripe_customer_id', $customer->id);
					}
					
								$user = new WP_User( $userId );
								$user->set_role('Provider');
								
								
								if($upgrade){
								delete_user_meta($userId, 'expire_limit');
								delete_user_meta($userId, 'profile_amt');
								}
								
								
								
								update_user_meta( $userId, 'provider_activation_time', array( 'role' => $role, 'time' => time()) );
								update_user_meta($userId, 'stripe_token', $token);
								update_user_meta( $userId, 'provider_role', $role );
								update_user_meta( $userId, 'pay_type', 'recurring' );
								
								if($upgrade){
								update_user_meta( $userId, 'payment_mode', $_POST['pay_mode'] );
								}else{
								update_user_meta( $userId, 'payment_mode', $_POST['payment_mode'] );
								}
								
								$type = ($upgrade) ? 'upgrade' : 'register';
								update_user_meta($userId, 'recurring_profile_type',$type);
								$roleNum = intval(substr($role, 8));
								$roleName = $service_finder_options['package'.$roleNum.'-name'];
								
								update_user_meta($userId, 'recurring_profile_amt',$rolePrice);
								update_user_meta($userId, 'recurring_profile_period',$service_finder_options['package'.$roleNum.'-billing-period']);
								update_user_meta($userId, 'recurring_profile_desc_full',$recurringDescriptionFull); 
								update_user_meta($userId, 'recurring_profile_desc',$recurringDescription); 
								$paymode = ($upgrade) ? $_POST['pay_mode'] : $_POST['payment_mode'];
								$userInfo = service_finder_getUserInfo($userId);
								$args = array(
										'username' => $userdata->user_login,
										'email' => $userdata->user_email,
										'phone' => $userInfo['phone'],
										'address' => $userInfo['address'],
										'city' => $userInfo['city'],
										'country' => $userInfo['country'],
										'zipcode' => $userInfo['zipcode'],
										'category' => $userInfo['categoryname'],
										'package_name' => $roleName,
										'payment_type' => $paymode
										);
								if($upgrade){
									delete_user_meta($userId, 'current_provider_status');
									service_finder_update_job_limit($userId);
									service_finder_sendUpgradeMailToUser($userdata->user_login,$userdata->user_email,$args);
									service_finder_sendProviderUpgradeEmail($args);
									$registerMessages = (!empty($service_finder_options['provider-upgrade-successfull'])) ? $service_finder_options['provider-upgrade-successfull'] : esc_html__('Your provider account was upgraded', 'service-finder');
								} else {
									service_finder_sendProviderEmail($args);
									service_finder_sendRegMailToUser($userdata->user_login,$userdata->user_email);
									$registerMessages = (!empty($service_finder_options['provider-signup-successfull'])) ? $service_finder_options['provider-signup-successfull'] : esc_html__('Your provider account created', 'service-finder');
								}
								
								$pageid = (!empty($service_finder_options['signup-redirect-option'])) ? $service_finder_options['signup-redirect-option'] : '';
								if($pageid == 'no' || $pageid == ''){
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
								
								
								
				 
							} catch (\Stripe\Error_Card $e) {
								$body = $e->getJsonBody();
								$err  = $body['error'];
							} catch (\Stripe\Error_RateLimit $e) {
								$body = $e->getJsonBody();
								$err  = $body['error'];
							} catch (\Stripe\Error_InvalidRequest $e) {
								$body = $e->getJsonBody();
								$err  = $body['error'];
							} catch (\Stripe\Error_Authentication $e) {
								$body = $e->getJsonBody();
								$err  = $body['error'];
							} catch (\Stripe\Error_ApiConnection $e) {
								$body = $e->getJsonBody();
								$err  = $body['error'];				
							} catch (\Stripe\Error_Base $e) {
								$body = $e->getJsonBody();
								$err  = $body['error'];
							} catch (Exception $e) {
								$body = $e->getJsonBody();
								$err  = $body['error'];
							}
							
							if($err['message'] != ""){
								if(!$upgrade){
								wp_delete_user($userId);
				  			    service_finder_deleteProvidersData($userId);
								}
								$error = array(
										'status' => 'error',
										'err_message' => sprintf( esc_html__('%s', 'service-finder'), $err['message'] )
										);
								echo json_encode($error);
							}

					} else {
						
				 		$signup_user_email = (isset($_POST['signup_user_email'])) ? $_POST['signup_user_email'] : '';
						try {			
						\Stripe\Stripe::setApiKey($secret_key);
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
								
								if($upgrade && $currentPayType == 'recurring'){
								
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
								if($expire_limit > 0){
									update_user_meta($userId, 'expire_limit', $expire_limit);
								}else{
									delete_user_meta($userId, 'expire_limit');
								}
								update_user_meta($userId, 'stripe_token', $token);
								update_user_meta($userId, 'stripe_customer_id', $customer->id);
								update_user_meta( $userId, 'provider_role', $role );
								update_user_meta($userId, 'profile_amt',$rolePrice);
								update_user_meta( $userId, 'pay_type', 'single' );
								$roleNum = intval(substr($role, 8));
								$roleName = $service_finder_options['package'.$roleNum.'-name'];
								if($upgrade){
								update_user_meta( $userId, 'payment_mode', $_POST['pay_mode'] );
								}else{
								update_user_meta( $userId, 'payment_mode', $_POST['payment_mode'] );
								}
								
								$paymode = ($upgrade) ? $_POST['pay_mode'] : $_POST['payment_mode'];
								$userInfo = service_finder_getUserInfo($userId);
								$args = array(
										'username' => (!empty($userdata->user_login)) ? $userdata->user_login : '',
										'email' => (!empty($userdata->user_email)) ? $userdata->user_email : '',
										'address' => (!empty($userInfo['address'])) ? $userInfo['address'] : '',
										'city' => (!empty($userInfo['city'])) ? $userInfo['city'] : '',
										'country' => (!empty($userInfo['country'])) ? $userInfo['country'] : '',
										'zipcode' => (!empty($userInfo['zipcode'])) ? $userInfo['zipcode'] : '',
										'category' => (!empty($userInfo['categoryname'])) ? $userInfo['categoryname'] : '',
										'package_name' => $roleName,
										'payment_type' => $paymode
										);
								if($upgrade){
									delete_user_meta($userId, 'current_provider_status');
									service_finder_update_job_limit($userId);
									service_finder_sendUpgradeMailToUser($userdata->user_login,$userdata->user_email,$args);
									service_finder_sendProviderUpgradeEmail($args);
									$registerMessages = (!empty($service_finder_options['provider-upgrade-successfull'])) ? $service_finder_options['provider-upgrade-successfull'] : esc_html__('Your provider account was upgraded', 'service-finder');
								} else {
									service_finder_sendProviderEmail($args);
									service_finder_sendRegMailToUser($userdata->user_login,$userdata->user_email);
									$registerMessages = (!empty($service_finder_options['provider-signup-successfull'])) ? $service_finder_options['provider-signup-successfull'] : esc_html__('Your provider account created', 'service-finder');
								}
								
								$pageid = (!empty($service_finder_options['signup-redirect-option'])) ? $service_finder_options['signup-redirect-option'] : '';
								if($pageid == 'no' || $pageid == ''){
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
								
											
						} catch (\Stripe\Error_Card $e) {
							$body = $e->getJsonBody();
							$err  = $body['error'];
						} catch (\Stripe\Error_RateLimit $e) {
							$body = $e->getJsonBody();
							$err  = $body['error'];
						} catch (\Stripe\Error_InvalidRequest $e) {
							$body = $e->getJsonBody();
							$err  = $body['error'];
						} catch (\Stripe\Error_Authentication $e) {
							$body = $e->getJsonBody();
							$err  = $body['error'];
						} catch (\Stripe\Error_ApiConnection $e) {
							$body = $e->getJsonBody();
							$err  = $body['error'];				
						} catch (\Stripe\Error_Base $e) {
							$body = $e->getJsonBody();
							$err  = $body['error'];
						} catch (Exception $e) {
							$body = $e->getJsonBody();
							$err  = $body['error'];
						}
						
						if($err['message'] != ""){
							if(!$upgrade){
							wp_delete_user($userId);
				  			service_finder_deleteProvidersData($userId);
							}
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
					if($expire_limit > 0){
						update_user_meta($userId, 'expire_limit', $expire_limit);
					}else{
						delete_user_meta($userId, 'expire_limit');
					}
					update_user_meta( $userId, 'provider_role', $role );
					$userInfo = service_finder_getUserInfo($userId);
					$args = array(
							'username' => $userdata->user_login,
							'email' => $userdata->user_email,
							'phone' => $userInfo['phone'],
							'address' => $userInfo['address'],
							'city' => $userInfo['city'],
							'country' => $userInfo['country'],
							'zipcode' => $userInfo['zipcode'],
							'category' => $userInfo['categoryname'],
							'package_name' => $roleName,
							'payment_type' => 'Free'
							);
					if($upgrade){
						delete_user_meta($userId, 'current_provider_status');
						// upgrade
						service_finder_update_job_limit($userId);
						service_finder_sendUpgradeMailToUser($userdata->user_login,$userdata->user_email,$args);
						service_finder_sendProviderUpgradeEmail($args);
						$registerMessages = (!empty($service_finder_options['provider-upgrade-successfull'])) ? $service_finder_options['provider-upgrade-successfull'] : esc_html__('Your provider account was upgraded', 'service-finder');
					} else {
						service_finder_sendProviderEmail($args);
						service_finder_sendRegMailToUser($userdata->user_login,$userdata->user_email);
						$registerMessages = (!empty($service_finder_options['provider-signup-successfull'])) ? $service_finder_options['provider-signup-successfull'] : esc_html__('Your provider account created', 'service-finder');
					}
					
					$pageid = (!empty($service_finder_options['signup-redirect-option'])) ? $service_finder_options['signup-redirect-option'] : '';
					if($pageid == 'no' || $pageid == ''){
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
	}
	
exit;
}

/* Provider Signup via ajax with wallet */
add_action('wp_ajax_wallet_upgrade', 'service_finder_wallet_upgrade');
add_action('wp_ajax_nopriv_wallet_upgrade', 'service_finder_wallet_upgrade');
function service_finder_wallet_upgrade(){
global $wpdb, $service_finder_Errors, $service_finder_options, $paypal;
$service_finder_options = get_option('service_finder_options');

	$pay_mode = (isset($_POST['pay_mode'])) ? $_POST['pay_mode'] : '';
	$upgrade = true;
		$currentRole =  get_user_meta($_POST['user_id'],'provider_role',true);
		$currentPayType = get_user_meta($_POST['user_id'],'pay_type',true);
		if($currentPayType == 'single'){
			$paidAmount =  get_user_meta($_POST['user_id'],'profile_amt',true);
		}
		$userId = $_POST['user_id'];
	
	$signup_user_role = (isset($_POST['signup_user_role'])) ? $_POST['signup_user_role'] : '';
	
	if(is_wp_error( $userId )){

		$error = array(
				'status' => 'error',
				'err_message' => $service_finder_Errors->get_error_message()
				);
		echo json_encode($error);

	}else{
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
							if($service_finder_options['payment-type'] == 'single' && $currentPayType == 'single' && $upgrade){
							$price = floatval($service_finder_options['package1-price']) - floatval($paidAmount);							
							}else{
							$price = trim($service_finder_options['package1-price']);								
							}
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
							if($service_finder_options['payment-type'] == 'single' && $currentPayType == 'single' && $upgrade){
							$price = floatval($service_finder_options['package2-price']) - floatval($paidAmount);							
							}else{
							$price = trim($service_finder_options['package2-price']);								
							}
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
							if($service_finder_options['payment-type'] == 'single' && $currentPayType == 'single' && $upgrade){
							$price = floatval($service_finder_options['package3-price']) - floatval($paidAmount);							
							}else{
							$price = trim($service_finder_options['package3-price']);								
							}
						}
						break;
					default:
						break;
				}
				
					$walletamount = service_finder_get_wallet_amount($userId);
					
					if(floatval($walletamount) < floatval($price)){
					$error = array(
					'status' => 'error',
					'err_message' => 'insufficient_amount'
					);
					echo json_encode($error);
					exit(0);
					}
				
					$user = new WP_User( $userId );
					$user->set_role('Provider');
					
					$remaining_wallet_amount = floatval($walletamount) - floatval($price); 
					$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('Provider', 'service-finder');
					$args = array(
						'user_id' => $userId,
						'amount' => $price,
						'action' => 'debit',
						'debit_for' => sprintf( esc_html__('Upgrade %s Plan', 'service-finder'), $providerreplacestring ),
						'payment_mode' => 'local',
						'payment_method' => 'wallet',
						'payment_status' => 'completed'
						);
						
					service_finder_add_wallet_history($args);
					
					$cashbackamount = service_finder_cashback_amount('upgrade');
					
					if(floatval($cashbackamount['amount']) > 0){
					$remaining_wallet_amount = floatval($remaining_wallet_amount) + floatval($cashbackamount['amount']);
					
					$args = array(
						'user_id' => $userId,
						'amount' => $cashbackamount['amount'],
						'action' => 'credit',
						'debit_for' => $cashbackamount['description'],
						'payment_mode' => '',
						'payment_method' => '',
						'payment_status' => 'completed'
						);
						
					service_finder_add_wallet_history($args);
					
					}

					update_user_meta($userId,'_sf_wallet_amount',$remaining_wallet_amount);
					
					$userdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$wpdb->users.' WHERE `ID` = %d',$userId));
					$userInfo = service_finder_getUserInfo($userId);
					
					$expire_limit = $service_finder_options['package'.$roleNum.'-expday'];
					
					update_user_meta( $userId, 'payment_mode', 'wallet' );					
					
					update_user_meta($userId, 'recurring_profile_type',$type);
					update_user_meta( $userId, 'provider_role', $role );
					if($expire_limit > 0){
						update_user_meta($userId, 'expire_limit', $expire_limit);
					}else{
						delete_user_meta($userId, 'expire_limit');
					}
					update_user_meta( $userId, 'provider_activation_time', array( 'role' => $role, 'time' => time()) );
					
					$roleNum = intval(substr($role, 8));
					$roleName = (!empty($service_finder_options['package'.$roleNum.'-name'])) ? $service_finder_options['package'.$roleNum.'-name'] : '';
					
					if($roleNum == 0){
						update_user_meta($userId, 'trial_package', 'yes');
					}
					
					$userInfo = service_finder_getUserInfo($userId);
					$paymentstatus = 'wallet';
					
					$args = array(
							'username' => $userdata->user_login,
							'email' => $userdata->user_email,
							'phone' => $userInfo['phone'],
							'address' => $userInfo['address'],
							'city' => $userInfo['city'],
							'country' => $userInfo['country'],
							'zipcode' => $userInfo['zipcode'],
							'package_name' => $roleName,
							'payment_type' => $paymentstatus
							);
					
					if($upgrade){
						delete_user_meta($userId, 'current_provider_status');
						service_finder_update_job_limit($userId);
						service_finder_sendUpgradeMailToUser($userdata->user_login,$userdata->user_email,$args);
						service_finder_sendProviderUpgradeEmail($args);
						// upgrade
						$registerMessages = (!empty($service_finder_options['provider-upgrade-successfull'])) ? $service_finder_options['provider-upgrade-successfull'] : esc_html__('Your provider account was upgraded', 'service-finder');
						
						$pageid = (!empty($service_finder_options['signup-redirect-option'])) ? $service_finder_options['signup-redirect-option'] : '';
						if($pageid == 'no' || $pageid == ''){
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
	}
	
exit;
}

/* Provider Signup via ajax with payulatam */
add_action('wp_ajax_payulatam_signup', 'service_finder_payulatam_signup');
add_action('wp_ajax_nopriv_payulatam_signup', 'service_finder_payulatam_signup');
function service_finder_payulatam_signup(){
	global $wpdb, $service_finder_Errors, $service_finder_options, $paypal;
	require_once(SERVICE_FINDER_PAYMENT_GATEWAY_DIR.'/stripe/init.php');

$service_finder_options = get_option('service_finder_options');

$creds = array();
$paypalCreds['USER'] = (isset($service_finder_options['paypal-username'])) ? $service_finder_options['paypal-username'] : '';
$paypalCreds['PWD'] = (isset($service_finder_options['paypal-password'])) ? $service_finder_options['paypal-password'] : '';
$paypalCreds['SIGNATURE'] = (isset($service_finder_options['paypal-signatue'])) ? $service_finder_options['paypal-signatue'] : '';
$paypalType = (isset($service_finder_options['paypal-type']) && $service_finder_options['paypal-type'] == 'live') ? '' : 'sandbox.';

$paypalTypeBool = (!empty($paypalType)) ? true : false;

$paypal = new Paypal($paypalCreds,$paypalTypeBool);

	
	$upgrade = false;
	$pay_mode = (isset($_POST['pay_mode'])) ? $_POST['pay_mode'] : '';
	if($pay_mode == 'payulatam_upgrade'){
		$upgrade = true;
		$currentRole =  get_user_meta($_POST['user_id'],'provider_role',true);
		$currentPayType = get_user_meta($_POST['user_id'],'pay_type',true);
		if($currentPayType == 'single'){
			$paidAmount =  get_user_meta($_POST['user_id'],'profile_amt',true);
		}
		$userId = $_POST['user_id'];
	} else {
		$userId = service_finder_sedateUserRegistration($_POST);
		$currentPayType = '';
	}
	
	$signup_user_role = (isset($_POST['signup_user_role'])) ? $_POST['signup_user_role'] : '';
	
	if(is_wp_error( $userId )){

		$error = array(
				'status' => 'error',
				'err_message' => $service_finder_Errors->get_error_message()
				);
		echo json_encode($error);

	}else{
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
							$token = $_POST['stripeToken'];
							$rolePrice = $service_finder_options['package1-price'];
							$free = false;
							$packageName = $service_finder_options['package1-name'];
							if($service_finder_options['payment-type'] == 'single'){
							$expire_limit = $service_finder_options['package1-expday'];
							}
							if($service_finder_options['payment-type'] == 'single' && $currentPayType == 'single' && $upgrade){
							$price = floatval($service_finder_options['package1-price']) - floatval($paidAmount);							
							}else{
							$price = trim($service_finder_options['package1-price']);								
							}
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
							if($service_finder_options['payment-type'] == 'single' && $currentPayType == 'single' && $upgrade){
							$price = floatval($service_finder_options['package2-price']) - floatval($paidAmount);							
							}else{
							$price = trim($service_finder_options['package2-price']);								
							}
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
							if($service_finder_options['payment-type'] == 'single' && $currentPayType == 'single' && $upgrade){
							$price = floatval($service_finder_options['package3-price']) - floatval($paidAmount);							
							}else{
							$price = trim($service_finder_options['package3-price']);								
							}
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
					$paymentDescription = ($upgrade) ? esc_html__('Upgrade to ','service-finder') . $packageName : $packageName;

					if($upgrade){
						$paymentName .= esc_html__(' Upgrade','service-finder');
						$reference = 'upgrade_'.$userId.'_'.time();
					}else{
						$reference = 'signup_'.$userId.'_'.time();
					}
					
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
						
						
						if($upgrade && $currentPayType == 'recurring'){
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
						
						if($upgrade && $currentPayType == 'single'){
						delete_user_meta($userId, 'expire_limit');
						delete_user_meta($userId, 'profile_amt');
						} 
						
						update_user_meta( $userId, 'provider_activation_time', array( 'role' => $role, 'time' => time()) );
						update_user_meta( $userId, 'provider_role', $role );
						update_user_meta( $userId, 'pay_type', 'recurring' );
						
						if($upgrade){
						update_user_meta( $userId, 'payment_mode', $_POST['pay_mode'] );
						}else{
						update_user_meta( $userId, 'payment_mode', $_POST['payment_mode'] );
						}
						
						$type = ($upgrade) ? 'upgrade' : 'register';
						update_user_meta($userId, 'recurring_profile_type',$type);
						$roleNum = intval(substr($role, 8));
						$roleName = $service_finder_options['package'.$roleNum.'-name'];
						
						update_user_meta($userId, 'recurring_profile_amt',$rolePrice);
						update_user_meta($userId, 'recurring_profile_period',$service_finder_options['package'.$roleNum.'-billing-period']);
						update_user_meta($userId, 'recurring_profile_desc_full',$recurringDescriptionFull); 
						update_user_meta($userId, 'recurring_profile_desc',$recurringDescription); 
						$paymode = ($upgrade) ? $_POST['pay_mode'] : $_POST['payment_mode'];
						$userInfo = service_finder_getUserInfo($userId);
						$args = array(
								'username' => $userdata->user_login,
								'email' => $userdata->user_email,
								'phone' => $userInfo['phone'],
								'address' => $userInfo['address'],
								'city' => $userInfo['city'],
								'country' => $userInfo['country'],
								'zipcode' => $userInfo['zipcode'],
								'category' => $userInfo['categoryname'],
								'package_name' => $roleName,
								'payment_type' => $paymode
								);
						if($upgrade){
							delete_user_meta($userId, 'current_provider_status');
							service_finder_update_job_limit($userId);
							service_finder_sendUpgradeMailToUser($userdata->user_login,$userdata->user_email,$args);
							service_finder_sendProviderUpgradeEmail($args);
							$registerMessages = (!empty($service_finder_options['provider-upgrade-successfull'])) ? $service_finder_options['provider-upgrade-successfull'] : esc_html__('Your provider account was upgraded', 'service-finder');
						} else {
							service_finder_sendProviderEmail($args);
							service_finder_sendRegMailToUser($userdata->user_login,$userdata->user_email);
							$registerMessages = (!empty($service_finder_options['provider-signup-successfull'])) ? $service_finder_options['provider-signup-successfull'] : esc_html__('Your provider account created', 'service-finder');
						}
						
						$pageid = (!empty($service_finder_options['signup-redirect-option'])) ? $service_finder_options['signup-redirect-option'] : '';
						if($pageid == 'no' || $pageid == ''){
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
							if(!$upgrade){
							wp_delete_user($userId);
				  			service_finder_deleteProvidersData($userId);
							}
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
							//Enter the account�s identifier here
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
								
								if($upgrade && $currentPayType == 'recurring'){
								
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
								if($expire_limit > 0){
									update_user_meta($userId, 'expire_limit', $expire_limit);
								}else{
									delete_user_meta($userId, 'expire_limit');
								}
								update_user_meta( $userId, 'provider_role', $role );
								update_user_meta($userId, 'profile_amt',$rolePrice);
								update_user_meta( $userId, 'pay_type', 'single' );
								$roleNum = intval(substr($role, 8));
								$roleName = $service_finder_options['package'.$roleNum.'-name'];
								if($upgrade){
								update_user_meta( $userId, 'payment_mode', $_POST['pay_mode'] );
								}else{
								update_user_meta( $userId, 'payment_mode', $_POST['payment_mode'] );
								}
								
								$paymode = ($upgrade) ? $_POST['pay_mode'] : $_POST['payment_mode'];
								$userInfo = service_finder_getUserInfo($userId);
								$args = array(
										'username' => (!empty($userdata->user_login)) ? $userdata->user_login : '',
										'email' => (!empty($userdata->user_email)) ? $userdata->user_email : '',
										'address' => (!empty($userInfo['address'])) ? $userInfo['address'] : '',
										'city' => (!empty($userInfo['city'])) ? $userInfo['city'] : '',
										'country' => (!empty($userInfo['country'])) ? $userInfo['country'] : '',
										'zipcode' => (!empty($userInfo['zipcode'])) ? $userInfo['zipcode'] : '',
										'category' => (!empty($userInfo['categoryname'])) ? $userInfo['categoryname'] : '',
										'package_name' => $roleName,
										'payment_type' => $paymode
										);
								if($upgrade){
									delete_user_meta($userId, 'current_provider_status');
									service_finder_update_job_limit($userId);
									service_finder_sendUpgradeMailToUser($userdata->user_login,$userdata->user_email,$args);
									service_finder_sendProviderUpgradeEmail($args);
									$registerMessages = (!empty($service_finder_options['provider-upgrade-successfull'])) ? $service_finder_options['provider-upgrade-successfull'] : esc_html__('Your provider account was upgraded', 'service-finder');
								} else {
									service_finder_sendProviderEmail($args);
									service_finder_sendRegMailToUser($userdata->user_login,$userdata->user_email);
									$registerMessages = (!empty($service_finder_options['provider-signup-successfull'])) ? $service_finder_options['provider-signup-successfull'] : esc_html__('Your provider account created', 'service-finder');
								}
								
								$pageid = (!empty($service_finder_options['signup-redirect-option'])) ? $service_finder_options['signup-redirect-option'] : '';
								if($pageid == 'no' || $pageid == ''){
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
							
							if(!$upgrade){
							wp_delete_user($userId);
				  			service_finder_deleteProvidersData($userId);
							}
							
							$error = array(
									'status' => 'error',
									'err_message' => $msg
									);
							echo json_encode($error);
						}
						

						} catch (Exception $e) {
							if(!$upgrade){
							wp_delete_user($userId);
				  			service_finder_deleteProvidersData($userId);
							}
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
	}
	
exit;
}


/* Provider Signup via ajax with stripe */
add_action('wp_ajax_twocheckout_signup', 'service_finder_twocheckout_signup');
add_action('wp_ajax_nopriv_twocheckout_signup', 'service_finder_twocheckout_signup');

function service_finder_twocheckout_signup(){
global $wpdb, $service_finder_Errors, $service_finder_options, $paypal;
require_once(SERVICE_FINDER_PAYMENT_GATEWAY_DIR.'/stripe/init.php');
require_once(SERVICE_FINDER_PAYMENT_GATEWAY_DIR.'/2checkout/lib/Twocheckout.php');

$token = (!empty($_POST['twocheckouttoken'])) ? esc_html($_POST['twocheckouttoken']) : '';

$service_finder_options = get_option('service_finder_options');

$creds = array();
$paypalCreds['USER'] = (isset($service_finder_options['paypal-username'])) ? $service_finder_options['paypal-username'] : '';
$paypalCreds['PWD'] = (isset($service_finder_options['paypal-password'])) ? $service_finder_options['paypal-password'] : '';
$paypalCreds['SIGNATURE'] = (isset($service_finder_options['paypal-signatue'])) ? $service_finder_options['paypal-signatue'] : '';
$paypalType = (isset($service_finder_options['paypal-type']) && $service_finder_options['paypal-type'] == 'live') ? '' : 'sandbox.';

$paypalTypeBool = (!empty($paypalType)) ? true : false;

$paypal = new Paypal($paypalCreds,$paypalTypeBool);

$upgrade = false;
$pay_mode = (isset($_POST['pay_mode'])) ? $_POST['pay_mode'] : '';
if($pay_mode == 'twocheckout_upgrade'){
	$upgrade = true;
	$currentRole =  get_user_meta($_POST['user_id'],'provider_role',true);
	$currentPayType = get_user_meta($_POST['user_id'],'pay_type',true);
	if($currentPayType == 'single'){
		$paidAmount =  get_user_meta($_POST['user_id'],'profile_amt',true);
	}
	$userId = (isset($_POST['user_id'])) ? $_POST['user_id'] : '';
} else {
	$userId = service_finder_sedateUserRegistration($_POST);
	$currentPayType = '';
}

$signup_user_role = (isset($_POST['signup_user_role'])) ? $_POST['signup_user_role'] : '';

if(is_wp_error( $userId )){

	$error = array(
			'status' => 'error',
			'err_message' => $service_finder_Errors->get_error_message()
			);
	echo json_encode($error);

}else{
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
						if($service_finder_options['payment-type'] == 'single' && $currentPayType == 'single' && $upgrade){
						$price = floatval($service_finder_options['package1-price']) - floatval($paidAmount);							
						}else{
						$price = trim($service_finder_options['package1-price']);								
						}
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
						if($service_finder_options['payment-type'] == 'single' && $currentPayType == 'single' && $upgrade){
						$price = floatval($service_finder_options['package2-price']) - floatval($paidAmount);							
						}else{
						$price = trim($service_finder_options['package2-price']);								
						}
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
						if($service_finder_options['payment-type'] == 'single' && $currentPayType == 'single' && $upgrade){
						$price = floatval($service_finder_options['package3-price']) - floatval($paidAmount);							
						}else{
						$price = trim($service_finder_options['package3-price']);								
						}
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

					if($upgrade){
					$signup_address = $userInfo['address'];
					$signup_city = $userInfo['city'];
					$signup_first_name = $userInfo['fname'];
					$signup_last_name = $userInfo['lname'];
					$signup_state = $userInfo['state'];
					$signup_country = $userInfo['country'];
					$signup_zipcode = $userInfo['zipcode'];
					$signup_user_email = $userInfo['email'];
					$signup_user_phone = $userInfo['phone'];
					}else{
					$signup_address = (isset($_POST['signup_address'])) ? $_POST['signup_address'] : '';
					$signup_city = (isset($_POST['signup_city'])) ? $_POST['signup_city'] : '';
					$signup_first_name = (isset($_POST['signup_first_name'])) ? $_POST['signup_first_name'] : '';
					$signup_last_name = (isset($_POST['signup_last_name'])) ? $_POST['signup_last_name'] : '';
					$signup_state = (isset($_POST['signup_state'])) ? $_POST['signup_state'] : '';
					$signup_country = (isset($_POST['signup_country'])) ? $_POST['signup_country'] : '';
					$signup_zipcode = (isset($_POST['signup_zipcode'])) ? $_POST['signup_zipcode'] : '302020';
					$signup_user_email = (isset($_POST['signup_user_email'])) ? $_POST['signup_user_email'] : '';
					$signup_user_phone = (isset($_POST['phone'])) ? $_POST['phone'] : '';
					}
					
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
				
						if($upgrade && $currentPayType == 'recurring'){
				
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
							
							if($upgrade && $currentPayType == 'single'){
							delete_user_meta($userId, 'expire_limit');
							delete_user_meta($userId, 'profile_amt');
							}
							
							
							
							update_user_meta( $userId, 'provider_activation_time', array( 'role' => $role, 'time' => time()) );
							update_user_meta( $userId, 'provider_role', $role );
							update_user_meta( $userId, 'pay_type', 'recurring' );
							
							if($upgrade){
							update_user_meta( $userId, 'payment_mode', $_POST['pay_mode'] );
							}else{
							update_user_meta( $userId, 'payment_mode', $_POST['payment_mode'] );
							}
							
							$type = ($upgrade) ? 'upgrade' : 'register';
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
							$paymode = ($upgrade) ? $_POST['pay_mode'] : $_POST['payment_mode'];
							$userInfo = service_finder_getUserInfo($userId);
							$args = array(
									'username' => $userdata->user_login,
									'email' => $userdata->user_email,
									'phone' => $userInfo['phone'],
									'address' => $userInfo['address'],
									'city' => $userInfo['city'],
									'country' => $userInfo['country'],
									'zipcode' => $userInfo['zipcode'],
									'category' => $userInfo['categoryname'],
									'package_name' => $roleName,
									'payment_type' => $paymode
									);
							if($upgrade){
								delete_user_meta($userId, 'current_provider_status');
								service_finder_update_job_limit($userId);
								service_finder_sendUpgradeMailToUser($userdata->user_login,$userdata->user_email,$args);
								service_finder_sendProviderUpgradeEmail($args);
								$registerMessages = (!empty($service_finder_options['provider-upgrade-successfull'])) ? $service_finder_options['provider-upgrade-successfull'] : esc_html__('Your provider account was upgraded', 'service-finder');
							} else {
								service_finder_sendProviderEmail($args);
								service_finder_sendRegMailToUser($userdata->user_login,$userdata->user_email);
								$registerMessages = (!empty($service_finder_options['provider-signup-successfull'])) ? $service_finder_options['provider-signup-successfull'] : esc_html__('Your provider account created', 'service-finder');
							}
							
							$pageid = (!empty($service_finder_options['signup-redirect-option'])) ? $service_finder_options['signup-redirect-option'] : '';
							if($pageid == 'no' || $pageid == ''){
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
						if(!$upgrade){
						wp_delete_user($userId);
						service_finder_deleteProvidersData($userId);
						}
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

					if($upgrade){
					$signup_address = $userInfo['address'];
					$signup_city = $userInfo['city'];
					$signup_first_name = $userInfo['fname'];
					$signup_last_name = $userInfo['lname'];
					$signup_state = $userInfo['state'];
					$signup_country = $userInfo['country'];
					$signup_zipcode = ($userInfo['zipcode']) ? $userInfo['zipcode'] : '302020';
					$signup_user_email = $userInfo['email'];
					$signup_user_phone = $userInfo['phone'];
					}else{
					$signup_address = (isset($_POST['signup_address'])) ? $_POST['signup_address'] : '';
					$signup_city = (isset($_POST['signup_city'])) ? $_POST['signup_city'] : '';
					$signup_first_name = (isset($_POST['signup_first_name'])) ? $_POST['signup_first_name'] : '';
					$signup_last_name = (isset($_POST['signup_last_name'])) ? $_POST['signup_last_name'] : '';
					$signup_state = (isset($_POST['signup_state'])) ? $_POST['signup_state'] : '';
					$signup_country = (isset($_POST['signup_country'])) ? $_POST['signup_country'] : '';
					$signup_zipcode = (isset($_POST['signup_zipcode'])) ? $_POST['signup_zipcode'] : '302020';
					$signup_user_email = (isset($_POST['signup_user_email'])) ? $_POST['signup_user_email'] : '';
					$signup_user_phone = (isset($_POST['phone'])) ? $_POST['phone'] : '';
					}
					
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
						
						if($upgrade && $currentPayType == 'recurring'){
						
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
						if($expire_limit > 0){
							update_user_meta($userId, 'expire_limit', $expire_limit);
						}else{
							delete_user_meta($userId, 'expire_limit');
						}
						update_user_meta($userId, 'provider_role', $role );
						update_user_meta($userId, 'profile_amt',$rolePrice);
						update_user_meta( $userId, 'pay_type', 'single' );
						$roleNum = intval(substr($role, 8));
						$roleName = $service_finder_options['package'.$roleNum.'-name'];
						if($upgrade){
						update_user_meta( $userId, 'payment_mode', $_POST['pay_mode'] );
						}else{
						update_user_meta( $userId, 'payment_mode', $_POST['payment_mode'] );
						}
						
						$paymode = ($upgrade) ? $_POST['pay_mode'] : $_POST['payment_mode'];
						$userInfo = service_finder_getUserInfo($userId);
						$args = array(
								'username' => (!empty($userdata->user_login)) ? $userdata->user_login : '',
								'email' => (!empty($userdata->user_email)) ? $userdata->user_email : '',
								'address' => (!empty($userInfo['address'])) ? $userInfo['address'] : '',
								'city' => (!empty($userInfo['city'])) ? $userInfo['city'] : '',
								'country' => (!empty($userInfo['country'])) ? $userInfo['country'] : '',
								'zipcode' => (!empty($userInfo['zipcode'])) ? $userInfo['zipcode'] : '',
								'category' => (!empty($userInfo['categoryname'])) ? $userInfo['categoryname'] : '',
								'package_name' => $roleName,
								'payment_type' => $paymode
								);
						if($upgrade){
							delete_user_meta($userId, 'current_provider_status');
							service_finder_update_job_limit($userId);
							service_finder_sendUpgradeMailToUser($userdata->user_login,$userdata->user_email,$args);
							service_finder_sendProviderUpgradeEmail($args);
							$registerMessages = (!empty($service_finder_options['provider-upgrade-successfull'])) ? $service_finder_options['provider-upgrade-successfull'] : esc_html__('Your provider account was upgraded', 'service-finder');
						} else {
							service_finder_sendProviderEmail($args);
							service_finder_sendRegMailToUser($userdata->user_login,$userdata->user_email);
							$registerMessages = (!empty($service_finder_options['provider-signup-successfull'])) ? $service_finder_options['provider-signup-successfull'] : esc_html__('Your provider account created', 'service-finder');
						}
						
						$pageid = (!empty($service_finder_options['signup-redirect-option'])) ? $service_finder_options['signup-redirect-option'] : '';
						if($pageid == 'no' || $pageid == ''){
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
						if(!$upgrade){
						wp_delete_user($userId);
						service_finder_deleteProvidersData($userId);
						}
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
}
	
exit;
}

/*User Registration Function*/
function service_finder_sedateUserRegistration($arg = array()){
	global $service_finder_Errors, $wpdb, $service_finder_Params, $service_finder_Tables, $service_finder_options;
	$service_finder_Errors = new WP_Error();
	if(!function_exists('get_user_by')) {
		include(ABSPATH . "wp-includes/pluggable.php"); 
	}


//Username already exist
if(username_exists( esc_attr($arg['signup_user_name']) )){

	$service_finder_Errors->add( 'username_exists',esc_html__('ERROR: Username already exist', 'service-finder') );

	return $service_finder_Errors;

}



// Email already exist

if(email_exists( esc_attr($arg['signup_user_email']) )){

	$service_finder_Errors->add( 'email_exists',esc_html__('ERROR: Email already exist', 'service-finder') );

	return $service_finder_Errors;

}

	

	// Registrations disabled

	if (!get_option( 'users_can_register' )){

		$service_finder_Errors->add( 'registrations_disabled',esc_html__('ERROR: User registration is currently not allowed.', 'service-finder') );

		return $service_finder_Errors;

	}
	
	// Check the username

	if ( ! validate_username( $arg['signup_user_name'] ) ) {

		$service_finder_Errors->add( 'invalid_username', esc_html__( 'ERROR: This username is invalid because it uses illegal characters. Please enter a valid username.' , 'service-finder') );

		return $service_finder_Errors;

	}

	
	$sanitized_user_name = sanitize_user( $arg['signup_user_name'] );

	if(service_finder_get_data($service_finder_options,'profileurlby') == 'companyname' && !empty($arg['signup_company_name'])){
		$nicename = sanitize_text_field($arg['signup_company_name']);
	}elseif(service_finder_get_data($service_finder_options,'profileurlby') == 'username' && !empty($arg['signup_user_name'])){
		$nicename = sanitize_text_field($arg['signup_user_name']);
	}else{
		$nicename = sanitize_text_field($arg['signup_first_name']).' '.sanitize_text_field($arg['signup_last_name']);
	}
	
	$fullname = sanitize_text_field($arg['signup_first_name']).' '.sanitize_text_field($arg['signup_last_name']);
	
	//$userId = wp_create_user( esc_attr($sanitized_user_name), esc_attr($arg['signup_password']), esc_attr($arg['signup_user_email']) );
	$userdata = array(
		'user_login'  =>  $sanitized_user_name,
		'user_pass'   =>  $arg['signup_password'],
		'user_email'  =>  $arg['signup_user_email'],
		'user_nicename'  =>  service_finder_create_user_name($nicename),
		'display_name'  =>  $fullname,
		'role'   	  =>  'subscriber'
	);
	$userId = wp_insert_user( $userdata ) ;
	
	$_SESSION['signup_username'] = $sanitized_user_name;
	$_SESSION['signup_password'] = $arg['signup_password'];
	
	global $wp_rewrite;
	$wp_rewrite->author_base = "";

	if ( ! $userId ) {
		$adminemail = get_option( 'admin_email' );
		$service_finder_Errors->add( 'registration_failed', sprintf( esc_html__('ERROR: Couldn&#8217;t register you... please contact the Administrator', 'service-finder'), $adminemail ) );
		return $service_finder_Errors;

	}else{

	$comment_post = array(
        'post_title' => $nicename,
        'post_status' => 'publish',
        'post_type' => 'sf_comment_rating',
		'comment_status' => 'open',
    );

    $postid = wp_insert_post( $comment_post );
	
	update_user_meta($userId, 'comment_post', $postid);

	// set role
	update_user_meta($userId, 'first_name', esc_attr($arg['signup_first_name']));
	update_user_meta($userId, 'last_name', esc_attr($arg['signup_last_name']));

	update_user_meta($userId, 'nickname', esc_attr($sanitized_user_name));

	if($arg['signup_user_role'] == 'Customer'){
		$user = new WP_User( $userId );
		$user->set_role($arg['signup_user_role']);
		
		$userdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$wpdb->users.' WHERE `ID` = %d',$userId));
		$userInfo = service_finder_getUserInfo($userId);
		
		service_finder_sendRegMailToUser($userdata->user_login,$userdata->user_email);
		service_finder_sendCustomerEmail($userdata->user_login,$userdata->user_email);	
		
	}

	if($arg['signup_user_role'] == 'Provider'){
		
		$signup_address = (isset($_POST['signup_address'])) ? $_POST['signup_address'] : '';
		$signup_city = (isset($_POST['signup_city'])) ? esc_html($_POST['signup_city']) : '';
		$signup_country = (isset($_POST['signup_country'])) ? esc_html($_POST['signup_country']) : '';
		
		$full_address = $signup_address.' '.$signup_city.' '.$signup_country;
		
		$address = str_replace(" ","+",$full_address);
		$res = service_finder_getLatLong($address);
		$lat = $res['lat'];
		$lng = $res['lng'];
		
		if($service_finder_options['account-moderation']){
			$adminapproval = 'pending';
		}else{
			$adminapproval = 'approved';
		}
		
		$payment_mode = (isset($arg['payment_mode'])) ? $arg['payment_mode'] : '';
		
		if($payment_mode == 'wired'){
			$accountblocked = 'yes';
		}else{
			$accountblocked = '';
		}

		$upload_overrides = array('test_form' => false);
		// $attachment_id = media_handle_upload( 'profile_image' , $upload_overrides) ?? 5026;
		$attachment_id = media_handle_upload( 'profile_image' , 22) ?? 5026;
		
		if( !is_wp_error( $attachment_id ) ) {
			add_user_meta($userId, 'user_avatar', $attachment_id);
		}
		
		$data = array(

			'wp_user_id' => $userId,

			'admin_moderation' => $adminapproval,

			'avatar_id' => 0,
			
			'account_blocked' => $accountblocked,
								
			'full_name' => esc_attr($arg['signup_user_name']),

			'email' => (!empty($arg['signup_user_email'])) ? esc_attr($arg['signup_user_email']) : '',
			
			'mobile' => (!empty($arg['signup_phone'])) ? esc_attr($arg['signup_phone']) : '',

			'category_id' => (!empty($arg['signup_category'])) ? esc_attr($arg['signup_category']) : '',

			'city' => (!empty($arg['signup_city'])) ? esc_attr($arg['signup_city']) : '',

			'country' => (!empty($arg['signup_country'])) ? esc_attr($arg['signup_country']) : '',

			'bio' => (!empty($arg['bio'])) ? esc_attr($arg['bio']) : '',
			
			'lat' => $lat,
			
			'long' => $lng,

		);

		$wpdb->insert($service_finder_Tables->providers,wp_unslash($data));
		
		if(service_finder_get_data($arg,'signup_city') != '' && service_finder_get_data($arg,'signup_country') != '')
		{
			service_finder_create_city_term(service_finder_get_data($arg,'signup_city'),service_finder_get_data($arg,'signup_country'));
		}
		
		$initialamount = 0;
		update_user_meta($userId,'_sf_wallet_amount',$initialamount);
		
		if(service_finder_availability_method($userId) == 'timeslots')
		{
			service_finder_create_default_timeslots($userId);
		}elseif(service_finder_availability_method($userId) == 'starttime')
		{
			service_finder_create_default_starttime($userId);
		}
		
		$userslots = get_user_meta($userId, 'timeslots', true);
		if($userslots == ""){
			service_finder_set_default_business_hours($userId);
		}
		
		service_finder_set_default_booking_settings($userId);
		
		$get_provider_role = (isset($arg['provider-role'])) ? $arg['provider-role'] : '';
		$role = $get_provider_role;
		if ($role == "package_0" || $role == "package_1" || $role == "package_2" || $role == "package_3"){
		$packageNum = intval(substr($role, 8));
		
		$allowedjobapply = (!empty($service_finder_options['package'.$packageNum.'-job-apply'])) ? $service_finder_options['package'.$packageNum.'-job-apply'] : '';
		
		$period = (!empty($service_finder_options['job-apply-limit-period'])) ? $service_finder_options['job-apply-limit-period'] : '';
		$numberofweekmonth = (!empty($service_finder_options['job-apply-number-of-week-month'])) ? $service_finder_options['job-apply-number-of-week-month'] : 1;
		$numberofperiod = (!empty($service_finder_options['job-apply-number-of-week-month'])) ? $service_finder_options['job-apply-number-of-week-month'] : '';
		
		$startdate = date('Y-m-d h:i:s');
		
		if($period == 'weekly'){
			$freq = 7 * $numberofweekmonth;
			$expiredate = date('Y-m-d h:i:s', strtotime("+".$freq." days"));
		}elseif($period == 'monthly'){
			$freq = 30 * $numberofweekmonth;
			$expiredate = date('Y-m-d h:i:s', strtotime("+".$freq." days"));
		}
		
		$data = array(
				'provider_id' => $userId,
				'free_limits' => $allowedjobapply,
				'available_limits' => $allowedjobapply,
				'membership_date' => $startdate,
				'start_date' => $startdate,
				'expire_date' => $expiredate,
				);
		
		$wpdb->insert($service_finder_Tables->job_limits,wp_unslash($data));
		}
		
		$primarycategory = (!empty($arg['signup_category'])) ? esc_attr($arg['signup_category']) : '';
		update_user_meta($userId,'primary_category',$primarycategory);

		

		$memberData = array(

					'member_name' => esc_attr($arg['signup_first_name']).' '.esc_attr($arg['signup_last_name']),

					'email' => esc_attr($arg['signup_user_email']),

					'admin_wp_id' => esc_attr($userId),

					'is_admin' => 'yes',

					);

	

		$wpdb->insert($service_finder_Tables->team_members,wp_unslash($memberData));

	}elseif($arg['signup_user_role'] == 'Customer'){

		$data = array(

					'wp_user_id' => $userId,

				);

		$wpdb->insert($service_finder_Tables->customers_data,wp_unslash($data));
		
		$initialamount = 0;
		update_user_meta($userId,'_sf_wallet_amount',$initialamount);
		
		$allowedjobapply = (!empty($service_finder_options['default-job-post-limit'])) ? $service_finder_options['default-job-post-limit'] : '';
	
		$period = (!empty($service_finder_options['job-post-limit-period'])) ? $service_finder_options['job-post-limit-period'] : '';
		$numberofweekmonth = (!empty($service_finder_options['job-post-number-of-week-month'])) ? $service_finder_options['job-post-number-of-week-month'] : 1;
		$numberofperiod = (!empty($service_finder_options['job-post-number-of-week-month'])) ? $service_finder_options['job-post-number-of-week-month'] : '';
		
		$startdate = date('Y-m-d h:i:s');
		
		$expiredate = '';
		
		if($period == 'weekly'){
			$freq = 7 * $numberofweekmonth;
			$expiredate = date('Y-m-d h:i:s', strtotime("+".$freq." days"));
		}elseif($period == 'monthly'){
			$freq = 30 * $numberofweekmonth;
			$expiredate = date('Y-m-d h:i:s', strtotime("+".$freq." days"));
		}
		
		$data = array(
				'provider_id' => $userId,
				'free_limits' => $allowedjobapply,
				'available_limits' => $allowedjobapply,
				'membership_date' => $startdate,
				'start_date' => $startdate,
				'expire_date' => $expiredate,
				);
		
		$wpdb->insert($service_finder_Tables->job_limits,wp_unslash($data));

	}
	
	}

	
	return $userId;



}



/*User Login Function*/

function service_finder_sedateUserLogin($user_name = '',$password = ''){

global $user, $service_finder_Tables, $service_finder_options, $wpdb;

$creds = array();

	$creds['user_login'] = $user_name;

	$creds['user_password'] = $password;

	$creds['remember'] = true;

	//$user = wp_signon( $creds, false );
	
	$secure_cookie = is_ssl();

	$secure_cookie = apply_filters('secure_signon_cookie', $secure_cookie, $creds);
	add_filter('authenticate', 'wp_authenticate_cookie', 30, 3);
	
	$user = wp_authenticate($creds['user_login'], $creds['user_password']);
	
	if ( is_wp_error($user) ){

		return $user;

	}else{
		wp_set_auth_cookie($user->ID, $creds["remember"], $secure_cookie);
		do_action('wp_login', $user->user_login, $user);
	
		wp_set_current_user($user->ID);
		
		if(service_finder_getUserRole($user->ID) == 'Customer'){
		
		$row = $wpdb->get_row('SELECT * FROM '.$service_finder_Tables->job_limits.' WHERE `provider_id` = "'.$user->ID.'"');
		
		if(empty($row)){
		$allowedjobapply = (!empty($service_finder_options['default-job-post-limit'])) ? $service_finder_options['default-job-post-limit'] : '';
	
		$period = (!empty($service_finder_options['job-post-limit-period'])) ? $service_finder_options['job-post-limit-period'] : '';
		$numberofweekmonth = (!empty($service_finder_options['job-post-number-of-week-month'])) ? $service_finder_options['job-post-number-of-week-month'] : 1;
		$numberofperiod = (!empty($service_finder_options['job-post-number-of-week-month'])) ? $service_finder_options['job-post-number-of-week-month'] : '';
		
		$startdate = date('Y-m-d h:i:s');
		
		$expiredate = '';
		
		if($period == 'weekly'){
			$freq = 7 * $numberofweekmonth;
			$expiredate = date('Y-m-d h:i:s', strtotime("+".$freq." days"));
		}elseif($period == 'monthly'){
			$freq = 30 * $numberofweekmonth;
			$expiredate = date('Y-m-d h:i:s', strtotime("+".$freq." days"));
		}
		
		$data = array(
				'provider_id' => $user->ID,
				'free_limits' => $allowedjobapply,
				'available_limits' => $allowedjobapply,
				'membership_date' => $startdate,
				'start_date' => $startdate,
				'expire_date' => $expiredate,
				);
		
		$wpdb->insert($service_finder_Tables->job_limits,wp_unslash($data));
		}
		}

		return $user->ID;

	}	

}

/*Forgot Password Function*/

function service_finder_sedateForgotPassword($user_login = ''){
global $service_finder_Errors, $wpdb, $service_finder_Params, $service_finder_Tables, $service_finder_options;
$service_finder_Errors = new WP_Error();

$success = '';
		
        if(  email_exists(trim($user_login)) || username_exists( trim($user_login) )) {
			
        	
			
            // lets generate our new password
            //$random_password = wp_generate_password( 12, false );
            
            // Get user data by field and data, other field are ID, slug, slug and login
            if(email_exists(trim($user_login))){
			$user = get_user_by( 'email', $user_login );
			}elseif(username_exists( trim($user_login) )){
			$user = get_user_by( 'login', $user_login );
			}
			
            
            /*$update_user = wp_update_user( array (
                    'ID' => $user->ID, 
                    'user_pass' => $random_password
                )
            );*/
			
			$key = get_password_reset_key( $user );

			if ( is_wp_error( $key ) ) {
				$service_finder_Errors->add( 'username_exist', esc_html__( 'key not found!' , 'service-finder') );
				return $service_finder_Errors;
			}else{
				if($service_finder_options['password-reset-mail'] != ""){
					$message = $service_finder_options['password-reset-mail'];
				}else{
					$message = 'Someone requested that the password be reset for the following account:<br/>
					Username: %USERNAME%<br/>
					Email: %EMAIL%<br/>
					If this was a mistake, just ignore this email and nothing will happen.<br/>
					To reset your password, visit the following address:<br/>
					%RESETLINK%';
				}
				
				$reset_url = add_query_arg( array('sfresetpass' => true,'sfrp_action' => 'rp','key' => $key,'login' => rawurlencode( $user->user_login )), service_finder_get_url_by_shortcode('[service_finder_forgot_password') );
				
				$reset_link = '<a href="' . $reset_url . '">' . $reset_url . '</a>';
				
				$tokens = array('%USERNAME%','%EMAIL%','%RESETLINK%');
				$replacements = array($user->user_login,$user->user_email,$reset_link);
				$msg_body = str_replace($tokens,$replacements,$message);
				if($service_finder_options['password-reset-subject'] != ""){
					$msg_subject = $service_finder_options['password-reset-subject'];
				}else{
					$msg_subject = esc_html__('Account Password Reset', 'service-finder');
				}
				
				if(service_finder_wpmailer($user->user_email,$msg_subject,$msg_body)) {
	
					return true;
					
				}
			}
           
			
        } else {
			$service_finder_Errors->add( 'username_exist', esc_html__( 'ERROR: There is no user registered with that username/email address.' , 'service-finder') );
			return $service_finder_Errors;
		}
        
}

/*Send Registration mail to user (Customer & Provider both)*/
function service_finder_sendRegMailToUser($username = '',$email = '',$invoiceid = ''){
global $wpdb, $service_finder_Tables, $service_finder_options;			
			
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
			
			$userobj = get_user_by( 'email', $email );
			$newuserid = $userobj->ID;
			if(service_finder_UserRole($newuserid) == 'Customer'){
			
				if($service_finder_options['send-to-user'] != ""){
					$message .= $service_finder_options['send-to-user'];
				}else{
					$message .= 'Hello %USERNAME%,
	
								Congratulations, You have successfully signed up with us.
								
								Your Login Details are following:
								
								Username: %USERNAME%
								
								Email: %EMAIL%';
				}
			
			}else{
			
				if($service_finder_options['send-to-provider'] != ""){
					$message .= $service_finder_options['send-to-provider'];
				}else{
					$message .= 'Hello %USERNAME%,
	
								Congratulations, You have successfully signed up with us.
								
								Your Login Details are following:
								
								Username: %USERNAME%
								
								Email: %EMAIL%';
				}
			
			}
			
			$user = get_user_by('login',$username);
			$uid = $user->ID;
			$first_name = get_user_meta($uid,'first_name',true);
			$last_name = get_user_meta($uid,'last_name',true);
			
			$tokens = array('%USERNAME%','%FIRSTNAME%','%LASTNAME%','%EMAIL%');
			$replacements = array($username,$first_name,$last_name,$email);
			$msg_body = str_replace($tokens,$replacements,$message);
			
			if(service_finder_UserRole($newuserid) == 'Customer'){
				if($service_finder_options['send-to-user-subject'] != ""){
					$msg_subject = $service_finder_options['send-to-user-subject'];
				}else{
					$msg_subject = esc_html__('User Registration', 'service-finder');
				}
			}else{
				if($service_finder_options['send-to-provider-subject'] != ""){
					$msg_subject = $service_finder_options['send-to-provider-subject'];
				}else{
					$msg_subject = esc_html__('User Registration', 'service-finder');
				}			
			}
			
				
			$loginaftersignup = (!empty($service_finder_options['login-after-signup'])) ? $service_finder_options['login-after-signup'] : '';
			
			if($loginaftersignup){
			$result = do_action('service_finder_login_after_signup',service_finder_get_data($_SESSION,'signup_username'),service_finder_get_data($_SESSION,'signup_password'));
			if(is_wp_error($result)){
				$pos = strpos($user->get_error_message(), 'Lost your password');
				if (is_int($pos)) {
					$error = explode('<a href',$user->get_error_message());
					$srrmsg = $error[0];
				}else{
					$srrmsg = $user->get_error_message();
				}
				
				
				$error = array(
						'status' => 'error',
						'err_message' => $srrmsg,
						);
				return json_encode($error);
		
			}
			}
			
			if(service_finder_wpmailer($email,$msg_subject,$msg_body)) {
				
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

/*Send Customer Registration mail to admin*/
function service_finder_sendCustomerEmail($username = '',$email = ''){
global $wpdb, $service_finder_Tables, $service_finder_options;			
			
			$admin_email = get_option( 'admin_email' );
			if($service_finder_options['customer-to-admin'] != ""){
				$message = $service_finder_options['customer-to-admin'];
			}else{
				$message = 'Hello Admin,

New customer have been signed up with us.

Customer Details are:

Username: %USERNAME%

Email: %EMAIL%';
			}
			
			$tokens = array('%USERNAME%','%EMAIL%');
			$replacements = array($username,$email);
			$msg_body = str_replace($tokens,$replacements,$message);
			if($service_finder_options['customer-to-admin-subject'] != ""){
				$msg_subject = $service_finder_options['customer-to-admin-subject'];
			}else{
				$msg_subject = esc_html__('User Registration', 'service-finder');
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

/*Send Provider Registration mail to admin*/
function service_finder_sendProviderEmail($args = array(),$invoiceid = ''){
global $wpdb, $service_finder_Tables, $service_finder_options;			
			
			$admin_email = get_option( 'admin_email' );
			
			if($invoiceid != ''){
			$message = 'Invoice ID:'.$invoiceid;
			}else{
			$message = '';
			}
			
			if($service_finder_options['provider-to-admin'] != ""){
				$message .= $service_finder_options['provider-to-admin'];
			}else{
				$message .= 'Hello Admin,

							New provider have been signed up with us.
							
							Provider Details are:
							
							Username: %USERNAME%
							
							Email: %EMAIL%
							
							Phone: %PROVIDERPHONE%
							
							Address: %ADDRESS%
							
							City: %CITY%
							
							Country: %COUNTRY%
							
							Postal Code: %ZIPCODE%
							
							Category: %CATEGORY%
							
							Package Name: %PACKAGENAME%
							
							Payment Type: %PAYMENTTYPE%';
			}
			
			$tokens = array('%USERNAME%','%EMAIL%','%ADDRESS%','%CITY%','%COUNTRY%','%ZIPCODE%','%CATEGORY%','%PACKAGENAME%','%PAYMENTTYPE%','%PROVIDERPHONE%');
			$replacements = array($args['username'],$args['email'],$args['address'],$args['city'],$args['country'],$args['zipcode'],$args['category'],$args['package_name'],$args['payment_type'],$args['phone']);
			$msg_body = str_replace($tokens,$replacements,$message);
			if($service_finder_options['provider-to-admin-subject'] != ""){
				$msg_subject = $service_finder_options['provider-to-admin-subject'];
			}else{
				$msg_subject = esc_html__('User Registration', 'service-finder');
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

/*Send Upgrade mail to user (Customer & Provider both)*/
function service_finder_sendUpgradeMailToUser($username = '',$email = '',$args = array()){
global $wpdb, $service_finder_Tables, $service_finder_options;			
			
			if($service_finder_options['upgrade-send-to-provider'] != ""){
				$message = $service_finder_options['upgrade-send-to-provider'];
			}else{
				$message = 'Hello %USERNAME%,

							Congratulations, Your account have been upgraded successfully.
							
							Your Account Details are following:
							
							Username: %USERNAME%
							
							Email: %EMAIL%
							
							Package Name: %PACKAGENAME%
							
							Payment Type: %PAYMENTTYPE%';
			}
			
			if($args['payment_type'] == 'stripe_upgrade'){
			$paytype = 'Stripe';
			}elseif($args['payment_type'] == 'paypal_upgrade'){
			$paytype = 'Paypal';
			}else{
			$paytype = $args['payment_type'];
			}
			
			$tokens = array('%USERNAME%','%EMAIL%','%PACKAGENAME%','%PAYMENTTYPE%');
			$replacements = array($username,$email,$args['package_name'],$paytype);
			$msg_body = str_replace($tokens,$replacements,$message);

			if($service_finder_options['upgrade-send-to-provider-subject'] != ""){
				$msg_subject = $service_finder_options['upgrade-send-to-provider-subject'];
			}else{
				$msg_subject = esc_html__('User Upgrade Notification', 'service-finder');
			}
			
			if(service_finder_wpmailer($email,$msg_subject,$msg_body)) {

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


/*Send Provider Upgrade mail to admin*/
function service_finder_sendProviderUpgradeEmail($args = array()){
global $wpdb, $service_finder_Tables, $service_finder_options;			
			
			$admin_email = get_option( 'admin_email' );
			
			if($service_finder_options['upgrade-provider-to-admin'] != ""){
				$message = $service_finder_options['upgrade-provider-to-admin'];
			}else{
				$message = 'Hello Admin,

							Provider have upgraded their account
							
							Provider Details are:
							
							Username: %USERNAME%
							
							Email: %EMAIL%
							
							Address: %ADDRESS%
							
							City: %CITY%
							
							Country: %COUNTRY%
							
							Postal Code: %ZIPCODE%
							
							Category: %CATEGORY%
							
							Package Name: %PACKAGENAME%
							
							Payment Type: %PAYMENTTYPE%';
			}				

			if($args['payment_type'] == 'stripe_upgrade'){
			$paytype = 'Stripe';
			}elseif($args['payment_type'] == 'paypal_upgrade'){
			$paytype = 'Paypal';
			}else{
			$paytype = $args['payment_type'];
			}
			
			$tokens = array('%USERNAME%','%EMAIL%','%ADDRESS%','%CITY%','%COUNTRY%','%ZIPCODE%','%CATEGORY%','%PACKAGENAME%','%PAYMENTTYPE%');
			$replacements = array($args['username'],$args['email'],$args['address'],$args['city'],$args['country'],$args['zipcode'],$args['category'],$args['package_name'],$paytype);
			$msg_body = str_replace($tokens,$replacements,$message);
			
			if($service_finder_options['upgrade-provider-to-admin-subject'] != ""){
				$msg_subject = $service_finder_options['upgrade-provider-to-admin-subject'];
			}else{
				$msg_subject = esc_html__('User Upgrade Notification', 'service-finder');
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

/*Send Upgrade mail to provider for wire transfer*/
function service_finder_sendWiredUpgradeMailToProvider($username = '',$email = '',$args = array()){
global $wpdb, $service_finder_Tables, $service_finder_options;			
			
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
			
			if($service_finder_options['upgrade-send-to-provider-wired'] != ""){
				$message .= $service_finder_options['upgrade-send-to-provider-wired'];
			}else{
				$message .= 'Hello %USERNAME%,

							Your account will be upgrad after payment transfer via wire transfer.
							
							Your Account Details are following:
							
							Username: %USERNAME%
							
							Email: %EMAIL%
							
							Package Name: %PACKAGENAME%
							
							Payment Type: %PAYMENTTYPE%';
			}
			
			$paytype = $args['payment_type'];
			
			$tokens = array('%USERNAME%','%EMAIL%','%PACKAGENAME%','%PAYMENTTYPE%');
			$replacements = array($username,$email,$args['package_name'],$paytype);
			$msg_body = str_replace($tokens,$replacements,$message);

			if($service_finder_options['upgrade-send-to-provider-subject-wired'] != ""){
				$msg_subject = $service_finder_options['upgrade-send-to-provider-subject-wired'];
			}else{
				$msg_subject = esc_html__('User Upgrade Notification', 'service-finder');
			}
			
			if(service_finder_wpmailer($email,$msg_subject,$msg_body)) {

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


/*Send Provider Upgrade mail to admin for wire transfer*/
function service_finder_sendProviderWiredUpgradeEmail($args = array(),$invoiceid = ''){
global $wpdb, $service_finder_Tables, $service_finder_options;			
			
			$admin_email = get_option( 'admin_email' );
			
			if($invoiceid != ''){
			$message = 'Invoice ID:'.$invoiceid;
			}else{
			$message = '';
			}
			
			if($service_finder_options['upgrade-provider-to-admin-wired'] != ""){
				$message .= $service_finder_options['upgrade-provider-to-admin-wired'];
			}else{
				$message .= 'Hello Admin,

							Provider have upgraded their account
							
							Provider Details are:
							
							Username: %USERNAME%
							
							Email: %EMAIL%
							
							Address: %ADDRESS%
							
							City: %CITY%
							
							Country: %COUNTRY%
							
							Postal Code: %ZIPCODE%
							
							Category: %CATEGORY%
							
							Package Name: %PACKAGENAME%
							
							Payment Type: %PAYMENTTYPE%';
			}				

			$paytype = $args['payment_type'];
			
			$tokens = array('%USERNAME%','%EMAIL%','%ADDRESS%','%CITY%','%COUNTRY%','%ZIPCODE%','%CATEGORY%','%PACKAGENAME%','%PAYMENTTYPE%');
			$replacements = array($args['username'],$args['email'],$args['address'],$args['city'],$args['country'],$args['zipcode'],$args['category'],$args['package_name'],$paytype);
			$msg_body = str_replace($tokens,$replacements,$message);
			
			if($service_finder_options['upgrade-provider-to-admin-subject-wired'] != ""){
				$msg_subject = $service_finder_options['upgrade-provider-to-admin-subject-wired'];
			}else{
				$msg_subject = esc_html__('User Upgrade Notification', 'service-finder');
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