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

	$catimage = (isset($service_finder_options['category-image-banner'])) ? $service_finder_options['category-image-banner'] : false;

	

	if($catimage){

		$bannerimg = service_finder_getCategoryImage(get_queried_object()->term_id,'full');

		$bannerclass = '';

	}elseif($innersubheaderbgimage){

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

  <?php if(!$service_finder_options['category-search-bar'] || $bannerimg != ""){ ?>

  <div class="sf-search-benner sf-overlay-wrapper">

  <div class="banner-inner-row <?php echo esc_html($bannerclass); ?>" style="background-image:url(<?php echo esc_url($bannerimg); ?>);">

  <?php if($bannerimg != ''){ ?>
  <div class="sf-overlay-main" style="opacity:<?php echo $bgopacity ?>; background-color:<?php echo $bgcolor ?>;"></div>
  <?php } ?>

  </div>

  <?php if(!$service_finder_options['category-search-bar']){ 

   $classes = (service_finder_themestyle() == 'style-2') ? 'sf-search-result' : '';

   $srhposition = (!empty($service_finder_options['search-bar-position-categorypage'])) ? $service_finder_options['search-bar-position-categorypage'] : 'bottom';

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

  <div class="sf-cat-text"><?php echo get_queried_object()->description; ?></div>

  </div>

    <?php

			$texonomy = 'providers-category';

            $term_children = get_term_children(get_queried_object()->term_id,$texonomy);

			

			if(!empty($term_children)){

			foreach($term_children as $term_child_id) {

				

				$child_term = get_term( $term_child_id, $texonomy );

				

				if($child_term->parent == get_queried_object()->term_id){

				$term = get_term_by( 'id', $term_child_id, $texonomy );

	

				$namearray[$term->name] = $term_child_id;

				}



            }

			ksort($namearray);

			}

			

			if(!empty($namearray)){

				if($service_finder_options['sub-category-layout'] == 'style-1'){

					echo '<div class="provider-sub-category equal-col-outer">';

					echo '<div class="row">';

				}elseif($service_finder_options['sub-category-layout'] == 'style-2'){

					echo '<div class="provider-sub-category-center equal-col-outer">';

					echo '<div class="row">';

				}elseif($service_finder_options['sub-category-layout'] == 'style-3'){

					echo '<div class="col-md-12 sf-cati-all">';

					echo '<ul class="list-unstyled">';

				}

				foreach($namearray as $term_child_id) {



					$term_child = get_term_by('id',$term_child_id,$texonomy);

					

					$catimage = service_finder_getCategoryImage($term_child_id,'service_finder-category-home');

					

					$caticon = service_finder_getCategoryIcon($term_child_id, 'service_finder-category-icon');

					

					if($catimage != ""){

					$imgtag = '<img src="'.esc_url($catimage).'" width="600" height="350" alt="">';

					$class2 = '';

					}else{

					$imgtag = '';

					$class2 = 'sf-cate-no-img';

					}

					if($caticon != ""){

					$imgicon = '<img src="'.esc_url($caticon).'" width="600" height="350" alt="">';

					}else{

					$imgicon = '';

					}

					

					?>

    <?php if($service_finder_options['sub-category-layout'] == 'style-1'){?>

    <!-- Display Subcategory in style 1 -->

    <div class="col-md-4 col-sm-6">

    <a href="<?php echo esc_url(get_term_link( $term_child )); ?>">

    <div class="sf-element-bx equal-col">

      <div class="<?php echo sanitize_html_class($class2); ?> overlay-bg">

        <div class="sf-thum-bx sf-catagories-listing img-effect1" style="background-image:url(<?php echo $catimage; ?>);"> </div>

        <span  class="service-plus pull-right"> <i class="fa fa-user-plus"></i><?php echo '('.service_finder_getTotalProvidersByCategory( $term_child_id ).')'; ?> </span>

        <h4 class="service-name pull-left"><?php echo esc_html($term_child->name)?></h4>

      </div>

      </div>

    </a>  

    </div>

    <?php }elseif($service_finder_options['sub-category-layout'] == 'style-2'){ ?>

    <!-- Display Subcategory in style 2 -->

    <div class="col-md-3 col-sm-4 col-xs-6">

      <div class="sf-element-bx equal-col">

        <div class="icon-bx-md rounded-bx"> <a href="<?php echo esc_url(get_term_link( $term_child )); ?>"> <?php echo $imgicon; ?> </a> </div>

        <h5><a href="<?php echo esc_url(get_term_link( $term_child )); ?>"><?php echo esc_html($term_child->name)?></a></h5>

        <p><?php echo nl2br(service_finder_getExcerpts($term_child->description,0,60)) ?></p>

      </div>

    </div>

    <?php }elseif($service_finder_options['sub-category-layout'] == 'style-3'){ ?>

        <li><span>( <i class="fa fa-user-plus"></i> <strong><?php echo service_finder_getTotalProvidersByCategory( $term_child_id ); ?></strong> )</span><a href="<?php echo esc_url(get_term_link( $term_child )); ?>"><?php echo esc_html($term_child->name)?></a></li>

    <?php } ?>

    <?php

					

				}

			if($service_finder_options['sub-category-layout'] == 'style-1'){

				echo '</div>';

				echo '</div>';

			}elseif($service_finder_options['sub-category-layout'] == 'style-2'){

				echo '</div>';

				echo '</div>';

			}elseif($service_finder_options['sub-category-layout'] == 'style-3'){

				echo '</div>';

				echo '</ul>';

			}

			}

			?>

    <!-- Display category result start -->

    <?php require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/category/templates/providers-category.php';?>

    <!-- Display category result end -->

  </div>

</div>

<?php

get_footer();

