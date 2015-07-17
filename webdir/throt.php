<?php
$base = "/var/www/lectbase/";
header("Content-type: image/png");
//header("Content-type: text/plain");
$string = $_GET["text"];
//$string = iconv ( "windows-1250", "utf-8", $string );
//
//$font  = $base . "fonts/futura/FuMeAT.TTF";
$font  = $base . "MyriadPro-Regular.otf";
$size  = 8;
$angle = 90;
// distance between the baseline and "small letter top" line
$bbox = imagettfbbox ( $size, $angle, $font, "x" );
$wx = $bbox[0] - $bbox[6];
// distance from baseline to the upper bbox line
$bbox = imagettfbbox ( $size, $angle, $font, "b" );
$wb = $bbox[0] - $bbox[6];
// distance from the "small letter top" line to the bottom of the bbox
$bbox = imagettfbbox ( $size, $angle, $font, "p" );
$wp = $bbox[0] - $bbox[6];
/* The width of the image is derived from $wx, $wb, $wp. */
$width = $wb + $wp - $wx + 3;
/* Height of the image is the width of the text. */
// $string = $string . " hx=" . $hx . " hb=" . $hb . " hp=" . $hp;
$bbox = imagettfbbox ( $size, $angle, $font, $string );
/* Rotation by 90 degrees means that all positions in the bounding box
   are <= 0. */
$height  = $bbox[1] - $bbox[3];
$im = @imagecreate( $width, $height )
     or die("Cannot Initialize new GD image stream");
$background = imagecolorallocate ( $im, 0xee, 0xee, 0xee );
$black = imagecolorallocate ( $im, 0, 0, 0 );
imagettftext($im, $size, $angle, $width+$wx-$wp-1, $height, $black, $font, $string);
imagepng($im);
imagedestroy($im);
/*
print_r ( $bbox );
echo "width=$width\n";
echo "height=$height";
*/
?>
