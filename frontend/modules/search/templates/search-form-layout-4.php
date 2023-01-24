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
$searchAddress = service_finder_get_data($_REQUEST,'searchAddress');
$keyword = service_finder_get_data($_REQUEST,'keyword');
$zipcode = service_finder_get_data($_REQUEST,'zipcode');
$price = service_finder_get_data($_REQUEST,'price');
$identitycheck = service_finder_get_data($service_finder_options,'identity-check');
$restrictuserarea = service_finder_get_data($service_finder_options,'restrict-user-area');
$providerreplacestring = service_finder_provider_replace_string();
$searchprice = service_finder_get_data($service_finder_options,'search-price');
$searchradius = service_finder_get_data($service_finder_options,'search-radius');
$defaultradius = service_finder_get_data($service_finder_options,'default-radius',0);
$searchbtntext = service_finder_get_data($service_finder_options,'search-btn-text',esc_html__('Search Now', 'service-finder'));
$minpricerange = 0;
$maxpricerange = service_finder_get_data($service_finder_options,'search-max-price',1000);
$minradiusrange = 0;
$maxradiusrange = service_finder_get_data($service_finder_options,'search-max-radius',1000);
$radiussearchunit = service_finder_get_data($service_finder_options,'radius-search-unit','mi');
$advancesearchview = service_finder_get_data($service_finder_options,'advance-search-view');
if($radiussearchunit == 'km'){
$radiusunit = esc_html__( ' Km', 'service-finder' );
}else{
$radiusunit = esc_html__( ' Mi.', 'service-finder' );
}
if($advancesearchview == 'hide'){
$hiddenclass = 'default-hidden';
$arrowclass = 'fa-chevron-up';
}else{
$hiddenclass = '';
$arrowclass = 'fa-chevron-down';
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
if($service_finder_options['search-state']){
	$searchoptions[] = $service_finder_options['search-state'];
}
if($service_finder_options['search-zipcode']){
	$searchoptions[] = $service_finder_options['search-zipcode'];
}
if($service_finder_options['search-address']){
	$searchoptions[] = $service_finder_options['search-address'];
}
$optionsfields = count($searchoptions) + 1;
if($optionsfields > 1)
{
?>
<form class="clearfix search-providers" method="get" action="<?php echo home_url('/'); ?>">
<input type="hidden" name="s" value="">
<div class="sf-searchbar-table">
    <div class="sf-searchbar-left">
        <ul class="clearfix sf-searchfileds-count-<?php echo esc_attr(count($searchoptions)); ?>">
        <?php if(service_finder_get_data($service_finder_options,'search-keyword')){ ?>
        <li>
        <label><?php echo esc_html__('Keyword', 'service-finder'); ?></label>
        <input type="text" value="<?php echo esc_attr($keyword); ?>" placeholder="<?php echo esc_html__('Keyword', 'service-finder'); ?>" id="keyword" name="keyword" class="form-control sf-form-control">
        <span class="sf-search-icon"><img src="<?php echo SERVICE_FINDER_BOOKING_IMAGE_URL.'/search-bar/keyword.png'; ?>" alt=""/></span>
        </li>
		<?php } ?>
        
        <?php if(service_finder_get_data($service_finder_options,'search-address')){ 
		$autolocation = '';
	  	if(service_finder_get_data($service_finder_options,'auto-location-detect')){
	  		$autolocation = '<a href="javascript:;" class="geolocate find-me"><i class="fa fa-crosshairs geo-locate-me"></i></a>';
	  	}
		?>
        <li>
        <label><?php echo esc_html__('Address', 'service-finder'); ?></label>
        <input type="text" value="<?php echo esc_attr($searchAddress); ?>" placeholder="<?php echo esc_html__('Address', 'service-finder'); ?>" id="searchAddress" name="searchAddress" class="form-control sf-form-control">
        <span class="sf-search-icon"><img src="<?php echo SERVICE_FINDER_BOOKING_IMAGE_URL.'/search-bar/location-pin.png'; ?>" alt=""/></span>
		<?php print($autolocation); ?>
        </li>
		<?php } ?>
        
        <?php if(service_finder_get_data($service_finder_options,'search-category')){ ?>
        <li>
        <label><?php echo esc_html__('Category', 'service-finder'); ?></label>
        <select id="categorysrh" name="catid" class="form-control sf-form-control sf-select-box" title="<?php echo esc_html__('Category', 'service-finder'); ?>" data-live-search="true" data-header="<?php echo esc_html__('Select a Category', 'service-finder'); ?>">
        <option value="">
        <?php echo esc_html__('Select a Category', 'service-finder') ?>
        </option>
        <?php 
		$showemptycategory = service_finder_get_data($service_finder_options,'show-empty-category');
		$limit = 1000;
		$texonomy = 'providers-category';
		$categories = service_finder_getCategoryList($limit);
		$catid = service_finder_get_data($_REQUEST,'catid');
		
		if($showemptycategory == 'yes')
		{
		if(!empty($categories)){
			foreach($categories as $category){
			$term_id = service_finder_get_data($category,'term_id');
			$term_name = service_finder_get_data($category,'name');
			$select = ($catid == $term_id) ? 'selected="selected"' : '';
			
			$catimage =  service_finder_getCategoryImage($term_id,'service_finder-category-small');
			?>	
			<option <?php echo esc_attr($select); ?> value="<?php echo esc_attr($term_id); ?>" data-content="<span><?php echo esc_attr($term_name); ?></span>"><?php echo esc_html($term_name); ?></option>
			<?php	
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
					?>
					<option <?php echo esc_attr($childselect); ?> value="<?php echo esc_attr($term_child_id); ?>" data-content="<?php echo esc_attr($imgtag); ?><span class='childcat'><?php echo esc_attr($term_child_name); ?></span>"><?php echo esc_html($term_child_name); ?></option>
					<?php
					}else{
					?>
					<option <?php echo esc_attr($childselect); ?> value="<?php echo esc_attr($term_child_id); ?>" data-content="<span class='childcat'><?php echo esc_attr($term_child_name) ?></span>"><?php echo esc_html($term_child_name); ?></option>
					<?php
					}
					
				}
				}
			}
			}
		}	
		}else
		{
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
							$term_children = get_term_children($term_id,$texonomy);
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
						?>
						<option <?php echo esc_attr($select) ?> value="<?php echo esc_attr($term_id) ?>" data-content="<span><?php echo esc_attr($term_name)?></span>"><?php echo esc_html($term_name) ?></option>
						<?php
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
								?>
								<option <?php echo esc_attr($childselect) ?> value="<?php echo esc_attr($term_child_id) ?>" data-content="<?php echo esc_attr($imgtag); ?><span class='childcat'><?php echo esc_attr($term_child_name) ?></span>"><?php echo esc_html($term_child_name) ?></option>
								<?php
                                }else{
                                ?>
								<option <?php echo esc_attr($childselect); ?> value="<?php echo esc_attr($term_child_id) ?>" data-content="<span class='childcat'><?php echo esc_attr($term_child_name) ?></span>"><?php echo esc_html($term_child_name); ?></option>
                                <?php
								}
								}
								
							}
							}
						}
                        
                        }
                    }	
		}
		?>
      	</select>
        <span class="sf-search-icon"><img src="<?php echo SERVICE_FINDER_BOOKING_IMAGE_URL.'/search-bar/maintenance.png'; ?>" alt=""/></span>
        </li>
		<?php } ?>
        
        <?php if(service_finder_get_data($service_finder_options,'search-country')){ ?>
        <li>
        <label><?php echo esc_html__('Country', 'service-finder'); ?></label>
        <?php
	    if($restrictuserarea && $identitycheck){
	    $qry = "select DISTINCT country from ".$service_finder_Tables->providers." WHERE admin_moderation = 'approved' AND identity = 'approved' AND account_blocked != 'yes' ORDER BY `country`";
	    }else{
	    $qry = "select DISTINCT country from ".$service_finder_Tables->providers." WHERE admin_moderation = 'approved' AND account_blocked != 'yes' ORDER BY `country`";
	    }
	   
		$maincountries = $wpdb->get_results($qry);
		
		$qry = "select DISTINCT country from ".$service_finder_Tables->branches." ORDER BY `country`";
		
		$branchcountries = $wpdb->get_results($qry);
		?>
        <select class="sf-select-box form-control sf-form-control" data-live-search="true" name="country" id="country" title="<?php echo esc_html__('Country','service-finder') ?>" data-header="<?php echo esc_html__('Select a Country','service-finder') ?>">
        <option value=""><?php echo esc_html__('Select Country','service-finder')?></option>
        <?php
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
        	$select = '';
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
		   if($countryvar != '')
		   {
           ?>	
            <option <?php echo esc_attr($select) ?> value="<?php echo esc_attr($countryvar) ?>"><?php echo esc_html($countryvar) ?></option>
            <?php
			}
        }
        }else{
            ?>
            <option value=""><?php echo esc_html__('No country available','service-finder') ?></option>
            <?php
        }
        ?>
        </select>
        <span class="sf-search-icon"><img src="<?php echo SERVICE_FINDER_BOOKING_IMAGE_URL.'/search-bar/globe.png'; ?>" alt=""/></span>
        </li>
		<?php } ?>
        
		<?php if(service_finder_get_data($service_finder_options,'search-state')){ ?>
        <li>
        <label><?php echo esc_html__('State', 'service-finder'); ?></label>
        <?php
		$country = service_finder_get_data($_REQUEST,'country');
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
           }else{
		   $select = '';
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
        <span class="sf-search-icon"><img src="<?php echo SERVICE_FINDER_BOOKING_IMAGE_URL.'/search-bar/globe.png'; ?>" alt=""/></span>
        </li>
		<?php } ?>
        <?php if(service_finder_get_data($service_finder_options,'search-city')){ ?>
        <li>
        <label><?php echo esc_html__('City', 'service-finder'); ?></label>
        <select class="sf-select-box form-control sf-form-control" data-live-search="true" name="city" id="city" title="<?php echo esc_html__('City','service-finder') ?>" data-header="<?php echo esc_html__('Select a City','service-finder') ?>">
        <option value=""><?php echo esc_html__('Select City','service-finder') ?></option>
		<?php
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
                ?>
                <option <?php echo esc_attr($select); ?> value="<?php echo esc_attr($city) ?>"><?php echo esc_html($cityname) ?></option>
                <?php
            }
            }else{
            ?>
                <option value=""><?php echo esc_html__('No city available','service-finder') ?></option>
                <?php
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
                    ?>
                    <option <?php echo esc_attr($select) ?> value="<?php echo esc_attr($city) ?>"><?php echo esc_html($cityname) ?></option>
                    <?php
                }
                }else{
                ?>
                    <option value=""><?php echo esc_html__('No city available','service-finder') ?></option>
                    <?php
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
                ?>
                <option <?php echo esc_attr($select) ?> value="<?php echo esc_attr($city) ?>"><?php echo esc_html($cityname) ?></option>
                <?php
            }
            }else{
                ?>
                <option value=""><?php echo esc_html__('No city available','service-finder') ?></option>
                <?php
            }
        
        }
        ?>
        </select>
        <span class="sf-search-icon"><img src="<?php echo SERVICE_FINDER_BOOKING_IMAGE_URL.'/search-bar/city.png'; ?>" alt=""/></span>
        </li>
		<?php } ?>
        <?php if(service_finder_get_data($service_finder_options,'search-zipcode')){ ?>
        <li>
        <label><?php echo esc_html__('Zipcode', 'service-finder'); ?></label>
        <input type="text" value="<?php echo esc_attr($zipcode); ?>" placeholder="<?php echo esc_html__('Zipcode', 'service-finder'); ?>" id="zipcode" name="zipcode" class="form-control sf-form-control">
        <span class="sf-search-icon"><img src="<?php echo SERVICE_FINDER_BOOKING_IMAGE_URL.'/search-bar/globe.png'; ?>" alt=""/></span>
        </li>
		<?php } ?>
    </ul>
    </div>
    <div class="sf-searchbar-right">
        <button class="btn btn-primary"><i class="fa fa-search"></i> <?php echo esc_html($searchbtntext) ?></button>
    </div>
    <?php
	if($searchprice || $searchradius){
	?>
	<div class="sf-advace-search-btn openadvsrh2"><?php echo esc_html__('Advance Search', 'service-finder'); ?> <i class="fa <?php echo sanitize_html_class($arrowclass); ?>"></i></div>
	<?php
	}
	?>
</div>
<?php
if($searchprice || $searchradius){
$price = (isset($_REQUEST['price'])) ? esc_html($_REQUEST['price']) : $minpricerange.','.$maxpricerange;
$defaultradius = (!empty($_REQUEST)) ? service_finder_get_data($_REQUEST,'distance',$defaultradius) : $defaultradius;
?>
<div class="sf-advace-search-two <?php echo sanitize_html_class($hiddenclass) ?>">
    <div class="row">
        <?php if(service_finder_get_data($service_finder_options,'search-price')){ ?>
        <div class="col-md-6  col-sm-6">
            <h5 class="sf-tilte"><?php echo esc_html__('Filter by price interval', 'service-finder'); ?>:</h5> 
            <b><?php echo service_finder_money_format($minpricerange)?></b>
            <input name="price" type="text" class="sf-price-filter span2" value="" data-slider-min="<?php echo esc_attr($minpricerange) ?>" data-slider-max="<?php echo esc_attr($maxpricerange) ?>" data-slider-step="5" data-slider-value="[<?php echo esc_attr($price) ?>]"/>
            <b><?php echo service_finder_money_format($maxpricerange)?></b>
        </div>
        <?php } ?>
        <?php if(service_finder_get_data($service_finder_options,'search-radius')){ ?>
        <div class="col-md-6  col-sm-6">
            <h5 class="sf-tilte"><?php echo esc_html__('Filter by Radius', 'service-finder'); ?>:</h5> 
            <b><?php echo esc_html($minradiusrange).$radiusunit; ?></b>
            <input class="sf-radius-filter" name="distance" data-slider-id="ex1Slider" type="text" data-slider-min="<?php echo esc_attr($minradiusrange)?> " data-slider-max="<?php echo esc_attr($maxradiusrange)?>" data-slider-step="1" data-slider-value="<?php echo esc_attr($defaultradius)?>"/>
            <b><?php echo esc_html($maxradiusrange).$radiusunit; ?></b>
        </div>
        <?php } ?>
    </div>.
</div>
<?php } ?>
<div class="loading-srh-bar default-hidden"><i class="fa fa-spinner fa-pulse"></i></div>
</form>
<?php
}
?>