<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/

/*Get My Favorites*/
add_action('wp_ajax_get_my_favorites', 'service_finder_get_my_favorites');
add_action('wp_ajax_nopriv_get_my_favorites', 'service_finder_get_my_favorites');

function service_finder_get_my_favorites(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/favorites/Favorites.php';
$getFavorites = new SERVICE_FINDER_Favorites();
$getFavorites->service_finder_getFavorites();
exit;
}

/*Delete Favorites*/
add_action('wp_ajax_delete_favorites', 'service_finder_delete_favorites');
add_action('wp_ajax_nopriv_delete_favorites', 'service_finder_delete_favorites');

function service_finder_delete_favorites(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/favorites/Favorites.php';
$deleteFavorites = new SERVICE_FINDER_Favorites();
$deleteFavorites->service_finder_deleteFavorites();
exit;
}