<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/

add_action('wp_ajax_load_business_hours', 'service_finder_load_business_hours');
function service_finder_load_business_hours(){
$user_id = (isset($_POST['user_id'])) ? esc_attr($_POST['user_id']) : '';
$timeslots = get_user_meta($user_id,'timeslots',true);
return $timeslots;
exit;
}

add_action('wp_ajax_update_business_hours', 'service_finder_update_business_hours');
function service_finder_update_business_hours(){
global $wpdb;

$user_id = (isset($_POST['user_id'])) ? esc_attr($_POST['user_id']) : '';
		
$start_time = (!empty($_POST['start_time'])) ? $_POST['start_time'] : '';
$end_time = (!empty($_POST['end_time'])) ? $_POST['end_time'] : '';

$arraymap = array_map(null,$start_time,$end_time);

if(!empty($arraymap)){
foreach ($arraymap as $key => $value) {

$bhstatus = (isset($_POST['bhstatus_'.$key])) ? esc_attr($_POST['bhstatus_'.$key]) : '';

if($bhstatus == 'on'){
if($value[0] != ""){
$timeslots[$key] = $value[0].'-'.$value[1];
}else{
$timeslots[$key] = 'off';
}
}else{
$timeslots[$key] = 'off';
}

}
}
update_user_meta($user_id, 'timeslots', $timeslots);
$success = array(
		'status' => 'success',
		'suc_message' => esc_html__('Business hours updated successfully.', 'service-finder'),
		);
echo json_encode($success);
exit;
}

add_action('wp_ajax_get_bh_breaktime', 'service_finder_get_bh_breaktime');
function service_finder_get_bh_breaktime(){
$breaktime = array();
		
$break_start_time = (isset($_POST['break_start_time'])) ? sanitize_text_field($_POST['break_start_time']) : '';
$break_end_time = (isset($_POST['break_end_time'])) ? sanitize_text_field($_POST['break_end_time']) : '';
$user_id = (isset($_POST['user_id'])) ? sanitize_text_field($_POST['user_id']) : '';
$weekday = (isset($_POST['weekday'])) ? sanitize_text_field($_POST['weekday']) : '';

$breaktime = get_user_meta($user_id, 'breaktime', true);
if(!empty($breaktime[$weekday])){
echo json_encode($breaktime[$weekday]);
}

exit(0);
}

add_action('wp_ajax_bh_addbreaktime', 'service_finder_bh_addbreaktime');
function service_finder_bh_addbreaktime(){
		
$break_start_time = (isset($_POST['break_start_time'])) ? sanitize_text_field($_POST['break_start_time']) : '';
$break_end_time = (isset($_POST['break_end_time'])) ? sanitize_text_field($_POST['break_end_time']) : '';
$user_id = (isset($_POST['user_id'])) ? sanitize_text_field($_POST['user_id']) : '';
$weekday = (isset($_POST['weekday'])) ? sanitize_text_field($_POST['weekday']) : '';

$breaktime = get_user_meta($user_id, 'breaktime', true);

if(empty($breaktime)){
$breaktime = array();
}

if($break_start_time != "" && $break_end_time != ""){
	$breaktime[$weekday][] = $break_start_time.'-'.$break_end_time;
}
update_user_meta($user_id, 'breaktime', $breaktime);
$breaktimes = get_user_meta($user_id, 'breaktime', true);

$breaktime = get_user_meta($user_id, 'breaktime', true);
$html = '';
if(!empty($breaktime[$weekday])){
	$html .= '<ul class="m-a0">';
	foreach($breaktime[$weekday] as $breakslot){
		$break = explode('-',$breakslot);
		$html .= '<li data-breakslot="'.$breakslot.'" data-weekday="'.esc_attr($weekday).'">'.date('h:i a',strtotime($break[0])).esc_html__(' TO ', 'service-finder').date('h:i a',strtotime($break[1])).' <span class="working-hours-remove"><i class="fa fa-close"></i></span></li>';
	}
	$html .= '</ul>';
}

$success = array(
		'status' => 'success',
		'breaktimes' => $breaktimes,
		'breaktime_html' => $html,
		'suc_message' => esc_html__('Break time addedd successfully.', 'service-finder'),
		);
echo json_encode($success);

exit(0);
}

add_action('wp_ajax_remove_breakslot', 'service_finder_remove_breakslot');
function service_finder_remove_breakslot(){
$user_id = (isset($_POST['user_id'])) ? sanitize_text_field($_POST['user_id']) : '';
$weekday = (isset($_POST['weekday'])) ? sanitize_text_field($_POST['weekday']) : '';
$breakslot = (isset($_POST['breakslot'])) ? sanitize_text_field($_POST['breakslot']) : '';

$breaktime = get_user_meta($user_id, 'breaktime', true);
if(!empty($breaktime[$weekday])){
	foreach($breaktime[$weekday] as $key => $slot){
		if($slot == $breakslot){
			unset($breaktime[$weekday][$key]);
			break;
		}
	}
}	

update_user_meta($user_id, 'breaktime', $breaktime);

$success = array(
		'status' => 'success',
		'suc_message' => esc_html__('Break slot removed successfully.', 'service-finder'),
		);
echo json_encode($success);
exit;
}

add_action('wp_ajax_businesshours_active_or_inactive', 'service_finder_businesshours_active_or_inactive');
function service_finder_businesshours_active_or_inactive(){

$providerid = (isset($_POST['providerid'])) ? sanitize_text_field($_POST['providerid']) : '';
$status = (isset($_POST['status'])) ? sanitize_text_field($_POST['status']) : '';

update_user_meta($providerid, 'business_hours_active_inactive', $status);

if($status == 'active'){
	$msg = esc_html__('Business hours activated successfully.', 'service-finder');
}else{
	$msg = esc_html__('Business hours deactivated successfully.', 'service-finder');
}

$success = array(
		'status' => 'success',
		'suc_message' => $msg,
		);
echo json_encode($success);
exit;
}