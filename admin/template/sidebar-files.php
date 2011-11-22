<?php
/**
 * Sidebar Files Template
 *
 * @package GetSimple
 */
 
$path = (isset($_GET['path'])) ? $_GET['path'] : "";
?>
<ul class="snav">
	<li><a href="upload.php" <?php check_menu('upload');  ?>><?php i18n('FILE_MANAGEMENT');?></a></li>
	<?php if(isset($_GET['i']) && $_GET['i'] != '') { ?><li><a href="#" class="current"><?php i18n('IMG_CONTROl_PANEL');?></a></li><?php } ?>
	
	<?php exec_action("files-sidebar"); ?>

<?php if (!defined('RATNOUPLOADIFY')) { ?>	
	<li class="upload">
		<div id="uploadify"></div>
	<?php 
	function toBytes($str){
		$val = trim($str);
		$last = strtolower($str[strlen($str)-1]);
			switch($last) {
				case 'g': $val *= 1024;
				case 'm': $val *= 1024;
				case 'k': $val *= 1024;
			}
		return $val;
	}
	// create Uploadify uploader
	$debug = (RATDEBUG == 1) ? 'true' : 'false';
	$fileSizeLimit = toBytes(ini_get('upload_max_filesize'))/1024;
	echo "
	<script type=\"text/javascript\">
	jQuery(document).ready(function() {
		if(jQuery().uploadify) {
		$('#uploadify').uploadify({
			'debug'			: ". $debug . ",
			'buttonText'	: '". i18n_r('UPLOADIFY_BUTTON') ."',
			'buttonCursor'	: 'pointer',
			'uploader'		: 'upload-uploadify.php',
			'swf'			: 'template/js/uploadify/uploadify.swf',
			'multi'			: true,
			'auto'			: true,
			'height'		: '25',
			'width'			: '100%',
			'requeueErrors'	: false,
			'fileSizeLimit'	: '".$fileSizeLimit."', // expects input in kb
			'cancelImage'	: 'template/images/cancel.png',
			'checkExisting'	: 'uploadify-check-exists.php?path=".$path."',
			'postData'		: {
				'sessionHash' : '". $SESSIONHASH ."',
				'path' : '". $path ."'
			},
			onUploadProgress: function() {
				$('#loader').show();
			},
			onUploadComplete: function() {
				$('#loader').fadeOut(500);
				$('#maincontent').load(location.href+' #maincontent', function() {
					attachFilterChangeEvent();
				});
			},
			onSelectError: function(file,errorCode,errorMsg) {
				//alert(file + ' Error ' + errorCode +':'+errorMsg);
			},
			onUploadError: function(file,errorCode,errorMsg, errorString) {
				alert(errorMsg);
			}
		});
		}
	});
	</script>";
	 ?>
	</li>
<?php } ?>
	<li style="float:right;"><small><?php i18n('MAX_FILE_SIZE'); ?>: <strong><?php echo ini_get('upload_max_filesize'); ?>B</strong></small></li>
</ul>


<?php 
# show normal upload form if Uploadify is turned off 
if (defined('RATNOUPLOADIFY')) { ?>
	<form class="uploadform" action="upload.php?path=<?php echo $path; ?>" method="post" enctype="multipart/form-data">
		<p><input type="file" name="file[]" id="file" style="width:220px;" multiple /></p>
		<input type="hidden" name="hash" id="hash" value="<?php echo $SESSIONHASH; ?>" />
		<input type="submit" class="submit" name="submit" value="<?php i18n('UPLOAD'); ?>" />
	</form>
<?php } else { ?>

	<!-- show normal upload form if javascript is turned off -->
	<noscript>
		<form class="uploadform" action="upload.php?path=<?php echo $path; ?>" method="post" enctype="multipart/form-data">
			<p><input type="file" name="file[]" id="file" style="width:220px;" multiple /></p>
			<input type="hidden" name="hash" id="hash" value="<?php echo $SESSIONHASH; ?>" />
			<input type="submit" class="submit" name="submit" value="<?php i18n('UPLOAD'); ?>" />
		</form>
	</noscript>

<?php } ?>