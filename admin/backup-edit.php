<?php
/**
 * Edit Backups
 *
 * View the current backup of a given page
 *
 * @package RAT6
 * @subpackage Backups
 */
 
# setup
$load['plugin'] = true;
include('inc/common.php');
$userid = login_cookie_check();

# get page url to display
if ($_GET['id'] != '') {
	$id = $_GET['id'];
	$file = $id .".bak.xml";
	$path = RATBACKUPSPATH .'pages/';
	
	$data = getXML($path . $file);
	$title = htmldecode($data->title);
	$pubDate = $data->pubDate;
	$parent = $data->parent;
	$metak = htmldecode($data->meta);
	$metad = htmldecode($data->metad);
	$url = $data->url;
	$content = htmldecode($data->content);
	$private = $data->private;
	$template = $data->template;
	$menu = htmldecode($data->menu);
	$menuStatus = $data->menuStatus;
	$menuOrder = $data->menuOrder;
} else {
	redirect('backups.php?upd=bak-err');
}

if ($private != '' ) { $private = '<span style="color:#cc0000">('.i18n_r('PRIVATE_SUBTITLE').')</span>'; } else { $private = ''; }
if ($menuStatus == '' ) { $menuStatus = i18n_r('NO'); } else { $menuStatus = i18n_r('YES'); }

// are we going to do anything with this backup?
if ($_GET['p'] != '') {
	$p = $_GET['p'];
} else {
	redirect('backups.php?upd=bak-err');
}

if ($p == 'delete') {
	$nonce = $_GET['nonce'];
	if(!check_nonce($nonce, "delete", "backup-edit.php")) {
		die("CSRF detected!");
	}

	delete_bak($id);
	redirect("backups.php?upd=bak-success&id=".$id);
} 

elseif ($p == 'restore') {
	$nonce = $_GET['nonce'];
	if(!check_nonce($nonce, "restore", "backup-edit.php")) {
		die("CSRF detected!");	
	}
	restore_bak($id);
	redirect("edit.php?id=". $id ."&upd=edit-success&type=restore");
}
?>

<?php get_template('header', cl($SITENAME).' &raquo; '. i18n_r('BAK_MANAGEMENT').' &raquo; '.i18n_r('VIEWPAGE_TITLE')); ?>
	
	<h1><a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?></a> <span>&raquo;</span> <?php i18n('BAK_MANAGEMENT'); ?> <span>&raquo;</span> <?php i18n('VIEWING');?> &lsquo;<span class="filename" ><?php echo $url; ?></span>&rsquo;</h1>
	
	<?php include('template/include-nav.php'); ?>
	<?php include('template/error_checking.php'); ?>
	<div class="bodycontent">
	
	<div id="maincontent">
		<div class="main" >
		<h3 class="floated"><?php i18n('BACKUP_OF');?> &lsquo;<em><?php echo $url; ?></em>&rsquo;</h3>
		
		<div class="edit-nav" >
			 <a href="backup-edit.php?p=restore&amp;id=<?php echo $id; ?>&amp;nonce=<?php echo get_nonce("restore", "backup-edit.php"); ?>" accesskey="<?php echo find_accesskey(i18n_r('ASK_RESTORE'));?>" ><?php i18n('ASK_RESTORE');?></a> <a href="backup-edit.php?p=delete&amp;id=<?php echo $id; ?>&amp;nonce=<?php echo get_nonce("delete", "backup-edit.php"); ?>" title="<?php i18n('DELETEPAGE_TITLE'); ?>: <?php echo $title; ?>?" id="delback" accesskey="<?php echo find_accesskey(i18n_r('ASK_DELETE'));?>" class="delconfirm" ><?php i18n('ASK_DELETE');?></a>
			<div class="clear"></div>
		</div>
		
		<table class="simple highlight" >
		<tr><td style="width:125px;" ><b><?php i18n('PAGE_TITLE');?>:</b></td><td><b><?php echo cl($title); ?></b> <?php echo $private; ?></td></tr>
		<tr><td><b><?php i18n('BACKUP_OF');?>:</b></td><td>
			<?php 
			if(isset($id)) {
					echo '<a target="_blank" href="'. find_url($url, $parent) .'">'. find_url($url, $parent) .'</a>'; 
			} 
			?>
		</td></tr>
		<tr><td><b><?php i18n('DATE');?>:</b></td><td><?php echo lngDate($pubDate); ?></td></tr>
		<tr><td><b><?php i18n('TAG_KEYWORDS');?>:</b></td><td><em><?php echo $metak; ?></em></td></tr>
		<tr><td><b><?php i18n('META_DESC');?>:</b></td><td><em><?php echo $metad; ?></em></td></tr>
		<tr><td><b><?php i18n('MENU_TEXT');?>:</b></td><td><?php echo $menu; ?></td></tr>
		<tr><td><b><?php i18n('PRIORITY');?>:</b></td><td><?php echo $menuOrder; ?></td></tr>
		<tr><td><b><?php i18n('ADD_TO_MENU');?></b></td><td><?php echo $menuStatus; ?></td></tr>
		</table>
		
		<textarea id="codetext" wrap='off' style="background:#f4f4f4;padding:4px;width:635px;color:#444;border:1px solid #666;" readonly ><?php echo strip_decode($content); ?></textarea>

		</div>
		
		<?php if ($HTMLEDITOR != '') { 
			if (defined('RATEDITORHEIGHT')) { $EDHEIGHT = RATEDITORHEIGHT .'px'; } else {	$EDHEIGHT = '500px'; }
			if (defined('RATEDITORLANG')) { $EDLANG = RATEDITORLANG; } else {	$EDLANG = 'en'; }
		?>
		<script type="text/javascript" src="template/js/ckeditor/ckeditor.js"></script>
		<script type="text/javascript">
		var editor = CKEDITOR.replace( 'codetext', {
			skin : 'RAT6',
			language : '<?php echo $EDLANG; ?>',
			defaultLanguage : '<?php echo $EDLANG; ?>',
			<?php if (file_exists(RATTHEMESPATH .$TEMPLATE."/editor.css")) { 
				$fullpath = suggest_site_path();
			?>
			contentsCss: '<?php echo $fullpath; ?>theme/<?php echo $TEMPLATE; ?>/editor.css',
			<?php } ?>
			entities : true,
			uiColor : '#FFFFFF',
			height: '<?php echo $EDHEIGHT; ?>',
			baseHref : '<?php echo $SITEURL; ?>',
			toolbar : [['Source']],
			removePlugins: 'image,link,elementspath,resize'
		});
		// set editor to read only mode
		editor.on('mode', function (ev) {
			if (ev.editor.mode == 'source') {
				$('#cke_contents_codetext .cke_source').attr("readonly", "readonly");
			}
			else {
				var bodyelement = ev.editor.document.$.body;
				bodyelement.setAttribute("contenteditable", false);
			}		
		});
		</script>
		
		<?php } ?>
		
	</div>
	
	<div id="sidebar" >
		<?php include('template/sidebar-backups.php'); ?>
	</div>
	
	<div class="clear"></div>
	</div>
<?php get_template('footer'); ?>
