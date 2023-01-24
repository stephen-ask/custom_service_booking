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

class SERVICE_FINDER_GetQuote{

	/*Get Saved Quotations into datatable*/
	public function service_finder_get_quotations($arg){
		global $wpdb, $service_finder_Tables, $current_user;
		$requestData= $_REQUEST;
		$columns = array( 
			0 =>'id', 
			1 =>'date', 
			2 =>'name', 
			3 =>'email', 
			4 =>'phone', 
		);
		
		$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';
		
		// getting total number records without any search
		$sql = $wpdb->prepare("SELECT quote.id as quoteid,quote.*,relatedproviders.* FROM ".$service_finder_Tables->quotations." as quote LEFT JOIN ".$service_finder_Tables->quoteto_related_providers." as relatedproviders ON quote.id = relatedproviders.quote_id WHERE quote.status = 'approved' AND (quote.provider_id = %d OR relatedproviders.related_provider_id = %d) GROUP BY quote.id",$user_id,$user_id);
		$query=$wpdb->get_results($sql);
		$totalData = count($query);
		$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		$sql = "SELECT quote.id as quoteid,quote.*,relatedproviders.* FROM ".$service_finder_Tables->quotations." as quote LEFT JOIN ".$service_finder_Tables->quoteto_related_providers." as relatedproviders ON quote.id = relatedproviders.quote_id WHERE quote.status = 'approved' AND (quote.provider_id = ".$user_id." OR relatedproviders.related_provider_id = ".$user_id.")";
		if( !empty($requestData['search']['value']) ) { 
			$sql.=" AND ( quote.`name` LIKE '".$requestData['search']['value']."%' ";    
			$sql.=" OR quote.`email` LIKE '".$requestData['search']['value']."%'";    
			$sql.=" OR quote.`phone` LIKE '".$requestData['search']['value']."%'";    
			$sql.=" OR quote.`message` LIKE '".$requestData['search']['value']."%'";    
			$sql.=" OR quote.`date` LIKE '".$requestData['search']['value']."%' )";    
		}
		$query=$wpdb->get_results($sql);
		$totalFiltered = count($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
		$sql.=" GROUP BY quote.id ORDER BY quote.". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		$query=$wpdb->get_results($sql);
		$data = array();
		
		foreach($query as $result){
			$nestedData=array();
			
			if($result->hired == 'yes' && $result->assignto == $user_id){
				$hiringstatus = esc_html__('Hired', 'service-finder');
			}elseif($result->hired == 'yes' && $result->assignto != $user_id){
				$hiringstatus = esc_html__('Not Hired', 'service-finder');
			}elseif($result->hired == ''){
				$hiringstatus = esc_html__('Pending', 'service-finder');
			}
		
			$nestedData[] = '#'.$result->quoteid;
			$nestedData[] = service_finder_date_format($result->date);
			$nestedData[] = $result->name;
			$nestedData[] = $result->email;
			$nestedData[] = $result->phone;
			$nestedData[] = $hiringstatus;
			
			$sendmessagebtn = '';
			if(class_exists('aone_messaging')){
				$args = array(
							'view' => 'popup',
							'type' => 'quote',
							'targetid' => $result->quoteid,
							'fromid' => $user_id,
							'toid' => $result->customer_id,
						);
				$aonemsg = new aone_msg_core();
				$totalunread = $aonemsg->get_total_unread_count($user_id,$args);
				
				$userCap = array();
				$userCap = service_finder_get_capability($user_id);
				
				if(!empty($userCap)){
				if(in_array('message-system',$userCap)){
				$sendmessagebtn = '<button type="button" class="btn btn-custom btn-xs singlechatpopup" data-options="'.esc_attr(wp_json_encode( $args )).'" title="'.esc_html__('Send Message', 'service-finder').' ('.esc_html($totalunread).')'.'"><i class="fa fa-comments"></i> <span class="sf-total-msgnumber">'.esc_html($totalunread).'</span></button>';
				}
				}
			}
			
			if($result->reply != "" || $result->quote_price > 0 || $result->related_reply != "" || $result->related_quote_price > 0){
			if($result->hired == 'yes'){
			$nestedData[] = '<button title="'.esc_html__('View Quotation', 'service-finder').'" data-quoteid="'.esc_attr($result->quoteid).'" class="btn btn-primary btn-xs viewquotation" type="button"><i class="fa fa-eye"></i></button> '.$sendmessagebtn;
			}else{
			$nestedData[] = '<button title="'.esc_html__('Edit Quotation', 'service-finder').'" data-quoteid="'.esc_attr($result->quoteid).'" data-userid="'.esc_attr($user_id).'" class="btn btn-primary btn-xs quotereply" type="button"><i class="fa fa-pencil"></i></button> <button title="'.esc_html__('View Quotation', 'service-finder').'" data-quoteid="'.esc_attr($result->quoteid).'" class="btn btn-primary btn-xs viewquotation" type="button"><i class="fa fa-eye"></i></button> '.$sendmessagebtn;
			}
			
			}else{
			$nestedData[] = '<button title="'.esc_html__('Send Quotation', 'service-finder').'" data-quoteid="'.esc_attr($result->quoteid).'" data-userid="'.esc_attr($user_id).'" class="btn btn-primary btn-xs quotereply" type="button"><i class="fa fa-paper-plane-o"></i></button> <button title="'.esc_html__('View Quotation', 'service-finder').'" data-quoteid="'.esc_attr($result->quoteid).'" class="btn btn-primary btn-xs viewquotation" type="button"><i class="fa fa-eye"></i></button> '.$sendmessagebtn;
			}
			
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
	
	/*Get Saved Quotations into datatable*/
	public function service_finder_get_customer_quotations($arg){
		global $wpdb, $service_finder_Tables;
		$requestData= $_REQUEST;
		$columns = array( 
			0 =>'id', 
			1 =>'date', 
			2 =>'name', 
			3 =>'email', 
			4 =>'phone', 
		);
		
		$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';
		
		// getting total number records without any search
		$sql = $wpdb->prepare("SELECT * FROM ".$service_finder_Tables->quotations." WHERE status = 'approved' AND customer_id = %d",$user_id);
		$query=$wpdb->get_results($sql);
		$totalData = count($query);
		$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		$sql = "SELECT * FROM ".$service_finder_Tables->quotations." WHERE status = 'approved' AND customer_id = ".$user_id;
		if( !empty($requestData['search']['value']) ) { 
			$sql.=" AND ( quote.`name` LIKE '".$requestData['search']['value']."%' ";    
			$sql.=" OR quote.`email` LIKE '".$requestData['search']['value']."%'";    
			$sql.=" OR quote.`phone` LIKE '".$requestData['search']['value']."%'";    
			$sql.=" OR quote.`message` LIKE '".$requestData['search']['value']."%'";    
			$sql.=" OR quote.`date` LIKE '".$requestData['search']['value']."%' )";    
		}
		$query=$wpdb->get_results($sql);
		$totalFiltered = count($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		$query=$wpdb->get_results($sql);
		$data = array();
		
		foreach($query as $result){
			$nestedData=array(); 
			
			if($result->hired == 'yes'){
				$hiringstatus = esc_html__('Hired', 'service-finder');
			}elseif($result->hired == ''){
				$hiringstatus = esc_html__('Not assign yet', 'service-finder');
			}
			
			if($result->hired == 'yes'){
				if($result->assignto > 0){
				$assignto = service_finder_get_providername_with_link($result->assignto);
				}else{
				$assignto = '-';
				}
			}else{
				$assignto = '-';
			}
		
			$nestedData[] = '#'.$result->id;
			$nestedData[] = service_finder_date_format($result->date);
			$nestedData[] = service_finder_getExcerpts($result->message,0,50);
			$nestedData[] = $hiringstatus;
			$nestedData[] = $assignto;
			
			$totalreplies = service_finder_get_number_of_replies($result->id);
			
			$redirect_uri = add_query_arg( array('quoteid' => $result->id), service_finder_get_url_by_shortcode('[service_finder_quotation_replies]') );
			
			$nestedData[] = '<a target="_blank" href="'.esc_url($redirect_uri).'" class="btn btn-custom btn-xs" title="'.esc_html__('View All Replies', 'service-finder').' ( '.$totalreplies.' )"><i class="fa fa-reply-all"></i> <span class="sf-total-msgnumber">'.esc_html($totalreplies).'</span></a> <button title="'.esc_html__('View Quotation', 'service-finder').'" data-quoteid="'.esc_attr($result->id).'" class="btn btn-primary btn-xs viewquotation" type="button"><i class="fa fa-eye"></i></button> ';
			
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
	
	/*View quotation details*/
	public function service_finder_view_quotation_details($arg){
		global $wpdb, $service_finder_Tables, $service_finder_options, $current_user;
		
		$quoteid = (!empty($arg['quoteid'])) ? $arg['quoteid'] : '';
		$user_id = (!empty($arg['quoteid'])) ? $arg['user_id'] : '';
		$customerreplacestring = (!empty($service_finder_options['customer-replace-string'])) ? $service_finder_options['customer-replace-string'] : esc_html__('Customer', 'service-finder');	
		$html = '';
		$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->quotations.' WHERE `id` = %d',$quoteid));
		
		if(!empty($row)){
		$attachfile = '';
		$attachments = (!empty($row->attachments)) ? unserialize($row->attachments) : '';
		
			if(!empty($attachments)){
				foreach($attachments as $attachmentid){
				$attachfile .= '<a href="'.get_permalink( $attachmentid ).'?attachment_id='. $attachmentid.'&download_file=1"><i class="fa fa-download"></i> '.esc_html__('View/Download').'</a><br/>';
				}
			}
			$attachfile = ($attachfile == '') ? esc_html__('N/A', 'service-finder') : $attachfile;
			$html = '<div class="margin-b-30 text-right"> <button type="button" class="btn btn-primary closequotedetails"><i class="fa fa-arrow-left"></i>'.esc_html__('Back', 'service-finder').'</button> </div>
						<table class="table table-striped table-bordered" border="0">
					  <tr>
						<td>'.esc_html__('Quote ID', 'service-finder').'</td>
						<td>#'.esc_html($quoteid).'</td>
					  </tr>
					  <tr>
						<td>'.esc_html__('Date', 'service-finder').'</td>
						<td>'.service_finder_date_format($row->date).'</td>
					  </tr>
					  <tr>
						<td>'.sprintf(esc_html__('%s Name', 'service-finder'),$customerreplacestring).'</td>
						<td>'.esc_html($row->name).'</td>
					  </tr>
					  <tr>
						<td>'.esc_html__('Email', 'service-finder').'</td>
						<td>'.esc_html($row->email).'</td>
					  </tr>
					  <tr>
						<td>'.esc_html__('Phone', 'service-finder').'</td>
						<td>'.esc_html($row->phone).'</td>
					  </tr>
					  <tr>
						<td>'.esc_html__('Message', 'service-finder').'</td>
						<td>'.esc_html($row->message).'</td>
					  </tr>
					  <tr>
						<td>'.esc_html__('Attachments', 'service-finder').'</td>
						<td>'.$attachfile.'</td>
					  </tr>
					  </table>';
		}

		$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->quotations.' WHERE `id` = %d AND `provider_id` = %d',$quoteid,$user_id));

		if(!empty($row)){
			$quotereply = (!empty($row->reply)) ? $row->reply : '';
			$quoteprice = ($row->quote_price > 0) ? $row->quote_price : '';
		}else{
		
		$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->quoteto_related_providers.' WHERE `quote_id` = %d AND `related_provider_id` = %d',$quoteid,$user_id));

		if(!empty($row)){
			$quotereply = (!empty($row->related_reply)) ? $row->related_reply : '';
			$quoteprice = ($row->related_quote_price > 0) ? $row->related_quote_price : '';
		}
		}
		
		if($quotereply != '' || $quoteprice > 0){
		$html .= '<h3>'.esc_html__('Quote Reply', 'service-finder').'</h3>';
		$html .= '<table class="table table-striped table-bordered" border="0">
					  <tr>
						<td>'.esc_html__('Quote Reply', 'service-finder').'</td>
						<td>'.esc_html($quotereply).'</td>
					  </tr>
					  <tr>
						<td>'.esc_html__('Quote Price', 'service-finder').'</td>
						<td>'.service_finder_money_format($quoteprice).'</td>
					  </tr>
					  </table>';
		}
		
		
		echo $html;
	}
	
	/*View quotation details*/
	public function service_finder_customer_quote_description($arg){
		global $wpdb, $service_finder_Tables, $service_finder_options, $current_user;
	
		$quoteid = (!empty($arg['quoteid'])) ? $arg['quoteid'] : '';
		$customerreplacestring = (!empty($service_finder_options['customer-replace-string'])) ? $service_finder_options['customer-replace-string'] : esc_html__('Customer', 'service-finder');	
		$html = '';
		$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->quotations.' WHERE `id` = %d',$quoteid));
		
		if(!empty($row)){
		$attachfile = '';
		$attachments = (!empty($row->attachments)) ? unserialize($row->attachments) : '';
			if(!empty($attachments)){
				foreach($attachments as $attachmentid){
				$attachfile .= '<a href="'.get_permalink( $attachmentid ).'?attachment_id='. $attachmentid.'&download_file=1"><i class="fa fa-download"></i> '.esc_html__('View/Download').'</a><br/>';
				}
			}
			
			$html = '<table class="table table-striped table-bordered" border="0">
					  <tr>
						<td>'.esc_html__('Date', 'service-finder').'</td>
						<td>'.service_finder_date_format($row->date).'</td>
					  </tr>
					  <tr>
						<td>'.sprintf(esc_html__('%s Name', 'service-finder'),$customerreplacestring).'</td>
						<td>'.esc_html($row->name).'</td>
					  </tr>
					  <tr>
						<td>'.esc_html__('Email', 'service-finder').'</td>
						<td>'.esc_html($row->email).'</td>
					  </tr>
					  <tr>
						<td>'.esc_html__('Phone', 'service-finder').'</td>
						<td>'.esc_html($row->phone).'</td>
					  </tr>
					  <tr>
						<td>'.esc_html__('Message', 'service-finder').'</td>
						<td>'.esc_html($row->message).'</td>
					  </tr>
					  <tr>
						<td>'.esc_html__('Attachments', 'service-finder').'</td>
						<td>'.$attachfile.'</td>
					  </tr>
					  </table>';
		}
		
		echo $html;
	}
	
	/*View replies listing*/
	public function service_finder_view_replies_listing($arg){
		global $wpdb, $service_finder_Tables, $service_finder_options, $current_user;
		$html = '';
		$quoteid = (!empty($arg['quoteid'])) ? $arg['quoteid'] : '';
		
		$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->quotations.' WHERE status = "approved" AND id = %d AND (reply != "" OR quote_price > 0)',$quoteid));
		
		$html .= '<div class="margin-b-30 text-right"> <button type="button" class="btn btn-primary closereplieslisting"><i class="fa fa-arrow-left"></i>'.esc_html__('Back', 'service-finder').'</button> </div>';
		
		if(!empty($results)){
			foreach($results as $row){
				$providerid = $row->provider_id;
				
				$html .= $this->service_finder_providers_listing($quoteid,$providerid,$row->reply,$row->quote_price,$row->hired,$row->assignto);
				
			}
		}
		
		$results = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->quoteto_related_providers.' WHERE quote_id = %d AND (related_reply != "" OR related_quote_price > 0)',$quoteid));
		
		if(!empty($results)){
			foreach($results as $row){
				$providerid = $row->related_provider_id;
				
				$quoterow = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->quotations.' WHERE status = "approved" AND id = %d',$quoteid));
				
				$html .= $this->service_finder_providers_listing($quoteid,$providerid,$row->related_reply,$row->related_quote_price,$quoterow->hired,$quoterow->assignto);
				
			}
		}
		
		echo $html;
	}
	
	public function service_finder_providers_listing($quoteid,$providerid,$reply,$quote_price,$hired,$assignto){
		global $wpdb, $service_finder_Tables, $service_finder_options, $current_user;

		$html = '';
		$provider = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `admin_moderation` = "approved" AND `account_blocked` != "yes" AND `wp_user_id` = %d',$providerid));
				
		$link = service_finder_get_author_url($providerid);
		$settings = service_finder_getProviderSettings($providerid);

		if(!empty($provider->avatar_id) && $provider->avatar_id > 0){
			$src  = wp_get_attachment_image_src( $provider->avatar_id, 'service_finder-provider-thumb' );
			$src  = $src[0];
		}else{
			$src  = service_finder_get_default_avatar();
		}
		
		if($src != ''){
			$imgtag = '<img src="'.esc_url($src).'" width="358" height="259" alt="">';
		}else{
			$imgtag = '';
		}
		
		if(service_finder_is_featured($provider->wp_user_id)){
		$featured = '<strong class="sf-featured-label"><span>'.esc_html__( 'Featured', 'service-finder' ).'</span></strong>';
		}else{
		$featured = '';
		}
		
		$hiredhtml = '';
		$hireurl = add_query_arg( array( 'quoteid' => absint( $quoteid ) . '#book-now-section' ), $link );
		
			if($hired == 'yes'){
				if($assignto == $provider->wp_user_id){
					$hireme = '<a href="javascript:;" class="btn btn-primary">'.esc_html__( 'Hired', 'service-finder' ).' <i class="fa fa-user"></i></a>';
					$hiredhtml = '<strong class="sf-hired-label"><span>'.esc_html__( 'Hired', 'service-finder' ).'</span></strong>';
				}else{
					$hireme = '<a href="javascript:;" class="btn btn-primary">'.esc_html__( 'Quote Booked', 'service-finder' ).' <i class="fa fa-user"></i></a>';
				}
			}else{
					if(service_finder_UserRole($current_user->ID) == 'Customer'){
					if($settings['booking_process'] == 'on'){
					$hireme = '<a href="'.esc_url($hireurl).'" target="_blank" class="btn btn-primary">'.esc_html__( 'Hire Me', 'service-finder' ).' <i class="fa fa-user"></i></a>';
					}else{
					$hireme = '<a href="javascript:;" class="btn btn-primary hire_if_booking_off" data-providerid="'.$provider->wp_user_id.'" data-quoteid="'.$quoteid.'">'.esc_html__( 'Hire Me', 'service-finder' ).' <i class="fa fa-user"></i></a>';
					}
					}else{
					$hireme = '<a href="javascript:;" target="_blank" class="btn btn-primary">'.esc_html__( 'Not Hired', 'service-finder' ).' <i class="fa fa-user"></i></a>';				
					}
			}
		
		$quotedesc = $reply;
		
		if($quotedesc != ""){
		$quotation_desc = '<div class="btn-group sf-provider-tooltip" role="group"><button type="button" class="btn btn-border quote_description" data-toggle="tooltip" data-placement="top" title="'.esc_html__('Provider Description','service-finder').'" data-providerid="'.$provider->wp_user_id.'" data-quoteid="'.$quoteid.'"><i class="fa fa-commenting-o"></i></button></div>';
		}
		
		if($quote_price > 0){
		$quotation = '<div class="provider-quotation">
						  '.esc_html__('Quotation','service-finder').': '.service_finder_money_format($quote_price).'
						</div>';
		}
		$addressbox = '';
		$showaddressinfo = (isset($service_finder_options['show-address-info'])) ? esc_attr($service_finder_options['show-address-info']) : '';
		if($showaddressinfo && $service_finder_options['show-postal-address'] && service_finder_check_address_info_access()){
			$addressbox = '<div class="overlay-text">
									<div class="sf-address-bx">
										<i class="fa fa-map-marker"></i>
										'.service_finder_getshortAddress($provider->wp_user_id).'
									</div>
								</div>';
		}
		
		$html .= '<div class="col-md-4 col-sm-6 equal-col">

				<div class="sf-provider-bx item">
					<div class="sf-element-bx">
					
						<div class="sf-thum-bx sf-listing-thum img-effect2" style="background-image:url('.esc_url($src).');"> <a href="'.esc_url($link).'" class="sf-listing-link"></a>
							
							<div class="overlay-bx">
								'.$addressbox.'
							</div>
							
							'.service_finder_get_primary_category_tag($provider->wp_user_id).'
							'.$featured.'
							'.$hiredhtml.'
						</div>
						
						<div class="padding-20 bg-white">
							<h4 class="sf-title">'.service_finder_getExcerpts(service_finder_getCompanyName($provider->wp_user_id),0,20).'</h4>
							<strong class="sf-company-name"><a href="'.esc_url($link).'">'.service_finder_getExcerpts($provider->full_name,0,35).'</a></strong>
							'.service_finder_displayRating(service_finder_getAverageRating($provider->wp_user_id)).'
							
						</div>
						'.$quotation.' '.$quotation_desc.'
						<div class="btn-group-justified" id="proid-'.$provider->wp_user_id.'">
						  <a href="'.esc_url($link).'" target="_blank" class="btn btn-custom mark-fullview">'.esc_html__('Full View','service-finder').' <i class="fa fa-arrow-circle-o-right"></i></a>
						  '.$hireme.'
						</div>
						
						
					</div>
				</div>

			</div>';	
			
		return $html;		
	}
	
	public function service_finder_reply_description($arg){
		global $wpdb,$service_finder_Tables;
		$quoteid = (!empty($arg['quoteid'])) ? $arg['quoteid'] : 0;
		$userid = (!empty($arg['providerid'])) ? $arg['providerid'] : 0;
		
		$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->quotations.' WHERE `id` = %d AND `provider_id` = %d',$quoteid,$userid));
		$reply = '';
		if(!empty($row)){
			$reply = $row->reply;
			echo $reply;
			exit;
		}else{
		
		$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->quoteto_related_providers.' WHERE `quote_id` = %d AND `related_provider_id` = %d',$quoteid,$userid));
		$reply = '';
		if(!empty($row)){
			$reply = $row->related_reply;
			echo $reply;
			exit;
		}
		}
		
	}
	
	/*Load quote reply for edit*/
	public function service_finder_load_quote_reply($arg){
		global $wpdb, $service_finder_Tables;		
		
		$quoteid = (!empty($arg['quoteid'])) ? $arg['quoteid'] : 0;
		$userid = (!empty($arg['userid'])) ? $arg['userid'] : 0;
		
		$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->quotations.' WHERE `id` = %d AND `provider_id` = %d',$quoteid,$userid));

		if(!empty($row)){
			$result = array(
				'reply'			=> (!empty($row->reply)) ? $row->reply : '',
				'quote_price'	=> ($row->quote_price > 0) ? $row->quote_price : '',

			);

			echo json_encode($result);
			exit;
		}else{
		
		$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->quoteto_related_providers.' WHERE `quote_id` = %d AND `related_provider_id` = %d',$quoteid,$userid));

		if(!empty($row)){
			$result = array(
				'reply'			=> (!empty($row->related_reply)) ? $row->related_reply : '',
				'quote_price'	=> ($row->related_quote_price > 0) ? $row->related_quote_price : '',
			);

			echo json_encode($result);
		}
		}
			
	}
	
	/*update quote reply*/
	public function service_finder_update_quote_reply($arg = ''){
			global $wpdb, $service_finder_Tables;
			
			$userid = (!empty($arg['userid'])) ? $arg['userid'] : '';
			$quoteid = (!empty($arg['quoteid'])) ? $arg['quoteid'] : '';
			$quote_price = (!empty($arg['quote_price'])) ? $arg['quote_price'] : '';
			$quote_reply = (!empty($arg['quote_reply'])) ? $arg['quote_reply'] : '';
			
			$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->quotations.' WHERE `id` = %d AND `provider_id` = %d',$quoteid,$userid));
			
			if(!empty($row)){
				$data = array(
					'reply'		=> $quote_reply,
					'quote_price'	=> $quote_price,
				);
				
				$where = array(
					'id'		=> $quoteid,
					'provider_id' => $userid
				);
				
				$wpdb->update($service_finder_Tables->quotations,wp_unslash($data),$where);
				
				$noticedata = array(
						'customer_id' => $row->customer_id,
						'target_id' => $quoteid, 
						'topic' => 'Quote Response',
						'title' => esc_html__('Quote Response', 'service-finder'),
						'notice' => sprintf(esc_html__('You have received reply of your quoatation id: #%d.', 'service-finder'),$quoteid),
						);
				service_finder_add_notices($noticedata);
				
				$this->service_finder_send_quotation_reply_mail($quoteid,$row->email,$quote_price,$quote_reply,$userid);
			}else{
				$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->quoteto_related_providers.' WHERE `quote_id` = %d AND `related_provider_id` = %d',$quoteid,$userid));
				
				if(!empty($row)){
					$data = array(
						'related_reply'		=> $quote_reply,
						'related_quote_price'	=> $quote_price,
					);
					
					$where = array(
						'quote_id'		=> $quoteid,
						'related_provider_id' => $userid
					);
					
					$wpdb->update($service_finder_Tables->quoteto_related_providers,wp_unslash($data),$where);
					
					$quoterow = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->quotations.' WHERE `id` = %d',$quoteid));
					
					$noticedata = array(
							'customer_id' => $quoterow->customer_id,
							'target_id' => $quoteid, 
							'topic' => 'Quote Response',
							'title' => esc_html__('Quote Response', 'service-finder'),
							'notice' => sprintf(esc_html__('You have received reply of your quoatation id: #%d.', 'service-finder'),$quoteid),
							);
					service_finder_add_notices($noticedata);
					
					$this->service_finder_send_quotation_reply_mail($quoteid,$quoterow->email,$quote_price,$quote_reply,$userid);
				}
			}
			
			$success = array(
					'status' => 'success',
					'suc_message' => esc_html__('Reply update successfully.', 'service-finder'),
					);
			echo json_encode($success);
	}
	
	/*Send quotations reply mail*/
	public function service_finder_send_quotation_reply_mail($quoteid = '',$email,$quote_price = '',$quote_reply = '',$userid =  0){
	global $wpdb, $service_finder_options;
	
	if($service_finder_options['quotation-reply-message'] != ""){
		$message = $service_finder_options['quotation-reply-message'];
	}else{
		$message = 'You have received reply of your quoatation.<br/>
		Provider Name: %PROVIDERNAME%
		Quote ID: %QUOTEID%
		Quote Price: %QUOTEPRICE%
		Reply: %REPLY%';
	}
	
	
	$tokens = array('%PROVIDERNAME%','%QUOTEID%','%REPLY%','%QUOTEPRICE%');
	$replacements = array(service_finder_get_providername_with_link($userid),'#'.$quoteid,$quote_reply,service_finder_money_format($quote_price));
	$msg_body = str_replace($tokens,$replacements,$message);
	
	if($service_finder_options['quotation-reply-message-subject'] != ""){
		$msg_subject = $service_finder_options['quotation-reply-message-subject'];
	}else{
		$msg_subject = 'Quote Response';
	}
	
	if(service_finder_wpmailer($email,$msg_subject,$msg_body)) {

		$success = array(
				'status' => 'success',
				'suc_message' => esc_html__('Message has been sent', 'service-finder'),
				);
		$service_finder_Success = json_encode($success);
		return $service_finder_Success;
		
		
	}	
			
	}

	/*Get Service Quotetion*/
	public function service_finder_get_quote_mail($args){
			global $wpdb, $service_finder_Tables, $service_finder_options, $current_user;
			
			$provider_id = (!empty($args['provider_id'])) ? $args['provider_id'] : $args['proid'];
			$customer_name = (!empty($args['customer_name'])) ? $args['customer_name'] : '';
			$customer_email = (!empty($args['customer_email'])) ? $args['customer_email'] : '';
			$phone = (!empty($args['phone'])) ? $args['phone'] : '';
			$description = (!empty($args['description'])) ? $args['description'] : '';
			$captcha_code = (!empty($args['captcha_code'])) ? $args['captcha_code'] : '';
			$captchaon = (!empty($args['captchaon'])) ? $args['captchaon'] : 0;
			$quoteattachmentid = (!empty($args['quoteattachmentid'])) ? serialize($args['quoteattachmentid']) : '';
			$relatedproviders = (!empty($args['relatedproviders'])) ? $args['relatedproviders'] : '';
			
			if($service_finder_options['request-quote-mail-to'] == 'hold'){
				$status = 'hold';
			}else{
				$status = 'approved';
			}
			
			if(is_user_logged_in() && service_finder_getUserRole($current_user->ID) == 'Customer'){
			$data = array(
					'provider_id' => $provider_id,
					'customer_id' => $current_user->ID,
					'date' => date('Y-m-d h:i:s'),
					'name' => $customer_name,
					'email' => $customer_email,
					'phone' => $phone,
					'message' => $description,
					'status' => $status,
					'attachments' => $quoteattachmentid,
					);
			}else{
			$data = array(
					'provider_id' => $provider_id,
					'date' => date('Y-m-d h:i:s'),
					'name' => $customer_name,
					'email' => $customer_email,
					'phone' => $phone,
					'message' => $description,
					'status' => $status,
					);
			}
			$wpdb->insert($service_finder_Tables->quotations,wp_unslash($data));
			
			$quoteid = $wpdb->insert_id;
			
			if(!empty($relatedproviders)){
				foreach($relatedproviders as $relatedprovider){
					$data = array(
							'quote_id' => $quoteid,
							'related_provider_id' => $relatedprovider,
							);
		
					$wpdb->insert($service_finder_Tables->quoteto_related_providers,wp_unslash($data));
				}
			}
			
			$getProvider = new SERVICE_FINDER_searchProviders();
			$providerInfo = $getProvider->service_finder_getProviderInfo(esc_attr($provider_id));
			
			$adminemail = get_option( 'admin_email' );

			if(!empty($service_finder_options['quote-to-provider'])){
				$message = $service_finder_options['quote-to-provider'];
			}else{
				$message = 'Requesting for Quotation

Customer Name: %CUSTOMERNAME%

Email: %EMAIL%

Phone: %PHONE%

Description: %DESCRIPTION%
';
			}
			
			if(!empty($service_finder_options['quote-to-admin'])){
				$adminmessage = $service_finder_options['quote-to-admin'];
			}else{
				$adminmessage = 'Requesting for Quotation for provider

Provider Name: %PROVIDERNAME%

Provider Email: %PROVIDEREMAIL%

Customer Name: %CUSTOMERNAME%

Email: %EMAIL%

Phone: %PHONE%

Description: %DESCRIPTION%
';
			}
			
			$userLink = service_finder_get_author_url($provider_id);
			
			$tokens = array('%PROVIDERNAME%','%PROVIDEREMAIL%','%CUSTOMERNAME%','%EMAIL%','%PHONE%','%DESCRIPTION%');
			$replacements = array(service_finder_get_providername_with_link($provider_id),'<a href="mailto:'.$providerInfo->email.'">'.$providerInfo->email.'</a>',$customer_name,$customer_email,$phone,$description);
			$msg_body = str_replace($tokens,$replacements,$message);
			$adminmsg_body = str_replace($tokens,$replacements,$adminmessage);
			if($service_finder_options['quote-to-provider-subject'] != ""){
				$msg_subject = $service_finder_options['quote-to-provider-subject'];
			}else{
				$msg_subject = esc_html__('Request a Quotation', 'service-finder');
			}
			if($service_finder_options['quote-to-admin-subject'] != ""){
				$adminmsg_subject = $service_finder_options['quote-to-admin-subject'];
			}else{
				$adminmsg_subject = esc_html__('Request a Quotation for provider', 'service-finder');
			}
			
			$msg = (!empty($service_finder_options['get-quote'])) ? $service_finder_options['get-quote'] : esc_html__('Message has been sent', 'service-finder');
			
			if($service_finder_options['request-quote-mail-to'] == 'provider'){
				
				$provideremails[] = $providerInfo->email;
				
				if(function_exists('service_finder_add_notices')) {
					$noticedata = array(
							'provider_id' => $provider_id,
							'target_id' => $quoteid, 
							'topic' => 'Get Quotation',
							'title' => esc_html__('Get Quotation', 'service-finder'),
							'notice' => sprintf(esc_html__('New quotation has arrived from %s', 'service-finder'),$customer_name)
							);
					service_finder_add_notices($noticedata);
				
				}
				
				if(!empty($relatedproviders)){
					foreach($relatedproviders as $relatedprovider){
						$provideremails[] = service_finder_getProviderEmail($relatedprovider);
						
						if(function_exists('service_finder_add_notices')) {
							$noticedata = array(
									'provider_id' => $relatedprovider,
									'target_id' => $quoteid, 
									'topic' => 'Get Quotation',
									'title' => esc_html__('Get Quotation', 'service-finder'),
									'notice' => sprintf(esc_html__('New quotation has arrived from %s', 'service-finder'),$customer_name)
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
					$success = array(
							'status' => 'success',
							'suc_message' => $msg
							);
					echo json_encode($success);
				} else {
					$error = array(
							'status' => 'error',
							'err_message' => esc_html__('Message could not be sent.', 'service-finder'),
							);
					echo json_encode($error);
				}
			
			}elseif($service_finder_options['request-quote-mail-to'] == 'admin' || $service_finder_options['request-quote-mail-to'] == 'hold'){
			
				if(service_finder_wpmailer($adminemail,$adminmsg_subject,$adminmsg_body)) {
					$success = array(
							'status' => 'success',
							'suc_message' => $msg
							);
					echo json_encode($success);
				} else {
					$error = array(
							'status' => 'error',
							'err_message' => esc_html__('Message could not be sent.', 'service-finder'),
							);
					echo json_encode($error);
				}
			
			}elseif($service_finder_options['request-quote-mail-to'] == 'both'){
			
				$provideremails[] = $providerInfo->email;
				
				if(function_exists('service_finder_add_notices')) {
					$noticedata = array(
							'provider_id' => $provider_id,
							'target_id' => $quoteid, 
							'topic' => 'Get Quotation',
							'title' => esc_html__('Get Quotation', 'service-finder'),
							'notice' => sprintf(esc_html__('New quotation has arrived from %s', 'service-finder'),$customer_name)
							);
					service_finder_add_notices($noticedata);
				
				}
				
				if(!empty($relatedproviders)){
					foreach($relatedproviders as $relatedprovider){
						$provideremails[] = service_finder_getProviderEmail($relatedprovider);
						
						if(function_exists('service_finder_add_notices')) {
							$noticedata = array(
									'provider_id' => $relatedprovider,
									'target_id' => $quoteid, 
									'topic' => 'Get Quotation',
									'title' => esc_html__('Get Quotation', 'service-finder'),
									'notice' => sprintf(esc_html__('New quotation has arrived from %s', 'service-finder'),$customer_name)
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
				
				service_finder_wpmailer($provideremails,$msg_subject,$msg_body);
				
				if(service_finder_wpmailer($adminemail,$adminmsg_subject,$adminmsg_body)) {
					$success = array(
							'status' => 'success',
							'suc_message' => $msg
							);
					echo json_encode($success);
				} else {
					$error = array(
							'status' => 'error',
							'err_message' => esc_html__('Message could not be sent.', 'service-finder'),
							);
					echo json_encode($error);
				}

			}
			
			
		}
				
}