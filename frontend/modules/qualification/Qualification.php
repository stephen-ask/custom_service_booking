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



class SERVICE_FINDER_Qualification{





	/*Add New Qualification*/

	public function service_finder_addQualification($arg = ''){

			global $wpdb, $service_finder_Tables;

			

			$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';

			$degree_name = (!empty($arg['degree_name'])) ? $arg['degree_name'] : '';

			$institute_name = (!empty($arg['institute_name'])) ? $arg['institute_name'] : '';

			$from_year = (!empty($arg['from_year'])) ? $arg['from_year'] : '';

			$to_year = (!empty($arg['to_year'])) ? $arg['to_year'] : '';

			$description = (!empty($arg['description'])) ? $arg['description'] : '';

			

			$qualification_data = array(

                'provider_id'		=> $user_id,

				'degree_name'		=> $degree_name,

				'institute_name'	=> $institute_name,

				'from_year'			=> $from_year,

				'to_year'			=> $to_year,

				'description'		=> $description,

            );

			

			$wpdb->insert($service_finder_Tables->qualification,wp_unslash($qualification_data));

			

			$success = array(

					'status' => 'success',

					'suc_message' => esc_html__('Add qualification successfully.', 'service-finder'),

					);

			echo json_encode($success);

	}

	

	/*update Qualification*/

	public function service_finder_updateQualification($arg = ''){

			global $wpdb, $service_finder_Tables;

			

			$qualificationid = (!empty($arg['qualificationid'])) ? $arg['qualificationid'] : '';

			$degree_name = (!empty($arg['degree_name'])) ? $arg['degree_name'] : '';

			$institute_name = (!empty($arg['institute_name'])) ? $arg['institute_name'] : '';

			$from_year = (!empty($arg['from_year'])) ? $arg['from_year'] : '';

			$to_year = (!empty($arg['to_year'])) ? $arg['to_year'] : '';

			$description = (!empty($arg['description'])) ? $arg['description'] : '';

			

			$qualification_data = array(

				'degree_name'		=> $degree_name,

				'institute_name'	=> $institute_name,

				'from_year'			=> $from_year,

				'to_year'			=> $to_year,

				'description'		=> $description,

            );

			

			$where = array(

                'id'		=> $qualificationid,

            );

			

			$wpdb->update($service_finder_Tables->qualification,wp_unslash($qualification_data),$where);



			$success = array(

					'status' => 'success',

					'suc_message' => esc_html__('Update qualification successfully.', 'service-finder'),

					);

			echo json_encode($success);

	}

	

	/*Get Saved Qualification into datatable*/

	public function service_finder_getQualification($arg){

		global $wpdb, $service_finder_Tables;

		$requestData= $_REQUEST;

		$currUser = wp_get_current_user(); 

		$columns = array( 

			0 =>'id', 

			1 =>'degree_name', 

			2 =>'institute_name', 

			3 =>'from_year', 

			4 =>'to_year', 

		);

		

		$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';

		

		// getting total number records without any search

		$sql = $wpdb->prepare("SELECT * FROM ".$service_finder_Tables->qualification. " WHERE `provider_id` = %d",$user_id);

		$query=$wpdb->get_results($sql);

		$totalData = count($query);

		$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

		

		$sql = "SELECT * FROM ".$service_finder_Tables->qualification. " WHERE `provider_id` = ".$user_id;

		if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter

			$sql.=" AND ( `degree_name` LIKE '".$requestData['search']['value']."%' ";    

			$sql.=" OR `institute_name` LIKE '".$requestData['search']['value']."%'";    

			$sql.=" OR `from_year` LIKE '".$requestData['search']['value']."%'";    

			$sql.=" OR `to_year` LIKE '".$requestData['search']['value']."%' )";    

		}

		$query=$wpdb->get_results($sql);

		$totalFiltered = count($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

		$query=$wpdb->get_results($sql);

		$data = array();

		

		foreach($query as $result){

			$nestedData=array(); 

		

			$nestedData[] = '<div class="checkbox sf-radio-checkbox">

			  <input type="checkbox" id="qualification-'.$result->id.'" class="deleteQualificationRow" value="'.esc_attr($result->id).'">

			  <label for="qualification-'.$result->id.'"></label>

			</div>';

			

			$nestedData[] = $result->degree_name;

			$nestedData[] = $result->institute_name;

			$nestedData[] = $result->from_year.'-'.$result->to_year;

			$nestedData[] = '<button title="'.esc_html__('Edit Qualification', 'service-finder').'" data-id="'.esc_attr($result->id).'" class="btn btn-primary btn-xs editQualification" type="button">'.esc_html__('Edit', 'service-finder').'</button>';

			

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

	

	/*Delete Qualification*/

	public function service_finder_deleteQualification($arg){

		global $wpdb, $service_finder_Tables;

		$currUser = wp_get_current_user(); 

			$data_ids = $_REQUEST['data_ids'];

			$data_id_array = explode(",", $data_ids); 

			if(!empty($data_id_array)) {

				foreach($data_id_array as $id) {

					$sql = $wpdb->prepare("DELETE FROM ".$service_finder_Tables->qualification." WHERE id = %d",$id);

					$query=$wpdb->query($sql);

				}

			}

			wp_send_json_success();
	}

		

	/*Load qualification for edit*/

	public function service_finder_loadQualification($arg){

		global $wpdb, $service_finder_Tables;		

		

		$qualificationid = (!empty($arg['qualificationid'])) ? $arg['qualificationid'] : '';

		

		$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->qualification.' WHERE `ID` = %d',$qualificationid));



		if(!empty($row)){

			$result = array(

				'degree_name'		=> $row->degree_name,

				'institute_name'	=> $row->institute_name,

				'from_year'			=> $row->from_year,

				'to_year'			=> $row->to_year,

				'description'		=> $row->description,

			);



			echo json_encode($result);

		}

			

	}

	

}