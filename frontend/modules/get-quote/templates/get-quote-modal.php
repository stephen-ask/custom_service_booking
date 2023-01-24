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
$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? esc_html__('Contact ', 'service-finder').$service_finder_options['provider-replace-string'] : esc_html__('Contact Provider', 'service-finder');	
$requestquote = (!empty($service_finder_options['requestquote-replace-string'])) ? esc_attr($service_finder_options['requestquote-replace-string']) : esc_html__( 'Request a Quote', 'service-finder' );
$current_user = wp_get_current_user(); 
if(service_finder_getUserRole($current_user->ID) == 'Customer')
{
$userinfo = service_finder_getUserInfo($current_user->ID);
$fullname = service_finder_getCustomerName($current_user->ID);
$email = service_finder_getCustomerEmail($current_user->ID);
$phone = service_finder_get_data($userinfo,'phone');
}else{
$fullname = '';
$email = '';
$phone = '';
}
?>
<!--Get Quote form in modal popup box Start-->

<div id="quotes-Modal" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">
          <?php echo esc_html($requestquote); ?>
        </h4>
      </div>
      <form class="get-quote" method="post">
        <div class="modal-body clearfix row">
          <div class="toggle-quoterelated-providers">
          <div class="col-md-6">
            <div class="form-group">
              <div class="input-group"> <i class="input-group-addon fa fa-user"></i>
                <input name="customer_name" id="customer_name" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Name', 'service-finder'); ?>" value="<?php echo esc_attr($fullname); ?>">
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <div class="input-group"> <i class="input-group-addon fa fa-envelope"></i>
                <input name="customer_email" id="customer_email" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Email', 'service-finder'); ?>" value="<?php echo esc_attr($email); ?>">
              </div>
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
              <div class="input-group"> <i class="input-group-addon fa fa-phone"></i>
                <input name="phone" type="text" class="form-control sf-form-control" value="<?php echo esc_attr($phone); ?>" placeholder="<?php esc_html_e('Phone', 'service-finder'); ?>">
              </div>
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
              <div class="input-group"> <i class="input-group-addon fa fa-pencil"></i>
                <textarea name="description" id="description" cols="4" class="form-control sf-form-control"></textarea>
              </div>
            </div>
          </div>
          </div>
          <?php 
		  if(is_user_logged_in() && service_finder_getUserRole($current_user->ID) == 'Customer'){
		  $bookingpagestyle = (isset($service_finder_options['booking-page-style'])) ? esc_attr($service_finder_options['booking-page-style']) : '';
		  if($bookingpagestyle == 'style-2'){
		  echo service_finder_quote_extend($author); 
		  }
		  }
		  ?>
          <div class="toggle-quoterelated-providers">
		  <?php echo service_finder_captcha('requestquotepopup'); ?>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">
          <?php esc_html_e('Cancel', 'service-finder'); ?>
          </button>
          <input type="hidden" id="proid" name="proid" data-provider="" value="" />
          <input type="submit" value="<?php esc_html_e('Send information', 'service-finder'); ?>" name="get-quote" class="btn btn-primary">
        </div>
      </form>
    </div>
  </div>
</div>
<!--Get Quote form in modal popup box End-->
