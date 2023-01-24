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
$claim_business = (!empty($service_finder_options['string-claim-business'])) ? esc_html($service_finder_options['string-claim-business']) : esc_html__('Claim Business', 'service-finder');
?>
<!--Claim Business form in modal popup box Start-->

<div id="claimbusiness-Modal" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">
          <?php echo esc_html($claim_business); ?>
        </h4>
      </div>
      <form class="claim-business" method="post">
        <div class="modal-body clearfix row">
          <div class="col-md-6">
            <div class="form-group">
              <div class="input-group"> <i class="input-group-addon fa fa-user"></i>
                <input name="customer_name" type="text" class="form-control" placeholder="<?php esc_html_e('Full Name', 'service-finder'); ?>">
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <div class="input-group"> <i class="input-group-addon fa fa-envelope"></i>
                <input name="customer_email" type="text" class="form-control" placeholder="<?php esc_html_e('Email', 'service-finder'); ?>">
              </div>
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
              <div class="input-group"> <i class="input-group-addon fa fa-pencil"></i>
                <textarea name="description" id="description" cols="4" class="form-control"></textarea>
              </div>
            </div>
          </div>
          <?php echo service_finder_captcha('claimbusiness'); ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">
          <?php esc_html_e('Cancel', 'service-finder'); ?>
          </button>
          <input type="submit" value="<?php echo esc_html($claim_business); ?>" name="claim-business" class="btn btn-primary">
        </div>
      </form>
    </div>
  </div>
</div>
<!--Claim Business form in modal popup box End-->
