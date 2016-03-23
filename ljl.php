<?php
error_reporting(E_ALL);

//phpinfo();

//Here, we are also applying the reverse proxy! For more details of reverse proxy, check out /etc/httpd/conf/httpd.conf
/*$file = "http://www.yetiket.com/uploadedimages/60d7c9b13e2d44dbb59ed2bfe4ad142a_mehmetsen80/Profile_60d7c9b13e2d44dbb59ed2bfe4ad142a_1363793667_Thumb2.jpg";*/

$file = "http://oyvent.com/uploadedimages/12/1403066158_thumb.jpg";

$image = imagecreatefromjpeg($file);

// start buffering
ob_start();
imagejpeg($image);
$contents =  ob_get_contents();
ob_end_clean();

echo "<P>";
echo "<img src='data:image/jpeg;base64,".base64_encode($contents)."' />";

imagedestroy($image);

echo "</p><P>\n";

$file = "/mnt/toros/uploadedimages/60d7c9b13e2d44dbb59ed2bfe4ad142a_mehmetsen80/Profile_60d7c9b13e2d44dbb59ed2bfe4ad142a_1383776169_Thumb.jpg";
$image = imagecreatefromjpeg($file);


// start buffering
ob_start();

imagejpeg($image);
$contents =  ob_get_contents();
ob_end_clean();

echo "<img src='data:image/jpeg;base64,".base64_encode($contents)."' />";

imagedestroy($image);
echo "</p>\n";



//phpinfo();



?>

