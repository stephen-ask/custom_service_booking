<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/
$service_finder_options = get_option('service_finder_options');
$wpdb = service_finder_plugin_global_vars('wpdb');
$service_finder_Tables = service_finder_plugin_global_vars('service_finder_Tables');
$searchAddress = (isset($_REQUEST['searchAddress'])) ? $_REQUEST['searchAddress'] : '';
$keyword = (isset($_REQUEST['keyword'])) ? $_REQUEST['keyword'] : '';
$identitycheck = (isset($service_finder_options['identity-check'])) ? esc_attr($service_finder_options['identity-check']) : '';
$restrictuserarea = (isset($service_finder_options['restrict-user-area'])) ? esc_attr($service_finder_options['restrict-user-area']) : '';
$providerreplacestring = (!empty($service_finder_options['provider-replace-string'])) ? $service_finder_options['provider-replace-string'] : esc_html__('Provider', 'service-finder');	

$searchbtntext = (!empty($service_finder_options['search-btn-text'])) ? esc_attr($service_finder_options['search-btn-text']) : esc_html__('Find', 'service-finder').' '.esc_html($providerreplacestring);


$minpricerange = 0;
$maxpricerange = (isset($service_finder_options['search-max-price'])) ? esc_attr($service_finder_options['search-max-price']) : '1000';

$minradiusrange = 0;
$maxradiusrange = (isset($service_finder_options['search-max-radius'])) ? esc_attr($service_finder_options['search-max-radius']) : '1000';

$radiussearchunit = (isset($service_finder_options['radius-search-unit'])) ? esc_attr($service_finder_options['radius-search-unit']) : 'mi';
if($radiussearchunit == 'km'){
$radiusunit = esc_html__( ' Km', 'service-finder' );
}else{
$radiusunit = esc_html__( ' Mi.', 'service-finder' );
}

$html = '';
$searchoptions = array();
if($service_finder_options['search-keyword']){
	$searchoptions[] = $service_finder_options['search-keyword'];
}
if($service_finder_options['search-category']){
	$searchoptions[] = $service_finder_options['search-category'];
}
if($service_finder_options['search-country']){
	$searchoptions[] = $service_finder_options['search-country'];
}
if($service_finder_options['search-city']){
	$searchoptions[] = $service_finder_options['search-city'];
}
if($service_finder_options['search-address']){
	$searchoptions[] = $service_finder_options['search-address'];
}
$optionsfields = count($searchoptions) + 1;
switch($optionsfields){
case 2:
	$fieldclass = 'col-md-5 col-sm-6 search-col-3';
	$submitclass = 'col-md-2 col-sm-12 search-col-3';
	break;
case 3:
	$fieldclass = 'col-md-5 col-sm-6 search-col-3';
	$submitclass = 'col-md-2 col-sm-12 search-col-3';
	break;
case 4:
	$fieldclass = 'col-md-3 col-sm-3 search-col-4';
	$submitclass = 'col-md-3 col-sm-12 search-col-4';
	break;
case 5:
	$fieldclass = 'col-md-3  col-sm-3 search-col-5';
	$submitclass = 'col-md-3 col-sm-12 search-col-5';
	break;
case 6:
	$fieldclass = 'col-md-2 col-sm-4 search-col-6';
	$submitclass = 'col-md-2 col-md-4 col-sm-12 search-col-6';
	break;
}
?>
<!--Search Form Template for HEader section on each page-->
<?php if($optionsfields > 1){ 
$html = '<form class="clearfix search-providers" method="get" action="'.home_url('/').'">
  <input type="hidden" name="s" value="">';
  $html .= '<div class="sf-search-form-element">';
  
  if($service_finder_options['search-keyword']):
  $html .= '<div class="'.$fieldclass.'">
    <div class="type-address">
      <input type="text" value="'.esc_attr($keyword).'" placeholder="'.esc_html__('Keyword', 'service-finder').'" id="keyword" name="keyword" class="form-control">
    </div>
  </div>';
  endif;
  if($service_finder_options['search-address']):
  $html .= '<div class="'.$fieldclass.'">
    <div class="type-keyword">
      <input type="text" value="'.esc_attr($searchAddress).'" placeholder="'.esc_html__('Address', 'service-finder').'" id="searchAddress2" name="searchAddress" class="form-control">
    </div>
  </div>';
  endif;
  if($service_finder_options['search-category']){
  if($service_finder_options['show-empty-category'] == 'yes'){
  $html .= '<div class="'.$fieldclass.'">
    <div class="category-select">
      <select id="categorysrh" name="catid" class="sf-select-box form-control sf-form-control" title="'.esc_html__('Category', 'service-finder').'" data-live-search="true" data-header="'.esc_html__('Select a Category', 'service-finder').'">
        <option value="">
        '.esc_html__('Select a Category', 'service-finder').'
        </option>';
        
		if(class_exists('service_finder_texonomy_plugin')){
                    $limit = 1000;
                    $categories = service_finder_getCategoryList($limit);
					$texonomy = 'providers-category';
					$catid = (isset($_REQUEST['catid'])) ? $_REQUEST['catid'] : '';
					
                    if(!empty($categories)){
                        foreach($categories as $category){
						$term_id = (!empty($category->term_id)) ? $category->term_id : '';
						$term_name = (!empty($category->name)) ? $category->name : '';
						if($catid == $term_id){
							$select = 'selected="selected"';
						}else{
							$select = '';
						}
						
						$catimage =  service_finder_getCategoryImage($term_id,'service_finder-category-small');
                        $html .= '<option '.$select.' value="'.esc_attr($term_id).'" data-content="<span>'.esc_attr($term_name).'</span>">'. $term_name.'</option>';
						
						$term_children = get_term_children($term_id,$texonomy);
						$namearray = array();
						if(!empty($term_children)){
						    foreach ($term_children as $child) {
								$term = get_term_by( 'id', $child, $texonomy );
					
								$namearray[$term->name]= $child;
				
							}
							
							if(!empty($namearray)){
							ksort($namearray);
						
							foreach($namearray as $key => $value) {
								$term_child_id = $value;
								$term_child = get_term_by('id',$term_child_id,$texonomy);
								$term_child_name = (!empty($term_child->name)) ? $term_child->name : '';
								if($catid == $term_child_id){
									$childselect = 'selected="selected"';
								}else{
									$childselect = '';
								}
								
								$catimage =  service_finder_getCategoryImage($term_child_id,'service_finder-category-small');
								if($catimage != ""){
								$imgtag = "<img class='childcat-img' width='50' height='auto' src=". esc_url($catimage).">";
								$html .= '<option '.$childselect.' value="'.esc_attr($term_child_id).'" data-content="'.$imgtag.'<span class=\'childcat\'>'.esc_attr($term_child_name).'</span>">'. $term_child_name.'</option>';
								}else{
								$html .= '<option '.$childselect.' value="'.esc_attr($term_child_id).'" data-content="<span class=\'childcat\'>'.esc_attr($term_child_name).'</span>">'. $term_child_name.'</option>';
								}
								
							}
							}
						}
						
                        
                        }
                    }	
		}			
      $html .= '</select>
    </div>
  </div>';
  }else{
  $html .= '<div class="'.$fieldclass.'">
    <div class="category-select">
      <select id="categorysrh" name="catid" class="sf-select-box form-control sf-form-control" title="'.esc_html__('Category', 'service-finder').'" data-live-search="true" data-header="'.esc_html__('Select a Category', 'service-finder').'">
        <option value="">
        '.esc_html__('Select a Category', 'service-finder').'
        </option>';
        
		if(class_exists('service_finder_texonomy_plugin')){
                    $limit = 1000;
                    $categories = service_finder_getCategoryList($limit);
					
					$texonomy = 'providers-category';
					$catid = (isset($_REQUEST['catid'])) ? $_REQUEST['catid'] : '';
					
                    if(!empty($categories)){
                        foreach($categories as $category){
						$term_id = (!empty($category->term_id)) ? $category->term_id : '';
						$term_name = (!empty($category->name)) ? $category->name : '';
						
						if($catid == $term_id){
							$select = 'selected="selected"';
						}else{
							$select = '';
						}
						
						$catimage =  service_finder_getCategoryImage($term_id,'service_finder-category-small');
                        $havechilde = false;
						
						if(!service_finder_check_empty_category($term_id)){
							$havechilde = true;
						}else{
						$term_children = get_term_children($term_id,$texonomy);
						if(!empty($term_children)){
						    foreach ($term_children as $child) {
								if(!service_finder_check_empty_category($child)){
									$havechilde = true;
								}
				
							}
						}	
						}
						
						if($havechilde){
						$html .= '<option '.$select.' value="'.esc_attr($term_id).'" data-content="<span>'.esc_attr($term_name).'</span>">'. $term_name.'</option>';
						}
						
						
						$namearray = array();
						if(!empty($term_children)){
						    foreach ($term_children as $child) {
								$term = get_term_by( 'id', $child, $texonomy );
					
								$namearray[$term->name]= $child;
				
							}
							
							if(!empty($namearray)){
							ksort($namearray);
						
							foreach($namearray as $key => $value) {
								$term_child_id = $value;
								$term_child = get_term_by('id',$term_child_id,$texonomy);
								$term_child_name = (!empty($term_child->name)) ? $term_child->name : '';
								
								if(!service_finder_check_empty_category($term_child_id)){
								
								if($catid == $term_child_id){
									$childselect = 'selected="selected"';
								}else{
									$childselect = '';
								}
								
								$catimage =  service_finder_getCategoryImage($term_child_id,'service_finder-category-small');
								if($catimage != ""){
								$imgtag = "<img class='childcat-img' width='50' height='auto' src=". esc_url($catimage).">";
								$html .= '<option '.$childselect.' value="'.esc_attr($term_child_id).'" data-content="'.$imgtag.'<span class=\'childcat\'>'.esc_attr($term_child_name).'</span>">'. $term_child_name.'</option>';
								}else{
								$html .= '<option '.$childselect.' value="'.esc_attr($term_child_id).'" data-content="<span class=\'childcat\'>'.esc_attr($term_child_name).'</span>">'. $term_child_name.'</option>';
								}
								}
								
							}
							}
						}
                        
                        }
                    }	
		}			
      $html .= '</select>
    </div>
  </div>';
  }
  }
  if($service_finder_options['search-country']):
  $html .= '<div class="'.$fieldclass.'">
    <div class="select-country">';
               if($restrictuserarea && $identitycheck){
			   $qry = "select DISTINCT country from ".$service_finder_Tables->providers." WHERE admin_moderation = 'approved' AND identity = 'approved' AND account_blocked != 'yes' ORDER BY `country`";
			   }else{
			   $qry = "select DISTINCT country from ".$service_finder_Tables->providers." WHERE admin_moderation = 'approved' AND account_blocked != 'yes' ORDER BY `country`";
			   }
			   
			    $maincountries = $wpdb->get_results($qry);
				
				$qry = "select DISTINCT country from ".$service_finder_Tables->branches." ORDER BY `country`";
				
				$branchcountries = $wpdb->get_results($qry);
				
                $html .= '<select class="sf-select-box form-control sf-form-control" data-live-search="true" name="country" id="country" title="'.esc_html__('Country','service-finder').'" data-header="'.esc_html__('Select a Country','service-finder').'">';
                $html .= '<option value="">'.esc_html__('Select Country','service-finder').'</option>';
				
				$allcountry = array();
				
				if(!empty($maincountries)){
                foreach($maincountries as $country){
					$allcountry[] = $country->country;
				}
				}
				
				if(!empty($branchcountries)){
                foreach($branchcountries as $country){
					$allcountry[] = $country->country;
				}
				}
                
				$allcountry = array_unique($allcountry);
				sort($allcountry);
						
				if(!empty($allcountry)){
				$getcountry = (isset($_REQUEST['country'])) ? $_REQUEST['country'] : '';
                foreach($allcountry as $country){
				$countryvar = (!empty($country)) ? $country : '';
                   if($getcountry != ""){
				    if($getcountry == $countryvar){
						$select = 'selected="selected"';
					}else{
						$select = '';
					}
				   }elseif(!empty($service_finder_options['default-country'])){
				   	if($service_finder_options['default-country'] == $countryvar){
						$select = 'selected="selected"';
					}else{
						$select = '';
					}
				   }	
					$html .= '<option '.$select.' value="'.esc_attr($countryvar).'">'.$countryvar.'</option>';
                }
                }else{
                    $html .= '<option value="">'.esc_html__('No country available','service-finder').'</option>';
                }
                $html .= '</select>';
    $html .= '</div>
  </div>';
  endif;
  if($service_finder_options['search-state']){
  ob_start();
		$country = service_finder_theme_get_data($_REQUEST,'country');
		if($country == '')
		{
			$country = (!empty($service_finder_options['default-country'])) ? $service_finder_options['default-country'] : '';
		}
	    if($restrictuserarea && $identitycheck){
	    $qry = $wpdb->prepare("select DISTINCT state from ".$service_finder_Tables->providers." WHERE admin_moderation = 'approved' AND country = '%s' AND identity = 'approved' AND account_blocked != 'yes' ORDER BY `state`",$country);
	    }else{
	    $qry = $wpdb->prepare("select DISTINCT state from ".$service_finder_Tables->providers." WHERE admin_moderation = 'approved' AND country = '%s' AND account_blocked != 'yes' ORDER BY `state`",$country);
	    }
	   
		$mainstates = $wpdb->get_results($qry);
		
		$qry = $wpdb->prepare("select DISTINCT state from ".$service_finder_Tables->branches." WHERE country = '%s' ORDER BY `state`",$country);
		
		$branchstates = $wpdb->get_results($qry);
		?>
        <div class="<?php echo esc_attr($fieldclass); ?>">
        <div class="select-state">
        <select class="sf-select-box form-control sf-form-control" data-live-search="true" name="state" id="state" title="<?php echo esc_html__('State','service-finder') ?>" data-header="<?php echo esc_html__('Select a State','service-finder') ?>">
        <option value=""><?php echo esc_html__('Select State','service-finder')?></option>
        <?php
        $allstates = array();
        
        if(!empty($mainstates)){
        foreach($mainstates as $state){
            $allstates[] = $state->state;
        }
        }
        
        if(!empty($branchstates)){
        foreach($branchstates as $state){
            $allstates[] = $state->state;
        }
        }
        
        $allstates = array_unique($allstates);
        sort($allstates);
                
        if(!empty($allstates)){
        $getstate = (isset($_REQUEST['state'])) ? $_REQUEST['state'] : '';
        foreach($allstates as $state){
        $statevar = (!empty($state)) ? $state : '';
           if($getstate != ""){
            if($getstate == $statevar){
                $select = 'selected="selected"';
            }else{
                $select = '';
            }
           }
		   if($statevar != '')
		   {
           ?>	
            <option <?php echo esc_attr($select) ?> value="<?php echo esc_attr($statevar) ?>"><?php echo esc_html($statevar) ?></option>
            <?php
			}
        }
        }else{
            ?>
            <option value=""><?php echo esc_html__('No state available','service-finder') ?></option>
            <?php
        }
        ?>
        </select>
        </div>
        </div>
  <?php	        
  $html .= ob_get_clean();
  }
  if($service_finder_options['search-city']):
  $html .= '<div class="'.$fieldclass.'">
    <div class="select-city">';
				$html .= '<select class="sf-select-box form-control sf-form-control" data-live-search="true" name="city" id="city" title="'.esc_html__('City','service-finder').'" data-header="'.esc_html__('Select a City','service-finder').'">';
                $html .= '<option value="">'.esc_html__('Select City','service-finder').'</option>';
				
				$country = (isset($_REQUEST['country'])) ? $_REQUEST['country'] : '';
				$city = (isset($_REQUEST['city'])) ? $_REQUEST['city'] : '';
				$categorysrh = (isset($_REQUEST['categorysrh'])) ? $_REQUEST['categorysrh'] : '';
				$searchAddress = (isset($_REQUEST['searchAddress'])) ? $_REQUEST['searchAddress'] : '';
				$defaultcity = (!empty($service_finder_options['default-city'])) ? $service_finder_options['default-city'] : '';
				$defaultcountry = (!empty($service_finder_options['default-country'])) ? $service_finder_options['default-country'] : '';
				
				if($service_finder_options['search-country']){

				if($country != ""){
					
					if($restrictuserarea && $identitycheck){
			  $maincities = $wpdb->get_results($wpdb->prepare("select DISTINCT city from ".$service_finder_Tables->providers." WHERE country = '%s' AND identity = 'approved' AND admin_moderation = 'approved' AND account_blocked != 'yes' ORDER BY `city`",$country));
			   }else{
			   $maincities = $wpdb->get_results($wpdb->prepare("select DISTINCT city from ".$service_finder_Tables->providers." WHERE country = '%s' AND admin_moderation = 'approved' AND account_blocked != 'yes' ORDER BY `city`",$country));
			   }
			   
			   $branchcities = $wpdb->get_results($wpdb->prepare("select DISTINCT city from ".$service_finder_Tables->branches." WHERE country = '%s' ORDER BY `city`",$country));
				
				$allcities = array();
				
				if(!empty($maincities)){
                foreach($maincities as $city){
					$allcities[] = $city->city;
				}
				}
				
				if(!empty($branchcities)){
                foreach($branchcities as $city){
					$allcities[] = $city->city;
				}
				}
                
				$allcities = array_unique($allcities);
				sort($allcities);	
					
					if(!empty($allcities)){
					$getcity = (isset($_REQUEST['city'])) ? $_REQUEST['city'] : '';
					foreach($allcities as $city){
						$cityname = service_finder_get_cityname_by_slug($city);
						if($getcity == $city){
							$select = 'selected="selected"';
						}else{
							$select = '';
						}
						$html .= '<option '.$select.' value="'.esc_attr($city).'">'.$cityname.'</option>';
						
					}
					}else{
						$html .= '<option value="">'.esc_html__('No city available','service-finder').'</option>';
					}
				}elseif($country == "" && $city == "" && $categorysrh == "" && $searchAddress == "" && $defaultcountry != ""){
				
						if($restrictuserarea && $identitycheck){
						$maincities = $wpdb->get_results($wpdb->prepare("select DISTINCT city from ".$service_finder_Tables->providers." WHERE country = '%s' AND identity = 'approved' AND admin_moderation = 'approved' AND account_blocked != 'yes' ORDER BY `city`",$defaultcountry));
						}else{
						$maincities = $wpdb->get_results($wpdb->prepare("select DISTINCT city from ".$service_finder_Tables->providers." WHERE country = '%s' AND admin_moderation = 'approved' AND account_blocked != 'yes' ORDER BY `city`",$defaultcountry));
						}
						
						$branchcities = $wpdb->get_results($wpdb->prepare("select DISTINCT city from ".$service_finder_Tables->branches." WHERE country = '%s' ORDER BY `city`",$defaultcountry));
				
						$allcities = array();
						
						if(!empty($maincities)){
						foreach($maincities as $city){
							$allcities[] = $city->city;
						}
						}
						
						if(!empty($branchcities)){
						foreach($branchcities as $city){
							$allcities[] = $city->city;
						}
						}
						
						$allcities = array_unique($allcities);
						sort($allcities);	
						
						if(!empty($allcities)){
						foreach($allcities as $city){
							$cityname = service_finder_get_cityname_by_slug($city);
							if($defaultcity == $city){
								$select = 'selected="selected"';
							}else{
								$select = '';
							}
							$html .= '<option '.$select.' value="'.esc_attr($city).'">'.$cityname.'</option>';
							
						}
						}else{
							$html .= '<option value="">'.esc_html__('No city available','service-finder').'</option>';
						}
				}
				}else{
				
					if($restrictuserarea && $identitycheck){
					$maincities = $wpdb->get_results($wpdb->prepare("select DISTINCT city from ".$service_finder_Tables->providers." WHERE country = '%s' AND identity = 'approved' AND admin_moderation = 'approved' AND account_blocked != 'yes' ORDER BY `city`",$defaultcountry));
					}else{
					$maincities = $wpdb->get_results($wpdb->prepare("select DISTINCT city from ".$service_finder_Tables->providers." WHERE country = '%s' AND admin_moderation = 'approved' AND account_blocked != 'yes' ORDER BY `city`",$defaultcountry));
					}
					
					$branchcities = $wpdb->get_results($wpdb->prepare("select DISTINCT city from ".$service_finder_Tables->branches." WHERE country = '%s' ORDER BY `city`",$defaultcountry));
				
					$allcities = array();
					
					if(!empty($maincities)){
					foreach($maincities as $city){
						$allcities[] = $city->city;
					}
					}
					
					if(!empty($branchcities)){
					foreach($branchcities as $city){
						$allcities[] = $city->city;
					}
					}
					
					$allcities = array_unique($allcities);
					sort($allcities);
					
					if(!empty($allcities)){
					$getcity = (isset($_REQUEST['city'])) ? $_REQUEST['city'] : '';
					foreach($allcities as $city){
						$cityname = service_finder_get_cityname_by_slug($city);
						if($getcity == $city){
							$select = 'selected="selected"';
						}else{
							$select = '';
						}
						$html .= '<option '.$select.' value="'.esc_attr($city).'">'.$cityname.'</option>';
						
					}
					}else{
						$html .= '<option value="">'.esc_html__('No city available','service-finder').'</option>';
					}
				
				}
				
                $html .= '</select>';
    $html .= '</div>
  </div>';
  endif;
  if($service_finder_options['search-zipcode']){
  ob_start();
  ?>
  	<div class="<?php echo esc_attr($fieldclass); ?>">
    <div class="type-zipcode">
	<input type="text" value="<?php echo esc_attr($zipcode); ?>" placeholder="<?php echo esc_html__('Zipcode', 'service-finder'); ?>" id="zipcode" name="zipcode" class="form-control sf-form-control">  
    </div>
    </div>
  <?php
  $html .= ob_get_clean();
  }
  $searchprice = (isset($service_finder_options['search-price'])) ? esc_attr($service_finder_options['search-price']) : '';
  $searchradius = (isset($service_finder_options['search-radius'])) ? esc_attr($service_finder_options['search-radius']) : '';
  
  $defaultradius = (isset($service_finder_options['default-radius'])) ? esc_attr($service_finder_options['default-radius']) : 0;
  
  $price = (isset($_REQUEST['price'])) ? esc_html($_REQUEST['price']) : $minpricerange.','.$maxpricerange;
  
  if($searchprice || $searchradius){
  
  $defaultradius = (isset($_REQUEST['distance'])) ? $_REQUEST['distance'] : $defaultradius;
  
  $html .= '<div class="sf-advace-search clearfix clear">';
  
  if($service_finder_options['search-price']){
  $price = (isset($_REQUEST['price'])) ? esc_html($_REQUEST['price']) : $minpricerange.','.$maxpricerange;	
  $html .= '<div class="col-md-6  col-sm-6">
                            <h5 class="sf-tilte">'.esc_html__('Filter by Price Interval','service-finder').':</h5> 
                            <input name="price" type="text" class="sf-price-filter span2" value="" data-slider-min="'.esc_attr($minpricerange).'" data-slider-max="'.esc_attr($maxpricerange).'" data-slider-step="5" data-slider-value="['.$price.']"/>
							<b class="sf-minimum-price">'.service_finder_money_format($minpricerange).'</b>
                            <b class="sf-maximum-price">'.service_finder_money_format($maxpricerange).'</b>
                        </div>';
  }
  if($service_finder_options['search-radius']){
   $html .= '<div class="col-md-6  col-sm-6">
                            <h5 class="sf-tilte">'.esc_html__('Filter by Radius','service-finder').':</h5> 
                            <input class="sf-radius-filter" name="distance" data-slider-id="ex1Slider" type="text" data-slider-min="'.esc_attr($minradiusrange).'" data-slider-max="'.esc_attr($maxradiusrange).'" data-slider-step="1" data-slider-value="'.esc_attr($defaultradius).'"/>
							<b class="sf-minimum-mile">'.esc_html($minradiusrange).$radiusunit. '</b>
                            <b class="sf-maximum-mile">'.esc_html($maxradiusrange).$radiusunit. '</b>
                        </div>';
  }
  
  $html .= '</div>';
  }
  
  $btnclass = (service_finder_themestyle() == 'style-2') ? 'btn-search-result' : '';
  $html .= '<div class="'.$submitclass.'">
    <div class="type-search">
      <input type="submit" value="'.esc_html($searchbtntext).'" class="btn btn-block btn-primary '.sanitize_html_class($btnclass).'">
    </div>
  </div>';
  
  $html .= '</div>';
  
  $html .= '<div class="loading-srh-bar default-hidden"><i class="fa fa-spinner fa-pulse"></i></div>
</form>';
}
