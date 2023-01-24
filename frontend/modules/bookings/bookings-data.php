<?php

/*****************************************************************************

*

*	copyright(c) - aonetheme.com - Service Finder Team

*	More Info: http://aonetheme.com/

*	Coder: Service Finder Team

*	Email: contact@aonetheme.com

*

******************************************************************************/



/*Get Provider Bokings Ajax Call*/

add_action('wp_ajax_get_bookings', 'service_finder_get_bookings');

add_action('wp_ajax_nopriv_get_bookings', 'service_finder_get_bookings');



function service_finder_get_bookings(){

global $wpdb;

require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/bookings/Bookings.php';

$getBookings = new SERVICE_FINDER_Bookings();

$getBookings->service_finder_getBookings($_POST);

exit;

}



/*Load All Available member or assign Ajax Call*/

add_action('wp_ajax_load_allmembers', 'service_finder_load_allmembers');

add_action('wp_ajax_nopriv_load_allmembers', 'service_finder_load_allmembers');



function service_finder_load_allmembers(){

global $wpdb;

require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/bookings/Bookings.php';

$loadAllMembers = new SERVICE_FINDER_Bookings();

$loadAllMembers->service_finder_loadAllMembers($_POST);

exit;

}



/*Assign member for new booking Ajax Call*/

add_action('wp_ajax_assign_new_member', 'service_finder_assign_new_member');

add_action('wp_ajax_nopriv_assign_new_member', 'service_finder_assign_new_member');



function service_finder_assign_new_member(){

global $wpdb;

require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/bookings/Bookings.php';

$assignMember = new SERVICE_FINDER_Bookings();

$assignMember->service_finder_assignMember($_POST);

exit;

}



/*Assign member for new booking Ajax Call*/

add_action('wp_ajax_change_status', 'service_finder_change_status');

add_action('wp_ajax_nopriv_change_status', 'service_finder_change_status');



function service_finder_change_status(){

global $wpdb;

require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/bookings/Bookings.php';

$changeStatus = new SERVICE_FINDER_Bookings();

$changeStatus->service_finder_changeStatus($_POST);

exit;

}





/*Delete Provider Bokings Ajax Call*/

add_action('wp_ajax_delete_bookings', 'service_finder_delete_bookings');

add_action('wp_ajax_nopriv_delete_bookings', 'service_finder_delete_bookings');



function service_finder_delete_bookings(){

global $wpdb;

require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/bookings/Bookings.php';

$deleteBooking = new SERVICE_FINDER_Bookings();

$deleteBooking->service_finder_deleteBookings();

exit;

}



/*View Provider Booking Details Ajax Call*/

add_action('wp_ajax_booking_details', 'service_finder_booking_details');

add_action('wp_ajax_nopriv_booking_details', 'service_finder_booking_details');



function service_finder_booking_details(){

global $wpdb;

require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/bookings/Bookings.php';

$viewBooking = new SERVICE_FINDER_Bookings();

$viewBooking->service_finder_viewBookings();

exit;

}



/*Get Customer Past Bokings Ajax Call*/

add_action('wp_ajax_get_customer_pastbookings', 'service_finder_get_customer_pastbookings');

add_action('wp_ajax_nopriv_get_customer_pastbookings', 'service_finder_get_customer_pastbookings');



function service_finder_get_customer_pastbookings(){

global $wpdb;

require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/bookings/Bookings.php';

$getBookings = new SERVICE_FINDER_Bookings();

$getBookings->service_finder_getCustomerPastBookings();

exit;

}



/*Get Customer Upcoming Bokings Ajax Call*/

add_action('wp_ajax_get_customer_upcomingbookings', 'service_finder_get_customer_upcomingbookings');

add_action('wp_ajax_nopriv_get_customer_upcomingbookings', 'service_finder_get_customer_upcomingbookings');



function service_finder_get_customer_upcomingbookings(){

global $wpdb;

require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/bookings/Bookings.php';

$getBookings = new SERVICE_FINDER_Bookings();

$getBookings->service_finder_getCustomerUpcomingBookings();

exit;

}



/*Get Customer Upcoming Bokings Ajax Call*/

add_action('wp_ajax_get_booked_services', 'service_finder_get_booked_services');

add_action('wp_ajax_nopriv_get_booked_services', 'service_finder_get_booked_services');



function service_finder_get_booked_services(){

global $wpdb;

require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/bookings/Bookings.php';

$getBookings = new SERVICE_FINDER_Bookings();

$getBookings->service_finder_getBookedServices($_POST);

exit;

}



/*Delete Customer Bokings Ajax Call*/

add_action('wp_ajax_delete_customer_bookings', 'service_finder_delete_customer_bookings');

add_action('wp_ajax_nopriv_delete_customer_bookings', 'service_finder_delete_customer_bookings');



function service_finder_delete_customer_bookings(){

global $wpdb;

require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/bookings/Bookings.php';

$deleteBooking = new SERVICE_FINDER_Bookings();

$deleteBooking->service_finder_deleteCustomerBookings();

exit;

}



/*Add Invoice Data Ajax Call*/

add_action('wp_ajax_add_booking_invoice', 'service_finder_add_booking_invoice');

add_action('wp_ajax_nopriv_add_booking_invoice', 'service_finder_add_booking_invoice');



function service_finder_add_booking_invoice(){

global $wpdb;

require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/bookings/Bookings.php';

$addData = new SERVICE_FINDER_Bookings();

$addData->service_finder_addInvoiceData($_POST);

exit;

}



/*Add Feedback Data Ajax Call*/

add_action('wp_ajax_add_feedback', 'service_finder_add_feedback');

add_action('wp_ajax_nopriv_add_feedback', 'service_finder_add_feedback');



function service_finder_add_feedback(){

global $wpdb;

require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/bookings/Bookings.php';

$addData = new SERVICE_FINDER_Bookings();

$addData->service_finder_addFeedback($_POST);

exit;

}



/*Show Feedback Data Ajax Call*/

add_action('wp_ajax_show_feedback', 'service_finder_show_feedback');

add_action('wp_ajax_nopriv_show_feedback', 'service_finder_show_feedback');



function service_finder_show_feedback(){

global $wpdb;

require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/bookings/Bookings.php';

$getData = new SERVICE_FINDER_Bookings();

$getData->service_finder_getFeedback($_POST);

exit;

}



/*Cancel booking ajax call*/

add_action('wp_ajax_cancel_booking', 'service_finder_cancel_booking');

add_action('wp_ajax_nopriv_cancel_booking', 'service_finder_cancel_booking');



function service_finder_cancel_booking(){

global $wpdb;

require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/bookings/Bookings.php';

$cancelBooking = new SERVICE_FINDER_Bookings();

$cancelBooking->service_finder_cancelBooking($_POST);

exit;

}



add_action('wp_ajax_complete_booking', 'service_finder_complete_booking');

add_action('wp_ajax_nopriv_complete_booking', 'service_finder_complete_booking');



function service_finder_complete_booking(){

global $wpdb;

require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/bookings/Bookings.php';

$cancelBooking = new SERVICE_FINDER_Bookings();

$cancelBooking->service_finder_completeBooking($_POST);

exit;

}



add_action('wp_ajax_change_bookedservice_status', 'service_finder_change_bookedservice_status');

add_action('wp_ajax_nopriv_change_bookedservice_status', 'service_finder_change_bookedservice_status');



function service_finder_change_bookedservice_status(){

global $wpdb;

require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/bookings/Bookings.php';

$cancelBooking = new SERVICE_FINDER_Bookings();

$cancelBooking->service_finder_changeServiceStatus($_POST);

exit;

}



/*Complete booking via paypal masspay*/

add_action('wp_ajax_complete_booking_and_pay', 'service_finder_complete_booking_and_pay');

add_action('wp_ajax_nopriv_complete_booking_and_pay', 'service_finder_complete_booking_and_pay');



function service_finder_complete_booking_and_pay(){

global $wpdb;

require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/bookings/Bookings.php';

$cancelBooking = new SERVICE_FINDER_Bookings();

$cancelBooking->service_finder_completeBookingandPay($_POST);

exit;

}



/*Complete booking via stripe*/

add_action('wp_ajax_complete_booking_and_pay_via_stripe', 'service_finder_complete_booking_and_pay_via_stripe');

add_action('wp_ajax_nopriv_complete_booking_and_pay_via_stripe', 'service_finder_complete_booking_and_pay_via_stripe');



function service_finder_complete_booking_and_pay_via_stripe(){

global $wpdb;

require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/bookings/Bookings.php';

$cancelBooking = new SERVICE_FINDER_Bookings();

$cancelBooking->service_finder_completeBookingandPayviaStripe($_POST);

exit;

}



/*Edit Booking Ajax Call*/

add_action('wp_ajax_editbooking', 'service_finder_editbooking');

add_action('wp_ajax_nopriv_editbooking', 'service_finder_editbooking');



function service_finder_editbooking(){

global $wpdb;

require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/bookings/Bookings.php';

$editBooking = new SERVICE_FINDER_Bookings();

echo $editBooking->service_finder_editBooking($_POST);

exit;

}



/*Update Booking Ajax Call*/

add_action('wp_ajax_update_booking', 'service_finder_update_booking');

add_action('wp_ajax_nopriv_update_booking', 'service_finder_update_booking');



function service_finder_update_booking(){

global $wpdb;

require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/bookings/Bookings.php';

$updateBooking = new SERVICE_FINDER_Bookings();

$updateBooking->service_finder_updateBooking($_POST);

exit;

}



/*Update Booking Ajax Call*/

add_action('wp_ajax_update_service_schedule', 'service_finder_update_service_schedule');

add_action('wp_ajax_nopriv_update_service_schedule', 'service_finder_update_service_schedule');



function service_finder_update_service_schedule(){

global $wpdb;

require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/bookings/Bookings.php';

$updateBooking = new SERVICE_FINDER_Bookings();

$updateBooking->service_finder_updateServiceSchedule($_POST);

exit;

}



/*Approve wired booking*/

add_action('wp_ajax_wired_booking_approval', 'service_finder_wired_booking_approval');

function service_finder_wired_booking_approval(){

global $wpdb;

require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/bookings/Bookings.php';

$approvebooking = new SERVICE_FINDER_Bookings();

$approvebooking->service_finder_approvebooking($_POST);

exit;

}



/*Approve wired booking*/

add_action('wp_ajax_get_ratingbox', 'service_finder_get_ratingbox');

function service_finder_get_ratingbox(){

global $wpdb;

require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/bookings/Bookings.php';

$getratingbox = new SERVICE_FINDER_Bookings();

$getratingbox->service_finder_getratingbox($_POST);

exit;

}



/*Load Edit Members List*/

add_action('wp_ajax_load_editmembers_list', 'service_finder_load_editmembers_list');

add_action('wp_ajax_nopriv_load_editmembers_list', 'service_finder_load_editmembers_list');



function service_finder_load_editmembers_list(){

global $wpdb;

require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/bookings/Bookings.php';

$getratingbox = new SERVICE_FINDER_Bookings();

$getratingbox->service_finder_loadEditMembersList($_POST);

exit;

}



/*Reset Booking Calendar*/

add_action('wp_ajax_reset_editbookingcalendar', 'service_finder_reset_editbookingcalendar');

add_action('wp_ajax_nopriv_reset_editbookingcalendar', 'service_finder_reset_editbookingcalendar');



function service_finder_reset_editbookingcalendar(){

global $wpdb;

require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/bookings/Bookings.php';

$resetBookingCalender = new SERVICE_FINDER_Bookings();



$provider_id = (!empty($_POST['provider_id'])) ? esc_html($_POST['provider_id']) : '';



$resetBookingCalender->service_finder_resetStartTimeBookingCalender($_POST);



exit;

}

/* Submit Provider Form via Ajax*/
add_action('wp_ajax_update_booking_settings', 'service_finder_update_booking_settings');
add_action('wp_ajax_nopriv_update_booking_settings', 'service_finder_update_booking_settings');

function service_finder_update_booking_settings(){
global $wpdb, $service_finder_Tables, $current_user;
$userId = (!empty($_POST['providerid'])) ? esc_html($_POST['providerid']) : '';

/*Update Provider Table*/
$data = array(
	'booking_description' => (!empty($_POST['booking_description'])) ? $_POST['booking_description'] : '',
	);

$where = array(
	'wp_user_id' => $userId,
	);
$proid = $wpdb->update($service_finder_Tables->providers,wp_unslash($data),$where);

/*Update Provider Settings*/
$booking_process = (!empty($_POST['booking_process'])) ? $_POST['booking_process'] : '';
$availability_based_on = (!empty($_POST['availability_based_on'])) ? $_POST['availability_based_on'] : '';
$slot_interval = (!empty($_POST['slot_interval'])) ? $_POST['slot_interval'] : '';
$offers_based_on = (!empty($_POST['offers_based_on'])) ? $_POST['offers_based_on'] : '';
$booking_date_based_on = (!empty($_POST['booking_date_based_on'])) ? $_POST['booking_date_based_on'] : '';
$booking_basedon = (!empty($_POST['booking_basedon'])) ? $_POST['booking_basedon'] : '';
$booking_charge_on_service = 'yes';
$booking_option = (!empty($_POST['booking_option'])) ? $_POST['booking_option'] : '';
$mincost = (isset($_POST['mincost'])) ? $_POST['mincost'] : '';
$mincost = ($mincost == 0) ? '0.0' : $mincost;
$booking_assignment = (!empty($_POST['booking_assignment'])) ? $_POST['booking_assignment'] : '';
$members_available = (!empty($_POST['members_available'])) ? $_POST['members_available'] : '';
$pay_options = (!empty($_POST['pay_options'])) ? $_POST['pay_options'] : '';
$paypalusername = (!empty($_POST['paypalusername'])) ? $_POST['paypalusername'] : '';
$paypalpassword = (!empty($_POST['paypalpassword'])) ? $_POST['paypalpassword'] : '';
$paypalsignatue = (!empty($_POST['paypalsignatue'])) ? $_POST['paypalsignatue'] : '';
$stripesecretkey = (!empty($_POST['stripesecretkey'])) ? $_POST['stripesecretkey'] : '';
$stripepublickey = (!empty($_POST['stripepublickey'])) ? $_POST['stripepublickey'] : '';
$wired_description = (!empty($_POST['wired_description'])) ? $_POST['wired_description'] : '';
$wired_instructions = (!empty($_POST['wired_instructions'])) ? $_POST['wired_instructions'] : '';
$twocheckoutaccountid = (!empty($_POST['twocheckoutaccountid'])) ? $_POST['twocheckoutaccountid'] : '';
$twocheckoutpublishkey = (!empty($_POST['twocheckoutpublishkey'])) ? $_POST['twocheckoutpublishkey'] : '';
$twocheckoutprivatekey = (!empty($_POST['twocheckoutprivatekey'])) ? $_POST['twocheckoutprivatekey'] : '';
$payumoneymid = (!empty($_POST['payumoneymid'])) ? $_POST['payumoneymid'] : '';
$payumoneykey = (!empty($_POST['payumoneykey'])) ? $_POST['payumoneykey'] : '';
$payumoneysalt = (!empty($_POST['payumoneysalt'])) ? $_POST['payumoneysalt'] : '';
$payulatammerchantid = (!empty($_POST['payulatammerchantid'])) ? $_POST['payulatammerchantid'] : '';
$payulatamapilogin = (!empty($_POST['payulatamapilogin'])) ? $_POST['payulatamapilogin'] : '';
$payulatamapikey = (!empty($_POST['payulatamapikey'])) ? $_POST['payulatamapikey'] : '';
$payulatamaccountid = (!empty($_POST['payulatamaccountid'])) ? $_POST['payulatamaccountid'] : '';

$futureavailability = (!empty($_POST['futureavailability'])) ? $_POST['futureavailability'] : '';
$buffertime = (!empty($_POST['buffertime'])) ? $_POST['buffertime'] : '';

$bank_account_holder_name = (!empty($_POST['bank_account_holder_name'])) ? $_POST['bank_account_holder_name'] : '';
$bank_account_number = (!empty($_POST['bank_account_number'])) ? $_POST['bank_account_number'] : '';
$swift_code = (!empty($_POST['swift_code'])) ? $_POST['swift_code'] : '';
$bank_name = (!empty($_POST['bank_name'])) ? $_POST['bank_name'] : '';
$bank_branch_city = (!empty($_POST['bank_branch_city'])) ? $_POST['bank_branch_city'] : '';
$bank_branch_country = (!empty($_POST['bank_branch_country'])) ? $_POST['bank_branch_country'] : '';
$paypal_email_id = (!empty($_POST['paypal_email_id'])) ? $_POST['paypal_email_id'] : '';

update_user_meta($userId,'bank_account_holder_name',$bank_account_holder_name);
update_user_meta($userId,'bank_account_number',$bank_account_number);
update_user_meta($userId,'swift_code',$swift_code);
update_user_meta($userId,'bank_name',$bank_name);
update_user_meta($userId,'bank_branch_city',$bank_branch_city);
update_user_meta($userId,'bank_branch_country',$bank_branch_country);

update_user_meta($userId,'paypal_email_id',$paypal_email_id);

$reloadflag = 0;
if(service_finder_get_slot_interval($userId) != intval($slot_interval)){
$reloadflag = 1;
if(service_finder_availability_method($userId) == 'timeslots'){
	$wpdb->query($wpdb->prepare('DELETE FROM `'.$service_finder_Tables->timeslots.'` WHERE `provider_id` = %d',$userId));
	$wpdb->query($wpdb->prepare('DELETE FROM `'.$service_finder_Tables->starttime.'` WHERE `provider_id` = %d',$userId));
}elseif(service_finder_availability_method($userId) == 'starttime'){
	$wpdb->query($wpdb->prepare('DELETE FROM `'.$service_finder_Tables->starttime.'` WHERE `provider_id` = %d',$userId));
	$wpdb->query($wpdb->prepare('DELETE FROM `'.$service_finder_Tables->timeslots.'` WHERE `provider_id` = %d',$userId));
}

}

if(service_finder_availability_method($userId) != $availability_based_on && $availability_based_on != ""){
$reloadflag = 1;
}

$options = unserialize(get_option( 'provider_settings'));
$google_calendar = $options[$userId]['google_calendar'];

$options[$userId] = array(
'booking_process' => esc_html($booking_process),
'availability_based_on' => esc_html($availability_based_on),
'slot_interval' => esc_html($slot_interval),
'offers_based_on' => esc_html($offers_based_on),
'booking_date_based_on' => esc_html($booking_date_based_on),
'booking_basedon' => ($booking_process == 'on') ? $booking_basedon : '',
'booking_charge_on_service' => 'yes',
'booking_option' => ($booking_process == 'on') ? $booking_option : '',
'mincost' => $mincost,
'future_bookings_availability' => ($booking_process == 'on') ? $futureavailability : '',
'buffertime' => ($booking_process == 'on') ? $buffertime : '',
'booking_assignment' => ($booking_process == 'on') ? $booking_assignment : '',
'members_available' => ($booking_process == 'on' && $booking_assignment == 'automatically') ? $members_available : '',
'paymentoption' => ($booking_option == 'paid' && $booking_process == 'on') ? $pay_options : '',
'paypalusername' => ($booking_option == 'paid' && $booking_process == 'on') ? $paypalusername : '',
'paypalpassword' => ($booking_option == 'paid' && $booking_process == 'on') ? $paypalpassword : '',
'paypalsignatue' => ($booking_option== 'paid' && $booking_process == 'on') ? $paypalsignatue : '',
'stripesecretkey' => ($booking_option == 'paid' && $booking_process == 'on') ? $stripesecretkey : '',
'stripepublickey' => ($booking_option == 'paid' && $booking_process == 'on') ? $stripepublickey : '',
'wired_description' => ($booking_option == 'paid' && $booking_process == 'on') ? $wired_description : '',
'wired_instructions' => ($booking_option == 'paid' && $booking_process == 'on') ? $wired_description : '',
'twocheckoutaccountid' => ($booking_option == 'paid' && $booking_process == 'on') ? $twocheckoutaccountid : '',
'twocheckoutpublishkey' => ($booking_option == 'paid' && $booking_process == 'on') ? $twocheckoutpublishkey : '',
'twocheckoutprivatekey' => ($booking_option == 'paid' && $booking_process == 'on') ? $twocheckoutprivatekey : '',
'payumoneymid' => ($booking_option == 'paid' && $booking_process == 'on') ? $payumoneymid : '',
'payumoneykey' => ($booking_option == 'paid' && $booking_process == 'on') ? $payumoneykey : '',
'payumoneysalt' => ($booking_option == 'paid' && $booking_process == 'on') ? $payumoneysalt : '',
'payulatammerchantid' => ($booking_option == 'paid' && $booking_process == 'on') ? $payulatammerchantid : '',
'payulatamapilogin' => ($booking_option == 'paid' && $booking_process == 'on') ? $payulatamapilogin : '',
'payulatamapikey' => ($booking_option == 'paid' && $booking_process == 'on') ? $payulatamapikey : '',
'payulatamaccountid' => ($booking_option == 'paid' && $booking_process == 'on') ? $payulatamaccountid : '',
'google_calendar' => $google_calendar,
);
update_option( 'provider_settings', serialize($options) );

$myaccounturl = service_finder_get_my_account_url($userId);

$redirect_uri = add_query_arg( array('tabname' => 'booking-settings'), $myaccounturl );

$success = array(
	'status' => 'success',
	'reloadflag' => $reloadflag,
	'redirect' => $redirect_uri,
	'suc_message' => esc_html__('Booking settings updated successfully.', 'service-finder'),
	);
echo json_encode($success);

exit;
}

/* Load Follower Count */
add_action('wp_ajax_load_get_follower_count', 'service_finder_load_followers_counts');
add_action('wp_ajax_nopriv_get_follower_count', 'service_finder_load_followers_counts');

function service_finder_load_followers_counts(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/bookings/Bookings.php';

$load_followers_count = new SERVICE_FINDER_Bookings();
echo $load_followers_count = $load_followers_count->get_follower_count($_POST);
exit;
}
