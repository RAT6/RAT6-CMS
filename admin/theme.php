<?php 
/**
 * Theme
 *
 * @package RAT6
 * @subpackage Theme
 */

# setup inclusions
$load['plugin'] = true;
include('inc/common.php');

# variable settinRAT
login_cookie_check();
$path 			= RATDATAOTHERPATH; 
$file 			= "website.xml"; 
$theme_options 	= '';

# was the form submitted?
if( (isset($_POST['submitted'])) && (isset($_POST['template'])) ) {
	
	# check for csrf
	$nonce = $_POST['nonce'];	
	if(!check_nonce($nonce, "activate")) {
		die("CSRF detected!");
	}
	
	# get passed value from form
	$TEMPLATE = $_POST['template'];
	
	# backup old website.xml file
	$bakpath = RATBACKUPSPATH.'other/';
	createBak($file, $path, $bakpath);
	
	# udpate website.xml file with new theme
	$xml = new SimpleXMLExtended('<item></item>');
	$note = $xml->addChild('SITENAME');
	$note->addCData($SITENAME);
	$note = $xml->addChild('SITEURL');
	$note->addCData($SITEURL);
	$note = $xml->addChild('TEMPLATE');
	$note->addCData($TEMPLATE);
	$xml->addChild('PRETTYURLS', $PRETTYURLS);
	$xml->addChild('PERMALINK', $PERMALINK);
	XMLsave($xml, $path . $file);
	
	$success = i18n_r('THEME_CHANGED');
}

# get available themes (only look for folders)
$themes_handle = opendir(RATTHEMESPATH) or die("Unable to open ".RATTHEMESPATH);
while ($file = readdir($themes_handle)) {
	$curpath = RATTHEMESPATH . $file;
	if( is_dir($curpath) && $file != "." && $file != ".." ) {
		$sel="";
		if (file_exists($curpath.'/template.php')){
			if ($TEMPLATE == $file)	{ 
				$sel="selected";
			}
			$theme_options .= '<option '.$sel.' value="'.$file.'" >'.$file.'</option>';
		}
	}
}
?>

<?php get_template('header', cl($SITENAME).' &raquo; '.i18n_r('ACTIVATE_THEME')); ?>
	
	<h1><a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?></a> <span>&raquo;</span> <?php i18n('THEME_MANAGEMENT');?></h1>
	<?php include('template/include-nav.php'); ?>
	<?php include('template/error_checking.php'); ?>

<div class="bodycontent">
	
	<div id="maincontent">
		<div class="main">
		<h3><?php i18n('CHOOSE_THEME');?></h3>
		<form action="<?php echo htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES); ?>" method="post" accept-charset="utf-8" >
		<input id="nonce" name="nonce" type="hidden" value="<?php echo get_nonce("activate"); ?>" />			
		<p style="display:none" id="waiting" ><?php i18n('SITEMAP_WAIT');?></p>

		<p><select class="text" style="width:250px;" name="template" >
					<?php echo $theme_options; ?>
			</select>&nbsp;&nbsp;&nbsp;<input class="submit" type="submit" name="submitted" value="<?php i18n('ACTIVATE_THEME');?>" /></p>
		</form>
		<?php
			if ( $SITEURL ) {	
				echo '<p><b>'.i18n_r('THEME_PATH').': &nbsp;</b> <code>'.$SITEURL.'theme/'.$TEMPLATE.'/</code></p>';
			}
		 	if (file_exists('../theme/'.$TEMPLATE.'/images/screenshot.png')) { 
				echo '<p><img style="border:2px solid #333;" src="../theme/'.$TEMPLATE.'/images/screenshot.png" alt="'.i18n_r('THEME_SCREENSHOT').'" /></p>';
			} else {
				echo '<p><em>'.i18n_r('NO_THEME_SCREENSHOT').'</em></p>';
			}

			exec_action('theme-extras');
		?>
			
		</div>
	
	</div>
	
	<div id="sidebar" >
		<?php include('template/sidebar-theme.php'); ?>
	</div>

	<div class="clear"></div>
	</div>
<?php get_template('footer'); ?>