<?php 

require_once($_SERVER['DOCUMENT_ROOT'].'/lib/Facebook/fbconfig.php');
require_once($_SERVER['DOCUMENT_ROOT']."/class/Album.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/UserInfo.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");

require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/FacebookSession.php' );
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/FacebookRedirectLoginHelper.php' );
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/FacebookRequest.php' );
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/FacebookResponse.php' );
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/FacebookSDKException.php' );
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/FacebookRequestException.php' );
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/FacebookAuthorizationException.php' );
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/GraphObject.php' );
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/GraphSessionInfo.php' );
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/GraphUser.php' );
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/Entities/AccessToken.php' );
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/HttpClients/FacebookHttpable.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/HttpClients/FacebookCurl.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/HttpClients/FacebookCurlHttpClient.php');



use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\GraphSessionInfo;
use Facebook\GraphUser;
use Facebook\Entities\AcessToken;
use Facebook\HttpClients\FacebookHttpable;
use Facebook\HttpClients\FacebookCurl;
use Facebook\HttpClients\FacebookCurlHttpClient;

$processType= ($_GET['processType'])?$_GET['processType']:$_POST['processType'];

 switch($processType)
 {
 	case "GETFACEBOOKPHOTOLIST":
		getFacebookPhotoList();
		break;
 }
 
 function getFacebookPhotoList(){
 	

@session_start();

$album = NULL;
$albumID = $_GET["albumID"];
if(isset($albumID) && !empty($albumID)){ 	   
	$album = new Album($userObject->userID,$albumID);
}

if(!isset($_SESSION['fb_token']))
	header("Location: facelogin.php");
	
$timezone = stripslashes($_SESSION['usertimezone']);
$timezone = str_replace('"',"",$timezone);	

FacebookSession::setDefaultApplication(FB_ID,FB_SECRET);

$fullname = "";

$fbalbumid = $_GET['fbalbumid'];
$limit = $_GET['limit'];
$pageid = $_GET['pageid'];	
$nextpageid = $pageid + 1;		
$offset =  $pageid * $limit;
$content = "";

if(isset($_SESSION['fb_token'])){
	$session = new FacebookSession($_SESSION['fb_token']);
	
	try{
		if ( !$session->validate($id,$secret) ) {
			$session = NULL;
		}
	}catch( FacebookAuthorizationException $ex){
		$session = NULL;
		//echo $ex->getMessage();
	}
	
	
	
	//login successful
	if ( isset( $session ) ) {	
		
		// graph api request for user data
  		//echo "Login Successful<br>";
  		$request = new FacebookRequest( $session, 'GET', '/me' );
  		$response = $request->execute();
  		// get response
  		$graphObject = $response->getGraphObject(GraphUser::className());  
		$fullname = $graphObject->getName();
		$fbid = $graphObject->getID();
				
		
		$fbrequest = new FacebookRequest( $session, 
										'GET', 
										'/'.$fbid.'/photos?offset='.$offset.'&limit='.$limit);
		
		//if facebook album selected, get photos of the facebook album
		if($fbalbumid >0 )
			$fbrequest = new FacebookRequest( $session, 
										'GET', 
										'/'.$fbalbumid.'/photos?offset='.$offset.'&limit='.$limit);
										
										
		$fbresponse = $fbrequest->execute();
		$fbphotos = $fbresponse->getGraphObject()->asArray();
		
		$fbphotos_data = $fbphotos['data'];
						
         foreach ($fbphotos_data as $photo) {
            
			$createdtime = date('Y-m-d H:i:s', strtotime($photo->created_time));
			$firstdate = GetDateDifference($createdtime, $timezone);
			$firstdate =  (isset($firstdate) && $firstdate!='')?$firstdate."<br>":"";
						
			if(isset($photo->source)){		
				$comments = $photo->comments->data;
				//print_r($comments);
				//$comments = $comments['data'];	
							
				$comment = "";
				if(sizeof($comments>0))
					$comment = $comments[0]->message;							
										
					$content  .= "<li> 
					<a  title='{$photo->from->name}' rel='fancybox-thumb' href='{$photo->images[0]->source}' class='fancybox-thumb' >
					<img class='media' src='$photo->source'  /> 
						    <div class='content'>
                           		<div class='avatar' style='background-image: url({$photo->picture}) '></div>
                           		<p>{$photo->from->name}</p>
                           		<div class='comment'>{$createdtime}{$comment}</div>
                           	</div>
						  	</a>
						  
						  	<div class='add2album'>
						  		<a class='btn'  data-fbid='{$photo->id}' data-contentlink='{$photo->link}' data-largeurl='{$photo->source}' data-latitude='{$photo->place->location->latitude}' data-longitude='{$photo->place->location->longitude}' data-mediumurl='{$photo->source}' data-smallurl='{$photo->source}' data-thumburl='{$photo->source}' data-caption='{$photo->name}'  data-createdtime='{$createdtime}' data-ownedby='{$photo->from->id}' onClick='addFacebookPhoto(this)' >
						  		Add to ".strtoupper($album->username)." Geo-Album
						  		</a>
								<div style='width:100%; padding:2px; text-align:right;'>								
									<a title='{$photo->link}' href='{$photo->link}' style='background:#ffffff;' ><img height='16' width='16' src='/images/facebook-icon-32.png' ></a>
								</div> 					  
						  	</div>						 
							</li>";
			}											
		}					
		
			
					
		$content .= "<div class='cleardiv'></div>";
		$content .= "<div id='divMore_Face_{$nextpageid}' ><a onClick='loadMoreFacebook(this)' class='btn' data-limit='{$limit}' data-pageid='{$nextpageid}'>Load More</a></div>";
		
	}
	
}

echo json_encode(array(
  		'prepageid' => $pageid,	
		'content' => $content,
		'success' => true
	));
		
 }

?>