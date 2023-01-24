<?php     
function execute_payment( $sandbox, $api_username, $api_password, $api_signature, $currency, $fees_payer, $receivers, $return_page, $ipn_page, $appid, $cancel_url = '' ) { 	 	
	// Create PayPal object. 	
	$PayPalConfig = array( 		
		'Sandbox' => $sandbox, 
		'DeveloperAccountEmail' => '', 
		'ApplicationID' => $appid, 
		'DeviceID' => '', 
		'IPAddress' => $_SERVER['REMOTE_ADDR'], 
		'APIUsername' => $api_username, 
		'APIPassword' => $api_password, 
		'APISignature' => $api_signature, 
		'APISubject' => ''
	);

	$PayPal = new angelleye\PayPal\Adaptive($PayPalConfig);
	
	// Prepare request arrays
	$PayRequestFields = array(
		'ActionType' => 'PAY_PRIMARY', 
		'CancelURL' => $cancel_url, 	
		'CurrencyCode' => $currency, 	
		'FeesPayer' => $fees_payer, 			
		'IPNNotificationURL' => $ipn_page, 	
		'Memo' => '', 	
		'Pin' => '', 	
		'PreapprovalKey' => '', 
		'ReturnURL' => $return_page, 
		'ReverseAllParallelPaymentsOnError' => '', 
		'SenderEmail' => '',           
		'TrackingID' => ''	
	);
		
	$ClientDetailsFields = array(
		'CustomerID' => '', 		
		'CustomerType' => '', 				
		'GeoLocation' => '', 		
		'Model' => '', 				
		'PartnerName' => 'Always Give Back'
	);
							
	$FundingTypes = array('ECHECK', 'BALANCE', 'CREDITCARD');
	
	$SenderIdentifierFields = array(
		'UseCredentials' => ''			
	);
									
	$AccountIdentifierFields = array(
		'Email' => '', 			
		'Phone' => array('CountryCode' => '', 'PhoneNumber' => '', 'Extension' => '')	
	);
									
	$PayPalRequestData = array(
		'PayRequestFields' => $PayRequestFields, 
		'ClientDetailsFields' => $ClientDetailsFields, 
		'Receivers' => $receivers, 
		'SenderIdentifierFields' => $SenderIdentifierFields, 
		'AccountIdentifierFields' => $AccountIdentifierFields
	);
	
	$PayPalResult = $PayPal->Pay($PayPalRequestData);
	
	return $PayPalResult;
}
?>