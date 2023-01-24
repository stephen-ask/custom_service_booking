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

class SERVICE_FINDER_MyJobs{

	/*Get applied jobs into datatable*/
	public function service_finder_getjobs($arg){
		global $wpdb, $service_finder_Tables, $current_user;
		$requestData= $_REQUEST;
		$currUser = wp_get_current_user(); 
		$totalData = '';
		$totalFiltered = '';
		$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';
		$data = array();
		$jobs = get_user_meta($user_id,'job_applications',true);
		if($jobs != ""){
		$jobs = array_unique(explode(',',$jobs));
		}
		if(!empty($jobs)){
		foreach($jobs as $job){
			$nestedData=array(); 
			$bookinginfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `id` = %d',get_post_meta($job,'_bookingid',true)));
			
			$jobinfo = get_post($job);
			if(!empty($jobinfo)){
			$jobexpire = get_post_meta($job,'_job_expires',true);
			$expiredate = date_i18n( get_option( 'date_format' ), strtotime( $jobexpire ) );
			$invoiceid = '-';
			$bookingstatus = '-';
			$approvallink = '';
			$paymentstatus = '-';
			$statusbtn = '';
			if(get_post_meta($job,'_filled',true)){
				if(get_post_meta($job,'_assignto',true) == $user_id){
					$jobstatus = esc_html__('Hired','service-finder');
					if($bookinginfo->type == 'wired' && $bookinginfo->payment_to == 'provider'){
						$invoiceid = esc_html($bookinginfo->wired_invoiceid);					

						if($bookinginfo->status == 'Need-Approval'){
							$approvallink = '<button type="button" data-bookingid="'.esc_attr($bookinginfo->id).'" class="btn btn-primary btn-xs approvewiredjob">'.esc_html__('Approve Booking','service-finder').'</button>';			
						}	
					}
				}else{
					$jobstatus = esc_html__('Hired someone else','service-finder');
				}
				
				if($bookinginfo->status != ""){
				if($bookinginfo->status == 'Cancel'){
					$bookingstatus = esc_html__('Cancelled', 'service-finder');
				}else{
					$bookingstatus = service_finder_translate_static_status_string($bookinginfo->status);
				}
				}else{
					$bookingstatus = esc_html__('Pending', 'service-finder');
				}
				
				
				if(!empty($bookinginfo)){
				
					if($bookinginfo->status == 'Cancel'){
						$bookingstatus = esc_html__('Cancelled', 'service-finder');
					}else{
						$bookingstatus = service_finder_translate_static_status_string($bookinginfo->status);
					}
					
					if(($bookinginfo->type == 'stripe' && ($bookinginfo->status == 'Pending' || $bookinginfo->status == 'Completed')) || ($bookinginfo->type == 'paypal' && ($bookinginfo->status == 'Pending' || $bookinginfo->status == 'Completed')) || ($bookinginfo->type == 'wired' && ($bookinginfo->status == 'Pending' || $bookinginfo->status == 'Completed'))){
						$paymentstatus = esc_html__('Paid', 'service-finder');
					}elseif(($bookinginfo->type == 'wired' && $bookinginfo->type == 'Need-Approval') || ($bookinginfo->type == 'paypal' && $bookinginfo->type == 'Need-Approval') || ($bookinginfo->type == 'stripe' && $bookinginfo->type == 'Need-Approval')){
						$paymentstatus = '';
					}elseif($bookinginfo->type == 'free'){
						$paymentstatus = esc_html__('Free', 'service-finder');
					}
					
					if($bookinginfo->status == 'Cancel' || $bookinginfo->status == 'Completed'){
				
						$statusbtn = '';
					}else{
						if(get_post_meta($job,'_assignto',true) == $user_id){
						$statusbtn = '<button type="button" class="btn btn-warning btn-xs changeBookingJobStatus" data-id="'.esc_attr($bookinginfo->id).'" title="'.esc_html__('Change Status', 'service-finder').'"><i class="fa fa-battery-half"></i></button>';
						}else{
						$statusbtn = '';
						}
					}
				
				}else{
					
					
					if(get_post_meta($job,'_job_booking_status',true) == 'complete'){
					
					$statusbtn = '';
					$paymentstatus = esc_html__('Paid', 'service-finder');
					$bookingstatus = esc_html__('Completed', 'service-finder');
					
					}else{
					if(get_post_meta($job,'_assignto',true) == $user_id){
					$statusbtn = '<button type="button" class="btn btn-warning btn-xs changeJobStatus" data-id="'.esc_attr($job).'" title="'.esc_html__('Change Status', 'service-finder').'"><i class="fa fa-battery-half"></i></button>';
					}else{
					$statusbtn = '';
					}
					$paymentstatus = '';
					$bookingstatus = esc_html__('Pending', 'service-finder');
					}
				}
			}else{
				if(strtotime(date('Y-m-d')) > strtotime( $jobexpire )){
					$jobstatus = esc_html__('Expired','service-finder');
				}else{
					$jobstatus = esc_html__('Applied','service-finder');
				}
			}
			
			$viewapplyjob = '<button type="button" class="btn btn-custom btn-xs viewapplyjob" data-user_id="'.esc_attr($user_id).'" data-jobid="'.esc_attr($job).'" title="'.esc_html__('View Applied Job Quotation', 'service-finder').'"><i class="fa fa-eye"></i></button>';
			
			if(class_exists('aone_messaging')){
				$customerid = get_post_field( 'post_author', $job );
				
				$args = array(
							'view' => 'popup',
							'type' => 'job',
							'targetid' => $job,
							'fromid' => $current_user->ID,
							'toid' => $customerid,
						);
				$aonemsg = new aone_msg_core();
				$totalunread = $aonemsg->get_total_unread_count($current_user->ID,$args);
				
				$userCap = array();
				$userCap = service_finder_get_capability($current_user->ID);
				
				if(!empty($userCap)){
				if(in_array('message-system',$userCap)){
				$sendmessagebtn = '<button type="button" class="btn btn-custom btn-xs singlechatpopup" data-options="'.esc_attr(wp_json_encode( $args )).'" title="'.esc_html__('Send Message', 'service-finder').'"><i class="fa fa-commenting-o"></i> '.esc_html__('Send Message','aone-messaging').' ('.esc_html($totalunread).')'.'</button>';
				}
				}
			}
			
			$nestedData[] = '<a href="'.esc_url(get_post_permalink($job)).'">'.esc_html($jobinfo->post_title).'</a>';
			$nestedData[] = $expiredate;
			$nestedData[] = $jobstatus;
			$nestedData[] = $invoiceid;
			$nestedData[] = $bookingstatus;
			$nestedData[] = $paymentstatus;
			if($approvallink == "" && $statusbtn == ""){
			$nestedData[] = $viewapplyjob.' '.$sendmessagebtn;
			}else{
			$nestedData[] = $approvallink.' '.$statusbtn.' '.$viewapplyjob.' '.$sendmessagebtn;
			}

			$data[] = $nestedData;
			}
		}
		}
		
		$json_data = array(
					"draw"            => (!empty($requestData['draw'])) ? intval( $requestData['draw'] ) : 0,
					"recordsTotal"    => intval( $totalData ),
					"recordsFiltered" => intval( $totalFiltered ),
					"data"            => $data
					);
		
		echo json_encode($json_data);
		exit(0);
	}
	
	/*Get job limit transactions*/
	public function service_finder_getjoblimits_txn($arg){
		global $wpdb, $service_finder_Tables, $service_finder_options;
		$requestData= $_REQUEST;
		$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';
		$columns = array( 
			0 =>'id', 
			1 =>'id', 
		);
		
		// getting total number records without any search
		$sql = $wpdb->prepare("SELECT * FROM ".$service_finder_Tables->transaction." WHERE `provider_id` = ".$user_id);
		$query=$wpdb->get_results($sql);
		$totalData = count($query);
		$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		$sql = "SELECT * FROM ".$service_finder_Tables->transaction." WHERE `provider_id` = ".$user_id;
		if( !empty($requestData['search']['value']) ) {   
			$sql.=" AND (( `txn_id` LIKE '".$requestData['search']['value']."%' )";    
			$sql.=" OR ( `payment_method` LIKE '".$requestData['search']['value']."%' )";    
			$sql.=" OR ( `payment_status` LIKE '".$requestData['search']['value']."%' )";    
			$sql.=" OR ( `amount` LIKE '".$requestData['search']['value']."%' )";    
			$sql.=" OR ( `limit` LIKE '".$requestData['search']['value']."%' )";    
			$sql.=" OR ( `plan` LIKE '".$requestData['search']['value']."%' )";    
			$sql.=" OR ( `payment_date` LIKE '".$requestData['search']['value']."%' ))";    
		}
		
		$query=$wpdb->get_results($sql);
		$totalFiltered = count($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." DESC LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		$query=$wpdb->get_results($sql);
		$data = array();
		
		foreach($query as $result){
			$nestedData=array(); 

			if(service_finder_getUserRole($user_id) == 'Provider'){
			$planname = (!empty($service_finder_options['plan'.$result->plan.'-name'])) ? $service_finder_options['plan'.$result->plan.'-name'] : '';
			}else{
			$planname = (!empty($service_finder_options['job-post-plan'.$result->plan.'-name'])) ? $service_finder_options['job-post-plan'.$result->plan.'-name'] : '';
			}

			$nestedData[] = date('d-m-Y',strtotime($result->payment_date));
			$nestedData[] = $result->txn_id;
			$nestedData[] = $result->payment_method;
			$nestedData[] = $result->payment_status;
			$nestedData[] = service_finder_money_format($result->amount);
			$nestedData[] = $planname;
			$nestedData[] = $result->limit;
			
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
	
	/*Approve wired booking*/
	public function service_finder_booking_approve(){
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
			$adminemail = get_option( 'admin_email' );
			$error = array(
					'status' => 'error',
					'err_message' => $service_id->get_error_message()
					);
			echo json_encode($error);
		}else{
			$msg = (!empty($service_finder_options['booking-approve'])) ? $service_finder_options['booking-approve'] : esc_html__('Booking approved successfully', 'service-finder');
			$success = array(
					'status' => 'success',
					'suc_message' => esc_html__('Booking approved successfully.', 'service-finder'),
					);
			echo json_encode($success);
		}
	}
	
	/*Job Status*/
	public function service_finder_job_status(){
	global $wpdb, $service_finder_Tables;
	
		$jobid = (!empty($_POST['jobid'])) ? esc_html($_POST['jobid']) : '';
		
		update_post_meta($jobid,'_job_booking_status','complete');
		
		if(function_exists('service_finder_add_notices')) {
	
		$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'posts where `ID` = %d',$jobid));
		
		$noticedata = array(
					'customer_id' => $row->post_author,
					'target_id' => $jobid, 
					'topic' => 'Job Completed',
					'title' => esc_html__('Job Completed', 'service-finder'),
					'notice' => esc_html__('Job have been completed by service provider', 'service-finder')
					);
		service_finder_add_notices($noticedata);
		
		}
		
			
		$success = array(
				'status' => 'success',
				);
		echo json_encode($success);
	}
	
	/*Make Stripe Payment for Increase job apply limit*/
	public function service_finder_makePayment($arg = '',$customerID = '',$txnid = '',$payment_mode = ''){
			global $wpdb, $service_finder_Tables, $service_finder_options;
			$stripetoken = (!empty($arg['stripeToken'])) ? $arg['stripeToken'] : '';
			$provider_id = (!empty($arg['provider_id'])) ? $arg['provider_id'] : '';
			$plan = (!empty($arg['plan'])) ? $arg['plan'] : '';
			
			$planlimit = (!empty($service_finder_options['plan'.$plan.'-limit'])) ? $service_finder_options['plan'.$plan.'-limit'] : 0;
			$planprice = (!empty($service_finder_options['plan'.$plan.'-price'])) ? $service_finder_options['plan'.$plan.'-price'] : 0;
			
			$row = $wpdb->get_row('SELECT * FROM '.$service_finder_Tables->job_limits.' WHERE `provider_id` = "'.$provider_id.'"');
			if(!empty($row)){
				$paidlimit = $planlimit + $row->paid_limits;
				$available_limits = $planlimit + $row->available_limits;
			}else{
				$paidlimit = $planlimit;
				$available_limits = $planlimit;
			}
			
			
			
			$data = array(
					'paid_limits' => $paidlimit,
					'available_limits' => $available_limits,
					'txn_id' => $txnid,
					'payment_method' => $payment_mode,
					'payment_status' => 'paid',
					'current_plan' => $plan,
					);
			$where = array(
					'provider_id' => $provider_id
			);
			$res = $wpdb->update($service_finder_Tables->job_limits,wp_unslash($data),$where);
			
			$paydate = date('Y-m-d h:i:s');
			$txndata = array(
					'provider_id' => $provider_id,
					'payment_date' => $paydate,
					'txn_id' => $txnid,
					'plan' => $plan,
					'amount' => $planprice,
					'limit' => $planlimit,
					'payment_method' => $payment_mode,
					'payment_status' => 'paid',
					);
			$wpdb->insert($service_finder_Tables->transaction,wp_unslash($txndata));
			
			send_mail_after_joblimit_connect_purchase( $provider_id );
			
			if ( ! $res) {
				$adminemail = get_option( 'admin_email' );
				$allowedhtml = array(
					'a' => array(
						'href' => array(),
						'title' => array()
					),
				);
				$error = array(
						'status' => 'error',
						'err_message' => sprintf( wp_kses(esc_html__('Couldn&#8217;t make payment for increase job limit... please contact the <a href="mailto:%s">Administrator</a> !', 'service-finder'),$allowedhtml), $adminemail )
						);
				$service_finder_Errors = json_encode($error);
				return $service_finder_Errors;
			}else{
				$success = array(
						'status' => 'success',
						'suc_message' => esc_html__('Payment made successfully.', 'service-finder'),
						);
				$service_finder_Success = json_encode($success);
				return $service_finder_Success;
			}
			
		}	
		
	/*Make Stripe Payment for Increase job post limit*/
	public function service_finder_makeJobPostPayment($arg = '',$customerID = '',$txnid = '',$payment_mode = ''){
			global $wpdb, $service_finder_Tables, $service_finder_options;
			$stripetoken = (!empty($arg['stripeToken'])) ? $arg['stripeToken'] : '';
			$customer_id = (!empty($arg['customer_id'])) ? $arg['customer_id'] : '';
			$plan = (!empty($arg['plan'])) ? $arg['plan'] : '';
			
			$planlimit = (!empty($service_finder_options['job-post-plan'.$plan.'-limit'])) ? $service_finder_options['job-post-plan'.$plan.'-limit'] : 0;
			$planprice = (!empty($service_finder_options['job-post-plan'.$plan.'-price'])) ? $service_finder_options['job-post-plan'.$plan.'-price'] : 0;
			
			$row = $wpdb->get_row('SELECT * FROM '.$service_finder_Tables->job_limits.' WHERE `provider_id` = "'.$customer_id.'"');
			if(!empty($row)){
				$paidlimit = $planlimit + $row->paid_limits;
				$available_limits = $planlimit + $row->available_limits;
			}else{
				$paidlimit = $planlimit;
				$available_limits = $planlimit;
			}
			
			
			
			$data = array(
					'paid_limits' => $paidlimit,
					'available_limits' => $available_limits,
					'txn_id' => $txnid,
					'payment_method' => $payment_mode,
					'payment_status' => 'paid',
					'current_plan' => $plan,
					);
			$where = array(
					'provider_id' => $customer_id
			);
			$res = $wpdb->update($service_finder_Tables->job_limits,wp_unslash($data),$where);
			
			$paydate = date('Y-m-d h:i:s');
			$txndata = array(
					'provider_id' => $customer_id,
					'payment_date' => $paydate,
					'txn_id' => $txnid,
					'plan' => $plan,
					'amount' => $planprice,
					'limit' => $planlimit,
					'payment_method' => $payment_mode,
					'payment_status' => 'paid',
					);
			$wpdb->insert($service_finder_Tables->transaction,wp_unslash($txndata));
			
			send_mail_after_jobpost_limit_connect_purchase( $customer_id );
			
			if ( ! $res) {
				$adminemail = get_option( 'admin_email' );
				$allowedhtml = array(
					'a' => array(
						'href' => array(),
						'title' => array()
					),
				);
				$error = array(
						'status' => 'error',
						'err_message' => sprintf( wp_kses(esc_html__('Couldn&#8217;t make payment for increase job limit... please contact the <a href="mailto:%s">Administrator</a> !', 'service-finder'),$allowedhtml), $adminemail )
						);
				$service_finder_Errors = json_encode($error);
				return $service_finder_Errors;
			}else{
				$success = array(
						'status' => 'success',
						'suc_message' => esc_html__('Payment made successfully.', 'service-finder'),
						);
				$service_finder_Success = json_encode($success);
				return $service_finder_Success;
			}
			
		}			
				
}