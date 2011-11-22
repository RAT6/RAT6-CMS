<?php
/**
 * All Backups
 *
 * Displays all available page backups. 	
 *
 * @package RAT6
 * @subpackage Backups
 * @link http://www.facebook.com/pages/RAT6/196237143792761/docs/restore-page-backup
 */
 
// Setup inclusions
$load['plugin'] = true;

// Include common.php
include('inc/common.php');
	
// Variable settinRAT
login_cookie_check();
$path = RATBACKUPSPATH.'pages/';
$counter = '0';
$table = '';


// delete all backup files if the ?deleteall session parameter is set
if (isset($_GET['deleteall'])){
	$nonce = $_GET['nonce'];
	if(!check_nonce($nonce, "deleteall")) {
		die("CSRF detected!");	
	}
	
	$filenames = getFiles($path);
	
	foreach ($filenames as $file) {
		if (file_exists($path . $file) ) {
			if (isFile($file, $path, 'bak')) {
				unlink($path . $file);
			}
		}
	}
	
	$success = i18n_r('ER_FILE_DEL_SUC');
}


//display all page backups
$filenames = getFiles($path);
$count="0";
$pagesArray = array();

if (count($filenames) != 0) 
{ 
	foreach ($filenames as $file) 
	{
		if (isFile($file, $path, 'bak')) 
		{
			$data = getXML($path .$file);
			$status = $data->menuStatus;
			$pagesArray[$count]['title'] = html_entity_decode($data->title, ENT_QUOTES, 'UTF-8');
			$pagesArray[$count]['url'] = $data->url;
			$pagesArray[$count]['date'] = $data->pubDate;
			$count++;
		}
	}
	$pagesSorted = subval_sort($pagesArray,'title');
}

if (count($pagesSorted) != 0) 
{ 
	foreach ($pagesSorted as $page) 
	{					
		$counter++;
		$table .= '<tr id="tr-'.$page['url'] .'" >';
		
		if ($page['title'] == '' ) { $page['title'] = '[No Title] &nbsp;&raquo;&nbsp; <em>'. $page['url'] .'</em>'; }
		
		$table .= '<td class="pagetitle"><a title="'.i18n_r('VIEWPAGE_TITLE').' '. cl($page['title']) .'" href="backup-edit.php?p=view&amp;id='. $page['url'] .'">'. cl($page['title']) .'</a></td>';
		$table .= '<td style="width:80px;text-align:right;" ><span>'. shtDate($page['date']) .'</span></td>';
		$table .= '<td class="delete" ><a class="delconfirm" title="'.i18n_r('DELETEPAGE_TITLE').' '. cl($page['title']) .'?" href="backup-edit.php?p=delete&amp;id='. $page['url'] .'&amp;nonce='.get_nonce("delete", "backup-edit.php").'">X</a></td>';
		$table .= '</tr>';
	}
}	
?>

<?php get_template('header', cl($SITENAME).' &raquo; '.i18n_r('BAK_MANAGEMENT')); ?>
	
	<h1><a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?></a> <span>&raquo;</span> <?php i18n('BAK_MANAGEMENT'); ?> <span>&raquo;</span> <?php i18n('ALL_PAGES'); ?></h1>
	
	<?php include('template/include-nav.php'); ?>
	<?php include('template/error_checking.php'); ?>
	
	<div class="bodycontent">
	
	<div id="maincontent">
		<div class="main" >
			<h3 class="floated"><?php i18n('PAGE_BACKUPS');?></h3>
			
			<div class="edit-nav clearfix" ><a href="#" id="filtertable" ><?php i18n('FILTER'); ?></a> <a href="backups.php?deleteall&amp;nonce=<?php echo get_nonce("deleteall"); ?>" title="<?php i18n('DELETE_ALL_BAK');?>" accesskey="<?php echo find_accesskey(i18n_r('ASK_DELETE_ALL'));?>" class="confirmation"  ><?php i18n('ASK_DELETE_ALL');?></a></div>
			<div id="filter-search">
				<form><input type="text" autocomplete="off" class="text" id="q" placeholder="<?php echo lowercase(i18n_r('FILTER')); ?>..." /> &nbsp; <a href="pages.php" class="cancel"><?php i18n('CANCEL'); ?></a></form>
			</div>
			<table id="editpages" class="highlight paginate">
				<tr><th><?php i18n('PAGE_TITLE'); ?></th><th style="text-align:right;" ><?php i18n('DATE'); ?></th><th></th></tr>
				<?php echo $table; ?>
			</table>
			<div id="page_counter" class="qc_pager"></div> 
			<p><em><b><span id="pg_counter"><?php echo $counter; ?></span></b> <?php i18n('TOTAL_BACKUPS');?></em></p>
		</div>
	</div>
	
	<div id="sidebar" >
		<?php include('template/sidebar-backups.php'); ?>
	</div>
	
	<div class="clear"></div>
	</div>

<?php get_template('footer'); ?>