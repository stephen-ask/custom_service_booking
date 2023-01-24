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



class SERVICE_FINDER_providersCategory{



	/*Get Searched Providers*/

	public function service_finder_getProvidersCategory($category_id = '', $start = 1, $per_page = 12, $orderby = 'id', $order = 'desc'){

		global $wpdb, $service_finder_Tables, $service_finder_options;
		$identitycheck = (isset($service_finder_options['identity-check'])) ? esc_attr($service_finder_options['identity-check']) : '';
		$restrictuserarea = (isset($service_finder_options['restrict-user-area'])) ? esc_attr($service_finder_options['restrict-user-area']) : '';
		$featuredontop = (isset($service_finder_options['show-category-featured-providers-top'])) ? esc_attr($service_finder_options['show-category-featured-providers-top']) : '';
		
		
		if($restrictuserarea && $identitycheck){
		$sql = 'SELECT providers.id,providers.wp_user_id, providers.bio, providers.avatar_id, providers.phone, providers.mobile, providers.full_name, providers.email, providers.phone, providers.lat, providers.long, providers.category_id, providers.country, providers.city FROM '.$service_finder_Tables->providers.' as providers WHERE admin_moderation = "approved" AND identity = "approved" AND account_blocked != "yes"';
		}else{
		$sql = 'SELECT providers.id,providers.wp_user_id, providers.bio, providers.avatar_id, providers.phone, providers.mobile, providers.full_name, providers.email, providers.phone, providers.lat, providers.long, providers.category_id, providers.country, providers.city FROM '.$service_finder_Tables->providers.' as providers WHERE admin_moderation = "approved" AND account_blocked != "yes"';		
		}

		if($category_id != ''){

		$texonomy = 'providers-category';
		$term_children = get_term_children($category_id,$texonomy);
		
		if(!empty($term_children)){
		$sql .= ' AND (';
			foreach($term_children as $term_child_id) {
				
				$sql .= ' FIND_IN_SET("'.$term_child_id.'", providers.category_id) OR ';
				
			}
		$sql .= ' FIND_IN_SET("'.$category_id.'", providers.category_id) ';	
		$sql .= ' )';	
			
		}else{
		
		$sql .= ' AND FIND_IN_SET("'.$category_id.'", providers.category_id)';
		
		}
		

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