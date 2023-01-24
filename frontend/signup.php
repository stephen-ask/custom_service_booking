<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/

$wpdb = service_finder_plugin_global_vars('wpdb');
$service_finder_Params = service_finder_plugin_global_vars('service_finder_Params');
$service_finder_options = get_option('service_finder_options');
$paymentsystem = service_finder_plugin_global_vars('paymentsystem');

$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('Provider', 'service-finder');	
$customerreplacestring = (!empty($service_finder_options['customer-replace-string'])) ? $service_finder_options['customer-replace-string'] : esc_html__('Customers', 'service-finder');	

$socialsignupwith = (!empty($service_finder_options['social-signup-with'])) ? esc_html($service_finder_options['social-signup-with']) : 'nextend-social-login';

$twocheckouttype = (!empty($service_finder_options['twocheckout-type'])) ? esc_html($service_finder_options['twocheckout-type']) : '';
if($twocheckouttype == 'live'){
	$twocheckoutmode = 'production';
}else{
	$twocheckoutmode = 'sandbox';
}

if($twocheckouttype == 'live'){
	$twocheckoutpublishkey = (!empty($service_finder_options['twocheckout-live-publish-key'])) ? esc_html($service_finder_options['twocheckout-live-publish-key']) : '';
	$twocheckoutaccountid = (!empty($service_finder_options['twocheckout-live-account-id'])) ? esc_html($service_finder_options['twocheckout-live-account-id']) : '';
}else{
	$twocheckoutpublishkey = (!empty($service_finder_options['twocheckout-test-publish-key'])) ? esc_html($service_finder_options['twocheckout-test-publish-key']) : '';
	$twocheckoutaccountid = (!empty($service_finder_options['twocheckout-test-account-id'])) ? esc_html($service_finder_options['twocheckout-test-account-id']) : '';
}

$signupautosuggestion = (isset($service_finder_options['signup-auto-suggestion']) && service_finder_show_autosuggestion_on_site()) ? $service_finder_options['signup-auto-suggestion'] : false;
$show_signup_otp = (!empty($service_finder_options['show-signup-otp'])) ? esc_html($service_finder_options['show-signup-otp']) : '';
$show_customer_signup_otp = (!empty($service_finder_options['show-customer-signup-otp'])) ? esc_html($service_finder_options['show-customer-signup-otp']) : '';

$packagenumber = (isset($_GET['package'])) ? esc_html($_GET['package']) : '';

$selectedpackage = '';
if($packagenumber != ''){
$selectedpackage = 'package_'.$packagenumber;
}

$countryarr = (!empty($service_finder_options['allowed-country'])) ? $service_finder_options['allowed-country'] : '';
if(is_array($countryarr))
{
	$totalcountry = count($countryarr);
}else{
	if($countryarr != '')
	{
		$totalcountry = 1;
	}else{
		$totalcountry = 0;
	}
}

wp_add_inline_script( 'service_finder-js-registration', '/*Declare global variable*/
var twocheckoutaccountid = "'.$twocheckoutaccountid.'";
var twocheckoutpublishkey = "'.$twocheckoutpublishkey.'";
var twocheckouttype = "'.$twocheckouttype.'";
var twocheckoutmode = "'.$twocheckoutmode.'";
var totalcountry = "'.$totalcountry.'";
var selectedpackage = "'.$selectedpackage.'";
var signupautosuggestion = "'.$signupautosuggestion.'";
var quicksignup = "'.$service_finder_options['quick-signup'].'";
', 'after' );

$socialflag = 0;
$socialloginclass = '';
if(class_exists('aonesms'))
{
	$socialflag++;
}
if($socialsignupwith == 'nextend-social-login') 
{
if(class_exists('NextendSocialLogin'))
{
	$socialflag++;
}
}elseif($socialsignupwith == 'wordpress-social-login')
{
if(function_exists('wsl_activate'))
{
	$socialflag++;
}
}

/*if($socialflag == 1){
$socialloginclass = 'sf-other-login-one';
}elseif($socialflag == 2){
$socialloginclass = 'sf-other-login-two';
}*/
$socialloginclass = 'sf-other-login-one';
?>
<!-- Signup Template -->
<!-- Content -->
<?php
if($a['role'] == 'customer'){
$role = 'customer';
$_SESSION['social_account_role'] = "customer";
}else{
$role = 'provider';
$_SESSION['social_account_role'] = "provider";
}

// var_dump($_SESSION['social_account_role']);

wp_add_inline_script( 'service_finder-js-registration', '/*Declare global variable*/
var formtype = "signup"; var userrole = "'.$role.'";', 'after' );
?>
	
  <!-- Left & right section start -->
  		<?php
        echo '<div class="padding-20 margin-b-30  bg-white sf-rouned-box sf-register-page wd-default-wrap">';
          echo '<div class="tabbable">';
          // if(!is_user_logged_in()){

            // var_dump($a) ;
            /* echo '<ul class="nav nav-tabs">';
            $active = ($a['role'] == 'customer') ? 'active' : '';
            if($a['role'] == 'provider' || $a['role'] == 'both' || $a['role'] == ''){
              echo '<li class="active"><a data-toggle="tab" href="#providertab">
                '.esc_html($providerreplacestring).'
                </a></li>';
            }
			if(($a['role'] == 'customer' || $a['role'] == 'both' || $a['role'] == '') && $packagenumber == ''){
              echo '<li class="'.$active.'"><a data-toggle="tab" href="#customertab">
                '.esc_html($customerreplacestring).'
                </a></li>'; 
            }
			
			
			
            echo '</ul>';*/
            echo '<div class="tab-content">';
             if($a['role'] == 'provider' || $a['role'] == 'both' || $a['role'] == ''){
			 
			 $readonly = 'readonly="readonly"';
			 $disabled = 'disabled="disabled"';
			 $placeholder = esc_html__('City (Select country to enable)','service-finder');
			 
		     $countryarr = (!empty($service_finder_options['allowed-country'])) ? $service_finder_options['allowed-country'] : array();
			 $totalcountry = count($countryarr);
			 
			 if($totalcountry == 1){
				$readonly = '';
				$disabled = '';
				$placeholder = esc_html__('Please select city from suggestion','service-finder');
			 }
              
			 if($signupautosuggestion){
			 $citybox = '<input type="text" class="form-control" autocomplete="off" '.$readonly.' placeholder="'.$placeholder.'" name="signup_city" id="signup_city_bx" placeholder="'.esc_html__('City', 'service-finder').'">';
			 }else{
			 $citybox = '<select '.$readonly.' '.$disabled.' class="form-control sf-form-control sf-select-box" name="signup_city" data-live-search="true" title="'.$placeholder.'" id="signup_city_bx">';
			 $citybox .= '<option value="">'.esc_html__('Select City', 'service-finder').'</option>';
			 $citybox .= '</select>';
			 }
			 
			/* $showotpsection = '';
			 if($show_signup_otp){ 
                    $showotpsection = '<div class="col-md-12">
                      <div class="form-group signupotp-section-bx">
                        <label>
                        '.esc_html__('One Time Password', 'service-finder').'
                        </label>
                        <div class="input-group"> <i class="input-group-addon fa fa-lock"></i>
                          <input name="fillsignupotp" type="text" class="form-control" value="">
                        </div>
                        <a href="javascript:;" class="signupotp_bx">
                        '.esc_html__('Generate One time Password to Confirm Email', 'service-finder').'
                        </a> </div>
                    </div>';
             } */
					  
			  echo '<div id="providertab" class="tab-pane fade in active">
				<h2>Become an influencer</h2>
                <form class="provider_registration_page wd-form-reg_wrap" method="post">
                  <div class="provider-bx clearfix row">
				     
					 <div class="col-md-12">
					 	<div class="form-group">
              <div class="profile-placeholder"></div>
					 		<input type="file" name="profile_image"class="signup-profile-image" accept="image/*" >
						</div>
					 </div>
                	<div class="row">
						<div class="col-md-6">
						  <div class="form-group">
							<input name="signup_user_name" type="text" class="form-control" placeholder="'.esc_html__('Your Name', 'service-finder').'">
						  </div>
						</div>
						<div class="col-md-6">
						  <div class="form-group">
							<input name="signup_user_email" id="signup_user_email_bx" type="text" class="form-control" placeholder="'.esc_html__('Email', 'service-finder').'">
						  </div>
						</div>
					</div>
					<div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <input name="signup_password" type="password" class="form-control" placeholder="'.esc_html__('Password', 'service-finder').'">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <input name="signup_confirm_password" type="password" class="form-control" placeholder="'.esc_html__('Confirm Password', 'service-finder').'">
                      </div>
                    </div>
				 </div>
				';
				  if(!$service_finder_options['quick-signup']){
				  echo '
				  <div class="row">
				  <div class="col-md-6">
                      <div class="form-group">
                      <select class="sf-select-box form-control sf-form-control" name="signup_country" data-live-search="true" title="'.esc_html__('Country', 'service-finder').'" id="signup_country_bx">
                      <option value="">
                        '.esc_html__('Select Country', 'service-finder').'
                        </option>';
					  $allcountry = (!empty($service_finder_options['all-countries'])) ? $service_finder_options['all-countries'] : '';
					  $countries = service_finder_get_countries();
					  if($allcountry){
						  if(!empty($countries)){
							foreach($countries as $key => $country){
								echo '<option value="'.esc_attr($country).'" data-code="'.esc_attr($key).'">'.esc_html__( $country, 'service-finder' ).'</option>';
							}
						  }
					  }else{
					 	 $countryarr = (!empty($service_finder_options['allowed-country'])) ? $service_finder_options['allowed-country'] : '';
						 $totalcountry = count($countryarr);
						 if($countryarr){
						 	foreach($countryarr as $key){
								if($totalcountry == 1){
									$select = 'selected="selected"';
								}else{
									$select = '';
								}
								echo '<option '.$select.' value="'.esc_attr($countries[$key]).'" data-code="'.esc_attr($key).'">'. esc_html__( $countries[$key], 'service-finder' ).'</option>';
							}
						 }
					  }
                      echo '</select>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group" id="autocity_bx">
                        '.$citybox.'
                      </div>
                    </div>
					</div>';
					}
                    echo '
					<div class="row">
						<div class="col-md-12">
                    <div class="form-group">
                      <input type="text" class="form-control" name="signup_phone" id="signup_phone" placeholder="'.esc_html__('Mobile', 'service-finder').'">
                    </div>
                  </div>
				  </div>
				 	<div class="row">
					<div class="col-md-6">
                      <div class="form-group has-select">
                        <select class="sf-select-box form-control sf-form-control" name="signup_category" data-live-search="true" title="'.esc_html__('Category', 'service-finder').'">
                          <option value="">
                          '.esc_html__('Select Category', 'service-finder').'
                          </option>';
                                                   if(class_exists('service_finder_texonomy_plugin')){
												    $limit = 1000;
                                                    $categories = service_finder_getCategoryList($limit);
                                                    $texonomy = 'providers-category';
                                                    if(!empty($categories)){
                                                        foreach($categories as $category){
                                                        
														$term_id = (!empty($category->term_id)) ? $category->term_id : '';
														$term_name = (!empty($category->name)) ? $category->name : '';
                                                        echo '<option value="'.esc_attr($term_id).'" data-content="<span>'.esc_attr($term_name).'</span>">'. $term_name.'</option>';
														
                                                        $term_children = get_term_children($category->term_id,$texonomy);
                                                        if(!empty($term_children)){
															$namearray = array();
															foreach ($term_children as $child) {
																$term = get_term_by( 'id', $child, $texonomy );
																$namearray[$term->name]= $child;
															}
															ksort($namearray);
														
                                                            foreach($namearray as $term_child_id) {
                                
                                                                $term_child = get_term_by('id',$term_child_id,$texonomy);
                                                                
                                                                echo '<option value="'.esc_attr($term_child_id).'" data-content="<span class=\'childcat\'>'.esc_attr($term_child->name).'</span>">'. $term_child->name.'</option>';
                                                                
                                                            }
                                                        }
                                                        
                                                        }
                                                    }	
													}
                        echo '</select>
                      </div>
                    </div>
					 <div class="col-md-6">
                      <div class="form-group has-select">
					  <textarea name="bio" class="form-control signup-about-me" placeholder="About Yourself"></textarea>
					  </div>
					</div>
					  </div>
					';
                      $enablepackage0 = (!empty($service_finder_options['enable-package0'])) ? $service_finder_options['enable-package0'] : '';
					  $enablepackage1 = (!empty($service_finder_options['enable-package1'])) ? $service_finder_options['enable-package1'] : '';
					  $enablepackage2 = (!empty($service_finder_options['enable-package2'])) ? $service_finder_options['enable-package2'] : '';
					  $enablepackage3 = (!empty($service_finder_options['enable-package3'])) ? $service_finder_options['enable-package3'] : '';
					  $defaultpackage = service_finder_get_data($service_finder_options,'default-signup-package');
					  $defaultrolenum = intval(substr($defaultpackage, 8));
					  $free = false;
					  if($enablepackage0 || $enablepackage1 || $enablepackage2 || $enablepackage3){
					  $withoutpackage = false;
					  if($defaultpackage != '')
					  {
						  $defaultpackage = ($selectedpackage != '') ? $selectedpackage : $defaultpackage;
						  echo '<input type="hidden" name="provider-role" value="'.esc_attr($defaultpackage).'" />';
						  if($packagenumber != ''){
						  	$free = service_finder_check_default_package_is_free($selectedpackage);
						  }else{
						  	$free = service_finder_check_default_package_is_free($defaultpackage);
						  }
					  }else{
						  echo '<div class="col-md-6">
							  <div class="form-group">
								<select name="provider-role" class="form-control sf-form-control sf-select-box">
								  <option class="blank" value="">
								  '.esc_html__('Select a Package', 'service-finder').'
								  </option>
								  '.service_finder_getPackages($selectedpackage).'
								</select>
							  </div>
							</div>';
					  }
					  
                     }else{
							$withoutpackage = true;
					  } 
					  
					if($free){
						echo '<input type="hidden" name="isfee" id="isfee" value="yes" />';
					}else{
						echo '<input type="hidden" name="isfee" id="isfee" value="no" />';
					}  
					
					$defaultpackageprice = (isset($service_finder_options['package'.$defaultrolenum.'-price'])) ? $service_finder_options['package'.$defaultrolenum.'-price'] : '';	
					
					$displayfree = ($free || !$defaultpackageprice > 0) ? 'style="display:none;"' : '';
					$displayfree = ($packagenumber != '') ? '' : $displayfree;	
                    if($paymentsystem != 'woocommerce'){
					echo '<div class="col-md-12 sf-card-group margin-less" id="paymethod_bx" '.$displayfree.' >
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
																			echo '<div class="radio sf-radio-checkbox">
																					<input id="'.$key.'_bx" type="radio" name="payment_mode" value="'.esc_attr($key).'">
																					<label for="'.$key.'_bx">'.$label.'</label>
																				</div>';	
																			}	
																		}else{
																		
																		echo '<div class="radio sf-radio-checkbox">
																					<input id="'.$key.'_bx" type="radio" name="payment_mode" value="'.esc_attr($key).'">
																					<label for="'.$key.'_bx">'.$label.'</label>
																				</div>';	
																		}	
																		
																	}
																}	
																}
																}
																
																if($paymentflag == 0){
																	echo '<div class="sf-alert-bx alert-info">';
																	echo esc_html__('Payment method not available.', 'service-finder');
																	echo '</div>';
																}
                      echo '</div>
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
                          '.esc_html__('Select Year', 'service-finder').'
                          </label>
                          <select id="scd_year" name="scd_year" class="form-control sf-form-control sf-select-box"  title="'.esc_html__('Select Year', 'service-finder').'">';
											$year = date('Y');
                                            for($i = $year;$i<=$year+50;$i++){
												echo '<option value="'.esc_attr($i).'">'.$i.'</option>';
											}
                          echo '</select>
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
												echo '<option value="'.esc_attr($i).'">'.$i.'</option>';
											}
                          echo '</select>
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
                            echo '<option value="'.esc_attr($card).'">'.$card.'</option>';
                          }
                                                    
                        echo '</select>
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
												echo '<option value="'.esc_attr($i).'">'.$i.'</option>';
											}
                          echo '</select>
                        </div>
                      </div>
                    </div>
                    <div id="signuppagewiredinfo" class="default-hidden">
                    <div class="col-md-12">';
                        $description = (!empty($service_finder_options['wire-transfer-description'])) ? $service_finder_options['wire-transfer-description'] : '';
                        echo $description;
                    echo '</div>
                  </div>';
				  }
                  $providertermchk = (isset($service_finder_options['terms-condition-checkbox-providers'])) ? esc_attr($service_finder_options['terms-condition-checkbox-providers']) : '';
				  if($providertermchk){
                  echo '<div class="col-md-12">
                  <div class="form-group">
                    <div class="checkbox sf-radio-checkbox">
                    <input type="checkbox" value="yes" name="providertermsncondition_bx" id="providertermsncondition_bx">
                    <label for="providertermsncondition_bx">';
                    $providerterms = (isset($service_finder_options['text-terms-condition-checkbox-providers'])) ? $service_finder_options['text-terms-condition-checkbox-providers'] : '';	
					$allowedhtml = array(
								'a' => array(
									'href' => array(),
									'class' => array(),
									'target' => array()
								),
							);
					echo wp_kses($providerterms,$allowedhtml);
                    echo '</label>
                  </div>
                  </div>
                  </div>';
                  }
				  echo service_finder_captcha('providersignuppage');
				  $loginpage = service_finder_get_url_by_shortcode('[service_finder_login]');
				  $pactype = ($free || $withoutpackage) ? 'yes' : 'no';
                    echo '
                    <div class="row">
                      <div class="col-md-12">
              <div class="form-group">
                <input type="hidden" name="freemode" id="freemode_bx" value="'.$pactype.'" />
                <input type="hidden" name="signup_user_role" value="'.esc_attr($service_finder_Params['role']['provider']).'" />
                <input type="hidden" name="userregister" value="signup">
                <input type="submit" class="btn btn-primary btn-block" name="user-register" value="'.esc_html__('Sign up', 'service-finder').'" />
              </div>
                      </div>
                    </div>
                    <div class="col-md-12 text-center"><small><a data-toggle="tab" href="#customertab" >
					  '.esc_html__("Signup an Customer Account", 'service-finder').'

					  </a> | <a href="'.esc_url($loginpage).'">
                      '.esc_html__('Already Registered?', 'service-finder').'
                      </a></small>
					 </div>
                  </div>
                </form>';
				
				?>
				<div class="sf-other-logins <?php echo sanitize_html_class($socialloginclass); ?>">
              	<ul class="row">
                	<?php 
                    if(class_exists('aonesms'))
                    {
                      echo '<li>';
					  echo do_shortcode('[aonesms_otp_login_signup]');
					  echo '</li>';
                    }
                    ?>
                    
                    <?php
					if($socialsignupwith == 'nextend-social-login') 
					{
						if(class_exists('NextendSocialLogin'))
						{
							echo '<li class="sf-nextend-center">';
							echo do_shortcode('[nextend_social_login]');
							echo '</li>';
						}
					}elseif($socialsignupwith == 'wordpress-social-login')
					{
						if(function_exists('wsl_activate'))
						{
						  	echo '<li>';
							echo do_action( 'wordpress_social_login' );
							echo '</li>';
						}
					}
					?>
                </ul>
              </div>
				<?php
              echo '</div>';
              }
		  	 if(($a['role'] == 'customer' || $a['role'] == 'both' || $a['role'] == '') && $packagenumber == '' ){
			 $proactive = ($a['role'] == 'customer') ? 'in active' : '';
			 /*$showcustomerotpsection = '';
			 if($show_customer_signup_otp){ 
                    $showcustomerotpsection = '<div class="col-md-12">
                      <div class="form-group signupotp-section-bx-customer">
                        <label>
                        '.esc_html__('One Time Password', 'service-finder').'
                        </label>
                        <div class="input-group"> <i class="input-group-addon fa fa-lock"></i>
                          <input name="fillsignupotp_customer" id="fillsignupotp-bx-customer" type="text" class="form-control" value="">
                        </div>
                        <a href="javascript:;" class="signupotp_bx_customer">
                        '.esc_html__('Generate One time Password to Confirm Email', 'service-finder').'
                        </a> </div>
                    </div>';
             }*/
			 
			 
              echo '<div id="customertab" class="tab-pane fade '.$proactive.'">
			  <h2>Signup an Customer</h2>
                <form class="customer_registration_page" method="post">
                  <div class="customer-bx clearfix row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <input name="signup_first_name" type="text" class="form-control" placeholder="'.esc_html__('First Name', 'service-finder').'">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <input name="signup_last_name" type="text" class="form-control" placeholder="'.esc_html__('Last Name', 'service-finder').'">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <input name="signup_user_name" type="text" class="form-control" placeholder="'.esc_html__('Username', 'service-finder').'">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <input name="signup_user_email" id="signup_customer_user_email_bx" type="text" class="form-control" placeholder="'.esc_html__('Email', 'service-finder').'">
                      </div>
                    </div>';
					//'.$showcustomerotpsection.'
                    echo '<div class="col-md-6">
                      <div class="form-group">
                        <input name="signup_password" type="password" class="form-control" placeholder="'.esc_html__('Password', 'service-finder').'">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <input name="signup_confirm_password" type="password" class="form-control" placeholder="'.esc_html__('Confirm Password', 'service-finder').'">
                      </div>
                    </div>';
                  $customertermchk = (isset($service_finder_options['terms-condition-checkbox-customers'])) ? esc_attr($service_finder_options['terms-condition-checkbox-customers']) : '';
				  if($customertermchk){
                  echo '<div class="col-md-12">
                    <div class="form-group">
                    <div class="checkbox sf-radio-checkbox">
                    <input type="checkbox" value="yes" name="customertermsncondition_bx" id="customertermsncondition_bx">
                    <label for="customertermsncondition_bx">';
                    $customerterms = (isset($service_finder_options['text-terms-condition-checkbox-customers'])) ? $service_finder_options['text-terms-condition-checkbox-customers'] : '';	
					$allowedhtml = array(
								'a' => array(
									'href' => array(),
									'class' => array(),
									'target' => array()
								),
							);
					echo wp_kses($customerterms,$allowedhtml);
                    echo '</label>
                  </div>
                  	</div>
                  </div>';
                  }
				  echo service_finder_captcha('customersignuppage');
				  $loginpage = service_finder_get_url_by_shortcode('[service_finder_login]');
                  echo '<div class="col-md-12">
						 <div class="form-group">
						  <input type="hidden" name="signup_user_role" value="'.esc_attr($service_finder_Params['role']['customer']).'" />
						  <input type="submit" class="btn btn-primary btn-block" name="user-register" value="'.esc_html__('Sign up', 'service-finder').'" />
						 </div>
                    </div>
                    <div class="col-md-12 text-center"> <small><a data-toggle="tab" href="#providertab" >
					  '.esc_html__("Signup an Influencer Account", 'service-finder').'

					  </a> | <a href="'.esc_url($loginpage).'" class="loginform">
                      '.esc_html__('Already Registered?', 'service-finder').'
                      </a></small> </div>
                  </div>
                </form>';
				?>
				<div class="sf-other-logins <?php echo sanitize_html_class($socialloginclass); ?>">
              	<ul class="row">
                	<?php 
                    if(class_exists('aonesms'))
                    {
                      echo '<li>';
					  echo do_shortcode('[aonesms_otp_login_signup]');
					  echo '</li>';
                    }
                    ?>
                    
                    <?php
					if($socialsignupwith == 'nextend-social-login') 
					{
						if(class_exists('NextendSocialLogin'))
						{
							echo '<li class="sf-nextend-center">';
							echo do_shortcode('[nextend_social_login]');
							echo '</li>';
						}
					}elseif($socialsignupwith == 'wordpress-social-login')
					{
						if(function_exists('wsl_activate'))
						{
						  	echo '<li>';
							echo do_action( 'wordpress_social_login' );
							echo '</li>';
						}
					}
					?>
                </ul>
              </div>
				<?php
              echo '</div>';
             }
            echo '</div>';
            
      //     }else{ 
      //     echo esc_html__('You are already logged in. Please logout for signup','service-finder');
		  // }
          echo '</div>
        </div>';
