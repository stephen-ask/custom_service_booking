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
/*Save Email into DB ajax call*/
add_action('wp_ajax_nl_signup', 'service_finder_nl_signup');
add_action('wp_ajax_nopriv_nl_signup', 'service_finder_nl_signup');

function service_finder_nl_signup(){
global $wpdb, $service_finder_Tables;
$data = array(
		'email' =>esc_attr($_POST['newsletter_email']),
		);

$wpdb->insert($service_finder_Tables->newsletter,wp_unslash($data));

$newsletter_id = $wpdb->insert_id;

if ( ! $newsletter_id ) {
	$adminemail = get_option( 'admin_email' );
	$allowedhtml = array(
					'a' => array(
						'href' => array(),
						'title' => array()
					),
				);
	$error = array(
			'status' => 'error',
			'err_message' => sprintf( wp_kses(esc_html__('Couldn&#8217;t subscribed your mail... please contact the <a href="mailto:%s">Administrator</a> !', 'service-finder'),$allowedhtml), $adminemail )
			);
	echo json_encode($error);
	
} else {
	$msg = (!empty($service_finder_options['nl-subscription'])) ? $service_finder_options['nl-subscription'] : esc_html__('You have been subscribed successfully', 'service-finder');	
	$success = array(
			'status' => 'success',
			'suc_message' => $msg
			);
	echo json_encode($success);
}

exit;
} 