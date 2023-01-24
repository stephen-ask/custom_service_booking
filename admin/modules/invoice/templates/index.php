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
$customerreplacestring = (!empty($service_finder_options['customer-replace-string'])) ? $service_finder_options['customer-replace-string'] : esc_html__('Customers', 'service-finder');	
$admin_fee_label = (!empty($service_finder_options['admin-fee-label'])) ? esc_html($service_finder_options['admin-fee-label']) : esc_html__('Admin Fee', 'service-finder');

$bookingid = (isset($_GET['bookingid'])) ? esc_html($_GET['bookingid']) : '';

wp_add_inline_script( 'service_finder-js-invoice-requests', '/*Declare global variable*/
var formtype = "signup"; var bookingid = "'.$bookingid.'";', 'after' );
?>
<div class="sf-wpbody-inr">
  <div class="sedate-title">
    <h2>
      <?php esc_html_e( 'Invoices', 'service-finder' ); ?>
    </h2>
  </div>
  <div class="sf-by-provider"> <?php echo esc_html__( 'By', 'service-finder' ).' '.esc_html($providerreplacestring); ?> -
    <select class="sf-select-box form-control sf-form-control" name="byproviderinvoice" id="byproviderinvoice">
      <?php
if(!empty($args)){
	echo '<option value="">'.esc_html__( 'All ', 'service-finder' ).esc_html($providerreplacestring).'</option>';
	foreach($args as $arg){
	echo '<option value="'.esc_attr($arg->full_name).'">'.$arg->full_name.'</option>';
	}
}else{
	echo '<option value="">'.esc_html__( 'No Providers Found', 'service-finder' ).'</option>';
}
?>
    </select>
  </div>
  <div class="datatable-outer">
  <div class="table-responsive">
    <table id="invoice-requests-grid" class="table table-striped sf-table">
    <thead>
      <tr>
      	<th></th>
        <th><input type="checkbox"  id="bulkAdminInvoiceDelete"  />
          <button id="deleteAdminInvoiceTriger" class="btn btn-danger btn-xs">
          <i class="fa fa-trash-o"></i>
        </button></th>
        <th><?php esc_html_e('Reference No', 'service-finder'); ?></th>
        <th><?php echo esc_html($providerreplacestring).' '.esc_html__( 'Name', 'service-finder' ); ?></th>
        <th><?php echo esc_html($customerreplacestring).' '.esc_html__( 'Name', 'service-finder' ); ?></th>
        <th><?php esc_html_e('Due Date', 'service-finder'); ?></th>
        <th><?php esc_html_e('Total Amount', 'service-finder'); ?></th>
        <th><?php esc_html_e('Invoice Status', 'service-finder'); ?></th>
        <th><?php esc_html_e('Booking ID', 'service-finder' ); ?></th>
        <th><?php echo sprintf( esc_html__('Pay to %s via Bank Transffer', 'service-finder'), $providerreplacestring ); ?></th>
        <th><?php echo sprintf( esc_html__('Pay to %s via Paypal/Stripe/Mangopay', 'service-finder'), $providerreplacestring ); ?></th>
        <th><?php esc_html_e('Action', 'service-finder' ); ?></th>
      </tr>
    </thead>
    </table>
  </div>
  </div>
</div>

<!--Load Booking Details-->
<div class="modal fade" id="invoice-booking-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal-title">
          <?php esc_html_e('Booking Details', 'service-finder'); ?>
        </h3>
      </div>
      <div class="modal-body"> 
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">
        <?php esc_html_e('Close', 'service-finder'); ?>
        </button>
      </div>
    </div>
  </div>
</div>
<!-- Loading area start -->
<div class="loading-area default-hidden">
  <div class="loading-box"></div>
  <div class="loading-pic"></div>
</div>
<!-- Loading area end -->
