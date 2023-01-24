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
 * Booking Manager abstract class that will be extend.
 */
abstract class SERVICE_FINDER_sedateManager
{

    
	/** Constructor */
    public function __construct()
    {
        $this->service_finder_registerWpActions();
		
    }
	
	/** Render a template file. */
    protected function service_finder_render($template, $module, $args = array(), $echo = true) {

        // Start output buffering.
        ob_start();
        ob_implicit_flush(0);
        try {
            include SERVICE_FINDER_BOOKING_ADMIN_MODULE_DIR. '/' . $module . '/templates/' . $template . '.php';
        } catch (Exception $e) {
            ob_end_clean();
            throw $e;
        }

        if ( $echo ) {
            echo ob_get_clean();
        } else {
            return ob_get_clean();
        }
    }
	
	
	/*Fetch Provider List Array By First Charecter*/
	protected function service_finder_getProvidersList(){
		global $wpdb, $service_finder_Tables, $service_finder_options;
		$identitycheck = (isset($service_finder_options['identity-check'])) ? esc_attr($service_finder_options['identity-check']) : '';
		$restrictuserarea = (isset($service_finder_options['restrict-user-area'])) ? esc_attr($service_finder_options['restrict-user-area']) : '';
		if($restrictuserarea && $identitycheck){
		$providers = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->providers.' WHERE full_name LIKE "a%" AND identity = "approved" AND admin_moderation = "approved" AND account_blocked != "yes"');
		}else{
		$providers = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->providers.' WHERE full_name LIKE "a%" AND admin_moderation = "approved" AND account_blocked != "yes"');
		}
		
		return $providers;
	}
	
	/*Fetch All Provider List Array*/
	protected function service_finder_getAllProvidersList(){
		global $wpdb, $service_finder_Tables, $service_finder_options;
		$identitycheck = (isset($service_finder_options['identity-check'])) ? esc_attr($service_finder_options['identity-check']) : '';
		$restrictuserarea = (isset($service_finder_options['restrict-user-area'])) ? esc_attr($service_finder_options['restrict-user-area']) : '';
		if($restrictuserarea && $identitycheck){
		$providers = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->providers.' WHERE admin_moderation = "approved" AND identity = "approved" AND account_blocked != "yes" ORDER BY full_name');
		}else{
		$providers = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->providers.' WHERE admin_moderation = "approved" AND account_blocked != "yes" ORDER BY full_name');
		}
		
		return $providers;
	}
	
}