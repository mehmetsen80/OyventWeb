<?php 

require_once($_SERVER['DOCUMENT_ROOT']."/db/DbConnection.php");
include($_SERVER['DOCUMENT_ROOT']."/class/PHPMailerAutoload.php");	
require_once($_SERVER['DOCUMENT_ROOT']."/class/UserInfo.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Album.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");



class Comment{
	
	public $pkCommentID = NULL;
	public $fkPhotoID = NULL;
	public $fkUserID = NULL;	
	public $comment = NULL;
	public $latitude = NULL;
	public $longitude = NULL;
	public $postDate = NULL;
	public $commentsize = 0;
	
	function __construct($commentID=NULL){
		
		if(isset($commentID)){
			$this->pkCommentID = $commentID;
			
			$query = "SELECT PKCOMMENTID, FKPHOTOID, FKUSERID, COMMENT, LATITUDE, LONGITUDE POSTDATE FROM TBLCOMMENT 
			WHERE PKCOMMENTID = '".$commentID."' ";
			$result = executeQuery($query);			
				
			if(mysql_num_rows($result)>0)
			{
				$row = mysql_fetch_array($result);
				$this->pkCommentID = $row["PKCOMMENTID"];
				$this->fkPhotoID = $row["FKPHOTOID"];
				$this->fkUserID = $row["FKUSERID"];
				$this->comment = real_escape_string($row["COMMENT"]);
				$this->comment = stripslashes($this->comment);		
				$this->latitude = $row["LATITUDE"];
				$this->longitude = $row["LONGITUDE"]; 
				$this->postDate = $row["POSTDATE"];				
			}
		}
	}
	
  function addReport($photoID,$userID,$report,$albumID){
  	
	include($_SERVER['DOCUMENT_ROOT']."/Settings.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/class/Album.class.php");
		
	
	try{		
			
		$fullname = GetFullName($userID);
		$album = new Album($userID,$albumID);
		$report = "Reported by ".$fullname." at album ".$album->albumName." :  ".$report;	
			
		beginTrans(); // transaction begins	
		
		date_default_timezone_set('America/Chicago');
		$date = date('Y-m-d H:i:s');	
		
		
		$query = "INSERT INTO TBLREPORT (FKPHOTOID,FKUSERID,FKALBUMID,REPORT,POSTDATE) 
			VALUES('".$photoID."','".$userID."','".$albumID."','".$report."','".$date."') ";		
			
		//return array('success' => false, 'error' => $query, 'message' => '');		
														
		$pkReportID = executeInsertQueryForTrans($query);
			
		if(isset($pkReportID))											
		{
				commitTrans(); // transaction is committed
				
				//return array('success' => false, 'error' => $pkReportID, 'message' => '');	
				
				$subject = "New Report by ".$fullname;				
				$report = str_replace("\n", "<br>\n", $report);				
     			$message = "New report posted by ".$fullname." under ".$album->albumName.".<br><br>-----------------------------------------------------------<br>".$report."<br><br>Go to post:<br><a href='".$sitepath."/admin/feed.php?photoID=".$photoID."'>".$sitepath."/admin/feed.php?photoID=".$photoID."</a>";			
				$mail = new PHPMailer;
				$mail->isSMTP();
				$mail->Host = 'localhost'; 
				$mail->From = $noReplyEmail;
				$mail->FromName = 'Oyvent Report Notification';
				$mail->addAddress('info@oyvent.com');
				if($albumID == 34){//ualr
					$mail->addAddress('afranke@oyvent.com');
					$mail->addAddress('ryoung@oyvent.com');
				}else if($albumID == 35){//ualbany
					$mail->addAddress('adonald@oyvent.com');
				}
				$mail->addReplyTo($noReplyEmail, 'NOREPLY');
				$mail->isHTML(true);
				$mail->Subject = $subject;
				$mail->Body    = $message;
				$mail->send();
				
				return array('success' => true, 'error' => '', 'message' => $pkReportID);
		}
		
		return array('success' => false, 'error' => 'Invalid Report Attemp, please try again!', 'message' => '');
		
	}
	catch(Exception $e){
		rollbackTrans(); // transaction rolls back		
		return array('success'	=>	false, 'error' => 'Invalid Report Addition!', 'message' => '');	
	}
	
  }
	
  function addComment($photoID,$userID,$comment,$latitude,$longitude,$owneremail,$ownername){
		
	include($_SERVER['DOCUMENT_ROOT']."/Settings.php");
		
		try{		
			@session_start();
			$userObject = $_SESSION['userObject'];
			
			beginTrans(); // transaction begins	
						
			date_default_timezone_set('America/Chicago');
			$date = date('Y-m-d H:i:s');	
		
			$query = "INSERT INTO TBLCOMMENT (FKPHOTOID,FKUSERID,COMMENT,LATITUDE,LONGITUDE,POSTDATE) 
				VALUES('".$photoID."','".$userID."','".$comment."','".$latitude."','".$longitude."','".$date."') ";
				
							
			$pkCommentID = executeInsertQueryForTrans($query);
			
			if(isset($pkCommentID))											
			{
				commitTrans(); // transaction is committed
				
				if($userObject->email != $owneremail){
					$subject = "You have new Comment";				
					$comment = str_replace("\n", "<br>\n", $comment);				
     				$message = "You have a new comment posted by ".$ownername.".<br><br>-----------------------------------------------------------<br>".$comment."<br><br><a href='".$sitepath."/admin/feed.php?photoID=".$photoID."'>".$sitepath."/admin/feed.php?photoID=".$photoID."</a>";			
					$mail = new PHPMailer;
					$mail->isSMTP();
					$mail->Host = 'localhost'; 
					$mail->From = $noReplyEmail;
					$mail->FromName = 'Oyvent';
					$mail->addAddress($owneremail);
					$mail->addReplyTo($noReplyEmail, 'NOREPLY');
					$mail->isHTML(true);
					$mail->Subject = $subject;
					$mail->Body    = $message;
					$mail->send();
				}
				
				return array('success' => true, 'error' => false, 'pkCommentID' => $pkCommentID);
				
			}
			else
			{
				rollbackTrans();
				return array('success'	=>	false, 'error' => 'Invalid CommentID!', 'pkCommentID' => $pkCommentID); 
			}		
				
			
		}
		catch(Exception $e){
			rollbackTrans(); // transaction rolls back		
			return array('success'	=>	false, 'error' => 'Invalid Comment Addition!', 'pkCommentID' => '');	
		}
	}
	
	function getComments($photoID){
		
		$query = "SELECT c.PKCOMMENTID,c.FKPHOTOID,c.FKUSERID,c.COMMENT,c.LATITUDE,
		c.LONGITUDE,c.POSTDATE,u.FULLNAME,u.EMAIL ".
		" FROM TBLCOMMENT c INNER JOIN TBLUSER u ON c.FKUSERID = u.PKUSERID ".
		" WHERE c.FKPHOTOID='".$photoID."' ORDER BY c.POSTDATE DESC";
		
		$result = executeQuery($query);		
		$this->commentsize = mysql_num_rows($result);
		
		return mysql_fetch_rowsarr($result);
	}
	
	function deleteComment(){
		
		try{		
			beginTrans(); // transaction begins
			
			$query = "DELETE FROM TBLCOMMENT WHERE PKCOMMENTID='".$this->pkCommentID."'";
			$delete = executeQueryForTrans($query);
			commitTrans(); // transaction is committed
			return true;
			
		}
		catch(Exception $e){
			rollbackTrans(); // transaction rolls back		
				return false;
		}
		
		return false;
	}
}

?>