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
$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('Provider', 'service-finder');	

$currUser = wp_get_current_user(); 

wp_add_inline_script( 'service_finder-js-bookings-form', '/*Declare global variable*/
var user_id = "'.$currUser->ID.'";', 'after' );

require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/favorites/Favorites.php';
?>
<div class="panel panel-default">
<div class="panel-heading sf-panel-heading">
  <h3 class="panel-tittle m-a0"><span class="fa fa-heart"></span> <?php esc_html_e('My Favorites', 'service-finder'); ?> </h3>
</div>
<div class="panel-body sf-panel-body padding-30">
  <table id="favorites-grid" class="table table-striped margin-0 favorites-listing">
    <thead>
      <tr>
        <th> <div class="checkbox sf-radio-checkbox">
            <input type="checkbox" id="bulkFavoritesDelete">
            <label for="bulkFavoritesDelete"></label>
          </div>
          <button class="btn btn-danger btn-xs" id="deleteFavoritesTriger" title="<?php esc_html_e('Delete', 'service-finder'); ?>"><i class="fa fa-trash-o"></i></button></th>
        <th> <?php echo esc_html($providerreplacestring).' '.esc_html__('Name', 'service-finder'); ?></th>
        <th> <?php esc_html_e('Category', 'service-finder'); ?></th>
        <th> <?php esc_html_e('Link', 'service-finder'); ?></th>
      </tr>
    </thead>
  </table>
</div>
</div>
