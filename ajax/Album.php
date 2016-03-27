<?php 

require_once($_SERVER['DOCUMENT_ROOT']."/class/UserInfo.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Album.class.php");

$processType= ($_GET['processType'])?$_GET['processType']:$_POST['processType'];

@session_start();
$userObject = NULL;

if (isset($_SESSION['userObject']))
		$userObject = $_SESSION['userObject'];

 switch($processType)
 {
	 case "GETALBUMLIST":
		 getAlbumList();
		 break;
 	case "CREATEALBUM":
		createAlbum();
		break;
	case "UPDATEALBUM":
		updateAlbum();
		break;
	case "DELETEALBUM":
		deleteAlbum();
		break;
	case "GETLATESTPHOTOTHUMBS":
		getLatestPhotoThumbs();
		break;
	case "GETLATESTPHOTOMEDIUMS":
		getLatestPhotoMediums();
		break;
	case "SHOWMOREALBUMPHOTOS":
		showMoreAlbumPhotos();
		break;
	case "VOTEUP":
		voteup();
		break;
	case "VOTEDOWN":
		votedown();
		break;
 }

 function getAlbumList(){

	 $albumObj = new Album(NULL,NULL);
	 $albumlist = $albumObj->getAlbumList("NAME asc",20);

	 $albums = array();
	 foreach ($albumlist as $album) {
		 $tmp = array();
		 $tmp["PKALBUMID"] =  doubleval($album["PKALBUMID"]);
		 $tmp["FKUSERID"] = doubleval($album["FKUSERID"]);
		 $tmp["FKPARENTID"] = ($album["FKPARENTID"] != NULL)? doubleval($album["FKPARENTID"]):0;
		 $tmp["ALBUMNAME"] = $album["NAME"];
		 $tmp["ALBUMUSERNAME"] = $album["USERNAME"];
		 $tmp["ADDRESS"] = $album["ADDRESS"];
		 $tmp["PRIVACY"] = $album["PRIVACY"];
		 $tmp["LAT1"] =  ($album["LATITUDE"] != NULL && $album["LATITUDE"] != "")?floatval($album["LATITUDE"]):0;
		 $tmp["LONG1"] =  ($album["LONGITUDE"] != NULL && $album["LONGITUDE"] != "")?floatval($album["LONGITUDE"]):0;
		 $tmp["RADIUS"] =  ($album["RADIOUS"] != NULL && $album["RADIOUS"] != "")?floatval($album["RADIOUS"]):0;
		 $tmp["URLLARGE"] = $album["URLLARGE"];
		 $tmp["URLMEDIUM"] = $album["URLMEDIUM"];
		 $tmp["URLSMALL"] = $album["URLSMALL"];
		 $tmp["URLTHUMB"] = $album["URLTHUMB"];
		 $tmp["POSTDATE"] = $album["POSTDATE"];

		 // push feed
		 array_push($albums, $tmp);
	 }

	 $result["results"]= $albums;

	 echo json_encode($result);
 }
 
 function createAlbum(){

	require_once($_SERVER['DOCUMENT_ROOT']."/class/Album.class.php");
	include($_SERVER['DOCUMENT_ROOT']."/Settings.php");	
	require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/class/Regex.class.php");
 
 	$albumname = $_GET["albumname"];
	$albumame = utf8_urldecode($albumname);
	$albumame = real_escape_string($albumname);
	$albumname = addslashes($albumname);
	
	$privacy = $_GET["privacy"];
	$privacy = utf8_urldecode($privacy);
	$privacy = real_escape_string($privacy);	
	
	$username = $_GET["username"];
	$username = utf8_urldecode($username);
	$username = real_escape_string($username);	
	$username = strtolower($username);
		
	$result = array("success" => false, "error" => $username, "pkAlbumID" => '');
	
	
	$userID = $_GET["userID"];
	$userID = utf8_urldecode($userID);
	$userID = real_escape_string($userID);
	
		
	if(isset($albumame) && isset($userID) && isset($privacy) && isset($username))
	{	
		$album = new Album($userID);
		
		if(!Regex::isUsernameLegal($username)){
  			$result = array("success"=>false,"error"=>"Please enter a valid album address!", "pkAlbumID" => '');
		}else{
			$tempID = $album->getAlbumIDFromUsername($username);
			
			if(isset($tempID)){
				$result = array("success" => false, "error" => "Album address already taken!", "pkAlbumID" => '');
			}else{
				$my_array = $album->createAlbum($albumame,$privacy,$username);		
				list($success,$error,$pkAlbumID) = array_values($my_array);
	
				if($success) 
					$result = array('success'	=>	true, 'error' => false, 'pkAlbumID' => $pkAlbumID);			
				else 
					$result = array("success" => $success, "error" => $error, "pkAlbumID" => $pkAlbumID);			
			}
		}
		
	}
	
	echo json_encode($result); 
 }
 
 function updateAlbum(){

	require_once($_SERVER['DOCUMENT_ROOT']."/class/Album.class.php");
	include($_SERVER['DOCUMENT_ROOT']."/Settings.php");	
	require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/class/Regex.class.php");
 
 	$albumname = $_GET["albumname"];
	$albumame = utf8_urldecode($albumname);
	$albumame = real_escape_string($albumname);
	$albumname = addslashes($albumname);
	
	$privacy = $_GET["privacy"];
	$privacy = utf8_urldecode($privacy);
	$privacy = real_escape_string($privacy);
	
	$username = $_GET["username"];
	$username = utf8_urldecode($username);	
	$username = real_escape_string($username);
	$username = strtolower($username);
		
	$result = array("success" => false, "error" => $username, "pkAlbumID" => '');
	
	
	$userID = $_GET["userID"];
	$userID = utf8_urldecode($userID);
	$userID = real_escape_string($userID);
	
	$albumID = $_GET["albumID"];
	$albumID = utf8_urldecode($albumID);
	$albumID = real_escape_string($albumID);
	
		
	if(isset($albumame) && isset($userID) && isset($privacy) && isset($username) && isset($albumID))
	{	
		$album = new Album($userID);
		
		if(!Regex::isUsernameLegal($username)){
  			$result = array("success"=>false,"error"=>"Please enter a valid album address!", "pkAlbumID" => '');
		}else{
			$tempID = $album->getAlbumIDFromUsername($username,$albumID);
			
			if(isset($tempID)){
				$result = array("success" => false, "error" => "Album address already taken!", "pkAlbumID" => '');
			}else{
				$my_array = $album->updateAlbum($albumame,$privacy,$username,$albumID);		
				list($success,$error,$pkAlbumID) = array_values($my_array);
	
				if($success) 
					$result = array('success'	=>	true, 'error' => false, 'pkAlbumID' => $pkAlbumID);			
				else 
					$result = array("success" => $success, "error" => $error, "pkAlbumID" => $pkAlbumID);			
			}
		}
		
	}
	
	echo htmlspecialchars(json_encode($result), ENT_NOQUOTES); 
 }
 
 function deleteAlbum(){
 	require_once($_SERVER['DOCUMENT_ROOT']."/class/Album.class.php");
	
	$userID = $_GET["userID"];	
	$albumID = $_GET["albumID"];	
	
	$result = array("success" => false, "error" => "No valid delete input, please try again!");
	
	if(isset($userID) && isset($albumID))
	{
		$album = new Album($userID,$albumID);
		
		if($album->photosize == 0){
			
			$deleted = $album->deleteAlbum();
			
			if($deleted)
				$result = array("success" => true, "error" => "");
			else
				$result = array("success" => false, "error" => "System deletion error, please try again!");
			
		}
		else if($album->photosize > 0){
			$result = array("success" => false, "error" => "Please first delete your photos from this album!");
		}
	}
	
	echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
	
 }
 
 function getLatestPhotoThumbs(){
 
 	require_once($_SERVER['DOCUMENT_ROOT']."/class/Album.class.php");
	include($_SERVER['DOCUMENT_ROOT']."/Settings.php");	
	
	$userID = $_GET["userID"];
	$limit = $_GET["limit"];
	
	$photos = NULL;
	if(isset($userID)){
		$album = new Album($userID);
		$photos = $album->getLatestPhotoThumbs($limit);
	}
	
	$result = array("success"	=>	false,	"message"	=>	"No photo found!");
	
	$message = "";
	foreach($photos as $photo){
		//$src = $sitepath.'/'.$photo['PHYSICALPATH']."/".$photo['NAMETHUMB'];
		//$message .= "<div class='thumb' ><a href='album.php?albumID=".$photo['PKALBUMID']."'><img src='{$photo['URLTHUMB']}' alt='".$photo['ALBUMNAME']."' title='".$photo['ALBUMNAME']."' ></a></div>";
		
	$src = (isset($photo["FKPARENTID"]))? $sitepath."/".$photo['USERNAME']."/".$photo["PKALBUMID"] : $sitepath."/".$photo['USERNAME'];
		
		
	$message .= "<div class='thumb' ><a href='".$src."'><img src='".$photo['URLTHUMB']."' alt='".$photo['ALBUMNAME']."' title='".$photo['ALBUMNAME']."' ></a></div>";
	}
	
	if(!empty($message))
		$result = array("success"	=>	false,	"message"	=>	$message);
 
 	echo json_encode($result);
 	//echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
 }
 
 function getLatestPhotoMediums(){
 
 }

 function showMoreAlbumPhotos(){
 	
	require_once($_SERVER['DOCUMENT_ROOT']."/class/Album.class.php");
	include($_SERVER['DOCUMENT_ROOT']."/Settings.php");	
	require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/class/Subject.class.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/class/UserInfo.class.php");
	
	
	@session_start();
	$userObject = isset($_SESSION['userObject'])?$_SESSION['userObject']:NULL;
	
	$albumID= $_GET["albumID"];
	$subjectID = $_GET["subjectID"];
	$rowat = $_GET["rowat"];
	$userID = $_GET["userID"];
	$latitude = $_GET["latitude"];
	$longitude = $_GET["longitude"];
	$ismine = $_GET["ismine"];
	$ismostrated = $_GET["ismostrated"];
	
	$radious = 1.8;
	$distance = 1.1;
	$album = NULL;
	if(isset($albumID)  && $userObject){
		$album = new Album($userID,$albumID);	
		$photos = $album->getLatestPhotoMediums($rowat,100,$ismine,$subjectID,$ismostrated);
		$distance =  distance($latitude,$longitude,$album->latitude,$album->longitude,'M');
		$radious = $album->radious;
		$distance =  number_format($distance,2)." mi";
		$radious = number_format($radious,2);
	}
	
	$subject = NULL;	
	$subject = isset($subjectID) && !empty($subjectID)? new Subject($subjectID):NULL;
	
	$rowat = $rowat + 100;
	
	if(isset($rowat))
	{	
		$content = "";
		
		
		$timezone = stripslashes($_SESSION['usertimezone']);
		$timezone = str_replace('"',"",$timezone);	
		
		
		foreach($photos as $photo){
			
			//$firstdate = GetDateDifference($photo['CREATEDDATE']);
			//$firstdate = $photo['POSTDATE'];				
			//$firstdate =  (isset($firstdate) && $firstdate!='')?$firstdate."<br>":"";
			
			
			
			$strSocialMedia = "";
					if(isset($photo['FKINSTAGRAMID'])) 
						$strSocialMedia .= "<a title='".$photo['CONTENTLINK']."' href='".$photo['CONTENTLINK']."' style='background:#ffffff; padding-right:2px;' ><img height='16' width='16' src='/images/instagram-icon-32.png' ></a>";	
					else if(isset($photo['FKTWITTERID'])) 
						$strSocialMedia .= "<a title='".$photo['CONTENTLINK']."' href='".$photo['CONTENTLINK']."' style='background:#ffffff;padding-right:2px;' ><img height='16' width='16' src='/images/twitter-icon-32.png' ></a>";
					else if(isset($photo['FKFACEBOOKID'])) 
						$strSocialMedia .= "<a title='".$photo['CONTENTLINK']."' href='".$photo['CONTENTLINK']."' style='background:#ffffff;padding-right:2px;' ><img height='16' width='16' src='/images/facebook-icon-32.png' ></a>";						
					
					if(isset($photo["FKALBUMID"])){
						$strSocialMedia .= "  <a class='blue' style='font-size:14px;' title='".$photo['ALBUMNAME']."' href='".$sitepath."/".$album->username."/".$photo["FKALBUMID"]."' ><i class='icon_tag_alt'></i> ".substr($photo['ALBUMNAME'],0,23)."</a>";
					}
					
					
					
					
					$strDelDown = "<div class='icon-left' style='font-size:16px;'>";					
					$strDelDown .= " <input type='checkbox'  name='cbxphoto' id='cbxphoto_".$photo["PKPHOTOID"]."' value='".$photo["PKPHOTOID"]."' > ";
					$strDelDown .= "<a  class='blue' style='margin-left:15px;' alt='go to comment page' title='go to comment page'  href='/admin/feed.php?photoID=".$photo["PKPHOTOID"]."' ><i class='icon_comment'></i> (".$photo["TOTALCOMMENTS"].")</a>" ; 
					$strDelDown .= "</div>";	
					
					
					
					
					//oys
					$strDelDown .= "<div class='icon-right'>";
					$strDelDown .= "<div style='float:left;font-size:16px;padding-right:2px;margin-right:2px;' id='oys_".$photo['PKPHOTOID']."' >";						
					$hasvoted = $album->hasVoted($photo['PKPHOTOID'],$userObject->userID);						
					if(!$hasvoted){						
						$strDelDown .= "<a class='blue' style='float:left;font-size:20px;' data-photoid='".$photo['PKPHOTOID']."' title='+1' alt='+1'  onClick='voteup(this)' > <i class='arrow_triangle-up'></i></a>";
					}					
					$oys =  $photo["OY"] <= 0 ?$photo["OY"]." oys":"+".$photo["OY"]." oys";						
					$strDelDown .= "<div  style='float:left;font-size:20px;padding-right:2px;margin-right:2px;' >".$oys."</div>";
					if(!$hasvoted){
						$strDelDown .= "<a class='blue' style='font-size:20px;' data-photoid='".$photo['PKPHOTOID']."' title='-1' alt='-1'  onClick='votedown(this)' ><i class='arrow_triangle-down'></i></a>";	
					}
					$strDelDown .= "</div>";																	
					if(isset($userObject) && ($photo['FKUSERID'] == $userObject->userID) || $userObject->isAdmin){						
						$strDelDown .= "<a class='blue'  style='padding:0px;' data-photoid='".$photo['PKPHOTOID']."' onClick='deletePhoto(this)' alt='delete photo' title='delete photo'>x</a>
						  ";
				 	}			
					$strDelDown .= "</div>";
					
					
					
					
					
					
					//social media						
					$strDelDown .= "<div class='icon-left' >".$strSocialMedia."</div>";	
					//miles	
					$strDelDown .= "<div class='icon-right' style='font-size:14px;margin-top:8px;padding-top:3px;'>".number_format(distance($photo['LAT1'],$photo['LONG1'],$photo['LAT2'],$photo['LONG2'],'M'),2)." mi</div>"; 
					
					
					$ownedby = isset($photo["OWNEDBY"])&&$photo["OWNEDBY"]!=""?"<p>owned by @".$photo["OWNEDBY"]."</p>":"";
					$isverified = $photo["ISVERIFIED"]?"<img src='/images/ok.png' width='16' height='16' >":"";
					$caption =  $photo['CAPTION'];
					$caption = '';
					
					$content .= "<li>
					   <a rel='fancybox-thumb' title=\"{$caption}\" href='{$photo['URLLARGE']}' class='fancybox-thumb' >
						   
						   <img class='media' src='{$photo['URLMEDIUM']}' />
						   <div class='content' style='z-index:2'>".
                           	//<div class='avatar' style='background-image: url({$photo['URLTHUMB']}) '></div>
							"<p>added by ".$photo["FULLNAME"]." ".$isverified."</p>".
							$ownedby.
                           "<div class='comment'>posted on<br>".getDateTime($photo["POSTDATE"],$timezone,false)." (".getDateTime($photo["POSTDATE"],$timezone,true).")															
							</div>
                           </div>
						   </a>
						    
						   <div class='iconcnt'>".$strDelDown."</div>
						   
						   </li>";	
						   
						  /* $result = array("success"	=>	true,	"message"	=>	$strDelDown);
 					echo json_encode($result);
					return;*/
			
		}//foreach
		
		if($content != ""){
			$content .= "<div class='loadmore'  id='load_$rowat'><input class='loadmorebtn' type='button' onClick='showMoreAlbumPhotos($rowat)' value='Load More Photos'></div>";
		}else{
			$content = "no more photos";
		}
		
				  
		$result = array("success"	=>	true,	"message"	=>	$content);
 		echo json_encode($result);
		
	}//if rowat is not null
	
 }
 
 function voteup(){	
	require_once($_SERVER['DOCUMENT_ROOT']."/class/Album.class.php");
 	
	$userID = ($_GET["userID"])?$_GET["userID"]:$_POST["userID"];
	$pkPhotoID = ($_GET["pkPhotoID"])?$_GET["pkPhotoID"]:$_POST["pkPhotoID"];
	
	$album = new Album($userID);
	
	$my_array = $album->vote($userID,$pkPhotoID,1);		
	list($success,$error,$message,$already) = array_values($my_array);
	
	if($success) 
		$result = array('success'	=>	true, 'error' => false, 'message' => $message, 'already' => $already);			
	else 
		$result = array("success" => $success, "error" => $error, "message" => $message,'already' => $already);	
		
	echo json_encode($result);
	
 }
 
 function votedown(){	
	require_once($_SERVER['DOCUMENT_ROOT']."/class/Album.class.php");
 	
	$userID = ($_GET["userID"])?$_GET["userID"]:$_POST["userID"];
	$pkPhotoID = ($_GET["pkPhotoID"])?$_GET["pkPhotoID"]:$_POST["pkPhotoID"];
	
	$album = new Album($userID);
	
	$my_array = $album->vote($userID,$pkPhotoID,-1);		
	list($success,$error,$message,$already) = array_values($my_array);
	
	if($success) 
		$result = array('success'	=>	true, 'error' => false, 'message' => $message, 'already' => $already);			
	else 
		$result = array("success" => $success, "error" => $error, "message" => $message, 'already' => $already);	
		
	echo json_encode($result);
	
 }


?>