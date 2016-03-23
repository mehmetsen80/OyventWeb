<?php 
	
 require_once($_SERVER['DOCUMENT_ROOT']."/class/Contact.class.php");

 $processType= ($_GET['processType'])?$_GET['processType']:$_POST['processType'];
 
 switch($processType)
 {
 	case "SENDMESSAGE":
		sendMessage();
		break;
 }
 
 function sendMessage(){
 	
	$name= $_POST['name'];
	$name = utf8_urldecode($name);
	$name = real_escape_string($name);
	
	$email = $_POST['email'];
	$email = utf8_urldecode($email);
	$email = real_escape_string($email);	
	
	$message = $_POST['message'];
	$message = utf8_urldecode($message);
	$message = real_escape_string($message);
	
	$contact = new Contact();	
	$my_array = $contact->sendMessage($name,$email,$message);
		
	list($success,$error,$pkContactID) = array_values($my_array);
	
	if($success) 
		$result = array('success' => true, 'error' => false, 'pkContactID' => $pkContactID);			
	else 
		$result = array("success" => $success, "error" => $error, "pkContactID" => $pkContactID);
	
	
	echo json_encode($result);
 }

?>