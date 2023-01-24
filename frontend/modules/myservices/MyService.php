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

class SERVICE_FINDER_MyService{

	/*Add New Service*/
	public function service_finder_addServices($arg){
			global $wpdb, $service_finder_Tables;
			
			$currUser = wp_get_current_user(); 
			$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';
			$group_id = (!empty($arg['group_id'])) ? $arg['group_id'] : '';
			$gname = (!empty($arg['gname']) && $group_id > 0) ? $arg['gname'] : '';
			
			$sedateProvider = $wpdb->get_row('SELECT * FROM '.$service_finder_Tables->providers.' where wp_user_id = '.$user_id);
			
			$data = array(
					'wp_user_id' => $user_id,
					'provider_id' => $sedateProvider->id,
					'service_name' => (!empty($arg['service_name'])) ? $arg['service_name'] : '',
					'cost' => (!empty($arg['service_cost'])) ? $arg['service_cost'] : '',
					'cost_type' => (!empty($arg['cost_type'])) ? $arg['cost_type'] : '',
					'hours' => (!empty($arg['service_hours'])) ? $arg['service_hours'] : 0,
					'persons' => (!empty($arg['service_persons'])) ? $arg['service_persons'] : 0,
					'days' => (!empty($arg['service_days'])) ? $arg['service_days'] : 0,
					'before_padding_time' => (!empty($arg['before_padding_time'])) ? $arg['before_padding_time'] : 0,
					'after_padding_time' => (!empty($arg['after_padding_time'])) ? $arg['after_padding_time'] : 0,
					'description' => (!empty($arg['description'])) ? $arg['description'] : '',
					'group_id' => $group_id,
					'group_name' => $gname,
					'offer' => (!empty($arg['offers'])) ? $arg['offers'] : '',
					'offer_title' => (!empty($arg['offer_title'])) ? $arg['offer_title'] : '',
					'coupon_code' => (!empty($arg['coupon_code'])) ? $arg['coupon_code'] : '',
					'expiry_date' => (!empty($arg['expiry_date'])) ? date('Y-m-d',strtotime($arg['expiry_date'])) : '',
					'max_coupon' => (!empty($arg['max_coupon'])) ? $arg['max_coupon'] : '',
					'discount_type' => (!empty($arg['discount_type'])) ? $arg['discount_type'] : '',
					'discount_value' => (!empty($arg['discount_value'])) ? $arg['discount_value'] : '',
					'discount_description' => (!empty($arg['discount_description'])) ? $arg['discount_description'] : '',
					);

			$wpdb->insert($service_finder_Tables->services,wp_unslash($data));
			
			$service_id = $wpdb->insert_id;
			
			$service_type = (!empty($arg['cost_type'])) ? $arg['cost_type'] : '';
			if($service_type == 'days'){
			$weekdays = service_finder_get_weekdays();
			if(!empty($weekdays)){
				foreach($weekdays as $weekday){
				$weekdaystatus = (!empty($arg[$weekday.'_status'])) ? $arg[$weekday.'_status'] : '';
				if($weekdaystatus == 'on'){
					$data = array(
							'service_id' => $service_id,
							'provider_id' => $user_id,
							'service_type' => 'days',
							'day' => $weekday,
							'max_booking' => (!empty($arg[$weekday.'_max_booking'])) ? $arg[$weekday.'_max_booking'] : '',
							);
		
					$wpdb->insert($service_finder_Tables->days_availability,wp_unslash($data));				
				}
				}
			}	
			}
			
			if ( ! $service_id ) {
				$adminemail = get_option( 'admin_email' );
				$allowedhtml = array(
					'a' => array(
						'href' => array(),
						'title' => array()
					),
				);
				$error = array(
						'status' => 'error',
						'err_message' => sprintf( wp_kses(esc_html__('Couldn&#8217;t add service... please contact the <a href="mailto:%s">Administrator</a> !', 'service-finder'),$allowedhtml), $adminemail )
						);
				echo json_encode($error);
			}else{
				$success = array(
						'status' => 'success',
						'suc_message' => esc_html__('Add service successfully.', 'service-finder'),
						'serviceid' => $service_id,
						);
				echo json_encode($success);
			}
		
		}
		
	/*Edit Service*/
	public function service_finder_editService($arg){
			global $wpdb, $service_finder_Tables;
			
			$currUser = wp_get_current_user(); 
			$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';
			$sedateProvider = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' where wp_user_id = %d',$user_id));
			
			$group_id = (!empty($arg['group_id'])) ? $arg['group_id'] : '';
			$gname = (!empty($arg['gname'])) ? $arg['gname'] : '';
			
			$data = array(
					'service_name' => (!empty($arg['service_name'])) ? $arg['service_name'] : '',
					'cost' => (!empty($arg['service_cost'])) ? $arg['service_cost'] : '',
					'cost_type' => (!empty($arg['cost_type'])) ? $arg['cost_type'] : '',
					'hours' => (!empty($arg['service_hours'])) ? $arg['service_hours'] : 0,
					'persons' => (!empty($arg['service_persons'])) ? $arg['service_persons'] : 0,
					'days' => (!empty($arg['service_days'])) ? $arg['service_days'] : 0,
					'before_padding_time' => (!empty($arg['before_padding_time'])) ? $arg['before_padding_time'] : 0,
					'after_padding_time' => (!empty($arg['after_padding_time'])) ? $arg['after_padding_time'] : 0,
					'description' => (!empty($arg['editdesc'])) ? $arg['editdesc'] : '',
					'group_id' => (!empty($arg['group_id'])) ? $arg['group_id'] : '',
					'group_name' => ($group_id != "" && $group_id > 0) ? $gname : '',
					'offer' => (!empty($arg['offers'])) ? $arg['offers'] : '',
					'offer_title' => (!empty($arg['offer_title'])) ? $arg['offer_title'] : '',
					'coupon_code' => (!empty($arg['coupon_code'])) ? $arg['coupon_code'] : '',
					'expiry_date' => (!empty($arg['expiry_date'])) ? date('Y-m-d',strtotime($arg['expiry_date'])) : '',
					'max_coupon' => (!empty($arg['max_coupon'])) ? $arg['max_coupon'] : '',
					'discount_type' => (!empty($arg['discount_type'])) ? $arg['discount_type'] : '',
					'discount_value' => (!empty($arg['discount_value'])) ? $arg['discount_value'] : '',
					'discount_description' => (!empty($arg['edit_discount_description'])) ? $arg['edit_discount_description'] : '',
					);
			
			$where = array(
						'id' => $arg['serviceid'],
						);

			$service_update_id = $wpdb->update($service_finder_Tables->services,wp_unslash($data),$where);		
			
			$wpdb->query($wpdb->prepare("DELETE FROM ".$service_finder_Tables->days_availability." WHERE `provider_id` = %d AND `service_id` = %d",$user_id,$arg['serviceid']));
			
			$service_type = (!empty($arg['cost_type'])) ? $arg['cost_type'] : '';
			if($service_type == 'days'){
			$weekdays = service_finder_get_weekdays();
			if(!empty($weekdays)){
				foreach($weekdays as $weekday){
				$weekdaystatus = (!empty($arg[$weekday.'_status'])) ? $arg[$weekday.'_status'] : '';
				
				if($weekdaystatus == 'on'){
					$data = array(
							'service_id' => $arg['serviceid'],
							'provider_id' => $user_id,
							'service_type' => 'days',
							'day' => $weekday,
							'max_booking' => (!empty($arg[$weekday.'_max_booking'])) ? $arg[$weekday.'_max_booking'] : '',
							);
		
					$wpdb->insert($service_finder_Tables->days_availability,wp_unslash($data));				
				}
				}
			}
			}

			if(is_wp_error($service_update_id)){
				$adminemail = get_option( 'admin_email' );
				$error = array(
						'status' => 'error',
						'err_message' => $service_id->get_error_message()
						);
				echo json_encode($error);
			}else{
				$success = array(
						'status' => 'success',
						'suc_message' => esc_html__('Edit service successfully.', 'service-finder'),
						'serviceid' => $arg['serviceid'],
						);
				echo json_encode($success);
			}
		
		}	
	
	/*Load service for edit*/
	public function service_finder_loadService($arg){
			global $wpdb, $service_finder_Tables;		
			$service = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->services.' WHERE `id` = %d',$arg['serviceid']));
			if(!empty($service)){
					$html = '<select class="sf-select-box form-control sf-form-control" name="group_id" data-live-search="true" title="'.esc_html__('Select Group', 'service-finder').'" id="group_id">';
					$groupinfo = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->service_groups.' where provider_id = %d ORDER BY group_name',$service->wp_user_id));
						  $html .= '<option value="">'.esc_html__('Select Group', 'service-finder').'</option>';
						  if(!empty($groupinfo)){
							foreach($groupinfo as $grouprow){
								if($grouprow->id == $service->group_id){
									$select = 'selected="selected"';
								}else{
									$select = '';
								}
							
								$html .= '<option '.$select.' value="'.esc_attr($grouprow->id).'">'.esc_html($grouprow->group_name).'</option>';
							}
						  }
						  
					$html .= '</select>';
					
					$days_availability = array();
					$weekdays = service_finder_get_weekdays();
					if(!empty($weekdays)){
						foreach($weekdays as $weekday){
						$daysavl = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->days_availability.' WHERE `service_id` = %d AND `day` = %s',$arg['serviceid'],$weekday));			
						if(!empty($daysavl)){
							$days_availability[$weekday]['max_booking'] = $daysavl->max_booking;
							$days_availability[$weekday]['day_status'] = 'on';
						}else{
							$days_availability[$weekday]['max_booking'] = '';
							$days_availability[$weekday]['day_status'] = 'off';
						}

						}
					}	
					
					$result = array(
							'service_name' => stripcslashes($service->service_name),
							'cost' => $service->cost,
							'cost_type' => $service->cost_type,
							'hours' => $service->hours,
							'persons' => $service->persons,
							'days' => $service->days,
							'days_availability' => $days_availability,
							'before_padding_time' => $service->before_padding_time,
							'after_padding_time' => $service->after_padding_time,
							'html' => $html,
							'description' => $service->description,
							'offer' => ($service->offer == 'yes') ? true : false,
							'offer_title' => (!empty($service->offer_title)) ? $service->offer_title : '',
							'coupon_code' => (!empty($service->coupon_code)) ? $service->coupon_code : '',
							'expiry_date' => (!empty($service->expiry_date)) ? service_finder_date_format($service->expiry_date) : '',
							'max_coupon' => (!empty($service->max_coupon)) ? $service->max_coupon : '',
							'discount_type' => (!empty($service->discount_type)) ? $service->discount_type : '',
							'discount_value' => (!empty($service->discount_value)) ? $service->discount_value : '',
							'discount_description' => (!empty($service->discount_description)) ? $service->discount_description : '',
					);

			}
			echo json_encode($result);
	}
		
		
	/*Get Saved Services into datatable*/
	public function service_finder_getServices($arg){
		global $wpdb, $service_finder_Tables;
		$requestData= $_REQUEST;
		$currUser = wp_get_current_user(); 
		$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';
		$userCap = service_finder_get_capability($user_id);

		$services = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->services.' WHERE `wp_user_id` = %d',$user_id));
		
		// storing  request (ie, get/post) global array to a variable  
		$requestData= $_REQUEST;
		
		
		$columns = array( 
			0 =>'service_name',
			1 =>'group_name', 
			2 => 'cost',
		);
		
		// getting total number records without any search
		$sql = $wpdb->prepare("SELECT id, service_name, cost, cost_type, group_name, status FROM ".$service_finder_Tables->services. " WHERE `wp_user_id` = %d",$user_id);
		$query=$wpdb->get_results($sql);
		$totalData = count($query);
		$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
		$sql = "SELECT id, service_name, cost, cost_type, group_name, status ";
		$sql.=" FROM ".$service_finder_Tables->services." WHERE `wp_user_id` = ".$user_id;
		if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
			$sql.=" AND ( service_name LIKE '".$requestData['search']['value']."%' ";    
			$sql.=" OR cost LIKE '".$requestData['search']['value']."%' )";
		}
		$query=$wpdb->get_results($sql);
		$totalFiltered = count($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		$query=$wpdb->get_results($sql);
		
		$data = array();
		
		foreach($query as $result){
			$nestedData=array(); 
			if($result->cost_type == 'hourly'){
				$type = '/ '.esc_html__('Hour', 'service-finder');
			}elseif($result->cost_type == 'perperson'){
				$type = '/ '.esc_html__('Person', 'service-finder');
			}elseif($result->cost_type == 'days'){
				$type = '/ '.esc_html__('Day', 'service-finder');
			}else{
				$type = '';
			}
			
			$nestedData[] = '
<div class="checkbox sf-radio-checkbox">
  <input type="checkbox" id="service-'.$result->id.'" class="deleteRow" value="'.esc_attr($result->id).'">
  <label for="service-'.$result->id.'"></label>
</div>
';
			$nestedData[] = stripcslashes($result->service_name);
			$nestedData[] = stripcslashes($result->group_name);
			$nestedData[] = service_finder_money_format($result->cost).' '.$type;
			
			if($result->status == 'active'){
			$status = 'hide';
			$text = esc_html__('Hide on Booking','service-finder');
			$class = 'btn-primary';
			$title = esc_html__('Click to show on booking','service-finder');
			}else{
			$status = 'active';
			$text = esc_html__('Show on Booking','service-finder');
			$class = 'btn-danger';
			$title = esc_html__('Click to show on booking','service-finder');
			}
			
			$actionbtn = '<button type="button" data-id="'.$result->id.'" class="btn btn-primary btn-xs editServiceButton"><i class="fa fa-pencil"></i>'.esc_html__('Edit','service-finder').'</button>&nbsp;&nbsp;&nbsp;';
			
			if(!empty($userCap)){
			if(in_array('bookings',$userCap)){	
			$actionbtn .= '<button data-id="'.esc_attr($result->id).'" data-status="'.esc_attr($status).'" title="'.esc_attr($title).'" class="btn '.sanitize_html_class($class).' btn-xs changeServiceStatus" type="button">'.esc_html($text).'</button>';
			}
			}
			
			$nestedData[] = $actionbtn;
			
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
	
	/*Delete Services*/
	public function service_finder_deleteServices(){
	global $wpdb, $service_finder_Tables;
			$data_ids = $_REQUEST['data_ids'];
			$data_id_array = explode(",", $data_ids); 
			if(!empty($data_id_array)) {
				foreach($data_id_array as $id) {
					$sql = $wpdb->prepare("DELETE FROM ".$service_finder_Tables->services." WHERE id = %d",$id);
					$query=$wpdb->query($sql);
				}
			}
	}
	
	/*Delete Group*/
	public function service_finder_deleteGroup($arg = ''){
	global $wpdb, $service_finder_Tables;
			$groupid = (!empty($arg['groupid'])) ? $arg['groupid'] : '';
			$res = $wpdb->query($wpdb->prepare("DELETE FROM ".$service_finder_Tables->service_groups." WHERE id = %d",$groupid));
			
			$data = array(
					'group_id' => 0,
					'group_name' => '',
					);
			$where = array(
					'group_id' => $groupid,
					);		

			$wpdb->update($service_finder_Tables->services,wp_unslash($data),$where);
			
			$success = array(
					'status' => 'success',
					'suc_message' => esc_html__('Delete group successfully.', 'service-finder'),
					);
			echo json_encode($success);
	}
	
	/*Change service status*/
	public function service_finder_change_service_status($arg = ''){
			global $wpdb, $service_finder_Tables;
			$currUser = wp_get_current_user();
			
			$data = array(
					'status' => esc_html($arg['status']),
					);
			$where = array(
					'id' => esc_html($arg['serviceid']),
					);		

			$wpdb->update($service_finder_Tables->services,wp_unslash($data),$where);
			
			$success = array(
					'status' => 'success',
					'suc_message' => esc_html__('Update status successfully.', 'service-finder'),
					);
			echo json_encode($success);
	}
	
	/*Add New Group*/
	public function service_finder_addGroup($arg){
			global $wpdb, $service_finder_Tables;
			
			$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';
			$group_name = (!empty($arg['group_name'])) ? $arg['group_name'] : '';
			$groupinfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->service_groups.' where provider_id = %d AND group_name = "%s"',$user_id,$group_name));
			
			if(empty($groupinfo)){
			$data = array(
					'provider_id' => $user_id,
					'group_name' => $group_name,
					);

			$wpdb->insert($service_finder_Tables->service_groups,wp_unslash($data));
			
			$group_id = $wpdb->insert_id;
			
			if ( ! $group_id ) {
				$adminemail = get_option( 'admin_email' );
				$allowedhtml = array(
					'a' => array(
						'href' => array(),
						'title' => array()
					),
				);
				$error = array(
						'status' => 'error',
						'err_message' => sprintf( wp_kses(esc_html__('Couldn&#8217;t add group... please contact the <a href="mailto:%s">Administrator</a> !', 'service-finder'),$allowedhtml), $adminemail )
						);
				echo json_encode($error);
			}else{
			
				$html = '<select class="sf-select-box form-control sf-form-control" name="group_id" data-live-search="true" title="'.esc_html__('Select Group', 'service-finder').'" id="group_id">';
                $groupinfo = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->service_groups.' where provider_id = %d ORDER BY group_name',$user_id));
					  if(!empty($groupinfo)){
					  	foreach($groupinfo as $grouprow){
							if($grouprow->id == $group_id){
								$select = 'selected="selected"';
							}else{
								$select = '';
							}
						
							$html .= '<option '.$select.' value="'.esc_attr($grouprow->id).'">'.esc_html($grouprow->group_name).'</option>';
						}
					  }
					  
                $html .= '</select>';
				
				$list = '<li>'.esc_html($group_name).' <a href="javascript:;" class="delete-group" data-id="'.esc_attr($group_id).'">&times;</a></li>';
				
				$success = array(
						'status' => 'success',
						'suc_message' => esc_html__('Add group successfully.', 'service-finder'),
						'html' => $html,
						'list' => $list,
						);
				echo json_encode($success);
			}
			}else{
				$error = array(
						'status' => 'error',
						'err_message' => esc_html__('This group name already exist. Please use another name', 'service-finder')
						);
				echo json_encode($error);
			}
		
		}
				
}