<?php
require_once($_SERVER['DOCUMENT_ROOT']."/checkuser.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/lib/Facebook/fbconfig.php');

require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/FacebookSession.php' );
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/FacebookRedirectLoginHelper.php' );
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/FacebookRequest.php' );
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/FacebookResponse.php' );
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/FacebookSDKException.php' );
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/FacebookRequestException.php' );
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/FacebookAuthorizationException.php' );
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/GraphObject.php' );
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/GraphSessionInfo.php' );
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/GraphUser.php' );
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/Entities/AccessToken.php' );
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/HttpClients/FacebookHttpable.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/HttpClients/FacebookCurl.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/HttpClients/FacebookCurlHttpClient.php');



use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\GraphSessionInfo;
use Facebook\GraphUser;
use Facebook\Entities\AcessToken;
use Facebook\HttpClients\FacebookHttpable;
use Facebook\HttpClients\FacebookCurl;
use Facebook\HttpClients\FacebookCurlHttpClient;


@session_start();

if(isset($_SESSION['fb_token']))
	header("Location: facephotos.php");


FacebookSession::setDefaultApplication(FB_ID,FB_SECRET);
// login helper with redirect_uri
$helper = new FacebookRedirectLoginHelper( 'http://oyvent.com/admin/facelogin.php' );


// Requested permissions - optional
 /* $permissions = array(
    'email',
    'user_location',
    'user_birthday',
	'user_photos'
  );*/
  
 $permissions =  array('scope' => 'user_photos,email');
	


try {
  $session = $helper->getSessionFromRedirect();
} catch( FacebookRequestException $ex ) {
  	//echo $ex->getMessage();
} catch( Exception $ex ) {
  	//echo $ex->getMessage();
}

// see if we have a session
if ( isset( $session ) ) {
  $_SESSION['fb_token'] = $session->getToken();	
  header("Location: facephotos.php");
}


?>

<!DOCTYPE html>
<html lang="en" class="no-js">    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">    
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
    
    <link rel="stylesheet" href="/css/icons.css" />
	<!--[if lte IE 7]><script src="/js/lte-ie7.js"></script><![endif]-->
    
    <title>Oyvent - Facebook Login</title>
    <link rel="stylesheet" type="text/css" href="/css/style.css">
    <script src="/js/modernizr.custom.js"></script> 
    
  </head>
  <body class="menu-push">
  
  <?php include("menu.php"); ?>
        
        <div class="container">
			
			<div class="main">
				<section id="contentArea"><!-- This is where all the content goes -->
                               
                	<div class="grid">			
        			
        			<h1>Pick your Facebook Photos</h1>
        			<ul>        	
          				<li><a class="instalogin" href="<?php echo $helper->getLoginUrl($permissions) ?>"><img src="/images/facebook-big.png" title="Facebook Logo"></a></li>
          				<li><a class="instalogin" href="<?php echo $helper->getLoginUrl($permissions) ?>">Login with Facebook</a>
                        <h4>Use your Facebook account to login.</h4>
          				</li>
        			</ul>        
       				</div>
                </section>
			</div>
		</div>
        
        
  </body>
</html>