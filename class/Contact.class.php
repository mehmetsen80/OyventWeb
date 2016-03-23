<?php 

require_once($_SERVER['DOCUMENT_ROOT']."/db/DbConnection.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");
include($_SERVER['DOCUMENT_ROOT']."/class/PHPMailerAutoload.php");	

class Contact 
{
	function __construct()
  	{
	}


	public function sendMessage($name,$email,$message)
	{
		include($_SERVER['DOCUMENT_ROOT']."/Settings.php");
		
		try{
			
			beginTrans(); // transaction begins			
			
			date_default_timezone_set('America/Chicago');
			$date = date('Y-m-d H:i:s');		
					
			$query =  " INSERT INTO TBLCONTACT (NAME,EMAIL,MESSAGE,POSTDATE) ";
	   	 	$query .= " VALUES ('".$name."','".$email."','".$message."','".$date."')";
     
        		
			$pkContactID = executeInsertQueryForTrans($query);
			
			if(isset($pkContactID))											
			{
				commitTrans(); // transaction is committed
				
				$subject = "You have new Contact Message";				
				$message = str_replace("\n", "\r\n", $message);				
     			$body = "You have a new contact message posted by ".$name." (".$email.")<br><br>-----------------------------------------------------------<br>".$message;		
				$mail = new PHPMailer;
				$mail->isSMTP();
				$mail->Host = 'localhost'; 
				$mail->From = $email;
				$mail->FromName = 'Oyvent';
				$mail->addAddress('info@oyvent.com');
				$mail->addReplyTo($email);
				$mail->addBCC('mehmetsen80@gmail.com');
				$mail->isHTML(true);
				$mail->Subject = $subject;
				$mail->Body  = $body;
				$mail->send();
				
				return array('success' => true, 'error' => false, 'pkContactID' => $pkContactID);
				
			}
			else
			{
				rollbackTrans();
				return array('success'	=>	false, 'error' => 'Invalid ContactID!', 'pkContactID' => $pkContactID); 
			}		
		
		}
		catch(Exception $e){
			rollbackTrans(); // transaction rolls back		
			return array('success'	=>	false, 'error' => 'Invalid Contact Message!', 'pkContactID' => '');	
		}
		
	}

}

?>