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
<!--Template for dispaly featured requests-->
<?php
$service_finder_options = get_option('service_finder_options');
$service_finder_Tables = service_finder_plugin_global_vars('service_finder_Tables');
?>
<div class="sf-wpbody-inr">
  <div class="sedate-title">
    <h2>
      <?php esc_html_e( 'Notifications', 'service-finder' ); ?>
    </h2>
  </div>
  <div class="datatable-outer">
  <div class="table-responsive">
    <table id="admin-notifications-grid" class="table table-striped sf-table">
    <thead>
      <tr>
        <th></th>
        <th><input type="checkbox"  id="bulkAdminNotificationDelete"  />
          <button id="deleteNotificationTriger" class="btn btn-danger btn-xs">
          <i class="fa fa-trash-o"></i>
        </button></th>
        <th><?php esc_html_e( 'Date', 'service-finder' ); ?></th>
        <th><?php esc_html_e( 'Title', 'service-finder' ); ?></th>
        <th><?php esc_html_e( 'Notification', 'service-finder' ); ?></th>
      </tr>
    </thead>
    </table>
  </div>
  </div>
</div>