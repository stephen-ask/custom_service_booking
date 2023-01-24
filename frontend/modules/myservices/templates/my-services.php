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
wp_enqueue_script('service_finder-js-service-form');
$wpdb = service_finder_plugin_global_vars('wpdb');
$service_finder_Tables = service_finder_plugin_global_vars('service_finder_Tables');

wp_add_inline_script( 'service_finder-js-service-form', '/*Declare global variable*/
var user_id = "'.$globalproviderid.'";', 'after' );

$slot_interval = service_finder_get_slot_interval($globalproviderid);

$currUser = wp_get_current_user(); 
?>
<div class="panel panel-default">
<div class="panel-heading sf-panel-heading">
  <h3 class="panel-tittle m-a0"><span class="fa fa-server"></span> <?php echo (!empty($service_finder_options['label-my-services'])) ? esc_html($service_finder_options['label-my-services']) : esc_html__('My Services', 'service-finder'); ?> </h3>
</div>
<div class="panel-body sf-panel-body padding-30">
  <div class="margin-b-30 text-right">
    <button class="btn btn-primary" data-toggle="modal" data-target="#addremovegroup" type="button"><i class="fa fa-plus"></i>
    <?php esc_html_e('ADD/REMOVE GROUP', 'service-finder'); ?>
    </button>
    <button class="btn btn-primary" data-toggle="modal" data-target="#addservice" type="button"><i class="fa fa-plus"></i>
    <?php esc_html_e('ADD A SERVICE', 'service-finder'); ?>
    </button>
  </div>
  <!--Display Services into datatable-->
  <table id="service-grid" class="table table-striped table-bordered">
    <thead>
      <tr>
        <th> <div class="checkbox sf-radio-checkbox">
            <input type="checkbox" id="bulkDelete">
            <label for="bulkDelete"></label>
          </div>
          <button class="btn btn-danger btn-xs" id="deleteTriger" title="<?php esc_html_e('Delete', 'service-finder'); ?>"><i class="fa fa-trash-o"></i></button></th>
        <th><?php esc_html_e('Service Name', 'service-finder'); ?></th>
        <th><?php esc_html_e('Group Name', 'service-finder'); ?></th>
        <th><?php esc_html_e('Cost', 'service-finder'); ?></th>
        <th><?php esc_html_e('Action', 'service-finder'); ?></th>
      </tr>
    </thead>
  </table>
  <!-- Add Service Modal Popup Box-->
  <div id="addservice" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="post" class="add-new-service">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">
              <?php esc_html_e('Add New Service', 'service-finder'); ?>
            </h4>
          </div>
          <div class="modal-body clearfix row input_fields_wrap">
            <div class="col-md-12">
              <div class="form-group">
                <input name="service_name" id="service_name" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Service Name', 'service-finder'); ?>">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group form-inline">
                <label>
                <?php esc_html_e('Service Cost', 'service-finder'); ?>
                </label>
                <br>
                <div class="radio sf-radio-checkbox">
                  <input id="fixed" type="radio" name="cost_type" value="fixed" checked>
                  <label for="fixed">
                  <?php esc_html_e('Fixed Price', 'service-finder'); ?>
                  </label>
                </div>
                <div class="radio sf-radio-checkbox">
                  <input id="hourly" type="radio" name="cost_type" value="hourly">
                  <label for="hourly">
                  <?php esc_html_e('Per Hour', 'service-finder'); ?>
                  </label>
                </div>
                <div class="radio sf-radio-checkbox">
                  <input id="perperson" type="radio" name="cost_type" value="perperson">
                  <label for="perperson">
                  <?php esc_html_e('Item', 'service-finder'); ?>
                  </label>
                </div>
                <?php if(service_finder_booking_date_method($globalproviderid) == 'multidate'){ ?>
                <div class="radio sf-radio-checkbox">
                  <input id="multidays" type="radio" name="cost_type" value="days">
                  <label for="multidays">
                  <?php esc_html_e('Days', 'service-finder'); ?>
                  </label>
                </div>
                <?php } ?>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <input name="service_cost" id="service_cost" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Service Cost', 'service-finder'); ?>">
                <span class="text-desc"><?php esc_html_e('Enter 0 if there wil be no cost.', 'service-finder'); ?></span>
              </div>
            </div>
            <div class="col-md-12" id="service_hours_bx" style="display:none;">
	          <label>
              <?php esc_html_e('Number of Hours', 'service-finder'); ?>
              </label>
              <br>
              <div class="form-group">
                <input name="service_hours" id="service_hours" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Service Hours e.g. 1.5 (1 hour 50 minutes) or .5 (50 minutes)', 'service-finder'); ?>">
              </div>
            </div>
            <div class="col-md-12" id="service_persons_bx" style="display:none;">
              <label>
              <?php esc_html_e('Number of Persons', 'service-finder'); ?>
              </label>
              <br>
              <div class="form-group">
                <input name="service_persons" id="service_persons" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Service Persons', 'service-finder'); ?>">
              </div>
            </div>
            <div id="service_days_bx" style="display:none;">
            <div class="col-md-12">
              <label>
              <?php esc_html_e('Number of Days', 'service-finder'); ?>
              </label>
              <br>	
              <div class="form-group">
                <input name="service_days" id="service_days" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Service Days', 'service-finder'); ?>">
              </div>
            </div>
            <div class="col-md-12">
        	  <label>
			  <?php esc_html_e('Weekdays Availability', 'service-finder'); ?>
          	  </label>
	          <br>
              <ul class="sf-service-weekdays">
              <?php
              $weekdays = service_finder_get_weekdays();
			  if(!empty($weekdays)){
			  	foreach($weekdays as $weekday){
				$dayname = service_finder_day_translate($weekday);
				echo '<li>';
				echo '<span>'.$dayname.'</span>';
				echo '<span><input checked data-toggle="toggle" data-on="'.esc_html__('On', 'service-finder').'" data-off="'.esc_html__('Off', 'service-finder').'" type="checkbox" name="'.esc_attr($weekday).'_status"></span>';
				echo '<span><input type="text" class="form-control sf-form-control" name="'.esc_attr($weekday).'_max_booking" value="1" placeholder="'.esc_html__('Max Booking', 'service-finder').'"></span>';
				echo '</li>';
				}
			  }
			  ?>
              </ul>
            </div>
            </div>
            
            <div id="paddingtime">
            <div class="col-md-6">
            <label>
			<?php esc_html_e('Padding Time (Before)', 'service-finder'); ?>
            </label>
            <br>
            <div class="form-group">
            <?php 
			$intervals = service_finder_get_buffer_time_interval();
            ?>
              <select class="sf-select-box form-control sf-form-control" name="before_padding_time" data-live-search="true" title="<?php esc_html_e('Padding Time (Before)', 'service-finder'); ?>">
                <option value=""><?php esc_html_e('Padding Time (Before)', 'service-finder'); ?></option>
                <?php
                if(!empty($intervals)){
                    foreach($intervals as $key => $value){
                        echo '<option value="'.esc_attr($key).'">'.esc_html($value).'</option>';	
                    }
                }
                ?>
              </select>  
            </div>
            </div>
        
        	<div class="col-md-6">
            <label>
			<?php esc_html_e('Padding Time (After)', 'service-finder'); ?>
            </label>
            <br>
            <div class="form-group">
            <?php 
			$intervals = service_finder_get_buffer_time_interval();
            ?>
              <select class="sf-select-box form-control sf-form-control" name="after_padding_time" data-live-search="true" title="<?php esc_html_e('Padding Time (After)', 'service-finder'); ?>">
                <option value=""><?php esc_html_e('Padding Time (After)', 'service-finder'); ?></option>
                <?php
                if(!empty($intervals)){
                    foreach($intervals as $key => $value){
                        echo '<option value="'.esc_attr($key).'">'.esc_html($value).'</option>';	
                    }
                }
                ?>
              </select>  
            </div>
            </div>
            </div>
            <div class="col-md-12">
              <div class="form-group" id="grouparea">
                <select class="sf-select-box form-control sf-form-control" name="group_id" data-live-search="true" title="<?php esc_html_e('Select Group', 'service-finder'); ?>" id="group_id">
                      <?php
                      $groupinfo = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->service_groups.' where provider_id = %d ORDER BY group_name',$globalproviderid));
					  echo '<option value="">'.esc_html__('Select Group', 'service-finder').'</option>';
					  if(!empty($groupinfo)){
					  	foreach($groupinfo as $grouprow){
							echo '<option value="'.esc_attr($grouprow->id).'">'.esc_html($grouprow->group_name).'</option>';
						}
					  }
					  ?>
                </select>
              </div>
            </div>
            <div class="col-md-12">
              <div class="group-outer-bx">
                <label>
                <?php echo '<a href="javascript:;" class="togglenewgroup btn btn-sm button-default"><i class="fa fa-plus"></i> '.esc_html__('Add New Group', 'service-finder').'</a>'; ?>
                </label>
              </div>
            </div>
            <div class="service_group_bx clearfix clear" style="display:none;">
            <div class="col-md-12">
              <div class="form-group">
                <input name="group_name" id="group_name" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Add New Group', 'service-finder'); ?>">
              </div>
            </div>
            <div class="col-md-12">
                <button type="button" class="btn btn-sm btn-primary addnewgroup">
                <i class="fa fa-plus"></i> <?php esc_html_e('Add New Group', 'service-finder'); ?>
                </button>
            </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
               	  <?php 
				  $settings = array( 
									'editor_height' => '100px',
									'textarea_name' => 'description',
									 'default_editor'      => 'quicktags'
								);
	
				  wp_editor( '', 'description', $settings );
				  ?>
              </div>
            </div>
            <?php if(service_finder_offers_method($globalproviderid) == 'services' && service_finder_check_offer_system() == true){ ?>
            <div class="col-md-12">
              <div class="form-group form-inline">
                <label>
                <?php esc_html_e('Offers & Promotions', 'service-finder'); ?>
                </label>
                <br>
                <div class="checkbox sf-radio-checkbox">
                  <input id="offers" type="checkbox" name="offers" value="yes">
                  <label for="offers">
                  <?php esc_html_e('Make Offer', 'service-finder'); ?>
                  </label>
                </div>
              </div>
            </div>
            <div id="offerson" style="display:none">
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
                <input name="max_coupon" id="max_coupon" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Max Uses', 'service-finder'); ?>">
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
									'default_editor' => 'quicktags',
								);
	
				  wp_editor( '', 'discount_description', $settings );
				  ?>
              </div>
            </div>
            </div>
            <?php } ?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">
            <?php esc_html_e('Cancel', 'service-finder'); ?>
            </button>
            <input type="submit" class="btn btn-primary" name="add-service" value="<?php esc_html_e('Create', 'service-finder'); ?>" />
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- Edit Service Modal Popup Box-->
  <form method="post" class="edit-service default-hidden" id="editservice">
    <div class="clearfix row input_fields_wrap">
      <div class="col-md-12">
        <div class="form-group">
          <input name="service_name" id="service_name" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Service Name', 'service-finder'); ?>">
        </div>
      </div>
      <div class="col-md-12">
        <div class="form-group form-inline">
          <label>
          <?php esc_html_e('Service Cost', 'service-finder'); ?>
          </label>
          <br>
          <div class="radio sf-radio-checkbox">
            <input id="editfixed" type="radio" name="cost_type" value="fixed" checked>
            <label for="editfixed">
            <?php esc_html_e('Fixed Price', 'service-finder'); ?>
            </label>
          </div>
          <div class="radio sf-radio-checkbox">
            <input id="edithourly" type="radio" name="cost_type" value="hourly">
            <label for="edithourly">
            <?php esc_html_e('Per Hour', 'service-finder'); ?>
            </label>
          </div>
          <div class="radio sf-radio-checkbox">
            <input id="editperperson" type="radio" name="cost_type" value="perperson">
            <label for="editperperson">
            <?php esc_html_e('Item', 'service-finder'); ?>
            </label>
          </div>
          <?php if(service_finder_booking_date_method($globalproviderid) == 'multidate'){ ?>
          <div class="radio sf-radio-checkbox">
            <input id="editmultidays" type="radio" name="cost_type" value="days">
            <label for="editmultidays">
            <?php esc_html_e('Days', 'service-finder'); ?>
            </label>
          </div>
          <?php } ?>
        </div>
      </div>
      <div class="col-md-12">
        <div class="form-group">
          <input name="service_cost" id="service_cost" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Service Cost', 'service-finder'); ?>">
          <span class="text-desc"><?php esc_html_e('Enter 0 if there wil be no cost.', 'service-finder'); ?></span>
        </div>
      </div>
      <div class="col-md-12" id="edit_service_hours_bx" style="display:none;">
	      <label>
          <?php esc_html_e('Number of Hours', 'service-finder'); ?>
          </label>
          <br>	
          <div class="form-group">
            <input name="service_hours" id="service_hours" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Service Hours e.g. 1.5 (1 hour 50 minutes) or .5 (50 minutes)', 'service-finder'); ?>">
            <span class="description"><?php esc_html_e('Service Hours e.g. 1.5 (1 hour 50 minutes) or .5 (50 minutes)', 'service-finder'); ?></span>
          </div>
        </div>
      <div class="col-md-12" id="edit_service_persons_bx" style="display:none;">
      	  <label>
		  <?php esc_html_e('Number of Persons', 'service-finder'); ?>
          </label>
          <br>	
          <div class="form-group">
            <input name="service_persons" id="service_persons" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Service Persons', 'service-finder'); ?>">
          </div>
        </div>  
      <div id="edit_service_days_bx" style="display:none;">
      <div class="col-md-12">
	      <label>
          <?php esc_html_e('Number of Days', 'service-finder'); ?>
          </label>
          <br>
          <div class="form-group">
            <input name="service_days" id="service_days" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Service Days', 'service-finder'); ?>">
          </div>
        </div>   
      <div class="col-md-12">
	      <label>
		  <?php esc_html_e('Weekdays Availability', 'service-finder'); ?>
          </label>
          <br>
              <ul class="sf-service-weekdays">
              <?php
              $weekdays = service_finder_get_weekdays();
			  if(!empty($weekdays)){
			  	foreach($weekdays as $weekday){
				$dayname = service_finder_day_translate($weekday);
				echo '<li>';
				echo '<span>'.$dayname.'</span>';
				echo '<span><input data-toggle="toggle" data-on="'.esc_html__('On', 'service-finder').'" data-off="'.esc_html__('Off', 'service-finder').'" type="checkbox" name="'.esc_attr($weekday).'_status"></span>';
				echo '<span><input type="text" class="form-control sf-form-control" name="'.esc_attr($weekday).'_max_booking" placeholder="'.esc_html__('Max Booking', 'service-finder').'"></span>';
				echo '</li>';
				}
			  }
			  ?>
              </ul>
            </div>   
      </div>      
        
      <div id="edit_paddingtime" style="display:none">
      <div class="col-md-6">
            <label>
			<?php esc_html_e('Padding Time (Before)', 'service-finder'); ?>
            </label>
            <br>
            <div class="form-group">
            <?php 
			$intervals = array(5 => esc_html__('5 Mins', 'service-finder'),10 => esc_html__('10 Mins', 'service-finder'),15 => esc_html__('15 Mins', 'service-finder'),20 => esc_html__('20 Mins', 'service-finder'),25 => esc_html__('25 Mins', 'service-finder'),30 => esc_html__('30 Mins', 'service-finder'),35 => esc_html__('35 Mins', 'service-finder'),40 => esc_html__('40 Mins', 'service-finder'),45 => esc_html__('45 Mins', 'service-finder'),50 => esc_html__('50 Mins', 'service-finder'),55 => esc_html__('55 Mins', 'service-finder'),60 => esc_html__('1 Hr', 'service-finder'),75 => esc_html__('1 Hr 15 Mins', 'service-finder'),90 => esc_html__('1 Hr 30 Mins', 'service-finder'),105 => esc_html__('1 Hr 45 Mins', 'service-finder'),120 => esc_html__('2 Hrs', 'service-finder'),150 => esc_html__('2 Hrs 30 Mins', 'service-finder'),180 => esc_html__('3 Hrs', 'service-finder'),210 => esc_html__('3 Hr 30 Mins', 'service-finder'),240 => esc_html__('4 Hrs', 'service-finder'));
            ?>
              <select class="sf-select-box form-control sf-form-control" name="before_padding_time" data-live-search="true" title="<?php esc_html_e('Padding Time (Before)', 'service-finder'); ?>">
                <option value=""><?php esc_html_e('Padding Time (Before)', 'service-finder'); ?></option>
                <?php
                if(!empty($intervals)){
                    foreach($intervals as $key => $value){
                        echo '<option value="'.esc_attr($key).'">'.esc_html($value).'</option>';	
                    }
                }
                ?>
              </select>  
            </div>
            </div>
        
	  <div class="col-md-6">
            <label>
			<?php esc_html_e('Padding Time (After)', 'service-finder'); ?>
            </label>
            <br>
            <div class="form-group">
            <?php 
			$intervals = array(5 => esc_html__('5 Mins', 'service-finder'),10 => esc_html__('10 Mins', 'service-finder'),15 => esc_html__('15 Mins', 'service-finder'),20 => esc_html__('20 Mins', 'service-finder'),25 => esc_html__('25 Mins', 'service-finder'),30 => esc_html__('30 Mins', 'service-finder'),35 => esc_html__('35 Mins', 'service-finder'),40 => esc_html__('40 Mins', 'service-finder'),45 => esc_html__('45 Mins', 'service-finder'),50 => esc_html__('50 Mins', 'service-finder'),55 => esc_html__('55 Mins', 'service-finder'),60 => esc_html__('1 Hr', 'service-finder'),75 => esc_html__('1 Hr 15 Mins', 'service-finder'),90 => esc_html__('1 Hr 30 Mins', 'service-finder'),105 => esc_html__('1 Hr 45 Mins', 'service-finder'),120 => esc_html__('2 Hrs', 'service-finder'),150 => esc_html__('2 Hrs 30 Mins', 'service-finder'),180 => esc_html__('3 Hrs', 'service-finder'),210 => esc_html__('3 Hr 30 Mins', 'service-finder'),240 => esc_html__('4 Hrs', 'service-finder'));
            ?>
              <select class="sf-select-box form-control sf-form-control" name="after_padding_time" data-live-search="true" title="<?php esc_html_e('Padding Time (After)', 'service-finder'); ?>">
                <option value=""><?php esc_html_e('Padding Time (After)', 'service-finder'); ?></option>
                <?php
                if(!empty($intervals)){
                    foreach($intervals as $key => $value){
                        echo '<option value="'.esc_attr($key).'">'.esc_html($value).'</option>';	
                    }
                }
                ?>
              </select>  
            </div>
            </div>  
      </div>      
      <div class="col-md-12">
      <div class="form-group" id="edit_grouparea">
      </div>
    </div>
    <div class="col-md-12">
      <div class="group-outer-bx">
        <label>
        <?php echo '<a href="javascript:;" class="togglenewgroup btn btn-sm button-default"><i class="fa fa-plus"></i> '.esc_html__('Add New Group', 'service-finder').'</a>'; ?>
        </label>
      </div>
    </div>
    <div class="edit_service_group_bx clearfix clear" style="display:none;">
    <div class="col-md-12">
      <div class="form-group">
        <input name="edit_group_name" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Add New Group', 'service-finder'); ?>">
      </div>
    </div>
    <div class="col-md-12">
        <button type="button" class="btn btn-sm btn-primary addnewgroup">
        <i class="fa fa-plus"></i> <?php esc_html_e('Add New Group', 'service-finder'); ?>
        </button>
    </div>
    </div>  
    <div class="col-md-12">
        <div class="form-group">
         <?php echo service_finder_add_media_button(); ?>
         <textarea id="editdesc" name="editdesc"></textarea>
        </div>
      </div>
    <?php if(service_finder_offers_method($globalproviderid) == 'services' && service_finder_check_offer_system() == true){ ?>
    <div class="col-md-12">
      <div class="form-group form-inline">
        <label>
        <?php esc_html_e('Offers & Promotions', 'service-finder'); ?>
        </label>
        <br>
        <div class="checkbox sf-radio-checkbox">
          <input id="edit_offers" type="checkbox" name="offers" value="yes">
          <label for="edit_offers">
          <?php esc_html_e('Make Offer', 'service-finder'); ?>
          </label>
        </div>
      </div>
    </div>
    <div id="editofferson" style="display:none">
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
            <input name="max_coupon" id="edit_max_coupon" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Max Uses', 'service-finder'); ?>">
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
    <?php } ?>  
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">
      <?php esc_html_e('Cancel', 'service-finder'); ?>
      </button>
      <input type="hidden" name="serviceid">
      <input type="submit" class="btn btn-primary" name="edit-service" value="<?php esc_html_e('Update', 'service-finder'); ?>" />
    </div>
  </form>
  <!-- Add/Remove group Modal Popup Box-->
  <div id="addremovegroup" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="post" class="add-new-group">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">
              <?php esc_html_e('Add New Group', 'service-finder'); ?>
            </h4>
          </div>
          <div class="modal-body clearfix row input_fields_wrap">
            <div class="col-md-12">
              <div class="form-group">
                <input name="group_name" id="group_name" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Group Name', 'service-finder'); ?>">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <?php
				  $groupinfo = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->service_groups.' where provider_id = %d ORDER BY group_name',$globalproviderid));
				  if(!empty($groupinfo)){
				  echo '<ul class="sf-group-list">';
					foreach($groupinfo as $grouprow){
						echo '<li>'.esc_html($grouprow->group_name).' <a href="javascript:;" class="delete-group" data-id="'.esc_attr($grouprow->id).'">&times;</a></li>';
					}
				  echo '</ul>';	
				  }
				  ?>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">
            <?php esc_html_e('Cancel', 'service-finder'); ?>
            </button>
            <input type="submit" class="btn btn-primary" name="add-group" value="<?php esc_html_e('Add Group', 'service-finder'); ?>" />
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</div>