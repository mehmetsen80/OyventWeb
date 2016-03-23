<?php 

@session_start();

if($_GET['id']=='logout')
{	
	unset($_SESSION['userObject']);
	$_SESSION['userObject'] = NULL;	
	session_destroy();
}

if(!isset($_SESSION['userObject']))
	header("Location: /login/");
else
	header("Location: /search/");

?>