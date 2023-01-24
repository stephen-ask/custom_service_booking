<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/

/*Add region ajax call*/
add_action('wp_ajax_add_service_region', 'service_finder_add_service_region');

function service_finder_add_service_region(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/service-area/ServiceArea.php';
$addRegion = new SERVICE_FINDER_ServiceArea();
$addRegion->service_finder_addServiceRegion($_POST);
exit;
}

/*Get Service region ajax call*/
add_action('wp_ajax_get_serviceregions', 'service_finder_get_serviceregions');

function service_finder_get_serviceregions(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/service-area/ServiceArea.php';
$getRegions = new SERVICE_FINDER_ServiceArea();
$getRegions->service_finder_getServicesRegion($_POST);
exit;
}

/*Change region status*/
add_action('wp_ajax_change_region_status', 'service_finder_change_region_status');

function service_finder_change_region_status(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/service-area/ServiceArea.php';
$getRegions = new SERVICE_FINDER_ServiceArea();
$getRegions->service_finder_change_region_status($_POST);
exit;
}

/*Change zipcode status*/
add_action('wp_ajax_change_zipcode_status', 'service_finder_change_zipcode_status');

function service_finder_change_zipcode_status(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/service-area/ServiceArea.php';
$getRegions = new SERVICE_FINDER_ServiceArea();
$getRegions->service_finder_change_zipcode_status($_POST);
exit;
}

/*Delete service region ajax call*/
add_action('wp_ajax_delete_serviceregion', 'service_finder_delete_serviceregion');

function service_finder_delete_serviceregion(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/service-area/ServiceArea.php';
$deleteRegion = new SERVICE_FINDER_ServiceArea();
$deleteRegion->service_finder_deleteRegions($_POST);
exit;
}

/*Add Service area ajax call*/
add_action('wp_ajax_add_service_area', 'service_finder_add_service_area');
add_action('wp_ajax_nopriv_add_service_area', 'service_finder_add_service_area');

function service_finder_add_service_area(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/service-area/ServiceArea.php';
$addZipcode = new SERVICE_FINDER_ServiceArea();
$addZipcode->service_finder_addServiceArea($_POST);
exit;
}

/*Get Service area ajax call*/
add_action('wp_ajax_get_serviceareas', 'service_finder_get_serviceareas');
add_action('wp_ajax_nopriv_get_serviceareas', 'service_finder_get_serviceareas');

function service_finder_get_serviceareas(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/service-area/ServiceArea.php';
$getZipcodes = new SERVICE_FINDER_ServiceArea();
$getZipcodes->service_finder_getServicesArea($_POST);
exit;
}

/*Delete service area ajax call*/
add_action('wp_ajax_delete_servicearea', 'service_finder_delete_servicearea');
add_action('wp_ajax_nopriv_delete_servicearea', 'service_finder_delete_servicearea');

function service_finder_delete_servicearea(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/service-area/ServiceArea.php';
$deleteZipcode = new SERVICE_FINDER_ServiceArea();
$deleteZipcode->service_finder_deleteZipcodes($_POST);
exit;
}

/*Relaod service area ajax call*/
add_action('wp_ajax_reaload_servicearea', 'service_finder_reaload_servicearea');
add_action('wp_ajax_nopriv_reaload_servicearea', 'service_finder_reaload_servicearea');

function service_finder_reaload_servicearea(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/service-area/ServiceArea.php';
$reloadZipcode = new SERVICE_FINDER_ServiceArea();
$reloadZipcode->service_finder_reloadZipcodes($_POST);
exit;
} 