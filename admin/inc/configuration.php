<?php
/**
 * Configuration File
 *
 * @package RAT6
 * @subpackage Config
 */

$site_full_name = 'RAT6';
$site_version_no = '3.0';
$name_url_clean = lowercase(str_replace(' ','-',$site_full_name));
$site_link_back_url = 'http://www.facebook.com/pages/RAT6/196237143792761/';
$ver_no_clean = str_replace('.','',$site_version_no);
$cookie_name = lowercase($name_url_clean) .'_cookie_'. $ver_no_clean;
if (isset($_GET['redirect'])){
	$cookie_redirect = $_GET['redirect'];
} else {	
	$cookie_redirect = 'pages.php';
}
$cookie_login = 'index.php';
$cookie_time = '7200';  // 2 hours 
$api_url = 'http://www.facebook.com/pages/RAT6/196237143792761/api/start/v3.php';
if (!defined('RATVERSION')) define('RATVERSION', $site_version_no);

?>