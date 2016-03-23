<?php 

$processType= ($_GET['processType'])?$_GET['processType']:$_POST['processType'];

 switch($processType)
 {
 	case "GETTWIITERSEARCHLIST":
		getTwitterSearchList();
		break;
 }
 
 function getTwitterSearchList(){
 	
	require_once($_SERVER['DOCUMENT_ROOT']."/lib/TwitterAPIExchange.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/admin/Twitter.config.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/class/Album.class.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/class/UserInfo.class.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");
	
	$tag = $_GET['tag'];
  	$maxID = $_GET['max_id'];
	
	@session_start(); 
  	$userObject = $_SESSION["userObject"];
	$timezone = stripslashes($_SESSION['usertimezone']);
    $timezone = str_replace('"',"",$timezone);
  
  	$album = NULL;
  	$albumID = $_GET["albumID"];
  	if(isset($albumID) && !empty($albumID)){ 	   
	   $album = new Album($userObject->userID,$albumID);
  	}
  
	
	$url = 'https://api.twitter.com/1.1/search/tweets.json';
	$requestMethod = 'GET';
	$getfield = '?q='.$tag.'%20-RT%20filter:images&result_type=recent&count=100&include_entities=true&include_retweets=false&since_id='.$maxID;					

	// Perform the request
	$twitter = new TwitterAPIExchange($settings);
	$response = $twitter->setGetfield($getfield)
    				 ->buildOauth($url, $requestMethod)
    				 ->performRequest();
								 
	$content = "";
	$tweets = json_decode($response);
	$max_id = "";
    foreach ($tweets->statuses as $tweet) {
        //if(!$tweet->retweeted){		
		
		$createdtime = date('Y-m-d H:i:s', strtotime($tweet->created_at));
		$caption = htmlentities(str_replace("'", "\'", $tweet->text));//not used for now
		$firstdate = GetDateDifference($createdtime,$timezone);
		$firstdate =  (isset($firstdate) && $firstdate!='')?$firstdate."<br>":"";
		$contentlink = "http://twitter.com/".$tweet->user->screen_name."/status/".$tweet->id."/";
							
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
						  		<a class='btn'  data-twitterid='{$tweet->id}' data-largeurl='{$tweet->entities->media[0]->media_url}:large' data-mediumurl='{$tweet->entities->media[0]->media_url}' data-smallurl='{$tweet->entities->media[0]->media_url}:small' data-thumburl='{$tweet->entities->media[0]->media_url}:thumb' data-caption='{$caption}' data-createdtime='{$createdtime}' data-favoritecount='{$tweet->favorite_count}' data-contentlink='{$contentlink}' data-retweetcount='{$tweet->retweet_count}' data-latitude='{$tweet->geo->coordinates[0]}' data-longitude='{$tweet->geo->coordinates[1]}' data-ownedby='{$tweet->user->screen_name}' onClick='addTwitterPhoto(this)' >
						  		Add to ".strtoupper($album->username)." 
						  		</a>	
								<div style='width:100%; padding:2px; text-align:right;'>								
									<a title='{$contentlink}' href='{$contentlink}' style='background:#ffffff;' ><img height='16' width='16' src='/images/twitter-icon-32.png' ></a>
								</div> 
						  	</div>
							
											 
						</li>";
		//}
		$max_id = $tweet->id;
		
	}					
	//$content .= "$max_id";
	$content .= "<div class='cleardiv'></div>";
	$content .= "<div id='divMore_Twit_{$max_id}' > <a onClick='loadMoreTwitter(this)' class='btn' data-maxid='{$max_id}' data-tag='{$tag}'>Load More</a></div>";
					
	echo json_encode(array(
  		'premaxid' => $maxID,	
		'content' => $content,
		'success' => true
	));
 }

?>