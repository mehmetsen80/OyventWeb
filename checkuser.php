<?php 

require_once($_SERVER['DOCUMENT_ROOT']."/class/UserInfo.class.php");

@session_start();

if (!isset($_SESSION['userObject']) || empty($_SESSION['userObject']))
	header("Location: /login/");
else
	$userObject = $_SESSION['userObject'];

?>