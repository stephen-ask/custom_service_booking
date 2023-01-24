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
 * Class SERVICE_FINDER_sedateBookingCalendar
 */
class SERVICE_FINDER_sedateBookingCalendar extends SERVICE_FINDER_sedateManager{

	
	
	public function service_finder_index()
    {
		wp_add_inline_script( 'service_finder-js-schedule', 'var caltmpls = "'.SERVICE_FINDER_BOOKING_ASSESTS_URL . '/bootstrap-calendar/tmpls/'.'";
		var adminscheduleurl = "'.SERVICE_FINDER_BOOKING_ADMIN_MODULE_URL . '/booking-calendar/admin-schedule-data.php'.'";', 'before' );
		
		wp_add_inline_script( 'service_finder-js-app', 'var caltmpls = "'.SERVICE_FINDER_BOOKING_ASSESTS_URL . '/bootstrap-calendar/tmpls/'.'";
		var adminscheduleurl = "'.SERVICE_FINDER_BOOKING_ADMIN_MODULE_URL . '/booking-calendar/admin-schedule-data.php'.'";', 'before' );
		
		/*Rander calendar template*/
		$this->service_finder_render( 'index','booking-calendar', $this->service_finder_getProvidersList() );
		
		/*Action for wp ajax call*/
		$this->service_finder_registerWpActions();
		
    }
	
	protected function service_finder_registerWpActions() {
       $_this = $this;
	   
	   add_action(
                    'wp_ajax_get_providers_list',
					function () use ( $_this ) {
						call_user_func( array( $_this, 'service_finder_getProvidersListUpdate' ) );
                    }
						
                );
    }
	
	/*Fetch Provider List Array*/
	public function service_finder_getProvidersListUpdate(){
		global $wpdb, $service_finder_Tables, $service_finder_options;
		
		$identitycheck = (isset($service_finder_options['identity-check'])) ? esc_attr($service_finder_options['identity-check']) : '';
		$restrictuserarea = (isset($service_finder_options['restrict-user-area'])) ? esc_attr($service_finder_options['restrict-user-area']) : '';
		if($restrictuserarea && $identitycheck){
		$providers = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->providers.' WHERE full_name LIKE "'.$_POST['char'].'%" AND admin_moderation = "approved" AND identity = "approved" AND account_blocked != "yes"');
		}else{
		$providers = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->providers.' WHERE full_name LIKE "'.$_POST['char'].'%" AND admin_moderation = "approved" AND account_blocked != "yes"');
		}
		
		if(!empty($providers)){
			echo '
<li data-staff-id="all">';
  echo '<a href="javascript:;" data-toggle="tab">All Providers</a>';
  echo '</li>
';
			foreach($providers as $arg){
			echo '
<li class="staff-tab-'.esc_attr($arg->wp_user_id).'" data-staff-id="'.esc_attr($arg->wp_user_id).'">';
  echo '<a href="javascript:;" data-toggle="tab">'.$arg->full_name.'</a>';
  echo '</li>
';
			$user_names[] = $arg->full_name;
			$user_ids[]   = $arg->wp_user_id;
			}
		}else{
			printf('
<li>'.esc_html__('No Providers Found', 'service-finder').'</li>
');
			
		}
		
		exit(0);
	}
	
}