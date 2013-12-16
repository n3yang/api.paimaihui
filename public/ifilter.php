<?php

define('PATH_IMAGE', '/tmp/');
define('IMAGE_WATER_WIDTH', 222);
define('IMAGE_WATER_HEIGHT', 25);
define('IMAGE_WATER_BIG_WIDTH', 355);
define('IMAGE_WATER_BIG_HEIGHT', 115);
$watermark = true;

$image = $_GET['image'];
if (preg_match('/(.*)-thumb-(160|345).(jpg|png|gif)$/', $image, $match)) {
	$filename = PATH_IMAGE . $match[1] . '.' . $match[3];
	$newWidth = $match[2];
	$isThumb = TRUE;
	$watermark = $newWidth==160 ? false : true;
} else {
	$filename = PATH_IMAGE . $image;
}

list($width, $height, $type) = getimagesize($filename);
switch ($type) {
	case IMAGETYPE_JPEG:
		$source = imagecreatefromjpeg($filename);
	break;
	
	case IMAGETYPE_PNG:
		$source = imagecreatefrompng($filename);
	break;
	
	case IMAGETYPE_BMP:
		$source = imagecreatefromwbmp($filename);
	break;
	
	case IMAGETYPE_GIF:
		$source = imagecreatefromgif($filename);
	break;
	
	default:
		exit;
	break;
}

// thumb ? to resize
if ($isThumb) {
	$newHeight = ($newWidth/$width)*$height;
	$imThumb = imagecreatetruecolor($newWidth, $newHeight);
	imagecopyresampled($imThumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
} else {
	$imThumb = $source;
	$newHeight = $height;
	$newWidth = $width;
}

// watermark
if ($watermark) {
        if ($newWidth < 400){
                $imMask =  imagecreatefrompng('watermark.png');
                imagecopy($imThumb, $imMask, ($newWidth-IMAGE_WATER_WIDTH)/2, ($newHeight-IMAGE_WATER_HEIGHT)/2, 0, 0, IMAGE_WATER_WIDTH, IMAGE_WATER_HEIGHT);
        } else {
                $imMask =  imagecreatefrompng('watermark-big.png');
                imagecopy($imThumb, $imMask, ($newWidth-IMAGE_WATER_BIG_WIDTH)/2, ($newHeight-IMAGE_WATER_BIG_HEIGHT)/2, 0, 0, IMAGE_WATER_BIG_WIDTH, IMAGE_WATER_BIG_HEIGHT);
        }
}

header("Content-Type: image/jpeg");
imagejpeg($imThumb);