<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/

/*Add TimeSlot Ajax Call*/
add_action('wp_ajax_weekday_timeslots', 'service_finder_weekday_timeslots');
add_action('wp_ajax_nopriv_weekday_timeslots', 'service_finder_weekday_timeslots');

function service_finder_weekday_timeslots(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/availability/Availability.php';
$addTimeSlots = new SERVICE_FINDER_Availability();
$addTimeSlots->service_finder_addTimeSlots($_POST);
exit;
}

/*Delete TimeSlot Ajax Call*/
add_action('wp_ajax_delete_timeslot', 'service_finder_delete_timeslot');
add_action('wp_ajax_nopriv_delete_timeslot', 'service_finder_delete_timeslot');

function service_finder_delete_timeslot(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/availability/Availability.php';
$deleteTimeSlot = new SERVICE_FINDER_Availability();
$deleteTimeSlot->service_finder_deleteTimeSlot($_POST);
exit;
}

/*Set Unavailability Ajax Call*/
add_action('wp_ajax_set_unavailability', 'service_finder_set_unavailability');
add_action('wp_ajax_nopriv_set_unavailability', 'service_finder_set_unavailability');

function service_finder_set_unavailability(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/availability/Availability.php';
$setUnAailability = new SERVICE_FINDER_Availability();


$provider_id = (!empty($_POST['provider_id'])) ? esc_html($_POST['provider_id']) : '';
if(service_finder_availability_method($provider_id) == 'timeslots'){
	$setUnAailability->service_finder_setUnAailability($_POST);
}elseif(service_finder_availability_method($provider_id) == 'starttime'){
	$setUnAailability->service_finder_setUnAailabilityStartTime($_POST);
}else{
	$setUnAailability->service_finder_setUnAailability($_POST);
}

exit;
}

/*Get Unavailability Ajax Call*/
add_action('wp_ajax_get_unavilability', 'service_finder_get_unavilability');
add_action('wp_ajax_nopriv_get_unavilability', 'service_finder_get_unavilability');

function service_finder_get_unavilability(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/availability/Availability.php';
$getUnAailability = new SERVICE_FINDER_Availability();

$provider_id = (!empty($_POST['user_id'])) ? esc_html($_POST['user_id']) : '';
if(service_finder_availability_method($provider_id) == 'timeslots'){
	$getUnAailability->service_finder_getUnAailability($_POST);
}elseif(service_finder_availability_method($provider_id) == 'starttime'){
	$getUnAailability->service_finder_getUnAailabilityStartTime($_POST);
}else{
	$getUnAailability->service_finder_getUnAailability($_POST);
}

exit;
}

/*Load Unavailability Ajax Call*/
add_action('wp_ajax_load_unavilability', 'service_finder_load_unavilability');
add_action('wp_ajax_nopriv_load_unavilability', 'service_finder_load_unavilability');

function service_finder_load_unavilability(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/availability/Availability.php';
$loadUnAailability = new SERVICE_FINDER_Availability();

$provider_id = (!empty($_POST['user_id'])) ? esc_html($_POST['user_id']) : '';
if(service_finder_availability_method($provider_id) == 'timeslots'){
	$loadUnAailability->service_finder_loadUnAailability($_POST);
}elseif(service_finder_availability_method($provider_id) == 'starttime'){
	$loadUnAailability->service_finder_loadUnAailabilityStartTime($_POST);
}else{
	$loadUnAailability->service_finder_loadUnAailability($_POST);
}

exit;
}

/*Edit Unavailability Ajax Call*/
add_action('wp_ajax_edit_unavailability', 'service_finder_edit_unavailability');
add_action('wp_ajax_nopriv_edit_unavailability', 'service_finder_edit_unavailability');

function service_finder_edit_unavailability(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/availability/Availability.php';
$editUnAailability = new SERVICE_FINDER_Availability();

$provider_id = (!empty($_POST['user_id'])) ? esc_html($_POST['user_id']) : '';
if(service_finder_availability_method($provider_id) == 'timeslots'){
	$editUnAailability->service_finder_editUnAailability($_POST);
}elseif(service_finder_availability_method($provider_id) == 'starttime'){
	$editUnAailability->service_finder_editUnAailabilityStartTime($_POST);
}else{
	$editUnAailability->service_finder_editUnAailability($_POST);
}

exit;
}

/*Delete Unavailability Ajax Call*/
add_action('wp_ajax_delete_unavilability', 'service_finder_delete_unavilability');
add_action('wp_ajax_nopriv_delete_unavilability', 'service_finder_delete_unavilability');

function service_finder_delete_unavilability(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/availability/Availability.php';

$deleteUnAailability = new SERVICE_FINDER_Availability();
$deleteUnAailability->service_finder_deleteUnAailability($_POST);
exit;
}

/*Reset Calendar Ajax Call*/
add_action('wp_ajax_reset_calendar', 'service_finder_reset_calendar');
add_action('wp_ajax_nopriv_reset_calendar', 'service_finder_reset_calendar');

function service_finder_reset_calendar(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/availability/Availability.php';

$resetCalender = new SERVICE_FINDER_Availability();
$provider_id = (!empty($_POST['provider_id'])) ? esc_html($_POST['provider_id']) : '';
if(service_finder_availability_method($provider_id) == 'timeslots'){
	$resetCalender->service_finder_resetCalender($_POST);
}elseif(service_finder_availability_method($provider_id) == 'starttime'){
	$resetCalender->service_finder_resetCalenderStartTime($_POST);
}else{
	$resetCalender->service_finder_resetCalender($_POST);
}
exit;
}

/*Get Timeslots based on date Ajax Call*/
add_action('wp_ajax_get_timeslot', 'service_finder_get_timeslot');
add_action('wp_ajax_nopriv_get_timeslot', 'service_finder_get_timeslot');

function service_finder_get_timeslot(){
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/availability/Availability.php';
global $wpdb;
$getTimeSlot = new SERVICE_FINDER_Availability();
$provider_id = (!empty($_POST['provider_id'])) ? esc_html($_POST['provider_id']) : '';
if(service_finder_availability_method($provider_id) == 'timeslots'){
	$getTimeSlot->service_finder_getTimeSlot($_POST);
}elseif(service_finder_availability_method($provider_id) == 'starttime'){
	$getTimeSlot->service_finder_getStartTime($_POST);
}else{
	$getTimeSlot->service_finder_getTimeSlot($_POST);
}

exit;
}

/*Add Start Time Ajax Call*/
add_action('wp_ajax_add_start_time', 'service_finder_add_start_time');
add_action('wp_ajax_nopriv_add_start_time', 'service_finder_add_start_time');
function service_finder_add_start_time(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/availability/Availability.php';
$addstarttime = new SERVICE_FINDER_Availability();
$addstarttime->service_finder_addStartTime($_POST);
exit;
}

/*Add Bulk Slots Ajax Call*/
add_action('wp_ajax_add_bulk_slots', 'service_finder_add_bulk_slots');
add_action('wp_ajax_nopriv_add_bulk_slots', 'service_finder_add_bulk_slots');
function service_finder_add_bulk_slots(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/availability/Availability.php';
$addbulkslots = new SERVICE_FINDER_Availability();
$provider_id = (!empty($_POST['user_id'])) ? esc_html($_POST['user_id']) : '';

if(service_finder_availability_method($provider_id) == 'timeslots'){
	$addbulkslots->service_finder_bulk_timeslots($_POST);
}elseif(service_finder_availability_method($provider_id) == 'starttime'){
	$addbulkslots->service_finder_bulk_starttimes($_POST);
}else{
	$addbulkslots->service_finder_bulk_timeslots($_POST);
}

exit;
}


/*Delete start time*/
add_action('wp_ajax_remove_start_time', 'service_finder_remove_start_time');
add_action('wp_ajax_nopriv_remove_start_time', 'service_finder_remove_start_time');
function service_finder_remove_start_time(){
global $wpdb,$service_finder_Tables;

$tid = (isset($_POST['tid'])) ? sanitize_text_field($_POST['tid']) : '';
$user_id = (isset($_POST['user_id'])) ? sanitize_text_field($_POST['user_id']) : '';
$wpdb->query($wpdb->prepare('DELETE FROM `'.$service_finder_Tables->starttime.'` WHERE `id` = %d AND `provider_id` = %d',$tid,$user_id));
exit;
}

/*Update max booking*/
add_action('wp_ajax_upadte_maxbooking', 'service_finder_upadte_maxbooking');
add_action('wp_ajax_nopriv_upadte_maxbooking', 'service_finder_upadte_maxbooking');
function service_finder_upadte_maxbooking(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/availability/Availability.php';
$updatemaxbooking = new SERVICE_FINDER_Availability();
$updatemaxbooking->service_finder_updateMaxBooking($_POST);
exit;
}

/*Get off Days*/
add_action('wp_ajax_get_offdays', 'service_finder_get_offdays');
add_action('wp_ajax_nopriv_get_offdays', 'service_finder_get_offdays');
function service_finder_get_offdays(){
	$unavl_type = (!empty($_POST['unavl_type'])) ? esc_html($_POST['unavl_type']) : '';
	$numberofdays = (!empty($_POST['numberofdays'])) ? esc_html($_POST['numberofdays']) : '';
	$startdate = (!empty($_POST['startdate'])) ? esc_html($_POST['startdate']) : '';
	
	$holidays = array();
		
	if($unavl_type != "" && $numberofdays != ""){
	$totaldays = service_finder_get_total_offdays($unavl_type,$numberofdays);
	
	for($i = 0; $i < $totaldays; $i++){
		$holidays[] = date('Y-m-d',strtotime($startdate. ' + '.$i.' days'));
	}
	
	}
	echo json_encode($holidays);  // send data as json format

exit;
}

