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
wp_add_inline_script( 'service_finder-js-provider-quote-form', '/*Declare global variable*/
var user_id = "'.$globalproviderid.'";', 'after' );
$customerreplacestring = (!empty($service_finder_options['customer-replace-string'])) ? $service_finder_options['customer-replace-string'] : esc_html__('Customer', 'service-finder');	
?>
<div class="panel panel-default">
<div class="panel-heading sf-panel-heading">
  <h3 class="panel-tittle m-a0"><span class="fa fa-money"></span> <?php echo (!empty($service_finder_options['label-quotation'])) ? esc_html($service_finder_options['label-quotation']) : esc_html__('Quotations', 'service-finder'); ?> </h3>
</div>
<div class="panel-body sf-panel-body padding-30">
  <table id="quotation-grid" class="table table-striped table-bordered">
    <thead>
      <tr>
        <th><?php esc_html_e('Quote ID', 'service-finder'); ?></th>
        <th><?php esc_html_e('Date', 'service-finder'); ?></th>
        <th><?php printf(esc_html__('%s Name', 'service-finder'),$customerreplacestring); ?></th>
        <th><?php esc_html_e('Email', 'service-finder'); ?></th>
        <th><?php esc_html_e('Phone', 'service-finder'); ?></th>
        <th><?php esc_html_e('Hiring Status', 'service-finder'); ?></th>
        <th><?php esc_html_e('Action', 'service-finder'); ?></th>
      </tr>
    </thead>
  </table>
  
  <div id="quotation-details" class="hidden"> </div>
  
  <form method="post" class="quotation-reply default-hidden" id="editquotationreply">
      <div class="clearfix row input_fields_wrap">
        <div class="col-md-12">
          <div class="form-group">
            <input type="text" class="form-control sf-form-control" name="quote_price" placeholder="<?php esc_html_e('Quote Price', 'service-finder'); ?>">
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group">
            <textarea id="quote_reply" name="quote_reply" placeholder="<?php esc_html_e('Reply', 'service-finder'); ?>"></textarea>
          </div>
        </div>
      </div>      
      <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">
      <?php esc_html_e('Cancel', 'service-finder'); ?>
      </button>
      <input type="hidden" name="quoteid">
      <input type="submit" class="btn btn-primary" name="edit-quotation" value="<?php esc_html_e('Submit', 'service-finder'); ?>" />
    </div>
  </form>
  
</div>
</div>
