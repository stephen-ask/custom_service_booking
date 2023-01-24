<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/

/*Add certificates ajax call*/
add_action('wp_ajax_add_certificates', 'service_finder_add_certificates');
function service_finder_add_certificates(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/certificates/Certificates.php';
$addCertificates = new SERVICE_FINDER_Certificates();
$addCertificates->service_finder_addCertificates($_POST);
exit;
}

/*Get Certificates ajax call*/
add_action('wp_ajax_get_certificates', 'service_finder_fn_get_certificates');
function service_finder_fn_get_certificates(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/certificates/Certificates.php';
$getCertificates = new SERVICE_FINDER_Certificates();
$getCertificates->service_finder_getCertificates($_POST);
exit;
}

/*Delete certificates ajax call*/
add_action('wp_ajax_delete_certificates', 'service_finder_delete_certificates');
function service_finder_delete_certificates(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/certificates/Certificates.php';
$deleteCertificates = new SERVICE_FINDER_Certificates();
$deleteCertificates->service_finder_deleteCertificates($_POST);
exit;
}

/*Load certificates ajax call*/
add_action('wp_ajax_load_certificates', 'service_finder_load_certificates');
function service_finder_load_certificates(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/certificates/Certificates.php';
$loadCertificates = new SERVICE_FINDER_Certificates();
$loadCertificates->service_finder_loadCertificates($_POST);
exit;
}

/*Load certificates ajax call*/
add_action('wp_ajax_update_certificates', 'service_finder_update_certificates');
function service_finder_update_certificates(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/certificates/Certificates.php';
$updateCertificates = new SERVICE_FINDER_Certificates();
$updateCertificates->service_finder_updateCertificates($_POST);
exit;
}


