<?php
require_once($_SERVER['DOCUMENT_ROOT']."/checkuser.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Instagram.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/admin/Instagram.config.php");


// create login URL
$loginUrl = $instagram->getLoginUrl();


?>

<!DOCTYPE html>
<html lang="en" class="no-js">    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">    
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
    
    <link rel="stylesheet" href="/css/icons.css" />
	<!--[if lte IE 7]><script src="/js/lte-ie7.js"></script><![endif]-->
    
    <title>Oyvent - Instagram Login</title>
    <link rel="stylesheet" type="text/css" href="/css/style.css">
    <script src="/js/modernizr.custom.js"></script> 
    
  </head>
  <body class="menu-push">
  
  <?php include("menu.php"); ?>
        
        <div class="container">
			
			<div class="main">
				<section id="contentArea"><!-- This is where all the content goes -->
                               
                	<div class="grid">			
        			
        			<h1>Pick your Instagram Photos</h1>
        			<ul>        	
          				<li><img src="../images/instagram-big.png" alt="Instagram logo"></li>
          				<li><a class="instalogin" href="<?php echo $loginUrl ?>">» Login with Instagram</a>
            			<h4>Use your Instagram account to login.</h4>
          				</li>
        			</ul>        
       				</div>
                </section>
			</div>
		</div>
        
        
  </body>
</html>