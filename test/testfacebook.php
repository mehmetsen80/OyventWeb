<?php 

if (!ini_get('display_errors')) {
    ini_set('display_errors', '1');
}

require_once($_SERVER['DOCUMENT_ROOT'].'/lib/Facebook/fbconfig.php');
include($_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php');

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;

//FacebookSession::setDefaultApplication('YOUR_APP_ID','YOUR_APP_SECRET');

// Use one of the helper classes to get a FacebookSession object.
//   FacebookRedirectLoginHelper
//   FacebookCanvasLoginHelper
//   FacebookJavaScriptLoginHelper
// or create a FacebookSession with a valid access token:
//$session = new FacebookSession('access-token-here');

// Get the GraphUser object for the current user:


@session_start();

if(!isset($_SESSION['fb_token']))
	header("Location: facelogin.php");
	

FacebookSession::setDefaultApplication(FB_ID,FB_SECRET);

$fullname = "";

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
	
	//login successful
	if ( isset( $session ) ) {
		try {
  			/*$me = (new FacebookRequest(
   				 $session, 'GET', '/me'
  				))->execute()->getGraphObject(GraphUser::className());*/
  			$request = new FacebookRequest( $session, 'GET', '/me' );
  			$response = $request->execute();
  			// get response
  			$graphObject = $response->getGraphObject(GraphUser::className());  
			$fullname = $graphObject->getName();  
  			echo $fullname;
		} catch (FacebookRequestException $e) {
  			// The Graph API returned an error
		} catch (\Exception $e) {
  		// Some other error occurred
		}
	}
}





?>