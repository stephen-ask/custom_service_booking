<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/

?>
<?php 
if(get_option('timezone_string') != ""){
date_default_timezone_set(get_option('timezone_string'));
}


wp_enqueue_script('service_finder-js-upgrade');
$service_finder_options = get_option('service_finder_options');
$wpdb = service_finder_plugin_global_vars('wpdb');
$service_finder_Tables = service_finder_plugin_global_vars('service_finder_Tables');
$paymentsystem = service_finder_plugin_global_vars('paymentsystem');

$walletamount = service_finder_get_wallet_amount($globalproviderid);

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

wp_add_inline_script( 'service_finder-js-upgrade', '/*Declare global variable*/
var user_id = "'.$globalproviderid.'";
var walletamount = "'.$walletamount.'";
var twocheckoutaccountid = "'.$twocheckoutaccountid.'";
var twocheckoutpublishkey = "'.$twocheckoutpublishkey.'";
var twocheckouttype = "'.$twocheckouttype.'";
var twocheckoutmode = "'.$twocheckoutmode.'";
', 'after' );

$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->feature.' WHERE `provider_id` = %d',$globalproviderid));

$current_role = get_user_meta($globalproviderid,'provider_role',true);
?>
<div class="panel panel-default">
<div class="panel-heading sf-panel-heading">
  <h3 class="panel-tittle m-a0"><span class="fa fa-gear"></span> <?php echo (!empty($service_finder_options['label-upgrade'])) ? esc_html($service_finder_options['label-upgrade']) : esc_html__('Upgrade Account', 'service-finder'); ?> </h3>
  
  <div class="sf-cancel-membership sf-membership-delete">
  <?php if(service_finder_check_provider_membership_status($globalproviderid) != 'draft' && $service_finder_options['payment-type'] != 'recurring'){ ?>
  <a href="javascript:;" data-toggle="tooltip" data-placement="top" title="<?php echo esc_html__('Delete Profile', 'service-finder'); ?>" class="btn btn-danger cancelmembership" data-providerid="<?php echo esc_attr($globalproviderid); ?>"><?php echo esc_html__('Delete Profile', 'service-finder'); ?> </a>
  <?php } ?>
  <?php if($current_role != "" && $service_finder_options['payment-type'] == 'recurring'){ ?>
    <a href="javascript:;" data-toggle="tooltip" data-placement="top" title="<?php echo esc_html__('Delete Profile', 'service-finder'); ?>" class="btn btn-danger cancel-subscription" data-url="<?php echo service_finder_get_url_by_shortcode('[service_finder_my_account]'); ?>" data-userid="<?php echo esc_attr($globalproviderid); ?>"><i class="fa fa fa-times"></i> <?php echo esc_html__('Cancel Subscription', 'service-finder'); ?> </a>
<?php }elseif($current_role == "" && $service_finder_options['payment-type'] == 'recurring'){
	?>
	<a href="javascript:;" data-toggle="tooltip" data-placement="top" title="<?php echo esc_html__('Delete Profile', 'service-finder'); ?>" class="btn btn-danger cancelmembership" data-providerid="<?php echo esc_attr($globalproviderid); ?>"><?php echo esc_html__('Delete Profile', 'service-finder'); ?> </a>
	<?php
	} ?>  
<?php if(!empty($row)){ ?>
	<a href="javascript:;" data-toggle="tooltip" data-placement="top" title="<?php echo esc_html__('Cancel Featured/Featured Request', 'service-finder'); ?>" class="btn btn-primary cancel-featured" data-userid="<?php echo esc_attr($globalproviderid); ?>"><i class="fa fa fa-times"></i> <?php echo esc_html__('Cancel Featured', 'service-finder'); ?> </a>
<?php } ?>    
	</div>
</div>
<div class="panel-body sf-panel-body padding-30">
	<?php if(service_finder_check_provider_membership_status($globalproviderid) == 'draft' && $service_finder_options['payment-type'] != 'recurring'){
	echo '<div class="alert alert-warning">'.esc_html__('Your membership have been cancelled', 'service-finder').'</div>';
	} ?>
  <!--Upgrade Provider Account-->
      <?php
      $enablepackage0 = (!empty($service_finder_options['enable-package0'])) ? $service_finder_options['enable-package0'] : '';
	  $enablepackage1 = (!empty($service_finder_options['enable-package1'])) ? $service_finder_options['enable-package1'] : '';
	  $enablepackage2 = (!empty($service_finder_options['enable-package2'])) ? $service_finder_options['enable-package2'] : '';
	  $enablepackage3 = (!empty($service_finder_options['enable-package3'])) ? $service_finder_options['enable-package3'] : '';
	  
	  $providerstatus = get_user_meta($globalproviderid, 'current_provider_status', true);
	  $expiredate = '';
	  
	  if($enablepackage1 || $enablepackage2 || $enablepackage3 || ($enablepackage0 && service_finder_is_social_user($globalproviderid) && $providerstatus != 'expire')){
	  ?>
      <form class="upgrade-form" method="post">
        <div class="panel-body padding-30">
          <div class="row">
          <?php
		  $paytype = get_user_meta($globalproviderid,'pay_type',true);
		  if($paytype == 'recurring'){
			  $recurring_profile_period = get_user_meta($globalproviderid,'recurring_profile_period',true);
			  if($recurring_profile_period == 'Month'){
			  	$expire_limit = 30;
			  }elseif($recurring_profile_period == 'Year'){
			  	$expire_limit = 365;
			  }elseif($recurring_profile_period == 'Week'){
			  	$expire_limit = 7;
			  }elseif($recurring_profile_period == 'Day'){
			  	$expire_limit = 1;
			  }
		  }else{
			  $expire_limit = get_user_meta($globalproviderid,'expire_limit',true);
		  }
		  
		  if($expire_limit > 0){
          $current_role = get_user_meta($globalproviderid,'provider_role',true);
		  $provider_activation_time = get_user_meta($globalproviderid,'provider_activation_time',true);
		  if($current_role != ""){
	   	  $roleNum = intval(substr($current_role, 8));
		  }else{
		  $roleNum = '';
		  }
		  $packagename = (!empty($service_finder_options['package'.$roleNum.'-name'])) ? $service_finder_options['package'.$roleNum.'-name'] : '';
		  $timeInSec = $provider_activation_time['time'];
		  
		  $activationdate = date('Y-m-d H:i:s',$timeInSec);
		  $date = new DateTime($activationdate);
		  $date->add(new DateInterval('P'.$expire_limit.'D'));
		  $expiredate = $date->format('Y-m-d');
		  $expiretime = $date->format('H:i:s');
		  
		  $packageprice = (!empty($service_finder_options['package'.$roleNum.'-price'])) ? $service_finder_options['package'.$roleNum.'-price'] : '';
  		  $free = (trim($packageprice) > 0) ? false : true;
		  if($free) {
		  $freeclass = 'free';
		  }else{
		  $freeclass = '';
		  }
		  
		  ?>
          <?php if(get_user_meta($globalproviderid, 'upgrade_request_status',true) == 'pending') { 
		  $upgrade_request = get_user_meta($globalproviderid, 'upgrade_request',true);
		  $requestedpackage = $upgrade_request['provider_role'];
		  $requestedpackageroleNum = intval(substr($requestedpackage, 8));
		  $requestedpackagename = (!empty($service_finder_options['package'.$requestedpackageroleNum.'-name'])) ? $service_finder_options['package'.$requestedpackageroleNum.'-name'] : '';
		  ?>
          <div class='alert alert-info'><?php echo sprintf(esc_html__('Payment via wire transfer is pending for package (%s) upgrade', 'service-finder'),$requestedpackagename); ?></div>
          <?php } ?>
          <div class="sf-select-plan">
            <div class="sf-plan-left-block">
                <h1><?php echo esc_html__('Current Package', 'service-finder'); ?></h1>
                <p><?php echo esc_html($packagename); ?></p>
                <?php if($current_role != 'package_0'){ ?>
                <div class="sf-renew-btn"><a class="btn btn-renew renewpackage <?php echo sanitize_html_class($freeclass); ?>" data-packageid="<?php echo esc_attr($current_role); ?>"  href="javascript:;"><i class="fa"></i> <?php echo esc_html__('Renew Current Package','service-finder') ?></a></div>
                <?php } ?>
            </div>
            <div class="sf-plan-right-block">
                <div class='countdown' data-date="<?php echo esc_attr($expiredate)?>" data-time="<?php echo esc_attr($expiretime); ?>"></div>
            </div>
          </div>
          <?php } ?>
          <?php
		  $current_role = get_user_meta($globalproviderid,'provider_role',true);
		  if($current_role != ""){
		  $roleNum = intval(substr($current_role, 8));
		  }else{
		  $roleNum = '';
		  }
		  $totalpackage = 0;
          for ($m = 1; $m <= 3; $m++) {
		  	$enablepackage = (!empty($service_finder_options['enable-package'.$m])) ? $service_finder_options['enable-package'.$m] : '';
			if($enablepackage > 0 && $m >= 0){
				$totalpackage++;
			}
		  }
		  switch($totalpackage){
		  	case 1:
				$packageclass =  'one-package-show';
				break;
			case 2:
				$packageclass =  'two-package-show';
				break;
			case 3:
				$packageclass =  'three-package-show';
				break;		
			default:
				$packageclass =  '';
				break;	
		  }
		  ?>
          <ul class="row sf-plans-list list-unstyled <?php echo sanitize_html_class($packageclass); ?>" id="sf-upgrade-plans">
            <?php
			$j = (service_finder_is_social_user($globalproviderid) && $providerstatus != 'expire') ? 0 : 1;
			for ($i=$j; $i <= 3; $i++) {
			
			$enablepackage = (!empty($service_finder_options['enable-package'.$i])) ? $service_finder_options['enable-package'.$i] : '';
			if($enablepackage > 0){
			
			$class3 = '';
			
			$enablepackage = (!empty($service_finder_options['enable-package'.$i])) ? $service_finder_options['enable-package'.$i] : '';
			$packageprice = (!empty($service_finder_options['package'.$i.'-price'])) ? $service_finder_options['package'.$i.'-price'] : '';
			$currency = service_finder_currencycode();
			$billingPeriod = '';
			if(isset($service_finder_options['enable-package'.$i]) && $enablepackage > 0 && $i >= $roleNum){
			$free = (trim($packageprice) > 0) ? false : true;
			
			if($current_role == 'package_'.$i){
			$checked = 'checked="checked"';
			$class3 = 'selected-plan';
			$selectedpackage = 'package_'.esc_attr($i);
			}else{
			$checked = '';
			$class3 = '';
			$selectedpackage = '';
			}
			$billingPeriod = esc_html__('year','service-finder');
			$packagebillingperiod = (!empty($service_finder_options['package'.$i.'-billing-period'])) ? $service_finder_options['package'.$i.'-billing-period'] : '';
			switch ($packagebillingperiod) {
				case 'Year':
					$billingPeriod = esc_html__('year','service-finder');
					break;
				case 'Month':
					$billingPeriod = esc_html__('month','service-finder');
					break;
				case 'Week':
					$billingPeriod = esc_html__('week','service-finder');
					break;
				case 'Day':
					$billingPeriod = esc_html__('day','service-finder');
					break;
			}
			}
			
			if($i >= 0){
			if (isset($service_finder_options['payment-type']) && ($service_finder_options['payment-type'] == 'recurring') && $service_finder_options['package'.$i.'-price'] > 0) {
			$displayprice = trim($service_finder_options['package'.$i.'-price']).' '.$currency;
			$price = (!empty($service_finder_options['package'.$i.'-price'])) ? trim($service_finder_options['package'.$i.'-price']) : '';
			} else {
			$currentPayType = get_user_meta($globalproviderid,'pay_type',true);
			if($currentPayType == 'single'){
			$paidAmount =  get_user_meta($globalproviderid,'profile_amt',true);
			}
			if($currentPayType == 'single'){
			if($current_role == 'package_'.$i){
			$price = (!empty($service_finder_options['package'.$i.'-price'])) ? trim($service_finder_options['package'.$i.'-price']) : '';	
			}else{	
			/*$pacprice = (!empty($service_finder_options['package'.$i.'-price'])) ? $service_finder_options['package'.$i.'-price'] : 0;
			$price = $pacprice - $paidAmount;*/
			$price = (!empty($service_finder_options['package'.$i.'-price'])) ? trim($service_finder_options['package'.$i.'-price']) : '';						
			}
			
			}else{
			$price = (!empty($service_finder_options['package'.$i.'-price'])) ? trim($service_finder_options['package'.$i.'-price']) : '';								
			}
			$displayprice = $price.' '.$currency;
			}
			$class = ($price > 0 && $price != "") ? '' : 'free';
			
			$packageexpday = '';
			if (isset($service_finder_options['payment-type']) && $service_finder_options['payment-type'] == 'single') {
				$packageexpday = (!empty($service_finder_options['package'.$i.'-expday'])) ? $service_finder_options['package'.$i.'-expday'] : '';
			}
			?>
            <li data-toggle="popover" data-container="body" data-placement="top" data-html="true" id="packageinfo-<?php echo esc_attr($i); ?>" data-trigger="hover" class="col-md-4 sf-plans-outer <?php echo sanitize_html_class($class).' '.sanitize_html_class($class3); ?>" data-packageid="<?php echo 'package_'.esc_attr($i); ?>">
            <div id="popover-content-packageinfo-<?php echo esc_attr($i); ?>" class="hide">
                                    <ul class="list-unstyled margin-0 booking-payment-data">
                                        <?php echo service_finder_display_package_capability($i); ?>
                                    </ul>
                                </div>
                  <div class="sf-plans-bx">
                        <h5 class="sf-plans-name"><?php echo (!empty($service_finder_options['package'.$i.'-name'])) ? esc_html($service_finder_options['package'.$i.'-name']) : ''; ?></h5>
                        <div class="sf-plans-price"><?php echo ($price > 0 && $price != "") ? esc_html($displayprice) : esc_html__('Free','service-finder'); ?></div>
                        <?php if($billingPeriod != '' && $service_finder_options['payment-type'] == 'recurring'){ ?>
                        <span class="sf-account-billingperiod"><?php echo esc_html($billingPeriod); ?></span>
                        <?php } ?>
                        <?php if($packageexpday != ''){ ?>
                        <span class="sf-account-expperiod"><?php echo esc_html($packageexpday).' '.esc_html__('Days','service-finder'); ?></span>
                        <?php } ?>
                        <div class="sf-plans-done"><i class="fa fa-check"></i></div>
                    </div>
            </li>	
<?php
			}
			}
			}
			?>
          </ul>      
          
          
          
            <?php  if($paymentsystem != 'woocommerce'){ ?>
            <div class="col-lg-12 sf-card-my-account default-hidden sf-upgrade-payment" id="payment_method">
            	<?php echo service_finder_display_wallet_amount($globalproviderid); ?>  
              <div class="form-group form-inline">
                <?php
				$payment_methods = $service_finder_options['payment-methods'];
				if(!empty($payment_methods)){
				foreach($payment_methods as $key => $value){
				if($key != 'cod'){
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
						if($key == 'payumoney'){
							if($service_finder_options['payment-type'] == 'single'){
							echo '<div class="radio sf-radio-checkbox">
									<input id="upgrade_'.$key.'" type="radio" name="pay_mode" value="'.esc_attr($key).'_upgrade">
									<label for="upgrade_'.$key.'">'.$label.'</label>
								</div>';
							}	
						}else{
						
						echo '<div class="radio sf-radio-checkbox">
									<input id="upgrade_'.$key.'" type="radio" name="pay_mode" value="'.esc_attr($key).'_upgrade">
									<label for="upgrade_'.$key.'">'.$label.'</label>
								</div>';	
						}		
					}
				}	
				}
				}
				
				if(service_finder_getUserRole($current_user->ID) == 'administrator'){
				echo '<div class="radio sf-radio-checkbox">
									<input id="skippayment" type="radio" name="pay_mode" value="skippayment">
									<label for="skippayment">'.esc_html__('Skip Payment','service-finder').'</label>
								</div>';
				}				
				?>
                <?php echo service_finder_add_wallet_option('pay_mode','upgrade'); ?>
              </div>
            </div>
            <div id="stripeinfo" class="default-hidden">
              <div class="col-md-8">
                <div class="form-group">
                  <label>
                  <?php esc_html_e('Card Number', 'service-finder'); ?>
                  </label>
                  <div class="input-group"> <i class="input-group-addon fa fa-credit-card"></i>
                    <input type="text" name="crd_number" id="crd_number" class="form-control sf-form-control">
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label>
                  <?php esc_html_e('CVC', 'service-finder'); ?>
                  </label>
                  <div class="input-group"> <i class="input-group-addon fa fa-ellipsis-h"></i>
                    <input type="text" name="crd_cvc" id="crd_cvc" class="form-control sf-form-control">
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>
                  <?php esc_html_e('Select Month', 'service-finder'); ?>
                  </label>
                  <select id="crd_month" name="crd_month" class="form-control sf-form-control sf-select-box" title="Select Month">
                  <option value="1"><?php echo esc_html__('January', 'service-finder') ?></option>
                  <option value="2"><?php echo esc_html__('February', 'service-finder')?></option>
                  <option value="3"><?php echo esc_html__('March', 'service-finder')?></option>
                  <option value="4"><?php echo esc_html__('April', 'service-finder')?></option>
                  <option value="5"><?php echo esc_html__('May', 'service-finder')?></option>
                  <option value="6"><?php echo esc_html__('June', 'service-finder')?></option>
                  <option value="7"><?php echo esc_html__('July', 'service-finder')?></option>
                  <option value="8"><?php echo esc_html__('August', 'service-finder')?></option>
                  <option value="9"><?php echo esc_html__('September', 'service-finder')?></option>
                  <option value="10"><?php echo esc_html__('October', 'service-finder')?></option>
                  <option value="11"><?php echo esc_html__('November', 'service-finder')?></option>
                  <option value="12"><?php echo esc_html__('December', 'service-finder')?></option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>
                  <?php esc_html_e('Select Year', 'service-finder'); ?>
                  </label>
                  <select name="crd_year" id="crd_year" class="form-control sf-form-control sf-select-box"  title="Select Year">
                    <?php
											$year = date('Y');
                                            for($i = $year;$i<=$year+50;$i++){
												echo '<option value="'.esc_attr($i).'">'.$i.'</option>';
											}
											?>
                  </select>
                </div>
              </div>
            </div>
            <div id="twocheckoutinfo" class="default-hidden">
              <div class="col-md-8">
                <div class="form-group">
                  <label>
                  <?php esc_html_e('Card Number', 'service-finder'); ?>
                  </label>
                  <div class="input-group"> <i class="input-group-addon fa fa-credit-card"></i>
                    <input type="text" name="twocheckout_crd_number" id="twocheckout_crd_number" class="form-control sf-form-control">
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label>
                  <?php esc_html_e('CVC', 'service-finder'); ?>
                  </label>
                  <div class="input-group"> <i class="input-group-addon fa fa-ellipsis-h"></i>
                    <input type="text" name="twocheckout_crd_cvc" id="twocheckout_crd_cvc" class="form-control sf-form-control">
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>
                  <?php esc_html_e('Select Month', 'service-finder'); ?>
                  </label>
                  <select id="twocheckout_crd_month" name="twocheckout_crd_month" class="form-control sf-form-control sf-select-box" title="Select Month">
                  <option value="1"><?php echo esc_html__('January', 'service-finder') ?></option>
                  <option value="2"><?php echo esc_html__('February', 'service-finder')?></option>
                  <option value="3"><?php echo esc_html__('March', 'service-finder')?></option>
                  <option value="4"><?php echo esc_html__('April', 'service-finder')?></option>
                  <option value="5"><?php echo esc_html__('May', 'service-finder')?></option>
                  <option value="6"><?php echo esc_html__('June', 'service-finder')?></option>
                  <option value="7"><?php echo esc_html__('July', 'service-finder')?></option>
                  <option value="8"><?php echo esc_html__('August', 'service-finder')?></option>
                  <option value="9"><?php echo esc_html__('September', 'service-finder')?></option>
                  <option value="10"><?php echo esc_html__('October', 'service-finder')?></option>
                  <option value="11"><?php echo esc_html__('November', 'service-finder')?></option>
                  <option value="12"><?php echo esc_html__('December', 'service-finder')?></option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>
                  <?php esc_html_e('Select Year', 'service-finder'); ?>
                  </label>
                  <select name="twocheckout_crd_year" id="twocheckout_crd_year" class="form-control sf-form-control sf-select-box"  title="Select Year">
                    <?php
											$year = date('Y');
                                            for($i = $year;$i<=$year+50;$i++){
												echo '<option value="'.esc_attr($i).'">'.$i.'</option>';
											}
											?>
                  </select>
                </div>
              </div>
            </div>
            <div id="payulataminfo" class="default-hidden">
              <div class="col-md-12">
              <div class="form-group">
                <label>
                <?php esc_html_e('Select Card', 'service-finder'); ?>
                </label>
               <select id="payulatam_upgrade_cardtype" name="payulatam_signup_cardtype" class="form-control sf-form-control sf-select-box"  title="<?php esc_html_e('Select Card', 'service-finder'); ?>">
                  <?php
                  $country = (isset($service_finder_options['payulatam-country'])) ? $service_finder_options['payulatam-country'] : '';
                  $cards = service_finder_get_cards($country);
                  foreach($cards as $card){
                    echo '<option value="'.esc_attr($card).'">'.$card.'</option>';
                  }
                                            
                  ?>
                </select>
              </div>
            </div>
              <div class="col-md-8">
                <div class="form-group">
                  <label>
                  <?php esc_html_e('Card Number', 'service-finder'); ?>
                  </label>
                  <div class="input-group"> <i class="input-group-addon fa fa-credit-card"></i>
                    <input type="text" name="payulatam_cd_number" id="payulatam_crd_number" class="form-control sf-form-control">
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label>
                  <?php esc_html_e('CVC', 'service-finder'); ?>
                  </label>
                  <div class="input-group"> <i class="input-group-addon fa fa-ellipsis-h"></i>
                    <input type="text" name="payulatam_cd_cvc" id="payulatam_crd_cvc" class="form-control sf-form-control">
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>
                  <?php esc_html_e('Select Month', 'service-finder'); ?>
                  </label>
                  <select id="payulatam_crd_month" name="payulatam_cd_month" class="form-control sf-form-control sf-select-box" title="<?php esc_html_e('Select Month', 'service-finder'); ?>">
                      <option value="01"><?php esc_html_e('January', 'service-finder'); ?></option>
                      <option value="02"><?php esc_html_e('February', 'service-finder'); ?></option>
                      <option value="03"><?php esc_html_e('March', 'service-finder'); ?></option>
                      <option value="04"><?php esc_html_e('April', 'service-finder'); ?></option>
                      <option value="05"><?php esc_html_e('May', 'service-finder'); ?></option>
                      <option value="06"><?php esc_html_e('June', 'service-finder'); ?></option>
                      <option value="07"><?php esc_html_e('July', 'service-finder'); ?></option>
                      <option value="08"><?php esc_html_e('August', 'service-finder'); ?></option>
                      <option value="09"><?php esc_html_e('September', 'service-finder'); ?></option>
                      <option value="10"><?php esc_html_e('October', 'service-finder'); ?></option>
                      <option value="11"><?php esc_html_e('November', 'service-finder'); ?></option>
                      <option value="12"><?php esc_html_e('December', 'service-finder'); ?></option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>
                  <?php esc_html_e('Select Year', 'service-finder'); ?>
                  </label>
                  <select name="payulatam_cd_year" id="payulatam_crd_year" class="form-control sf-form-control sf-select-box"  title="<?php esc_html_e('Select Year', 'service-finder'); ?>">
                    <?php
											$year = date('Y');
                                            for($i = $year;$i<=$year+50;$i++){
												echo '<option value="'.esc_attr($i).'">'.$i.'</option>';
											}
											?>
                  </select>
                </div>
              </div>
            </div>
            <div id="upgradewiredinfo" class="default-hidden">
                    <div class="col-md-12 margin-b-20">
                        <?php
                        $description = (!empty($service_finder_options['wire-transfer-description'])) ? $service_finder_options['wire-transfer-description'] : '';
                        echo $description;
                        ?>
                    </div>
                  </div>
            <?php }else{
			echo '<div class="col-md-12 default-hidden" id="skipoption">
              <div class="form-group form-inline">';
			if(service_finder_getUserRole($current_user->ID) == 'administrator'){
			
			echo '<div class="radio sf-radio-checkbox">
								<input id="skipforadmin" type="radio" name="upgrade_woopayment" value="skippayment">
								<label for="skipforadmin">'.esc_html__('Skip Payment','service-finder').'</label>
							</div>';
			
			echo '<input type="hidden" name="skip_pay_mode" value="skippayment">';
			}
			
			echo service_finder_add_wallet_option('upgrade_woopayment','upgrade');
			echo service_finder_add_woo_commerce_option('upgrade_woopayment','upgrade');
			echo '</div></div>';					
			} ?>
            <div class="col-md-12 default-hidden" id="proupgrade">
              <input type="hidden" name="expiredate" id="expiredate" value="<?php echo esc_attr($expiredate); ?>" />
              <input type="hidden" name="provider-role" id="provider-role" value="" />
              <input type="hidden" name="freemode" id="upgradefreemode" value="" />
              <input type="hidden" name="user_id" value="<?php echo esc_attr($globalproviderid); ?>">
              <input type="hidden" name="userregister" value="upgrade">
              <input type="submit" class="btn btn-primary btn-block" name="user-register" value="<?php esc_html_e('Continue', 'service-finder'); ?>" />
            </div>
          </div>
        </div>
      </form>
      <?php }else{
	  echo '<div class="row sf-upgrade-bx">
		  <div class="alert alert-warning">'.esc_html__('There is no active package','service-finder').'</div>
		  </div>';
	  } ?>
</div>
</div>
<?php if($service_finder_options['provider-feature']){ ?>
<div class="panel panel-default">
<div class="panel-heading sf-panel-heading">
  <h3 class="panel-tittle m-a0"><span class="fa fa-money"></span> <?php esc_html_e('Featured', 'service-finder'); ?> </h3>
</div>
<div class="panel-body sf-panel-body padding-30">
  <?php if(service_finder_check_profile_after_trial_expire($globalproviderid)){ ?>
      <?php if(service_finder_check_display_features_after_social_login($globalproviderid)){ ?>
      <!--Request to make feature-->
        <div id="feature-req-bx">
          <?php
                                                       
if(!empty($row)){
$getstatus = $row->status;
$getfeature_status = $row->feature_status;
}else{
$getstatus = '';
$getfeature_status = '';
}
if(empty($row) || $getstatus == 'Declined' || $getstatus == 'cancelled' || $getfeature_status == 'expire'){
echo '<div class="alert alert-info">'.esc_html__('You can submit request to admin in order to become featured on the site, after admin approval you will be required to make payment defined by the admin and after payment you will be featured on the site.','service-finder').'</div>';
if($getstatus == 'Declined'){
echo '<div class="alert alert-warning"><a href="javascript:;" class="close hideinfomsg" data-id="'.esc_attr($globalproviderid).'" data-dismiss="alert" aria-label="close">&times;</a>'.esc_html__('Your request has been declined.','service-finder').'</div>';
}elseif($getstatus == 'cancelled'){
echo '<div class="alert alert-warning">'.esc_html__('Payment failed via wire transfer.','service-finder').'</div>';
}elseif($getfeature_status == 'expire'){
echo '<div class="alert alert-warning">'.esc_html__('Your feature account has been expired.','service-finder').'</div>';
}
?>
<form class="feature-form" method="post">
<div class="col-lg-12">
<div class="form-group form-inline">
<label>
<?php esc_html_e('Make Featured', 'service-finder'); ?>
</label>
<br>
<div class="checkbox sf-radio-checkbox">
<input type="checkbox" id="make-feature" name="make-feature" value="yes">
<label for="make-feature"></label>
</div>
</div>
</div>
<div id="feature-bx" class="default-hidden">
<div class="col-lg-12">
<div class="form-group">
<label>
<?php esc_html_e('Number of Days', 'service-finder'); ?>
</label>
<div class="input-group">
<input type="text" class="form-control sf-form-control" name="featuredays" id="featuredays" value="<?php echo (!empty($service_finder_options['feature-min-max-days-min'])) ? esc_attr($service_finder_options['feature-min-max-days-min']) : '' ?>" >
<input type="hidden" name="minvalue" id="minvalue" value="<?php echo (!empty($service_finder_options['feature-min-max-days-min'])) ? esc_attr($service_finder_options['feature-min-max-days-min']) : '' ?>" >
<input type="hidden" name="maxvalue" id="maxvalue" value="<?php echo (!empty($service_finder_options['feature-min-max-days-max'])) ? esc_attr($service_finder_options['feature-min-max-days-max']) : '' ?>" >
</div>
</div>
</div>
<div class="col-md-12">
<input type="hidden" name="user_id" value="<?php echo esc_attr($globalproviderid); ?>">
<input type="submit" class="btn btn-primary btn-block" name="feature-request" value="<?php esc_html_e('Request for Feature', 'service-finder'); ?>" />
</div>
</div>
</form>
<?php 
}elseif($row->status == 'Payment Pending'){
$amt = service_finder_money_format($row->amount);
echo '<div class="alert-bx alert-info">'.sprintf( esc_html__('Congratulations! Your request have been approved. Please make a payment of %s to be featured', 'service-finder'), $amt ).'</div>';
echo '<form class="feature-payment-form sf-card-group" method="post">';
		$payment_methods = (!empty($service_finder_options['payment-methods'])) ? $service_finder_options['payment-methods'] : '';															
		?>
		<?php echo service_finder_display_wallet_amount($globalproviderid); ?>  
		<?php
		if($paymentsystem == 'woocommerce'){
		echo '<div class="col-lg-12">
				  <div class="form-group form-inline">';
				echo service_finder_add_wallet_option('feature_woopayment','feature');
				echo service_finder_add_woo_commerce_option('feature_woopayment','feature');
				echo '</div></div>';
		?>
<div class="col-md-12">
<input type="hidden" name="feature_id" value="<?php echo esc_attr($row->id); ?>">
<input type="submit" class="btn btn-primary btn-block" name="feature-payment" value="<?php esc_html_e('Pay Now', 'service-finder'); ?>" />
</div>
		<?php
		}else{
		if(!empty($payment_methods)){
		echo '<div class="panel-body padding-30">
		<div class="row">
		<div class="form-group form-inline">';
		foreach($payment_methods as $key => $value){
		if($key != 'cod'){
			if($key == 'stripe'){
			$label = '<img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/mastercard.jpg" title="'.esc_html__('Stripe','service-finder').'" alt="'.esc_html__('mastercard','service-finder').'"><img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/payment.jpg" title="'.esc_html__('Stripe','service-finder').'" alt="'.esc_html__('american express','service-finder').'"><img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/discover.jpg" title="'.esc_html__('Stripe','service-finder').'" alt="'.esc_html__('discover','service-finder').'"><img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/visa.jpg" title="'.esc_html__('Stripe','service-finder').'" alt="'.esc_html__('visa','service-finder').'">';
			}elseif($key == 'twocheckout'){
			 $label = '<img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/twocheckout.jpg" title="'.esc_html__('2Checkout','service-finder').'" alt="'.esc_html__('2Checkout','service-finder').'">';
			}elseif($key == 'wired'){
			$label = '<img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/wired.jpg" title="'.esc_html__('Wire Transfer','service-finder').'" alt="'.esc_html__('Wired','service-finder').'">';
			}elseif($key == 'payumoney'){
			 $label = '<img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/payumoney.jpg" title="'.esc_html__('PayU Money','service-finder').'" alt="'.esc_html__('PayU Money','service-finder').'">';
			}elseif($key == 'payulatam'){
			$label = '<img src="'.get_template_directory_uri().'/inc/images/payment/payulatam.jpg" title="'.esc_html__('PayU Latam','service-finder').'" alt="'.esc_html__('PayU Latam','service-finder').'" alt="'.esc_html__('PayU Latam','service-finder').'">';
			}else{
			$label = '<img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/paypal.jpg" title="'.esc_html__('Paypal','service-finder').'" alt="'.esc_html__('Paypal','service-finder').'">';
			}
			if($value == 1){
				echo '<div class="radio sf-radio-checkbox">
							<input id="feature_'.$key.'" type="radio" name="payment_mode" value="'.$key.'">
							<label for="feature_'.esc_attr($key).'">'.$label.'</label>
						</div>';	
			}
		}
		}
		?>
		<?php echo service_finder_add_wallet_option('payment_mode','feature'); ?>
        <?php echo '</div></div></div>'; ?>
          <div id="featurecardinfo" class="default-hidden">
            <div class="col-md-8">
              <div class="form-group">
                <label>
                <?php esc_html_e('Card Number', 'service-finder'); ?>
                </label>
                <div class="input-group"> <i class="input-group-addon fa fa-credit-card"></i>
                  <input type="text" id="fcd_number" name="fcd_number" class="form-control sf-form-control">
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>
                <?php esc_html_e('CVC', 'service-finder'); ?>
                </label>
                <div class="input-group"> <i class="input-group-addon fa fa-ellipsis-h"></i>
                  <input type="text" id="fcd_cvc" name="fcd_cvc" class="form-control sf-form-control">
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>
                <?php esc_html_e('Select Month', 'service-finder'); ?>
                </label>
                <select id="fcd_month" name="fcd_month" class="form-control sf-form-control sf-select-box" title="Select Month">
                  <option value="1"><?php echo esc_html__('January', 'service-finder') ?></option>
                  <option value="2"><?php echo esc_html__('February', 'service-finder')?></option>
                  <option value="3"><?php echo esc_html__('March', 'service-finder')?></option>
                  <option value="4"><?php echo esc_html__('April', 'service-finder')?></option>
                  <option value="5"><?php echo esc_html__('May', 'service-finder')?></option>
                  <option value="6"><?php echo esc_html__('June', 'service-finder')?></option>
                  <option value="7"><?php echo esc_html__('July', 'service-finder')?></option>
                  <option value="8"><?php echo esc_html__('August', 'service-finder')?></option>
                  <option value="9"><?php echo esc_html__('September', 'service-finder')?></option>
                  <option value="10"><?php echo esc_html__('October', 'service-finder')?></option>
                  <option value="11"><?php echo esc_html__('November', 'service-finder')?></option>
                  <option value="12"><?php echo esc_html__('December', 'service-finder')?></option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>
                <?php esc_html_e('Select Year', 'service-finder'); ?>
                </label>
                <select id="fcd_year" name="fcd_year" class="form-control sf-form-control sf-select-box"  title="Select Year">
                  <?php
											$year = date('Y');
                                            for($i = $year;$i<=$year+50;$i++){
												echo '<option value="'.esc_attr($i).'">'.$i.'</option>';
											}
											?>
                </select>
              </div>
            </div>
          </div>
          <div id="twocheckout_featurecardinfo" class="default-hidden">
            <div class="col-md-8">
              <div class="form-group">
                <label>
                <?php esc_html_e('Card Number', 'service-finder'); ?>
                </label>
                <div class="input-group"> <i class="input-group-addon fa fa-credit-card"></i>
                  <input type="text" id="twocheckout_fcd_number" name="twocheckout_fcd_number" class="form-control sf-form-control">
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>
                <?php esc_html_e('CVC', 'service-finder'); ?>
                </label>
                <div class="input-group"> <i class="input-group-addon fa fa-ellipsis-h"></i>
                  <input type="text" id="twocheckout_fcd_cvc" name="twocheckout_fcd_cvc" class="form-control sf-form-control">
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>
                <?php esc_html_e('Select Month', 'service-finder'); ?>
                </label>
                <select id="twocheckout_fcd_month" name="twocheckout_fcd_month" class="form-control sf-form-control sf-select-box" title="Select Month">
                  <option value="1"><?php echo esc_html__('January', 'service-finder') ?></option>
                  <option value="2"><?php echo esc_html__('February', 'service-finder')?></option>
                  <option value="3"><?php echo esc_html__('March', 'service-finder')?></option>
                  <option value="4"><?php echo esc_html__('April', 'service-finder')?></option>
                  <option value="5"><?php echo esc_html__('May', 'service-finder')?></option>
                  <option value="6"><?php echo esc_html__('June', 'service-finder')?></option>
                  <option value="7"><?php echo esc_html__('July', 'service-finder')?></option>
                  <option value="8"><?php echo esc_html__('August', 'service-finder')?></option>
                  <option value="9"><?php echo esc_html__('September', 'service-finder')?></option>
                  <option value="10"><?php echo esc_html__('October', 'service-finder')?></option>
                  <option value="11"><?php echo esc_html__('November', 'service-finder')?></option>
                  <option value="12"><?php echo esc_html__('December', 'service-finder')?></option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>
                <?php esc_html_e('Select Year', 'service-finder'); ?>
                </label>
                <select id="twocheckout_fcd_year" name="twocheckout_fcd_year" class="form-control sf-form-control sf-select-box"  title="Select Year">
                  <?php
											$year = date('Y');
                                            for($i = $year;$i<=$year+50;$i++){
												echo '<option value="'.esc_attr($i).'">'.$i.'</option>';
											}
											?>
                </select>
              </div>
            </div>
          </div>
          <div id="payulatam_featurecardinfo" class="default-hidden">
            <div class="col-md-12">
              <div class="form-group">
                <label>
                <?php esc_html_e('Select Card', 'service-finder'); ?>
                </label>
                <select id="payulatam_f_cardtype" name="payulatam_f_cardtype" class="form-control sf-form-control sf-select-box"  title="<?php esc_html_e('Select Card', 'service-finder'); ?>">
                  <?php
				  $country = (isset($service_finder_options['payulatam-country'])) ? $service_finder_options['payulatam-country'] : '';
				  $cards = service_finder_get_cards($country);
				  foreach($cards as $card){
				  	echo '<option value="'.esc_attr($card).'">'.$card.'</option>';
				  }
                                            
											?>
                </select>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <label>
                <?php esc_html_e('Card Number', 'service-finder'); ?>
                </label>
                <div class="input-group"> <i class="input-group-addon fa fa-credit-card"></i>
                  <input type="text" id="payulatam_fcd_number" name="payulatam_fcd_number" class="form-control sf-form-control">
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>
                <?php esc_html_e('CVC', 'service-finder'); ?>
                </label>
                <div class="input-group"> <i class="input-group-addon fa fa-ellipsis-h"></i>
                  <input type="text" id="payulatam_fcd_cvc" name="payulatam_fcd_cvc" class="form-control sf-form-control">
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>
                <?php esc_html_e('Select Month', 'service-finder'); ?>
                </label>
                <select id="payulatam_fcd_month" name="payulatam_fcd_month" class="form-control sf-form-control sf-select-box" title="Select Month">
                  <option value="01"><?php echo esc_html__('January', 'service-finder') ?></option>
                  <option value="02"><?php echo esc_html__('February', 'service-finder')?></option>
                  <option value="03"><?php echo esc_html__('March', 'service-finder')?></option>
                  <option value="04"><?php echo esc_html__('April', 'service-finder')?></option>
                  <option value="05"><?php echo esc_html__('May', 'service-finder')?></option>
                  <option value="06"><?php echo esc_html__('June', 'service-finder')?></option>
                  <option value="07"><?php echo esc_html__('July', 'service-finder')?></option>
                  <option value="08"><?php echo esc_html__('August', 'service-finder')?></option>
                  <option value="09"><?php echo esc_html__('September', 'service-finder')?></option>
                  <option value="10"><?php echo esc_html__('October', 'service-finder')?></option>
                  <option value="11"><?php echo esc_html__('November', 'service-finder')?></option>
                  <option value="12"><?php echo esc_html__('December', 'service-finder')?></option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>
                <?php esc_html_e('Select Year', 'service-finder'); ?>
                </label>
                <select id="payulatam_fcd_year" name="payulatam_fcd_year" class="form-control sf-form-control sf-select-box"  title="Select Year">
                  <?php
											$year = date('Y');
                                            for($i = $year;$i<=$year+50;$i++){
												echo '<option value="'.esc_attr($i).'">'.$i.'</option>';
											}
											?>
                </select>
              </div>
            </div>
          </div>
          <div id="featuredwiredinfo" class="default-hidden">
                    <div class="col-md-12 margin-t-10 margin-b-10">
                        <?php
                        $description = (!empty($service_finder_options['wire-transfer-description'])) ? $service_finder_options['wire-transfer-description'] : '';
                        echo $description;
                        ?>
                    </div>
                  </div>
          <div class="col-md-12">
            <input type="hidden" name="feature_id" value="<?php echo esc_attr($row->id); ?>">
            <input type="submit" class="btn btn-primary btn-block" name="feature-payment" value="<?php esc_html_e('Pay Now', 'service-finder'); ?>" />
          </div>
          <?php
																}	
																}
														echo '</form>';															
														}elseif($row->status == 'Paid' || $row->status == 'Free'){
														
														$activationtimeInSec = strtotime($row->date);
														$differenceInSec = time() - $activationtimeInSec;
														$differenceInDays = floor($differenceInSec / 60 / 60 / 24);
														
														$limit = floatval($row->days);
														
														$remainingdays = $limit - $differenceInDays;
														
														$msg = (!empty($service_finder_options['featured-account'])) ? $service_finder_options['featured-account'] : esc_html__('Now you are a featured member. You have %REMAININGDAYS% days remaining to expire your feature account.', 'service-finder');
														
														$msg = str_replace('%REMAININGDAYS%',$remainingdays,$msg);
														
														echo '<div class="alert-bx alert-info">'.esc_html($msg).'</div>';
														
														}elseif($row->status == 'on-hold'){
														echo '<div class="alert-bx alert-info">'.esc_html__('You paid via wire transfer. Please wait for approval.','service-finder').'</div>';
														}else{
														echo '<div class="alert-bx alert-info">'.esc_html__('You have already made a request for feature. Please wait for approval.','service-finder').'</div>';
														}
														?>
        </div>
      <?php } ?>
      <?php } ?>
</div>
</div>
<?php } ?>
