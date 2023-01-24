<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/

/*Load Category Results*/	
add_action( 'wp_ajax_load-category-result', 'service_finder_load_category_result' );
add_action( 'wp_ajax_nopriv_load-category-result', 'service_finder_load_category_result' );

function service_finder_load_category_result() {
   
   global $service_finder_Params, $wpdb, $service_finder_options, $service_finder_Tables;
    //Include Provider category Class
    require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/category/providersCategory.php';
	if($_POST['page'] != ""){
	$page = sanitize_text_field($_POST['page']);
	}else{
	$page = 1;
	}
	
	if($_POST['numberofpages'] != ""){
	$per_page = $_POST['numberofpages'];
	}else{
	$srhperpage = (!empty($service_finder_options['srh-per-page'])) ? $service_finder_options['srh-per-page'] : '';
	$per_page = ($srhperpage > 0) ? $srhperpage : 12;
	}
	
	if($_POST['setorderby'] != ""){
	$orderby = $_POST['setorderby'];
	}else{
	$orderby = 'id';
	}
	
	if($_POST['setorder'] != ""){
	$order = $_POST['setorder'];
	}else{
	$order = 'desc';
	}
	
	$cur_page = $page;
	$page -= 1;
	// Set the number of results to display
	
	
	$previous_btn = true;
	$next_btn = true;
	$first_btn = true;
	$last_btn = true;
	$start = $page * $per_page;
   $getProviders = new SERVICE_FINDER_providersCategory();
	
   $providersInfoArr = $getProviders->service_finder_getProvidersCategory(esc_attr($_POST['catid']),$start,$per_page,$orderby,$order);
   $providersInfo = $providersInfoArr['result'];
   $count = $providersInfoArr['count'];
   $msg = '';
	$flag = 0;
	if(!empty($providersInfo)){ 
		
		    if($_POST['viewtype'] == 'listview'){
			$msg .= '<div class="listing-box row">';
			}elseif($_POST['viewtype'] == 'grid-4'){
			$msg .= '<div class="listing-grid-box sf-listing-grid-4 equal-col-outer">
							<div class="row">';
			}elseif($_POST['viewtype'] == 'grid-3'){
			$msg .= '<div class="listing-grid-box sf-listing-grid-3 equal-col-outer">
							<div class="row">';
			}else{
			$msg .= '<div class="listing-grid-box sf-listing-grid-4 equal-col-outer">
							<div class="row">';
			}				  
			  
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
	  	$msg .= service_finder_display_provider_boxes($provider->wp_user_id,$_POST['viewtype'],false);
	  }elseif(service_finder_themestyle() == 'style-4'){
		$msg .= service_finder_provider_box_fourth($provider->wp_user_id,$_POST['viewtype'],false);
	  }else{
	  if($_POST['viewtype'] == 'grid-4'){
			/*4 grid layout*/
			if(service_finder_themestyle() == 'style-2'){
			$msg .= '<div class="col-md-3 col-sm-6 equal-col">
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
			$msg .= '<div class="col-md-3 col-sm-6 equal-col">

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
            
			}elseif($_POST['viewtype'] == 'listview'){
      /*List View layout*/
	  if(service_finder_themestyle() == 'style-2'){
			$msg .= '<div class="sf-featured-listing clearfix">
                            
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
      $msg .= '
      <div class="col-md-12">
        <div class="sf-element-bx result-listing clearfix">
          <div class="sf-thum-bx sf-listing-thum img-effect2" style="background-image:url('.esc_url($src).');"> <a href="'.esc_url($link).'" class="sf-listing-link"></a>
            <div class="overlay-bx">
              '.$addressbox.'
            </div>
            '.service_finder_get_primary_category_tag($provider->wp_user_id).' '.$featured.''.service_finder_check_varified_icon($provider->wp_user_id).' </div>
          <div class="result-text '.service_finder_check_varified($provider->wp_user_id).'" id="proid-'.$provider->wp_user_id.'">
            <h5 class="sf-title">'.service_finder_getExcerpts(service_finder_getCompanyName($provider->wp_user_id),0,30).'</h5>
            <strong class="sf-company-name"><a href="'.esc_url($link).'">'.service_finder_getExcerpts($provider->full_name,0,35).'</a></strong> '.service_finder_show_provider_meta($provider->wp_user_id,$provider->phone,$provider->mobile).''.service_finder_displayRating(service_finder_getAverageRating($provider->wp_user_id)).'
            <div class="sf-address2-bx"> <i class="fa fa-map-marker"></i> '.service_finder_getshortAddress($provider->wp_user_id).' </div>
            <p>'.service_finder_getExcerpts($provider->bio,0,300).'</p>
            '.$addtofavorite.' </div>
        </div>
      </div>
      ';
	  }
      }elseif($_POST['viewtype'] == 'grid-3'){
			
			if(service_finder_themestyle() == 'style-2'){
			$msg .= '<div class="col-md-4 col-sm-6 equal-col">
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
			/*3 grid layout*/            
		    $msg .= '<div class="col-md-4 col-sm-6 equal-col">
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
			/*4 grid layout*/
			if(service_finder_themestyle() == 'style-2'){
			$msg .= '<div class="col-md-3 col-sm-6 equal-col">
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
			$msg .= '<div class="col-md-3 col-sm-6 equal-col">

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
      
      if($_POST['viewtype'] == 'listview'){
      $msg .= '</div>
    ';
    }else{
    $msg .= '</div>
</div>
';
		}

	}else{
	/*No Result Found*/
		$msg .= '
<div class="sf-nothing-found2"> <strong class="sf-tilte">'.esc_html__('Nothing Found', 'service-finder').'</strong>
  <p>'.esc_html__('Apologies, but no results were found for the request.', 'service-finder').'</p>
</div>
';
		$flag = 1;		
	
	}
	
	 // Optional, wrap the output into a container
        $msg = "
<div class='cvf-universal-content'>" . $msg . "</div>
<br class = 'clear' />
";
       
        $no_of_paginations = ceil($count / $per_page);

        if ($cur_page >= 7) {
            $start_loop = $cur_page - 3;
            if ($no_of_paginations > $cur_page + 3)
                $end_loop = $cur_page + 3;
            else if ($cur_page <= $no_of_paginations && $cur_page > $no_of_paginations - 6) {
                $start_loop = $no_of_paginations - 6;
                $end_loop = $no_of_paginations;
            } else {
                $end_loop = $no_of_paginations;
            }
        } else {
            $start_loop = 1;
            if ($no_of_paginations > 7)
                $end_loop = 7;
            else
                $end_loop = $no_of_paginations;
        }
		
		if(service_finder_themestyle() == 'style-4'){
			$pag_container = "";
			$pag_container .= "<div class='cvf-universal-pagination pagination-two pagination-center clearfix'>
				<ul class='pagination'>";
	
			if ($previous_btn && $cur_page > 1) {
				$pre = $cur_page - 1;
				$pag_container .= "<li data-pnum='$pre' class='activelink'><a href='javascript:;'><i class='fa fa-chevron-left'></i></a></li>";
			} else if ($previous_btn) {
				$pag_container .= "<li class='inactive'><a href='javascript:;'><i class='fa fa-chevron-left'></i></a></li>";
			}
			for ($i = $start_loop; $i <= $end_loop; $i++) {
	
				if ($cur_page == $i)
					$pag_container .= "<li data-pnum='$i' class = 'selected active' ><a href='javascript:;'>{$i}</a></li>";
				else
					$pag_container .= "<li data-pnum='$i' class='activelink'><a href='javascript:;'>{$i}</a></li>";
			}
		   
			if ($next_btn && $cur_page < $no_of_paginations) {
				$nex = $cur_page + 1;
				$pag_container .= "<li data-pnum='$nex' class='activelink'><a href='javascript:;'><i class='fa fa-chevron-right'></i></a></li>";
			} else if ($next_btn) {
				$pag_container .= "<li class='inactive'><a href='javascript:;'><i class='fa fa-chevron-right'></i></a></li>";
			}
	
			$pag_container = $pag_container . "
				</ul>
			</div>";
		}else{
		// Pagination Buttons logic    
		$pag_container = "";
        $pag_container .= "
			<div class='cvf-universal-pagination pagination clearfix'>
			  <ul class='pagination'>
				";
				
				if ($first_btn && $cur_page > 1) {
				$pag_container .= "
				<li data-pnum='1' class='activelink'><a href='javascript:;'><i class='fa fa-angle-double-left'></i></a></li>
				";
				} else if ($first_btn) {
				$pag_container .= "
				<li data-pnum='1' class='inactive'><a href='javascript:;'><i class='fa fa-angle-double-left'></i></a></li>
				";
				}
				
				if ($previous_btn && $cur_page > 1) {
				$pre = $cur_page - 1;
				$pag_container .= "
				<li data-pnum='$pre' class='activelink'><a href='javascript:;'><i class='fa fa-angle-left'></i></a></li>
				";
				} else if ($previous_btn) {
				$pag_container .= "
				<li class='inactive'><a href='javascript:;'><i class='fa fa-angle-left'></i></a></li>
				";
				}
				for ($i = $start_loop; $i <= $end_loop; $i++) {
				
				if ($cur_page == $i)
				$pag_container .= "
				<li data-pnum='$i' class = 'selected active' ><a href='javascript:;'>{$i}</a></li>
				";
				else
				$pag_container .= "
				<li data-pnum='$i' class='activelink'><a href='javascript:;'>{$i}</a></li>
				";
				}
				
				if ($next_btn && $cur_page < $no_of_paginations) {
				$nex = $cur_page + 1;
				$pag_container .= "
				<li data-pnum='$nex' class='activelink'><a href='javascript:;'><i class='fa fa-angle-right'></i></a></li>
				";
				} else if ($next_btn) {
				$pag_container .= "
				<li class='inactive'><a href='javascript:;'><i class='fa fa-angle-right'></i></a></li>
				";
				}
				
				if ($last_btn && $cur_page < $no_of_paginations) {
				$pag_container .= "
				<li data-pnum='$no_of_paginations' class='activelink'><a href='javascript:;'><i class='fa fa-angle-double-right'></i></a></li>
				";
				} else if ($last_btn) {
				$pag_container .= "
				<li data-pnum='$no_of_paginations' class='inactive'><a href='javascript:;'><i class='fa fa-angle-double-right'></i></a></li>
				";
				}
				
				$pag_container = $pag_container . "
			  </ul>
			</div>
			";
		}       
        
       
         if($flag == 1){
			$result = '
<div class = "cvf-pagination-content">' . $msg . '</div>
';
		}else{
	        $result = '
<div class = "cvf-pagination-content">' . $msg . '</div>
' .
    	    '
<div class = "cvf-pagination-nav">' . $pag_container . '</div>
';
		}
		
		$start = $page * $per_page;
		$start = $start + 1;
		$end = $start + $per_page;
		
		if($count <= $end){
		$end = $count;
		}
		
		$start = ($count > 0) ? $start : 0;
		
		if($count > 0){
		$counttext = $count.' '.esc_html__( 'Results Found', 'service-finder' );
		}else{
		$counttext = esc_html__( 'No Results Found', 'service-finder' );
		}
		
		$resarr = array(
					'result' => $result,
					'count' => $count,
					'counttext' => $counttext,
					'startpagenum' => $start,
					'endresultnum' => $end
				);
		
		echo json_encode($resarr);		
		
	
    exit();
}