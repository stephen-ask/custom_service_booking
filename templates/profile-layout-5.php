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

wp_enqueue_style( 'lc_lightbox' );
wp_enqueue_script( 'lc_lightbox' );

$service_finder_options = get_option('service_finder_options');

$service_finder_Params = service_finder_plugin_global_vars('service_finder_Params');

$paymentsystem = service_finder_plugin_global_vars('paymentsystem');



$moreqa = (!empty($_GET['moreqa'])) ? esc_attr($_GET['moreqa']) : '';

$morearticles = (!empty($_GET['morearticles'])) ? esc_attr($_GET['morearticles']) : '';



if($moreqa == true){

require SERVICE_FINDER_BOOKING_TEMPLATES_DIR . '/full-qa.php';

return;

}



if($morearticles == true){

require SERVICE_FINDER_BOOKING_TEMPLATES_DIR . '/full-articles.php';

return;

}



$jobid = (!empty($_GET['jobid'])) ? $_GET['jobid']  : '';

$quoteid = (!empty($_GET['quoteid'])) ? $_GET['quoteid']  : '';



$jobpost = get_post($jobid);

if(!empty($jobpost)){

$jobauthor = $jobpost->post_author;

}else{

$jobauthor = '';

}



$quoteauthor = '';

$quoteprice = 0;



if($quoteid != ""){

$quoteauthor = service_finder_get_quote_author($quoteid);

$quoteprice = service_finder_get_quote_price($quoteid,$author);

}



$paid_booking = (!empty($service_finder_options['paid-booking'])) ? $service_finder_options['paid-booking'] : '';

$inviteforjob = (!empty($service_finder_options['invite-for-job'])) ? $service_finder_options['invite-for-job'] : '';

$identitycheck = (isset($service_finder_options['identity-check'])) ? esc_attr($service_finder_options['identity-check']) : '';

$restrictuserarea = (isset($service_finder_options['restrict-user-area'])) ? esc_attr($service_finder_options['restrict-user-area']) : '';

$requestquote = (!empty($service_finder_options['requestquote-replace-string'])) ? esc_attr($service_finder_options['requestquote-replace-string']) : esc_html__( 'Request a Quote', 'service-finder' );



$curveleftcolor = (!empty($service_finder_options['profile-left-curve-color'])) ? $service_finder_options['profile-left-curve-color'] : '';

$curverightcolor = (!empty($service_finder_options['profile-right-curve-color'])) ? $service_finder_options['profile-right-curve-color'] : '';



$settings = service_finder_getProviderSettings($author);



$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? esc_html($service_finder_options['pay_booking_amount_to']) : '';

$twocheckouttype = (!empty($service_finder_options['twocheckout-type'])) ? esc_html($service_finder_options['twocheckout-type']) : '';

if($twocheckouttype == 'live'){

	$twocheckoutmode = 'production';

}else{

	$twocheckoutmode = 'sandbox';

}

if($pay_booking_amount_to == 'admin'){

	if($twocheckouttype == 'live'){

		$twocheckoutpublishkey = (!empty($service_finder_options['twocheckout-live-publish-key'])) ? esc_html($service_finder_options['twocheckout-live-publish-key']) : '';

		$twocheckoutaccountid = (!empty($service_finder_options['twocheckout-live-account-id'])) ? esc_html($service_finder_options['twocheckout-live-account-id']) : '';

	}else{

		$twocheckoutpublishkey = (!empty($service_finder_options['twocheckout-test-publish-key'])) ? esc_html($service_finder_options['twocheckout-test-publish-key']) : '';

		$twocheckoutaccountid = (!empty($service_finder_options['twocheckout-test-account-id'])) ? esc_html($service_finder_options['twocheckout-test-account-id']) : '';

	}

}elseif($pay_booking_amount_to == 'provider'){

	$twocheckoutpublishkey = esc_html($settings['twocheckoutpublishkey']);

	$twocheckoutaccountid = esc_html($settings['twocheckoutaccountid']);

}



wp_add_inline_script( 'service_finder-js-invoice-paid', '/*Declare global variable*/

var twocheckoutaccountid = "'.$twocheckoutaccountid.'";

var twocheckoutpublishkey = "'.$twocheckoutpublishkey.'";

var twocheckouttype = "'.$twocheckouttype.'";

var twocheckoutmode = "'.$twocheckoutmode.'";', 'after' );







/*Author page for provider profile*/

$imagepath = null;

$coverbanner = null;

$getProvider = new SERVICE_FINDER_searchProviders();

$providerInfo = $getProvider->service_finder_getProviderInfo(esc_attr($author));



$userCap = service_finder_get_capability($author);

wp_add_inline_script( 'bootstrap', 'jQuery(document).ready(function($) {

/*Load cities on country onchange event*/

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

$totalvideos = 0;
if($providerInfo->embeded_code != '')
{
	if(is_serialized($providerInfo->embeded_code)){
		$embeded_codes = unserialize($providerInfo->embeded_code);
		$totalvideos = count($embeded_codes);
	}
}	

if($totalvideos <= 2){
	$totalvideosoption = 'no';
}else{
	$totalvideosoption = 'yes';
}

$relatedproviders = service_finder_getRelatedProviders($author,get_user_meta($author,'primary_category',true),3);
$totalrelatedproviders = count($relatedproviders);

if($totalrelatedproviders <= 1){
	$relatedpronavoption = 'no';
}else{
	$relatedpronavoption = 'yes';
}

if(is_rtl()){  

$rtl = 'yes';

}else{

$rtl = 'no';

}

wp_add_inline_script( 'service_finder-js-profile', 'var relatedpronavoption = "'.$relatedpronavoption.'"; var totalvideosoption = "'.$totalvideosoption.'"; var rtlnavoption = "'.$rtl.'";', 'before' );

$current_user = wp_get_current_user(); 

global $wpdb,$service_finder_Tables;



if(service_finder_user_has_capability('cover-image',$author))

{

	$coverimage = service_finder_getProviderAttachments($author,'cover-image');

	if(!empty($coverimage)){

		$src  = wp_get_attachment_image_src( $coverimage[0]->attachmentid, 'service_finder-provider-thumb' );

		$coverbanner  = $src[0];

		$coverclass = 'provider-cover-img';

	}

}



if($coverbanner == ""){

	$subheader = service_finder_sub_header_pl();

	$providersubheaderbgimage = service_finder_provider_coverbanner_pl();

	if($subheader && $providersubheaderbgimage){

		$coverbanner = service_finder_provider_coverbanner_pl();

		$coverclass = '';

	}else{

		$coverbanner = '';

		$coverclass = 'provider-banner-off overlay-black-middle';

	}

}

?>

<?php
if($providerInfo->account_blocked == 'yes' || !service_finder_check_profile_after_trial_expire($author) || $providerInfo->admin_moderation == 'pending' || ($restrictuserarea && $identitycheck && $providerInfo->identity != 'approved')){
  require SERVICE_FINDER_BOOKING_FRONTEND_DIR . '/blocked-profile.php';
}else{
?>

<div class="page-content bg-white">

  <?php if(service_finder_get_data($service_finder_options,'hide-top-banner-area','no') == 'no'){ ?>
  <div class="sf-profile-banner">

    <div class="container sf-proBnr-container" id="sf-provider-info">

      <div class="sf-proBnr-row row">

        <div class="col-md-6 sf-proBnr-left text-center">

          <div class="sf-provi-pic">
          
          <?php if(service_finder_is_varified_user($author)){ ?>
            <div class="sf-pro-check">
                <span><i class="fa fa-check"></i></span>
                <strong class="sf-verified-label"><?php echo esc_html__( 'Verified', 'service-finder' ); ?></strong>
            </div>
            <?php } ?>

          <?php $profilethumb = service_finder_get_avatar_by_userid($author,'service_finder-provider-medium'); ?>

          <img src="<?php echo esc_url($profilethumb); ?>" alt=""/></div>

          <h3 class="sf-provi-name"><?php echo service_finder_getProviderFullName($author); ?></h3>

          <div class="sf-provi-tagline"><?php echo (!empty($providerInfo->tagline)) ? $providerInfo->tagline : service_finder_default_tagline(); ?></div>

          	<?php $images = service_finder_getProviderAttachments($providerInfo->wp_user_id,'gallery');  ?>
			<?php if(!empty($images)){ ?>
            <div class="sf-provi-gallery">
            <?php 
            $cnt = 0;
            foreach($images as $image){ 
            $fullsrc  = wp_get_attachment_image_src( $image->attachmentid, 'full' );	
            $fullsrc  = $fullsrc[0];
            if($cnt == 0){
            echo '<a class="elem pic-long" href="'.esc_url($fullsrc).'">'.count($images).' '.esc_html__('Photos', 'service-finder').'</a>';
            }else{
            echo '<a class="elem pic-long" href="'.esc_url($fullsrc).'"></a>';
            }
            $cnt++;
            }
            ?>
            </div>
            <?php } ?>

          <div class="sf-provi-rating">

            <?php echo service_finder_displayRating(service_finder_getAverageRating($author)); ?>

          </div>

          <div class="sf-provi-social">

            <?php if($service_finder_options['enable-social-shares']){ ?>

            <ul class="share-social-bx">

              <?php if($service_finder_options['facebook']){ ?>

              <li class="fb"><a onclick="javascript:window.open('https://www.facebook.com/sharer/sharer.php?u=<?php echo service_finder_get_author_url($author) ?>', '_blank', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" href="javascript:;"> <i class="fa fa-facebook"></i>

                <?php esc_html_e('Share', 'service-finder'); ?>

                </a></li>

              <?php } ?>

              <?php if($service_finder_options['twitter']){ ?>

              <li class="tw"><a onclick="javascript:window.open('https://twitter.com/intent/tweet?text=<?php the_author_meta( 'first_name', $author ); ?> <?php the_author_meta( 'last_name', $author ); ?>&url=<?php echo service_finder_get_author_url($author) ?>', '_blank', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" href="javascript:;"> <i class="fa fa-twitter"></i>

                <?php esc_html_e('Share', 'service-finder'); ?>

                </a></li>

              <?php } ?>

              <?php if($service_finder_options['linkedin']){ ?>

              <li class="lin"><a onclick="javascript:window.open('http://www.linkedin.com/shareArticle?mini=true&url=<?php echo service_finder_get_author_url($author) ?>', '_blank', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" href="javascript:;"> <i class="fa fa-linkedin"></i>

                <?php esc_html_e('Share', 'service-finder'); ?>

                </a></li>

              <?php } ?>

              <?php if($service_finder_options['pinterest']){ ?>

              <li class="pin"><a href="javascript:void((function()%7Bvar%20e=document.createElement('script');e.setAttribute('type','text/javascript');e.setAttribute('charset','<?php echo get_bloginfo( 'charset' ) ?>');e.setAttribute('src','http://assets.pinterest.com/js/pinmarklet.js?r='+Math.random()*99999999);document.body.appendChild(e)%7D)());"> <i class="fa fa-pinterest"></i>

                <?php esc_html_e('Share', 'service-finder'); ?>

                </a></li>

              <?php } ?>

              <?php if($service_finder_options['digg']){ ?>

              <li class="dig"><a onclick="javascript:window.open('http://www.digg.com/submit?url=<?php echo service_finder_get_author_url($author) ?>', '_blank', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" href="javascript:;"> <i class="fa fa-digg"></i>

                <?php esc_html_e('Share', 'service-finder'); ?>

                </a></li>

              <?php } ?>

            </ul>

            <?php } ?> 

          </div>

          <div class="sf-provi-btn"> 

          <?php

			if(class_exists('WP_Job_Manager') && $inviteforjob){

				if(is_user_logged_in()){

					if(service_finder_getUserRole($current_user->ID) == 'Customer'){

					echo '<a href="javascript:;" class="btn btn-primary" data-action="invite" data-redirect="no" data-toggle="modal" data-target="#invite-job"><i class="fa fa-briefcase"></i> '.esc_html__('Invite for Job', 'service-finder').'</a>';

					}

				}else{

					echo '<a href="javascript:;" class="btn btn-primary" data-action="login" data-redirect="no" data-toggle="modal" data-target="#login-Modal"><i class="fa fa-briefcase"></i> '.esc_html__('Invite for Job', 'service-finder').'</a>';

				}

			}

			?>

            

            <?php if(service_finder_get_data($service_finder_options,'add-to-fav')){ ?>

			<?php

            if(is_user_logged_in()){

                $myfav = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->favorites.' where user_id = %d AND provider_id = %d',$current_user->ID,$author));

                if(!empty($myfav)){

                echo '<a href="javascript:;" id="favproid-'.esc_attr($author).'" class="btn btn-primary removefromfavorite4" data-proid="'.esc_attr($author).'" data-userid="'.esc_attr($current_user->ID).'"> <i class="fa fa-heart"></i> '.esc_html__('My Favorite', 'service-finder').'</a>';

                }else{

                echo '<a href="javascript:;" id="favproid-'.esc_attr($author).'" class="btn btn-primary addtofavorite4" data-proid="'.esc_attr($author).'" data-userid="'.esc_attr($current_user->ID).'"><i class="fa fa-heart-o"></i> '.esc_html__('Add to Favorites', 'service-finder').'</a>';

                }

            }else{

                echo '<span id="favproid-'.esc_attr($author).'" class="btn btn-primary" data-action="login" data-redirect="no" data-toggle="modal" data-target="#login-Modal"><i class="fa fa-heart"></i> '.esc_html__('Add to Favorites', 'service-finder').'</a>';

            }

            ?>

            <?php } ?>
            
            <?php 
			if(class_exists('aone_messaging')){
				if(is_user_logged_in()){
				$args = array(
					'view' => 'popup',
					'type' => '',
					'targetid' => 0,
					'fromid' => $current_user->ID,
					'toid' => $author,
				);
				do_action( 'aone_messaging_send_message', $args );
				}
			}
			?>

          

        </div>
        </div>

        <div class="col-md-6 sf-proBnr-right">

          <div class="sf-proBnr-pic <?php echo esc_attr($coverclass); ?>" style="background-image:url(<?php echo esc_url($coverbanner); ?>);"></div>

        </div>

      </div>

    </div>

  </div>
  <?php } ?>

  <section class="sf-page-scroll-wrap sf-page-scroll-wrap2">

    <div class="container">

      <div class="sf-page-scroll-nav clearfix">

        <ul class="clearfix">

            <li><a href="#sf-provider-info"><?php echo service_finder_get_data($service_finder_options,'label-about-info',esc_html__('About', 'service-finder')); ?></a></li>

            <?php if((service_finder_get_data($service_finder_options,'show-contact-map') && service_finder_show_map_on_site()) || service_finder_get_data($service_finder_options,'show-contact-info') || service_finder_check_business_hours_status($author)){ ?>

            <li><a href="#sf-provider-address"><?php echo service_finder_get_data($service_finder_options,'label-address-info',esc_html__('Contact', 'service-finder')); ?></a></li>

            <?php } ?>

            <?php if($providerInfo->embeded_code != ''){ ?>

            <li><a href="#sf-provider-video"><?php echo service_finder_get_data($service_finder_options,'label-video',esc_html__('Videos', 'service-finder')); ?></a></li>

            <?php } ?>

            <?php

            $services = service_finder_getAllServices($author);

            if(!empty($services) && service_finder_get_data($service_finder_options,'my-services-menu')){

            ?>

            <li><a href="#sf-provider-services"><?php echo service_finder_get_data($service_finder_options,'label-services',esc_html__('Services', 'service-finder')); ?></a></li>

            <?php } ?>

            <?php

            if(service_finder_get_data($service_finder_options,'review-system')){

            ?>

            <li><a href="#sf-provider-review"><?php echo service_finder_get_data($service_finder_options,'label-review',esc_html__('Review', 'service-finder')); ?></a></li>

            <?php } ?>

        </ul>

      </div>

    </div>

  </section>

  <!-- Left & right section start -->

  <div class="container">

    <div class="row">

      <!-- Side bar start -->

      <div class="col-md-4">

        <?php 

		if ( is_active_sidebar( 'sf-provider-profile' ) ) {

			dynamic_sidebar('sf-provider-profile');

		}else{

		?>

        <!-- Contact Provider form start-->

        <?php if($service_finder_options['request-quote'] && service_finder_request_quote_for_loggedin_user()){ ?>

        <?php

        $providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? esc_html__('Contact ', 'service-finder').$service_finder_options['provider-replace-string'] : esc_html__('Contact Provider', 'service-finder');	

		?>

        <aside  class="sf-sidebar-left">

          <h3 class="sf-sidebar-title"><?php echo esc_html($requestquote); ?></h3>

          <div class="padding-30  margin-b-30  bg-white sf-shadow-box sf-border-box sf-radius-10">

            <div class="form-quot-bx">

              <?php require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/get-quote/templates/get-quote-layout-3.php'; ?>

            </div>

          </div>

          <?php 

		  $providers = service_finder_getRelatedProviders($providerInfo->wp_user_id,get_user_meta($providerInfo->wp_user_id,'primary_category',true),3);

		  if(!empty($providers)){ ?>

          <h3 class="sf-sidebar-title"><?php echo (!empty($service_finder_options['label-related-provider'])) ? esc_html($service_finder_options['label-related-provider']) : esc_html__('Related', 'service-finder').' '.service_finder_provider_replace_string(); ?></h3>

          <div class="owl-carousel sf-ow-provider-sidebar sf-owl-arrow">

            <?php 

							if(!empty($providers)){

								foreach($providers as $provider){

								$bookingurl = service_finder_get_author_url($provider->wp_user_id);

								$src = service_finder_get_avatar_by_userid($provider->wp_user_id,'service_finder-related-provider');

								$providerid = $provider->wp_user_id;
								$profileurl = service_finder_get_author_url($providerid);

								

								if($src != ''){

									$imgtag = '<img src="'.esc_url($src).'" width="150" height="150" alt="">';

								}else{

									$imgtag = '<img src="'.esc_url($service_finder_Params['pluginImgUrl'].'/no_img.jpg').'" width="150" height="150" alt="">';

								}

								?>

								<div class="item">

                                  <div class="sf-ow-provider-wrap">

                                    <div class="sf-ow-provider">

                        

                                        <div class="sf-ow-top">

                                            <?php if(service_finder_is_varified_user($providerid)){ ?>

                                            <div class="sf-pro-check">
                        <span><i class="fa fa-check"></i></span>
                        <strong class="sf-verified-label"><?php echo esc_html__( 'Verified', 'service-finder' ); ?></strong>
                    </div>

                                            <?php } ?>

                                            

                                            <?php if(service_finder_get_data($service_finder_options,'add-to-fav')){

                                            if($service_finder_options['add-to-fav']){

                                                if(is_user_logged_in()){

                                                    $myfav = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->favorites.' where user_id = %d AND provider_id = %d',$current_user->ID,$providerid));

                                                    if(!empty($myfav)){

                                                    echo '<div class="sf-pro-favorite"><a id="favproid-'.esc_attr($providerid).'" data-proid="'.esc_attr($providerid).'" data-userid="'.esc_attr($current_user->ID).'" href="javascript:;" class="removefromfavoriteshort"><i class="fa fa-heart"></i></a></div>';

                                                    }else{

                                                    echo '<div class="sf-pro-favorite"><a id="favproid-'.esc_attr($providerid).'" data-proid="'.esc_attr($providerid).'" data-userid="'.esc_attr($current_user->ID).'" href="javascript:;" class="addtofavoriteshort"><i class="fa fa-heart-o"></i></a></div>';

                                                    }

                                                }else{

                                                    echo '<div class="sf-pro-favorite"><a href="javascript:;" data-action="login" data-redirect="no" data-toggle="modal" data-target="#login-Modal"><i class="fa fa-heart-o"></i></a></div>';

                                                }

                                            }

                                            } ?>

                                            

                                            <div class="sf-ow-info">

                                                <h4 class="sf-title"><a href="<?php echo esc_url($profileurl); ?>"><?php echo service_finder_getProviderFullName($providerid); ?></a></h4>

                                                <?php if(service_finder_get_data($service_finder_options,'show-address-info') && service_finder_check_address_info_access()){ ?>	

                                                <?php if(service_finder_getAddress($providerid) != "" && service_finder_get_data($service_finder_options,'show-postal-address')){ ?>

                                                <span><?php echo service_finder_getshortAddress($providerid); ?></span>

                                                <?php } ?>

                                                <?php } ?>

                                            </div>

                                        </div>

                                        <div class="sf-ow-mid">

                                            <div class="sf-ow-media">

                                                <a href="<?php echo esc_url($profileurl); ?>"><img src="<?php echo esc_url($src); ?>"></a>

                                            </div>

                                            <p><?php echo service_finder_getExcerpts(nl2br(stripcslashes($providerInfo->bio)),0,80); ?></p>

                                            <?php echo service_finder_displayRating(service_finder_getAverageRating($providerid)); ?>

                                        </div>

                                    </div>

                                    <?php

                                    $requestquote = service_finder_get_data($service_finder_options,'requestquote-replace-string',esc_html__('Request A Quote','service-finder'));

                            

                                    if(service_finder_get_data($service_finder_options,'request-quote') && service_finder_request_quote_for_loggedin_user()){

                                    ?>

                                    <div class="sf-ow-bottom">

                                        <a href="javascript:;" data-providerid="<?php echo esc_attr($providerid); ?>" data-tool="tooltip" data-toggle="modal" data-target="#quotes-Modal"><?php echo esc_html($requestquote); ?></a>

                                    </div>

                                    <?php

                                    }

                                    ?>

                                    

                                </div>

                                </div>

								<?php

								}

							}else{

								echo '<li>';

								echo sprintf( esc_html__('No Related %s Available', 'service-finder'), $providerreplacestring );

								echo '</li>';

							}

							?>

                      </div>

	      <?php } ?>                      

		  </aside>

        <?php } ?>

        <?php } ?>

      </div>

      <!-- Side bar END -->

      <!-- Left part start -->

      <div class="col-md-8">

        <div class="sf-provi-bio-box margin-b-50">

          <h3 class="sf-provi-title">

          <?php

          echo service_finder_getCompanyName($providerInfo->wp_user_id);

		  ?>

          </h3>

          <div class="sf-divider-line"></div>

          <div class="sf-provi-cat"><strong><?php esc_html_e('Categories', 'service-finder'); ?>:</strong> 

		  						<?php

								$primarycatid = get_user_meta($providerInfo->wp_user_id,'primary_category',true);

								$categories = $providerInfo->category_id;

								if($categories != '')

								{

								$cats = explode(',',$categories);

								$displaycat = array();

								if(!empty($cats)){

									foreach($cats as $catid){

										if($primarycatid == $catid){

										$displaycat[] = '<a class="sf-pre-cat" href="'.esc_url(service_finder_getCategoryLink($catid)).'">'.service_finder_getCategoryName($catid).'</a>';	

										}else{

										$displaycat[] = '<a href="'.esc_url(service_finder_getCategoryLink($catid)).'">'.service_finder_getCategoryName($catid).'</a>';	

										}

										

									}

								} 

								echo implode(', ',$displaycat);		

								}

								?></div>

          <div class="sf-provi-bio-text">

            <?php 

			if($providerInfo->bio != ""){

				echo apply_filters('the_content', $providerInfo->bio);

			}

			?>

          </div>

          <?php
          if($providerInfo->facebook != "" || $providerInfo->twitter != "" || $providerInfo->linkedin != "" || $providerInfo->digg != "" || $providerInfo->pinterest != "" || $providerInfo->instagram != ""){
		  ?>
          <div class="social-share-icon social-share-icon2">

            <div class="social-share-cell"> <strong><?php esc_html_e('Explore Us On Social Media', 'service-finder'); ?></strong> </div>

            <div class="social-share-cell">

              <ul class="share-buttons">

				  <?php if($providerInfo->facebook != ""){ ?>

                  <li><a class="fb-share" href="<?php echo esc_url($providerInfo->facebook); ?>" target="_blank" rel="nofollow"><i class="fa fa-facebook"></i></a></li>

                  <?php } ?>

                  <?php if($providerInfo->twitter != ""){ ?>

                  <li><a class="twitter-share" href="<?php echo esc_url($providerInfo->twitter); ?>" target="_blank" rel="nofollow"><i class="fa fa-twitter"></i></a></li>

                  <?php } ?>

                  <?php if($providerInfo->linkedin != ""){ ?>

                  <li><a class="linkedin-share" href="<?php echo esc_url($providerInfo->linkedin); ?>" target="_blank" rel="nofollow"><i class="fa fa-linkedin"></i></a></li>

                  <?php } ?>

                  <?php if($providerInfo->digg != ""){ ?>

                  <li><a class="digg-share" href="<?php echo esc_url($providerInfo->digg); ?>" target="_blank" rel="nofollow"><i class="fa fa-digg"></i></a></li>

                  <?php } ?>

                  <?php if($providerInfo->pinterest != ""){ ?>

                  <li><a class="pinterest-share" href="<?php echo esc_url($providerInfo->pinterest); ?>" target="_blank" rel="nofollow"><i class="fa fa-pinterest"></i></a></li>

                  <?php } ?>

                  <?php if($providerInfo->instagram != ""){ ?>

                  <li><a class="instagram-share" href="<?php echo esc_url($providerInfo->instagram); ?>" target="_blank" rel="nofollow"><i class="fa fa-instagram"></i></a></li>

                  <?php } ?>

                </ul>

            </div>

          </div>
          <?php } ?>

        </div>

        <?php if(!empty($services) && service_finder_get_data($service_finder_options,'my-services-menu')){ ?>
        <div class="sf-provi-service-box margin-b-50 sf-bookngservice-fours" id="sf-provider-services">

          <h3 class="sf-provi-title"><?php esc_html_e('Service', 'service-finder'); ?></h3>

          <div class="sf-divider-line"></div>

          <?php require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/book-now/templates/book-now-v4.php'; ?>

        </div>
        <?php } ?>

        <?php if((service_finder_get_data($service_finder_options,'show-contact-map') && service_finder_show_map_on_site()) || service_finder_get_data($service_finder_options,'show-contact-info') || service_finder_check_business_hours_status($author)){ ?> 

        <div class="sf-provi-coInfo-box margin-b-50" id="sf-provider-address">

          <h3 class="sf-provi-title"><?php esc_html_e('Contact Information', 'service-finder'); ?></h3>

          <div class="sf-divider-line"></div>

          <div class="row">

            <?php if(service_finder_get_data($service_finder_options,'show-contact-info')){ ?>

			<?php if(service_finder_get_data($service_finder_options,'show-contact-map') && service_finder_show_map_on_site()){ ?>

            <div class="col-md-6">

              <div class="sf-provi-coInfo-map"> 

              	<div id='gmap_canvas map-canvas-in'>

									<?php

									$userLink = service_finder_get_author_url($providerInfo->wp_user_id);

									if(!empty($providerInfo->avatar_id) && $providerInfo->avatar_id > 0){

										$src  = wp_get_attachment_image_src( $providerInfo->avatar_id, 'service_finder-provider-thumb' );

										$src  = $src[0];

									}else{

										$src  = '';

									}

									$icon = service_finder_getCategoryIcon(get_user_meta($providerInfo->wp_user_id,'primary_category',true));

									if($icon == ""){

									$icon = (!empty($service_finder_options['default-map-marker-icon']['url'])) ? $service_finder_options['default-map-marker-icon']['url'] : '';

									}

									

									$markeraddress = service_finder_getAddress($providerInfo->wp_user_id);

									

									$zoom_level = get_user_meta($providerInfo->wp_user_id,'zoomlevel',true);

									

									if($zoom_level == ""){

									$zoom_level = (!empty($service_finder_options['zoom-level'])) ? $service_finder_options['zoom-level'] : 14;

									}

									

									$companyname = service_finder_getCompanyName($providerInfo->wp_user_id);

									

									$radius = get_user_meta($providerInfo->wp_user_id,'serviceradius',true);

									$radius = floatval($radius) * 1000;

									

									$marker = '[\"'.$providerInfo->full_name.'\",\"'.$providerInfo->lat.'\",\"'.$providerInfo->long.'\",\"'.$src.'\",\"'.$icon.'\",\"'.$userLink.'\",\"'.$providerInfo->wp_user_id.'\",\"'.service_finder_getCategoryName(get_user_meta($providerInfo->wp_user_id,'primary_category',true)).'\",\"'.$markeraddress.'\",\"'.stripcslashes($companyname).'\",\"\",\"'.stripcslashes($radius).'\"]';

									

									wp_add_inline_script( 'service_finder-js-gmapfunctions', 'var googlecode_regular_vars = {"general_latitude":"'.esc_js($providerInfo->lat).'", "general_longitude":"'.esc_js($providerInfo->long).'","path":"'.esc_js($imagepath).'","markers":"['.$marker.']","idx_status":"0","page_custom_zoom":"'.esc_js($zoom_level).'","generated_pins":"0"}; jQuery(document).ready(function($) {

												initializeSearchMap();

												});', 'before' );

                                    

                                    echo do_shortcode('[service_finder_map general_latitude="'.$providerInfo->lat.'" general_longitude="'.$providerInfo->long.'" height="296px" width="709px;"]'); ?> 

                                    </div>

				<div class="sf-location-gallery">

                                      <ul class="sf-location-listing equal-col-outer">

                                      <?php

                                      $results = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->branches.' WHERE wp_user_id = %d ORDER BY ID DESC LIMIT 0,2',$providerInfo->wp_user_id));

                                      if(!empty($results)){

                                        foreach($results as $res){

                                        ?>

                                            <li class="equal-col">

                                               <a href="javascript:;" class="load-branch-address" data-branchid="<?php echo esc_attr($res->id); ?>" data-userid="<?php echo esc_attr($providerInfo->wp_user_id); ?>">

                                               <?php

                                               echo service_finder_getBranches($res->id);

                                               ?>

                                               </a>

                                            </li>

                                        <?php

                                        }

                                      }

                                      ?>

                                      <div id="morebranches" class="showalllocation default-hidden">

                                      </div>

                                      </ul>

                                      <?php

                                      $results = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->branches.' WHERE wp_user_id = %d',$providerInfo->wp_user_id));

                                      if(count($results) > 2){

                                      ?>

                                      <div class="sf-location-btn"><a class="btn btn-primary showmorelocation" data-userid="<?php echo esc_attr($providerInfo->wp_user_id); ?>" href="javascript:;"><?php esc_html_e('Show More Location', 'service-finder'); ?></a></div>

                                      <?php } ?>

                                      </div>

              </div>

            </div>

            <?php } ?>

            <?php } ?>

            

            <?php if(service_finder_check_business_hours_status($author)){ ?>

            <div class="col-md-6">

              <div class="sf-provi-coInfo-hour sf-list-business-hours">

                <ul class="list-unstyled sf-bh-wrapper">

                    <?php

                    $weekdays = service_finder_get_weekdays();

                    $time_format = service_finder_get_data($service_finder_options,'time-format');

                    $timeslots = get_user_meta($author,'timeslots',true);

                    $breaktimes = get_user_meta($author,'breaktime',true);

                    $i = 0;

                    foreach($weekdays as $weekday)

                    {

                    $timeslot = (!empty($timeslots)) ? $timeslots[$i] : '';	

                    $item = explode('-',$timeslot);

                    

                    if($item[0] != ""){

                        if($timeslot == 'off'){

                            echo '<li><span>'.service_finder_day_translate($weekday).'<b>:</b></span><span>'.esc_html__('Closed','service-finder').'</span></li>';

                        }else{

                            

                            if($time_format){

                                $starttime = date('H:i',strtotime(esc_html($item[0])));

                                $endtime = date('H:i',strtotime(esc_html($item[1])));

                            }else{

                                $starttime = date('h:i a',strtotime(esc_html($item[0])));

                                $endtime = date('h:i a',strtotime(esc_html($item[1])));

                            }

                            

                            $breakhtml = '';

                

                            if(!empty($breaktimes[$i])){

                            $breaktime = $breaktimes[$i];	

                            

                            if(!empty($breaktime)){

                                $breakhtml .= '<li>'.esc_html__('Break Time','service-finder').'</li>';

                                foreach($breaktime as $bktime){

                                    $bkitem = explode('-',$bktime);	

                                    

                                    if($time_format){

                                        $bhstarttime = date('H:i',strtotime(esc_html($bkitem[0])));

                                        $bhendtime = date('H:i',strtotime(esc_html($bkitem[1])));

                                    }else{

                                        $bhstarttime = date('h:i a',strtotime(esc_html($bkitem[0])));

                                        $bhendtime = date('h:i a',strtotime(esc_html($bkitem[1])));

                                    }

                                    

                                    $breakhtml .= '<li>'.$bhstarttime.' <b>'.esc_html__('to','service-finder').'</b> '.$bhendtime.'</li>';

                                }

                            }else{

                                $breakhtml .= '<li>-</li>';

                            }

                            }

                            

                            

                            echo '<li><span>'.service_finder_day_translate($weekday).'<b>:</b></span><span>'.$starttime.' '.esc_html__('to','service-finder').' '.$endtime.'<ul class="sf-bh-breaktime">'.$breakhtml.'</ul></span></li>';

                        }

                    }

                    $i++;

                    }

                    ?>

                </ul>

              </div>

            </div>

            <?php } ?>

          </div>

          <div class="row">

            <?php if(service_finder_get_data($service_finder_options,'show-address-info') && service_finder_check_address_info_access()){ ?>	

			<?php if(service_finder_getAddress($author) != "" && service_finder_get_data($service_finder_options,'show-postal-address')){ ?>

            <div class="col-md-6">

              <div class="sf-provi-coInfo-box">

                <h6><?php esc_html_e('Address', 'service-finder'); ?></h6>

                <div class="sf-provi-coInfo-text"><?php echo service_finder_getAddress($author); ?></div>

              </div>

            </div>

            <?php } ?>

			<?php } ?>

            <?php if(service_finder_get_contact_info_with_text($providerInfo->phone,$providerInfo->mobile) != "" && service_finder_contact_number_is_accessible($author)){ ?>

            <div class="col-md-6">

              <div class="sf-provi-coInfo-box">

                <h6><?php esc_html_e('Telephone', 'service-finder'); ?></h6>

                <?php echo service_finder_get_contact_info_with_text($providerInfo->phone,$providerInfo->mobile); ?>

              </div>

            </div>

            <?php } ?>

            <?php if(service_finder_get_data($service_finder_options,'show-email-address')){ ?>

            <div class="col-md-6">

              <div class="sf-provi-coInfo-box">

                <h6><?php esc_html_e('Email', 'service-finder'); ?></h6>

                <div class="sf-provi-coInfo-text"><a href="mailto:<?php the_author_meta( 'user_email', $author ); ?>"><?php the_author_meta( 'user_email', $author ); ?></a></div>

              </div>

            </div>

            <?php } ?>

            <?php if($providerInfo->website != "" && service_finder_get_data($service_finder_options,'show-website')){ ?>

            <div class="col-md-6">

              <div class="sf-provi-coInfo-box">

                <h6><?php echo esc_html__( 'Web', 'service-finder' ) ?></h6>

                <div class="sf-provi-coInfo-text"><a href="<?php echo service_finder_addhttp(esc_html($providerInfo->website)); ?>" target="_blank"><?php echo esc_html($providerInfo->website); ?></a></div>

              </div>

            </div>

            <?php } ?>

            <?php if($providerInfo->skypeid != "" && service_finder_get_data($service_finder_options,'show-skype')){ ?>

            <div class="col-md-6">

              <div class="sf-provi-coInfo-box">

                <h6><?php echo esc_html__( 'Skype', 'service-finder' ) ?></h6>

                <div class="sf-provi-coInfo-text"><a href="skype:<?php echo esc_html($providerInfo->skypeid); ?>?chat"><?php echo esc_html($providerInfo->skypeid); ?></a></div>

              </div>

            </div>

            <?php } ?>

          </div>

        </div>

        <?php } ?>

        <?php

		$languages = service_finder_get_languages($author);

		$experiences = service_finder_get_experience($author);            

		$certificates = service_finder_get_certificates($author);

		if(!empty($languages) || !empty($experiences) || !empty($certificates))

		{

		?>

        <div class="sf-provi-laexce-box margin-b-50">

          <div class="sf-custom-tabs sf-custom-new">

            <ul class="nav nav-tabs nav-table-cell font-20">

              <?php if(!empty($languages)){?>

              <li class="active"><a data-toggle="tab" href="#tab-111"><?php echo esc_html__( 'Language', 'service-finder' ) ?> </a></li>

              <?php } ?>

              <?php if(!empty($experiences)){?>

              <li class="<?php echo (empty($languages)) ? 'active' : ''; ?>"><a data-toggle="tab" href="#tab-222"><?php echo esc_html__( 'Experience', 'service-finder' ) ?> </a></li>

              <?php } ?>

              <?php if(!empty($certificates)){?>

              <li class="<?php echo (empty($languages) && empty($experiences)) ? 'active' : ''; ?>"><a data-toggle="tab" href="#tab-333"><?php echo esc_html__( 'Certificates & Awards', 'service-finder' ) ?> </a></li>

              <?php } ?>

            </ul>

            <div class="tab-content">

              <?php

			  if(!empty($languages))

			  {

			  $languagearray = service_finder_get_alllanguages();

		      ?>

              <div id="tab-111" class="tab-pane active">

                <div class="sf-languages-tab">

                  <ul class="sf-languages-list sf-languages-list-new clearfix">

                    <?php

					foreach($languages as $language){

						$flagimgsrc = SERVICE_FINDER_BOOKING_IMAGE_URL.'/flags/'.$language.'.png';

						echo '<li><span><img src="'.$flagimgsrc.'" alt=""></span> '.esc_html($languagearray[$language]).'</li>';

					}

					?>

                  </ul>

                </div>

              </div>

              <?php	

		      }

			  ?>

              <?php

			  if(!empty($experiences))

			  {

			  ?>

              <div id="tab-222" class="tab-pane <?php echo (empty($languages)) ? 'active' : ''; ?>">

                <div class="sf-document-tab">

                  <div class="sf-experience-acord" id="experience-acord">

                    <?php

					$i = 1;

					foreach($experiences as $experience)

					{

					?>

					<div class="panel sf-panel">

						<div class="acod-head acc-actives">

							 <h6 class="acod-title text-uppercase">

								<a data-toggle="collapse" href="#experience<?php echo $experience->id; ?>" data-parent="#experience-acord" >

									<span class="exper-author"><?php echo esc_html($experience->job_title); ?></span>

									<span class="exper-slogan"><?php echo esc_html($experience->company_name); ?></span>

									<?php if($experience->current_job == 'yes'){ ?>

									<span class="exper-date"><i class="fa fa-clock-o"></i> <?php echo date('M d Y',strtotime($experience->start_date)); ?></span>

									<?php }else{ ?>

									<span class="exper-date"><i class="fa fa-clock-o"></i> <?php echo date('M d Y',strtotime($experience->start_date)).' -  '.date('M d Y',strtotime($experience->end_date)); ?></span>

									<?php } ?>

									

								</a>

							 </h6>

						</div>

						<div id="experience<?php echo $experience->id; ?>" class="acod-body collapse <?php echo ($i == 1) ? 'in' : ''; ?>">

							<div class="acod-content p-tb15"><?php printf($experience->description); ?></div>

						</div>

					</div>

					<?php

					$i++;

					}

					?>

                  </div>

                </div>

              </div>

              <?php	

		      }

			  ?>

              <?php

			  if(!empty($certificates))

			  {
			  $fileicon = new SERVICE_FINDER_ImageSpace();
			  foreach($certificates as $certificate){
			  if(!empty($certificate->attachment_id))
			  {
				$arr  = $fileicon->get_icon_for_attachment($certificate->attachment_id);
			
				$src = (!empty($arr['src'])) ? $arr['src'] : '';
				
				if($src == ''){
				$arr  = service_finder_get_icon_for_attachment($certificate->attachment_id);
				$src  = $arr['src'];
				}
				}else{
				$src  = '';
			  }
			  
			  $certificatetitle = (!empty($certificate->certificate_title)) ? $certificate->certificate_title : '';
			  $certificatedescription = (!empty($certificate->description)) ? $certificate->description : '';
			  $certificatedate = (!empty($certificate->issue_date)) ? $certificate->issue_date : '';
			  $certificateattachmentid = (!empty($certificate->attachment_id)) ? $certificate->attachment_id : '';

			  ?>

			  <div id="tab-333" class="tab-pane <?php echo (empty($languages) && empty($experiences)) ? 'active' : ''; ?>">

				<div class="sf-document-tab">

				  <ul class="sf-certificates-list">

					<li>

					  <?php if($src != ''){ ?>
					  <div class="awards-pic"><img src="<?php echo esc_url($src); ?>" alt=""></div>
					  <?php } ?>

					  <span class="awards-title"><?php echo esc_html($certificatetitle); ?></span> <span class="awards-date"><i class="fa fa-clock-o"></i> <?php echo date('M d Y',strtotime($certificatedate)); ?></span>
					  
					  <?php if(!empty($certificateattachmentid)){ ?>
					  <a class="sf-download-certificate" href="<?php echo SERVICE_FINDER_BOOKING_LIB_URL.'/downloads.php?file='.wp_get_attachment_url( $certificateattachmentid ) ?>"><i class="fa fa-download"></i> <?php echo esc_html__('View/Download'); ?></a>
					  <?php } ?>

					  <div class="awards-text"><?php printf($certificatedescription); ?></div>

					</li>

				  </ul>

				</div>

			  </div>

			  <?php	

			  }
			  }

			  ?>

            </div>

          </div>

        </div>

        <?php

		}

		?>

        <?php

		if($providerInfo->embeded_code != '')

		{

		if(is_serialized($providerInfo->embeded_code)){

		$embeded_codes = unserialize($providerInfo->embeded_code);

		$totalvideos = count($embeded_codes);

		if($totalvideos > 0){

		?>

        <div class="sf-provi-vido-box margin-b-50" id="sf-provider-video">

          <h3 class="sf-provi-title"><?php echo service_finder_get_data($service_finder_options,'label-our-video',esc_html__( 'Videos', 'service-finder' )); ?></h3>

          <div class="sf-divider-line"></div>

          <div class="owl-carousel sf-video-carousel sf-owl-arrow">

            <?php

			$oembed = new ClassOEmbed();

			foreach($embeded_codes as $embeded_code){

			$thumburl = service_finder_get_video_thumb_url($embeded_code,'full');

			?>

            <div class="item">

              <div class="sf-video-box">

                <div class="sf-video-pic" style="background-image:url(<?php echo esc_url($thumburl) ?>)"> </div>

                 <?php

				 $videotype = service_finder_get_video_type($embeded_code);

				 if($videotype == 'facebook'){

				 ?>

				 <a href="https://www.facebook.com/v2.5/plugins/video.php?href=<?php echo esc_url($embeded_code); ?>" class="popup-youtube sf-video-play-btn">

						<i class="fa fa-play"></i>

				 </a>

				 <?php

				 }else{

				 $ytshorturl = 'youtu.be/';

				 $ytlongurl = 'youtube.com/watch?v=';

				

				 $pos = strpos($embeded_code, $ytshorturl);

				 if ($pos !== false) {

					$embeded_code = str_replace($ytshorturl, $ytlongurl, $embeded_code);

				 }

				 ?>

				 <a class="sf-video-play-btn popup-youtube" href="<?php echo esc_url($embeded_code); ?>">

					<i class="fa fa-play"></i>

				 </a>

				 <?php

				 }

				 ?>

                 </div>

            </div>

			<?php

			}

			?>

          </div>

        </div>

        <?php

		}

		}else

		{

		?>

        <div class="sf-provi-vido-box margin-b-50" id="sf-provider-video">

          <h3 class="sf-provi-title"><?php echo service_finder_get_data($service_finder_options,'label-our-video',esc_html__( 'Videos', 'service-finder' )); ?></h3>

          <div class="sf-divider-line"></div>

          <div>

            <?php 

			$oembed = new ClassOEmbed();

			echo $oembed->getembededcode($providerInfo->embeded_code); 

			?> 

          </div>

        </div>

        <?php

		}

		}

		?>

        <?php

		$qualifications = service_finder_get_qualifications($author);

		$amenities = service_finder_get_amenities($author);

		$attachmentIDs = service_finder_getDocuments($providerInfo->wp_user_id);

		if(!empty($qualifications) || !empty($amenities) || !empty($attachmentIDs))

		{

		?>

        <div class="sf-provi-amqudo-box margin-b-50">

          <div class="sf-custom-tabs sf-custom-new">

            <ul class="nav nav-tabs nav-table-cell font-20">

              <?php if(!empty($amenities)){?>

              <li class="active"><a data-toggle="tab" href="#tab2-111"><?php echo esc_html__( 'Amenities & Features', 'service-finder' ) ?> </a></li>

              <?php } ?>

			  <?php if(!empty($qualifications)){?>

              <li class="<?php echo (empty($amenities)) ? 'active' : ''; ?>"><a data-toggle="tab" href="#tab2-222"><?php echo esc_html__( 'Qualification', 'service-finder' ) ?> </a></li>

              <?php } ?>

              <?php if(!empty($attachmentIDs)){ ?>

              <li class="<?php echo (empty($amenities) && empty($qualifications)) ? 'active' : ''; ?>"><a data-toggle="tab" href="#tab2-333"><?php echo service_finder_get_data($service_finder_options,'label-documents',esc_html__( 'Documents', 'service-finder' )); ?></a></li>

              <?php } ?>

            </ul>

            <div class="tab-content">

              <?php if(!empty($amenities)){

			  ?>

              <div id="tab2-111" class="tab-pane active">

                <div class="sf-document-tab">

                 <ul class="sf-features-list sf-features-list-new clearfix">

					<?php

                    foreach($amenities as $amenity){

                        $amenityinfo = get_term_by('id',$amenity,'sf-amenities');

                        $src = service_finder_getTermIcon($amenity);

                        

                        if($src != '')

                        {

                        echo '<li><span class="features-icon"><img src="'.esc_url($src).'" alt=""></span> '.esc_html($amenityinfo->name).'</li>';

                        }else

                        {

                        echo '<li><span class="features-icon"></span> '.esc_html($amenityinfo->name).'</li>';									

                        }

                    }

                    ?>

                </ul>

                </div>

              </div>

              <?php	} ?>

              <?php if(!empty($qualifications)){

			  ?>

              <div id="tab2-222" class="tab-pane <?php echo (empty($amenities)) ? 'active' : ''; ?>">

                <div class="sf-document-tab">

                  <div class="sf-experience-acord" id="experience-acord">

                    <?php

					$i = 1;

					foreach($qualifications as $qualification)

					{

					?>

					<div class="panel sf-panel">

						<div class="acod-head acc-actives">

							 <h6 class="acod-title text-uppercase">

								<a data-toggle="collapse" href="#qualification<?php echo esc_html($qualification->id); ?>" data-parent="#qualification-acord" >

									<span class="exper-author"> <?php echo esc_html($qualification->degree_name); ?></span>

									<span class="exper-slogan"><?php echo esc_html($qualification->institute_name); ?></span>

									<span class="exper-date"><i class="fa fa-clock-o"></i> <?php echo esc_html($qualification->from_year).' -  '.esc_html($qualification->to_year); ?></span>

								</a>

							 </h6>

						</div>

						<div id="qualification<?php echo esc_html($qualification->id); ?>" class="acod-body collapse <?php echo ($i == 1) ? 'in' : ''; ?>">

							<div class="acod-content p-tb15"><?php echo esc_html($qualification->description); ?></div>

						</div>

					</div>

					<?php

					$i++;

					}

					?>

                  </div>

                </div>

              </div>

              <?php	} ?>

              <?php if(!empty($attachmentIDs)){ ?>

              <div id="tab2-333" class="tab-pane <?php echo (empty($amenities) && empty($qualifications)) ? 'active' : ''; ?>">

                <div class="sf-documents-tab">

                  <div class="table-responsive">

                    <table class="table borderless margin-0">

                        <tbody>

                            <?php

                            if(!empty($attachmentIDs)){

                                $k=0;

                                foreach($attachmentIDs as $attachmentID){

                                if (!$k%2) echo '<tr>';

                                ?>

                                <td>

                                    <div class="panel panel-default">

                                        <div class="panel-heading">

                                            <a download="<?php echo (!empty(basename(get_attached_file($attachmentID->attachmentid)))) ? basename(get_attached_file($attachmentID->attachmentid)) : basename(wp_get_attachment_url($attachmentID->attachmentid)); ?>" href="<?php echo get_permalink( $attachmentID->attachmentid ).'?attachment_id='. $attachmentID->attachmentid.'&download_file=1'; ?>">

                                            <strong class="price-bx"><i class="fa fa-download"></i></strong>

                                            <span class="service-title"><?php echo (!empty(basename(get_attached_file($attachmentID->attachmentid)))) ? basename(get_attached_file($attachmentID->attachmentid)) : basename(wp_get_attachment_url($attachmentID->attachmentid)); ?></span>

                                            </a>

                                        </div>

                                    </div>

                                </td>

                                <?php

                                if ($k%2) echo '</tr>';

                                $k++;

                                }

                            }

                            ?>

                        </tbody>

                    </table>

                  </div>

                </div>

              </div>

              <?php } ?>

            </div>

          </div>

        </div>

        <?php } ?>

        <?php if(service_finder_article_exist($author)){ ?>

        <div class="sf-provi-articles-box margin-b-50">

          <h3 class="sf-provi-title"><?php echo service_finder_get_data($service_finder_options,'label-articles',esc_html__('Articles', 'service-finder')); ?></h3>

          <div class="sf-divider-line"></div>

          <?php require SERVICE_FINDER_BOOKING_TEMPLATES_DIR . '/articles.php'; ?>

        </div>

        <?php } ?>

        <?php if(service_finder_get_data($service_finder_options,'question-answer-section')){ ?>

        <div class="sf-provi-amqudo-box margin-b-50">

          <div class="sf-custom-tabs sf-custom-new">

            <ul class="nav nav-tabs nav-table-cell font-20">

              <li class="active"><a data-toggle="tab" href="#tab3-111"><?php echo service_finder_get_data($service_finder_options,'label-qa',esc_html__('Q & A', 'service-finder')); ?></a></li>

              <li class=""><a data-toggle="tab" href="#tab3-222"><?php echo service_finder_get_data($service_finder_options,'label-ask-question',esc_html__('Ask Question', 'service-finder')); ?></a></li>

            </ul>

            <div class="tab-content">

              <div id="tab3-111" class="tab-pane active">

                <?php require SERVICE_FINDER_BOOKING_TEMPLATES_DIR . '/qa.php'; ?>

              </div>

              <div id="tab3-222" class="tab-pane ">

                <?php require SERVICE_FINDER_BOOKING_TEMPLATES_DIR . '/ask-qa.php'; ?>

              </div>

            </div>

          </div>

        </div>

        <?php } ?>

        <?php if(service_finder_get_data($service_finder_options,'review-system')){ ?>
        <div class="sf-provi-articles-box margin-b-50 comments-area" id="sf-provider-review">

          <h3 class="sf-provi-title"><?php echo service_finder_get_data($service_finder_options,'label-review',esc_html__('Customer Review', 'service-finder')); ?></h3>

          <div class="sf-divider-line"></div>

          <?php 
		  $totalreview = service_finder_get_total_reviews($author);
		  if($totalreview > 0){
		  service_finder_review_box_style_4($author,$totalreview); 
		  }
		  ?>
          
          <?php
		  if(service_finder_get_data($service_finder_options,'review-style') == 'open-review'){
		  $postid = get_user_meta($author, 'comment_post', true);
		  if ( ! comments_open($postid) && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) {
		  ?>
		  <p class="no-comments">
			<?php esc_html_e( 'Comments are closed.', 'service-finder' ); ?>
		  </p>
		  <?php } ?>
		  <?php
		  if(is_user_logged_in()){ 
			  $postid = get_user_meta($author, 'comment_post', true);
			  $flag = 0;	
			  $providersreview = (!empty($service_finder_options['providers-review'])) ? $service_finder_options['providers-review'] : false;
			  if($providersreview){
					if(service_finder_getUserRole($current_user->ID) == 'Customer' || service_finder_getUserRole($current_user->ID) == 'Provider'){
						$flag = 1;
					}
			  }else{
					if(service_finder_getUserRole($current_user->ID) == 'Customer'){
						$flag = 1;
					}
			  }			
			  if($flag == 1){
				$usercomment = get_comments(array('user_id' => $current_user->ID, 'post_id' => $postid) );
				if($usercomment) { 
					echo '<div class="alert alert-info">'.esc_html__('You have already posted your review for this profile', 'service-finder').'.</div>';
				} else { 
					comment_form(array('title_reply' => esc_html__( 'Leave a Review', 'service-finder' )),$postid);
				} 
				
			  }else{
				echo '<div class="alert alert-warning" role="alert">';
				echo esc_html__('Please ', 'service-finder'); 
				echo '<a href="javascript:;" data-action="login" data-redirect="no" data-toggle="modal" data-target="#login-Modal">'.esc_html__('login', 'service-finder').'</a>'; 
				echo sprintf( esc_html__(' via %s account to submit review', 'service-finder'), strtolower(service_finder_customer_replace_string()) ); 
				echo '</div>';
			  }
		  }else{
			echo '<div class="alert alert-warning" role="alert">';
			echo esc_html__('Please ', 'service-finder'); 
			echo '<a href="javascript:;" data-action="login" data-redirect="no" data-toggle="modal" data-target="#login-Modal">'.esc_html__('login', 'service-finder').'</a>'; 
			echo sprintf( esc_html__(' via %s account to submit review', 'service-finder'), strtolower(service_finder_customer_replace_string()) ); 
			echo '</div>';
		  }
		  }
		  ?>
          
		  <?php
		  if(service_finder_get_data($service_finder_options,'review-style') == 'booking-review'){
		  ?>
		  <div class="row d-flex flex-wrap a-b-none">
          
          <?php
          $limit = (get_option('comments_per_page') > 0) ? get_option('comments_per_page') : 6;  
		  $authorurl = service_finder_get_author_url($author);
		  if (isset($_GET["comment_page"])) { $page  = $_GET["comment_page"]; } else { $page=1; };  
		  $start_from = ($page-1) * $limit;  
			  
		  $reviews = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->feedback.' where provider_id = %d ORDER BY id DESC LIMIT %d, %d',$author,$start_from, $limit));
		  
		  if(!empty($reviews)){
			foreach($reviews as $review){
				$customername = get_user_meta($review->customer_id,'first_name',true).' '.get_user_meta($review->customer_id,'last_name',true);
				$avatar_id = service_finder_getCustomerAvatarID($review->customer_id);
				if(!empty($avatar_id) && $avatar_id > 0){
						$src  = wp_get_attachment_image_src( $avatar_id, 'thumbnail' );
						$src  = $src[0];
				}else{
						$src = '//2.gravatar.com/avatar/2d8b3378fb00ca047026e456903cae16?s=56&d=mm&r=g';
				}		
				?>
                <div class="col-md-6">

                  <div class="sf-review-box sf-shadow-box">
    
                    <div class="sf-review-head clearfix">
    
                      <div class="sf-review-pic"><img src="<?php echo esc_url($src); ?>" alt=""/></div>
    
                      <div class="sf-review-info">
    
                        <h5 class="sf-review-name"><?php echo esc_html($customername); ?></h5>
    
                      </div>
    
                      <div class="sf-review-date"><?php echo date('M, d, Y \A\T h:i a',strtotime($review->date)); ?></div>
    
                    </div>
                    
                    <?php 
					$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM `'.$service_finder_Tables->custom_rating.'` where `feedbackid_id` = %d',$review->id));
		
					$rating = '';
					if(!empty($row)){
					
					if($row->label1 != ""){
					$k = 1;
					}
					if($row->label2 != ""){
					$k = 2;
					}
					if($row->label3 != ""){
					$k = 3;
					}
					if($row->label4 != ""){
					$k = 4;
					}
					if($row->label5 != ""){
					$k = 5;
					}
					?>
					<div class="sf-review-body">
    
                      <ul class="sf-review-rating d-flex flex-wrap">
    
                        <?php
                        for($i=1;$i<=$k;$i++){
						switch($i){
						case 1:
							$label = $row->label1;
							$ratingnumber = $row->rating1;
							break;
						case 2:
							$label = $row->label2;
							$ratingnumber = $row->rating2;
							break;
						case 3:
							$label = $row->label3;
							$ratingnumber = $row->rating3;
							break;
						case 4:
							$label = $row->label4;
							$ratingnumber = $row->rating4;
							break;
						case 5:
							$label = $row->label5;
							$ratingnumber = $row->rating5;
							break;				
						}
						$rating .= '<div class="sf-customer-rating-row clearfix">';
							
							$rating .= '<div class="sf-customer-rating-name pull-left">'.$label.'</div>';
							
							$rating .= '<div class="sf-customer-rating-count  pull-right">';
							$rating .= service_finder_displayRating($ratingnumber);
							$rating .= '</div>';
						$rating .= '</div>';
						?>
						<li>
    
                          <div class="sf-customer-rating-names"><?php echo esc_html($label); ?></div>
    
                          <div class="sf-customer-rating-star">
    
                            <?php echo service_finder_displayRating($ratingnumber); ?>
    
                          </div>
    
                        </li>
						<?php	
						}
						?>
                      </ul>
    
                    </div>
					<?php
					}else{
					?>
					<div class="sf-review-body">
    
                      <ul class="sf-review-rating d-flex flex-wrap">
    
                        <li>
    
                          <div class="sf-customer-rating-star">
    
                            <?php echo service_finder_displayRating($review->rating); ?>
    
                          </div>
    
                        </li>

                      </ul>
    
                    </div>
					<?php
					}
					?>
    
                    <div class="sf-review-footer sf-shadow-box"><span class="sf-review-write show-read-more" id="showreadmore<?php echo esc_attr($review->id); ?>" data-reviewid="<?php echo esc_attr($review->id); ?>"><?php echo $review->comment; ?></span></div>
    
                  </div>
    
                </div>
				<?php
				}
			}
		  ?>
          <?php $total_pages = ceil($totalreview / $limit); ?>
		  <?php if($total_pages > 1){ ?>
          <div align="center">
        <ul class='pagination text-center'>
        <?php if(!empty($total_pages)):for($i=1; $i<=$total_pages; $i++):  
                    if($i == $page):?>
                    <li class='active' id="<?php echo $i;?>" data-link="<?php echo $authorurl ?>"><a href='<?php echo $authorurl.'/?comment_page='.$i.'#sf-provider-review'; ?>'><?php echo $i;?></a></li> 
                    <?php else:?>
                    <li id="<?php echo $i;?>" data-link="<?php echo $authorurl ?>"><a href='<?php echo $authorurl.'/?comment_page='.$i.'#sf-provider-review'; ?>'><?php echo $i;?></a></li>
                <?php endif;?>			
        <?php endfor;endif;?>  
        </ul>
        </div>
          <?php } ?>	

          </div>
		  <?php			  
		  }else{
		  ?>
		  <div class="row d-flex flex-wrap a-b-none">
          
          <?php
          $limit = (get_option('comments_per_page') > 0) ? get_option('comments_per_page') : 6;  
		  $authorurl = service_finder_get_author_url($author);
		  if (isset($_GET["comment_page"])) { $page  = $_GET["comment_page"]; } else { $page=1; };  
		  $start_from = ($page-1) * $limit;  
			  
		  $author_post_id = get_user_meta($author,'comment_post',true);
		  $args = array(
			'number'  => $limit,
			'paged'  => $page,
			'offset'  => $start_from
		  );
		  $reviews = get_approved_comments($author_post_id,$args);
		  
		  if(!empty($reviews)){
			foreach($reviews as $review){
				$customername = get_user_meta($review->user_id,'first_name',true).' '.get_user_meta($review->user_id,'last_name',true);
				$avatar_id = service_finder_getCustomerAvatarID($review->user_id);
				if(!empty($avatar_id) && $avatar_id > 0){
						$src  = wp_get_attachment_image_src( $avatar_id, 'thumbnail' );
						$src  = $src[0];
				}else{
						$src = '//2.gravatar.com/avatar/2d8b3378fb00ca047026e456903cae16?s=56&d=mm&r=g';
				}	
				
				$reviewrating = get_comment_meta($review->comment_ID,'pixrating',true);
				$rating_title = get_comment_meta($review->comment_ID,'pixrating_title',true);	
				?>
                <div class="col-md-6">

                  <div class="sf-review-box sf-shadow-box">
    
                    <div class="sf-review-head clearfix">
    
                      <div class="sf-review-pic"><img src="<?php echo esc_url($src); ?>" alt=""/></div>
    
                      <div class="sf-review-info">
    
                        <h5 class="sf-review-name"><?php echo esc_html($customername); ?></h5>
                        
                        <div class="sf-review-feedback"><?php echo esc_html($rating_title); ?></div>
    
                      </div>
    
                      <div class="sf-review-date"><?php echo date('M, d, Y \A\T h:i a',strtotime($review->comment_date)); ?></div>
    
                    </div>
                    
                    <?php 
					$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM `'.$service_finder_Tables->custom_rating.'` where `comment_id` = %d',$review->comment_ID));
		
					$rating = '';
					if(!empty($row)){
					
					if($row->label1 != ""){
					$k = 1;
					}
					if($row->label2 != ""){
					$k = 2;
					}
					if($row->label3 != ""){
					$k = 3;
					}
					if($row->label4 != ""){
					$k = 4;
					}
					if($row->label5 != ""){
					$k = 5;
					}
					?>
					<div class="sf-review-body">
    
                      <ul class="sf-review-rating d-flex flex-wrap">
    
                        <?php
                        for($i=1;$i<=$k;$i++){
						switch($i){
						case 1:
							$label = $row->label1;
							$ratingnumber = $row->rating1;
							break;
						case 2:
							$label = $row->label2;
							$ratingnumber = $row->rating2;
							break;
						case 3:
							$label = $row->label3;
							$ratingnumber = $row->rating3;
							break;
						case 4:
							$label = $row->label4;
							$ratingnumber = $row->rating4;
							break;
						case 5:
							$label = $row->label5;
							$ratingnumber = $row->rating5;
							break;				
						}
						$rating .= '<div class="sf-customer-rating-row clearfix">';
							
							$rating .= '<div class="sf-customer-rating-name pull-left">'.$label.'</div>';
							
							$rating .= '<div class="sf-customer-rating-count  pull-right">';
							$rating .= service_finder_displayRating($ratingnumber);
							$rating .= '</div>';
						$rating .= '</div>';
						?>
						<li>
    
                          <div class="sf-customer-rating-names"><?php echo esc_html($label); ?></div>
    
                          <div class="sf-customer-rating-star">
    
                            <?php echo service_finder_displayRating($ratingnumber); ?>
    
                          </div>
    
                        </li>
						<?php	
						}
						?>
                      </ul>
    
                    </div>
					<?php
					}else{
					?>
					<div class="sf-review-body">
    
                      <ul class="sf-review-rating d-flex flex-wrap">
    
                        <li>
    
                          <div class="sf-customer-rating-star">
    
                            <?php echo service_finder_displayRating($reviewrating); ?>
    
                          </div>
    
                        </li>

                      </ul>
    
                    </div>
					<?php
					}
					?>
    
                    <div class="sf-review-footer sf-shadow-box"> <span class="sf-review-write show-read-more" id="showreadmore<?php echo esc_attr($review->comment_ID); ?>" data-reviewid="<?php echo esc_attr($review->comment_ID); ?>"><?php echo $review->comment_content; ?></span></div>
    
                  </div>
    
                </div>
				<?php
				}
			}
		  ?>
          <?php $total_pages = ceil($totalreview / $limit); ?>
		  <?php if($total_pages > 1){ ?>
          <div align="center">
        <ul class='pagination text-center'>
        <?php if(!empty($total_pages)):for($i=1; $i<=$total_pages; $i++):  
                    if($i == $page):?>
                    <li class='active' id="<?php echo $i;?>" data-link="<?php echo $authorurl ?>"><a href='<?php echo $authorurl.'/?comment_page='.$i.'#sf-provider-review'; ?>'><?php echo $i;?></a></li> 
                    <?php else:?>
                    <li id="<?php echo $i;?>" data-link="<?php echo $authorurl ?>"><a href='<?php echo $authorurl.'/?comment_page='.$i.'#sf-provider-review'; ?>'><?php echo $i;?></a></li>
                <?php endif;?>			
        <?php endfor;endif;?>  
        </ul>
        </div>
          <?php } ?>

          </div>
		  <?php	
		  }
		  ?>	
        </div>
        <?php } ?>
      </div>

      <!-- Left part END -->

    </div>

  </div>

  <!-- Left & right section  END -->

</div>

<?php } ?>


<?php

if(is_user_logged_in()){

	if(service_finder_getUserRole($current_user->ID) == 'Customer'){

	require SERVICE_FINDER_BOOKING_LIB_DIR . '/invite-job.php';

	}

}

?>

<?php

get_footer();

