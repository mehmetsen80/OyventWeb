<?php

class UserInfo
{
  public  $userID = '';
  public  $userUUID = '';
  public  $fullname = '';
  public  $username = '';
  public  $email = ''; 
  public  $firstlogindate = '';
  public  $lastlogindate = '';
  public  $lastactivedate = '';  
  public  $firstinip = '';
  public  $isAdmin = 0; 
  public  $isVerified = 0;
  public  $asFacebook = '0';
  public  $facebookID = '';
  public  $asTwitter = '0';
  public  $twitterID = '';
  public  $twitdetails = NULL;
  public  $instagramID = '';
  public  $instadetails = NULL;
  public  $linkedinID = '';
  
 
  //public $info = list(userID,name,gender);  

  
  function __construct() {
   
  } 
 
  public function foo()
  {
   echo "foo";
  }
  
  
  function updateOnlineUser()
  {
  
  	require_once($_SERVER['DOCUMENT_ROOT']."/db/DbConnection.php");
	
  	@session_start();
	
	if(isset($_SESSION['userObject']))
	{
		date_default_timezone_set("Europe/Istanbul");
		$lastactivedate = date('Y-m-d H:i:s'); 
		$this->lastactivedate = $lastactivedate;						
	 	$_SESSION['userObject'] = $this;
		$query = "UPDATE TBLUSER SET LASTACTIVEDATE ='".$lastactivedate."' WHERE EMAIL='".$this->email."' ";
		$result = executeQuery($query);	
	}
  }

  function getOnlineUsers($limit=50)
  {
  	require_once($_SERVER['DOCUMENT_ROOT']."/db/DbConnection.php");
	include($_SERVER['DOCUMENT_ROOT']."/Settings.php");
	
	
	//date_default_timezone_set("Europe/Istanbul");
	date_default_timezone_set("America/Chicago");
	$now = date('Y-m-d H:i:s');
	
	$query =  " SELECT DISTINCT u.PKUSRID, LASTACTIVEDATE, TIMESTAMPDIFF(SECOND,LASTACTIVEDATE,'".$now."'), p.NAMETHUMB2, p.PHYSICALPATHTHUMB2, ";
	$query .= " u.FULLNAME, u.USERNAME ";		
	$query .= " FROM TBLUSER u ";
	$query .= " LEFT OUTER JOIN TBLUPLOADEDPICTURES p ON p.FKUSRID = u.PKUSRID AND p.ISPROFILE = '1' ";
	$query .= " WHERE  u.ISACTIVE = '1'  ";///*AND p.ISPROFILE = '1'*/
	$query .= "  AND (TIMESTAMPDIFF(SECOND,LASTACTIVEDATE, '".$now."') <= 100 AND TIMESTAMPDIFF(SECOND,LASTACTIVEDATE,'".$now."') > 0) ";	
	$query .= " ORDER BY  u.LASTLOGINDATE DESC LIMIT $limit ";	
	
	$result = executeQuery($query);		
	$usersize = mysql_num_rows($result);
	
	$output = "<h2 style='font-size:16px; color:#ff6600; font-weight:bold;'>Online &#220;yeler ($usersize)</h2>";	
	if($usersize > 0)
	{		
		$output .= "<div style='float:left;margin:2px; padding:3px; float:right; clear:both; text-align:right;'>";
		
		while($row = mysql_fetch_array($result)){								
			
			if(isset($row["NAMETHUMB2"]) && trim($row["PHYSICALPATHTHUMB2"] != ""))
			{
				if(file_exists("/".$imageServer."/".$row['PHYSICALPATHTHUMB2']."/".$row['NAMETHUMB2']))
					$image = $imageReverseUrl."/".$row["PHYSICALPATHTHUMB2"]."/".$row["NAMETHUMB2"];
				else
					$image = $sitepath."/images/profile.png";
			}
			else
			{
				$image = $sitepath."/images/profile.png";
			}
								
						
  			$output .= "<div style='text-align:right; float:right;margin:2px;' >";
				
					if($this->username != $row["USERNAME"])
					{
  						$output .= '<a href="javascript:void(0)" onclick="javascript:chatWith(\''.$row["USERNAME"].'\')"   >';
  						$output .= "<img  alt='".$row["FULLNAME"]."'  title='".$row["FULLNAME"]."' src='".$image."' border='0' height='25' width='25'>";
						$output .= "</a>";
					}
					else
					{
						$output .= "<img  alt='".$row["FULLNAME"]."'  title='".$row["FULLNAME"]."' src='".$image."' border='0' height='25' width='25'>";
					}
  			$output .= "</div>";				
		}		
	}

	echo $output;
	
  }
  
  
  
  function getOnlineUsers4Header($limit=20)
  {
  	require_once($_SERVER['DOCUMENT_ROOT']."/db/DbConnection.php");
	include($_SERVER['DOCUMENT_ROOT']."/Settings.php");
	
	
	date_default_timezone_set("Europe/Istanbul");
	$now = date('Y-m-d H:i:s');
	
	$query =  " SELECT DISTINCT u.PKUSRID, LASTACTIVEDATE, TIMESTAMPDIFF(SECOND,LASTACTIVEDATE,'".$now."'), p.NAMETHUMB2, p.PHYSICALPATHTHUMB2, ";
	$query .= " u.FULLNAME, u.USERNAME ";		
	$query .= " FROM TBLUSER u ";
	$query .= " LEFT OUTER JOIN TBLUPLOADEDPICTURES p ON p.FKUSRID = u.PKUSRID AND p.ISPROFILE = '1' ";
	$query .= " WHERE  u.ISACTIVE = '1'  ";///*AND p.ISPROFILE = '1'*/
	$query .= "  AND (TIMESTAMPDIFF(SECOND,LASTACTIVEDATE, '".$now."') <= 100 AND TIMESTAMPDIFF(SECOND,LASTACTIVEDATE,'".$now."') > 0) ";	
	$query .= " ORDER BY  u.LASTLOGINDATE DESC LIMIT $limit ";	
	
	$result = executeQuery($query);		
	$usersize = mysql_num_rows($result);
		
	if($usersize > 0)
	{		
		while($row = mysql_fetch_array($result)){								
			
			if(isset($row["NAMETHUMB2"]) && trim($row["PHYSICALPATHTHUMB2"] != ""))
			{
				if(file_exists("/".$imageServer."/".$row['PHYSICALPATHTHUMB2']."/".$row['NAMETHUMB2']))
					$image = $imageReverseUrl."/".$row["PHYSICALPATHTHUMB2"]."/".$row["NAMETHUMB2"];
				else
					$image = $sitepath."/images/profile.png";
			}
			else
			{
				$image = $sitepath."/images/profile.png";
			}
								
			if($this->username != $row["USERNAME"])
			{
  				$output .= '<li><a href="javascript:void(0)" onclick="javascript:chatWith(\''.$row["USERNAME"].'\')"   >';
  				$output .= $row["FULLNAME"]." <img  alt='".$row["FULLNAME"]."'  title='".$row["FULLNAME"]."' src='".$image."' border='0' height='25' width='25'>";
				$output .= "</a></li>";
			}
			else
			{
				$output .= "<li>".$row["FULLNAME"]." <img  alt='".$row["FULLNAME"]."'  title='".$row["FULLNAME"]."' src='".$image."' border='0' height='25' width='25'></li>";
			}  						
		}		
	}
	else
	{
		$output = "<li>".utf8_encode("Henüz kimse online degil")."</li>";
	}

	echo $output;
	
  }
  
  public function checkUserMessages()
	{	
		include($_SERVER['DOCUMENT_ROOT']."/db/DbConnection.php");		
				
		$strResult = "";
		
		$query  = "SELECT TBLUSER.USERNAME,TBLUSER.FULLNAME,TBLUSER.PKUSRID, TBLMESSAGES.PKID,";
		$query .= "TBLMESSAGES.TOUSRID,TBLMESSAGES.FROMUSRID, TBLMESSAGES.SUBJECT, TBLMESSAGES.UNREADINBOXTOUSR AS UNREAD,";
		$query .= "TBLMESSAGES.UPDATEDDATE,TBLMESSAGES.RECEIVEDDATE ";
		$query .= "FROM TBLMESSAGES ";
		$query .= "INNER JOIN TBLUSER ON TBLUSER.PKUSRID = TBLMESSAGES.FROMUSRID ";	
		$query .= "WHERE TBLMESSAGES.TOUSRID = '".$this->userID."'  AND TBLMESSAGES.ISTRASHTOUSR = 0 AND ISINBOXTOUSR=1 AND UNREADINBOXTOUSR=1 AND TBLMESSAGES.ISROOT = 1 ";
			
		$query .= "UNION ALL ";
			
		//if there are replied messages that the user had sent.
		$query .= "SELECT TBLUSER.USERNAME,TBLUSER.FULLNAME,TBLUSER.PKUSRID, TBLMESSAGES.PKID,";
		$query .= "TBLMESSAGES.TOUSRID,TBLMESSAGES.FROMUSRID, TBLMESSAGES.SUBJECT, TBLMESSAGES.UNREADINBOXFROMUSR AS UNREAD,";
		$query .= "TBLMESSAGES.UPDATEDDATE,TBLMESSAGES.RECEIVEDDATE ";
		$query .= "FROM TBLMESSAGES ";
		$query .= "INNER JOIN TBLUSER ON TBLUSER.PKUSRID = TBLMESSAGES.FROMUSRID ";	
		$query .= "WHERE TBLMESSAGES.FROMUSRID = '".$this->userID."'  AND TBLMESSAGES.ISTRASHFROMUSR = 0 AND ISINBOXFROMUSR=1 AND UNREADINBOXFROMUSR=1 AND TBLMESSAGES.ISINBOXFROMUSR=1 AND TBLMESSAGES.ISROOT = 1 ";
			
		$query .= "ORDER BY UPDATEDDATE DESC";
			
		
		$result = executeQuery($query);
		$totalNewMessages = mysql_num_rows($result);
				
			
		//$totalNewMessages = 2;	
		if($totalNewMessages > 0)
			$strResult = "Inbox(".$totalNewMessages.")";
		else
			$strResult = "Inbox";
			
		return $strResult;
	}
	
	public function checkUserMessages2()
	{	
		//$documentRoot = $_SERVER['DOCUMENT_ROOT'];
		//include($documentRoot."//database//DbConnection.php");		
				
		$strResult = "";
		
		$query  = "SELECT TBLUSER.USERNAME,TBLUSER.FULLNAME,TBLUSER.PKUSRID, TBLMESSAGES.PKID,";
		$query .= "TBLMESSAGES.TOUSRID,TBLMESSAGES.FROMUSRID, TBLMESSAGES.SUBJECT, TBLMESSAGES.UNREADINBOXTOUSR AS UNREAD,";
		$query .= "TBLMESSAGES.UPDATEDDATE,TBLMESSAGES.RECEIVEDDATE ";
		$query .= "FROM TBLMESSAGES ";
		$query .= "INNER JOIN TBLUSER ON TBLUSER.PKUSRID = TBLMESSAGES.FROMUSRID ";	
		$query .= "WHERE TBLMESSAGES.TOUSRID = '$this->userID' AND TBLMESSAGES.ISTRASHTOUSR = 0 AND ISINBOXTOUSR=1 AND UNREADINBOXTOUSR=1 AND TBLMESSAGES.ISROOT = 1 ";
			
		$query .= "UNION ALL ";
			
		//if there are replied messages that the user had sent.
		$query .= "SELECT TBLUSER.USERNAME,TBLUSER.FULLNAME,TBLUSER.PKUSRID, TBLMESSAGES.PKID,";
		$query .= "TBLMESSAGES.TOUSRID,TBLMESSAGES.FROMUSRID, TBLMESSAGES.SUBJECT, TBLMESSAGES.UNREADINBOXFROMUSR AS UNREAD,";
		$query .= "TBLMESSAGES.UPDATEDDATE,TBLMESSAGES.RECEIVEDDATE ";
		$query .= "FROM TBLMESSAGES ";
		$query .= "INNER JOIN TBLUSER ON TBLUSER.PKUSRID = TBLMESSAGES.FROMUSRID ";	
		$query .= "WHERE TBLMESSAGES.FROMUSRID = '$this->userID' AND TBLMESSAGES.ISTRASHFROMUSR = 0 AND ISINBOXFROMUSR=1 AND UNREADINBOXFROMUSR=1 AND TBLMESSAGES.ISINBOXFROMUSR=1 AND TBLMESSAGES.ISROOT = 1 ";
			
		$query .= "ORDER BY UPDATEDDATE DESC";
			
		$result = executeQuery($query);
		$totalNewMessages = mysql_num_rows($result);
			
		$totalNewMessages = 2;	
		if($totalNewMessages > 0)
			$strResult = "Inbox(".$totalNewMessages.")";
		else
			$strResult = "Inbox";
			
		//$temp = $_SERVER['DOCUMENT_ROOT'];
			
		return $strResult;
	}
	
  /*public function UserInfo()
  {
  } */
   
   
 /*  public function GetArrayList()
   {
   	  $list = array(0=> '89',1=> 'Mehmet',2=> 'Male');
	  
	  return $list;
   }*/
   
 
}

?>