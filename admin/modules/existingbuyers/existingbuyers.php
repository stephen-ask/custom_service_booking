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
class SERVICE_FINDER_EXISTING_BUYERS extends SERVICE_FINDER_sedateManager{

	/*Actions for wp ajax call*/
	protected function service_finder_registerWpActions() {
     	$_this = $this;
     	$_this = $this;
	   add_action(
                    'wp_ajax_manage_shortcodes',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_manage_shortcodes' ) );
                    }
						
                );
		add_action(
                    'wp_ajax_updatecitytaxonomy',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_updatecitytaxonomy' ) );
                    }
						
                );		
	}
	
	/*Initial Function*/
	public function service_finder_index()
    {
        /*Rander providers template*/
		$this->service_finder_render( 'index','existingbuyers' );
		
		/*Action for wp ajax call*/
		$this->service_finder_registerWpActions();
	}
    
    public function service_finder_manage_shortcodes(){
	
		$manageshortcode = service_finder_get_data($_POST,'option');
		
		if($manageshortcode == 'yes'){
			update_option('manageshortcode_from_themeoption','yes');
		}else{
			update_option('manageshortcode_from_themeoption','no');
		}
		
		$success = array(
				'status' => 'success',
				'suc_message' => esc_html__('Manage shortcode option updated successfully', 'service-finder'),
				);
		echo json_encode($success);
		
		exit;
	}
	
	public function service_finder_updatecitytaxonomy(){
		global $wpdb, $service_finder_Tables;
		$results = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->providers);
		
		if(!empty($results))
		{
			foreach($results as $row)
			{
				if(service_finder_get_data($row,'city') != '' && service_finder_get_data($row,'country') != '')
				{
					service_finder_create_city_term(service_finder_get_data($row,'city'),service_finder_get_data($row,'country'));
				}
			}
		}
		
		$results = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->cities);
		
		if(!empty($results))
		{
			foreach($results as $row)
			{
				if(service_finder_get_data($row,'cityname') != '' && service_finder_get_data($row,'countryname') != '')
				{
					service_finder_create_city_term(service_finder_get_data($row,'cityname'),service_finder_get_data($row,'countryname'));
				}
			}
		}
		
		$success = array(
				'status' => 'success',
				'suc_message' => esc_html__('Cities taxonomy updated successfully', 'service-finder'),
				);
		echo json_encode($success);
		
		exit;
	}
	
}