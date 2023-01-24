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
 * Class SERVICE_FINDER_sedateProviders
 */
class SERVICE_FINDER_sedateProviders extends SERVICE_FINDER_sedateManager{

	
	/*Initial Function*/
	public function service_finder_index()
    {
        
		/*Rander providers template*/
		$this->service_finder_render( 'index','providers' );
		
		/*Action for wp ajax call*/
		$this->service_finder_registerWpActions();
		
    }
	
	/*Identity check functionality*/
	public function service_finder_identitycheck()
    {
        
		/*Rander providers template*/
		$this->service_finder_render( 'identity-check','providers' );
		
		/*Action for wp ajax call*/
		$this->service_finder_registerWpActions();
		
    }

	/*Actions for wp ajax call*/
	protected function service_finder_registerWpActions() {
       $_this = $this;
	   add_action(
                    'wp_ajax_get_providers',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_get_providers' ) );
                    }
						
                );
		add_action(
                    'wp_ajax_delete_providers',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_delete_providers' ) );
                    }
						
                );
		add_action(
                    'wp_ajax_free_featured',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_free_featured' ) );
                    }
						
                );	
		add_action(
                    'wp_ajax_make_unfeatured',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_make_unfeatured' ) );
                    }
						
                );	
		add_action(
                    'wp_ajax_block_user',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_block_user' ) );
                    }
						
                );	
		add_action(
                    'wp_ajax_get_bank_account_info',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_get_bank_account_info' ) );
                    }
						
                );			
		add_action(
                    'wp_ajax_unblock_user',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_unblock_user' ) );
                    }
						
                );
		add_action(
                    'wp_ajax_approved_user',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_approved_user' ) );
                    }
						
                );
		add_action(
                    'wp_ajax_reject_user',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_reject_user' ) );
                    }
						
                );
		add_action(
                    'wp_ajax_get_providers_identity',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_get_providers_identity' ) );
                    }
						
                );
		add_action(
                    'wp_ajax_approve_provider_identity',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_approve_provider_identity' ) );
                    }
						
                );
		add_action(
                    'wp_ajax_unapprove_provider_identity',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_unapprove_provider_identity' ) );
                    }
						
                );
		add_action(
                    'wp_ajax_make_it_vendors',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_make_it_vendors' ) );
                    }
						
                );	
		add_action(
                    'wp_ajax_addtowallet',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_addtowallet' ) );
                    }
						
                );																	
											
				
    }
	
	/*Add to wallet*/
	public function service_finder_addtowallet(){
	global $service_finder_Tables, $wpdb;
	
	$user_id = (!empty($_POST['user_id'])) ? $_POST['user_id'] : '';
	$amount = (!empty($_POST['amount'])) ? esc_attr($_POST['amount']) : 0;
	
	service_finder_add_wallet_amount($user_id,$amount);
	
	$success = array(
			'status' => 'success',
			'suc_message' => esc_html__('Add balance to wallet successfully', 'service-finder'),
			);
	echo json_encode($success);
		
	exit(0);
	
	}
	
	/*Make all providers to vendors also*/
	public function service_finder_make_it_vendors(){
	global $service_finder_Tables, $wpdb;
		
	$providers = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->providers.' Where id > 0');
	
	if(!empty($providers)){
		foreach($providers as $provider){
			$user_id = $provider->wp_user_id;
			
			service_finder_meke_user_vendor($user_id);
	
		}
		
		$success = array(
				'status' => 'success',
				'suc_message' => esc_html__('All providers make vendor Successfully', 'service-finder'),
				);
		echo json_encode($success);
	}else{
		$error = array(
				'status' => 'error',
				'err_message' => esc_html__('No providers found', 'service-finder'),
				);
		echo json_encode($error);
	}
	
	exit(0);
	
	}
	
	/*Display providers into datatable*/
	public function service_finder_get_providers(){
		global $wpdb,$service_finder_Tables,$service_finder_options;
		$requestData= $_REQUEST;
		
		$sql = 'SELECT provider.id, provider.wp_user_id, provider.status as membershipstatus, provider.admin_moderation, provider.company_name, provider.account_blocked, provider.full_name, provider.tagline, provider.bio, provider.email, provider.mobile, provider.phone, provider.category_id, provider.email, provider.city, featured.amount, featured.days, featured.status,featured.feature_status FROM '.$service_finder_Tables->feature.' as featured RIGHT JOIN '.$service_finder_Tables->providers.' as provider on featured.provider_id = provider.wp_user_id';
		
		$totalrecords = $wpdb->get_results($sql);
		$totalrecords = count($totalrecords);
		
		if( !empty($requestData['search']['value']) ) {
			$sql .= " WHERE 1 = 1";    
			$sql .= " AND ( provider.wp_user_id LIKE '".$requestData['search']['value']."%' ";    
			$sql .= " OR provider.company_name LIKE '".$requestData['search']['value']."%' ";
			$sql .= " OR provider.full_name LIKE '".$requestData['search']['value']."%' ";
			$sql .= " OR provider.tagline LIKE '".$requestData['search']['value']."%' ";
			$sql .= " OR provider.bio LIKE '".$requestData['search']['value']."%' ";
			$sql .= " OR provider.phone LIKE '".$requestData['search']['value']."%' ";
			$sql .= " OR provider.mobile LIKE '".$requestData['search']['value']."%' ";
			$sql .= " OR provider.email LIKE '".$requestData['search']['value']."%' )";
		}
		
		$totalfiltered = $wpdb->get_results($sql);
		$totalfiltered = count($totalfiltered);

		$sql .= " LIMIT ".$requestData['start'].','.$requestData['length'];
		
		$providers = $wpdb->get_results($sql);
		
		$data = array();
		
		foreach($providers as $result){
			$nestedData=array(); 
			
			$plandata = get_user_meta($result->wp_user_id,'provider_activation_time',true);
			$role = get_user_meta($result->wp_user_id,'provider_role',true);
			if($role != ""){
			$roleNum = intval(substr($role, 8));
			}else{
			$roleNum = '';
			}
			$packagename = (!empty($service_finder_options['package'.$roleNum.'-name'])) ? $service_finder_options['package'.$roleNum.'-name'] : esc_html__('No Package','service-finder');
			
			$nestedData['providerid'] = $result->wp_user_id;
			$nestedData['delete'] = "<input type='checkbox' class='deleteProvidersRow' value='".esc_attr($result->wp_user_id)."'  />";
			$mobile = (!empty($result->mobile)) ? $result->mobile : '';
			$phone = (!empty($result->phone)) ? $result->phone : '';
			
			$contactnumber = service_finder_get_contact_info($phone,$mobile);
			$contactnumber = ($contactnumber != '') ? $contactnumber : 'N/A';
			$city = ($result->city != '') ? $result->city : 'N/A';
			$categoryname = (service_finder_getCategoryName(get_user_meta($result->wp_user_id,'primary_category',true)) != '') ? service_finder_getCategoryName(get_user_meta($result->wp_user_id,'primary_category',true)) : 'N/A';
			
			$providerinfo = '<span data-toggle="popover" data-container="body" data-placement="top" type="button" data-html="true" id="providerinfo-'.$result->id.'" data-trigger="hover"><i class="fa fa-question-circle"></i></span>';
			$providerinfo .= '<div id="popover-content-providerinfo-'.$result->id.'" class="hide pop-full">
									<ul class="sf-popoverinfo-list">
										<li><span>'.esc_html__( 'Contact Number','service-finder' ).':</span> <span>'.$contactnumber.'</span></li>
										<li><span>'.esc_html__( 'City','service-finder' ).':</span> <span>'.service_finder_get_cityname_by_slug($city).'</span></li>
										<li><span>'.esc_html__( 'Category','service-finder' ).':</span> <span>'.$categoryname.'</span></li>
									</ul>
								</div>';
			
			$nestedData['providername'] = $result->full_name.' '.$providerinfo;
			$nestedData['email'] = $result->email;
			
			$membershipdate = (!empty($plandata['time'])) ? service_finder_date_format(date('Y-m-d',$plandata['time'])) : 'N/A';
			$membershipinfo = '<span data-toggle="popover" data-container="body" data-placement="top" type="button" data-html="true" id="membershipinfo-'.$result->id.'" data-trigger="hover"><i class="fa fa-question-circle"></i></span>';
			$membershipinfo .= '<div id="popover-content-membershipinfo-'.$result->id.'" class="hide pop-full">
									<ul class="sf-popoverinfo-list">
										<li><span>'.esc_html__( 'Membership Date','service-finder' ).':</span> <span>'.$membershipdate.'</span></li>
									</ul>
								</div>';
			
			$nestedData['membership'] = esc_html($packagename).' '.$membershipinfo;
			if($result->status == 'Paid' && $result->feature_status == 'active'){
			$status = esc_html__('Featured (Paid)', 'service-finder');
			}elseif($result->status == 'Free' && $result->feature_status == 'active'){
			$status = '
<input type="checkbox" checked="checked" name="makefeatured" value="'.esc_attr($result->wp_user_id).'" id="makefeatured-'.esc_attr($result->wp_user_id).'">
'.esc_html__('Featured (By Admin)', 'service-finder');
			}else{
			$status = '<input type="checkbox" name="makefeatured" value="'.esc_attr($result->wp_user_id).'" id="makefeatured-'.esc_attr($result->wp_user_id).'">';
			}
			$nestedData['featured'] = $status;
			
			$payment_type = get_user_meta($result->wp_user_id,'payment_type',true);
			$payment_method = get_user_meta($result->wp_user_id,'payment_mode',true);
			$order_id = get_user_meta($result->wp_user_id,'order_id',true);
			
			if($payment_type == 'woocommerce' && ($payment_method == 'bacs' || $payment_method == 'cheque')){
			$wiredinvoiceid = get_user_meta($result->wp_user_id,'order_id',true);
			}elseif($payment_type == 'woocommerce' && $payment_method != 'bacs' && $payment_method != 'cheque'){
			$wiredinvoiceid = '-';
			}elseif(($payment_type == 'local' || $payment_type == "") && $payment_method == 'wired'){
			$wiredinvoiceid = get_user_meta($result->wp_user_id,'wired_invoiceid',true);
			}else{
			$wiredinvoiceid = 'N/A';
			}
			
			$currentPayType = get_user_meta($result->wp_user_id,'pay_type',true);
			$transactionid = 'N/A';
			if($currentPayType == 'single'){
			
			if($payment_type == 'woocommerce'){
			$transactionid = get_user_meta($result->wp_user_id,'order_id',true);
			}else{
			$transactionid = get_user_meta($result->wp_user_id,'txn_id',true);
			}
			
			}elseif($currentPayType == 'recurring'){
			
				$subscription_id = get_user_meta($result->wp_user_id,'subscription_id',true);
				$profileid = get_user_meta($result->wp_user_id,'recurring_profile_id',true);
	
				if($subscription_id != ""){
					$transactionid = $subscription_id;
				}elseif(!empty($profileid)){
					$transactionid = $profileid;
				}
			}
			
			$paytype = ($payment_type == 'woocommerce') ? esc_html__('Woocommerce','service-finder') : esc_html__('Local','service-finder');
			
			$paymentinfo = '<span data-toggle="popover" data-container="body" data-placement="top" type="button" data-html="true" id="paymentinfo-'.$result->id.'" data-trigger="hover"><i class="fa fa-question-circle"></i></span>';
			$paymentinfo .= '<div id="popover-content-paymentinfo-'.$result->id.'" class="hide pop-full">
									<ul class="sf-popoverinfo-list">
										<li><span>'.esc_html__( 'Transaction ID','service-finder' ).':</span> <span>'.$transactionid.'</span></li>
										<li><span>'.esc_html__( 'Payment Type','service-finder' ).':</span> <span>'.$paytype.'</span></li>
										<li><span>'.esc_html__( 'Invoice ID (Wire Transffer)','service-finder' ).':</span> <span>'.$wiredinvoiceid.'</span></li>
									</ul>
								</div>';
			
			$nestedData['paymentmethod'] = service_finder_translate_static_status_string($payment_method).' '.$paymentinfo;
			
			$manageprofilelink = add_query_arg( array('manageaccountby' => 'admin','manageproviderid' => esc_attr($result->wp_user_id)), service_finder_get_url_by_shortcode('[service_finder_my_account') );
			$actionsbtns = '';
			if($result->admin_moderation == 'approved'){
				if($result->account_blocked == 'yes'){
				$actionsbtns .= '<a href="javascript:;" data-id="'.esc_attr($result->wp_user_id).'" class="unblockaccount btn btn-status btn-xs blue"><i class="fa fa-unlock"></i> '.esc_html__('UnBlock', 'service-finder').'</a>';
				
				if($payment_method == 'wired'){
				$actionsbtns .= '<a href="javascript:;" data-id="'.esc_attr($result->wp_user_id).'" class="unblockaccount btn btn-status btn-xs green"><i class="fa fa-unlock"></i> '.esc_html__('Approve After Wired Transfer', 'service-finder').'</a>';				
				}
				}else{
				$actionsbtns .= '<a href="javascript:;" data-id="'.esc_attr($result->wp_user_id).'" class="blockaccount btn btn-status btn-xs yellow"><i class="fa fa-ban"></i> '.esc_html__('Block', 'service-finder').'</a>';
				}
			}elseif($result->admin_moderation == 'pending'){
				$actionsbtns .= '<a href="javascript:;" data-id="'.esc_attr($result->wp_user_id).'" class="approveprovider btn btn-status btn-xs green"><i class="fa fa-check"></i> '.esc_html__('Approve', 'service-finder').'</a> <a href="javascript:;" data-id="'.esc_attr($result->wp_user_id).'" class="rejectprovider btn btn-status btn-xs red"><i class="fa fa-ban"></i> '.esc_html__('Reject', 'service-finder').'</a>';
			}elseif($result->admin_moderation == 'rejected'){
				$actionsbtns .= '<span class="reject">Rejected</span> <a href="javascript:;" data-id="'.esc_attr($result->wp_user_id).'" class="approveprovider btn btn-status btn-xs green"><i class="fa fa-check"></i> '.esc_html__('Re-Approve', 'service-finder').'</a>';
			}
			
			$nestedData['status'] = $actionsbtns;
			
			if(service_finder_check_wallet_system()){
				$walletamount = service_finder_get_wallet_amount($result->wp_user_id);
				$walletbtn = '<li>';
				$walletbtn .= ' <a href="javascript:;" data-id="'.esc_attr($result->wp_user_id).'" class="addtowallet"><i class="fa fa-money"></i> '.esc_html__('Add Balance to Wallet', 'service-finder').'<span class="sf-wallet-amount">'.service_finder_money_format($walletamount).'</span></a>';
				$walletbtn .= '</li>';
			}else{
				$walletbtn = '<li><a href="javascript:;" data-toggle="tooltip" title="'.esc_html__( 'Please activate wallet system from theme options to enable this.','service-finder' ).'" class="disable-btn"><i class="fa fa-money"></i> '.esc_html__('Add Balance to Wallet', 'service-finder').'</a></li>';
			}
			
			$actions = '<div class="dropdown action-dropdown dropdown-left">
						  <button class="action-button gray dropdown-toggle" type="button" data-toggle="dropdown"><i class="fa fa-ellipsis-v"></i></button>
						  <ul class="dropdown-menu">
							<li><a href="'.esc_url($manageprofilelink).'" target="_blank" ><i class="fa fa-briefcase"></i> '.esc_html__('Manage Profile', 'service-finder').'</a></li>
							<li><a href="'.esc_url(service_finder_get_author_url($result->wp_user_id)).'" target="_blank"><i class="fa fa-eye"></i> '.esc_html__( 'View Profile','service-finder' ).'</a></li>
							<li><a href="javascript:;" data-id="'.esc_attr($result->wp_user_id).'" class="viewbankinfo"><i class="fa fa-eye"></i> '.esc_html__( 'View Bank Info','service-finder' ).'</a></li>
							'.$wooorder.'
							'.$walletbtn.'
							<li><a href="javascript:;"><i class="fa fa-close"></i> '.esc_html__( 'Close','service-finder' ).'</a></li>
						  </ul>
						</div>';
			
			$nestedData['actions'] = $actions;
			
			$data[] = $nestedData;
		}
		
		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),
					"recordsTotal"    => intval( $totalrecords ),
					"recordsFiltered" => intval( $totalfiltered ),
					"data"            => $data
					);
		
		echo json_encode($json_data);
	
		exit;
	}
	
	/*Display providers identity into datatable*/
	public function service_finder_get_providers_identity(){
		global $wpdb,$service_finder_Tables,$service_finder_options;
		$requestData= $_REQUEST;
		
		$sql = 'SELECT provider.id, provider.identity, provider.wp_user_id, provider.status as membershipstatus, provider.admin_moderation, provider.company_name, provider.account_blocked, provider.full_name, provider.tagline, provider.bio, provider.email, provider.mobile, provider.phone, provider.category_id, provider.email, provider.city, featured.amount, featured.days, featured.status,featured.feature_status FROM '.$service_finder_Tables->feature.' as featured RIGHT JOIN '.$service_finder_Tables->providers.' as provider on featured.provider_id = provider.wp_user_id';
		
		$totalrecords = $wpdb->get_results($sql);
		$totalrecords = count($totalrecords);
		
		if( !empty($requestData['search']['value']) ) {
			$sql .= " WHERE 1 = 1";    
			$sql .= " AND ( provider.wp_user_id LIKE '".$requestData['search']['value']."%' ";    
			$sql .= " OR provider.company_name LIKE '".$requestData['search']['value']."%' ";
			$sql .= " OR provider.full_name LIKE '".$requestData['search']['value']."%' ";
			$sql .= " OR provider.tagline LIKE '".$requestData['search']['value']."%' ";
			$sql .= " OR provider.bio LIKE '".$requestData['search']['value']."%' ";
			$sql .= " OR provider.phone LIKE '".$requestData['search']['value']."%' ";
			$sql .= " OR provider.mobile LIKE '".$requestData['search']['value']."%' ";
			$sql .= " OR provider.email LIKE '".$requestData['search']['value']."%' )";
		}
		
		$totalfiltered = $wpdb->get_results($sql);
		$totalfiltered = count($totalfiltered);

		$sql .= " LIMIT ".$requestData['start'].','.$requestData['length'];
		
		$providers = $wpdb->get_results($sql);
		
		$data = array();
		
		foreach($providers as $result){
			$nestedData=array(); 
			
			$plandata = get_user_meta($result->wp_user_id,'provider_activation_time',true);
			$role = (!empty($plandata['role'])) ? esc_html($plandata['role']) : '';
			$roleNum = intval(substr($role, 8));
			$packagename = (!empty($service_finder_options['package'.$roleNum.'-name'])) ? $service_finder_options['package'.$roleNum.'-name'] : '';
			
			$nestedData['identityid'] = $result->id;
			$nestedData['providername'] = $result->full_name;
			$nestedData['phone'] = $result->phone;
			$nestedData['email'] = $result->email;
			
			$attachmentIDs = service_finder_get_identity($result->wp_user_id);
			
			$identityfile = '';
			if(!empty($attachmentIDs)){
				foreach($attachmentIDs as $attachmentID){
				$identityfile .= '<a href="'.get_permalink( $attachmentID->attachmentid ).'?attachment_id='. $attachmentID->attachmentid.'&download_file=1"><i class="fa fa-download"></i> '.esc_html__('View/Download').'</a><br/>';
				}
			}else{
				$identityfile = esc_html__('No identity available', 'service-finder');
			}
			
			$nestedData['identity'] = $identityfile;
			
			$decline_reason = get_user_meta($result->wp_user_id,'identity_decline_reason',true);
			$declinereason = '';
			if($result->identity == 'unapproved' && $decline_reason != ''){
			$declinereason = '<span data-toggle="popover" data-container="body" data-placement="top" type="button" data-html="true" id="declinereason-'.$result->wp_user_id.'" data-trigger="hover"><i class="fa fa-question-circle"></i></span>';
			$declinereason .= '<div id="popover-content-declinereason-'.$result->wp_user_id.'" class="hide pop-full">
									<ul class="sf-popoverinfo-list">
										<li><span>'.esc_html__( 'Decline Reason','service-finder' ).':</span> <span>'.$decline_reason.'</span></li>
									</ul>
								</div>';
			}					
			
			if($result->identity == 'approved'){
			$nestedData['status'] = '<span class="aon-green identity-status">'.esc_html__('Approved', 'service-finder').'</span>';
			}elseif($result->identity == 'unapproved'){
			$nestedData['status'] = '<span class="aon-red identity-status">'.esc_html__('Declined', 'service-finder').'</span> '.$declinereason;
			}else{
				if(!empty($attachmentIDs)){
					$nestedData['status'] = '<span class="aon-yellow identity-status">'.esc_html__('In Process','service-finder').'</span>';
				}else
				{
					$nestedData['status'] = '<span class="aon-orange identity-status">'.esc_html__('Pending','service-finder').'</span>';
				}
			}
			
			$actions = '<div class="dropdown action-dropdown dropdown-left">
						  <button class="action-button gray dropdown-toggle" type="button" data-toggle="dropdown"><i class="fa fa-ellipsis-v"></i></button>
						  <ul class="dropdown-menu">
							<li><a href="javascript:;" data-id="'.esc_attr($result->wp_user_id).'" class="approveidentity"><i class="fa fa-check"></i> '.esc_html__('Approve', 'service-finder').'</a></li>
							<li><a href="javascript:;" data-id="'.esc_attr($result->wp_user_id).'" class="unapproveidentity"><i class="fa fa-ban"></i> '.esc_html__('Reject', 'service-finder').'</a></li>
							<li><a href="javascript:;"><i class="fa fa-close"></i> '.esc_html__( 'Close','service-finder' ).'</a></li>
						  </ul>
						</div>';
			
			$nestedData['actions'] = $actions;
			
			$data[] = $nestedData;
		}
		
		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),
					"recordsTotal"    => intval( $totalrecords ),
					"recordsFiltered" => intval( $totalfiltered ),
					"data"            => $data
					);
		
		echo json_encode($json_data);
	
		exit;
	}
	
	/*Delete Providers*/
	public function service_finder_delete_providers(){
	global $wpdb, $service_finder_Tables;
			$data_ids = $_REQUEST['data_ids'];
			$data_id_array = explode(",", $data_ids); 
			if(!empty($data_id_array)) {
				foreach($data_id_array as $id) {
					wp_delete_user( $id );
					$sql = $wpdb->prepare("DELETE FROM ".$service_finder_Tables->providers." WHERE wp_user_id = %d",$id);
					$query=$wpdb->query($sql);
				}
			}
	exit(0);		
	}
	
	/*Make Featured by Admin*/
	public function service_finder_free_featured(){
	global $wpdb, $service_finder_Tables;
	
	$proid = (isset($_POST['proid'])) ? esc_html($_POST['proid']) : '';
	$days = (isset($_POST['days'])) ? esc_html($_POST['days']) : '';
	
	$wpdb->query($wpdb->prepare('DELETE FROM '.$service_finder_Tables->feature.' WHERE `provider_id` = %d',$proid));
	
	$date = date('Y-m-d H:i:s');
	$data = array(
			'provider_id' => $proid,
			'days' => $days,
			'status' => 'Free',
			'feature_status' => 'active',
			'date' => $date,
			);

	$wpdb->insert($service_finder_Tables->feature,wp_unslash($data));
	
	$feature_id = $wpdb->insert_id;
	
	$data = array(
			'featured' => 1,
			);
	
	$where = array(
			'wp_user_id' => $proid,
			);
	$wpdb->update($service_finder_Tables->providers,wp_unslash($data),$where);

	if ( ! $feature_id ) {
		$errmsg = 'Provider Couldn&#8217;t make featured... Please try again';
		$error = array(
				'status' => 'error',
				'err_message' => sprintf( esc_html__('%s', 'service-finder'), $errmsg )
				);
		echo json_encode($error);
	}else{
		$success = array(
				'status' => 'success',
				'suc_message' => esc_html__('Provider has been Featured Successfully', 'service-finder'),
				);
		echo json_encode($success);
	}
	
	exit(0);		
	}
	
	/*Block User by Admin*/
	public function service_finder_block_user(){
	global $wpdb, $service_finder_Tables;
	
	$wpdb->query($wpdb->prepare('UPDATE '.$service_finder_Tables->providers.' SET `account_blocked` = "yes" WHERE `wp_user_id` = %d',$_POST['uid']));
	
	$email = service_finder_getProviderEmail($_POST['uid']);
	$message = esc_html__('Dear Provider,', 'service-finder');
	$message .= esc_html__('Your account has been blocked by following reason:', 'service-finder');
	$message .= '%COMMENT%';
	
	$tokens = array('%COMMENT%');
	$replacements = array($_POST['comment']);
	$msg_body = str_replace($tokens,$replacements,$message);
	$msg_subject = esc_html__('Account Blocked', 'service-finder');
	if(service_finder_wpmailer($email,$msg_subject,$msg_body)) {

		$success = array(
				'status' => 'success',
				'suc_message' => esc_html__('User has been Blocked Successfully', 'service-finder'),
				);
		echo json_encode($success);
	}else{
		$success = array(
				'status' => 'error',
				'err_message' => esc_html__('Message could not be sent.', 'service-finder'),
				);
		echo json_encode($success);
	}
	
	exit(0);		
	}
	
	/*Get Bank account info*/
	public function service_finder_get_bank_account_info(){
	
	$userId = (!empty($_POST['uid'])) ? esc_html($_POST['uid']) : '';
	
	$bank_account_holder_name = get_user_meta($userId,'bank_account_holder_name',true);
	$bank_account_number = get_user_meta($userId,'bank_account_number',true);
	$swift_code = get_user_meta($userId,'swift_code',true);
	$bank_name = get_user_meta($userId,'bank_name',true);
	$bank_branch_city = get_user_meta($userId,'bank_branch_city',true);
	$bank_branch_country = get_user_meta($userId,'bank_branch_country',true);
	
	if($bank_account_holder_name == "" && $bank_account_holder_name == "" && $bank_account_holder_name == "" && $bank_account_holder_name == "" && $bank_account_holder_name == "" && $bank_account_holder_name == ""){
		$flag = 0;
	}else{
		$flag = 1;
	}
	
	$success = array(
				'status' => 'success',
				'flag' => $flag,
				'bank_account_holder_name' => esc_html($bank_account_holder_name),
				'bank_account_number' => esc_html($bank_account_number),
				'swift_code' => esc_html($swift_code),
				'bank_name' => esc_html($bank_name),
				'bank_branch_city' => esc_html($bank_branch_city),
				'bank_branch_country' => esc_html($bank_branch_country),
				);
	echo json_encode($success);

	exit(0);		
	}
	
	/*Un-Block User by Admin*/
	public function service_finder_unblock_user(){
	global $wpdb, $service_finder_Tables;
	
	$data = array(
			'account_blocked' => 'no',
			'status' => 'active',
			);
	
	$where = array(
			'wp_user_id' => $_POST['uid'],
			);
	$wpdb->update($service_finder_Tables->providers,wp_unslash($data),$where);
	
	
	$email = service_finder_getProviderEmail($_POST['uid']);
	$message = esc_html__('Dear Provider,', 'service-finder');
	$message .= esc_html__('Your account has been UnBlocked successfully.', 'service-finder');
	
	$msg_body = $message;
	$msg_subject =  esc_html__('Account UnBlocked', 'service-finder');
	
	service_finder_wpmailer($email,$msg_subject,$msg_body);

	$success = array(
			'status' => 'success',
			'suc_message' => esc_html__('User has been UnBlocked Successfully', 'service-finder'),
			);
	echo json_encode($success);

	exit(0);		
	}
	
	/*Approved User by Admin*/
	public function service_finder_approved_user(){
	global $wpdb, $service_finder_Tables, $service_finder_options;
	$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('Provider', 'service-finder');	
	
	$wpdb->query($wpdb->prepare('UPDATE '.$service_finder_Tables->providers.' SET `admin_moderation` = "approved" WHERE `wp_user_id` = %d',$_POST['uid']));
	
	$email = service_finder_getProviderEmail($_POST['uid']);
	
	if(!empty($service_finder_options['send-to-provider-account-approval'])){
		$message = $service_finder_options['send-to-provider-account-approval'];
	}else{
		$message = 'Dear '.esc_html($providerreplacestring).',
		Congratulations! Your account has been approved.';
	}
	
	$tokens = array('%PROVIDERNAME%');
	$replacements = array(service_finder_getProviderName($_POST['uid']));
	$msg_body = str_replace($tokens,$replacements,$message);
	
	if(!empty($service_finder_options['provider-account-approval-subject'])){
		$msg_subject = $service_finder_options['provider-account-approval-subject'];
	}else{
		$msg_subject = 'User account approved';
	}
	
	service_finder_wpmailer($email,$msg_subject,$msg_body);
	
	$success = array(
			'status' => 'success',
			'suc_message' => esc_html__('User has been Approved Successfully', 'service-finder'),
			);
	echo json_encode($success);
	
	exit(0);		
	}
	
	/*Approved provider identity*/
	public function service_finder_approve_provider_identity(){
	global $wpdb, $service_finder_Tables, $service_finder_options;
	
	$providerid = (isset($_POST['providerid'])) ? esc_attr($_POST['providerid']) : '';
	
	$wpdb->query($wpdb->prepare('UPDATE '.$service_finder_Tables->providers.' SET `identity` = "approved" WHERE `wp_user_id` = %d',$providerid));
	
	update_user_meta( $providerid, 'identity', 'approved' );
	
	delete_user_meta($providerid,'identity_decline_reason');
	
	$email = service_finder_getProviderEmail($providerid);
	
	$messagetmp = (!empty($service_finder_options['identity-approve-mail'])) ? $service_finder_options['identity-approve-mail'] : '';
	if($messagetmp != ""){
	$message = $messagetmp;
	}else{
	$message = 'Dear %PROVIDERNAME%,
	Congratulations! Your identity has been approved.';
	}
	
	$tokens = array('%PROVIDERNAME%');
	$replacements = array(service_finder_getProviderName($providerid));
	$msg_body = str_replace($tokens,$replacements,$message);
	
	$noticedata = array(
			'provider_id' => $providerid,
			'target_id' => $providerid, 
			'topic' => 'Identity Approved',
			'title' => esc_html__('Identity Approved', 'service-finder'),
			'notice' => esc_html__('Your identity has been approved.', 'service-finder'),
			);
	service_finder_add_notices($noticedata);
	
	if($service_finder_options['identity-approve-mail-subject'] != ""){
		$msg_subject = $service_finder_options['identity-approve-mail-subject'];
	}else{
		$msg_subject = esc_html__('Identity check approved', 'service-finder');
	}
	
	service_finder_wpmailer($email,$msg_subject,$msg_body);
	
	$success = array(
			'status' => 'success',
			'suc_message' => esc_html__('Provider Identity Approved Successfully', 'service-finder'),
			);
	echo json_encode($success);
	
	exit(0);		
	}
	
	/*UnApproved provider identity*/
	public function service_finder_unapprove_provider_identity(){
	global $wpdb, $service_finder_Tables, $service_finder_options;
	
	$providerid = (isset($_POST['providerid'])) ? esc_attr($_POST['providerid']) : '';
	$reason = (isset($_POST['reason'])) ? sanitize_text_field($_POST['reason']) : '';
	
	$wpdb->query($wpdb->prepare('UPDATE '.$service_finder_Tables->providers.' SET `identity` = "unapproved" WHERE `wp_user_id` = %d',$providerid));
	$wpdb->query($wpdb->prepare('DELETE FROM `'.$service_finder_Tables->attachments.'` WHERE `type` = "identity" AND `wp_user_id` = %d',$providerid));
	
	update_user_meta( $providerid, 'identity', 'unapproved' );
	
	update_user_meta($providerid,'identity_decline_reason',$reason);
	
	$email = service_finder_getProviderEmail($providerid);
	$messagetmp = (!empty($service_finder_options['identity-unapprove-mail'])) ? $service_finder_options['identity-unapprove-mail'] : '';
	if($messagetmp != ""){
	$message = $messagetmp;
	}else{
	$message = 'Dear %PROVIDERNAME%,
	Your identity has been unapproved.';
	}
	
	$tokens = array('%PROVIDERNAME%');
	$replacements = array(service_finder_getProviderName($providerid));
	$msg_body = str_replace($tokens,$replacements,$message);
	
	if($service_finder_options['identity-unapprove-mail-subject'] != ""){
		$msg_subject = $service_finder_options['identity-unapprove-mail-subject'];
	}else{
		$msg_subject = esc_html__('Identity check unapproved', 'service-finder');
	}
	
	$noticedata = array(
			'provider_id' => $providerid,
			'target_id' => $providerid, 
			'topic' => 'Identity Declined',
			'title' => esc_html__('Identity Declined', 'service-finder'),
			'notice' => esc_html__('Your identity has been declined.', 'service-finder'),
			);
	service_finder_add_notices($noticedata);
	
	if($service_finder_options['identity-unapprove-mail-subject'] != ""){
		$msg_subject = $service_finder_options['identity-unapprove-mail-subject'];
	}else{
		$msg_subject = esc_html__('Identity check unapproved', 'service-finder');
	}
	
	service_finder_wpmailer($email,$msg_subject,$msg_body);
	
	$success = array(
			'status' => 'success',
			'suc_message' => esc_html__('Provider Identity Un-Approved Successfully', 'service-finder'),
			);
	echo json_encode($success);
	
	exit(0);		
	}
	
	/*Approved User by Admin*/
	public function service_finder_reject_user(){
	global $wpdb, $service_finder_Tables;
	
	$wpdb->query($wpdb->prepare('UPDATE '.$service_finder_Tables->providers.' SET `admin_moderation` = "rejected" WHERE `wp_user_id` = %d',$_POST['uid']));
	
	$email = service_finder_getProviderEmail($_POST['uid']);
	$message = 'Dear Provider,
	Your account has been rejected by following reason: %COMMENT%';
	
	$tokens = array('%COMMENT%');
	$replacements = array($_POST['comment']);
	$msg_body = str_replace($tokens,$replacements,$message);
	$msg_subject = 'User account rejected';
	
	service_finder_wpmailer($email,$msg_subject,$msg_body);

	$success = array(
			'status' => 'success',
			'suc_message' => esc_html__('User has been Rejected Successfully', 'service-finder'),
			);
	echo json_encode($success);	
	
	exit(0);		
	}
	
	/*Make Un Featured by Admin*/
	public function service_finder_make_unfeatured(){
	global $wpdb, $service_finder_Tables;
	
	$proid = (isset($_POST['proid'])) ? esc_html($_POST['proid']) : '';
	
	$res = $wpdb->query($wpdb->prepare('DELETE FROM '.$service_finder_Tables->feature.' WHERE `provider_id` = %d',$proid));
	
	$data = array(
			'featured' => 0,
			);
	
	$where = array(
			'wp_user_id' => $proid,
			);
	$wpdb->update($service_finder_Tables->providers,wp_unslash($data),$where);

	if ( ! $res ) {
		$errmsg = 'Provider Couldn&#8217;t make unfeatured... Please try again';
		$error = array(
				'status' => 'error',
				'err_message' => sprintf( esc_html__('%s', 'service-finder'), $errmsg )
				);
		echo json_encode($error);
	}else{
		$success = array(
				'status' => 'success',
				'suc_message' => esc_html__('Provider has been UnFeatured Successfully', 'service-finder'),
				);
		echo json_encode($success);
	}
	
	exit(0);		
	}
	
}