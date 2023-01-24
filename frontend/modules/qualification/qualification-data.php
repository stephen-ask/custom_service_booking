<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/

/*Add qualificationajax call*/
add_action('wp_ajax_add_qualification', 'service_finder_add_qualification');
function service_finder_add_qualification(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/qualification/Qualification.php';
$addQualification = new SERVICE_FINDER_Qualification();
$addQualification->service_finder_addQualification($_POST);
exit;
}

/*Get Qualification ajax call*/
add_action('wp_ajax_get_qualification', 'service_finder_get_qualification');
function service_finder_get_qualification(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/qualification/Qualification.php';
$getQualification = new SERVICE_FINDER_Qualification();
$getQualification->service_finder_getQualification($_POST);
exit;
}

/*Delete qualificationajax call*/
add_action('wp_ajax_delete_qualification', 'service_finder_delete_qualification');
function service_finder_delete_qualification(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/qualification/Qualification.php';
$deleteQualification = new SERVICE_FINDER_Qualification();
$deleteQualification->service_finder_deleteQualification($_POST);
exit;
}

/*Load qualificationajax call*/
add_action('wp_ajax_load_qualification', 'service_finder_load_qualification');
function service_finder_load_qualification(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/qualification/Qualification.php';
$loadQualification = new SERVICE_FINDER_Qualification();
$loadQualification->service_finder_loadQualification($_POST);
exit;
}

/*Load qualificationajax call*/
add_action('wp_ajax_update_qualification', 'service_finder_update_qualification');
function service_finder_update_qualification(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/qualification/Qualification.php';
$updateQualification = new SERVICE_FINDER_Qualification();
$updateQualification->service_finder_updateQualification($_POST);
exit;
}


