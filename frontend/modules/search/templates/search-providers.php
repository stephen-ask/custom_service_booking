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

global $service_finder_Params, $wpdb;
$wpdb = service_finder_plugin_global_vars('wpdb');
$service_finder_Params = service_finder_plugin_global_vars('service_finder_Params');
$service_finder_Tables = service_finder_plugin_global_vars('service_finder_Tables');
$zoomlevel = 7;

$keyword = (isset($_GET['keyword'])) ? $_GET['keyword'] : '';
$getcity = (isset($_GET['city'])) ? $_GET['city'] : '';
$getstate = (isset($_GET['state'])) ? $_GET['state'] : '';
$getzipcode = (isset($_GET['zipcode'])) ? $_GET['zipcode'] : '';
$getcatid = (isset($_GET['catid'])) ? $_GET['catid'] : '';
$getcountry = (isset($_GET['country'])) ? $_GET['country'] : '';
$price = (isset($_GET['price'])) ? esc_html($_GET['price']) : '';
   
$minprice = '';
$maxprice = '';
if($price != ""){
$price = explode(',',$price);
if(!empty($price)){
$minprice = service_finder_get_data($price,0);
$maxprice = service_finder_get_data($price,1);
}
}
$distance = (isset($_GET['distance'])) ? esc_html($_GET['distance']) : '';

$getsearchAddress = (isset($_GET['searchAddress'])) ? $_GET['searchAddress'] : '';

if($getcity == 'New York'){
$getcity = $getcity.', NY';
}

$defaultview = (!empty($service_finder_options["default-view"])) ? esc_js($service_finder_options["default-view"]) : "grid-4";
$defaultcity = (!empty($service_finder_options['default-city'])) ? $service_finder_options['default-city'] : '';
$defaultcountry = (!empty($service_finder_options['default-country'])) ? $service_finder_options['default-country'] : '';

if($getcity != "" && $getcountry != ""){
	$zoomlevel = (!empty($service_finder_options['zoom-level-city'])) ? $service_finder_options['zoom-level-city'] : 12;
}elseif($getcity != "" && $getcountry == ""){
	$zoomlevel = (!empty($service_finder_options['zoom-level-city'])) ? $service_finder_options['zoom-level-city'] : 12;
}elseif($getcity == "" && $getcountry != ""){
	$zoomlevel = (!empty($service_finder_options['zoom-level-country'])) ? $service_finder_options['zoom-level-country'] : 5;
}elseif($getcatid != "" && $getcity == "" && $getcountry == ""){
	if($defaultcity != ""){
		$zoomlevel = (!empty($service_finder_options['zoom-level-city'])) ? $service_finder_options['zoom-level-city'] : 12;
	}elseif($defaultcountry != ""){
		$zoomlevel = (!empty($service_finder_options['zoom-level-country'])) ? $service_finder_options['zoom-level-country'] : 5;
	}else{
		$zoomlevel = 2;
	}
	
}elseif($getcity == "" && $getcountry == ""){
	if($defaultcity != ""){
		$zoomlevel = (!empty($service_finder_options['zoom-level-city'])) ? $service_finder_options['zoom-level-city'] : 12;
	}elseif($defaultcountry != ""){
		$zoomlevel = (!empty($service_finder_options['zoom-level-country'])) ? $service_finder_options['zoom-level-country'] : 5;
	}else{
		$zoomlevel = 2;
	}
}


if(isset($_GET['searchAddress']) && $getsearchAddress != ""){
$srhaddress = $_GET['searchAddress'];
}else{
$srhaddress = '';
}

$imagepath = SERVICE_FINDER_BOOKING_IMAGE_URL.'/markers';
?>
<?php if($service_finder_options['search-style'] == 'ajax-search'){?>
<!--Ajax Search Style-->

<div class="col-md-12 content">
  <div class = "inner-box content no-right-margin darkviolet">
  <?php
  wp_add_inline_script( 'google-map', 'var googlecode_regular_vars = {"path":"'.esc_js($imagepath).'","markers":"[]","idx_status":"0","page_custom_zoom":"'.esc_js($zoomlevel).'","generated_pins":"0"};', 'after' );
   
  $headerstyle = (!empty($service_finder_options["search-header-style"]) && service_finder_show_map_on_site()) ? esc_js($service_finder_options["search-header-style"]) : "";
  $city = (isset($_GET["city"])) ? esc_js($_GET["city"]) : "";
  $catid = (isset($_GET["catid"])) ? esc_js($_GET["catid"]) : "";
  $country = (isset($_GET["country"])) ? esc_js($_GET["country"]) : "";
  $state = (isset($_GET["state"])) ? esc_js($_GET["state"]) : "";
  $zipcode = (isset($_GET["zipcode"])) ? esc_js($_GET["zipcode"]) : "";
   			
   wp_add_inline_script( 'bootstrap', 'jQuery(document).ready(function($) {
				
				//Load Search Result Function
                function service_finder_loadSearchResult(page,viewtype,numberofpages,setorderby,setorder,srhbybooking,srhdate,srhperiod,srhtime,srhgender){
                    // Start the transition
					var headerstyle = "'.$headerstyle.'";

					var data = {
						page: page,
                        action: "load-search-result",
						keyword: "'.$keyword.'",
						minprice: "'.$minprice.'",
						maxprice: "'.$maxprice.'",
						distance: "'.$distance.'",
						address: "'.esc_js($srhaddress).'",
						city: "'.$city.'",
						catid: "'.$catid.'",
						country: "'.$country.'",
						state: "'.$state.'",
						zipcode: "'.$zipcode.'",
						viewtype: viewtype,
						numberofpages: numberofpages,
						setorderby: setorderby,
						setorder: setorder,
						srhdate: srhdate,
						srhperiod: srhperiod,
						srhtime: srhtime,
						srhbybooking: srhbybooking,
						srhgender: srhgender,
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
									jQuery(".result-title strong").html(data["count"]);
									jQuery("#srhtotalresults").html(data["count"]);
									jQuery("#srhstartpagenum").html(data["startpagenum"]);
									jQuery("#srhendpagenum").html(data["endresultnum"]);
									if(headerstyle == "map"){	
										var  new_markers = jQuery.parseJSON(data["markers"]);
										if(data["center_latitude"] != "" && data["center_longitude"] != ""){
										googlecode_regular_vars.general_latitude = data["center_latitude"];
										googlecode_regular_vars.general_longitude = data["center_longitude"];
										}
										if (new_markers.length > 0) {
											refresh_marker(map, new_markers);
											initializeSearchMap();
											jQuery("#no-result").hide();
										}else{
											googlecode_regular_vars.page_custom_zoom = 2;
											initializeSearchMap();
											jQuery("#no-result").show();
										}
									}	
										jQuery(".cvf_universal_container").html(data["result"]);
										jQuery(".display-ratings").rating();
										jQuery(".sf-show-rating").show();
										jQuery("[data-tool=\'tooltip\']").tooltip();
										equalheight(".equal-col-outer .equal-col");
									
									}
			
								});
                   
                }
               
                // Load page 1 as the default
				var viewtype = jQuery("#viewTypes li.active").attr("data-view");
				
				var numberofpages = jQuery("#numberofpages option:selected").val();
				var setorderby = jQuery("#setorderby option:selected").val();
				var setorder = jQuery("#setorder option:selected").val();
				
                service_finder_loadSearchResult(1,viewtype,numberofpages,setorderby,setorder);
               
                // Handle the clicks
				jQuery("body").on("click", ".cvf_universal_container .cvf-universal-pagination li.activelink a", function(){
                    var page = jQuery(this).parents("li").attr("data-pnum");
					viewtype = jQuery("#viewTypes li.active").attr("data-view");
					
					var srhbybooking = jQuery("#srhbybooking option:selected").val();
					var setorderby = jQuery("#setorderby option:selected").val();
					var setorder = jQuery("#setorder option:selected").val();
					var numberofpages = jQuery("#numberofpages option:selected").val();
					
					var srhdate = jQuery("#srhdate").val();
					var srhperiod = jQuery("#srhperiod option:selected").val();
					var srhtime = jQuery("#srhtime option:selected").val();
					
					var srhgender = jQuery("#srhgender option:selected").val();
					
                    service_finder_loadSearchResult(page,viewtype,numberofpages,setorderby,setorder,srhbybooking,srhdate,srhperiod,srhtime,srhgender);
                   
                });
				
				// Handle the view types
				jQuery("body").on("click", "#viewTypes li", function(){
					jQuery("#viewTypes li").removeClass("active");
                    jQuery(this).addClass("active");
					viewtype = jQuery(this).attr("data-view");
					var page = jQuery(".cvf_universal_container .cvf-universal-pagination li.selected").attr("data-pnum");
					
					var srhbybooking = jQuery("#srhbybooking option:selected").val();
					var setorderby = jQuery("#setorderby option:selected").val();
					var setorder = jQuery("#setorder option:selected").val();
					var numberofpages = jQuery("#numberofpages option:selected").val();
						
                    service_finder_loadSearchResult(page,viewtype,numberofpages,setorderby,setorder,srhbybooking);
                   
                });
				//Set number of pages
				jQuery("body").on("change", "#numberofpages", function(){
						var page = jQuery(".cvf_universal_container .cvf-universal-pagination li.selected").attr("data-pnum");
						var viewtype = jQuery("#viewTypes li.active").attr("data-view");
						
						var numberofpages = jQuery(this).val();
						var srhbybooking = jQuery("#srhbybooking option:selected").val();
						var setorderby = jQuery("#setorderby option:selected").val();
						var setorder = jQuery("#setorder option:selected").val();
						service_finder_loadSearchResult(1,viewtype,numberofpages,setorderby,setorder,srhbybooking);
				});
				//Set order by
				jQuery("body").on("change", "#setorderby", function(){
						var page = jQuery(".cvf_universal_container .cvf-universal-pagination li.selected").attr("data-pnum");
						var viewtype = jQuery("#viewTypes li.active").attr("data-view");
						
						var setorderby = jQuery(this).val();
						var srhbybooking = jQuery("#srhbybooking option:selected").val();
						var numberofpages = jQuery("#numberofpages option:selected").val();
						var setorder = jQuery("#setorder option:selected").val();
						service_finder_loadSearchResult(page,viewtype,numberofpages,setorderby,setorder,srhbybooking);
				});
				//Set order
				jQuery("body").on("change", "#setorder", function(){
						var page = jQuery(".cvf_universal_container .cvf-universal-pagination li.selected").attr("data-pnum");
						var viewtype = jQuery("#viewTypes li.active").attr("data-view");
						
						var setorder = jQuery(this).val();
						var srhbybooking = jQuery("#srhbybooking option:selected").val();
						var numberofpages = jQuery("#numberofpages option:selected").val();
						var setorderby = jQuery("#setorderby option:selected").val();
						service_finder_loadSearchResult(page,viewtype,numberofpages,setorderby,setorder,srhbybooking);
				});
				
				//Set order
				jQuery("body").on("change", "#srhbybooking", function(){
						var page = jQuery(".cvf_universal_container .cvf-universal-pagination li.selected").attr("data-pnum");
						var viewtype = jQuery("#viewTypes li.active").attr("data-view");
						
						var setorder = jQuery("#setorder option:selected").val();
						var srhbybooking = jQuery(this).val();
						var numberofpages = jQuery("#numberofpages option:selected").val();
						var setorderby = jQuery("#setorderby option:selected").val();
						service_finder_loadSearchResult(page,viewtype,numberofpages,setorderby,setorder,srhbybooking);
				});
				
				// Aavailability filter
				jQuery("body").on("click", "#avialabilityfilter", function(){
					var viewtype = jQuery("#viewTypes li.active").attr("data-view");
					var page = jQuery(".cvf_universal_container .cvf-universal-pagination li.selected").attr("data-pnum");
					
					var numberofpages = jQuery("#numberofpages option:selected").val();
					var setorderby = jQuery("#setorderby option:selected").val();
					var setorder = jQuery("#setorder option:selected").val();
					
					var srhbybooking = jQuery("#srhbybooking option:selected").val();
					var srhdate = jQuery("#srhdate").val();
					var srhperiod = jQuery("#srhperiod option:selected").val();
					var srhtime = jQuery("#srhtime option:selected").val();
					
					var srhgender = jQuery("#srhgender option:selected").val();
						
                    service_finder_loadSearchResult(page,viewtype,numberofpages,setorderby,setorder,srhbybooking,srhdate,srhperiod,srhtime,srhgender);
                   
                });
				
				
				
                           
            });', 'after' );			
  ?>

  </div>
</div>

<?php if(service_finder_themestyle() != 'style-4'){ ?>
<div class="title-section">
  <?php echo do_action('service_finder_availability_search_filter'); ?>	
</div>
<?php } ?>
<!-- Display Search Result -->
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
  $headerstyle = (!empty($service_finder_options["search-header-style"]) && service_finder_show_map_on_site()) ? esc_js($service_finder_options["search-header-style"]) : "";
  
  $getcity = (isset($_GET['city'])) ? $_GET['city'] : '';
  $getcatid = (isset($_GET['catid'])) ? $_GET['catid'] : '';
  $getcountry = (isset($_GET['country'])) ? $_GET['country'] : '';
  $getstate = (isset($_GET['state'])) ? $_GET['state'] : '';
  $getzipcode = (isset($_GET['zipcode'])) ? $_GET['zipcode'] : '';
  
  wp_add_inline_script( 'google-map', 'var googlecode_regular_vars = {"path":"'.esc_js($imagepath).'","markers":"[]","idx_status":"0","page_custom_zoom":"'.esc_js($zoomlevel).'","generated_pins":"0"};', 'after' );
  
  wp_add_inline_script( 'bootstrap', 'jQuery(document).ready(function($) {
				//Load Search result fucntion
                function service_finder_loadSearchResult(page,viewtype,numberofpages,setorderby,setorder,srhbybooking,srhdate,srhperiod,srhtime,srhgender){
                    
					var headerstyle = "'.$headerstyle.'";
					
					var data = {
						page: page,
                        action: "load-search-result",
						keyword: "'.$keyword.'",
						minprice: "'.$minprice.'",
						maxprice: "'.$maxprice.'",
						distance: "'.$distance.'",
						address: "'.esc_js($srhaddress).'",
						city: "'.esc_js($getcity).'",
						catid: "'.esc_js($getcatid).'",
						country: "'.esc_js($getcountry).'",
						state: "'.$getstate.'",
						zipcode: "'.$getzipcode.'",
						viewtype: viewtype,
						numberofpages: numberofpages,
						setorderby: setorderby,
						setorder: setorder,
						srhdate: srhdate,
						srhperiod: srhperiod,
						srhtime: srhtime,
						srhbybooking: srhbybooking,
						srhgender: srhgender,
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
										
										jQuery(".result-title strong").html(data["count"]);
										jQuery("#srhtotalresults").html(data["count"]);
										jQuery("#srhstartpagenum").html(data["startpagenum"]);
										jQuery("#srhendpagenum").html(data["endresultnum"]);
										
										if(headerstyle == "map"){
										var  new_markers = jQuery.parseJSON(data["markers"]);
										
										if(data["center_latitude"] != "" && data["center_longitude"] != ""){
										googlecode_regular_vars.general_latitude = data["center_latitude"];
										googlecode_regular_vars.general_longitude = data["center_longitude"];
										}	
										 if (new_markers.length > 0) {
											refresh_marker(map, new_markers);
											initializeSearchMap();
											jQuery("#no-result").hide();
										}else{
											googlecode_regular_vars.page_custom_zoom = 2;
											initializeSearchMap();
											jQuery("#no-result").show();
										}
										}
										jQuery(".cvf_universal_container").html(data["result"]);
										jQuery(".display-ratings").rating();
										jQuery(".sf-show-rating").show();
										jQuery("[data-tool=\'tooltip\']").tooltip();
										equalheight(".equal-col-outer .equal-col");
									
									}
			
								});
                   
                }
               
                // Load page 1 as the default
				var viewtype = jQuery("#viewTypes li.active").attr("data-view");
				var page = jQuery(".pagination span.current").html();
				
				var numberofpages = jQuery("#numberofpages option:selected").val();
				var setorderby = jQuery("#setorderby option:selected").val();
				var setorder = jQuery("#setorder option:selected").val();
				
                if(page > 1){
				service_finder_loadSearchResult(page,viewtype,numberofpages,setorderby,setorder);
				}else{
				service_finder_loadSearchResult(1,viewtype,numberofpages,setorderby,setorder);
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
					
					var srhbybooking = jQuery("#srhbybooking option:selected").val();
					var setorderby = jQuery("#setorderby option:selected").val();
					var setorder = jQuery("#setorder option:selected").val();
					var numberofpages = jQuery("#numberofpages option:selected").val();
						
                   // service_finder_loadSearchResult(page,viewtype);
					var homeurl = "'.esc_js(home_url("/")).'";
					window.location = homeurl+"?s=&searchAddress='.esc_js($srhaddress).'&catid='.esc_js($getcatid).'&country='.esc_js($getcountry).'&city='.esc_js($getcity).'&state='.esc_js($getstate).'&zipcode='.esc_js($getzipcode).'&pagenum="+page+"&viewtype="+viewtype+"&numberofpages="+numberofpages+"&setorderby="+setorderby+"&setorder="+setorder+"&srhbybooking="+srhbybooking;
                   
                });
				//Set number of pages for pagination
				jQuery("body").on("change", "#numberofpages", function(){
						var page = jQuery(".pagination span.current").html();
						if(!page > 0){
						page = 1;
						}
						var viewtype = jQuery("#viewTypes li.active").attr("data-view");
						
						var numberofpages = jQuery(this).val();
						var srhbybooking = jQuery("#srhbybooking option:selected").val();
						var setorderby = jQuery("#setorderby option:selected").val();
						var setorder = jQuery("#setorder option:selected").val();

						var homeurl = "'.esc_js(home_url("/")).'";
						window.location = homeurl+"?s=&searchAddress='.esc_js($srhaddress).'&catid='.esc_js($getcatid).'&country='.esc_js($getcountry).'&city='.esc_js($getcity).'&state='.esc_js($getstate).'&zipcode='.esc_js($getzipcode).'&pagenum=1&viewtype="+viewtype+"&numberofpages="+numberofpages+"&setorderby="+setorderby+"&setorder="+setorder+"&srhbybooking="+srhbybooking;
				});
				//Set Order by
				jQuery("body").on("change", "#setorderby", function(){
						var page = jQuery(".pagination span.current").html();
						if(!page > 0){
						page = 1;
						}
						var viewtype = jQuery("#viewTypes li.active").attr("data-view");
						
						var setorderby = jQuery(this).val();
						var srhbybooking = jQuery("#srhbybooking option:selected").val();
						var numberofpages = jQuery("#numberofpages option:selected").val();
						var setorder = jQuery("#setorder option:selected").val();

						var homeurl = "'.esc_js(home_url("/")).'";
						window.location = homeurl+"?s=&searchAddress='.esc_js($srhaddress).'&catid='.esc_js($getcatid).'&country='.esc_js($getcountry).'&city='.esc_js($getcity).'&state='.esc_js($getstate).'&zipcode='.esc_js($getzipcode).'&pagenum="+page+"&viewtype="+viewtype+"&numberofpages="+numberofpages+"&setorderby="+setorderby+"&setorder="+setorder+"&srhbybooking="+srhbybooking;
				});
				//Set Order
				jQuery("body").on("change", "#setorder", function(){
						var page = jQuery(".pagination span.current").html();
						if(!page > 0){
						page = 1;
						}
						var viewtype = jQuery("#viewTypes li.active").attr("data-view");
						
						var setorder = jQuery(this).val();
						var srhbybooking = jQuery("#srhbybooking option:selected").val();
						var numberofpages = jQuery("#numberofpages option:selected").val();
						var setorderby = jQuery("#setorderby option:selected").val();
						
						var homeurl = "'.esc_js(home_url("/")).'";
						window.location = homeurl+"?s=&searchAddress='.esc_js($srhaddress).'&catid='.esc_js($getcatid).'&country='.esc_js($getcountry).'&city='.esc_js($getcity).'&state='.esc_js($getstate).'&zipcode='.esc_js($getzipcode).'&pagenum="+page+"&viewtype="+viewtype+"&numberofpages="+numberofpages+"&setorderby="+setorderby+"&setorder="+setorder+"&srhbybooking="+srhbybooking;
				});
				
				jQuery("body").on("change", "#srhbybooking", function(){
						var page = jQuery(".pagination span.current").html();
						if(!page > 0){
						page = 1;
						}
						var viewtype = jQuery("#viewTypes li.active").attr("data-view");
						
						var srhbybooking = jQuery(this).val();
						var setorder = jQuery("#setorder option:selected").val();
						var numberofpages = jQuery("#numberofpages option:selected").val();
						var setorderby = jQuery("#setorderby option:selected").val();
						
						var homeurl = "'.esc_js(home_url("/")).'";
						window.location = homeurl+"?s=&searchAddress='.esc_js($srhaddress).'&catid='.esc_js($getcatid).'&country='.esc_js($getcountry).'&city='.esc_js($getcity).'&state='.esc_js($getstate).'&zipcode='.esc_js($getzipcode).'&pagenum="+page+"&viewtype="+viewtype+"&numberofpages="+numberofpages+"&setorderby="+setorderby+"&setorder="+setorder+"&srhbybooking="+srhbybooking;
				});
				
				// Aavailability filter
				jQuery("body").on("click", "#avialabilityfilter", function(){
					var page = jQuery(".pagination span.current").html();
					if(!page > 0){
					page = 1;
					}
					
					var viewtype = jQuery("#viewTypes li.active").attr("data-view");
					
					var numberofpages = jQuery("#numberofpages option:selected").val();
					var setorderby = jQuery("#setorderby option:selected").val();
					var setorder = jQuery("#setorder option:selected").val();
					
					var srhbybooking = jQuery("#srhbybooking option:selected").val();
					var srhdate = jQuery("#srhdate").val();
					var srhperiod = jQuery("#srhperiod option:selected").val();
					var srhtime = jQuery("#srhtime option:selected").val();
					var srhgender = jQuery("#srhgender option:selected").val();
						
					var homeurl = "'.esc_js(home_url("/")).'";
					window.location = homeurl+"?s=&searchAddress='.esc_js($srhaddress).'&catid='.esc_js($getcatid).'&country='.esc_js($getcountry).'&city='.esc_js($getcity).'&state='.esc_js($getstate).'&zipcode='.esc_js($getzipcode).'&pagenum="+page+"&viewtype="+viewtype+"&numberofpages="+numberofpages+"&setorderby="+setorderby+"&setorder="+setorder+"&srhbybooking="+srhbybooking+"&srhdate="+srhdate+"&srhperiod="+srhperiod+"&srhtime="+srhtime+"&srhgender="+srhgender;
                   
                });
				
			
                           
            });', 'after' );
  ?>
  </div>
</div>
<?php
$numberofpages = (isset($_GET['numberofpages'])) ? $_GET['numberofpages'] : '';
$setorderby = (isset($_GET['setorderby'])) ? $_GET['setorderby'] : '';
$setorder = (isset($_GET['setorder'])) ? $_GET['setorder'] : '';

$srhbybooking = (isset($_GET['srhbybooking'])) ? esc_html($_GET['srhbybooking']) : '';
$srhdate = (isset($_GET['srhdate'])) ? esc_html($_GET['srhdate']) : '';
$srhperiod = (isset($_GET['srhperiod'])) ? esc_html($_GET['srhperiod']) : '';
$srhtime = (isset($_GET['srhtime'])) ? esc_html($_GET['srhtime']) : '';
$state = (isset($_GET['state'])) ? $_GET['state'] : '';
$zipcode = (isset($_GET['zipcode'])) ? $_GET['zipcode'] : '';
$srhgender = service_finder_get_data($_GET,'srhgender');

$searchdata = array(
	'srhbybooking' => $srhbybooking,
	'srhdate' => $srhdate,
	'srhperiod' => $srhperiod,
	'srhtime' => $srhtime,
	'state' => $state,
	'zipcode' => $zipcode,
	'srhgender' => $srhgender
);
?>
<?php
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
	$orderby = service_finder_get_data($service_finder_options,'search-default-orderby');
	}
	
	if($setorder != ""){
	$order = $setorder;
	}else{
	$order = service_finder_get_data($service_finder_options,'search-default-order');
	}
   
   $getProviders = new SERVICE_FINDER_searchProviders();
   
   $keyword = (isset($_GET['keyword'])) ? esc_html($_GET['keyword']) : '';
   $address = (isset($_GET['searchAddress'])) ? esc_html($_GET['searchAddress']) : '';
   $city = (isset($_GET['city'])) ? esc_html($_GET['city']) : '';
   $catid = (isset($_GET['catid'])) ? esc_html($_GET['catid']) : '';
   $country = (isset($_GET['country'])) ? esc_html($_GET['country']) : '';
   $price = (isset($_GET['price'])) ? esc_html($_GET['price']) : '';
   $state = (isset($_GET['state'])) ? $_GET['state'] : '';
   $zipcode = (isset($_GET['zipcode'])) ? $_GET['zipcode'] : '';
   
   $minprice = '';
$maxprice = '';
if($price != ""){
$price = explode(',',$price);
if(!empty($price)){
$minprice = service_finder_get_data($price,0);
$maxprice = service_finder_get_data($price,1);
}
}
   
   
   $distance = (isset($_GET['distance'])) ? esc_html($_GET['distance']) : '';
   
   
   $providersInfoArr = $getProviders->service_finder_getSearchedProviders($searchdata,esc_attr($distance),esc_attr($minprice),esc_attr($maxprice),esc_attr($keyword),esc_attr($address),esc_attr($city),esc_attr($catid),esc_attr($country),$start,$per_page,$orderby,$order);
	
   $providersavailability = array();
   	   
   $providersInfo = $providersInfoArr['srhResult'];
   $count = $providersInfoArr['count'];
   if(!empty($providersInfoArr['sortresult'])){
   $providersavailability = $providersInfoArr['sortresult'];
   }
	?>
<?php if(service_finder_themestyle() != 'style-4'){ ?>    
<?php if(!empty($providersInfo)){ ?>
<div class="title-section">
  <?php echo do_action('service_finder_availability_search_filter'); ?>
</div>
<?php } ?>
<?php } ?>
<!-- result start -->
<div class="section-content">
<?php
$srhcontent = '';
$viewtype = (isset($_GET['viewtype'])) ? $_GET['viewtype'] : $defaultview;
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

	$markers = '';
	if(!empty($providersInfo)){ 
	
	foreach($providersInfo as $provider){

	$userLink = service_finder_get_author_url($provider->wp_user_id);
	
	$services = '';
	if($keyword != "" || ($minprice != "" && $maxprice != "" && $maxprice > 0)){
	$services = service_finder_get_searched_services($provider->wp_user_id,$keyword,$minprice,$maxprice);

    $searchedservices = '';
	if(!empty($services)){
		$searchedservices .= '<ul class="sf-service-price-list">';
		foreach($services as $service){
			$searchedservices .= '<li><span>'.service_finder_money_format(esc_html($service->cost)).'</span> '.esc_html($service->service_name).'</li>';
		}
		$searchedservices .= '</ul>';
	}
	
	}

	if(!empty($provider->avatar_id) && $provider->avatar_id > 0){
		$src  = wp_get_attachment_image_src( $provider->avatar_id, 'service_finder-provider-thumb' );
		$src  = $src[0];
	}else{
		$src  = service_finder_get_default_avatar();
	}
	$icon = service_finder_getCategoryIcon($catid);
	if($icon == ""){
	$icon = (!empty($service_finder_options['default-map-marker-icon']['url'])) ? $service_finder_options['default-map-marker-icon']['url'] : '';
	}

	$markeraddress = service_finder_getAddress($provider->wp_user_id);
	
	$companyname = service_finder_getCompanyName($provider->wp_user_id);
	$companyname = str_replace(array("\n", "\r"), ' ', $companyname);
	$companyname = preg_replace('/\t+/', '', $companyname);
	
	$full_name = str_replace(array("\n", "\r"), ' ', $provider->full_name);
	$full_name = preg_replace('/\t+/', '', $full_name);
	
	$markeraddress = str_replace(array("\n", "\r"), ' ', $markeraddress);
	$markeraddress = str_replace('\t', '', $markeraddress);
	/*Load markers*/
	$markers .= '["'.stripcslashes($full_name).'","'.$provider->lat.'","'.$provider->long.'","'.service_finder_encodeURIComponent($src).'","'.$icon.'","'.$userLink.'","'.$provider->wp_user_id.'","'.service_finder_getCategoryName(get_user_meta($provider->wp_user_id,'primary_category',true)).'","'.stripcslashes($markeraddress).'","'.stripcslashes($companyname).'"],';
	
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
			$addressbox = '';
			$showaddressinfo = (isset($service_finder_options['show-address-info'])) ? esc_attr($service_finder_options['show-address-info']) : '';
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
			
			$viewtype = (isset($_GET['viewtype'])) ? $_GET['viewtype'] : $defaultview;
			
			if(service_finder_themestyle() == 'style-3')
			{
				$srhcontent .= service_finder_display_provider_boxes($provider->wp_user_id,$viewtype,true);
			}elseif(service_finder_themestyle() == 'style-4'){
				$srhcontent .= service_finder_provider_box_fourth($provider->wp_user_id,$viewtype,true);
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
									'.service_finder_availability_label($provider->wp_user_id,$providersavailability).'
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
							'.service_finder_availability_label($provider->wp_user_id,$providersavailability).'
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
			/*List view layout*/
			$totaldistance = (!empty($provider->distance)) ? service_finder_getDistance($provider->distance) : '';
			if(service_finder_themestyle() == 'style-2'){
			$srhcontent .= '<div class="sf-featured-listing clearfix">
                            
                            <div class="sf-featured-left" id="proid-'.$provider->wp_user_id.'">
                                <div class="sf-featured-media" style="background-image:url('.esc_url($src).')"></div>
								<a href="'.esc_url($link).'" class="sf-listing-link"></a>
                                <div class="sf-overlay-box"></div>
                                '.service_finder_display_category_label($provider->wp_user_id).'
								'.service_finder_availability_label($provider->wp_user_id,$providersavailability).'
                                '.service_finder_check_varified_icon($provider->wp_user_id).'
                                '.$addtofavorite.'
                                
                                <div class="sf-featured-info">
                                    '.$featured.'
                                </div>
                            </div>
                            
                            <div class="sf-featured-right">
                                <div  class="sf-featured-provider"><a href="'.esc_url($link).'">'.service_finder_getExcerpts($provider->full_name,0,35).'</a></div>
                                <div  class="sf-featured-address"><i class="fa fa-map-marker"></i> '.$addressbox.' </div>
                                '.$totaldistance.'
								'.service_finder_displayRating(service_finder_getAverageRating($provider->wp_user_id)).'
                                <div class="sf-featured-comapny">'.service_finder_getExcerpts(service_finder_getCompanyName($provider->wp_user_id),0,30).'</div>
                                <div class="sf-featured-text">'.service_finder_getExcerpts($provider->bio,0,300).'</div>
                                '.$searchedservices.'
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
										'.service_finder_availability_label($provider->wp_user_id,$providersavailability).'
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
										'.$totaldistance.'
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
									'.service_finder_availability_label($provider->wp_user_id,$providersavailability).'
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
							'.service_finder_availability_label($provider->wp_user_id,$providersavailability).'
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
									'.service_finder_availability_label($provider->wp_user_id,$providersavailability).'
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
							'.service_finder_availability_label($provider->wp_user_id,$providersavailability).'
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
	/*No result found*/
		$srhcontent .= '<div class="sf-nothing-found">
				<strong class="sf-tilte">'.esc_html__('Nothing Found', 'service-finder').'</strong>
					  <p>'.esc_html__('Apologies, but no results were found for the request.', 'service-finder').'</p>
				</div>';
	
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
	if(!empty($providersInfo)){
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
		$lastpageurl = 'pagenum='.$no_of_paginations.'&'.$qryString;
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
<!-- Ajax with URL change Style -->
<div class="col-md-12 content">
  <div class = "inner-box content no-right-margin darkviolet">
  <?php
  wp_add_inline_script( 'google-map', 'var googlecode_regular_vars = {"path":"'.esc_js($imagepath).'","markers":"[]","idx_status":"0","page_custom_zoom":"'.esc_js($zoomlevel).'","generated_pins":"0"};', 'after' );
  
  $viewtype = (!empty($_GET["viewtype"])) ? esc_js($_GET["viewtype"]) : $defaultview;
  $numberofpages = (!empty($_GET["numberofpages"])) ? esc_js($_GET["numberofpages"]) : '';
  $setorderby = (!empty($_GET["setorderby"])) ? esc_js($_GET["setorderby"]) : '';
  $setorder = (!empty($_GET["setorder"])) ? esc_js($_GET["setorder"]) : '';
  
  $headerstyle = (!empty($service_finder_options["search-header-style"]) && service_finder_show_map_on_site()) ? esc_js($service_finder_options["search-header-style"]) : "";
  
  $getcity = (isset($_GET['city'])) ? $_GET['city'] : '';
  $getcatid = (isset($_GET['catid'])) ? $_GET['catid'] : '';
  $getcountry = (isset($_GET['country'])) ? $_GET['country'] : '';
  $getstate = (isset($_GET['state'])) ? $_GET['state'] : '';
  $getzipcode = (isset($_GET['zipcode'])) ? $_GET['zipcode'] : '';
  
  $srhdate = (!empty($_GET["srhdate"])) ? esc_js($_GET["srhdate"]) : '';
  $srhperiod = (!empty($_GET["srhperiod"])) ? esc_js($_GET["srhperiod"]) : '';
  $srhtime = (!empty($_GET["srhtime"])) ? esc_js($_GET["srhtime"]) : '';
  $state = (!empty($_GET["state"])) ? esc_js($_GET["state"]) : '';
  $zipcode = (!empty($_GET["zipcode"])) ? esc_js($_GET["zipcode"]) : '';
  
  $srhbybooking = (!empty($_GET["srhbybooking"])) ? esc_js($_GET["srhbybooking"]) : '';
  
  $srhgender = (!empty($_GET["srhgender"])) ? esc_js($_GET["srhgender"]) : '';
  
  wp_add_inline_script( 'bootstrap', 'jQuery(document).ready(function($) {
				var headerstyle = "";
				
				//Load Search Result function
                function service_finder_loadSearchResult(page,viewtype,numberofpages,setorderby,setorder,srhbybooking,srhdate,srhperiod,srhtime,srhgender){
                    // Start the transition
                    
					var headerstyle = "'.$headerstyle.'";
					var data = {
						page: page,
                        action: "load-search-result",
						keyword: "'.$keyword.'",
						minprice: "'.$minprice.'",
						distance: "'.$distance.'",
						maxprice: "'.$maxprice.'",
						address: "'.esc_js($srhaddress).'",
						city: "'.esc_js($getcity).'",
						catid: "'.esc_js($getcatid).'",
						country: "'.esc_js($getcountry).'",
						state: "'.esc_js($getstate).'",
						zipcode: "'.esc_js($getzipcode).'",
						viewtype: viewtype,
						numberofpages: numberofpages,
						setorderby: setorderby,
						setorder: setorder,
						srhdate: srhdate,
						srhperiod: srhperiod,
						srhtime: srhtime,
						srhbybooking: srhbybooking,
						srhgender: srhgender,
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
										
										jQuery(".result-title strong").html(data["count"]);
										jQuery("#srhtotalresults").html(data["count"]);
										jQuery("#srhstartpagenum").html(data["startpagenum"]);
										jQuery("#srhendpagenum").html(data["endresultnum"]);
										
										if(headerstyle == "map"){
										var  new_markers = jQuery.parseJSON(data["markers"]);
										
										if(data["center_latitude"] != "" && data["center_longitude"] != ""){
										googlecode_regular_vars.general_latitude = data["center_latitude"];
										googlecode_regular_vars.general_longitude = data["center_longitude"];
										}
										
										 if (new_markers.length > 0) {
											refresh_marker(map, new_markers);
											initializeSearchMap();
											jQuery("#no-result").hide();
										}else{
											googlecode_regular_vars.page_custom_zoom = 2;
											initializeSearchMap();
											jQuery("#no-result").show();
										}
										}
										jQuery(".cvf_universal_container").html(data["result"]);
										jQuery(".display-ratings").rating();
										jQuery(".sf-show-rating").show();
										jQuery("[data-tool=\'tooltip\']").tooltip();
										equalheight(".equal-col-outer .equal-col");
									
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

						
						var data = {
						page: urlParams["pagenum"],
                        action: "load-search-result",
						keyword: urlParams["keyword"],
						minprice: urlParams["minprice"],
						maxprice: urlParams["maxprice"],
						distance: urlParams["distance"],
						city: urlParams["city"],
						catid: urlParams["catid"],
						country: urlParams["country"],
						state: urlParams["state"],
						zipcode: urlParams["zipcode"],
						viewtype: urlParams["viewtype"],
						numberofpages: urlParams["numberofpages"],
						setorderby: urlParams["setorderby"],
						setorder: urlParams["setorder"],
						srhdate: urlParams["srhdate"],
						srhperiod: urlParams["srhperiod"],
						srhtime: urlParams["srhtime"],
						srhbybooking: urlParams["srhbybooking"],
						srhgender: urlParams["srhgender"],
                    };
						
				  var formdata = jQuery.param(data);
				  jQuery.ajax({
			
									type: "POST",
			
									url: ajaxurl,
			
									data: formdata,
									
									dataType: "json",
			
									success:function (data, textStatus) {
										
										if(headerstyle == "map"){	
										var  new_markers = jQuery.parseJSON(data["markers"]);
										
										if(data["center_latitude"] != "" && data["center_longitude"] != ""){
										googlecode_regular_vars.general_latitude = data["center_latitude"];
										googlecode_regular_vars.general_longitude = data["center_longitude"];
										}
									
										 if (new_markers.length > 0) {
											refresh_marker(map, new_markers);
											initializeSearchMap();
											jQuery("#no-result").hide();
										}else{
											googlecode_regular_vars.page_custom_zoom = 2;
											initializeSearchMap();
											jQuery("#no-result").show();
										}
									}
										jQuery(".cvf_universal_container").html(data["result"]);
										jQuery(".display-ratings").rating();
										jQuery("[data-tool=\'tooltip\']").tooltip();
										jQuery(".sf-show-rating").show();
									
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
				if(setorderby == ""){
				var setorderby = jQuery("#setorderby option:selected").val();
				}
				if(setorder == ""){
				var setorder = jQuery("#setorder option:selected").val();
				}
				
				var srhbybooking = "'.$srhbybooking.'";
				var srhdate = "'.$srhdate.'";
				var srhperiod = "'.$srhperiod.'";
				var srhgender = "'.$srhgender.'";
				var srhtime = "'.$srhtime.'";
				var state = "'.$state.'";
				var zipcode = "'.$zipcode.'";
				
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
				
				var baseurl = "'.esc_js(home_url("/")).'";
				var pageurl = baseurl+"?s=&keyword='.esc_js($keyword).'&distance='.esc_js($distance).'&minprice='.esc_js($minprice).'&maxprice='.esc_js($maxprice).'&catid='.esc_js($getcatid).'&country='.esc_js($getcountry).'&city='.esc_js($getcity).'&state='.esc_js($getstate).'&zipcode='.esc_js($getzipcode).'&pagenum="+startpage+"&viewtype="+viewtype+"&numberofpages="+numberofpages+"&setorderby="+setorderby+"&setorder="+setorder+"&srhbybooking="+srhbybooking+"&srhdate="+srhdate+"&srhperiod="+srhperiod+"&srhtime="+srhtime+"&srhgender="+srhgender;
				 if(pageurl!=window.location){
						window.history.pushState({path:pageurl},"",pageurl);
				}
				service_finder_loadSearchResult(startpage,viewtype,numberofpages,setorderby,setorder,srhbybooking,srhdate,srhperiod,srhtime,srhgender);
               
                // Handle the view ypes
				jQuery("body").on("click", ".cvf_universal_container .cvf-universal-pagination li.activelink a", function(){
                    var page = jQuery(this).parents("li").attr("data-pnum");
					viewtype = jQuery("#viewTypes li.active").attr("data-view");
					var baseurl = "'.esc_js(home_url("/")).'";
					
					var setorderby = jQuery("#setorderby option:selected").val();
					var setorder = jQuery("#setorder option:selected").val();
					var numberofpages = jQuery("#numberofpages option:selected").val();
					var srhbybooking = jQuery("#srhbybooking option:selected").val();
					
					var srhdate = jQuery("#srhdate").val();
					var srhperiod = jQuery("#srhperiod option:selected").val();
					var srhtime = jQuery("#srhtime option:selected").val();
					
					var srhgender = jQuery("#srhgender option:selected").val();
					
					var pageurl = baseurl+"?s=&keyword='.esc_js($keyword).'&distance='.esc_js($distance).'&minprice='.esc_js($minprice).'&maxprice='.esc_js($maxprice).'&catid='.esc_js($getcatid).'&country='.esc_js($getcountry).'&city='.esc_js($getcity).'&state='.esc_js($getstate).'&zipcode='.esc_js($getzipcode).'&pagenum="+page+"&viewtype="+viewtype+"&numberofpages="+numberofpages+"&setorderby="+setorderby+"&setorder="+setorder+"&srhbybooking="+srhbybooking+"&srhdate="+srhdate+"&srhperiod="+srhperiod+"&srhtime="+srhtime+"&srhgender="+srhgender;
					 if(pageurl!=window.location){
						window.history.pushState({path:pageurl},"",pageurl);
					}
					  
					
                    service_finder_loadSearchResult(page,viewtype,numberofpages,setorderby,setorder,srhbybooking,srhdate,srhperiod,srhtime,srhgender);
                   
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
					var srhbybooking = jQuery("#srhbybooking option:selected").val();

					var baseurl = "'.esc_js(home_url("/")).'";
					
					var pageurl = baseurl+"?s=&keyword='.esc_js($keyword).'&distance='.esc_js($distance).'&minprice='.esc_js($minprice).'&maxprice='.esc_js($maxprice).'&catid='.esc_js($getcatid).'&country='.esc_js($getcountry).'&city='.esc_js($getcity).'&state='.esc_js($getstate).'&zipcode='.esc_js($getzipcode).'&pagenum="+page+"&viewtype="+viewtype+"&numberofpages="+numberofpages+"&setorderby="+setorderby+"&setorder="+setorder+"&srhbybooking="+srhbybooking;
					
					if(pageurl!=window.location){
						window.history.pushState({path:pageurl},"",pageurl);
					}
                    service_finder_loadSearchResult(page,viewtype,numberofpages,setorderby,setorder,srhbybooking);
                   
                });
				//Set number of pages for pagination
				jQuery("body").on("change", "#numberofpages", function(){
						var page = jQuery(".cvf_universal_container .cvf-universal-pagination li.selected").attr("data-pnum");
						var viewtype = jQuery("#viewTypes li.active").attr("data-view");
						
						var numberofpages = jQuery(this).val();
						var srhbybooking = jQuery("#srhbybooking option:selected").val();
						var setorderby = jQuery("#setorderby option:selected").val();
						var setorder = jQuery("#setorder option:selected").val();
						
						var baseurl = "'.esc_js(home_url("/")).'";
					
						var pageurl = baseurl+"?s=&keyword='.esc_js($keyword).'&distance='.esc_js($distance).'&minprice='.esc_js($minprice).'&maxprice='.esc_js($maxprice).'&catid='.esc_js($getcatid).'&country='.esc_js($getcountry).'&city='.esc_js($getcity).'&state='.esc_js($getstate).'&zipcode='.esc_js($getzipcode).'&pagenum="+page+"&viewtype="+viewtype+"&numberofpages="+numberofpages+"&setorderby="+setorderby+"&setorder="+setorder+"&srhbybooking="+srhbybooking;
						
						if(pageurl!=window.location){
							window.history.pushState({path:pageurl},"",pageurl);
						}
						service_finder_loadSearchResult(1,viewtype,numberofpages,setorderby,setorder,srhbybooking);
				});
				//Set order by
				jQuery("body").on("change", "#setorderby", function(){
						var page = jQuery(".cvf_universal_container .cvf-universal-pagination li.selected").attr("data-pnum");
						var viewtype = jQuery("#viewTypes li.active").attr("data-view");
						
						var setorderby = jQuery(this).val();
						var srhbybooking = jQuery("#srhbybooking option:selected").val();
						var numberofpages = jQuery("#numberofpages option:selected").val();
						var setorder = jQuery("#setorder option:selected").val();
						
						var baseurl = "'.esc_js(home_url("/")).'";
					
						var pageurl = baseurl+"?s=&keyword='.esc_js($keyword).'&distance='.esc_js($distance).'&minprice='.esc_js($minprice).'&maxprice='.esc_js($maxprice).'&catid='.esc_js($getcatid).'&country='.esc_js($getcountry).'&city='.esc_js($getcity).'&state='.esc_js($getstate).'&zipcode='.esc_js($getzipcode).'&pagenum="+page+"&viewtype="+viewtype+"&numberofpages="+numberofpages+"&setorderby="+setorderby+"&setorder="+setorder+"&srhbybooking="+srhbybooking;
						
						if(pageurl!=window.location){
							window.history.pushState({path:pageurl},"",pageurl);
						}
						service_finder_loadSearchResult(page,viewtype,numberofpages,setorderby,setorder,srhbybooking);
				});
				//Set order
				jQuery("body").on("change", "#setorder", function(){
						var page = jQuery(".cvf_universal_container .cvf-universal-pagination li.selected").attr("data-pnum");
						var viewtype = jQuery("#viewTypes li.active").attr("data-view");
						
						var setorder = jQuery(this).val();
						var srhbybooking = jQuery("#srhbybooking option:selected").val();
						var numberofpages = jQuery("#numberofpages option:selected").val();
						var setorderby = jQuery("#setorderby option:selected").val();
						var baseurl = "'.esc_js(home_url("/")).'";
					
						var pageurl = baseurl+"?s=&keyword='.esc_js($keyword).'&distance='.esc_js($distance).'&minprice='.esc_js($minprice).'&maxprice='.esc_js($maxprice).'&catid='.esc_js($getcatid).'&country='.esc_js($getcountry).'&city='.esc_js($getcity).'&state='.esc_js($getstate).'&zipcode='.esc_js($getzipcode).'&pagenum="+page+"&viewtype="+viewtype+"&numberofpages="+numberofpages+"&setorderby="+setorderby+"&setorder="+setorder+"&srhbybooking="+srhbybooking;
						
						if(pageurl!=window.location){
							window.history.pushState({path:pageurl},"",pageurl);
						}
						service_finder_loadSearchResult(page,viewtype,numberofpages,setorderby,setorder,srhbybooking);
				});
				
				jQuery("body").on("change", "#srhbybooking", function(){
						var page = jQuery(".cvf_universal_container .cvf-universal-pagination li.selected").attr("data-pnum");
						var viewtype = jQuery("#viewTypes li.active").attr("data-view");
						
						var srhbybooking = jQuery(this).val();
						var setorder = jQuery("#setorder option:selected").val();
						var numberofpages = jQuery("#numberofpages option:selected").val();
						var setorderby = jQuery("#setorderby option:selected").val();
						var baseurl = "'.esc_js(home_url("/")).'";
					
						var pageurl = baseurl+"?s=&keyword='.esc_js($keyword).'&distance='.esc_js($distance).'&minprice='.esc_js($minprice).'&maxprice='.esc_js($maxprice).'&catid='.esc_js($getcatid).'&country='.esc_js($getcountry).'&city='.esc_js($getcity).'&state='.esc_js($getstate).'&zipcode='.esc_js($getzipcode).'&pagenum="+page+"&viewtype="+viewtype+"&numberofpages="+numberofpages+"&setorderby="+setorderby+"&setorder="+setorder+"&srhbybooking="+srhbybooking;
						
						if(pageurl!=window.location){
							window.history.pushState({path:pageurl},"",pageurl);
						}
						service_finder_loadSearchResult(page,viewtype,numberofpages,setorderby,setorder,srhbybooking);
				});
				
				// Aavailability filter
				jQuery("body").on("click", "#avialabilityfilter", function(){
					var page = jQuery(".cvf_universal_container .cvf-universal-pagination li.selected").attr("data-pnum");
					
					var viewtype = jQuery("#viewTypes li.active").attr("data-view");
					
					var numberofpages = jQuery("#numberofpages option:selected").val();
					var setorderby = jQuery("#setorderby option:selected").val();
					var setorder = jQuery("#setorder option:selected").val();
					
					var srhbybooking = jQuery("#srhbybooking option:selected").val();
					var srhdate = jQuery("#srhdate").val();
					var srhperiod = jQuery("#srhperiod option:selected").val();
					var srhtime = jQuery("#srhtime option:selected").val();
					
					var srhgender = jQuery("#srhgender option:selected").val();
						
					var baseurl = "'.esc_js(home_url("/")).'";
					
					var pageurl = baseurl+"?s=&keyword='.esc_js($keyword).'&distance='.esc_js($distance).'&minprice='.esc_js($minprice).'&maxprice='.esc_js($maxprice).'&catid='.esc_js($getcatid).'&country='.esc_js($getcountry).'&city='.esc_js($getcity).'&state='.esc_js($getstate).'&zipcode='.esc_js($getzipcode).'&pagenum="+page+"&viewtype="+viewtype+"&numberofpages="+numberofpages+"&setorderby="+setorderby+"&setorder="+setorder+"&srhbybooking="+srhbybooking+"&srhdate="+srhdate+"&srhperiod="+srhperiod+"&srhtime="+srhtime+"&srhgender="+srhgender;
					if(pageurl!=window.location){
						window.history.pushState({path:pageurl},"",pageurl);
					}
					service_finder_loadSearchResult(page,viewtype,numberofpages,setorderby,setorder,srhbybooking,srhdate,srhperiod,srhtime,srhgender);
                   
                });
                           
            });', 'after' );
  ?>
    
  </div>
</div>
<?php if(service_finder_themestyle() != 'style-4'){ ?>   
<div class="title-section">
  <?php $viewtype = (isset($_GET['viewtype'])) ? $_GET['viewtype'] : $defaultview; ?>
  <?php echo do_action('service_finder_availability_search_filter', $viewtype); ?>
</div>
<?php } ?>
<!-- Display Search Result -->
<div class="section-content">
    <div class = "cvf_pag_loading">
      <div class = "cvf_universal_container">
        <div class="cvf-universal-content"></div>
      </div>
    </div>
</div>
<!-- result END -->
<?php } ?>
