<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/

/*Add new branch ajax call*/
add_action('wp_ajax_add_new_branch', 'service_finder_add_new_branch');
function service_finder_add_new_branch(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/branches/OurBranches.php';
$addBranch = new SERVICE_FINDER_OurBranches();
$addBranch->service_finder_addBranches($_POST);
exit;
}

/*Get All Provider Branches*/
add_action('wp_ajax_get_branches', 'service_finder_get_branches');
function service_finder_get_branches(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/branches/OurBranches.php';
$getBranches = new SERVICE_FINDER_OurBranches();
$getBranches->service_finder_getBranches($_POST);
exit;
}

/*Delete Branches*/
add_action('wp_ajax_delete_branches', 'service_finder_delete_branches');
function service_finder_delete_branches(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/branches/OurBranches.php';
$deleteBranches = new SERVICE_FINDER_OurBranches();
$deleteBranches->service_finder_deleteBranches($_POST);
exit;
}

/*Get branch location*/
add_action('wp_ajax_get_branch_location', 'service_finder_get_branch_location');
function service_finder_get_branch_location(){
global $wpdb, $service_finder_Errors, $service_finder_Tables;
		
		$branchid = (!empty($_POST['branchid'])) ? sanitize_text_field($_POST['branchid']) : '';
		
		$sql = $wpdb->prepare("SELECT * FROM ".$service_finder_Tables->branches. " WHERE `id` = %d",$branchid);
		$row = $wpdb->get_row($sql);
		
		$lat = $row->lat;
		$lng = $row->long;
		
		if($lat == '' && $lng == ''){
		$address = $row->address;
		$city = $row->city;
		$country = $row->country;
		
		$full_address = $address.' '.$city.' '.$country;
		
		$address = str_replace(" ","+",$full_address);
		$res = service_finder_getLatLong($address);
		$lat = $res['lat'];
		$lng = $res['lng'];
		}
		
		$success = array(
				'status' => 'success',
				'lat' => esc_html($lat),
				'lng' => esc_html($lng)
				);
		echo json_encode($success);
		exit;
}

/*Update markers*/
add_action('wp_ajax_save_marker_position', 'service_finder_save_marker_position');
function service_finder_save_marker_position(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/branches/OurBranches.php';
$updateMarker = new SERVICE_FINDER_OurBranches();
$updateMarker->service_finder_updateMarker($_POST);
exit;
}
