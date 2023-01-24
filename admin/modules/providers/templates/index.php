<?php 
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
$service_finder_options = get_option('service_finder_options');
$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('Provider', 'service-finder');	
?>
<!--Template for providers in admin panel-->

<div class="sf-wpbody-inr">
  <div class="sedate-title">
    <h2>
      <?php echo esc_html($providerreplacestring); ?>
    </h2>
  </div>
  <div class="sf-by-provider">
    <div class="row">
    <div class="col-sm-6">
	<?php echo esc_html__( 'By Featured', 'service-finder' ); ?> -
    <select class="sf-select-box form-control sf-form-control" name="byfeatured" id="byfeatured">
    <?php
	echo '<option value="">'.esc_html__( 'All ', 'service-finder' ).esc_html($providerreplacestring).'</option>';
	echo '<option value="Featured">'.esc_html__( 'Featured ', 'service-finder' ).esc_html($providerreplacestring).'</option>';
	?>
    </select>
    </div>
    <div class="col-sm-6">
    <?php echo esc_html__( 'By Approval', 'service-finder' ); ?> -
    <select class="sf-select-box form-control sf-form-control" name="byapproval" id="byapproval">
    <?php
	echo '<option value="">'.esc_html__( 'All ', 'service-finder' ).esc_html($providerreplacestring).'</option>';
	echo '<option value="UnBlock">'.esc_html__( 'Blocked ', 'service-finder' ).'</option>';
	echo '<option value="Approve">'.esc_html__( 'Moderation Pending', 'service-finder' ).'</option>';
	echo '<option value="Block">'.esc_html__( 'Approved', 'service-finder' ).'</option>';
	echo '<option value="Re-Approve">'.esc_html__( 'Rejected', 'service-finder' ).'</option>';
	?>
    </select>
    </div>
    </div>
    <?php if( class_exists( 'WC_Vendors' ) && class_exists( 'WooCommerce' ) ) { ?>
    <div class="aon-bulk-vendor"><a href="javascript:;" class="btn btn-primary mekeitvendors"><?php echo esc_html__( 'All Providers Make Vendors', 'service-finder' ); ?></a></div>
    <?php } ?>
  </div>
  <input type="hidden" name="minvalue" id="minvalue" value="<?php echo (!empty($service_finder_options['feature-min-max-days-min'])) ? esc_attr($service_finder_options['feature-min-max-days-min']) : '' ?>" >
  <input type="hidden" name="maxvalue" id="maxvalue" value="<?php echo (!empty($service_finder_options['feature-min-max-days-max'])) ? esc_attr($service_finder_options['feature-min-max-days-max']) : '' ?>" >
  
  <div class="datatable-outer">
  <div class="table-responsive">
    <table id="providers-grid" class="table table-striped sf-table">
    <thead>
      <tr>
        <th></th>
        <th><input type="checkbox"  id="bulkProvidersDelete"  />
          <button id="deleteProvidersTriger" class="btn btn-danger btn-xs">
          <i class="fa fa-trash-o"></i>
          </button></th>
        <th><?php esc_html_e( 'Name', 'service-finder' ); ?></th>
        <th><?php esc_html_e( 'Email', 'service-finder' ); ?></th>
        <th><?php esc_html_e( 'Membership Plan', 'service-finder' ); ?></th>
        <th><?php esc_html_e( 'Featured', 'service-finder' ); ?></th>
        <th><?php esc_html_e( 'Payment Method', 'service-finder' ); ?></th>
        <th><?php esc_html_e( 'Status', 'service-finder' ); ?></th>
        <th><?php esc_html_e( 'Action', 'service-finder' ); ?></th>
      </tr>
    </thead>
    </table>
  </div>
  </div>
</div>
