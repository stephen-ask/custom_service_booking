<?php
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/
//Return global variable $service_finder_ThemeParams
function service_finder_plugin_global_vars($global_var)
{
	global $wpdb, $current_user, $service_finder_ThemeParams, $current_template, $registerErrors, $service_finder_Params, $paypal, $service_finder_Tables, $service_finder_Errors, $registerMessages,$wp_filesystem,$globalproviderid,$paymentsystem;
	switch($global_var){
	case 'wpdb':
		return $wpdb;
		break;
	case 'current_user':
		return $current_user;
		break;	
	case 'service_finder_ThemeParams':
		return $service_finder_ThemeParams;
		break;
	case 'current_template':
		return $current_template;
		break;	
	case 'registerErrors':
		return $registerErrors;
		break;	
	case 'service_finder_Params':
		return $service_finder_Params;
		break;
	case 'paypal':
		return $paypal;
		break;
	case 'service_finder_Tables':
		return $service_finder_Tables;
		break;
	case 'service_finder_Errors':
		return $service_finder_Errors;
		break;
	case 'registerMessages':
		return $registerMessages;
		break;
	case 'wp_filesystem':
		return $wp_filesystem;
		break;
	case 'globalproviderid':
		return $globalproviderid;
		break;
	case 'paymentsystem':
		return $paymentsystem;
		break;									
	default:
		break;		
	}
}
