<?php 
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/
global $current_user;
if ( !current_user_can( 'edit_user', $current_user->ID ) ) { return false; }

$signup_user_role = (isset($_POST['role'])) ? esc_html($_POST['role']) : '';
$wpdb = service_finder_plugin_global_vars('wpdb');
$service_finder_Tables = service_finder_plugin_global_vars('service_finder_Tables');
$service_finder_options = get_option('service_finder_options');


if(strtolower($signup_user_role) == 'provider'){

$signup_address = (isset($_POST['signup_address'])) ? $_POST['signup_address'] : '';
$signup_city = (isset($_POST['signup_city'])) ? esc_html($_POST['signup_city']) : '';
$signup_state = (isset($_POST['signup_state'])) ? esc_html($_POST['signup_state']) : '';
$signup_country = (isset($_POST['signup_country'])) ? esc_html($_POST['signup_country']) : '';

$full_address = $signup_address.' '.$signup_city.' '.$signup_country;

$userInfo = service_finder_getUserInfo($user_id);

$currentaddress = $userInfo['address'];
$currentcity = $userInfo['city'];
$currentstate = $userInfo['state'];
$currentcountry = $userInfo['country'];
$currentlat = $userInfo['lat'];
$currentlong = $userInfo['long'];

if($currentlat == '' || $currentlong = ''){
$address = str_replace(" ","+",$full_address);
$res = service_finder_getLatLong($address);
$lat = $res['lat'];
$lng = $res['lng'];
}elseif($currentaddress != $signup_addres || $currentcity != $signup_city || $currentstate != $signup_state || $currentcountry != $signup_country ){
$address = str_replace(" ","+",$full_address);
$res = service_finder_getLatLong($address);
$lat = $res['lat'];
$lng = $res['lng'];
}else{
$lat = $currentlat;
$lng = $currentlong;
}


$fname = (!empty($_POST['first_name'])) ? esc_html($_POST['first_name']) : '';
$lname = (!empty($_POST['last_name'])) ? esc_html($_POST['last_name']) : '';
$signup_company_name = (!empty($_POST['signup_company_name'])) ? esc_html($_POST['signup_company_name']) : '';

$fullname = $fname.' '.$lname;

if(service_finder_get_data($service_finder_options,'profileurlby') == 'companyname' && !empty($_POST['signup_company_name'])){
	$nicename = sanitize_text_field($_POST['signup_company_name']);
}elseif(service_finder_get_data($service_finder_options,'profileurlby') == 'username' && !empty($_POST['user_login'])){
	$nicename = sanitize_text_field($_POST['user_login']);
}else{
	$nicename = $fname.' '.$lname;
}

if($signup_company_name != "" && $signup_company_name != " "){

$user_id = wp_update_user( array( 'ID' => $user_id, 'user_nicename' => service_finder_create_user_name($nicename) ) );

$comment_postid = get_user_meta($user_id, 'comment_post', true);
				
$comment_post = array(
	'ID' => $comment_postid,
	'post_name' => service_finder_create_user_name($nicename),
	'post_title' => $nicename,
	'post_status' => 'publish',
	'post_type' => 'sf_comment_rating',
	'comment_status' => 'open',
);

wp_update_post($comment_post);

}

$primarycategory = (!empty($_POST['signup_category'])) ? esc_html($_POST['signup_category']) : '';

$existingprimarycategory = get_user_meta($user_id,'primary_category',true);


$existing_category = $userInfo['category'];
$existingcatarr = explode(',',$existing_category);

if($primarycategory == $existingprimarycategory){
	$updated_category = $userInfo['category'];
}else{
	if(!in_array($primarycategory,$existingcatarr)){
	$tempcategories = str_replace(','.$existingprimarycategory,'',$userInfo['category']);
	$updated_category = $tempcategories.','.$primarycategory;
	}else{
	$updated_category = $userInfo['category'];
	}
}

$data = array(

			'company_name' => (!empty($_POST['signup_company_name'])) ? esc_html($_POST['signup_company_name']) : '',
			
			'full_name' => $fname.' '.$lname,
			
			'avatar_id' => (!empty($_POST['profile_picture_id'])) ? esc_html($_POST['profile_picture_id']) : '',
			
			'bio' => (!empty($_POST['description'])) ? $_POST['description'] : '',

			'email' => (!empty($_POST['email'])) ? esc_html($_POST['email']) : '',
			
			'category_id' => $updated_category,

			'address' => (!empty($_POST['signup_address'])) ? esc_html($_POST['signup_address']) : '',

			'apt' => (!empty($_POST['signup_apt'])) ? esc_html($_POST['signup_apt']) : '',

			'city' => (!empty($_POST['signup_city'])) ? esc_html($_POST['signup_city']) : '',

			'state' => (!empty($_POST['signup_state'])) ? esc_html($_POST['signup_state']) : '',

			'zipcode' => (!empty($_POST['signup_zipcode'])) ? esc_html($_POST['signup_zipcode']) : '',

			'country' => (!empty($_POST['signup_country'])) ? esc_html($_POST['signup_country']) : '',
			
			'website' => (!empty($_POST['url'])) ? esc_html($_POST['url']) : '',
			
			'lat' => $lat,
			
			'long' => $lng,

		);
$where = array(
			'wp_user_id' => $user_id
			);
					
$wpdb->update($service_finder_Tables->providers,wp_unslash($data),$where);

update_user_meta($user_id,'primary_category',$primarycategory);

$userslots = get_user_meta($user_id, 'timeslots', true);
if($userslots == ""){
	$start_time = array( '08:00:00','08:00:00','08:00:00','08:00:00','08:00:00','','' );
	$end_time = array( '18:00:00','18:00:00','18:00:00','18:00:00','18:00:00','','' );
	$arraymap = array_map(null,$start_time,$end_time);
	
	foreach ($arraymap as $key => $value) {
	if($value[0] != ""){
	$timeslots[$key] = $value[0].'-'.$value[1];
	}else{
	$timeslots[$key] = 'off';
	}
	}
	update_user_meta($user_id, 'timeslots', $timeslots);
}

$memberData = array(

'member_name' => $fname.' '.$lname,

'email' => (!empty($_POST['email'])) ? esc_html($_POST['email']) : '',

'is_admin' => 'yes',

);

$where = array(
			'admin_wp_id' => $user_id
			);

$wpdb->update($service_finder_Tables->team_members,wp_unslash($memberData),$where);

$roleNum = 1;
$rolePrice = '0';
$free = true;
$price = '0';
$packageName = '';

$getpackage = get_user_meta($user_id,'provider_role',true);
$get_activation_time = get_user_meta($user_id,'provider_activation_time',true);
service_finder_resetProviderPackage($user_id);

$role = (!empty($_POST['provider-role'])) ? esc_html($_POST['provider-role']) : '';
if($role == ''){
delete_user_meta($user_id,'provider_role' );
update_user_meta( $user_id, 'provider_activation_time', array( 'role' => '', 'time' => time()) );
}

if(isset($_POST['provider-role'])){
	$role = (!empty($_POST['provider-role'])) ? esc_html($_POST['provider-role']) : '';
	if (($role == "package_0") || ($role == "package_1") || ($role == "package_2") || ($role == "package_3")){
	$roleNum = intval(substr($role, 8));
	switch ($role) {
		case "package_0":
			if(isset($service_finder_options['package0-expday'])) {
				$expire_limit = $service_finder_options['package0-expday'];
			}
			break;
		case "package_1":
			if(isset($service_finder_options['package1-price'])) {
				$free = false;
				$packageName = $service_finder_options['package1-name'];
				$expire_limit = $service_finder_options['package1-expday'];
				$price = trim($service_finder_options['package1-price']);								
			}
			break;
		case "package_2":
			if(isset($service_finder_options['package2-price'])) {
				$expire_limit = $service_finder_options['package2-expday'];
				$free = false;
				$packageName = $service_finder_options['package2-name'];
				$price = trim($service_finder_options['package2-price']);								
			}
			break;
		case "package_3":
			if(isset($service_finder_options['package3-price'])) {
				$expire_limit = $service_finder_options['package3-expday'];
				$free = false;
				$packageName = $service_finder_options['package3-name'];
				$price = trim($service_finder_options['package3-price']);								
			}
			break;
		default:
			break;
	}

	// free
	$user = new WP_User( $user_id );
	$user->set_role('Provider');
	
	delete_user_meta($user_id, 'current_provider_status');
	
	if($getpackage != $role){
	update_user_meta( $user_id, 'provider_activation_time', array( 'role' => $role, 'time' => time()) );
	}else{
	update_user_meta( $user_id, 'provider_activation_time', $get_activation_time );
	}
	$roleNum = intval(substr($role, 8));
	$roleName = $service_finder_options['package'.$roleNum.'-name'];
	if($expire_limit > 0){
	update_user_meta( $user_id, 'expire_limit', $expire_limit);
	}
	update_user_meta( $user_id, 'provider_role', $role );
	update_user_meta( $user_id, 'updated_by', 'admin' );
	
	if($roleNum == 0){
		update_user_meta($user_id, 'trial_package', 'yes');
	}else{
		delete_user_meta($user_id, 'trial_package');
	}
	service_finder_update_job_limit($user_id);
	
	$access_only_claimed_users = (!empty($_POST['access_only_claimed_users'])) ? esc_html($_POST['access_only_claimed_users']) : '';
	update_user_meta( $user_id, 'access_only_claimed_users', $access_only_claimed_users );
	
	$is_vendor = (!empty($_POST['is_vendor'])) ? esc_html($_POST['is_vendor']) : '';
	
	if($is_vendor == 'yes'){
	service_finder_meke_user_vendor($user_id);
	}else{
	service_finder_remove_user_vendor($user_id);
	}
	
	$args = array(
			'username' => (!empty($_POST['user_login'])) ? esc_html($_POST['user_login']) : '',
			'email' => (!empty($_POST['email'])) ? esc_html($_POST['email']) : '',
			'address' => $userInfo['address'],
			'city' => $userInfo['city'],
			'country' => $userInfo['country'],
			'zipcode' => $userInfo['zipcode'],
			'category' => $userInfo['categoryname'],
			'package_name' => $roleName,
			'payment_type' => 'By Admin'
			);
	
	$user_login = (!empty($_POST['user_login'])) ? esc_html($_POST['user_login']) : '';
	$email = (!empty($_POST['email'])) ? esc_html($_POST['email']) : '';
	
	$user = get_user_by( 'email', $email );
	
	if($getpackage != $role){
	service_finder_sendUpgradeMailToUser($user->user_login,$email,$args);
	service_finder_sendProviderUpgradeEmail($args);
	}

	}
}

}elseif(strtolower($signup_user_role) == 'customer'){
	$data = array(
				'avatar_id' => (!empty($_POST['profile_picture_id'])) ? esc_html($_POST['profile_picture_id']) : ''
			);
	$where = array(
			'wp_user_id' => $user_id
			);
					
	$wpdb->update($service_finder_Tables->customers_data,wp_unslash($data),$where);		
}

	
?>