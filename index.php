<?php
$load['plugin'] = true;
if (file_exists('RATconfig.php')) {
	require_once('RATconfig.php');
}

# Relative
if (defined('RATADMIN')) {
	$RATADMIN = RATADMIN;
} else {
	$RATADMIN = 'admin';
}
$admin_relative = $RATADMIN.'/inc/';
$lang_relative = $RATADMIN.'/';
$base = true;

# Include common.php
include($RATADMIN.'/inc/common.php');

# get page id (url slug) that is being passed via .htaccess mod_rewrite
if (isset($_GET['id'])){ 
	$id = str_replace ('..','',$_GET['id']);
	$id = str_replace ('/','',$id);
	$id = lowercase($id);
} else {
	$id = "index";
}

# define page, spit out 404 if it doesn't exist
$file = RATDATAPAGESPATH . $id .'.xml';
$file_404 = RATDATAOTHERPATH . '404.xml';
$user_created_404 = RATDATAPAGESPATH . '404.xml';
if (! file_exists($file)) {
	if (file_exists($user_created_404)) {
		//user created their own 404 page, which overrides the default 404 message
		$file = $user_created_404;
	} elseif (file_exists($file_404))	{
		$file = $file_404;
	}
	exec_action('error-404');
}

# get data from page
$data_index = getXML($file);
$title = $data_index->title;
$date = $data_index->pubDate;
$metak = $data_index->meta;
$metad = $data_index->metad;
$url = $data_index->url;
$content = $data_index->content;
$parent = $data_index->parent;
$template_file = $data_index->template;
$private = $data_index->private;

# if page is private, check user
if ($private == 'Y') {
	redirect('404');
}

# if page does not exist, throw 404 error
if ($url == '404') {
	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
}

# check for correctly formed url
if (defined('RATCANONICAL')) {
	if ($_SERVER['REQUEST_URI'] != find_url($url, $parent, 'relative')) {
		redirect(find_url($url, $parent));
	}
}

# include the functions.php page if it exists within the theme
if ( file_exists(RATTHEMESPATH .$TEMPLATE."/functions.php") ) {
	include(RATTHEMESPATH .$TEMPLATE."/functions.php");	
}

# call pretemplate Hook
exec_action('index-pretemplate');

# include the template and template file set within theme.php and each page
if ( (!file_exists(RATTHEMESPATH .$TEMPLATE."/".$template_file)) || ($template_file == '') ) { $template_file = "template.php"; }
include(RATTHEMESPATH .$TEMPLATE."/".$template_file);

# call posttemplate Hook
exec_action('index-posttemplate');

?>