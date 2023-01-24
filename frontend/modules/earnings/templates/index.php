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

wp_add_inline_script( 'service_finder-js-earnings', '/*Declare global variable*/
var user_id = "'.$globalproviderid.'";', 'after' );
?>
<div class="sf-page-title">
<h2><?php echo (!empty($service_finder_options['label-earnings-dues'])) ? esc_html($service_finder_options['label-earnings-dues']) : esc_html__('Earnings & Dues', 'service-finder'); ?></h2>
</div>

<div class="panel panel-default">
<div class="panel-heading sf-panel-heading">
  <h3 class="panel-tittle m-a0"><span class="fa fa-money"></span> <?php esc_html_e('Total Earnings', 'service-finder'); ?> </h3>
</div>
<div class="panel-body sf-panel-body padding-30">
  <div class="row">
    <strong class="sf-total-earnings">
    <?php 
    echo service_finder_money_format(service_finder_get_total_earnings($globalproviderid)); 
    ?></strong>
  </div>
</div>
</div>

<div class="panel panel-default">
<div class="panel-heading sf-panel-heading">
  <h3 class="panel-tittle m-a0"><span class="fa fa-money"></span> <?php esc_html_e('Total Dues', 'service-finder'); ?> </h3>
</div>
<div class="panel-body sf-panel-body padding-30">
  <div class="row">
    <strong class="sf-total-dues">
		<?php 
		echo service_finder_money_format(service_finder_get_total_dues($globalproviderid)); 
		?></strong>
  </div>
</div>
</div>


