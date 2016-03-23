<?php 

require_once($_SERVER['DOCUMENT_ROOT']."/class/Regex.class.php");

$password = "Memocan80$&(";

if (!Regex::isIllegalPassword($password))
 echo "Password is not ok: false";
else
 echo "Password is ok: true";

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