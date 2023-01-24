<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/
$service_finder_options = get_option('service_finder_options');
$wpdb = service_finder_plugin_global_vars('wpdb');
$service_finder_Params = service_finder_plugin_global_vars('service_finder_Params');

if(service_finder_getUserRole($current_user->ID) == 'Provider'){
$userInfo = service_finder_getCurrentUserInfo();
}else{
$userInfo = service_finder_getUserInfo($globalproviderid);
}

$user_info = get_user_by('ID',$globalproviderid);
$user_email = $user_info->user_email;

$userCap = service_finder_get_capability($globalproviderid);
$url = str_replace('/','\/',$service_finder_Params['homeUrl']);
$adminajaxurl = str_replace('/','\/',admin_url('admin-ajax.php'));
$hiddenclass = '';
$settings = service_finder_getProviderSettings($globalproviderid);

$payment_methods = (!empty($service_finder_options['payment-methods'])) ? $service_finder_options['payment-methods'] : '';
$woopayment = (isset($service_finder_options['woocommerce-payment'])) ? esc_html($service_finder_options['woocommerce-payment']) : false;
$showamenities = (isset($service_finder_options['show-amenities'])) ? esc_html($service_finder_options['show-amenities']) : true;
$showlanguages = (isset($service_finder_options['show-languages'])) ? esc_html($service_finder_options['show-languages']) : true;

$google_calendar = (!empty($settings['google_calendar'])) ? $settings['google_calendar'] : '';
$paymentoption = (!empty($settings['paymentoption'])) ? $settings['paymentoption'] : '';
$booking_process = (!empty($settings['booking_process'])) ? $settings['booking_process'] : '';
$future_bookings_availability = (!empty($settings['future_bookings_availability'])) ? $settings['future_bookings_availability'] : '';
$buffertime = (!empty($settings['buffertime'])) ? $settings['buffertime'] : '';
$availability_based_on = (!empty($settings['availability_based_on'])) ? $settings['availability_based_on'] : '';
$slot_interval = (!empty($settings['slot_interval'])) ? $settings['slot_interval'] : '';
$offers_based_on = (!empty($settings['offers_based_on'])) ? $settings['offers_based_on'] : '';
$booking_date_based_on = (!empty($settings['booking_date_based_on'])) ? $settings['booking_date_based_on'] : '';
$booking_option = (!empty($settings['booking_option'])) ? $settings['booking_option'] : '';
$booking_assignment = (!empty($settings['booking_assignment'])) ? $settings['booking_assignment'] : '';
$members_available = (!empty($settings['members_available'])) ? $settings['members_available'] : '';
$booking_charge_on_service = 'yes';
$booking_basedon = (!empty($settings['booking_basedon'])) ? $settings['booking_basedon'] : '';
$mincost = (isset($settings['mincost'])) ? $settings['mincost'] : '';
$paypalusername = (!empty($settings['paypalusername'])) ? $settings['paypalusername'] : '';
$paypalpassword = (!empty($settings['paypalpassword'])) ? $settings['paypalpassword'] : '';
$paypalsignatue = (!empty($settings['paypalsignatue'])) ? $settings['paypalsignatue'] : '';
$stripesecretkey = (!empty($settings['stripesecretkey'])) ? $settings['stripesecretkey'] : '';
$stripepublickey = (!empty($settings['stripepublickey'])) ? $settings['stripepublickey'] : '';
$wired_description = (!empty($settings['wired_description'])) ? $settings['wired_description'] : '';
$wired_instructions = (!empty($settings['wired_instructions'])) ? $settings['wired_instructions'] : '';
$twocheckoutaccountid = (!empty($settings['twocheckoutaccountid'])) ? $settings['twocheckoutaccountid'] : '';
$twocheckoutpublishkey = (!empty($settings['twocheckoutpublishkey'])) ? $settings['twocheckoutpublishkey'] : '';
$twocheckoutprivatekey = (!empty($settings['twocheckoutprivatekey'])) ? $settings['twocheckoutprivatekey'] : '';
$payumoneymid = (!empty($settings['payumoneymid'])) ? $settings['payumoneymid'] : '';
$payumoneykey = (!empty($settings['payumoneykey'])) ? $settings['payumoneykey'] : '';
$payumoneysalt = (!empty($settings['payumoneysalt'])) ? $settings['payumoneysalt'] : '';
$payulatammerchantid = (!empty($settings['payulatammerchantid'])) ? $settings['payulatammerchantid'] : '';
$payulatamapilogin = (!empty($settings['payulatamapilogin'])) ? $settings['payulatamapilogin'] : '';
$payulatamapikey = (!empty($settings['payulatamapikey'])) ? $settings['payulatamapikey'] : '';
$payulatamaccountid = (!empty($settings['payulatamaccountid'])) ? $settings['payulatamaccountid'] : '';

$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? esc_html($service_finder_options['pay_booking_amount_to']) : '';

$bankaccount_info_section = (isset($service_finder_options['bank-account-info-section'])) ? esc_html($service_finder_options['bank-account-info-section']) : '';

$stripeconnecttype = (!empty($service_finder_options['stripe-connect-type'])) ? esc_html($service_finder_options['stripe-connect-type']) : '';

$adminavailabilitybasedon = (!empty($service_finder_options['availability-based-on'])) ? esc_html($service_finder_options['availability-based-on']) : '';
$adminoffersbasedon = (!empty($service_finder_options['offers-based-on'])) ? esc_html($service_finder_options['offers-based-on']) : '';
$datestyle = (!empty($service_finder_options['booking-date-style'])) ? esc_html($service_finder_options['booking-date-style']) : '';

$paid_booking = (!empty($service_finder_options['paid-booking'])) ? $service_finder_options['paid-booking'] : '';
if(!$paid_booking){
$free_booking = true;
}else{
$free_booking = (!empty($service_finder_options['free-booking'])) ? $service_finder_options['free-booking'] : '';
}



$minradiusrange = 0;
$maxradiusrange = (isset($service_finder_options['search-max-radius'])) ? esc_attr($service_finder_options['search-max-radius']) : '1000';

$radiussearchunit = (isset($service_finder_options['radius-search-unit'])) ? esc_attr($service_finder_options['radius-search-unit']) : 'mi';
if($radiussearchunit == 'km'){
$radiusunit = esc_html__( ' Km', 'service-finder' );
}else{
$radiusunit = esc_html__( ' Mi.', 'service-finder' );
}

$identitycheck = (isset($service_finder_options['identity-check'])) ? esc_attr($service_finder_options['identity-check']) : '';
$restrictmyaccount = (isset($service_finder_options['restrict-my-account'])) ? esc_attr($service_finder_options['restrict-my-account']) : '';

$signupautosuggestion = (isset($service_finder_options['signup-auto-suggestion']) && service_finder_show_autosuggestion_on_site()) ? $service_finder_options['signup-auto-suggestion'] : false;

$identityapproved = $userInfo['identity'];
$attachmentIDs = service_finder_get_identity($globalproviderid);
if(!empty($attachmentIDs)){
$identityupload = 'yes';
}else{
$identityupload = 'no';
}
//echo service_finder_check_identity_approved($globalproviderid);die;
wp_add_inline_script( 'bootstrap', 'jQuery(document).ready(function($) {
var identitycheckfeature = "'.$identitycheck.'";
var restrictmyaccount = "'.$restrictmyaccount.'";
var identityapproved = "'.$identityapproved.'";
var identityupload = "'.$identityupload.'";
var identityapproved = "'.service_finder_check_identity_approved($globalproviderid).'";

if(identitycheckfeature > 0 && restrictmyaccount > 0 && identityapproved){
       jQuery("#identityCheck").modal({

            backdrop: "static",

            keyboard: false

        });
}
})', 'after' );

wp_add_inline_script( 'map', '/*Declare global variable*/
var signupautosuggestion = "'.$signupautosuggestion.'";
', 'after' );

$addressalert = ($userInfo['address'] != "") ? true : false;

wp_add_inline_script( 'service_finder-js-form-submit', '/*Declare global variable*/
var signupautosuggestion = "'.$signupautosuggestion.'";
var userrole = "'.service_finder_getUserRole($current_user->ID).'";
var addressalert = "'.$addressalert.'";
', 'after' );

if(in_array('crop',$userCap))
{
/*For image croping*/
wp_enqueue_style('cropper');
wp_enqueue_style('normalize');
wp_enqueue_style('cropper-custom');
wp_enqueue_script('cropper-custom');
wp_enqueue_script('jquery-cropper');
wp_enqueue_script('service-finder-crop');
}
?>
<!--Provider Profile Settings Template-->
<form class="pro-setting user-update" method="post">
<input type="hidden" name="action" value="update_user">
<input name="providerlat" type="hidden" value="">
<input name="providerlng" type="hidden" value="">
<input name="videosarr" type="hidden" value="">
<input name="videocount" type="hidden" value="">
  <div class="sf-submit-btns clearfix">
  <div class="text-right sf-check-my-profile">
  <a href="<?php echo esc_js(service_finder_get_author_url($globalproviderid)); ?>" class="btn btn-primary"><i class="fa fa-user"></i> <?php esc_html_e('My Profile', 'service-finder'); ?></a>
  <?php if ( class_exists( 'WP_Job_Manager_Alerts' ) ) { ?>
  <?php
  $jobnotification = (!empty($service_finder_options['job-notification'])) ? $service_finder_options['job-notification'] : '';
  if(!empty($userCap)){
  if(in_array('job-alerts',$userCap) && $jobnotification == 'job-alert'){	
  ?>
  <a href="<?php echo esc_url(service_finder_get_url_by_shortcode('[job_alerts')); ?>" class="btn btn-primary"><i class="fa fa-bell"></i>  <?php esc_html_e('Job Alerts', 'service-finder'); ?></a>
  <?php
  }
  }
  ?>
  <?php } ?>
  <?php if(service_finder_getUserRole($current_user->ID) == 'administrator'){ 
  if(get_user_meta($globalproviderid,'claimbusiness',true) == 'enable'){
  $claimstatus = 'disable';
  $claimstring = esc_html__('Disable Claim Business', 'service-finder');
  }else{
  $claimstatus = 'enable';
  $claimstring = esc_html__('Enable Claim Business', 'service-finder');
  }
  ?>
  <?php if(get_user_meta($globalproviderid,'claimed',true) == 'yes'){ ?>
  <a href="javascript:;" class="btn btn-primary"><i class="fa fa-briefcase"></i> <?php echo esc_html__('Claimed', 'service-finder'); ?></a>
  <?php }else{ ?>
  <a href="javascript:;" class="btn btn-primary claimbusinessaction" data-providerid="<?php echo esc_attr($globalproviderid); ?>" data-status="<?php echo esc_attr($claimstatus); ?>"><i class="fa fa-briefcase"></i> <?php echo esc_html($claimstring); ?></a>
  <?php } ?>
  <?php } ?>
  </div>
  <div id="submit-fixed" class="sf-submit-my-profile">
    <button type="submit" class="btn btn-primary margin-r-10" name="update-profile"><i class="fa fa-save"></i> <?php esc_html_e('Submit information', 'service-finder'); ?></button>
  </div>
  </div>
  <?php if(service_finder_check_provider_membership_status($globalproviderid) == 'draft'){ ?>
  <div class="alert alert-info sf-membership-reactivate"><?php 
  $membershipmsg = esc_html__('Membership of this provider has been cancelled. %LINKSTART%Click here%LINKEND% to reavtivate this profile.', 'service-finder'); 
  $reactivatelinkstart = '<a href="javascript:;" class="btn-linking membership-reactivate" data-providerid="'.esc_attr($globalproviderid).'">';
  $reactivatelinkend = '</a>';
  $tokens = array('%LINKSTART%','%LINKEND%');
  $replacements = array($reactivatelinkstart,$reactivatelinkend);
  $displaymsg = str_replace($tokens,$replacements,$membershipmsg);
  print($displaymsg);
  ?> </div>
  <?php } ?>
  <div class="sf-page-title">
<h2><?php echo (!empty($service_finder_options['label-profile-settings'])) ? esc_html($service_finder_options['label-profile-settings']) : esc_html__('Profile Settings', 'service-finder'); ?></h2>
</div>
  <div class="panel panel-default">
    <div class="panel-heading sf-panel-heading">
        <h3 class="panel-tittle m-a0"><span class="fa fa-user"></span> <?php esc_html_e('User Avatar', 'service-finder'); ?> </h3>
    </div>
    <div class="panel-body sf-panel-body padding-30">
        <div class="auther-pic-text form-inr clearfix">
        
      <?php
      if(in_array('crop',$userCap)){	
	  echo do_shortcode('[service_finder_profile_avatar user_id="'.$globalproviderid.'"]');
	  }else
	  {
	  ?>
      <div class="profile-pic-bx">
    <div class="rwmb-field rwmb-plupload_image-wrapper">
      <div class="rwmb-input">
        <ul class="rwmb-images rwmb-uploaded" data-field_id="plavatarupload" data-delete_nonce="" data-reorder_nonce="" data-force_delete="0" data-max_file_uploads="1">
          <?php
            if(!empty($userInfo['avatar_id']) && $userInfo['avatar_id'] > 0){
                $src  = wp_get_attachment_image_src( $userInfo['avatar_id'], 'thumbnail' );
                $src  = $src[0];
                $i18n_delete = apply_filters( 'rwmb_image_delete_string', _x( 'Delete', 'image upload', 'service-finder' ) );
                $hiddenclass = 'hidden';
                
                $html = sprintf('<li id="item_%s">
                <img src="%s" />
                <div class="rwmb-image-bar">
                    <a title="%s" class="rwmb-delete-file" href="javascript:;" data-attachment_id="%s">&times;</a>
                    <input type="hidden" name="plavatar" value="%s">
                </div>
            </li>',
            esc_attr($userInfo['avatar_id']),
            esc_url($src),
            esc_attr($i18n_delete), esc_attr($userInfo['avatar_id']),
            esc_attr($userInfo['avatar_id'])
            );
                echo $html;	
            }
            ?>
        </ul>
        <div id="plavatarupload-dragdrop" class="RWMB-drag-drop drag-drop hide-if-no-js new-files <?php echo esc_attr($hiddenclass); ?>" data-upload_nonce="1f7575f6fa" data-js_options="{&quot;runtimes&quot;:&quot;html5,silverlight,flash,html4&quot;,&quot;file_data_name&quot;:&quot;async-upload&quot;,&quot;browse_button&quot;:&quot;plavatarupload-browse-button&quot;,&quot;drop_element&quot;:&quot;plavatarupload-dragdrop&quot;,&quot;multiple_queues&quot;:true,&quot;max_file_size&quot;:&quot;8388608b&quot;,&quot;url&quot;:&quot;<?php echo esc_url($adminajaxurl); ?>&quot;,&quot;flash_swf_url&quot;:&quot;<?php echo esc_url($url); ?>wp-includes\/js\/plupload\/plupload.flash.swf&quot;,&quot;silverlight_xap_url&quot;:&quot;<?php echo esc_url($url); ?>wp-includes\/js\/plupload\/plupload.silverlight.xap&quot;,&quot;multipart&quot;:true,&quot;urlstream_upload&quot;:true,&quot;filters&quot;:[{&quot;title&quot;:&quot;Allowed Image Files&quot;,&quot;extensions&quot;:&quot;jpg,jpeg,gif,png&quot;}],&quot;multipart_params&quot;:{&quot;field_id&quot;:&quot;plavatarupload&quot;,&quot;action&quot;:&quot;avatar_upload&quot;}}">
          <div class = "drag-drop-inside text-center"> <img src="<?php echo esc_url($service_finder_Params['pluginImgUrl'].'/no_img.jpg'); ?>">
            <p class="drag-drop-info">
              <?php esc_html_e('Drop avatar here', 'service-finder'); ?>
            </p>
            <p><?php esc_html_e('or', 'service-finder'); ?></p>
            <p class="drag-drop-buttons">
              <input id="plavatarupload-browse-button" type="button" value="<?php esc_html_e('Select Image', 'service-finder'); ?>" class="button btn btn-primary" />
            </p>
          </div>
        </div>
      </div>
      </div>
  </div>
	  <div class="profile-text-bx">
    <p><em>
      <?php esc_html_e('Update your avatar manually', 'service-finder'); ?>
      </em></p>
    <ul class="auther-limit list-unstyled">
      <li><strong>
        <?php esc_html_e('Dimensions', 'service-finder'); ?>
        :</strong> 600 x 600 px</li>
      <li><strong>
        <?php esc_html_e('Extensions', 'service-finder'); ?>
        :</strong> JPEG,PNG,GIF</li>
    </ul>
  </div>
      <?php
      }
	  ?>
        </div>
    </div>

</div>	    
  <!--About Me Section-->
  <div class="panel panel-default about-me-here">
    <div class="panel-heading sf-panel-heading">
      <h3 class="panel-tittle m-a0"><span class="fa fa-user"></span> <?php esc_html_e('About me', 'service-finder'); ?> </h3>
    </div>
    <div class="panel-body sf-panel-body padding-30">
      <div class="row">
        <div class="col-lg-12">
          <div class="form-group">
            <label>
            <?php esc_html_e('Username', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-user"></i>
              <input type="text" class="form-control sf-form-control" readonly="readonly" name="username" value="<?php echo esc_attr($userInfo['username']) ?>">
            </div>
          </div>
        </div>
        <div class="col-lg-12">
          <div class="form-group">
            <label>
            <?php esc_html_e('Company Name', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-user"></i>
              <input type="text" class="form-control sf-form-control" name="company_name" value="<?php echo esc_attr($userInfo['company_name']) ?>">
            </div>
          </div>
        </div>
        <div class="col-lg-12">
          <div class="form-group">
            <label>
            <?php esc_html_e('Biography', 'service-finder'); ?>
            </label>
              <?php 
              $settings = array( 
                                'editor_height' => '100px',
                                'textarea_name' => 'bio',
                            );

              wp_editor( wp_unslash($userInfo['bio']), 'bio', $settings );
              ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--Contact Details Section-->
  <div class="panel panel-default contact-details">
    <div class="panel-heading sf-panel-heading">
      <h3 class="panel-tittle m-a0"><span class="fa fa-envelope"></span> <?php esc_html_e('Contact Detail', 'service-finder'); ?> </h3>
    </div>
    <div class="panel-body sf-panel-body padding-30">
      <div class="row">
      	<?php if(get_user_meta($globalproviderid,'sms_phone_with_countrycode',true) != ''){ ?>
        <div class="col-lg-12">
          <div class="form-group">
            <label>
            <?php esc_html_e('Registred Mobile Number for Login via OTP', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-user"></i>
              <input type="text" class="form-control sf-form-control" readonly="readonly" name="registerednumber" value="<?php echo get_user_meta($globalproviderid,'sms_phone_with_countrycode',true); ?>">
            </div>
          </div>
        </div>
        <?php } ?>
        <div class="col-lg-6">
          <div class="form-group">
            <label>
            <?php esc_html_e('Mobile', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-phone"></i>
              <input type="text" class="form-control sf-form-control" name="mobile" value="<?php echo esc_attr($userInfo['mobile']) ?>">
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="form-group">
            <label>
            <?php esc_html_e('Email Address', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-envelope"></i>
              <input type="text" class="form-control sf-form-control" name="user_email" value="<?php echo esc_attr($user_email) ?>">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!--Address Section-->
  <div class="panel panel-default address-here">
    <div class="panel-heading sf-panel-heading">
      <h3 class="panel-tittle m-a0"><span class="fa fa-address-card"></span> <?php esc_html_e('Address', 'service-finder'); ?> </h3>
    </div>
    <div class="panel-body sf-panel-body padding-30">
      <div class="row">
      	<?php if(service_finder_get_data($service_finder_options,'show-contact-map') && service_finder_show_map_on_site()){ ?>
        <div class=" col-md-12 rwmb-field rwmb-map-wrapper checkbox-condition show">
        
        </div>
        <?php } ?>
        <div class="col-lg-6">
          <div class="form-group">
            <label>
            <?php esc_html_e('Address', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-globe"></i>
              <input type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Please enter only address', 'service-finder'); ?>" name="address" id="address" value="<?php echo esc_attr(stripcslashes($userInfo['address'])) ?>">
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="form-group">
            <label>
            <?php esc_html_e('Country', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-map-marker"></i>
              <select class="sf-select-box form-control sf-form-control" name="country" id="<?php echo ($signupautosuggestion) ? 'country' : 'customcountry'; ?>" data-live-search="true" title="<?php esc_html_e('Country', 'service-finder'); ?>">
                <option value="">
                <?php esc_html_e('Select Country', 'service-finder'); ?>
                </option>
                <?php
                $selectedcountry = (!empty($userInfo['country'])) ? esc_html($userInfo['country']) : '';
                $allcountry = (!empty($service_finder_options['all-countries'])) ? $service_finder_options['all-countries'] : '';
                $countries = service_finder_get_countries();
                if($allcountry){
                  if(!empty($countries)){
                    foreach($countries as $key => $country){
                        if($selectedcountry == $country){
                        $select = 'selected="selected"';
                        }else{
                        $select = '';
                        }
                        echo '<option '.$select.' value="'.esc_attr($country).'" data-code="'.esc_attr($key).'">'. esc_html__( $country, 'service-finder' ) .'</option>';
                    }
                  }
                }else{
                 $countryarr = (!empty($service_finder_options['allowed-country'])) ? $service_finder_options['allowed-country'] : '';
                 if($countryarr){
                    foreach($countryarr as $key){
                        if($selectedcountry == $countries[$key]){
                        $select = 'selected="selected"';
                        }else{
                        $select = '';
                        }
                        echo '<option '.$select.' value="'.esc_attr($countries[$key]).'" data-code="'.esc_attr($key).'">'. esc_html__( $countries[$key], 'service-finder' ) .'</option>';
                    }
                 }
                }
                ?>
            </select>
            </div>
          </div>
        </div>
        <div class="col-lg-12">
          <div class="form-group city-outer-bx">
            <label>
            <?php esc_html_e('City', 'service-finder'); ?>
            </label>
            <div class="input-group" id="cityautosuggestion"> <i class="input-group-addon fixed-w fa fa-map-marker"></i>
              <?php if($signupautosuggestion){ ?>
              <input type="text" class="form-control sf-form-control" name="city" id="city" value="<?php echo esc_attr($userInfo['city']) ?>">
              <?php }else{ ?>
              <select class="sf-select-box form-control sf-form-control" name="city" data-live-search="true" title="<?php esc_html_e('Select City', 'service-finder'); ?>" id="city">
              <?php
              $selectedcountry = (!empty($userInfo['country'])) ? esc_html($userInfo['country']) : '';
              $cities = service_finder_get_cities($selectedcountry);
			  
              if(!empty($cities)){
                foreach($cities as $term){
                    if($userInfo['city'] == $term->slug){
                    $select = 'selected="selected"';
                    }else{
                    $select = '';
                    }
                    echo '<option '.$select.' value="'.esc_attr($term->slug).'">'.$term->name.'</option>';
                }
              }
              ?>
              </select>
              <?php } ?>
            </div>
          </div>
        </div>
        <?php if(service_finder_get_data($service_finder_options,'show-contact-map') && service_finder_show_map_on_site()){ ?>
        <?php } ?>
      </div>
    </div>
  </div>
  <!--Service to Perform Section-->
  <?php if($service_finder_options['show-address-info']){ ?>
  <!-- <div class="panel panel-default service-perform-here">
    <div class="panel-heading sf-panel-heading">
      <h3 class="panel-tittle m-a0"><span class="fa fa-building"></span> <?php esc_html_e('Service to Perform At', 'service-finder'); ?> </h3>
    </div>
    <div class="panel-body sf-panel-body padding-30">
      <div class="row">
        <div class="col-lg-12">
          <div class="form-group form-inline">
            <div class="radio sf-radio-checkbox">
              <input id="provider_location" type="radio" name="service_perform" value="provider_location" <?php echo ($userInfo['service_perform'] == 'provider_location') ? 'checked' : ''; ?>>
              <label for="provider_location">
              <?php esc_html_e('My Location', 'service-finder'); ?>
              </label>
            </div>
            <div class="radio sf-radio-checkbox">
              <input id="customer_location" type="radio" name="service_perform" value="customer_location" <?php echo ($userInfo['service_perform'] == 'customer_location' || $userInfo['service_perform'] == '') ? 'checked' : ''; ?>>
              <label for="customer_location">
              <?php esc_html_e('Customer Location', 'service-finder'); ?>
              </label>
            </div>
            <div class="radio sf-radio-checkbox">
              <input id="both_location" type="radio" name="service_perform" value="both" <?php echo ($userInfo['service_perform'] == 'both') ? 'checked' : ''; ?>>
              <label for="both_location">
              <?php esc_html_e('Both', 'service-finder'); ?>
              </label>
            </div>
          </div>
        </div>
        <div class="col-lg-12" id="providerlocation_bx" <?php echo ($userInfo['service_perform'] == 'customer_location' || $userInfo['service_perform'] == '') ? 'style="display:none"' : ''; ?>>
          <div class="form-group">
            <label>
            <?php esc_html_e('My Location', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-street-view"></i>
              <input type="text" class="form-control sf-form-control" name="my_location" id="my_location" value="<?php echo esc_attr($userInfo['my_location']) ?>">
            </div>
          </div>
          <button id="showmylocation" class="btn btn-primary" data-providerid="<?php echo esc_attr($globalproviderid); ?>" type="button"><i class="fa fa-plus"></i>
            <?php esc_html_e('Set Marker Position', 'service-finder'); ?>
            </button>
        </div>
      </div>
    </div>
  </div> -->
  <?php } ?>
  <!--Social Media Section-->
  <?php if($service_finder_options['social-media']){ ?>
  <!-- <div class="panel panel-default social-media-here">
    <div class="panel-heading sf-panel-heading">
      <h3 class="panel-tittle m-a0"><span class="fa fa-share-alt"></span> <?php esc_html_e('Social Media', 'service-finder'); ?> </h3>
    </div>
    <div class="panel-body sf-panel-body padding-30">
      <div class="row">
        <div class="col-lg-6">
          <div class="form-group">
            <label>
            <?php esc_html_e('Facebook', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-facebook sf-color-social"></i>
              <input type="text" class="form-control sf-form-control" name="facebook" value="<?php echo esc_attr($userInfo['facebook']) ?>">
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="form-group">
            <label>
            <?php esc_html_e('Twitter', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-twitter sf-color-social"></i>
              <input type="text" class="form-control sf-form-control" name="twitter" value="<?php echo esc_attr($userInfo['twitter']) ?>">
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="form-group">
            <label>
            <?php esc_html_e('Linkedin', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-linkedin sf-color-social"></i>
              <input type="text" class="form-control sf-form-control" name="linkedin" value="<?php echo esc_attr($userInfo['linkedin']) ?>">
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="form-group">
            <label>
            <?php esc_html_e('Pinterest', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-pinterest sf-color-social"></i>
              <input type="text" class="form-control sf-form-control" name="pinterest" value="<?php echo esc_attr($userInfo['pinterest']) ?>">
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="form-group">
            <label>
            <?php esc_html_e('Digg', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-digg sf-color-social"></i>
              <input type="text" class="form-control sf-form-control" name="digg" value="<?php echo esc_attr($userInfo['digg']) ?>">
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="form-group">
            <label>
            <?php esc_html_e('Instagram', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-instagram sf-color-social"></i>
              <input type="text" class="form-control sf-form-control" name="instagram" value="<?php echo esc_attr($userInfo['instagram']) ?>">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div> -->
  <?php } ?>
  <!--Password Update Section-->
  <div class="panel panel-default password-update">
    <div class="panel-heading sf-panel-heading">
      <h3 class="panel-tittle m-a0"><span class="fa fa-lock"></span> <?php esc_html_e('Password Update', 'service-finder'); ?> </h3>
    </div>
    <div class="panel-body sf-panel-body padding-30">
      <div class="row">
        <div class="col-lg-6">
          <div class="form-group">
            <label>
            <?php esc_html_e('New Password', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-lock"></i>
              <input type="password" class="form-control sf-form-control" name="password" id="password" value="" autocomplete="off">
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="form-group">
            <label>
            <?php esc_html_e('Repeat Password', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-lock"></i>
              <input type="password" class="form-control sf-form-control" name="confirm_password" value="" id="confirm_password" autocomplete="off">
            </div>
          </div>
        </div>
        <div class="col-md-12">
          <p  class="margin-0">
            <?php esc_html_e('Enter same password in both fields. Use an uppercase letter and a number for stronger password.', 'service-finder'); ?>
          </p>
        </div>
      </div>
    </div>
  </div>
  
  <?php 
  if(!empty($userCap)){
  if(in_array('google-calendar',$userCap) && service_finder_getUserRole($current_user->ID) == 'Provider'){	
  session_start();
    require_once SERVICE_FINDER_BOOKING_LIB_DIR.'/google-api-php-client/src/Google/autoload.php';
	
	$gcal_creds = service_finder_get_gcal_cred();
	$client_id = $gcal_creds['client_id'];
    $client_secret = $gcal_creds['client_secret'];
	
    $redirect_uri = add_query_arg( array('action' => 'googleoauth-callback'), home_url() );
    $_SESSION['providerid'] = $globalproviderid;
    $client = new Google_Client();
    $client->setClientId($client_id);
    $client->setClientSecret($client_secret);
    $client->setRedirectUri($redirect_uri);
    $client->setAccessType("offline");
	$client->setApprovalPrompt('force');
    $client->setScopes('https://www.googleapis.com/auth/calendar');	
    
  ?>
  <!--Google Calendar Section-->
  <div class="panel panel-default">
    <div class="panel-heading sf-panel-heading">
      <h3 class="panel-tittle m-a0"><span class="fa fa-calendar"></span> <?php esc_html_e('Google Calendar Settings', 'service-finder'); ?> </h3>
    </div>
    <div class="panel-body sf-panel-body padding-30">
      <div class="row">
        <div class="col-lg-6">
          <div class="form-group form-inline">
            <label>
            <?php esc_html_e('Google Calendar', 'service-finder'); ?>
            </label>
            <br>
            <div class="radio sf-radio-checkbox">
              <input id="google_calendar_on" type="radio" name="google_calendar" value="on" <?php echo ($google_calendar == 'on') ? 'checked' : ''; ?>>
              <label for="google_calendar_on">
              <?php esc_html_e('On', 'service-finder'); ?>
              </label>
            </div>
            <div class="radio sf-radio-checkbox">
              <input id="google_calendar_off" type="radio" name="google_calendar" value="off" <?php echo ($google_calendar == 'off' || $google_calendar == '') ? 'checked' : ''; ?>>
              <label for="google_calendar_off">
              <?php esc_html_e('Off', 'service-finder'); ?>
              </label>
            </div>
          </div>
        </div>
        
        <div id="google_calendar_options" <?php echo ($google_calendar != 'on') ? 'style="display: none;"' : ''; ?>>
        <?php
        $flag = 0;
        if(isset($_SESSION['access_token']) && $_SESSION['access_token'] && service_finder_get_gcal_access_token($globalproviderid) != "") {
          $client->setAccessToken($_SESSION['access_token']);
          $flag = 1;
        
        } elseif(service_finder_get_gcal_access_token($globalproviderid) != ""){
          $client->setAccessToken(service_finder_get_gcal_access_token($globalproviderid));
          $flag = 1;
        }
         $newaccesstoken = json_decode(service_finder_get_gcal_access_token($globalproviderid));
		 //echo '<pre>';print_r($newaccesstoken);echo '</pre>';
        if($client->isAccessTokenExpired()) {
             try{
             
             if(isset($_SESSION['access_token']) && $_SESSION['access_token']) {
              $newaccesstoken = json_decode($_SESSION['access_token']);
              $client->refreshToken($newaccesstoken->refresh_token);
            
             }elseif(service_finder_get_gcal_access_token($globalproviderid) != ""){
              $newaccesstoken = json_decode(service_finder_get_gcal_access_token($globalproviderid));
              $client->refreshToken($newaccesstoken->refresh_token);
             }
             
             } catch (Exception $e) {
                
             }
    
         }
        ?>
        <?php if($flag == 1){ ?>
        <div class="col-lg-12" style="display:block;" id="disconnectbtn-outer">
          <div class="form-group">
          <input type="hidden" class="form-control sf-form-control" name="google_client_id" value="<?php echo esc_attr($client_id) ?>">
          <input type="hidden" class="form-control sf-form-control" name="google_client_secret" value="<?php echo esc_attr($client_secret) ?>">
          <?php
            echo '<a href="javascript:;" class="btn btn-primary margin-r-10 updategcal" data-providerid="'.$globalproviderid.'">'.esc_html__('Disconnect Google Calendar', 'service-finder').'</a>';
            
          ?>
          
        </div>
        </div>
        <div class="col-lg-12" id="gcallist">
          <div class="form-group">
            <label>
            <?php esc_html_e('Calendar ID', 'service-finder'); ?>
            </label>
            <div class="input-group"> 
              <select name="google_calendar_id" class="form-control sf-form-control sf-select-box" id="google_calendar_id" title="<?php esc_html_e('Select Calendar ID', 'service-finder'); ?>">
                <?php
                try{
                $service = new Google_Service_Calendar($client);
                $calendarList = $service->calendarList->listCalendarList();
                if(!empty($calendarList)){
                while(true) {
                  foreach ($calendarList->getItems() as $calendarListEntry) {
                    if(get_user_meta($globalproviderid,'google_calendar_id',true) == $calendarListEntry->id){
                        $select = 'selected="selected"';
                    }else{
                        $select = '';
                    }
                    echo '<option '.$select.' value="'.$calendarListEntry->id.'">'.$calendarListEntry->getSummary().'</option>';
					//$gcalid = $calendarListEntry->id;
                  }
                  $pageToken = $calendarList->getNextPageToken();
                  if ($pageToken) {
                    $optParams = array('pageToken' => $pageToken);
                    $calendarList = $service->calendarList->listCalendarList($optParams);
                  } else {
                    break;
                  }
                } 
                }
                } catch (Exception $e) {
                print_r($e);
                }
                ?>
              </select>
            </div>
          </div>
        </div>
        <?php } ?>
         <div class="col-lg-12">
          <div class="form-group">
          <?php
            if(isset($_SESSION['access_token']) && $_SESSION['access_token'] && service_finder_get_gcal_access_token($globalproviderid) != "") {
              $client->setAccessToken($_SESSION['access_token']);
              //echo '<div id="connectbtn"><a href="javascript:;" class="btn btn-primary margin-r-10">'.esc_html__('Already Connected to Google Calendar', 'service-finder').'</a></div>';
            
            } elseif(service_finder_get_gcal_access_token($globalproviderid) != ""){
              $client->setAccessToken(service_finder_get_gcal_access_token($globalproviderid));
              //echo '<div id="connectbtn"><a href="javascript:;" class="btn btn-primary margin-r-10">'.esc_html__('Already Connected to Google Calendar', 'service-finder').'</a></div>';
            
            }else {
              $authUrl = $client->createAuthUrl();
              echo '<div id="connectbtn"><a href="'.esc_url($authUrl).'" class="btn btn-primary margin-r-10"><span class="google-icon-wrapper">
					  	<img class="google-icon" src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/gcal.svg">
					  </span>'.esc_html__('Connect to Google Calendar', 'service-finder').'</a></div>';
            }
          ?>
          
        </div>
        </div>
        </div>
      </div>
    </div> 		
  </div>
  <?php } 
  }
  ?>
  
  <?php
  if(!empty($payment_methods)){
  if($payment_methods['stripe'] && service_finder_getUserRole($current_user->ID) == 'Provider' && $stripeconnecttype == 'standard' && !$woopayment){
  ?>
  <div class="panel panel-default password-update">
    <div class="panel-heading sf-panel-heading">
      <h3 class="panel-tittle m-a0"><span class="fa fa-cc-stripe"></span> <?php esc_html_e('Stripe Connect', 'service-finder'); ?> </h3>
    </div>
    <div class="panel-body sf-panel-body padding-30">
      <div class="row">
        <?php 
        $provider_connected = get_user_meta($current_user->ID, 'provider_connected', true);
        if (isset($provider_connected) && $provider_connected == 1) {
        $stripe_connect_id = get_user_meta($current_user->ID, 'stripe_connect_id', true);
        $admin_client_id = get_user_meta($current_user->ID, 'admin_client_id', true);
        ?>
        <div class="col-lg-12">
          <div class="form-group">
              <?php
              $disconnecturl = add_query_arg( array('disconnect_stripe' => 'true','stripe_connect_id' => $stripe_connect_id,'client_id' => $admin_client_id), home_url() );
              ?>
              <label><?php esc_html_e('You are connected with Stripe', 'service-finder'); ?></label><br />
              <a class="btn btn-primary" href="<?php echo esc_url($disconnecturl);?>"><?php esc_html_e('Disconnect Stripe Account', 'service-finder'); ?></a>
          </div>
        </div>
        <?php }else{ ?>
        <div class="col-lg-12">
          <div class="form-group">
              <?php
              $authorizeduri = 'https://connect.stripe.com/oauth/authorize';
              $clientid = (!empty($service_finder_options['stripe-connect-client-id'])) ? $service_finder_options['stripe-connect-client-id'] : '';
              $connecturl = add_query_arg( array('response_type' => 'code','scope' => 'read_write','client_id' => $clientid), $authorizeduri );
              ?>
              <a class="btn btn-primary" href="<?php echo esc_url($connecturl);?>"><?php esc_html_e('Connect with Stripe', 'service-finder'); ?></a>
          </div>
        </div>
        <?php } ?>
      </div>
    </div>
  </div>
  <?php 
  }
  }
  ?>
  <!--Category Select Section-->
  <div class="panel panel-default category-drop">
    <div class="panel-heading sf-panel-heading">
      <h3 class="panel-tittle m-a0"><span class="fa fa-list-alt"></span> <?php esc_html_e('Category', 'service-finder'); ?> </h3>
    </div>
    <div class="panel-body sf-panel-body padding-30">
      <div class="row">
        <div class="col-lg-12">
          <div class="form-group">
            <label>
            <?php esc_html_e('Category', 'service-finder'); ?>
            </label>
            <div class="input-group">
            <?php 
            $multiple = '';
            $multipleclass = '';
            if(!empty($userCap)):
                if(in_array('multiple-categories',$userCap)):
                    $multiple = 'multiple="multiple"';
                    $multipleclass = 'sf-multiple-categories-select';
                endif;
            endif;
            if(!empty($userInfo['category'])){
            $allcategories = explode(',',$userInfo['category']);
            }else{
            $allcategories[] = $userInfo['category'];
            }

            $primary_category = get_user_meta($globalproviderid,'primary_category',true);
            
            if(!empty($userCap)){
                if(in_array('multiple-categories',$userCap)){
                    $package = get_user_meta($globalproviderid,'provider_role',true);
                    $packageNum = intval(substr($package, 8));
                    $maxcategory = (!empty($service_finder_options['package'.$packageNum.'-multiple-categories'])) ? $service_finder_options['package'.$packageNum.'-multiple-categories'] : '';
					$displaycatmsg = sprintf(esc_html__('Currently you can choose %d categories. You can increase it by upgrade membership plan','service-finder'),$maxcategory);
					echo '<div class="alert alert-info">'.$displaycatmsg.'</div>';
                }else{
                $maxcategory = '';
				$displaycatmsg = esc_html__('Currently you can choose only 1 category. You can increase it by upgrade membership plan','service-finder');
				echo '<div class="alert alert-info">'.$displaycatmsg.'</div>';
                }
            }else{
                $maxcategory = '';
				$displaycatmsg = esc_html__('Currently you can choose only 1 category. You can increase it by upgrade membership plan','service-finder');
				echo '<div class="alert alert-info">'.$displaycatmsg.'</div>';
            }
            ?>
              <select name="category[]" class="<?php echo sanitize_html_class($multipleclass) ?> sf-select-box form-control sf-form-control" id="category" <?php echo esc_attr($multiple);?> data-primaryid="<?php echo esc_attr($primary_category); ?>" data-max-options="<?php echo esc_attr($maxcategory);?>">
                <?php
                if(class_exists('service_finder_texonomy_plugin')){
                $limit = 1000;
                $categories = service_finder_getCategoryList($limit);
                $texonomy = 'providers-category';
                if(!empty($categories)){
                    foreach($categories as $category){
                        if(in_array($category->term_id,$allcategories)){
                        $select = 'selected="selected"';
                        }else{
                        $select = '';
                        }
                        echo '<option '.$select.' value="'.esc_attr($category->term_id).'">'. $category->name.'</option>';
                        $term_children = get_term_children($category->term_id,$texonomy);
                        $namearray = array();
                        if(!empty($term_children)){
                            
                            foreach ($term_children as $child) {
                                $term = get_term_by( 'id', $child, $texonomy );
                    
                                $namearray[$term->name]= $child;
                
                            }
                            
                            if(!empty($namearray)){
                            ksort($namearray);
                            
                            foreach($namearray as $key => $value) {
                            
                                $term_child_id = $value;

                                $term_child = get_term_by('id',$term_child_id,$texonomy);
                                
                                if(in_array($term_child_id,$allcategories)){
                                    $childselect = 'selected="selected"';
                                }else{
                                    $childselect = '';
                                }
                                
                                echo '<option '.$childselect.' value="'.esc_attr($term_child_id).'" data-content="<span class=\'childcat\'>'.esc_attr($term_child->name).'</span>">'. $term_child->name.'</option>';
                                
                            }
                            }
                        }
                    }
                }	
                }
                ?>
              </select>
            </div>
          </div>
        </div>
        <?php
        if(!empty($userCap)){
            if(in_array('multiple-categories',$userCap)){
        ?>
        <div class="col-lg-12">
                  <div class="form-group form-inline">
                    <label>
                    <?php esc_html_e('Primary Category', 'service-finder'); ?>
                    </label>
                    <br>
                    <div id="providers-category-bx">
                    <?php
                    if(!empty($allcategories)){
                        foreach($allcategories as $category){
                        $catname = service_finder_getCategoryName($category);
                        ?>
                        <div class="radio sf-radio-checkbox">
                          <input id="cat-<?php echo esc_attr($category); ?>" type="radio" name="primary_category" <?php echo ($primary_category == $category) ? 'checked' : ''; ?> value="<?php echo esc_attr($category); ?>">
                          <label for="cat-<?php echo esc_attr($category); ?>">
                          <?php echo esc_html($catname);  ?>
                          </label>
                        </div>
                        <?php
                        }
                    }
                    ?>
                    </div>
                  </div>
                </div>
        <?php 
            }
        }
        ?>        
      </div>
    </div>
  </div>
  <!--Amenities Select Section-->
  <?php if(!empty($showamenities)){ ?>

  <?php } ?>
  <!--Languages Select Section-->
  <?php if(!empty($showlanguages)){ ?>
  <!-- <div class="panel panel-default category-drop">
    <div class="panel-heading sf-panel-heading">
      <h3 class="panel-tittle m-a0"><span class="fa fa-language"></span> <?php esc_html_e('Languages', 'service-finder'); ?> </h3>
    </div>
    <div class="panel-body sf-panel-body padding-30">
      <div class="row">
        <div class="col-lg-12">
          <div class="form-group">
            <label>
            <?php esc_html_e('Languages', 'service-finder'); ?>
            </label>
            <div class="input-group">
            <?php 
            if(!empty($userInfo['languages'])){
            $alllanguages = explode(',',$userInfo['languages']);
            }else{
            $alllanguages[] = $userInfo['languages'];
            }
			$languages = get_option( 'sf_languages');
			$languagearray = service_finder_get_alllanguages();
            ?>
              <select name="languages[]" class="sf-multiple-categories-select sf-select-box form-control sf-form-control" multiple="multiple" id="languages">
                <?php
				if(!empty($languages)){
                    foreach($languages as $language){
						if(in_array($language,$alllanguages)){
							$select = 'selected="selected"';
						}else{
							$select = '';
						}
					
						$flagimgsrc = SERVICE_FINDER_BOOKING_IMAGE_URL.'/flags/'.$language.'.png';
						$imgtag = '<img src="'.$flagimgsrc.'">';
						echo '<option '.$select.' value="'.esc_attr($language).'" data-content="'.esc_attr($imgtag).'<span>'.esc_attr($languagearray[$language]).'</span>">'. $languagearray[$language].'</option>';
					}
				}	
                ?>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div> -->
  <?php } ?>
  <!--Add cover image Section-->
  <?php 
  if(!empty($userCap)):
  if(in_array('cover-image',$userCap)):
  ?>
  <div class="panel panel-default gallery-images">
    <div class="panel-heading sf-panel-heading">
      <h3 class="panel-tittle m-a0"><span class="fa fa-image"></span> <?php esc_html_e('Cover Image', 'service-finder'); ?> </h3>
      <span><?php esc_html_e('Please upload 2000px x 400px size image for higher quality', 'service-finder'); ?></span> </div>
    <div class="panel-body sf-panel-body padding-30">
      <div class="row">
        <div class="col-md-12">
        	  <?php
			  if(in_array('crop',$userCap)){	
			  echo do_shortcode('[service_finder_crop_cover_image user_id="'.$globalproviderid.'"]');
			  }else
			  {
			  ?>
			  <div class="rwmb-field rwmb-plupload_image-wrapper">
                <div class="rwmb-input">
                  <ul class="rwmb-images rwmb-uploaded" data-field_id="coverimageuploader" data-delete_nonce="" data-reorder_nonce="" data-force_delete="0" data-max_file_uploads="1">
                    <?php
                                                                $coverimage = service_finder_getProviderAttachments($globalproviderid,'cover-image');
                                                                $hiddencoverclass = '';
                                                                if(!empty($coverimage)){
                                                                foreach($coverimage as $cimage){
                                                                        $src  = wp_get_attachment_image_src( $cimage->attachmentid, 'thumbnail' );
                                                                        $src  = $src[0];
                                                                        $i18n_delete = apply_filters( 'rwmb_image_delete_string', _x( 'Delete', 'image upload', 'service-finder' ) );
                                                                        $hiddencoverclass = 'hidden';
                                                                        
                                                                        $html = sprintf('<li id="item_%s">
                                                                        <img src="%s" />
                                                                        <div class="rwmb-image-bar">
                                                                            <a title="%s" class="rwmb-delete-file" href="javascript:;" data-attachment_id="%s">&times;</a>
                                                                            <input type="hidden" name="coverimageattachmentid[]" value="%s">
                                                                        </div>
                                                                    </li>',
                                                                    esc_attr($cimage->attachmentid),
                                                                    esc_url($src),
                                                                    esc_attr($i18n_delete), esc_attr($cimage->attachmentid),
                                                                    esc_attr($cimage->attachmentid)
                                                                    );
                                                                        echo $html;	
                                                                    }
                                                                }
                                                                ?>
                  </ul>
                  <div id="coverimageuploader-dragdrop" class="RWMB-drag-drop drag-drop hide-if-no-js new-files <?php echo esc_attr($hiddencoverclass); ?>" data-upload_nonce="1f7575f6fa" data-js_options="{&quot;runtimes&quot;:&quot;html5,silverlight,flash,html4&quot;,&quot;file_data_name&quot;:&quot;async-upload&quot;,&quot;browse_button&quot;:&quot;coverimageuploader-browse-button&quot;,&quot;drop_element&quot;:&quot;coverimageuploader-dragdrop&quot;,&quot;multiple_queues&quot;:true,&quot;max_file_size&quot;:&quot;8388608b&quot;,&quot;url&quot;:&quot;<?php echo esc_url($adminajaxurl); ?>&quot;,&quot;flash_swf_url&quot;:&quot;<?php echo esc_url($url); ?>wp-includes\/js\/plupload\/plupload.flash.swf&quot;,&quot;silverlight_xap_url&quot;:&quot;<?php echo esc_url($url); ?>wp-includes\/js\/plupload\/plupload.silverlight.xap&quot;,&quot;multipart&quot;:true,&quot;urlstream_upload&quot;:true,&quot;filters&quot;:[{&quot;title&quot;:&quot;Allowed  Files&quot;,&quot;extensions&quot;:&quot;jpg,jpeg,gif,png&quot;}],&quot;multipart_params&quot;:{&quot;field_id&quot;:&quot;coverimageuploader&quot;,&quot;action&quot;:&quot;coverimage_upload&quot;}}">
                    <div class = "drag-drop-inside text-center">
                      <p class="drag-drop-info"><?php esc_html_e('Drop files here', 'service-finder'); ?></p>
                      <p><?php esc_html_e('or', 'service-finder'); ?></p>
                      <p class="drag-drop-buttons">
                        <input id="coverimageuploader-browse-button" type="button" value="<?php esc_html_e('Select Files', 'service-finder'); ?>" class="button btn btn-default" />
                      </p>
                    </div>
                  </div>
                </div>
              </div>
			  <?php
			  }
			  ?>
        </div>
      </div>
    </div>
  </div>
  <?php 
  endif;
  endif;
  ?>
  <!--Gallery Images Section-->
  <div class="panel panel-default gallery-images">
    <div class="panel-heading sf-panel-heading">
      <h3 class="panel-tittle m-a0"><span class="fa fa-file-image-o"></span> <?php esc_html_e('Gallery Images', 'service-finder'); ?> </h3>
    </div>
    <div class="panel-body sf-panel-body padding-30">
      <div class="row">
        <div class="col-md-12">
          <div class="rwmb-field rwmb-plupload_image-wrapper">
            <div class="rwmb-input">
              <?php 
                if(!empty($userCap)){
                    if(in_array('gallery-images',$userCap)){
                        $package = get_user_meta($globalproviderid,'provider_role',true);
                        $packageNum = intval(substr($package, 8));
                        $maxupload = (!empty($service_finder_options['package'.$packageNum.'-gallery-images']))? $service_finder_options['package'.$packageNum.'-gallery-images'] : '';
                    }else{
                    $maxupload = (!empty($service_finder_options['default-gallery-images'])) ? $service_finder_options['default-gallery-images'] : '';
                    }
                }else{
                    $maxupload = (!empty($service_finder_options['default-gallery-images'])) ? $service_finder_options['default-gallery-images'] : '';
                }
                ?>
<ul class="rwmb-images rwmb-uploaded" data-field_id="plupload" data-delete_nonce="" data-reorder_nonce="" data-force_delete="0" data-max_file_uploads="<?php echo esc_attr($maxupload); ?>">
<?php
                    
                    $images = service_finder_getProviderAttachments($globalproviderid,'gallery');
                    $totalimages = count($images);
                    if($totalimages >= $maxupload){
                    $hiddenclass = 'hidden';
                    }else{
                    $hiddenclass = '';
                    }
                    if(!empty($images)){
                    foreach($images as $image){
                        $src  = wp_get_attachment_image_src( $image->attachmentid, 'thumbnail' );
                        $src  = $src[0];
                        $i18n_delete = apply_filters( 'rwmb_image_delete_string', _x( 'Delete', 'image upload', 'service-finder' ) );
                        
                        $html = sprintf('<li id="item_%s">
                        <img src="%s" />
                        <div class="rwmb-image-bar">
                            <a title="%s" class="rwmb-delete-file" href="javascript:;" data-attachment_id="%s">&times;</a>
                            <input type="hidden" name="attachmentid[]" value="%s">
                        </div>
                    </li>',
                    esc_attr($image->attachmentid),
                    esc_url($src),
                    esc_attr($i18n_delete), esc_attr($image->attachmentid),
                    esc_attr($image->attachmentid)
                    );
                        echo $html;	
                    }
                    }
                    ?>
              </ul>
              <div id="plupload-dragdrop" class="RWMB-drag-drop drag-drop hide-if-no-js new-files <?php echo esc_attr($hiddenclass); ?>" data-upload_nonce="1f7575f6fa" data-js_options="{&quot;runtimes&quot;:&quot;html5,silverlight,flash,html4&quot;,&quot;file_data_name&quot;:&quot;async-upload&quot;,&quot;browse_button&quot;:&quot;plupload-browse-button&quot;,&quot;drop_element&quot;:&quot;plupload-dragdrop&quot;,&quot;multiple_queues&quot;:true,&quot;max_file_size&quot;:&quot;8388608b&quot;,&quot;url&quot;:&quot;<?php echo esc_url($adminajaxurl); ?>&quot;,&quot;flash_swf_url&quot;:&quot;<?php echo esc_url($url); ?>wp-includes\/js\/plupload\/plupload.flash.swf&quot;,&quot;silverlight_xap_url&quot;:&quot;<?php echo esc_url($url); ?>wp-includes\/js\/plupload\/plupload.silverlight.xap&quot;,&quot;multipart&quot;:true,&quot;urlstream_upload&quot;:true,&quot;filters&quot;:[{&quot;title&quot;:&quot;Allowed Image Files&quot;,&quot;extensions&quot;:&quot;jpg,jpeg,gif,png&quot;}],&quot;multipart_params&quot;:{&quot;field_id&quot;:&quot;plupload&quot;,&quot;action&quot;:&quot;image_upload&quot;}}">
                <div class = "drag-drop-inside text-center">
                  <p class="drag-drop-info">
                    <?php esc_html_e('Drop images here', 'service-finder'); ?>
                  </p>
                  <p><?php esc_html_e('or', 'service-finder'); ?></p>
                  <p class="drag-drop-buttons">
                    <input id="plupload-browse-button" type="button" value="<?php esc_html_e('Select Files', 'service-finder'); ?>" class="button btn btn-default" />
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--Attachments Section-->

  <!--Embedded Code Section-->


