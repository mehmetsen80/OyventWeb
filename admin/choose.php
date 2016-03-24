<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/checkuser.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Album.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Subject.class.php");
include($_SERVER['DOCUMENT_ROOT']."/Settings.php");

$albumID = $_GET["albumID"];


$geo =  unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=".$_SERVER['REMOTE_ADDR']));
$latitude = $geo["geoplugin_latitude"];
$longitude = $geo["geoplugin_longitude"];


/*$url = 'http'.(empty($_SERVER['HTTPS'])?'':'s').'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].$_SERVER['QUERY_STRING'];
echo $url;*/

if(isset($albumID) && $albumID != ""){	
	
	$_SESSION['redirect_url'] = str_replace("albumID=","", $_SESSION['redirect_url']);
	if (strpos($_SESSION['redirect_url'],'?') !== false || $_GET['add']=='1') {
    	$redirect_url = (isset($_SESSION['redirect_url'])) ? $sitepath."/".$_SESSION['redirect_url'].'&albumID='.$albumID : '/';
	}else{
		$redirect_url = (isset($_SESSION['redirect_url'])) ? $sitepath."/".$_SESSION['redirect_url'].'?albumID='.$albumID : '/';
	}
 
	
	unset($_SESSION['redirect_url']);
	header("Location: $redirect_url", true, 303);
}

?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
	<title>Choose Group</title> 	
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
    
    <script src="/js/Choose.js"></script>
     
<title>Choose Album</title>
</head>

<body class="menu-push">

 <input type="hidden" id="txtUserID" value="<?php echo $userObject->userID ?>" >
 
 <?php //include("menu.php"); ?>
       
        <div class="maincontainer">
			
			<div class="main">

				<?php include("../header.php"); ?>

				<section id="contentArea"><!-- This is where all the content goes -->
                
                	<div class="center">
                    	<h2>Please choose your album to navigate</h2>
                	</div>
                    
                    <div class="grid">
                    	                        
                        <?php if(isset($userObject)) { ?>
							
                            	<select class="select" id="selAlbum"  name="selAlbum">
                            		<option value="0">Select an album</option>
                                    <?php	
		
									$myalbums = new Album($userObject->userID);
									$albums = $myalbums->getAlbumListAsDistance("DISTANCE ASC",NULL,$latitude,$longitude);
									$stralbums = "";
			
									foreach($albums as $album)
									{
									 ?>
				 					<option value="<?php echo $album["PKALBUMID"] ?>"><?php echo $album["NAME"]." (".number_format($album["DISTANCE"],2)." mi)" ?></option>
									<?php } ?>			
                            	</select>
                          
                            <input type="button" class="btn" value="Continue" onClick="chooseAlbum()" >
                            
                         <?php } ?>     
                    </div>
                    
                     <div class="cleardiv"></div>
                    
                </section>
            </div>
        </div>
  <?php  include("footer.php") ?>
</body>
</html>