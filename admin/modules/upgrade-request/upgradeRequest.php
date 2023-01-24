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
class SERVICE_FINDER_upgradeRequest extends SERVICE_FINDER_sedateManager{

	
	/*Initial Function*/
	public function service_finder_index()
    {
        
		/*Rander providers template*/
		$this->service_finder_render( 'index','upgrade-request' );
		
		/*Action for wp ajax call*/
		$this->service_finder_registerWpActions();
		
    }
	
	/*Actions for wp ajax call*/
	protected function service_finder_registerWpActions() {
       $_this = $this;
	   add_action(
                    'wp_ajax_get_upgrade_requests',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_get_upgrade_requests' ) );
                    }
						
                );
		add_action(
                    'wp_ajax_approve_upgrade_request',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_approve_upgrade_request' ) );
                    }
						
                );	
    }
	
	public function service_finder_get_upgrade_requests(){
		global $wpdb, $service_finder_Tables, $service_finder_options;
		$requestData= $_REQUEST;

		$requests = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'usermeta WHERE `meta_key` = "upgrade_request_status"');
		
		$data = array();
		
		foreach($requests as $result){
			$nestedData=array(); 
			$userid = $result->user_id;
			$requestdata = get_user_meta($userid,'upgrade_request',true);
			
			$nestedData['requestid'] = $result->umeta_id;
			$nestedData['providername'] = service_finder_getProviderFullName($userid);
			$nestedData['requestdate'] = service_finder_date_format(date('Y-m-d',$requestdata['time']));
			
			if($requestdata['current_package'] != ""){
	   	    $roleNum = intval(substr($requestdata['current_package'], 8));
			$currentpackagename = (!empty($service_finder_options['package'.$roleNum.'-name'])) ? $service_finder_options['package'.$roleNum.'-name'] : '';
		    }else{
			$currentpackagename = esc_html__('No Package','service-finder');
		    }
			
			$nestedData['currentpackage'] = $currentpackagename;
			
			if($requestdata['provider_role'] != ""){
	   	    $roleNum = intval(substr($requestdata['provider_role'], 8));
			$packagename = (!empty($service_finder_options['package'.$roleNum.'-name'])) ? $service_finder_options['package'.$roleNum.'-name'] : '';
		    }else{
			$packagename = 'NA';
		    }
			
			$nestedData['upgradepackage'] = $packagename;
			
			$payment_type = $requestdata['payment_type'];
			$payment_method = $requestdata['payment_mode'];
			$order_id = $requestdata['wired_invoiceid'];
			
			$paytype = ($payment_type == 'woocommerce') ? esc_html__('Woocommerce','service-finder') : esc_html__('Local','service-finder');
			
			if($payment_type == 'woocommerce' && ($payment_method == 'bacs' || $payment_method == 'cheque')){
			$wiredinvoiceid = $requestdata['wired_invoiceid'];
			}elseif($payment_type == 'woocommerce' && $payment_method != 'bacs' && $payment_method != 'cheque'){
			$wiredinvoiceid = 'N/A';
			}elseif(($payment_type == 'local' || $payment_type == "") && $payment_method == 'wire-transfer'){
			$wiredinvoiceid = $requestdata['wired_invoiceid'];
			}else{
			$wiredinvoiceid = 'N/A';
			}
			
			$paymentmethod = ($paymentmethod != '') ? service_finder_translate_static_status_string($payment_method) : 'N/A';
			
			$paymentinfo = '<span data-toggle="popover" data-container="body" data-placement="top" type="button" data-html="true" id="paymentinfo-'.$result->umeta_id.'" data-trigger="hover"><i class="fa fa-question-circle"></i></span>';
			$paymentinfo .= '<div id="popover-content-paymentinfo-'.$result->umeta_id.'" class="hide pop-full">
									<ul class="sf-popoverinfo-list">
										<li><span>'.esc_html__( 'Payment Type','service-finder' ).':</span> <span>'.$paytype.'</span></li>
										<li><span>'.esc_html__( 'Payment Method','service-finder' ).':</span> <span>'.$paymentmethod.'</span></li>
										<li><span>'.esc_html__( 'Invoice ID (Wire Transffer)','service-finder' ).':</span> <span>'.$wiredinvoiceid.'</span></li>
									</ul>
								</div>';
			
			$nestedData['amount'] = service_finder_currencysymbol().$requestdata['price'].' '.$paymentinfo;
			
			if($result->meta_value == 'pending' && $payment_type == 'woocommerce'){
			
				$order_id = get_user_meta($userid,'order_id',true);
				$nestedData['paymentstatus'] = '<a href="'.admin_url().'post.php?post='.$order_id.'&action=edit" target="_blank">'.esc_html__('Approve', 'service-finder').'</a>';
			
			}elseif($result->meta_value == 'pending' && get_user_meta($userid,'payment_mode',true) != 'woocommerce'){
				$nestedData['paymentstatus'] = '<a href="javascript:;" data-id="'.esc_attr($userid).'" class="approverequest">'.esc_html__('Approve', 'service-finder').'</a>';
			}else{
				if($payment_type == 'woocommerce' && $result->meta_value == 'approve'){
					$nestedData['paymentstatus'] = esc_html__( 'Completed', 'service-finder' );
				}elseif($payment_type == 'woocommerce' && $result->meta_value == 'cancelled'){
					$nestedData['paymentstatus'] = esc_html__( 'Cancelled', 'service-finder' );	
				}else{
					$nestedData['paymentstatus'] = esc_html__( 'Paid', 'service-finder' );
				}
			}
			
			$actionbtns = '';
			if($payment_type == 'woocommerce'){
				$actionbtns .= '<li><a href="'.admin_url().'post.php?post='.$order_id.'&action=edit" target="_blank"><i class="fa fa-shopping-cart"></i> '.esc_html__('View Order', 'service-finder').'</a></li>';
			}
			
			$actions = '<div class="dropdown action-dropdown dropdown-left">
						  <button class="action-button gray dropdown-toggle" type="button" data-toggle="dropdown"><i class="fa fa-ellipsis-v"></i></button>
						  <ul class="dropdown-menu">
							<li><a href="'.esc_url(service_finder_get_author_url($userid)).'" target="_blank"><i class="fa fa-eye"></i> '.esc_html__('View Profile', 'service-finder').'</a></li>
							'.$actionbtns.'
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
	
	public function service_finder_approve_upgrade_request(){
	global $wpdb, $service_finder_Tables, $service_finder_options;
	
	$userid = (isset($_POST['uid'])) ? esc_html($_POST['uid']) : '';

	$requestdata = get_user_meta($userid,'upgrade_request',true);
	
	update_user_meta( $userid, 'payment_mode', $requestdata['payment_mode'] );
	update_user_meta( $userid, 'wired_invoiceid', $requestdata['wired_invoiceid'] );
	update_user_meta( $userid, 'recurring_profile_type', $requestdata['recurring_profile_type'] );
	update_user_meta( $userid, 'provider_role', $requestdata['provider_role'] );
	
	if($requestdata['expire_limit'] > 0){
		update_user_meta($userid, 'expire_limit', $requestdata['expire_limit']);
	}
	
	if($requestdata['trial_package'] == 'yes'){
		update_user_meta($userid, 'trial_package', 'yes');
	}else{
		delete_user_meta($userid, 'trial_package');
	}
	delete_user_meta($userid, 'current_provider_status');
	update_user_meta( $userid, 'provider_activation_time', array( 'role' => $requestdata['provider_role'], 'time' => time()) );
	update_user_meta($userid, 'upgrade_request_status','approve');
	
	$email = service_finder_getProviderEmail($userid);
	
	$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('Provider', 'service-finder');	
	
	if(!empty($service_finder_options['send-to-provider-upgrade-request-approval'])){
		$message = $service_finder_options['send-to-provider-upgrade-request-approval'];
	}else{
		$message = 'Dear '.esc_html($providerreplacestring).',
		Congratulations! Your account upgraded Successfully';
	}
	
	$msg_body = $message;
	if(!empty($service_finder_options['send-to-provider-upgrade-request-approval-subject'])){
		$msg_subject = $service_finder_options['send-to-provider-upgrade-request-approval-subject'];
	}else{
		$msg_subject = 'Account Upgrade Notification';
	}
	
	service_finder_wpmailer($email,$msg_subject,$msg_body);
	
	$success = array(
			'status' => 'success',
			'suc_message' => esc_html__('Account has been upgrade Successfully', 'service-finder'),
			);
	echo json_encode($success);
	
	exit(0);
	}
	
}