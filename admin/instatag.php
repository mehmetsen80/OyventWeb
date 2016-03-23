<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/checkuser.php");

require_once($_SERVER['DOCUMENT_ROOT']."/class/Instagram.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/admin/Instagram.config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Album.class.php");


$tag = $_GET['txtHashTag'];

if(!isset($tag) || empty($tag))
	$tag = 'kitty';

 $tempAlbumID = $_GET["albumID"];
 $tempalbum = NULL;
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
	<title>Search Instagram Photos</title> 	
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
    <script src="/js/InstaTag.js"></script>
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
                             
                	<img src="/images/instagram.png" alt="Instagram logo">
        			<h1>Search Instagram Photos </h1>
        			
        			<form action="instatag.php" id="formHashTag" method="get" name="formHashTag" >
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
		  $media = $instagram->getTagMedia($tag);
          // display all user likes
		  $content = "";
          foreach ($media->data as $data) {
            	$content .= "<li>
							<a  title='' rel='fancybox-thumb' href='{$data->images->standard_resolution->url}' class='fancybox-thumb' >
							<img class='media' src='{$data->images->low_resolution->url}' /> 
						   <div class='content'>
                           	<div class='avatar' style='background-image: url({$data->user->profile_picture}) '></div>
                           	<p>{$data->user->username}</p>
                           	<div class='comment'>{$data->caption->text}</div>
                           </div>
						  </a>
						   
						   <div class='add2album'>
						   <a class='btn'  data-instagramid='{$data->id}' data-smallurl='{$data->images->thumbnail->url}' data-mediumurl='{$data->images->low_resolution->url}' data-largeurl='{$data->images->standard_resolution->url}' data-thumburl=''  data-ownedby='{$data->user->username}' data-caption='{$data->caption->text}'  onClick='addInstagramPhoto(this)' >Add to ".strtoupper($tempalbum->username)." Geo-Album</a>					   
						   </div>
						   </li>";			          
          }
		  
		  $content .= "<div class='cleardiv'></div>";
		  $content .= "<div id='divMore_Inst_{$media->pagination->next_max_id}' > <a  onClick='loadMoreInstagram(this)' class='btn' data-maxid='{$media->pagination->next_max_id}' data-tag='{$tag}'>Load More</a></div>";
		  
		  // output media
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