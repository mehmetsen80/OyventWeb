<?php 

require_once($_SERVER['DOCUMENT_ROOT']."/checkuser.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Instagram.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/admin/Instagram.config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Album.class.php");

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
    
	<title>My Gallery</title>
  </head>

<body class="menu-push">

<input type="hidden" id="txtUserID" value="<?php echo $userObject->userID ?>" >

		<?php include("menu.php"); ?>
        
        <div class="container">
			
			<div class="main">
				<section id="contentArea"><!-- This is where all the content goes -->
                
                <div  class="center">                     
                  
                  <h2>Welcome <?php echo $userObject->fullname ?> </h2>                                 
                          
                </div>
                
                <div class="cleardiv"></div>
                <h2>My Albums</h2>
                <div class="grid">
                
                 <?php 		 
		 
		
		$stralbums = "";
		
		
			$myalbums = new Album($userObject->userID);
			$albums = $myalbums->getAlbumList(NULL,NULL);
			$stralbums = "";//initial add album div			
			
			foreach($albums as $album)
			{
				$myalbum = new Album($userObject->userID,$album["PKALBUMID"]);
				$numofthumbs = $myalbum->photosize;
				$thumbs = $myalbum->getLatestPhotoThumbs(10);
				$strthumbs = "";
				foreach($thumbs as $thumb){
					$thumpath = $sitepath.'/'.$thumb['PHYSICALPATH']."/".$thumb['NAMETHUMB'];
					$strthumbs .= "<img class='thumbsmall' src='".$thumpath."' >";
				}
				
				$stralbums .= "<a href='/album/?albumID=".$album["PKALBUMID"]."'>
				<div class='album' >
				<span>".$album["NAME"]."<br>".$strthumbs."<br></span>
				<h3>".$numofthumbs."</h3>				
				
				</div></a>";
			}
		
		
		echo $stralbums;
		?>
                </div>
                
                
                </section>
			</div>
		</div>
     
  
</body>
</html>