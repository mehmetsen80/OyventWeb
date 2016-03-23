<?php 

require_once($_SERVER['DOCUMENT_ROOT']."/checkuser.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Album.class.php");

$albumID = $_GET["albumID"]; 
$album = NULL;

if(isset($albumID)){	
	$album = new Album($userObject->userID,$albumID);	
	
}

?>
<!DOCTYPE html>
<html lang="en" class="no-js"> 
<head>	
	<title>Create Album</title>
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
    <script src="/js/General.js"></script>
    <script src="/js/CreateAlbum.js"></script>
    <script src="/js/modernizr.custom.js"></script> 
</head>

<body class="menu-push">

<input type="hidden" id="txtUserID" value="<?php echo $userObject->userID ?>" >
<input type="hidden" id="txtAlbumID" value="<?php echo $albumID ?>" >


<?php include("menu.php"); ?>
        
        <div class="container">
			
			<div class="main">
            
            	<?php include("../header.php"); ?>
            
				<section id="contentArea"><!-- This is where all the content goes -->
                
                <div class="grid">
               
                	<ul ><li style="width:500px; height:470px;">
                	<div class="inputcontainer" style="width:480px;height:450px;">

  					<div class="input"><img src="../images/addalbum.png" alt="Add Album" title="Add Album"></div>  
                    <?php 
						if(isset($albumID)) 
							echo "<h3>Update Album</h3>";
						  else
						    echo "<h3>Create Album</h3>";						
					?>
  					
  					<div class="input"> <input name="txtAlbumName" class="txt" value="<?php echo isset($albumID)?$album->albumName: 'Album Name' ?>"   type="text" id="txtAlbumName" style="width:400px;"  maxlength="150" /></div>
                   
                    <div class="input" style='font-size:18px;' >http://oyvent.com/<input name="txtUsername" class="txt" style='width:232px;' value=<?php echo isset($albumID)? $album->username: "address" ?>  type="text" id="txtUsername"  maxlength="20" /></div>  
  					
                    <div class="input"> <select class="txt" style="width:400px;" name="cmbPrivacy" id="cmbPrivacy">
         				
                        <option value="1"  <?php echo $album->privacy == '1'?'selected':''; ?>  >Yes, everyone can see it - Public</option>
            			<option value="0" <?php echo $album->privacy == '0'?'selected':''; ?>>No, keep it private - Private</option>
         				</select></div>
                         <div class="input"><div class="cleardiv"></div> </div>
                        
  					<div class="input">
                    
                    <?php if(!isset($albumID)){ ?>
                    <a name="btnCreate" style="width:303px;"  class="btn"  onClick="executeAlbum('CREATEALBUM')" id="btnCreate"  >Create Album</a>
					<?php } else{?>
                    <a name="btnUpdate" style="width:303px;"  class="btn"  onClick="executeAlbum('UPDATEALBUM')" id="btnUpdate"  >Update Album</a>
                    <?php }?>
                    
                    </div>
  					<div class="input"><div style='color:#ff0000;' id="divLoading"></div></div>
  
  					 <?php if(isset($albumID)){ ?>
                    <div class="input"><a style="width:303px; clear:both;"  href="album.php?albumID=<?php echo $albumID; ?>"  class="btn"    >Go Back to Album</a> </div>
                    <?php } ?>
  
 					</div>
                    
                    
                    
                   
                   
                    </li>
                    
                     
                    
                    </ul>
                    
                    
                </div>
                </section>
			</div>
		</div>      
         
</body>
</html>