<?php 
/*if (!ini_get('display_errors')) {
    ini_set('display_errors', '1');
}*/


require_once($_SERVER['DOCUMENT_ROOT']."/checkuser.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/lib/Facebook/fbconfig.php');
require_once($_SERVER['DOCUMENT_ROOT']."/class/Album.class.php");
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


@session_start();


$tempAlbumID = $_GET["albumID"];
if($tempAlbumID == NULL || $tempAlbumID == ""){
 	@session_start();
 	if($_SESSION["region"] == "AR")
		$tempAlbumID = 34;
 	if($_SESSION["region"] == "NY")
		$tempAlbumID = 35;
}

$_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
if(!isset($tempAlbumID) || empty($tempAlbumID)){	
	header('Location: choose.php');
}


if(!isset($_SESSION['fb_token']))
	header("Location: facelogin.php");
	

FacebookSession::setDefaultApplication(FB_ID,FB_SECRET);

$fullname = "";

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
		
		//get photos		
		$limit = 24;
		$prepageid = 0;
		$pageid = $prepageid + 1;
		
		$fbalbumid = 0;
		if(isset($_GET['selFBAlbum'])){			
			$fbalbumid = $_GET['selFBAlbum'];			
		}
		
		//let's get the initial facebook photos by default
		$fbrequest = new FacebookRequest( $session, 
										'GET', 
										'/'.$fbid.'/photos?offset='.$prepageid.'&limit='.$limit);		
		//if facebook album selected, get photos of the facebook album
		if($fbalbumid >0 )
			$fbrequest = new FacebookRequest( $session, 
										'GET', 
										'/'.$fbalbumid.'/photos?offset='.$prepageid.'&limit='.$limit);
																
		$fbphotos = $fbrequest->execute()->getGraphObject()->asArray();
		
		
		//get facebook albums to fill the dropdown
		$fbrequest =  new FacebookRequest( $session, 
										'GET', 
										'/'.$fbid.'/albums/');
		$fbalbums = $fbrequest->execute()->getGraphObject()->asArray();
				
		
			
	}
}

	
	
	$tempalbum = NULL;
 	$iseligible = 0;
 	$distance = NULL;
 	if(isset($tempAlbumID) && !empty($tempAlbumID)){ 	 
		$tempalbum = new Album($userObject->userID,$tempAlbumID);
		$geo =  unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=".$_SERVER['REMOTE_ADDR']));
		$distance =  distance($geo["geoplugin_latitude"],$geo["geoplugin_longitude"],$tempalbum->latitude,$tempalbum->longitude,'M');
		$iseligible = $distance <= $tempalbum->radious?1:0;
	}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en" class="no-js"> 
<head>
	<title>My Facebook Photos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">    
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">  
     
    
    <link rel="stylesheet" href="/css/icons.css" />
	<!--[if lte IE 7]><script src="/js/lte-ie7.js"></script><![endif]-->
	
    
    <link href="/css/style.css" rel="stylesheet">
    <link href="/css/custom-theme/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">
    <!-- javascript -->
    <script src="/js/lib/jquery-1.10.2.js"></script>
    <script src="/js/lib/jquery-ui-1.10.4.custom.min.js"></script>
    <script src="/js/General.js"></script>
    <script src="/js/FacePhotos.js"></script>
    <script src="/js/modernizr.custom.js"></script>
    
    <!-- Add mousewheel plugin (this is optional) -->
<script type="text/javascript" src="/js/lib/fancybox/lib/jquery.mousewheel-3.0.6.pack.js"></script>

<!-- Add fancyBox -->
<link rel="stylesheet" href="/js/lib/fancybox/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
<script type="text/javascript" src="/js/lib/fancybox/jquery.fancybox.pack.js?v=2.1.5"></script>

<!-- Optionally add helpers - button, thumbnail and/or media -->
<link rel="stylesheet" href="/js/lib/fancybox/helpers/jquery.fancybox-buttons.css?v=1.0.5" type="text/css" media="screen" />
<script type="text/javascript" src="/js/lib/fancybox/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>
<script type="text/javascript" src="/js/lib/fancybox/helpers/jquery.fancybox-media.js?v=1.0.6"></script>
<link rel="stylesheet" href="/js/lib/fancybox/helpers/jquery.fancybox-thumbs.css?v=1.0.7" type="text/css" media="screen" />
<script type="text/javascript" src="/js/lib/fancybox/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>

</head>

<body class="menu-push">

<input type="hidden" id="txtUserID" value="<?php echo $userObject->userID ?>" >
<input type="hidden" id="txtEligible" value="<?php echo $iseligible ?>" >
<input type="hidden" name="txtAlbumID" id="txtAlbumID" value="<?php echo $tempAlbumID ?>" />
<input type="hidden" id="txtAlbumUsername" value="<?php echo $album->username ?>" >

<?php //include("menu.php"); ?>
        
        <div class="maincontainer">
			
			<div class="main">

				<?php include("../header.php"); ?>

				<section id="contentArea"><!-- This is where all the content goes -->
               
                
                	<div class="center">
                    
                    	<img src="/images/facebook.png" title="Facebook Logo">
        				<h1>My Facebook Photos <span style="font-weight:bold;"></span></h1>
 						<h3><?php echo $fullname ?></h3>  
						
                        
                    </div>
                	
                    
                    <div class="grid">    
                    
                   	<!--<h1>Selected Geo-Album<br><u><a class="red" href="/<?php echo $tempalbum->username; ?>"><?php echo $tempalbum->albumName ?> </a></u></h1><a href='#' class="btn" onClick='window.location.href="choose.php";return false;' ><i class='arrow_carrot-2right'></i> Change Geo-Album</a>
        <div class="cleardiv"></div>
        <br />-->
                    
        			<ul id="photos" style='padding:0px;margin:0px;'>
                    
                    <div class="cleardiv"></div>
                    <div class="cleardiv"></div>
                    
                    <div style='width:100%;clear:both; margin:20px; text-align:center; '>
                    <form action="facephotos.php" method="get" >
                    <input type="hidden" name="albumID" id="albumID" value="<?php echo $tempAlbumID ?>" />
                    <select onChange="this.form.submit()" class="select" id="selFBAlbum"  name="selFBAlbum">
        					<option <?php echo $fbalbumid==0?"selected":"";  ?> value="0">Facebook Photos</option>  
                            <?php 
					
							foreach($fbalbums['data'] as $fbalbum)
							{ ?>
				 				<option <?php echo $fbalbumid==$fbalbum->id?"selected":"";  ?> value="<?php echo $fbalbum->id; ?>"><?php echo $fbalbum->name; ?></option>
					<?php 	} ?>
                                                      
                    </select>&nbsp; </form></div>
                    
         <?php 
					
					//print_r($fbalbums['data']);
					//$fbalbums_data = $fbalbums->getProperty('data');
					/*foreach ($fbalbums['data'] as $fbalbum) {
    					echo $fbalbum->name;
					}*/
					 //$fbphotos_data = $fbphotos['data'];
					 //$photos = $fbphotos_data->images;
					/*foreach ($fbphotos_data as $photo) {
    					echo $photo->source."  ".$photo->images[0]->source;
						echo "<br><br>";
					}*/
									
					$fbphotos_data = $fbphotos['data'];
						
          			foreach ($fbphotos_data as $photo) {
            			$createdtime = date('Y-m-d H:i:s', strtotime($photo->created_time));
						$firstdate = GetDateDifference($createdtime,$timezone);
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
							<img class='media' src='{$photo->source}'  /> 
						    <div class='content'>
                           		<div class='avatar' style='background-image: url({$photo->picture}) '></div>
                           		<p>{$photo->from->name}</p>
                           		<div class='comment'>{$firstdate}{$comment}</div>
                           	</div>
						  	</a>
						  
						  	<div class='add2album'>
						  		<a class='btn'  data-fbid='{$photo->id}' data-contentlink='{$photo->link}' data-largeurl='{$photo->source}' data-latitude='{$photo->place->location->latitude}' data-longitude='{$photo->place->location->longitude}' data-mediumurl='{$photo->source}' data-smallurl='{$photo->source}' data-thumburl='{$photo->source}' data-caption='{$photo->name}'  data-createdtime='{$createdtime}' data-ownedby='{$photo->from->id}' onClick='addFacebookPhoto(this)' >
						  		Add to ".strtoupper($tempalbum->username)." 
						  		</a>
								<div style='width:100%; padding:2px; text-align:right;'>									
									<a title='{$photo->link}' href='{$photo->link}' style='background:#ffffff;' ><img height='16' width='16' src='/images/facebook-icon-32.png' ></a>
								</div> 					  
						  	</div>						 
							</li>";
						}											
					}					
					
					
					$content .= "<div class='cleardiv'></div>";
					$content .= "<div id='divMore_Face_{$pageid}' ><a onClick='loadMoreFacebook(this)' class='btn' data-limit='{$limit}' data-pageid='{$pageid}'>Load More</a></div>";
					
					echo $content;
					
					?>
        			</ul>
        			</div>
                    
                
                 </section>
			</div>
		</div>        
        

    <?php
   include("footerthumbs.php");
   include("footer.php");
   ?>
  
  <div class="modal" id="dgModal"><div id="loading"></div></div>


</body>
</html>