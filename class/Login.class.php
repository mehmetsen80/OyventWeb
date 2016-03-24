<?php

require_once($_SERVER['DOCUMENT_ROOT']."/class/UserInfo.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Regex.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/db/DbConnection.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/UUID.class.php");



class Login {

	function __construct() {
   		
  	}
  	
  	function loginUser($email,$password)
  	{
  		include($_SERVER['DOCUMENT_ROOT']."/Settings.php");		
		require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");			
		
  		$email = trim($email);
  		$password = trim($password);		
     	$password = hashPassword("aro071buqW", $password);// hash the password  		
		
  		
		if(strlen($email) <= 0 || empty($email) || !isset($email))
	  		return array("success" => false, "message" => "Invalid email or username!");
	  		
	  	if(strlen($password) <= 0 || empty($password) || !isset($password))
	  		return array("success" => false, "message" => "Invalid Password!");	  	
  		
  		// select first required fields to check;  activeness, isblocked, email, deactivated
 		$query =  "SELECT PKUSERID,USERUUID, FULLNAME,USERNAME, EMAIL,ISVERIFIED, FIRSTLOGINDATE,LASTLOGINDATE,LASTACTIVEDATE,INSTAGRAMID, ISACTIVE, ISBLOCKED, ISADMIN, FIRSTINIP, LASTINIP FROM TBLUSER  WHERE (TRIM(EMAIL)='$email' AND PASSWORD='$password') OR (TRIM(USERNAME)='$email' AND PASSWORD='$password') ";
		
		//return array("success" => false, "message" => $query);
 
 		$result = executeQuery($query);
 
 		if(mysql_num_rows($result)>0) //if user exists
 		{     
     		$row = mysql_fetch_array($result); //fetch the row for furhter checkup



			$userObject = new UserInfo();
	 		$userObject->userID =  doubleval($row["PKUSERID"]);
			$userObject->email = $row["EMAIL"];
			$userObject->userUUID = $row["USERUUID"];
			$userObject->username = $row["USERNAME"];
	 		$userObject->fullname = $row["FULLNAME"];
			$userObject->fullname = stripslashes($userObject->fullname);				
     		$userObject->instagramID = $row["INSTAGRAMID"];
     		$userObject->firstlogindate = $row["FIRSTLOGINDATE"];	
			$userObject->isAdmin = ($row["ISADMIN"] != NULL && $row["ISADMIN"] != "")? (bool)$row["ISADMIN"]:false;
			$userObject->isVerified = $row["ISVERIFIED"];
			$userObject->lastlogindate = $row["LASTLOGINDATE"];
			$userObject->lastactivedate = $row["LASTACTIVEDATE"];
			$userObject->firstinip = $row["FIRSTINIP"];
			$userObject->lastinip = $_SERVER['REMOTE_ADDR'];
	 			 
	 		if($row["ISBLOCKED"] == 1)
	 		{
	 			$query = "UPDATE TBLUSER SET ISACTIVE='0', ISADMIN ='0' WHERE PKUSERID ='".$row["PKUSERID"]."' ";
				$result = executeQuery($query);				
				return array("success" => false, "message" => "Your account is blocked, please contact the administrator!");		
	 		}	 		
	 		else // if user active
	 		{								
				$query = "UPDATE TBLUSER SET ISACTIVE='1', ISDEACTIVATED='0', LASTLOGINDATE=NOW(), 
				LASTINIP='".$_SERVER['REMOTE_ADDR']."'  WHERE PKUSERID ='".$userObject->userID."' ";

                //return array("success" => false, "message" => $query);

				$result = executeQuery($query);				
				
				@session_start();
	 			$_SESSION['userObject'] = $userObject;     			
					
				return array("success" => true, "message" => "OK",
							"userID" =>$userObject->userID,"email" =>$userObject->email,
							"username" => "", "fullname" => $userObject->fullname, 
							"lastlogindate" => $userObject->lastlogindate,
							"signupdate" => $userObject->firstlogindate,
							"isadmin" => $userObject->isAdmin);	//finally we are fine  				
				
 			}//end of if user active  
 		}//end of if user exists
		else
		{
			return array("success" => false, "message" => "Invalid user, please try again!");
		}
  		
  	}  	
  	
  	function forgotPassword($email)
  	{	
		require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");
		include($_SERVER['DOCUMENT_ROOT']."/Settings.php");		  	
		include($_SERVER['DOCUMENT_ROOT']."/class/PHPMailerAutoload.php");	
		
  		//check if valid email		
   		$arr = $this->checkEmail($email);
		if(!$arr["success"]) return $arr;	
		
		
   		// lookup if email exists
  		$query = "SELECT ISACTIVE FROM TBLUSER WHERE EMAIL='".$email."' ";
		$result = executeQuery($query);	
		
		if(mysql_num_rows($result) == 0) // if email not exists
			return array("success" => false, "message" => "This email does not exist!");			
									
  		//generate new id, 10 is the length
	 	$generatedID = fnc_generate_random_ID(10);	
	//echo $generatedID;
	 	
	 	
	 	$subject = "Oyvent Forgot Password";
	 	$message = "Please click the link below to reset your password:";
	 	$message .= "\n\n";
	 	$message .= $sitepath."/login/Reset.php?email=".$email."&";
	 	$message .= "code=".$generatedID;
	 	$message .= "\n\n";
	 	$message .= "Oyvent Team";	
	 	
		$mail = new PHPMailer;
		$mail->isSMTP();
		$mail->Host = 'localhost'; 
		$mail->From = $noReplyEmail;
		$mail->FromName = 'Oyvent';
		$mail->addAddress($email);
		$mail->addReplyTo($noReplyEmail, 'NOREPLY');
		$mail->isHTML(true);
		$mail->Subject = $subject;
		$mail->Body    = $message;
		//$mail->AltBody = $message;

		//ini_set($smtp, $dbhost);				
	 	//mail($email, $subject, $message,"From:".$noReplyEmail);
	 	
	 	/* some examples for date calculations
		 	date("Y-m-d h:i:s", strtotime("+1 week"));
		 	date("Y-m-d h:i:s", strtotime("+2 week"));
		 	date("Y-m-d h:i:s", strtotime("+1 month"));
		 	date("Y-m-d h:i:s", strtotime("+30 days"));
		 	$today = date("Y-m-d h:i:s");
		*/
		
		if($mail->send()){
					
			date_default_timezone_set("America/Chicago");		
			$expiredate = date("Y-m-d h:i:s", strtotime("+1 day"));	
	 	
			$query =  " UPDATE TBLUSER SET FORGOTCODE='".$generatedID."' , ISACTIVE='1', ";
			$query .= " FORGOTEXPIREDATE='".$expiredate."' ";
			$query .= " WHERE EMAIL='".$email."'";
			$result = executeQuery($query);
		
			return array("success" => true, "message" => "Congratulations, the password reset link is sent to your email.<br>".
		     "Please do not forget to check out your junk folder!");
		}else {
   		 	return array("success" => false, "message" => "Email could not be sent:".$mail->ErrorInfo);
		}
		
  	}  	
  	
  	function changePassword($oldpassword, $newpassword, $repassword)
  	{
  		$arr = $this->checkNewPassword($newpassword);
		if(!$arr["success"]) return $arr;
		
		$arr = $this->checkRePassword($newpassword, $repassword);		
		if(!$arr["success"]) return $arr;
		
		include($_SERVER['DOCUMENT_ROOT']."/Settings.php");
		
  		if(isset($_SESSION['userObject']))
		{
			$userObject = $_SESSION['userObject'];
			$username = $userObject->username;
	 
	 		//hash password
     		$newpassword = hashPassword($username,$newpassword);
	
	  		$query = "UPDATE TBLUSER SET PASSWORD='".$newpassword."' WHERE USERNAME='".$username."' ";		
	 		$result = executeQuery($query);		
	
	 		return array("success" => true, "message" => "Congratulations, your password has been changed successfully.<br>");			
		}
		else
		{
			return array("success" => false, "message" => "Your session is expired. Please <a href='$sitepath/login/'>login</a> and try again.");			
		}		
  	}  	
  	
  	function resetPassword($email, $password, $repassword)
  	{  		
  		$arr = $this->checkNewPassword($password);
		if(!$arr["success"]) return $arr;
		
		$arr = $this->checkRePassword($password, $repassword);		
		if(!$arr["success"]) return $arr;		
		
		//hash password
     	$password = hashPassword("aro071buqW",$password);
		
		$query = "UPDATE TBLUSER SET PASSWORD='".$password."' WHERE EMAIL='".$email."' ";		
		$result = executeQuery($query);		
		
		$message = "Congratulations, your password has been changed.<br>".
		     "Please <a class='button' href='/'>login</a> now!";
		
		
		return array("success" => true, "message" => $message);
  	}
  	
  	function checkEmail($email)
  	{
  		if(!Regex::isValidEmailAddress($email))
  			return array("success" => false, "message" => "Please enter a valid email address!"); 
  		
  		return array("success" => true, "message" => "OK");
  	}
  	
  	function checkNewPassword($password)
  	{
  		if (!Regex::isIllegalPassword($password))
 			return array("success" => false, "message" => "Enter valid password between 4-20 chars!"); 		
 			
 		return array("success" => true, "message" => "Password OK"); 	
  	}
  	
	function checkRePassword($newpassword,$repassword)
  	{
  		if (!Regex::isIllegalPassword($repassword))
 			return array("success" => false, "message" => "Enter valid re-password between 4-20 chars!");

 		if($newpassword != $repassword)
 			return array("success" => false, "message" => "Passwords dont match!");
 		
 		return array("success" => true, "message" => "Repassword OK");
  	}  	
	
}

?>