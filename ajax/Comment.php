<?php 

include($_SERVER['DOCUMENT_ROOT']."/Settings.php");	
require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Comment.class.php");

$processType= ($_GET['processType'])?$_GET['processType']:$_POST['processType'];

 switch($processType)
 {	
 	case "ADDCOMMENT":	//upload Instagram Photo
		addcomment();
		break;
	case "DELETECOMMENT":
		deleteComment();
		break;
	case "ADDREPORT":
		addreport();
		break;
 }
 
 function addreport(){
 	
	include($_SERVER['DOCUMENT_ROOT']."/Settings.php");			
	require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");
	
	$photoID = $_POST["photoID"];
	$userID = $_POST["userID"];
	$albumID = $_POST["albumID"];
	$report = $_POST["report"];
	$report = utf8_urldecode($report);
	$report = real_escape_string($report);	
	
	$result = array("success"	=>	false,	"error"	=>	"", "message" => "");	
	$commentObj = new Comment();
	$my_array = $commentObj->addReport($photoID,$userID,$report,$albumID);
	list($success,$error,$message) = array_values($my_array);
	$result = array("success" => $success, "error" => $error, "message" => $message);	
	echo json_encode($result);
	
 }
 
 function addcomment(){
 	
	include($_SERVER['DOCUMENT_ROOT']."/Settings.php");			
	require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");
	
	$photoID = $_POST["photoID"];
	$userID = $_POST["userID"];
	$owneremail = $_POST["owneremail"];
	$ownername = $_POST["ownername"];
	$ownername = utf8_urldecode($ownername);
	$ownername = real_escape_string($ownername);
	$comment = $_POST["comment"];
	$comment = utf8_urldecode($comment);
	$comment = real_escape_string($comment);
	$latitude = $_POST['latitude'];
	$longitude = $_POST['longitude'];
	
	@session_start();
	$timezone = stripslashes($_SESSION['usertimezone']);
	$timezone = str_replace('"',"",$timezone);	
	$userObject = $_SESSION['userObject'];	
	
	$result = array("success"	=>	false,	"error"	=>	"", "message" => "");
	
	$commentObj = new Comment();
	$my_array = $commentObj->addComment($photoID,$userID,$comment,$latitude,$longitude,$owneremail,$ownername);
	list($success,$error,$pkCommentID) = array_values($my_array);

	
	if($success){	
		$comments = $commentObj->getComments($photoID);		
       	$container = "<div class='panel-heading'> <h3 class='panel-title' style='text-align:left;'>Comments (".$commentObj->commentsize.")</h3></div>        
               		<table class='table table-bordered table-striped'><tbody>"; 
				 	foreach($comments as $comment){                 
                  	 	$container .= "<tr> <td style='text-align:left;'>";
						$container .= str_replace("\n", "<br>\n", $comment["COMMENT"]);	
                        $container .= "<h5 style='float:left;text-align:left; clear:both; margin:1px; margin-top:5px; padding:1px; color:#999; width:100%;'>";							  								   
						$container .= "posted by ".$comment["FULLNAME"]." ".getDateTime($comment["POSTDATE"],$timezone,false)." (".getDateTime($comment["POSTDATE"],$timezone,true).")";
					
						if($comment["FKUSERID"] == $userObject->userID || $userObject->isAdmin){
							$container .= "  <a class='blue' onClick='deleteComment(".$comment["PKCOMMENTID"].")' title='Delete this Comment' alt='Delete this Comment' >x</a>";
						}
					
						$container .= "</h5>";
						$container .= "</td></tr>";
					}
					$container .= "</table>"; 	
					
		$result = array("success" => true, "error" => $error, "message" => $container);
		
	}else{
		$result = array("success" => $success, "error" => $error, "message" => "Invalid Comment Addition");
	}
	
	
	echo json_encode($result);
	
 }
 
 function deleteComment(){
 	
	$pkCommentID = $_GET["pkCommentID"];
	$commentObj = new Comment($pkCommentID);
	$deleted =  $commentObj->deleteComment();
	$result = $deleted?array("success" => true, "error" => ""):array("success" => false, "error" => "Invalid Comment Deletion!");
	echo json_encode($result);
 }

?>