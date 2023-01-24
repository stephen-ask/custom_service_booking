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
$service_finder_options = get_option('service_finder_options');

if(service_finder_getUserRole($current_user->ID) == 'Provider'){
$userInfo = service_finder_getCurrentUserInfo();
}else{
$userInfo = service_finder_getUserInfo($globalproviderid);
}

$userCap = service_finder_get_capability($globalproviderid);

$settings = service_finder_getProviderSettings($globalproviderid);

$payment_methods = (!empty($service_finder_options['payment-methods'])) ? $service_finder_options['payment-methods'] : '';

$google_calendar = (!empty($settings['google_calendar'])) ? $settings['google_calendar'] : '';
$paymentoption = (!empty($settings['paymentoption'])) ? $settings['paymentoption'] : '';
$booking_process = (!empty($settings['booking_process'])) ? $settings['booking_process'] : '';
$future_bookings_availability = (!empty($settings['future_bookings_availability'])) ? $settings['future_bookings_availability'] : '';
$buffertime = (!empty($settings['buffertime'])) ? $settings['buffertime'] : '';
$availability_based_on = (!empty($settings['availability_based_on'])) ? $settings['availability_based_on'] : '';
$slot_interval = (!empty($settings['slot_interval'])) ? $settings['slot_interval'] : '';
$offers_based_on = (!empty($settings['offers_based_on'])) ? $settings['offers_based_on'] : '';
$booking_date_based_on = (!empty($settings['booking_date_based_on'])) ? $settings['booking_date_based_on'] : '';
$booking_option = (!empty($settings['booking_option'])) ? $settings['booking_option'] : '';
$booking_assignment = (!empty($settings['booking_assignment'])) ? $settings['booking_assignment'] : '';
$members_available = (!empty($settings['members_available'])) ? $settings['members_available'] : '';
$booking_basedon = (!empty($settings['booking_basedon'])) ? $settings['booking_basedon'] : '';
$mincost = (isset($settings['mincost'])) ? $settings['mincost'] : '';
$paypalusername = (!empty($settings['paypalusername'])) ? $settings['paypalusername'] : '';
$paypalpassword = (!empty($settings['paypalpassword'])) ? $settings['paypalpassword'] : '';
$paypalsignatue = (!empty($settings['paypalsignatue'])) ? $settings['paypalsignatue'] : '';
$stripesecretkey = (!empty($settings['stripesecretkey'])) ? $settings['stripesecretkey'] : '';
$stripepublickey = (!empty($settings['stripepublickey'])) ? $settings['stripepublickey'] : '';
$wired_description = (!empty($settings['wired_description'])) ? $settings['wired_description'] : '';
$wired_instructions = (!empty($settings['wired_instructions'])) ? $settings['wired_instructions'] : '';
$twocheckoutaccountid = (!empty($settings['twocheckoutaccountid'])) ? $settings['twocheckoutaccountid'] : '';
$twocheckoutpublishkey = (!empty($settings['twocheckoutpublishkey'])) ? $settings['twocheckoutpublishkey'] : '';
$twocheckoutprivatekey = (!empty($settings['twocheckoutprivatekey'])) ? $settings['twocheckoutprivatekey'] : '';
$payumoneymid = (!empty($settings['payumoneymid'])) ? $settings['payumoneymid'] : '';
$payumoneykey = (!empty($settings['payumoneykey'])) ? $settings['payumoneykey'] : '';
$payumoneysalt = (!empty($settings['payumoneysalt'])) ? $settings['payumoneysalt'] : '';
$payulatammerchantid = (!empty($settings['payulatammerchantid'])) ? $settings['payulatammerchantid'] : '';
$payulatamapilogin = (!empty($settings['payulatamapilogin'])) ? $settings['payulatamapilogin'] : '';
$payulatamapikey = (!empty($settings['payulatamapikey'])) ? $settings['payulatamapikey'] : '';
$payulatamaccountid = (!empty($settings['payulatamaccountid'])) ? $settings['payulatamaccountid'] : '';

$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? esc_html($service_finder_options['pay_booking_amount_to']) : '';

$bankaccount_info_section = (isset($service_finder_options['bank-account-info-section'])) ? esc_html($service_finder_options['bank-account-info-section']) : '';

$adminavailabilitybasedon = (!empty($service_finder_options['availability-based-on'])) ? esc_html($service_finder_options['availability-based-on']) : '';
$adminoffersbasedon = (!empty($service_finder_options['offers-based-on'])) ? esc_html($service_finder_options['offers-based-on']) : '';
$datestyle = (!empty($service_finder_options['booking-date-style'])) ? esc_html($service_finder_options['booking-date-style']) : '';

$paid_booking = (!empty($service_finder_options['paid-booking'])) ? $service_finder_options['paid-booking'] : '';
if(!$paid_booking){
$free_booking = true;
}else{
$free_booking = (!empty($service_finder_options['free-booking'])) ? $service_finder_options['free-booking'] : '';
}
?>
<form class="booking-settings" method="post">
<!--Booking Settings Section-->
<div class="panel panel-default paypal-pay">
<div class="panel-heading sf-panel-heading">
  <h3 class="panel-tittle m-a0"><span class="fa fa-cog"></span> <?php esc_html_e('Booking Settings', 'service-finder'); ?> </h3>
</div>
<div class="panel-body sf-panel-body padding-30">
  <div class="row">
    <div class="col-lg-12">
      <div class="form-group">
        <label>
        <?php esc_html_e('Short Description for Booking', 'service-finder'); ?>
        </label>
        <div class="input-group"> <i class="input-group-addon fixed-w fa fa-pencil v-align-t"></i>
          <textarea class="form-control sf-form-control" maxlength="200" rows="3" cols="" name="booking_description"><?php echo esc_textarea($userInfo['booking_description']) ?></textarea>
        </div>
      </div>
    </div>
    <div class="col-lg-12" id="bookingalert" <?php echo ($booking_process == 'off' || $booking_process == '') ? 'style="display: none;"' : ''; ?>>
        <div class="alert alert-warning">
          <?php esc_html_e('Please set available times for the booking system to work', 'service-finder'); ?>
        </div>
    </div>
    <div class="col-lg-6">
      <div class="form-group form-inline">
        <label>
        <?php esc_html_e('Booking Process', 'service-finder'); ?>
        </label>
        <br>
        <div class="radio sf-radio-checkbox">
          <input id="on" type="radio" name="booking_process" value="on" <?php echo ($booking_process == 'on') ? 'checked' : ''; ?>>
          <label for="on">
          <?php esc_html_e('On', 'service-finder'); ?>
          </label>
        </div>
        <div class="radio sf-radio-checkbox">
          <input id="off" type="radio" name="booking_process" value="off" <?php echo ($booking_process == 'off' || $booking_process == '') ? 'checked' : ''; ?>>
          <label for="off">
          <?php esc_html_e('Off', 'service-finder'); ?>
          </label>
        </div>
      </div>
    </div>
    <?php if($adminavailabilitybasedon == 'both'){ ?>
    <div class="col-lg-6">
      <div class="form-group form-inline">
        <label>
        <?php esc_html_e('Availability Based On', 'service-finder'); ?>
        </label>
        <br>
        <div class="radio sf-radio-checkbox">
          <input id="timeslots" type="radio" name="availability_based_on" value="timeslots" <?php echo ($availability_based_on == 'timeslots' || $availability_based_on == '') ? 'checked' : ''; ?>>
          <label for="timeslots">
          <?php esc_html_e('Time Slots', 'service-finder'); ?>
          </label>
        </div>
        <div class="radio sf-radio-checkbox">
          <input id="starttime" type="radio" name="availability_based_on" value="starttime" <?php echo ($availability_based_on == 'starttime') ? 'checked' : ''; ?>>
          <label for="starttime">
          <?php esc_html_e('Start Time', 'service-finder'); ?>
          </label>
        </div>
      </div>
    </div>
    <?php } ?>
    <div class="col-lg-6">
      <div class="form-group form-inline">
        <label>
        <?php esc_html_e('Slot Interval', 'service-finder'); ?>
        </label>
        <br>
        <div class="radio sf-radio-checkbox">
          <input id="15mins" type="radio" name="slot_interval" value="15" <?php echo ($slot_interval == '15') ? 'checked' : ''; ?>>
          <label for="15mins">
          <?php esc_html_e('15 Minutes', 'service-finder'); ?>
          </label>
        </div>
        <div class="radio sf-radio-checkbox">
          <input id="30mins" type="radio" name="slot_interval" value="30" <?php echo ($slot_interval == '30' || $slot_interval == '') ? 'checked' : ''; ?>>
          <label for="30mins">
          <?php esc_html_e('30 Minutes', 'service-finder'); ?>
          </label>
        </div>
      </div>
    </div>
    <div class="col-lg-6" id="bookingbasedon" <?php echo ($booking_process == 'off' || $booking_process == '') ? 'style="display: none;"' : ''; ?>>
      <div class="form-group form-inline">
        <label>
        <?php esc_html_e('Booking Based On', 'service-finder'); ?>
        </label>
        <br>
        <div class="radio sf-radio-checkbox">
          <input id="basedonzipcode" type="radio" name="booking_basedon" value="zipcode" <?php echo ($booking_basedon == 'zipcode') ? 'checked' : ''; ?>>
          <label for="basedonzipcode">
          <?php esc_html_e('Postal Code', 'service-finder'); ?>
          </label>
        </div>
        <div class="radio sf-radio-checkbox">
          <input id="region" type="radio" name="booking_basedon" value="region" <?php echo ($booking_basedon == 'region') ? 'checked' : ''; ?>>
          <label for="region">
          <?php esc_html_e('Region', 'service-finder'); ?>
          </label>
        </div>
        <div class="radio sf-radio-checkbox">
          <input id="open" type="radio" name="booking_basedon" value="open" <?php echo ($booking_basedon == 'open' || $booking_basedon == '') ? 'checked' : ''; ?>>
          <label for="open">
          <?php esc_html_e('Open', 'service-finder'); ?>
          </label>
        </div>
      </div>
    </div>
    <?php if($datestyle == 'both'){ ?>
    <div class="col-lg-6" id="bookingdatestyle">
      <div class="form-group form-inline">
        <label>
        <?php esc_html_e('Booking Date Style', 'service-finder'); ?>
        </label>
        <br>
        <div class="radio sf-radio-checkbox">
          <input id="singledate" type="radio" name="booking_date_based_on" value="singledate" <?php echo ($booking_date_based_on == 'singledate' || $booking_date_based_on == '') ? 'checked' : ''; ?>>
          <label for="singledate">
          <?php esc_html_e('Single Date', 'service-finder'); ?>
          </label>
        </div>
        <div class="radio sf-radio-checkbox">
          <input id="multidate" type="radio" name="booking_date_based_on" value="multidate" <?php echo ($booking_date_based_on == 'multidate') ? 'checked' : ''; ?>>
          <label for="multidate">
          <?php esc_html_e('Multi Date (Service Based)', 'service-finder'); ?>
          </label>
        </div>
      </div>
    </div>
    <?php } ?>
    <div class="col-lg-6" id="bookingOption" <?php echo ($booking_process == 'off' || $booking_process == '') ? 'style="display: none;"' : ''; ?>>
      <div class="form-group form-inline">
        <label>
        <?php esc_html_e('Booking Mode', 'service-finder'); ?>
        </label>
        <br>
        <?php if($free_booking){ ?>
        <div class="radio sf-radio-checkbox">
          <input id="free" type="radio" name="booking_option" value="free" <?php echo ($booking_option == 'free' || $booking_option == '' || !$paid_booking) ? 'checked' : ''; ?>>
          <label for="free">
          <?php esc_html_e('Free Booking', 'service-finder'); ?>
          </label>
        </div>
        <?php } ?>
        <?php if($paid_booking){ ?>
        <div class="radio sf-radio-checkbox">
          <input id="paid" type="radio" name="booking_option" value="paid" <?php echo ($booking_option == 'paid' || !$free_booking) ? 'checked' : ''; ?>>
          <label for="paid">
          <?php esc_html_e('Paid Booking', 'service-finder'); ?>
          </label>
        </div>
        <?php } ?>
      </div>
    </div>
    <?php 
                                                if(!empty($userCap)):
                                                if(in_array('staff-members',$userCap) && in_array('bookings',$userCap)):		
                                                ?>
    <div class="col-lg-6" id="bookingAssignment" <?php echo ($booking_process == 'off' ||  $booking_process == '') ? 'style="display: none;"' : ''; ?>>
      <div class="form-group form-inline">
        <label>
        <?php esc_html_e('Booking Assignment', 'service-finder'); ?>
        </label>
        <br>
        <div class="radio sf-radio-checkbox">
          <input id="automatically" type="radio" name="booking_assignment" value="automatically" <?php echo ($booking_assignment == 'automatically') ? 'checked' : ''; ?>>
          <label for="automatically">
          <?php esc_html_e('Automatically', 'service-finder'); ?>
          </label>
        </div>
        <div class="radio sf-radio-checkbox">
          <input id="manually" type="radio" name="booking_assignment" value="manually" <?php echo ($booking_assignment == 'manually' || $booking_assignment == '') ? 'checked' : ''; ?>>
          <label for="manually">
          <?php esc_html_e('Manually', 'service-finder'); ?>
          </label>
        </div>
      </div>
    </div>
    <div class="col-lg-6" id="membersAvailable" <?php echo ($booking_assignment == 'manually' || $booking_process == 'off' || $booking_assignment == '' || $booking_process == '') ? 'style="display: none;"' : ''; ?>>
      <div class="form-group form-inline">
        <label>
        <?php esc_html_e('Staff members available at the time of booking', 'service-finder'); ?>
        </label>
        <br>
        <div class="radio sf-radio-checkbox">
          <input id="yes" type="radio" name="members_available" value="yes" <?php echo ($members_available == 'yes') ? 'checked' : ''; ?>>
          <label for="yes">
          <?php esc_html_e('Yes', 'service-finder'); ?>
          </label>
        </div>
        <div class="radio sf-radio-checkbox">
          <input id="no" type="radio" name="members_available" value="no" <?php echo ($members_available == 'no' || $members_available == '') ? 'checked' : ''; ?>>
          <label for="no">
          <?php esc_html_e('No', 'service-finder'); ?>
          </label>
        </div>
      </div>
    </div>
    <?php 
                                                endif;
                                                endif;
                                                ?>
    <?php if($adminoffersbasedon == 'both'){ ?>
    <div class="col-lg-6">
      <div class="form-group form-inline">
        <label>
        <?php esc_html_e('Offers Based On', 'service-finder'); ?>
        </label>
        <br>
        <div class="radio sf-radio-checkbox">
          <input id="services" type="radio" name="offers_based_on" value="services" <?php echo ($offers_based_on == 'services' || $offers_based_on == '') ? 'checked' : ''; ?>>
          <label for="services">
          <?php esc_html_e('Services', 'service-finder'); ?>
          </label>
        </div>
        <div class="radio sf-radio-checkbox">
          <input id="booking" type="radio" name="offers_based_on" value="booking" <?php echo ($offers_based_on == 'booking') ? 'checked' : ''; ?>>
          <label for="booking">
          <?php esc_html_e('Booking', 'service-finder'); ?>
          </label>
        </div>
      </div>
    </div>
    <?php } ?>	                                                        
    <div class="col-lg-12" id="futureavailability" <?php echo ($booking_process == 'off' || $booking_process == '') ? 'style="display: none;"' : ''; ?>>
      <div class="form-group">
        <label>
        <?php esc_html_e('Availability for Future Bookings', 'service-finder'); ?>
        </label>
        <div class="input-group">
          <select name="futureavailability" class="sf-select-box form-control sf-form-control" data-live-search="true" title="<?php esc_html_e('Availability for Future Bookings', 'service-finder'); ?>">
          <option value=""><?php esc_html_e('Availability for Future Bookings', 'service-finder'); ?></option>
            <?php
            $futureavailabilities = service_finder_get_future_bookings_availabilities();
            if(!empty($futureavailabilities)){
                foreach($futureavailabilities as $key => $value){
                    if($future_bookings_availability  == $key){
                        $select = 'selected="selected"';
                    }else{
                        $select = '';
                    }
                    echo '<option '.$select.' value="'.esc_attr($key).'">'.esc_html($value).'</option>';	
                }
            }
            ?>
          </select>
        </div>
      </div>
    </div>
    <div class="col-lg-12" id="buffertime" <?php echo ($booking_process == 'off' || $booking_process == '') ? 'style="display: none;"' : ''; ?>>
      <div class="form-group">
        <label>
        <?php esc_html_e('Buffer Time for Bookings', 'service-finder'); ?>
        </label>
        <div class="input-group">
          <select name="buffertime" class="sf-select-box form-control sf-form-control" data-live-search="true" title="<?php esc_html_e('Buffer Time for Bookings', 'service-finder'); ?>">
          <option value=""><?php esc_html_e('Buffer Time for Bookings', 'service-finder'); ?></option>
            <?php
			$intervals = service_finder_get_buffer_time_interval();
			if(!empty($intervals)){
				foreach($intervals as $key => $value){
					if($buffertime  == $key){
                        $select = 'selected="selected"';
                    }else{
                        $select = '';
                    }
					echo '<option '.$select.' value="'.esc_attr($key).'">'.esc_html($value).'</option>';	
				}
			}
			?>
          </select>
        </div>
      </div>
    </div>
    <div class="col-lg-12" id="minCost" <?php echo ($booking_process == 'off' || $booking_process == '') ? 'style="display: none;"' : ''; ?>>
      <div class="form-group">
        <label>
        <?php esc_html_e('Minimum Amount (It will be charge at the time of booking)', 'service-finder'); ?>
        </label>
        <div class="input-group"> <i class="input-group-addon fixed-w fa fa-money"></i>
          <input type="text" class="form-control sf-form-control" name="mincost" value="<?php echo esc_attr($mincost) ?>" placeholder="<?php esc_html_e('ex. 0 if no mimimum amount', 'service-finder'); ?>">
        </div>
      </div>
    </div>
    <?php if($pay_booking_amount_to == 'provider'){ ?>
    <div id="payoptions" <?php echo ($booking_option == 'free' || $booking_process == 'off' || $booking_option == '' || $booking_process == '') ? 'style="display: none;"' : ''; ?>>
      <div class="col-lg-12">
        <div class="form-group form-inline">
          <label>
          <?php esc_html_e('Payment Method', 'service-finder'); ?>
          </label>
          <br>

          <?php
          if(!empty($payment_methods)){
          if($payment_methods['paypal']){
          
          if(!empty($paymentoption)){
                if(in_array('paypal',$paymentoption)){
                $check1 = 'checked="checked"';
                }else{
                $check1 = '';
                }
            }else{
                $check1 = '';
            }
          ?>
          <div class="checkbox sf-radio-checkbox">
            <input <?php echo esc_attr($check1); ?> type="checkbox" value="paypal" name="pay_options[]" id="bypaypal">
            <label for="bypaypal">
            <?php esc_html_e('Paypal', 'service-finder'); ?>
            </label>
          </div>
          <?php
          }
          }
          ?>
          <?php
          if(!empty($payment_methods)){
          if($payment_methods['stripe']){
          
          if(!empty($paymentoption)){
                if(in_array('stripe',$paymentoption)){
                $check2 = 'checked="checked"';
                }else{
                $check2 = '';
                }
            }else{
                $check2 = '';
            }
          ?>
          <div class="checkbox sf-radio-checkbox">
            <input type="checkbox" <?php echo esc_attr($check2); ?> value="stripe" name="pay_options[]" id="bystripe">
            <label for="bystripe">
            <?php esc_html_e('Stripe', 'service-finder'); ?>
            </label>
          </div>
          <?php
          }
          }
          ?>
          <?php
          /*?>if(!empty($payment_methods)){
          if($payment_methods['twocheckout']){
          
          if(!empty($paymentoption)){
                if(in_array('twocheckout',$paymentoption)){
                $check4 = 'checked="checked"';
                }else{
                $check4 = '';
                }
            }else{
                $check4 = '';
            }
          ?>
          <div class="checkbox sf-radio-checkbox">
            <input type="checkbox" <?php echo esc_attr($check4); ?> value="twocheckout" name="pay_options[]" id="bytwocheckout">
            <label for="bytwocheckout">
            <?php esc_html_e('2Checkout', 'service-finder'); ?>
            </label>
          </div>
          <?php
          }
          }<?php */?>
          <?php
          if(!empty($payment_methods)){
          if($payment_methods['wired']){
          
          if(!empty($paymentoption)){
                if(in_array('wired',$paymentoption)){
                $check3 = 'checked="checked"';
                }else{
                $check3 = '';
                }
            }else{
                $check3 = '';
            }
          
          ?>
          <div class="checkbox sf-radio-checkbox">
            <input type="checkbox" <?php echo esc_attr($check3); ?> value="wired" name="pay_options[]" id="bywire">
            <label for="bywire">
            <?php esc_html_e('Wire Transfer', 'service-finder'); ?>
            </label>
          </div>
          <?php
          }
          }
          ?>
          <?php
          if(!empty($payment_methods)){
          if($payment_methods['payumoney']){
          
          if(!empty($paymentoption)){
          if(in_array('payumoney',$paymentoption)){
                $check5 = 'checked="checked"';
                }else{
                $check5 = '';
                }
            }else{
                $check5 = '';
            }
          ?>
          <div class="checkbox sf-radio-checkbox">
            <input type="checkbox" <?php echo esc_attr($check5); ?> value="payumoney" name="pay_options[]" id="bypayumoney">
            <label for="bypayumoney">
            <?php esc_html_e('PayU Money', 'service-finder'); ?>
            </label>
          </div>
          <?php
          }
          }
          ?>
          <?php
          if(!empty($payment_methods)){
          if($payment_methods['payulatam']){
          
          if(!empty($paymentoption)){
          if(in_array('payulatam',$paymentoption)){
                $check6 = 'checked="checked"';
                }else{
                $check6 = '';
                }
            }else{
                $check6 = '';
            }
          ?>
          <div class="checkbox sf-radio-checkbox">
            <input type="checkbox" <?php echo esc_attr($check6); ?> value="payulatam" name="pay_options[]" id="bypayulatam">
            <label for="bypayulatam">
            <?php esc_html_e('PayU Latam', 'service-finder'); ?>
            </label>
          </div>
          <?php
          }
          }
          ?>
          <?php
          if(!empty($payment_methods)){
          if($payment_methods['cod']){
          
          if(!empty($paymentoption)){
          if(in_array('cod',$paymentoption)){
                $check7 = 'checked="checked"';
                }else{
                $check7 = '';
                }
            }else{
                $check7 = '';
            }
          ?>
          <div class="checkbox sf-radio-checkbox">
            <input <?php echo esc_attr($check7); ?> type="checkbox" value="cod" name="pay_options[]" id="bycod">
            <label for="bycod">
            <?php esc_html_e('Cash on Delevery', 'service-finder'); ?>
            </label>
          </div>
          <?php
          }
          }
          ?>
        </div>
      </div>
    </div>
    <div class="allpaymentinfo">
    <?php
                                                $stybx = 'style="display: none;"';
                                                if(!empty($payment_methods)){
                                                if($payment_methods['paypal']){
                                                if(!empty($paymentoption)){
                                                    if(!in_array('paypal',$paymentoption) || $booking_option == 'free' || $booking_process == 'off' || $booking_option == '' || $booking_process == ''){
                                                    $stybx = 'style="display: none;"';
                                                    }else{
                                                    $stybx = '';
                                                    }
                                                }
                                                }
                                                }
                                                ?>
    <div id="paypalemail" <?php echo $stybx; ?> >
      <div class="col-lg-6">
        <div class="form-group">
          <label>
          <?php esc_html_e('PayPal API Username', 'service-finder'); ?>
          </label>
          <div class="input-group"> <i class="input-group-addon fixed-w fa fa-envelope"></i>
            <input type="text" class="form-control sf-form-control" name="paypalusername" value="<?php echo esc_attr($paypalusername) ?>">
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="form-group">
          <label>
          <?php esc_html_e('PayPal API Password', 'service-finder'); ?>
          </label>
          <div class="input-group"> <i class="input-group-addon fixed-w fa fa-lock"></i>
            <input type="text" class="form-control sf-form-control" name="paypalpassword" value="<?php echo esc_attr($paypalpassword) ?>">
          </div>
        </div>
      </div>
      <div class="col-lg-12">
        <div class="form-group">
          <label>
          <?php esc_html_e('PayPal API Signatue', 'service-finder'); ?>
          </label>
          <div class="input-group"> <i class="input-group-addon fixed-w fa fa-envelope"></i>
            <input type="text" class="form-control sf-form-control" name="paypalsignatue" value="<?php echo esc_attr($paypalsignatue) ?>">
          </div>
        </div>
      </div>
    </div>
    <?php
                                                $stybx = 'style="display: none;"';
                                                if(!empty($payment_methods)){
                                                if($payment_methods['stripe']){
                                                if(!empty($paymentoption)){
                                                    if(!in_array('stripe',$paymentoption) || $booking_option == 'free' || $booking_process == 'off' || $booking_option == '' || $booking_process == ''){
                                                    $stybx = 'style="display: none;"';
                                                    }else{
                                                    $stybx = '';
                                                    }
                                                }
                                                }
                                                }
                                                ?>
    <div id="stripekey" <?php echo $stybx; ?> >
      <div class="col-lg-6">
        <div class="form-group">
          <label>
          <?php esc_html_e('Stripe Secret Key', 'service-finder'); ?>
          </label>
          <div class="input-group"> <i class="input-group-addon fixed-w fa fa-envelope"></i>
            <input type="text" class="form-control sf-form-control" name="stripesecretkey" value="<?php echo esc_attr($stripesecretkey) ?>">
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="form-group">
          <label>
          <?php esc_html_e('Stripe Public Key', 'service-finder'); ?>
          </label>
          <div class="input-group"> <i class="input-group-addon fixed-w fa fa-envelope"></i>
            <input type="text" class="form-control sf-form-control" name="stripepublickey" value="<?php echo esc_attr($stripepublickey) ?>">
          </div>
        </div>
      </div>
    </div>
    <?php
    $stybx = 'style="display: none;"';
    if(!empty($payment_methods)){
    $twocheckoutpayment = (isset($payment_methods['twocheckout'])) ? $payment_methods['twocheckout'] : '';
    if($twocheckoutpayment){
    if(!empty($paymentoption)){
        if(!in_array('twocheckout',$paymentoption) || $booking_option == 'free' || $booking_process == 'off' || $booking_option == '' || $booking_process == ''){
        $stybx = 'style="display: none;"';
        }else{
        $stybx = '';
        }
    }
    }
    }
    ?>
    <div id="twocheckoutkey" <?php echo $stybx; ?> >
    <div class="col-lg-12">
    <div class="form-group">
    <label>
    <?php esc_html_e('2Checkout Account ID', 'service-finder'); ?>
    </label>
    <div class="input-group"> <i class="input-group-addon fixed-w fa fa-envelope"></i>
    <input type="text" class="form-control sf-form-control" name="twocheckoutaccountid" value="<?php echo esc_attr($twocheckoutaccountid) ?>">
    </div>
    </div>
    </div>
    <div class="col-lg-6">
    <div class="form-group">
    <label>
    <?php esc_html_e('2Checkout Publish Key', 'service-finder'); ?>
    </label>
    <div class="input-group"> <i class="input-group-addon fixed-w fa fa-envelope"></i>
    <input type="text" class="form-control sf-form-control" name="twocheckoutpublishkey" value="<?php echo esc_attr($twocheckoutpublishkey) ?>">
    </div>
    </div>
    </div>
    <div class="col-lg-6">
    <div class="form-group">
    <label>
    <?php esc_html_e('2Checkout Private Key', 'service-finder'); ?>
    </label>
    <div class="input-group"> <i class="input-group-addon fixed-w fa fa-envelope"></i>
    <input type="text" class="form-control sf-form-control" name="twocheckoutprivatekey" value="<?php echo esc_attr($twocheckoutprivatekey) ?>">
    </div>
    </div>
    </div>
    </div>
    <?php
    $stybx = 'style="display: none;"';
    if(!empty($payment_methods)){
    if($payment_methods['payumoney']){
    if(!empty($paymentoption)){
        if(!in_array('payumoney',$paymentoption) || $booking_option == 'free' || $booking_process == 'off' || $booking_option == '' || $booking_process == ''){
        $stybx = 'style="display: none;"';
        }else{
        $stybx = '';
        }
    }
    }
    }
    ?>
    <div id="payumoneyinfo" <?php echo $stybx; ?> >
    <div class="col-lg-12">
    <div class="form-group">
    <label>
    <?php esc_html_e('PayU Money MID', 'service-finder'); ?>
    </label>
    <div class="input-group"> <i class="input-group-addon fixed-w fa fa-envelope"></i>
    <input type="text" class="form-control sf-form-control" name="payumoneymid" value="<?php echo esc_attr($payumoneymid) ?>">
    </div>
    </div>
    </div>
    <div class="col-lg-6">
    <div class="form-group">
    <label>
    <?php esc_html_e('PayU Money Key', 'service-finder'); ?>
    </label>
    <div class="input-group"> <i class="input-group-addon fixed-w fa fa-envelope"></i>
    <input type="text" class="form-control sf-form-control" name="payumoneykey" value="<?php echo esc_attr($payumoneykey) ?>">
    </div>
    </div>
    </div>
    <div class="col-lg-6">
    <div class="form-group">
    <label>
    <?php esc_html_e('PayU Money Salt', 'service-finder'); ?>
    </label>
    <div class="input-group"> <i class="input-group-addon fixed-w fa fa-envelope"></i>
    <input type="text" class="form-control sf-form-control" name="payumoneysalt" value="<?php echo esc_attr($payumoneysalt) ?>">
    </div>
    </div>
    </div>
    </div>
    <?php
    $stybx = 'style="display: none;"';
    if(!empty($payment_methods)){
    if($payment_methods['payulatam']){
    if(!empty($paymentoption)){
        if(!in_array('payulatam',$paymentoption) || $booking_option == 'free' || $booking_process == 'off' || $booking_option == '' || $booking_process == ''){
        $stybx = 'style="display: none;"';
        }else{
        $stybx = '';
        }
    }
    }
    }
    ?>
    <div id="payulataminfo" <?php echo $stybx; ?> >
    <div class="col-lg-6">
    <div class="form-group">
    <label>
    <?php esc_html_e('PayU Latam Merchant Id', 'service-finder'); ?>
    </label>
    <div class="input-group"> <i class="input-group-addon fixed-w fa fa-envelope"></i>
    <input type="text" class="form-control sf-form-control" name="payulatammerchantid" value="<?php echo esc_attr($payulatammerchantid) ?>">
    </div>
    </div>
    </div>
    <div class="col-lg-6">
    <div class="form-group">
    <label>
    <?php esc_html_e('PayU Latam API Login', 'service-finder'); ?>
    </label>
    <div class="input-group"> <i class="input-group-addon fixed-w fa fa-envelope"></i>
    <input type="text" class="form-control sf-form-control" name="payulatamapilogin" value="<?php echo esc_attr($payulatamapilogin) ?>">
    </div>
    </div>
    </div>
    <div class="col-lg-6">
    <div class="form-group">
    <label>
    <?php esc_html_e('PayU Latam API Key', 'service-finder'); ?>
    </label>
    <div class="input-group"> <i class="input-group-addon fixed-w fa fa-envelope"></i>
    <input type="text" class="form-control sf-form-control" name="payulatamapikey" value="<?php echo esc_attr($payulatamapikey) ?>">
    </div>
    </div>
    </div>
    <div class="col-lg-6">
    <div class="form-group">
    <label>
    <?php esc_html_e('PayU Latam Account Id', 'service-finder'); ?>
    </label>
    <div class="input-group"> <i class="input-group-addon fixed-w fa fa-envelope"></i>

    <input type="text" class="form-control sf-form-control" name="payulatamaccountid" value="<?php echo esc_attr($payulatamaccountid) ?>">
    </div>
    </div>
    </div>
    </div>
    <?php
    $stybx = 'style="display: none;"';
    if(!empty($payment_methods)){
    if($payment_methods['wired']){
    if(!empty($paymentoption)){
        if(!in_array('wired',$paymentoption) || $booking_option == 'free' || $booking_process == 'off' || $booking_option == '' || $booking_process == ''){
        $stybx = 'style="display: none;"';
        }else{
        $stybx = '';
        }
    }
    }
    }
    ?>
    <div id="wiredescription" <?php echo $stybx; ?> >
    <div class="col-lg-12">
      <div class="form-group">
        <label>
        <?php esc_html_e('Description for Wired Transfer', 'service-finder'); ?>
        </label>
        <div class="input-group"> <i class="input-group-addon fixed-w fa fa-pencil v-align-t"></i>
          <textarea class="form-control sf-form-control" maxlength="200" rows="3" cols="" name="wired_description"><?php echo (isset($wired_description)) ? esc_attr($wired_description) : '' ?></textarea>
        </div>
      </div>
    </div>
    </div>
    
    <div id="wireinstructions" <?php echo $stybx; ?> >
    <div class="col-lg-12">
      <div class="form-group">
        <label>
        <?php esc_html_e('Instructions for Wired Transfer (For Mail Template)', 'service-finder'); ?>
        </label>
        <div class="input-group"> <i class="input-group-addon fixed-w fa fa-pencil v-align-t"></i>
          <textarea class="form-control sf-form-control" maxlength="200" rows="3" cols="" name="wired_instructions"><?php echo (isset($wired_instructions)) ? esc_attr($wired_instructions) : '' ?></textarea>
        </div>
      </div>
    </div>
    </div>
    </div>
    <?php } ?>
  </div>
</div>
</div>

<?php
  if($pay_booking_amount_to == 'admin' && $bankaccount_info_section){
  ?>
  <!--Back Account Details Section-->
  <div class="panel panel-default password-update">
    <div class="panel-heading sf-panel-heading">
      <h3 class="panel-tittle m-a0"><span class="fa fa-university"></span> <?php esc_html_e('Bank Account Details', 'service-finder'); ?> <i class="tip-info fa fa-question" data-toggle="tooltip" title="<?php echo esc_html__('Bank details will be used by admin to pay your booking amount to your bank account once service is delivered. It will happen offsite manually.', 'service-finder'); ?>"></i></h3>
    </div>
    <div class="panel-body sf-panel-body padding-30">
      <div class="row">
        <div class="col-lg-6">
          <div class="form-group">
            <label>
            <?php esc_html_e('Bank Account Holder\'s Name', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-lock"></i>
              <input type="text" class="form-control sf-form-control" name="bank_account_holder_name" value="<?php echo esc_attr(get_user_meta($globalproviderid,'bank_account_holder_name',true)) ?>">
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="form-group">
            <label>
            <?php esc_html_e('Bank Account Number/IBAN', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-lock"></i>
              <input type="text" class="form-control sf-form-control" name="bank_account_number" value="<?php echo esc_attr(get_user_meta($globalproviderid,'bank_account_number',true)) ?>">
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="form-group">
            <label>
            <?php esc_html_e('Swift Code', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-lock"></i>
              <input type="text" class="form-control sf-form-control" name="swift_code" value="<?php echo esc_attr(get_user_meta($globalproviderid,'swift_code',true)) ?>">
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="form-group">
            <label>
            <?php esc_html_e('Bank Name in Full', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-lock"></i>
              <input type="text" class="form-control sf-form-control" name="bank_name" value="<?php echo esc_attr(get_user_meta($globalproviderid,'bank_name',true)) ?>">
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="form-group">
            <label>
            <?php esc_html_e('Bank Branch City', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-lock"></i>
              <input type="text" class="form-control sf-form-control" name="bank_branch_city" value="<?php echo esc_attr(get_user_meta($globalproviderid,'bank_branch_city',true)) ?>">
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="form-group">
            <label>
            <?php esc_html_e('Bank Branch Country', 'service-finder'); ?>
            </label>
            <div class="input-group"> <i class="input-group-addon fixed-w fa fa-lock"></i>
              <input type="text" class="form-control sf-form-control" name="bank_branch_country" value="<?php echo esc_attr(get_user_meta($globalproviderid,'bank_branch_country',true)) ?>">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php } ?>

<div class="sf-submit-booking-seeting">
<input type="hidden" name="providerid" value="<?php echo esc_attr($globalproviderid); ?>" />
<input type="submit" class="btn btn-primary margin-r-10" value="<?php esc_html_e('Submit information', 'service-finder'); ?>" />
</div>
</form>