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

/*Author Page Style 1 Start*/
if($service_finder_options['booking-page-style'] == 'style-1'): ?>
<!-- Content -->
<div class="page-content">
<?php 
/*Get cover Image if exist*/ 
if(!empty($userCap)){
if(in_array('cover-image',$userCap)){
	$coverimage = service_finder_getProviderAttachments($author,'cover-image');
	if(!empty($coverimage)){
		$src  = wp_get_attachment_image_src( $coverimage[0]->attachmentid, 'full' );
		$coverbanner  = $src[0];
		$coverclass = 'provider-cover-img';
	}
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
$bgcolor = (!empty($service_finder_options['inner-banner-bg-color'])) ? $service_finder_options['inner-banner-bg-color'] : '';
$bgopacity = (!empty($service_finder_options['inner-banner-opacity'])) ? $service_finder_options['inner-banner-opacity'] : '';
?>
  <!-- inner page banner -->
  <!-- Start Search Form -->
  <?php if(!$service_finder_options['profile-search-bar'] || $coverbanner != ""){ ?>
  <div class="sf-search-benner sf-overlay-wrapper">
  <div class="banner-inner-row <?php echo esc_attr($coverclass); ?>" style="background-image:url(<?php echo esc_url($coverbanner); ?>);">
  <?php if($coverbanner != ''){ ?>
  <div class="sf-overlay-main" style="opacity:<?php echo $bgopacity ?>; background-color:<?php echo $bgcolor ?>;"></div>
  <?php } ?>
  </div>
  <?php if(!$service_finder_options['profile-search-bar']){ 
  $classes = (service_finder_themestyle() == 'style-2') ? 'sf-search-result' : '';
  $srhposition = (!empty($service_finder_options['search-bar-position-profilepage'])) ? $service_finder_options['search-bar-position-profilepage'] : 'bottom';
	if($srhposition == 'middle'){
		$positionclass = 'pos-v-center';
	}else{
		$positionclass = 'pos-v-bottom';
    }
  ?>
  <div class="sf-find-bar-inr <?php echo $classes; ?> <?php echo sanitize_html_class($positionclass); ?>">
      <div class="container">
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
  <!-- End Search Form -->
  <!-- inner page banner END -->
  <?php require SERVICE_FINDER_BOOKING_FRONTEND_DIR . '/breadcrumb.php'; //Breadcrumb ?>
  <?php
if($providerInfo->account_blocked == 'yes' || !service_finder_check_profile_after_trial_expire($author) || $providerInfo->admin_moderation == 'pending' || ($restrictuserarea && $identitycheck && $providerInfo->identity != 'approved')){
require SERVICE_FINDER_BOOKING_FRONTEND_DIR . '/blocked-profile.php';
}else{
?>
  <!-- Left & right section start -->
  <div class="container">
    <div class="row section-content provider-content">
      <!-- Left part start -->
      <div class="col-md-8">
        
        <?php 
		$images = service_finder_getProviderAttachments($providerInfo->wp_user_id,'gallery'); 
		if(!empty($images)){
		?>
        <!-- Start Gallery Section-->
        <h4>
          <?php echo (!empty($service_finder_options['label-photo-gallery'])) ? esc_html($service_finder_options['label-photo-gallery']) : esc_html__('Photo Gallery', 'service-finder'); ?>
        </h4>
        <div class="padding-20  margin-b-30  bg-white sf-rouned-box" id="sf-provider-gallery">
          <!--Thumbnail slider Large images-->
			<div class="slider-container"> 
               <!--Main Slider Start--> 
               <div id="sliderLarge" class="sliderLarge owl-carousel">
                    
                    <?php
                    if(!empty($images)){
                    $i = 1;
                    foreach($images as $image){
                        $src  = wp_get_attachment_image_src( $image->attachmentid, 'service_finder-gallery-thumb-v1' );
                        $fullsrc  = wp_get_attachment_image_src( $image->attachmentid, 'service_finder-gallery-big-v1' );
                        $src  = $src[0];
                        $fullsrc  = $fullsrc[0];
                        
                        $html = sprintf('<div class="item"> 
                                             <div class="sf-thum-bx">
                                                <img src="%s" alt="">
                                             </div> 
                                          </div>',
                            esc_url($fullsrc)
                        );
                        echo $html;	
                        $i++;
                    }
                    }
                    ?>
               </div> 
               <!--Main Slider End-->

            </div>
                              
		  <!--Thumbnail slider small images--> 
			<div class="thumbnail-slider-container"> 
                                   <!--Thumbnail Slider Start--> 
                                   <div id="thumbnailSlider" class="thumbnail-slider owl-carousel owl-btn-center-lr"> 
                                   
                                        <?php
										if(!empty($images)){
										$i = 1;
										foreach($images as $image){
											$src  = wp_get_attachment_image_src( $image->attachmentid, 'service_finder-gallery-thumb-v1' );
											$fullsrc  = wp_get_attachment_image_src( $image->attachmentid, 'service_finder-gallery-big-v1' );
											$src  = $src[0];
											$fullsrc  = $fullsrc[0];
											
											$html = sprintf('<div class="item"> 
																 <div class="sf-thum-bx">
																	<img src="%s" alt="">
																 </div> 
															  </div>',
												esc_url($src)
											);
											echo $html;	
											$i++;
										}
										}
										?>
                                      
                                </div>
                                
                            </div>                                   
          
        </div>
        <!-- End Gallery Section-->
        <?php } ?>
        <!-- Start Provider Bio-->
        <h4>
          <?php echo (!empty($service_finder_options['label-provider-bio'])) ? esc_html($service_finder_options['label-provider-bio']) : esc_html__('Provider Bio', 'service-finder'); ?>
        </h4>
        <div class="padding-20  margin-b-30  bg-white sf-rouned-box" id="sf-provider-info">
          <div class="provider-details clearfix">
            <div class="provider-logo sf-feaProgrid-wrap">
              <?php if(service_finder_is_featured($author)){ ?>
              <div class="sf-feaProgrid-label"><?php esc_html_e('Featured', 'service-finder'); ?></div>
              <?php } ?>
			  <?php
			  	$profilethumb = service_finder_get_avatar_by_userid($author,'service_finder-provider-medium');
				if($profilethumb != ''){
				$imgtag = '<img src="'.esc_url($profilethumb).'" alt="">';
				}else{
				$imgtag = '';
				}
				echo $imgtag;
				?>
               <span class="sf-provider-name">
              <?php the_author_meta( 'first_name', $author ) ?>
              <?php the_author_meta( 'last_name', $author ); ?>
              <?php echo service_finder_check_varified_icon($author); ?>
              </span> 
              <?php if($service_finder_options['review-system']){ ?>
              <button class="btn btn-sm btn-primary sf-review-btn"><?php echo esc_html__('Write A Review', 'service-finder') ?></button>
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
            <div class="provider-text">
              <h2 class="sf-company-name"><?php echo service_finder_getCompanyName($providerInfo->wp_user_id) ?></h2>
              <span class="tagline"><?php echo (!empty($providerInfo->tagline)) ? $providerInfo->tagline : service_finder_default_tagline(); ?></span>
              <div class="sf-provider-cat sf-p-c-v2">
            <strong><?php esc_html_e('Categories', 'service-finder'); ?> : </strong>
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
			?>
			
        </div>
              <?php 
			  if($providerInfo->bio != ""){
			  echo apply_filters('the_content', $providerInfo->bio);
			  }
			  ?>
            </div>
          </div>
          <div class="provider-social clearfix">
            <?php if(service_finder_get_data($service_finder_options,'social-media') && ($providerInfo->facebook != "" || $providerInfo->twitter != "" || $providerInfo->linkedin != "" || $providerInfo->digg != "" || $providerInfo->pinterest != "" || $providerInfo->instagram != "")){ ?>
            <ul class="social-bx list-inline">
              <?php if($providerInfo->facebook != ""){ ?>
              <li><a href="<?php echo esc_url($providerInfo->facebook); ?>" target="_blank" rel="nofollow" class="fa fa-facebook"></a></li>
              <?php } ?>
              <?php if($providerInfo->twitter != ""){ ?>
              <li><a href="<?php echo esc_url($providerInfo->twitter); ?>" target="_blank" rel="nofollow" class="fa fa-twitter"></a></li>
              <?php } ?>
              <?php if($providerInfo->linkedin != ""){ ?>
              <li><a href="<?php echo esc_url($providerInfo->linkedin); ?>" target="_blank" rel="nofollow" class="fa fa-linkedin"></a></li>
              <?php } ?>
              <?php if($providerInfo->digg != ""){ ?>
              <li><a href="<?php echo esc_url($providerInfo->digg); ?>" target="_blank" rel="nofollow" class="fa fa-digg"></a></li>
              <?php } ?>
              <?php if($providerInfo->pinterest != ""){ ?>
              <li><a href="<?php echo esc_url($providerInfo->pinterest); ?>" target="_blank" rel="nofollow" class="fa fa-pinterest"></a></li>
              <?php } ?>
              <?php if($providerInfo->instagram != ""){ ?>
              <li><a href="<?php echo esc_url($providerInfo->instagram); ?>" target="_blank" rel="nofollow" class="fa fa-instagram"></a></li>
              <?php } ?>
            </ul>
            <?php } ?>
            <div class="show-rating-bx"><?php echo service_finder_displayRating(service_finder_getAverageRating($author)); ?></div>
          </div>
        </div>
        <!-- End Provider Bio-->
        <!-- Start Social media section-->
        <div class="padding-20  margin-b-30  bg-white sf-rouned-box">
          <div class="shared-bx clearfix">
            <ul class="sharebtn-bx">
              <?php if($service_finder_options['add-to-fav']){ ?>
              <li>
                <?php
							if(is_user_logged_in()){
								$myfav = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->favorites.' where user_id = %d AND provider_id = %d',$current_user->ID,$author));
								if(!empty($myfav)){
								echo '<a href="javascript:;" class="remove-favorite" data-proid="'.esc_attr($author).'" data-userid="'.esc_attr($current_user->ID).'"><i class="fa fa-heart"></i>'.esc_html__( 'My Favorite', 'service-finder' ).'</a>';
								}else{
								echo '<a href="javascript:;" class="add-favorite" data-proid="'.esc_attr($author).'" data-userid="'.esc_attr($current_user->ID).'"><i class="fa fa-heart"></i>'.esc_html__( 'Add to Fav', 'service-finder' ).'</a>';
								}
							}else{
								echo '<a href="javascript:;" data-action="login" data-redirect="no" data-toggle="modal" data-target="#login-Modal"><i class="fa fa-heart"></i>'.esc_html__( 'Add to Fav', 'service-finder' ).'</a>';
							}
							?>
              </li>
              <?php } ?>
              <?php if(get_user_meta($author,'claimbusiness',true) == 'enable' && get_user_meta($author,'claimed',true) != 'yes'){
			  $claim_business = (!empty($service_finder_options['string-claim-business'])) ? esc_html($service_finder_options['string-claim-business']) : esc_html__('Claim Business', 'service-finder');
			  ?>
              <li>
              <?php
              echo '<a href="javascript:;" data-toggle="modal" data-target="#claimbusiness-Modal" class="claimbusiness" data-proid="'.esc_attr($author).'"><i class="fa fa-briefcase"></i>'.$claim_business.'</a>';
			  ?>
              </li>
              <?php } ?>
              <?php  if(class_exists('WP_Job_Manager') && $inviteforjob){ ?>
              
              <?php
			  if(is_user_logged_in()){
								if(service_finder_getUserRole($current_user->ID) == 'Customer'){
								echo '<li><a href="javascript:;" data-action="invite" data-redirect="no" data-toggle="modal" data-target="#invite-job"><i class="fa fa-briefcase"></i>'.esc_html__('Invite for Job', 'service-finder').'</a></li>';
								}
							}else{
								echo '<li><a href="javascript:;" data-action="login" data-redirect="no" data-toggle="modal" data-target="#login-Modal"><i class="fa fa-briefcase"></i>'.esc_html__('Invite for Job', 'service-finder').'</a></li>';
							}
							?>
              
              <?php } ?>
            </ul>
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
        </div>
        <!-- End Social media section-->
        <?php 
		if(service_finder_check_business_hours_status($author)){
		?>
        <!-- Business Hours Section-->
        <h4>
          <?php echo (!empty($service_finder_options['label-public-business-hours'])) ? esc_html($service_finder_options['label-public-business-hours']) : esc_html__('Business Hours', 'service-finder'); ?>
        </h4>
        <div class="padding-20  margin-b-30  bg-white bh-section sf-rouned-box" id="sf-provider-hours"> <?php echo service_finder_showBusinessHours($author); ?> </div>
        <!-- End Business Hours Section-->
        <?php } ?>
        <?php if($service_finder_options['show-address-info'] && service_finder_check_address_info_access() && service_finder_get_data($service_finder_options,'show-contact-info')){ ?>
        <!-- Address Section-->
        <h4>
          <?php esc_html_e( 'Our Address', 'service-finder' ) ?>
        </h4>
        <div class="padding-20  margin-b-30  bg-white sf-rouned-box" id="sf-provider-address">
          <div class="provider-info-outer">
          <ul class="provider-info clearfix no-margin equal-col-outer">
             <?php if(service_finder_getAddress($author) != "" && $service_finder_options['show-postal-address']){ ?>
             <li class="equal-col"><i class="fa fa-map-marker"></i>
             	<strong><?php esc_html_e( 'Address', 'service-finder' ) ?>:</strong><span><?php echo service_finder_getAddress($author); ?></span>
             </li>
             <?php } ?>
             <?php if($providerInfo->lat != "" && $providerInfo->long != "" && $service_finder_options['show-gps']){ ?> 
             <li class="equal-col"><i class="fa fa-street-view"></i>
             	<strong><?php esc_html_e( 'GPS', 'service-finder' ) ?>:</strong><span><?php echo esc_html($providerInfo->lat).', '.esc_html($providerInfo->long); ?></span>
             </li>
             <?php } ?>
             <?php if(service_finder_get_contact_info_with_text($providerInfo->phone,$providerInfo->mobile) != "" && service_finder_contact_number_is_accessible($author)){ ?>
             <li class="equal-col"><i class="fa fa-phone"></i>
             	<strong><?php esc_html_e( 'Telephone', 'service-finder' ) ?>:</strong>
				 <span><?php echo service_finder_get_contact_info_with_text($providerInfo->phone,$providerInfo->mobile); ?></span>
             </li>
             <?php } ?>
             <?php if($service_finder_options['show-email-address']){ ?>
             <li class="equal-col"><i class="fa fa-envelope"></i>
	             <strong><?php esc_html_e( 'Email', 'service-finder' ) ?>:</strong><span><a href="mailto:<?php the_author_meta( 'user_email', $author ); ?>"><?php the_author_meta( 'user_email', $author ); ?></a></span>
             </li>
             <?php } ?>
             <?php if($providerInfo->fax != "" && $service_finder_options['show-fax']){ ?>
             <li class="equal-col"><i class="fa fa-fax"></i>
             	<strong><?php esc_html_e( 'Fax', 'service-finder' ) ?>:</strong><span><?php echo esc_html($providerInfo->fax); ?></span>
             </li>
             <?php } ?>
             <?php if($providerInfo->website != "" && $service_finder_options['show-website']){ ?> 
             <li class="equal-col"><i class="fa fa-globe"></i>
             	<strong><?php esc_html_e( 'Web', 'service-finder' ) ?>:</strong><span><a href="<?php echo service_finder_addhttp(esc_html($providerInfo->website)); ?>" target="_blank"><?php echo esc_html($providerInfo->website); ?></a></span>
             </li>
             <?php } ?>
             <?php if($providerInfo->skypeid != "" && $service_finder_options['show-skype']){ ?> 
             <li class="equal-col"><i class="fa fa-skype"></i>
             	<strong><?php esc_html_e( 'Skype', 'service-finder' ) ?>:</strong><span><a href="skype:<?php echo esc_html($providerInfo->skypeid); ?>?chat"><?php echo esc_html($providerInfo->skypeid); ?></a></span>
             </li>
             <?php } ?>
          </ul>
          </div>
        </div>
                            
        <!-- End Address Section-->
        <?php if($service_finder_options['show-contact-map'] && service_finder_show_map_on_site()){ ?> 
        <!-- Provider location on map-->
        <h4>
          <?php esc_html_e( 'Our Location', 'service-finder' ) ?>
        </h4>
        <div class="padding-20  margin-b-30  bg-white sf-rouned-box" id="sf-provider-map">
          <div class="provider-map">
            <div class="provider-location">
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
                
				echo do_shortcode('[service_finder_map general_latitude="'.$providerInfo->lat.'" general_longitude="'.$providerInfo->long.'" height="296px" width="709px;"]'); ?> </div>
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
        </div>
        <!-- End Provider location on map-->
        <?php } ?>
        <?php } ?>
        <!-- Languages Section-->
        <?php
		$languages = service_finder_get_languages($author);
		$languagearray = service_finder_get_alllanguages();
        if(!empty($languages)){
		?>  
        <h4>
          <?php esc_html_e( 'Languages', 'service-finder' ) ?>
        </h4>
        <div class="padding-20 margin-b-30 bg-white sf-rouned-box">
          <ul class="sf-languages-list clearfix">
          <?php
	  	  foreach($languages as $language){
				$flagimgsrc = SERVICE_FINDER_BOOKING_IMAGE_URL.'/flags/'.$language.'.png';
				$langname = (!empty($languagearray[$language])) ? $languagearray[$language] : '';
				echo '<li><img src="'.$flagimgsrc.'" alt=""> '.esc_html($langname).'</li>';
			}
		  ?>
          </ul>
        </div>
        <?php } ?>
        <!-- Experience Section-->
        <?php
        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$service_finder_Tables->experience. " WHERE `provider_id` = %d ORDER BY ID ASC",$author));
		if(!empty($results)){
		?>
        <h4>
          <?php esc_html_e( 'Experience', 'service-finder' ) ?>
        </h4>
        <div class="padding-20 margin-b-30 bg-white sf-rouned-box">
            <div class="sf-experience-acord" id="experience-acord">
            <?php
			$i = 1;
			foreach($results as $row){
					?>
					<div class="panel sf-panel">
                        <div class="acod-head acc-actives">
                             <h6 class="acod-title text-uppercase">
                                <a data-toggle="collapse" href="#experience-<?php echo esc_html($row->id); ?>" data-parent="#experience-acord" >
                                    <span class="exper-author"><?php echo esc_html($row->job_title); ?>
                                    <?php if($row->current_job == 'yes'){ ?>
                                    <strong class="sf-current-job"><?php echo esc_html__('Current Job', 'service-finder'); ?></strong>
                                    <?php } ?>
                                    </span>
                                    <span class="exper-slogan"><?php echo esc_html($row->company_name); ?></span>
                                    <?php if($row->current_job == 'yes'){ ?>
                                    <span class="exper-date"><i class="fa fa-calendar"></i> <?php echo date('M d Y',strtotime($row->start_date)); ?></span>
                                    <?php }else{ ?>
                                    <span class="exper-date"><i class="fa fa-calendar"></i> <?php echo date('M d Y',strtotime($row->start_date)).' -  '.date('M d Y',strtotime($row->end_date)); ?></span>
                                    <?php } ?>
                                </a>
                             </h6>
                        </div>
                        <div id="experience-<?php echo esc_html($row->id); ?>" class="acod-body collapse <?php echo ($i == 1) ? 'in' : ''; ?>">
                            <div class="acod-content p-tb15"><?php printf($row->description); ?></div>
                        </div>
                    </div>
					<?php
					$i++;
				}
			?>
            </div>
        </div>
        <?php } ?>
        
        <?php
        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$service_finder_Tables->certificates. " WHERE `provider_id` = %d ORDER BY ID ASC",$author));
		if(!empty($results)){
		?>
        <h4><?php esc_html_e( 'Certificates & Awards', 'service-finder' ) ?></h4>
		<div class="padding-20  margin-b-30  bg-white sf-rouned-box">
            <ul class="sf-certificates-list">
                <?php
				$fileicon = new SERVICE_FINDER_ImageSpace();
				foreach($results as $row){
					if($row->attachment_id != '' && $row->attachment_id > 0)
					{
					$arr  = $fileicon->get_icon_for_attachment($row->attachment_id);
				
					$src = (!empty($arr['src'])) ? $arr['src'] : '';
					
					if($src == ''){
					$arr  = service_finder_get_icon_for_attachment($row->attachment_id);
					$src  = $arr['src'];
					}
					}else{
					$src  = '';
					}
					?>
						<li>
                            <?php if(!empty($src) && $row->attachment_id > 0){ ?>
                            <div class="awards-pic"><img src="<?php echo esc_url($src); ?>" alt="">
                            <a class="sf-download-certificate" href="<?php echo get_permalink( $row->attachment_id ).'?attachment_id='. $row->attachment_id.'&download_file=1'; ?>"><i class="fa fa-download"></i> <?php echo esc_html__('View/Download'); ?></a>
                            </div>
                            <?php } ?>
                            <span class="awards-title"><?php echo esc_html($row->certificate_title); ?></span>
                            <span class="awards-date"><i class="fa fa-clock-o"></i> <?php echo date('M d Y',strtotime($row->issue_date)); ?></span>
                            <div class="awards-text"><?php printf($row->description); ?></div>
                        </li>
					<?php
					}
				?>
            </ul>
        </div>
        <?php } ?>
        
        <!-- Qualification Section-->
        <?php
        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$service_finder_Tables->qualification. " WHERE `provider_id` = %d ORDER BY ID ASC",$author));
		if(!empty($results)){
		?>
        <h4>
          <?php esc_html_e( 'Qualification', 'service-finder' ) ?>
        </h4>
        <div class="padding-20 margin-b-30 bg-white sf-rouned-box">
            <div class="sf-qualification-acord" id="qualification-acord">
            <?php
			$i = 1;
			foreach($results as $row){
					?>
					<div class="panel sf-panel">
                        <div class="acod-head acc-actives">
                             <h6 class="acod-title text-uppercase">
                                <a data-toggle="collapse" href="#qualification-<?php echo esc_html($row->id); ?>" data-parent="#qualification-acord" >
                                    <span class="exper-author"><?php echo esc_html($row->degree_name); ?></span>
                                    <span class="exper-slogan"><?php echo esc_html($row->institute_name); ?></span>
                                    <span class="exper-date"><i class="fa fa-calendar"></i> <?php echo esc_html($row->from_year).' -  '.esc_html($row->to_year); ?></span>
                                </a>
                             </h6>
                        </div>
                        <div id="qualification-<?php echo esc_html($row->id); ?>" class="acod-body collapse <?php echo ($i == 1) ? 'in' : ''; ?>">
                            <div class="acod-content p-tb15"><?php printf($row->description); ?></div>
                        </div>
                    </div>
					<?php
					$i++;
				}
			?>
            </div>
        </div>
        <?php } ?>
        <!-- Amenities and Features Section-->
        <?php
        $amenities = service_finder_get_amenities($author);
		if(!empty($amenities)){
		?>
        <h4><?php esc_html_e( 'Amenities and Features', 'service-finder' ) ?></h4>
        <div class="padding-20  margin-b-30  bg-white sf-rouned-box">
            <ul class="sf-features-list clearfix">
                <?php
				foreach($amenities as $amenityid){
						$amenityinfo = get_term_by('id',$amenityid,'sf-amenities');
						$src = service_finder_getTermIcon($amenityid);
						$imgtag = '';
						if($src != ""){
							$imgtag = '<img src="'.esc_url($src).'" alt="">';
						}
						echo '<li>'.$imgtag.' '.esc_html($amenityinfo->name).'</li>';
					}
			    ?>
            </ul>
        </div> 
        <?php } ?>
        
        <?php
		if($providerInfo->embeded_code != '')
		{
		if(is_serialized($providerInfo->embeded_code)){
		$embeded_codes = unserialize($providerInfo->embeded_code);
		$totalvideos = count($embeded_codes);
		if(!empty($embeded_codes)){
			?>
			<h4>
              <?php echo (!empty($service_finder_options['label-our-video'])) ? esc_html($service_finder_options['label-our-video']) : esc_html__('Our Video', 'service-finder'); ?>
            </h4>
			<?php
			if($totalvideos > 1){
			echo '<div class="padding-20  margin-b-30  bg-white sf-rouned-box" id="sf-provider-video">';
			echo '<div id="slider" class="owl-video sf-video-gallery owl-btn-center-lr">';
			$oembed = new ClassOEmbed();
			foreach($embeded_codes as $embeded_code){
			?>
				<div class="item">
                    <div class="ow-portfolio sf-video-box">
                        <div class="sf-thum-bx  img-effect1">
                                    
                                        <a href="javascript:void(0);">
                                            <?php
                                            echo service_finder_identify_videos($embeded_code,'full');
											?>
                                        </a>
                                        
                                        <div class="overlay-bx">
                                            <div class="overlay-icon">
                                            <?php
                                            $videotype = service_finder_get_video_type($embeded_code);
											if($videotype == 'facebook'){
											?>
											<a href="https://www.facebook.com/v2.5/plugins/video.php?href=<?php echo esc_url($embeded_code); ?>" class="mfp-link">
                                                	<i class="fa fa-play icon-bx-xs"></i>
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
											<a href="<?php echo esc_url($embeded_code); ?>" class="mfp-link">
                                                	<i class="fa fa-play icon-bx-xs"></i>
                                                </a>
											<?php
											}
											?>
                                            </div>
                                        </div>
                                        
                                    </div>
                        
                        
                    </div>
                </div>
				<?php
			}
			echo '</div>';
			echo '</div>';
			}else{
			?>
			<div class="padding-20  margin-b-30  bg-white sf-rouned-box">
              <div class="embed-responsive embed-responsive-16by9">
                <?php 
				$oembed = new ClassOEmbed();
				echo $oembed->getembededcode($embeded_codes[0]); 
				?>
              </div>
            </div>
			<?php
			}
			
		}
		}else{
			if($providerInfo->embeded_code != ""){
			?>
			<h4>
              <?php echo (!empty($service_finder_options['label-our-video'])) ? esc_html($service_finder_options['label-our-video']) : esc_html__('Our Video', 'service-finder'); ?>
            </h4>
            <div class="padding-20  margin-b-30  bg-white sf-rouned-box">
              <div class="embed-responsive embed-responsive-16by9">
                <?php 
				$oembed = new ClassOEmbed();
				echo $oembed->getembededcode($providerInfo->embeded_code); 
				?>
              </div>
            </div>
			<?php	
			}
		}
		}
		?>
        <?php
        if($service_finder_options['my-services-menu'])
		{
		?>
        <h4><?php echo (!empty($service_finder_options['label-our-services'])) ? esc_html($service_finder_options['label-our-services']) : esc_html__('Our services', 'service-finder'); ?></h4>
		<div class="padding-20 margin-b-30 bg-white sf-rouned-box">
        
            <div class="sf-services-area" id="sf-services-listing">
                <?php do_action('service_finder_display_services',$author); ?>
            </div>
            
        </div>                               
    	<?php } ?>

        <!-- Our Services-->
        <?php
		$attachmentIDs = service_finder_getDocuments($providerInfo->wp_user_id);
        if(!empty($attachmentIDs)){
		?>
        <h4>
          <?php echo (!empty($service_finder_options['label-documents'])) ? esc_html($service_finder_options['label-documents']) : esc_html__('Documents', 'service-finder'); ?>
        </h4>
        <div class="padding-20  margin-b-30  bg-white sf-rouned-box" id="sf-provider-services">
          <div class="tabbable">
            <ul class="nav nav-tabs">
              <li class="active"><a data-toggle="tab" href="#box2">
                <?php echo (!empty($service_finder_options['label-documents'])) ? esc_html($service_finder_options['label-documents']) : esc_html__('Documents', 'service-finder'); ?>
                </a></li>
            </ul>
            <div class="tab-content">
              <div id="box2" class="tab-pane fade in active">
                <?php 
								$k=0;
								if(!empty($attachmentIDs)){
								echo '<table class="table borderless margin-0 sf-documents-table">';
									foreach($attachmentIDs as $attachmentID){
									if (!$k%2) echo '<tr>';
										$html = sprintf('<td>
                                	<div class="panel panel-default">
                                        <div class="panel-heading">
                                            <strong class="price-bx"><a download="%s" href="%s"><i class="fa fa-download"></i></a></strong>
                                            <span class="service-title">%s</span>
                                        </div>
                                    </div>
                                </td>',
													basename(get_attached_file($attachmentID->attachmentid)),
													wp_get_attachment_url( $attachmentID->attachmentid ), 
													basename(get_attached_file($attachmentID->attachmentid))
												);
													echo $html;	
									if ($k%2) echo '</tr>';
								      $k++;
									}
								echo '</table>';	
								}else{
									echo '<div>';
									esc_html_e('No documents Available', 'service-finder');
									echo '</div>';
								}
								?>
              </div>
            </div>
          </div>
        </div>
        <?php } ?>
        <!--End Our Services-->
        <?php
		/*Display invoice summary and option to pay*/
        if(isset($_GET['invoiceid']) && $_GET['invoiceid'] != ""){
			$service_finder_Tables = service_finder_plugin_global_vars('service_finder_Tables');
			$invoiceid = service_finder_decrypt($_GET['invoiceid'], 'Developer#@)!%');
			$sql = $wpdb->prepare("SELECT invoice.id, invoice.reference_no, invoice.duedate, invoice.booking_id, invoice.discount_type, invoice.tax_type, invoice.discount, invoice.tax, invoice.services, invoice.description, invoice.total, invoice.grand_total, invoice.status, customers.name, customers.phone as cusphone, customers.phone2 as cusphone2, customers.email as cusemail, customers.address as cusaddress, customers.apt as cusapt, customers.city as cuscity, customers.state as cusstate, customers.zipcode as cuszipcdoe, customers.description, providers.full_name, providers.phone, providers.email, providers.mobile, providers.fax, providers.address, providers.apt, providers.city, providers.state, providers.zipcode, providers.country FROM ".$service_finder_Tables->invoice." as invoice INNER JOIN ".$service_finder_Tables->customers." as customers on invoice.customer_email = customers.email LEFT JOIN ".$service_finder_Tables->providers." as providers on invoice.provider_id = providers.wp_user_id WHERE invoice.id = %d",$invoiceid);
	
			$row = $wpdb->get_row($sql);
			
			$discount_type = $row->discount_type;
			$tax_type = $row->tax_type;
			if($row->discount > 0){
			if($discount_type == 'fix'){
				$displaydiscount = $row->discount;
			}elseif($discount_type == 'percentage'){
				$displaydiscount = $row->total * ($row->discount/100);
			}
			}else{
				$displaydiscount = '0.00';
			}
			
			if($row->tax > 0){
			if($tax_type == 'fix'){
				$displaytax = $row->tax;
			}elseif($tax_type == 'percentage'){
				$displaytax = $row->total * ($row->tax/100);
			}
			}else{
				$displaytax = '0.00';
			}
			
			$services = unserialize($row->services);
			$servicehtml = '';
			if(!empty($services)){
			foreach($services as $key => $value){
			if($value[0] == 'new'){
				$servicename =  esc_html__('Extra Service', 'service-finder');
			}else{
				$servicedata = service_finder_getServiceData($value[0]);
				$servicename = stripcslashes($servicedata->service_name);
			}
			
			
			if($value[1] == 'fix'){
				$hrs = esc_html__('N/A', 'service-finder');
			}else{
				$hrs = $value[2];
			}
			
			$servicehtml .= '<tr>
										<td>'.($key+1).'</td>
                                        <td>'.$servicename.'</td>
                                        <td>'.$value[1].'</td>
                                        <td>'.$hrs.'</td>
                                        <td>'.$value[3].'</td>
										<td>'.$value[4].'</td>
							</tr>';
			}
		}
											$year = date('Y');
											$yearoption = '';
                                            for($k = $year;$k<=$year+50;$k++){
												$yearoption .= '<option value="'.esc_attr($k).'">'.$k.'</option>';
											}
		
											$payflag = 0;
											$availablepaymentmethod = '';
											$paynowbtn = '';
$pay_booking_amount_to = (!empty($service_finder_options['pay_booking_amount_to'])) ? esc_html($service_finder_options['pay_booking_amount_to']) : '';
$payment_methods = (!empty($service_finder_options['payment-methods'])) ? $service_finder_options['payment-methods'] : '';

if($pay_booking_amount_to == 'admin'){
if($payment_methods['paypal']){
	$checkpaypal = true;
}else{
	$checkpaypal = false;
}
}elseif($pay_booking_amount_to == 'provider'){
if(!empty($settings['paymentoption'])){
if(in_array('paypal',$settings['paymentoption'])){
	$checkpaypal = true;
}else{
	$checkpaypal = false;
}
}else{
	$checkpaypal = false;
}
}

if($checkpaypal){ 

$availablepaymentmethod .= '
<div class="radio sf-radio-checkbox">
<input type="radio" value="paypal" name="invoicepayment_mode" id="invoicepaypal" >
<label for="invoicepaypal"><img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/paypal.jpg" title="'.esc_html__('Paypal','service-finder').'" alt="'.esc_html__('paypal','service-finder').'"></label>
</div>
';
$payflag = 1;
}

if($pay_booking_amount_to == 'admin'){
if($payment_methods['payumoney']){
	$checkpayumoney = true;
}else{
	$checkpayumoney = false;
}
}elseif($pay_booking_amount_to == 'provider'){
if(!empty($settings['paymentoption'])){
if(in_array('payumoney',$settings['paymentoption'])){
	$checkpayumoney = true;
}else{
	$checkpayumoney = false;
}
}else{
	$checkpayumoney = false;
}
}

if($checkpayumoney){ 

$availablepaymentmethod .= '
<div class="radio sf-radio-checkbox">
<input type="radio" value="payumoney" name="invoicepayment_mode" id="invoicepayumoney" >
<label for="invoicepayumoney"><img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/payumoney.jpg" title="'.esc_html__('PayU Money','service-finder').'" alt="'.esc_html__('PayU Money','service-finder').'"></label>
</div>
';
$payflag = 1;
}

if($pay_booking_amount_to == 'admin'){
if($payment_methods['payulatam']){
	$checkpayulatam = true;
}else{
	$checkpayulatam = false;
}
}elseif($pay_booking_amount_to == 'provider'){
if(!empty($settings['paymentoption'])){
if(in_array('payulatam',$settings['paymentoption'])){
	$checkpayulatam = true;
}else{
	$checkpayulatam = false;
}
}else{
	$checkpayulatam = false;
}
}

if($checkpayulatam){ 

$availablepaymentmethod .= '
<div class="radio sf-radio-checkbox">
<input type="radio" value="payulatam" name="invoicepayment_mode" id="invoicepayulatam" >
<label for="invoicepayulatam"><img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/payulatam.jpg" title="'.esc_html__('PayU Latam','service-finder').'" alt="'.esc_html__('PayU Latam','service-finder').'"></label>
</div>
';
$payflag = 1;
}

if($pay_booking_amount_to == 'admin'){
if($payment_methods['stripe']){
$checkstripe = true;
}else{
$checkstripe = false;
}
}elseif($pay_booking_amount_to == 'provider'){
if(!empty($settings['paymentoption'])){
if(in_array('stripe',$settings['paymentoption'])){
$checkstripe = true;
}else{
$checkstripe = false;
}
}else{
$checkstripe = false;
}
}

if($checkstripe){
$availablepaymentmethod .= '
<div class="radio sf-radio-checkbox">
<input type="radio" value="stripe" name="invoicepayment_mode" id="invoicestripe">
<label for="invoicestripe"><img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/mastercard.jpg" title="'.esc_html__('Stripe','service-finder').'" alt="'.esc_html__('mastercard','service-finder').'"><img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/payment.jpg" title="'.esc_html__('Stripe','service-finder').'" alt="'.esc_html__('american express','service-finder').'"><img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/discover.jpg" title="'.esc_html__('Stripe','service-finder').'" alt="'.esc_html__('discover','service-finder').'"><img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/visa.jpg" title="'.esc_html__('Stripe','service-finder').'" alt="'.esc_html__('visa','service-finder').'"></label>
</div>
';
									$payflag = 1;
									}
											

$checktwocheckout = '';	
if($pay_booking_amount_to == 'admin'){
if(isset($payment_methods['twocheckout'])){
if($payment_methods['twocheckout']){
	$checktwocheckout = true;
}else{
	$checktwocheckout = false;
}
}else{
	$checktwocheckout = false;
}
}elseif($pay_booking_amount_to == 'provider'){
if(!empty($settings['paymentoption'])){
if(in_array('twocheckout',$settings['paymentoption'])){
	$checktwocheckout = true;
}else{
	$checktwocheckout = false;
}
}else{
	$checktwocheckout = false;
}
}

if($checktwocheckout){
$availablepaymentmethod .= '
<div class="radio sf-radio-checkbox">
<input type="radio" value="twocheckout" name="invoicepayment_mode" id="invoicetwocheckout">
<label for="invoicetwocheckout"><img src="'.SERVICE_FINDER_BOOKING_IMAGE_URL.'/payment/twocheckout.jpg" alt="'.esc_html__('2Checkout','service-finder').'"></label>
</div>
';
$payflag = 1;
}

$availablepaymentmethod .= service_finder_add_wallet_option('invoicepayment_mode','invoice');

											
											if($paymentsystem == 'woocommerce'){
												$payflag = 1;
											}
											if($payflag == 1 || service_finder_check_wallet_system()){
											$paynowbtn = '<input name="invoicepayment" id="invoicepayment" type="submit" value="'.esc_html__( 'Pay Now', 'service-finder' ).'" class="btn btn-primary">	';
											}
		
		$payform = '';									
		if($row->status != 'paid' && $row->status != 'on-hold'){
		$payform = '<form class="myform pay-now" method="post">';
					if($paymentsystem != 'woocommerce'){
					$walletamount = service_finder_get_wallet_amount($current_user->ID);
					$payform .= service_finder_display_wallet_amount($current_user->ID);
					$payform .= '<div class="col-md-12">
							<div class="form-group form-inline sf-card-group">
												
							'.$availablepaymentmethod.'
													
							</div>
							</div>';
					}else{
					$payform .= '<div class="col-lg-12">
						  <div class="form-group form-inline">';
					$payform .= service_finder_add_wallet_option('invoice_woopayment','invoice');
					$payform .= service_finder_add_woo_commerce_option('invoice_woopayment','invoice');
					$payform .= '</div></div>';
					}		
						
						$description = (!empty($service_finder_options['wire-transfer-description'])) ? $service_finder_options['wire-transfer-description'] : '';
						$payform .= '<div id="invoicecardinfo" class="default-hidden">
						  <div class="col-md-8">
							<div class="form-group">
								<label>'.esc_html__( 'Card Number', 'service-finder' ).'</label>
								<div class="input-group">
									<i class="input-group-addon fa fa-credit-card"></i>
									<input type="text" id="card_number" name="card_number" class="form-control">
								</div>
							</div>
						</div>
						  <div class="col-md-4">
							<div class="form-group">
								<label>'.esc_html__( 'CVC', 'service-finder' ).'</label>
								<div class="input-group">
									<i class="input-group-addon fa fa-ellipsis-h"></i>
									<input type="text" id="card_cvc" name="card_cvc" class="form-control">
								</div>
							</div>
						</div>
						  <div class="col-md-6">
							<div class="form-group has-select">
								<label>'.esc_html__( 'Select Month', 'service-finder' ).'</label>
								<select id="card_month" name="card_month" class="form-control sf-form-control sf-select-box" title="Select Month">
									  <option value="1">'.esc_html__('January', 'service-finder').'</option>
									  <option value="2">'.esc_html__('February', 'service-finder').'</option>
									  <option value="3">'.esc_html__('March', 'service-finder').'</option>
									  <option value="4">'.esc_html__('April', 'service-finder').'</option>
									  <option value="5">'.esc_html__('May', 'service-finder').'</option>
									  <option value="6">'.esc_html__('June', 'service-finder').'</option>
									  <option value="7">'.esc_html__('July', 'service-finder').'</option>
									  <option value="8">'.esc_html__('August', 'service-finder').'</option>
									  <option value="9">'.esc_html__('September', 'service-finder').'</option>
									  <option value="10">'.esc_html__('October', 'service-finder').'</option>
									  <option value="11">'.esc_html__('November', 'service-finder').'</option>
									  <option value="12">'.esc_html__('December', 'service-finder').'</option>
							  </select>
							</div>
						</div>
						  <div class="col-md-6">
							<div class="form-group has-select">
								<label>'.esc_html__( 'Select Year', 'service-finder' ).'</label>
								<select id="card_year" name="card_year" class="form-control sf-form-control sf-select-box"  title="Select Year">
								'.$yearoption.'
							  </select>
							</div>
						</div>
						  </div>
						  <div id="invoicewiredinfo" class="default-hidden">
                    <div class="col-md-12 margin-b-20">
                        '.$description.'
                    </div>
                  </div>
						  <div id="payulataminvoicecardinfo" class="default-hidden">
    <div class="col-md-12">
	  <div class="form-group">
		<label>
		'.esc_html__('Select Card', 'service-finder').'
		</label>
	   <select id="payulatam_invoice_cardtype" name="payulatam_invoice_cardtype" class="form-control sf-form-control sf-select-box"  title="'.esc_html__('Select Card', 'service-finder').'">';
		  $country = (isset($service_finder_options['payulatam-country'])) ? $service_finder_options['payulatam-country'] : '';
		  $cards = service_finder_get_cards($country);
		  foreach($cards as $card){
			$payform .= '<option value="'.esc_attr($card).'">'.$card.'</option>';
		  }
		$payform .= '</select>
	  </div>
	</div>
	<div class="col-md-8">
      <div class="form-group">
        <label>'.esc_html__('Card Number', 'service-finder').'</label>
        <div class="input-group"> <i class="input-group-addon fa fa-credit-card"></i>
          <input type="text" id="payulatam_card_number" name="payulatam_card_number" class="form-control">
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label>'.esc_html__('CVC', 'service-finder').'</label>
        <div class="input-group"> <i class="input-group-addon fa fa-ellipsis-h"></i>
          <input type="text" id="payulatam_card_cvc" name="payulatam_card_cvc" class="form-control">
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group has-select">
        <label>'.esc_html__('Select Month', 'service-finder').'</label>
        <select id="payulatam_card_month" name="payulatam_card_month" class="form-control sf-form-control sf-select-box" title="'.esc_html__('Select Month', 'service-finder').'">
          <option value="01">'.esc_html__('January', 'service-finder').'</option>
		  <option value="02">'.esc_html__('February', 'service-finder').'</option>
		  <option value="03">'.esc_html__('March', 'service-finder').'</option>
		  <option value="04">'.esc_html__('April', 'service-finder').'</option>
		  <option value="05">'.esc_html__('May', 'service-finder').'</option>
		  <option value="06">'.esc_html__('June', 'service-finder').'</option>
		  <option value="07">'.esc_html__('July', 'service-finder').'</option>
		  <option value="08">'.esc_html__('August', 'service-finder').'</option>
		  <option value="09">'.esc_html__('September', 'service-finder').'</option>
		  <option value="10">'.esc_html__('October', 'service-finder').'</option>
		  <option value="11">'.esc_html__('November', 'service-finder').'</option>
		  <option value="12">'.esc_html__('December', 'service-finder').'</option>
        </select>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group has-select">
        <label>'.esc_html__('Select Year', 'service-finder').'</label>
        <select id="payulatam_card_year" name="payulatam_card_year" class="form-control sf-form-control sf-select-box"  title="'.esc_html__('Select Year', 'service-finder').'">
          
                                            '.$yearoption.'
                                          
        </select>
      </div>
    </div>
  </div>
									  <div id="twocheckoutinvoicecardinfo" class="default-hidden">
    <div class="col-md-8">
      <div class="form-group">
        <label>'.esc_html__('Card Number', 'service-finder').'</label>
        <div class="input-group"> <i class="input-group-addon fa fa-credit-card"></i>
          <input type="text" id="twocheckout_card_number" name="twocheckout_card_number" class="form-control">
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label>'.esc_html__('CVC', 'service-finder').'</label>
        <div class="input-group"> <i class="input-group-addon fa fa-ellipsis-h"></i>
          <input type="text" id="twocheckout_card_cvc" name="twocheckout_card_cvc" class="form-control">
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group has-select">
        <label>'.esc_html__('Select Month', 'service-finder').'</label>
        <select id="twocheckout_card_month" name="twocheckout_card_month" class="form-control sf-form-control sf-select-box" title="Select Month">
          <option value="1">'.esc_html__('January', 'service-finder').'</option>
		  <option value="2">'.esc_html__('February', 'service-finder').'</option>
		  <option value="3">'.esc_html__('March', 'service-finder').'</option>
		  <option value="4">'.esc_html__('April', 'service-finder').'</option>
		  <option value="5">'.esc_html__('May', 'service-finder').'</option>
		  <option value="6">'.esc_html__('June', 'service-finder').'</option>
		  <option value="7">'.esc_html__('July', 'service-finder').'</option>
		  <option value="8">'.esc_html__('August', 'service-finder').'</option>
		  <option value="9">'.esc_html__('September', 'service-finder').'</option>
		  <option value="10">'.esc_html__('October', 'service-finder').'</option>
		  <option value="11">'.esc_html__('November', 'service-finder').'</option>
		  <option value="12">'.esc_html__('December', 'service-finder').'</option>
        </select>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group has-select">
        <label>'.esc_html__('Select Year', 'service-finder').'</label>
        <select id="twocheckout_card_year" name="twocheckout_card_year" class="form-control sf-form-control sf-select-box"  title="Select Year">
          
                                            '.$yearoption.'
                                          
        </select>
      </div>
    </div>
  </div>
									  <input type="hidden" name="email" id="email" value="'.esc_attr($row->cusemail).'">
									  <input type="hidden" name="amount" id="amount" value="'.esc_attr($row->grand_total).'">
									  <input type="hidden" name="provider" id="provider" data-provider="'.esc_attr($author).'" value="'.esc_attr($author).'">
									  <input type="hidden" name="invoiceid" id="invoiceid" value="'.esc_attr($invoiceid).'">
									  '.$paynowbtn.'
								</form>';
		}elseif($row->status == 'on-hold'){
		$payform .= '<div class="alert alert-info">'.esc_html__('You have paid via wire transfer. Please wait once its approved.', 'service-finder').'</div>';
		}else{
			echo '<input type="hidden" name="provider" id="provider" data-provider="'.esc_attr($author).'" value="'.esc_attr($author).'">';
		}
		
		$now = time();
		$date = $row->duedate;
		
		if($row->status == 'pending' && strtotime($date) < $now){
			$status = esc_html__( 'Overdue', 'service-finder' );
		}else{
			$status = sprintf( esc_html__('%s', 'service-finder'), service_finder_translate_static_status_string($row->status) );
		}
		$cuszipcode = (!empty($row->cuszipcode)) ? $row->cuszipcode : '';
		$html = '<div class="padding-20 margin-b-30 bg-white sf-rouned-box" id="invoiceview"><div class="invoice-view">
                                
                                    <div class="row">
									<div class="col-md-12"><span class="invoice-status">'.ucfirst($status).'</span></div>
                                        <div class="col-md-6 col-sm-6">
											<h4>'.esc_html__( 'Invoice Manager', 'service-finder' ).'</h4>
                                        	<table class="table">
                                                <tbody><tr>
                                                	<td>'.esc_html__( 'Name', 'service-finder' ).': '.$row->full_name.'</td>
                                                </tr>
												<tr>
                                                	<td>'.esc_html__( 'Email', 'service-finder' ).': '.$row->email.'</td>
                                                </tr>
												<tr>
                                                	<td>'.esc_html__( 'Phone', 'service-finder' ).': '.$row->phone.' '.$row->mobile.'</td>
                                                </tr>
												<tr>
                                                	<td>'.esc_html__( 'Fax', 'service-finder' ).': '.$row->fax.'</td>
                                                </tr>
                                                <tr>
                                                	<td>'.esc_html__( 'Address', 'service-finder' ).': '.$row->apt.' '.$row->address.'</td>
                                                </tr>
                                                <tr>
                                                	<td>'.$row->city.', '.$row->state.'</td>
                                                </tr>
												<tr>
                                                	<td>'.esc_html__( 'Postal Code', 'service-finder' ).': '.$row->zipcode.'</td>
                                                </tr>
                                                
                                            </tbody></table>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                        	<h4>'.esc_html__( 'Billed to', 'service-finder' ).': </h4>
                                        	<table class="table">
                                                <tbody><tr>
                                                	<td>'.esc_html__( 'Attn', 'service-finder' ).': '.$row->name.'</td>
                                                </tr>
                                                <tr>
                                                	<td>'.esc_html__( 'Email', 'service-finder' ).': '.$row->cusemail.'</td>
                                                </tr>
												<tr>
                                                	<td>'.esc_html__( 'Phone', 'service-finder' ).': '.$row->cusphone.' '.$row->cusphone2.'</td>
                                                </tr>
                                                <tr>
                                                	<td>'.esc_html__( 'Address', 'service-finder' ).': '.$row->cusapt.' '.$row->cusaddress.'</td>
                                                </tr>
                                                <tr>
                                                	<td>'.$row->cuscity.', '.$row->cusstate.'</td>
                                                </tr>
												<tr>
                                                	<td>'.esc_html__( 'Postal Code', 'service-finder' ).': '.$cuszipcode.'</td>
                                                </tr>
                                            </tbody></table>
                                        </div>
                                    </div>
                                    
                                    <br>
                                    
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6">
                                        	<h4>Invoice No <strong class="text-primary">'.$row->id.'</strong></h4>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                        	<table class="table">
                                                <tbody><tr>
                                                	<td><strong>'.esc_html__( 'Reference No', 'service-finder' ).': '.$row->reference_no.'</strong></td>
                                                </tr>
                                                <tr>
                                                	<td><strong>'.esc_html__( 'Due Date', 'service-finder' ).': '.date('d-m-Y',strtotime($row->duedate)).'</strong></td>
                                                </tr>
                                            </tbody></table>
                                        </div>
                                    </div>
                                    
                                    <table class="table table-bordered table-hover profile-margin-in">
                                    
                                    	<thead>
                                        	<tr>
                                            	<th>'.esc_html__( 'No', 'service-finder' ).'</th>
                                                <th>'.esc_html__( 'Service', 'service-finder' ).'</th>
                                                <th>'.esc_html__( 'Type', 'service-finder' ).'</th>
                                                <th>'.esc_html__( 'Hours', 'service-finder' ).'</th>
												<th>'.esc_html__( 'Description', 'service-finder' ).'</th>
                                                <th>'.esc_html__( 'Price', 'service-finder' ).'</th>
                                            </tr>
                                        </thead>
                                        
                                        <tbody>
                                            '.$servicehtml.'
                                            <tr>
                                              <td colspan="6">&nbsp;</td>
                                            </tr>
                                            <tr>
                                              <td colspan="4" class="text-right font-weight-600">'.esc_html__( 'Total', 'service-finder' ).'('.service_finder_currencycode().')'.'</td>
                                              <td colspan="2" class="text-right font-weight-600">'.service_finder_money_format($row->total).'</td>
                                            </tr>
											<tr>
                                              <td colspan="4" class="text-right font-weight-600">'.esc_html__( 'Discount', 'service-finder' ).'</td>
                                              <td colspan="2" class="text-right font-weight-600">'.$displaydiscount.'</td>
                                            </tr>
											<tr>
                                              <td colspan="4" class="text-right font-weight-600">'.esc_html__( 'Tax', 'service-finder' ).'</td>
                                              <td colspan="2" class="text-right font-weight-600">'.$displaytax.'</td>
                                            </tr>
                                            <tr class="info">
                                              <td colspan="4" class="text-right font-weight-600">'.esc_html__( 'Grand Total', 'service-finder' ).'('.service_finder_currencycode().')'.'</td>
                                              <td colspan="2" class="text-right font-weight-600">'.service_finder_money_format($row->grand_total).'</td>
                                            </tr>
                                        </tbody>
  
    
                                    </table>

                                </div>'.$payform.'</div>';
		
		echo $html;
		
		echo '<input type="hidden" id="provider" name="provider" data-provider="'.esc_attr($author).'" value="'.esc_attr($author).'" />';
			
			}else{
			?>
        <?php
      if(!empty($userCap)){
		if(in_array('bookings',$userCap)){	
	  ?>
        <?php if($settings['booking_process'] == 'on' && (!is_user_logged_in() || service_finder_getUserRole($current_user->ID) == 'administrator' || service_finder_getUserRole($current_user->ID) == 'Customer' || (service_finder_getUserRole($current_user->ID) == 'Provider' && $current_user->ID == $author) )){ ?>
        <?php
        if((!is_user_logged_in() && !$service_finder_options['guest-booking'])){
			echo '<div class="alert alert-danger" role="alert">';
			echo esc_html__('In Order to book services you have to login.', 'service-finder');
			echo '</div>';
		}
		?>
        <!-- Booking Form Start -->
        <h4 class="book-now-scroll" id="book-now-section">
          <?php echo (!empty($service_finder_options['label-book-now'])) ? esc_html($service_finder_options['label-book-now']) : esc_html__('Book Now', 'service-finder'); ?>
          <span class="mincost">
          <?php echo (!empty($service_finder_options['label-booking-amount'])) ? esc_html($service_finder_options['label-booking-amount']) : esc_html__('Booking Amount: ', 'service-finder'); ?>
          <?php
		  $bookingcost = ($settings['mincost'] != "") ? $settings['mincost'] : '0.0';
		  ?>
          <strong><?php echo '<span id="bookingamount">'.service_finder_money_format($bookingcost).'</span>'; ?></strong></span>
        </h4>
        <?php if($providerInfo->booking_description != ""){ ?>
        <div class="padding-20  margin-b-30  bg-white sf-rouned-box">
          <div class="booking-desc-bx"><?php echo nl2br(stripcslashes($providerInfo->booking_description)) ?></div>
        </div>
        <?php } ?>
        <?php
		if(service_finder_has_pay_only_admin_fee()){
		?>
		<div class="sf-adminfee-outer" style="display:none" id="adminfee-outer">
		<div class="sf-payonly-adminfee"><span><?php echo esc_html__('Admin Fee:', 'service-finder'); ?></span> <span id="onlyadminfee"></span></div>
		<div class="sf-payonly-adminfee"><?php echo esc_html__('You need to pay only admin fee at the time of booking.', 'service-finder'); ?></div>
		</div>
		<?php
		}
		?>

        <?php require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/book-now/templates/book-now-v1.php'; ?>
        <?php }else{
		echo '<input type="hidden" id="provider" name="provider" data-provider="'.esc_attr($author).'" value="'.esc_attr($author).'" />';
		} ?>
        <?php 
	  	}
	  }
	  ?>
        <?php } ?>
      <?php if($service_finder_options['review-system'] || service_finder_get_data($service_finder_options,'question-answer-section')){ ?>
      <h4>
          <?php echo (!empty($service_finder_options['label-review-qa'])) ? esc_html($service_finder_options['label-review-qa']) : esc_html__('Review & Q&A', 'service-finder'); ?>
        </h4>
        <div class="padding-20  margin-b-30  bg-white sf-rouned-box review-section" id="sf-provider-extended">
          <div class="tabbable">
            <ul class="nav nav-tabs">
              <?php if($service_finder_options['review-system']){ ?>
              <li class="active"><a data-toggle="tab" href="#qa1">
                <?php echo (!empty($service_finder_options['label-review'])) ? esc_html($service_finder_options['label-review']) : esc_html__('Review', 'service-finder'); ?>
                </a></li>
              <?php } ?>
              <?php if(service_finder_get_data($service_finder_options,'question-answer-section')){ ?>
              <li <?php echo (!$service_finder_options['review-system']) ? 'class="active"' : ''; ?>><a data-toggle="tab" href="#qa2">
                <?php echo (!empty($service_finder_options['label-qa'])) ? esc_html($service_finder_options['label-qa']) : esc_html__('Q&A', 'service-finder'); ?>
                </a></li>
              
              <li><a data-toggle="tab" href="#qa3">
                <?php echo (!empty($service_finder_options['label-ask-question'])) ? esc_html($service_finder_options['label-ask-question']) : esc_html__('Ask Question', 'service-finder'); ?>
                </a></li>
              <?php } ?>
              <?php if(service_finder_article_exist($author)){ ?>    
              <li class="<?php echo (!service_finder_get_data($service_finder_options,'review-system') && !service_finder_get_data($service_finder_options,'question-answer-section')) ? 'active' : ''; ?>"><a data-toggle="tab" href="#qa4">
                <?php echo (!empty($service_finder_options['label-articles'])) ? esc_html($service_finder_options['label-articles']) : esc_html__('Articles', 'service-finder'); ?>
                </a></li> 
              <?php } ?>   
            </ul>
            <div class="tab-content">
              <?php if($service_finder_options['review-system']){ ?>
              <div id="qa1" class="tab-pane fade in active">
                <div id="sf-provider-review">
                <?php require SERVICE_FINDER_BOOKING_TEMPLATES_DIR . '/comment-template.php'; ?>
                </div>
              </div>
              <?php } ?>
              <?php if(service_finder_get_data($service_finder_options,'question-answer-section')){ ?>
              <div id="qa2" class="tab-pane fade <?php echo (!$service_finder_options['review-system']) ? 'in active' : ''; ?>">
                <?php require SERVICE_FINDER_BOOKING_TEMPLATES_DIR . '/qa.php'; ?>
              </div>
              <div id="qa3" class="tab-pane fade">
                <?php require SERVICE_FINDER_BOOKING_TEMPLATES_DIR . '/ask-qa.php'; ?>
              </div>
              <?php } ?>
              <?php if(service_finder_article_exist($author)){ ?>  
              <div id="qa4" class="tab-pane fade class="<?php echo (!service_finder_get_data($service_finder_options,'review-system') && !service_finder_get_data($service_finder_options,'question-answer-section')) ? 'in active' : ''; ?>"">
                <?php require SERVICE_FINDER_BOOKING_TEMPLATES_DIR . '/articles.php'; ?>
              </div>
              <?php } ?>
            </div>
          </div>
        </div>  
        <?php } ?>
      </div>
      
      <!-- Left part END -->
      <!-- Right part start -->
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
        <h4>
          <?php echo esc_html($requestquote); ?>
        </h4>
        <div class="padding-20  margin-b-30  bg-white sf-rouned-box">
          <div class="form-quot-bx" id="form-quot-bx">
            <?php require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/get-quote/templates/get-quote.php'; ?>
          </div>
        </div>
        <?php } ?>
        <!-- Contact Provider form end-->
        <?php if(!is_user_logged_in()){ ?>
        <!-- Start login form in sidebar-->
        <h4>
          <?php esc_html_e('User Login', 'service-finder'); ?>
        </h4>
        <div class="padding-20  margin-b-30  bg-white sf-rouned-box">
          <div class="form-user-login">
            <form class="loginform" method="post">
              <div class="form-group">
                <label>
                <?php esc_html_e('User Name', 'service-finder'); ?>
                </label>
                <input name="login_user_name" type="text" class="form-control">
              </div>
              <div class="form-group">
                <label>
                <?php esc_html_e('Password', 'service-finder'); ?>
                </label>
                <input name="login_password" type="password" class="form-control">
              </div>
              <div class="form-group">
                <div class="checkbox sf-radio-checkbox">
                  <input type="checkbox" id="remember-me">
                  <label for="remember-me">
                  <?php esc_html_e('Remember me', 'service-finder'); ?>
                  </label>
                </div>
              </div>
              <div class="form-group">
                <input type="submit" class="btn btn-primary btn-block" name="user-login" value="<?php esc_html_e('Log in', 'service-finder'); ?>" />
              </div>
            </form>
          </div>
        </div>
        <!-- End login form in sidebar-->
        <?php } ?>
        <!-- Display Related Providers in sidebar start-->
        <?php if($service_finder_options['show-address-info'] && service_finder_check_address_info_access()){ ?>
		<?php 
		$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('Providers', 'service-finder');
		?>
        <h4>
          <?php echo (!empty($service_finder_options['label-related-provider'])) ? esc_html($service_finder_options['label-related-provider']) : esc_html__('Related', 'service-finder').' '.$providerreplacestring; ?>
        </h4>
        <div class="padding-20  margin-b-30  bg-white sf-rouned-box">
          <div class="recent-services-bx">
            <ul>
              <?php 
							$providers = service_finder_getRelatedProviders($providerInfo->wp_user_id,get_user_meta($providerInfo->wp_user_id,'primary_category',true),3);
							if(!empty($providers)){
								foreach($providers as $provider){
								$bookingurl = service_finder_get_author_url($provider->wp_user_id);
								$src = service_finder_get_avatar_by_userid($provider->wp_user_id,'service_finder-related-provider');
								
								if($src != ''){
									$imgtag = '<img src="'.esc_url($src).'" width="150" height="150" alt="">';
								}else{
									$imgtag = '<img src="'.esc_url($service_finder_Params['pluginImgUrl'].'/no_img.jpg').'" width="150" height="150" alt="">';
								}
								
								
									echo '<li><a href="'.esc_url($bookingurl).'">';	
									echo '<div class="post-thum-bx">';
									echo $imgtag;
									echo '</div>';
									echo '<div class="post-text-bx">';
									echo '<h6 class="post-title">'.$provider->full_name.'</h6>';
									echo '<p>'.$provider->address.'<br>'.$provider->city.'</p>';
									echo '</div>';
									echo '</a></li>';
								}
							}else{
								echo '<li>';
								echo sprintf( esc_html__('No Related %s Available', 'service-finder'), $providerreplacestring );
								echo '</li>';
							}
							?>
            </ul>
          </div>
        </div>
        <!-- Display Related Providers in sidebar end-->
        <?php } ?>
        <?php } ?>
      </div>
      <!-- Right part END -->
    </div>
  </div>
  <!-- Left & right section  END -->
  <?php } ?>
</div>
<!-- Left & right section END -->
<!-- Content END-->
<?php 
/*Author Page Style 1 END*/
elseif($service_finder_options['booking-page-style'] == 'style-2'): 
/*Author Page Style 2 Start*/
/*Display cover image if exist*/
if(!empty($userCap)){
if(in_array('cover-image',$userCap)){
	$coverimage = service_finder_getProviderAttachments($author,'cover-image');
	if(!empty($coverimage)){
		$src  = wp_get_attachment_image_src( $coverimage[0]->attachmentid, 'full' );
		$coverbanner  = $src[0];
		$coverclass = 'provider-cover-img';
	}
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
$bgcolor = (!empty($service_finder_options['inner-banner-bg-color'])) ? $service_finder_options['inner-banner-bg-color'] : '';
$bgopacity = (!empty($service_finder_options['inner-banner-opacity'])) ? $service_finder_options['inner-banner-opacity'] : '';
?>
<div class="page-content">
  <!-- inner page banner -->
  <!-- Start Search Form -->
	<?php //if($coverbanner != "") { ?>
	<div class="sf-search-benner sf-overlay-wrapper">
		<div class="banner-inner-row <?php echo esc_html($coverclass); ?>" style="background-image:url(<?php echo esc_url($coverbanner) ?>);">
			<?php //if($coverbanner != '') { ?>
			<div class="sf-overlay-main" style="opacity:<?php echo $bgopacity ?>; background-color:<?php echo $bgcolor ?>;"></div>
			<?php //} ?>
		</div>
	</div>  
	<?php //} ?>
  <!-- End Search Form -->
  <!-- inner page banner END -->
  <?php //require SERVICE_FINDER_BOOKING_FRONTEND_DIR . '/breadcrumb.php'; //Breadcrumb ?>
  <?php
  if($providerInfo->account_blocked == 'yes' || !service_finder_check_profile_after_trial_expire($author) || $providerInfo->admin_moderation == 'pending' || ($restrictuserarea && $identitycheck && $providerInfo->identity != 'approved')) {
  require SERVICE_FINDER_BOOKING_FRONTEND_DIR . '/blocked-profile.php';
  } else {
  ?>
  <!-- About info -->
  <section class="section-full bg-white about-info" id="sf-provider-info">
	  <div class="provider-profile-container">
		  <div class="container-fluid">
			  <div class="row">
				  <div class="col-lg-4">
					  <div class="profile-card">
						  <a href="https://wediscover.solstium.net/my-account"><i class="fa fa-ellipsis-v more-info"></i></a>
						  <?php
							$profilethumb = service_finder_get_avatar_by_userid($author,'service_finder-provider-medium');
	  						$user_profiles_get = get_userdata($author);
							if($profilethumb != '') {
							$imgtag = '<div class="profile-img" style="background-image: url('.esc_url($profilethumb).')"></div>';
							} else {
							$imgtag = '<div class="profile-img"></div>';
							}
							echo $imgtag;
						  ?>
						 
						  <h2><?php echo $user_profiles_get->user_login; ?></h2>
						  <?php
							$primarycatid = get_user_meta($providerInfo->wp_user_id,'primary_category',true);
							$categories = $providerInfo->category_id;
							if($categories != '')
							{
							$cats = explode(',',$categories);
							$displaycat = array();
							if(!empty($cats)){
								foreach($cats as $catid) {
									if($primarycatid == $catid){
										$displaycat[] = '<h5 class="sub-text">'.service_finder_getCategoryName($catid).'</h5>';	
									}else{
										$displaycat[] = '<h5 class="sub-text">'.service_finder_getCategoryName($catid).'</h5>';	
									}

								}
							} 
							echo implode(', ',$displaycat);		
							}
							?>
						  <?php if($providerInfo->country != "") { ?>
							  <div class="location">
								  <i class="fa fa-map-marker"></i> <?php echo $providerInfo->country; ?>
							  </div>
						  <?php } ?>
						  <?php if($service_finder_options['add-to-fav']) { ?>
							  <?php if(is_user_logged_in()) {
								  $myfav = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->favorites.' where user_id = %d AND provider_id = %d',$current_user->ID, $author));
								  if(!empty($myfav)){
									  echo '<a href="javascript:void(0);" class="remove-favorite" data-proid="'.esc_attr($author).'" data-userid="'.esc_attr($current_user->ID).'"><i class="fa fa-heart"></i>Unfollow</a>';
								  }else{
									  echo '<a href="javascript:void(0);" class="add-favorite" data-proid="'.esc_attr($author).'" data-userid="'.esc_attr($current_user->ID).'"><i class="fa fa-heart"></i>Follow</a>';
								  }
							  } else {
								  echo '<a href="javascript:void(0);" data-action="login" data-redirect="no" data-toggle="modal" data-target="#login-Modal">'.esc_html__('+ Follow', 'service-finder').'</a>';
							  }
							  ?>
						  <?php } ?>
						  <div class="follower-wrap">
							  <div class="follower">
								  <div class="left">Follower</div>
								  <?php
	  								$get_all_followers = $wpdb->get_results("SELECT * FROM $service_finder_Tables->favorites WHERE provider_id = '$author'");
								  	$totalfollowers = count($get_all_followers);
							 	echo '<div class="follower-ct">'. $totalfollowers .'</div>'; ?>
							  </div>
						  </div>
						  <div class="follower">
							  <div class="left">Total Meetup</div>
							  <?php 
								  $allbookings = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' where provider_id = %d', $author));
								  $totalbookings = count($allbookings);
	                          ?>
							  <div class="booking-ct"><?php echo $totalbookings; ?></div>
						  </div>
						   <?php 
	  						if($providerInfo->bio != "") { ?>
							  <div class="about-us">
								 <h6>About us</h6> 
								  <?php echo apply_filters('the_content', $providerInfo->bio); ?>
							  </div>
						  <?php } ?>
					  </div>
				  </div>
				  <div class="col-lg-8">
					  <div class="sf-provi-laexce-box margin-b-50">
						  <div class="sf-custom-tabs sf-custom-new">
							  <ul class="nav nav-tabs nav-table-cell font-20">
								  <li class="active"><a data-toggle="tab" href="#tab-meetup" aria-expanded="true">Meetup </a></li>
								  <li class=""><a data-toggle="tab" href="#tab-product" aria-expanded="false">Product </a></li>
							  </ul>

							  <div class="tab-content">
								  <div id="tab-meetup" class="tab-pane active">
									  <div class="sf-meetup-tab">
										  <?php //echo do_shortcode('[elementor-template id="5437"]'); ?>
										  <div class="row">

											  <div class="col-lg-6 col-md-6">
												  <div class="meetup-card">
													  <div class="meetup-img" style="background-image: url('https://wediscover.solstium.net/wp-content/uploads/2022/12/Group-90408-768x492.png')">
														  <a href="#"><i class="fas fa-share-alt shared-icon"></i></a>
													  </div>
													  <div class="card-content">
														  <h4>
															  Lorem Ipsum meeting title
														  </h4>
														  <p>
															  Travel
														  </p>
														  <div class="meet-wrap">
															  <div class="meet-up-description">
																  <i class="fa fa-calendar"></i> <div class="date">August 12, 2022 at 12:00 P.M
																  </div>
																  <div class="right">
																	<i class="fa fa-user"></i><div class="bookings">700 Booking</div>  
																  </div>																 
															  </div>
														  </div>
														  <a href="#" class="meetup-remainder">Set Remainder</a>
													  </div>
												  </div>
											  </div>
										  </div>
									  </div>
								  </div>
								  <div id="tab-product" class="tab-pane">
									  <div class="sf-product-tab">
										  <?php // echo do_shortcode('[elementor-template id="5612"]'); ?>
		
										  
	  									<?php 
	  										$current_user_products = wc_get_products( array(
												'status'    => 'publish',
												'limit'     => -1,
												'author'    => $author //author ID
											));
	  									$product_ids = array();
	  									foreach($current_user_products as $current_user_product) {
											$product_ids[] = $current_user_product->get_id();
										}
	  									$product_ids = implode(',',$product_ids); ?>
										  <div class="custom-shop-card">
											  	<?php  echo do_shortcode('[products ids="'.$product_ids.'"]'); ?>
										  </div>
									  </div>
								  </div>
							  </div>
						  </div>
					  </div>
				  </div>
			  </div>
		  </div>
	  </div>
	</section>
  <!-- About info END -->
  <?php } ?>
</div>
<?php endif; ?>
<?php
if(is_user_logged_in()){
	if(service_finder_getUserRole($current_user->ID) == 'Customer'){
	require SERVICE_FINDER_BOOKING_LIB_DIR . '/invite-job.php';
	}
}
?>
<?php
$floatingmenudesktop = (!empty($service_finder_options['floating-menu-desktop'])) ? $service_finder_options['floating-menu-desktop'] : '';
$floatingmenumobile = (!empty($service_finder_options['floating-menu-mobile'])) ? $service_finder_options['floating-menu-mobile'] : '';
$desktopclass = ($floatingmenudesktop) ? 'sf-floating-desktop-menu' : '';
$mobileclass = ($floatingmenumobile) ? 'sf-floating-mobile-menu' : '';
?>
<div class="sf-scroll-nav <?php echo sanitize_html_class($desktopclass); ?> <?php echo sanitize_html_class($mobileclass); ?>" >
<ul>

    <li>

        <a href="#sf-provider-info" class="active">

            <span><i class="fa fa-user"></i></span>

            <strong><?php echo (!empty($service_finder_options['label-about-info'])) ? $service_finder_options['label-about-info'] : esc_html__('About info', 'service-finder'); ?></strong>

        </a>

    </li>

    <li>

        <a href="#sf-provider-gallery">

            <span><i class="fa fa-picture-o"></i></span>

            <strong><?php echo (!empty($service_finder_options['label-gallery'])) ? $service_finder_options['label-gallery'] : esc_html__('Gallery', 'service-finder'); ?></strong>

        </a>

    </li>

    <li>

        <a href="#sf-provider-hours">

            <span><i class="fa fa-clock-o"></i></span>

            <strong><?php echo (!empty($service_finder_options['label-business-hours'])) ? $service_finder_options['label-business-hours'] : esc_html__('Business hours', 'service-finder'); ?></strong>

        </a>

    </li>

    <li>

        <a href="#sf-provider-address">

            <span><i class="fa fa-book"></i></span>

            <strong><?php echo (!empty($service_finder_options['label-address-info'])) ? $service_finder_options['label-address-info'] : esc_html__('Address info', 'service-finder'); ?></strong>

        </a>

    </li>

    <li>

        <a href="#sf-provider-map">

            <span><i class="fa fa-map-marker"></i></span>

            <strong><?php echo (!empty($service_finder_options['label-map'])) ? $service_finder_options['label-map'] : esc_html__('Map', 'service-finder'); ?></strong>

        </a>

    </li>

    <li>

        <a href="#sf-provider-video">

            <span><i class="fa fa-video-camera"></i></span>

            <strong><?php echo (!empty($service_finder_options['label-video'])) ? $service_finder_options['label-video'] : esc_html__('Video', 'service-finder'); ?></strong>

        </a>

    </li>

    <li>

        <a href="#sf-provider-services">

            <span><i class="fa fa-gear"></i></span>

            <strong><?php echo (!empty($service_finder_options['label-services'])) ? $service_finder_options['label-services'] : esc_html__('Services', 'service-finder'); ?></strong>

        </a>

    </li>

    <li>

        <a href="#book-now-section">

            <span><i class="fa fa-money"></i></span>

            <strong><?php echo (!empty($service_finder_options['label-booking-form'])) ? $service_finder_options['label-booking-form'] : esc_html__('Booking form', 'service-finder'); ?></strong>

        </a>

    </li>

    <li>

        <a href="#sf-provider-review">

            <span><i class="fa fa-star"></i></span>

            <strong><?php echo (!empty($service_finder_options['label-review'])) ? $service_finder_options['label-review'] : esc_html__('Review', 'service-finder'); ?></strong>

        </a>

    </li>

</ul>
</div>
<?php
get_footer();
