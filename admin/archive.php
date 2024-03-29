<?php 
/**
 * Archive
 *
 * Displays and starts the website archives 	
 *
 * @package RAT6
 * @subpackage Backups
 */

// Setup inclusions
$load['plugin'] = true;

// Include common.php
include('inc/common.php');

// Variable SettinRAT
login_cookie_check();
$table = '';

// if a backup needs to be created
if(isset($_GET['do'])) {
	$nonce = $_GET['nonce'];
	if(!check_nonce($nonce, "create"))
		die("CSRF detected!");	
	exec_action('archive-backup');
	redirect('zip.php?s='.$SESSIONHASH);	
}

// if a backup has just been created
if(isset($_GET['done'])) {
	$success = i18n_r('SUCC_WEB_ARCHIVE');
}

if(isset($_GET['nozip'])) {
	$error = i18n_r('NO_ZIPARCHIVE'). ' - <a href="health-check.php">'.i18n_r('WEB_HEALTH_CHECK').'</a>';
}


?> 

<?php get_template('header', cl($SITENAME).' &raquo; '.i18n_r('BAK_MANAGEMENT').' &raquo; '.i18n_r('WEBSITE_ARCHIVES')); ?>
	
	<h1><a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?></a> <span>&raquo;</span> <?php i18n('BAK_MANAGEMENT'); ?> <span>&raquo;</span> <?php i18n('WEBSITE_ARCHIVES'); ?></h1>
	
	<?php include('template/include-nav.php'); ?>
	<?php include('template/error_checking.php'); ?>

	<div class="bodycontent">
	
	<div id="maincontent">
		<div class="main" >
		<h3 class="floated"><?php i18n('WEBSITE_ARCHIVES');?></h3>
		<div class="edit-nav" >
                    <a id="waittrigger" href="archive.php?do&amp;nonce=<?php echo get_nonce("create"); ?>" accesskey="<?php echo find_accesskey(i18n_r('ASK_CREATE_ARC'));?>" title="<?php i18n('CREATE_NEW_ARC');?>" ><?php i18n('ASK_CREATE_ARC');?></a>
		<div class="clear"></div></div>
		<p style="display:none" id="waiting" ><?php i18n('CREATE_ARC_WAIT');?></p>
		<table class="highlight paginate">
			<tr><th><?php i18n('ARCHIVE_DATE'); ?></th><th style="text-align:right;" ><?php i18n('FILE_SIZE'); ?></th><th></th></tr>
		<?php
			$count="0";
			$path = tsl(RATBACKUPSPATH .'zip/');
			
			$filenames = getFiles($path);

			natsort($filenames);
			rsort($filenames);
			
			foreach ($filenames as $file) {
				if($file[0] != "." ) {
					$timestamp = explode('_', $file);
					$name = lngDate($timestamp[0]);
					clearstatcache();
					$ss = stat($path . $file);
					$size = fSize($ss['size']);
					echo '<tr>
							<td><a title="'.i18n_r('DOWNLOAD').' '. $name .'" href="download.php?file='. $path . $file .'&amp;nonce='.get_nonce("archive", "download.php").'">'.$name .'</a></td>
							<td style="width:70px;text-align:right;" ><span>'.$size.'</span></td>
							<td class="delete" ><a class="delconfirm" title="'.i18n_r('DELETE_ARCHIVE').' '. $name .'?" href="deletefile.php?zip='. $file .'&amp;nonce='.get_nonce("delete", "deletefile.php").'">X</a></td>
						  </tr>';
					$count++;
				}
			}

		?>
		</table>
		<p><em><b><?php echo $count; ?></b> <?php i18n('TOTAL_ARCHIVES');?></em></p>
		</div>
	</div>
	
	<div id="sidebar" >
		<?php include('template/sidebar-backups.php'); ?>
	</div>

	
	<div class="clear"></div>
	</div>
<?php get_template('footer'); ?>
