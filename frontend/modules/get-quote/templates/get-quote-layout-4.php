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
global $author;
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
<!--Get Quote form Start-->
<form class="get-quote" method="post">
    <div class="col-md-4">
        <div class="form-group">
            <label><?php esc_html_e('Name', 'service-finder'); ?></label>
            <input name="customer_name" id="customer_name" type="text" class="form-control" value="<?php echo esc_attr($fullname); ?>">
        </div>
    </div>
    <div class="col-md-4">
    <div class="form-group">
        <label><?php esc_html_e('Email', 'service-finder'); ?></label>
        <input name="customer_email" id="customer_email" type="text" class="form-control" value="<?php echo esc_attr($email); ?>">
    </div>
    </div>
    <div class="col-md-4">
    <div class="form-group">
        <label><?php esc_html_e('Phone', 'service-finder'); ?></label>
        <input name="phone" type="text" class="form-control" value="<?php echo esc_attr($phone); ?>">
    </div>
    </div>
    <div class="col-md-12">
    <div class="form-group">
        <label><?php esc_html_e('Message', 'service-finder'); ?></label>
        <textarea name="description" id="description" class="form-control"></textarea>
    </div>
    </div>
    <?php 
	  if(is_user_logged_in() && service_finder_getUserRole($current_user->ID) == 'Customer'){
	  echo service_finder_quote_extend($author); 
	  }
	  ?>
	  <?php echo service_finder_captcha('requestquote'); ?>
    <div class="col-md-12">
    <div class="form-group text-center qout-submit-btn">
        <input type="hidden" id="proid" name="proid" data-provider="<?php echo esc_attr($author) ?>" value="<?php echo esc_attr($author) ?>" />
        <input type="submit" value="<?php esc_html_e('Send information', 'service-finder'); ?>" name="get-quote" class="btn btn-primary">
    </div>
    </div>
</form>
<!--Get Quote form End-->
