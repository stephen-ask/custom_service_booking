<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/
require_once('../../../../../../wp-load.php');
/*Validate zipcode at the time of booking*/
header('Content-type: application/json');

$wpdb = service_finder_plugin_global_vars('wpdb');
$service_finder_Tables = service_finder_plugin_global_vars('service_finder_Tables');

$provider_id = (!empty($_GET['provider_id'])) ? $_GET['provider_id']  : '';

$settings = service_finder_getProviderSettings($provider_id);

$valid = true;		

if($settings['booking_basedon'] == 'open'){
$valid = true;
}else{

		$sql = $wpdb->prepare('SELECT id FROM '.$service_finder_Tables->service_area.' WHERE provider_id = %d AND zipcode = "%s" AND status = "active"',$_GET['provider_id'],$_POST['zipcode']);
		
		$res = $wpdb->get_row($sql);
		
		if(!empty($res)){
		
			$valid = true;
			
		}else{

			$valid = false;
		}
}		

echo json_encode(array(
    'valid' => $valid,
)); 