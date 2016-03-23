<?php 
// Report all errors
error_reporting(E_ALL & ~E_NOTICE);
require_once($_SERVER['DOCUMENT_ROOT']."/checkuser.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Album.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Subject.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/UserInfo.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Settings.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Comment.class.php");

$photoID = $_GET["photoID"];
$photoID = real_escape_string($photoID);

$album=NULL;
$photo = NULL;

$geo =  unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=".$_SERVER['REMOTE_ADDR']));
$latitude = $geo["geoplugin_latitude"];
$random = ((rand()*(0.0002/getrandmax()))-0.0001);
$longitude = $geo["geoplugin_longitude"] + $random;


$iseligible = false;
$distance = 100.0;
$radious = 1.0;
if(isset($photoID) && isset($userObject)){
	$album = new Album($userObject->userID);
	$photo = $album->getUniquePhoto($photoID);	
	$album = new Album($userObject->userID,$photo["FKALBUMID"]);
		
	if(isset($album->albumID)){		
		$distance =  distance($latitude,$longitude,$album->latitude,$album->longitude,'M');
		$radious = $album->radious;
		if($distance <= $radious)
			$iseligible = true;	
		$distance =  number_format($distance,2)." mi";
		$radious = number_format($radious,2);
	}
}


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en" class="no-js"> 
<head>
	<title>Comments</title>
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
      me: {
        icon: 'http://labs.google.com/ridefinder/images/mm_20_blue.png'
      },
      bar: {
        icon: 'http://labs.google.com/ridefinder/images/mm_20_red.png'
      }
    };

    function load() {
      var map = new google.maps.Map(document.getElementById("map"), {
        center: new google.maps.LatLng(<?php echo $photo["LAT2"] ?>, <?php echo $photo["LONG2"] ?>),
        zoom: 12,
        mapTypeId: 'roadmap'
      });
      var infoWindow = new google.maps.InfoWindow;

      // Change this depending on the name of your PHP file
      downloadUrl("getmarkerscomments.php?photoID=<?php echo $photoID ?>&photoLAT=<?php echo $photo["LAT2"] ?>&photoLONG=<?php echo $photo["LONG2"] ?>", function(data) {
        var xml = data.responseXML;
        var markers = xml.documentElement.getElementsByTagName("marker");
		//alert('len:'+markers.length);		
        for (var i = 0; i < markers.length; i++) {
          var name = markers[i].getAttribute("name");         
		  var comment = markers[i].getAttribute("comment");
		  var sql = markers[i].getAttribute("sql");
		  //if(i<2)  alert(sql);
          var postdate = markers[i].getAttribute("postdate");
		  var type = markers[i].getAttribute("type");
		  var urlthumb = markers[i].getAttribute("urlthumb");
          var point = new google.maps.LatLng(
              parseFloat(markers[i].getAttribute("lat")),
              parseFloat(markers[i].getAttribute("lng")));
          var html = "<div style='height:220px;width:220px;color:#000000;'><b>posted by " + name + "</b><br>"+comment+"</div>";
		  
		  var photoid = markers[i].getAttribute("photoid");
		  var isfeed = markers[i].getAttribute("isfeed");
		  var icon = customIcons[type] || {};
		  if(isfeed == 'YES')	{	 		
		  	icon = customIcons['me'] || {};
		  	 html = "<div style='height:160px;width:220px;color:#000000;'><b>posted by " + name + "</b><br><img height='120' width='120' src='"+urlthumb+"' ></div>";
		  }
		  
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
    
    
    <script src="/js/Feed.js"></script>    
	
  </head>

<body onLoad="load()" class="menu-push">

<input type="hidden" id="txtUserID" value="<?php echo $userObject->userID ?>" >
<input type="hidden" id="txtOwnerEmail" value="<?php echo $photo["EMAIL"] ?>" >
<input type="hidden" id="txtOwnerName" value="<?php echo $photo["FULLNAME"] ?>" >
<input type="hidden" id="txtAlbumUsername" value="<?php echo $photo["USERNAME"] ?>" >
<input type="hidden" id="txtAlbumID" value="<?php echo $photo["FKALBUMID"] ?>" >
<input type="hidden" id="txtLatitude" value="<?php echo $latitude ?>" >
<input type="hidden" id="txtLongitude" value="<?php echo $longitude ?>" >



<?php include("menu.php"); ?>
        
<div class="container">			
	<div class="main">
		<section id="contentArea"><!-- This is where all the content goes -->
                  
   <?php  if(!isset($photo)){
	 	echo "<h2>invalid photo feed!</h2>";        
	}else{ ?>
                  
         <div  class="center" style="padding:0px;"> 
                  <span style="color:#292929; float:left; font-size:22px; clear:left; text-align:left;">Comments                      
                  
                   <span style='font-size:16px; margin-left:20px; '>Your distance to this album is <?php echo $distance ?>&nbsp;&nbsp;&nbsp; <?php if($iseligible){ ?><=<?php }else{ ?>><?php  }?>&nbsp;&nbsp;&nbsp;Radious=<?php echo $radious ?> mi &nbsp;
                            <?php if($iseligible){ ?>
                     		<img height="32" src="/images/ok.png" alt="You are eligible to post!" title="You are eligible to post!" />                            
                     	   <?php }else{ ?>
                     		<img height="32" src="/images/no.png" alt="You are not eligible to post!" title="You are not eligible to post!" />                           
                        <?php  }?>
                        
                            </span>
                            
                      </span>
                  
                  
                         <span style="color:#0000ff; float:right; font-size:14px; clear:right; text-align:right;"><?php echo "<a class='red'  title='Back to ".$photo['ALBUMNAME']."' alt='Back to ".$photo['ALBUMNAME']."' href='$sitepath/".$photo['USERNAME']."' ><i class='arrow_carrot-2left' ></i> ".$photo['ALBUMNAME']."</a>" ?></span><br/><br/>
                         
                     
                 
         </div>                  	
                  
        <div class="row">               
        <br><br>
        
        	<div class="col-md-4">                    	
             <br>
                
                 <textarea id="txtMessage" class="txtarea" style='width:100%;'  cols="15" rows="7"  placeholder="Enter Comment" ></textarea><br> <br>
                  <?php if($iseligible){ ?>                     
                      <input type="submit" class="btn" style='width:100%;height:34px;font-size:16px;' onClick="addComment('<?php echo $photoID; ?>')" id="btnAdd" value="Add Comment" ><br>
                  <?php } else{?>
                      <a href="#"  class="btn" style='width:100%;height:34px;font-size:16px;' onClick="alertRadius()">Not within radius!</a><br>
                  <?php } ?>	
               
            <div id='divCommentPanel' class='panel panel-default' style='margin-top:20px; width:100%;'>
            <?php 
				$commentObj = new Comment();
				$comments = $commentObj->getComments($photoID);			 	
            	$container = "<div class='panel-heading'> 
						<h3 class='panel-title' style='text-align:left;' >Comments ($commentObj->commentsize)</h3></div>        
               			<table class='table table-bordered table-striped'><tbody>"; 
				 	foreach($comments as $comment){                 
                  	 	$container .= "<tr> <td style='text-align:left;'>";
						$container .= str_replace("\n", "<br>\n", $comment["COMMENT"]);	
                        $container .= "<h5 style='float:left;text-align:left; clear:both; margin:1px; margin-top:5px; padding:1px; color:#999; width:100%;'>";							  								   
						$container .= "posted by ".$comment["FULLNAME"]." ".getDateTime($comment["POSTDATE"],$timezone,false)." (".getDateTime($comment["POSTDATE"],$timezone,true).")";
					
						if($comment["FKUSERID"] == $userObject->userID || $userObject->isAdmin){
							$container .= "  <a class='blue' onClick='deleteComment(".$comment["PKCOMMENTID"].")' title='Delete this Comment' alt='Delete this Comment' >x</a>";
						}
					
						$container .= "</h5>";
						$container .= "</td></tr>";
					}
					$container .= "</table>"; 
						
					echo $container;
			  ?>        
              
              </div>
                    
                    
         	</div>
            
           <div class="col-md-4">
           <br>
           	<div class="grid2" style="text-align:center; width:100%;clear:both;" >                         	
                   <ul id="photos" style='padding:0px;margin:0px;'>
                   
                   <?php 
							 	
					$strSocialMedia = "";
					if(isset($photo['FKINSTAGRAMID'])) 
						$strSocialMedia = "<a title='".$photo['CONTENTLINK']."' href='".$photo['CONTENTLINK']."' style='background:#ffffff; padding-right:2px;' ><img height='20' width='20' src='/images/instagram-icon-32.png' ></a>";	
					else if(isset($photo['FKTWITTERID'])) 
						$strSocialMedia = "<a title='".$photo['CONTENTLINK']."' href='".$photo['CONTENTLINK']."' style='background:#ffffff;padding-right:2px;' ><img height='20' width='20' src='/images/twitter-icon-32.png' ></a>";
					else if(isset($photo['FKFACEBOOKID'])) 
						$strSocialMedia = "<a title='".$photo['CONTENTLINK']."' href='".$photo['CONTENTLINK']."' style='background:#ffffff;padding-right:2px;' ><img height='20' width='20' src='/images/facebook-icon-32.png' ></a>";						
					
					if(isset($photo["FKSUBJECTID"])){
						$strSocialMedia .= " <a class='blue' href='".$sitepath."/".$photo["USERNAME"]."/".$photo["FKSUBJECTID"]."' ><i class='icon_tag_alt'></i>".$photo['TITLE']."</a>";
					}
					
					$strDelDown = "<div class='icon-left'>";					
					$strDelDown .= "<form id='formDownload' style='float:left;'  name='formDownload' method='post' action='/album/getphotos.php' >
                 	<input type='hidden' name='hdUserID' value='".$userObject->userID."' >
                 	<input type='hidden' name='hdZipName' value='".$photo["USERNAME"]."'>    
                 	<input type='hidden' name='hdPhotos' id='hdPhotos' value=''>                
                 	<a class='download' onClick='downloadPhotos()' alt='Download this Photo' title='Download this Photo'  ><i class='icon_download'></i></a>         
                	 </form>";
					 $strDelDown .= " <input type='checkbox' style='float:left;visibility:hidden;' checked  name='cbxphoto' id='cbxphoto_".$photo["PKPHOTOID"]."' value='".$photo["PKPHOTOID"]."' >";
                     $strDelDown .= "</div>";
                  
					
					//oys
					$strDelDown .= "<div class='icon-right'>";
					$strDelDown .= "<div style='float:left;font-size:20px;padding-right:2px;margin-right:2px;' id='oys_".$photo['PKPHOTOID']."' >";						
					$hasvoted = $album->hasVoted($photo['PKPHOTOID'],$userObject->userID);						
					if(!$hasvoted){						
						$strDelDown .= "<a class='blue' style='float:left;font-size:20px;' data-photoid='".$photo['PKPHOTOID']."' title='+1' alt='+1'  onClick='voteup(this)' > <i class='arrow_triangle-up'></i></a>";
					}					
					$oys =  $photo["OY"] <= 0 ?$photo["OY"]." oys":"+".$photo["OY"]." oys";						
					$strDelDown .= "<div  style='float:left;font-size:20px;padding-right:2px;margin-right:2px;' >".$oys."</div>";
					if(!$hasvoted){
						$strDelDown .= "<a class='blue' style='font-size:20px;' data-photoid='".$photo['PKPHOTOID']."' title='-1' alt='-1'  onClick='votedown(this)' ><i class='arrow_triangle-down'></i></a>";	
					}
					$strDelDown .= "</div>";																	
					if(isset($userObject) && ($photo['FKUSERID'] == $userObject->userID)){						
						$strDelDown .= "<a class='blue'  style='padding:0px;' data-photoid='".$photo['PKPHOTOID']."' onClick='deletePhoto(this)' alt='delete photo' title='delete photo'>x</a>
						  ";
				 	}			
					$strDelDown .= "</div>";
					
					
					//social media						
					$strDelDown .= "<div class='icon-left' >".$strSocialMedia."</div>";	
					//miles	
					$strDelDown .= "<div class='icon-right' style='font-size:16px;margin-top:8px;padding-top:3px;'>".number_format(distance($photo['LAT1'],$photo['LONG1'],$photo['LAT2'],$photo['LONG2'],'M'),2)." mi</div>"; 
							
					
				 	$content .= "<li>
						   <a title='".$photo["ALBUMNAME"]."' rel='fancybox-thumb' href='{$photo['URLLARGE']}' class='fancybox-thumb' >
						   <img class='media' src='{$photo['URLMEDIUM']}' />
						   <div class='content'>".
                           	//<div class='avatar' style='background-image: url({$photo['URLTHUMB']}) '></div>
                           	"<p> added by <br>".$photo["FULLNAME"]."</p>  							
                           	<div class='comment'>posted on<br>".getDateTime($photo["POSTDATE"],$timezone,false)." (".getDateTime($photo["POSTDATE"],$timezone,true).")"."<br>".
							(floor(($photo["SIZELARGE"]/1024)*10)/10)." KB																	
							</div>
                           </div>
						   </a>
						    
						   <div class='iconcnt'>".$strDelDown."</div>
						   
						   </li>";
							
					
							
					echo $content	; 
							 
							 ?>
                             </ul>  
                             <?php 
							 
							 $caption = str_replace("\n", "<br>\n", $photo['CAPTION']);
							 
							if($caption=="")
							{
								echo "<div style='color:#000000;width:100%; text-align:center;clear:both;'> no caption entered</div>";
							}else{
								echo "<div style='color:#000000;width:100%;margin-bottom:40px; text-align:left; clear:both;'><b>Caption</b>:<br>  ".$caption." </div>"; 
                             }
						//echo $photo['CAPTION']==""?"no caption entered":$photo['CAPTION']; ?>    
                             
                                                                               
                         </div>
           </div>               
           
          
           
           <div class="col-md-4">
            <!--<div id="map" class="map2"></div>  -->
            <br>
            
            <div id="detailsTabs">
				<ul>
					<li><a href="#tabs-1">Details</a></li>
					<li><a href="#tabs-2">Other</a></li>
					<li><a href="#tabs-3">Report</a></li>
				</ul>
				<div id="tabs-1" >
				<table class="table table-bordered table-striped" style="text-align:left;">
				<tbody>
                	
                    <tr>
                    	<td width="140">Geo-Album</td>
                        <td><a style='color:#0000ff;' class='blue'  title='Go back to <?php echo $photo['ALBUMNAME'] ?> ' href='<?php echo $sitepath."/".$photo['USERNAME'] ?>' ><?php echo $photo['ALBUMNAME']?> </a></td>
                    </tr>
                    <tr>
                    	<td>Posted by</td>
                        <td><?php echo $photo["FULLNAME"] ?></td>
                    </tr>
                    <tr>
                    	<td>Posted Date</td>
                        <td><?php echo getDateTime($photo["POSTDATE"],$timezone,false)." (".getDateTime($photo["POSTDATE"],$timezone,true).")"; ?></td>
                    </tr>
                    <tr>
						<td >Total Oys</td>
						<td><?php echo $photo["TOTALVOTES"]; ?></td>
					</tr>
					<tr>
						<td>Oys Up&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="arrow_up"></i></td>
						<td><?php echo "+".$photo["VOTESUP"];	?></td>
					</tr>
					<tr>
						<td>Oys Down <i class="arrow_down"></i></td>
						<td><?php echo "-".$photo["VOTESDOWN"];	?></td>
					</tr>
                    <tr>
                    	<td>Oys<i class="arrow_right"></i></td>
                        <td><?php echo $oys; ?></td>
                    </tr>
                    		
                    			
				</tbody>
			</table> 
			</div>
			<div id="tabs-2">
			<table class="table table-bordered table-striped" style="text-align:left;">
				<tbody>                
                	<tr>
                    	<td  width="140">Comments</td>
                        <td><?php echo $commentObj->commentsize; ?></td>
                    </tr>
                    <tr>
                    	<td>Category</td>
                        <td>
                         <?php 
						 if(isset($photo["FKSUBJECTID"])){
							echo " <a class='blue' href='".$sitepath."/".$photo["USERNAME"]."/".$photo["FKSUBJECTID"]."' ><i class='icon_tag_alt'></i>".$photo['TITLE']."</a>";
						 }else{
							 echo "no subject";
						 }
					?>
                        </td>
                    </tr>
                    <tr>
                    	<td>Social</td>
                        <td>
						<?php  
						  if(isset($photo['FKINSTAGRAMID']) || isset($photo["FKTWITTERID"]) 
						  	|| isset($photo["FKFACEBOOKID"]))
						    	echo $strSocialMedia;
						   else 
						   		echo "no social media";	
							?></td>
                    </tr>
                    
                    <tr>
                    	<td>Latitude</td>
                        <td><?php echo $photo["LAT2"] ?></td>
                    </tr>
                    <tr>
                    	<td>Longitude</td>
                        <td><?php echo $photo["LONG2"] ?></td>
                    </tr>
                    <tr>
                    	<td>Distance to geo-album</td>
                        <td><?php echo $distance; ?></td>
                    </tr>
                    <tr>
                    	<td>Photo Size</td>
                        <td><?php echo (floor(($photo["SIZELARGE"]/1024)*10)/10)." KB "; ?></td>
                    </tr>  		
                
                </tbody>
            </table>
			</div>
			<div id="tabs-3">
			<p><strong>Please report this post if you see as harmful.</strong></p>
			<p><textarea cols="22" rows="12" class="txtarea" style='width:100%;' placeholder="Enter your concerns"  id="txtReport" ></textarea>
            	<input id="btnReport" type="button" class="btn" onClick="report('<?php echo $photoID; ?>')"  style='width:100%;' value="Report this Post" >
            </p>
			</div>
		</div><!-- Tabs -->
                       
          </div><!-- col-md-4 -->           
                   
                    
          </div><!-- row -->
                   
          
                 <?php } ?> 	
                </section>
			</div>
		</div>

<div class="modal" id="dgModalDelete"><div id="loadingDelete"></div></div>  
<div class="modal" id="dgModal"><div id="loading"></div></div>

		 <?php  include("footer.php") ?>


</body>
</html>