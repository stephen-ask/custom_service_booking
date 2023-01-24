<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/

$service_finder_options = get_option('service_finder_options');
$wpdb = service_finder_plugin_global_vars('wpdb');

$bookingcompleted = (isset($_GET['bookingcompleted'])) ? $_GET['bookingcompleted'] : '';
$featured = (isset($_GET['featured'])) ? $_GET['featured'] : '';

if($bookingcompleted == 'success'){
$msg = (!empty($service_finder_options['provider-booked'])) ? $service_finder_options['provider-booked'] : esc_html__('Congratulations! You have been booked a service successfully.', 'service-finder');
$html = $msg;
}elseif($featured == 'success'){
$msg = (!empty($service_finder_options['feature-payment'])) ? $service_finder_options['feature-payment'] : esc_html__('Congratulations! Your payment has been done successfully to be featured provider.', 'service-finder');
$html = $msg;
}

?>
