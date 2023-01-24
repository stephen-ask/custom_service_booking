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
wp_enqueue_script('service_finder-js-wallet');
$wpdb = service_finder_plugin_global_vars('wpdb');
$service_finder_Tables = service_finder_plugin_global_vars('service_finder_Tables');
$service_finder_options = get_option('service_finder_options');
$paymentsystem = service_finder_plugin_global_vars('paymentsystem');
$current_user = service_finder_plugin_global_vars('current_user');

if(service_finder_getUserRole($current_user->ID) == 'Customer'){
$user_id = $current_user->ID; 
}else{
$user_id = $globalproviderid; 
}

$minamount = (!empty($service_finder_options['min-wallet-amount'])) ? esc_html($service_finder_options['min-wallet-amount']) : 0;
$maxamount = (!empty($service_finder_options['max-wallet-amount'])) ? esc_html($service_finder_options['max-wallet-amount']) : 0;

wp_add_inline_script( 'service_finder-js-wallet', '/*Declare global variable*/
var minamount = "'.floatval($minamount).'";
var maxamount = "'.floatval($maxamount).'";
var user_id = "'.$user_id.'";', 'after' );
?>
<div class="panel panel-default">
<div class="panel-heading sf-panel-heading">
  <h3 class="panel-tittle m-a0"><span class="fa fa-money"></span> <?php echo (!empty($service_finder_options['label-wallet'])) ? esc_html($service_finder_options['label-wallet']) : esc_html__('Wallet', 'service-finder'); ?> </h3>
  <div id="sf-wallet-top-balance" style="display:none">
	<img src="<?php echo SERVICE_FINDER_BOOKING_IMAGE_URL ?>/walleticon.png">
	<?php 
    $currentamount = service_finder_get_wallet_amount($user_id);
    echo service_finder_money_format($currentamount); 
    ?>
  </div>
</div>
<div class="panel-body sf-panel-body padding-30">
  <div id="sf-wallet-bx">
  <div class="sf-wallet-section">
  <div class="icon-bx-lg rounded-bx mostion"><img src="<?php echo SERVICE_FINDER_BOOKING_IMAGE_URL ?>/walleticon.png"></div>
  <ul class="list-unstyled clear sf-plans-available margin-0">
    <li>
        <h5 class="sf-wallet-title"><?php esc_html_e('Wallet Balance', 'service-finder'); ?></h5>
        <strong class="sf-wallet-amount">
		<?php 
		$currentamount = service_finder_get_wallet_amount($user_id);
		echo service_finder_money_format($currentamount); 
		?></strong>
    </li>
</ul>	
  
  <button class="btn btn-primary" data-toggle="collapse" data-target="#sf-add-money">
  <i class="fa fa-plus"></i>
  <?php esc_html_e('Add Balance To Wallet', 'service-finder'); ?>
  </button>
  <button class="btn btn-primary view-wallet-history">
  <i class="fa fa-eye"></i>
  <?php esc_html_e('View Wallet History', 'service-finder'); ?>
  </button>
  </div>
  <div id="sf-add-money" class="collapse">
  	<form class="wallet-payment-form sf-card-group" method="post">
	<div class="col-lg-6">
      <div class="form-group">
        <div class="input-group"> <i class="input-group-addon fixed-w fa fa-money"></i>
          <input type="text" class="form-control sf-form-control" name="amount" placeholder="<?php esc_html_e('Please enter amount', 'service-finder'); ?>" >
        </div>
      </div>
    </div>
	<?php
	$args = array(
		'user_id' => base64_encode($user_id)
	);
	echo service_finder_site_payments('wallet',$args);
	?>
    </form>
  </div>
</div>
  <div id="sf-wallet-history" style="display:none">
  <div class="margin-b-30 text-right">
    <button type="button" class="btn btn-primary close-wallet-history"><i class="fa fa-arrow-left"></i><?php echo esc_html__('Back', 'service-finder')?></button>
  </div>	
  <!--Display Applied Jobs into datatable-->
  <table id="wallet-history-grid" class="table table-striped table-bordered">
    <thead>
      <tr>
        <th><?php esc_html_e('Transaction Date', 'service-finder'); ?></th>
        <th><?php esc_html_e('Txn ID', 'service-finder'); ?></th>
        <th><?php esc_html_e('Payment Method', 'service-finder'); ?></th>
        <th><?php esc_html_e('Payment Status', 'service-finder'); ?></th>
        <th><?php esc_html_e('Amount', 'service-finder'); ?></th>
        <th><?php esc_html_e('Dr/Cr', 'service-finder'); ?></th>
        <th><?php esc_html_e('Debit/Credit for', 'service-finder'); ?></th>
      </tr>
    </thead>
  </table>
</div>
</div>
</div>

