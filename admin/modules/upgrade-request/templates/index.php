<?php 
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<!--Template for dispaly featured requests-->
<?php
$service_finder_options = get_option('service_finder_options');
$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('Provider', 'service-finder');
?>
<div class="sf-wpbody-inr">
  <div class="sedate-title">
    <h2>
      <?php esc_html_e( 'Upgrade Requests', 'service-finder' ); ?>
    </h2>
  </div>
  <div class="datatable-outer">
  <div class="table-responsive">
    <table id="upgrade-requests-grid" class="table table-striped sf-table">
    <thead>
      <tr>
        <th></th>
        <th><?php echo esc_html__( 'Name', 'service-finder' ); ?></th>
        <th><?php esc_html_e( 'Upgrade Request Date', 'service-finder' ); ?></th>
        <th><?php esc_html_e( 'Current Package', 'service-finder' ); ?></th>
        <th><?php esc_html_e( 'Upgrade Package', 'service-finder' ); ?></th>
        <th><?php esc_html_e( 'Amount', 'service-finder' ); ?></th>
        <th><?php esc_html_e( 'Payment Status', 'service-finder' ); ?></th>
        <th><?php esc_html_e( 'Action', 'service-finder' ); ?></th>
      </tr>
    </thead>
    </table>
  </div>
  </div>
</div>
