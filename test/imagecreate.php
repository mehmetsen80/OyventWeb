<?php
/*header("Content-type: image/png");
$im = @imagecreate(110, 20)
    or die("Cannot Initialize new GD image stream");
$background_color = imagecolorallocate($im, 0, 0, 0);
$text_color = imagecolorallocate($im, 233, 14, 91);
imagestring($im, 1, 5, 5,  "A Simple Text String", $text_color);
imagepng($im);
imagedestroy($im);*/

//error_reporting(-1);
//ini_set('display_errors', 'On');	




//Header("Content-type: image/jpeg");

require_once($_SERVER['DOCUMENT_ROOT']."/class/Text2Image.class.php");

/*$msg = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer non nunc lectus. Curabitur hendrerit bibendum enim dignissim tempus. Suspendisse non ipsum auctor metus consectetur eleifend. Fusce cursus ullamcorper sem nec ultricies. Aliquam erat volutpat. Vivamus massa justo, pharetra et sodales quis, rhoncus in ligula. Integer dolor';

echo "width:".strlen($msg);*/

$msg = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer non nunc lectus. Curabitur hendrerit bibendum enim dignissim tempus. Suspendisse non ipsum auctor metus consectetur eleifend. Fusce cursus ullamcorper sem nec ultricies. Aliquam erat volutpat. Vivamus massa justo, pharetra et sodales quis, rhoncus in ligula. Integer dolor velit, ultrices in iaculis nec, viverra ut nunc.';

$text = new Text2Image(400,400,$msg,"largefile");
$file = $text->createImage(); 
$text->destroy_buffer();
//$text-
echo "size:". imagesx($file);
//echo $text->output();

?> 