<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/
include_once SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/invoice/Invoice.php';
?>
<?php
wp_add_inline_script( 'service_finder-js-invoice-form', '/*Declare global variable*/
var user_id = "'.$globalproviderid.'";', 'after' );

$currUser = wp_get_current_user();
$invoiceData = new SERVICE_FINDER_Invoice();
$customers = $invoiceData->service_finder_getCustomers($globalproviderid);
$services = service_finder_getAllServices($globalproviderid);
$service_finder_options = get_option('service_finder_options');
$customerreplacestring = (!empty($service_finder_options['customer-replace-string'])) ? $service_finder_options['customer-replace-string'] : esc_html__('Customers', 'service-finder');
$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('Providers', 'service-finder');	
?>
<div class="panel panel-default">
<div class="panel-heading sf-panel-heading">
  <h3 class="panel-tittle m-a0"><span class="fa fa-file-text-o"></span> <?php echo (!empty($service_finder_options['label-invoice'])) ? esc_html($service_finder_options['label-invoice']) : esc_html__('Invoice', 'service-finder'); ?> </h3>
</div>
<div class="panel-body sf-panel-body padding-30">
  <div class="margin-b-30 text-right"> <a href="javascript:;" data-toggle="modal" class="btn btn-primary" data-target="#invoice-Modal"><i class="fa fa-file-text-o"></i>
    <?php esc_html_e('Add Invoice', 'service-finder'); ?>
    </a> </div>
  <!--Display Invoice Datatable-->
  <table id="invoice-grid" class="table table-striped table-bordered">
    <thead>
      <tr>
        <th class="text-nowrap"> <div class="checkbox sf-radio-checkbox">
            <input type="checkbox" id="bulkInvoiceDelete">
            <label for="bulkInvoiceDelete"></label>
          </div>
          <button class="btn btn-danger btn-xs" id="deleteInvoiceTriger" title="<?php esc_html_e('Delete', 'service-finder'); ?>"><i class="fa fa-trash-o"></i></button></th>
        <th><?php esc_html_e('Reference No', 'service-finder'); ?></th>
        <th><?php echo esc_html( $customerreplacestring ).' '.esc_html__('Name', 'service-finder'); ?></th>
        <th><?php esc_html_e('Due Date', 'service-finder'); ?></th>
        <th><?php esc_html_e('Payment Method', 'service-finder'); ?></th>
        <th><?php esc_html_e('Total Amount', 'service-finder'); ?></th>
        <th><?php esc_html_e('Admin Fee', 'service-finder'); ?></th>
        <th><?php echo esc_html( $providerreplacestring ).' '.esc_html__('Fee', 'service-finder'); ?></th>
        <th><?php echo esc_html__('Admin pay to', 'service-finder').' '.esc_html( $providerreplacestring ); ?></th>
        <th><?php esc_html_e('Invoice Status', 'service-finder'); ?></th>
        <th><?php esc_html_e('Booking ID', 'service-finder' ); ?></th>
        <th><?php esc_html_e('Txn ID', 'service-finder' ); ?></th>
        <th><?php esc_html_e('Action', 'service-finder'); ?></th>
      </tr>
    </thead>
  </table>
  <!--Booking details start-->
  <div id="invoice-booking-details" class="hidden"> </div>
  <!--Display Invoice Details-->
  <div id="invoice-details" class="hidden"> </div>
  <!--Add Invoice Modal Poup box-->
  <div id="invoice-Modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xlg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">
            <?php esc_html_e('Add Invoice', 'service-finder'); ?>
          </h4>
        </div>
        <div class="modal-body clearfix row">
          <form method="post" class="add-invoice" id="addinvoice">
            <div class="col-md-6">
              <div class="form-group">
                <label>
                <?php esc_html_e('Reference No', 'service-finder'); ?>
                </label>
                <div class="input-group"> <i class="input-group-addon fixed-w fa fa-random gen_ref"></i>
                  <input name="refno" type="text" class="form-control sf-form-control" placeholder="">
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>
                <?php esc_html_e('Due Date', 'service-finder'); ?>
                </label>
                <div class="input-group input-append date invoicedueDatePicker"> <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                  <input type="text" class="form-control sf-form-control" name="dueDate" placeholder="<?php esc_html_e('Due date', 'service-finder'); ?>" />
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group has-select">
                <select title="<?php echo esc_html( $customerreplacestring ); ?>" name="customer" class="form-control sf-form-control sf-select-box">
                  <option value="">
                  <?php esc_html_e('Select', 'service-finder').' '.esc_html( $customerreplacestring ); ?>
                  </option>
                  <?php 
								if(!empty($customers)){
									foreach($customers as $customer){
										echo '<option value="'.esc_attr($customer->email).'">'.$customer->name.' ('.$customer->email.')</option>';
									}	
								}
								?>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group has-select">
                <select title="<?php esc_html_e('Status', 'service-finder'); ?>" name="status" class="form-control sf-form-control sf-select-box">
                  <option value="">
                  <?php esc_html_e('Select Status', 'service-finder'); ?>
                  </option>
                  <option value="canceled">
                  <?php esc_html_e('Canceled', 'service-finder'); ?>
                  </option>
                  <option value="overdue">
                  <?php esc_html_e('Overdue', 'service-finder'); ?>
                  </option>
                  <option value="paid">
                  <?php esc_html_e('Paid', 'service-finder'); ?>
                  </option>
                  <option value="pending">
                  <?php esc_html_e('Pending', 'service-finder'); ?>
                  </option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group form-inline">
                <label class="help-block">
                <?php esc_html_e('Discount', 'service-finder'); ?>
                </label>
                <div class="radio sf-radio-checkbox">
                  <input id="dis-fix" type="radio" name="discount-type" value="fix" checked>
                  <label for="dis-fix">
                  <?php esc_html_e('Fix', 'service-finder'); ?>
                  </label>
                </div>
                <div class="radio sf-radio-checkbox">
                  <input id="dis-percentage" type="radio" name="discount-type" value="percentage">
                  <label for="dis-percentage">
                  <?php esc_html_e('Percentage', 'service-finder'); ?>
                  </label>
                </div>
              </div>
              <div class="form-group">
                <input name="discount" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Discount Amount/Percentage', 'service-finder'); ?>">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group form-inline">
                <label class="help-block">
                <?php esc_html_e('Tax', 'service-finder'); ?>
                </label>
                <div class="radio sf-radio-checkbox">
                  <input id="tax-fix" type="radio" name="tax-type" value="fix" checked>
                  <label for="tax-fix">
                  <?php esc_html_e('Fix', 'service-finder'); ?>
                  </label>
                </div>
                <div class="radio sf-radio-checkbox">
                  <input id="tax-percentage" type="radio" name="tax-type" value="percentage">
                  <label for="tax-percentage">
                  <?php esc_html_e('Percentage', 'service-finder'); ?>
                  </label>
                </div>
              </div>
              <div class="form-group">
                <input name="tax" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Tax Amount/Percentage', 'service-finder'); ?>">
              </div>
            </div>
            <div class="col-md-12">
              <div class="servicearea-group">
                <div class="form-group clearfix ">
                  <div class="col-xs-3">
                    <select title="<?php esc_html_e('Services', 'service-finder'); ?>" name="service_title[0]" data-index="0" class="form-control sf-form-control servicedrp sf-select-box">
                      <option value="new">
                      <?php esc_html_e('New Service', 'service-finder'); ?>
                      </option>
                      <?php 
											if(!empty($services)){
												foreach($services as $service){
													echo '<option value="'.esc_attr($service->id).'">'.stripcslashes($service->service_name).'</option>';
												}	
											}
											?>
                    </select>
                  </div>
                  <div class="col-xs-3">
                    <div class="form-group form-inline text-nowrap">
                      <div class="radio sf-radio-checkbox">
                        <input id="fix-price[0]" type="radio" data-index="0" name="cost_type[0]" value="fix" checked>
                        <label for="fix-price[0]">
                        <?php esc_html_e('Fix', 'service-finder'); ?>
                        </label>
                      </div>
                      <div class="radio sf-radio-checkbox">
                        <input id="hourly-price[0]" type="radio" data-index="0" name="cost_type[0]" value="hourly">
                        <label for="hourly-price[0]">
                        <?php esc_html_e('Hour', 'service-finder'); ?>
                        </label>
                      </div>
                      <div class="num-hours default-hidden num-hrs-btn-in">
                        <input class="service-num-hours" data-index="0" type="text" value="1" name="num_hours[0]">
                      </div>
                    </div>
                  </div>
                  <div class="col-xs-3">
                    <input type="text" name="service_desc[0]" data-index="0" class="form-control sf-form-control" placeholder="<?php esc_html_e('Description', 'service-finder'); ?>">
                  </div>
                  <div class="col-xs-2">
                    <input type="text" name="service_price[0]" data-index="0" class="form-control sf-form-control text-right" placeholder="<?php esc_html_e('Price', 'service-finder'); ?>">
                  </div>
                  <div class="col-xs-1">
                    <button type="button" class="btn btn-default addButton"><i class="fa fa-plus"></i></button>
                  </div>
                </div>
                <!-- The template for adding new field -->
                <div class="form-group hide clearfix " id="serviceTemplate">
                  <div class="col-xs-3 col-xs-offset-1">
                    <select title="Services" name="service_title" data-index="" class="form-control sf-form-control servicedrp sf-select-box">
                      <option value="new">
                      <?php esc_html_e('New Service', 'service-finder'); ?>
                      </option>
                      <?php 
											if(!empty($services)){
												foreach($services as $service){
													echo '<option value="'.esc_attr($service->id).'">'.stripcslashes($service->service_name).'</option>';
												}	
											}
											?>
                    </select>
                  </div>
                  <div class="col-xs-3">
                    <div class="form-group form-inline text-nowrap">
                      <div class="radio sf-radio-checkbox">
                        <input id="fix-price" type="radio" data-index="" name="cost_type" value="fix" checked>
                        <label for="fix-price">
                        <?php esc_html_e('Fix', 'service-finder'); ?>
                        </label>
                      </div>
                      <div class="radio sf-radio-checkbox">
                        <input id="hourly-price" type="radio" data-index="" name="cost_type" value="hourly">
                        <label for="hourly-price">
                        <?php esc_html_e('Hour', 'service-finder'); ?>
                        </label>
                      </div>
                      <div class="num-hours default-hidden num-hrs-btn-in">
                        <input class="service-num-hours num_hours2" type="text" data-index="" value="1" name="num_hours">
                      </div>
                    </div>
                  </div>
                  <div class="col-xs-3">
                    <input type="text" name="service_desc" data-index="" class="form-control sf-form-control" placeholder="<?php esc_html_e('Description', 'service-finder'); ?>">
                  </div>
                  <div class="col-xs-2">
                    <input type="text" name="service_price" data-index="" class="form-control sf-form-control text-right" placeholder="<?php esc_html_e('Price', 'service-finder'); ?>">
                  </div>
                  <div class="col-xs-1">
                    <button type="button" class="btn btn-default removeButton"><i class="fa fa-minus"></i></button>
                  </div>
                </div>
              </div>
            </div>
            <div class="sf-summary-lists">
            <div class="col-md-6 margin-b-30">
              <div class="well well-sm margin-0">
                <h5 class="margin-0">
                  <?php esc_html_e('Amount', 'service-finder'); ?>
                  <span id="total_amount" class="pull-right"><?php echo service_finder_currencysymbol(); ?>0.00</span></h5>
              </div>
              <div class="well well-sm margin-0">
                <h5 class="margin-0">
                  <?php esc_html_e('Discount', 'service-finder'); ?>
                  <span id="total_discount" class="pull-right"><?php echo service_finder_currencysymbol(); ?>0.00</span></h5>
              </div>
              <div class="well well-sm margin-0">
                <h5 class="margin-0">
                  <?php esc_html_e('Tax', 'service-finder'); ?>
                  <span id="total_tax" class="pull-right"><?php echo service_finder_currencysymbol(); ?>0.00</span></h5>
              </div>
              <div class="well well-sm margin-0">
                <h5 class="margin-0">
                  <?php esc_html_e('Total', 'service-finder'); ?>
                  <span id="grand_total" class="pull-right"><?php echo service_finder_currencysymbol(); ?>0.00</span></h5>
              </div>
            </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <textarea name="short-desc" cols="" rows="3+" class="form-control sf-form-control" placeholder="<?php esc_html_e('Enter text', 'service-finder'); ?>"></textarea>
              </div>
            </div>
            <div class="col-md-12">
            <?php
            $admin_fee_type = (!empty($service_finder_options['admin-fee-type'])) ? $service_finder_options['admin-fee-type'] : 0;
			$admin_fee_percentage = (!empty($service_finder_options['admin-fee-percentage'])) ? $service_finder_options['admin-fee-percentage'] : 0;
			$admin_fee_fixed = (!empty($service_finder_options['admin-fee-fixed'])) ? $service_finder_options['admin-fee-fixed'] : 0;
			
			$charge_admin_fee = (!empty($service_finder_options['charge-admin-fee'])) ? $service_finder_options['charge-admin-fee'] : '';
			$charge_admin_fee_from = (!empty($service_finder_options['charge-admin-fee-from'])) ? $service_finder_options['charge-admin-fee-from'] : '';
			
			$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? esc_html($service_finder_options['pay_booking_amount_to']) : '';
			
			$admin_fee_label = (!empty($service_finder_options['admin-fee-label'])) ? esc_html($service_finder_options['admin-fee-label']) : esc_html__('Admin Fee', 'service-finder');
			
			if($charge_admin_fee && $pay_booking_amount_to == 'admin' && (($admin_fee_type == 'fixed' && $admin_fee_fixed > 0) || ($admin_fee_type == 'percentage' && $admin_fee_percentage > 0)) && $charge_admin_fee_from == 'customer'){
			
				if($admin_fee_type == 'fixed'){
					echo '<div class="alert alert-info">';
					echo sprintf( esc_html__('Note: %s %s will be added to total invoice amount.', 'service-finder'), service_finder_money_format($admin_fee_fixed),$admin_fee_label );
					echo '</div>';
				}elseif($admin_fee_type == 'percentage'){
					echo '<div class="alert alert-info">';
					echo sprintf( esc_html__('Note: %s %s will be added to total invoice amount.', 'service-finder'), $admin_fee_percentage.'%',$admin_fee_label );
					echo '</div>';
				}
				
			}elseif($charge_admin_fee && $pay_booking_amount_to == 'admin' && (($admin_fee_type == 'fixed' && $admin_fee_fixed > 0) || ($admin_fee_type == 'percentage' && $admin_fee_percentage > 0)) && $charge_admin_fee_from == 'provider'){
				if($admin_fee_type == 'fixed'){
					echo sprintf( esc_html__('Note: %s %s will be charged from total invoice amount.', 'service-finder'), service_finder_money_format($admin_fee_fixed), $admin_fee_label );
				}elseif($admin_fee_type == 'percentage'){
					echo sprintf( esc_html__('Note: %s %s will be charged from total invoice amount.', 'service-finder'), $admin_fee_percentage.'%',$admin_fee_label );
				}
			}
			?>
            </div>
            <div class="col-md-12">
              <input name="" type="submit" class="btn btn-primary" value="<?php esc_html_e('Submit', 'service-finder'); ?>">
            </div>
          </form>
        </div>
        <div class="modal-footer"> </div>
      </div>
    </div>
  </div>
  <!--Edit Invoice Popup Box-->
  <form method="post" class="edit-invoice default-hidden" id="editInvoice">
    <div class="col-md-6">
      <div class="form-group">
        <label>
        <?php esc_html_e('Reference No', 'service-finder'); ?>
        </label>
        <div class="input-group"> <i class="input-group-addon fixed-w fa fa-random gen_ref"></i>
          <input name="refno" type="text" class="form-control sf-form-control" placeholder="">
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <label>
        <?php esc_html_e('Due Date', 'service-finder'); ?>
        </label>
        <div class="input-group input-append date invoicedueDatePicker"> <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
          <input type="text" class="form-control sf-form-control" name="dueDate" placeholder="<?php esc_html_e('Due date', 'service-finder'); ?>" />
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <select title="Customer" name="customer" class="form-control sf-form-control sf-select-box">
          <?php 
								if(!empty($customers)){
									foreach($customers as $customer){
										echo '<option value="'.esc_attr($customer->email).'">'.$customer->name.' ('.$customer->email.')</option>';
									}	
								}
								?>
        </select>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        <select title="<?php esc_html_e('Status', 'service-finder'); ?>" name="status" class="form-control sf-form-control sf-select-box">
          <option value="canceled">
          <?php esc_html_e('Canceled', 'service-finder'); ?>
          </option>
          <option value="overdue">
          <?php esc_html_e('Overdue', 'service-finder'); ?>
          </option>
          <option value="paid">
          <?php esc_html_e('Paid', 'service-finder'); ?>
          </option>
          <option value="pending">
          <?php esc_html_e('Pending', 'service-finder'); ?>
          </option>
        </select>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group form-inline">
        <label class="help-block">
        <?php esc_html_e('Discount', 'service-finder'); ?>
        </label>
        <div class="radio sf-radio-checkbox">
          <input id="editdis-fix" type="radio" name="discount-type" value="fix" checked>
          <label for="editdis-fix">
          <?php esc_html_e('Fix', 'service-finder'); ?>
          </label>
        </div>
        <div class="radio sf-radio-checkbox">
          <input id="editdis-percentage" type="radio" name="discount-type" value="percentage">
          <label for="editdis-percentage">
          <?php esc_html_e('Percentage', 'service-finder'); ?>
          </label>
        </div>
      </div>
      <div class="form-group">
        <input name="discount" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Discount Amount/Percentage', 'service-finder'); ?>">
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group form-inline">
        <label class="help-block">
        <?php esc_html_e('Tax', 'service-finder'); ?>
        </label>
        <div class="radio sf-radio-checkbox">
          <input id="edittax-fix" type="radio" name="tax-type" value="fix" checked>
          <label for="edittax-fix">
          <?php esc_html_e('Fix', 'service-finder'); ?>
          </label>
        </div>
        <div class="radio sf-radio-checkbox">
          <input id="edittax-percentage" type="radio" name="tax-type" value="percentage">
          <label for="edittax-percentage">
          <?php esc_html_e('Percentage', 'service-finder'); ?>
          </label>
        </div>
      </div>
      <div class="form-group">
        <input name="tax" type="text" class="form-control sf-form-control" placeholder="<?php esc_html_e('Tax Amount/Percentage', 'service-finder'); ?>">
      </div>
    </div>
    <div class="col-md-12">
      <div class="invoiceservices-bx">
        <div id="editservicedata" class="servicearea-group">
          <div class="form-group clearfix" >
            <div class="col-xs-3">
              <select title="<?php esc_html_e('Services', 'service-finder'); ?>" name="service_title[0]" data-index="0" class="form-control sf-form-control sf-select-box">
                <option value="new">
                <?php esc_html_e('New Service', 'service-finder'); ?>
                </option>
                <?php 
											if(!empty($services)){
												foreach($services as $service){
													echo '<option value="'.esc_attr($service->id).'">'.stripcslashes($service->service_name).'</option>';
												}	
											}
											?>
              </select>
            </div>
            <div class="col-xs-3">
              <div class="form-group form-inline text-nowrap">
                <div class="radio sf-radio-checkbox">
                  <input id="editfix-price[0]" type="radio" data-index="0" name="cost_type[0]" value="fix" checked>
                  <label for="editfix-price[0]">
                  <?php esc_html_e('Fix', 'service-finder'); ?>
                  </label>
                </div>
                <div class="radio sf-radio-checkbox">
                  <input id="edithourly-price[0]" type="radio" data-index="0" name="cost_type[0]" value="hourly">
                  <label for="edithourly-price[0]">
                  <?php esc_html_e('Hour', 'service-finder'); ?>
                  </label>
                </div>
                <div class="num-hours default-hidden num-hrs-btn-in">
                  <input class="service-num-hours" data-index="0" type="text" value="1" name="num_hours[0]">
                </div>
              </div>
            </div>
            <div class="col-xs-3">
              <input type="text" name="service_desc[0]" data-index="0" class="form-control sf-form-control" placeholder="<?php esc_html_e('Description', 'service-finder'); ?>">
            </div>
            <div class="col-xs-2">
              <input type="text" name="service_price[0]" data-index="0" class="form-control sf-form-control text-right" placeholder="<?php esc_html_e('Price', 'service-finder'); ?>">
            </div>
            <div class="col-xs-1">
              <button type="button" class="btn btn-default addButton"><i class="fa fa-plus"></i></button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 margin-b-30">
      <div class="sf-summary-lists">
      <div class="well well-sm margin-0">
        <h5 class="margin-0">
          <?php esc_html_e('Amount', 'service-finder'); ?>
          <span id="total_amount" class="pull-right"></span></h5>
      </div>
      <div class="well well-sm margin-0">
        <h5 class="margin-0">
          <?php esc_html_e('Discount', 'service-finder'); ?>
          <span id="total_discount" class="pull-right"></span></h5>
      </div>
      <div class="well well-sm margin-0">
        <h5 class="margin-0">
          <?php esc_html_e('Tax', 'service-finder'); ?>
          <span id="total_tax" class="pull-right"></span></h5>
      </div>
      <div class="well well-sm margin-0">
        <h5 class="margin-0">
          <?php esc_html_e('Total', 'service-finder'); ?>
          <span id="grand_total" class="pull-right"></span></h5>
      </div>
      </div>
    </div>
    <div class="col-md-12">
      <div class="form-group">
        <textarea name="short-desc" cols="" rows="3+" class="form-control sf-form-control" placeholder="<?php esc_html_e('Enter text', 'service-finder'); ?>"></textarea>
      </div>
    </div>
    <div class="col-md-12">
            <?php
            $admin_fee_type = (!empty($service_finder_options['admin-fee-type'])) ? $service_finder_options['admin-fee-type'] : 0;
			$admin_fee_percentage = (!empty($service_finder_options['admin-fee-percentage'])) ? $service_finder_options['admin-fee-percentage'] : 0;
			$admin_fee_fixed = (!empty($service_finder_options['admin-fee-fixed'])) ? $service_finder_options['admin-fee-fixed'] : 0;
			
			$charge_admin_fee = (!empty($service_finder_options['charge-admin-fee'])) ? $service_finder_options['charge-admin-fee'] : '';
			$charge_admin_fee_from = (!empty($service_finder_options['charge-admin-fee-from'])) ? $service_finder_options['charge-admin-fee-from'] : '';
			
			$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? esc_html($service_finder_options['pay_booking_amount_to']) : '';
			
			$admin_fee_label = (!empty($service_finder_options['admin-fee-label'])) ? esc_html($service_finder_options['admin-fee-label']) : esc_html__('Admin Fee', 'service-finder');
			
			if($charge_admin_fee && $pay_booking_amount_to == 'admin' && (($admin_fee_type == 'fixed' && $admin_fee_fixed > 0) || ($admin_fee_type == 'percentage' && $admin_fee_percentage > 0)) && $charge_admin_fee_from == 'customer'){
			
				if($admin_fee_type == 'fixed'){
					echo '<div class="alert alert-info">';
					echo sprintf( esc_html__('Note: %s %s will be added to total invoice amount.', 'service-finder'), service_finder_money_format($admin_fee_fixed),$admin_fee_label );
					echo '</div>';
				}elseif($admin_fee_type == 'percentage'){
					echo '<div class="alert alert-info">';
					echo sprintf( esc_html__('Note: %s %s will be added to total invoice amount.', 'service-finder'), $admin_fee_percentage.'%',$admin_fee_label );
					echo '</div>';
				}
				
			}elseif($charge_admin_fee && $pay_booking_amount_to == 'admin' && (($admin_fee_type == 'fixed' && $admin_fee_fixed > 0) || ($admin_fee_type == 'percentage' && $admin_fee_percentage > 0)) && $charge_admin_fee_from == 'provider'){
				if($admin_fee_type == 'fixed'){
					echo sprintf( esc_html__('Note: %s %s will be charged from total invoice amount.', 'service-finder'), service_finder_money_format($admin_fee_fixed), $admin_fee_label );
				}elseif($admin_fee_type == 'percentage'){
					echo sprintf( esc_html__('Note: %s% %s will be charged from total invoice amount.', 'service-finder'), $admin_fee_percentage,$admin_fee_label );
				}
			}
			?>
            </div>
    <div class="col-md-12">
      <input name="" type="submit" class="btn btn-primary" value="<?php esc_html_e('Submit', 'service-finder'); ?>">
    </div>
  </form>
</div>
</div>
