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

class SERVICE_FINDER_Favorites{
	
	/*Display invoice into datatable*/
	public function service_finder_getFavorites(){
		global $wpdb, $service_finder_Tables;
		$requestData= $_REQUEST;
		$currUser = wp_get_current_user(); 

		$invoices = $wpdb->
get_results('SELECT providers.wp_user_id, providers.full_name, providers.category_id, favorites.id, favorites.favorite, favorites.user_id FROM '.$service_finder_Tables->favorites.' as favorites INNER JOIN '.$service_finder_Tables->providers.' as providers on providers.wp_user_id = favorites.provider_id WHERE favorites.favorite = "yes" AND favorites.user_id = '.$currUser->ID);
		
		$columns = array( 
			0 =>'full_name', 
			1 =>'full_name',
			2 =>'full_name',
			3 =>'full_name'
		);
		// getting total number records without any search
		$sql = $wpdb->prepare('SELECT providers.wp_user_id, providers.full_name, providers.category_id, favorites.id, favorites.favorite, favorites.user_id FROM '.$service_finder_Tables->favorites.' as favorites INNER JOIN '.$service_finder_Tables->providers.' as providers on providers.wp_user_id = favorites.provider_id WHERE favorites.favorite = "yes" AND  favorites.user_id = %d',$currUser->ID);
		$query=$wpdb->get_results($sql);
		$totalData = count($query);
		$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
		$sql='SELECT providers.wp_user_id, providers.full_name, providers.category_id, favorites.id, favorites.favorite, favorites.user_id FROM '.$service_finder_Tables->favorites.' as favorites INNER JOIN '.$service_finder_Tables->providers.' as providers on providers.wp_user_id = favorites.provider_id WHERE favorites.favorite = "yes" AND  favorites.user_id = '.$currUser->ID;
		
		if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
			$sql.=" AND ( providers.full_name LIKE '".$requestData['search']['value']."%' )";    
		}
		$query=$wpdb->get_results($sql);
		$totalFiltered = count($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		$query=$wpdb->get_results($sql);
		
		$data = array();

		foreach($query as $result){
			$nestedData=array(); 
		

			$nestedData[] = '
<div class="checkbox sf-radio-checkbox">
  <input type="checkbox" id="favorites-'.esc_attr($result->id).'" class="deleteFavoritesRow" value="'.esc_attr($result->id).'">
  <label for="favorites-'.esc_attr($result->id).'"></label>
</div>
';
			
			$nestedData[] = $result->full_name;
			$nestedData[] = service_finder_getCategoryName(get_user_meta($result->wp_user_id,'primary_category',true));
			$nestedData[] = '<a href="'.esc_url(service_finder_get_author_url($result->wp_user_id)).'">'.esc_html__('View Profile', 'service-finder').'</a>';
			
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
	
	/*Delete Favorites*/
	public function service_finder_deleteFavorites(){
	global $wpdb, $service_finder_Tables;
			$data_ids = $_REQUEST['data_ids'];
			$data_id_array = explode(",", $data_ids); 
			if(!empty($data_id_array)) {
				foreach($data_id_array as $id) {
					$sql = $wpdb->prepare("DELETE FROM ".$service_finder_Tables->favorites." WHERE id = %d",$id);
					$query=$wpdb->query($sql);
				}
			}
	}
	
}