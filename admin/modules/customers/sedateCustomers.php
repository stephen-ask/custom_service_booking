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
 * Class SERVICE_FINDER_sedateCustomers
 */
class SERVICE_FINDER_sedateCustomers extends SERVICE_FINDER_sedateManager{

	
	/*Initial Function*/
	public function service_finder_index()
    {
        
		/*Rander customers template*/
		$this->service_finder_render( 'index','customers' );
		
		/*Action for wp ajax call*/
		$this->service_finder_registerWpActions();
		
    }
	
	/*Actions for wp ajax call*/
	protected function service_finder_registerWpActions() {
       $_this = $this;
	   add_action(
                    'wp_ajax_get_customers',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_get_customers' ) );
                    }
						
                );
		add_action(
                    'wp_ajax_delete_customers',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_delete_customers' ) );
                    }
						
                );		
				
    }
	
	/*Display customers into datatable*/
	public function service_finder_get_customers(){
		global $wpdb, $service_finder_Tables;
		$requestData= $_REQUEST;
		
		$sql = 'SELECT customerdata.id,customerdata.wp_user_id,customerdata.phone, customerdata.phone2, customerdata.address, customerdata.apt, customerdata.city, customerdata.state, customerdata.zipcode, userdata.user_email, userdata.display_name FROM '.$service_finder_Tables->customers_data.' as customerdata INNER JOIN `'.$wpdb->prefix.'users` as userdata on customerdata.wp_user_id = userdata.ID';
		
		$totalrecords = $wpdb->get_results($sql);
		$totalrecords = count($totalrecords);
		
		if( !empty($requestData['search']['value']) ) {
			$sql .= " WHERE 1 = 1";    
			$sql .= " AND ( customerdata.wp_user_id LIKE '".$requestData['search']['value']."%' ";    
			$sql .= " OR customerdata.phone LIKE '".$requestData['search']['value']."%' ";
			$sql .= " OR customerdata.phone2 LIKE '".$requestData['search']['value']."%' ";
			$sql .= " OR customerdata.city LIKE '".$requestData['search']['value']."%' ";
			$sql .= " OR userdata.user_email LIKE '".$requestData['search']['value']."%' ";
			$sql .= " OR userdata.display_name LIKE '".$requestData['search']['value']."%' )";
		}
		
		$totalfiltered = $wpdb->get_results($sql);
		$totalfiltered = count($totalfiltered);

		$sql .= " LIMIT ".$requestData['start'].','.$requestData['length'];
		
		$customers = $wpdb->get_results($sql);
		
		$data = array();
		
		foreach($customers as $result){
			$nestedData=array(); 
			$resultid = (!empty($result->wp_user_id)) ? $result->wp_user_id : '';
			$nestedData['customerid'] = $result->wp_user_id;
			$nestedData['delete'] = "<input type='checkbox' class='deleteCustomersRow' value='".esc_attr($resultid)."'  />";
			$nestedData['customername'] = service_finder_getCustomerName($result->wp_user_id);
			$nestedData['phone'] = $result->phone.' '.$result->phone2;
			$nestedData['email'] = $result->user_email;
			
			$address = ($result->address != '') ? $result->address : 'N/A';
			$apt = ($result->apt != '') ? $result->apt : 'N/A';
			$state = ($result->state != '') ? $result->state : 'N/A';
			$zipcode = ($result->zipcode != '') ? $result->zipcode : 'N/A';
			
			$customerinfo = '<span data-toggle="popover" data-container="body" data-placement="top" type="button" data-html="true" id="customerinfo-'.$result->id.'" data-trigger="hover"><i class="fa fa-question-circle"></i></span>';
			$customerinfo .= '<div id="popover-content-customerinfo-'.$result->id.'" class="hide pop-full">
									<ul class="sf-popoverinfo-list">
										<li><span>'.esc_html__( 'Address','service-finder' ).':</span> <span>'.$address.'</span></li>
										<li><span>'.esc_html__( 'Apt','service-finder' ).':</span> <span>'.$apt.'</span></li>
										<li><span>'.esc_html__( 'State','service-finder' ).':</span> <span>'.$state.'</span></li>
										<li><span>'.esc_html__( 'Postal Code','service-finder' ).':</span> <span>'.$zipcode.'</span></li>
									</ul>
								</div>';
			
			$nestedData['city'] = $result->city.' '.$customerinfo;
			
			$walletbtn = '';
			if(service_finder_check_wallet_system()){
			$walletamount = service_finder_get_wallet_amount($result->wp_user_id);
			$walletbtn = service_finder_money_format($walletamount);
			$walletbtn .= ' <li><a href="javascript:;" data-id="'.esc_attr($result->wp_user_id).'" class="addtowallet"><i class="fa fa-money"></i> '.esc_html__('Add Balance to Wallet', 'service-finder').'</a></li>';
			}else{
				$walletbtn = '<li><a href="javascript:;" data-toggle="tooltip" title="'.esc_html__( 'Please activate wallet system from theme options to enable this.','service-finder' ).'" class="disable-btn"><i class="fa fa-money"></i> '.esc_html__('Add Balance to Wallet', 'service-finder').'</a></li>';
			}
			$actions = '<div class="dropdown action-dropdown dropdown-left">
						  <button class="action-button gray dropdown-toggle" type="button" data-toggle="dropdown"><i class="fa fa-ellipsis-v"></i></button>
						  <ul class="dropdown-menu">
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
	
	/*Delete customers*/
	protected function service_finder_delete_customers(){
	global $wpdb, $service_finder_Tables;
			$data_ids = $_REQUEST['data_ids'];
			$data_id_array = explode(",", $data_ids); 
			if(!empty($data_id_array)) {
				foreach($data_id_array as $id) {
					wp_delete_user( $id );
					$sql = $wpdb->prepare("DELETE FROM ".$service_finder_Tables->customers_data." WHERE id = %d",$id);
					$query=$wpdb->query($sql);
				}
			}
	exit(0);		
	}
	
}