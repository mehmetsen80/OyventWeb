<?php 

require_once($_SERVER['DOCUMENT_ROOT']."/lib/twitteroauth/config.php");

/** Set access tokens here - see: https://dev.twitter.com/apps/ **/
$settings = array(
    'oauth_access_token' => ACCESS_TOKEN,
    'oauth_access_token_secret' => ACCESS_TOKEN_SECRET,
    'consumer_key' => CONSUMER_KEY,
    'consumer_secret' => CONSUMER_SECRET
);

?>