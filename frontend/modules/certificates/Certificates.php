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



class SERVICE_FINDER_Certificates{



	/*Add New Certificates*/

	public function service_finder_addCertificates($arg = ''){

			global $wpdb, $service_finder_Tables;

			

			$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';

			$certificate_title = (!empty($arg['certificate_title'])) ? $arg['certificate_title'] : '';

			$issue_date = (!empty($arg['issue_date'])) ? $arg['issue_date'] : '';

			$description = (!empty($arg['description'])) ? $arg['description'] : '';

			$current_job = (!empty($arg['current_job'])) ? $arg['current_job'] : '';

			$attachment_id = (!empty($arg['certificateattachmentid'])) ? $arg['certificateattachmentid'] : '';

			

			$certificates_data = array(

                'provider_id'			=> $user_id,

				'attachment_id'			=> $attachment_id,

				'certificate_title'		=> $certificate_title,

				'issue_date'			=> $issue_date,

				'description'			=> $description,

            );

			

			$wpdb->insert($service_finder_Tables->certificates,wp_unslash($certificates_data));

			

			$success = array(

					'status' => 'success',

					'suc_message' => esc_html__('Add certificate successfully.', 'service-finder'),

					);

			echo json_encode($success);

	}

	

	/*update Certificates*/

	public function service_finder_updateCertificates($arg = ''){

			global $wpdb, $service_finder_Tables;

			

			$certificatesid = (!empty($arg['certificatesid'])) ? $arg['certificatesid'] : '';

			$certificate_title = (!empty($arg['certificate_title'])) ? $arg['certificate_title'] : '';

			$issue_date = (!empty($arg['issue_date'])) ? $arg['issue_date'] : '';

			$description = (!empty($arg['description'])) ? $arg['description'] : '';

			$current_job = (!empty($arg['current_job'])) ? $arg['current_job'] : '';

			$attachment_id = (!empty($arg['certificateattachmentid'])) ? $arg['certificateattachmentid'] : '';

			

			$certificates_data = array(

                'certificate_title'		=> $certificate_title,

				'attachment_id'			=> $attachment_id,

				'issue_date'			=> $issue_date,

				'description'			=> $description,

            );

			

			$where = array(

                'id'		=> $certificatesid,

            );

			

			$wpdb->update($service_finder_Tables->certificates,wp_unslash($certificates_data),$where);



			$success = array(

					'status' => 'success',

					'suc_message' => esc_html__('Update certificates successfully.', 'service-finder'),

					);

			echo json_encode($success);

	}

	

	/*Get Saved Certificates into datatable*/

	public function service_finder_getCertificates($arg){

		global $wpdb, $service_finder_Tables;

		$requestData= $_REQUEST;

		$currUser = wp_get_current_user(); 

		$columns = array( 

			0 =>'id', 

			1 =>'certificate_title', 

			2 =>'issue_date', 

		);

		

		$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';

		

		// getting total number records without any search

		$sql = $wpdb->prepare("SELECT * FROM ".$service_finder_Tables->certificates. " WHERE `provider_id` = %d",$user_id);

		$query=$wpdb->get_results($sql);

		$totalData = count($query);

		$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

		

		$sql = "SELECT * FROM ".$service_finder_Tables->certificates. " WHERE `provider_id` = ".$user_id;

		if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter

			$sql.=" AND ( `certificate_title` LIKE '".$requestData['search']['value']."%' ";    

			$sql.=" OR `issue_date` LIKE '".$requestData['search']['value']."%' )";    

		}

		$query=$wpdb->get_results($sql);

		$totalFiltered = count($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

		$query=$wpdb->get_results($sql);

		$data = array();

		

		foreach($query as $result){

			$nestedData=array(); 

		

			$nestedData[] = '<div class="checkbox sf-radio-checkbox">

			  <input type="checkbox" id="certificates-'.$result->id.'" class="deleteCertificatesRow" value="'.esc_attr($result->id).'">

			  <label for="certificates-'.$result->id.'"></label>

			</div>';

			

			$nestedData[] = $result->certificate_title;

			$nestedData[] = $result->issue_date;

			$nestedData[] = '<button title="'.esc_html__('Edit Certificate', 'service-finder').'" data-id="'.esc_attr($result->id).'" class="btn btn-primary btn-xs editCertificate" type="button">'.esc_html__('Edit', 'service-finder').'</button>';

			

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

	

	/*Delete Certificates*/

	public function service_finder_deleteCertificates($arg){

		global $wpdb, $service_finder_Tables;

		$currUser = wp_get_current_user(); 

			$data_ids = $_REQUEST['data_ids'];

			$data_id_array = explode(",", $data_ids); 

			if(!empty($data_id_array)) {

				foreach($data_id_array as $id) {

					$sql = $wpdb->prepare("DELETE FROM ".$service_finder_Tables->certificates." WHERE id = %d",$id);

					$query=$wpdb->query($sql);

				}

			}

			wp_send_json_success();

	}

		

	/*Load certificates for edit*/

	public function service_finder_loadCertificates($arg){

		global $wpdb, $service_finder_Tables;		

		

		$certificatesid = (!empty($arg['certificatesid'])) ? $arg['certificatesid'] : '';

		

		$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->certificates.' WHERE `ID` = %d',$certificatesid));



		if(!empty($row)){

			$hiddenclass = '';

			$html = '';

			

			if (!empty($row->attachment_id)) {

				

				$attachment_id = $row->attachment_id;

				

				$fileicon = new SERVICE_FINDER_ImageSpace();

				$arr  = $fileicon->get_icon_for_attachment($attachment_id);

			

				$i18n_delete = apply_filters( 'rwmb_image_delete_string', _x( 'Delete', 'image upload', 'service-finder' ) );

				$hiddenclass = 'hidden';

				

				$html = sprintf('<li id="item_%s">

					<img src="%s" />

					<div class="rwmb-image-bar">

						<a title="%s" class="rwmb-delete-file" href="javascript:;" data-attachment_id="%s">&times;</a>

						<input type="hidden" name="certificateattachmentid" value="%s">

					</div>

				</li>',

				esc_attr($attachment_id),

				esc_url($arr['src']),

				esc_attr($i18n_delete), esc_attr($attachment_id),

				esc_attr($attachment_id)

				);

			}

		

			$result = array(

				'certificate_title'		=> $row->certificate_title,

				'issue_date'			=> $row->issue_date,

				'description'			=> $row->description,

				'imagehtml'				=> $html,

				'hiddenclass'			=> $hiddenclass,

			);



			echo json_encode($result);

		}

			

	}

	

}