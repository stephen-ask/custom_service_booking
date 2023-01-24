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
wp_enqueue_script('service_finder-js-offers');
wp_add_inline_script( 'service_finder-js-offers', '/*Declare global variable*/
var user_id = "'.$globalproviderid.'";', 'after' );
?>

<div class="panel panel-default">
<div class="panel-heading sf-panel-heading">
  <h3 class="panel-tittle m-a0"><span class="fa fa-money"></span> <?php echo (!empty($service_finder_options['label-offers'])) ? esc_html($service_finder_options['label-offers']) : esc_html__('Offers & Promotions', 'service-finder'); ?> </h3>
</div>
<div class="panel-body sf-panel-body padding-30">
  <div class="margin-b-30 text-right">
    <button class="btn btn-primary" data-toggle="modal" data-target="#addoffers" type="button"><i class="fa fa-plus"></i>
    <?php esc_html_e('ADD OFFER', 'service-finder'); ?>
    </button>
  </div>
  <!--Display articles template-->
  <table id="offers-grid" class="table table-striped table-bordered">
    <thead>
      <tr>
        <th> <div class="checkbox sf-radio-checkbox">
            <input type="checkbox" id="bulkOffersDelete">
            <label for="bulkOffersDelete"></label>
          </div>
          <button class="btn btn-danger btn-xs" id="deleteOffersTriger" title="<?php esc_html_e('Delete', 'service-finder'); ?>"><i class="fa fa-trash-o"></i></button></th>
        <th><?php esc_html_e('Offer Title', 'service-finder'); ?></th>
        <th><?php esc_html_e('Coupon Code', 'service-finder'); ?></th>
        <th><?php esc_html_e('Discount', 'service-finder'); ?></th>
        <th><?php esc_html_e('Expiry Date', 'service-finder'); ?></th>
        <th><?php esc_html_e('Max Coupon', 'service-finder'); ?></th>
        <th><?php esc_html_e('Action', 'service-finder'); ?></th>
      </tr>
    </thead>
  </table>
  <!-- Add articles modal popup box -->
  <div id="addoffers" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="post" class="add-offers">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">
              <?php esc_html_e('Add New Offer', 'service-finder'); ?>
            </h4>
          </div>
          <div class="modal-body clearfix row input_fields_wrap">
            <div class="col-md-12">
              <div class="form-group">
                <input name="offer_title" id="offer_title" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Title', 'service-finder'); ?>">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <input name="coupon_code" id="coupon_code" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Coupon Code', 'service-finder'); ?>">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <input name="expiry_date" id="expiry_date" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Expiry Date', 'service-finder'); ?>">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <input name="max_coupon" id="max_coupon" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Max Coupons', 'service-finder'); ?>">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group form-inline">
                <label>
                <?php esc_html_e('Discount', 'service-finder'); ?>
                </label>
                <br>
                <div class="radio sf-radio-checkbox">
                  <input id="offer_percentage" type="radio" name="discount_type" value="percentage">
                  <label for="offer_percentage">
                  <?php esc_html_e('Percentage', 'service-finder'); ?>
                  </label>
                </div>
                <div class="radio sf-radio-checkbox">
                  <input id="offer_fixed" type="radio" name="discount_type" value="fixed">
                  <label for="offer_fixed">
                  <?php esc_html_e('Fixed', 'service-finder'); ?>
                  </label>
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <input name="discount_value" id="discount_value" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Number of percentage or fixed amount', 'service-finder'); ?>">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
               	  <?php 
				  $settings = array( 
									'editor_height' => '100px',
									'textarea_name' => 'discount_description',
									'default_editor' => 'quicktags'
								);
	
				  wp_editor( '', 'discount_description', $settings );
				  ?>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">
            <?php esc_html_e('Cancel', 'service-finder'); ?>
            </button>
            <input type="submit" class="btn btn-primary" name="add-offers" value="<?php esc_html_e('Save', 'service-finder'); ?>" />
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- Modal END-->
  
  <!-- Edit Offers Modal Popup Box-->
  <form method="post" class="edit-offers default-hidden" id="editoffers">
      <div class="clearfix row input_fields_wrap">
        <div class="col-md-12">
          <div class="form-group">
            <input name="offer_title" id="edit_offer_title" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Title', 'service-finder'); ?>">
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <input name="coupon_code" id="edit_coupon_code" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Coupon Code', 'service-finder'); ?>">
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <input name="expiry_date" id="edit_expiry_date" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Expiry Date', 'service-finder'); ?>">
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            <input name="max_coupon" id="edit_max_coupon" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Max Coupons', 'service-finder'); ?>">
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group form-inline">
            <label>
            <?php esc_html_e('Discount', 'service-finder'); ?>
            </label>
            <br>
            <div class="radio sf-radio-checkbox">
              <input id="edit_offer_percentage" type="radio" name="discount_type" value="percentage">
              <label for="edit_offer_percentage">
              <?php esc_html_e('Percentage', 'service-finder'); ?>
              </label>
            </div>
            <div class="radio sf-radio-checkbox">
              <input id="edit_offer_fixed" type="radio" name="discount_type" value="fixed">
              <label for="edit_offer_fixed">
              <?php esc_html_e('Fixed', 'service-finder'); ?>
              </label>
            </div>
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group">
            <input name="discount_value" id="edit_discount_value" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Number of percentage or fixed amount', 'service-finder'); ?>">
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group">
              <?php echo service_finder_add_media_button(); ?>
              <textarea id="edit_discount_description" name="edit_discount_description"></textarea>
          </div>
        </div>
      </div>      
      <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">
      <?php esc_html_e('Cancel', 'service-finder'); ?>
      </button>
      <input type="hidden" name="offerid">
      <input type="submit" class="btn btn-primary" name="edit-offers" value="<?php esc_html_e('Update Offers', 'service-finder'); ?>" />
    </div>
  </form>
</div>
</div>