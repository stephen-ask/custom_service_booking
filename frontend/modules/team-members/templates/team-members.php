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
global $wpdb, $service_finder_Tables; 
wp_enqueue_script('service_finder-js-team-form');
$wpdb = service_finder_plugin_global_vars('wpdb');
$service_finder_Tables = service_finder_plugin_global_vars('service_finder_Tables');
$currUser = wp_get_current_user(); 
$settings = service_finder_getProviderSettings($globalproviderid);

wp_add_inline_script( 'service_finder-js-team-form', '/*Declare global variable*/
var user_id = "'.$globalproviderid.'";', 'after' );
?>
<div class="panel panel-default">
<div class="panel-heading sf-panel-heading">
  <h3 class="panel-tittle m-a0"><span class="fa fa-users"></span> <?php echo (!empty($service_finder_options['label-team-members'])) ? esc_html($service_finder_options['label-team-members']) : esc_html__('Team Members', 'service-finder'); ?> </h3>
</div>
<div class="panel-body sf-panel-body padding-30">
  <div class="margin-b-30 text-right">
    <button class="btn btn-primary" data-toggle="modal" data-target="#addmember" type="button"><i class="fa fa-plus"></i>
    <?php esc_html_e('ADD TEAM MEMBER', 'service-finder'); ?>
    </button>
  </div>
  <!--Display Team Member template-->
  <table id="members-grid" class="table table-striped table-bordered">
    <thead>
      <tr>
        <th> <div class="checkbox sf-radio-checkbox">
            <input type="checkbox" id="bulkMemberDelete">
            <label for="bulkMemberDelete"></label>
          </div>
          <button class="btn btn-danger btn-xs" id="deleteMemberTriger" title="<?php esc_html_e('Delete', 'service-finder'); ?>"><i class="fa fa-trash-o"></i></button></th>
        <th><?php esc_html_e('Name', 'service-finder'); ?></th>
        <th><?php esc_html_e('Phone', 'service-finder'); ?></th>
        <th><?php esc_html_e('Email', 'service-finder'); ?></th>
        <th><?php esc_html_e('Is Admin?', 'service-finder'); ?></th>
        <th><?php esc_html_e('Action', 'service-finder'); ?></th>
      </tr>
    </thead>
  </table>
  <!-- Basic -->
  <!-- Add Team Member Modal Popup Box -->
  <div id="addmember" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="post" class="add-new-member">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">
              <?php esc_html_e('Add New Member', 'service-finder'); ?>
            </h4>
          </div>
          <div class="modal-body clearfix row input_fields_wrap">
            <div class="profile-pic-bx">
              <div class="rwmb-field rwmb-plupload_image-wrapper">
                <div class="rwmb-input">
                  <ul class="rwmb-images rwmb-uploaded" data-field_id="sfmemberavatarupload" data-delete_nonce="" data-reorder_nonce="" data-force_delete="0" data-max_file_uploads="1" id="memberavatar">
                  </ul>
                  <div id="sfmemberavatarupload-dragdrop" class="RWMB-drag-drop drag-drop hide-if-no-js new-files" data-upload_nonce="1f7575f6fa" data-js_options="{&quot;runtimes&quot;:&quot;html5,silverlight,flash,html4&quot;,&quot;file_data_name&quot;:&quot;async-upload&quot;,&quot;browse_button&quot;:&quot;sfmemberavatarupload-browse-button&quot;,&quot;drop_element&quot;:&quot;sfmemberavatarupload-dragdrop&quot;,&quot;multiple_queues&quot;:true,&quot;max_file_size&quot;:&quot;8388608b&quot;,&quot;url&quot;:&quot;<?php echo esc_url($url); ?>wp-admin\/admin-ajax.php&quot;,&quot;flash_swf_url&quot;:&quot;<?php echo esc_url($url); ?>wp-includes\/js\/plupload\/plupload.flash.swf&quot;,&quot;silverlight_xap_url&quot;:&quot;<?php echo esc_url($url); ?>wp-includes\/js\/plupload\/plupload.silverlight.xap&quot;,&quot;multipart&quot;:true,&quot;urlstream_upload&quot;:true,&quot;filters&quot;:[{&quot;title&quot;:&quot;Allowed Image Files&quot;,&quot;extensions&quot;:&quot;jpg,jpeg,gif,png&quot;}],&quot;multipart_params&quot;:{&quot;field_id&quot;:&quot;sfmemberavatarupload&quot;,&quot;action&quot;:&quot;memberavatar_upload&quot;}}">
                    <div class = "drag-drop-inside text-center"> <img src="<?php echo esc_url($service_finder_Params['pluginImgUrl'].'/no_img.jpg'); ?>">
                      <p class="drag-drop-buttons">
                        <input id="sfmemberavatarupload-browse-button" type="button" value="<?php esc_html_e('Select Image', 'service-finder'); ?>" class="button btn btn-primary" />
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input name="member_fullname" id="member_fullname" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Full Name', 'service-finder'); ?>">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input name="member_email" id="member_email" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Member Email', 'service-finder'); ?>">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <input name="member_phone" id="member_phone" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Member Phone', 'service-finder'); ?>">
              </div>
            </div>
            <?php if($settings['booking_basedon'] == 'zipcode'){ ?>
            <div class="col-md-12">
              <label>
              <?php esc_html_e('Service Area', 'service-finder'); ?>
              </label>
            </div>
            <div id="loadservicearea"> </div>
            <?php }elseif($settings['booking_basedon'] == 'region'){ ?>
            <div class="col-md-12">
              <label>
              <?php esc_html_e('Regions', 'service-finder'); ?>
              </label>
            </div>
            <div id="loadregions"> </div>
            <?php }?>
            <?php if(service_finder_booking_date_method($globalproviderid) == 'multidate'){ ?>
          	<div class="col-md-12">
            	<label><?php esc_html_e('Services', 'service-finder'); ?></label>
          	</div>
          	<div id="loadservices"> </div>
          	<?php }?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">
            <?php esc_html_e('Cancel', 'service-finder'); ?>
            </button>
            <input type="submit" class="btn btn-primary" name="add-member" value="<?php esc_html_e('Create', 'service-finder'); ?>" />
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- Add Modal END-->
  <!-- Edit Team Members Modal Popup box -->
  <form method="post" class="edit-member default-hidden" id="editmember">
    <div class="clearfix row input_fields_wrap">
      <div class="profile-pic-bx">
        <div class="rwmb-field rwmb-plupload_image-wrapper">
          <div class="rwmb-input">
            <ul class="rwmb-images rwmb-uploaded" data-field_id="sfmemberavataruploadedit" data-delete_nonce="" data-reorder_nonce="" data-force_delete="0" data-max_file_uploads="1" id="memberavataredit">
            </ul>
            <div id="sfmemberavataruploadedit-dragdrop" class="RWMB-drag-drop drag-drop hide-if-no-js new-files" data-upload_nonce="1f7575f6fa" data-js_options="{&quot;runtimes&quot;:&quot;html5,silverlight,flash,html4&quot;,&quot;file_data_name&quot;:&quot;async-upload&quot;,&quot;browse_button&quot;:&quot;sfmemberavataruploadedit-browse-button&quot;,&quot;drop_element&quot;:&quot;sfmemberavataruploadedit-dragdrop&quot;,&quot;multiple_queues&quot;:true,&quot;max_file_size&quot;:&quot;8388608b&quot;,&quot;url&quot;:&quot;<?php echo esc_url($url); ?>wp-admin\/admin-ajax.php&quot;,&quot;flash_swf_url&quot;:&quot;<?php echo esc_url($url); ?>wp-includes\/js\/plupload\/plupload.flash.swf&quot;,&quot;silverlight_xap_url&quot;:&quot;<?php echo esc_url($url); ?>wp-includes\/js\/plupload\/plupload.silverlight.xap&quot;,&quot;multipart&quot;:true,&quot;urlstream_upload&quot;:true,&quot;filters&quot;:[{&quot;title&quot;:&quot;Allowed Image Files&quot;,&quot;extensions&quot;:&quot;jpg,jpeg,gif,png&quot;}],&quot;multipart_params&quot;:{&quot;field_id&quot;:&quot;sfmemberavataruploadedit&quot;,&quot;action&quot;:&quot;memberavatar_uploadedit&quot;}}">
              <div class = "drag-drop-inside text-center"> <img src="<?php echo esc_url($service_finder_Params['pluginImgUrl'].'/no_img.jpg'); ?>">
                <p class="drag-drop-info">
                  <?php esc_html_e('Drop avatar here', 'service-finder'); ?>
                </p>
                <p><?php esc_html_e('or', 'service-finder'); ?></p>
                <p class="drag-drop-buttons">
                  <input id="sfmemberavataruploadedit-browse-button" type="button" value="<?php esc_html_e('Select Image', 'service-finder'); ?>" class="button btn btn-primary" />
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <input name="member_fullname" id="member_fullname" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Full Name', 'service-finder'); ?>">
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <input name="member_email" id="member_email" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Member Email', 'service-finder'); ?>">
        </div>
      </div>
      <div class="col-md-12">
        <div class="form-group">
          <input name="member_phone" id="member_phone" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Member Phone', 'service-finder'); ?>">
        </div>
      </div>
      <?php if($settings['booking_basedon'] == 'zipcode'){ ?>
      <div class="col-md-12">
        <label><?php esc_html_e('Service Area', 'service-finder'); ?></label>
      </div>
      <div id="editloadservicearea"> </div>
      <?php }elseif($settings['booking_basedon'] == 'region'){ ?>
      <div class="col-md-12">
        <label><?php esc_html_e('Regions', 'service-finder'); ?></label>
      </div>
      <div id="editloadregions"> </div>
      <?php }?>
      <?php if(service_finder_booking_date_method($globalproviderid) == 'multidate'){ ?>
      <div class="col-md-12">
        <label><?php esc_html_e('Services', 'service-finder'); ?></label>
      </div>
      <div id="editloadservices"> </div>
      <?php }?>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">
      <?php esc_html_e('Cancel', 'service-finder'); ?>
      </button>
      <input type="hidden" name="memberid">
      <input type="submit" class="btn btn-primary" name="edit-member" value="<?php esc_html_e('Update', 'service-finder'); ?>" />
    </div>
  </form>
  <!-- Edit Team Members Modal END-->
  <!-- Set time slots for member Modal Popup box -->
  <?php if(service_finder_availability_method($globalproviderid) == 'timeslots'){ ?>
  <form method="post" class="member-timeslots default-hidden" id="membertimeslots">
    <div class="clearfix input_fields_wrap">
      <?php 
	  $days = array('monday','tuesday','wednesday','thursday','friday','saturday','sunday'); 
		?>
        <div class="tabbable sf-tabs-admin sf-member-slots">
        <ul class="nav nav-tabs" id="subTab">
        <?php
        if(!empty($days)){
	  	foreach($days as $day){
		$class = ($day == 'monday') ? 'active' : '';
		?>
        <?php echo '<li class="'.sanitize_html_class($class).'"><a data-toggle="tab" href="#member-'.$day.'">'.service_finder_day_translate($day).'</a></li>'; ?>
        <?php 
		}
		} 
		?>
        </ul>
        <div class="tab-content">
        <?php 
		if(!empty($days)){
	  	foreach($days as $day){
		?>
        <div id="member-<?php echo esc_attr($day); ?>" class="tab-pane <?php echo ($day == 'monday') ? 'active' : '';?>">
        <div class="tabs-inr">
		<ul id="memberslot-<?php echo esc_attr($day); ?>" class="timeslots memberslot list-inline">
        </ul>            
        </div>
        </div>
	    <?php 
		} 
		}
		?>
      	</div>
        </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">
      <?php esc_html_e('Cancel', 'service-finder'); ?>
      </button>
      <input type="hidden" name="memberid">
      <input type="submit" class="btn btn-primary" name="edit-member" value="<?php esc_html_e('Update', 'service-finder'); ?>" />
    </div>
  </form>
  <?php } ?>
  <!-- Set start time for member Modal Popup box -->
  <?php if(service_finder_availability_method($globalproviderid) == 'starttime'){ ?>
  <form method="post" class="member-starttime default-hidden" id="memberstarttime">
    <div class="clearfix input_fields_wrap">
      <?php 
	  $days = array('monday','tuesday','wednesday','thursday','friday','saturday','sunday'); 
		?>
        <div class="tabbable sf-tabs-admin sf-member-slots">
        <ul class="nav nav-tabs" id="subTab">
        <?php
        if(!empty($days)){
	  	foreach($days as $day){
		$class = ($day == 'monday') ? 'active' : '';
		?>
        <?php echo '<li class="'.sanitize_html_class($class).'"><a data-toggle="tab" href="#member-'.$day.'">'.service_finder_day_translate($day).'</a></li>'; ?>
        <?php 
		}
		} 
		?>
        </ul>
        <div class="tab-content">
        <?php 
		if(!empty($days)){
	  	foreach($days as $day){
		?>
        <div id="member-<?php echo esc_attr($day); ?>" class="tab-pane <?php echo ($day == 'monday') ? 'active' : '';?>">
        <div class="tabs-inr">
		<div class="sf-team-schedule-bx">
        <div class="col-md-5 col-sm-5">
        <div class="form-group">
        <?php 
            ?>
          <select class="sf-select-box form-control sf-form-control" name="memberstarttime-<?php echo esc_attr($day); ?>" data-live-search="true" title="<?php esc_html_e('Start Time', 'service-finder'); ?>">
            <option value=""><?php esc_html_e('OFF', 'service-finder'); ?></option>
            <?php 
            $begin = new DateTime("00:00");
            $end   = new DateTime("24:00");
            
            $interval = DateInterval::createFromDateString($slot_interval.' min');
            
            $times    = new DatePeriod($begin, $interval, $end);
            
            foreach ($times as $time) {
                if($time_format){
                    echo '<option value="'.$time->format('H:i').'">'.$time->format('H:i').'</option>';	 
                }else{
                    echo '<option value="'.$time->format('H:i').'">'.$time->format('h:i a').'</option>';
                }
            }
            ?>
          </select>  
        </div>
        </div>
        <div class="col-md-5 col-sm-5">
        <div class="form-group">
        <?php 
            ?>
          <select class="sf-select-box form-control sf-form-control" name="memberendtime-<?php echo esc_attr($day); ?>" data-live-search="true" title="<?php esc_html_e('End Time', 'service-finder'); ?>">
            <option value=""><?php esc_html_e('End Time', 'service-finder'); ?></option>
            <?php 
            $begin = new DateTime("00:00");
            $end   = new DateTime("24:00");
            
            $interval = DateInterval::createFromDateString($slot_interval.' min');
            
            $times    = new DatePeriod($begin, $interval, $end);
            
            foreach ($times as $time) {
                if($time_format){
                    echo '<option value="'.$time->format('H:i').'">'.$time->format('H:i').'</option>';	 
                }else{
                    echo '<option value="'.$time->format('H:i').'">'.$time->format('h:i a').'</option>';
                }
            }
            ?>
          </select>  
        </div>
        </div>
        <div class="col-md-2 col-sm-2 sf-member-break-btn"><a href="javascript:;" data-weekday="<?php echo esc_attr($day) ?>" class="site-button radius square m-tb10 openmemberbreakpopup" title="<?php esc_html_e( 'Add Break Time', 'service-finder' ) ?>"><i class="fa fa-plus"></i></a></div>
        <div class="col-md-12">
        <ul id="memberbreak-<?php echo esc_attr($day); ?>" class="sf-memberbreak-days">
        </ul>
        </div>          
        </div>
        </div>
        </div>
	    <?php 
		} 
		}
		?>
      	</div>
        </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">
      <?php esc_html_e('Cancel', 'service-finder'); ?>
      </button>
      <input type="hidden" name="memberid">
      <input type="submit" class="btn btn-primary" name="edit-member" value="<?php esc_html_e('Update', 'service-finder'); ?>" />
    </div>
  </form>
  
  <div id="addmemberbreaktimepopup" class="collapse sf-couponcode-popup sf-cpc-popuplarge">
  <i class="fa fa-close sf-memberbreakpopup-close"></i>
  <form class="addmemberbreaktimeform" method="post">
      
      <div class="row">
               <?php 
                $breakStart = new SFTimeSlots( array( 'use_empty' => false ) );
                $break_start_choices = $breakStart->render(
                    'break_start_time',
                    '',
                    array( 'class' => 'break-start sf-select-box', 'style' => 'width:auto' )
                );
                $breakEnd = new SFTimeSlots( array( 'use_empty' => false ) );
                $break_end_choices_attributes = array( 'class' => 'break-end sf-select-box', 'style' => 'width:auto' );
                $break_end_choices = $breakEnd->render(
                    'break_end_time',
                    '',
                    $break_end_choices_attributes
                );
              ?>
              <div class="col-xs-5">
                <div class="form-group break-schedule-item-row">
                  <?php 
                    printf($break_start_choices);
                  ?>
                </div>
              </div>
              <div class="col-xs-2">
                <div class="text-center">
                  <?php 
                    echo '<h4>'.esc_html__( 'to', 'service-finder').'</h4>';
                  ?>
                </div>
              </div>
              <div class="col-xs-5">
                <div class="form-group break-schedule-item-row">
                  <?php 
                    printf($break_end_choices);
                  ?>
                </div>
              </div>
            </div>
      <div class="row">     
          <div class="col-md-12">
            <a href="javascript:;" class="btn btn-primary addbreaktime"><?php esc_html_e('Continue', 'service-finder'); ?> <i class="fa fa-arrow-circle-right"></i></a>
          </div>
      </div>
      </form>
  </div>
	<div class="sf-couponcode-popup-overlay" style="display:none;"></div>
  <?php } ?>
</div>
</div>