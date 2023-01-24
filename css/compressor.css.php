<?php
global $wp_customize, $service_finder_options, $wp_filesystem, $wpdb;
$root = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
if ( file_exists( $root.'/wp-load.php' ) ) {
    require_once( $root.'/wp-load.php' );
} else {
    $root = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
    if ( file_exists( $root.'/wp-load.php' ) ) {
    require_once( $root.'/wp-load.php' );
    }
}
if ( empty( $wp_filesystem ) ) {
	require_once ABSPATH . '/wp-admin/includes/file.php';
	WP_Filesystem();
}
$writabledir = SERVICE_FINDER_BOOKING_DIR.'/inc/caches/';
$md5_dir = SERVICE_FINDER_BOOKING_DIR.'/css/';
global $options_data;
function compress( $minify ) {
    $minify = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $minify );
    $minify = str_replace( array("\r\n", "\r", "\n", "\t", '  ', '    ', '    ','(inc/'), array('','', '', '', '', '', '', '(../'), $minify );
    return $minify;
}
header('Content-type: text/css');
header('Expires: '.gmdate("D, d M Y H:i:s", time() + 3600*24*365).' GMT');
$name = service_finder_booking_scan_dir($md5_dir).'.css';
if( !file_exists($writabledir.$name) ){
	ob_start("compress");
	
	include(SERVICE_FINDER_BOOKING_DIR.'/css/bootstrap-select.min.css');
	include(SERVICE_FINDER_BOOKING_DIR.'/assets/bootstrap-calendar/calendar.css');
	
	include(SERVICE_FINDER_BOOKING_DIR.'/assets/booking-calendar/booking_calendar.min.css');
	
	include(SERVICE_FINDER_BOOKING_DIR.'/frontend/modules/invoice/resources/datepicker.min.css');
	
	include(SERVICE_FINDER_BOOKING_DIR.'/assets/ratings/star-rating.css');
	include(SERVICE_FINDER_BOOKING_DIR.'/assets/datatable/dataTables.customLoader.walker.css');
	include(SERVICE_FINDER_BOOKING_DIR.'/assets/datatable/dataTables.bootstrap.css');
	include(SERVICE_FINDER_BOOKING_DIR.'/assets/datatable/dataTables.tableTools.css');
	include(SERVICE_FINDER_BOOKING_DIR.'/assets/manage-uploads/image-upload.css');
	include(SERVICE_FINDER_BOOKING_DIR.'/assets/manage-uploads/image-manager.css');
	include(SERVICE_FINDER_BOOKING_DIR.'/assets/map/map.css');
	include(SERVICE_FINDER_BOOKING_DIR.'/assets/embeded/embeded.css');
	include(SERVICE_FINDER_BOOKING_DIR.'/assets/validator/bootstrapValidator.css');
	include(SERVICE_FINDER_BOOKING_DIR.'/css/custom-inline.css');
	include(SERVICE_FINDER_BOOKING_DIR.'/css/city-autocomplete.css');
	

	$wp_filesystem->put_contents($writabledir.$name, compress(ob_get_contents()), FS_CHMOD_FILE); // Save it
	service_finder_booking_delete_old_cache($writabledir);
	ob_end_flush();
} else {
	$wp_filesystem->get_contents($writabledir.$name);
}
?>