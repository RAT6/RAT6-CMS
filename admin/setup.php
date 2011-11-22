<?php 
/**
 * Setup
 *
 * Second step of installation (install.php). Sets up initial files & structure
 *
 * @package RAT6
 * @subpackage Installation
 */

# setup inclusions
$load['plugin'] = true;
if($_POST['lang'] != '') { $LANG = $_POST['lang']; }
include('inc/common.php');

# default variables
if(defined('RATLOGINSALT')) { $loRATalt = RATLOGINSALT;} else { $loRATalt = null; }
$kill = ''; 
$status = ''; 
$err = null; 
$message = null; 
$random = null;
$fullpath = suggest_site_path();	
$path_parts = suggest_site_path(true);   

# if the form was submitted, continue
if(isset($_POST['submitted'])) {
	if($_POST['sitename'] != '') { 
		$SITENAME = htmlentities($_POST['sitename'], ENT_QUOTES, 'UTF-8'); 
	} else { 
		$err .= i18n_r('WEBSITENAME_ERROR') .'<br />'; 
	}
	
	$urls = $_POST['siteurl']; 
	if(preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $urls)) {
		$SITEURL = tsl($_POST['siteurl']); 
	} else {
		$err .= i18n_r('WEBSITEURL_ERROR') .'<br />'; 
	}
	
	if($_POST['user'] != '') { 
		$USR = strtolower($_POST['user']);
	} else {
		$err .= i18n_r('USERNAME_ERROR') .'<br />'; 
	}
	
	if (! check_email_address($_POST['email'])) {
		$err .= i18n_r('EMAIL_ERROR') .'<br />'; 
	} else {
		$EMAIL = $_POST['email'];
	}

	# if there were no errors, continue setting up the site
	if ($err == '')	{
		
		# create new password
		$random = createRandomPassword();
		$PASSWD = passhash($random);
		
		# create user xml file
		$file = _id($USR).'.xml';
		createBak($file, RATUSERSPATH, RATBACKUSERSPATH);
		$xml = new SimpleXMLElement('<item></item>');
		$xml->addChild('USR', $USR);
		$xml->addChild('PWD', $PASSWD);
		$xml->addChild('EMAIL', $EMAIL);
		$xml->addChild('HTMLEDITOR', '1');
		$xml->addChild('TIMEZONE', $TIMEZONE);
		$xml->addChild('LANG', $LANG);
		if (! XMLsave($xml, RATUSERSPATH . $file) ) {
			$kill = i18n_r('CHMOD_ERROR');
		}
		
		# create password change trigger file
		$flagfile = RATUSERSPATH . _id($USR).".xml.reset";
		copy(RATUSERSPATH . $file, $flagfile);
		
		# create new website.xml file
		$file = 'website.xml';
		$xmls = new SimpleXMLExtended('<item></item>');
		$note = $xmls->addChild('SITENAME');
		$note->addCData($SITENAME);
		$note = $xmls->addChild('SITEURL');
		$note->addCData($SITEURL);
		$xmls->addChild('TEMPLATE', 'Innovation');
		$xmls->addChild('PRETTYURLS', '');
		$xmls->addChild('PERMALINK', '');
		if (! XMLsave($xmls, RATDATAOTHERPATH . $file) ) {
			$kill = i18n_r('CHMOD_ERROR');
		}
		
		# create default index.xml page
		$init = RATDATAPAGESPATH.'index.xml'; 
		$temp = RATADMININCPATH.'tmp/tmp-index.xml';
		if (! file_exists($init))	{
			copy($temp,$init);
		}

		# create default components.xml page
		$init = RATDATAOTHERPATH.'components.xml';
		$temp = RATADMININCPATH.'tmp/tmp-components.xml'; 
		if (! file_exists($init)) {
			copy($temp,$init);
		}
		
		# create default 404.xml page
		$init = RATDATAOTHERPATH.'404.xml';
		$temp = RATADMININCPATH.'tmp/tmp-404.xml'; 
		if (! file_exists($init)) {
			copy($temp,$init);
		}

		# create root .htaccess file
		$init = RATROOTPATH.'.htaccess';
		$temp_data = file_get_contents(RATROOTPATH .'temp.htaccess');
		$temp_data = str_replace('**REPLACE**',tsl($path_parts), $temp_data);
		$fp = fopen($init, 'w');
		fwrite($fp, $temp_data);
		fclose($fp);
		if (!file_exists($init)) {
			$kill .= sprintf(i18n_r('ROOT_HTACCESS_ERROR'), 'temp.htaccess', '**REPLACE**', tsl($path_parts)) . '<br />';
		} else {
			unlink(RATROOTPATH .'temp.htaccess');
		}
		
		# create RATconfig.php if it doesn't exist yet.
		$init = RATROOTPATH.'RATconfig.php';
		$temp = RATROOTPATH.'temp.RATconfig.php';
		if (file_exists($init)) {
			unlink($temp);
			if (file_exists($temp)) {
				$kill .= sprintf(i18n_r('REMOVE_TEMPCONFIG_ERROR'), 'temp.RATconfig.php') . '<br />';
			}
		} else {
			rename($temp, $init);
			if (!file_exists($init)) {
				$kill .= sprintf(i18n_r('MOVE_TEMPCONFIG_ERROR'), 'temp.RATconfig.php', 'RATconfig.php') . '<br />';
			}
		}
		
		# send email to new administrator
		$subject  = $site_full_name .' '. i18n_r('EMAIL_COMPLETE');
		$message .= i18n_r('EMAIL_USERNAME') . ': '. stripslashes($_POST['user']);
		$message .= '<br>'. i18n_r('EMAIL_PASSWORD') .': '. $random;
		$message .= '<br>'. i18n_r('EMAIL_LOGIN') .': <a href="'.$SITEURL.$RATADMIN.'/">'.$SITEURL.$RATADMIN.'/</a>';
		$message .= '<br><br>'. i18n_r('EMAIL_THANKYOU') .' '.$site_full_name.'!';
		$status   = sendmail($EMAIL,$subject,$message);
		
		# set the login cookie, then redirect user to secure panel		
		setcookie('RAT_ADMIN_USERNAME', _id($USR));
		create_cookie();
		
		# check for fatal errors, if none, redirect to 
		if ($kill == '') {
			redirect("welcome.php");
		}
	}
}
?>

<?php get_template('header', $site_full_name.' &raquo; '. i18n_r('INSTALLATION')); ?>
	
		<h1><?php echo $site_full_name; ?> <span>&raquo;</span> <?php i18n('INSTALLATION'); ?></h1>
	</div>
</div>
<div class="wrapper">
	
<?php
	
	# display error or success messages 
	if ($status == 'success') {
		echo '<div class="updated">'. i18n_r('NOTE_REGISTRATION') .' '. $_POST['email'] .'</div>';
	} 
	elseif ($status == 'error') {
		echo '<div class="error">'. i18n_r('NOTE_REGERROR') .'.</div>';
	}
	
	if ($kill != '') {
		echo '<div class="error">'. $kill .'</div>';
	}	
	
	if ($err != '') {
		echo '<div class="error">'. $err .'</div>';
	}
	
	if ($random != ''){
		echo '<div class="updated">'.i18n_r('NOTE_USERNAME').' <b>'. stripslashes($_POST['user']) .'</b> '.i18n_r('NOTE_PASSWORD').' <b>'. $random .'</b> &nbsp&raquo;&nbsp; <a href="welcome.php">'.i18n_r('EMAIL_LOGIN').'</a></div>';
	}
?>
	<div id="maincontent">
		<div class="main" >
			<h3><?php echo $site_full_name .' '. i18n_r('INSTALLATION'); ?></h3>
			<form action="<?php myself(); ?>" method="post" accept-charset="utf-8" >
				<div class="leftsec">
					<p><label for="sitename" ><?php i18n('LABEL_WEBSITE'); ?>:</label><input class="text" id="sitename" name="sitename" type="text" value="<?php if(isset($_POST['sitename'])) { echo $_POST['sitename']; } ?>" /></p>
					<input name="siteurl" type="hidden" value="<?php if(isset($_POST['siteurl'])) { echo $_POST['siteurl']; } else { echo $fullpath;} ?>" />
					<input name="lang" type="hidden" value="<?php echo $LANG; ?>" />
					<p>
					<label for="user" ><?php i18n('LABEL_USERNAME'); ?>:</label><input class="text" name="user" id="user" type="text" value="<?php if(isset($_POST['user'])) { echo $_POST['user']; } ?>" />
					<label for="email" ><?php i18n('LABEL_EMAIL'); ?>:</label><input class="text" name="email" id="email" type="text" value="<?php if(isset($_POST['email'])) { echo $_POST['email']; } ?>" />
					</p>
				</div>
				<div class="clear"></div>
				<p><input class="submit" type="submit" name="submitted" value="<?php i18n('LABEL_INSTALL'); ?>" /></p>
			</form>
	</div>
	
</div>

<div class="clear"></div>
<?php get_template('footer'); ?>