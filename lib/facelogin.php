<?php 

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
$id = '553429438101230';
$secret = 'e852fbbf9c364095ae0776f8c88b65ac';
// init app with app id (APPID) and secret (SECRET)
FacebookSession::setDefaultApplication($id,$secret);


// login helper with redirect_uri
$helper = new FacebookRedirectLoginHelper( 'http://oyvent.com/lib/facelogin.php' );

try {
  $session = $helper->getSessionFromRedirect();
} catch( FacebookRequestException $ex ) {
  	//echo $ex->getMessage();
} catch( Exception $ex ) {
  	//echo $ex->getMessage();
}

if(isset($_SESSION['fb_token'])){
	$session = new FacebookSession($_SESSION['fb_token']);
	
	try{
		if ( !$session->validate($id,$secret) ) {
			$session = NULL;
		}
	}catch( FacebookAuthorizationException $ex){
		$session = NULL;
		//echo $ex->getMessage();
	}
}

// see if we have a session
if ( isset( $session ) ) {
  $_SESSION['fb_token'] = $session->getToken();	
	
  // graph api request for user data
  echo "Login Successful<br>";
  $request = new FacebookRequest( $session, 'GET', '/me' );
  $response = $request->execute();
  // get response
  $graphObject = $response->getGraphObject(GraphUser::className());  
  echo "Hi ".$graphObject->getName();
  
  /*$graphObject = $response->getGraphObject();  */
  
   // print logout url using session and redirect_uri (logout.php page should destroy the session)
echo '<a href="' . $helper->getLogoutUrl( $session, 'http://oyvent.com/logout.php?id=logout' ) . '">Logout</a>';

  // print data
  echo '<pre>' . print_r( $graphObject, 1 ) . '</pre>';
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