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
$claimbusiness = (!empty($service_finder_options['string-claim-business'])) ? $service_finder_options['string-claim-business'] : esc_html__('Claim Business', 'service-finder');
$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('Provider', 'service-finder');
$customerreplacestring = (!empty($service_finder_options['customer-replace-string'])) ? $service_finder_options['customer-replace-string'] : esc_html__('Customers', 'service-finder');	
?>
<div class="sf-wpbody-inr">
  <div class="sedate-title">
    <h2>
      <?php echo esc_html($claimbusiness); ?>
    </h2>
  </div>
  
  <div class="datatable-outer">
  <div class="table-responsive">
    <table id="claim-grid" class="table table-striped sf-table">
    <thead>
      <tr>
      	<th></th>
        <th><input type="checkbox"  id="bulkAdminClaimDelete"  />
          <button id="deleteAdminClaimTriger" class="btn btn-danger btn-xs">
          <i class="fa fa-trash-o"></i>
        </button></th>
        <th><?php echo esc_html($providerreplacestring).' '.esc_html__( 'Name', 'service-finder' ); ?></th>
        <th><?php echo esc_html($customerreplacestring).' '.esc_html__( 'Name', 'service-finder' ); ?></th>
        <th><?php esc_html_e( 'Date', 'service-finder' ); ?></th>
        <th><?php esc_html_e( 'Email', 'service-finder' ); ?></th>
        <th><?php esc_html_e( 'Message', 'service-finder' ); ?></th>
        <th><?php esc_html_e( 'Payment Status', 'service-finder' ); ?></th>
        <th><?php esc_html_e( 'Claim Status', 'service-finder' ); ?></th>
        <th><?php esc_html_e( 'Action', 'service-finder' ); ?></th>
      </tr>
    </thead>
    </table>
  </div>
  </div>
</div>
