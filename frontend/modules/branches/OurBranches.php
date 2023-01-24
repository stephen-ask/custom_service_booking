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

class SERVICE_FINDER_OurBranches{

	/*Add New Branch*/
	public function service_finder_addBranches($arg){
			global $wpdb, $service_finder_Tables;
			
			$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';
			
			$signup_address = (isset($arg['signup_address'])) ? $arg['signup_address'] : '';
			$signup_city = (isset($arg['signup_city'])) ? esc_html($arg['signup_city']) : '';
			$signup_country = (isset($arg['signup_country'])) ? esc_html($arg['signup_country']) : '';
			
			$full_address = $signup_address.' '.$signup_city.' '.$signup_country;
			
			$address = str_replace(" ","+",$full_address);
			$res = service_finder_getLatLong($address);
			$lat = $res['lat'];
			$lng = $res['lng'];
			
			$data = array(
						'wp_user_id' => $user_id,
						'address' => (!empty($arg['signup_address'])) ? esc_attr($arg['signup_address']) : '',
						'apt' => (!empty($arg['signup_apt'])) ? esc_attr($arg['signup_apt']) : '',
						'city' => (!empty($arg['signup_city'])) ? esc_attr($arg['signup_city']) : '',
						'state' => (!empty($arg['signup_state'])) ? esc_attr($arg['signup_state']) : '',
						'zipcode' => (!empty($arg['signup_zipcode'])) ? esc_attr($arg['signup_zipcode']) : '',
						'country' => (!empty($arg['signup_country'])) ? esc_attr($arg['signup_country']) : '',
						'lat' => $lat,
						'long' => $lng,
						'zoomlevel' => 14,
					);
	
			$wpdb->insert($service_finder_Tables->branches,wp_unslash($data));
			
			$branch_id = $wpdb->insert_id;
			
			if ( ! $branch_id ) {
				$adminemail = get_option( 'admin_email' );
				$allowedhtml = array(
					'a' => array(
						'href' => array(),
						'title' => array()
					),
				);
				$error = array(
						'status' => 'error',
						'err_message' => sprintf( wp_kses(esc_html__('Couldn&#8217;t add branch... please contact the <a href="mailto:%s">Administrator</a> !', 'service-finder'),$allowedhtml), $adminemail )
						);
				echo json_encode($error);
			}else{
				$success = array(
						'status' => 'success',
						'suc_message' => esc_html__('Branch added successfully.', 'service-finder'),
						);
				echo json_encode($success);
			}
		
		}
		
	/*Update Marker*/
	public function service_finder_updateMarker($arg){
			global $wpdb, $service_finder_Tables;
			
			$branchlat = (isset($arg['branchlat'])) ? $arg['branchlat'] : '';
			$branchlng = (isset($arg['branchlng'])) ? esc_html($arg['branchlng']) : '';
			$branchzoomlevel = (isset($arg['branchzoomlevel'])) ? esc_html($arg['branchzoomlevel']) : '';
			$branchid = (isset($arg['branchid'])) ? esc_html($arg['branchid']) : '';
			
			$data = array(

						'lat' => $branchlat,
						
						'long' => $branchlng,
						
						'zoomlevel' => $branchzoomlevel,
	
					);
					
			$where = array(

						'id' => $branchid,
						
					);		
	
			$branch_id = $wpdb->update($service_finder_Tables->branches,wp_unslash($data),$where);
			
			if(is_wp_error($branch_id)){
				$adminemail = get_option( 'admin_email' );
				$error = array(
						'status' => 'error',
						'err_message' => $branch_id->get_error_message()
						);
				echo json_encode($error);
			}else{
				$success = array(
						'status' => 'success',
						'suc_message' => esc_html__('Branch marker updated successfully.', 'service-finder'),
						);
				echo json_encode($success);
			}
		
		}
		
		/*Get Saved Branches into datatable*/
		public function service_finder_getBranches($arg){
			global $wpdb, $service_finder_Tables;
			$requestData= $_REQUEST;
			$currUser = wp_get_current_user(); 
			$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';
	
			$branches = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->branches.' WHERE `wp_user_id` = %d',$user_id));
			
			// storing  request (ie, get/post) global array to a variable  
			$requestData= $_REQUEST;
			
			
			$columns = array( 
				0 =>'city',
				1 =>'address', 
				2 => 'apt',
				3 => 'city',
				4 => 'state',
				5 => 'country',
				6 => 'zipcode',
			);
			
			// getting total number records without any search
			$sql = $wpdb->prepare("SELECT * FROM ".$service_finder_Tables->branches. " WHERE `wp_user_id` = %d",$user_id);
			$query=$wpdb->get_results($sql);
			$totalData = count($query);
			$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
			
			
			$sql = "SELECT * FROM ".$service_finder_Tables->branches." WHERE `wp_user_id` = ".$user_id;
			if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
				$sql.=" AND ( address LIKE '".$requestData['search']['value']."%' ";    
				$sql.=" OR apt LIKE '".$requestData['search']['value']."%'";
				$sql.=" OR city LIKE '".$requestData['search']['value']."%'";
				$sql.=" OR state LIKE '".$requestData['search']['value']."%'";
				$sql.=" OR country LIKE '".$requestData['search']['value']."%'";
				$sql.=" OR zipcode LIKE '".$requestData['search']['value']."%' )";
			}
			$query=$wpdb->get_results($sql);
			$totalFiltered = count($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
			$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length'];
			$query=$wpdb->get_results($sql);
			
			$data = array();
			
			foreach($query as $result){
				$nestedData=array(); 
				
				$nestedData[] = '<div class="checkbox sf-radio-checkbox">
				  <input type="checkbox" id="branch-'.$result->id.'" class="deleteBranchRow" value="'.esc_attr($result->id).'">
				  <label for="branch-'.$result->id.'"></label>
				</div>';
				$nestedData[] = stripcslashes($result->address);
				$nestedData[] = stripcslashes($result->apt);
				$nestedData[] = stripcslashes(service_finder_get_cityname_by_slug($result->city));
				$nestedData[] = stripcslashes($result->state);
				$nestedData[] = stripcslashes($result->country);
				$nestedData[] = stripcslashes($result->zipcode);
				
				$nestedData[] = '<button type="button" data-branchzoomlevel="'.$result->zoomlevel.'" data-id="'.$result->id.'" class="btn btn-primary btn-xs setmarker"><i class="fa fa-map-marker"></i>'.esc_html__('Set Marker','service-finder').'</button>';
				
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
		public function service_finder_deleteBranches(){
		global $wpdb, $service_finder_Tables;
				$data_ids = $_REQUEST['data_ids'];
				$data_id_array = explode(",", $data_ids); 
				if(!empty($data_id_array)) {
					foreach($data_id_array as $id) {
						$sql = $wpdb->prepare("DELETE FROM ".$service_finder_Tables->branches." WHERE id = %d",$id);
						$query=$wpdb->query($sql);
					}
				}
		}
		
}