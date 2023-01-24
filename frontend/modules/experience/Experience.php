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



class SERVICE_FINDER_Experience{





	/*Add New Experience*/

	public function service_finder_addExperience($arg = ''){

			global $wpdb, $service_finder_Tables;

			

			$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';

			$title = (!empty($arg['job_title'])) ? $arg['job_title'] : '';

			$company_name = (!empty($arg['company_name'])) ? $arg['company_name'] : '';

			$start_date = (!empty($arg['start_date'])) ? $arg['start_date'] : '';

			$end_date = (!empty($arg['end_date'])) ? $arg['end_date'] : '';

			$description = (!empty($arg['job_description'])) ? $arg['job_description'] : '';

			$current_job = (!empty($arg['current_job'])) ? $arg['current_job'] : '';

			

			if($current_job == 'yes'){

			$experience_data = array(

				'current_job'	=> '',

            );

			

			$where = array(

                'provider_id'		=> $user_id,

            );

			

			$wpdb->update($service_finder_Tables->experience,wp_unslash($experience_data),$where);

			}

			

			$experience_data = array(

                'provider_id'	=> $user_id,

				'job_title'		=> $title,

				'company_name'	=> $company_name,

				'start_date'	=> $start_date,

				'end_date'		=> $end_date,

				'description'	=> $description,

				'current_job'	=> $current_job,

            );

			

			$wpdb->insert($service_finder_Tables->experience,wp_unslash($experience_data));

			

			$success = array(

					'status' => 'success',

					'suc_message' => esc_html__('Add experience successfully.', 'service-finder'),

					);

			echo json_encode($success);

	}

	

	/*update Experience*/

	public function service_finder_updateExperience($arg = ''){

			global $wpdb, $service_finder_Tables;

			

			$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';

			$experienceid = (!empty($arg['experienceid'])) ? $arg['experienceid'] : '';

			$title = (!empty($arg['job_title'])) ? $arg['job_title'] : '';

			$company_name = (!empty($arg['company_name'])) ? $arg['company_name'] : '';

			$start_date = (!empty($arg['start_date'])) ? $arg['start_date'] : '';

			$end_date = (!empty($arg['end_date'])) ? $arg['end_date'] : '';

			$description = (!empty($arg['job_description'])) ? $arg['job_description'] : '';

			$current_job = (!empty($arg['current_job'])) ? $arg['current_job'] : '';

			

			if($current_job == 'yes'){

			$experience_data = array(

				'current_job'	=> '',

            );

			

			$where = array(

                'provider_id'		=> $user_id,

            );

			

			$wpdb->update($service_finder_Tables->experience,wp_unslash($experience_data),$where);

			}

			

			$experience_data = array(

                'job_title'		=> $title,

				'company_name'	=> $company_name,

				'start_date'	=> $start_date,

				'end_date'		=> $end_date,

				'description'	=> $description,

				'current_job'	=> $current_job,

            );

			

			$where = array(

                'id'		=> $experienceid,

            );

			

			$wpdb->update($service_finder_Tables->experience,wp_unslash($experience_data),$where);



			$success = array(

					'status' => 'success',

					'suc_message' => esc_html__('Update experience successfully.', 'service-finder'),

					);

			echo json_encode($success);

	}

	

	/*Get Saved Experience into datatable*/

	public function service_finder_getExperience($arg){

		global $wpdb, $service_finder_Tables;

		$requestData= $_REQUEST;

		$currUser = wp_get_current_user(); 

		$columns = array( 

			0 =>'id', 

			1 =>'job_title', 

			2 =>'company_name', 

			3 =>'start_date', 

			4 =>'end_date', 

			5 =>'current_job', 

		);

		

		$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';

		

		// getting total number records without any search

		$sql = $wpdb->prepare("SELECT * FROM ".$service_finder_Tables->experience. " WHERE `provider_id` = %d",$user_id);

		$query=$wpdb->get_results($sql);

		$totalData = count($query);

		$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

		

		$sql = "SELECT * FROM ".$service_finder_Tables->experience. " WHERE `provider_id` = ".$user_id;

		if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter

			$sql.=" AND ( `job_title` LIKE '".$requestData['search']['value']."%' ";    

			$sql.=" OR `company_name` LIKE '".$requestData['search']['value']."%'";    

			$sql.=" OR `start_date` LIKE '".$requestData['search']['value']."%'";    

			$sql.=" OR `end_date` LIKE '".$requestData['search']['value']."%'";    

			$sql.=" OR `current_job` LIKE '".$requestData['search']['value']."%' )";    

		}

		$query=$wpdb->get_results($sql);

		$totalFiltered = count($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

		$query=$wpdb->get_results($sql);

		$data = array();

		

		foreach($query as $result){

			$nestedData=array(); 

		

			$nestedData[] = '<div class="checkbox sf-radio-checkbox">

			  <input type="checkbox" id="experience-'.$result->id.'" class="deleteExperienceRow" value="'.esc_attr($result->id).'">

			  <label for="experience-'.$result->id.'"></label>

			</div>';

			

			$nestedData[] = $result->job_title;

			$nestedData[] = $result->company_name;

			$nestedData[] = $result->start_date;

			$nestedData[] = ($result->current_job == 'yes') ? '-' : $result->end_date;

			$nestedData[] = $result->current_job;

			$nestedData[] = '<button title="'.esc_html__('Edit Experience', 'service-finder').'" data-id="'.esc_attr($result->id).'" class="btn btn-primary btn-xs editExperience" type="button">'.esc_html__('Edit', 'service-finder').'</button>';

			

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

	

	/*Delete Experience*/

	public function service_finder_deleteExperience($arg){

		global $wpdb, $service_finder_Tables;

		$currUser = wp_get_current_user(); 

			$data_ids = $_REQUEST['data_ids'];

			$data_id_array = explode(",", $data_ids); 

			if(!empty($data_id_array)) {

				foreach($data_id_array as $id) {

					$sql = $wpdb->prepare("DELETE FROM ".$service_finder_Tables->experience." WHERE id = %d",$id);

					$query=$wpdb->query($sql);

				}

			}

			wp_send_json_success();

	}

		

	/*Load experience for edit*/

	public function service_finder_loadExperience($arg){

		global $wpdb, $service_finder_Tables;		

		

		$experienceid = (!empty($arg['experienceid'])) ? $arg['experienceid'] : '';

		

		$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->experience.' WHERE `ID` = %d',$experienceid));



		if(!empty($row)){

			$result = array(

				'job_title'		=> $row->job_title,

				'company_name'	=> $row->company_name,

				'start_date'	=> $row->start_date,

				'end_date'		=> $row->end_date,

				'description'	=> $row->description,

				'current_job'	=> ($row->current_job == 'yes') ? true : false,

			);



			echo json_encode($result);

		}

			

	}

	

}