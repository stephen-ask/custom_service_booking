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

class SERVICE_FINDER_Invoice{
	
	/*Display invoice into datatable*/
	public function service_finder_getInvoice($arg){
		global $wpdb, $service_finder_Tables;
		$requestData= $_REQUEST;
		$currUser = wp_get_current_user(); 
		$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';
		$invoices = $wpdb->get_results($wpdb->prepare('SELECT invoice.customer_email, invoice.paid_to_provider, invoice.booking_id, invoice.adminfee, invoice.payment_type, invoice.id, invoice.reference_no, invoice.duedate, invoice.grand_total, invoice.status, invoice.txnid FROM '.$service_finder_Tables->invoice.' as invoice WHERE `invoice`.`provider_id` = %d',$user_id));
		
		$columns = array( 
			0 =>'id', 
			1 =>'reference_no', 
			2 =>'name', 
			3 => 'duedate',
			4 =>'grand_total', 
			5=> 'status',
		);
		
		// getting total number records without any search
		$sql = $wpdb->prepare('SELECT invoice.customer_email, invoice.adminfee, invoice.id, invoice.paid_to_provider, invoice.booking_id, invoice.payment_type, invoice.reference_no, invoice.duedate, invoice.grand_total, invoice.status, invoice.txnid FROM '.$service_finder_Tables->invoice.' as invoice WHERE `invoice`.`provider_id` = %d',$user_id);
		$query=$wpdb->get_results($sql);
		$totalData = count($query);
		$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
		$sql = "SELECT invoice.customer_email, invoice.adminfee, invoice.id, invoice.paid_to_provider, invoice.reference_no, invoice.booking_id, invoice.payment_type, invoice.duedate, invoice.grand_total, invoice.status, invoice.txnid";
		$sql.=" FROM ".$service_finder_Tables->invoice." as invoice WHERE `invoice`.`provider_id` = ".$user_id;
		
		if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
			$sql.=" AND ( invoice.reference_no LIKE '".$requestData['search']['value']."%' ";
			$sql.=" OR invoice.duedate LIKE '".$requestData['search']['value']."%' ";
			$sql.=" OR invoice.grand_total LIKE '".$requestData['search']['value']."%' ";
			$sql.=" OR invoice.status LIKE '".$requestData['search']['value']."%' )";
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
  <input type="checkbox" id="invoice-'.esc_attr($result->id).'" class="deleteInvoiceRow" value="'.esc_attr($result->id).'">
  <label for="invoice-'.esc_attr($result->id).'"></label>
</div>';
			
			$q = $wpdb->get_row($wpdb->prepare('SELECT name FROM '.$service_finder_Tables->customers.' WHERE `email` = "%s" GROUP BY email',$result->customer_email));
			$nestedData[] = $result->reference_no;
			$nestedData[] = $q->name;
			$nestedData[] = service_finder_date_format($result->duedate);
			$nestedData[] = service_finder_translate_static_status_string($result->payment_type);
			$nestedData[] = service_finder_money_format($result->grand_total);
			$nestedData[] = service_finder_money_format($result->adminfee);
			$nestedData[] = service_finder_money_format(($result->grand_total - $result->adminfee));
			
			$nestedData[] = service_finder_translate_static_status_string($result->paid_to_provider);;
			
			$now = time();
			$date = $result->duedate;
			
			if($result->status == 'pending' && strtotime($date) < $now){
				$status = esc_html__('Overdue', 'service-finder');
			}else{
				$status = service_finder_translate_static_status_string($result->status);
			}
			
			$nestedData[] = $status;
			if($result->booking_id > 0){
			$nestedData[] = '<a href="javascript:;" data-id="'.esc_attr($result->booking_id).'" data-upcoming="no" class="viewbookingdeatils">#'.$result->booking_id.'</a>';
			}else{
			$nestedData[] = '-';
			}
			$nestedData[] = $result->txnid;
			if($result->status != 'paid'){
			$reminder = '
<button type="button" class="btn btn-primary btn-xs sendReminder" data-id="'.esc_attr($result->id).'" title="'.esc_html__('Send Reminder', 'service-finder').'"><i class="fa fa-envelope"></i></button>
';
			
			$editbtn = '
<button type="button" data-id="'.esc_attr($result->id).'" class="btn btn-warning btn-xs editInvoice margin-r-5" title="'.esc_html__('Edit Invoice', 'service-finder').'"><i class="fa fa-pencil"></i></button>
';
			}else{
			$reminder = '';
			$editbtn = '';
			}
			$nestedData[] = $editbtn.'
<button type="button" class="btn btn-custom btn-xs viewInvoice" data-id="'.esc_attr($result->id).'" title="'.esc_html__('View Invoice', 'service-finder').'"><i class="fa fa-eye"></i></button>
'.$reminder;

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
	
	/*Delete Invoice*/
	public function service_finder_deleteInvoice(){
	global $wpdb, $service_finder_Tables;
			$data_ids = $_REQUEST['data_ids'];
			$data_id_array = explode(",", $data_ids); 
			if(!empty($data_id_array)) {
				foreach($data_id_array as $id) {
					$sql = $wpdb->prepare("DELETE FROM ".$service_finder_Tables->invoice." WHERE id = %d",$id);
					$query=$wpdb->query($sql);
				}
			}
	}
	
	/*View Invoice*/
	public function service_finder_viewInvoice(){
	
		$invoiceid = (isset($_REQUEST['invoiceid'])) ? esc_attr($_REQUEST['invoiceid']) : '';
		echo $html = '<div class="margin-b-30 text-right">
				  <button type="button" class="btn btn-primary closeInvoiceDetails"><i class="fa fa-arrow-left"></i>'.esc_html__('Back', 'service-finder').'</button>
				</div>';
		echo service_finder_view_invoice($invoiceid);
		
	}
	
	/*View Customer Invoice*/
	public function service_finder_viewCustomerInvoice(){
		global $wpdb, $service_finder_Tables, $service_finder_Params, $service_finder_options, $paymentsystem, $current_user;
		$woopayment = (isset($service_finder_options['woocommerce-payment'])) ? esc_html($service_finder_options['woocommerce-payment']) : false;
		
		$paymentsystem = ($woopayment) ? 'woocommerce' : 'local';
		
		$invoiceid = $_REQUEST['invoiceid'];
		
		$servicehtml = '';
	
		$sql = $wpdb->prepare("SELECT invoice.id, invoice.provider_id, invoice.reference_no, invoice.duedate, invoice.booking_id, invoice.discount_type, invoice.tax_type, invoice.discount, invoice.tax, invoice.services, invoice.description as invoicedes, invoice.total, invoice.grand_total, invoice.status, customers.name, customers.phone as cusphone, customers.phone2 as cusphone2, customers.email as cusemail, customers.address as cusaddress, customers.apt as cusapt, customers.city as cuscity, customers.state as cusstate, customers.zipcode as cuszipcode, customers.description, providers.full_name, providers.phone, providers.email, providers.mobile, providers.fax, providers.address, providers.apt, providers.city, providers.state, providers.zipcode, providers.country FROM ".$service_finder_Tables->invoice." as invoice INNER JOIN ".$service_finder_Tables->customers." as customers on invoice.customer_email = customers.email LEFT JOIN ".$service_finder_Tables->providers." as providers on invoice.provider_id = providers.wp_user_id WHERE invoice.id = %d",$invoiceid);
	
		$row = $wpdb->get_row($sql);
		
		$discount_type = $row->discount_type;
			$tax_type = $row->tax_type;
			if($row->discount > 0){
			if($discount_type == 'fix'){
				$displaydiscount = $row->discount;
			}elseif($discount_type == 'percentage'){
				$discountedprice = $row->total - $displaydiscount;
				$displaytax = $discountedprice * ($row->tax/100);
			}
			}else{
				$displaydiscount = '0.00';
			}
			
			if($row->tax > 0){
			if($tax_type == 'fix'){
				$displaytax = $row->tax;
			}elseif($tax_type == 'percentage'){
				$displaytax = $row->total * ($row->tax/100);
			}
			}else{
				$displaytax = '0.00';
			}
		
		$services = unserialize($row->services);
		
		if(!empty($services)){
			foreach($services as $key => $value){
			if($value[0] == 'new'){
				$servicename = 'Extra Service';
			}else{
				$servicedata = service_finder_getServiceData($value[0]);
				$service_name = (!empty($servicedata->service_name)) ? $servicedata->service_name : '';
				$servicename = stripcslashes($service_name);
			}
			
			
			if($value[1] == 'fix'){
				$hrs = 'N/A';
			}else{
				$hrs = $value[2];
			}
			
			$servicehtml .= '
<tr>
  <td>'.(esc_html($key)+1).'</td>
  <td>'.esc_html($servicename).'</td>
  <td>'.esc_html($value[1]).'</td>
  <td>'.esc_html($hrs).'</td>
  <td>'.esc_html($value[3]).'</td>
  <td>'.esc_html($value[4]).'</td>
</tr>
';
			}
		}
											$year = date('Y');
											$yearoption = '';
                                            for($i = $year;$i<=$year+50;$i++){
												$yearoption .= '<option value="'.esc_attr($i).'">'.$i.'</option>';
											}
$settings = service_finder_getProviderSettings($row->provider_id);
$availablepaymentmethod = '';
$flag = 0;
$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? esc_html($service_finder_options['pay_booking_amount_to']) : '';
$payment_methods = (!empty($service_finder_options['payment-methods'])) ? $service_finder_options['payment-methods'] : '';

if($pay_booking_amount_to == 'admin'){
if($payment_methods['paypal']){
	$checkpaypal = true;
}else{
	$checkpaypal = false;
}
}elseif($pay_booking_amount_to == 'provider'){
if(!empty($settings['paymentoption'])){
if(in_array('paypal',$settings['paymentoption'])){
	$checkpaypal = true;
}else{
	$checkpaypal = false;
}
}else{
	$checkpaypal = false;
}
}

if($checkpaypal){ 

$availablepaymentmethod .= '
<div class="radio sf-radio-checkbox">
<input type="radio" value="paypal" name="invoicepayment_mode" id="invoicepaypal" >
<label for="invoicepaypal"><img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/paypal.jpg" title="'.esc_html__('Paypal','service-finder').'" alt="'.esc_html__('paypal','service-finder').'"></label>
</div>
';
$flag = 1;
}

if($pay_booking_amount_to == 'admin'){
if($payment_methods['wired']){
	$checkwired = true;
}else{
	$checkwired = false;
}
}elseif($pay_booking_amount_to == 'provider'){
if(!empty($settings['paymentoption'])){
if(in_array('wired',$settings['paymentoption'])){
	$checkwired = true;
}else{
	$checkwired = false;
}
}else{
	$checkwired = false;
}
}

if($checkwired){ 

$availablepaymentmethod .= '
<div class="radio sf-radio-checkbox">
<input type="radio" value="wired" name="invoicepayment_mode" id="invoicewired" >
<label for="invoicewired"><img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/wired.jpg" title="'.esc_html__('Wire Transfer','service-finder').'" alt="'.esc_html__('wired','service-finder').'"></label>
</div>
';
$flag = 1;
}

if($pay_booking_amount_to == 'admin'){
if($payment_methods['payumoney']){
	$checkpayumoney = true;
}else{
	$checkpayumoney = false;
}
}elseif($pay_booking_amount_to == 'provider'){
if(!empty($settings['paymentoption'])){
if(in_array('payumoney',$settings['paymentoption'])){
	$checkpayumoney = true;
}else{
	$checkpayumoney = false;
}
}else{
	$checkpayumoney = false;
}
}

if($checkpayumoney){ 

$availablepaymentmethod .= '
<div class="radio sf-radio-checkbox">
<input type="radio" value="payumoney" name="invoicepayment_mode" id="invoicepayumoney" >
<label for="invoicepayumoney"><img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/payumoney.jpg" title="'.esc_html__('PayU Money','service-finder').'" alt="'.esc_html__('PayU Money','service-finder').'"></label>
</div>
';
$flag = 1;
}

if($pay_booking_amount_to == 'admin'){
if($payment_methods['payulatam']){
	$checkpayulatam = true;
}else{
	$checkpayulatam = false;
}
}elseif($pay_booking_amount_to == 'provider'){
if(!empty($settings['paymentoption'])){
if(in_array('payulatam',$settings['paymentoption'])){
	$checkpayulatam = true;
}else{
	$checkpayulatam = false;
}
}else{
	$checkpayulatam = false;
}
}

if($checkpayulatam){ 

$availablepaymentmethod .= '
<div class="radio sf-radio-checkbox">
<input type="radio" value="payulatam" name="invoicepayment_mode" id="invoicepayulatam" >
<label for="invoicepayulatam"><img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/payulatam.jpg" title="'.esc_html__('PayU Latam','service-finder').'" alt="'.esc_html__('PayU Latam','service-finder').'"></label>
</div>
';
$flag = 1;
}

if($pay_booking_amount_to == 'admin'){
if($payment_methods['stripe']){
$checkstripe = true;
}else{
$checkstripe = false;
}
}elseif($pay_booking_amount_to == 'provider'){
if(!empty($settings['paymentoption'])){
if(in_array('stripe',$settings['paymentoption'])){
$checkstripe = true;
}else{
$checkstripe = false;
}
}else{
$checkstripe = false;
}
}

if($checkstripe){
$availablepaymentmethod .= '
<div class="radio sf-radio-checkbox">
<input type="radio" value="stripe" name="invoicepayment_mode" id="invoicestripe">
<label for="invoicestripe"><img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/mastercard.jpg" title="'.esc_html__('Stripe','service-finder').'" alt="'.esc_html__('mastercard','service-finder').'"><img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/payment.jpg" title="'.esc_html__('Stripe','service-finder').'" alt="'.esc_html__('american express','service-finder').'"><img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/discover.jpg" title="'.esc_html__('Stripe','service-finder').'" alt="'.esc_html__('discover','service-finder').'"><img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/visa.jpg" title="'.esc_html__('Stripe','service-finder').'" alt="'.esc_html__('visa','service-finder').'"></label>
</div>
';
$flag = 1;
}
											

if($pay_booking_amount_to == 'admin'){
if($payment_methods['twocheckout']){
	$checktwocheckout = true;
}else{
	$checktwocheckout = false;
}
}elseif($pay_booking_amount_to == 'provider'){
if(!empty($settings['paymentoption'])){
if(in_array('twocheckout',$settings['paymentoption'])){
	$checktwocheckout = true;
}else{
	$checktwocheckout = false;
}
}else{
	$checktwocheckout = false;
}
}

if($checktwocheckout){
$availablepaymentmethod .= '
<div class="radio sf-radio-checkbox">
<input type="radio" value="twocheckout" name="invoicepayment_mode" id="invoicetwocheckout">
<label for="invoicetwocheckout"><img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/twocheckout.jpg" alt="'.esc_html__('2Checkout','service-finder').'"></label>
</div>
';
$flag = 1;
}

$availablepaymentmethod .= service_finder_add_wallet_option('invoicepayment_mode','invoice');

		$payform = '';												
		if($row->status != 'paid' && $row->status != 'on-hold' && $row->status != 'canceled'){
		if($paymentsystem == 'woocommerce'){
			$flag = 1;
			$payform .= service_finder_display_wallet_amount($current_user->ID);
			$payform .= '<div class="col-lg-12">
				  <div class="form-group form-inline">';
			$payform .= service_finder_add_wallet_option('invoice_woopayment','invoice');
			$payform .= service_finder_add_woo_commerce_option('invoice_woopayment','invoice');
			$payform .= '</div></div>';
		}
		}
		if($flag == 1 || service_finder_check_wallet_system()){
		$paybtn = '<input name="invoicepayment" id="invoicepayment" type="submit" value="'.esc_html__('Pay Now', 'service-finder').'" class="btn btn-primary center-block">';
		}else{
		$paybtn = '<div>'.esc_html__('No Payment Method Available', 'service-finder').'</div>';
		}	
		
		if($row->status != 'paid' && $row->status != 'on-hold' && $row->status != 'canceled'){
		$payform .= '<form class="myform pay-now" method="post">';
		if($paymentsystem != 'woocommerce'){
		$walletamount = service_finder_get_wallet_amount($current_user->ID);
		$payform .= service_finder_display_wallet_amount($current_user->ID);
		
		$description = (!empty($service_finder_options['wire-transfer-description'])) ? $service_finder_options['wire-transfer-description'] : '';
		
		$payform .= '<div class="col-md-12">
					<div class="form-group form-inline">
					  <div class="col-md-12 sf-card-group">
						<div class="form-group form-inline"> '.$availablepaymentmethod.' </div>
					  </div>
					</div>
				  </div>';
		}		  
  $payform .= '<div id="invoicecardinfo" class="default-hidden">
    <div class="col-md-8">
      <div class="form-group">
        <label>'.esc_html__('Card Number', 'service-finder').'</label>
        <div class="input-group"> <i class="input-group-addon fa fa-credit-card"></i>
          <input type="text" id="card_number" name="card_number" class="form-control sf-form-control">
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label>'.esc_html__('CVC', 'service-finder').'</label>
        <div class="input-group"> <i class="input-group-addon fa fa-ellipsis-h"></i>
          <input type="text" id="card_cvc" name="card_cvc" class="form-control sf-form-control">
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group has-select">
        <label>'.esc_html__('Select Month', 'service-finder').'</label>
        <select id="card_month" name="card_month" class="form-control sf-form-control sf-select-box" title="Select Month">
          <option value="1">'.esc_html__('January', 'service-finder').'</option>
		  <option value="2">'.esc_html__('February', 'service-finder').'</option>
		  <option value="3">'.esc_html__('March', 'service-finder').'</option>
		  <option value="4">'.esc_html__('April', 'service-finder').'</option>
		  <option value="5">'.esc_html__('May', 'service-finder').'</option>
		  <option value="6">'.esc_html__('June', 'service-finder').'</option>
		  <option value="7">'.esc_html__('July', 'service-finder').'</option>
		  <option value="8">'.esc_html__('August', 'service-finder').'</option>
		  <option value="9">'.esc_html__('September', 'service-finder').'</option>
		  <option value="10">'.esc_html__('October', 'service-finder').'</option>
		  <option value="11">'.esc_html__('November', 'service-finder').'</option>
		  <option value="12">'.esc_html__('December', 'service-finder').'</option>
        </select>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group has-select">
        <label>'.esc_html__('Select Year', 'service-finder').'</label>
        <select id="card_year" name="card_year" class="form-control sf-form-control sf-select-box"  title="Select Year">
          
                                            '.$yearoption.'
                                          
        </select>
      </div>
    </div>
  </div>
  <div id="invoicewiredinfo" class="default-hidden">
                    <div class="col-md-12 margin-b-20">
                        '.$description.'
                    </div>
                  </div>
  <div id="payulataminvoicecardinfo" class="default-hidden">
    <div class="col-md-12">
	  <div class="form-group">
		<label>
		'.esc_html__('Select Card', 'service-finder').'
		</label>
	   <select id="payulatam_invoice_cardtype" name="payulatam_invoice_cardtype" class="form-control sf-form-control sf-select-box"  title="'.esc_html__('Select Card', 'service-finder').'">';
		  $country = (isset($service_finder_options['payulatam-country'])) ? $service_finder_options['payulatam-country'] : '';
		  $cards = service_finder_get_cards($country);
		  foreach($cards as $card){
			$payform .= '<option value="'.esc_attr($card).'">'.$card.'</option>';
		  }
		$payform .= '</select>
	  </div>
	</div>
	<div class="col-md-8">
      <div class="form-group">
        <label>'.esc_html__('Card Number', 'service-finder').'</label>
        <div class="input-group"> <i class="input-group-addon fa fa-credit-card"></i>
          <input type="text" id="payulatam_card_number" name="payulatam_card_number" class="form-control sf-form-control">
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label>'.esc_html__('CVC', 'service-finder').'</label>
        <div class="input-group"> <i class="input-group-addon fa fa-ellipsis-h"></i>
          <input type="text" id="payulatam_card_cvc" name="payulatam_card_cvc" class="form-control sf-form-control">
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group has-select">
        <label>'.esc_html__('Select Month', 'service-finder').'</label>
        <select id="payulatam_card_month" name="payulatam_card_month" class="form-control sf-form-control sf-select-box" title="'.esc_html__('Select Month', 'service-finder').'">
          <option value="01">'.esc_html__('January', 'service-finder').'</option>
		  <option value="02">'.esc_html__('February', 'service-finder').'</option>
		  <option value="03">'.esc_html__('March', 'service-finder').'</option>
		  <option value="04">'.esc_html__('April', 'service-finder').'</option>
		  <option value="05">'.esc_html__('May', 'service-finder').'</option>
		  <option value="06">'.esc_html__('June', 'service-finder').'</option>
		  <option value="07">'.esc_html__('July', 'service-finder').'</option>
		  <option value="08">'.esc_html__('August', 'service-finder').'</option>
		  <option value="09">'.esc_html__('September', 'service-finder').'</option>
		  <option value="10">'.esc_html__('October', 'service-finder').'</option>
		  <option value="11">'.esc_html__('November', 'service-finder').'</option>
		  <option value="12">'.esc_html__('December', 'service-finder').'</option>
        </select>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group has-select">
        <label>'.esc_html__('Select Year', 'service-finder').'</label>
        <select id="payulatam_card_year" name="payulatam_card_year" class="form-control sf-form-control sf-select-box"  title="'.esc_html__('Select Year', 'service-finder').'">
          
                                            '.$yearoption.'
                                          
        </select>
      </div>
    </div>
  </div>
   <div id="twocheckoutinvoicecardinfo" class="default-hidden">
    <div class="col-md-8">
      <div class="form-group">
        <label>'.esc_html__('Card Number', 'service-finder').'</label>
        <div class="input-group"> <i class="input-group-addon fa fa-credit-card"></i>
          <input type="text" id="twocheckout_card_number" name="twocheckout_card_number" class="form-control sf-form-control">
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label>'.esc_html__('CVC', 'service-finder').'</label>
        <div class="input-group"> <i class="input-group-addon fa fa-ellipsis-h"></i>
          <input type="text" id="twocheckout_card_cvc" name="twocheckout_card_cvc" class="form-control sf-form-control">
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group has-select">
        <label>'.esc_html__('Select Month', 'service-finder').'</label>
        <select id="twocheckout_card_month" name="twocheckout_card_month" class="form-control sf-form-control sf-select-box" title="Select Month">
          <option value="1">'.esc_html__('January', 'service-finder').'</option>
		  <option value="2">'.esc_html__('February', 'service-finder').'</option>
		  <option value="3">'.esc_html__('March', 'service-finder').'</option>
		  <option value="4">'.esc_html__('April', 'service-finder').'</option>
		  <option value="5">'.esc_html__('May', 'service-finder').'</option>
		  <option value="6">'.esc_html__('June', 'service-finder').'</option>
		  <option value="7">'.esc_html__('July', 'service-finder').'</option>
		  <option value="8">'.esc_html__('August', 'service-finder').'</option>
		  <option value="9">'.esc_html__('September', 'service-finder').'</option>
		  <option value="10">'.esc_html__('October', 'service-finder').'</option>
		  <option value="11">'.esc_html__('November', 'service-finder').'</option>
		  <option value="12">'.esc_html__('December', 'service-finder').'</option>
        </select>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group has-select">
        <label>'.esc_html__('Select Year', 'service-finder').'</label>
        <select id="twocheckout_card_year" name="twocheckout_card_year" class="form-control sf-form-control sf-select-box"  title="Select Year">
          
                                            '.$yearoption.'
                                          
        </select>
      </div>
    </div>
  </div>
  <input type="hidden" name="email" id="email" value="'.esc_attr($row->cusemail).'">
  <input type="hidden" name="amount" id="amount" value="'.esc_attr(ceil($row->grand_total)).'">
  <input type="hidden" name="provider" id="provider" value="'.esc_attr($row->provider_id).'">
  <input type="hidden" name="invoiceid" id="invoiceid" value="'.esc_attr($invoiceid).'">
  '.$paybtn.'
</form>
';
		
		}elseif($row->status == 'on-hold'){
		$payform .= '<div class="alert alert-info">'.esc_html__('You have paid via wire transfer. Please wait once its approve.', 'service-finder').'</div>';
		}
		
		$now = time();
		$date = $row->duedate;
		
		if($row->status == 'pending' && strtotime($date) < $now){
			$status = esc_html__('Overdue', 'service-finder');
		}else{
			$status = service_finder_translate_static_status_string($row->status);
		}
		$cuszipcode = (!empty($row->cuszipcode)) ? $row->cuszipcode : '';
		$html = '
<div class="margin-b-30 text-right">
  <button type="button" class="btn btn-primary closeInvoiceDetails"><i class="fa fa-arrow-left"></i>'.esc_html__('Back', 'service-finder').'</button>
</div>
<div class="invoice-view">
  <div class="row">
    <div class="col-md-12"><span class="invoice-status">'.esc_html($status).'</span></div>
    <div class="col-md-6 col-sm-6">
      <h4>'.esc_html__('Invoice Manager', 'service-finder').'</h4>
      <table class="table">
        <tbody>
          <tr>
            <td>'.esc_html__('Name', 'service-finder').': '.esc_html($row->full_name).'</td>
          </tr>
          <tr>
            <td>'.esc_html__('Email', 'service-finder').': '.esc_html($row->email).'</td>
          </tr>
          <tr>
            <td>'.esc_html__('Phone', 'service-finder').': '.esc_html($row->phone).' '.esc_html($row->mobile).'</td>
          </tr>
          <tr>
            <td>'.esc_html__('Fax', 'service-finder').': '.esc_html($row->fax).'</td>
          </tr>
          <tr>
            <td>'.esc_html__('Address', 'service-finder').': '.esc_html($row->apt).' '.esc_html($row->address).'</td>
          </tr>
          <tr>
            <td>'.esc_html($row->city).', '.esc_html($row->state).'</td>
          </tr>
          <tr>
            <td>'.esc_html__('Postal Code', 'service-finder').': '.esc_html($row->zipcode).'</td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="col-md-6 col-sm-6">
      <h4>'.esc_html__('Billed to', 'service-finder').': </h4>
      <table class="table">
        <tbody>
          <tr>
            <td>'.esc_html__('Attn', 'service-finder').': '.esc_html($row->name).'</td>
          </tr>
          <tr>
            <td>'.esc_html__('Email', 'service-finder').': '.esc_html($row->cusemail).'</td>
          </tr>
          <tr>
            <td>'.esc_html__('Phone', 'service-finder').': '.esc_html($row->cusphone).' '.esc_html($row->cusphone2).'</td>
          </tr>
          <tr>
            <td>'.esc_html__('Address', 'service-finder').': '.esc_html($row->cusapt).' '.esc_html($row->cusaddress).'</td>
          </tr>
          <tr>
            <td>'.esc_html($row->cuscity).', '.esc_html($row->cusstate).'</td>
          </tr>
          <tr>
            <td>'.esc_html__('Postal Code', 'service-finder').': '.esc_html($cuszipcode).'</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <br>
  <div class="row">
    <div class="col-md-12 col-sm-12">
      '.$row->invoicedesc.'
	  <br><br>
    </div>
	<div class="col-md-6 col-sm-6">
      <h4>'.esc_html__('Invoice No', 'service-finder').' <strong class="text-primary">'.esc_html($row->id).'</strong></h4>
    </div>
    <div class="col-md-6 col-sm-6">
      <table class="table">
        <tbody>
          <tr>
            <td><strong>'.esc_html__('Reference No', 'service-finder').': '.esc_html($row->reference_no).'</strong></td>
          </tr>
          <tr>
            <td><strong>'.esc_html__('Due Date', 'service-finder').': '.service_finder_date_format($row->duedate).'</strong></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <table class="table table-bordered table-hover invoice-margin-in">
    <thead>
      <tr>
        <th>'.esc_html__('No.', 'service-finder').'</th>
        <th>'.esc_html__('Service', 'service-finder').'</th>
        <th>'.esc_html__('Type', 'service-finder').'</th>
        <th>'.esc_html__('Hours', 'service-finder').'</th>
        <th>'.esc_html__('Description', 'service-finder').'</th>
        <th>'.esc_html__('Price', 'service-finder').'</th>
      </tr>
    </thead>
    <tbody>
    
    '.$servicehtml.'
    <tr>
      <td colspan="6">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="4" class="text-right font-weight-600">'.esc_html__('Total', 'service-finder').' ('.service_finder_currencycode().')</td>
      <td colspan="2" class="text-right font-weight-600">'.service_finder_money_format($row->total).'</td>
    </tr>
    <tr>
      <td colspan="4" class="text-right font-weight-600">'.esc_html__('Discount', 'service-finder').'</td>
      <td colspan="2" class="text-right font-weight-600">'.service_finder_money_format($displaydiscount).'</td>
    </tr>
    <tr>
      <td colspan="4" class="text-right font-weight-600">'.esc_html__('Tax', 'service-finder').'</td>
      <td colspan="2" class="text-right font-weight-600">'.service_finder_money_format($displaytax).'</td>
    </tr>
    <tr class="info">
      <td colspan="4" class="text-right font-weight-600">'.esc_html__('Grand Total', 'service-finder').' ('.service_finder_currencycode().')</td>
      <td colspan="2" class="text-right font-weight-600">'.service_finder_money_format($row->grand_total).'</td>
    </tr>
    </tbody>
    
  </table>
</div>
'.$payform;
		
		echo $html;
	}
	
	/*Display invoice into datatable*/
	public function service_finder_getCustomerInvoice($arg){
		global $wpdb, $service_finder_Tables, $service_finder_options;
		$requestData= $_REQUEST;
		$currUser = wp_get_current_user(); 
		
		if(!empty($arg['bookingid']) && $arg['bookingid'] > 0){
			$invoices = $wpdb->get_results($wpdb->prepare('SELECT invoice.id, invoice.reference_no, invoice.provider_id, invoice.duedate, invoice.grand_total, invoice.status, providers.full_name FROM '.$service_finder_Tables->invoice.' as invoice INNER JOIN '.$service_finder_Tables->providers.' as providers on invoice.provider_id = providers.wp_user_id WHERE `invoice`.`booking_id` = %d AND `invoice`.`customer_email` LIKE "%s"',$arg['bookingid'],$currUser->user_email));
			
			$columns = array( 
			0 =>'reference_no', 
			1 =>'id', 
			2 =>'full_name', 
			3 => 'duedate',
			4 =>'grand_total', 
			5=> 'status',
		);
		
		// getting total number records without any search
		$sql = $wpdb->prepare('SELECT invoice.id, invoice.reference_no, invoice.provider_id, invoice.duedate, invoice.grand_total, invoice.status, invoice.txnid, providers.full_name FROM '.$service_finder_Tables->invoice.' as invoice INNER JOIN '.$service_finder_Tables->providers.' as providers on invoice.provider_id = providers.wp_user_id WHERE `invoice`.`booking_id` = %d AND `invoice`.`customer_email` LIKE "%s"',$arg['bookingid'],$currUser->user_email);
		$query=$wpdb->get_results($sql);
		$totalData = count($query);
		$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
		$sql = "SELECT invoice.id, invoice.reference_no, invoice.provider_id, invoice.duedate, invoice.grand_total, invoice.status, invoice.txnid, providers.full_name";
		$sql.=" FROM ".$service_finder_Tables->invoice." as invoice INNER JOIN ".$service_finder_Tables->providers." as providers on invoice.provider_id = providers.wp_user_id WHERE `invoice`.`booking_id` = ".$arg['bookingid']." AND `invoice`.`customer_email` LIKE '".$currUser->user_email."'";
		
		if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
			$sql.=" AND ( providers.full_name LIKE '".$requestData['search']['value']."%' ";    
			$sql.=" OR invoice.reference_no LIKE '".$requestData['search']['value']."%' ";
			$sql.=" OR invoice.duedate LIKE '".$requestData['search']['value']."%' ";
			$sql.=" OR invoice.grand_total LIKE '".$requestData['search']['value']."%' ";
			$sql.=" OR invoice.status LIKE '".$requestData['search']['value']."%' )";
		}
		$query=$wpdb->get_results($sql);
		$totalFiltered = count($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
			
		}else{
		$invoices = $wpdb->get_results($wpdb->prepare('SELECT invoice.id, invoice.reference_no, invoice.provider_id, invoice.duedate, invoice.grand_total, invoice.status, invoice.txnid, providers.full_name FROM '.$service_finder_Tables->invoice.' as invoice INNER JOIN '.$service_finder_Tables->providers.' as providers on invoice.provider_id = providers.wp_user_id WHERE `invoice`.`customer_email` LIKE "%s"',$currUser->user_email));
		
		$columns = array( 
			0 =>'reference_no', 
			1 =>'reference_no', 
			2 =>'full_name', 
			3 => 'duedate',
			4 =>'grand_total', 
			5=> 'status',
		);
		
		// getting total number records without any search
		$sql = $wpdb->prepare('SELECT invoice.id, invoice.reference_no, invoice.provider_id, invoice.duedate, invoice.grand_total, invoice.status, invoice.txnid, providers.full_name FROM '.$service_finder_Tables->invoice.' as invoice INNER JOIN '.$service_finder_Tables->providers.' as providers on invoice.provider_id = providers.wp_user_id WHERE `invoice`.`customer_email` LIKE "%s"',$currUser->user_email);
		$query=$wpdb->get_results($sql);
		$totalData = count($query);
		$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
		$sql = "SELECT invoice.id, invoice.reference_no, invoice.provider_id, invoice.duedate, invoice.grand_total, invoice.status, invoice.txnid, providers.full_name";
		$sql.=" FROM ".$service_finder_Tables->invoice." as invoice INNER JOIN ".$service_finder_Tables->providers." as providers on invoice.provider_id = providers.wp_user_id WHERE `invoice`.`customer_email` LIKE '".$currUser->user_email."'";
		
		if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
			$sql.=" AND ( providers.full_name LIKE '".$requestData['search']['value']."%' ";    
			$sql.=" OR invoice.reference_no LIKE '".$requestData['search']['value']."%' ";
			$sql.=" OR invoice.duedate LIKE '".$requestData['search']['value']."%' ";
			$sql.=" OR invoice.grand_total LIKE '".$requestData['search']['value']."%' ";
			$sql.=" OR invoice.status LIKE '".$requestData['search']['value']."%' )";
		}
		$query=$wpdb->get_results($sql);
		$totalFiltered = count($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";	
		}
		
		
		$query=$wpdb->get_results($sql);
		
		$data = array();
		
		foreach($query as $result){
			$nestedData=array(); 
	$twocheckoutpublishkey = '';
	$twocheckoutaccountid = '';
	$settings = service_finder_getProviderSettings($result->provider_id);
			
	$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? esc_html($service_finder_options['pay_booking_amount_to']) : '';
	$twocheckouttype = (!empty($service_finder_options['twocheckout-type'])) ? esc_html($service_finder_options['twocheckout-type']) : '';
	if($twocheckouttype == 'live'){
		$twocheckoutmode = 'production';
	}else{
		$twocheckoutmode = 'sandbox';
	}
	if($pay_booking_amount_to == 'admin'){
		if($twocheckouttype == 'live'){
			$twocheckoutpublishkey = (!empty($service_finder_options['twocheckout-live-publish-key'])) ? esc_html($service_finder_options['twocheckout-live-publish-key']) : '';
			$twocheckoutaccountid = (!empty($service_finder_options['twocheckout-live-account-id'])) ? esc_html($service_finder_options['twocheckout-live-account-id']) : '';
		}else{
			$twocheckoutpublishkey = (!empty($service_finder_options['twocheckout-test-publish-key'])) ? esc_html($service_finder_options['twocheckout-test-publish-key']) : '';
			$twocheckoutaccountid = (!empty($service_finder_options['twocheckout-test-account-id'])) ? esc_html($service_finder_options['twocheckout-test-account-id']) : '';
		}
	}elseif($pay_booking_amount_to == 'provider'){
		$twocheckoutpublishkey = esc_html($settings['twocheckoutpublishkey']);
		$twocheckoutaccountid = esc_html($settings['twocheckoutaccountid']);
	}


			$nestedData[] = '<div class="booking-price">'.$result->reference_no.'</div>';
			$nestedData[] = '<div class="booking-price">'.$result->full_name.'</div>';
			$nestedData[] = '<div class="booking-price">'.service_finder_date_format($result->duedate).'</div>';
			$nestedData[] = '<div class="booking-price">'.service_finder_money_format($result->grand_total).'</div>';
			
			$now = time();
			$date = $result->duedate;
			
			if($result->status == 'pending' && strtotime($date) < $now){
				$status = esc_html__('Overdue', 'service-finder');
			}else{
				$status = service_finder_translate_static_status_string($result->status);
			}
			
			$nestedData[] = '
<div class="booking-price">'.$status.'</div>
';
			$nestedData[] = $result->txnid;

			$nestedData[] = '
<button type="button" class="btn btn-primary btn-xs viewInvoice" data-twocheckoutpublishkey="'.esc_attr($twocheckoutpublishkey).'" data-twocheckoutmode="'.esc_attr($twocheckoutmode).'" data-twocheckoutaccountid="'.esc_attr($twocheckoutaccountid).'" data-id="'.esc_attr($result->id).'"><i class="fa fa-eye"></i>'.esc_html__('View Invoice', 'service-finder').'</button>';

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
	
	/*Get Customers*/
	public function service_finder_getCustomers($user_id){
		
		global $wpdb, $service_finder_Tables, $service_finder_Params;
		
		$currUser = wp_get_current_user(); 
		
		$customers = $wpdb->get_results($wpdb->prepare('SELECT bookings.id, customers.name, customers.email FROM '.$service_finder_Tables->bookings.' as bookings INNER JOIN '.$service_finder_Tables->customers.' as customers on bookings.booking_customer_id = customers.id WHERE `bookings`.`provider_id` = %d GROUP BY customers.email',$user_id));
		
		return $customers;

	}	
	
	/*Edit Invoice*/
	public function service_finder_editInvoiceData($arg){
		
		global $wpdb, $service_finder_Tables, $service_finder_Params;
		
		$currUser = wp_get_current_user(); 
		$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';
		$servicedata = '';
		
		$invoicedata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->invoice.' WHERE id = %d',$arg['invoiceid']));
		$services = unserialize($invoicedata->services);
		$cnt = count($services);
		if(!empty($services)){
			foreach($services as $key => $service){
			$chk1 = ($service[1] == 'fix') ? 'checked="checked"' : '';
			$chk2 = ($service[1] == 'hourly') ? 'checked="checked"' : '';
			$cls = ($service[1] == 'hourly') ? 'display:inline-block;' : 'display:none;';
			$numval = ($service[2] != "") ? $service[2] : 1;
			
			$dropdownitem = service_finder_getAllServices($user_id);
			
			$service_dropdown = '';
			$service_dropdown_template = '';
			
			if(!empty($dropdownitem)){
				foreach($dropdownitem as $tem){
					if($service[0] == $tem->id){
						$select = 'selected="selected"';
					}else{
						$select = '';
					}
					$service_dropdown .= '
<option '.$select.' value="'.esc_attr($tem->id).'">'.stripcslashes($tem->service_name).'</option>
';
					$service_dropdown_template .= '
<option value="'.esc_attr($tem->id).'">'.stripcslashes($tem->service_name).'</option>
';
				}	
			}
			
				if($key == ($cnt - 1)){
				$addmore = '
<div class="col-xs-1">
  <button type="button" class="btn btn-default addButton"><i class="fa fa-plus"></i></button>
</div>
';
				}else{
				$addmore = '';
				}
			
				$servicedata .= '
<div class="sf-invoice-addbtn">'.$addmore.'</div>
<div class="form-group clearfix" >
  <div class="col-xs-3">
    <select title="Services" name="service_title['.$key.']" data-index="'.esc_attr($key).'" class="form-control sf-form-control sf-select-box">
      <option value="new">'.esc_html__('New Service', 'service-finder').'</option>
      
											'.$service_dropdown.'
                                            
    </select>
  </div>
  <div class="col-xs-3">
    <div class="form-group form-inline text-nowrap">
      <div class="radio sf-radio-checkbox">
        <input id="editfix-price['.esc_attr($key).']" type="radio" '.$chk1.' data-index="'.esc_attr($key).'" name="cost_type['.$key.']" value="fix" checked>
        <label for="editfix-price['.esc_attr($key).']">'.esc_html__('Fix', 'service-finder').'</label>
      </div>
      <div class="radio sf-radio-checkbox">
        <input id="edithourly-price['.esc_attr($key).']" type="radio" '.$chk2.' data-index="'.esc_attr($key).'" name="cost_type['.$key.']" value="hourly">
        <label for="edithourly-price['.esc_attr($key).']">'.esc_html__('Hour', 'service-finder').'</label>
      </div>
      <div class="num-hours" style="'.$cls.' width:48%;">
        <input class="service-num-hours num_hours_al" type="text" value="'.esc_attr($numval).'" value="'.esc_attr($service[2]).'" name="num_hours['.$key.']">
      </div>
    </div>
  </div>
  <div class="col-xs-3">
    <input type="text" name="service_desc['.$key.']" value="'.esc_attr($service[3]).'" data-index="'.esc_attr($key).'" class="form-control sf-form-control" placeholder="'.esc_html__('Description', 'service-finder').'">
  </div>
  <div class="col-xs-2">
    <input type="text" name="service_price['.$key.']" value="'.esc_attr($service[4]).'" data-index="'.esc_attr($key).'" class="form-control sf-form-control text-right" placeholder="'.esc_html__('Price', 'service-finder').'">
  </div>
  <div class="col-xs-1">
    <button type="button" class="btn btn-default removeButton"><i class="fa fa-minus"></i></button>
  </div>
</div>
';
			}
		}
		
		$newkey = $key + 1;
		
		$servicedata .= '
<div class="form-group hide clearfix " id="serviceEditTemplate">
  <div class="col-xs-3">
    <select title="Services" name="service_title" data-index="" class="form-control sf-form-control sf-select-box">
      <option value="new">'.esc_html__('New Service', 'service-finder').'</option>
      
											'.$service_dropdown_template.'
                                            
    </select>
  </div>
  <div class="col-xs-3">
    <div class="form-group form-inline text-nowrap">
      <div class="radio sf-radio-checkbox">
        <input id="editfix-price" type="radio" data-index="" name="cost_type" value="fix" checked>
        <label for="editfix-price">'.esc_html__('Fix', 'service-finder').'</label>
      </div>
      <div class="radio sf-radio-checkbox">
        <input id="edithourly-price" type="radio" data-index="" name="cost_type" value="hourly">
        <label for="edithourly-price">'.esc_html__('Hour', 'service-finder').'</label>
      </div>
      <div class="num-hours default-hidden num-hrs-btn-in">
        <input class="service-num-hours num_hours2" data-index="" type="text" value="1" name="num_hours">
      </div>
    </div>
  </div>
  <div class="col-xs-3">
    <input type="text" name="service_desc" data-index="" class="form-control sf-form-control" placeholder="'.esc_html__('Description', 'service-finder').'">
  </div>
  <div class="col-xs-2">
    <input type="text" name="service_price" data-index="" class="form-control sf-form-control text-right" placeholder="'.esc_html__('Price', 'service-finder').'">
  </div>
  <div class="col-xs-1">
    <button type="button" class="btn btn-default removeButton"><i class="fa fa-minus"></i></button>
  </div>
</div>
';
		
		
		$now = time();
		$date = $invoicedata->duedate;
		
		if($invoicedata->status == 'pending' && strtotime($date) < $now){
			$status = 'overdue';
		}else{
			$status = $invoicedata->status;
		}
		
		$discount_type = $invoicedata->discount_type;
		$tax_type = $invoicedata->tax_type;
		if($invoicedata->discount > 0){
		if($discount_type == 'fix'){
			$displaydiscount = $invoicedata->discount;
		}elseif($discount_type == 'percentage'){
			$displaydiscount = $invoicedata->total * ($invoicedata->discount/100);
		}
		}else{
			$displaydiscount = '0.00';
		}
		
		if($invoicedata->tax > 0){
		if($tax_type == 'fix'){
			$displaytax = $invoicedata->tax;
		}elseif($tax_type == 'percentage'){
			$discountedprice = $invoicedata->total - $displaydiscount;
			$displaytax = $discountedprice * ($invoicedata->tax/100);
		}
		}else{
			$displaytax = '0.00';
		}
		
		$result = array(
							'reference_no' => $invoicedata->reference_no,
							'duedate' => $invoicedata->duedate,
							'provider_id' => $invoicedata->provider_id,
							'customer_email' => $invoicedata->customer_email,
							'booking_id' => $invoicedata->booking_id,
							'discount_type' => $invoicedata->discount_type,
							'tax_type' => $invoicedata->tax_type,
							'discount' => $invoicedata->discount,
							'tax' => $invoicedata->tax,
							'services' => $servicedata,
							'serviceskey' => $key,
							'description' => $invoicedata->description,
							'total' => $invoicedata->total,
							'grand_total' => $invoicedata->grand_total,
							'discountamount' => $displaydiscount,
							'taxamount' => $displaytax,
							'status' => $status,
					);
		echo json_encode($result);
	}	
	
	/*Get Service Details*/
	public function service_finder_getServiceDetails($arg){
		
		global $wpdb, $service_finder_Tables, $service_finder_Params;
		
		$currUser = wp_get_current_user(); 
		
		$serviceid = (!empty($arg['serviceid'])) ? $arg['serviceid'] : '';
		
		if($serviceid > 0){
		
		$result = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->services.' WHERE `status` = "active" AND `id` = %d',$serviceid));
		if(!empty($result)){
		$success = array(
					'status' => 'success',
					'cost_type' => (!empty($result->cost_type)) ? $result->cost_type : '',
					'cost' => (!empty($result->cost)) ? $result->cost : '',
					);
		echo json_encode($success);
		}else{
		$success = array(
					'status' => 'success',
					'cost_type' => '',
					'cost' => '',
					);
		echo json_encode($success);
		}
		}else{
		$success = array(
					'status' => 'success',
					'cost_type' => '',
					'cost' => '',
					);
		echo json_encode($success);
		}
		

	}
	
	/*Add Invoice*/
	public function service_finder_addInvoiceData($arg){
		
		global $wpdb, $service_finder_Tables, $service_finder_Params, $service_finder_options;
			$services = array_map(null, $arg['service_title'], $arg['cost_type'], $arg['num_hours'], $arg['service_desc'], $arg['service_price']);
			
			$services = serialize($services);
			
			$currUser = wp_get_current_user(); 
			
			$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';
			
			$admin_fee_type = (!empty($service_finder_options['admin-fee-type'])) ? $service_finder_options['admin-fee-type'] : 0;
			$admin_fee_percentage = (!empty($service_finder_options['admin-fee-percentage'])) ? $service_finder_options['admin-fee-percentage'] : 0;
			$admin_fee_fixed = (!empty($service_finder_options['admin-fee-fixed'])) ? $service_finder_options['admin-fee-fixed'] : 0;
			
			$charge_admin_fee = (!empty($service_finder_options['charge-admin-fee'])) ? $service_finder_options['charge-admin-fee'] : '';
			$charge_admin_fee_from = (!empty($service_finder_options['charge-admin-fee-from'])) ? $service_finder_options['charge-admin-fee-from'] : '';
			
			$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? esc_html($service_finder_options['pay_booking_amount_to']) : '';
			
			if($charge_admin_fee && $pay_booking_amount_to == 'admin' && (($admin_fee_type == 'fixed' && $admin_fee_fixed > 0) || ($admin_fee_type == 'percentage' && $admin_fee_percentage > 0)) && $charge_admin_fee_from == 'customer'){
			
				if($admin_fee_type == 'fixed'){
					$adminfee = $admin_fee_fixed;
				}elseif($admin_fee_type == 'percentage'){
					$adminfee = $arg['gtotal'] * ($admin_fee_percentage/100);	
				}
				
				$gtotal = $arg['gtotal'] + $adminfee;
			}elseif($charge_admin_fee && $pay_booking_amount_to == 'admin' && (($admin_fee_type == 'fixed' && $admin_fee_fixed > 0) || ($admin_fee_type == 'percentage' && $admin_fee_percentage > 0)) && $charge_admin_fee_from == 'provider'){
				if($admin_fee_type == 'fixed'){
					$adminfee = $admin_fee_fixed;
				}elseif($admin_fee_type == 'percentage'){
					$adminfee = $arg['gtotal'] * ($admin_fee_percentage/100);	
				}
				$gtotal = $arg['gtotal'];
			}else{
				$adminfee = 0;
				$gtotal = $arg['gtotal'];
			}
			
			$data = array(
					'reference_no'	=>   esc_attr($arg['refno']),
					'duedate'      	=> 	 esc_attr($arg['dueDate']),
					'provider_id'   => 	 $user_id,
					'customer_email'   => esc_attr($arg['customer']),
					'discount_type' =>   esc_attr($arg['discount-type']),
					'tax_type'      =>	 esc_attr($arg['tax-type']),
					'discount'      =>   esc_attr($arg['discount']),
					'tax'           =>	 esc_attr($arg['tax']),
					'services' 	    => 	 $services,
					'description' 	=>   esc_attr($arg['short-desc']),
					'total'         =>   esc_attr($arg['total']),
					'grand_total'   =>   $gtotal,
					'status'	    => 	 esc_attr($arg['status']),
					'charge_admin_fee_from'   =>   $charge_admin_fee_from,
					'paid_to_provider' => ($pay_booking_amount_to == 'provider') ? 'paid' : 'pending',
					'adminfee'	    => 	 $adminfee
					);

			$wpdb->insert($service_finder_Tables->invoice,wp_unslash($data));
			
			$invoice_id = $wpdb->insert_id;
			
			if ( ! $invoice_id ) {
				$adminemail = get_option( 'admin_email' );
				$allowedhtml = array(
					'a' => array(
						'href' => array(),
						'title' => array()
					),
				);
				$error = array(
						'status' => 'error',
						'err_message' => sprintf( wp_kses(esc_html__('Couldn&#8217;t add invoice... please contact the <a href="mailto:%s">Administrator</a> !', 'service-finder'),$allowedhtml), $adminemail )
						);
				echo json_encode($error);
			}else{

				$users = $wpdb->prefix . 'users';
				$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$users.' WHERE `user_email` = "%s"',$arg['customer']));
				if(!empty($row)){
				$urole = service_finder_getUserRole($row->ID);
				}
				
				if(empty($row)){
				$userLink = service_finder_get_invoice_author_url($user_id,'',$invoice_id);
				}elseif($urole == 'Provider'){
				$userLink = service_finder_get_invoice_author_url($user_id,'',$invoice_id);
				}else{
				$userLink = '';
				}
				
				if(function_exists('service_finder_add_notices')) {
					$noticedata = array(
							'customer_id' => $row->ID,
							'target_id' => $invoice_id, 
							'topic' => 'Generate Invoice',
							'title' => esc_html__('Generate Invoice', 'service-finder'),
							'notice' => sprintf( esc_html__('New Invoice generated by %s. Please pay it soon. Invoice Ref id is #%d', 'service-finder'), ucfirst(service_finder_getProviderName($user_id)), $arg['refno'] )
							);
					service_finder_add_notices($noticedata);
				
				}
				
				/*Send Invoice mail to customer*/
				$this->service_finder_SendInvoiceMailToCustomer($data,$userLink,$invoice_id);
				
				$success = array(
						'status' => 'success',
						'suc_message' => esc_html__('Invoice generated successfully.', 'service-finder'),
						'invoiceid' => $invoice_id,
						'userlink' => $userLink,
						);
				echo json_encode($success);
			}
		

	}	
	/*Send Invoice Mail to Customer*/
	public function service_finder_SendInvoiceMailToCustomer($maildata = '',$userLink = '',$invoiceid = ''){
		global $wpdb, $service_finder_options, $service_finder_Tables;

			if(!empty($service_finder_options['invoice-to-customer'])){
				$message = $service_finder_options['invoice-to-customer'];
			}else{
				$message = '
<h4>Invoice Details</h4>
Reference No: %REFERENCENO%
							
							Due date: %DUEDATE%
							
							Provider Name: %PROVIDERNAME%
							
							Discount Type: %DISCOUNTTYPE%
							
							Discount: %DISCOUNT%
							
							Discount Amount: %DISCOUNTAMOUNT%
							
							Tax Type: %TAXTYPE%
							
							Tax: %TAX%
							
							Tax Amount: %TAXAMOUNT%
							
							Description: %DESCRIPTION%
							
							Total: %TOTAL%
							
							Grand Total: %GRANDTOTAL%';
			}
								
			if($userLink != ""){
			$message .= '<br/>
<br/>
<a href="'.esc_url($userLink).'">'.esc_html__('Pay Now','service-finder').'</a>';
			}
			
			$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->providers.' WHERE `wp_user_id` = %d',$maildata['provider_id']));
			$customerInfo = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->customers.' WHERE `email` = "%s" GROUP BY email',$maildata['customer_email']));
			
			if($maildata['discount'] != ""){

			$discounttype = $maildata['discount_type'];

			$discount = $maildata['discount'];

			}else{

			$discounttype = 'N/A';

			$discount = 'N/A';

			}

			

			if($maildata['tax'] != ""){

			$taxtype = $maildata['tax_type'];

			$tax = $maildata['tax'];

			}else{

			$taxtype = 'N/A';

			$tax = 'N/A';

			}
			
			if($maildata['description'] != ""){
			$description = $maildata['description'];
			}else{
			$description = 'N/A';
			}
			
			$discount_type = $maildata['discount_type'];
			$tax_type = $maildata['tax_type'];
			if($maildata['discount'] > 0){
			if($discount_type == 'fix'){
				$displaydiscount = $maildata['discount'];
			}elseif($discount_type == 'percentage'){
				$displaydiscount = $maildata['total'] * ($maildata['discount']/100);
			}
			}else{
				$displaydiscount = '0.00';
			}
			
			if($maildata['tax'] > 0){
			if($tax_type == 'fix'){
				$displaytax = $maildata['tax'];
			}elseif($tax_type == 'percentage'){
				$discountedprice = $maildata['total'] - $displaydiscount;
				$displaytax = $discountedprice * ($maildata['tax']/100);
			}
			}else{
				$displaytax = '0.00';
			}
			
			$tokens = array('%REFERENCENO%','%DUEDATE%','%PROVIDERNAME%','%DISCOUNTTYPE%','%DISCOUNT%','%TAXTYPE%','%TAX%','%DESCRIPTION%','%TOTAL%','%GRANDTOTAL%','%CUSTOMERNAME%','%CUSTOMEREMAIL%','%CUSTOMERPHONE%','%CUSTOMERPHONE2%','%ADDRESS%','%APT%','%CITY%','%STATE%','%ZIPCODE%','%COUNTRY%','%DISCOUNTAMOUNT%','%TAXAMOUNT%');
			
			$replacements = array($maildata['reference_no'],$maildata['duedate'],service_finder_get_providername_with_link($row->wp_user_id),$discounttype,$discount,$taxtype,$tax,$description,service_finder_money_format($maildata['total']),service_finder_money_format($maildata['grand_total']),$customerInfo->name,$customerInfo->email,$customerInfo->phone,$customerInfo->phone2,$customerInfo->address,$customerInfo->apt,$customerInfo->city,$customerInfo->state,$customerInfo->zipcode,$customerInfo->country,service_finder_money_format($displaydiscount),service_finder_money_format($displaytax));
			
			
			$msg_body = str_replace($tokens,$replacements,$message);
			
			if($service_finder_options['invoice-to-customer-subject'] != ""){
				$msg_subject = $service_finder_options['invoice-to-customer-subject'];
			}else{
				$msg_subject = esc_html__('Invoice Notification', 'service-finder');
			}
			
			if(service_finder_wpmailer($maildata['customer_email'],$msg_subject,$msg_body)) {

				$success = array(
						'status' => 'success',
						'suc_message' => esc_html__('Message has been sent', 'service-finder'),
						);
				$service_finder_Success = json_encode($success);
				return $service_finder_Success;
				
				
			} else {
					
				$error = array(
						'status' => 'error',
						'err_message' => esc_html__('Message could not be sent.', 'service-finder'),
						);
				$service_finder_Errors = json_encode($error);
				return $service_finder_Errors;
			}
		
	}
	
	/*Update Invoice*/
	public function service_finder_updateInvoiceData($arg){
		
		global $wpdb, $service_finder_Tables, $service_finder_Params, $service_finder_options;
		$service_title = (!empty($arg['service_title'])) ? $arg['service_title'] : '';
		$cost_type = (!empty($arg['cost_type'])) ? $arg['cost_type'] : '';
		$num_hours = (!empty($arg['num_hours'])) ? $arg['num_hours'] : '';
		$service_desc = (!empty($arg['service_desc'])) ? $arg['service_desc'] : '';
		$service_price = (!empty($arg['service_price'])) ? $arg['service_price'] : '';
		
		$user_id = (!empty($arg['user_id'])) ? $arg['user_id'] : '';
		
			$services = array_map(null, $service_title, $cost_type, $num_hours, $service_desc, $service_price);
			
			$services = serialize($services);
			
			$admin_fee_type = (!empty($service_finder_options['admin-fee-type'])) ? $service_finder_options['admin-fee-type'] : 0;
			$admin_fee_percentage = (!empty($service_finder_options['admin-fee-percentage'])) ? $service_finder_options['admin-fee-percentage'] : 0;
			$admin_fee_fixed = (!empty($service_finder_options['admin-fee-fixed'])) ? $service_finder_options['admin-fee-fixed'] : 0;
			
			$charge_admin_fee = (!empty($service_finder_options['charge-admin-fee'])) ? $service_finder_options['charge-admin-fee'] : '';
			$charge_admin_fee_from = (!empty($service_finder_options['charge-admin-fee-from'])) ? $service_finder_options['charge-admin-fee-from'] : '';
			
			$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? esc_html($service_finder_options['pay_booking_amount_to']) : '';
			
			if($charge_admin_fee && $pay_booking_amount_to == 'admin' && (($admin_fee_type == 'fixed' && $admin_fee_fixed > 0) || ($admin_fee_type == 'percentage' && $admin_fee_percentage > 0)) && $charge_admin_fee_from == 'customer'){
			
				if($admin_fee_type == 'fixed'){
					$adminfee = $admin_fee_fixed;
				}elseif($admin_fee_type == 'percentage'){
					$adminfee = $arg['gtotal'] * ($admin_fee_percentage/100);	
				}
				
				$gtotal = $arg['gtotal'] + $adminfee;
			}elseif($charge_admin_fee && $pay_booking_amount_to == 'admin' && (($admin_fee_type == 'fixed' && $admin_fee_fixed > 0) || ($admin_fee_type == 'percentage' && $admin_fee_percentage > 0)) && $charge_admin_fee_from == 'provider'){
				if($admin_fee_type == 'fixed'){
					$adminfee = $admin_fee_fixed;
				}elseif($admin_fee_type == 'percentage'){
					$adminfee = $arg['gtotal'] * ($admin_fee_percentage/100);	
				}
				$gtotal = $arg['gtotal'];
			}else{
				$adminfee = 0;
				$gtotal = $arg['gtotal'];
			}
			
			$currUser = wp_get_current_user(); 
			$data = array(
					'reference_no'	=>   esc_attr($arg['refno']),
					'duedate'      	=> 	 esc_attr($arg['dueDate']),
					'provider_id'   => 	 $user_id,
					'customer_email'   => esc_attr($arg['customer']),
					'discount_type' =>   esc_attr($arg['discount-type']),
					'tax_type'      =>	 esc_attr($arg['tax-type']),
					'discount'      =>   esc_attr($arg['discount']),
					'tax'           =>	 esc_attr($arg['tax']),
					'services' 	    => 	 $services,
					'description' 	=>   esc_attr($arg['short-desc']),
					'total'         =>   esc_attr($arg['total']),
					'grand_total'   =>   $gtotal,
					'status'	    => 	 esc_attr($arg['status']),
					'charge_admin_fee_from' 	=>   $charge_admin_fee_from,
					'adminfee'	    => 	 $adminfee
					);
			
			$where = array(
					'id'	=>   $arg['invoiceid'],
					);		

			$res = $wpdb->update($service_finder_Tables->invoice,wp_unslash($data),$where);
			
			if ( ! $res) {
				$adminemail = get_option( 'admin_email' );
				$allowedhtml = array(
					'a' => array(
						'href' => array(),
						'title' => array()
					),
				);
				$error = array(
						'status' => 'error',
						'err_message' => sprintf( wp_kses(esc_html__('Couldn&#8217;t update invoice... please contact the <a href="mailto:%s">Administrator</a> !', 'service-finder'),$allowedhtml), $adminemail )
						);
				echo json_encode($error);
			}else{
			
				if(function_exists('service_finder_add_notices')) {
					$users = $wpdb->prefix . 'users';
					$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$users.' WHERE `user_email` = "%s"',$arg['customer']));
					$noticedata = array(
							'customer_id' => $row->ID,
							'target_id' => $arg['invoiceid'], 
							'topic' => 'Invoice Update',
							'title' => esc_html__('Invoice Update', 'service-finder'),
							'notice' => sprintf( esc_html__('Previousaly generated invoice have been updated. Please review it. Invoice Ref id is #%d', 'service-finder'), $arg['refno'] )
							);
					service_finder_add_notices($noticedata);
				
				}
				
				$success = array(
						'status' => 'success',
						'suc_message' => esc_html__('Update invoice successfully.', 'service-finder'),
						'invoiceid' => $arg['invoiceid'],
						);
				echo json_encode($success);
			}
		

	}	
}