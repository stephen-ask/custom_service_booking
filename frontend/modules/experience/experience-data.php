<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/

/*Add experience ajax call*/
add_action('wp_ajax_add_experience', 'service_finder_add_experience');
function service_finder_add_experience(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/experience/Experience.php';
$addexperience = new SERVICE_FINDER_Experience();
$addexperience->service_finder_addExperience($_POST);
exit;
}

/*Get experience ajax call*/
add_action('wp_ajax_get_experience', 'service_finder_fn_get_experience');
function service_finder_fn_get_experience(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/experience/Experience.php';
$getExperience = new SERVICE_FINDER_Experience();
$getExperience->service_finder_getExperience($_POST);
exit;
}

/*Delete experience ajax call*/
add_action('wp_ajax_delete_experience', 'service_finder_delete_experience');
function service_finder_delete_experience(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/experience/Experience.php';
$deleteexperience = new SERVICE_FINDER_Experience();
$deleteexperience->service_finder_deleteExperience($_POST);
exit;
}

/*Load experience ajax call*/
add_action('wp_ajax_load_experience', 'service_finder_load_experience');
function service_finder_load_experience(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/experience/Experience.php';
$loadexperience = new SERVICE_FINDER_Experience();
$loadexperience->service_finder_loadexperience($_POST);
exit;
}

/*Load experience ajax call*/
add_action('wp_ajax_update_experience', 'service_finder_update_experience');
function service_finder_update_experience(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/experience/Experience.php';
$updateexperience = new SERVICE_FINDER_Experience();
$updateexperience->service_finder_updateexperience($_POST);
exit;
}


