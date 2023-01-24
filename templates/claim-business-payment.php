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
$service_finder_options = get_option('service_finder_options');
$service_finder_Tables = service_finder_plugin_global_vars('service_finder_Tables');
$wpdb = service_finder_plugin_global_vars('wpdb');
$paymentsystem = service_finder_plugin_global_vars('paymentsystem');

$service_finder_Params = service_finder_plugin_global_vars('service_finder_Params');
$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('Provider', 'service-finder');	
$customerreplacestring = (!empty($service_finder_options['customer-replace-string'])) ? $service_finder_options['customer-replace-string'] : esc_html__('Customers', 'service-finder');	

$claimedbusinessid = (isset($_GET['claimedbusinessid'])) ? esc_html($_GET['claimedbusinessid']) : '';
$profileid = (isset($_GET['profileid'])) ? esc_html($_GET['profileid']) : '';

$claimedbusinessid = service_finder_decrypt($claimedbusinessid, 'Developer#@)!%');
$profileid = service_finder_decrypt($profileid, 'Developer#@)!%');

$claimedrow = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->claim_business.' WHERE id = %d AND provider_id = %d AND status = "approved"',$claimedbusinessid,$profileid));

if(!empty($claimedrow)){
$html = '';

$custommessage = (!empty($service_finder_options['claim-business-payment'])) ? $service_finder_options['claim-business-payment'] : '';

$token = array('%PROVIDERNAME%');
$replacement = array(service_finder_get_providername_with_link($profileid));
$custommessage = str_replace($token,$replacement,$custommessage);

$html .= '<p>'.$custommessage.'</p>';

$html .= '<form class="claimbusiness_payment clearfix bg-white padding-30 sf-rouned-box" method="post">';
$enablepackage0 = (!empty($service_finder_options['enable-package0'])) ? $service_finder_options['enable-package0'] : '';
$enablepackage1 = (!empty($service_finder_options['enable-package1'])) ? $service_finder_options['enable-package1'] : '';
$enablepackage2 = (!empty($service_finder_options['enable-package2'])) ? $service_finder_options['enable-package2'] : '';
$enablepackage3 = (!empty($service_finder_options['enable-package3'])) ? $service_finder_options['enable-package3'] : '';

if($enablepackage1 || $enablepackage2 || $enablepackage3){
$withoutpackage = false;
$html .= '<div class="">
<div class="form-group">
<select name="provider-role" class="form-control sf-form-control sf-select-box">
  <option class="blank" value="">
  '.esc_html__('No Package', 'service-finder').'
  </option>
  '.service_finder_claimed_getPackages().'
</select>
</div>
</div>';
}else{
	$withoutpackage = true;
} 
$package1price = (!empty($service_finder_options['package1-price'])) ? $service_finder_options['package1-price'] : '';
$free = (trim($package1price) == 0) ? true : false;
$displayfree = ($free) ? 'style="display:none;"' : '';	
if($paymentsystem != 'woocommerce'){					
$html .= '<div class="col-md-12 sf-card-group margin-less" id="paymethod_bx" '.$displayfree.' >
<div class="form-group form-inline">';

										$payment_methods = (!empty($service_finder_options['payment-methods'])) ? $service_finder_options['payment-methods'] : '';
										$paymentflag = 0;
										if(!empty($payment_methods)){
										foreach($payment_methods as $key => $value){
										if($key != 'paypal-adaptive' && $key != 'cod'){
										if($key == 'stripe'){
										$label = '<img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/mastercard.jpg" title="'.esc_html__('Stripe','service-finder').'" alt="'.esc_html__('mastercard','service-finder').'"><img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/payment.jpg" title="'.esc_html__('Stripe','service-finder').'" alt="'.esc_html__('american express','service-finder').'"><img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/discover.jpg" title="'.esc_html__('Stripe','service-finder').'" alt="'.esc_html__('discover','service-finder').'"><img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/visa.jpg" title="'.esc_html__('Stripe','service-finder').'" alt="'.esc_html__('visa','service-finder').'">';
										}elseif($key == 'twocheckout'){
										$label = '<img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/twocheckout.jpg" title="'.esc_html__('2Checkout','service-finder').'" alt="'.esc_html__('2Checkout','service-finder').'">';
										}elseif($key == 'wired'){
										$label = '<img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/wired.jpg" title="'.esc_html__('Wire Transfer','service-finder').'" alt="'.esc_html__('Wired','service-finder').'">';
										}elseif($key == 'payumoney' && $service_finder_options['payment-type'] == 'single'){
										$label = '<img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/payumoney.jpg" title="'.esc_html__('PayU Money','service-finder').'" alt="'.esc_html__('PayU Money','service-finder').'">';
										}elseif($key == 'payulatam'){
										$label = '<img src="'.get_template_directory_uri().'/inc/images/payment/payulatam.jpg" title="'.esc_html__('PayU Latam','service-finder').'" alt="'.esc_html__('PayU Latam','service-finder').'" alt="'.esc_html__('PayU Latam','service-finder').'">';
										}else{
										$label = '<img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/paypal.jpg" title="'.esc_html__('Paypal','service-finder').'" alt="'.esc_html__('Paypal','service-finder').'">';
										}
											
											if($value == 1){
											$paymentflag = 1;
												if($key == 'payumoney'){
													if($service_finder_options['payment-type'] == 'single'){
													$html .= '<div class="radio sf-radio-checkbox">
															<input id="'.$key.'_bx" type="radio" name="claim_payment_mode" value="'.esc_attr($key).'">
															<label for="'.$key.'_bx">'.$label.'</label>
														</div>';	
													}	
												}else{
												
												$html .= '<div class="radio sf-radio-checkbox">
															<input id="'.$key.'_bx" type="radio" name="claim_payment_mode" value="'.esc_attr($key).'">
															<label for="'.$key.'_bx">'.$label.'</label>
														</div>';	
												}	
												
											}
										}	
											
										}
										}
										
										if($paymentflag == 0){
											$html .= '<div class="sf-alert-bx alert-danger">';
											$html .= esc_html__('Payment method not available.', 'service-finder');
											$html .= '</div>';
										}
$html .= '</div>
</div>
<div id="stripeinfo" class="default-hidden">
<div class="col-md-8">
<div class="form-group">
  <label>
  '.esc_html__('Card Number', 'service-finder').'
  </label>
  <div class="input-group"> <i class="input-group-addon fa fa-credit-card"></i>
	<input type="text" id="scd_number" name="scd_number" class="form-control">
  </div>
</div>
</div>
<div class="col-md-4">
<div class="form-group">
  <label>
  '.esc_html__('CVC', 'service-finder').'
  </label>
  <div class="input-group"> <i class="input-group-addon fa fa-ellipsis-h"></i>
	<input type="text" id="scd_cvc" name="scd_cvc" class="form-control">
  </div>
</div>
</div>
<div class="col-md-6">
<div class="form-group has-select">
  <label>
  '.esc_html__('Select Month', 'service-finder').'
  </label>
  <select id="scd_month" name="scd_month" class="form-control sf-form-control sf-select-box" title="'.esc_html__('Select Month', 'service-finder').'">
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
  <label>
  '.esc_html__('First Name', 'service-finder').'
  Select Year</label>
  <select id="scd_year" name="scd_year" class="form-control sf-form-control sf-select-box"  title="'.esc_html__('Select Year', 'service-finder').'">';
					$year = date('Y');
					for($i = $year;$i<=$year+50;$i++){
						$html .= '<option value="'.esc_attr($i).'">'.$i.'</option>';
					}
  $html .= '</select>
</div>
</div>
</div>
<div id="twocheckoutstripeinfo" class="default-hidden">
<div class="col-md-8">
<div class="form-group">
  <label>
  '.esc_html__('Card Number', 'service-finder').'
  </label>
  <div class="input-group"> <i class="input-group-addon fa fa-credit-card"></i>
	<input type="text" id="twocheckout_scd_number" name="twocheckout_scd_number" class="form-control">
  </div>
</div>
</div>
<div class="col-md-4">
<div class="form-group">
  <label>
  '.esc_html__('CVC', 'service-finder').'
  </label>
  <div class="input-group"> <i class="input-group-addon fa fa-ellipsis-h"></i>
	<input type="text" id="twocheckout_scd_cvc" name="twocheckout_scd_cvc" class="form-control">
  </div>
</div>
</div>
<div class="col-md-6">
<div class="form-group has-select">
  <label>
  '.esc_html__('Select Month', 'service-finder').'
  </label>
  <select id="twocheckout_scd_month" name="twocheckout_scd_month" class="form-control sf-form-control sf-select-box" title="'.esc_html__('Select Month', 'service-finder').'">
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
  <label>
  '.esc_html__('Select Year', 'service-finder').'
  </label>
  <select id="twocheckout_scd_year" name="twocheckout_scd_year" class="form-control sf-form-control sf-select-box"  title="'.esc_html__('Select Year', 'service-finder').'">';
					$year = date('Y');
					for($i = $year;$i<=$year+50;$i++){
						$html .= '<option value="'.esc_attr($i).'">'.$i.'</option>';
					}
  $html .= '</select>
</div>
</div>
</div>
<div id="payulatampageinfo" class="default-hidden">
<div class="col-md-12">
<div class="form-group">
<label>
'.esc_html__('Select Card', 'service-finder').'
</label>
<select id="payulatam_page_cardtype" name="payulatam_signup_cardtype" class="form-control sf-form-control sf-select-box"  title="'.esc_html__('Select Card', 'service-finder').'">';
  $country = (isset($service_finder_options['payulatam-country'])) ? $service_finder_options['payulatam-country'] : '';
  $cards = service_finder_get_cards($country);
  foreach($cards as $card){
	$html .= '<option value="'.esc_attr($card).'">'.$card.'</option>';
  }
							
$html .= '</select>
</div>
</div>
<div class="col-md-8">
<div class="form-group">
  <label>
  '.esc_html__('Card Number', 'service-finder').'
  </label>
  <div class="input-group"> <i class="input-group-addon fa fa-credit-card"></i>
	<input type="text" id="payulatam_scd_number" name="payulatam_cd_number" class="form-control">
  </div>
</div>
</div>
<div class="col-md-4">
<div class="form-group">
  <label>
  '.esc_html__('CVC', 'service-finder').'
  </label>
  <div class="input-group"> <i class="input-group-addon fa fa-ellipsis-h"></i>
	<input type="text" id="payulatam_scd_cvc" name="payulatam_cd_cvc" class="form-control">
  </div>
</div>
</div>
<div class="col-md-6">
<div class="form-group has-select">
  <label>
  '.esc_html__('Select Month', 'service-finder').'
  </label>
  <select id="payulatam_scd_month" name="payulatam_cd_month" class="form-control sf-form-control sf-select-box" title="'.esc_html__('Select Month', 'service-finder').'">
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
  <label>
  '.esc_html__('Select Year', 'service-finder').'
  </label>
  <select id="payulatam_scd_year" name="payulatam_cd_year" class="form-control sf-form-control sf-select-box"  title="'.esc_html__('Select Year', 'service-finder').'">';
					$year = date('Y');
					for($i = $year;$i<=$year+50;$i++){
						$html .= '<option value="'.esc_attr($i).'">'.$i.'</option>';
					}
  $html .= '</select>
</div>
</div>
</div>
<div id="signuppagewiredinfo" class="default-hidden">
<div class="col-md-12">';
$description = (!empty($service_finder_options['wire-transfer-description'])) ? $service_finder_options['wire-transfer-description'] : '';
$html .= $description;
$html .= '</div>
</div>';
}
$pactype = ($free || $withoutpackage) ? 'yes' : 'no';
                    $html .= '<div class="">
                      <input type="hidden" name="freemode" id="freemode_bx" value="'.$pactype.'" />
					  <input type="hidden" name="profileid" value="'.$profileid.'" />
					  <input type="hidden" name="claimedbusinessid" value="'.$claimedbusinessid.'" />
                      <input type="hidden" name="signup_user_role" value="'.esc_attr($service_finder_Params['role']['provider']).'" />
                      <input type="submit" class="btn btn-primary btn-block" name="claimprofile" value="'.esc_html__('Pay for Claimed Businesss', 'service-finder').'" />
                    </div>';
$html .= '</form>';
}else{
$html .= '<div class="alert alert-info">'.esc_html__('Sorry! You dont have access to this page','service-finder').'</div>';
}