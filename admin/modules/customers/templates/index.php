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
<!--Template for customers in admin panel-->
<?php
$service_finder_options = get_option('service_finder_options');
$customerreplacestring = (!empty($service_finder_options['customer-replace-string'])) ? $service_finder_options['customer-replace-string'] : esc_html__('Customers', 'service-finder');	
?>
<div class="sf-wpbody-inr">
  <div class="sedate-title">
    <h2>
      <?php echo esc_html( $customerreplacestring ); ?>
    </h2>
  </div>
  <div class="datatable-outer">
  <div class="table-responsive">
    <table id="customers-grid" class="table table-striped sf-table">
    <thead>
      <tr>
        <th></th>
        <th><input type="checkbox"  id="bulkCustomersDelete"  />
          <button id="deleteCustomersTriger" class="btn btn-danger btn-xs">
          <i class="fa fa-trash-o"></i>
          </button></th>
        <th><?php esc_html_e( 'Name', 'service-finder' ); ?></th>
        <th><?php esc_html_e( 'Phone', 'service-finder' ); ?></th>
        <th><?php esc_html_e( 'Email', 'service-finder' ); ?></th>
        <th><?php esc_html_e( 'City', 'service-finder' ); ?></th>
        <th><?php esc_html_e( 'Action', 'service-finder' ); ?></th>
      </tr>
    </thead>
    </table>
  </div>
  </div>
</div>
