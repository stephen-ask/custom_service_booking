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

class SERVICE_FINDER_MyAccount{

	/*Update Provider Profile*/
	public function service_finder_updateUserProfile($arg){
			global $wpdb, $service_finder_Tables, $current_user, $service_finder_options;
			
			$user_email = (!empty($arg['user_email'])) ? $arg['user_email'] : '';
			
			//Email already exist
			if($user_email != ""){
			if($this->service_finder_custom_email_exists( esc_attr($arg['user_email']), $arg['user_id'] )){
				$error = array(
						'status' => 'error',
						'err_message' => esc_html__('Email already exist', 'service-finder'),
						);
				wp_send_json_error($error);
				exit;
			}
			}
		
			
			
			
			if($arg['password'] != ""){
			$userdata = array(
						'ID' => $arg['user_id'],
						'user_email' => $arg['user_email'],
						'user_pass' => $arg['password'],
						'first_name' => $arg['first_name'],
						'last_name' => $arg['last_name'],
						);
			}else{
			$userdata = array(
						'ID' => $arg['user_id'],
						'user_email' => $arg['user_email'],
						'first_name' => $arg['first_name'],
						'last_name' => $arg['last_name'],
						);
			}			
			

			$userId = wp_update_user( $userdata );
			
			if ( ! empty($userId->errors) ) {
				$errmsg = 'Couldn&#8217;t update you... please contact the <a href="mailto:'.esc_url($adminemail).'">Administrator</a> !';
				$allowedhtml = array(
					'a' => array(
						'href' => array(),
						'title' => array()
					),
				);
				$error = array(
						'status' => 'error',
						'err_message' => sprintf( wp_kses(esc_html__('Couldn&#8217;t update you... please contact the <a href="mailto:%s">Administrator</a> !', 'service-finder'),$allowedhtml), $adminemail )
						);
				wp_send_json_error($error);
				exit;
			}else{
			
				update_user_meta($userId, 'first_name', $arg['first_name']);
				update_user_meta($userId, 'last_name', $arg['last_name']);
				$fname = (!empty($arg['first_name'])) ? $arg['first_name'] : '';
				$lname = (!empty($arg['last_name'])) ? $arg['last_name'] : '';
				
				if($arg['fillprofileotp'] != ''){
					update_user_meta($userId, 'verify_mobile_number', 'yes');
				}

				if(!empty($arg['category'])){
					$selectedcategory = implode(',',$arg['category']);
				}else{
					$selectedcategory = $arg['category'];
				}
				
				if(!empty($arg['amenities'])){
					$selectedamenities = implode(',',$arg['amenities']);
				}else{
					$selectedamenities = $arg['amenities'];
				}
				
				if(!empty($arg['languages'])){
					$selectedlanguages = implode(',',$arg['languages']);
				}else{
					$selectedlanguages = $arg['languages'];
				}
				
				$address = (!empty($arg['address'])) ? $arg['address'] : '';
				$city = (!empty($arg['city'])) ? $arg['city'] : '';
				$state = (!empty($arg['state'])) ? $arg['state'] : '';
				$country = (!empty($arg['country'])) ? $arg['country'] : '';
				
				$tokens = array($city,$state,$country);
				$replacements = array('','','');
				$address = str_replace($tokens,$replacements,$address);
				$tokens2 = array(', ,',', , ,');
				$replacements2 = array(',',',');
				$address = str_replace($tokens2,$replacements2,$address);
				$address = rtrim($address,', ,');
				$address = rtrim($address,',');
				
				$videocount = (isset($arg['videocount'])) ? $arg['videocount'] : '';
				if($videocount == 1){
				if(empty($arg['videosarr'])){

					$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$userId));

					$embeded_code = $row->embeded_code;

				}else{
					$videosarr = explode(',',$arg['videosarr']);
					$embeded_code = serialize($videosarr);

				}
				}else{
					$embeded_code = '';
				}
				
				/*Update Provider Table*/
				$data = array(
						'company_name' => (!empty($arg['company_name'])) ? $arg['company_name'] : '',
						'full_name' => $fname.' '.$lname,
						'phone' => (!empty($arg['phone'])) ? $arg['phone'] : '',
						'gender' => (!empty($arg['gender'])) ? $arg['gender'] : '',
						'email' => (!empty($arg['user_email'])) ? $arg['user_email'] : '',
						'category_id' => $selectedcategory,
						'amenities' => $selectedamenities,
						'languages' => $selectedlanguages,
						'tagline' => (!empty($arg['tagline'])) ? $arg['tagline'] : '',
						'bio' => (!empty($arg['bio'])) ? $arg['bio'] : '',
						'embeded_code' => $embeded_code,
						'mobile' => (!empty($arg['mobile'])) ? $arg['mobile'] : '',
						'fax' => (!empty($arg['fax'])) ? $arg['fax'] : '',
						'address' => $address,
						'apt' => (!empty($arg['apt'])) ? $arg['apt'] : '',
						'city' => (!empty($arg['city'])) ? $arg['city'] : '',
						'state' => (!empty($arg['state'])) ? $arg['state'] : '',
						'zipcode' => (!empty($arg['zipcode'])) ? $arg['zipcode'] : '',
						'country' => (!empty($arg['country'])) ? $arg['country'] : '',
						'lat' => (!empty($arg['lat'])) ? $arg['lat'] : '',
						'long' => (!empty($arg['long'])) ? $arg['long'] : '',
						'facebook' => (!empty($arg['facebook'])) ? $arg['facebook'] : '',
						'twitter' => (!empty($arg['twitter'])) ? $arg['twitter'] : '',
						'linkedin' => (!empty($arg['linkedin'])) ? $arg['linkedin'] : '',
						'pinterest' => (!empty($arg['pinterest'])) ? $arg['pinterest'] : '',
						'google_plus' => (!empty($arg['google_plus'])) ? $arg['google_plus'] : '',
						'digg' => (!empty($arg['digg'])) ? $arg['digg'] : '',
						'instagram' => (!empty($arg['instagram'])) ? $arg['instagram'] : '',
						'skypeid' => (!empty($arg['skypeid'])) ? $arg['skypeid'] : '',
						'website' => (!empty($arg['website'])) ? $arg['website'] : '',
						'radius' => (!empty($arg['serviceradius'])) ? esc_html($arg['serviceradius']) : 0,
						'service_perform_at' => (!empty($arg['service_perform'])) ? esc_html($arg['service_perform']) : ''
						);
				
				$where = array(
						'wp_user_id' => $userId,
						);
				$proid = $wpdb->update($service_finder_Tables->providers,wp_unslash($data),$where);
				
				if(service_finder_get_data($service_finder_options,'profileurlby') == 'companyname' && !empty($arg['company_name'])){
					$nicename = sanitize_text_field($arg['company_name']);
				}elseif(service_finder_get_data($service_finder_options,'profileurlby') == 'username' && !empty($arg['username'])){
					$nicename = sanitize_text_field($arg['username']);
				}else{
					$nicename = $fname.' '.$lname;
				}
				
				if($nicename != "" && $nicename != " "){
				
				wp_update_user( array( 'ID' => $userId, 'user_nicename' => service_finder_create_user_name($nicename) ) );
				
				$comment_postid = get_user_meta($userId, 'comment_post', true);
				
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
				
				if(service_finder_get_data($arg,'city') != '' && service_finder_get_data($arg,'country') != '')
				{
					service_finder_create_city_term(service_finder_get_data($arg,'city'),service_finder_get_data($arg,'country'));
				}
				
				if(service_finder_user_has_capability('crop',$userId))
				{
					$this->update_user_avatar($_FILES,$_POST,$userId);
				}else{
					$data = array(
							'avatar_id' => (!empty($arg['plavatar'])) ? $arg['plavatar'] : ''
							);
					
					$where = array(
							'wp_user_id' => $userId,
							);
					$wpdb->update($service_finder_Tables->providers,wp_unslash($data),$where);
				}
				
				$bio = (!empty($arg['bio'])) ? $arg['bio'] : '';
				update_user_meta($userId,'description',$bio);
				
				$userCap = service_finder_get_capability($userId);
				
				
				if(!empty($userCap)){
					if(in_array('multiple-categories',$userCap)){
						$primarycategory = (!empty($arg['primary_category'])) ? $arg['primary_category'] : '';
					}else{
						$primarycategory = (!empty($arg['category'][0])) ? $arg['category'][0] : '';
					}
				}else{
						$primarycategory = (!empty($arg['category'][0])) ? $arg['category'][0] : '';
				}	
				
				$serviceradius = (!empty($arg['serviceradius'])) ? esc_html($arg['serviceradius']) : 0;
				
				update_user_meta($userId,'primary_category',$primarycategory);
				update_user_meta($userId,'serviceradius',$serviceradius);
				update_user_meta($userId,'gender',service_finder_get_data($arg,'gender'));
				
				if(!empty($arg['attachmentid'])){
					foreach($arg['attachmentid'] as $attachmentid){
						if(!$this->service_finder_check_attachment($attachmentid)){
							$data = array(
										'wp_user_id' => $userId,
										'attachmentid' => $attachmentid,
										'type' => 'gallery'
										);
					
							$wpdb->insert($service_finder_Tables->attachments,wp_unslash($data));	
						}
					}
				}
				
				if(!empty($arg['fileattachmentid'])){
					foreach($arg['fileattachmentid'] as $fileattachmentid){
						if(!$this->service_finder_check_attachment($fileattachmentid)){
							$data = array(
										'wp_user_id' => $userId,
										'attachmentid' => $fileattachmentid,
										'type' => 'file'
										);
					
							$wpdb->insert($service_finder_Tables->attachments,wp_unslash($data));	
						}
					}
				}
				
				if(service_finder_user_has_capability('crop',$userId))
				{
					$this->update_cover_image($_FILES,$_POST,$userId);
				}else
				{
					if(!empty($arg['coverimageattachmentid'])){
						foreach($arg['coverimageattachmentid'] as $coverimageattachmentid){
							if(!$this->service_finder_check_attachment($coverimageattachmentid)){
								$data = array(
											'wp_user_id' => $userId,
											'attachmentid' => $coverimageattachmentid,
											'type' => 'cover-image'
											);
						
								$wpdb->insert($service_finder_Tables->attachments,wp_unslash($data));	
							}
						}
					}
				}
				
				/*Update Member Table*/
				$memberData = array(
						'member_name' => $arg['first_name'].' '.$arg['last_name'],
						'email' => $arg['user_email'],
						'phone' => $arg['phone'],
						);
				
				$where = array(
						'admin_wp_id' => $userId,
						'is_admin' => 'yes',
						);		
		
				$wpdb->update($service_finder_Tables->team_members,wp_unslash($memberData),$where);
				
				/*Update Provider Settings*/
				$google_calendar = (!empty($arg['google_calendar'])) ? $arg['google_calendar'] : '';
				$zoomlevel = (!empty($arg['zoomlevel'])) ? $arg['zoomlevel'] : '';
				$locationzoomlevel = (!empty($arg['locationzoomlevel'])) ? $arg['locationzoomlevel'] : '';
				
				$service_perform = (!empty($arg['service_perform'])) ? $arg['service_perform'] : '';
				$my_location = (!empty($arg['my_location'])) ? $arg['my_location'] : '';
				$providerlat = (!empty($arg['providerlat'])) ? $arg['providerlat'] : '';
				$providerlng = (!empty($arg['providerlng'])) ? $arg['providerlng'] : '';
				
				$google_calendar_id = (!empty($arg['google_calendar_id'])) ? $arg['google_calendar_id'] : '';
				
				update_user_meta($userId,'google_calendar_id',$google_calendar_id);
				
				update_user_meta($userId,'zoomlevel',$zoomlevel);
				update_user_meta($userId,'locationzoomlevel',$locationzoomlevel);
				
				update_user_meta($userId,'service_perform',$service_perform);
				
				if($service_perform == 'provider_location' || $service_perform == 'both'){
				
				if($providerlat == '' && $providerlng == '' && service_finder_get_data($service_finder_options,'show-contact-map' && service_finder_show_map_on_site())){
				if($my_location != get_user_meta($userId,'my_location',true)){
				$address = str_replace(" ","+",$my_location);
				$res = service_finder_getLatLong($address);
				$providerlat = $res['lat'];
				$providerlng = $res['lng'];
				}
				}
				
				update_user_meta($userId,'my_location',$my_location);
				update_user_meta($userId,'providerlat',$providerlat);
				update_user_meta($userId,'providerlng',$providerlng);
				}
				
				$options = unserialize(get_option( 'provider_settings'));

				$options[$userId]['google_calendar'] = $google_calendar;
				
				update_option( 'provider_settings', serialize($options) );
				
				/*if($google_calendar == 'on' && $google_client_id != "" && $google_client_secret != ""){
					service_finder_connect_to_google_calendar($google_client_id,$google_client_secret);
				}*/
				$profilethumb = service_finder_get_avatar_by_userid($userId);
				$coverthumb = service_finder_get_user_coverimage($userId,'full');
				$success = array(
						'status' => 'success',
						'profilethumb' => $profilethumb,
						'coverthumb' => $coverthumb,
						'suc_message' => esc_html__('Your profile updated successfully.', 'service-finder'),
						'userid' => $userId,
						'primarycatid' => $primarycategory,
						);
				wp_send_json_success($success);
				//echo json_encode($success);
			}
		
				}
				
	public function update_user_avatar($files = array(),$attr = array(),$user_id){
		global $wpdb,$service_finder_Tables;
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		if(isset( $files['profilepic'] ) && !empty($files['profilepic']))
		{
		if($files['profilepic']['size'] > 0)
		{
		$file = isset( $files['profilepic'] ) ? $files['profilepic'] : '';
	
		$file_attr = wp_handle_upload( $file, array( 'test_form' => false ) );
		
		$attachment = array(
			'guid'           => $file_attr['url'],
			'post_mime_type' => $file_attr['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file['name'] ) ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);
		
		$id = wp_insert_attachment( $attachment, $file_attr['file'] );
		
		if ( ! is_wp_error( $id ) )
		{
			wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file_attr['file'] ) );
			
			update_post_meta($id, '_wp_attachment_wp_user_avatar', $user_id);
			$data = array(
					'avatar_id' => $id,
					);
			
			$where = array(
					'wp_user_id' => $user_id,
					);
			$proid = $wpdb->update($service_finder_Tables->providers,wp_unslash($data),$where);
			if($attr['croppedimage'] != '')
			{
				$croppedimage = service_finder_cropped_data_to_image($attr['croppedimage']);
				update_user_meta( $user_id, 'cropped_user_avatar', $croppedimage );
				
				update_post_meta($croppedimage, '_wp_attachment_wp_user_avatar', $user_id);
				$data = array(
						'avatar_id' => $croppedimage,
						);
				
				$where = array(
						'wp_user_id' => $user_id,
						);
				$proid = $wpdb->update($service_finder_Tables->providers,wp_unslash($data),$where);
			}else
			{
				delete_user_meta( $user_id, 'cropped_user_avatar' );
			}
		}	
		}else
		{
			if($attr['croppedimage'] != '')
			{
				$croppedimage = service_finder_cropped_data_to_image($attr['croppedimage']);
				update_user_meta( $user_id, 'cropped_user_avatar', $croppedimage );
				
				update_post_meta($croppedimage, '_wp_attachment_wp_user_avatar', $user_id);
				$data = array(
						'avatar_id' => $croppedimage,
						);
				
				$where = array(
						'wp_user_id' => $user_id,
						);
				$proid = $wpdb->update($service_finder_Tables->providers,wp_unslash($data),$where);
			}
		}
		}
	}
	
	public function update_cover_image($files = array(),$attr = array(),$user_id){
		global $wpdb,$service_finder_Tables;
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		if(isset( $files['coverpic'] ) && !empty($files['coverpic']))
		{
		if($files['coverpic']['size'] > 0)
		{
		$file = isset( $files['coverpic'] ) ? $files['coverpic'] : '';
	
		$file_attr = wp_handle_upload( $file, array( 'test_form' => false ) );
		
		$attachment = array(
			'guid'           => $file_attr['url'],
			'post_mime_type' => $file_attr['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file['name'] ) ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);
		
		$id = wp_insert_attachment( $attachment, $file_attr['file'] );
		
		if ( ! is_wp_error( $id ) )
		{
			wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file_attr['file'] ) );
			
			update_post_meta($id, '_wp_attachment_wp_user_cover_image', $user_id);
			if(service_finder_cover_image_exists($user_id,'cover-image'))
			{
			$data = array(
					'attachmentid' => $id,
					);
			
			$where = array(
					'wp_user_id' => $user_id,
					'type' => 'cover-image'
					);
			$wpdb->update($service_finder_Tables->attachments,wp_unslash($data),$where);
			}else
			{
			$data = array(
						'wp_user_id' => $user_id,
						'attachmentid' => $id,
						'type' => 'cover-image'
						);
	
			$wpdb->insert($service_finder_Tables->attachments,wp_unslash($data));
			}
			
			if($attr['croppedcoverimage'] != '')
			{
				$croppedimage = service_finder_cropped_data_to_image($attr['croppedcoverimage']);
				update_user_meta( $user_id, 'cropped_cover_image', $croppedimage );
			}else
			{
				delete_user_meta( $user_id, 'cropped_cover_image' );
			}
		}	
		}else
		{
			if($attr['croppedcoverimage'] != '')
			{
				$croppedimage = service_finder_cropped_data_to_image($attr['croppedcoverimage']);
				update_user_meta( $user_id, 'cropped_cover_image', $croppedimage );
			}
		}
		}
	}
	
	/*Update Gcal Info*/
	public function service_finder_updateGcalInfo($arg){
			global $wpdb, $service_finder_Tables, $current_user;				
			
			require_once SERVICE_FINDER_BOOKING_LIB_DIR.'/google-api-php-client/src/Google/autoload.php';
			
			$google_client_id = (!empty($arg['google_client_id'])) ? $arg['google_client_id'] : '';
			$google_client_secret = (!empty($arg['google_client_secret'])) ? $arg['google_client_secret'] : '';
			$providerid = (!empty($arg['providerid'])) ? $arg['providerid'] : '';
			
			update_user_meta($providerid,'google_client_id',$google_client_id);
			update_user_meta($providerid,'google_client_secret',$google_client_secret);
			update_user_meta($providerid,'google_calendar_id',$google_calendar_id);
			
			$client_id = get_user_meta($providerid,'google_client_id',true);
			$client_secret = get_user_meta($providerid,'google_client_secret',true);
			$redirect_uri = add_query_arg( array('action' => 'googleoauth-callback'), home_url() );
			
			try{	
    		$client = new Google_Client();
			$client->setClientId($client_id);
			$client->setClientSecret($client_secret);
			$client->setRedirectUri($redirect_uri);
			$client->setAccessType("offline");
			$client->setApprovalPrompt('force');
			$client->setScopes('https://www.googleapis.com/auth/calendar');	
			
			$authUrl = $client->createAuthUrl();
		    $connectlink = '<a href="'.esc_url($authUrl).'" class="btn btn-primary margin-r-10">'.esc_html__('Connect to Google Calendar', 'service-finder').'</a>';
			
			unset($_SESSION['access_token']);
			delete_user_meta($providerid,'gcal_access_token');
			delete_user_meta($providerid,'google_calendar_id');
			
			$success = array(
						'status' => 'success',
						'suc_message' => esc_html__('Google calendar disconnected successfully.', 'service-finder'),
						'connectlink' => $connectlink,
						);
			echo json_encode($success);
			} catch (Exception $e) {
				$error = array(
						'status' => 'error',
						'err_message' => $e->getMessage()
						);
				echo json_encode($error);
			}
	}		
				
	/*Upload identity*/
	public function service_finder_uploadIdentity($arg){
			global $wpdb, $service_finder_Tables, $current_user, $service_finder_options;
			
			if(!empty($arg['identityattachmentid'])){
					foreach($arg['identityattachmentid'] as $identityattachmentid){
						if(!$this->service_finder_check_attachment($identityattachmentid)){
							$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';
							$data = array(
										'wp_user_id' => $user_id,
										'attachmentid' => $identityattachmentid,
										'type' => 'identity'
										);
					
							$wpdb->insert($service_finder_Tables->attachments,wp_unslash($data));	
							
							$wpdb->query($wpdb->prepare('UPDATE '.$service_finder_Tables->providers.' SET `identity` = "" WHERE `wp_user_id` = %d',$user_id));
							
							delete_user_meta($user_id,'identity');
							
							delete_user_meta($user_id,'identity_decline_reason');
						}
					}
					
					$noticedata = array(
							'admin_id' => 1,
							'target_id' => $user_id, 
							'topic' => 'Identity Upload',
							'title' => esc_html__('Identity Upload', 'service-finder'),
							'notice' => sprintf(esc_html__('%s has uploaded their identity. %s name is: %s', 'service-finder'),service_finder_provider_replace_string(),service_finder_provider_replace_string(),service_finder_getProviderFullName($user_id)),
							);
					service_finder_add_notices($noticedata);
					
					$messagetmp = service_finder_get_data($service_finder_options,'identity-upload-mail');
					if($messagetmp != ""){
					$message = $messagetmp;
					}else{
					$message = 'Hello Admin,
					Your provider has upload their identity. Provider name is: %PROVIDERNAME%';
					}
					
					$tokens = array('%PROVIDERNAME%');
					$replacements = array(service_finder_get_providername_with_link($user_id));
					$msg_body = str_replace($tokens,$replacements,$message);
					
					if(!empty(service_finder_get_data($service_finder_options,'identity-upload-mail-subject'))){
						$msg_subject = service_finder_get_data($service_finder_options,'identity-upload-mail-subject');
					}else{
						$msg_subject = 'Identity Upload Mail';
					}
					
					service_finder_wpmailer(get_option( 'admin_email' ),$msg_subject,$msg_body);
				
					$msg = (!empty($service_finder_options['upload-identity'])) ? $service_finder_options['upload-identity'] : esc_html__('Your identity uploaded successfully. You can proceed once its approved by admin.', 'service-finder');
					$success = array(
							'status' => 'success',
							'suc_message' => $msg,
							'alert_message' => esc_html__('Awaiting Identity verification', 'service-finder'),
							'userid' => $userId,
							);
					echo json_encode($success);
				
				}else{
					$error = array(
							'status' => 'error',
							'err_message' => esc_html__('You have not uploaded any identity document. Please Upload.', 'service-finder'),
							);
					echo json_encode($error);
				}
				
	}					
				
	/*Update Customer Profile*/
	public function service_finder_updateCustomerProfile($arg){
			global $wpdb, $service_finder_Tables;
			
			$user_email = (!empty($arg['user_email'])) ? $arg['user_email'] : '';
			$password = (!empty($arg['password'])) ? $arg['password'] : '';
			$first_name = (!empty($arg['first_name'])) ? $arg['first_name'] : '';
			$last_name = (!empty($arg['last_name'])) ? $arg['last_name'] : '';
			$phone = (!empty($arg['phone'])) ? $arg['phone'] : '';
			$phone2 = (!empty($arg['phone2'])) ? $arg['phone2'] : '';
			$address = (!empty($arg['address'])) ? $arg['address'] : '';
			$apt = (!empty($arg['apt'])) ? $arg['apt'] : '';
			$city = (!empty($arg['city'])) ? $arg['city'] : '';
			$state = (!empty($arg['state'])) ? $arg['state'] : '';
			$zipcode = (!empty($arg['zipcode'])) ? $arg['zipcode'] : '';
			$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';
			$country = (!empty($arg['country'])) ? $arg['country'] : '';
			$plavatar = (!empty($arg['plavatar'])) ? $arg['plavatar'] : '';
			
			//Email already exist
			if($this->service_finder_custom_email_exists( $user_email, $user_id )){
				$error = array(
						'status' => 'error',
						'err_message' => esc_html__('Email already exist', 'service-finder'),
						);
				echo json_encode($error);
				exit;
			}
		
			if($password != ""){
			$userdata = array(
						'ID' => $user_id,
						'user_email' => $user_email,
						'user_pass' => $password,
						'first_name' => $first_name,
						'last_name' => $last_name,
						);
			}else{
			$userdata = array(
						'ID' => $user_id,
						'user_email' => $user_email,
						'first_name' => $first_name,
						'last_name' => $last_name,
						);
			}			
			

			$userId = wp_update_user( $userdata );
			
			if ( ! empty($userId->errors) ) {
				$adminemail = get_option( 'admin_email' );
				$allowedhtml = array(
					'a' => array(
						'href' => array(),
						'title' => array()
					),
				);
				$error = array(
						'status' => 'error',
						'err_message' => sprintf( wp_kses(esc_html__('Couldn&#8217;t update you... please contact the <a href="mailto:%s">Administrator</a> !', 'service-finder'),$allowedhtml), $adminemail )
						);
				echo json_encode($error);
			}else{
			
				update_user_meta($userId, 'first_name', $first_name);
				update_user_meta($userId, 'last_name', $last_name);
				
				/*Update Customer Data Table*/
				$data = array(
						'phone' => $phone,
						'phone2' => $phone2,
						'address' => $address,
						'apt' => $apt,
						'city' => $city,
						'state' => $state,
						'zipcode' => $zipcode,
						'country' => $country,
						'avatar_id' => $plavatar
						);
				
				$where = array(
						'wp_user_id' => $userId,
						);
				$wpdb->update($service_finder_Tables->customers_data,wp_unslash($data),$where);		
				
				$success = array(
						'status' => 'success',
						'suc_message' => esc_html__('Your profile updated successfully.', 'service-finder'),
						'userid' => $userId,
						);
				echo json_encode($success);
			}
		
				}			
				
	/*Check email is exist or not*/
	function service_finder_custom_email_exists($email,$userid){
		global $wpdb;
		$table_name = $wpdb->prefix . 'users';
		$res = $wpdb->get_row($wpdb->prepare('SELECT ID from '.$table_name.' where `user_email` = "%s" and ID != %d',$email,$userid));
		return (!empty($res)) ? true : false;
	}
	
	/*Check If Gallery Image is attached already*/
	function service_finder_check_attachment($attachmentid){
		global $wpdb, $service_finder_Tables;
		$table_name = $wpdb->prefix . 'users';
		$res = $wpdb->get_row($wpdb->prepare('SELECT * from '.$service_finder_Tables->attachments.' where `attachmentid` = %d',$attachmentid));
		return (!empty($res)) ? true : false;
	}

}