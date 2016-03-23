<?php 

require_once($_SERVER['DOCUMENT_ROOT']."/class/Instagram.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/admin/Instagram.config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Album.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/UserInfo.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");

@session_start();

$userObject = NULL;

if (isset($_SESSION['userObject']))
		$userObject = $_SESSION['userObject'];

$albumID = $_GET["albumID"]; 
$username = $_GET["usr"];

$album = NULL;
$photos = NULL;



if(isset($username)){
	$album = new Album($userObject->userID);
	$tempID = $album->getAlbumIDFromUsername($username);
	if(isset($tempID)){
		$albumID = $tempID;
	}
}

$latitude = NULL;
$longitude = NULL;

if(isset($albumID)){
	
	$album = new Album($userObject->userID,$albumID);	
	$photos = $album->getLatestPhotoMediums();	
}


if((!isset($username) || empty($username)) && isset($albumID))
	header('Location', 'http://oyvent.com/'.$album->username);

?>
<!DOCTYPE html>
 <html lang="en" class="no-js"> 
  <head>    
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
    <script src="/js/modernizr.custom.js"></script> 
    <script src="/js/General.js"></script>
    <script src="/js/Album.js"></script>
    
    
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
    
	<title><?php echo $album->albumName ?></title>
  </head>

<body class="menu-push">

<input type="hidden" id="txtUserID" value="<?php echo $userObject->userID ?>" >
<input type="hidden" id="txtAlbumID" value="<?php echo $albumID ?>" >
<input type="hidden" id="txtAlbumName" value="<?php echo $album->username ?>" >

<?php if(isset($albumID)){ ?>


<?php include("menu.php"); ?>
        
        <div class="container">
			
			<div class="main">
            
            	<?php include("../header.php"); ?>
            
				<section id="contentArea"><!-- This is where all the content goes -->
               
                	<div  class="center">
                    
                    
                     <?php if($album->privacy || $album->albumUserID == $userObject->userID){               
                 
                   	 if(!$album->privacy) 				   
				     	echo "<img title='private, only you see' width='48' height='48' src='/images/locked.png' >";
					 else 
                     	echo "<img title='public, every user sees' width='48' height='48' src='/images/unlock.png' >";                       
												
						
					  ?>
                     
                     
                     <?php  if(isset($userObject) && ($album->albumUserID == $userObject->userID)){ ?>
                	 <div  class="center" style="padding:0px;  padding-bottom:10px;"> 
                     <a  class="red" title="Delete Album"  onClick="deleteAlbum()" >delete this album</a><br>
                     <a class='red' href='/admin/createalbum.php?albumID=<?php echo $albumID ?>'>edit this album</a>
                     </div>
               		 <?php } ?>
                     
                    
                    <h1><?php echo  "<a class='download' href='".$sitepath."/".$album->username."'>@".$album->username."</a>" ?> </h1>	
                    
                        
                	</div>
                
                <div class="grid">
   
   				
   				             
                
                
                  <div  class="center" style="padding:10px;">                
                	<a  class="btn" title="Import Photos from #Hashtag"  onClick="gotoHashTagPage()" ><i class="icon_search"></i>Import Photos from #Hashtag</a>&nbsp;&nbsp;<a  class="btn" title="Import Photos from My Instagram"  onClick="gotoInstaTagPage()" ><i class="social_instagram"></i>Import Photos from My Instagram</a>&nbsp;&nbsp;<a  class="btn" title="Import Photos from My Twitter"  onClick="gotoTwitTagPage()" ><i class="social_twitter"></i>Import Photos from My Twitter</a>&nbsp;&nbsp;<a  class="btn" title="Import Photos from My Facebook"  onClick="gotoFacePage()" ><i class="social_facebook"></i>Import Photos from My Facebook</a>
                  </div>	
                
                <ul id="photos">
                
               
                
                <div style='float:left; width:80%; margin:4px; margin-left:90px; clear:both; text-align:left;'>
                 
                  <?php if($album->photosize > 0 /*&& $album->albumUserID == $userObject->userID*/){ ?>
                 <div style='float:left; margin-right:15px;'><h3><input type="checkbox" onChange="selectAll()" id="cbxAll" title="Select All" value="All" >All</h3></div>
                 <div style='float:left; margin-left:5px;'> 
                 <form id="formDownload" name="formDownload" method="post" action="/album/getphotos.php" >
                 <input type="hidden" name="hdUserID" value="<?php echo $userObject->userID ?>" >
                 <input type="hidden" name="hdZipName" value="<?php echo $album->username ?>">    
                 <input type="hidden" name="hdPhotos" id="hdPhotos" value="">                
                 <a class="download" onClick='downloadPhotos()' title='Download Selected Photos'  ><h3><i class="icon_download"></i>Download Selected Photos</h3> </a> 
                 <a class="download" onClick='deleteSelectedPhotos()' title='Delete Selected Photos'  ><h3><i class="icon_download"></i>Delete Selected Photos</h3> </a>    
                      
                 </form>               
                                
                 </div>
                   <?php } ?>
                 
                 <div style='float:right;'><h3>Total <?php echo $album->photosize; ?> Photos</h3></div>
                 </div>
                 <?php 
				 
				 $content = "";
				 foreach($photos as $photo){
					/*$thumb = $sitepath.'/'.$photo['PHYSICALPATH']."/".$photo['NAMETHUMB'];
					$medium = $sitepath.'/'.$photo['PHYSICALPATH']."/".$photo['NAMEMEDIUM'];
					$large = $sitepath.'/'.$photo['PHYSICALPATH']."/".$photo['NAMELARGE'];*/
					
					$thumb = $photo['URLTHUMB'];
					$medium = $photo['URLMEDIUM'];
					$large = $photo['URLLARGE'];
					
					//$firstdate = GetDateDifference($photo['CREATEDDATE']);
					$firstdate = $photo['CREATEDDATE'];
					$firstdate =  (isset($firstdate) && $firstdate!='')?$firstdate."<br>":"";
					
					$strSocialMedia = "";
					if(isset($photo['FKINSTAGRAMID'])) 
						$strSocialMedia = "<a title='".$photo['CONTENTLINK']."' href='".$photo['CONTENTLINK']."' style='background:#ffffff; padding-right:2px;' ><img height='16' width='16' src='/images/instagram-icon-32.png' ></a>";	
					else if(isset($photo['FKTWITTERID'])) 
						$strSocialMedia = "<a title='".$photo['CONTENTLINK']."' href='".$photo['CONTENTLINK']."' style='background:#ffffff;padding-right:2px;' ><img height='16' width='16' src='/images/twitter-icon-32.png' ></a>";						
					
					$strDelDown = "";					
						$strDelDown .= " <input type='checkbox' class='icon-left' name='cbxphoto' id='cbxphoto_".$photo["PKPHOTOID"]."' value='".$photo["PKPHOTOID"]."' > ";	
						$strDelDown .= "<div class='icon-right' style='font-size:14px;margin-top:8px;'>".number_format(distance($photo['LAT1'],$photo['LONG1'],$photo['LAT2'],$photo['LONG2'],'M'),2)." mi </div>"; 
											
						$strDelDown .= "<div class='icon-left' >".$strSocialMedia."</div>";												
						if(isset($userObject) && ($album->albumUserID == $userObject->userID)){						
							$strDelDown .= "<a class='icon-right' style='padding:0px;' data-photoid='".$photo['PKPHOTOID']."' onClick='deletePhoto(this)' alt='delete photo' title='delete photo'>x</a>
						  ";
				 		}						
					
				 	$content .= "<li>
						   <a title='".$photo["ALBUMNAME"]."' rel='fancybox-thumb' href='{$photo['URLLARGE']}' class='fancybox-thumb' >
						   <img class='media' src='{$photo['URLMEDIUM']}' />
						   <div class='content'>
                           	<div class='avatar' style='background-image: url({$photo['URLTHUMB']}) '></div>
                           	<p> posted by @".$photo["OWNEDBY"]."</p>  							
                           	<div class='comment'>".$firstdate.(floor(($photo["SIZELARGE"]/1024)*10)/10)." KB																	
							</div>
                           </div>
						   </a>
						    
						   <div class='iconcnt'>".$strDelDown."</div>
						   
						   </li>";
				 }
				 
				  if($content != ""){
				  	$content .= "<div style='width:100%;clear:both;float:left'><input class='btn' type='button' onClick='loadMorePhotos()' value='Load More Photos'></div>";
				  }else{
				  	$content = "no photos";
				  }
				 
				  echo $content;
				 ?>
                
                </ul>
                <?php }else{ 
					echo "<img src='/images/locked.png' title='Set to private by the owner' >
							<h2>Set to private by the owner</h2>";
				}?>
                </div>
                </section>
			</div>
		</div>
        
      <div class="modal" id="dgModalDelete"><div id="loading"></div></div>
      <div class="modal" id="dgModalDeleteSelected"><div id="loading"></div></div>
      <div class="modal" id="dgDownload"><div style='width:100%; float:left; clear:both; text-align:center;' id="lbldownload"></div></div>

<?php } else { ?>
<h3>Invalid Album </h3>
<?php } ?>
</body>
</html>