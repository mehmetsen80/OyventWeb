<?php 

$processType= ($_GET['processType'])?$_GET['processType']:$_POST['processType'];

 switch($processType)
 {
 	case "GETTAGCONTENTLIST":
		getTagContentList();
		break;
	case "GETONLYTAGIMAGELIST":
		getOnlyTagImageList();
		break;
 }
 
 function getTagContentList(){
	 
  require_once($_SERVER['DOCUMENT_ROOT']."/class/Instagram.class.php");
  require_once($_SERVER['DOCUMENT_ROOT']."/admin/Instagram.config.php");
  require_once($_SERVER['DOCUMENT_ROOT']."/class/Album.class.php");
  require_once($_SERVER['DOCUMENT_ROOT']."/class/UserInfo.class.php");
  require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");
 	
  $tag = $_GET['tag'];
  $maxID = $_GET['max_id'];
  $clientID = $instagram->getApiKey();
  
  
  @session_start(); 
  $userObject = $_SESSION["userObject"];
  $timezone = stripslashes($_SESSION['usertimezone']);

  $timezone = str_replace('"',"",$timezone);	
   
  
  $album = NULL;
  $albumID = $_GET["albumID"];
  if(isset($albumID) && !empty($albumID)){ 	   
	   $album = new Album($userObject->userID,$albumID);
  }
  
 
  $call = new stdClass();
  $call->pagination = new stdClass();
  $call->pagination->next_max_id = $maxID;
  $call->pagination->next_url =   "https://api.instagram.com/v1/tags/{$tag}/media/recent?client_id={$clientID}&max_tag_id={$maxID}";
 
  // Receive new data
  $media = $instagram->pagination($call,100);
  
  $content = "";
  foreach ($media->data as $data) {
	
	$caption = htmlentities(str_replace("'", "\'", $data->caption->text));//not used for now
	$createdtime = date("Y-m-d H:i:s",$data->created_time);
	$tags =  "[".implode(",", $data->tags)."]";
	$firstdate = GetDateDifference($createdtime,$timezone);
	$firstdate =  (isset($firstdate) && $firstdate!='')?$firstdate."<br>":"";
	  
  	$content .= "<li>
		<a  title='' rel='fancybox-thumb' href='{$data->images->standard_resolution->url}' class='fancybox-thumb' >
	<img class='media' src='{$data->images->low_resolution->url}' />
						   <div class='content'>
                           	<div class='avatar' style='background-image: url({$data->user->profile_picture}) '></div>
                           	<p>{$data->user->username}</p>
                           	<div class='comment'>{$firstdate}{$caption}</div>
                           </div>
						   </a>
						   
						   <div class='add2album'>						   
						  <a class='btn'  data-instagramid='{$data->id}' data-smallurl='{$data->images->thumbnail->url}' data-mediumurl='{$data->images->low_resolution->url}' data-largeurl='{$data->images->standard_resolution->url}' data-thumburl='{$data->images->thumbnail->url}'  data-ownedby='{$data->user->username}' data-createdtime='{$createdtime}' data-likes='{$data->likes->count}' data-contentlink='{$data->link}' data-tags='{$tags}' data-latitude='{$data->location->latitude}' data-longitude='{$data->location->longitude}' data-contenttype='{$data->type}'     data-caption='{$caption}'  onClick='addInstagramPhoto(this)' >Add to ".strtoupper($album->username)." </a>	
						   
						   
						   <div style='width:100%; padding:2px; text-align:right;'>								
									<a title='{$data->link}' href='{$data->link}' style='background:#ffffff;' ><img height='16' width='16' src='/images/instagram-icon-32.png' ></a>
							</div> 
							
						   </div>
						  </li>";
  }
  
   $content .= "<div class='cleardiv'></div>";
   $content .= "<div id='divMore_Inst_{$media->pagination->next_max_id}' > <a onClick='loadMoreInstagram(this)' class='btn' data-maxid='{$media->pagination->next_max_id}' data-tag='{$tag}'>Load More</a></div>";
  
  echo json_encode(array(
  		'premaxid' => $maxID,	
		'content' => $content,
		'success' => true
	));
	
 }
 
 function getOnlyTagImageList(){
	 
  require_once($_SERVER['DOCUMENT_ROOT']."/class/Instagram.class.php");
  require_once($_SERVER['DOCUMENT_ROOT']."/admin/Instagram.config.php");
 
  $tag = $_GET['tag'];
  $maxID = $_GET['max_id'];
  $clientID = $instagram->getApiKey();
  
 
  $call = new stdClass();
  $call->pagination = new stdClass();
  $call->pagination->next_max_id = $maxID;
  $call->pagination->next_url =   "https://api.instagram.com/v1/tags/{$tag}/media/recent?client_id={$clientID}&max_tag_id={$maxID}";
 
  // Receive new data
  $media = $instagram->pagination($call);
 
 // Collect everything for json output
  $images = array();
  foreach ($media->data as $data) {
   $images[] = $data->images->thumbnail->url;
  }
 
	echo json_encode(array(
		'next_id' => $media->pagination->next_max_id,
		'images' => $images,
		'success' => true
	));
 
 }

  

?>