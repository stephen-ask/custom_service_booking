<?php
session_start();
/*****************************************************************************
*
*	copyright(c) - aonetheme.com - Service Finder Team
*	More Info: http://aonetheme.com/
*	Coder: Service Finder Team
*	Email: contact@aonetheme.com
*
******************************************************************************/
require_once('../../../../wp-load.php');
/*Validate zipcode at the time of booking*/
header('Content-type: application/json');

$valid = true;		


$captcha_code = isset($_POST['captcha_code']) ? esc_html($_POST['captcha_code']) : 0;
	
if((empty($_SESSION['captcha_code_providersignuppage'] ) || strcasecmp($_SESSION['captcha_code_providersignuppage'], $captcha_code) != 0)){  
	$valid = false;
}else{
	$valid = true;
}

echo json_encode(array(
    'valid' => $valid,
)); 