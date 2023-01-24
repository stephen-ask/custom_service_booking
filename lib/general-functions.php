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

/*Get Current User Info*/
function service_finder_getCurrentUserInfo(){
			global $wpdb, $service_finder_Tables;
			$currUser = wp_get_current_user(); 
			$fname = get_user_meta($currUser->ID,'first_name',true);
			$lname = get_user_meta($currUser->ID,'last_name',true);	
			
			if(service_finder_getUserRole($currUser->ID) == 'Provider'){
			
				/* Get Provider info */
				$sedateProvider = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' where wp_user_id = %d',$currUser->ID));
				
				$address = (!empty($sedateProvider->address)) ? $sedateProvider->address : '';
				$city = (!empty($sedateProvider->city)) ? $sedateProvider->city: '';
				$state = (!empty($sedateProvider->state)) ? $sedateProvider->state : '';
				$country = (!empty($sedateProvider->country)) ? $sedateProvider->country : '';
				
				$state = (!empty($state)) ? ', '.esc_html($state) : '';
						
				$fulladdress = $address.', '.$city.$state.', '.$country;
				
				$service_perform = get_user_meta($currUser->ID,'service_perform',true);
				$my_location = get_user_meta($currUser->ID,'my_location',true);
				$providerlat = get_user_meta($currUser->ID,'providerlat',true);
				$providerlng = get_user_meta($currUser->ID,'providerlng',true);	
				
				$userinfo = array(
							$currUser,
							'username' => $currUser->user_login,
							'company_name' => (!empty($sedateProvider->company_name)) ? $sedateProvider->company_name : '',
							'fname' => $fname,
							'lname' => $lname,
							'email' => (!empty($sedateProvider->email)) ? $sedateProvider->email : '',
							'avatar_id' => (!empty($sedateProvider->avatar_id)) ? $sedateProvider->avatar_id : '',
							'provider_id' => (!empty($sedateProvider->id)) ? $sedateProvider->id : '',
							'identity' => (!empty($sedateProvider->identity)) ? $sedateProvider->identity : '',
							'phone' => (!empty($sedateProvider->phone)) ? $sedateProvider->phone : '',
							'category' => (!empty($sedateProvider->category_id)) ? $sedateProvider->category_id : '',
							'categoryname' => service_finder_getCategoryName(get_user_meta($currUser->ID,'primary_category',true)),
							'tagline' => (!empty($sedateProvider->tagline)) ? $sedateProvider->tagline : '',
							'bio' => (!empty($sedateProvider->bio)) ? $sedateProvider->bio : '',
							'booking_description' => (!empty($sedateProvider->booking_description)) ? $sedateProvider->booking_description : '',
							'embeded_code' => (!empty($sedateProvider->embeded_code)) ? $sedateProvider->embeded_code : '',
							'mobile' => (!empty($sedateProvider->mobile)) ? $sedateProvider->mobile : '',
							'fax' => (!empty($sedateProvider->fax)) ? $sedateProvider->fax : '',
							'lat' => (!empty($sedateProvider->lat)) ? $sedateProvider->lat : '',
							'long' => (!empty($sedateProvider->long)) ? $sedateProvider->long : '',
							'facebook' => (!empty($sedateProvider->facebook)) ? $sedateProvider->facebook : '',
							'twitter' => (!empty($sedateProvider->twitter)) ? $sedateProvider->twitter : '',
							'linkedin' => (!empty($sedateProvider->linkedin)) ? $sedateProvider->linkedin : '',
							'pinterest' => (!empty($sedateProvider->pinterest)) ? $sedateProvider->pinterest : '',
							'digg' => (!empty($sedateProvider->digg)) ? $sedateProvider->digg : '',
							'google_plus' => (!empty($sedateProvider->google_plus)) ? $sedateProvider->google_plus : '',
							'instagram' => (!empty($sedateProvider->instagram)) ? $sedateProvider->instagram : '',
							'skypeid' => (!empty($sedateProvider->skypeid)) ? $sedateProvider->skypeid : '',
							'website' => (!empty($sedateProvider->website)) ? $sedateProvider->website : '',
							'address' => (!empty($address)) ? $address : '',
							'apt' => (!empty($sedateProvider->apt)) ? $sedateProvider->apt : '',
							'city' => (!empty($sedateProvider->city)) ? $sedateProvider->city : '',
							'state' => (!empty($sedateProvider->state)) ? $sedateProvider->state : '',
							'zipcode' => (!empty($sedateProvider->zipcode)) ? $sedateProvider->zipcode : '',
							'country' => (!empty($sedateProvider->country)) ? $sedateProvider->country : '',
							'service_perform' => $service_perform,
							'my_location' => $my_location,
							'providerlat' => $providerlat,
							'providerlng' => $providerlng,
							'amenities' => (!empty($sedateProvider->amenities)) ? $sedateProvider->amenities : '',
							'languages' => (!empty($sedateProvider->languages)) ? $sedateProvider->languages : '',
							);
				return $userinfo;	
			
			}else{
				
				/* Get Customer info */
				$sedateCustomer = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers_data.' where wp_user_id = %d',$currUser->ID));
				
				$userinfo = array(
							$currUser,
							'username' => $currUser->user_login,
							'fname' => $fname,
							'lname' => $lname,
							'phone' => (!empty($sedateCustomer->phone)) ? $sedateCustomer->phone : '',
							'phone2' => (!empty($sedateCustomer->phone2)) ? $sedateCustomer->phone2 : '',
							'address' =>(!empty($sedateCustomer->address)) ? $sedateCustomer->address : '',
							'apt' => (!empty($sedateCustomer->apt)) ? $sedateCustomer->apt : '',
							'city' => (!empty($sedateCustomer->city)) ? $sedateCustomer->city : '',
							'state' => (!empty($sedateCustomer->state)) ? $sedateCustomer->state : '',
							'zipcode' => (!empty($sedateCustomer->zipcode)) ? $sedateCustomer->zipcode : '',
							'country' => (!empty($sedateCustomer->country)) ? $sedateCustomer->country : '',
							'avatar_id' => (!empty($sedateCustomer->avatar_id)) ? $sedateCustomer->avatar_id : '',
							);
				return $userinfo;
				
			}		
	}
	
/*Get User Info by ID*/
function service_finder_getUserInfo($userid = 0){
			global $wpdb, $service_finder_Tables;
			if($userid > 0){
			$fname = get_user_meta($userid,'first_name',true);
			$lname = get_user_meta($userid,'last_name',true);	
			}else{
			$fname = '';
			$lname = '';	
			}
			
			if(service_finder_getUserRole($userid) == 'Provider'){
			
				/* Get Provider info */
				$sedateProvider = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' where wp_user_id = %d',$userid));
				
				$address = (!empty($sedateProvider->address)) ? $sedateProvider->address : '';
				$city = (!empty($sedateProvider->city)) ? $sedateProvider->city: '';
				$state = (!empty($sedateProvider->state)) ? $sedateProvider->state : '';
				$country = (!empty($sedateProvider->country)) ? $sedateProvider->country : '';
				
				$state = (!empty($state)) ? ', '.esc_html($state) : '';
						
				$fulladdress = $address.', '.$city.$state.', '.$country;
				
				$service_perform = get_user_meta($userid,'service_perform',true);
				$my_location = get_user_meta($userid,'my_location',true);
				$providerlat = get_user_meta($userid,'providerlat',true);
				$providerlng = get_user_meta($userid,'providerlng',true);	
				
				$user = get_user_by( 'id', $userid );
				
				$userinfo = array(
							'username' => $user->user_login,
							'company_name' => (!empty($sedateProvider->company_name)) ? $sedateProvider->company_name : '',
							'fname' => (!empty($fname)) ? $fname : '',
							'lname' => (!empty($lname)) ? $lname : '',
							'email' => (!empty($sedateProvider->email)) ? $sedateProvider->email : '',
							'avatar_id' => (!empty($sedateProvider->avatar_id)) ? $sedateProvider->avatar_id : '',
							'provider_id' => (!empty($sedateProvider->id)) ? $sedateProvider->id : '',
							'identity' => (!empty($sedateProvider->identity)) ? $sedateProvider->identity : '',
							'phone' => (!empty($sedateProvider->phone)) ? $sedateProvider->phone : '',
							'category' => (!empty($sedateProvider->category_id)) ? $sedateProvider->category_id : '',
							'categoryname' => service_finder_getCategoryName(get_user_meta($userid,'primary_category',true)),
							'tagline' => (!empty($sedateProvider->tagline)) ? $sedateProvider->tagline : '',
							'bio' => (!empty($sedateProvider->bio)) ? $sedateProvider->bio : '',
							'booking_description' => (!empty($sedateProvider->booking_description)) ? $sedateProvider->booking_description : '',
							'embeded_code' => (!empty($sedateProvider->embeded_code)) ? $sedateProvider->embeded_code : '',
							'mobile' => (!empty($sedateProvider->mobile)) ? $sedateProvider->mobile : '',
							'fax' => (!empty($sedateProvider->fax)) ? $sedateProvider->fax : '',
							'lat' => (!empty($sedateProvider->lat)) ? $sedateProvider->lat : '',
							'long' => (!empty($sedateProvider->long)) ? $sedateProvider->long : '',
							'facebook' => (!empty($sedateProvider->facebook)) ? $sedateProvider->facebook : '',
							'twitter' => (!empty($sedateProvider->twitter)) ? $sedateProvider->twitter : '',
							'linkedin' => (!empty($sedateProvider->linkedin)) ? $sedateProvider->linkedin : '',
							'pinterest' => (!empty($sedateProvider->pinterest)) ? $sedateProvider->pinterest : '',
							'digg' => (!empty($sedateProvider->digg)) ? $sedateProvider->digg : '',
							'google_plus' => (!empty($sedateProvider->google_plus)) ? $sedateProvider->google_plus : '',
							'instagram' => (!empty($sedateProvider->instagram)) ? $sedateProvider->instagram : '',
							'skypeid' => (!empty($sedateProvider->skypeid)) ? $sedateProvider->skypeid : '',
							'website' => (!empty($sedateProvider->website)) ? $sedateProvider->website : '',
							'simpleaddress' => (!empty($sedateProvider->address)) ? $sedateProvider->address : '',
							'address' => $address,
							'apt' => (!empty($sedateProvider->apt)) ? $sedateProvider->apt : '',
							'city' => (!empty($sedateProvider->city)) ? $sedateProvider->city : '',
							'state' => (!empty($sedateProvider->state)) ? $sedateProvider->state : '',
							'zipcode' => (!empty($sedateProvider->zipcode)) ? $sedateProvider->zipcode : '',
							'country' => (!empty($sedateProvider->country)) ? $sedateProvider->country : '',
							'service_perform' => $service_perform,
							'my_location' => $my_location,
							'providerlat' => $providerlat,
							'providerlng' => $providerlng,
							'amenities' => (!empty($sedateProvider->amenities)) ? $sedateProvider->amenities : '',
							'languages' => (!empty($sedateProvider->languages)) ? $sedateProvider->languages : '',
							);
				return $userinfo;	
			
			}else{
				
				/* Get Customer info */
				$sedateCustomer = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers_data.' where wp_user_id = %d',$userid));
				$customerinfo =  get_userdata($userid);
				$user = get_user_by( 'id', $userid );
				$userinfo = array(
							'username' => $user->user_login,
							'fname' => $fname,
							'lname' => $lname,
							'email' => (!empty($customerinfo->user_email)) ? $customerinfo->user_email : '',
							'phone' => (!empty($sedateCustomer->phone)) ? $sedateCustomer->phone : '',
							'phone2' => (!empty($sedateCustomer->phone2)) ? $sedateCustomer->phone2 : '',
							'address' => (!empty($sedateCustomer->address)) ? $sedateCustomer->address : '',
							'apt' => (!empty($sedateCustomer->apt)) ? $sedateCustomer->apt : '',
							'city' => (!empty($sedateCustomer->city)) ? $sedateCustomer->city : '',
							'state' => (!empty($sedateCustomer->state)) ? $sedateCustomer->state : '',
							'zipcode' => (!empty($sedateCustomer->zipcode)) ? $sedateCustomer->zipcode : '',
							'country' => (!empty($sedateCustomer->country)) ? $sedateCustomer->country : '',
							'avatar_id' => (!empty($sedateCustomer->avatar_id)) ? $sedateCustomer->avatar_id: '',
							);
				return $userinfo;
				
			}		
	}	

/*Get Providers Settings*/
function service_finder_getProviderSettings($uid = 0){
		global $wpdb, $service_finder_Tables;

		$options = unserialize(get_option( 'provider_settings'));
		if($uid > 0){
		$settings = array(
							'booking_process' => (!empty($options[$uid]['booking_process'])) ? $options[$uid]['booking_process'] : '',
							'availability_based_on' => (!empty($options[$uid]['availability_based_on'])) ? $options[$uid]['availability_based_on'] : '',
							'slot_interval' => (!empty($options[$uid]['slot_interval'])) ? $options[$uid]['slot_interval'] : '',
							'offers_based_on' => (!empty($options[$uid]['offers_based_on'])) ? $options[$uid]['offers_based_on'] : '',
							'booking_date_based_on' => (!empty($options[$uid]['booking_date_based_on'])) ? $options[$uid]['booking_date_based_on'] : '',
							'booking_basedon' => (!empty($options[$uid]['booking_basedon'])) ? $options[$uid]['booking_basedon'] : '',
							'booking_charge_on_service' => 'yes',
							'booking_option' => (!empty($options[$uid]['booking_option'])) ? $options[$uid]['booking_option'] : '',
							'mincost' => (!empty($options[$uid]['mincost'])) ? $options[$uid]['mincost'] : '',
							'future_bookings_availability' => (!empty($options[$uid]['future_bookings_availability'])) ? $options[$uid]['future_bookings_availability'] : '',
							'buffertime' => (!empty($options[$uid]['buffertime'])) ? $options[$uid]['buffertime'] : '',
							'booking_assignment' => (!empty($options[$uid]['booking_assignment'])) ? $options[$uid]['booking_assignment'] : '',
							'members_available' => (!empty($options[$uid]['members_available'])) ? $options[$uid]['members_available'] : '',
							'paymentoption' => (!empty($options[$uid]['paymentoption'])) ? $options[$uid]['paymentoption'] : '',
							'paypalusername' => (!empty($options[$uid]['paypalusername'])) ? $options[$uid]['paypalusername'] : '',
							'paypalpassword' => (!empty($options[$uid]['paypalpassword'])) ? $options[$uid]['paypalpassword'] : '',
							'paypalsignatue' => (!empty($options[$uid]['paypalsignatue'])) ? $options[$uid]['paypalsignatue'] : '',
							'stripesecretkey' => (!empty($options[$uid]['stripesecretkey'])) ? $options[$uid]['stripesecretkey'] : '',
							'stripepublickey' => (!empty($options[$uid]['stripepublickey'])) ? $options[$uid]['stripepublickey'] : '',
							'wired_description' => (!empty($options[$uid]['wired_description'])) ? $options[$uid]['wired_description'] : '',
							'wired_instructions' => (!empty($options[$uid]['wired_instructions'])) ? $options[$uid]['wired_instructions'] : '',
							'twocheckoutaccountid' => (!empty($options[$uid]['twocheckoutaccountid'])) ? $options[$uid]['twocheckoutaccountid'] : '',
							'twocheckoutpublishkey' => (!empty($options[$uid]['twocheckoutpublishkey'])) ? $options[$uid]['twocheckoutpublishkey'] : '',
							'twocheckoutprivatekey' => (!empty($options[$uid]['twocheckoutprivatekey'])) ? $options[$uid]['twocheckoutprivatekey'] : '',
							'payumoneymid' => (!empty($options[$uid]['payumoneymid'])) ? $options[$uid]['payumoneymid'] : '',
							'payumoneykey' => (!empty($options[$uid]['payumoneykey'])) ? $options[$uid]['payumoneykey'] : '',
							'payumoneysalt' => (!empty($options[$uid]['payumoneysalt'])) ? $options[$uid]['payumoneysalt'] : '',
							'payulatammerchantid' => (!empty($options[$uid]['payulatammerchantid'])) ? $options[$uid]['payulatammerchantid'] : '',
							'payulatamapilogin' => (!empty($options[$uid]['payulatamapilogin'])) ? $options[$uid]['payulatamapilogin'] : '',
							'payulatamapikey' => (!empty($options[$uid]['payulatamapikey'])) ? $options[$uid]['payulatamapikey'] : '',
							'payulatamaccountid' => (!empty($options[$uid]['payulatamaccountid'])) ? $options[$uid]['payulatamaccountid'] : '',
							'google_calendar' => (!empty($options[$uid]['google_calendar'])) ? $options[$uid]['google_calendar'] : '',
							);
		return $settings;
		}
}

/*Get User Role By ID*/
function service_finder_getUserRole($userid = 0){
if($userid > 0){
	$user = new WP_User( $userid );
	return (!empty($user->roles[0])) ? $user->roles[0] : '';
}	
}

/*Fetch Category List Array*/
function service_finder_getCategoryList($limit = '',$child=false,$texonomy = 'providers-category'){
	global $wpdb, $service_finder_Tables;
	
	if($child == 'true'){
		$parent = '';
	}else{
		$parent = 0;
	}
	$args = array(
		'orderby'           => 'name',
		'order'             => 'ASC',
		'number'            => $limit,
		'parent'            => $parent,
		'hide_empty'        => false, 
	); 
	return $categories = get_terms( $texonomy,$args );
}

/*Fetch Amenity List Array*/
function service_finder_getAmenityList($limit = '',$child=false){
	global $wpdb, $service_finder_Tables;
	
	if($child == 'true'){
		$parent = '';
	}else{
		$parent = 0;
	}
	$args = array(
		'orderby'           => 'name',
		'order'             => 'ASC',
		'number'            => $limit,
		'parent'            => $parent,
		'hide_empty'        => false, 
	); 
	return $categories = get_terms( 'sf-amenities',$args );
}

/*Fetch Language List Array*/
function service_finder_getLanguageList($limit = '',$child=false){
	global $wpdb, $service_finder_Tables;
	
	if($child == 'true'){
		$parent = '';
	}else{
		$parent = 0;
	}
	$args = array(
		'orderby'           => 'name',
		'order'             => 'ASC',
		'number'            => $limit,
		'parent'            => $parent,
		'hide_empty'        => false, 
	); 
	return $categories = get_terms( 'sf-languages',$args );
}


/*Fetch Category List Array*/
function service_finder_get_child_category($parentid = 0){
	global $wpdb, $service_finder_Tables;
	
	$args = array(
		'orderby'           => 'name',
		'order'             => 'ASC',
		'number'            => 0,
		'child_of'          => $parentid,
		'hide_empty'        => false, 
	); 
	return $categories = get_terms( 'providers-category',$args );
}

/*Fetch Category List Array*/
function service_finder_getCategoryListwithOffest($limit = '',$child=false,$offset = 0){
	global $wpdb, $service_finder_Tables;
	
	if($child == 'true'){
		$parent = '';
	}else{
		$parent = 0;
	}
	$args = array(
		'orderby'           => 'name',
		'order'             => 'ASC',
		'offset'            => $offset,
		'number'            => $limit,
		'parent'            => $parent,
		'hide_empty'        => false, 
	); 

	return $categories = get_terms( 'providers-category',$args );
}

/*Get Category Link*/
function service_finder_getCategoryLink($catid = 0){
	global $wpdb, $service_finder_Tables;
	
	if($catid > 0){
	$catdetails = get_term_by('id', $catid, 'providers-category');
	if(!empty($catdetails)){
	$link = get_term_link( $catdetails );
	return $link;
	}else{
	return '';
	}
	}else{
	return '';
	}
}

/*Get Provider Services*/
function service_finder_getServices($uid = 0,$status = '',$groupid = 0){
	global $wpdb, $service_finder_Tables;
	
	if($status == 'active'){
	
	if($groupid != '' && $groupid > 0){
	$services = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->services.' WHERE `status` = "active" AND group_id = %d AND `wp_user_id` = %d',$groupid,$uid));
	}else{
	$services = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->services.' WHERE `status` = "active" AND group_id = 0 AND `wp_user_id` = %d',$uid));
	}
	}else{
	if($groupid != '' && $groupid > 0){
	$services = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->services.' WHERE group_id = %d AND `wp_user_id` = %d',$groupid,$uid));
	}else{
	$services = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->services.' WHERE group_id = 0 AND `wp_user_id` = %d',$uid));
	}
	}
	
	return $services;
}

/*Get Provider Services*/
function service_finder_getAllServices($uid = 0,$status = ''){
	global $wpdb, $service_finder_Tables;
	
	if($status == 'active'){
	$services = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->services.' WHERE `status` = "active" AND `wp_user_id` = %d',$uid));
	}else{
	$services = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->services.' WHERE `wp_user_id` = %d',$uid));
	}
	
	return $services;
}

/*Get Provider Service Data*/
function service_finder_getServiceData($sid = 0){
	global $wpdb, $service_finder_Tables;
	
	$servicedata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->services.' WHERE `id` = %d',$sid));
	
	return $servicedata;
}

/*Get Provider Service Data*/
function service_finder_getServiceName($sid = 0){
	global $wpdb, $service_finder_Tables;
	
	$row = $wpdb->get_row($wpdb->prepare('SELECT service_name FROM '.$service_finder_Tables->services.' WHERE `id` = %d',$sid));
	
	return $row->service_name;
}

/*Get Provider Documents*/
function service_finder_getDocuments($uid = 0){
	global $wpdb, $service_finder_Tables;
	
	$attachmentsIDs = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->attachments.' WHERE `type` = "file" AND `wp_user_id` = %d',$uid));
	
	return $attachmentsIDs;
}

/*Get Provider Identity*/
function service_finder_get_identity($uid = 0){
	global $wpdb, $service_finder_Tables;
	
	$attachmentsIDs = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->attachments.' WHERE `type` = "identity" AND `wp_user_id` = %d',$uid));
	
	return $attachmentsIDs;
}

function service_finder_get_proviers_allcategories($uid = 0){
	global $wpdb, $service_finder_Tables, $service_finder_options;
	$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$uid));
	if(!empty($row)){
		return $row->category_id;
	}else{
		return '';
	}
	
}

/*Fetch Related Providers List Array*/
function service_finder_getRelatedProviders($uid = 0,$catid = 0,$limit=5){
	global $wpdb, $service_finder_Tables, $service_finder_options, $current_user;
	
	$identitycheck = (isset($service_finder_options['identity-check'])) ? esc_attr($service_finder_options['identity-check']) : '';
	$restrictuserarea = (isset($service_finder_options['restrict-user-area'])) ? esc_attr($service_finder_options['restrict-user-area']) : '';
	
	$allcats = service_finder_get_proviers_allcategories($uid);
	$sql = '';
	
	
	if(is_user_logged_in() && service_finder_getUserRole($current_user->ID) != 'Customer'){
	$userInfo = service_finder_getCurrentUserInfo();
	$customercity = $userInfo['city'];	
	
	if($allcats != ""){
		$allcats = explode(',',$allcats);
		if(!empty($allcats)){
			$sql .= ' AND (';
			foreach($allcats as $allcatid){
				$sql .= ' FIND_IN_SET("'.$allcatid.'", category_id) OR ';
			}
			$sql .= ' `city` = "'.$customercity.'" ';	
			$sql .= ' )';
		}
	}
	
	if($restrictuserarea && $identitycheck){
	$providers = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE admin_moderation = "approved" AND identity = "approved" AND account_blocked != "yes" '.$sql.' AND `wp_user_id` != %d ORDER BY RAND() LIMIT %d',$uid,$limit));
	}else{
	$providers = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE admin_moderation = "approved" AND account_blocked != "yes" '.$sql.' AND `wp_user_id` != %d ORDER BY RAND() LIMIT %d',$uid,$limit));
	}
	}else{
	
	if($allcats != ""){
		$allcats = explode(',',$allcats);
		if(!empty($allcats)){
			$sql .= ' AND (';
			foreach($allcats as $allcatid){
				$sql .= ' FIND_IN_SET("'.$allcatid.'", category_id) OR ';
			}
			$sql .= ' FIND_IN_SET("'.$catid.'", category_id) ';	
			$sql .= ' )';
		}
	}
	
	if($restrictuserarea && $identitycheck){
	$providers = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE admin_moderation = "approved" AND identity = "approved" AND account_blocked != "yes" '.$sql.' AND `wp_user_id` != %d ORDER BY RAND() LIMIT %d',$uid,$limit));
	}else{
	$providers = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE admin_moderation = "approved" AND account_blocked != "yes" '.$sql.' AND `wp_user_id` != %d ORDER BY RAND() LIMIT %d',$uid,$limit));
	}
	}
	
	return $providers;
}

/*Fetch Provider Attachments*/
function service_finder_getProviderAttachments($uid = 0,$type = ''){
	global $wpdb, $service_finder_Tables;
	$attachments = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->attachments.' WHERE `type` = "%s" AND `wp_user_id` = %d',$type,$uid));
	
	return $attachments;
}

/*Fetch Provider Attachments*/
function service_finder_cover_image_exists($uid = 0,$type = ''){
	global $wpdb, $service_finder_Tables;
	$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->attachments.' WHERE `type` = "%s" AND `wp_user_id` = %d',$type,$uid));
	
	if(!empty($row))
	{
		return true;
	}else
	{
		return false;	
	}
}

/*Fetch Service Area*/
function service_finder_getServiceArea($uid = 0){
	global $wpdb, $service_finder_Tables;
	$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->service_area.' WHERE `provider_id` = %d AND `status` = "active"',$uid));
	
	return $results;
}

/*Fetch All Service Area*/
function service_finder_getAllServiceArea($uid = 0){
	global $wpdb, $service_finder_Tables;
	$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->service_area.' WHERE `provider_id` = %d',$uid));
	
	return $results;
}

/*Fetch Service Regions*/
function service_finder_getServiceRegions($uid = 0){
	global $wpdb, $service_finder_Tables;
	$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->regions.' WHERE `status` = "active" AND `provider_id` = %d',$uid));
	
	return $results;
}

/*Fetch All Service Regions*/
function service_finder_getAllServiceRegions($uid = 0){
	global $wpdb, $service_finder_Tables;
	$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->regions.' WHERE `provider_id` = %d',$uid));
	
	return $results;
}

/*Fetch Staff Members*/
function service_finder_getStaffMembers($uid = 0,$zipcode = '',$date = '',$slot ='',$memberid = 0,$editbooking = '',$region=''){
	global $wpdb, $service_finder_Tables;
	
	$settings = service_finder_getProviderSettings($uid);
	
	$dayname = date('l', strtotime( $date ));
	$tem = explode('-',$slot);
	$start_time = (!empty($tem[0])) ? $tem[0] : '';
	$end_time = (!empty($tem[1])) ? $tem[1] : '';
	
	if($memberid > 0){
	
		if($settings['booking_basedon'] == 'zipcode'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND (`bookings`.`start_time` > "'.$start_time.'" AND `bookings`.`start_time` < "'.$end_time.'" OR (`bookings`.`end_time` > "'.$start_time.'" AND `bookings`.`end_time` < "'.$end_time.'") OR (`bookings`.`start_time` < "'.$start_time.'" AND `bookings`.`end_time` > "'.$end_time.'") OR (`bookings`.`start_time` = "'.$start_time.'" OR `bookings`.`end_time` = "'.$end_time.'") ) AND `bookings`.`member_id` != "'.$memberid.'" AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `service_area` LIKE "%'.$zipcode.'%" AND `admin_wp_id` = '.$uid);
		}elseif($settings['booking_basedon'] == 'region'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND (`bookings`.`start_time` > "'.$start_time.'" AND `bookings`.`start_time` < "'.$end_time.'" OR (`bookings`.`end_time` > "'.$start_time.'" AND `bookings`.`end_time` < "'.$end_time.'") OR (`bookings`.`start_time` < "'.$start_time.'" AND `bookings`.`end_time` > "'.$end_time.'") OR (`bookings`.`start_time` = "'.$start_time.'" OR `bookings`.`end_time` = "'.$end_time.'") ) AND `bookings`.`member_id` != "'.$memberid.'" AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `regions` LIKE "%'.$region.'%" AND `admin_wp_id` = '.$uid);
		
		}elseif($settings['booking_basedon'] == 'open'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND (`bookings`.`start_time` > "'.$start_time.'" AND `bookings`.`start_time` < "'.$end_time.'" OR (`bookings`.`end_time` > "'.$start_time.'" AND `bookings`.`end_time` < "'.$end_time.'") OR (`bookings`.`start_time` < "'.$start_time.'" AND `bookings`.`end_time` > "'.$end_time.'") OR (`bookings`.`start_time` = "'.$start_time.'" OR `bookings`.`end_time` = "'.$end_time.'") ) AND `bookings`.`member_id` != "'.$memberid.'" AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `admin_wp_id` = '.$uid);
		}
		
	
	}elseif($slot == ''){
		
		if($settings['booking_basedon'] == 'zipcode'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `service_area` LIKE "%'.$zipcode.'%" AND `admin_wp_id` = '.$uid);
		}elseif($settings['booking_basedon'] == 'region'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `regions` LIKE "%'.$region.'%" AND `admin_wp_id` = '.$uid);
		}elseif($settings['booking_basedon'] == 'open'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `admin_wp_id` = '.$uid);
		}
		
		
	}else{
	
		if($settings['booking_basedon'] == 'zipcode'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND (`bookings`.`start_time` > "'.$start_time.'" AND `bookings`.`start_time` < "'.$end_time.'" OR (`bookings`.`end_time` > "'.$start_time.'" AND `bookings`.`end_time` < "'.$end_time.'") OR (`bookings`.`start_time` < "'.$start_time.'" AND `bookings`.`end_time` > "'.$end_time.'") OR (`bookings`.`start_time` = "'.$start_time.'" OR `bookings`.`end_time` = "'.$end_time.'") ) AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `service_area` LIKE "%'.$zipcode.'%" AND `admin_wp_id` = '.$uid);
		}elseif($settings['booking_basedon'] == 'region'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND (`bookings`.`start_time` > "'.$start_time.'" AND `bookings`.`start_time` < "'.$end_time.'" OR (`bookings`.`end_time` > "'.$start_time.'" AND `bookings`.`end_time` < "'.$end_time.'") OR (`bookings`.`start_time` < "'.$start_time.'" AND `bookings`.`end_time` > "'.$end_time.'") OR (`bookings`.`start_time` = "'.$start_time.'" OR `bookings`.`end_time` = "'.$end_time.'") ) AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `regions` LIKE "%'.$region.'%" AND `admin_wp_id` = '.$uid);
		}elseif($settings['booking_basedon'] == 'open'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND (`bookings`.`start_time` > "'.$start_time.'" AND `bookings`.`start_time` < "'.$end_time.'" OR (`bookings`.`end_time` > "'.$start_time.'" AND `bookings`.`end_time` < "'.$end_time.'") OR (`bookings`.`start_time` < "'.$start_time.'" AND `bookings`.`end_time` > "'.$end_time.'") OR (`bookings`.`start_time` = "'.$start_time.'" OR `bookings`.`end_time` = "'.$end_time.'") ) AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `admin_wp_id` = '.$uid);
		}
		
	
	}
	//echo $wpdb->last_query;
	return $members;
}

function service_finder_getStaffMembersList($uid = 0,$sid = 0,$zipcode='',$region=''){
	global $wpdb, $service_finder_Tables;
	
	$settings = service_finder_getProviderSettings($uid);
	
	if($settings['booking_basedon'] == 'zipcode'){
	$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE `is_admin` = "no" AND FIND_IN_SET("'.$sid.'",services) AND `service_area` LIKE "%'.$zipcode.'%" AND `admin_wp_id` = '.$uid);
	}elseif($settings['booking_basedon'] == 'region'){
	$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE `is_admin` = "no" AND FIND_IN_SET("'.$sid.'",services) AND `regions` LIKE "%'.$region.'%" AND `admin_wp_id` = '.$uid);
	}elseif($settings['booking_basedon'] == 'open'){
	$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE `is_admin` = "no" AND FIND_IN_SET("'.$sid.'",services) AND `admin_wp_id` = '.$uid);
	}
	
	return $members;
}

/*Fetch Staff Members*/
function service_finder_getStaffMembersStartTime($uid = 0,$zipcode='',$date = '',$slot ='',$memberid = 0,$editbooking = '',$region=''){
	global $wpdb, $service_finder_Tables;
	
	$settings = service_finder_getProviderSettings($uid);
	
	$dayname = date('l', strtotime( $date ));
	$tem = explode('-',$slot);
	$start_time = (!empty($tem[0])) ? $tem[0] : '';
	$end_time = (!empty($tem[1])) ? $tem[1] : '';
	
	if($memberid > 0){
	
		if($settings['booking_basedon'] == 'zipcode'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND (start_time > "'.$start_time.'" AND start_time < "'.$end_time.'" OR (end_time > "'.$start_time.'" AND end_time < "'.$end_time.'") OR (start_time < "'.$start_time.'" AND end_time > "'.$end_time.'") OR (start_time = "'.$start_time.'" OR end_time = "'.$end_time.'") ) AND `bookings`.`member_id` != "'.$memberid.'" AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `service_area` LIKE "%'.$zipcode.'%" AND `admin_wp_id` = '.$uid);
		}elseif($settings['booking_basedon'] == 'region'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND (start_time > "'.$start_time.'" AND start_time < "'.$end_time.'" OR (end_time > "'.$start_time.'" AND end_time < "'.$end_time.'") OR (start_time < "'.$start_time.'" AND end_time > "'.$end_time.'") OR (start_time = "'.$start_time.'" OR end_time = "'.$end_time.'") ) AND `bookings`.`member_id` != "'.$memberid.'" AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `regions` LIKE "%'.$region.'%" AND `admin_wp_id` = '.$uid);
		
		}elseif($settings['booking_basedon'] == 'open'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND (start_time > "'.$start_time.'" AND start_time < "'.$end_time.'" OR (end_time > "'.$start_time.'" AND end_time < "'.$end_time.'") OR (start_time < "'.$start_time.'" AND end_time > "'.$end_time.'") OR (start_time = "'.$start_time.'" OR end_time = "'.$end_time.'") ) AND `bookings`.`member_id` != "'.$memberid.'" AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `admin_wp_id` = '.$uid);
		}
		
	
	}elseif($slot == ''){
		
		if($settings['booking_basedon'] == 'zipcode'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `service_area` LIKE "%'.$zipcode.'%" AND `admin_wp_id` = '.$uid);
		}elseif($settings['booking_basedon'] == 'region'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `regions` LIKE "%'.$region.'%" AND `admin_wp_id` = '.$uid);
		}elseif($settings['booking_basedon'] == 'open'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `admin_wp_id` = '.$uid);
		}
		
		
	}else{
	
		if($settings['booking_basedon'] == 'zipcode'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND (start_time > "'.$start_time.'" AND start_time < "'.$end_time.'" OR (end_time > "'.$start_time.'" AND end_time < "'.$end_time.'") OR (start_time < "'.$start_time.'" AND end_time > "'.$end_time.'") OR (start_time = "'.$start_time.'" OR end_time = "'.$end_time.'") ) AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `service_area` LIKE "%'.$zipcode.'%" AND `admin_wp_id` = '.$uid);
		}elseif($settings['booking_basedon'] == 'region'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND (start_time > "'.$start_time.'" AND start_time < "'.$end_time.'" OR (end_time > "'.$start_time.'" AND end_time < "'.$end_time.'") OR (start_time < "'.$start_time.'" AND end_time > "'.$end_time.'") OR (start_time = "'.$start_time.'" OR end_time = "'.$end_time.'") ) AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `regions` LIKE "%'.$region.'%" AND `admin_wp_id` = '.$uid);
		}elseif($settings['booking_basedon'] == 'open'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND (start_time > "'.$start_time.'" AND start_time < "'.$end_time.'" OR (end_time > "'.$start_time.'" AND end_time < "'.$end_time.'") OR (start_time < "'.$start_time.'" AND end_time > "'.$end_time.'") OR (start_time = "'.$start_time.'" OR end_time = "'.$end_time.'") ) AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `admin_wp_id` = '.$uid);
		}
		
	
	}

	return $members;
}

/*Fetch Staff Members Edit*/
function service_finder_getStaffMembersStartTimeEdit($uid = 0,$zipcode='',$date = '',$slot ='',$memberid = 0,$editbooking = '',$region='',$bookingid = 0){
	global $wpdb, $service_finder_Tables;
	
	$settings = service_finder_getProviderSettings($uid);
	
	$dayname = date('l', strtotime( $date ));
	$tem = explode('-',$slot);
	$start_time = (!empty($tem[0])) ? $tem[0] : '';
	$end_time = (!empty($tem[1])) ? $tem[1] : '';
	
	if($memberid > 0){
	
		if($settings['booking_basedon'] == 'zipcode'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND `bookings`.`id` != '.$bookingid.' AND (start_time > "'.$start_time.'" AND start_time < "'.$end_time.'" OR (end_time > "'.$start_time.'" AND end_time < "'.$end_time.'") OR (start_time < "'.$start_time.'" AND end_time > "'.$end_time.'") OR (start_time = "'.$start_time.'" OR end_time = "'.$end_time.'") ) AND `bookings`.`member_id` != "'.$memberid.'" AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `service_area` LIKE "%'.$zipcode.'%" AND `admin_wp_id` = '.$uid);
		}elseif($settings['booking_basedon'] == 'region'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND `bookings`.`id` != '.$bookingid.' AND (start_time > "'.$start_time.'" AND start_time < "'.$end_time.'" OR (end_time > "'.$start_time.'" AND end_time < "'.$end_time.'") OR (start_time < "'.$start_time.'" AND end_time > "'.$end_time.'") OR (start_time = "'.$start_time.'" OR end_time = "'.$end_time.'") ) AND `bookings`.`member_id` != "'.$memberid.'" AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `regions` LIKE "%'.$region.'%" AND `admin_wp_id` = '.$uid);
		
		}elseif($settings['booking_basedon'] == 'open'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND `bookings`.`id` != '.$bookingid.' AND (start_time > "'.$start_time.'" AND start_time < "'.$end_time.'" OR (end_time > "'.$start_time.'" AND end_time < "'.$end_time.'") OR (start_time < "'.$start_time.'" AND end_time > "'.$end_time.'") OR (start_time = "'.$start_time.'" OR end_time = "'.$end_time.'") ) AND `bookings`.`member_id` != "'.$memberid.'" AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `admin_wp_id` = '.$uid);
		}
		
	
	}elseif($slot == ''){
		
		if($settings['booking_basedon'] == 'zipcode'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND `bookings`.`id` != '.$bookingid.' AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `service_area` LIKE "%'.$zipcode.'%" AND `admin_wp_id` = '.$uid);
		}elseif($settings['booking_basedon'] == 'region'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND `bookings`.`id` != '.$bookingid.' AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `regions` LIKE "%'.$region.'%" AND `admin_wp_id` = '.$uid);
		}elseif($settings['booking_basedon'] == 'open'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND `bookings`.`id` != '.$bookingid.' AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `admin_wp_id` = '.$uid);
		}
		
		
	}else{
	
		if($settings['booking_basedon'] == 'zipcode'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND `bookings`.`id` != '.$bookingid.' AND (start_time > "'.$start_time.'" AND start_time < "'.$end_time.'" OR (end_time > "'.$start_time.'" AND end_time < "'.$end_time.'") OR (start_time < "'.$start_time.'" AND end_time > "'.$end_time.'") OR (start_time = "'.$start_time.'" OR end_time = "'.$end_time.'") ) AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `service_area` LIKE "%'.$zipcode.'%" AND `admin_wp_id` = '.$uid);
		}elseif($settings['booking_basedon'] == 'region'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND `bookings`.`id` != '.$bookingid.' AND (start_time > "'.$start_time.'" AND start_time < "'.$end_time.'" OR (end_time > "'.$start_time.'" AND end_time < "'.$end_time.'") OR (start_time < "'.$start_time.'" AND end_time > "'.$end_time.'") OR (start_time = "'.$start_time.'" OR end_time = "'.$end_time.'") ) AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `regions` LIKE "%'.$region.'%" AND `admin_wp_id` = '.$uid);
		}elseif($settings['booking_basedon'] == 'open'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND `bookings`.`id` != '.$bookingid.' AND (start_time > "'.$start_time.'" AND start_time < "'.$end_time.'" OR (end_time > "'.$start_time.'" AND end_time < "'.$end_time.'") OR (start_time < "'.$start_time.'" AND end_time > "'.$end_time.'") OR (start_time = "'.$start_time.'" OR end_time = "'.$end_time.'") ) AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `admin_wp_id` = '.$uid);
		}
		
	
	}

	return $members;
}

/*Fetch Staff Members no hours*/
function service_finder_getStaffMembersStartTime_nohours($uid = 0,$zipcode='',$date = '',$start_time ='',$memberid = 0,$editbooking = '',$region=''){
	global $wpdb, $service_finder_Tables;
	
	$settings = service_finder_getProviderSettings($uid);
	
	$dayname = date('l', strtotime( $date ));
	
	if($memberid > 0){
	
		if($settings['booking_basedon'] == 'zipcode'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND start_time = "'.$start_time.'" AND `bookings`.`member_id` != "'.$memberid.'" AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `service_area` LIKE "%'.$zipcode.'%" AND `admin_wp_id` = '.$uid);
		}elseif($settings['booking_basedon'] == 'region'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND start_time = "'.$start_time.'" AND `bookings`.`member_id` != "'.$memberid.'" AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `regions` LIKE "%'.$region.'%" AND `admin_wp_id` = '.$uid);
		
		}elseif($settings['booking_basedon'] == 'open'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND start_time = "'.$start_time.'" AND `bookings`.`member_id` != "'.$memberid.'" AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `admin_wp_id` = '.$uid);
		}
		
	
	}elseif($start_time == ''){
		
		if($settings['booking_basedon'] == 'zipcode'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `service_area` LIKE "%'.$zipcode.'%" AND `admin_wp_id` = '.$uid);
		}elseif($settings['booking_basedon'] == 'region'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `regions` LIKE "%'.$region.'%" AND `admin_wp_id` = '.$uid);
		}elseif($settings['booking_basedon'] == 'open'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `admin_wp_id` = '.$uid);
		}
		
		
	}else{
	
		if($settings['booking_basedon'] == 'zipcode'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND start_time = "'.$start_time.'" AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `service_area` LIKE "%'.$zipcode.'%" AND `admin_wp_id` = '.$uid);
		}elseif($settings['booking_basedon'] == 'region'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND start_time = "'.$start_time.'" AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `regions` LIKE "%'.$region.'%" AND `admin_wp_id` = '.$uid);
		}elseif($settings['booking_basedon'] == 'open'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND start_time = "'.$start_time.'" AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `admin_wp_id` = '.$uid);
		}
		
	
	}

	return $members;
}

/*Fetch Staff Members no hours Edit*/
function service_finder_getStaffMembersStartTimeEdit_nohours($uid = 0,$zipcode='',$date = '',$start_time ='',$memberid = 0,$editbooking = '',$region='',$bookingid = 0){
	global $wpdb, $service_finder_Tables;
	
	$settings = service_finder_getProviderSettings($uid);
	
	$dayname = date('l', strtotime( $date ));
	
	if($memberid > 0){
	
		if($settings['booking_basedon'] == 'zipcode'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND `bookings`.`id` != '.$bookingid.' AND start_time = "'.$start_time.'" AND `bookings`.`member_id` != "'.$memberid.'" AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `service_area` LIKE "%'.$zipcode.'%" AND `admin_wp_id` = '.$uid);
		}elseif($settings['booking_basedon'] == 'region'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND `bookings`.`id` != '.$bookingid.' AND start_time = "'.$start_time.'" AND `bookings`.`member_id` != "'.$memberid.'" AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `regions` LIKE "%'.$region.'%" AND `admin_wp_id` = '.$uid);
		
		}elseif($settings['booking_basedon'] == 'open'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND `bookings`.`id` != '.$bookingid.' AND start_time = "'.$start_time.'" AND `bookings`.`member_id` != "'.$memberid.'" AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `admin_wp_id` = '.$uid);
		}
		
	
	}elseif($start_time == ''){
		
		if($settings['booking_basedon'] == 'zipcode'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND `bookings`.`id` != '.$bookingid.' AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `service_area` LIKE "%'.$zipcode.'%" AND `admin_wp_id` = '.$uid);
		}elseif($settings['booking_basedon'] == 'region'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND `bookings`.`id` != '.$bookingid.' AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `regions` LIKE "%'.$region.'%" AND `admin_wp_id` = '.$uid);
		}elseif($settings['booking_basedon'] == 'open'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND `bookings`.`id` != '.$bookingid.' AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `admin_wp_id` = '.$uid);
		}
		
		
	}else{
	
		if($settings['booking_basedon'] == 'zipcode'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND `bookings`.`id` != '.$bookingid.' AND start_time = "'.$start_time.'" AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `service_area` LIKE "%'.$zipcode.'%" AND `admin_wp_id` = '.$uid);
		}elseif($settings['booking_basedon'] == 'region'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND `bookings`.`id` != '.$bookingid.' AND start_time = "'.$start_time.'" AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `regions` LIKE "%'.$region.'%" AND `admin_wp_id` = '.$uid);
		}elseif($settings['booking_basedon'] == 'open'){
		$members = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->team_members.' AS members WHERE NOT EXISTS(SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`date` = "'.$date.'" AND `bookings`.`status` != "Cancel" AND `bookings`.`id` != '.$bookingid.' AND start_time = "'.$start_time.'" AND `bookings`.`member_id` = `members`.`id`) AND `is_admin` = "no" AND `admin_wp_id` = '.$uid);
		}
		
	
	}

	return $members;
}


/*Get Members for Schedule Calendar*/
function service_finder_getMembers($uid = 0){
global $wpdb, $service_finder_Tables;
		
		$members = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->team_members.' WHERE `is_admin` = "no" AND `admin_wp_id` = %d',$uid));
		return $members;
		
}

/*Get Members for Schedule Calendar*/
function service_finder_getMemberName($mid = 0){
global $wpdb, $service_finder_Tables;
		
		$member = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->team_members.' WHERE `id` = %d',$mid));
		return $member->member_name;
		
}

/*Get Members for Schedule Calendar*/
function service_finder_getMemberEmail($mid = 0){
global $wpdb, $service_finder_Tables;
		
		$member = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->team_members.' WHERE `id` = %d',$mid));
		return $member->email;
		
}

/*Get Members for Schedule Calendar*/
function service_finder_getMemberAvatar($mid = 0){
global $wpdb, $service_finder_Tables, $service_finder_Params;
		
		$member = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->team_members.' WHERE `id` = %d',$mid));
		
		$src  = wp_get_attachment_image_src( $member->avatar_id, 'service_finder-staff-member' );
		$src  = $src[0];
		$src = ($src != '') ? $src : '';
		
		return $src;
		
}

/*Get Category Image*/
function service_finder_getCategoryImage($cid = 0,$imagesize = 'medium'){
global $wpdb, $service_finder_Tables;
		
		if($cid > 0){
		
			if(service_finder_check_new_client())
			{
				$imageid = get_term_meta( $cid,'imageid', true );
				if($imageid > 0)
				{
					$image_attributes = wp_get_attachment_image_src( $imageid, $imagesize );
					if(!empty($image_attributes[0]))
					{
						return $image_attributes[0];
					}else{
						return SERVICE_FINDER_BOOKING_IMAGE_URL.'/cat-placeholder.png';
					}
				}else{
					return SERVICE_FINDER_BOOKING_IMAGE_URL.'/cat-placeholder.png';
				}
				
			}else{
				$term_meta_image = get_option( "providers-category_image_".$cid );
				$providerimage = (!empty($term_meta_image)) ? esc_attr( $term_meta_image ) : '';
				if($providerimage != ""){
					$imageid = service_finder_get_image_id_by_link($providerimage);
					$image_attributes = wp_get_attachment_image_src( $imageid, $imagesize );
					if($image_attributes[0] != '')
					{
						return $image_attributes[0];
					}else{
						return SERVICE_FINDER_BOOKING_IMAGE_URL.'/cat-placeholder.png';
					}
				}else{
					return SERVICE_FINDER_BOOKING_IMAGE_URL.'/cat-placeholder.png';		
				}
			}
		
		
		}else{
			return SERVICE_FINDER_BOOKING_IMAGE_URL.'/cat-placeholder.png';		
		}
												
}

/** Get Category name by catgory id*/
function service_finder_getCategoryName($cid = 0,$taxonomy = 'providers-category'){
		if($cid > 0){
		$term = get_term( $cid, $taxonomy );
		if(!empty($term)){
		return (!empty($term->name)) ? $term->name : '';
		}else{
		return '';
		}
		}else{
		return '';
		}
}

/** Get Category description by catgory id*/
function service_finder_getCategoryDescription($cid = 0,$taxonomy = 'providers-category'){
		if($cid > 0){
		$term = get_term( $cid, $taxonomy );
		if(!empty($term)){
		return (!empty($term->description)) ? $term->description : '';
		}else{
		return '';
		}
		}else{
		return '';
		}
}

/** Get Category name by catgory id via sql query*/
function service_finder_getCategoryNameviaSql($cid = 0){
		global $wpdb;
		$term = $wpdb->get_row($wpdb->prepare('SELECT * FROM `'.$wpdb->prefix.'terms` WHERE `term_id` = %d',$cid));
		return $term->name;
}

/*Get Category Icon*/
function service_finder_getCategoryIcon($cid = 0, $size = 'service_finder-marker-icon'){
global $wpdb, $service_finder_Tables, $service_finder_options;
		
		if($cid > 0){
		$term_meta_icon = get_option( "providers-category_icon_".$cid );
		$providerimage = esc_attr( $term_meta_icon ) ? esc_attr( $term_meta_icon ) : '';
		$icon = (!empty($service_finder_options['default-map-marker-icon']['url'])) ? $service_finder_options['default-map-marker-icon']['url'] : '';
		
		$imgid = service_finder_get_image_id_by_link($providerimage);
		$src = wp_get_attachment_image_src( $imgid, $size );
		
		$srcpath = (!empty($src[0])) ? $src[0] : '';
		
		if($srcpath != ""){
			return $srcpath;
		}else{
			$term = get_term( $cid, "providers-category" );
			$term_parent = '';
			if(!empty($term)){
				$term_parent = (isset($term->parent)) ? $term->parent : '';
			}
			if($term_parent > 0){
				$termid = $term->parent;
				$term_meta_icon = get_option( "providers-category_icon_".$termid );
				$providerimage = esc_attr( $term_meta_icon ) ? esc_attr( $term_meta_icon ) : '';
				
				$imgid = service_finder_get_image_id_by_link($providerimage);
				$src = wp_get_attachment_image_src( $imgid, $size );
				
				if($size == 'service_finder-marker-icon'){
					if(!empty($src[0])){
						return $src[0];
					}elseif(!empty($icon)){
						return $icon;
					}else{
						return '';
					}
				}else{
					if(!empty($src[0])){
						return $src[0];
					}else{
						return '';
					}
				}
				
			}else{
				if($size == 'service_finder-marker-icon'){
					if(!empty($icon)){
						return $icon;
					}else{
						return '';
					}
				}else{
					return '';
				}
				
			}
		}
		}else{
			return '';
		}
												
}

/*Get Category Icon*/
function service_finder_getTermIcon($cid = 0, $size = 'service_finder-amenity-icon'){
global $wpdb, $service_finder_Tables, $service_finder_options;
		
		if($cid > 0){
		$term_meta_icon = get_option( "cat_icon_".$cid );
		$providerimage = esc_attr( $term_meta_icon ) ? esc_attr( $term_meta_icon ) : '';
		$icon = (!empty($service_finder_options['default-map-marker-icon']['url'])) ? $service_finder_options['default-map-marker-icon']['url'] : '';
		
		$imgid = service_finder_get_image_id_by_link($providerimage);
		$src = wp_get_attachment_image_src( $imgid, $size );
		
		if(!empty($src[0])){
			return $src[0];
		}else{
			return '';
		}
			
		}											
}

/*Get Category Icon*/
function service_finder_getCategoryColor($cid = 0){
global $wpdb, $service_finder_Tables, $service_finder_options;
		
		if($cid > 0){
		$categorycolor = get_term_meta( $cid, 'provider_category_color', true );
		
		if($categorycolor != ""){
			return $categorycolor;
		}else{
			$term = get_term( $cid, "providers-category" );
			$term_parent = '';
			if(!empty($term)){
				$term_parent = (isset($term->parent)) ? $term->parent : '';
			}
			if($term_parent > 0){
				$termid = $term->parent;
				$categorycolor = get_term_meta( $termid, 'provider_category_color', true );
				if($categorycolor != ""){
					return $categorycolor;
				}
			}else{
				return '';
			}
		}
		}else{
			return '';
		}
												
}

/*Get brache address*/
function service_finder_getBranches($bid = 0){
global $wpdb,$service_finder_Tables;

$res = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->branches.' WHERE id = %d',$bid));
if(!empty($res)){
$address = $res->address;
$city = $res->city;
$state = $res->state;
$country = $res->country;

$state = (!empty($res->state)) ? ', '.esc_html($res->state) : '';
		
$fulladdress = $address.', '.$city.$state.', '.$country;

return $fulladdress;
}else{
return '';
}

}


/*Get Cities*/
function service_finder_getCities($country = ''){
global $wpdb, $service_finder_Tables, $service_finder_options;

	/*Get cities*/
	$identitycheck = (isset($service_finder_options['identity-check'])) ? esc_attr($service_finder_options['identity-check']) : '';
	$restrictuserarea = (isset($service_finder_options['restrict-user-area'])) ? esc_attr($service_finder_options['restrict-user-area']) : '';
	if($restrictuserarea && $identitycheck){
		$maincities = $wpdb->get_results($wpdb->prepare('SELECT DISTINCT city FROM '.$service_finder_Tables->providers.' WHERE admin_moderation = "approved" AND identity = "approved" AND account_blocked != "yes" AND `country` LIKE "%s" ORDER BY `city`',$country));
	}else{
		$maincities = $wpdb->get_results($wpdb->prepare('SELECT DISTINCT city FROM '.$service_finder_Tables->providers.' WHERE admin_moderation = "approved" AND account_blocked != "yes" AND `country` LIKE "%s" ORDER BY `city`',$country));
	}	
	
	$branchcities = $wpdb->get_results($wpdb->prepare("select DISTINCT city from ".$service_finder_Tables->branches." WHERE country = '%s' ORDER BY `city`",$country));
					
	$allcities = array();
	
	if(!empty($maincities)){
	foreach($maincities as $city){
		$allcities[] = $city->city;
	}
	}
	
	if(!empty($branchcities)){
	foreach($branchcities as $city){
		$allcities[] = $city->city;
	}
	}
	
	$allcities = array_unique($allcities);
	sort($allcities);
		
	$cityhtml = '<option value="">'.esc_html__('Select City', 'service-finder').'</option>';
	if(!empty($allcities)){
	foreach($allcities as $city){
		$cityname = service_finder_get_cityname_by_slug($city);
		$cityhtml .= '<option value="'.esc_attr($city).'">'.$cityname.'</option>';
	}
	}else{
		$cityhtml .= '<option value="">'.esc_html__('No city available', 'service-finder').'</option>';
	}
	
	/*Get states*/
	$identitycheck = (isset($service_finder_options['identity-check'])) ? esc_attr($service_finder_options['identity-check']) : '';
	$restrictuserarea = (isset($service_finder_options['restrict-user-area'])) ? esc_attr($service_finder_options['restrict-user-area']) : '';
	if($restrictuserarea && $identitycheck){
		$mainstates = $wpdb->get_results($wpdb->prepare('SELECT DISTINCT state FROM '.$service_finder_Tables->providers.' WHERE admin_moderation = "approved" AND identity = "approved" AND account_blocked != "yes" AND `country` LIKE "%s" ORDER BY `state`',$country));
	}else{
		$mainstates = $wpdb->get_results($wpdb->prepare('SELECT DISTINCT state FROM '.$service_finder_Tables->providers.' WHERE admin_moderation = "approved" AND account_blocked != "yes" AND `country` LIKE "%s" ORDER BY `state`',$country));
	}	
	
	$branchstates = $wpdb->get_results($wpdb->prepare("select DISTINCT state from ".$service_finder_Tables->branches." WHERE country = '%s' ORDER BY `state`",$country));
					
	$allstates = array();
	
	if(!empty($mainstates)){
	foreach($mainstates as $state){
		$allstates[] = $state->state;
	}
	}
	
	if(!empty($branchstates)){
	foreach($branchstates as $state){
		$allstates[] = $state->state;
	}
	}
	
	$allstates = array_unique($allstates);
	sort($allstates);
		
	$statehtml = '<option value="">'.esc_html__('Select State', 'service-finder').'</option>';
	if(!empty($allstates)){
	foreach($allstates as $state){
		$statehtml .= '<option value="'.esc_attr($state).'">'.$state.'</option>';
	}
	}else{
		$statehtml .= '<option value="">'.esc_html__('No state available', 'service-finder').'</option>';
	}
		
	$success = array(
			'status' => 'success',
			'cityhtml' => $cityhtml,
			'statehtml' => $statehtml,
			);
	$service_finder_Success = json_encode($success);
	return $service_finder_Success;
}

/*Get Cities by states*/
function service_finder_get_cities_by_states($state = ''){
global $wpdb, $service_finder_Tables, $service_finder_options;

	/*Get cities*/
	$identitycheck = (isset($service_finder_options['identity-check'])) ? esc_attr($service_finder_options['identity-check']) : '';
	$restrictuserarea = (isset($service_finder_options['restrict-user-area'])) ? esc_attr($service_finder_options['restrict-user-area']) : '';
	if($restrictuserarea && $identitycheck){
		$maincities = $wpdb->get_results($wpdb->prepare('SELECT DISTINCT city FROM '.$service_finder_Tables->providers.' WHERE admin_moderation = "approved" AND identity = "approved" AND account_blocked != "yes" AND `state` LIKE "%s" ORDER BY `city`',$state));
	}else{
		$maincities = $wpdb->get_results($wpdb->prepare('SELECT DISTINCT city FROM '.$service_finder_Tables->providers.' WHERE admin_moderation = "approved" AND account_blocked != "yes" AND `state` LIKE "%s" ORDER BY `city`',$state));
	}	
	
	$branchcities = $wpdb->get_results($wpdb->prepare("select DISTINCT city from ".$service_finder_Tables->branches." WHERE state = '%s' ORDER BY `city`",$state));
					
	$allcities = array();
	
	if(!empty($maincities)){
	foreach($maincities as $city){
		$allcities[] = $city->city;
	}
	}
	
	if(!empty($branchcities)){
	foreach($branchcities as $city){
		$allcities[] = $city->city;
	}
	}
	
	$allcities = array_unique($allcities);
	sort($allcities);
		
	$cityhtml = '<option value="">'.esc_html__('Select City', 'service-finder').'</option>';
	if(!empty($allcities)){
	foreach($allcities as $city){
		$cityname = service_finder_get_cityname_by_slug($city);
		$cityhtml .= '<option value="'.esc_attr($city).'">'.$cityname.'</option>';
	}
	}else{
		$cityhtml .= '<option value="">'.esc_html__('No city available', 'service-finder').'</option>';
	}
	
	$success = array(
			'status' => 'success',
			'cityhtml' => $cityhtml,
			);
	$service_finder_Success = json_encode($success);
	return $service_finder_Success;
}

/*Get Packages for provider signup*/
function service_finder_getPackages($selectedpackage = ''){
global $wpdb, $service_finder_Tables, $service_finder_options;
$html = '';
$currency = service_finder_currencycode();
for ($i=0; $i <= 3; $i++) {
					if (isset($service_finder_options['payment-type']) && ($service_finder_options['payment-type'] == 'recurring') && $i > 0) {
						$billingPeriod = esc_html__('year','service-finder');
						$packagebillingperiod = (!empty($service_finder_options['package'.$i.'-billing-period'])) ? $service_finder_options['package'.$i.'-billing-period'] : '';
						switch ($packagebillingperiod) {
							case 'Year':
								$billingPeriod = esc_html__('year','service-finder');
								break;
							case 'Month':
								$billingPeriod = esc_html__('month','service-finder');
								break;
							case 'Week':
								$billingPeriod = esc_html__('week','service-finder');
								break;
							case 'Day':
								$billingPeriod = esc_html__('day','service-finder');
								break;
						}
					}
					$packageprice = (isset($service_finder_options['package'.$i.'-price']) && $i > 0) ? $service_finder_options['package'.$i.'-price'] : '';
					$enablepackage = (!empty($service_finder_options['enable-package'.$i])) ? $service_finder_options['enable-package'.$i] : '';
					$paymenttype = (!empty($service_finder_options['payment-type'])) ? $service_finder_options['payment-type'] : '';
					$packagename = (!empty($service_finder_options['package'.$i.'-name'])) ? $service_finder_options['package'.$i.'-name'] : '';
					
					$free = (trim($packageprice) == '0' || $i == 0) ? true : false;
					if(isset($service_finder_options['enable-package'.$i]) && $enablepackage > 0){
					
					if($selectedpackage == 'package_'.esc_attr($i)){
					$select = 'selected="selected"';
					}else{
					$select = '';
					}
					
						$html .= '<option '.$select.' value="package_'.esc_attr($i).'"'; 
						if($free) { $html .= ' class="free"'; } $html .= '>'.$packagename;
						$html .= '</option>';
					}
				}		

return $html;
}

/*Check is there only free package or not*/
function service_finder_check_only_free_package(){
global $service_finder_options;
$result = array();
$k = 0;
$flag = 0;
for ($i=0; $i <= 3; $i++) {
$packageprice = (isset($service_finder_options['package'.$i.'-price']) && $i > 0) ? $service_finder_options['package'.$i.'-price'] : 0;
$enablepackage = (!empty($service_finder_options['enable-package'.$i])) ? $service_finder_options['enable-package'.$i] : '';

if(isset($service_finder_options['enable-package'.$i]) && $enablepackage > 0){
if(intval($packageprice) == 0){
$packageid = $i;
$flag++;
}
$k++;
}
}		

if($k == 1 && $flag == 1){
$result = array(
	'freepackage' => 'yes',
	'packageid' => $packageid,
	);
return $result;
}else{
return $result;
}
}

/*Default package is free or not*/
function service_finder_check_default_package_is_free($defaultpackage = ''){
	global $service_finder_options;
	
	$roleNum = intval(substr($defaultpackage, 8));
	
	$packageprice = service_finder_get_data($service_finder_options,'package'.$roleNum.'-price',0);
	
	if(intval($packageprice) == 0){
		return true;
	}else
	{
		return false;
	}
}

/*Get Packages for claim business*/
function service_finder_claimed_getPackages($selectedpackage = ''){
global $wpdb, $service_finder_Tables, $service_finder_options;
$html = '';
$currency = service_finder_currencycode();
for ($i=1; $i <= 3; $i++) {
					if (isset($service_finder_options['payment-type']) && ($service_finder_options['payment-type'] == 'recurring')) {
						$billingPeriod = esc_html__('year','service-finder');
						$packagebillingperiod = (!empty($service_finder_options['package'.$i.'-billing-period'])) ? $service_finder_options['package'.$i.'-billing-period'] : '';
						switch ($packagebillingperiod) {
							case 'Year':
								$billingPeriod = esc_html__('year','service-finder');
								break;
							case 'Month':
								$billingPeriod = esc_html__('month','service-finder');
								break;
							case 'Week':
								$billingPeriod = esc_html__('week','service-finder');
								break;
							case 'Day':
								$billingPeriod = esc_html__('day','service-finder');
								break;
						}
					}
					$packageprice = (isset($service_finder_options['package'.$i.'-price']) && $i > 0) ? $service_finder_options['package'.$i.'-price'] : '';
					$enablepackage = (!empty($service_finder_options['enable-package'.$i])) ? $service_finder_options['enable-package'.$i] : '';
					$paymenttype = (!empty($service_finder_options['payment-type'])) ? $service_finder_options['payment-type'] : '';
					$packagename = (!empty($service_finder_options['package'.$i.'-name'])) ? $service_finder_options['package'.$i.'-name'] : '';
					
					$free = (trim($packageprice) == '0') ? true : false;
					if(isset($service_finder_options['enable-package'.$i])){
					
					if($selectedpackage == 'package_'.esc_attr($i)){
					$select = 'selected="selected"';
					}else{
					$select = '';
					}
					
						$html .= '<option '.$select.' value="package_'.esc_attr($i).'"'; 
						if($free) { $html .= ' class="free"'; } $html .= '>'.$packagename;
						if(!$free) {
							if (isset($service_finder_options['payment-type']) && ($paymenttype == 'recurring')) {
								$html .= ' - '.trim($packageprice).' '.$currency.' '.esc_html__('per','service-finder').' '.$billingPeriod;
							} else {
								$html .= ' ('.$packageprice.' '.service_finder_currencysymbol().')';
							}
						} 
						$html .= '</option>';
					}
				}		

return $html;
}

/*Check Provider Capability by package*/
function service_finder_get_capability($uid = 0){
global $wpdb, $service_finder_options;
$package = get_user_meta($uid,'provider_role',true);
$userCap = array();
$packageNum = intval(substr($package, 8));
if($package != ''){
$cap = (!empty($service_finder_options['package'.$packageNum.'-capabilities'])) ? $service_finder_options['package'.$packageNum.'-capabilities'] : '';
$subcap = (!empty($service_finder_options['package'.$packageNum.'-subcapabilities'])) ? $service_finder_options['package'.$packageNum.'-subcapabilities'] : '';
	if(!empty($cap['booking'])){
	if($cap['booking']){
		$userCap[] = 'bookings';	
	}
	}
	if(!empty($cap['cover-image'])){
	if($cap['cover-image']){
		$userCap[] = 'cover-image';	
	}
	}
	if(!empty($cap['gallery-images'])){
	if($cap['gallery-images']){
		$userCap[] = 'gallery-images';	
	}
	}
	if(!empty($cap['multiple-categories'])){
	if($cap['multiple-categories']){
		$userCap[] = 'multiple-categories';	
	}
	}
	if(!empty($cap['apply-for-job'])){
	if($cap['apply-for-job']){
		$userCap[] = 'apply-for-job';	
	}
	}
	if(!empty($cap['job-alerts'])){
	if($cap['job-alerts']){
		$userCap[] = 'job-alerts';	
	}
	}
	if(!empty($cap['branches'])){
	if($cap['branches']){
		$userCap[] = 'branches';	
	}
	}
	if(!empty($cap['google-calendar'])){
	if($cap['google-calendar']){
		$userCap[] = 'google-calendar';	
	}
	}
	if(!empty($cap['crop'])){
	if($cap['crop']){
		$userCap[] = 'crop';	
	}
	}
	if(!empty($cap['message-system'])){
	if($cap['message-system']){
		$userCap[] = 'message-system';	
	}
	}
	if(!empty($cap['contact-numbers'])){
	if($cap['contact-numbers']){
		$userCap[] = 'contact-numbers';	
	}
	}
	
	if(!empty($subcap)){
		foreach($subcap as $key => $val){
			if($val == 1){
				$userCap[] = $key;	
			}
		}
	}
}	

return $userCap;
}

/*Delete Provider's Data when delete user*/
function service_finder_deleteProvidersData($user_id = 0){
global $wpdb, $service_finder_Tables;
/*Delete Providers*/
$wpdb->query($wpdb->prepare('DELETE FROM '.$service_finder_Tables->providers.' WHERE wp_user_id = %d',$user_id));

$commentpostid = get_user_meta($user_id, 'comment_post', true);

wp_delete_post( $commentpostid, true );

delete_user_meta($user_id, 'upgrade_request');
delete_user_meta($user_id, 'upgrade_request_status');

/*Delete User Attchments*/
$galleryattchments = service_finder_getProviderAttachments($user_id,'gallery');
foreach($galleryattchments as $galleryattchment){
wp_delete_attachment( $galleryattchment->attachmentid, true );
}
$galleryattchments = service_finder_getProviderAttachments($user_id,'file');
foreach($galleryattchments as $galleryattchment){
wp_delete_attachment( $galleryattchment->attachmentid, true );
}
$wpdb->query($wpdb->prepare('DELETE FROM '.$service_finder_Tables->attachments.' WHERE wp_user_id = %d',$user_id));

/*Delete providers bookings*/
$wpdb->query($wpdb->prepare('DELETE FROM '.$service_finder_Tables->bookings.' WHERE provider_id = %d',$user_id));

/*If user is customer then delete customer data from customer table*/
$wpdb->query($wpdb->prepare('DELETE FROM '.$service_finder_Tables->customers_data.' WHERE wp_user_id = %d',$user_id));
$wpdb->query($wpdb->prepare('DELETE FROM '.$service_finder_Tables->customers.' WHERE wp_user_id = %d',$user_id));

/*Delete Providers Feedback*/
$wpdb->query($wpdb->prepare('DELETE FROM '.$service_finder_Tables->feedback.' WHERE provider_id = %d',$user_id));

/*Delete Providers Invoice Generated*/
$wpdb->query($wpdb->prepare('DELETE FROM '.$service_finder_Tables->invoice.' WHERE provider_id = %d',$user_id));

/*Delete Providers Services*/
$wpdb->query($wpdb->prepare('DELETE FROM '.$service_finder_Tables->services.' WHERE wp_user_id = %d',$user_id));

/*Delete Providers Service Area*/
$wpdb->query($wpdb->prepare('DELETE FROM '.$service_finder_Tables->service_area.' WHERE provider_id = %d',$user_id));

/*Delete Providers Team Members*/
$wpdb->query($wpdb->prepare('DELETE FROM '.$service_finder_Tables->team_members.' WHERE admin_wp_id = %d',$user_id));

/*Delete Providers Timeslot*/
$wpdb->query($wpdb->prepare('DELETE FROM '.$service_finder_Tables->timeslots.' WHERE provider_id = %d',$user_id));

/*Delete Providers UnAvailability*/
$wpdb->query($wpdb->prepare('DELETE FROM '.$service_finder_Tables->unavailability.' WHERE provider_id = %d',$user_id));

/*Delete Feature Providers*/
$wpdb->query($wpdb->prepare('DELETE FROM '.$service_finder_Tables->feature.' WHERE provider_id = %d',$user_id));

/*Delete Providers from favorite*/
$wpdb->query($wpdb->prepare('DELETE FROM '.$service_finder_Tables->favorites.' WHERE provider_id = %d',$user_id));

}

/*Get Author's Link by Author ID*/
function service_finder_get_author_url($author_id = 0, $author_nicename = '') {

	$link = get_author_posts_url($author_id);

	$link = apply_filters('author_link', $link, $author_id, $author_nicename);

	return $link;
}

/*Get Author's Link For Invoce Payment*/
function service_finder_get_invoice_author_url($author_id = 0, $author_nicename = '',$invoice_id = 0) {

	if(get_option('permalink_structure')){
		$link = get_author_posts_url($author_id).'?invoiceid='.service_finder_encrypt($invoice_id, 'Developer#@)!%').'#invoiceview';
	}else{
		$link = get_author_posts_url($author_id).'&invoiceid='.service_finder_encrypt($invoice_id, 'Developer#@)!%').'#invoiceview';
	}

	$link = apply_filters('author_link', $link, $author_id, $author_nicename);

	return $link;
}

// To Get attachment image ID By Image Link
function service_finder_get_image_id_by_link($link = '')
{
    global $wpdb;

    $newlink = preg_replace('/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $link);

    $imageid = $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE guid='%s'",$newlink));
 if(empty($imageid)){$imageid = $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE guid='%s'",$link));}
 return $imageid;
}

/*Encrypt Qyery String*/
function service_finder_encrypt($id  = '', $key = '')
{
    //$id = base_convert($id, 10, 36); // Save some space
    //$data = mcrypt_encrypt(MCRYPT_BLOWFISH, $key, $id, 'ecb');
    //$data = bin2hex($data);
	
    return base64_encode($id);
}

/*Decrypt Qyery String*/
function service_finder_decrypt($encrypted_id  = '', $key = '')
{
    //$data = pack('H*', $encrypted_id); // Translate back to binary
    //$data = mcrypt_decrypt(MCRYPT_BLOWFISH, $key, $data, 'ecb');
    //$data = base_convert($data, 36, 10);

    return base64_decode($encrypted_id);
}


/*Mailing Function*/
function service_finder_wpmailer($to = '',$subject = '',$message = ''){
global $service_finder_options, $wp_filesystem;
	if ( empty( $wp_filesystem ) ) {
          require_once ABSPATH . '/wp-admin/includes/file.php';
          WP_Filesystem();
    }
	add_filter('wp_mail_content_type', 'service_finder_set_html_content_type');

	$emaillogo = (!empty($service_finder_options['email-logo']['url'])) ? $service_finder_options['email-logo']['url'] : '';
	
	$sitelogo = (!empty($service_finder_options['site-logo']['url'])) ? $service_finder_options['site-logo']['url'] : '';
	
	if($emaillogo != ""){
		$logo = '<img src="'.$emaillogo.'" style="max-width:100%; height:auto; display:block; margin:10px 0 20px;">';
	}elseif($sitelogo != ""){
		$logo = '<img src="'.$sitelogo.'" style="max-width:100%; height:auto; display:block; margin:10px 0 20px;">';
	}else{ 
		$logo = '';
	}

	$link_color = (!empty($service_finder_options['link-color'])) ? $service_finder_options['link-color'] : '#56C477';

	$template = $wp_filesystem->get_contents(SERVICE_FINDER_BOOKING_TEMPLATES_DIR.'/default.html', true);
	
	if(is_rtl()){  
	$dir = 'rtl';
	}else{
	$dir = 'ltr';
	}
	
	$filter = array('%SITELOGO%','%MAILBODY%','%LINKCOLOR%','%CHARSET%','%DIRECTION%');
	$replace = array($logo,wpautop($message),$link_color,get_bloginfo( 'charset' ),$dir);
	$headers = array('Content-Type: text/html; charset='.get_bloginfo( 'charset' ));
	$message = str_replace($filter, $replace, $template);

	if(wp_mail($to,$subject,$message,$headers)){
	return true;
	}else{
	return false;
	}
	
	remove_filter('wp_mail_content_type','service_finder_set_html_content_type');

}

/*Set content type for mail function*/
function service_finder_set_html_content_type() {
	return 'text/html';
}

/*Get page url by shortcode call withing that page*/
function service_finder_get_url_by_shortcode($shortcode = '') {
	global $wpdb;

	$url = '';

	$sql = 'SELECT ID FROM ' . $wpdb->posts . ' WHERE post_type = "page" AND post_status="publish" AND post_content LIKE "%'.$shortcode.'%"';

	if ($id = $wpdb->get_var($sql)) {
		$url = get_permalink($id);
	}

	return $url;
}

/*Get page id by shortcode call withing that page*/
function service_finder_get_page_id_by_shortcode($shortcode = '') {
	global $wpdb;

	$pageid = '';

	$sql = 'SELECT ID FROM ' . $wpdb->posts . ' WHERE post_type = "page" AND post_status="publish" AND post_content LIKE "%'.$shortcode.'%"';

	if ($id = $wpdb->get_var($sql)) {
		$pageid = $id;
	}

	return $pageid;
}

/*Get page id by shortcode call withing that page*/
function service_finder_get_id_by_shortcode($shortcode = '') {
	global $wpdb;

	$url = '';
	$pageids = array();

	$sql = 'SELECT ID FROM ' . $wpdb->posts . ' WHERE post_type = "page" AND post_status="publish" AND post_content LIKE "%'.$shortcode.'%"';

	if ($results = $wpdb->get_results($sql)) {
		foreach($results as $res){
			$pageids[] = $res->ID;
		}
	}
	return $pageids;

	
}

/*Get User avatar id by its user id*/
function service_finder_getUserAvatarID($userid = 0){
global $wpdb,$service_finder_Tables;
$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' where wp_user_id = %d',$userid));
if(!empty($row)){
return $row->avatar_id;
}else{
return '';
}
}

/*Get User avatar id by its user id*/
function service_finder_getCustomerAvatarID($userid = 0){
global $wpdb,$service_finder_Tables;
$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers_data.' where wp_user_id = %d',$userid));
if(!empty($row)){
return $row->avatar_id;
}else{
return '';
}
}

/*Get User avatar id by its user id*/
function service_finder_get_avatar_by_userid($userid = 0,$size = 'medium'){
global $wpdb,$service_finder_Tables;

if(service_finder_getUserRole($userid) == 'Customer'){
	$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers_data.' where wp_user_id = %d',$userid));
	if(!empty($row)){
	if($row->avatar_id > 0){
	return service_finder_get_user_profile_image($row->avatar_id);
	}else{
	return service_finder_get_default_avatar();
	}
	}else{
	return service_finder_get_default_avatar();
	}
	service_finder_get_user_profile_image($avatar_id);
}elseif(service_finder_getUserRole($userid) == 'Provider'){
	$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' where wp_user_id = %d',$userid));
	
	$croppedavatar = get_user_meta($userid, 'cropped_user_avatar', true);
	$src = '';
	if(service_finder_user_has_capability('crop',$userid) && $croppedavatar != '' && $croppedavatar > 0)
	{
		$src  = wp_get_attachment_image_src( $croppedavatar, $size );
		$src  = $src[0];
		return $src;
	}
	
	if(!empty($row)){
	if($row->avatar_id > 0){
	return service_finder_get_user_profile_image($row->avatar_id,$size);
	}else{
	return service_finder_get_default_avatar();
	}
	}else{
	return service_finder_get_default_avatar();
	}
}else{
	return service_finder_get_default_avatar();
}
}

/*Get Total number of providers*/
function service_finder_totalProviders(){
global $wpdb,$service_finder_Tables, $service_finder_options;
$identitycheck = (isset($service_finder_options['identity-check'])) ? esc_attr($service_finder_options['identity-check']) : '';
$restrictuserarea = (isset($service_finder_options['restrict-user-area'])) ? esc_attr($service_finder_options['restrict-user-area']) : '';
if($restrictuserarea && $identitycheck){
$res = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->providers.' WHERE admin_moderation = "approved" AND identity = "approved" AND account_blocked != "yes"');
}else{
$res = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->providers.' WHERE admin_moderation = "approved" AND account_blocked != "yes"');
}
if(!empty($res)){
	return count($res);
}else{
	return 0;
}
}

/*Get Total number of customers*/
function service_finder_totalCustomers(){
global $wpdb,$service_finder_Tables;

$res = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->customers_data);

if(!empty($res)){
	return count($res);
}else{
	return 0;
}
}

/*Get feature providers*/
function service_finder_getFeaturedProviders($limit = 3,$categoryid = 0){
global $wpdb,$service_finder_Tables, $service_finder_options;
$identitycheck = (isset($service_finder_options['identity-check'])) ? esc_attr($service_finder_options['identity-check']) : '';
$restrictuserarea = (isset($service_finder_options['restrict-user-area'])) ? esc_attr($service_finder_options['restrict-user-area']) : '';

if($restrictuserarea && $identitycheck){
if($categoryid > 0){
$providers = $wpdb->get_results($wpdb->prepare('SELECT featured.id, provider.full_name, provider.phone, provider.mobile, provider.avatar_id, provider.bio, provider.category_id, featured.provider_id, featured.amount, featured.days, featured.status FROM '.$service_finder_Tables->feature.' as featured INNER JOIN '.$service_finder_Tables->providers.' as provider on featured.provider_id = provider.wp_user_id WHERE admin_moderation = "approved" AND account_blocked != "yes" AND FIND_IN_SET("'.$categoryid.'", provider.category_id) AND identity = "approved" AND featured.feature_status = "active" AND (featured.status = "Paid" OR featured.status = "Free") ORDER BY RAND() limit 0,%d',$limit));
}else{
$providers = $wpdb->get_results($wpdb->prepare('SELECT featured.id, provider.full_name, provider.phone, provider.mobile, provider.avatar_id, provider.bio, provider.category_id, featured.provider_id, featured.amount, featured.days, featured.status FROM '.$service_finder_Tables->feature.' as featured INNER JOIN '.$service_finder_Tables->providers.' as provider on featured.provider_id = provider.wp_user_id WHERE admin_moderation = "approved" AND account_blocked != "yes" AND identity = "approved" AND featured.feature_status = "active" AND (featured.status = "Paid" OR featured.status = "Free") ORDER BY RAND() limit 0,%d',$limit));
}
}else{
if($categoryid > 0){
$providers = $wpdb->get_results($wpdb->prepare('SELECT featured.id, provider.full_name, provider.phone, provider.mobile, provider.avatar_id, provider.bio, provider.category_id, featured.provider_id, featured.amount, featured.days, featured.status FROM '.$service_finder_Tables->feature.' as featured INNER JOIN '.$service_finder_Tables->providers.' as provider on featured.provider_id = provider.wp_user_id WHERE admin_moderation = "approved" AND account_blocked != "yes" AND FIND_IN_SET("'.$categoryid.'", provider.category_id) AND featured.feature_status = "active" AND (featured.status = "Paid" OR featured.status = "Free") ORDER BY RAND() limit 0,%d',$limit));
}else{
$providers = $wpdb->get_results($wpdb->prepare('SELECT featured.id, provider.full_name, provider.phone, provider.mobile, provider.avatar_id, provider.bio, provider.category_id, featured.provider_id, featured.amount, featured.days, featured.status FROM '.$service_finder_Tables->feature.' as featured INNER JOIN '.$service_finder_Tables->providers.' as provider on featured.provider_id = provider.wp_user_id WHERE admin_moderation = "approved" AND account_blocked != "yes" AND featured.feature_status = "active" AND (featured.status = "Paid" OR featured.status = "Free") ORDER BY RAND() limit 0,%d',$limit));
}
}

return $providers;

}

/*Get currecy code*/
if ( !function_exists( 'service_finder_currencycode' ) ){
function service_finder_currencycode(){
global $service_finder_options;
$currency = (!empty($service_finder_options['currency-code'])) ? $service_finder_options['currency-code'] : 'USD';
return $currency;
}
}

/* Currecy List */
function service_finder_get_currency_list(){
	$currency = array(
    'AED' => esc_html__( 'United Arab Emirates Dirham', 'service-finder' ),
    'AUD' => esc_html__( 'Australian Dollars (&#36;)', 'service-finder' ),
	'ARS' => esc_html__( 'Argentina (&#36;)', 'service-finder' ),
    'BDT' => esc_html__( 'Bangladeshi Taka (&#2547;&nbsp;)', 'service-finder' ),
    'BRL' => esc_html__( 'Brazilian Real (&#82;&#36;)', 'service-finder' ),
    'BGN' => esc_html__( 'Bulgarian Lev (&#1083;&#1074;.)', 'service-finder' ),
	'BZD' => esc_html__( 'Belize Dollar (BZ&#36;)', 'service-finder' ),
	'BHD' => esc_html__( 'Bahraini dinar (BD)', 'service-finder' ),
    'CAD' => esc_html__( 'Canadian Dollars (&#36;)', 'service-finder' ),
    'CLP' => esc_html__( 'Chilean Peso (&#36;)', 'service-finder' ),
    'CNY' => esc_html__( 'Chinese Yuan (&yen;)', 'service-finder' ),
    'COP' => esc_html__( 'Colombian Peso (&#36;)', 'service-finder' ),
    'CZK' => esc_html__( 'Czech Koruna (&#75;&#269;)', 'service-finder' ),
    'DZD' => esc_html__( 'Algerian Dinar', 'service-finder' ),
	'DKK' => esc_html__( 'Danish Krone (kr.)', 'service-finder' ),
    'DOP' => esc_html__( 'Dominican Peso (RD&#36;)', 'service-finder' ),
    'EUR' => esc_html__( 'Euros (&euro;)', 'service-finder' ),
    'GYD' => esc_html__( 'Guyanese dollar (GY$)', 'service-finder' ),
	'GHS' => esc_html__( 'Ghanaian cedi (GH&#8373;)', 'service-finder' ),
	'HKD' => esc_html__( 'Hong Kong Dollar (&#36;)', 'service-finder' ),
    'HRK' => esc_html__( 'Croatia kuna (Kn)', 'service-finder' ),
    'HUF' => esc_html__( 'Hungarian Forint (&#70;&#116;)', 'service-finder' ),
    'ISK' => esc_html__( 'Icelandic krona (Kr.)', 'service-finder' ),
    'IDR' => esc_html__( 'Indonesia Rupiah (Rp)', 'service-finder' ),
    'INR' => esc_html__( 'Indian Rupee (Rs.)', 'service-finder' ),
	'PKR' => esc_html__( 'Pakistani Rupee (Rs.)', 'service-finder' ),
    'NPR' => esc_html__( 'Nepali Rupee (Rs.)', 'service-finder' ),
    'ILS' => esc_html__( 'Israeli Shekel (&#8362;)', 'service-finder' ),
    'JPY' => esc_html__( 'Japanese Yen (&yen;)', 'service-finder' ),
    'KIP' => esc_html__( 'Lao Kip (&#8365;)', 'service-finder' ),
    'KRW' => esc_html__( 'South Korean Won (&#8361;)', 'service-finder' ),
    'MAD' => esc_html__( 'Moroccan Dirham (&#x2e;&#x62f;&#x2e;&#x645;)', 'service-finder' ),
	'MYR' => esc_html__( 'Malaysian Ringgits (&#82;&#77;)', 'service-finder' ),
	'MVR' => esc_html__( 'Maldivian Rufiyaa (Rf)', 'service-finder' ),
    'MXN' => esc_html__( 'Mexican Peso (&#36;)', 'service-finder' ),
    'NGN' => esc_html__( 'Nigerian Naira (&#8358;)', 'service-finder' ),
    'NOK' => esc_html__( 'Norwegian Krone (&#107;&#114;)', 'service-finder' ),
    'NZD' => esc_html__( 'New Zealand Dollar (&#36;)', 'service-finder' ),
    'PEN' => esc_html__( 'Peru (Sol)', 'service-finder' ),
	'PYG' => esc_html__( 'Paraguayan Guaran&&iuml; (&#8370;)', 'service-finder' ),
    'PHP' => esc_html__( 'Philippine Pesos (&#8369;)', 'service-finder' ),
    'PLN' => esc_html__( 'Polish Zloty (&#122;&#322;)', 'service-finder' ),
    'GBP' => esc_html__( 'Pounds Sterling (&pound;)', 'service-finder' ),
    'RON' => esc_html__( 'Romanian Leu (lei)', 'service-finder' ),
    'RUB' => esc_html__( 'Russian Ruble (&#1088;&#1091;&#1073;.)', 'service-finder' ),
	'UAH' => esc_html__( 'Ukranian Hrivna (&#8372;)', 'service-finder' ),
    'SGD' => esc_html__( 'Singapore Dollar (&#36;)', 'service-finder' ),
	'LKR' => esc_html__( 'Sri Lankan Rupee (Rs)', 'service-finder' ),
    'ZAR' => esc_html__( 'South African rand (&#82;)', 'service-finder' ),
    'SEK' => esc_html__( 'Swedish Krona (&#107;&#114;)', 'service-finder' ),
    'CHF' => esc_html__( 'Swiss Franc (&#67;&#72;&#70;)', 'service-finder' ),
    'TWD' => esc_html__( 'Taiwan New Dollars (&#78;&#84;&#36;)', 'service-finder' ),
    'THB' => esc_html__( 'Thai Baht (&#3647;)', 'service-finder' ),
    'TRY' => esc_html__( 'Turkish Lira (&#8378;)', 'service-finder' ),
    'USD' => esc_html__( 'US Dollars (&#36;)', 'service-finder' ),
    'VND' => esc_html__( 'Vietnamese Dong (&#8363;)', 'service-finder' ),
    'EGP' => esc_html__( 'Egyptian Pound (EGP)', 'service-finder' ),
	'XOF' => esc_html__( 'West African Franc (FCFA)', 'service-finder' ),
	'SAR' => esc_html__( 'Saudi Riyal', 'service-finder' ),
	'KSH' => esc_html__( 'Kenyan Shilling', 'service-finder' ),
	'HNL' => esc_html__( 'Honduran Lempira', 'service-finder' ),
	'TZS' => esc_html__( 'Tanzanian Shilling', 'service-finder' ),
	'XPF' => esc_html__( 'CFP Franc', 'service-finder' ),
	'UGX' => esc_html__( 'Uganda Shillings', 'service-finder' ),
	'TZS' => esc_html__( 'Tanzania Shillings', 'service-finder' ),
	'RWF' => esc_html__( 'Rwandan Franc', 'service-finder' ),
	'BIF' => esc_html__( 'Burundi Franc', 'service-finder' ),
	'colones' => esc_html__( 'Costa Rica Colones', 'service-finder' ),
	'BAM' => esc_html__( 'Bosnia and Herzegovina', 'service-finder' ),
	'KZT' => esc_html__( 'Kazakhstan', 'service-finder' ),
    );
	
	return $currency;
}

/* Country List */
function service_finder_get_country_list(){
	$country = array(
    	'AT' => esc_html__( 'Austria', 'service-finder' ), //Europe Country start
		'BE' => esc_html__( 'Belgium', 'service-finder' ),
		'BG' => esc_html__( 'Bulgaria', 'service-finder' ),
		'HR' => esc_html__( 'Croatia', 'service-finder' ),
		'CY' => esc_html__( 'Cyprus', 'service-finder' ),
		'CZ' => esc_html__( 'Czech Republic', 'service-finder' ),
		'DK' => esc_html__( 'Denmark', 'service-finder' ),
		'EE' => esc_html__( 'Estonia', 'service-finder' ),
		'FI' => esc_html__( 'Finland', 'service-finder' ),
		'FR' => esc_html__( 'France', 'service-finder' ),
		'DE' => esc_html__( 'Germany', 'service-finder' ),
		'GR' => esc_html__( 'Greece', 'service-finder' ),	
		'HU' => esc_html__( 'Hungary', 'service-finder' ),
		'IN' => esc_html__( 'India', 'service-finder' ),
		'IE' => esc_html__( 'Ireland', 'service-finder' ),
		'IT' => esc_html__( 'Italy', 'service-finder' ),
		'LV' => esc_html__( 'Latvia', 'service-finder' ),
		'LT' => esc_html__( 'Lithuania', 'service-finder' ),
		'LU' => esc_html__( 'Luxembourg', 'service-finder' ),
		'MT' => esc_html__( 'Malta', 'service-finder' ),
		'NL' => esc_html__( 'Netherlands', 'service-finder' ),
		'PL' => esc_html__( 'Poland', 'service-finder' ),
		'PT' => esc_html__( 'Portugal', 'service-finder' ),
		'RO' => esc_html__( 'Romania', 'service-finder' ),
		'SK' => esc_html__( 'Slovakia', 'service-finder' ),
		'SI' => esc_html__( 'Slovenia', 'service-finder' ),
		'ES' => esc_html__( 'Spain', 'service-finder' ),
		'SE' => esc_html__( 'Sweden', 'service-finder' ), //Europe Country end
		'GB' => esc_html__( 'Great Britain', 'service-finder' ),
		'AU' => esc_html__( 'Australia', 'service-finder' ),
		'BR' => esc_html__( 'Brazil', 'service-finder' ),
		'CA' => esc_html__( 'Canada', 'service-finder' ),
		'HK' => esc_html__( 'Hong Kong', 'service-finder' ),
		'JP' => esc_html__( 'Japan', 'service-finder' ),
		'MX' => esc_html__( 'Mexico', 'service-finder' ),
		'MY' => esc_html__( 'Malaysia', 'service-finder' ),
		'NZ' => esc_html__( 'New Zealand', 'service-finder' ),
		'SG' => esc_html__( 'Singapore', 'service-finder' ),
		'US' => esc_html__( 'United States', 'service-finder' ),
    );
	
	return $country;
}

/*Get currecy symbol*/
if ( !function_exists( 'service_finder_currencysymbol' ) ){
function service_finder_currencysymbol(){
global $service_finder_options;
$currency = (!empty($service_finder_options['currency-code'])) ? $service_finder_options['currency-code'] : 'USD';

switch ( $currency ) {
		case 'ARS' :
			$currency_symbol = '&#36;';
			break;
		case 'PEN' :
			$currency_symbol = 'Sol';
			break;	
		case 'AED' :
			$currency_symbol = 'AED';
			break;
		case 'BDT':
			$currency_symbol = '&#2547;&nbsp;';
			break;
		case 'BRL' :
			$currency_symbol = '&#82;&#36;';
			break;
		case 'BGN' :
			$currency_symbol = '&#1083;&#1074;.';
			break;
		case 'AUD' :
		case 'CAD' :
		case 'CLP' :
		case 'COP' :
		case 'MXN' :
		case 'NZD' :
		case 'HKD' :
		case 'SGD' :
		case 'USD' :
			$currency_symbol = '&#36;';
			break;
		case 'EUR' :
			$currency_symbol = '&euro;';
			break;
		case 'CNY' :
		case 'RMB' :
		case 'JPY' :
			$currency_symbol = '&yen;';
			break;
		case 'RUB' :
			$currency_symbol = '&#1088;&#1091;&#1073;.';
			break;
		case 'KRW' : $currency_symbol = '&#8361;'; break;
        case 'PYG' : $currency_symbol = '&#8370;'; break;
		case 'TRY' : $currency_symbol = '&#8378;'; break;
		case 'NOK' : $currency_symbol = '&#107;&#114;'; break;
		case 'ZAR' : $currency_symbol = '&#82;'; break;
		case 'CZK' : $currency_symbol = '&#75;&#269;'; break;
		case 'DZD' : $currency_symbol = 'DA'; break;
		case 'MYR' : $currency_symbol = '&#82;&#77;'; break;
		case 'DKK' : $currency_symbol = 'kr.'; break;
		case 'GYD' : $currency_symbol = 'GY$'; break;
		case 'GHS' : $currency_symbol = 'GH&#8373;'; break;
		case 'HUF' : $currency_symbol = '&#70;&#116;'; break;
		case 'IDR' : $currency_symbol = 'Rp'; break;
		case 'INR' : $currency_symbol = 'Rs.'; break;
		case 'PKR' : $currency_symbol = 'Rs.'; break;
		case 'NPR' : $currency_symbol = 'Rs.'; break;
		case 'ISK' : $currency_symbol = 'Kr.'; break;
		case 'ILS' : $currency_symbol = '&#8362;'; break;
		case 'PHP' : $currency_symbol = '&#8369;'; break;
		case 'PLN' : $currency_symbol = '&#122;&#322;'; break;
		case 'SEK' : $currency_symbol = '&#107;&#114;'; break;
		case 'CHF' : $currency_symbol = '&#67;&#72;&#70;'; break;
		case 'TWD' : $currency_symbol = '&#78;&#84;&#36;'; break;
		case 'THB' : $currency_symbol = '&#3647;'; break;
		case 'GBP' : $currency_symbol = '&pound;'; break;
		case 'RON' : $currency_symbol = 'lei'; break;
		case 'VND' : $currency_symbol = '&#8363;'; break;
		case 'NGN' : $currency_symbol = '&#8358;'; break;
		case 'HRK' : $currency_symbol = 'Kn'; break;
		case 'EGP' : $currency_symbol = 'EGP'; break;
		case 'DOP' : $currency_symbol = 'RD&#36;'; break;
		case 'KIP' : $currency_symbol = '&#8365;'; break;
		case 'MAD' : $currency_symbol = '&#x2e;&#x62f;&#x2e;&#x645;'; break;
		case 'XOF' : $currency_symbol = 'FCFA'; break;
		case 'MVR' : $currency_symbol = 'Rf'; break;
		case 'SAR' : $currency_symbol = 'SAR'; break;
		case 'KSH' : $currency_symbol = 'Ksh'; break;
		case 'HNL' : $currency_symbol = 'L'; break;
		case 'TZS' : $currency_symbol = 'TSh'; break;
		case 'XPF' : $currency_symbol = 'F'; break;
		case 'UGX' : $currency_symbol = 'USh'; break;
		case 'RWF' : $currency_symbol = 'FRw'; break;
		case 'BIF' : $currency_symbol = 'BIF'; break;
		case 'colones' : $currency_symbol = 'colones'; break;
		case 'BAM' : $currency_symbol = 'KM'; break;
		case 'NE' : $currency_symbol = 'KM'; break;
		case 'KZT' : $currency_symbol = 'KZT'; break;
		case 'UAH' : $currency_symbol = '&#8372;'; break;
		case 'BZD' : $currency_symbol = 'BZ&#36;'; break;
		case 'BHD' : $currency_symbol = 'BD'; break;
		case 'GHS' : $currency_symbol = 'GH&cent;'; break;
		default    : $currency_symbol = ''; break;
	}


return $currency_symbol;
}
}

/*Display rating*/
function service_finder_displayRating($rating = 0){
global $service_finder_options;
if($rating > 0){
$rating = $rating;
}else{
$rating = 0;
}
	if($service_finder_options['review-system']){
	return '<div class="sf-show-rating default-hidden"><input class="display-ratings" value="'.esc_attr($rating).'" type="number" min=0 max=5 step=0.5 data-size="sm" disabled="disabled"></div>';
	}else{
	return '';
	}
}

/*Get average rating*/
function service_finder_getAverageRating($providerid = 0){
global $wpdb,$service_finder_Tables,$service_finder_options;

	$ratingstyle = (!empty($service_finder_options['rating-style'])) ? $service_finder_options['rating-style'] : '';
	
	if($service_finder_options['review-style'] == 'booking-review'){
		$res = $wpdb->get_row($wpdb->prepare('SELECT rating FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$providerid));
		return $res->rating;
	}elseif($service_finder_options['review-style'] == 'open-review'){
		$comment_postid = get_user_meta($providerid,'comment_post',true);
		$comment_rating = 0;
		$avg_rating = 0;
		
		$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'comments WHERE `comment_approved` = 1 AND `comment_post_ID` = %d',$comment_postid));
		$total_comments = count($results);
		if(!empty($results)){
			foreach($results as $result){
			$comment_id = $result->comment_ID;
				
				if($ratingstyle == 'custom-rating'){
					$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM `'.$service_finder_Tables->custom_rating.'` where `comment_id` = %d',$comment_id));
					if(!empty($row)){
						$comment_rating = $comment_rating + $row->avgrating;
					}
				}else{
					$row = $wpdb->get_row($wpdb->prepare('SELECT `meta_value` FROM '.$wpdb->prefix.'commentmeta WHERE `comment_id` = %d AND `meta_key` = "pixrating"',$comment_id));
					if(!empty($row)){
						$comment_rating = $comment_rating + $row->meta_value;
					}
				}
			}
			$avg_rating = $comment_rating/$total_comments;
		}
		
		return $avg_rating;
	}

}

/*Get average rating*/
function service_finder_number_of_stars($providerid = 0){
global $wpdb,$service_finder_Tables,$service_finder_options;
	$onestar = 0;
	$twostar = 0;
	$threestar = 0;
	$fourstar = 0;
	$fivestar = 0;
	if($service_finder_options['review-style'] == 'booking-review'){
		$allreviews = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->feedback.' where provider_id = %d',$providerid));
		if(!empty($allreviews)){
			foreach($allreviews as $rev){
				if(floatval($rev->rating) > 0 && floatval($rev->rating) < 1.5){
					$onestar = $onestar + 1;
				}elseif(floatval($rev->rating) >= 1.5 && floatval($rev->rating) < 2.5){
					$twostar = $twostar + 1;
				}elseif(floatval($rev->rating) >= 2.5 && floatval($rev->rating) < 3.5){
					$threestar = $threestar + 1;
				}elseif(floatval($rev->rating) >= 3.5 && floatval($rev->rating) < 4.5){
					$fourstar = $fourstar + 1;
				}elseif(floatval($rev->rating) >= 4.5 && floatval($rev->rating) <= 5){
					$fivestar = $fivestar + 1;
				}
			}
		}
	}elseif($service_finder_options['review-style'] == 'open-review'){
		$comment_postid = get_user_meta($providerid,'comment_post',true);
		$ratingstyle = (!empty($service_finder_options['rating-style'])) ? $service_finder_options['rating-style'] : '';
		$comment_rating = 0;
		$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'comments WHERE `comment_approved` = 1 AND `comment_post_ID` = %d',$comment_postid));
		$total_comments = count($results);
		if(!empty($results)){
			foreach($results as $result){
			$comment_id = $result->comment_ID;
				
				if($ratingstyle == 'custom-rating'){
				$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM `'.$service_finder_Tables->custom_rating.'` where `comment_id` = %d',$comment_id));
				if(!empty($row)){
					if(floatval($row->avgrating) > 0 && floatval($row->avgrating) < 1.5){
						$onestar = $onestar + 1;
					}elseif(floatval($row->avgrating) >= 1.5 && floatval($row->avgrating) < 2.5){
						$twostar = $twostar + 1;
					}elseif(floatval($row->avgrating) >= 2.5 && floatval($row->avgrating) < 3.5){
						$threestar = $threestar + 1;
					}elseif(floatval($row->avgrating) >= 3.5 && floatval($row->avgrating) < 4.5){
						$fourstar = $fourstar + 1;
					}elseif(floatval($row->avgrating) >= 4.5 && floatval($row->avgrating) <= 5){
						$fivestar = $fivestar + 1;
					}
				}
				}else{
				$row = $wpdb->get_row($wpdb->prepare('SELECT `meta_value` FROM '.$wpdb->prefix.'commentmeta WHERE `comment_id` = %d AND `meta_key` = "pixrating"',$comment_id));
				if(!empty($row)){
					if(floatval($row->meta_value) > 0 && floatval($row->meta_value) < 1.5){
						$onestar = $onestar + 1;
					}elseif(floatval($row->meta_value) >= 1.5 && floatval($row->meta_value) < 2.5){
						$twostar = $twostar + 1;
					}elseif(floatval($row->meta_value) >= 2.5 && floatval($row->meta_value) < 3.5){
						$threestar = $threestar + 1;
					}elseif(floatval($row->meta_value) >= 3.5 && floatval($row->meta_value) < 4.5){
						$fourstar = $fourstar + 1;
					}elseif(floatval($row->meta_value) >= 4.5 && floatval($row->meta_value) <= 5){
						$fivestar = $fivestar + 1;
					}
				}
				}
			}
		}
	}
	
	return array(
			'1' => $onestar,
			'2' => $twostar,
			'3' => $threestar,
			'4' => $fourstar,
			'5' => $fivestar,
		);

}

/*Get member average rating*/
function service_finder_getMemberAverageRating($memberid = 0){
global $wpdb,$service_finder_Tables;

$res = $wpdb->get_row($wpdb->prepare('SELECT rating FROM '.$service_finder_Tables->team_members.' WHERE `id` = %d',$memberid));

return $res->rating;
}

/*Get provider name by id*/
function service_finder_getProviderName($providerid = 0){
global $wpdb,$service_finder_Tables;

$sedateProvider = $wpdb->get_row($wpdb->prepare('SELECT company_name,full_name FROM '.$service_finder_Tables->providers.' where wp_user_id = %d',$providerid));

if(!empty($sedateProvider)){
if($sedateProvider->company_name != ""){
$providername = $sedateProvider->company_name;
}else{
$providername = $sedateProvider->full_name;
}
return $providername;
}else{
return '';
}
}

/*Get booking cutomer name by booking cutomer id*/
function service_finder_getBookingCustomerName($bookingcustomerid = 0){
global $wpdb,$service_finder_Tables;

$row = $wpdb->get_row($wpdb->prepare('SELECT name FROM '.$service_finder_Tables->customers.' where id = %d',$bookingcustomerid));

if(!empty($row)){
if(!empty($row->name)){
return $row->name;
}else{
return $row->email;
}
}else{
return '';
}
}

/*Get user name by id*/
function service_finder_get_user_name($userid = 0){

$fullname = get_user_meta($providerid,'first_name',true).' '.get_user_meta($providerid,'last_name',true);

return $fullname;
}

/*Get provider name by id*/
function service_finder_getProviderFullName($providerid = 0){

$fullname = get_user_meta($providerid,'first_name',true).' '.get_user_meta($providerid,'last_name',true);

return $fullname;
}

/*Get customer name by id*/
function service_finder_getCustomerName($customerid = 0){

$fullname = get_user_meta($customerid,'first_name',true).' '.get_user_meta($customerid,'last_name',true);

return $fullname;

}

/*Get customer email by id*/
function service_finder_getCustomerEmail($userId = 0){

$userinfo = get_user_by( 'ID', $userId );

return $userinfo->user_email;

}

/*Get provider name by id*/
function service_finder_getCompanyName($providerid = 0){
global $wpdb,$service_finder_Tables;

$sedateProvider = $wpdb->get_row($wpdb->prepare('SELECT company_name FROM '.$service_finder_Tables->providers.' where wp_user_id = %d',$providerid));
if(!empty($sedateProvider)){
return $sedateProvider->company_name;
}

}

/*Get provider category by user id*/
function service_finder_getProviderCategory($providerid = 0){
global $wpdb,$service_finder_Tables;

if($providerid > 0){
$res = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' where wp_user_id = %d',$providerid));
return $res->category_id;
}else{
return '';
}

}

/*Get Total Number of Providers in Particular Category*/
function service_finder_getTotalProvidersByCategory($catid = 0){
global $wpdb, $service_finder_Tables, $service_finder_options;
$identitycheck = (isset($service_finder_options['identity-check'])) ? esc_attr($service_finder_options['identity-check']) : '';
$restrictuserarea = (isset($service_finder_options['restrict-user-area'])) ? esc_attr($service_finder_options['restrict-user-area']) : '';
if($restrictuserarea && $identitycheck){
$sql = 'SELECT count(*) as total FROM '.$service_finder_Tables->providers.' where admin_moderation = "approved" AND identity = "approved" AND account_blocked != "yes" AND';
}else{
$sql = 'SELECT count(*) as total FROM '.$service_finder_Tables->providers.' where admin_moderation = "approved" AND account_blocked != "yes" AND';
}



$texonomy = 'providers-category';
$term_children = get_term_children($catid,$texonomy);

if(!empty($term_children)){
$sql .= ' (';
	foreach($term_children as $term_child_id) {
		
		$sql .= ' FIND_IN_SET("'.$term_child_id.'", category_id) OR ';
		
	}
$sql .= ' FIND_IN_SET("'.$catid.'", category_id) ';	
$sql .= ' )';	
	
}else{

$sql .= ' FIND_IN_SET("'.$catid.'", category_id)';

}

$res = $wpdb->get_row($sql);

return $res->total;

}

function service_finder_check_business_hours_status($pid = 0){
global $service_finder_options;
$business_hours_active_inactive = get_user_meta($pid,'business_hours_active_inactive',true); 
if(($business_hours_active_inactive == 'active' || $business_hours_active_inactive == '') && $service_finder_options['business-hours-menu']){
return true;
}else{
return false;
}
}

function service_finder_showBusinessHours($pid = 0){
global $wpdb,$service_finder_Tables,$service_finder_options;
$currUser = wp_get_current_user();
$time_format = (!empty($service_finder_options['time-format'])) ? $service_finder_options['time-format'] : '';
$days = array('monday','tuesday','wednesday','thursday','friday','saturday','sunday');
$shortdays = array('MON','TUE','WED','THU','FRI','SAT','SUN');
$timeslots = get_user_meta($pid,'timeslots',true);
$breaktimes = get_user_meta($pid,'breaktime',true);
$flag = 0;
if(!empty($timeslots)){
$flag = 1;
}
$html = '';
$html .= '<table class="sf-business-hours table table-bordered">
    <thead>
        <tr>';
            foreach($shortdays as $day){
			
			switch($day){
			case 'MON':
				$dayname = esc_html__('Mon','service-finder');
				break;
			case 'TUE':
				$dayname = esc_html__('Tue','service-finder');
				break;
			case 'WED':
				$dayname = esc_html__('Wed','service-finder');
				break;
			case 'THU':
				$dayname = esc_html__('Thu','service-finder');
				break;
			case 'FRI':
				$dayname = esc_html__('Fri','service-finder');
				break;
			case 'SAT':
				$dayname = esc_html__('Sat','service-finder');
				break;
			case 'SUN':
				$dayname = esc_html__('Sun','service-finder');
				break;						
			}
			
			$html .= '<th>'.strtoupper($dayname).'</th>';
			}
$html .= '</tr>
    </thead>
    <tbody>
        <tr>';
			$i = 0;
            foreach($days as $day){
				$timeslot = (!empty($timeslots)) ? $timeslots[$i] : '';	
				$item = explode('-',$timeslot);
				
				if($item[0] != ""){
				if($timeslot == 'off'){
					$html .= '<td class="sf-closed-day">'.esc_html__('Closed','service-finder').'</td>';
				}else{
					
					if($time_format){
						$starttime = date('H:i',strtotime(esc_html($item[0])));
						$endtime = date('H:i',strtotime(esc_html($item[1])));
					}else{
						$starttime = date('h:i a',strtotime(esc_html($item[0])));
						$endtime = date('h:i a',strtotime(esc_html($item[1])));
					}
					
					$breakhtml = '';
					
					if(!empty($breaktimes[$i])){
					$breaktime = $breaktimes[$i];	
					
					if(!empty($breaktime)){
						foreach($breaktime as $bktime){
							$bkitem = explode('-',$bktime);	
							
							if($time_format){
								$bhstarttime = date('H:i',strtotime(esc_html($bkitem[0])));
								$bhendtime = date('H:i',strtotime(esc_html($bkitem[1])));
							}else{
								$bhstarttime = date('h:i a',strtotime(esc_html($bkitem[0])));
								$bhendtime = date('h:i a',strtotime(esc_html($bkitem[1])));
							}
							
							$breakhtml .= '<li>'.$bhstarttime.' <b>'.esc_html__('to','service-finder').'</b> '.$bhendtime.'</li>';
						}
					}else{
						$breakhtml .= '<li>-</li>';
					}
					}
					
					
					
					
					$html .= '<td class="other-day">
							<span class="from">'.$starttime.'</span>
							<span class="sf-to">'.esc_html__('to','service-finder').'</span>
							<span class="to">'.$endtime.'</span>
							<div class="sf-break-timing">
								<strong><i class="fa fa-coffee"></i> '.esc_html__('Break Time','service-finder').'</strong>
								<ul>
									'.$breakhtml.'
								</ul>
							</div>
						</td>';
				}
				}
				
				$i++;
			}
$html .= '</tr>
    </tbody>
</table>';

if($flag == 1){
return $html; 	
}else{
return false;
}
}

/*Get Stripe Public Key via AJax for Provider*/
add_action('wp_ajax_get_stripekey', 'service_finder_get_stripekey');
add_action('wp_ajax_nopriv_get_stripekey', 'service_finder_get_stripekey');

function service_finder_get_stripekey(){
global $service_finder_options;

$settings = service_finder_getProviderSettings($_POST['provider_id']);

$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? esc_html($service_finder_options['pay_booking_amount_to']) : '';
if($pay_booking_amount_to == 'admin'){
	$stripetype = (!empty($service_finder_options['stripe-type'])) ? esc_html($service_finder_options['stripe-type']) : '';
	if($stripetype == 'live'){
		$stripepublickey = (!empty($service_finder_options['stripe-live-public-key'])) ? esc_html($service_finder_options['stripe-live-public-key']) : '';
	}else{
		$stripepublickey = (!empty($service_finder_options['stripe-test-public-key'])) ? esc_html($service_finder_options['stripe-test-public-key']) : '';
	}
}elseif($pay_booking_amount_to == 'provider'){
	$stripepublickey = esc_html($settings['stripepublickey']);
}

echo esc_html($stripepublickey);
exit;
}

/*Get Stripe Public Key via AJax for Admin*/
add_action('wp_ajax_get_adminstripekey', 'service_finder_get_adminstripekey');
add_action('wp_ajax_nopriv_get_adminstripekey', 'service_finder_get_adminstripekey');

function service_finder_get_adminstripekey(){
global $service_finder_options;
if( isset($service_finder_options['stripe-type']) && $service_finder_options['stripe-type'] == 'test' ){
	$secret_key = (!empty($service_finder_options['stripe-test-secret-key'])) ? $service_finder_options['stripe-test-secret-key'] : '';
	$public_key = (!empty($service_finder_options['stripe-test-public-key'])) ? $service_finder_options['stripe-test-public-key'] : '';
}else{
	$secret_key = (!empty($service_finder_options['stripe-live-secret-key'])) ? $service_finder_options['stripe-live-secret-key'] : '';
	$public_key = (!empty($service_finder_options['stripe-live-public-key'])) ? $service_finder_options['stripe-live-public-key'] : '';
}


$success = array(
			'status' => 'success',
			'secret_key' => $secret_key,
			'public_key' => $public_key,
			);
echo json_encode($success);	

exit;
}


function service_finder_getExcerpts($str = '',$start = '',$end = ''){
if(strlen($str) > $end) {
	$s = substr(strip_tags(wpautop($str)), $start, $end);
	$result = substr($s, 0, strrpos($s, ' '));
	if($result != ""){
	return stripcslashes(strip_shortcodes($result)).' [...]';
	}else{
	return stripcslashes(strip_shortcodes($result));
	}
}else{
	return stripcslashes(strip_shortcodes($str));
}	
}

function service_finder_getHours($val = ''){
global $service_finder_options;
for($i = 0; $i < 24; $i++):

$tem = ''; 
if($val != ""){
$tem = explode(':',$val);
}
$time_format = (!empty($service_finder_options['time-format'])) ? $service_finder_options['time-format'] : '';
if($time_format){ 
if(!empty($tem)){
?>st
	<option <?php echo ($tem[0] == $i && $tem[1] == 00) ? 'selected="selected"' : ''; ?> value="<?php echo esc_attr($i); ?>:00"><?php echo esc_attr($i); ?>:00</option>
    <option <?php echo ($tem[0] == $i && $tem[1] == 30) ? 'selected="selected"' : ''; ?> value="<?php echo esc_attr($i); ?>:30"><?php echo esc_attr($i); ?>:30</option>
    
<?php    
}else{
?>
	<option value="<?php echo esc_attr($i); ?>:00"><?php echo esc_attr($i); ?>:00</option>
    <option value="<?php echo esc_attr($i); ?>:30"><?php echo esc_attr($i); ?>:30</option>
<?php
}
}else{ 
if(!empty($tem)){
?>
<option <?php echo ($tem[0] == $i && $tem[1] == 00) ? 'selected="selected"' : ''; ?> value="<?php echo esc_attr($i); ?>:00"><?php echo ($i % 12) ? esc_attr($i) % 12 : 12 ?>:00 <?php echo ($i >= 12) ? 'PM' : 'AM' ?></option>
<option <?php echo ($tem[0] == $i && $tem[1] == 30) ? 'selected="selected"' : ''; ?> value="<?php echo esc_attr($i); ?>:30"><?php echo ($i % 12) ? esc_attr($i) % 12 : 12 ?>:30 <?php echo ($i >= 12) ? 'PM' : 'AM' ?></option>
<?php
}else{
?>
<option value="<?php echo esc_attr($i); ?>:00"><?php echo ($i % 12) ? esc_attr($i) % 12 : 12 ?>:00 <?php echo ($i >= 12) ? 'PM' : 'AM' ?></option>
<option value="<?php echo esc_attr($i); ?>:30"><?php echo ($i % 12) ? esc_attr($i) % 12 : 12 ?>:30 <?php echo ($i >= 12) ? 'PM' : 'AM' ?></option>
<?php
}
}
endfor;
}

function service_finder_getAddress($userid = 0){
global $wpdb,$service_finder_Tables;

$res = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE wp_user_id = %d',$userid));
if(!empty($res)){

	if($res->address != '')
	{
	$shortaddress[] = $res->address;
	}
	if($res->city != '')
	{
	$cityname = service_finder_get_cityname_by_slug($res->city);
	$shortaddress[] = ucfirst($cityname);
	}
	if($res->state != '')
	{
	$shortaddress[] = ucfirst($res->state);
	}
	if($res->country != '')
	{
	$shortaddress[] = ucfirst($res->country);
	}
	
	if(!empty($shortaddress))
	{
		return implode(', ',$shortaddress);
	}else{
		return '';
	}
}else{
	return '';
}

}

function service_finder_getBranchAddress($branchid = 0){

	global $wpdb,$service_finder_Tables;
	$res = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->branches.' WHERE id = %d',$branchid));
	
	if(!empty($res)){
	
	$shortaddress = array();
		
	
	if($res->address != '')
	{
	$shortaddress[] = ucfirst($res->address);
	}
	if($res->city != '')
	{
	$cityname = service_finder_get_cityname_by_slug($res->city);
	$shortaddress[] = ucfirst($cityname);
	}
	if($res->state != '')
	{
	$shortaddress[] = ucfirst($res->state);
	}
	if($res->country != '')
	{
	$shortaddress[] = ucfirst($res->country);
	}
	
	if(!empty($shortaddress))
	{
		return implode(', ',$shortaddress);
	}else{
		return '';
	}
	}else{
	return '';
	}

}

/*Get provider short address*/
function service_finder_getshortAddress($userid = 0){
	global $wpdb,$service_finder_Tables;
	$res = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE wp_user_id = %d',$userid));
	if(!empty($res)){
	
	$shortaddress = array();
		
	if($res->city != '')
	{
	$cityname = service_finder_get_cityname_by_slug($res->city);
	$shortaddress[] = ucfirst($cityname);
	}
	if($res->country != '')
	{
	$shortaddress[] = ucfirst($res->country);
	}
	
	if(!empty($shortaddress))
	{
		return implode(', ',$shortaddress);
	}else{
		return '';
	}
	}else{
	return '';
	}
}

/*Get avatar id by user id*/
function service_finder_getAvatarID($userid = 0){
global $wpdb,$service_finder_Tables;
$res = $wpdb->get_row($wpdb->prepare('SELECT avatar_id FROM '.$service_finder_Tables->providers.' WHERE wp_user_id = %d',$userid));
if(!empty($res)){
return $res->avatar_id;
}else{
return 0;
}
}

/*Get provider email*/
function service_finder_getProviderEmail($userid = 0){
global $wpdb,$service_finder_Tables;
$res = $wpdb->get_row($wpdb->prepare('SELECT email FROM '.$service_finder_Tables->providers.' WHERE wp_user_id = %d',$userid));
if(!empty($res)){
return $res->email;
}else{
return '';
}
}

/*Check if provider is featured*/
function service_finder_is_featured($pid = 0){
global $wpdb,$service_finder_Tables;
$res = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->feature.' WHERE (`feature_status` = "active" AND (`status` = "Paid" OR `status` = "Free")) AND `provider_id` = %d',$pid));
if(!empty($res)){
return true;
}else{
return false;
}
}

/*Get total number of featured provider*/
function service_finder_total_featured_providers(){
global $wpdb,$service_finder_Tables;
$row = $wpdb->get_row('SELECT count(id) as total FROM '.$service_finder_Tables->providers.' WHERE admin_moderation = "approved" AND account_blocked != "yes" AND `featured` = 1');
return $row->total;
}

/*Get total number of paid providers*/
function service_finder_total_paid_providers(){
global $wpdb,$service_finder_Tables;
$row = $wpdb->get_row('SELECT count(user_id) as total FROM `'.$wpdb->prefix.'usermeta` WHERE `meta_key` = "provider_role" AND `meta_value` IN ("package_1","package_2","package_3")');
return $row->total;
}


/*Check provider is blocked or not*/
function service_finder_is_blocked($userid = 0){
global $wpdb,$service_finder_Tables;
$res = $wpdb->get_row($wpdb->prepare('SELECT account_blocked FROM '.$service_finder_Tables->providers.' WHERE wp_user_id = %d',$userid));
if(!empty($res)){
return $res->account_blocked;
}else{
return '';
}
}

/*Ajax Pagination for load search results*/	
add_action( 'wp_ajax_load-search-result', 'service_finder_load_search_result' );
add_action( 'wp_ajax_nopriv_load-search-result', 'service_finder_load_search_result' );

function service_finder_load_search_result() {
   
   global $service_finder_ThemeParams, $wpdb, $service_finder_options, $service_finder_Tables;

    if($_POST['page'] != ""){
	$page = sanitize_text_field($_POST['page']);
	}else{
	$page = 1;
	}
	$cur_page = $page;
	$page -= 1;
	if($_POST['numberofpages'] != ""){
	$per_page = $_POST['numberofpages'];
	}else{
	$srhperpage = (!empty($service_finder_options['srh-per-page'])) ? $service_finder_options['srh-per-page'] : '';
	$per_page = ($srhperpage > 0) ? $service_finder_options['srh-per-page'] : 12;
	}
	
	if($_POST['setorderby'] != ""){
	$orderby = $_POST['setorderby'];
	}else{
	$orderby = 'id';
	}
	
	if($_POST['setorder'] != ""){
	$order = $_POST['setorder'];
	}else{
	$order = 'desc';
	}
	
	$previous_btn = true;
	$next_btn = true;
	$first_btn = true;
	$last_btn = true;
	$start = $page * $per_page;
	
	$keyword = (isset($_POST['keyword'])) ? $_POST['keyword'] : '';
	$address = (isset($_POST['address'])) ? $_POST['address'] : '';
	$city = (isset($_POST['city'])) ? $_POST['city'] : '';
	$catid = (isset($_POST['catid'])) ? $_POST['catid'] : '';
	$country = (isset($_POST['country'])) ? $_POST['country'] : '';
	$minprice = (isset($_POST['minprice'])) ? esc_html($_POST['minprice']) : '';
	$maxprice = (isset($_POST['maxprice'])) ? esc_html($_POST['maxprice']) : '';
	$distance = (isset($_POST['distance'])) ? $_POST['distance'] : '';
	
	$srhbybooking = (isset($_POST['srhbybooking'])) ? esc_html($_POST['srhbybooking']) : '';
	
	$srhdate = (isset($_POST['srhdate'])) ? esc_html($_POST['srhdate']) : '';
	$srhperiod = (isset($_POST['srhperiod'])) ? esc_html($_POST['srhperiod']) : '';
	$srhtime = (isset($_POST['srhtime'])) ? esc_html($_POST['srhtime']) : '';
	
	$state = (isset($_POST['state'])) ? $_POST['state'] : '';
	$zipcode = (isset($_POST['zipcode'])) ? $_POST['zipcode'] : '';
	$srhgender = service_finder_get_data($_POST,'srhgender');
	
	$searchdata = array(
		'srhbybooking' => $srhbybooking,
		'srhdate' => $srhdate,
		'srhperiod' => $srhperiod,
		'srhtime' => $srhtime,
		'state' => $state,
		'zipcode' => $zipcode,
		'srhgender' => $srhgender,
	);
   
   $getProviders = new SERVICE_FINDER_searchProviders();
	
   $providersInfoArr = $getProviders->service_finder_getSearchedProviders($searchdata,$distance,$minprice,$maxprice,esc_attr($keyword),esc_attr($address),esc_attr($city),esc_attr($catid),esc_attr($country),$start,$per_page,$orderby,$order);
   
   $providersavailability = array();
   
   $providersInfo = $providersInfoArr['srhResult'];
   $count = $providersInfoArr['count'];
   if(!empty($providersInfoArr['sortresult'])){
   $providersavailability = $providersInfoArr['sortresult'];
   }
   $msg = '';
	
	$markers = '';
	$flag = 0;
	if(!empty($providersInfo)){ 
		if($service_finder_options['search-template'] == 'style-1' || !service_finder_show_map_on_site()){
			if($_POST['viewtype'] == 'listview'){
			$msg .= '<div class="listing-box row equal-col-outer">';
			}elseif($_POST['viewtype'] == 'grid-4'){
			$msg .= '<div class="listing-grid-box sf-listing-grid-4 equal-col-outer">
							<div class="row">';
			}elseif($_POST['viewtype'] == 'grid-3'){
			$msg .= '<div class="listing-grid-box sf-listing-grid-3 equal-col-outer">
							<div class="row">';
			}else{
			$msg .= '<div class="listing-grid-box sf-listing-grid-4 equal-col-outer">
							<div class="row">';
			}
		}elseif($service_finder_options['search-template'] == 'style-2'){
			if($_POST['viewtype'] == 'listview'){
			$msg .= '<div class="listing-box row">';
			}else{
			$msg .= '<div class="listing-grid-box sf-listing-grid-2 equal-col-outer">
							<div class="row">';
			}
		}
	foreach($providersInfo as $provider){

	$userLink = service_finder_get_author_url($provider->wp_user_id);
	
	$services = '';
	$searchedservices = '';
	if($keyword != "" || ($minprice != "" && $maxprice != "" && $maxprice > 0)){
	$services = service_finder_get_searched_services($provider->wp_user_id,$keyword,$minprice,$maxprice);

    if(!empty($services)){
		$searchedservices .= '<ul class="sf-service-price-list">';
		foreach($services as $service){
			$searchedservices .= '<li><span>'.service_finder_money_format(esc_html($service->cost)).'</span> '.esc_html($service->service_name).'</li>';
		}
		$searchedservices .= '</ul>';
	}
	
	}



	if(!empty($provider->avatar_id) && $provider->avatar_id > 0){
		$src  = wp_get_attachment_image_src( $provider->avatar_id, 'service_finder-provider-thumb' );
		$src  = $src[0];
	}else{
		$src  = service_finder_get_default_avatar();
	}
	
	$procatid = get_user_meta($provider->wp_user_id,'primary_category',true);
	
	$icon = service_finder_getCategoryIcon($procatid);
	
	if($icon == ""){
	$imagepath = SERVICE_FINDER_BOOKING_IMAGE_URL.'/markers';
	$icon = (!empty($service_finder_options['default-map-marker-icon']['url'])) ? $service_finder_options['default-map-marker-icon']['url'] : '';
	}
	
	$markeraddress = service_finder_getAddress($provider->wp_user_id);
	
	$companyname = service_finder_getCompanyName($provider->wp_user_id);
	$companyname = str_replace(array("\n", "\r", '"', "'"), ' ', $companyname);
	$companyname = preg_replace('/\t+/', '', $companyname);
	
	$full_name = str_replace(array("\n", "\r", '"', "'"), ' ', $provider->full_name);
	$full_name = preg_replace('/\t+/', '', $full_name);
	
	$markeraddress = str_replace(array("\n", "\r", '"', "'"), ' ', $markeraddress);
	$markeraddress = str_replace('\t', '', $markeraddress);
	
	$categorycolor = service_finder_getCategoryColor(get_user_meta($provider->wp_user_id,'primary_category',true));
	
	$catname = service_finder_getCategoryName(get_user_meta($provider->wp_user_id,'primary_category',true));
	
	$catname = str_replace(array("\n", "\r", '"', "'"), ' ', $catname);

	$catname = str_replace('\t', '', $catname);
	
	$center_latitude = '';
	$center_longitude = '';
	
	if($provider->lat != '' && $provider->long != '' && $center_latitude == '' && $center_longitude == ''){
		$center_latitude = $provider->lat;
		$center_longitude = $provider->long;
	}
	
	//Create the markers	
	$markers .= '["'.stripcslashes($full_name).'","'.$provider->lat.'","'.$provider->long.'","'.$src.'","'.$icon.'","'.$userLink.'","'.$provider->wp_user_id.'","'.$catname.'","'.stripcslashes($markeraddress).'","'.stripcslashes($companyname).'","'.$categorycolor.'"],';
	
	if($city != "" && $country != ""){
		$branches = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->branches.' WHERE wp_user_id = %d AND `city` = "%s" AND `country` = "%s"',$provider->wp_user_id,$city,$country));		
	}elseif($city == "" && $country != ""){
		$branches = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->branches.' WHERE wp_user_id = %d AND `country` = "%s"',$provider->wp_user_id,$country));
	}elseif($city != "" && $country == ""){
		$branches = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->branches.' WHERE wp_user_id = %d AND `city` = "%s"',$provider->wp_user_id,$city));		
	}else{
		$branches = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->branches.' WHERE wp_user_id = %d',$provider->wp_user_id));
	}
	
	if(!empty($branches)){
		foreach($branches as $branch){
			$branchaddress = service_finder_getBranchAddress($branch->id);
			
			$markers .= '["'.stripcslashes($full_name).'","'.$branch->lat.'","'.$branch->long.'","'.$src.'","'.$icon.'","'.$userLink.'","'.$provider->wp_user_id.'","'.$catname.'","'.stripcslashes($branchaddress).'","'.stripcslashes($companyname).'","'.$categorycolor.'"],';
		}
	}
	
	$link = $userLink;
    $current_user = wp_get_current_user();         
	$addtofavorite = '';
	if($service_finder_options['add-to-fav']){
	if(is_user_logged_in()){
		$myfav = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->favorites.' where user_id = %d AND provider_id = %d',$current_user->ID,$provider->wp_user_id));
		
		if(!empty($myfav)){
		if(service_finder_themestyle() == 'style-2'){
		$addtofavorite = '<a href="javascript:;" class="remove-favorite sf-featured-item" data-proid="'.esc_attr($provider->wp_user_id).'" data-userid="'.esc_attr($current_user->ID).'"><i class="fa fa-heart"></i></a>';
		}else{
		$addtofavorite = '<a href="javascript:;" class="remove-favorite btn btn-primary" data-proid="'.esc_attr($provider->wp_user_id).'" data-userid="'.esc_attr($current_user->ID).'">'.esc_html__( 'My Favorite', 'service-finder' ).'<i class="fa fa-heart"></i></a>';
		}
		}else{
		if(service_finder_themestyle() == 'style-2'){
		$addtofavorite = '<a href="javascript:;" class="add-favorite sf-featured-item" data-proid="'.esc_attr($provider->wp_user_id).'" data-userid="'.esc_attr($current_user->ID).'"><i class="fa fa-heart-o"></i></a>';
		}else{
		$addtofavorite = '<a href="javascript:;" class="add-favorite btn btn-primary" data-proid="'.esc_attr($provider->wp_user_id).'" data-userid="'.esc_attr($current_user->ID).'">'.esc_html__( 'Add to Fav', 'service-finder' ).'<i class="fa fa-heart"></i></a>';
		}
		}
	}else{
		if(service_finder_themestyle() == 'style-2'){
		$addtofavorite = '<a class="sf-featured-item" href="javascript:;" data-action="login" data-redirect="no" data-toggle="modal" data-target="#login-Modal"><i class="fa fa-heart-o"></i></a>';
		}else{
		$addtofavorite = '<a class="btn btn-primary" href="javascript:;" data-action="login" data-redirect="no" data-toggle="modal" data-target="#login-Modal">'.esc_html__( 'Add to Fav', 'service-finder' ).'<i class="fa fa-heart"></i></a>';
		}
	}  
	}

	if(service_finder_is_featured($provider->wp_user_id)){
	if(service_finder_themestyle() == 'style-2'){
	$featured = '<div  class="sf-featured-sign">'.esc_html__( 'Featured', 'service-finder' ).'</div>';
	}else{
	$featured = '<strong class="sf-featured-label"><span>'.esc_html__( 'Featured', 'service-finder' ).'</span></strong>';
	}
	}else{
	$featured = '';
	}
	
	if(service_finder_themestyle() == 'style-3')
	{
		$msg .= service_finder_display_provider_boxes($provider->wp_user_id,$_POST['viewtype'],true,$providersavailability,$provider->distance,$_POST);
	}elseif(service_finder_themestyle() == 'style-4'){
		$msg .= service_finder_provider_box_fourth($provider->wp_user_id,$_POST['viewtype'],true,$providersavailability,$provider->distance,$_POST);
	}else{
	if($service_finder_options['search-template'] == 'style-1' || !service_finder_show_map_on_site()){
	$addressbox = '';
	$showaddressinfo = (isset($service_finder_options['show-address-info'])) ? esc_attr($service_finder_options['show-address-info']) : '';
	  if($showaddressinfo && $service_finder_options['show-postal-address'] && service_finder_check_address_info_access()){
			if(service_finder_themestyle() == 'style-2'){
			$addressbox = service_finder_getshortAddress($provider->wp_user_id);			
			}else{
			$addressbox = '<div class="overlay-text">
									<div class="sf-address-bx">
										<i class="fa fa-map-marker"></i>
										'.service_finder_getshortAddress($provider->wp_user_id).'
									</div>
								</div>';
			}					
		}
			/*Start Search Style 1*/
			if($_POST['viewtype'] == 'grid-4'){
			/*4 grid layout*/
			if(service_finder_themestyle() == 'style-2'){
			$msg .= '<div class="col-md-3 col-sm-6 equal-col">
			<div class="sf-search-result-girds" id="proid-'.$provider->wp_user_id.'">
                            
                                <div class="sf-featured-top">
                                    <div class="sf-featured-media" style="background-image:url('.esc_url($src).')"></div>
                                    <div class="sf-overlay-box"></div>
                                    '.service_finder_display_category_label($provider->wp_user_id).'
									'.service_finder_availability_label($provider->wp_user_id,$providersavailability).'
                                    '.service_finder_check_varified_icon($provider->wp_user_id).'
									'.$addtofavorite.'
                                    
                                    <div class="sf-featured-info">
                                        '.$featured.'
                                        <div  class="sf-featured-provider">'.service_finder_getExcerpts($provider->full_name,0,35).'</div>
                                        <div  class="sf-featured-address"><i class="fa fa-map-marker"></i> '.$addressbox.' </div>
                                        '.service_finder_displayRating(service_finder_getAverageRating($provider->wp_user_id)).'
                                    </div>
									<a href="'.esc_url($link).'" class="sf-profile-link"></a>
                                </div>
                                
                                <div class="sf-featured-bot">
                                    <div class="sf-featured-comapny">'.service_finder_getExcerpts(service_finder_getCompanyName($provider->wp_user_id),0,20).'</div>
                                    <div class="sf-featured-text">'.service_finder_getExcerpts(nl2br(stripcslashes($provider->bio)),0,75).'</div>
                                    '.service_finder_show_provider_meta($provider->wp_user_id,$provider->phone,$provider->mobile).'
                                </div>
                                
                            </div>
							 </div>';
			}else{
			$msg .= '<div class="col-md-3 col-sm-6 equal-col">

                <div class="sf-provider-bx item">
                    <div class="sf-element-bx">
                    
                        <div class="sf-thum-bx sf-listing-thum img-effect2" style="background-image:url('.esc_url($src).');"> <a href="'.esc_url($link).'" class="sf-listing-link"></a>
                            
                           <div class="overlay-bx">
								'.$addressbox.'
						   </div>
                            
                           '.service_finder_get_primary_category_tag($provider->wp_user_id).'
						   '.service_finder_availability_label($provider->wp_user_id,$providersavailability).'
						   '.$featured.'
                            
                        </div>
                        
                        <div class="padding-20 bg-white '.service_finder_check_varified($provider->wp_user_id).'">
                            <h4 class="sf-title">'.service_finder_getExcerpts(service_finder_getCompanyName($provider->wp_user_id),0,20).'</h4>
                            <strong class="sf-company-name"><a href="'.esc_url($link).'">'.service_finder_getExcerpts($provider->full_name,0,35).'</a></strong>
							'.service_finder_show_provider_meta($provider->wp_user_id,$provider->phone,$provider->mobile).'
							'.service_finder_displayRating(service_finder_getAverageRating($provider->wp_user_id)).'
							'.service_finder_check_varified_icon($provider->wp_user_id).'
                        </div>
                        
                        <div class="btn-group-justified" id="proid-'.$provider->wp_user_id.'">
                          <a href="'.esc_url($link).'" class="btn btn-custom">'.esc_html__('Full View','service-finder').' <i class="fa fa-arrow-circle-o-right"></i></a>
                          '.$addtofavorite.'
                        </div>
                        
                    </div>
                </div>

            </div>';	
			}
            
			}elseif($_POST['viewtype'] == 'listview'){
			/*listview layout*/
			if(service_finder_themestyle() == 'style-2'){
			$msg .= '<div class="sf-featured-listing clearfix">
                            
                            <div class="sf-featured-left" id="proid-'.$provider->wp_user_id.'">
                                <div class="sf-featured-media" style="background-image:url('.esc_url($src).')"></div>
								<a href="'.esc_url($link).'" class="sf-listing-link"></a>
                                <div class="sf-overlay-box"></div>
                                '.service_finder_display_category_label($provider->wp_user_id).'
								'.service_finder_availability_label($provider->wp_user_id,$providersavailability).'
                                '.service_finder_check_varified_icon($provider->wp_user_id).'
                                '.$addtofavorite.'
                                
                                <div class="sf-featured-info">
                                    '.$featured.'
                                </div>
                            </div>
                            
                            <div class="sf-featured-right">
                                <div  class="sf-featured-provider"><a href="'.esc_url($link).'">'.service_finder_getExcerpts($provider->full_name,0,35).'</a></div>
                                <div  class="sf-featured-address"><i class="fa fa-map-marker"></i> '.$addressbox.' </div>
								'.service_finder_getDistance($provider->distance).'
                                '.service_finder_displayRating(service_finder_getAverageRating($provider->wp_user_id)).'
                                <div class="sf-featured-comapny">'.service_finder_getExcerpts(service_finder_getCompanyName($provider->wp_user_id),0,30).'</div>
                                <div class="sf-featured-text">'.service_finder_getExcerpts($provider->bio,0,300).'</div>
								'.service_finder_get_service_startfrom($provider->wp_user_id).'
                                '.$searchedservices.'
                                '.service_finder_show_provider_meta($provider->wp_user_id,$provider->phone,$provider->mobile).'
                            </div>
                            
                        </div>';
			}else{
			$msg .= '<div class="col-md-12">
                                <div class="sf-element-bx result-listing clearfix">
                                
                                    <div class="sf-thum-bx sf-listing-thum img-effect2" style="background-image:url('.esc_url($src).');"> <a href="'.esc_url($link).'" class="sf-listing-link"></a>
                                        
                                        <div class="overlay-bx">
											'.$addressbox.'
										</div>
										
										'.service_finder_get_primary_category_tag($provider->wp_user_id).'
										'.service_finder_availability_label($provider->wp_user_id,$providersavailability).'
										'.$featured.'
                                        '.service_finder_check_varified_icon($provider->wp_user_id).'
                                    </div>
                                    
                                    <div class="result-text '.service_finder_check_varified($provider->wp_user_id).'" id="proid-'.$provider->wp_user_id.'">
                                    	<h5 class="sf-title">'.service_finder_getExcerpts(service_finder_getCompanyName($provider->wp_user_id),0,30).'</h5>
										<strong class="sf-company-name"><a href="'.esc_url($link).'">'.service_finder_getExcerpts($provider->full_name,0,35).'</a></strong>
										'.service_finder_show_provider_meta($provider->wp_user_id,$provider->phone,$provider->mobile).'
                                        '.service_finder_displayRating(service_finder_getAverageRating($provider->wp_user_id)).'
										
                                        <div class="sf-address2-bx">
											<i class="fa fa-map-marker"></i>
											'.service_finder_getshortAddress($provider->wp_user_id).'
										</div>
										'.service_finder_getDistance($provider->distance).'
										<p>'.service_finder_getExcerpts($provider->bio,0,300).'</p>
										'.service_finder_get_service_startfrom($provider->wp_user_id).'
										'.$searchedservices.'
                                        '.$addtofavorite.'
                                    </div>
                                    
                                </div>
                            </div>';
			}				
			}elseif($_POST['viewtype'] == 'grid-3'){
			
			if(service_finder_themestyle() == 'style-2'){
			$msg .= '<div class="col-md-4 col-sm-6 equal-col">
                                <div class="sf-search-result-girds" id="proid-'.$provider->wp_user_id.'">
                            
                                <div class="sf-featured-top">
                                    <div class="sf-featured-media" style="background-image:url('.esc_url($src).')"></div>
                                    <div class="sf-overlay-box"></div>
                                    '.service_finder_display_category_label($provider->wp_user_id).'
									'.service_finder_availability_label($provider->wp_user_id,$providersavailability).'
                                    '.service_finder_check_varified_icon($provider->wp_user_id).'
									'.$addtofavorite.'
                                    
                                    <div class="sf-featured-info">
                                        '.$featured.'
                                        <div  class="sf-featured-provider">'.service_finder_getExcerpts($provider->full_name,0,35).'</div>
                                        <div  class="sf-featured-address"><i class="fa fa-map-marker"></i> '.$addressbox.' </div>
                                        '.service_finder_displayRating(service_finder_getAverageRating($provider->wp_user_id)).'
                                    </div>
									<a href="'.esc_url($link).'" class="sf-profile-link"></a>
                                </div>
                                
                                <div class="sf-featured-bot">
                                    <div class="sf-featured-comapny">'.service_finder_getExcerpts(service_finder_getCompanyName($provider->wp_user_id),0,30).'</div>
                                    <div class="sf-featured-text">'.service_finder_getExcerpts(nl2br(stripcslashes($provider->bio)),0,75).'</div>
                                    '.service_finder_show_provider_meta($provider->wp_user_id,$provider->phone,$provider->mobile).'
                                </div>
                                
                            </div>
                            </div>';
			}else{
			/*3 grid layout*/            
		    $msg .= '<div class="col-md-4 col-sm-6 equal-col">
                                <div class="sf-provider-bx item">
                    <div class="sf-element-bx">
                    
                        <div class="sf-thum-bx sf-listing-thum img-effect2" style="background-image:url('.esc_url($src).');"> <a href="'.esc_url($link).'" class="sf-listing-link"></a>
                            
							<div class="overlay-bx">
								'.$addressbox.'
							</div>
                            
                            '.service_finder_get_primary_category_tag($provider->wp_user_id).'
							'.service_finder_availability_label($provider->wp_user_id,$providersavailability).'
							'.$featured.'
                            
                        </div>
                        
                        <div class="padding-20 bg-white '.service_finder_check_varified($provider->wp_user_id).'">
                            <h4 class="sf-title">'.service_finder_getExcerpts(service_finder_getCompanyName($provider->wp_user_id),0,30).'</h4>
                            <strong class="sf-company-name"><a href="'.esc_url($link).'">'.service_finder_getExcerpts($provider->full_name,0,35).'</a></strong>
							'.service_finder_show_provider_meta($provider->wp_user_id,$provider->phone,$provider->mobile).'
							'.service_finder_displayRating(service_finder_getAverageRating($provider->wp_user_id)).'
							'.service_finder_check_varified_icon($provider->wp_user_id).'
                        </div>
                        
                        <div class="btn-group-justified" id="proid-'.$provider->wp_user_id.'">
                          <a href="'.esc_url($link).'" class="btn btn-custom">'.esc_html__('Full View','service-finder').' <i class="fa fa-arrow-circle-o-right"></i></a>
                          '.$addtofavorite.'
                        </div>
                        
                    </div>
                </div>
                            </div>';
				}			
			}else{
			/*4 grid layout*/
			if(service_finder_themestyle() == 'style-2'){
			$msg .= '<div class="col-md-3 col-sm-6 equal-col">
			<div class="sf-search-result-girds" id="proid-'.$provider->wp_user_id.'">
                            
                                <div class="sf-featured-top">
                                    <div class="sf-featured-media" style="background-image:url('.esc_url($src).')"></div>
                                    <div class="sf-overlay-box"></div>
                                    '.service_finder_display_category_label($provider->wp_user_id).'
									'.service_finder_availability_label($provider->wp_user_id,$providersavailability).'
                                    '.service_finder_check_varified_icon($provider->wp_user_id).'
									'.$addtofavorite.'
                                    
                                    <div class="sf-featured-info">
                                        '.$featured.'
                                        <div  class="sf-featured-provider">'.service_finder_getExcerpts($provider->full_name,0,35).'</div>
                                        <div  class="sf-featured-address"><i class="fa fa-map-marker"></i> '.$addressbox.' </div>
                                        '.service_finder_displayRating(service_finder_getAverageRating($provider->wp_user_id)).'
                                    </div>
									<a href="'.esc_url($link).'" class="sf-profile-link"></a>
                                </div>
                                
                                <div class="sf-featured-bot">
                                    <div class="sf-featured-comapny">'.service_finder_getExcerpts(service_finder_getCompanyName($provider->wp_user_id),0,20).'</div>
                                    <div class="sf-featured-text">'.service_finder_getExcerpts(nl2br(stripcslashes($provider->bio)),0,75).'</div>
                                    '.service_finder_show_provider_meta($provider->wp_user_id,$provider->phone,$provider->mobile).'
                                </div>
                                
                            </div>
							 </div>';
			}else{
			$msg .= '<div class="col-md-3 col-sm-6 equal-col">

                <div class="sf-provider-bx item">
                    <div class="sf-element-bx">
                    
                        <div class="sf-thum-bx sf-listing-thum img-effect2" style="background-image:url('.esc_url($src).');"> <a href="'.esc_url($link).'" class="sf-listing-link"></a>
                            
                           <div class="overlay-bx">
								'.$addressbox.'
						   </div>
                            
                           '.service_finder_get_primary_category_tag($provider->wp_user_id).'
						   '.service_finder_availability_label($provider->wp_user_id,$providersavailability).'
						   '.$featured.'
                            
                        </div>
                        
                        <div class="padding-20 bg-white '.service_finder_check_varified($provider->wp_user_id).'">
                            <h4 class="sf-title">'.service_finder_getExcerpts(service_finder_getCompanyName($provider->wp_user_id),0,20).'</h4>
                            <strong class="sf-company-name"><a href="'.esc_url($link).'">'.service_finder_getExcerpts($provider->full_name,0,35).'</a></strong>
							'.service_finder_show_provider_meta($provider->wp_user_id,$provider->phone,$provider->mobile).'
							'.service_finder_displayRating(service_finder_getAverageRating($provider->wp_user_id)).'
							'.service_finder_check_varified_icon($provider->wp_user_id).'
                        </div>
                        
                        <div class="btn-group-justified" id="proid-'.$provider->wp_user_id.'">
                          <a href="'.esc_url($link).'" class="btn btn-custom">'.esc_html__('Full View','service-finder').' <i class="fa fa-arrow-circle-o-right"></i></a>
                          '.$addtofavorite.'
                        </div>
                        
                    </div>
                </div>

            </div>';	
			}
            
			}
			/*End Search Style 1*/
	}elseif($service_finder_options['search-template'] == 'style-2'){
	/*Start Search Style 2*/
	
	$showaddressinfo = (isset($service_finder_options['show-address-info'])) ? esc_attr($service_finder_options['show-address-info']) : '';
	$addressbox = '';
	  if($showaddressinfo && $service_finder_options['show-postal-address'] && service_finder_check_address_info_access()){
			if(service_finder_themestyle() == 'style-2'){
			$addressbox = service_finder_getshortAddress($provider->wp_user_id);			
			}else{
			$addressbox = '<div class="overlay-text">
									<div class="sf-address-bx">
										<i class="fa fa-map-marker"></i>
										'.service_finder_getshortAddress($provider->wp_user_id).'
									</div>
								</div>';
			}					
		}
			if($_POST['viewtype'] == 'grid-2'){
			/*Grid 2 Layout*/
			if(service_finder_themestyle() == 'style-2'){
			$msg .= '<div class="col-md-6 col-sm-6 equal-col maphover" data-id="'.esc_attr($provider->wp_user_id).'">
                                <div class="sf-search-result-girds" id="proid-'.$provider->wp_user_id.'">
                            
                                <div class="sf-featured-top">
                                    <div class="sf-featured-media" style="background-image:url('.esc_url($src).')"></div>
                                    <div class="sf-overlay-box"></div>
                                    '.service_finder_display_category_label($provider->wp_user_id).'
									'.service_finder_availability_label($provider->wp_user_id,$providersavailability).'
                                    '.service_finder_check_varified_icon($provider->wp_user_id).'
									'.$addtofavorite.'
                                    
                                    <div class="sf-featured-info">
                                        '.$featured.'
                                        <div  class="sf-featured-provider">'.service_finder_getExcerpts($provider->full_name,0,35).'</div>
                                        <div  class="sf-featured-address"><i class="fa fa-map-marker"></i> '.$addressbox.' </div>
                                        '.service_finder_displayRating(service_finder_getAverageRating($provider->wp_user_id)).'
                                    </div>
									<a href="'.esc_url($link).'" class="sf-profile-link"></a>
                                </div>
                                
                                <div class="sf-featured-bot">
                                    <div class="sf-featured-comapny">'.service_finder_getExcerpts(service_finder_getCompanyName($provider->wp_user_id),0,20).'</div>
                                    <div class="sf-featured-text">'.service_finder_getExcerpts(nl2br(stripcslashes($provider->bio)),0,60).'</div>
                                    '.service_finder_show_provider_meta($provider->wp_user_id,$provider->phone,$provider->mobile).'
                                </div>
                                
                            </div>
                            </div>';
			}else{
            $msg .= '<div class="col-md-6 col-sm-6 equal-col maphover" data-id="'.esc_attr($provider->wp_user_id).'">
                                <div class="sf-provider-bx item">
                    <div class="sf-element-bx">
                    
                        <div class="sf-thum-bx sf-listing-thum img-effect2" style="background-image:url('.esc_url($src).');"> <a href="'.esc_url($link).'" class="sf-listing-link"></a>
                            
							<div class="overlay-bx">
								'.$addressbox.'
							</div>
                            
                            '.service_finder_get_primary_category_tag($provider->wp_user_id).'
							'.service_finder_availability_label($provider->wp_user_id,$providersavailability).'
							'.$featured.'
                            
                        </div>
                        
                        <div class="padding-20 bg-white '.service_finder_check_varified($provider->wp_user_id).'">
                            <h4 class="sf-title">'.service_finder_getExcerpts(service_finder_getCompanyName($provider->wp_user_id),0,20).'</h4>
                            <strong class="sf-company-name"><a href="'.esc_url($link).'">'.service_finder_getExcerpts($provider->full_name,0,35).'</a></strong>
							'.service_finder_show_provider_meta($provider->wp_user_id,$provider->phone,$provider->mobile).'
							'.service_finder_displayRating(service_finder_getAverageRating($provider->wp_user_id)).'
							'.service_finder_check_varified_icon($provider->wp_user_id).'
                        </div>
                        
                        <div class="btn-group-justified" id="proid-'.$provider->wp_user_id.'">
                          <a href="'.esc_url($link).'" class="btn btn-custom">'.esc_html__('Full View','service-finder').' <i class="fa fa-arrow-circle-o-right"></i></a>
                          '.$addtofavorite.'
                        </div>
                        
                    </div>
                </div>
                            </div>';
				}			
			}elseif($_POST['viewtype'] == 'listview'){
			/*Listview layout*/
			if(service_finder_themestyle() == 'style-2'){
			$msg .= '<div class="sf-featured-listing clearfix">
                            
                            <div class="sf-featured-left" id="proid-'.$provider->wp_user_id.'">
                                <div class="sf-featured-media" style="background-image:url('.esc_url($src).')"></div>
								<a href="'.esc_url($link).'" class="sf-listing-link"></a>
                                <div class="sf-overlay-box"></div>
                                '.service_finder_display_category_label($provider->wp_user_id).'
								'.service_finder_availability_label($provider->wp_user_id,$providersavailability).'
                                '.service_finder_check_varified_icon($provider->wp_user_id).'
                                '.$addtofavorite.'
                                
                                <div class="sf-featured-info">
                                    '.$featured.'
                                </div>
                            </div>
                            
                            <div class="sf-featured-right">
                                <div  class="sf-featured-provider"><a href="'.esc_url($link).'">'.service_finder_getExcerpts($provider->full_name,0,35).'</a></div>
                                <div  class="sf-featured-address"><i class="fa fa-map-marker"></i> '.$addressbox.' </div>
								'.service_finder_getDistance($provider->distance).'
                                '.service_finder_displayRating(service_finder_getAverageRating($provider->wp_user_id)).'
                                <div class="sf-featured-comapny">'.service_finder_getExcerpts(service_finder_getCompanyName($provider->wp_user_id),0,30).'</div>
                                <div class="sf-featured-text">'.service_finder_getExcerpts($provider->bio,0,300).'</div>
								'.service_finder_get_service_startfrom($provider->wp_user_id).'
                                '.$searchedservices.'
                                '.service_finder_show_provider_meta($provider->wp_user_id,$provider->phone,$provider->mobile).'
                            </div>
                            
                        </div>';
			}else{
			$msg .= '<div class="col-md-12"><div class="sf-element-bx result-listing clearfix">
                        
                            <div class="sf-thum-bx sf-listing-thum img-effect2" style="background-image:url('.esc_url($src).');"> <a href="'.esc_url($link).'" class="sf-listing-link"></a>
                                
								<div class="overlay-bx maphover" data-id="'.esc_attr($provider->wp_user_id).'">
									'.$addressbox.'
								</div>
								
								'.service_finder_get_primary_category_tag($provider->wp_user_id).'
								'.service_finder_availability_label($provider->wp_user_id,$providersavailability).'
								'.$featured.'
                                '.service_finder_check_varified_icon($provider->wp_user_id).'
                            </div>
                            
                            <div class="result-text '.service_finder_check_varified($provider->wp_user_id).'" id="proid-'.$provider->wp_user_id.'">
                                <h5 class="sf-title">'.service_finder_getExcerpts(service_finder_getCompanyName($provider->wp_user_id),0,30).'</h5>
                                <strong class="sf-company-name"><a href="'.esc_url($link).'">'.service_finder_getExcerpts($provider->full_name,0,35).'</a></strong>
								'.service_finder_show_provider_meta($provider->wp_user_id,$provider->phone,$provider->mobile).'
							    '.service_finder_displayRating(service_finder_getAverageRating($provider->wp_user_id)).'
								
                                <div class="sf-address2-bx">
									<i class="fa fa-map-marker"></i>
									'.service_finder_getshortAddress($provider->wp_user_id).'
								</div>
								'.service_finder_getDistance($provider->distance).'
								<p>'.service_finder_getExcerpts($provider->bio,0,150).'</p>
								'.service_finder_get_service_startfrom($provider->wp_user_id).'
								'.$searchedservices.'
                                '.$addtofavorite.'
								
                            </div>
                            
                        </div></div>';
			}			
			}else{
			/*Grid 2 Layout*/
			if(service_finder_themestyle() == 'style-2'){
			$msg .= '<div class="col-md-6 col-sm-6 equal-col maphover" data-id="'.esc_attr($provider->wp_user_id).'">
                                <div class="sf-search-result-girds" id="proid-'.$provider->wp_user_id.'">
                            
                                <div class="sf-featured-top">
                                    <div class="sf-featured-media" style="background-image:url('.esc_url($src).')"></div>
                                    <div class="sf-overlay-box"></div>
                                    '.service_finder_display_category_label($provider->wp_user_id).'
									'.service_finder_availability_label($provider->wp_user_id,$providersavailability).'
                                    '.service_finder_check_varified_icon($provider->wp_user_id).'
									'.$addtofavorite.'
                                    
                                    <div class="sf-featured-info">
                                        '.$featured.'
                                        <div  class="sf-featured-provider">'.service_finder_getExcerpts($provider->full_name,0,35).'</div>
                                        <div  class="sf-featured-address"><i class="fa fa-map-marker"></i> '.$addressbox.' </div>
                                        '.service_finder_displayRating(service_finder_getAverageRating($provider->wp_user_id)).'
                                    </div>
									<a href="'.esc_url($link).'" class="sf-profile-link"></a>
                                </div>
                                
                                <div class="sf-featured-bot">
                                    <div class="sf-featured-comapny">'.service_finder_getExcerpts(service_finder_getCompanyName($provider->wp_user_id),0,20).'</div>
                                    <div class="sf-featured-text">'.service_finder_getExcerpts(nl2br(stripcslashes($provider->bio)),0,60).'</div>
                                    '.service_finder_show_provider_meta($provider->wp_user_id,$provider->phone,$provider->mobile).'
                                </div>
                                
                            </div>
                            </div>';
			}else{
            $msg .= '<div class="col-md-6 col-sm-6 equal-col maphover" data-id="'.esc_attr($provider->wp_user_id).'">
                                <div class="sf-provider-bx item">
                    <div class="sf-element-bx">
                    
                        <div class="sf-thum-bx sf-listing-thum img-effect2" style="background-image:url('.esc_url($src).');"> <a href="'.esc_url($link).'" class="sf-listing-link"></a>
                            
							<div class="overlay-bx">
								'.$addressbox.'
							</div>
                            
                            '.service_finder_get_primary_category_tag($provider->wp_user_id).'
							'.service_finder_availability_label($provider->wp_user_id,$providersavailability).'
							'.$featured.'
                            
                        </div>
                        
                        <div class="padding-20 bg-white '.service_finder_check_varified($provider->wp_user_id).'">
                            <h4 class="sf-title">'.service_finder_getExcerpts(service_finder_getCompanyName($provider->wp_user_id),0,20).'</h4>
                            <strong class="sf-company-name"><a href="'.esc_url($link).'">'.service_finder_getExcerpts($provider->full_name,0,35).'</a></strong>
							'.service_finder_show_provider_meta($provider->wp_user_id,$provider->phone,$provider->mobile).'
							'.service_finder_displayRating(service_finder_getAverageRating($provider->wp_user_id)).'
							'.service_finder_check_varified_icon($provider->wp_user_id).'
                        </div>
                        
                        <div class="btn-group-justified" id="proid-'.$provider->wp_user_id.'">
                          <a href="'.esc_url($link).'" class="btn btn-custom">'.esc_html__('Full View','service-finder').' <i class="fa fa-arrow-circle-o-right"></i></a>
                          '.$addtofavorite.'
                        </div>
                        
                    </div>
                </div>
                            </div>';
				}			
			}
	}		
	}

    }
	 	if($_POST['viewtype'] == 'listview'){
		$msg .= '</div>';
		}else{
		$msg .= '</div>
                        </div>';
		}

	}else{
		/*No Result Found*/
		$msg .= '<div class="sf-nothing-found">
				<strong class="sf-tilte">'.esc_html__('Nothing Found', 'service-finder').'</strong>
					  <p>'.esc_html__('Apologies, but no results were found for the request.', 'service-finder').'</p>
				</div>';
		$flag = 1;
	}
	
	 // Optional, wrap the output into a container
        $msg = "<div class='cvf-universal-content'>" . $msg . "</div><br class = 'clear' />";
       
        // Ajax Pagination
        $no_of_paginations = ceil($count / $per_page);

        if ($cur_page >= 7) {
            $start_loop = $cur_page - 3;
            if ($no_of_paginations > $cur_page + 3)
                $end_loop = $cur_page + 3;
            else if ($cur_page <= $no_of_paginations && $cur_page > $no_of_paginations - 6) {
                $start_loop = $no_of_paginations - 6;
                $end_loop = $no_of_paginations;
            } else {
                $end_loop = $no_of_paginations;
            }
        } else {
            $start_loop = 1;
            if ($no_of_paginations > 7)
                $end_loop = 7;
            else
                $end_loop = $no_of_paginations;
        }
       
        // Pagination Buttons logic    
		if(service_finder_themestyle() == 'style-4'){
			$pag_container = "";
			$pag_container .= "<div class='cvf-universal-pagination pagination-two pagination-center clearfix'>
				<ul class='pagination'>";
	
			if ($previous_btn && $cur_page > 1) {
				$pre = $cur_page - 1;
				$pag_container .= "<li data-pnum='$pre' class='activelink'><a href='javascript:;'><i class='fa fa-chevron-left'></i></a></li>";
			} else if ($previous_btn) {
				$pag_container .= "<li class='inactive'><a href='javascript:;'><i class='fa fa-chevron-left'></i></a></li>";
			}
			for ($i = $start_loop; $i <= $end_loop; $i++) {
	
				if ($cur_page == $i)
					$pag_container .= "<li data-pnum='$i' class = 'selected active' ><a href='javascript:;'>{$i}</a></li>";
				else
					$pag_container .= "<li data-pnum='$i' class='activelink'><a href='javascript:;'>{$i}</a></li>";
			}
		   
			if ($next_btn && $cur_page < $no_of_paginations) {
				$nex = $cur_page + 1;
				$pag_container .= "<li data-pnum='$nex' class='activelink'><a href='javascript:;'><i class='fa fa-chevron-right'></i></a></li>";
			} else if ($next_btn) {
				$pag_container .= "<li class='inactive'><a href='javascript:;'><i class='fa fa-chevron-right'></i></a></li>";
			}
	
			$pag_container = $pag_container . "
				</ul>
			</div>";
		}else{
			$pag_container = "";
			$pag_container .= "<div class='cvf-universal-pagination pagination clearfix'>
				<ul class='pagination'>";
	
			if ($first_btn && $cur_page > 1) {
				$pag_container .= "<li data-pnum='1' class='activelink'><a href='javascript:;'><i class='fa fa-angle-double-left'></i></a></li>";
			} else if ($first_btn) {
				$pag_container .= "<li data-pnum='1' class='inactive'><a href='javascript:;'><i class='fa fa-angle-double-left'></i></a></li>";
			}
	
			if ($previous_btn && $cur_page > 1) {
				$pre = $cur_page - 1;
				$pag_container .= "<li data-pnum='$pre' class='activelink'><a href='javascript:;'><i class='fa fa-angle-left'></i></a></li>";
			} else if ($previous_btn) {
				$pag_container .= "<li class='inactive'><a href='javascript:;'><i class='fa fa-angle-left'></i></a></li>";
			}
			for ($i = $start_loop; $i <= $end_loop; $i++) {
	
				if ($cur_page == $i)
					$pag_container .= "<li data-pnum='$i' class = 'selected active' ><a href='javascript:;'>{$i}</a></li>";
				else
					$pag_container .= "<li data-pnum='$i' class='activelink'><a href='javascript:;'>{$i}</a></li>";
			}
		   
			if ($next_btn && $cur_page < $no_of_paginations) {
				$nex = $cur_page + 1;
				$pag_container .= "<li data-pnum='$nex' class='activelink'><a href='javascript:;'><i class='fa fa-angle-right'></i></a></li>";
			} else if ($next_btn) {
				$pag_container .= "<li class='inactive'><a href='javascript:;'><i class='fa fa-angle-right'></i></a></li>";
			}
	
			if ($last_btn && $cur_page < $no_of_paginations) {
				$pag_container .= "<li data-pnum='$no_of_paginations' class='activelink'><a href='javascript:;'><i class='fa fa-angle-double-right'></i></a></li>";
			} else if ($last_btn) {
				$pag_container .= "<li data-pnum='$no_of_paginations' class='inactive'><a href='javascript:;'><i class='fa fa-angle-double-right'></i></a></li>";
			}
	
			$pag_container = $pag_container . "
				</ul>
			</div>";
		}
        
       
	    if($flag == 1){
			$result = '<div class = "cvf-pagination-content">' . $msg . '</div>';
		}else{
	        $result = '<div class = "cvf-pagination-content">' . $msg . '</div>' .
    	    '<div class = "cvf-pagination-nav">' . $pag_container . '</div>';
		}
        
		
		if($center_latitude == '' && $center_longitude == ''){
			$defaultlatlng = service_finder_get_default_latlong();

			$center_latitude = $defaultlatlng['defaultlat'];
	
			$center_longitude = $defaultlatlng['defaultlng'];
		}
		
		
		$markers = rtrim($markers,',');
		$markers = '[ '.$markers.' ]';
		
		$start = $page * $per_page;
		$start = $start + 1;
		$end = $start + $per_page;
		
		if($count <= $end){
		$end = $count;
		}
		
		$start = ($count > 0) ? $start : 0;
		
		$resarr = array(
					'result' => $result,
					'markers' => $markers,
					'center_latitude' => $center_latitude,
					'center_longitude' => $center_longitude,
					'count' => $count,
					'startpagenum' => $start,
					'endresultnum' => $end
				);
		
		echo json_encode($resarr);		
	
    exit();
}

/*Create plans for stripe*/
function service_finder_createPlans($service_finder_options = array()){
global $wpdb, $service_finder_Errors, $service_finder_options;

/*Start Stripe Plans*/
require_once(SERVICE_FINDER_PAYMENT_GATEWAY_DIR.'/stripe/init.php');

if( isset($service_finder_options['stripe-type']) && $service_finder_options['stripe-type'] == 'test' ){
	$secret_key = (!empty($service_finder_options['stripe-test-secret-key'])) ? $service_finder_options['stripe-test-secret-key'] : '';
	$public_key = (!empty($service_finder_options['stripe-test-public-key'])) ? $service_finder_options['stripe-test-public-key'] : '';
}else{
	$secret_key = (!empty($service_finder_options['stripe-live-secret-key'])) ? $service_finder_options['stripe-live-secret-key'] : '';
	$public_key = (!empty($service_finder_options['stripe-live-public-key'])) ? $service_finder_options['stripe-live-public-key'] : '';
}

if($secret_key != ""){
try {
\Stripe\Stripe::setApiKey($secret_key);

$products_data = \Stripe\Product::all();

$products = array();
$k = 0;
if($products_data) {
	foreach($products_data['data'] as $product) {
		// store the plan ID as the array key and the plan name as the value
		$products[$k]['id'] = $product['id'];
		$products[$k]['name'] = $product['name'];
		$k++;
	}
}

$productexist = false;
$productid = '';
if(!empty($products)){
	foreach($products as $product){
		if($product['name'] == 'Service Finder'){
			$productexist = true;
			$productid = $product['id'];
		}
	}
}

if($productexist == false){
$newproduct = \Stripe\Product::create(array(
  "name" => 'Service Finder',
  "type" => "service")
);
$productid = $newproduct['id'];
}

// retrieve all plans from stripe
$plans_data = \Stripe\Plan::all(array("product" => $productid));

// setup a blank array
$plans = array();
if($plans_data) {
	foreach($plans_data['data'] as $plan) {
		// store the plan ID as the array key and the plan name as the value
		$plans[] = $plan['id'];
	}
}

try {
		for ($i=1; $i <= 3; $i++) {
		$enablepackage = (!empty($service_finder_options['enable-package'.$i])) ? $service_finder_options['enable-package'.$i] : '';
		if(isset($service_finder_options['enable-package'.$i]) && $enablepackage > 0){
		
		if (isset($service_finder_options['payment-type']) && ($service_finder_options['payment-type'] == 'recurring')) {
						$billingPeriod = esc_html__('year','service-finder');
						$packagebillingperiod = (!empty($service_finder_options['package'.$i.'-billing-period'])) ? $service_finder_options['package'.$i.'-billing-period'] : '';
						switch ($packagebillingperiod) {
							case 'Year':
								$billingPeriod = 'year';
								break;
							case 'Month':
								$billingPeriod = 'month';
								break;
							case 'Week':
								$billingPeriod = 'week';
								break;
							case 'Day':
								$billingPeriod = 'day';
								break;
						}
					
		
		$billingPrice = $service_finder_options['package'.$i.'-price'] * 100;
		$packageName = $service_finder_options['package'.$i.'-name'];
		$currencyCode = strtolower(service_finder_currencycode());
		$planID = 'package_'.$i;
		
		
		$free = (trim($service_finder_options['package'.$i.'-price']) == '0') ? true : false;
		
			if(!$free) {
			
			if(in_array($planID,$plans)){
			
			$p = \Stripe\Plan::retrieve($planID);
				if($p->nickname != $packageName && $p->amount == $billingPrice && $p->interval == $billingPeriod && $p->currency == $currencyCode){
					$p->nickname = $packageName;
					$p->save();
				}elseif($p->amount != $billingPrice || $p->interval != $billingPeriod || $p->currency != $currencyCode){
					$p->delete();
					\Stripe\Plan::create(array(
					  'product' => $productid,
					  "amount" => $billingPrice,
					  "interval" => $billingPeriod,
					  "nickname" => $packageName,
					  "currency" => $currencyCode,
					  "id" => $planID)
					);
				}
			}else{
				$a = \Stripe\Plan::create(array(
					  'product' => $productid,
					  "amount" => $billingPrice,
					  "interval" => $billingPeriod,
					  "nickname" => $packageName,
					  "currency" => $currencyCode,
					  "id" => $planID)
					);
					
			}	
			
			}
		}
		}
		}
		
		


} catch (Exception $e) {
	$body = $e->getJsonBody();
	$err  = $body['error'];

	$error = array(
			'status' => 'error',
			'err_message' => sprintf( esc_html__('%s', 'service-finder'), $err['message'] )
			);
	//echo $service_finder_Errors = json_encode($error);
}

} catch (Exception $e) {
	$body = $e->getJsonBody();
	$err  = $body['error'];

	$error = array(
			'status' => 'error',
			'err_message' => sprintf( esc_html__('%s', 'service-finder'), $err['message'] )
			);
	//echo $err['message'];
}

}
/*End Stripe Plans*/

/*Start PayU Latam Plans*/
require_once(SERVICE_FINDER_PAYMENT_GATEWAY_DIR.'/payulatam/lib/PayU.php');
					
if( isset($service_finder_options['payulatam-type']) && $service_finder_options['payulatam-type'] == 'test' ){
	$testmode = true;
	$payulatammerchantid = (isset($service_finder_options['payulatam-merchantid-test'])) ? $service_finder_options['payulatam-merchantid-test'] : '';
	$payulatamapilogin = (isset($service_finder_options['payulatam-apilogin-test'])) ? $service_finder_options['payulatam-apilogin-test'] : '';
	$payulatamapikey = (isset($service_finder_options['payulatam-apikey-test'])) ? $service_finder_options['payulatam-apikey-test'] : '';
	$payulatamaccountid = (isset($service_finder_options['payulatam-accountid-test'])) ? $service_finder_options['payulatam-accountid-test'] : '';
	
	$paymenturl = "https://sandbox.api.payulatam.com/payments-api/4.0/service.cgi";
	$reportsurl = "https://sandbox.api.payulatam.com/reports-api/4.0/service.cgi";
	$subscriptionurl = "https://sandbox.api.payulatam.com/payments-api/rest/v4.3/";
	
	$fullname = 'APPROVED';
	
}else{
	$testmode = false;
	$payulatammerchantid = (isset($service_finder_options['payulatam-merchantid-live'])) ? $service_finder_options['payulatam-merchantid-live'] : '';
	$payulatamapilogin = (isset($service_finder_options['payulatam-apilogin-live'])) ? $service_finder_options['payulatam-apilogin-live'] : '';
	$payulatamapikey = (isset($service_finder_options['payulatam-apikey-live'])) ? $service_finder_options['payulatam-apikey-live'] : '';
	$payulatamaccountid = (isset($service_finder_options['payulatam-accountid-live'])) ? $service_finder_options['payulatam-accountid-live'] : '';
	
	$paymenturl = "https://api.payulatam.com/payments-api/4.0/service.cgi";
	$reportsurl = "https://api.payulatam.com/reports-api/4.0/service.cgi";
	$subscriptionurl = "https://api.payulatam.com/payments-api/rest/v4.3/";
	
	$fullname = '';//$userdata->user_login;
}

$country = (isset($service_finder_options['payulatam-country'])) ? $service_finder_options['payulatam-country'] : '';

PayU::$apiKey = $payulatamapikey; //Enter your own apiKey here.
PayU::$apiLogin = $payulatamapilogin; //Enter your own apiLogin here.
PayU::$merchantId = $payulatammerchantid; //Enter your commerce Id here.
PayU::$language = SupportedLanguages::EN; //Select the language.
PayU::$isTest = $testmode; //Leave it True when testing.

// Payments URL
Environment::setPaymentsCustomUrl($paymenturl);
// Queries URL
Environment::setReportsCustomUrl($reportsurl);
// Subscriptions for recurring payments URL
Environment::setSubscriptionsCustomUrl($subscriptionurl);

if($payulatamapikey != "" && $payulatamapilogin != "" && $payulatammerchantid != "" && $payulatamaccountid != ""){

try {
	
	for ($i=1; $i <= 3; $i++) {
		$enablepackage = (!empty($service_finder_options['enable-package'.$i])) ? $service_finder_options['enable-package'.$i] : '';
		if(isset($service_finder_options['enable-package'.$i]) && $enablepackage > 0){
		
		if (isset($service_finder_options['payment-type']) && ($service_finder_options['payment-type'] == 'recurring')) {
						$billingPeriod = esc_html__('year','service-finder');
						$packagebillingperiod = (!empty($service_finder_options['package'.$i.'-billing-period'])) ? $service_finder_options['package'.$i.'-billing-period'] : '';
						switch ($packagebillingperiod) {
							case 'Year':
								$billingPeriod = esc_html__('YEAR','service-finder');
								break;
							case 'Month':
								$billingPeriod = esc_html__('MONTH','service-finder');
								break;
							case 'Week':
								$billingPeriod = esc_html__('WEEK','service-finder');
								break;
							case 'Day':
								$billingPeriod = esc_html__('DAY','service-finder');
								break;
						}
					
		
		$billingPrice = $service_finder_options['package'.$i.'-price'] * 100;
		$packageName = $service_finder_options['package'.$i.'-name'];
		$currencyCode = strtoupper(service_finder_currencycode());
		$planID = 'package_'.$i;
		
		
		$free = (trim($service_finder_options['package'.$i.'-price']) == '0') ? true : false;
		
			if(!$free) {
			
			$parameters = array(
				// Enter the plan‘s description here.
				PayUParameters::PLAN_DESCRIPTION => $packageName,
				// Enter the identification code of the plan here.
				PayUParameters::PLAN_CODE => $planID,
				// Enter the interval of the plan here.
				//DAY||WEEK||MONTH||YEAR
				PayUParameters::PLAN_INTERVAL => $billingPeriod,
				// Enter the number of intervals here.
				PayUParameters::PLAN_INTERVAL_COUNT => "1",
				// Enter the currency of the plan here.
				PayUParameters::PLAN_CURRENCY => $currencyCode,
				// Enter the value of the plan here.
				PayUParameters::PLAN_VALUE => $billingPrice,
				// Enter the account ID of the plan here.
				PayUParameters::ACCOUNT_ID => $payulatamaccountid,
				// Enter the amount of charges that make up the plan here
				PayUParameters::PLAN_MAX_PAYMENTS => "12",
				// Enter the retry interval here
				PayUParameters::PLAN_ATTEMPTS_DELAY => "1",
			);
			
			$response = PayUSubscriptionPlans::create($parameters);

			}
		}
		}
		}
	
} catch (Exception $e) {

	$error = array(
			'status' => 'error',
			'err_message' => $e->getMessage()
			);
	$service_finder_Errors = json_encode($error);
	
}	

}

/*End PayU Latam Plans*/

}

/*Get Page ID By Its Slug*/
function service_finder_get_id_by_slug($page_slug = '') {
    $page = get_page_by_path($page_slug);
    if ($page) {
        return $page->ID;
    } else {
        return null;
    }
}

/*Get Lat Long By Address*/
function service_finder_getLatLong($address = ''){
	global $service_finder_options;
	
	$apikey = (!empty($service_finder_options['server-api-key'])) ? $service_finder_options['server-api-key'] : '';
	
	$url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.$address.'&key='.$apikey;
	
	$feeds = json_decode(wp_remote_fopen($url));
	
	if($feeds->status == 'OK')
	{
		$lat = $feeds->results[0]->geometry->location->lat;
		$lng = $feeds->results[0]->geometry->location->lng;
		
		$result = array(
				'status' => $feeds->status,
				'lat' => $lat,
				'lng' => $lng,
		);
		
	}else{
		$result = array(
				'status' => service_finder_get_data($feeds,'status'),
				'error_message' => service_finder_get_data($feeds,'error_message')
		);
	}
	return $result;
}

/*Call the function to delete providers data when delete the provider from admin*/
function service_finder_custom_remove_user( $user_id = 0 ) {
service_finder_deleteProvidersData($user_id);
}
add_action( 'delete_user', 'service_finder_custom_remove_user', 10 );

/*Manage Redirect after login*/
function service_finder_redirect_afterlogin( $redirect_to = '', $request = '', $user = '' ) {
	global $user;
	if ( isset( $user->roles ) && is_array( $user->roles ) ) {
		if ( in_array( 'administrator', $user->roles ) ) {
			return $redirect_to;
		} elseif(in_array( 'Provider', $user->roles ) || in_array( 'Customer', $user->roles )){
			return service_finder_get_url_by_shortcode('[service_finder_my_account]');
		} else{
			return home_url('/');
		}
	} else {
		return $redirect_to;
	}
}
/*Filter to Manage Redirect after login*/
add_filter( 'login_redirect', 'service_finder_redirect_afterlogin', 10, 3 );

/*Manage authentication user for block and moderation purpose*/
add_filter('wp_authenticate_user', 'service_finder_user_authentication',10,2);
function service_finder_user_authentication ($user = 0, $password = '') {
	 global $service_finder_Errors, $service_finder_options, $service_finder_Tables, $wpdb;
	 
	 $allowaccess = (isset($service_finder_options['allow-access-untill-admin-approves'])) ? esc_attr($service_finder_options['allow-access-untill-admin-approves']) : '';

	 $role = service_finder_getUserRole($user->ID);
	 if($role == "Provider"){
		 $service_finder_Errors = new WP_Error();
		 $providerinfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE wp_user_id = %d',$user->ID));
		 
		 if($providerinfo->admin_moderation != "approved" && $allowaccess == 'no'){
			$service_finder_Errors->add( 'admin_moderation', esc_html__( 'ERROR: Your account is not approved' , 'service-finder') );
			return $service_finder_Errors;
		 }elseif($providerinfo->account_blocked == "yes"){
			$service_finder_Errors->add( 'account_block', esc_html__( 'ERROR: Your account has been blocked. Please contact administrator' , 'service-finder') );
			return $service_finder_Errors;
		 }else{
			return $user;
		 }
	 }else{
	 	return $user;
	 }
}

/*Encode url for use in javascript files*/
function service_finder_encodeURIComponent(){
$url = '';
		$unescaped = array(
        '%2D'=>'-','%5F'=>'_','%2E'=>'.','%21'=>'!', '%7E'=>'~',
        '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')'
    );
    $reserved = array(
        '%3B'=>';','%2C'=>',','%2F'=>'/','%3F'=>'?','%3A'=>':',
        '%40'=>'@','%26'=>'&','%3D'=>'=','%2B'=>'+','%24'=>'$'
    );
    $score = array(
        '%23'=>'#'
    );
    return strtr(rawurlencode($url), array_merge($reserved,$unescaped,$score));
}

//function to check permalinks
function service_finder_using_permalink(){
return get_option('permalink_structure');
}

//Sub header
function service_finder_sub_header_pl(){
global $service_finder_globals;
$service_finder_options = $service_finder_globals;

$subheader = (!empty($service_finder_options['sub-header'])) ? $service_finder_options['sub-header'] : '';
return $subheader;
}

//Inner page banner image
function service_finder_innerpage_banner_pl(){
global $service_finder_globals;
$service_finder_options = $service_finder_globals;

$bannerimg = (!empty($service_finder_options['inner-sub-header-bg-image']['url'])) ? $service_finder_options['inner-sub-header-bg-image']['url'] : '';
return $bannerimg;
}

//Provider sub header bg image
function service_finder_provider_coverbanner_pl(){
global $service_finder_globals;
$service_finder_options = $service_finder_globals;

$coverbanner = (!empty($service_finder_options['provider-sub-header-bg-image']['url'])) ? $service_finder_options['provider-sub-header-bg-image']['url'] : '';
return $coverbanner;
}

//Breadcrumb
function service_finder_breadcrumb_pl(){
global $service_finder_globals;
$service_finder_options = $service_finder_globals;

$breadcrumbs = (!empty($service_finder_options['breadcrumbs'])) ? $service_finder_options['breadcrumbs'] : '';
return $breadcrumbs;
}

//Get services
function service_finder_get_booking_services($bookingid = 0){
global $wpdb, $service_finder_Tables;
$html = '';
$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `id` = %d',$bookingid));
	if(!empty($row)){
		if($row->multi_date != 'yes'){
		$services = esc_html($row->services);
		$services = rtrim($services,'%%');
		$servicearr = explode('%%',$services);
		if(!empty($servicearr)){
		$html = '<ul class="sf-booking-services">';
			if(!empty($servicearr)){
			foreach($servicearr as $service){
				$tem = explode('-',$service);
				if(!empty($tem)){
				$serviceid = $tem[0];
				$servicehours = (!empty($tem[1])) ? $tem[1] : '';
				$servicedata = service_finder_get_service_by_id($serviceid);
				if(!empty($servicedata)){
					if($servicedata->cost_type == 'hourly'){
					$html .= '<li>'.esc_html($servicedata->service_name).' ('.esc_html__( 'Hourly', 'service-finder' ).') - '.esc_html($servicehours).' '.esc_html__( 'hrs', 'service-finder' ).'</li>';
					}elseif($servicedata->cost_type == 'perperson'){
					$html .= '<li>'.esc_html($servicedata->service_name).' ('.esc_html__( 'Item', 'service-finder' ).') - '.esc_html($servicehours).' '.esc_html__( 'persons', 'service-finder' ).'</li>';
					}else{
					$html .= '<li>'.esc_html($servicedata->service_name).'</li>';
					}
				}	
				}
			}
			}
		$html .= '</ul>';	
		}
		}else{
		$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$service_finder_Tables->booked_services." WHERE `booking_id` = %d GROUP BY `service_id`",$bookingid));
		if(!empty($results)){
			$html = '<div class="table-responsive" id="small-divice-tables">          
					  <table class="table sf-responsive-table">
						<thead>
						  <tr>
							<th>'.esc_html__( 'Service Name', 'service-finder' ).'</th>
							<th>'.esc_html__( 'Date', 'service-finder' ).'</th>
							<th>'.esc_html__( 'Start Time', 'service-finder' ).'</th>
							<th>'.esc_html__( 'End Time', 'service-finder' ).'</th>
							<th>'.esc_html__( 'Full Day', 'service-finder' ).'</th>
							<th>'.esc_html__( 'Status', 'service-finder' ).'</th>
							<th>'.esc_html__( 'Member Name', 'service-finder' ).'</th>
							<th>'.esc_html__( 'Coupon Code', 'service-finder' ).'</th>
							<th>'.esc_html__( 'Discount', 'service-finder' ).'</th>
						  </tr>
						</thead>
						<tbody>';
			foreach($results as $result){
				
				$totalrows = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$service_finder_Tables->booked_services." WHERE `booking_id` = %d AND `service_id` = %d",$bookingid,$result->service_id));
				
				$totaldays = count($totalrows);
				
				if($totaldays > 1)
				{
				$startrow = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$service_finder_Tables->booked_services." WHERE `booking_id` = %d AND `service_id` = %d order by id ASC limit 0,1",$bookingid,$result->service_id));
				
				$startdate = $startrow->date;
				
				$startrow = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$service_finder_Tables->booked_services." WHERE `booking_id` = %d AND `service_id` = %d order by id DESC limit 0,1",$bookingid,$result->service_id));
				
				$enddate = $startrow->date;
				$dates = service_finder_date_format($startdate).' - '.service_finder_date_format($enddate);
				}else{
				$startdate = service_finder_date_format($result->date);
				$dates = $startdate;
				}
				
				$starttime = ($result->without_padding_start_time != NULL) ? $result->without_padding_start_time : $result->start_time;
				$endtime = ($result->without_padding_end_time != NULL) ? $result->without_padding_end_time : $result->end_time;
				
				$starttime = ($starttime != NULL) ? $starttime : '-';
				$endtime = ($endtime != NULL) ? $endtime : '-';
				
				$fullday = ($result->fullday != "") ? $result->fullday : '-';
				$member = ($result->member_id > 0) ? service_finder_getMemberName($result->member_id) : '-';
				
				$couponcode = ($result->couponcode != "") ? esc_attr($result->couponcode) : 'NA';
				$discount = ($result->discount > 0 && $result->discount != "") ? service_finder_money_format($result->discount) : 'NA';
				$changestatus = '<button type="button" class="btn btn-warning btn-xs change_service_status" data-currentstatus="'.esc_attr($result->status).'" data-bsid="'.esc_attr($result->id).'" title="'.esc_html__( 'Change Status', 'service-finder' ).'"><i class="fa fa-battery-half"></i></button>';
				if($result->status == 'pending'){
				$status = 'incomplete';
				}else{
				$status = $result->status;
				}
				$html .= '<tr id="service-'.$result->id.'">
							<td data-title="'.esc_html__( 'Service Name', 'service-finder' ).'">'.service_finder_get_service_name($result->service_id).'</td>
							<td data-title="'.esc_html__( 'Date', 'service-finder' ).'">'.$dates.'</td>
							<td data-title="'.esc_html__( 'Start Time', 'service-finder' ).'">'.$starttime.'</td>
							<td data-title="'.esc_html__( 'End Time', 'service-finder' ).'">'.$endtime.'</td>
							<td data-title="'.esc_html__( 'Full Day', 'service-finder' ).'">'.$fullday.'</td>
							<td data-title="'.esc_html__( 'Status', 'service-finder' ).'"><span class="servicestatus">'.service_finder_translate_static_status_string($status).'</span> '.$changestatus.'</td>
							<td data-title="'.esc_html__( 'Member Name', 'service-finder' ).'">'.$member.'</td>
							<td data-title="'.esc_html__( 'Coupon Code', 'service-finder' ).'">'.$couponcode.'</td>
							<td data-title="'.esc_html__( 'Discount', 'service-finder' ).'">'.$discount.'</td>
						  </tr>';
			}
			$html .= '</tbody>
					  </table>
					  </div>';	
		}
		
		}
	}
	return $html;
}

function service_finder_get_bookingsms_services($bookingid = 0){
global $wpdb, $service_finder_Tables;
$html = '';
$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `id` = %d',$bookingid));
	if(!empty($row)){
		if($row->multi_date != 'yes'){
		$services = esc_html($row->services);
		$services = rtrim($services,'%%');
		$servicearr = explode('%%',$services);
		if(!empty($servicearr)){
		$html = '<ul class="sf-booking-services">';
			if(!empty($servicearr)){
			foreach($servicearr as $service){
				$tem = explode('-',$service);
				if(!empty($tem)){
				$serviceid = $tem[0];
				$servicehours = (!empty($tem[1])) ? $tem[1] : '';
				$servicedata = service_finder_get_service_by_id($serviceid);
				if(!empty($servicedata)){
					if($servicedata->cost_type == 'hourly'){
					$html .= '<li>'.esc_html($servicedata->service_name).' ('.esc_html__( 'Hourly', 'service-finder' ).') - '.esc_html($servicehours).' '.esc_html__( 'hrs', 'service-finder' ).'</li>';
					}elseif($servicedata->cost_type == 'perperson'){
					$html .= '<li>'.esc_html($servicedata->service_name).' ('.esc_html__( 'Item', 'service-finder' ).') - '.esc_html($servicehours).' '.esc_html__( 'persons', 'service-finder' ).'</li>';
					}else{
					$html .= '<li>'.esc_html($servicedata->service_name).'</li>';
					}
				}	
				}
			}
			}
		$html .= '</ul>';	
		}
		}else{
		$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$service_finder_Tables->booked_services." WHERE `booking_id` = %d GROUP BY `service_id`",$bookingid));
		if(!empty($results)){
			
			foreach($results as $result){
				$totalrows = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$service_finder_Tables->booked_services." WHERE `booking_id` = %d AND `service_id` = %d",$bookingid,$result->service_id));
				
				$totaldays = count($totalrows);
				
				if($totaldays > 1)
				{
				$startrow = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$service_finder_Tables->booked_services." WHERE `booking_id` = %d AND `service_id` = %d order by id ASC limit 0,1",$bookingid,$result->service_id));
				
				$startdate = $startrow->date;
				
				$startrow = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$service_finder_Tables->booked_services." WHERE `booking_id` = %d AND `service_id` = %d order by id DESC limit 0,1",$bookingid,$result->service_id));
				
				$enddate = $startrow->date;
				$dates = $startdate.' - '.$enddate;
				}else{
				$startdate = $result->date;
				$dates = $startdate;
				}
				
				$starttime = ($result->without_padding_start_time != NULL) ? $result->without_padding_start_time : $result->start_time;
				$endtime = ($result->without_padding_end_time != NULL) ? $result->without_padding_end_time : $result->end_time;
				
				$starttime = ($starttime != NULL) ? $starttime : '-';
				$endtime = ($endtime != NULL) ? $endtime : '-';
				
				$fullday = ($result->fullday != "") ? $result->fullday : '-';
				$member = ($result->member_id > 0) ? service_finder_getMemberName($result->member_id) : '-';
				
				$couponcode = ($result->couponcode != "") ? esc_attr($result->couponcode) : 'NA';
				$discount = ($result->discount > 0 && $result->discount != "") ? service_finder_money_format($result->discount) : 'NA';
				$changestatus = '<button type="button" class="btn btn-warning btn-xs change_service_status" data-currentstatus="'.esc_attr($result->status).'" data-bsid="'.esc_attr($result->id).'" title="'.esc_html__( 'Change Status', 'service-finder' ).'"><i class="fa fa-battery-half"></i></button>';
				if($result->status == 'pending'){
				$status = 'incomplete';
				}else{
				$status = $result->status;
				}
							$html .= esc_html__( 'Service Name', 'service-finder' ).': '.service_finder_get_service_name($result->service_id);
							$html .= esc_html__( 'Date', 'service-finder' ).': '.$dates;
							$html .= esc_html__( 'Start Time', 'service-finder' ).': '.$starttime;
							$html .= esc_html__( 'End Time', 'service-finder' ).': '.$endtime;
							$html .= esc_html__( 'Full Day', 'service-finder' ).': '.$fullday;
							$html .= esc_html__( 'Status', 'service-finder' ).': '.service_finder_translate_static_status_string($status).' '.$changestatus;
							$html .= esc_html__( 'Member Name', 'service-finder' ).': '.$member;
							$html .= esc_html__( 'Coupon Code', 'service-finder' ).': '.$couponcode;
							$html .= esc_html__( 'Discount', 'service-finder' ).': '.$discount;
			}
			
		}
		
		}
	}
	return $html;
}

//Get services summary
function service_finder_get_booking_services_summary($bookingid = 0){
global $wpdb, $service_finder_Tables;
$html = '';
$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `id` = %d',$bookingid));
	if(!empty($row)){
		if($row->multi_date == 'yes'){
		$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$service_finder_Tables->booked_services." WHERE `booking_id` = %d GROUP BY `service_id`",$bookingid));
		if(!empty($results)){
			$html = '<div class="table-responsive">          
					  <table class="table">
						<thead>
						  <tr>
							<th>'.esc_html__( 'Service Name', 'service-finder' ).'</th>
							<th>'.esc_html__( 'Date', 'service-finder' ).'</th>
							<th>'.esc_html__( 'Start Time', 'service-finder' ).'</th>
							<th>'.esc_html__( 'End Time', 'service-finder' ).'</th>
						  </tr>
						</thead>
						<tbody>';
			foreach($results as $result){
				$starttime = ($result->without_padding_start_time != NULL) ? $result->without_padding_start_time : $result->start_time;
				$endtime = ($result->without_padding_end_time != NULL) ? $result->without_padding_end_time : $result->end_time;
				
				$starttime = ($starttime != NULL) ? $starttime : '-';
				$endtime = ($endtime != NULL) ? $endtime : '-';

				$html .= '<tr id="service-'.$result->id.'">
							<td>'.service_finder_get_service_name($result->service_id).'</td>
							<td>'.service_finder_date_format($result->date).'</td>
							<td>'.$starttime.'</td>
							<td>'.$endtime.'</td>
						  </tr>';
			}
			$html .= '</tbody>
					  </table>
					  </div>';	
		}
		
		}
	}
	return $html;
}

//Get service by id
function service_finder_get_service_by_id($serviceid = 0){
global $wpdb, $service_finder_Tables;
$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->services.' WHERE `id` = %d',$serviceid));
return $row;	
}

add_action( 'add_meta_boxes', 'service_finder_fn_add_metabox' );
function service_finder_fn_add_metabox() {
	
	add_meta_box('pageoptions',esc_html__( 'Page Options', 'service-finder' ),'service_finder_fn_page_meta_box','page','side','high');

}

function service_finder_fn_page_meta_box()
{
    $post_id = get_the_ID();
  
    if (get_post_type($post_id) != 'page') {
        return;
    }
  
  	$value = get_post_meta($post_id, '_display_banner', true);
    wp_nonce_field('my_custom_nonce_'.$post_id, 'my_custom_nonce');
	
	if (service_finder_is_edit_page('new')){
	    $checked = 'checked="checked"';
	}else{
		$checked = checked($value, true, false);
	}
    ?>
    <div class="misc-pub-section misc-pub-section-last">
        <label><input type="checkbox" value="1" <?php echo esc_attr($checked); ?> name="_display_banner" /><?php esc_html_e('Display Banner', 'service-finder'); ?></label>
    </div>

    <?php
	$value = get_post_meta($post_id, '_display_title', true);
    if (service_finder_is_edit_page('new')){
	    $checked = 'checked="checked"';
	}else{
		$checked = checked($value, true, false);
	}
	?>
    
    <div class="misc-pub-section misc-pub-section-last">
        <label><input type="checkbox" value="1" <?php echo esc_attr($checked); ?> name="_display_title" /><?php esc_html_e('Display Title', 'service-finder'); ?></label>
    </div>
    
    <?php
	$value = get_post_meta($post_id, '_display_sidebar', true);
    if (service_finder_is_edit_page('new')){
	    $checked = 'checked="checked"';
	}else{
		$checked = checked($value, true, false);
	}
	?>
    
    <div class="misc-pub-section misc-pub-section-last">
        <label><input type="checkbox" value="1" <?php echo esc_attr($checked); ?> name="_display_sidebar" /><?php esc_html_e('Display Sidebar', 'service-finder'); ?></label>
    </div>
    
    <?php
	$value = get_post_meta($post_id, '_display_comment', true);
    if (service_finder_is_edit_page('new')){
	    $checked = 'checked="checked"';
	}else{
		$checked = checked($value, true, false);
	}
	?>
    
    <div class="misc-pub-section misc-pub-section-last">
        <label><input type="checkbox" value="1" <?php echo esc_attr($checked); ?> name="_display_comment" /><?php esc_html_e('Display Comment', 'service-finder'); ?></label>
    </div>
    <?php
}

add_action('save_post', 'service_finder_save_display_banner');

function service_finder_save_display_banner($post_id = 0)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
	$my_custom_nonce = (!empty($_POST['my_custom_nonce'])) ? esc_html($_POST['my_custom_nonce']) : '';
	$display_banner = (!empty($_POST['_display_banner'])) ? esc_html($_POST['_display_banner']) : '';
	$display_title = (!empty($_POST['_display_title'])) ? esc_html($_POST['_display_title']) : '';
	$display_sidebar = (!empty($_POST['_display_sidebar'])) ? esc_html($_POST['_display_sidebar']) : '';
	$display_comment = (!empty($_POST['_display_comment'])) ? esc_html($_POST['_display_comment']) : '';
	
	if (!isset($my_custom_nonce) || !wp_verify_nonce($my_custom_nonce, 'my_custom_nonce_'.$post_id)) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (isset($display_banner)) {
        update_post_meta($post_id, '_display_banner', $display_banner);
    } else {
        delete_post_meta($post_id, '_display_banner');
    }
	if (isset($display_title)) {
        update_post_meta($post_id, '_display_title', $display_title);
    } else {
        delete_post_meta($post_id, '_display_title');
    }
	if (isset($display_sidebar)) {
        update_post_meta($post_id, '_display_sidebar', $display_sidebar);
    } else {
        delete_post_meta($post_id, '_display_sidebar');
    }
	if (isset($display_comment)) {
        update_post_meta($post_id, '_display_comment', $display_comment);
    } else {
        delete_post_meta($post_id, '_display_comment');
    }
}

function service_finder_is_edit_page($new_edit = null){
    global $pagenow;
    //make sure we are on the backend
    if (!is_admin()) return false;


    if($new_edit == "edit")
        return in_array( $pagenow, array( 'post.php',  ) );
    elseif($new_edit == "new") //check for new post page
        return in_array( $pagenow, array( 'post-new.php' ) );
    else //check for either new or edit
        return in_array( $pagenow, array( 'post.php', 'post-new.php' ) );
}

//To Check if job booking is authorized or not
function service_finder_is_job_author($jobid = 0,$jobauthor = 0){
global $wpdb, $current_user; 

	if(is_user_logged_in() && service_finder_getUserRole($current_user->ID) == 'Customer' && $jobid > 0 && $jobauthor == $current_user->ID){
	  return true;
	}else{
	  return false;
	}
}

//To Check if job booking is authorized or not
function service_finder_is_quotation_author($quoteid = 0,$quoteauthor = 0){
global $wpdb, $current_user; 

	if(is_user_logged_in() && service_finder_getUserRole($current_user->ID) == 'Customer' && $quoteid > 0 && $quoteauthor == $current_user->ID){
	  return true;
	}else{
	  return false;
	}
}

//To Check if job booking is authorized or not
function service_finder_check_account_authorization($manageaccountby = '',$manageproviderid = 0){
global $wpdb, $current_user; 

	if(is_user_logged_in() && $manageaccountby == 'admin' && service_finder_getUserRole($manageproviderid) == 'Provider' && service_finder_getUserRole($current_user->ID) == 'administrator'){
	  return true;
	}else{
	  return false;
	}
}

function service_finder_get_countries(){
	$countries = array(
    "AF" =>  'Afghanistan',
    "AX" =>  'Aland Islands',
    "AL" =>  'Albania',
    "DZ" =>  'Algeria',
    "AS" =>  'American Samoa',
    "AD" =>  'Andorra',
    "AO" =>  'Angola',
    "AI" =>  'Anguilla',
    "AQ" =>  'Antarctica',
    "AG" =>  'Antigua and Barbuda',
    "AR" =>  'Argentina',
    "AM" =>  'Armenia',
    "AW" =>  'Aruba',
    "AU" =>  'Australia',
    "AT" =>  'Austria',
    "AZ" =>  'Azerbaijan',
    "BS" =>  'Bahamas',
    "BH" =>  'Bahrain',
    "BD" =>  'Bangladesh',
    "BB" =>  'Barbados',
    "BY" =>  'Belarus',
    "BE" =>  'Belgium',
    "BZ" =>  'Belize',
    "BJ" =>  'Benin',
    "BM" =>  'Bermuda',
    "BT" =>  'Bhutan',
    "BO" =>  'Bolivia',
    "BA" =>  'Bosnia and Herzegovina',
    "BW" =>  'Botswana',
    "BV" =>  'Bouvet Island',
    "BR" =>  'Brazil',
    "IO" =>  'British Indian Ocean Territory',
    "BN" =>  'Brunei Darussalam',
    "BG" =>  'Bulgaria',
    "BF" =>  'Burkina Faso',
    "BI" =>  'Burundi',
    "KH" =>  'Cambodia',
    "CM" =>  'Cameroon',
    "CA" =>  'Canada',
    "CV" =>  'Cape Verde',
    "KY" =>  'Cayman Islands',
    "CF" =>  'Central African Republic',
    "TD" =>  'Chad',
    "CL" =>  'Chile',
    "CN" =>  'China',
    "CX" =>  'Christmas Island',
    "CC" =>  'Cocos (Keeling) Islands',
    "CO" =>  'Colombia',
    "KM" =>  'Comoros',
    "CG" =>  'Congo',
    "CD" =>  'Congo, The Democratic Republic of The',
    "CK" =>  'Cook Islands',
    "CR" =>  'Costa Rica',
    "CI" =>  "Cote D'Ivoire",
    "HR" =>  'Croatia',
    "CU" =>  'Cuba',
    "CY" =>  'Cyprus',
    "CZ" =>  'Czech Republic',
    "DK" =>  'Denmark',
    "DJ" =>  'Djibouti',
    "DM" =>  'Dominica',
    "DO" =>  'Dominican Republic',
    "EC" =>  'Ecuador',
    "EG" =>  'Egypt',
    "SV" =>  'El Salvador',
    "GQ" =>  'Equatorial Guinea',
    "ER" =>  'Eritrea',
    "EE" =>  'Estonia',
    "ET" =>  'Ethiopia',
    "FK" =>  'Falkland Islands (Malvinas)',
    "FO" =>  'Faroe Islands',
    "FJ" =>  'Fiji',
    "FI" =>  'Finland',
    "FR" =>  'France',
    "GF" =>  'French Guiana',
    "PF" =>  'French Polynesia',
    "TF" =>  'French Southern Territories',
    "GA" =>  'Gabon',
    "GM" =>  'Gambia',
    "GE" =>  'Georgia',
    "DE" =>  'Germany',
    "GH" =>  'Ghana',
    "GI" =>  'Gibraltar',
    "GR" =>  'Greece',
    "GL" =>  'Greenland',
    "GD" =>  'Grenada',
    "GP" =>  'Guadeloupe',
    "GU" =>  'Guam',
    "GT" =>  'Guatemala',
    "GG" =>  'Guernsey',
    "GN" =>  'Guinea',
    "GW" =>  'Guinea-bissau',
    "GY" =>  'Guyana',
    "HT" =>  'Haiti',
    "HM" =>  'Heard Island and Mcdonald Islands',
    "VA" =>  'Holy See (Vatican City State)',
    "HN" =>  'Honduras',
    "HK" =>  'Hong Kong',
    "HU" =>  'Hungary',
    "IS" =>  'Iceland',
    "IN" =>  'India',
    "ID" =>  'Indonesia',
    "IR" =>  'Iran, Islamic Republic of',
    "IQ" =>  'Iraq',
    "IE" =>  'Ireland',
    "IM" =>  'Isle of Man',
    "IL" =>  'Israel',
    "IT" =>  'Italy',
    "JM" =>  'Jamaica',
    "JP" =>  'Japan',
    "JE" =>  'Jersey',
    "JO" =>  'Jordan',
    "KZ" =>  'Kazakhstan',
    "KE" =>  'Kenya',
    "KI" =>  'Kiribati',
    "KP" =>  'Korea, Democratic People\'s Republic of',
    "KR" =>  'Korea, Republic of',
    "KW" =>  'Kuwait',
    "KG" =>  'Kyrgyzstan',
    "LA" =>  'Lao People\'s Democratic Republic',
    "LV" =>  'Latvia',
    "LB" =>  'Lebanon',
    "LS" =>  'Lesotho',
    "LR" =>  'Liberia',
    "LY" =>  'Libyan Arab Jamahiriya',
    "LI" =>  'Liechtenstein',
    "LT" =>  'Lithuania',
    "LU" =>  'Luxembourg',
    "MO" =>  'Macao',
    "MK" =>  'Macedonia, The Former Yugoslav Republic of',
    "MG" =>  'Madagascar',
	"HU" =>  'Hungary',
    "MW" =>  'Malawi',
    "MY" =>  'Malaysia',
    "MV" =>  'Maldives',
    "ML" =>  'Mali',
    "MT" =>  'Malta',
    "MH" =>  'Marshall Islands',
    "MQ" =>  'Martinique',
    "MR" =>  'Mauritania',
    "MU" =>  'Mauritius',
    "YT" =>  'Mayotte',
    "MX" =>  'Mexico',
    "FM" =>  'Micronesia, Federated States of',
    "MD" =>  'Moldova, Republic of',
    "MC" =>  'Monaco',
    "MN" =>  'Mongolia',
    "ME" =>  'Montenegro',
    "MS" =>  'Montserrat',
    "MA" =>  'Morocco',
    "MZ" =>  'Mozambique',
    "MM" =>  'Myanmar',
    "NA" =>  'Namibia',
    "NR" =>  'Nauru',
    "NP" =>  'Nepal',
    "NL" =>  'Netherlands',
    "AN" =>  'Netherlands Antilles',
    "NC" =>  'New Caledonia',
    "NZ" =>  'New Zealand',
    "NI" =>  'Nicaragua',
    "NE" =>  'Nicaragua',
    "NG" =>  'Nigeria',
	"NE" =>  'Niger',
    "NU" =>  'Niue',
    "NF" =>  'Norfolk Island',
    "MP" =>  'Northern Mariana Islands',
    "NO" =>  'Norway',
    "OM" =>  'Oman',
    "PK" =>  'Pakistan',
    "PW" =>  'Palau',
    "PS" =>  'Palestinian Territory, Occupied',
    "PA" =>  'Panama',
    "PG" =>  'Papua New Guinea',
    "PY" =>  'Paraguay',
    "PE" =>  'Peru',
    "PH" =>  'Philippines',
    "PN" =>  'Pitcairn',
    "PL" =>  'Poland',
    "PT" =>  'Portugal',
    "PR" =>  'Puerto Rico',
    "QA" =>  'Qatar',
    "RE" =>  'Reunion',
    "RO" =>  'Romania',
    "RU" =>  'Russian Federation',
    "RW" =>  'Rwanda',
    "SH" =>  'Saint Helena',
    "KN" =>  'Saint Kitts and Nevis',
    "LC" =>  'Saint Lucia',
    "PM" =>  'Saint Pierre and Miquelon',
    "VC" =>  'Saint Vincent and The Grenadines',
    "WS" =>  'Samoa',
    "SM" =>  'San Marino',
    "ST" =>  'Sao Tome and Principe',
    "SA" =>  'Saudi Arabia',
    "SN" =>  'Senegal',
    "RS" =>  'Serbia',
    "SC" =>  'Seychelles',
    "SL" =>  'Sierra Leone',
    "SG" =>  'Singapore',
    "SK" =>  'Slovakia',
    "SI" =>  'Slovenia',
    "SB" =>  'Solomon Islands',
    "SO" =>  'Somalia',
    "ZA" =>  'South Africa',
    "GS" =>  'South Georgia and The South Sandwich Islands',
    "ES" =>  'Spain',
    "LK" =>  'Sri Lanka',
    "SD" =>  'Sudan',
    "SR" =>  'Suriname',
    "SJ" =>  'Svalbard and Jan Mayen',
    "SZ" =>  'Swaziland',
    "SE" =>  'Sweden',
    "CH" =>  'Switzerland',
    "SY" =>  'Syrian Arab Republic',
    "TW" =>  'Taiwan, Province of China',
    "TJ" =>  'Tajikistan',
    "TZ" =>  'Tanzania, United Republic of',
    "TH" =>  'Thailand',
    "TL" =>  'Timor-leste',
    "TG" =>  'Togo',
    "TK" =>  'Tokelau',
    "TO" =>  'Tonga',
    "TT" =>  'Trinidad and Tobago',
    "TN" =>  'Tunisia',
    "TR" =>  'Turkey',
    "TM" =>  'Turkmenistan',
    "TC" =>  'Turks and Caicos Islands',
    "TV" =>  'Tuvalu',
    "UG" =>  'Uganda',
    "UA" =>  'Ukraine',
    "AE" =>  'United Arab Emirates',
    "GB" =>  'United Kingdom',
    "US" =>  'United States',
    "UM" =>  'United States Minor Outlying Islands',
    "UY" =>  'Uruguay',
    "UZ" =>  'Uzbekistan',
    "VU" =>  'Vanuatu',
    "VE" =>  'Venezuela',
    "VN" =>  'Viet Nam',
    "VG" =>  'Virgin Islands, British',
    "VI" =>  'Virgin Islands, U.S.',
    "WF" =>  'Wallis and Futuna',
    "EH" =>  'Western Sahara',
    "YE" =>  'Yemen',
    "ZM" =>  'Zambia',
    "ZW" =>  'Zimbabwe');
	return $countries;
}

function service_finder_convert_to_csv($input_array = array(), $output_file_name = '', $delimiter = '')
{
    /** open raw memory as file, no need for temp files */
    $temp_memory = fopen('php://memory', 'w');
    /** loop through array  */
    foreach ($input_array as $line) {
        /** default php csv handler **/
        fputcsv($temp_memory, $line, $delimiter);
    }
    /** rewrind the "file" with the csv lines **/
    fseek($temp_memory, 0);
    /** modify header to be downloadable csv file **/
    header('Content-Type: application/csv');
    header('Content-Disposition: attachement; filename="' . $output_file_name . '";');
    /** Send file to browser for download */
    fpassthru($temp_memory);
}

/*Reset provider package*/
function service_finder_resetProviderPackage($userId = 0) {
	global $wpdb, $service_finder_options, $service_finder_Tables;
	for ($i=1; $i <= 3; $i++) {
		$freepackage = (trim($service_finder_options['package'.$i.'-price']) == '0') ? true : false;
		if((trim($service_finder_options['package'.$i.'-price']) == '0')){
			$freepackage = 'package_'.$i;
			break;
		}else{
			$freepackage = '';
		}
	}
	
	if($freepackage != ""){
	update_user_meta( $userId, 'provider_role', $freepackage );
	}else{
	update_user_meta( $userId, 'current_provider_status', 'expire' );
	delete_user_meta($userId,'provider_role' );
	}
	
	delete_user_meta($userId, 'recurring_profile_id');
	delete_user_meta($userId, 'recurring_profile_amt');
	delete_user_meta($userId, 'recurring_profile_period');
	delete_user_meta($userId, 'recurring_profile_desc_full'); 
	delete_user_meta($userId, 'recurring_profile_desc'); 
	delete_user_meta($userId, 'recurring_profile_type');
	delete_user_meta($userId, 'paypal_token');
	delete_user_meta($userId, 'reg_paypal_role');

	delete_user_meta($userId, 'expire_limit');
	delete_user_meta($userId, 'profile_amt');
	delete_user_meta($userId, 'stripe_customer_id');
	delete_user_meta($userId, 'stripe_token');
	delete_user_meta($userId, 'subscription_id');
	delete_user_meta($userId, 'payment_mode');
	delete_user_meta($userId, 'pay_type');
	
	delete_user_meta($userId, 'expire_limit');
	delete_user_meta($userId, 'provider_activation_time');
	
	$primarycategory = get_user_meta($userId, 'primary_category',true);
	
	/*Update Primary category*/
	$data = array(
			'category_id' => $primarycategory,
			);
	
	$where = array(
			'wp_user_id' => $userId,
			);
	$wpdb->update($service_finder_Tables->providers,wp_unslash($data),$where);
}

/*Scan Directory for css/js*/
if(!function_exists('service_finder_booking_scan_dir')){
	function service_finder_booking_scan_dir($folder = '') {
	  $dircontent = scandir($folder);
	  $ret='';
	  foreach($dircontent as $filename) {
	    if ($filename != '.' && $filename != '..') {
	      if (filemtime($folder.$filename) === false) return false;
	      $ret.=date("YmdHis", filemtime($folder.$filename)).$filename;
	    }
	  }
	  return md5($ret);
	}
}

/*Delete Old Cache*/
if(!function_exists('service_finder_booking_delete_old_cache')){
	function service_finder_booking_delete_old_cache($folder = '') {
	  $olddate=time()-60;
	  $dircontent = scandir($folder);
	  foreach($dircontent as $filename) {
	    if (strlen($filename)==32 && filemtime($folder.$filename) && filemtime($folder.$filename)<$olddate) unlink($folder.$filename);
	  }
	}
}

/*Get contact info*/
if(!function_exists('service_finder_get_contact_info')){
	function service_finder_get_contact_info($phone = '',$mobile = ''){
		$contactnumber = '';
		if($phone != "" && $mobile != ""){
		$contactnumber = '<a href="tel:'.$phone.'">'.$phone.'</a>, <a href="tel:'.$mobile.'">'.$mobile.'</a>';
		}elseif($phone != ""){
		$contactnumber = '<a href="tel:'.$phone.'">'.$phone.'</a>';
		}elseif($mobile != ""){
		$contactnumber = '<a href="tel:'.$mobile.'">'.$mobile.'</a>';
		}
		return $contactnumber;  
	}
}

/*Get contact info*/
if(!function_exists('service_finder_get_contact_info_with_text')){
	function service_finder_get_contact_info_with_text($phone = '',$mobile = ''){
		$contactnumber = '';
		
		if(service_finder_themestyle() == 'style-4'){
			if($phone != "" && $mobile != ""){
			$contactnumber = '<div class="sf-provi-coInfo-text">'.esc_html__('Tel', 'service-finder').': <a href="tel:'.$phone.'">'.$phone.'</a></div> <div class="sf-provi-coInfo-text">'.esc_html__('Mob', 'service-finder').': <a href="tel:'.$mobile.'">'.$mobile.'</a></div>';
			}elseif($phone != ""){
			$contactnumber = '<div class="sf-provi-coInfo-text">'.esc_html__('Tel', 'service-finder').': <a href="tel:'.$phone.'">'.$phone.'</a></div>';
			}elseif($mobile != ""){
			$contactnumber = '<div class="sf-provi-coInfo-text">'.esc_html__('Mob', 'service-finder').': <a href="tel:'.$mobile.'">'.$mobile.'</a></div>';
			}
		}else{
			if($phone != "" && $mobile != ""){
			$contactnumber = '<b>'.esc_html__('Tel', 'service-finder').': </b><a href="tel:'.$phone.'">'.$phone.'</a><br/> <b>'.esc_html__('Mob', 'service-finder').': </b><a href="tel:'.$mobile.'">'.$mobile.'</a><br/>';
			}elseif($phone != ""){
			$contactnumber = '<b>'.esc_html__('Tel', 'service-finder').': </b><a href="tel:'.$phone.'">'.$phone.'</a>';
			}elseif($mobile != ""){
			$contactnumber = '<b>'.esc_html__('Mob', 'service-finder').': </b><a href="tel:'.$mobile.'">'.$mobile.'</a>';
			}
		}
		
		return $contactnumber;  
	}
}

/*Get contact info*/
if(!function_exists('service_finder_get_contact_info_for_toltip')){
	function service_finder_get_contact_info_for_toltip($phone = '',$mobile = ''){
		$contactnumber = '';
		if($phone != "" && $mobile != ""){
		$contactnumber = 'Tel: '.$phone.' Mob: '.$mobile;
		}elseif($phone != ""){
		$contactnumber = 'Tel: '.$phone;
		}elseif($mobile != ""){
		$contactnumber = 'Mob: '.$mobile;
		}
		return $contactnumber;  
	}
}


/*Get contact info*/
if(!function_exists('service_finder_cancel_subscription')){
function service_finder_cancel_subscription($userId = 0,$by = '') {
	global $wpdb, $service_finder_options, $service_finder_Tables;
	service_finder_SendSubscriptionNotificationMail($userId,0,$by);
	
	update_user_meta( $userId, 'current_provider_status', 'cancel' );
	delete_user_meta($userId,'provider_role' );
	
	delete_user_meta($userId, 'payulatam_planid');
	delete_user_meta($userId, 'payulatam_customer_id');
									
	delete_user_meta($userId, 'recurring_profile_id');
	delete_user_meta($userId, 'recurring_profile_amt');
	delete_user_meta($userId, 'recurring_profile_period');
	delete_user_meta($userId, 'recurring_profile_desc_full'); 
	delete_user_meta($userId, 'recurring_profile_desc'); 
	delete_user_meta($userId, 'recurring_profile_type');
	delete_user_meta($userId, 'paypal_token');
	delete_user_meta($userId, 'reg_paypal_role');

	delete_user_meta($userId, 'expire_limit');
	delete_user_meta($userId, 'profile_amt');
	delete_user_meta($userId, 'stripe_customer_id');
	delete_user_meta($userId, 'stripe_token');
	delete_user_meta($userId, 'subscription_id');
	delete_user_meta($userId, 'payment_mode');
	delete_user_meta($userId, 'pay_type');
	delete_user_meta($userId, 'orderNumber');
	
	delete_user_meta($userId, 'expire_limit');
	delete_user_meta($userId, 'provider_activation_time');
	
	$primarycategory = get_user_meta($userId, 'primary_category',true);
	
	/*Update Primary category*/
	$data = array(
			'category_id' => $primarycategory,
			);
	
	$where = array(
			'wp_user_id' => $userId,
			);
	$wpdb->update($service_finder_Tables->providers,wp_unslash($data),$where);
	
	$data = array(
			'free_limits' => 0,
			'available_limits' => 0,
			'paid_limits' => 0,
			);
	$where = array(
			'provider_id' => $userId,

			);		
	
	$wpdb->update($service_finder_Tables->job_limits,wp_unslash($data),$where);
}
}

/*Redirect after comment submit on author*/
function service_finder_comment_redirect( $location = '' ) {
	global $service_finder_options;
	$postid = (isset($_POST['comment_post_ID'])) ? $_POST['comment_post_ID'] : '';
	$post_type = get_post_type($postid);
	
	if($post_type == 'sf_comment_rating'){
	
		$keepauthorword = (!empty($service_finder_options['keep-author-word'])) ? $service_finder_options['keep-author-word'] : '';
		$authorreplacestring = (!empty($service_finder_options['author-replace-string'])) ? $service_finder_options['author-replace-string'] : '';
		
		if($keepauthorword == 'no'){
			
			$location = str_replace('comment-rating','',$location);
			
		}else{
		
		if($keepauthorword == 'yes' && $authorreplacestring != ""){
			
			$location = str_replace('comment-rating',$authorreplacestring,$location);
			
		}elseif($keepauthorword == 'yes' && $authorreplacestring == ""){
			
			$location = str_replace('comment-rating','author',$location);
		
		}
		}
	
		$tem = explode('comment-page',$location);
		if(!empty($tem[1])){
		$base = $tem[0];
		$mid = explode('#',$tem[1]);
			if($mid[1] != ""){
			$end = $mid[1];
			$location = $base.'#'.$end;
			}else{
			$location = $base;
			}
		}
	}

	return $location;
}

add_filter( 'comment_post_redirect', 'service_finder_comment_redirect' );

/*Add Notifications*/
if ( !function_exists( 'service_finder_add_notices' ) ){
function service_finder_add_notices($args = array()) {
global $wpdb, $service_finder_Tables;
$data = array(
		'datetime' => date('Y-m-d H:i:s'),
		'admin_id' => (!empty($args['admin_id'])) ? $args['admin_id'] : 0,
		'provider_id' => (!empty($args['provider_id'])) ? $args['provider_id'] : 0,
		'customer_id' => (!empty($args['customer_id'])) ? $args['customer_id'] : 0,
		'target_id' => (!empty($args['target_id'])) ? $args['target_id'] : 0,
		'topic' => (!empty($args['topic'])) ? $args['topic'] : '',
		'title' => (!empty($args['title'])) ? $args['title'] : '',
		'notice' => (!empty($args['notice'])) ? $args['notice'] : '',
		'extra' => (!empty($args['extra'])) ? $args['extra'] : ''
		);
$wpdb->insert($service_finder_Tables->notifications,wp_unslash($data));

}
}

/*View Notifications*/
add_action('wp_ajax_view_notificaions', 'service_finder_view_notificaions');
add_action('wp_ajax_nopriv_view_notificaions', 'service_finder_view_notificaions');
function service_finder_view_notificaions(){
global $wpdb, $service_finder_Tables;

	$usertype = (isset($_POST['usertype'])) ? esc_html($_POST['usertype']) : '';
	$userid = (isset($_POST['userid'])) ? esc_html($_POST['userid']) : '';
	
	$data = array(
			'read' => 'yes'
			);
	
	$where = '';
	
	if($usertype == 'Provider'){
		$where = array(
				'provider_id' => $userid
				);		
	}elseif($usertype == 'Customer'){
		$where = array(
				'customer_id' => $userid
				);	
	}		

	$wpdb->update($service_finder_Tables->notifications,wp_unslash($data),$where);
	
	exit(0);
}

/*Show contact info at search result page*/
function show_contactinfo_at_search_result($phone = '',$mobile = ''){
global $service_finder_options;
	if($service_finder_options['show-address-info'] && $service_finder_options['show-contact-number'] && service_finder_check_address_info_access()){
		$contactinfo = '<strong class="sf-provider-phone">'.service_finder_get_contact_info_with_text($phone,$mobile).'</strong>';
		return $contactinfo;
	}
}

/*Show total review at search result page*/
function show_review_at_search_result($providerid = 0){
global $service_finder_options,$wpdb,$service_finder_Tables;
	if($service_finder_options['review-system']){
		if($service_finder_options['review-style'] == 'open-review'){
			$comment_postid = get_user_meta($providerid,'comment_post',true);
			$total_review = get_comments_number( $comment_postid );
			$review = $total_review.' '.esc_html__('Review','service-finder');
			return $review; 
		}elseif($service_finder_options['review-style'] == 'booking-review'){
			$allreviews = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->feedback.' where provider_id = %d',$providerid));
			$total_review = count($allreviews);
			$review = $total_review.' '.esc_html__('Review','service-finder');
			return $review; 
		}	
	}

}

/*Show Request a quote model popup to search result page*/
function show_request_quote_at_search_result($providerid = 0){
global $service_finder_options,$wpdb,$service_finder_Tables;
	$requestquote = (!empty($service_finder_options['requestquote-replace-string'])) ? esc_attr($service_finder_options['requestquote-replace-string']) : esc_html__( 'Request a Quote', 'service-finder' );
	
	if($service_finder_options['request-quote'] && service_finder_request_quote_for_loggedin_user()){
		return '<button data-providerid="'.$providerid.'" data-tool="tooltip" data-toggle="modal" data-target="#quotes-Modal" type="button" class="btn btn-border" data-toggle="tooltip" data-placement="top" title="'.$requestquote.'"> <i class="fa fa-file-o"></i> </button>';
	}

}

/*Identity Check*/
function service_finder_is_varified_user($providerid = 0){
global $wpdb,$service_finder_Tables;

$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' where `identity` = "approved" AND `wp_user_id` = %d',$providerid));
if(!empty($row)){
	return true;
}else{
	return false;
}
}

/*Check if exist for apply limit table*/
function service_finder_is_exist_in_joblimit($providerid = 0){
global $wpdb,$service_finder_Tables;

$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->job_limits.' where `provider_id` = %d',$providerid));
if(!empty($row)){
	return true;
}else{
	return false;
}
}


/*Add class for verified providers*/
function service_finder_check_varified($providerid = 0){
	if(service_finder_is_varified_user($providerid)){
		return 'sf-approved';
	}else{
		return '';
	}
}

/*Add class for verified providers*/
function service_finder_check_varified_icon($providerid = 0){
	if(service_finder_is_varified_user($providerid)){
		if(service_finder_themestyle() == 'style-2' || service_finder_themestyle() == 'style-3'){
		$html = '<span class="sf-featured-approve"><i class="fa fa-check"></i><span>'.esc_html__('Verified Provider', 'service-finder').'</span></span>';
		}else{
		$html = '<span class="sf-average-question" data-tool="tooltip" data-placement="top" title="'.esc_html__('Verified Provider', 'service-finder').'">
               		<i class="fa fa-check-square-o"></i>
              	 </span>';
		}		 
		return $html;
	}else{
		return '';
	}
}

/*Check if is default view for search result style 1*/
function service_finder_is_default_view($view = "grid-4"){
global $service_finder_options;

$defaultview = (!empty($service_finder_options["default-view"])) ? esc_js($service_finder_options["default-view"]) : "grid-4";

	if($defaultview == $view){
		return 'class="active"';
	}else{
		return '';
	}
}

/*Check if is default view for search result style 2*/
function service_finder_is_default_view_style2($view = "grid-2"){
global $service_finder_options;

$defaultview = (!empty($service_finder_options["default-view-2"])) ? esc_js($service_finder_options["default-view-2"]) : "grid-2";

	if($defaultview == $view){
		return 'class="active"';
	}else{
		return '';
	}
}

/*Check if is default view for category page*/
function service_finder_is_default_view_category($view = "grid-4"){
global $service_finder_options;

$defaultview = (!empty($service_finder_options["category-default-view"])) ? esc_js($service_finder_options["category-default-view"]) : "grid-4";

	if($defaultview == $view){
		return 'class="active"';
	}else{
		return '';
	}
}

function service_finder_show_provider_meta($providerid = 0,$phone = '',$mobile = ''){
	global $service_finder_options;
	$contact = service_finder_get_contact_info_for_toltip($phone,$mobile);
	$reviewcount = show_review_at_search_result($providerid);
	if($contact == ""){
	$contact = esc_html__('Not Available', 'service-finder');
	}
	
	$showphone = '';
	if(service_finder_contact_number_is_accessible($providerid)){
	$showphone = '<button type="button" class="btn btn-border" data-tool="tooltip" data-placement="top" title="'.$contact.'"> <i class="fa fa-phone"></i> </button>';
	}
	
	$showreview = '';
	if($service_finder_options['review-system']){
	$showreview = '<button type="button" class="btn btn-border" data-tool="tooltip" data-placement="top" title="'.$reviewcount.'"> <i class="fa fa-commenting-o"></i> </button>';
	}
	
	$html = '<div class="btn-group sf-provider-tooltip" role="group" aria-label="Basic example">
			  '.$showphone.'
			  '.$showreview.'
			  '.show_request_quote_at_search_result($providerid).'
			</div>';
			
	return $html;		
}

/*Get available job apply limits*/
function service_finder_get_avl_job_limits($providerid = 0){
global $wpdb, $service_finder_options, $service_finder_Tables;

$availablelimit = 0;
$row = $wpdb->get_row('SELECT * FROM '.$service_finder_Tables->job_limits.' WHERE `provider_id` = "'.$providerid.'"');

if(!empty($row)){
$availablelimit = $row->available_limits;
}
return $availablelimit;
}

/*Get available job apply data*/
function service_finder_get_job_limits_data($providerid = 0){
global $wpdb, $service_finder_options, $service_finder_Tables;

$row = $wpdb->get_row('SELECT * FROM '.$service_finder_Tables->job_limits.' WHERE `provider_id` = "'.$providerid.'"');

if(!empty($row)){
return $row;
}else{
return '';
}

}

/*Get job apply limits current plan*/
function service_finder_get_current_plan($providerid = 0){
global $wpdb, $service_finder_options, $service_finder_Tables;

$current_plan = '';
$planname = esc_html__('No Plans', 'service-finder');
$row = $wpdb->get_row('SELECT * FROM '.$service_finder_Tables->job_limits.' WHERE `provider_id` = "'.$providerid.'"');

if(!empty($row)){
	$current_plan = $row->current_plan;
}

return $current_plan;
}

/**********************
Draw Review Box
**********************/
function service_finder_review_box($author = 0,$totalreview = 0){
	global $service_finder_options, $wpdb, $service_finder_Tables;
	
	$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('Provider', 'service-finder');
	
	$avgrating = service_finder_getAverageRating($author);
	
	$numberofstars = service_finder_number_of_stars($author);
	
	$allbookings = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' where provider_id = %d',$author));
	$totalbookings = count($allbookings);
	
	$completedbookings = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' where status = "Completed" AND provider_id = %d',$author));
	$totalcompleted = count($completedbookings);
	if($totalbookings > 0){
	$completionrate = ($totalcompleted/$totalbookings) * 100;
	}else{
	$completionrate = 0;
	}
	?>
    
    <div class="sf-stats-rating">
    <?php echo service_finder_displayRating($avgrating); ?>
    <div class="sf-average-reviews">
	<?php 
	if($avgrating > 1){
		printf( esc_html__('%.1f Stars - ', 'service-finder' ), $avgrating );
	}else{
		printf( esc_html__('%.1f Star - ', 'service-finder' ), $avgrating );
	}
	?>
	</div>
    <div class="sf-average-reviews">
	<?php 
	if($totalreview > 1){
		printf( esc_html__('%d Reviews', 'service-finder' ), $totalreview );
	}else{
		printf( esc_html__('%d Review', 'service-finder' ), $totalreview );
	}
	?>
	</div>
    <div class="sf-completion-rate">
        <div class="sf-rate-persent"><?php echo number_format((float)$completionrate,2,'.','').esc_html__('% Completion Rate', 'service-finder'); ?></div>
        <div class="sf-average-question" id="example" type="button" data-toggle="tooltip" data-placement="top" title="<?php echo sprintf( esc_html__('The percentage of accepted tasks this %s has completed', 'service-finder'), esc_html($providerreplacestring) ); ?>"><i class="fa fa-info-circle"></i></div>
    </div>
    <p class="sf-completed-tasks"><?php echo sprintf( esc_html__('%d Completed Task', 'service-finder'), esc_html($totalcompleted) ); ?></p>
</div>
	<div class="sf-reviews-summary">
    <div class="sf-reviews-row">
        <div class="sf-reviews-star">
            <i class="fa fa-star"></i>
            <i class="fa fa-star"></i>
            <i class="fa fa-star"></i>
            <i class="fa fa-star"></i>
            <i class="fa fa-star"></i>
        </div>
        <div class="sf-reviews-star-no"><?php echo esc_html($numberofstars[5]); ?></div>
    </div>
    <div class="sf-reviews-row">
        <div class="sf-reviews-star">
            <i class="fa fa-star"></i>
            <i class="fa fa-star"></i>
            <i class="fa fa-star"></i>
            <i class="fa fa-star"></i>
        </div>
        <div class="sf-reviews-star-no"><?php echo esc_html($numberofstars[4]); ?></div>
    </div>
    <div class="sf-reviews-row">
        <div class="sf-reviews-star">
            <i class="fa fa-star"></i>
            <i class="fa fa-star"></i>
            <i class="fa fa-star"></i>
        </div>
        <div class="sf-reviews-star-no"><?php echo esc_html($numberofstars[3]); ?></div>
    </div>
    <div class="sf-reviews-row">
        <div class="sf-reviews-star">
            <i class="fa fa-star"></i>
            <i class="fa fa-star"></i>
        </div>
        <div class="sf-reviews-star-no"><?php echo esc_html($numberofstars[2]); ?></div>
    </div>
    <div class="sf-reviews-row">
        <div class="sf-reviews-star">
            <i class="fa fa-star"></i>
        </div>
        <div class="sf-reviews-star-no"><?php echo esc_html($numberofstars[1]); ?></div>
    </div>
</div>
	<?php 
	$ratingstyle = (!empty($service_finder_options['rating-style'])) ? $service_finder_options['rating-style'] : '';
	if($ratingstyle == 'custom-rating'){ 
	service_finder_average_review_box($author,$totalreview); 
	}
	?>
	<?php
}	

function service_finder_average_review_box($author = 0,$totalreview = 0){
global $wpdb,$service_finder_Tables;

	$categoryid = get_user_meta($author,'primary_category',true);
	
	$labels = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->rating_labels.' where category_id = %d',$categoryid));
	$totallevel = count($labels);
	if(!empty($labels)){
		$i = 1;
		echo '<div class="sf-customer-avgrage-rating">';
		foreach($labels as $label){
		$avgrating = service_finder_getSpecialityAverageRating($author);
		$avgrating = $avgrating['avg_rating'.$i];
		?>
		<div class="sf-customer-rating-row clearfix">
        
            <div class="sf-customer-rating-name"><?php echo $label->label_name; ?></div>
            
            <div class="sf-customer-rating-count">
                <?php echo service_finder_displayRating($avgrating); ?>
            </div>
            
            <div class="sf-customer-rating-count-digits">
                <?php echo $avgrating; ?>
            </div>
            
            <div class="sf-customer-rating-smiley star-rating">
            	<?php
                if ($avgrating <= 1) {
					$iconclass = 'aon-icon-angry';
				} elseif($avgrating <= 2){
					$iconclass = 'aon-icon-cry';
				} elseif($avgrating <= 3){
					$iconclass = 'aon-icon-sad';
				} elseif($avgrating <= 4){
					$iconclass = 'aon-icon-happy';
				} elseif($avgrating <= 5){
					$iconclass = 'aon-icon-awesome';
				}
				?>
                <div class="caption"><span class="<?php echo sanitize_html_class($iconclass); ?>"></span></div>
            </div>
            
        </div>
		<?php
		$i++;
		}
		echo '</div>';
		}else{
		$labels = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->rating_labels.' where category_id = 0');
		$i = 1;
		
		$totallevel = count($labels);
		
		echo '<div class="sf-customer-avgrage-rating">';
		if(!empty($labels)){
		foreach($labels as $label){
		$avgrating = service_finder_getSpecialityAverageRating($author);
		$avgrating = $avgrating['avg_rating'.$i];
		?>
		<div class="sf-customer-rating-row clearfix">
        
            <div class="sf-customer-rating-name"><?php echo $label->label_name; ?></div>
            
            <div class="sf-customer-rating-count">
                <?php echo service_finder_displayRating($avgrating); ?>
            </div>
            
            <div class="sf-customer-rating-count-digits">
                <?php echo $avgrating; ?>
            </div>
            
            <div class="sf-customer-rating-smiley star-rating">
            	<?php
                if ($avgrating <= 1) {
					$iconclass = 'aon-icon-angry';
				} elseif($avgrating <= 2){
					$iconclass = 'aon-icon-cry';
				} elseif($avgrating <= 3){
					$iconclass = 'aon-icon-sad';
				} elseif($avgrating <= 4){
					$iconclass = 'aon-icon-happy';
				} elseif($avgrating <= 5){
					$iconclass = 'aon-icon-awesome';
				}
				?>
                <div class="caption"><span class="<?php echo sanitize_html_class($iconclass); ?>"></span></div>
            </div>
            
        </div>
		<?php
		$i++;
		}
		}else{
		echo '<div class="alert alert-danger">';
		echo esc_html__('Please set labels for custom rating','service-finder');
		echo '</div>';
		}
		echo '<input name="totallevel" value="'.$totallevel.'" type="hidden">';
		echo '</div>';
		}
}

function service_finder_review_box_style_4($author = 0,$totalreview = 0){
	global $service_finder_options, $wpdb, $service_finder_Tables;
	
	$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('Provider', 'service-finder');
	
	$avgrating = service_finder_getAverageRating($author);
	
	$numberofstars = service_finder_number_of_stars($author);
	
	$allbookings = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' where provider_id = %d',$author));
	$totalbookings = count($allbookings);
	
	$completedbookings = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' where status = "Completed" AND provider_id = %d',$author));
	$totalcompleted = count($completedbookings);
	if($totalbookings > 0){
	$completionrate = ($totalcompleted/$totalbookings) * 100;
	}else{
	$completionrate = 0;
	}
	?>
    
    <div class="sf-rating-outer sf-rating-outer-border clearfix">

      <div class="sf-rating-averages-wraps sf-rating-averages-new">
    
        <div class="sf-rating-averages-table">
    
          <div class="sf-rating-averages-cell">
    
            <div class="sf-rating-holder"> <?php echo service_finder_displayRating($avgrating); ?></div>
    
          </div>
    
          <div class="sf-rating-averages-cell">
    
            <div class="sf-reviews-row">
    
              <div class="sf-reviews-star"> <i class="fa fa-star"></i> <i class="fa fa-star"></i> <i class="fa fa-star"></i> <i class="fa fa-star"></i> <i class="fa fa-star"></i></div>
    
              <div class="sf-reviews-star-no"><?php echo esc_html($numberofstars[5]); ?></div>
    
            </div>
    
          </div>
    
        </div>
    
        <div class="sf-rating-averages-table">
    
          <div class="sf-rating-averages-cell">
    
            <div class="sf-average-rating&amp;review"><span>
            <?php 
			if($avgrating > 1){
				printf( esc_html__('%.1f Stars - ', 'service-finder' ), $avgrating );
			}else{
				printf( esc_html__('%.1f Star - ', 'service-finder' ), $avgrating );
			}
			?>
            </span> - <span>
            <?php 
			if($totalreview > 1){
				printf( esc_html__('%d Reviews', 'service-finder' ), $totalreview );
			}else{
				printf( esc_html__('%d Review', 'service-finder' ), $totalreview );
			}
			?>
            </span></div>
    
          </div>
    
          <div class="sf-rating-averages-cell">
    
            <div class="sf-reviews-row">
    
              <div class="sf-reviews-star"> <i class="fa fa-star"></i> <i class="fa fa-star"></i> <i class="fa fa-star"></i> <i class="fa fa-star"></i></div>
    
              <div class="sf-reviews-star-no"><?php echo esc_html($numberofstars[4]); ?></div>
    
            </div>
    
          </div>
    
        </div>
    
        <div class="sf-rating-averages-table">
    
          <div class="sf-rating-averages-cell">
    
            <div class="sf-completion-rate"> <span class="sf-rate-persent"><?php echo number_format((float)$completionrate,2,'.','').esc_html__('% Completion Rate', 'service-finder'); ?></span> <span class="sf-average-question" id="example" type="button" data-toggle="tooltip" data-placement="top" title="" data-original-title="<?php echo sprintf( esc_html__('The percentage of accepted tasks this %s has completed', 'service-finder'), esc_html($providerreplacestring) ); ?>"> <i class="fa fa-question-circle"></i> </span></div>
    
          </div>
    
          <div class="sf-rating-averages-cell">
    
            <div class="sf-reviews-row">
    
              <div class="sf-reviews-star"> <i class="fa fa-star"></i> <i class="fa fa-star"></i> <i class="fa fa-star"></i></div>
    
              <div class="sf-reviews-star-no"><?php echo esc_html($numberofstars[3]); ?></div>
    
            </div>
    
          </div>
    
        </div>
    
        <div class="sf-rating-averages-table">
    
          <div class="sf-rating-averages-cell"> <span class="sf-completed-tasks"><?php echo sprintf( esc_html__('%d Completed Task', 'service-finder'), esc_html($totalcompleted) ); ?></span></div>
    
          <div class="sf-rating-averages-cell">
    
            <div class="sf-reviews-row">
    
              <div class="sf-reviews-star"> <i class="fa fa-star"></i> <i class="fa fa-star"></i></div>
    
              <div class="sf-reviews-star-no"><?php echo esc_html($numberofstars[2]); ?></div>
    
            </div>
    
          </div>
    
        </div>
    
        <div class="sf-rating-averages-table">
    
          <div class="sf-rating-averages-cell"></div>
    
          <div class="sf-rating-averages-cell">
    
            <div class="sf-reviews-row">
    
              <div class="sf-reviews-star"> <i class="fa fa-star"></i></div>
    
              <div class="sf-reviews-star-no"><?php echo esc_html($numberofstars[1]); ?></div>
    
            </div>
    
          </div>
    
        </div>
    
      </div>
    
	  <?php 
	  $ratingstyle = (!empty($service_finder_options['rating-style'])) ? $service_finder_options['rating-style'] : '';
	  if($ratingstyle == 'custom-rating'){ 
	  service_finder_average_review_box_style4($author,$totalreview); 
	  }
	  ?>	    
    
    </div>
	
	<?php
}	

function service_finder_average_review_box_style4($author = 0,$totalreview = 0){
global $wpdb,$service_finder_Tables;

	$categoryid = get_user_meta($author,'primary_category',true);
	
	$labels = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->rating_labels.' where category_id = %d',$categoryid));
	$totallevel = count($labels);
	if(!empty($labels)){
		$i = 1;
		echo '<div class="sf-rating-categories-wraps sf-rating-categories-new">';
		foreach($labels as $label){
		$avgrating = service_finder_getSpecialityAverageRating($author);
		$avgrating = $avgrating['avg_rating'.$i];
		?>
        <div class="sf-rating-categories-table">
    
          <div class="sf-rating-categories-cell"><?php echo $label->label_name; ?></div>
    
          <div class="sf-rating-categories-cell">
    
            <div class="sf-reviews-row">
    
              <?php echo service_finder_displayRating($avgrating); ?>
    
              <div class="sf-reviews-star-no"><?php echo $avgrating; ?></div>
              
              <div class="sf-customer-rating-smiley star-rating">
            	<?php
                if ($avgrating <= 1) {
					$iconclass = 'aon-icon-angry';
				} elseif($avgrating <= 2){
					$iconclass = 'aon-icon-cry';
				} elseif($avgrating <= 3){
					$iconclass = 'aon-icon-sad';
				} elseif($avgrating <= 4){
					$iconclass = 'aon-icon-happy';
				} elseif($avgrating <= 5){
					$iconclass = 'aon-icon-awesome';
				}
				?>
                <div class="caption"><span class="<?php echo sanitize_html_class($iconclass); ?>"></span></div>
             </div>
    
            </div>
    
          </div>
    
        </div>
		<?php
		$i++;
		}
		echo '</div>';
		}else{
		$labels = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->rating_labels.' where category_id = 0');
		$i = 1;
		
		$totallevel = count($labels);
		
		echo '<div class="sf-rating-categories-wraps sf-rating-categories-new">';
		if(!empty($labels)){
		foreach($labels as $label){
		$avgrating = service_finder_getSpecialityAverageRating($author);
		$avgrating = $avgrating['avg_rating'.$i];
		?>
		<div class="sf-rating-categories-table">
    
          <div class="sf-rating-categories-cell"><?php echo $label->label_name; ?></div>
    
          <div class="sf-rating-categories-cell">
    
            <div class="sf-reviews-row">
    
              <?php echo service_finder_displayRating($avgrating); ?>
    
              <div class="sf-reviews-star-no"><?php echo $avgrating; ?></div>
              
              <div class="sf-customer-rating-smiley star-rating">
            	<?php
                if ($avgrating <= 1) {
					$iconclass = 'aon-icon-angry';
				} elseif($avgrating <= 2){
					$iconclass = 'aon-icon-cry';
				} elseif($avgrating <= 3){
					$iconclass = 'aon-icon-sad';
				} elseif($avgrating <= 4){
					$iconclass = 'aon-icon-happy';
				} elseif($avgrating <= 5){
					$iconclass = 'aon-icon-awesome';
				}
				?>
                <div class="caption"><span class="<?php echo sanitize_html_class($iconclass); ?>"></span></div>
            </div>
    
            </div>
    
          </div>
    
        </div>
		<?php
		$i++;
		}
		}else{
		echo '<div class="alert alert-danger">';
		echo esc_html__('Please set labels for custom rating','service-finder');
		echo '</div>';
		}
		echo '<input name="totallevel" value="'.$totallevel.'" type="hidden">';
		echo '</div>';
		}
}

/*Get average rating*/
function service_finder_getSpecialityAverageRating($providerid = 0){
global $wpdb,$service_finder_Tables,$service_finder_options;

	if($service_finder_options['review-style'] == 'booking-review'){
		$rating1 = 0;
		$rating2 = 0;
		$rating3 = 0;
		$rating4 = 0;
		$rating5 = 0;
		$avg_rating1 = 0;
		$avg_rating2 = 0;
		$avg_rating3 = 0;
		$avg_rating4 = 0;
		$avg_rating5 = 0;
		
		$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM `'.$service_finder_Tables->custom_rating.'` where `provider_id` = %d AND `feedbackid_id` > 0',$providerid));
		$total_comments = count($results);
		if(!empty($results)){
			foreach($results as $row){
				if(!empty($row)){
					$rating1 = $rating1 + $row->rating1;
					$rating2 = $rating2 + $row->rating2;
					$rating3 = $rating3 + $row->rating3;
					$rating4 = $rating4 + $row->rating4;
					$rating5 = $rating5 + $row->rating5;
				}
			}
			if($rating1 > 0){
			$avg_rating1 = $rating1/$total_comments;
			}
			if($rating2 > 0){
			$avg_rating2 = $rating2/$total_comments;

			}
			if($rating3 > 0){
			$avg_rating3 = $rating3/$total_comments;
			}
			if($rating4 > 0){
			$avg_rating4 = $rating4/$total_comments;
			}
			if($rating5 > 0){
			$avg_rating5 = $rating5/$total_comments;
			}
		}
		
		$ratingarr = array(
			'avg_rating1' => round($avg_rating1,1),
			'avg_rating2' => round($avg_rating2,1),
			'avg_rating3' => round($avg_rating3,1),
			'avg_rating4' => round($avg_rating4,1),
			'avg_rating5' => round($avg_rating5,1)
		);
		return $ratingarr;
	
	}elseif($service_finder_options['review-style'] == 'open-review'){
		$comment_postid = get_user_meta($providerid,'comment_post',true);
		$rating1 = 0;
		$rating2 = 0;
		$rating3 = 0;
		$rating4 = 0;
		$rating5 = 0;
		$avg_rating1 = 0;
		$avg_rating2 = 0;
		$avg_rating3 = 0;
		$avg_rating4 = 0;
		$avg_rating5 = 0;
		
		$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'comments WHERE `comment_approved` = 1 AND `comment_post_ID` = %d',$comment_postid));
		$total_comments = count($results);
		if(!empty($results)){
			foreach($results as $result){
			$comment_id = $result->comment_ID;
				$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM `'.$service_finder_Tables->custom_rating.'` where `comment_id` = %d',$comment_id));
				if(!empty($row)){
					$rating1 = $rating1 + $row->rating1;
					$rating2 = $rating2 + $row->rating2;
					$rating3 = $rating3 + $row->rating3;
					$rating4 = $rating4 + $row->rating4;
					$rating5 = $rating5 + $row->rating5;
				}
			}
			if($rating1 > 0){
			$avg_rating1 = $rating1/$total_comments;
			}
			if($rating2 > 0){
			$avg_rating2 = $rating2/$total_comments;
			}
			if($rating3 > 0){
			$avg_rating3 = $rating3/$total_comments;
			}
			if($rating4 > 0){
			$avg_rating4 = $rating4/$total_comments;
			}
			if($rating5 > 0){
			$avg_rating5 = $rating5/$total_comments;
			}
		}
		
		$ratingarr = array(
			'avg_rating1' => round($avg_rating1,1),
			'avg_rating2' => round($avg_rating2,1),
			'avg_rating3' => round($avg_rating3,1),
			'avg_rating4' => round($avg_rating4,1),
			'avg_rating5' => round($avg_rating5,1)
		);
		return $ratingarr;
	}

}

/**********************
Captcha Field
**********************/
add_action('wp_ajax_load_captcha_form', 'service_finder_load_captcha_form');
add_action('wp_ajax_nopriv_load_captcha_form', 'service_finder_load_captcha_form');
function service_finder_load_captcha_form(){
$success = array(
			'status' => 'success',
			'providersignup' => service_finder_captcha('providersignup'),
			'customersignup' => service_finder_captcha('customersignup')
			);
echo json_encode($success);
exit;

}

function service_finder_captcha($where = ''){
global $service_finder_options;

if($where == 'requestquote' || $where == 'requestquotepopup'){
	$chkcaptcha = ($service_finder_options['request-quote-captcha']) ? true : false;
}elseif($where == 'customersignup' || $where == 'customersignuppage'){
	$chkcaptcha = ($service_finder_options['customer-signup-captcha']) ? true : false;
}elseif($where == 'providersignup' || $where == 'providersignuppage'){
	$chkcaptcha = ($service_finder_options['provider-signup-captcha']) ? true : false;
}elseif($where == 'claimbusiness'){
	$chkcaptcha = ($service_finder_options['claim-business-captcha']) ? true : false;
}elseif($where == 'contactus'){
	$chkcaptcha = true;
}

$html = '';
if($chkcaptcha){

if(isset($service_finder_options['captcha-style']) && $service_finder_options['captcha-style'] == 'style-1'){
	$label = esc_html__('Can&#8217;t read the image? click %LINKSTART%here%LINKEND% to refresh.', 'service-finder'); 
	$label = str_replace('%LINKSTART%','<a href="javascript:;" data-where="'.$where.'" class="refreshCaptcha">',$label);
	$label = str_replace('%LINKEND%','</a>',$label);
		
	$html = '<div class="sf-captcha-wrap"><div class="col-md-12 margin-b-20"><img src="'.SERVICE_FINDER_BOOKING_LIB_URL.'/captcha.php?where='.$where.'&rand='.rand().'" id="captchaimg_'.$where.'">
	'.$label.'</div>
	<div class="col-md-12">
	<div class="form-group">
	  <div class="input-group"> <i class="input-group-addon fa fa-pencil"></i>
		<input name="captcha_code" id="captcha_code" type="text" class="form-control" placeholder="'.esc_html__("Enter the code above here", "service-finder").'">					
		<input type="hidden" name="captchaon" value="1">
	  </div>
	</div>
	</div></div>';
}else{
	$captchasitekey = (isset($service_finder_options['captcha-sitekey'])) ? esc_html($service_finder_options['captcha-sitekey']) : '';
	$captchatheme = (isset($service_finder_options['captcha-theme'])) ? esc_html($service_finder_options['captcha-theme']) : 'light';

	if($captchasitekey != ""){
	return '<div class="col-md-12">
	<div class="form-group">
	  <div class="input-group">
		<div class="captchaouter" id="recaptcha_'.$where.'" data-theme="'.$captchatheme.'" data-sitekey="'.$captchasitekey.'"></div>
	  </div>
	</div>
	</div>';
	}
}

}
return $html;
}

/*Run Before User Delete*/
add_action('load-users.php','service_finder_before_delete_user');
function service_finder_before_delete_user(){
	if (isset($_GET['action']) && 'delete' === $_GET['action']) {
	  $userid = (isset($_GET['user'])) ? $_GET['user'] : '';
	  if (isset($_GET['user'])) {
		global $wpdb, $service_finder_Errors, $service_finder_options, $paypal;
		
		$creds = array();
		$paypalCreds['USER'] = (isset($service_finder_options['paypal-username'])) ? $service_finder_options['paypal-username'] : '';
		$paypalCreds['PWD'] = (isset($service_finder_options['paypal-password'])) ? $service_finder_options['paypal-password'] : '';
		$paypalCreds['SIGNATURE'] = (isset($service_finder_options['paypal-signatue'])) ? $service_finder_options['paypal-signatue'] : '';
		$paypalType = (isset($service_finder_options['paypal-type']) && $service_finder_options['paypal-type'] == 'live') ? '' : 'sandbox.';
		
		$paypalTypeBool = (!empty($paypalType)) ? true : false;
		
		$paypal = new Paypal($paypalCreds,$paypalTypeBool);
		
		$subscription_id = get_user_meta($userid,'subscription_id',true);
		$cusID = get_user_meta($userid,'stripe_customer_id',true);
		$payment_mode = get_user_meta($userid,'payment_mode',true);
		$oldProfile = get_user_meta($userid,'recurring_profile_id',true);
		
		$msg = esc_html__('This user cannot be deleted. Due to subscription cancellation failed.', 'service-finder');
		
		
		if($subscription_id != "" && ($payment_mode == 'stripe' || $payment_mode == 'stripe_upgrade')){
		require_once(SERVICE_FINDER_PAYMENT_GATEWAY_DIR.'/stripe/init.php');
		
		if( isset($service_finder_options['stripe-type']) && $service_finder_options['stripe-type'] == 'test' ){
			$secret_key = (!empty($service_finder_options['stripe-test-secret-key'])) ? $service_finder_options['stripe-test-secret-key'] : '';
			$public_key = (!empty($service_finder_options['stripe-test-public-key'])) ? $service_finder_options['stripe-test-public-key'] : '';
		}else{
			$secret_key = (!empty($service_finder_options['stripe-live-secret-key'])) ? $service_finder_options['stripe-live-secret-key'] : '';
			$public_key = (!empty($service_finder_options['stripe-live-public-key'])) ? $service_finder_options['stripe-live-public-key'] : '';
		}
		
			\Stripe\Stripe::setApiKey($secret_key);
			try {			
		
				$currentcustomer = \Stripe\Customer::retrieve($cusID);
				$res = $currentcustomer->subscriptions->retrieve($subscription_id)->cancel();
				if($res->status == 'canceled'){
				
				service_finder_cancel_subscription($userid,'manually');
				}else{
					wp_die($msg);
				}
					
								
			} catch (Exception $e) {
			
				$body = $e->getJsonBody();
				$err  = $body['error'];
			
				wp_die($msg);
			}
		}elseif(!empty($oldProfile)) {
			$cancelParams = array(
				'PROFILEID' => $oldProfile,
				'ACTION' => 'Cancel'
			);
			$res = $paypal -> request('ManageRecurringPaymentsProfileStatus',$cancelParams);
			//echo '<pre>';print_r($res);echo '</pre>';
			if($res['ACK'] == 'Success'){
				service_finder_cancel_subscription($userid,'manually');
			}else{
				wp_die($msg);
			}
		}
	  }
	}
}

/*Update Job Limit*/
function service_finder_update_job_limit($userid = 0){
global $service_finder_options, $wpdb, $service_finder_Tables;
	$role = get_user_meta($userid,'provider_role',true);
	if ($role == "package_0" || $role == "package_1" || $role == "package_2" || $role == "package_3"){
	$packageNum = intval(substr($role, 8));
	
	$allowedjobapply = (!empty($service_finder_options['package'.$packageNum.'-job-apply'])) ? $service_finder_options['package'.$packageNum.'-job-apply'] : '';
	
	$period = (!empty($service_finder_options['job-apply-limit-period'])) ? $service_finder_options['job-apply-limit-period'] : '';
	$numberofweekmonth = (!empty($service_finder_options['job-apply-number-of-week-month'])) ? $service_finder_options['job-apply-number-of-week-month'] : 1;
	
	$startdate = date('Y-m-d h:i:s');
	
	if($period == 'weekly'){
		$freq = 7 * $numberofweekmonth;
		$expiredate = date('Y-m-d h:i:s', strtotime("+".$freq." days"));
	}elseif($period == 'monthly'){
		$freq = 30 * $numberofweekmonth;
		$expiredate = date('Y-m-d h:i:s', strtotime("+".$freq." days"));
	}
	
	$row = $wpdb->get_row('SELECT * FROM '.$service_finder_Tables->job_limits.' WHERE `provider_id` = "'.$userid.'"');

	if(!empty($row)){
	$available_limits = $row->available_limits + $allowedjobapply;
	}else{
	$available_limits = $allowedjobapply;
	}
	
	$data = array(
			'free_limits' => $allowedjobapply,
			'available_limits' => $available_limits,
			'membership_date' => $startdate,
			'start_date' => $startdate,
			'expire_date' => $expiredate,
			);
	$where = array(
			'provider_id' => $userid,
			);		
	
	$wpdb->update($service_finder_Tables->job_limits,wp_unslash($data),$where);
	}
}

/*Get job limit cycle*/
if ( ! function_exists( 'service_finder_get_schedule_cycle' ) ) {
function service_finder_get_schedule_cycle($bookingdate = '',$selecteddate = '',$freq = ''){
	
	if(date('Y-m-d',strtotime($bookingdate)) != date('Y-m-d',strtotime($selecteddate))){
		$daysBetween = $freq + 1;
		$start = new DateTime($bookingdate);                        // Meeting origination date
		$target = new DateTime($selecteddate);                       // The given date
		$daysApart = $start->diff($target)->days;
		$nextMultipleOfDaysBetweenAfterDaysApart = ceil($daysApart/$daysBetween) * $daysBetween;
		$dateOfNextMeeting = $start->modify('+' . $nextMultipleOfDaysBetweenAfterDaysApart . 'days');
		$dateOfNextMeeting->modify('-1 day');
		$nextdate = $dateOfNextMeeting->format('Y-m-d');
		
		$fromdate = $nextdate;
		$fromdate = DateTime::createFromFormat('Y-m-d',$fromdate);
		
		$fromdate->modify('-'.$freq.' day');
		$fromdate = $fromdate->format('Y-m-d');
		
		$arr = array(
			'startdate' => $fromdate,
			'expiredate' => $nextdate,
		);
		return $arr;
	}else{
		$fromdate = $selecteddate;
		$fromdate = DateTime::createFromFormat('Y-m-d',$fromdate);
		
		$fromdate->modify('+'.$freq.' day');
		$nextdate = $fromdate->format('Y-m-d');
		
		$arr = array(
			'startdate' => $selecteddate,
			'expiredate' => $nextdate,
		);
		return $arr;
	}
	
}
}

/*Get custom cities*/
function service_finder_get_cities($country = ''){
	global $wpdb, $service_finder_Tables; 
	
	$args = array(
	'hide_empty' => false,
	'meta_query' => array(
		array(
		   'key'       => 'country',
		   'value'     => $country,
		   'compare'   => 'LIKE'
		)
	),
	'taxonomy'  => 'sf-cities',
	);
	$cities = get_terms( $args );
	
	return $cities;
}

/*Create city term if not exists*/
function service_finder_create_city_term($cityslug = '',$countryname = ''){
	if(!term_exists($cityslug,'sf-cities')){
		$cityname = service_finder_get_cityname_by_slug($cityslug);
		$result = wp_insert_term($cityname, 'sf-cities');
		if(!empty($result))
		{
			$term_id  = service_finder_get_data($result,'term_id');
			update_term_meta($term_id, 'country', $countryname);
		}
	}
}

add_action('wp_ajax_load_cities_by_country', 'service_finder_cities_by_country');
add_action('wp_ajax_nopriv_load_cities_by_country', 'service_finder_cities_by_country');
function service_finder_cities_by_country(){
	global $wpdb, $service_finder_Tables; 
	
	$country = (isset($_POST['country'])) ? esc_html($_POST['country']) : '';
	
	$country =  str_replace("\'","'",$country);
	
	$args = array(
	'hide_empty' => false,
	'meta_query' => array(
		array(
		   'key'       => 'country',
		   'value'     => $country,
		   'compare'   => 'LIKE'
		)
	),
	'taxonomy'  => 'sf-cities',
	);
	$cities = get_terms( $args );

	$citydropdown = '<option value="">'.esc_html__('Select City', 'service-finder').'</option>';
	if(!empty($cities)){
		foreach($cities as $term){
			$citydropdown .= '<option value="'.esc_attr($term->slug).'">'.$term->name.'</option>';
		}
	}
	echo $citydropdown;
	exit;
}

/*Get card by country*/
function service_finder_get_cards($country = 'AR'){
	
	$cards = array();
	
	switch ($country) {
		case 'AR':
			$cards[] = 'MASTERCARD';
			$cards[] = 'AMEX';
			$cards[] = 'ARGENCARD';
			$cards[] = 'CABAL';
			$cards[] = 'NARANJA';
			$cards[] = 'CENCOSUD';
			$cards[] = 'SHOPPING';
			$cards[] = 'VISA';
			break;
		case 'BR':
			$cards[] = 'MASTERCARD';
			$cards[] = 'AMEX';
			$cards[] = 'VISA';
			$cards[] = 'DINERS';
			$cards[] = 'ELO';
			$cards[] = 'HIPERCARD';
			break;
		case 'CO':
			$cards[] = 'MASTERCARD';
			$cards[] = 'AMEX';
			$cards[] = 'CODENSA';
			$cards[] = 'DINERS';
			$cards[] = 'VISA';
			break;
		case 'MX':
			$cards[] = 'MASTERCARD';
			$cards[] = 'AMEX';
			$cards[] = 'VISA';
			break;
		case 'PA':
			$cards[] = 'MASTERCARD';
			break;
		case 'PE':
			$cards[] = 'MASTERCARD';
			$cards[] = 'AMEX';
			$cards[] = 'VISA';
			$cards[] = 'DINERS';
			break;		
	}
	
	return $cards;
}

/*Get Provider default avatar*/
function service_finder_get_default_avatar(){
global $service_finder_options;

$defaultavatar = (!empty($service_finder_options['default-avatar']['url'])) ? $service_finder_options['default-avatar']['url'] : '';

if($defaultavatar != ''){
	return $defaultavatar;
}else{
	return SERVICE_FINDER_BOOKING_IMAGE_URL.'/no_img_full.jpg';
}

}

/*Add http to url*/
function service_finder_addhttp($url = '') {
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://" . $url;
    }
    return $url;
}

/*Customer Signup*/
add_action( 'user_register', 'service_finder_customer_signup_hook', 10, 1 );
function service_finder_customer_signup_hook( $user_id = 0 ) {
global $wpdb, $service_finder_Tables, $service_finder_options;

if(service_finder_getUserRole($user_id) == 'Customer'){

$chkcustomer = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers_data.' where wp_user_id = %d',$user_id));
if(empty($chkcustomer)){
	$data = array(
			'wp_user_id' => $user_id,
			);
	
	$wpdb->insert($service_finder_Tables->customers_data,wp_unslash($data));
	
	$initialamount = 0;
	update_user_meta($userId,'_sf_wallet_amount',$initialamount);
	
	$allowedjobapply = (!empty($service_finder_options['default-job-post-limit'])) ? $service_finder_options['default-job-post-limit'] : '';
	
	$period = (!empty($service_finder_options['job-post-limit-period'])) ? $service_finder_options['job-post-limit-period'] : '';
	$numberofweekmonth = (!empty($service_finder_options['job-post-number-of-week-month'])) ? $service_finder_options['job-post-number-of-week-month'] : 1;
	
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
}
}

/*Generate OTP*/
add_action('wp_ajax_sendotp', 'service_finder_sendotp');
add_action('wp_ajax_nopriv_sendotp', 'service_finder_sendotp');
function service_finder_sendotp(){
	global $wpdb,$service_finder_options;
		
		$pass = rand(100000, 999999);
		
		if($service_finder_options['confirm-email-otp-to-provider'] != ""){
			$message = $service_finder_options['confirm-email-otp-to-provider'];
		}else{
			$message = esc_html__( 'Generated OTP is:', 'service-finder' ).' '.$pass;
		}
		
		$tokens = array('%OTP%');

		$replacements = array($pass);

		$msg_body = str_replace($tokens,$replacements,$message);

		if($service_finder_options['confirm-email-otp-to-provider-subject'] != ""){
			$msg_subject = $service_finder_options['confirm-email-otp-to-provider-subject'];
		}else{
			$msg_subject = esc_html__('One Time Password for confirm email id', 'service-finder');
		}
		
		if(service_finder_wpmailer($_POST['emailid'],$msg_subject,$msg_body)) {
			echo esc_html($pass);
		} else {
			echo esc_html($pass);
		}
	exit;
}

/*Check display basic profile or not after trial package expire*/
function service_finder_check_profile_after_trial_expire($uid = 0){
global $wpdb, $service_finder_options;

$display_profile_trialexpire = (isset($service_finder_options['basic-profile-after-trial-expire'])) ? esc_attr($service_finder_options['basic-profile-after-trial-expire']) : '';		
$trialpackage = get_user_meta($uid, 'trial_package', true);
$providerstatus = get_user_meta($uid, 'current_provider_status', true);		
		
if($trialpackage == 'yes' && $providerstatus == 'expire' && $display_profile_trialexpire == 'no'){
return false;
}else{
return true;
}
}

function service_finder_money_format($amount = '',$tag = ''){
global $service_finder_options;
if($tag != ""){
if($amount != ""){
return '<'.$tag.'>'.service_finder_currencysymbol().'</'.$tag.'> '.number_format((float)$amount,2,'.','');
}else{
return '<'.$tag.'>'.service_finder_currencysymbol().'</'.$tag.'> '.'0.00';
}
}

if($amount != ""){
return service_finder_currencysymbol().number_format((float)$amount,2,'.','');
}else{
return service_finder_currencysymbol().'0.00';
}
}

/*Get percentage format*/
function service_finder_percentage_format($value = 0){

$value = (floatval($value) > 0) ? $value : 0;
$result = $value.'%';

return $result;
}


/*Claim Business*/
add_action('wp_ajax_claim_business', 'service_finder_claim_business');
add_action('wp_ajax_nopriv_claim_business', 'service_finder_claim_business');
function service_finder_claim_business(){
	global $wpdb, $service_finder_Tables, $service_finder_options;
	
	$claim_business = (!empty($service_finder_options['string-claim-business'])) ? esc_html($service_finder_options['string-claim-business']) : esc_html__('Claim Business', 'service-finder');
	
	$provider_id = (!empty($_POST['provider_id'])) ? esc_html($_POST['provider_id']) : '';
	$customer_name = (!empty($_POST['customer_name'])) ? esc_html($_POST['customer_name']) : '';
	$customer_email = (!empty($_POST['customer_email'])) ? esc_html($_POST['customer_email']) : '';
	$description = (!empty($_POST['description'])) ? esc_html($_POST['description']) : '';
	$captchaon = (!empty($_POST['captchaon'])) ? esc_html($_POST['captchaon']) : '';
	$captcha_code = (!empty($_POST['captcha_code'])) ? esc_html($_POST['captcha_code']) : '';

	if($captchaon == 1){

	if((empty($_SESSION['captcha_code_claimbusiness'] ) || strcasecmp($_SESSION['captcha_code_claimbusiness'], $captcha_code) != 0) && (strcasecmp($_SESSION['captcha_code_claimbusiness'], $captcha_code) != 0 || empty($_SESSION['captcha_code_claimbusiness'] ))){  
		$error = array(
				'status' => 'error',
				'err_message' => esc_html__('The Validation code does not match!', 'service-finder'),
				);
		echo json_encode($error);
		exit;
	}

	}
	
	$data = array(
			'provider_id' => $provider_id,
			'date' => date('Y-m-d h:i:s'),
			'fullname' => $customer_name,
			'email' => $customer_email,
			'message' => $description,
			'status' => 'pending',
			);

	$wpdb->insert($service_finder_Tables->claim_business,wp_unslash($data));
	
	$claim_id = $wpdb->insert_id;
	
	$adminemail = get_option( 'admin_email' );
	
	if($service_finder_options['claimbusiness-to-admin-subject'] != ""){
		$subject = $service_finder_options['claimbusiness-to-admin-subject'];
	}else{
		$subject = $claim_business;
	}
	
	if(!empty($service_finder_options['claimbusiness-to-admin'])){
		$message = $service_finder_options['claimbusiness-to-admin'];
	}else{
		$message = $claim_business.' for following profile

		Provider Name: %PROVIDERNAME%
		
		Provider Email: %PROVIDEREMAIL%
		
		Provider Profile: %PROVIDERPROFILELINK%
		
		Customer Name: %CUSTOMERNAME%
		
		Email: %EMAIL%
		
		Description: %DESCRIPTION%';
	}
	
	$getProvider = new SERVICE_FINDER_searchProviders();
	$providerInfo = $getProvider->service_finder_getProviderInfo(esc_attr($provider_id));
	
	$userLink = service_finder_get_author_url($provider_id);
	
	$tokens = array('%PROVIDERNAME%','%PROVIDEREMAIL%','%PROVIDERPROFILELINK%','%CUSTOMERNAME%','%EMAIL%','%DESCRIPTION%');
	$replacements = array(service_finder_get_providername_with_link($provider_id),'<a href="mailto:'.$providerInfo->email.'">'.$providerInfo->email.'</a>',$userLink,$customer_name,$customer_email,$description);
	$msg_body = str_replace($tokens,$replacements,$message);
	
	service_finder_wpmailer($adminemail,$subject,$msg_body);
			
	if ( ! $claim_id ) {
		$error = array(
				'status' => 'error',
				'err_message' => sprintf(esc_html__('Couldn&#8217;t %s.', 'service-finder'),$claim_business)
				);
		echo json_encode($error);
	}else{
		$success = array(
				'status' => 'success',
				'suc_message' => sprintf(esc_html__('%s successfully. Pleast wait for approve', 'service-finder'),$claim_business)
				);
		echo json_encode($success);
	}
	exit;
}

/*Check availability method*/
function service_finder_availability_method($provider_id = 0){
global $service_finder_options;

$adminavailabilitybasedon = (!empty($service_finder_options['availability-based-on'])) ? esc_html($service_finder_options['availability-based-on']) : '';

$settings = service_finder_getProviderSettings($provider_id);

$availability_based_on = (!empty($settings['availability_based_on'])) ? $settings['availability_based_on'] : '';

if($adminavailabilitybasedon == 'timeslots' || ($adminavailabilitybasedon == 'both' && $availability_based_on == 'timeslots')){
	return 'timeslots';
}elseif($adminavailabilitybasedon == 'starttime' || ($adminavailabilitybasedon == 'both' && $availability_based_on == 'starttime')){
	return 'starttime';
}else{
	return 'timeslots';
}

}

/*Get offers method*/
function service_finder_offers_method($provider_id = 0){
global $service_finder_options;

$adminoffersbasedon = (!empty($service_finder_options['offers-based-on'])) ? esc_html($service_finder_options['offers-based-on']) : '';

$settings = service_finder_getProviderSettings($provider_id);

$offers_based_on = (!empty($settings['offers_based_on'])) ? $settings['offers_based_on'] : '';

if($adminoffersbasedon == 'services' || ($adminoffersbasedon == 'both' && $offers_based_on == 'services')){
	return 'services';
}elseif($adminoffersbasedon == 'booking' || ($adminoffersbasedon == 'both' && $offers_based_on == 'booking')){
	return 'booking';
}else{
	return 'services';
}

}

/*Check booking date method*/
function service_finder_booking_date_method($provider_id = 0){
global $service_finder_options;

$datestyle = (!empty($service_finder_options['booking-date-style'])) ? esc_html($service_finder_options['booking-date-style']) : '';

$settings = service_finder_getProviderSettings($provider_id);

$booking_date_based_on = (!empty($settings['booking_date_based_on'])) ? $settings['booking_date_based_on'] : '';

if($datestyle == 'singledate' || ($datestyle == 'both' && $booking_date_based_on == 'singledate')){
	return 'singledate';
}elseif($datestyle == 'multidate' || ($datestyle == 'both' && $booking_date_based_on == 'multidate')){
	return 'multidate';
}else{
	return 'singledate';
}

}

/*Get earch radius from given distance*/
function service_finder_radius_search($latitude = '',$longitude = '',$d = 0){
global $service_finder_options;

$radiussearchunit = (isset($service_finder_options['radius-search-unit'])) ? esc_attr($service_finder_options['radius-search-unit']) : 'mi';
		
if($radiussearchunit == 'km'){
$r = 6371; //earth's radius in km
}else{
$r = 3959; //earth's radius in miles
}



$latN = rad2deg(asin(sin(deg2rad($latitude)) * cos($d / $r)
        + cos(deg2rad($latitude)) * sin($d / $r) * cos(deg2rad(0))));

$latS = rad2deg(asin(sin(deg2rad($latitude)) * cos($d / $r)
        + cos(deg2rad($latitude)) * sin($d / $r) * cos(deg2rad(180))));

$lonE = rad2deg(deg2rad($longitude) + atan2(sin(deg2rad(90))
        * sin($d / $r) * cos(deg2rad($latitude)), cos($d / $r)
        - sin(deg2rad($latitude)) * sin(deg2rad($latN))));

$lonW = rad2deg(deg2rad($longitude) + atan2(sin(deg2rad(270))
        * sin($d / $r) * cos(deg2rad($latitude)), cos($d / $r)
        - sin(deg2rad($latitude)) * sin(deg2rad($latN))));


$radius = array(
			'latN' => $latN,
			'latS' => $latS,
			'lonE' => $lonE,
			'lonW' => $lonW,
			);
return $radius;			
}

/*Check address info crediantials*/
function service_finder_check_address_info_access(){
global $service_finder_options, $current_user;

$onlyregistereduser = (!empty($service_finder_options['only-registered-user'])) ? esc_html($service_finder_options['only-registered-user']) : '';

if($onlyregistereduser){
	if(is_user_logged_in() && ( service_finder_getUserRole($current_user->ID) == 'administrator' || service_finder_getUserRole($current_user->ID) == 'Customer' || service_finder_getUserRole($current_user->ID) == 'Provider' )){
		return true;
	}else{
		return false;
	}
}else{
	return true;
}
}

/*Translate Static Status Messages*/
function service_finder_translate_static_status_string($status = ''){
global $wpdb;

	switch (strtolower($status)) {
		case 'pending':
			$returnstatus = esc_html__('Pending','service-finder');
			break;
		case 'completed':
			$returnstatus = esc_html__('Completed','service-finder');
			break;
		case 'incomplete':
			$returnstatus = esc_html__('Incomplete','service-finder');
			break;	
		case 'cancel':
			$returnstatus = esc_html__('Cancelled','service-finder');
			break;
		case 'need-approval':
			$returnstatus = esc_html__('Need-Approval','service-finder');
			break;
		case 'free':
			$returnstatus = esc_html__('Free','service-finder');
			break;
		case 'paid':
			$returnstatus = esc_html__('Paid','service-finder');
			break;
		case 'overdue':
			$returnstatus = esc_html__('Overdue','service-finder');
			break;	
		case 'upcoming':
			$returnstatus = esc_html__('Upcoming','service-finder');
			break;	
		case 'past':
			$returnstatus = esc_html__('Past','service-finder');
			break;
		case 'stripe':
			$returnstatus = esc_html__('Stripe','service-finder');
			break;
		case 'paypal':
			$returnstatus = esc_html__('Paypal','service-finder');
			break;
		case 'wired':
			$returnstatus = esc_html__('Wired','service-finder');
			break;
		case 'bacs':
			$returnstatus = esc_html__('Bank Transfer','service-finder');
			break;
		case 'ppec_paypal':
			$returnstatus = esc_html__('Paypal Express Checkout','service-finder');
			break;
		case 'created':
			$returnstatus = esc_html__('Created','service-finder');
			break;	
		case 'failed':
			$returnstatus = esc_html__('Failed','service-finder');
			break;
		case 'publish':
			$returnstatus = esc_html__('Publish','service-finder');
			break;
		case 'pending_payment':
			$returnstatus = esc_html__('Pending_Payment','service-finder');
			break;
		case 'Pending payment':
			$returnstatus = esc_html__('Pending Payment','service-finder');
			break;
		case 'pending':
			$returnstatus = esc_html__('Pending','service-finder');
			break;
		case 'preview':
			$returnstatus = esc_html__('Preview','service-finder');
			break;
		case 'expired':
			$returnstatus = esc_html__('Expired','service-finder');
			break;	
		case 'draft':
			$returnstatus = esc_html__('Draft','service-finder');
			break;	
		default:
			$returnstatus = ucfirst($status);
			break;												
	}
	
	return $returnstatus;

}

/*Send Booking Reminder mail to provider*/
function service_finder_SendBookingReminderMailToProvider($maildata = array()){
	global $service_finder_options, $service_finder_Tables, $wpdb;
	
	$providerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$maildata['provider_id']));
	$customerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `id` = %d',$maildata['booking_customer_id']));
	
	$bookingpayment_mode = (!empty($maildata['type'])) ? $maildata['type'] : '';
	
	$payent_mode = ($bookingpayment_mode != '') ? $bookingpayment_mode : 'free';
	$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? $service_finder_options['pay_booking_amount_to'] : '';
	
	$message = '';
	
	if(!empty($service_finder_options['booking-reminder-to-provider'])){
		$message .= $service_finder_options['booking-reminder-to-provider'];
	}else{
		$message .= '
<h4>Booking Details</h4>
Date: %DATE%
			
			Time: %STARTTIME% - %ENDTIME%
			
			Member Name: %MEMBERNAME%
<h4>Provider Details</h4>
Provider Name: %PROVIDERNAME%

			Provider Email: %PROVIDEREMAIL%
			
			Phone: %PROVIDERPHONE%
<h4>Customer Details</h4>
Customer Name: %CUSTOMERNAME%

Customer Email: %CUSTOMEREMAIL%

Phone: %CUSTOMERPHONE%

Alternate Phone: %CUSTOMERPHONE2%

Address: %ADDRESS%

Apt/Suite: %APT%

City: %CITY%

State: %STATE%

Postal Code: %ZIPCODE%

Country: %COUNTRY%

Services: %SERVICES%

<h4>Payment Details</h4>
Pay Via: %PAYMENTMETHOD%
			
			Amount: %AMOUNT%';
	}
		
		$tokens = array('%DATE%','%STARTTIME%','%ENDTIME%','%MEMBERNAME%','%PROVIDERNAME%','%PROVIDEREMAIL%','%PROVIDERPHONE%','%CUSTOMERNAME%','%CUSTOMEREMAIL%','%CUSTOMERPHONE%','%CUSTOMERPHONE2%','%ADDRESS%','%APT%','%CITY%','%STATE%','%ZIPCODE%','%COUNTRY%','%SERVICES%','%PAYMENTMETHOD%','%AMOUNT%','%SHORTDESCRIPTION%','%SERVICELOCATION%','%BOOKINGREFID%');
		
		if($maildata['member_id'] > 0){
		$membername = service_finder_getMemberName($maildata['member_id']);
		}else{
		$membername = '-';
		}
		
		$services = service_finder_get_booking_services($maildata['id']);
		
		$charge_admin_fee = (!empty($service_finder_options['charge-admin-fee'])) ? $service_finder_options['charge-admin-fee'] : '';
		$charge_admin_fee_from = (!empty($service_finder_options['charge-admin-fee-from'])) ? $service_finder_options['charge-admin-fee-from'] : '';
		
		if($charge_admin_fee_from == 'provider' && $pay_booking_amount_to == 'admin' && $charge_admin_fee){
		$bookingamount = $maildata['total'] - $adminfee;
		}elseif($charge_admin_fee_from == 'customer' && $charge_admin_fee && $pay_booking_amount_to == 'admin'){
		$bookingamount = $maildata['total'];
		}else{
		$bookingamount = $maildata['total'];
		}
		
		$replacements = array(service_finder_date_format($maildata['date']),service_finder_time_format($maildata['start_time']),service_finder_time_format(service_finder_get_booking_end_time($maildata['end_time'],$maildata['end_time_no_buffer'])),$membername,service_finder_get_providername_with_link($providerInfo->wp_user_id),$providerInfo->email,service_finder_get_contact_info($providerInfo->phone,$providerInfo->mobile),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country,$services,ucfirst($payent_mode),service_finder_money_format($bookingamount),$customerInfo->description,service_finder_get_service_location($maildata['id']),$maildata['id']);
		$msg_body = str_replace($tokens,$replacements,$message);
		
		if($service_finder_options['booking-reminder-to-provider-subject'] != ""){
			$msg_subject = $service_finder_options['booking-reminder-to-provider-subject'];
		}else{
			$msg_subject = esc_html__('Booking Reminder Notification', 'service-finder');
		}
		
		if(service_finder_wpmailer($providerInfo->email,$msg_subject,$msg_body)) {

			$success = array(
					'status' => 'success',
					'suc_message' => esc_html__('Message has been sent', 'service-finder'),
					);
			$service_finder_Success = json_encode($success);
			return $service_finder_Success;
			
			
		} else {
				
			$error = array(
					'status' => 'error',
					'err_message' => esc_html__('Message could not be sent.', 'service-finder'),
					);
			$service_finder_Errors = json_encode($error);
			return $service_finder_Errors;
		}
	
}
/*Send Booking Reminder mail to customer*/
function service_finder_SendBookingReminderMailToCustomer($maildata = array()){
	global $service_finder_options, $service_finder_Tables, $wpdb;
	$providerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$maildata['provider_id']));
	$customerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `id` = %d',$maildata['booking_customer_id']));
	
	$bookingpayment_mode = (!empty($maildata['type'])) ? $maildata['type'] : '';
	
	$payent_mode = ($bookingpayment_mode != '') ? $bookingpayment_mode : 'free';
	
	$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? $service_finder_options['pay_booking_amount_to'] : '';
	
	$message = '';
	
	if(!empty($service_finder_options['booking-reminder-to-customer'])){
		$message .= $service_finder_options['booking-reminder-to-customer'];
	}else{
		$message .= '
<h4>Booking Details</h4>
Date: %DATE%
			
			Time: %STARTTIME% - %ENDTIME%
			
			Member Name: %MEMBERNAME%
<h4>Provider Details</h4>
Provider Name: %PROVIDERNAME%

			Provider Email: %PROVIDEREMAIL%
			
			Phone: %PROVIDERPHONE%
<h4>Customer Details</h4>
Customer Name: %CUSTOMERNAME%

Customer Email: %CUSTOMEREMAIL%

Phone: %CUSTOMERPHONE%

Alternate Phone: %CUSTOMERPHONE2%

Address: %ADDRESS%

Apt/Suite: %APT%

City: %CITY%

State: %STATE%

Postal Code: %ZIPCODE%

Country: %COUNTRY%

Services: %SERVICES%

<h4>Payment Details</h4>
Pay Via: %PAYMENTMETHOD%
			
			Amount: %AMOUNT%';
	}
	
		$tokens = array('%DATE%','%STARTTIME%','%ENDTIME%','%MEMBERNAME%','%PROVIDERNAME%','%PROVIDEREMAIL%','%PROVIDERPHONE%','%CUSTOMERNAME%','%CUSTOMEREMAIL%','%CUSTOMERPHONE%','%CUSTOMERPHONE2%','%ADDRESS%','%APT%','%CITY%','%STATE%','%ZIPCODE%','%COUNTRY%','%SERVICES%','%PAYMENTMETHOD%','%AMOUNT%','%SHORTDESCRIPTION%','%SERVICELOCATION%','%BOOKINGREFID%');
		
		if($maildata['member_id'] > 0){
		$membername = service_finder_getMemberName($maildata['member_id']);
		}else{
		$membername = '-';
		}
		$services = service_finder_get_booking_services($maildata['id']);
		
		$charge_admin_fee_from = (!empty($service_finder_options['charge-admin-fee-from'])) ? $service_finder_options['charge-admin-fee-from'] : '';
		$charge_admin_fee = (!empty($service_finder_options['charge-admin-fee'])) ? $service_finder_options['charge-admin-fee'] : '';
		
		if($charge_admin_fee_from == 'provider' && $charge_admin_fee && $pay_booking_amount_to == 'admin'){
		$adminfee = '0.0';
		}
		
		$replacements = array(service_finder_date_format($maildata['date']),service_finder_time_format($maildata['start_time']),service_finder_time_format(service_finder_get_booking_end_time($maildata['end_time'],$maildata['end_time_no_buffer'])),$membername,service_finder_get_providername_with_link($providerInfo->wp_user_id),$providerInfo->email,service_finder_get_contact_info($providerInfo->phone,$providerInfo->mobile),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country,$services,ucfirst($payent_mode),service_finder_money_format($maildata['total']),$customerInfo->description,service_finder_get_service_location($maildata['id']),$maildata['id']);
		$msg_body = str_replace($tokens,$replacements,$message);

		if($service_finder_options['booking-reminder-to-customer-subject'] != ""){
			$msg_subject = $service_finder_options['booking-reminder-to-customer-subject'];
		}else{
			$msg_subject = esc_html__('Booking Reminder Notification', 'service-finder');

		}
		
		if(service_finder_wpmailer($customerInfo->email,$msg_subject,$msg_body)) {

			$success = array(
					'status' => 'success',
					'suc_message' => esc_html__('Message has been sent', 'service-finder'),
					);
			$service_finder_Success = json_encode($success);
			return $service_finder_Success;
			
			
		} else {
				
			$error = array(
					'status' => 'error',
					'err_message' => esc_html__('Message could not be sent.', 'service-finder'),
					);
			$service_finder_Errors = json_encode($error);
			return $service_finder_Errors;
		}
	
}
/*Send Booking Reminder mail to admin*/
function service_finder_SendBookingReminderMailToAdmin($maildata = array()){
	global $service_finder_options, $wpdb, $service_finder_Tables;
	$providerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$maildata['provider_id']));
	$customerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `id` = %d',$maildata['booking_customer_id']));
	
	$bookingpayment_mode = (!empty($maildata['type'])) ? $maildata['type'] : '';
	
	$payent_mode = ($bookingpayment_mode != '') ? $bookingpayment_mode : 'free';
	$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? $service_finder_options['pay_booking_amount_to'] : '';
	
	$message = '';
	if(!empty($service_finder_options['booking-reminder-to-admin'])){
		$message .= $service_finder_options['booking-reminder-to-admin'];
	}else{
		$message .= '
<h4>Booking Details</h4>
Date: %DATE%
			
			Time: %STARTTIME% - %ENDTIME%
			
			Member Name: %MEMBERNAME%
<h4>Provider Details</h4>
Provider Name: %PROVIDERNAME%

			Provider Email: %PROVIDEREMAIL%
			
			Phone: %PROVIDERPHONE%
<h4>Customer Details</h4>
Customer Name: %CUSTOMERNAME%

Customer Email: %CUSTOMEREMAIL%

Phone: %CUSTOMERPHONE%

Alternate Phone: %CUSTOMERPHONE2%

Address: %ADDRESS%

Apt/Suite: %APT%

City: %CITY%

State: %STATE%

Postal Code: %ZIPCODE%

Country: %COUNTRY%

Services: %SERVICES%


<h4>Payment Details</h4>
Pay Via: %PAYMENTMETHOD%
			
			Amount: %AMOUNT%';
	}
		
		$tokens = array('%DATE%','%STARTTIME%','%ENDTIME%','%MEMBERNAME%','%PROVIDERNAME%','%PROVIDEREMAIL%','%PROVIDERPHONE%','%CUSTOMERNAME%','%CUSTOMEREMAIL%','%CUSTOMERPHONE%','%CUSTOMERPHONE2%','%ADDRESS%','%APT%','%CITY%','%STATE%','%ZIPCODE%','%COUNTRY%','%SERVICES%','%PAYMENTMETHOD%','%AMOUNT%','%SHORTDESCRIPTION%','%SERVICELOCATION%','%BOOKINGREFID%');
		
		if($maildata['member_id'] > 0){
		$membername = service_finder_getMemberName($maildata['member_id']);
		}else{
		$membername = '-';
		}
		$services = service_finder_get_booking_services($maildata['id']);
		
		$charge_admin_fee = (!empty($service_finder_options['charge-admin-fee'])) ? $service_finder_options['charge-admin-fee'] : '';
		$charge_admin_fee_from = (!empty($service_finder_options['charge-admin-fee-from'])) ? $service_finder_options['charge-admin-fee-from'] : '';
		
		if($charge_admin_fee_from == 'provider' && $charge_admin_fee && $pay_booking_amount_to == 'admin'){
		$bookingamount = $maildata['total'] - $adminfee;
		}elseif($charge_admin_fee_from == 'customer' && $charge_admin_fee && $pay_booking_amount_to == 'admin'){
		$bookingamount = $maildata['total'];
		}else{
		$bookingamount = $maildata['total'];
		$adminfee = '0.0';
		}
		
		$replacements = array(service_finder_date_format($maildata['date']),service_finder_time_format($maildata['start_time']),service_finder_time_format(service_finder_get_booking_end_time($maildata['end_time'],$maildata['end_time_no_buffer'])),$membername,service_finder_get_providername_with_link($providerInfo->wp_user_id),$providerInfo->email,service_finder_get_contact_info($providerInfo->phone,$providerInfo->mobile),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country,$services,ucfirst($payent_mode),service_finder_money_format($bookingamount),$customerInfo->description,service_finder_get_service_location($maildata['id']),$maildata['id']);
		$msg_body = str_replace($tokens,$replacements,$message);
		
		if($service_finder_options['booking-reminder-to-admin-subject'] != ""){
			$msg_subject = $service_finder_options['booking-reminder-to-admin-subject'];
		}else{
			$msg_subject = esc_html__('Booking Reminder Notification', 'service-finder');
		}
		
		if(service_finder_wpmailer(get_option('admin_email'),$msg_subject,$msg_body)) {

			$success = array(
					'status' => 'success',
					'suc_message' => esc_html__('Message has been sent', 'service-finder'),
					);
			$service_finder_Success = json_encode($success);
			return $service_finder_Success;
			
			
		} else {
				
			$error = array(
					'status' => 'error',
					'err_message' => esc_html__('Message could not be sent.', 'service-finder'),
					);
			$service_finder_Errors = json_encode($error);
			return $service_finder_Errors;
		}
	
}

/*Set Social Cookie*/
add_action('wp_ajax_set_social_cookie', 'service_finder_set_social_cookie');
add_action('wp_ajax_nopriv_set_social_cookie', 'service_finder_set_social_cookie');
function service_finder_set_social_cookie(){
global $wpdb, $service_finder_Tables;
	unset($_SESSION['social_account_role']);
	$target = (isset($_POST['target'])) ? esc_html($_POST['target']) : '';
	$target = ltrim($target,'#');
	
	if($target == "tab1" || $target == "customertab"){
		$_SESSION['social_account_role'] = "customer";
	}elseif($target == "tab2" || $target == "providertab"){
		$_SESSION['social_account_role'] = "provider";
	}
	exit(0);
}

/*Check display basic feature after social login*/
function service_finder_check_display_features_after_social_login($provider_id = 0){
global $service_finder_options, $current_user;

$socialaccount = get_user_meta($provider_id,'social_provider',true);
$providerrole = get_user_meta($provider_id,'provider_role',true);

if($socialaccount != "" && $providerrole == ""){

$showfeatures = (!empty($service_finder_options['display-basicfeature-after-sociallogin'])) ? esc_html($service_finder_options['display-basicfeature-after-sociallogin']) : '';
	
	if($showfeatures){
		return true;
	}else{
		return false;
	}
}else{
	return true;
}

}

/*Add to google calendar*/
function service_finder_addto_google_calendar($booking_id = 0,$provider_id = 0){
session_start();
global $service_finder_options, $current_user,  $service_finder_Tables, $wpdb;
		$flag = 0;
		require_once SERVICE_FINDER_BOOKING_LIB_DIR.'/google-api-php-client/src/Google/autoload.php';
		
		$gcal_creds = service_finder_get_gcal_cred();
		$google_client_id = $gcal_creds['client_id'];
		$google_client_secret = $gcal_creds['client_secret'];
		$google_calendar_id = get_user_meta($provider_id,'google_calendar_id',true);
		
		$client = new Google_Client();
		$client->setClientId($google_client_id);
		$client->setClientSecret($google_client_secret);
		$redirect_uri = add_query_arg( array('action' => 'googleoauth-callback'), home_url() );
		$client->setRedirectUri($redirect_uri);
		$client->setScopes('https://www.googleapis.com/auth/calendar');
		
		if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
			$client->setAccessToken($_SESSION['access_token']);
			$flag = 1;
		}elseif(service_finder_get_gcal_access_token($provider_id) != ""){
			$client->setAccessToken(service_finder_get_gcal_access_token($provider_id));
			$flag = 1;
		}
		
		if($client->isAccessTokenExpired()) {
			 try{
			 
			 if(isset($_SESSION['access_token']) && $_SESSION['access_token']) {
			  $newaccesstoken = json_decode($_SESSION['access_token']);
			  $client->refreshToken($newaccesstoken->refresh_token);
			
			 }elseif(service_finder_get_gcal_access_token($provider_id) != ""){
			  $newaccesstoken = json_decode(service_finder_get_gcal_access_token($provider_id));
			  $client->refreshToken($newaccesstoken->refresh_token);
			 }
			 
			 } catch (Exception $e) {
				
			 }
	
		 }
		
		if($flag == 1){
			$bookingdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `id` = %d',$booking_id));
			$customerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `id` = %d',$bookingdata->booking_customer_id));
			$offset = 0;
			
			$bookingtitle = (!empty($service_finder_options['google-calendar-booking-title'])) ? $service_finder_options['google-calendar-booking-title'] : esc_html__('Service Finder Booking', 'service-finder');
			
			try{
			
			if($bookingdata->multi_date == 'yes')
			{
				$bookedservices = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->booked_services.' WHERE `booking_id` = %d',$booking_id));
				
				if(!empty($bookedservices))
				{
					foreach($bookedservices as $bookedservice)
					{
						$str_date = strtotime($bookedservice->date.' '.$bookedservice->start_time);
						$dateTimeS = service_finder_date_format_RFC3339($str_date, $offset);
						if($bookingdata->end_time != ""){
						$str_date = strtotime($bookedservice->date.' '.$bookedservice->end_time);
						}else{
						$str_date = strtotime($bookedservice->date.' '.$bookedservice->start_time);
						}
						$dateTimeE = service_finder_date_format_RFC3339($str_date, $offset);
						$address = $customerInfo->apt.' '.$customerInfo->address.' '.$customerInfo->city.' '.$customerInfo->country;
						
						if(get_option('timezone_string') != ""){
						$timezone = get_option('timezone_string');
						}
						
						$tokens = array('%CUSTOMERNAME%','%CUSTOMEREMAIL%');
						
						$replacements = array($customerInfo->name,$customerInfo->email);
						
						$bookingtitle = str_replace($tokens,$replacements,$bookingtitle);
								
						$event = new Google_Service_Calendar_Event(array(
						  'summary' => $bookingtitle,
						  'location' => $address,
						  'description' => sprintf(esc_html__('Booking Made by %s', 'service-finder'),$customerInfo->name),
						  'start' => array(
							'dateTime' => $dateTimeS,
							'timeZone' => $timezone,
						  ),
						  'end' => array(
							'dateTime' => $dateTimeE,
							'timeZone' => $timezone,
						  ),
						  'attendees' => array(
							array('email' => $customerInfo->email)
						  ),
						));
						
						$calendarId = $google_calendar_id;
						$cal = new Google_Service_Calendar($client);
						$event = $cal->events->insert($calendarId, $event);
						$bookdata = array(
								'gcal_booking_url' => $event->htmlLink, 
								'gcal_booking_id' => $event->id, 
								);
								
						$where = array(
								'id' => $bookedservice->id 
								);		
				
						$wpdb->update($service_finder_Tables->booked_services,wp_unslash($bookdata),$where);
					}
				}
			}else{
			
				$str_date = strtotime($bookingdata->date.' '.$bookingdata->start_time);
				$dateTimeS = service_finder_date_format_RFC3339($str_date, $offset);
				if($bookingdata->end_time != ""){
				$str_date = strtotime($bookingdata->date.' '.$bookingdata->end_time);
				}else{
				$str_date = strtotime($bookingdata->date.' '.$bookingdata->start_time);
				}
				$dateTimeE = service_finder_date_format_RFC3339($str_date, $offset);
				$address = $customerInfo->apt.' '.$customerInfo->address.' '.$customerInfo->city.' '.$customerInfo->country;
				
				if(get_option('timezone_string') != ""){
				$timezone = get_option('timezone_string');
				}
				
				$tokens = array('%CUSTOMERNAME%','%CUSTOMEREMAIL%');
				
				$replacements = array($customerInfo->name,$customerInfo->email);
				
				$bookingtitle = str_replace($tokens,$replacements,$bookingtitle);
						
				$event = new Google_Service_Calendar_Event(array(
				  'summary' => $bookingtitle,
				  'location' => $address,
				  'description' => sprintf(esc_html__('Booking Made by %s', 'service-finder'),$customerInfo->name),
				  'start' => array(
					'dateTime' => $dateTimeS,
					'timeZone' => $timezone,
				  ),
				  'end' => array(
					'dateTime' => $dateTimeE,
					'timeZone' => $timezone,
				  ),
				  'attendees' => array(
					array('email' => $customerInfo->email)
				  ),
				));
				
				$calendarId = $google_calendar_id;
				$cal = new Google_Service_Calendar($client);
				$event = $cal->events->insert($calendarId, $event);
				$bookdata = array(
						'gcal_booking_url' => $event->htmlLink, 
						'gcal_booking_id' => $event->id, 
						);
						
				$where = array(
						'id' => $booking_id 
						);		
		
				$wpdb->update($service_finder_Tables->bookings,wp_unslash($bookdata),$where);
			}
			
			} catch (Exception $e) {
			//echo '<pre>';print_r($e);
			}
			return true;
		}
}

/*Update to google calendar*/
function service_finder_updateto_google_calendar($booking_id = 0,$provider_id = 0){
session_start();
global $service_finder_options, $current_user,  $service_finder_Tables, $wpdb;
		$flag = 0;
		require_once SERVICE_FINDER_BOOKING_LIB_DIR.'/google-api-php-client/src/Google/autoload.php';
		
		$gcal_creds = service_finder_get_gcal_cred();
		$google_client_id = $gcal_creds['client_id'];
		$google_client_secret = $gcal_creds['client_secret'];
		$google_calendar_id = get_user_meta($provider_id,'google_calendar_id',$google_calendar_id);
		
		$client = new Google_Client();
		$client->setClientId($google_client_id);
		$client->setClientSecret($google_client_secret);
		$redirect_uri = add_query_arg( array('action' => 'googleoauth-callback'), home_url() );
		$client->setRedirectUri($redirect_uri);
		$client->setScopes('https://www.googleapis.com/auth/calendar');
		
		if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
			$client->setAccessToken($_SESSION['access_token']);
			$flag = 1;
		}elseif(service_finder_get_gcal_access_token($provider_id) != ""){
			$client->setAccessToken(service_finder_get_gcal_access_token($provider_id));
			$flag = 1;
		}
		
		if($client->isAccessTokenExpired()) {
			 try{
			 
			 if(isset($_SESSION['access_token']) && $_SESSION['access_token']) {
			  $newaccesstoken = json_decode($_SESSION['access_token']);
			  $client->refreshToken($newaccesstoken->refresh_token);
			
			 }elseif(service_finder_get_gcal_access_token($provider_id) != ""){
			  $newaccesstoken = json_decode(service_finder_get_gcal_access_token($provider_id));
			  $client->refreshToken($newaccesstoken->refresh_token);
			 }
			 
			 } catch (Exception $e) {
				
			 }
	
		 }
		
		if($flag == 1){
			$bookingdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `id` = %d',$booking_id));
			$customerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `id` = %d',$bookingdata->booking_customer_id));
			$offset = 0;
			
			try{
			
			if($bookingdata->multi_date == 'yes')
			{
				$bookedservices = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->booked_services.' WHERE `booking_id` = %d',$booking_id));
				
				if(!empty($bookedservices))
				{
					foreach($bookedservices as $bookedservice)
					{
						$str_date = strtotime($bookedservice->date.' '.$bookedservice->start_time);
						$dateTimeS = service_finder_date_format_RFC3339($str_date, $offset);
						if($bookingdata->end_time != ""){
						$str_date = strtotime($bookedservice->date.' '.$bookedservice->end_time);
						}else{
						$str_date = strtotime($bookedservice->date.' '.$bookedservice->start_time);
						}
						$dateTimeE = service_finder_date_format_RFC3339($str_date, $offset);
						
						if(get_option('timezone_string') != ""){
						$timezone = get_option('timezone_string');
						}
						
						$calendarId = $google_calendar_id;
						$cal = new Google_Service_Calendar($client);
						$event = $cal->events->get($calendarId,$bookingdata->gcal_booking_id);
						
						$start = new Google_Service_Calendar_EventDateTime();
						$start->setDateTime($dateTimeS);
						$event->setStart($start);
						
						$end = new Google_Service_Calendar_EventDateTime();
						$end->setDateTime($dateTimeE);
						$event->setEnd($end);
						
						$updatedEvent = $cal->events->update($calendarId, $event->getId(), $event);
			
						$updatedEvent->getUpdated();
					}
				}
			}else{
			
				$str_date = strtotime($bookingdata->date.' '.$bookingdata->start_time);
				$dateTimeS = service_finder_date_format_RFC3339($str_date, $offset);
				if($bookingdata->end_time != ""){
				$str_date = strtotime($bookingdata->date.' '.$bookingdata->end_time);
				}else{
				$str_date = strtotime($bookingdata->date.' '.$bookingdata->start_time);
				}
				$dateTimeE = service_finder_date_format_RFC3339($str_date, $offset);
				
				if(get_option('timezone_string') != ""){
				$timezone = get_option('timezone_string');
				}
				
				$calendarId = $google_calendar_id;
				$cal = new Google_Service_Calendar($client);
				$event = $cal->events->get($calendarId,$bookingdata->gcal_booking_id);
				
				$start = new Google_Service_Calendar_EventDateTime();
				$start->setDateTime($dateTimeS);
				$event->setStart($start);
				
				$end = new Google_Service_Calendar_EventDateTime();
				$end->setDateTime($dateTimeE);
				$event->setEnd($end);
				
				$updatedEvent = $cal->events->update($calendarId, $event->getId(), $event);
	
				$updatedEvent->getUpdated();
			}

			} catch (Exception $e) {
			//echo '<pre>';print_r($e);
			}
			return true;
		}
}

/*Cancel to google calendar*/
function service_finder_cancelto_google_calendar($booking_id = 0,$provider_id = 0){
session_start();
global $service_finder_options, $current_user,  $service_finder_Tables, $wpdb;
		$flag = 0;
		require_once SERVICE_FINDER_BOOKING_LIB_DIR.'/google-api-php-client/src/Google/autoload.php';
		
		$gcal_creds = service_finder_get_gcal_cred();
		$google_client_id = $gcal_creds['client_id'];
		$google_client_secret = $gcal_creds['client_secret'];
		$google_calendar_id = get_user_meta($provider_id,'google_calendar_id',$google_calendar_id);
		
		$client = new Google_Client();
		$client->setClientId($google_client_id);
		$client->setClientSecret($google_client_secret);
		$redirect_uri = add_query_arg( array('action' => 'googleoauth-callback'), home_url() );
		$client->setRedirectUri($redirect_uri);
		$client->setScopes('https://www.googleapis.com/auth/calendar');
		
		if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
			$client->setAccessToken($_SESSION['access_token']);
			$flag = 1;
		}elseif(service_finder_get_gcal_access_token($provider_id) != ""){
			$client->setAccessToken(service_finder_get_gcal_access_token($provider_id));
			$flag = 1;
		}
		
		if($client->isAccessTokenExpired()) {
			 try{
			 
			 if(isset($_SESSION['access_token']) && $_SESSION['access_token']) {
			  $newaccesstoken = json_decode($_SESSION['access_token']);
			  $client->refreshToken($newaccesstoken->refresh_token);
			
			 }elseif(service_finder_get_gcal_access_token($provider_id) != ""){
			  $newaccesstoken = json_decode(service_finder_get_gcal_access_token($provider_id));
			  $client->refreshToken($newaccesstoken->refresh_token);
			 }
			 
			 } catch (Exception $e) {
				
			 }
	
		 }
		
		if($flag == 1){
			$bookingdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `id` = %d',$booking_id));
			try{
			
			if($bookingdata->multi_date == 'yes')
			{
				$bookedservices = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->booked_services.' WHERE `booking_id` = %d',$booking_id));
				
				if(!empty($bookedservices))
				{
					foreach($bookedservices as $bookedservice)
					{
						$calendarId = $google_calendar_id;
						$cal = new Google_Service_Calendar($client);
						$cal->events->delete($calendarId, $bookedservice->gcal_booking_id);
						
						$data = array(
									'gcal_booking_url' => '',
									'gcal_booking_id' => ''
									);
				
						$where = array(
									'id' => $bookedservice->id
									);
									
						$wpdb->update($service_finder_Tables->booked_services,wp_unslash($data),$where);
					}
				}
			}else{
			
				$calendarId = $google_calendar_id;
				$cal = new Google_Service_Calendar($client);
				$cal->events->delete($calendarId, $bookingdata->gcal_booking_id);
				
				$data = array(
							'gcal_booking_url' => '',
							'gcal_booking_id' => ''
							);
		
				$where = array(
							'id' => $booking_id
							);
							
				$wpdb->update($service_finder_Tables->bookings,wp_unslash($data),$where);
			}
			
			} catch (Exception $e) {
			//echo '<pre>';print_r($e);
			}
			return true;
		}
}

/*google calendar date format*/
function service_finder_date_format_RFC3339($timestamp = 0, $offset = 0) {
        if(get_option('timezone_string') != ""){
		$timezone = get_option('timezone_string');
		}else{
		$timezone = 'Asia/Kolkata';
		}
        $date = new DateTime(date('Y-m-d H:i:s', $timestamp), new DateTimeZone($timezone));
        return $date->format(DateTime::RFC3339);
}

if (isset($_GET['code']) && isset($_GET['action']) && $_GET['action'] == 'googleoauth-callback') {
    session_start();
	require_once SERVICE_FINDER_BOOKING_LIB_DIR.'/google-api-php-client/src/Google/autoload.php';
	
	$providerid = isset($_SESSION['providerid']) ? esc_html($_SESSION['providerid']) : '';
	$code = isset($_GET['code']) ? $_GET['code'] : '';
	
	$gcal_creds = service_finder_get_gcal_cred();
	$client_id = $gcal_creds['client_id'];
    $client_secret = $gcal_creds['client_secret'];
	$redirect_uri = add_query_arg( array('action' => 'googleoauth-callback'), home_url() );
	
	$client = new Google_Client();
	$client->setClientId($client_id);
	$client->setClientSecret($client_secret);
	$client->setRedirectUri($redirect_uri);
	$client->setAccessType("offline");
	$client->setApprovalPrompt('force');
	$client->setScopes('https://www.googleapis.com/auth/calendar');	
	try{	
    $client->authenticate($_GET['code']);
	$options = unserialize(get_option( 'provider_settings'));
	$options[$providerid]['google_calendar'] = 'on';
	update_option( 'provider_settings', serialize($options) );
	} catch (Exception $e) {
	//echo '<pre>';print_r($e);
	}
	
    $_SESSION['access_token'] = $client->getAccessToken();
	update_user_meta($providerid,'gcal_access_token',$_SESSION['access_token']);
	
	$account_url = service_finder_get_url_by_shortcode('[service_finder_my_account]');
	
	$redirect_uri = add_query_arg( array('gcal' => 'connected'), $account_url );
	
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
	die;
}

/*google calendar date format*/
function service_finder_get_gcal_access_token($providerid = 0) {
	return get_user_meta($providerid,'gcal_access_token',true);
}

/*theme style*/
function service_finder_themestyle() {
	global $service_finder_options;
	
	$themestyle = (isset($service_finder_options['theme-style'])) ? esc_html($service_finder_options['theme-style']) : '';
	return $themestyle;
}

/*Load branch loaction map*/
add_action('wp_ajax_load_branch_marker', 'service_finder_load_branch_marker');
add_action('wp_ajax_nopriv_load_branch_marker', 'service_finder_load_branch_marker');

function service_finder_load_branch_marker(){
global $wpdb,$service_finder_options, $service_finder_Tables;

$branchid = (isset($_POST['branchid'])) ? esc_attr($_POST['branchid']) : '';
$userid = (isset($_POST['userid'])) ? esc_attr($_POST['userid']) : '';

$res = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->branches.' WHERE id = %d',$branchid));

if(!empty($res)){

$getProvider = new SERVICE_FINDER_searchProviders();
$providerInfo = $getProvider->service_finder_getProviderInfo(esc_attr($userid));

$userLink = service_finder_get_author_url($providerInfo->wp_user_id);
						if(!empty($providerInfo->avatar_id) && $providerInfo->avatar_id > 0){
							$src  = wp_get_attachment_image_src( $providerInfo->avatar_id, 'service_finder-provider-thumb' );
							$src  = $src[0];
						}else{
							$src  = '';
						}
						$icon = service_finder_getCategoryIcon(get_user_meta($providerInfo->wp_user_id,'primary_category',true));
						if($icon == ""){
						$icon = (!empty($service_finder_options['default-map-marker-icon']['url'])) ? $service_finder_options['default-map-marker-icon']['url'] : '';
						}
						
						$markeraddress = service_finder_getBranchAddress($res->id);
		
						if($res->zoomlevel != ""){
							$zoom_level = $res->zoomlevel;
						}else{
							$zoom_level = get_user_meta($providerInfo->wp_user_id,'zoomlevel',true);
						
							if($zoom_level == ""){
							$zoom_level = (!empty($service_finder_options['zoom-level'])) ? $service_finder_options['zoom-level'] : 14;
							}
						}
				
						
						$companyname = service_finder_getCompanyName($providerInfo->wp_user_id);
						
						$marker = '[["'.stripcslashes($providerInfo->full_name).'","'.$res->lat.'","'.$res->long.'","'.$src.'","'.$icon.'","'.$userLink.'","'.$providerInfo->wp_user_id.'","'.service_finder_getCategoryName(get_user_meta($providerInfo->wp_user_id,'primary_category',true)).'","'.stripcslashes($markeraddress).'","'.stripcslashes($companyname).'"]]';
						
						//$marker = '["","'.$providerInfo->lat.'","'.$providerInfo->long.'","","","","","","",""]';
						
						$resarr = array(
									'lat' => $res->lat,
									'long' => $res->long,
									'zoomlevel' => $zoom_level,
									'markers' => $marker
								);
						
						echo json_encode($resarr);		

}
exit;
}		

/*Check if request a quote form show to logged in user or without logged in user*/
function service_finder_request_quote_for_loggedin_user(){
global $service_finder_options, $current_user;

$afterlogin = (!empty($service_finder_options["request-quote-after-login"])) ? esc_html($service_finder_options["request-quote-after-login"]) : "";

	if($afterlogin){
		if(is_user_logged_in() && service_finder_getUserRole($current_user->ID) == 'Customer'){
			return true;
		}else{
			return false;
		}
		
	}else{
		return true;
	}
}

/*Check is user is login from socail account*/
function service_finder_is_social_user($userid = 0){
global $service_finder_options, $current_user;

	$socialaccount = get_user_meta($userid,'social_provider',true);
	
	if($socialaccount != ""){
	return true;
	}else{
	return false;
	}
}

/*Searched provider services*/
function service_finder_get_searched_services($provider_id = 0,$keyword = '',$minprice = 0,$maxprice = 0){
global $wpdb, $service_finder_Tables, $service_finder_options;

if($minprice != '' && $maxprice != '' && $maxprice > 0 && $keyword == ''){

	$services = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->services.' WHERE `wp_user_id` = '.$provider_id.' AND (cost BETWEEN '.$minprice.' AND '.$maxprice.')');

}elseif($minprice == 0 && $maxprice == 0 && $keyword != ''){
	$services = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->services.' WHERE `wp_user_id` = '.$provider_id.' AND (service_name LIKE "%'.$keyword.'%" OR description LIKE "%'.$keyword.'%")');

}elseif($minprice != '' && $maxprice != '' && $maxprice > 0 && $keyword != ''){
	$services = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->services.' WHERE `wp_user_id` = '.$provider_id.' AND (cost BETWEEN '.$minprice.' AND '.$maxprice.') AND (service_name LIKE "%'.$keyword.'%" OR description LIKE "%'.$keyword.'%")');
}else{
	$services = '';
}
	
	return $services;

}


/*Get Provider name with link*/
function service_finder_get_providername_with_link($provider_id = 0){
	global $service_finder_Tables, $wpdb;
		
	$providerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$provider_id));
		
	if(!empty($providerInfo)){
	$userLink = service_finder_get_author_url($provider_id);
	
	$providerlink = '<a href="'.esc_url($userLink).'" target="_blank">'.esc_html($providerInfo->full_name).'</a>';
		
	return $providerlink;
	}

}

/*Get Provider name with link*/
function service_finder_getServiceGroups($provider_id = 0){
	global $service_finder_Tables, $wpdb;
		
	$groups = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->service_groups.' WHERE `provider_id` = %d',$provider_id));
		
	if(!empty($groups)){	
	return $groups;
	}else{
	return '';
	}

}

/*Get video thumbnail*/
function service_finder_identify_videos($embeded_code = '',$size = ''){
		$fbfind   = '//www.facebook.com';
		$fbpos = strpos($embeded_code, $fbfind);
		$thumb = '';
		
		/*if ($fbpos !== false) {
			if (preg_match("~(?:t\.\d+/)?(\d+)~i", $embeded_code, $matches)) {
		   		$videoid = $matches[1];
				
				$xml = file_get_contents('http://graph.facebook.com/' . $videoid); 
			    $result = json_decode($xml); */
				/*if($size == 'full'){
				$thumburl = $result->format[2]->picture; 
				$thumb = "<img src='".$thumburl."' />";
				}else{
				$thumburl = $result->format[1]->picture; 
				$thumb = "<img src='".$thumburl."' width='150' />";
				}*/
				/*
				if($size == 'full'){
				$thumb = "<img src='//graph.facebook.com/".$videoid."/picture?type=large' />";
				}else{
				$thumb = "<img src='//graph.facebook.com/".$videoid."/picture' width='150' />";
				}
				
				
		    }
		
		}*/
		
		$ytfind   = 'youtu';
		$ytpos = strpos($embeded_code, $ytfind);
		
		if ($ytpos !== false) {
			$youtubeinfo = service_finder_get_youtube_info($embeded_code);
			if(!empty($youtubeinfo)){
				$thumb = $youtubeinfo['thumbnail_url'];
				$thumb = "<img src='".$thumb."' />";
			}
		
		}
		
		$vmfind   = 'vimeo.com';
		$vmpos = strpos($embeded_code, $vmfind);
		
		if ($vmpos !== false) {
			 if (preg_match("/(?:.*)\/([0-9]*)/i", $embeded_code, $matches)) {
		   		$videoid = $matches[1];
				if($videoid != ""){
				
				$pagedocument = @file_get_contents("https://vimeo.com/api/v2/video/".$videoid.".php");
				
				if ($pagedocument === false) {
					return '';
				}else{
				
				$hash = unserialize(file_get_contents("https://vimeo.com/api/v2/video/".$videoid.".php"));
				
				if($size == 'full'){
				$thumburl = $hash[0]['thumbnail_large'];
				$thumb = "<img src='".$thumburl."' />";
				}else{
				$thumburl = $hash[0]['thumbnail_medium'];
				$thumb = "<img src='".$thumburl."' width='150' />";
				}
				}
				}
		    }
		
		}
		
		return $thumb;
}

/*Get video thumbnail*/
function service_finder_get_video_thumb_url($embeded_code = '',$size = ''){
		$fbfind   = '//www.facebook.com';
		$fbpos = strpos($embeded_code, $fbfind);
		$thumburl = '';
		
		/*if ($fbpos !== false) {
			if (preg_match("~(?:t\.\d+/)?(\d+)~i", $embeded_code, $matches)) {
		   		$videoid = $matches[1];
				
				$xml = file_get_contents('http://graph.facebook.com/' . $videoid); 
			    $result = json_decode($xml); 

				if($size == 'full'){
				$thumburl = '//graph.facebook.com/'.$videoid.'/picture?type=large';
				}else{
				$thumburl = '//graph.facebook.com/'.$videoid.'/picture';
				}
		    }
		}*/
		
		$ytfind   = 'youtu';
		$ytpos = strpos($embeded_code, $ytfind);
		
		if ($ytpos !== false) {
			$youtubeinfo = service_finder_get_youtube_info($embeded_code);
			if(!empty($youtubeinfo)){
				$thumburl = $youtubeinfo['thumbnail_url'];
			}
		}
		
		$vmfind   = 'vimeo.com';
		$vmpos = strpos($embeded_code, $vmfind);
		
		if ($vmpos !== false) {
			 if (preg_match("/(?:.*)\/([0-9]*)/i", $embeded_code, $matches)) {
		   		$videoid = $matches[1];
				if($videoid != ""){
				
				$pagedocument = @file_get_contents("https://vimeo.com/api/v2/video/".$videoid.".php");
				
				if ($pagedocument === false) {
					return '';
				}else{
				
				$hash = unserialize(file_get_contents("https://vimeo.com/api/v2/video/".$videoid.".php"));
				
				if($size == 'full'){
				$thumburl = $hash[0]['thumbnail_large'];
				$thumburl = $thumburl;
				}else{
				$thumburl = $hash[0]['thumbnail_medium'];
				$thumburl = $thumburl;
				}
				}
				}
		    }
		
		}
		
		return $thumburl;
}		

/*Get video type*/
function service_finder_get_video_type($embeded_code = ''){
		$fbfind   = '//www.facebook.com';
		$fbpos = strpos($embeded_code, $fbfind);
		$videotype = '';
		/*if ($fbpos !== false) {
			if (preg_match("~(?:t\.\d+/)?(\d+)~i", $embeded_code, $matches)) {
				$videotype = 'facebook';
			}
		
		}*/
		
		$ytfind   = 'youtu';
		$ytpos = strpos($embeded_code, $ytfind);
		
		if ($ytpos !== false) {
			$youtubeinfo = service_finder_get_youtube_info($embeded_code);
			if(!empty($youtubeinfo)){
				$videotype = 'youtube';
			}
		}
		
		$vmfind   = 'vimeo.com';
		$vmpos = strpos($embeded_code, $vmfind);
		
		if ($vmpos !== false) {
			 if (preg_match("/(?:.*)\/([0-9]*)/i", $embeded_code, $matches)) {
		   		$videotype = 'vimeo';
		    }
		
		}
		
		return $videotype;
}	

/*Translate week days name*/
function service_finder_trans_weekdays($day = ''){
	$day = strtolower($day);
	switch($day){
	case 'monday':
		$dayname = esc_html__('Monday','service-finder');
		break;
	case 'tuesday':
		$dayname = esc_html__('Tuesday','service-finder');
		break;
	case 'wednesday':
		$dayname = esc_html__('Wednesday','service-finder');
		break;
	case 'thursday':
		$dayname = esc_html__('Thursday','service-finder');
		break;
	case 'friday':
		$dayname = esc_html__('Friday','service-finder');
		break;
	case 'saturday':
		$dayname = esc_html__('Saturday','service-finder');
		break;
	case 'sunday':
		$dayname = esc_html__('Sunday','service-finder');
		break;
	default:
		$dayname = $day;
		break;							
	}
	
	return $dayname;
}

/*Translate am/pm*/
function service_finder_trans_timeunit($timeunit = ''){
	$timeunit = strtolower($timeunit);
	switch($timeunit){
	case 'am':
		$unitname = esc_html__('am','service-finder');
		break;
	case 'pm':
		$unitname = esc_html__('pm','service-finder');
		break;
	default:
		$unitname = $timeunit;
		break;							
	}
	
	return $unitname;
}

/*Load more branches*/
add_action('wp_ajax_load_more_branches', 'service_finder_load_more_branches');
add_action('wp_ajax_nopriv_load_more_branches', 'service_finder_load_more_branches');

function service_finder_load_more_branches(){
global $wpdb,$service_finder_options, $service_finder_Tables;

$userid = (isset($_POST['userid'])) ? esc_attr($_POST['userid']) : '';


$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->branches.' WHERE wp_user_id = %d ORDER BY ID DESC LIMIT 2,10',$userid));

if(!empty($results)){
foreach($results as $res){
?>
	<li class="equal-col">
	   <a href="javascript:;" class="load-branch-address" data-branchid="<?php echo esc_attr($res->id); ?>" data-userid="<?php echo esc_attr($userid); ?>">
	   <?php
	   echo service_finder_getBranches($res->id);
	   ?>
	   </a>
	</li>
<?php
}
}

exit;
}	



/*Custom Comment Rating*/
function service_finder_output_review_fields($post){
	global $wpdb, $service_finder_Tables, $pixreviews_plugin, $author;

		$post_id = get_the_ID();

		//$row = $wpdb->get_row($wpdb->prepare('SELECT `user_id` FROM '.$wpdb->prefix.'usermeta WHERE `meta_value` = %d AND `meta_key` = "comment_post"',$post_id));
		if($author > 0){
		$categoryid = get_user_meta($author,'primary_category',true);
		
		$labels = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->rating_labels.' where category_id = %d',$categoryid));
		$totallevel = count($labels);
		
		if(!empty($labels)){
		$i = 1;
		echo '<div class="sf-customer-rating">';
		foreach($labels as $label){
		?>
		<div class="sf-customer-rating-row clearfix">
        
            <div class="sf-customer-rating-name pull-left"><?php echo $label->label_name; ?></div>
            
            <div class="sf-customer-rating-count  pull-right">
                <div class="sf-customer-rating-sarts">
                    <input class="add-custom-rating" name="comment-rating-<?php echo $i; ?>" value="" type="number" class="rating" min=0 max=5 step=0.5 data-size="sm">
                    <input name="rating-label-<?php echo $i; ?>" value="<?php echo $label->label_name; ?>" type="hidden">
                </div>
            </div>
            
        </div>
		<?php
		$i++;
		}
		echo '<input name="totallevel" value="'.$totallevel.'" type="hidden">';
		echo '</div>';
		}else{
		$labels = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->rating_labels.' where category_id = 0');
		$i = 1;
		
		$totallevel = count($labels);
		
		echo '<div class="sf-customer-rating">';
		if(!empty($labels)){
		foreach($labels as $label){
		?>
		<div class="sf-customer-rating-row clearfix">
        
            <div class="sf-customer-rating-name pull-left"><?php echo $label->label_name; ?></div>
            
            <div class="sf-customer-rating-count  pull-right">
                <div class="sf-customer-rating-sarts">
                    <input class="add-custom-rating" name="comment-rating-<?php echo $i; ?>" value="" type="number" class="rating" min=0 max=5 step=0.5 data-size="sm">
                    <input name="rating-label-<?php echo $i; ?>" value="<?php echo $label->label_name; ?>" type="hidden">
                </div>
            </div>
            
        </div>
		<?php
		$i++;
		}
		}else{
		echo '<div class="alert alert-danger">';
		echo esc_html__('Please set labels for custom rating','service-finder');
		echo '</div>';
		}
		echo '<input name="totallevel" value="'.$totallevel.'" type="hidden">';
		echo '</div>';
		}
		
		}
?>
    <p class="review-title-form">
        <label for="pixrating_title"><?php echo $pixreviews_plugin->get_plugin_option( 'review_title_label' ); ?></label>
        <input type='text' id='rating_title' name='rating_title' value="" placeholder="<?php echo esc_attr( $pixreviews_plugin->get_plugin_option( 'review_title_placeholder' ) ) ?>" size='25'/>
    </p>
<?php
}

/*Save Custom Comment Rating*/
function service_finder_save_comment($commentID = 0){
global $wpdb, $service_finder_Tables, $current_user;
		
		if ( ! is_numeric( $commentID ) ) {
			return;
		}
		
		$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM `'.$wpdb->prefix.'comments` where comment_ID = %d',$commentID));
		$comment_post_id = $row->comment_post_ID;
		
		$row = $wpdb->get_row($wpdb->prepare('SELECT `user_id` FROM '.$wpdb->prefix.'usermeta WHERE `meta_value` = %d AND `meta_key` = "comment_post"',$comment_post_id));
		
		$rating1 = (!empty($_POST['comment-rating-1'])) ? sanitize_text_field($_POST['comment-rating-1']) : 0;
		$rating2 = (!empty($_POST['comment-rating-2'])) ? sanitize_text_field($_POST['comment-rating-2']) : 0;
		$rating3 = (!empty($_POST['comment-rating-3'])) ? sanitize_text_field($_POST['comment-rating-3']) : 0;
		$rating4 = (!empty($_POST['comment-rating-4'])) ? sanitize_text_field($_POST['comment-rating-4']) : 0;
		$rating5 = (!empty($_POST['comment-rating-5'])) ? sanitize_text_field($_POST['comment-rating-5']) : 0;
		
		$label1 = (!empty($_POST['rating-label-1'])) ? sanitize_text_field($_POST['rating-label-1']) : '';
		$label2 = (!empty($_POST['rating-label-2'])) ? sanitize_text_field($_POST['rating-label-2']) : '';
		$label3 = (!empty($_POST['rating-label-3'])) ? sanitize_text_field($_POST['rating-label-3']) : '';
		$label4 = (!empty($_POST['rating-label-4'])) ? sanitize_text_field($_POST['rating-label-4']) : '';
		$label5 = (!empty($_POST['rating-label-5'])) ? sanitize_text_field($_POST['rating-label-5']) : '';
		
		$totallevel = (!empty($_POST['totallevel'])) ? sanitize_text_field($_POST['totallevel']) : 1;
		
		$avgrating = ($rating1 + $rating2 + $rating3 + $rating4 + $rating5)/$totallevel;
		
		$data = array(
				'provider_id' => $row->user_id,
				'customer_id' => $current_user->ID,
				'comment_id' => $commentID,
				'rating_title' => (!empty($_POST['rating_title'])) ? sanitize_text_field($_POST['rating_title']) : '',
				'rating1' => $rating1,
				'rating2' => $rating2,
				'rating3' => $rating3,
				'rating4' => $rating4,
				'rating5' => $rating5,
				'label1' => $label1,
				'label2' => $label2,
				'label3' => $label3,
				'label4' => $label4,
				'label5' => $label5,
				'avgrating' => $avgrating,
				);
		$wpdb->insert($service_finder_Tables->custom_rating,wp_unslash($data));
		
		$avgrating = round($avgrating, 2);
			
		$wpdb->query($wpdb->prepare('UPDATE '.$service_finder_Tables->providers.' SET `rating` = "%f" WHERE `wp_user_id` = %d',$avgrating,$row->user_id));
}

/*Save Custom Comment Rating for simple rating*/
function service_finder_save_comment_simple_rating($commentID = 0){
global $wpdb, $service_finder_Tables, $current_user;
	if ( ! is_numeric( $commentID ) ) {
		return;
	}
	
	$pixrating = (!empty($_POST['pixrating'])) ? sanitize_text_field($_POST['pixrating']) : 0;
	$pixrating_title = (!empty($_POST['pixrating_title'])) ? sanitize_text_field($_POST['pixrating_title']) : 0;
	
	update_comment_meta($commentID,'pixrating',$pixrating);
	update_comment_meta($commentID,'pixrating_title',$pixrating_title);
	
	$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM `'.$wpdb->prefix.'comments` where comment_ID = %d',$commentID));
	$comment_post_id = $row->comment_post_ID;
	
	$row = $wpdb->get_row($wpdb->prepare('SELECT `user_id` FROM '.$wpdb->prefix.'usermeta WHERE `meta_value` = %d AND `meta_key` = "comment_post"',$comment_post_id));	
	
	$avgrating = service_finder_getAverageRating($row->user_id);
	
	$avgrating = round($avgrating, 2);
			
	$wpdb->query($wpdb->prepare('UPDATE '.$service_finder_Tables->providers.' SET `rating` = "%f" WHERE `wp_user_id` = %d',$avgrating,$row->user_id));
}

/*update avg rating for provider when change comment status*/
add_action('transition_comment_status', 'my_approve_comment_callback', 10, 3);
function my_approve_comment_callback($new_status = '', $old_status = '', $comment = '') {
    global $wpdb, $service_finder_Tables, $current_user;
	
	if($old_status != $new_status) {
		$commentID = (!empty($_POST['id'])) ? sanitize_text_field($_POST['id']) : 0;
		
        $row = $wpdb->get_row($wpdb->prepare('SELECT * FROM `'.$wpdb->prefix.'comments` where comment_ID = %d',$commentID));
		$comment_post_id = $row->comment_post_ID;
		
		$row = $wpdb->get_row($wpdb->prepare('SELECT `user_id` FROM '.$wpdb->prefix.'usermeta WHERE `meta_value` = %d AND `meta_key` = "comment_post"',$comment_post_id));	
		
		$avgrating = service_finder_getAverageRating($row->user_id);
		
		$avgrating = round($avgrating, 2);
				
		$wpdb->query($wpdb->prepare('UPDATE '.$service_finder_Tables->providers.' SET `rating` = "%f" WHERE `wp_user_id` = %d',$avgrating,$row->user_id));
    }
}

/*Save Custom Comment Rating*/
function service_finder_display_rating($comment = ''){
global $wpdb, $service_finder_Tables, $current_user;

	//bail if we don't have a valid current comment ID
	if ( ! get_comment_ID() ) {
		return $comment;
	}
	$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM `'.$service_finder_Tables->custom_rating.'` where `comment_id` = %d',get_comment_ID()));

	$rating = '';
	if(!empty($row)){
	
	if($row->label1 != ""){
	$k = 1;
	}
	if($row->label2 != ""){
	$k = 2;
	}
	if($row->label3 != ""){
	$k = 3;
	}
	if($row->label4 != ""){
	$k = 4;
	}
	if($row->label5 != ""){
	$k = 5;
	}
	
	$rating .= '<div class="sf-customer-display-rating">';
	for($i=1;$i<=$k;$i++){
	switch($i){
	case 1:
		$label = $row->label1;
		$ratingnumber = $row->rating1;
		break;
	case 2:
		$label = $row->label2;
		$ratingnumber = $row->rating2;
		break;
	case 3:
		$label = $row->label3;
		$ratingnumber = $row->rating3;
		break;
	case 4:
		$label = $row->label4;
		$ratingnumber = $row->rating4;
		break;
	case 5:
		$label = $row->label5;
		$ratingnumber = $row->rating5;
		break;				
	}
	$rating .= '<div class="sf-customer-rating-row clearfix">';
        
        $rating .= '<div class="sf-customer-rating-name pull-left">'.$label.'</div>';
        
        $rating .= '<div class="sf-customer-rating-count  pull-right">';
        $rating .= service_finder_displayRating($ratingnumber);
        $rating .= '</div>';
	$rating .= '</div>';	
	}
	$rating .= '</div>';
	
	$comment = $rating . $comment;
	
	/*if(!empty($row->rating_title)){
	$comment = '<h3 class="pixrating_title">' . $row->rating_title . '</h3>' . $comment;
	}*/
	
	}
	
	return $comment;
}

function service_finder_get_youtube_info($youtubeurl = ''){

$url = "https://www.youtube.com/oembed?url=". $youtubeurl ."&format=json";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
$response = curl_exec($ch);
curl_close($ch);

return json_decode($response, true);

}

/*Breadcrumb for provider profile page*/
function service_finder_get_category_breadcrumb($catid = 0){
$texonomy = 'providers-category';
$catdetails = get_term_by('id', $catid, $texonomy);
if(!empty($catdetails)){
$link = get_term_link( $catdetails, $texonomy );
$li = '';
if($catdetails->parent != "" && $catdetails->parent > 0){
$parentcatid = $catdetails->parent;
$parentcatdetails = get_term_by('id', $parentcatid, $texonomy);
$parentlink = get_term_link( $parentcatdetails, $texonomy );
$li .= '<li><a href="'.esc_url($parentlink).'">'.service_finder_getCategoryName($parentcatid).'</a></li>';
}
$li .= '<li><a href="'.esc_url($link).'">'.service_finder_getCategoryName($catid).'</a></li>';
return $li;
}
}

function send_mail_after_joblimit_connect_purchase( $userid  = 0 ){
		global $wpdb, $service_finder_options, $service_finder_Tables;
		
		$email = service_finder_getProviderEmail($userid);
		
		$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('Provider', 'service-finder');	
		
		$row = $wpdb->get_row('SELECT * FROM '.$service_finder_Tables->job_limits.' WHERE `provider_id` = "'.$userid.'"');
		$payment_method = '';
		$current_plan = '';
		if(!empty($row)){
		$payment_method = $row->payment_method;
		$current_plan = $row->current_plan;
		}
		
		$upgrade_plan = (!empty($service_finder_options['plan'.$current_plan.'-name'])) ? $service_finder_options['plan'.$current_plan.'-name'] : '';
		
		if(!empty($service_finder_options['joblimit-connect-purchase-message'])){
			$message = $service_finder_options['joblimit-connect-purchase-message'];
		}else{
			$message = esc_html__('Dear ', 'service-finder').esc_html($providerreplacestring).',';
			$message .= esc_html__('Congratulations! Your Job Coonect Upgraded Successfully', 'service-finder');
			$message .= esc_html__('Name: ', 'service-finder').'%USERNAME%';
			$message .= esc_html__('Plan Name: ', 'service-finder').'%PLANNAME%';
			$message .= esc_html__('Payment Method: ', 'service-finder').'%PAYMENTMETHOD%';
		}
		
		if($service_finder_options['joblimit-connect-purchase-subject'] != ""){
			$msg_subject = $service_finder_options['joblimit-connect-purchase-subject'];
		}else{
			$msg_subject = esc_html__('Job Connect Approval Notification', 'service-finder');
		}
		
		$tokens = array('%USERNAME%','%PLANNAME%','%PAYMENTMETHOD%');
		$replacements = array(service_finder_getProviderName($userid),$upgrade_plan,service_finder_translate_static_status_string($payment_method));
		$msg_body = str_replace($tokens,$replacements,$message);
		
		if(function_exists('service_finder_add_notices')) {
	
			$noticedata = array(
					'provider_id' => $userid,
					'target_id' => $row->id, 
					'topic' => 'Job Apply Connect',
					'title' => esc_html__('Job Apply Connect', 'service-finder'),
					'notice' => esc_html__('Your job apply connect plan upgraded.', 'service-finder')
					);
			service_finder_add_notices($noticedata);
		
		}
		
		service_finder_wpmailer($email,$msg_subject,$msg_body);
	}
	
function send_mail_after_jobpost_limit_connect_purchase( $userid = 0 ){
		global $wpdb, $service_finder_options, $service_finder_Tables;
		
		$email = service_finder_getCustomerEmail($userid);
			
		$customerreplacestring = (!empty($service_finder_options['customer-replace-string'])) ? $service_finder_options['customer-replace-string'] : esc_html__('Customer', 'service-finder');	
		
		$row = $wpdb->get_row('SELECT * FROM '.$service_finder_Tables->job_limits.' WHERE `provider_id` = "'.$userid.'"');
		$payment_method = '';
		$current_plan = '';
		if(!empty($row)){
		$payment_method = $row->payment_method;
		$current_plan = $row->current_plan;
		}
		
		$upgrade_plan = (!empty($service_finder_options['job-post-plan'.$current_plan.'-name'])) ? $service_finder_options['job-post-plan'.$current_plan.'-name'] : '';
		
		if(!empty($service_finder_options['jobpost-connect-purchase-message'])){
			$message = $service_finder_options['jobpost-connect-purchase-message'];
		}else{
			$message = esc_html__('Dear ', 'service-finder').esc_html($customerreplacestring).',';
			$message .= esc_html__('Congratulations! Your Job Post Connect Upgraded Successfully', 'service-finder');
			$message .= esc_html__('Name: ', 'service-finder').'%USERNAME%';
			$message .= esc_html__('Plan Name: ', 'service-finder').'%PLANNAME%';
			$message .= esc_html__('Payment Method: ', 'service-finder').'%PAYMENTMETHOD%';
		}
		
		if($service_finder_options['jobpost-connect-purchase-subject'] != ""){
			$msg_subject = $service_finder_options['jobpost-connect-purchase-subject'];
		}else{
			$msg_subject = esc_html__('Job Post Connect Approval Notification', 'service-finder');
		}
		
		$tokens = array('%USERNAME%','%PLANNAME%','%PAYMENTMETHOD%');
		$replacements = array(service_finder_getCustomerName($userid),$upgrade_plan,service_finder_translate_static_status_string($payment_method));
		$msg_body = str_replace($tokens,$replacements,$message);
		
		if(function_exists('service_finder_add_notices')) {
	
			$noticedata = array(
					'customer_id' => $userid,
					'target_id' => $row->id, 
					'topic' => 'Job Post Connect',
					'title' => esc_html__('Job Post Connect', 'service-finder'),
					'notice' => esc_html__('Your job post connect plan upgraded.', 'service-finder')
					);
			service_finder_add_notices($noticedata);
		
		}
		
		service_finder_wpmailer($email,$msg_subject,$msg_body);
	
	}	
	
function service_finder_get_identity_bubble(){
	global $wpdb, $service_finder_Tables;

	$results = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `identity` = ""');

	$total = 0;
	if(!empty($results)){
		foreach($results as $row){
			$providerid = $row->wp_user_id;
			$attachmentIDs = service_finder_get_identity($providerid);
			if(!empty($attachmentIDs)){
				$total++;
			}
		}
	}

	return $total;
}

add_action( 'admin_menu', 'service_finder_add_menu_bubble' );
function service_finder_add_menu_bubble() {
  global $menu, $submenu;
  
  $totalcount = 0;
  
  if(!empty($submenu['service-finder'])){
  foreach ( $submenu['service-finder'] as $key => $value ) {
      
	  $menuslug = $submenu['service-finder'][$key][2];
	  switch($menuslug){
	  	case 'identity-check':
			$count = service_finder_get_identity_bubble();
			$totalcount = $totalcount + $count;
			$submenu['service-finder'][$key][0] .= ' <span class="awaiting-mod update-sfservices count-'.$count.'" data-toggle="tooltip" data-placement="top" title="'.esc_html__('Pending Identity Check Requests', 'service-finder').'"><span class="pending-count">'.$count.'</span></span>';
			break;
		case 'featured-requests':
			$count = service_finder_get_featured_bubble();
			$totalcount = $totalcount + $count;
			$submenu['service-finder'][$key][0] .= ' <span class="awaiting-mod update-sfservices count-'.$count.'" data-toggle="tooltip" data-placement="top" title="'.esc_html__('Pending Featured Requests', 'service-finder').'"><span class="pending-count">'.$count.'</span></span>';
			break;
		case 'claimbusiness':
			$count = service_finder_get_claimbusiness_bubble();
			$totalcount = $totalcount + $count;
			$submenu['service-finder'][$key][0] .= ' <span class="awaiting-mod update-sfservices count-'.$count.'" data-toggle="tooltip" data-placement="top" title="'.esc_html__('Pending Claimed Business Requests', 'service-finder').'"><span class="pending-count">'.$count.'</span></span>';
			break;
		case 'upgraderequest':
			$count = service_finder_get_upgraderequest_bubble();
			$totalcount = $totalcount + $count;
			$submenu['service-finder'][$key][0] .= ' <span class="awaiting-mod update-sfservices count-'.$count.'" data-toggle="tooltip" data-placement="top" title="'.esc_html__('Pending Account Upgrade Requests via Wire Transfer', 'service-finder').'"><span class="pending-count">'.$count.'</span></span>';
			break;
		case 'jobconnectrequest':
			$count = service_finder_get_jobconnectrequest_bubble();
			$totalcount = $totalcount + $count;
			$submenu['service-finder'][$key][0] .= ' <span class="awaiting-mod update-sfservices count-'.$count.'" data-toggle="tooltip" data-placement="top" title="'.esc_html__('Pending Job Connect Requests via Wire Transfer', 'service-finder').'"><span class="pending-count">'.$count.'</span></span>';
			break;
		case 'wallet-wired-request':
			$count = service_finder_get_walletrequest_bubble();
			$totalcount = $totalcount + $count;
			$submenu['service-finder'][$key][0] .= ' <span class="awaiting-mod update-sfservices count-'.$count.'" data-toggle="tooltip" data-placement="top" title="'.esc_html__('Pending Wallet Amount Requests via Wire Transfer', 'service-finder').'"><span class="pending-count">'.$count.'</span></span>';
			break;
		case 'invoices':
			$count = service_finder_get_wiredinvoicerequest_bubble();
			$totalcount = $totalcount + $count;
			$submenu['service-finder'][$key][0] .= ' <span class="awaiting-mod update-sfservices count-'.$count.'" data-toggle="tooltip" data-placement="top" title="'.esc_html__('Pending Invoice Amount Requests via Wire Transfer', 'service-finder').'"><span class="pending-count">'.$count.'</span></span>';
			break;					
	  }

    }
	
	foreach ( $menu as $key => $value ) {
      if ( $menu[$key][2] == 'service-finder' ) {
        $menu[$key][0] .= ' <span class="awaiting-mod update-sfservices count-'.$totalcount.'"><span class="pending-count">'.$totalcount.'</span></span>';
        return;
      }
    }
	}
  
  	return;

}

function service_finder_get_featured_bubble(){
	global $wpdb, $service_finder_Tables;
	
	$total = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->feature.' WHERE `status` = "Waiting for Approval"');
	
	$total = count($total);
	
	return $total;
}	

function service_finder_get_claimbusiness_bubble(){
	global $wpdb, $service_finder_Tables;
	
	$total = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->claim_business.' WHERE `status` = "pending"');
	
	$total = count($total);
	
	return $total;
}

function service_finder_get_upgraderequest_bubble(){
	global $wpdb, $service_finder_Tables;
	
	$total = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'usermeta WHERE `meta_key` = "upgrade_request_status" AND `meta_value` = "pending"');
	
	$total = count($total);
	
	return $total;
}	

function service_finder_get_jobconnectrequest_bubble(){
	global $wpdb, $service_finder_Tables;
	
	$total = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'usermeta WHERE `meta_key` = "job_connect_request_status" AND `meta_value` = "pending"');
	
	$total = count($total);
	
	return $total;
}

function service_finder_get_walletrequest_bubble(){
	global $wpdb, $service_finder_Tables;
	
	$total = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->wallet_transaction.' WHERE `payment_status` = "pending"');
	
	$total = count($total);
	
	return $total;
}	

function service_finder_get_wiredinvoicerequest_bubble(){
	global $wpdb, $service_finder_Tables;
	
	$total = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->invoice.' WHERE `payment_type` = "wire-transfer" AND `status` = "on-hold"');
	
	$total = count($total);
	
	return $total;
}	

add_shortcode( 'wordpress_social_login', 'wordpress_social_login_fix' );

function wordpress_social_login_fix( $attributes = '', $content = '' ) {
    ob_start();
    wsl_render_login_form();
    return ob_get_clean();
}

function service_finder_create_user_name($fullname = '') {
	global $wpdb, $service_finder_Tables;
	
    $slug = sanitize_user($fullname);
	
	return $slug;
	
}

function service_finder_get_admin_id(){
	global $wpdb, $service_finder_Tables;
	
    $users_query = new WP_User_Query( array( 
                'role' => 'administrator', 
                'orderby' => 'display_name'
                ) );
				
    $results = $users_query->get_results();
	
	if(!empty($results)){
    foreach($results as $user)
    {
        return $user->ID;
    }
	}
	
}

function service_finder_check_empty_category($catid = 0){
	global $wpdb, $service_finder_Tables;
	
	$row = $wpdb->get_row('SELECT * FROM '.$service_finder_Tables->providers.' where `admin_moderation` = "approved" AND `account_blocked` != "yes" AND FIND_IN_SET("'.$catid.'", category_id)');
	
	if(empty($row)){
		return true;
	}else{
		return false;
	}
}

/*Connect Stripe Account*/
if (isset($_GET['code']) && isset($_GET['scope']) && !isset($_GET['loginSocial'])) { 

	$service_finder_options = get_option('service_finder_options');
	$current_user = service_finder_plugin_global_vars('current_user');
	
	if( isset($service_finder_options['stripe-type']) && $service_finder_options['stripe-type'] == 'test' ){
		$secret_key = (!empty($service_finder_options['stripe-test-secret-key'])) ? $service_finder_options['stripe-test-secret-key'] : '';
	}else{
		$secret_key = (!empty($service_finder_options['stripe-live-secret-key'])) ? $service_finder_options['stripe-live-secret-key'] : '';
	}

    $code = (isset($_GET['code'])) ? esc_html($_GET['code']) : '';
	$clientid = (!empty($service_finder_options['stripe-connect-client-id'])) ? $service_finder_options['stripe-connect-client-id'] : '';
	define('TOKEN_URI', 'https://connect.stripe.com/oauth/token');
	
    $token_request_body = array(
      'client_secret' => $secret_key,
      'grant_type' => 'authorization_code',
      'client_id' => $clientid,
      'code' => $code,
    );
	
	$req = curl_init(TOKEN_URI);
    curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($req, CURLOPT_POST, true );
    curl_setopt($req, CURLOPT_POSTFIELDS, http_build_query($token_request_body));
	curl_setopt($req, CURLOPT_SSL_VERIFYPEER, false);
    // TODO: Additional error handling
    $respCode = curl_getinfo($req, CURLINFO_HTTP_CODE);
    $resp = json_decode(curl_exec($req), true);
    
	curl_close($req);
	if (!isset($resp['error'])) {
		update_user_meta($current_user->ID, 'provider_connected', 1);
		update_user_meta($current_user->ID, 'admin_client_id', $clientid);
		update_user_meta($current_user->ID, 'access_token', $resp['access_token']);
		update_user_meta($current_user->ID, 'refresh_token', $resp['refresh_token']);
		update_user_meta($current_user->ID, 'stripe_publishable_key', $resp['stripe_publishable_key']);
		update_user_meta($current_user->ID, 'stripe_connect_id', $resp['stripe_user_id']);
		
		$redirect_uri = service_finder_get_url_by_shortcode('[service_finder_my_account]');	
		$redirect_uri = add_query_arg( array('stripe_connect' => 'success'), $redirect_uri );
	}else{
		$redirect_uri = service_finder_get_url_by_shortcode('[service_finder_my_account]');	
		$redirect_uri = add_query_arg( array('stripe_connect' => 'failed'), $redirect_uri );
	}
	
	
	wp_redirect($redirect_uri);
	exit;
	
}

/*Disconnect Stripe Account*/
if (isset($_GET['disconnect_stripe']) && isset($_GET['stripe_connect_id'])) { 

	$service_finder_options = get_option('service_finder_options');
	$current_user = service_finder_plugin_global_vars('current_user');
	
	if( isset($service_finder_options['stripe-type']) && $service_finder_options['stripe-type'] == 'test' ){
		$secret_key = (!empty($service_finder_options['stripe-test-secret-key'])) ? $service_finder_options['stripe-test-secret-key'] : '';
	}else{
		$secret_key = (!empty($service_finder_options['stripe-live-secret-key'])) ? $service_finder_options['stripe-live-secret-key'] : '';
	}
	
	$stripe_connect_id = (isset($_GET['stripe_connect_id'])) ? esc_html($_GET['stripe_connect_id']) : '';
	$client_id = (isset($_GET['client_id'])) ? esc_html($_GET['client_id']) : '';
		
	$token_request_body = array(
		'client_id' => $client_id,
		'stripe_user_id' => $stripe_connect_id,
		'client_secret' => $secret_key
	);
	$req = curl_init('https://connect.stripe.com/oauth/deauthorize');
	curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($req, CURLOPT_POST, true);
	curl_setopt($req, CURLOPT_POSTFIELDS, http_build_query($token_request_body));
	curl_setopt($req, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($req, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($req, CURLOPT_VERBOSE, true);
	
	$respCode = curl_getinfo($req, CURLINFO_HTTP_CODE);
	$resp = json_decode(curl_exec($req), true);
	curl_close($req);
	if (isset($resp['stripe_user_id'])) {
		delete_user_meta($current_user->ID, 'provider_connected');
		delete_user_meta($current_user->ID, 'admin_client_id');
		delete_user_meta($current_user->ID, 'access_token');
		delete_user_meta($current_user->ID, 'refresh_token');
		delete_user_meta($current_user->ID, 'stripe_publishable_key');
		delete_user_meta($current_user->ID, 'stripe_connect_id');
		
		$redirect_uri = service_finder_get_url_by_shortcode('[service_finder_my_account]');	
		$redirect_uri = add_query_arg( array('stripe_disconnect' => 'success'), $redirect_uri );
	} else {
		$redirect_uri = service_finder_get_url_by_shortcode('[service_finder_my_account]');	
		$redirect_uri = add_query_arg( array('stripe_disconnect' => 'failed'), $redirect_uri );
	}
	
	wp_redirect($redirect_uri);
	exit;
	
}

/*Get distance in km/mi*/
function service_finder_getDistance($distance = ''){
global $service_finder_options;
if($distance != ""){
$radiussearchunit = (isset($service_finder_options['radius-search-unit'])) ? esc_attr($service_finder_options['radius-search-unit']) : 'mi';
$html = '<div  class="sf-featured-address"><i class="fa fa-road"></i> '.esc_html__( 'Distance', 'service-finder' ).': '.round($distance,2).' '.$radiussearchunit.' </div>';
return $html;
}

}

/*Get Mangopay settings*/
function service_finder_mangopay_settings(){
	$mp_settings = get_option('mangopay_settings');
	return $mp_settings;
}

/*Add vendor role to user*/
function service_finder_meke_user_vendor($user_id = 0){
if( class_exists( 'WC_Vendors' ) && class_exists( 'WooCommerce' ) && class_exists( 'mangopayWCMain' ) ) {
$user = new WP_User( $user_id );
$user->add_role('Provider');
$user->add_role('vendor');

service_finder_make_mp_user($user_id);

update_user_meta( $user_id, 'is_vendor', 'yes' );

$productid = get_user_meta( $user_id, '_vendor_product_id', true );
if($productid == ''){
service_finder_create_wooproduct($user_id);
}
}
}

/*Remove user from vendor role*/
function service_finder_remove_user_vendor($user_id = 0){
if( class_exists( 'WC_Vendors' ) && class_exists( 'WooCommerce' ) ) {
$user = new WP_User( $user_id );
$user->remove_role( 'vendor' );

update_user_meta( $user_id, 'is_vendor', '' );
}
}

/*Create new product for provider booking*/
function service_finder_create_wooproduct($user_id = 0){
global $service_finder_Tables, $wpdb;
		
	$provider = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$user_id));
		
	$post_id = wp_insert_post( array(
		'post_author' => $user_id,
		'post_title' => $provider->full_name,
		'post_content' => esc_html__('Its a provider booking product', 'service-finder'),
		'post_status' => 'publish',
		'post_type' => "product",
	) );
	wp_set_object_terms( $post_id, 'simple', 'product_type' );
	update_post_meta( $post_id, '_visibility', 'visible' );
	update_post_meta( $post_id, '_stock_status', 'instock');
	update_post_meta( $post_id, 'total_sales', '0' );
	update_post_meta( $post_id, '_downloadable', 'no' );
	update_post_meta( $post_id, '_virtual', 'no' );
	update_post_meta( $post_id, '_regular_price', 15 );
	update_post_meta( $post_id, '_sale_price', 10 );
	update_post_meta( $post_id, '_purchase_note', '' );
	update_post_meta( $post_id, '_featured', 'no' );
	update_post_meta( $post_id, '_weight', '' );
	update_post_meta( $post_id, '_length', '' );
	update_post_meta( $post_id, '_width', '' );
	update_post_meta( $post_id, '_height', '' );
	update_post_meta( $post_id, '_sku', '' );
	update_post_meta( $post_id, '_product_attributes', array() );
	update_post_meta( $post_id, '_sale_price_dates_from', '' );
	update_post_meta( $post_id, '_sale_price_dates_to', '' );
	update_post_meta( $post_id, '_price', 10 );
	update_post_meta( $post_id, '_sold_individually', '' );
	update_post_meta( $post_id, '_manage_stock', 'no' );
	update_post_meta( $post_id, '_backorders', 'no' );
	update_post_meta( $post_id, '_stock', '' );
	
	update_user_meta( $user_id, '_vendor_product_id', $post_id );
}

/*Create new product for provider booking*/
function service_finder_make_mp_user($user_id = 0){
global $service_finder_Tables, $wpdb;
		
	try {
		$mp_settings = service_finder_mangopay_settings();
		$prod_or_sandbox = $mp_settings['prod_or_sandbox'];
		
		if( $prod_or_sandbox == 'prod' ){
			$clientid = $mp_settings['prod_client_id'];
			$clientpassphrase = $mp_settings['prod_passphrase'];
		}else{
			$clientid = $mp_settings['sand_client_id'];
			$clientpassphrase = $mp_settings['sand_passphrase'];
		}
		
		$tmp_path = service_finder_get_mp_temp_path();
		
		$api = new MangoPay\MangoPayApi();
		$api->Config->ClientId = $clientid;
		$api->Config->ClientPassword = $clientpassphrase;
		$api->Config->TemporaryFolder = $tmp_path;
		
		$userInfo = service_finder_getUserInfo($user_id);
		$user_birthday = get_user_meta($user_id,'user_birthday',true);
		$user_nationality = get_user_meta($user_id,'user_nationality',true);
		$billing_country = get_user_meta($user_id,'billing_country',true);
		
		// CREATE NATURAL USER
		$naturalUser = new MangoPay\UserNatural();
		$naturalUser->Email = $userInfo['email'];
		$naturalUser->FirstName = $userInfo['fname'];
		$naturalUser->Tag = "wp_user_id:".$user_id;
		$naturalUser->LastName = $userInfo['lname'];
		$naturalUser->Birthday = strtotime($user_birthday);
		$naturalUser->Nationality = $user_nationality;
		$naturalUser->CountryOfResidence = $billing_country;
		$naturalUserResult = $api->Users->Create($naturalUser);

		//MangoPay\Libraries\Logs::Debug('CREATED NATURAL USER', $naturalUserResult);
		$mp_user_id = $naturalUserResult->Id;
		if( $prod_or_sandbox == 'prod' ){
		update_user_meta($user_id,'mp_user_id_prod',$mp_user_id);
		}else{
		update_user_meta($user_id,'mp_user_id_sandbox',$mp_user_id);
		}
		
	} catch (MangoPay\Libraries\ResponseException $e) {
		
		MangoPay\Libraries\Logs::Debug('MangoPay\ResponseException Code', $e->GetCode());
		MangoPay\Libraries\Logs::Debug('Message', $e->GetMessage());
		MangoPay\Libraries\Logs::Debug('Details', $e->GetErrorDetails());
		
	} catch (MangoPay\Libraries\Exception $e) {
		
		MangoPay\Libraries\Logs::Debug('MangoPay\Exception Message', $e->GetMessage());
	}
}

/*Get mango pay user id*/
function service_finder_get_mp_vendor_id($wp_user_id = 0){

$mp_settings = service_finder_mangopay_settings();
$prod_or_sandbox = $mp_settings['prod_or_sandbox'];

$umeta_key = 'mp_user_id';
if( $prod_or_sandbox == 'sandbox' ){
	$umeta_key .= '_sandbox';
}	

$mp_vendor_id = get_user_meta( $wp_user_id, $umeta_key, true );

return $mp_vendor_id;
}

/*Get mango pay temp path*/
function service_finder_get_mp_temp_path(){

$mp_settings = service_finder_mangopay_settings();
$prod_or_sandbox = $mp_settings['prod_or_sandbox'];

$uploads			= wp_upload_dir();
$uploads_path		= $uploads['basedir'];
$tmp_path			= $uploads_path . '/mp_tmp/' . $prod_or_sandbox;

return $tmp_path;
}

/*Get mango pay vendor bank account id*/
function service_finder_get_mp_account_id($wp_user_id = 0){

$mp_settings = service_finder_mangopay_settings();
$prod_or_sandbox = $mp_settings['prod_or_sandbox'];

$umeta_key = 'mp_account_id';
if( $prod_or_sandbox == 'sandbox' ){
	$umeta_key .= '_sandbox';
}	

$mp_account_id = get_user_meta( $wp_user_id, $umeta_key, true );

return $mp_account_id;
}

//echo '<pre>';print_r(service_finder_get_bank_account_status());echo '</pre>';


/*Get mango pay vendor bank account id*/
function service_finder_get_bank_account_status(){
$mp_settings = service_finder_mangopay_settings();
$prod_or_sandbox = $mp_settings['prod_or_sandbox'];

if( $prod_or_sandbox == 'prod' ){
	$clientid = $mp_settings['prod_client_id'];
	$clientpassphrase = $mp_settings['prod_passphrase'];
}else{
	$clientid = $mp_settings['sand_client_id'];
	$clientpassphrase = $mp_settings['sand_passphrase'];
}

$tmp_path = service_finder_get_mp_temp_path();

$api = new MangoPay\MangoPayApi();
$api->Config->ClientId = $clientid;
$api->Config->ClientPassword = $clientpassphrase;
$api->Config->TemporaryFolder = $tmp_path;

try {

$UserId = '51355364';
$BankAccountId = '51355436';

return $BankAccount = $api->Users->GetBankAccount($UserId, $BankAccountId);

} catch(MangoPay\Libraries\ResponseException $e) {
// handle/log the response exception with code $e->GetCode(), message $e->GetMessage() and error(s) $e->GetErrorDetails()
return $e->GetMessage();

} catch(MangoPay\Libraries\Exception $e) {
// handle/log the exception $e->GetMessage()
return $e->GetMessage();

} 

}

/*Create new product for provider booking*/
function service_finder_get_mp_wallet($mp_user_id = 0){

$mp_settings = service_finder_mangopay_settings();
$prod_or_sandbox = $mp_settings['prod_or_sandbox'];

if( $prod_or_sandbox == 'prod' ){
	$clientid = $mp_settings['prod_client_id'];
	$clientpassphrase = $mp_settings['prod_passphrase'];
}else{
	$clientid = $mp_settings['sand_client_id'];
	$clientpassphrase = $mp_settings['sand_passphrase'];
}

$tmp_path = service_finder_get_mp_temp_path();

$api = new MangoPay\MangoPayApi();
$api->Config->ClientId = $clientid;
$api->Config->ClientPassword = $clientpassphrase;
$api->Config->TemporaryFolder = $tmp_path;

$wallets = $api->Users->GetWallets( $mp_user_id );

foreach( $wallets as $wallet ){
	$mp_vendor_wallet_id = $wallet->Id;
}

return $mp_vendor_wallet_id;
}

function service_finder_get_mp_payout_status( $bookingid = 0, $order_id = 0 ){
	global $wpdb,$service_finder_options, $service_finder_Tables;
	
	try {
		
	$PayOutId = get_post_meta($order_id,'mp_payout_id',true);	

	$mp_settings = service_finder_mangopay_settings();
		$prod_or_sandbox = $mp_settings['prod_or_sandbox'];
		
		if( $prod_or_sandbox == 'prod' ){
			$clientid = $mp_settings['prod_client_id'];
			$clientpassphrase = $mp_settings['prod_passphrase'];
		}else{
			$clientid = $mp_settings['sand_client_id'];
			$clientpassphrase = $mp_settings['sand_passphrase'];
		}
		
		$tmp_path = service_finder_get_mp_temp_path();
		
		$api = new MangoPay\MangoPayApi();
		$api->Config->ClientId = $clientid;
		$api->Config->ClientPassword = $clientpassphrase;
		$api->Config->TemporaryFolder = $tmp_path;

	$PayOut = $api->PayOuts->Get($PayOutId);
	
	$oldstatus = get_post_meta($order_id,'mp_payout_status',true);
	
	if($result->Status != $oldstatus){
		if($result->Status == 'SUCCEEDED'){
			
			$payoutstatus = 'paid';
			$notificationmsg = sprintf(esc_html__('Succeed: Payout has been paid to your bank account. Booking Ref id is #%d', 'service-finder'),$bookingid);

		}elseif($result->Status == 'FAILED'){
			
			$payoutstatus = 'failed';
			$responsemsg = $result->ResultMessage;
			$notificationmsg = sprintf(esc_html__('Failed: Payout Failed (%s). Booking Ref id is #%d', 'service-finder'),$responsemsg,$bookingid);
		}else{
			
			$payoutstatus = $result->Status;
			$responsemsg = $result->ResultMessage;
			$notificationmsg = $responsemsg;
		}
		
		$data = array(
					'paid_to_provider' => $payoutstatus,
					);
			
			$where = array(
					'id' => $bookingid,
					);
			
			$booking_id = $wpdb->update($service_finder_Tables->bookings,wp_unslash($data),$where);
			
			update_post_meta($order_id,'mp_payout_due','no');
			
			$data = array(
					'status' => $payoutstatus,
					);
			
			$where = array(
					'order_id' => $order_id,
					);
			
			$commission_id = $wpdb->update($wpdb->prefix.'pv_commission',wp_unslash($data),$where);
			
		if(function_exists('service_finder_add_notices')) {
				$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `id` = %d',$bookingid));	
				$noticedata = array(
						'provider_id' => $row->provider_id,
						'target_id' => $row->id, 
						'topic' => 'Booking Payment',
						'title' => esc_html__('Booking Payment', 'service-finder'),
						'notice' => $notificationmsg
						);
				service_finder_add_notices($noticedata);
			
			}	
		
	}
	

	} catch(MangoPay\Libraries\ResponseException $e) {
	// handle/log the response exception with code $e->GetCode(), message $e->GetMessage() and error(s) $e->GetErrorDetails()
	$body = $e->getJsonBody();
	$err  = $body['error'];

	$error = array(
			'status' => 'error',
			'err_message' => sprintf( esc_html__('%s', 'service-finder'), $err['message'] )
			);
	return $service_finder_Errors = json_encode($error);
	} catch(MangoPay\Libraries\Exception $e) {
	// handle/log the exception $e->GetMessage()
	$body = $e->getJsonBody();
	$err  = $body['error'];

	$error = array(
			'status' => 'error',
			'err_message' => sprintf( esc_html__('%s', 'service-finder'), $err['message'] )
			);
	return $service_finder_Errors = json_encode($error);
	} 
	
}

/* Mango pay Payout */
function service_finder_payout(  $wp_user_id = 0, $mp_account_id = 0, $order_id = 0, $currency = '', $amount = 0, $fees = 0 ){
		
		$mp_vendor_id	= service_finder_get_mp_vendor_id( $wp_user_id );
		
		$mp_vendor_wallet_id 		= service_finder_get_mp_wallet( $mp_vendor_id );
		
		if( !$mp_vendor_wallet_id ){
			return false;
		}	
		
		$mp_settings = service_finder_mangopay_settings();
		$prod_or_sandbox = $mp_settings['prod_or_sandbox'];
		
		if( $prod_or_sandbox == 'prod' ){
			$clientid = $mp_settings['prod_client_id'];
			$clientpassphrase = $mp_settings['prod_passphrase'];
		}else{
			$clientid = $mp_settings['sand_client_id'];
			$clientpassphrase = $mp_settings['sand_passphrase'];
		}
		
		$tmp_path = service_finder_get_mp_temp_path();
		
		$api = new MangoPay\MangoPayApi();
		$api->Config->ClientId = $clientid;
		$api->Config->ClientPassword = $clientpassphrase;
		$api->Config->TemporaryFolder = $tmp_path;
		
		$PayOut = new \MangoPay\PayOut();
		$PayOut->AuthorId								= $mp_vendor_id;
		$PayOut->DebitedWalletID						= $mp_vendor_wallet_id;
		$PayOut->DebitedFunds							= new \MangoPay\Money();
		$PayOut->DebitedFunds->Currency					= $currency;
		$PayOut->DebitedFunds->Amount					= round($amount * 100);
		$PayOut->Fees									= new \MangoPay\Money();
		$PayOut->Fees->Currency							= $currency;
		$PayOut->Fees->Amount							= round($fees * 100);
		$PayOut->PaymentType							= "BANK_WIRE";
		$PayOut->MeanOfPaymentDetails					= new \MangoPay\PayOutPaymentDetailsBankWire();
		$PayOut->MeanOfPaymentDetails->BankAccountId	= $mp_account_id;
		$PayOut->MeanOfPaymentDetails->BankWireRef				= 'ID ' . $order_id;
		$PayOut->Tag									= 'Commission for WC Order #' . $order_id . ' - ValidatedBy:' . wp_get_current_user()->user_login;
		
		$result = $api->PayOuts->Create($PayOut);
		
		
		return $result;
	}
	
/* get team members */
function service_finder_get_team_members($user_id = 0){
global $wpdb, $service_finder_Tables;

	$sql = $wpdb->prepare("SELECT * FROM ".$service_finder_Tables->team_members. " WHERE `admin_wp_id` = %d AND `is_admin` = 'no'",$user_id);

	$results = $wpdb->get_results($sql);
	
	return $results;
}	

/* Get total number of days */
function service_finder_get_total_offdays($type = '',$number = 0) {
	$days = 0;
	switch($type){
		case 'days':
			$days = intval($number);
			break; 
		case 'weeks':
			$days = intval($number) * 7;
			break; 
		case 'months':
			$days = intval($number) * 30;
			break; 	
		default:
			break;		
	}
	
	return $days;
}

/*Get off Days*/
add_action('wp_ajax_get_bookingdays', 'service_finder_get_bookingdays');
add_action('wp_ajax_nopriv_get_bookingdays', 'service_finder_get_bookingdays');
function service_finder_get_bookingdays(){
	$unavl_type = (!empty($_POST['unavl_type'])) ? esc_html($_POST['unavl_type']) : '';
	$numberofdays = (!empty($_POST['numberofdays'])) ? esc_html($_POST['numberofdays']) : '';
	$startdate = (!empty($_POST['startdate'])) ? esc_html($_POST['startdate']) : '';
	$blockdatearr = (!empty($_POST['datearr'])) ? $_POST['datearr'] : '';
	$daynumarr = (!empty($_POST['daynumarr'])) ? $_POST['daynumarr'] : '';
	$bookedarr = (!empty($_POST['bookedarr'])) ? $_POST['bookedarr'] : '';
	
	if(!empty($daynumarr)){
	
		foreach($daynumarr as $daynum){
			$daynumarrmatch[] = intval($daynum);
		}
	}
	
	$bookingdates = array();
		
	if($unavl_type != "" && $numberofdays != ""){
	$totaldays = service_finder_get_total_offdays($unavl_type,$numberofdays);
	$flag = 0;
	for($i = 0; $i < $totaldays; $i++){
		$datenum = date('w', strtotime(date('Y-m-d',strtotime($startdate. ' + '.$i.' days'))));
		$datenum = ($datenum == 0) ? 6 : intval($datenum) - 1;
		$daynumbers[] = intval($datenum);
		
		if(!in_array($datenum,$daynumarrmatch)){
			$flag = 1;
		}
		
		$bookingdates[] = date('Y-m-d',strtotime($startdate. ' + '.$i.' days'));
		$bookingdatesformatch[] = date('Y-n-d',strtotime($startdate. ' + '.$i.' days'));
	}
	
	}
	
	$result = array_intersect($blockdatearr,$bookingdatesformatch);
	$result2 = array_intersect($bookedarr,$bookingdatesformatch);
	
	if(empty($result) && empty($result2) && $flag == 0){
		$success = array(
			'status' => 'success',
			'bookingdates' => $bookingdates
		);
		echo json_encode($success);
	}else{
		$error = array(
			'status' => 'error',
			'err_message' => esc_html__('Please select continue available dates.', 'service-finder'),
			);
		echo json_encode($error);
	}

exit;
}

/*Get off Days*/
add_action('wp_ajax_get_editbookingdays', 'service_finder_get_editbookingdays');
add_action('wp_ajax_nopriv_get_editbookingdays', 'service_finder_get_editbookingdays');
function service_finder_get_editbookingdays(){
	$totalnumber = (!empty($_POST['totalnumber'])) ? esc_html($_POST['totalnumber']) : '';
	$startdate = (!empty($_POST['startdate'])) ? esc_html($_POST['startdate']) : '';
	$blockdatearr = (!empty($_POST['datearr'])) ? $_POST['datearr'] : '';
	
	$bookingdates = array();
		
	if($totalnumber > 0){

	for($i = 0; $i < $totalnumber; $i++){
		$bookingdates[] = date('Y-m-d',strtotime($startdate. ' + '.$i.' days'));
		$bookingdatesformatch[] = date('Y-n-d',strtotime($startdate. ' + '.$i.' days'));
	}
	
	}
	
	$result = array_intersect($blockdatearr,$bookingdatesformatch);
	
	if(empty($result)){
		$success = array(
			'status' => 'success',
			'bookingdates' => $bookingdates
		);
		echo json_encode($success);
	}else{
		$error = array(
			'status' => 'error',
			'err_message' => esc_html__('Please select continue available dates.', 'service-finder'),
			);
		echo json_encode($error);
	}

exit;
}

/*Get service type */
function service_finder_get_service_type($sid = 0){
global $wpdb, $service_finder_Tables;

	$row = $wpdb->get_row($wpdb->prepare('SELECT cost_type FROM '.$service_finder_Tables->services.' where `id` = %d',$sid));
	
	return $row->cost_type;
}

/*Get service name */
function service_finder_get_service_name($sid = 0){
global $wpdb, $service_finder_Tables;

	$row = $wpdb->get_row($wpdb->prepare('SELECT service_name FROM '.$service_finder_Tables->services.' where `id` = %d',$sid));
	
	return $row->service_name;
}

/* Month Dropdown */
function service_finder_month_dropdown($key = ''){

	$html = '<label>'.esc_html__('Select Month', 'service-finder').'</label>';
	$html .= '<select id="'.$key.'_month" name="'.$key.'_month" class="form-control sf-form-control sf-select-box" title="'.esc_html__('Select Month', 'service-finder').'">
    <option value="1">'.esc_html__('January', 'service-finder').'</option>
    <option value="2">'.esc_html__('February', 'service-finder').'</option>
    <option value="3">'.esc_html__('March', 'service-finder').'</option>
    <option value="4">'.esc_html__('April', 'service-finder').'</option>
    <option value="5">'.esc_html__('May', 'service-finder').'</option>
    <option value="6">'.esc_html__('June', 'service-finder').'</option>
    <option value="7">'.esc_html__('July', 'service-finder').'</option>
    <option value="8">'.esc_html__('August', 'service-finder').'</option>
    <option value="9">'.esc_html__('September', 'service-finder').'</option>
    <option value="10">'.esc_html__('October', 'service-finder').'</option>
    <option value="11">'.esc_html__('November', 'service-finder').'</option>
    <option value="12">'.esc_html__('December', 'service-finder').'</option>
    </select>';	
	return $html;
}

/* Month Dropdown */
function service_finder_year_dropdown($key = ''){

	$html = '<label>'.esc_html__('Select Year', 'service-finder').'</label>';
	$html .= '<select id="'.$key.'_year" name="'.$key.'_year" class="form-control sf-form-control sf-select-box"  title="'.esc_html__('Select Year', 'service-finder').'">';
			$year = date('Y');
			for($i = $year;$i<=$year+50;$i++){
				$html .= '<option value="'.esc_attr($i).'">'.$i.'</option>';
			}
    $html .= '</select>';
	return $html;
}

/* Local Payment Gatways */
function service_finder_site_payments($where = '',$args = array()){
global $wpdb, $service_finder_Tables, $service_finder_options, $paymentsystem, $current_user;

$payment_methods = (!empty($service_finder_options['payment-methods'])) ? $service_finder_options['payment-methods'] : '';
$falg = 0;
$html = '';

if($paymentsystem == 'woocommerce'){
    if(service_finder_getUserRole($current_user->ID) == 'administrator'){
    $html .= '<div class="col-lg-12 clear" id="'.$where.'skipoption">
    <div class="form-group form-inline">';
    $html .= '<div class="checkbox sf-radio-checkbox">
                    <input id="'.$where.'_skipforadmin" type="checkbox" name="'.$where.'_skipforadmin" value="yes">
                    <label for="'.$where.'_skipforadmin">'.esc_html__('Skip Payment','service-finder').'</label>
                    <input id="'.$where.'_skippayment" type="hidden" name="payment_mode" value="skippayment">
                </div>';
    $html .= '</div></div>';						
    }
    $html .= '<div class="col-md-6 clear">';
	if(!empty($args)){
		foreach($args as $key => $value){
			$html .= '<input type="hidden" name="'.$key.'" value="'.$value.'">';	
		}
	}
    $html .= '<input type="submit" class="btn btn-primary btn-block" name="'.$where.'-payment" value="'.esc_html__('Add to Wallet', 'service-finder').'" />';
    $html .= '</div>';
    }else{
    if(!empty($payment_methods)){
    $html .= '<div class="panel-body clear">
      <div class="row"><div class="form-group form-inline">';
    foreach($payment_methods as $key => $value){
    if($key != 'cod' && $key != 'payulatam'){
    if($value){
    $falg = 1;
    }
     if($key == 'stripe'){
	$label = '<img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/mastercard.jpg" title="'.esc_html__('Stripe','service-finder').'" alt="'.esc_html__('mastercard','service-finder').'"><img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/payment.jpg" title="'.esc_html__('Stripe','service-finder').'" alt="'.esc_html__('american express','service-finder').'"><img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/discover.jpg" title="'.esc_html__('Stripe','service-finder').'" alt="'.esc_html__('discover','service-finder').'"><img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/visa.jpg" title="'.esc_html__('Stripe','service-finder').'" alt="'.esc_html__('visa','service-finder').'">';
	}elseif($key == 'twocheckout'){
	 $label = '<img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/twocheckout.jpg" title="'.esc_html__('2Checkout','service-finder').'" alt="'.esc_html__('2Checkout','service-finder').'">';
	}elseif($key == 'wired'){
	$label = '<img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/wired.jpg" title="'.esc_html__('Wire Transfer','service-finder').'" alt="'.esc_html__('Wired','service-finder').'">';
	}elseif($key == 'payumoney'){
	 $label = '<img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/payumoney.jpg" title="'.esc_html__('PayU Money','service-finder').'" alt="'.esc_html__('PayU Money','service-finder').'">';
	}else{
	$label = '<img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/paypal.jpg" title="'.esc_html__('Paypal','service-finder').'" alt="'.esc_html__('Paypal','service-finder').'">';
	}
    if($value == 1){
        $html .= '<div class="radio sf-radio-checkbox">
                    <input id="'.$where.'_'.$key.'" type="radio" name="payment_mode" value="'.$key.'">
                    <label for="'.$where.'_'.esc_attr($key).'">'.$label.'</label>
                </div>';	
    }
    }
    }
    
    if(service_finder_getUserRole($current_user->ID) == 'administrator'){
	$falg = 1;
    $html .= '<div class="radio sf-radio-checkbox">
                    <input id="'.$where.'_skippayment" type="radio" name="payment_mode" value="skippayment">
                    <label for="'.$where.'_skippayment">'.esc_html__('Skip Payment','service-finder').'</label>
                </div>';
    }
    $html .= '</div></div>
      </div>';
    ?>
    <?php if($falg == 1){               
    $html .= '<div id="'.$where.'cardinfo" class="default-hidden">
    <div class="col-md-8">
    <div class="form-group">
    <label>'
    .esc_html__('Card Number', 'service-finder').
    '</label>
    <div class="input-group"> <i class="input-group-addon fa fa-credit-card"></i>
    <input type="text" id="'.$where.'_number" name="'.$where.'_number" class="form-control">
    </div>
    </div>
    </div>
    <div class="col-md-4">
    <div class="form-group">
    <label>'
    .esc_html__('CVC', 'service-finder').
    '</label>
    <div class="input-group"> <i class="input-group-addon fa fa-ellipsis-h"></i>
    <input type="text" id="'.$where.'_cvc" name="'.$where.'_cvc" class="form-control">
    </div>
    </div>
    </div>
    <div class="col-md-6">
    <div class="form-group">'
    .service_finder_month_dropdown('wallet').
    '</div>
    </div>
    <div class="col-md-6">
    <div class="form-group">'
	.service_finder_year_dropdown('wallet').
    '</div>
    </div>
    </div>
    <div id="twocheckout_'.$where.'cardinfo" class="default-hidden">
    <div class="col-md-8">
    <div class="form-group">
    <label>
    '.esc_html__('Card Number', 'service-finder').'
    </label>
    <div class="input-group"> <i class="input-group-addon fa fa-credit-card"></i>
    <input type="text" id="twocheckout_'.$where.'_number" name="twocheckout_'.$where.'_number" class="form-control">
    </div>
    </div>
    </div>
    <div class="col-md-4">
    <div class="form-group">
    <label>
    '.esc_html__('CVC', 'service-finder').'
    </label>
    <div class="input-group"> <i class="input-group-addon fa fa-ellipsis-h"></i>
    <input type="text" id="twocheckout_'.$where.'_cvc" name="twocheckout_'.$where.'_cvc" class="form-control">
    </div>
    </div>
    </div>
    <div class="col-md-6">
    <div class="form-group">
    '.service_finder_month_dropdown('twocheckout_wallet').'
    </div>
    </div>
    <div class="col-md-6">
    <div class="form-group">
	'.service_finder_year_dropdown('twocheckout_wallet').'
    </div>
    </div>
    </div>
    <div id="'.$where.'wiredinfo" class="default-hidden">
    <div class="col-md-12 margin-b-20">';
    $description = (!empty($service_finder_options['wire-transfer-description'])) ? $service_finder_options['wire-transfer-description'] : '';
    $html .= $description;
    $html .= '</div>
    </div>
    <div class="col-md-6">';
	
	if(!empty($args)){
		foreach($args as $key => $value){
			$html .= '<input type="hidden" name="'.$key.'" value="'.$value.'">';	
		}
	}
	
    $html .= '<input type="submit" class="btn btn-primary btn-block" name="'.$where.'-payment" value="'.esc_html__('Add to Wallet', 'service-finder').'" />
    </div>';
	
    }else{
    $html .= '<div class="alert alert-warning">'.esc_html__('There is no payment gateway available.', 'service-finder').' </div>';
    }
    }
    }
    return $html;
}

function service_finder_cashback_amount($transaction_type = ''){
global $service_finder_options;

$applytoall = (!empty($service_finder_options['wallet-cashback-all-transaction'])) ? $service_finder_options['wallet-cashback-all-transaction'] : false;

if($applytoall == true){
$amount = (!empty($service_finder_options['cashback-amount'])) ? $service_finder_options['cashback-amount'] : 0;
$description = (!empty($service_finder_options['cashback-description'])) ? $service_finder_options['cashback-description'] : '';

if($description == ''){
switch($transaction_type){
	case 'upgrade':
			$description = esc_html__('Cashback after upgrade account', 'service-finder');
			break;
	case 'featured':
			$description = esc_html__('Cashback after featured account', 'service-finder');
			break;		

	case 'job-apply-limit':
			$description = esc_html__('Cashback after purchase job connect', 'service-finder');
			break;
	case 'booking':
			$description = esc_html__('Cashback after booking', 'service-finder');
			break;
	case 'invoice':
			$description = esc_html__('Cashback after invoice pay', 'service-finder');
			break;		
	case 'job-post-limit':
			$description = esc_html__('Cashback after purchase job post limits', 'service-finder');
			break;				
}
}

}else{

switch($transaction_type){
	case 'upgrade':
			$amount = (!empty($service_finder_options['upgrade-cashback-amount'])) ? $service_finder_options['upgrade-cashback-amount'] : 0;
			$description = (!empty($service_finder_options['upgrade-cashback-description'])) ? $service_finder_options['upgrade-cashback-description'] : esc_html__('Cashback after upgrade account', 'service-finder');
			break;
	case 'featured':
			$amount = (!empty($service_finder_options['featured-cashback-amount'])) ? $service_finder_options['featured-cashback-amount'] : 0;
			$description = (!empty($service_finder_options['featured-cashback-description'])) ? $service_finder_options['featured-cashback-description'] : esc_html__('Cashback after featured account', 'service-finder');
			break;		
	case 'job-apply-limit':
			$amount = (!empty($service_finder_options['job-apply-limit-cashback-amount'])) ? $service_finder_options['job-apply-limit-cashback-amount'] : 0;
			$description = (!empty($service_finder_options['job-apply-limit-cashback-description'])) ? $service_finder_options['job-apply-limit-cashback-description'] : esc_html__('Cashback after purchase job connect', 'service-finder');
			break;
	case 'booking':
			$amount = (!empty($service_finder_options['booking-cashback-amount'])) ? $service_finder_options['booking-cashback-amount'] : 0;
			$description = (!empty($service_finder_options['booking-cashback-description'])) ? $service_finder_options['booking-cashback-description'] : esc_html__('Cashback after booking', 'service-finder');
			break;
	case 'invoice':
			$amount = (!empty($service_finder_options['invoice-cashback-amount'])) ? $service_finder_options['invoice-cashback-amount'] : 0;
			$description = (!empty($service_finder_options['invoice-cashback-description'])) ? $service_finder_options['invoice-cashback-description'] : esc_html__('Cashback after invoice pay', 'service-finder');
			break;		
	case 'job-post-limit':
			$amount = (!empty($service_finder_options['job-post-limit-cashback-amount'])) ? $service_finder_options['job-post-limit-cashback-amount'] : 0;
			$description = (!empty($service_finder_options['job-post-limit-cashback-description'])) ? $service_finder_options['job-post-limit-cashback-description'] : esc_html__('Cashback after purchase job post limits', 'service-finder');
			break;				
}

}
$return = array(
	'amount' => $amount,
	'description' => $description,
);

return $return;
}

/*Add wallet payment option*/
function service_finder_add_wallet_option($varname = '',$key = ''){
global $service_finder_options, $current_user;

$walletsystem = (!empty($service_finder_options['wallet-system'])) ? $service_finder_options['wallet-system'] : 0;

$html = '';
if($walletsystem == true){
if(service_finder_getUserRole($current_user->ID) == 'Provider' || service_finder_getUserRole($current_user->ID) == 'Customer'){
$html .= '<div class="radio sf-radio-checkbox sf-payments-outer">
                  <input type="radio" value="wallet" name="'.$varname.'" id="'.$key.'_wallet" >
                  <label for="'.$key.'_wallet"><img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/wallet.jpg" title="'.esc_html__('Wallet','service-finder').'" alt="'.esc_html__('Wallet','service-finder').'"></label>
                </div>';
}
}				
return $html;
}

/*Add wallet payment option*/
function service_finder_add_skip_option($varname = '',$key = ''){
global $service_finder_options, $current_user;

$html = '';
if(is_user_logged_in()){
if(service_finder_getUserRole($current_user->ID) == 'Provider' || service_finder_getUserRole($current_user->ID) == 'administrator'){
$html .= '<div class="radio sf-radio-checkbox">
                  <input type="radio" value="skippayment" name="'.$varname.'" id="'.$key.'_skippayment" >
                  <label for="'.$key.'_skippayment">'.esc_html__('Skip Payment','service-finder').'</label>
                </div>';
}				
}
return $html;
}

/*Add woo-commerce payment option*/
function service_finder_add_woo_commerce_option($varname = '',$key = ''){
global $service_finder_options, $current_user;

$walletsystem = (!empty($service_finder_options['wallet-system'])) ? $service_finder_options['wallet-system'] : 0;

$html = '';
if($walletsystem == true || service_finder_getUserRole($current_user->ID) == 'administrator'){
$html .= '<div class="radio sf-radio-checkbox sf-payments-outer">
                  <input type="radio" value="woopayment" name="'.$varname.'" id="'.$key.'_woopayment" >
                  <label for="'.$key.'_woopayment">'.esc_html__('Checkout','service-finder').'</label>
				  <img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/woopayment.jpg" alt="'.esc_html__('Checkout','service-finder').'">
                </div>';
}				
return $html;
}

/*Dislay wallet amount*/
function service_finder_check_wallet_system(){
global $service_finder_options;

$walletsystem = (!empty($service_finder_options['wallet-system'])) ? $service_finder_options['wallet-system'] : false;

return $walletsystem;
}

/*Dislay wallet amount*/
function service_finder_display_wallet_amount($user_id = 0){
global $service_finder_options, $current_user;

$walletsystem = (!empty($service_finder_options['wallet-system'])) ? $service_finder_options['wallet-system'] : false;

$html = '';
if($walletsystem == true){
if(service_finder_getUserRole($current_user->ID) == 'Provider' || service_finder_getUserRole($current_user->ID) == 'Customer'){
$walletamount = service_finder_get_wallet_amount($user_id);

$html .= '<ul class="list-unstyled clear">
                        <li>
                            <h5>'.esc_html__('Wallet Balance', 'service-finder').'</h5>
                            <strong>
                            '.service_finder_money_format($walletamount).'</strong>
                        </li>
                    </ul>';
}				
}
return $html;
}

/*Check offer system*/
function service_finder_check_offer_system(){
global $service_finder_options;

$offerssystem = (!empty($service_finder_options['offers-system'])) ? $service_finder_options['offers-system'] : false;

return $offerssystem;
}

/*Get My account url*/
function service_finder_get_my_account_url($userid = 0){
global $service_finder_options, $current_user;

if(service_finder_getUserRole($current_user->ID) == 'administrator'){
$url = add_query_arg( array('manageaccountby' => 'admin','manageproviderid' => esc_attr($userid)), service_finder_get_url_by_shortcode('[service_finder_my_account') );
}else{
$url = service_finder_get_url_by_shortcode('[service_finder_my_account]');
}

return $url;
}

/*Get notification link*/
function service_finder_get_notification_link($topic = '',$target_id = ''){
global $service_finder_options, $current_user;

$url = '';
switch($topic){
	case 'Booking': 	
		$url = service_finder_get_my_account_url($current_user->ID);
		$url = add_query_arg( array('tabname' => 'bookings','bookingid' => esc_attr($target_id)), $url );
		break;
	case 'Booking Edited': 	
		$url = service_finder_get_my_account_url($current_user->ID);
		$url = add_query_arg( array('tabname' => 'bookings','bookingid' => esc_attr($target_id)), $url );
		break;
	case 'Service Complete':
	case 'Service Incomplete':
	case 'Booking Complete':
	case 'Booking Completed': 	
		$url = service_finder_get_my_account_url($current_user->ID);
		if(service_finder_getUserRole($current_user->ID) == 'Provider'){
		$url = add_query_arg( array('tabname' => 'bookings','bookingid' => esc_attr($target_id)), $url );
		}elseif(service_finder_getUserRole($current_user->ID) == 'Customer'){
		$url = add_query_arg( array('action' => 'bookings','bookingid' => esc_attr($target_id)), $url );
		}
		break;	
	case 'Job Apply Connect': 	
		$url = service_finder_get_my_account_url($current_user->ID);
		$url = add_query_arg( array('tabname' => 'job-limits'), $url );
		break;
	case 'Job Application': 	
		$url = service_finder_get_url_by_shortcode('[job_dashboard');
		break;	
	case 'Feature Request Approved': 	
		$url = service_finder_get_my_account_url($current_user->ID);
		$url = add_query_arg( array('tabname' => 'upgrade#feature-req-bx'), $url );
		break;
	case 'Job Post Connect': 	
		$url = service_finder_get_my_account_url($current_user->ID);
		$url = add_query_arg( array('action' => 'job-post-plans'), $url );
		break;
	case 'Generate Invoice': 	
		$url = service_finder_get_my_account_url($current_user->ID);
		$url = add_query_arg( array('action' => 'invoice','invoiceid' => esc_attr($target_id)), $url );
		break;
	case 'Invoice Paid': 	
		$url = service_finder_get_my_account_url($current_user->ID);
		$url = add_query_arg( array('tabname' => 'invoice','invoiceid' => esc_attr($target_id)), $url );
		break;
	case 'Quote Response':
		$quoteapplicantspage = service_finder_get_url_by_shortcode('[service_finder_quotation_replies');
		$url = add_query_arg( array('quoteid' => $target_id ),$quoteapplicantspage );
		break;
	case 'Get Quotation': 	
		$url = service_finder_get_my_account_url($current_user->ID);
		$url = add_query_arg( array('tabname' => 'quotation','quoteid' => esc_attr($target_id)), $url );
		break;	
	case 'Question': 	
		$url = get_permalink($target_id);
		break;
	case 'Answer Submitted': 	
		$url = get_the_permalink($target_id);
		break;
	case 'Job Published': 	
		$jobapplicantspage = service_finder_get_url_by_shortcode('[service_finder_job_applicants');
		$url = add_query_arg( array('jobid' => $target_id ),$jobapplicantspage );
		break;	
	case 'Job Invitation': 	
		$url = get_permalink($target_id);
		break;	
	case 'Identity Declined': 	
		$url = 'javascript:;';
		break;
	case 'Identity Approved': 	
		$url = 'javascript:;';
		break;							
	default: 	
		$url = 'javascript:;';
		break;
					
}

return $url;
}

/*total income from bookings*/
function service_finder_get_booking_earnings($userid = 0){
global $wpdb,$service_finder_Tables;
$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE provider_id = %d AND (status = "Pending" OR status = "Completed") AND paid_to_provider = "paid"',$userid));

$totalincome = 0;
if(!empty($results)){
foreach($results as $row){
$total = $row->total;
$adminfee = $row->adminfee;
$discount = $row->discount;

$income = floatval($total) - (floatval($adminfee) + floatval($discount));

$totalincome = floatval($totalincome) + floatval($income);

}
}

return $totalincome;

}

/*total income from invoice*/
function service_finder_get_invoice_earnings($userid = 0){
global $wpdb,$service_finder_Tables;
$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->invoice.' WHERE provider_id = %d AND status = "paid" AND paid_to_provider = "paid"',$userid));

$totalincome = 0;
if(!empty($results)){
foreach($results as $row){
$total = $row->total;

$totalincome = floatval($totalincome) + floatval($total);

}
}

return $totalincome;

}

/*total total earnings*/
function service_finder_get_total_earnings($userid = 0){

$totalincome = 0;
$bookingincome = service_finder_get_booking_earnings($userid);
$invoiceincome = service_finder_get_invoice_earnings($userid);

$totalincome = floatval($bookingincome) + floatval($invoiceincome);

return $totalincome;

}

/*get booking dues*/
function service_finder_get_booking_dues($userid = 0){
global $wpdb,$service_finder_Tables;
$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE provider_id = %d AND status != "Cancel" AND (status = "Need-Approval" OR paid_to_provider = "pending")',$userid));

$totaldues = 0;
if(!empty($results)){
foreach($results as $row){
$total = $row->total;
$adminfee = $row->adminfee;
$discount = $row->discount;

$dues = floatval($total) - (floatval($adminfee) + floatval($discount));

$totaldues = floatval($totaldues) + floatval($dues);

}
}

return $totaldues;

}

/*get invoice dues*/
function service_finder_get_invoice_dues($userid = 0){
global $wpdb,$service_finder_Tables;
$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->invoice.' WHERE provider_id = %d AND (status = "pending" OR paid_to_provider = "pending")',$userid));

$totaldues = 0;
if(!empty($results)){
foreach($results as $row){
$total = $row->total;

$totaldues = floatval($totaldues) + floatval($total);

}
}

return $totaldues;

}

/*total total earnings*/
function service_finder_get_total_dues($userid = 0){

$totaldues = 0;
$bookingdues = service_finder_get_booking_dues($userid);
$invoicedues = service_finder_get_invoice_dues($userid);

$totaldues = floatval($bookingdues) + floatval($invoicedues);

return $totaldues;

}

function service_finder_day_translate($day = ''){
	switch($day){
	case 'monday':
		$dayname = esc_html__('Monday','service-finder');
		break;
	case 'tuesday':
		$dayname = esc_html__('Tuesday','service-finder');
		break;
	case 'wednesday':
		$dayname = esc_html__('Wednesday','service-finder');
		break;
	case 'thursday':
		$dayname = esc_html__('Thursday','service-finder');
		break;
	case 'friday':
		$dayname = esc_html__('Friday','service-finder');
		break;
	case 'saturday':
		$dayname = esc_html__('Saturday','service-finder');
		break;
	case 'sunday':
		$dayname = esc_html__('Sunday','service-finder');
		break;						
	}
	
	return $dayname;
}

/*Get total coupon code used in booking*/
function service_finder_total_service_coupon($couponcode = '',$userid = 0,$serviceid = 0){
global $wpdb,$service_finder_Tables;

$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `provider_id` = %d',$userid));	
$cnt = 1;
if(!empty($results)){
	foreach($results as $row){
		$services = $row->services;
		$services = explode('%%',$services);
		if($row->multi_date == 'yes'){
			$service = explode('-',$services);
			if($service[0] == $serviceid && $service[3] == $couponcode){
				$cnt++;
			}
		}else{
			$service = explode('||',$services);
			if($services[0] == $serviceid && $services[6] == $couponcode){
				$cnt++;
			}	
		}
	}
}

return $cnt;
}

/*Get total coupon code used in booking*/
function service_finder_total_booking_coupon($couponcode = '',$userid = 0){
global $wpdb,$service_finder_Tables;

$row = $wpdb->get_row($wpdb->prepare('SELECT count(id) as cnt FROM '.$service_finder_Tables->bookings.' WHERE `provider_id` = %d AND `coupon_code` = %s',$userid,$couponcode));	

return $row->cnt;
}

/*Get total coupon code used in booking*/
function service_finder_check_is_couponcode_used($couponcode = '',$userid = 0){
global $wpdb,$service_finder_Tables,$current_user;

$row = $wpdb->get_row($wpdb->prepare('SELECT count(`bookings`.`id`) as bookingcount FROM '.$service_finder_Tables->bookings.' as bookings INNER JOIN '.$service_finder_Tables->customers.' as customers on bookings.booking_customer_id = customers.id WHERE `bookings`.`provider_id` = %d AND `bookings`.`coupon_code` = %s AND `customers`.`wp_user_id` = %d',$userid,$couponcode,$current_user->ID));

return $row->bookingcount;
}

/*Get total coupon code used in booking*/
function service_finder_check_is_service_couponcode_used($couponcode = '',$userid = 0,$serviceid = 0){
global $wpdb,$service_finder_Tables,$current_user;

$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `provider_id` = %d AND `customer_id` = %s',$userid,$current_user->ID));	
$cnt = 0;
if(!empty($results)){
	foreach($results as $row){
		$services = $row->services;
		$services = explode('%%',$services);
		if(!empty($services)){
			foreach($services as $servicesitem){
			
				if($row->multi_date == 'no'){
					$serviceitemchunk = explode('-',$servicesitem);
					if($serviceitemchunk[0] == $serviceid && $serviceitemchunk[3] == $couponcode){
						$cnt++;
					}
				}else{
					$serviceitemchunk = explode('||',$servicesitem);
					if($serviceitemchunk[0] == $serviceid && $serviceitemchunk[6] == $couponcode){
						$cnt++;
					}	
				}
			}
		}	
	}
}

return $cnt;
}


/*Get user image url*/
function service_finder_get_user_profile_image($avatar_id = 0,$size = 'thumbnail'){
$src = '';
if(!empty($avatar_id) && $avatar_id > 0){
$src  = wp_get_attachment_image_src( $avatar_id, $size );
$src  = $src[0];

$src = (!empty($src)) ? $src : '';
}

return $src;
}

/*Get providers customer list*/
function service_finder_get_providers_customer($user_id = 0){
global $wpdb,$service_finder_Tables;

$results = $wpdb->get_results($wpdb->prepare('SELECT `customers`.`wp_user_id` as customerid, `customers`.`name` as customer_name FROM '.$service_finder_Tables->bookings.' as bookings INNER JOIN '.$service_finder_Tables->customers.' as customers on bookings.booking_customer_id = customers.id WHERE `bookings`.`provider_id` = %d GROUP BY `customers`.`email`',$user_id));

return $results;
}

/*Get stripe connection*/
function service_finder_stripe_connection(){
global $wpdb, $service_finder_Errors,$service_finder_options;

require_once(SERVICE_FINDER_PAYMENT_GATEWAY_DIR.'/stripe/init.php');

if( isset($service_finder_options['stripe-type']) && $service_finder_options['stripe-type'] == 'test' ){
	$secret_key = (!empty($service_finder_options['stripe-test-secret-key'])) ? $service_finder_options['stripe-test-secret-key'] : '';
	$public_key = (!empty($service_finder_options['stripe-test-public-key'])) ? $service_finder_options['stripe-test-public-key'] : '';
}else{
	$secret_key = (!empty($service_finder_options['stripe-live-secret-key'])) ? $service_finder_options['stripe-live-secret-key'] : '';
	$public_key = (!empty($service_finder_options['stripe-live-public-key'])) ? $service_finder_options['stripe-live-public-key'] : '';
}

return array(
	'secret_key' => $secret_key,
	'public_key' => $public_key
);
}

/*Get dayname by day number*/
function service_finder_get_dayname_by_daynumber($number = ''){
	$days = array(
        0 => esc_html__('Monday','service-finder'),
        1 => esc_html__('Tuesday','service-finder'),
        2 => esc_html__('Wednesday','service-finder'),
        3 => esc_html__('Thursday','service-finder'),
        4 => esc_html__('Friday','service-finder'),
        5 => esc_html__('Saturday','service-finder'),
		6 => esc_html__('Sunday','service-finder')
    );
	return isset( $days[ $number ] ) ? $days[ $number ] : '';
}

/*Get dayname by day number*/
function service_finder_get_slot_interval($user_id = 0){
	$settings = service_finder_getProviderSettings($user_id);
	$slot_interval = (!empty($settings['slot_interval'])) ? $settings['slot_interval'] : '';
	
	if($slot_interval == '15'){
	$slot_interval = 15;
	}else{
	$slot_interval = 30;
	}
	return $slot_interval;
}

/*Get dayname by day number*/
function service_finder_get_weekdays(){
	
	$days = array('monday','tuesday','wednesday','thursday','friday','saturday','sunday');
	
	return $days;
}

/*Get future booking availabilities*/
function service_finder_get_future_bookings_availabilities(){
	
	$availabilities = array(
			1 => esc_html__('1 Day', 'service-finder'),
			2 => esc_html__('2 Days', 'service-finder'),
			3 => esc_html__('3 Days', 'service-finder'),
			4 => esc_html__('4 Days', 'service-finder'),
			5 => esc_html__('5 Days', 'service-finder'),
			6 => esc_html__('6 Days', 'service-finder'),
			7 => esc_html__('1 Week', 'service-finder'),
			14 => esc_html__('2 Weeks', 'service-finder'),
			21 => esc_html__('3 Weeks', 'service-finder'),
			28 => esc_html__('4 Weeks', 'service-finder'),
			30 => esc_html__('1 Month', 'service-finder'),
			60 => esc_html__('2 Months', 'service-finder'),
			90 => esc_html__('3 Months', 'service-finder'),
			120 => esc_html__('4 Months', 'service-finder'),
			150 => esc_html__('5 Months', 'service-finder'),
			180 => esc_html__('6 Months', 'service-finder'),
			210 => esc_html__('7 Months', 'service-finder'),
			240 => esc_html__('8 Months', 'service-finder'),
			270 => esc_html__('9 Months', 'service-finder'),
			300 => esc_html__('10 Months', 'service-finder'),
			330 => esc_html__('11 Months', 'service-finder'),
			365 => esc_html__('1 Year', 'service-finder'),
	);
	
	return $availabilities;
}

/*Get date range for dates array*/
function service_finder_date_range($startdate = '', $enddate = '', $format = "Y-m-d"){

    $begin = new DateTime($startdate);
    $end = new DateTime($enddate);
	$end = $end->modify( '+1 day' );

    $interval = new DateInterval('P1D');
    $dateRange = new DatePeriod($begin, $interval, $end);

    $range = [];
    foreach ($dateRange as $date) {
        $range[] = $date->format($format);
    }

    return $range;
}

/*Get disbaled dates for zabuto calendar*/
function service_finder_get_disabled_dates($userid = 0){

    $settings = service_finder_getProviderSettings($userid);

	$future_bookings_availability = (!empty($settings['future_bookings_availability'])) ? $settings['future_bookings_availability'] : 365;
	
	$number_of_months = ($future_bookings_availability/30);
	
	if($number_of_months < 1){
	$lastdate = date('Y-m-d', strtotime("+".$future_bookings_availability." days", time()));
	}else{
	$lastdate = date('Y-m-d', strtotime("+".$number_of_months." months", time()));
	}
	
	$lastdate = date('Y-m-d', strtotime("+1 day", strtotime($lastdate)));
	$monthlastdate = date("Y-m-t", strtotime($lastdate));
	
	$disabledates = service_finder_date_range($lastdate, $monthlastdate, "Y-m-j");

    return $disabledates;
}

/*Get service padding time*/
function service_finder_get_service_paddind_time($serviceid = 0){
global $wpdb, $service_finder_Tables;

	$service = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->services.' WHERE `id` = %d',$serviceid));

	if(!empty($service)){
	$paddingtime = array(
		'before_padding_time' => $service->before_padding_time,
		'after_padding_time'  => $service->after_padding_time,
	); 
	
	return $paddingtime;
	}

}

/*Sort by booking feature*/
function service_finder_sort_by_booking_feature($providers = array()){
global $wpdb, $service_finder_Tables;

$available = array();
$unavailable = array();

if(!empty($providers)){
	foreach($providers as $provider){
		$provider_id = $provider->wp_user_id;
		
		$userCap = service_finder_get_capability($provider_id);
		
		if(!empty($userCap)){
		if(in_array('bookings',$userCap)){
			$settings = service_finder_getProviderSettings($provider_id);
		
			if($settings['booking_process'] == 'on'){
				$available[] = $provider_id;
			}else{
				$unavailable[] = $provider_id;	
			}
			
		}else{
			$unavailable[] = $provider_id;
		}
		}else{
			$unavailable[] = $provider_id;
		}
	}
}

$return = array(
		'available' => $available,
		'unavailable' => $unavailable
	);

return $return;

}

/*Sort by avaialbility*/
function service_finder_sort_by_availability($providers = array(),$date = '',$starttime = '',$endtime = '',$minuts = '',$srhperiod = ''){
global $wpdb, $service_finder_Tables;

$available = array();
$unavailable = array();

if(!empty($providers)){
	foreach($providers as $provider){
		$provider_id = $provider->wp_user_id;
		
		$userCap = service_finder_get_capability($provider_id);
		
		if(!empty($userCap)){
		if(in_array('bookings',$userCap)){
			$settings = service_finder_getProviderSettings($provider_id);
		
			if($settings['booking_process'] == 'on'){
				
				if(service_finder_availability_method($provider_id) == 'timeslots'){
					$availability = service_finder_sort_by_availability_timeslot($provider_id,$date,$starttime,$endtime,$minuts,$srhperiod);
				}elseif(service_finder_availability_method($provider_id) == 'starttime'){
					$availability = service_finder_sort_by_availability_starttime($provider_id,$date,$starttime,$endtime,$minuts,$srhperiod);
				}else{
					$availability = service_finder_sort_by_availability_timeslot($provider_id,$date,$starttime,$endtime,$minuts,$srhperiod);
				}
				
				if($availability == 1){
					$available[] = $provider_id;
				}elseif($availability == 0){
					$unavailable[] = $provider_id;
				}
				
			}else{
				$unavailable[] = $provider_id;	
			}
			
		}else{
			$unavailable[] = $provider_id;
		}
		}else{
			$unavailable[] = $provider_id;
		}
		
	}
}

$return = array(
		'available' => $available,
		'unavailable' => $unavailable
	);

return $return;

}

/*Sort by avaialbility*/
function service_finder_sort_by_availability_starttime($provider_id = 0,$date = '',$starttime = '',$endtime = '',$minuts = '',$srhperiod = ''){
global $wpdb, $service_finder_Tables;

$dayname = date('l', strtotime( $date ));
$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->starttime.' AS starttime WHERE `starttime`.`provider_id` = %d AND `starttime`.`day` = "%s"',$provider_id,strtolower($dayname)));

$flag = 0;

if(!empty($results)){
	foreach($results as $row){
	
		$slotendtime = date('H:i:s', strtotime($row->start_time." +".$minuts." minutes"));
		
		$totalbookings = service_finder_get_provider_availability( $provider_id,$date,$row->start_time,$slotendtime );	
		$chkunavailability = service_finder_get_provider_unavailability( $provider_id,$date,$starttime );
		
		if($row->max_bookings > $totalbookings && $chkunavailability == 0) {		
			
			$slotstarttimestamp = strtotime($date.' '.$row->start_time);
			$slotendtimestamp = strtotime($date.' '.$slotendtime);

			$srhstarttimestamp = strtotime($date.' '.$starttime);
			$srhendtimestamp = strtotime($date.' '.$endtime);
			
			if($srhperiod == 'any'){
			if($slotstarttimestamp > current_time( 'timestamp' )){
			$flag = 1;
			}
			}else{
			if($slotstarttimestamp > current_time( 'timestamp' ) && ($srhstarttimestamp <= $slotstarttimestamp && $slotstarttimestamp < $srhendtimestamp) && ($srhstarttimestamp < $slotendtimestamp && $slotendtimestamp <= $srhendtimestamp)){
			$flag = 1;
			}
			}
		}
	}
}

return $flag;

}

/*Get provider avaialbility*/
function service_finder_get_provider_availability($provider_id = 0,$date = '',$starttime = '',$endtime = ''){
global $wpdb, $service_finder_Tables;	
	
$result = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings LEFT JOIN '.$service_finder_Tables->booked_services.' as bookedservices on bookings.id = bookedservices.booking_id WHERE `bookings`.`status` != "Cancel" AND `bookings`.`provider_id` = %d AND ((`bookings`.`multi_date` = "yes" AND `bookedservices`.`date` = "%s" AND (`bookedservices`.`start_time` > "%s" AND `bookedservices`.`start_time` < "%s" OR (`bookedservices`.`end_time` > "%s" AND `bookedservices`.`end_time` < "%s") OR (`bookedservices`.`start_time` < "%s" AND `bookedservices`.`end_time` > "%s") OR (`bookedservices`.`start_time` = "%s" OR `bookedservices`.`end_time` = "%s") )) OR (`bookings`.`multi_date` = "no" AND `bookings`.`date` = "%s" AND (`bookings`.`start_time` > "%s" AND `bookings`.`start_time` < "%s" OR (`bookings`.`end_time` > "%s" AND `bookings`.`end_time` < "%s") OR (`bookings`.`start_time` < "%s" AND `bookings`.`end_time` > "%s") OR (`bookings`.`start_time` = "%s" OR `bookings`.`end_time` = "%s") )))',$provider_id,$date,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime,$date,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime));

$totalrows = count($result);

return $totalrows;
}

/*Get provider unavaialbility*/
function service_finder_get_provider_unavailability($provider_id = 0,$date = '',$starttime = ''){
global $wpdb,$service_finder_Tables;

$result = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' AS unavl WHERE `unavl`.`date` = "%s" AND availability_method = "starttime" AND `unavl`.`single_start_time` = "%s" AND `unavl`.`provider_id` = %d',$date,$starttime,$provider_id));

$totalrows = count($result);

return $totalrows;
	
}

/*Sort by avaialbility*/
function service_finder_sort_by_availability_timeslot($provider_id = 0,$date = '',$starttime = '',$endtime = '',$minuts = '',$srhperiod = ''){
global $wpdb, $service_finder_Tables;

$dayname = date('l', strtotime( $date ));

$flag = 0;

$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->timeslots.' AS timeslots WHERE (SELECT COUNT(*) FROM '.$service_finder_Tables->bookings.' AS bookings LEFT JOIN '.$service_finder_Tables->booked_services.' as bookedservices on bookings.id = bookedservices.booking_id WHERE `bookings`.`status` != "Cancel" AND (`bookings`.`multi_date` = "yes" AND `bookedservices`.`date` = "%s" AND `bookedservices`.`start_time` = `timeslots`.`start_time` AND `bookedservices`.`end_time` = `timeslots`.`end_time`) OR (`bookings`.`multi_date` = "no" AND `bookings`.`date` = "%s" AND `bookings`.`start_time` = `timeslots`.`start_time` AND `bookings`.`end_time` = `timeslots`.`end_time`)) < `timeslots`.`max_bookings` AND (SELECT COUNT(*) FROM '.$service_finder_Tables->unavailability.' AS unavl WHERE `unavl`.`date` = "%s" AND  `unavl`.availability_method = "timeslots" AND `unavl`.`start_time` = `timeslots`.`start_time` AND `unavl`.`end_time` = `timeslots`.`end_time`) = 0 AND `timeslots`.`provider_id` = %d AND `timeslots`.`day` = "%s"',$date,$date,$date,$provider_id,strtolower($dayname)));

if(!empty($results)){
	foreach($results as $slot){
		$qry = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE `date` = "%s" AND availability_method = "timeslots" AND start_time = "%s" AND end_time = "%s" AND provider_id = %d',$date,$slot->start_time,$slot->end_time,$provider_id));
		
		if(empty($qry)){
		$slotstarttimestamp = strtotime($date.' '.$slot->start_time);
		$slotendtimestamp = strtotime($date.' '.$slot->end_time);
		$diff = $slotendtimestamp - $slotstarttimestamp;
		$diff = abs($diff);
		$slotperiod = floor($diff / 60);

		$srhstarttimestamp = strtotime($date.' '.$starttime);
		$srhendtimestamp = strtotime($date.' '.$endtime);
		
		if($srhperiod == 'any' || $srhperiod == ''){
		if($slotstarttimestamp > current_time( 'timestamp' ) && $slotperiod >= $minuts){
			$flag = 1;
		}
		}else{
		if($slotstarttimestamp > current_time( 'timestamp' ) && ($srhstarttimestamp <= $slotstarttimestamp && $slotstarttimestamp < $srhendtimestamp) && ($srhstarttimestamp < $slotendtimestamp && $slotendtimestamp <= $srhendtimestamp) && $slotperiod >= $minuts){
			$flag = 1;
		}
		}
		}
	}
}

return $flag;

}


/*Get start and end time by search period*/
function service_finder_get_search_period($srhperiod = ''){

	switch($srhperiod){
		case 'morning':
			$starttime = '06:00:00';
			$endtime = '12:00:00';
			break;
		case 'afternoon':
			$starttime = '12:00:00';
			$endtime = '17:00:00';
			break;
		case 'evening':
			$starttime = '17:00:00';
			$endtime = '21:00:00';
			break;
		default:
			$starttime = '';
			$endtime = '';
			break;
	}
	
	$return = array(
		'starttime' => $starttime,
		'endtime' => $endtime
	);
	
	return $return;
}

/*Get start and end time by search period*/
function service_finder_availability_label($providerid = 0,$providersavailability = array()){
	
	$html = '';
	
	if(!empty($providersavailability)){
		$availableproviders = $providersavailability['available'];
		$unavailableproviders = $providersavailability['unavailable'];
		
		if(in_array($providerid,$availableproviders)){
			$html = '<span class="sf-availability-label">'.esc_html__('Available','service-finder').'</span>';
		}
		
		if(in_array($providerid,$unavailableproviders)){
			$html = '<span class="sf-availability-label unavailable">'.esc_html__('Unavailable','service-finder').'</span>';
		}
	}
	
	return $html;
   
}

/*Get start and end time by search period*/
function service_finder_get_wallet_amount($user_id = 0){
	
	$walletamount = get_user_meta($user_id,'_sf_wallet_amount',true);
	if($walletamount == ""){
		$walletamount = 0;
	}
	
	return $walletamount;
}

/*Get provider languages*/
function service_finder_get_languages($user_id = 0){
	
	$userInfo = service_finder_getUserInfo($user_id);
	
	if(!empty($userInfo['languages'])){
	$alllanguages = explode(',',$userInfo['languages']);
	}elseif($userInfo['languages'] != ""){
	$alllanguages[] = $userInfo['languages'];
	}else{
	$alllanguages = array();
	}
	
	return $alllanguages;
}

/*Get provider experience*/
function service_finder_get_experience($user_id = 0){
	global $wpdb,$service_finder_Tables;
	$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$service_finder_Tables->experience. " WHERE `provider_id` = %d ORDER BY ID ASC",$user_id));
	
	return $results;
}

/*Get provider certificate and award*/
function service_finder_get_certificates($user_id = 0){
	global $wpdb,$service_finder_Tables;
	$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$service_finder_Tables->certificates. " WHERE `provider_id` = %d ORDER BY ID ASC",$user_id));
	
	return $results;
}

/*Get provider qualification*/
function service_finder_get_qualifications($user_id = 0){
	global $wpdb,$service_finder_Tables;
	$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$service_finder_Tables->qualification. " WHERE `provider_id` = %d ORDER BY ID ASC",$user_id));
	
	return $results;
}

/*Get provider amenities*/
function service_finder_get_amenities($user_id = 0){
	
	$userInfo = service_finder_getUserInfo($user_id);
	
	if(!empty($userInfo['amenities'])){
	$amenities = explode(',',$userInfo['amenities']);
	}elseif($userInfo['amenities'] != ""){
	$amenities[] = $userInfo['amenities'];
	}else{
	$amenities = array();
	}
	
	return $amenities;
}

/*Check advance search option in on/off*/
function service_finder_check_advance_search(){
global $service_finder_options;

$searchprice = (isset($service_finder_options['search-price'])) ? esc_attr($service_finder_options['search-price']) : '';
$searchradius = (isset($service_finder_options['search-radius'])) ? esc_attr($service_finder_options['search-radius']) : '';

if($searchprice || $searchradius){
	return true;
}else{
	return false;
}
}

/*Check if any experience exist or not*/
function service_finder_experience_exist($author = 0){
global $wpdb,$service_finder_Tables;

$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$service_finder_Tables->experience. " WHERE `provider_id` = %d ORDER BY ID ASC",$author));

if(!empty($results)){
	return true;
}else{
	return false;
}
}

/*Check if any certificate exist or not*/
function service_finder_certificate_exist($author = 0){
global $wpdb,$service_finder_Tables;

$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$service_finder_Tables->certificates. " WHERE `provider_id` = %d ORDER BY ID ASC",$author));

if(!empty($results)){
	return true;
}else{
	return false;
}
}

/*Check if any certificate exist or not*/
function service_finder_qualification_exist($author = 0){
global $wpdb,$service_finder_Tables;

$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$service_finder_Tables->qualification. " WHERE `provider_id` = %d ORDER BY ID ASC",$author));

if(!empty($results)){
	return true;
}else{
	return false;
}
}

/*Check if any amenities exist or not*/
function service_finder_amenities_exist($author = 0){
global $wpdb,$service_finder_Tables;

$results = service_finder_get_amenities($author);

if(!empty($results)){
	return true;
}else{
	return false;
}
}

/*Check if any languages exist or not*/
function service_finder_languages_exist($author = 0){
global $wpdb,$service_finder_Tables;

$results = service_finder_get_languages($author);

if(!empty($results)){
	return true;
}else{
	return false;
}
}

/*Check if any article exist or not*/
function service_finder_article_exist($author = 0){
global $wpdb,$service_finder_Tables;

$args = array(
	'post_type' 	=> 'sf_articles',
	'post_status' 	=> 'publish',
	'posts_per_page' => 5,
	'order' => 'DESC',
	'author' => $author,
);
$the_query = new WP_Query( $args );

if ( $the_query->have_posts() ) {
	return true;
}else{
	return false;
}
}

/*Check provider membership status*/
function service_finder_check_provider_membership_status($providerid = 0){
global $wpdb,$service_finder_Tables;

$row = $wpdb->get_row($wpdb->prepare('SELECT status FROM '.$service_finder_Tables->providers.' where wp_user_id = %d',$providerid));

if(!empty($row)){
	return $row->status;
}else{
	return '';
}
}

/*Provider Category Dropdown*/
function service_finder_category_dropdown($texonomy = 'providers-category'){
global $wpdb,$service_finder_Tables;

$limit = 1000;
$categories = service_finder_getCategoryList($limit,'',$texonomy);
$html = '';

$html = '<select name="categoryid" class="form-control sf-form-control sf-select-box" title="'.esc_html__('Category', 'service-finder').'" data-live-search="true" data-header="'.esc_html__('Select a Category', 'service-finder').'">
        <option value="">
        '.esc_html__('Select a Category', 'service-finder').'
        </option>';

if(!empty($categories)){
	foreach($categories as $category){
	$term_id = (!empty($category->term_id)) ? $category->term_id : '';
	$term_name = (!empty($category->name)) ? $category->name : '';
	
	$catimage =  service_finder_getCategoryImage($term_id,'service_finder-category-small');
	$html .= '<option value="'.esc_attr($term_id).'" data-content="<span>'.esc_attr($term_name).'</span>">'. $term_name.'</option>';
	
	$term_children = get_term_children($term_id,$texonomy);
	$namearray = array();
	if(!empty($term_children)){
		foreach ($term_children as $child) {
			$term = get_term_by( 'id', $child, $texonomy );

			$namearray[$term->name]= $child;

		}
		
		if(!empty($namearray)){
		ksort($namearray);
	
		foreach($namearray as $key => $value) {
			$term_child_id = $value;
			$term_child = get_term_by('id',$term_child_id,$texonomy);
			$term_child_name = (!empty($term_child->name)) ? $term_child->name : '';
			
			$catimage =  service_finder_getCategoryImage($term_child_id,'service_finder-category-small');
			if($catimage != ""){
			$html .= '<option value="'.esc_attr($term_child_id).'" data-content="<img class=\'childcat-img\' width=\'50\' height=\'auto\' src=\''. esc_url($catimage).'\'><span class=\'childcat\'>'.esc_attr($term_child_name).'</span>">'. $term_child_name.'</option>';
			}else{
			$html .= '<option value="'.esc_attr($term_child_id).'" data-content="<span class=\'childcat\'>'.esc_attr($term_child_name).'</span>">'. $term_child_name.'</option>';
			}
			
		}
		}
	}
	
	}
}	
$html .= '</select>';

return $html;
}

/*Booked Services Summary for woo commerce cart*/
function service_finder_booking_services_woosummary($serviceitems = array(),$providerid = 0){
$html = '';
if(!empty($serviceitems)){
			$html = '<div class="table-responsive">          
				  <table class="table">
					<thead>
					  <tr>
						<th>'.esc_html__( 'Service Name', 'service-finder' ).'</th>
						<th>'.esc_html__( 'Date', 'service-finder' ).'</th>
						<th>'.esc_html__( 'Start Time', 'service-finder' ).'</th>
						<th>'.esc_html__( 'End Time', 'service-finder' ).'</th>
					  </tr>
					</thead>
					<tbody>';
				foreach($serviceitems as $servicesitem){
					$serviceitem = explode('||',$servicesitem);
				
					$sid = (!empty($serviceitem[0])) ? $serviceitem[0] : '';
					$shours = (!empty($serviceitem[1])) ? $serviceitem[1] : '';
					$sdate = (!empty($serviceitem[2])) ? $serviceitem[2] : '';
					$serslots = (!empty($serviceitem[3])) ? $serviceitem[3] : '';
					$smemberid = (!empty($serviceitem[4])) ? $serviceitem[4] : 0;
					$discount = (!empty($serviceitem[5])) ? $serviceitem[5] : 0;
					$couponcode = (!empty($serviceitem[6])) ? $serviceitem[6] : '';
			
					if(service_finder_get_service_type($sid) == 'days'){
						$sdate = trim($sdate,'##');
						
						$dates = str_replace('##',',',$sdate);
						
						$datesarr = explode(',',$dates);
						
						$startdate = $datesarr[0];
						
						$enddate = end($datesarr);
						
						$dates = service_finder_date_format($startdate).' - '.service_finder_date_format($enddate);
						
						$html .= '<tr>
							<td>'.service_finder_get_service_name($sid).'</td>
							<td>'.$dates.'</td>
							<td>-</td>
							<td>-</td>
						  </tr>';
					}else{
						
						$serslot = explode('-',$serslots);
						
						$paddingtime = service_finder_get_service_paddind_time($sid);
						$before_padding_time = $paddingtime['before_padding_time'];
						$after_padding_time = $paddingtime['after_padding_time'];
						
						$mstarttime = (!empty($serslot[0])) ? $serslot[0] : Null; 
						$mendtime = (!empty($serslot[1])) ? $serslot[1] : Null; 
						$midtime = (!empty($serslot[1])) ? $serslot[1] : Null; 
						
						if(service_finder_availability_method($providerid) == 'timeslots'){
							
							if($shours > 0){
								$tem = number_format($shours, 2);
								$temarr = explode('.',$tem);
								$tem1 = 0;
								$tem2 = 0;
								if(!empty($temarr)){
								
								if(!empty($temarr[0])){
									$tem1 = floatval($temarr[0]) * 60;
								}
								if(!empty($temarr[1])){
									$tem2 = $temarr[1];
								}
								
								}
								
								$totalhours = floatval($tem1) + floatval($tem2);
							
								if($totalhours > 0 && $totalhours != ""){
									$mendtime = date('H:i:s', strtotime($mstarttime." +".$totalhours." minutes"));
									$midtime = date('H:i:s', strtotime($mstarttime." +".$totalhours." minutes"));
								}	
							}
						}
						
						if($before_padding_time > 0 || $after_padding_time > 0){
						if(!empty($serslot[0])){
						$mstarttime = date('H:i:s', strtotime($mstarttime." -".$before_padding_time." minutes"));
						}
						if(!empty($serslot[1])){
						$mendtime = date('H:i:s', strtotime($mendtime." +".$after_padding_time." minutes"));
						}
						}
						
						$stime = (!empty($serslot[0])) ? $mstarttime : '-'; 
						$etime = (!empty($serslot[1])) ? $mendtime : '-'; 
						
						$html .= '<tr>
							<td>'.service_finder_get_service_name($sid).'</td>
							<td>'.$sdate.'</td>
							<td>'.$stime.'</td>
							<td>'.$etime.'</td>
						  </tr>';
								
					}		
				}
				
				$html .= '</tbody>
					  </table>
					  </div>';	
			}
return $html; 			
}

/*Get Languages*/
function service_finder_get_alllanguages(){
	$lng = array();
	$lng['aa'] = esc_html__( 'Afar', 'service-finder' );
	$lng['ab'] = esc_html__( 'Abkhazian', 'service-finder' );
	$lng['ae'] = esc_html__( 'Avestan', 'service-finder' );
	$lng['af'] = esc_html__( 'Afrikaans', 'service-finder' );
	$lng['am'] = esc_html__( 'Amharic', 'service-finder' );
	$lng['arg'] = esc_html__( 'Argentina', 'service-finder' );
	$lng['ar'] = esc_html__( 'Arabic', 'service-finder' );
	$lng['as'] = esc_html__( 'Assamese', 'service-finder' );
	$lng['ay'] = esc_html__( 'Aymara', 'service-finder' );
	$lng['az'] = esc_html__( 'Azerbaijani', 'service-finder' );
	$lng['ba'] = esc_html__( 'Bashkir', 'service-finder' );
	$lng['be'] = esc_html__( 'Belarusian', 'service-finder' );
	$lng['bg'] = esc_html__( 'Bulgarian', 'service-finder' );
	$lng['bh'] = esc_html__( 'Bihari', 'service-finder' );
	$lng['bi'] = esc_html__( 'Bislama', 'service-finder' );
	$lng['bn'] = esc_html__( 'Bengali', 'service-finder' );
	$lng['bo'] = esc_html__( 'Tibetan', 'service-finder' );
	$lng['br'] = esc_html__( 'Breton', 'service-finder' );
	$lng['bs'] = esc_html__( 'Bosnian', 'service-finder' );
	$lng['ca'] = esc_html__( 'Canada', 'service-finder' );
	$lng['ce'] = esc_html__( 'Chechen', 'service-finder' );
	$lng['ch'] = esc_html__( 'Chamorro', 'service-finder' );
	$lng['co'] = esc_html__( 'Corsican', 'service-finder' );
	$lng['cs'] = esc_html__( 'Czech', 'service-finder' );
	$lng['cu'] = esc_html__( 'Church Slavic', 'service-finder' );
	$lng['cv'] = esc_html__( 'Chuvash', 'service-finder' );
	$lng['cy'] = esc_html__( 'Welsh', 'service-finder' );
	$lng['da'] = esc_html__( 'Danish', 'service-finder' );
	$lng['de'] = esc_html__( 'German', 'service-finder' );
	$lng['dz'] = esc_html__( 'Dzongkha', 'service-finder' );
	$lng['el'] = esc_html__( 'Greek', 'service-finder' );
	$lng['eo'] = esc_html__( 'Esperanto', 'service-finder' );
	$lng['es'] = esc_html__( 'Spanish', 'service-finder' );
	$lng['et'] = esc_html__( 'Estonian', 'service-finder' );
	$lng['en'] = esc_html__( 'English', 'service-finder' );
	$lng['eu'] = esc_html__( 'Basque', 'service-finder' );
	$lng['fa'] = esc_html__( 'Persian', 'service-finder' );
	$lng['fi'] = esc_html__( 'Finnish', 'service-finder' );
	$lng['fj'] = esc_html__( 'Fijian', 'service-finder' );
	$lng['fo'] = esc_html__( 'Faeroese', 'service-finder' );
	$lng['fr'] = esc_html__( 'French', 'service-finder' );
	$lng['fy'] = esc_html__( 'Frisian', 'service-finder' );
	$lng['ga'] = esc_html__( 'Irish', 'service-finder' );
	$lng['gd'] = esc_html__( 'Gaelic (Scots)', 'service-finder' );
	$lng['gl'] = esc_html__( 'Gallegan', 'service-finder' );
	$lng['gn'] = esc_html__( 'Guarani', 'service-finder' );
	$lng['gu'] = esc_html__( 'Gujarati', 'service-finder' );
	$lng['gv'] = esc_html__( 'Manx', 'service-finder' );
	$lng['ha'] = esc_html__( 'Hausa', 'service-finder' );
	$lng['he'] = esc_html__( 'Hebrew', 'service-finder' );
	$lng['hi'] = esc_html__( 'Hindi', 'service-finder' );
	$lng['ho'] = esc_html__( 'Hiri Motu', 'service-finder' );
	$lng['hr'] = esc_html__( 'Croatian', 'service-finder' );
	$lng['hu'] = esc_html__( 'Hungarian', 'service-finder' );
	$lng['hy'] = esc_html__( 'Armenian', 'service-finder' );
	$lng['hz'] = esc_html__( 'Herero', 'service-finder' );
	$lng['ia'] = esc_html__( 'Interlingua', 'service-finder' );
	$lng['id'] = esc_html__( 'Indonesian', 'service-finder' );
	$lng['ie'] = esc_html__( 'Interlingue', 'service-finder' );
	$lng['ik'] = esc_html__( 'Inupiaq', 'service-finder' );
	$lng['is'] = esc_html__( 'Icelandic', 'service-finder' );
	$lng['it'] = esc_html__( 'Italian', 'service-finder' );
	$lng['iu'] = esc_html__( 'Inuktitut', 'service-finder' );
	$lng['ja'] = esc_html__( 'Japanese', 'service-finder' );
	$lng['jw'] = esc_html__( 'Javanese', 'service-finder' );
	$lng['ka'] = esc_html__( 'Georgian', 'service-finder' );
	$lng['ki'] = esc_html__( 'Kikuyu', 'service-finder' );
	$lng['kj'] = esc_html__( 'Kuanyama', 'service-finder' );
	$lng['kk'] = esc_html__( 'Kazakh', 'service-finder' );
	$lng['kl'] = esc_html__( 'Kalaallisut', 'service-finder' );
	$lng['km'] = esc_html__( 'Khmer', 'service-finder' );
	$lng['kn'] = esc_html__( 'Kannada', 'service-finder' );
	$lng['ko'] = esc_html__( 'Korean', 'service-finder' );
	$lng['ks'] = esc_html__( 'Kashmiri', 'service-finder' );
	$lng['ku'] = esc_html__( 'Kurdish', 'service-finder' );
	$lng['kv'] = esc_html__( 'Komi', 'service-finder' );
	$lng['kw'] = esc_html__( 'Cornish', 'service-finder' );
	$lng['ky'] = esc_html__( 'Kirghiz', 'service-finder' );
	$lng['la'] = esc_html__( 'Latin', 'service-finder' );
	$lng['lb'] = esc_html__( 'Letzeburgesch', 'service-finder' );
	$lng['ln'] = esc_html__( 'Lingala', 'service-finder' );
	$lng['lo'] = esc_html__( 'Lao', 'service-finder' );
	$lng['lt'] = esc_html__( 'Lithuanian', 'service-finder' );
	$lng['lv'] = esc_html__( 'Latvian', 'service-finder' );
	$lng['mg'] = esc_html__( 'Malagasy', 'service-finder' );
	$lng['mh'] = esc_html__( 'Marshall', 'service-finder' );
	$lng['mi'] = esc_html__( 'Maori', 'service-finder' );
	$lng['mk'] = esc_html__( 'Macedonian', 'service-finder' );
	$lng['ml'] = esc_html__( 'Malayalam', 'service-finder' );
	$lng['mn'] = esc_html__( 'Mongolian', 'service-finder' );
	$lng['mo'] = esc_html__( 'Moldavian', 'service-finder' );
	$lng['mr'] = esc_html__( 'Marathi', 'service-finder' );
	$lng['ms'] = esc_html__( 'Malay', 'service-finder' );
	$lng['mt'] = esc_html__( 'Maltese', 'service-finder' );
	$lng['my'] = esc_html__( 'Burmese', 'service-finder' );
	$lng['na'] = esc_html__( 'Nauru', 'service-finder' );
	$lng['nb'] = esc_html__( 'Norwegian Bokmal', 'service-finder' );
	$lng['nd'] = esc_html__( 'Ndebele, North', 'service-finder' );
	$lng['ne'] = esc_html__( 'Afar', 'service-finder' );
	$lng['ng'] = esc_html__( 'Nepali', 'service-finder' );
	$lng['nl'] = esc_html__( 'Dutch', 'service-finder' );
	$lng['nn'] = esc_html__( 'Norwegian Nynorsk', 'service-finder' );
	$lng['no'] = esc_html__( 'Norwegian', 'service-finder' );
	$lng['nr'] = esc_html__( 'Ndebele, South', 'service-finder' );
	$lng['nv'] = esc_html__( 'Navajo', 'service-finder' );
	$lng['ny'] = esc_html__( 'Chichewa Nyanja', 'service-finder' );
	$lng['oc'] = esc_html__( 'Occitan (post 1500)', 'service-finder' );
	$lng['om'] = esc_html__( 'Oromo', 'service-finder' );
	$lng['or'] = esc_html__( 'Oriya', 'service-finder' );
	$lng['os'] = esc_html__( 'Ossetian', 'service-finder' );
	$lng['pa'] = esc_html__( 'Panjabi', 'service-finder' );
	$lng['pi'] = esc_html__( 'Pali', 'service-finder' );
	$lng['pl'] = esc_html__( 'Polish', 'service-finder' );
	$lng['ps'] = esc_html__( 'Pushto', 'service-finder' );
	$lng['pt'] = esc_html__( 'Portuguese', 'service-finder' );
	$lng['pb'] = esc_html__( 'Brazilian Portuguese', 'service-finder' );
	$lng['qu'] = esc_html__( 'Quechua', 'service-finder' );
	$lng['rm'] = esc_html__( 'Rhaeto-Romance', 'service-finder' );
	$lng['rn'] = esc_html__( 'Rundi', 'service-finder' );
	$lng['ro'] = esc_html__( 'Romanian', 'service-finder' );
	$lng['ru'] = esc_html__( 'Russian', 'service-finder' );
	$lng['rw'] = esc_html__( 'Kinyarwanda', 'service-finder' );
	$lng['sa'] = esc_html__( 'Sanskrit', 'service-finder' );
	$lng['sc'] = esc_html__( 'Sardinian', 'service-finder' );
	$lng['sd'] = esc_html__( 'Sindhi', 'service-finder' );
	$lng['se'] = esc_html__( 'Sami', 'service-finder' );
	$lng['sg'] = esc_html__( 'Sango', 'service-finder' );
	$lng['si'] = esc_html__( 'Sinhalese', 'service-finder' );
	$lng['sk'] = esc_html__( 'Slovak', 'service-finder' );
	$lng['sl'] = esc_html__( 'Slovenian', 'service-finder' );
	$lng['sm'] = esc_html__( 'Samoan', 'service-finder' );
	$lng['sn'] = esc_html__( 'Shona', 'service-finder' );
	$lng['so'] = esc_html__( 'Somali', 'service-finder' );
	$lng['sq'] = esc_html__( 'Albanian', 'service-finder' );
	$lng['sr'] = esc_html__( 'Serbian', 'service-finder' );
	$lng['ss'] = esc_html__( 'Swati', 'service-finder' );
	$lng['st'] = esc_html__( 'Sotho', 'service-finder' );
	$lng['su'] = esc_html__( 'Sundanese', 'service-finder' );
	$lng['sv'] = esc_html__( 'Swedish', 'service-finder' );
	$lng['sw'] = esc_html__( 'Swahili', 'service-finder' );
	$lng['ta'] = esc_html__( 'Tamil', 'service-finder' );
	$lng['te'] = esc_html__( 'Telugu', 'service-finder' );
	$lng['tg'] = esc_html__( 'Tajik', 'service-finder' );
	$lng['th'] = esc_html__( 'Thai', 'service-finder' );
	$lng['ti'] = esc_html__( 'Tigrinya', 'service-finder' );
	$lng['tk'] = esc_html__( 'Turkmen', 'service-finder' );
	$lng['tl'] = esc_html__( 'Tagalog', 'service-finder' );
	$lng['tn'] = esc_html__( 'Tswana', 'service-finder' );
	$lng['to'] = esc_html__( 'Tonga', 'service-finder' );
	$lng['tr'] = esc_html__( 'Turkish', 'service-finder' );
	$lng['ts'] = esc_html__( 'Tsonga', 'service-finder' );
	$lng['tt'] = esc_html__( 'Tatar', 'service-finder' );
	$lng['tw'] = esc_html__( 'Twi', 'service-finder' );
	$lng['ug'] = esc_html__( 'Uighur', 'service-finder' );
	$lng['ua'] = esc_html__( 'Ukrainian', 'service-finder' );
	$lng['ur'] = esc_html__( 'Urdu', 'service-finder' );
	$lng['uz'] = esc_html__( 'Uzbek', 'service-finder' );
	$lng['vi'] = esc_html__( 'Vietnamese', 'service-finder' );
	$lng['vo'] = esc_html__( 'Volapuk', 'service-finder' );
	$lng['wo'] = esc_html__( 'Wolof', 'service-finder' );
	$lng['xh'] = esc_html__( 'Xhosa', 'service-finder' );
	$lng['yi'] = esc_html__( 'Yiddish', 'service-finder' );
	$lng['yo'] = esc_html__( 'Yoruba', 'service-finder' );
	$lng['za'] = esc_html__( 'Zhuang', 'service-finder' );
	$lng['zh'] = esc_html__( 'Chinese', 'service-finder' );
	$lng['zu'] = esc_html__( 'Zulu', 'service-finder' );
	return $lng;
}

/*Display package capabilities*/
function service_finder_display_package_capability($packageid = ''){
global $service_finder_options;	
$caps = (!empty($service_finder_options['package'.$packageid.'-capabilities'])) ? $service_finder_options['package'.$packageid.'-capabilities'] : '';
$subcaps = (!empty($service_finder_options['package'.$packageid.'-subcapabilities'])) ? $service_finder_options['package'.$packageid.'-subcapabilities'] : '';

	if(!empty($caps)){
		foreach($caps as $key => $value){
			$featuretitle = service_finder_get_data($service_finder_options,'shortcode-pricing-feature-'.$key,service_finder_get_capability_name_by_key($key));
			if($value){
			echo '<li><strong>'.strtoupper($featuretitle).'</strong> <i class="fa fa-check"></i></li>';
			if($key == 'multiple-categories'){
			echo '<li><strong>'.$featuretitle.'</strong> '.service_finder_get_number_of_category($packageid).'</li>';
			}
			}else{
			echo '<li><strong>'.strtoupper($featuretitle).'</strong> <i class="fa fa-close"></i></li>';
			}
		}
	}
	
	if(!empty($subcaps)){
		foreach($subcaps as $key => $value){
			$featuretitle = service_finder_get_data($service_finder_options,'shortcode-pricing-feature-'.$key,service_finder_get_capability_name_by_key($key));
			if($value){
			echo '<li><strong>'.strtoupper($featuretitle).'</strong> <i class="fa fa-check"></i></li>';
			}else{
			echo '<li><strong>'.strtoupper($featuretitle).'</strong> <i class="fa fa-close"></i></li>';
			}
		}
	}
	
}

function service_finder_get_capability_name_by_key($key = ''){
	$string = '';
	switch ($key) {
		case 'booking':
			$string = esc_html__('Booking','service-finder');
			break;
		case 'cover-image':
			$string = esc_html__('Cover Image','service-finder');
			break;
		case 'gallery-images':
			$string = esc_html__('Gallery Images','service-finder');
			break;
		case 'multiple-categories':
			$string = esc_html__('Multiple Categories','service-finder');
			break;
		case 'apply-for-job':
			$string = esc_html__('Apply for Job','service-finder');
			break;
		case 'job-alerts':
			$string = esc_html__('Job Alerts','service-finder');
			break;
		case 'branches':
			$string = esc_html__('Branches','service-finder');
			break;
		case 'google-calendar':
			$string = esc_html__('Google Calendar','service-finder');
			break;
		case 'crop':
			$string = esc_html__('Crop Profile Image','service-finder');
			break;	
		case 'invoice':
			$string = esc_html__('Invoice','service-finder');
			break;
		case 'availability':
			$string = esc_html__('Availability','service-finder');
			break;
		case 'staff-members':
			$string = esc_html__('Staff Members','service-finder');
			break;
		case 'contact-numbers':
			$string = esc_html__('Contact Numbers','service-finder');
			break;
		case 'message-system':
			$string = esc_html__('Message System','service-finder');
			break;		
	}
	return $string;
}

/*Get number of multiple categories in package*/
function service_finder_get_number_of_category($packageid = ''){
global $service_finder_options;	

$numberofcategory = (!empty($service_finder_options['package'.$packageid.'-multiple-categories'])) ? $service_finder_options['package'.$packageid.'-multiple-categories'] : 0;

return $numberofcategory;
}

/*Get job quote price*/
function service_finder_get_job_quote_price($providerid = 0,$jobid = 0){

$applications_cost = get_user_meta($providerid,'job_applications_cost',true);
$quotecost = 0;		
$tempdata = explode(',',$applications_cost);
if(!empty($tempdata)){
	foreach($tempdata as $temp){
		$tmp = explode('-',$temp);
		if($tmp[0] == $jobid){
			$quotecost = $tmp[1];
		}
	}
}
return $quotecost;
}

/*Check identity approved or not*/
function service_finder_check_identity_approved($uid = 0){
	global $wpdb, $service_finder_Tables, $service_finder_options;

	$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$uid));
	if(!empty($row))
	{
		if($row->identity == 'approved'){
			return false;
		}else{
			return true;
		}
		
	}else{
		return true;
	}
}

/*Validate signup fileds*/
add_action('wp_ajax_signupvalidate', 'service_finder_signupvalidate');
add_action('wp_ajax_nopriv_signupvalidate', 'service_finder_signupvalidate');
function service_finder_signupvalidate(){
global $wpdb, $service_finder_Tables;
extract($_POST);

if(!empty($signup_user_name))
{
	if(username_exists($signup_user_name)){
		$valid = false;
	}else{
		$valid = true;
	}
}

if(!empty($signup_user_email))
{
	if(email_exists($signup_user_email)){
		$valid = false;
	}else{
		$valid = true;
	}
}

echo json_encode(array(
    'valid' => $valid,
)); 

exit;
}

add_action('wp_insert_comment','service_finder_comment_inserted',99,2);
function service_finder_comment_inserted($comment_id = 0, $comment_object = '') {

	$rating_title = (!empty($_REQUEST['rating_title'])) ? sanitize_text_field($_REQUEST['rating_title']) : '';
	if($rating_title != '')
	{
		update_comment_meta( $comment_id, 'pixrating_title', $rating_title );
	}
}

/* Get User Paypal Email */
function service_finder_get_provider_paypal_email($userid = 0)
{
	if($userid > 0)
	{
		$paypalemail = get_user_meta($userid,'paypal_email_id',true);
		
		return $paypalemail;
	}
}

/* Get total saloons by city */
function service_finderfn_get_upload_filesize(){
	
	$filesize = wp_max_upload_size() / 1048576;
	
	return $filesize;
}

/* Get static user avatar that display after delete avtar  function */
function service_finder_fn_preview_placeholder()
{
	$placeholder = SERVICE_FINDER_BOOKING_IMAGE_URL.'/preview-placeholder.jpg';
	
	return $placeholder;
}

/* Get static user avatar that display after delete avtar  function */
function service_finder_fn_crop_preview_placeholder()
{
	$placeholder = SERVICE_FINDER_BOOKING_IMAGE_URL.'/crop-preview-placeholder.jpg';
	
	return $placeholder;
}

/////// Check member has capability //////////
function service_finder_user_has_capability($capability = array(),$userid = 0)
{
	$usercapabilities = service_finder_get_capability($userid);
	
	if(in_array($capability,$usercapabilities))
	{
		return true;
	}else
	{
		return false;
	}
}

/* Convert cropped image data to image format*/
function service_finder_cropped_data_to_image($croppedimagedata = '')
{
	global $wp_filesystem;
	
	require_once ABSPATH . '/wp-admin/includes/media.php';
	
	if ( empty( $wp_filesystem ) ) {
          require_once ABSPATH . '/wp-admin/includes/file.php';
          WP_Filesystem();
    }
	
	$data = $croppedimagedata;
	list($type, $data) = explode(';', $data);
	list(, $data)      = explode(',', $data);
	$data = base64_decode($data);
	
	$filename = time().'.jpg';
	
	$tempdir = SERVICE_FINDER_BOOKING_DIR.'images/cropped/';
	$tempurl = SERVICE_FINDER_BOOKING_URL.'images/cropped/';
	$wp_filesystem->put_contents($tempdir.$filename, $data);
	
	$timeout_seconds = 5;
	$imageurl = $tempurl.$filename;
	
	$temp_file = download_url( $imageurl, $timeout_seconds );
	if(!is_wp_error($temp_file)) 
	{
		$wp_filetype = wp_check_filetype( basename($imageurl), null );
		
		$file = array(
			'name'     => basename($imageurl),
			'type'     => $wp_filetype['type'],
			'tmp_name' => $temp_file,
			'error'    => 0,
			'size'     => filesize($temp_file),
		);
		$post_id = 0;
		
		$attachmentid = media_handle_sideload( $file,  $post_id, 'Cropped Image' );
		
		if ( ! is_wp_error( $attachmentid ) )
		{
			$attach_data = wp_generate_attachment_metadata( $attachmentid,  get_attached_file($attachmentid));
			wp_update_attachment_metadata( $attachmentid,  $attach_data );
			
			return $attachmentid;
		}else
		{
			return '';
		}
	}
	
}

/* Get attachment url by attachment id */
function service_finder_get_image_url($attachmentid = 0,$size = 'thumbnail')
{
	if($attachmentid > 0)
	{
	$src  = wp_get_attachment_image_src( $attachmentid, $size );
	$src  = $src[0];
	
	$src = (!empty($src)) ? $src : '';
	
	return $src;
	}
}

/* Get attachment url by attachment id */
function service_finder_get_user_coverimage($userid = 0,$size = 'thumbnail')
{
	$croppedcoverimage = get_user_meta($userid, 'cropped_cover_image', true);
	$src = '';
	if(service_finder_user_has_capability('crop',$userid))
	{
		if($croppedcoverimage != '' && $croppedcoverimage > 0){
			$src  = wp_get_attachment_image_src( $croppedcoverimage, $size );
			$src  = $src[0];
			return $src;
		}else
		{
			return SERVICE_FINDER_BOOKING_IMAGE_URL.'/crop-preview-placeholder.jpg';
		}
	}else{
	$coverimage = service_finder_getProviderAttachments($userid,'cover-image');
	$imgrsrc = '';
	if(!empty($coverimage)){
		foreach($coverimage as $cimage){
				$src  = wp_get_attachment_image_src( $cimage->attachmentid, 'thumbnail' );
				$imgrsrc  = $src[0];
		}
	}else{
		$imgrsrc = SERVICE_FINDER_BOOKING_IMAGE_URL.'/crop-preview-placeholder.jpg';
	}		
		return $imgrsrc;
	}
}

/* Set default timeslots */
function service_finder_create_default_timeslots($uid = 0){
global $wpdb, $service_finder_Tables, $service_finder_options;

	$weekdays = service_finder_get_weekdays();
	
	if(!empty($weekdays)){
		foreach($weekdays as $weekday){
			$liday = ucfirst(str_replace("day","",$weekday));
		
			$begin = new DateTime("08:00");
			$end   = new DateTime("20:00");
			
			$period = 30;
			
			$intval = DateInterval::createFromDateString( $period.' min' );
			$available_start_times = new DatePeriod($begin, $intval ,$end);
			$k = 17;
			foreach($available_start_times as $date){
			
			$endTime = date('H:i:s',strtotime("+".$period." minutes", strtotime($date->format("H:i:s"))));
			$t = $k + 1;
			$dataset = array(
		
					'provider_id' => esc_attr($uid),
		
					'day' => $weekday,
		
					'start_time' => $date->format("H:i:s"),
		
					'end_time' => $endTime,
		
					'slotids' => 'li'.$liday.$k.'-li'.$liday.$t,
		
					'max_bookings' => 1,
		
					);

			$wpdb->insert($service_finder_Tables->timeslots,wp_unslash($dataset));
			$k++;
			
			}	
		}
	}
}

/* Set default start time */
function service_finder_create_default_starttime($uid = 0){
global $wpdb, $service_finder_Tables, $service_finder_options;

	$weekdays = service_finder_get_weekdays();
	
	if(!empty($weekdays)){
		foreach($weekdays as $weekday){
			$liday = ucfirst(str_replace("day","",$weekday));
		
			$begin = new DateTime("08:00");
			$end   = new DateTime("20:00");
			
			$period = 30;
			
			$intval = DateInterval::createFromDateString( $period.' min' );
			$available_start_times = new DatePeriod($begin, $intval ,$end);

			foreach($available_start_times as $date){
			
				$dataset = array(

						'provider_id' => esc_attr($uid),

						'day' => $weekday,

						'start_time' => $date->format("H:i:s"),

						'max_bookings' => 1,

						);

				$wpdb->insert($service_finder_Tables->starttime,wp_unslash($dataset));
			
			}	
		}
	}
}

/* Get data from array/object by key */
function service_finder_get_data( $obj = array(), $key = '', $default = '' )
{
	if( !$obj )
	{
		return false;
	}
	
	if( is_object( $obj ) && isset( $obj->$key ) )
	{
		return (!empty($obj->$key)) ? $obj->$key : $default;
	}elseif( is_array( $obj ) && isset( $obj[$key] ) )
	{
		return (!empty($obj[$key])) ? $obj[$key] : $default;
	}elseif( $default )
	{
		return (!empty($default)) ? $default : '';
	}else
	{
		return false;
	}
}

/* Get job related providers */
if ( ! function_exists('service_finder_get_job_related_providers') ) {
function service_finder_get_job_related_providers($jobid = 0,$args = array()){
	global $wpdb, $service_finder_Tables, $service_finder_options;

	$taxonomy = 'job_listing_category';
	$terms = wp_get_post_terms( $jobid, $taxonomy );
	
	//$service_perform_at = service_finder_get_data($_POST,'service_perform_at');
	$providertype = service_finder_get_data($_POST,'providertype');
	$filterlocation = service_finder_get_data($_POST,'filterlocation');
	$radius = service_finder_get_data($_POST,'radius');
	$amenities = service_finder_get_data($_POST,'amenities');
	
	$service_perform_at = (!empty($_POST['service_perform_at'])) ? $_POST['service_perform_at'] : array();
	
	$provider_categotyid = array();
	if(!empty($terms)){
		foreach($terms as $term){
			$termname = (!empty($term->name)) ? $term->name : '';
			$providercategory = get_term_by('name', $termname, 'providers-category');
			$provider_categotyid[] = (!empty($providercategory->term_id)) ? $providercategory->term_id : '';
		}
	}
	
	$job_applications = get_post_meta($jobid,'job_applications',true);
	
	$totalcat = count($provider_categotyid);
	
	$sql = 'SELECT * FROM '.$service_finder_Tables->providers.' WHERE `admin_moderation` = "approved" AND `account_blocked` != "yes"';
	
	if($filterlocation != '' && (in_array('customer_location',$service_perform_at) || $radius > 0))
	{
		$unit = service_finder_get_radius_unit();
	
		if($radiussearchunit == 'mi'){
		$unitchanger = 3959;
		}else{
		$unitchanger = 6371;
		}
		
		$filterlocation = str_replace(" ","+",$filterlocation);
		$res = service_finder_getLatLong($filterlocation);
		$latitude = $res['lat'];
		$longitude = $res['lng'];
		if($latitude != '' && $longitude != '')
		{
		if(in_array('customer_location',$service_perform_at) && in_array('provider_location',$service_perform_at) && $radius > 0)
		{
		$sql = 'SELECT *,( '.$unitchanger.' * acos( cos( radians('.$latitude.') ) * cos( radians( providers.lat ) ) * cos( radians( providers.long ) - radians('.$longitude.')) + sin(radians('.$latitude.')) * sin( radians( providers.lat )))) AS distance FROM '.$service_finder_Tables->providers.' as providers WHERE `admin_moderation` = "approved" AND `account_blocked` != "yes" AND (( '.$unitchanger.' * acos( cos( radians('.$latitude.') ) * cos( radians( providers.lat ) ) * cos( radians( providers.long ) - radians('.$longitude.')) + sin(radians('.$latitude.')) * sin( radians( providers.lat )))) <= providers.radius) OR (( '.$unitchanger.' * acos( cos( radians('.$latitude.') ) * cos( radians( providers.lat ) ) * cos( radians( providers.long ) - radians('.$longitude.')) + sin(radians('.$latitude.')) * sin( radians( providers.lat )))) <= '.$radius.')';
		}elseif(in_array('customer_location',$service_perform_at))
		{
		$sql = 'SELECT *,( '.$unitchanger.' * acos( cos( radians('.$latitude.') ) * cos( radians( providers.lat ) ) * cos( radians( providers.long ) - radians('.$longitude.')) + sin(radians('.$latitude.')) * sin( radians( providers.lat )))) AS distance FROM '.$service_finder_Tables->providers.' as providers WHERE `admin_moderation` = "approved" AND `account_blocked` != "yes" AND ( '.$unitchanger.' * acos( cos( radians('.$latitude.') ) * cos( radians( providers.lat ) ) * cos( radians( providers.long ) - radians('.$longitude.')) + sin(radians('.$latitude.')) * sin( radians( providers.lat )))) <= providers.radius';
		}elseif(in_array('provider_location',$service_perform_at) && $radius > 0)
		{
		$sql = 'SELECT *,( '.$unitchanger.' * acos( cos( radians('.$latitude.') ) * cos( radians( providers.lat ) ) * cos( radians( providers.long ) - radians('.$longitude.')) + sin(radians('.$latitude.')) * sin( radians( providers.lat )))) AS distance FROM '.$service_finder_Tables->providers.' as providers WHERE `admin_moderation` = "approved" AND `account_blocked` != "yes" AND ( '.$unitchanger.' * acos( cos( radians('.$latitude.') ) * cos( radians( providers.lat ) ) * cos( radians( providers.long ) - radians('.$longitude.')) + sin(radians('.$latitude.')) * sin( radians( providers.lat )))) <= '.$radius;
		}
		}
	}
	//print_r($service_perform_at);
	if(!empty($service_perform_at))
	{
		$service_perform_locations = implode('","',$service_perform_at);
		//print_r('"'.$service_perform_locations.'"');
		$sql .= ' AND (`service_perform_at` IN ("'.$service_perform_locations.'") OR `service_perform_at` = "both")';
	}
	
	if(!empty($provider_categotyid) && $job_applications != ''){
		$sql .= ' AND ( (';
				$i = 1;
				foreach($provider_categotyid as $catid) {
					if($totalcat == $i){	
						$sql .= ' FIND_IN_SET("'.$catid.'", category_id) ';
					}else{
						$sql .= ' FIND_IN_SET("'.$catid.'", category_id) OR ';
					}
					$i++;
					
				}
		$sql .= ' )';	
		$sql .= ' OR `wp_user_id` IN ('.$job_applications.'))';
	}elseif(!empty($provider_categotyid))
	{
		$sql .= ' AND (';
				$i = 1;
				foreach($provider_categotyid as $catid) {
					if($totalcat == $i){	
						$sql .= ' FIND_IN_SET("'.$catid.'", category_id) ';
					}else{
						$sql .= ' FIND_IN_SET("'.$catid.'", category_id) OR ';
					}
					$i++;
					
				}
		$sql .= ' )';
	}elseif($job_applications != '')
	{
		$sql .= ' AND `wp_user_id` IN ('.$job_applications.')';
	}	
	
	if(!empty($providertype))
	{
		if(in_array('featured',$providertype))
		{
			$sql .= ' AND featured = 1';
		}
		
		if(in_array('verified',$providertype))
		{
			$sql .= ' AND identity = "approved"';
		}
	}
	
	if($amenities != ''){
		$sql .= ' AND FIND_IN_SET("'.$amenities.'", amenities) ';
	}
	
	$results = $wpdb->get_results($sql);
	
	if(get_post_meta($jobid,'_filled',true))
	{
		$providerid = get_post_meta($jobid,'_assignto',true);
		
		if($providerid > 0)
		{
		$allproviders = array();
		
		if(!empty($results))
		{
			foreach($results as $row)
			{
				if($providerid != $row->wp_user_id)
				{
					$allproviders[] = $row->wp_user_id;
				}
			}
		}
		
		$assigedprovider = array($providerid);	
		$orderedproviders = array_merge($assigedprovider,$allproviders);
		$orderedproviders = implode(',',$orderedproviders);

		$sql .= ' ORDER BY FIELD(wp_user_id,'.$orderedproviders.'), featured DESC,full_name ASC';
		}
	}	
	//echo $sql;
	$results = $wpdb->get_results($sql);
	
	return $results;
}
}

/* Get quote info */
function service_finder_get_quote_info($quoteid = 0){
	global $wpdb, $service_finder_Tables, $service_finder_options;

	$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->quotations.' WHERE status = "approved" AND id = %d',$quoteid));
	
	return $row;
}

/* Get related quote providers */
function service_finder_get_related_quote_providers($quoteid = 0){
	global $wpdb, $service_finder_Tables, $service_finder_options;

	$sql = $wpdb->prepare("SELECT * FROM ".$service_finder_Tables->quoteto_related_providers." WHERE quote_id = %d",$quoteid);
	
	$results = $wpdb->get_results($sql);
	
	return $results;
}

/////// Set date time format //////////
function service_finder_date_time_format( $datetime = '' ) 
{
	if( $datetime != '' )
	{
		$datetime = date('Y-m-d g:i a',strtotime($datetime));
		return $datetime;
	}
}
/////// Set time format //////////
function service_finder_time_format( $time ) 
{
	global $service_finder_options;
	
	if( $time != '' )
	{
		$time_format = (!empty($service_finder_options['time-format'])) ? $service_finder_options['time-format'] : '';
		
		if($time_format){
			$time = date('H:i',strtotime($time));
		}else{
			$time = date('g:i a',strtotime($result->start_time));
		}

		return $time;
	}
}
/////// Set date format //////////
function service_finder_date_format( $date = '' ) 
{
	if( $date != '' )
	{
		$date = date('Y-m-d',strtotime($date));
		return $date;
	}
}
/////// Set datepicker format //////////
function service_finder_datepicker_format() 
{
	return 'yyyy-mm-dd';
}

/////// Check invitation has been sent or not //////////
function service_finder_has_sent_invitation($jobid = 0,$providerid = 0) 
{
	global $wpdb,$service_finder_options,$service_finder_Tables;
	
	$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->job_invitations.' where `jobid` = %d AND `provider_id` = %d',$jobid,$providerid));
	
	if(!empty($row))	
	{
		return true;
	}else
	{
		return false;
	}
}

/////// Check job publish notification has been sent or not //////////
function service_finder_has_sent_job_notification($jobid = 0,$providerid = 0) 
{
	$notification_mail_sent = service_finder_get_custom_meta($jobid,$providerid,'notification_mail_sent'); 
	
	if($notification_mail_sent == 'yes')
	{
		return true;
	}else
	{
		return false;
	}
}

/////// Check provider has applied for job //////////
function service_finder_has_applied_for_job($jobid = 0,$providerid = 0) 
{
	$job_applications = get_post_meta($jobid,'job_applications',true);
	
	if($job_applications != '')
	{
		$job_applications = explode(',',$job_applications);
		
		if(in_array($providerid,$job_applications))
		{
			return true;
		}else
		{
			return false;
		}
	}else
	{
		return false;
	}
}

/////// Get custom meta data //////////
function service_finder_get_custom_meta($post_id = '',$user_id = '',$meta_key = '') 
{
	global $wpdb,$service_finder_Tables;
	
	$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->post_user_meta.' where `post_id` = %d AND `user_id` = %d AND `meta_key` = %d',$post_id,$user_id,$meta_key));
	
	if(!empty($row))	
	{
		return $row->meta_value;
	}else
	{
		return '';
	}
}

/////// Update custom meta data //////////
function service_finder_update_custom_meta($post_id = '',$user_id  = '',$meta_key = '',$meta_value = '') 
{
	global $wpdb,$service_finder_Tables;
	
	$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->post_user_meta.' where `post_id` = %d AND `user_id` = %d AND `meta_key` = %d',$post_id,$user_id,$meta_key));
	
	if(!empty($row))	
	{
		$data = array(
				'meta_value' => $meta_value,
				);
		
		$where = array(
				'post_id' => $post_id,
				'user_id' => $user_id,
				'meta_key' => $meta_key
				);
		$wpdb->update($service_finder_Tables->post_user_meta,wp_unslash($data),$where);
	}else
	{
		$data = array(
				'meta_value' => $meta_value,
				);
		
		$data = array(
				'post_id' => $post_id,
				'user_id' => $user_id,
				'meta_key' => $meta_key,
				'meta_value' => $meta_value
				);
		$wpdb->insert($service_finder_Tables->post_user_meta,wp_unslash($data));
	}
}

/*Get provider info */
function service_finder_get_provier_info($providerid = 0){
	global $wpdb, $service_finder_Tables;
	$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$providerid));
	
	return $row;
}

/*Get provider total reviews*/
function service_finder_get_total_reviews($providerid = 0){
	global $wpdb, $service_finder_Tables, $service_finder_options;
	
	$totalreview = 0;
	
	if($service_finder_options['review-style'] == 'open-review')
	{
		$author_post_id = get_user_meta($providerid,'comment_post',true);
		
		$totalreview = get_comments_number( $author_post_id );
	}elseif($service_finder_options['review-style'] == 'booking-review')
	{
		$allreviews = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->feedback.' where provider_id = %d',$providerid));
		$totalreview = count($allreviews);
	}
	
	return $totalreview;
	
}

/*Payment amount goes to*/
function service_finder_get_payment_goes_to(){
	global $service_finder_options;	
	
	$pay_booking_amount_to = service_finder_get_data($service_finder_options,'pay_booking_amount_to');
	
	return $pay_booking_amount_to;
}

/*Get Payment Methods*/
function service_finder_get_payment_methods(){
	global $service_finder_options;	
	
	$paymentmethods = service_finder_get_data($service_finder_options,'payment-methods');
	
	return $paymentmethods;
}

/*Payment amount goes to*/
function service_finder_get_stripe_public_key(){
	global $service_finder_options;	
	
	$pay_booking_amount_to = service_finder_get_payment_goes_to();
	
	if($pay_booking_amount_to == 'admin'){
		$stripetype = (!empty($service_finder_options['stripe-type'])) ? esc_html($service_finder_options['stripe-type']) : '';
		if($stripetype == 'live'){
			$stripepublickey = (!empty($service_finder_options['stripe-live-public-key'])) ? esc_html($service_finder_options['stripe-live-public-key']) : '';
		}else{
			$stripepublickey = (!empty($service_finder_options['stripe-test-public-key'])) ? esc_html($service_finder_options['stripe-test-public-key']) : '';
		}
	}
	
	return $stripepublickey;
}

/*Provider Replace String*/
function service_finder_provider_replace_string(){
	global $service_finder_options;	
	
	$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('Provider', 'service-finder');

	
	return $providerreplacestring;
}

/*Customer Replace String*/
function service_finder_customer_replace_string(){
	global $service_finder_options;	
	
	$customerreplacestring = (!empty($service_finder_options['customer-replace-string'])) ? $service_finder_options['customer-replace-string'] : esc_html__('Customer', 'service-finder');
	
	return $customerreplacestring;
}

/*Filter Job applicants*/
add_action('wp_ajax_job_filter_applicants', 'service_finder_job_filter_applicants');
if ( ! function_exists('service_finder_job_filter_applicants') ) {
function service_finder_job_filter_applicants(){
	global $wpdb,$current_user,$service_finder_Tables,$service_finder_options;

	$jobid = service_finder_get_data($_POST,'jobid');
	$quotereceived = service_finder_get_data($_POST,'quotereceived');
	
	$providers = service_finder_get_job_related_providers($jobid,$_POST);
	$jobauthor = get_post_field( 'post_author', $jobid );
	
	ob_start();
	$providerflag = 0;
	
	$totalproviders = count($providers);
	if(!empty($providers))
	{
		?>
        <div class="sf-chkallinv-outer">
            <div class="sf-chkallinv-left">
                <label for="allinvitationrow" style="text-transform:none;"><?php echo service_finder_get_data($service_finder_options,'job-send-invitation-selected-lession-provider-text'); ?></label>
            </div>
            <div class="sf-chkallinv-right">
                <button id="sendallinvitations" class="btn btn-primary" data-jobid="<?php echo esc_attr($jobid); ?>">
                  <?php echo esc_html__('Send', 'service-finder') ?>
                </button>
            </div>
        </div>
        <?php
		$i = 1;
		foreach($providers as $provider)
		{
			$providerid = $provider->wp_user_id;
			$profileurl = service_finder_get_author_url($providerid);
			$profileimage = service_finder_get_avatar_by_userid($providerid,'service_finder-provider-medium');
			$providerinfo = service_finder_get_provier_info($providerid);
			$categories = $providerinfo->category_id;
			
			$distance = service_finder_get_data($provider,'distance');
			
			$providerrating = service_finder_get_data($_POST,'providerrating');
			$quotereceived = service_finder_get_data($_POST,'quotereceived');
			
			$avgrating = service_finder_getAverageRating($providerid);
			$quoteflag = 1;
			if($quotereceived != '')
			{
				$quoteflag = 0;
				if($quotereceived == 'yes')
				{
					if(service_finder_has_applied_for_job($jobid,$providerid))
					{
						$quoteflag = 1;
					}
				}
				
				if($quotereceived == 'no')
				{
					if(!service_finder_has_applied_for_job($jobid,$providerid))
					{
						$quoteflag = 1;
					}
				}
			}
			
			if($quoteflag == 0)
			{
				continue;
			}
			
			$ratingflag = 1;
			if(!empty($providerrating))
			{
				$ratingflag = 0;
				if(in_array('1',$providerrating))
				{
					if(floatval($avgrating) >= 1 && floatval($avgrating) < 2)
					{
						$ratingflag = 1;					
					}
				}
				if(in_array('2',$providerrating))
				{
					if(floatval($avgrating) >= 2 && floatval($avgrating) < 3)
					{
						$ratingflag = 1;					
					}
				}
				if(in_array('3',$providerrating))
				{
					if(floatval($avgrating) >= 3 && floatval($avgrating) < 4)
					{
						$ratingflag = 1;					
					}
				}
				if(in_array('4',$providerrating))
				{
					if(floatval($avgrating) >= 4 && floatval($avgrating) < 5)
					{
						$ratingflag = 1;					
					}
				}
				if(in_array('5',$providerrating))
				{
					if(floatval($avgrating) >= 5)
					{
						$ratingflag = 1;					
					}
				}
			}
			
			if($ratingflag == 0)
			{
				continue;
			}
			?>
			<div class="sf-serach-result-wrap moreproviderbox" <?php echo ($i >= 10) ? 'style="display:none"' : ''; ?>>
				<div class="sf-serach-result-left">
					<?php if(service_finder_is_featured($providerid)){ ?>
					 <div class="sf-featuerd-label"><span><?php echo esc_html__( 'Featured', 'service-finder' ); ?></span></div>
					<?php } ?> 
					<div class="sf-serach-result-propic">
						<img src="<?php echo esc_url($profileimage); ?>" alt="">
						<?php if(service_finder_is_varified_user($providerid)){ ?>
                        <span class="sf-featured-approve">
                            <i class="fa fa-check"></i><span><?php esc_html_e('Verified Provider', 'service-finder'); ?></span>
                        </span>
                        <?php } ?>
					</div>
					<div class="sf-serach-result-bookNow">
						<?php
						if(get_post_meta($jobid,'_filled',true)){
							if(get_post_meta($jobid,'_assignto',true) == $providerid){
								echo '<span class="sf-hiring-status status-jobhired">'.esc_html__( 'Hired', 'service-finder' ).'</span>';
							}
						}else{
							$jobexpire = get_post_meta($jobid,'_job_expires',true);
							
							if(strtotime(date('Y-m-d')) > strtotime( $jobexpire )){
								echo '<a href="javascript:;" class="btn btn-primary">'.esc_html__( 'Job Expired', 'service-finder' ).' <i class="fa fa-times"></i></a>';
							}else{
								if(service_finder_has_applied_for_job($jobid,$providerid))
								{
									$walletamount = service_finder_get_wallet_amount($current_user->ID);
									$walletsystem = service_finder_check_wallet_system();
									
									$settings = service_finder_getProviderSettings($providerid);
									
									if(service_finder_getUserRole($current_user->ID) == 'Provider' || service_finder_getUserRole($current_user->ID) == 'administrator'){
									$skipoption = true;
									}else{
									$skipoption = false;
									}
									
									$paymentoptions = '';
									$payflag = 0;
									$stripepublickey = '';
				
									if(service_finder_get_payment_goes_to() == 'provider')
									{
										ob_start();
										$stripepublickey = $settings['stripepublickey'];
										if(!empty($settings['paymentoption']))
										{
											foreach($settings['paymentoption'] as $paymentoption)
											{
											$payflag = 1;
											?>
											<div class="radio sf-radio-checkbox">
											  <input type="radio" value="<?php echo esc_attr($paymentoption); ?>" name="bookingpayment_mode" id="paymentvia<?php echo esc_attr($paymentoption); ?>" >
											  <label for="paymentvia<?php echo esc_attr($paymentoption); ?>"><?php echo '<img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/'.$paymentoption.'.jpg" title="'.esc_attr(ucfirst($paymentoption)).'" alt="'.esc_attr(ucfirst($paymentoption)).'">'; ?></label>
											</div>
											<?php
											}
										}elseif(!service_finder_check_wallet_system()){
											$payflag = 0;
											echo '<p>';
											echo esc_html__('There is no payment method available.','service-finder');
											echo '</p>';
										}
										
										if(service_finder_check_wallet_system())
										{
											$payflag = 1;
										}
										
										echo service_finder_add_wallet_option('bookingpayment_mode','paymentvia');
										echo service_finder_add_skip_option('bookingpayment_mode','paymentvia');
										
										$paymentoptions = ob_get_clean();
									}
									
									$params = array(
										'jobid' 	=> $jobid,
										'skipoption' 	=> $skipoption,
										'providerid' 	=> $providerid,
										'jobtitle' 	=> get_the_title($jobid),
										'jobprice' 	=> service_finder_get_job_quote_price($providerid,$jobid),
										'jobhours' 	=> get_post_meta( $jobid, '_job_hours', true ),
										'walletamount' 	=> $walletamount,
										'walletamountwithcurrency' 	=> service_finder_money_format($walletamount),
										'walletsystem' 	=> $walletsystem,
										'adminfeetype' 	=> service_finder_get_data($service_finder_options,'admin-fee-type'),
										'adminfeefixed' 	=> service_finder_get_data($service_finder_options,'admin-fee-fixed'),
										'adminfeepercentage' 	=> service_finder_get_data($service_finder_options,'admin-fee-percentage'),
										'pay_booking_amount_to' 	=> service_finder_get_payment_goes_to(),
										'paymentoptions' 	=> $paymentoptions,
										'paymentoptionsavl' 	=> $payflag,
										'stripepublickey' 	=> $stripepublickey,
										'is_booking_free_paid' 	=> service_finder_is_booking_free_paid($providerid),
									);
									echo '<a href="javascript:;" class="btn btn-primary bookthisprovider" data-params="'.esc_attr(wp_json_encode( $params )).'">'.esc_html__( 'Book Now', 'service-finder' ).'</a>';
								}
							}
						}
						?>
						<a href="<?php echo esc_url($profileurl); ?>" target="_blank" class="btn-link"><?php echo esc_html__( 'View Profile', 'service-finder' ); ?></a>
                        <?php 
						if(get_user_meta($providerid,'primary_category',true) != '')
						{
							echo '<span class="sf-profilecat-label">'.service_finder_getCategoryName(get_user_meta($providerid,'primary_category',true)).'</span>';
						}
						?>
                        <div class="checkbox sf-radio-checkbox" data-toggle="tooltip" title="<?php echo esc_html__('Send Invitation', 'service-finder') ?>">
                             <input type="checkbox" id="invitationrow-<?php echo esc_attr($providerid); ?>" class="invitationrow" value="<?php echo esc_attr($providerid); ?>">
                             <label for="invitationrow-<?php echo esc_attr($providerid); ?>" style="text-transform:none"><?php echo esc_html__( 'Add to multi-invite', 'service-finder' ); ?></label>
                        </div>
					</div>
				</div>
				<div class="sf-serach-result-right">
				
					<div class="sf-serach-result-head">
						<h3 class="sf-serach-result-title"><?php echo service_finder_getCompanyName($providerid); ?></h3>
                        <span class="sf-serach-result-name"><i class="fa fa-user"></i> <?php echo service_finder_getProviderFullName($providerid); ?></span>
						 
						 <?php if($distance != '' || $distance == 0){ 
						 $radiussearchunit = (isset($service_finder_options['radius-search-unit'])) ? esc_attr($service_finder_options['radius-search-unit']) : 'mi';
						 ?>
						 <div class="sf-serach-result-address staging-distance"><i class="fa fa-road"></i> <?php echo round($distance,2).' '.$radiussearchunit; ?> </div>
						 <?php } ?> 
						 
						 <?php 
						 if($service_finder_options['show-postal-address']){ 
						 if(($provider->service_perform_at == 'provider_location' || $provider->service_perform_at == 'both') && service_finder_getAddress($providerid) != "")
						 {
						 $providerlat = get_user_meta($providerid,'providerlat',true); 
						 $providerlng = get_user_meta($providerid,'providerlng',true); 
						 $locationzoomlevel = get_user_meta($providerid,'locationzoomlevel',true); 
						 ?>
						 <div class="sf-serach-result-address"><i class="fa fa-map-marker"></i> <?php echo service_finder_getAddress($providerid); ?> </div>
						 <button class="btn btn-primary btn-sm margin-b-10" style="margin-bottom:10px;" data-tool="tooltip" id="viewjoblocation" data-locationzoomlevel="<?php echo esc_attr($locationzoomlevel); ?>" data-providerlat="<?php echo esc_attr($providerlat); ?>" data-providerlng="<?php echo esc_attr($providerlng); ?>" type="button">
						  <i class="fa fa-map-o"></i> <?php echo esc_html__('View Map','service-finder'); ?>
						  </button>
						 <?php 
						 }elseif($provider->service_perform_at != 'provider_location' && service_finder_getshortAddress($providerid) != "")
						 {
						 ?>
						 <div class="sf-serach-result-address"><i class="fa fa-map-marker"></i> <?php echo service_finder_getshortAddress($providerid); ?> </div>
						 <?php
						 }
	                     if($provider->service_perform_at == 'provider_location' || $provider->service_perform_at == 'customer_location' || $provider->service_perform_at == 'both')
						 {
						 echo '<h4>'.esc_html__( 'Available Locations', 'service-finder') .'</h4>';
						 if($provider->service_perform_at == 'provider_location')
						 {
						 ?>
                         <div class="sf-serach-result-address"><i class="fa fa-map-pin"></i> <?php echo esc_html__( 'Provider Location', 'service-finder' ); ?> </div>
						 <?php
						 }elseif($provider->service_perform_at == 'customer_location')
						 {
						 ?>
						 <div class="sf-serach-result-address"><i class="fa fa-car"></i> <?php echo esc_html__( 'Your Location', 'service-finder' ); ?> </div>
						 <?php
						 }elseif($provider->service_perform_at == 'both')
						 {
						 ?>
						 <div class="sf-serach-result-address"><i class="fa fa-map-pin"></i> <?php echo esc_html__( 'Provider Location', 'service-finder' ); ?> </div>
                         <div class="sf-serach-result-address"><i class="fa fa-car"></i> <?php echo esc_html__( 'Your Location', 'service-finder' ); ?> </div>
						 <?php
						 }
						 }
						 } ?> 	
						 <div class="sf-serach-result-lable-wrarp">
							<?php
							if(service_finder_has_sent_invitation($jobid,$providerid))
							{
								echo '<span class="sf-serach-lable-invitation">'.esc_html__( 'Invitation Sent', 'service-finder' ).'</span>';
							}else
							{
								if(!service_finder_has_applied_for_job($jobid,$providerid))
								{
								echo '<span id="jobinvitation-'.$jobid.'-'.$providerid.'"><a class="sf-serach-lable-invitation" href="javascript:;" data-action="invite" data-redirect="no" data-jobid="'.esc_attr($jobid).'" data-providerid="'.esc_attr($providerid).'" data-toggle="modal" data-target="#invite-job">'.sprintf(esc_html__('Invite for %s', 'service-finder'),service_finder_get_data($service_finder_options,'job-text')).'</a><span>';
								}
							}
							?>
							<?php
							if(service_finder_has_applied_for_job($jobid,$providerid))
							{
								echo '<a href="javascript:;" class="sf-serach-lable-quotation provider_description" data-jobid="'.esc_attr($jobid).'" data-providerid="'.esc_attr($providerid).'">'.esc_html__( 'View Quotation', 'service-finder' ).'</a>';
							}
							?>
						 </div>
						 <div class="sf-serach-rating-addto">
							<div class="sf-serach-ratings">
								<?php echo service_finder_displayRating(service_finder_getAverageRating($providerid)); ?>
								<span class="sf-serach-ratings-total">
								<?php 
								$totalreview = service_finder_get_total_reviews($providerid);
								if($totalreview > 1){
									printf( esc_html__('(%d Reviews)', 'service-finder' ), $totalreview );
								}else{
									printf( esc_html__('(%d Review)', 'service-finder' ), $totalreview );
								}
								?>
								</span>
							</div>
							<?php
							$myfav = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->favorites.' where user_id = %d AND provider_id = %d',$current_user->ID,$providerid));

							if(!empty($myfav)){
								echo '<a href="javascript:;" id="removefave-'.esc_attr($providerid).'" class="remove-job-favorite sf-serach-addToFav" data-proid="'.esc_attr($providerid).'" data-userid="'.esc_attr($current_user->ID).'"><i class="fa fa-heart"></i></a>';
							}else
							{
								echo '<a href="javascript:;" id="addfave-'.esc_attr($providerid).'" class="add-job-favorite sf-serach-addToFav" data-proid="'.esc_attr($providerid).'" data-userid="'.esc_attr($current_user->ID).'"><i class="fa fa-heart-o"></i></a>';
							}
							?>
						 </div>
						
					 </div>
					 
					 <div class="sf-serach-result-body">
						 <div class="sf-serach-proviText">
							<?php 
							if($providerinfo->bio != ""){
								echo apply_filters('the_content', $providerinfo->bio);
							}
							?>
						 </div>
					 </div>
					 
					 <div class="sf-serach-result-footer clearfix">
						<?php
						if(service_finder_has_applied_for_job($jobid,$providerid))
						{ 
						if(class_exists('aone_messaging')){
							$args = array(
										'view' => 'popup',
										'type' => 'job',
										'targetid' => $jobid,
										'fromid' => $current_user->ID,
										'toid' => $providerid,
									);
							do_action( 'aone_messaging_custom_send_message', $args );
						}
						}
						?>
						<?php
						if(service_finder_has_applied_for_job($jobid,$providerid))
						{
							$price =  service_finder_money_format(service_finder_get_job_quote_price($providerid,$jobid));
							echo '<div class="sf-serach-result-price">'.$price.'</div>';
							
						}
						?>
					 </div>
				</div>
			</div>
			<?php
			$providerflag = 1;
		$i++;
		}
		
		if($totalproviders > 10)
		{
		?>
		<div id="loadmorejobproviders">
			<a href="javascript:;"><?php esc_html_e( 'Load More','makeover' ); ?> <i class="fa fa-refresh"></i></a>
		</div>
		<?php
		}
	}
	
	if($providerflag == 0)
	{
		if($quotereceived == 'yes')
		{
			if(service_finder_get_data($service_finder_options,'job-no-result-html-view-applicants') != '')
			{
			echo '<div class="sf-noresult-outer">';
			echo service_finder_get_data($service_finder_options,'job-no-result-html-view-applicants');
			echo '</div>';
			}else{
			echo '<div class="sf-noresult-outer">';
			echo esc_html__('No Results Found.', 'service-finder');
			echo '</div>';
			}
		}else{
			if(service_finder_get_data($service_finder_options,'job-no-result-html-send-invitations') != '')
			{
			echo '<div class="sf-noresult-outer">';
			echo service_finder_get_data($service_finder_options,'job-no-result-html-send-invitations');
			echo '</div>';
			}else{
			echo '<div class="sf-noresult-outer">';
			echo esc_html__('No Results Found.', 'service-finder');
			echo '</div>';
			}
		}
	}
	
	$response = ob_get_clean();
	wp_send_json_success($response);
	exit;
}
}

/*Get raius unit*/
function service_finder_get_radius_unit(){
	global $service_finder_options;
	
	$radiussearchunit = (isset($service_finder_options['radius-search-unit'])) ? esc_attr($service_finder_options['radius-search-unit']) : 'mi';
	
	return $radiussearchunit;
}

/*Set default business hours*/
function service_finder_set_default_business_hours($userid = 0){
	if($userid > 0)
	{
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
	update_user_meta($userid, 'timeslots', $timeslots);
	}
}

/*Set default booking settings*/
function service_finder_set_default_booking_settings($userid = 0){
	global $service_finder_options;
	
	if($userid > 0)
	{
	$paid_booking = service_finder_get_data($service_finder_options,'paid-booking');
	$free_booking = service_finder_get_data($service_finder_options,'free-booking');
	
	if($paid_booking && $free_booking){
		$default_booking_option = 'free';
	}elseif($paid_booking && !$free_booking){
		$default_booking_option = 'paid';
	}elseif(!$paid_booking && $free_booking){
		$default_booking_option = 'free';
	}else{
		$default_booking_option = 'free';
	}
	
	$options = unserialize(get_option( 'provider_settings'));
	
	$options[$userid] = array(
		'booking_process' => 'on',
		'availability_based_on' => 'timeslots',
		'slot_interval' => '30',
		'offers_based_on' => '',
		'booking_date_based_on' => 'singledate',
		'booking_basedon' => 'open',
		'booking_charge_on_service' => 'yes',
		'booking_option' => $default_booking_option,
		'future_bookings_availability' => 90,
		'buffertime' => '',
		'mincost' => '',
		'booking_assignment' => 'manually',
		'members_available' => 'no',
		'paymentoption' => '',
		'paypalusername' => '',
		'paypalpassword' => '',
		'paypalsignatue' => '',
		'stripesecretkey' => '',
		'stripepublickey' => '',
		'wired_description' => '',
		'wired_instructions' => '',
		'twocheckoutaccountid' => '',
		'twocheckoutpublishkey' => '',
		'twocheckoutprivatekey' => '',
		'payumoneymid' => '',
		'payumoneykey' => '',
		'payumoneysalt' => '',
		'payulatammerchantid' => '',
		'payulatamapilogin' => '',
		'payulatamapikey' => '',
		'payulatamaccountid' => '',
		'google_calendar' => 'off'
	);
	
	update_option( 'provider_settings', serialize($options) );
	}
}

/*Layout for no access page*/
function service_finder_no_access_layout($string1 = '',$string2 = ''){
	ob_start();	
	?>
	<div class="no-aacess-head">
        <div class="no-aacess-title"><?php echo wp_kses_post($string1); ?><span><?php echo wp_kses_post($string2); ?></span></div>
    </div>
    <div class="no-aacess-pic">
        <img src="<?php echo SERVICE_FINDER_BOOKING_IMAGE_URL.'/no-acccess.png'; ?>" alt="">
    </div>
	<?php
	$html = ob_get_clean();	
	return $html;
}	

/*Provider listing boxes*/
function service_finder_display_provider_boxes($providerid = 0,$layouttype = '',$issearch = false,$providersavailability = array(),$distance = '',$args = array()){
	global $wpdb,$current_user,$service_finder_options,$service_finder_Tables;
	$providerinfo = service_finder_get_provier_info($providerid);
	$profileurl = service_finder_get_author_url($providerid);
	
	$keyword = service_finder_get_data($args,'keyword');
	$minprice = service_finder_get_data($args,'minprice');
	$maxprice = service_finder_get_data($args,'maxprice');
	
	$params = array(
		'keyword' 	=> $keyword,
		'minprice' 	=> $minprice,
		'maxprice' 	=> $maxprice,
		'providerid' 	=> $providerid
	);
	
	ob_start();
	if($layouttype == 'grid-2')
	{
	$profilepicurl = service_finder_get_avatar_by_userid($providerid,'service_finder-featured-provider');
	?>
    <div class="col-md-6 equal-col" id="proid-<?php echo esc_attr($providerid) ?>">
        <div class="sf-feaProgrid-wrap clearfix">
        <?php if(service_finder_is_featured($providerid)){ ?>
        <div class="sf-feaProgrid-label"><?php esc_html_e('Featured', 'service-finder'); ?></div>
        <?php } ?>
        <div class="sf-feaProgrid-pic" style="background-image:url(<?php echo esc_url($profilepicurl); ?>);">
            <div class="sf-feaProgrid-info">
            <?php echo service_finder_availability_label($providerid,$providersavailability); ?>
			<?php echo service_finder_displayRating(service_finder_getAverageRating($providerid)); ?>
            <h4 class="sf-feaProgrid-title"><?php echo service_finder_getProviderFullName($providerid); ?></h4>
            <?php if(service_finder_get_data($service_finder_options,'show-address-info') && service_finder_check_address_info_access()){ ?>	
            <?php if(service_finder_getAddress($providerid) != "" && service_finder_get_data($service_finder_options,'show-postal-address')){ ?>
            <div class="sf-feaProgrid-address"><?php echo service_finder_getAddress($providerid); ?></div>
            <?php } ?>
            <?php } ?>
            <?php echo service_finder_getDistance($distance); ?>
        </div>
            <div class="sf-overlay-box"></div>
            <?php if(service_finder_is_varified_user($providerid)){ ?>
            <span class="sf-featured-approve">
                <i class="fa fa-check"></i><span><?php esc_html_e('Verified Provider', 'service-finder'); ?></span>
            </span>
            <?php } ?>
        </div>
        
        <div class="sf-feaProgrid-iconwrap">
            <?php
			if(service_finder_get_data($service_finder_options,'my-services-menu')){
			?>
            <span class="sf-feaProgrid-icon sfp-yellow sf-services-slider-btn" data-params="<?php echo esc_attr(wp_json_encode( $params )); ?>" data-providerid="<?php echo esc_attr($providerid); ?>"><span class="sf-feaPro-tooltip"><?php echo esc_html__('Display Services','service-finder'); ?></span><i class="sl-icon-settings"></i></span>
            <?php } ?>
            <?php
			if(service_finder_get_data($service_finder_options,'review-system')){
			?>
            <span class="sf-feaProgrid-icon sfp-perple"><span class="sf-feaPro-tooltip"><?php echo sprintf(_n( '%d Comment', '%d Comments', service_finder_get_total_reviews($providerid), 'service-finder' ),service_finder_get_total_reviews($providerid)); ?></span><i class="sl-icon-speech"></i></span>
            <?php } ?>
            
			<?php
            $requestquote = service_finder_get_data($service_finder_options,'requestquote-replace-string');
	
			if(service_finder_get_data($service_finder_options,'request-quote') && service_finder_request_quote_for_loggedin_user()){
			echo '<span class="sf-feaProgrid-icon sfp-green" data-providerid="'.$providerid.'" data-tool="tooltip" data-toggle="modal" data-target="#quotes-Modal"><span class="sf-feaPro-tooltip">'.esc_html__('Request Quote','service-finder').'</span><i class="sl-icon-doc"></i></span>';
			}
			?>
            
			<?php if(service_finder_get_data($service_finder_options,'add-to-fav')){ ?>
            <?php
            if(is_user_logged_in()){
                $myfav = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->favorites.' where user_id = %d AND provider_id = %d',$current_user->ID,$providerid));
                if(!empty($myfav)){
                echo '<span id="favproid-'.esc_attr($providerid).'" class="sf-feaProgrid-icon sfp-blue removefromfavorite" data-proid="'.esc_attr($providerid).'" data-userid="'.esc_attr($current_user->ID).'"><span class="sf-feaPro-tooltip">'.esc_html__('My Favorite', 'service-finder').'</span><i class="fa fa-heart"></i></span>';
                }else{
                echo '<span id="favproid-'.esc_attr($providerid).'" class="sf-feaProgrid-icon sfp-blue addtofavorite" data-proid="'.esc_attr($providerid).'" data-userid="'.esc_attr($current_user->ID).'"><span class="sf-feaPro-tooltip">'.esc_html__('Add to Favorites', 'service-finder').'</span><i class="sl-icon-heart"></i></span>';
                }
            }else{
                echo '<span id="favproid-'.esc_attr($providerid).'" class="sf-feaProgrid-icon sfp-blue" data-action="login" data-redirect="no" data-toggle="modal" data-target="#login-Modal"><span class="sf-feaPro-tooltip">'.esc_html__('Add to Favorites', 'service-finder').'</span><i class="sl-icon-heart"></i></span>';
            }
            ?>
            <?php } ?>
        </div>
        <a href="<?php echo esc_url($profileurl); ?>" class="sf-profile-link"></a>
        </div>
	</div>
	<?php
	}elseif($layouttype == 'grid-3')
	{
	$profilepicurl = service_finder_get_avatar_by_userid($providerid,'service_finder-provider-medium');
	?>
	<div class="col-md-4 col-sm-6 equal-col" id="proid-<?php echo esc_attr($providerid) ?>">
        <div class="sf-feaProgrid-wrap clearfix">
        <?php if(service_finder_is_featured($providerid)){ ?>
        <div class="sf-feaProgrid-label"><?php esc_html_e('Featured', 'service-finder'); ?></div>
        <?php } ?>
        <div class="sf-feaProgrid-pic" style="background-image:url(<?php echo esc_url($profilepicurl); ?>);">
            <div class="sf-feaProgrid-info">
            <?php echo service_finder_availability_label($providerid,$providersavailability); ?>
			<?php echo service_finder_displayRating(service_finder_getAverageRating($providerid)); ?>
            <h4 class="sf-feaProgrid-title"><?php echo service_finder_getProviderFullName($providerid); ?></h4>
            <?php if(service_finder_get_data($service_finder_options,'show-address-info') && service_finder_check_address_info_access()){ ?>	
            <?php if(service_finder_getAddress($providerid) != "" && service_finder_get_data($service_finder_options,'show-postal-address')){ ?>
            <div class="sf-feaProgrid-address"><?php echo service_finder_getAddress($providerid); ?></div>
            <?php } ?>
            <?php } ?>
            <?php echo service_finder_getDistance($distance); ?>
        </div>
            <div class="sf-overlay-box"></div>
            <a href="<?php echo esc_url($profileurl); ?>" class="sf-profile-link"></a>
            <?php if(service_finder_is_varified_user($providerid)){ ?>
            <span class="sf-featured-approve">
                <i class="fa fa-check"></i><span><?php esc_html_e('Verified Provider', 'service-finder'); ?></span>
            </span>
            <?php } ?>
        </div>
        
        <div class="sf-feaProgrid-iconwrap">
            <?php
			if(service_finder_get_data($service_finder_options,'my-services-menu')){
			?>
            <span class="sf-feaProgrid-icon sfp-yellow sf-services-slider-btn" data-params="<?php echo esc_attr(wp_json_encode( $params )); ?>" data-providerid="<?php echo esc_attr($providerid); ?>"><span class="sf-feaPro-tooltip"><?php echo esc_html__('Display Services','service-finder'); ?></span><i class="sl-icon-settings"></i></span>
            <?php } ?>
            <?php
			if(service_finder_get_data($service_finder_options,'review-system')){
			?>
            <span class="sf-feaProgrid-icon sfp-perple"><span class="sf-feaPro-tooltip"><?php echo sprintf(_n( '%d Comment', '%d Comments', service_finder_get_total_reviews($providerid), 'service-finder' ),service_finder_get_total_reviews($providerid)); ?></span><i class="sl-icon-speech"></i></span>
            <?php } ?>
            
			<?php
            $requestquote = service_finder_get_data($service_finder_options,'requestquote-replace-string');
	
			if(service_finder_get_data($service_finder_options,'request-quote') && service_finder_request_quote_for_loggedin_user()){
			echo '<span class="sf-feaProgrid-icon sfp-green" data-providerid="'.$providerid.'" data-tool="tooltip" data-toggle="modal" data-target="#quotes-Modal"><span class="sf-feaPro-tooltip">'.esc_html__('Request Quote','service-finder').'</span><i class="sl-icon-doc"></i></span>';
			}
			?>
            
			<?php if(service_finder_get_data($service_finder_options,'add-to-fav')){ ?>
            <?php
            if(is_user_logged_in()){
                $myfav = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->favorites.' where user_id = %d AND provider_id = %d',$current_user->ID,$providerid));
                if(!empty($myfav)){
                echo '<span id="favproid-'.esc_attr($providerid).'" class="sf-feaProgrid-icon sfp-blue removefromfavorite" data-proid="'.esc_attr($providerid).'" data-userid="'.esc_attr($current_user->ID).'"><span class="sf-feaPro-tooltip">'.esc_html__('My Favorite', 'service-finder').'</span><i class="fa fa-heart"></i></span>';
                }else{
                echo '<span id="favproid-'.esc_attr($providerid).'" class="sf-feaProgrid-icon sfp-blue addtofavorite" data-proid="'.esc_attr($providerid).'" data-userid="'.esc_attr($current_user->ID).'"><span class="sf-feaPro-tooltip">'.esc_html__('Add to Favorites', 'service-finder').'</span><i class="sl-icon-heart"></i></span>';
                }
            }else{
                echo '<span id="favproid-'.esc_attr($providerid).'" class="sf-feaProgrid-icon sfp-blue" data-action="login" data-redirect="no" data-toggle="modal" data-target="#login-Modal"><span class="sf-feaPro-tooltip">'.esc_html__('Add to Favorites', 'service-finder').'</span><i class="sl-icon-heart"></i></span>';
            }
            ?>
            <?php } ?>
        </div>
        </div>
	</div>                            
	<?php
	}elseif($layouttype == 'grid-4')
	{
	$profilepicurl = service_finder_get_avatar_by_userid($providerid,'service_finder-provider-medium');
	?>
	<div class="col-md-3 col-sm-6 equal-col" id="proid-<?php echo esc_attr($providerid) ?>">
        <div class="sf-feaProgrid-wrap clearfix">
        <?php if(service_finder_is_featured($providerid)){ ?>
        <div class="sf-feaProgrid-label"><?php esc_html_e('Featured', 'service-finder'); ?></div>
        <?php } ?>
        <div class="sf-feaProgrid-pic" style="background-image:url(<?php echo esc_url($profilepicurl); ?>);">
            <div class="sf-feaProgrid-info">
            <?php echo service_finder_availability_label($providerid,$providersavailability); ?>
			<?php echo service_finder_displayRating(service_finder_getAverageRating($providerid)); ?>
            <h4 class="sf-feaProgrid-title"><?php echo service_finder_getProviderFullName($providerid); ?></h4>
            <?php if(service_finder_get_data($service_finder_options,'show-address-info') && service_finder_check_address_info_access()){ ?>	
            <?php if(service_finder_getAddress($providerid) != "" && service_finder_get_data($service_finder_options,'show-postal-address')){ ?>
            <div class="sf-feaProgrid-address"><?php echo service_finder_getAddress($providerid); ?></div>
            <?php } ?>
            <?php } ?>
            <?php echo service_finder_getDistance($distance); ?>
        </div>
            <div class="sf-overlay-box"></div>
            <a href="<?php echo esc_url($profileurl); ?>" class="sf-profile-link"></a>
            <?php if(service_finder_is_varified_user($providerid)){ ?>
            <span class="sf-featured-approve">
                <i class="fa fa-check"></i><span><?php esc_html_e('Verified Provider', 'service-finder'); ?></span>
            </span>
            <?php } ?>
        </div>
        
        <div class="sf-feaProgrid-iconwrap">
            <?php
			if(service_finder_get_data($service_finder_options,'my-services-menu')){
			?>
            <span class="sf-feaProgrid-icon sfp-yellow sf-services-slider-btn" data-params="<?php echo esc_attr(wp_json_encode( $params )); ?>" data-providerid="<?php echo esc_attr($providerid); ?>"><span class="sf-feaPro-tooltip"><?php echo esc_html__('Display Services','service-finder'); ?></span><i class="sl-icon-settings"></i></span>
            <?php } ?>
            <?php
			if(service_finder_get_data($service_finder_options,'review-system')){
			?>
            <span class="sf-feaProgrid-icon sfp-perple"><span class="sf-feaPro-tooltip"><?php echo sprintf(_n( '%d Comment', '%d Comments', service_finder_get_total_reviews($providerid), 'service-finder' ),service_finder_get_total_reviews($providerid)); ?></span><i class="sl-icon-speech"></i></span>
            <?php } ?>
            <?php
            $requestquote = service_finder_get_data($service_finder_options,'requestquote-replace-string');
	
			if(service_finder_get_data($service_finder_options,'request-quote') && service_finder_request_quote_for_loggedin_user()){
			echo '<span class="sf-feaProgrid-icon sfp-green" data-providerid="'.$providerid.'" data-tool="tooltip" data-toggle="modal" data-target="#quotes-Modal"><span class="sf-feaPro-tooltip">'.esc_html__('Request Quote','service-finder').'</span><i class="sl-icon-doc"></i></span>';
			}
			?>
			<?php if(service_finder_get_data($service_finder_options,'add-to-fav')){ ?>
            <?php
            if(is_user_logged_in()){
                $myfav = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->favorites.' where user_id = %d AND provider_id = %d',$current_user->ID,$providerid));
                if(!empty($myfav)){
                echo '<span id="favproid-'.esc_attr($providerid).'" class="sf-feaProgrid-icon sfp-blue removefromfavorite" data-proid="'.esc_attr($providerid).'" data-userid="'.esc_attr($current_user->ID).'"><span class="sf-feaPro-tooltip">'.esc_html__('My Favorite', 'service-finder').'</span><i class="fa fa-heart"></i></span>';
                }else{
                echo '<span id="favproid-'.esc_attr($providerid).'" class="sf-feaProgrid-icon sfp-blue addtofavorite" data-proid="'.esc_attr($providerid).'" data-userid="'.esc_attr($current_user->ID).'"><span class="sf-feaPro-tooltip">'.esc_html__('Add to Favorites', 'service-finder').'</span><i class="sl-icon-heart"></i></span>';
                }
            }else{
                echo '<span id="favproid-'.esc_attr($providerid).'" class="sf-feaProgrid-icon sfp-blue" data-action="login" data-redirect="no" data-toggle="modal" data-target="#login-Modal"><span class="sf-feaPro-tooltip">'.esc_html__('Add to Favorites', 'service-finder').'</span><i class="sl-icon-heart"></i></span>';
            }
            ?>
            <?php } ?>
        </div>
        </div>
	</div>                            
	<?php
	}elseif($layouttype == 'listview')
	{
	$profilepicurl = service_finder_get_avatar_by_userid($providerid,'service_finder-featured-provider');
	?>
    <?php
	$iconbox = 1;
	if($service_finder_options['review-system']){
		$iconbox++;
	}
	if($service_finder_options['request-quote'] && service_finder_request_quote_for_loggedin_user()){
		$iconbox++;
	}
	if($service_finder_options['add-to-fav']){
		$iconbox++;
	}
	?>
    <?php if($service_finder_options['search-template'] == 'style-2' && service_finder_show_map_on_site() && $issearch == true){ ?>
    <div class="col-md-12 equal-col" id="proid-<?php echo esc_attr($providerid) ?>">
      <div class="sf-feaProvideer-wrap clearfix">
      	<?php if(service_finder_is_featured($providerid)){ ?>
        <div class="sf-feaProvideer-label"><?php echo esc_html__('Featured','service-finder'); ?></div>
        <?php } ?>
        <div class="sf-feaProvideer-pic">
        <img src="<?php echo esc_url($profilepicurl); ?>" alt="">
        <?php if(service_finder_is_varified_user($providerid)){ ?>
        <span class="sf-featured-approve">
            <i class="fa fa-check"></i><span><?php esc_html_e('Verified Provider', 'service-finder'); ?></span>
        </span>
        <?php } ?>
        </div>
        <div class="sf-feaProvideer-info">
          <?php echo service_finder_availability_label($providerid,$providersavailability); ?>
		  <?php echo service_finder_displayRating(service_finder_getAverageRating($providerid)); ?>  
          <h4 class="sf-feaProvideer-title"><?php echo service_finder_getProviderFullName($providerid); ?></h4>
          <?php
          if(service_finder_get_data($service_finder_options,'show-address-info') && $service_finder_options['show-postal-address']){
          echo '<div class="sf-feaProvideer-address">'.service_finder_getshortAddress($providerid).'</div>';
          }
          ?>
          <?php echo service_finder_getDistance($distance); ?>
          <?php echo service_finder_display_category_label($providerid); ?>
          <?php echo service_finder_get_service_startfrom($providerid); ?>
          <div class="sf-feaProvideer-text"><?php echo service_finder_getExcerpts(nl2br(stripcslashes($providerinfo->bio)),0,130); ?></div>
        </div>
        <div class="sf-feaProvideer-iconwrap sf-iconbox-cnt-<?php echo esc_attr($iconbox); ?>">
            <?php
			if(service_finder_get_data($service_finder_options,'my-services-menu')){
			?>
            <span class="sf-feaProvideer-icon sfp-yellow sf-services-slider-btn" data-params="<?php echo esc_attr(wp_json_encode( $params )); ?>" data-providerid="<?php echo esc_attr($providerid); ?>"><span class="sf-feaPro-tooltip"><?php echo esc_html__('Display Services','service-finder'); ?></span><i class="sl-icon-settings"></i></span>
            <?php } ?>
			<?php
            if($service_finder_options['review-system']){
            $reviewcount = show_review_at_search_result($providerid);
            echo '<span class="sf-feaProvideer-icon sfp-perple"><span class="sf-feaPro-tooltip">'.$reviewcount.' '.esc_html__('Comment','service-finder').'</span><i class="sl-icon-speech"></i></span>';
            }
            
            $requestquote = (!empty($service_finder_options['requestquote-replace-string'])) ? esc_attr($service_finder_options['requestquote-replace-string']) : esc_html__( 'Request a Quote', 'service-finder' );
    
            if($service_finder_options['request-quote'] && service_finder_request_quote_for_loggedin_user()){
            echo '<span class="sf-feaProvideer-icon sfp-green" data-providerid="'.$providerid.'" data-tool="tooltip" data-toggle="modal" data-target="#quotes-Modal"><span class="sf-feaPro-tooltip">'.esc_html__('Request Quote','service-finder').'</span><i class="sl-icon-doc"></i></span>';
            }
			
			if($service_finder_options['add-to-fav']){
				if(is_user_logged_in()){
					$myfav = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->favorites.' where user_id = %d AND provider_id = %d',$current_user->ID,$providerid));
					if(!empty($myfav)){
					echo '<span id="favproid-'.esc_attr($providerid).'" class="sf-feaProgrid-icon sfp-blue removefromfavorite" data-proid="'.esc_attr($providerid).'" data-userid="'.esc_attr($current_user->ID).'"><span class="sf-feaPro-tooltip">'.esc_html__('My Favorite', 'service-finder').'</span><i class="fa fa-heart"></i></span>';
					}else{
					echo '<span id="favproid-'.esc_attr($providerid).'" class="sf-feaProgrid-icon sfp-blue addtofavorite" data-proid="'.esc_attr($providerid).'" data-userid="'.esc_attr($current_user->ID).'"><span class="sf-feaPro-tooltip">'.esc_html__('Add to Favorites', 'service-finder').'</span><i class="sl-icon-heart"></i></span>';
					}
				}else{
					echo '<span id="favproid-'.esc_attr($providerid).'" class="sf-feaProgrid-icon sfp-blue" data-action="login" data-redirect="no" data-toggle="modal" data-target="#login-Modal"><span class="sf-feaPro-tooltip">'.esc_html__('Add to Favorites', 'service-finder').'</span><i class="sl-icon-heart"></i></span>';
				}
			}
            ?>
        </div>
        <a href="<?php echo esc_url($profileurl); ?>" class="sf-profile-link"></a>
        
      </div>
    </div>
	<?php
	}else{
	?>
	<div class="col-md-6 equal-col" id="proid-<?php echo esc_attr($providerid) ?>">
      <div class="sf-feaProvideer-wrap clearfix">
      	<?php if(service_finder_is_featured($providerid)){ ?>
        <div class="sf-feaProvideer-label"><?php echo esc_html__('Featured','service-finder'); ?></div>
        <?php } ?>
        <div class="sf-feaProvideer-pic">
        <img src="<?php echo esc_url($profilepicurl); ?>" alt="">
        <?php if(service_finder_is_varified_user($providerid)){ ?>
        <span class="sf-featured-approve">
            <i class="fa fa-check"></i><span><?php esc_html_e('Verified Provider', 'service-finder'); ?></span>
        </span>
        <?php } ?>
        </div>
        <div class="sf-feaProvideer-info">
          <?php echo service_finder_availability_label($providerid,$providersavailability); ?>
		  <?php echo service_finder_displayRating(service_finder_getAverageRating($providerid)); ?>  
          <h4 class="sf-feaProvideer-title"><?php echo service_finder_getProviderFullName($providerid); ?></h4>
          <?php
          if(service_finder_get_data($service_finder_options,'show-address-info') && $service_finder_options['show-postal-address']){
          echo '<div class="sf-feaProvideer-address">'.service_finder_getshortAddress($providerid).'</div>';
          }
          ?>
          <?php echo service_finder_getDistance($distance); ?>
          <?php echo service_finder_display_category_label($providerid); ?>
          <?php echo service_finder_get_service_startfrom($providerid); ?>
          <div class="sf-feaProvideer-text"><?php echo service_finder_getExcerpts(nl2br(stripcslashes($providerinfo->bio)),0,130); ?></div>
        </div>
        
        <div class="sf-feaProvideer-iconwrap sf-iconbox-cnt-<?php echo esc_attr($iconbox); ?>">
            <?php
			if(service_finder_get_data($service_finder_options,'my-services-menu')){
			?>
            <span class="sf-feaProvideer-icon sfp-yellow sf-services-slider-btn" data-params="<?php echo esc_attr(wp_json_encode( $params )); ?>" data-providerid="<?php echo esc_attr($providerid); ?>"><span class="sf-feaPro-tooltip"><?php echo esc_html__('Display Services','service-finder'); ?></span><i class="sl-icon-settings"></i></span>
            <?php } ?>
			<?php
            if($service_finder_options['review-system']){
            $reviewcount = show_review_at_search_result($providerid);
            echo '<span class="sf-feaProvideer-icon sfp-perple"><span class="sf-feaPro-tooltip">'.$reviewcount.' '.esc_html__('Comment','service-finder').'</span><i class="sl-icon-speech"></i></span>';
            }
            
            $requestquote = (!empty($service_finder_options['requestquote-replace-string'])) ? esc_attr($service_finder_options['requestquote-replace-string']) : esc_html__( 'Request a Quote', 'service-finder' );
    
            if($service_finder_options['request-quote'] && service_finder_request_quote_for_loggedin_user()){
            echo '<span class="sf-feaProvideer-icon sfp-green" data-providerid="'.$providerid.'" data-tool="tooltip" data-toggle="modal" data-target="#quotes-Modal"><span class="sf-feaPro-tooltip">'.$requestquote.'</span><i class="sl-icon-doc"></i></span>';
            }
			
			if($service_finder_options['add-to-fav']){
				if(is_user_logged_in()){
					$myfav = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->favorites.' where user_id = %d AND provider_id = %d',$current_user->ID,$providerid));
					if(!empty($myfav)){
					echo '<span id="favproid-'.esc_attr($providerid).'" class="sf-feaProgrid-icon sfp-blue removefromfavorite" data-proid="'.esc_attr($providerid).'" data-userid="'.esc_attr($current_user->ID).'"><span class="sf-feaPro-tooltip">'.esc_html__('My Favorite', 'service-finder').'</span><i class="fa fa-heart"></i></span>';
					}else{
					echo '<span id="favproid-'.esc_attr($providerid).'" class="sf-feaProgrid-icon sfp-blue addtofavorite" data-proid="'.esc_attr($providerid).'" data-userid="'.esc_attr($current_user->ID).'"><span class="sf-feaPro-tooltip">'.esc_html__('Add to Favorites', 'service-finder').'</span><i class="sl-icon-heart"></i></span>';
					}
				}else{
					echo '<span id="favproid-'.esc_attr($providerid).'" class="sf-feaProgrid-icon sfp-blue" data-action="login" data-redirect="no" data-toggle="modal" data-target="#login-Modal"><span class="sf-feaPro-tooltip">'.esc_html__('Add to Favorites', 'service-finder').'</span><i class="sl-icon-heart"></i></span>';
				}
			}
            ?>
        </div>
        <a href="<?php echo esc_url($profileurl); ?>" class="sf-profile-link"></a>
        
      </div>
    </div>
	<?php
	}
	}
	$html = ob_get_clean();
	return $html;
}

function service_finder_provider_box_fourth($providerid = 0,$layouttype = '',$issearch = false,$providersavailability = array(),$distance = '',$args = array()){
	global $wpdb,$current_user,$service_finder_options,$service_finder_Tables;
	$providerinfo = service_finder_get_provier_info($providerid);
	$profileurl = service_finder_get_author_url($providerid);
	
	$keyword = service_finder_get_data($args,'keyword');
	$minprice = service_finder_get_data($args,'minprice');
	$maxprice = service_finder_get_data($args,'maxprice');
	
	$params = array(
		'keyword' 	=> $keyword,
		'minprice' 	=> $minprice,
		'maxprice' 	=> $maxprice,
		'providerid' 	=> $providerid
	);
	
	ob_start();
	if($layouttype == 'grid-2')
	{
	$profilepicurl = service_finder_get_avatar_by_userid($providerid,'service_finder-featured-provider');
	?>
    <div class="col-md-6" id="proid-<?php echo esc_attr($providerid) ?>">
        <div class="sf-ow-provider-wrap">
            <div class="sf-ow-provider">

                <div class="sf-ow-top">
                    <?php if(service_finder_is_varified_user($providerid)){ ?>
                    <div class="sf-pro-check">
                        <span><i class="fa fa-check"></i></span>
                        <strong class="sf-verified-label"><?php echo esc_html__( 'Verified', 'service-finder' ); ?></strong>
                    </div>
                    <?php } ?>
                    
                    <?php if(service_finder_get_data($service_finder_options,'add-to-fav')){
					if($service_finder_options['add-to-fav']){
						if(is_user_logged_in()){
							$myfav = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->favorites.' where user_id = %d AND provider_id = %d',$current_user->ID,$providerid));
							if(!empty($myfav)){
							echo '<div class="sf-pro-favorite"><a id="favproid-'.esc_attr($providerid).'" data-proid="'.esc_attr($providerid).'" data-userid="'.esc_attr($current_user->ID).'" href="javascript:;" class="removefromfavoriteshort"><i class="fa fa-heart"></i></a></div>';
							}else{
							echo '<div class="sf-pro-favorite"><a id="favproid-'.esc_attr($providerid).'" data-proid="'.esc_attr($providerid).'" data-userid="'.esc_attr($current_user->ID).'" href="javascript:;" class="addtofavoriteshort"><i class="fa fa-heart-o"></i></a></div>';
							}
						}else{
							echo '<div class="sf-pro-favorite"><a href="javascript:;" data-action="login" data-redirect="no" data-toggle="modal" data-target="#login-Modal"><i class="fa fa-heart-o"></i></a></div>';
						}
					}
					} ?>
                    
                    <div class="sf-ow-info">
                        <h4 class="sf-title"><a href="<?php echo esc_url($profileurl); ?>"><?php echo service_finder_getProviderFullName($providerid); ?></a></h4>
                        <?php if(service_finder_get_data($service_finder_options,'show-address-info') && service_finder_check_address_info_access()){ ?>	
						<?php if(service_finder_getAddress($providerid) != "" && service_finder_get_data($service_finder_options,'show-postal-address')){ ?>
                        <span><?php echo service_finder_getAddress($providerid); ?></span>
                        <?php } ?>
                        <?php } ?>
                    </div>
                </div>
                <div class="sf-ow-mid">
                    <div class="sf-ow-media">
                        <a href="<?php echo esc_url($profileurl); ?>"><img src="<?php echo esc_url($profilepicurl); ?>"></a>
                        <?php 
						if(service_finder_is_featured($providerid)){
							echo '<div  class="sf-featured-tag">'.esc_html__( 'Featured', 'service-finder' ).'</div>';
						}
						?>
                    </div>
                    <p><?php echo service_finder_getExcerpts(nl2br(stripcslashes($providerinfo->bio)),0,80); ?></p>
                    <?php echo service_finder_displayRating(service_finder_getAverageRating($providerid)); ?>
                </div>
            </div>
            <?php
            $requestquote = service_finder_get_data($service_finder_options,'requestquote-replace-string',esc_html__('Request A Quote','service-finder'));
	
			if(service_finder_get_data($service_finder_options,'request-quote') && service_finder_request_quote_for_loggedin_user()){
			?>
			<div class="sf-ow-bottom">
                <a href="javascript:;" data-providerid="<?php echo esc_attr($providerid); ?>" data-tool="tooltip" data-toggle="modal" data-target="#quotes-Modal"><?php echo esc_html($requestquote); ?></a>
            </div>
			<?php
			}
			?>
            
        </div>
    </div>
	<?php
	}elseif($layouttype == 'grid-3')
	{
	$profilepicurl = service_finder_get_avatar_by_userid($providerid,'service_finder-featured-provider');
	?>
	<div class="col-md-4" id="proid-<?php echo esc_attr($providerid) ?>">
        <div class="sf-ow-provider-wrap">
            <div class="sf-ow-provider">

                <div class="sf-ow-top">
                    <?php if(service_finder_is_varified_user($providerid)){ ?>
                    <div class="sf-pro-check">
                        <span><i class="fa fa-check"></i></span>
                        <strong class="sf-verified-label"><?php echo esc_html__( 'Verified', 'service-finder' ); ?></strong>
                    </div>
                    <?php } ?>
                    
                    <?php if(service_finder_get_data($service_finder_options,'add-to-fav')){
					if($service_finder_options['add-to-fav']){
						if(is_user_logged_in()){
							$myfav = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->favorites.' where user_id = %d AND provider_id = %d',$current_user->ID,$providerid));
							if(!empty($myfav)){
							echo '<div class="sf-pro-favorite"><a id="favproid-'.esc_attr($providerid).'" data-proid="'.esc_attr($providerid).'" data-userid="'.esc_attr($current_user->ID).'" href="javascript:;" class="removefromfavoriteshort"><i class="fa fa-heart"></i></a></div>';
							}else{
							echo '<div class="sf-pro-favorite"><a id="favproid-'.esc_attr($providerid).'" data-proid="'.esc_attr($providerid).'" data-userid="'.esc_attr($current_user->ID).'" href="javascript:;" class="addtofavoriteshort"><i class="fa fa-heart-o"></i></a></div>';
							}
						}else{
							echo '<div class="sf-pro-favorite"><a href="javascript:;" data-action="login" data-redirect="no" data-toggle="modal" data-target="#login-Modal"><i class="fa fa-heart-o"></i></a></div>';
						}
					}
					} ?>
                    
                    <div class="sf-ow-info">
                        <h4 class="sf-title"><a href="<?php echo esc_url($profileurl); ?>"><?php echo service_finder_getProviderFullName($providerid); ?></a></h4>
                        <?php if(service_finder_get_data($service_finder_options,'show-address-info') && service_finder_check_address_info_access()){ ?>	
						<?php if(service_finder_getAddress($providerid) != "" && service_finder_get_data($service_finder_options,'show-postal-address')){ ?>
                        <span><?php echo service_finder_getshortAddress($providerid); ?></span>
                        <?php } ?>
                        <?php } ?>
                    </div>
                </div>
                <div class="sf-ow-mid">
                    <div class="sf-ow-media">
                        <a href="<?php echo esc_url($profileurl); ?>"><img src="<?php echo esc_url($profilepicurl); ?>"></a>
                        <?php 
						if(service_finder_is_featured($providerid)){
							echo '<div  class="sf-featured-tag">'.esc_html__( 'Featured', 'service-finder' ).'</div>';
						}
						?>
                    </div>
                    <p><?php echo service_finder_getExcerpts(nl2br(stripcslashes($providerinfo->bio)),0,80); ?></p>
                    <?php echo service_finder_displayRating(service_finder_getAverageRating($providerid)); ?>
                </div>
            </div>
            <?php
            $requestquote = service_finder_get_data($service_finder_options,'requestquote-replace-string',esc_html__('Request A Quote','service-finder'));
	
			if(service_finder_get_data($service_finder_options,'request-quote') && service_finder_request_quote_for_loggedin_user()){
			?>
			<div class="sf-ow-bottom">
                <a href="javascript:;" data-providerid="<?php echo esc_attr($providerid); ?>" data-tool="tooltip" data-toggle="modal" data-target="#quotes-Modal"><?php echo esc_html($requestquote); ?></a>
            </div>
			<?php
			}
			?>
            
        </div>
    </div>
	<?php
	}elseif($layouttype == 'listview')
	{
	$profilepicurl = service_finder_get_avatar_by_userid($providerid,'service_finder-featured-provider');
	?>
    <?php
	$iconbox = 1;
	if($service_finder_options['review-system']){
		$iconbox++;
	}
	if($service_finder_options['request-quote'] && service_finder_request_quote_for_loggedin_user()){
		$iconbox++;
	}
	if($service_finder_options['add-to-fav']){
		$iconbox++;
	}
	?>
    <?php if($service_finder_options['search-template'] == 'style-2' && service_finder_show_map_on_site() && $issearch == true){ ?>
    <div class="col-md-6" id="proid-<?php echo esc_attr($providerid) ?>">
        <div class="sf-vender-list-wrap">
            <div class="sf-vender-list-box d-flex">
                <div class="sf-vender-list-pic" style="background-image:url(<?php echo esc_url($profilepicurl); ?>)">
                    <a class="sf-vender-pic-link" href="<?php echo esc_url($profileurl); ?>"></a>
                    <?php 
					if(service_finder_is_featured($providerid)){
						echo '<div  class="sf-featured-tag">'.esc_html__( 'Featured', 'service-finder' ).'</div>';
					}
					?>
                </div>
                <div class="sf-vender-list-info">
                    <h4 class="sf-venders-title"><a href="<?php echo esc_url($profileurl); ?>"><?php echo service_finder_getProviderFullName($providerid); ?></a></h4>
                    
                    <?php
					if(service_finder_get_data($service_finder_options,'show-address-info') && $service_finder_options['show-postal-address']){
						echo '<span class="sf-venders-address"><i class="fa fa-map-marker"></i>'.service_finder_getshortAddress($providerid).'</span>';
					}
					?>
                    <?php echo service_finder_displayRating(service_finder_getAverageRating($providerid)); ?>
                    <p><?php echo service_finder_getExcerpts(nl2br(stripcslashes($providerinfo->bio)),0,80); ?></p>
                    <?php if(service_finder_is_varified_user($providerid)){ ?>
                    <div class="sf-pro-check">
                        <span><i class="fa fa-check"></i></span>
                        <strong class="sf-verified-label"><?php echo esc_html__( 'Verified', 'service-finder' ); ?></strong>
                    </div>
                    <?php } ?>
                    
                    <?php if(service_finder_get_data($service_finder_options,'add-to-fav')){
					if($service_finder_options['add-to-fav']){
						if(is_user_logged_in()){
							$myfav = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->favorites.' where user_id = %d AND provider_id = %d',$current_user->ID,$providerid));
							if(!empty($myfav)){
							echo '<div class="sf-pro-favorite"><a id="favproid-'.esc_attr($providerid).'" data-proid="'.esc_attr($providerid).'" data-userid="'.esc_attr($current_user->ID).'" href="javascript:;" class="removefromfavoriteshort"><i class="fa fa-heart"></i></a></div>';
							}else{
							echo '<div class="sf-pro-favorite"><a id="favproid-'.esc_attr($providerid).'" data-proid="'.esc_attr($providerid).'" data-userid="'.esc_attr($current_user->ID).'" href="javascript:;" class="addtofavoriteshort"><i class="fa fa-heart-o"></i></a></div>';
							}
						}else{
							echo '<div class="sf-pro-favorite"><a href="javascript:;" data-action="login" data-redirect="no" data-toggle="modal" data-target="#login-Modal"><i class="fa fa-heart-o"></i></a></div>';
						}
					}
					} ?>
                    
                    <div class="dropdown action-dropdown dropdown-left">
                        <button class="action-button gray dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-ellipsis-v"></i></button>
                        <ul class="dropdown-menu">
                            <?php
							if(service_finder_get_data($service_finder_options,'my-services-menu')){
							echo '<li><a href="javascript:;" class="sf-services-slider-btn" data-params="'.esc_attr(wp_json_encode( $params )).'" data-providerid="'.esc_attr($providerid).'"><i class="sl-icon-settings"></i> '.esc_html__('Display Services','service-finder').'</a></li>';
							}
							
							if($service_finder_options['review-system']){
							$reviewcount = show_review_at_search_result($providerid);
							echo '<li><a href="javascript:;"><i class="sl-icon-bubble"></i> '.$reviewcount.' '.esc_html__('Comment','service-finder').'</a></li>';
							}
							
							$requestquote = (!empty($service_finder_options['requestquote-replace-string'])) ? esc_attr($service_finder_options['requestquote-replace-string']) : esc_html__( 'Request a Quote', 'service-finder' );
    
							if($service_finder_options['request-quote'] && service_finder_request_quote_for_loggedin_user()){
							echo '<li><a href="javascript:;" data-providerid="'.$providerid.'" data-tool="tooltip" data-toggle="modal" data-target="#quotes-Modal"><i class="sl-icon-doc"></i> '.$requestquote.'e</a></li>';
							}
							?>	
                        </ul>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
	<?php
	}else{
	?>
	<div class="col-md-6" id="proid-<?php echo esc_attr($providerid) ?>">
        <div class="sf-vender-list-wrap">
            <div class="sf-vender-list-box d-flex">
                <div class="sf-vender-list-pic" style="background-image:url(<?php echo esc_url($profilepicurl); ?>)">
                    <a class="sf-vender-pic-link" href="<?php echo esc_url($profileurl); ?>"></a>
                    <?php 
					if(service_finder_is_featured($providerid)){
						echo '<div  class="sf-featured-tag">'.esc_html__( 'Featured', 'service-finder' ).'</div>';
					}
					?>
                </div>
                <div class="sf-vender-list-info">
                    <h4 class="sf-venders-title"><a href="<?php echo esc_url($profileurl); ?>"><?php echo service_finder_getProviderFullName($providerid); ?></a></h4>
                    
                    <?php
					if(service_finder_get_data($service_finder_options,'show-address-info') && $service_finder_options['show-postal-address']){
						echo '<span class="sf-venders-address"><i class="fa fa-map-marker"></i>'.service_finder_getshortAddress($providerid).'</span>';
					}
					?>
                    <?php echo service_finder_displayRating(service_finder_getAverageRating($providerid)); ?>
                    <p><?php echo service_finder_getExcerpts(nl2br(stripcslashes($providerinfo->bio)),0,130); ?></p>
                    <?php if(service_finder_is_varified_user($providerid)){ ?>
                    <div class="sf-pro-check">
                        <span><i class="fa fa-check"></i></span>
                        <strong class="sf-verified-label"><?php echo esc_html__( 'Verified', 'service-finder' ); ?></strong>
                    </div>
                    <?php } ?>
                    
                    <?php if(service_finder_get_data($service_finder_options,'add-to-fav')){
					if($service_finder_options['add-to-fav']){
						if(is_user_logged_in()){
							$myfav = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->favorites.' where user_id = %d AND provider_id = %d',$current_user->ID,$providerid));
							if(!empty($myfav)){
							echo '<div class="sf-pro-favorite"><a id="favproid-'.esc_attr($providerid).'" data-proid="'.esc_attr($providerid).'" data-userid="'.esc_attr($current_user->ID).'" href="javascript:;" class="removefromfavoriteshort"><i class="fa fa-heart"></i></a></div>';
							}else{
							echo '<div class="sf-pro-favorite"><a id="favproid-'.esc_attr($providerid).'" data-proid="'.esc_attr($providerid).'" data-userid="'.esc_attr($current_user->ID).'" href="javascript:;" class="addtofavoriteshort"><i class="fa fa-heart-o"></i></a></div>';
							}
						}else{
							echo '<div class="sf-pro-favorite"><a href="javascript:;" data-action="login" data-redirect="no" data-toggle="modal" data-target="#login-Modal"><i class="fa fa-heart-o"></i></a></div>';
						}
					}
					} ?>
                    
                    <div class="dropdown action-dropdown dropdown-left">
                        <button class="action-button gray dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-ellipsis-v"></i></button>
                        <ul class="dropdown-menu">
                            <?php
							if(service_finder_get_data($service_finder_options,'my-services-menu')){
							echo '<li><a href="javascript:;" class="sf-services-slider-btn" data-params="'.esc_attr(wp_json_encode( $params )).'" data-providerid="'.esc_attr($providerid).'"><i class="sl-icon-settings"></i> '.esc_html__('Display Services','service-finder').'</a></li>';
							}
							
							if($service_finder_options['review-system']){
							$reviewcount = show_review_at_search_result($providerid);
							echo '<li><a href="javascript:;"><i class="sl-icon-bubble"></i> '.$reviewcount.' '.esc_html__('Comment','service-finder').'</a></li>';
							}
							
							$requestquote = (!empty($service_finder_options['requestquote-replace-string'])) ? esc_attr($service_finder_options['requestquote-replace-string']) : esc_html__( 'Request a Quote', 'service-finder' );
    
							if($service_finder_options['request-quote'] && service_finder_request_quote_for_loggedin_user()){
							echo '<li><a href="javascript:;" data-providerid="'.$providerid.'" data-tool="tooltip" data-toggle="modal" data-target="#quotes-Modal"><i class="sl-icon-doc"></i> '.$requestquote.'e</a></li>';
							}
							?>	
                        </ul>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
	<?php
	}
	}
	$html = ob_get_clean();
	return $html;
}

/*Check contact number is accessable*/
function service_finder_contact_number_is_accessible($providerid = 0){
global $service_finder_options;

	$usercap = service_finder_get_capability($providerid);
	
	if(in_array('contact-numbers',$usercap))
	{
		return true;
	}else
	{
		if(service_finder_get_data($service_finder_options,'show-address-info') && service_finder_get_data($service_finder_options,'show-contact-number') && service_finder_check_address_info_access()){
			return true;
		}else
		{
			return false;
		}
	}
}

function service_finder_get_buffer_time_interval(){

$intervals = array(5 => esc_html__('5 Mins', 'service-finder'),10 => esc_html__('10 Mins', 'service-finder'),15 => esc_html__('15 Mins', 'service-finder'),20 => esc_html__('20 Mins', 'service-finder'),25 => esc_html__('25 Mins', 'service-finder'),30 => esc_html__('30 Mins', 'service-finder'),35 => esc_html__('35 Mins', 'service-finder'),40 => esc_html__('40 Mins', 'service-finder'),45 => esc_html__('45 Mins', 'service-finder'),50 => esc_html__('50 Mins', 'service-finder'),55 => esc_html__('55 Mins', 'service-finder'),60 => esc_html__('1 Hr', 'service-finder'),75 => esc_html__('1 Hr 15 Mins', 'service-finder'),90 => esc_html__('1 Hr 30 Mins', 'service-finder'),105 => esc_html__('1 Hr 45 Mins', 'service-finder'),120 => esc_html__('2 Hrs', 'service-finder'),150 => esc_html__('2 Hrs 30 Mins', 'service-finder'),180 => esc_html__('3 Hrs', 'service-finder'),210 => esc_html__('3 Hr 30 Mins', 'service-finder'),240 => esc_html__('4 Hrs', 'service-finder'));

return $intervals;

}

/*Return end time if buffer exist other wise original time will return*/
function service_finder_get_booking_end_time($end_time = '',$end_time_no_bufer = ''){
	
	if($end_time_no_bufer != NULL && $end_time_no_bufer != '' && $end_time_no_bufer != 'NULL')
	{
		return $end_time_no_bufer;
	}else{
		return $end_time;
	}
}

/*Check pay only admin fee is on/off*/
function service_finder_has_pay_only_admin_fee(){
	global $service_finder_options;
	
	if(service_finder_get_data($service_finder_options,'pay-only-admin-fee'))
	{
		return true;
	}else{
		return false;
	}
}

/*Check pay only admin fee is on/off*/
function service_finder_get_primary_category_tag($providerid = 0){
	global $service_finder_options;
	$html = '';
	if(get_user_meta($providerid,'primary_category',true) != '')
	{
	$html = '<strong class="sf-category-tag"><a href="'.esc_url(service_finder_getCategoryLink(get_user_meta($providerid,'primary_category',true))).'">'.service_finder_getCategoryName(get_user_meta($providerid,'primary_category',true)).'</a></strong>';
	}
	
	return $html;
}

/*Get Job Categories*/
function service_finder_get_job_categories($jobid = 0){
	$categories = wp_get_post_terms( $jobid, 'job_listing_category');
	$catname= array();
    if(!empty($categories)){
		foreach($categories as $category){
			$catname[] = $category->name;
		}
	}	
	
	return $catname;
}

/*Get Total providers in city*/
function service_finder_total_city_providers($cityname = ''){
	global $wpdb, $service_finder_Tables, $service_finder_options;
	
	$identitycheck = (isset($service_finder_options['identity-check'])) ? esc_attr($service_finder_options['identity-check']) : '';
	$restrictuserarea = (isset($service_finder_options['restrict-user-area'])) ? esc_attr($service_finder_options['restrict-user-area']) : '';
	
	if($restrictuserarea && $identitycheck){
	$sql = 'SELECT * FROM '.$service_finder_Tables->providers.' WHERE admin_moderation = "approved" AND identity = "approved" AND account_blocked != "yes" AND city = "'.$cityname.'"';
	}else{
	$sql = 'SELECT * FROM '.$service_finder_Tables->providers.' WHERE admin_moderation = "approved" AND account_blocked != "yes" AND city = "'.$cityname.'"';
	}

	$providers_total = $wpdb->get_results($sql);
	$total = count($providers_total);
	
	return $total;
}

/*Get available connected account balance*/
function service_finder_get_stripe_connect_avl_balance($providerid = 0){
global $service_finder_options;

    require_once(SERVICE_FINDER_PAYMENT_GATEWAY_DIR.'/stripe/init.php');
	
	$avlbalance = 0;
		
	$acct_id = service_finder_get_stripe_connect_id($providerid);
	
	$stripetype = (!empty($service_finder_options['stripe-type'])) ? esc_html($service_finder_options['stripe-type']) : '';
	if($stripetype == 'live'){
		$secret_key = (!empty($service_finder_options['stripe-live-secret-key'])) ? esc_html($service_finder_options['stripe-live-secret-key']) : '';
	}else{
		$secret_key = (!empty($service_finder_options['stripe-test-secret-key'])) ? esc_html($service_finder_options['stripe-test-secret-key']) : '';
	}
	
	\Stripe\Stripe::setApiKey($secret_key);
	
	if($acct_id == '')
	{
		return $avlbalance;
	}
	
	try {
		$balance = \Stripe\Balance::retrieve(
		  array(
			"stripe_account" => $acct_id
		  )
		);
		
		if(!empty($balance))
		{
			$avlbalance = $balance->available[0]['amount'];
			$avlbalance = floatval($avlbalance)/100;
		}
		
		return $avlbalance;
	
	} catch (Exception $e) {
	
		return $avlbalance;
	}
}

/*Get available balance*/
function service_finder_get_stripe_avl_balance(){
global $service_finder_options;

    require_once(SERVICE_FINDER_PAYMENT_GATEWAY_DIR.'/stripe/init.php');
	
	$avlbalance = 0;
		
	$stripetype = (!empty($service_finder_options['stripe-type'])) ? esc_html($service_finder_options['stripe-type']) : '';
	if($stripetype == 'live'){
		$secret_key = (!empty($service_finder_options['stripe-live-secret-key'])) ? esc_html($service_finder_options['stripe-live-secret-key']) : '';
	}else{
		$secret_key = (!empty($service_finder_options['stripe-test-secret-key'])) ? esc_html($service_finder_options['stripe-test-secret-key']) : '';
	}
	
	\Stripe\Stripe::setApiKey($secret_key);
	
	try {
	
		$balance = \Stripe\Balance::retrieve();
		
		if(!empty($balance))
		{
			$avlbalance = $balance->available[0]['amount'];
			$avlbalance = floatval($avlbalance)/100;
		}
		
		return $avlbalance;
	
	} catch (Exception $e) {
	
		return $avlbalance;
	}
}

/*Get service location*/
function service_finder_get_service_location($bookingid = 0){
	global $wpdb, $service_finder_Tables, $service_finder_options;
	
	$bookingdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `id` = %d',$bookingid));
	
	if(!empty($bookingdata))
	{
		return $bookingdata->service_location;
	}else
	{
		return esc_html__('N/A', 'service-finder');
	}
}

/*Check is new installation or existing client*/
function service_finder_check_new_client(){
	global $wpdb, $service_finder_Tables, $service_finder_options;
	$flag = 0;
	$result = $wpdb->get_results('SHOW TABLES');
	$dbname = key($result[0]);
	foreach ($result as $mytable)
    {
        if($mytable->$dbname == 'service_finder_cities')
		{
			$flag = 1;
			break;
		}
    }
	
	if($flag == 1)
	{
		return false;
	}else{
		return true;
	}
}

/*Check is database updated or not*/
function service_finder_is_updated_database(){
	global $wpdb, $service_finder_Tables, $service_finder_options;
	$flag = 0;
	
	$results = $wpdb->get_results("show columns from ".$service_finder_Tables->providers);
	
	foreach($results as $result){
		if($result->Field == 'radius'){
			$flag = 1;
			break;
		}
	}	
	
	if($flag == 1)
	{
		return true;
	}else{
		return false;
	}
}

/*Display category label*/
function service_finder_display_category_label($providerid = 0){
	
	if(service_finder_getCategoryName(get_user_meta($providerid,'primary_category',true)) != '')
	{
		$catlabel = '<span class="sf-categories-label"><a href="'.esc_url(service_finder_getCategoryLink(get_user_meta($providerid,'primary_category',true))).'">'.service_finder_getCategoryName(get_user_meta($providerid,'primary_category',true)).'</a></span>';
		
		return $catlabel;
	}else{
		return '';
	}
}

/* Upload imported image url to attchemnt for provider profilepic*/
function service_finder_upload_import_image($user_id = 0,$url = '')
{
	global $wpdb,$service_finder_Tables;
	require_once( ABSPATH . 'wp-admin/includes/image.php' );
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	
	$timeout_seconds = 5;
	
	$temp_file = download_url( $url, $timeout_seconds );
	
	if ( !is_wp_error( $temp_file ) ) {
	
		$file = array(
			'name'     => basename($url),
			'type'     => 'image/png',
			'tmp_name' => $temp_file,
			'error'    => 0,
			'size'     => filesize($temp_file),
		);
	
		$overrides = array(
			'test_form' => false,
			'test_size' => true,
		);

		$results = wp_handle_sideload( $file, $overrides );

		if ( ! is_wp_error( $results ) ) {
			
			$attachment = array(
				'guid'           => $results['url'],
				'post_mime_type' => $results['type'],
				'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $results['url'] ) ),
				'post_content'   => '',
				'post_status'    => 'inherit',
			);
			
			$id = wp_insert_attachment( $attachment, $results['file'] );
			
			if ( ! is_wp_error( $id ) )
			{
				wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $results['file'] ) );
				
				update_post_meta($id, '_wp_attachment_wp_user_avatar', $user_id);
				$data = array(
						'avatar_id' => $id,
						);
				
				$where = array(
						'wp_user_id' => $user_id,
						);
				$wpdb->update($service_finder_Tables->providers,wp_unslash($data),$where);
				delete_user_meta( $user_id, 'cropped_user_avatar' );
			}
		}
	}
}

/* Upload imported image url to attchemnt for category image*/
function service_finder_import_category_image($catid = 0,$url = '')
{
	global $wpdb,$service_finder_Tables;
	require_once( ABSPATH . 'wp-admin/includes/image.php' );
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	
	$timeout_seconds = 5;
	
	$temp_file = download_url( $url, $timeout_seconds );
	
	if ( !is_wp_error( $temp_file ) ) {
	
		$file = array(
			'name'     => basename($url),
			'type'     => 'image/png',
			'tmp_name' => $temp_file,
			'error'    => 0,
			'size'     => filesize($temp_file),
		);
	
		$overrides = array(
			'test_form' => false,
			'test_size' => true,
		);

		$results = wp_handle_sideload( $file, $overrides );

		if ( ! is_wp_error( $results ) ) {
			
			$attachment = array(
				'guid'           => $results['url'],
				'post_mime_type' => $results['type'],
				'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $results['url'] ) ),
				'post_content'   => '',
				'post_status'    => 'inherit',
			);
			
			$id = wp_insert_attachment( $attachment, $results['file'] );
			
			if ( ! is_wp_error( $id ) )
			{
				wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $results['file'] ) );
				
				update_option( "providers-category_image_".$catid, $results['url'] );
				update_term_meta( $catid, 'imageid', $id );
				
			}
		}
	}
}

/*Get icon for attachment file*/
function service_finder_get_icon_for_attachment($post_id = 0) {
	  $base = SERVICE_FINDER_BOOKING_IMAGE_URL . "/file_icons/";
	  $type = get_post_mime_type($post_id);
	  switch ($type) {
		case 'image/jpeg':
		case 'image/png':
		case 'image/jpg':
		case 'image/gif':
		  $src = wp_get_attachment_image_src( $post_id, 'thumbnail' ); 
		  $arr = array(
							'src' => $src[0],
							'filename' => 'attachmentid',
							);
		  return $arr;
		  break;
		case 'application/pdf':
		  $arr = array(
							'src' => $base . "pdf.png",
							'filename' => 'fileattachmentid',
							);
		  return $arr;
		  break;
		case 'application/msword':
		  $arr = array(
							'src' => $base . "doc.png",
							'filename' => 'fileattachmentid',
							);
		  return $arr;
		  break;
		case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
		  $arr = array(
							'src' => $base . "doc.png",
							'filename' => 'fileattachmentid',
							);
		  return $arr;
		  break;
		case 'application/vnd.ms-excel':
		  $arr = array(
							'src' => $base . "xls.png",
							'filename' => 'fileattachmentid',
							);
		  return $arr;
		  break; 
		case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
		  $arr = array(
							'src' => $base . "xls.png",
							'filename' => 'fileattachmentid',
							);
		  return $arr;
		  break; 
		case 'application/vnd.ms-powerpoint':
		  $arr = array(
							'src' => $base . "ppt.png",
							'filename' => 'fileattachmentid',
							);
		  return $arr;
		  break;
		case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
		  $arr = array(
							'src' => $base . "ppt.png",
							'filename' => 'fileattachmentid',
							);
		  return $arr;
		  break;        
		default:
			//return $type;
		  $arr = array(
							'src' => $base . "file.png",
							'filename' => 'fileattachmentid',
							);
		  return $arr;
		  break;
  }
}

/*Send bulk job invitations*/
add_action('wp_ajax_send_bulk_invitations', 'service_finder_send_bulk_invitations');
function service_finder_send_bulk_invitations(){
	global $wpdb,$service_finder_options,$service_finder_Tables;

	$data_ids = service_finder_get_data($_POST,'data_ids');
	$invitedjob = service_finder_get_data($_POST,'jobid');
	
	$data_id_array = explode(",", $data_ids); 
	if(!empty($data_id_array)) {
		foreach($data_id_array as $provider_id) {
			$job = get_post($invitedjob);
			
			$data = array(
				'created' => date('Y-m-d H:i:s'),
				'customer_id' => get_post_field( 'post_author', $invitedjob ),
				'provider_id' => $provider_id,
				'jobid' => $invitedjob,
			);
			
			$wpdb->insert($service_finder_Tables->job_invitations,wp_unslash($data));
			
			if($service_finder_options['invitejob-to-provider-subject'] != ""){
				$msg_subject = $service_finder_options['invitejob-to-provider-subject'];
			}else{
				$msg_subject = esc_html__('Job Invitation');
			}
			
			$provider = get_user_by('ID',$provider_id);
			
			if(!empty($service_finder_options['invitejob-to-provider'])){
				$message = $service_finder_options['invitejob-to-provider'];
			}else{
				$message = 'Congratulations, You have been invited for following job. Please go to job link and apply for the job.
			
				Job Title: %JOBTITLE%
				
				Job Link: %JOBLINK%';
			}
			
			$tokens = array('%JOBTITLE%','%JOBLINK%');
			$replacements = array($job->post_title,'<a href="'.esc_url(get_permalink($invitedjob)).'">'.get_permalink($invitedjob).'</a>');
			$msg_body = str_replace($tokens,$replacements,$message);
			
			if(class_exists('aonesms'))
			{
			if(service_finder_get_data($service_finder_options,'is-active-provider-job-invite-sms') == true)
			{
			$smsbody = service_finder_get_data($service_finder_options,'template-provider-job-invite-sms');
			if($smsbody != '')
			{
			$providerInfo = service_finder_get_provier_info($provider_id);
			
			$smsreplacements = array($job->post_title,'<a href="'.esc_url(get_permalink($invitedjob)).'">'.get_permalink($invitedjob).'</a>');
			
			$smsbody = str_replace($tokens,$smsreplacements,$smsbody);
			
			aonesms_send_sms_notifications($providerInfo->mobile,$smsbody);
			}
			}
			}
			
			if(function_exists('service_finder_add_notices')) {
		
				$noticedata = array(
						'provider_id' => $provider_id,
						'target_id' => $invitedjob, 
						'topic' => 'Job Invitation',
						'title' => esc_html__('Job Invitation', 'service-finder'),
						'notice' => sprintf( esc_html__('You have been invited for job. Job title is %s', 'service-finder'), get_the_title( $invitedjob ) ),
						);
				service_finder_add_notices($noticedata);
				
			}
			
			service_finder_wpmailer($provider->user_email,$msg_subject,$msg_body);
		}
	}
	
	$response = esc_html__('Invitations sent successfully.', 'service-finder');
	wp_send_json_success($response);
	exit;
}

/*Get stripe connect account id*/
function service_finder_get_stripe_connect_id($providerid = 0) {

	if(get_user_meta($providerid,'stripe_connect_custom_account_id',true) != '')
	{
		$acct_id = get_user_meta($providerid,'stripe_connect_custom_account_id',true);
	}elseif(get_user_meta($providerid,'stripe_connect_id',true) != '')
	{
		$acct_id = get_user_meta($providerid,'stripe_connect_id',true);
	}else{
		$acct_id = '';
	}
	
	return $acct_id;
}

/*Get google calendar credintials*/
function service_finder_get_gcal_cred(){
	global $service_finder_options;
	
	$client_id = (isset($service_finder_options['google-calendar-client-id'])) ? esc_attr($service_finder_options['google-calendar-client-id']) : '';
	$client_secret = (isset($service_finder_options['google-calendar-client-secret'])) ? esc_attr($service_finder_options['google-calendar-client-secret']) : '';
	
	return array(
			'client_id' => $client_id,
			'client_secret' => $client_secret		
		);
}

/*Check booking is free or paid*/
function service_finder_is_booking_free_paid($providerid = 0){
	global $service_finder_options;
	
	$paid_booking = service_finder_get_data($service_finder_options,'paid-booking');
	$free_booking = service_finder_get_data($service_finder_options,'free-booking');
	
	if($paid_booking && $free_booking){
		if($providerid > 0)
		{
			$settings = service_finder_getProviderSettings($providerid);
			return $settings['booking_option'];
		}else{
			return 'free';		
		}
		
	}elseif($paid_booking && !$free_booking){
	
		return 'paid';
	
	}elseif(!$paid_booking && $free_booking){
	
		return 'free';
	
	}else{
	
		return 'free';
	
	}
}

/*Get default lat long based on default city and country*/
function service_finder_get_default_latlong(){
		
	global $service_finder_options;
	
	$defaultcountry = (isset($service_finder_options['default-country'])) ? esc_html($service_finder_options['default-country']) : '';
	$defaultcity = (isset($service_finder_options['default-city'])) ? esc_html($service_finder_options['default-city']) : '';
	
	if($defaultcountry != '' || $defaultcity != ''){
	
	$defaultlat = get_option('defaultlat','28.6430536');
	$defaultlng = get_option('defaultlng','77.2223442');
	
	}else{
	$defaultlat = '28.6430536';
	$defaultlng = '77.2223442';
	}
	
	$defaults = array('defaultlat' => $defaultlat,'defaultlng' => $defaultlng);
	
	return $defaults;
}

function service_finder_show_map_on_site(){
	global $service_finder_options;
	
	$removemap = (isset($service_finder_options['remove-map-from-site'])) ? $service_finder_options['remove-map-from-site'] : false;
	
	if($removemap == false){
		return true;
	}else{
		return false;
	}
}	

function service_finder_show_autosuggestion_on_site(){
	global $service_finder_options;
	
	$removeautosuggestion = (isset($service_finder_options['remove-autosuggestion-from-site'])) ? $service_finder_options['remove-autosuggestion-from-site'] : false;
	
	if($removeautosuggestion == false){
		return true;
	}else{
		return false;
	}
}

/*Service start from*/
function service_finder_get_service_startfrom($providerid = 0){
global $wpdb, $service_finder_Tables, $service_finder_options;


	$row = $wpdb->get_row('SELECT * FROM '.$service_finder_Tables->services.' WHERE `status` = "active" AND `wp_user_id` = '.$providerid.' order by `cost` limit 0,1');

	if(!empty($row)){
		if($row->cost_type == 'hourly'){
			$perunit = esc_html__('/hour', 'service-finder');
		}elseif($row->cost_type == 'perperson')
		{
			$perunit = esc_html__('/person', 'service-finder');
		}elseif($row->cost_type == 'days')
		{
			$perunit = esc_html__('/day', 'service-finder');
		}else
		{
			$perunit = '';
		}
	
		return '<div class="sf-services-startfrom"><strong>'.esc_html__('Service start from', 'service-finder').':</strong> <strong>'.service_finder_money_format($row->cost).$perunit.'</strong></div>';
	}else{
		return '';	
	}
}

function service_finder_get_admin_fee($bookingid = 0,$for = ''){
		
	global $wpdb, $service_finder_Tables;
	
	$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `id` = %d',$bookingid));
	
	if($for == 'Customer'){
		if($row->charge_admin_fee_from == 'provider'){
			$totalamount = $row->total;
			$bookingamount = $row->total;
			$adminfee = '0.0';
		}elseif($row->charge_admin_fee_from == 'customer'){
			$totalamount = $row->total;
			$bookingamount = $row->total;
			$adminfee = $row->adminfee;
		}else{
			$totalamount = $row->total;
			$bookingamount = $row->total;
			$adminfee = $row->adminfee;
		}	
	}elseif($for == 'Provider'){
		if($row->charge_admin_fee_from == 'provider'){
			$totalamount = $row->total;
			$bookingamount = $row->total - $row->adminfee;
			$adminfee = $row->adminfee;
		}elseif($row->charge_admin_fee_from == 'customer'){
			$totalamount = $row->total;
			$bookingamount = $row->total;
			$adminfee = $row->adminfee;
		}else{
			$totalamount = $row->total;
			$bookingamount = $row->total;
			$adminfee = $row->adminfee;
		}
	}else{
		if($row->charge_admin_fee_from == 'provider'){
			$totalamount = $row->total;
			$bookingamount = $row->total - $row->adminfee;
			$adminfee = $row->adminfee;
		}elseif($row->charge_admin_fee_from == 'customer'){
			$totalamount = $row->total;
			$bookingamount = $row->total;
			$adminfee = $row->adminfee;
		}else{
			$totalamount = $row->total;
			$bookingamount = $row->total;
			$adminfee = $row->adminfee;
		}
	}
	
	return $adminfee;
}	

function service_finder_get_slug_by_cityname($cityname = ''){
	global $wpdb, $service_finder_Tables;
	
	if($cityname != ''){
		$cityinfo = get_term_by('name', $cityname, 'sf-cities');
		
		if(!empty($cityinfo)){
			return $cityinfo->slug;
		}else{
			return $cityname;
		}
	}else{
		return $cityname;
	}
}	

function service_finder_get_cityname_by_slug($cityslug = ''){
	global $wpdb, $service_finder_Tables;
	
	if($cityslug != ''){
		$cityinfo = get_term_by('slug', $cityslug, 'sf-cities');
		
		if(!empty($cityinfo)){
			return $cityinfo->name;
		}else{
			return $cityslug;
		}
	}else{
		return $cityslug;
	}
}	

//Provider default tagline
function service_finder_default_tagline(){
global $service_finder_options;

$defaulttagline = (!empty($service_finder_options['provider-default-tagline'])) ? $service_finder_options['provider-default-tagline'] : '';
return $defaulttagline;
}