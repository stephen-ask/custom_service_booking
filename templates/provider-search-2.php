<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/


get_header(); 

$service_finder_options = get_option('service_finder_options');
$wpdb = service_finder_plugin_global_vars('wpdb');
?>
<!-- Search Style 1 Start -->
<?php if($service_finder_options['search-template'] == 'style-1' || !service_finder_show_map_on_site()){
?>
<div class="page-content">
    
    <div class="sf-seach-vertical sf-search-bar-panel">
        <div class="search-form ">
          <?php echo do_shortcode('[service_finder_search_form]'); ?>
        </div>
    </div>
    
    <div class="sf-search-result-area">
    
    	<div class="sf-map-row"><?php echo do_shortcode('[service_finder_map_search]'); ?></div>
        
        <?php
		$defaultview = (!empty($service_finder_options["default-view"])) ? esc_js($service_finder_options["default-view"]) : "grid-4";
		$viewtype = (isset($_GET['viewtype'])) ? $_GET['viewtype'] : $defaultview; ?>
		<?php echo do_action('service_finder_availability_search_filter_style_4', $viewtype); ?>
        
        <?php require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/search/templates/search-providers.php';?>
            
    </div>

       
</div>

<?php
}elseif($service_finder_options['search-template'] == 'style-2'){
?>
<div class="page-content">
    <div class="sf-seach-vertical sf-search-bar-panel">
        <div class="search-form ">
          <?php echo do_shortcode('[service_finder_search_form]'); ?>
        </div>
    </div>
    
    <div class="sf-search-result-area">
    
    	<div class="google-map-leftslide default-hidden" id="showhidemap"><a href="javascript:;" class="google-map-leftslide-close showhidemapbtn"><i class="fa fa-close"></i></a><?php echo do_shortcode('[service_finder_map_search]'); ?></div>
        <?php
		$defaultview = (!empty($service_finder_options["default-view"])) ? esc_js($service_finder_options["default-view"]) : "grid-4";
		$viewtype = (isset($_GET['viewtype'])) ? $_GET['viewtype'] : $defaultview; ?>
		<?php echo do_action('service_finder_availability_search_filter_style_4', $viewtype); ?>
        
        <?php require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/search/templates/search-providers-v2.php';?>
    </div>

       
</div>
<?php
} ?>

<?php wp_footer(); ?>
<!-- Loading area start for 2 search style -->
<div class="loading-area default-hidden">
  <div class="loading-box"></div>
  <div class="loading-pic"></div>
</div>
<!-- Loading area end for 2 search style -->
</body>
</html>
