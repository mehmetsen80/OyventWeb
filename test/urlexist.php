<?php 

require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");
$exists = is_url_exist("https://s3-oy-vent-images-14.s3.amazonaws.com/12/7ebb1984449e4136-large.jpg");
echo "URL 1: ".$url."  does exists? ".$exists."<br>"; 

$exists = is_url_exist("https://s3-oy-vent-images-14.s3.amazonaws.com/12/d07f4aeb2fad4f9f-large.jpg");
echo "URL 2: ".$url."  does exists? ".$exists."<br>"; 

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Untitled Document</title>
</head>

<body>
</body>
</html>