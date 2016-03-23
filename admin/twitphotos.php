<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/checkuser.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/TwitterAPIExchange.php");
require_once($_SERVER['DOCUMENT_ROOT']."/admin/Twitter.config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Album.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");

$twitdetails = $userObject->twitdetails;

/*echo $userObject->userID."  ".$twitdetails->screen_name;
echo "<br>";
echo print_r($twitdetails);
echo "<br>";

if(isset($twitdetails))
echo "not null:".$twitdetails->id;
else
echo "twitdetails is null";*/
				

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
	

	if(!isset($twitdetails))
	header("Location: twitlogin.php");

	
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
	<title>My Twitter Photos</title>
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
    <script src="/js/TwitPhotos.js"></script>
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

<?php include("menu.php"); ?>
        
        <div class="container">
			
			<div class="main">
				<section id="contentArea"><!-- This is where all the content goes -->
               
                
                	<div class="center">
                    
                    	<img src="/images/twitter.png" title="Twitter Logo">
        				<h1>My Twitter Photos <span style="font-weight:bold;"></span></h1>
 						<h3>[<?php echo $twitdetails->name ?>] [@<?php  echo $twitdetails->screen_name ?>]</h3>  
						                       
                    </div>
                	
                    
                    <div class="grid">    
                    
                    <!--<h1>Selected Geo-Album<br><u><a class="red" href="/<?php echo $tempalbum->username; ?>"><?php echo $tempalbum->albumName ?> </a></u></h1><a href='#' class="btn" onClick='window.location.href="choose.php";return false;' ><i class='arrow_carrot-2right'></i> Change Geo-Album</a>
        				<div class="cleardiv"></div>
        				<br />-->
                    
        			<ul id="photos" style='padding:0px;margin:0px;'>
         <?php 
					
					/*$url = 'https://api.twitter.com/1.1/search/tweets.json';
					$requestMethod = 'GET';
					$getfield = '?q=@'.$twitdetails->screen_name.'%20filter:images&result_type=recent&count=100&include_entities=true';	*/
					
					$url= 'https://api.twitter.com/1.1/statuses/user_timeline.json';
					$requestMethod = 'GET';
					$getfield =	'?user_id='.$twitdetails->id.'&count=100&screen_name='.$twitdetails->screen_name.'&exclude_replies=true&include_rts=true';			
					
					

					// Perform the request
					$twitter = new TwitterAPIExchange($settings);
					$response = $twitter->setGetfield($getfield)
             					 ->buildOauth($url, $requestMethod)
             					 ->performRequest();								
								 
					$content = "";
					$tweets = json_decode($response);				
					
					$max_id = "";					
          			foreach ($tweets as $tweet) {
						
						$createdtime = date('Y-m-d H:i:s', strtotime($tweet->created_at));
						$caption = htmlentities(str_replace("'", "\'", $tweet->text));//not used for now	
						$firstdate = GetDateDifference($createdtime,$timezone);
						$firstdate =  (isset($firstdate) && $firstdate!='')?$firstdate."<br>":"";
						$contentlink = "http://twitter.com/".$tweet->user->screen_name."/status/".$tweet->id."/";
            			
						 if(isset($tweet->entities->media[0]->media_url)){						
						 	$content  .= "<li> 
						 	<a  title='{$tweet->text}' rel='fancybox-thumb' href='{$tweet->entities->media[0]->media_url}' class='fancybox-thumb' >
							<img class='media' src='{$tweet->entities->media[0]->media_url}'  /> 
						    <div class='content'>
                           		<div class='avatar' style='background-image: url({$tweet->user->profile_image_url}) '></div>
                           		<p>@{$tweet->user->screen_name}</p>
                           		<div class='comment'>{$firstdate}{$tweet->entities->urls->expanded_url} {$tweet->text}  </div>
                           	</div>
						  	</a>
						  
						  	<div class='add2album'>
						  		<a class='btn'  data-twitterid='{$tweet->id}' data-largeurl='{$tweet->entities->media[0]->media_url}:large' data-mediumurl='{$tweet->entities->media[0]->media_url}' data-smallurl='{$tweet->entities->media[0]->media_url}:small' data-thumburl='{$tweet->entities->media[0]->media_url}:thumb' data-caption='{$caption}' data-createdtime='{$createdtime}' data-favoritecount='{$tweet->favorite_count}' data-contentlink='{$contentlink}'  data-retweetcount='{$tweet->retweet_count}' data-latitude='{$tweet->geo->coordinates[0]}' data-longitude='{$tweet->geo->coordinates[1]}' data-ownedby='{$tweet->user->screen_name}' onClick='addTwitterPhoto(this)' >
						  		Add to ".strtoupper($tempalbum->username)." 
						  		</a>	
								<div style='width:100%; padding:2px; text-align:right;'>								
									<a title='{$contentlink}' href='{$contentlink}' style='background:#ffffff;' ><img height='16' width='16' src='/images/twitter-icon-32.png' ></a>
								</div> 
						  	</div>						 
							</li>";
						}
						
						
						$max_id = $tweet->id;
											
					}					
					
					
					$content .= "<div class='cleardiv'></div>";
					$content .= "<div id='divMore_Twit_{$max_id}' ><a onClick='loadMoreTwitter(this)' class='btn' data-maxid='{$max_id}'>Load More</a></div>";
					
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