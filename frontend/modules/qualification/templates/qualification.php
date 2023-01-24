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
wp_enqueue_script('service_finder-js-qualification');
wp_add_inline_script( 'service_finder-js-qualification', '/*Declare global variable*/
var user_id = "'.$globalproviderid.'";', 'after' );
?>
<div class="panel panel-default">
<div class="panel-heading sf-panel-heading">
  <h3 class="panel-tittle m-a0"><span class="fa fa-graduation-cap"></span> <?php echo (!empty($service_finder_options['label-qualification'])) ? esc_html($service_finder_options['label-qualification']) : esc_html__('Qualification', 'service-finder'); ?> </h3>
</div>
<div class="panel-body sf-panel-body padding-30">
  <div class="margin-b-30 text-right">
    <button class="btn btn-primary" data-toggle="modal" data-target="#addqualification" type="button"><i class="fa fa-plus"></i>
    <?php esc_html_e('ADD QUALIFICATION', 'service-finder'); ?>
    </button>
  </div>
  <!--Display articles template-->
  <table id="qualification-grid" class="table table-striped table-bordered">
    <thead>
      <tr>
        <th> <div class="checkbox sf-radio-checkbox">
            <input type="checkbox" id="bulkQualificationDelete">
            <label for="bulkQualificationDelete"></label>
          </div>
          <button class="btn btn-danger btn-xs" id="deleteQualificationTriger" title="<?php esc_html_e('Delete', 'service-finder'); ?>"><i class="fa fa-trash-o"></i></button></th>
        <th><?php esc_html_e('Degree Name', 'service-finder'); ?></th>
        <th><?php esc_html_e('Institute Name', 'service-finder'); ?></th>
        <th><?php esc_html_e('Years', 'service-finder'); ?></th>
        <th><?php esc_html_e('Action', 'service-finder'); ?></th>
      </tr>
    </thead>
  </table>
  <!-- Add articles modal popup box -->
  <div id="addqualification" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="post" class="add-qualification">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">
              <?php esc_html_e('Add New Qualification', 'service-finder'); ?>
            </h4>
          </div>
          <div class="modal-body clearfix row input_fields_wrap">
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" class="form-control sf-form-control" name="degree_name" placeholder="<?php esc_html_e('Degree Name', 'service-finder'); ?>">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" class="form-control sf-form-control" name="institute_name" placeholder="<?php esc_html_e('Institute Name', 'service-finder'); ?>">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <select class="sf-select-box form-control sf-form-control" name="from_year" data-live-search="true" title="<?php esc_html_e('From Year', 'service-finder'); ?>">
                <option value=""><?php esc_html_e('Select From Year', 'service-finder'); ?></option>
                <?php
				$currentyear = date('Y');
				$toyear = $currentyear - 100;
                for($i = $currentyear; $i >= $toyear; $i--){
                    echo '<option value="'.esc_attr($i).'">'.esc_html($i).'</option>';	
                }
                ?>
              </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <select class="sf-select-box form-control sf-form-control" name="to_year" data-live-search="true" title="<?php esc_html_e('To Year', 'service-finder'); ?>">
                <option value=""><?php esc_html_e('Select To Year', 'service-finder'); ?></option>
                <?php
				$currentyear = date('Y');
				$toyear = $currentyear - 100;
                for($i = $currentyear; $i >= $toyear; $i--){
                    echo '<option value="'.esc_attr($i).'">'.esc_html($i).'</option>';	
                }
                ?>
              </select>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <textarea id="degree_description" class="form-control sf-form-control" name="description" placeholder="<?php esc_html_e('Description', 'service-finder'); ?>"></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">
            <?php esc_html_e('Cancel', 'service-finder'); ?>
            </button>
            <input type="submit" class="btn btn-primary" name="add-qualification" value="<?php esc_html_e('Save', 'service-finder'); ?>" />
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- Modal END-->
  
  <!-- Edit Article Modal Popup Box-->
  <form method="post" class="edit-qualification default-hidden" id="editqualification">
      <div class="clearfix row input_fields_wrap">
      <div class="col-md-6">
              <div class="form-group">
                <input type="text" class="form-control sf-form-control" name="degree_name" placeholder="<?php esc_html_e('Degree Name', 'service-finder'); ?>">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" class="form-control sf-form-control" name="institute_name" placeholder="<?php esc_html_e('Institute Name', 'service-finder'); ?>">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <select class="sf-select-box form-control sf-form-control" name="from_year" data-live-search="true" title="<?php esc_html_e('From Year', 'service-finder'); ?>">
                <option value=""><?php esc_html_e('Select From Year', 'service-finder'); ?></option>
                <?php
				$currentyear = date('Y');
				$toyear = $currentyear - 100;
                for($i = $currentyear; $i >= $toyear; $i--){
                    echo '<option value="'.esc_attr($i).'">'.esc_html($i).'</option>';	
                }
                ?>
              </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <select class="sf-select-box form-control sf-form-control" name="to_year" data-live-search="true" title="<?php esc_html_e('To Year', 'service-finder'); ?>">
                <option value=""><?php esc_html_e('Select To Year', 'service-finder'); ?></option>
                <?php
				$currentyear = date('Y');
				$toyear = $currentyear - 100;
                for($i = $currentyear; $i >= $toyear; $i--){
                    echo '<option value="'.esc_attr($i).'">'.esc_html($i).'</option>';	
                }
                ?>
              </select>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <textarea id="edit_degree_description" class="form-control sf-form-control" name="description" placeholder="<?php esc_html_e('Description', 'service-finder'); ?>"></textarea>
              </div>
            </div>
      </div>      
      <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">
      <?php esc_html_e('Cancel', 'service-finder'); ?>
      </button>
      <input type="hidden" name="qualificationid">
      <input type="submit" class="btn btn-primary" name="edit-qualification" value="<?php esc_html_e('Update Qualification', 'service-finder'); ?>" />
    </div>
  </form>
</div>
</div>