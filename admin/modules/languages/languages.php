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

class SERVICE_FINDER_LANGUAGES extends SERVICE_FINDER_sedateManager{

	
	/*Initial Function*/
	public function service_finder_index()
    {
        
		/*Rander providers template*/
		$this->service_finder_render( 'index','languages' );
		
		/*Action for wp ajax call*/
		//$this->service_finder_registerWpActions();
		
    }
	
	/*Actions for wp ajax call*/
	protected function service_finder_registerWpActions() {
       $_this = $this;
		add_action(
                    'wp_ajax_update_languages',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_update_languages' ) );
                    }
						
                );	
    }
	
	public function service_finder_update_languages(){
	global $wpdb, $service_finder_Tables;

	update_option( 'sf_languages', $_POST['languages'] );
	
	$success = array(
			'status' => 'success',
			'suc_message' => esc_html__('Languages updated successfully', 'service-finder'),
			);
	echo json_encode($success);

	exit(0);
	}
	
}