<?php 


require_once($_SERVER['DOCUMENT_ROOT']."/class/Feed.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Comment.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Album.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Profile.class.php");

$processType= ($_GET['processType'])?$_GET['processType']:$_POST['processType'];


const MAX_TAG_CONTENT_LENGTH = 100;


 switch($processType)
 {
     case "GETPROFILEPHOTO": //mobile
		getProfilePhoto();
		break;
     case "GETFEEDLIST": //mobile
		getFeedList();
		break;
	case "DELETEFEED":
		deleteFeed();
		break;	
	case "GETCOMMENTS": //mobile
		getComments();
		break;
	case "ADDCOMMENT":	//mobile
		addcomment();
		break;
	case "DELETECOMMENT": //mobile
		deleteComment();
		break;
	case "DELETEPOST": //mobile
		deletePost();
		break;
	case "GETALLALBUMLISTNEARBY": //mobile
		getAllAlbumListNearBy();
		break;
	case "GETPARENTALBUMLISTNEARBY": //mobile
		getParentAlbumListNearBy();
		break;
	case "ADDTEXTONLY": //mobile
		addTextOnly();
		break;
		
 } 



 function getProfilePhoto(){
 	//get userID
	$userID = ($_GET["userID"])?$_GET["userID"]:$_POST["userID"];
	$result = array("success" => false, "error" => 'Invalid Profile!');
	
	if(isset($userID)){
		$profile = new Profile($userID);
		$profile->getProfilePhoto();
		$result = array("success" => true, "error" => '', 
		"PKUSERID" => doubleval($profile->pkUserID), 
		"FULLNAME" => $profile->fullname,
		"URLLARGE" => $profile->urllarge,
		"URLMEDIUM" => $profile->urlmedium,
		"URLSMALL" => $profile->urlsmall,
		"URLTHUMB" => $profile->urlthumb);
	}
	
	echo json_encode($result);
 }
 
 function getFeedList()
 {
	 
	 /*$feed_tracks = array(
    1 => array(
        "PKFEEDID" => 1,
        "CONTENT" => "127 Hours",
        "POSTDATE" => date()
		),
	2 => array(
        "PKFEEDID" => 2,
        "CONTENT" => "128 Hours",
        "POSTDATE" => date()
		),
	3 => array(
        "PKFEEDID" => 3,
        "CONTENT" => "129 Hours",
        "POSTDATE" => date()
		),
	4 => array(
        "PKFEEDID" => 4,
        "CONTENT" => "130 Hours",
        "POSTDATE" => date()
		)
);

$feeds = array();

// looping through each album
foreach ($feed_tracks as $feed) {
    $tmp = array();
    $tmp["PKFEEDID"] = $feed["PKFEEDID"];
    $tmp["CONTENT"] = $feed["CONTENT"];
    $tmp["POSTDATE"] = $feed["POSTDATE"];

    // push album
    array_push($feeds, $tmp);
}

// printing json
echo json_encode($feeds);*/

	//$count = ($_GET['count'])?$_GET['count']:$_POST['count'];
	$currentPage = ($_GET['currentPage'])?$_GET['currentPage']:$_POST['currentPage'];
	$currentPage = utf8_urldecode($currentPage);
	$userID = ($_GET["userID"])?$_GET["userID"]:$_POST["userID"];
	$userID = utf8_urldecode($userID);
	$lat = ($_GET["lat"])?$_GET["lat"]:$_POST["lat"];
	$lng = ($_GET["lng"])?$_GET["lng"]:$_POST["lng"];
	$albumID = ($_GET["albumID"])?$_GET["albumID"]:$_POST["albumID"];
	$fkParentID = ($_GET["fkParentID"])?$_GET["fkParentID"]:$_POST["fkParentID"];
	
	//$lat = 34.710098;
	//$lng = -92.354525926605;
	
	$currentTagID = ($_GET['currentTagID'])?$_GET['currentTagID']:$_POST['currentTagID'];

	$feeds = array();
	$feed = new Feed();	
	$feed->initFeedList($currentPage,$currentTagID,$userID,$lat,$lng,$albumID,$fkParentID);
	
	foreach ($feed->feedList as $sfeed) {
		$tmp = array();
    	/*$tmp["PKFEEDID"] = $sfeed["PKFEEDID"];
    	//$tmp["CONTENT"] =  substr($sfeed["CONTENT"],0,MAX_TAG_CONTENT_LENGTH);
		$tmp["CONTENT"] =  $sfeed["CONTENT"];
    	$tmp["POSTDATE"] = GetDateDifference($sfeed["POSTDATE"]);		
		$tmp["NAMETHUMB2"] = $sfeed["NAMETHUMB2"];
		$tmp["NAMETHUMB1"] = $sfeed["NAMETHUMB"];
		$tmp["NAMETHUMB"] = $sfeed["NAME"];
		$tmp["USERNAMETHUMB2"] = $sfeed["USERNAMETHUMB2"];
		$tmp["USERNAMETHUMB1"] = $sfeed["USERNAMETHUMB1"];
		$tmp["USERNAMETHUMB0"] = $sfeed["USERNAMETHUMB0"];
		$tmp["PHYSICALPATHTHUMB2"] = $sfeed["PHYSICALPATHTHUMB2"];
		$tmp["PHYSICALPATHTHUMB1"] = $sfeed["PHYSICALPATHTHUMB"];
		$tmp["PHYSICALPATHTHUMB"] = $sfeed["PHYSICALPATH"];
		$tmp["USERPHYSICALPATHTHUMB2"] = $sfeed["USERPHYSICALPATHTHUMB2"];
		$tmp["USERPHYSICALPATHTHUMB1"] = $sfeed["USERPHYSICALPATHTHUMB1"];
		$tmp["USERPHYSICALPATHTHUMB0"] = $sfeed["USERPHYSICALPATHTHUMB0"];		
		$tmp["FULLNAME"] = $sfeed["FULLNAME"];
		$tmp["USERNAME"] = $sfeed["USERNAME"];	*/
		
		$tmp["PKPHOTOID"] =  doubleval($sfeed["PKPHOTOID"]);
		$tmp["FKUSERID"] = doubleval($sfeed["FKUSERID"]);
		//$tmp["POSTDATE"] = GetDateDifference($sfeed["POSTDATE"]);
		$tmp["POSTDATE"] = $sfeed["POSTDATE"];
		$tmp["URLLARGE"] = $sfeed["URLLARGE"];
		$tmp["URLMEDIUM"] = $sfeed["URLMEDIUM"];
		$tmp["URLSMALL"] = $sfeed["URLSMALL"];
		$tmp["URLTHUMB"] = $sfeed["URLTHUMB"];
		$tmp["FULLNAME"] = $sfeed["FULLNAME"];
		$tmp["EMAIL"] = $sfeed["EMAIL"];
		$tmp["ALBUMNAME"] = $sfeed["ALBUMNAME"];
		$tmp["FKALBUMID"] = doubleval($sfeed["FKALBUMID"]);
		$tmp["OY"] =  ($sfeed["OY"] != NULL && $sfeed["OY"] != "")?intval($sfeed["OY"]):0;
		$tmp["LAT1"] =  ($sfeed["LAT1"] != NULL && $sfeed["LAT1"] != "")?floatval($sfeed["LAT1"]):0;
		$tmp["LONG1"] =  ($sfeed["LONG1"] != NULL && $sfeed["LONG1"] != "")?floatval($sfeed["LONG1"]):0;
		$tmp["LAT2"] =  ($sfeed["LAT2"] != NULL && $sfeed["LAT2"] != "")?floatval($sfeed["LAT2"]):0;
		$tmp["LONG2"] =  ($sfeed["LONG2"] != NULL && $sfeed["LONG2"] != "")?floatval($sfeed["LONG2"]):0;
		$tmp["FKTWITTERID"] = $sfeed["FKTWITTERID"];
		$tmp["FKINSTAGRAMID"] = $sfeed["FKINSTAGRAMID"];
		$tmp["FKFACEBOOKID"] = $sfeed["FKFACEBOOKID"];
		$tmp["CONTENTLINK"] = $sfeed["CONTENTLINK"];
		$tmp["OWNEDBY"] = $sfeed["OWNEDBY"];	
		$tmp["CAPTION"] = $sfeed["CAPTION"];
		//$tmp["CAPTION"] = 't';
		$tmp["CAPTION"] =  mysql_real_escape_string($sfeed["CAPTION"]);
		$tmp["CREATEDDATE"] = $sfeed["CREATEDDATE"];
		$tmp["TOTALCOMMENTS"] = intval($sfeed["TOTALCOMMENTS"]);
		//$tmp["HASVOTED"] =  boolval( $sfeed["HASVOTED"]);
		$tmp["HASVOTED"] = (bool)$sfeed["HASVOTED"];
		$tmp["DISTANCE"] = doubleval($sfeed["DISTANCE"]);
			

    	// push feed
    	array_push($feeds, $tmp);
	}
	
	$result["results"]= $feeds;
	
	echo json_encode($result);
 }
 
 function deleteFeed()
 {
	$feedID = ($_GET['feedID'])?$_GET['feedID']:$_POST['feedID'];
	$feedID = filter_data($feedID);	
	$results = array();
	$result = array("success"	=>	false,	"error"	=>	"System error, please try again!");
	
	if(isset($feedID) && $feedID != "")
	{	
		$feed = new Feed();
		$my_array =  $feed->deleteFeed($feedID);
		list($success,$error) = array_values($my_array);
		
		if($success)
		{
			$result = array("success" => true, 
							"error" => "", 
							"feedID" => $feedID,							
							"test1" => "ne haber"							
						);
		}
		else
		{
			$result = array("success" => $success, "error" => "Icerik Silme Hatasi:".$error);
		}		
	}
	else
	{
		$result = array("success"	=>	false,	"error"	=>	"Gecersiz kullanici adi veya gecersiz icerik!");
	}
	
	array_push($results, $result);	
	echo htmlspecialchars(json_encode($results), ENT_NOQUOTES);	
 } 
 
 function getComments(){
	 
	require_once($_SERVER['DOCUMENT_ROOT']."/class/Comment.class.php");
 	
	
	$photoID = ($_GET["photoID"])?$_GET["photoID"]:$_POST["photoID"];
	$commentObj = new Comment();
	$commentResult = $commentObj->getComments($photoID);	
	
	$comments = array();
	foreach ($commentResult as $comment) {
		$tmp = array();
		
		$tmp["PKCOMMENTID"] = doubleval($comment["PKCOMMENTID"]);
		$tmp["FKPHOTOID"] =  doubleval($comment["FKPHOTOID"]);
		$tmp["FKUSERID"] = doubleval($comment["FKUSERID"]);
		$tmp["FULLNAME"] = $comment["FULLNAME"];
		$tmp["COMMENT"] = $comment["COMMENT"];
		$tmp["LATITUDE"] =  ($comment["LATITUDE"] != NULL && $comment["LATITUDE"] != "")?floatval($comment["LATITUDE"]):0;
		$tmp["LONGITUDE"] =  ($comment["LONGITUDE"] != NULL && $comment["LONGITUDE"] != "")?floatval($comment["LONGITUDE"]):0;
		$tmp["POSTDATE"] = $comment["POSTDATE"];
		$tmp["EMAIL"] = $comment["EMAIL"];
		
		// push comment
    	array_push($comments, $tmp);
	}
	
	$result["results"]= $comments;
	
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
	
	$results = array();
	$commentObj = new Comment();
	$my_array = $commentObj->addComment($photoID,$userID,$comment,$latitude,$longitude,$owneremail,$ownername);
	list($success,$error,$pkCommentID) = array_values($my_array);
	$result =  ($success)? array("success" => true,"error" => ""): array("success" => false, "error" => "Comment error:".$error);

	
	echo json_encode($result);
	
 }
 
 
 function deleteComment(){
 	
	$pkCommentID = $_POST["pkCommentID"];
	$commentObj = new Comment($pkCommentID);
	$deleted =  $commentObj->deleteComment();
	$result = $deleted?array("success" => true, "error" => ""):array("success" => false, "error" => "Comment not deleted, please try again!");
	echo json_encode($result);
 }
 
 function deletePost(){
 	$pkPhotoID = $_POST["pkPhotoID"];
	$album = new Album();
	$result =  $album->deletePhoto($pkPhotoID)?array('success'=>true, "message" => "Post deleted successfully!"):array('success' => false, "message","Post not be deleted, please try again!");
	echo json_encode($result);
 }
 
 function getAllAlbumListNearBy(){
	 
	require_once($_SERVER['DOCUMENT_ROOT']."/class/Album.class.php");
 	
	$currentPage = ($_GET['currentPage'])?$_GET['currentPage']:$_POST['currentPage'];
	$userID = ($_GET["userID"])?$_GET["userID"]:$_POST["userID"]; //user ID is not being used for Album right, keep for future reference
	$lat = ($_GET["lat"])?$_GET["lat"]:$_POST["lat"];
	$lng = ($_GET["lng"])?$_GET["lng"]:$_POST["lng"];
	$pkAlbumID = ($_GET["pkAlbumID"])?$_GET["pkAlbumID"]:$_POST["pkAlbumID"];
	
	$albumObj = new Album();
	$albumResult = $albumObj->getAllAlbumListNearBy($currentPage,$lat,$lng,$pkAlbumID);	
	
	$albums = array();
	foreach ($albumResult as $album) {
		$tmp = array();
		
		$tmp["PKALBUMID"] = doubleval($album["PKALBUMID"]);
		$tmp["FKUSERID"] = doubleval($album["FKUSERID"]);
		$tmp["FKPARENTID"] = ($album["FKPARENTID"] != NULL)? doubleval($album["FKPARENTID"]):0;
		$tmp["FKCATEGORYID"] = ($album["FKCATEGORYID"] != NULL)? doubleval($album["FKCATEGORYID"]):0;
		$tmp["ALBUMNAME"] = $album["NAME"];
		$tmp["ALBUMUSERNAME"] = $album["USERNAME"];
		$tmp["PARENTNAME"] = $album["PARENTNAME"];
		$tmp["ADDRESS"] = $album["ADDRESS"];
		$tmp["LATITUDE"] =  ($album["LATITUDE"] != NULL && $album["LATITUDE"] != "")?floatval($album["LATITUDE"]):0;
		$tmp["LONGITUDE"] =  ($album["LONGITUDE"] != NULL && $album["LONGITUDE"] != "")?floatval($album["LONGITUDE"]):0;
		$tmp["POSTDATE"] = $album["POSTDATE"];
		$tmp["RADIUS"] = ($album["RADIOUS"] != NULL && $album["RADIOUS"] != "")?floatval($album["RADIOUS"]):0;
		$tmp["DISTANCE"] = ($album["DISTANCE"] != NULL)? doubleval($album["DISTANCE"]):0;
		$tmp["PHOTOSIZE"] = intval($album["PHOTOSIZE"]);
		$tmp["URLLARGE"] = $album["URLLARGE"];
		$tmp["URLMEDIUM"] = $album["URLMEDIUM"];
		$tmp["URLSMALL"] = $album["URLSMALL"];
		$tmp["URLTHUMB"] = $album["URLTHUMB"];
		$tmp["TOTALPHOTOSIZE"] = intval($album["TOTALPHOTOSIZE"]);
		// push album
    	array_push($albums, $tmp);
	}
	
	$result["results"]= $albums;
	
	echo json_encode($result);
 }
 
 function getParentAlbumListNearBy(){
 	require_once($_SERVER['DOCUMENT_ROOT']."/class/Album.class.php");
 	
	$currentPage = ($_GET['currentPage'])?$_GET['currentPage']:$_POST['currentPage'];
	$userID = ($_GET["userID"])?$_GET["userID"]:$_POST["userID"]; //user ID is not being used for Album right, keep for future reference
	$lat = ($_GET["lat"])?$_GET["lat"]:$_POST["lat"];
	$lng = ($_GET["lng"])?$_GET["lng"]:$_POST["lng"];
	
	$albumObj = new Album();
	$albumResult = $albumObj->getParentAlbumListNearBy($currentPage,$lat,$lng);	
	
	$albums = array();
	foreach ($albumResult as $album) {
		$tmp = array();
		
		$tmp["PKALBUMID"] = doubleval($album["PKALBUMID"]);
		$tmp["FKUSERID"] = doubleval($album["FKUSERID"]);
		$tmp["FKPARENTID"] = ($album["FKPARENTID"] != NULL)? doubleval($album["FKPARENTID"]):0;
		$tmp["FKCATEGORYID"] = ($album["FKCATEGORYID"] != NULL)? doubleval($album["FKCATEGORYID"]):0;
		$tmp["ALBUMNAME"] = $album["NAME"];
		$tmp["ALBUMUSERNAME"] = $album["USERNAME"];
		$tmp["PARENTNAME"] = $album["PARENTNAME"];
		$tmp["ADDRESS"] = $album["ADDRESS"];
		$tmp["LATITUDE"] =  ($album["LATITUDE"] != NULL && $album["LATITUDE"] != "")?floatval($album["LATITUDE"]):0;
		$tmp["LONGITUDE"] =  ($album["LONGITUDE"] != NULL && $album["LONGITUDE"] != "")?floatval($album["LONGITUDE"]):0;
		$tmp["POSTDATE"] = $album["POSTDATE"];
		$tmp["RADIUS"] = ($album["RADIOUS"] != NULL && $album["RADIOUS"] != "")?floatval($album["RADIOUS"]):0;
		$tmp["DISTANCE"] = ($album["DISTANCE"] != NULL)? doubleval($album["DISTANCE"]):0;
		$tmp["PHOTOSIZE"] = intval($album["PHOTOSIZE"]);
		$tmp["URLLARGE"] = $album["URLLARGE"];
		$tmp["URLMEDIUM"] = $album["URLMEDIUM"];
		$tmp["URLSMALL"] = $album["URLSMALL"];
		$tmp["URLTHUMB"] = $album["URLTHUMB"];
		$tmp["TOTALPHOTOSIZE"] = intval($album["TOTALPHOTOSIZE"]);
		// push album
    	array_push($albums, $tmp);
	}
	
	$result["results"]= $albums;
	
	echo json_encode($result);
 }
 
 function addTextOnly(){
 	
 }

?>