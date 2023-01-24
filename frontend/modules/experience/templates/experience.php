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
wp_enqueue_script('service_finder-js-experience');
wp_add_inline_script( 'service_finder-js-experience', '/*Declare global variable*/
var user_id = "'.$globalproviderid.'";', 'after' );
?>
<div class="panel panel-default">
<div class="panel-heading sf-panel-heading">
  <h3 class="panel-tittle m-a0"><span class="fa fa-black-tie"></span> <?php echo (!empty($service_finder_options['label-experience'])) ? esc_html($service_finder_options['label-experience']) : esc_html__('Experience', 'service-finder'); ?> </h3>
</div>
<div class="panel-body sf-panel-body padding-30">
  <div class="margin-b-30 text-right">
    <button class="btn btn-primary" data-toggle="modal" data-target="#addexperience" type="button"><i class="fa fa-plus"></i>
    <?php esc_html_e('ADD EXPERIENCE', 'service-finder'); ?>
    </button>
  </div>
  <!--Display articles template-->
  <table id="experience-grid" class="table table-striped table-bordered">
    <thead>
      <tr>
        <th> <div class="checkbox sf-radio-checkbox">
            <input type="checkbox" id="bulkExperienceDelete">
            <label for="bulkExperienceDelete"></label>
          </div>
          <button class="btn btn-danger btn-xs" id="deleteExperienceTriger" title="<?php esc_html_e('Delete', 'service-finder'); ?>"><i class="fa fa-trash-o"></i></button></th>
        <th><?php esc_html_e('Job Title', 'service-finder'); ?></th>
        <th><?php esc_html_e('Company Name', 'service-finder'); ?></th>
        <th><?php esc_html_e('Start Date', 'service-finder'); ?></th>
        <th><?php esc_html_e('End Date', 'service-finder'); ?></th>
        <th><?php esc_html_e('Current Job', 'service-finder'); ?></th>
        <th><?php esc_html_e('Action', 'service-finder'); ?></th>
      </tr>
    </thead>
  </table>
  <!-- Add articles modal popup box -->
  <div id="addexperience" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="post" class="add-experience">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">
              <?php esc_html_e('Add New Experience', 'service-finder'); ?>
            </h4>
          </div>
          <div class="modal-body clearfix row input_fields_wrap">
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" class="form-control sf-form-control" name="job_title" placeholder="<?php esc_html_e('Job Title', 'service-finder'); ?>">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" class="form-control sf-form-control" name="company_name" placeholder="<?php esc_html_e('Company Name', 'service-finder'); ?>">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" class="form-control sf-form-control job_period_date" name="start_date" placeholder="<?php esc_html_e('Joining Date', 'service-finder'); ?>">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" class="form-control sf-form-control job_period_date" name="end_date" placeholder="<?php esc_html_e('End Date', 'service-finder'); ?>">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <textarea id="job_description" class="form-control sf-form-control" name="job_description" placeholder="<?php esc_html_e('Job Description', 'service-finder'); ?>"></textarea>
              </div>
            </div>
            <div class="col-md-6">
          <div class="form-group">
            <div class="checkbox sf-radio-checkbox">
                <input type="checkbox" value="yes" name="current_job" id="current_job">
                <label for="current_job">
                <?php esc_html_e('Current Job', 'service-finder'); ?>
                </label>
              </div>
          </div>
        </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">
            <?php esc_html_e('Cancel', 'service-finder'); ?>
            </button>
            <input type="submit" class="btn btn-primary" name="add-experience" value="<?php esc_html_e('Save', 'service-finder'); ?>" />
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- Modal END-->
  
  <!-- Edit Article Modal Popup Box-->
  <form method="post" class="edit-experience default-hidden" id="editexperience">
      <div class="clearfix row input_fields_wrap">
      <div class="col-md-6">
          <div class="form-group">
            <input type="text" class="form-control sf-form-control" name="job_title" placeholder="<?php esc_html_e('Job Title', 'service-finder'); ?>">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <input type="text" class="form-control sf-form-control" name="company_name" placeholder="<?php esc_html_e('Company Name', 'service-finder'); ?>">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <input type="text" class="form-control sf-form-control job_period_date" name="start_date" placeholder="<?php esc_html_e('Joining Date', 'service-finder'); ?>">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <input type="text" class="form-control sf-form-control job_period_date" name="end_date" placeholder="<?php esc_html_e('End Date', 'service-finder'); ?>">
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group">
            <textarea id="edit_job_description" class="form-control sf-form-control" name="job_description" placeholder="<?php esc_html_e('Job Description', 'service-finder'); ?>"></textarea>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <div class="checkbox sf-radio-checkbox">
                <input type="checkbox" value="yes" name="current_job" id="edit_current_job">
                <label for="edit_current_job">
                <?php esc_html_e('Current Job', 'service-finder'); ?>
                </label>
              </div>
          </div>
        </div>
      </div>      
      <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">
      <?php esc_html_e('Cancel', 'service-finder'); ?>
      </button>
      <input type="hidden" name="experienceid">
      <input type="submit" class="btn btn-primary" name="edit-experience" value="<?php esc_html_e('Update Experience', 'service-finder'); ?>" />
    </div>
  </form>
</div>
</div>
