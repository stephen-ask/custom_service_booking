<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/

/*Add article ajax call*/
add_action('wp_ajax_add_articles', 'service_finder_add_articles');
function service_finder_add_articles(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/articles/Articles.php';
$addArticle = new SERVICE_FINDER_Articles();
$addArticle->service_finder_addArticle($_POST);
exit;
}

/*Get Article ajax call*/
add_action('wp_ajax_get_articles', 'service_finder_get_articles');
function service_finder_get_articles(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/articles/Articles.php';
$getArticles = new SERVICE_FINDER_Articles();
$getArticles->service_finder_getArticles($_POST);
exit;
}

/*Delete article ajax call*/
add_action('wp_ajax_delete_article', 'service_finder_delete_article');
function service_finder_delete_article(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/articles/Articles.php';
$deleteArticle = new SERVICE_FINDER_Articles();
$deleteArticle->service_finder_deleteArticles($_POST);
exit;
}

/*Load article ajax call*/
add_action('wp_ajax_load_article', 'service_finder_load_article');
function service_finder_load_article(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/articles/Articles.php';
$loadArticle = new SERVICE_FINDER_Articles();
$loadArticle->service_finder_loadArticle($_POST);
exit;
}

/*Load article ajax call*/
add_action('wp_ajax_update_article', 'service_finder_update_article');
function service_finder_update_article(){
global $wpdb;
require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/articles/Articles.php';
$updateArticle = new SERVICE_FINDER_Articles();
$updateArticle->service_finder_updateArticle($_POST);
exit;
}


