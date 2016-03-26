<?php 

@session_start();

if (isset($_SESSION['userObject']) && !empty($_SESSION['userObject']))
	header("Location: /admin/");
	
$geo =  unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=".$_SERVER['REMOTE_ADDR']));
$region = $geo["geoplugin_region"];

$album = "";
if($region == "AR")
	$album = "UALR";
else if($region == "NY")
	$album = "UALBANY";


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
        
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <header id="mainHeader">
            <div class="container">
                <a href="/" id="mainLogo"></a><span style='padding-left:20px; font-size:16px; margin-top:20px;width:60px;'>Beta</span>
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
                    	<h2><b><u>What's happening at NAU?</u></b></h2>
                        <h3 style='font-size:22px;font-weight:normal;'>It's now easier to learn what's happening at your campus. We help you to share posts in one place. With Oyvent, you can view, import, download, vote and share trending community related posts.
</h3><br>
                        <h2><b><u>New way of communication <?php //echo $album; ?></u></b></h2>
                        <h3 style='font-size:22px;font-weight:normal;'>Oyvent allows you to communicate visually. View posts based on interests, and then group them together in your specific group. You can also filter best rated posts and learn how community members respond.</h3>
                        <a href="/Explore.php" class="btn btn-default btn-lg">Explore</a>
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
                            
                                <input type="text" name="fullname" id="txtFullName" name="txtFullName"  placeholder="Full Name"><br>
                                <input type="text" name="txtEmail2" id="txtEmail2" onFocus="validateEmailSignUpUser()"  onKeyUp="validateEmailSignUpUser()" placeholder="Email"><br>
                                <input type="password" name="txtPassword2" onKeyPress="fireRegisterUser(event)"  id="txtPassword2"   placeholder="Password"><br>
                                <a href='/Terms.php' style='font-size:12px;' class='download' >Terms & Conditions</a>
                                <br>
                                <input type="submit"  onClick="signUpUser()" id="btnCreate" value="Sign Up for Oyvent"><br><div id="divSignUpMessage"  class="red" ></div>
                        </div>  
                            
                        
                    </div>
                </div>
            </div>
        </section>

        	<section class="highlight">            
            	<div class="container">                
                	<div id="preview" class="hidden-xs">
                    	<header>
                        	<span></span>
                        	<span></span>
                        	<span></span>
                    	</header>
                    	<a rel='fancybox-thumb' class='fancybox-thumb' href='/images/screenshot.jpg' ><img src="/images/screenshot.jpg" alt=""></a>
                	</div>
            	</div>           
        	</section>
      
        
       

        <footer id="mainFooter">
            <div class="container">
                <p>&copy; Copyright 2015 | <a href="http://oyvent.com">Oyvent</a> | <a class="blue" href="/Contact.php">Contact</a></p>
            </div>
        </footer>
        

    </body>
</html>
