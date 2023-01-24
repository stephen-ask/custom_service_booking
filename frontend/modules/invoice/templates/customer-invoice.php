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
$currUser = wp_get_current_user(); 
$service_finder_options = get_option('service_finder_options');

wp_add_inline_script( 'service_finder-js-invoice-form', '/*Declare global variable*/
var user_id = "'.$currUser->ID.'";', 'after' );

wp_add_inline_script( 'service_finder-js-bookings-form', '/*Declare global variable*/
var user_id = "'.$currUser->ID.'";', 'after' );

require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/invoice/Invoice.php';
$currUser = wp_get_current_user();
$invoiceData = new SERVICE_FINDER_Invoice();
$customers = $invoiceData->service_finder_getCustomers($currUser->ID);
$services = service_finder_getAllServices($currUser->ID)
?>
<div class="panel panel-default">
<div class="panel-heading sf-panel-heading">
  <h3 class="panel-tittle m-a0"><span class="fa fa-file-text-o"></span> <?php esc_html_e('Invoice', 'service-finder'); ?> </h3>
</div>
<div class="panel-body sf-panel-body padding-30">
  <!--Display Invoice Datatable-->
  <table id="invoice-grid" class="table table-striped margin-0 booking-listing" data-bookingid="<?php echo (!empty($_GET['bookingid'])) ? esc_attr($_GET['bookingid']) : ''; ?>">
    <thead>
      <tr>
        <th> <?php esc_html_e('Reference No', 'service-finder'); ?></th>
        <th> <?php esc_html_e('Provider Name', 'service-finder'); ?></th>
        <th> <?php esc_html_e('Due Date', 'service-finder'); ?></th>
        <th> <?php esc_html_e('Amount', 'service-finder'); ?></th>
        <th> <?php esc_html_e('Status', 'service-finder'); ?></th>
        <th> <?php esc_html_e('Txn ID', 'service-finder'); ?></th>
        <th> <?php esc_html_e('Action', 'service-finder'); ?></th>
      </tr>
    </thead>
  </table>
  <!--Display Invoice Details-->
  <div id="invoice-details" class="hidden"> </div>
</div>
</div>
