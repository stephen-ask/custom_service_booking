<?php
require_once('../../../../wp-load.php');
// CONFIG: Enable debug mode. This means we'll log requests into 'ipn.log' in the same directory.
// Especially useful if you encounter network errors or other intermittent problems with IPN (validation).
// Set this to 0 once you go live or don't require logging.
define("DEBUG", 1);
// Set to 0 once you're ready to go live
define("USE_SANDBOX", 1);
define("LOG_FILE", "./ipn.log");
update_option('ipndatatest',$_REQUEST);
// Read POST data
// reading posted data directly from $_POST causes serialization
// issues with array data in POST. Reading raw POST data from input stream instead.
global $wpdb, $service_finder_Tables, $service_finder_options;
$raw_post_data = file_get_contents('php://input');
$raw_post_array = explode('&', $raw_post_data);
$myPost = array();
foreach ($raw_post_array as $keyval) {
	$keyval = explode ('=', $keyval);
	if (count($keyval) == 2)
		$myPost[$keyval[0]] = urldecode($keyval[1]);
}
// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';
if(function_exists('get_magic_quotes_gpc')) {
	$get_magic_quotes_exists = true;
}
$status = '';
$paymentstatus = '';
$bookingid = (!empty($_GET['bookingid'])) ? esc_html($_GET['bookingid']) : '';
$invoiceid = (!empty($_GET['invoiceid'])) ? esc_html($_GET['invoiceid']) : '';
$booking = (!empty($_GET['booking'])) ? esc_html($_GET['booking']) : '';
$signup = (!empty($_GET['signup'])) ? esc_html($_GET['signup']) : '';
$userid = (!empty($_GET['userid'])) ? esc_html($_GET['userid']) : '';

foreach ($myPost as $key => $value) {
	if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
		$value = urlencode(stripslashes($value));
	} else {
		$value = urlencode($value);
	}
	if($key == "transaction%5B0%5D.status"){
		$sender_status = $value;
	}
	if($key == "status"){
		$status = $value;
	}
	if($key == "payment_status"){
		$paymentstatus = $value;
	}
	if($key == "pay_key"){
		$pay_key = $value;
	}
	if($key == "transaction%5B0%5D.id"){
		$txnid = $value;
	}
	$req .= "&$key=$value";
}
//$status == "INCOMPLETE"
$sandbox = (isset($service_finder_options['paypal-type']) && $service_finder_options['paypal-type'] == 'live') ? 'false' : 'true';

if($booking == 'complete' && (($sandbox == 'true' && ($status == "COMPLETED" || $status == "INCOMPLETE" || $paymentstatus == "Completed" || $paymentstatus == "Incompleted")) || ($sandbox == 'false' && ($status == "COMPLETED" || $paymentstatus == "Completed")))){
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/book-now/BookNow.php';

$bookdata = array(
		'status' => 'Pending',
		'txnid' => $txnid,
		);

$where = array(
		'paypal_paykey' => $pay_key, 
		);
$wpdb->update($service_finder_Tables->bookings,$bookdata,$where);

$senMail = new SERVICE_FINDER_BookNow();

$bookingdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `paypal_paykey` = "%s"',$pay_key),ARRAY_A);

$senMail->service_finder_SendBookingMailToProvider($bookingdata,'',$bookingdata->adminfee);
$senMail->service_finder_SendBookingMailToCustomer($bookingdata,'',$bookingdata->adminfee);
$senMail->service_finder_SendBookingMailToAdmin($bookingdata,'',$bookingdata->adminfee);
}

if($signup == 'done' && $userid > 0 && (($sandbox == 'true' && ($status == "COMPLETED" || $status == "INCOMPLETE" || $paymentstatus == "Completed" || $paymentstatus == "Incompleted")) || ($sandbox == 'false' && ($status == "COMPLETED" || $paymentstatus == "Completed")))){

}elseif($signup == 'done' && $userid > 0){
	$wpdb->query($wpdb->prepare('DELETE FROM '.$wpdb->users.' WHERE `ID` = %d',$userid));
	$wpdb->query($wpdb->prepare('DELETE FROM '.$wpdb->usermeta.' WHERE `user_id` = %d',$userid));
	service_finder_deleteProvidersData($userid);
}

update_option('ipndata',$myPost);

error_log($req, 3, LOG_FILE);
?>
