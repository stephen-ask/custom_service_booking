<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$wpdb = service_finder_plugin_global_vars('wpdb');
$service_finder_Params = service_finder_plugin_global_vars('service_finder_Params');
$service_finder_options = get_option('service_finder_options');
$service_finder_Tables = service_finder_plugin_global_vars('service_finder_Tables');

$setorderby = (isset($_GET['setorderby'])) ? esc_html($_GET['setorderby']) : '';
$setorder = (isset($_GET['setorder'])) ? esc_html($_GET['setorder']) : '';
$numberofpages = (isset($_GET['numberofpages'])) ? esc_html($_GET['numberofpages']) : '';

$defaultview = (!empty($service_finder_options["city-default-view"])) ? esc_js($service_finder_options["city-default-view"]) : "grid-4";

$viewtype = (isset($_GET['viewtype'])) ? esc_html($_GET['viewtype']) : $defaultview;

require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/cities/providerscities.php';
$getProviders = new SERVICE_FINDER_providersCities();
$providersInfoArr = $getProviders->service_finder_getProvidersCities(get_queried_object()->term_id);
$totalresult = $providersInfoArr['count'];
?>
<?php if($service_finder_options['search-style'] == 'ajax-search'){?>
<!--Ajax Search Style-->

<div class="col-md-12 content">
  <div class = "inner-box content no-right-margin darkviolet">
  <?php
  wp_add_inline_script( 'bootstrap', 'jQuery(document).ready(function($) {
				/*Load category result function*/
                function service_finder_loadCitiesResult(page,viewtype,numberofpages,setorderby,setorder){
                    
					var data = {
						page: page,
                        action: "load-city-result",
						cityid: "'.esc_js(get_queried_object()->term_id).'",
						viewtype: viewtype,
						numberofpages: numberofpages,
						setorderby: setorderby,
						setorder: setorder
                    };
						
				  var formdata = jQuery.param(data);
				  
				  jQuery.ajax({
			
									type: "POST",
			
									url: ajaxurl,
			
									data: formdata,
									
									dataType: "json",
									
									beforeSend: function() {
										jQuery(".loading-area").show();
									},
			
									success:function (data, textStatus) {
									
									jQuery(".loading-area").hide();
										jQuery(".cvf_universal_container").html(data["result"]);
										jQuery(".display-ratings").rating();
										jQuery(".sf-show-rating").show();
										equalheight(".equal-col-outer .equal-col");
										jQuery("[data-tool=\"tooltip\"]").tooltip();
									
									}
			
								});
                   
                }
               
                // Load page 1 as the default
				var viewtype = jQuery("#viewTypes li.active").attr("data-view");

                service_finder_loadCitiesResult(1,viewtype);
               
                // Handle the clicks
				jQuery("body").on("click", ".cvf_universal_container .cvf-universal-pagination li.activelink a", function(){
                    var page = jQuery(this).parents("li").attr("data-pnum");
					viewtype = jQuery("#viewTypes li.active").attr("data-view");
                    
					var setorderby = jQuery("#setorderby option:selected").val();
					var setorder = jQuery("#setorder option:selected").val();
					var numberofpages = jQuery("#numberofpages option:selected").val();
						
                    service_finder_loadCitiesResult(page,viewtype,numberofpages,setorderby,setorder);
                   
                });
				
				// Handle the view types
				jQuery("body").on("click", "#viewTypes li", function(){
					jQuery("#viewTypes li").removeClass("active");
                    jQuery(this).addClass("active");
					viewtype = jQuery(this).attr("data-view");
					var page = jQuery(".cvf_universal_container .cvf-universal-pagination li.selected").attr("data-pnum");
                   
				    var setorderby = jQuery("#setorderby option:selected").val();
					var setorder = jQuery("#setorder option:selected").val();
					var numberofpages = jQuery("#numberofpages option:selected").val();
						
                    service_finder_loadCitiesResult(page,viewtype,numberofpages,setorderby,setorder);
                   
                });
				/*Set the number of pages*/
				jQuery("body").on("change", "#numberofpages", function(){
						var page = jQuery(".cvf_universal_container .cvf-universal-pagination li.selected").attr("data-pnum");
						var viewtype = jQuery("#viewTypes li.active").attr("data-view");
						
						var numberofpages = jQuery(this).val();
						var setorderby = jQuery("#setorderby option:selected").val();
						var setorder = jQuery("#setorder option:selected").val();
						service_finder_loadCitiesResult(1,viewtype,numberofpages,setorderby,setorder);
				});
				/*Set the sort order by*/
				jQuery("body").on("change", "#setorderby", function(){
						var page = jQuery(".cvf_universal_container .cvf-universal-pagination li.selected").attr("data-pnum");
						var viewtype = jQuery("#viewTypes li.active").attr("data-view");
						
						var setorderby = jQuery(this).val();
						var numberofpages = jQuery("#numberofpages option:selected").val();
						var setorder = jQuery("#setorder option:selected").val();
						service_finder_loadCitiesResult(page,viewtype,numberofpages,setorderby,setorder);
				});
				/*Set the order*/
				jQuery("body").on("change", "#setorder", function(){
						var page = jQuery(".cvf_universal_container .cvf-universal-pagination li.selected").attr("data-pnum");
						var viewtype = jQuery("#viewTypes li.active").attr("data-view");
						
						var setorder = jQuery(this).val();
						var numberofpages = jQuery("#numberofpages option:selected").val();
						var setorderby = jQuery("#setorderby option:selected").val();
						service_finder_loadCitiesResult(page,viewtype,numberofpages,setorderby,setorder);
				});
				
            });', 'after' );
  ?>
  </div>
</div>
<div class="title-section">
  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-5">
      <?php if($totalresult > 0){ ?>
      <h2 class="result-title">
	  <?php 
	  $allowedhtml = array(
			'strong' => array()
		);
	  printf( wp_kses( '<strong>%s</strong> ', $allowedhtml ), $totalresult ); echo esc_html__('Results Found','service-finder');
	  ?>
      </h2>
      <?php } ?>
    </div>
    <div class="col-md-7 col-sm-7 col-xs-7">
      <?php echo do_action('service_finder_category_filter'); ?>
    </div>
  </div>
</div>
<!--Display Result Section-->
<div class="section-content">
    <div class = "cvf_pag_loading">
      <div class = "cvf_universal_container">
        <div class="cvf-universal-content"></div>
      </div>
    </div>
</div>
<!-- result END -->
<?php }elseif($service_finder_options['search-style'] == 'page-reload'){?>
<!--Page Reload Style-->
<div class="col-md-12 content">
  <div class = "inner-box content no-right-margin darkviolet">
  <?php
  
  wp_add_inline_script( 'bootstrap', 'jQuery(document).ready(function($) {
				var get_permalink = "'.service_finder_using_permalink().'";
                /*Load category result function*/
				function service_finder_loadCitiesResult(page,viewtype,numberofpages,setorderby,setorder){
					var data = {
						page: page,
                        action: "load-city-result",
						cityid: "'.esc_js(get_queried_object()->term_id).'",
						viewtype: viewtype,
						numberofpages: numberofpages,
						setorderby: setorderby,
						setorder: setorder
                    };
						
				  var formdata = jQuery.param(data);
				  
				  jQuery.ajax({
			
									type: "POST",
			
									url: ajaxurl,
			
									data: formdata,
									
									dataType: "json",
									
									beforeSend: function() {
										jQuery(".loading-area").show();
									},
			
									success:function (data, textStatus) {
										jQuery(".loading-area").hide();
										jQuery(".cvf_universal_container").html(data["result"]);
										jQuery(".display-ratings").rating();
										jQuery(".sf-show-rating").show();
										equalheight(".equal-col-outer .equal-col");
										jQuery("[data-tool=\"tooltip\"]").tooltip();
									}
			
								});
                   
                }
               
                // Load page 1 as the default
				var viewtype = jQuery("#viewTypes li.active").attr("data-view");
				var page = jQuery(".pagination span.current").html();
                
				if(!page > 0){
					page = 1;
				}
				
				if(page > 1){
				service_finder_loadCitiesResult(page,viewtype);
				}else{
				service_finder_loadCitiesResult(1,viewtype);
				}
               
               // Handle the view types
				jQuery("body").on("click", "#viewTypes li", function(){
					jQuery("#viewTypes li").removeClass("active");
                    jQuery(this).addClass("active");
					viewtype = jQuery(this).attr("data-view");
					
					var page = jQuery(".pagination span.current").html();
					if(!page > 0){
						page = 1;
					}
					
					var setorderby = jQuery("#setorderby option:selected").val();
					var setorder = jQuery("#setorder option:selected").val();
					var numberofpages = jQuery("#numberofpages option:selected").val();
					
					var homeurl = "'.esc_js(home_url("/city/".get_query_var("sf-cities"))).'";
					
					if(get_permalink){
					var homeurl = "'.esc_js(home_url("/city/".get_query_var("sf-cities"))).'";
					window.location = homeurl+"?cityid='.esc_js(get_queried_object()->term_id).'&pagenum="+page+"&viewtype="+viewtype+"&numberofpages="+numberofpages+"&setorderby="+setorderby+"&setorder="+setorder;
						}else{
						var homeurl = "'.esc_js(home_url("/")."?city=".get_query_var("sf-cities")).'";
						window.location = homeurl+"&cityid='.esc_js(get_queried_object()->term_id).'&pagenum="+page+"&viewtype="+viewtype+"&numberofpages="+numberofpages+"&setorderby="+setorderby+"&setorder="+setorder;
					}
					
                   
                });
				//Set number of pages
				jQuery("body").on("change", "#numberofpages", function(){
						var page = jQuery(".pagination span.current").html();
						var viewtype = jQuery("#viewTypes li.active").attr("data-view");
						if(!page > 0){
							page = 1;
						}
						
						var numberofpages = jQuery(this).val();
						var setorderby = jQuery("#setorderby option:selected").val();
						var setorder = jQuery("#setorder option:selected").val();

						var homeurl = "'.esc_js(home_url("/city/".get_query_var("sf-cities"))).'";
					
					if(get_permalink){
					var homeurl = "'.esc_js(home_url("/city/".get_query_var("sf-cities"))).'";
					window.location = homeurl+"?cityid='.esc_js(get_queried_object()->term_id).'&pagenum=1&viewtype="+viewtype+"&numberofpages="+numberofpages+"&setorderby="+setorderby+"&setorder="+setorder;
					
						}else{
						var homeurl = "'.esc_js(home_url("/")."?city=".get_query_var("sf-cities")).'";
						window.location = homeurl+"&cityid='.esc_js(get_queried_object()->term_id).'&pagenum="+page+"&viewtype="+viewtype+"&numberofpages="+numberofpages+"&setorderby="+setorderby+"&setorder="+setorder;
					}
				});
				//Set order by
				jQuery("body").on("change", "#setorderby", function(){
						var page = jQuery(".pagination span.current").html();
						var viewtype = jQuery("#viewTypes li.active").attr("data-view");
						if(!page > 0){
							page = 1;
						}
						
						var setorderby = jQuery(this).val();
						var numberofpages = jQuery("#numberofpages option:selected").val();
						var setorder = jQuery("#setorder option:selected").val();

						var homeurl = "'.esc_js(home_url("/city/".get_query_var("sf-cities"))).'";
					
					if(get_permalink){
					var homeurl = "'.esc_js(home_url("/city/".get_query_var("sf-cities"))).'";
					window.location = homeurl+"?cityid='.esc_js(get_queried_object()->term_id).'&pagenum="+page+"&viewtype="+viewtype+"&numberofpages="+numberofpages+"&setorderby="+setorderby+"&setorder="+setorder;
						}else{
						var homeurl = "'.esc_js(home_url("/")."?city=".get_query_var("sf-cities")).'";
						window.location = homeurl+"&cityid='.esc_js(get_queried_object()->term_id).'&pagenum="+page+"&viewtype="+viewtype+"&numberofpages="+numberofpages+"&setorderby="+setorderby+"&setorder="+setorder;
					}
				});
				//Set order
				jQuery("body").on("change", "#setorder", function(){
						var page = jQuery(".pagination span.current").html();
						var viewtype = jQuery("#viewTypes li.active").attr("data-view");
						if(!page > 0){
							page = 1;
						}
						
						var setorder = jQuery(this).val();
						var numberofpages = jQuery("#numberofpages option:selected").val();
						var setorderby = jQuery("#setorderby option:selected").val();
						
						var homeurl = "'.esc_js(home_url("/city/".get_query_var("sf-cities"))).'";
					
					if(get_permalink){
					var homeurl = "'.esc_js(home_url("/city/".get_query_var("sf-cities"))).'";
					window.location = homeurl+"?cityid='.esc_js(get_queried_object()->term_id).'&pagenum="+page+"&viewtype="+viewtype+"&numberofpages="+numberofpages+"&setorderby="+setorderby+"&setorder="+setorder;
						}else{
						var homeurl = "'.esc_js(home_url("/")."?city=".get_query_var("sf-cities")).'";
						window.location = homeurl+"&cityid='.esc_js(get_queried_object()->term_id).'&pagenum="+page+"&viewtype="+viewtype+"&numberofpages="+numberofpages+"&setorderby="+setorderby+"&setorder="+setorder;
					}
				});
				
            });', 'after' );
  ?>
  </div>
</div>
<div class="title-section">
  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-5">
      <?php if($totalresult > 0){ ?>
      <h2 class="result-title">
      <?php 
	  $allowedhtml = array(
			'strong' => array()
		);
	  printf( wp_kses('<strong>%s</strong> ', $allowedhtml ), $totalresult ); echo esc_html__('Results Found','service-finder');
	  ?>
      </h2>
      <?php } ?>
    </div>
    <div class="col-md-7 col-sm-7 col-xs-7">
      <?php echo do_action('service_finder_category_filter',$setorderby,$setorder,$numberofpages,$viewtype,$defaultview); ?>
    </div>
  </div>
</div>
<!--Display category result-->
<div class="section-content">
    <?php
			$srhcontent = '';
			if($viewtype == 'listview'){
			$srhcontent .= '<div class="listing-box row">';
			}elseif($viewtype == 'grid-4'){
			$srhcontent .= '<div class="listing-grid-box sf-listing-grid-4 equal-col-outer">
							<div class="row">';
			}elseif($viewtype == 'grid-3'){
			$srhcontent .= '<div class="listing-grid-box sf-listing-grid-3 equal-col-outer">
							<div class="row">';
			}else{
			$srhcontent .= '<div class="listing-grid-box sf-listing-grid-4 equal-col-outer">
							<div class="row">';
			}

    if($numberofpages != ""){
	$per_page = $numberofpages;
	}else{
	$srhperpage = (!empty($service_finder_options['srh-per-page'])) ? $service_finder_options['srh-per-page'] : '';
	$per_page = ($srhperpage > 0) ? $service_finder_options['srh-per-page'] : 12;
	}
   
									 
   $page = (isset($_GET['pagenum'])) ? $_GET['pagenum'] : 1;
	
   $start = ($page - 1) * $per_page;	

	if($setorderby != ""){
	$orderby = $setorderby;
	}else{
	$orderby = 'id';
	}
	
	if($setorder != ""){
	$order = $setorder;
	}else{
	$order = 'desc';
	}
	
   $getProviders = new SERVICE_FINDER_providersCities();
	
   $providersInfoArr = $getProviders->service_finder_getProvidersCities(get_queried_object()->term_id,$start,$per_page,$orderby,$order);
   $providersInfo = $providersInfoArr['result'];
   $count = $providersInfoArr['count'];
   $flag = 0;
   
	
	if(!empty($providersInfo)){ 
	
	foreach($providersInfo as $provider){

	$userLink = service_finder_get_author_url($provider->wp_user_id);

	if(!empty($provider->avatar_id) && $provider->avatar_id > 0){
		$src  = wp_get_attachment_image_src( $provider->avatar_id, 'service_finder-provider-thumb' );
		$src  = $src[0];
	}else{
		$src  = service_finder_get_default_avatar();
	}
	
	$link = $userLink;
    $current_user = wp_get_current_user(); 
	$addtofavorite = '';
	if($service_finder_options['add-to-fav']){
	if(is_user_logged_in()){
		$myfav = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->favorites.' where user_id = %d AND provider_id = %d',$current_user->ID,$provider->wp_user_id));
		
		if(!empty($myfav)){
		if(service_finder_themestyle() == 'style-2'){
		$addtofavorite = '<a href="javascript:;" class="remove-favorite sf-featured-item" data-proid="'.esc_attr($provider->wp_user_id).'" data-userid="'.esc_attr($current_user->ID).'"><i class="fa fa-heart"></i></a>';
		}else{
		$addtofavorite = '<a href="javascript:;" class="remove-favorite btn btn-primary" data-proid="'.esc_attr($provider->wp_user_id).'" data-userid="'.esc_attr($current_user->ID).'">'.esc_html__( 'My Favorite', 'service-finder' ).'<i class="fa fa-heart"></i></a>';
		}
		}else{
		if(service_finder_themestyle() == 'style-2'){
		$addtofavorite = '<a href="javascript:;" class="add-favorite sf-featured-item" data-proid="'.esc_attr($provider->wp_user_id).'" data-userid="'.esc_attr($current_user->ID).'"><i class="fa fa-heart-o"></i></a>';
		}else{
		$addtofavorite = '<a href="javascript:;" class="add-favorite btn btn-primary" data-proid="'.esc_attr($provider->wp_user_id).'" data-userid="'.esc_attr($current_user->ID).'">'.esc_html__( 'Add to Fav', 'service-finder' ).'<i class="fa fa-heart"></i></a>';
		}
		}
	}else{
		if(service_finder_themestyle() == 'style-2'){
		$addtofavorite = '<a class="sf-featured-item" href="javascript:;" data-action="login" data-redirect="no" data-toggle="modal" data-target="#login-Modal"><i class="fa fa-heart-o"></i></a>';
		}else{
		$addtofavorite = '<a class="btn btn-primary" href="javascript:;" data-action="login" data-redirect="no" data-toggle="modal" data-target="#login-Modal">'.esc_html__( 'Add to Fav', 'service-finder' ).'<i class="fa fa-heart"></i></a>';
		}
	}        
	}
			if(service_finder_is_featured($provider->wp_user_id)){
			if(service_finder_themestyle() == 'style-2'){
			$featured = '<div  class="sf-featured-sign">'.esc_html__( 'Featured', 'service-finder' ).'</div>';
			}else{
			$featured = '<strong class="sf-featured-label"><span>'.esc_html__( 'Featured', 'service-finder' ).'</span></strong>';
			}
			}else{
			$featured = '';
			}
			$showaddressinfo = (isset($service_finder_options['show-address-info'])) ? esc_attr($service_finder_options['show-address-info']) : '';
			$addressbox = '';
	  if($showaddressinfo && $service_finder_options['show-postal-address'] && service_finder_check_address_info_access()){
			if(service_finder_themestyle() == 'style-2'){
			$addressbox = service_finder_getshortAddress($provider->wp_user_id);			
			}else{
			$addressbox = '<div class="overlay-text">
									<div class="sf-address-bx">
										<i class="fa fa-map-marker"></i>
										'.service_finder_getshortAddress($provider->wp_user_id).'
									</div>
								</div>';
			}	
		}
			
		if(service_finder_themestyle() == 'style-3')
	    {
		 $srhcontent .= service_finder_display_provider_boxes($provider->wp_user_id,$viewtype,false);
		}elseif(service_finder_themestyle() == 'style-4'){
			$msg .= service_finder_provider_box_fourth($provider->wp_user_id,$viewtype,false);
	    }else{
		if($viewtype == 'grid-4'){
			/*4 colomn layout*/
			if(service_finder_themestyle() == 'style-2'){
			
			$srhcontent .= '<div class="col-md-3 col-sm-6 equal-col">
			<div class="sf-search-result-girds" id="proid-'.$provider->wp_user_id.'">
                            
                                <div class="sf-featured-top">
                                    <div class="sf-featured-media" style="background-image:url('.esc_url($src).')"></div>
                                    <div class="sf-overlay-box"></div>
                                    '.service_finder_display_category_label($provider->wp_user_id).'
                                    '.service_finder_check_varified_icon($provider->wp_user_id).'
									'.$addtofavorite.'
                                    
                                    <div class="sf-featured-info">
                                        '.$featured.'
                                        <div  class="sf-featured-provider">'.service_finder_getExcerpts($provider->full_name,0,35).'</div>
                                        <div  class="sf-featured-address"><i class="fa fa-map-marker"></i> '.$addressbox.' </div>
                                        '.service_finder_displayRating(service_finder_getAverageRating($provider->wp_user_id)).'
                                    </div>
									<a href="'.esc_url($link).'" class="sf-profile-link"></a>
                                </div>
                                
                                <div class="sf-featured-bot">
                                    <div class="sf-featured-comapny">'.service_finder_getExcerpts(service_finder_getCompanyName($provider->wp_user_id),0,20).'</div>
                                    <div class="sf-featured-text">'.service_finder_getExcerpts(nl2br(stripcslashes($provider->bio)),0,75).'</div>
                                    '.service_finder_show_provider_meta($provider->wp_user_id,$provider->phone,$provider->mobile).'
                                </div>
                                
                            </div>
							 </div>';
			
			}else{
            $srhcontent .= '<div class="col-md-3 col-sm-6 equal-col">

                <div class="sf-provider-bx item">
                    <div class="sf-element-bx">
                    
                        <div class="sf-thum-bx sf-listing-thum img-effect2" style="background-image:url('.esc_url($src).');"> <a href="'.esc_url($link).'" class="sf-listing-link"></a>
                            
							<div class="overlay-bx">
								'.$addressbox.'
							</div>
                            
                            '.service_finder_get_primary_category_tag($provider->wp_user_id).'
							'.$featured.'
                            
                        </div>
                        
                        <div class="padding-20 bg-white '.service_finder_check_varified($provider->wp_user_id).'">
                            <h4 class="sf-title">'.service_finder_getExcerpts(service_finder_getCompanyName($provider->wp_user_id),0,20).'</h4>
                            <strong class="sf-company-name"><a href="'.esc_url($link).'">'.service_finder_getExcerpts($provider->full_name,0,35).'</a></strong>
							'.service_finder_show_provider_meta($provider->wp_user_id,$provider->phone,$provider->mobile).'
							'.service_finder_displayRating(service_finder_getAverageRating($provider->wp_user_id)).'
							'.service_finder_check_varified_icon($provider->wp_user_id).'
                        </div>
                        
                        <div class="btn-group-justified" id="proid-'.$provider->wp_user_id.'">
                          <a href="'.esc_url($link).'" class="btn btn-custom">'.esc_html__('Full View','service-finder').' <i class="fa fa-arrow-circle-o-right"></i></a>
                          '.$addtofavorite.'
                        </div>
                        
                    </div>
                </div>

            </div>';
			}
			}elseif($viewtype == 'listview'){
			
			/*Listview Layout*/
			if(service_finder_themestyle() == 'style-2'){
			$srhcontent .= '<div class="sf-featured-listing clearfix">
                            
                            <div class="sf-featured-left" id="proid-'.$provider->wp_user_id.'">
                                <div class="sf-featured-media" style="background-image:url('.esc_url($src).')"></div>
								<a href="'.esc_url($link).'" class="sf-listing-link"></a>
                                <div class="sf-overlay-box"></div>
                                '.service_finder_display_category_label($provider->wp_user_id).'
                                '.service_finder_check_varified_icon($provider->wp_user_id).'
                                '.$addtofavorite.'
                                
                                <div class="sf-featured-info">
                                    '.$featured.'
                                </div>
                            </div>
                            
                            <div class="sf-featured-right">
                                <div  class="sf-featured-provider"><a href="'.esc_url($link).'">'.service_finder_getExcerpts($provider->full_name,0,35).'</a></div>
                                <div  class="sf-featured-address"><i class="fa fa-map-marker"></i> '.$addressbox.' </div>
                                '.service_finder_displayRating(service_finder_getAverageRating($provider->wp_user_id)).'
                                <div class="sf-featured-comapny">'.service_finder_getExcerpts(service_finder_getCompanyName($provider->wp_user_id),0,30).'</div>
                                <div class="sf-featured-text">'.service_finder_getExcerpts($provider->bio,0,300).'</div>
                                '.service_finder_show_provider_meta($provider->wp_user_id,$provider->phone,$provider->mobile).'
                            </div>
                            
                        </div>';
			}else{
			$srhcontent .= '<div class="col-md-12">
                                <div class="sf-element-bx result-listing clearfix">
                                
                                    <div class="sf-thum-bx sf-listing-thum img-effect2" style="background-image:url('.esc_url($src).');"> <a href="'.esc_url($link).'" class="sf-listing-link"></a>
                                        
                                        <div class="overlay-bx">
											'.$addressbox.'
										</div>
                                        
										'.service_finder_get_primary_category_tag($provider->wp_user_id).'
										'.$featured.'
										'.service_finder_check_varified_icon($provider->wp_user_id).'
										
                                    </div>
                                    
                                    <div class="result-text '.service_finder_check_varified($provider->wp_user_id).'" id="proid-'.$provider->wp_user_id.'">
                                    	<h5 class="sf-title">'.service_finder_getExcerpts(service_finder_getCompanyName($provider->wp_user_id),0,30).'</h5>
                                        <strong class="sf-company-name"><a href="'.esc_url($link).'">'.service_finder_getExcerpts($provider->full_name,0,35).'</a></strong>
										'.service_finder_show_provider_meta($provider->wp_user_id,$provider->phone,$provider->mobile).'
										'.service_finder_displayRating(service_finder_getAverageRating($provider->wp_user_id)).'
										
                                        <div class="sf-address2-bx">
											<i class="fa fa-map-marker"></i>
											'.service_finder_getshortAddress($provider->wp_user_id).'
										</div>
										<p>'.service_finder_getExcerpts($provider->bio,0,300).'</p>
                                        '.$addtofavorite.'
										
                                    </div>
                                    
                                </div>
                            </div>';
			}				
			}elseif($viewtype == 'grid-3'){
			/*3 colomn layout*/
            if(service_finder_themestyle() == 'style-2'){
			
			$srhcontent .= '<div class="col-md-4 col-sm-6 equal-col">
                                <div class="sf-search-result-girds" id="proid-'.$provider->wp_user_id.'">
                            
                                <div class="sf-featured-top">
                                    <div class="sf-featured-media" style="background-image:url('.esc_url($src).')"></div>
                                    <div class="sf-overlay-box"></div>
                                    '.service_finder_display_category_label($provider->wp_user_id).'
                                    '.service_finder_check_varified_icon($provider->wp_user_id).'
									'.$addtofavorite.'
                                    
                                    <div class="sf-featured-info">
                                        '.$featured.'
                                        <div  class="sf-featured-provider">'.service_finder_getExcerpts($provider->full_name,0,35).'</div>
                                        <div  class="sf-featured-address"><i class="fa fa-map-marker"></i> '.$addressbox.' </div>
                                        '.service_finder_displayRating(service_finder_getAverageRating($provider->wp_user_id)).'
                                    </div>
									<a href="'.esc_url($link).'" class="sf-profile-link"></a>
                                </div>
                                
                                <div class="sf-featured-bot">
                                    <div class="sf-featured-comapny">'.service_finder_getExcerpts(service_finder_getCompanyName($provider->wp_user_id),0,30).'</div>
                                    <div class="sf-featured-text">'.service_finder_getExcerpts(nl2br(stripcslashes($provider->bio)),0,75).'</div>
                                    '.service_finder_show_provider_meta($provider->wp_user_id,$provider->phone,$provider->mobile).'
                                </div>
                                
                            </div>
                            </div>';
			
			}else{
			$srhcontent .= '<div class="col-md-4 col-sm-6 equal-col">
                                <div class="sf-provider-bx item">
                    <div class="sf-element-bx">
                    
                        <div class="sf-thum-bx sf-listing-thum img-effect2" style="background-image:url('.esc_url($src).');"> <a href="'.esc_url($link).'" class="sf-listing-link"></a>
                            
							<div class="overlay-bx">
								'.$addressbox.'
							</div>
                            
                            '.service_finder_get_primary_category_tag($provider->wp_user_id).'
							'.$featured.'
                            
                        </div>
                        
                        <div class="padding-20 bg-white '.service_finder_check_varified($provider->wp_user_id).'">
                            <h4 class="sf-title">'.service_finder_getExcerpts(service_finder_getCompanyName($provider->wp_user_id),0,30).'</h4>
                            <strong class="sf-company-name"><a href="'.esc_url($link).'">'.service_finder_getExcerpts($provider->full_name,0,35).'</a></strong>
							'.service_finder_show_provider_meta($provider->wp_user_id,$provider->phone,$provider->mobile).'
							'.service_finder_displayRating(service_finder_getAverageRating($provider->wp_user_id)).'
							'.service_finder_check_varified_icon($provider->wp_user_id).'
                        </div>
                        
                        <div class="btn-group-justified" id="proid-'.$provider->wp_user_id.'">
                          <a href="'.esc_url($link).'" class="btn btn-custom">'.esc_html__('Full View','service-finder').' <i class="fa fa-arrow-circle-o-right"></i></a>
                          '.$addtofavorite.'
                        </div>
                        
                    </div>
                </div>
                            </div>';
				}			
			}else{
			/*4 colomn layout*/
			if(service_finder_themestyle() == 'style-2'){
			
			$srhcontent .= '<div class="col-md-3 col-sm-6 equal-col">
			<div class="sf-search-result-girds" id="proid-'.$provider->wp_user_id.'">
                            
                                <div class="sf-featured-top">
                                    <div class="sf-featured-media" style="background-image:url('.esc_url($src).')"></div>
                                    <div class="sf-overlay-box"></div>
                                    '.service_finder_display_category_label($provider->wp_user_id).'
                                    '.service_finder_check_varified_icon($provider->wp_user_id).'
									'.$addtofavorite.'
                                    
                                    <div class="sf-featured-info">
                                        '.$featured.'
                                        <div  class="sf-featured-provider">'.service_finder_getExcerpts($provider->full_name,0,35).'</div>
                                        <div  class="sf-featured-address"><i class="fa fa-map-marker"></i> '.$addressbox.' </div>
                                        '.service_finder_displayRating(service_finder_getAverageRating($provider->wp_user_id)).'
                                    </div>
									<a href="'.esc_url($link).'" class="sf-profile-link"></a>
                                </div>
                                
                                <div class="sf-featured-bot">
                                    <div class="sf-featured-comapny">'.service_finder_getExcerpts(service_finder_getCompanyName($provider->wp_user_id),0,20).'</div>
                                    <div class="sf-featured-text">'.service_finder_getExcerpts(nl2br(stripcslashes($provider->bio)),0,75).'</div>
                                    '.service_finder_show_provider_meta($provider->wp_user_id,$provider->phone,$provider->mobile).'
                                </div>
                                
                            </div>
							 </div>';
			
			}else{
            $srhcontent .= '<div class="col-md-3 col-sm-6 equal-col">

                <div class="sf-provider-bx item">
                    <div class="sf-element-bx">
                    
                        <div class="sf-thum-bx sf-listing-thum img-effect2" style="background-image:url('.esc_url($src).');"> <a href="'.esc_url($link).'" class="sf-listing-link"></a>
                            
							<div class="overlay-bx">
								'.$addressbox.'
							</div>
                            
                            '.service_finder_get_primary_category_tag($provider->wp_user_id).'
							'.$featured.'
                            
                        </div>
                        
                        <div class="padding-20 bg-white '.service_finder_check_varified($provider->wp_user_id).'">
                            <h4 class="sf-title">'.service_finder_getExcerpts(service_finder_getCompanyName($provider->wp_user_id),0,20).'</h4>
                            <strong class="sf-company-name"><a href="'.esc_url($link).'">'.service_finder_getExcerpts($provider->full_name,0,35).'</a></strong>
							'.service_finder_show_provider_meta($provider->wp_user_id,$provider->phone,$provider->mobile).'
							'.service_finder_displayRating(service_finder_getAverageRating($provider->wp_user_id)).'
							'.service_finder_check_varified_icon($provider->wp_user_id).'
                        </div>
                        
                        <div class="btn-group-justified" id="proid-'.$provider->wp_user_id.'">
                          <a href="'.esc_url($link).'" class="btn btn-custom">'.esc_html__('Full View','service-finder').' <i class="fa fa-arrow-circle-o-right"></i></a>
                          '.$addtofavorite.'
                        </div>
                        
                    </div>
                </div>

            </div>';
			}
			}
		}	

     }

	}else{
	/*No Result Found*/
		$srhcontent .= '<div class="sf-nothing-found2">
				<strong class="sf-tilte">'.esc_html__('Nothing Found', 'service-finder').'</strong>
					  <p>'.esc_html__('Apologies, but no results were found for the request.', 'service-finder').'</p>
				</div>';
		$flag = 1;		
	
	}
		
		
		
		if($viewtype == 'listview'){
		$srhcontent .= '</div>';
		}else{
		$srhcontent .= '</div>
                        </div>';
		}
		echo $srhcontent;
		?>

</div>
<?php
	if($flag == 0){
	$numberofpages = (isset($_GET['numberofpages'])) ? sanitize_text_field($_GET['numberofpages']) : '';
	if($numberofpages != ""){
	$per_page = $numberofpages;
	}else{
	$per_page = ($service_finder_options['srh-per-page'] > 0) ? $service_finder_options['srh-per-page'] : 12;
	}
	
    $no_of_paginations = ceil($count / $per_page);
	
	$args = array(
		'base'               => '%_%',
		'format'             => '?pagenum=%#%',
		'total'              => $no_of_paginations,
		'current'            => (isset($_GET['pagenum'])) ? sanitize_text_field($_GET['pagenum']) : 1,
		'show_all'           => true,
		'end_size'           => 1,
		'mid_size'           => 2,
		'prev_next'          => true,
		'prev_text'          => '<i class="fa fa-angle-left"></i>',
		'next_text'          => '<i class="fa fa-angle-right"></i>',
		'type'               => 'plain',
		'add_args'           => false,
		'add_fragment'       => '',
		'before_page_number' => '',
		'after_page_number'  => ''
	); 
	echo '<div class="pagination-bx pagination col-lg-12 clearfix">';
	
	$pagenum = (isset($_GET['pagenum'])) ? sanitize_text_field($_GET['pagenum']) : '';
	$getUrl = explode("?",esc_url($_SERVER['REQUEST_URI']));
	$qryString = (!empty($getUrl[1])) ? $getUrl[1] : '';

	if($pagenum > 1){
	$firstpageurl = str_replace('pagenum='.$pagenum,'pagenum=1',$qryString);
	echo '<a href="?'.$firstpageurl.'" class="first page-numbers"><i class="fa fa-angle-double-left"></i></a>';
	}

	echo paginate_links( $args );

	if($pagenum < $no_of_paginations){
		if($pagenum > 0){
		$lastpageurl = str_replace('pagenum='.$pagenum,'pagenum='.$no_of_paginations,$qryString);
		}else{
			if($qryString != ""){
				$lastpageurl = 'pagenum='.$no_of_paginations.'&'.$qryString;
			}else{
				$lastpageurl = 'pagenum='.$no_of_paginations;
			}
		}
		if($pagenum > 1){
		echo '<a href="?'.$lastpageurl.'" class="last page-numbers"><i class="fa fa-angle-double-right"></i></a>';
		}
	}

	echo '</div>';
	}
	?>
<!-- result END -->
<?php }elseif($service_finder_options['search-style'] == 'ajax-with-url-change'){?>
<!--Ajax Search with URL Change-->
<div class="col-md-12 content">
  <div class = "inner-box content no-right-margin darkviolet">
  <?php
  $viewtype = (!empty($_GET["viewtype"])) ? esc_js($_GET["viewtype"]) : '';
  $numberofpages = (!empty($_GET["numberofpages"])) ? esc_js($_GET["numberofpages"]) : '';
  $setorderby = (!empty($_GET["setorderby"])) ? esc_js($_GET["setorderby"]) : '';
  $setorder = (!empty($_GET["setorder"])) ? esc_js($_GET["setorder"]) : '';
  wp_add_inline_script( 'bootstrap', 'jQuery(document).ready(function($) {
                //Load category result function
				var get_permalink = "'.service_finder_using_permalink().'";
				function service_finder_loadCitiesResult(page,viewtype,numberofpages,setorderby,setorder){
                    // Start the transition
					var data = {
						page: page,
                        action: "load-city-result",
						cityid: "'.esc_js(get_queried_object()->term_id).'",
						viewtype: viewtype,
						numberofpages: numberofpages,
						setorderby: setorderby,
						setorder: setorder
                    };
						
				  var formdata = jQuery.param(data);
				  
				 
				  jQuery.ajax({
			
									type: "POST",
			
									url: ajaxurl,
			
									data: formdata,
									
									dataType: "json",
									
									beforeSend: function() {
										jQuery(".loading-area").show();
									},
			
									success:function (data, textStatus) {
										jQuery(".loading-area").hide();
										jQuery(".cvf_universal_container").html(data["result"]);
										jQuery(".display-ratings").rating();
										jQuery(".sf-show-rating").show();
										equalheight(".equal-col-outer .equal-col");
										jQuery("[data-tool=\"tooltip\"]").tooltip();
									}
			
								});
                   
                }
				
               	// HTML5 History API used to get content on Back and Prev button
				jQuery(window).bind("popstate", function() {
						
						var urlParams;
						
						var match,
								pl     = /\+/g,  // Regex for replacing addition symbol with a space
								search = /([^&=]+)=?([^&]*)/g,
								decode = function (s) { return decodeURIComponent(s.replace(pl, " ")); },
								query  = window.location.search.substring(1);
						
							urlParams = {};
							while (match = search.exec(query))
							   urlParams[decode(match[1])] = decode(match[2]);

						
						if(urlParams["cityid"] > 0){
							var data = {

								page: urlParams["pagenum"],
		
								action: "load-city-result",
		
								cityid: urlParams["cityid"],
		
								viewtype: urlParams["viewtype"],
		
								numberofpages: urlParams["numberofpages"],
		
								setorderby: urlParams["setorderby"],
		
								setorder: urlParams["setorder"]
		
							};
							
						}else{
							var data = {

								page: 1,
		
								action: "load-city-result",
		
								cityid: "'.esc_js(get_queried_object()->term_id).'",

								viewtype: "'.esc_js($viewtype).'",
		
								numberofpages: "'.esc_js($numberofpages).'",
		
								setorderby: "'.esc_js($setorderby).'",
		
								setorder: "'.esc_js($setorder).'"
		
							};
						}
						
				  var formdata = jQuery.param(data);
				  jQuery.ajax({
			
									type: "POST",
			
									url: ajaxurl,
			
									data: formdata,
									
									dataType: "json",
			
									success:function (data, textStatus) {
									jQuery(".cvf_universal_container").html(data["result"]);
										jQuery(".display-ratings").rating();
										jQuery(".sf-show-rating").show();
										jQuery("[data-tool=\"tooltip\"]").tooltip();
									}
			
								});
				});
				
                // Load page 1 as the default
				var viewtype = "'.$viewtype.'";
				if(viewtype == ""){
				var viewtype = jQuery("#viewTypes li.active").attr("data-view");
				}
				
				var numberofpages = "'.$numberofpages.'";
				var setorderby = "'.$setorderby.'";
				var setorder = "'.$setorder.'";

				var urlParams;
						
						var match,
								pl     = /\+/g,  // Regex for replacing addition symbol with a space
								search = /([^&=]+)=?([^&]*)/g,
								decode = function (s) { return decodeURIComponent(s.replace(pl, " ")); },
								query  = window.location.search.substring(1);
						
							urlParams = {};
							while (match = search.exec(query))
							   urlParams[decode(match[1])] = decode(match[2]);
							   
				
				if(urlParams["pagenum"] > 0){
				var startpage = urlParams["pagenum"];
				}else{
				var startpage = 1;
				}
				if(get_permalink){
					var baseurl = "'.esc_js(home_url("/city/".get_query_var("sf-cities"))).'";
					var pageurl = baseurl+"?cityid='.esc_js(get_queried_object()->term_id).'&pagenum="+startpage+"&viewtype="+viewtype+"&numberofpages="+numberofpages+"&setorderby="+setorderby+"&setorder="+setorder;
						}else{
						var baseurl = "'.esc_js(home_url("/")."?city=".get_query_var("sf-cities")).'";
						var pageurl = baseurl+"&cityid='.esc_js(get_queried_object()->term_id).'&pagenum="+startpage+"&viewtype="+viewtype+"&numberofpages="+numberofpages+"&setorderby="+setorderby+"&setorder="+setorder;
					}
				 if(pageurl!=window.location){
						window.history.pushState({path:pageurl},"",pageurl);
				}
				service_finder_loadCitiesResult(startpage,viewtype,numberofpages,setorderby,setorder);
               
                // Handle the clicks
				jQuery("body").on("click", ".cvf_universal_container .cvf-universal-pagination li.activelink a", function(){
                    var page = jQuery(this).parents("li").attr("data-pnum");
					viewtype = jQuery("#viewTypes li.active").attr("data-view");
					var setorderby = jQuery("#setorderby option:selected").val();
					var setorder = jQuery("#setorder option:selected").val();
					var numberofpages = jQuery("#numberofpages option:selected").val();
					
					if(get_permalink){
					var baseurl = "'.esc_js(home_url("/city/".get_query_var("sf-cities"))).'";
					var pageurl = baseurl+"?cityid='.esc_js(get_queried_object()->term_id).'&pagenum="+page+"&viewtype="+viewtype+"&numberofpages="+numberofpages+"&setorderby="+setorderby+"&setorder="+setorder;
						}else{
						var baseurl = "'.esc_js(home_url("/")."?city=".get_query_var("sf-cities")).'";
						var pageurl = baseurl+"&cityid='.esc_js(get_queried_object()->term_id).'&pagenum="+page+"&viewtype="+viewtype+"&numberofpages="+numberofpages+"&setorderby="+setorderby+"&setorder="+setorder;
					}
					
					 if(pageurl!=window.location){
						window.history.pushState({path:pageurl},"",pageurl);
					}
					  
					
                    service_finder_loadCitiesResult(page,viewtype,numberofpages,setorderby,setorder);
                   
                });
				
				// Handle the view types
				jQuery("body").on("click", "#viewTypes li", function(){
					jQuery("#viewTypes li").removeClass("active");
                    jQuery(this).addClass("active");
					viewtype = jQuery(this).attr("data-view");
					var page = jQuery(".cvf_universal_container .cvf-universal-pagination li.selected").attr("data-pnum");
					
					var setorderby = jQuery("#setorderby option:selected").val();
					var setorder = jQuery("#setorder option:selected").val();
					var numberofpages = jQuery("#numberofpages option:selected").val();
					
					if(get_permalink){
					var baseurl = "'.esc_js(home_url("/city/".get_query_var("sf-cities"))).'";
					var pageurl = baseurl+"?cityid='.esc_js(get_queried_object()->term_id).'&pagenum="+page+"&viewtype="+viewtype+"&numberofpages="+numberofpages+"&setorderby="+setorderby+"&setorder="+setorder;
						}else{
						var baseurl = "'.esc_js(home_url("/")."?city=".get_query_var("sf-cities")).'";
						var pageurl = baseurl+"&cityid='.esc_js(get_queried_object()->term_id).'&pagenum="+page+"&viewtype="+viewtype+"&numberofpages="+numberofpages+"&setorderby="+setorderby+"&setorder="+setorder;
					}
					

					
					if(pageurl!=window.location){
						window.history.pushState({path:pageurl},"",pageurl);
					}
					service_finder_loadCitiesResult(page,viewtype,numberofpages,setorderby,setorder);
                   
                });
				//Set number of pages
				jQuery("body").on("change", "#numberofpages", function(){
						var page = jQuery(".cvf_universal_container .cvf-universal-pagination li.selected").attr("data-pnum");
						var viewtype = jQuery("#viewTypes li.active").attr("data-view");
						
						var numberofpages = jQuery(this).val();
						var setorderby = jQuery("#setorderby option:selected").val();
						var setorder = jQuery("#setorder option:selected").val();
						
						if(get_permalink){
						var baseurl = "'.esc_js(home_url("/city/".get_query_var("sf-cities"))).'";
						var pageurl = baseurl+"?cityid='.esc_js(get_queried_object()->term_id).'&pagenum=1&viewtype="+viewtype+"&numberofpages="+numberofpages+"&setorderby="+setorderby+"&setorder="+setorder;
							}else{
							var baseurl = "'.esc_js(home_url("/")."?city=".get_query_var("sf-cities")).'";
							var pageurl = baseurl+"&cityid='.esc_js(get_queried_object()->term_id).'&pagenum=1&viewtype="+viewtype+"&numberofpages="+numberofpages+"&setorderby="+setorderby+"&setorder="+setorder;
						}
						
						if(pageurl!=window.location){
							window.history.pushState({path:pageurl},"",pageurl);
						}
						service_finder_loadCitiesResult(1,viewtype,numberofpages,setorderby,setorder);
				});
				//Set order by
				jQuery("body").on("change", "#setorderby", function(){
						var page = jQuery(".cvf_universal_container .cvf-universal-pagination li.selected").attr("data-pnum");
						var viewtype = jQuery("#viewTypes li.active").attr("data-view");
						
						var setorderby = jQuery(this).val();
						var numberofpages = jQuery("#numberofpages option:selected").val();
						var setorder = jQuery("#setorder option:selected").val();
						
						if(get_permalink){
						var baseurl = "'.esc_js(home_url("/city/".get_query_var("sf-cities"))).'";
						var pageurl = baseurl+"?cityid='.esc_js(get_queried_object()->term_id).'&pagenum="+page+"&viewtype="+viewtype+"&numberofpages="+numberofpages+"&setorderby="+setorderby+"&setorder="+setorder;
							}else{
							var baseurl = "'.esc_js(home_url("/")."?city=".get_query_var("sf-cities")).'";
							var pageurl = baseurl+"&cityid='.esc_js(get_queried_object()->term_id).'&pagenum="+page+"&viewtype="+viewtype+"&numberofpages="+numberofpages+"&setorderby="+setorderby+"&setorder="+setorder;
						}
						
						if(pageurl!=window.location){
							window.history.pushState({path:pageurl},"",pageurl);
						}
						service_finder_loadCitiesResult(page,viewtype,numberofpages,setorderby,setorder);
				});
				//Set order
				jQuery("body").on("change", "#setorder", function(){
						var page = jQuery(".cvf_universal_container .cvf-universal-pagination li.selected").attr("data-pnum");
						var viewtype = jQuery("#viewTypes li.active").attr("data-view");
						
						var setorder = jQuery(this).val();
						var numberofpages = jQuery("#numberofpages option:selected").val();
						var setorderby = jQuery("#setorderby option:selected").val();
						if(get_permalink){
						var baseurl = "'.esc_js(home_url("/city/".get_query_var("sf-cities"))).'";
						var pageurl = baseurl+"?cityid='.esc_js(get_queried_object()->term_id).'&pagenum="+page+"&viewtype="+viewtype+"&numberofpages="+numberofpages+"&setorderby="+setorderby+"&setorder="+setorder;
							}else{
							var baseurl = "'.esc_js( home_url("/")."?city=".get_query_var("sf-cities")).'";
							var pageurl = baseurl+"&cityid='.esc_js(get_queried_object()->term_id).'&pagenum="+page+"&viewtype="+viewtype+"&numberofpages="+numberofpages+"&setorderby="+setorderby+"&setorder="+setorder;
						}
						
						if(pageurl!=window.location){
							window.history.pushState({path:pageurl},"",pageurl);
						}
						service_finder_loadCitiesResult(page,viewtype,numberofpages,setorderby,setorder);
				});
				           
            });', 'after' );
  ?>
  </div>
</div>
<div class="title-section">
  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-5">
      <?php if($totalresult > 0){ ?>
      <h2 class="result-title">
      <?php 
	  $allowedhtml = array(
			'strong' => array()
		);
	  printf( wp_kses( '<strong>%s</strong> ', $allowedhtml ), $totalresult ); echo esc_html__('Results Found','service-finder');
	  ?>
      </h2>
      <?php } ?>
    </div>
    <?php
    $setorderby = (isset($_GET['setorderby'])) ? esc_html($_GET['setorderby']) : '';
	$setorder = (isset($_GET['setorder'])) ? esc_html($_GET['setorder']) : '';
	$numberofpages = (isset($_GET['numberofpages'])) ? esc_html($_GET['numberofpages']) : '';
	$viewtype = (isset($_GET['viewtype'])) ? esc_html($_GET['viewtype']) : $defaultview;
	echo $defaultview;
	echo 'hellovkas';
	?>
    <div class="col-md-7 col-sm-7 col-xs-7">
      <?php echo do_action('service_finder_category_filter',$setorderby,$setorder,$numberofpages,$viewtype,$defaultview); ?>
    </div>
  </div>
</div>
<!-- Display Category Result -->
<div class="section-content">
    <div class = "cvf_pag_loading">
      <div class = "cvf_universal_container">
        <div class="cvf-universal-content"></div>
      </div>
    </div>
</div>
<!-- result END -->
<?php } ?>
