<?php

require_once($_SERVER['DOCUMENT_ROOT']."/class/Regex.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/db/DbConnection.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/UUID.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/UserInfo.class.php");


class Register {
	
	function __construct() {
   		
  	}  	
  	
  	function signupUser($fullname,$email,$username,$password,$isWeb)
  	{  		
		include($_SERVER['DOCUMENT_ROOT']."/Settings.php");
		require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");	
		include($_SERVER['DOCUMENT_ROOT']."/lib/PhpMailer/PHPMailerAutoload.php");
			
		/*first check out the fields*/
		
		if($isWeb){
			$arr = $this->checkFullname($fullname);		
			if(!$arr["success"]) return $arr;
		}
		
		
		$arr = $this->checkEmail($email);		
		if(!$arr["success"]) return $arr;	
		
		/*if(!$isWeb){
			$arr = $this->checkUsername($username);		
			if(!$arr["success"]) return $arr;	
		}*/
			
		$arr = $this->checkPassword($password);
		if(!$arr["success"]) return $arr;  
		
		$password = hashPassword("aro071buqW",$password);
	 	
	 	//generate unique id for primary key	  	
		$userUUID = UUID::generate(UUID::UUID_RANDOM, UUID::FMT_STRING,"02831768");
		$userUUID = str_replace("-","",$userUUID);
		if(strlen($userUUID) >= 16)
				$userUUID = substr($userUUID,0,16);	
				
		date_default_timezone_set('America/Chicago');
		$firstlogindate = date("Y-m-d H:i:s");
		
	    $query =  " INSERT INTO TBLUSER (USERUUID,FULLNAME,EMAIL,USERNAME,PASSWORD,FIRSTINIP,LASTINIP,FIRSTLOGINDATE,LASTLOGINDATE,LASTACTIVEDATE)
	     VALUES ('".$userUUID."','".$fullname."','".$email."','".$username."','".$password."','".$_SERVER['REMOTE_ADDR']."','".$_SERVER['REMOTE_ADDR']."','".$firstlogindate."','".$firstlogindate."','".$firstlogindate."')";
		 
		
        //return array("success" => false, "message" => $query);
	 	try{		
			beginTrans(); // transaction begins
			
        	$pkUserID = executeInsertQueryForTrans($query);
			
		
			if(isset($pkUserID))
			{				
				$userObject = new UserInfo();
	 			$userObject->userID = $pkUserID;			
	 			$userObject->fullname = $fullname;
				$userObject->fullname = stripslashes($userObject->fullname);		
     			$userObject->email = $email;
				$userObject->username = $username;
	 			$userObject->lastlogindate = $firstlogindate;
	 			$userObject->firstlogindate = $firstlogindate;				
				$userObject->lastactivedate = $firstlogindate;
				$userObject->firstinip = $_SERVER['REMOTE_ADDR'];
				$userObject->lastinip = $_SERVER['REMOTE_ADDR'];
	 
	      						
				
				$subject = "Welcome to Oyvent";
     			$message = "Dear ".$fullname."<br>Oyvent allows you to share community experience at local level. We are looking forward to see you again.
				<br>Best Regards
	 			<br>Oyvent Team";
				
				$mail = new PHPMailer;
				$mail->isSMTP();
				$mail->Host = 'localhost'; 
				$mail->From = $noReplyEmail;
				$mail->FromName = 'Oyvent';
				$mail->addAddress($email);
				$mail->addReplyTo($noReplyEmail, 'NOREPLY');
				$mail->addBCC('mehmetsen80@gmail.com');
				$mail->isHTML(true);
				$mail->Subject = $subject;
				$mail->Body    = $message;				
								
				//ini_set($smtp, $dbhost);
				//mail($email, $subject, $message,"From:".$noReplyEmail);				
				
				if($mail->send()){				
					commitTrans(); // transaction is committed		
					
					// start and assign the session
     				@session_start();
	 				$_SESSION['userObject'] = $userObject;
						 
			 		return array("success" => true, "message" => "OK",
							"userID" =>$userObject->userID,"email" =>$userObject->email,
							"username" => "", "fullname" => $userObject->fullname, "lastlogindate" => $userObject->lastlogindate,
							"signupdate" => $userObject->firstlogindate,"isadmin" => $userObject->isAdmin);	//finally we are fine 
							
					
				}else{
					return array('success'	=>	false, 'message' => 'Unsuccessful Register;'.$mail->ErrorInfo);
				}
			}
			else{
				rollbackTrans();
				return array('success'	=>	false, 'message' => 'Invalid User ID!'); 
			}
		}catch(Exception $e){
			rollbackTrans(); // transaction rolls back		
			return array('success'	=>	false, 'message' => 'Invalid User Addition!');	
		}
		
	   
	    return array("success" => false, "message" => 'Sorry, something went wrong. Please try again!');
				
  	}
  	
  	function checkFullname($fullname)
  	{  
    	if(strlen($fullname) <= 0 || empty($fullname) || !isset($fullname))
	  		return array("success" => false, "message" => "Enter Full Name!");
	     
		$illegalchars = array("\"", "%","^","/","\\","+","&","#","<",">","?","£","'"); 
    	
    	if(!Regex::isVarCleanFrom($fullname,$illegalchars))
     		return array("success" => false, "message" => "Invalid character!");     	    
  	
     	return array("success" => true, "message" => "<img alt='Fullname' src='/images/ok.png' border=0>");     	    	   	  	
  	} 
  	
	function checkEmail($email)
  	{
		//regular email check, valid or not
		if(!Regex::isValidEmailAddress($email))
			return array("success" => false, "message" => "Please enter valid email!");
  
		// if email exists
  		$query = "SELECT * FROM TBLUSER WHERE EMAIL='".$email."' ";
		$result = executeQuery($query);
	
		if(mysql_num_rows($result) > 0) // Already taken
  	   		return array("success" => false, "message" => "Email registered already!");	
	
  	   	return array("success" => true, "message" => "<img alt='Email' src='/images/ok.png' border=0>");		
  	}
  	
  	function checkUsername($username)
  	{
  		if(!Regex::isUsernameLegal($username))//if between 4-20 chars and chars or numbers
			return array("success" => false, "message" => "Pleae enter valid username between 4-20 chars!");	  
		
    	if(!Regex::isIllegalUsername($username))//general names not allowed
			return array("success" => false, "message" => "General usernames not allowed!");
	 
   		// if email exists
  		$query = "SELECT * FROM TBLUSER WHERE USERNAME='".$username."' ";
		$result = executeQuery($query);	
	
		if(mysql_num_rows($result) > 0)
  	   		return array("success" => false, "message" => "Username registered already!");	
	
  	  	return array("success" => true, "message" => "<img alt='Username' src='/images/ok.png' border=0>");  
  	}  	
  	 	
  	
  	function checkPassword($password)
  	{
  		if (!Regex::isIllegalPassword($password))
 			return array("success" => false, "message" => "Please enter valid password between 4-20 chars!");
 		
 		return array("success" => true, "message" => "<img alt='Password' src='/images/ok.png' border=0>");		
 	
  	}
  	
	function checkRePassword($password,$repassword)
  	{
  		if (!Regex::isIllegalPassword($repassword))
 			return array("success" => false, "message" => "Enter valid re-password between 4-20 chars!");

 		if($password != $repassword)
 			return array("success" => false, "message" => "Passwords dont match!");
 		
 		return array("success" => true, "message" => "<img alt='RePassword' src='/images/ok.png' border=0>");
  	}  	
  	
}

?>