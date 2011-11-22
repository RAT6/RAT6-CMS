<?php 
/**
 * Images
 *
 * Displays information on the passed image
 *
 * @package GetSimple
 * @subpackage Images
 */

// Setup inclusions
$load['plugin'] = true;

// Include common.php
include('inc/common.php');

// Variable SettinRAT
login_cookie_check();

$subPath = (isset($_GET['path'])) ? $_GET['path'] : "";
if ($subPath != '') $subPath = tsl($subPath);

$src = strippath($_GET['i']);
$thumb_folder = RATTHUMBNAILPATH.$subPath;
$src_folder = '../data/uploads/';
$thumb_folder_rel = '../data/thumbs/'.$subPath;
if (!is_file($src_folder . $subPath .$src)) redirect("upload.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	
	require('inc/imagemanipulation.php');
	
	$objImage = new ImageManipulation($src_folder . $subPath .$src);
	if ( $objImage->imageok ) 
	{
		$objImage->setCrop($_POST['x'], $_POST['y'], $_POST['w'], $_POST['h']);
		//$objImage->show();
		$objImage->save($thumb_folder . 'thumbnail.' .$src);
		$success = i18n_r('THUMB_SAVED');
	} 
	else 
	{
		echo 'Error!';
	}
}

list($imgwidth, $imgheight, $imgtype, $imgattr) = getimagesize($src_folder .$subPath. urlencode($src));

if (file_exists($thumb_folder . 'thumbnail.' . $src)) {
	list($thwidth, $thheight, $thtype, $athttr) = getimagesize($thumb_folder . urlencode('thumbnail.'.$src));
	$thumb_exists = ' &nbsp; | &nbsp; <a href="'.$thumb_folder_rel . 'thumbnail.'. $src .'" rel="facybox" >'.i18n_r('CURRENT_THUMBNAIL').'</a> <code>'.$thwidth.'x'.$thheight.'</code>';
}
?>

<?php get_template('header', cl($SITENAME).' &raquo; '.i18n_r('FILE_MANAGEMENT').' &raquo; '.i18n_r('IMAGES')); ?>
	
	<h1><a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?></a> <span>&raquo;</span> <?php i18n('FILE_MANAGEMENT');?> <span>&raquo;</span> <?php i18n('IMAGES');?></h1>
	<?php include('template/include-nav.php'); ?>
	<?php include('template/error_checking.php'); ?>

<div class="bodycontent">
	<div id="maincontent">
			
		<div class="main">
		<h3><?php i18n('IMG_CONTROl_PANEL');?></h3>
	
			<?php echo '<p><a href="'.$src_folder . $subPath .$src .'" rel="facybox" >'.i18n_r('ORIGINAL_IMG').'</a> <code>'.$imgwidth.'x'.$imgheight .'</code>'. $thumb_exists .'</p>'; ?>

			<form>
				<select class="text" id="img-info" style="width:50%" >
					<option selected="selected" value="code-img-html" ><?php i18n('HTML_ORIG_IMG');?></option>
					<option value="code-img-link" ><?php i18n('LINK_ORIG_IMG');?></option>
					<option value="code-thumb-html" ><?php i18n('HTML_THUMBNAIL');?></option>
					<option value="code-thumb-link" ><?php i18n('LINK_THUMBNAIL');?></option>
					<option value="code-imgthumb-html" ><?php i18n('HTML_THUMB_ORIG');?></option>
				</select>
				<textarea class="copykit" >&lt;img src="<?php echo tsl($SITEURL) .'data/uploads/'. $subPath. $src; ?>" class="RAT_image" alt=""></textarea>
				<p style="color:#666;font-size:11px;margin:-10px 0 0 0"><a href="#" class="select-all" ><?php i18n('CLIPBOARD_INSTR');?></a></p>
			</form>
			<div class="toggle">
				<p id="code-img-html">&lt;img src="<?php echo tsl($SITEURL) .'data/uploads/'. $subPath. $src; ?>" class="RAT_image" alt=""></p>
				<p id="code-img-link"><?php echo tsl($SITEURL) .'data/uploads/'. $subPath. $src; ?></p>
				<p id="code-thumb-html">&lt;img src="<?php echo tsl($SITEURL) .'data/thumbs/'.$subPath.'thumbnail.'. $src; ?>" class="RAT_image RAT_thumb" alt=""></p>
				<p id="code-thumb-link"><?php echo tsl($SITEURL) .'data/thumbs/'.$subPath.'thumbnail.'.$src; ?></p>
				<p id="code-imgthumb-html">&lt;a href="<?php echo tsl($SITEURL) .'data/uploads/'. $subPath. $src; ?>" class="RAT_image_link" >&lt;img src="<?php echo tsl($SITEURL) .'data/thumbs/'.$subPath.'thumbnail.'.$src; ?>" class="RAT_thumb" alt="" />&lt;/a></p>
			</div>
	</div>
	
	<div id="jcrop_open" class="main">

    <img src="<?php echo $src_folder . $subPath.$src; ?>" id="cropbox" style="max-width:585px;"/>
    

		<div id="handw" class="toggle" ><?php i18n('SELECT_DIMENTIONS'); ?><br /><span id="picw"></span> x <span id="pich"></span></div>
 
    <!-- This is the form that our event handler fills -->
    <form id="jcropform" action="<?php myself(); ?>?i=<?php echo $src; ?>&path=<?php echo $subPath; ?>" method="post" onsubmit="return checkCoords();">
      <input type="hidden" id="x" name="x" />
      <input type="hidden" id="y" name="y" />
      <input type="hidden" id="w" name="w" />
      <input type="hidden" id="h" name="h" />
      <input type="submit" class="submit" value="<?php i18n('CREATE_THUMBNAIL');?>" /> &nbsp; <span style="color:#666;font-size:11px;"><?php i18n('CROP_INSTR_NEW');?></span>

    </form>

		</div>
	
	</div>
	
	<div id="sidebar" >
		<?php include('template/sidebar-files.php'); ?>
	</div>	
	
	<div class="clear"></div>
	
	<script language="Javascript">
	  jQuery(document).ready(function() { 
	    		
			$(window).load(function(){
			var api = $.Jcrop('#cropbox',{
		    onChange: updateCoords,
		    onSelect: updateCoords,
		    boxWidth: 585, 
		    boxHeight: 500
		  }); 
		  var isCtrl = false;
			$(document).keyup(function (e) {
				api.setOptions({ aspectRatio: 0 });
				api.focus();
				if(e.which == 17) isCtrl=false;
			}).keydown(function (e) {
				if(e.which == 17) isCtrl=true;
				if(e.which == 66 && isCtrl == true) {
					api.setOptions({ aspectRatio: 1 });
					api.focus();
				}
			});
		});
		
	});
	</script>
	
	</div>
<?php get_template('footer'); ?>
