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

$currUser = wp_get_current_user(); 

$service_finder_options = get_option('service_finder_options');

wp_add_inline_script( 'service_finder-js-bookings-form', '/*Declare global variable*/

var user_id = "'.$currUser->ID.'";', 'after' );

?>

<div class="panel panel-default">

<div class="panel-heading sf-panel-heading">

  <h3 class="panel-tittle m-a0"><span class="fa fa-hand-o-up"></span> <?php esc_html_e('Bookings', 'service-finder'); ?> </h3>

</div>

<div class="panel-body sf-panel-body padding-30">

  <!--Display customer upcoming/past bookings-->

<div class="booking-list bg-white" >

  <div class="tabbable">

    <ul class="nav nav-tabs">

      <li class="active"><a data-toggle="tab" href="#upcoming">

        <?php esc_html_e('Upcoming', 'service-finder'); ?>

        </a></li>

      <li><a data-toggle="tab" href="#past">

        <?php esc_html_e('Past', 'service-finder'); ?>

        </a></li>

    </ul>

    <div class="tab-content">

      <!--upcoming bookings-->

      <div id="upcoming" class="tab-pane fade in active">

        <table id="upcomingbookings-customer-grid" class="table table-striped margin-0 booking-listing">

          <thead>

            <tr>

              <th></th>

              <th></th>

              <th></th>

              <th></th>

            </tr>

          </thead>

        </table>
        <div class="sf-couponcode-popup-overlay"> </div>	

        <div id="booking-details" class="hidden"> </div>

      </div>

      <!--past bookings-->

      <div id="past" class="tab-pane fade">

        <table id="pastbookings-customer-grid" class="table table-striped margin-0 booking-listing">

          <thead>

            <tr>

              <th></th>

              <th></th>

              <th></th>

              <th></th>

            </tr>

          </thead>

        </table>
        <div class="sf-couponcode-popup-overlay"> </div>	

        <div id="pastbooking-details" class="hidden"> </div>

      </div>

    </div>

  </div>

</div>

<!--Display Services-->

<div class="services-list bg-white" style="display:none" >

 <div class="backbtn">	  

 <a href="javascript:;" class="btn btn-primary backtobookings"><?php esc_html_e('Back', 'service-finder'); ?></a>

 </div>

  <table id="services-grid" class="table table-striped table-bordered">

    <thead>

      <tr>

        <th><?php esc_html_e('Service Name', 'service-finder'); ?></th>

        <th><?php esc_html_e('Date', 'service-finder'); ?></th>

        <th><?php esc_html_e('Start Time', 'service-finder'); ?></th>

        <th><?php esc_html_e('End Time', 'service-finder'); ?></th>

        <th><?php esc_html_e('Full Day', 'service-finder'); ?></th>

        <th><?php esc_html_e('Status', 'service-finder'); ?></th>

        <th><?php esc_html_e('Member Name', 'service-finder'); ?></th>

        <th><?php esc_html_e('Action', 'service-finder'); ?></th>

      </tr>

    </thead>

  </table>

</div>

<!--Add feedback form for bookings-->

<form method="post" class="add-feedback default-hidden" id="addFeedback">

  <div class="clearfix row input_fields_wrap">

    <?php 

	$ratingstyle = (!empty($service_finder_options['rating-style'])) ? $service_finder_options['rating-style'] : '';

		

	if($ratingstyle == 'custom-rating'){ 

	echo '<div id="customrating">';

	echo '</div>';

	}else{

	echo '<div class="col-md-12">

		  <div class="form-group rating_bx">

			<input id="comment-rating" name="comment-rating" value="" type="number" class="rating" min=0 max=5 step=0.5 data-size="sm">

		  </div>

		</div>';

	}

	?>

    <div class="col-md-12">

      <div class="form-group">

        <textarea name="comment" id="comment" class="form-control sf-form-control" rows="" cols="4" placeholder="<?php esc_html_e('Enter Some Comments', 'service-finder'); ?>"></textarea>

      </div>

    </div>

  </div>

  <div class="modal-footer">

    <button type="button" class="btn btn-default" data-dismiss="modal">

    <?php esc_html_e('Cancel', 'service-finder'); ?>

    </button>

    <input type="submit" class="btn btn-primary" name="add-feedback" value="<?php esc_html_e('Submit', 'service-finder'); ?>" />

  </div>

</form>

<!--View feedback for bookings-->

<form method="post" class="view-feedback default-hidden" id="viewFeedback">

  <div class="clearfix row input_fields_wrap">

    <?php 

	$ratingstyle = (!empty($service_finder_options['rating-style'])) ? $service_finder_options['rating-style'] : '';

		

	if($ratingstyle == 'custom-rating'){ 

	echo '<div id="displaycustomrating">';

	echo '</div>';

	}else{

	echo '<div class="col-md-12">

		  <div class="form-group">

			<input id="show-comment-rating" value="" type="number" class="rating" min=0 max=5 step=0.5 data-size="sm" disabled="disabled">

		  </div>

		</div>';

	}

	?>

    <div class="col-md-12">

      <div class="form-group">

        <p id="showcomment"></p>

      </div>

    </div>

  </div>

</form>

<!--Template for edit bookings modal popup box-->

<form method="post" class="edit-booking default-hidden" id="editbooking">

  <div class="clearfix row input_fields_wrap">

    <div class="col-md-12">

      <div class="form-group" id="loadcalendar">

        <div id="editbooking-calendar"></div>

      </div>

    </div>

    <div class="col-md-12">

      <div class="form-group form-inline">

        <ul class="timeslots protimelist list-inline">

        </ul>

        <div class="col-md-12" id="members"> </div>

      </div>

    </div>

  </div>

  <div class="modal-footer">

    <button type="button" class="btn btn-default" data-dismiss="modal">

    <?php esc_html_e('Cancel', 'service-finder'); ?>

    </button>

    <input type="submit" class="btn btn-primary" name="edit-booking" value="<?php esc_html_e('Update', 'service-finder'); ?>" />

    <input type="hidden" id="boking-slot" data-slot="" name="boking-slot" value="" />

    <input type="hidden" id="memberid" data-memid="" name="memberid" value="" />

    <input type="hidden" id="date" name="date" value="" />

    <input type="hidden" id="booking_id" name="booking_id" value="" />

    <input type="hidden" id="provider" name="provider" value="" />

  </div>

</form>



<div id="edit-servicedate" class="modal fade" tabindex="-1" role="dialog">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

        <h4 class="modal-title">

          <?php echo esc_html__('Edit Service Schedule','service-finder'); ?>

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

          <input type="hidden" name="providerid" value="">

          <input type="hidden" name="serviceid" value="">

          <input type="hidden" name="costtype" value="">

          <input type="hidden" name="totalnumber" value="">

          <input type="hidden" name="bookedserviceid" value="">

          <input type="hidden" name="bookingid" value="">

          <input type="hidden" name="memberid" value="">

          <input type="hidden" name="slot" value="">

          <input type="hidden" name="date" value="">

          <input type="hidden" name="dates" value="">

          <input type="button" name="nextstepbox" value="<?php esc_html_e('Next', 'service-finder'); ?>" class="btn btn-primary">

          <input type="button" name="backstepbox" value="<?php esc_html_e('Back', 'service-finder'); ?>" class="btn btn-primary">

          <input type="submit" value="<?php esc_html_e('Update Schedule', 'service-finder'); ?>" name="submit" class="btn btn-primary update-service-date">

        </div>

    </div>

  </div>

</div>

</div>

</div>



