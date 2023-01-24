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
$signup_user_role = (isset($_POST['role'])) ? $_POST['role'] : '';
$wpdb = service_finder_plugin_global_vars('wpdb');
$service_finder_Tables = service_finder_plugin_global_vars('service_finder_Tables');
$service_finder_options = get_option('service_finder_options');
if(strtolower($signup_user_role) == 'provider'){
$signup_address = (isset($_POST['signup_address'])) ? $_POST['signup_address'] : '';
$signup_city = (isset($_POST['signup_city'])) ? esc_html($_POST['signup_city']) : '';
$signup_country = (isset($_POST['signup_country'])) ? esc_html($_POST['signup_country']) : '';
$send_user_notification = (isset($_POST['send_user_notification'])) ? esc_html($_POST['send_user_notification']) : '';

$full_address = $signup_address.' '.$signup_city.' '.$signup_country;

$address = str_replace(" ","+",$full_address);
$res = service_finder_getLatLong($address);
$lat = $res['lat'];
$lng = $res['lng'];

if($service_finder_options['account-moderation']){
	$adminapproval = 'pending';
}else{
	$adminapproval = 'approved';
}

$fname = (!empty($_POST['first_name'])) ? esc_html($_POST['first_name']) : '';
$lname = (!empty($_POST['last_name'])) ? esc_html($_POST['last_name']) : '';
$signup_company_name = (!empty($_POST['signup_company_name'])) ? esc_html($_POST['signup_company_name']) : '';
$lname = (!empty($_POST['last_name'])) ? esc_html($_POST['last_name']) : '';

$fullname = $fname.' '.$lname;

if(service_finder_get_data($service_finder_options,'profileurlby') == 'companyname' && !empty($_POST['signup_company_name'])){
	$nicename = sanitize_text_field($_POST['signup_company_name']);
}elseif(service_finder_get_data($service_finder_options,'profileurlby') == 'username' && !empty($_POST['user_login'])){
	$nicename = sanitize_text_field($_POST['user_login']);
}else{
	$nicename = $fname.' '.$lname;
}


if($nicename != "" && $nicename != " "){
	$user_id = wp_update_user( array( 'ID' => $user_id, 'user_nicename' => $nicename ) );
	
	$comment_post = array(
		'post_title' => $nicename,
		'post_status' => 'publish',
		'post_type' => 'sf_comment_rating',
		'comment_status' => 'open',
	);
	
	$postid = wp_insert_post( $comment_post );
}else{
	$user_login = (!empty($_POST['user_login'])) ? esc_html($_POST['user_login']) : '';
	
	$comment_post = array(
		'post_title' => $user_login,
		'post_status' => 'publish',
		'post_type' => 'sf_comment_rating',
		'comment_status' => 'open',
	);
	
	$postid = wp_insert_post( $comment_post );
}

update_user_meta($user_id, 'comment_post', $postid);

$initialamount = 0;
update_user_meta($user_id,'_sf_wallet_amount',$initialamount);

$data = array(

			'wp_user_id' => $user_id,

			'admin_moderation' => $adminapproval,
			
			'company_name' => (!empty($_POST['signup_company_name'])) ? esc_html($_POST['signup_company_name']) : '',
			
			'full_name' => $fname.' '.$lname,
			
			'avatar_id' => (!empty($_POST['profile_picture_id'])) ? esc_html($_POST['profile_picture_id']) : '',
			
			'bio' => (!empty($_POST['description'])) ? $_POST['description'] : '',

			'email' => (!empty($_POST['email'])) ? esc_html($_POST['email']) : '',

			'category_id' => (!empty($_POST['signup_category'])) ? esc_html($_POST['signup_category']) : '',

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
$wpdb->insert($service_finder_Tables->providers,wp_unslash($data));

$paid_booking = (!empty($service_finder_options['paid-booking'])) ? $service_finder_options['paid-booking'] : '';
$free_booking = (!empty($service_finder_options['free-booking'])) ? $service_finder_options['free-booking'] : '';

if($paid_booking && $free_booking){
$default_booking_option = 'free';
}elseif($paid_booking && !$free_booking){
$default_booking_option = 'paid';
}elseif(!$paid_booking && $free_booking){
$default_booking_option = 'free';
}else{
$default_booking_option = 'free';
}

if(service_finder_availability_method($user_id) == 'timeslots')
{
	service_finder_create_default_timeslots($user_id);
}elseif(service_finder_availability_method($user_id) == 'starttime')
{
	service_finder_create_default_starttime($user_id);
}

$userslots = get_user_meta($user_id, 'timeslots', true);
if($userslots == ""){
	service_finder_set_default_business_hours($user_id);
}

service_finder_set_default_booking_settings($user_id);

$primarycategory = (!empty($_POST['signup_category'])) ? esc_html($_POST['signup_category']) : '';
update_user_meta($user_id,'primary_category',$primarycategory);



$memberData = array(

'member_name' => $fname.' '.$lname,

'email' => (!empty($_POST['email'])) ? esc_html($_POST['email']) : '',

'admin_wp_id' => esc_html($user_id),

'is_admin' => 'yes',

);

$wpdb->insert($service_finder_Tables->team_members,wp_unslash($memberData));

$roleNum = 1;
$rolePrice = '0';
$free = true;
$price = '0';
$packageName = '';

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
	
	update_user_meta( $user_id, 'provider_activation_time', array( 'role' => $role, 'time' => time()) );
	$roleNum = intval(substr($role, 8));
	$roleName = $service_finder_options['package'.$roleNum.'-name'];
	if($expire_limit > 0){
	update_user_meta( $user_id, 'expire_limit', $expire_limit);
	}
	update_user_meta( $user_id, 'provider_role', $role );
	update_user_meta( $user_id, 'created_by', 'admin' );
	
	if($roleNum == 0){
		update_user_meta($user_id, 'trial_package', 'yes');
	}
	
	$access_only_claimed_users = (!empty($_POST['access_only_claimed_users'])) ? esc_html($_POST['access_only_claimed_users']) : '';
	update_user_meta( $user_id, 'access_only_claimed_users', $access_only_claimed_users );
	
	if( class_exists( 'WC_Vendors' ) && class_exists( 'WooCommerce' ) ) {
	$is_vendor = (!empty($_POST['is_vendor'])) ? esc_html($_POST['is_vendor']) : '';
	
	if($is_vendor == 'yes'){
	service_finder_meke_user_vendor($user_id);
	}else{
	service_finder_remove_user_vendor($user_id);
	}
	}
	
	$userInfo = service_finder_getUserInfo($user_id);
	$args = array(
			'username' => (!empty($_POST['user_login'])) ? esc_html($_POST['user_login']) : '',
			'email' => (!empty($_POST['email'])) ? esc_html($_POST['email']) : '',
			'address' => $userInfo['address'],
			'city' => $userInfo['city'],
			'country' => $userInfo['country'],
			'zipcode' => $userInfo['zipcode'],
			'category' => $userInfo['categoryname'],
			'package_name' => $roleName,
			'payment_type' => esc_html__( 'By Admin', 'service-finder' ),
			);
	
	service_finder_sendProviderEmail($args);
	$user_login = (!empty($_POST['user_login'])) ? esc_html($_POST['user_login']) : '';
	$email = (!empty($_POST['email'])) ? esc_html($_POST['email']) : '';
	
	if($send_user_notification){
	service_finder_sendRegMailToUser($user_login,$email);
	}
	
	$allowedjobapply = (!empty($service_finder_options['package'.$roleNum.'-job-apply'])) ? $service_finder_options['package'.$roleNum.'-job-apply'] : '';
	
	$period = (!empty($service_finder_options['job-apply-limit-period'])) ? $service_finder_options['job-apply-limit-period'] : '';
	$numberofweekmonth = (!empty($service_finder_options['job-apply-number-of-week-month'])) ? $service_finder_options['job-apply-number-of-week-month'] : 1;
	$numberofperiod = (!empty($service_finder_options['job-apply-number-of-week-month'])) ? $service_finder_options['job-apply-number-of-week-month'] : '';
	
	$startdate = date('Y-m-d h:i:s');
	
	if($period == 'weekly'){
		$freq = 7 * $numberofweekmonth;
		$expiredate = date('Y-m-d h:i:s', strtotime("+".$freq." days"));
	}elseif($period == 'monthly'){
		$freq = 30 * $numberofweekmonth;
		$expiredate = date('Y-m-d h:i:s', strtotime("+".$freq." days"));
	}
	
	$data = array(
			'provider_id' => $user_id,
			'free_limits' => $allowedjobapply,
			'available_limits' => $allowedjobapply,
			'membership_date' => $startdate,
			'start_date' => $startdate,
			'expire_date' => $expiredate,
			);
	
	$wpdb->insert($service_finder_Tables->job_limits,wp_unslash($data));

}
}

}elseif(strtolower($signup_user_role) == 'customer'){
	$data = array(
				'wp_user_id' => $user_id,
				'avatar_id' => (!empty($_POST['profile_picture_id'])) ? esc_html($_POST['profile_picture_id']) : ''
			);
	$wpdb->insert($service_finder_Tables->customers_data,wp_unslash($data));
	
	$initialamount = 0;
	update_user_meta($user_id,'_sf_wallet_amount',$initialamount);
	
	$allowedjobapply = (!empty($service_finder_options['default-job-post-limit'])) ? $service_finder_options['default-job-post-limit'] : '';
	
	$period = (!empty($service_finder_options['job-post-limit-period'])) ? $service_finder_options['job-post-limit-period'] : '';
	$numberofweekmonth = (!empty($service_finder_options['job-post-number-of-week-month'])) ? $service_finder_options['job-post-number-of-week-month'] : 1;
	$numberofperiod = (!empty($service_finder_options['job-post-number-of-week-month'])) ? $service_finder_options['job-post-number-of-week-month'] : '';
	
	$startdate = date('Y-m-d h:i:s');
	
	$expiredate = '';
	
	if($period == 'weekly'){
		$freq = 7 * $numberofweekmonth;
		$expiredate = date('Y-m-d h:i:s', strtotime("+".$freq." days"));
	}elseif($period == 'monthly'){
		$freq = 30 * $numberofweekmonth;
		$expiredate = date('Y-m-d h:i:s', strtotime("+".$freq." days"));
	}
	
	$data = array(
			'provider_id' => $user_id,
			'free_limits' => $allowedjobapply,
			'available_limits' => $allowedjobapply,
			'membership_date' => $startdate,
			'start_date' => $startdate,
			'expire_date' => $expiredate,
			);
	
	$wpdb->insert($service_finder_Tables->job_limits,wp_unslash($data));
}

	
?>