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
<!--Availability Template-->

<div class="auther-availability form-inr clearfix">
    <div class="alert alert-warning">
      <?php esc_html_e('You need to put available hours for the booking system to work', 'service-finder'); ?>
    </div>
    <p>
      <?php esc_html_e('Set Up time slots for each week day', 'service-finder'); ?>
    </p>
    <div class="section-content">
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
              <input type="checkbox" id="chk-<?php echo esc_attr(strtolower($day)); ?>" name="weekdays[]" value="<?php echo esc_attr(strtolower($day)); ?>">
              <label for="chk-<?php echo esc_attr(strtolower($day)); ?>"><?php echo esc_html($display_weekdayname); ?></label>
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
    <div class="tabbable sf-tabs-admin">
      <ul class="nav nav-tabs" id="subTab">
        <?php 									foreach($days as $day){ 

                                                $class = ($day == 'monday') ? 'active' : '';
												$dayname = service_finder_day_translate($day);

												echo '<li class="'.sanitize_html_class($class).'"><a data-toggle="tab" href="#'.$day.'">'.$dayname.'</a></li>';

                                                }?>
      </ul>
      <div class="tab-content">
        <?php 

   

										   foreach($days as $day){

										   ?>
        <div id="<?php echo esc_attr($day); ?>" class="tab-pane <?php echo ($day == 'monday') ? 'active' : '';?>">
          <div class="tabs-inr">
            <form class="form-availability input_pro_slots <?php echo esc_attr($day); ?>-timeslots" id="<?php echo esc_attr($day); ?>-timeslots" method="post">
              <?php

														$liday = ucfirst(str_replace("day","",$day));

														$timeslots = $getTimeSlot->service_finder_getTimeSlots($day,$globalproviderid);

														$liarr = array();
														$endarr = array();

														if(!empty($timeslots)){

															foreach($timeslots as $timeslot){

															$slotids = explode('-',$timeslot->slotids);

                                                        

															$startid = explode($liday,$slotids[0]);

															$endid = explode($liday,$slotids[1]);

															

															$startid = $startid[1];

															$endid = $endid[1];
															
															
															$endarr[] = $endid;


																for ($x = $startid; $x <= $endid-1; $x++) {

																	$liarr[] = $x;

																}

															}

														}	

														

														?>
	  <?php if($time_format){ ?>
      <ul class="time-zone clearfix">
      	<?php
		$begin = new DateTime("00:00");
		$end   = new DateTime("24:00");
		
		$interval = DateInterval::createFromDateString($slot_interval.' min');
		
		$times    = new DatePeriod($begin, $interval, $end);
		$k = 1;
		foreach ($times as $time) {
			if(in_array($k,$endarr)){
				$datapoint = 'data-point="endpoint"';
			}else{
				$datapoint = '';
			}
			if(in_array($k,$liarr)){
				$listyle = 'style="background-color: rgb(234, 234, 234);" class="disable-slot"';
			}else{
				$listyle = '';
			}
			echo '<li id="li'.esc_attr($liday).$k.'" '.$datapoint.' '.$listyle.'>'.$time->format('H:i').'</li>';
			$k++;
		}
		
		?>
      </ul>	
      <?php }else{ ?>
      <h5><?php esc_html_e('AM', 'service-finder') ?></h5>
      <ul class="time-zone clearfix">
      	<?php
		$begin = new DateTime("00:00");
		$end   = new DateTime("12:00");
		
		$interval = DateInterval::createFromDateString($slot_interval.' min');
		
		$times    = new DatePeriod($begin, $interval, $end);
		$k = 1;
		foreach ($times as $time) {
			if(in_array($k,$endarr)){
				$datapoint = 'data-point="endpoint"';
			}else{
				$datapoint = '';
			}
			if(in_array($k,$liarr)){
				$listyle = 'style="background-color: rgb(234, 234, 234);" class="disable-slot"';
			}else{
				$listyle = '';
			}
			echo '<li id="li'.esc_attr($liday).$k.'" '.$datapoint.' '.$listyle.'>'.$time->format('h:i').'</li>';
			$k++;
		}
		
		?>
      </ul>
      <h5><?php esc_html_e('PM', 'service-finder') ?></h5>
      <ul class="time-zone clearfix">
      	<?php
		$begin = new DateTime("12:00");
		$end   = new DateTime("24:00");
		
		$interval = DateInterval::createFromDateString($slot_interval.' min');
		
		$times    = new DatePeriod($begin, $interval, $end);
		foreach ($times as $time) {
			if(in_array($k,$endarr)){
				$datapoint = 'data-point="endpoint"';
			}else{
				$datapoint = '';
			}
			if(in_array($k,$liarr)){
				$listyle = 'style="background-color: rgb(234, 234, 234);" class="disable-slot"';
			}else{
				$listyle = '';
			}
			echo '<li id="li'.esc_attr($liday).$k.'" '.$datapoint.' '.$listyle.'>'.$time->format('h:i').'</li>';
			$k++;
		}
		
		?>
      </ul>
      <?php } ?>
              
              <ul class="selected-time">
                <?php
				$time_format = (!empty($service_finder_options['time-format'])) ? $service_finder_options['time-format'] : '';

                                                        if(!empty($timeslots)){

															foreach($timeslots as $timeslot){
															
															if($time_format){
																$showslots = date('H:i',strtotime($timeslot->start_time)).'-'.date('H:i',strtotime($timeslot->end_time));
															}else{
																$showslots = date('h:i a',strtotime($timeslot->start_time)).'-'.date('h:i a',strtotime($timeslot->end_time));
															}

															echo '<li data-ids="'.esc_attr($timeslot->slotids).'">

                                                                <div class="input-group">

                                                                    <input type="text" value="'.esc_attr($timeslot->max_bookings).'" class="form-control sf-form-control" placeholder="'.esc_html__('Number of bookings allowed','service-finder').'">

                                                                    <div class="input-group-btn">

                                                                        <button type="button" class="btn btn-primary">'.$showslots.'</button>

                                                                        <button type="button" class="btn btn-danger removeSlot"><i class="fa fa-remove"></i></button>

                                                                    </div>

                                                                </div>

                                                            </li>';

															}

														}

														?>
              </ul>
              <div class="form-group">
                <button <?php echo (empty($timeslots)) ? 'style="display:none;"' : ''; ?> class="btn btn-primary margin-r-10 saveslots" name="Save" type="button" >
                <?php esc_html_e('Submit', 'service-finder'); ?>
                </button>
              </div>
            </form>
          </div>
        </div>
        <?php

										   }

										   ?>
      </div>
    </div>
  </div>

<!--Modal Popup-->
<form method="post" class="get-avl default-hidden" id="getavl">
    <div class="clearfix row input_fields_wrap">
      
      <div class="col-md-6">
        <div class="form-group form-group padding-tb-5 font-size-18">
            <strong class="text-primary"><?php esc_html_e('From:', 'service-finder'); ?></strong> <span id="startval"></span>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <select class="sf-select-box form-control sf-form-control" name="totime" id="totime" data-live-search="true" title="<?php esc_html_e('To', 'service-finder'); ?>">
            <option value=""><?php esc_html_e('To', 'service-finder'); ?></option>
          </select>  
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">
      <?php esc_html_e('Cancel', 'service-finder'); ?>
      </button>
      <input type="button" class="btn btn-primary addslots" name="addslots" value="<?php esc_html_e('Ok', 'service-finder'); ?>" />
    </div>
</form>
