<?php   
 require_once($_SERVER['DOCUMENT_ROOT']."/class/Register.class.php");
 include($_SERVER['DOCUMENT_ROOT']."/Tools.php");
  
 $processType= ($_GET['processType'])?$_GET['processType']:$_POST["processType"];

 
 switch($processType)
 {
	case "SIGNUPUSERVIAWEB":
		signupUserViaWeb();
		break;
 	case "SIGNUPUSER"://mobile
		signupUser();
		break;
 	case "CHECKUSERNAME":
 		checkUsername();
 		break;
 	case "CHECKFULLNAME":
 		checkFullname();
 		break;
 	case "CHECKPASSWORD":
 		checkPassword();
 		break;
 	case "CHECKREPASSWORD":
 		checkRePassword();
 		break;
 	case "CHECKEMAIL":
 		checkEmail();
 		break;
 }
 
 function signupUserViaWeb()
 {
 	//get the parameters from URL
  	$fullname = $_GET["fullname"];
	$fullname = utf8_urldecode($fullname);	
	$fullname = real_escape_string($fullname);	
	
  	$email = $_GET['email'];
	$email = utf8_urldecode($email);
	$email = real_escape_string($email);
  	
	$password =$_GET["password"];	
	$password = utf8_urldecode($password);
	$password = real_escape_string($password);
	
	//$result = array("success"=>true,"message" => $fullname);
	
	$isWeb = true;
 	$register = new Register();
 	$result = $register->signupUser($fullname,$email,'',$password,$isWeb); 	
 	echo json_encode($result);	 	
 }
 
 function signupUser()
 {
 	//get the parameters from URL
  	$fullname = $_POST["fullname"];
	$fullname = utf8_urldecode($fullname);	
	$fullname = real_escape_string($fullname);	
	
  	$username = $_POST["username"];
	if($username != NULL){
		$username = utf8_urldecode($username);	
		$username = real_escape_string($username);
	}
	
  	$email = $_POST['email'];
	$email = utf8_urldecode($email);
	$email = real_escape_string($email);
  	
	$password =$_POST["password"];	
	$password = utf8_urldecode($password);
	$password = real_escape_string($password);
	
	//$result = array("success"=>true,"message" => $fullname);
	
 	$register = new Register();
 	$result = $register->signupUser($fullname,$email,$username,$password,false);
 	$results = array();
	array_push($results, $result);
 	//echo htmlspecialchars(json_encode($results), ENT_NOQUOTES);	
	
 	echo json_encode($result);	
 }
 
 function checkUsername()
 {
 	$username = $_GET["username"];
	$username = real_escape_string($username);
	
 	$register = new Register();
 	$result  = $register->checkUsername($username);
 	//echo json_encode($result);
	echo htmlspecialchars(json_encode($result), ENT_NOQUOTES); 
 }
 
 function checkFullname()
 {
 	$fullname = $_GET["fullname"];
	$fullname = utf8_urldecode($fullname);
	$fullname = real_escape_string($fullname);
	
 	$register = new Register();
 	$result =  $register->checkFullname($fullname); 	
 	echo json_encode($result);
	
 }
 
 function checkPassword()
 {
 	$password = $_GET["password"];
	$password = real_escape_string($password);
	
 	$register = new Register();
 	$result = $register->checkPassword($password);
 	
 	echo json_encode($result);	
 }
 
 function checkRePassword()
 {
 	$password = $_GET["password"];
	$password = real_escape_string($password);
	
 	$repassword = $_GET["repassword"];
	$repassword = real_escape_string($repassword);
	
 	$register = new Register();
 	$result = $register->checkRePassword($password,$repassword);
 	
 	echo json_encode($result);	
 }
 
 function checkEmail()
 {
 	$email = $_GET['email'];
	$email = real_escape_string($email);
	
 	$register = new Register();
 	$result = $register->checkEmail($email);
 	
 	echo json_encode($result);	
 }
 
 ?>