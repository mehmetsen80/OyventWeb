<?php 

require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/FacebookSession.php' );
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/FacebookRedirectLoginHelper.php' );
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/FacebookRequest.php' );
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/FacebookResponse.php' );
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/FacebookSDKException.php' );
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/FacebookRequestException.php' );
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/FacebookAuthorizationException.php' );
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/GraphObject.php' );
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/Entities/AccessToken.php' );
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/HttpClients/FacebookCurlHttpClient.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/lib/Facebook/HttpClients/FacebookHttpable.php');

use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\Entities\AcessToken;
use Facebook\HttpClients\FacebookCurlHttpClient;
use Facebook\HttpClients\FacebookHttpable;

@session_start();
$id = '553429438101230';
$secret = 'e852fbbf9c364095ae0776f8c88b65ac';
// init app with app id (APPID) and secret (SECRET)
FacebookSession::setDefaultApplication($id,$secret);


// login helper with redirect_uri
$helper = new FacebookRedirectLoginHelper( 'http://oyvent.com/test/facebook.php' );

try {
  $session = $helper->getSessionFromRedirect();
} catch( FacebookRequestException $ex ) {
  	echo $ex->getMessage();
} catch( Exception $ex ) {
  	echo $ex->getMessage();
}

if(isset($_SESSION['token'])){
	$session = new FacebookSession($_SESSION['token']);
	
	try{
		$session->validate($id,$secret);
	}catch( FacebookAuthorizationException $ex){
		$session = '';
		echo $ex->getMessage();
	}
}

// see if we have a session
if ( isset( $session ) ) {
  $_SESSION['token'] = $session->getToken();	
	
  // graph api request for user data
  echo "Login Successful<br>";
  $request = new FacebookRequest( $session, 'GET', '/me' );
  $response = $request->execute();
  // get response
  $graphObject = $response->getGraphObject();
  
  echo "Hi ".$graphObject->getName();

  // print data
  echo  print_r( $graphObject, 1 );
} else {
  // show login url
  echo '<a href="' . $helper->getLoginUrl() . '">Login</a>';
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Untitled Document</title>
</head>

<body>
</body>
</html>