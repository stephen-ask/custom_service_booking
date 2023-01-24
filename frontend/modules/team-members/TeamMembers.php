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

class SERVICE_FINDER_TeamMembers{

	/*Add New Member*/
	public function service_finder_addMembers($arg){
			global $wpdb, $service_finder_Tables;
			
			$currUser = wp_get_current_user(); 
			$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';
			$getsarea = (!empty($arg['sarea'])) ? $arg['sarea'] : '';
			if(!empty($getsarea)){
			$sarea = implode(',',$getsarea);
			}else{
			$sarea = '';
			}
			
			$getregion = (!empty($arg['region'])) ? $arg['region'] : '';
			if(!empty($getregion)){
			$regions = implode('%%%',$getregion);
			}else{
			$regions = '';
			}
			
			$getservice = (!empty($arg['service'])) ? $arg['service'] : '';
			if(!empty($getservice)){
			$services = implode(',',$getservice);
			}else{
			$services = '';
			}
			
			$data = array(
					'avatar_id' => (!empty($arg['sfmemberavatar'])) ? esc_attr($arg['sfmemberavatar']) : '',
					'member_name' => (!empty($arg['member_fullname'])) ? esc_attr($arg['member_fullname']) : '',
					'email' => (!empty($arg['member_email'])) ? esc_attr($arg['member_email']) : '',
					'phone' => (!empty($arg['member_phone'])) ? esc_attr($arg['member_phone']) : '',
					'service_area' => (!empty($sarea)) ? esc_attr($sarea) : '',
					'regions' => (!empty($regions)) ? esc_attr($regions) : '',
					'services' => (!empty($services)) ? esc_attr($services) : '',
					'admin_wp_id' => esc_attr($user_id),
					'is_admin' => 'no',
					);

			$wpdb->insert($service_finder_Tables->team_members,wp_unslash($data));
			
			$member_id = $wpdb->insert_id;
			
			if(service_finder_availability_method($user_id) == 'timeslots'){
				$this->service_finder_set_members_default_slots($user_id,$member_id);
			}elseif(service_finder_availability_method($user_id) == 'starttime'){
				//$this->service_finder_set_members_default_startitme_slots($user_id,$member_id);
			}else{
				$this->service_finder_set_members_default_slots($user_id,$member_id);
			}
			
			if ( ! $member_id ) {
				$adminemail = get_option( 'admin_email' );
				$allowedhtml = array(
					'a' => array(
						'href' => array(),
						'title' => array()
					),
				);
				$error = array(
						'status' => 'error',
						'err_message' => sprintf( wp_kses(esc_html__('Couldn&#8217;t add member... please contact the <a href="mailto:%s">Administrator</a> !', 'service-finder'),$allowedhtml), $adminemail )
						);
				echo json_encode($error);
			}else{
				$success = array(
						'status' => 'success',
						'suc_message' => esc_html__('Add member successfully.', 'service-finder'),
						'memberid' => $member_id,
						);
				echo json_encode($success);
			}
		
		}
		
	/*Edit Member*/
	public function service_finder_editMember($arg){
			global $wpdb, $service_finder_Tables;
			
			$currUser = wp_get_current_user();
			if(!empty($arg['sarea'])){
			$sarea = implode(',',$arg['sarea']);
			}else{
			$sarea = '';
			}
			
			if(!empty($arg['region'])){
			$regions = implode('%%%',$arg['region']);
			}else{
			$regions = '';
			}
			
			$sfmemberavatar = (isset($arg['sfmemberavatar'])) ? $arg['sfmemberavatar'] : '';
			$sfmemberavataredit = (isset($arg['sfmemberavataredit'])) ? $arg['sfmemberavataredit'] : '';
			if($sfmemberavatar > 0){
			$avtid = $sfmemberavatar;
			}else{
			$avtid = $sfmemberavataredit;
			}
			
			$getservice = (!empty($arg['service'])) ? $arg['service'] : '';
			if(!empty($getservice)){
			$services = implode(',',$getservice);
			}else{
			$services = '';
			}
			
			$data = array(
					'avatar_id' => $avtid,
					'member_name' => (!empty($arg['member_fullname'])) ? esc_attr($arg['member_fullname']) : '',
					'email' => (!empty($arg['member_email'])) ? esc_attr($arg['member_email']) : '',
					'phone' => (!empty($arg['member_phone'])) ? esc_attr($arg['member_phone']) : '',
					'service_area' => esc_attr($sarea),
					'regions' => esc_attr($regions),
					'services' => esc_attr($services),
					);
			
			$where = array(
						'id' => $arg['memberid'],
						);
			$member_id = $wpdb->update($service_finder_Tables->team_members,wp_unslash($data),$where);		
			
			if(is_wp_error($member_id)){
				$adminemail = get_option( 'admin_email' );
				$allowedhtml = array(
					'a' => array(
						'href' => array(),
						'title' => array()
					),
				);
				$error = array(
						'status' => 'error',
						'data' => $data,
						'where' => $where,
						'member_id' => $member_id->get_error_message(),
						'dbtable' => $service_finder_Tables->team_members,
						'err_message' => sprintf( wp_kses(esc_html__('Couldn&#8217;t edit member... please contact the <a href="mailto:%s">Administrator</a> !', 'service-finder'),$allowedhtml), $adminemail )
						);
				echo json_encode($error);
			}else{
				$success = array(
						'status' => 'success',
						'suc_message' => esc_html__('Edit member successfully.', 'service-finder'),
						'memberid' => $arg['memberid'],
						);
				echo json_encode($success);
			}
		
		}	
		
	/*Get Saved Members into datatable*/
	public function service_finder_getMembers($arg){
		global $wpdb, $service_finder_Tables;
		$requestData= $_REQUEST;
		$currUser = wp_get_current_user(); 
		$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';
		$members = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->team_members.' WHERE `admin_wp_id` = %d',$user_id));
		
		// storing  request (ie, get/post) global array to a variable  
		$requestData= $_REQUEST;
		
		
		$columns = array( 
			0 =>'member_name', 
			1 =>'member_name', 
			2=> 'phone',
			3 => 'email',
			4=> 'is_admin'
		);
		
		// getting total number records without any search
		$sql = $wpdb->prepare("SELECT id, member_name, phone, email, is_admin FROM ".$service_finder_Tables->team_members. " WHERE `admin_wp_id` = %d",$user_id);
		$query=$wpdb->get_results($sql);
		$totalData = count($query);
		$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
		$sql = "SELECT id, member_name, phone, email, is_admin";
		$sql.=" FROM ".$service_finder_Tables->team_members." WHERE `admin_wp_id` = ".$user_id;
		if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
			$sql.=" AND ( member_name LIKE '".$requestData['search']['value']."%' ";    
			$sql.=" OR phone LIKE '".$requestData['search']['value']."%' ";
			$sql.=" OR email LIKE '".$requestData['search']['value']."%' )";
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
  <input type="checkbox" id="member-'.$result->id.'" class="deleteMemberRow" value="'.esc_attr($result->id).'">
  <label for="member-'.$result->id.'"></label>
</div>
';
			$nestedData[] = $result->member_name;
			$nestedData[] = $result->phone;
			$nestedData[] = $result->email;
			if($result->is_admin == 'yes'){
			$nestedData[] = esc_html__( 'yes', 'service-finder' ) ;
			$nestedData[] = '';
			}else{
			$nestedData[] = esc_html__( 'no', 'service-finder' ) ;
			
			if(service_finder_availability_method($user_id) == 'timeslots'){
				$slotclass = 'setslots';
			}elseif(service_finder_availability_method($user_id) == 'starttime'){
				$slotclass = 'setstarttime';
			}
			
			$nestedData[] = '<button type="button" data-id="'.esc_attr($result->id).'" class="btn btn-primary btn-xs editMemberButton"><i class="fa fa-pencil"></i>'.esc_html__('Edit','service-finder').'</button> <button type="button" data-id="'.esc_attr($result->id).'" class="btn btn-primary btn-xs '.sanitize_html_class($slotclass).'"><i class="fa fa-pencil"></i>'.esc_html__('Set Timeslot','service-finder').'</button>';
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
	
	/*Load member for edit*/
	public function service_finder_loadMembers($arg){
			global $wpdb, $service_finder_Tables;
			$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';		
			$member = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->team_members.' WHERE `id` = %d',$arg['memberid']));
			$avatar_id = '';
			$html = '';
			if(!empty($member)){
			
			if($member->is_admin == 'yes' && $member->avatar_id == 0){
				$avatar_id = service_finder_getUserAvatarID($member->admin_wp_id);
				$src  = wp_get_attachment_image_src( $avatar_id, 'thumbnail' );
					$src  = $src[0];
					$i18n_delete = apply_filters( 'rwmb_image_delete_string', _x( 'Delete', 'image upload', 'service-finder' ) );
					
					$html = sprintf('
<li id="item_%s"> <img src="%s" />
  <div class="rwmb-image-bar"> <a title="%s" class="rwmb-delete-file" href="javascript:;" data-attachment_id="%s">&times;</a>
    <input type="hidden" name="sfmemberavatar" value="%s">
  </div>
</li>
',
				esc_attr($avatar_id),
				esc_url($src),
				esc_attr($i18n_delete), esc_attr($avatar_id),
				esc_attr($avatar_id)
				);
			}else{
				if(!empty($member->avatar_id) && $member->avatar_id > 0){
					$src  = wp_get_attachment_image_src( $member->avatar_id, 'service_finder-provider-thumb' );
					$src  = $src[0];
					$i18n_delete = apply_filters( 'rwmb_image_delete_string', _x( 'Delete', 'image upload', 'service-finder' ) );
					
					$html = sprintf('
<li id="item_%s"> <img src="%s" />
  <div class="rwmb-image-bar"> <a title="%s" class="rwmb-delete-file" href="javascript:;" data-attachment_id="%s">&times;</a>
    <input type="hidden" name="sfmemberavatar" value="%s">
  </div>
</li>
',
				esc_attr($member->avatar_id),
				esc_url($src),
				esc_attr($i18n_delete), esc_attr($member->avatar_id),
				esc_attr($member->avatar_id)
				);
				}
			}	
			
$current_user = wp_get_current_user(); 
$newzipcodes = '';
$sAreas = service_finder_getServiceArea($user_id);
if(!empty($sAreas)){
	foreach($sAreas as $sArea){
		$newzipcodes .= '<div class="col-lg-3">
						  <div class="checkbox sf-radio-checkbox">
							<input id="sf-'.esc_attr($sArea->zipcode).'" type="checkbox" name="sarea[]" value="'.esc_attr($sArea->zipcode).'">
							<label for="sf-'.esc_attr($sArea->zipcode).'">'.esc_html($sArea->zipcode).'</label>
						  </div>
						</div>
						';	
	}
}

$regions = service_finder_getServiceRegions($user_id);
$newregions = '';
if(!empty($regions)){
	foreach($regions as $region){
		$newregions .= '<div class="col-lg-3">
						  <div class="checkbox sf-radio-checkbox">
							<input id="sf-'.esc_attr($region->region).'" type="checkbox" name="region[]" value="'.esc_attr($region->region).'">
							<label for="sf-'.esc_attr($region->region).'">'.esc_html($region->region).'</label>
						  </div>
						</div>
						';	
	}
}

$services = service_finder_getAllServices($user_id);
$newservices = '';
if(!empty($services)){
	foreach($services as $service){
		$servicearr = explode(',',$member->services);
		if(in_array($service->id,$servicearr)){
			$checked = 'checked="checked"';
		}else{
			$checked = '';
		}
		$newservices .= '<div class="col-lg-3">
						  <div class="checkbox sf-radio-checkbox">
							<input '.$checked.' id="sf-'.esc_attr($service->id).'" type="checkbox" name="service[]" value="'.esc_attr($service->id).'">
							<label for="sf-'.esc_attr($service->id).'">'.esc_html($service->service_name).'</label>
						  </div>
						</div>
						';	
	}
}						

$result = array(
		'member_fullname' => $member->member_name,
		'member_email' => $member->email,
		'member_phone' => $member->phone,
		'avatar' => $html,
		'avatar_id' => $member->avatar_id,
		'service_area' => $member->service_area,
		'admin_avatar_id' => $avatar_id,
		'newzipcodes' => $newzipcodes,
		'newregions' => $newregions,
		'newservices' => $newservices,
		'selected_regions' => $member->regions,
);

}
echo json_encode($result);
	}
	
	/*Load member slots*/
	public function service_finder_load_member_slots($arg){
			global $wpdb, $service_finder_Tables;
			$user_id = (!empty($arg['user_id'])) ? esc_attr($arg['user_id']) : '';
			$memberid = (!empty($arg['memberid'])) ? esc_attr($arg['memberid']) : '';
			
			if(service_finder_availability_method($user_id) == 'timeslots'){
				$this->service_finder_get_member_timeslots($user_id,$memberid);
			}elseif(service_finder_availability_method($user_id) == 'starttime'){
				$this->service_finder_get_member_starttime($user_id,$memberid);
			}else{
				$this->service_finder_get_member_timeslots($user_id,$memberid);
			}
					
	}
	
	public function service_finder_get_member_timeslots($user_id,$memberid){
	global $wpdb, $service_finder_Tables, $service_finder_options;
	
	$time_format = (!empty($service_finder_options['time-format'])) ? $service_finder_options['time-format'] : '';
	
	$days = array('monday','tuesday','wednesday','thursday','friday','saturday','sunday'); 
	if(!empty($days)){
		foreach($days as $day){
			$member_timeslots = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->member_timeslots.' where day = %s and provider_id = %d AND `member_id` = %d',$day,$user_id,$memberid));
			$starttime = array();
			$endtime = array();
			
			if(!empty($member_timeslots)){
				foreach($member_timeslots as $member_timeslot){
					$starttime[] = $member_timeslot->start_time;
					$endtime[] = $member_timeslot->end_time;
				}
			}
		
			$li = '';
			$timeslots = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->timeslots.' where day = %s and provider_id = %d',$day,$user_id));
			if(!empty($timeslots)){
				foreach($timeslots as $timeslot){
					if($time_format){
						$showtime = $timeslot->start_time.'-'.$timeslot->end_time;
					}else{
						$showtime = date('h:i a',strtotime($timeslot->start_time)).'-'.date('h:i a',strtotime($timeslot->end_time));
					}
					
					if(!empty($starttime) && !empty($endtime)){
						if(in_array($timeslot->start_time,$starttime) && in_array($timeslot->end_time,$endtime)){
						$active = 'class="active"';
						}else{
						$active = '';
						}
					}
					
					$li .= '<li '.$active.' data-source="'.esc_attr($timeslot->start_time).'-'.esc_attr($timeslot->end_time).'"><span>'.$showtime.'</span></li>';
				}
			}
			if($li == ""){
				$li .= '<div class="alert alert-info">'.esc_html__('No slots available', 'service-finder').'</div>';
			}
			$daysli[$day] = $li;
		}
	}	
	echo json_encode($daysli);
	}
	
	public function service_finder_get_member_starttime($user_id,$memberid){
		
	global $wpdb, $service_finder_Tables, $service_finder_options;
	
	$time_format = (!empty($service_finder_options['time-format'])) ? $service_finder_options['time-format'] : '';
	$daysli = array();
	$starttime = array();
	$endtime = array();
	$breakstarttime = array();
	$breakendtime = array();
	$days = array('monday','tuesday','wednesday','thursday','friday','saturday','sunday'); 
	if(!empty($days)){
		foreach($days as $day){
			$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->member_starttimes.' where day = %s and provider_id = %d AND `member_id` = %d',$day,$user_id,$memberid));
			
			$li = '';
			if(!empty($row)){
				if($row->break_start_time != '' || $row->break_start_time != NULL){
				if($time_format){
					$showtime = $row->break_start_time.' - '.$row->break_end_time;
				}else{
					$showtime = date('h:i a',strtotime($row->break_start_time)).' - '.date('h:i a',strtotime($row->break_end_time));
				}
			
				$li .= '<li data-memberid="'.esc_attr($memberid).'" data-weekday="'.esc_attr($day).'" data-source="'.esc_attr($row->break_start_time 	).'-'.esc_attr($row->break_end_time).'"><span>'.$showtime.'</span> <span class="member-breaktime-remove"><i class="fa fa-close"></i></span></li>';
				}
			}
			
			$daysli[$day] = $li;
			$starttime[$day] = (!empty($row->start_time)) ? date('H:i',strtotime($row->start_time)) : '';
			$endtime[$day] = (!empty($row->end_time)) ? date('H:i',strtotime($row->end_time)) : '';
			$breakstarttime[$day] = (!empty($row->break_start_time)) ? date('H:i',strtotime($row->break_start_time)) : '';
			$breakendtime[$day] = (!empty($row->break_end_time)) ? date('H:i',strtotime($row->break_end_time)) : '';
		}
	}	
	$success = array(
			'breaktime' => $daysli,
			'starttime' => $starttime,
			'endtime' => $endtime,
			'breakstarttime' => $breakstarttime,
			'breakendtime' => $breakendtime,
			);
	echo json_encode($success);
	
	}
	
	public function service_finder_set_members_default_startitme_slots($user_id,$member_id){
	global $wpdb, $service_finder_Tables, $service_finder_options;
		$days = array('monday','tuesday','wednesday','thursday','friday','saturday','sunday'); 
		if(!empty($days)){
			foreach($days as $day){
			$showtime = array();
				$timeslots = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->starttime.' where day = %s and provider_id = %d',$day,$user_id));
				if(!empty($timeslots)){
					foreach($timeslots as $timeslot){
					$showtime[] = $timeslot->start_time;
					}
				}	
				if(!empty($showtime)){
				//$this->service_finder_update_member_starttime_table($user_id,$member_id,$day,$showtime);
				}
			}
		}
	}
	
	public function service_finder_set_members_default_slots($user_id,$member_id){
	global $wpdb, $service_finder_Tables, $service_finder_options;
		$days = array('monday','tuesday','wednesday','thursday','friday','saturday','sunday'); 
		if(!empty($days)){
			foreach($days as $day){
			$showtime = array();
				$timeslots = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->timeslots.' where day = %s and provider_id = %d',$day,$user_id));
				if(!empty($timeslots)){
					foreach($timeslots as $timeslot){
					$showtime[] = $timeslot->start_time.'-'.$timeslot->end_time;
					}
				}	
				if(!empty($showtime)){
				$this->service_finder_update_member_table($user_id,$member_id,$day,$showtime);
				}
			}
		}
	}
	
	/*upadte member timeslot*/
	public function service_finder_update_member_slots($arg){
	global $wpdb, $service_finder_Tables, $service_finder_options;
	
	$providerid = (!empty($arg['user_id'])) ? esc_attr($arg['user_id']) : '';
	$memberid = (!empty($arg['memberid'])) ? esc_attr($arg['memberid']) : '';
	
	$days = array('monday','tuesday','wednesday','thursday','friday','saturday','sunday'); 
	if(!empty($days)){
		foreach($days as $day){
			$wpdb->query($wpdb->prepare('DELETE FROM `'.$service_finder_Tables->member_timeslots.'` WHERE `day` = %s AND `provider_id` = %d AND `member_id` = %d',$day,$providerid,$memberid));
			switch($day){
				case 'monday':
						if(!empty($arg['monarr'])){
							$this->service_finder_update_member_table($providerid,$memberid,$day,$arg['monarr']);
						}	
						break;
				case 'tuesday':
						if(!empty($arg['tuearr'])){
							$this->service_finder_update_member_table($providerid,$memberid,$day,$arg['tuearr']);
						}	
						break;
				case 'wednesday':
						if(!empty($arg['wedarr'])){
							$this->service_finder_update_member_table($providerid,$memberid,$day,$arg['wedarr']);
						}	
						break;
				case 'thursday':
						if(!empty($arg['thurarr'])){
							$this->service_finder_update_member_table($providerid,$memberid,$day,$arg['thurarr']);
						}	
						break;
				case 'friday':
						if(!empty($arg['friarr'])){
							$this->service_finder_update_member_table($providerid,$memberid,$day,$arg['friarr']);
						}	
						break;
				case 'saturday':
						if(!empty($arg['satarr'])){
							$this->service_finder_update_member_table($providerid,$memberid,$day,$arg['satarr']);
						}	
						break;
				case 'sunday':
						if(!empty($arg['sunarr'])){
							$this->service_finder_update_member_table($providerid,$memberid,$day,$arg['sunarr']);
						}	
						break;												
				default:		
						break;
			}
			
		}
	}	
	
	$success = array(
			'status' => 'success',
			'suc_message' => esc_html__('Member timeslots updated successfully.', 'service-finder'),
			);
	echo json_encode($success);
	
	}
	
	/*upadte member starttime*/
	public function service_finder_update_member_starttime($arg){
	global $wpdb, $service_finder_Tables, $service_finder_options;
	
	$providerid = (!empty($arg['user_id'])) ? esc_attr($arg['user_id']) : '';
	$memberid = (!empty($arg['memberid'])) ? esc_attr($arg['memberid']) : '';
	
	$days = array('monday','tuesday','wednesday','thursday','friday','saturday','sunday'); 
	if(!empty($days)){
		foreach($days as $day){
			$wpdb->query($wpdb->prepare('DELETE FROM `'.$service_finder_Tables->member_starttimes.'` WHERE `day` = %s AND `provider_id` = %d AND `member_id` = %d',$day,$providerid,$memberid));
			
			$slotstarttime = (!empty($arg['memberstarttime-'.$day])) ? esc_attr($arg['memberstarttime-'.$day]) : '';
			$slotendtime = (!empty($arg['memberendtime-'.$day])) ? esc_attr($arg['memberendtime-'.$day]) : '';
			
			$slotstime = $slotstarttime.'-'.$slotendtime;
			
			switch($day){
				case 'monday':
						if(!empty($arg['monarr']) && $slotstarttime != ''){
							$this->service_finder_update_member_starttime_table($providerid,$memberid,$day,$slotstime,$arg['monarr']);
						}	
						break;
				case 'tuesday':
						if(!empty($arg['tuearr']) && $slotstarttime != ''){
							$this->service_finder_update_member_starttime_table($providerid,$memberid,$day,$slotstime,$arg['tuearr']);
						}	
						break;
				case 'wednesday':
						if(!empty($arg['wedarr']) && $slotstarttime != ''){
							$this->service_finder_update_member_starttime_table($providerid,$memberid,$day,$slotstime,$arg['wedarr']);
						}	
						break;
				case 'thursday':
						if(!empty($arg['thurarr']) && $slotstarttime != ''){
							$this->service_finder_update_member_starttime_table($providerid,$memberid,$day,$slotstime,$arg['thurarr']);
						}	
						break;
				case 'friday':
						if(!empty($arg['friarr']) && $slotstarttime != ''){
							$this->service_finder_update_member_starttime_table($providerid,$memberid,$day,$slotstime,$arg['friarr']);
						}	
						break;
				case 'saturday':
						if(!empty($arg['satarr']) && $slotstarttime != ''){
							$this->service_finder_update_member_starttime_table($providerid,$memberid,$day,$slotstime,$arg['satarr']);
						}	
						break;
				case 'sunday':
						if(!empty($arg['sunarr']) && $slotstarttime != ''){
							$this->service_finder_update_member_starttime_table($providerid,$memberid,$day,$slotstime,$arg['sunarr']);
						}	
						break;												
				default:		
						break;
			}
			
		}
	}	
	
	$success = array(
			'status' => 'success',
			'suc_message' => esc_html__('Member starttime updated successfully.', 'service-finder'),
			);
	echo json_encode($success);
	
	}
	
	public function service_finder_update_member_table($providerid,$memberid,$day,$slotarr){
		global $wpdb, $service_finder_Tables, $service_finder_options;
		if(!empty($slotarr)){
			foreach($slotarr as $slot){
				$time = explode('-',$slot);
				$dataset = array(
					'provider_id' => $providerid,
					'member_id' => $memberid,
					'day' => $day,
					'start_time' => $time[0],
					'end_time' => $time[1],
					);
				$wpdb->insert($service_finder_Tables->member_timeslots,wp_unslash($dataset));					
			}
		}
	}
	
	public function service_finder_update_member_starttime_table($providerid,$memberid,$day,$slotstime,$breakslotarr){
		global $wpdb, $service_finder_Tables, $service_finder_options;
		if(!empty($slotstime)){
			$workingtime = explode('-',$slotstime);
			
			$breaktime = array();
			if(!empty($breakslotarr) && $breakslotarr[0] != '-'){
			$breaktime = explode('-',$breakslotarr[0]);
			}
			
			$dataset = array(
				'provider_id' => $providerid,
				'member_id' => $memberid,
				'day' => $day,
				'start_time' => (!empty($workingtime[0])) ? $workingtime[0] : '',
				'end_time' => (!empty($workingtime[1])) ? $workingtime[1] : '',
				'break_start_time' => (!empty($breaktime)) ? $breaktime[0] : NULL,
				'break_end_time' => (!empty($breaktime)) ? $breaktime[1] : NULL,
				);
			$wpdb->insert($service_finder_Tables->member_starttimes,wp_unslash($dataset));
		}
	}
	
	/*public function service_finder_update_member_starttime_table($providerid,$memberid,$day,$starttimearr){
		global $wpdb, $service_finder_Tables, $service_finder_options;
		if(!empty($starttimearr)){
			foreach($starttimearr as $starttime){
				$dataset = array(
					'provider_id' => $providerid,
					'member_id' => $memberid,
					'day' => $day,
					'start_time' => $starttime
					);
				$wpdb->insert($service_finder_Tables->member_starttimes,wp_unslash($dataset));					
			}
		}
	}*/
	
	/*Delete Members*/
	public function service_finder_deleteMembers(){
	global $wpdb, $service_finder_Tables;
			$data_ids = $_REQUEST['data_ids'];
			$data_id_array = explode(",", $data_ids); 
			if(!empty($data_id_array)) {
				foreach($data_id_array as $id) {
					$sql = $wpdb->prepare("DELETE FROM ".$service_finder_Tables->team_members." WHERE id = %d",$id);
					$query=$wpdb->query($sql);
				}
			}
	}
				
}