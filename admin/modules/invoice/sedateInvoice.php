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
class SERVICE_FINDER_sedateInvoice extends SERVICE_FINDER_sedateManager{

	
	/*Initial Function*/
	public function service_finder_index()
    {
        
		/*Rander providers template*/
		$this->service_finder_render( 'index','invoice',$this->service_finder_getAllProvidersList() );
		
		/*Action for wp ajax call*/
		$this->service_finder_registerWpActions();
		
    }
	
	/*Actions for wp ajax call*/
	protected function service_finder_registerWpActions() {
       $_this = $this;
	   add_action(
                    'wp_ajax_get_admin_invoice',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_get_admin_invoice' ) );
                    }
						
                );
		add_action(
                    'wp_ajax_delete_admin_invoice',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_delete_admin_invoice' ) );
                    }
						
                );		
		add_action(
                    'wp_ajax_approve_wired_invoice',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_approve_wired_invoice' ) );
                    }
						
                );		
		add_action(
                    'wp_ajax_invoice_pay_via_masspay',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_invoice_pay_via_masspay' ) );
                    }
						
                );		
		add_action(
                    'wp_ajax_invoicepay_via_stripe_connect',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_invoicepay_via_stripe_connect' ) );
                    }
						
                );		
		add_action(
                    'wp_ajax_status_invoice_pay_to_provider',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_status_invoice_pay_to_provider' ) );
                    }
						
                );
				
    }
	
	/*Pay to Provider via stripe connect*/
	public function service_finder_invoicepay_via_stripe_connect(){
		global $wpdb,$service_finder_options, $service_finder_Tables;

		$providerid = (!empty($_POST['providerid'])) ? esc_html($_POST['providerid']) : '';
		$invoiceid = (!empty($_POST['invoiceid'])) ? esc_html($_POST['invoiceid']) : '';
		$amount = (!empty($_POST['amount'])) ? esc_html($_POST['amount']) : '';
		
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
			$payout = json_decode($payout);
			
				
			}else{
			
			$stripe_connect_id = get_user_meta($providerid,'stripe_connect_id',true);
			
			\Stripe\Stripe::setApiKey($secret_key);
            $transfer_args = array(
                'amount' => $totalcost,
                'currency' => strtolower(service_finder_currencycode()),
                'destination' => $stripe_connect_id
            );
            $payout = \Stripe\Transfer::create($transfer_args);
			$payout = json_decode($payout);
			}
			
			if($payout->status == 'pending' || $payout->status == 'in_transit' || $payout->status == 'paid'){
			
			if($payout->status == 'paid'){
				$invoiceidtablestatus = 'paid';
			}else{
				$invoiceidtablestatus = 'in-process';
			}
			
			$data = array(
					'paid_to_provider' => $invoiceidtablestatus,
					);
			
			$where = array(
					'id' => $invoiceid,
					);
			
			$booking_id = $wpdb->update($service_finder_Tables->invoice,wp_unslash($data),$where);
			
			$data = array(
					'created' => date('Y-m-d h:i:s',$payout->created),
					'arrival_date' => date('Y-m-d h:i:s',$payout->arrival_date),
					'provider_id' => $providerid,
					'booking_id' => $invoiceid,
					'connected_account_id' => $stripe_connect_id,
					'amount' => $amount,
					'stripe_connect_type' => $stripeconnecttype,
					'status' => $payout->status,
					'payout_id' => $payout->id,
					'payout_for' => 'invoice'
					);
					
			$wpdb->insert($service_finder_Tables->payout_history,wp_unslash($data));
			
			if(function_exists('service_finder_add_notices')) {
				$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->invoice.' WHERE `id` = %d',$invoiceid));	
				$noticedata = array(
						'provider_id' => $row->provider_id,
						'target_id' => $row->id, 
						'topic' => 'Booking Payment',
						'title' => esc_html__('Booking Payment', 'service-finder'),
						'notice' => sprintf(esc_html__('Site administrator release payout. It will take some time to reflect in your account. Invoice Ref id is #%d', 'service-finder'),$invoiceid)
						);
				service_finder_add_notices($noticedata);
			
			}
			
			$success = array(
					'status' => 'success',
					'suc_message' => esc_html__('Payout initiate successfully.', 'service-finder'),
					);
			echo json_encode($success);
			}else{
			$error = array(
					'status' => 'error',
					'err_message' => $payout->err_message
					);
			echo json_encode($error);
			}
			
        } catch (\Stripe\Error\InvalidRequest $e) {
           $error = array(
					'status' => 'error',
					'err_message' => $e->getMessage()
					);
			echo json_encode($error);
        } catch (\Stripe\Error\Authentication $e) {
           $error = array(
					'status' => 'error',
					'err_message' => $e->getMessage()
					);
			echo json_encode($error);
        } catch (\Stripe\Error\ApiConnection $e) {
            $error = array(
					'status' => 'error',
					'err_message' => $e->getMessage()
					);
			echo json_encode($error);
        } catch (\Stripe\Error\Base $e) {
            $error = array(
					'status' => 'error',
					'err_message' => $e->getMessage()
					);
			echo json_encode($error);
        } catch (Exception $e) {
            $error = array(
					'status' => 'error',
					'err_message' => $e->getMessage()
					);
			echo json_encode($error);
        }
			
		exit(0);
	}
	
	/*Change provider payment status from pending to paid*/
	public function service_finder_status_invoice_pay_to_provider(){
		global $wpdb, $service_finder_options, $service_finder_Tables;
		$receiver          = array();
		
		$invoiceid = (!empty($_POST['invoiceid'])) ? esc_html($_POST['invoiceid']) : '';
		
		$data = array(
				'paid_to_provider' => 'paid',
				);
		
		$where = array(
				'id' => $invoiceid,
				);
		
		$invoice_id = $wpdb->update($service_finder_Tables->invoice,wp_unslash($data),$where);
				
		if(is_wp_error($invoice_id)){
			$error = array(
					'status' => 'error',
					'err_message' => $invoice_id->get_error_message()
					);
			echo json_encode($error);
		}else{
			
			if(function_exists('service_finder_add_notices')) {
				$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->invoice.' WHERE `id` = %d',$invoiceid));	
				$noticedata = array(
						'provider_id' => $row->provider_id,
						'target_id' => $row->id, 
						'topic' => 'Booking Invoice Payment',
						'title' => esc_html__('Booking Invoice Payment', 'service-finder'),
						'notice' => esc_html__('Site administrator paid you for your service via bank transfer', 'service-finder')
						);
				service_finder_add_notices($noticedata);
			
			}
			
			$success = array(
					'status' => 'success',
					'suc_message' => esc_html__('Payment status changed successfully.', 'service-finder'),
					);
			echo json_encode($success);
		}
		 
		exit(0);
	}
	
	/*Pay to Provider via paypal masspay*/
	public function service_finder_invoice_pay_via_masspay(){
		global $service_finder_options;
		$receiver          = array();
		
		$invoiceid = (!empty($_POST['invoiceid'])) ? esc_html($_POST['invoiceid']) : '';
		$providerid = (!empty($_POST['providerid'])) ? esc_html($_POST['providerid']) : '';
		$amount = (!empty($_POST['amount'])) ? esc_html($_POST['amount']) : '';
		
		$args = array( 
				'providerid'	=> $providerid,
				'bookingid'		=> $invoiceid,
				'payoutamount'	=> $amount,
				'payouttype'	=> 'manual',
		);
		
		$payout = new service_finder_payment_masspay();
		$response = $payout->service_finder_process_payment($args);
		
		if($response['type'] == 'success')
		{
			$data = array(
					'paid_to_provider' => 'paid',
					);
			
			$where = array(
					'id' => $invoiceid,
					);
			
			$invoice_id = $wpdb->update($service_finder_Tables->invoice,wp_unslash($data),$where);
		
			$success = array(
					'status' => 'success',
					'role' => strtolower($role), 
					'suc_message' => $response['message']
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
		
		exit(0);
	}
	
	/*Display invoice into datatable*/
	public function service_finder_get_admin_invoice(){
		global $wpdb, $service_finder_Tables, $service_finder_options;
		$requestData= $_REQUEST;
		$currUser = wp_get_current_user(); 

		$bookingid = (isset($_POST['bookingid'])) ? esc_html($_POST['bookingid']) : '';
		
		if($bookingid != ""){
		$sql = $wpdb->prepare("SELECT * FROM ".$service_finder_Tables->invoice." WHERE `booking_id` = %d",$bookingid);
		}else{
		$sql = "SELECT * FROM ".$service_finder_Tables->invoice;		
		}
		
		$invoices = $wpdb->get_results($sql);
		
		$data = array();
		
		$payment_methods = (!empty($service_finder_options['payment-methods'])) ? $service_finder_options['payment-methods'] : '';
		
		foreach($invoices as $result){
			$nestedData=array(); 
		
			$nestedData['invoiceid'] = $result->id;
			$nestedData['delete'] = '<div class="checkbox sf-radio-checkbox">
  <input type="checkbox" id="invoice-'.esc_attr($result->id).'" class="deleteInvoiceRow" value="'.esc_attr($result->id).'">
  <label for="invoice-'.esc_attr($result->id).'"></label>
</div>';

			if($result->charge_admin_fee_from == 'provider'){
				$invoiceamount = $result->grand_total - $result->adminfee;
			}elseif($result->charge_admin_fee_from == 'customer'){
				$invoiceamount = $result->grand_total;
			}else{
				$invoiceamount = $result->grand_total;
			}
			
			$q = $wpdb->get_row($wpdb->prepare('SELECT name FROM '.$service_finder_Tables->customers.' WHERE `email` = "%s" GROUP BY email',$result->customer_email));
			$nestedData['refno'] = $result->reference_no;
			$nestedData['providername'] = service_finder_getProviderFullName($result->provider_id);
			$nestedData['customername'] = $q->name;
			$nestedData['duedate'] = $result->duedate;
			
			$adminfee = ($result->adminfee != '') ? service_finder_money_format($result->adminfee) : 'N/A';
			$displayinvoiceamount = ($invoiceamount != '') ? service_finder_money_format($invoiceamount) : 'N/A';
			
			$amountinfo = '<span data-toggle="popover" data-container="body" data-placement="top" type="button" data-html="true" id="amountinfo-'.$result->id.'" data-trigger="hover"><i class="fa fa-question-circle"></i></span>';
			$amountinfo .= '<div id="popover-content-amountinfo-'.$result->id.'" class="hide pop-full">
									<ul class="sf-popoverinfo-list">
										<li><span>'.esc_html__( 'Admin Fee','service-finder' ).':</span> <span>'.$adminfee.'</span></li>
										<li><span>'.esc_html__( 'Provider Fee','service-finder' ).':</span> <span>'.$displayinvoiceamount.'</span></li>
									</ul>
								</div>';
								
			$nestedData['amount'] = service_finder_money_format($result->grand_total).' '.$amountinfo;
			
			$now = time();
			$date = $result->duedate;
			
			if($result->status == 'pending' && strtotime($date) < $now){
				$status = esc_html__('Overdue', 'service-finder');
			}else{
				$status = service_finder_translate_static_status_string($result->status);
			}
			
			$payment_type = $result->payment_mode;
			$payment_method = $result->payment_type;
			$order_id = $result->txnid;
			
			$paytype = ($payment_type == 'woocommerce') ? esc_html__('Woocommerce','service-finder') : esc_html__('Local','service-finder');
			$paymentmethod = service_finder_translate_static_status_string($payment_method);
			$transactionid = $result->txnid;
			
			if($payment_type == 'woocommerce' && ($payment_method == 'bacs' || $payment_method == 'cheque')){
			$wiredinvocieid = $result->txnid;
			}elseif($payment_type == 'woocommerce' && $payment_method != 'bacs' && $payment_method != 'cheque'){
			$wiredinvocieid = 'N/A';
			}elseif(($payment_type == 'local' || $payment_type == "") && $payment_method == 'wire-transfer'){
			$wiredinvocieid = $result->txnid;
			}else{
			$wiredinvocieid = 'N/A';
			}
			
			$paymentmethod = ($paymentmethod != '') ? $paymentmethod : 'N/A';
			$transactionid = ($transactionid != '') ? $transactionid : 'N/A';
			$status = ($result->status != '') ? service_finder_translate_static_status_string($result->status) : 'N/A';
			
			$paymentinfo = '<span data-toggle="popover" data-container="body" data-placement="top" type="button" data-html="true" id="paymentinfo-'.$result->id.'" data-trigger="hover"><i class="fa fa-question-circle"></i></span>';
			$paymentinfo .= '<div id="popover-content-paymentinfo-'.$result->id.'" class="hide pop-full">
									<ul class="sf-popoverinfo-list">
										<li><span>'.esc_html__( 'Payment Type','service-finder' ).':</span> <span>'.$paytype.'</span></li>
										<li><span>'.esc_html__( 'Payment Method','service-finder' ).':</span> <span>'.$paymentmethod.'</span></li>
										<li><span>'.esc_html__( 'Invoice ID (Wire Transffer)','service-finder' ).':</span> <span>'.$wiredinvocieid.'</span></li>
										<li><span>'.esc_html__( 'Transaction ID','service-finder' ).':</span> <span>'.$transactionid.'</span></li>
										<li><span>'.esc_html__( 'Payment Status','service-finder' ).':</span> <span>'.$status.'</span></li>
									</ul>
								</div>';
			
			$nestedData['status'] = $status.' '.$paymentinfo;
			
			if($result->booking_id > 0){
			$nestedData['bookingid'] = '<a href="javascript:;" data-toggle="modal" data-target="#invoice-booking-modal" data-bookingid="'.esc_attr($result->booking_id).'" class="viewbookingdeatils">#'.$result->booking_id.'</a>';
			}else{
			$nestedData['bookingid'] = '-';
			}
			
			$paytoproviderstatus = '';
			if($result->paid_to_provider == 'pending'){
				
				$paytoproviderstatus = '<button type="button" data-invoiceid="'.esc_attr($result->id).'" class="btn btn-primary statusinvoicepaytoprovider" title="'.esc_html__('Change Payment Status to Paid', 'service-finder').'">'.esc_html__('Change Status', 'service-finder').'</button>';
				
				
			}elseif($result->paid_to_provider == 'paid'){
				$paytoproviderstatus = esc_html__('Paid', 'service-finder');
			}else{
				$paytoproviderstatus = 'N/A';
			}
			
			$nestedData['payviabank'] = $paytoproviderstatus;
			
			$paynow = '';
			if($result->paid_to_provider == 'pending' && $invoiceamount > 0 && $result->payment_type == "paypal"){
				$paynow = '<button data-toggle="tooltip" type="button" data-invoiceid="'.esc_attr($result->id).'" data-providerid="'.esc_attr($result->provider_id).'" data-amount="'.esc_attr($invoiceamount).'" class="btn btn-primary invoicepaytoprovider" title="'.esc_html__('Pay Now', 'service-finder').'">'.esc_html__('Pay Now', 'service-finder').'</button>';
			}elseif($result->paid_to_provider == 'pending' && $result->stripe_token != "" && $invoiceamount > 0 && $result->payment_type == "stripe"){
				
				$stripeconnecttype = (!empty($service_finder_options['stripe-connect-type'])) ? esc_html($service_finder_options['stripe-connect-type']) : '';
			
				$acct_id = service_finder_get_stripe_connect_id($result->provider_id);
				
				if($acct_id != '')
				{
				if(service_finder_get_stripe_connect_avl_balance($result->provider_id) >= $invoiceamount)
				{
				$paynow = '<button data-toggle="tooltip" type="button" data-invoiceid="'.esc_attr($result->id).'" data-providerid="'.esc_attr($result->provider_id).'" data-amount="'.esc_attr($invoiceamount).'" class="btn btn-primary invoicepaytoproviderviastripe" title="'.esc_html__('Pay Now', 'service-finder').'">'.esc_html__('Pay Now', 'service-finder').'</button>';
				}else
				{
				$paynow = '<button data-toggle="tooltip" type="button" class="btn btn-table pay-disable" title="'.esc_html__('Payout balance not avialable yet.', 'service-finder').'">'.esc_html__('Pay Now', 'service-finder').'</button>';
				}
				}else{
				$paynow = '<button data-toggle="tooltip" type="button" class="btn btn-table pay-disable" title="'.esc_html__('This provider connect account is not connected to your stripe account yet.', 'service-finder').'">'.esc_html__('Pay Now', 'service-finder').'</button>';
				}
				
				
			}elseif($result->paid_to_provider == 'paid'){
				$paynow = esc_html__('Paid', 'service-finder');
			}elseif($result->paid_to_provider == 'in-process'){
				$paynow = esc_html__('In-process', 'service-finder');
			}else{
				$paynow = service_finder_translate_static_status_string($result->paid_to_provider);
			}
			
			$nestedData['payviapaypal'] = $paynow;
			$actionbtns = '';
			if($payment_type == 'woocommerce'){
				$actionbtns .= '<li><a href="'.admin_url().'post.php?post='.$order_id.'&action=edit" target="_blank"><i class="fa fa-shopping-cart"></i> '.esc_html__('View Order', 'service-finder').'</a></li>';
			}
			
			if($result->status == 'on-hold' && $payment_type != 'woocommerce'){
				$actionbtns .= '<li><a href="javascript:;" data-id="'.esc_attr($result->id).'" class="approve_wiredinvoice"><i class="fa fa-check"></i> '.esc_html__('Approve', 'service-finder').'</a></li>';
			}elseif($result->status == 'on-hold' && $payment_type == 'woocommerce' && $result->txnid != ""){
				$actionbtns .= '<li><a href="'.admin_url().'post.php?post='.$result->txnid.'&action=edit" target="_blank"><i class="fa fa-check"></i> '.esc_html__('Approve', 'service-finder').'</a></li>';
			}
			
			if($actionbtns != '')
			{
			$actions = '<div class="dropdown action-dropdown dropdown-left">
						  <button class="action-button gray dropdown-toggle" type="button" data-toggle="dropdown"><i class="fa fa-ellipsis-v"></i></button>
						  <ul class="dropdown-menu">
							'.$actionbtns.'
							<li><a href="javascript:;"><i class="fa fa-close"></i> '.esc_html__( 'Close','service-finder' ).'</a></li>
						  </ul>
						</div>';
			}else{
			$actions = 'N/A';
			}
			
			$nestedData['actions'] = $actions;
			
			$data[] = $nestedData;
		}
		
		$json_data = array( "data" => $data );
	
		echo json_encode($json_data);
	
		exit;
	}
	
	/*Delete Invoice*/
	public function service_finder_delete_admin_invoice(){
	global $wpdb, $service_finder_Tables;
			$data_ids = $_REQUEST['data_ids'];
			$data_id_array = explode(",", $data_ids); 
			if(!empty($data_id_array)) {
				foreach($data_id_array as $id) {
					$sql = $wpdb->prepare("DELETE FROM ".$service_finder_Tables->invoice." WHERE id = %d",$id);
					$query=$wpdb->query($sql);
				}
			}
	}
	
	public function service_finder_approve_wired_invoice(){
	global $wpdb, $service_finder_Tables;
	
	$invoiceid = (isset($_POST['invoiceid'])) ? esc_html($_POST['invoiceid']) : '';
	
	$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->invoice.' WHERE `id` = %d',$invoiceid));

	if(!empty($row)){
	$provider_id = $row->provider_id;
	$customer_email = $row->customer_email;
	
	$data = array(
	'status' => 'paid',
	);
	
	$where = array(
	'id' => $invoiceid
	);
	
	$wpdb->update($service_finder_Tables->invoice,wp_unslash($data),$where);
	
	if(function_exists('service_finder_add_notices')) {

		$noticedata = array(
				'provider_id' => $provider_id,
				'target_id' => $invoiceid, 
				'topic' => 'Invoice Paid',
				'title' => esc_html__('Invoice Paid', 'service-finder'),
				'notice' => sprintf( esc_html__('Invoice paid by %s', 'service-finder'), $customer_email ),
				);
		service_finder_add_notices($noticedata);
	
	}
	
	service_finder_SendInvoicePaidMailToProvider($invoiceid);
	service_finder_SendInvoicePaidMailToCustomer($invoiceid);
	
	$success = array(
			'status' => 'success',
			'suc_message' => esc_html__('Invoice paid via wire transfer successful', 'service-finder'),
			);
	echo json_encode($success);
	
	}
	
	exit(0);
	}
	
}