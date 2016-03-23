<?php 

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


@session_start();

$userObject = NULL;
if (isset($_SESSION['userObject']) & !empty($_SESSION['userObject']))
	$userObject = $_SESSION['userObject'];

	

?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Oyvent</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
        <link href="http://fonts.googleapis.com/css?family=Roboto:100,300,100italic,400,300italic" rel="stylesheet" type="text/css">
        
     
        
        <link rel="stylesheet" href="/css/main.css">
         <link rel="stylesheet" href="/css/icons.css" />
	<!--[if lte IE 7]><script src="/js/lte-ie7.js"></script><![endif]-->
       
    <link href="/css/style.css" rel="stylesheet">
        
        <script src="/js/vendor/modernizr-2.6.2.min.js"></script>
        
        
        <link href="/css/custom-theme/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">
		<!-- javascript -->
		<script src="/js/lib/jquery-1.10.2.js"></script>
		<script src="/js/lib/jquery-ui-1.10.4.custom.min.js"></script>
        
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
       
		<script src="/js/General.js"></script>
		<script src="/js/Login.js"></script>
		<script src="/js/Register.js"></script>
        
        <!-- bxSlider Javascript file -->
		<script src="/js/lib/jquery.bxslider/jquery.bxslider.min.js"></script>
		<!-- bxSlider CSS file -->
		<link href="/js/lib/jquery.bxslider/jquery.bxslider.css" rel="stylesheet" />
        <script src="/js/main.js" ></script>        
    	<script src="/js/General.js"></script>    	
   	 	<script src="/js/modernizr.custom.js"></script> 
        
        
    </head>
    <body>
    
    <input type="hidden" id="txtUserID" value="<?php echo $userObject->userID ?>" >
    
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <header id="mainHeader">
            <div class="container">
                <a href="/" id="mainLogo"></a>
                <ul id="mainNav">
                    <li><a class="btn btn-default" href="/Explore.php">Explore</a></li>
                   <!-- <li><a class="btn btn-default" href="#">About</a></li> -->
                </ul>
            </div>
        </header>

        <section id="homeTop">
            <div class="container">
                <div class="row">
                    <div id="welcome" class="col-sm-8">
                    	<h1>Reinvent Group Photos</h1>
                        <h3>A social tool for group photos that allows venues and events to connect people based on experiences and photos they 

create.</h3><br>
                        <h1>Import Photos</h1>
                        <h3>A simple way to collect all the photos from Instagram, Twitter and Facebook. Create albums from hashtags and share with your friends.</h3>
                        <a href="/Explore.php" class="btn btn-default btn-lg">Explore Now</a>
                    </div>
                    <div class="col-sm-4">
                        <div class="panel">
                            
                                <input type="text"  name="txtEmail" id="txtEmail" onFocus="initLogin()" placeholder="Email"><br>
                                <input type="password" onKeyPress="fireLoginUser(event)" onFocus="initLogin()" name="txtPassword" id="txtPassword" placeholder="Password"><br>
                                <input type="submit" id="btnLogin" onClick="loginUser()" value="Sign In">
                                <a href="/login/Forgot.php" class="download" style='font-size:12px;'   title="Forgot Password" alt="Forgot Password" target="_self">Forgot password?</a>
                                <div class="red" id="divLoginMessage"></div>
                           
                        </div>
                        <div class="panel">
                            <h4>Sign Up</h4>
                            
                                <input type="text" name="fullname" id="txtFullName" name="txtFullName" onFocus="checkFullName();"  onKeyUp="checkFullName()" placeholder="Full Name"><br>
                                <input type="text" name="txtEmail2" id="txtEmail2" onFocus="validateEmailSignUpUser()"  onKeyUp="validateEmailSignUpUser()" placeholder="Email"><br>
                                <input type="password" name="txtPassword2" onKeyPress="fireRegisterUser(event)"  id="txtPassword2"   placeholder="Password"><br>
                                <input type="submit"  onClick="signUpUser()" id="btnCreate" value="Sign Up for Oyvent"><br><div id="divSignUpMessage"  class="red" ></div>
                        </div>  
                            
                        
                    </div>
                </div>
            </div>
        </section>

        	
            
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
                
                 	 <ul id="photos">
                     </ul>
                     
                 </div>
                                
                
                </section>
			</div>
		</div>
            
            
            
            
  <?php include("footer.php") ?>
  
  <div class="modal" id="dgModal"><div id="loading"></div></div>
      
       
       

        <footer id="mainFooter">
            <div class="container">
                <p>&copy; Copyright 2014 | <a href="http://oyvent.com">Oyvent</a></p>
            </div>
        </footer>
        
        
        
        

    </body>
</html>
