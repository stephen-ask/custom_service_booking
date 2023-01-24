<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/
?>
<?php 
require_once('../../../../wp-load.php');
$service_finder_Tables = service_finder_plugin_global_vars('service_finder_Tables');

$array_to_csv[] = array(
	'Bank Account Holder\'s Name',
	'Bank Account Number/IBAN',
	'Swift Code',
	'Bank Name in Full',
	'Bank Branch City',
	'Bank Branch Country',
);

$providers = $wpdb->get_results('SELECT wp_user_id FROM '.$service_finder_Tables->providers);

if(!empty($providers)){
foreach($providers as $provider){
	$userid = $provider->wp_user_id;
	$bank_account_holder_name = get_user_meta($userid,'bank_account_holder_name',true);
	$bank_account_number = get_user_meta($userid,'bank_account_number',true);
	$swift_code = get_user_meta($userid,'swift_code',true);
	$bank_name = get_user_meta($userid,'bank_name',true);
	$bank_branch_city = get_user_meta($userid,'bank_branch_city',true);
	$bank_branch_country = get_user_meta($userid,'bank_branch_country',true);
	if($bank_account_holder_name != "" || $bank_account_holder_name != "" || $bank_account_holder_name != "" || $bank_account_holder_name != "" || $bank_account_holder_name != "" || $bank_account_holder_name != ""){
	$array_to_csv[] = array(
		$bank_account_holder_name,
		$bank_account_number,
		$swift_code,
		$bank_name,
		$bank_branch_city,
		$bank_branch_country,
	);
	}
}
}

service_finder_convert_to_csv($array_to_csv, 'bankinfo.csv', ',');


