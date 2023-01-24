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
$service_finder_options = get_option('service_finder_options');

$currUser = wp_get_current_user(); 

wp_add_inline_script( 'service_finder-js-customer-quote-form', '/*Declare global variable*/
var user_id = "'.$currUser->ID.'";', 'after' );
?>
<div class="panel panel-default">
<div class="panel-heading sf-panel-heading">
  <h3 class="panel-tittle m-a0"><span class="fa fa-check-circle-o"></span> <?php echo (!empty($service_finder_options['label-quotation'])) ? esc_html($service_finder_options['label-quotation']) : esc_html__('Quotations', 'service-finder'); ?> </h3>
</div>
<div class="panel-body sf-panel-body padding-30">
  <table id="quotation-grid" class="table table-striped margin-0 quotation-listing">
    <thead>
      <tr>
        <th><?php esc_html_e('Quote ID', 'service-finder'); ?></th>
        <th><?php esc_html_e('Date', 'service-finder'); ?></th>
        <th><?php esc_html_e('Quotation', 'service-finder'); ?></th>
        <th><?php esc_html_e('Hiring Status', 'service-finder'); ?></th>
        <th><?php esc_html_e('Assign to', 'service-finder'); ?></th>
        <th><?php esc_html_e('Action', 'service-finder'); ?></th>
      </tr>
    </thead>
  </table>
  
  <div id="quotation-details" class="hidden"> </div>
  <div id="replies-listing" class="hidden"> </div>
</div>
</div>