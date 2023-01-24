<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/

wp_enqueue_script('service_finder-js-bh-form');
$wpdb = service_finder_plugin_global_vars('wpdb');
$service_finder_Params = service_finder_plugin_global_vars('service_finder_Params');
$service_finder_Tables = service_finder_plugin_global_vars('service_finder_Tables');
wp_add_inline_script( 'service_finder-js-bh-form', '/*Declare global variable*/
var user_id = "'.$globalproviderid.'";', 'after' );
?>
<!--Availability Template-->
<div class="panel panel-default">
<div class="panel-heading sf-panel-heading">
  <h3 class="panel-tittle m-a0"><span class="fa fa-clock-o"></span> <?php echo (!empty($service_finder_options['label-business-hours'])) ? esc_html($service_finder_options['label-business-hours']) : esc_html__('Business Hours', 'service-finder'); ?> </h3>
  <div id="sf-wallet-top-balance">
  <?php 
  $business_hours_active_inactive = get_user_meta($globalproviderid,'business_hours_active_inactive',true); 
  if($business_hours_active_inactive == 'active' || $business_hours_active_inactive == ''){
	$checked = 'checked="checked"';  
  }else{
  	$checked = '';
  }
  ?>
  <input <?php echo esc_attr($checked); ?> id="businesshours-active" data-toggle="toggle" data-on="<?php esc_html_e('On', 'service-finder'); ?>" data-off="<?php esc_html_e('Off', 'service-finder'); ?>" data-providerid="<?php echo esc_attr($globalproviderid); ?>" type="checkbox" name="businesshours-active">
  </div>
</div>
<div class="panel-body aon-panel-body p-a30">
<div id="pro-bh">
<form class="provider-bh-hour provideravl-<?php echo esc_attr($globalproviderid); ?>">
<?php 
$timeslots = get_user_meta($globalproviderid,'timeslots',true);
if(!empty($timeslots)){
foreach ( $timeslots as $key => $list_item ) {
$item = explode('-',$list_item);
if($list_item != "off"){
$checked = 'checked="checked"';
}else{
$checked = '';
}
?>
<div id="weekdaybx-<?php echo esc_attr($key) ?>" data-id="<?php echo esc_attr($key) ?>" data-staff_schedule_item_id="<?php echo esc_attr($key) ?>" class="row working-hours-admin m-b10 staff-schedule-item-row">
<div class="col-md-3">
<div class="clearfix sf-bh-onoff">
<h5><?php echo service_finder_get_dayname_by_daynumber($key); ?></h5> 
<input class="bh-onoff" <?php echo esc_attr($checked); ?> data-toggle="toggle" data-on="<?php esc_html_e('On', 'service-finder'); ?>" data-off="<?php esc_html_e('Off', 'service-finder'); ?>" data-weekday="<?php echo esc_attr($key); ?>" type="checkbox" name="<?php echo 'bhstatus_'.esc_attr($key); ?>">
</div>
</div>
<div class="col-md-9">
<div class="row sf-bh-timing-row">
<div class="col-sm-5">
	<?php
	$workingStart = new SFTimeSlots( array( 'use_empty' => false ) );
	echo $working_start_choices = $workingStart->render(
		'start_time[' . $key . ']',
		$item[0],
		array( 'class' => 'working-start sf-select-box', 'style' => 'width:auto' )
	);
	?>
</div>
<div class="col-sm-5">
	<?php
	$workingEnd = new SFTimeSlots( array( 'use_empty' => false ) );
	$working_end_choices_attributes = array( 'class' => 'working-end sf-select-box hide-on-non-working-day', 'style' => 'width:auto' );
	echo $working_end_choices = $workingEnd->render(
		'end_time[' . $key . ']',
		(!empty($item[1])) ? $item[1] : '',
		$working_end_choices_attributes
	);
	?>
</div>
<div class="col-sm-2 sf-add-breaktime-btn"><a href="javascript:;" data-weekday="<?php echo esc_attr($key) ?>" class="site-button radius square m-tb10 openbreaktimepopup" title="<?php esc_html_e( 'Add Break Time', 'service-finder' ) ?>"><i class="fa fa-plus"></i></a></div>
<div class="selected-working-hours clearfix breaktimes-<?php echo esc_attr($key) ?>">
		<?php
		$breaktime = get_user_meta($globalproviderid, 'breaktime', true);
		$html = '';
		if(!empty($breaktime[$key])){
			$html .= '<ul class="m-a0">';
			foreach($breaktime[$key] as $breakslot){
				$break = explode('-',$breakslot);
				$html .= '<li data-breakslot="'.esc_attr($breakslot).'" data-weekday="'.esc_attr($key).'">'.date('h:i a',strtotime($break[0])).' '.esc_html__( 'TO', 'service-finder').' '.date('h:i a',strtotime($break[1])).' <span class="working-hours-remove"><i class="fa fa-close"></i></span></li>';
			}
			$html .= '</ul>';
		}
		echo $html;
		?>
	</div>
<div class="sf-bh-overlay" style=" <?php echo ($list_item != "off") ? 'display:none' : ''; ?>"></div>
</div>
</div>
</div>
<?php
} 
}
?>
<div class="row working-hours-admin text-center m-b10">
    <div class="col-md-12">
        <input type="hidden" id="currentweek" value="">
        <input type="hidden" name="providerid" value="<?php echo esc_attr($globalproviderid) ?>">
        <a id="sf-bh-update" href="javascript:void(0)" class="btn btn-primary"><?php esc_html_e( 'Update Business Hours', 'service-finder' ) ?> <i class="fa fa-refresh"></i></a>
    </div>
    <div class="col-md-1"></div>
</div>
</form>

<div id="addbreaktimepopup" class="collapse sf-couponcode-popup sf-cpc-popuplarge">
<i class="fa fa-close sf-breakpopup-close"></i>
<form class="addbreaktimeform" method="post">
  
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
        <input type="hidden" name="weekday" value="">
        <button type="submit" name="updatebreaktime" class="btn btn-primary"><?php esc_html_e('Save Break Time', 'service-finder'); ?> <i class="fa fa-arrow-circle-right"></i></button>
      </div>
  </div>
  </form>
</div>
<div class="sf-couponcode-popup-overlay" style="display:none;"></div>
</div>
</div>
</div>
