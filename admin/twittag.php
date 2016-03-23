<?php 

require_once($_SERVER['DOCUMENT_ROOT']."/checkuser.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/TwitterAPIExchange.php");
require_once($_SERVER['DOCUMENT_ROOT']."/admin/Twitter.config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Album.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");

$tag = $_GET['txtHashTag'];

if(!isset($tag) || empty($tag))
	$tag = 'kitty';
	
	
 $tempAlbumID = $_GET["albumID"];
 if($tempAlbumID == NULL || $tempAlbumID == ""){
 	@session_start();
 	if($_SESSION["region"] == "AR")
		$tempAlbumID = 34;
 	if($_SESSION["region"] == "NY")
		$tempAlbumID = 35;
 }
 
 if(isset($tempAlbumID) && !empty($tempAlbumID)){ 	   
	   $tempalbum = new Album($userObject->userID,$tempAlbumID);
	   if(empty($tag))
	   		$tag = $tempalbum->username;
 }
 
 $tag = str_replace("#","",$tag);
 $tag = trim($tag);

?>

<!DOCTYPE html>
<html lang="en" class="no-js"> 
<head>
	<title>Search Twitter Photos</title> 	
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
    <script src="/js/TwitTag.js"></script>
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

<?php include("menu.php"); ?>
        
        <div class="container">
			
			<div class="main">
				<section id="contentArea"><!-- This is where all the content goes -->
                
                	<div class="center">                             
                		<img src="/images/twitter.png"  title="Twitter logo">
        				<h1>Search Twitter Photos </h1>        			
        				<form action="twittag.php" id="formHashTag" method="get" name="formHashTag" >
                        	<input type="hidden" name="albumID" id="albumID" value="<?php echo $tempAlbumID ?>" />
        					<input type="text" name="txtHashTag" class="txt"  id="txtHashTag" value="<?php echo $tag; ?>" />
        					<input type="submit" id="btnHashTag" class="btn"  value="#Search Tag" />
        				</form>
						<br />
       				 <h2>#<?php echo $tag; ?></h2>        
       				 </div>

					<div class="grid">

						<h1><select class="select" id="selAlbum"  name="selAlbum">
        					<option value="0">Select an Album</option>    
    						<?php 		  		   
		   						if(isset($tempalbum)){			   
		  					?>
          					 <option selected value="<?php echo $tempAlbumID ?>"><?php echo $tempalbum->albumName ?></option>            
							<?php } ?>
        
        
        					<?php 
		
								$myalbums = new Album($userObject->userID);
								$albums = $myalbums->getAlbumList("NAME ASC");
								$stralbums = "";
			
								foreach($albums as $album)
								{ 
									if($tempAlbumID != $album["PKALBUMID"]){
								?>
				 					<option value="<?php echo $album["PKALBUMID"] ?>"><?php echo $album["NAME"] ?></option>
						<?php 		} 
								}?>
		
		        
        					</select></h1>

        			<ul id="photos">
                    
                    <?php 
					
					$url = 'https://api.twitter.com/1.1/search/tweets.json';
					$requestMethod = 'GET';
					$getfield = '?q=' .$tag.'&result_type=recent&count=100&include_entities=true&include_retweets=false';					

					// Perform the request
					$twitter = new TwitterAPIExchange($settings);
					$response = $twitter->setGetfield($getfield)
             					 ->buildOauth($url, $requestMethod)
             					 ->performRequest();
								 
					$content = "";
					$tweets = json_decode($response);
					print_r($tweets->statuses);
					$max_id = "";
          			foreach ($tweets->statuses as $tweet) {		
					
					$createdtime = date('Y-m-d H:i:s', strtotime($tweet->created_at));
					$caption = htmlentities(str_replace("'", "\'", $tweet->text));//not used for now
					$firstdate = GetDateDifference($createdtime);
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
						  		<a class='btn'  data-twitterid='{$tweet->id}' data-largeurl='{$tweet->entities->media[0]->media_url}:large' data-mediumurl='{$tweet->entities->media[0]->media_url}' data-smallurl='{$tweet->entities->media[0]->media_url}:small' data-thumburl='{$tweet->entities->media[0]->media_url}:thumb' data-caption='{$caption}' data-createdtime='{$createdtime}' data-favoritecount='{$tweet->favorite_count}'  data-contentlink='{$contentlink}' data-retweetcount='{$tweet->retweet_count}' data-latitude='{$tweet->geo->coordinates[0]}' data-longitude='{$tweet->geo->coordinates[1]}' data-ownedby='{$tweet->user->screen_name}' onClick='addTwitterPhoto(this)' >
						  		Add to Album
						  		</a>	
								<div style='width:100%; padding:2px; text-align:right;'>								
									<a title='{$contentlink}' href='{$contentlink}' style='background:#ffffff;' ><img height='16' width='16' src='/images/twitter-icon-32.png' ></a>
								</div> 
						  	</div>					 
						</li>";
						//}
						
						$max_id = $tweet->id;
											
					}
					
					$content .= "<div class='cleardiv'></div>";
					$content .= "<div id='divMore_Twit_{$max_id}' > <a onClick='loadMoreTwitter(this)' class='btn' data-maxid='{$max_id}' data-tag='{$tag}'>Load More</a></div>";
					
					echo $content;
					
					?>
        
       	 			</ul>
        			</div>     
               
                
                </section>
			</div>
		</div>

  
        
         
  <?php include("footer.php") ?>
  
  <div class="modal" id="dgModal"><div id="loading"></div></div>
  
  
        
</body>
</html>