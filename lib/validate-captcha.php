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
$role = isset($_GET['role']) ? esc_html($_GET['role']) : 'provider';
	
if($role == 'provider'){	
if((empty($_SESSION['captcha_code_providersignup'] ) || strcasecmp($_SESSION['captcha_code_providersignup'], $captcha_code) != 0)){  
	$valid = false;
}else{
	$valid = true;
}
}

if($role == 'customer'){
if((empty($_SESSION['captcha_code_customersignup'] ) || strcasecmp($_SESSION['captcha_code_customersignup'], $captcha_code) != 0)){  
	$valid = false;
}else{
	$valid = true;
}
}

echo json_encode(array(
    'valid' => $valid,
)); 