<?php
require_once($_SERVER['DOCUMENT_ROOT']."/checkuser.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/lib/twitteroauth/config.php');



 if (array_key_exists("login", $_GET)) {
    $oauth_provider = $_GET['oauth_provider'];
    if ($oauth_provider == 'twitter') {
        header("Location: ../lib/twitteroauth/redirect.php");
    }
}

?>

<!DOCTYPE html>
<html lang="en" class="no-js">    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">    
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
    
    <link rel="stylesheet" href="/css/icons.css" />
	<!--[if lte IE 7]><script src="/js/lte-ie7.js"></script><![endif]-->
    
    <title>Oyvent - Twitter Login</title>
    <link rel="stylesheet" type="text/css" href="/css/style.css">
    <script src="/js/modernizr.custom.js"></script> 
    
  </head>
  <body class="menu-push">
  
  <?php include("menu.php"); ?>
        
        <div class="container">
			
			<div class="main">
				<section id="contentArea"><!-- This is where all the content goes -->
                               
                	<div class="grid">			
        			
        			<h1>Pick your Twitter Photos</h1>
        			<ul>        	
          				<li><img src="/images/twitter-big.png" title="Twitter logo"></li>
          				<li><a class="instalogin" href="?login&oauth_provider=twitter">» Login with Twitter</a>
                        <h4>Use your Twitter account to login.</h4>
          				</li>
        			</ul>        
       				</div>
                </section>
			</div>
		</div>
        
        
  </body>
</html>