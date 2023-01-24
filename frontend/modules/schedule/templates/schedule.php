<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/

wp_add_inline_script( 'service_finder-js-schedule-form', '/*Declare global variable*/
var user_id = "'.$globalproviderid.'";', 'after' );

wp_add_inline_script( 'service_finder-js-app', '/*Declare global variable*/
var user_id = "'.$globalproviderid.'";', 'after' );

$currUser = wp_get_current_user(); 
$members = service_finder_getMembers($globalproviderid);

wp_add_inline_script( 'service_finder-js-schedule-form', 'jQuery(function () {
		
		jQuery(".memberselect").on("changed.bs.select", function (e) {
		var data = jQuery(this).val();
		jQuery(".all-staff").hide();
		if(data != null){
		var str = data.toString();
		var ids = str.split(",");
		var arrayLength = ids.length;
		if(arrayLength > 0){
		for (var i = 0; i < arrayLength; i++) {
			jQuery(".staff-tab-"+ids[i]).show();
		}  
		}
		}
		  
		});
	});', 'before' );
?>
<div class="panel panel-default">
<div class="panel-heading sf-panel-heading">
  <h3 class="panel-tittle m-a0"><span class="fa fa-clock-o"></span> <?php echo (!empty($service_finder_options['label-schedule'])) ? esc_html($service_finder_options['label-schedule']) : esc_html__('Schedule', 'service-finder'); ?> </h3>
</div>
<div class="panel-body sf-panel-body padding-30">
  <div class="profile-form-bx sf-calview-custum">
  <div class="scheduledata-bx clearfix">
    <div class="tabbable">
      <!--Load Members Tabs-->
      <ul class="nav nav-tabs" id="membertab">
        <?php
					if(!empty($members)){
						echo '<li data-staff-id="all">';
                        echo '<a href="javascript:;" data-toggle="tab">'.esc_html__('All', 'service-finder').'</a>';
                        echo '</li>';
						foreach($members as $member){
						echo '<li class="staff-tab-'.$member->id.' all-staff" data-staff-id="'.esc_attr($member->id).'">';
                        echo '<a href="javascript:;" data-toggle="tab">'.$member->member_name.'</a>';
                        echo '</li>';
						$user_names[] = $member->member_name;
	                    $user_ids[]   = $member->id;
						}
					}else{
						$allowedhtml = array(
							'li' => array()
						);
						wp_kses(esc_html__('<li>No Member Found</li>', 'service-finder'), $allowedhtml);
					}
					?>
      </ul>
    </div>
    <h4 class="month-title" id="calmonth"></h4>
    <div class="staffmembers-bx">
      <select multiple="multiple" placeholder="<?php esc_html_e('Select Members', 'service-finder'); ?>" class="selectBox memberselect sf-select-box form-control sf-form-control">
        <?php
                if(!empty($members)){
                    foreach($members as $member){
                    echo '<option selected="selected" data-id="'.esc_attr($member->id).'" value="'.esc_attr($member->id).'">'.$member->member_name.'</option>';
                    }
                }else{
                    $allowedhtml = array(
							'li' => array()
						);
					wp_kses(esc_html__('<li>No Member Found</li>', 'service-finder'), $allowedhtml);
                }
                ?>
      </select>
    </div>
  </div>
  <!--Calendar Navigation-->
  <div class="pull-right form-inline">
    <div class="btn-group">
      <button class="btn btn-primary" data-calendar-nav="prev"><< <?php esc_html_e('Prev', 'service-finder'); ?></button>
      <button class="btn btn-default" data-calendar-nav="today">
      <?php esc_html_e('Today', 'service-finder'); ?>
      </button>
      <button class="btn btn-primary" data-calendar-nav="next">
      <?php esc_html_e('Next', 'service-finder'); ?>
      >></button>
    </div>
    <div class="btn-group">
      <button class="btn btn-custom" data-calendar-view="year">
      <?php esc_html_e('Year', 'service-finder'); ?>
      </button>
      <button class="btn btn-custom active" data-calendar-view="month">
      <?php esc_html_e('Month', 'service-finder'); ?>
      </button>
      <button class="btn btn-custom" data-calendar-view="week">
      <?php esc_html_e('Week', 'service-finder'); ?>
      </button>
      <button class="btn btn-custom" data-calendar-view="day">
      <?php esc_html_e('Day', 'service-finder'); ?>
      </button>
    </div>
    <div class="sf-calbooking-status">
      <span class="sf-calbooking-status-complete"><?php esc_html_e('Complete', 'service-finder'); ?></span>
      <span class="sf-calbooking-status-incomplete"><?php esc_html_e('Incomplete', 'service-finder'); ?></span>
    </div>
  </div>
  <br />
  <br />
  <br />
  <!--Load Calendar-->
  <div id="calendar"></div>
</div>
<!--Load Booking Details-->
<div id="booking-details" class="hidden"> </div>
<div class="modal fade" id="events-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal-title">
          <?php esc_html_e('Booking', 'service-finder'); ?>
        </h3>
      </div>
      <div class="modal-body"> </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">
        <?php esc_html_e('Close', 'service-finder'); ?>
        </button>
      </div>
    </div>
  </div>
</div>
</div>
</div>

