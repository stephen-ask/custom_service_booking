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
$customerreplacestring = (!empty($service_finder_options['customer-replace-string'])) ? $service_finder_options['customer-replace-string'] : esc_html__('Customer', 'service-finder');
?>
<div class="sf-wpbody-inr">
  <div class="sedate-title">
    <h2>
      <?php esc_html_e( 'Quotations', 'service-finder' ); ?>
    </h2>
  </div>
  
  <div class="datatable-outer">
      <div class="table-responsive">
        <table id="quotations-grid" class="table table-striped sf-table">
        <thead>
          <tr>
            <th></th>
            <th><input type="checkbox"  id="bulkAdminQuoteDelete"  />
              <button id="deleteAdminQuoteTriger" class="btn btn-danger btn-xs">
              <i class="fa fa-trash-o"></i>
            </button></th>
            <th><?php echo esc_html($providerreplacestring).' '.esc_html__( 'Name', 'service-finder' ); ?></th>
            <th><?php echo esc_html($customerreplacestring).' '.esc_html__( 'Name', 'service-finder' ); ?></th>
            <th><?php esc_html_e( 'Date', 'service-finder' ); ?></th>
            <th><?php esc_html_e( 'Email', 'service-finder' ); ?></th>
            <th><?php esc_html_e( 'Phone', 'service-finder' ); ?></th>
            <th><?php esc_html_e( 'Attachments', 'service-finder' ); ?></th>
            <th><?php esc_html_e( 'Message', 'service-finder' ); ?></th>
            <th><?php esc_html_e( 'Status', 'service-finder' ); ?></th>
          </tr>
        </thead>
        </table>
      </div>
  </div>
</div>
