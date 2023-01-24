<?php

if (!defined('ABSPATH')) {
    exit;
}

class service_finder_payment_masspay{

    private $massapi_username;
    private $massapi_password;
    private $massapi_signature;
    private $test_mode = false;
    private $reciver_email;
    private $massapi_endpoint;
	public  $message;
	private $providerid;
	private $bookingid;
	private $commision_amount;
	private $payout_type;
	private $short_msg;
	private $long_msg;

    public function __construct() {
		global $service_finder_options;
		$masspaypayoutenable = (isset($service_finder_options['masspay-payout-enable'])) ? $service_finder_options['masspay-payout-enable'] : false;
		$masspayapiusername = (!empty($service_finder_options['masspay-api-username'])) ? $service_finder_options['masspay-api-username'] : '';
		$masspayapipassword = (!empty($service_finder_options['masspay-api-password'])) ? $service_finder_options['masspay-api-password'] : '';
		$masspayapisignature = (!empty($service_finder_options['masspay-api-signature'])) ? $service_finder_options['masspay-api-signature'] : '';
		$masspaytestmode = (isset($service_finder_options['masspay-test-mode'])) ? $service_finder_options['masspay-test-mode'] : true;
		$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('Provider', 'service-finder');
        
        $this->massapi_username = $masspayapiusername;
        $this->massapi_password = $masspayapipassword;
        $this->massapi_signature = $masspayapisignature;

        $this->enabled = $masspaypayoutenable;

        $this->massapi_endpoint = 'https://api-3t.paypal.com/nvp';
        if ($masspaytestmode == true) {
            $this->test_mode = true;
            $this->massapi_endpoint = 'https://api-3t.sandbox.paypal.com/nvp';
        }
    }
    
    public function service_finder_process_payment($args = array()) {
        //$this->vendor = $vendor;
        //$this->commissions = $commissions;
		
		$providerid = (!empty($args['providerid'])) ? $args['providerid'] : 0;
		$payoutamount = (!empty($args['payoutamount'])) ? $args['payoutamount'] : 0;
		$payouttype = (!empty($args['payouttype'])) ? $args['payouttype'] : 'auto';
		
        $this->currency = service_finder_currencycode();
		$this->providerid = $providerid;
		$this->bookingid = $bookingid;
        $this->reciver_email = service_finder_get_provider_paypal_email($providerid);
		$this->commision_amount = $payoutamount;
		$this->payout_type = $payouttype;
		
        if ($this->service_finder_validate_request()) {
            $paypal_response = $this->service_finder_process_paypal_masspay();

            if ($paypal_response) {
                $this->service_finder_record_transaction($paypal_response);
                if ($this->transaction_id) {
                    if($this->payout_type == 'manual')
					{
						return array('message' => esc_html__('New transaction has been initiated', 'service-finder'), 'type' => 'success', 'transaction_id' => $this->transaction_id);
					}else{
						return true;
					}
                }
            } else {
                if($this->payout_type == 'manual')
				{
					return array('type' => 'fail','short_msg' => $this->short_msg, 'long_msg' => $this->long_msg, 'message' => $this->long_msg);
				}else
				{
					return false;				
				}
            }
        } else {
            return $this->message;
        }
    }

    public function service_finder_validate_request() {
        if ($this->enabled != true) {
            $this->message = array('message' => esc_html__('Masspay payment method is not enable.', 'service-finder'), 'type' => 'error');
			$validation_message = esc_html__('Masspay payment method is not enable.', 'service-finder');
			$this->service_finder_payout_validation_mail($validation_message,1);
            return false;
        } else if (!$this->massapi_username || !$this->massapi_password || !$this->massapi_signature) {
            $this->message = array('message' => esc_html__('Paypal masspay setting is not configured properly please configure.', 'service-finder'), 'type' => 'error');
			$validation_message = esc_html__('Paypal masspay setting is not configured properly please configure it first', 'service-finder');
			$this->service_finder_payout_validation_mail($validation_message,1);
            return false;
        } else if (!$this->reciver_email) {
           	
		    $this->message = array('message' => sprintf(esc_html__('Please update %s PayPal email to receive payout', 'service-finder'),$providerreplacestring), 'type' => 'error');
			
			$validation_message = esc_html__('Please update paypal email to receive payout', 'service-finder');
			$this->service_finder_payout_validation_mail($validation_message,$this->providerid);
            return false;
        }else{
			return true;
		}
    }

    private function service_finder_process_paypal_masspay() {
        
		$nvpheader = "&PWD=" . urlencode($this->massapi_password) . "&USER=" . urlencode($this->massapi_username) . "&SIGNATURE=" . urlencode($this->massapi_signature);
		
        $amount_to_pay = round($this->commision_amount, 2);
        
		$note = sprintf(__('Total payout amount at %2$s on %3$s', 'service-finder'), date('H:i:s'), date('d-m-Y'));
		
        $nvpStr = '&L_EMAIL0=' . urlencode($this->reciver_email) . '&L_Amt0=' . urlencode($amount_to_pay) . '&L_UNIQUEID0=' . urlencode($this->providerid) . '&L_NOTE0=' . urlencode($note) . '&EMAILSUBJECT=' . urlencode('You have money!') . '&RECEIVERTYPE=' . urlencode('EmailAddress') . '&CURRENCYCODE=' . urlencode($this->currency);
		
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->massapi_endpoint);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        $nvpStr = $nvpheader . $nvpStr;
        $nvpStr = "&VERSION=" . urlencode(90) . $nvpStr;
        $nvpreq = "METHOD=" . urlencode('MassPay') . $nvpStr;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
        $response = curl_exec($ch);
        $nvpResArray = $this->service_finder_deformatNVP($response);
        curl_close($ch);
        $ack = strtoupper($nvpResArray["ACK"]);
        if ($ack == "SUCCESS" || $ack == "SuccessWithWarning") {
            return $nvpResArray;
        } else {
            //print_r($nvpResArray);
			$this->service_finder_record_failed_transaction($nvpResArray);
			/*doProductVendorLOG(json_encode($nvpResArray));
            if (isset($nvpResArray['L_LONGMESSAGE0'])) {
                $this->add_commission_note($this->commissions, 'Error: ' . $nvpResArray['L_LONGMESSAGE0']);
            }*/
			return false;
            
        }
    }

    public function service_finder_deformatNVP($nvpstr) {
        $intial = 0;
        $nvpArray = array();
        while (strlen($nvpstr)) {
            //postion of Key
            $keypos = strpos($nvpstr, '=');
            //position of value
            $valuepos = strpos($nvpstr, '&') ? strpos($nvpstr, '&') : strlen($nvpstr);

            /* getting the Key and Value values and storing in a Associative Array */
            $keyval = substr($nvpstr, $intial, $keypos);
            $valval = substr($nvpstr, $keypos + 1, $valuepos - $keypos - 1);
            //decoding the respose
            $nvpArray[urldecode($keyval)] = urldecode($valval);
            $nvpstr = substr($nvpstr, $valuepos + 1, strlen($nvpstr));
        }
        return $nvpArray;
    }
	
	public function service_finder_record_transaction($args = array()) {
        global $wpdb;
	   
        if($this->payout_type == 'manual')
		{
			$transaction_post = array(
				'post_type' => 'payout_transaction',
				'post_title' => sprintf(esc_html__('Manual Payout to %s', 'service-finder'), service_finder_getProviderName($this->providerid) ),
				'post_status' => 'completed',
				'ping_status' => 'closed',
				'post_author' => $this->providerid
			);
			$this->transaction_id = wp_insert_post($transaction_post);
		}
		
        if (!is_wp_error($this->transaction_id) && $this->transaction_id) {
			update_post_meta($this->transaction_id, 'transaction_mode', 'paypal_masspay');
			update_post_meta($this->transaction_id, 'payout_amount', $this->commision_amount);
			update_post_meta($this->transaction_id, 'paid_date', date("Y-m-d H:i:s"));
			update_post_meta($this->transaction_id, 'payout_type', $this->payout_type);
			update_post_meta($this->transaction_id, 'bookingid', $this->bookingid);
			
			$this->short_msg = (!empty($args['L_SHORTMESSAGE0'])) ? $args['L_SHORTMESSAGE0'] : '';
			$this->long_msg = (!empty($args['L_LONGMESSAGE0'])) ? $args['L_LONGMESSAGE0'] : '';
			
			update_post_meta($this->transaction_id, 'L_SHORTMESSAGE', $this->short_msg);
			update_post_meta($this->transaction_id, 'L_LONGMESSAGE', $this->long_msg);
			
			$this->service_finder_payout_transaction_mail_to_provider('success',$this->providerid,$this->commision_amount);
			$this->service_finder_payout_transaction_mail_to_admin('success',$this->providerid,$this->commision_amount);
			
        }
    
    }
	
	public function service_finder_record_failed_transaction($args = array()) {
        global $wpdb;
	   
        if($this->payout_type == 'manual')
		{
			$transaction_post = array(
				'post_type' => 'payout_transaction',
				'post_title' => sprintf(esc_html__('Manual Payout to %s has been failed', 'service-finder'), service_finder_getProviderName($this->providerid) ),
				'post_status' => 'payout-failed',
				'ping_status' => 'closed',
				'post_author' => $this->providerid
			);
			$this->transaction_id = wp_insert_post($transaction_post);
		}
		
        if (!is_wp_error($this->transaction_id) && $this->transaction_id) {
			
			$this->short_msg = (!empty($args['L_SHORTMESSAGE0'])) ? $args['L_SHORTMESSAGE0'] : '';
			$this->long_msg = (!empty($args['L_LONGMESSAGE0'])) ? $args['L_LONGMESSAGE0'] : '';
			update_post_meta($this->transaction_id, 'payout_type', $this->payout_type);
			
			update_post_meta($this->transaction_id, 'L_SHORTMESSAGE', $this->short_msg);
			update_post_meta($this->transaction_id, 'L_LONGMESSAGE', $this->long_msg);
			update_post_meta($this->transaction_id, 'payout_amount', $this->commision_amount);
			update_post_meta($this->transaction_id, 'bookingid', $this->bookingid);
			
			$this->service_finder_payout_transaction_mail_to_provider('fail',$this->providerid,$this->commision_amount,$this->short_msg);
			$this->service_finder_payout_transaction_mail_to_admin('fail',$this->providerid,$this->commision_amount,$this->short_msg);
        }
    
    }
	
	/*Process payout validation mail*/
	public function service_finder_payout_validation_mail( $message = '', $user_id = 0 ) {
		
		if($user_id == 1)
		{
		$adminemail = get_option( 'admin_email' );
	
		if($service_finder_options['masspay-config-to-admin-subject'] != ""){
			$subject = $service_finder_options['masspay-config-to-admin-subject'];
		}else{
			$subject = esc_html__('Payout Configuration Failed', 'service-finder');
		}
		
		if(!empty($service_finder_options['masspay-config-to-admin'])){
			$message = $service_finder_options['masspay-config-to-admin'];
		}else{
			$message = 'Hey Admin!
					  Please configure paypal masspay settings for payout. Following is validation message from payout settings.
					  %VALIDATION_MESSAGE%';
		}
		
		$tokens = array('%VALIDATION_MESSAGE%','%PROVIDERNAME%');
		
		$replacements = array($message,service_finder_getProviderName($user_id));
		
		$msg_body = str_replace($tokens,$replacements,$message);
		
		service_finder_wpmailer($adminemail,$subject,$msg_body);
		
		}else{
		
		if($service_finder_options['masspay-config-to-provider-subject'] != ""){
			$subject = $service_finder_options['masspay-config-to-provider-subject'];
		}else{
			$subject = esc_html__('Payout Failed', 'service-finder');
		}
		
		if(!empty($service_finder_options['masspay-config-to-provider'])){
			$message = $service_finder_options['masspay-config-to-provider'];
		}else{
			$message = 'Hey %PROVIDERNAME%!
					  Payout failed because of you did not set paypal email. Following are the validation message during autometic payout
					  %VALIDATION_MESSAGE%';
		}
		
		$tokens = array('%VALIDATION_MESSAGE%','%PROVIDERNAME%');
		
		$replacements = array($message,service_finder_getProviderName($user_id));
		
		$msg_body = str_replace($tokens,$replacements,$message);
		
		service_finder_wpmailer(service_finder_getProviderEmail($user_id),$subject,$msg_body);
		}
	}
	
	/*Process payout transaction mail to provider*/
	public function service_finder_payout_transaction_mail_to_provider( $status = '', $user_id = 0, $amount = 0, $failedmessage = '' ) {
		
		if($status == 'success')
		{
		if($service_finder_options['masspay-payout-transfer-success-to-provider-subject'] != ""){
			$subject = $service_finder_options['masspay-payout-transfer-success-to-provider-subject'];
		}else{
			$subject = esc_html__('Payout Transfered Succeed', 'service-finder');
		}
		
		if(!empty($service_finder_options['masspay-payout-transfer-success-to-provider'])){
			$message = $service_finder_options['masspay-payout-transfer-success-to-provider'];
		}else{
			$message = 'Hey %PROVIDERNAME%!
					  Payout transferred successfully. Payout amount was %PAYOUTAMOUNT%';
		}
		
		$tokens = array('%PAYOUTAMOUNT%','%PROVIDERNAME%');
		
		$replacements = array($amount,service_finder_getProviderName($user_id));
		}else
		{
		if($service_finder_options['masspay-payout-transfer-fail-to-provider-subject'] != ""){
			$subject = $service_finder_options['masspay-payout-transfer-fail-to-provider-subject'];
		}else{
			$subject = esc_html__('Payout Transfered Failed', 'service-finder');
		}
		
		if(!empty($service_finder_options['masspay-payout-transfer-fail-to-provider'])){
			$message = $service_finder_options['masspay-payout-transfer-fail-to-provider'];
		}else{
			$message = 'Hey %PROVIDERNAME%!
					  Payout transferred was failed. Payout amount was %PAYOUTAMOUNT%';
		}
		
		$tokens = array('%PAYOUTAMOUNT%','%PROVIDERNAME%','%FAILEDREASON%');
		
		$replacements = array($amount,service_finder_getProviderName($user_id),$failedmessage);
		}
		$msg_body = str_replace($tokens,$replacements,$message);
		
		service_finder_wpmailer(service_finder_getProviderEmail($user_id),$subject,$msg_body);
	}
	
	/*Process payout transaction mail to admin*/
	public function service_finder_payout_transaction_mail_to_admin( $status = '', $user_id = 0, $amount = 0, $failedmessage = '' ) {
		
		if($status == 'success')
		{
		if($service_finder_options['masspay-payout-transfer-success-to-admin-subject'] != ""){
			$subject = $service_finder_options['masspay-payout-transfer-success-to-admin-subject'];
		}else{
			$subject = esc_html__('Payout Transfered Succeed', 'service-finder');
		}
		
		if(!empty($service_finder_options['masspay-payout-transfer-success-to-admin'])){
			$message = $service_finder_options['masspay-payout-transfer-success-to-admin'];
		}else{
			$message = 'Hey Admin!
					  Payout transferred successfully. Payout amount was %PAYOUTAMOUNT%';
		}
		
		$tokens = array('%PAYOUTAMOUNT%','%PROVIDERNAME%');
		
		$replacements = array($amount,service_finder_getProviderName($user_id));
		}else
		{
		if($service_finder_options['masspay-payout-transfer-fail-to-admin-subject'] != ""){
			$subject = $service_finder_options['masspay-payout-transfer-fail-to-admin-subject'];
		}else{
			$subject = esc_html__('Payout Transfered Failed', 'service-finder');
		}
		
		if(!empty($service_finder_options['masspay-payout-transfer-fail-to-admin'])){
			$message = $service_finder_options['masspay-payout-transfer-fail-to-admin'];
		}else{
			$message = 'Hey Admin!
					  Payout transferred was failed. Payout amount was %PAYOUTAMOUNT%';
		}
		
		$tokens = array('%PAYOUTAMOUNT%','%PROVIDERNAME%','%FAILEDREASON%');
		
		$replacements = array($amount,service_finder_getProviderName($user_id),$failedmessage);
		}
		$adminemail = get_option( 'admin_email' );
		
		$msg_body = str_replace($tokens,$replacements,$message);
		
		service_finder_wpmailer($adminemail,$subject,$msg_body);
	}

}
