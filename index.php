<?php
//session_set_cookie_params(0, '/', '.$site_url');
session_start();
include('system/setting.php');
//include('system/database.php');
include('system/function.php');

/*
replace array indexes:
1) fix windows slashes
2) strip up-tree ../ as possible hack attempts
*/
$URL = str_replace(
	array( '\\', '../' ),
	array( '/',  '' ),
	$_SERVER['REQUEST_URI']
);

if ($offset = strpos($URL,'?')){
	// strip getData
	$URL = substr($URL,0,$offset);
}else if($offset = strpos($URL,'#')){
	// Since hashes are after getData, you only need to strip hashes when there is no getData
	$URL = substr($URL,0,$offset);
}

/*
the path routes below aren't just handy for stripping out
the REQUEST_URI and looking to see if this is an attempt at
direct file access, they're also useful for moving uploads,
creating absolute URI's if needed, etc, etc
*/
$chop = -strlen(basename($_SERVER['SCRIPT_NAME']));
define('DOC_ROOT',substr($_SERVER['SCRIPT_FILENAME'],0,$chop));
define('URL_ROOT',substr($_SERVER['SCRIPT_NAME'],0,$chop));

//strip off the URL root from REQUEST_URI
if (URL_ROOT != '/') $URL=substr($URL,strlen(URL_ROOT));

//strip off excess slashes
$URL = trim($URL,'/');

//404 if trying to call a real file
if(file_exists(DOC_ROOT.'/'.$URL) && ($_SERVER['SCRIPT_FILENAME'] != DOC_ROOT.$URL) && ($URL != '') && ($URL != 'index.php')) $includeFile="site/404.php";

//If $url is empty of default value, set action to 'default' otherwise, explode $URL into an array
$uri = (($URL == '') || ($URL == 'index.php') || ($URL == 'index.html')) ? array($site_homepage) : explode('/',html_entity_decode($URL));

if($uri[0]=="process" || $uri[0]=="ajax"){
	$includeFile = "system/".$uri[0].'.php';
}else{
	$includeFile = "site/".preg_replace('/[^\w]/','',$uri[0]).'.php';
}

//Check real file to include
if (is_file($includeFile)) include($includeFile); else include("site/404.php");

//Close databases
//mysqli_close($connect);
?>