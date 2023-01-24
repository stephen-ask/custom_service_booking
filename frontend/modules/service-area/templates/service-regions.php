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

$sRegions = service_finder_getAllServiceRegions($globalproviderid);
if(!empty($sRegions)){
	foreach($sRegions as $sRegion){
		$regionarr[] = $sRegion->region;
	}
	$sRegions = implode(',',$regionarr);
}
wp_add_inline_script( 'service_finder-js-servicearea-form', '/*Declare global variable*/
var user_id = "'.$globalproviderid.'";', 'before' );
?>
<div class="panel panel-default">
<div class="panel-heading sf-panel-heading">
  <h3 class="panel-tittle m-a0"><span class="fa fa-location-arrow"></span> <?php echo (!empty($service_finder_options['label-regions'])) ? esc_html($service_finder_options['label-regions']) : esc_html__('Regions', 'service-finder'); ?> <i class="tip-info fa fa-question" data-toggle="tooltip" title="<?php echo esc_html__('If you want to restrict your services to specific regions then you can add from here', 'service-finder'); ?>"></i></h3>
</div>
<div class="panel-body sf-panel-body padding-30">
  <div class="margin-b-30 text-right">
    <button class="btn btn-primary" data-toggle="modal" data-target="#addserviceregions" type="button"><i class="fa fa-plus"></i>
    <?php esc_html_e('ADD REGIONS', 'service-finder'); ?>
    </button>
  </div>
  <!--Display service area template-->
  <table id="regions-grid" class="table table-striped table-bordered">
    <thead>
      <tr>
        <th> <div class="checkbox sf-radio-checkbox">
            <input type="checkbox" id="bulkRegionsDelete">
            <label for="bulkRegionsDelete"></label>
          </div>
          <button class="btn btn-danger btn-xs" id="deleteRegionTriger" title="<?php esc_html_e('Delete', 'service-finder'); ?>"><i class="fa fa-trash-o"></i></button></th>
        <th><?php esc_html_e('Regions', 'service-finder'); ?></th>
        <th><?php esc_html_e('Status', 'service-finder'); ?></th>
      </tr>
    </thead>
  </table>
  <!-- Add service area modal popup box -->
  <div id="addserviceregions" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="post" class="add-service-region">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">
              <?php esc_html_e('Add New Region', 'service-finder'); ?>
            </h4>
          </div>
          <div class="modal-body clearfix row input_fields_wrap">
            <div class="col-md-12">
              <div class="form-group">
                <textarea class="form-control sf-form-control" name="region" id="arearegion" placeholder="<?php esc_html_e('Add comma separate regions', 'service-finder'); ?>" rows="" cols="4"><?php echo (!empty($sRegions)) ? $sRegions : ''; ?></textarea>
                <p><?php esc_html_e('Add comma separate postal regions', 'service-finder'); ?></p>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">
            <?php esc_html_e('Cancel', 'service-finder'); ?>
            </button>
            <input type="submit" class="btn btn-primary" name="add-serviceregion" value="<?php esc_html_e('Save', 'service-finder'); ?>" />
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- Modal END-->
</div>
</div>