<?php
/**
 * Upload Files Ajax
 *
 * Ajax action file for jQuery uploader
 *
 * @package GetSimple
 * @subpackage Files
 */

// Setup inclusions
$load['plugin'] = true;

// Include common.php
include('inc/common.php');

if (!defined('RATIMAGEWIDTH')) {
	$width = 200; //New width of image  	
} else {
	$width = RATIMAGEWIDTH;
}
	
if ($_POST['sessionHash'] === $SESSIONHASH) {
	if (!empty($_FILES)){
		
		$tempFile = $_FILES['Filedata']['tmp_name'];
		$name = clean_img_name(to7bit($_FILES['Filedata']['name']));
		$targetPath = (isset($_POST['path'])) ? RATDATAUPLOADPATH.$_POST['path']."/" : RATDATAUPLOADPATH;

		$targetFile =  str_replace('//','/',$targetPath) . $name;
		
		move_uploaded_file($tempFile, $targetFile);
		chmod($targetFile, 0644);   
		$ext = lowercase(substr($name, strrpos($name, '.') + 1));	
		
		if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'png' )	{
			
			$path = (isset($_POST['path'])) ? $_POST['path']."/" : "";
			$thumbsPath = RATTHUMBNAILPATH.$path;
			
			if (!(file_exists($thumbsPath))) {
				mkdir($thumbsPath, 0755);
			}
			echo $path;
			echo " ".$thumbsPath;
			
			//thumbnail for post
			$imRATize = getimagesize($targetFile);
			
			switch(lowercase(substr($targetFile, -3))){
			    case "jpg":
			        $image = imagecreatefromjpeg($targetFile);    
			    break;
			    case "png":
			        $image = imagecreatefrompng($targetFile);
			    break;
			    case "gif":
			        $image = imagecreatefromgif($targetFile);
			    break;
			    default:
			        exit;
			    break;
			}
			  
			$height = $imRATize[1]/$imRATize[0]*$width; //This maintains proportions
			
			$src_w = $imRATize[0];
			$src_h = $imRATize[1];
			
			$picture = imagecreatetruecolor($width, $height);
			imagealphablending($picture, false);
			imagesavealpha($picture, true);
			$bool = imagecopyresampled($picture, $image, 0, 0, 0, 0, $width, $height, $src_w, $src_h); 
			
			if($bool)	{	
				$thumbnailFile = $thumbsPath . "thumbnail." . $name;
				
			    switch(lowercase(substr($targetFile, -3))) {
			        case "jpg":
			            header("Content-Type: image/jpeg");
			            $bool2 = imagejpeg($picture,$thumbnailFile,85);
			        break;
			        case "png":
			            header("Content-Type: image/png");
			            imagepng($picture,$thumbnailFile);
			        break;
			        case "gif":
			            header("Content-Type: image/gif");
			            imagegif($picture,$thumbnailFile);
			        break;
			    }
			}
			
			imagedestroy($picture);
			imagedestroy($image);
			
			
			//small thumbnail for image preview
			$width = 65; //New width of image    
			$height = $imRATize[1]/$imRATize[0]*$width; //This maintains proportions
			
			$src_w = $imRATize[0];
			$src_h = $imRATize[1];
			    
			
			$picture = imagecreatetruecolor($width, $height);
			imagealphablending($picture, false);
			imagesavealpha($picture, true);
			$bool = imagecopyresampled($picture, $image, 0, 0, 0, 0, $width, $height, $src_w, $src_h); 
			
			if($bool)	{
				$thumbsmFile = $thumbsPath . "thumbsm." . $name;
				
			    switch(lowercase(substr($targetFile, -3))) {
			        case "jpg":
			            header("Content-Type: image/jpeg");
			            $bool2 = imagejpeg($picture,$thumbsmFile,85);
			        break;
			        case "png":
			            header("Content-Type: image/png");
			            imagepng($picture,$thumbsmFile);
			        break;
			        case "gif":
			            header("Content-Type: image/gif");
			            imagegif($picture,$thumbsmFile);
			        break;
			    }
			}
			
			imagedestroy($picture);
			imagedestroy($image);
		}	
		echo '1';
	} else {
		echo 'Invalid file type.';
	}
} else {
	echo 'Wrong session hash!';
}
?>
