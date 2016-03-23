<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/checkuser.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Instagram.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/admin/Instagram.config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Album.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");

$instadetails = $userObject->instadetails;


	/*$myalbums = new Album($userObject->userID);
	$albums = $myalbums->getAlbumList("NAME ASC");
	$stralbums = "";*/
	
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

if(!isset($instadetails))
	header("Location: instalogin.php");

	

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
	<title>My Instagram Photos</title>
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
    <script src="/js/InstaTimeline.js"></script>
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
                    
                    	<img src="/images/instagram.png" alt="Instagram logo">
        				<h1>My Instagram Timeline Photos <span style="font-weight:bold;"></span></h1>
 						<h3>[<?php echo $instadetails->user->full_name ?>] [@<?php  echo $instadetails->user->username ?>]</h3>            
                    </div>
                	
                    
                    <div class="grid">    
                    
                    <!--<h1>Selected Geo-Album<br><u><a class="red" href="/<?php echo $tempalbum->username; ?>"><?php echo $tempalbum->albumName ?> </a></u></h1><a href='#' class="btn" onClick='window.location.href="choose.php";return false;' ><i class='arrow_carrot-2right'></i> Change Geo-Album</a>
        <div class="cleardiv"></div>
        <br />-->
                    
        			<ul id="photos" style='padding:0px;margin:0px;'>
        <?php		 
				
		  		$instagram->setAccessToken($instadetails);
		  		$media = $instagram->getUserFeed(100);
         		 // display all user likes
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
						    <a class='btn'  data-instagramid='{$data->id}' data-smallurl='{$data->images->thumbnail->url}' data-mediumurl='{$data->images->low_resolution->url}' data-largeurl='{$data->images->standard_resolution->url}' data-thumburl='{$data->images->thumbnail->url}'  data-ownedby='{$data->user->username}' data-createdtime='{$createdtime}' data-likes='{$data->likes->count}' data-contentlink='{$data->link}' data-tags='{$tags}' data-latitude='{$data->location->latitude}' data-longitude='{$data->location->longitude}' data-contenttype='{$data->type}'     data-caption='{$caption}'  onClick='addInstagramPhoto(this)' >Add to ".strtoupper($tempalbum->username)." </a>
						   
						    <div style='width:100%; padding:2px; text-align:right;'>								
									<a title='{$data->link}' href='{$data->link}' style='background:#ffffff;' ><img height='16' width='16' src='/images/instagram-icon-32.png' ></a>
							</div>
							
						   </div>
						   </li>";			          
          		}
		  
		  		$content .= "<div class='cleardiv'></div>";
		  		$content .= "<div id='divMore_{$media->pagination->next_max_id}' > <a  onClick='loadMoreInstagram(this)' class='btn' data-maxid='{$media->pagination->next_max_id}' data-tag='{$tag}'>Load More</a></div>";
		  
		   		// output media
           		echo $content;
		 
        ?>
        			</ul>
        			</div>
                    
                
                 </section>
			</div>
		</div>        
        

  <?php 
  include("footerthumbs.php");
  include("footer.php"); ?>
  
  <div class="modal" id="dgModal"><div id="loading"></div></div>


</body>
</html>