<?php 

require_once($_SERVER['DOCUMENT_ROOT']."/checkuser.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Instagram.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/admin/Instagram.config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Album.class.php");

/*$userObject = NULL;
if (isset($_SESSION['userObject']) && !empty($_SESSION['userObject']))
{
	$userObject = $_SESSION['userObject'];
	$data=$_SESSION['userdetails'];
	echo '<img src='.$data->user->profile_picture.' >';
	echo 'Name:'.$data->user->full_name;
	echo 'Username:'.$data->user->username;
	echo 'User ID:'.$data->user->id;
	echo 'Bio:'.$data->user->bio;
	echo 'Website:'.$data->user->website;
	echo 'Profile Pic:'.$data->user->profile_picture;
	echo 'Access Token: '.$data->access_token;	
	// store user access token
  	$instagram->setAccessToken($data);
	$username = $username = $data->user->username;
   	// now you have access to all authenticated user methods
  	$result = $instagram->getUserMedia();
} // check whether an error occurred
else{
	header("Location: login.php");
}*/

@session_start();
/*if($_SESSION["region"] == NULL || $_SESSION["region"] == ""){
	$geo =  unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=".$_SERVER['REMOTE_ADDR']));	
	$_SESSION["region"] = $geo["geoplugin_region"];
	//$_SESSION["region"] = "NY";
	$latitude = $geo["geoplugin_latitude"];
	$longitude = $geo["geoplugin_longitude"];
	
}*/

/*if($_SESSION["region"] == "AR")
	header("Location: /ualr/");
else if($_SESSION["region"] == "NY")
	header("Location: /ualbany/");
else
	header("Location: /ualr/");*/
	
	
$geo =  unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=".$_SERVER['REMOTE_ADDR']));	
$latitude = $geo["geoplugin_latitude"];
$longitude = $geo["geoplugin_longitude"];	
	

?>
<!DOCTYPE html>
 <html lang="en" class="no-js"> 
  <head>    
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
    <script src="/js/modernizr.custom.js"></script> 
    <script src="/js/General.js"></script>
    <script src="/js/Admin.js"></script>
    
    
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
    
	<title>Oyvent Admin</title>
  </head>

<body class="menu-push">

<input type="hidden" id="txtUserID" value="<?php echo $userObject->userID ?>" >

		<?php //include("menu.php"); ?>
        
        <div class="container">
			
			<div class="main">

				<?php include("../header.php"); ?>

				<section id="contentArea"><!-- This is where all the content goes -->
                
                <div  class="center">
                	<h1>Welcome <?php echo $userObject->fullname ?> </h1>
                    
                    
                  <a href='createalbum.php'><img src='/images/addalbum.png'>
                  <h2>Create an Album</h2></a>
                    
                </div>
                
                <div class="cleardiv"></div>
               
                <div class="grid">
                
                 <?php 		 
		
			$myalbums = new Album($userObject->userID);
			//$albums = $myalbums->getAlbumList(NULL,12);
			$albums = $myalbums->getAlbumListAsDistance("DISTANCE ASC",NULL,$latitude,$longitude);
			$stralbums = "";//initial add album div		
			$numofthumbs = 0;	
			
			if(isset($albums))
				$stralbums =  "<h2>Latest Albums</h2>";
			else 
				$stralbums = "<h2>No album found, please create an album</h2>";
			
			foreach($albums as $album)
			{
				$myalbum = new Album($userObject->userID,$album["PKALBUMID"]);
				$numofthumbs = $myalbum->photosize;				
				$thumbs = $myalbum->getLatestPhotoThumbs(66);
				$strthumbs = "";
				foreach($thumbs as $thumb){
					$strthumbs .= "<img class='thumbsmall' src='".$thumb['URLTHUMB']."' >";
				}
				
				$stralbums .= "<a href='album.php?albumID=".$album["PKALBUMID"]."'>
				<div class='album' >
				<span>".$album["NAME"]."<br>".$strthumbs."<br></span>

				<h3 style='color:#ff0000;'>".$numofthumbs." photos</h3>
				</div></a>";

				//*<h3 style='color:#ff0000;'>".$numofthumbs." photos  - ".number_format($album["DISTANCE"],2)." mi</h3>*/
			}
		
			
		echo $stralbums;
		?>
                </div>
                
                <div class="cleardiv"></div>
                               
                <div class="grid">
                
             
                </div>
                </section>
			</div>
		</div>
        
      <div class="modal" id="dgModalDelete"><div id="loading"></div></div>
  
</body>
</html>