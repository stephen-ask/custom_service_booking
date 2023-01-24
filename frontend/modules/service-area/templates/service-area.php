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

$sAreas = service_finder_getAllServiceArea($globalproviderid);
if(!empty($sAreas)){
	foreach($sAreas as $sArea){
		$ziparr[] = $sArea->zipcode;
	}
	$sAreas = implode(',',$ziparr);
}
wp_add_inline_script( 'service_finder-js-servicearea-form', '/*Declare global variable*/
var user_id = "'.$globalproviderid.'";', 'after' );
?>
<div class="panel panel-default">
<div class="panel-heading sf-panel-heading">
  <h3 class="panel-tittle m-a0"><span class="fa fa-book"></span> <?php echo (!empty($service_finder_options['label-postal-codes'])) ? esc_html($service_finder_options['label-postal-codes']) : esc_html__('Postal Codes', 'service-finder'); ?> <i class="tip-info fa fa-question" data-toggle="tooltip" title="<?php echo esc_html__('If you want to restrict your services to specific postal area then you can add from here', 'service-finder'); ?>"></i></h3>
</div>
<div class="panel-body sf-panel-body padding-30">
  <div class="margin-b-30 text-right">
    <button class="btn btn-primary" data-toggle="modal" data-target="#addservicearea" type="button"><i class="fa fa-plus"></i>
    <?php esc_html_e('ADD POSTAL CODES', 'service-finder'); ?>
    </button>
  </div>
  <!--Display service area template-->
  <table id="zipcodes-grid" class="table table-striped table-bordered">
    <thead>
      <tr>
        <th> <div class="checkbox sf-radio-checkbox">
            <input type="checkbox" id="bulkZipcodeDelete">
            <label for="bulkZipcodeDelete"></label>
          </div>
          <button class="btn btn-danger btn-xs" id="deleteZipcodeTriger" title="<?php esc_html_e('Delete', 'service-finder'); ?>"><i class="fa fa-trash-o"></i></button></th>
        <th><?php esc_html_e('Postal Code', 'service-finder'); ?></th>
        <th><?php esc_html_e('Status', 'service-finder'); ?></th>
      </tr>
    </thead>
  </table>
  <!-- Add service area modal popup box -->
  <div id="addservicearea" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="post" class="add-service-area">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">
              <?php esc_html_e('Add New Postal Code', 'service-finder'); ?>
            </h4>
          </div>
          <div class="modal-body clearfix row input_fields_wrap">
            <div class="col-md-12">
              <div class="form-group">
                <textarea class="form-control sf-form-control" name="zipcode" id="areazipcode" placeholder="<?php esc_html_e('Add commas to separate postal codes', 'service-finder'); ?>" rows="" cols="4"><?php echo (!empty($sAreas)) ? $sAreas : ''; ?></textarea>
                <p><?php esc_html_e('Add commas to separate postal codes', 'service-finder'); ?></p>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">
            <?php esc_html_e('Cancel', 'service-finder'); ?>
            </button>
            <input type="submit" class="btn btn-primary" name="add-servicearea" value="<?php esc_html_e('Save', 'service-finder'); ?>" />
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- Modal END-->
</div>
</div>