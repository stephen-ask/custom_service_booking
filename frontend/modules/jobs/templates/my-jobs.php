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
$wpdb = service_finder_plugin_global_vars('wpdb');
$service_finder_Tables = service_finder_plugin_global_vars('service_finder_Tables');

wp_add_inline_script( 'service_finder-js-job-form', '/*Declare global variable*/
var user_id = "'.$globalproviderid.'";', 'after' );

$currUser = wp_get_current_user(); 
?>
<div class="panel panel-default">
<div class="panel-heading sf-panel-heading">
  <h3 class="panel-tittle m-a0"><span class="fa fa-briefcase"></span> <?php echo (!empty($service_finder_options['label-my-jobs'])) ? esc_html($service_finder_options['label-my-jobs']) : esc_html__('My Jobs', 'service-finder'); ?> </h3>
</div>
<div class="panel-body sf-panel-body padding-30">
  <!--Display Applied Jobs into datatable-->
  <table id="jobs-grid" class="table table-striped table-bordered">
    <thead>
      <tr>
        <th><?php esc_html_e('Title', 'service-finder'); ?></th>
        <th><?php esc_html_e('Expires', 'service-finder'); ?></th>
        <th><?php esc_html_e('Hiring Status', 'service-finder'); ?></th>
        <th><?php esc_html_e('Invoice ID', 'service-finder'); ?></th>
        <th><?php esc_html_e('Booking Status', 'service-finder'); ?></th>
        <th><?php esc_html_e('Payment Status', 'service-finder'); ?></th>
        <th><?php esc_html_e('Action', 'service-finder'); ?></th>
      </tr>
    </thead>
  </table>
  
  <form method="post" class="applyforjobedit default-hidden" id="applyforjobedit">
  <div class="modal-body clearfix row">
  <div class="row">
    <div class="col-md-12">
      <div class="form-group">
        <div class="input-group"> <span class="input-group-addon"><?php echo service_finder_currencysymbol(); ?></span>
          <input name="costing" readonly="readonly" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Cost', 'service-finder'); ?>">
        </div>
      </div>
    </div>
    <div class="col-md-12">
      <div class="form-group">
        <div class="input-group"> <span class="input-group-addon v-align-t"><i class="fa fa-pencil"></i></span>
          <textarea name="description" readonly="readonly" rows="4" class="form-control sf-form-control" placeholder="<?php esc_html_e('Description', 'service-finder'); ?>"></textarea>
        </div>
      </div>
    </div>
    </div>
    <div class="modal-footer">
    <div class="col-md-12">
	  <button type="button" class="btn btn-default" data-dismiss="modal">
      <?php esc_html_e('Close', 'service-finder'); ?>
      </button>
      <input type="hidden" name="jobid" value="">
    </div>
    </div>
  </div>
</form>
</div>
</div>