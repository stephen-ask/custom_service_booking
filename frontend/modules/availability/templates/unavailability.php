<?php

/*****************************************************************************

*

*	copyright(c) - aonetheme.com - Service Finder Team

*	More Info: http://aonetheme.com/

*	Coder: Service Finder Team

*	Email: contact@aonetheme.com

*

******************************************************************************/



$wpdb = service_finder_plugin_global_vars('wpdb');

$service_finder_Params = service_finder_plugin_global_vars('service_finder_Params');

require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/book-now/BookNow.php';

$userCap = service_finder_get_capability($globalproviderid);



wp_add_inline_script( 'service_finder-js-unavailability-form', '/*Declare global variable*/

var numberofdays;
var unavl_type;
var user_id = "'.$globalproviderid.'";', 'after' );



?>

<!--UnAvailability Template-->
<div class="panel panel-default">
<div class="panel-heading sf-panel-heading">
  <h3 class="panel-tittle m-a0"><span class="fa fa-calendar"></span> <?php echo (!empty($service_finder_options['label-set-unavailability'])) ? esc_html($service_finder_options['label-set-unavailability']) : esc_html__('Set UnAvailability', 'service-finder'); ?> </h3>
</div>
<div class="panel-body sf-panel-body padding-30">
  <div class="margin-b-30 text-right">

    <button class="btn btn-primary" data-toggle="modal" data-target="#setunavailability" type="button"><i class="fa fa-plus"></i>

    <?php esc_html_e('Set New UnAvailability', 'service-finder'); ?>

    </button>

  </div>

  <table id="unavilability-grid" class="table table-striped table-bordered">

    <thead>

      <tr>

        <th> <div class="checkbox sf-radio-checkbox">

            <input type="checkbox" id="bulkUnAvilabilityDelete">

            <label for="bulkUnAvilabilityDelete"></label>

          </div>

          <button class="btn btn-danger btn-xs" id="deleteUnAvilabilityTriger" title="Delete"><i class="fa fa-trash-o"></i></button></th>

        <th><?php esc_html_e('Date', 'service-finder'); ?></th>

        <th><?php esc_html_e('Day', 'service-finder'); ?></th>

        <th><?php esc_html_e('Timeslots', 'service-finder'); ?></th>

        <th><?php esc_html_e('Whole Day', 'service-finder'); ?></th>
        
        <th><?php esc_html_e('Member Name', 'service-finder'); ?></th>

        <th><?php esc_html_e('Action', 'service-finder'); ?></th>

      </tr>

    </thead>

  </table>

  <!--Template for set Unavailability modal popup box-->

  <div id="setunavailability" class="modal fade" tabindex="-1" role="dialog" data-proid="<?php echo esc_attr($globalproviderid) ?>">

    <div class="modal-dialog">

      <div class="modal-content">

        <form method="post" class="set-new-unavailability">

          <div class="modal-header">

            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

            <h4 class="modal-title">

              <?php esc_html_e('Set New UnAvailability', 'service-finder'); ?>

            </h4>

          </div>

          <div class="modal-body clearfix row input_fields_wrap">

            <?php
			if(!empty($userCap)){
			if(in_array('staff-members',$userCap)){
			?>
            <div class="col-md-6">

              <div class="form-group form-inline">

                <label>

                <?php esc_html_e('Team Members', 'service-finder'); ?>

                </label>

                <br>
                <select class="sf-select-box form-control sf-form-control" name="avlmemberid" title="<?php esc_html_e('Select Team Members', 'service-finder'); ?>">
                <?php
				echo '<option value="0">'.esc_html__('For All Members', 'service-finder').'</option>';
				$members = service_finder_get_team_members($globalproviderid);
                if(!empty($members)){
				foreach($members as $member){
					echo '<option value="'.$member->id.'">'.$member->member_name.'</option>';
				}
				}
				?>
                </select>

              </div>

            </div>
            <?php
            }
			}
			?>
            
            <div class="col-md-12">

              <div class="form-group" id="loadavlcalendar">

                <div id="availability-calendar"></div>

              </div>

            </div>

            <div class="col-md-12" id="timeslotbox">

              <div class="form-group form-inline">

                <ul class="protimelist list-inline">

                  <?php esc_html_e('Please select timeslot', 'service-finder'); ?>

                </ul>

              </div>

            </div>

            <div class="col-md-6">

              <div class="form-group form-inline">

                <label>

                <?php esc_html_e('Whole Day', 'service-finder'); ?>

                </label>

                <br>

                <div class="radio sf-radio-checkbox">

                  <input id="wholeday" type="checkbox" name="wholeday" value="yes">

                  <label for="wholeday">

                  <?php esc_html_e('Yes', 'service-finder'); ?>

                  </label>

                </div>

              </div>

            </div>

          </div>

          <div class="modal-footer">

            <button type="button" class="btn btn-default" data-dismiss="modal">

            <?php esc_html_e('Cancel', 'service-finder'); ?>

            </button>

            <input type="submit" class="btn btn-primary" name="set-unavailability" value="<?php esc_html_e('Set UnAvailability', 'service-finder'); ?>" />

          </div>

        </form>

      </div>

    </div>

  </div>

  <!--Template for edit Unavailability modal popup box-->

  <form method="post" class="edit-unavailability default-hidden" id="editunavailability">

    <div class="clearfix row input_fields_wrap">

      <?php
	  if(!empty($userCap)){
	  if(in_array('staff-members',$userCap)){
	  ?>
      <div class="col-md-6">

              <div class="form-group form-inline">

                <label>

                <?php esc_html_e('Member Name: ', 'service-finder'); ?>

                </label>

                <span id="editedmembername"><strong></strong></span>

              </div>

            </div>
      <?php
      }
	  }
	  ?>      
            
      <div class="col-md-12">

        <div class="form-group" id="loadcalendar">

          <div id="editavailability-calendar"></div>

        </div>

      </div>

      <div class="col-md-12">

        <div class="form-group form-inline">

          <ul class="protimelist list-inline">

          </ul>

        </div>

      </div>

      <div class="col-md-6">

        <div class="form-group form-inline">

          <label>

          <?php esc_html_e('Whole Day', 'service-finder'); ?>

          </label>

          <br>

          <div class="radio sf-radio-checkbox">

            <input id="editwholeday" type="checkbox" name="wholeday" value="yes">

            <label for="editwholeday">

            <?php esc_html_e('Yes', 'service-finder'); ?>

            </label>

          </div>

        </div>

      </div>

    </div>

    <div class="modal-footer">

      <button type="button" class="btn btn-default" data-dismiss="modal">

      <?php esc_html_e('Cancel', 'service-finder'); ?>

      </button>
	  <input type="hidden" name="editavlmemberid">	
      <input type="submit" class="btn btn-primary" name="edit-unavailability" value="<?php esc_html_e('Update', 'service-finder'); ?>" />

    </div>

  </form>
  
  <!--Template for multiple date set Unavailability modal popup box-->
  
  <form method="post" class="multidatepopover default-hidden" id="multidatepopover">

    <div class="clearfix row input_fields_wrap">

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
                <div class="radio sf-radio-checkbox">

                  <input id="slots" type="radio" name="unavl_type" value="slots">

                  <label for="slots">

                  <?php esc_html_e('Slots', 'service-finder'); ?>

                  </label>

                </div>

              </div>

            </div>
            
          <div class="col-md-12" id="numdays">
    
                  <div class="form-group form-inline">
    
                    <label>
    
                    <?php esc_html_e('Number of Month/Week/Day/Slots', 'service-finder'); ?>
    
                    </label>
    
                    <br>
    
                    <input type="text" class="form-control sf-form-control" name="number_of_days" plaveholder="<?php esc_html_e('Number of Month/Week/Day/Slots', 'service-finder'); ?>">
    
                  </div>
    
                </div>
                
			<div class="col-md-12" id="timeslotpopover" style="display:none">

              <div class="form-group form-inline">

                <ul class="protimelist list-inline">

                  <?php esc_html_e('Please select timeslot', 'service-finder'); ?>

                </ul>

              </div>

            </div>                      

    </div>

  </form>
</div>
</div>