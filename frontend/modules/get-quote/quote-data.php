<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/*Get Quote Ajax Call*/
add_action('wp_ajax_get_quotation', 'service_finder_get_quotation');
add_action('wp_ajax_nopriv_get_quotation', 'service_finder_get_quotation');

function service_finder_get_quotation(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/get-quote/GetQuote.php';
$reqQuote = new SERVICE_FINDER_GetQuote();
$provider_id = (!empty($_POST['provider_id'])) ? $_POST['provider_id'] : '';
$proid = (!empty($_POST['proid'])) ? $_POST['proid'] : '';
$proid = ($provider_id != "") ? $provider_id : $proid;
$reqQuote->service_finder_get_quote_mail($_POST);
exit;
} 

/*Get quotations ajax call*/
add_action('wp_ajax_get_quotations', 'service_finder_get_quotations');
function service_finder_get_quotations(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/get-quote/GetQuote.php';
$getquotations = new SERVICE_FINDER_GetQuote();
$getquotations->service_finder_get_quotations($_POST);
exit;
}

/*Get quotations ajax call*/
add_action('wp_ajax_get_customer_quotation', 'service_finder_get_customer_quotation');
function service_finder_get_customer_quotation(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/get-quote/GetQuote.php';
$getquotations = new SERVICE_FINDER_GetQuote();
$getquotations->service_finder_get_customer_quotations($_POST);
exit;
}

/*Load quote reply ajax call*/
add_action('wp_ajax_load_quote_reply', 'service_finder_load_quote_reply');
function service_finder_load_quote_reply(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/get-quote/GetQuote.php';
$loadquotereply = new SERVICE_FINDER_GetQuote();
$loadquotereply->service_finder_load_quote_reply($_POST);
exit;
}

/*Update quote reply ajax call*/
add_action('wp_ajax_update_quote_reply', 'service_finder_update_quote_reply');
function service_finder_update_quote_reply(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/get-quote/GetQuote.php';
$updatequotereply = new SERVICE_FINDER_GetQuote();
$updatequotereply->service_finder_update_quote_reply($_POST);
exit;
}

/*View Provider Booking Details Ajax Call*/
add_action('wp_ajax_quotation_details', 'service_finder_quotation_details');
function service_finder_quotation_details(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/get-quote/GetQuote.php';
$viewquotation = new SERVICE_FINDER_GetQuote();
$viewquotation->service_finder_view_quotation_details($_POST);
exit;
}

/*View Provider Booking Details Ajax Call*/
add_action('wp_ajax_view_quote_description', 'service_finder_view_customer_quote_description');
function service_finder_view_customer_quote_description(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/get-quote/GetQuote.php';
$viewquotation = new SERVICE_FINDER_GetQuote();
$viewquotation->service_finder_customer_quote_description($_POST);
exit;
}

/*View Provider Booking Details Ajax Call*/
add_action('wp_ajax_replies_listing', 'service_finder_replies_listing');
function service_finder_replies_listing(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/get-quote/GetQuote.php';
$replieslisting = new SERVICE_FINDER_GetQuote();
$replieslisting->service_finder_view_replies_listing($_POST);
exit;
}

/*View Provider Booking Details Ajax Call*/
add_action('wp_ajax_get_quote_reply_description', 'service_finder_get_quote_reply_description');
function service_finder_get_quote_reply_description(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/get-quote/GetQuote.php';
$repliesdesc = new SERVICE_FINDER_GetQuote();
$repliesdesc->service_finder_reply_description($_POST);
exit;
}

