<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/

/*Add new service ajax call*/
add_action('wp_ajax_add_new_service', 'service_finder_add_new_service');
add_action('wp_ajax_nopriv_add_new_service', 'service_finder_add_new_service');

function service_finder_add_new_service(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/myservices/MyService.php';
$addService = new SERVICE_FINDER_MyService();
$addService->service_finder_addServices($_POST);
exit;
}

/*Edit service ajax call*/
add_action('wp_ajax_edit_service', 'service_finder_edit_service');
add_action('wp_ajax_nopriv_edit_service', 'service_finder_edit_service');

function service_finder_edit_service(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/myservices/MyService.php';
$editService = new SERVICE_FINDER_MyService();
$editService->service_finder_editService($_POST);
exit;
}

/*Load service ajax call*/
add_action('wp_ajax_load_service', 'service_finder_load_service');
add_action('wp_ajax_nopriv_load_service', 'service_finder_load_service');

function service_finder_load_service(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/myservices/MyService.php';
$loadService = new SERVICE_FINDER_MyService();
$loadService->service_finder_loadService($_POST);
exit;
}

/*Get service ajax call*/
add_action('wp_ajax_get_services', 'service_finder_get_services');
add_action('wp_ajax_nopriv_get_services', 'service_finder_get_services');

function service_finder_get_services(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/myservices/MyService.php';
$getService = new SERVICE_FINDER_MyService();
$getService->service_finder_getServices($_POST);
exit;
}

/*Delete service ajax call*/
add_action('wp_ajax_delete_services', 'service_finder_delete_services');
add_action('wp_ajax_nopriv_delete_services', 'service_finder_delete_services');

function service_finder_delete_services(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/myservices/MyService.php';
$deleteService = new SERVICE_FINDER_MyService();
$deleteService->service_finder_deleteServices();
exit;
}

/*Change service status*/
add_action('wp_ajax_change_service_status', 'service_finder_change_service_status');

function service_finder_change_service_status(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/myservices/MyService.php';
$changeStatus = new SERVICE_FINDER_MyService();
$changeStatus->service_finder_change_service_status($_POST);
exit;
}

/*Add new group*/
add_action('wp_ajax_add_new_group', 'service_finder_add_new_group');
add_action('wp_ajax_nopriv_add_new_group', 'service_finder_add_new_group');

function service_finder_add_new_group(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/myservices/MyService.php';
$addGroup = new SERVICE_FINDER_MyService();
$addGroup->service_finder_addGroup($_POST);
exit;
}

/*Delete group*/
add_action('wp_ajax_delete_group', 'service_finder_delete_group');
add_action('wp_ajax_nopriv_delete_group', 'service_finder_delete_group');
function service_finder_delete_group(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/myservices/MyService.php';
$deleteGroup = new SERVICE_FINDER_MyService();
$deleteGroup->service_finder_deleteGroup($_POST);
exit;
}