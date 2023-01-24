<?php
require_once('../../../../wp-load.php');
$wp_filesystem = service_finder_plugin_global_vars('wp_filesystem');
if ( empty( $wp_filesystem ) ) {
  require_once ABSPATH . '/wp-admin/includes/file.php';
  WP_Filesystem();
}

$current_user = wp_get_current_user();
if ( !($current_user instanceof WP_User) ){
	echo "Unauthorised access";die;
}

$roles = $current_user->roles;  //$roles is an array
if( $roles[0] == 'administrator' || $roles[0] == 'Provider' || $roles[0] == 'Customer'){
	$file = (isset($_REQUEST['file'])) ? $_REQUEST['file']: '';
	$path      = parse_url($file, PHP_URL_PATH);       // get path from url
	$extension = pathinfo($path, PATHINFO_EXTENSION); // get ext from path
	$allowed =  array("doc","docx","pdf","xls","xlsx","rtf","txt","ppt","pptx","jpg","jpeg","png","csv");
	if(!in_array($extension,$allowed) ) {
		echo 'Download file extension is not vaild. Please Download ex:- "doc", "docx", "pdf", "xls", "xlsx", "rtf", "txt", "ppt", "pptx", "jpg", "jpeg", "png", "csv"';die;
	}

	if(isset($_SERVER["HTTPS"])){
	$fullPath = $_SERVER['DOCUMENT_ROOT'].'/'.str_replace('https://'.$_SERVER['HTTP_HOST'].'/','',$_REQUEST['file']);
	}else{
	$fullPath = $_SERVER['DOCUMENT_ROOT'].'/'.str_replace('http://'.$_SERVER['HTTP_HOST'].'/','',$_REQUEST['file']);
	}
	if($wp_filesystem->exists($fullPath)){
		$fsize = filesize($fullPath);
		$path_parts = pathinfo($fullPath);
		$ext = strtolower($path_parts["extension"]);
		switch ($ext) {
			case "pdf":
			header("Content-Type: application/pdf"); // add here more headers for diff. extensions
			header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a download
			break;
			case "doc":
			header("Content-Type: application/msword"); // add here more headers for diff. extensions
			header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a download
			break;
			default;
			header("Content-Type: application/octet-stream");
			header("Content-Disposition: filename=\"".$path_parts["basename"]."\"");
		}
		header("Content-length: $fsize");
		header("Cache-control: private"); //use this to open files directly
		$buffer = $wp_filesystem->get_contents($fullPath);
		echo $buffer;
	}
	exit;
	
}else{
	echo "Unauthorised access";die;
}
     

  


