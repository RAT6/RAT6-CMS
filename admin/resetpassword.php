<?php 
/**
 * Reset Password
 *
 * Resets the password for GetSimple control panel access
 *
 * @package GetSimple
 * @subpackage Login
 */

# setup inclusions
$load['plugin'] = true;
include('inc/common.php');

if(isset($_POST['submitted'])){
	
	# check for csrf
	$nonce = $_POST['nonce'];
	if(!check_nonce($nonce, "reset_password")) {
		die("CSRF detected!");
	}
	
	if(isset($_POST['username']))	{

		# user filename
		$file = _id($_POST['username']).'.xml';
		
		# get user information from existing XML file
		if (file_exists(RATUSERSPATH . $file)) {
			$data = getXML(RATUSERSPATH . $file);
			$USR = strtolower($data->USR);
			$EMAIL = $data->EMAIL;
			
			if(strtolower($_POST['username']) == $USR) {
				# create new random password
				$random = createRandomPassword();
				
				# create backup
				createBak($file, RATUSERSPATH, RATBACKUSERSPATH);
				
				# create password change trigger file
				$flagfile = RATUSERSPATH . _id($USR).".xml.reset";
				copy(RATUSERSPATH . $file, $flagfile);
				
				# resave new user xml file
				$xml = new SimpleXMLElement('<item></item>');
				$xml->addChild('USR', $data->USR);
				$xml->addChild('PWD', passhash($random));
				$xml->addChild('EMAIL', $data->EMAIL);
				$xml->addChild('HTMLEDITOR', $data->HTMLEDITOR);
				$xml->addChild('PRETTYURLS', $data->PRETTYURLS);
				$xml->addChild('PERMALINK', $data->PERMALINK);
				$xml->addChild('TIMEZONE', $data->TIMEZONE);
				$xml->addChild('LANG', $data->LANG);
				XMLsave($xml, RATUSERSPATH . $file);
				
				# send the email with the new password
				$subject = $site_full_name .' '. i18n_r('RESET_PASSWORD') .' '. i18n_r('ATTEMPT');
				$message = "'". cl($SITENAME) ."' ". i18n_r('RESET_PASSWORD') ." ". i18n_r('ATTEMPT');
				$message .= '<br>-------------------------------------------------------<br>';
				$message .= "<br>". i18n_r('LABEL_USERNAME').": ". $USR;
				$message .= "<br>". i18n_r('NEW_PASSWORD').": ". $random;
				$message .= '<br><br>'. i18n_r('EMAIL_LOGIN') .': <a href="'.$SITEURL . $RATADMIN.'/">'.$SITEURL . $RATADMIN.'/</a>';
				exec_action('resetpw-success');
				$status = sendmail($EMAIL,$subject,$message);
				
				# show the result of the reset attempt
				redirect("resetpassword.php?upd=pwd-".$status);
			} else{
				
				# username doesnt match listed xml username
				exec_action('resetpw-error');
				redirect("resetpassword.php?upd=pwd-error");
			} 
		} else {
			# no user exists for this username, but do not show this to the submitter		
		}
	} else {
		
		# no username was submitted
		redirect("resetpassword.php?upd=pwd-error");
	}
} 
?>

<?php get_template('header', cl($SITENAME).' &raquo; '.i18n_r('RESET_PASSWORD')); ?>
</div>
</div>
<div class="wrapper">
	
<?php include('template/error_checking.php'); ?>

<div id="maincontent">
	<div class="main" >
	
	<h3><?php i18n('RESET_PASSWORD'); ?></h3>
	<p class="desc"><?php i18n('MSG_PLEASE_EMAIL'); ?></p>
	
	<form class="login" action="<?php myself(); ?>" method="post" >
		<input name="nonce" id="nonce" type="hidden" value="<?php echo get_nonce("reset_password");?>"/>
		<p><b><?php i18n('LABEL_USERNAME'); ?>:</b><br /><input class="text" name="username" type="text" value="" /></p>
		<p><input class="submit" type="submit" name="submitted" value="<?php echo i18n('SEND_NEW_PWD'); ?>" /></p>
	</form>
	<p class="cta" ><b>&laquo;</b> <a href="<?php echo $SITEURL; ?>"><?php i18n('BACK_TO_WEBSITE'); ?></a> &nbsp; | &nbsp; <a href="index.php"><?php i18n('CONTROL_PANEL'); ?></a></p>
	</div>
	
</div>

<div class="clear"></div>
<?php get_template('footer'); ?>