<?php
ini_set('display_errors', 1);
require_once($_SERVER['DOCUMENT_ROOT']."/lib/TwitterAPIExchange.php");

/** Set access tokens here - see: https://dev.twitter.com/apps/ **/
$settings = array(
    'oauth_access_token' => "25149170-IgmVV1tlMh7Mm3aZWYvgqof2rlhAHFOqfjXSduo0m",
    'oauth_access_token_secret' => "5LKUFJxZKwpFMUpfLWsOwmwvLewRY4eFbOiZKYFZqJB87",
    'consumer_key' => "B1JGPiMPbePk4KSdKVwBkg0gO",
    'consumer_secret' => "4dbqACnpA5CGkHmeMizINTDkvbRjp6QZepxk38ALj8Ohkg10KE"
);



//list the followers of the twitter user; mehmetsen80
/*$url = 'https://api.twitter.com/1.1/followers/list.json';
$getfield = '?username=mehmetsen80&skip_status=1';
$requestMethod = 'GET';
$twitter = new TwitterAPIExchange($settings);
echo $twitter->setGetfield($getfield)
             ->buildOauth($url, $requestMethod)
             ->performRequest();  */
			 


//search hashtag
$url = 'https://api.twitter.com/1.1/search/tweets.json';
$requestMethod = 'GET';
$getfield = '?q=#worldcup2014&result_type=recent&count=4';

// Perform the request
$twitter = new TwitterAPIExchange($settings);
echo $twitter->setGetfield($getfield)
             ->buildOauth($url, $requestMethod)
             ->performRequest();			 
			 


?>