<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/

$current_user = service_finder_plugin_global_vars('current_user');
?>
<!--Availability Template-->
<div class="auther-availability form-inr clearfix">
    <div class="alert alert-warning">
      <?php esc_html_e('You need to put available hours for the booking system to work', 'service-finder'); ?>
    </div>
    <div class="section-content">
      <div class="row">
        <form class="allstarttime" method="post">
        <div class="col-md-4">
        <div class="form-group">
        <?php 
            ?>
          <select class="sf-select-box form-control sf-form-control" name="starttime" data-live-search="true" title="<?php esc_html_e('Start Time', 'service-finder'); ?>">
            <option value=""><?php esc_html_e('Start Time', 'service-finder'); ?></option>
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
              
        <div class="col-md-4">
        <div class="form-group">
          <input type="text" name="maxbooking" class="form-control sf-form-control" placeholder="<?php esc_html_e('Max bookings','service-finder') ?>">
        </div>
        </div> 
        
        <div class="col-md-4">
        <div class="form-group">
            <div class="input-group-btn">
                <button type="submit" class="btn btn-primary addstarttime"><i class="fa fa-plus"></i> <?php esc_html_e('Add','service-finder') ?></button>
                <?php
				$currentpageurl = service_finder_get_url_by_shortcode('[service_finder_my_account]');
				
				if(service_finder_getUserRole($current_user->ID) == 'administrator'){
				
				$currentpageurl = add_query_arg( array('manageaccountby' => 'admin','manageproviderid' => $globalproviderid,'tabname' => 'availability'), $currentpageurl );
				
				}else{
				
				$currentpageurl = add_query_arg( array('tabname' => 'availability'), $currentpageurl );
				
				}
				?>
                <a href="javascript:;" data-currentpage="<?php echo esc_attr($currentpageurl); ?>" class="btn btn-primary reloadstarttimes"><i class="fa fa-refresh"></i> <?php esc_html_e('Reload','service-finder') ?></a>
            </div> 
        </div>
        </div>
        
        <div class="sf-availability-days clearfix">
		<?php 
		foreach($days as $day){
		$display_weekdayname = service_finder_trans_weekdays($day);
		?>
		<div class="col-md-4">
          <div class="checkbox sf-radio-checkbox">
              <input type="checkbox" id="chk-<?php echo esc_attr(strtolower($day)); ?>" name="weekdays[]" value="<?php echo esc_attr(strtolower($day)); ?>">
              <label for="chk-<?php echo esc_attr(strtolower($day)); ?>"><?php echo esc_html($display_weekdayname); ?></label>
            </div>
        </div>
		<?php
		}
		?>       
        </div>
    
        </form>
   	   </div>
       <div class="toggle-bulk-availability">
       <a href="javascript:;" class="btn btn-primary" data-toggle="collapse" data-target="#bulk-intervals"><i class="fa fa-plus"></i> <?php esc_html_e('Set Bulk Intervals','service-finder') ?></a>
       </div>
      <div class="collapse" id="bulk-intervals">
      <div class="row">
        <form class="bulk-intervals" method="post">
        <div class="col-md-3">
        <div class="form-group">
        <?php 
            ?>
          <select class="sf-select-box form-control sf-form-control" name="starttime" data-live-search="true" title="<?php esc_html_e('Start Time', 'service-finder'); ?>">
            <option value=""><?php esc_html_e('Start Time', 'service-finder'); ?></option>
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
        <div class="col-md-3">
        <div class="form-group">
        <?php 
            ?>
          <select class="sf-select-box form-control sf-form-control" name="endtime" data-live-search="true" title="<?php esc_html_e('End Time', 'service-finder'); ?>">
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
        
        <div class="col-md-3">
        <div class="form-group">
        <?php 
		if($slot_interval == 15){
			$intervals = array(15 => esc_html__('15 Mins', 'service-finder'),30 => esc_html__('30 Mins', 'service-finder'),45 => esc_html__('45 Mins', 'service-finder'),60 => esc_html__('1 Hr', 'service-finder'),75 => esc_html__('1 Hr 15 Mins', 'service-finder'),90 => esc_html__('1 Hr 30 Mins', 'service-finder'),105 => esc_html__('1 Hr 45 Mins', 'service-finder'),120 => esc_html__('2 Hrs', 'service-finder'));
		}else{
			$intervals = array(30 => esc_html__('30 Mins', 'service-finder'),60 => esc_html__('1 Hr', 'service-finder'),90 => esc_html__('1 Hr 30 Mins', 'service-finder'),120 => esc_html__('2 Hr', 'service-finder'),150 => esc_html__('2 Hrs 30 Mins', 'service-finder'),180 => esc_html__('3 Hrs', 'service-finder'),210 => esc_html__('3 Hr 30 Mins', 'service-finder'),240 => esc_html__('4 Hrs', 'service-finder'));
		}
            ?>
          <select class="sf-select-box form-control sf-form-control" name="slotinterval" data-live-search="true" title="<?php esc_html_e('Slot Interval', 'service-finder'); ?>">
            <option value=""><?php esc_html_e('Slot Interval', 'service-finder'); ?></option>
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
              
        <div class="col-md-3">
        <div class="form-group">
          <input type="text" name="maxbooking" class="form-control sf-form-control" placeholder="<?php esc_html_e('Max bookings','service-finder') ?>">
        </div>
        </div> 
        
        <div class="sf-availability-days clearfix">
		<?php 
		foreach($days as $day){
		$display_weekdayname = service_finder_trans_weekdays($day);
		?>
		<div class="col-md-4">
          <div class="checkbox sf-radio-checkbox">
              <input type="checkbox" id="bulkchk-<?php echo esc_attr(strtolower($day)); ?>" name="weekdays[]" value="<?php echo esc_attr(strtolower($day)); ?>">
              <label for="bulkchk-<?php echo esc_attr(strtolower($day)); ?>"><?php echo esc_html($display_weekdayname); ?></label>
            </div>
        </div>
		<?php
		}
		?>    
        </div>
        
        <div class="col-md-4">
        <div class="form-group">
            <div class="input-group-btn">
                <button type="submit" class="btn btn-primary addstarttime"><i class="fa fa-plus"></i> <?php esc_html_e('Save Slots','service-finder') ?></button>
            </div> 
        </div>
        </div>
    
        </form>
   	   </div> 
       </div>
    </div> 
    <div class="tabbable sf-tabs-admin">
      <ul class="nav nav-tabs" id="subTab">
        <?php foreach($days as $day){ 
    
                                                $class = ($day == 'monday') ? 'active' : '';
                                                switch($day){
                                                case 'monday':
                                                    $dayname = esc_html__('Monday','service-finder');
                                                    break;
                                                case 'tuesday':
                                                    $dayname = esc_html__('Tuesday','service-finder');
                                                    break;
                                                case 'wednesday':
                                                    $dayname = esc_html__('Wednesday','service-finder');
                                                    break;
                                                case 'thursday':
                                                    $dayname = esc_html__('Thursday','service-finder');
                                                    break;
                                                case 'friday':
                                                    $dayname = esc_html__('Friday','service-finder');
                                                    break;
                                                case 'saturday':
                                                    $dayname = esc_html__('Saturday','service-finder');
                                                    break;
                                                case 'sunday':
                                                    $dayname = esc_html__('Sunday','service-finder');
                                                    break;						
                                                }
    
                                                echo '<li class="'.sanitize_html_class($class).'"><a data-toggle="tab" href="#'.$day.'">'.$dayname.'</a></li>';
    
                                                }?>
      </ul>
      <div class="tab-content">
       <?php foreach($days as $day){ ?>
    <div id="<?php echo esc_attr($day); ?>" class="tab-pane <?php echo ($day == 'monday') ? 'active' : '';?>">
    <div class="tabs-inr">
    	<ul class="selected-time list-unstyled">
                <?php
				$starttimeinfo = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->starttime.' WHERE provider_id = %d AND day = "%s"',$globalproviderid,$day));
				if(!empty($starttimeinfo)){
					foreach($starttimeinfo as $starttime){
						$start = new DateTime($starttime->start_time);
						
						if($time_format){
							$startval = $start->format('H:i');
						}else{
							$startval = $start->format('h:i a');
						}
						
						
						echo '<li data-tid="'.esc_attr($starttime->id).'">

								<div class="input-group">
		
									<input type="text" value="'.esc_attr($starttime->max_bookings).'" id="maxbooking_'.esc_attr($starttime->id).'" name="maxbooking" class="form-control sf-form-control" placeholder="'.esc_html__('Number of bookings allowed','service-finder').'">
		
									<div class="input-group-btn">
		
										<button type="button" class="btn btn-primary">'.$startval.'</button>
		
										<button type="button" class="btn btn-danger removestarttime" title="'.esc_html__('Delete','service-finder').'"><i class="fa fa-remove"></i></button>
										
										<button type="button" class="btn btn-primary updatemaxbooking" title="'.esc_html__('Update','service-finder').'"><i class="fa fa-refresh"></i></button>
		
									</div>
		
								</div>
		
							</li>';					
					}
				}
				
				?>
              </ul>
    </div>
    </div>
    <?php
    
       }
    
       ?>
      </div>
    </div>
  </div>

