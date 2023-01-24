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

class SERVICE_FINDER_Bookings{
	
	public function service_finder_getratingbox($arg){
		global $service_finder_options, $wpdb;
		
		$bookingid = (!empty($arg['feedbookingid'])) ? esc_html($arg['feedbookingid']) : '';
		$ratingstyle = (!empty($service_finder_options['rating-style'])) ? $service_finder_options['rating-style'] : '';
		
		if($ratingstyle == 'custom-rating'){
		$this->service_finder_booking_rating_form($bookingid);
		}else{
		$this->service_finder_booking_rating_form($bookingid);
		}
		exit(0);
	}
	
	public function service_finder_booking_rating_form($bookingid){
		global $wpdb, $service_finder_Tables;
		
		$row = $wpdb->get_row($wpdb->prepare('SELECT provider_id, member_id FROM '.$service_finder_Tables->bookings.' WHERE id = %d',$bookingid));
		
		
		if(!empty($row)){
		$categoryid = get_user_meta($row->provider_id,'primary_category',true);
		
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
		}
		
		}
	}
	
	/*Load Members for Assign*/
	public function service_finder_loadAllMembers($arg){
		
		global $wpdb, $service_finder_Tables, $service_finder_Params;
		
			$booking = $wpdb->get_row($wpdb->prepare('SELECT bookings.id, bookings.provider_id, bookings.date, bookings.start_time, bookings.end_time, customers.zipcode FROM '.$service_finder_Tables->bookings.' as bookings INNER JOIN '.$service_finder_Tables->customers.' as customers on bookings.booking_customer_id = customers.id WHERE `bookings`.`id` = %d',$arg['bookingid']));
			
			
			$slot = $booking->start_time.'-'.$booking->end_time;
			$memberid = (!empty($arg['memberid'])) ? $arg['memberid'] : '';
			$members = service_finder_getStaffMembers($booking->provider_id,$booking->zipcode,$booking->date,$slot,$memberid);
			//print_r($members);
				$html = '';
				if(!empty($members)){
				
					$html = '
<div class="staff-member clear equal-col-outer">';
  $html .= '
  <div class="col-md-12">
    <h6>'.esc_html__('Choose Staff Member', 'service-finder').'</h6>
  </div>
  ';
  foreach($members as $member){
  
  if(service_finder_availability_method($booking->provider_id) == 'timeslots'){
  $dayname = date('l', strtotime( $booking->date ));
  $start_time = (!empty($booking->start_time)) ? $booking->start_time : '';
  $end_time = (!empty($booking->end_time)) ? $booking->end_time : '';
  
  $settings = service_finder_getProviderSettings($booking->provider_id);
  
  $slot_interval = (!empty($settings['slot_interval'])) ? $settings['slot_interval'] : '';
  
  $mendtime = date('H:i:s', strtotime($start_time." +".$slot_interval." minutes"));
  
  $new_start_time = $start_time;
  
  $avlflag = 1;
  $bookingavlflag = 1;
  
  for($i = 1;$i <= 24;$i++){
  
  	$member_timeslots = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->member_timeslots.' where day = %s and provider_id = %d AND `member_id` = %d AND start_time = %s AND end_time = %s',$dayname,$booking->provider_id,$member->id,$new_start_time,$mendtime));
	//echo $wpdb->last_query;
	if(empty($member_timeslots)){
		$avlflag = 0;
	}
	
	$new_start_time = $mendtime;
	
	if($end_time == $mendtime){
		break;	
	}
	//echo '<br/>';
	$mendtime = date('H:i:s', strtotime($mendtime." +".$slot_interval." minutes"));
	
  }
  
  //$member_timeslots = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->member_timeslots.' where day = %s and provider_id = %d AND `member_id` = %d AND start_time = %s AND end_time = %s',$dayname,$booking->provider_id,$member->id,$new_start_time,$mendtime));
	
  //$member_timeslots = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->member_timeslots.' where day = %s and provider_id = %d AND `member_id` = %d AND start_time = %s AND end_time = %s',$dayname,$booking->provider_id,$member->id,$start_time,$end_time));
  //echo $avlflag;
  if($avlflag == 1){
  
  $src  = wp_get_attachment_image_src( $member->avatar_id, 'service_finder-provider-thumb' );
  $src  = $src[0];
  $memberid = (!empty($arg['memberid'])) ? $arg['memberid']  : '';
  $member_id = (!empty($member->id)) ? $member->id  : '';
  if($memberid == $member_id){
  $select = 'selected';
  }else{
  $select = '';
  }
  
  if($src != ''){
	$imgtag = '<img src="'.esc_url($src).'" width="185" height="185" alt="">';
	}else{
	$defaultavatar = service_finder_get_default_avatar();
	$imgtag = '<img src="'.esc_url($defaultavatar).'" width="185" height="185" alt="">';
	}
  $html .= sprintf('
  <div class="col-md-3 col-sm-4 col-xs-6 equal-col">
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
  }elseif(service_finder_availability_method($booking->provider_id) == 'starttime'){
   $src  = wp_get_attachment_image_src( $member->avatar_id, 'service_finder-provider-thumb' );
  $src  = $src[0];
  $memberid = (!empty($arg['memberid'])) ? $arg['memberid']  : '';
  $member_id = (!empty($member->id)) ? $member->id  : '';
  if($memberid == $member_id){
  $select = 'selected';
  }else{
  $select = '';
  }
  
  if($src != ''){
	$imgtag = '<img src="'.esc_url($src).'" width="185" height="185" alt="">';
	}else{
	$defaultavatar = service_finder_get_default_avatar();
	$imgtag = '<img src="'.esc_url($defaultavatar).'" width="185" height="185" alt="">';
	}
  $html .= sprintf('
  <div class="col-md-3 col-sm-4 col-xs-6 equal-col">
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
';
					
					
					}else{
					$html = '<div class="alert alert-warning">'.esc_html__('No members available', 'service-finder').'</div>';
				}
					
					
					$success = array(
						'status' => 'success',
						'members' => $html,
						);
					echo json_encode($success);
			
				
	}	
	
	/*Approve wired booking*/
	public function service_finder_approvebooking(){
	global $wpdb, $service_finder_Tables;
	
	$bookingid = (!empty($_POST['bookingid'])) ? esc_html($_POST['bookingid']) : '';
	
		$data = array(
				'status' => 'Pending',
				);
		
		$where = array(
				'id' => $bookingid,
				);

		$booking_id = $wpdb->update($service_finder_Tables->bookings,wp_unslash($data),$where);		

		if(is_wp_error($booking_id)){
			$error = array(
					'status' => 'error',
					'err_message' => $service_id->get_error_message()
					);
			echo json_encode($error);
		}else{
			
			$bookingdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `id` = %d',$bookingid),ARRAY_A);
			if(function_exists('service_finder_add_notices')) {
			$res = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `id` = %d',$bookingdata['booking_customer_id']),ARRAY_A);
			$users = $wpdb->prefix . 'users';
			$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$users.' WHERE `user_email` = "%s"',$res['email']));
			
			
			$noticedata = array(
						'customer_id' => $row->ID,
						'target_id' => $bookingid, 
						'topic' => 'Approve Booking',
						'title' => esc_html__('Approve Booking', 'service-finder'),
						'notice' => esc_html__('Booking have been approved after wired bank transffer', 'service-finder')
						);
				service_finder_add_notices($noticedata);
			
			}
			
			$senMail = new SERVICE_FINDER_Bookings();
			$senMail->service_finder_SendApproveBookingMailToProvider($bookingdata);
			$senMail->service_finder_SendApproveBookingMailToCustomer($bookingdata);
			$senMail->service_finder_SendApproveBookingMailToAdmin($bookingdata);
			
			$msg = (!empty($service_finder_options['booking-approve'])) ? $service_finder_options['booking-approve'] : esc_html__('Booking approved successfully', 'service-finder');
			$success = array(
					'status' => 'success',
					'suc_message' => $msg,
					);
			echo json_encode($success);
		}
	}
	
	/*Send Booking Approval mail to provider*/
	public function service_finder_SendApproveBookingMailToProvider($bookingdata){
		global $service_finder_options, $service_finder_Tables, $wpdb;
		
		$providerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$bookingdata['provider_id']));
		$customerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `id` = %d',$bookingdata['booking_customer_id']));
		
		$bookingpayment_mode = (!empty($maildata['type'])) ? $maildata['type'] : '';
		
		$payent_mode = ($bookingpayment_mode != '') ? $bookingpayment_mode : 'free';
		
		$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? $service_finder_options['pay_booking_amount_to'] : '';
		
		if(!empty($service_finder_options['booking-approval-to-provider'])){
			$message = $service_finder_options['booking-approval-to-provider'];
		}else{
			$message = '
<h4>Booking Approved</h4>
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
				
Amount: %AMOUNT%
				
Admin Fee: %ADMINFEE%';
}
			
			$tokens = array('%DATE%','%STARTTIME%','%ENDTIME%','%MEMBERNAME%','%PROVIDERNAME%','%PROVIDEREMAIL%','%PROVIDERPHONE%','%CUSTOMERNAME%','%CUSTOMEREMAIL%','%CUSTOMERPHONE%','%CUSTOMERPHONE2%','%ADDRESS%','%APT%','%CITY%','%STATE%','%ZIPCODE%','%COUNTRY%','%SERVICES%','%PAYMENTMETHOD%','%AMOUNT%','%ADMINFEE%');
			
			if($bookingdata['member_id'] > 0){
			$membername = service_finder_getMemberName($bookingdata['member_id']);
			}else{
			$membername = '-';
			}
			
			$services = service_finder_get_booking_services($bookingdata['id']);
			
			$charge_admin_fee = (!empty($service_finder_options['charge-admin-fee'])) ? $service_finder_options['charge-admin-fee'] : '';
			$charge_admin_fee_from = (!empty($service_finder_options['charge-admin-fee-from'])) ? $service_finder_options['charge-admin-fee-from'] : '';
			
			if($charge_admin_fee_from == 'provider' && $pay_booking_amount_to == 'admin' && $charge_admin_fee){
			$bookingamount = $bookingdata['total'] - $adminfee;
			}elseif($charge_admin_fee_from == 'customer' && $charge_admin_fee && $pay_booking_amount_to == 'admin'){
			$bookingamount = $bookingdata['total'];
			}else{
			$bookingamount = $bookingdata['total'];
			$adminfee = '0.0';
			}
			
			$replacements = array(service_finder_date_format($bookingdata['date']),service_finder_time_format($bookingdata['start_time']),service_finder_time_format($bookingdata['end_time']),$membername,service_finder_get_providername_with_link($providerInfo->wp_user_id),$providerInfo->email,service_finder_get_contact_info($providerInfo->phone,$providerInfo->mobile),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country,$services,ucfirst($payent_mode),service_finder_money_format($bookingamount),service_finder_money_format($adminfee));
			$msg_body = str_replace($tokens,$replacements,$message);
			
			if($service_finder_options['booking-approval-to-provider-subject'] != ""){
				$msg_subject = $service_finder_options['booking-approval-to-provider-subject'];
			}else{
				$msg_subject = esc_html__('Booking Approval Notification', 'service-finder');
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
	
	/*Send Booking Approval mail to customer*/
	public function service_finder_SendApproveBookingMailToCustomer($bookingdata){
		global $service_finder_options, $service_finder_Tables, $wpdb;
		$providerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$bookingdata['provider_id']));
		$customerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `id` = %d',$bookingdata['booking_customer_id']));
		
		$bookingpayment_mode = (!empty($maildata['type'])) ? $maildata['type'] : '';
		
		$payent_mode = ($bookingpayment_mode != '') ? $bookingpayment_mode : 'free';
		
		$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? $service_finder_options['pay_booking_amount_to'] : '';
		
		if(!empty($service_finder_options['booking-approval-to-customer'])){
			$message = $service_finder_options['booking-approval-to-customer'];
		}else{
			$message = '
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
				
				Amount: %AMOUNT%
				
				Admin Fee: %ADMINFEE%';
		}
		
			$tokens = array('%DATE%','%STARTTIME%','%ENDTIME%','%MEMBERNAME%','%PROVIDERNAME%','%PROVIDEREMAIL%','%PROVIDERPHONE%','%CUSTOMERNAME%','%CUSTOMEREMAIL%','%CUSTOMERPHONE%','%CUSTOMERPHONE2%','%ADDRESS%','%APT%','%CITY%','%STATE%','%ZIPCODE%','%COUNTRY%','%SERVICES%','%PAYMENTMETHOD%','%AMOUNT%','%ADMINFEE%');
			
			if($bookingdata['member_id'] > 0){
			$membername = service_finder_getMemberName($bookingdata['member_id']);
			}else{
			$membername = '-';
			}
			$services = service_finder_get_booking_services($bookingdata['id']);
			
			$charge_admin_fee_from = (!empty($service_finder_options['charge-admin-fee-from'])) ? $service_finder_options['charge-admin-fee-from'] : '';
			$charge_admin_fee = (!empty($service_finder_options['charge-admin-fee'])) ? $service_finder_options['charge-admin-fee'] : '';
			
			if($charge_admin_fee_from == 'provider' && $charge_admin_fee && $pay_booking_amount_to == 'admin'){
			$adminfee = '0.0';
			}
			
			$replacements = array(service_finder_date_format($bookingdata['date']),service_finder_time_format($bookingdata['start_time']),service_finder_time_format($bookingdata['end_time']),$membername,service_finder_get_providername_with_link($providerInfo->wp_user_id),$providerInfo->email,service_finder_get_contact_info($providerInfo->phone,$providerInfo->mobile),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country,$services,ucfirst($payent_mode),service_finder_money_format($bookingdata['total']),service_finder_money_format($adminfee));
			$msg_body = str_replace($tokens,$replacements,$message);

			if($service_finder_options['booking-approval-to-customer-subject'] != ""){
				$msg_subject = $service_finder_options['booking-approval-to-customer-subject'];
			}else{
				$msg_subject = esc_html__('Booking Approval Notification', 'service-finder');
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
	
	/*Send Booking Approval mail to admin*/
	public function service_finder_SendApproveBookingMailToAdmin($bookingdata){
		global $service_finder_options, $wpdb, $service_finder_Tables;
		$providerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$bookingdata['provider_id']));
		$customerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `id` = %d',$bookingdata['booking_customer_id']));
		
		$bookingpayment_mode = (!empty($bookingdata['type'])) ? $bookingdata['type'] : '';
		
		$payent_mode = ($bookingpayment_mode != '') ? $bookingpayment_mode : 'free';
		
		$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? $service_finder_options['pay_booking_amount_to'] : '';
		
		if(!empty($service_finder_options['booking-approval-to-admin'])){
			$message = $service_finder_options['booking-approval-to-admin'];
		}else{
			$message = '
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
				
				Amount: %AMOUNT%
				
				Admin Fee: %ADMINFEE%';
		}
			
			$tokens = array('%DATE%','%STARTTIME%','%ENDTIME%','%MEMBERNAME%','%PROVIDERNAME%','%PROVIDEREMAIL%','%PROVIDERPHONE%','%CUSTOMERNAME%','%CUSTOMEREMAIL%','%CUSTOMERPHONE%','%CUSTOMERPHONE2%','%ADDRESS%','%APT%','%CITY%','%STATE%','%ZIPCODE%','%COUNTRY%','%SERVICES%','%PAYMENTMETHOD%','%AMOUNT%','%ADMINFEE%');
			
			if($bookingdata['member_id'] > 0){
			$membername = service_finder_getMemberName($bookingdata['member_id']);
			}else{
			$membername = '-';
			}
			$services = service_finder_get_booking_services($bookingdata['id']);
			
			$charge_admin_fee = (!empty($service_finder_options['charge-admin-fee'])) ? $service_finder_options['charge-admin-fee'] : '';
			$charge_admin_fee_from = (!empty($service_finder_options['charge-admin-fee-from'])) ? $service_finder_options['charge-admin-fee-from'] : '';
			
			if($charge_admin_fee_from == 'provider' && $charge_admin_fee && $pay_booking_amount_to == 'admin'){
			$bookingamount = $bookingdata['total'] - $adminfee;
			}elseif($charge_admin_fee_from == 'customer' && $charge_admin_fee && $pay_booking_amount_to == 'admin'){
			$bookingamount = $bookingdata['total'];
			}else{
			$bookingamount = $bookingdata['total'];
			$adminfee = '0.0';
			}
			
			$replacements = array(service_finder_date_format($bookingdata['date']),service_finder_time_format($bookingdata['start_time']),service_finder_time_format($bookingdata['end_time']),$membername,service_finder_get_providername_with_link($providerInfo->wp_user_id),$providerInfo->email,service_finder_get_contact_info($providerInfo->phone,$providerInfo->mobile),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country,$services,ucfirst($payent_mode),service_finder_money_format($bookingamount),service_finder_money_format($adminfee));
			$msg_body = str_replace($tokens,$replacements,$message);
			
			if($service_finder_options['booking-approval-to-admin-subject'] != ""){
				$msg_subject = $service_finder_options['booking-approval-to-admin-subject'];
			}else{
				$msg_subject = esc_html__('Booking Approval Notification', 'service-finder');
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
	
	/*Assign Member for new booking*/
	public function service_finder_assignMember($arg){
		
		global $wpdb, $service_finder_Tables, $service_finder_Params;
		
		$data = array(
					'member_id' => esc_attr($arg['memberid']),
					);
					
		$where = array(
					'id' => esc_attr($arg['bookingid']),
					);			

		$wpdb->update($service_finder_Tables->bookings,wp_unslash($data),$where);
		
		$bookingdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `id` = %d',$arg['bookingid']),ARRAY_A);
		
		$senMail = new SERVICE_FINDER_Bookings();
		
		$senMail->service_finder_SendAssignBookingMailToMember($bookingdata);
		
		$success = array(
			'status' => 'success',
			);

		echo json_encode($success);
			
				
	}	
	
	/*Send Assign Booking mail to provider*/
	public function service_finder_SendAssignBookingMailToMember($maildata = ''){
		global $service_finder_options, $service_finder_Tables, $wpdb;
		
		$providerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$maildata['provider_id']));
		$customerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `id` = %d',$maildata['booking_customer_id']));
		
		$job_assign = (!empty($service_finder_options['job-assign-to-member'])) ? $service_finder_options['job-assign-to-member'] : '';
		
		if(!empty($job_assign)){
			$message = $job_assign;
		}else{
			$message = '<h3>Booking Assigned</h3>
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

Services: %SERVICES%';
		}
			
			$tokens = array('%DATE%','%STARTTIME%','%ENDTIME%','%MEMBERNAME%','%PROVIDERNAME%','%PROVIDEREMAIL%','%PROVIDERPHONE%','%CUSTOMERNAME%','%CUSTOMEREMAIL%','%CUSTOMERPHONE%','%CUSTOMERPHONE2%','%ADDRESS%','%APT%','%CITY%','%STATE%','%ZIPCODE%','%COUNTRY%','%SERVICES%');
			
			if($maildata['member_id'] > 0){
			$membername = service_finder_getMemberName($maildata['member_id']);
			$memberemail = service_finder_getMemberEmail($maildata['member_id']);
			}else{
			$membername = '-';
			}
			
			$services = service_finder_get_booking_services($maildata['id']);
			
			$replacements = array(service_finder_date_format($maildata['date']),service_finder_time_format($maildata['start_time']),service_finder_time_format(service_finder_get_booking_end_time($maildata['end_time'],$maildata['end_time_no_buffer'])),$membername,service_finder_get_providername_with_link($providerInfo->wp_user_id),$providerInfo->email,service_finder_get_contact_info($providerInfo->phone,$providerInfo->mobile),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country,$services);
			$msg_body = str_replace($tokens,$replacements,$message);
			$msg_subject = 'Booking Assigned to you';
			
			if($service_finder_options['job-assign-to-member-subject'] != ""){
				$msg_subject = $service_finder_options['job-assign-to-member-subject'];
			}else{
				$msg_subject = esc_html__('Booking Assigned to you', 'service-finder');
			}
			
			if(service_finder_wpmailer($memberemail,$msg_subject,$msg_body)) {

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
	
		
	/*Display provider bookings into datatable*/
	public function service_finder_getBookings($arg){
		global $wpdb, $service_finder_Tables, $service_finder_options;
		$requestData= $_REQUEST;
		$currUser = wp_get_current_user(); 
		$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';
		$userCap = service_finder_get_capability($user_id);
		$members = $wpdb->get_results($wpdb->prepare('SELECT bookings.id, bookings.multi_date, bookings.payment_to, bookings.charge_admin_fee_from, bookings.paid_to_provider, bookings.total, bookings.adminfee, bookings.payment_type, bookings.type, bookings.jobid, bookings.quoteid, bookings.wired_invoiceid, bookings.date, bookings.start_time, bookings.end_time, bookings.end_time_no_buffer, members.member_name, bookings.member_id, bookings.status, bookings.txnid, customers.name, customers.phone, customers.email, customers.address, customers.city FROM '.$service_finder_Tables->bookings.' as bookings INNER JOIN '.$service_finder_Tables->customers.' as customers on bookings.booking_customer_id = customers.id LEFT JOIN '.$service_finder_Tables->team_members.' as members on bookings.member_id = members.id WHERE `provider_id` = %d',$user_id));
		
		
		$columns = array( 
			0 =>'name', 
			1 =>'name', 
			2 => 'email',
			3 =>'date', 
			4=> 'start_time',
			5=> 'member_name',
			6=> 'status',
		);
		
		// getting total number records without any search
		$sql = $wpdb->prepare("SELECT bookings.id, bookings.jobid, bookings.quoteid, bookings.multi_date, bookings.payment_type, bookings.payment_to, bookings.charge_admin_fee_from, bookings.paid_to_provider, bookings.total, bookings.adminfee, bookings.type, bookings.wired_invoiceid, bookings.date, bookings.start_time, bookings.end_time, bookings.end_time_no_buffer, members.member_name, bookings.member_id, bookings.status, bookings.txnid, customers.name, customers.phone, customers.email, customers.address, customers.city FROM ".$service_finder_Tables->bookings." as bookings INNER JOIN ".$service_finder_Tables->customers." as customers on bookings.booking_customer_id = customers.id LEFT JOIN ".$service_finder_Tables->team_members." as members on bookings.member_id = members.id WHERE `provider_id` = %d",$user_id);
		$query=$wpdb->get_results($sql);
		$totalData = count($query);
		$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
		$sql = "SELECT bookings.id, bookings.jobid, bookings.quoteid, bookings.payment_to, bookings.multi_date, bookings.payment_type, bookings.charge_admin_fee_from, bookings.paid_to_provider, bookings.total, bookings.adminfee, bookings.type, bookings.wired_invoiceid, bookings.date, bookings.start_time, bookings.end_time, bookings.end_time_no_buffer, members.member_name, bookings.member_id, bookings.status, bookings.txnid, customers.name, customers.phone, customers.email, customers.address, customers.city";
		$sql.=" FROM ".$service_finder_Tables->bookings." as bookings INNER JOIN ".$service_finder_Tables->customers." as customers on bookings.booking_customer_id = customers.id LEFT JOIN ".$service_finder_Tables->team_members." as members on bookings.member_id = members.id WHERE `provider_id` = ".$user_id;
		
		if( !empty($requestData['search']['value']) && $requestData['search']['value'] == 'upcoming') {
			$sql.=" AND bookings.date >= CURDATE()";
		}elseif( !empty($requestData['search']['value']) && $requestData['search']['value'] == 'past') {
			$sql.=" AND bookings.date < CURDATE()";
		}elseif( !empty($requestData['search']['value']) ) {
			$sql.=" AND ( customers.name LIKE '".$requestData['search']['value']."%' ";    
			$sql.=" OR bookings.id LIKE '".$requestData['search']['value']."%' ";
			$sql.=" OR bookings.date LIKE '".$requestData['search']['value']."%' ";
			$sql.=" OR bookings.start_time LIKE '".$requestData['search']['value']."%' ";
			$sql.=" OR members.member_name LIKE '".$requestData['search']['value']."%' ";
			$sql.=" OR bookings.status LIKE '".$requestData['search']['value']."%' ";
			$sql.=" OR customers.email LIKE '".$requestData['search']['value']."%' )";
		}
		$query=$wpdb->get_results($sql);
		$totalFiltered = count($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
		$sql.=" ORDER BY bookings.id ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		$query=$wpdb->get_results($sql);
		
		$data = array();
		
		foreach($query as $result){
			$nestedData=array(); 
		

			$nestedData[] = '
<div class="checkbox sf-radio-checkbox">
  <input type="checkbox" id="booking-'.esc_attr($result->id).'" class="deleteBookingRow" value="'.esc_attr($result->id).'">
  <label for="booking-'.$result->id.'"></label>
</div>
';
			if((strtotime($result->date) >= strtotime(date("Y-m-d")) && $result->multi_date != "yes") || ($result->status == "Pending" && $result->multi_date == "yes")){
				$status2 = esc_html__('Upcoming','service-finder');
				$upcoming = 'yes';
				
			}else{
				$status2 = esc_html__('Past','service-finder');
				$upcoming = 'no';
				
			}
			
			if($result->jobid > 0){
				$type = esc_html__('Job','service-finder');
			}elseif($result->quoteid > 0){
				$type = esc_html__('Quotation','service-finder');
			}else{
				$type = esc_html__('Booking','service-finder');
			}
			
			$assign = '';
			if($result->status == 'Cancel' || $result->status == 'Completed'){
				
				$status = ($result->status == 'Cancel') ? esc_html__('Cancelled','service-finder') : esc_html__('Completed','service-finder');
				$statusclass = ($result->status == 'Cancel') ? 'sf-booking-cancelled' : 'sf-booking-completed';
				$assign = '';
				$membername = ($result->member_id > 0) ? ucfirst($result->member_name) : '';
				$statusbtn = '';
			}else{
				$statusbtn = '
<button type="button" class="btn btn-warning btn-xs changeStatus" data-id="'.esc_attr($result->id).'" title="'.esc_html__('Change Status', 'service-finder').'"><i class="fa fa-battery-half"></i></button>
';
				$status = esc_html__('Incomplete','service-finder');
				$statusclass = 'sf-booking-incomplete';
				$membername = ($result->member_id > 0) ? ucfirst($result->member_name) : '';
				if(!empty($userCap)){
				if(in_array('staff-members',$userCap)){
				$assign = ($result->member_id > 0) ? '<button type="button" data-id="'.esc_attr($result->id).'-'.esc_attr($result->member_id).'" class="btn btn-primary editAssignButton margin-r-10"><i class="fa fa-user"></i>'.esc_html__('Edit Team Member', 'service-finder').'</button>' : '<button type="button" data-id="'.esc_attr($result->id).'" class="btn btn-primary assignButton margin-r-10"><i class="fa fa-user"></i>'.esc_html__('Assign Now', 'service-finder').'</button>';
				}
				}
				
			}
			
			$time_format = (!empty($service_finder_options['time-format'])) ? $service_finder_options['time-format'] : '';
			
			if($result->end_time != Null){

			$showtime = service_finder_time_format($result->start_time).' TO '.service_finder_time_format(service_finder_get_booking_end_time($result->end_time,$result->end_time_no_buffer));
			

			}else{

			$showtime = service_finder_time_format($result->start_time);

			}
			
			$multidate = ($result->multi_date == 'yes') ? $result->multi_date : 'no';
			
			$services = '';
			if($result->jobid == 0 && $result->quoteid == 0){
			if($multidate == 'yes'){
			$services = '<a href="javascript:;" class="btn btn-primary" data-toggle="collapse" data-target="#services-summary-'.esc_attr($result->id).'"><i class="fa fa-plus"></i> '.esc_html__('Booked Services','service-finder').'</a>
						<div class="collapse" id="services-summary-'.esc_attr($result->id).'">
							'.service_finder_get_booking_services_summary($result->id).'
						</div>';
			}
			}
			
			$bookingdetails = '<div class="sf-booking-info-col">
										<span class="sf-booking-refid">#'.$result->id.'</span> <span class="booking-status '.sanitize_html_class($statusclass).'">'.$status.'</span>

										<div class="sf-booking-upcoming">
											'.$type.'
										</div>		

										<div class="sf-booking-customer">
											<ul class="customer-info">
												<li><strong><i class="fa fa-user"></i> '.esc_html__('Name', 'service-finder').'</strong> '.$result->name.'</li>
												<li><strong><i class="fa fa-envelope"></i> '.esc_html__('Email', 'service-finder').'</strong> '.$result->email.'</li>
												<li><strong><i class="fa fa-phone"></i> '.esc_html__('Phone', 'service-finder').'</strong> '.$result->phone.'</li>';
							if($multidate == 'no' || $result->jobid > 0 || $result->quoteid > 0){
							$bookingdetails .= '<li><strong><i class="fa fa-calendar-o"></i> '.esc_html__('Date', 'service-finder').'</strong> '.service_finder_date_format($result->date).'</li>
												<li><strong><i class="fa fa-clock-o"></i> '.esc_html__('Time', 'service-finder').'</strong> '.$showtime.'</li>';
							}
							if($membername != ''){
							$bookingdetails .= '<li><strong><i class="fa fa-users"></i> '.esc_html__('Team Member', 'service-finder').'</strong> '.$membername.'</li>';			
							}
							$bookingdetails .= '</ul>
										</div>';
										
							$bookingdetails .= $assign;
							$bookingdetails .= $services;

							$bookingdetails .= '</div>';
			
			$nestedData[] = $bookingdetails;
			
			$paymentstatus = '';
			if($result->type == 'free'){
				$paymentstatus = esc_html__('Free', 'service-finder');
			}else{
				if($result->status == 'Pending'){
				$paymentstatus = esc_html__('Paid', 'service-finder');
				}elseif($result->status == 'Need-Approval'){
				$paymentstatus = esc_html__('Pending', 'service-finder');
				}else{
				$paymentstatus = esc_html__('Paid', 'service-finder');
				}
			}
			
			if($result->charge_admin_fee_from == 'provider'){
				$bookingamount = $result->total - $result->adminfee;
			}elseif($result->charge_admin_fee_from == 'customer'){
				$bookingamount = $result->total;
			}else{
				$bookingamount = $result->total;
			}
			if($result->payment_to == 'admin'){
				$paytoproviderstatus = service_finder_translate_static_status_string($result->paid_to_provider);
			}else{
				$paytoproviderstatus = esc_html__('No Dues','service-finder');
			}
			
			$txnid = ($result->txnid != "") ? $result->txnid : 'NA';
			
			if($result->type == "mangopay"){
				$totalamount = $result->total;
				$bookingamount = $result->total - $result->adminfee;
			}else{
				$totalamount = $bookingamount;
			}
			
			$paymentdetails = '<div class="inner">
										<h3><span class="sf-booking-payment-info" data-toggle="popover" data-container="body" data-placement="top" data-html="true" id="payinfo-'.$result->id.'" data-trigger="hover">'.service_finder_money_format($result->total).' </span><span class="sf-payment-status">'.$paymentstatus.'</span></h3>
                                <div id="popover-content-payinfo-'.$result->id.'" class="hide">
                                    <ul class="list-unstyled margin-0 booking-payment-data">
                                        <li><strong>'.esc_html__('Total Amount','service-finder').':</strong> '.service_finder_money_format($totalamount).'</li>
										<li><strong>'.esc_html__('Providers Fee','service-finder').':</strong> '.service_finder_money_format($bookingamount).'</li>
                                        <li><strong>'.esc_html__('Admin Fee','service-finder').':</strong> '.service_finder_money_format($result->adminfee).'</li>
                                        <li><strong>'.esc_html__('Payment Method','service-finder').':</strong> '.service_finder_translate_static_status_string($result->type).'</li>
                                        <li><strong>'.esc_html__('Admin pay to providers','service-finder').':</strong> '.$paytoproviderstatus.'</li>
                                        <li><strong>'.esc_html__('Transaction ID','service-finder').':</strong> '.$txnid.'</li>
                                    </ul>
                                </div>
									</div>';
			
			$nestedData[] = $paymentdetails;
			
			$userCap = service_finder_get_capability($user_id);
			$actions = '';
			if(!empty($userCap)){
				if(in_array('invoice',$userCap) && in_array('bookings',$userCap)){
				$actions = '
<button type="button" data-email="'.esc_attr($result->email).'" data-id="'.esc_attr($result->id).'" class="btn btn-primary btn-xs addInvoice margin-r-5" title="'.esc_html__('Add Invoice', 'service-finder').'"><i class="fa fa-plus"></i></button>
';
				}
			}
			
			$actions .= '
<button type="button" class="btn btn-custom btn-xs viewBookings" data-upcoming="'.esc_attr($upcoming).'" data-id="'.esc_attr($result->id).'" title="'.esc_html__('View Booking', 'service-finder').'"><i class="fa fa-eye"></i></button>
'.$statusbtn;

			if($result->type == 'wired' && $result->status == 'Need-Approval' && $result->payment_to == 'provider'){
			$actions .= '
<button type="button" data-bookingid="'.esc_attr($result->id).'" class="btn btn-primary btn-xs approvewiredbooking" title="'.esc_html__('Approve Booking', 'service-finder').'"><i class="fa fa-check-square"></i></button>';
			}
				
			$nestedData[] = $actions;
			
			$data[] = $nestedData;
		}
		
		
		
		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
					"recordsTotal"    => intval( $totalData ),  // total number of records
					"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
					"data"            => $data   // total data array
					);
		
		echo json_encode($json_data);  // send data as json format
	}
	
	/*Delete provider Bookings*/
	public function service_finder_deleteBookings(){
	global $wpdb, $service_finder_Tables;
			$data_ids = $_REQUEST['data_ids'];
			$data_id_array = explode(",", $data_ids); 
			if(!empty($data_id_array)) {
				foreach($data_id_array as $id) {
					$sql = $wpdb->prepare("DELETE FROM ".$service_finder_Tables->bookings." WHERE id = %d",$id);
					$query=$wpdb->query($sql);
					$sql = $wpdb->prepare("DELETE FROM ".$service_finder_Tables->booked_services." WHERE booking_id = %d",$id);
					$query=$wpdb->query($sql);
					$sql = $wpdb->prepare("DELETE FROM ".$service_finder_Tables->notifications." WHERE `topic` = 'Booking' AND `target_id` = %d",$id);
					$query=$wpdb->query($sql);
				}
			}
	}
	
	
	/*View provider Bookings*/
	public function service_finder_viewBookings(){
		global $wpdb, $service_finder_Tables, $service_finder_options, $current_user;
	
		$bookingid = esc_html($_REQUEST['bookingid']);
		$isadmin = (isset($_REQUEST['isadmin'])) ? esc_html($_REQUEST['isadmin']) : '';
		$cancel = '';
		$complete = '';
		
		$customerreplacestring = (!empty($service_finder_options['customer-replace-string'])) ? $service_finder_options['customer-replace-string'] : esc_html__('Customers', 'service-finder');	
		$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('Providers', 'service-finder');	
	
		$sql = $wpdb->prepare("SELECT * FROM ".$service_finder_Tables->customers." as customers INNER JOIN ".$service_finder_Tables->bookings." as bookings on bookings.booking_customer_id = customers.id WHERE bookings.id = %d",$bookingid);
	
		$row = $wpdb->get_row($sql);
		$feedbackrow = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->feedback.' WHERE booking_id = %d',$bookingid));
		
		if(!isset($_REQUEST['calendar'])){
		$back = '<button type="button" class="btn btn-primary closeDetails"><i class="fa fa-arrow-left"></i>'.esc_html__('Back', 'service-finder').'</button>
';
			if(strtotime($row->date) >= strtotime(date("Y-m-d"))){
				$flag = 1;
			}else{
				$flag = 0;
			}
			
			if($row->multi_date != 'yes' && $flag == 1 && $row->status != "Cancel" && $row->status != "Completed")
			{
			$cancel = '<button type="button" class="btn btn-danger cancelbooking" data-id="'.esc_attr($bookingid).'">'.esc_html__('Cancel Booking', 'service-finder').'</button>';
			}elseif($row->multi_date == 'yes' && $row->status != "Cancel" && $row->status != "Completed"){
			$cancel = '<button type="button" class="btn btn-danger cancelbooking" data-id="'.esc_attr($bookingid).'">'.esc_html__('Cancel Booking', 'service-finder').'</button>';
			}else{
			$cancel = '';
			}
		
		$payoutflag = 0;	
		$payment_methods = (!empty($service_finder_options['payment-methods'])) ? $service_finder_options['payment-methods'] : '';
		
		if($row->charge_admin_fee_from == 'provider'){
			$bookingamount = $row->total - $row->adminfee;
		}elseif($row->charge_admin_fee_from == 'customer'){
			$bookingamount = $row->total;
		}else{
			$bookingamount = $row->total;
		}
		
		if($row->status != "Cancel" && $row->status != "Completed" && $isadmin != "yes"){
			if($row->paid_to_provider == 'pending' && $row->type == "paypal" ){
			$complete .= '<button type="button" data-providerid="'.esc_attr($row->provider_id).'" class="btn btn-primary completebookingandpay" data-amount="'.esc_attr($bookingamount).'" data-id="'.esc_attr($bookingid).'">'.esc_html__('Mark As Complete', 'service-finder').'</button>';
			$payoutflag = 1;
			}elseif($row->paid_to_provider == 'pending' && $row->stripe_token != "" && $payment_methods['stripe']){
			//$complete .= '<button type="button" data-id="'.esc_attr($bookingid).'" data-providerid="'.esc_attr($row->provider_id).'" data-amount="'.esc_attr($bookingamount).'" class="btn btn-primary completebookingandpayviastripe" data-id="'.esc_attr($bookingid).'">'.esc_html__('Mark As Complete', 'service-finder').'</button>';
			$complete .= '<button type="button" class="btn btn-primary completebooking" data-id="'.esc_attr($bookingid).'">'.esc_html__('Mark As Complete', 'service-finder').'</button>';
			$payoutflag = 1;
			}
			
			if($payoutflag == 0){
			$complete .= '<button type="button" class="btn btn-primary completebooking" data-id="'.esc_attr($bookingid).'">'.esc_html__('Mark As Complete', 'service-finder').'</button>';
			}
			
			
			}else{
			$complete = '';
			}
				
		}else{
		$back = '';
		}
		$member = '';
		if($row->member_id > 0){
		if(service_finder_getMemberAvatar($row->member_id) != ""){
		$imgtag = '<img src="'.esc_url(service_finder_getMemberAvatar($row->member_id)).'" width="50" height="50" alt="">';
		}else{
		$imgtag = '';
		}
		$member = '
<tr>
  <td>'.esc_html__('Staff Member', 'service-finder').'</td>
  <td><div class="member-thumb">'.$imgtag.service_finder_getMemberName($row->member_id).'</div></td>
</tr>
';
		}		  
		
		$rating = (!empty($feedbackrow->rating)) ? $feedbackrow->rating : '';;
		$comment = (!empty($feedbackrow->comment)) ? $feedbackrow->comment : '';
		$description = (!empty($row->description)) ? $row->description : '';
		$phone2 = (!empty($row->phone2)) ? $row->phone2 : '';
		$apt = (!empty($row->apt)) ? $row->apt : '';
		
		$jobtitle = '';
		if($row->jobid > 0){
		$jobtitle = '<tr>
						<td>'.esc_html__('Job Title', 'service-finder').'</td>
						<td>'.get_the_title($row->jobid).'</td>
					  </tr>';
		}
		
		if($row->type == 'free'){
			$paymentstatus = esc_html__('Free', 'service-finder');
		}else{
			$paidonlyadminfee = ($row->payonlyadminfee == 'yes') ? '('.service_finder_money_format($row->adminfee).')' : '';
			if($row->status == 'Pending'){
			$paymentstatus = esc_html__('Paid', 'service-finder').' '.$paidonlyadminfee;
			}elseif($row->status == 'Need-Approval'){
			$paymentstatus = esc_html__('Pending', 'service-finder');
			}else{
			$paymentstatus = esc_html__('Paid', 'service-finder').' '.$paidonlyadminfee;
			}
		}
			
			if($row->status == 'Cancel'){
				$bookingstatus = service_finder_translate_static_status_string($row->status);
			}elseif($row->status == 'Pending'){
				$bookingstatus = esc_html__('Incomplete','service-finder');
			}else{
				$bookingstatus = service_finder_translate_static_status_string($row->status);
			}
		
	$admin_fee_label = (!empty($service_finder_options['admin-fee-label'])) ? esc_html($service_finder_options['admin-fee-label']) : esc_html__('Admin Fee', 'service-finder');
	
	if(service_finder_getUserRole($current_user->ID) == 'Customer'){
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
	}elseif(service_finder_getUserRole($current_user->ID) == 'Provider'){
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
	
	$time_format = (!empty($service_finder_options['time-format'])) ? $service_finder_options['time-format'] : '';
	
	if($row->end_time != Null){
	$showtime = service_finder_time_format($row->start_time).'-'.service_finder_time_format(service_finder_get_booking_end_time($row->end_time,$row->end_time_no_buffer));
	}else{
	$showtime = service_finder_time_format($row->start_time);
	}
	
	if($row->payment_type == 'woocommerce'){
	$txnid = $row->order_id;
	$paymenttype = $row->type.' '.esc_html__('(Woocommerce)', 'service-finder');
	}else{
	$txnid = $row->txnid;
	$paymenttype = service_finder_translate_static_status_string($row->type);
	}
	
	$txnid = ($txnid != "") ? $txnid : 'NA';
	
	$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? $service_finder_options['pay_booking_amount_to'] : '';
	
	$extrafield = '';
	if($pay_booking_amount_to == 'admin' && service_finder_getUserRole($current_user->ID) != 'Customer'){
	$extrafield = '<tr>
					<td>'.esc_html__('Admin Pay to Provider', 'service-finder').'</td>
					<td>'.esc_html(service_finder_translate_static_status_string($row->paid_to_provider)).'</td>
				  </tr>';
	}
	
	if(service_finder_getUserRole($current_user->ID) != 'Customer'){
	if($row->payment_type == 'woocommerce' && $row->type == 'mangopay'){
	$bookingamount = $row->total - $row->adminfee;
	}
	
	$adminfeefield = '<tr>
						<td>'.$admin_fee_label.'</td>
						<td>'.service_finder_money_format($adminfee).'</td>
					  </tr>';
	$adminfeefield .= '<tr>
						<td>'.$providerreplacestring.' '.esc_html__('Fee', 'service-finder').'</td>
						<td>'.service_finder_money_format($bookingamount).'</td>
					  </tr>';				  
	}	
	
	$bookingdiscount = '';
	$couponcode = ($row->coupon_code != "") ? esc_attr($row->coupon_code) : 'NA';
	$discount = ($row->discount > 0 && $row->discount != "") ? service_finder_money_format($row->discount) : 'NA';
	
	if($row->dicount_based_on == 'booking'){
	$bookingdiscount = '<tr>
						<td>'.esc_html__('Coupon Code', 'service-finder').'</td>
						<td>'.$couponcode.'</td>
					  </tr>';
	$bookingdiscount .= '<tr>
						<td>'.esc_html__('Discount', 'service-finder').'</td>
						<td>'.$discount.'</td>
					  </tr>';				  
	}
	
	
  if(!$row->jobid > 0 && !$row->quoteid > 0){
  $bookedservices = '<tr>
    <td colspan="2" class="sf-services-table-wrap">'.service_finder_get_booking_services($bookingid).'</td>
  </tr>';
  }else{
  $bookedservices = '';
  }
		  
		
		$html = '<div class="margin-b-30 text-right"> '.$complete.' '.$cancel.' '.$back.' </div>
<table class="table table-striped table-bordered" border="0">
  '.$jobtitle.'	
  <tr>
    <td>'.esc_html__('Booking Ref ID #', 'service-finder').'</td>
    <td>'.esc_html($row->id).'</td>
  </tr>
  <tr>
    <td>'.esc_html( $customerreplacestring ).' '.esc_html__('Name', 'service-finder').'</td>
    <td>'.esc_html($row->name).'</td>
  </tr>
  <tr>
    <td>'.esc_html__('Email', 'service-finder').'</td>
    <td>'.esc_html($row->email).'</td>
  </tr>';
if($row->multi_date != 'yes'){
$html .= '<tr>
    <td>'.esc_html__('Date', 'service-finder').'</td>
    <td>'.service_finder_date_format($row->date).'</td>
  </tr>
  <tr>
    <td>'.esc_html__('Time', 'service-finder').'</td>
    <td>'.$showtime.'</td>
  </tr>';
}  
$html .= '<tr>
    <td>'.esc_html__('Phone', 'service-finder').'</td>
    <td>'.esc_html($row->phone).'</td>
  </tr>
  <tr>
    <td>'.esc_html__('Phone2', 'service-finder').'</td>
    <td>'.esc_html($phone2).'</td>
  </tr>
  '.$member.'
  <tr>
    <td>'.esc_html__('Service Location', 'service-finder').'</td>
    <td>'.service_finder_get_service_location($bookingid).'</td>
  </tr>
  <tr>
    <td>'.esc_html__('Apartment', 'service-finder').'</td>
    <td>'.esc_html($apt).'</td>
  </tr>
  <tr>
    <td>'.esc_html__('Address', 'service-finder').'</td>
    <td>'.esc_html($row->address).'</td>
  </tr>
  <tr>
    <td>'.esc_html__('City', 'service-finder').'</td>
    <td>'.esc_html($row->city).'</td>
  </tr>
  <tr>
    <td>'.esc_html__('State', 'service-finder').'</td>
    <td>'.$row->state.'</td>
  </tr>
  <tr>
    <td>'.esc_html__('Postal Code', 'service-finder').'</td>
    <td>'.esc_html($row->zipcode).'</td>
  </tr>
  <tr>
    <td>'.esc_html__('Country', 'service-finder').'</td>
    <td>'.esc_html($row->country).'</td>
  </tr>
  <tr>
    <td>'.esc_html__('Short Description', 'service-finder').'</td>
    <td>'.nl2br(stripcslashes($description)).'</td>
  </tr>
  '.$bookedservices.'
  <tr>
    <td>'.esc_html__('Rating', 'service-finder').'</td>
    <td>'.service_finder_displayRating($rating).'</td>
  </tr>
  <tr>
    <td>'.esc_html__('Feedback', 'service-finder').'</td>
    <td>'.esc_html($comment).'</td>
  </tr>
  <tr>
    <td>'.esc_html__('Total Amount', 'service-finder').'</td>
    <td>'.service_finder_money_format($totalamount).'</td>
  </tr>
  '.$adminfeefield.'
  '.$bookingdiscount.'
  <tr>
    <td>'.esc_html__('Booking Status', 'service-finder').'</td>
    <td>'.esc_html($bookingstatus).'</td>
  </tr>
  <tr>
    <td>'.esc_html__('Payment Type', 'service-finder').'</td>
    <td>'.$paymenttype.'</td>
  </tr>
  <tr>
    <td>'.esc_html__('Payment Status', 'service-finder').'</td>
    <td>'.esc_html($paymentstatus).'</td>
  </tr>
  <tr>
    <td>'.esc_html__('Txn ID', 'service-finder').'</td>
    <td>'.esc_html($txnid).'</td>
  </tr>
  '.$extrafield.'
</table>
';
		
		echo $html;
	}
	
	/*Display customer past bookings into datatable*/
	public function service_finder_getCustomerPastBookings(){
		global $wpdb, $service_finder_Tables, $service_finder_Params, $service_finder_options;
		$requestData= $_REQUEST;
		$currUser = wp_get_current_user(); 

		$columns = array( 
			0 =>'date', 
			1=> 'start_time',
			2 => 'end_time',
			3 =>'full_name', 
			4=> 'phone',
			5 => 'email',
		);
		
		// getting total number records without any search
		$sql = $wpdb->prepare("SELECT bookings.*, customers.address, providers.full_name, providers.wp_user_id, providers.phone, providers.avatar_id, providers.email, customers.address FROM ".$service_finder_Tables->bookings." as bookings INNER JOIN ".$service_finder_Tables->customers." as customers INNER JOIN ".$service_finder_Tables->providers." as providers on bookings.booking_customer_id = customers.id AND bookings.provider_id = providers.wp_user_id WHERE ((bookings.date < CURDATE() AND bookings.multi_date != 'yes') OR (bookings.status = 'Completed' AND bookings.multi_date = 'yes')) AND customers.`wp_user_id` = %d",$currUser->ID);
		$query=$wpdb->get_results($sql);
		$totalData = count($query);
		$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
		$sql = "SELECT bookings.*, customers.address, providers.full_name, providers.wp_user_id, providers.phone, providers.avatar_id, providers.email, customers.address";
		$sql.=" FROM ".$service_finder_Tables->bookings." as bookings INNER JOIN ".$service_finder_Tables->customers." as customers INNER JOIN ".$service_finder_Tables->providers." as providers on bookings.booking_customer_id = customers.id AND bookings.provider_id = providers.wp_user_id WHERE ((bookings.date < CURDATE() AND bookings.multi_date != 'yes') OR (bookings.status = 'Completed' AND bookings.multi_date = 'yes')) AND customers.`wp_user_id` = ".$currUser->ID;
		if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
			$sql.=" AND ( customers.name LIKE '".$requestData['search']['value']."%' ";    
			$sql.=" OR bookings.id LIKE '".$requestData['search']['value']."%' ";
			$sql.=" OR bookings.date LIKE '".$requestData['search']['value']."%' ";
			$sql.=" OR bookings.start_time LIKE '".$requestData['search']['value']."%' ";
			$sql.=" OR bookings.end_time LIKE '".$requestData['search']['value']."%' ";
			$sql.=" OR providers.full_name LIKE '".$requestData['search']['value']."%' ";
			$sql.=" OR providers.phone LIKE '".$requestData['search']['value']."%' ";
			$sql.=" OR providers.email LIKE '".$requestData['search']['value']."%' ";
			$sql.=" OR bookings.status LIKE '".$requestData['search']['value']."%' )";
		}
		
		$sql.=" ORDER BY bookings.id DESC";
		
		$query=$wpdb->get_results($sql);
		
		$totalFiltered = count($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 

		$data = array();
		
		foreach($query as $result){
			$nestedData=array(); 
			
			if(!empty($result->avatar_id) && $result->avatar_id > 0){
				$src  = wp_get_attachment_image_src( $result->avatar_id, 'service_finder-provider-thumb' );
				$src  = $src[0];
				$imgtag = '<img class="img-thumbnail" src="'.esc_url($src).'" alt="">';
			}else{
				$imgtag = '<img class="img-thumbnail" src="'.esc_url($service_finder_Params['pluginImgUrl'].'/'.'no_img.jpg').'" alt="">';
			}
			
			$feedback = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->feedback.' WHERE booking_id = %d',$result->id));
			
			$time_format = (!empty($service_finder_options['time-format'])) ? $service_finder_options['time-format'] : '';
	
			if($result->end_time != Null){
			$showtime = service_finder_time_format($result->start_time).' TO '.service_finder_time_format(service_finder_get_booking_end_time($result->end_time,$result->end_time_no_buffer));
			}else{
			$showtime = service_finder_time_format($result->start_time);
			}
			
			$userLink = service_finder_get_author_url($result->provider_id);
			$nestedData[] = '<div class="provider-pic">
<div class="thum-bx">'.$imgtag.'</div>
';
			if($result->status == 'Cancel'){
				$status = esc_html__('Cancelled', 'service-finder');
				$statusclass = 'sf-booking-cancelled';
			}elseif($result->status == 'Completed'){
				$status = esc_html__('Completed', 'service-finder');
				$statusclass = 'sf-booking-completed';
			}else{
				$status = esc_html__('Incomplete','service-finder');
				$statusclass = 'sf-booking-incomplete';
			}
			
			if($result->jobid > 0){
				$type = esc_html__('Job','service-finder');
			}elseif($result->quoteid > 0){
				$type = esc_html__('Quotation','service-finder');
			}else{
				$type = esc_html__('Booking','service-finder');
			}
			
			$multidate = ($result->multi_date == 'yes') ? $result->multi_date : 'no';
			
			$services = '';
			if($result->jobid == 0 && $result->quoteid == 0){
			if($multidate == 'yes'){
			$services = '<a href="javascript:;" class="btn btn-primary" data-toggle="collapse" data-target="#services-summary-'.esc_attr($result->id).'"><i class="fa fa-plus"></i> '.esc_html__('Booked Services','service-finder').'</a>
						<div class="collapse" id="services-summary-'.esc_attr($result->id).'">
							'.service_finder_get_booking_services_summary($result->id).'
						</div>';
			}
			}
			
			$bookingdetails = '<div class="sf-booking-info-col">
										<span class="sf-booking-refid">#'.$result->id.'</span> <span class="booking-status '.sanitize_html_class($statusclass).'">'.$status.'</span>
										<div class="sf-booking-upcoming">
											'.$type.'
										</div>

										<div class="sf-booking-customer">
											<ul class="customer-info">
												<li><strong><i class="fa fa-user"></i> '.esc_html__('Name', 'service-finder').'</strong> <a href="'.esc_url($userLink).'">'.$result->full_name.'</a></li>
												<li><strong><i class="fa fa-envelope"></i> '.esc_html__('Email', 'service-finder').'</strong> '.$result->email.'</li>';
												if($multidate == 'no' || $result->jobid > 0 || $result->quoteid > 0){
							$bookingdetails .= '<li><strong><i class="fa fa-calendar-o"></i> '.esc_html__('Date', 'service-finder').'</strong> '.service_finder_date_format($result->date).'</li>
												<li><strong><i class="fa fa-clock-o"></i> '.esc_html__('Time', 'service-finder').'</strong> '.$showtime.'</li>';
												}
							$bookingdetails .= '<li><strong><i class="fa fa-map-marker-alt"></i> '.esc_html__('Address', 'service-finder').'</strong> '.$result->address.'</li>
												<li>'.service_finder_displayRating(service_finder_getAverageRating($result->wp_user_id)).'</li>
											</ul>
										</div>';
										
							$bookingdetails .= $services;
							$bookingdetails .= '</div>';
			
			$nestedData[] = $bookingdetails;
			
			$paymentstatus = '';
			if($result->type == 'free'){
				$paymentstatus = esc_html__('Free', 'service-finder');
			}else{
				if($result->status == 'Pending' || $result->status == 'Completed'){
				$paymentstatus = esc_html__('Paid', 'service-finder');
				}elseif($result->status == 'Need-Approval'){
				$paymentstatus = esc_html__('Pending', 'service-finder');
				}else{
				$paymentstatus = esc_html__('Paid', 'service-finder');
				}
			}
			
			if($result->charge_admin_fee_from == 'provider'){
				$bookingamount = $result->total - $result->adminfee;
			}elseif($result->charge_admin_fee_from == 'customer'){
				$bookingamount = $result->total;
			}else{
				$bookingamount = $result->total;
			}
			
			$txnid = ($result->txnid != "") ? $result->txnid : 'NA';
			
			$paymentdetails = '<div class="inner">
										<h3><span class="sf-booking-payment-info" data-toggle="popover" data-container="body" data-placement="top" data-html="true" id="payinfo-'.$result->id.'" data-trigger="hover">'.service_finder_money_format($result->total).' </span><span class="sf-payment-status">'.$paymentstatus.'</span></h3>
                                <div id="popover-content-payinfo-'.$result->id.'" class="hide">
                                    <ul class="list-unstyled margin-0 booking-payment-data">
                                        <li><strong>'.esc_html__('Booking Amount','service-finder').':</strong> '.service_finder_money_format($result->total).'</li>
                                        <li><strong>'.esc_html__('Payment Method','service-finder').':</strong> '.service_finder_translate_static_status_string($result->type).'</li>
                                        <li><strong>'.esc_html__('Transaction ID','service-finder').':</strong> '.$txnid.'</li>
                                    </ul>
                                </div>
									</div>';
			
			$nestedData[] = $paymentdetails;
			
			$option = '';
			if($service_finder_options['review-system']){
			if(!empty($feedback)){
			$option .= '<option value="viewfeedback">'.esc_html__('View Feedback', 'service-finder').'</option>';
			}else{
			$option .= '<option value="addfeedback">'.esc_html__('Add Feedback', 'service-finder').'</option>';
			}
			}
			
			if($this->service_finder_booking_has_invoice($result->id))
			{
			$option .= '<option value="invoice">'.esc_html__('View Invoice', 'service-finder').'</option>';
			}
			
			$nestedData[] = '<div class="booking-option text-right">
  <select title="'.esc_html__('Option', 'service-finder').'" class="bookingOptionSelect form-control sf-form-control sf-select-box" data-bid="'.esc_attr($result->id).'">
    <option value="">'.esc_html__('Select Option', 'service-finder').'</option>
    <option value="booking">'.esc_html__('View Booking', 'service-finder').'</option>'.$option.'
  </select>
</div>';
			$data[] = $nestedData;
		}
		
		
		
		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
					"recordsTotal"    => intval( $totalData ),  // total number of records
					"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
					"data"            => $data   // total data array
					);
		
		echo json_encode($json_data);  // send data as json format
	}
	
	/*Get Booked Services*/
	public function service_finder_getBookedServices($arg){
		global $wpdb, $service_finder_Tables, $service_finder_options;
		$requestData= $_REQUEST;
		$bookingid = (!empty($arg['editbookingid'])) ? $arg['editbookingid'] : '';
		$columns = array( 
			0 =>'id', 
			1 =>'date', 
			2 =>'start_time', 
			3 =>'end_time', 
			4 =>'status', 
			5 =>'fullday', 
		);
		
		// getting total number records without any search
		$sql = $wpdb->prepare("SELECT * FROM ".$service_finder_Tables->booked_services." WHERE `booking_id` = %d GROUP BY `service_id`",$bookingid);
		$query=$wpdb->get_results($sql);
		$totalData = count($query);
		$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		$sql = "SELECT * FROM ".$service_finder_Tables->booked_services." WHERE `booking_id` = ".$bookingid." GROUP BY `service_id`";
		if( !empty($requestData['search']['value']) ) {   
			$sql.=" AND (( `date` LIKE '".$requestData['search']['value']."%' )";    
			$sql.=" OR ( `start_time` LIKE '".$requestData['search']['value']."%' )";    
			$sql.=" OR ( `end_time` LIKE '".$requestData['search']['value']."%' )";    
			$sql.=" OR ( `status` LIKE '".$requestData['search']['value']."%' )";    
			$sql.=" OR ( `fullday` LIKE '".$requestData['search']['value']."%' ))";    
		}
		
		$query=$wpdb->get_results($sql);
		$totalFiltered = count($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." DESC LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		$query=$wpdb->get_results($sql);
		$data = array();
		
		foreach($query as $result){
			$nestedData=array(); 
			
			$bookingrow = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$service_finder_Tables->bookings." WHERE `id` = %d",$bookingid));
			
			$rowstarttime = ($result->without_padding_start_time != NULL) ? $result->without_padding_start_time : $result->start_time;
			$rowendtime = ($result->without_padding_end_time != NULL) ? $result->without_padding_end_time : $result->end_time;

			$nestedData[] = service_finder_get_service_name($result->service_id);
			$nestedData[] = service_finder_date_format($result->date);
			$nestedData[] = ($rowstarttime != NULL) ? $rowstarttime : '-';
			$nestedData[] = ($rowendtime != NULL) ? $rowendtime : '-';
			$nestedData[] = ($result->fullday != "") ? $result->fullday : '-';
			$nestedData[] = $result->status;
			$nestedData[] = ($result->member_id > 0) ? service_finder_getMemberName($result->member_id) : '-';
			$costtype = service_finder_get_service_type($result->service_id);
			
			if($costtype == 'days'){
				$totalrows = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$service_finder_Tables->booked_services." WHERE `booking_id` = %d AND `service_id` = %d",$bookingid,$result->service_id));
				$totalnumber = count($totalrows);
			}else{
				$totalnumber = $result->hours;
			}
			
			$userCap = service_finder_get_capability($bookingrow->provider_id);
			$settings = service_finder_getProviderSettings($bookingrow->provider_id);
			
			$members_available = false;
			if(!empty($userCap)){
			if(in_array('staff-members',$userCap) && in_array('bookings',$userCap)){
			if($settings['members_available'] == 'yes'){
			$members_available = true;
			}
			}
			}
			
			$nestedData[] = '<a href="javascript:;" class="btn btn-primary editservice" data-membersavailable="'.$members_available.'" data-costtype="'.$costtype.'" data-totalnumber="'.$totalnumber.'" data-providerid="'.$bookingrow->provider_id.'" data-bookedserviceid="'.$result->id.'" data-serviceid="'.$result->service_id.'" data-bookingid="'.$result->booking_id.'" data-memberid="'.$result->member_id.'">'.esc_html__('Edit', 'service-finder').'</a>';
			
			$data[] = $nestedData;
		}
		
		
		
		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
					"recordsTotal"    => intval( $totalData ),  // total number of records
					"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
					"data"            => $data   // total data array
					);
		
		echo json_encode($json_data);  // send data as json format
		exit(0);
	}
	
	public function service_finder_booking_has_invoice($bookingid = ''){
		global $wpdb, $service_finder_Tables;
		
		$res = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->invoice.' WHERE booking_id = %d',$bookingid));
		
		if(!empty($res))
		{
			return true;
		}else{
			return false;
		}
	}
	
	/*Display customer upcoming bookings into datatable*/
	public function service_finder_getCustomerUpcomingBookings(){
		global $wpdb, $service_finder_Tables, $service_finder_Params, $service_finder_options;
		$requestData= $_REQUEST;
		$currUser = wp_get_current_user(); 

		$columns = array( 
		// datatable column index  => database column name
			0 =>'date', 
			1=> 'start_time',
			2 => 'end_time',
			3 =>'full_name', 
			4=> 'phone',
			5 => 'email',
			8=> 'status'
		);
		
		// getting total number records without any search
		$sql = $wpdb->prepare("SELECT bookings.*, providers.full_name, providers.avatar_id, providers.phone, providers.email, customers.address FROM ".$service_finder_Tables->bookings." as bookings INNER JOIN ".$service_finder_Tables->customers." as customers INNER JOIN ".$service_finder_Tables->providers." as providers on bookings.booking_customer_id = customers.id AND bookings.provider_id = providers.wp_user_id WHERE ((bookings.date >= CURDATE() AND bookings.multi_date != 'yes') OR ((bookings.status = 'Pending' OR bookings.status = 'Need-Approval') AND bookings.multi_date = 'yes')) AND customers.`wp_user_id` = %d",$currUser->ID);
		$query=$wpdb->get_results($sql);
		$totalData = count($query);
		$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
		$sql = "SELECT bookings.*, providers.full_name, providers.avatar_id, providers.phone, providers.email, customers.address";
		$sql.=" FROM ".$service_finder_Tables->bookings." as bookings INNER JOIN ".$service_finder_Tables->customers." as customers INNER JOIN ".$service_finder_Tables->providers." as providers on bookings.booking_customer_id = customers.id AND bookings.provider_id = providers.wp_user_id WHERE ((bookings.date >= CURDATE() AND bookings.multi_date != 'yes') OR ((bookings.status = 'Pending' OR bookings.status = 'Need-Approval') AND bookings.multi_date = 'yes')) AND customers.`wp_user_id` = ".$currUser->ID;
		if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
			$sql.=" AND ( customers.name LIKE '".$requestData['search']['value']."%' ";    
			$sql.=" OR bookings.id LIKE '".$requestData['search']['value']."%' ";
			$sql.=" OR bookings.date LIKE '".$requestData['search']['value']."%' ";
			$sql.=" OR bookings.start_time LIKE '".$requestData['search']['value']."%' ";
			$sql.=" OR bookings.end_time LIKE '".$requestData['search']['value']."%' ";
			$sql.=" OR providers.full_name LIKE '".$requestData['search']['value']."%' ";
			$sql.=" OR providers.phone LIKE '".$requestData['search']['value']."%' ";
			$sql.=" OR providers.email LIKE '".$requestData['search']['value']."%' ";
			$sql.=" OR bookings.status LIKE '".$requestData['search']['value']."%' )";
		}
		$sql.=" ORDER BY bookings.id DESC";
		$query=$wpdb->get_results($sql);
		$totalFiltered = count($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 

		$data = array();
		
		foreach($query as $result){
			$nestedData=array(); 
			
			$feedback = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->feedback.' WHERE booking_id = %d',$result->id));
			
			if(!empty($result->avatar_id) && $result->avatar_id > 0){
				$src  = wp_get_attachment_image_src( $result->avatar_id, 'service_finder-provider-thumb' );
				$src  = $src[0];
				$imgtag = '<img class="img-thumbnail" src="'.esc_url($src).'" alt="">';
			}else{
				$imgtag = '<img class="img-thumbnail" src="'.esc_url($service_finder_Params['pluginImgUrl'].'/'.'no_img.jpg').'" alt="">';
			}
			
			$option = '';
			if($result->status == 'Cancel'){
				$status = esc_html__('Cancelled','service-finder');
				$statusclass = 'sf-booking-cancelled';
			}elseif($result->status == 'Completed'){
				$status = esc_html__('Completed','service-finder');
				$statusclass = 'sf-booking-completed';
				if($this->service_finder_booking_has_invoice($result->id))
				{
				$option .= '<option value="invoice">'.esc_html__('View Invoice', 'service-finder').'</option>';
				}
				if(!empty($feedback)){
				$option .= '<option value="viewfeedback">'.esc_html__('View Feedback', 'service-finder').'</option>';
				}else{
				$option .= '<option value="addfeedback">'.esc_html__('Add Feedback', 'service-finder').'</option>';
				}
			}else{
				$status = esc_html__('Incomplete','service-finder');
				$statusclass = 'sf-booking-incomplete';
				if($this->service_finder_booking_has_invoice($result->id))
				{
				$option .= '<option value="invoice">'.esc_html__('View Invoice', 'service-finder').'</option>';
				}
				if($result->jobid == 0 && $result->quoteid == 0){
				if($result->multi_date == 'yes'){
				$option .= '<option value="editservices" data-bookingid="'.$result->id.'">'.esc_html__('Edit Services', 'service-finder').'</option>';
				}else{
				$option .= '<option value="editbooking">'.esc_html__('Edit Bookings', 'service-finder').'</option>';
				}
				}else{
				$option .= '<option value="editbooking">'.esc_html__('Edit Bookings', 'service-finder').'</option>';
				}
			}
			
			$time_format = (!empty($service_finder_options['time-format'])) ? $service_finder_options['time-format'] : '';
			
			if($result->end_time != Null){
			$showtime = service_finder_time_format($result->start_time).' TO '.service_finder_time_format(service_finder_get_booking_end_time($result->end_time,$result->end_time_no_buffer));
			}else{
			$showtime = service_finder_time_format($result->start_time);
			}
			
			$userLink = service_finder_get_author_url($result->provider_id);
			$nestedData[] = '<div class="provider-pic"><div class="thum-bx">'.$imgtag.'</div>';
			
			if($result->jobid > 0){
				$type = esc_html__('Job','service-finder');
			}elseif($result->quoteid > 0){
				$type = esc_html__('Quotation','service-finder');
			}else{
				$type = esc_html__('Booking','service-finder');
			}
			
			$multidate = ($result->multi_date == 'yes') ? $result->multi_date : 'no';
			
			$services = '';
			if($result->jobid == 0 && $result->quoteid == 0){
			if($multidate == 'yes'){
			$services = '<a href="javascript:;" class="btn btn-primary" data-toggle="collapse" data-target="#services-summary-'.esc_attr($result->id).'"><i class="fa fa-plus"></i> '.esc_html__('Booked Services','service-finder').'</a>
						<div class="collapse" id="services-summary-'.esc_attr($result->id).'">
							'.service_finder_get_booking_services_summary($result->id).'
						</div>';
			}
			}
			
			$bookingdetails = '<div class="sf-booking-info-col">
										<span class="sf-booking-refid">#'.$result->id.'</span> <span class="booking-status '.sanitize_html_class($statusclass).'">'.$status.'</span>
										<div class="sf-booking-upcoming">
											'.$type.'
										</div>

										<div class="sf-booking-customer">
											<ul class="customer-info">
												<li><strong><i class="fa fa-user"></i> '.esc_html__('Name', 'service-finder').'</strong> <a href="'.esc_url($userLink).'">'.$result->full_name.'</a></li>
												<li><strong><i class="fa fa-envelope"></i> '.esc_html__('Email', 'service-finder').'</strong> '.$result->email.'</li>';
												if($multidate == 'no' || $result->jobid > 0 || $result->quoteid > 0){
							$bookingdetails .= '<li><strong><i class="fa fa-calendar-o"></i> '.esc_html__('Date', 'service-finder').'</strong> '.service_finder_date_format($result->date).'</li>
												<li><strong><i class="fa fa-clock-o"></i> '.esc_html__('Time', 'service-finder').'</strong> '.$showtime.'</li>';
												}
							$bookingdetails .= '<li><strong><i class="fa fa-map-marker"></i> '.esc_html__('Address', 'service-finder').'</strong> '.$result->address.'</li>
											</ul>
										</div>';
							$bookingdetails .= $services;			
							$bookingdetails .= '</div>';
			
			$nestedData[] = $bookingdetails;
			
			$paymentstatus = '';
			if($result->type == 'free'){
				$paymentstatus = esc_html__('Free', 'service-finder');
			}else{
				if($result->status == 'Pending' || $result->status == 'Completed'){
				$paymentstatus = esc_html__('Paid', 'service-finder');
				}elseif($result->status == 'Need-Approval'){
				$paymentstatus = esc_html__('Pending', 'service-finder');
				}else{
				$paymentstatus = esc_html__('Paid', 'service-finder');
				}
			}
			
			if($result->charge_admin_fee_from == 'provider'){
				$bookingamount = $result->total - $result->adminfee;
			}elseif($result->charge_admin_fee_from == 'customer'){
				$bookingamount = $result->total;
			}else{
				$bookingamount = $result->total;
			}
			
			$txnid = ($result->txnid != "") ? $result->txnid : 'NA';
			
			$paymentdetails = '<div class="inner">
										<h3><span class="sf-booking-payment-info" data-toggle="popover" data-container="body" data-placement="top" data-html="true" id="payinfo-'.$result->id.'" data-trigger="hover">'.service_finder_money_format($result->total).' </span><span class="sf-payment-status">'.$paymentstatus.'</span></h3>
                                <div id="popover-content-payinfo-'.$result->id.'" class="hide">
                                    <ul class="list-unstyled margin-0 booking-payment-data">
                                        <li><strong>'.esc_html__('Booking Amount','service-finder').':</strong> '.service_finder_money_format($result->total).'</li>
                                        <li><strong>'.esc_html__('Payment Method','service-finder').':</strong> '.service_finder_translate_static_status_string($result->type).'</li>
                                        <li><strong>'.esc_html__('Transaction ID','service-finder').':</strong> '.$txnid.'</li>
                                    </ul>
                                </div>
									</div>';
			
			$nestedData[] = $paymentdetails;
			
			$nestedData[] = '<div class="booking-option text-right">
  <select title="'.esc_html__('Option','service-finder').'" class="bookingOptionSelect form-control sf-form-control sf-select-box" data-upcoming="yes" data-bid="'.esc_attr($result->id).'">
    <option value="">'.esc_html__('Select Option', 'service-finder').'</option>
    <option value="booking">'.esc_html__('View Booking', 'service-finder').'</option>
    
												'.$option.'
                                            
  </select>
</div>
';
			

			$data[] = $nestedData;
		}
		
		
		
		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
					"recordsTotal"    => intval( $totalData ),  // total number of records
					"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
					"data"            => $data   // total data array
					);
		
		echo json_encode($json_data);  // send data as json format
	}
	
	/*Delete cutomer Bookings*/
	public function service_finder_deleteCustomerBookings(){
	global $wpdb, $service_finder_Tables;
			$data_ids = $_REQUEST['data_ids'];
			$data_id_array = explode(",", $data_ids); 
			if(!empty($data_id_array)) {
				foreach($data_id_array as $id) {
					$sql = $wpdb->prepare("DELETE FROM ".$service_finder_Tables->bookings." WHERE id = %d",$id);
					$query=$wpdb->query($sql);
					$sql = $wpdb->prepare("DELETE FROM ".$service_finder_Tables->booked_services." WHERE booking_id = %d",$id);
					$query=$wpdb->query($sql);
					$sql = $wpdb->prepare("DELETE FROM ".$service_finder_Tables->notifications." WHERE `topic` = 'Booking' AND `target_id` = %d",$id);
					$query=$wpdb->query($sql);
				}
			}
	}
	
	/*Add Invoice*/
	public function service_finder_addInvoiceData($arg){
		
		global $wpdb, $service_finder_Tables, $service_finder_Params, $service_finder_options;
			$services = array_map(null, $arg['service_title'], $arg['cost_type'], $arg['num_hours'], $arg['service_desc'], $arg['service_price']);
			
			$services = serialize($services);
			
			$currUser = wp_get_current_user(); 
			$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';
			
			$admin_fee_type = (!empty($service_finder_options['admin-fee-type'])) ? $service_finder_options['admin-fee-type'] : 0;
			$admin_fee_percentage = (!empty($service_finder_options['admin-fee-percentage'])) ? $service_finder_options['admin-fee-percentage'] : 0;
			$admin_fee_fixed = (!empty($service_finder_options['admin-fee-fixed'])) ? $service_finder_options['admin-fee-fixed'] : 0;
			
			$charge_admin_fee = (!empty($service_finder_options['charge-admin-fee'])) ? $service_finder_options['charge-admin-fee'] : '';
			$charge_admin_fee_from = (!empty($service_finder_options['charge-admin-fee-from'])) ? $service_finder_options['charge-admin-fee-from'] : '';
			
			$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? esc_html($service_finder_options['pay_booking_amount_to']) : '';
			
			if($charge_admin_fee && $pay_booking_amount_to == 'admin' && (($admin_fee_type == 'fixed' && $admin_fee_fixed > 0) || ($admin_fee_type == 'percentage' && $admin_fee_percentage > 0)) && $charge_admin_fee_from == 'customer'){
			
				if($admin_fee_type == 'fixed'){
					$adminfee = $admin_fee_fixed;
				}elseif($admin_fee_type == 'percentage'){
					$adminfee = $arg['gtotal'] * ($admin_fee_percentage/100);	
				}
				
				$gtotal = $arg['gtotal'] + $adminfee;
			}elseif($charge_admin_fee && $pay_booking_amount_to == 'admin' && (($admin_fee_type == 'fixed' && $admin_fee_fixed > 0) || ($admin_fee_type == 'percentage' && $admin_fee_percentage > 0)) && $charge_admin_fee_from == 'provider'){
				if($admin_fee_type == 'fixed'){
					$adminfee = $admin_fee_fixed;
				}elseif($admin_fee_type == 'percentage'){
					$adminfee = $arg['gtotal'] * ($admin_fee_percentage/100);	
				}
				$gtotal = $arg['gtotal'];
			}else{
				$adminfee = 0;
				$gtotal = $arg['gtotal'];
			}
			
			
			$data = array(
					'reference_no'	=>   esc_attr($arg['refno']),
					'duedate'      	=> 	 esc_attr($arg['dueDate']),
					'provider_id'   => 	 $user_id,
					'customer_email'   => esc_attr($arg['customer']),
					'booking_id'   => esc_attr($arg['bookingid']),
					'discount_type' =>   esc_attr($arg['discount-type']),
					'tax_type'      =>	 esc_attr($arg['tax-type']),
					'discount'      =>   esc_attr($arg['discount']),
					'tax'           =>	 esc_attr($arg['tax']),
					'services' 	    => 	 $services,
					'description' 	=>   esc_attr($arg['short-desc']),
					'total'         =>   esc_attr($arg['total']),
					'grand_total'   =>   $gtotal,
					'status'	    => 	 esc_attr($arg['status']),
					'charge_admin_fee_from' 	=>   $charge_admin_fee_from,
					'adminfee'	    => 	 $adminfee
					);

			$wpdb->insert($service_finder_Tables->invoice,wp_unslash($data));
			
			$invoice_id = $wpdb->insert_id;
			
			if ( ! $invoice_id ) {
				$adminemail = get_option( 'admin_email' );
				$allowedhtml = array(
					'a' => array(
						'href' => array(),
						'title' => array()
					),
				);
				$error = array(
						'status' => 'error',
						'err_message' => sprintf( wp_kses(esc_html__('Couldn&#8217;t add invoice... please contact the <a href="mailto:%s">Administrator</a> !', 'service-finder'),$allowedhtml), $adminemail )
						);
				echo json_encode($error);
			}else{
				
				$users = $wpdb->prefix . 'users';
				$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$users.' WHERE `user_email` = "%s"',$arg['customer']));
				
				$urole = service_finder_getUserRole($row->ID);
				
				if(empty($row)){
				$userLink = service_finder_get_invoice_author_url($user_id,'',$invoice_id);
				}elseif($urole == 'Provider'){
				$userLink = service_finder_get_invoice_author_url($user_id,'',$invoice_id);
				}else{
				$userLink = '';
				}
				
				if(function_exists('service_finder_add_notices')) {
					$noticedata = array(
							'customer_id' => $row->ID,
							'target_id' => $invoice_id, 
							'topic' => 'Generate Invoice',
							'title' => esc_html__('Generate Invoice', 'service-finder'),
							'notice' => sprintf( esc_html__('New Invoice generated by %s. Please pay it soon. Invoice Ref id is #%d', 'service-finder'), ucfirst(service_finder_getProviderName($user_id)), $arg['refno'] )
							);
					service_finder_add_notices($noticedata);
				
				}
				
				/*Send Invoice mail to customer*/
				$this->service_finder_SendInvoiceMailToCustomer($data,$userLink,$invoice_id);
				
				$success = array(
						'status' => 'success',
						'suc_message' => esc_html__('Invoice generated successfully.', 'service-finder'),
						'invoiceid' => $invoice_id,
						);
				echo json_encode($success);
			}
	}	
	
	/*Load Edit Members List*/
	public function service_finder_loadEditMembersList($arg){
		global $wpdb, $service_finder_Tables, $service_finder_Params, $service_finder_options;
			
			$bookingid = (!empty($arg['bookingid'])) ? $arg['bookingid'] : '';
			$serviceid = (!empty($arg['serviceid'])) ? $arg['serviceid'] : '';
			$memberid = (!empty($arg['memberid'])) ? $arg['memberid'] : '';
			
			$row = $wpdb->get_row($wpdb->prepare('SELECT bookings.*,customers.* FROM '.$service_finder_Tables->bookings.' as bookings INNER JOIN '.$service_finder_Tables->customers.' as customers on bookings.booking_customer_id = customers.id WHERE bookings.id = %d',$bookingid));
			
			if(!empty($row)){

				$settings = service_finder_getProviderSettings($row->provider_id);
				$html = '';
				if($settings['members_available'] == 'yes'){
					$zipcode = $row->zipcode;
					$region = $row->region;
					$provider_id = $row->provider_id;
					$sid = $serviceid;
					
					$members = service_finder_getStaffMembersList($provider_id,$sid,$zipcode,$region);
					
					$html .= '<option value="0">'.esc_html__('Any Member', 'service-finder').'</option>';
					
					if(!empty($members)){
						foreach($members as $member){
							if($memberid == $member->id){
								$select = 'selected="selected"';
							}else{
								$select = '';
							}
							$html .= '<option '.$select.' value="'.$member->id.'">'.$member->member_name.'</option>';
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
			
				
			
	}
	
	/*Send Invoice Mail to Customer*/
	public function service_finder_SendInvoiceMailToCustomer($maildata = '',$userLink = '',$invoiceid = ''){
		global $wpdb, $service_finder_options, $service_finder_Tables;

			if(!empty($service_finder_options['invoice-to-customer'])){
				$message = $service_finder_options['invoice-to-customer'];
			}else{
				$message = '
<h4>Invoice Details</h4>
Reference No: %REFERENCENO%
							
							Due date: %DUEDATE%
							
							Provider Name: %PROVIDERNAME%
							
							Discount Type: %DISCOUNTTYPE%
							
							Discount: %DISCOUNT%
							
							Tax: %TAX%
							
							Description: %DESCRIPTION%
							
							Total: %TOTAL%
							
							Grand Total: %GRANDTOTAL%';
			}
								
			if($userLink != ""){
			$message .= '<br/>
<br/>
<a href="'.esc_url($userLink).'">'.esc_html__('Pay Now','service-finder').'</a>';
			}
			$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$maildata['provider_id']));
			$customerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `email` = "%s" GROUP BY email',$maildata['customer_email']));
			
			$tokens = array('%REFERENCENO%','%DUEDATE%','%PROVIDERNAME%','%DISCOUNTTYPE%','%DISCOUNT%','%TAX%','%DESCRIPTION%','%TOTAL%','%GRANDTOTAL%','%CUSTOMERNAME%','%CUSTOMEREMAIL%','%CUSTOMERPHONE%','%CUSTOMERPHONE2%','%ADDRESS%','%APT%','%CITY%','%STATE%','%ZIPCODE%','%COUNTRY%');
			$replacements = array($maildata['reference_no'],service_finder_date_format($maildata['duedate']),service_finder_get_providername_with_link($row->wp_user_id),$maildata['discount_type'],$maildata['discount'],$maildata['tax'],$maildata['description'],service_finder_money_format($maildata['total']),service_finder_money_format($maildata['grand_total']),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country);
			$msg_body = str_replace($tokens,$replacements,$message);
			
			if($service_finder_options['invoice-to-customer-subject'] != ""){
				$msg_subject = $service_finder_options['invoice-to-customer-subject'];
			}else{
				$msg_subject = esc_html__('Invoice Notification', 'service-finder');
			}
			
			if(service_finder_wpmailer($maildata['customer_email'],$msg_subject,$msg_body)) {

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
	
	/*Add Feedback*/
	public function service_finder_addFeedback($arg){
		
		global $wpdb, $service_finder_Tables, $service_finder_Params, $service_finder_options, $current_user;
		
		$customer_id = (!empty($arg['user_id'])) ? sanitize_text_field($arg['user_id']) : '';
		$booking_id = (!empty($arg['booking_id'])) ? sanitize_text_field($arg['booking_id']) : '';
		
		$res = $wpdb->get_row($wpdb->prepare('SELECT provider_id, member_id FROM '.$service_finder_Tables->bookings.' WHERE id = %d',$booking_id));
		
		$ratingstyle = (!empty($service_finder_options['rating-style'])) ? $service_finder_options['rating-style'] : '';
		
		if($ratingstyle == 'custom-rating'){ 
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
				'provider_id'   => 	$res->provider_id,
				'customer_id'   => $current_user->ID,
				'member_id'   => $res->member_id,
				'booking_id'   => esc_attr($arg['booking_id']),
				'comment' =>   esc_attr($arg['comment']),
				'rating'      => esc_attr($avgrating),
				'date'      => date('Y-m-d h:i:s'),
				);

		$wpdb->insert($service_finder_Tables->feedback,wp_unslash($data));
		
		$feedbackid = $wpdb->insert_id;
		
		$data = array(
				'provider_id' => $res->provider_id,
				'customer_id' => $current_user->ID,
				'feedbackid_id' => $feedbackid,
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
		}else{
		$avgrating = (!empty($arg['rating'])) ? sanitize_text_field($arg['rating']) : '';
		
		$data = array(
				'provider_id'   => 	$res->provider_id,
				'customer_id'   => $current_user->ID,
				'member_id'   => $res->member_id,
				'booking_id'   => esc_attr($arg['booking_id']),
				'comment' =>   esc_attr($arg['comment']),
				'rating'      => esc_attr($avgrating),
				'date'      => date('Y-m-d h:i:s'),
				);

		$wpdb->insert($service_finder_Tables->feedback,wp_unslash($data));
		}
			
			$rating = $wpdb->get_row($wpdb->prepare('SELECT avg(rating) as avarage FROM '.$service_finder_Tables->feedback.' WHERE `provider_id` = %d',$res->provider_id));
			
			$avgrating = round($rating->avarage, 2);
			
			$wpdb->query($wpdb->prepare('UPDATE '.$service_finder_Tables->providers.' SET `rating` = "%f" WHERE `wp_user_id` = %d',$avgrating,$res->provider_id));
			
			$memberrating = $wpdb->get_row($wpdb->prepare('SELECT avg(rating) as avarage FROM '.$service_finder_Tables->feedback.' WHERE `member_id` = %d',$res->member_id));
			
			$memberavgrating = round($memberrating->avarage, 2);
			
			$wpdb->query($wpdb->prepare('UPDATE '.$service_finder_Tables->team_members.' SET `rating` = "%f" WHERE `id` = %d',$memberavgrating,$res->member_id));
			
			$success = array(
					'status' => 'success',
					'suc_message' => esc_html__('Feedback added successfully.', 'service-finder'),
					);
			echo json_encode($success);
		

	}
	
	/*Show Feedback*/
	public function service_finder_getFeedback($arg){
		
		global $wpdb, $service_finder_Tables, $service_finder_Params, $service_finder_options;
		
		$ratingstyle = (!empty($service_finder_options['rating-style'])) ? $service_finder_options['rating-style'] : '';
		
		$feedback = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->feedback.' WHERE booking_id = %d',$arg['feedbookingid']));
		
		$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM `'.$service_finder_Tables->custom_rating.'` where `feedbackid_id` = %d',$feedback->id));

		$rating = '';
		if(!empty($row)){
		$ratingtype = 'custom';
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
		
		}else{
		$ratingtype = 'simple';
		}
		
		if ( ! $feedback ) {
				$adminemail = get_option( 'admin_email' );
				$allowedhtml = array(
					'a' => array(
						'href' => array(),
						'title' => array()
					),
				);
				$error = array(
						'status' => 'error',
						'err_message' => sprintf( wp_kses(esc_html__('Couldn&#8217;t get feedback... please contact the <a href="mailto:%s">Administrator</a> !', 'service-finder'),$allowedhtml), $adminemail )
						);
				echo json_encode($error);
			}else{
				$success = array(
						'status' => 'success',
						'ratingtype' => $ratingtype,
						'rating' => $feedback->rating,
						'comment' => $feedback->comment,
						'customrating' => $rating
						);
				echo json_encode($success);
			}
				
	}	
	
	/*Cancel Booking*/
	public function service_finder_cancelBooking($arg){
		
		global $wpdb, $service_finder_Tables, $service_finder_Params, $current_user;
		
		$data = array(
					'status' => 'Cancel'
					);

		$where = array(
					'id' => esc_attr($arg['bookingid'])
					);
					
		$wpdb->update($service_finder_Tables->bookings,wp_unslash($data),$where);
		$role = service_finder_getUserRole($current_user->ID);
		
		$bookingdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `id` = %d',$arg['bookingid']),ARRAY_A);
		
		if(!empty($bookingdata))
		{
			if($bookingdata['jobid'] > 0)
			{
				delete_post_meta($bookingdata['jobid'],'_filled');
				delete_post_meta($bookingdata['jobid'],'_assignto');
				delete_post_meta($bookingdata['jobid'],'_bookingid');
			}
		}
		
		if(function_exists('service_finder_add_notices')) {
			
			$customerreplacestring = (!empty($service_finder_options['customer-replace-string'])) ? $service_finder_options['customer-replace-string'] : esc_html__('customer', 'service-finder');	
			$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('provider', 'service-finder');	
			
			$customerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `id` = %d',$bookingdata['booking_customer_id']));
			
			if($role == 'Customer'){
			$noticedata = array(
						'provider_id' => $bookingdata['provider_id'],
						'target_id' => $arg['bookingid'], 
						'topic' => 'Cancel Booking',
						'title' => esc_html__('Cancel Booking', 'service-finder'),
						'notice' => sprintf( esc_html__('Booking have been canceled by %s', 'service-finder'), $customerreplacestring ),
						);
			service_finder_add_notices($noticedata);
			}else{
			$noticedata = array(
						'customer_id' => $customerInfo->wp_user_id,
						'target_id' => $arg['bookingid'], 
						'topic' => 'Cancel Booking',
						'title' => esc_html__('Cancel Booking', 'service-finder'),
						'notice' => sprintf( esc_html__('Booking have been canceled by %s', 'service-finder'), $providerreplacestring ),
						);
			service_finder_add_notices($noticedata);
			}
			
		}
		
		$settings = service_finder_getProviderSettings($bookingdata['provider_id']);
		$google_calendar = (!empty($settings['google_calendar'])) ? $settings['google_calendar'] : '';
		
		if($google_calendar == 'on'){
		service_finder_cancelto_google_calendar($arg['bookingid'],$bookingdata['provider_id']);
		}
		
		$this->service_finder_SendCancelBookingMailToProvider($arg['bookingid']);
		$this->service_finder_SendCancelBookingMailToCustomer($arg['bookingid']);
		$this->service_finder_SendCancelBookingMailToAdmin($arg['bookingid']);
		
		$success = array(
						'status' => 'success',
						'role' => strtolower($role) 
						);
		echo json_encode($success);
	}
	
	/*Complete Booking*/
	public function service_finder_completeBooking($arg){
		
		global $wpdb, $service_finder_Tables, $service_finder_Params, $current_user, $service_finder_options;
		
		$data = array(
					'status' => 'Completed'
					);

		$where = array(
					'id' => esc_attr($arg['bookingid'])
					);
					
		$wpdb->update($service_finder_Tables->bookings,wp_unslash($data),$where);
		$role = service_finder_getUserRole($current_user->ID);
		
		$bookingdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `id` = %d',$arg['bookingid']),ARRAY_A);
		if(function_exists('service_finder_add_notices')) {
			
			$customerreplacestring = (!empty($service_finder_options['customer-replace-string'])) ? $service_finder_options['customer-replace-string'] : esc_html__('customer', 'service-finder');	
			$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('provider', 'service-finder');	
			
			$customerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `id` = %d',$bookingdata['booking_customer_id']));
			
			if($role == 'Customer'){
			$noticedata = array(
						'provider_id' => $bookingdata['provider_id'],
						'target_id' => $arg['bookingid'], 
						'topic' => 'Booking Completed',
						'title' => esc_html__('Booking Completed', 'service-finder'),
						'notice' => sprintf( esc_html__('Booking have been completed by %s', 'service-finder'), $customerreplacestring ),
						);
			service_finder_add_notices($noticedata);
			}else{
			$noticedata = array(
						'customer_id' => $customerInfo->wp_user_id,
						'target_id' => $arg['bookingid'], 
						'topic' => 'Booking Completed',
						'title' => esc_html__('Booking Completed', 'service-finder'),
						'notice' => sprintf( esc_html__('Booking have been completed by %s', 'service-finder'), $providerreplacestring ),
						);
			service_finder_add_notices($noticedata);
			}
			
		}
		
		$this->service_finder_SendBookingCompleteMailToProvider($arg['bookingid']);
		$this->service_finder_SendBookingCompleteMailToCustomer($arg['bookingid']);
		$this->service_finder_SendBookingCompleteMailToAdmin($arg['bookingid']);
		
		$success = array(
						'status' => 'success',
						'role' => strtolower($role),
						'suc_message' => esc_html__('Booking mark as completed successfully. Now you can give feedback if you want.', 'service-finder'),
						);
		echo json_encode($success);
	}
	
	/*Change Service Status*/
	public function service_finder_changeServiceStatus($arg){
		global $wpdb, $service_finder_Tables, $service_finder_Params, $current_user;
		
		$bookedserviceid = (!empty($arg['bookedserviceid'])) ? esc_html($arg['bookedserviceid']) : '';
		$currentstatus = (!empty($arg['currentstatus'])) ? esc_html($arg['currentstatus']) : '';
		
		if($currentstatus == 'pending'){
			$updatedstatus = 'completed';
			$topic = 'Service Complete';
			$title = esc_html__('Service Complete', 'service-finder');
			$noticestatus = esc_html__('completed', 'service-finder');
		}elseif($currentstatus == 'completed'){
			$updatedstatus = 'pending';
			$topic = 'Service Incomplete';
			$title = esc_html__('Service Incomplete', 'service-finder');
			$noticestatus = esc_html__('incompleted', 'service-finder');
		}
		
		$data = array(
					'status' => $updatedstatus
					);

		$where = array(
					'id' => $bookedserviceid
					);
					
		$wpdb->update($service_finder_Tables->booked_services,wp_unslash($data),$where);
		
		$role = service_finder_getUserRole($current_user->ID);
		
		$bookedservicedata = $wpdb->get_row($wpdb->prepare('SELECT booking_id,service_id,status FROM '.$service_finder_Tables->booked_services.' WHERE `id` = %d',$bookedserviceid));
		$bookingdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `id` = %d',$bookedservicedata->booking_id),ARRAY_A);
		if(function_exists('service_finder_add_notices')) {
			
			$customerreplacestring = (!empty($service_finder_options['customer-replace-string'])) ? $service_finder_options['customer-replace-string'] : esc_html__('customer', 'service-finder');	
			$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('provider', 'service-finder');	
			
			$customerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `id` = %d',$bookingdata['booking_customer_id']));
			
			if($role == 'Customer'){
			$noticedata = array(
						'provider_id' => $bookingdata['provider_id'],
						'target_id' => $bookingdata['id'], 
						'topic' => $topic,
						'title' => $title,
						'notice' => sprintf( esc_html__('Service (%s) status have been changed to %s by %s. Booking Ref id is #%d', 'service-finder'), service_finder_getServiceName($bookedservicedata->service_id),$noticestatus,$customerreplacestring,$arg['bookingid'] ),
						);
			service_finder_add_notices($noticedata);
			}elseif($role == 'Provider'){
			$noticedata = array(
						'customer_id' => $customerInfo->wp_user_id,
						'target_id' => $bookingdata['id'], 
						'topic' => $topic,
						'title' => $title,
						'notice' => sprintf( esc_html__('Service (%s) status have been changed to %s by %s. Booking Ref id is #%d', 'service-finder'), service_finder_getServiceName($bookedservicedata->service_id),$noticestatus,$providerreplacestring,$arg['bookingid'] ),
						);
			service_finder_add_notices($noticedata);
			}else{
			$noticedata = array(
						'customer_id' => $customerInfo->wp_user_id,
						'target_id' => $bookingdata['id'], 
						'topic' => $topic,
						'title' => $title,
						'notice' => sprintf( esc_html__('Service (%s) status have been changed to %s by admin. Booking Ref id is #%d', 'service-finder'), service_finder_getServiceName($bookedservicedata->service_id),$noticestatus,$arg['bookingid'] ),
						);
			service_finder_add_notices($noticedata);
			}
			
		}
		
		if($bookedservicedata->status == 'pending'){
		$status = 'incomplete';
		}else{
		$status = $bookedservicedata->status;
		}
		
		$success = array(
						'status' => 'success',
						'suc_message' => esc_html__('Service status updated successfully', 'service-finder'),
						'servicestatus' => service_finder_translate_static_status_string($status)
						);
		echo json_encode($success);
	}
	
	/*Complete Booking and pay via paypal masspay*/
	public function service_finder_completeBookingandPay($arg){
		global $wpdb, $service_finder_Tables, $service_finder_Params, $current_user, $service_finder_options;
		
		$customerreplacestring = (!empty($service_finder_options['customer-replace-string'])) ? $service_finder_options['customer-replace-string'] : esc_html__('customer', 'service-finder');	
		$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('provider', 'service-finder');
		
		$role = service_finder_getUserRole($current_user->ID);
		
		$providerid = (!empty($arg['providerid'])) ? esc_html($arg['providerid']) : '';
		$bookingid = (!empty($arg['bookingid'])) ? esc_html($arg['bookingid']) : '';
		
		$amount = (!empty($_POST['amount'])) ? esc_html($_POST['amount']) : '';
		$args = array( 
				'providerid'	=> $providerid,
				'bookingid'		=> $bookingid,
				'payoutamount'	=> $amount,
				'payouttype'	=> 'manual',
		);
		
		$payout = new service_finder_payment_masspay();
		$response = $payout->service_finder_process_payment($args);
		
		if($response['type'] == 'success')
		{
			$data = array(
					'paid_to_provider' => 'paid',
					'status' => 'Completed'
					);
			
			$where = array(
					'id' => $bookingid,
					);
			
			$booking_id = $wpdb->update($service_finder_Tables->bookings,wp_unslash($data),$where);
		
			$bookingdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `id` = %d',$bookingid),ARRAY_A);

			$customerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `id` = %d',$bookingdata['booking_customer_id']));
			
			if($role == 'Customer'){
			$noticedata = array(
						'provider_id' => $providerid,
						'target_id' => $bookingid, 
						'topic' => 'Booking Completed',
						'title' => esc_html__('Booking Completed', 'service-finder'),
						'notice' => sprintf( esc_html__('Booking have been completed by %s', 'service-finder'), $customerreplacestring ),
						);
			service_finder_add_notices($noticedata);
			}else{
			$noticedata = array(
						'customer_id' => $customerInfo->wp_user_id,
						'target_id' => $bookingid, 
						'topic' => 'Booking Completed',
						'title' => esc_html__('Booking Completed', 'service-finder'),
						'notice' => sprintf( esc_html__('Booking have been completed by %s', 'service-finder'), $providerreplacestring ),
						);
			service_finder_add_notices($noticedata);
			}
			
			$this->service_finder_SendBookingCompleteMailToProvider($bookingid);
			$this->service_finder_SendBookingCompleteMailToCustomer($bookingid);
			$this->service_finder_SendBookingCompleteMailToAdmin($bookingid);
			
			$success = array(
					'status' => 'success',
					'role' => strtolower($role), 
					'suc_message' => sprintf( esc_html__('Booking status changed and payment transferred to %s successfully.', 'service-finder'), $providerreplacestring ),
					);
			echo json_encode($success);
		}else
		{
			$error = array(
					'status' => 'error',
					'role' => strtolower($role), 
					'err_message' => $response['message']
					);
			echo json_encode($error);
		}
		
	}
	
	/*Complete Booking and pay via stripe*/
	public function service_finder_completeBookingandPayviaStripe($arg){
		global $wpdb, $service_finder_Tables, $service_finder_Params, $current_user, $service_finder_options;
		
		$providerid = (!empty($arg['providerid'])) ? esc_html($arg['providerid']) : '';
		$bookingid = (!empty($arg['bookingid'])) ? esc_html($arg['bookingid']) : '';
		$amount = (!empty($arg['amount'])) ? esc_html($arg['amount']) : '';
		
		$stripe_connect_id = get_user_meta($providerid,'stripe_connect_id',true);
		
		$role = service_finder_getUserRole($current_user->ID);
		
		$stripetype = (!empty($service_finder_options['stripe-type'])) ? esc_html($service_finder_options['stripe-type']) : '';
		if($stripetype == 'live'){
			$secret_key = (!empty($service_finder_options['stripe-live-secret-key'])) ? esc_html($service_finder_options['stripe-live-secret-key']) : '';
		}else{
			$secret_key = (!empty($service_finder_options['stripe-test-secret-key'])) ? esc_html($service_finder_options['stripe-test-secret-key']) : '';
		}
		
		$totalcost = $amount * 100;
		require_once(SERVICE_FINDER_PAYMENT_GATEWAY_DIR.'/stripe/init.php');
		
	    try {
            
			$stripeconnecttype = (!empty($service_finder_options['stripe-connect-type'])) ? esc_html($service_finder_options['stripe-connect-type']) : '';
			
			if(get_user_meta($providerid,'stripe_connect_custom_account_id',true) != ''){
			
			$stripe_connect_id = get_user_meta($providerid,'stripe_connect_custom_account_id',true);
			
			$payout = service_finder_do_payout($providerid,$totalcost);
				
			}else{
			
			\Stripe\Stripe::setApiKey($secret_key);
            $transfer_args = array(
                'amount' => $totalcost,
                'currency' => strtolower(service_finder_currencycode()),
                'destination' => $stripe_connect_id
            );
            $transfer = \Stripe\Transfer::create($transfer_args);
			$payout = json_decode($transfer);
			}
			
			if($payout->status == 'pending' || $payout->status == 'in_transit' || $payout->status == 'paid'){
			
			if($payout->status == 'paid'){
				$bookingtablestatus = 'paid';
			}else{
				$bookingtablestatus = 'in-process';
			}
			
			$data = array(
					'paid_to_provider' => $bookingtablestatus,
					'status' => 'Completed'
					);
			
			$where = array(
					'id' => $bookingid
					);
			
			$booking_id = $wpdb->update($service_finder_Tables->bookings,wp_unslash($data),$where);
			
			$data = array(
					'created' => date('Y-m-d h:i:s',$payout->created),
					'arrival_date' => date('Y-m-d h:i:s',$payout->arrival_date),
					'provider_id' => $providerid,
					'booking_id' => $bookingid,
					'connected_account_id' => $stripe_connect_id,
					'amount' => $amount,
					'stripe_connect_type' => $stripeconnecttype,
					'status' => $payout->status,
					'payout_id' => $payout->id,
					);
					
			$wpdb->insert($service_finder_Tables->payout_history,wp_unslash($data));
		
			$bookingdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `id` = %d',$bookingid),ARRAY_A);
			if(function_exists('service_finder_add_notices')) {
				
				$customerreplacestring = (!empty($service_finder_options['customer-replace-string'])) ? $service_finder_options['customer-replace-string'] : esc_html__('customer', 'service-finder');	
				$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('provider', 'service-finder');	
				
				$customerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `id` = %d',$bookingdata['booking_customer_id']));
				
				if($role == 'Customer'){
				$noticedata = array(
							'provider_id' => $bookingdata['provider_id'],
							'target_id' => $arg['bookingid'], 
							'topic' => 'Booking Completed',
							'title' => esc_html__('Booking Completed', 'service-finder'),
							'notice' => sprintf( esc_html__('Booking have been completed by %s', 'service-finder'), $customerreplacestring ),
							);
				service_finder_add_notices($noticedata);
				}else{
				$noticedata = array(
							'customer_id' => $customerInfo->wp_user_id,
							'target_id' => $arg['bookingid'], 
							'topic' => 'Booking Completed',
							'title' => esc_html__('Booking Completed', 'service-finder'),
							'notice' => sprintf( esc_html__('Booking have been completed by %s', 'service-finder'), $providerreplacestring ),
							);
				service_finder_add_notices($noticedata);
				}
				
			}
			
			$this->service_finder_SendBookingCompleteMailToProvider($arg['bookingid']);
			$this->service_finder_SendBookingCompleteMailToCustomer($arg['bookingid']);
			$this->service_finder_SendBookingCompleteMailToAdmin($arg['bookingid']);
			
			$success = array(
					'status' => 'success',
					'role' => strtolower($role), 
					'transfer' => $transfer,
					'suc_message' => sprintf( esc_html__('Booking status changed and payment initiate for %s successfully.', 'service-finder'), $providerreplacestring ),
					);
			echo json_encode($success);
			
			}else{
			$error = array(
					'status' => 'error',
					'role' => strtolower($role), 
					'err_message' => $payout->err_message
					);
			echo json_encode($error);
			}
			
        } catch (\Stripe\Error\InvalidRequest $e) {
           $error = array(
					'status' => 'error',
					'role' => strtolower($role), 
					'err_message' => $e->getMessage()
					);
			echo json_encode($error);
        } catch (\Stripe\Error\Authentication $e) {
           $error = array(
					'status' => 'error',
					'role' => strtolower($role), 
					'err_message' => $e->getMessage()
					);
			echo json_encode($error);
        } catch (\Stripe\Error\ApiConnection $e) {
            $error = array(
					'status' => 'error',
					'role' => strtolower($role), 
					'err_message' => $e->getMessage()
					);
			echo json_encode($error);
        } catch (\Stripe\Error\Base $e) {
            $error = array(
					'status' => 'error',
					'role' => strtolower($role), 
					'err_message' => $e->getMessage()
					);
			echo json_encode($error);
        } catch (Exception $e) {
            $error = array(
					'status' => 'error',
					'role' => strtolower($role), 
					'err_message' => $e->getMessage()
					);
			echo json_encode($error);
        }
			
	}
	
	/*Send Cancel Booking mail to provider*/
	public function service_finder_SendCancelBookingMailToProvider($bookingid = ''){
		global $service_finder_options, $service_finder_Tables, $wpdb;
		
		$bookingInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `id` = %d',$bookingid));
		$providerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$bookingInfo->provider_id));
		$customerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `id` = %d',$bookingInfo->booking_customer_id));

		$mailtemplate = (!empty($service_finder_options['cancel-booking-to-provider'])) ? $service_finder_options['cancel-booking-to-provider'] : '';

		if(!empty($mailtemplate)){

			$message = $mailtemplate;

		}else{
		
		$message = '
<h4>Booking Cancelled</h4>
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
<h4>Payment Details</h4>
Pay Via: %PAYMENTMETHOD%
				
Amount: %AMOUNT%';
}
			
			$tokens = array('%BOOKINGREFID%','%DATE%','%STARTTIME%','%ENDTIME%','%MEMBERNAME%','%PROVIDERNAME%','%PROVIDEREMAIL%','%PROVIDERPHONE%','%CUSTOMERNAME%','%CUSTOMEREMAIL%','%CUSTOMERPHONE%','%CUSTOMERPHONE2%','%ADDRESS%','%APT%','%CITY%','%STATE%','%ZIPCODE%','%COUNTRY%','%PAYMENTMETHOD%','%AMOUNT%');
			
			if($bookingInfo->member_id > 0){
			$membername = service_finder_getMemberName($bookingInfo->member_id);
			}else{
			$membername = '-';
			}
			
			if($bookingInfo->multi_date == 'yes')
			{
				$bookingdate = service_finder_date_format($bookingInfo->created);
			}else{
				$bookingdate = service_finder_date_format($bookingInfo->date);
			}
			
			$replacements = array($bookingid,service_finder_date_format($bookingdate),service_finder_time_format($bookingInfo->start_time),service_finder_time_format($bookingInfo->end_time),$membername,service_finder_get_providername_with_link($providerInfo->wp_user_id),$providerInfo->email,service_finder_get_contact_info($providerInfo->phone,$providerInfo->mobile),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country,ucfirst($bookingInfo->type),service_finder_money_format($bookingInfo->total));
			$msg_body = str_replace($tokens,$replacements,$message);
			
			if(class_exists('aonesms'))
			{
			if(service_finder_get_data($service_finder_options,'is-active-provider-booking-cancel-sms') == true)
			{
			$smsbody = service_finder_get_data($service_finder_options,'template-provider-booking-cancel-sms');
			if($smsbody != '')
			{
			
			$smsreplacements =array($bookingid,service_finder_date_format($bookingdate),service_finder_time_format($bookingInfo->start_time),service_finder_time_format($bookingInfo->end_time),$membername,service_finder_get_providername_with_link($providerInfo->wp_user_id),$providerInfo->email,service_finder_get_contact_info($providerInfo->phone,$providerInfo->mobile),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country,ucfirst($bookingInfo->type),service_finder_money_format($bookingInfo->total));
			
			$smsbody = str_replace($tokens,$smsreplacements,$smsbody);
			
			aonesms_send_sms_notifications($providerInfo->mobile,$smsbody);
			}
			}
			}
			
			if($service_finder_options['cancel-booking-to-provider-subject'] != ""){

				$msg_subject = $service_finder_options['cancel-booking-to-provider-subject'];

			}else{
			
				$msg_subject = 'Booking Cancelled';
			
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
	
	/*Send Cancel Booking mail to customer*/
	public function service_finder_SendCancelBookingMailToCustomer($bookingid = ''){
		global $service_finder_options, $service_finder_Tables, $wpdb;
		
		$bookingInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `id` = %d',$bookingid));
		$providerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$bookingInfo->provider_id));
		$customerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `id` = %d',$bookingInfo->booking_customer_id));

		$mailtemplate = (!empty($service_finder_options['cancel-booking-to-customer'])) ? $service_finder_options['cancel-booking-to-customer'] : '';

		if(!empty($mailtemplate)){

			$message = $mailtemplate;

		}else{
		
		$message = '
<h4>Booking Cancelled</h4>
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
<h4>Payment Details</h4>
Pay Via: %PAYMENTMETHOD%
				
				Amount: %AMOUNT%';
				
				}
			
			$tokens = array('%BOOKINGREFID%','%DATE%','%STARTTIME%','%ENDTIME%','%MEMBERNAME%','%PROVIDERNAME%','%PROVIDEREMAIL%','%PROVIDERPHONE%','%CUSTOMERNAME%','%CUSTOMEREMAIL%','%CUSTOMERPHONE%','%CUSTOMERPHONE2%','%ADDRESS%','%APT%','%CITY%','%STATE%','%ZIPCODE%','%COUNTRY%','%PAYMENTMETHOD%','%AMOUNT%');
			
			if($bookingInfo->member_id > 0){
			$membername = service_finder_getMemberName($bookingInfo->member_id);
			}else{
			$membername = '-';
			}
			
			if($bookingInfo->multi_date == 'yes')
			{
				$bookingdate = service_finder_date_format($bookingInfo->created);
			}else{
				$bookingdate = service_finder_date_format($bookingInfo->date);
			}
			
			$replacements = array($bookingid,service_finder_date_format($bookingdate),service_finder_time_format($bookingInfo->start_time),service_finder_time_format($bookingInfo->end_time),$membername,service_finder_get_providername_with_link($providerInfo->wp_user_id),$providerInfo->email,service_finder_get_contact_info($providerInfo->phone,$providerInfo->mobile),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country,ucfirst($bookingInfo->type),service_finder_money_format($bookingInfo->total));
			$msg_body = str_replace($tokens,$replacements,$message);
			if($service_finder_options['cancel-booking-to-customer-subject'] != ""){

				$msg_subject = $service_finder_options['cancel-booking-to-customer-subject'];

			}else{
			
				$msg_subject = 'Booking Cancelled';
			
			}
			
			if(class_exists('aonesms'))
			{
			if(service_finder_get_data($service_finder_options,'is-active-customer-booking-cancel-sms') == true)
			{
			$smsbody = service_finder_get_data($service_finder_options,'template-customer-booking-cancel-sms');
			if($smsbody != '')
			{
			
			$smsreplacements = array($bookingid,service_finder_date_format($bookingdate),service_finder_time_format($bookingInfo->start_time),service_finder_time_format($bookingInfo->end_time),$membername,service_finder_get_providername_with_link($providerInfo->wp_user_id),$providerInfo->email,service_finder_get_contact_info($providerInfo->phone,$providerInfo->mobile),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country,ucfirst($bookingInfo->type),service_finder_money_format($bookingInfo->total));
			
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
	
	/*Send Cancel Booking mail to admin*/
	public function service_finder_SendCancelBookingMailToAdmin($bookingid = ''){
		global $service_finder_options, $service_finder_Tables, $wpdb;
		
		$bookingInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `id` = %d',$bookingid));
		$providerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$bookingInfo->provider_id));
		$customerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `id` = %d',$bookingInfo->booking_customer_id));

		$mailtemplate = (!empty($service_finder_options['cancel-booking-to-admin'])) ? $service_finder_options['cancel-booking-to-admin'] : '';

		if(!empty($mailtemplate)){

			$message = $mailtemplate;

		}else{
		
		$message = '
<h4>Booking Cancelled</h4>
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
<h4>Payment Details</h4>
Pay Via: %PAYMENTMETHOD%
				
				Amount: %AMOUNT%';
				
			}	
			
			$tokens = array('%BOOKINGREFID%','%DATE%','%STARTTIME%','%ENDTIME%','%MEMBERNAME%','%PROVIDERNAME%','%PROVIDEREMAIL%','%PROVIDERPHONE%','%CUSTOMERNAME%','%CUSTOMEREMAIL%','%CUSTOMERPHONE%','%CUSTOMERPHONE2%','%ADDRESS%','%APT%','%CITY%','%STATE%','%ZIPCODE%','%COUNTRY%','%PAYMENTMETHOD%','%AMOUNT%');
			
			if($bookingInfo->member_id > 0){
			$membername = service_finder_getMemberName($bookingInfo->member_id);
			}else{
			$membername = '-';
			}
			
			$replacements = array($bookingid,service_finder_date_format($bookingInfo->date),service_finder_time_format($bookingInfo->start_time),service_finder_time_format($bookingInfo->end_time),$membername,service_finder_get_providername_with_link($providerInfo->wp_user_id),$providerInfo->email,service_finder_get_contact_info($providerInfo->phone,$providerInfo->mobile),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country,ucfirst($bookingInfo->type),service_finder_money_format($bookingInfo->total));
			$msg_body = str_replace($tokens,$replacements,$message);
			
			if($service_finder_options['cancel-booking-to-admin-subject'] != ""){

				$msg_subject = $service_finder_options['cancel-booking-to-admin-subject'];

			}else{
			
				$msg_subject = 'Booking Cancelled';
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
	
	/*Send Booking Complete mail to provider*/
	public function service_finder_SendBookingCompleteMailToProvider($bookingid = ''){
		global $service_finder_options, $service_finder_Tables, $wpdb;
		
		$bookingInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `id` = %d',$bookingid));
		$providerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$bookingInfo->provider_id));
		$customerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `id` = %d',$bookingInfo->booking_customer_id));

		$change_status = (!empty($service_finder_options['change-booking-status-to-provider'])) ? $service_finder_options['change-booking-status-to-provider'] : '';
		
		if(!empty($change_status)){
			$message = $change_status;
		}else{
		$message = '
<h4>Booking Completed</h4>
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
<h4>Payment Details</h4>
Pay Via: %PAYMENTMETHOD%
				
				Amount: %AMOUNT%';
			}	
			
			$tokens = array('%BOOKINGREFID%','%DATE%','%STARTTIME%','%ENDTIME%','%MEMBERNAME%','%PROVIDERNAME%','%PROVIDEREMAIL%','%PROVIDERPHONE%','%CUSTOMERNAME%','%CUSTOMEREMAIL%','%CUSTOMERPHONE%','%CUSTOMERPHONE2%','%ADDRESS%','%APT%','%CITY%','%STATE%','%ZIPCODE%','%COUNTRY%','%PAYMENTMETHOD%','%AMOUNT%','%SERVICES%');
			
			if($bookingInfo->member_id > 0){
			$membername = service_finder_getMemberName($bookingInfo->member_id);
			}else{
			$membername = '-';
			}
			$services = service_finder_get_booking_services($bookingid);
			
			if($bookingInfo->multi_date == 'yes')
			{
				$bookingdate = service_finder_date_format($bookingInfo->created);
			}else{
				$bookingdate = service_finder_date_format($bookingInfo->date);
			}
			
			$replacements = array($bookingid,service_finder_date_format($bookingdate),service_finder_time_format($bookingInfo->start_time),service_finder_time_format($bookingInfo->end_time),$membername,service_finder_get_providername_with_link($providerInfo->wp_user_id),$providerInfo->email,service_finder_get_contact_info($providerInfo->phone,$providerInfo->mobile),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country,ucfirst($bookingInfo->type),service_finder_money_format($bookingInfo->total),$services);
			$msg_body = str_replace($tokens,$replacements,$message);
			if($service_finder_options['change-booking-status-to-provider-subject'] != ""){
				$msg_subject = $service_finder_options['change-booking-status-to-provider-subject'];
			}else{
				$msg_subject = esc_html__('Booking Completed', 'service-finder');
			}
			
			if(class_exists('aonesms'))
			{
			if(service_finder_get_data($service_finder_options,'is-active-provider-booking-complete-sms') == true)
			{
			$smsbody = service_finder_get_data($service_finder_options,'template-provider-booking-complete-sms');
			if($smsbody != '')
			{
			
			$smsservices = service_finder_get_bookingsms_services($bookingid);
			
			$smsreplacements = array($bookingid,service_finder_date_format($bookingdate),service_finder_time_format($bookingInfo->start_time),service_finder_time_format($bookingInfo->end_time),$membername,service_finder_get_providername_with_link($providerInfo->wp_user_id),$providerInfo->email,service_finder_get_contact_info($providerInfo->phone,$providerInfo->mobile),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country,ucfirst($bookingInfo->type),service_finder_money_format($bookingInfo->total),$smsservices);
			
			$smsbody = str_replace($tokens,$smsreplacements,$smsbody);
			
			aonesms_send_sms_notifications($providerInfo->phone,$smsbody);
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
	
	/*Send Booking Complete mail to customer*/
	public function service_finder_SendBookingCompleteMailToCustomer($bookingid = ''){
		global $service_finder_options, $service_finder_Tables, $wpdb;
		
		$bookingInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `id` = %d',$bookingid));
		$providerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$bookingInfo->provider_id));
		$customerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `id` = %d',$bookingInfo->booking_customer_id));

		$change_status = (!empty($service_finder_options['change-booking-status-to-customer'])) ? $service_finder_options['change-booking-status-to-customer'] : '';
		
		if(!empty($change_status)){
			$message = $change_status;
		}else{
		$message = '
<h4>Booking Completed</h4>
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
<h4>Payment Details</h4>
Pay Via: %PAYMENTMETHOD%
				
				Amount: %AMOUNT%';
			}	
			
			$tokens = array('%BOOKINGREFID%','%DATE%','%STARTTIME%','%ENDTIME%','%MEMBERNAME%','%PROVIDERNAME%','%PROVIDEREMAIL%','%PROVIDERPHONE%','%CUSTOMERNAME%','%CUSTOMEREMAIL%','%CUSTOMERPHONE%','%CUSTOMERPHONE2%','%ADDRESS%','%APT%','%CITY%','%STATE%','%ZIPCODE%','%COUNTRY%','%PAYMENTMETHOD%','%AMOUNT%','%SERVICES%');
			
			if($bookingInfo->member_id > 0){
			$membername = service_finder_getMemberName($bookingInfo->member_id);
			}else{
			$membername = '-';
			}
			$services = service_finder_get_booking_services($bookingid);
			
			if($bookingInfo->multi_date == 'yes')
			{
				$bookingdate = service_finder_date_format($bookingInfo->created);
			}else{
				$bookingdate = service_finder_date_format($bookingInfo->date);
			}
			
			$replacements = array($bookingid,service_finder_date_format($bookingdate),service_finder_time_format($bookingInfo->start_time),service_finder_time_format($bookingInfo->end_time),$membername,service_finder_get_providername_with_link($providerInfo->wp_user_id),$providerInfo->email,service_finder_get_contact_info($providerInfo->phone,$providerInfo->mobile),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country,ucfirst($bookingInfo->type),service_finder_money_format($bookingInfo->total),$services);
			$msg_body = str_replace($tokens,$replacements,$message);
			if($service_finder_options['change-booking-status-to-customer-subject'] != ""){
				$msg_subject = $service_finder_options['change-booking-status-to-customer-subject'];
			}else{
				$msg_subject = esc_html__('Booking Completed', 'service-finder');
			}
			
			if(class_exists('aonesms'))
			{
			if(service_finder_get_data($service_finder_options,'is-active-customer-booking-complete-sms') == true)
			{
			$smsbody = service_finder_get_data($service_finder_options,'template-customer-booking-complete-sms');
			if($smsbody != '')
			{
			
			$smsservices = service_finder_get_bookingsms_services($bookingid);
			
			$smsreplacements = array($bookingid,service_finder_date_format($bookingdate),service_finder_time_format($bookingInfo->start_time),service_finder_time_format($bookingInfo->end_time),$membername,service_finder_get_providername_with_link($providerInfo->wp_user_id),$providerInfo->email,service_finder_get_contact_info($providerInfo->phone,$providerInfo->mobile),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country,ucfirst($bookingInfo->type),service_finder_money_format($bookingInfo->total),$smsservices);
			
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
	
	/*Send Booking Complete mail to admin*/
	public function service_finder_SendBookingCompleteMailToAdmin($bookingid = ''){
		global $service_finder_options, $service_finder_Tables, $wpdb;
		
		$bookingInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `id` = %d',$bookingid));
		$providerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$bookingInfo->provider_id));
		$customerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `id` = %d',$bookingInfo->booking_customer_id));

		$change_status = (!empty($service_finder_options['change-booking-status-to-admin'])) ? $service_finder_options['change-booking-status-to-admin'] : '';
		
		if(!empty($change_status)){
			$message = $change_status;
		}else{
		$message = '
<h4>Booking Completed</h4>
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
<h4>Payment Details</h4>
Pay Via: %PAYMENTMETHOD%
				
				Amount: %AMOUNT%';
			}	
			
			$tokens = array('%BOOKINGREFID%','%DATE%','%STARTTIME%','%ENDTIME%','%MEMBERNAME%','%PROVIDERNAME%','%PROVIDEREMAIL%','%PROVIDERPHONE%','%CUSTOMERNAME%','%CUSTOMEREMAIL%','%CUSTOMERPHONE%','%CUSTOMERPHONE2%','%ADDRESS%','%APT%','%CITY%','%STATE%','%ZIPCODE%','%COUNTRY%','%PAYMENTMETHOD%','%AMOUNT%');
			
			if($bookingInfo->member_id > 0){
			$membername = service_finder_getMemberName($bookingInfo->member_id);
			}else{
			$membername = '-';
			}
			
			$replacements = array($bookingid,service_finder_date_format($bookingInfo->date),service_finder_time_format($bookingInfo->start_time),service_finder_time_format($bookingInfo->end_time),$membername,service_finder_get_providername_with_link($providerInfo->wp_user_id),$providerInfo->email,service_finder_get_contact_info($providerInfo->phone,$providerInfo->mobile),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country,ucfirst($bookingInfo->type),service_finder_money_format($bookingInfo->total));
			$msg_body = str_replace($tokens,$replacements,$message);
			if($service_finder_options['change-booking-status-to-admin-subject'] != ""){
				$msg_subject = $service_finder_options['change-booking-status-to-admin-subject'];
			}else{
				$msg_subject = esc_html__('Booking Completed', 'service-finder');
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
	
	/*Booking Completed*/
	public function service_finder_changeStatus($arg){
		
		global $wpdb, $service_finder_Tables, $service_finder_Params, $service_finder_options;
		
		$data = array(
					'status' => 'Completed'
					);

		$where = array(
					'id' => esc_attr($arg['bookingid'])
					);
					
		$wpdb->update($service_finder_Tables->bookings,wp_unslash($data),$where);
		
		$data2 = array(
					'status' => 'completed'
					);

		$where2 = array(
					'booking_id' => $arg['bookingid']
					);
					
		$wpdb->update($service_finder_Tables->booked_services,wp_unslash($data2),$where2);

		$bookingdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `id` = %d',$arg['bookingid']),ARRAY_A);
		
		if(function_exists('service_finder_add_notices')) {
		$res = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `id` = %d',$bookingdata['booking_customer_id']),ARRAY_A);
		$users = $wpdb->prefix . 'users';
		$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$users.' WHERE `user_email` = "%s"',$res['email']));
		
		$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('provider', 'service-finder');	
		$noticedata = array(
					'customer_id' => $row->ID,
					'target_id' => $arg['bookingid'], 
					'topic' => 'Booking Completed',
					'title' => esc_html__('Booking Completed', 'service-finder'),
					'notice' => sprintf( esc_html__('Booking have been completed by %s', 'service-finder'), $providerreplacestring )
					);
		service_finder_add_notices($noticedata);
		
		}
		
		$senMail = new SERVICE_FINDER_Bookings();
		
		$senMail->service_finder_SendChangeBookingStatusMailToProvider($bookingdata);
		$senMail->service_finder_SendChangeBookingStatusMailToCustomer($bookingdata);
		$senMail->service_finder_SendChangeBookingStatusMailToAdmin($bookingdata);
		
		$success = array(
						'status' => 'success',
						);
		echo json_encode($success);
	}	
	
	/*Send Change Status mail to provider*/
	public function service_finder_SendChangeBookingStatusMailToProvider($maildata = ''){
		global $service_finder_options, $service_finder_Tables, $wpdb;
		
		$providerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$maildata['provider_id']));
		$customerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `id` = %d',$maildata['booking_customer_id']));
		
		$change_status = (!empty($service_finder_options['change-booking-status-to-provider'])) ? $service_finder_options['change-booking-status-to-provider'] : '';
		
		if(!empty($change_status)){
			$message = $change_status;
		}else{
			$message = '<h3>Booking Completed</h3>
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

Services: %SERVICES%';
		}
			
			$tokens = array('%BOOKINGREFID%','%DATE%','%STARTTIME%','%ENDTIME%','%MEMBERNAME%','%PROVIDERNAME%','%PROVIDEREMAIL%','%PROVIDERPHONE%','%CUSTOMERNAME%','%CUSTOMEREMAIL%','%CUSTOMERPHONE%','%CUSTOMERPHONE2%','%ADDRESS%','%APT%','%CITY%','%STATE%','%ZIPCODE%','%COUNTRY%','%SERVICES%');
			
			if($maildata['member_id'] > 0){
			$membername = service_finder_getMemberName($maildata['member_id']);
			}else{
			$membername = '-';
			}
			
			$services = service_finder_get_booking_services($maildata['id']);
			
			if($maildata['multi_date'] == 'yes')
			{
				$bookingdate = service_finder_date_format($maildata['created']);
			}else{
				$bookingdate = service_finder_date_format($maildata['date']);
			}
			
			$replacements = array($maildata['id'],$bookingdate,service_finder_time_format($maildata['start_time']),service_finder_time_format(service_finder_get_booking_end_time($maildata['end_time'],$maildata['end_time_no_buffer'])),$membername,service_finder_get_providername_with_link($providerInfo->wp_user_id),$providerInfo->email,service_finder_get_contact_info($providerInfo->phone,$providerInfo->mobile),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country,$services);
			$msg_body = str_replace($tokens,$replacements,$message);
			
			if($service_finder_options['change-booking-status-to-provider-subject'] != ""){
				$msg_subject = $service_finder_options['change-booking-status-to-provider-subject'];
			}else{
				$msg_subject = esc_html__('Booking Completed', 'service-finder');
			}
			
			if(class_exists('aonesms'))
			{
			if(service_finder_get_data($service_finder_options,'is-active-provider-booking-complete-sms') == true)
			{
			$smsbody = service_finder_get_data($service_finder_options,'template-provider-booking-complete-sms');
			if($smsbody != '')
			{
			
			$smsservices = service_finder_get_bookingsms_services($maildata['id']);
			
			$smsreplacements = array($maildata['id'],$bookingdate,service_finder_time_format($maildata['start_time']),service_finder_time_format(service_finder_get_booking_end_time($maildata['end_time'],$maildata['end_time_no_buffer'])),$membername,service_finder_get_providername_with_link($providerInfo->wp_user_id),$providerInfo->email,service_finder_get_contact_info($providerInfo->phone,$providerInfo->mobile),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country,$smsservices);
			
			$smsbody = str_replace($tokens,$smsreplacements,$smsbody);
			
			aonesms_send_sms_notifications($providerInfo->mobile,$smsbody);
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
	/*Send Change Status mail to customer*/
	public function service_finder_SendChangeBookingStatusMailToCustomer($maildata = ''){
		global $service_finder_options, $service_finder_Tables, $wpdb;
		$providerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$maildata['provider_id']));
		$customerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `id` = %d',$maildata['booking_customer_id']));
		
		$change_status = (!empty($service_finder_options['change-booking-status-to-customer'])) ? $service_finder_options['change-booking-status-to-customer'] : '';
		
		if(!empty($change_status)){
			$message = $change_status;
		}else{
			$message = '<h3>Booking Completed</h3>
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

Zipcode: %ZIPCODE%

Country: %COUNTRY%

Services: %SERVICES%';
		}
		
			$tokens = array('%BOOKINGREFID%','%DATE%','%STARTTIME%','%ENDTIME%','%MEMBERNAME%','%PROVIDERNAME%','%PROVIDEREMAIL%','%PROVIDERPHONE%','%CUSTOMERNAME%','%CUSTOMEREMAIL%','%CUSTOMERPHONE%','%CUSTOMERPHONE2%','%ADDRESS%','%APT%','%CITY%','%STATE%','%ZIPCODE%','%COUNTRY%','%SERVICES%');
			
			if($maildata['member_id'] > 0){
			$membername = service_finder_getMemberName($maildata['member_id']);
			}else{
			$membername = '-';
			}
			$services = service_finder_get_booking_services($maildata['id']);
			
			if($maildata['multi_date'] == 'yes')
			{
				$bookingdate = service_finder_date_format($maildata['created']);
			}else{
				$bookingdate = service_finder_date_format($maildata['date']);
			}
			
			$replacements = array($maildata['id'],$bookingdate,service_finder_time_format($maildata['start_time']),service_finder_time_format(service_finder_get_booking_end_time($maildata['end_time'],$maildata['end_time_no_buffer'])),$membername,service_finder_get_providername_with_link($providerInfo->wp_user_id),$providerInfo->email,service_finder_get_contact_info($providerInfo->phone,$providerInfo->mobile),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country,$services);
			$msg_body = str_replace($tokens,$replacements,$message);
			
			if($service_finder_options['change-booking-status-to-customer-subject'] != ""){
				$msg_subject = $service_finder_options['change-booking-status-to-customer-subject'];
			}else{
				$msg_subject = esc_html__('Booking Completed', 'service-finder');
			}
			
			if(class_exists('aonesms'))
			{
			if(service_finder_get_data($service_finder_options,'is-active-customer-booking-complete-sms') == true)
			{
			$smsbody = service_finder_get_data($service_finder_options,'template-customer-booking-complete-sms');
			if($smsbody != '')
			{
			
			$smsservices = service_finder_get_bookingsms_services($maildata['id']);
			
			$smsreplacements = array($maildata['id'],$bookingdate,service_finder_time_format($maildata['start_time']),service_finder_time_format(service_finder_get_booking_end_time($maildata['end_time'],$maildata['end_time_no_buffer'])),$membername,service_finder_get_providername_with_link($providerInfo->wp_user_id),$providerInfo->email,service_finder_get_contact_info($providerInfo->phone,$providerInfo->mobile),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country,$smsservices);
			
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
	/*Send Change Status mail to admin*/
	public function service_finder_SendChangeBookingStatusMailToAdmin($maildata = ''){
		global $service_finder_options, $wpdb, $service_finder_Tables;
		$providerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$maildata['provider_id']));
		$customerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `id` = %d',$maildata['booking_customer_id']));
		
		$change_status = (!empty($service_finder_options['change-booking-status-to-admin'])) ? $service_finder_options['change-booking-status-to-admin'] : '';
		
		if(!empty($change_status)){
			$message = $change_status;
		}else{
			$message = '<h3>Booking Completed</h3>
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

Services: %SERVICES%';
		}
			
			$tokens = array('%DATE%','%STARTTIME%','%ENDTIME%','%MEMBERNAME%','%PROVIDERNAME%','%PROVIDEREMAIL%','%PROVIDERPHONE%','%CUSTOMERNAME%','%CUSTOMEREMAIL%','%CUSTOMERPHONE%','%CUSTOMERPHONE2%','%ADDRESS%','%APT%','%CITY%','%STATE%','%ZIPCODE%','%COUNTRY%','%SERVICES%');
			
			if($maildata['member_id'] > 0){
			$membername = service_finder_getMemberName($maildata['member_id']);
			}else{
			$membername = '-';
			}
			$services = service_finder_get_booking_services($maildata['id']);
			$replacements = array(service_finder_date_format($maildata['date']),service_finder_time_format($maildata['start_time']),service_finder_time_format(service_finder_get_booking_end_time($maildata['end_time'],$maildata['end_time_no_buffer'])),$membername,service_finder_get_providername_with_link($providerInfo->wp_user_id),$providerInfo->email,service_finder_get_contact_info($providerInfo->phone,$providerInfo->mobile),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country,$services);
			$msg_body = str_replace($tokens,$replacements,$message);
			
			if($service_finder_options['change-booking-status-to-admin-subject'] != ""){
				$msg_subject = $service_finder_options['change-booking-status-to-admin-subject'];
			}else{
				$msg_subject = esc_html__('Booking Completed', 'service-finder');
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
	
	/*Edit Booking*/
	public function service_finder_editBooking($arg){
		
		global $wpdb, $service_finder_Tables, $service_finder_Params;
		
		$booking = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE id = %d',$arg['bookingid']));
		
		$bookeddate = array();
		$dayavlnum = array();
		$allocateddate = array();
		$allbookeddate = array();
		
		$dayname = date('l', strtotime( $booking->date));
		require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/book-now/BookNow.php';
		$getBookingTimeSlot = new SERVICE_FINDER_BookNow();
		
		$remtimeinsec = strtotime($booking->end_time) - strtotime($booking->start_time);
		
		$totalhours = $remtimeinsec/(60 * 60);
		
		$argdata = array(
						'seldate' => $booking->date,
						'provider_id' => $booking->provider_id,
						'start_time' => $booking->start_time,
						'end_time' => $booking->end_time,
						'editbooking' => 'yes',
						'bookingid' => $arg['bookingid'],
						'totalhours' => $totalhours,
		);
		
		if(service_finder_availability_method($booking->provider_id) == 'timeslots'){
			$avlslots = $getBookingTimeSlot->service_finder_getBookingTimeSlot($argdata);
		}elseif(service_finder_availability_method($booking->provider_id) == 'starttime'){
			$avlslots = $getBookingTimeSlot->service_finder_getBookingStartTime($argdata);
		}else{
			$avlslots = $getBookingTimeSlot->service_finder_getBookingTimeSlot($argdata);
		}

		
		
		$customerbookingdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE id = %d',$booking->booking_customer_id));
		$loadMembers = new SERVICE_FINDER_BookNow();
		
		if($arg['customereditbooking'] == 'yes'){
		$argdata = array(
						'date' => $booking->date,
						'provider_id' => $booking->provider_id,
						'editbooking' => 'yes',
						'memberid' => $booking->member_id,
						'zipcode' => $customerbookingdata->zipcode,
						'customeredit' => 'yes',
		);	
		}else{
		$argdata = array(
						'date' => $booking->date,
						'provider_id' => $booking->provider_id,
						'editbooking' => 'yes',
						'memberid' => $booking->member_id,
						'zipcode' => $customerbookingdata->zipcode,
		);
		}
		$avlMembers = $loadMembers->service_finder_loadMembers($argdata);
		
		$settings = service_finder_getProviderSettings($booking->provider_id);
		$userCap = service_finder_get_capability($booking->provider_id);
			
		if(service_finder_availability_method($booking->provider_id) == 'timeslots'){
			$getdays = $wpdb->get_results($wpdb->prepare('SELECT day FROM '.$service_finder_Tables->timeslots.' WHERE `provider_id` = %d GROUP BY day',$booking->provider_id));
		}elseif(service_finder_availability_method($booking->provider_id) == 'starttime'){
			$getdays = $wpdb->get_results($wpdb->prepare('SELECT day FROM '.$service_finder_Tables->starttime.' WHERE `provider_id` = %d GROUP BY day',$booking->provider_id));
		}else{
			$getdays = $wpdb->get_results($wpdb->prepare('SELECT day FROM '.$service_finder_Tables->timeslots.' WHERE `provider_id` = %d GROUP BY day',$booking->provider_id));
		}
		
			
		if(!empty($getdays)){
			foreach($getdays as $getday){
				$dayavlnum[] = date('N', strtotime($getday->day)) - 1;
			}
		}	
		
		if(service_finder_availability_method($booking->provider_id) == 'timeslots'){
			$res2 = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE `provider_id` = %d AND availability_method = "timeslots" AND wholeday = "yes" GROUP BY date',$booking->provider_id));
		}elseif(service_finder_availability_method($booking->provider_id) == 'starttime'){
			$res2 = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE `provider_id` = %d AND availability_method = "starttime" AND wholeday = "yes" GROUP BY date',$booking->provider_id));
		}else{
			$res2 = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE `provider_id` = %d AND availability_method = "timeslots" AND wholeday = "yes" GROUP BY date',$booking->provider_id));
		}
		
			
			$bookings = $wpdb->get_results($wpdb->prepare('SELECT date, COUNT(ID) as totalbooked FROM '.$service_finder_Tables->bookings.' WHERE `provider_id` = %d AND date > now() GROUP BY date',$booking->provider_id));
			if(!empty($bookings)){
				foreach($bookings as $bookingloop){
					$dayname = date('l', strtotime($bookingloop->date));
					if(service_finder_availability_method($booking->provider_id) == 'timeslots'){
					$q = $wpdb->get_row($wpdb->prepare('SELECT sum(max_bookings) as avlbookings FROM '.$service_finder_Tables->timeslots.' WHERE `provider_id` = %d AND day = "%s"',$booking->provider_id,strtolower($dayname)));
					}elseif(service_finder_availability_method($booking->provider_id) == 'starttime'){
					$q = $wpdb->get_row($wpdb->prepare('SELECT sum(max_bookings) as avlbookings FROM '.$service_finder_Tables->starttime.' WHERE `provider_id` = %d AND day = "%s"',$booking->provider_id,strtolower($dayname)));
					}
					if(!empty($q)){
						if($q->avlbookings <= $bookingloop->totalbooked){
							$bookeddate[] = date('Y-n-j',strtotime($bookingloop->date));			
						}
					}
				}
			}
			
			if(service_finder_availability_method($booking->provider_id) == 'timeslots'){
				$getalloteddates = $wpdb->get_results($wpdb->prepare('SELECT date FROM '.$service_finder_Tables->unavailability.' WHERE `provider_id` = %d AND availability_method = "timeslots" AND wholeday != "yes" AND date > now() GROUP BY date',$booking->provider_id));
			}elseif(service_finder_availability_method($booking->provider_id) == 'starttime'){
				$getalloteddates = $wpdb->get_results($wpdb->prepare('SELECT date FROM '.$service_finder_Tables->unavailability.' WHERE `provider_id` = %d AND availability_method = "starttime" AND wholeday != "yes" AND date > now() GROUP BY date',$booking->provider_id));
			}else{
				$getalloteddates = $wpdb->get_results($wpdb->prepare('SELECT date FROM '.$service_finder_Tables->unavailability.' WHERE `provider_id` = %d AND availability_method = "timeslots" AND wholeday != "yes" AND date > now() GROUP BY date',$booking->provider_id));
			}
			
			
			if(!empty($getalloteddates)){
				foreach($getalloteddates as $getalloteddate){
					$allocateddate[] = date('Y-n-j',strtotime($getalloteddate->date));
				}
			}
			
			if(!empty($res2)){
				foreach($res2 as $row){
				$allbookeddate[] = date('Y-n-j',strtotime($row->date));
				}
			}
			
		
		$result = array(
							'provider_id' => $booking->provider_id,
							'date' => $booking->date,
							'dayavlnum' => json_encode($dayavlnum),
							'day' => $dayname,
							'slots' => $avlslots,
							'activeslots' => $booking->start_time.'-'.$booking->end_time,
							'members' => $avlMembers,
							'memberid' => $booking->member_id,
							'staffmember' => $settings['members_available'],		
							'caps' => $userCap,
							'zipcode' => $customerbookingdata->zipcode,
							'daynum' => date('j',strtotime($booking->date)),
							'month' => date('n',strtotime($booking->date)),
							'year' => date('Y',strtotime($booking->date)),
							'dates' => json_encode($allbookeddate),
							'bookeddates' => json_encode($bookeddate),
							'totalhours' => $totalhours,
							'bookingid' => $arg['bookingid'],
					);

			$res = json_encode($result);
			return $res;
				
	}
	
	/*Update Booking*/
	public function service_finder_updateBooking($arg){
		
		global $wpdb, $service_finder_Tables, $service_finder_Params;
		
		$time = explode('-',$arg['boking-slot']);
		$settings = service_finder_getProviderSettings($arg['provider']);
		$buffertime = service_finder_get_data($settings,'buffertime');
		if($buffertime > 0 && $buffertime != '' && !empty($time[1]))
		{
		$booking_end_time = date('H:i:s', strtotime($time[1]." +".$buffertime." minutes"));
		}else{
		$booking_end_time = $time[1];
		}
		
		if($arg['memberid'] != ""){
		$memberid = $arg['memberid'];
		}else{
		$memberid = 0;
		}
		
		$data = array(
					'date' => $arg['date'],
					'start_time' => (!empty($time[0])) ? $time[0] : Null,
					'member_id' => $memberid,
					'end_time' => (!empty($time[1])) ? $booking_end_time : Null,
					'end_time_no_buffer' => (!empty($time[1])) ? $time[1] : Null, 
					);

		$where = array(
					'id' => esc_attr($arg['booking_id'])
					);
					
		$wpdb->update($service_finder_Tables->bookings,wp_unslash($data),$where);
		
		if(function_exists('service_finder_add_notices')) {
			$res = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `id` = %d',$arg['booking_id']));
			$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `id` = %d',$res->booking_customer_id));
			$noticedata = array(
					'provider_id' => $arg['provider'],
					'target_id' => $arg['booking_id'], 
					'topic' => 'Booking Edited',
					'title' => esc_html__('Booking Edited', 'service-finder'),
					'notice' => sprintf( esc_html__('Booking Edited by %s', 'service-finder'), $row->name ),
					);
			service_finder_add_notices($noticedata);
		
		}
		
		$settings = service_finder_getProviderSettings($arg['provider']);
		$google_calendar = (!empty($settings['google_calendar'])) ? $settings['google_calendar'] : '';
		
		if($google_calendar == 'on'){
		service_finder_updateto_google_calendar($arg['booking_id'],$arg['provider']);
		}
		
		
		$this->service_finder_SendEditBookingMailToProvider($arg['booking_id']);
		$this->service_finder_SendEditBookingMailToCustomer($arg['booking_id']);
		$this->service_finder_SendEditBookingMailToAdmin($arg['booking_id']);
		
		$success = array(
						'status' => 'success',
						);
		echo json_encode($success);
	}	
	
	/*Update Service Schedule*/
	public function service_finder_updateServiceSchedule($arg){
		
		global $wpdb, $service_finder_Tables, $service_finder_Params;
		
		
		$memberid = ($arg['memberid'] != "") ? $arg['memberid'] : 0;
		$bookingid = ($arg['bookingid'] != "") ? $arg['bookingid'] : '';
		$serviceid = ($arg['serviceid'] != "") ? $arg['serviceid'] : '';
		$costtype = ($arg['costtype'] != "") ? $arg['costtype'] : '';
		$totalnumber = ($arg['totalnumber'] != "") ? $arg['totalnumber'] : '';
		$dates = ($arg['dates'] != "") ? $arg['dates'] : '';
		$providerid = ($arg['costtype'] != "") ? $arg['providerid'] : '';
		$date = ($arg['date'] != "") ? $arg['date'] : '';
		$slot = ($arg['slot'] != "") ? $arg['slot'] : '';
		$time = explode('-',$slot);
		
		if($costtype == 'days'){
		$dates = rtrim($dates,'##');
		$dates = explode('##',$dates);
		
		if(!empty($dates)){
			$wpdb->query($wpdb->prepare("DELETE FROM ".$service_finder_Tables->booked_services." WHERE `booking_id` = %d AND `service_id` = %d",$bookingid,$serviceid));
			
			foreach($dates as $date){
				$data = array(
						'date' => $date,
						'start_time' => Null,
						'end_time' => Null,
						'member_id' => $memberid,
						'fullday' => 'yes',
						'booking_id' => $bookingid,
						'service_id' => $serviceid
						);
	
				$wpdb->insert($service_finder_Tables->booked_services,wp_unslash($data));			
			}		
			
		}
		}else{
				$serslot = explode('-',$serslots);
								
				$paddingtime = service_finder_get_service_paddind_time($serviceid);
				$before_padding_time = $paddingtime['before_padding_time'];
				$after_padding_time = $paddingtime['after_padding_time'];
				
				$mstarttime = (!empty($time[0])) ? $time[0] : Null; 
				$mendtime = (!empty($time[1])) ? $time[1] : Null; 
				$midtime = (!empty($time[1])) ? $time[1] : Null; 
				
				if(service_finder_availability_method($providerid) == 'timeslots'){
					
					if($totalnumber > 0){
						$tem = number_format($totalnumber, 2);
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
				if(!empty($time[0])){
				$mstarttime = date('H:i:s', strtotime($mstarttime." -".$before_padding_time." minutes"));
				}
				if(!empty($time[1])){
				$mendtime = date('H:i:s', strtotime($mendtime." +".$after_padding_time." minutes"));
				}
				}
		
				$data = array(
							'date' => $date, 
							'start_time' => (!empty($time[0])) ? $mstarttime : Null, 
							'end_time' => (!empty($time[1])) ? $mendtime : Null, 
							'without_padding_start_time' => (!empty($time[0])) ? $time[0] : Null, 
							'without_padding_end_time' => $midtime, 
							'member_id' => $memberid,
							);			
		
				$where = array(
							'booking_id' => $bookingid,
							'service_id' => $serviceid
							);
							
				$wpdb->update($service_finder_Tables->booked_services,wp_unslash($data),$where);
		}
		
		if(function_exists('service_finder_add_notices')) {
			$res = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `id` = %d',$bookingid));
			$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `id` = %d',$res->booking_customer_id));
			$noticedata = array(
					'provider_id' => $providerid,
					'target_id' => $bookingid, 
					'topic' => 'Booking Edited',
					'title' => esc_html__('Booking Edited', 'service-finder'),
					'notice' => sprintf( esc_html__('Booking Edited by %s', 'service-finder'), $row->name ),
					);
			service_finder_add_notices($noticedata);
		
		}
		
		$settings = service_finder_getProviderSettings($providerid);
		$google_calendar = (!empty($settings['google_calendar'])) ? $settings['google_calendar'] : '';
		
		if($google_calendar == 'on'){
		service_finder_updateto_google_calendar($bookingid,$arg['provider']);
		}
		
		
		$this->service_finder_SendEditBookingMailToProvider($bookingid,$serviceid);
		$this->service_finder_SendEditBookingMailToCustomer($bookingid,$serviceid);
		$this->service_finder_SendEditBookingMailToAdmin($bookingid,$serviceid);
		
		$success = array(
				'status' => 'success',
				'suc_message' => esc_html__('Service updated successfully', 'service-finder'),
				);
		$service_finder_Success = json_encode($success);
		echo $service_finder_Success;
	}	
	
	/*Send Edit Booking mail to provider*/
	public function service_finder_SendEditBookingMailToProvider($bookingid = '',$serviceid = 0){
		global $service_finder_options, $service_finder_Tables, $wpdb;
		
		$bookingInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `id` = %d',$bookingid));
		$providerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$bookingInfo->provider_id));
		$customerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `id` = %d',$bookingInfo->booking_customer_id));

		if(!empty($service_finder_options['edit-booking-to-provider'])){
			$message .= $service_finder_options['edit-booking-to-provider'];
		}else{
		$message = '
<h4>Updated Booking Info</h4>
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
<h4>Payment Details</h4>
Pay Via: %PAYMENTMETHOD%
				
				Amount: %AMOUNT%';
				
			}	
			
			$tokens = array('%DATE%','%STARTTIME%','%ENDTIME%','%MEMBERNAME%','%PROVIDERNAME%','%PROVIDEREMAIL%','%PROVIDERPHONE%','%CUSTOMERNAME%','%CUSTOMEREMAIL%','%CUSTOMERPHONE%','%CUSTOMERPHONE2%','%ADDRESS%','%APT%','%CITY%','%STATE%','%ZIPCODE%','%COUNTRY%','%PAYMENTMETHOD%','%AMOUNT%','%SERVICELOCATION%','%SERVICES%','%SHORTDESCRIPTION%','%ADMINFEE%','%BOOKINGREFID%');
			
			$services = service_finder_get_booking_services($bookingid);
			$adminfee = service_finder_get_admin_fee($bookingid,'provider');
			
			if($serviceid > 0)
			{
			$serviceinfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->booked_services.' WHERE `booking_id` = %d AND service_id = %d',$bookingid,$serviceid));
			
			if($serviceinfo->member_id > 0){
			$membername = service_finder_getMemberName($serviceinfo->member_id);
			}else{
			$membername = '-';
			}
			
			$replacements = array(service_finder_date_format($serviceinfo->date),service_finder_time_format($serviceinfo->start_time),service_finder_time_format($serviceinfo->end_time),$membername,service_finder_get_providername_with_link($providerInfo->wp_user_id),$providerInfo->email,service_finder_get_contact_info($providerInfo->phone,$providerInfo->mobile),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country,ucfirst($bookingInfo->type),service_finder_money_format($bookingInfo->total),service_finder_get_service_location($bookingid),$services,$customerInfo->description,service_finder_money_format($adminfee),$bookingid);
			
			}else{
			
			if($bookingInfo->member_id > 0){
			$membername = service_finder_getMemberName($bookingInfo->member_id);
			}else{
			$membername = '-';
			}
			
			$replacements = array(service_finder_date_format($bookingInfo->date),service_finder_time_format($bookingInfo->start_time),service_finder_time_format($bookingInfo->end_time),$membername,service_finder_get_providername_with_link($providerInfo->wp_user_id),$providerInfo->email,service_finder_get_contact_info($providerInfo->phone,$providerInfo->mobile),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country,ucfirst($bookingInfo->type),service_finder_money_format($bookingInfo->total),service_finder_get_service_location($bookingid),$services,$customerInfo->description,service_finder_money_format($adminfee),$bookingid);
			}
			
			$customerreplacestring = (!empty($service_finder_options['customer-replace-string'])) ? $service_finder_options['customer-replace-string'] : esc_html__('Customer', 'service-finder');	
			
			$msg_body = str_replace($tokens,$replacements,$message);
			
			if($service_finder_options['edit-booking-to-provider-subject'] != ""){
				$msg_subject = $service_finder_options['edit-booking-to-provider-subject'];
			}else{
				$msg_subject = 'Booking Edited by '.$customerreplacestring;
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
	
	/*Send Edit Booking mail to customer*/
	public function service_finder_SendEditBookingMailToCustomer($bookingid = '',$serviceid = 0){
		global $service_finder_options, $service_finder_Tables, $wpdb;
		
		$bookingInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `id` = %d',$bookingid));
		$providerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$bookingInfo->provider_id));
		$customerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `id` = %d',$bookingInfo->booking_customer_id));

		if(!empty($service_finder_options['edit-booking-to-customer'])){
			$message .= $service_finder_options['edit-booking-to-customer'];
		}else{
		$message = '
<h4>Updated Booking Info</h4>
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
<h4>Payment Details</h4>
Pay Via: %PAYMENTMETHOD%
				
				Amount: %AMOUNT%';
				
			}	
			
			$tokens = array('%DATE%','%STARTTIME%','%ENDTIME%','%MEMBERNAME%','%PROVIDERNAME%','%PROVIDEREMAIL%','%PROVIDERPHONE%','%CUSTOMERNAME%','%CUSTOMEREMAIL%','%CUSTOMERPHONE%','%CUSTOMERPHONE2%','%ADDRESS%','%APT%','%CITY%','%STATE%','%ZIPCODE%','%COUNTRY%','%PAYMENTMETHOD%','%AMOUNT%','%SERVICELOCATION%','%SERVICES%','%SHORTDESCRIPTION%','%ADMINFEE%','%BOOKINGREFID%');
			
			$services = service_finder_get_booking_services($bookingid);
			$adminfee = service_finder_get_admin_fee($bookingid,'customer');
			
			if($serviceid > 0)
			{
			$serviceinfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->booked_services.' WHERE `booking_id` = %d AND service_id = %d',$bookingid,$serviceid));
			
			if($serviceinfo->member_id > 0){
			$membername = service_finder_getMemberName($serviceinfo->member_id);
			}else{
			$membername = '-';
			}
			
			$replacements = array(service_finder_date_format($serviceinfo->date),service_finder_time_format($serviceinfo->start_time),service_finder_time_format($serviceinfo->end_time),$membername,service_finder_get_providername_with_link($providerInfo->wp_user_id),$providerInfo->email,service_finder_get_contact_info($providerInfo->phone,$providerInfo->mobile),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country,ucfirst($bookingInfo->type),service_finder_money_format($bookingInfo->total),service_finder_get_service_location($bookingid),$services,$customerInfo->description,service_finder_money_format($adminfee),$bookingid);
			
			}else{
			
			if($bookingInfo->member_id > 0){
			$membername = service_finder_getMemberName($bookingInfo->member_id);
			}else{
			$membername = '-';
			}
			
			$replacements = array(service_finder_date_format($bookingInfo->date),service_finder_time_format($bookingInfo->start_time),service_finder_time_format($bookingInfo->end_time),$membername,service_finder_get_providername_with_link($providerInfo->wp_user_id),$providerInfo->email,service_finder_get_contact_info($providerInfo->phone,$providerInfo->mobile),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country,ucfirst($bookingInfo->type),service_finder_money_format($bookingInfo->total),service_finder_get_service_location($bookingid),$services,$customerInfo->description,service_finder_money_format($adminfee),$bookingid);
			}
			
			$customerreplacestring = (!empty($service_finder_options['customer-replace-string'])) ? $service_finder_options['customer-replace-string'] : esc_html__('Customer', 'service-finder');	
			
			$msg_body = str_replace($tokens,$replacements,$message);
			
			if($service_finder_options['edit-booking-to-customer-subject'] != ""){
				$msg_subject = $service_finder_options['edit-booking-to-customer-subject'];
			}else{
				$msg_subject = 'Booking Edited by '.$customerreplacestring;
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
	
	/*Send Edit Booking mail to admin*/
	public function service_finder_SendEditBookingMailToAdmin($bookingid = '',$serviceid = 0){
		global $service_finder_options, $service_finder_Tables, $wpdb;
		
		$bookingInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `id` = %d',$bookingid));
		$providerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$bookingInfo->provider_id));
		$customerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `id` = %d',$bookingInfo->booking_customer_id));

		if(!empty($service_finder_options['edit-booking-to-admin'])){
			$message .= $service_finder_options['edit-booking-to-admin'];
		}else{
		$message = '
<h4>Updated Booking Info</h4>
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
<h4>Payment Details</h4>
Pay Via: %PAYMENTMETHOD%
				
				Amount: %AMOUNT%';
				
			}	
			
			$tokens = array('%DATE%','%STARTTIME%','%ENDTIME%','%MEMBERNAME%','%PROVIDERNAME%','%PROVIDEREMAIL%','%PROVIDERPHONE%','%CUSTOMERNAME%','%CUSTOMEREMAIL%','%CUSTOMERPHONE%','%CUSTOMERPHONE2%','%ADDRESS%','%APT%','%CITY%','%STATE%','%ZIPCODE%','%COUNTRY%','%PAYMENTMETHOD%','%AMOUNT%','%SERVICELOCATION%','%SERVICES%','%SHORTDESCRIPTION%','%ADMINFEE%','%BOOKINGREFID%');
			
			$services = service_finder_get_booking_services($bookingid);
			$adminfee = service_finder_get_admin_fee($bookingid,'admin');
			
			if($serviceid > 0)
			{
			$serviceinfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->booked_services.' WHERE `booking_id` = %d AND service_id = %d',$bookingid,$serviceid));
			
			if($serviceinfo->member_id > 0){
			$membername = service_finder_getMemberName($serviceinfo->member_id);
			}else{
			$membername = '-';
			}
			
			$replacements = array(service_finder_date_format($serviceinfo->date),service_finder_time_format($serviceinfo->start_time),service_finder_time_format($serviceinfo->end_time),$membername,service_finder_get_providername_with_link($providerInfo->wp_user_id),$providerInfo->email,service_finder_get_contact_info($providerInfo->phone,$providerInfo->mobile),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country,ucfirst($bookingInfo->type),service_finder_money_format($bookingInfo->total),service_finder_get_service_location($bookingid),$services,$customerInfo->description,service_finder_money_format($adminfee),$bookingid);
			
			}else{
			
			if($bookingInfo->member_id > 0){
			$membername = service_finder_getMemberName($bookingInfo->member_id);
			}else{
			$membername = '-';
			}
			
			$replacements = array(service_finder_date_format($bookingInfo->date),service_finder_time_format($bookingInfo->start_time),service_finder_time_format($bookingInfo->end_time),$membername,service_finder_get_providername_with_link($providerInfo->wp_user_id),$providerInfo->email,service_finder_get_contact_info($providerInfo->phone,$providerInfo->mobile),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country,ucfirst($bookingInfo->type),service_finder_money_format($bookingInfo->total),service_finder_get_service_location($bookingid),$services,$customerInfo->description,service_finder_money_format($adminfee),$bookingid);
			}
			
			$customerreplacestring = (!empty($service_finder_options['customer-replace-string'])) ? $service_finder_options['customer-replace-string'] : esc_html__('Customer', 'service-finder');	
			
			$msg_body = str_replace($tokens,$replacements,$message);
			
			if($service_finder_options['edit-booking-to-admin-subject'] != ""){
				$msg_subject = $service_finder_options['edit-booking-to-admin-subject'];
			}else{
				$msg_subject = 'Booking Edited by '.$customerreplacestring;
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
	
	/*Reset Booking Calendar*/
	public function service_finder_resetBookingCalender($data = ''){
	$date = null;
	$bookeddate = null;
	$allocateddate = null;
	$daynum = null;
	
	
			global $wpdb, $service_finder_Tables;
			$provider_id = (!empty($data['provider_id'])) ? $data['provider_id'] : '';
			$member_id = (!empty($data['member_id'])) ? $data['member_id'] : 0; 
			$booking_id = (!empty($data['booking_id'])) ? $data['booking_id'] : 0; 
			$service_id = (!empty($data['service_id'])) ? $data['service_id'] : 0; 
			
			$selectedrows = $wpdb->get_results($wpdb->prepare('SELECT date FROM '.$service_finder_Tables->booked_services.' WHERE `booking_id` = %d AND `service_id` = %d AND `member_id` = %d',$booking_id,$service_id,$member_id));
			
			if($member_id > 0){
			$res = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE `provider_id` = %d AND (`member_id` = 0 OR `member_id` = %d) AND wholeday = "yes" GROUP BY date',$provider_id,$member_id));
			$bookings = $wpdb->get_results($wpdb->prepare('SELECT date, COUNT(ID) as totalbooked FROM '.$service_finder_Tables->bookings.' WHERE `provider_id` = %d AND `member_id` = %d AND date > now() GROUP BY date',$provider_id,$member_id));
			}else{
			$res = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE `provider_id` = %d AND (`member_id` = 0 OR `member_id` = %d) AND wholeday = "yes" GROUP BY date',$provider_id,$member_id));
			$bookings = $wpdb->get_results($wpdb->prepare('SELECT date, COUNT(ID) as totalbooked FROM '.$service_finder_Tables->bookings.' WHERE `provider_id` = %d AND `member_id` = %d AND date > now() GROUP BY date',$provider_id,$member_id));
			}
			
			
			if(!empty($bookings)){
				foreach($bookings as $booking){
					$dayname = date('l', strtotime($booking->date));
					$q = $wpdb->get_row($wpdb->prepare('SELECT sum(max_bookings) as avlbookings FROM '.$service_finder_Tables->timeslots.' WHERE `provider_id` = %d AND day = "%s"',$provider_id,strtolower($dayname)));
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
			
			if($service_id > 0 && service_finder_get_service_type($service_id) == 'days'){
			$getdays = $wpdb->get_results($wpdb->prepare('SELECT day FROM '.$service_finder_Tables->days_availability.' WHERE `service_id` = %d AND `provider_id` = %d GROUP BY day',$service_id,$provider_id));

			if(!empty($getdays)){
				foreach($getdays as $getday){
					$daynum[] = date('N', strtotime($getday->day)) - 1;
				}
			}
			}else{
			$getdays = $wpdb->get_results($wpdb->prepare('SELECT day FROM '.$service_finder_Tables->timeslots.' WHERE `provider_id` = %d GROUP BY day',$provider_id));
			
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
			
			$success = array(
						'status' => 'success',
						'daynum' => json_encode($daynum),
						'dates' => json_encode($date),
						'selecteddates' => json_encode($selecteddates),
						'bookeddates' => json_encode($bookeddate),
						'allocateddates' => json_encode($allocateddate)
						);
			echo json_encode($success);
			
		}	
	
	/*Reset Start Time Booking Calendar*/
	public function service_finder_resetStartTimeBookingCalender($data = ''){
	$date = null;
	$bookeddate = null;
	$allocateddate = null;
	$daynum = null;
	
	
			global $wpdb, $service_finder_Tables;
			$provider_id = (!empty($data['provider_id'])) ? $data['provider_id'] : '';
			$member_id = (!empty($data['member_id'])) ? $data['member_id'] : 0; 
			$booking_id = (!empty($data['booking_id'])) ? $data['booking_id'] : 0; 
			$service_id = (!empty($data['service_id'])) ? $data['service_id'] : 0; 
			
			if(service_finder_get_service_type($service_id) == 'days'){
			$selectedrows = $wpdb->get_results($wpdb->prepare('SELECT date FROM '.$service_finder_Tables->booked_services.' WHERE `booking_id` = %d AND `service_id` = %d AND `member_id` = %d',$booking_id,$service_id,$member_id));
			
			$selecteddate = '';
			$selecteddates = array();
			$selecteddatesstr = '';
			
			if(!empty($selectedrows)){
				$selecteddaynum = date('j',strtotime($selectedrows[0]->date));
				$selectedmonth = date('n',strtotime($selectedrows[0]->date));
				$selectedyear = date('Y',strtotime($selectedrows[0]->date));
				foreach($selectedrows as $selectedrow){
					$selecteddates[] = $selectedrow->date;
					$selecteddatesstr = $selecteddatesstr . $selectedrow->date .'##';
				}
			}
			}else{
			$selectedrow = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->booked_services.' WHERE `booking_id` = %d AND `service_id` = %d AND `member_id` = %d',$booking_id,$service_id,$member_id));
			
			$selecteddate = $selectedrow->date;
			
			$selecteddaynum = date('j',strtotime($selecteddate));
			$selectedmonth = date('n',strtotime($selecteddate));
			$selectedyear = date('Y',strtotime($selecteddate));
			
			$selectedslot = $selectedrow->start_time .'-'. $selectedrow->end_time;
			
			$argdata = array(
							'seldate' => $selecteddate,
							'provider_id' => $provider_id,
							'start_time' => ($selectedrow->without_padding_start_time != NULL) ? $selectedrow->without_padding_start_time : $selectedrow->start_time,
							'end_time' => ($selectedrow->without_padding_end_time != NULL) ? $selectedrow->without_padding_end_time : $selectedrow->end_time,
							'editbooking' => 'yes',
							'bookingid' => $booking_id,
							'totalhours' =>  $selectedrow->hours,
			);
			
			require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/book-now/BookNow.php';
			$getBookingTimeSlot = new SERVICE_FINDER_BookNow();
			
			if(service_finder_availability_method($provider_id) == 'timeslots'){
				$avlslots = $getBookingTimeSlot->service_finder_getBookingTimeSlot($argdata);
			}elseif(service_finder_availability_method($provider_id) == 'starttime'){
				$avlslots = $getBookingTimeSlot->service_finder_getBookingStartTime($argdata);
			}else{
				$avlslots = $getBookingTimeSlot->service_finder_getBookingTimeSlot($argdata);
			}
			}
			
			if($member_id > 0){
			$res = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE `provider_id` = %d AND (`member_id` = 0 OR `member_id` = %d) AND wholeday = "yes" GROUP BY date',$provider_id,$member_id));
			
			$bookings = $wpdb->get_results($wpdb->prepare('SELECT date, COUNT(id) as totalbooked FROM '.$service_finder_Tables->bookings.' WHERE `provider_id` = %d AND (`member_id` = 0 OR `member_id` = %d) AND date > now() AND `multi_date` = "no" GROUP BY date',$provider_id,$member_id));
			
			$multidatebookings = $wpdb->get_results($wpdb->prepare('SELECT `bookedservices`.date, COUNT(`bookedservices`.id) as totalbooked FROM '.$service_finder_Tables->bookings.' as bookings LEFT JOIN '.$service_finder_Tables->booked_services.' as bookedservices on bookings.id = bookedservices.booking_id WHERE `bookings`.`provider_id` = %d AND (`bookedservices`.`member_id` = 0 OR `bookedservices`.`member_id` = %d) AND `bookedservices`.`date` > now() AND `bookings`.`multi_date` = "yes" GROUP BY date',$provider_id,$member_id));
			}else{
			$res = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE `provider_id` = %d AND `member_id` = %d AND wholeday = "yes" GROUP BY date',$provider_id,$member_id));
			
			$bookings = $wpdb->get_results($wpdb->prepare('SELECT date, COUNT(id) as totalbooked FROM '.$service_finder_Tables->bookings.' WHERE `provider_id` = %d AND `member_id` = %d AND date > now() AND `multi_date` = "no" GROUP BY date',$provider_id,$member_id));
			
			$multidatebookings = $wpdb->get_results($wpdb->prepare('SELECT `bookedservices`.date, COUNT(`bookedservices`.id) as totalbooked FROM '.$service_finder_Tables->bookings.' as bookings LEFT JOIN '.$service_finder_Tables->booked_services.' as bookedservices on bookings.id = bookedservices.booking_id WHERE `bookings`.`provider_id` = %d AND `bookedservices`.`member_id` = %d AND `bookedservices`.`date` > now() AND `bookings`.`multi_date` = "yes" GROUP BY date',$provider_id,$member_id));
			}
			
			if(!empty($bookings)){
				foreach($bookings as $booking){
					$dayname = date('l', strtotime($booking->date));
					$q = $wpdb->get_row($wpdb->prepare('SELECT sum(max_bookings) as avlbookings FROM '.$service_finder_Tables->starttime.' WHERE `provider_id` = %d AND day = "%s"',$provider_id,strtolower($dayname)));
					if(!empty($q)){
						if($q->avlbookings <= $booking->totalbooked && strtotime($booking->date) != strtotime($selecteddate)){
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
			
			if($service_id > 0 && service_finder_get_service_type($service_id) == 'days'){
			$getdays = $wpdb->get_results($wpdb->prepare('SELECT day FROM '.$service_finder_Tables->days_availability.' WHERE `service_id` = %d AND `provider_id` = %d GROUP BY day',$service_id,$provider_id));

			if(!empty($getdays)){
				foreach($getdays as $getday){
					$daynum[] = date('N', strtotime($getday->day)) - 1;
				}
			}
			}else{
			
			if(service_finder_availability_method($provider_id) == 'timeslots'){
			$getdays = $wpdb->get_results($wpdb->prepare('SELECT day FROM '.$service_finder_Tables->timeslots.' WHERE `provider_id` = %d GROUP BY day',$provider_id));
			}elseif(service_finder_availability_method($provider_id) == 'starttime'){
			$getdays = $wpdb->get_results($wpdb->prepare('SELECT day FROM '.$service_finder_Tables->starttime.' WHERE `provider_id` = %d GROUP BY day',$provider_id));
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
			
			$success = array(
						'status' => 'success',
						'daynum' => json_encode($daynum),
						'dates' => json_encode($date),
						'selecteddates' => json_encode($selecteddates),
						'selecteddatesstr' => $selecteddatesstr,
						'selecteddate' => $selecteddate,
						'selectedslot' => $selectedslot,
						'month' => $selectedmonth,
						'year' => $selectedyear,
						'slots' => $avlslots,
						'bookeddates' => json_encode($bookeddate),
						'allocateddates' => json_encode($allocateddate),
						);
				echo json_encode($success);
			
		}	
		
		/*Get Calendar TimeSlot*/
		public function service_finder_getBookingTimeSlot($data = ''){
		
			global $wpdb, $service_finder_Tables, $service_finder_Params, $service_finder_options;
			
			$provider_id = (!empty($data['provider_id'])) ? $data['provider_id'] : '';
			$member_id = (!empty($data['member_id'])) ? $data['member_id'] : 0; 
			
			if($member_id > 0)
			{
			$time_format = (!empty($service_finder_options['time-format'])) ? $service_finder_options['time-format'] : '';
			
			$wpdb->show_errors();
			$dayname = date('l', strtotime( $data['seldate']));
			$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->timeslots.' AS timeslots WHERE (SELECT COUNT(*) FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`status` != "Cancel" AND `bookings`.`date` = "%s" AND `bookings`.`start_time` = `timeslots`.`start_time` AND `bookings`.`end_time` = `timeslots`.`end_time`) < `timeslots`.`max_bookings` AND (SELECT COUNT(*) FROM '.$service_finder_Tables->unavailability.' AS unavl WHERE `unavl`.`date` = "%s" AND  `unavl`.availability_method = "timeslots" AND `unavl`.`start_time` = `timeslots`.`start_time` AND `unavl`.`end_time` = `timeslots`.`end_time`) = 0 AND `timeslots`.`provider_id` = %d AND `timeslots`.`day` = "%s"',$data['seldate'],$data['seldate'],$data['provider_id'],strtolower($dayname)));
			
			
			
			$res = '';
			if(!empty($results)){
				foreach($results as $slot){
				
				$qry = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE `date` = "%s" AND availability_method = "timeslots" AND start_time = "%s" AND end_time = "%s" AND provider_id = %d',$data['seldate'],$slot->start_time,$slot->end_time,$data['provider_id']));
				
				if(empty($qry)){
				
				$member_timeslots = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->member_timeslots.' where day = %s and provider_id = %d AND `member_id` = %d AND start_time = %s AND end_time = %s',strtolower($dayname),$provider_id,$member_id,$slot->start_time,$slot->end_time));
				
				if(!empty($member_timeslots)){
				
				$editbooking = (!empty($data['editbooking'])) ? $data['editbooking'] : '';
				if($editbooking == 'yes'){
					if($data['start_time'] == $slot->start_time && $data['end_time'] == $slot->end_time){
						$active = 'class="active"';
					}else{
						$active = '';
					}
				}else{
					$active = '';
				}
				
				$showtime = service_finder_time_format($slot->start_time).'-'.service_finder_time_format($slot->end_time);
				
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
				$res .= '<div class="notavail">'.esc_html__('There are no time slot available.', 'service-finder').'</div>';
			}
			
			if($res == ''){
				$res .= '<div class="notavail">'.esc_html__('There are no time slot available.', 'service-finder').'</div>';
			}
			}else{
			
			$time_format = (!empty($service_finder_options['time-format'])) ? $service_finder_options['time-format'] : '';
			
			$wpdb->show_errors();
			$dayname = date('l', strtotime( $data['seldate']));
			$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->timeslots.' AS timeslots WHERE (SELECT COUNT(*) FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`status` != "Cancel" AND `bookings`.`date` = "%s" AND `bookings`.`start_time` = `timeslots`.`start_time` AND `bookings`.`end_time` = `timeslots`.`end_time`) < `timeslots`.`max_bookings` AND (SELECT COUNT(*) FROM '.$service_finder_Tables->unavailability.' AS unavl WHERE `unavl`.`date` = "%s" AND  `unavl`.availability_method = "timeslots" AND `unavl`.`start_time` = `timeslots`.`start_time` AND `unavl`.`end_time` = `timeslots`.`end_time`) = 0 AND `timeslots`.`provider_id` = %d AND `timeslots`.`day` = "%s"',$data['seldate'],$data['seldate'],$data['provider_id'],strtolower($dayname)));
			
			
			
			$res = '';
			if(!empty($results)){
				foreach($results as $slot){
				
				$qry = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE `date` = "%s" AND availability_method = "timeslots" AND start_time = "%s" AND end_time = "%s" AND provider_id = %d',$data['seldate'],$slot->start_time,$slot->end_time,$data['provider_id']));
				
				if(empty($qry)){
				$editbooking = (!empty($data['editbooking'])) ? $data['editbooking'] : '';
				if($editbooking == 'yes'){
					if($data['start_time'] == $slot->start_time && $data['end_time'] == $slot->end_time){
						$active = 'class="active"';
					}else{
						$active = '';
					}
				}else{
					$active = '';
				}

				$showtime = service_finder_time_format($slot->start_time).'-'.service_finder_time_format($slot->end_time);
				
				$slottimestamp = strtotime($data['seldate'].' '.$slot->start_time);
				
				if($slottimestamp > current_time( 'timestamp' )){
				$res .= '<li '.$active.' data-source="'.esc_attr($slot->start_time).'-'.esc_attr($slot->end_time).'"><span>'.$showtime.'</span></li>';
				}else{
				$res .= '';
				}
				}
				}
			}else{
				$res .= '<div class="notavail">'.esc_html__('There are no time slot available.', 'service-finder').'</div>';
			}
			
			if($res == ''){
				$res .= '<div class="notavail">'.esc_html__('There are no time slot available.', 'service-finder').'</div>';
			}
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
		
						
						$totalbookings = $this->service_finder_get_availability( $data['seldate'],$row->start_time,$endtime,$data['provider_id'],$bookingid);
						
						$chkunavailability = $this->service_finder_get_chkunavailability( $data['seldate'],$row->start_time,$data['provider_id']);
						
						if($row->max_bookings > $totalbookings && $chkunavailability == 0) {		
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
							
							$showtime = service_finder_time_format($row->start_time);
							
							$slottimestamp = strtotime($data['seldate'].' '.$row->start_time);
							if($slottimestamp > current_time( 'timestamp' )){
							$flag = 1;
							$res .= '<li '.$active.' '.$databookingid.' data-source="'.esc_attr($row->start_time).'-'.esc_attr($endtime).'"><span>'.$showtime.'</span></li>';
							}else{
							$res .= '';
							}
						}
					}else{
						$totalbookings = $this->service_finder_get_availability_nohours( $data['seldate'],$row->start_time,$data['provider_id'],$bookingid);
						
						$chkunavailability = $this->service_finder_get_chkunavailability( $data['seldate'],$row->start_time,$data['provider_id']);
						
						if($row->max_bookings > $totalbookings && $chkunavailability == 0) {		
							
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
							
							$showtime = service_finder_time_format($row->start_time);
							
							$slottimestamp = strtotime($data['seldate'].' '.$row->start_time);
							if($slottimestamp > current_time( 'timestamp' )){
								$flag = 1;
								$res .= '<li '.$active.' '.$databookingid.' data-source="'.esc_attr($row->start_time).'"><span>'.$showtime.'</span></li>';
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
	

	public function get_follower_count() {
		global $wpdb;
		$provider_id = $_POST['provider_id'];
		$get_all_followers = $wpdb->get_results("SELECT * FROM $service_finder_Tables->favorites WHERE provider_id = '$provider_id'");
		$totalfollowers = count($get_all_followers);

		echo json_encode(array('count' => $totalfollowers));
	}
				
}