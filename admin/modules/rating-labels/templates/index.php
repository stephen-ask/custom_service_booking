<?php 
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<!--Template for dispaly featured requests-->
<?php
$service_finder_options = get_option('service_finder_options');
$service_finder_Tables = service_finder_plugin_global_vars('service_finder_Tables');
?>
<div class="sf-wpbody-inr">
  <div class="sedate-title">
    <h2>
      <?php esc_html_e( 'Rating Labels', 'service-finder' ); ?>
    </h2>
  </div>
  <div class="sf-add-amenity"> 
  <button class="btn btn-primary" data-toggle="modal" data-target="#addratinglabels" type="button"><i class="fa fa-plus"></i>
    <?php echo esc_html__( 'Add/Edit Label', 'service-finder' ); ?> 
  </button>
  </div>
  <div class="datatable-outer">
  <div class="table-responsive">
    <table id="ratinglabels-grid" class="table table-striped sf-table">
    <thead>
      <tr>
        <th></th>
        <th><input type="checkbox"  id="bulkAdminRatingLabelsDelete"  />
          <button id="deleteRatingLabelsTriger" class="btn btn-danger btn-xs">
          <i class="fa fa-trash-o"></i>
        </button></th>
        <th><?php esc_html_e( 'Label Name', 'service-finder' ); ?></th>
        <th><?php esc_html_e( 'Category', 'service-finder' ); ?></th>
      </tr>
    </thead>
    </table>
  </div>
  </div>
  <!-- Add Amenity Modal Popup Box-->
  <div id="addratinglabels" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="post" class="add-new-labels">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">
              <?php esc_html_e('Add New Label', 'service-finder'); ?>
            </h4>
          </div>
          <div class="modal-body clearfix row input_fields_wrap">
            <div class="col-lg-12">
              <div class="form-group">
                <div class="input-group">
                  <select class="sf-select-box form-control sf-form-control" name="category" id="category">
                  <option value=""><?php esc_html_e( 'Select Category', 'service-finder' ); ?></option>
                  <option value="0"><?php esc_html_e( 'Default applies to all', 'service-finder' ); ?></option>
                    <?php
					if(class_exists('service_finder_texonomy_plugin')){
					$limit = 1000;
					$categories = service_finder_getCategoryList($limit);
					$texonomy = 'providers-category';
					if(!empty($categories)){
						foreach($categories as $category){
							echo '<option value="'.esc_attr($category->term_id).'">'. $category->name.'</option>';
							$term_children = get_term_children($category->term_id,$texonomy);
							if(!empty($term_children)){
								foreach($term_children as $term_child_id) {
	
									$term_child = get_term_by('id',$term_child_id,$texonomy);
									
									echo '<option value="'.esc_attr($term_child_id).'" data-content="<span class=\'childcat\'>'.esc_attr($term_child->name).'</span>">'. $term_child->name.'</option>';
									
								}
							}
						}
					}	
					}
					?>
                  </select>
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <input name="labelname" id="labelname" type="text" class="form-control" placeholder="<?php esc_html_e('Label Name', 'service-finder'); ?>">
                <span><i><?php esc_html_e( 'You can add maxium 5 lables', 'service-finder' ); ?></i></span>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <div id="labels-list">
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">
            <?php esc_html_e('Cancel', 'service-finder'); ?>
            </button>
            <input type="submit" class="btn btn-primary" name="add-group" value="<?php esc_html_e('Add Label', 'service-finder'); ?>" />
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- Loading area start -->
<div class="loading-area default-hidden">
  <div class="loading-box"></div>
  <div class="loading-pic"></div>
</div>
<!-- Loading area end -->
