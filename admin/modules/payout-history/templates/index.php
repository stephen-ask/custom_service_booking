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
      <?php esc_html_e( 'Stripe Payout History', 'service-finder' ); ?>
    </h2>
  </div>
  
  <div class="datatable-outer">
  <div class="table-responsive">
    <table id="payout-history-grid" class="table table-striped sf-table">
    <thead>
      <tr>
        <th></th>
        <th><?php esc_html_e( 'Booking Ref ID', 'service-finder' ); ?></th>
        <th><?php echo esc_html($providerreplacestring).' '.esc_html__( 'Name', 'service-finder' ); ?></th>
        <th><?php esc_html_e( 'Created on', 'service-finder' ); ?></th>
        <th><?php esc_html_e( 'Available on', 'service-finder' ); ?></th>
        <th><?php esc_html_e( 'Amount', 'service-finder' ); ?></th>
        <th><?php esc_html_e( 'Stripe Connect Method', 'service-finder' ); ?></th>
        <th><?php esc_html_e( 'Connect Account ID', 'service-finder' ); ?></th>
        <th><?php esc_html_e( 'Status', 'service-finder' ); ?></th>
      </tr>
    </thead>
    </table>
  </div>
  </div>
</div>
