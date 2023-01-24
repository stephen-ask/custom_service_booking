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
 * Class SERVICE_FINDER_sedateQuotations
 */
class SERVICE_FINDER_sedateQuotations extends SERVICE_FINDER_sedateManager{

	
	/*Initial Function*/
	public function service_finder_index()
    {
        
		/*Rander providers template*/
		$this->service_finder_render( 'index','quotations' );
		
		/*Action for wp ajax call*/
		$this->service_finder_registerWpActions();
		
    }
	
	/*Actions for wp ajax call*/
	protected function service_finder_registerWpActions() {
       $_this = $this;
	   add_action(
                    'wp_ajax_get_admin_quotations',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_get_admin_quotations' ) );
                    }
						
                );
		add_action(
                    'wp_ajax_approvemail',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_approvemail' ) );
                    }
						
                );	
		add_action(
                    'wp_ajax_delete_quote',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_delete_quote' ) );
                    }
						
                );	
		
    }
	
	public function service_finder_get_admin_quotations(){
		global $wpdb, $service_finder_Tables;
		$requestData= $_REQUEST;

		$quotations = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->quotations);

		$data = array();
		
		foreach($quotations as $result){
			$nestedData=array(); 
			$userLink = service_finder_get_author_url($result->provider_id);
			$nestedData['quoteid'] = $result->id;
			$nestedData['delete'] = "<input type='checkbox' class='deleteQuoteRow' value='".esc_attr($result->id)."' />";
			$nestedData['providername'] = '<a href="'.esc_url($userLink).'" target="_blank">'.service_finder_getProviderName($result->provider_id).'</a>';
			$nestedData['customername'] = $result->name;
			$nestedData['date'] = service_finder_date_format($result->date);
			$nestedData['email'] = $result->email;
			$nestedData['phone'] = $result->phone;
			
			$attachfile = '';
			$attachments = (!empty($result->attachments)) ? unserialize($result->attachments) : '';
			if(!empty($attachments)){
				foreach($attachments as $attachmentid){
				$attachfile .= '<a href="'.get_permalink( $attachmentid ).'?attachment_id='. $attachmentid.'&download_file=1"><i class="fa fa-download"></i> '.esc_html__('View/Download').'</a><br/>';
				}
			}else
			{
				$attachfile = '-';
			}
			
			$nestedData['attachments'] = $attachfile;
			
			if(class_exists('aone_messaging')){
			$manageprofilelink = add_query_arg( array('manageaccountby' => 'admin','manageproviderid' => esc_attr($result->provider_id),'tabname' => 'quotation'), service_finder_get_url_by_shortcode('[service_finder_my_account') );
			$viewconversation = '<a target="_blank" href="'.esc_url($manageprofilelink).'">'.esc_html__('View Conversation', 'service-finder').'</a>';
			}else
			{
			$viewconversation = '';
			}
			
			$nestedData['message'] = $result->message.' '.$viewconversation;
			
			if($result->status == "hold"){
				$nestedData['status'] = '<a href="javascript:;" class="btn btn-success btn-xs" data-id="'.esc_attr($result->id).'" data-providerid="'.esc_attr($result->provider_id).'" id="approvemail">'.esc_html__('Approve', 'service-finder').'</a>';
			}else{
				$nestedData['status'] = esc_html__('Mail Sent', 'service-finder');
			}
			
			$data[] = $nestedData;
		}
		
		$json_data = array( "data" => $data );
	
		echo json_encode($json_data);
	
		exit;
	}
	
	/*Approve Featured Request*/
	public function service_finder_approvemail(){
	global $wpdb, $service_finder_Tables, $service_finder_options;
	$qid = isset($_POST['qid']) ? esc_html($_POST['qid']) : '';
	$provider_id = isset($_POST['pid']) ? esc_html($_POST['pid']) : '';
	
	$customerinfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->quotations.' WHERE id = %d',$qid));
	
	if(!empty($customerinfo)){
	
	$getProvider = new SERVICE_FINDER_searchProviders();
	$providerInfo = $getProvider->service_finder_getProviderInfo(esc_attr($provider_id));
	
	if($service_finder_options['quote-to-provider-subject'] != ""){
		$msg_subject = $service_finder_options['quote-to-provider-subject'];
	}else{
		$msg_subject = esc_html__('Request a Quotation', 'service-finder');
	}
	
	if(!empty($service_finder_options['quote-to-provider'])){
		$message = $service_finder_options['quote-to-provider'];
	}else{
		$message = 'Requesting for Quotation

		Customer Name: %CUSTOMERNAME%
		
		Email: %EMAIL%
		
		Phone: %PHONE%
		
		Description: %DESCRIPTION%';
	}
	
	$tokens = array('%PROVIDERNAME%','%PROVIDEREMAIL%','%CUSTOMERNAME%','%EMAIL%','%PHONE%','%DESCRIPTION%');
	$replacements = array(service_finder_get_providername_with_link($provider_id),'<a href="mailto:'.$providerInfo->email.'">'.$providerInfo->email.'</a>',$customerinfo->name,$customerinfo->email,$customerinfo->phone,$customerinfo->message);
	$msg_body = str_replace($tokens,$replacements,$message);
	
	$relatedproviders = service_finder_quote_related_providers($qid);
		
	$provideremails[] = $providerInfo->email;
	
	if(function_exists('service_finder_add_notices')) {
		$noticedata = array(
				'provider_id' => $provider_id,
				'target_id' => $qid, 
				'topic' => 'Get Quotation',
				'title' => esc_html__('Get Quotation', 'service-finder'),
				'notice' => sprintf(esc_html__('New quotation has arrived from %s', 'service-finder'),$customerinfo->name)
				);
		service_finder_add_notices($noticedata);
	
	}
			
	if(!empty($relatedproviders)){
		foreach($relatedproviders as $relatedprovider){
			$provideremails[] = service_finder_getProviderEmail($relatedprovider->related_provider_id);
			
			if(function_exists('service_finder_add_notices')) {
				$noticedata = array(
						'provider_id' => $relatedprovider,
						'target_id' => $quoteid, 
						'topic' => 'Get Quotation',
						'title' => esc_html__('Get Quotation', 'service-finder'),
						'notice' => sprintf(esc_html__('New quotation has arrived from %s', 'service-finder'),$customerinfo->name)
						);
				service_finder_add_notices($noticedata);
			
			}
		}
	}
	
	if(!empty($provideremails)){
		$provideremails = implode(',',$provideremails);
	}else{
		$provideremails = $providerInfo->email;
	}

	if(service_finder_wpmailer($provideremails,$msg_subject,$msg_body)) {
	
		$res = $wpdb->query($wpdb->prepare('UPDATE '.$service_finder_Tables->quotations.' SET `status` = "approved" WHERE `id` = %d',$qid));
		
		$success = array(
				'status' => 'success',
				'suc_message' => esc_html__('Approved and send mail Successfully', 'service-finder'),
				);
		echo json_encode($success);
	}else{
		$error = array(
				'status' => 'error',
				'err_message' => esc_html__('Couldn&#8217;t approved', 'service-finder')
				);
		echo json_encode($error);
	}
	
	}
	
	exit(0);		
	}
	
	/*Delete Quote*/
	public function service_finder_delete_quote(){
	global $wpdb, $service_finder_Tables;
			$data_ids = $_REQUEST['data_ids'];
			$data_id_array = explode(",", $data_ids); 
			if(!empty($data_id_array)) {
				foreach($data_id_array as $id) {
					$sql = $wpdb->prepare("DELETE FROM ".$service_finder_Tables->quotations." WHERE id = %d",$id);
					$query=$wpdb->query($sql);
					$sql = $wpdb->prepare("DELETE FROM ".$service_finder_Tables->quoteto_related_providers." WHERE quote_id = %d",$id);
					$query=$wpdb->query($sql);
				}
			}
	wp_send_json_success();
	exit;
	}
	
}