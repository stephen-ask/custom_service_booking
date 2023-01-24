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

class SERVICE_FINDER_WALLET_REQUEST extends SERVICE_FINDER_sedateManager{

	
	/*Initial Function*/
	public function service_finder_index()
    {
        
		/*Rander providers template*/
		$this->service_finder_render( 'index','wallet-request' );
		
		/*Action for wp ajax call*/
		$this->service_finder_registerWpActions();
		
    }
	
	/*Actions for wp ajax call*/
	protected function service_finder_registerWpActions() {
       $_this = $this;
	   add_action(
                    'wp_ajax_get_wallet_requests',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_get_wallet_requests' ) );
                    }
						
                );
		add_action(
                    'wp_ajax_approve_wallet_amount_request',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_approve_wallet_amount_request' ) );
                    }
						
                );	
    }
	
	public function service_finder_get_wallet_requests(){
	global $wpdb, $service_finder_Tables, $service_finder_options;
	$requestData= $_REQUEST;
	
	// getting total number records without any search
	$sql = "SELECT * FROM ".$service_finder_Tables->wallet_transaction." WHERE 1 = 1";
	$requests = $wpdb->get_results($sql);
	
	$data = array();
	
	foreach($requests as $result){
		$nestedData=array(); 
	
		$nestedData['requestid'] = $result->id;
		$nestedData['providername'] = '<a href="'.esc_url(service_finder_get_author_url($result->user_id)).'" target="_blank">'.service_finder_getProviderFullName($result->user_id).'</a>';
		$nestedData['transactiondate'] = date('d-m-Y',strtotime($result->payment_date));
		$nestedData['wiredinvoiceid'] = $result->txn_id;
		$nestedData['paymentmethod'] = $result->payment_method;
		$nestedData['amount'] = service_finder_money_format($result->amount);
		
		if($result->payment_status == 'on-hold' && $result->payment_mode == 'woocommerce'){
			$nestedData['paymentstatus'] = '<a href="'.admin_url().'post.php?post='.$result->txn_id.'&action=edit" target="_blank">'.esc_html__('Approve', 'service-finder').'</a>';
		}elseif($result->payment_status == 'pending' && $row->payment_mode != 'woocommerce'){
			$nestedData['paymentstatus'] = '<a href="javascript:;" data-id="'.esc_attr($result->id).'" class="approve_wallet_payment">'.esc_html__('Approve', 'service-finder').'</a>';
		}else{
			if($row->payment_mode == 'woocommerce'){
				$nestedData['paymentstatus'] = service_finder_translate_static_status_string($result->payment_status);
			}else{
				$nestedData['paymentstatus'] = esc_html__( 'Completed', 'service-finder' );
			}
		}
		
		$actionbtns = '';
		if($result->payment_mode == 'woocommerce'){
			$actionbtns .= '<li><a href="'.admin_url().'post.php?post='.$result->txn_id.'&action=edit" target="_blank">><i class="fa fa-shopping-cart"></i> '.esc_html__('View Order', 'service-finder').'</a></li>';
		}
		
		$actions = '<div class="dropdown action-dropdown dropdown-left">
					  <button class="action-button gray dropdown-toggle" type="button" data-toggle="dropdown"><i class="fa fa-ellipsis-v"></i></button>
					  <ul class="dropdown-menu">
						<li><a href="'.esc_url(service_finder_get_author_url($result->user_id)).'" target="_blank"><i class="fa fa-eye"></i> '.esc_html__('View Profile', 'service-finder').'</a></li>
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
	
	public function service_finder_approve_wallet_amount_request(){
	global $wpdb, $service_finder_Tables;
	
	$request_id = (isset($_POST['request_id'])) ? esc_html($_POST['request_id']) : '';
	
	$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->wallet_transaction.' WHERE `id` = %d',$request_id));
	
	if(!empty($row)){
		$amount = $row->amount;
		$provider_id = $row->user_id;
		
		service_finder_add_wallet_amount($provider_id,$amount);
		
		$data = array(
				'payment_status' => 'completed',
				);
		$where = array(
				'id' => $request_id
		);		
		$wpdb->update($service_finder_Tables->wallet_transaction,wp_unslash($data),$where);
	}
	
	$success = array(
			'status' => 'success',
			'suc_message' => esc_html__('Wallet amount added successfully', 'service-finder'),
			);
	echo json_encode($success);

	exit(0);
	}
	
}