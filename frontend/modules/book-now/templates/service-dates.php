<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$service_finder_options = get_option('service_finder_options');
?>
<!--Claim Business form in modal popup box Start-->

<div id="servicedate-Modal" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">
          <?php echo esc_html__('Select Date','service-finder'); ?>
        </h4>
      </div>
      <div class="modal-body clearfix row">
          <div id="stepbox1" style="display:none">
			
            <div id="memberslist" style="display:none">
            <div class="col-md-12">
                <div class="form-group form-inline">
    
                    <label>
    
                    <?php esc_html_e('Select Member', 'service-finder'); ?>
    
                    </label>
    
                    <br>
    
                    <select name="members_list" class="form-control sf-form-control sf-select-box">
                	</select>
    
                  </div>
                
            </div>
            <div id="sf-bookingmember-image" style="display:none">
            </div>
            </div>
            <div id="numberofhours" style="display:none">
            <div class="col-md-12">
    
                  <div class="form-group form-inline">
    
                    <label>
    
                    <?php esc_html_e('Number of Hours', 'service-finder'); ?>
    
                    </label>
    
                    <br>
    
                    <input type="text" class="form-control sf-form-control" name="number_of_hours" plaveholder="<?php esc_html_e('Number of Hours', 'service-finder'); ?>">
    
                  </div>
    
                </div>
            </div>
            <div id="numberofdays" style="display:none">    
            <div class="col-md-12">

              <div class="form-group form-inline">

                <label>

                <?php esc_html_e('Select Month/Week/Day/Slots', 'service-finder'); ?>

                </label>

                <br>

                <div class="radio sf-radio-checkbox">

                  <input id="months" type="radio" name="unavl_type" value="months">

                  <label for="months">

                  <?php esc_html_e('Month', 'service-finder'); ?>

                  </label>

                </div>
                <div class="radio sf-radio-checkbox">

                  <input id="weeks" type="radio" name="unavl_type" value="weeks">

                  <label for="weeks">

                  <?php esc_html_e('Week', 'service-finder'); ?>

                  </label>

                </div>
                <div class="radio sf-radio-checkbox">

                  <input id="days" type="radio" name="unavl_type" value="days" checked="checked">

                  <label for="days">

                  <?php esc_html_e('Day', 'service-finder'); ?>

                  </label>

                </div>

              </div>

            </div>
            
            <div class="col-md-12" id="numdays">
    
                  <div class="form-group form-inline">
    
                    <label>
    
                    <?php esc_html_e('Number of Month/Week/Day', 'service-finder'); ?>
    
                    </label>
    
                    <br>
    
                    <input type="text" class="form-control sf-form-control" name="number_of_days" plaveholder="<?php esc_html_e('Number of Month/Week/Day', 'service-finder'); ?>">
    
                  </div>
    
                </div>
            </div>
          </div>
          <div id="stepbox2" style="display:none">
          <div class="col-md-12" id="loadservicecalendar">
            <div id="service-calendar"></div>
          </div>
          <div class="col-md-12">
            <ul class="indiget-booking">
              <li class="allbooked"><b></b>
                <?php esc_html_e('All Booked', 'service-finder'); ?>
              </li>
              <li class="unavailable"><b></b>
                <?php esc_html_e('Unavailable', 'service-finder'); ?>
              </li>
            </ul>
          </div>
		  <div class="col-md-12">
			<ul class="timeslots timelist list-inline">
			<span class="notavail">
			<?php esc_html_e('Please select date to show timeslot.', 'service-finder'); ?>
			</span>
			</ul>
			</div>
          <?php
		  if(!empty($userCap)){
		  if(in_array('availability',$userCap) && in_array('bookings',$userCap)){
		  ?>
            <div class="col-md-12" id="bookingslot-box">
            <ul class="timeslots timelist list-inline">
            <span class="notavail">
            <?php esc_html_e('Please select date to show timeslot.', 'service-finder'); ?>
            </span>
            </ul>
            </div>
          <?php 
		  }
		  }
		  ?>
          </div>
          </div>
        <div class="servicedate-error-bx"></div>
      <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">
          <?php esc_html_e('Cancel', 'service-finder'); ?>
          </button>
          <input type="hidden" name="serviceid" value="">
          <input type="hidden" name="costtype" value="">
          <input type="hidden" name="providerhours" value="">
           <input type="hidden" name="dates" value="">
           <input type="button" name="nextstepbox" value="<?php esc_html_e('Next', 'service-finder'); ?>" class="btn btn-primary">
           <input type="button" name="backstepbox" value="<?php esc_html_e('Back', 'service-finder'); ?>" class="btn btn-primary">
          <input type="submit" value="<?php esc_html_e('Continue', 'service-finder'); ?>" name="submit" class="btn btn-primary add-service-date">
        </div>
    </div>
  </div>
</div>
<!--Claim Business form in modal popup box End-->
