<?php 
/**
 * Health Check
 *
 * Displays the status and health check of your installation	
 *
 * @package GetSimple
 * @subpackage Support
 */

// Setup inclusions
$load['plugin'] = true;

// Include common.php
include('inc/common.php');
login_cookie_check();
$php_modules = get_loaded_extensions();

?>

<?php get_template('header', cl($SITENAME).' &raquo; '.i18n_r('SUPPORT').' &raquo; '.i18n_r('WEB_HEALTH_CHECK')); ?>
	
	<h1><a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?></a> <span>&raquo;</span> <?php i18n('SUPPORT');?> <span>&raquo;</span> <?php i18n('WEB_HEALTH_CHECK');?></h1>
	<?php include('template/include-nav.php'); ?>
	<?php include('template/error_checking.php'); ?>

<div class="bodycontent">
	
	<div id="maincontent">
		<div class="main">
			<h3><?php echo $site_full_name; ?> <?php i18n('VERSION');?></h3>
			<table class="highlight healthcheck">
				<?php
				if (in_arrayi('curl', $php_modules))
				{
					$curl_URL = $api_url .'?v='.$site_version_no;
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_TIMEOUT, 2);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_URL, $curl_URL);
					$data = curl_exec($ch);
					curl_close($ch);
					if ($data !== false) {
						$apikey = json_decode($data);
						$verstatus = $apikey->status;
					} else {
						$apikey = null;
						$verstatus = null;
					}
				} else {
					$verstatus = '10';
				}
				
				if ($verstatus == '0') {
					$ver = '<span class="ERRmsg" >'. i18n_r('UPG_NEEDED').' <b>'.$apikey->latest .'</b><br /><a href="http://get-simple.info/download/">'. i18n_r('DOWNLOAD').'</a></span>';
				} elseif ($verstatus == '1') {
					$ver = '<span class="OKmsg" ><b>'.$site_version_no.'</b> - '. i18n_r('LATEST_VERSION').'</span>';
				} elseif ($verstatus == '2') {
					$ver = '<span class="WARNmsg" ><b>'.$site_version_no.'</b> - '. i18n_r('BETA').'</span>';
				} else {
					$ver = '<span class="WARNmsg" >'. i18n_r('CANNOT_CHECK').' <b>'.$site_version_no.'</b><br /><a href="http://get-simple.info/download">'. i18n_r('DOWNLOAD').'</a></span>';
				}
				?>
				<tr><td style="width:445px;" ><?php echo $site_full_name; ?> <?php i18n('VERSION');?></td><td><?php echo $ver; ?></td></tr>
			</table>
			
			<h3><?php i18n('SERVER_SETUP');?></h3>
			<table class="highlight healthcheck">
				<tr><td style="width:445px;" >
				<?php
					if (version_compare(PHP_VERSION, "5.2", "<")) {
						echo 'PHP '.i18n_r('VERSION').'</td><td><span class="ERRmsg" ><b>'. PHP_VERSION.'</b> - PHP 5.2 '.i18n_r('OR_GREATER_REQ').' - '.i18n_r('ERROR').'</span></td></tr>';
					} else {
						echo 'PHP '.i18n_r('VERSION').'</td><td><span class="OKmsg" ><b>'. PHP_VERSION.'</b> - '.i18n_r('OK').'</span></td></tr>';
					}

					if  (in_arrayi('curl', $php_modules) ) {
						echo '<tr><td>cURL Module</td><td><span class="OKmsg" >'.i18n_r('INSTALLED').' - '.i18n_r('OK').'</span></td></tr>';
					} else{
						echo '<tr><td>cURL Module</td><td><span class="WARNmsg" >'.i18n_r('NOT_INSTALLED').' - '.i18n_r('WARNING').'</span></td></tr>';
					}
					if  (in_arrayi('gd', $php_modules) ) {
						echo '<tr><td>GD Library</td><td><span class="OKmsg" >'.i18n_r('INSTALLED').' - '.i18n_r('OK').'</span></td></tr>';
					} else{
						echo '<tr><td>GD Library</td><td><span class="WARNmsg" >'.i18n_r('NOT_INSTALLED').' - '.i18n_r('WARNING').'</span></td></tr>';
					}
					if  (in_arrayi('zip', $php_modules) ) {
						echo '<tr><td>ZipArchive</td><td><span class="OKmsg" >'.i18n_r('INSTALLED').' - '.i18n_r('OK').'</span></td></tr>';
					} else{
						echo '<tr><td>ZipArchive</td><td><span class="WARNmsg" >'.i18n_r('NOT_INSTALLED').' - '.i18n_r('WARNING').'</span></td></tr>';
					}
					if (! in_arrayi('SimpleXML', $php_modules) ) {
						echo '<tr><td>SimpleXML Module</td><td><span class="ERRmsg" >'.i18n_r('NOT_INSTALLED').' - '.i18n_r('ERROR').'</span></td></tr>';
					} else {
						echo '<tr><td>SimpleXML Module</td><td><span class="OKmsg" >'.i18n_r('INSTALLED').' - '.i18n_r('OK').'</span></td></tr>';
					}

					if ( function_exists('apache_get_modules') ) {
						if(! in_arrayi('mod_rewrite',apache_get_modules())) {
							echo '<tr><td>Apache Mod Rewrite</td><td><span class="WARNmsg" >'.i18n_r('NOT_INSTALLED').' - '.i18n_r('WARNING').'</span></td></tr>';
						} else {
							echo '<tr><td>Apache Mod Rewrite</td><td><span class="OKmsg" >'.i18n_r('INSTALLED').' - '.i18n_r('OK').'</span></td></tr>';
						}
					} else {
						echo '<tr><td>Apache Mod Rewrite</td><td><span class="OKmsg" >'.i18n_r('INSTALLED').' - '.i18n_r('OK').'</span></td></tr>';
					}

	?>
			</table>
			<p class="hint"><?php echo sprintf(i18n_r('REQS_MORE_INFO'), "http://get-simple.info/wiki/installation:requirements"); ?></p>
			
			<h3><?php i18n('DATA_FILE_CHECK');?></h3>
			<table class="highlight healthcheck">
				<?php 
						$path = RATDATAPAGESPATH;
						$data = getFiles($path);
						sort($data);
						foreach($data as $file) {
							if( isFile($file, $path) ) {
								echo '<tr><td style="width:445px;" >/data/pages/' . $file .'</td><td>' . valid_xml($path . $file) .'</td></tr>';
							}							
						}

						$path = RATDATAOTHERPATH;
						$data = getFiles($path);
						sort($data);
						foreach($data as $file) {
							if( isFile($file, $path) ) {
								echo '<tr><td>/data/other/' . $file .'</td><td>' . valid_xml($path . $file) .'</td></tr>';
							}							
						}

						$path = RATDATAOTHERPATH.'loRAT/';
						$data = getFiles($path);
						sort($data);
						foreach($data as $file) {
							if( isFile($file, $path, '.log') ) {
								echo '<tr><td>/data/other/loRAT/' . $file .'</td><td>' . valid_xml($path . $file) .'</td></tr>';
							}							
						}

						$path = RATBACKUPSPATH.'other/';
						$data = getFiles($path);
						sort($data);
						foreach($data as $file) {
							if( isFile($file, $path) ) {
								echo '<tr><td>/backups/other/' . $file .'</td><td>' . valid_xml($path . $file) .'</td></tr>';
							}							
						}
						
						$path = RATBACKUPSPATH.'users/';
						$data = getFiles($path);
						sort($data);
						foreach($data as $file) {
							if( isFile($file, $path) ) {
								echo '<tr><td>/backups/users/' . $file .'</td><td>' . valid_xml($path . $file) .'</td></tr>';
							}							
						}

						$path = RATBACKUPSPATH.'pages/';
						$data = getFiles($path);
						sort($data);
						foreach($data as $file) {
							if( isFile($file, $path) ) {
								echo '<tr><td>/backups/pages/' . $file .'</td><td>' . valid_xml($path . $file) .'</td></tr>';
							}							
						}
				?>
			</table>
			
			<h3><?php i18n('DIR_PERMISSIONS');?></h3>
			<table class="highlight healthcheck">
				<?php $me = check_perms(RATDATAPAGESPATH); ?><tr><td style="width:445px;" >/data/pages/</td><td><?php if( $me >= '0755' ) { echo '<span class="OKmsg" >'. $me .' '.i18n_r('WRITABLE').' - '.i18n_r('OK').'</span>'; } else { echo '<span class="ERRmsg" >'. $me .' '.i18n_r('NOT_WRITABLE').' - '.i18n_r('ERROR').'!</span>'; } ?></td></tr>
				<?php $me = check_perms(RATDATAOTHERPATH); ?><tr><td>/data/other/</td><td><?php if( $me >= '0755' ) { echo '<span class="OKmsg" >'. $me .' '.i18n_r('WRITABLE').' - '.i18n_r('OK').'</span>'; } else { echo '<span class="ERRmsg" >'. $me .' '.i18n_r('NOT_WRITABLE').' - '.i18n_r('ERROR').'!</span>'; } ?></td></tr>
				<?php $me = check_perms(RATDATAOTHERPATH.'loRAT/'); ?><tr><td>/data/other/loRAT/</td><td><?php if( $me >= '0755' ) { echo '<span class="OKmsg" >'. $me .' '.i18n_r('WRITABLE').' - '.i18n_r('OK').'</span>'; } else { echo '<span class="ERRmsg" >'. $me .' '.i18n_r('NOT_WRITABLE').' - '.i18n_r('ERROR').'!</span>'; } ?></td></tr>
				<?php $me = check_perms(RATTHUMBNAILPATH); ?><tr><td>/data/thumbs/</td><td><?php if( $me >= '0755' ) { echo '<span class="OKmsg" >'. $me .' '.i18n_r('WRITABLE').' - '.i18n_r('OK').'</span>'; } else { echo '<span class="ERRmsg" >'. $me .' '.i18n_r('NOT_WRITABLE').' - '.i18n_r('ERROR').'!</span>'; } ?></td></tr>
				<?php $me = check_perms(RATDATAUPLOADPATH); ?><tr><td>/data/uploads/</td><td><?php if( $me >= '0755' ) { echo '<span class="OKmsg" >'. $me .' '.i18n_r('WRITABLE').' - '.i18n_r('OK').'</span>'; } else { echo '<span class="ERRmsg" >'. $me .' '.i18n_r('NOT_WRITABLE').' - '.i18n_r('ERROR').'!</span>'; } ?></td></tr>
				<?php $me = check_perms(RATUSERSPATH); ?><tr><td>/data/users/</td><td><?php if( $me >= '0755' ) { echo '<span class="OKmsg" >'. $me .' '.i18n_r('WRITABLE').' - '.i18n_r('OK').'</span>'; } else { echo '<span class="ERRmsg" >'. $me .' '.i18n_r('NOT_WRITABLE').' - '.i18n_r('ERROR').'!</span>'; } ?></td></tr>
				<?php $me = check_perms(RATBACKUPSPATH.'zip/'); ?><tr><td>/backups/zip/</td><td><?php if( $me >= '0755' ) { echo '<span class="OKmsg" >'. $me .' '.i18n_r('WRITABLE').' - '.i18n_r('OK').'</span>'; } else { echo '<span class="ERRmsg" >'. $me .' '.i18n_r('NOT_WRITABLE').' - '.i18n_r('ERROR').'!</span>'; } ?></td></tr>
				<?php $me = check_perms(RATBACKUPSPATH.'pages/'); ?><tr><td>/backups/pages/</td><td><?php if( $me >= '0755' ) { echo '<span class="OKmsg" >'. $me .' '.i18n_r('WRITABLE').' - '.i18n_r('OK').'</span>'; } else { echo '<span class="ERRmsg" >'. $me .' '.i18n_r('NOT_WRITABLE').' - '.i18n_r('ERROR').'!</span>'; } ?></td></tr>
				<?php $me = check_perms(RATBACKUPSPATH.'other/'); ?><tr><td>/backups/other/</td><td><?php if( $me >= '0755' ) { echo '<span class="OKmsg" >'. $me .' '.i18n_r('WRITABLE').' - '.i18n_r('OK').'</span>'; } else { echo '<span class="ERRmsg" >'. $me .' '.i18n_r('NOT_WRITABLE').' - '.i18n_r('ERROR').'!</span>'; } ?></td></tr>
				<?php $me = check_perms(RATBACKUSERSPATH); ?><tr><td>/backups/users/</td><td><?php if( $me >= '0755' ) { echo '<span class="OKmsg" >'. $me .' '.i18n_r('WRITABLE').' - '.i18n_r('OK').'</span>'; } else { echo '<span class="ERRmsg" >'. $me .' '.i18n_r('NOT_WRITABLE').' - '.i18n_r('ERROR').'!</span>'; } ?></td></tr>
			</table>

			
			<h3><?php echo sprintf(i18n_r('EXISTANCE'), '.htaccess');?></h3>
			<table class="highlight healthcheck">
				<tr><td style="width:445px;" >/data/</td><td> 
				<?php	
					$file = RATDATAPATH.".htaccess";
					if (! file_exists($file)) {
						copy (RATADMININCPATH.'tmp/tmp.deny.htaccess', $file);
					} 
					if (! file_exists($file)) {
						echo '<span class="WARNmsg" >'.i18n_r('MISSING_FILE').' - '.i18n_r('WARNING').'</span>';
					} else {
						$res = file_get_contents($file);
						if ( !strstr($res, 'Deny from all')) {
							echo '<span class="WARNmsg" >'.i18n_r('BAD_FILE').' - '.i18n_r('WARNING').'</span>';
						} else {
							echo '<span class="OKmsg" >'.i18n_r('GOOD_D_FILE').' - '.i18n_r('OK').'</span>';
						}
					}
				?>
			</td></tr>

				<tr><td>/data/uploads/</td><td>
				<?php	
					$file = RATDATAUPLOADPATH.".htaccess";
					if (! file_exists($file)) {
						copy (RATADMININCPATH.'tmp/tmp.allow.htaccess', $file);
					} 
					if (! file_exists($file)) {
						echo ' <span class="WARNmsg" >'.i18n_r('MISSING_FILE').' - '.i18n_r('WARNING').'</span>';
					} else {
						$res = file_get_contents($file);
						if ( !strstr($res, 'Allow from all')) {
							echo ' <span class="WARNmsg" >'.i18n_r('BAD_FILE').' - '.i18n_r('WARNING').'</span>';
						} else {
							echo ' <span class="OKmsg" >'.i18n_r('GOOD_A_FILE').' - '.i18n_r('OK').'</span>';
						}
					}
				?>
				</td></tr>
				
				<tr><td>/data/thumbs/</td><td> 
				<?php	
					$file = RATTHUMBNAILPATH.".htaccess";
					if (! file_exists($file)) {
						copy (RATADMININCPATH.'tmp/tmp.allow.htaccess', $file);
					} 
					if (! file_exists($file)) {
						echo ' <span class="WARNmsg" >'.i18n_r('MISSING_FILE').' - '.i18n_r('WARNING').'</span>';
					} else {
						$res = file_get_contents($file);
						if ( !strstr($res, 'Allow from all')) {
							echo ' <span class="WARNmsg" >'.i18n_r('BAD_FILE').' - '.i18n_r('WARNING').'</span>';
						} else {
							echo ' <span class="OKmsg" >'.i18n_r('GOOD_A_FILE').' - '.i18n_r('OK').'</span>';
						}
					}
				?>
				</td></tr>
				
				<tr><td>/data/pages/</td><td>
				<?php	
					$file = RATDATAPAGESPATH.".htaccess";
					if (! file_exists($file)) {
						copy (RATADMININCPATH.'tmp/tmp.deny.htaccess', $file);
					} 
					if (! file_exists($file)) {
						echo ' <span class="WARNmsg" >'.i18n_r('MISSING_FILE').' - '.i18n_r('WARNING').'</span>';
					} else {
						$res = file_get_contents($file);
						if ( !strstr($res, 'Deny from all')) {
							echo ' <span class="WARNmsg" >'.i18n_r('BAD_FILE').' - '.i18n_r('WARNING').'</span>';
						} else {
							echo ' <span class="OKmsg" >'.i18n_r('GOOD_D_FILE').' - '.i18n_r('OK').'</span>';
						}
					}
				?>
				</td></tr>
				
				<tr><td>/plugins/</td><td>
				<?php	
					$file = RATPLUGINPATH.".htaccess";
					if (! file_exists($file)) {
						copy (RATADMININCPATH.'tmp/tmp.deny.htaccess', $file);
					} 
					if (! file_exists($file)) {
						echo ' <span class="WARNmsg" >'.i18n_r('MISSING_FILE').' - '.i18n_r('WARNING').'</span>';
					} else {
						$res = file_get_contents($file);
						if ( !strstr($res, 'Deny from all')) {
							echo ' <span class="WARNmsg" >'.i18n_r('BAD_FILE').' - '.i18n_r('WARNING').'</span>';
						} else {
							echo ' <span class="OKmsg" >'.i18n_r('GOOD_D_FILE').' - '.i18n_r('OK').'</span>';
						}
					}
				?>
				</td></tr>
				
				<tr><td>/data/other/</td><td> 
				<?php	
					$file = RATDATAOTHERPATH.".htaccess";
					if (! file_exists($file)) {
						copy (RATADMININCPATH.'tmp/tmp.deny.htaccess', $file);
					} 
					if (! file_exists($file)) {
						echo ' <span class="WARNmsg" >'.i18n_r('MISSING_FILE').' - '.i18n_r('WARNING').'</span>';
					} else {
						$res = file_get_contents($file);
						if ( !strstr($res, 'Deny from all')) {
							echo ' <span class="WARNmsg" >'.i18n_r('BAD_FILE').' - '.i18n_r('WARNING').'</span>';
						} else {
							echo ' <span class="OKmsg" >'.i18n_r('GOOD_D_FILE').' - '.i18n_r('OK').'</span>';
						}
					}
				?>
				</td></tr>

				<tr><td>/data/other/loRAT/</td><td>
				<?php	
					$file = RATDATAOTHERPATH."loRAT/.htaccess";
					if (! file_exists($file)) {
						copy (RATADMININCPATH.'tmp/tmp.deny.htaccess', $file);
					} 
					if (! file_exists($file)) {
						echo ' <span class="WARNmsg" >'.i18n_r('MISSING_FILE').' - '.i18n_r('WARNING').'</span>';
					} else {
						$res = file_get_contents($file);
						if ( !strstr($res, 'Deny from all')) {
							echo ' <span class="WARNmsg" >'.i18n_r('BAD_FILE').' - '.i18n_r('WARNING').'</span>';
						} else {
							echo ' <span class="OKmsg" >'.i18n_r('GOOD_D_FILE').' - '.i18n_r('OK').'</span>';
						}
					}
				?>
				</td></tr>
				
				<tr><td>/theme/</td><td>
				<?php	
					$file = RATTHEMESPATH.".htaccess";
					if (file_exists($file)) {
						unlink($file);
					} 
					if (file_exists($file)) {
						echo ' <span class="ERRmsg" >'.i18n_r('CANNOT_DEL_FILE').' - '.i18n_r('ERROR').'</span>';
					} else {
						echo ' <span class="OKmsg" >'.i18n_r('NO_FILE').' - '.i18n_r('OK').'</span>';
					}
				?>
				</td></tr>
				<?php exec_action('healthcheck-extras'); ?>
			</table>
	</div>
		
	</div>
	
	<div id="sidebar" >
		<?php include('template/sidebar-support.php'); ?>
	</div>	
	
	<div class="clear"></div>
	</div>
<?php get_template('footer'); ?>