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

$user_birthday = get_user_meta($globalproviderid,'user_birthday',true);
$user_nationality = get_user_meta($globalproviderid,'user_nationality',true);
$billing_country = get_user_meta($globalproviderid,'billing_country',true);
?>
<?php if(get_user_meta( $globalproviderid, 'is_vendor', true ) == 'yes'){  
echo '<div class="alert alert-info">'.esc_html__('First step completed, please continue to next step.', 'service-finder').'</div>';
}?>
<form class="payout-settings" method="post">
  <div class="panel panel-default about-me-here">
    <div class="panel-heading sf-panel-heading">
      <h3 class="panel-tittle m-a0"><span class="fa fa-user"></span> <?php esc_html_e('Payout Settings', 'service-finder'); ?> </h3>
    </div>
    <div class="panel-body sf-panel-body padding-30">
      <div class="row">
        <div class="col-lg-12">
          <div class="form-group">
            <label>
            <?php esc_html_e('Date of Birth', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-envelope"></i>
              <input type="text" class="form-control sf-form-control mp_dob" name="dob" value="<?php echo (!empty($user_birthday)) ? esc_attr($user_birthday) : ''; ?>">
            </div>
          </div>
        </div>
		<div class="col-md-6">
            <div class="form-group">
              <label>
	            <?php esc_html_e('Nationality', 'service-finder'); ?>
    	      </label>
              <select class="sf-select-box form-control" name="mp_nationality" data-live-search="true" title="<?php esc_html_e('Country', 'service-finder'); ?>" id="mp_nationality">
              <option value="">
                <?php esc_html_e('Select Country', 'service-finder'); ?>
                </option>
              <?php
              $allcountry = (!empty($service_finder_options['all-countries'])) ? $service_finder_options['all-countries'] : '';
              $countries = service_finder_get_countries();
              if($allcountry){
                  if(!empty($countries)){
                    foreach($countries as $key => $country){
						if($user_nationality == $key){
							$select = 'selected="selected"';
						}else{
							$select = '';
						}
                        echo '<option '.esc_attr($select).' value="'.esc_attr($key).'">'. esc_html( $country ).'</option>';
                    }
                  }
              }
              ?>
              </select>
            </div>
          </div>
        <div class="col-md-6">
            <div class="form-group">
              <label>
	            <?php esc_html_e('Country', 'service-finder'); ?>
    	      </label>
              <select class="sf-select-box form-control" name="mp_country" data-live-search="true" title="<?php esc_html_e('Country', 'service-finder'); ?>" id="mp_country">
              <option value="">
                <?php esc_html_e('Select Country', 'service-finder'); ?>
                </option>
              <?php
              $allcountry = (!empty($service_finder_options['all-countries'])) ? $service_finder_options['all-countries'] : '';
              $countries = service_finder_get_countries();
              if($allcountry){
                  if(!empty($countries)){
                    foreach($countries as $key => $country){
                        if($billing_country == $key){
							$select = 'selected="selected"';
						}else{
							$select = '';
						}
                        echo '<option '.esc_attr($select).' value="'.esc_attr($key).'">'. esc_html( $country ).'</option>';
                    }
                  }
              }
              ?>
              </select>
            </div>
          </div>
      </div>
    </div>
  </div>

  <div class="sf-submit-payout">
  	<?php 
	if(get_user_meta( $globalproviderid, 'is_vendor', true ) == 'yes'){ 
	$mangopayurl = service_finder_get_url_by_shortcode('[wcv_shop_settings]');
	?>
    <a class="btn btn-primary" href="<?php echo esc_url($mangopayurl); ?>"><?php echo esc_html__( 'Continue', 'service-finder' ); ?></a>
	<?php }else{ ?>
  	<input type="submit" class="btn btn-primary margin-r-10" value="<?php esc_html_e('Submit information', 'service-finder'); ?>" />
    <?php } ?>
  </div>
  </form>