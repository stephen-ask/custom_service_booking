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
<div class="panel panel-default">
<div class="panel-heading sf-panel-heading">
  <h3 class="panel-tittle m-a0"><span class="fa fa-money"></span> <?php echo (!empty($service_finder_options['label-payout-history'])) ? esc_html($service_finder_options['label-payout-history']) : esc_html__('Payout History', 'service-finder'); ?> </h3>
</div>
<div class="panel-body sf-panel-body padding-30">
  <table id="payout-history-grid" class="table table-striped table-bordered">
    <thead>
      <tr>
        <th><?php esc_html_e( 'Booking Ref ID', 'service-finder' ); ?></th>
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
