<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/

/**
* Add the stylesheet
*/
function service_finder_base_scripts() {
 
	
}
add_action( 'wp_enqueue_scripts', 'service_finder_base_scripts' );


//Action hook to call shortcodes
add_action( 'init', 'service_finder_base_shortcodes');

function service_finder_base_shortcodes() {
	
	/* Claim Business Payment */
	function service_finder_claimbusiness_payment($atts, $content = null)
	{
			$html = '';
			require SERVICE_FINDER_BOOKING_TEMPLATES_DIR . '/claim-business-payment.php';
			
			return $html;
	}
	add_shortcode( 'service_finder_claimbusiness_payment', 'service_finder_claimbusiness_payment' );
	
	/* Forgot password function */
	function service_finder_fn_forgot_password($atts, $content = null)
	{
			$html = '';
			require SERVICE_FINDER_BOOKING_TEMPLATES_DIR . '/reset-password.php';
			
			return $html;
	}
	add_shortcode( 'service_finder_forgot_password', 'service_finder_fn_forgot_password' );

	/* Search Form */
	function service_finder_search_form($atts, $content = null)
	{
			if(service_finder_themestyle() == 'style-4' && !(is_home() || is_front_page()))
			{
				ob_start();
				if(is_tax( 'sf-cities' )){
				require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/search/templates/search-form-layout-2.php';
				}else{
				require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/search/templates/search-form-layout-3.php';
				}
				
				$html = ob_get_clean();
			}elseif(service_finder_themestyle() == 'style-3' || service_finder_themestyle() == 'style-4')
			{
				ob_start();
				if((is_home() || is_front_page()) && service_finder_themestyle() == 'style-4'){
				$bannerstyle = service_finder_banner_style();
				$headerstyle = service_finder_header_style();
				if($bannerstyle == 'old' && $headerstyle == 'banner'){
				require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/search/templates/search-form-layout-2.php';
				}else{
				require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/search/templates/search-form-layout-4.php';
				}
				
				}else{
				require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/search/templates/search-form-layout-2.php';
				}
				
				$html = ob_get_clean();
			}else
			{
				require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/search/templates/search-form.php';
			}
			
			return $html;
	}
	add_shortcode( 'service_finder_search_form', 'service_finder_search_form' );
	
	/* My Account Page */
	function service_finder_MyAccount($atts, $content = null)
	{
			if(!is_admin()){
			ob_start();
			require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/myaccount/MyAccount.php';
			require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/myservices/MyService.php';
			require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/availability/Availability.php';
			require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/business-hours/BusinessHours.php';
			require SERVICE_FINDER_BOOKING_FRONTEND_DIR . '/my-profile.php';
			return ob_get_clean();
			}
	}
	add_shortcode( 'service_finder_my_account', 'service_finder_MyAccount' );
	
	/* User Registration */
	function service_finder_signupform($atts, $content = null)
	{
			
			if(!is_admin()){
			ob_start();
			$a = shortcode_atts( array(
	
			   'role' => 'both',
	
			), $atts );
			
			require SERVICE_FINDER_BOOKING_FRONTEND_DIR . '/signup.php';
			
			return ob_get_clean();
			}
			
	}
	add_shortcode( 'service_finder_signup', 'service_finder_signupform' );
	
	/* User Login */
	function service_finder_loginform($atts, $content = null)
	{
			if(!is_admin()){
			ob_start();
			require SERVICE_FINDER_BOOKING_FRONTEND_DIR . '/login.php';
			
			return ob_get_clean();
			}
	}
	add_shortcode( 'service_finder_login', 'service_finder_loginform' );

  /* Become an Influencer Register */
	function service_finder_influencerform($atts, $content = null)
	{
			if(!is_admin()){
			ob_start();
			require SERVICE_FINDER_BOOKING_FRONTEND_DIR . '/become-influencer.php';
			
			return ob_get_clean();
			}
	}
	add_shortcode( 'service_finder_influencer', 'service_finder_influencerform' );
	
	/* Success Page */
	function service_finder_SuccessMessage($atts, $content = null)
	{
			
			if(!is_admin()){
			$html = '';
			require SERVICE_FINDER_BOOKING_TEMPLATES_DIR . '/success.php';
			
			return $html;
			}
	}
	add_shortcode( 'service_finder_success_message', 'service_finder_SuccessMessage' );
	
	/* Thank You Page */
	function service_finder_ThankYou($atts, $content = null)
	{
			
			if(!is_admin()){
			require SERVICE_FINDER_BOOKING_TEMPLATES_DIR . '/thank-you.php';
			
			return $html;
			}
			
	}
	add_shortcode( 'service_finder_thank_you', 'service_finder_ThankYou' );
	
	/* Fronten Map Search*/
	function service_finder_sedateMapSearch($atts, $content = null)
	{
	global $service_finder_options;
	
	if(is_home() || is_front_page()){
	$height = '';
	$mapclass = 'gmap_home';
	}else{
	if($service_finder_options['search-template'] == 'style-1' || !service_finder_show_map_on_site()){
	$height = 'style="height:600px"';
	$mapclass = '';
	}elseif($service_finder_options['search-template'] == 'style-2'){
	$height = 'style="height:100%"';
	$mapclass = '';
	}else{
	$height = 'style="height:600px"';
	$mapclass = '';
	}
	
	}
	
	$html = '<!-- Google Map -->
	<div id="gmap_wrapper" class="'.$mapclass.'" data-post_id="661" data-cur_lat="0" data-cur_long="0"  '.$height.'  >
		<span id="isgooglemap" data-isgooglemap="1"></span>       
	   
		<div id="gmap-controls-wrapper">
			<div id="gmapzoomplus"><i class="fa fa-plus"></i></div>
			<div id="gmapzoomminus"><i class="fa fa-minus"></i></div>
			<div  id="gmap-full"><i class="fa fa-arrows-alt"></i></div>
			<div  id="gmap-prev"><i class="fa fa-arrow-left"></i></div>
			<div  id="gmap-next"><i class="fa fa-arrow-right"></i></div>
		</div>
		<div id="googleMap" class="'.$mapclass.'" '.$height.'>   
		</div>    
	   <div class="tooltip"> click to enable zoom</div>
	   <div id="gmap-loading">     
			<div class="loader-inner ball-pulse"  id="listing_loader_maps">
				<div class="double-bounce1"></div>
				<div class="double-bounce2"></div>
			</div>
	   </div>
	  
	</div>    
	<!-- END Google Map --> ';		
	
	$defaultcity = (!empty($service_finder_options['default-city'])) ? $service_finder_options['default-city'] : '';
	if(!empty($service_finder_options['default-city'])){
	
	$defaultlat = get_option('defaultlat','28.6430536');
	$defaultlng = get_option('defaultlng','77.2223442');
	
	}else{
	$defaultlatlng = service_finder_get_default_latlong();
	$defaultlat = $defaultlatlng['defaultlat'];
	$defaultlng = $defaultlatlng['defaultlat'];
	}
	
	$defaults = array('general_latitude'=>$defaultlat,'general_longitude'=>$defaultlng,'path'=>'','idx_status'=>'0','page_custom_zoom'=>'12','markers'=>'','generated_pins'=>'0');
	
	$attr = shortcode_atts( $defaults, $atts );
	
	$imagepath = SERVICE_FINDER_BOOKING_IMAGE_URL.'/markers';
	
	$administrativeColor = (!empty($service_finder_options['map-color-administrative'])) ? $service_finder_options['map-color-administrative'] : '#0088ff';
	$landscapeColor = (!empty($service_finder_options['map-color-landscape'])) ? $service_finder_options['map-color-landscape'] : '#ff0000';
	$poiColor = (!empty($service_finder_options['map-color-poi-government'])) ? $service_finder_options['map-color-poi-government'] : '#aaaaaa';
	$roadGeometryColor = (!empty($service_finder_options['map-color-road-geometry'])) ? $service_finder_options['map-color-road-geometry'] : '#f0ece9';
	$roadLabelsColor = (!empty($service_finder_options['map-color-road-labels'])) ? $service_finder_options['map-color-road-labels'] : '#ccdca1';
	$waterAllColor = (!empty($service_finder_options['map-color-water-all'])) ? $service_finder_options['map-color-water-all'] : '#767676';
	$waterGeometryColor = (!empty($service_finder_options['map-color-water-geometry'])) ? $service_finder_options['map-color-water-geometry'] : '#ffffff';
	$waterLabelsColor = (!empty($service_finder_options['map-color-water-labels'])) ? $service_finder_options['map-color-water-labels'] : '#b8cb93';
	
	wp_add_inline_script( 'service_finder-js-gmapfunctions', 'var mapfunctions_vars = {"path":"'.esc_js($imagepath).'","pin_images":"{}","geolocation_radius":"1000","adv_search":"","in_text":"","zoom_cluster":"12","user_cluster":"yes","generated_pins":"0","geo_no_pos":"The browser couldn\'t detect your position!","geo_no_brow":"Geolocation is not supported by this browser.","map_style":"[{\"featureType\":\"water\",\"stylers\":[{\"saturation\":43},{\"lightness\":-11},{\"hue\":\"'.esc_js($administrativeColor).'\"}]},{\"featureType\":\"road\",\"elementType\":\"geometry.fill\",\"stylers\":[{\"hue\":\"'.esc_js($landscapeColor).'\"},{\"saturation\":-100},{\"lightness\":99}]},{\"featureType\":\"road\",\"elementType\":\"geometry.stroke\",\"stylers\":[{\"color\":\"'.esc_js($poiColor).'\"},{\"lightness\":54}]},{\"featureType\":\"landscape.man_made\",\"elementType\":\"geometry.fill\",\"stylers\":[{\"color\":\"'.esc_js($roadGeometryColor).'\"}]},{\"featureType\":\"poi.park\",\"elementType\":\"geometry.fill\",\"stylers\":[{\"color\":\"'.esc_js($roadLabelsColor).'\"}]},{\"featureType\":\"road\",\"elementType\":\"labels.text.fill\",\"stylers\":[{\"color\":\"'.esc_js($waterAllColor).'\"}]},{\"featureType\":\"road\",\"elementType\":\"labels.text.stroke\",\"stylers\":[{\"color\":\"'.esc_js($waterGeometryColor).'\"}]},{\"featureType\":\"poi\",\"stylers\":[{\"visibility\":\"off\"}]},{\"featureType\":\"landscape.natural\",\"elementType\":\"geometry.fill\",\"stylers\":[{\"visibility\":\"on\"},{\"color\":\"'.esc_js($waterLabelsColor).'\"}]},{\"featureType\":\"poi.park\",\"stylers\":[{\"visibility\":\"on\"}]},{\"featureType\":\"poi.sports_complex\",\"stylers\":[{\"visibility\":\"on\"}]},{\"featureType\":\"poi.medical\",\"stylers\":[{\"visibility\":\"on\"}]},{\"featureType\":\"poi.business\",\"stylers\":[{\"visibility\":\"simplified\"}]}]"};', 'before' );
			  
	return $html;	
	}
	add_shortcode( 'service_finder_map_search', 'service_finder_sedateMapSearch' );
	
	/*General Map*/
	function service_finder_sedateMap($atts, $content = null)
	{
	global $service_finder_options;
	
	$defaults = array('general_latitude'=>$atts['general_latitude'],'general_longitude'=>$atts['general_longitude'],'path'=>'','idx_status'=>'0','page_custom_zoom'=>'12','markers'=>'','generated_pins'=>'0');
	
	$attr = shortcode_atts( $defaults, $atts );
	
	$height = $atts['height'];
	
	$html = '<!-- Google Map -->
	<div id="gmap_wrapper"   data-post_id="661" data-cur_lat="0" data-cur_long="0"  style="height:'.$height.'"  >
		<span id="isgooglemap" data-isgooglemap="1"></span>       
	   
		<div id="gmap-controls-wrapper">
			<div id="gmapzoomplus"><i class="fa fa-plus"></i></div>
			<div id="gmapzoomminus"><i class="fa fa-minus"></i></div>
			<div  id="gmap-full"><i class="fa fa-arrows-alt"></i></div>
		</div>
		<div id="googleMap"  style="height:'.$height.'">   
		</div>    
	   <div class="tooltip"> click to enable zoom</div>
	   <div id="gmap-loading">     
			<div class="loader-inner ball-pulse"  id="listing_loader_maps">
				<div class="double-bounce1"></div>
				<div class="double-bounce2"></div>
			</div>
	   </div>
	  
	</div>    
	<!-- END Google Map --> ';		
	
	$imagepath = SERVICE_FINDER_BOOKING_IMAGE_URL.'/markers';
	
	$administrativeColor = (!empty($service_finder_options['map-color-administrative'])) ? $service_finder_options['map-color-administrative'] : '#0088ff';
	$landscapeColor = (!empty($service_finder_options['map-color-landscape'])) ? $service_finder_options['map-color-landscape'] : '#ff0000';
	$poiColor = (!empty($service_finder_options['map-color-poi-government'])) ? $service_finder_options['map-color-poi-government'] : '#aaaaaa';
	$roadGeometryColor = (!empty($service_finder_options['map-color-road-geometry'])) ? $service_finder_options['map-color-road-geometry'] : '#f0ece9';
	$roadLabelsColor = (!empty($service_finder_options['map-color-road-labels'])) ? $service_finder_options['map-color-road-labels'] : '#ccdca1';
	$waterAllColor = (!empty($service_finder_options['map-color-water-all'])) ? $service_finder_options['map-color-water-all'] : '#767676';
	$waterGeometryColor = (!empty($service_finder_options['map-color-water-geometry'])) ? $service_finder_options['map-color-water-geometry'] : '#ffffff';
	$waterLabelsColor = (!empty($service_finder_options['map-color-water-labels'])) ? $service_finder_options['map-color-water-labels'] : '#b8cb93';
	
	wp_add_inline_script( 'service_finder-js-gmapfunctions', 'var mapfunctions_vars = {"path":"'.esc_js($imagepath).'","pin_images":"{}","geolocation_radius":"1000","adv_search":"","in_text":"","zoom_cluster":"12","user_cluster":"yes","generated_pins":"0","geo_no_pos":"The browser couldn\'t detect your position!","geo_no_brow":"Geolocation is not supported by this browser.","map_style":"[{\"featureType\":\"water\",\"stylers\":[{\"saturation\":43},{\"lightness\":-11},{\"hue\":\"'.esc_js($administrativeColor).'\"}]},{\"featureType\":\"road\",\"elementType\":\"geometry.fill\",\"stylers\":[{\"hue\":\"'.esc_js($landscapeColor).'\"},{\"saturation\":-100},{\"lightness\":99}]},{\"featureType\":\"road\",\"elementType\":\"geometry.stroke\",\"stylers\":[{\"color\":\"'.esc_js($poiColor).'\"},{\"lightness\":54}]},{\"featureType\":\"landscape.man_made\",\"elementType\":\"geometry.fill\",\"stylers\":[{\"color\":\"'.esc_js($roadGeometryColor).'\"}]},{\"featureType\":\"poi.park\",\"elementType\":\"geometry.fill\",\"stylers\":[{\"color\":\"'.esc_js($roadLabelsColor).'\"}]},{\"featureType\":\"road\",\"elementType\":\"labels.text.fill\",\"stylers\":[{\"color\":\"'.esc_js($waterAllColor).'\"}]},{\"featureType\":\"road\",\"elementType\":\"labels.text.stroke\",\"stylers\":[{\"color\":\"'.esc_js($waterGeometryColor).'\"}]},{\"featureType\":\"poi\",\"stylers\":[{\"visibility\":\"off\"}]},{\"featureType\":\"landscape.natural\",\"elementType\":\"geometry.fill\",\"stylers\":[{\"visibility\":\"on\"},{\"color\":\"'.esc_js($waterLabelsColor).'\"}]},{\"featureType\":\"poi.park\",\"stylers\":[{\"visibility\":\"on\"}]},{\"featureType\":\"poi.sports_complex\",\"stylers\":[{\"visibility\":\"on\"}]},{\"featureType\":\"poi.medical\",\"stylers\":[{\"visibility\":\"on\"}]},{\"featureType\":\"poi.business\",\"stylers\":[{\"visibility\":\"simplified\"}]}]"};', 'before' );
			  
	return $html;	
	}
	add_shortcode( 'service_finder_map', 'service_finder_sedateMap' );
} 

/*Notification Bar*/
if ( !function_exists( 'service_finder_notification_bar' ) ){
function service_finder_notification_bar( ) {
global $wpdb, $service_finder_Tables, $current_user;

if(is_user_logged_in()){
$html  = '<ul class="login-bx  list-inline">';
$html .= '<li>';
if(service_finder_getUserRole($current_user->ID) == 'Provider'){
$res = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->notifications.' WHERE `provider_id` = %d AND `read` = "no" ORDER BY id DESC',$current_user->ID));
$count = count($res);
$html .= '<a href="javascript:;" data-delay="1000" data-hover="dropdown" data-toggle="dropdown" data-usertype="Provider" data-userid="'.$current_user->ID.'" class="dropdown-toggle sf-notifications" aria-expanded="false"><i class="fa fa-bell"></i> <span>'.esc_attr($count).'</span></a>';
if($count == 0){
$res = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->notifications.' WHERE `provider_id` = %d ORDER BY id DESC LIMIT 5',$current_user->ID));
}
}elseif(service_finder_getUserRole($current_user->ID) == 'Customer'){
$res = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->notifications.' WHERE `customer_id` = %d AND `read` = "no" ORDER BY id DESC',$current_user->ID));
$count = count($res);
$html .= '<a href="javascript:;" data-delay="1000" data-hover="dropdown" data-toggle="dropdown" data-usertype="Customer" data-userid="'.$current_user->ID.'" class="dropdown-toggle sf-notifications" aria-expanded="false"><i class="fa fa-bell"></i> <span>'.esc_attr($count).'</span></a>';
if($count == 0){
$res = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->notifications.' WHERE `customer_id` = %d ORDER BY id DESC LIMIT 5',$current_user->ID));
}
}
if(!empty($res)){
	$html .= '<ul class="dropdown-menu arrow-up sf-notification-list">';
	foreach($res as $row){
	if($row->read == 'yes'){
		$class = 'sf-read';
	}else{
		$class = 'sf-unread';
	}
	
	$url = service_finder_get_notification_link($row->topic,$row->target_id);
	
	$noticetitle = (!empty($row->title)) ? $row->title : $row->topic;
	
	$html .= '<li class="'.$class.'"><a href="'.esc_url($url).'">'.$noticetitle.': '.$row->notice.'</a></li>';
	}
	$html .= '</ul>';
}
$html .= '</li>';
$html .= '</ul>';
}else{
$html = '';
}

return $html;
}
add_shortcode( 'service_finder_notification_bar', 'service_finder_notification_bar' );
}

/*Notification Bar*/
if ( !function_exists( 'service_finder_myaccount_notification_bar' ) ){
function service_finder_myaccount_notification_bar( ) {
global $wpdb, $service_finder_Tables, $current_user;

if(is_user_logged_in()){
$html = '<li class="header-widget dropdown">';
if(service_finder_getUserRole($current_user->ID) == 'Provider'){
$res = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->notifications.' WHERE `provider_id` = %d AND `read` = "no" ORDER BY id DESC',$current_user->ID));
$count = count($res);

$html .= '<div data-delay="1000" data-hover="dropdown" data-toggle="dropdown" data-usertype="Provider" data-userid="'.$current_user->ID.'" class="aon-admin-notification dropdown-toggle sf-notifications" aria-expanded="false"><i class="fa fa-bell"></i> <span>'.esc_attr($count).'</span></div>';
								
if($count == 0){
$res = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->notifications.' WHERE `provider_id` = %d ORDER BY id DESC LIMIT 5',$current_user->ID));
}
}elseif(service_finder_getUserRole($current_user->ID) == 'Customer'){
$res = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->notifications.' WHERE `customer_id` = %d AND `read` = "no" ORDER BY id DESC',$current_user->ID));
$count = count($res);

$html .= '<div data-delay="1000" data-hover="dropdown" data-toggle="dropdown" data-usertype="Customer" data-userid="'.$current_user->ID.'" class="aon-admin-notification dropdown-toggle sf-notifications" aria-expanded="false"><i class="fa fa-bell"></i> <span>'.esc_attr($count).'</span></div>';

if($count == 0){
$res = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->notifications.' WHERE `customer_id` = %d ORDER BY id DESC LIMIT 5',$current_user->ID));
}
}

if(!empty($res)){
	$html .= '<ul class="dropdown-menu arrow-up sf-notification-list">';
	foreach($res as $row){
	if($row->read == 'yes'){
		$class = 'sf-read';
	}else{
		$class = 'sf-unread';
	}
	
	$url = service_finder_get_notification_link($row->topic,$row->target_id);
	
	$noticetitle = (!empty($row->title)) ? $row->title : $row->topic;
	
	$html .= '<li class="'.$class.'"><a href="'.esc_url($url).'">'.$noticetitle.': '.$row->notice.'</a></li>';
	}
	$html .= '</ul>';
}
$html .= '</li>';
}else{
$html = '';
}

return $html;
}
add_shortcode( 'service_finder_myaccount_notification_bar', 'service_finder_myaccount_notification_bar' );
} 

/*Notification Bar for Top Bar*/
if ( !function_exists( 'service_finder_notification_notopbar' ) ){
function service_finder_notification_notopbar( ) {
global $wpdb, $service_finder_Tables, $current_user;

if(is_user_logged_in()){
$html  = '';
if(service_finder_getUserRole($current_user->ID) == 'Provider'){
$res = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->notifications.' WHERE `provider_id` = %d AND `read` = "no" ORDER BY id DESC',$current_user->ID));
$count = count($res);
$html .= '<div class="extra-cell"><a href="javascript:;" data-delay="1000" data-hover="dropdown" data-toggle="dropdown" data-usertype="Provider" data-userid="'.$current_user->ID.'" class="dropdown-toggle btn btn-border btn-sm" aria-expanded="false"><i class="fa fa-bell"></i> <span>'.esc_attr($count).'</span></a>';
if($count == 0){
$res = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->notifications.' WHERE `provider_id` = %d ORDER BY id DESC LIMIT 5',$current_user->ID));
}
}elseif(service_finder_getUserRole($current_user->ID) == 'Customer'){
$res = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->notifications.' WHERE `customer_id` = %d AND `read` = "no" ORDER BY id DESC',$current_user->ID));
$count = count($res);
$html .= '<div class="extra-cell"><a href="javascript:;" data-delay="1000" data-hover="dropdown" data-toggle="dropdown" data-usertype="Customer" data-userid="'.$current_user->ID.'" class="dropdown-toggle btn btn-border btn-sm" aria-expanded="false"><i class="fa fa-bell"></i> <span>'.esc_attr($count).'</span></a>';
if($count == 0){
$res = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->notifications.' WHERE `customer_id` = %d ORDER BY id DESC LIMIT 5',$current_user->ID));
}
}
if(!empty($res)){
	$html .= '<ul class="dropdown-menu arrow-up sf-notifications">';
	foreach($res as $row){
	if($row->read == 'yes'){
		$class = 'sf-read';
	}else{
		$class = 'sf-unread';
	}
	$url = service_finder_get_notification_link($row->topic,$row->target_id);
	
	$noticetitle = (!empty($row->title)) ? $row->title : $row->topic;
	
	$html .= '<li class="'.$class.'"><a href="'.esc_url($url).'">'.$noticetitle.': '.$row->notice.'</a></li>';
	}
	$html .= '</ul>';
}
if(service_finder_getUserRole($current_user->ID) == 'Provider' || service_finder_getUserRole($current_user->ID) == 'Customer'){
$html .= '</div>';
}

}else{
$html = '';
}

return $html;
}
add_shortcode( 'service_finder_notification_notopbar', 'service_finder_notification_notopbar' );
} 

add_shortcode('service_finder_profile_avatar','service_finder_fn_profile_avatar');
function service_finder_fn_profile_avatar( $atts, $content = null )
{
ob_start();
global $service_finder_options,$wpdb;

$user_id = (!empty($atts['user_id'])) ? sanitize_text_field($atts['user_id']) : '';
?>

<div class="sf-avtarinfo-wrapper">
<div class="sf-img-section">
    <?php
    $profilethumb = service_finder_get_avatar_by_userid($user_id);
    ?>
    <img src="<?php echo esc_attr($profilethumb); ?>" alt="" class="sf-avatar-src profileavtthumb">
    
    <label for="file-upload" class="custom-file-upload site-button">
       <?php esc_html_e('Upload Image','service-finder'); ?>
    </label>
    <?php
	$croppedavatar = get_user_meta($user_id, 'cropped_user_avatar', true);
	$avatarid = service_finder_getUserAvatarID($user_id);
	$hideclosebtn = ($croppedavatar == '' && $avatarid == 0) ? 'hide' : '';
	?>
    <span class="sf-avatar-close delete-user-avatar <?php echo sanitize_html_class($hideclosebtn); ?>" data-userid="<?php echo esc_attr($user_id); ?>"><i class="fa fa-close"></i></span>
    <input type="file" id="file-upload" name="profilepic" accept=".jpg,.jpeg,.png,.gif,.bmp,.tiff">
</div>
<div class="sf-author-avatar-info">
<h5><?php esc_html_e('Upload Your Avatar','service-finder'); ?></h5>
<?php
$profileimageheight = 600;
$profileimagewidth = 600;
?>
<ul class="sf-author-avatar-limits list-angle-right">
  <li><strong><?php esc_html_e('Min width and height for good quality','service-finder'); ?>:</strong> <?php echo sprintf(esc_html__( '%d x %d px', 'service-finder' ),$profileimagewidth,$profileimageheight); ?></li>
  <li><strong><?php esc_html_e('Extensions','service-finder'); ?>:</strong> <?php esc_html_e('JPEG,PNG,GIF','service-finder'); ?></li>
</ul>
</div>
</div>

<div class="crop-img-area m-b30 clearfix">
    <div class="sf-cropimg-table">
        <div class="crop-img-left">
            <div class="img-container">
              <img id="image" src="<?php echo ($avatarid > 0) ? service_finder_get_image_url($avatarid,'full') : service_finder_fn_preview_placeholder(); ?>" alt="Picture">
              <input type="hidden" name="croppedimage" id="croppedimage">
              <input type="hidden" name="minheight" value="">
              <input type="hidden" name="minwidth" value="">
            </div>
            
            <div class="docs-buttons">
                <div class="btn-group">
                  <button type="button" class="site-button outline gray" data-method="setDragMode" data-option="move">
                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="<?php esc_html_e('Move','service-finder'); ?>">
                      <span class="fa fa-arrows-alt"></span>
                    </span>
                  </button>
                </div>
        
                <div class="btn-group">
                  <button type="button" class="site-button outline gray" data-method="zoom" data-option="0.1">
                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="<?php esc_html_e('Zoom In','service-finder'); ?>">
                      <span class="fa fa-search-plus"></span>
                    </span>
                  </button>
                  <button type="button" class="site-button outline gray" data-method="zoom" data-option="-0.1">
                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="<?php esc_html_e('Zoom Out','service-finder'); ?>">
                      <span class="fa fa-search-minus"></span>
                    </span>
                  </button>
                </div>
        
                <div class="btn-group">
                  <button type="button" class="site-button outline gray" data-method="move" data-option="-10" data-second-option="0">
                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="<?php esc_html_e('Move Left','service-finder'); ?>">
                      <span class="fa fa-arrow-left"></span>
                    </span>
                  </button>
                  <button type="button" class="site-button outline gray" data-method="move" data-option="10" data-second-option="0">
                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="<?php esc_html_e('Move Right','service-finder'); ?>">
                      <span class="fa fa-arrow-right"></span>
                    </span>
                  </button>
                  <button type="button" class="site-button outline gray" data-method="move" data-option="0" data-second-option="-10">
                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="<?php esc_html_e('Move Up','service-finder'); ?>">
                      <span class="fa fa-arrow-up"></span>
                    </span>
                  </button>
                  <button type="button" class="site-button outline gray" data-method="move" data-option="0" data-second-option="10">
                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="<?php esc_html_e('Move Down','service-finder'); ?>">
                      <span class="fa fa-arrow-down"></span>
                    </span>
                  </button>
                </div>
        
                <div class="btn-group">
                  <button type="button" class="site-button outline gray" data-method="rotate" data-option="-45">
                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="<?php esc_html_e('Rotate Left','service-finder'); ?>">
                      <span class="fa fa-rotate-left"></span>
                    </span>
                  </button>
                  <button type="button" class="site-button outline gray" data-method="rotate" data-option="45">
                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="<?php esc_html_e('Rotate Right','service-finder'); ?>">
                      <span class="fa fa-rotate-right"></span>
                    </span>
                  </button>
                </div>
        
                <div class="btn-group">
                  <button type="button" class="site-button outline gray" data-method="scaleX" data-option="-1">
                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="<?php esc_html_e('Flip Horizontal','service-finder'); ?>">
                      <span class="fa fa-arrows-h"></span>
                    </span>
                  </button>
                  <button type="button" class="site-button outline gray" data-method="scaleY" data-option="-1">
                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="<?php esc_html_e('Flip Vertical','service-finder'); ?>">
                      <span class="fa fa-arrows-v"></span>
                    </span>
                  </button>
                </div>
        
                <div class="btn-group">
                  <button type="button" class="site-button outline gray" data-method="reset">
                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="<?php esc_html_e('Reset','service-finder'); ?>">
                      <span class="fa fa-refresh"></span>
                    </span>
                  </button>
                </div>
        
                
        
              </div>
      
      		<div class="docs-buttons docs-preview-buttons">
                <div class="btn-group btn-group-crop">
                  <button type="button" class="site-button outline green" data-method="getCroppedCanvas" data-option="{ &quot;maxWidth&quot;: 4096, &quot;maxHeight&quot;: 4096 }">
                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="<?php esc_html_e('Preview','service-finder'); ?>">
                      <i class="fa fa-object-group"></i> <?php esc_html_e('Crop Image','service-finder'); ?>
                    </span>
                  </button>
                </div>
        	</div>
        </div>
        
        <div class="crop-img-right box-2 img-result">
            <div class="sf-dimension-wrap">
            <div class="docs-preview clearfix">
              <div class="profile-preview img-preview preview-lg"></div>
            </div>
            
            <div class="docs-data">
              <div class="input-group input-group-sm">
                <span class="input-group-prepend">
                  <label class="input-group-text" for="dataWidth"><?php esc_html_e('Width','service-finder'); ?></label>
                </span>
                <input type="text" class="form-control" id="dataWidth" placeholder="width">
                <span class="input-group-append">
                  <span class="input-group-text">px</span>
                </span>
              </div>
              <div class="input-group input-group-sm">
                <span class="input-group-prepend">
                  <label class="input-group-text" for="dataHeight"><?php esc_html_e('Height','service-finder'); ?></label>
                </span>
                <input type="text" class="form-control" id="dataHeight" placeholder="height">
                <span class="input-group-append">
                  <span class="input-group-text">px</span>
                </span>
              </div>
          	</div>
            </div>
            
            <h5 class="crop-img-title"><?php esc_html_e('Preview','service-finder'); ?></h5>
            <div class="cropped sf-preview-placeholder"><img class="sf-preview-placeholder" src="<?php echo service_finder_fn_preview_placeholder(); ?>" alt=""></div>
        </div>
    </div>
    
</div>

<?php
return ob_get_clean();
}

add_shortcode('service_finder_crop_cover_image','service_finder_fn_crop_cover_image');
function service_finder_fn_crop_cover_image( $atts, $content = null )
{
ob_start();
global $service_finder_options,$wpdb;

$user_id = (!empty($atts['user_id'])) ? sanitize_text_field($atts['user_id']) : '';
?>

<div class="sf-avtarinfo-wrapper sf-coverinfo-wrapper">
<div class="sf-img-section">
    <?php
	$imagethumb = service_finder_get_user_coverimage($user_id,'full')
    ?>
    <img src="<?php echo esc_attr($imagethumb); ?>" alt="" class="sf-avatar-src profilecoverthumb">
    
    <label for="cover-upload" class="custom-file-upload site-button">
       <?php esc_html_e('Upload Image','service-finder'); ?>
    </label>
    <?php
	$croppedcoverimage = get_user_meta($user_id, 'cropped_cover_image', true);
	$hideclosebtn = ($croppedcoverimage == '') ? 'hide' : '';
	?>
    <span class="sf-avatar-close delete-cover-image <?php echo sanitize_html_class($hideclosebtn); ?>" data-userid="<?php echo esc_attr($user_id); ?>"><i class="fa fa-close"></i></span>
    <input type="file" id="cover-upload" name="coverpic" accept=".jpg,.jpeg,.png,.gif,.bmp,.tiff">
</div>
<div class="sf-author-avatar-info">
<h5><?php esc_html_e('Upload Cover Image','service-finder'); ?></h5>
<?php
$coverimageimageheight = 1900;
$coverimageimagewidth = 1200;
?>
<ul class="sf-author-avatar-limits list-angle-right">
  <li><strong><?php esc_html_e('Min width and height for good quality','service-finder'); ?>:</strong> <?php echo sprintf(esc_html__( '%d x %d px', 'service-finder' ),$coverimageimagewidth,$coverimageimageheight); ?></li>
  <li><strong><?php esc_html_e('Extensions','service-finder'); ?>:</strong> <?php esc_html_e('JPEG,PNG,GIF','service-finder'); ?></li>
</ul>
</div>
</div>

<div class="crop-img-area m-b30 clearfix">
    <div class="sf-cropimg-table">
        <div class="crop-img-left">
            <div class="img-container">
              <img id="coverimage" src="<?php echo ($croppedcoverimage > 0) ? service_finder_get_image_url($croppedcoverimage,'full') : service_finder_fn_crop_preview_placeholder(); ?>" alt="Picture">
              <input type="hidden" name="croppedcoverimage" id="croppedcoverimage">
              <input type="hidden" name="coverminheight" value="">
              <input type="hidden" name="coverminwidth" value="">
            </div>
            
            <div class="cover-buttons">
                <div class="btn-group">
                  <button type="button" class="site-button outline gray" data-method="setDragMode" data-option="move">
                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="<?php esc_html_e('Move','service-finder'); ?>">
                      <span class="fa fa-arrows-alt"></span>
                    </span>
                  </button>
                </div>
        
                <div class="btn-group">
                  <button type="button" class="site-button outline gray" data-method="zoom" data-option="0.1">
                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="<?php esc_html_e('Zoom In','service-finder'); ?>">
                      <span class="fa fa-search-plus"></span>
                    </span>
                  </button>
                  <button type="button" class="site-button outline gray" data-method="zoom" data-option="-0.1">
                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="<?php esc_html_e('Zoom Out','service-finder'); ?>">
                      <span class="fa fa-search-minus"></span>
                    </span>
                  </button>
                </div>
        
                <div class="btn-group">
                  <button type="button" class="site-button outline gray" data-method="move" data-option="-10" data-second-option="0">
                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="<?php esc_html_e('Move Left','service-finder'); ?>">
                      <span class="fa fa-arrow-left"></span>
                    </span>
                  </button>
                  <button type="button" class="site-button outline gray" data-method="move" data-option="10" data-second-option="0">
                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="<?php esc_html_e('Move Right','service-finder'); ?>">
                      <span class="fa fa-arrow-right"></span>
                    </span>
                  </button>
                  <button type="button" class="site-button outline gray" data-method="move" data-option="0" data-second-option="-10">
                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="<?php esc_html_e('Move Up','service-finder'); ?>">
                      <span class="fa fa-arrow-up"></span>
                    </span>
                  </button>
                  <button type="button" class="site-button outline gray" data-method="move" data-option="0" data-second-option="10">
                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="<?php esc_html_e('Move Down','service-finder'); ?>">
                      <span class="fa fa-arrow-down"></span>
                    </span>
                  </button>
                </div>
        
                <div class="btn-group">
                  <button type="button" class="site-button outline gray" data-method="rotate" data-option="-45">
                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="<?php esc_html_e('Rotate Left','service-finder'); ?>">
                      <span class="fa fa-rotate-left"></span>
                    </span>
                  </button>
                  <button type="button" class="site-button outline gray" data-method="rotate" data-option="45">
                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="<?php esc_html_e('Rotate Right','service-finder'); ?>">
                      <span class="fa fa-rotate-right"></span>
                    </span>
                  </button>
                </div>
        
                <div class="btn-group">
                  <button type="button" class="site-button outline gray" data-method="scaleX" data-option="-1">
                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="<?php esc_html_e('Flip Horizontal','service-finder'); ?>">
                      <span class="fa fa-arrows-h"></span>
                    </span>
                  </button>
                  <button type="button" class="site-button outline gray" data-method="scaleY" data-option="-1">
                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="<?php esc_html_e('Flip Vertical','service-finder'); ?>">
                      <span class="fa fa-arrows-v"></span>
                    </span>
                  </button>
                </div>
        
                <div class="btn-group">
                  <button type="button" class="site-button outline gray" data-method="reset">
                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="<?php esc_html_e('Reset','service-finder'); ?>">
                      <span class="fa fa-refresh"></span>
                    </span>
                  </button>
                </div>
        
                
        
              </div>
      
      		<div class="cover-buttons docs-preview-buttons">
                <div class="btn-group btn-group-crop">
                  <button type="button" class="site-button outline green" data-method="getCroppedCanvas" data-option="{ &quot;maxWidth&quot;: 4096, &quot;maxHeight&quot;: 4096 }">
                    <span class="docs-tooltip" data-toggle="tooltip" data-animation="false" title="<?php esc_html_e('Preview','service-finder'); ?>">
                      <i class="fa fa-object-group"></i> <?php esc_html_e('Crop Image','service-finder'); ?>
                    </span>
                  </button>
                </div>
        	</div>
        </div>
        
        <div class="crop-img-right box-2 img-result">
            <div class="sf-dimension-wrap">
            <div class="docs-preview clearfix">
              <div class="cover-preview img-preview preview-lg"></div>
            </div>
            
            <div class="docs-data">
              <div class="input-group input-group-sm">
                <span class="input-group-prepend">
                  <label class="input-group-text" for="dataWidth"><?php esc_html_e('Width','service-finder'); ?></label>
                </span>
                <input type="text" class="form-control" id="coverdataWidth" placeholder="width">
                <span class="input-group-append">
                  <span class="input-group-text">px</span>
                </span>
              </div>
              <div class="input-group input-group-sm">
                <span class="input-group-prepend">
                  <label class="input-group-text" for="dataHeight"><?php esc_html_e('Height','service-finder'); ?></label>
                </span>
                <input type="text" class="form-control" id="coverdataHeight" placeholder="height">
                <span class="input-group-append">
                  <span class="input-group-text">px</span>
                </span>
              </div>
          	</div>
            </div>
            
            <h5 class="crop-img-title"><?php esc_html_e('Preview','service-finder'); ?></h5>
            <div class="covercropped sf-preview-placeholder"><img class="sf-preview-placeholder" src="<?php echo service_finder_fn_crop_preview_placeholder(); ?>" alt=""></div>
        </div>
    </div>
    
</div>

<?php
return ob_get_clean();
}