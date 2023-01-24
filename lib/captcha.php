<?php
session_start();
require_once('../../../../wp-load.php');
include(SERVICE_FINDER_BOOKING_LIB_DIR.'/phptextClass.php');
$where = (!empty($_GET['where'])) ? $_GET['where'] : '';
/*create class object*/
$phptextObj = new phptextClass();	
/*phptext function to genrate image with text*/
$phptextObj->phpcaptcha('#162453','#fff',120,40,10,25,'#162453',$where);	
 ?>