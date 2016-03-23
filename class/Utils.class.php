<?php

class Utils {

	
/*generate a random id based on the length but put at least 4 chars at the end */
public static function  fnc_generate_random_ID($length)
{
  // start with a blank id
  $id = "";

  $id = "827a68ce3b";
  return $id;
  
  // define possible characters
  $possible = "0123456789abcdefghijklmnopqrstuvwxyz"; 
    
  // set up a counter
  $i = 0; 
    
  // add random characters to $id until $length is reached
  while ($i < $length) { 

    // pick a random character from the possible ones
    $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
        
    // we don't want id character if it's already in the password
    /*if (!strstr($password, $char)) { 
      $id .= $char;
      $i++;
    }*/

  }
  
  // We have to have at least 4 characters of password
  $k = strlen($id);
  while($k<4)
  {
     $k++;
     $id .= 'a';
  }

  // done!
  return $id;

}


public static function getMonth($month)
{
  $str = "";
  
  switch($month)
  {
    case '01':
	 	$str = "Januray";
	 break;
	case '02':
	 	$str = "February";
	 break;
	case '03':
		$str = "March";
	 break;
	case '04':
		$str = "April";
	 break;
	case '05':
		$str = "May";
	 break;
	case '06':
		$str = "June";
	 break;
	case '07':
		$str = "July";
	 break;
	case '08':
	 	$str = "August";
	 break;
	case '09':
		$str = "September";
	 break;
	case '10':
		$str = "October";
	 break;
	case '11':
		$str = "November";
	 break;
	case '12':
		$str = "December";
	 break;
	}
	 
	return $str;
}


// date calculation function
// adds or subtracts a date based upon the input.
// $this_date is a string format of a valid date ie.. "2006/08/11"
// $num_days is the number of days that you would like to add (positive number) or subtract (negative number)

public static function fnc_date_calc($this_date,$num_days){
   
    $my_time = strtotime ($this_date); //converts date string to UNIX timestamp
    $timestamp = $my_time + ($num_days * 86400); //calculates # of days passed ($num_days) * # seconds in a day (86400)
     $return_date = date("Y/m/d",$timestamp);  //puts the UNIX timestamp back into string format
   
    return $return_date;//exit function and return string
}//end of function

// handle password by hashing it together with $username
// sandukchaahckudnas is the code to strengthen the password 
public static function hashPassword($_email,$_password)
{
	return sha1(md5($_email.'sandukchaahckudnas'.$_password));
}


// hash username first with md5 then  sha1, after that take the first 30 characters
// this is the same format used in activate page to retrieve the valid activation id
public static function hashActivateID($_username)
{
    return substr(sha1(md5($_username)), 0, 30);
}

//This functions returns the difference between now and posted datetime
public static function GetDateDifference($fromTime)
{		
	//Get the current time
	date_default_timezone_set("America/Chicago");
	$toTime = mktime(date("H"), date("i"), date("s"), date("m")  , date("d"), date("Y"));

	//Get the posted time
	$fromTime = strtotime($fromTime);
			
	$distanceInSeconds = round(abs($toTime - $fromTime));
    $distanceInMinutes = round($distanceInSeconds / 60);
       
        if ( $distanceInMinutes <= 1 ) {
            if ( !$showLessThanAMinute ) {
                return ($distanceInMinutes == 0) ? 'less than a minute ago' : '1 minute ago';
            } else {
                if ( $distanceInSeconds < 5 ) {
                    return 'less than 5 seconds ago';
                }
                if ( $distanceInSeconds < 10 ) {
                    return 'less than 10 seconds ago';
                }
                if ( $distanceInSeconds < 20 ) {
                    return 'less than 20 seconds ago';
                }
                if ( $distanceInSeconds < 40 ) {
                    return 'half a minute ago';
                }
                if ( $distanceInSeconds < 60 ) {
                    return 'less than a minute ago';
                }
               
                return '1 minute ago';
            }
        }
        if ( $distanceInMinutes < 45 ) {
            return $distanceInMinutes . ' minutes ago';
        }
        if ( $distanceInMinutes < 90 ) {
            return '1 hour ago';
        }
        if ( $distanceInMinutes < 1440 ) {
            return '' . round(floatval($distanceInMinutes) / 60.0) . ' hours ago';
        }
        if ( $distanceInMinutes < 2880 ) {
            return '1 day ago';
        }
        if ( $distanceInMinutes < 43200 ) {
            return '' . round(floatval($distanceInMinutes) / 1440) . ' days ago';
        }
        if ( $distanceInMinutes < 86400 ) {
            return '1 month ago';
        }
        if ( $distanceInMinutes < 525600 ) {
            return round(floatval($distanceInMinutes) / 43200) . ' months ago';
        }
        if ( $distanceInMinutes < 1051199 ) {
            return '1 year ago';
        }
       
        return 'over ' . round(floatval($distanceInMinutes) / 525600) . ' years ago';
						
}


public static function utf8_urldecode($str) {
    $str = preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($str));
    return html_entity_decode($str,null,'UTF-8');;
 }


public static function GetGoogleMapKey()
{
	// generated on web site http://code.google.com/apis/maps/signup.html with funtle.com
	return "ABQIAAAAOBjGm334McXTYyP39TFbdhQXX8haAp-ACXWHHTr09g-2DtD9gxRdPXKwR9F13SlVD5pZlyNgGjvprQ";
}


public static function GetState($abbr)
{
  $result = "";
  switch($abbr)
  {
  	case "AL":
	   	$result = "ALABAMA";
	break;
	case "AK":
		$result = "ALASKA";
	break;
	case "AS":
		$result = "AMERICAN SAMOA";
	break;
	case "AZ":
		$result = "ARIZONA";
	break;	
	case "AR":
		$result = "ARKANSAS";
	break;
	case "CA":
		$result = "CALIFORNIA";
	break;
	case "CO":
		$result = "COLORADO";
	break;
	case "CT":
		$result = "CONNECTICUT";
	break;
	case "DE":
		$result = "DELAWARE";
	break;	
	case "DC":
		$result = "DISTRICT OF COLUMBIA";
	break;
	case "FM":
		$result = "FEDERATED STATES OF MICRONESIA";
	break;
	case "FL":
		$result = "FLORIDA";
	break;
	case "GA":
		$result = "GEORGIA";
	break;
	case "GU":
		$result = "GUAM";
	break;
	case "HI":
		$result = "HAWAII";
	break;
	case "ID":
		$result = "IDAHO";
	break;
	case "IL":
		$result = "ILLINOIS";
	break;
	case "IN":		
		$result = "INDIANA";
	break;
	case "IA":
		$result = "IOWA";
	break;
	case "KS":
		$result = "KANSAS";
	break;
	case "KY":
		$result = "KENTUCKY";
	break;
	case "LA":
		$result = "LOUISIANA";
	break;
	case "ME":
		$result = "MAINE";
	break;
	case "MH":
		$result = "MARSHALL ISLANDS";
	break;
	case "MD":
		$result = "MARYLAND";
	break;
	case "MA":
		$result = "MASSACHUSETTS";
	break;
	case "MI":
		$result = "MICHIGAN";
	break;
	case "MN":
		$result = "MINNESOTA";
	break;
	case "MS":
		$result = "MISSISSIPPI";
	break;
	case "MO":
		$result = "MISSOURI";
	break;
	case "MT":
		$result = "MONTANA";
	break;
	case "NE":
		$result = "NEBRASKA";
	break;
	case "NV":
		$result = "NEVADA";
	break;
	case "NH":
		$result = "NEW HAMPSHIRE";
	break;
	case "NJ":
		$result = "NEW JERSEY";
	break;
	case "NM":
		$result = "NEW MEXICO";
	break;
	case "NY":
		$result = "NEW YORK";
	break;
	case "NC":
		$result = "NORTH CAROLINA";		
	break;
	case "ND":
		$result = "NORTH DAKOTA";
	break;
	case "MP";
		$result = "NORTHERN MARIANA ISLANDS";
	break;
	case "OH":
		$result = "OHIO";
	break;
	case "OK":
		$result = "OKLAHOMA";
	break;
	case "OR":
		$result = "OREGON";
	break;
	case "PW":
		$result = "PALAU";
	break;
	case "PA":
		$result = "PENNSYLVANIA";
	break;
	case "PR":
		$result = "PUERTO RICO";
	break;
	case "RI":
		$result = "RHODE ISLAND";
	break;
	case "SC":
		$result = "SOUTH CAROLINA";
	break;
	case "SD":
		$result = "SOUTH DAKOTA";
	break;
	case "TN":
		$result = "TENNESSEE";
	break;
	case "TX":
		$result = "TEXAS";
	break;
	case "UT":
		$result = "UTAH";
	break;
	case "VT":
		$result = "VERMONT";
	break;
	case "VI":
		$result = "VIRGIN ISLANDS";
	break;
	case "VA":
		$result = "VIRGINIA";
	break;
	case "WA":
		$result = "WASHINGTON";
	break;
	case "WV":
		$result = "WEST VIRGINIA";
	break;
	case "WI":
		$result = "WISCONSIN";
	break;
	case "WY":
		$result = "WYOMING";
	break;

  }
  
  return $result;
}
	
}

?>