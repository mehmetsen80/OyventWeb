<?php  
 require_once($_SERVER['DOCUMENT_ROOT']."/class/UserInfo.class.php");
 require_once($_SERVER['DOCUMENT_ROOT']."/class/Login.class.php");
 include($_SERVER['DOCUMENT_ROOT']."/Tools.php");
  
 $processType= ($_GET['processType'])?$_GET['processType']:$_POST["processType"];
 
 switch($processType)
 {
 	case "LOGINUSER":
		loginUser();
		break;
	case "LOGINUSERVIAWEB":
		loginUserviaWeb();
		break;
 	case "FORGOTPASSWORD":
 		forgotPassword();
 		break;
 	case "CHANGEPASSWORD":
 		changePassword();
 		break;
 	case "RESETPASSWORD":
 		resetPassword();
 		break;
 	case "CHECKPASSWORD":
 		checkPassword();
 		break;
 	case "CHECKREPASSWORD":
 		checkRePassword();
 		break;
 }
 
 function loginUserviaWeb(){
 	
	$email = $_GET["email"]; 	
	$email = utf8_urldecode($email);
	$email = real_escape_string($email);
	
  	$password =$_GET["password"];
	$password = utf8_urldecode($password);
	$password = real_escape_string($password);
	 	
 	$login = new Login();
	$result  = $login->loginUser($email, $password);
	echo json_encode($result);
 }
 
 function loginUser()//mobile
 {
 	$email = $_POST["email"]; 	
	//$email = utf8_urldecode($email);
	//$email = real_escape_string($email);
	
  	$password =$_POST["password"];
	//$password = utf8_urldecode($password);
	//$password = real_escape_string($password);
	 	
 	$login = new Login();
	$result  = $login->loginUser($email, $password);
	$results = array();
	array_push($results, $result);
 	//echo htmlspecialchars(json_encode($results), ENT_NOQUOTES);
	echo json_encode($result);
	
 }
 
 function forgotPassword()
 {
 	$email = $_GET["email"];
	$email = real_escape_string($email);
 	
 	$login = new Login();
 	$result = $login->forgotPassword($email);
 	echo json_encode($result);
 }
 
 function changePassword()
 {
 	$oldpassword = $_GET["oldpassword"];
 	$newpassword = $_GET["newpassword"];
 	$repassword = $_GET["repassword"];
 	
 	$login = new Login();
 	$result  = $login->changePassword($oldpassword, $newpassword, $repassword);
 	echo json_encode($result);
 }
 
 function resetPassword()
 {
 	$email = $_GET["email"];
	$email = utf8_urldecode($email);
	$email = real_escape_string($email);
	
 	$password = $_GET["password"];
	$password = utf8_urldecode($password);
	$password = real_escape_string($password);
		
 	$repassword = $_GET["repassword"];
	$repassword = utf8_urldecode($repassword);
	$repassword = real_escape_string($repassword);
 	
 	$login = new Login();
 	$result  = $login->resetPassword($email, $password, $repassword);
 	echo json_encode($result);
 }
 
 function checkPassword()
 {
 	$password=$_GET["password"]; 
	$password = utf8_urldecode($password);
	$password = real_escape_string($password);
  
 	$login = new Login();
 	$result = $login->checkNewPassword($password);
 	echo json_encode($result);    
 }
 
 function checkRePassword()
 {
 	$password = $_GET["password"];
	$password = utf8_urldecode($password);
	$password = real_escape_string($password);	
	//check repassword
	$repassword=$_GET["repassword"]; 
	$password = utf8_urldecode($repassword);
	$repassword = real_escape_string($repassword);
	
	$login = new Login();
 	$result = $login->checkRePassword($password,$repassword);
 	echo json_encode($result);    
 }
		
		
?>