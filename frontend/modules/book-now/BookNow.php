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

class SERVICE_FINDER_BookNow{

	/*Check Zipcode*/
	public function service_finder_checkZipcode($arg){
		global $wpdb, $service_finder_Tables;
		
		$settings = service_finder_getProviderSettings($arg['provider_id']);
		
		if($settings['booking_basedon'] == 'open'){
		
			$success = array(
						'status' => 'success',
						);
			echo json_encode($success);
		
		}else{
		
		$sql = $wpdb->prepare('SELECT id FROM '.$service_finder_Tables->service_area.' WHERE provider_id = %d AND zipcode = "%s" AND status = "active"',$arg['provider_id'],$arg['zipcode']);
		
		$res = $wpdb->get_row($sql);
		
		if(!empty($res)){
		
			$success = array(
						'status' => 'success',
						);
			echo json_encode($success);
			
		}else{

			$error = array(
						'status' => 'error',
						);
			echo json_encode($error);
		}
		
		}	

	}
	
	/*Load Members*/
	public function service_finder_loadMembers($arg){
		global $wpdb, $service_finder_Tables, $service_finder_Params, $service_finder_options;
			$settings = service_finder_getProviderSettings($arg['provider_id']);
			$editbooking = (!empty($arg['editbooking'])) ? $arg['editbooking'] : '';
			$html = '';
			if($settings['members_available'] == 'yes'){
				$memberid = (!empty($arg['memberid'])) ? $arg['memberid'] : '';
				$slot = (!empty($arg['slot'])) ? $arg['slot'] : '';
				$date = (!empty($arg['date'])) ? $arg['date'] : '';
				$zipcode = (!empty($arg['zipcode'])) ? $arg['zipcode'] : '';
				$region = (!empty($arg['region'])) ? $arg['region'] : '';
				$provider_id = (!empty($arg['provider_id'])) ? $arg['provider_id'] : '';
				$bookingid = (!empty($arg['bookingid'])) ? $arg['bookingid'] : '';
				
				if(service_finder_availability_method($provider_id) == 'timeslots'){
					$members = service_finder_getStaffMembers($provider_id,$zipcode,$date,$slot,$memberid,'',$region);
				}elseif(service_finder_availability_method($provider_id) == 'starttime'){
					$tem = explode('-',$slot);
					$start_time = (!empty($tem[0])) ? $tem[0] : '';
					$end_time = (!empty($tem[1])) ? $tem[1] : '';
					if(!empty($start_time) && !empty($end_time)){
						if($bookingid != "" && $bookingid > 0){
							$members = service_finder_getStaffMembersStartTimeEdit($provider_id,$zipcode,$date,$slot,$memberid,'',$region,$bookingid);
						}else{
							$members = service_finder_getStaffMembersStartTime($provider_id,$zipcode,$date,$slot,$memberid,'',$region);
						}
						
					}else{
						if($bookingid != "" && $bookingid > 0){
							$members = service_finder_getStaffMembersStartTimeEdit_nohours($provider_id,$zipcode,$date,$start_time,$memberid,'',$region,$bookingid);
						}else{
							$members = service_finder_getStaffMembersStartTime_nohours($provider_id,$zipcode,$date,$start_time,$memberid,'',$region);
						}
					}
				}else{
					$members = service_finder_getStaffMembers($provider_id,$zipcode,$date,$slot,$memberid,'',$region);
				}
				
				if(!empty($members)){
  if($service_finder_options['booking-page-style'] == 'style-1'){
  $class = 'col-md-3 col-sm-4 col-xs-6 equal-col';
  $html = '<div class="staff-member clear equal-col-outer">
  <div class="col-md-12">
    <div class="row">
      <h6 class="sf-title-staff">'.esc_html__('Choose Staff Member', 'service-finder').'</h6>
    </div>
  </div>
  ';
  }elseif($service_finder_options['booking-page-style'] == 'style-2'){
  $class = 'col-md-2 col-sm-3 col-xs-6 equal-col';
  $html = '<div class="staff-member clear equal-col-outer">
  <div class="col-md-12">
    <div class="row">
      <h6 class="sf-title-staff">'.esc_html__('Choose Staff Member', 'service-finder').'</h6>
    </div>
  </div>
  ';
  }
  $customeredit = (!empty($arg['customeredit'])) ? $arg['customeredit']  : '';
  if($customeredit == 'yes'){
  $class = 'col-md-3 col-sm-4 col-xs-6 equal-col';
  $html = '<div class="staff-member clear equal-col-outer">
  <div class="col-md-12">
    <div class="row">
      <h6 class="sf-title-staff">'.esc_html__('Choose Staff Member', 'service-finder').'</h6>
    </div>
  </div>
  ';
  }
  
  $html .= '
  <div class="row">';
    foreach($members as $member){
	
	if(service_finder_availability_method($provider_id) == 'timeslots'){
	$dayname = date('l', strtotime( $date ));
	$tem = explode('-',$slot);
	$start_time = (!empty($tem[0])) ? $tem[0] : '';
	$end_time = (!empty($tem[1])) ? $tem[1] : '';
	
	$member_timeslots = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->member_timeslots.' where day = %s and provider_id = %d AND `member_id` = %d AND start_time = %s AND end_time = %s',$dayname,$provider_id,$member->id,$start_time,$end_time));
	
	if(!empty($member_timeslots)){
	
	$editbooking = (!empty($arg['editbooking'])) ? $arg['editbooking']  : '';
	$memberid = (!empty($arg['memberid'])) ? $arg['memberid']  : '';
	$member_id = (!empty($member->id)) ? $member->id  : '';
    if($editbooking == 'yes'){
    if($memberid == $member_id){
    $select = 'selected';
    }else{
    $select = '';
    }	
    }else{
    $select = '';
    }
    $src  = wp_get_attachment_image_src( $member->avatar_id, 'service_finder-staff-member' );
    $src  = $src[0];
	if($src != ''){
	$imgtag = '<img src="'.esc_url($src).'" width="185" height="185" alt="">';
	}else{
	$imgtag = '';
	}
    $html .= sprintf('
    <div class="'.$class.'">
      <div class="sf-element-bx '.$select.'" data-id="'.esc_attr($member->id).'">
        <div class="sf-thum-bx overlay-black-light"> '.$imgtag.'
          <div class="member-done"><i class="fa fa-check"></i></div>
        </div>
        <div class="sf-title-bx clearfix"> <strong class="member-name">%s</strong> '.service_finder_displayRating(service_finder_getMemberAverageRating($member->id)).' </div>
      </div>
    </div>
    ',
    $member->member_name
    );
	}
	}elseif(service_finder_availability_method($provider_id) == 'starttime'){
	$editbooking = (!empty($arg['editbooking'])) ? $arg['editbooking']  : '';
	$memberid = (!empty($arg['memberid'])) ? $arg['memberid']  : '';
	$member_id = (!empty($member->id)) ? $member->id  : '';
    if($editbooking == 'yes'){
    if($memberid == $member_id){
    $select = 'selected';
    }else{
    $select = '';
    }	
    }else{
    $select = '';
    }
    $src  = wp_get_attachment_image_src( $member->avatar_id, 'service_finder-staff-member' );
    $src  = $src[0];
	if($src != ''){
	$imgtag = '<img src="'.esc_url($src).'" width="185" height="185" alt="">';
	}else{
	$imgtag = '';
	}
    $html .= sprintf('
    <div class="'.$class.'">
      <div class="sf-element-bx '.$select.'" data-id="'.esc_attr($member->id).'">
        <div class="sf-thum-bx overlay-black-light"> '.$imgtag.'
          <div class="member-done"><i class="fa fa-check"></i></div>
        </div>
        <div class="sf-title-bx clearfix"> <strong class="member-name">%s</strong> '.service_finder_displayRating(service_finder_getMemberAverageRating($member->id)).' </div>
      </div>
    </div>
    ',
    $member->member_name
    );
	}
	
	
    }
    $html .= '</div>
</div>
';
					}else{
					$html .= '<div>'.esc_html__('Sorry, There are no members available', 'service-finder').'</div>';
					}
			
			if($editbooking == 'yes'){
				return $html;
			}else{
				$success = array(
				'status' => 'success',
				'members' => $html,
				'totalmember' => count($members)
				);
				echo json_encode($success);
			}
			
											
			}else{
			
			if($arg['editbooking'] == 'yes'){
			return '';
			}else{
			$error = array(
				'status' => 'error',
				);
			echo json_encode($success);
			}
			}	
			
	}
	
	/*Load Customers data*/
	public function service_finder_load_customer_data($arg){
		global $wpdb, $service_finder_Tables, $service_finder_Params, $service_finder_options,$current_user;
			
		$customerid = (!empty($arg['customerid'])) ? esc_attr($arg['customerid']) : '';
		
		if($customerid != "" && $customerid > 0){
		
		$userInfo = service_finder_getUserInfo($customerid);
		
		}else{
		
		$userInfo = array();
		}
		
		echo json_encode($userInfo);
			
	}
	
	/*Load Members List*/
	public function service_finder_loadMembersList($arg){
		global $wpdb, $service_finder_Tables, $service_finder_Params, $service_finder_options;
			$settings = service_finder_getProviderSettings($arg['provider_id']);
			$editbooking = (!empty($arg['editbooking'])) ? $arg['editbooking'] : '';
			$html = '';
			if($settings['members_available'] == 'yes'){
				$sid = (!empty($arg['sid'])) ? $arg['sid'] : '';
				$memberid = (!empty($arg['memberid'])) ? $arg['memberid'] : '';
				$zipcode = (!empty($arg['zipcode'])) ? $arg['zipcode'] : '';
				$region = (!empty($arg['region'])) ? $arg['region'] : '';
				$provider_id = (!empty($arg['provider_id'])) ? $arg['provider_id'] : '';
				$bookingid = (!empty($arg['bookingid'])) ? $arg['bookingid'] : '';
				
				$members = service_finder_getStaffMembersList($provider_id,$sid,$zipcode,$region);
				
				$html .= '<option value="0">'.esc_html__('Any Member', 'service-finder').'</option>';
				
				if(!empty($members)){
					foreach($members as $member){
						$memberavatar = service_finder_getMemberAvatar($member->id);
						$html .= '<option data-avatar="'.esc_url($memberavatar).'" value="'.$member->id.'">'.$member->member_name.'</option>';
					}
				}else{
					$html .= '<option value="">'.esc_html__('No members available', 'service-finder').'</option>';
				}
				
			
				$success = array(
				'status' => 'success',
				'members' => $html,
				'totalmember' => count($members)
				);
				echo json_encode($success);
			
											
			}else{
			
			$error = array(
				'status' => 'error',
				);
			echo json_encode($error);
			}	
			
	}
	
	/*Fetch Services*/
	public function service_finder_getServices($provider_id = ''){
	
		global $wpdb, $service_finder_Tables, $service_finder_Params;
		$services = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->services.' WHERE `status` = "active" and `wp_user_id` = %d',$provider_id));
		if(!empty($services)){
			foreach($services as $service){
			$servicedata .= '
<li data-serviceid="'.esc_attr($service->id).'" data-cost="'.esc_attr($service->cost).'">
  <div class="addiner">
    <div class="addicon"><img src="'.$service_finder_Params['pluginImgUrl'].'/extra_services/lundry.png" alt=""></div>
    <div class="done"> <img src="'.$service_finder_Params['pluginImgUrl'].'/done.png" width="162" height="130" alt="">
      <h6>'.$service->cost.'</h6>
    </div>
    <p>'.$service->service_name.'</p>
  </div>
</li>
';
			}

			return $services = array(
						'status' => 1,
						'data' => $servicedata,
						);	
		}else{
			$servicedata = '
<li>No Service Found.</li>
';
			
			return $services = array(
						'status' => 0,
						'data' => $servicedata,
						);	
		}

	}
	
	public function service_finder_create_new_user($bookingdata){
	global $wpdb,$current_user,$service_finder_Tables;
	
	$username = $bookingdata['email'];
	$email = $bookingdata['email'];

		// Create account
		$new_user = array(
			'user_login' => $username,
			'user_pass'  => wp_generate_password(),
			'user_email' => $email,
			'role'       => 'Customer',
		);
	
		$user_id = wp_insert_user( $new_user );
	
		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}
		
		$firstname = (!empty($bookingdata['firstname'])) ? $bookingdata['firstname'] : ''; 
		$lastname = (!empty($bookingdata['lastname'])) ? $bookingdata['lastname'] : ''; 
		
		update_user_meta($user_id, 'first_name', $firstname);
		update_user_meta($user_id, 'last_name', $lastname);
		
		$initialamount = 0;
		update_user_meta($userId,'_sf_wallet_amount',$initialamount);

		/*Update Customer Data Table*/
		$data = array(
				'phone' => (!empty($bookingdata['phone'])) ? $bookingdata['phone'] : '', 
				'phone2' => (!empty($bookingdata['phone2'])) ? $bookingdata['phone2'] : '',
				'address' => (!empty($bookingdata['address'])) ? $bookingdata['address'] : '', 
				'apt' => (!empty($bookingdata['apt'])) ? $bookingdata['apt'] : '', 
				'city' => (!empty($bookingdata['city'])) ? $bookingdata['city'] : '', 
				'state' => (!empty($bookingdata['state'])) ? $bookingdata['state'] : '', 
				'country' => (!empty($bookingdata['country'])) ? $bookingdata['country'] : '', 
				'zipcode' => (!empty($bookingdata['zipcode'])) ? $bookingdata['zipcode'] : '',
				);
		
		$where = array(
				'wp_user_id' => $user_id,
				);
		$wpdb->update($service_finder_Tables->customers_data,wp_unslash($data),$where);	
	
		// Notify
		$notify = 'both';
		wp_new_user_notification( $user_id, null, $notify );
	
		// Login
		if(service_finder_getUserRole($current_user->ID) != 'Provider' && service_finder_getUserRole($current_user->ID) != 'administrator'){
		wp_set_auth_cookie( $user_id, true, is_ssl() );
		$current_user = get_user_by( 'id', $user_id );
		}
		
		return $user_id;
	}
	
	/*Save Booking Data*/
	public function service_finder_SaveBooking($bookingdata = '',$customerid = '',$txnid = '',$adminfee = 0,$paykey = ''){
		global $wpdb, $service_finder_Tables, $service_finder_Params, $current_user, $service_finder_options, $current_user;
		
		if(is_user_logged_in()){
			if(service_finder_getUserRole($current_user->ID) == 'Provider' || service_finder_getUserRole($current_user->ID) == 'administrator'){
			$bookingcustomerid = (!empty($bookingdata['choose_customer'])) ? $bookingdata['choose_customer'] : 0;
			if($bookingcustomerid > 0){
			$wp_user_id = $bookingcustomerid;
			}else{
			if(email_exists(esc_html($bookingdata['email']))){
				$ret = get_user_by( 'email', esc_html($bookingdata['email']) );
				
				if(!empty($ret)){
					$wp_user_id = $ret->ID;
				}else{
					$wp_user_id = 'NULL';
				}
			}else{
				$user_id = $this->service_finder_create_new_user($bookingdata);
				$wp_user_id = $user_id;
			}
			}
			}else{
			$wp_user_id = $current_user->ID;
			}
		}else{
			if(email_exists(esc_html($bookingdata['email']))){
				$ret = get_user_by( 'email', esc_html($bookingdata['email']) );
				
				if(!empty($ret)){
					$wp_user_id = $ret->ID;
					wp_set_auth_cookie( $wp_user_id, true, is_ssl() );
					$current_user = get_user_by( 'id', $wp_user_id );
				}else{
					$wp_user_id = 'NULL';
				}
			}else{
				$user_id = $this->service_finder_create_new_user($bookingdata);
				$wp_user_id = $user_id;
			}
		}
		
		if(service_finder_getUserRole($current_user->ID) == 'Provider'){
		$booking_made_by = 'provider';
		}elseif(service_finder_getUserRole($current_user->ID) == 'administrator'){
		$booking_made_by = 'admin';
		}else{
		$booking_made_by = 'customer';
		}
		$bookingpayment_mode = (!empty($bookingdata['bookingpayment_mode'])) ? $bookingdata['bookingpayment_mode'] : '';
		
		if($bookingpayment_mode == 'paypal'){
			$paypal_token = $customerid;
			$stripe_cusID = '';
			$invoiceid = '';
			$status = 'Need-Approval';
		}elseif($bookingpayment_mode == 'payumoney'){
			$stripe_cusID = '';
			$paypal_token = '';
			$invoiceid = '';
			$status = 'Need-Approval';
		}elseif($bookingpayment_mode == 'payulatam'){
			$stripe_cusID = '';
			$paypal_token = '';
			$invoiceid = '';
			$status = 'Pending';
		}elseif($bookingpayment_mode == 'wired'){
			$stripe_cusID = '';
			$paypal_token = '';
			$invoiceid = strtoupper(uniqid('BK-'));
			$status = 'Need-Approval';
		}elseif($bookingpayment_mode == 'twocheckout'){
			$stripe_cusID = '';
			$paypal_token = '';
			$invoiceid = '';
			$status = 'Pending';
		}elseif($bookingpayment_mode == 'cod'){
			$stripe_cusID = '';
			$paypal_token = '';
			$invoiceid = '';
			$status = 'Need-Approval';
		}elseif($bookingpayment_mode == 'wallet'){
			$stripe_cusID = '';
			$paypal_token = '';
			$invoiceid = '';
			$status = 'Pending';
		}else{
			$stripe_cusID = $customerid;
			$paypal_token = '';
			$invoiceid = '';
			$status = 'Pending';
		}
		
		$customerdata = array(
				'wp_user_id' => $wp_user_id,
				'name' => $bookingdata['firstname'].' '.$bookingdata['lastname'], 
				'phone' => (!empty($bookingdata['phone'])) ? $bookingdata['phone'] : '', 
				'phone2' => (!empty($bookingdata['phone2'])) ? $bookingdata['phone2'] : '',
				'email' => (!empty($bookingdata['email'])) ? $bookingdata['email'] : '',
				'address' => (!empty($bookingdata['address'])) ? $bookingdata['address'] : '', 
				'apt' => (!empty($bookingdata['apt'])) ? $bookingdata['apt'] : '', 
				'city' => (!empty($bookingdata['city'])) ? $bookingdata['city'] : '', 
				'state' => (!empty($bookingdata['state'])) ? $bookingdata['state'] : '', 
				'country' => (!empty($bookingdata['country'])) ? $bookingdata['country'] : '', 
				'zipcode' => (!empty($bookingdata['zipcode'])) ? $bookingdata['zipcode'] : '',
				'region' => (!empty($bookingdata['region'])) ? $bookingdata['region'] : '',
				'description' => (!empty($bookingdata['shortdesc'])) ? $bookingdata['shortdesc'] : '',  
				);
		$wpdb->insert($service_finder_Tables->customers,wp_unslash($customerdata));
		
		$booking_customer_id = $wpdb->insert_id;
		$time = explode('-',$bookingdata['boking-slot']);
		
		$selecteddate = (!empty($bookingdata['selecteddate'])) ? $bookingdata['selecteddate'] : '';
		$bookingdate = (!empty($bookingdata['bookingdate'])) ? $bookingdata['bookingdate'] : '';
		
		if($bookingpayment_mode == 'paypal' || $bookingpayment_mode == 'payumoney'){
		$bookingdate = date('Y-m-d',strtotime($selecteddate));
		}elseif($bookingpayment_mode == 'stripe' || $bookingpayment_mode == 'twocheckout'){
		$bookingdate = date('Y-m-d',strtotime($bookingdate));
		}else{
			if($selecteddate != ""){
			$bookingdate = date('Y-m-d',strtotime($selecteddate));
			}else{
			$bookingdate = date('Y-m-d',strtotime($bookingdate));
			}
		}
		
		$anymember = (!empty($bookingdata['anymember'][0])) ? $bookingdata['anymember'][0] : '';
		if($anymember == 'yes'){
		$memberid = 0;
		}else{
		$memberid = (!empty($bookingdata['memberid'])) ? $bookingdata['memberid'] : '';
		}

		$payent_mode = ($bookingpayment_mode != '') ? $bookingpayment_mode : 'free'; 
		
		$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? esc_html($service_finder_options['pay_booking_amount_to']) : '';
		
		$getjobid = (!empty($bookingdata['jobid'])) ? esc_html($bookingdata['jobid']) : '';
		if($getjobid > 0){
			$jobpost = get_post($getjobid);
			$jobauthor = $jobpost->post_author;
				if(service_finder_is_job_author($getjobid,$jobauthor)){
					$jobid = $getjobid;
				}else{
					$jobid = 0;
				}
		}else{
			$jobid = 0;
		}
		
		$admin_fee_fixed = (!empty($service_finder_options['admin-fee-fixed'])) ? $service_finder_options['admin-fee-fixed'] : 0;
		$admin_fee_percentage = (!empty($service_finder_options['admin-fee-percentage'])) ? $service_finder_options['admin-fee-percentage'] : 0;
		$charge_admin_fee = (!empty($service_finder_options['charge-admin-fee'])) ? $service_finder_options['charge-admin-fee'] : '';
		$charge_admin_fee_from = (!empty($service_finder_options['charge-admin-fee-from'])) ? $service_finder_options['charge-admin-fee-from'] : '';
		
		if($charge_admin_fee && $pay_booking_amount_to == 'admin' && ($admin_fee_percentage > 0 || $admin_fee_fixed > 0)){
			$charge_admin_fee_from = (!empty($service_finder_options['charge-admin-fee-from'])) ? $service_finder_options['charge-admin-fee-from'] : '';
		}else{
			$charge_admin_fee_from = '';
		}	
		
		$multidate = (service_finder_booking_date_method($bookingdata['provider']) == 'multidate') ? 'yes' : 'no';
		
		$getquoteid = (!empty($bookingdata['quoteid'])) ? esc_html($bookingdata['quoteid']) : 0;
		
		$settings = service_finder_getProviderSettings($bookingdata['provider']);
		$buffertime = service_finder_get_data($settings,'buffertime');
		if($buffertime > 0 && $buffertime != '' && $multidate == 'no' && !empty($time[1]))
		{
		$booking_end_time = date('H:i:s', strtotime($time[1]." +".$buffertime." minutes"));
		}else{
		$booking_end_time = $time[1];
		}
		
		if($bookingdata['service_perform_at'] == 'provider_location')
		{
			$service_location = get_user_meta($bookingdata['provider'],'my_location',true);
		}elseif($bookingdata['service_perform_at'] == 'customer_location'){
			if(service_finder_themestyle() == 'style-4'){
				$service_location = (!empty($bookingdata['address'])) ? $bookingdata['address'] : '';
			}else{
				$service_location = (!empty($bookingdata['location'])) ? $bookingdata['location'] : '';
			}
			
		}else{
			$service_location = '';
		}
		
		$bookdata = array(
				'created' => date('Y-m-d h:i:s'), 
				'booking_made_by' => $booking_made_by, 
				'date' => ($multidate == 'no' || $bookingdata['jobid'] > 0 || $bookingdata['quoteid'] > 0) ? $bookingdate : Null, 
				'start_time' => ((!empty($time[0]) && $multidate == 'no') || ($bookingdata['jobid'] > 0 || $bookingdata['quoteid'] > 0)) ? $time[0] : Null, 
				'end_time' => ((!empty($time[1]) && $multidate == 'no') || ($bookingdata['jobid'] > 0 || $bookingdata['quoteid'] > 0)) ? $booking_end_time : Null,
				'end_time_no_buffer' => ((!empty($time[1]) && $multidate == 'no') || ($bookingdata['jobid'] > 0 || $bookingdata['quoteid'] > 0)) ? $time[1] : Null, 
				'jobid' => $jobid,
				'quoteid' => $getquoteid,
				'provider_id' => $bookingdata['provider'], 
				'service_perform_at' => $bookingdata['service_perform_at'], 
				'service_location' => $service_location,
				'customer_id' => $wp_user_id,
				'member_id' => $memberid, 
				'type' => $payent_mode, 
				'services' => (!empty($bookingdata['servicearr'])) ? $bookingdata['servicearr'] : '',
				'booking_customer_id' => $booking_customer_id,
				'stripe_customer_id' => (!empty($stripe_cusID)) ? $stripe_cusID : '',
				'stripe_token' => (!empty($bookingdata['stripeToken'])) ? $bookingdata['stripeToken'] : '',
				'paypal_token' => (!empty($paypal_token)) ? $paypal_token : '',
				'wired_invoiceid' => esc_html($invoiceid),
				'payment_to' => esc_html($pay_booking_amount_to),
				'total' => (!empty($bookingdata['totalcost'])) ? $bookingdata['totalcost'] : '',
				'discount' => (!empty($bookingdata['totaldiscount']) && service_finder_offers_method($bookingdata['provider']) == 'booking') ? $bookingdata['totaldiscount'] : 0,
				'coupon_code' => (!empty($bookingdata['couponcode']) && service_finder_offers_method($bookingdata['provider']) == 'booking') ? $bookingdata['couponcode'] : '',
				'dicount_based_on' => service_finder_offers_method($bookingdata['provider']),
				'adminfee' => $adminfee,
				'charge_admin_fee_from' => $charge_admin_fee_from,
				'status' => esc_html($status),
				'payonlyadminfee' => (service_finder_has_pay_only_admin_fee() == true) ? 'yes' : 'no',
				'txnid' => esc_html($txnid),
				'paid_to_provider' => ($pay_booking_amount_to == 'provider') ? 'paid' : 'pending',
				'paypal_paykey' => $paykey,
				'multi_date' => $multidate,
				);

		$wpdb->insert($service_finder_Tables->bookings,wp_unslash($bookdata));
		$booking_id = $wpdb->insert_id;
		
		if($bookingdata['jobid'] == '' && $bookingdata['quoteid'] == ''){
		if($multidate == 'yes'){
			$servicearr = (!empty($bookingdata['servicearr'])) ? $bookingdata['servicearr'] : '';
			$servicearr = trim($servicearr,'%%');
			$serviceitems = explode('%%',$servicearr);
			
			if(!empty($serviceitems)){
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
						$sdates = explode('##',$sdate);
						
						if(!empty($sdates)){
							foreach($sdates as $sdate){
								$servicedata = array(
										'booking_id' => $booking_id,
										'service_id' => $sid, 
										'date' => $sdate, 
										'hours' => '',
										'start_time' => Null,
										'end_time' => Null,
										'without_padding_start_time' => Null,
										'without_padding_end_time' => Null,
										'fullday' => 'yes', 
										'member_id' => $smemberid, 
										'couponcode' => $couponcode,
										'discount' => $discount, 
										);
								$wpdb->insert($service_finder_Tables->booked_services,wp_unslash($servicedata));
							}
						}
					}else{
								$serslot = explode('-',$serslots);
								
								$paddingtime = service_finder_get_service_paddind_time($sid);
								$before_padding_time = $paddingtime['before_padding_time'];
								$after_padding_time = $paddingtime['after_padding_time'];
								
								$mstarttime = (!empty($serslot[0])) ? $serslot[0] : Null; 
								$mendtime = (!empty($serslot[1])) ? $serslot[1] : Null; 
								$midtime = (!empty($serslot[1])) ? $serslot[1] : Null; 
								
								if(service_finder_availability_method($bookingdata['provider']) == 'timeslots'){
									
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
								
								if(!$after_padding_time > 0)
								{
									if($buffertime > 0 && $buffertime != '')
									{
									$mendtime = date('H:i:s', strtotime($midtime." +".$buffertime." minutes"));
									}
								}
								
								$servicedata = array(
										'booking_id' => $booking_id,
										'service_id' => $sid, 
										'date' => $sdate, 
										'hours' => $shours,
										'start_time' => (!empty($serslot[0])) ? $mstarttime : Null, 
										'end_time' => (!empty($serslot[1])) ? $mendtime : Null, 
										'without_padding_start_time' => (!empty($serslot[0])) ? $serslot[0] : Null, 
										'without_padding_end_time' => $midtime, 
										'member_id' => $smemberid, 
										'couponcode' => $couponcode,
										'discount' => $discount, 
										);
								$wpdb->insert($service_finder_Tables->booked_services,wp_unslash($servicedata));
					}	
				}
			}
		}
		}
		
		$getjobid = (!empty($bookingdata['jobid'])) ? esc_html($bookingdata['jobid']) : '';
		if($getjobid > 0){
			$jobpost = get_post($getjobid);
			$jobauthor = $jobpost->post_author;
				if(service_finder_is_job_author($getjobid,$jobauthor)){
					$jobid = $getjobid;
					update_post_meta($jobid,'_filled',1);
					update_post_meta($jobid,'_assignto',$bookingdata['provider']);
					update_post_meta($jobid,'_bookingid',$booking_id);
				}
		}
		
		$getquoteid = (!empty($bookingdata['quoteid'])) ? esc_html($bookingdata['quoteid']) : '';
		if($getquoteid > 0){
			service_finder_update_quote_hired($getquoteid,$bookingdata['provider']);
		}
		
		$customername = $bookingdata['firstname'].' '.$bookingdata['lastname'];
		
		if(function_exists('service_finder_add_notices') && $bookingpayment_mode != 'payumoney') {
		
			if($bookingdata['jobid'] == '' && $bookingdata['quoteid'] == ''){
			if($multidate == 'yes'){
			$noticedata = array(
					'provider_id' => $bookingdata['provider'],
					'target_id' => $booking_id, 
					'topic' => 'Booking',
					'title' => esc_html__('Booking', 'service-finder'),
					'notice' => sprintf( esc_html__('You have new booking. Booking Ref id is #%d', 'service-finder'), $booking_id ),
					);
			service_finder_add_notices($noticedata);
			}else{
			$noticedata = array(
					'provider_id' => $bookingdata['provider'],
					'target_id' => $booking_id, 
					'topic' => 'Booking',
					'title' => esc_html__('Booking', 'service-finder'),
					'notice' => sprintf( esc_html__('You have new booking on %s at %s by %s. Booking Ref id is #%d', 'service-finder'), $bookingdate,$time[0],$customername,$booking_id ),
					);
			service_finder_add_notices($noticedata);
			}
			}else{
			$noticedata = array(
					'provider_id' => $bookingdata['provider'],
					'target_id' => $booking_id, 
					'topic' => 'Booking',
					'title' => esc_html__('Booking', 'service-finder'),
					'notice' => sprintf( esc_html__('You have new booking on %s at %s by %s. Booking Ref id is #%d', 'service-finder'), $bookingdate,$time[0],$customername,$booking_id ),
					);
			service_finder_add_notices($noticedata);
			}
			
		}
		
		$google_calendar = (!empty($settings['google_calendar'])) ? $settings['google_calendar'] : '';
		
		if($google_calendar == 'on'){
		service_finder_addto_google_calendar($booking_id,$bookingdata['provider']);
		}
		
		if($payent_mode == 'free' || $payent_mode == 'skippayment' || $payent_mode == 'wired' || $payent_mode == 'cod' || $payent_mode == 'wallet'){
		$senMail = new SERVICE_FINDER_BookNow();
				
		$bookingdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `id` = %d',$booking_id),ARRAY_A);
		
		$senMail->service_finder_SendBookingMailToProvider($bookingdata,$invoiceid,$adminfee);
		$senMail->service_finder_SendBookingMailToCustomer($bookingdata,$invoiceid,$adminfee);
		$senMail->service_finder_SendBookingMailToAdmin($bookingdata,$invoiceid,$adminfee);
		
		}

	}
	
	/*Save Booking Data*/
	public function service_finder_SaveWooBooking($bookingdata = '',$order_id = 0,$item_id = 0){
		global $wpdb, $service_finder_Tables, $service_finder_Params, $current_user, $service_finder_options;
		
		$order = new \WC_Order( $order_id );
		
		$payment_method = $order->get_payment_method();
		$order_status = $order->get_status();
		
		if ( $bookingdata && ! isset ( $bookingdata['processed'] ) ) {
		
		if($order_status == 'on-hold'){
			$status = 'Need-Approval';
		}elseif($order_status == 'completed' || $order_status == 'processing'){
			$status = 'Pending';
		}
		
		$existingbooking = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `payment_type` = "woocommerce" AND `order_id` = %d',$order_id));
		if(!empty($existingbooking)){
			$updatedata = array(
						'status' => esc_html($status),
						);
			$where = array(
						'order_id' => $order_id,
						'payment_type' => 'woocommerce'
						);			
		
			$wpdb->update($service_finder_Tables->bookings,wp_unslash($updatedata),$where);
			return;
		}
		
			if(is_user_logged_in()){
				
				if(service_finder_getUserRole($current_user->ID) == 'Provider' || service_finder_getUserRole($current_user->ID) == 'administrator'){
				$bookingcustomerid = (!empty($bookingdata['choose_customer'])) ? $bookingdata['choose_customer'] : 0;
				if($bookingcustomerid > 0){
				$wp_user_id = $bookingcustomerid;
				}else{
				if(email_exists(esc_html($bookingdata['email']))){
					$ret = get_user_by( 'email', esc_html($bookingdata['email']) );
					
					if(!empty($ret)){
						$wp_user_id = $ret->ID;
					}else{
						$wp_user_id = 'NULL';
					}
				}else{
					$user_id = $this->service_finder_create_new_user($bookingdata);
					$wp_user_id = $user_id;
				}
				}
				}else{
				$wp_user_id = $current_user->ID;
				}
			
			}else{
				if(email_exists(esc_html($bookingdata['email']))){
					$ret = get_user_by( 'email', esc_html($bookingdata['email']) );
					
					if(!empty($ret)){
						$wp_user_id = $ret->ID;
						wp_set_auth_cookie( $wp_user_id, true, is_ssl() );
						$current_user = get_user_by( 'id', $wp_user_id );
					}else{
						$wp_user_id = 'NULL';
					}
				}else{
					$user_id = $this->service_finder_create_new_user($bookingdata);
					$wp_user_id = $user_id;
				}
			}
			
			if(service_finder_getUserRole($current_user->ID) == 'Provider'){
			$booking_made_by = 'provider';
			}elseif(service_finder_getUserRole($current_user->ID) == 'administrator'){
			$booking_made_by = 'admin';
			}else{
			$booking_made_by = 'customer';
			}
			
			$customerdata = array(
					'wp_user_id' => $wp_user_id,
					'name' => $bookingdata['firstname'].' '.$bookingdata['lastname'], 
					'phone' => (!empty($bookingdata['phone'])) ? $bookingdata['phone'] : '', 
					'phone2' => (!empty($bookingdata['phone2'])) ? $bookingdata['phone2'] : '',
					'email' => (!empty($bookingdata['email'])) ? $bookingdata['email'] : '',
					'address' => (!empty($bookingdata['address'])) ? $bookingdata['address'] : '', 
					'apt' => (!empty($bookingdata['apt'])) ? $bookingdata['apt'] : '', 
					'city' => (!empty($bookingdata['city'])) ? $bookingdata['city'] : '', 
					'state' => (!empty($bookingdata['state'])) ? $bookingdata['state'] : '', 
					'country' => (!empty($bookingdata['country'])) ? $bookingdata['country'] : '', 
					'zipcode' => (!empty($bookingdata['zipcode'])) ? $bookingdata['zipcode'] : '',
					'region' => (!empty($bookingdata['region'])) ? $bookingdata['region'] : '',
					'description' => (!empty($bookingdata['shortdesc'])) ? $bookingdata['shortdesc'] : '',  
					);
			$wpdb->insert($service_finder_Tables->customers,wp_unslash($customerdata));
			$booking_customer_id = $wpdb->insert_id;
			
			$firstname = (!empty($bookingdata['firstname'])) ? $bookingdata['firstname'] : '';
			$lastname = (!empty($bookingdata['lastname'])) ? $bookingdata['lastname'] : '';
			
			$getfname = get_user_meta($wp_user_id,'first_name',true );
			$getlname = get_user_meta($wp_user_id,'first_name',true );
			
			if($getfname == '' && $getlname == ''){
			update_user_meta($wp_user_id,'first_name',$firstname );
			update_user_meta($wp_user_id,'last_name',$lastname);
			}
			
			
			$time = explode('-',$bookingdata['boking-slot']);
			
			$bookingdate = (!empty($bookingdata['bookingdate'])) ? $bookingdata['bookingdate'] : '';
			
			$bookingdate = date('Y-m-d',strtotime($bookingdate));
			
			$anymember = (!empty($bookingdata['anymember'][0])) ? $bookingdata['anymember'][0] : '';
			if($anymember == 'yes'){
			$memberid = 0;
			}else{
			$memberid = (!empty($bookingdata['memberid'])) ? $bookingdata['memberid'] : '';
			}
			
			$payent_mode = $payment_method; 
		
			$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? esc_html($service_finder_options['pay_booking_amount_to']) : '';
			
			$getjobid = (!empty($bookingdata['jobid'])) ? esc_html($bookingdata['jobid']) : '';
			if($getjobid > 0){
				$jobpost = get_post($getjobid);
				$jobauthor = $jobpost->post_author;
					if(service_finder_is_job_author($getjobid,$jobauthor)){
						$jobid = $getjobid;
					}else{
						$jobid = 0;
					}
			}else{
				$jobid = 0;
			}
			
			$getquoteid = (!empty($bookingdata['quoteid'])) ? esc_html($bookingdata['quoteid']) : 0;
			
			$admin_fee_fixed = (!empty($service_finder_options['admin-fee-fixed'])) ? $service_finder_options['admin-fee-fixed'] : 0;
			$admin_fee_percentage = (!empty($service_finder_options['admin-fee-percentage'])) ? $service_finder_options['admin-fee-percentage'] : 0;
			$charge_admin_fee = (!empty($service_finder_options['charge-admin-fee'])) ? $service_finder_options['charge-admin-fee'] : '';
			$charge_admin_fee_from = (!empty($service_finder_options['charge-admin-fee-from'])) ? $service_finder_options['charge-admin-fee-from'] : '';
			
			if($charge_admin_fee && $pay_booking_amount_to == 'admin' && ($admin_fee_percentage > 0 || $admin_fee_fixed > 0)){
				$charge_admin_fee_from = (!empty($service_finder_options['charge-admin-fee-from'])) ? $service_finder_options['charge-admin-fee-from'] : '';
			}else{
				$charge_admin_fee_from = '';
			}	
			
			$multidate = (service_finder_booking_date_method($bookingdata['provider']) == 'multidate') ? 'yes' : 'no';
			
			$settings = service_finder_getProviderSettings($bookingdata['provider']);
			$buffertime = service_finder_get_data($settings,'buffertime');
			if($buffertime > 0 && $buffertime != '' && $multidate == 'no' && !empty($time[1]))
			{
			$booking_end_time = date('H:i:s', strtotime($time[1]." +".$buffertime." minutes"));
			}else{
			$booking_end_time = $time[1];
			}
			
			if($bookingdata['service_perform_at'] == 'provider_location')
			{
				$service_location = get_user_meta($bookingdata['provider'],'my_location',true);
			}elseif($bookingdata['service_perform_at'] == 'customer_location'){
				if(service_finder_themestyle() == 'style-4'){
					$service_location = (!empty($bookingdata['address'])) ? $bookingdata['address'] : '';
				}else{
					$service_location = (!empty($bookingdata['location'])) ? $bookingdata['location'] : '';
				}
			}else{
				$service_location = '';
			}
			
			$bookdata = array(
					'created' => date('Y-m-d h:i:s'), 
					'booking_made_by' => $booking_made_by, 
					'date' => ($multidate == 'no' || $bookingdata['jobid'] > 0 || $bookingdata['quoteid'] > 0) ? $bookingdate : Null, 
					'start_time' => ((!empty($time[0]) && $multidate == 'no') || ($bookingdata['jobid'] > 0 || $bookingdata['quoteid'] > 0)) ? $time[0] : Null, 
					'end_time' => ((!empty($time[1]) && $multidate == 'no') || ($bookingdata['jobid'] > 0 || $bookingdata['quoteid'] > 0)) ? $booking_end_time : Null, 
					'end_time_no_buffer' => ((!empty($time[1]) && $multidate == 'no') || ($bookingdata['jobid'] > 0 || $bookingdata['quoteid'] > 0)) ? $time[1] : Null,
					'jobid' => $jobid,
					'quoteid' => $getquoteid,
					'service_perform_at' => $bookingdata['service_perform_at'],
					'service_location' => $service_location,
					'provider_id' => $bookingdata['provider'], 
					'customer_id' => $wp_user_id,
					'member_id' => $memberid, 
					'type' => $payent_mode, 
					'services' => (!empty($bookingdata['servicearr'])) ? $bookingdata['servicearr'] : '',
					'booking_customer_id' => $booking_customer_id,
					'payment_to' => esc_html($pay_booking_amount_to),
					'total' => (!empty($bookingdata['totalcost'])) ? $bookingdata['totalcost'] : '',
					'discount' => (!empty($bookingdata['totaldiscount']) && service_finder_offers_method($bookingdata['provider']) == 'booking') ? $bookingdata['totaldiscount'] : 0,
					'coupon_code' => (!empty($bookingdata['couponcode']) && service_finder_offers_method($bookingdata['provider']) == 'booking') ? $bookingdata['couponcode'] : '',
					'dicount_based_on' => service_finder_offers_method($bookingdata['provider']),
					'adminfee' => $bookingdata['adminfee'],
					'charge_admin_fee_from' => $charge_admin_fee_from,
					'status' => esc_html($status),
					'payonlyadminfee' => (service_finder_has_pay_only_admin_fee() == true) ? 'yes' : 'no',
					'paid_to_provider' => ($pay_booking_amount_to == 'provider') ? 'paid' : 'pending',
					'order_id' => $order_id,
					'payment_type' => 'woocommerce',
					'multi_date' => $multidate,
					);
	
			$wpdb->insert($service_finder_Tables->bookings,wp_unslash($bookdata));
			$booking_id = $wpdb->insert_id;
			
			if($bookingdata['jobid'] == '' && $bookingdata['quoteid'] == ''){
			if($multidate == 'yes'){
			$servicearr = (!empty($bookingdata['servicearr'])) ? $bookingdata['servicearr'] : '';
			$servicearr = trim($servicearr,'%%');
			$serviceitems = explode('%%',$servicearr);
			
			if(!empty($serviceitems)){
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
						$sdates = explode('##',$sdate);
						
						if(!empty($sdates)){
							foreach($sdates as $sdate){
								$servicedata = array(
										'booking_id' => $booking_id,
										'service_id' => $sid, 
										'date' => $sdate, 
										'hours' => '',
										'start_time' => Null,
										'end_time' => Null,
										'without_padding_start_time' => Null,
										'without_padding_end_time' => Null,
										'fullday' => 'yes', 
										'member_id' => $smemberid, 
										'couponcode' => $couponcode,
										'discount' => $discount, 
										);
								$wpdb->insert($service_finder_Tables->booked_services,wp_unslash($servicedata));
							}
						}
					}else{
								$serslot = explode('-',$serslots);
								
								$paddingtime = service_finder_get_service_paddind_time($sid);
								$before_padding_time = $paddingtime['before_padding_time'];
								$after_padding_time = $paddingtime['after_padding_time'];
								
								$mstarttime = (!empty($serslot[0])) ? $serslot[0] : Null; 
								$mendtime = (!empty($serslot[1])) ? $serslot[1] : Null; 
								$midtime = (!empty($serslot[1])) ? $serslot[1] : Null; 
								
								if(service_finder_availability_method($bookingdata['provider']) == 'timeslots'){
									
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
								
								if(!$after_padding_time > 0)
								{
									if($buffertime > 0 && $buffertime != '')
									{
									$mendtime = date('H:i:s', strtotime($midtime." +".$buffertime." minutes"));
									}
								}
								
								$servicedata = array(
										'booking_id' => $booking_id,
										'service_id' => $sid, 
										'date' => $sdate, 
										'hours' => $shours,
										'start_time' => (!empty($serslot[0])) ? $mstarttime : Null, 
										'end_time' => (!empty($serslot[1])) ? $mendtime : Null, 
										'without_padding_start_time' => (!empty($serslot[0])) ? $serslot[0] : Null, 
										'without_padding_end_time' => $midtime, 
										'member_id' => $smemberid, 
										'couponcode' => $couponcode,
										'discount' => $discount, 
										);
								$wpdb->insert($service_finder_Tables->booked_services,wp_unslash($servicedata));
					}		
				}
			}
			}
			}
			
			$getjobid = (!empty($bookingdata['jobid'])) ? esc_html($bookingdata['jobid']) : '';
			if($getjobid > 0){
				$jobpost = get_post($getjobid);
				$jobauthor = $jobpost->post_author;
					if(service_finder_is_job_author($getjobid,$jobauthor)){
						$jobid = $getjobid;
						update_post_meta($jobid,'_filled',1);
						update_post_meta($jobid,'_assignto',$bookingdata['provider']);
						update_post_meta($jobid,'_bookingid',$booking_id);
					}
			}
			
			$getquoteid = (!empty($bookingdata['quoteid'])) ? esc_html($bookingdata['quoteid']) : '';
			if($getquoteid > 0){
				service_finder_update_quote_hired($getquoteid,$bookingdata['provider']);
			}
			
			$customername = $bookingdata['firstname'].' '.$bookingdata['lastname'];
			
			if(function_exists('service_finder_add_notices')) {
			
				if($bookingdata['jobid'] == '' && $bookingdata['quoteid'] == ''){
				if($multidate == 'yes'){
				$noticedata = array(
						'provider_id' => $bookingdata['provider'],
						'target_id' => $booking_id, 
						'topic' => 'Booking',
						'title' => esc_html__('Booking', 'service-finder'),
						'notice' => sprintf( esc_html__('You have new booking. Booking Ref id is #%d', 'service-finder'), $booking_id ),
						);
				service_finder_add_notices($noticedata);
				}else{
				$noticedata = array(
						'provider_id' => $bookingdata['provider'],
						'target_id' => $booking_id, 
						'topic' => 'Booking',
						'title' => esc_html__('Booking', 'service-finder'),
						'notice' => sprintf( esc_html__('You have new booking on %s at %s by %s. Booking Ref id is #%d', 'service-finder'), $bookingdate,$time[0],$customername, $booking_id ),
						);
				service_finder_add_notices($noticedata);
				}
				}else{
				$noticedata = array(
						'provider_id' => $bookingdata['provider'],
						'target_id' => $booking_id, 
						'topic' => 'Booking',
						'title' => esc_html__('Booking', 'service-finder'),
						'notice' => sprintf( esc_html__('You have new booking on %s at %s by %s. Booking Ref id is #%d', 'service-finder'), $bookingdate,$time[0],$customername, $booking_id ),
						);
				service_finder_add_notices($noticedata);
				}
				
			}
			
			$google_calendar = (!empty($settings['google_calendar'])) ? $settings['google_calendar'] : '';
			
			if($google_calendar == 'on'){
			service_finder_addto_google_calendar($booking_id,$bookingdata['provider']);
			}
			
			$senMail = new SERVICE_FINDER_BookNow();
					
			$bookingdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `id` = %d',$booking_id),ARRAY_A);
			$invoiceid = '';
			$senMail->service_finder_SendBookingMailToProvider($bookingdata,$invoiceid,$bookingdata['adminfee']);
			$senMail->service_finder_SendBookingMailToCustomer($bookingdata,$invoiceid,$bookingdata['adminfee']);
			$senMail->service_finder_SendBookingMailToAdmin($bookingdata,$invoiceid,$bookingdata['adminfee']);
			
			}else{
			
			$existingbooking = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `payment_type` = "woocommerce" AND `order_id` = %d',$order_id));
			if(!empty($existingbooking) && ($order_status == 'completed' || $order_status == 'processing') && $bookingdata && ! isset ( $bookingdata['completed'] ) && ! isset ( $bookingdata['cancelled'] )){
				$updatedata = array(
							'status' => 'Pending'
							);
				$where = array(
							'order_id' => $order_id,
							'payment_type' => 'woocommerce'
							);			
			
				$wpdb->update($service_finder_Tables->bookings,wp_unslash($updatedata),$where);
				
			}	
		}
			
	}
	
	/*Send Booking mail to provider*/
	public function service_finder_SendBookingMailToProvider($maildata = '',$invoiceid = '',$adminfee = '0.0'){
		global $service_finder_options, $service_finder_Tables, $wpdb;
		
		$providerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$maildata['provider_id']));
		$customerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `id` = %d',$maildata['booking_customer_id']));
		
		$bookingpayment_mode = (!empty($maildata['type'])) ? $maildata['type'] : '';
		
		$payent_mode = ($bookingpayment_mode != '') ? $bookingpayment_mode : 'free';
		$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? $service_finder_options['pay_booking_amount_to'] : '';
		
		if($payent_mode == 'wired' && $pay_booking_amount_to == 'provider'){
		$message = 'Invoice ID:'.$invoiceid;
		}else{
		$message = '';
		}
		
		if($maildata['jobid'] > 0){
		$message .= 'Job ID #: '.$maildata['jobid'];
		}
		
		if($maildata['quoteid'] > 0){
		$message .= 'Quote ID #: '.$maildata['quoteid'];
		}

		if(!empty($service_finder_options['booking-to-provider'])){
			$message .= $service_finder_options['booking-to-provider'];
		}else{
			$message .= '
<h4>Booking Details</h4>
Booking REF ID #: %BOOKINGREFID%

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

Service Location: %SERVICELOCATION%

Description: %SHORTDESCRIPTION%

<h4>Payment Details</h4>
Pay Via: %PAYMENTMETHOD%
				
				Amount: %AMOUNT%
				
				Admin Fee: %ADMINFEE%';
		}
			
			$tokens = array('%BOOKINGREFID%','%DATE%','%STARTTIME%','%ENDTIME%','%MEMBERNAME%','%PROVIDERNAME%','%PROVIDEREMAIL%','%PROVIDERPHONE%','%CUSTOMERNAME%','%CUSTOMEREMAIL%','%CUSTOMERPHONE%','%CUSTOMERPHONE2%','%ADDRESS%','%APT%','%CITY%','%STATE%','%ZIPCODE%','%COUNTRY%','%SERVICES%','%PAYMENTMETHOD%','%AMOUNT%','%ADMINFEE%','%SHORTDESCRIPTION%','%SERVICELOCATION%');
			
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
			$adminfee = '0.0';
			}
			
			if($maildata['multi_date'] == 'yes')
			{
				$bookingdate = service_finder_date_format($maildata['created']);
			}else{
				$bookingdate = service_finder_date_format($maildata['date']);
			}
			
			$replacements = array($maildata['id'],$bookingdate,$maildata['start_time'],service_finder_get_booking_end_time($maildata['end_time'],$maildata['end_time_no_buffer']),$membername,service_finder_get_providername_with_link($providerInfo->wp_user_id),$providerInfo->email,service_finder_get_contact_info($providerInfo->phone,$providerInfo->mobile),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country,$services,ucfirst($payent_mode),service_finder_money_format($bookingamount),service_finder_money_format($adminfee),$customerInfo->description,service_finder_get_service_location($maildata['id']));
			$msg_body = str_replace($tokens,$replacements,$message);
			
			if($service_finder_options['booking-to-provider-subject'] != ""){
				$msg_subject = $service_finder_options['booking-to-provider-subject'];
			}else{
				$msg_subject = esc_html__('Booking Notification', 'service-finder');
			}
			
			if(class_exists('aonesms'))
			{
			if($maildata['jobid'] > 0)
			{
			if(service_finder_get_data($service_finder_options,'is-active-provider-job-hire-sms') == true)
			{
			$smsbody = service_finder_get_data($service_finder_options,'template-provider-job-hire-sms');
			if($smsbody != '')
			{
			
			$smsservices = service_finder_get_bookingsms_services($maildata['id']);
			
			$smsreplacements = array($maildata['id'],$bookingdate,$maildata['start_time'],service_finder_get_booking_end_time($maildata['end_time'],$maildata['end_time_no_buffer']),$membername,service_finder_get_providername_with_link($providerInfo->wp_user_id),$providerInfo->email,service_finder_get_contact_info($providerInfo->phone,$providerInfo->mobile),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country,$smsservices,ucfirst($payent_mode),service_finder_money_format($bookingamount),service_finder_money_format($adminfee),$customerInfo->description,service_finder_get_service_location($maildata['id']));
			
			$smsbody = str_replace($tokens,$smsreplacements,$smsbody);
			
			aonesms_send_sms_notifications($providerInfo->mobile,$smsbody);
			}
			}
			}else
			{
			
			if(service_finder_get_data($service_finder_options,'is-active-provider-new-booking-sms') == true)
			{
			$smsbody = service_finder_get_data($service_finder_options,'template-provider-new-booking-sms');
			if($smsbody != '')
			{
			
			$smsservices = service_finder_get_bookingsms_services($maildata['id']);
			
			$smsreplacements = array($maildata['id'],$bookingdate,$maildata['start_time'],service_finder_get_booking_end_time($maildata['end_time'],$maildata['end_time_no_buffer']),$membername,service_finder_get_providername_with_link($providerInfo->wp_user_id),$providerInfo->email,service_finder_get_contact_info($providerInfo->phone,$providerInfo->mobile),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country,$smsservices,ucfirst($payent_mode),service_finder_money_format($bookingamount),service_finder_money_format($adminfee),$customerInfo->description);
			
			$smsbody = str_replace($tokens,$smsreplacements,$smsbody);
			
			aonesms_send_sms_notifications($providerInfo->mobile,$smsbody);
			}
			}
			}
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
	/*Send Booking mail to customer*/
	public function service_finder_SendBookingMailToCustomer($maildata = '',$invoiceid = '',$adminfee = '0.0'){
		global $service_finder_options, $service_finder_Tables, $wpdb;
		$providerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$maildata['provider_id']));
		$customerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `id` = %d',$maildata['booking_customer_id']));
		
		$bookingpayment_mode = (!empty($maildata['type'])) ? $maildata['type'] : '';
		
		$payent_mode = ($bookingpayment_mode != '') ? $bookingpayment_mode : 'free';
		
		$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? $service_finder_options['pay_booking_amount_to'] : '';
		
		if($payent_mode == 'wired'){
			if($pay_booking_amount_to == 'admin'){
				$wiretransfermailinstructions = (!empty($service_finder_options['wire-transfer-mail-instructions'])) ? $service_finder_options['wire-transfer-mail-instructions'] : '';
				$message = $wiretransfermailinstructions;
			}elseif($pay_booking_amount_to == 'provider'){
				$settings = service_finder_getProviderSettings($maildata['provider_id']);
				$wired_instructions = (!empty($settings['wired_instructions'])) ? $settings['wired_instructions'] : '';
				$message = $wired_instructions;
			}else{
				$message = 'Use following invoice ID When transfer amount in bank.';
			}
		$message .= 'Invoice ID:'.$invoiceid;
		}else{
		$message = '';
		}
		
		if($maildata['jobid'] > 0){
		$message .= 'Job ID #: '.$maildata['jobid'];
		}
		
		if($maildata['quoteid'] > 0){
		$message .= 'Quote ID #: '.$maildata['quoteid'];
		}

		if(!empty($service_finder_options['booking-to-customer'])){
			$message .= $service_finder_options['booking-to-customer'];
		}else{
			$message .= '
<h4>Booking Details</h4>
Booking REF ID #: %BOOKINGREFID%

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

Service Location: %SERVICELOCATION%

Description: %SHORTDESCRIPTION%

<h4>Payment Details</h4>
Pay Via: %PAYMENTMETHOD%
				
				Amount: %AMOUNT%
				
				Admin Fee: %ADMINFEE%';
		}
		
			$tokens = array('%BOOKINGREFID%','%DATE%','%STARTTIME%','%ENDTIME%','%MEMBERNAME%','%PROVIDERNAME%','%PROVIDEREMAIL%','%PROVIDERPHONE%','%CUSTOMERNAME%','%CUSTOMEREMAIL%','%CUSTOMERPHONE%','%CUSTOMERPHONE2%','%ADDRESS%','%APT%','%CITY%','%STATE%','%ZIPCODE%','%COUNTRY%','%SERVICES%','%PAYMENTMETHOD%','%AMOUNT%','%ADMINFEE%','%SHORTDESCRIPTION%','%SERVICELOCATION%');
			
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
			
			if($maildata['multi_date'] == 'yes')
			{
				$bookingdate = service_finder_date_format($maildata['created']);
			}else{
				$bookingdate = service_finder_date_format($maildata['date']);
			}
			
			$replacements = array($maildata['id'],$bookingdate,$maildata['start_time'],service_finder_get_booking_end_time($maildata['end_time'],$maildata['end_time_no_buffer']),$membername,service_finder_get_providername_with_link($providerInfo->wp_user_id),$providerInfo->email,service_finder_get_contact_info($providerInfo->phone,$providerInfo->mobile),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country,$services,ucfirst($payent_mode),service_finder_money_format($maildata['total']),service_finder_money_format($adminfee),$customerInfo->description,service_finder_get_service_location($maildata['id']));
			$msg_body = str_replace($tokens,$replacements,$message);

			if($service_finder_options['booking-to-customer-subject'] != ""){
				$msg_subject = $service_finder_options['booking-to-customer-subject'];
			}else{
				$msg_subject = esc_html__('Booking Notification', 'service-finder');
			}
			
			if(class_exists('aonesms'))
			{
			
			if(service_finder_get_data($service_finder_options,'is-active-customer-new-booking-sms') == true)
			{
			$smsbody = service_finder_get_data($service_finder_options,'template-customer-new-booking-sms');
			if($smsbody != '')
			{
			$smsservices = service_finder_get_bookingsms_services($maildata['id']);
			
			$smsreplacements = array($maildata['id'],$bookingdate,$maildata['start_time'],service_finder_get_booking_end_time($maildata['end_time'],$maildata['end_time_no_buffer']),$membername,service_finder_get_providername_with_link($providerInfo->wp_user_id),$providerInfo->email,service_finder_get_contact_info($providerInfo->phone,$providerInfo->mobile),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country,$smsservices,ucfirst($payent_mode),service_finder_money_format($maildata['total']),service_finder_money_format($adminfee),$customerInfo->description);
			
			$smsbody = str_replace($tokens,$smsreplacements,$smsbody);
			
			aonesms_send_sms_notifications($customerInfo->phone,$smsbody);
			}
			}
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
	/*Send Booking mail to admin*/
	public function service_finder_SendBookingMailToAdmin($maildata = '',$invoiceid = '',$adminfee = '0.0'){
		global $service_finder_options, $wpdb, $service_finder_Tables;
		$providerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$maildata['provider_id']));
		$customerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `id` = %d',$maildata['booking_customer_id']));
		
		$bookingpayment_mode = (!empty($maildata['type'])) ? $maildata['type'] : '';
		
		$payent_mode = ($bookingpayment_mode != '') ? $bookingpayment_mode : 'free';
		$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? $service_finder_options['pay_booking_amount_to'] : '';
		if($payent_mode == 'wired' && $pay_booking_amount_to == 'admin'){
		$message = 'Invoice ID:'.$invoiceid;
		}else{
		$message = '';
		}
		
		if($maildata['jobid'] > 0){
		$message .= 'Job ID #: '.$maildata['jobid'];
		}
		
		if($maildata['quoteid'] > 0){
		$message .= 'Quote ID #: '.$maildata['quoteid'];
		}

		if(!empty($service_finder_options['booking-to-admin'])){
			$message .= $service_finder_options['booking-to-admin'];
		}else{
			$message .= '
<h4>Booking Details</h4>
Booking REF ID #: %BOOKINGREFID%

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

Service Location: %SERVICELOCATION%

Description: %SHORTDESCRIPTION%

<h4>Payment Details</h4>
Pay Via: %PAYMENTMETHOD%
				
				Amount: %AMOUNT%
				
				Admin Fee: %ADMINFEE%';
		}
			
			$tokens = array('%BOOKINGREFID%','%DATE%','%STARTTIME%','%ENDTIME%','%MEMBERNAME%','%PROVIDERNAME%','%PROVIDEREMAIL%','%PROVIDERPHONE%','%CUSTOMERNAME%','%CUSTOMEREMAIL%','%CUSTOMERPHONE%','%CUSTOMERPHONE2%','%ADDRESS%','%APT%','%CITY%','%STATE%','%ZIPCODE%','%COUNTRY%','%SERVICES%','%PAYMENTMETHOD%','%AMOUNT%','%ADMINFEE%','%SHORTDESCRIPTION%','%SERVICELOCATION%');
			
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
			
			if($maildata['multi_date'] == 'yes')
			{
				$bookingdate = service_finder_date_format($maildata['created']);
			}else{
				$bookingdate = service_finder_date_format($maildata['date']);
			}
			
			$replacements = array($maildata['id'],$bookingdate,$maildata['start_time'],service_finder_get_booking_end_time($maildata['end_time'],$maildata['end_time_no_buffer']),$membername,service_finder_get_providername_with_link($providerInfo->wp_user_id),$providerInfo->email,service_finder_get_contact_info($providerInfo->phone,$providerInfo->mobile),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country,$services,ucfirst($payent_mode),service_finder_money_format($bookingamount),service_finder_money_format($adminfee),$customerInfo->description,service_finder_get_service_location($maildata['id']));
			$msg_body = str_replace($tokens,$replacements,$message);
			
			if($service_finder_options['booking-to-admin-subject'] != ""){
				$msg_subject = $service_finder_options['booking-to-admin-subject'];
			}else{
				$msg_subject = esc_html__('Booking Notification', 'service-finder');
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
	
	/*Get Calendar TimeSlot*/
	public function service_finder_getBookingTimeSlot($data = ''){
	
		global $wpdb, $service_finder_Tables, $service_finder_Params, $service_finder_options;
		
		$time_format = (!empty($service_finder_options['time-format'])) ? $service_finder_options['time-format'] : '';
		
		$memberid = (!empty($data['member_id'])) ? esc_attr($data['member_id']) : 0;
		$serviceid = (!empty($data['serviceid'])) ? $data['serviceid'] : 0;
		$shours = (!empty($data['totalhours'])) ? $data['totalhours'] : 0;
		
		$wpdb->show_errors();
		$dayname = date('l', strtotime( $data['seldate']));
		
		$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->timeslots.' AS timeslots WHERE (SELECT COUNT(*) FROM '.$service_finder_Tables->unavailability.' AS unavl WHERE `unavl`.`date` = "%s" AND  `unavl`.availability_method = "timeslots" AND `unavl`.`start_time` = `timeslots`.`start_time` AND `unavl`.`end_time` = `timeslots`.`end_time`) = 0 AND `timeslots`.`provider_id` = %d AND `timeslots`.`day` = "%s"',$data['seldate'],$data['provider_id'],strtolower($dayname)));
		
		$member_timeslots = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->member_timeslots.' where day = %s and provider_id = %d AND `member_id` = %d',strtolower($dayname),$data['provider_id'],$memberid));
			
		if(!empty($member_timeslots)){
			foreach($member_timeslots as $member_timeslot){
				$starttime[] = $member_timeslot->start_time;
				$endtime[] = $member_timeslot->end_time;
			}
		}
		
		if($memberid > 0 && empty($member_timeslots))
		{
			$res = '<div class="notavail">'.esc_html__('There are no time slot available.', 'service-finder').'</div>';
			return $res;
		}
		
		$res = '';
		if(!empty($results)){
			foreach($results as $slot){
			
			$slotstart = $slot->start_time;
			$slotend = $slot->end_time;
			
			if($serviceid > 0){
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
						$slotend = date('H:i:s', strtotime($slotstart." +".$totalhours." minutes"));
					}	
				}
			
				$paddingtime = service_finder_get_service_paddind_time($serviceid);
				$before_padding_time = $paddingtime['before_padding_time'];
				$after_padding_time = $paddingtime['after_padding_time'];
				
				if($before_padding_time > 0 || $after_padding_time > 0){
				if(!empty($slotstart)){
				$slotstart = date('H:i:s', strtotime($slotstart." -".$before_padding_time." minutes"));
				}
				if(!empty($slotend)){
				$slotend = date('H:i:s', strtotime($slotend." +".$after_padding_time." minutes"));
				}
				}
			}
			
			$totalbookings = $this->service_finder_get_availability( $data['seldate'],$slotstart,$slotend,$data['provider_id'],'',$serviceid);
			$totalmemberbookings = $this->service_finder_get_member_availability( $data['seldate'],$slotstart,$slotend,$data['provider_id'],'',$serviceid,$memberid);
			
			
			if(($memberid == 0 && ($slot->max_bookings > $totalbookings || $data['start_time'] == $slot->start_time)) || ($memberid > 0 && $totalmemberbookings < 1 && ($slot->max_bookings > $totalbookings || $data['start_time'] == $slot->start_time))) {	
			if(!empty($member_timeslots)){
			if(!empty($starttime) && !empty($endtime)){
				if(in_array($slot->start_time,$starttime) && in_array($slot->end_time,$endtime)){
					$qry = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE `date` = "%s" AND availability_method = "timeslots" AND start_time = "%s" AND end_time = "%s" AND provider_id = %d',$data['seldate'],$slot->start_time,$slot->end_time,$data['provider_id']));
			
					if(empty($qry)){
			$editbooking = (!empty($data['editbooking'])) ? $data['editbooking'] : '';
			if($editbooking == 'yes'){ 
				if($data['start_time'] == $slot->start_time){
					$active = 'class="active"';
				}else{
					$active = '';
				}
			}else{
				$active = '';
			}
			if($time_format){
				$showtime = $slot->start_time.'-'.$slot->end_time;
			}else{
				$showtime = date('h:i a',strtotime($slot->start_time)).'-'.date('h:i a',strtotime($slot->end_time));
			}
			
			$slottimestamp = strtotime($data['seldate'].' '.$slot->start_time);
			
			if($slottimestamp > current_time( 'timestamp' )){
			$res .= '<li '.$active.' data-source="'.esc_attr($slot->start_time).'-'.esc_attr($slot->end_time).'"><span>'.$showtime.'</span></li>';
			}else{
			$res .= '';
			}
			}
				}
			}	
			}else{
			$qry = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE `date` = "%s" AND availability_method = "timeslots" AND start_time = "%s" AND end_time = "%s" AND provider_id = %d',$data['seldate'],$slot->start_time,$slot->end_time,$data['provider_id']));
			
			if(empty($qry)){
			$editbooking = (!empty($data['editbooking'])) ? $data['editbooking'] : '';
			if($editbooking == 'yes'){ 
				if($data['start_time'] == $slot->start_time){
					$active = 'class="active"';
				}else{
					$active = '';
				}
			}else{
				$active = '';
			}
			if($time_format){
				$showtime = $slot->start_time.'-'.$slot->end_time;
			}else{
				$showtime = date('h:i a',strtotime($slot->start_time)).'-'.date('h:i a',strtotime($slot->end_time));
			}
			
			$slottimestamp = strtotime($data['seldate'].' '.$slot->start_time);
			
			if($slottimestamp > current_time( 'timestamp' )){
			$res .= '<li '.$active.' data-source="'.esc_attr($slot->start_time).'-'.esc_attr($slot->end_time).'"><span>'.$showtime.'</span></li>';
			}else{
			$res .= '';
			}
			}
			}
			}
			
			}
		}else{
			$res .= '<div class="notavail">'.esc_html__('There are no time slot available.', 'service-finder').'</div>';
		}
		
		if($res == ''){
			$res .= '<div class="notavail">'.esc_html__('There are no time slot available.', 'service-finder').'</div>';
		}
		
		return $res;
	}
	
	/*Get Calendar Start Time*/
	public function service_finder_getBookingStartTime($data = ''){
	
		global $wpdb, $service_finder_Tables, $service_finder_Params, $service_finder_options;
		
		$time_format = (!empty($service_finder_options['time-format'])) ? $service_finder_options['time-format'] : '';
		$res = '';
		$flag = 0;
		$dayname = date('l', strtotime( $data['seldate']));
		$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->starttime.' AS starttime WHERE `starttime`.`provider_id` = %d AND `starttime`.`day` = "%s"',$data['provider_id'],strtolower($dayname)));
		
		$editbooking = (!empty($data['editbooking'])) ? $data['editbooking'] : 'no';
		$bookingid = (!empty($data['bookingid'])) ? $data['bookingid'] : 0;
		$serviceid = (!empty($data['serviceid'])) ? $data['serviceid'] : 0;
		$memberid = (!empty($data['member_id'])) ? esc_attr($data['member_id']) : 0;
		
		$member_timings = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->member_starttimes.' where day = %s and provider_id = %d AND `member_id` = %d',strtolower($dayname),$data['provider_id'],$memberid));
		
		
		
		if(!empty($results)){
			foreach($results as $row){
				$tem = number_format($data['totalhours'], 2);
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
					$endtime = date('H:i:s', strtotime($row->start_time." +".$totalhours." minutes"));
					
					$slotstart = $row->start_time;
					
					if($serviceid > 0){
					$paddingtime = service_finder_get_service_paddind_time($serviceid);
					$before_padding_time = $paddingtime['before_padding_time'];
					$after_padding_time = $paddingtime['after_padding_time'];
					
					if($before_padding_time > 0 || $after_padding_time > 0){
					if(!empty($slotstart)){
					$slotstart = date('H:i:s', strtotime($slotstart." -".$before_padding_time." minutes"));
					}
					if(!empty($endtime)){
					$endtime = date('H:i:s', strtotime($endtime." +".$after_padding_time." minutes"));
					}
					}
					}
	
					$totalbookings = $this->service_finder_get_availability( $data['seldate'],$slotstart,$endtime,$data['provider_id'],$bookingid,$serviceid);
					$totalmemberbookings = $this->service_finder_get_member_availability( $data['seldate'],$slotstart,$endtime,$data['provider_id'],$bookingid,$serviceid,$memberid);
					
					$chkunavailability = $this->service_finder_get_chkunavailability( $data['seldate'],$slotstart,$data['provider_id']);
					
					if(($memberid == 0 && $row->max_bookings > $totalbookings && $chkunavailability == 0) || ($memberid > 0 && $totalmemberbookings < 1 && $row->max_bookings > $totalbookings && $chkunavailability == 0)) {		
					
						if($editbooking == 'yes'){
							$databookingid = 'data-bookingid="'.$bookingid.'"';
							if($data['start_time'] == $row->start_time){
								$active = 'class="active"';
							}else{
								$active = '';
							}
						}else{
							$active = '';
							$databookingid = '';
						}
						
						
						if($time_format){
							$showtime = date('H:i',strtotime($row->start_time));
						}else{
							$showtime = date('h:i a',strtotime($row->start_time));
						}
						
						$slottimestamp = strtotime($data['seldate'].' '.$row->start_time);
						$slotendtimestamp = strtotime($data['seldate'].' '.$endtime);
						if($slottimestamp > current_time( 'timestamp' )){
						if(!empty($member_timings)){
						$memberstarttime = strtotime($data['seldate'].' '.$member_timings->start_time);
						$memberendtime = strtotime($data['seldate'].' '.$member_timings->end_time);
						$memberbreakstarttime = strtotime($data['seldate'].' '.$member_timings->break_start_time);
						$memberbreakendtime = strtotime($data['seldate'].' '.$member_timings->break_end_time);
						
						if($memberstarttime <= $slottimestamp && $slottimestamp < $memberendtime && $memberstarttime < $slotendtimestamp && $slotendtimestamp <= $memberendtime){
						
						if(($memberstarttime <= $slottimestamp && $slottimestamp < $memberbreakstarttime && $memberstarttime < $slotendtimestamp && $slotendtimestamp <=$memberbreakstarttime) || ($memberbreakendtime <= $slottimestamp && $slottimestamp < $memberendtime && $memberbreakendtime < $slotendtimestamp && $slotendtimestamp <=$memberendtime)){
						$flag = 1;
						$res .= '<li '.$active.' '.$databookingid.' data-source="'.esc_attr($row->start_time).'-'.esc_attr($endtime).'"><span>'.$showtime.'</span></li>';
						}	
						
						}
						
						
						}else{
						$flag = 1;
						$res .= '<li '.$active.' '.$databookingid.' data-source="'.esc_attr($row->start_time).'-'.esc_attr($endtime).'"><span>'.$showtime.'</span></li>';
						}
						
						}else{
						$res .= '';
						}
					}
				}else{
					$totalbookings = $this->service_finder_get_availability_nohours( $data['seldate'],$row->start_time,$data['provider_id'],$bookingid,$serviceid);
					
					$totalmemberbookings = $this->service_finder_get_member_availability_nohours( $data['seldate'],$row->start_time,$data['provider_id'],$bookingid,$serviceid,$memberid);
					
					$chkunavailability = $this->service_finder_get_chkunavailability( $data['seldate'],$row->start_time,$data['provider_id']);
					
					if(($memberid == 0 && $row->max_bookings > $totalbookings && $chkunavailability == 0) || ($memberid > 0 && $totalmemberbookings < 1 && $row->max_bookings > $totalbookings && $chkunavailability == 0)) {
						
						if($editbooking == 'yes'){
							$databookingid = 'data-bookingid="'.$bookingid.'"';
							if($data['start_time'] == $row->start_time){
								$active = 'class="active"';
							}else{
								$active = '';
							}
						}else{
							$active = '';
							$databookingid = '';
						}
						
						if($time_format){
							$showtime = date('H:i',strtotime($row->start_time));
						}else{
							$showtime = date('h:i a',strtotime($row->start_time));
						}
						
						$slottimestamp = strtotime($data['seldate'].' '.$row->start_time);
						if($slottimestamp > current_time( 'timestamp' )){
							
						if(!empty($member_timings)){
						$memberstarttime = strtotime($data['seldate'].' '.$member_timings->start_time);
						$memberendtime = strtotime($data['seldate'].' '.$member_timings->end_time);
						$memberbreakstarttime = strtotime($data['seldate'].' '.$member_timings->break_start_time);
						$memberbreakendtime = strtotime($data['seldate'].' '.$member_timings->break_end_time);
						
						if($memberstarttime <= $slottimestamp && $slottimestamp <= $memberendtime){
						
						if(($memberstarttime <= $slottimestamp && $slottimestamp <= $memberbreakstarttime) || ($memberbreakendtime <= $slottimestamp && $slottimestamp <= $memberendtime)){
						$flag = 1;
						$res .= '<li '.$active.' '.$databookingid.' data-source="'.esc_attr($row->start_time).'"><span>'.$showtime.'</span></li>';
						}	
						
						}
						
						}else{
						$flag = 1;
						$res .= '<li '.$active.' '.$databookingid.' data-source="'.esc_attr($row->start_time).'"><span>'.$showtime.'</span></li>';
						}	
							
							
						}else{
							$res .= '';
						}
						
						
					}
				}
			}
		}
		
		if($flag == 0){
			$res = '<div class="notavail">'.esc_html__('There are no time slot available.', 'service-finder').'</div>';
		}
		
		return $res;
	}
	
	/*Get Availability*/
	public function service_finder_get_availability($date,$starttime,$endtime,$provider_id,$bookingid = 0,$serviceid = 0){

		global $wpdb,$service_finder_Tables;

		$servicedata = service_finder_get_service_by_id($serviceid);
	
		$servicetype = (!empty($servicedata)) ? $servicedata->cost_type : '';

		if($bookingid != '' && $bookingid > 0){
		if($servicetype == 'days'){
		$result = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings LEFT JOIN '.$service_finder_Tables->booked_services.' as bookedservices on bookings.id = bookedservices.booking_id WHERE `bookings`.`id` != %d AND `bookings`.`status` != "Cancel" AND `bookings`.`provider_id` = %d AND `bookedservices`.`service_id` = %d AND ((`bookings`.`multi_date` = "yes" AND `bookedservices`.`date` = "%s" AND (`bookedservices`.`start_time` > "%s" AND `bookedservices`.`start_time` < "%s" OR (`bookedservices`.`end_time` > "%s" AND `bookedservices`.`end_time` < "%s") OR (`bookedservices`.`start_time` < "%s" AND `bookedservices`.`end_time` > "%s") OR (`bookedservices`.`start_time` = "%s" OR `bookedservices`.`end_time` = "%s") )) OR (`bookings`.`multi_date` = "no" AND `bookings`.`date` = "%s" AND (`bookings`.`start_time` > "%s" AND `bookings`.`start_time` < "%s" OR (`bookings`.`end_time` > "%s" AND `bookings`.`end_time` < "%s") OR (`bookings`.`start_time` < "%s" AND `bookings`.`end_time` > "%s") OR (`bookings`.`start_time` = "%s" OR `bookings`.`end_time` = "%s") )))',$bookingid,$provider_id,$serviceid,$date,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime,$date,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime));
		}else{	
		$result = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings LEFT JOIN '.$service_finder_Tables->booked_services.' as bookedservices on bookings.id = bookedservices.booking_id WHERE `bookings`.`id` != %d AND `bookings`.`status` != "Cancel" AND `bookings`.`provider_id` = %d AND ((`bookings`.`multi_date` = "yes" AND `bookedservices`.`date` = "%s" AND (`bookedservices`.`start_time` > "%s" AND `bookedservices`.`start_time` < "%s" OR (`bookedservices`.`end_time` > "%s" AND `bookedservices`.`end_time` < "%s") OR (`bookedservices`.`start_time` < "%s" AND `bookedservices`.`end_time` > "%s") OR (`bookedservices`.`start_time` = "%s" OR `bookedservices`.`end_time` = "%s") )) OR (`bookings`.`multi_date` = "no" AND `bookings`.`date` = "%s" AND (`bookings`.`start_time` > "%s" AND `bookings`.`start_time` < "%s" OR (`bookings`.`end_time` > "%s" AND `bookings`.`end_time` < "%s") OR (`bookings`.`start_time` < "%s" AND `bookings`.`end_time` > "%s") OR (`bookings`.`start_time` = "%s" OR `bookings`.`end_time` = "%s") )))',$bookingid,$provider_id,$date,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime,$date,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime));
		}

		}else{

		if($servicetype == 'days'){
		$result = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings LEFT JOIN '.$service_finder_Tables->booked_services.' as bookedservices on bookings.id = bookedservices.booking_id WHERE `bookings`.`status` != "Cancel" AND `bookings`.`provider_id` = %d AND `bookedservices`.`service_id` = %d AND ((`bookings`.`multi_date` = "yes" AND `bookedservices`.`date` = "%s" AND (`bookedservices`.`start_time` > "%s" AND `bookedservices`.`start_time` < "%s" OR (`bookedservices`.`end_time` > "%s" AND `bookedservices`.`end_time` < "%s") OR (`bookedservices`.`start_time` < "%s" AND `bookedservices`.`end_time` > "%s") OR (`bookedservices`.`start_time` = "%s" OR `bookedservices`.`end_time` = "%s") )) OR (`bookings`.`multi_date` = "no" AND `bookings`.`date` = "%s" AND (`bookings`.`start_time` > "%s" AND `bookings`.`start_time` < "%s" OR (`bookings`.`end_time` > "%s" AND `bookings`.`end_time` < "%s") OR (`bookings`.`start_time` < "%s" AND `bookings`.`end_time` > "%s") OR (`bookings`.`start_time` = "%s" OR `bookings`.`end_time` = "%s") )))',$provider_id,$serviceid,$date,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime,$date,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime));
		}else{	

		$result = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings LEFT JOIN '.$service_finder_Tables->booked_services.' as bookedservices on bookings.id = bookedservices.booking_id WHERE `bookings`.`status` != "Cancel" AND `bookings`.`provider_id` = %d AND ((`bookings`.`multi_date` = "yes" AND `bookedservices`.`date` = "%s" AND (`bookedservices`.`start_time` > "%s" AND `bookedservices`.`start_time` < "%s" OR (`bookedservices`.`end_time` > "%s" AND `bookedservices`.`end_time` < "%s") OR (`bookedservices`.`start_time` < "%s" AND `bookedservices`.`end_time` > "%s") OR (`bookedservices`.`start_time` = "%s" OR `bookedservices`.`end_time` = "%s") )) OR (`bookings`.`multi_date` = "no" AND `bookings`.`date` = "%s" AND (`bookings`.`start_time` > "%s" AND `bookings`.`start_time` < "%s" OR (`bookings`.`end_time` > "%s" AND `bookings`.`end_time` < "%s") OR (`bookings`.`start_time` < "%s" AND `bookings`.`end_time` > "%s") OR (`bookings`.`start_time` = "%s" OR `bookings`.`end_time` = "%s") )))',$provider_id,$date,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime,$date,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime));		
		
		}

		}

		

		$totalrows = count($result);

		//echo $wpdb->last_query;//lists only single query

		return $totalrows;

		

	}
	
	/*Get Availability for without hours*/
	public function service_finder_get_availability_nohours($date,$starttime,$provider_id,$bookingid = 0,$serviceid = 0){
		global $wpdb,$service_finder_Tables;
		
		if($bookingid != '' && $bookingid > 0){
		if($serviceid > 0){
		$result = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings LEFT JOIN '.$service_finder_Tables->booked_services.' as bookedservices on bookings.id = bookedservices.booking_id WHERE `bookings`.`id` != %d AND `bookings`.`status` != "Cancel" AND `bookings`.`provider_id` = %d AND `bookedservices`.`service_id` = %d AND ((`bookings`.`multi_date` = "no" AND `bookings`.`date` = "%s" AND `bookings`.`start_time` = "%s") OR (`bookings`.`multi_date` = "yes" AND `bookedservices`.`date` = "%s" AND `bookedservices`.`start_time` = "%s"))',$bookingid,$provider_id,$serviceid,$date,$starttime,$date,$starttime));
		}else{
		$result = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings LEFT JOIN '.$service_finder_Tables->booked_services.' as bookedservices on bookings.id = bookedservices.booking_id WHERE `bookings`.`id` != %d AND `bookings`.`status` != "Cancel" AND `bookings`.`provider_id` = %d AND ((`bookings`.`multi_date` = "no" AND `bookings`.`date` = "%s" AND `bookings`.`start_time` = "%s") OR (`bookings`.`multi_date` = "yes" AND `bookedservices`.`date` = "%s" AND `bookedservices`.`start_time` = "%s"))',$bookingid,$provider_id,$date,$starttime,$date,$starttime));
		}
		}else{
		if($serviceid > 0){
		$result = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings LEFT JOIN '.$service_finder_Tables->booked_services.' as bookedservices on bookings.id = bookedservices.booking_id WHERE `bookings`.`status` != "Cancel" AND `bookings`.`provider_id` = %d AND `bookedservices`.`service_id` = %d AND ((`bookings`.`multi_date` = "no" AND `bookings`.`date` = "%s" AND `bookings`.`start_time` = "%s") OR (`bookings`.`multi_date` = "yes" AND `bookedservices`.`date` = "%s" AND `bookedservices`.`start_time` = "%s"))',$provider_id,$serviceid,$date,$starttime,$date,$starttime));
		}else{
		$result = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings LEFT JOIN '.$service_finder_Tables->booked_services.' as bookedservices on bookings.id = bookedservices.booking_id WHERE `bookings`.`status` != "Cancel" AND `bookings`.`provider_id` = %d AND ((`bookings`.`multi_date` = "no" AND `bookings`.`date` = "%s" AND `bookings`.`start_time` = "%s") OR (`bookings`.`multi_date` = "yes" AND `bookedservices`.`date` = "%s" AND `bookedservices`.`start_time` = "%s"))',$provider_id,$date,$starttime,$date,$starttime));
		}
		}
		
		$totalrows = count($result);
		//echo $wpdb->last_query;//lists only single query
		return $totalrows;
		
	}
	
	/*Get Member Availability*/
	public function service_finder_get_member_availability($date,$starttime,$endtime,$provider_id,$bookingid = 0,$serviceid = 0,$memberid = 0){

		global $wpdb,$service_finder_Tables;

		$servicedata = service_finder_get_service_by_id($serviceid);
	
		$servicetype = (!empty($servicedata)) ? $servicedata->cost_type : '';

		if($bookingid != '' && $bookingid > 0){
		if($servicetype == 'days'){
		$result = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings LEFT JOIN '.$service_finder_Tables->booked_services.' as bookedservices on bookings.id = bookedservices.booking_id WHERE `bookings`.`id` != %d AND `bookings`.`status` != "Cancel" AND `bookings`.`provider_id` = %d AND `bookedservices`.`service_id` = %d AND ((`bookings`.`multi_date` = "yes" AND `bookedservices`.`date` = "%s" AND `bookedservices`.`member_id` = "%d" AND (`bookedservices`.`start_time` > "%s" AND `bookedservices`.`start_time` < "%s" OR (`bookedservices`.`end_time` > "%s" AND `bookedservices`.`end_time` < "%s") OR (`bookedservices`.`start_time` < "%s" AND `bookedservices`.`end_time` > "%s") OR (`bookedservices`.`start_time` = "%s" OR `bookedservices`.`end_time` = "%s") )) OR (`bookings`.`multi_date` = "no" AND `bookings`.`date` = "%s" AND `bookings`.`member_id` = "%d" AND (`bookings`.`start_time` > "%s" AND `bookings`.`start_time` < "%s" OR (`bookings`.`end_time` > "%s" AND `bookings`.`end_time` < "%s") OR (`bookings`.`start_time` < "%s" AND `bookings`.`end_time` > "%s") OR (`bookings`.`start_time` = "%s" OR `bookings`.`end_time` = "%s") )))',$bookingid,$provider_id,$serviceid,$date,$memberid,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime,$date,$memberid,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime,$memberid));
		}else{	
		$result = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings LEFT JOIN '.$service_finder_Tables->booked_services.' as bookedservices on bookings.id = bookedservices.booking_id WHERE `bookings`.`id` != %d AND `bookings`.`status` != "Cancel" AND `bookings`.`provider_id` = %d AND ((`bookings`.`multi_date` = "yes" AND `bookedservices`.`date` = "%s" AND `bookedservices`.`member_id` = "%d" AND (`bookedservices`.`start_time` > "%s" AND `bookedservices`.`start_time` < "%s" OR (`bookedservices`.`end_time` > "%s" AND `bookedservices`.`end_time` < "%s") OR (`bookedservices`.`start_time` < "%s" AND `bookedservices`.`end_time` > "%s") OR (`bookedservices`.`start_time` = "%s" OR `bookedservices`.`end_time` = "%s") )) OR (`bookings`.`multi_date` = "no" AND `bookings`.`date` = "%s" AND `bookings`.`member_id` = "%d" AND (`bookings`.`start_time` > "%s" AND `bookings`.`start_time` < "%s" OR (`bookings`.`end_time` > "%s" AND `bookings`.`end_time` < "%s") OR (`bookings`.`start_time` < "%s" AND `bookings`.`end_time` > "%s") OR (`bookings`.`start_time` = "%s" OR `bookings`.`end_time` = "%s") )))',$bookingid,$provider_id,$date,$memberid,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime,$date,$memberid,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime));
		}

		}else{

		if($servicetype == 'days'){
		$result = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings LEFT JOIN '.$service_finder_Tables->booked_services.' as bookedservices on bookings.id = bookedservices.booking_id WHERE `bookings`.`status` != "Cancel" AND `bookings`.`provider_id` = %d AND `bookedservices`.`service_id` = %d AND ((`bookings`.`multi_date` = "yes" AND `bookedservices`.`date` = "%s" AND `bookedservices`.`member_id` = "%d" AND (`bookedservices`.`start_time` > "%s" AND `bookedservices`.`start_time` < "%s" OR (`bookedservices`.`end_time` > "%s" AND `bookedservices`.`end_time` < "%s") OR (`bookedservices`.`start_time` < "%s" AND `bookedservices`.`end_time` > "%s") OR (`bookedservices`.`start_time` = "%s" OR `bookedservices`.`end_time` = "%s") )) OR (`bookings`.`multi_date` = "no" AND `bookings`.`date` = "%s" AND `bookings`.`member_id` = "%d" AND (`bookings`.`start_time` > "%s" AND `bookings`.`start_time` < "%s" OR (`bookings`.`end_time` > "%s" AND `bookings`.`end_time` < "%s") OR (`bookings`.`start_time` < "%s" AND `bookings`.`end_time` > "%s") OR (`bookings`.`start_time` = "%s" OR `bookings`.`end_time` = "%s") )))',$provider_id,$serviceid,$date,$memberid,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime,$date,$memberid,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime));
		}else{	

		$result = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings LEFT JOIN '.$service_finder_Tables->booked_services.' as bookedservices on bookings.id = bookedservices.booking_id WHERE `bookings`.`status` != "Cancel" AND `bookings`.`provider_id` = %d AND ((`bookings`.`multi_date` = "yes" AND `bookedservices`.`date` = "%s" AND `bookedservices`.`member_id` = "%d" AND (`bookedservices`.`start_time` > "%s" AND `bookedservices`.`start_time` < "%s" OR (`bookedservices`.`end_time` > "%s" AND `bookedservices`.`end_time` < "%s") OR (`bookedservices`.`start_time` < "%s" AND `bookedservices`.`end_time` > "%s") OR (`bookedservices`.`start_time` = "%s" OR `bookedservices`.`end_time` = "%s") )) OR (`bookings`.`multi_date` = "no" AND `bookings`.`date` = "%s" AND `bookings`.`member_id` = "%d" AND (`bookings`.`start_time` > "%s" AND `bookings`.`start_time` < "%s" OR (`bookings`.`end_time` > "%s" AND `bookings`.`end_time` < "%s") OR (`bookings`.`start_time` < "%s" AND `bookings`.`end_time` > "%s") OR (`bookings`.`start_time` = "%s" OR `bookings`.`end_time` = "%s") )))',$provider_id,$date,$memberid,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime,$date,$memberid,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime,$starttime,$endtime));		
		}

		}

		

		$totalrows = count($result);

		//echo $wpdb->last_query;//lists only single query

		return $totalrows;

		

	}
	
	/*Get Member Availability for without hours*/
	public function service_finder_get_member_availability_nohours($date,$starttime,$provider_id,$bookingid = 0,$serviceid = 0,$memberid = 0){

		global $wpdb,$service_finder_Tables;

		$servicedata = service_finder_get_service_by_id($serviceid);
	
		$servicetype = (!empty($servicedata)) ? $servicedata->cost_type : '';

		if($bookingid != '' && $bookingid > 0){
		if($servicetype == 'days'){
		$result = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings LEFT JOIN '.$service_finder_Tables->booked_services.' as bookedservices on bookings.id = bookedservices.booking_id WHERE `bookings`.`id` != %d AND `bookings`.`status` != "Cancel" AND `bookings`.`provider_id` = %d AND `bookedservices`.`service_id` = %d AND ((`bookings`.`multi_date` = "yes" AND `bookedservices`.`date` = "%s" AND `bookedservices`.`member_id` = "%d" AND `bookedservices`.`start_time` = "%s") OR (`bookings`.`multi_date` = "no" AND `bookings`.`date` = "%s" AND `bookings`.`member_id` = "%d" AND `bookings`.`start_time` = "%s"))',$bookingid,$provider_id,$serviceid,$date,$memberid,$starttime,$date,$memberid,$starttime));
		}else{	
		$result = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings LEFT JOIN '.$service_finder_Tables->booked_services.' as bookedservices on bookings.id = bookedservices.booking_id WHERE `bookings`.`id` != %d AND `bookings`.`status` != "Cancel" AND `bookings`.`provider_id` = %d AND ((`bookings`.`multi_date` = "yes" AND `bookedservices`.`date` = "%s" AND `bookedservices`.`member_id` = "%d" AND `bookedservices`.`start_time` = "%s") OR (`bookings`.`multi_date` = "no" AND `bookings`.`date` = "%s" AND `bookings`.`member_id` = "%d" AND `bookings`.`start_time` = "%s"))',$bookingid,$provider_id,$date,$memberid,$starttime,$date,$memberid,$starttime));
		}

		}else{

		if($servicetype == 'days'){
		$result = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings LEFT JOIN '.$service_finder_Tables->booked_services.' as bookedservices on bookings.id = bookedservices.booking_id WHERE `bookings`.`status` != "Cancel" AND `bookings`.`provider_id` = %d AND `bookedservices`.`service_id` = %d AND ((`bookings`.`multi_date` = "yes" AND `bookedservices`.`date` = "%s" AND `bookedservices`.`member_id` = "%d" AND `bookedservices`.`start_time` = "%s") OR (`bookings`.`multi_date` = "no" AND `bookings`.`date` = "%s" AND `bookings`.`member_id` = "%d" AND `bookings`.`start_time` = "%s"))',$provider_id,$serviceid,$date,$memberid,$starttime,$date,$memberid,$starttime));
		}else{	

		$result = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' AS bookings LEFT JOIN '.$service_finder_Tables->booked_services.' as bookedservices on bookings.id = bookedservices.booking_id WHERE `bookings`.`status` != "Cancel" AND `bookings`.`provider_id` = %d AND ((`bookings`.`multi_date` = "yes" AND `bookedservices`.`date` = "%s" AND `bookedservices`.`member_id` = "%d" AND `bookedservices`.`start_time` = "%s") OR (`bookings`.`multi_date` = "no" AND `bookings`.`date` = "%s" AND `bookings`.`member_id` = "%d" AND `bookings`.`start_time` = "%s"))',$provider_id,$date,$memberid,$starttime,$date,$memberid,$starttime));		
		
		}

		}

		

		$totalrows = count($result);

		//echo $wpdb->last_query;//lists only single query

		return $totalrows;

		

	}
	
	/*Check UNAvailability*/
	public function service_finder_get_chkunavailability($date,$starttime,$provider_id){
		global $wpdb,$service_finder_Tables;
		
		$result = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' AS unavl WHERE `unavl`.`date` = "%s" AND availability_method = "starttime" AND `unavl`.`single_start_time` = "%s" AND `unavl`.`provider_id` = %d',$date,$starttime,$provider_id));
		
		$totalrows = count($result);
		//echo $wpdb->last_query;//lists only single query
		return $totalrows;
		
	}
	
	
	
	/*Inner Login*/
	public function service_finder_innerLogin($data = ''){
	
		global $wpdb, $service_finder_Tables, $service_finder_Params, $user;

		$creds = array();
			$creds['user_login'] = esc_attr($data['username']);
			$creds['user_password'] = esc_attr($data['password']);
			$creds['remember'] = true;
			$user = wp_signon( $creds, false );
			if(is_wp_error($user)) {
				$error = array(
						'status' => 'error',
						'err_message' => esc_html__('Couldn&rsquo;t Login. Please try again', 'service-finder'),
						);
						
				echo json_encode($error);
				
			} else {
			$fname = get_user_meta($user->ID, 'first_name', true);
			$lname = get_user_meta($user->ID, 'last_name', true);
			$udata = $user->data;
			$uemail = $udata->user_email;
			
			if(service_finder_getUserRole($user->ID) == 'Provider'){
			
				/* Get Provider info */
				$sedateProvider = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' where wp_user_id = %d',$user->ID));
				
				$userinfo = array(
							$currUser,
							'userid' => $user->ID,
							'firstname' => $fname,
							'lastname' => $lname,
							'email' => $uemail,
							'provider_id' => $sedateProvider->id,
							'country' => $sedateProvider->country,
							'city' => $sedateProvider->city,
							'phone' => $sedateProvider->phone,
							'category' => get_user_meta($user->ID,'primary_category',true),
							'min_cost' => $sedateProvider->min_cost,
							);
			}else{
				
				/* Get Customer info */
				$sedateCustomer = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers_data.' where wp_user_id = %d',$user->ID));
				
				$userinfo = array(
							$currUser,
							'userid' => $user->ID,
							'firstname' => $fname,
							'lastname' => $lname,
							'email' => $uemail,
							'phone' => $sedateCustomer->phone,
							'phone2' => $sedateCustomer->phone2,
							'address' => $sedateCustomer->address,
							'apt' => $sedateCustomer->apt,
							'city' => $sedateCustomer->city,
							'state' => $sedateCustomer->state,
							'zipcode' => $sedateCustomer->zipcode,
							);
			}	
			
				$success = array(
						'status' => 'success',
						'suc_message' => esc_html__('Login Successful', 'service-finder'),
						'userinfo' => $userinfo,
						);
				echo json_encode($success);
			}
		
	}
	
	/*Add to Favorite*/
	public function service_finder_addtofavorite($data = ''){
	global $wpdb, $service_finder_Tables;
	
			$data = array(
				'user_id' => $data['userid'],
				'provider_id' => $data['providerid'],
				'favorite' => 'yes',
				);

			$wpdb->insert($service_finder_Tables->favorites,wp_unslash($data));
			
			$favorite_id = $wpdb->insert_id;
			
			if ( ! $favorite_id ) {
				$adminemail = get_option( 'admin_email' );
				$allowedhtml = array(
					'a' => array(
						'href' => array(),
						'title' => array()
					),
				);
				$error = array(
						'status' => 'error',
						'err_message' => sprintf( wp_kses(esc_html__('Couldn&#8217;t added to favorite... please contact the <a href="mailto:%s">Administrator</a> !', 'service-finder'),$allowedhtml), $adminemail )
						);
				echo json_encode($error);
			}else{
				$success = array(
						'status' => 'success',
						'suc_message' => esc_html__('Added to favorite successfully.', 'service-finder'),
						'favoriteid' => $favorite_id,
						);
				echo json_encode($success);
			}
	}
	
	/*Remove From Favorite*/
	public function service_finder_removeFromFavorite($data = ''){
	global $wpdb, $service_finder_Tables;
	
			$res = $wpdb->query($wpdb->prepare('DELETE FROM `'.$service_finder_Tables->favorites.'` WHERE `provider_id` = %d AND `user_id` = %d',$data['providerid'],$data['userid']));
			
			if($res){
				$success = array(
						'status' => 'success',
						'suc_message' => esc_html__('Remove from favorite successfully.', 'service-finder'),
						);
				echo json_encode($success);
			}
			
	}
	
	/*Reset Booking Calendar*/
	public function service_finder_resetBookingCalender($data = ''){
	$date = null;
	$bookeddate = null;
	$allocateddate = null;
	$daynum = null;
	
	
			global $wpdb, $service_finder_Tables;
			$provider_id = (!empty($data['provider_id'])) ? $data['provider_id'] : '';
			$member_id = (!empty($data['member_id'])) ? $data['member_id'] : 0; 
			$serviceid = (!empty($data['serviceid'])) ? $data['serviceid'] : 0; 
			
			$servicedata = service_finder_getServiceData($serviceid);
			
			if($member_id > 0){
			$res = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE `provider_id` = %d AND (`member_id` = 0 OR `member_id` = %d) AND wholeday = "yes" GROUP BY date',$provider_id,$member_id));
			
			$bookings = $wpdb->get_results($wpdb->prepare('SELECT `bookedservices`.date, COUNT(`bookedservices`.id) as totalbooked FROM '.$service_finder_Tables->bookings.' as bookings LEFT JOIN '.$service_finder_Tables->booked_services.' as bookedservices on bookings.id = bookedservices.booking_id WHERE `bookings`.`provider_id` = %d AND   (`bookedservices`.`member_id` = 0 OR `bookedservices`.`member_id` = %d) AND `bookedservices`.`date` > now() AND `bookings`.`multi_date` = "yes" GROUP BY `bookedservices`.`date`',$provider_id,$member_id));
			}else{
			$res = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE `provider_id` = %d AND `member_id` = %d AND wholeday = "yes" GROUP BY date',$provider_id,$member_id));
			
			$bookings = $wpdb->get_results($wpdb->prepare('SELECT `bookedservices`.date, COUNT(`bookedservices`.id) as totalbooked FROM '.$service_finder_Tables->bookings.' as bookings LEFT JOIN '.$service_finder_Tables->booked_services.' as bookedservices on bookings.id = bookedservices.booking_id WHERE `bookings`.`provider_id` = %d AND  `bookedservices`.`date` > now() AND `bookings`.`multi_date` = "yes" GROUP BY `bookedservices`.`date`',$provider_id,$member_id));
			}
			
			
			if(!empty($bookings)){
				foreach($bookings as $booking){
					$dayname = date('l', strtotime($booking->date));
					
					if($serviceid > 0 && $servicedata->cost_type == 'days'){
					$q = $wpdb->get_row($wpdb->prepare('SELECT max_booking as avlbookings FROM '.$service_finder_Tables->days_availability.' WHERE `service_id` = %d AND `provider_id` = %d AND day = "%s"',$serviceid,$provider_id,strtolower($dayname)));
					}else{
					$q = $wpdb->get_row($wpdb->prepare('SELECT sum(max_bookings) as avlbookings FROM '.$service_finder_Tables->timeslots.' WHERE `provider_id` = %d AND day = "%s"',$provider_id,strtolower($dayname)));
					}
					
					if(!empty($q)){
						if($q->avlbookings <= $booking->totalbooked){
							$bookeddate[] = date('Y-n-j',strtotime($booking->date));			
						}
					}
				}
			}
			
			if($member_id > 0){
			$getalloteddates = $wpdb->get_results($wpdb->prepare('SELECT date FROM '.$service_finder_Tables->unavailability.' WHERE `provider_id` = %d AND (`member_id` = 0 OR `member_id` = %d) AND wholeday != "yes" AND date > now() GROUP BY date',$provider_id,$member_id));
			}else{
			$getalloteddates = $wpdb->get_results($wpdb->prepare('SELECT date FROM '.$service_finder_Tables->unavailability.' WHERE `provider_id` = %d AND `member_id` = %d AND wholeday != "yes" AND date > now() GROUP BY date',$provider_id,$member_id));
			}
			
			if(!empty($getalloteddates)){
				foreach($getalloteddates as $getalloteddate){
					$allocateddate[] = date('Y-n-j',strtotime($getalloteddate->date));
				}
			}
			
			if($serviceid > 0){
			$getdays = $wpdb->get_results($wpdb->prepare('SELECT day FROM '.$service_finder_Tables->days_availability.' WHERE `service_id` = %d AND `provider_id` = %d GROUP BY day',$serviceid,$provider_id));

			if(!empty($getdays)){
				foreach($getdays as $getday){
					$daynum[] = date('N', strtotime($getday->day)) - 1;
				}
			}
			}else{
			if($member_id > 0){
			$getdays = $wpdb->get_results($wpdb->prepare('SELECT day FROM '.$service_finder_Tables->member_timeslots.' WHERE `provider_id` = %d AND `member_id` = %d GROUP BY day',$provider_id,$member_id));
			}else{
			$getdays = $wpdb->get_results($wpdb->prepare('SELECT day FROM '.$service_finder_Tables->timeslots.' WHERE `provider_id` = %d GROUP BY day',$provider_id));		
			}

			if(!empty($getdays)){
				foreach($getdays as $getday){
					$daynum[] = date('N', strtotime($getday->day)) - 1;
				}
			}
			}
			
			if(!empty($res)){
				foreach($res as $row){
				$date[] = date('Y-n-j',strtotime($row->date));
				}
			}
			
			$disabledates = service_finder_get_disabled_dates($provider_id);
			
			$alldays = array(0,1,2,3,4,5,6);
			$daysOfWeekDisabled = array_diff($alldays,$daynum);
			
			if(!empty($daysOfWeekDisabled)){
				foreach($daysOfWeekDisabled as $disableddays){
					$disableddays = $disableddays + 1;
					if($disableddays == 7){
						$disableddays = 0;
					}
					$disableddaysnum[] = $disableddays;
				}
			}
			
			$bookeddatearr = (!empty($bookeddate)) ? $bookeddate : array();
			$disabledatesarr = (!empty($disabledates)) ? $disabledates : array();
			$datearr = (!empty($date)) ? $date : array();
			
			$alldisableddates = array_merge($bookeddatearr,$disabledatesarr,$datearr);
			
			$success = array(
						'status' => 'success',
						'daynum' => json_encode($daynum),
						'daysOfWeekDisabled' => json_encode($disableddaysnum),
						'dates' => json_encode($date),
						'bookeddates' => json_encode($bookeddate),
						'disabledates' => json_encode($disabledates),
						'allocateddates' => json_encode($allocateddate),
						'alldisableddates' => json_encode($alldisableddates)
						);
				echo json_encode($success);
			
		}	
		
	/*Reset Start Time Booking Calendar*/
	public function service_finder_resetStartTimeBookingCalender($data = ''){
	$date = null;
	$bookeddate = null;
	$allocateddate = null;
	$daynum = null;
	$disableddaysnum = null;
	
	
			global $wpdb, $service_finder_Tables;
			$provider_id = (!empty($data['provider_id'])) ? $data['provider_id'] : '';
			$member_id = (!empty($data['member_id'])) ? $data['member_id'] : 0; 
			$serviceid = (!empty($data['serviceid'])) ? $data['serviceid'] : 0; 
			
			$servicedata = service_finder_getServiceData($serviceid);
			
			if($member_id > 0){
			$res = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE `provider_id` = %d AND (`member_id` = 0 OR `member_id` = %d) AND wholeday = "yes" GROUP BY date',$provider_id,$member_id));
			
			$bookings = $wpdb->get_results($wpdb->prepare('SELECT `bookedservices`.date, COUNT(`bookedservices`.id) as totalbooked FROM '.$service_finder_Tables->bookings.' as bookings LEFT JOIN '.$service_finder_Tables->booked_services.' as bookedservices on bookings.id = bookedservices.booking_id WHERE `bookings`.`provider_id` = %d AND   (`bookedservices`.`member_id` = 0 OR `bookedservices`.`member_id` = %d) AND `bookedservices`.`date` > now() AND `bookings`.`multi_date` = "yes" GROUP BY `bookedservices`.`date`',$provider_id,$member_id));
			}else{
			$res = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE `provider_id` = %d AND `member_id` = %d AND wholeday = "yes" GROUP BY date',$provider_id,$member_id));
			
			$bookings = $wpdb->get_results($wpdb->prepare('SELECT `bookedservices`.date, COUNT(`bookedservices`.id) as totalbooked FROM '.$service_finder_Tables->bookings.' as bookings LEFT JOIN '.$service_finder_Tables->booked_services.' as bookedservices on bookings.id = bookedservices.booking_id WHERE `bookings`.`provider_id` = %d AND  `bookedservices`.`date` > now() AND `bookings`.`multi_date` = "yes" GROUP BY `bookedservices`.`date`',$provider_id,$member_id));
			}
			
			if(!empty($bookings)){
				foreach($bookings as $booking){
					$dayname = date('l', strtotime($booking->date));
					
					if($serviceid > 0 && $servicedata->cost_type == 'days'){
					$q = $wpdb->get_row($wpdb->prepare('SELECT max_booking as avlbookings FROM '.$service_finder_Tables->days_availability.' WHERE `service_id` = %d AND `provider_id` = %d AND day = "%s"',$serviceid,$provider_id,strtolower($dayname)));
					}else{
					$q = $wpdb->get_row($wpdb->prepare('SELECT sum(max_bookings) as avlbookings FROM '.$service_finder_Tables->starttime.' WHERE `provider_id` = %d AND day = "%s"',$provider_id,strtolower($dayname)));
					}
					
					if(!empty($q)){
						if($q->avlbookings <= $booking->totalbooked){
							$bookeddate[] = date('Y-n-j',strtotime($booking->date));			
						}
					}
				}
			}
			
			if($member_id > 0){
			$getalloteddates = $wpdb->get_results($wpdb->prepare('SELECT date FROM '.$service_finder_Tables->unavailability.' WHERE `provider_id` = %d AND (`member_id` = 0 OR `member_id` = %d) AND wholeday != "yes" AND date > now() GROUP BY date',$provider_id,$member_id));
			}else{
			$getalloteddates = $wpdb->get_results($wpdb->prepare('SELECT date FROM '.$service_finder_Tables->unavailability.' WHERE `provider_id` = %d AND `member_id` = %d AND wholeday != "yes" AND date > now() GROUP BY date',$provider_id,$member_id));
			}
			
			if(!empty($getalloteddates)){
				foreach($getalloteddates as $getalloteddate){
					$allocateddate[] = date('Y-n-j',strtotime($getalloteddate->date));
				}
			}
			
			if($serviceid > 0 && $servicedata->cost_type == 'days'){
			$getdays = $wpdb->get_results($wpdb->prepare('SELECT day FROM '.$service_finder_Tables->days_availability.' WHERE `service_id` = %d AND `provider_id` = %d GROUP BY day',$serviceid,$provider_id));

			if(!empty($getdays)){
				foreach($getdays as $getday){
					$daynum[] = date('N', strtotime($getday->day)) - 1;
				}
			}
			}else{
			
			if($member_id > 0){
			$getdays = $wpdb->get_results($wpdb->prepare('SELECT day FROM '.$service_finder_Tables->member_starttimes.' WHERE `provider_id` = %d AND `member_id` = %d GROUP BY day',$provider_id,$member_id));
			}else{
			$getdays = $wpdb->get_results($wpdb->prepare('SELECT day FROM '.$service_finder_Tables->starttime.' WHERE `provider_id` = %d GROUP BY day',$provider_id));
			}

			if(!empty($getdays)){
				foreach($getdays as $getday){
					$daynum[] = date('N', strtotime($getday->day)) - 1;
				}
			}
			}
			
			if(!empty($res)){
				foreach($res as $row){
				$date[] = date('Y-n-j',strtotime($row->date));
				}
			}
			
			$disabledates = service_finder_get_disabled_dates($provider_id);
			
			$alldays = array(0,1,2,3,4,5,6);
			$daysOfWeekDisabled = array_diff($alldays,$daynum);
			
			if(!empty($daysOfWeekDisabled)){
				foreach($daysOfWeekDisabled as $disableddays){
					$disableddays = $disableddays + 1;
					if($disableddays == 7){
						$disableddays = 0;
					}
					$disableddaysnum[] = $disableddays;
				}
			}
			
			$bookeddatearr = (!empty($bookeddate)) ? $bookeddate : array();
			$disabledatesarr = (!empty($disabledates)) ? $disabledates : array();
			$datearr = (!empty($date)) ? $date : array();
			
			$alldisableddates = array_merge($bookeddatearr,$disabledatesarr,$datearr);
			
			$success = array(
						'status' => 'success',
						'daynum' => json_encode($daynum),
						'daysOfWeekDisabled' => json_encode($disableddaysnum),
						'dates' => json_encode($date),
						'bookeddates' => json_encode($bookeddate),
						'disabledates' => json_encode($disabledates),
						'allocateddates' => json_encode($allocateddate),
						'alldisableddates' => json_encode($alldisableddates)
						);
			echo json_encode($success);
			
		}	
				
}