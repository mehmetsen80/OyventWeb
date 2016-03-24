<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/checkuser.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Album.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Subject.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/class/UUID.class.php');	

@session_start();

$albumID = $_GET["albumID"]; 

//nau album by default, remove this later!
$albumID = 1;

//if($_SESSION["region"] == "AR")
//	$albumID = 34;
//if($_SESSION["region"] == "NY")
//	$albumID = 35;

$_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
if(!isset($albumID) || empty($albumID)){	
	header('Location: choose.php?add=1');
}


$album=NULL;
$photos=NULL;
$distance = 1.1;
$geo =  unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=".$_SERVER['REMOTE_ADDR']));
$latitude = $geo["geoplugin_latitude"];
$random = ((rand()*(0.0002/getrandmax()))-0.0001);
$longitude = $geo["geoplugin_longitude"] + $random;

$iseligible = false;

$radious = 3;

if(isset($albumID) && $userObject){	
	$album = new Album($userObject->userID,$albumID);	
	//$distance = distance($coordinates[0],$coordinates[1],$album->latitude,$album->longitude,'M');
	$distance =  distance($latitude,$longitude,$album->latitude,$album->longitude,'M');
	$radious = $album->radious;
	//$distance = 1.8;
	if($distance <= $radious)
		$iseligible = true;	
	$distance =  number_format($distance,2)." mi";
	$radious = number_format($radious,2);
}


$picUUID = UUID::generate(UUID::UUID_RANDOM, UUID::FMT_STRING,"02787914");
$picUUID = str_replace("-","",$picUUID);
$picUUID = strlen($picUUID)>=16?substr($picUUID,0,16):$picUUID;


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en" class="no-js"> 
<head>
	<title>Add Post</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">    
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">       
    
    <link rel="stylesheet" href="/css/icons.css" />
	<!--[if lte IE 7]><script src="/js/lte-ie7.js"></script><![endif]-->	
    <link href="/css/style.css" rel="stylesheet">    
    <link href="/css/custom-theme/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/uploadfile.css" /> 

    
    <!-- javascript -->
    <script src="/js/lib/jquery-1.10.2.js"></script>
    <script src="/js/lib/jquery-ui-1.10.4.custom.min.js"></script>
    <script src="/js/lib/jquery.uploadfile.js"></script>

    
    <script src="/js/General.js"></script>   
    <script src="/js/modernizr.custom.js"></script>    
    <script src="/js/bootstrap.js"></script>
    
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

    var customIcons = {
      restaurant: {
        icon: 'http://labs.google.com/ridefinder/images/mm_20_blue.png'
      },
      bar: {
        icon: 'http://labs.google.com/ridefinder/images/mm_20_red.png'
      }
    };

    function load() {
      var map = new google.maps.Map(document.getElementById("map"), {
        center: new google.maps.LatLng(<?php echo $album->latitude ?>, <?php echo $album->longitude ?>),
        zoom: 13,
        mapTypeId: 'roadmap'
      });
      var infoWindow = new google.maps.InfoWindow;

      // Change this depending on the name of your PHP file
      downloadUrl("getmarkers.php?albumID=<?php echo $album->albumID ?>", function(data) {
        var xml = data.responseXML;
        var markers = xml.documentElement.getElementsByTagName("marker");		
        for (var i = 0; i < markers.length; i++) {
          var name = markers[i].getAttribute("name");
		  var photoid =  markers[i].getAttribute("photoid");
          var address = markers[i].getAttribute("address");
          var postdate = markers[i].getAttribute("postdate");
		  var type = markers[i].getAttribute("type");
		  var urlthumb = markers[i].getAttribute("urlthumb");		
		  var urlsmall = markers[i].getAttribute("urlsmall");  
          var point = new google.maps.LatLng(
              parseFloat(markers[i].getAttribute("lat")),
              parseFloat(markers[i].getAttribute("lng")));
          var html = "<div style='height:160px;width:140px;color:#000000;'>posted by <b>" + name + "</b><br><a href='/admin/feed.php?photoID="+photoid+"' title='go to this post' alt='go to this post' ><img height='120' width='120' src='"+urlsmall+"' ></a></div>";
          var icon = customIcons[type] || {};
          var marker = new google.maps.Marker({
            map: map,
            position: point,
            icon: icon.icon
          });
          bindInfoWindow(marker, map, infoWindow, html);
        }
      });
    }

    function bindInfoWindow(marker, map, infoWindow, html) {
      google.maps.event.addListener(marker, 'click', function() {
        infoWindow.setContent(html);
        infoWindow.open(map, marker);
      });
    }

    function downloadUrl(url, callback) {
      var request = window.ActiveXObject ?
          new ActiveXObject('Microsoft.XMLHTTP') :
          new XMLHttpRequest;

      request.onreadystatechange = function() {
        if (request.readyState == 4) {
          request.onreadystatechange = doNothing;
          callback(request, request.status);
        }
      };

      request.open('GET', url, true);
      request.send(null);
    }

    function doNothing() {}

    //]]>

  </script>
    
    
    <script src="/js/Add.js"></script>
    
	<title>Add Content</title>
  </head>

<body onLoad="load()" class="menu-push">

<input type="hidden" id="txtUserID" value="<?php echo $userObject->userID ?>" >
<input type="hidden" id="txtAlbumUsername" value="<?php echo $album->username ?>" >
<input type="hidden" id="txtAlbumID" value="<?php echo $albumID ?>" >
<input type="hidden" id="txtLatitude" value="<?php echo $latitude ?>" >
<input type="hidden" id="txtLongitude" value="<?php echo $longitude ?>" >
<input type="hidden" id="txtPicUUID" value="<?php echo $picUUID ?>" >


		<?php //include("menu.php"); ?>
        
        <div class="maincontainer">
			
			<div class="main">

                <?php include("../header.php"); ?>

				<section id="contentArea"><!-- This is where all the content goes -->
                  
                  	<!-- <div  class="center" style="padding:1px;">
                     <span style='font-size:14px;color:#0000ff;'><h3 style='color:#047fb7;'>Your distance to the geo-album is <?php echo $distance ?>&nbsp;&nbsp;&nbsp; <?php if($iseligible){ ?><=<?php }else{ ?>><?php  }?>&nbsp;&nbsp;&nbsp;radious=<?php echo $radious ?> mi &nbsp;
                            <?php if($iseligible){ ?>
                     		<img height="32" src="/images/ok.png" alt="You are eligible to post!" title="You are eligible to post!" />                            
                     	   <?php }else{ ?>
                     		<img height="32" src="/images/no.png" alt="You are not eligible to post!" title="You are not eligible to post!" />                           
                        <?php  }?>
                        </h3>
                            </span>   
                    </div>
                    -->

                    <div class="row">

                        <div class="col-md-3">

                        </div>
                        <div class="col-md-6">


                            <h1><?php echo $album->albumName ?></h1>



                            <?php
                            /*$thumbs = $album->getLatestPhotoThumbs(66);
                            $strthumbs = "";
                            $stralbums = "";
                            foreach($thumbs as $thumb){
                                $strthumbs .= "<img class='thumbsmall' src='".$thumb['URLTHUMB']."' >";
                            }

                            $stralbums = "<a title='$album->albumName' alt='$album->albumName' href='".$sitepath."/$album->username'>
							<div class='album' >
                                <span>$strthumbs</span>
								<h3 style='color:#ff0000;'>".$album->photosize." photos</h3>

							</div></a>";

                            echo $stralbums;*/
                            ?>
                        </div>

                        <div class="col-md-3">

                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h1></h1>
                        </div>
                    </div>

                  	<div class="row">
                        <div class="col-md-3">

                        </div>
                    	<div class="col-md-6" style="text-align: left;" >
                          <h1 >Add Post</h1>                                                              
                          <select class="txt" id="selSubject" style='width:100%; height:36px;color:#000000;'>
                           <option value='<?php echo $album->albumID; ?>' ><?php echo $album->albumName; ?></option>
                           <optgroup label='Categories' >
                           	<?php
									$categories = $album->getCategories($albumID);						
									foreach($categories as $category)
									{ ?>
											<option <?php  echo $category['PKALBUMID'] == $subjectID?'selected':'' ?>  value='<?php echo $category["PKALBUMID"] ?>' ><?php echo $category["NAME"]." (".$category["PHOTOS"].")" ?> </option>
							  <?php } ?>
                           </optgroup>
                          
                          <?php																	
							/*$subjectObj = new Subject();
							$subjDefault = $subjectObj->getDefaultSubject($albumID);
							if(isset($subjDefault))
								echo "<option value='".$subjDefault["PKSUBJECTID"]."' >".$subjDefault["TITLE"]."</option>";									
									
							$categories = $subjectObj->getCategories($albumID);		
							foreach($categories as $category){
								echo "<optgroup label='".$category["TITLE"]."'>";
								$subjects = $subjectObj->getSubCategories($albumID,$category["PKSUBJECTID"]);
								foreach($subjects as $subject)
									echo "<option value='".$subject["PKSUBJECTID"]."' >".$subject["TITLE"]."</option>";									
								echo "</optgroup>";
							}	*/
							
							
							
													
								?>                             	
                                    
                                </select><br><br>
                                
                                <textarea id="txtMessage" class="txtarea" style='width:100%;'  cols="20" rows="7"  placeholder="Enter Text" ></textarea><br><br>   
                                
                               <div style='text-align:left; margin:4px; float:left; clear:both;' id='divPhoto'></div> 
                               <div id="file" style="width:100%;">Select Image</div>                                
                                <br>
                                
                                 <?php if($iseligible){ ?>                     
                                <input type="submit" class="btn" style='width:100%;height:40px;font-size:18px;' onClick="addFeed()" id="btnAdd" value="Add Post" ><br>
                                 <?php } else{?>
                                 <a href="#"  class="btn" style='width:100%;height:34px;font-size:16px;' onClick="alertRadius()">Not within radius!</a><br>
                                 <?php } ?>	
                         
                        
                         
                   		</div>

                        <div class="col-md-3">

                        </div>
                    
                   
                    
                   </div>
                   
                   <div class="row">
                   	<div class="col-md-6">
                       <h1></h1>
                    </div>
                    <div class="col-md-6">
                       <h1></h1>                
                    </div>
                   
                   </div>
                  	
                </section>
			</div>
		</div>


		 <?php  include("footer.php") ?>
  
<div class="modal" id="dgModal"><div id="loading"></div></div>

</body>
</html>