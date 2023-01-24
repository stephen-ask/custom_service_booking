<?php

/*

Plugin Name: Service Finder Bookings

Plugin URI: http://aonetheme.com/

Description: This is a plugin for all booking related functionality

Version: 3.5.1

Author: Aonetheme

Author URI: http://aonetheme.com/

*/



define('SERVICE_FINDER_BOOKING_URL', plugin_dir_url( __FILE__ ));

define('SERVICE_FINDER_BOOKING_DIR', plugin_dir_path(__FILE__));

define('SERVICE_FINDER_BOOKING_TEMPLATES_DIR', plugin_dir_path(__FILE__) . 'templates/');

define('SERVICE_FINDER_BOOKING_JS_URL', SERVICE_FINDER_BOOKING_URL . 'js');

define('SERVICE_FINDER_BOOKING_CSS_URL', SERVICE_FINDER_BOOKING_URL . 'css');

define('SERVICE_FINDER_BOOKING_IMAGE_URL', SERVICE_FINDER_BOOKING_URL . 'images');

define('SERVICE_FINDER_ASSESTS_ADMINURL', home_url('/') . '/wp-admin');

define('SERVICE_FINDER_BOOKING_ASSESTS_URL', SERVICE_FINDER_BOOKING_URL . 'assets');

$locale = get_locale(); 

$temp = explode('_',$locale);

if(!empty($temp)){

$langcode = strtolower($temp[0]);

define('LANGCODE', $langcode);

}else{

$langcode = 'en';

define('LANGCODE', $langcode);

}

define('LANGUAGE_CODE', $langcode);



define('SERVICE_FINDER_BOOKING_FRONTEND_DIR', plugin_dir_path(__FILE__) . 'frontend');



define('SERVICE_FINDER_BOOKING_LIB_DIR', plugin_dir_path(__FILE__) . 'lib');



define('SERVICE_FINDER_BOOKING_FONTS_DIR', plugin_dir_path(__FILE__) . 'fonts');



define('SERVICE_FINDER_BOOKING_LIB_URL', SERVICE_FINDER_BOOKING_URL . 'lib');



define('SERVICE_FINDER_BOOKING_INC_URL', SERVICE_FINDER_BOOKING_URL . 'inc');



define('SERVICE_FINDER_BOOKING_ADMIN_DIR', plugin_dir_path(__FILE__) . 'admin');



define('SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR', plugin_dir_path(__FILE__) . 'frontend/modules');



define('SERVICE_FINDER_BOOKING_FRONTEND_MODULE_URL', SERVICE_FINDER_BOOKING_URL . 'frontend/modules');



define('SERVICE_FINDER_BOOKING_ADMIN_MODULE_DIR', SERVICE_FINDER_BOOKING_DIR . '/admin/modules');



define('SERVICE_FINDER_BOOKING_ADMIN_MODULE_URL', SERVICE_FINDER_BOOKING_URL . 'admin/modules');



define('SERVICE_FINDER_PAYMENT_GATEWAY_DIR', SERVICE_FINDER_BOOKING_DIR . '/payment-gateway');



if(!class_exists('service_finder_booking_plugin')) {

	class service_finder_booking_plugin {

		/**

		 * Construct the plugin object

		 */

		public function __construct() {

			global $service_finder_options, $wpdb;



			$service_finder_options = get_option('service_finder_options');

			

			add_action("init", array(&$this, 'service_finder_init'));

			

			add_action( 'init', array(&$this, 'service_finder_comment_rating_post_type'), 0 );

			

			add_action("plugins_loaded", array(&$this, 'service_finder_update_database'));

			

			require plugin_dir_path(__FILE__) . 'lib/multi-post-thumbnails.php'; //multi-post-thumbnails

			

			require plugin_dir_path(__FILE__) . 'lib/sedateManager.php'; //Include core extende file for admin panel

			

			require plugin_dir_path(__FILE__) . 'admin/includes.php'; //Include All Admin Files

			

			require plugin_dir_path(__FILE__) . 'templates/base-shortcodes.php'; //Shortcode for templates

			

			require plugin_dir_path(__FILE__) . 'frontend/modules/myaccount/account-data.php'; //My Account Ajax Call

			

			require plugin_dir_path(__FILE__) . 'frontend/modules/cities/city-data.php'; //My Account Ajax Call



			require plugin_dir_path(__FILE__) . 'frontend/modules/category/category-data.php'; //Category Ajax Call

			

			require plugin_dir_path(__FILE__) . 'frontend/modules/myservices/service-data.php'; //My Services Ajax Call

			

			require plugin_dir_path(__FILE__) . 'frontend/modules/branches/branch-data.php'; //My Services Ajax Call

			

			require plugin_dir_path(__FILE__) . 'frontend/modules/search/search-data.php'; //Search Ajax Call

			

			require plugin_dir_path(__FILE__) . 'frontend/modules/team-members/team-data.php'; //Team Members Ajax Call

			

			require plugin_dir_path(__FILE__) . 'frontend/modules/get-quote/quote-data.php'; //Get quote form Ajax Call

			

			require plugin_dir_path(__FILE__) . 'frontend/modules/newsletter/nl-data.php'; //Newsletter Ajax Call

			

			require plugin_dir_path(__FILE__) . 'frontend/modules/business-hours/bh-data.php'; //Business Hours Ajax Call

			

			require plugin_dir_path(__FILE__) . 'frontend/modules/business-hours/SFTimeSlots.php'; //Business Hours Ajax Call

			

			require plugin_dir_path(__FILE__) . 'frontend/modules/business-hours/SFDateInterval.php'; //Business Hours Ajax Call

			

			require plugin_dir_path(__FILE__) . 'frontend/modules/business-hours/SFDateTime.php'; //Business Hours Ajax Call

			

			require plugin_dir_path(__FILE__) . 'frontend/modules/search/searchProviders.php'; //Search Providers Ajax Call

			

			require plugin_dir_path(__FILE__) . 'frontend/modules/bookings/bookings-data.php'; //Display all booking Ajax Call

			

			require plugin_dir_path(__FILE__) . 'frontend/modules/favorites/favorites-data.php'; //Favorite Ajax Call

			

			require plugin_dir_path(__FILE__) . 'frontend/modules/availability/availability-data.php'; //Availability Ajax Call

			

			require plugin_dir_path(__FILE__) . 'frontend/modules/service-area/servicearea-data.php'; //Service Area Ajax Call

			

			require plugin_dir_path(__FILE__) . 'frontend/modules/articles/articles-data.php'; //Service Area Ajax Call

			

			require plugin_dir_path(__FILE__) . 'frontend/modules/experience/experience-data.php'; //Service Area Ajax Call

			

			require plugin_dir_path(__FILE__) . 'frontend/modules/payout/payout-data.php'; //Service Area Ajax Call



			require plugin_dir_path(__FILE__) . 'frontend/modules/certificates/certificates-data.php'; //Service Area Ajax Call

			

			require plugin_dir_path(__FILE__) . 'frontend/modules/offers/offers-data.php'; //Service Area Ajax Call



			require plugin_dir_path(__FILE__) . 'frontend/modules/qualification/qualification-data.php'; //Service Area Ajax Call

			

			require plugin_dir_path(__FILE__) . 'frontend/modules/myaccount/library/outerarea.php'; //My Account Library files

			

			require plugin_dir_path(__FILE__) . 'frontend/modules/myaccount/library/managefiles.php'; //My Account Library files for file manage

			

			require plugin_dir_path(__FILE__) . 'frontend/modules/myaccount/library/image-manager.php'; //My Account Library files for image manage

			

			require plugin_dir_path(__FILE__) . 'frontend/modules/myaccount/library/embeded.php'; //My Account Library files for embeded code for videos

			

			add_action( 'widgets_init', array(&$this, 'service_finder_load_custom_widget') );

			

			add_filter( 'cron_schedules', array(&$this, 'service_finder_every_minute') );

			

			add_action('admin_enqueue_scripts', array(&$this, 'service_finder_admin_styles'));

			

			add_action('admin_enqueue_scripts', array(&$this, 'service_finder_admin_scripts'));

			

			add_action('wp_enqueue_scripts', array(&$this, 'service_finder_frontend_scripts'));

			

			add_action('wp_head', array(&$this, 'service_finder_inline_scripts'),9);

			

			add_action('admin_head', array(&$this, 'service_finder_inline_scripts'),9);

			

			/*Add provider fields in create/edit user from admin*/

			add_action( 'user_new_form', array( $this, 'service_finder_extra_user_profile_fields' ) );

			

			add_action( 'edit_user_profile', array( $this, 'service_finder_extra_user_profile_fields' ) );

			

			add_action( 'show_user_profile', array( $this, 'service_finder_extra_user_profile_fields' ) );

			

			/*Save provider profile fileds*/

			add_action( 'user_register', array( $this, 'service_finder_save_provider_data' ) );

			

			add_action( 'user_profile_update_errors', array( $this, 'service_finder_validate_field' ) );

			

			/*Update provider profile*/

			add_action( 'edit_user_profile_update', array(&$this, 'service_finder_update_user_profile') );

			

			/*Expire Featured Functionality from Providers*/

			add_action('service_finder_check_feature_expirations', array(&$this, 'service_finder_CheckFeatureExpirations'));

			

			/*Expire Featured Functionality from Providers*/

			add_action('service_finder_check_joblimit_expirations', array(&$this, 'service_finder_CheckJobLimitExpirations'));

			

			/*Expire Featured Functionality from Providers*/

			add_action('service_finder_booking_reminder_mail', array(&$this, 'service_finder_booking_reminder_mail'));

			

			/*Update payout status*/

			add_action('service_finder_update_payout_status', array(&$this, 'service_finder_update_payout_status'));			/*Update mp payout status*/			add_action('service_finder_update_mp_payout_status', array(&$this, 'service_finder_update_mp_payout_status'));	

			

			add_action("admin_init", array(&$this, 'service_finder_sedateCreateRoles'));

			

			/*Remove Provider Role from Admin Panel New User DropDwon*/

			add_action('editable_roles', array(&$this, 'service_finder_exclude_provider_role'),99);

			

			/*Remove Provider Role from Admin Panel New User DropDwon*/

			add_filter( 'template_include', array(&$this, 'service_finder_provider_page_template'), 99 );

			

			add_action( 'wp_footer', array(&$this, 'service_finder_add_modal_popup'));

			

			$keepauthorword = (!empty($service_finder_options['keep-author-word'])) ? $service_finder_options['keep-author-word'] : '';

			$authorreplacestring = (!empty($service_finder_options['author-replace-string'])) ? $service_finder_options['author-replace-string'] : '';



			if($keepauthorword == 'no'){

				add_filter( 'author_rewrite_rules', array(&$this, 'service_finder_no_author_base_rewrite_rules'));

				

				add_action( 'init', array(&$this, 'service_finder_remove_author_permalinks'));

				

				add_filter( 'post_type_link', array(&$this, 'service_finder_no_author_comment_link_filter_function'));

				

			}

			

			if($keepauthorword == 'yes' && $authorreplacestring != ""){

				add_filter( 'author_rewrite_rules', array(&$this, 'service_finder_replace_author_rewrite_rules'));

				

				add_action( 'init', array(&$this, 'service_finder_replace_author_permalinks'));

				

				add_filter( 'post_type_link', array(&$this, 'service_finder_comment_link_filter_function'));

				

			}elseif($keepauthorword == 'yes' && $authorreplacestring == ""){

				add_filter( 'post_type_link', array(&$this, 'service_finder_comment_link_filter_function'));

			}

			

			add_filter( 'post_type_link', array(&$this, 'service_finder_answer_comment_link'),10,2);

			

		} // END public function __construct

		

		function service_finder_answer_comment_link( $post_link, $post  ){

 			global $wp_rewrite, $service_finder_options;

			 if ( 'sf_answers' == get_post_type( $post ) ) {

				$questionid = $post->post_parent;

				$post_link = add_query_arg( array('quesid' => $questionid, 'ansid' => $post->ID), $post_link );

				

				$post_link = str_replace('answers/','questions/', $post_link);

				$post_link = str_replace($post->ID.'-2/','', $post_link);

				

			 }

			 return $post_link;

		}

		

		function service_finder_no_author_comment_link_filter_function( $post_link, $id = 0, $leavename = FALSE ){

 			global $wp_rewrite, $service_finder_options;

			if (strpos('%comment-rating%', $post_link) === FALSE) {

			

				$post_link = str_replace('comment-rating/','', $post_link);

				

				return $post_link;

			

			 }

		}

		

		function service_finder_comment_link_filter_function( $post_link, $id = 0, $leavename = FALSE ){

			

			global $wp_rewrite, $service_finder_options;

			if (strpos('%comment-rating%', $post_link) === FALSE) {

			

				$authorreplacestring = (!empty($service_finder_options['author-replace-string'])) ? $service_finder_options['author-replace-string'] : 'author';

				

				$post_link = str_replace('comment-rating',$authorreplacestring, $post_link);

				

				return $post_link;

			

			 }

			 

		}

		

		function service_finder_term_permalink( $url, $term, $taxonomy ){

 

			$taxonomy_name = 'providers-category'; // your taxonomy name here

			$taxonomy_slug = 'providers-category'; // the taxonomy slug can be different with the taxonomy name (like 'post_tag' and 'tag' )

		 

			// exit the function if taxonomy slug is not in URL

			if ( strpos($url, $taxonomy_slug) === FALSE || $taxonomy != $taxonomy_name ) return $url;

		 

			$url = str_replace('/' . $taxonomy_slug, '', $url);

		 

			return $url;

		}

		

		function service_finder_no_author_base_rewrite_rules($author_rewrite) { 

			global $service_finder_options, $wpdb;

			

			$author_rewrite = array();

			$authors = $wpdb->get_results("SELECT user_nicename AS nicename from $wpdb->users");    

			foreach($authors as $author) {

				$author_rewrite["({$author->nicename})/page/?([0-9]+)/?$"] = 'index.php?author_name=$matches[1]&paged=$matches[2]';

				$author_rewrite["({$author->nicename})/?$"] = 'index.php?author_name=$matches[1]';

			}   

			

			return $author_rewrite;

		}

		

		/*Refresh Auhtor URL's to rewrite */

		function service_finder_remove_author_permalinks() {

			global $wp_rewrite, $service_finder_options;

			// Change the value of the author permalink base to whatever you want here

			$wp_rewrite->author_base = '';

		

			$wp_rewrite->flush_rules();

		}

		

		function service_finder_replace_author_rewrite_rules($author_rewrite) { 

			global $service_finder_options, $wpdb;

			

			$authorreplacestring = (!empty($service_finder_options['author-replace-string'])) ? $service_finder_options['author-replace-string'] : '';

			

			$author_rewrite = array();

			$authors = $wpdb->get_results("SELECT user_nicename AS nicename from $wpdb->users");    

			foreach($authors as $author) {

				$author_rewrite[$authorreplacestring."/({$author->nicename})/page/?([0-9]+)/?$"] = 'index.php?author_name=$matches[1]&paged=$matches[2]';

				$author_rewrite[$authorreplacestring."/({$author->nicename})/?$"] = 'index.php?author_name=$matches[1]';

			}   

			return $author_rewrite;

		}

		

		/*Refresh Auhtor URL's to rewrite */

		function service_finder_replace_author_permalinks() {

			global $wp_rewrite, $service_finder_options;

			

			$authorreplacestring = (!empty($service_finder_options['author-replace-string'])) ? $service_finder_options['author-replace-string'] : '';

			// Change the value of the author permalink base to whatever you want here

			$wp_rewrite->author_base = $authorreplacestring;

		

			$wp_rewrite->flush_rules();

		}

		

		/*Create post type for comment rating*/

		function service_finder_comment_rating_post_type() {

		

				$labels = array(

					'name'                => esc_html_x( 'Comment Ratings', 'Post Type General Name', 'cleanx' ),

					'singular_name'       => esc_html_x( 'Comment Rating', 'Post Type Singular Name', 'cleanx' ),

					'menu_name'           => esc_html__( 'Comment Ratings', 'cleanx' ),

					'parent_item_colon'   => esc_html__( 'Parent Comment Rating:', 'cleanx' ),

					'all_items'           => esc_html__( 'All Comment Ratings', 'cleanx' ),

					'view_item'           => esc_html__( 'View Comment Rating', 'cleanx' ),

					'add_new_item'        => esc_html__( 'Add New Comment Ratings', 'cleanx' ),

					'add_new'             => esc_html__( 'Add New', 'cleanx' ),

					'edit_item'           => esc_html__( 'Edit Comment Rating', 'cleanx' ),

					'update_item'         => esc_html__( 'Update Comment Rating', 'cleanx' ),

					'search_items'        => esc_html__( 'Search Comment Rating', 'cleanx' ),

					'not_found'           => esc_html__( 'Not found', 'cleanx' ),

					'not_found_in_trash'  => esc_html__( 'Not found in Trash', 'cleanx' ),

				);

		

				$custom_slug = 'comment-rating';

				//$custom_slug = null;

				$rewrite = array(

					'slug'                => $custom_slug,

					'with_front'          => false,

					'pages'               => true,

					'feeds'               => true,

				);

				$args = array(

					'label'               => esc_html__( 'service_finder_comment_rating', 'cleanx' ),

					'description'         => esc_html__( 'Comment Ratings', 'cleanx' ),

					'labels'              => $labels,

					'supports'            => array( 'title', 'editor', 'comments', 'page-attributes' ),

					'hierarchical'        => false,

					'public'              => true,

					'show_ui'             => true,

					'show_in_menu'        => true,

					'show_in_nav_menus'   => true,

					'show_in_admin_bar'   => false,

					'menu_position'       => 40,

					'can_export'          => true,

					'has_archive'         => false,

					'exclude_from_search' => false,

					'publicly_queryable'  => true,

					'rewrite'             => $rewrite,

					'capability_type'     => 'post',

				);

				

				register_post_type( 'sf_comment_rating', $args );

				

				/*Add Cities Texonomy*/

				$labels = array(

					'name' => esc_html__('Cities', 'service-finder'),

					'singular_name' => esc_html__('City', 'service-finder'),

					'search_items' => esc_html__('Search City', 'service-finder'),

					'all_items' => esc_html__('All Cities', 'service-finder'),

					'parent_item' => esc_html__('Parent City', 'service-finder'),

					'parent_item_colon' => esc_html__('Parent City', 'service-finder'),

					'edit_item' => esc_html__('Edit City', 'service-finder'),

					'update_item' => esc_html__('Update City', 'service-finder'),

					'add_new_item' => esc_html__('Add New City', 'service-finder'),

					'new_item_name' => esc_html__('New City', 'service-finder'),

					'menu_name' => esc_html__('Cities', 'service-finder')

				);

			 

				$catslug = 'city';

			 

				$args = array(

					'hierarchical' => false,

					'labels' => $labels,

					'show_ui' => true,

					'show_admin_column' => true,

					'query_var' => true,

					'rewrite' => array( 'slug' => $catslug)

				);

			 

				register_taxonomy( 'sf-cities' , array('user') , $args );

		

			}



		

		function service_finder_extra_user_profile_fields( $args ) {

			

			require plugin_dir_path(__FILE__) . '/admin/profile-fields.php';

			

		}

		

		/*Save Provider profile*/

		function service_finder_save_provider_data( $user_id ) {

		

			if(service_finder_getUserRole($user_id) == 'Provider' || service_finder_getUserRole($user_id) == 'Customer'){



			require plugin_dir_path(__FILE__) . 'admin/save-profile.php';

			

			}

			

		}

		

		/*Update User Profile*/

		public function service_finder_update_user_profile( $user_id ) {



			if(service_finder_getUserRole($user_id) == 'Provider' || service_finder_getUserRole($user_id) == 'Customer'){



				$user_terms[] = (!empty($_POST['signup_category'])) ? esc_html($_POST['signup_category']) : '';

				$terms = array_unique( array_map( 'intval', $user_terms ) );

				wp_set_object_terms( $user_id, $terms, 'user_category', false );

			 

				//make sure you clear the term cache

				clean_object_term_cache($user_id, 'user_category');

				

				require plugin_dir_path(__FILE__) . 'admin/update-profile.php';

			

			}

			

		}

		

		function service_finder_validate_field(&$errors, $update = null, &$user  = null){

		if(!empty($user_id)){

			if(service_finder_getUserRole($user_id) == 'Provider'){

				$signup_category = (!empty($_POST['signup_category'])) ? esc_html($_POST['signup_category']) : '';

				$signup_country = (!empty($_POST['signup_country'])) ? esc_html($_POST['signup_country']) : '';

				

				if ($signup_category=='' || $signup_category == NULL){

			

					$errors->add('empty_category', "ERROR: Please select a category");

			

				}

				

				if ($signup_country=='' || $signup_country == NULL){

			

					$errors->add('empty_country', "ERROR: Please select a country");

			

				}

			}

		}	

		

		}

		

		public function service_finder_load_custom_widget()

		{

			require plugin_dir_path(__FILE__) . '/lib/custom-widget.php';

			

			register_widget( 'service_finder_recent_post_with_thumbnails' );

			register_widget( 'service_finder_custom_search' );

			register_widget( 'service_finder_provider_search_form' );

			register_widget( 'service_finder_request_a_quote' );

			register_widget( 'service_finder_login_form' );

			register_widget( 'service_finder_related_providers' );

			register_widget( 'service_finder_providers_category' );

			register_widget( 'service_finder_providers_cities' );

		}

		

		function service_finder_every_minute( $schedules ) {

 

			$schedules['every_minute'] = array(

					'interval'  => 60,

					'display'   => esc_html__( 'Every Minutes', 'service-finder' )

			);

			 

			return $schedules;

		}

		

		/**

		 * Activate the plugin

		 */

		public static function service_finder_activate() {

			global $wpdb, $service_finder_Tables;

			

			//Allow any user to register

			update_option( 'users_can_register', 1 );

			

			if ( !wp_next_scheduled( 'service_finder_check_feature_expirations' ) ){

				wp_schedule_event( time(), 'daily', 'service_finder_check_feature_expirations' );

			}

			if ( !wp_next_scheduled( 'service_finder_check_provider_expirations' ) ){

				wp_schedule_event( time(), 'daily', 'service_finder_check_provider_expirations' );	

			}

			if ( !wp_next_scheduled( 'service_finder_check_joblimit_expirations' ) ){

				wp_schedule_event( time(), 'daily', 'service_finder_check_joblimit_expirations' );	

			}

			if ( !wp_next_scheduled( 'service_finder_booking_reminder_mail' ) ){

				wp_schedule_event( time(), 'daily', 'service_finder_booking_reminder_mail' );	

			}

			if ( !wp_next_scheduled( 'service_finder_update_payout_status' ) ){

				wp_schedule_event( time(), 'daily', 'service_finder_update_payout_status' );	

			}						

			if ( !wp_next_scheduled( 'service_finder_update_mp_payout_status' ) ){	

				wp_schedule_event( time(), 'daily', 'service_finder_update_mp_payout_status' );				

			}

			

			/*Create object for table name access in theme*/

			$service_finder_Tables = (object) array(

										'providers' => 'service_finder_providers',

										'providers' => 'service_finder_providers',

										'notifications' =>  'service_finder_notifications',

										'services' => 'service_finder_services',

										'team_members' => 'service_finder_team_members',

										'bookings' => 'service_finder_bookings',

										'customers' => 'service_finder_customers',

										'customers_data' => 'service_finder_customers_data',

										'booked_services' => 'service_finder_booked_services',

										'timeslots' => 'service_finder_timeslots',

										'service_area' => 'service_finder_service_area',

										'regions' => 'service_finder_regions',

										'attachments' => 'service_finder_attachments',

										'invoice' => 'service_finder_invoice',

										'feedback' => 'service_finder_feedback',

										'feature' => 'service_finder_feature',

										'favorites' => 'service_finder_favorites',

										'newsletter' => 'service_finder_newsletter',

										'unavailability' => 'service_finder_unavailability',

										'business_hours' => 'service_finder_business_hours',

										'job_limits' => 'service_finder_job_limits',

										'transaction' => 'service_finder_transaction',

										'cities' => 'service_finder_cities',

										'quotations' => 'service_finder_quotations',

										'claim_business' => 'service_finder_claim_business',

										'starttime' => 'service_finder_starttime',

										'service_groups' => 'service_finder_service_groups',

										'branches' => 'service_finder_branches',

										'custom_rating' => 'service_finder_custom_rating',

										'rating_labels' => 'service_finder_rating_labels',

										'visitors_log' => 'service_finder_visitors_log',

										'experience' => 'service_finder_experience',

										'certificates' => 'service_finder_certificates',

										'qualification' => 'service_finder_qualification',

										'wallet_transaction' => 'service_finder_wallet_transaction',

										'offers' => 'service_finder_offers',

										'quoteto_related_providers' => 'service_finder_quoteto_related_providers',

										'member_timeslots' => 'service_finder_member_timeslots',

										'member_starttimes' => 'service_finder_member_starttimes',

										'payout_history' => 'service_finder_payout_history',

										'mp_payout_history' => 'service_finder_mp_payout_history',

										'days_availability' => 'service_finder_days_availability',

										'job_invitations' => 'service_finder_job_invitations',

										'post_user_meta' => 'service_finder_post_user_meta',

							);

							

			//Create service_finder_ Theme Tables

			/*Provider Table*/

			$wpdb->query(

				"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->providers."` (

					`id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

					`wp_user_id`         BIGINT(20) UNSIGNED,

					`avatar_id`          INT UNSIGNED NOT NULL,

					`admin_moderation`   VARCHAR(128) DEFAULT '',

					`account_blocked`    VARCHAR(128) DEFAULT '',

					`status`    		 VARCHAR(128) DEFAULT '',

					`gender`    		 VARCHAR(128) DEFAULT '',

					`identity`     		 VARCHAR(128) DEFAULT '',

					`company_name`       VARCHAR(128) DEFAULT '',

					`rating`      		 DECIMAL(10,2) NOT NULL DEFAULT 0.0,

					`full_name`          VARCHAR(128) DEFAULT '',

					`tagline`            TEXT,

					`bio`            	 TEXT,

					`booking_description` TEXT,

					`embeded_code`     	 TEXT,

					`email`              VARCHAR(128) DEFAULT '',

					`phone`              VARCHAR(128) DEFAULT '',

					`mobile`             VARCHAR(128) DEFAULT '',

					`fax`             	 VARCHAR(128) DEFAULT '',

					`lat`           	 VARCHAR(128) DEFAULT '',

					`long`           	 VARCHAR(128) DEFAULT '',

					`facebook`           VARCHAR(128) DEFAULT '',

					`twitter`            VARCHAR(128) DEFAULT '',

					`linkedin`           VARCHAR(128) DEFAULT '',

					`pinterest`          VARCHAR(128) DEFAULT '',

					`google_plus`        VARCHAR(128) DEFAULT '',

					`digg`		         VARCHAR(128) DEFAULT '',

					`instagram`		     VARCHAR(128) DEFAULT '',

					`paypalid`           VARCHAR(128) DEFAULT '',

					`stripekey`          VARCHAR(128) DEFAULT '',

					`skypeid`            VARCHAR(128) DEFAULT '',

					`website`            VARCHAR(128) DEFAULT '',

					`category_id` 		 VARCHAR(255) DEFAULT '',

					`address`            TEXT,

					`apt`             	 VARCHAR(128) DEFAULT '',

					`city`             	 VARCHAR(128) DEFAULT '',

					`state`            	 VARCHAR(128) DEFAULT '',

					`zipcode`            VARCHAR(128) DEFAULT '',

					`country`            VARCHAR(128) DEFAULT '',

					`amenities` 		 VARCHAR(255) DEFAULT '',

					`languages` 		 VARCHAR(255) DEFAULT '',

					`radius`          	 INT UNSIGNED NOT NULL,

					`featured`           INT UNSIGNED NOT NULL,

					`service_perform_at` VARCHAR(128) DEFAULT ''

				) ENGINE = INNODB

				DEFAULT CHARACTER SET = utf8

				COLLATE = utf8_general_ci"

			);

			/*Services Table*/

			$wpdb->query(

				"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->services."` (

					`id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

					`wp_user_id`         BIGINT(20) UNSIGNED,

					`provider_id`        BIGINT(20) UNSIGNED,

					`service_name`       VARCHAR(255) DEFAULT '',

					`cost`               DECIMAL(10,2) NOT NULL DEFAULT 0.00,

					`cost_type`       	 VARCHAR(100) DEFAULT '',

					`hours`       	 	 FLOAT(11) NOT NULL DEFAULT 0,

					`persons`       	 INT UNSIGNED NOT NULL DEFAULT 0,

					`days`       	 	 INT UNSIGNED NOT NULL DEFAULT 0,

					`before_padding_time` INT UNSIGNED NOT NULL DEFAULT 0,

					`after_padding_time`  INT UNSIGNED NOT NULL DEFAULT 0,

					`description`        TEXT,

					`offer`		         VARCHAR(128) DEFAULT '',

					`offer_title`        VARCHAR(128) DEFAULT '',

					`coupon_code`        VARCHAR(128) DEFAULT '',

					`expiry_date`        DATE NOT NULL,

					`max_coupon`         INT UNSIGNED NOT NULL DEFAULT 0,

					`discount_type`      VARCHAR(128) DEFAULT '',

					`discount_value`     FLOAT(11) DEFAULT 0,

					`discount_description` TEXT DEFAULT '',

					`group_id`         	 INT UNSIGNED NOT NULL,

					`group_name`      	 VARCHAR(128) DEFAULT '',

					`status` 		     VARCHAR(128) DEFAULT 'active'

				) ENGINE = INNODB

				DEFAULT CHARACTER SET = utf8

				COLLATE = utf8_general_ci"

			);

			/*Service days availability Table*/

			$wpdb->query(

				"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->days_availability."` (

					`id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

					`service_id`         BIGINT(20) UNSIGNED,

					`provider_id`        BIGINT(20) UNSIGNED,

					`service_type`  	 VARCHAR(128) DEFAULT '',

					`day`       		 VARCHAR(128) DEFAULT '',

					`max_booking`        BIGINT(20) UNSIGNED

				) ENGINE = INNODB

				DEFAULT CHARACTER SET = utf8

				COLLATE = utf8_general_ci"

			);

			/*Team Members Table*/

			$wpdb->query(

				"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->team_members."` (

					`id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

					`avatar_id`          INT UNSIGNED NOT NULL,

					`member_name`        VARCHAR(255) DEFAULT '',

					`email`              VARCHAR(128) DEFAULT '',

					`phone`              VARCHAR(128) DEFAULT '',

					`service_area`       TEXT,

					`regions`            VARCHAR(128) DEFAULT '',

					`services`       	 TEXT,

					`rating`      		 DECIMAL(10,2) NOT NULL DEFAULT 0.0,

					`admin_wp_id`	     VARCHAR(128) DEFAULT '',

					`is_admin` 		     VARCHAR(128) DEFAULT ''

				) ENGINE = INNODB

				DEFAULT CHARACTER SET = utf8

				COLLATE = utf8_general_ci"

			);

			/*Booking Table*/

			$wpdb->query(

				"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->bookings."` (

					`id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

					`created`            DATETIME NOT NULL,

					`booking_made_by`	 VARCHAR(100) DEFAULT 'customer',

					`customer_id`	 	 INT UNSIGNED NOT NULL,

					`service_perform_at` VARCHAR(100) DEFAULT '',

					`service_location`   VARCHAR(100) DEFAULT '',

					`date`      	 	 DATE,

					`start_time`         TIME,

					`end_time`           TIME,

					`end_time_no_buffer` TIME,

					`multi_date`         VARCHAR(255) DEFAULT 'no',

					`jobid`			     INT UNSIGNED NOT NULL,

					`quoteid`			 INT UNSIGNED NOT NULL,

					`provider_id`        INT UNSIGNED NOT NULL,

					`member_id`        	 INT UNSIGNED NOT NULL,

					`services`	 		 TEXT NOT NULL DEFAULT '',

					`booking_customer_id` INT UNSIGNED NOT NULL,

					`type`               VARCHAR(255) DEFAULT '',

					`status`             ENUM('Pending','Completed','Cancel','Need-Approval') NOT NULL DEFAULT 'Pending',

					`stripe_customer_id` VARCHAR(255) DEFAULT '',

					`paypal_token` 		 VARCHAR(255) DEFAULT '',

					`stripe_token`       VARCHAR(255) DEFAULT '',

					`wired_invoiceid`    VARCHAR(255) DEFAULT '',

					`payment_to`       	 VARCHAR(255) DEFAULT '',

					`total`              DECIMAL(10,2) NOT NULL DEFAULT 0.00,

					`discount`           DECIMAL(10,2) NOT NULL DEFAULT 0.00,

					`coupon_code` 		 VARCHAR(128) DEFAULT '',

					`dicount_based_on` 	 VARCHAR(128) DEFAULT '',

					`adminfee`           DECIMAL(10,2) NOT NULL DEFAULT 0.00,

					`charge_admin_fee_from` VARCHAR(255) DEFAULT '',

					`paid_to_provider` 	 VARCHAR(255) DEFAULT 'pending',

					`paid_to_provider_txnid` VARCHAR(255) DEFAULT '',

					`order_id`	     	 INT UNSIGNED NOT NULL,

					`txnid`	     	 	 VARCHAR(255) DEFAULT '',

					`payumoneyid`	     VARCHAR(255) NOT NULL DEFAULT '',

					`gcal_booking_url`	 VARCHAR(255) DEFAULT '',

					`gcal_booking_id`	 VARCHAR(255) DEFAULT '',

					`paypal_paykey`	     VARCHAR(255) DEFAULT '',

					`booked_services_id` INT UNSIGNED NOT NULL,
					
					`payonlyadminfee`	 VARCHAR(128) DEFAULT '',

					`payment_type`	     VARCHAR(255) DEFAULT 'local'

				) ENGINE = INNODB

				AUTO_INCREMENT = 1000

				DEFAULT CHARACTER SET = utf8

				COLLATE = utf8_general_ci"

			);

			/*Customers Data Table*/

			$wpdb->query(

				"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->customers_data."` (

					`id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

					`wp_user_id`         BIGINT(20) UNSIGNED,

					`phone`      		 VARCHAR(255) NOT NULL DEFAULT '',

					`phone2`      		 VARCHAR(255) NOT NULL DEFAULT '',

					`address`      		 VARCHAR(255) NOT NULL DEFAULT '',

					`apt`   	   		 VARCHAR(255) NOT NULL DEFAULT '',

					`city`      		 VARCHAR(255) NOT NULL DEFAULT '',

					`state`      		 VARCHAR(255) NOT NULL DEFAULT '',

					`zipcode`      		 VARCHAR(255) NOT NULL DEFAULT '',

					`avatar_id`      	 INT UNSIGNED NOT NULL,

					`country`      		 VARCHAR(255) NOT NULL DEFAULT ''

				) ENGINE = INNODB

				DEFAULT CHARACTER SET = utf8

				COLLATE = utf8_general_ci"

			);

			/*Customer Table*/

			$wpdb->query(

				"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->customers."` (

					`id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

					`wp_user_id`         BIGINT(20) UNSIGNED,

					`name`      		 VARCHAR(255) NOT NULL DEFAULT '',

					`phone`      		 VARCHAR(255) NOT NULL DEFAULT '',

					`phone2`      		 VARCHAR(255) NOT NULL DEFAULT '',

					`email`      		 VARCHAR(255) NOT NULL DEFAULT '',

					`address`      		 VARCHAR(255) NOT NULL DEFAULT '',

					`apt`   	   		 VARCHAR(255) NOT NULL DEFAULT '',

					`city`      		 VARCHAR(255) NOT NULL DEFAULT '',

					`state`      		 VARCHAR(255) NOT NULL DEFAULT '',

					`country`      		 VARCHAR(255) NOT NULL DEFAULT '',

					`zipcode`      		 VARCHAR(255) NOT NULL DEFAULT '',

					`region`      		 VARCHAR(255) NOT NULL DEFAULT '',

					`description` 	     TEXT NOT NULL DEFAULT ''

				) ENGINE = INNODB

				DEFAULT CHARACTER SET = utf8

				COLLATE = utf8_general_ci"

			);

			/*Booked Services Table*/

			$wpdb->query(

				"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->booked_services."` (

					`id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

					`booking_id`         INT UNSIGNED NOT NULL,

					`service_id`         INT UNSIGNED NOT NULL,

					`date`      	 	 DATE NOT NULL,

					`hours`		         INT UNSIGNED NOT NULL,

					`start_time`         TIME NULL,

					`end_time`           TIME NULL,

					`without_padding_start_time` TIME NULL,

					`without_padding_end_time`   TIME NULL,

					`fullday`            VARCHAR(255) NOT NULL DEFAULT 'no',

					`couponcode`         VARCHAR(128) DEFAULT '',

					`discount`        	 DECIMAL(10,2) NOT NULL DEFAULT 0.00,

					`status`           	 VARCHAR(255) NOT NULL DEFAULT 'pending',

					`gcal_booking_url`	 VARCHAR(255) DEFAULT '',

					`gcal_booking_id`	 VARCHAR(255) DEFAULT '',

					`member_id`          INT UNSIGNED NOT NULL

				) ENGINE = INNODB

				DEFAULT CHARACTER SET = utf8

				COLLATE = utf8_general_ci"

			);

			/*Timeslots Table*/

			$wpdb->query(

					"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->timeslots."` (

						`id`                     INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

						`provider_id` 			 INT UNSIGNED NOT NULL,

						`day`      		 		 VARCHAR(255) NOT NULL DEFAULT '',

						`start_time`             TIME,

						`end_time`               TIME,

						`slotids`      		 	 VARCHAR(255) NOT NULL DEFAULT '',

						`max_bookings`        	 INT UNSIGNED NOT NULL

					 ) ENGINE = INNODB

					 DEFAULT CHARACTER SET = utf8

					 COLLATE = utf8_general_ci"

				);

			/*Timeslots Table*/

			$wpdb->query(

					"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->member_timeslots."` (

						`id`                     INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

						`provider_id` 			 INT UNSIGNED NOT NULL,

						`member_id` 			 INT UNSIGNED NOT NULL,

						`day`      		 		 VARCHAR(255) NOT NULL DEFAULT '',

						`start_time`             TIME,

						`end_time`               TIME

					 ) ENGINE = INNODB

					 DEFAULT CHARACTER SET = utf8

					 COLLATE = utf8_general_ci"

				);

			/*Timeslots Table*/

			$wpdb->query(

					"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->member_starttimes."` (

						`id`                     INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

						`provider_id` 			 INT UNSIGNED NOT NULL,

						`member_id` 			 INT UNSIGNED NOT NULL,

						`day`      		 		 VARCHAR(255) NOT NULL DEFAULT '',

						`start_time`             TIME,

						`end_time`               TIME,

						`break_start_time`       TIME,

						`break_end_time`         TIME

					 ) ENGINE = INNODB

					 DEFAULT CHARACTER SET = utf8

					 COLLATE = utf8_general_ci"

				);	

			/*Unavailability Table*/

			$wpdb->query(

					"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->unavailability."` (

						`id`                     INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

						`provider_id` 			 INT UNSIGNED NOT NULL,

						`member_id` 			 INT UNSIGNED NOT NULL,

						`date`      	 	 	 DATE NOT NULL,

						`day`      		 		 VARCHAR(255) NOT NULL DEFAULT '',

						`start_time`             TIME,

						`end_time`               TIME,

						`single_start_time`      TIME,

						`availability_method`    VARCHAR(255) NOT NULL DEFAULT 'timeslots',

						`wholeday`      		 VARCHAR(255) NOT NULL DEFAULT ''

					 ) ENGINE = INNODB

					 DEFAULT CHARACTER SET = utf8

					 COLLATE = utf8_general_ci"

				);

			/*Service Area Table*/

			$wpdb->query(

					"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->service_area."` (

						`id`                     INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

						`provider_id` 			 INT UNSIGNED NOT NULL,

						`zipcode`      		 	 VARCHAR(255) NOT NULL DEFAULT '',

						`status`             	 VARCHAR(128) DEFAULT 'active'

					 ) ENGINE = INNODB

					 DEFAULT CHARACTER SET = utf8

					 COLLATE = utf8_general_ci"

				);

			/*Region Table*/

			$wpdb->query(

					"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->regions."` (

						`id`                     INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

						`provider_id` 			 INT UNSIGNED NOT NULL,

						`region`      		 	 VARCHAR(255) NOT NULL DEFAULT '',

						`status`      		 	 VARCHAR(128) NOT NULL DEFAULT 'active'

					 ) ENGINE = INNODB

					 DEFAULT CHARACTER SET = utf8

					 COLLATE = utf8_general_ci"

				);	

			/*Attachments Table*/

			$wpdb->query(

				"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->attachments."` (

					`id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

					`wp_user_id`         BIGINT(20) UNSIGNED,

					`attachmentid`		 INT UNSIGNED NOT NULL,

					`type`      		 VARCHAR(100) NOT NULL DEFAULT ''

				) ENGINE = INNODB

				DEFAULT CHARACTER SET = utf8

				COLLATE = utf8_general_ci"

			);

			/*Invoice Table*/

			$wpdb->query(

				"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->invoice."` (

					`id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

					`reference_no`       VARCHAR(100) NOT NULL DEFAULT '',

					`duedate`      	 	 DATE NOT NULL,

					`provider_id` 		 INT UNSIGNED NOT NULL,

					`customer_email`     VARCHAR(128) DEFAULT '',

					`booking_id`         BIGINT(20) UNSIGNED,

					`discount_type`      VARCHAR(100) NOT NULL DEFAULT '',

					`tax_type`      	 VARCHAR(100) NOT NULL DEFAULT '',

					`discount`           DECIMAL(10,2) NOT NULL DEFAULT 0.00,

					`tax`           	 DECIMAL(10,2) NOT NULL DEFAULT 0.00,

					`services` 	     	 TEXT NOT NULL DEFAULT '',

					`description` 	     TEXT NOT NULL DEFAULT '',

					`total`              DECIMAL(10,2) NOT NULL DEFAULT 0.00,

					`grand_total`        DECIMAL(10,2) NOT NULL DEFAULT 0.00,

					`status`	     	 ENUM('canceled', 'overdue', 'paid', 'pending', 'on-hold') NOT NULL DEFAULT 'Pending',

					`payment_mode`   	 VARCHAR(255) DEFAULT 'local',

					`payment_type`       VARCHAR(100) DEFAULT '',

					`paid_to_provider` 	 VARCHAR(255) DEFAULT 'pending',

					`paid_to_provider_txnid` VARCHAR(255) DEFAULT '',

					`charge_admin_fee_from` VARCHAR(255) DEFAULT '',

					`stripe_customer_id` VARCHAR(255) DEFAULT '',

					`paypal_token` 		 VARCHAR(255) DEFAULT '',

					`txnid` 		 	 VARCHAR(255) DEFAULT '',

					`adminfee` 		 	 DECIMAL(10,2) NOT NULL DEFAULT 0.00,

					`stripe_token`       VARCHAR(255) DEFAULT ''			

				) ENGINE = INNODB

				DEFAULT CHARACTER SET = utf8

				COLLATE = utf8_general_ci"

			);	

			/*Feedback Table*/

			$wpdb->query(

				"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->feedback."` (

					`id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

					`provider_id` 		 INT UNSIGNED NOT NULL,

					`customer_id`     	 INT UNSIGNED NOT NULL,

					`member_id` 		 INT UNSIGNED NOT NULL,

					`booking_id`     	 INT UNSIGNED NOT NULL,

					`comment`         	 TEXT NOT NULL DEFAULT '',

					`date`         	 	 DATETIME NOT NULL,

					`rating`      		 DECIMAL(10,2) NOT NULL DEFAULT 0.0

				) ENGINE = INNODB

				DEFAULT CHARACTER SET = utf8

				COLLATE = utf8_general_ci"

			);	

			/*Custom comment rating*/

			$wpdb->query(

				"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->custom_rating."` (

					`id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

					`provider_id` 		 INT UNSIGNED NOT NULL,

					`customer_id`     	 INT UNSIGNED NOT NULL,

					`comment_id`     	 INT UNSIGNED NOT NULL,

					`feedbackid_id`      INT UNSIGNED NOT NULL,

					`rating_title`       TEXT NOT NULL DEFAULT '',

					`rating1` 		 	 DECIMAL(10,2) NOT NULL DEFAULT 0.0,

					`rating2`     	     DECIMAL(10,2) NOT NULL DEFAULT 0.0,

					`rating3`     	     DECIMAL(10,2) NOT NULL DEFAULT 0.0,

					`rating4`     	     DECIMAL(10,2) NOT NULL DEFAULT 0.0,

					`rating5`     	     DECIMAL(10,2) NOT NULL DEFAULT 0.0,

					`label1`     	  	 VARCHAR(255) DEFAULT '',

					`label2`     	  	 VARCHAR(255) DEFAULT '',

					`label3`     	  	 VARCHAR(255) DEFAULT '',

					`label4`     	  	 VARCHAR(255) DEFAULT '',

					`label5`     	  	 VARCHAR(255) DEFAULT '',

					`avgrating`     	 DECIMAL(10,2) NOT NULL DEFAULT 0.0

				) ENGINE = INNODB

				DEFAULT CHARACTER SET = utf8

				COLLATE = utf8_general_ci"

			);

			/*Rating Labels*/

			$wpdb->query(

				"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->rating_labels."` (

					`id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

					`category_id` 		 INT UNSIGNED NOT NULL,

					`label_name`     	  VARCHAR(255) DEFAULT ''

				) ENGINE = INNODB

				DEFAULT CHARACTER SET = utf8

				COLLATE = utf8_general_ci"

			);	

			/*Feature Table*/

			$wpdb->query(

				"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->feature."` (

					`id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

					`provider_id` 		 INT UNSIGNED NOT NULL,

					`days`     	 		 INT UNSIGNED NOT NULL,

					`amount`     	 	 DECIMAL(10,2) NOT NULL DEFAULT 0.00,

					`payment_mode`	     VARCHAR(255) DEFAULT 'local',

					`paymenttype`        VARCHAR(255) DEFAULT '',

					`stripe_customer_id` VARCHAR(255) DEFAULT '',

					`stripe_token`       VARCHAR(255) DEFAULT '',

					`paypal_token` 		 VARCHAR(255) DEFAULT '',

					`paypal_transaction_id`	 VARCHAR(255) DEFAULT '',

					`comments`         	 TEXT NOT NULL DEFAULT '',

					`status`         	 VARCHAR(100) DEFAULT '',

					`txnid`         	 VARCHAR(255) NOT NULL DEFAULT '',

					`payumoneyid`        VARCHAR(255) NOT NULL DEFAULT '',

					`feature_status` 	 ENUM('active','expire','pending') NOT NULL DEFAULT 'pending',

					`date`           	 DATETIME NOT NULL

				) ENGINE = INNODB

				DEFAULT CHARACTER SET = utf8

				COLLATE = utf8_general_ci"

			);	

			/*Favorite Table*/

			$wpdb->query(

				"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->favorites."` (

					`id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

					`user_id`     	 	 INT UNSIGNED NOT NULL,

					`provider_id` 		 INT UNSIGNED NOT NULL,

					`favorite` 	    	 VARCHAR(255) DEFAULT ''

				) ENGINE = INNODB

				DEFAULT CHARACTER SET = utf8

				COLLATE = utf8_general_ci"

			);	

			/*Newsletter Table*/

			$wpdb->query(

				"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->newsletter."` (

					`id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

					`email`      		 VARCHAR(255) NOT NULL DEFAULT ''

				) ENGINE = INNODB

				DEFAULT CHARACTER SET = utf8

				COLLATE = utf8_general_ci"

			);

			/*Notification Table*/

			$wpdb->query(

				"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->notifications."` (

					`id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

					`datetime`     		 datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,

					`provider_id` 		 INT UNSIGNED NOT NULL,

					`customer_id` 		 INT UNSIGNED NOT NULL,

					`admin_id`	 		 INT UNSIGNED NOT NULL,

					`target_id` 		 INT UNSIGNED NOT NULL,

					`topic` 	    	 VARCHAR(255) DEFAULT '',

					`title` 	    	 VARCHAR(255) DEFAULT '',

					`notice` 	    	 VARCHAR(255) DEFAULT '',

					`read`	 	    	 VARCHAR(255) DEFAULT 'no',

					`extra`	 	    	 VARCHAR(255) DEFAULT ''

				) ENGINE = INNODB

				DEFAULT CHARACTER SET = utf8

				COLLATE = utf8_general_ci"

			);

			/*Job Apply Limits*/

			$wpdb->query(

				"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->job_limits."` (

					`id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

					`provider_id` 		 INT UNSIGNED NOT NULL,

					`free_limits`	 	 INT UNSIGNED NOT NULL,

					`paid_limits` 		 INT UNSIGNED NOT NULL,

					`available_limits` 	 INT UNSIGNED NOT NULL,

					`membership_date`    DATETIME NOT NULL,

					`start_date`	     DATETIME NOT NULL,

					`expire_date`        DATETIME NOT NULL,

					`txn_id` 			 VARCHAR(255) DEFAULT '',

					`payment_type`   	 VARCHAR(255) DEFAULT 'local',

					`payment_method`   	 VARCHAR(255) DEFAULT '',

					`payment_status`	 VARCHAR(255) DEFAULT '',

					`current_plan`		 VARCHAR(128) DEFAULT '',

					`paypal_token` 		 VARCHAR(255) DEFAULT ''

				) ENGINE = INNODB

				DEFAULT CHARACTER SET = utf8

				COLLATE = utf8_general_ci"

			);	

			/*Increase job limit transaction*/

			$wpdb->query(

				"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->transaction."` (

					`id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

					`provider_id` 		 INT UNSIGNED NOT NULL,

					`payment_date`       DATETIME NOT NULL,

					`txn_id` 			 VARCHAR(255) DEFAULT '',

					`payment_type`   	 VARCHAR(255) DEFAULT 'local',

					`payment_method`   	 VARCHAR(255) DEFAULT '',

					`payment_status`	 VARCHAR(255) DEFAULT '',

					`plan`	 			 VARCHAR(255) DEFAULT '',

					`amount`     	 	 DECIMAL(10,2) NOT NULL DEFAULT 0.00,

					`limit`	 			 INT UNSIGNED NOT NULL,

					`current_plan`   	 VARCHAR(128) DEFAULT ''

				) ENGINE = INNODB

				DEFAULT CHARACTER SET = utf8

				COLLATE = utf8_general_ci"

			);

			/*Quotations table*/

			$wpdb->query(

				"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->quotations."` (

					`id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

					`provider_id` 		 INT UNSIGNED NOT NULL,

					`customer_id`		 INT UNSIGNED NOT NULL,

					`date`       		 DATETIME NOT NULL,

					`name` 				 VARCHAR(255) DEFAULT '',

					`email`   			 VARCHAR(255) DEFAULT '',

					`phone`	 			 VARCHAR(255) DEFAULT '',

					`message`	 		 TEXT NOT NULL DEFAULT '',

					`attachments`     	 VARCHAR(100) DEFAULT '',

					`reply`	 			 TEXT NOT NULL DEFAULT '',

					`quote_price`		 DECIMAL(10,2) NOT NULL DEFAULT 0.00,

					`hired`		 		 VARCHAR(100) DEFAULT '',

					`assignto`		 	 INT UNSIGNED NOT NULL,

					`status`     	 	 VARCHAR(100) DEFAULT ''

				) ENGINE = INNODB

				DEFAULT CHARACTER SET = utf8

				COLLATE = utf8_general_ci"

			);

			/*Quotations table*/

			$wpdb->query(

				"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->quoteto_related_providers."` (

					`id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

					`quote_id` 		 	 INT UNSIGNED NOT NULL,

					`related_provider_id` INT UNSIGNED NOT NULL,

					`related_reply`	 	 TEXT NOT NULL DEFAULT '',

					`related_quote_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00

				) ENGINE = INNODB

				DEFAULT CHARACTER SET = utf8

				COLLATE = utf8_general_ci"

			);

			/*Claim Business*/

			$wpdb->query(

				"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->claim_business."` (

					`id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

					`provider_id` 		 INT UNSIGNED NOT NULL,

					`date`       		 DATETIME NOT NULL,

					`fullname` 			 VARCHAR(255) DEFAULT '',

					`email`   			 VARCHAR(255) DEFAULT '',

					`message`	 		 TEXT NOT NULL DEFAULT '',

					`status`     	 	 VARCHAR(100) DEFAULT '',

					`txn_id` 			 VARCHAR(255) DEFAULT '',

					`payment_type`   	 VARCHAR(255) DEFAULT 'local',

					`payment_method`   	 VARCHAR(255) DEFAULT '',

					`payment_status`	 VARCHAR(255) DEFAULT ''

				) ENGINE = INNODB

				DEFAULT CHARACTER SET = utf8

				COLLATE = utf8_general_ci"

			);

			/*Start Time Table*/

			$wpdb->query(

					"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->starttime."` (

						`id`                     INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

						`provider_id` 			 INT UNSIGNED NOT NULL,

						`day`      		 		 VARCHAR(255) NOT NULL DEFAULT '',

						`start_time`             TIME,

						`max_bookings`        	 INT UNSIGNED NOT NULL

					 ) ENGINE = INNODB

					 DEFAULT CHARACTER SET = utf8

					 COLLATE = utf8_general_ci"

				);

			/*Service Groups Table*/

			$wpdb->query(

					"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->service_groups."` (

						`id`                     INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

						`provider_id` 			 INT UNSIGNED NOT NULL,

						`group_name`      		 VARCHAR(255) NOT NULL DEFAULT ''

					 ) ENGINE = INNODB

					 DEFAULT CHARACTER SET = utf8

					 COLLATE = utf8_general_ci"

				);	

			/*Branches Table*/

			$wpdb->query(

				"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->branches."` (

					`id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

					`wp_user_id`         BIGINT(20) UNSIGNED,

					`address`            TEXT,

					`apt`             	 VARCHAR(128) DEFAULT '',

					`city`             	 VARCHAR(128) DEFAULT '',

					`state`            	 VARCHAR(128) DEFAULT '',

					`zipcode`            VARCHAR(128) DEFAULT '',

					`country`            VARCHAR(128) DEFAULT '',

					`lat`           	 VARCHAR(128) DEFAULT '',

					`long`           	 VARCHAR(128) DEFAULT '',

					`zoomlevel`    		 INT UNSIGNED NOT NULL

				) ENGINE = INNODB

				DEFAULT CHARACTER SET = utf8

				COLLATE = utf8_general_ci"

			);	

			/*Service Groups Table*/

			$wpdb->query(

					"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->visitors_log."` (

						`id`                     INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

						`provider_id` 			 INT UNSIGNED NOT NULL,

						`date`      	 	 	 DATE NOT NULL,

						`day`      		 		 VARCHAR(255) NOT NULL DEFAULT '',

						`count`      		 	 VARCHAR(255) NOT NULL DEFAULT ''

					 ) ENGINE = INNODB

					 DEFAULT CHARACTER SET = utf8

					 COLLATE = utf8_general_ci"

				);

			/*experience Table*/

			$wpdb->query(

					"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->experience."` (

						`id`                     INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

						`provider_id` 			 INT UNSIGNED NOT NULL,

						`job_title` 			 VARCHAR(128) NOT NULL DEFAULT '',

						`company_name`      	 VARCHAR(128) NOT NULL DEFAULT '',

						`start_date`      		 DATE,

						`end_date`      		 DATE,

						`description`      	 	 TEXT,

						`current_job`      		 VARCHAR(128) NOT NULL DEFAULT ''

					 ) ENGINE = INNODB

					 DEFAULT CHARACTER SET = utf8

					 COLLATE = utf8_general_ci"

				);

			/*qualification Table*/

			$wpdb->query(

					"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->qualification."` (

						`id`                     INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

						`provider_id` 			 INT UNSIGNED NOT NULL,

						`degree_name` 			 VARCHAR(128) NOT NULL DEFAULT '',

						`institute_name`      	 VARCHAR(128) NOT NULL DEFAULT '',

						`from_year`      		 INT UNSIGNED NOT NULL,

						`to_year`      		 	 INT UNSIGNED NOT NULL,

						`description`      	 	 TEXT

					 ) ENGINE = INNODB

					 DEFAULT CHARACTER SET = utf8

					 COLLATE = utf8_general_ci"

				);

			/*certificates Table*/

			$wpdb->query(

					"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->certificates."` (

						`id`                     INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

						`provider_id` 			 INT UNSIGNED NOT NULL,

						`attachment_id`		 	 INT UNSIGNED NOT NULL,

						`certificate_title` 	 VARCHAR(128) NOT NULL DEFAULT '',

						`issue_date`      		 DATE,

						`description`      	 	 TEXT

					 ) ENGINE = INNODB

					 DEFAULT CHARACTER SET = utf8

					 COLLATE = utf8_general_ci"

				);

			/*Wallet Table*/

			$wpdb->query(

				"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->wallet_transaction."` (

					`id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

					`user_id` 		 	 INT UNSIGNED NOT NULL,

					`payment_date`       DATETIME NOT NULL,

					`amount`		 	 DECIMAL(10,2) NOT NULL DEFAULT 0.00,

					`action`      		 VARCHAR(128) DEFAULT '',

					`debit_for`	 		 VARCHAR(255) DEFAULT '',

					`txn_id` 			 VARCHAR(255) DEFAULT '',

					`payment_mode`   	 VARCHAR(255) DEFAULT 'local',

					`payment_method`   	 VARCHAR(255) DEFAULT '',

					`payment_status`	 VARCHAR(255) DEFAULT ''

				) ENGINE = INNODB

				DEFAULT CHARACTER SET = utf8

				COLLATE = utf8_general_ci"

			);	

			/*Offers Table*/

			$wpdb->query(

				"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->offers."` (

					`id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

					`wp_user_id`         BIGINT(20) UNSIGNED,

					`offer_title`        VARCHAR(128) DEFAULT '',

					`coupon_code`        VARCHAR(128) DEFAULT '',

					`expiry_date`        DATE NOT NULL,

					`max_coupon`         INT UNSIGNED NOT NULL DEFAULT 0,

					`discount_type`      VARCHAR(128) DEFAULT '',

					`discount_value`     FLOAT(11) DEFAULT 0,

					`discount_description` TEXT DEFAULT ''

				) ENGINE = INNODB

				DEFAULT CHARACTER SET = utf8

				COLLATE = utf8_general_ci"

			);

			/*Payout History Table*/

			$wpdb->query(

				"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->payout_history."` (

					`id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

					`created`            DATETIME NOT NULL,

					`arrival_date`		 DATETIME NOT NULL,

					`provider_id`        BIGINT(20) UNSIGNED,

					`booking_id`         BIGINT(20) UNSIGNED,

					`connected_account_id` VARCHAR(128) NOT NULL DEFAULT '',

					`amount`     		 FLOAT(11) DEFAULT 0,

					`stripe_connect_type` VARCHAR(128) DEFAULT '',

					`payout_id` 		 VARCHAR(128) DEFAULT '',

					`payout_for` 		 VARCHAR(128) DEFAULT '',

					`status`        	 VARCHAR(128) DEFAULT ''

				) ENGINE = INNODB

				DEFAULT CHARACTER SET = utf8

				COLLATE = utf8_general_ci"

			);

			/*Mangopay Payout History Table*/

			$wpdb->query(

				"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->mp_payout_history."` (

					`id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

					`created`            DATETIME NOT NULL,

					`payout_id` 		 VARCHAR(128) DEFAULT '',

					`order_id`        	 BIGINT(20) UNSIGNED,

					`mp_account_id`      BIGINT(20) UNSIGNED,

					`provider_id` 		 BIGINT(20) UNSIGNED,

					`amount`     		 FLOAT(11) DEFAULT 0,

					`booking_id` 		 BIGINT(20) UNSIGNED,

					`status`        	 VARCHAR(128) DEFAULT ''

				) ENGINE = INNODB

				DEFAULT CHARACTER SET = utf8

				COLLATE = utf8_general_ci"

			);

			/*Job invitations*/

			$wpdb->query(

				"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->job_invitations."` (

					`id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

					`created`            DATETIME NOT NULL,

					`customer_id`	     BIGINT(20) UNSIGNED,

					`provider_id`	     BIGINT(20) UNSIGNED,

					`jobid`		 		 BIGINT(20) UNSIGNED

				) ENGINE = INNODB

				DEFAULT CHARACTER SET = utf8

				COLLATE = utf8_general_ci"

			);

			/*Post and user meta*/

			$wpdb->query(

				"CREATE TABLE IF NOT EXISTS `".$service_finder_Tables->post_user_meta."` (

					`id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

					`post_id`	 		 BIGINT(20) UNSIGNED,

					`user_id`	     	 BIGINT(20) UNSIGNED,

					`meta_key`		 	 VARCHAR(128) DEFAULT '',

					`meta_value`		 VARCHAR(128) DEFAULT ''

				) ENGINE = INNODB

				DEFAULT CHARACTER SET = utf8

				COLLATE = utf8_general_ci"

			);

			

			//update_option('service_finder_current_db_version', 3.5);

			

			/*if(service_finder_is_updated_database())

			{

				update_option('service_finder_current_db_version', 1);

			}else{

				update_option('service_finder_current_db_version', 0);

			}*/

		} // END public static function activate



		/**

		 * Deactivate the plugin

		 */

		public static function service_finder_deactivate() {

			wp_clear_scheduled_hook('service_finder_check_provider_expirations');

			wp_clear_scheduled_hook('service_finder_check_feature_expirations');

			wp_clear_scheduled_hook('service_finder_check_joblimit_expirations');

			wp_clear_scheduled_hook('service_finder_booking_reminder_mail');

			wp_clear_scheduled_hook('service_finder_update_payout_status');						

			wp_clear_scheduled_hook('service_finder_update_mp_payout_status');

		} // END public static function deactivate

		

		public function service_finder_update_database() {

			global $wpdb, $service_finder_Tables;

			

			/*Create object for table name access in theme*/

			$service_finder_Tables = (object) array(

										'providers' => 'service_finder_providers',

										'providers' => 'service_finder_providers',

										'notifications' =>  'service_finder_notifications',

										'services' => 'service_finder_services',

										'team_members' => 'service_finder_team_members',

										'bookings' => 'service_finder_bookings',

										'customers' => 'service_finder_customers',

										'customers_data' => 'service_finder_customers_data',

										'booked_services' => 'service_finder_booked_services',

										'timeslots' => 'service_finder_timeslots',

										'service_area' => 'service_finder_service_area',

										'regions' => 'service_finder_regions',

										'attachments' => 'service_finder_attachments',

										'invoice' => 'service_finder_invoice',

										'feedback' => 'service_finder_feedback',

										'feature' => 'service_finder_feature',

										'favorites' => 'service_finder_favorites',

										'newsletter' => 'service_finder_newsletter',

										'unavailability' => 'service_finder_unavailability',

										'business_hours' => 'service_finder_business_hours',

										'job_limits' => 'service_finder_job_limits',

										'transaction' => 'service_finder_transaction',

										'cities' => 'service_finder_cities',

										'quotations' => 'service_finder_quotations',

										'claim_business' => 'service_finder_claim_business',

										'starttime' => 'service_finder_starttime',

										'service_groups' => 'service_finder_service_groups',

										'branches' => 'service_finder_branches',

										'custom_rating' => 'service_finder_custom_rating',

										'rating_labels' => 'service_finder_rating_labels',

										'visitors_log' => 'service_finder_visitors_log',

										'experience' => 'service_finder_experience',

										'certificates' => 'service_finder_certificates',

										'qualification' => 'service_finder_qualification',

										'wallet_transaction' => 'service_finder_wallet_transaction',

										'offers' => 'service_finder_offers',

										'quoteto_related_providers' => 'service_finder_quoteto_related_providers',

										'member_timeslots' => 'service_finder_member_timeslots',

										'member_starttimes' => 'service_finder_member_starttimes',

										'payout_history' => 'service_finder_payout_history',

										'mp_payout_history' => 'service_finder_mp_payout_history',

										'days_availability' => 'service_finder_days_availability',

										'job_invitations' => 'service_finder_job_invitations',

										'post_user_meta' => 'service_finder_post_user_meta',

										'demo' => 'service_finder_demo',

							);

			

			$current_db_version = get_option('service_finder_current_db_version', 0);

			

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');



			/*$sql = "CREATE TABLE " . $service_finder_Tables->demo . " (

			  id int(9) NOT NULL AUTO_INCREMENT,

			  title tinytext NOT NULL,

			  alias tinytext,

			  params LONGTEXT NOT NULL,

			  params2 tinytext,

			  settings text NULL,

			  type VARCHAR(191) NOT NULL DEFAULT '',

			  slug tinytext NOT NULL,

			  UNIQUE KEY id (id)

			);";

			dbDelta($sql);*/

			

			if($current_db_version != 3.5)

			{

			//Modify/add table fields
			
			$this->service_finder_add_column_if_not_exist($service_finder_Tables->payout_history, 'payout_for', $column_attr = "VARCHAR(255) NOT NULL DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->customers, 'region', $column_attr = "VARCHAR(255) NOT NULL DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->team_members, 'regions', $column_attr = "VARCHAR(128) DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->team_members, 'services', $column_attr = "TEXT DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->bookings, 'booking_made_by', $column_attr = "VARCHAR(100) DEFAULT 'customer'" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->bookings, 'service_perform_at', $column_attr = "VARCHAR(100) DEFAULT 'customer'" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->bookings, 'service_location', $column_attr = "VARCHAR(255) NOT NULL DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->bookings, 'customer_id', $column_attr = "INT UNSIGNED NOT NULL" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->bookings, 'services', $column_attr = "TEXT NOT NULL DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->bookings, 'adminfee', $column_attr = "DECIMAL(10,2) DEFAULT '0.0'" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->bookings, 'charge_admin_fee_from', $column_attr = "VARCHAR(255) NOT NULL DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->bookings, 'txnid', $column_attr = "VARCHAR(255) DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->bookings, 'end_time_no_buffer', $column_attr = "TIME" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->bookings, 'jobid', $column_attr = "INT UNSIGNED NOT NULL" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->bookings, 'quoteid', $column_attr = "INT UNSIGNED NOT NULL" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->bookings, 'wired_invoiceid', $column_attr = "VARCHAR(255) DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->bookings, 'payment_to', $column_attr = "VARCHAR(255) DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->bookings, 'paid_to_provider', $column_attr = "VARCHAR(255) DEFAULT 'pending'" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->bookings, 'paid_to_provider_txnid', $column_attr = "VARCHAR(255) DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->bookings, 'order_id', $column_attr = "INT UNSIGNED NOT NULL" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->bookings, 'payment_type', $column_attr = "VARCHAR(255) DEFAULT 'local'" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->bookings, 'discount', $column_attr = "DECIMAL(10,2) DEFAULT '0.0'" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->bookings, 'coupon_code', $column_attr = "VARCHAR(128) DEFAULT 'local'" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->bookings, 'dicount_based_on', $column_attr = "VARCHAR(128) DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->providers, 'identity', $column_attr = "VARCHAR(128) DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->providers, 'gender', $column_attr = "VARCHAR(128) DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->providers, 'instagram', $column_attr = "VARCHAR(128) DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->providers, 'featured', $column_attr = "INT UNSIGNED NOT NULL" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->providers, 'amenities', $column_attr = "VARCHAR(255) DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->providers, 'languages', $column_attr = "VARCHAR(255) DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->providers, 'status', $column_attr = "VARCHAR(128) DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->providers, 'service_perform_at', $column_attr = "VARCHAR(128) DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->providers, 'radius', $column_attr = "INT UNSIGNED NOT NULL" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->invoice, 'txnid', $column_attr = "VARCHAR(255) DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->invoice, 'adminfee', $column_attr = "DECIMAL(10,2) NOT NULL DEFAULT 0.00" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->invoice, 'paid_to_provider', $column_attr = "VARCHAR(255) DEFAULT 'pending'" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->invoice, 'paid_to_provider_txnid', $column_attr = "VARCHAR(255) DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->invoice, 'charge_admin_fee_from', $column_attr = "VARCHAR(255) NOT NULL DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->invoice, 'payment_mode', $column_attr = "VARCHAR(255) DEFAULT 'local'" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->services, 'hours', $column_attr = "FLOAT(11) DEFAULT '0'" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->services, 'persons', $column_attr = "INT UNSIGNED NOT NULL DEFAULT 0" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->services, 'days', $column_attr = "INT UNSIGNED NOT NULL DEFAULT 0" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->services, 'before_padding_time', $column_attr = "INT UNSIGNED NOT NULL DEFAULT 0" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->services, 'after_padding_time', $column_attr = "INT UNSIGNED NOT NULL DEFAULT 0" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->services, 'group_id', $column_attr = "INT UNSIGNED NOT NULL" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->services, 'group_name', $column_attr = "VARCHAR(128) DEFAULT ''" );

			

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->services, 'offer', $column_attr = "VARCHAR(128) DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->services, 'offer_title', $column_attr = "VARCHAR(128) DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->services, 'coupon_code', $column_attr = "VARCHAR(128) DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->services, 'expiry_date', $column_attr = "DATE NOT NULL" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->services, 'max_coupon', $column_attr = "INT UNSIGNED NOT NULL DEFAULT 0" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->services, 'discount_type', $column_attr = "VARCHAR(128) DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->services, 'discount_value', $column_attr = "FLOAT(11) DEFAULT 0" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->services, 'discount_description', $column_attr = "TEXT DEFAULT ''" );

			

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->feedback, 'date', $column_attr = "DATETIME NOT NULL" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->customers_data, 'avatar_id', $column_attr = "INT UNSIGNED NOT NULL" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->feature, 'txnid', $column_attr = "VARCHAR(255) NOT NULL DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->feature, 'payumoneyid', $column_attr = "VARCHAR(255) NOT NULL DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->feature, 'payment_mode', $column_attr = "VARCHAR(255) DEFAULT 'local'" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->bookings, 'payumoneyid', $column_attr = "VARCHAR(255) NOT NULL DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->job_limits, 'payment_type', $column_attr = "VARCHAR(255) DEFAULT 'local'" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->transaction, 'payment_type', $column_attr = "VARCHAR(255) DEFAULT 'local'" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->unavailability, 'single_start_time', $column_attr = "TIME" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->unavailability, 'member_id', $column_attr = "INT UNSIGNED NOT NULL" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->unavailability, 'availability_method', $column_attr = "VARCHAR(255) NOT NULL DEFAULT 'timeslots'" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->bookings, 'gcal_booking_url', $column_attr = "VARCHAR(255) DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->bookings, 'gcal_booking_id', $column_attr = "VARCHAR(255) DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->bookings, 'paypal_paykey', $column_attr = "VARCHAR(255) DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->bookings, 'multi_date', $column_attr = "VARCHAR(255) DEFAULT 'no'" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->bookings, 'booked_services_id', $column_attr = "INT UNSIGNED NOT NULL" );
			
			$this->service_finder_add_column_if_not_exist($service_finder_Tables->bookings, 'payonlyadminfee', $column_attr = "VARCHAR(128) DEFAULT ''" );


			$this->service_finder_add_column_if_not_exist($service_finder_Tables->claim_business, 'txn_id', $column_attr = "VARCHAR(255) DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->claim_business, 'payment_type', $column_attr = "VARCHAR(255) DEFAULT 'local'" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->claim_business, 'payment_method', $column_attr = "VARCHAR(255) DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->claim_business, 'payment_status', $column_attr = "VARCHAR(255) DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->notifications, 'admin_id', $column_attr = "INT UNSIGNED NOT NULL" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->notifications, 'title', $column_attr = "VARCHAR(255) DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->notifications, 'datetime', $column_attr = "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->quotations, 'attachments', $column_attr = "VARCHAR(255) DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->quotations, 'reply', $column_attr = "TEXT NOT NULL DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->quotations, 'quote_price', $column_attr = "DECIMAL(10,2) NOT NULL DEFAULT 0.00" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->quotations, 'customer_id', $column_attr = "INT UNSIGNED NOT NULL" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->quotations, 'hired', $column_attr = "VARCHAR(100) DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->quotations, 'assignto', $column_attr = "INT UNSIGNED NOT NULL" );

			

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->booked_services, 'date', $column_attr = "DATE NOT NULL" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->booked_services, 'hours', $column_attr = "INT UNSIGNED NOT NULL" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->booked_services, 'start_time', $column_attr = "TIME NULL" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->booked_services, 'end_time', $column_attr = "TIME NULL" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->booked_services, 'without_padding_start_time', $column_attr = "TIME NULL" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->booked_services, 'without_padding_end_time', $column_attr = "TIME NULL" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->booked_services, 'status', $column_attr = "VARCHAR(255) DEFAULT 'pending'" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->booked_services, 'fullday', $column_attr = "VARCHAR(255) DEFAULT 'no'" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->booked_services, 'member_id', $column_attr = "INT UNSIGNED NOT NULL" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->booked_services, 'couponcode', $column_attr = "VARCHAR(128) DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->booked_services, 'discount', $column_attr = "DECIMAL(10,2) NOT NULL DEFAULT 0.00" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->booked_services, 'gcal_booking_url', $column_attr = "VARCHAR(255) DEFAULT ''" );

			$this->service_finder_add_column_if_not_exist($service_finder_Tables->booked_services, 'gcal_booking_id', $column_attr = "VARCHAR(255) DEFAULT ''" );

			

			$this->service_finder_modify_column($service_finder_Tables->bookings, 'type', $column_attr = "VARCHAR(255) DEFAULT ''","ENUM('stripe','paypal','wired','free','twocheckout')" );

			$this->service_finder_modify_column($service_finder_Tables->bookings, 'date', $column_attr = "DATE","DATE NULL" );

			

			$this->service_finder_modify_column($service_finder_Tables->bookings, 'status', $column_attr = "ENUM('Pending','Completed','Cancel','Need-Approval') NOT NULL DEFAULT 'Pending'","enum('Pending','Completed','Cancel')" );

			$this->service_finder_modify_column($service_finder_Tables->invoice, 'status', $column_attr = "ENUM('canceled', 'overdue', 'paid', 'pending', 'on-hold') NOT NULL DEFAULT 'Pending'","enum('canceled', 'overdue', 'paid', 'pending')" );

			$this->service_finder_modify_column($service_finder_Tables->providers, 'category_id', $column_attr = "VARCHAR(255) DEFAULT ''","VARCHAR(128) DEFAULT ''" );

			$this->service_finder_modify_column($service_finder_Tables->services, 'hours', $column_attr = "FLOAT(11) DEFAULT '0'","DECIMAL(10,2)" );	

			$wpdb->query('ALTER TABLE '.$service_finder_Tables->bookings.' AUTO_INCREMENT = 1000');

			

			update_option('service_finder_current_db_version', 3.5);

			}

		}

		

		//Alter table for new fields

		public function service_finder_add_column_if_not_exist($db, $column, $column_attr = "VARCHAR( 255 ) NULL" ){

		global $wpdb;

			$exists = false;

			$results = $wpdb->get_results("show columns from ".$db);

			foreach($results as $result){

			if($result->Field == $column){

				$exists = true;

				break;

			}

			}

			if(!$exists){

				$wpdb->query("ALTER TABLE `$db` ADD `$column`  $column_attr");

			}

		}

		

		public function service_finder_check_table_if_exist($tablename = ''){

		global $wpdb;

			$result = $wpdb->query("SHOW TABLES LIKE '".$tablename."'");

			

			if(!empty($result)){

				return true;

			}else{

				return false;

			}

		}

		

		//Modify column for new fields

		function service_finder_modify_column($db, $column, $column_attr = "VARCHAR( 255 ) NULL", $column_chk = '' ){

		global $wpdb;

			$exists = false;

			$results = $wpdb->get_results("show columns from ".$db);

			foreach($results as $result){

			

			if($result->Field == $column){

				if($result->Type == $column_chk){

					$exists = true;

					break;

				}	

			}

			}

			

			if(!$exists){

				$wpdb->query("ALTER TABLE `$db` MODIFY `$column`  $column_attr");

			}

		}

		

		/**********************

		Plugin Initialize function

		**********************/

		public function service_finder_init(){

			global $wpdb, $current_user, $service_finder_ThemeParams, $current_template, $registerErrors, $service_finder_Params, $paypal, $service_finder_Tables, $service_finder_Errors, $registerMessages,$globaluserid, $service_finder_options;

			

			/*Create object for table name access in theme*/

			$service_finder_Tables = (object) array(

										'providers' => 'service_finder_providers',

										'providers' => 'service_finder_providers',

										'notifications' =>  'service_finder_notifications',

										'services' => 'service_finder_services',

										'team_members' => 'service_finder_team_members',

										'bookings' => 'service_finder_bookings',

										'customers' => 'service_finder_customers',

										'customers_data' => 'service_finder_customers_data',

										'booked_services' => 'service_finder_booked_services',

										'timeslots' => 'service_finder_timeslots',

										'service_area' => 'service_finder_service_area',

										'regions' => 'service_finder_regions',

										'attachments' => 'service_finder_attachments',

										'invoice' => 'service_finder_invoice',

										'feedback' => 'service_finder_feedback',

										'feature' => 'service_finder_feature',

										'favorites' => 'service_finder_favorites',

										'newsletter' => 'service_finder_newsletter',

										'unavailability' => 'service_finder_unavailability',

										'business_hours' => 'service_finder_business_hours',

										'job_limits' => 'service_finder_job_limits',

										'transaction' => 'service_finder_transaction',

										'cities' => 'service_finder_cities',

										'quotations' => 'service_finder_quotations',

										'claim_business' => 'service_finder_claim_business',

										'starttime' => 'service_finder_starttime',

										'service_groups' => 'service_finder_service_groups',

										'branches' => 'service_finder_branches',

										'custom_rating' => 'service_finder_custom_rating',

										'rating_labels' => 'service_finder_rating_labels',

										'visitors_log' => 'service_finder_visitors_log',

										'experience' => 'service_finder_experience',

										'certificates' => 'service_finder_certificates',

										'qualification' => 'service_finder_qualification',

										'wallet_transaction' => 'service_finder_wallet_transaction',

										'offers' => 'service_finder_offers',

										'quoteto_related_providers' => 'service_finder_quoteto_related_providers',

										'member_timeslots' => 'service_finder_member_timeslots',

										'member_starttimes' => 'service_finder_member_starttimes',

										'payout_history' => 'service_finder_payout_history',

										'mp_payout_history' => 'service_finder_mp_payout_history',

										'days_availability' => 'service_finder_days_availability',

										'job_invitations' => 'service_finder_job_invitations',

										'post_user_meta' => 'service_finder_post_user_meta',

							);

			

			if ( ! current_user_can( 'manage_options' ) ) {

			show_admin_bar(false);

			}

			

			$ratingstyle = (!empty($service_finder_options['rating-style'])) ? $service_finder_options['rating-style'] : '';

			if($ratingstyle == 'custom-rating'){

			if(class_exists('PixReviewsPlugin')){

			global $pixreviews_plugin;

			remove_action( 'comment_form_logged_in_after', array( $pixreviews_plugin, 'output_review_fields' ) );

			remove_action( 'comment_post', array( $pixreviews_plugin, 'save_comment' ) );

			//remove_action( 'comment_text', array( $pixreviews_plugin, 'display_rating' ) );

			add_action( 'comment_form_logged_in_after', 'service_finder_output_review_fields', 1 );

			add_action( 'comment_post', 'service_finder_save_comment', 1 );

			}

			}else{

			remove_action( 'comment_post', 'save_comment' );

			add_action( 'comment_post', 'service_finder_save_comment_simple_rating', 1 );

			}

			add_action( 'comment_text', 'service_finder_display_rating', 1 );

			

			// global and allways accessible template variables

			$service_finder_Params = array(

				'pluginImgUrl' => SERVICE_FINDER_BOOKING_IMAGE_URL,

				'homeUrl' =>  home_url('/'),

				'role' =>  array(

								'provider' => esc_html('Provider'),

								'customer' => esc_html('Customer'),

							),

			);

			

			$provider = get_role('Provider');

			if(!empty($provider)){

			$provider->add_cap('upload_files');

			$provider->add_cap('publish_posts');

			$provider->add_cap('edit_posts');

			$provider->add_cap('edit_published_posts');

			$provider->add_cap('edit_others_posts');

			$provider->add_cap('delete_posts');

			$provider->add_cap('delete_others_posts');

			$provider->add_cap('delete_published_posts');

		

			$provider->add_cap('publish_pages');

			$provider->add_cap('edit_pages');

			$provider->add_cap('edit_published_pages');

			$provider->add_cap('edit_others_pages');

			}

			

			require plugin_dir_path(__FILE__) . '/lib/globals.php'; //Load Global variables

			

			require plugin_dir_path(__FILE__) . '/lib/class-paypal.php'; //Main Paypal Class File

			

			require plugin_dir_path(__FILE__) . '/lib/general-functions.php'; //Load General Functions

			

			require plugin_dir_path(__FILE__) . '/lib/action-hooks.php'; //Load General Functions

			

			require plugin_dir_path(__FILE__) . '/lib/woopayment.php'; //Load General Functions

			

			require plugin_dir_path(__FILE__) . '/frontend/modules/book-now/booking-data.php'; //Make booking Ajax Call

			

			require plugin_dir_path(__FILE__) . '/lib/account-registration.php'; //Complete registration/login process

			

			require plugin_dir_path(__FILE__) . '/lib/claimed-profile.php'; //Complete registration/login process

			

			require plugin_dir_path(__FILE__) . '/frontend/modules/upgrade/upgrade-data.php'; //Account upgrade and make feature Ajax Call

			

			if(service_finder_getUserRole($current_user->ID) == 'Customer'){

			require plugin_dir_path(__FILE__) . '/frontend/modules/jobs/jobs-customer-data.php'; //Jobs Ajax Call

			}else{

			require plugin_dir_path(__FILE__) . '/frontend/modules/jobs/jobs-data.php'; //Jobs Ajax Call

			}

			

			require plugin_dir_path(__FILE__) . 'frontend/modules/wallet/wallet-data.php'; //Service Area Ajax Call

			

			require plugin_dir_path(__FILE__) . '/frontend/modules/invoice/invoice-data.php'; //Invoice Ajax Call

			

			foreach (glob(SERVICE_FINDER_BOOKING_DIR . "/shortcodes/*.php") as $filename)

			{		

				require_once($filename);

			}

			

			if(!empty($service_finder_Tables)){

			if($this->service_finder_check_table_if_exist($service_finder_Tables->business_hours)){

			$results = $wpdb->get_results('SELECT * FROM '.$service_finder_Tables->providers);

			if(!empty($results)){

				foreach($results as $row){

					$rows = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->business_hours.' WHERE provider_id = %d',$row->wp_user_id));

					if(!empty($rows)){

						$mondaystarttime = $tuesdaystarttime = $wednesdaystarttime = $thursdaystarttime = $fridaystarttime = $saturdaystarttime = $sundaystarttime = '';

						$mondayendtime = $tuesdayendtime = $wednesdayendtime = $thursdayendtime = $fridayendtime = $saturdayendtime = $sundayendtime = '';

						foreach($rows as $bhrow){

							switch($bhrow->day){

							case 'monday':

								if($bhrow->offday == 'yes'){

								$mondaystarttime = '';

								$mondayendtime = '';

								}else{

								$mondaystarttime = $bhrow->from_time;

								$mondayendtime = $bhrow->to_time;

								}

								break;

							case 'tuesday':

								if($bhrow->offday == 'yes'){

								$tuesdaystarttime = '';

								$tuesdayendtime = '';

								}else{

								$tuesdaystarttime = $bhrow->from_time;

								$tuesdayendtime = $bhrow->to_time;

								}

								break;

							case 'wednesday':

								if($bhrow->offday == 'yes'){

								$wednesdaystarttime = '';

								$wednesdayendtime = '';

								}else{

								$wednesdaystarttime = $bhrow->from_time;

								$wednesdayendtime = $bhrow->to_time;

								}

								break;

							case 'thursday':

								if($bhrow->offday == 'yes'){

								$thursdaystarttime = '';

								$thursdayendtime = '';

								}else{

								$thursdaystarttime = $bhrow->from_time;

								$thursdayendtime = $bhrow->to_time;

								}

								break;

							case 'friday':

								if($bhrow->offday == 'yes'){

								$fridaystarttime = '';

								$fridayendtime = '';

								}else{

								$fridaystarttime = $bhrow->from_time;

								$fridayendtime = $bhrow->to_time;

								}

								break;

							case 'saturday':

								if($bhrow->offday == 'yes'){

								$saturdaystarttime = '';

								$saturdayendtime = '';

								}else{

								$saturdaystarttime = $bhrow->from_time;

								$saturdayendtime = $bhrow->to_time;

								}

								break;

							case 'sunday':

								if($bhrow->offday == 'yes'){

								$sundaystarttime = '';

								$sundayendtime = '';

								}else{

								$sundaystarttime = $bhrow->from_time;

								$sundayendtime = $bhrow->to_time;

								}

								break;						

							}

						}

						$start_time = array( $mondaystarttime,$tuesdaystarttime,$wednesdaystarttime,$thursdaystarttime,$fridaystarttime,$saturdaystarttime,$sundaystarttime );

						$end_time = array( $mondayendtime,$tuesdayendtime,$wednesdayendtime,$thursdayendtime,$fridayendtime,$saturdayendtime,$sundayendtime );

						$arraymap = array_map(null,$start_time,$end_time);

							

						foreach ($arraymap as $key => $value) {

						if($value[0] != ""){

						$timeslots[$key] = $value[0].'-'.$value[1];

						}else{

						$timeslots[$key] = 'off';

						}

						}

						update_user_meta($row->wp_user_id, 'timeslots', $timeslots);

						$wpdb->query($wpdb->prepare('DELETE FROM '.$service_finder_Tables->business_hours.' WHERE provider_id = %d',$row->wp_user_id));

					}else{

					$userslots = get_user_meta($row->wp_user_id, 'timeslots', true);

					if($userslots == ""){

						$start_time = array( '08:00:00','08:00:00','08:00:00','08:00:00','08:00:00','','' );

						$end_time = array( '18:00:00','18:00:00','18:00:00','18:00:00','18:00:00','','' );

						$arraymap = array_map(null,$start_time,$end_time);

						

						foreach ($arraymap as $key => $value) {

						if($value[0] != ""){

						$timeslots[$key] = $value[0].'-'.$value[1];

						}else{

						$timeslots[$key] = 'off';

						}

						}

						update_user_meta($row->wp_user_id, 'timeslots', $timeslots);

					}

					}



				}

			}

			}

			}

			

		}

		

		/*************************************************************

		*   Add Theme Roles 

		**************************************************************/

		public function service_finder_sedateCreateRoles(){

		add_role( 'Provider', 'Provider' );

		add_role( 'Customer', 'Customer' );

		

		//Add Image size for gallery thumb for recent post thumbnail

		add_image_size( 'service_finder-small-thumb', 73, 73, true );

		

		//Add Image size for gallery thumb for version 1

		add_image_size( 'service_finder-gallery-thumb-v1', 200, 150, true );

		

		//Add Image size for gallery big image for version 1

		add_image_size( 'service_finder-gallery-big-v1', 1000, 700, true );

		

		//Add Image size for gallery thumb for version 2

		add_image_size( 'service_finder-gallery-v2', 380, 270, true );

		

		//Add Image size for staff member

		add_image_size( 'service_finder-staff-member', 130, 130, true );

		

		//Size for Banner Image at Sub Header

		add_image_size( 'service_finder-provider-thumb', 600, 450, true );

		

		//Size for Banner Image at Sub Header

		add_image_size( 'service_finder-provider-medium', 600, 600, true );

		

		//Size for Banner Image at Sub Header

		add_image_size( 'service_finder-header-banner', 2000, 400, false ); 

		

		//Size for Banner Image at Sub Header

		add_image_size( 'service_finder-featured-provider', 600, 450, true ); 

		

		//Size for Banner Image at Sub Header

		add_image_size( 'service_finder-related-provider', 150, 150, true ); 

		}

		

		/**********************

		Provider Page Template

		**********************/



		public function service_finder_provider_page_template($template) {

		global $author, $service_finder_options;

				if ( is_author() ) {

				

					if(service_finder_getUserRole($author) == 'Provider' && class_exists('service_finder_texonomy_plugin')){

				

						if(service_finder_themestyle() == 'style-1' || service_finder_themestyle() == 'style-2')

						{

							require plugin_dir_path(__FILE__) . '/templates/provider-profile.php';

						}elseif(service_finder_themestyle() == 'style-4')

						{

							if(service_finder_get_data($service_finder_options,'booking-page-style') == 'style-1')
							{
								require plugin_dir_path(__FILE__) . '/templates/profile-layout-5.php';
							}else
							{
								require plugin_dir_path(__FILE__) . '/templates/profile-layout-6.php';
							}
						}else

						{

							if(service_finder_get_data($service_finder_options,'booking-page-style') == 'style-1')

							{

								require plugin_dir_path(__FILE__) . '/templates/profile-layout-3.php';

							}else

							{

								require plugin_dir_path(__FILE__) . '/templates/profile-layout-4.php';

							}	

						}

						

					}else{

					

						return $template;

					

					}

				

				}elseif ( is_tax( 'providers-category' ) ) {

					if(service_finder_themestyle() == 'style-4')
					{
						require plugin_dir_path(__FILE__) . '/templates/taxonomy-providers-category-2.php';
					}else{
						require plugin_dir_path(__FILE__) . '/templates/taxonomy-providers-category.php';
					}

					

				

				}elseif ( is_tax( 'sf-cities' ) ) {

				

					require plugin_dir_path(__FILE__) . '/templates/taxonomy-cities.php';

				

				}elseif ( is_search() && class_exists('service_finder_booking_plugin')) {

				

				$getsrhstring = isset($_GET['s']) ? $_GET['s'] : '';

				

					if($getsrhstring == '' && (isset($_GET['searchAddress']) || isset($_GET['catid']) || isset($_GET['country']) || isset($_GET['keyword']) || isset($_GET['state']) || isset($_GET['zipcode']) || isset($_GET['city']))){

					
					if(service_finder_themestyle() == 'style-4'){
					return plugin_dir_path(__FILE__) . '/templates/provider-search-2.php';
					}else{
					return plugin_dir_path(__FILE__) . '/templates/provider-search.php';
					}
					
					}else{

					

						return $template;

					

					}

				

				}else{

				

					return $template;

				

				}

		}

		

		/**********************

		Expire Job Limit Functionality from Providers

		**********************/

		public function service_finder_CheckJobLimitExpirations() {

			global $service_finder_options, $wpdb, $service_finder_Tables;

			

				$joblimits = $wpdb->get_results("SELECT * FROM ".$service_finder_Tables->job_limits);

				foreach ($joblimits as $joblimit) {

					

					$expiretimeInSec = strtotime($joblimit->expire_date);

					$differenceInSec = time() - $expiretimeInSec;

					

					if($differenceInSec >= 0){

						$package = get_user_meta($joblimit->provider_id,'provider_role',true);

						$packageNum = intval(substr($package, 8));

						

						$allowedjobapply = (!empty($service_finder_options['package'.$packageNum.'-job-apply'])) ? $service_finder_options['package'.$packageNum.'-job-apply'] : '';

						

						$period = (!empty($service_finder_options['job-apply-limit-period'])) ? $service_finder_options['job-apply-limit-period'] : '';

						$numberofweekmonth = (!empty($service_finder_options['job-post-number-of-week-month'])) ? $service_finder_options['job-post-number-of-week-month'] : 1;

						

						$appendlimit = (!empty($service_finder_options['append-limit'])) ? $service_finder_options['append-limit'] : '';

						

						if($period == 'weekly'){

							$freq = 7 * floatval($numberofweekmonth);

							$expiredate = date('Y-m-d h:i:s', strtotime("+".$freq." days"));

						}elseif($period == 'monthly'){

							$freq = 30 * floatval($numberofweekmonth);

							$expiredate = date('Y-m-d h:i:s', strtotime("+".$freq." days"));

						}

						

						$userCap = array();

						

						$userCap = service_finder_get_capability($joblimit->provider_id);

						

						$startdate = date('Y-m-d h:i:s');

						

						if(!empty($userCap)){

						if(in_array('apply-for-job',$userCap)){

							

							if($appendlimit){

									$data = array(

									'free_limits' => $joblimit->free_limits + $allowedjobapply,

									'available_limits' => $joblimit->available_limits + $allowedjobapply,

									'paid_limits' => $joblimit->paid_limits,

									'start_date' => $startdate,

									'expire_date' => $expiredate,

									);

							}else{

									$data = array(

									'free_limits' => $allowedjobapply,

									'available_limits' => $allowedjobapply,

									'paid_limits' => 0,

									'start_date' => $startdate,

									'expire_date' => $expiredate,

									'current_plan' => '',

									'txn_id' => '',

									'payment_method' => '',

									'payment_status' => '',

									);

							}

						

						}else{

								$data = array(

								'free_limits' => $allowedjobapply,

								'available_limits' => $allowedjobapply,

								'paid_limits' => 0,

								'start_date' => $startdate,

								'expire_date' => $expiredate,

								'current_plan' => '',

								'txn_id' => '',

								'payment_method' => '',

								'payment_status' => '',

								);

						}

						}else{

								$data = array(

								'free_limits' => $allowedjobapply,

								'available_limits' => $allowedjobapply,

								'paid_limits' => 0,

								'start_date' => $startdate,

								'expire_date' => $expiredate,

								'current_plan' => '',

								'txn_id' => '',

								'payment_method' => '',

								'payment_status' => '',

								);

						}

						

						$where = array(

								'provider_id' => $joblimit->provider_id

						);

						$wpdb->update($service_finder_Tables->job_limits,wp_unslash($data),$where);

				}

				}

		}

		

		/**********************

		Update payout status

		**********************/

		public function service_finder_update_payout_status() {

			global $service_finder_options, $wpdb, $service_finder_Tables;

			

				$results = $wpdb->get_results("SELECT * FROM ".$service_finder_Tables->payout_history." WHERE status != 'paid'");

				if(!empty($results)){

					foreach ($results as $row) {

				

					$payoutinfo = service_finder_retrieve_payout($row->payout_id);

					

					if($payoutinfo->status != "" && $payoutinfo->status != $row->status){

					$data = array(

							'status' => $payoutinfo->status,

							);

					

					$where = array(

							'booking_id' => $row->booking_id,

							);

					

					$wpdb->update($service_finder_Tables->payout_history,wp_unslash($data),$where);

					

					$data = array(

							'paid_to_provider' => $payoutinfo->status,

							);

					

					$where = array(

							'id' => $row->booking_id,

							);

					

					$wpdb->update($service_finder_Tables->bookings,wp_unslash($data),$where);

					

					if(function_exists('service_finder_add_notices')) {

						$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `id` = %d',$bookingid));	

						$noticedata = array(

								'provider_id' => $row->provider_id,

								'target_id' => $row->id, 

								'topic' => 'Payout status',

								'title' => esc_html__('Payout status', 'service-finder'),

								'notice' => $payoutinfo->status

								);

						service_finder_add_notices($noticedata);

					

					}

					}

					

				}				

				}

		}

		/**********************		Update mp payout status		**********************/

		public function service_finder_update_mp_payout_status	() {

			global $service_finder_options, $wpdb, $service_finder_Tables;							

			$results = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix.'postmeta'." WHERE `meta_key` = 'mp_payout_due' AND `meta_value` = 'yes'");				if(!empty($results)){

			foreach ($results as $row) {					

			$order_id = $row->post_id;											

			$bookingid = get_post_meta($order_id,'mp_booking_id',true);					

			service_finder_get_mp_payout_status($bookingid,$order_id);				

		}								

		}		

		}		

		/**********************

		Send booking reminder mail

		**********************/

		public function service_finder_booking_reminder_mail() {

			global $service_finder_options, $wpdb, $service_finder_Tables;

			

				$bookings = $wpdb->get_results("SELECT * FROM ".$service_finder_Tables->bookings." WHERE status = 'Pending'");

				foreach ($bookings as $booking) {

					

					$bookingdateInSec = strtotime($booking->date);

					$differenceInSec = $bookingdateInSec - time();

					

					$differenceInDays = floor($differenceInSec / 60 / 60 / 24);

					

					if(!empty($service_finder_options['booking-reminder-mail-notification-days'])){

						foreach($service_finder_options['booking-reminder-mail-notification-days'] as $days){

							if($differenceInDays - $days == 0 ){

								$bookingdata = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$service_finder_Tables->bookings.' WHERE `id` = %d',$booking->id),ARRAY_A);

		

								service_finder_SendBookingReminderMailToProvider($bookingdata);

								service_finder_SendBookingReminderMailToCustomer($bookingdata);

								service_finder_SendBookingReminderMailToAdmin($bookingdata);

							}			

						}

					}

					

				}

		}

		

		/**********************

		Expire Featured Functionality from Providers

		**********************/

		public function service_finder_CheckFeatureExpirations() {

			global $service_finder_options, $wpdb, $service_finder_Tables;

			

				$featured = $wpdb->get_results("SELECT * FROM ".$service_finder_Tables->feature." WHERE feature_status = 'active'");

				foreach ($featured as $time) {

					

					$activationtimeInSec = strtotime($time->date);

					$differenceInSec = time() - $activationtimeInSec;

					$differenceInDays = floor($differenceInSec / 60 / 60 / 24);

					

					$limit = floatval($time->days);

		

					

					if($differenceInDays >= $limit){

						$data = array(

								'feature_status' => 'expire',

								);

						$where = array(

								'id' => $time->id

						);

						$wpdb->update($service_finder_Tables->feature,wp_unslash($data),$where);

						$data = array(

								'featured' => 0,

								);

						

						$where = array(

								'wp_user_id' => $time->provider_id,

								);

						$wpdb->update($service_finder_Tables->providers,wp_unslash($data),$where);

						$this->service_finder_SendFeatureExpiryNotificationMail($time->provider_id);

					}

				}

		}

		

		/*Send Feature expiry notification mail*/

		function service_finder_SendFeatureExpiryNotificationMail($userid){

		global $wpdb, $service_finder_options;

		

		if($service_finder_options['featured-expire-message'] != ""){

			$message = $service_finder_options['featured-expire-message'];

		}else{

			$message = '<h3>Feature Membership Expire Notification</h3>

						<br>

						Your feature membership has been expired. Please upgrade it now.<br/>';

		}

		

		

		

					

					$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM '.$wpdb->users.' WHERE `ID` = %d',$userid));

					

					

					$msg_body = $message;

					

					if($service_finder_options['featured-expire-subject'] != ""){

						$msg_subject = $service_finder_options['featured-expire-subject'];

					}else{

						$msg_subject = 'Feature Expiry Notification';

					}

					

					if(service_finder_wpmailer($row->user_email,$msg_subject,$msg_body)) {

		

						$success = array(

								'status' => 'success',

								'suc_message' => esc_html__('Message has been sent', 'service-finder'),

								);

						$service_finder_Success = json_encode($success);

						return $service_finder_Success;

						

						

					} else {

							

						$error = array(

								'status' => 'error',

								'err_message' => esc_html__('Message could not be sent.', 'service-finder'),

								);

						$service_finder_Errors = json_encode($error);

						return $service_finder_Errors;

					}

				

			

		}

		

	

		/*Remove Provider Role from Admin Panel New User DropDwon*/

		public function service_finder_exclude_provider_role($roles) {

			$userid = (isset($_GET['user_id'])) ? $_GET['user_id'] : 0;

			

			$roles['Provider']['name'] = 'Service Finder Provider';

			$roles['Customer']['name'] = 'Service Finder Customer';

			

			if( strstr($_SERVER['REQUEST_URI'], 'wp-admin/user-edit.php') && $userid > 0) {

			

				$user = new WP_User( $userid );

				$user_role = (!empty($user->roles[0])) ? $user->roles[0] : '';

			

				if(in_array('Provider',$user->roles)){

					if (isset($roles['Customer'])) {

					  unset($roles['Customer']);

					}

					if (isset($roles['subscriber'])) {

					  unset($roles['subscriber']);

					}

					if (isset($roles['employer'])) {

					  unset($roles['employer']);

					}

					if (isset($roles['contributor'])) {

					  unset($roles['contributor']);

					}

					if (isset($roles['author'])) {

					  unset($roles['author']);

					}

					if (isset($roles['editor'])) {

					  unset($roles['editor']);

					}

					if (isset($roles['administrator'])) {

					  unset($roles['administrator']);

					}

				}

				

				if(in_array('Customer',$user->roles)){

					if (isset($roles['Provider'])) {

					  unset($roles['Provider']);

					}

					if (isset($roles['subscriber'])) {

					  unset($roles['subscriber']);

					}

					if (isset($roles['employer'])) {

					  unset($roles['employer']);

					}

					if (isset($roles['contributor'])) {

					  unset($roles['contributor']);

					}

					if (isset($roles['author'])) {

					  unset($roles['author']);

					}

					if (isset($roles['editor'])) {

					  unset($roles['editor']);

					}

					if (isset($roles['administrator'])) {

					  unset($roles['administrator']);

					}

				}

				

				if(!in_array('Customer',$user->roles) && !in_array('Provider',$user->roles)){

					if (isset($roles['Provider'])) {

					  unset($roles['Provider']);

					}

					if (isset($roles['Customer'])) {

					  unset($roles['Customer']);

					}

				}



			}

			

			return $roles;

		}

	

	

		/**********************

		FRONT-END SCRIPTS/STYLES

		**********************/



		public function service_finder_frontend_scripts() {

			global $service_finder_options, $author, $current_user;

			

			$local = get_locale();

			$langcode = explode('_',$local);

			

			$adminavailabilitybasedon = (!empty($service_finder_options['availability-based-on'])) ? esc_html($service_finder_options['availability-based-on']) : '';

			

			$manageproviderid = (isset($_GET['manageproviderid'])) ? esc_attr($_GET['manageproviderid']) : '';

			if(service_finder_getUserRole($current_user->ID) == 'Provider'){

				$globalproviderid = $current_user->ID;

			}else{

				$globalproviderid = $manageproviderid;

			}

			

			$availability_based_on = (!empty($settings['availability_based_on'])) ? $settings['availability_based_on'] : '';

			

			$apikey = (!empty($service_finder_options['google-map-api-key'])) ? $service_finder_options['google-map-api-key'] : '';

			

			$paid_booking = (!empty($service_finder_options['paid-booking'])) ? $service_finder_options['paid-booking'] : '';

			

			wp_register_script('cropper-custom', plugins_url('/sf-booking/') . '/js/cropper.min.js', array('jquery') , null, true);

			wp_register_script('jquery-cropper', plugins_url('/sf-booking/') . '/js/jquery-cropper.js', array('jquery') , null, true);

	

			wp_register_style('normalize', plugins_url('/sf-booking/') . '/css/normalize.min.css','',null);

			wp_register_style('cropper', plugins_url('/sf-booking/') . '/css/cropper.min.css','',null);

			wp_register_style('cropper-custom', plugins_url('/sf-booking/') . '/css/cropper-custom.css','',null);

			

			wp_register_script('service-finder-crop', plugins_url('/sf-booking/') . '/assets/scripts/crop.js', array('jquery') , null, true);

			

			wp_enqueue_script('recaptcha-api', 'https://www.google.com/recaptcha/api.js?render=explicit', array('jquery') , null, true);

			
			if(!in_array(get_the_ID(),service_finder_get_id_by_shortcode('[woocommerce_checkout')) ){
			wp_enqueue_script('bootstrap-select', plugins_url('/sf-booking/') . '/js/bootstrap-select.min.js', array('jquery') , null, true);
			}

			
			if(!(is_home() || is_front_page())){
			wp_enqueue_script('bootstrap-touchspin', plugins_url('/sf-booking/') . '/js/jquery.bootstrap-touchspin.js', array('jquery') , null, true);
			}

			

			wp_enqueue_script('waypoints', plugins_url('/sf-booking/') . '/js/waypoints-min.js', array('jquery') , null, true);

			

			wp_enqueue_script('counterup', plugins_url('/sf-booking/') . '/js/counterup.min.js', array('jquery') , null, true);

			

			//For Google Map API
			if(service_finder_show_map_on_site() || service_finder_show_autosuggestion_on_site()){
			wp_enqueue_script('google-map', '//maps.googleapis.com/maps/api/js?key='.esc_html($apikey).'&types=(cities)&libraries=places&language='.$langcode[0], array('jquery') , null, true);
			}

			

			wp_enqueue_script('bootstrap', plugins_url('/sf-booking/') . '/js/bootstrap.min.js', array('jquery') , null, true);

			
			if(!(is_home() || is_front_page())){
			wp_enqueue_script('bootstrap-toggle', plugins_url('/sf-booking/') . '/js/bootstrap-toggle.min.js', array('jquery') , null, true);
			}



			

			/*Star Rating*/

			wp_enqueue_script('star-rating', plugins_url('/sf-booking/') . '/assets/ratings/star-rating.js', array('jquery') , null, true);

			

			/*Encoding to regular text in javascript*/

			wp_enqueue_script('service_finder-js-encoder', plugins_url('/sf-booking/') . '/js/encoder.js', array('jquery') , null, true);

		

			/*Jquery Validation*/

			if(!is_user_logged_in()){

			wp_enqueue_script('service_finder-js-form-validation', plugins_url('/sf-booking/') . '/assets/scripts/form-validation.js', array('jquery') , null, true);

			}

			

			/*Claim Business*/

			if( in_array(get_the_ID(),service_finder_get_id_by_shortcode('[service_finder_claimbusiness_payment]') ) ){

			wp_enqueue_script('service_finder-js-claimbusiness-payment', plugins_url('/sf-booking/') . '/assets/scripts/claimbusiness.js', array('jquery') , null, true);

			}

			

			/*Stripe Payment Gatway*/

			$payment_methods = (!empty($service_finder_options['payment-methods'])) ? $service_finder_options['payment-methods'] : '';

			$woopayment = (isset($service_finder_options['woocommerce-payment'])) ? esc_html($service_finder_options['woocommerce-payment']) : false;

			

			if(!$woopayment){

			if(!empty($payment_methods)){

			if($payment_methods['stripe']){

			wp_enqueue_script('stripe', 'https://js.stripe.com/v2/', array() , null, true);

			}

			}

			}

			

			/*2Checkout Payment Gatway*/

			//wp_enqueue_script('2checkout', 'https://www.2checkout.com/checkout/api/2co.min.js', array() , null, true);

		

			//wp_enqueue_script('jquery-ui', plugins_url('/sf-booking/') . '/js/jquery-ui.js', array() , null, true);

		

			/*Custom Effects Like Tabs and others*/

			wp_enqueue_script('service_finder-js-custom-effects', plugins_url('/sf-booking/') . '/assets/scripts/custom-effects.js', array('jquery') , null, true);

			

			wp_register_script('service-finder-job-applications', plugins_url('/sf-booking/') . '/assets/scripts/job-applications.js', array('jquery') , null, true);

			

			wp_register_script('service-finder-quote-applications', plugins_url('/sf-booking/') . '/assets/scripts/quote-applications.js', array('jquery') , null, true);

			
			if(!(is_home() || is_front_page())){
			wp_enqueue_script('service_finder-js-qanda', plugins_url('/sf-booking/') . '/js/qanda.js', array('jquery') , null, true);
			}

		

			/*For Popup*/

			wp_enqueue_script('popupoverlay', plugins_url('/sf-booking/') . '/js/jquery.popupoverlay.js', array('jquery') , null, true);

			

			/*Required js files for Frontend Modules*/

			wp_enqueue_script('service_finder-js-quote-form', plugins_url('/sf-booking/') . '/frontend/modules/get-quote/resources/quote-form.js', array('jquery') , null, true);

		

			/*For Bootstrap Validator*/

			wp_enqueue_script('bootstrapValidator', plugins_url('/sf-booking/') . '/assets/validator/bootstrapValidator.min.js', array('jquery') , null, true);

		

			/*Boot Box For Edit and alert Popup*/

			wp_enqueue_script('bootbox', plugins_url('/sf-booking/') . '/assets/bootbox/bootbox.min.js', array('jquery') , null, true);

			
			if(!(is_home() || is_front_page())){
			wp_enqueue_script('bootstrap-datepicker', plugins_url('/sf-booking/') . '/frontend/modules/invoice/resources/bootstrap-datepicker.min.js', array('jquery') , null, true);
			}

			if(LANGUAGE_CODE != 'en')
			{
			if(!(is_home() || is_front_page())){
			wp_enqueue_script('bootstrap-datepicker-lang', '//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/locales/bootstrap-datepicker.'.LANGUAGE_CODE.'.min.js', array('jquery') , null, true);
			}

			}

			

			/*For Newsletter Widget*/

			wp_enqueue_script('service_finder-js-newsletter', plugins_url('/sf-booking/') . '/frontend/modules/newsletter/resources/newsletter.js', array('jquery') , null, true);

			/*For Search on Google Map*/

			if(class_exists('service_finder_booking_plugin') && service_finder_show_map_on_site() && ( (is_author() && service_finder_is_blocked($author) != 'yes' && service_finder_getUserRole($author) == 'Provider') || (is_search() && (isset($_GET['keyword']) || isset($_GET['searchAddress']) || isset($_GET['catid']) || isset($_GET['country']) || isset($_GET['state']) || isset($_GET['zipcode']) || isset($_GET['city'])) && (service_finder_get_data($service_finder_options,'search-template') == 'style-2' || (service_finder_get_data($service_finder_options,'search-template') == 'style-1' && service_finder_get_data($service_finder_options,'search-header-style') == 'map') || !class_exists( 'ReduxFrameworkPlugin' ))) || (is_home() && service_finder_get_data($service_finder_options,'header-style') == 'map') || (is_front_page() && service_finder_get_data($service_finder_options,'header-style') == 'map') )){

			wp_enqueue_script('service_finder-js-gmapfunctions', plugins_url('/sf-booking/') . '/assets/gmap/gmapfunctions.js', array('jquery','google-map') , null, true);

			

			wp_enqueue_script('marker-clusterer', plugins_url('/sf-booking/') . '/assets/gmap/marker-clusterer.js', array('jquery','google-map') , null, true);

			

			wp_enqueue_script('markerinfo', plugins_url('/sf-booking/') . '/assets/gmap/markerinfo.js', array('jquery','google-map') , null, true);

			

			wp_enqueue_script('modernizr', plugins_url('/sf-booking/') . '/assets/gmap/modernizr.js', array('jquery','google-map') , null, true);

			

			wp_enqueue_script('marker-spider', plugins_url('/sf-booking/') . '/assets/gmap/marker-spider.js', array('jquery','google-map') , null, true);

			

			wp_enqueue_script('richmarker-compiled', plugins_url('/sf-booking/') . '/assets/gmap/richmarker-compiled.js', array('jquery','google-map') , null, true);

			}

			

			/*For Home page banner style autofill script for search*/

			wp_enqueue_script('service_finder-js-search-autofill',  plugins_url('/sf-booking/') . '/assets/scripts/autofill.js', array('jquery') , null, true);

			

			/*For Login/Signup Pages*/

			if(!is_user_logged_in() && !is_search() && (in_array(get_the_ID(),service_finder_get_id_by_shortcode('[service_finder_login')) || in_array(get_the_ID(),service_finder_get_id_by_shortcode('[service_finder_signup')))){

			

			wp_enqueue_script('service_finder-js-registration',  plugins_url('/sf-booking/') . '/assets/scripts/registration.js', array('jquery') , null, true);

			

			}

			if(!(is_home() || is_front_page())){
			/*For SF Uploader*/

			wp_enqueue_script('image-manager', plugins_url('/sf-booking/') . '/assets/manage-uploads/image-manager.js', array('jquery') , null, true);

		

			wp_enqueue_script('managefiles', plugins_url('/sf-booking/') . '/assets/manage-uploads/managefiles.js', array('jquery') , null, true);

		

			wp_enqueue_script('plupload', SERVICE_FINDER_ASSESTS_ADMINURL . '/load-scripts.php?c=1&load=plupload&ver=4.3.1', array('jquery') , '4.5.1', true);
			}

			

			/*For My Account Section*/

			if( !is_search() && in_array(get_the_ID(),service_finder_get_id_by_shortcode('[service_finder_my_account') ) ){

			

			/*Datatabe for displaying data in table Format*/

			wp_enqueue_script('dataTables', plugins_url('/sf-booking/') . '/assets/datatable/jquery.dataTables.js', array('jquery') , null, true);

		

			wp_enqueue_script('dataTables-tableTools', plugins_url('/sf-booking/') . '/assets/datatable/dataTables.tableTools.js', array('jquery') , null, true);

		

			wp_enqueue_script('dataTables-bootstrap', plugins_url('/sf-booking/') . '/assets/datatable/dataTables.bootstrap.js', array('jquery') , null, true);

			

			if(service_finder_availability_method($globalproviderid) == 'timeslots'){

			wp_register_script('service_finder-js-availability-form', plugins_url('/sf-booking/') . '/frontend/modules/availability/resources/availability-form.js', array('jquery') , null, true);

			}elseif(service_finder_availability_method($globalproviderid) == 'starttime'){

			wp_register_script('service_finder-js-availability-form', plugins_url('/sf-booking/') . '/frontend/modules/availability/resources/availability-starttime.js', array('jquery') , null, true);

			}else{

			wp_register_script('service_finder-js-availability-form', plugins_url('/sf-booking/') . '/frontend/modules/availability/resources/availability-form.js', array('jquery') , null, true);

			}

			

			/*For Bootstrap Calendar*/

			wp_enqueue_script('underscore', plugins_url('/sf-booking/') . '/assets/bootstrap-calendar/underscore-min.js', array('jquery') , null, true);

		

			wp_enqueue_script('bootstrap-calendar', plugins_url('/sf-booking/') . '/assets/bootstrap-calendar/calendar.js', array('jquery') , null, true);

			

			wp_enqueue_script('bootstrap-calendar-lang', plugins_url('/sf-booking/') . '/assets/bootstrap-calendar/language/'.str_replace('_','-',get_locale()).'.js', array('jquery') , null, true);

			

			/*Google calendar js*/

			wp_enqueue_script('fullcalendar', plugins_url('/sf-booking/') . '/assets/fullcalendar/fullcalendar.min.js', array('jquery') , null, true);

		

			wp_enqueue_script('gcal', plugins_url('/sf-booking/') . '/assets/fullcalendar/gcal.js', array('jquery') , null, true);

			

			wp_enqueue_script('bootstrap-datepicker', plugins_url('/sf-booking/') . '/frontend/modules/invoice/resources/bootstrap-datepicker.min.js', array('jquery') , null, true);

			

			/*For Embeded Code*/

			wp_enqueue_script('embeded', plugins_url('/sf-booking/') . '/assets/embeded/embeded.js', array('jquery') , '4.5.7', true);

			

			/*For Add to Favorite*/

			wp_enqueue_script('service_finder-js-my-favorites', plugins_url('/sf-booking/') . '/frontend/modules/favorites/resources/my-favorites.js', array('jquery') , null, true);

			

			wp_enqueue_script('service_finder-js-form-submit', plugins_url('/sf-booking/') . '/frontend/modules/myaccount/resources/form-submit.js', array('jquery') , null, true);

			wp_enqueue_script('service_finder-js-bookings-form', plugins_url('/sf-booking/') . '/frontend/modules/bookings/resources/bookings-form.js', array('jquery') , null, true);

			wp_enqueue_script('service_finder-js-booking-settings', plugins_url('/sf-booking/') . '/frontend/modules/bookings/resources/booking-settings.js', array('jquery') , null, true);

			

			wp_enqueue_script('service_finder-js-schedule-form', plugins_url('/sf-booking/') . '/frontend/modules/schedule/resources/schedule-form.js', array('jquery') , null, true);

			

			$manageaccountby = (isset($_GET['manageaccountby'])) ? esc_attr($_GET['manageaccountby']) : '';

			$manageproviderid = (isset($_GET['manageproviderid'])) ? esc_attr($_GET['manageproviderid']) : '';

			if(service_finder_getUserRole($current_user->ID) == 'Provider' || service_finder_check_account_authorization($manageaccountby,$manageproviderid)){

			wp_enqueue_script('service_finder-js-unavailability-form', plugins_url('/sf-booking/') . '/frontend/modules/availability/resources/unavailability-form.js', array('jquery') , null, true);

			

			wp_enqueue_script('service_finder-js-servicearea-form', plugins_url('/sf-booking/') . '/frontend/modules/service-area/resources/servicearea-form.js', array('jquery') , null, true);

			wp_enqueue_script('service_finder-js-invoice-form', plugins_url('/sf-booking/') . '/frontend/modules/invoice/resources/invoice-form.js', array('jquery') , null, true);

			wp_register_script('service_finder-js-service-form', plugins_url('/sf-booking/') . '/frontend/modules/myservices/resources/service-form.js', array('jquery') , null, true);

			wp_register_script('service_finder-js-branches-form', plugins_url('/sf-booking/') . '/frontend/modules/branches/resources/branches-form.js', array('jquery') , null, true);

			wp_enqueue_script('service_finder-js-job-form', plugins_url('/sf-booking/') . '/frontend/modules/jobs/resources/jobs-form.js', array('jquery') , null, true);

			wp_register_script('service_finder-js-team-form', plugins_url('/sf-booking/') . '/frontend/modules/team-members/resources/team-form.js', array('jquery') , null, true);

			wp_register_script('service_finder-js-bh-form', plugins_url('/sf-booking/') . '/frontend/modules/business-hours/resources/bh-form.js', array('jquery') , null, true);

			wp_register_script('service_finder-js-upgrade', plugins_url('/sf-booking/') . '/frontend/modules/upgrade/resources/upgrade.js', array('jquery') , null, true);

			

			wp_register_script('service_finder-js-articles', plugins_url('/sf-booking/') . '/frontend/modules/articles/resources/articles.js', array('jquery') , null, true);

			wp_register_script('service_finder-js-experience', plugins_url('/sf-booking/') . '/frontend/modules/experience/resources/experience.js', array('jquery') , null, true);

			wp_register_script('service_finder-js-payout', plugins_url('/sf-booking/') . '/frontend/modules/payout/resources/payout.js', array('jquery') , null, true);

			wp_register_script('service_finder-js-certificates', plugins_url('/sf-booking/') . '/frontend/modules/certificates/resources/certificates.js', array('jquery') , null, true);

			wp_register_script('service_finder-js-qualification', plugins_url('/sf-booking/') . '/frontend/modules/qualification/resources/qualification.js', array('jquery') , null, true);

			

			wp_enqueue_script('service_finder-js-app', plugins_url('/sf-booking/') . '/assets/bootstrap-calendar/app.js"', array('jquery') , null, true);



			wp_enqueue_script('bootstrap-multiselect', plugins_url('/sf-booking/') . '/assets/bootstrap-calendar/bootstrap-multiselect.js', array('jquery') , null, true);

			

			/*For Find Address Map on Provider Setting Page*/

			if(service_finder_show_map_on_site() || service_finder_show_autosuggestion_on_site()){
			wp_enqueue_script('map', plugins_url('/sf-booking/') . '/assets/map/map.js', array('jquery') , null, true);
			}

			

			wp_register_script('service_finder-js-wallet', plugins_url('/sf-booking/') . '/frontend/modules/wallet/resources/wallet.js', array('jquery') , null, true);

			

			wp_register_script('service_finder-js-offers', plugins_url('/sf-booking/') . '/frontend/modules/offers/resources/offers.js', array('jquery') , null, true);

			

			wp_enqueue_script('service_finder-js-provider-quote-form', plugins_url('/sf-booking/') . '/frontend/modules/get-quote/resources/provider-quote-form.js', array('jquery') , null, true);

			

			}elseif(service_finder_getUserRole($current_user->ID) == 'Customer'){

			

			wp_enqueue_script('service_finder-js-app', plugins_url('/sf-booking/') . '/assets/bootstrap-calendar/customerapp.js', array('jquery') , null, true);

			

			wp_enqueue_script('service_finder-js-invoice-customer-form', plugins_url('/sf-booking/') . '/frontend/modules/invoice/resources/invoice-customer-form.js', array('jquery') , null, true);

			

			wp_register_script('service_finder-js-job-post-form', plugins_url('/sf-booking/') . '/frontend/modules/jobs/resources/job-post-form.js', array('jquery') , null, true);

			wp_register_script('service_finder-js-wallet', plugins_url('/sf-booking/') . '/frontend/modules/wallet/resources/wallet.js', array('jquery') , null, true);

			

			wp_enqueue_script('service_finder-js-customer-quote-form', plugins_url('/sf-booking/') . '/frontend/modules/get-quote/resources/customer-quote-form.js', array('jquery') , null, true);

			

			}

			

			}

			

			if((is_author() && service_finder_getUserRole($author) == 'Provider') || (!is_search() && in_array(get_the_ID(),service_finder_get_id_by_shortcode('[service_finder_my_account'))) ){

			

			/*For Bootstrap Booking Calendar*/

			wp_enqueue_script('service_finder-js-calendar_expand', plugins_url('/sf-booking/') . '/assets/booking-calendar/booking_calendar_expand.js', array('jquery') , null, true);	

			wp_add_inline_script( 'service_finder-js-calendar_expand', '/*Declare global variable*/

var monthlabels = ["'.esc_html__('January','service-finder').'", "'.esc_html__('February','service-finder').'", "'.esc_html__('March','service-finder').'", "'.esc_html__('April','service-finder').'", "'.esc_html__('May','service-finder').'", "'.esc_html__('June','service-finder').'", "'.esc_html__('July','service-finder').'", "'.esc_html__('August','service-finder').'", "'.esc_html__('September','service-finder').'", "'.esc_html__('October','service-finder').'", "'.esc_html__('November','service-finder').'", "'.esc_html__('December','service-finder').'"];

var dowlabels = ["'.esc_html__('Mon','service-finder').'", "'.esc_html__('Tue','service-finder').'", "'.esc_html__('Wed','service-finder').'", "'.esc_html__('Thu','service-finder').'", "'.esc_html__('Fri','service-finder').'", "'.esc_html__('Sat','service-finder').'", "'.esc_html__('Sun','service-finder').'"];', 'before' );

			

			}

			

			if(is_author() || is_search() || is_page() || is_tax( 'providers-category' )){

			/*Search form js*/

			wp_enqueue_script('service_finder-js-search-form', plugins_url('/sf-booking/') . '/frontend/modules/search/resources/search-form.js', array('jquery') , null, true);

			}

			

			if(is_author() && service_finder_getUserRole($author) == 'Provider'){

			$settings = service_finder_getProviderSettings($author);

			

			wp_enqueue_script('bootstrap-wizard', plugins_url('/sf-booking/') . '/js/jquery.bootstrap.wizard.js', array('jquery') , null, true);

			

			/*For Bootstrap Calendar*/

			wp_enqueue_script('underscore', plugins_url('/sf-booking/') . '/assets/bootstrap-calendar/underscore-min.js', array('jquery') , null, true);

		

			wp_enqueue_script('bootstrap-calendar', plugins_url('/sf-booking/') . '/assets/bootstrap-calendar/calendar.js', array('jquery') , null, true);

			

			wp_enqueue_script('bootstrap-calendar-lang', plugins_url('/sf-booking/') . '/assets/bootstrap-calendar/language/'.str_replace('_','-',get_locale()).'.js', array('jquery') , null, true);

			

			wp_enqueue_script('service_finder-js-invoice-paid', plugins_url('/sf-booking/') . '/assets/scripts/invoice-paid.js', array('jquery') , null, true);

			wp_enqueue_script('service_finder-js-claim-business', plugins_url('/sf-booking/') . '/js/claim-business.js', array('jquery') , null, true);

			

			wp_enqueue_script('service_finder-js-profile', plugins_url('/sf-booking/') . '/assets/scripts/profile.js', array('jquery') , null, true);
			
			wp_register_script('lc_lightbox', plugins_url('/sf-booking/') . '/js/lc_lightbox.lite.js', array('jquery') , null, true);

			

			if(is_rtl()){  

			$rtl = 1;

			}else{

			$rtl = 0;

			}

			wp_add_inline_script( 'service_finder-js-profile', 'var rtloption = "'.$rtl.'";', 'before' );
			wp_add_inline_script( 'service_finder-js-custom-effects', 'var rtloption = "'.$rtl.'";', 'before' );
			
			/*For SF Uploader*/

			wp_enqueue_script('image-manager', plugins_url('/sf-booking/') . '/assets/manage-uploads/image-manager.js', array('jquery') , null, true);

		

			wp_enqueue_script('managefiles', plugins_url('/sf-booking/') . '/assets/manage-uploads/managefiles.js', array('jquery') , null, true);

		

			wp_enqueue_script('plupload', SERVICE_FINDER_ASSESTS_ADMINURL . '/load-scripts.php?c=1&load=plupload&ver=4.3.1', array('jquery') , '4.5.1', true);

			

			wp_enqueue_script('service_finder-js-booking', plugins_url('/sf-booking/') . '/frontend/modules/book-now/resources/booking.js', array('jquery') , null, true);

			/*Author Page Style 1 Start*/

			$jobid = (!empty($_GET['jobid'])) ? esc_attr($_GET['jobid'])  : '';

			$quoteid = (!empty($_GET['quoteid'])) ? esc_attr($_GET['quoteid'])  : '';

			if(service_finder_user_has_capability('bookings',$author))
			{
			$settings = service_finder_getProviderSettings($author);
			
			$booking_process = (!empty($settings['booking_process'])) ? $settings['booking_process']  : '';
			
			if($booking_process == 'on'){
			
			if(service_finder_themestyle() == 'style-3')

			{

				if(service_finder_is_booking_free_paid($author) == 'paid' && $paid_booking){

					if(service_finder_booking_date_method($author) == 'multidate' && $quoteid == '' && $jobid == ''){

					wp_enqueue_script('service_finder-js-booking-form-v2', plugins_url('/sf-booking/') . '/frontend/modules/book-now/resources/booking-form-multidate-v2.js', array('jquery') , null, true);

					}else{

					wp_enqueue_script('service_finder-js-booking-form-v2', plugins_url('/sf-booking/') . '/frontend/modules/book-now/resources/booking-form-v2.js', array('jquery') , null, true);	

					}

				}else{

					if(service_finder_booking_date_method($author) == 'multidate' && $quoteid == '' && $jobid == ''){

					wp_enqueue_script('service_finder-js-booking-form-free-v2', plugins_url('/sf-booking/') . '/frontend/modules/book-now/resources/booking-form-free-multidate-v2.js', array('jquery') , null, true);

					}else{

					wp_enqueue_script('service_finder-js-booking-form-free-v2', plugins_url('/sf-booking/') . '/frontend/modules/book-now/resources/booking-form-free-v2.js', array('jquery') , null, true);					

					}

				}

			}elseif(service_finder_themestyle() == 'style-4')

			{
				if(service_finder_booking_date_method($author) == 'multidate'){
				wp_register_script('service_finder-js-booking-form-v4', plugins_url('/sf-booking/') . '/frontend/modules/book-now/resources/booking-form-multidate-v4.js', array('jquery') , null, true);
				}else{
				wp_register_script('service_finder-js-booking-form-v4', plugins_url('/sf-booking/') . '/frontend/modules/book-now/resources/booking-form-v4.js', array('jquery') , null, true);
				}
			}else

			{

			if($service_finder_options['booking-page-style'] == 'style-1'){

				/*For booking process FREE/PAID*/

				if(service_finder_is_booking_free_paid($author) == 'paid' && $paid_booking){

					if(service_finder_booking_date_method($author) == 'multidate' && $quoteid == '' && $jobid == ''){

					wp_enqueue_script('service_finder-js-booking-form-v1', plugins_url('/sf-booking/') . '/frontend/modules/book-now/resources/booking-form-multidate-v1.js', array('jquery') , null, true);

					}else{

					wp_enqueue_script('service_finder-js-booking-form-v1', plugins_url('/sf-booking/') . '/frontend/modules/book-now/resources/booking-form-v1.js', array('jquery') , null, true);

					}

				}else{

					if(service_finder_booking_date_method($author) == 'multidate' && $quoteid == '' && $jobid == ''){

					wp_enqueue_script('service_finder-js-booking-form-free-v1', plugins_url('/sf-booking/') . '/frontend/modules/book-now/resources/booking-form-free-multidate-v1.js', array('jquery') , null, true);

					}else{

					wp_enqueue_script('service_finder-js-booking-form-free-v1', plugins_url('/sf-booking/') . '/frontend/modules/book-now/resources/booking-form-free-v1.js', array('jquery') , null, true);

					}

				}

			}elseif($service_finder_options['booking-page-style'] == 'style-2'){ 

			/*Author Page Style 2 Start*/

			/*Booking form js for FREE/PAID*/

				if(service_finder_is_booking_free_paid($author) == 'paid' && $paid_booking){

					if(service_finder_booking_date_method($author) == 'multidate' && $quoteid == '' && $jobid == ''){

					wp_enqueue_script('service_finder-js-booking-form-v2', plugins_url('/sf-booking/') . '/frontend/modules/book-now/resources/booking-form-multidate-v2.js', array('jquery') , null, true);

					}else{

					wp_enqueue_script('service_finder-js-booking-form-v2', plugins_url('/sf-booking/') . '/frontend/modules/book-now/resources/booking-form-v2.js', array('jquery') , null, true);	

					}

				}else{

					if(service_finder_booking_date_method($author) == 'multidate' && $quoteid == '' && $jobid == ''){

					wp_enqueue_script('service_finder-js-booking-form-free-v2', plugins_url('/sf-booking/') . '/frontend/modules/book-now/resources/booking-form-free-multidate-v2.js', array('jquery') , null, true);

					}else{

					wp_enqueue_script('service_finder-js-booking-form-free-v2', plugins_url('/sf-booking/') . '/frontend/modules/book-now/resources/booking-form-free-v2.js', array('jquery') , null, true);					

					}

				}

			}

			}
			
			}

			}

			}

		}



		public static function service_finder_frontend_styles() {

			global $wp_customize, $service_finder_options;

		

			$writabledir = plugin_dir_path(__FILE__).'inc/caches/';

			$css_dir = plugin_dir_path(__FILE__).'css/';

			service_finder_booking_scan_dir($css_dir);

			$cssname = service_finder_booking_scan_dir($css_dir).'.css';

			wp_register_style('bootstrap-select', plugins_url('/sf-booking/') . '/css/bootstrap-select.min.css','',null);

			

			/*For Bootstrap Booking Calendar*/

			wp_register_style('bootstrap-calendar', plugins_url('/sf-booking/') . '/assets/bootstrap-calendar/calendar.css','',null);

			

			/*For Bootstrap Booking Calendar*/

			wp_register_style('booking-calendar', plugins_url('/sf-booking/') . '/assets/booking-calendar/booking_calendar.min.css','',null);

			

			/*For My Account Section*/
			
			wp_register_style('datepicker', plugins_url('/sf-booking/') . '/frontend/modules/invoice/resources/datepicker.min.css','',null);

			

			/*Star Rating*/

			wp_register_style('star-rating', plugins_url('/sf-booking/') . '/assets/ratings/star-rating.css','',null);

			

			

			/*Datatable for display tables*/

			wp_register_style('dataTables-customLoader', plugins_url('/sf-booking/') . '/assets/datatable/dataTables.customLoader.walker.css','',null);

			

			wp_register_style('dataTables-bootstrap', plugins_url('/sf-booking/') . '/assets/datatable/dataTables.bootstrap.css','',null);

		

			wp_register_style('dataTables-tableTools', plugins_url('/sf-booking/') . '/assets/datatable/dataTables.tableTools.css','',null);

		

			/*For SF Uploader*/

			wp_register_style('image-upload', plugins_url('/sf-booking/') . '/assets/manage-uploads/image-upload.css','',null);

		

			wp_register_style('image-manager', plugins_url('/sf-booking/') . '/assets/manage-uploads/image-manager.css','',null);

		

			/*For Map on My Account Page*/

			wp_register_style('map', plugins_url('/sf-booking/') . '/assets/map/map.css','',null);

		

			/*For Embeded Code*/

			wp_register_style('embeded', plugins_url('/sf-booking/') . '/assets/embeded/embeded.css','',null);

		

			/*For Bootstrap Validator*/

			wp_register_style('bootstrapValidator', plugins_url('/sf-booking/') . '/assets/validator/bootstrapValidator.min.css','',null);

			

			wp_register_style('service_finder-custom-inline', plugins_url('/sf-booking/') . '/css/custom-inline.css','',null);

			

			wp_register_style('service_finder-city-autocomplete', plugins_url('/sf-booking/') . '/css/city-autocomplete.css','',null);

			

			wp_register_style( 'service_finder-booking-compress-cache', plugins_url('/sf-booking/') .'inc/caches/'.$cssname, array(), '1.0', 'all' );
			
			wp_register_style('lc_lightbox', plugins_url('/sf-booking/') . '/css/lc_lightbox.css','',null);

			$minifycss = (!empty($service_finder_options['minify-css'])) ? $service_finder_options['minify-css'] : '';

	

			if(!isset($minifycss) || $minifycss != 0){

				if( file_exists($writabledir.$cssname)){

					wp_enqueue_style( 'service_finder-booking-compress-cache');

				}else{

					wp_enqueue_style("service_finder-booking-minify-css", plugins_url('/sf-booking/') . "css/compressor.css.php", array(), '1.0', 'all');

				}

		

			} else {

				wp_enqueue_style( 'bootstrap-select' );
				if(!(is_home() || is_front_page())){
				wp_enqueue_style( 'datepicker' );
				}

				

				if(is_author() || (!is_search() && in_array(get_the_ID(),service_finder_get_id_by_shortcode('[service_finder_my_account')))){

				wp_enqueue_style( 'bootstrap-calendar' );

				wp_enqueue_style( 'booking-calendar' );

				}

				

				if(!is_search() && in_array(get_the_ID(),service_finder_get_id_by_shortcode('[service_finder_my_account'))){

				wp_enqueue_style( 'bootstrap-calendar' );

				wp_enqueue_style( 'dataTables-customLoader' );

				wp_enqueue_style( 'dataTables-bootstrap' );

				wp_enqueue_style( 'dataTables-tableTools' );

				wp_enqueue_style( 'image-upload' );

				wp_enqueue_style( 'image-manager' );

				wp_enqueue_style( 'embeded' );

				}

				

				wp_enqueue_style( 'star-rating' );

				wp_enqueue_style( 'map' );

				

				wp_enqueue_style( 'bootstrapValidator' );

				wp_enqueue_style( 'service_finder-custom-inline' );

				wp_enqueue_style( 'service_finder-city-autocomplete' );



			}

			

		}



		/**********************

		ADMIN SCRIPTS/STYLES

		**********************/



		public function service_finder_admin_scripts() {

			global $service_finder_options;

			$current_page = (isset($_GET['page'])) ? $_GET['page'] : false;
			$current_post_type = (isset($_GET['post_type'])) ? $_GET['post_type'] : false;

			$screen = get_current_screen();

			

			$local = get_locale();

			$langcode = explode('_',$local);

			

			$apikey = (!empty($service_finder_options['google-map-api-key'])) ? $service_finder_options['google-map-api-key'] : '';

			

			//For Google Map API
			if(service_finder_show_map_on_site() || service_finder_show_autosuggestion_on_site()){
			wp_enqueue_script('google-map', '//maps.googleapis.com/maps/api/js?key='.esc_html($apikey).'&types=(cities)&libraries=places&language='.$langcode[0], array('jquery') , null, true);
			}

			

			if( $current_post_type == 'job_listing' || $current_page == 'booking-calendar' || $current_page == 'bookings' || $current_page == 'providers' || $current_page == 'identity-check' || $current_page == 'customers' || $current_page == 'featured-requests' || $current_page == 'stripe-payout-history' || $current_page == 'masspay-payout-history' || $current_page == 'job-category-import' || $current_page == 'wallet-wired-request' || $current_page == 'invoices' || $current_page == 'existingbuyers' || $current_page == 'cities' || $current_page == 'quotations' || $current_page == 'claimbusiness' || $current_page == 'notifications' || $current_page == 'ratinglabels' || $current_page == 'upgraderequest' || $current_page == 'jobconnectrequest' || $current_page == 'providerimport' || $current_page == 'xmlsitemap' || $current_page == 'languages' ){

			wp_register_script('datatables', '//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js', array('jquery') , null, true);

			wp_register_style('datatables', '//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css','',null);

			wp_enqueue_script('datatables');

			wp_enqueue_style('datatables');

			

			wp_register_script('bootstrap-datatables', '//cdn.datatables.net/1.10.20/js/dataTables.bootstrap.min.js', array('jquery') , null, true);

			wp_register_style('bootstrap-datatables', '//cdn.datatables.net/1.10.20/css/dataTables.bootstrap.min.css','',null);

			wp_enqueue_script('bootstrap-datatables');

			wp_enqueue_style('bootstrap-datatables');

			

			wp_enqueue_style('bootstrap-select', plugins_url('/sf-booking/') . '/css/bootstrap-select.min.css','',null);

			wp_enqueue_script('bootstrap-select', plugins_url('/sf-booking/') . '/js/bootstrap-select.min.js', array('jquery') , null, true);

			

			wp_register_script('service_finder-js-existingbuyers-popup', plugins_url('/sf-booking/') . '/js/existingbuyers-popup.js', array('jquery') , null, true);



			/*Datatabe for displaying data in table Format*/

			//wp_enqueue_script('dataTables', plugins_url('/sf-booking/') . '/assets/datatable/jquery.dataTables.js', array('jquery') , null, true);

		

			//wp_enqueue_script('dataTables-tableTools', plugins_url('/sf-booking/') . '/assets/datatable/dataTables.tableTools.js', array('jquery') , null, true);

		

			//wp_enqueue_script('dataTables-bootstrap', plugins_url('/sf-booking/') . '/assets/datatable/dataTables.bootstrap.js', array('jquery') , null, true);



			wp_enqueue_script('bootstrap', plugins_url('/sf-booking/') . '/js/bootstrap.min.js', array('jquery') , null, true);

			

			/*Boot Box For Edit and alert Popup*/

			wp_enqueue_script('bootbox', plugins_url('/sf-booking/') . '/assets/bootbox/bootbox.min.js', array('jquery') , null, true);

			

			/*Touch Spin for min and max input box*/

			wp_enqueue_script('bootstrap-touchspin', plugins_url('/sf-booking/') . '/js/jquery.bootstrap-touchspin.js', array('jquery') , null, true);

			

			/*For Bootstrap Validator*/

			wp_enqueue_script('bootstrapValidator', plugins_url('/sf-booking/') . '/assets/validator/bootstrapValidator.min.js', array('jquery') , null, true);

			

			wp_enqueue_script('service_finder-js-admin-custom', plugins_url('/sf-booking/') . '/js/admin-custom.js', array('jquery') , null, true);

			}

			wp_enqueue_script('service_finder-js-admin-options', plugins_url('/sf-booking/') . '/js/admin-options.js', array('jquery') , null, true);
			

			/*Custom Js Functions*/

			if(($screen->base == 'user' && $screen->action == 'add') || $screen->base == 'user-edit'){

			wp_enqueue_script('bootstrap', plugins_url('/sf-booking/') . '/js/bootstrap.min.js', array('jquery') , null, true);
			
			wp_enqueue_script('bootbox', plugins_url('/sf-booking/') . '/assets/bootbox/bootbox.min.js', array('jquery') , null, true);
			
			wp_enqueue_script('bootstrapValidator', plugins_url('/sf-booking/') . '/assets/validator/bootstrapValidator.min.js', array('jquery') , null, true);

			wp_enqueue_script('service_finder-js-admin-custom', plugins_url('/sf-booking/') . '/js/admin-custom.js', array('jquery') , null, true);

			wp_enqueue_script('service_finder-js-manage-signup', plugins_url('/sf-booking/') . '/js/manage-signup.js', array('jquery') , null, true);

			}

			

			if($current_page == 'bookings'){

			

			wp_enqueue_script('admin-booking-form', plugins_url('/sf-booking/') . '/admin/modules/bookings/resources/bookings-form.js', array('jquery') , '1.0.0', true);	
			
			wp_enqueue_script('star-rating', plugins_url('/sf-booking/') . '/assets/ratings/star-rating.js', array('jquery') , null, true);

			

			}

			

			if($current_page == 'booking-calendar'){

			

			/*For Bootstrap Event Calendar*/

			wp_enqueue_script('underscore', plugins_url('/sf-booking/') . '/assets/bootstrap-calendar/underscore-min.js', array('jquery') , null, true);

			

			wp_enqueue_script('calendar',  plugins_url('/sf-booking/') . '/assets/bootstrap-calendar/calendar.js', array('jquery') , null, true);

			

			wp_enqueue_script('bootstrap-calendar-lang', plugins_url('/sf-booking/') . '/assets/bootstrap-calendar/language/'.str_replace('_','-',get_locale()).'.js', array('jquery') , null, true);

			

			/*Star Rating*/

			wp_enqueue_script('star-rating', plugins_url('/sf-booking/') . '/assets/ratings/star-rating.js', array('jquery') , null, true);

			

			/*For Bootstrap Event Calendar*/

			wp_enqueue_style('bootstrap-calendar', plugins_url('/sf-booking/') . '/assets/bootstrap-calendar/calendar.css','',null);

		

			wp_enqueue_script('service_finder-js-app', plugins_url('/sf-booking/') . '/assets/bootstrap-calendar/adminapp.js"', array('jquery') , null, true);

	

			wp_enqueue_script('bootstrap-multiselect', plugins_url('/sf-booking/') . '/assets/bootstrap-calendar/bootstrap-multiselect.js', array('jquery') , null, true);

			

			wp_enqueue_script('service_finder-js-schedule', plugins_url('/sf-booking/') . '/admin/modules/booking-calendar/resources/schedule.js"', array('jquery') , null, true);

			

			}

			

			if($current_page == 'providers' || $current_page == 'identity-check'){

			

			/*Call providers js*/

			wp_enqueue_script('service_finder-js-providers', plugins_url('/sf-booking/') . '/admin/modules/providers/resources/providers.js', array('jquery') , '1.0.0', true);	

			

			}

			

			if($current_page == 'customers'){

			

			/*Call customers js*/

			wp_enqueue_script('service_finder-js-admin-booking-form', plugins_url('/sf-booking/') . '/admin/modules/customers/resources/customers.js', array('jquery') , '1.0.0', true);

			

			}

			

			if($current_page == 'featured-requests'){

			

			/*Call Featured request js*/

			wp_enqueue_script('service_finder-js-featured-requests', plugins_url('/sf-booking/') . '/admin/modules/featured/resources/featured.js', array('jquery') , '1.0.0', true);	

			

			}

			

			if($current_page == 'quotations'){

			

			/*Call quotations js*/

			wp_enqueue_script('service_finder-js-quotations', plugins_url('/sf-booking/') . '/admin/modules/quotations/resources/quotations.js', array('jquery') , '1.0.0', true);	

			

			}

			

			if($current_page == 'stripe-payout-history' || $current_page == 'masspay-payout-history'){

			

			/*Call payout history js*/

			wp_enqueue_script('service_finder-js-payout-history', plugins_url('/sf-booking/') . '/admin/modules/payout-history/resources/payout.js', array('jquery') , '1.0.0', true);	

			

			}

			

			if($current_page == 'job-category-import'){

			

			/*Job category import js*/

			wp_enqueue_script('service_finder-js-job-category-import', plugins_url('/sf-booking/') . '/admin/modules/category-import/resources/category-import.js', array('jquery') , '1.0.0', true);	

			

			}

			

			if($current_page == 'claimbusiness'){

			

			/*Call claim business js*/

			wp_enqueue_script('service_finder-js-claimbusiness', plugins_url('/sf-booking/') . '/admin/modules/claimbusiness/resources/claimbusiness.js', array('jquery') , '1.0.0', true);	

			

			}

			

			if($current_page == 'invoices'){

			

			/*Call invoice request js*/

			wp_enqueue_script('service_finder-js-invoice-requests', plugins_url('/sf-booking/') . '/admin/modules/invoice/resources/invoice.js', array('jquery') , '1.0.0', true);	

			

			}

			

			if($current_page == 'cities'){

			

			/*Call cities request js*/

			wp_enqueue_script('service_finder-js-cities', plugins_url('/sf-booking/') . '/admin/modules/cities/resources/cities.js', array('jquery') , '1.0.0', true);	

			

			}

			

			if($current_page == 'existingbuyers'){

			

			/*Call existingbuyers js*/

			wp_enqueue_script('service_finder-js-existingbuyers', plugins_url('/sf-booking/') . '/admin/modules/existingbuyers/resources/existingbuyers.js', array('jquery') , '1.0.0', true);	

			

			}

			

			if($current_page == 'notifications'){

			

			wp_enqueue_script('service_finder-js-notifications', plugins_url('/sf-booking/') . '/admin/modules/notifications/resources/notifications.js', array('jquery') , '1.0.0', true);	

			

			}

			if($current_page == 'ratinglabels'){

			

			/*Call rating labels request js*/

			wp_enqueue_script('service_finder-js-ratinglabels', plugins_url('/sf-booking/') . '/admin/modules/rating-labels/resources/rating-labels.js', array('jquery') , '1.0.0', true);	

			

			}

			if($current_page == 'upgraderequest'){

			

			wp_enqueue_script('service_finder-js-upgraderequest', plugins_url('/sf-booking/') . '/admin/modules/upgrade-request/resources/upgrade.js', array('jquery') , '1.0.0', true);	

			

			}

			if($current_page == 'jobconnectrequest'){

			

			wp_enqueue_script('service_finder-js-jobconnectrequest', plugins_url('/sf-booking/') . '/admin/modules/jobconnect-request/resources/jobconnect.js', array('jquery') , '1.0.0', true);	

			

			}

			if($current_page == 'wallet-wired-request'){

			

			wp_enqueue_script('service_finder-js-wallet-request', plugins_url('/sf-booking/') . '/admin/modules/wallet-request/resources/walletrequest.js', array('jquery') , '1.0.0', true);	

			

			}

			if($current_page == 'languages'){

			

			wp_enqueue_script('service_finder-js-languages', plugins_url('/sf-booking/') . '/admin/modules/languages/resources/languages.js', array('jquery') , '1.0.0', true);	

			

			}

			if($current_page == 'providerimport'){

			

			wp_enqueue_script('service_finder-js-providerimport', plugins_url('/sf-booking/') . '/admin/modules/providerimport/resources/providerimport.js', array('jquery') , '1.0.0', true);	

			

			}

			if($current_page == 'xmlsitemap'){

			

			wp_enqueue_script('service_finder-js-xmlsitemap', plugins_url('/sf-booking/') . '/admin/modules/xml-sitemap/resources/xml-sitemap.js', array('jquery') , '1.0.0', true);	

			

			}

		}



		public function service_finder_admin_styles() {

			

			$current_page = (isset($_GET['page']) ? $_GET['page'] : false);

			$screen = get_current_screen();

			

			/*Datatable core style*/

			//wp_enqueue_style('dataTables-customLoader', plugins_url('/sf-booking/') . '/assets/datatable/dataTables.customLoader.walker.css','',null);

	

			//wp_enqueue_style('dataTables', plugins_url('/sf-booking/') . '/css/jquery.dataTables.css','',null);

		

			//wp_enqueue_style('dataTables-bootstrap', plugins_url('/sf-booking/') . '/assets/datatable/dataTables.bootstrap.css','',null);

		

			//wp_enqueue_style('dataTables-tableTools', plugins_url('/sf-booking/') . '/assets/datatable/dataTables.tableTools.css','',null);

			

			wp_enqueue_style('simple-line-icons', plugins_url('/sf-booking/') . '/css/simple-line-icons.css','',null);

			

			if( $current_page == 'dashboard' || $current_page == 'booking-calendar' || $current_page == 'bookings' || $current_page == 'providers' || $current_page == 'identity-check' || $current_page == 'customers' || $current_page == 'featured-requests' || $current_page == 'stripe-payout-history' || $current_page == 'masspay-payout-history' || $current_page == 'job-category-import' || $current_page == 'wallet-wired-request' || $current_page == 'invoices' || $current_page == 'existingbuyers' || $current_page == 'cities' || $current_page == 'quotations' || $current_page == 'claimbusiness' || $current_page == 'notifications' || $current_page == 'ratinglabels' || $current_page == 'upgraderequest' || $current_page == 'jobconnectrequest' || $current_page == 'providerimport' || $current_page == 'xmlsitemap' || $current_page == 'languages' ){

			/*Bootstrap*/

			wp_enqueue_style('bootstrap', plugins_url('/sf-booking/') . '/css/bootstrap.min.css','',null);

			/*Star Rating*/

			wp_enqueue_style('star-rating', plugins_url('/sf-booking/') . '/assets/ratings/star-rating.css','',null);

			

			wp_enqueue_style('service_finder-custom-admin-style.css', plugins_url('/sf-booking/') . '/css/custom-admin-style.css','',null);

			}

			

			if($screen->base == 'edit' && $screen->post_type == 'job_listing'){

			/*Bootstrap*/

			wp_enqueue_style('bootstrap', plugins_url('/sf-booking/') . '/css/bootstrap.min.css','',null);

			

			wp_enqueue_style('service_finder-custom-admin-style.css', plugins_url('/sf-booking/') . '/css/custom-admin-style.css','',null);

			}

			

			/*Loading Icon CSS*/

			wp_enqueue_style('service_finder-css-loading', plugins_url('/sf-booking/') . '/css/laoding.css','',null);

			

			wp_enqueue_style('service_finder-city-autocomplete', plugins_url('/sf-booking/') . '/css/city-autocomplete.css','',null);

			

			

		

		}



		public function service_finder_add_modal_popup() {

		global $service_finder_globals, $author;

		$service_finder_options = $service_finder_globals;

		

			//Modal popup for signup and login

			require plugin_dir_path(__FILE__) . '/lib/view-location.php';

			require plugin_dir_path(__FILE__) . '/lib/modal-signup.php';

			if(is_search() || $author > 0 || is_home() || is_front_page() || is_tax( 'providers-category' )){

			require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/get-quote/templates/get-quote-modal.php';

			}

			if($author > 0){

			require plugin_dir_path(__FILE__) . '/templates/claim-business.php';

			require SERVICE_FINDER_BOOKING_FRONTEND_MODULE_DIR . '/book-now/templates/service-dates.php';

			}

			

			if(in_array(get_the_ID(),service_finder_get_id_by_shortcode('[service_finder_job_applicants')) )

			{

			require SERVICE_FINDER_BOOKING_LIB_DIR . '/invite-job.php';

			}

			

		}



		public function service_finder_inline_scripts() {

		global $service_finder_Params, $service_finder_options, $current_user, $woopayment, $paymentsystem;

		$countryarr = (!empty($service_finder_options['allowed-country'])) ? $service_finder_options['allowed-country'] : array();

		$woopayment = (isset($service_finder_options['woocommerce-payment'])) ? esc_html($service_finder_options['woocommerce-payment']) : false;

		

		$defaultlatlng = service_finder_get_default_latlong();

		$defaultlat = $defaultlatlng['defaultlat'];

		$defaultlng = $defaultlatlng['defaultlng'];



		$defaultzoomlevel = 2;

		

		$paymentsystem = ($woopayment) ? 'woocommerce' : 'local';

		

		$alowedcountries = '';

		if(is_array($countryarr)){

		if(count($countryarr) > 1){

		$alowedcountries = (!empty($countryarr)) ? '"'.implode('","', $countryarr).'"' : '';

		}

		}else{

		$countryarr = array();

		}

		?>

		<script type="text/javascript">

		// <![CDATA[

		var param = '';

		var ajaxurl = '';

		var scheduleurl = '';

		var caltmpls = '';

		var customerscheduleurl = '';

		var myaccount = '';

		var datavalidate = '';

		var captchavalidate = '';

		var captchavalidatepage = '';

		var captchaquotevalidate = '';

		var countrycount = '';

		var allcountry = '';

		var allowedcountry = '';

		var allowedcountries = '';

		var showallowedcountries = '';

		var currencysymbol = '';

		var showaddressinfo = '';

		var showpostaladdress = '';

		var accessaddressinfo = '';

		var tokenRequestUrl = '';

		var availabilitytab;

		var busihours;

		var themestyle;

		var radiusfillcolor;

		var radiusbordercolor;

		var radiusfillcoloropacity;

		var radiusbordercoloropacity;

		var radiusborderthickness;

		var woopayment;

		var cart_url;

		var chkwoopayment;

		var userrole;

		var dateformat;

		var defaultcountry;

		var defaultlat;

		var defaultlng;

		var defaultzoomlevel;

		var hide_msg_seconds;
		
		var siteautosuggestion;

		

		ajaxurl = '<?php echo esc_js(admin_url('admin-ajax.php')); ?>';

		chkwoopayment = '<?php echo $woopayment; ?>';

		if(chkwoopayment == 1){

		woopayment = true;

		}else{

		woopayment = false;

		}

		langcode = '<?php echo LANGUAGE_CODE; ?>';

		imgpath = '<?php echo SERVICE_FINDER_BOOKING_IMAGE_URL; ?>';

		siteautosuggestion = '<?php echo service_finder_show_autosuggestion_on_site(); ?>';
		
		hide_msg_seconds = '<?php echo (!empty($service_finder_options['hide-msg-seconds'])) ? intval($service_finder_options['hide-msg-seconds']) * 1000 : 5000; ?>';

		cart_url = '<?php echo (class_exists( 'WooCommerce' ) && !is_admin()) ? wc_get_cart_url() : ''; ?>';

		currencysymbol = '<?php echo service_finder_currencysymbol(); ?>';

		scheduleurl = '<?php echo SERVICE_FINDER_BOOKING_FRONTEND_MODULE_URL . '/schedule/schedule-data.php';?>';

		caltmpls = '<?php echo SERVICE_FINDER_BOOKING_ASSESTS_URL . '/bootstrap-calendar/tmpls/';?>';

		customerscheduleurl = '<?php echo SERVICE_FINDER_BOOKING_FRONTEND_MODULE_URL . '/schedule/customer-schedule-data.php';?>';

		myaccount = '<?php echo esc_js(service_finder_get_url_by_shortcode('[service_finder_my_account]'));?>';

		datavalidate = '<?php echo SERVICE_FINDER_BOOKING_FRONTEND_MODULE_URL . '/book-now/validate-data.php';?>';

		captchavalidate = '<?php echo SERVICE_FINDER_BOOKING_LIB_URL . '/validate-captcha.php';?>';

		captchavalidatepage = '<?php echo SERVICE_FINDER_BOOKING_LIB_URL . '/validate-captcha-page.php';?>';

		captchaquotevalidate = '<?php echo SERVICE_FINDER_BOOKING_LIB_URL . '/validate-quote-captcha.php';?>';

		countrycount = '<?php echo esc_js(count($countryarr)); ?>';

		allcountry = '<?php echo (!empty($service_finder_options['all-countries'])) ? $service_finder_options['all-countries'] : ''; ?>';

		showaddressinfo = '<?php echo (!empty($service_finder_options['show-address-info'])) ? $service_finder_options['show-address-info'] : ''; ?>';

		showpostaladdress = '<?php echo (!empty($service_finder_options['show-postal-address'])) ? $service_finder_options['show-postal-address'] : ''; ?>';

		accessaddressinfo = '<?php echo service_finder_check_address_info_access(); ?>';

		themestyle = '<?php echo service_finder_themestyle(); ?>';

		dateformat = '<?php echo esc_js(service_finder_datepicker_format()); ?>';

		defaultlat = '<?php echo esc_js($defaultlat); ?>';

		defaultlng = '<?php echo esc_js($defaultlng); ?>';

		defaultzoomlevel = '<?php echo esc_js($defaultzoomlevel); ?>';

		defaultcountry = '<?php echo (!empty($service_finder_options['default-country'])) ? $service_finder_options['default-country'] : ''; ?>';

		radiusfillcolor = '<?php echo (!empty($service_finder_options['map-radius-fill-color'])) ? $service_finder_options['map-radius-fill-color'] : ''; ?>';

		radiusbordercolor = '<?php echo (!empty($service_finder_options['map-radius-border-color'])) ? $service_finder_options['map-radius-border-color'] : ''; ?>';

		radiusfillcoloropacity = '<?php echo (!empty($service_finder_options['radius-fill-color-opacity'])) ? $service_finder_options['radius-fill-color-opacity'] : ''; ?>';

		radiusbordercoloropacity = '<?php echo (!empty($service_finder_options['radius-border-color-opacity'])) ? $service_finder_options['radius-border-color-opacity'] : ''; ?>';

		userrole = '<?php echo service_finder_getUserRole($current_user->ID); ?>';

		

		radiusborderthickness = '<?php echo (!empty($service_finder_options['radius-border-thickness'])) ? $service_finder_options['radius-border-thickness'] : ''; ?>';

		if(!parseInt(allcountry)){

			if(parseInt(countrycount) == 1){

			allowedcountry = '<?php echo (!empty($service_finder_options['allowed-country'][0])) ? strtolower($service_finder_options['allowed-country'][0]) : ''; ?>';

			}else if(parseInt(countrycount) > 1){

			allowedcountries = [<?php echo $alowedcountries; ?>];

			showallowedcountries = '<?php echo $alowedcountries; ?>';

			}

		}	

		// ]]>

		</script>

		<?php

		require plugin_dir_path(__FILE__) . '/lib/localize.php';

		}



	} // END class booked_plugin

} // END if(!class_exists('service_finder_booking_plugin'))



if(class_exists('service_finder_booking_plugin')) {



	



	// Installation and uninstallation hooks



	register_activation_hook(__FILE__, array('service_finder_booking_plugin', 'service_finder_activate'));



	register_deactivation_hook(__FILE__, array('service_finder_booking_plugin', 'service_finder_deactivate'));

	

	

	// instantiate the plugin class



	$service_finder_booking_plugin = new service_finder_booking_plugin();



	



	/*Initialize the class for service finder uplaoder*/



	$plup = new SERVICE_FINDER_Upload_Image();







	$plup->add_actions();



	



	/*Initialize the class for file manager*/



	$plfile = new SERVICE_FINDER_FileSpace();



	



	$plfile->add_actions();



	



	/*Initialize the class for embeded code*/



	$oembed = new ClassOEmbed();



	



	$oembed->add_actions();



	



	if(is_admin()){



	new SERVICE_FINDER_sedateAdmin();



	}







	// Add a link to the settings page onto the plugin page



	if(isset($service_finder_booking_plugin)) {



		add_action('wp_enqueue_scripts', array('service_finder_booking_plugin', 'service_finder_frontend_styles'));



	}



}



/*Redefine wp_new_user_notification function*/

if ( !function_exists('wp_new_user_notification') ) {

function wp_new_user_notification( $user_id, $deprecated = null, $notify = '' ) {

	global $wpdb, $wp_hasher, $service_finder_options;

	

	if ( ! in_array( $notify, array( 'user', 'both', '' ), true ) ) {

		return;

	}

	

	$user = get_userdata( $user_id );

	

	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);



	// Generate something random for a password reset key.

	$key = wp_generate_password( 20, false );





	// Now insert the key, hashed, into the DB.

	if ( empty( $wp_hasher ) ) {

		require_once ABSPATH . WPINC . '/class-phpass.php';

		$wp_hasher = new PasswordHash( 8, true );

	}

	$hashed = time() . ':' . $wp_hasher->HashPassword( $key );

	$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );



	if($service_finder_options['password-reset-to-customer-subject'] != ""){

		$subject = $service_finder_options['password-reset-to-customer-subject'];

	}else{

		$subject = sprintf(__('[%s] Your username and password info'), $blogname);

	}

	

	$pageurl = service_finder_get_url_by_shortcode( '[service_finder_forgot_password' );

	

	if(!empty($service_finder_options['password-reset-to-customer'])){

				$message = $service_finder_options['password-reset-to-customer'];

	}else{

		$message = 'Username: %USERNAME%';

		$message .= 'To set your password, visit the following address:';

		$message .=  '%PASSWORDRESETLINK%';

	}

	

	$tokens = array('%USERNAME%','%PASSWORDRESETLINK%');

	

	$reset_url = add_query_arg( array('sfresetpass' => true,'sfrp_action' => 'rp','key' => $key,'login' => rawurlencode( $user->user_login )), $pageurl );

			

	$reset_link = '<a href="' . esc_url($reset_url) . '">' . $reset_url . '</a>';

	

	$replacements = array($user->user_login,$reset_link);		

	

	$msg_body = str_replace($tokens,$replacements,$message);

	

	service_finder_wpmailer($user->user_email,$subject,$msg_body);

}

}



