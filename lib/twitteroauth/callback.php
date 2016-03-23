<?php
/**
 * @file
 * Take the user when they return from Twitter. Get access tokens.
 * Verify credentials and redirect to based on response from Twitter.
 */

/* Start session and load lib */

require_once($_SERVER['DOCUMENT_ROOT'].'/lib/twitteroauth/twitteroauth.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/lib/twitteroauth/config.php');
require_once($_SERVER['DOCUMENT_ROOT']."/class/UserInfo.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/db/DbConnection.php");

@session_start();
$userObject = $_SESSION['userObject'];
if(!isset($userObject))
	header('Location: ../../index.php');

/* If the oauth_token is old redirect to the connect page. */
if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
  $_SESSION['oauth_status'] = 'oldtoken';
  //echo "it is just too old!";
  header('Location: ../../admin/twitlogin.php');
}

/* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
$twitteroauth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
/* Request access tokens from twitter */
$access_token = $twitteroauth->getAccessToken($_REQUEST['oauth_verifier']);
/* Save the access tokens. Normally these would be saved in a database for future use. */
$_SESSION['access_token'] = $access_token;

/* Remove no longer needed request tokens */
unset($_SESSION['oauth_token']);
unset($_SESSION['oauth_token_secret']);

/* If HTTP response is 200 continue otherwise send to connect page to retry */
if (200 == $twitteroauth->http_code) {
  /* The user has been verified and the access tokens can be saved for future use */
  	$_SESSION['status'] = 'verified';
 
	/*The user is verified now let's get the user's credentials*/
	$twitdetails = $twitteroauth->get('account/verify_credentials');
	if (isset($twitdetails->error)){
   		header('Location: ../../admin/twitlogin.php');
		//echo "twitdetails error:".$twitdetails->error;
	}

	/*Update the user's twitter ID*/
	// use this later $twitdetails->screen_name (username),$twitdetails->id (twitter id),$twitdetails->name (twitter fullname)						
	$query = "UPDATE TBLUSER SET TWITTERID='".$twitdetails->id."' WHERE PKUSERID ='".$userObject->userID."' ";
	$result = executeQuery($query);
	//print_r($twitdetails);
	//print_r("<br><br><br>");
	$userObject->twitdetails = $twitdetails;
	$userObject->twitterID = $twitdetails->id;
	$_SESSION['userObject'] = $userObject;
	$_SESSION['twitteroauth'] = $twitteroauth;
    //print_r($_SESSION['userObject']);
  	header('Location: ../../admin/twitphotos.php');
} else {
  /* Save HTTP status for error dialog on connnect page.*/
  header('Location: ../../admin/twitlogin.php');
  //echo "sorry didn't work out! http_code:".$connection->http_code;
}

?>