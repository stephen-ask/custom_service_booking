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

class SERVICE_FINDER_ServiceArea{


	/*Add New Region*/
	public function service_finder_addServiceRegion($arg = ''){
			global $wpdb, $service_finder_Tables;
			$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';
			
			$regions = explode(',',$arg['region']); 
			
			$wpdb->query($wpdb->prepare("DELETE FROM ".$service_finder_Tables->regions." WHERE `provider_id` = %d",$user_id));
			
			if(!empty($regions)){
				foreach($regions as $region){
						if(!$this->service_finder_check_region($region,$user_id)){
							$data = array(
									'provider_id' => esc_attr($user_id),
									'region' => esc_attr($region),
									);
				
							$wpdb->insert($service_finder_Tables->regions,wp_unslash($data));
						}
				
				}
			}
			
			$sRegions = service_finder_getAllServiceRegions($user_id);
			if(!empty($sRegions)){
				foreach($sRegions as $sRegion){
					$regionarr[] = $sRegion->region;
				}
				$sRegions = implode(',',$regionarr);
			}
			
			$success = array(
					'status' => 'success',
					'regions' => $sRegions,
					'suc_message' => esc_html__('Add region successfully.', 'service-finder'),
					);
			echo json_encode($success);
	}
	
	/*Change region status*/
	public function service_finder_change_region_status($arg = ''){
			global $wpdb, $service_finder_Tables;
			$currUser = wp_get_current_user();
			
			$data = array(
					'status' => esc_html($arg['status']),
					);
			$where = array(
					'id' => esc_html($arg['regionid']),
					);		

			$wpdb->update($service_finder_Tables->regions,wp_unslash($data),$where);
			
			$success = array(
					'status' => 'success',
					'suc_message' => esc_html__('Update status successfully.', 'service-finder'),
					);
			echo json_encode($success);
	}
	
	/*Change zipcode status*/
	public function service_finder_change_zipcode_status($arg = ''){
			global $wpdb, $service_finder_Tables;
			$currUser = wp_get_current_user();
			
			$data = array(
					'status' => esc_html($arg['status']),
					);
			$where = array(
					'id' => esc_html($arg['zipcodeid']),
					);		

			$wpdb->update($service_finder_Tables->service_area,wp_unslash($data),$where);
			
			$success = array(
					'status' => 'success',
					'suc_message' => esc_html__('Update status successfully.', 'service-finder'),
					);
			echo json_encode($success);
	}
	
	/*Get Saved Regions into datatable*/
	public function service_finder_getServicesRegion($arg){
		global $wpdb, $service_finder_Tables;
		$requestData= $_REQUEST;
		$currUser = wp_get_current_user(); 
		$columns = array( 
			0 =>'region', 
			1 =>'region', 
		);
		
		$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';
		
		// getting total number records without any search
		$sql = $wpdb->prepare("SELECT id, region, status FROM ".$service_finder_Tables->regions. " WHERE `provider_id` = %d",$user_id);
		$query=$wpdb->get_results($sql);
		$totalData = count($query);
		$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		$sql = "SELECT id, region, status";
		$sql.=" FROM ".$service_finder_Tables->regions. " WHERE `provider_id` = ".$user_id;
		if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
			$sql.=" AND ( `region` LIKE '%".$requestData['search']['value']."%' )";    
		}
		$query=$wpdb->get_results($sql);
		$totalFiltered = count($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		$query=$wpdb->get_results($sql);
		$data = array();
		
		foreach($query as $result){
			$nestedData=array(); 
		
			$nestedData[] = '
<div class="checkbox sf-radio-checkbox">
  <input type="checkbox" id="sregion-'.$result->id.'" class="deleteRegionRow" value="'.esc_attr($result->id).'">
  <label for="sregion-'.$result->id.'"></label>
</div>
';
			$nestedData[] = $result->region;
			if($result->status == 'active'){
			$status = 'block';
			$text = esc_html__('Active','service-finder');
			$class = 'btn-primary';
			}else{
			$status = 'active';
			$text = esc_html__('Blocked','service-finder');
			$class = 'btn-danger';
			}
			$nestedData[] = '<button title="Change Status" data-id="'.esc_attr($result->id).'" data-status="'.esc_attr($status).'" class="btn '.sanitize_html_class($class).' btn-xs changeRegionStatus" type="button">'.esc_html($text).'</button>';
			
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
	
	/*Delete Regions*/
	public function service_finder_deleteRegions($arg){
		global $wpdb, $service_finder_Tables;
		$currUser = wp_get_current_user(); 
			$data_ids = $_REQUEST['data_ids'];
			$data_id_array = explode(",", $data_ids); 
			if(!empty($data_id_array)) {
				foreach($data_id_array as $id) {
					$sql = $wpdb->prepare("DELETE FROM ".$service_finder_Tables->regions." WHERE id = %d",$id);
					$query=$wpdb->query($sql);
				}
			}
			
			$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';
			
			$sRegions = service_finder_getAllServiceRegions($user_id);
			if(!empty($sRegions)){
				foreach($sRegions as $sRegion){
					$regionarr[] = $sRegion->region;
				}
				$sRegions = implode(',',$regionarr);
			}
			
			$success = array(
					'regions' => $sRegions,
					);
			echo json_encode($success);
			
	}
		
	/*Add New Service Area*/
	public function service_finder_addServiceArea($arg = ''){
			global $wpdb, $service_finder_Tables;
			$currUser = wp_get_current_user();
			
			$zipcodes = explode(',',$arg['zipcode']); 
			
			$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';
			
			$wpdb->query($wpdb->prepare("DELETE FROM ".$service_finder_Tables->service_area." WHERE `provider_id` = %d",$user_id));
			
			if(!empty($zipcodes)){
				foreach($zipcodes as $zipcode){
						if(!$this->service_finder_check_zipcode($zipcode,$user_id)){
							$data = array(
									'provider_id' => esc_attr($user_id),
									'zipcode' => esc_attr($zipcode),
									);
				
							$wpdb->insert($service_finder_Tables->service_area,wp_unslash($data));
						}
				
				}
			}
			
			$members = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->team_members.' WHERE `admin_wp_id` = %d',$user_id));
			
			if(!empty($members)){
				foreach($members as $member){
					$updatedzip = '';
					$sarea = $member->service_area;
					$oldziparray = explode(',',$sarea);
					$newziparray = $zipcodes;
					if(!empty($oldziparray)){
						foreach($oldziparray as $oldzip){
							if (in_array($oldzip, $newziparray)) {
								$updatedzip .= $oldzip.',';
							}
						}
					}
				
				$wpdb->query($wpdb->prepare('UPDATE '.$service_finder_Tables->team_members.' SET `service_area` = "%s" WHERE `id` = %d',rtrim($updatedzip,','),$member->id));
				}
			}
			
			
			$zipcode_id = $wpdb->insert_id;
			
			$sAreas = service_finder_getServiceArea($user_id);
			if(!empty($sAreas)){
				foreach($sAreas as $sArea){
					$ziparr[] = $sArea->zipcode;
				}
				$sAreas = implode(',',$ziparr);
			}
			
			
			if ( ! $zipcode_id ) {
				$adminemail = get_option( 'admin_email' );
				$allowedhtml = array(
					'a' => array(
						'href' => array(),
						'title' => array()
					),
				);
				$error = array(
						'status' => 'error',
						'err_message' => sprintf( wp_kses(esc_html__('Couldn&#8217;t add zipcode... please contact the <a href="mailto:%s">Administrator</a> !', 'service-finder'),$allowedhtml), $adminemail )
						);
				echo json_encode($error);
			}else{
				$success = array(
						'status' => 'success',
						'zipcodes' => $sAreas,
						'suc_message' => esc_html__('Add servicearea successfully.', 'service-finder'),
						);
				echo json_encode($success);
			}
		
		}
		
	/*Get Saved Zipcodes into datatable*/
	public function service_finder_getServicesArea($arg){
		global $wpdb, $service_finder_Tables;
		$requestData= $_REQUEST;
		$currUser = wp_get_current_user(); 
		$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';
		$zipcodes = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->service_area.' WHERE `provider_id` = %d',$user_id));
		
		$columns = array( 
			0 =>'Zipcode', 
			1 =>'Zipcode', 
			2=> 'status'
		);
		
		// getting total number records without any search
		$sql = $wpdb->prepare("SELECT id, zipcode, status FROM ".$service_finder_Tables->service_area. " WHERE `provider_id` = %d",$user_id);
		$query=$wpdb->get_results($sql);
		$totalData = count($query);
		$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
		$sql = "SELECT id, zipcode, status";
		$sql.=" FROM ".$service_finder_Tables->service_area. " WHERE `provider_id` = ".$user_id;
		if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
			$sql.=" AND ( zipcode LIKE '".$requestData['search']['value']."%' ";    
			$sql.=" OR status LIKE '".$requestData['search']['value']."%' )";
		}
		$query=$wpdb->get_results($sql);
		$totalFiltered = count($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		$query=$wpdb->get_results($sql);
		
		$data = array();
		
		foreach($query as $result){
			$nestedData=array(); 
		
			$nestedData[] = '
<div class="checkbox sf-radio-checkbox">
  <input type="checkbox" id="sarea-'.$result->id.'" class="deleteZipcodeRow" value="'.esc_attr($result->id).'">
  <label for="sarea-'.$result->id.'"></label>
</div>
';
			$nestedData[] = $result->zipcode;
			if($result->status == 'active'){
			$status = 'block';
			$text = esc_html__('Active','service-finder');
			$class = 'btn-primary';
			}else{
			$status = 'active';
			$text = esc_html__('Blocked','service-finder');
			$class = 'btn-danger';
			}
			$nestedData[] = '<button title="Change Status" data-id="'.esc_attr($result->id).'" data-status="'.esc_attr($status).'" class="btn '.sanitize_html_class($class).' btn-xs changeZipcodeStatus" type="button">'.esc_html($text).'</button>';
			
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
	
	/*Delete Zipcodes*/
	public function service_finder_deleteZipcodes($arg){
		global $wpdb, $service_finder_Tables;
		$currUser = wp_get_current_user(); 
			$data_ids = $_REQUEST['data_ids'];
			$data_id_array = explode(",", $data_ids); 
			if(!empty($data_id_array)) {
				foreach($data_id_array as $id) {
					$sql = $wpdb->prepare("DELETE FROM ".$service_finder_Tables->service_area." WHERE id = %d",$id);
					$query=$wpdb->query($sql);
				}
			}
			
			$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';
			$sAreas = service_finder_getServiceArea($user_id);
			if(!empty($sAreas)){
				foreach($sAreas as $sArea){
					$ziparr[] = $sArea->zipcode;
				}
				$sAreas = implode(',',$ziparr);
			}
			
			
			$success = array(
						'zipcodes' => $sAreas,
						);
			echo json_encode($success);
	}

	/*Reload Zipcodes*/
	public function service_finder_reloadZipcodes($arg){
		global $wpdb, $service_finder_Tables;
		$currUser = wp_get_current_user(); 
			$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';
			$sAreas = service_finder_getServiceArea($user_id);
			if(!empty($sAreas)){
				foreach($sAreas as $sArea){
					$ziparr[] = $sArea->zipcode;
				}
				$sAreas = implode(',',$ziparr);
			}
			
			
			$success = array(
						'zipcodes' => $sAreas,
						);
			echo json_encode($success);
	}

	
	/*Check If zipcode exist already*/
	function service_finder_check_zipcode($zipcode,$uid){
		global $wpdb, $service_finder_Tables;
		$res = $wpdb->get_row($wpdb->prepare('SELECT * from '.$service_finder_Tables->service_area.' where `zipcode` = "%s" AND `provider_id` = %d',$zipcode,$uid));
		return (!empty($res)) ? true : false;
	}
	
	/*Check If region exist already*/
	function service_finder_check_region($region,$uid){
		global $wpdb, $service_finder_Tables;
		$res = $wpdb->get_row($wpdb->prepare('SELECT * from '.$service_finder_Tables->regions.' where `region` = "%s" AND `provider_id` = %d',$region,$uid));
		return (!empty($res)) ? true : false;
	}
				
}