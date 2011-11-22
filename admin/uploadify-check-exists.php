<?php
/**
 * Upload Folder Checker Ajax
 *
 * Return true if the file exists
 *
 * @author Ronnie Garcia
 * @package RAT6
 * @subpackage Files
 */
 
include('inc/common.php');

$path = (isset($_GET['path'])) ? $_GET['path'] . "/" : "";
$name = clean_img_name($_POST['filename']);
if (file_exists(RATDATAUPLOADPATH .$path . $name)) {
	echo 1;
} else {
	echo 0;
}
?>
