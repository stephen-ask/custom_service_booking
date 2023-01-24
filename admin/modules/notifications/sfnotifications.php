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
class SERVICE_FINDER_notifications extends SERVICE_FINDER_sedateManager{

	
	/*Initial Function*/
	public function service_finder_index()
    {
        
		/*Rander providers template*/
		$this->service_finder_render( 'index','notifications' );
		
		/*Action for wp ajax call*/
		$this->service_finder_registerWpActions();
		
    }
	
	/*Actions for wp ajax call*/
	protected function service_finder_registerWpActions() {
       $_this = $this;
	   add_action(
                    'wp_ajax_get_admin_notifications',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_get_admin_notifications' ) );
                    }
						
                );
		add_action(
                    'wp_ajax_delete_admin_notifications',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_delete_admin_notifications' ) );
                    }
						
                );		
    }
	
	public function service_finder_get_admin_notifications(){
		global $wpdb, $service_finder_Tables;
		$requestData= $_REQUEST;

		$notifications = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->notifications.' WHERE `admin_id` = %d ORDER BY id DESC',1));
		
		$data = array();
		
		foreach($notifications as $result){
			$nestedData=array(); 
		
			$nestedData['notificationid'] = $result->id;
			$nestedData['delete'] = "<input type='checkbox' class='deleteNotificationRow' value='".esc_attr($result->id)."' />";
			$nestedData['date'] = ($result->datetime != '0000-00-00 00:00:00' ) ? date('Y-m-d h:i a',strtotime($result->datetime)) : esc_html__('N/A', 'service-finder');
			$nestedData['title'] = $result->topic;
			$nestedData['notice'] = $result->notice;
			
			$data[] = $nestedData;
		}
		
		$json_data = array( "data" => $data );
	
		echo json_encode($json_data);
	
		exit;
	}
	
	public function service_finder_delete_admin_notifications(){
		global $wpdb, $service_finder_Tables;
			$data_ids = $_REQUEST['data_ids'];
			$data_id_array = explode(",", $data_ids); 
			if(!empty($data_id_array)) {
				foreach($data_id_array as $id) {
					$sql = $wpdb->prepare("DELETE FROM ".$service_finder_Tables->notifications." WHERE id = %d",$id);
					$query=$wpdb->query($sql);
				}
			}
		exit(0);	
	}
	
}