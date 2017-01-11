<?php 

require_once($_SERVER['DOCUMENT_ROOT']."/class/Instagram.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/admin/Instagram.config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/TwitterAPIExchange.php");
require_once($_SERVER['DOCUMENT_ROOT']."/admin/Twitter.config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/UserInfo.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Album.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");


$tag = $_GET['txtHashTag'];
//$tag = real_escape_string($tag);
$tag = strip_tags($tag);
$social = $_GET["social"];

if($social == NULL) 
	$social = 0;//instagram=0, twitter=1
	 
if(!isset($tag) || empty($tag)) 
	$tag = 'concert';

 $tempAlbumID = $_GET["albumID"];
 $tempalbum = NULL;
 if(isset($tempAlbumID) && !empty($tempAlbumID)){ 	   
	   $tempalbum = new Album($userObject->userID,$tempAlbumID);
	   //if(empty($tag))
	   	$tag = $tempalbum->username;
 }

$tag = str_replace("#","",$tag);
$tag = trim($tag);

@session_start();

$userObject = NULL;
if (isset($_SESSION['userObject']) & !empty($_SESSION['userObject']))
	$userObject = $_SESSION['userObject'];
	

//if (isset($_SESSION['userObject']) && !empty($_SESSION['userObject']))
	//header("Location: /login/");

	//header("Location: /mobile/");
header("Location: http://peerit.net");

?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
	<title>Discover Real Time Event Photos</title> 	
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">    
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">     
    
    <link rel="stylesheet" href="/css/icons.css" />
	<!--[if lte IE 7]><script src="/js/lte-ie7.js"></script><![endif]-->
       
    
    <link href="/css/search.css" rel="stylesheet">
    
    <link href="/css/custom-theme/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">
    <!-- javascript -->
    <script src="/js/lib/jquery-1.10.2.js"></script>
    <script src="/js/lib/jquery-ui-1.10.4.custom.min.js"></script> 
    <script src="/js/lib/jquery.ddslick.js"></script> 
    <script src="/js/General.js"></script>
    <script src="/js/Search.js"></script>

    
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

       
        <div class="maincontainer">
			
			<div class="main">
            
           		<?php include("header.php"); ?>
            
             
				<section id="contentArea"><!-- This is where all the content goes -->
                
                <div class="center">
                    
        			<h1>Discover Real Time Event Photos </h1>
        			
        			<form  id="formHashTag"  method="get" name="formHashTag" >
                    	
        				
                        <div style="margin-left:auto; margin-right:auto; width:1200px; clear:both;  text-align:center">
                        <div style="width:600px; margin-left:auto; margin-right:auto; clear:both;  text-align:center">
                           <input type="hidden" name="albumID" id="albumID" value="<?php echo $tempAlbumID ?>" />
                           <input type="hidden" name="social" id="social" value="<?php echo $social ?>" />
                           
                           <div id="ddlSocialNetwork" ></div>
                            <!--<select name="ddlSocialNetwork" id="ddlSocialNetwork">
        							<option value="0" data-imagesrc="/images/instagram-icon-32.png"></option>
        							<option value="1" data-imagesrc="/images/twitter-icon-32.png"></option>
            				</select>-->
                        	<input type="text" name="txtHashTag" class="txtSearch"  id="txtHashTag" value="<?php echo $tag; ?>" />
                           
        					<input type="submit"  class="btnSearch"   value="#Search Tag" />
                        </div>
                        </div>
        			</form> 
        		
        

		<br />
        
        <?php if(isset($tempalbum)){ ?>
        	<h2><a style='color:#ff0000;' href="/<?php echo $tempalbum->username; ?>">#<?php echo $tag; ?></a></h2>
        <?php } else{ ?>
        <h2>#<?php echo $tag; ?></h2>
        <?php } ?>
        </div>



<div class="grid">

		<a href='/album/createalbum.php' class='btn' ><i class='icon_plus'></i> Create Album</a>

		<?php if(isset($userObject)) { ?>		
			

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
        
                
        <?php } ?>        
        
        <ul id="photos">
                
  <?php
  
  	$content = "";		 
  	switch($social){
  
  		case 0: //instagram
		  $media = $instagram->getTagMedia($tag);
          // display all user likes
		  
          foreach ($media->data as $data) {
			  	$caption = htmlentities(str_replace("'", "\'", $data->caption->text));//not used for now
				$createdtime = date("Y-m-d H:i:s",$data->created_time);
				$tags =  "[".implode(",", $data->tags)."]";
				$firstdate = GetDateDifference($createdtime);
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
						   <a class='btn'  data-instagramid='{$data->id}' data-smallurl='{$data->images->thumbnail->url}' data-mediumurl='{$data->images->low_resolution->url}' data-largeurl='{$data->images->standard_resolution->url}' data-thumburl='{$data->images->thumbnail->url}'  data-ownedby='{$data->user->username}' data-createdtime='{$createdtime}' data-likes='{$data->likes->count}' data-contentlink='{$data->link}' data-tags='{$tags}' data-latitude='{$data->location->latitude}' data-longitude='{$data->location->longitude}' data-contenttype='{$data->type}'     data-caption='{$caption}'  onClick='addInstagramPhoto(this)' >Add to Album</a>					   						   
						    <div style='width:100%; padding:2px;  text-align:right;'>								
								<a title='{$data->link}' href='{$data->link}' style='background:#ffffff;' ><img height='16' width='16' src='/images/instagram-icon-32.png' ></a>
							</div> 
						   
						   </div>
						   
						  
						   
						   </li>";			          
        }
		  
		  $content .= "<div class='cleardiv'></div>";
		  $content .= "<div id='divMore_Inst_{$media->pagination->next_max_id}' > <a  onClick='loadMoreInstagram(this)' class='btn' data-maxid='{$media->pagination->next_max_id}' data-tag='{$tag}'>Load More</a></div>";
		  
		  	break;
		  
		 case 1: //twitter
		  		
				$url = 'https://api.twitter.com/1.1/search/tweets.json';
					$requestMethod = 'GET';
					$getfield = '?q='.$tag.'%20-RT%20filter:images&result_type=recent&count=100&include_entities=true&include_retweets=false';					

					// Perform the request
					$twitter = new TwitterAPIExchange($settings);
					$response = $twitter->setGetfield($getfield)
             					 ->buildOauth($url, $requestMethod)
             					 ->performRequest();
								 
					$content = "";
					$tweets = json_decode($response);
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
						  		<a class='btn'  data-twitterid='{$tweet->id}' data-largeurl='{$tweet->entities->media[0]->media_url}:large' data-mediumurl='{$tweet->entities->media[0]->media_url}' data-smallurl='{$tweet->entities->media[0]->media_url}:small' data-thumburl='{$tweet->entities->media[0]->media_url}:thumb' data-caption='{$caption}' data-createdtime='{$createdtime}' data-favoritecount='{$tweet->favorite_count}' data-retweetcount='{$tweet->retweet_count}' data-latitude='{$tweet->geo->coordinates[0]}' data-longitude='{$tweet->geo->coordinates[1]}'  data-contentlink='{$contentlink}' data-ownedby='{$tweet->user->screen_name}' onClick='addTwitterPhoto(this)' >
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
					
					//$content .= "$max_id";
					$content .= "<div class='cleardiv'></div>";
					$content .= "<div id='divMore_Twit_{$max_id}' > <a onClick='loadMoreTwitter(this)' class='btn' data-maxid='{$max_id}' data-tag='{$tag}'>Load More</a></div>";
				
				
		 	break;
		  
		 }
		  
		  // output media
           echo $content;		  
        ?>
        </ul>
        </div>
                
                
                
                
                
                </section>
			</div>
		</div>
 
  
  <script>
	$(document).ready(function(){
 		$('#ddlSocialNetwork').ddslick('select', {index: <?php echo $social ?> });
	});

</script>  
        
         
  <?php 
  
  if(isset($userObject))
	  include("admin/footer.php") 
  
  ?>
  
  <div class="modal" id="dgModal"><div id="loading"></div></div>

</body>
</html>