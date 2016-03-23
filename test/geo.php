<?php 
echo "<br>ip:".$_SERVER['REMOTE_ADDR']."<br>";
						 
	$geo =  unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=".$_SERVER['REMOTE_ADDR']));
	$country = $geo["geoplugin_countryName"];
	$city = $geo["geoplugin_city"];
	$region = $geo["geoplugin_region"];
	$regionName = $geo["geoplugin_regionName"];
	$latitude = $geo["geoplugin_latitude"];
	$longitude = $geo["geoplugin_longitude"];
							
	echo "country:".$country."<br>";
	echo "city:".$city."<br>";
	echo "region:".$region."<br>";	
	echo "regionName:".$regionName."<br>";	
	echo "latitude:".$latitude."<br>";
	echo "longitude:".$longitude."<br>";
	
	echo var_export(unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=".$_SERVER['REMOTE_ADDR'])));
	
	/*
	array (
  'geoplugin_request' => '108.87.140.31',
  'geoplugin_status' => 200,
  'geoplugin_credit' => 'Some of the returned data includes GeoLite data created by MaxMind, available from http://www.maxmind.com.',
  'geoplugin_city' => 'Little Rock',
  'geoplugin_region' => 'AR',
  'geoplugin_areaCode' => '501',
  'geoplugin_dmaCode' => '693',
  'geoplugin_countryCode' => 'US',
  'geoplugin_countryName' => 'United States',
  'geoplugin_continentCode' => 'NA',
  'geoplugin_latitude' => '34.7771',
  'geoplugin_longitude' => '-92.374802',
  'geoplugin_regionCode' => 'AR',
  'geoplugin_regionName' => 'Arkansas',
  'geoplugin_currencyCode' => 'USD',
  'geoplugin_currencySymbol' => '$',
  'geoplugin_currencySymbol_UTF8' => '$',
  'geoplugin_currencyConverter' => '1',
)
	*/
	
	
	
	//this is another tool:   look at http://ipinfo.io  but it costs per after 1000 requests
	//$ip = $_SERVER['REMOTE_ADDR'];
//$details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
//$coordinates = explode(",", $details->loc); 
//echo $details->city; // -> "Little Rock"
//echo "<br>".$details->loc;
//echo "<br>".$coordinates[0]; // latitude
//echo "<br>".$coordinates[1]; // longitude

?>