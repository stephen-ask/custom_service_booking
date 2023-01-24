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
?>

<form class="paypal-masspay" method="post">
  <div class="panel panel-default password-update">
    <div class="panel-heading sf-panel-heading">
      <h3 class="panel-tittle m-a0"><span class="fa fa-paypal"></span> <?php esc_html_e('Paypal Account Details', 'service-finder'); ?> </h3>
    </div>
    <div class="panel-body sf-panel-body padding-30">
      <div class="row">
        <div class="col-lg-12">
          <div class="form-group">
            <label>
            <?php esc_html_e('Paypal Email ID', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-lock"></i>
              <input type="text" class="form-control sf-form-control" name="paypal_email_id" value="<?php echo esc_attr(get_user_meta($globalproviderid,'paypal_email_id',true)) ?>">
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