<?php 

require_once($_SERVER['DOCUMENT_ROOT']."/class/Instagram.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/admin/Instagram.config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/db/DbConnection.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/UserInfo.class.php");

@session_start();
$userObject = $_SESSION['userObject'];

if(isset($userObject)){

	// receive OAuth code parameter
	$code = $_GET['code'];

	// check whether the user has granted access
	if (isset($code)) {

		date_default_timezone_set('America/Chicago');
		$firstlogindate = date("Y-m-d H:i:s");

  		// receive OAuth token object					
		$instadetails = $instagram->getOAuthToken($code);
		if(empty($instadetails->user->username))
		{
			header('Location: instalogin.php');
		}
		else
		{						
				$query = "UPDATE TBLUSER SET INSTAGRAMID='".$instadetails->user->id."' WHERE PKUSERID ='".$userObject->userID."' ";
				$result = executeQuery($query);
				$userObject->instagramID = $instadetails->user->id;
				$userObject->instadetails = $instadetails;
				$_SESSION['userObject'] = $userObject;
				
				header('Location: instaphotos.php');
		}
	 	
	} // check whether an error occurred
  	else{
		if (isset($_GET['error'])) {
    		echo 'An error occurred: ' . $_GET['error_description'];
  		}
	}
}else
{
	echo "Session expired, please first <a href='/login' >login</a>";
}

?>
