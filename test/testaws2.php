<?php 

if (!ini_get('display_errors')) {
    ini_set('display_errors', '1');
}

include($_SERVER['DOCUMENT_ROOT']."/lib/aws/awsfnc.php");	

$keyname = "1467479bc0844831-large.jpg";
$foldername = "12";

//$result = deleteObject($foldername,$keyname);
$result = getObject($keyname);

print_r($result);


?>