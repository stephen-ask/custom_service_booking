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

/**
 * Class SERVICE_FINDER_sedateFeatured
 */
class SERVICE_FINDER_sedateFeatured extends SERVICE_FINDER_sedateManager{

	
	/*Initial Function*/
	public function service_finder_index()
    {
        
		/*Rander providers template*/
		$this->service_finder_render( 'index','featured' );
		
		/*Action for wp ajax call*/
		$this->service_finder_registerWpActions();
		
    }
	
	/*Actions for wp ajax call*/
	protected function service_finder_registerWpActions() {
       $_this = $this;
	   add_action(
                    'wp_ajax_get_featured',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_get_featured' ) );
                    }
						
                );
		add_action(
                    'wp_ajax_featured_approve',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_featured_approve' ) );
                    }
						
                );	
		add_action(
                    'wp_ajax_featured_decline',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_featured_decline' ) );
                    }
						
                );	
		add_action(
                    'wp_ajax_featured_edit_price',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_featured_edit_price' ) );
                    }
						
                );			
									
				
    }
	
	/*Display featured providers into datatable*/
	public function service_finder_get_featured(){
		global $wpdb, $service_finder_Tables;
		$requestData= $_REQUEST;

		$providers = $wpdb->get_results('SELECT featured.id, featured.paymenttype, featured.payment_mode, featured.txnid, featured.date, featured.feature_status, provider.full_name, provider.category_id, featured.provider_id, featured.amount, featured.days, featured.status, featured.paypal_transaction_id FROM '.$service_finder_Tables->feature.' as featured INNER JOIN '.$service_finder_Tables->providers.' as provider on featured.provider_id = provider.wp_user_id');
		
		$data = array();
		
		foreach($providers as $result){
			$nestedData=array(); 
		
			$nestedData['featuredid'] = $result->id;
			$nestedData['providername'] = $result->full_name;
			$nestedData['numberofdays'] = $result->days;
			if($result->feature_status == 'active'){
			$nestedData['startdate'] = service_finder_date_format($result->date);
			$nestedData['enddate'] = service_finder_date_format(date('Y-m-d',strtotime($result->date .'+'.$result->days.' day')));
			}else{
			$nestedData['startdate'] = 'N/A';
			$nestedData['enddate'] = 'N/A';
			}
			
			if($result->status == "Payment Pending"){
			$editprice = ' <a href="javascript:;" class="btn btn-success btn-xs editfeaturedprice" data-id="'.esc_attr($result->id).'" data-amount="'.esc_attr($result->amount).'">'.esc_html__('Edit Price', 'service-finder').'</a>';
			}else{
			$editprice = 'N/A';
			}
			if($result->amount > 0){
			$nestedData['amount'] = service_finder_money_format($result->amount).' '.$editprice;
			}else{
			$nestedData['amount'] = 'N/A';
			}
			
			$payment_type = $result->payment_mode;
			$payment_method = $result->paymenttype;
			$order_id = $requestdata['wired_invoiceid'];
			
			$paytype = ($payment_type == 'woocommerce') ? esc_html__('Woocommerce','service-finder') : esc_html__('Local','service-finder');
			
			if($payment_type == 'woocommerce' && ($payment_method == 'bacs' || $payment_method == 'cheque')){
			$wiredtransactionid = $result->txnid;
			}elseif($payment_type == 'woocommerce' && $payment_method != 'bacs' && $payment_method != 'cheque'){
			$wiredtransactionid = 'N/A';
			}elseif(($payment_type == 'local' || $payment_type == "") && $payment_method == 'wire-transfer'){
			$wiredtransactionid = $result->txnid;
			}else{
			$wiredtransactionid = 'N/A';
			}
			
			if($payment_type == 'woocommerce'){
			$transactionid = $result->txnid;
			}else{
			$transactionid = $result->paypal_transaction_id;
			}
			
			$paymentmethod = ($payment_method != '') ? service_finder_translate_static_status_string($payment_method) : 'N/A';
			$transactionid = ($transactionid != '') ? $transactionid : 'N/A';
			$status = ($result->status != '') ? $result->status : 'N/A';
			
			$paymentinfo = '<span data-toggle="popover" data-container="body" data-placement="top" type="button" data-html="true" id="paymentinfo-'.$result->id.'" data-trigger="hover"><i class="fa fa-question-circle"></i></span>';
			$paymentinfo .= '<div id="popover-content-paymentinfo-'.$result->id.'" class="hide pop-full">
									<ul class="sf-popoverinfo-list">
										<li><span>'.esc_html__( 'Payment Type','service-finder' ).':</span> <span>'.$paytype.'</span></li>
										<li><span>'.esc_html__( 'Payment Method','service-finder' ).':</span> <span>'.$paymentmethod.'</span></li>
										<li><span>'.esc_html__( 'Invoice ID (Wire Transffer)','service-finder' ).':</span> <span>'.$wiredtransactionid.'</span></li>
										<li><span>'.esc_html__( 'Transaction ID','service-finder' ).':</span> <span>'.$transactionid.'</span></li>
										<li><span>'.esc_html__( 'Payment Status','service-finder' ).':</span> <span>'.$status.'</span></li>
									</ul>
								</div>';
			
			if($result->status == "Declined"){
			$nestedData['status'] = 'Declined'.' '.$paymentinfo;
			}else{
			$nestedData['status'] = ucfirst($result->feature_status).' '.$paymentinfo;
			}
			
			$actionbtns = '';
			if($result->amount > 0 || $result->status == "Declined" || $result->status == "Free"){
				if($result->status == "on-hold" && $result->paymenttype == "woocommerce"){
				$actionbtns .= '<li><a href="'.admin_url().'post.php?post='.$result->txnid.'&action=edit" target="_blank"><i class="fa fa-check"></i> '.esc_html__('Approve', 'service-finder').'</a></li>';
				}elseif($result->paymenttype == "wire-transfer" && $result->status == "on-hold"){
				$actionbtns .= '<li><a href="javascript:;" data-id="'.esc_attr($result->id).'" id="approve-wired"><i class="fa fa-check"></i> '.esc_html__('Approve After Wire Transfer', 'service-finder').'</a></li>';
				}
			}else{
				$actionbtns = '<li><a href="javascript:;" data-id="'.esc_attr($result->id).'" id="approve-bx"><i class="fa fa-check"></i> '.esc_html__('Approve', 'service-finder').'</a></li> <li><a id="decline-bx" data-id="'.esc_attr($result->id).'" href="javascript:;"><i class="fa fa-times"></i> '.esc_html__('Decline', 'service-finder').'</a></li>';
			}
			
			if($payment_type == 'woocommerce'){
				$actionbtns = '<li><a href="'.admin_url().'post.php?post='.$result->txnid.'&action=edit" target="_blank"><i class="fa fa-shopping-cart"></i> '.esc_html__('View Order', 'service-finder').'</a></li>';
			}
			
			$actions = '<div class="dropdown action-dropdown dropdown-left">
						  <button class="action-button gray dropdown-toggle" type="button" data-toggle="dropdown"><i class="fa fa-ellipsis-v"></i></button>
						  <ul class="dropdown-menu">
							'.$actionbtns.'
							<li><a href="'.esc_url(service_finder_get_author_url($result->provider_id)).'" target="_blank"><i class="fa fa-eye"></i> '.esc_html__('View Profile', 'service-finder').'</a></li>
							<li><a href="javascript:;"><i class="fa fa-close"></i> '.esc_html__( 'Close','service-finder' ).'</a></li>
						  </ul>
						</div>';
			
			$nestedData['actions'] = $actions;
			
			
			$data[] = $nestedData;
		}
		
		$json_data = array( "data" => $data );
	
		echo json_encode($json_data);
	
		exit;
	}
	
	/*Approve Featured Request*/
	public function service_finder_featured_approve(){
	global $wpdb, $service_finder_Tables, $service_finder_options;

	if($_POST['featured_amount'] > 0)
	{
	$res = $wpdb->query($wpdb->prepare('UPDATE '.$service_finder_Tables->feature.' SET `status` = "Payment Pending", `amount` = %f WHERE `id` = %d',$_POST['featured_amount'],$_POST['fid']));
	}else{
	$date = date('Y-m-d H:i:s');
	$data = array(
			'status' => 'Free',
			'feature_status' => 'active',
			'date' => $date,
			);

	$where = array(
			'id' => $_POST['fid'],
			);
	$wpdb->update($service_finder_Tables->feature,wp_unslash($data),$where);
	
	$getfeature = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->feature.' WHERE `id` = %d',$_POST['fid']));
	
	$data = array(
			'featured' => 1,
			);
	
	$where = array(
			'wp_user_id' => $getfeature->provider_id,
			);
	$wpdb->update($service_finder_Tables->providers,wp_unslash($data),$where);
	}
	
	$email = service_finder_getProviderEmail($getfeature->provider_id);
	
	if(!empty($service_finder_options['send-to-provider-featured-request-approval'])){
		$message = $service_finder_options['send-to-provider-featured-request-approval'];
	}else{
		$message = 'Dear,
		Your account has been approved for feature. Please make payment to activate';
	}
	
	$msg_body = $message;
	if(!empty($service_finder_options['provider-featured-request-approval-subject'])){
		$msg_subject = $service_finder_options['provider-featured-request-approval-subject'];
	}else{
		$msg_subject = 'Approved Feature Request';
	}
	
	if(function_exists('service_finder_add_notices')) {
		
		$noticedata = array(
				'provider_id' => $getfeature->provider_id,
				'target_id' => $fid, 
				'topic' => 'Feature Request Approved',
				'title' => esc_html__('Feature Request Approved', 'service-finder'),
				'notice' => esc_html__('Your feature request has been approved.', 'service-finder')
				);
		service_finder_add_notices($noticedata);
	
	}
	
	service_finder_wpmailer($email,$msg_subject,$msg_body);
	
	$success = array(
			'status' => 'success',
			'suc_message' => esc_html__('Approved Successfully', 'service-finder'),
			);
	echo json_encode($success);
	
	exit(0);		
	}
	
	/*Update Featured Request*/
	public function service_finder_featured_edit_price(){
	global $wpdb, $service_finder_Tables;
	
	$fid = (isset($_POST['fid'])) ? esc_html($_POST['fid']) : '';
	$featured_amount = (isset($_POST['featured_amount'])) ? esc_html($_POST['featured_amount']) : '';

	$res = $wpdb->query($wpdb->prepare('UPDATE '.$service_finder_Tables->feature.' SET `status` = "Payment Pending", `amount` = %f WHERE `id` = %d',$_POST['featured_amount'],$_POST['fid']));
	
	$data = array(
			'amount' => esc_attr($featured_amount)
			);
	$where = array(
			'id' => esc_attr($fid)
			);		

	$wpdb->update($service_finder_Tables->feature,wp_unslash($data),$where);

	$getfeature = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->feature.' WHERE `id` = %d',$fid));
	
	if(function_exists('service_finder_add_notices')) {
		
		$noticedata = array(
				'provider_id' => $getfeature->provider_id,
				'target_id' => $fid, 
				'topic' => 'Featured Amount Edited',
				'title' => esc_html__('Featured Amount Edited', 'service-finder'),
				'notice' => esc_html__('Featured Amount has been updated', 'service-finder')
				);
		service_finder_add_notices($noticedata);
	
	}
	
	$success = array(
			'status' => 'success',
			'suc_message' => esc_html__('Featured Amount has been updated', 'service-finder'),
			);
	echo json_encode($success);
	
	exit(0);		
	}
	
	/*Decline Featured Request*/
	public function service_finder_featured_decline(){
	global $wpdb, $service_finder_Tables;

	$res = $wpdb->query($wpdb->prepare('UPDATE '.$service_finder_Tables->feature.' SET `status` = "Declined", `comments` = "%s" WHERE `id` = %d',$_POST['comment'],$_POST['fid']));
	
	$getfeature = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->feature.' WHERE `id` = %d',$_POST['fid']));
	
	$email = service_finder_getProviderEmail($getfeature->provider_id);
	$message = esc_html__('Dear Provider,', 'service-finder');
	$message .= esc_html__('Your account has been declined for feature.', 'service-finder');
	
	$message .= '<br>'.$_POST['comment'];
	
	$msg_body = $message;
	$msg_subject = esc_html__('Declined Feature Request', 'service-finder');
	if(service_finder_wpmailer($email,$msg_subject,$msg_body)) {
		$success = array(
				'status' => 'success',
				'suc_message' => esc_html__('Declined Successfully', 'service-finder'),
				);
		echo json_encode($success);
	}else{
		$adminemail = get_option( 'admin_email' );
		$allowedhtml = array(
			'a' => array(
				'href' => array(),
				'title' => array()
			),
		);
		$error = array(
				'status' => 'error',
				'err_message' => sprintf( wp_kses(esc_html__('Couldn&#8217;t declined... please contact the <a href="mailto:%s">Administrator</a> !', 'service-finder'),$allowedhtml), $adminemail )
				);		
		echo json_encode($error);
	}

	exit(0);		
	}
	
}