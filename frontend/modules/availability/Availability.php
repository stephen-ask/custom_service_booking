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



class SERVICE_FINDER_Availability{



	/*Add Time Slots*/

	public function service_finder_addTimeSlots($timeslots = ''){



			global $wpdb, $service_finder_Tables;

			$currUser = wp_get_current_user(); 

			

			$user_id = (isset($_POST['user_id'])) ? esc_attr($_POST['user_id']) : '';

			

			$maxbookingarr = explode(',',rtrim($timeslots['maxbooking'],','));

			$slotarr = explode(',',$timeslots['slots']);

			$slotids = explode(',',rtrim($timeslots['slotids'],','));

			

			

			$d = array_map(null, $maxbookingarr, $slotarr, $slotids);

			

			if(!empty($d)){

				/*Delete Old Timeslots*/

				$wpdb->query($wpdb->prepare('DELETE FROM `'.$service_finder_Tables->timeslots.'` WHERE `day` = "%s" AND `provider_id` = %d',$timeslots['day'],$user_id));

				foreach($d as $data){

				$time = explode('-',$data[1]);

				

					if($time[0] != "" && $time[1] != "" && $data[0] != "" ){

						$dataset = array(

								'provider_id' => esc_attr($user_id),

								'day' => $timeslots['day'],

								'start_time' => esc_attr($time[0].':00:00'),

								'end_time' => esc_attr($time[1].':00:00'),

								'slotids' => esc_attr($data[2]),

								'max_bookings' => esc_attr($data[0]),

								);

						/*Insert timeslots into DB*/

						$wpdb->insert($service_finder_Tables->timeslots,wp_unslash($dataset));

					}	

				

				}

			}

			

	

				



			$timeslotid = $wpdb->insert_id;



			$currentpageurl = service_finder_get_url_by_shortcode('[service_finder_my_account]');
			if(service_finder_getUserRole($currUser->ID) == 'administrator'){
			$currentpageurl = add_query_arg( array('manageaccountby' => 'admin','manageproviderid' => $user_id,'tabname' => 'availability'), $currentpageurl );
			}else{
			$currentpageurl = add_query_arg( array('tabname' => 'availability'), $currentpageurl );
			}

			$success = array(

					'status' => 'success',
					
					'redirect_url' => $currentpageurl,

					'suc_message' => esc_html__('Time slots added successfully.', 'service-finder'),

					);

			echo json_encode($success);

		

		}

		

	/*Add Start Time*/

	public function service_finder_addStartTime($args = ''){

			global $wpdb, $service_finder_Tables;

			$currUser = wp_get_current_user(); 

			$weekdays = array();

			$user_id = (isset($args['user_id'])) ? sanitize_text_field($args['user_id']) : '';

			$maxbooking = (isset($args['maxbooking'])) ? sanitize_text_field($args['maxbooking']) : '';

			$starttime = (isset($args['starttime'])) ? sanitize_text_field($args['starttime']) : '';

			$weekdays = (isset($args['weekdays'])) ? $args['weekdays'] : '';

			

			if(!empty($weekdays)){

			foreach($weekdays as $weekday){

				

				$starttimeinfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->starttime.' WHERE provider_id = %d AND day = "%s" AND start_time = "%s"',$user_id,$weekday,$starttime));

				if(empty($starttimeinfo)){

				$dataset = array(

						'provider_id' => esc_attr($user_id),

						'day' => $weekday,

						'start_time' => esc_attr($starttime),

						'max_bookings' => esc_attr($maxbooking),

						);

				/*Insert timeslots into DB*/

				$wpdb->insert($service_finder_Tables->starttime,wp_unslash($dataset));

				}

			}

			}

			

			$starttimeid = $wpdb->insert_id;



			if ( ! $starttimeid ) {

				$error = array(

						'status' => 'error',

						'err_message' => esc_html__('Already exist this data.', 'service-finder'),

						);

				echo json_encode($error);

			}else{

				$success = array(

						'status' => 'success',

						'suc_message' => esc_html__('Start time added successfully.', 'service-finder'),

						);

				echo json_encode($success);

			}

		

	}	
	
	/*Add Bulk Time Slots*/

	public function service_finder_bulk_timeslots($args = ''){

			global $wpdb, $service_finder_Tables;

			$currUser = wp_get_current_user(); 

			$weekdays = array();

			$user_id = (isset($args['user_id'])) ? sanitize_text_field($args['user_id']) : '';

			$maxbooking = (isset($args['maxbooking'])) ? sanitize_text_field($args['maxbooking']) : '';

			$starttime = (isset($args['starttime'])) ? sanitize_text_field($args['starttime']) : '';
			
			$endtime = (isset($args['endtime'])) ? sanitize_text_field($args['endtime']) : '';
			
			$slotinterval = (isset($args['slotinterval'])) ? sanitize_text_field($args['slotinterval']) : '';

			$weekdays = (isset($args['weekdays'])) ? $args['weekdays'] : '';
			
			if(!empty($weekdays)){

			foreach($weekdays as $weekday){
				$wpdb->query($wpdb->prepare('DELETE FROM `'.$service_finder_Tables->timeslots.'` WHERE `provider_id` = %d AND `day` = %s',$user_id,$weekday));

				$begin = new DateTime($starttime);
				$end   = new DateTime($endtime);
				
				$interval = DateInterval::createFromDateString($slotinterval.' min');
				
				$times    = new DatePeriod($begin, $interval, $end);
				
				$liday = ucfirst(str_replace("day","",$weekday));
				
				$k = 1;
				foreach ($times as $time) {
					$slotstarttime = $time->format('H:i:s');
					$slotendtime = date('H:i:s', strtotime($slotstarttime." +".$slotinterval." minutes"));
					
					$slotstartid = $this->service_finder_get_slotid($user_id,$slotstarttime);
					$slotendid = $this->service_finder_get_slotid($user_id,$slotendtime);
					
					$slotid = 'li'.$liday.$slotstartid.'-'.'li'.$liday.$slotendid;
					
					$dataset = array(
								'provider_id' => esc_attr($user_id),
								'day' => esc_attr($weekday),
								'start_time' => esc_attr($slotstarttime),
								'end_time' => esc_attr($slotendtime),
								'slotids' => $slotid,
								'max_bookings' => esc_attr($maxbooking),
								);
					$wpdb->insert($service_finder_Tables->timeslots,wp_unslash($dataset));
					$k++;
				}

			}

			}
			
			$currentpageurl = service_finder_get_url_by_shortcode('[service_finder_my_account]');
			if(service_finder_getUserRole($currUser->ID) == 'administrator'){
			$currentpageurl = add_query_arg( array('manageaccountby' => 'admin','manageproviderid' => $user_id,'tabname' => 'availability'), $currentpageurl );
			}else{
			$currentpageurl = add_query_arg( array('tabname' => 'availability'), $currentpageurl );
			}

			$success = array(
					'status' => 'success',
					'suc_message' => esc_html__('Bulk timeslots added successfully.', 'service-finder'),
					'redirect_url' => $currentpageurl
					);

			echo json_encode($success);

			
	}	
	
	/*Add Bulk Start Times*/

	public function service_finder_bulk_starttimes($args = ''){

			global $wpdb, $service_finder_Tables;

			$currUser = wp_get_current_user(); 

			$weekdays = array();

			$user_id = (isset($args['user_id'])) ? sanitize_text_field($args['user_id']) : '';

			$maxbooking = (isset($args['maxbooking'])) ? sanitize_text_field($args['maxbooking']) : '';

			$starttime = (isset($args['starttime'])) ? sanitize_text_field($args['starttime']) : '';
			
			$endtime = (isset($args['endtime'])) ? sanitize_text_field($args['endtime']) : '';
			
			$slotinterval = (isset($args['slotinterval'])) ? sanitize_text_field($args['slotinterval']) : '';

			$weekdays = (isset($args['weekdays'])) ? $args['weekdays'] : '';
			
			if(!empty($weekdays)){

			foreach($weekdays as $weekday){
				$wpdb->query($wpdb->prepare('DELETE FROM `'.$service_finder_Tables->starttime.'` WHERE `provider_id` = %d AND `day` = %s',$user_id,$weekday));

				$begin = new DateTime($starttime);
				$end   = new DateTime($endtime);
				
				$interval = DateInterval::createFromDateString($slotinterval.' min');
				
				$times    = new DatePeriod($begin, $interval, $end);
				
				$liday = ucfirst(str_replace("day","",$weekday));
				
				$k = 1;
				foreach ($times as $time) {
					$slotstarttime = $time->format('H:i:s');
					
					$dataset = array(
								'provider_id' => esc_attr($user_id),
								'day' => esc_attr($weekday),
								'start_time' => esc_attr($slotstarttime),
								'max_bookings' => esc_attr($maxbooking),
								);
					$wpdb->insert($service_finder_Tables->starttime,wp_unslash($dataset));
					$k++;
				}

			}

			}
			
			$currentpageurl = service_finder_get_url_by_shortcode('[service_finder_my_account]');
			if(service_finder_getUserRole($currUser->ID) == 'administrator'){
			$currentpageurl = add_query_arg( array('manageaccountby' => 'admin','manageproviderid' => $user_id,'tabname' => 'availability'), $currentpageurl );
			}else{
			$currentpageurl = add_query_arg( array('tabname' => 'availability'), $currentpageurl );
			}

			$success = array(
					'status' => 'success',
					'suc_message' => esc_html__('Bulk starttimes added successfully.', 'service-finder'),
					'redirect_url' => $currentpageurl
					);

			echo json_encode($success);
		

	}	

	public function service_finder_get_slotid($userid,$slot){
		
		$slot_interval = service_finder_get_slot_interval($userid);
		
		$begin = new DateTime("00:00");
		$end   = new DateTime("24:00");
		
		$interval = DateInterval::createFromDateString($slot_interval.' min');
		
		$times    = new DatePeriod($begin, $interval, $end);
		$k = 1;
		foreach ($times as $time) {
			if($time->format('H:i:s') == $slot){
				return $k;
			}
			$k++;
		}
	}

	/*Update Max Booking*/

	public function service_finder_updateMaxBooking($args = ''){

			global $wpdb, $service_finder_Tables;

			$currUser = wp_get_current_user(); 

			$weekdays = array();

			$user_id = (isset($args['user_id'])) ? sanitize_text_field($args['user_id']) : '';

			$maxbooking = (isset($args['maxbooking'])) ? sanitize_text_field($args['maxbooking']) : '';

			$tid = (isset($args['tid'])) ? sanitize_text_field($args['tid']) : '';

			

			$dataset = array(

					'max_bookings' => esc_attr($maxbooking),

					);

			$where = array(

					'id' => esc_attr($tid),

					'provider_id' => esc_attr($user_id),

					);		

			$updateid = $wpdb->update($service_finder_Tables->starttime,wp_unslash($dataset),$where);

			

			if(is_wp_error($updateid)){

				$error = array(

						'status' => 'error',

						'err_message' => esc_html__('Number of max booking not updated.', 'service-finder'),

						);

				echo json_encode($error);

			}else{

				$success = array(

						'status' => 'success',

						'suc_message' => esc_html__('Number of max booking updated successfully.', 'service-finder'),

						);

				echo json_encode($success);

			}

			

			

		

	}	

		

	/*Get Time Slots*/

	public function service_finder_getTimeSlots($day,$globalproviderid){



			global $wpdb, $service_finder_Tables;

			$currUser = wp_get_current_user(); 

			$timeSlot = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->timeslots.' where day = %s and provider_id = %d',$day,$globalproviderid));

			

			return $timeSlot;

			

	}

	

	/*Delete Time Slot*/

	public function service_finder_deleteTimeSlot($data){



			global $wpdb, $service_finder_Tables;

			$currUser = wp_get_current_user(); 

			$res = $wpdb->query($wpdb->prepare('DELETE FROM `'.$service_finder_Tables->timeslots.'` WHERE `id` = %d AND `provider_id` = %d',$data['slotid'],$currUser->ID));

			if ( ! $res ) {

				$adminemail = get_option( 'admin_email' );

				$allowedhtml = array(

					'a' => array(

						'href' => array(),

						'title' => array()

					),

				);

				$error = array(

						'status' => 'error',

						'err_message' => sprintf( wp_kses(esc_html__('Couldn&#8217;t delete timeslot... please contact the <a href="mailto:%s">Administrator</a> !', 'service-finder'),$allowedhtml), $adminemail )

						);

				echo json_encode($error);

			}else{

				$success = array(

						'status' => 'success',

						'suc_message' => esc_html__('Time slot deleted successfully.', 'service-finder'),

						);

				echo json_encode($success);

			}

			

	}

	

	/*Set UnAailability*/

	public function service_finder_setUnAailability($attrs = ''){



			global $wpdb, $service_finder_Tables;

			$currUser = wp_get_current_user(); 

			

			if($attrs['date'] == ""){

				$datetime = new DateTime('tomorrow');

		        $date = $datetime->format('Y-m-d');

				

				$dayname = date('l', strtotime( $tomdate));

			}else{

				$date = $attrs['date'];

				$dayname = date('l', strtotime( $attrs['date']));

			}

			

			

			$wholeday = (isset($attrs['wholeday'])) ? $attrs['wholeday'] : '';
			$memberid = (!empty($attrs['avlmemberid'])) ? $attrs['avlmemberid'] : 0;
			$unavl_type = (!empty($attrs['unavl_type'])) ? $attrs['unavl_type'] : 'days';
			$numberofdays = (!empty($attrs['numberofdays'])) ? $attrs['numberofdays'] : 0;

			if($unavl_type == 'months' || $unavl_type == 'weeks' || ($unavl_type == 'days' && $numberofdays > 0)){
			
				$totaldays = service_finder_get_total_offdays($unavl_type,$numberofdays);
	
				for($i = 0; $i < $totaldays; $i++){
					$dataset = array(
	
								'provider_id' => $attrs['provider_id'],
								
								'member_id' => $memberid,
	
								'date' => date('Y-m-d',strtotime($date. ' + '.$i.' days')),
	
								'day' => date('l',strtotime($date. ' + '.$i.' days')),
	
								'wholeday' => 'yes',
	
								'availability_method' => 'timeslots',
	
								);
	
					$wpdb->insert($service_finder_Tables->unavailability,wp_unslash($dataset));
				}
			
			}else{

			if($wholeday != 'yes'){

			if(!empty($attrs['slots'])){

				foreach($attrs['slots'] as $attr){

				

					$time = explode('-',$attr);

							

					$dataset = array(

							'provider_id' => $attrs['provider_id'],

							'member_id' => $memberid,
							
							'date' => $date,

							'day' => $dayname,

							'start_time' => esc_attr($time[0].':00:00'),

							'end_time' => esc_attr($time[1].':00:00'),

							'availability_method' => 'timeslots',

							'wholeday' => esc_attr($wholeday)

							);

					$wpdb->insert($service_finder_Tables->unavailability,wp_unslash($dataset));

				}

			}

			}else{

				$dataset = array(

							'provider_id' => $attrs['provider_id'],
							
							'member_id' => $memberid,

							'date' => $attrs['date'],

							'day' => $dayname,

							'wholeday' => esc_attr($wholeday),

							'availability_method' => 'timeslots',

							);

				$wpdb->insert($service_finder_Tables->unavailability,wp_unslash($dataset));

			}
			}

			

			$resid = $wpdb->insert_id;



			if ( ! $resid ) {

				$adminemail = get_option( 'admin_email' );

				$allowedhtml = array(

					'a' => array(

						'href' => array(),

						'title' => array()

					),

				);

				$error = array(

						'status' => 'error',

						'err_message' => sprintf( wp_kses(esc_html__('Couldn&#8217;t set Unavailability... please contact the <a href="mailto:%s">Administrator</a> !', 'service-finder'),$allowedhtml), $adminemail )

						

						);

				echo json_encode($error);

			}else{

				$success = array(

						'status' => 'success',

						'suc_message' => esc_html__('Unavailability set successfully.', 'service-finder'),

						);

				echo json_encode($success);

			}

			

		}

		

	/*Set UnAailability for start time*/

	public function service_finder_setUnAailabilityStartTime($attrs = ''){



			global $wpdb, $service_finder_Tables;

			$currUser = wp_get_current_user(); 

			

			if($attrs['date'] == ""){

				$datetime = new DateTime('tomorrow');

		        $date = $datetime->format('Y-m-d');

				

				$dayname = date('l', strtotime( $tomdate));

			}else{

				$date = $attrs['date'];

				$dayname = date('l', strtotime( $attrs['date']));

			}

			

			

			$wholeday = (isset($attrs['wholeday'])) ? $attrs['wholeday'] : '';
			$memberid = (!empty($attrs['avlmemberid'])) ? $attrs['avlmemberid'] : 0;
			$unavl_type = (!empty($attrs['unavl_type'])) ? $attrs['unavl_type'] : 'days';
			$numberofdays = (!empty($attrs['numberofdays'])) ? $attrs['numberofdays'] : 0;

			if($unavl_type == 'months' || $unavl_type == 'weeks' || ($unavl_type == 'days' && $numberofdays > 0)){
				$totaldays = service_finder_get_total_offdays($unavl_type,$numberofdays);
	
				for($i = 0; $i < $totaldays; $i++){
					$dataset = array(
	
								'provider_id' => $attrs['provider_id'],
								
								'member_id' => $memberid,
	
								'date' => date('Y-m-d',strtotime($date. ' + '.$i.' days')),
	
								'day' => date('l',strtotime($date. ' + '.$i.' days')),
	
								'wholeday' => 'yes',
	
								'availability_method' => 'starttime',
	
								);
	
					$wpdb->insert($service_finder_Tables->unavailability,wp_unslash($dataset));
				}
			}else{

			if($wholeday != 'yes'){

			if(!empty($attrs['slots'])){

				foreach($attrs['slots'] as $attr){

				

					$dataset = array(

							'provider_id' => $attrs['provider_id'],
							
							'member_id' => $memberid,

							'date' => $date,

							'day' => $dayname,

							'single_start_time' => esc_attr($attr),

							'availability_method' => 'starttime',

							'wholeday' => esc_attr($wholeday)

							);

					$wpdb->insert($service_finder_Tables->unavailability,wp_unslash($dataset));

				}

			}

			}else{

				$dataset = array(

							'provider_id' => $attrs['provider_id'],
							
							'member_id' => $memberid,

							'date' => $attrs['date'],

							'day' => $dayname,

							'wholeday' => esc_attr($wholeday),

							'availability_method' => 'starttime',

							);

				$wpdb->insert($service_finder_Tables->unavailability,wp_unslash($dataset));

			}
			}

			

			$resid = $wpdb->insert_id;



			if ( ! $resid ) {

				$adminemail = get_option( 'admin_email' );

				$allowedhtml = array(

					'a' => array(

						'href' => array(),

						'title' => array()

					),

				);

				$error = array(

						'status' => 'error',

						'err_message' => sprintf( wp_kses(esc_html__('Couldn&#8217;t set Unavailability... please contact the <a href="mailto:%s">Administrator</a> !', 'service-finder'),$allowedhtml), $adminemail )

						

						);

				echo json_encode($error);

			}else{

				$success = array(

						'status' => 'success',

						'suc_message' => esc_html__('Unavailability set successfully.', 'service-finder'),

						);

				echo json_encode($success);

			}

			

		}		

		

	/*Set UnAailability*/

	public function service_finder_editUnAailability($attrs = ''){



			global $wpdb, $service_finder_Tables;

			$currUser = wp_get_current_user(); 

			$user_id = (!empty($attrs['user_id'])) ? $attrs['user_id'] : '';

			

			if($attrs['date'] == ""){

				$error = array(

						'status' => 'error',

						'err_message' => esc_html__('Please Select Date', 'service-finder'),

						);

				$service_finder_Errors = json_encode($error);

				return $service_finder_Errors;

			}

				$date = $attrs['date'];

				$olddate = $attrs['olddate'];

				$dayname = date('l', strtotime( $attrs['date']));

		/*Delete old data at the time of edit*/	

		$wpdb->query($wpdb->prepare('DELETE FROM `'.$service_finder_Tables->unavailability.'` WHERE `date` = "%s" AND availability_method = "timeslots" AND `provider_id` = %d',$olddate,$user_id));

			

			$wholeday = (!empty($attrs['wholeday'])) ? $attrs['wholeday'] : '';
			$memberid = (!empty($attrs['editavlmemberid'])) ? $attrs['editavlmemberid'] : 0;

			$unavl_type = (!empty($attrs['unavl_type'])) ? $attrs['unavl_type'] : 'days';
			$numberofdays = (!empty($attrs['numberofdays'])) ? $attrs['numberofdays'] : 0;

			if($unavl_type == 'months' || $unavl_type == 'weeks' || ($unavl_type == 'days' && $numberofdays > 0)){
			
				$totaldays = service_finder_get_total_offdays($unavl_type,$numberofdays);
	
				for($i = 0; $i < $totaldays; $i++){
					
					$dataset = array(
	
								'provider_id' => $user_id,
								
								'member_id' => $memberid,
	
								'date' => date('Y-m-d',strtotime($attrs['date']. ' + '.$i.' days')),
	
								'day' => date('l',strtotime($attrs['date']. ' + '.$i.' days')),
	
								'wholeday' => 'yes',
	
								'availability_method' => 'timeslots',
	
								);
	
					$wpdb->insert($service_finder_Tables->unavailability,wp_unslash($dataset));
				}
			
			}else{
			
			if($wholeday != 'yes'){

			if(!empty($attrs['slots'])){

				foreach(array_unique($attrs['slots']) as $attr){

				

					$time = explode('-',$attr);

							

					$dataset = array(

							'provider_id' => $user_id,
							
							'member_id' => $memberid,

							'date' => $date,

							'day' => $dayname,

							'start_time' => esc_attr($time[0].':00:00'),

							'end_time' => esc_attr($time[1].':00:00'),

							'wholeday' => esc_attr($wholeday),

							'availability_method' => 'timeslots'

							);

					$wpdb->insert($service_finder_Tables->unavailability,wp_unslash($dataset));

				}

			}

			}else{

				$dataset = array(

							'provider_id' => $user_id,
							
							'member_id' => $memberid,

							'date' => $attrs['date'],

							'day' => $dayname,

							'wholeday' => esc_attr($wholeday),

							'availability_method' => 'timeslots'

							);

				$wpdb->insert($service_finder_Tables->unavailability,wp_unslash($dataset));

			}
			}

			

			$resid = $wpdb->insert_id;



			if(is_wp_error($resid)){

				$adminemail = get_option( 'admin_email' );

				$allowedhtml = array(

					'a' => array(

						'href' => array(),

						'title' => array()

					),

				);

				$error = array(

						'status' => 'error',

						'err_message' => sprintf( wp_kses(esc_html__('Couldn&#8217;t update Unavailability... please contact the <a href="mailto:%s">Administrator</a> !', 'service-finder'),$allowedhtml), $adminemail )

						);

				echo json_encode($error);

			}else{

				$success = array(

						'status' => 'success',

						'suc_message' => esc_html__('Unavailability update successfully.', 'service-finder'),

						);

				echo json_encode($success);

			}

			

		}	

		

	/*Set UnAailability for start time*/

	public function service_finder_editUnAailabilityStartTime($attrs = ''){



			global $wpdb, $service_finder_Tables;

			$currUser = wp_get_current_user(); 

			$user_id = (!empty($attrs['user_id'])) ? $attrs['user_id'] : '';

			

			if($attrs['date'] == ""){

				$error = array(

						'status' => 'error',

						'err_message' => esc_html__('Please Select Date', 'service-finder'),

						);

				$service_finder_Errors = json_encode($error);

				return $service_finder_Errors;

			}

				$date = $attrs['date'];

				$olddate = $attrs['olddate'];

				$dayname = date('l', strtotime( $attrs['date']));

		/*Delete old data at the time of edit*/	

		$wpdb->query($wpdb->prepare('DELETE FROM `'.$service_finder_Tables->unavailability.'` WHERE `date` = "%s" AND availability_method = "starttime" AND `provider_id` = %d',$olddate,$user_id));

			

			$wholeday = (!empty($attrs['wholeday'])) ? $attrs['wholeday'] : '';
			$memberid = (!empty($attrs['editavlmemberid'])) ? $attrs['editavlmemberid'] : 0;
			$unavl_type = (!empty($attrs['unavl_type'])) ? $attrs['unavl_type'] : 'days';
			$numberofdays = (!empty($attrs['numberofdays'])) ? $attrs['numberofdays'] : 0;

			if($unavl_type == 'months' || $unavl_type == 'weeks' || ($unavl_type == 'days' && $numberofdays > 0)){
			
				$totaldays = service_finder_get_total_offdays($unavl_type,$numberofdays);
	
				for($i = 0; $i < $totaldays; $i++){
					$dataset = array(
	
								'provider_id' => $user_id,
								
								'member_id' => $memberid,
	
								'date' => date('Y-m-d',strtotime($attrs['date']. ' + '.$i.' days')),
	
								'day' => date('l',strtotime($attrs['date']. ' + '.$i.' days')),
	
								'wholeday' => 'yes',
	
								'availability_method' => 'starttime',
	
								);
	
					$wpdb->insert($service_finder_Tables->unavailability,wp_unslash($dataset));
				}
			
			}else{
			
			if($wholeday != 'yes'){

			if(!empty($attrs['slots'])){

				foreach(array_unique($attrs['slots']) as $attr){

							

					$dataset = array(

							'provider_id' => $user_id,
							
							'member_id' => $memberid,

							'date' => $date,

							'day' => $dayname,

							'single_start_time' => esc_attr($attr),

							'wholeday' => esc_attr($wholeday),

							'availability_method' => 'starttime'

							);

					$wpdb->insert($service_finder_Tables->unavailability,wp_unslash($dataset));

				}

			}

			}else{

				$dataset = array(

							'provider_id' => $user_id,
							
							'member_id' => $memberid,

							'date' => $attrs['date'],

							'day' => $dayname,

							'wholeday' => esc_attr($wholeday),

							'availability_method' => 'starttime'

							);

				$wpdb->insert($service_finder_Tables->unavailability,wp_unslash($dataset));

			}
			}

			

			$resid = $wpdb->insert_id;



			if(is_wp_error($resid)){

				$adminemail = get_option( 'admin_email' );

				$allowedhtml = array(

					'a' => array(

						'href' => array(),

						'title' => array()

					),

				);

				$error = array(

						'status' => 'error',

						'err_message' => sprintf( wp_kses(esc_html__('Couldn&#8217;t update Unavailability... please contact the <a href="mailto:%s">Administrator</a> !', 'service-finder'),$allowedhtml), $adminemail )

						);

				echo json_encode($error);

			}else{

				$success = array(

						'status' => 'success',

						'suc_message' => esc_html__('Unavailability update successfully.', 'service-finder'),

						);

				echo json_encode($success);

			}

			

		}		

		

	/*Get Saved unavailability into datatable*/

	public function service_finder_getUnAailability($arg){

		global $wpdb, $service_finder_Tables;

		$requestData= $_REQUEST;

		$currUser = wp_get_current_user(); 

		$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';

		

		// storing  request (ie, get/post) global array to a variable  

		$requestData= $_REQUEST;

		

		

		$columns = array( 

			0 =>'date',

			1 =>'day', 

		);

		

		// getting total number records without any search

		$sql = $wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE date >= CURDATE() AND availability_method = "timeslots" AND `provider_id` = %d GROUP BY date',$user_id);

		$query=$wpdb->get_results($sql);

		$totalData = count($query);

		$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

		

		

		$sql = 'SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE date >= CURDATE() AND availability_method = "timeslots" AND `provider_id` = '.$user_id;

		if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter

			$sql.=" AND ( day LIKE '".$requestData['search']['value']."%' ";    

			$sql.=" OR start_time LIKE '".$requestData['search']['value']."%' )";

		}

		$sql.= ' GROUP BY date';

		

		$query=$wpdb->get_results($sql);

		$totalFiltered = count($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

		$query=$wpdb->get_results($sql);

		

		$data = array();

		

		foreach($query as $result){

			

			$qry = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE `date` = "%s" AND availability_method = "timeslots" AND provider_id = %d ORDER BY start_time',$result->date,$user_id));

			

			if(!empty($qry)){

				$slots = '';

				foreach($qry as $row){

					if($row->start_time != "" && $row->start_time != "NULL" && $row->end_time != "" && $row->end_time != "NULL"){

					$slots .= date('h:i a',strtotime($row->start_time)).'-'.date('h:i a',strtotime($row->end_time)).',';

					}else{

					$slots = '-';	

					}

				}

				$slots = rtrim($slots,',');

			}else{

				$slots = '-';

			}

		

			$nestedData=array(); 

			$nestedData[] = '

<div class="checkbox sf-radio-checkbox">

  <input type="checkbox" id="unavilability-'.strtotime($result->date).'" class="deleteUnAvilabilityRow" value="'.esc_attr(strtotime($result->date)).'">

  <label for="unavilability-'.strtotime($result->date).'"></label>

</div>

';

			$timeunit = str_replace('am',service_finder_trans_timeunit('am'),$slots);

			$timeunit = str_replace('pm',service_finder_trans_timeunit('pm'),$timeunit);

			

			$nestedData[] = service_finder_date_format($result->date);

			$nestedData[] = service_finder_trans_weekdays($result->day);

			$nestedData[] = $timeunit;

			if($result->wholeday == 'yes'){

			$nestedData[] = esc_html__('yes','service-finder');

			}else{

			$nestedData[] = $result->wholeday;

			}
			
			if($result->member_id > 0){
			$membername = service_finder_getMemberName($result->member_id);
			}else{
			$membername = '-';
			}
		
			$nestedData[] = $membername;

			$nestedData[] = '<button type="button" data-id="'.esc_attr(strtotime($result->date)).'" data-memberid="'.esc_attr($result->member_id).'" class="btn btn-primary btn-xs editUnAvilabilityButton"><i class="fa fa-pencil"></i>'.esc_html__('Edit','service-finder').'</button>';

			

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

	

	/*Get Saved unavailability for start time into datatable*/

	public function service_finder_getUnAailabilityStartTime($arg){

		global $wpdb, $service_finder_Tables;

		$requestData= $_REQUEST;

		$currUser = wp_get_current_user(); 

		$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';

		

		// storing  request (ie, get/post) global array to a variable  

		$requestData= $_REQUEST;

		

		

		$columns = array( 

			0 =>'date',

			1 =>'day', 

		);

		

		// getting total number records without any search

		$sql = $wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE date >= CURDATE() AND availability_method = "starttime" AND `provider_id` = %d GROUP BY date',$user_id);

		$query=$wpdb->get_results($sql);

		$totalData = count($query);

		$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

		

		

		$sql = 'SELECT unavailability.*, members.member_name FROM '.$service_finder_Tables->unavailability.' as unavailability LEFT JOIN '.$service_finder_Tables->team_members.' as members on members.id = unavailability.member_id WHERE date >= CURDATE() AND availability_method = "starttime" AND `provider_id` = '.$user_id;

		if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter

			$sql.=" AND ( unavailability.day LIKE '".$requestData['search']['value']."%' ";    

			$sql.=" OR members.member_name LIKE '%".$requestData['search']['value']."%' ";
			
			$sql.=" OR unavailability.single_start_time LIKE '".$requestData['search']['value']."%' )";

		}

		$sql.= ' GROUP BY unavailability.date';

		

		$query=$wpdb->get_results($sql);

		$totalFiltered = count($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

		$query=$wpdb->get_results($sql);

		

		$data = array();

		

		foreach($query as $result){

			

			$qry = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE `date` = "%s" AND availability_method = "starttime" AND provider_id = %d ORDER BY single_start_time',$result->date,$user_id));

			

			if(!empty($qry)){

				$slots = '';

				foreach($qry as $row){

					if($row->single_start_time != "" && $row->single_start_time != "NULL"){

					$slots .= date('h:i a',strtotime($row->single_start_time)).',';

					}else{

					$slots = '-';	

					}

				}

				$slots = rtrim($slots,',');

			}else{

				$slots = '-';

			}

		

			$nestedData=array(); 

			$nestedData[] = '

<div class="checkbox sf-radio-checkbox">

  <input type="checkbox" id="unavilability-'.strtotime($result->date).'" class="deleteUnAvilabilityRow" value="'.esc_attr(strtotime($result->date)).'">

  <label for="unavilability-'.strtotime($result->date).'"></label>

</div>

';

			$nestedData[] = service_finder_date_format($result->date);

			$nestedData[] = $result->day;

			$nestedData[] = $slots;

			if($result->wholeday == 'yes'){

			$nestedData[] = esc_html__('yes','service-finder');

			}else{

			$nestedData[] = $result->wholeday;

			}
			
			if($result->member_id > 0){
			$membername = service_finder_getMemberName($result->member_id);
			}else{
			$membername = '-';
			}
		
			$nestedData[] = $membername;

			$nestedData[] = '<button type="button" data-id="'.esc_attr(strtotime($result->date)).'" data-memberid="'.esc_attr($result->member_id).'" class="btn btn-primary btn-xs editUnAvilabilityButton"><i class="fa fa-pencil"></i>'.esc_html__('Edit','service-finder').'</button>';

			

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

	

	/*Load UnAvilability for edit*/

	public function service_finder_loadUnAailability($arg){

			global $wpdb, $service_finder_Tables;		

			$currUser = wp_get_current_user(); 

			$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';
			$memberid = (!empty($arg['memberid'])) ? $arg['memberid'] : 0;

			$date = date('Y-m-d',$arg['unavilabilitydate']);

			

			$allbookeddate = array();

			$bookeddate = array();

			$allocateddate = array();

			if($memberid > 0){
			$data = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE `date` = "%s" AND availability_method = "timeslots" AND `provider_id` = %d AND (`member_id` = 0 OR `member_id` = %d) GROUP BY date',$date,$user_id,$memberid));
			
			}else{
			
			$data = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE `date` = "%s" AND availability_method = "timeslots" AND `provider_id` = %d AND `member_id` = %d GROUP BY date',$date,$user_id,$memberid));
			}

			if(!empty($data)){

			

			$dayname = date('l', strtotime( $date));

			if($memberid > 0){
			$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->timeslots.' AS timeslots WHERE (SELECT COUNT(*) FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`status` != "Cancel" AND `bookings`.`provider_id` = %d AND (`bookings`.`member_id` = 0 OR `bookings`.`member_id` = %d) AND `bookings`.`date` = "%s" AND `bookings`.`start_time` = `timeslots`.`start_time` AND `bookings`.`end_time` = `timeslots`.`end_time`) < `timeslots`.`max_bookings` AND `timeslots`.`provider_id` = %d AND `timeslots`.`day` = "%s"',$user_id,$memberid,$date,$user_id,strtolower($dayname)));

			$qry = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE `date` = "%s" AND availability_method = "timeslots" AND provider_id = %d AND (`member_id` = 0 OR `member_id` = %d)',$date,$user_id,$memberid));
			
			}else{
			
			$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->timeslots.' AS timeslots WHERE (SELECT COUNT(*) FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`status` != "Cancel" AND `bookings`.`provider_id` = %d AND `bookings`.`member_id` = %d AND `bookings`.`date` = "%s" AND `bookings`.`start_time` = `timeslots`.`start_time` AND `bookings`.`end_time` = `timeslots`.`end_time`) < `timeslots`.`max_bookings` AND `timeslots`.`provider_id` = %d AND `timeslots`.`day` = "%s"',$user_id,$memberid,$date,$user_id,strtolower($dayname)));

			$qry = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE `date` = "%s" AND availability_method = "timeslots" AND provider_id = %d AND `member_id` = %d',$date,$user_id,$memberid));
			}
			

			

			if(!empty($qry)){

				foreach($qry as $row){

					$starttime[] = $row->start_time;

					$endtime[] = $row->end_time;

				}

			}else{

				$starttime[] = '';

				$endtime[] = '';

			}

			

			$res = '';

			if(!empty($results)){

				foreach($results as $slot){

				if(!empty($starttime) && !empty($endtime)){

					if($data->wholeday != 'yes'){

						if(in_array($slot->start_time,$starttime) && in_array($slot->end_time,$endtime)){

						$active = 'class="active"';

						}else{

						$active = '';

						}

					}else{

						$active = 'class="active"';

					}

				}

				$res .= '

<li '.$active.' data-source="'.esc_attr($slot->start_time).'-'.esc_attr($slot->end_time).'"><span>'.date('h:i a',strtotime($slot->start_time)).'-'.date('h:i a',strtotime($slot->end_time)).'</span></li>

';

				}

			}else{

				$res .= '

<div class="notavail">'.esc_html__('There are no time slot available.', 'service-finder').'</div>

';

			}

			

			

			$getdays = $wpdb->get_results($wpdb->prepare('SELECT day FROM '.$service_finder_Tables->timeslots.' WHERE `provider_id` = %d GROUP BY day',$user_id));

			

			if(!empty($getdays)){

				foreach($getdays as $getday){

					$dayavlnum[] = date('N', strtotime($getday->day)) - 1;

				}

			}

			
			if($memberid > 0){
			$res2 = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE `provider_id` = %d AND (`member_id` = 0 OR `member_id` = %d) AND availability_method = "timeslots" AND wholeday = "yes" GROUP BY date',$user_id,$memberid));

			$bookings = $wpdb->get_results($wpdb->prepare('SELECT date, COUNT(ID) as totalbooked FROM '.$service_finder_Tables->bookings.' WHERE `provider_id` = %d AND `member_id` = %d AND (`member_id` = 0 OR `member_id` = %d) AND date > now() GROUP BY date',$user_id,$memberid));
			
			}else{
			
			$res2 = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE `provider_id` = %d  AND `member_id` = %d AND availability_method = "timeslots" AND wholeday = "yes" GROUP BY date',$user_id,$memberid));

			$bookings = $wpdb->get_results($wpdb->prepare('SELECT date, COUNT(ID) as totalbooked FROM '.$service_finder_Tables->bookings.' WHERE `provider_id` = %d AND `member_id` = %d AND date > now() GROUP BY date',$user_id,$memberid));
			}
			

			

			if(!empty($bookings)){

				foreach($bookings as $booking){

					$dayname = date('l', strtotime($booking->date));

					$q = $wpdb->get_row($wpdb->prepare('SELECT sum(max_bookings) as avlbookings FROM '.$service_finder_Tables->timeslots.' WHERE `provider_id` = %d AND day = "%s"',$user_id,strtolower($dayname)));

					if(!empty($q)){

						if($q->avlbookings <= $booking->totalbooked){

							$bookeddate[] = date('Y-n-j',strtotime($booking->date));			

						}

					}

				}

			}

			if($memberid > 0){
			$getalloteddates = $wpdb->get_results($wpdb->prepare('SELECT date FROM '.$service_finder_Tables->unavailability.' WHERE `provider_id` = %d AND (`member_id` = 0 OR `member_id` = %d) AND availability_method = "timeslots" AND wholeday != "yes" AND date > now() GROUP BY date',$user_id,$memberid));
			
			}else{
			
			$getalloteddates = $wpdb->get_results($wpdb->prepare('SELECT date FROM '.$service_finder_Tables->unavailability.' WHERE `provider_id` = %d AND `member_id` = %d AND availability_method = "timeslots" AND wholeday != "yes" AND date > now() GROUP BY date',$user_id,$memberid));
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

			
			if($memberid > 0){
				$membername = service_finder_getMemberName($memberid);
			}else{
				$membername = '-';
			}
					

					$result = array(

							'date' => $date,

							'day' => (!empty($data->day)) ? $data->day : '',

							'dayavlnum' => json_encode($dayavlnum),

							'slots' => $res,

							'wholeday' => (!empty($data->wholeday)) ? $data->wholeday : '',
							
							'memberid' => $memberid,
							
							'membername' => $membername,

							'daynum' => date('j',strtotime($date)),

							'month' => date('n',strtotime($date)),

							'year' => date('Y',strtotime($date)),

							'dates' => json_encode($allbookeddate),

							'bookeddates' => json_encode($bookeddate),

							'allocateddates' => json_encode($allocateddate)

					);



			}

			echo json_encode($result);

	}

	

	/*Load UnAvilability for start time for edit*/

	public function service_finder_loadUnAailabilityStartTime($arg){

			global $wpdb, $service_finder_Tables, $service_finder_options;	

			

			$time_format = (!empty($service_finder_options['time-format'])) ? $service_finder_options['time-format'] : '';	

			$currUser = wp_get_current_user(); 

			$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';
			$memberid = (!empty($arg['memberid'])) ? $arg['memberid'] : 0;

			$date = date('Y-m-d',$arg['unavilabilitydate']);

			

			$allbookeddate = array();

			$bookeddate = array();

			$allocateddate = array();

			

			if($memberid > 0){
			$data = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE `date` = "%s" AND availability_method = "starttime" AND `provider_id` = %d AND (`member_id` = 0 OR `member_id` = %d) GROUP BY date',$date,$user_id,$memberid));
			
			}else{
			
			$data = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE `date` = "%s" AND availability_method = "starttime" AND `provider_id` = %d AND `member_id` = %d GROUP BY date',$date,$user_id,$memberid));
			}

			if(!empty($data)){

			$dayname = date('l', strtotime( $date));

			if($memberid > 0){
			$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->starttime.' AS starttime WHERE (SELECT COUNT(*) FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`status` != "Cancel" AND `bookings`.`provider_id` = %d AND (`bookings`.`member_id` = 0 OR `bookings`.`member_id` = %d) AND `bookings`.`date` = "%s" AND `bookings`.`start_time` = `starttime`.`start_time`) < `starttime`.`max_bookings` AND `starttime`.`provider_id` = %d AND `starttime`.`day` = "%s"',$user_id,$memberid,$date,$user_id,strtolower($dayname)));
			
			}else{
			
			$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->starttime.' AS starttime WHERE (SELECT COUNT(*) FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`status` != "Cancel" AND `bookings`.`provider_id` = %d AND `bookings`.`member_id` = %d AND `bookings`.`date` = "%s" AND `bookings`.`start_time` = `starttime`.`start_time`) < `starttime`.`max_bookings` AND `starttime`.`provider_id` = %d AND `starttime`.`day` = "%s"',$user_id,$memberid,$date,$user_id,strtolower($dayname)));
			}


			if($memberid > 0){
			$qry = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE `date` = "%s" AND availability_method = "starttime" AND provider_id = %d AND (`member_id` = 0 OR `member_id` = %d)',$date,$user_id,$memberid));
			
			}else{
			
			$qry = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE `date` = "%s" AND availability_method = "starttime" AND provider_id = %d AND `member_id` = %d',$date,$user_id,$memberid));
			}

			

			if(!empty($qry)){

				foreach($qry as $row){

					$starttime[] = $row->single_start_time;

				}

			}else{

				$starttime[] = '';

			}

			

			$res = '';

			if(!empty($results)){

				foreach($results as $slot){

				if(!empty($starttime)){

					if($data->wholeday != 'yes'){

						if(in_array($slot->start_time,$starttime)){

						$active = 'class="active"';

						}else{

						$active = '';

						}

					}else{

						$active = 'class="active"';

					}

				}

				if($time_format){

					$showtime = date('H:i',strtotime($slot->start_time));

				}else{

					$showtime = date('h:i a',strtotime($slot->start_time));

				}

				

				$res .= '<li '.$active.' data-source="'.esc_attr($slot->start_time).'"><span>'.$showtime.'</span></li>';

				}

			}else{

				$res .= '<div class="notavail">'.esc_html__('There are no time slot available.', 'service-finder').'</div>';

			}

			

			

			$getdays = $wpdb->get_results($wpdb->prepare('SELECT day FROM '.$service_finder_Tables->starttime.' WHERE `provider_id` = %d GROUP BY day',$user_id));

			

			if(!empty($getdays)){

				foreach($getdays as $getday){

					$dayavlnum[] = date('N', strtotime($getday->day)) - 1;

				}

			}

			

			if($memberid > 0){
			$res2 = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE `provider_id` = %d AND (`member_id` = 0 OR `member_id` = %d) AND availability_method = "starttime" AND wholeday = "yes" GROUP BY date',$user_id,$memberid));

			$bookings = $wpdb->get_results($wpdb->prepare('SELECT date, COUNT(ID) as totalbooked FROM '.$service_finder_Tables->bookings.' WHERE `provider_id` = %d AND (`member_id` = 0 OR `member_id` = %d) AND date > now() GROUP BY date',$user_id,$memberid));
			
			}else{
			
			$res2 = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE `provider_id` = %d AND `member_id` = %d AND availability_method = "starttime" AND wholeday = "yes" GROUP BY date',$user_id,$memberid));

			$bookings = $wpdb->get_results($wpdb->prepare('SELECT date, COUNT(ID) as totalbooked FROM '.$service_finder_Tables->bookings.' WHERE `provider_id` = %d AND `member_id` = %d AND date > now() GROUP BY date',$user_id,$memberid));
			}

			

			if(!empty($bookings)){

				foreach($bookings as $booking){

					$dayname = date('l', strtotime($booking->date));

					$q = $wpdb->get_row($wpdb->prepare('SELECT sum(max_bookings) as avlbookings FROM '.$service_finder_Tables->starttime.' WHERE `provider_id` = %d AND day = "%s"',$user_id,strtolower($dayname)));

					if(!empty($q)){

						if($q->avlbookings <= $booking->totalbooked){

							$bookeddate[] = date('Y-n-j',strtotime($booking->date));			

						}

					}

				}

			}

			

			if($memberid > 0){
			$getalloteddates = $wpdb->get_results($wpdb->prepare('SELECT date FROM '.$service_finder_Tables->unavailability.' WHERE `provider_id` = %d AND (`member_id` = 0 OR `member_id` = %d) AND availability_method = "starttime" AND wholeday != "yes" AND date > now() GROUP BY date',$user_id,$memberid));
			
			}else{
			
			$getalloteddates = $wpdb->get_results($wpdb->prepare('SELECT date FROM '.$service_finder_Tables->unavailability.' WHERE `provider_id` = %d AND `member_id` = %d AND availability_method = "starttime" AND wholeday != "yes" AND date > now() GROUP BY date',$user_id,$memberid));
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

			
			if($memberid > 0){
				$membername = service_finder_getMemberName($memberid);
			}else{
				$membername = '-';
			}	
					

					$result = array(

							'date' => $date,

							'day' => (!empty($data->day)) ? $data->day : '',

							'dayavlnum' => json_encode($dayavlnum),

							'slots' => $res,

							'wholeday' => (!empty($data->wholeday)) ? $data->wholeday : '',
							
							'memberid' => $memberid,
							
							'membername' => $membername,

							'daynum' => date('j',strtotime($date)),

							'month' => date('n',strtotime($date)),

							'year' => date('Y',strtotime($date)),

							'dates' => json_encode($allbookeddate),

							'bookeddates' => json_encode($bookeddate),

							'allocateddates' => json_encode($allocateddate)

					);



			}

			echo json_encode($result);

	}

	

	/*Delete UnAailability*/

	public function service_finder_deleteUnAailability($arg){

	global $wpdb, $service_finder_Tables;

			$currUser = wp_get_current_user(); 

			$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';

			$data_ids = $_REQUEST['data_ids'];

			$data_id_array = explode(",", $data_ids); 

			if(!empty($data_id_array)) {

				foreach($data_id_array as $id) {

					$date = date('Y-m-d',$id);

					$sql = $wpdb->prepare("DELETE FROM ".$service_finder_Tables->unavailability." WHERE date = '%s' AND provider_id = %d",$date,$user_id);

					$query=$wpdb->query($sql);

				}

			}else{

				$date = date('Y-m-d',$data_ids);

				$sql = $wpdb->prepare("DELETE FROM ".$service_finder_Tables->unavailability." WHERE date = '%s' AND provider_id = %d",$date,$user_id);

				$query=$wpdb->query($sql);

			}

	}

	

	/*Reset Calendar time slot*/

	public function service_finder_resetCalender($attrs = ''){



			global $wpdb, $service_finder_Tables;

			$currUser = wp_get_current_user(); 

			$user_id = (!empty($attrs['user_id'])) ? $attrs['user_id'] : '';
			$memberid = (!empty($attrs['memberid'])) ? $attrs['memberid'] : 0;

			$bookeddate = array();

			$allocateddate = array();

			$daynum = array();

			$date = array();

			
			if($memberid > 0){
			$res = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE `provider_id` = %d AND (`member_id` = 0 OR `member_id` = %d) AND wholeday = "yes" GROUP BY date',$user_id,$memberid));
			$bookings = $wpdb->get_results($wpdb->prepare('SELECT date, COUNT(ID) as totalbooked FROM '.$service_finder_Tables->bookings.' WHERE `provider_id` = %d AND (`member_id` = 0 OR `member_id` = %d) AND date > now() GROUP BY date',$user_id,$memberid));
			
			}else{
			
			$res = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE `provider_id` = %d AND `member_id` = %d AND wholeday = "yes" GROUP BY date',$user_id,$memberid));
			$bookings = $wpdb->get_results($wpdb->prepare('SELECT date, COUNT(ID) as totalbooked FROM '.$service_finder_Tables->bookings.' WHERE `provider_id` = %d AND `member_id` = %d AND date > now() GROUP BY date',$user_id,$memberid));
			}

			if(!empty($bookings)){

				foreach($bookings as $booking){

					$dayname = date('l', strtotime($booking->date));

					$q = $wpdb->get_row($wpdb->prepare('SELECT sum(max_bookings) as avlbookings FROM '.$service_finder_Tables->timeslots.' WHERE `provider_id` = %d AND day = "%s"',$user_id,strtolower($dayname)));

					if(!empty($q)){

						if($q->avlbookings <= $booking->totalbooked){

							$bookeddate[] = date('Y-n-j',strtotime($booking->date));			

						}

					}

				}

			}

			

			if($memberid > 0){
			$getalloteddates = $wpdb->get_results($wpdb->prepare('SELECT date FROM '.$service_finder_Tables->unavailability.' WHERE `provider_id` = %d AND (`member_id` = 0 OR `member_id` = %d) AND wholeday != "yes" AND date > now() GROUP BY date',$user_id,$memberid));
			}else{
			$getalloteddates = $wpdb->get_results($wpdb->prepare('SELECT date FROM '.$service_finder_Tables->unavailability.' WHERE `provider_id` = %d AND `member_id` = %d AND wholeday != "yes" AND date > now() GROUP BY date',$user_id,$memberid));
			}
			

			if(!empty($getalloteddates)){

				foreach($getalloteddates as $getalloteddate){

					$allocateddate[] = date('Y-n-j',strtotime($getalloteddate->date));

				}

			}

			

			$getdays = $wpdb->get_results($wpdb->prepare('SELECT day FROM '.$service_finder_Tables->timeslots.' WHERE `provider_id` = %d GROUP BY day',$user_id));

			

			if(!empty($getdays)){

				foreach($getdays as $getday){

					$daynum[] = date('N', strtotime($getday->day)) - 1;

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

						'bookeddates' => json_encode($bookeddate),

						'allocateddates' => json_encode($allocateddate)

						);

				echo json_encode($success);

		}	

		

	/*Reset Calendar start time*/

	public function service_finder_resetCalenderStartTime($attrs = ''){
			global $wpdb, $service_finder_Tables;

			$currUser = wp_get_current_user(); 

			$user_id = (!empty($attrs['user_id'])) ? $attrs['user_id'] : '';
			
			$memberid = (!empty($attrs['memberid'])) ? $attrs['memberid'] : 0;

			$bookeddate = array();

			$allocateddate = array();

			$daynum = array();

			$date = array();

			
			if($memberid > 0){
			$res = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE `provider_id` = %d AND (`member_id` = 0 OR `member_id` = %d) AND wholeday = "yes" GROUP BY date',$user_id,$memberid));
			$bookings = $wpdb->get_results($wpdb->prepare('SELECT date, COUNT(ID) as totalbooked FROM '.$service_finder_Tables->bookings.' WHERE `provider_id` = %d AND (`member_id` = 0 OR `member_id` = %d) AND date > now() GROUP BY date',$user_id,$memberid));
			}else{
			$res = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->unavailability.' WHERE `provider_id` = %d AND `member_id` = %d AND wholeday = "yes" GROUP BY date',$user_id,$memberid));
			$bookings = $wpdb->get_results($wpdb->prepare('SELECT date, COUNT(ID) as totalbooked FROM '.$service_finder_Tables->bookings.' WHERE `provider_id` = %d AND `member_id` = %d AND date > now() GROUP BY date',$user_id,$memberid));
			}
			

			if(!empty($bookings)){

				foreach($bookings as $booking){

					$dayname = date('l', strtotime($booking->date));

					$q = $wpdb->get_row($wpdb->prepare('SELECT sum(max_bookings) as avlbookings FROM '.$service_finder_Tables->starttime.' WHERE `provider_id` = %d AND day = "%s"',$user_id,strtolower($dayname)));

					if(!empty($q)){

						if($q->avlbookings <= $booking->totalbooked){

							$bookeddate[] = date('Y-n-j',strtotime($booking->date));			

						}

					}

				}

			}

			

			if($memberid > 0){
			$getalloteddates = $wpdb->get_results($wpdb->prepare('SELECT date FROM '.$service_finder_Tables->unavailability.' WHERE `provider_id` = %d AND (`member_id` = 0 OR `member_id` = %d) AND wholeday != "yes" AND date > now() GROUP BY date',$user_id,$memberid));
			}else{
			$getalloteddates = $wpdb->get_results($wpdb->prepare('SELECT date FROM '.$service_finder_Tables->unavailability.' WHERE `provider_id` = %d AND `member_id` = %d AND wholeday != "yes" AND date > now() GROUP BY date',$user_id,$memberid));
			}

			

			if(!empty($getalloteddates)){

				foreach($getalloteddates as $getalloteddate){

					$allocateddate[] = date('Y-n-j',strtotime($getalloteddate->date));

				}

			}

			

			$getdays = $wpdb->get_results($wpdb->prepare('SELECT day FROM '.$service_finder_Tables->starttime.' WHERE `provider_id` = %d GROUP BY day',$user_id));

			

			if(!empty($getdays)){

				foreach($getdays as $getday){

					$daynum[] = date('N', strtotime($getday->day)) - 1;

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

						'bookeddates' => json_encode($bookeddate),

						'allocateddates' => json_encode($allocateddate)

						);

				echo json_encode($success);

		}		

		

	/*Get Calendar TimeSlot*/

	public function service_finder_getTimeSlot($data = ''){

	

		global $wpdb, $service_finder_Tables, $service_finder_Params;

		$wpdb->show_errors();

		$dayname = date('l', strtotime( $data['seldate']));

		$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->timeslots.' AS timeslots WHERE (SELECT COUNT(*) FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`status` != "Cancel" AND `bookings`.`date` = "%s" AND `bookings`.`start_time` = `timeslots`.`start_time` AND `bookings`.`end_time` = `timeslots`.`end_time`) < `timeslots`.`max_bookings` AND `timeslots`.`provider_id` = %d AND `timeslots`.`day` = "%s"',$data['seldate'],$data['provider_id'],strtolower($dayname)));

		

		$res = '';

		if(!empty($results)){

			foreach($results as $slot){

			$res .= '

<li data-source="'.esc_attr($slot->start_time).'-'.esc_attr($slot->end_time).'"><span>'.date('h:i a',strtotime($slot->start_time)).'-'.date('h:i a',strtotime($slot->end_time)).'</span></li>

';

			}

		}else{

			$res .= '

<div class="notavail">'.esc_html__('There are no time slot available.', 'service-finder').'</div>

';

		}

		

		echo $res;

	}	

	

	/*Get Calendar TimeSlot*/

	public function service_finder_getStartTime($data = ''){

	

		global $wpdb, $service_finder_Tables, $service_finder_Params,$service_finder_options;

		$wpdb->show_errors();

		

		$time_format = (!empty($service_finder_options['time-format'])) ? $service_finder_options['time-format'] : '';

		$dayname = date('l', strtotime( $data['seldate']));

		$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->starttime.' AS starttime WHERE (SELECT COUNT(*) FROM '.$service_finder_Tables->bookings.' AS bookings WHERE `bookings`.`status` != "Cancel" AND `bookings`.`date` = "%s" AND `bookings`.`start_time` = `starttime`.`start_time`) < `starttime`.`max_bookings` AND `starttime`.`provider_id` = %d AND `starttime`.`day` = "%s"',$data['seldate'],$data['provider_id'],strtolower($dayname)));

		

		$res = '';

		if(!empty($results)){

			foreach($results as $row){

			if($time_format){

				$showtime = date('H:i',strtotime($row->start_time));

			}else{

				$showtime = date('h:i a',strtotime($row->start_time));

			}

			$res .= '<li data-source="'.esc_attr($row->start_time).'"><span>'.$showtime.'</span></li>';

			

			}

		}else{

			$res .= '<div class="notavail">'.esc_html__('There are no time slot available.', 'service-finder').'</div>';

		}

		

		echo $res;

	}	



}