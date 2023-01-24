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
wp_enqueue_script('service_finder-js-payout');
wp_add_inline_script( 'service_finder-js-payout', '/*Declare global variable*/
var user_id = "'.$globalproviderid.'";', 'after' );

$accountinfo = service_finder_get_stripe_account_info($globalproviderid);

//echo '<pre>';print_r($accountinfo);echo '</pre>';

$first_name = $last_name = $email = $dob = $address = $city = $postal_code = $state = $country = $id_number_provided = $last4 = $routing_number = $currency = $bankcountry = $website = $product_description = '';

if(!empty($accountinfo))
{
$individual = service_finder_get_data($accountinfo,'individual');
//echo '<pre>';print_r($individual);echo '</pre>';
$first_name = service_finder_get_data($individual,'first_name');
$last_name = service_finder_get_data($individual,'last_name');
$email = service_finder_get_data($individual,'email');
$dob = service_finder_get_data($individual['dob'],'year').'-'.service_finder_get_data($individual['dob'],'month').'-'.service_finder_get_data($individual['dob'],'day');

$address = service_finder_get_data($individual['address'],'line1');
$city = service_finder_get_data($individual['address'],'city');
$postal_code = service_finder_get_data($individual['address'],'postal_code');
$state = service_finder_get_data($individual['address'],'state');
$country = service_finder_get_data($individual['address'],'country');

$external_accounts = service_finder_get_data($accountinfo['external_accounts'],'data');
//echo '<pre>';print_r($external_accounts);echo '</pre>';
$id_number_provided = get_user_meta($globalproviderid,'stripe_personal_id_number',true);

$last4 = service_finder_get_data($external_accounts[0],'last4');
$routing_number = service_finder_get_data($external_accounts[0],'routing_number');
$currency = service_finder_get_data($external_accounts[0],'currency');
$bankcountry = service_finder_get_data($external_accounts[0],'country');

$website = service_finder_get_data($accountinfo['business_profile'],'url');
$product_description = service_finder_get_data($accountinfo['business_profile'],'product_description');
}

echo service_finder_get_payout_status($accountinfo);
echo service_finder_get_fields_needed($accountinfo);
echo service_finder_get_documents_status($accountinfo);
?>

<form class="payout-general" method="post">
  <div class="panel panel-default about-me-here">
    <div class="panel-heading sf-panel-heading">
      <h3 class="panel-tittle m-a0"><span class="fa fa-user"></span> <?php esc_html_e('General', 'service-finder'); ?> </h3>
    </div>
    <div class="panel-body sf-panel-body padding-30">
      <div class="row">
        <div class="col-lg-6">
          <div class="form-group">
            <label>
            <?php esc_html_e('First Name', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-user"></i>
              <input type="text" class="form-control sf-form-control" name="first_name" value="<?php echo esc_attr($first_name) ?>">
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="form-group">
            <label>
            <?php esc_html_e('Last Name', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-user"></i>
              <input type="text" class="form-control sf-form-control" name="last_name" value="<?php echo esc_attr($last_name) ?>">
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="form-group">
            <label>
            <?php esc_html_e('Email', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-envelope"></i>
              <input type="text" class="form-control sf-form-control" name="email" value="<?php echo esc_attr($email) ?>">
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="form-group">
            <label>
            <?php esc_html_e('Date of Birth', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-envelope"></i>
              <input type="text" class="form-control sf-form-control payout_customer_dob" name="dob" value="<?php echo (!empty($dob)) ? esc_attr($dob) : ''; ?>">
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="form-group">
            <label>
            <?php esc_html_e('Address', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-envelope"></i>
              <input type="text" class="form-control sf-form-control" name="address" value="<?php echo esc_attr($address) ?>">
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="form-group">
            <label>
            <?php esc_html_e('Postal Code', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-envelope"></i>
              <input type="text" class="form-control sf-form-control" name="postal_code" value="<?php echo esc_attr($postal_code) ?>">
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="form-group">
            <label>
            <?php esc_html_e('City', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-envelope"></i>
              <input type="text" class="form-control sf-form-control" name="city" value="<?php echo esc_attr($city) ?>">
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="form-group">
            <label>
            <?php esc_html_e('State', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-envelope"></i>
              <input type="text" class="form-control sf-form-control" name="state" value="<?php echo esc_attr($state) ?>">
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="form-group">
            <label>
            <?php esc_html_e('Country', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-envelope"></i>
              <input type="text" class="form-control sf-form-control" name="country" value="<?php echo esc_attr($country) ?>">
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="form-group">
            <label>
            <?php esc_html_e('Business URL', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-envelope"></i>
              <input type="text" class="form-control sf-form-control" name="website" value="<?php echo esc_attr($website) ?>">
            </div>
          </div>
        </div>
        <div class="col-lg-12">
          <div class="form-group">
            <label>
            <?php esc_html_e('Business Short Description', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-envelope"></i>
              <textarea class="form-control sf-form-control" name="product_description"><?php echo esc_attr($product_description) ?></textarea>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>  
  
  <div class="panel panel-default about-me-here" style="display:none;">
    <div class="panel-heading sf-panel-heading">
      <h3 class="panel-tittle m-a0"><span class="fa fa-user"></span> <?php esc_html_e('Personal ID Number (Optional)', 'service-finder'); ?> </h3>
      <span class="id-number-info"><?php echo esc_html__('The government-issued ID number of the individual, as appropriate for the representatives country. (Examples are a Social Security Number in the U.S., or a Social Insurance Number in Canada).', 'service-finder'); ?></span>
    </div>
    
    <div class="panel-body sf-panel-body padding-30">
    <div class="row">
        <div class="col-lg-12">
          <div class="form-group">
            <label>
            <?php esc_html_e('Personal ID Number', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-user"></i>
              <input type="text" class="form-control sf-form-control" name="personal_id_number" value="<?php echo esc_attr($id_number_provided) ?>">
            </div>
          </div>
        </div>
      </div>
    </div>  
    </div>
    
    <div class="panel panel-default about-me-here">  
    <div class="panel-heading sf-panel-heading">
      <h3 class="panel-tittle m-a0"><span class="fa fa-user"></span> <?php esc_html_e('External Account', 'service-finder'); ?> </h3>
    </div>  
    
    <div class="panel-body sf-panel-body padding-30">
      <div class="row">
        <div class="col-lg-6">
        <label>
        <?php esc_html_e('Currency', 'service-finder'); ?>
        </label>
        <div class="form-group">
        <?php 
        $currencylist = service_finder_get_currency_list();
        ?>
          <select class="sf-select-box form-control sf-form-control" name="currency" data-live-search="true" title="<?php esc_html_e('Currency', 'service-finder'); ?>">
            <option value=""><?php esc_html_e('Currency', 'service-finder'); ?></option>
            <?php
            if(!empty($currencylist)){
                foreach($currencylist as $key => $value){
                    if($currency == strtolower($key)){
                    $select = 'selected="selected"';
                    }else{
                    $select = '';
                    }
                    echo '<option '.$select.' value="'.esc_attr(strtolower($key)).'">'.esc_html($value).'</option>';	
                }
            }
            ?>
          </select>  
        </div>
        </div>
        <div class="col-lg-6">
        <label>
        <?php esc_html_e('Bank Country', 'service-finder'); ?>
        </label>
        <div class="form-group">
        <?php 
        $countrylist = service_finder_get_country_list();
        ?>
          <select class="sf-select-box form-control sf-form-control" name="bank_country" data-live-search="true" title="<?php esc_html_e('Country', 'service-finder'); ?>">
            <option value=""><?php esc_html_e('Country', 'service-finder'); ?></option>
            <?php
            if(!empty($countrylist)){
                foreach($countrylist as $key => $value){
                    if($bankcountry == $key){
                    $select = 'selected="selected"';
                    }else{
                    $select = '';
                    }
                    echo '<option '.$select.' value="'.esc_attr($key).'">'.esc_html($value).'</option>';	
                }
            }
            ?>
          </select>  
        </div>
        </div>
        <?php
		if($bankcountry == 'US' || $bankcountry == 'AU' || $bankcountry == 'BR' || $bankcountry == 'CA' || $bankcountry == 'HK' || $bankcountry == 'JP' || $bankcountry == 'MY' || $bankcountry == 'SG')
		{
			$routingfield = 'block';
			$routinglabel = esc_html__('Routing Number', 'service-finder');
			$acountlabel = esc_html__('Account Number', 'service-finder');
		}elseif($bankcountry == 'MX' || $bankcountry == 'NZ'){
			$routingfield = 'none';
			$routinglabel = esc_html__('Routing Number', 'service-finder');
			$acountlabel = esc_html__('Account Number', 'service-finder');
		}elseif($bankcountry == 'GB'){
			$routingfield = 'block';
			$routinglabel = esc_html__('Sort Code', 'service-finder');
			$acountlabel = esc_html__('Account Number', 'service-finder');
		}elseif($bankcountry == 'AT' || $bankcountry == 'BE' || $bankcountry == 'BG' || $bankcountry == 'HR' || $bankcountry == 'CY' || $bankcountry == 'CZ' || $bankcountry == 'DK' || $bankcountry == 'EE' || $bankcountry == 'FI' || $bankcountry == 'FR' || $bankcountry == 'DE' || $bankcountry == 'GR' || $bankcountry == 'HU' || $bankcountry == 'IE' || $bankcountry == 'IT' || $bankcountry == 'LV' || $bankcountry == 'LT' || $bankcountry == 'LU' || $bankcountry == 'MT' || $bankcountry == 'NL' || $bankcountry == 'PL' || $bankcountry == 'PT' || $bankcountry == 'RO' || $bankcountry == 'SK' || $bankcountry == 'SI' || $bankcountry == 'ES' || $bankcountry == 'SE'){ //Its apply on all europe country. Need to add country in country list function
			$routingfield = 'none';
			$routinglabel = esc_html__('Routing Number', 'service-finder');
			$acountlabel = esc_html__('IBAN Number', 'service-finder');
		}elseif($bankcountry == 'IN'){
			$routingfield = 'block';
			$routinglabel = esc_html__('IFSC Code', 'service-finder');
			$acountlabel = esc_html__('Account Number', 'service-finder');
		}else
		{
			$routingfield = 'block';
			$routinglabel = esc_html__('Routing Number', 'service-finder');
			$acountlabel = esc_html__('Account Number', 'service-finder');
		}
		?>
        <div class="col-lg-6" id="routing-field" style="display:<?php echo esc_attr($routingfield); ?>">
          <div class="form-group">
            <label id="routing-field-label">
            <?php echo esc_html($routinglabel); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-envelope"></i>
              <input type="text" class="form-control sf-form-control" name="routing_number" value="<?php echo esc_attr($routing_number) ?>">
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="form-group">
            <label id="acount-field-label">
            <?php echo esc_html($acountlabel); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-envelope"></i>
              <input type="text" class="form-control sf-form-control" name="account_number" value="<?php echo esc_attr($last4) ?>">
            </div>
          </div>
        </div>
      </div>
    </div>
    </div>
    
  

  <div class="sf-submit-payout">
  	<input type="submit" class="btn btn-primary margin-r-10" value="<?php esc_html_e('Submit information', 'service-finder'); ?>" />
  </div>
  </form>
<br />
<form class="stripe-identity-verification" method="post">
<div class="panel panel-default about-me-here">
<div class="panel-heading sf-panel-heading">
  <h3 class="panel-tittle m-a0"><span class="fa fa-user"></span> <?php esc_html_e('Identity Verification', 'service-finder'); ?> </h3>
</div>
<div class="panel-body sf-panel-body padding-30">
  <div class="row">
    <div class="col-md-12">
      <div class="rwmb-field rwmb-plupload_image-wrapper">
        <div class="rwmb-input">
          <ul class="rwmb-images rwmb-uploaded" data-field_id="stripeidentityuploader" data-delete_nonce="" data-reorder_nonce="" data-force_delete="0" data-max_file_uploads="1">
          <?php
		  	$attachmentid = get_user_meta($globalproviderid,'stripe_identity_attachment_id',true);
			$hiddenidentityclass = '';
		  	if($attachmentid != '')
			{
		  	$src  = wp_get_attachment_image_src( $attachmentid, 'thumbnail' );
				$src  = $src[0];
				$i18n_delete = apply_filters( 'rwmb_image_delete_string', _x( 'Delete', 'image upload', 'service-finder' ) );
				$hiddenidentityclass = 'hidden';
				
				$html = sprintf('<li id="item_%s">
				<img src="%s" />
				<div class="rwmb-image-bar">
					<a title="%s" class="rwmb-delete-file" href="javascript:;" data-attachment_id="%s">&times;</a>
					<input type="hidden" name="stripeidentityattachmentid" value="%s">
				</div>
			</li>',
			esc_attr($attachmentid),
			esc_url($src),
			esc_attr($i18n_delete), esc_attr($attachmentid),
			esc_attr($attachmentid)
			);
			echo $html;
			}
			?>
          </ul>
          <div id="stripeidentity-dragdrop" class="RWMB-drag-drop drag-drop hide-if-no-js new-files <?php echo esc_attr($hiddenidentityclass); ?>" data-upload_nonce="1f7575f6fa" data-js_options="{&quot;runtimes&quot;:&quot;html5,silverlight,flash,html4&quot;,&quot;file_data_name&quot;:&quot;async-upload&quot;,&quot;browse_button&quot;:&quot;stripeidentity-browse-button&quot;,&quot;drop_element&quot;:&quot;stripeidentity-dragdrop&quot;,&quot;multiple_queues&quot;:true,&quot;max_file_size&quot;:&quot;8388608b&quot;,&quot;url&quot;:&quot;<?php echo esc_url($adminajaxurl); ?>&quot;,&quot;flash_swf_url&quot;:&quot;<?php echo esc_url($url); ?>wp-includes\/js\/plupload\/plupload.flash.swf&quot;,&quot;silverlight_xap_url&quot;:&quot;<?php echo esc_url($url); ?>wp-includes\/js\/plupload\/plupload.silverlight.xap&quot;,&quot;multipart&quot;:true,&quot;urlstream_upload&quot;:true,&quot;filters&quot;:[{&quot;title&quot;:&quot;Allowed  Files&quot;,&quot;extensions&quot;:&quot;jpg,jpeg,gif,png&quot;}],&quot;multipart_params&quot;:{&quot;field_id&quot;:&quot;stripeidentity&quot;,&quot;action&quot;:&quot;stripeidentity_upload&quot;}}">
            <div class = "drag-drop-inside text-center">
              <p class="drag-drop-info"><?php esc_html_e('Drop files here', 'service-finder'); ?></p>
              <p><?php esc_html_e('or', 'service-finder'); ?></p>
              <p class="drag-drop-buttons">
                <input id="stripeidentity-browse-button" type="button" value="<?php esc_html_e('Select Files', 'service-finder'); ?>" class="button btn btn-default" />
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>

<div class="sf-submit-payout">
<input type="submit" class="btn btn-primary margin-r-10" value="<?php esc_html_e('Submit information', 'service-finder'); ?>" />
</div>
</form>  