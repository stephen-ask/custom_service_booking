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

class SERVICE_FINDER_searchProviders{

	/*Get Searched Providers*/
	public function service_finder_getSearchedProviders($searchdata = array(),$distance = '',$minprice = '',$maxprice = '',$keyword = '',$address = '',$city = '',$category_id = '',$country = '', $start = 0, $per_page = 12, $orderby = 'id', $order = 'asc'){
		global $wpdb, $service_finder_Tables, $service_finder_options;
		$srhaddress = $address;
		$identitycheck = (isset($service_finder_options['identity-check'])) ? esc_attr($service_finder_options['identity-check']) : '';
		$restrictuserarea = (isset($service_finder_options['restrict-user-area'])) ? esc_attr($service_finder_options['restrict-user-area']) : '';
		$featuredontop = (isset($service_finder_options['show-featured-providers-top'])) ? esc_attr($service_finder_options['show-featured-providers-top']) : '';
		$radiussearchunit = (isset($service_finder_options['radius-search-unit'])) ? esc_attr($service_finder_options['radius-search-unit']) : 'mi';
		$maxpricerange = (isset($service_finder_options['search-max-price'])) ? esc_attr($service_finder_options['search-max-price']) : 0;
		
		$state = service_finder_get_data($searchdata,'state');
		$zipcode = service_finder_get_data($searchdata,'zipcode');
		$srhgender = service_finder_get_data($searchdata,'srhgender');
		
		if($minprice == 0 && $maxprice == $maxpricerange){
			$minprice = 0;
			$maxprice = 0;
		}
		
		if($radiussearchunit == 'mi'){
		$unitchanger = 3959;
		}else{
		$unitchanger = 6371;
		}
		
		$latitude = '23.5572045';
		$longitude = '74.4404721';
		
		if($address != ""){
	
			$address = str_replace(" ","+",$address);
			$res = service_finder_getLatLong($address);
			$latitude = service_finder_get_data($res,'lat');
			$longitude = service_finder_get_data($res,'lng');
		
		}
		
		/*elseif($city != "" && $country != ""){
	
			$address = str_replace(" ","+",$city).'+'.str_replace(" ","+",$country);
			$res = service_finder_getLatLong($address);
			$latitude = service_finder_get_data($res,'lat');
			$longitude = service_finder_get_data($res,'lng');

		}elseif($city != "" && $country == ""){
			
			$defaultcountry = (!empty($service_finder_options['default-country'])) ? $service_finder_options['default-country'] : '';
			
			if($defaultcountry != ""){
			$address = str_replace(" ","+",$city).'+'.str_replace(" ","+",$defaultcountry);
			}else{
			$address = str_replace(" ","+",$city);
			}
			$res = service_finder_getLatLong($address);
			$latitude = service_finder_get_data($res,'lat');
			$longitude = service_finder_get_data($res,'lng');
		}*/
		
		if($keyword != '' || ($minprice != "" && $maxprice != "" && $maxprice > 0)){
		if($restrictuserarea && $identitycheck){
		if($distance != '' && $distance > 0 && $latitude != "" && $longitude != "" && $address != ""){
		$sql = 'SELECT ( '.$unitchanger.' * acos( cos( radians('.$latitude.') ) * cos( radians( providers.lat ) ) * cos( radians(providers.long) - radians('.$longitude.')) + sin(radians('.$latitude.')) * sin( radians(providers.lat)))) AS distance, providers.id,providers.wp_user_id, providers.bio, providers.avatar_id, providers.full_name, providers.email, providers.phone, providers.mobile, providers.lat, providers.long, providers.category_id, providers.address, providers.country, providers.city, providers.company_name, providers.full_name, providers.bio, providers.booking_description, providers.tagline, services.service_name, services.description FROM '.$service_finder_Tables->providers.' as providers LEFT JOIN '.$service_finder_Tables->services.' as services ON providers.wp_user_id = services.wp_user_id LEFT JOIN '.$service_finder_Tables->branches.' as branches ON providers.wp_user_id = branches.wp_user_id LEFT JOIN '.$service_finder_Tables->service_area.' as service_area ON providers.wp_user_id = service_area.provider_id WHERE providers.admin_moderation = "approved" AND providers.identity = "approved" AND providers.account_blocked != "yes" ';
		}else{
		$sql = 'SELECT providers.id,providers.wp_user_id, providers.bio, providers.avatar_id, providers.full_name, providers.email, providers.phone, providers.mobile, providers.lat, providers.long, providers.category_id, providers.address, providers.country, providers.city, providers.company_name, providers.full_name, providers.bio, providers.booking_description, providers.tagline, services.service_name, services.description FROM '.$service_finder_Tables->providers.' as providers LEFT JOIN '.$service_finder_Tables->services.' as services ON providers.wp_user_id = services.wp_user_id LEFT JOIN '.$service_finder_Tables->branches.' as branches ON providers.wp_user_id = branches.wp_user_id LEFT JOIN '.$service_finder_Tables->service_area.' as service_area ON providers.wp_user_id = service_area.provider_id WHERE providers.admin_moderation = "approved" AND providers.identity = "approved" AND providers.account_blocked != "yes" ';
		}
		}else{
		if($distance != '' && $distance > 0 && $latitude != "" && $longitude != "" && $address != ""){
		$sql = 'SELECT ( '.$unitchanger.' * acos( cos( radians('.$latitude.') ) * cos( radians( providers.lat ) ) * cos( radians(providers.long) - radians('.$longitude.')) + sin(radians('.$latitude.')) * sin( radians(providers.lat)))) AS distance, providers.id,providers.wp_user_id, providers.bio, providers.avatar_id, providers.full_name, providers.email, providers.phone, providers.mobile, providers.lat, providers.long, providers.category_id, providers.address, providers.country, providers.city, providers.company_name, providers.full_name, providers.bio, providers.booking_description, providers.tagline, services.service_name, services.description FROM '.$service_finder_Tables->providers.' as providers LEFT JOIN '.$service_finder_Tables->services.' as services ON providers.wp_user_id = services.wp_user_id LEFT JOIN '.$service_finder_Tables->branches.' as branches ON providers.wp_user_id = branches.wp_user_id LEFT JOIN '.$service_finder_Tables->service_area.' as service_area ON providers.wp_user_id = service_area.provider_id WHERE providers.admin_moderation = "approved" AND providers.account_blocked != "yes" ';
		}else{
		$sql = 'SELECT providers.id,providers.wp_user_id, providers.bio, providers.avatar_id, providers.full_name, providers.email, providers.phone, providers.mobile, providers.lat, providers.long, providers.category_id, providers.address, providers.country, providers.city, providers.company_name, providers.full_name, providers.bio, providers.booking_description, providers.tagline, services.service_name, services.description FROM '.$service_finder_Tables->providers.' as providers LEFT JOIN '.$service_finder_Tables->services.' as services ON providers.wp_user_id = services.wp_user_id LEFT JOIN '.$service_finder_Tables->branches.' as branches ON providers.wp_user_id = branches.wp_user_id LEFT JOIN '.$service_finder_Tables->service_area.' as service_area ON providers.wp_user_id = service_area.provider_id WHERE providers.admin_moderation = "approved" AND providers.account_blocked != "yes" ';
		}
		}
		}else{
		if($restrictuserarea && $identitycheck){
		if($distance != '' && $distance > 0 && $latitude != "" && $longitude != "" && $address != ""){
		$sql = 'SELECT ( '.$unitchanger.' * acos( cos( radians('.$latitude.') ) * cos( radians( providers.lat ) ) * cos( radians(providers.long) - radians('.$longitude.')) + sin(radians('.$latitude.')) * sin( radians(providers.lat)))) AS distance, providers.id,providers.wp_user_id, providers.bio, providers.avatar_id, providers.full_name, providers.email, providers.phone, providers.mobile, providers.lat, providers.long, providers.category_id, providers.address, providers.country, providers.city, providers.company_name, providers.full_name, providers.bio, providers.booking_description, providers.tagline FROM '.$service_finder_Tables->providers.' as providers LEFT JOIN '.$service_finder_Tables->branches.' as branches ON providers.wp_user_id = branches.wp_user_id LEFT JOIN '.$service_finder_Tables->service_area.' as service_area ON providers.wp_user_id = service_area.provider_id WHERE admin_moderation = "approved" AND identity = "approved" AND account_blocked != "yes" ';
		}else{
		$sql = 'SELECT providers.id,providers.wp_user_id, providers.bio, providers.avatar_id, providers.full_name, providers.email, providers.phone, providers.mobile, providers.lat, providers.long, providers.category_id, providers.address, providers.country, providers.city, providers.company_name, providers.full_name, providers.bio, providers.booking_description, providers.tagline FROM '.$service_finder_Tables->providers.' as providers LEFT JOIN '.$service_finder_Tables->branches.' as branches ON providers.wp_user_id = branches.wp_user_id LEFT JOIN '.$service_finder_Tables->service_area.' as service_area ON providers.wp_user_id = service_area.provider_id WHERE admin_moderation = "approved" AND identity = "approved" AND account_blocked != "yes" ';
		}
		
		}else{
		if($distance != '' && $distance > 0 && $latitude != "" && $longitude != "" && $address != ""){
		$sql = 'SELECT ( '.$unitchanger.' * acos( cos( radians('.$latitude.') ) * cos( radians( providers.lat ) ) * cos( radians(providers.long) - radians('.$longitude.')) + sin(radians('.$latitude.')) * sin( radians(providers.lat)))) AS distance, providers.id,providers.wp_user_id, providers.bio, providers.avatar_id, providers.full_name, providers.email, providers.phone, providers.mobile, providers.lat, providers.long, providers.category_id, providers.address, providers.country, providers.city, providers.company_name, providers.full_name, providers.bio, providers.booking_description, providers.tagline FROM '.$service_finder_Tables->providers.' as providers LEFT JOIN '.$service_finder_Tables->branches.' as branches ON providers.wp_user_id = branches.wp_user_id LEFT JOIN '.$service_finder_Tables->service_area.' as service_area ON providers.wp_user_id = service_area.provider_id WHERE admin_moderation = "approved" AND account_blocked != "yes" ';		
		}else{
		
		$sql = 'SELECT providers.id,providers.wp_user_id, providers.bio, providers.avatar_id, providers.full_name, providers.email, providers.phone, providers.mobile, providers.lat, providers.long, providers.category_id, providers.address, providers.country, providers.city, providers.company_name, providers.full_name, providers.bio, providers.booking_description, providers.tagline FROM '.$service_finder_Tables->providers.' as providers LEFT JOIN '.$service_finder_Tables->branches.' as branches ON providers.wp_user_id = branches.wp_user_id LEFT JOIN '.$service_finder_Tables->service_area.' as service_area ON providers.wp_user_id = service_area.provider_id WHERE admin_moderation = "approved" AND account_blocked != "yes" ';
		}
		
		}
		}

		if($srhgender != ''){

		$sql .= 'AND (providers.gender = "'.$srhgender.'") ';

		}

		if($city != '' && $category_id != '' && $country != ''){
		
		$texonomy = 'providers-category';
		$term_children = get_term_children($category_id,$texonomy);
		if(!empty($term_children)){
		$sql .= ' AND (';
			foreach($term_children as $term_child_id) {
				
				$sql .= ' FIND_IN_SET("'.$term_child_id.'", providers.category_id) OR ';
				
			}
		$sql .= ' FIND_IN_SET("'.$category_id.'", providers.category_id) ';	
		$sql .= ' ) AND';	
			
		}else{
		
		$sql .= 'AND FIND_IN_SET("'.$category_id.'", providers.category_id) AND';
		
		}
		
		$sql .= ' (providers.city = "'.$city.'" OR branches.city = "'.$city.'") AND';

		$sql .= ' (providers.country = "'.$country.'" OR branches.country = "'.$country.'")';

		}

		

		if($city != '' && $category_id == '' && $country == ''){

		$sql .= 'AND (providers.city = "'.$city.'" OR branches.city = "'.$city.'") ';

		}
		
		if($state != ''){

		$sql .= 'AND (providers.state = "'.$state.'" OR branches.state = "'.$state.'") ';

		}

		if($zipcode != ''){

		$sql .= 'AND (providers.zipcode = "'.$zipcode.'" OR branches.zipcode = "'.$zipcode.'" OR (service_area.zipcode = "'.$zipcode.'" AND service_area.status = "active")) ';

		}

		if($category_id != '' && $city == '' && $country == ''){

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
		
		$sql .= 'AND FIND_IN_SET("'.$category_id.'", providers.category_id)';
		
		}
		
		}

		

		if($category_id == '' && $city == '' && $country != ''){

		$sql .= 'AND (providers.country = "'.$country.'" OR branches.country = "'.$country.'")';

		}

		

		if($city != '' && $category_id != '' && $country == ''){

		$sql .= 'AND (providers.city = "'.$city.'" OR branches.city = "'.$city.'") AND';

		$texonomy = 'providers-category';
		$term_children = get_term_children($category_id,$texonomy);
		if(!empty($term_children)){
		$sql .= ' (';
			foreach($term_children as $term_child_id) {
				
				$sql .= ' FIND_IN_SET("'.$term_child_id.'", providers.category_id) OR ';
				
			}
		$sql .= ' FIND_IN_SET("'.$category_id.'", providers.category_id) ';	
		$sql .= ' )';	
			
		}else{
		
		$sql .= ' FIND_IN_SET("'.$category_id.'", providers.category_id)';
		
		}

		}

		

		if($city != '' && $category_id == '' && $country != ''){

		$sql .= 'AND (providers.city = "'.$city.'" OR branches.city = "'.$city.'") AND';

		$sql .= ' (providers.country = "'.$country.'" OR branches.country = "'.$country.'")';

		}

		

		if($city == '' && $category_id != '' && $country != ''){

		$texonomy = 'providers-category';
		$term_children = get_term_children($category_id,$texonomy);
		if(!empty($term_children)){
		$sql .= ' AND (';
			foreach($term_children as $term_child_id) {
				
				$sql .= ' FIND_IN_SET("'.$term_child_id.'", providers.category_id) OR ';
				
			}
		$sql .= ' FIND_IN_SET("'.$category_id.'", providers.category_id) ';	
		$sql .= ' ) AND';	
			
		}else{
		
		$sql .= 'AND FIND_IN_SET("'.$category_id.'", providers.category_id) AND';
		
		}
		
		$sql .= ' (providers.country = "'.$country.'" OR branches.country = "'.$country.'")';

		}
		
		if($distance != '' && $distance > 0 && $latitude != "" && $longitude != "" && $srhaddress != ""){
		
		$radiussearchunit = (isset($service_finder_options['radius-search-unit'])) ? esc_attr($service_finder_options['radius-search-unit']) : 'mi';
		
		if($radiussearchunit == 'mi'){
		$distance = floatval($distance) * 1.609344;
		}
		
		$radius = service_finder_radius_search($latitude,$longitude,$distance);
		
		$sql .= ' AND ((providers.lat <= '.$radius['latN'].' AND providers.lat >= '.$radius['latS'].' AND providers.long <= '.$radius['lonE'].' AND providers.long >= '.$radius['lonW'].') OR (branches.lat <= '.$radius['latN'].' AND branches.lat >= '.$radius['latS'].' AND branches.long <= '.$radius['lonE'].' AND branches.long >= '.$radius['lonW'].'))';

		}
		
		if($minprice != '' && $maxprice != '' && $maxprice > 0){
		$sql .= ' AND (services.cost BETWEEN '.$minprice.' AND '.$maxprice.')';

		}
		
		if($keyword != ''){
		$sql .= ' AND (providers.company_name LIKE "%'.$keyword.'%" OR providers.full_name LIKE "%'.$keyword.'%" OR providers.tagline LIKE "%'.$keyword.'%" OR providers.bio LIKE "%'.$keyword.'%" OR providers.booking_description LIKE "%'.$keyword.'%" OR services.service_name LIKE "%'.$keyword.'%" OR services.description LIKE "%'.$keyword.'%")';

		}
		
		$sql .= ' GROUP BY providers.wp_user_id';
		
		$providers_total = $wpdb->get_results($sql);
		$total = count($providers_total);
		
		$sortedproviders = array();
		$sortresult = array();
		
		$srhbybooking = (!empty($searchdata['srhbybooking'])) ? esc_attr($searchdata['srhbybooking']) : '';
		$srhdate = (!empty($searchdata['srhdate'])) ? esc_attr($searchdata['srhdate']) : '';
		$srhperiod = (!empty($searchdata['srhperiod'])) ? esc_attr($searchdata['srhperiod']) : '';
		$srhtime = (!empty($searchdata['srhtime'])) ? esc_attr($searchdata['srhtime']) : '';
		
		if($srhdate != "" || $srhperiod != "" || $srhtime != ""){
		if($srhperiod != 'any' && $srhperiod != ''){
		$srhperiod = service_finder_get_search_period($srhperiod);
		}
		
		$date = $srhdate;
		$starttime = (!empty($srhperiod['starttime'])) ? esc_attr($srhperiod['starttime']) : '';
		$endtime = (!empty($srhperiod['endtime'])) ? esc_attr($srhperiod['endtime']) : '';
		$srhperiod = $srhperiod;
		$minuts = $srhtime;
		
		$sortresult = service_finder_sort_by_availability($providers_total,$date,$starttime,$endtime,$minuts,$srhperiod);
		
		if(!empty($sortresult)){
			$sortedproviders = array_merge($sortresult['available'],$sortresult['unavailable']);	
			$sortedproviders = implode(',',$sortedproviders);
		}
		}
		
		if($srhbybooking != "" && $srhdate == "" && $srhperiod == "" && $srhtime == ""){
		
		$sortresult = service_finder_sort_by_booking_feature($providers_total);
		
		if(!empty($sortresult)){
			if($srhbybooking == 'on'){
			$sortedproviders = array_merge($sortresult['available'],$sortresult['unavailable']);	
			}else{
			$sortedproviders = array_merge($sortresult['unavailable'],$sortresult['available']);
			}
			$sortedproviders = implode(',',$sortedproviders);
		}
		}
		
		if(!empty($sortedproviders)){
			if($featuredontop){
				if($orderby == 'title'){
				$orderby = ' ORDER BY FIELD(providers.wp_user_id,'.$sortedproviders.'), providers.featured DESC,providers.full_name '.$order;
				}elseif($orderby == 'rating'){
				$orderby = ' ORDER BY FIELD(providers.wp_user_id,'.$sortedproviders.'), providers.featured DESC,providers.rating '.$order;
				}elseif($distance != '' && $distance > 0 && $latitude != "" && $longitude != "" && $address != ""){
				$orderby = ' ORDER BY FIELD(providers.wp_user_id,'.$sortedproviders.'), distance '.$order;	
				}else{
				$orderby = ' ORDER BY FIELD(providers.wp_user_id,'.$sortedproviders.'), providers.featured DESC,providers.id '.$order;
				}
			}else{
				if($orderby == 'title'){
				$orderby = ' ORDER BY FIELD(providers.wp_user_id,'.$sortedproviders.'), providers.full_name '.$order;
				}elseif($orderby == 'rating'){
				$orderby = ' ORDER BY FIELD(providers.wp_user_id,'.$sortedproviders.'), providers.rating '.$order;
				}elseif($distance != '' && $distance > 0 && $latitude != "" && $longitude != "" && $address != ""){
				$orderby = ' ORDER BY FIELD(providers.wp_user_id,'.$sortedproviders.'), distance '.$order;	
				}else{
				$orderby = ' ORDER BY FIELD(providers.wp_user_id,'.$sortedproviders.'), providers.id '.$order;
				}
			}
		}else{
			if($featuredontop){
				if($orderby == 'title'){
				$orderby = ' ORDER BY providers.featured DESC,providers.full_name '.$order;
				}elseif($orderby == 'rating'){
				$orderby = ' ORDER BY providers.featured DESC,providers.rating '.$order;
				}elseif($distance != '' && $distance > 0 && $latitude != "" && $longitude != "" && $address != ""){
				$orderby = ' ORDER BY distance '.$order;	
				}else{
				$orderby = ' ORDER BY providers.featured DESC,providers.id '.$order;
				}
			}else{
				if($orderby == 'title'){
				$orderby = ' ORDER BY providers.full_name '.$order;
				}elseif($orderby == 'rating'){
				$orderby = ' ORDER BY providers.rating '.$order;
				}elseif($distance != '' && $distance > 0 && $latitude != "" && $longitude != "" && $address != ""){
				$orderby = ' ORDER BY distance '.$order;	
				}else{
				$orderby = ' ORDER BY providers.id '.$order;
				}
			}
		}
		
		
		
		$sql .= $orderby.' LIMIT '.$start.', '.$per_page;
		
		$providers = $wpdb->get_results($sql);
		
		$res = array(
				'count' => $total,
				'srhResult' => $providers,
				'sortresult' => $sortresult,
				);

		return $res;

	}
	
	/*Get Providers Marker for Home Page*/

	public function service_finder_getProvidersMarkers($allproviders = '',$address = '',$city = '',$category_id = '',$country = ''){

		global $wpdb, $service_finder_Tables, $service_finder_options;
		$identitycheck = (isset($service_finder_options['identity-check'])) ? esc_attr($service_finder_options['identity-check']) : '';
		$restrictuserarea = (isset($service_finder_options['restrict-user-area'])) ? esc_attr($service_finder_options['restrict-user-area']) : '';
		if($allproviders == 'all' || ($city == '' && $category_id == '' && $country == '')){
		if($restrictuserarea && $identitycheck){
		$sql = 'SELECT providers.wp_user_id, providers.bio, providers.avatar_id, providers.full_name, providers.email, providers.phone, providers.lat, providers.long, providers.category_id, providers.country, providers.address, providers.city FROM '.$service_finder_Tables->providers.' as providers WHERE admin_moderation = "approved" AND identity = "approved" AND account_blocked != "yes"';
		}else{
		$sql = 'SELECT providers.wp_user_id, providers.bio, providers.avatar_id, providers.full_name, providers.email, providers.phone, providers.lat, providers.long, providers.category_id, providers.country, providers.address, providers.city FROM '.$service_finder_Tables->providers.' as providers WHERE admin_moderation = "approved" AND account_blocked != "yes"';
		}
		}elseif($address != ''){
		
		if($restrictuserarea && $identitycheck){
		$sql = 'SELECT providers.wp_user_id, providers.bio, providers.avatar_id, providers.full_name, providers.email, providers.phone, providers.lat, providers.long, providers.category_id, providers.country, providers.address, providers.city FROM '.$service_finder_Tables->providers.' as providers WHERE admin_moderation = "approved" AND identity = "approved" AND account_blocked != "yes" AND';
		}else{
		$sql = 'SELECT providers.wp_user_id, providers.bio, providers.avatar_id, providers.full_name, providers.email, providers.phone, providers.lat, providers.long, providers.category_id, providers.country, providers.address, providers.city FROM '.$service_finder_Tables->providers.' as providers WHERE admin_moderation = "approved" AND account_blocked != "yes" AND';
		}

		

		if($city != '' && $category_id != '' && $country != '' && $address != ''){

		$sql .= ' providers.city = "'.$city.'" AND';

		$texonomy = 'providers-category';
		$term_children = get_term_children($category_id,$texonomy);
		
		if(!empty($term_children)){
		$sql .= ' (';
			foreach($term_children as $term_child_id) {
				
				$sql .= ' FIND_IN_SET("'.$term_child_id.'", providers.category_id) OR ';
				
			}
		$sql .= ' FIND_IN_SET("'.$category_id.'", providers.category_id) ';	
		$sql .= ' ) AND';	
			
		}else{
		
		$sql .= ' FIND_IN_SET("'.$category_id.'", providers.category_id) AND';
		
		}
		
		$sql .= ' providers.address LIKE "%'.$address.'%" AND';
		
		$sql .= ' providers.country = "'.$country.'"';

		}
		
		if($city != '' && $category_id == '' && $country != '' && $address != ''){

		$sql .= ' providers.city = "'.$city.'" AND';

		$sql .= ' providers.address LIKE "%'.$address.'%" AND';
		
		$sql .= ' providers.country = "'.$country.'"';

		}

		}else{

		if($restrictuserarea && $identitycheck){
		$sql = 'SELECT providers.wp_user_id, providers.bio, providers.avatar_id, providers.full_name, providers.email, providers.phone, providers.lat, providers.long, providers.category_id, providers.country, providers.address, providers.city FROM '.$service_finder_Tables->providers.' as providers WHERE admin_moderation = "approved" AND identity = "approved" AND account_blocked != "yes" AND';
		}else{
		$sql = 'SELECT providers.wp_user_id, providers.bio, providers.avatar_id, providers.full_name, providers.email, providers.phone, providers.lat, providers.long, providers.category_id, providers.country, providers.address, providers.city FROM '.$service_finder_Tables->providers.' as providers WHERE admin_moderation = "approved" AND account_blocked != "yes" AND';
		}

		if($city != '' && $category_id != '' && $country != ''){

		$sql .= ' providers.city = "'.$city.'" AND';

		$texonomy = 'providers-category';
		$term_children = get_term_children($category_id,$texonomy);
		
		if(!empty($term_children)){
		$sql .= ' (';
			foreach($term_children as $term_child_id) {
				
				$sql .= ' FIND_IN_SET("'.$term_child_id.'", providers.category_id) OR ';
				
			}
		$sql .= ' FIND_IN_SET("'.$category_id.'", providers.category_id) ';	
		$sql .= ' ) AND';	
			
		}else{
		
		$sql .= ' FIND_IN_SET("'.$category_id.'", providers.category_id) AND';
		
		}
		
		$sql .= ' providers.country = "'.$country.'"';

		}

		

		if($city != '' && $category_id == '' && $country == ''){

		$sql .= ' providers.city = "'.$city.'" ';

		}

		

		if($category_id != '' && $city == '' && $country == ''){

		$texonomy = 'providers-category';
		$term_children = get_term_children($category_id,$texonomy);
		
		if(!empty($term_children)){
		$sql .= ' (';
			foreach($term_children as $term_child_id) {
				
				$sql .= ' FIND_IN_SET("'.$term_child_id.'", providers.category_id) OR ';
				
			}
		$sql .= ' FIND_IN_SET("'.$category_id.'", providers.category_id) ';	
		$sql .= ' )';	
			
		}else{
		
		$sql .= ' FIND_IN_SET("'.$category_id.'", providers.category_id)';
		
		}

		}

		

		if($category_id == '' && $city == '' && $country != ''){

		$sql .= ' providers.country = "'.$country.'"';

		}

		

		if($city != '' && $category_id != '' && $country == ''){

		$sql .= ' providers.city = "'.$city.'" AND';

		$texonomy = 'providers-category';
		$term_children = get_term_children($category_id,$texonomy);
		
		if(!empty($term_children)){
		$sql .= ' (';
			foreach($term_children as $term_child_id) {
				
				$sql .= ' FIND_IN_SET("'.$term_child_id.'", providers.category_id) OR ';
				
			}
		$sql .= ' FIND_IN_SET("'.$category_id.'", providers.category_id) ';	
		$sql .= ' )';	
			
		}else{
		
		$sql .= ' FIND_IN_SET("'.$category_id.'", providers.category_id)';
		
		}

		}

		

		if($city != '' && $category_id == '' && $country != ''){

		$sql .= ' providers.city = "'.$city.'" AND';

		$sql .= ' providers.country = "'.$country.'"';

		}

		

		if($city == '' && $category_id != '' && $country != ''){

		$texonomy = 'providers-category';
		$term_children = get_term_children($category_id,$texonomy);
		
		if(!empty($term_children)){
		$sql .= ' (';
			foreach($term_children as $term_child_id) {
				
				$sql .= ' FIND_IN_SET("'.$term_child_id.'", providers.category_id) OR ';
				
			}
		$sql .= ' FIND_IN_SET("'.$category_id.'", providers.category_id) ';	
		$sql .= ' ) AND';	
			
		}else{
		
		$sql .= ' FIND_IN_SET("'.$category_id.'", providers.category_id) AND';
		
		}

		$sql .= ' providers.country = "'.$country.'"';

		}
		}
		
		$providers = $wpdb->get_results($sql);
		$total = count($providers);
		
		$res = array(
				'count' => $total,
				'srhResult' => $providers
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