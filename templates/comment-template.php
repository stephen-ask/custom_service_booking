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

if($service_finder_options['review-system']){

	switch($service_finder_options['review-style']){
		case 'open-review':
			require SERVICE_FINDER_BOOKING_TEMPLATES_DIR . '/open-review.php';
			break;
		case 'booking-review':
			require SERVICE_FINDER_BOOKING_TEMPLATES_DIR . '/booking-review.php';
			break;
		default:
			require SERVICE_FINDER_BOOKING_TEMPLATES_DIR . '/open-review.php';
			break;
	}
}


?>
