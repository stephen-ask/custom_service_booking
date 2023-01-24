<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/

/*Add new team member ajax call*/
add_action('wp_ajax_add_new_member', 'service_finder_add_new_member');
add_action('wp_ajax_nopriv_add_new_member', 'service_finder_add_new_member');

function service_finder_add_new_member(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/team-members/TeamMembers.php';
$addMember = new SERVICE_FINDER_TeamMembers();
$addMember->service_finder_addMembers($_POST);
exit;
}

/*Load service area for team members*/
add_action('wp_ajax_loadserviceareas', 'service_finder_loadserviceareas');
add_action('wp_ajax_nopriv_loadserviceareas', 'service_finder_loadserviceareas');

function service_finder_loadserviceareas(){
global $wpdb;
$current_user = wp_get_current_user(); 
$user_id = (!empty($_POST['user_id'])) ? $_POST['user_id'] : '';
$sAreas = service_finder_getServiceArea($user_id);

$loadservicearea = '';
$loadregions = '';
$loadservices = '';

if(!empty($sAreas)){
	foreach($sAreas as $sArea){
		$loadservicearea .= '<div class="col-lg-3">
				  <div class="checkbox sf-radio-checkbox">
					<input id="'.esc_attr($sArea->zipcode).'" type="checkbox" name="sarea[]" value="'.esc_attr($sArea->zipcode).'" checked>
					<label for="'.esc_attr($sArea->zipcode).'">'.esc_html($sArea->zipcode).'</label>
				  </div>
				</div>';	
	}
}

$regions = service_finder_getServiceRegions($user_id);
if(!empty($regions)){
	foreach($regions as $region){
		$loadregions .= '<div class="col-lg-3">
				  <div class="checkbox sf-radio-checkbox">
					<input id="'.esc_attr($region->region).'" type="checkbox" name="region[]" value="'.esc_attr($region->region).'" checked>
					<label for="'.esc_attr($region->region).'">'.esc_html($region->region).'</label>
				  </div>
				</div>';	
	}
}

$services = service_finder_getAllServices($user_id);
if(!empty($services)){
	foreach($services as $service){
		$loadservices .= '<div class="col-lg-3">
				  <div class="checkbox sf-radio-checkbox">
					<input id="'.esc_attr($service->id).'" type="checkbox" name="service[]" value="'.esc_attr($service->id).'">
					<label for="'.esc_attr($service->id).'">'.esc_html($service->service_name).'</label>
				  </div>
				</div>';	
	}
}

$success = array(
		'status' => 'success',
		'servicearea' => $loadservicearea,
		'regions' => $loadregions,
		'services' => $loadservices
		);
echo json_encode($success);
					
						
exit;
}

/*Load member for edit*/
add_action('wp_ajax_load_member', 'service_finder_load_member');
add_action('wp_ajax_nopriv_load_member', 'service_finder_load_member');
function service_finder_load_member(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/team-members/TeamMembers.php';
$loadMember = new SERVICE_FINDER_TeamMembers();
$loadMember->service_finder_loadMembers($_POST);
exit;
}

/*Edit member ajax call*/
add_action('wp_ajax_edit_member', 'service_finder_edit_member');
add_action('wp_ajax_nopriv_edit_member', 'service_finder_edit_member');

function service_finder_edit_member(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/team-members/TeamMembers.php';
$editMember = new SERVICE_FINDER_TeamMembers();
$editMember->service_finder_editMember($_POST);
exit;
}

/*Get member into datatable ajax call*/
add_action('wp_ajax_get_members', 'service_finder_get_members');
add_action('wp_ajax_nopriv_get_members', 'service_finder_get_members');

function service_finder_get_members(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/team-members/TeamMembers.php';
$getMember = new SERVICE_FINDER_TeamMembers();
$getMember->service_finder_getMembers($_POST);
exit;
}

/*Delete members*/
add_action('wp_ajax_delete_members', 'service_finder_delete_members');
add_action('wp_ajax_nopriv_delete_members', 'service_finder_delete_members');

function service_finder_delete_members(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/team-members/TeamMembers.php';
$deleteMember = new SERVICE_FINDER_TeamMembers();
$deleteMember->service_finder_deleteMembers();
exit;
}

/*Load member for edit*/
add_action('wp_ajax_load_member_slots', 'service_finder_load_member_slots');
add_action('wp_ajax_nopriv_load_member_slots', 'service_finder_load_member_slots');
function service_finder_load_member_slots(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/team-members/TeamMembers.php';
$loadmemberslots = new SERVICE_FINDER_TeamMembers();
$loadmemberslots->service_finder_load_member_slots($_POST);
exit;
}

/*Update member timeslots*/
add_action('wp_ajax_update_member_timeslot', 'service_finder_update_member_timeslot');
add_action('wp_ajax_nopriv_update_member_timeslot', 'service_finder_update_member_timeslot');
function service_finder_update_member_timeslot(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/team-members/TeamMembers.php';
$upadtememberslots = new SERVICE_FINDER_TeamMembers();
$upadtememberslots->service_finder_update_member_slots($_POST);
exit;
}

/*Update member timeslots*/
add_action('wp_ajax_update_member_starttime', 'service_finder_update_member_starttime');
add_action('wp_ajax_nopriv_update_member_starttime', 'service_finder_update_member_starttime');
function service_finder_update_member_starttime(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/team-members/TeamMembers.php';
$upadtememberslots = new SERVICE_FINDER_TeamMembers();
$upadtememberslots->service_finder_update_member_starttime($_POST);
exit;
}

/*Remove Breaktime*/
add_action('wp_ajax_remove_member_breaktime', 'service_finder_remove_member_breaktime');
function service_finder_remove_member_breaktime(){
global $wpdb, $service_finder_Tables;
$user_id = (isset($_POST['user_id'])) ? sanitize_text_field($_POST['user_id']) : '';
$weekday = (isset($_POST['weekday'])) ? sanitize_text_field($_POST['weekday']) : '';
$breakslot = (isset($_POST['breakslot'])) ? sanitize_text_field($_POST['breakslot']) : '';
$memberid = (isset($_POST['memberid'])) ? sanitize_text_field($_POST['memberid']) : '';

$dataset = array(
				'break_start_time' => NULL,
				'break_end_time' => NULL,
				);
$where = array(
			'provider_id' => $user_id,
			'member_id' => $memberid,
			'day' => $weekday,
			);
$wpdb->update($service_finder_Tables->member_starttimes,wp_unslash($dataset),$where);		

$success = array(
		'status' => 'success',
		'suc_message' => esc_html__('Break slot removed successfully.', 'service-finder'),
		);
echo json_encode($success);
exit;
}
