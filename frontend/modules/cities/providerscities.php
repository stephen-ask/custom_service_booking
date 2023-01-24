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



class SERVICE_FINDER_providersCities{



	/*Get Searched Providers*/

	public function service_finder_getProvidersCities($cityid = '', $start = 1, $per_page = 12, $orderby = 'id', $order = 'desc'){

		global $wpdb, $service_finder_Tables, $service_finder_options;
		$identitycheck = (isset($service_finder_options['identity-check'])) ? esc_attr($service_finder_options['identity-check']) : '';
		$restrictuserarea = (isset($service_finder_options['restrict-user-area'])) ? esc_attr($service_finder_options['restrict-user-area']) : '';
		$featuredontop = (isset($service_finder_options['show-category-featured-providers-top'])) ? esc_attr($service_finder_options['show-category-featured-providers-top']) : '';
		$term = get_term_by('id',$cityid,'sf-cities');
		$cityname = service_finder_get_data($term,'slug');
		
		if($restrictuserarea && $identitycheck){
		$sql = 'SELECT * FROM '.$service_finder_Tables->providers.' WHERE admin_moderation = "approved" AND identity = "approved" AND account_blocked != "yes" AND city = "'.$cityname.'"';
		}else{
		$sql = 'SELECT * FROM '.$service_finder_Tables->providers.' WHERE admin_moderation = "approved" AND account_blocked != "yes" AND city = "'.$cityname.'"';
		}

		$providers_total = $wpdb->get_results($sql);
		$total = count($providers_total);
		
		if($featuredontop){
			if($orderby == 'title'){
			$orderby = ' ORDER BY featured DESC, full_name '.$order;
			}elseif($orderby == 'rating'){
			$orderby = ' ORDER BY featured DESC,rating '.$order;
			}else{
			$orderby = ' ORDER BY featured DESC,id '.$order;
			}
		}else{
			if($orderby == 'title'){
			$orderby = ' ORDER BY full_name '.$order;
			}elseif($orderby == 'rating'){
			$orderby = ' ORDER BY rating '.$order;
			}else{
			$orderby = ' ORDER BY id '.$order;
			}		
		}
		
		$sql .= $orderby.' LIMIT '.$start.', '.$per_page;
		
		$providers = $wpdb->get_results($sql);
		
		$res = array(
				'count' => $total,
				'result' => $providers,
				'sql' => $sql
				);

		return $res;

	}
	
	/*Get Single Provider*/

	public function service_finder_getProviderInfo($userID){

		global $wpdb, $service_finder_Tables;

		$provider = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$userID));

		return $provider;

	}

	

}