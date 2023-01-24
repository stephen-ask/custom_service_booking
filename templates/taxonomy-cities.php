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

wp_add_inline_script( 'bootstrap', 'jQuery(document).ready(function($) {
/*Load cities on country change event*/
jQuery("body").on("change", "#country", function(){
        var country = jQuery(this).val();
		
		var data = {
			  "action": "load_cities",
			  "country": country
			};
			
	  var formdata = jQuery.param(data);
	  
	  jQuery.ajax({

						type: "POST",

						url: ajaxurl,

						data: formdata,
						
						dataType: "json",
						
						beforeSend: function() {
							jQuery(".loading-srh-bar").show();
						},

						success:function (data, textStatus) {
							jQuery(".loading-srh-bar").hide();
							if(data["status"] == "success"){
								
								jQuery("select[name=\'city\']").html(data["html"]);
								jQuery("select[name=\'city\']").selectpicker("refresh");
							}
						
						}

					});
		
	
	});
})', 'after' );

?>

<div class="page-content clearfix">
<?php
$subheader = service_finder_sub_header_pl();
$innersubheaderbgimage = service_finder_innerpage_banner_pl();
if($subheader){
	$cityimage = (!empty($service_finder_options['city-image-banner'])) ? $service_finder_options['city-image-banner'] : '';
	
	if($innersubheaderbgimage){
		$bannerimg = service_finder_innerpage_banner_pl();
		$bannerclass = '';
	}else{
		$bannerimg = '';
		$bannerclass = 'provider-banner-off overlay-black-middle';
	}
}else{
	$bannerimg = '';
	$bannerclass = 'provider-banner-off overlay-black-middle';
}
	$bgcolor = (!empty($service_finder_options['inner-banner-bg-color'])) ? $service_finder_options['inner-banner-bg-color'] : '';
	$bgopacity = (!empty($service_finder_options['inner-banner-opacity'])) ? $service_finder_options['inner-banner-opacity'] : '';
?>
  <!-- Display Banner -->
  <?php if(!$service_finder_options['city-search-bar'] || $bannerimg != ""){ ?>
  <div class="sf-search-benner sf-overlay-wrapper">
  <div class="banner-inner-row <?php echo esc_html($bannerclass); ?>" style="background-image:url(<?php echo esc_url($bannerimg); ?>);">
  <?php if($bannerimg != ''){ ?>
  <div class="sf-overlay-main" style="opacity:<?php echo $bgopacity ?>; background-color:<?php echo $bgcolor ?>;"></div>
  <?php } ?>
  </div>
  <?php if(!$service_finder_options['city-search-bar']){ 
   $classes = (service_finder_themestyle() == 'style-2') ? 'sf-search-result' : '';
   $srhposition = (!empty($service_finder_options['search-bar-position-citypage'])) ? $service_finder_options['search-bar-position-citypage'] : 'bottom';
	if($srhposition == 'middle'){
		$positionclass = 'pos-v-center';
	}else{
		$positionclass = 'pos-v-bottom';
    }
  ?>
  <div class="sf-find-bar-inr <?php echo $classes; ?> <?php echo sanitize_html_class($positionclass); ?>">
      <div class="container">
        <!-- Search form -->
        <?php if(service_finder_themestyle() == 'style-2'){ ?>
      <ul class="sf-search-title clearfix">
            <li><?php echo esc_html__('Search Provider', 'service-finder'); ?></li>
        </ul>
       <?php } ?>
       <?php $advanceclass = (service_finder_check_advance_search()) ? '' : 'sf-empty-radius'; ?>
	    <div class="search-form <?php echo sanitize_html_class($advanceclass); ?>">
          <?php echo do_shortcode('[service_finder_search_form]'); ?>
        </div>
      </div>
    </div>
  <?php } ?>  
  </div> 
  <?php } ?> 
  <?php require SERVICE_FINDER_BOOKING_FRONTEND_DIR . '/breadcrumb.php'; //Breadcrumb ?>
  <!-- Left & right section start -->
  <div class="container">
  <div class="sf-category-des">
  <h2 class="sf-title"><?php echo get_queried_object()->name; ?></h2>
  </div>
    <!-- Display category result start -->
    <?php require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/cities/templates/index.php';?>
    <!-- Display category result end -->
  </div>
</div>
<?php
get_footer();
