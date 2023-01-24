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
 * Class SERVICE_FINDER_sedateFeatured
 */
class SERVICE_FINDER_ratingLabels extends SERVICE_FINDER_sedateManager{

	
	/*Initial Function*/
	public function service_finder_index()
    {
        
		/*Rander providers template*/
		$this->service_finder_render( 'index','rating-labels' );
		
		/*Action for wp ajax call*/
		$this->service_finder_registerWpActions();
		
    }
	
	/*Actions for wp ajax call*/
	protected function service_finder_registerWpActions() {
       $_this = $this;
	   add_action(
                    'wp_ajax_add_labels',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_add_labels' ) );
                    }
						
                );
		add_action(
                    'wp_ajax_load_labels',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_load_labels' ) );
                    }
						
                );		
		add_action(
                    'wp_ajax_get_labels',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_get_labels' ) );
                    }
						
                );
		add_action(
                    'wp_ajax_delete_label',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_delete_label' ) );
                    }
						
                );
		add_action(
                    'wp_ajax_delete_labels',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_delete_labels' ) );
                    }
						
                );		
    }
	
	/*Add label to db*/
	public function service_finder_add_labels(){
		global $wpdb, $service_finder_Tables;
		
		$categoryid = isset($_POST['category']) ? esc_html($_POST['category']) : '';
		$labelname = isset($_POST['labelname']) ? esc_html($_POST['labelname']) : '';
		
		$chklabel = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->rating_labels.' WHERE `label_name` = "%s" AND `category_id` = "%d"',$labelname,$categoryid));
		
		$labelcount = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->rating_labels.' where category_id = %d',$categoryid));
		
		$total = count($labelcount);
		
		if($total < 5){
		if(empty($chklabel)){
		$data = array(
				'category_id' => $categoryid,
				'label_name' => $labelname, 
				);
		$wpdb->insert($service_finder_Tables->rating_labels,$data);
		
		$labelid = $wpdb->insert_id;
		
		$labels = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->rating_labels.' where category_id = %d ORDER BY label_name',$categoryid));
		$html = '';
		if(!empty($labels)){
		$html .= '<ul class="sf-group-list">';
		foreach($labels as $label){
			$html .= '<li>'.esc_html($label->label_name).' <a href="javascript:;" class="delete-label" data-id="'.esc_attr($label->id).'">&times;</a></li>';
		}
		$html .= '</ul>';	
		}
				
		if ($labelid > 0) {
		
			$success = array(
					'status' => 'success',
					'suc_message' => esc_html__('Label added successfully', 'service-finder'),
					'html' => $html,
					);
			echo json_encode($success);
		}else{
			$error = array(
					'status' => 'error',
					'err_message' => esc_html__('Couldn&#8217;t add label.', 'service-finder'),
					);
			echo json_encode($error);
		
		}
		}else{
			$error = array(
					'status' => 'error',
					'err_message' => esc_html__('Label already exist.', 'service-finder'),
					);
			echo json_encode($error);
		}
		}else{
			$error = array(
					'status' => 'error',
					'err_message' => esc_html__('Sorry! You have already added maxium labels.', 'service-finder'),
					);
			echo json_encode($error);
		}
		exit(0);
	}
	
	/*Load label to db*/
	public function service_finder_load_labels(){
		global $wpdb, $service_finder_Tables;
		
		$categoryid = isset($_POST['categoryid']) ? esc_html($_POST['categoryid']) : '';
		
		$labels = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->rating_labels.' where category_id = %d ORDER BY label_name',$categoryid));
		$html = '';
		if(!empty($labels)){
		$html .= '<ul class="sf-group-list">';
		foreach($labels as $label){
			$html .= '<li>'.esc_html($label->label_name).' <a href="javascript:;" class="delete-label" data-id="'.esc_attr($label->id).'">&times;</a></li>';
		}
		$html .= '</ul>';	
		}
		
		$success = array(
				'status' => 'success',
				'html' => $html,
				);
		echo json_encode($success);
		exit(0);
	}
	
	/*Delete Label*/
	public function service_finder_delete_label(){
	global $wpdb, $service_finder_Tables;
			$labelid = isset($_POST['labelid']) ? esc_html($_POST['labelid']) : '';
			$res = $wpdb->query($wpdb->prepare("DELETE FROM ".$service_finder_Tables->rating_labels." WHERE id = %d",$labelid));
			
			$success = array(
					'status' => 'success',
					'suc_message' => esc_html__('Delete label successfully.', 'service-finder'),
					);
			echo json_encode($success);
			exit(0);
	}
	
	/*Display Amenity to datatable*/
	public function service_finder_get_labels(){
		global $wpdb, $service_finder_Tables;
		$requestData= $_REQUEST;

		$labels = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->rating_labels);
		
		$data = array();
		
		foreach($labels as $result){
			$nestedData=array(); 
		
			$nestedData['labelid'] = $result->id;
			$nestedData['delete'] = "<input type='checkbox' class='deleteRatingLabelsRow' value='".esc_attr($result->id)."' />";
			$nestedData['labelname'] = ucfirst($result->label_name);
			if($result->category_id == 0){
			$nestedData['category'] = esc_html__('Default', 'service-finder');
			}else{
			$nestedData['category'] = ucfirst(service_finder_getCategoryName($result->category_id));
			}
			
			$data[] = $nestedData;
		}
		
		$json_data = array( "data" => $data );
	
		echo json_encode($json_data);
	
		exit;
	}
	
	public function service_finder_delete_labels(){
	global $wpdb, $service_finder_Tables;
			$data_ids = $_REQUEST['data_ids'];
			$data_id_array = explode(",", $data_ids); 
			if(!empty($data_id_array)) {
				foreach($data_id_array as $id) {
					$sql = $wpdb->prepare("DELETE FROM ".$service_finder_Tables->rating_labels." WHERE id = %d",$id);
					$query=$wpdb->query($sql);
				}
			}
	exit(0);		
	}
}