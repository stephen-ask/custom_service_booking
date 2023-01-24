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
$currUser = wp_get_current_user(); 
$url = str_replace('/','\/',$service_finder_Params['homeUrl']);
$adminajaxurl = str_replace('/','\/',admin_url('admin-ajax.php'));
$hiddenclass = '';
wp_add_inline_script( 'service_finder-js-bookings-form', '/*Declare global variable*/
var user_id = "'.$currUser->ID.'";', 'after' );

wp_add_inline_script( 'service_finder-js-form-submit', '/*Declare global variable*/
var userrole = "'.service_finder_getUserRole($current_user->ID).'";
var addressalert;
', 'after' );

$userInfo = service_finder_getCurrentUserInfo();

wp_add_inline_script( 'google-map', 'jQuery(function() {
	/*Autofill address by google script in 3rd Step*/
	function service_finder_initCustomerAddressAutoComplete(){
				var address = document.getElementById("customeraddress");

				var my_address = new google.maps.places.Autocomplete(address);
		
				google.maps.event.addListener(my_address, "place_changed", function() {
            var place = my_address.getPlace();
            
            // if no location is found
            if (!place.geometry) {
                return;
            }
            
			var $city =jQuery("#customercity");
            var $state = jQuery("#customerstate");
			var $country = jQuery("#customercountry");
			var $zipcode = jQuery("#customerzipcode");
			
            var country_long_name = "";
            var country_short_name = "";
            for(var i=0; i<place.address_components.length; i++){
                var address_component = place.address_components[i];
                var ty = address_component.types;

                for (var k = 0; k < ty.length; k++) {
                    if (ty[k] === "locality" || ty[k] === "sublocality" || ty[k] === "sublocality_level_1"  || ty[k] === "postal_town") {
                        $city.val(address_component.long_name);
						var cityname = address_component.long_name;
                    } else if (ty[k] === "administrative_area_level_1" || ty[k] === "administrative_area_level_2") {
                        $state.val(address_component.long_name);
						var statename = address_component.long_name;
                    } else if (ty[k] === "postal_code") {
                        $zipcode.val(address_component.short_name);
                    } else if(ty[k] === "country"){
                        country_long_name = address_component.long_name;
                        country_short_name = address_component.short_name;
						$country.val(address_component.long_name);
                    }
                }
            }
			
            var address = jQuery("#customeraddress").val();
			var new_address = address.replace(cityname,"");
            new_address = new_address.replace(statename,"");
			
			new_address = new_address.replace(country_long_name,"");
            new_address = new_address.replace(country_short_name,"");
            new_address = jQuery.trim(new_address);
            
            
            new_address = new_address.replace(/,/g, "");
            new_address = new_address.replace(/ +/g," ");
			jQuery("#customeraddress").val(address);
			
        
         });
			}
	if (jQuery("#customeraddress").length && siteautosuggestion == true){
    google.maps.event.addDomListener(window, "load", service_finder_initCustomerAddressAutoComplete);
	}
	});', 'after' );
?>
<div class="sf-page-title">
<h2><?php echo (!empty($service_finder_options['label-profile-settings'])) ? esc_html($service_finder_options['label-profile-settings']) : esc_html__('Profile Settings', 'service-finder'); ?></h2>
</div>
<!--Cutomer Profile Settings Template-->
<form class="pro-setting customer-update" method="post">
  	<div class="panel panel-default">
    <div class="panel-heading sf-panel-heading">
      <h3 class="panel-tittle m-a0"><span class="fa fa-user"></span> <?php esc_html_e('User Avatar', 'service-finder'); ?> </h3>
    </div>
    <div class="panel-body sf-panel-body padding-30">
      <div class="auther-pic-text form-inr clearfix">
          <!--Avatar Upload-->
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
              <?php esc_html_e('Update your profile picture, if not the default Avatar image will be used', 'service-finder'); ?>
              </em></p>
            <ul class="auther-limit list-unstyled">
              <li><strong>
                <?php esc_html_e('Max Upload Size', 'service-finder'); ?>
                :</strong> 1MB</li>
              <li><strong>
                <?php esc_html_e('Dimensions', 'service-finder'); ?>
                :</strong> 150x150</li>
              <li><strong>
                <?php esc_html_e('Extensions', 'service-finder'); ?>
                :</strong> JPEG,PNG</li>
            </ul>
          </div>
        </div>
    </div>
    </div>
    
    <div class="panel panel-default">
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
              <div class="col-lg-6">
                <div class="form-group">
                  <label>
                  <?php esc_html_e('First Name', 'service-finder'); ?>
                  </label>
                  <div class="input-group"> <i class="input-group-addon fixed-w fa fa-user"></i>
                    <input type="text" class="form-control sf-form-control" name="first_name" value="<?php echo esc_attr($userInfo['fname']) ?>">
                  </div>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label>
                  <?php esc_html_e('Last Name', 'service-finder'); ?>
                  </label>
                  <div class="input-group"> <i class="input-group-addon fixed-w fa fa-user"></i>
                    <input type="text" class="form-control sf-form-control" name="last_name" value="<?php echo esc_attr($userInfo['lname']) ?>">
                  </div>
                </div>
              </div>
            </div>
    </div>
    </div>
    
    <div class="panel panel-default">
    <div class="panel-heading sf-panel-heading">
      <h3 class="panel-tittle m-a0"><span class="fa fa-envelope"></span> <?php esc_html_e('Contact Detail', 'service-finder'); ?> </h3>
    </div>
    <div class="panel-body sf-panel-body padding-30">
      <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <label>
                  <?php esc_html_e('Phone', 'service-finder'); ?>
                  </label>
                  <div class="input-group"> <i class="input-group-addon fixed-w fa fa-user"></i>
                    <input type="text" class="form-control sf-form-control" name="phone" value="<?php echo esc_attr($userInfo['phone']) ?>">
                  </div>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label>
                  <?php esc_html_e('Phone2', 'service-finder'); ?>
                  </label>
                  <div class="input-group"> <i class="input-group-addon fixed-w fa fa-user"></i>
                    <input type="text" class="form-control sf-form-control" name="phone2" value="<?php echo esc_attr($userInfo['phone2']) ?>">
                  </div>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label>
                  <?php esc_html_e('Email', 'service-finder'); ?>
                  </label>
                  <div class="input-group"> <i class="input-group-addon fixed-w fa fa-user"></i>
                    <input type="text" class="form-control sf-form-control" name="user_email" value="<?php echo esc_attr($userInfo[0]->user_email) ?>">
                  </div>
                </div>
              </div>
            </div>
    </div>
    </div>
    
    <div class="panel panel-default">
    <div class="panel-heading sf-panel-heading">
      <h3 class="panel-tittle m-a0"><span class="fa fa-address-card"></span> <?php esc_html_e('Address', 'service-finder'); ?> </h3>
    </div>
    <div class="panel-body sf-panel-body padding-30">
      <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <label>
                  <?php esc_html_e('Address', 'service-finder'); ?>
                  </label>
                  <div class="input-group"> <i class="input-group-addon fixed-w fa fa-map-marker"></i>
                    <input type="text" class="form-control sf-form-control" name="address" id="customeraddress" value="<?php echo esc_attr($userInfo['address']) ?>">
                  </div>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label>
                  <?php esc_html_e('Apt/Suite #', 'service-finder'); ?>
                  </label>
                  <div class="input-group"> <i class="input-group-addon fixed-w fa fa-map-marker"></i>
                    <input type="text" class="form-control sf-form-control" name="apt" id="customerapt" value="<?php echo esc_attr($userInfo['apt']) ?>">
                  </div>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label>
                  <?php esc_html_e('City', 'service-finder'); ?>
                  </label>
                  <div class="input-group"> <i class="input-group-addon fixed-w fa fa-map-marker"></i>
                    <input type="text" class="form-control sf-form-control" name="city" id="customercity" value="<?php echo esc_attr($userInfo['city']) ?>">
                  </div>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label>
                  <?php esc_html_e('State', 'service-finder'); ?>
                  </label>
                  <div class="input-group"> <i class="input-group-addon fixed-w fa fa-map-marker"></i>
                    <input type="text" class="form-control sf-form-control" name="state" id="customerstate" value="<?php echo esc_attr($userInfo['state']) ?>">
                  </div>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label>
                  <?php esc_html_e('Postal Code', 'service-finder'); ?>
                  </label>
                  <div class="input-group"> <i class="input-group-addon fixed-w fa fa-map-marker"></i>
                    <input type="text" class="form-control sf-form-control" name="zipcode" id="customerzipcode" value="<?php echo esc_attr($userInfo['zipcode']) ?>">
                  </div>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label>
                  <?php esc_html_e('Country', 'service-finder'); ?>
                  </label>
                  <div class="input-group"> <i class="input-group-addon fixed-w fa fa-map-marker"></i>
                    <input type="text" class="form-control sf-form-control" name="country" id="customercountry" value="<?php echo esc_attr($userInfo['country']) ?>">
                  </div>
                </div>
              </div>
            </div>
    </div>
    </div>
    
    <div class="panel panel-default">
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
                    <input type="password" class="form-control sf-form-control" name="password" id="password">
                  </div>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label>
                  <?php esc_html_e('Repeat Password', 'service-finder'); ?>
                  </label>
                  <div class="input-group"> <i class="input-group-addon fixed-w fa fa-lock"></i>
                    <input type="password" class="form-control sf-form-control" name="confirm_password" id="confirm_password">
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
    
    <div class="form-group margin-0">
      <input type="hidden" name="user_id" value="<?php echo esc_attr($userInfo[0]->ID); ?>" />
      <input type="submit" class="btn btn-primary margin-r-10" name="update-profile" value="<?php esc_html_e('Submit information', 'service-finder'); ?>" />
      <input type="reset" class="btn btn-custom" value="<?php esc_html_e('Reset', 'service-finder'); ?>">
    </div>
  </form>
