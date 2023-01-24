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

$texonomy = 'providers-category';

$bannerimg = service_finder_getCategoryImage(get_queried_object()->term_id,'full');

?>

<div class="page-content">

    

    <div class="section-content sf-allCaty-info-wrap">

        <div class="container">



                <div class="row">

                    <div class="col-md-6">

                        <div class="sf-caty-pic" style="background-image:url(<?php echo esc_url($bannerimg); ?>);">

                            <div class="sf-caty-btn"><?php echo sprintf(esc_html__('View %s', 'service-finder'),service_finder_provider_replace_string()); ?></div>

                            <div class="sf-caty-cirle"><a href="#sf-cati-results"><i class="fa fa-arrow-circle-down"></i></a></div>

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="sf-caty-info">

                        	<?php

                            $catid = get_queried_object()->term_id;

							$term = get_term( $catid, $texonomy );

							if($term->parent > 0){

							$termid = $term->parent;

							?>

							<div><strong><a href="<?php echo esc_url(get_term_link( $term->parent, $texonomy )); ?>"><?php echo service_finder_getCategoryName($term->parent); ?></a></strong> / <?php echo service_finder_getCategoryName($catid); ?></div>

							<?php

							}else{
							?>
							<div><strong><a href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html__( 'Home', 'service-finder' ); ?></a></strong> / <?php echo service_finder_getCategoryName($catid); ?></div>
							<?php
							}

							?>

                            <h4><?php echo get_queried_object()->name; ?></h4>

                            <div class="sf-caty-text">

                                <?php echo get_queried_object()->description; ?>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

    </div>

    

    <?php

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

	?>

	<div class="section-content sf-allCaty-grid-wrap">

        <div class="container">



            <div class="section-content">

            	<?php if($service_finder_options['sub-category-layout'] == 'style-2'){ ?>

                <div class="row">

                    <?php 

					foreach($namearray as $term_child_id) { 

					$term_child = get_term_by('id',$term_child_id,$texonomy);

					$caticon = service_finder_getCategoryIcon($term_child_id, 'service_finder-category-icon');

					?>

                    <div class="col-md-3">

                        <a href="<?php echo esc_url(get_term_link( $term_child )); ?>" class="sf-caty-link">
                        <div class="sf-caty-icon-col">

                            <div class="sf-caty-icon-box"><img src="<?php echo esc_url($caticon); ?>" alt=""/></div>

                            <h5 class="sf-caty-icon-title"><?php echo esc_html($term_child->name)?></h5>

                        </div>
						</a>
                    </div>

					<?php } ?>

                </div>

                <?php }else{ ?>

                <div class="owl-carousel owl-caty-carousel sf-owl-arrow">

                    <?php 

					$k = 0;

					$m = 1;

					$totalchildcategories = count($namearray);

					foreach($namearray as $term_child_id) { 

					$term_child = get_term_by('id',$term_child_id,$texonomy);

					$catimage = service_finder_getCategoryImage($term_child_id,'service_finder-category-home');

					?>

                    <?php if($k % 2 == 0){ ?>

					<div class="item sf-caty-item-col">

                    <?php } ?>

                    

                        <div class="sf-catyitem-box">

                            <a href="<?php echo esc_url(get_term_link( $term_child )); ?>" class="sf-caty-link">
                            <div class="sf-catyitem-pic" style="background-image: url(<?php echo esc_url($catimage); ?>)">

                                <span class="sf-caty-num"></span>

                            </div>
                            </a>

                            <h5 class="sf-catyitem-title"><a href="<?php echo esc_url(get_term_link( $term_child )); ?>" class="sf-caty-link"><?php echo esc_html($term_child->name)?></a></h5>

                        </div>

                    <?php if($m % 2 == 0 || $m == $totalchildcategories){ ?>

                    </div>

                    <?php } ?>

					<?php 
					$k++;
					$m++;
					} 
					?>

                </div>

				<?php } ?>

            </div>                       

            

        </div>

    </div>

	<?php

	}else{
	?>
	<div class="section-content sf-allCaty-grid-wrap">

        <div class="container">

            <div class="section-content">

               	<div class="sf-catresults-found" id="catresultfound"><?php esc_html_e('No results found.', 'service-finder'); ?></div>

            </div>                       

        </div>

    </div>
	<?php
	}

	?>

    

    <?php require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/category/templates/providers-category-2.php';?>

        

</div>

<?php

get_footer();

