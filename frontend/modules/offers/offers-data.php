<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/

/*Add offers*/
add_action('wp_ajax_add_offers', 'service_finder_add_offers');
function service_finder_add_offers(){
global $wpdb, $service_finder_Tables;

$data = array(
		'wp_user_id' => (!empty($_POST['user_id'])) ? esc_attr($_POST['user_id']) : 0,
		'offer_title' => (!empty($_POST['offer_title'])) ? esc_attr($_POST['offer_title']) : '',
		'coupon_code' => (!empty($_POST['coupon_code'])) ? esc_attr($_POST['coupon_code']) : '',
		'expiry_date' => (!empty($_POST['expiry_date'])) ? esc_attr($_POST['expiry_date']) : '',
		'max_coupon' => (!empty($_POST['max_coupon'])) ? esc_attr($_POST['max_coupon']) : '',
		'discount_type' => (!empty($_POST['discount_type'])) ? esc_attr($_POST['discount_type']) : '',
		'discount_value' => (!empty($_POST['discount_value'])) ? esc_attr($_POST['discount_value']) : '',
		'discount_description' => (!empty($_POST['discount_description'])) ? $_POST['discount_description'] : '',
		);

$wpdb->insert($service_finder_Tables->offers,wp_unslash($data));

$offer_id = $wpdb->insert_id;

if ( ! $offer_id ) {
	$error = array(
			'status' => 'error',
			'err_message' => esc_html__('Couldn&#8217;t add offer.', 'service-finder')
			);
	echo json_encode($error);
}else{
	$success = array(
			'status' => 'success',
			'suc_message' => esc_html__('Add offer successfully.', 'service-finder'),
			);
	echo json_encode($success);
}
exit;
}

/*Get offers*/
add_action('wp_ajax_get_offers', 'service_finder_get_offers');
function service_finder_get_offers(){
global $wpdb, $service_finder_Tables;

$requestData= $_REQUEST;
$currUser = wp_get_current_user(); 
$columns = array( 
	0 =>'id', 
	1 =>'offer_title', 
	2 =>'coupon_code', 
	3 =>'discount_value', 
	3 =>'expiry_date', 
	4 =>'max_coupon', 
	5 =>'current_job', 
);

$user_id = (!empty($_POST['user_id'])) ? esc_attr($_POST['user_id']) : '';

// getting total number records without any search
$sql = $wpdb->prepare("SELECT * FROM ".$service_finder_Tables->offers. " WHERE `wp_user_id` = %d",$user_id);
$query=$wpdb->get_results($sql);
$totalData = count($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

$sql = "SELECT * FROM ".$service_finder_Tables->offers. " WHERE `wp_user_id` = ".$user_id;
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND ( `offer_title` LIKE '".$requestData['search']['value']."%' ";    
	$sql.=" OR `coupon_code` LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR `expiry_date` LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR `max_coupon` LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR `discount_type` LIKE '".$requestData['search']['value']."%'";    
	$sql.=" OR `discount_value` LIKE '".$requestData['search']['value']."%' )";    
}
$query=$wpdb->get_results($sql);
$totalFiltered = count($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
$query=$wpdb->get_results($sql);
$data = array();

foreach($query as $result){
	$nestedData=array(); 

	$nestedData[] = '<div class="checkbox sf-radio-checkbox">
	  <input type="checkbox" id="offer-'.$result->id.'" class="deleteOffersRow" value="'.esc_attr($result->id).'">
	  <label for="offer-'.$result->id.'"></label>
	</div>';
	
	$nestedData[] = $result->offer_title;
	$nestedData[] = $result->coupon_code;
	$discount = '';
	if($result->discount_type == 'fixed'){
		$discount = service_finder_money_format($result->discount_value);
	}elseif($result->discount_type == 'percentage'){
		$discount = service_finder_percentage_format($result->discount_value);
	}
	$nestedData[] = $discount;
	$nestedData[] = $result->expiry_date;
	$nestedData[] = $result->max_coupon;
	$nestedData[] = '<button title="'.esc_html__('Edit Offers', 'service-finder').'" data-id="'.esc_attr($result->id).'" class="btn btn-primary btn-xs editOffers" type="button">'.esc_html__('Edit', 'service-finder').'</button>';
	
	$data[] = $nestedData;
}



$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
			);

echo json_encode($json_data);  // send data as json format

exit;
}

/*Load offers*/
add_action('wp_ajax_load_offers', 'service_finder_load_offers');
function service_finder_load_offers(){
global $wpdb, $service_finder_Tables;		

$offerid = (!empty($_POST['offerid'])) ? esc_attr($_POST['offerid']) : 0;

$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->offers.' WHERE `ID` = %d',$offerid));

if(!empty($row)){
	$result = array(
		'offer_title' => (!empty($row->offer_title)) ? $row->offer_title : '',
		'coupon_code' => (!empty($row->coupon_code)) ? $row->coupon_code : '',
		'expiry_date' => (!empty($row->expiry_date)) ? $row->expiry_date : '',
		'max_coupon' => (!empty($row->max_coupon)) ? $row->max_coupon : '',
		'discount_type' => (!empty($row->discount_type)) ? $row->discount_type : '',
		'discount_value' => (!empty($row->discount_value)) ? $row->discount_value : '',
		'discount_description' => (!empty($row->discount_description)) ? $row->discount_description : '',
	);

	echo json_encode($result);
}
exit;
}

/*Update offers*/
add_action('wp_ajax_update_offers', 'service_finder_update_offers');
function service_finder_update_offers(){
global $wpdb, $service_finder_Tables;

$data = array(
		'offer_title' => (!empty($_POST['offer_title'])) ? esc_attr($_POST['offer_title']) : '',
		'coupon_code' => (!empty($_POST['coupon_code'])) ? esc_attr($_POST['coupon_code']) : '',
		'expiry_date' => (!empty($_POST['expiry_date'])) ? esc_attr($_POST['expiry_date']) : '',
		'max_coupon' => (!empty($_POST['max_coupon'])) ? esc_attr($_POST['max_coupon']) : '',
		'discount_type' => (!empty($_POST['discount_type'])) ? esc_attr($_POST['discount_type']) : '',
		'discount_value' => (!empty($_POST['discount_value'])) ? esc_attr($_POST['discount_value']) : '',
		'discount_description' => (!empty($_POST['edit_discount_description'])) ? $_POST['edit_discount_description'] : '',
		);

$where = array(
			'id' => (!empty($_POST['offerid'])) ? esc_attr($_POST['offerid']) : ''
			);

$service_id = $wpdb->update($service_finder_Tables->offers,wp_unslash($data),$where);

$success = array(
		'status' => 'success',
		'suc_message' => esc_html__('Update offer successfully.', 'service-finder'),
		);
echo json_encode($success);
	
exit;
}

/*Delete offers*/
add_action('wp_ajax_delete_offers', 'service_finder_delete_offers');
function service_finder_delete_offers(){
global $wpdb, $service_finder_Tables;
$currUser = wp_get_current_user(); 
$data_ids = $_REQUEST['data_ids'];
$data_id_array = explode(",", $data_ids); 
if(!empty($data_id_array)) {
	foreach($data_id_array as $id) {
		$sql = $wpdb->prepare("DELETE FROM ".$service_finder_Tables->offers." WHERE id = %d",$id);
		$query=$wpdb->query($sql);
	}
}
exit;
}