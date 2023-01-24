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
wp_enqueue_script('service_finder-js-certificates');
wp_add_inline_script( 'service_finder-js-certificates', '/*Declare global variable*/
var user_id = "'.$globalproviderid.'";', 'after' );

$url = str_replace('/','\/',$service_finder_Params['homeUrl']);
$adminajaxurl = str_replace('/','\/',admin_url('admin-ajax.php'));
?>
<div class="panel panel-default">
<div class="panel-heading sf-panel-heading">
  <h3 class="panel-tittle m-a0"><span class="fa fa-shield"></span> <?php echo (!empty($service_finder_options['label-certificates'])) ? esc_html($service_finder_options['label-certificates']) : esc_html__('Certificates', 'service-finder'); ?> </h3>
</div>
<div class="panel-body sf-panel-body padding-30">
  <div class="margin-b-30 text-right">
    <button class="btn btn-primary" data-toggle="modal" data-target="#addcertificates" type="button"><i class="fa fa-plus"></i>
    <?php esc_html_e('ADD CERTIFICATES', 'service-finder'); ?>
    </button>
  </div>
  <!--Display articles template-->
  <table id="certificates-grid" class="table table-striped table-bordered">
    <thead>
      <tr>
        <th> <div class="checkbox sf-radio-checkbox">
            <input type="checkbox" id="bulkCertificatesDelete">
            <label for="bulkCertificatesDelete"></label>
          </div>
          <button class="btn btn-danger btn-xs" id="deleteCertificatesTriger" title="<?php esc_html_e('Delete', 'service-finder'); ?>"><i class="fa fa-trash-o"></i></button></th>
        <th><?php esc_html_e('Certificate Title', 'service-finder'); ?></th>
        <th><?php esc_html_e('Issue Date', 'service-finder'); ?></th>
        <th><?php esc_html_e('Action', 'service-finder'); ?></th>
      </tr>
    </thead>
  </table>
  <!-- Add articles modal popup box -->
  <div id="addcertificates" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="post" class="add-certificates">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">
              <?php esc_html_e('Add New Certificates', 'service-finder'); ?>
            </h4>
          </div>
          <div class="modal-body clearfix row input_fields_wrap">
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" class="form-control sf-form-control" name="certificate_title" placeholder="<?php esc_html_e('Certificate Title', 'service-finder'); ?>">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" class="form-control sf-form-control certificate_issue_date" name="issue_date" placeholder="<?php esc_html_e('Date', 'service-finder'); ?>">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <textarea id="certificate_description" class="form-control sf-form-control" name="description" placeholder="<?php esc_html_e('Description', 'service-finder'); ?>"></textarea>
              </div>
            </div>
            <div class="col-md-12">
              <div class="rwmb-field rwmb-plupload_image-wrapper">
                <div class="rwmb-input">
                  <ul class="rwmb-images rwmb-uploaded" data-field_id="certificateuploader" data-delete_nonce="" data-reorder_nonce="" data-force_delete="0" data-max_file_uploads="1">
                  </ul>
                  <div id="certificate-dragdrop" class="RWMB-drag-drop drag-drop hide-if-no-js new-files" data-upload_nonce="1f7575f6fa" data-js_options="{&quot;runtimes&quot;:&quot;html5,silverlight,flash,html4&quot;,&quot;file_data_name&quot;:&quot;async-upload&quot;,&quot;browse_button&quot;:&quot;certificate-browse-button&quot;,&quot;drop_element&quot;:&quot;certificate-dragdrop&quot;,&quot;multiple_queues&quot;:true,&quot;max_file_size&quot;:&quot;8388608b&quot;,&quot;url&quot;:&quot;<?php echo esc_url($adminajaxurl); ?>&quot;,&quot;flash_swf_url&quot;:&quot;<?php echo esc_url($url); ?>wp-includes\/js\/plupload\/plupload.flash.swf&quot;,&quot;silverlight_xap_url&quot;:&quot;<?php echo esc_url($url); ?>wp-includes\/js\/plupload\/plupload.silverlight.xap&quot;,&quot;multipart&quot;:true,&quot;urlstream_upload&quot;:true,&quot;filters&quot;:[{&quot;title&quot;:&quot;Allowed  Files&quot;,&quot;extensions&quot;:&quot;doc,docx,jpg,jpeg,png,gif,pdf,xls,xlsx,rtf,txt,ppt,pptx&quot;}],&quot;multipart_params&quot;:{&quot;field_id&quot;:&quot;certificate&quot;,&quot;action&quot;:&quot;certificate_upload&quot;}}">
                    <div class = "drag-drop-inside text-center">
                      <p class="drag-drop-info"><?php esc_html_e('Drop files here', 'service-finder'); ?></p>
                      <p><?php esc_html_e('or', 'service-finder'); ?></p>
                      <p class="drag-drop-buttons">
                        <input id="certificate-browse-button" type="button" value="<?php esc_html_e('Select Files', 'service-finder'); ?>" class="button btn btn-default" />
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">
            <?php esc_html_e('Cancel', 'service-finder'); ?>
            </button>
            <input type="submit" class="btn btn-primary" name="add-certificates" value="<?php esc_html_e('Save', 'service-finder'); ?>" />
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- Modal END-->
  
  <!-- Edit Article Modal Popup Box-->
  <form method="post" class="edit-certificates default-hidden" id="editcertificates">
      <div class="clearfix row input_fields_wrap">
      <div class="col-md-6">
              <div class="form-group">
                <input type="text" class="form-control sf-form-control" name="certificate_title" placeholder="<?php esc_html_e('Certificate Title', 'service-finder'); ?>">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" class="form-control sf-form-control certificate_issue_date" name="issue_date" placeholder="<?php esc_html_e('Date', 'service-finder'); ?>">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <textarea id="edit_certificate_description" class="form-control sf-form-control" name="description" placeholder="<?php esc_html_e('Description', 'service-finder'); ?>"></textarea>
              </div>
            </div>
            <div class="col-md-12">
              <div class="rwmb-field rwmb-plupload_image-wrapper">
                <div class="rwmb-input">
                  <ul class="rwmb-images rwmb-uploaded" data-field_id="certificateedituploader" data-delete_nonce="" data-reorder_nonce="" data-force_delete="0" data-max_file_uploads="1">
                  </ul>
                  <div id="certificateedit-dragdrop" class="RWMB-drag-drop drag-drop hide-if-no-js new-files" data-upload_nonce="1f7575f6fa" data-js_options="{&quot;runtimes&quot;:&quot;html5,silverlight,flash,html4&quot;,&quot;file_data_name&quot;:&quot;async-upload&quot;,&quot;browse_button&quot;:&quot;certificateedit-browse-button&quot;,&quot;drop_element&quot;:&quot;certificateedit-dragdrop&quot;,&quot;multiple_queues&quot;:true,&quot;max_file_size&quot;:&quot;8388608b&quot;,&quot;url&quot;:&quot;<?php echo esc_url($adminajaxurl); ?>&quot;,&quot;flash_swf_url&quot;:&quot;<?php echo esc_url($url); ?>wp-includes\/js\/plupload\/plupload.flash.swf&quot;,&quot;silverlight_xap_url&quot;:&quot;<?php echo esc_url($url); ?>wp-includes\/js\/plupload\/plupload.silverlight.xap&quot;,&quot;multipart&quot;:true,&quot;urlstream_upload&quot;:true,&quot;filters&quot;:[{&quot;title&quot;:&quot;Allowed  Files&quot;,&quot;extensions&quot;:&quot;doc,docx,jpg,jpeg,png,gif,pdf,xls,xlsx,rtf,txt,ppt,pptx&quot;}],&quot;multipart_params&quot;:{&quot;field_id&quot;:&quot;certificateedit&quot;,&quot;action&quot;:&quot;certificateedit_upload&quot;}}">
                    <div class = "drag-drop-inside text-center">
                      <p class="drag-drop-info"><?php esc_html_e('Drop files here', 'service-finder'); ?></p>
                      <p><?php esc_html_e('or', 'service-finder'); ?></p>
                      <p class="drag-drop-buttons">
                        <input id="certificateedit-browse-button" type="button" value="<?php esc_html_e('Select Files', 'service-finder'); ?>" class="button btn btn-default" />
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
      </div>      
      <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">
      <?php esc_html_e('Cancel', 'service-finder'); ?>
      </button>
      <input type="hidden" name="certificatesid">
      <input type="submit" class="btn btn-primary" name="edit-certificates" value="<?php esc_html_e('Update Certificates', 'service-finder'); ?>" />
    </div>
  </form>
</div>
</div>