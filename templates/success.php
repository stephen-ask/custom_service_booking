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

$created = (isset($_GET['created'])) ? $_GET['created'] : '';
$upgrade = (isset($_GET['upgrade'])) ? $_GET['upgrade'] : '';

if($created == 'success'){
$msg = (!empty($service_finder_options['provider-signup-successfull'])) ? $service_finder_options['provider-signup-successfull'] : esc_html__('Congratulations! Your account has been created successfully.', 'service-finder');
$html = $msg;
}elseif($upgrade == 'success'){
$msg = (!empty($service_finder_options['provider-upgrade-successfull'])) ? $service_finder_options['provider-upgrade-successfull'] : esc_html__('Congratulations! Your account has been upgrade successfully.', 'service-finder');
$html = $msg;
}
?>
