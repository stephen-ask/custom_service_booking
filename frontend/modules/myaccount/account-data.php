<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/

/* Submit Provider Form via Ajax*/
add_action('wp_ajax_update_user', 'service_finder_user_update');
add_action('wp_ajax_nopriv_update_user', 'service_finder_user_update');

function service_finder_user_update(){
global $wpdb, $service_finder_Errors;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/myaccount/MyAccount.php';
$updateProfile = new SERVICE_FINDER_MyAccount();
$updateProfile->service_finder_updateUserProfile($_POST);
exit;
}

/* Submit Customer Form via Ajax*/
add_action('wp_ajax_update_customer', 'service_finder_customer_update');
add_action('wp_ajax_nopriv_update_customer', 'service_finder_customer_update');

function service_finder_customer_update(){
global $wpdb, $service_finder_Errors;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/myaccount/MyAccount.php';
$updateProfile = new SERVICE_FINDER_MyAccount();
$updateProfile->service_finder_updateCustomerProfile($_POST);
exit;
}

/*Identity check */
add_action('wp_ajax_upload_identity', 'service_finder_upload_identity');
function service_finder_upload_identity(){
global $wpdb, $service_finder_Errors;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/myaccount/MyAccount.php';
$uploadIdentity = new SERVICE_FINDER_MyAccount();
$uploadIdentity->service_finder_uploadIdentity($_POST);
exit;
}

/*Get my current location*/
add_action('wp_ajax_get_mycurrent_location', 'service_finder_get_mycurrent_location');
function service_finder_get_mycurrent_location(){
global $wpdb, $service_finder_Errors;
		
		$providerid = (!empty($_POST['providerid'])) ? sanitize_text_field($_POST['providerid']) : '';
		$address = (!empty($_POST['address'])) ? sanitize_text_field($_POST['address']) : '';
		
		$lat = get_user_meta($providerid,'providerlat',true);
		$lng = get_user_meta($providerid,'providerlng',true);
		
		$my_location = get_user_meta($providerid,'my_location',true);
		
		if(($lat == '' && $lng == '') || $my_location != $address){
		$address = str_replace(" ","+",$address);
		$res = service_finder_getLatLong($address);
		$lat = $res['lat'];
		$lng = $res['lng'];
		}
		
		$success = array(
				'status' => 'success',
				'lat' => esc_html($lat),
				'lng' => esc_html($lng)
				);
		echo json_encode($success);
exit;
}

/* Submit Provider Form via Ajax*/
add_action('wp_ajax_claimbusiness', 'service_finder_claimbusiness');
add_action('wp_ajax_nopriv_claimbusiness', 'service_finder_claimbusiness');
function service_finder_claimbusiness(){
global $wpdb;
		$providerid = (!empty($_POST['providerid'])) ? sanitize_text_field($_POST['providerid']) : '';
		$status = (!empty($_POST['status'])) ? sanitize_text_field($_POST['status']) : '';
		
		update_user_meta($providerid,'claimbusiness',$status);
		
		if($status == 'enable'){
			$succmsg = esc_html__('Claim business successfully enabled for this profile.', 'service-finder');
		}else{
			$succmsg = esc_html__('Claim business successfully disabled for this profile.', 'service-finder');
		}
		
		$success = array(
				'status' => 'success',
				'suc_message' => $succmsg
				);
		echo json_encode($success);
		exit;
}

/* Submit Provider Form via Ajax*/
add_action('wp_ajax_update_gcal_info', 'service_finder_update_gcal_info');
add_action('wp_ajax_nopriv_update_gcal_info', 'service_finder_update_gcal_info');
function service_finder_update_gcal_info(){
global $wpdb, $service_finder_Errors;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/myaccount/MyAccount.php';
$updateGcalinfo = new SERVICE_FINDER_MyAccount();
$updateGcalinfo->service_finder_updateGcalInfo($_POST);
exit;
}

/* Submit Provider Form via Ajax*/
add_action('wp_ajax_identify_video_type', 'service_finder_identify_video_type');
function service_finder_identify_video_type(){
global $wpdb, $service_finder_Errors;
		
		$embeded_code = (!empty($_POST['embeded_code'])) ? sanitize_text_field($_POST['embeded_code']) : '';
		
		$fbfind   = '//www.facebook.com';
		$fbpos = strpos($embeded_code, $fbfind);
		
		if ($fbpos !== false) {
			/*if (preg_match("~(?:t\.\d+/)?(\d+)~i", $embeded_code, $matches)) {
		   		$videoid = $matches[1];
				$videotype = 'facebook';
				$xml = file_get_contents('http://graph.facebook.com/' . $videoid); 
			    $result = json_decode($xml); 
				$thumb = $result->format[1]->picture; 
		    }*/
			if (preg_match("~(?:t\.\d+/)?(\d+)~i", $embeded_code, $matches)) {
		   		$videoid = $matches[1];
				$videotype = 'facebook';
				$thumb = $videoid; 
		    }
		
		}
		
		$ytfind   = 'youtu';
		$ytpos = strpos($embeded_code, $ytfind);
		
		if ($ytpos !== false) {
			$youtubeinfo = service_finder_get_youtube_info($embeded_code);
			if(!empty($youtubeinfo)){
				$videoid = $matches[1];
				$videotype = 'youtube';
				$thumb = $youtubeinfo['thumbnail_url'];
			}
		
		}
		
		$vmfind   = 'vimeo.com';
		$vmpos = strpos($embeded_code, $vmfind);
		
		if ($vmpos !== false) {
			 if (preg_match("/(?:.*)\/([0-9]*)/i", $embeded_code, $matches)) {
		   		$videoid = $matches[1];
				$videotype = 'vimeo';
				if($videoid != ""){
				$hash = unserialize(file_get_contents("https://vimeo.com/api/v2/video/".$videoid.".php"));
				$thumb = $hash[0]['thumbnail_medium'];
				}
		    }
		
		}
		
		$success = array(
				'videoid' => $videoid,
				'videotype' => $videotype,
				'thumburl' => $thumb,
				);
		echo json_encode($success);
exit;
}

/*Reavtivate membership*/
add_action('wp_ajax_reactivate_membership', 'service_finder_reactivate_membership');
add_action('wp_ajax_nopriv_reactivate_membership', 'service_finder_reactivate_membership');

function service_finder_reactivate_membership(){
global $wpdb, $service_finder_Tables, $current_user, $service_finder_options;

$providerid = (isset($_POST['providerid'])) ? esc_attr($_POST['providerid']) : '';

$data = array(
		'account_blocked' => 'no',
		'status' => 'active',
		);

$where = array(
		'wp_user_id' => $providerid,
		);
		
$wpdb->update($service_finder_Tables->providers,wp_unslash($data),$where);

$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('Providers', 'service-finder');

$success = array(
		'status' => 'success',
		'suc_message' => sprintf(esc_html__('%s membership has been reactivated successfully.', 'service-finder'),$providerreplacestring),
		);
echo json_encode($success);

exit;
}

/* Delete user avatar */
add_action('wp_ajax_delete_user_avatar', 'service_finder_delete_user_avatar');
add_action('wp_ajax_nopriv_delete_user_avatar', 'service_finder_delete_user_avatar');
function service_finder_delete_user_avatar()
{
	global $wpdb,$service_finder_Tables;
	
	$userid = isset( $_POST['userid'] ) ? sanitize_text_field($_POST['userid']) : '';
	
	$attachmentid = get_user_meta($userid,'cropped_user_avatar', true);
	
	$ok = wp_delete_attachment( $attachmentid );
	if ( $ok )
	{
	
		delete_post_meta($attachmentid,'_wp_attachment_wp_user_avatar');
		delete_user_meta( $userid, 'cropped_user_avatar' );
		
		$data = array(
			'avatar_id' => '',
			);
	
		$where = array(
				'wp_user_id' => $userid,
				);
		$wpdb->update($service_finder_Tables->providers,wp_unslash($data),$where);
		
		$response = array(
						'message'   => esc_html__('User avatar deleted successfully.','makeover'),
						'defaultavatar'   => service_finder_get_avatar_by_userid($userid),
					);
		wp_send_json_success($response);
	}else
	{
		$response = array(
			'message'   => esc_html__('User avatar not deleted.','makeover')
		);
		wp_send_json_error($response);
	}
	
	exit;
}

/* Delete cover avatar */
add_action('wp_ajax_delete_cover_image', 'service_finder_delete_cover_image');
add_action('wp_ajax_nopriv_delete_cover_image', 'service_finder_delete_cover_image');
function service_finder_delete_cover_image()
{
	global $wpdb,$service_finder_Tables;
	
	$userid = isset( $_POST['userid'] ) ? sanitize_text_field($_POST['userid']) : '';
	
	$attachmentid = get_user_meta($userid,'cropped_cover_image', true);
	
	$ok = wp_delete_attachment( $attachmentid );
	if ( $ok )
	{
	
		delete_post_meta($attachmentid,'_wp_attachment_wp_user_cover_image');
		delete_user_meta( $userid, 'cropped_cover_image' );
		
		$wpdb->query($wpdb->prepare('DELETE FROM '.$service_finder_Tables->attachments.' WHERE wp_user_id = %d AND type = "cover-image"',$userid));
		
		$response = array(
						'message'   => esc_html__('User cover image deleted successfully.','makeover'),
						'defaultcoverthumb'   => service_finder_get_user_coverimage($userid,'full'),
					);
		wp_send_json_success($response);
	}else
	{
		$response = array(
			'message'   => esc_html__('User avatar not deleted.','makeover')
		);
		wp_send_json_error($response);
	}
	
	exit;
}
