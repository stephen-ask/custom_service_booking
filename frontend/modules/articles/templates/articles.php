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
wp_enqueue_script('service_finder-js-articles');
wp_add_inline_script( 'service_finder-js-articles', '/*Declare global variable*/
var user_id = "'.$globalproviderid.'";', 'after' );

$url = str_replace('/','\/',$service_finder_Params['homeUrl']);
$adminajaxurl = str_replace('/','\/',admin_url('admin-ajax.php'));
?>
<div class="panel panel-default">
<div class="panel-heading sf-panel-heading">
  <h3 class="panel-tittle m-a0"><span class="fa fa-files-o"></span> <?php echo (!empty($service_finder_options['label-articles'])) ? esc_html($service_finder_options['label-articles']) : esc_html__('Articles', 'service-finder'); ?> </h3>
</div>
<div class="panel-body sf-panel-body padding-30">
  <div class="margin-b-30 text-right">
    <button class="btn btn-primary" data-toggle="modal" data-target="#addarticles" type="button"><i class="fa fa-plus"></i>
    <?php esc_html_e('ADD ARTICLES', 'service-finder'); ?>
    </button>
  </div>
  <!--Display articles template-->
  <table id="articles-grid" class="table table-striped table-bordered">
    <thead>
      <tr>
        <th> <div class="checkbox sf-radio-checkbox">
            <input type="checkbox" id="bulkArticlesDelete">
            <label for="bulkArticlesDelete"></label>
          </div>
          <button class="btn btn-danger btn-xs" id="deleteArticlesTriger" title="<?php esc_html_e('Delete', 'service-finder'); ?>"><i class="fa fa-trash-o"></i></button></th>
        <th><?php esc_html_e('Article Title', 'service-finder'); ?></th>
        <th><?php esc_html_e('Action', 'service-finder'); ?></th>
      </tr>
    </thead>
  </table>
  <!-- Add articles modal popup box -->
  <div id="addarticles" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="post" class="add-articles">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">
              <?php esc_html_e('Add New Article', 'service-finder'); ?>
            </h4>
          </div>
          <div class="modal-body clearfix row input_fields_wrap">
            <div class="col-md-12">
              <div class="form-group">
                <input type="text" class="form-control sf-form-control" name="article_title" placeholder="<?php esc_html_e('Article Title', 'service-finder'); ?>">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <?php 
				$settings = array('media_buttons' => true);
				wp_editor('', 'article_description', $settings); 
				?> 
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <?php echo service_finder_category_dropdown('sf_article_category'); ?>
              </div>
            </div>
            <div class="col-md-12">
              <div class="rwmb-field rwmb-plupload_image-wrapper">
                <div class="rwmb-input">
                  <ul class="rwmb-images rwmb-uploaded" data-field_id="articlefeatureduploader" data-delete_nonce="" data-reorder_nonce="" data-force_delete="0" data-max_file_uploads="1">
                  </ul>
                  <div id="articlefeatured-dragdrop" class="RWMB-drag-drop drag-drop hide-if-no-js new-files" data-upload_nonce="1f7575f6fa" data-js_options="{&quot;runtimes&quot;:&quot;html5,silverlight,flash,html4&quot;,&quot;file_data_name&quot;:&quot;async-upload&quot;,&quot;browse_button&quot;:&quot;articlefeatured-browse-button&quot;,&quot;drop_element&quot;:&quot;articlefeatured-dragdrop&quot;,&quot;multiple_queues&quot;:true,&quot;max_file_size&quot;:&quot;8388608b&quot;,&quot;url&quot;:&quot;<?php echo esc_url($adminajaxurl); ?>&quot;,&quot;flash_swf_url&quot;:&quot;<?php echo esc_url($url); ?>wp-includes\/js\/plupload\/plupload.flash.swf&quot;,&quot;silverlight_xap_url&quot;:&quot;<?php echo esc_url($url); ?>wp-includes\/js\/plupload\/plupload.silverlight.xap&quot;,&quot;multipart&quot;:true,&quot;urlstream_upload&quot;:true,&quot;filters&quot;:[{&quot;title&quot;:&quot;Allowed  Files&quot;,&quot;extensions&quot;:&quot;jpg,jpeg,gif,png&quot;}],&quot;multipart_params&quot;:{&quot;field_id&quot;:&quot;articlefeatured&quot;,&quot;action&quot;:&quot;articlefeatured_upload&quot;}}">
                    <div class = "drag-drop-inside text-center">
                      <p class="drag-drop-info"><?php esc_html_e('Drop files here', 'service-finder'); ?></p>
                      <p><?php esc_html_e('or', 'service-finder'); ?></p>
                      <p class="drag-drop-buttons">
                        <input id="articlefeatured-browse-button" type="button" value="<?php esc_html_e('Select Files', 'service-finder'); ?>" class="button btn btn-default" />
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
            <input type="submit" class="btn btn-primary" name="add-articles" value="<?php esc_html_e('Save', 'service-finder'); ?>" />
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- Modal END-->
  
  <!-- Edit Article Modal Popup Box-->
  <form method="post" class="edit-article default-hidden" id="editarticle">
      <div class="clearfix row input_fields_wrap">
      <div class="col-md-12">
              <div class="form-group">
                <input type="text" class="form-control sf-form-control" name="article_title" placeholder="<?php esc_html_e('Article Title', 'service-finder'); ?>">
              </div>
            </div>
      <div class="col-md-12">
              <div class="form-group">
                <?php echo service_finder_add_media_button(); ?>
                <textarea id="edit_article_description" name="edit_article_description"></textarea>
              </div>
            </div>
      <div class="col-md-12">
              <div class="form-group">
                <?php echo service_finder_category_dropdown('sf_article_category'); ?>
              </div>
            </div>
      <div class="col-md-12">
              <div class="rwmb-field rwmb-plupload_image-wrapper">
                <div class="rwmb-input">
                  <ul class="rwmb-images rwmb-uploaded" data-field_id="articlefeatureedituploader" data-delete_nonce="" data-reorder_nonce="" data-force_delete="0" data-max_file_uploads="1">
                  </ul>
                  <div id="articlefeatureedit-dragdrop" class="RWMB-drag-drop drag-drop hide-if-no-js new-files" data-upload_nonce="1f7575f6fa" data-js_options="{&quot;runtimes&quot;:&quot;html5,silverlight,flash,html4&quot;,&quot;file_data_name&quot;:&quot;async-upload&quot;,&quot;browse_button&quot;:&quot;articlefeatureedit-browse-button&quot;,&quot;drop_element&quot;:&quot;articlefeatureedit-dragdrop&quot;,&quot;multiple_queues&quot;:true,&quot;max_file_size&quot;:&quot;8388608b&quot;,&quot;url&quot;:&quot;<?php echo esc_url($adminajaxurl); ?>&quot;,&quot;flash_swf_url&quot;:&quot;<?php echo esc_url($url); ?>wp-includes\/js\/plupload\/plupload.flash.swf&quot;,&quot;silverlight_xap_url&quot;:&quot;<?php echo esc_url($url); ?>wp-includes\/js\/plupload\/plupload.silverlight.xap&quot;,&quot;multipart&quot;:true,&quot;urlstream_upload&quot;:true,&quot;filters&quot;:[{&quot;title&quot;:&quot;Allowed  Files&quot;,&quot;extensions&quot;:&quot;jpg,jpeg,gif,png&quot;}],&quot;multipart_params&quot;:{&quot;field_id&quot;:&quot;articlefeatureedit&quot;,&quot;action&quot;:&quot;articlefeatureedit_upload&quot;}}">
                    <div class = "drag-drop-inside text-center">
                      <p class="drag-drop-info"><?php esc_html_e('Drop files here', 'service-finder'); ?></p>
                      <p><?php esc_html_e('or', 'service-finder'); ?></p>
                      <p class="drag-drop-buttons">
                        <input id="articlefeatureedit-browse-button" type="button" value="<?php esc_html_e('Select Files', 'service-finder'); ?>" class="button btn btn-default" />
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
      <input type="hidden" name="articleid">
      <input type="submit" class="btn btn-primary" name="edit-article" value="<?php esc_html_e('Update Article', 'service-finder'); ?>" />
    </div>
  </form>
</div>
</div>

