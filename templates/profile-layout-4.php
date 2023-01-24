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
		$src  = wp_get_attachment_image_src( $coverimage[0]->attachmentid, 'full' );
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
	<div class="page-content bg-white">
        
        <!-- inner page banner -->
		<?php if(!service_finder_get_data($service_finder_options,'profile-search-bar') || $coverbanner != ""){ ?>
        <div class="sf-search-benner sf-overlay-wrapper">
            <div class="banner-inner-row provider-cover-img <?php echo esc_attr($coverclass); ?>" style="background-image:url(<?php echo esc_url($coverbanner); ?>);">
            <?php if($coverbanner != ''){ ?>
            <div class="sf-overlay-main" style="opacity:<?php echo service_finder_get_data($service_finder_options,'inner-banner-opacity'); ?>; background-color:<?php echo service_finder_get_data($service_finder_options,'inner-banner-bg-color'); ?>;"></div>
            <?php } ?>
            <div class="sf-banner-heading-wrap">
                <div class="sf-banner-heading-area">
                    <div class="sf-banner-heading-large"><?php echo service_finder_getCompanyName($providerInfo->wp_user_id) ?></div>
                    <?php require SERVICE_FINDER_BOOKING_FRONTEND_DIR . '/breadcrumb.php'; //Breadcrumb ?>
                    </div>
            </div>
            </div>
            <?php if(!service_finder_get_data($service_finder_options,'profile-search-bar')){ 
            $srhposition = service_finder_get_data($service_finder_options,'search-bar-position-profilepage','bottom');
            if($srhposition == 'middle'){
                $positionclass = 'pos-v-center';
            }else{
                $positionclass = 'pos-v-bottom';
            }
            ?>
            <div class="sf-search-bar-warp <?php echo sanitize_html_class($positionclass); ?>">
                <div class="container">
                    <?php $advanceclass = (service_finder_check_advance_search()) ? '' : 'sf-empty-radius'; ?>
                    <div class="sf-searchbar-table-wrap search-form <?php echo sanitize_html_class($advanceclass); ?>">
                        <?php echo do_shortcode('[service_finder_search_form]'); ?>
                    </div>
                </div>
            </div>
            <?php } ?>
       </div>
        <?php } ?>
        <!-- inner page banner END -->   

        <?php
		if($providerInfo->account_blocked == 'yes' || !service_finder_check_profile_after_trial_expire($author) || $providerInfo->admin_moderation == 'pending' || ($restrictuserarea && $identitycheck && $providerInfo->identity != 'approved')){
		  require SERVICE_FINDER_BOOKING_FRONTEND_DIR . '/blocked-profile.php';
		}else{
		?>
        
        <!-- Page Scroll Nav -->
        <section class="sf-page-scroll-wrap">
            <div class="container">
                <div class="sf-page-scroll-nav clearfix">                                                                 
                    <ul class="clearfix">
                        <li><a href="#sf-provider-info"><?php echo service_finder_get_data($service_finder_options,'label-about-info',esc_html__('About', 'service-finder')); ?></a></li>
                        <?php 
						$images = service_finder_getProviderAttachments($providerInfo->wp_user_id,'gallery'); 
						if(!empty($images)){
						?>
                        <li><a href="#sf-provider-gallery"><?php echo service_finder_get_data($service_finder_options,'label-gallery',esc_html__('Gallery', 'service-finder')); ?></a></li>
                        <?php } ?>
                        <?php if((service_finder_get_data($service_finder_options,'show-contact-map') && service_finder_show_map_on_site()) || service_finder_get_data($service_finder_options,'show-contact-info') || service_finder_check_business_hours_status($author)){ ?>
                        <li><a href="#sf-provider-address"><?php echo service_finder_get_data($service_finder_options,'label-address-info',esc_html__('Contact', 'service-finder')); ?></a></li>
                        <?php } ?>
                        <?php if($providerInfo->embeded_code != ''){ ?>
                        <li><a href="#sf-provider-video"><?php echo service_finder_get_data($service_finder_options,'label-video',esc_html__('Videos', 'service-finder')); ?></a></li>
                        <?php } ?>
                        <?php
						$attachmentIDs = service_finder_getDocuments($providerInfo->wp_user_id);
						$services = service_finder_getAllServices($author);
						if((!empty($services) && service_finder_get_data($service_finder_options,'my-services-menu')) || !empty($attachmentIDs)){
						?>
                        <li><a href="#sf-provider-services"><?php echo service_finder_get_data($service_finder_options,'label-services',esc_html__('Services', 'service-finder')); ?></a></li>
                        <?php } ?>
                        <?php
						if(service_finder_user_has_capability('bookings',$author))
						{
						if($settings['booking_process'] == 'on' && (!is_user_logged_in() || service_finder_getUserRole($current_user->ID) == 'administrator' || service_finder_getUserRole($current_user->ID) == 'Customer' || (service_finder_getUserRole($current_user->ID) == 'Provider' && $current_user->ID == $author) ))
						{
						?>
                        <li><a href="#book-now-section"><?php echo service_finder_get_data($service_finder_options,'label-booking-form',esc_html__('Booking', 'service-finder')); ?></a></li>
                        <?php } } ?>
                        <?php
						if(service_finder_get_data($service_finder_options,'review-system')){
						?>
                        <li><a href="#sf-provider-review"><?php echo service_finder_get_data($service_finder_options,'label-review',esc_html__('Review', 'service-finder')); ?></a></li>
                        <?php } ?>
                    </ul>
                </div>                
            </div>
        </section>  
        <!-- Page Scroll Nav End -->
        
        <!-- Company About -->
        <section class="section-full bg-white sf-company-about-info" id="sf-provider-info">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="sf-company-abou-left" style="padding-right:0px;">
                            <div class="sf-company-about-right">
                                <div class="sf-feaProgrid-wrap clearfix">
                                <?php
                                if(service_finder_is_featured($providerInfo->wp_user_id)){
                                ?>
                                <div class="sf-feaProgrid-label"><?php esc_html_e('Featured', 'service-finder'); ?></div>
                                <?php
                                }
                                ?>
                                <?php
                                $profilethumb = service_finder_get_avatar_by_userid($author,'service_finder-provider-medium');
                                ?>
                                <div class="sf-feaProgrid-pic" style="background-image:url(<?php echo esc_url($profilethumb); ?>);">
                                    <div class="sf-feaProgrid-info">
                                    <?php echo service_finder_displayRating(service_finder_getAverageRating($author)); ?>
                                    <h4 class="sf-feaProgrid-title"><?php echo service_finder_getProviderFullName($author); ?></h4>
                                    <?php if(service_finder_get_data($service_finder_options,'show-address-info') && service_finder_check_address_info_access()){ ?>	
                                    <?php if(service_finder_getAddress($author) != "" && service_finder_get_data($service_finder_options,'show-postal-address')){ ?>
                                    <div class="sf-feaProgrid-address"><?php echo service_finder_getAddress($author); ?></div>
                                    <?php } ?>
                                    <?php } ?>
                                </div>
                                    <div class="sf-overlay-box"></div>
                                    <?php
                                    if(service_finder_is_varified_user($author)){
                                    ?>
                                    <span class="sf-featured-approve">
                                        <i class="fa fa-check"></i><span><?php esc_html_e('Verified Provider', 'service-finder'); ?></span>
                                    </span>
                                    <?php } ?>
                                </div>
                                
                                <div class="sf-feaProgrid-iconwrap">
                                    <?php
									if(service_finder_get_data($service_finder_options,'my-services-menu')){
									?>
                                    <span class="sf-feaProgrid-icon sfp-yellow sf-services-slider-btn" data-providerid="<?php echo esc_attr($author); ?>"><span class="sf-feaPro-tooltip"><?php echo esc_html__('Display Services','service-finder'); ?></span><i class="sl-icon-settings"></i></span>
                                    <?php } ?>
                                    <?php
									if(service_finder_get_data($service_finder_options,'review-system')){
									?>
                                    <span class="sf-feaProgrid-icon sfp-perple"><span class="sf-feaPro-tooltip"><?php echo sprintf(_n( '%d Comment', '%d Comments', service_finder_get_total_reviews($author), 'service-finder' ),service_finder_get_total_reviews($author)); ?></span><i class="sl-icon-speech"></i></span>
                                    <?php } ?>
                                    <?php
                                    $requestquote = service_finder_get_data($service_finder_options,'requestquote-replace-string');
                            
                                    if(service_finder_get_data($service_finder_options,'request-quote') && service_finder_request_quote_for_loggedin_user()){
                                    echo '<span class="sf-feaProgrid-icon sfp-green" data-providerid="'.$author.'" data-tool="tooltip" data-toggle="modal" data-target="#quotes-Modal"><span class="sf-feaPro-tooltip">'.esc_html__('Request Quote','service-finder').'</span><i class="sl-icon-doc"></i></span>';
                                    }
                                    ?>
                                    <?php if(service_finder_get_data($service_finder_options,'add-to-fav')){ ?>
                                    <?php
                                    if(is_user_logged_in()){
                                        $myfav = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->favorites.' where user_id = %d AND provider_id = %d',$current_user->ID,$author));
                                        if(!empty($myfav)){
                                        echo '<span id="favproid-'.esc_attr($author).'" class="sf-feaProgrid-icon sfp-blue removefromfavorite" data-proid="'.esc_attr($author).'" data-userid="'.esc_attr($current_user->ID).'"><span class="sf-feaPro-tooltip">'.esc_html__('My Favorite', 'service-finder').'</span><i class="fa fa-heart"></i></span>';
                                        }else{
                                        echo '<span id="favproid-'.esc_attr($author).'" class="sf-feaProgrid-icon sfp-blue addtofavorite" data-proid="'.esc_attr($author).'" data-userid="'.esc_attr($current_user->ID).'"><span class="sf-feaPro-tooltip">'.esc_html__('Add to Favorites', 'service-finder').'</span><i class="sl-icon-heart"></i></span>';
                                        }
                                    }else{
                                        echo '<span id="favproid-'.esc_attr($author).'" class="sf-feaProgrid-icon sfp-blue" data-action="login" data-redirect="no" data-toggle="modal" data-target="#login-Modal"><span class="sf-feaPro-tooltip">'.esc_html__('Add to Favorites', 'service-finder').'</span><i class="sl-icon-heart"></i></span>';
                                    }
                                    ?>
                                    <?php } ?>
                                </div>
                                </div>
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
                            <h2><?php echo service_finder_getCompanyName($providerInfo->wp_user_id) ?></h2>   
                            <div class="sf-company-about-table sf-comAbou-two">
                                <div class="sf-company-about-cell"><?php echo (!empty($providerInfo->tagline)) ? $providerInfo->tagline : service_finder_default_tagline(); ?></div>
                                <div class="sf-company-about-cell"><strong><?php esc_html_e('Categories', 'service-finder'); ?>:</strong>
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
                            </div>
                            <div class="sf-company-about-texts">
                            <?php 
							if($providerInfo->bio != ""){
								echo apply_filters('the_content', $providerInfo->bio);
							}
							?>
                            </div> 
                            
                            <?php if(service_finder_get_data($service_finder_options,'social-media') && ($providerInfo->facebook != "" || $providerInfo->twitter != "" || $providerInfo->linkedin != "" || $providerInfo->digg != "" || $providerInfo->pinterest != "" || $providerInfo->instagram != "")){ ?>
                            <div class="social-share-icon">
                                <div class="social-share-cell">
                                    <strong><?php esc_html_e('Share with', 'service-finder'); ?></strong>
                                </div>
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
                    </div>
                    <div class="sf-company-need-btn clear col-md-12">
                        <?php 
						if(get_user_meta($author,'claimbusiness',true) == 'enable' && get_user_meta($author,'claimed',true) != 'yes'){
					    echo '<a href="javascript:;" data-toggle="modal" data-target="#claimbusiness-Modal" class="claimbusiness btn btn-primary" data-proid="'.esc_attr($author).'">'.service_finder_get_data($service_finder_options,'string-claim-business').'</a>';
					    }
						?>
                        <?php
						if(class_exists('WP_Job_Manager') && $inviteforjob){
							if(is_user_logged_in()){
								if(service_finder_getUserRole($current_user->ID) == 'Customer'){
								echo '<a href="javascript:;" class="btn btn-custom" data-action="invite" data-redirect="no" data-toggle="modal" data-target="#invite-job"><i class="fa fa-briefcase"></i>'.esc_html__('Invite for Job', 'service-finder').'</a>';
								}
							}else{
								echo '<a href="javascript:;" class="btn btn-custom" data-action="login" data-redirect="no" data-toggle="modal" data-target="#login-Modal"><i class="fa fa-briefcase"></i>'.esc_html__('Invite for Job', 'service-finder').'</a>';
							}
              			}
						?>
                        <?php 
						if(service_finder_get_data($service_finder_options,'request-quote') && service_finder_request_quote_for_loggedin_user()){ 
						?>
                        <a href="javascript:void(0);" class="btn btn-custom" data-providerid="<?php echo esc_attr($author);?>" data-toggle="modal" data-target="#quotes-Modal"><i class="fa fa-file-text-o"></i><?php echo service_finder_get_data($service_finder_options,'requestquote-replace-string',esc_html__('Request a Quote', 'service-finder')); ?></a>
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
            </div>
            <div class="sf-curve-topWrap"><div class="sf-curveTop sf-aboutInfo-curveTop" style="background-color:<?php echo esc_attr($curveleftcolor); ?>"></div></div>
            <div class="sf-curve-botWrap"><div class="sf-curveBot sf-aboutInfo-curveBot" style="background-color:<?php echo esc_attr($curverightcolor); ?>"></div></div>
        </section>  
        <!-- Company Abou End -->  
        
        <!-- Galley Section -->
        <?php 
		$images = service_finder_getProviderAttachments($providerInfo->wp_user_id,'gallery'); 
		if(!empty($images)){
		$custombgcolor = (service_finder_theme_get_data($service_finder_options,'profile-gallery-bg-color') != '') ? 'background-color:'.service_finder_theme_get_data($service_finder_options,'profile-gallery-bg-color').'; ' : '';
		?>
        <section class="section-full sf-vender-gallery-wrap bg-primary profile-gallery-third" id="sf-provider-gallery" style=" <?php echo esc_attr($custombgcolor);?>">
            <div class="container"> 
            	<div class="section-head text-center">
                    <h2 class="text-white"><?php echo service_finder_get_data($service_finder_options,'label-photo-gallery',esc_html__('Photo Gallery', 'service-finder'));  ?></h2>
                </div>
                    
                <div class="section-content">
                    <ul class="sf-venderGallery-wrap mfp-gallery">
                        <?php
						if(!empty($images)){
							$i = 0;
							foreach($images as $image){
								$src  = wp_get_attachment_image_src( $image->attachmentid, 'service_finder-gallery-thumb-v1' );
								$fullsrc  = wp_get_attachment_image_src( $image->attachmentid, 'full' );
								$src  = $src[0];
								$fullsrc  = $fullsrc[0];
								if($i%2 == 0 && $i != 0)
								{
									$wideclass = 'sf-venGal-wide';
								}else
								{
									$wideclass = '';
								}
								?>
                                <li class="<?php echo sanitize_html_class($wideclass); ?>" style="display:<?php echo ($i > 5) ? 'none' : ''; ?>">
                                    <div class="sf-venderGallery-box" style="background-image:url(<?php echo esc_url($fullsrc); ?>)">
                                        <div class="sf-vendGallery-overlay">
                                        <a class="mfp-link2 mfp-video video-play-btn" title="" href="<?php echo esc_url($fullsrc); ?>">
                                        <i class="sl-icon-magnifier"></i>
                                        </a>
                                        </div>
                                    </div>
                                </li>
								<?php	
								$i++;
							}
						}
						?>
                    </ul>
                    <div class="sf-viewallgal-btn-wrap">
                    <a class="sfviewallgallery bnt btn-link" href="javascript:;">
					<i class="fa fa-eye"></i> <?php echo esc_html__( 'View All', 'service-finder' ); ?>
                    </a>
                    </div>
                </div>
            </div>
            <div class="sf-curve-topWrap"><div class="sf-curveTop sf-vendGallery-curveTop" style="background-color:<?php echo esc_attr($curveleftcolor); ?>"></div></div>
            <div class="sf-curve-botWrap"><div class="sf-curveBot sf-vendGallery-curveBot" style="background-color:<?php echo esc_attr($curverightcolor); ?>"></div></div>            
        </section> 
        <?php
        }
		?>
        <!-- Galley Section END -->  
        
        <!-- Contact Information --> 
        <?php if((service_finder_get_data($service_finder_options,'show-contact-map') && service_finder_show_map_on_site()) || service_finder_get_data($service_finder_options,'show-contact-info') || service_finder_check_business_hours_status($author)){ ?> 
        <section class="section-full bg-white sf-venContInfo-wrap" id="sf-provider-address">
            <div class="container">
            	<?php if(service_finder_get_data($service_finder_options,'show-contact-info')){ ?>
            	<div class="section-head text-center">
                    <h2><?php esc_html_e('Contact Information', 'service-finder'); ?></h2>
                </div>
                <?php } ?>    
                <div class="section-content">
                    <div class="row">
                        <?php if(service_finder_check_business_hours_status($author)){ ?>
                        <div class="col-md-6">
                        
                          <div class="sf-provider-business-hour margin-b-30">
                          
                              <div class="sf-list-business-hours">
                                <h3 class="margin-t-0"><?php echo service_finder_get_data($service_finder_options,'label-public-business-hours',esc_html__('Business Hours', 'service-finder')) ?></h3>
                                <div class="sf-border-icon">
                                	<div class="sf-icon-sm "><i class="sl-icon-calender sf-icon-purple"></i></div>
                                </div>
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
                          
                        </div>
                        <?php } ?>
                        <?php if(service_finder_get_data($service_finder_options,'show-contact-info')){ ?>
                        <?php if(service_finder_get_data($service_finder_options,'show-contact-map') && service_finder_show_map_on_site()){ ?>
                        <div class="col-md-6">
                          	<div class="sf-provider-business-map margin-b-30">
                                <div class="sf-provider-business-map-inner">
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
                        </div>
                        <?php } ?>
                        <?php } ?>

                    </div>
                    
                    <?php if(service_finder_get_data($service_finder_options,'show-contact-info')){ ?>
                    <div class="row equal-col-outer">
                    	<?php if(service_finder_get_data($service_finder_options,'show-address-info') && service_finder_check_address_info_access()){ ?>	
                        <?php if(service_finder_getAddress($author) != "" && service_finder_get_data($service_finder_options,'show-postal-address')){ ?>
                        <div class="col-md-4 col-sm-6 equal-col margin-b-30">
                            <div class="sf-icon-box left clearfix">
                                <div class="sf-border-icon">
                                    <div class="sf-icon-sm "><i class="sl-icon-location sf-icon-yellow"></i></div>
                                </div>
                                <div class="sf-icon-content">
                                    <div class="sf-icon-title"><?php esc_html_e('Address', 'service-finder'); ?></div>
                                    <div class="sf-icon-detail">
                                        <?php echo service_finder_getAddress($author); ?>
                                    </div>
                                </div>                            	
                            </div>
                        </div>
						<?php } ?>
                        <?php } ?>
                        
                        <?php if(service_finder_get_contact_info_with_text($providerInfo->phone,$providerInfo->mobile) != "" && service_finder_contact_number_is_accessible($author)){ ?>
                        <div class="col-md-4 col-sm-6 equal-col margin-b-30">
                            <div class="sf-icon-box left clearfix margin-b-20">
                                <div class="sf-border-icon">
                                    <div class="sf-icon-sm "><i class="sl-icon-phone sf-icon-purple"></i></div>
                                </div>
                                <div class="sf-icon-content">
                                    <div class="sf-icon-title"><?php esc_html_e('Telephone', 'service-finder'); ?></div>
                                    <div class="sf-icon-detail">
                                        <?php echo service_finder_get_contact_info_with_text($providerInfo->phone,$providerInfo->mobile); ?>
                                    </div>
                                </div>                            	
                            </div>
                        </div>
                        <?php } ?>
                        
                        <?php if(service_finder_get_data($service_finder_options,'show-email-address')){ ?>
                        <div class="col-md-4 col-sm-6 equal-col margin-b-30">
                            <div class="sf-icon-box left clearfix margin-b-20">
                                <div class="sf-border-icon">
                                    <div class="sf-icon-sm "><i class="sl-icon-envolope sf-icon-sky-blue"></i></div>
                                </div>
                                <div class="sf-icon-content">
                                    <div class="sf-icon-title"><?php echo esc_html__( 'Email', 'service-finder' ) ?></div>
                                    <div class="sf-icon-detail">
                                        <p><a href="mailto:<?php the_author_meta( 'user_email', $author ); ?>"><?php the_author_meta( 'user_email', $author ); ?></a></p>
                                    </div>
                                </div>                            	
                            </div>
                        </div>
                        <?php } ?>
                        
                        <?php if($providerInfo->website != "" && service_finder_get_data($service_finder_options,'show-website')){ ?>
                        <div class="col-md-4 col-sm-6 equal-col margin-b-30">
                            <div class="sf-icon-box left clearfix margin-b-20">
                                <div class="sf-border-icon">
                                    <div class="sf-icon-sm "><i class="sl-icon-globe sf-icon-red"></i></div>
                                </div>
                                <div class="sf-icon-content">
                                    <div class="sf-icon-title"><?php echo esc_html__( 'Web', 'service-finder' ) ?></div>
                                    <div class="sf-icon-detail">
                                        <p><a href="<?php echo service_finder_addhttp(esc_html($providerInfo->website)); ?>" target="_blank"><?php echo esc_html($providerInfo->website); ?></a></p>
                                    </div>
                                </div>                            	
                            </div>
                        </div>
                        <?php } ?>
                        <?php if($providerInfo->skypeid != "" && service_finder_get_data($service_finder_options,'show-skype')){ ?>
                        <div class="col-md-4 col-sm-6 equal-col margin-b-30">
                            <div class="sf-icon-box left clearfix margin-b-20">
                                <div class="sf-border-icon">
                                    <div class="sf-icon-sm "><i class="fa fa-skype sf-icon-blue"></i></div>
                                </div>
                                <div class="sf-icon-content">
                                    <div class="sf-icon-title"><?php echo esc_html__( 'Skype', 'service-finder' ) ?></div>
                                    <div class="sf-icon-detail">
                                        <p><a href="skype:<?php echo esc_html($providerInfo->skypeid); ?>?chat"><?php echo esc_html($providerInfo->skypeid); ?></a></p>
                                    </div>
                                </div>                            	
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <?php } ?>
                </div>
                
            </div>
            <div class="sf-curve-topWrap"><div class="sf-curveTop sf-venContInfo-curveTop" style="background-color:<?php echo esc_attr($curveleftcolor); ?>"></div></div>
            <div class="sf-curve-botWrap"><div class="sf-curveBot sf-venContInfo-curveBot" style="background-color:<?php echo esc_attr($curverightcolor); ?>"></div></div>            
        </section>
        <?php } ?>
        <!-- Contact Information END -->        
        
        <?php
		$languages = service_finder_get_languages($author);
		$experiences = service_finder_get_experience($author);            
		$certificates = service_finder_get_certificates($author);
		$qualifications = service_finder_get_qualifications($author);
		$amenities = service_finder_get_amenities($author);
		if(!empty($languages) || !empty($experiences) || !empty($certificates) || !empty($qualifications) || !empty($amenities))
		{
		?>
		<section class="section-full bg-gray sf-vender-bio-wrap">
			<div class="container">
            <div class="section-content">
			<div class="sf-custom-accordion" id="accordion1">
				<?php
				if(!empty($languages))
				{
				$languagearray = service_finder_get_alllanguages();
				?>
				<div class="panel">
					<div class="acod-head">
						 <h5 class="acod-title">
							<a data-toggle="collapse" href="#collapseOne1" data-parent="#accordion1" aria-expanded="ture">
							<?php echo esc_html__( 'Language', 'service-finder' ) ?>
							<span class="indicator"><i class="fa fa-plus"></i></span>
							</a>
						 </h5>
					</div>
					<div id="collapseOne1" class="acod-body collapse in">
						<div class="acod-content">
							<ul class="sf-languages-list clearfix">
								<?php
								foreach($languages as $language){
									$flagimgsrc = SERVICE_FINDER_BOOKING_IMAGE_URL.'/flags/'.$language.'.png';
									echo '<li><img src="'.$flagimgsrc.'" alt=""> '.esc_html($languagearray[$language]).'</li>';
								}
								?>
							</ul>
						</div>
					</div>
				</div>	
				<?php	
				}
				?>
				  
				<?php
				if(!empty($experiences))
				{
				?>
				<div class="panel">
					<div class="acod-head">
						 <h5 class="acod-title">
							<a data-toggle="collapse" href="#collapseTwo1" class="collapsed" data-parent="#accordion1" aria-expanded="false">
							<?php echo esc_html__( 'Experience', 'service-finder' ) ?> 
							<span class="indicator"><i class="fa fa-plus"></i></span>
							</a>
						 </h5>
					</div>
					<div id="collapseTwo1" class="acod-body collapse">
						<div class="acod-content">
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
				</div>
				<?php
				}
				?>                
								   
				<?php
				if(!empty($certificates))
				{
				?>
				<div class="panel">
					<div class="acod-head">
					 <h5 class="acod-title">
						<a data-toggle="collapse"  href="#collapseThree1" class="collapsed"  data-parent="#accordion1"aria-expanded="false">
						<?php echo esc_html__( 'Certificates & Awards', 'service-finder' ) ?>
						<span class="indicator"><i class="fa fa-plus"></i></span>
						</a>
					 </h5>
					</div>
					<div id="collapseThree1" class="acod-body collapse">
						<div class="acod-content">
							<ul class="sf-certificates-list">
							<?php
							$fileicon = new SERVICE_FINDER_ImageSpace();
							foreach($certificates as $certificate)
							{
							if($certificate->attachment_id != '' && $certificate->attachment_id > 0)
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
							?>
							<li>
								<?php if($src != ''){ ?>
								<div class="awards-pic">
                                <img src="<?php echo esc_url($src); ?>" alt="">
                                <a class="sf-download-certificate" href="<?php echo SERVICE_FINDER_BOOKING_LIB_URL.'/downloads.php?file='.wp_get_attachment_url( $certificate->attachment_id ) ?>"><i class="fa fa-download"></i> <?php echo esc_html__('View/Download'); ?></a>
                                </div>
								<?php } ?>
								<span class="awards-title"><?php echo esc_html($certificate->certificate_title); ?></span>
								<span class="awards-date"><i class="fa fa-clock-o"></i> <?php echo date('M d Y',strtotime($certificate->issue_date)); ?></span>
								<div class="awards-text"><?php printf($certificate->description); ?></div>
							</li>
							<?php
							}
							?>
						</ul>
						</div>
					</div>
				</div>
				<?php
				}
				?>
				
				<?php
				if(!empty($qualifications))
				{
				?>
				<div class="panel">
					<div class="acod-head">
					 <h5 class="acod-title">
						<a data-toggle="collapse"  href="#collapseFour1" class="collapsed"  data-parent="#accordion1"aria-expanded="false">
						<?php echo esc_html__( 'Qualification', 'service-finder' ) ?>
						<span class="indicator"><i class="fa fa-plus"></i></span>
						</a>
					 </h5>
					</div>
					<div id="collapseFour1" class="acod-body collapse">
						<div class="acod-content">
							<div class="sf-qualification-acord" id="qualification-acord">
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
				</div>
				<?php
				}
				?>
				
				<?php
				if(!empty($amenities))
				{
				?>
				<div class="panel">
					<div class="acod-head">
					 <h5 class="acod-title">
						<a data-toggle="collapse"  href="#collapseFive1" class="collapsed"  data-parent="#accordion1"aria-expanded="false">
						<?php echo esc_html__( 'Amenities & Features', 'service-finder' ) ?>
						<span class="indicator"><i class="fa fa-plus"></i></span>
						</a>
					 </h5>
					</div>
					<div id="collapseFive1" class="acod-body collapse">
						<div class="acod-content">
							<ul class="sf-features-list clearfix">
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
				</div>
				<?php
				}
				?>
			</div>                    
		</div>
        	</div>
            <div class="sf-curve-topWrap"><div class="sf-curveTop sf-vendBio-curveTop" style="background-color:<?php echo esc_attr($curveleftcolor); ?>"></div></div>
            <div class="sf-curve-botWrap"><div class="sf-curveBot sf-vendBio-curveBot" style="background-color:<?php echo esc_attr($curverightcolor); ?>"></div></div>
		</section>
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
		<section class="section-full bg-white sf-vender-video-wrap clearfix" id="sf-provider-video">
			<div class="container">
            <div class="section-head text-center">
				<h2><?php echo service_finder_get_data($service_finder_options,'label-our-video',esc_html__( 'Our Video', 'service-finder' )); ?></h2>
			</div>
			<div class="section-content">
			
				<div class="sf-video-slider mfp-gallery owl-carousel">
					<?php
					$oembed = new ClassOEmbed();
					foreach($embeded_codes as $embeded_code){
					$thumburl = service_finder_get_video_thumb_url($embeded_code,'full');
					?>
					<div class="item">
						<div class="sf-video-slide-wrap sf-videos-slider-two ">
							 <div class="sf-video-slide-thum" style="background-image:url(<?php echo esc_url($thumburl); ?>)"></div>
							 <div class="sf-video-slide-overlay"></div>
							 <?php
							 $videotype = service_finder_get_video_type($embeded_code);
							 if($videotype == 'facebook'){
							 ?>
							 <a href="https://www.facebook.com/v2.5/plugins/video.php?href=<?php echo esc_url($embeded_code); ?>" class="popup-youtube">
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
							 <a class="sf-video video-play-btn popup-youtube" href="<?php echo esc_url($embeded_code); ?>">
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
            </div>
            <div class="sf-curve-topWrap"><div class="sf-curveTop sf-vendVideo-curveTop" style="background-color:<?php echo esc_attr($curveleftcolor); ?>"></div></div>
            <div class="sf-curve-botWrap"><div class="sf-curveBot sf-vendVideo-curveBot" style="background-color:<?php echo esc_attr($curverightcolor); ?>"></div></div>
		</section>
		<?php
		}
		}else
		{
		?>
		<section class="section-full bg-white sf-vender-video-wrap clearfix" id="sf-provider-video">
			<div class="container">
                <div class="section-head text-center">
                    <h2><?php echo service_finder_get_data($service_finder_options,'label-our-video',esc_html__( 'Our Video', 'service-finder' )); ?></h2>
                </div>
                <div class="section-content">
                    <div class="embed-responsive embed-responsive-16by9">
                    <?php 
                    $oembed = new ClassOEmbed();
                    echo $oembed->getembededcode($providerInfo->embeded_code); 
                    ?>                
                    </div>
                </div>
            </div>
            <div class="sf-curve-topWrap"><div class="sf-curveTop sf-vendVideo-curveTop" style="background-color:<?php echo esc_attr($curveleftcolor); ?>"></div></div>
            <div class="sf-curve-botWrap"><div class="sf-curveBot sf-vendVideo-curveBot" style="background-color:<?php echo esc_attr($curverightcolor); ?>"></div></div>
		</section>
		<?php
		}
		}
		?>
        
        <!-- Services & Documents Section --> 
        <?php
		$attachmentIDs = service_finder_getDocuments($providerInfo->wp_user_id);
		$services = service_finder_getAllServices($author);
		if((!empty($services) && service_finder_get_data($service_finder_options,'my-services-menu')) || !empty($attachmentIDs)){
		$custombgcolor = (service_finder_theme_get_data($service_finder_options,'profile-document-services-bg-color') != '') ? 'background-color:'.service_finder_theme_get_data($service_finder_options,'profile-document-services-bg-color').'; ' : '';
		?>
        <section class="section-full sf-venderDocument-wrap bg-secondary profile-services-third" id="sf-provider-services" style=" <?php echo esc_attr($custombgcolor);?>">
            <div class="container">
                   
                <div class="section-content">
                	<div class="sf-custom-tabs">
                    <ul class="nav nav-tabs nav-table-cell text-center font-20">                                        
                        <?php if(!empty($services) && service_finder_get_data($service_finder_options,'my-services-menu')){ ?>
                        <li class="active"><a data-toggle="tab" href="#tab-111"><i class="fa fa-cog"></i> <?php echo service_finder_get_data($service_finder_options,'label-our-services',esc_html__( 'Services', 'service-finder' )); ?></a></li>                                        
                        <?php } ?>
                        <?php if(!empty($attachmentIDs)){ ?>
                        <li class="<?php echo (empty($services) || !service_finder_get_data($service_finder_options,'my-services-menu')) ? 'active' : ''; ?>"><a data-toggle="tab" href="#tab-222"><i class="fa fa-file-o"></i> <?php echo service_finder_get_data($service_finder_options,'label-documents',esc_html__( 'Documents', 'service-finder' )); ?></a></li>             
                        <?php } ?>                           
                    </ul>
                    <div class="tab-content">
                        <?php if(!empty($services) && service_finder_get_data($service_finder_options,'my-services-menu')){ ?>
                        <div id="tab-111" class="tab-pane active">
                            <div class="sf-services-tab">
                                <?php do_action('service_finder_display_services',$author); ?>
                            </div>                                            
                        </div>
                        <?php } ?>
                        
                        <?php if(!empty($attachmentIDs)){ ?>
                        <div id="tab-222" class="tab-pane <?php echo (empty($services) || !service_finder_get_data($service_finder_options,'my-services-menu')) ? 'active' : ''; ?>">
                            <div class="sf-document-tab">
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
                                                        <a download="><?php echo (!empty(basename(get_attached_file($attachmentID->attachmentid)))) ? basename(get_attached_file($attachmentID->attachmentid)) : basename(wp_get_attachment_url($attachmentID->attachmentid)); ?>" href="<?php echo get_permalink( $attachmentID->attachmentid ).'?attachment_id='. $attachmentID->attachmentid.'&download_file=1'; ?>">
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
                        <?php } ?>
                        
                    </div>
                </div>
                </div>
                
            </div>
            <div class="sf-curve-topWrap"><div class="sf-curveTop sf-venderDoc-curveTop" style="background-color:<?php echo esc_attr($curveleftcolor); ?>"></div></div>
            <div class="sf-curve-botWrap"><div class="sf-curveBot sf-venderDoc-curveBot" style="background-color:<?php echo esc_attr($curverightcolor); ?>"></div></div>            
        </section>
        <?php } ?>
        <!-- Services & Documents Section END -->   
        
        <!-- Book Now --> 
        <?php
		if(service_finder_user_has_capability('bookings',$author))
		{
		if($settings['booking_process'] == 'on' && (!is_user_logged_in() || service_finder_getUserRole($current_user->ID) == 'administrator' || service_finder_getUserRole($current_user->ID) == 'Customer' || (service_finder_getUserRole($current_user->ID) == 'Provider' && $current_user->ID == $author) ))
		{
		
		if((!is_user_logged_in() && !$service_finder_options['guest-booking'])){
			echo '<div class="alert alert-danger" role="alert">';
			echo esc_html__('In Order to book services you have to login.', 'service-finder');
			echo '</div>';
		}
		?>
        <section class="section-full sf-BookNow-wrap book-now-scroll" id="book-now-section">
            <div class="container">
            
            	<div class="section-head text-center">
                    <h2><?php echo service_finder_get_data($service_finder_options,'label-book-now',esc_html__('Book Now', 'service-finder')); ?></h2>
                </div>                   
                <div class="section-content">
                    <div class="sf-booking-text-price <?php echo ($providerInfo->booking_description == "") ? 'sf-no-booking-desc' : ''; ?>">
                        <div class="sf-booking-text-cell">
                            <?php if($providerInfo->booking_description != ""){ ?>
                              <?php echo apply_filters('the_content', $providerInfo->booking_description); ?>
                            <?php } ?>
                        </div>
                        <div class="sf-booking-price-cell">
                            <div class="sf-pricex-lable"><?php echo service_finder_get_data($service_finder_options,'label-booking-amount',esc_html__('Booking Amount', 'service-finder')); ?>:</div>
                            <div class="sf-price-amoutss"><span id="bookingamount"><?php echo service_finder_money_format(service_finder_get_data($settings,'mincost','0.0')); ?></span></div>
                        </div>                        
                    </div>
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
                    
                    <?php require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/book-now/templates/book-now-v3.php'; ?>

                </div>
                
            </div>
            <div class="sf-curve-topWrap"><div class="sf-curveTop sf-BookNow-curveTop" style="background-color:<?php echo esc_attr($curveleftcolor); ?>"></div></div>
            <div class="sf-curve-botWrap"><div class="sf-curveBot sf-BookNow-curveBot" style="background-color:<?php echo esc_attr($curverightcolor); ?>"></div></div>            
        </section>
        <?php
		}
		}
		?>
        <!-- Book Now END -->   
        
        <!-- Review & Article --> 
        <section class="section-full bg-green-2 sf-vendReviewArticle-wrap" id="sf-provider-review">
            <div class="container">
                   
                <div class="section-content">
                
                	<div class="sf-custom-tabs">
                        <ul class="nav nav-tabs nav-table-cell text-center font-20">                                        
                            <?php if(service_finder_get_data($service_finder_options,'review-system')){ ?>
                            <li class="active"><a data-toggle="tab" href="#tab-11111"><?php echo service_finder_get_data($service_finder_options,'label-review',esc_html__('Review', 'service-finder')); ?></a></li>                                        
                            <?php } ?>
                            <?php if(service_finder_get_data($service_finder_options,'question-answer-section')){ ?>
                            <li class="<?php echo (!service_finder_get_data($service_finder_options,'review-system')) ? 'active' : ''; ?>"><a data-toggle="tab" href="#tab-22222"><?php echo service_finder_get_data($service_finder_options,'label-qa',esc_html__('Q & A', 'service-finder')); ?></a></li>
                            <li><a data-toggle="tab" href="#tab-33333"><?php echo service_finder_get_data($service_finder_options,'label-ask-question',esc_html__('Ask Question', 'service-finder')); ?></a></li> 
                            <?php } ?>
                            <?php if(service_finder_article_exist($author)){ ?>
                            <li class="<?php echo (!service_finder_get_data($service_finder_options,'review-system') && !service_finder_get_data($service_finder_options,'question-answer-section')) ? 'active' : ''; ?>"><a data-toggle="tab" href="#tab-44444"><?php echo service_finder_get_data($service_finder_options,'label-articles',esc_html__('Articles', 'service-finder')); ?></a></li>                                      
                            <?php } ?>                                      
                        </ul>
                        <div class="tab-content">
							<?php if(service_finder_get_data($service_finder_options,'review-system')){ ?>
                            <div id="tab-11111" class="tab-pane active">
                                <?php require SERVICE_FINDER_BOOKING_TEMPLATES_DIR . '/comment-template.php'; ?>     
                            </div>
                            <?php } ?>
                            
                            <?php if(service_finder_get_data($service_finder_options,'question-answer-section')){ ?>
                            <div id="tab-22222" class="tab-pane <?php echo (!service_finder_get_data($service_finder_options,'review-system')) ? 'active' : ''; ?>">
                                <div class="sf-q&a-tab-area">
                                    <?php require SERVICE_FINDER_BOOKING_TEMPLATES_DIR . '/qa.php'; ?>                            
                                </div>
                            </div>
                            
                            <div id="tab-33333" class="tab-pane">
                                <div class="sf-ask-question-tab-area">
                                    <?php require SERVICE_FINDER_BOOKING_TEMPLATES_DIR . '/ask-qa.php'; ?>                                    
                                </div>
                            </div>  
                            <?php } ?>
                            <?php if(service_finder_article_exist($author)){ ?>                          
                            <div id="tab-44444" class="tab-pane <?php echo (!service_finder_get_data($service_finder_options,'review-system')  && !service_finder_get_data($service_finder_options,'question-answer-section')) ? 'active' : ''; ?>">
                                <div class="sf-articles-tab-area">
                                    <?php require SERVICE_FINDER_BOOKING_TEMPLATES_DIR . '/articles.php'; ?>
                                </div>
                            </div>                            
                            <?php } ?>
                        </div>
                    </div>
                
                    
                    
                </div>
                
            </div>
            <div class="sf-curve-topWrap"><div class="sf-curveTop sf-vendRevArt-curveTop" style="background-color:<?php echo esc_attr($curveleftcolor); ?>"></div></div>
            <div class="sf-curve-botWrap"><div class="sf-curveBot sf-vendRevArt-curveBot" style="background-color:<?php echo esc_attr($curverightcolor); ?>"></div></div>            
        </section>
        <!-- Review & Article END -->             
        <?php } ?>     
        
        </div>
<?php
if(is_user_logged_in()){
	if(service_finder_getUserRole($current_user->ID) == 'Customer'){
	require SERVICE_FINDER_BOOKING_LIB_DIR . '/invite-job.php';
	}
}
?>    
<?php
get_footer();
