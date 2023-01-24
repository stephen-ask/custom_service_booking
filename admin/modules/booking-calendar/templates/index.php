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
<?php
$service_finder_options = get_option('service_finder_options');
$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('Provider', 'service-finder');
?>
<div class="profile-form-bx">
  <div class="scheduledata-bx clearfix">
    <div class="tabbable">
      <ul class="nav nav-tabs" id="alphabetsort">
        <?php
$a=range("A","Z");
foreach($a as $char){
echo '<li data-char="'.strtolower(esc_attr($char)).'">'.$char.'</li>';
}
?>
      </ul>
      <!--Load Members Tabs-->
      <ul class="nav nav-tabs provider-tab-in" id="providertab">
        <?php
					if(!empty($args)){
						echo '<li data-staff-id="all">';
                        echo '<a href="javascript:;" data-toggle="tab">'.esc_html__( 'All', 'service-finder' ).' '.esc_html($providerreplacestring).'</a>';
                        echo '</li>';
						foreach($args as $arg){
						echo '<li class="staff-tab-'.esc_attr($arg->wp_user_id).'" data-staff-id="'.esc_attr($arg->wp_user_id).'">';
                        echo '<a href="javascript:;" data-toggle="tab">'.$arg->full_name.'</a>';
                        echo '</li>';
						$user_names[] = $arg->full_name;
	                    $user_ids[]   = $arg->wp_user_id;
						}
					}else{
						printf('<li>'.esc_html__( 'No', 'service-finder' ).' '.esc_html($providerreplacestring).' '.esc_html__('Found', 'service-finder').'</li>');
						
					}
					?>
      </ul>
    </div>
    <h4 class="month-title" id="calmonth"></h4>
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
<!-- Loading area start -->
<div class="loading-area default-hidden">
  <div class="loading-box"></div>
  <div class="loading-pic"></div>
</div>
<!-- Loading area end -->
