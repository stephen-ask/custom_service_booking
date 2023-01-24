<?php

/*****************************************************************************

*

*	copyright(c) - aonetheme.com - Service Finder Team

*	More Info: http://aonetheme.com/

*	Coder: Service Finder Team

*	Email: contact@aonetheme.com

*

******************************************************************************/



$service_finder_ThemeParams = service_finder_plugin_global_vars('service_finder_ThemeParams');

$service_finder_options = get_option('service_finder_options');

$userInfo = service_finder_getCurrentUserInfo();

$settings = array();

$paymentmethods = service_finder_get_payment_methods();



$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? esc_html($service_finder_options['pay_booking_amount_to']) : '';

$admin_fee_type = (!empty($service_finder_options['admin-fee-type'])) ? $service_finder_options['admin-fee-type'] : 0;

$admin_fee_percentage = (!empty($service_finder_options['admin-fee-percentage'])) ? $service_finder_options['admin-fee-percentage'] : 0;

$admin_fee_fixed = (!empty($service_finder_options['admin-fee-fixed'])) ? $service_finder_options['admin-fee-fixed'] : 0;



$admin_fee_label = (!empty($service_finder_options['admin-fee-label'])) ? $service_finder_options['admin-fee-label'] : esc_html__('Admin Fee', 'service-finder');

$charge_admin_fee = (!empty($service_finder_options['charge-admin-fee'])) ? $service_finder_options['charge-admin-fee'] : '';

$charge_admin_fee_from = (!empty($service_finder_options['charge-admin-fee-from'])) ? $service_finder_options['charge-admin-fee-from'] : '';



if($charge_admin_fee && $pay_booking_amount_to == 'admin' && (($admin_fee_type == 'fixed' && $admin_fee_fixed > 0) || ($admin_fee_type == 'percentage' && $admin_fee_percentage > 0)) && $charge_admin_fee_from == 'customer'){

$showadminfee = '<li>'.esc_html__('Booking Amount', 'service-finder').': <strong><span id="bookingfee"></span></strong> </li>';

$showadminfee .= '<li>'.sprintf( esc_html__('%s', 'service-finder'), $admin_fee_label ).': <strong><span id="bookingadminfee"></span></strong> </li>';

$showadminfee .= '<li>'.esc_html__('Total Amount', 'service-finder').': <strong><span id="totalbookingfee"></span></strong> </li>';

$showadminfee = '<ul class="sf-adminfee-bx">'.$showadminfee.'</ul>';

}else{

$showadminfee = '';

}



$jsdata = 'var jsdata = { 

			"stripepublickey": "'.service_finder_get_stripe_public_key().'",

			};';

wp_add_inline_script('service-finder-quote-applications', $jsdata, 'before');

?>

<form class="myform book-now" method="post" id="bookingjobform">

<input type="hidden" id="provider" name="provider" value="" />

<input type="hidden" id="jobid" name="jobid" value="" />

<input type="hidden" id="quoteid" name="quoteid" value="" />

<input type="hidden" id="boking-slot" data-slot="" name="boking-slot" value="" />

<input type="hidden" id="memberid" data-memid="" name="memberid" value="" />

<input type="hidden" id="totalcost" name="totalcost" value="" />

<input type="hidden" id="totaldiscount" name="totaldiscount" value="" />

<input type="hidden" id="servicearr" name="servicearr" value="" />

<input type="hidden" id="selecteddate" data-seldate="" name="selecteddate" />

<div class="booking-panel-wrap">

	<span class="sf-serach-result-close"><i class="fa fa-close"></i></span>

	<div class="booking-panel-cell">

		<?php

		if(service_finder_has_pay_only_admin_fee()){

		?>

		<div class="sf-adminfee-outer" style="display:none" id="adminfee-outer">

		<div class="sf-payonly-adminfee"><span><?php echo esc_html__('Admin Fee:', 'service-finder'); ?></span> <span id="onlyadminfee"></span></div>

		<div class="sf-payonly-adminfee"><?php echo esc_html__('You need to pay only admin fee at the time of booking.', 'service-finder'); ?></div>

		</div>

		<?php

		}

		?>

        <div class="sf-custom-accordion" id="jobbookingaccordion">

			<div class="panel">

				<div class="acod-head">

					 <h5 class="acod-title">

						<a data-toggle="collapse" href="#pricebox" data-parent="#jobbookingaccordion" aria-expanded="ture"><?php esc_html_e('Job Price', 'service-finder'); ?>

						<span class="indicator"><i class="fa fa-plus"></i></span>

						</a>

					 </h5>

				</div>

				<div id="pricebox" class="acod-body collapse in">

					<div class="acod-content">

						<div class="booking-panel-step-one">

							<span class="booking-panel-price" id="job-booking-price"></span>

							<?php /*?><a href="javascript:;" class="booking-panel-price-edit btn btn-sm editprice"><i class="fa fa-pencil"></i> <?php esc_html_e('Edit', 'service-finder'); ?></a><?php */?>

                            <?php /*?><div class="booking-update-box" id="editpricebox" style="display:none">

                                <div class="booking-update-cell"><input type="text" class="form-control" name="customprice" placeholder="<?php esc_html_e('Enter Price', 'service-finder'); ?>"></div>

                                <div class="booking-update-cell"><button class="btn btn-primary updateprice" type="button"><i class="fa fa-refresh"></i> <?php esc_html_e('Update Price', 'service-finder'); ?></button></div>

                            </div><?php */?>

                            <div class="booking-panel-btn-wrap"> 

                                <button type="button" class="btn btn-custom continuetodatetime"><?php esc_html_e('Continue', 'service-finder'); ?></button> 

                            </div>

						</div>

					</div>

				</div>

			</div>                  

			<div class="panel">

				<div class="acod-head">

				 <h5 class="acod-title">

					<a data-toggle="collapse" href="#datetimebox" data-parent="#jobbookingaccordion" aria-expanded="false" id="datetimeheader">

					<?php esc_html_e('Delivery Date/Time', 'service-finder'); ?>

					<span class="indicator"><i class="fa fa-plus"></i></span>

					</a>

				 </h5>

				</div>

				<div id="datetimebox" class="acod-body collapse">

					<div class="acod-content">

						<div class="booking-panel-step-three">

                            <div class="booking-panel-calender-wrap">

                                <div class="jobbookingdate"></div>

                            </div>

                            <div class="booking-panel-time-slot">

                                <div class="booking-slots-outer">

                                  <ul class="list-inline clearfix timeslots timelist">

                                  </ul>

                                </div>            	

                            </div>

                            <div class="booking-panel-btn-wrap"> 

                                <button type="button" class="btn btn-primary backtopricebox"><?php esc_html_e('Back', 'service-finder'); ?></button>

                                <button type="button" class="btn btn-custom continuetocustomerinfo"><?php esc_html_e('Continue', 'service-finder'); ?></button> 

                            </div>            

                        </div>

					</div>

				</div>

			</div>

			<div class="panel">

				<div class="acod-head">

				 <h5 class="acod-title">

					<a data-toggle="collapse"  href="#customerinfobox" data-parent="#jobbookingaccordion" aria-expanded="false" id="customerinfoheader">

					<?php esc_html_e('Customer Info', 'service-finder'); ?>

					<span class="indicator"><i class="fa fa-plus"></i></span>

					</a>

				 </h5>

				</div>

				<div id="customerinfobox" class="acod-body collapse">

					<div class="acod-content">

						<div class="booking-panel-step-four">

                            <div class="m-b30 clearfix sf-aad-booking-backbtn">

                                <h4><span class="sf-heading-icon text-blue"><i class="fa fa-user"></i></span> <?php esc_html_e('Your Details', 'service-finder'); ?></h4>

                                <div class="row">

                                    <div class="col-md-6">

                                      <div class="form-group">

                                        <label>

                                        <?php esc_html_e('First Name', 'service-finder'); ?>

                                        </label>

                                        <div class="input-group"> <i class="input-group-addon fa fa-user"></i>

                                          <input name="firstname" id="firstname" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo['fname'])) ? esc_attr($userInfo['fname']) : ''; ?>">

                                        </div>

                                      </div>

                                    </div>

                                    <div class="col-md-6">

                                      <div class="form-group">

                                        <label>

                                        <?php esc_html_e('Last Name', 'service-finder'); ?>

                                        </label>

                                        <div class="input-group"> <i class="input-group-addon fa fa-user"></i>

                                          <input name="lastname" id="lastname" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo['lname'])) ? esc_attr($userInfo['lname']) : ''; ?>">

                                        </div>

                                      </div>

                                    </div>

                                    <div class="col-md-6">

                                      <div class="form-group">

                                        <label>

                                        <?php esc_html_e('Email', 'service-finder'); ?>

                                        </label>

                                        <div class="input-group"> <i class="input-group-addon fa fa-envelope"></i>

                                          <input id="email" name="email" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo[0]->user_email)) ? esc_attr($userInfo[0]->user_email) : ''; ?>">

                                        </div>

                                      </div>

                                    </div>

                                    <div class="col-md-6">

                                      <div class="form-group">

                                        <label>

                                        <?php esc_html_e('Phone', 'service-finder'); ?>

                                        </label>

                                        <div class="input-group"> <i class="input-group-addon fa fa-phone"></i>

                                          <input id="phone" name="phone" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo['phone'])) ? esc_attr($userInfo['phone']) : ''; ?>">

                                        </div>

                                      </div>

                                    </div>

                                    <div class="col-md-6">

                                      <div class="form-group">

                                        <label>

                                        <?php esc_html_e('Alt. Phone', 'service-finder'); ?>

                                        </label>

                                        <div class="input-group"> <i class="input-group-addon fa fa-phone"></i>

                                          <input name="phone2" id="phone2" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo['phone2'])) ? esc_attr($userInfo['phone2']) : ''; ?>">

                                        </div>

                                      </div>

                                    </div>

                                    <div class="col-md-8">

                                      <div class="form-group">

                                        <label>

                                        <?php esc_html_e('Address', 'service-finder'); ?>

                                        </label>

                                        <div class="input-group"> <i class="input-group-addon fa fa-globe"></i>

                                          <input id="booking-address" name="address" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo['address'])) ? esc_attr($userInfo['address']) : ''; ?>">

                                        </div>

                                      </div>

                                    </div>

                                    <div class="col-md-4">

                                      <div class="form-group">

                                        <label>

                                        <?php esc_html_e('Apt/Suite #', 'service-finder'); ?>

                                        </label>

                                        <div class="input-group"> <i class="input-group-addon fa fa-building-o"></i>

                                          <input name="apt" id="apt" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo['apt'])) ? esc_attr($userInfo['apt']) : ''; ?>">

                                        </div>

                                      </div>

                                    </div>

                                    <div class="col-md-6">

                                      <div class="form-group">

                                        <label>

                                        <?php esc_html_e('City', 'service-finder'); ?>

                                        </label>

                                        <div class="input-group"> <i class="input-group-addon fa fa-map-marker"></i>

                                          <input id="bookingcity" name="city" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo['city'])) ? esc_attr($userInfo['city']) : ''; ?>">

                                        </div>

                                      </div>

                                    </div>

                                    <div class="col-md-6">

                                      <div class="form-group">

                                        <label>

                                        <?php esc_html_e('State', 'service-finder'); ?>

                                        </label>

                                        <div class="input-group"> <i class="input-group-addon fa fa-map-marker"></i>

                                          <input id="bookingstate" name="state" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo['state'])) ? esc_attr($userInfo['state']) : ''; ?>">

                                        </div>

                                      </div>

                                    </div>

                                    <div class="col-md-6">

                                      <div class="form-group">

                                        <label>

                                        <?php esc_html_e('Country', 'service-finder'); ?>

                                        </label>

                                        <div class="input-group"> <i class="input-group-addon fa fa-map-marker"></i>

                                          <input id="bookingcountry" name="country" type="text" class="form-control sf-form-control" value="<?php echo (!empty($userInfo['country'])) ? esc_attr($userInfo['country']) : ''; ?>">

                                        </div>

                                      </div>

                                    </div>

                                    <div class="col-md-12">

                                      <div class="form-group">

                                        <label>

                                        <?php esc_html_e('Describe Your Task', 'service-finder'); ?>

                                        </label>

                                        <div class="input-group"> <span class="input-group-addon v-align-t"><i class="fa fa-pencil"></i></span>

                                          <textarea id="shortdesc" name="shortdesc" class="form-control sf-form-control" placeholder="<?php esc_html_e('Please insert short description of your task', 'service-finder'); ?>"></textarea>

                                        </div>

                                      </div>

                                    </div>

                                </div>

                                <div class="booking-panel-btn-wrap"> 

                                    <button type="button" class="btn btn-primary backtodatetimebox"><?php esc_html_e('Back', 'service-finder'); ?></button>

                                    <button type="button" class="btn btn-custom continuetopayment"> <?php esc_html_e('Proceed to Checkout', 'service-finder'); ?></button> 

                                </div>

                            </div>

						</div>

					</div>

				</div>

			</div>

			<div class="panel" id="quotebooking-paid-panel">

				<div class="acod-head">

				 <h5 class="acod-title">

					<a data-toggle="collapse"  href="#paymentbox" data-parent="#jobbookingaccordion" aria-expanded="false" id="paymentheader">

					<?php esc_html_e('Payment', 'service-finder'); ?>

					<span class="indicator"><i class="fa fa-plus"></i></span>

					</a>

				 </h5>

				</div>

				<div id="paymentbox" class="acod-body collapse">

					<div class="acod-content">

						<div class="booking-panel-step-four">

		

                        <div class="clearfix f-row">

                          <?php echo $showadminfee; ?>

                          <?php echo service_finder_display_wallet_amount($current_user->ID); ?> 

                            <div class="col-md-12">

                              <div class="form-group form-inline">

                                <div class="col-md-12">

                                  <div class="form-group form-inline sf-card-group" id="sf-payment-options">

                                    <?php

									$payflag = 0;

                                    if(service_finder_get_payment_goes_to() == 'admin')

									{

										$paymentmethods = service_finder_get_payment_methods();

										

										if(!empty($paymentmethods))

										{

											foreach($paymentmethods as $key => $paymentmethod)

											{

												if($paymentmethod == 1)

												{

												$payflag = 1;

												?>

												<div class="radio sf-radio-checkbox">

                                                  <input type="radio" value="<?php echo esc_attr($key); ?>" name="bookingpayment_mode" id="paymentvia<?php echo esc_attr($key); ?>" >

                                                  <label for="paymentvia<?php echo esc_attr($key); ?>"><?php echo '<img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/'.$key.'.jpg" title="'.esc_attr(ucfirst($key)).'" alt="'.esc_attr(ucfirst($key)).'">'; ?></label>

                                                </div>

												<?php

												}

											}

										}

										

									}

									?>

                                    <?php 

                                    echo service_finder_add_wallet_option('bookingpayment_mode','paymentvia');

                                    echo service_finder_add_skip_option('bookingpayment_mode','paymentvia');

                                    ?>

                                  </div>

                                </div>

                              </div>

                            </div>

                            <div id="bookingcardinfo" class="default-hidden">

                              <div class="col-md-8">

                                <div class="form-group">

                                  <label>

                                  <?php esc_html_e('Card Number', 'service-finder'); ?>

                                  </label>

                                  <div class="input-group"> <i class="input-group-addon fa fa-credit-card"></i>

                                    <input type="text" id="card_number" name="card_number" class="form-control sf-form-control">

                                  </div>

                                </div>

                              </div>

                              <div class="col-md-4">

                                <div class="form-group">

                                  <label>

                                  <?php esc_html_e('CVC', 'service-finder'); ?>

                                  </label>

                                  <div class="input-group"> <i class="input-group-addon fa fa-ellipsis-h"></i>

                                    <input type="text" id="card_cvc" name="card_cvc" class="form-control sf-form-control">

                                  </div>

                                </div>

                              </div>

                              <div class="col-md-6">

                                <div class="form-group has-select">

                                  <label>

                                  <?php esc_html_e('Select Month', 'service-finder'); ?>

                                  </label>

                                  <select id="card_month" name="card_month" class="form-control sf-form-control sf-select-box" title="Select Month">

                                      <option value="1"><?php echo esc_html__('January', 'service-finder') ?></option>

                                      <option value="2"><?php echo esc_html__('February', 'service-finder')?></option>

                                      <option value="3"><?php echo esc_html__('March', 'service-finder')?></option>

                                      <option value="4"><?php echo esc_html__('April', 'service-finder')?></option>

                                      <option value="5"><?php echo esc_html__('May', 'service-finder')?></option>

                                      <option value="6"><?php echo esc_html__('June', 'service-finder')?></option>

                                      <option value="7"><?php echo esc_html__('July', 'service-finder')?></option>

                                      <option value="8"><?php echo esc_html__('August', 'service-finder')?></option>

                                      <option value="9"><?php echo esc_html__('September', 'service-finder')?></option>

                                      <option value="10"><?php echo esc_html__('October', 'service-finder')?></option>

                                      <option value="11"><?php echo esc_html__('November', 'service-finder')?></option>

                                      <option value="12"><?php echo esc_html__('December', 'service-finder')?></option>

                                  </select>

                                </div>

                              </div>

                              <div class="col-md-6">

                                <div class="form-group has-select">

                                  <label>

                                  <?php esc_html_e('Select Year', 'service-finder'); ?>

                                  </label>

                                  <select id="card_year" name="card_year" class="form-control sf-form-control sf-select-box"  title="Select Year">

                                    <?php

                                                                $year = date('Y');

                                                                for($i = $year;$i<=$year+50;$i++){

                                                                    echo '<option value="'.esc_attr($i).'">'.$i.'</option>';

                                                                }

                                                                ?>

                                  </select>

                                </div>

                              </div>

                            </div>

                            <div id="bookingtwocheckoutcardinfo" class="default-hidden">

                                <div class="col-md-8">

                                  <div class="form-group">

                                    <label>

                                    <?php esc_html_e('Card Number', 'service-finder'); ?>

                                    </label>

                                    <div class="input-group"> <i class="input-group-addon fa fa-credit-card"></i>

                                      <input type="text" id="twocheckout_card_number" name="twocheckout_card_number" class="form-control sf-form-control">

                                    </div>

                                  </div>

                                </div>

                                <div class="col-md-4">

                                  <div class="form-group">

                                    <label>

                                    <?php esc_html_e('CVC', 'service-finder'); ?>

                                    </label>

                                    <div class="input-group"> <i class="input-group-addon fa fa-ellipsis-h"></i>

                                      <input type="text" id="twocheckout_card_cvc" name="twocheckout_card_cvc" class="form-control sf-form-control">

                                    </div>

                                  </div>

                                </div>

                                <div class="col-md-6">

                                  <div class="form-group has-select">

                                    <label>

                                    <?php esc_html_e('Select Month', 'service-finder'); ?>

                                    </label>

                                    <select id="twocheckout_card_month" name="twocheckout_card_month" class="form-control sf-form-control sf-select-box" title="Select Month">

                                      <option value="1"><?php echo esc_html__('January', 'service-finder') ?></option>

                                      <option value="2"><?php echo esc_html__('February', 'service-finder')?></option>

                                      <option value="3"><?php echo esc_html__('March', 'service-finder')?></option>

                                      <option value="4"><?php echo esc_html__('April', 'service-finder')?></option>

                                      <option value="5"><?php echo esc_html__('May', 'service-finder')?></option>

                                      <option value="6"><?php echo esc_html__('June', 'service-finder')?></option>

                                      <option value="7"><?php echo esc_html__('July', 'service-finder')?></option>

                                      <option value="8"><?php echo esc_html__('August', 'service-finder')?></option>

                                      <option value="9"><?php echo esc_html__('September', 'service-finder')?></option>

                                      <option value="10"><?php echo esc_html__('October', 'service-finder')?></option>

                                      <option value="11"><?php echo esc_html__('November', 'service-finder')?></option>

                                      <option value="12"><?php echo esc_html__('December', 'service-finder')?></option>

                                    </select>

                                  </div>

                                </div>

                                <div class="col-md-6">

                                  <div class="form-group has-select">

                                    <label>

                                    <?php esc_html_e('Select Year', 'service-finder'); ?>

                                    </label>

                                    <select id="twocheckout_card_year" name="twocheckout_card_year" class="form-control sf-form-control sf-select-box"  title="Select Year">

                                      <?php

                                                                $year = date('Y');

                                                                for($i = $year;$i<=$year+50;$i++){

                                                                    echo '<option value="'.$i.'">'.$i.'</option>';

                                                                }

                                                                ?>

                                    </select>

                                  </div>

                                </div>

                              </div>

                            <div id="bookingpayulatamcardinfo" class="default-hidden">

                                <div class="col-md-12">

                                  <div class="form-group">

                                    <label>

                                    <?php esc_html_e('Select Card', 'service-finder'); ?>

                                    </label>

                                    <select id="payulatam_cardtype" name="payulatam_cardtype" class="form-control sf-form-control sf-select-box"  title="<?php esc_html_e('Select Card', 'service-finder'); ?>">

                                      <?php

                                      $country = (isset($service_finder_options['payulatam-country'])) ? $service_finder_options['payulatam-country'] : '';

                                      $cards = service_finder_get_cards($country);

                                      foreach($cards as $card){

                                        echo '<option value="'.esc_attr($card).'">'.$card.'</option>';

                                      }

                                                                

                                                                ?>

                                    </select>

                                  </div>

                                </div>

                                <div class="col-md-8">

                                  <div class="form-group">

                                    <label>

                                    <?php esc_html_e('Card Number', 'service-finder'); ?>

                                    </label>

                                    <div class="input-group"> <i class="input-group-addon fa fa-credit-card"></i>

                                      <input type="text" id="payulatam_card_number" name="payulatam_card_number" class="form-control sf-form-control">

                                    </div>

                                  </div>

                                </div>

                                <div class="col-md-4">

                                  <div class="form-group">

                                    <label>

                                    <?php esc_html_e('CVC', 'service-finder'); ?>

                                    </label>

                                    <div class="input-group"> <i class="input-group-addon fa fa-ellipsis-h"></i>

                                      <input type="text" id="payulatam_card_cvc" name="payulatam_card_cvc" class="form-control sf-form-control">

                                    </div>

                                  </div>

                                </div>

                                <div class="col-md-6">

                                  <div class="form-group has-select">

                                    <label>

                                    <?php esc_html_e('Select Month', 'service-finder'); ?>

                                    </label>

                                    <select id="payulatam_card_month" name="payulatam_card_month" class="form-control sf-form-control sf-select-box" title="<?php esc_html_e('Select Month', 'service-finder'); ?>">

                                      <option value="01"><?php echo esc_html__('January', 'service-finder') ?></option>

                                      <option value="02"><?php echo esc_html__('February', 'service-finder')?></option>

                                      <option value="03"><?php echo esc_html__('March', 'service-finder')?></option>

                                      <option value="04"><?php echo esc_html__('April', 'service-finder')?></option>

                                      <option value="05"><?php echo esc_html__('May', 'service-finder')?></option>

                                      <option value="06"><?php echo esc_html__('June', 'service-finder')?></option>

                                      <option value="07"><?php echo esc_html__('July', 'service-finder')?></option>

                                      <option value="08"><?php echo esc_html__('August', 'service-finder')?></option>

                                      <option value="09"><?php echo esc_html__('September', 'service-finder')?></option>

                                      <option value="10"><?php echo esc_html__('October', 'service-finder')?></option>

                                      <option value="11"><?php echo esc_html__('November', 'service-finder')?></option>

                                      <option value="12"><?php echo esc_html__('December', 'service-finder')?></option>

                                    </select>

                                  </div>

                                </div>

                                <div class="col-md-6">

                                  <div class="form-group has-select">

                                    <label>

                                    <?php esc_html_e('Select Year', 'service-finder'); ?>

                                    </label>

                                    <select id="payulatam_card_year" name="payulatam_card_year" class="form-control sf-form-control sf-select-box"  title="<?php esc_html_e('Select Year', 'service-finder'); ?>">

                                      <?php

                                                                $year = date('Y');

                                                                for($i = $year;$i<=$year+50;$i++){

                                                                    echo '<option value="'.$i.'">'.$i.'</option>';

                                                                }

                                                                ?>

                                    </select>

                                  </div>

                                </div>

                              </div>

                            <div id="wiredinfo" class="default-hidden">

                                <div class="col-md-12">

                                    <?php

                                    $pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? $service_finder_options['pay_booking_amount_to'] : '';

                                    if($pay_booking_amount_to == 'admin'){

                                    $description = (!empty($service_finder_options['wire-transfer-description'])) ? $service_finder_options['wire-transfer-description'] : '';

                                    echo $description;

                                    }elseif($pay_booking_amount_to == 'provider'){

                                    echo (!empty($settings['wired_description'])) ? $settings['wired_description'] : '';

                                    }

                                    ?>

                                </div>

                              </div>

                            <?php if($payflag == 1 || service_finder_check_wallet_system() || service_finder_get_payment_goes_to() == 'provider'){ ?>  

                            <div class="col-md-12" id="sf-bookform-submitarea">

                              <div class="form-group">

                                <input name="book-now" id="save-booking" type="submit" value="<?php esc_html_e('Pay Now', 'service-finder'); ?>" class="btn btn-primary center-block">

                              </div>

                            </div>

                            <?php }else{

                            echo '<p>';

                            echo esc_html__('There is no payment method available.','service-finder');

                            echo '</p>';

                            } ?>

                          </div> 

                    </div>

					</div>

				</div>

			</div>

		</div>

	</div>

</div> 



</form>

<div class="booking-panel-overlay"></div> 