<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/checkuser.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Instagram.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/admin/Instagram.config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Album.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Subject.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/UserInfo.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");


$albumID = $_GET["albumID"]; 
$username = $_GET["usr"];
$subjectID = $_GET["sbj"];


$album = NULL;
$photos = NULL;
$subject = NULL;

/*if(isset($subjectID) && !empty($subjectID)){
	$subject = new Subject($subjectID);
}else{
	$subjectID = NULL;
}*/

$subject = isset($subjectID) && !empty($subjectID) && $subjectID != "0"? new Subject($subjectID):NULL;

$ismine = $subjectID == "0"?1:0;
$ismostrated = $subjectID == "-2"?1:0;



if(isset($username) && !empty($username)){
	$album = new Album($userObject->userID);
	$tempID = $album->getAlbumIDFromUsername($username);
	if(isset($tempID)){
		$albumID = $tempID;
	}
}

$latitude = NULL;
$longitude = NULL;

$distance = 1.1;
$geo =  unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=".$_SERVER['REMOTE_ADDR']));
$latitude = $geo["geoplugin_latitude"];
$longitude = $geo["geoplugin_longitude"];


$iseligible = false;
$radious = 3;
if(isset($albumID)  && $userObject){

	$album = new Album($userObject->userID,$albumID);	
	$photos = $album->getLatestPhotoMediums(0,100,$ismine,$subjectID,$ismostrated);
	
	$distance =  distance($latitude,$longitude,$album->latitude,$album->longitude,'M');
	$radious = $album->radious;
	if($distance <= $radious)
		$iseligible = true;	
		
	$distance =  number_format($distance,2)." mi";
	$radious = number_format($radious,2);
}


if((!isset($username) || empty($username)) && isset($albumID)){
	if(isset($subjectID) && $subjectID != "")
		header('Location', 'http://oyvent.com/'.$album->username.'/'.$subjectID);
	else
		header('Location', 'http://oyvent.com/'.$album->username);
}

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
    
    
    
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js"></script>
    <script type="text/javascript">
    //<![CDATA[

   

    //]]>

  </script>  
    
     
    
	<title><?php echo $album->albumName ?></title>
  </head>

<body class="menu-push">


<input type="hidden" id="txtUserID" value="<?php echo $userObject->userID ?>" >
<input type="hidden" id="txtAlbumID" value="<?php echo $albumID ?>" >
<input type="hidden" id="txtAlbumName" value="<?php echo $album->albumName ?>" >
<input type="hidden" id="txtAlbumUserName" value="<?php echo $album->username ?>" >
<input type="hidden" id="txtLatitude" value="<?php echo $latitude ?>" >
<input type="hidden" id="txtLongitude" value="<?php echo $longitude ?>" >
<input type="hidden" id="txtAlbumLatitude" value="<?php echo $album->latitude ?>" >
<input type="hidden" id="txtAlbumLongitude" value="<?php echo $album->longitude ?>" >
<input type="hidden" id="txtSubjectID" value="<?php echo $subjectID ?>" >
<input type="hidden" id="txtisMine" value="<?php echo $ismine ?>" >
<input type="hidden" id="txtisMostRated" value="<?php echo $ismostrated ?>" >

  <script src="/js/Album.js"></script>

<?php if(isset($albumID)){ ?>


<?php  include("menu.php");  ?>
        
        <div class="container">
        
       
			
			<div class="main">
				<section id="contentArea"><!-- This is where all the content goes -->
              
                	<div  class="center">
                    
                     <?php if($album->privacy || $album->albumUserID == $userObject->userID){                 
                   	 			if(!$album->privacy) 				   
				     				echo "<img title='private, only you see' width='16' height='16' src='/images/locked.png' >";
					 												
					  ?>
                     
                	</div>
                
                <div class="grid">
                   
                  <div  class="center" >  
                  
                  <div class="left" ><h2><?php echo "$album->albumName" ?></h2></div>                         
                   
                	 <div  class="right" style='margin:5px; width:100%;clear:both;'> 
                     <a href="/admin/add.php?albumID=<?php echo "$albumID" ?>"  class="btnred" title="Add Content" alt="Add Content"  ><i class="icon_plus"></i> Add Post</a> 
                      <!--<?php  if(isset($userObject) && ($album->albumUserID == $userObject->userID)){ ?>
                     <a  class="btnred" title="Delete Album" alt="Delete Album"  onClick="deleteAlbum()" ><i class="icon_trash_alt"></i> Delete</a> <a class='btnred' title="Edit Album" alt="Edit Album" href='/admin/createalbum.php?albumID=<?php echo $albumID ?>'><i class="icon_pencil-edit"></i> Edit</a>
                     <?php } ?>-->
                     </div>
               		 
                         <!--<div id="map" class="map" ></div>-->
                         
                         <div class="cleardiv"></div>
                                
                	<a  class="btn" title="Import Photos from #Hashtag" style='margin:2px;width:250px;;'  onClick="gotoHashTagPage()" ><i class="icon_search-2"></i> Import Photos from #Hashtag</a>
                    <a style='margin:2px;width:250px;'  class="btn" title="Import Photos from My Instagram"  onClick="gotoInstaTagPage()" ><i class="social_instagram"></i> Import Photos from Instagram</a>                    
                    <a style='margin:2px;width:250px;'  class="btn" title="Import Photos from My Twitter"  onClick="gotoTwitTagPage()" ><i class="social_twitter"></i> Import Photos from Twitter</a>
                    <a style='margin:2px;width:250px;'  class="btn" title="Import Photos from My Facebook"  onClick="gotoFacePage()" ><i class="social_facebook"></i> Import Photos from Facebook</a>
                  </div>	
                
                <ul style='padding:0px;margin:0px;text-align:center;' id="photos">
                
               
                <!-- dropdown list  -->
                <div style='float:left; width:100%; clear:both; margin:4px; margin-top:60px; text-align:center;'>
                	<h3>  <select class="txt" id="selSubject" style='color:#000000;height:35px; width:400px;' onChange="filterSubject()">
                   
                        	<option value="-1"><?php echo "$album->albumName (". $album->photosize .")" ?></option>                            
                            <option <?php  echo $subjectID == '0'?'selected':'' ?> value="0">My Posts </option>
                            <option <?php  echo $subjectID == '-2'?'selected':'' ?> value="-2">Most Rated Posts</option>
                        
							    <optgroup label='Categories' >
								<?php
									//$categories = $subjectObj->getCategories($albumID);	
									$categories = $album->getCategories($albumID);						
									foreach($categories as $category)
									{ ?>
											<option <?php  echo $category['PKALBUMID'] == $subjectID?'selected':'' ?>  value='<?php echo $category["PKALBUMID"] ?>' ><?php echo $category["NAME"]." (".$category["PHOTOS"].")" ?> </option>
							  <?php } ?>
								  </optgroup>
									
							                                	
                                    
                         </select></h3> 
                </div><!-- end of dropdown list -->
                
                 <!-- download & delete buttons -->
                 <div style='float:left; width:100%; clear:both; margin:4px; margin-top:60px; margin-left:70px; text-align:left;'>  
                       
                  <?php if($album->photosize > 0){ ?>                    
                	 <div style='float:left; margin-right:15px;'><h3><input type="checkbox" onChange="selectAll()" id="cbxAll" title="Select All" value="All" >Select All</h3></div>
                 	<div style='float:left; width:600px; margin-left:5px;'> 
                 	<form id="formDownload" name="formDownload" method="post" action="/album/getphotos.php" >
                 	<input type="hidden" name="hdUserID" value="<?php echo $userObject->userID ?>" >
                 	<input type="hidden" name="hdZipName" value="<?php echo $album->username ?>">    
                 	<input type="hidden" name="hdPhotos" id="hdPhotos" value="">                
                 	<a class="download" style="float:left;" onClick='downloadPhotos()' title='Download Selected Photos'  ><h3><i class="icon_download"></i>Download Selected Photos</h3> </a>   
                     <?php if(isset($userObject) &&  $userObject->isAdmin){	 ?>
                     <a class="download" style="float:left; margin-left:20px;" onClick='deleteSelectedPhotos()' title='Delete Selected Photos'  ><h3><i class="icon_trash"></i>Delete Selected Photos</h3> </a>    
                      <?php } ?>         
                	 </form>           
                     </div>
                    
                   <?php } ?>   
                   
                 </div><!-- end of download & delete buttons -->
                 
                 <h2 style="width:100%;text-align:center;"> <?php echo " $album->photosize photos listed"; ?>  </h2> 
                 
                 <?php 
				 
				 $content = "";
				 //var_dump($photos);
				 //$photos = $photos[0];
				 $_photos = array();
				 //array_push($_photos,$photos);
				 $_photos = $photos[0];
				 
				 foreach($photos as $photo){
					 //$firstdate = GetDateDifference($photo['CREATEDDATE']);
					//$firstdate = $photo['POSTDATE'];				
					//$firstdate =  (isset($firstdate) && $firstdate!='')?$firstdate."<br>":"";
					 
				 	$strSocialMedia = "";
					
					
					if(isset($photo['FKINSTAGRAMID'])) 
						$strSocialMedia .= "<a title='".$photo['CONTENTLINK']."' href='".$photo['CONTENTLINK']."' style='background:#ffffff; padding-right:2px;' ><img height='16' width='16' src='/images/instagram-icon-32.png' ></a>";	
					else if(isset($photo['FKTWITTERID'])) 
						$strSocialMedia .= "<a title='".$photo['CONTENTLINK']."' href='".$photo['CONTENTLINK']."' style='background:#ffffff;padding-right:2px;' ><img height='16' width='16' src='/images/twitter-icon-32.png' ></a>";
					else if(isset($photo['FKFACEBOOKID'])) 
						$strSocialMedia .= "<a title='".$photo['CONTENTLINK']."' href='".$photo['CONTENTLINK']."' style='background:#ffffff;padding-right:2px;' ><img height='16' width='16' src='/images/facebook-icon-32.png' ></a>";						
					
					if(isset($photo["FKALBUMID"])){
						$strSocialMedia .= "  <a class='blue' style='font-size:14px;' title='".$photo['ALBUMNAME']."' href='".$sitepath."/".$username."/".$photo["FKALBUMID"]."' ><i class='icon_tag_alt'></i> ".substr($photo['ALBUMNAME'],0,23)."</a>";
					}
					
					$strDelDown = "<div class='icon-left' style='font-size:16px;'>";					
					$strDelDown .= " <input type='checkbox'  name='cbxphoto' id='cbxphoto_".$photo["PKPHOTOID"]."' value='".$photo["PKPHOTOID"]."' > ";
					$strDelDown .= "<a  class='blue' style='margin-left:15px;' alt='go to comment page' title='go to comment page'  href='/admin/feed.php?photoID=".$photo["PKPHOTOID"]."' ><i class='icon_comment'></i> (".$photo["TOTALCOMMENTS"].")</a>" ; 
					$strDelDown .= "</div>";
					
						
					
					//oys
					$strDelDown .= "<div class='icon-right'>";
					
					$strDelDown .= "<div style='float:left;font-size:16px;padding-right:2px;margin-right:2px;' id='oys_".$photo['PKPHOTOID']."' >";						
					$hasvoted = $album->hasVoted($photo['PKPHOTOID'],$userObject->userID);						
					if(!$hasvoted){						
						$strDelDown .= "<a class='blue' style='float:left;font-size:20px;' data-photoid='".$photo['PKPHOTOID']."' title='+1' alt='+1'  onClick='voteup(this)' > <i class='arrow_triangle-up'></i></a>";
					}					
					$oys =  $photo["OY"] <= 0 || $photo["OY"] == null ?"0 oys":"+".$photo["OY"]." oys";
					$strDelDown .= "<div  style='float:left;font-size:16px;padding-right:2px;margin-right:2px;' >".$oys."</div>";
					
					if(!$hasvoted){
						$strDelDown .= "<a class='blue' style='font-size:20px;' data-photoid='".$photo['PKPHOTOID']."' title='-1' alt='-1'  onClick='votedown(this)' ><i class='arrow_triangle-down'></i></a>";	
					}
					$strDelDown .= "</div>";																	
					if(isset($userObject) && ($photo['FKUSERID'] == $userObject->userID) || $userObject->isAdmin){						
						$strDelDown .= "<a class='blue'  style='padding:0px;' data-photoid='".$photo['PKPHOTOID']."' onClick='deletePhoto(this)' alt='delete photo' title='delete photo'>x</a>
						  ";
				 	}			
					$strDelDown .= "</div>";
					
					//social media						
					$strDelDown .= "<div class='icon-left' >".$strSocialMedia."</div>";	
					//miles	
					//$strDelDown .= "<div class='icon-right' style='font-size:14px;margin-top:8px;padding-top:3px;'>".number_format(distance($photo['LAT1'],$photo['LONG1'],$photo['LAT2'],$photo['LONG2'],'M'),2)." mi</div>";
					
					$ownedby = isset($photo["OWNEDBY"])&&$photo["OWNEDBY"]!=""?"<p>owned by @".$photo["OWNEDBY"]."</p>":"";
					$isverified = $photo["ISVERIFIED"]?"<img src='/images/ok.png' width='16' height='16' >":"";					
					$caption =  $photo['CAPTION'];
					
					
					$content .= "<li>
					   <a  rel='fancybox-thumb' title=\"{$caption}\" href='{$photo['URLLARGE']}' class='fancybox-thumb' >
						   
						   <img class='media'  src='{$photo['URLMEDIUM']}' />
						   <div class='content' style='z-index:2'>".
                           	//<div class='avatar' style='background-image: url({$photo['URLTHUMB']}) '></div>
							
							"<p>added by ".$photo["FULLNAME"]." ".$isverified."</p>".							
							$ownedby.
                           	"<div class='comment'>posted on<br>".getDateTime($photo["POSTDATE"],$timezone,false)." (".getDateTime($photo["POSTDATE"],$timezone,true).")
																							
							</div>
                           </div>
						   </a>
						    
						   <div class='iconcnt'>".$strDelDown."</div>
						   
					  </li>";
					
				 }
				 
				  if($content != ""){
				  	$content .= "<div class='loadmore' id='load_100'><input class='loadmorebtn'  type='button' onClick='showMoreAlbumPhotos(100)' value='Load More Photos'></div>";
				  }else{
				  	$content = "<h2>no photos</h2>";
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
      <div class="modal" id="dgModalDeleteSelected"><div id="loadingselected"></div><div id="loadingstatus"></div></div>
      <div class="modal" id="dgDownload"><div style='width:100%; float:left; clear:both; text-align:center;' id="lbldownload"></div></div>

<?php } else { ?>
<h3>Invalid Album </h3>
<?php } ?>

 <?php include("footer.php"); ?>




</body>
</html>