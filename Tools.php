<?php 


/*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
/*::                                                                         :*/
/*::  This routine calculates the distance between two points (given the     :*/
/*::  latitude/longitude of those points). It is being used to calculate     :*/
/*::  the distance between two locations using GeoDataSource(TM) Products    :*/
/*::                     													 :*/
/*::  Definitions:                                                           :*/
/*::    South latitudes are negative, east longitudes are positive           :*/
/*::                                                                         :*/
/*::  Passed to function:                                                    :*/
/*::    lat1, lon1 = Latitude and Longitude of point 1 (in decimal degrees)  :*/
/*::    lat2, lon2 = Latitude and Longitude of point 2 (in decimal degrees)  :*/
/*::    unit = the unit you desire for results                               :*/
/*::           where: 'M' is statute miles                                   :*/
/*::                  'K' is kilometers (default)                            :*/
/*::                  'N' is nautical miles                                  :*/
/*::  Worldwide cities and other features databases with latitude longitude  :*/
/*::  are available at http://www.geodatasource.com                          :*/
/*::                                                                         :*/
/*::  For enquiries, please contact sales@geodatasource.com                  :*/
/*::                                                                         :*/
/*::  Official Web site: http://www.geodatasource.com                        :*/
/*::                                                                         :*/
/*::         GeoDataSource.com (C) All Rights Reserved 2014		   		     :*/
/*::                                                                         :*/
/*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
function distance($lat1, $lon1, $lat2, $lon2, $unit) {

  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $miles = $dist * 60 * 1.1515;
  $unit = strtoupper($unit);

  if ($unit == "K") {
    return ($miles * 1.609344);
  } else if ($unit == "N") {
      return ($miles * 0.8684);
    } else {
        return $miles;
      }
}
//samples
//echo distance(32.9697, -96.80322, 29.46786, -98.53506, "M") . " Miles<br>";
//echo distance(32.9697, -96.80322, 29.46786, -98.53506, "K") . " Kilometers<br>";
//echo distance(32.9697, -96.80322, 29.46786, -98.53506, "N") . " Nautical Miles<br>";


/*generate a random id based on the length but put at least 4 chars at the end */
function fnc_generate_random_ID($length)
{
  // start with a blank id
  $id = "";

  // define possible characters
  $possible = "0123456789abcdefghijklmnopqrstuvwxyz"; 
    
  // set up a counter
  $i = 0; 
    
  // add random characters to $id until $length is reached
  while ($i < $length) { 

    // pick a random character from the possible ones
    $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
        
    // we don't want id character if it's already in the password
    if (!strstr($password, $char)) { 
      $id .= $char;
      $i++;
    }

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

function  make_links_blank($text)
{
  return  preg_replace(
     array(
       '/(?(?=<a[^>]*>.+<\/a>)
             (?:<a[^>]*>.+<\/a>)
             |
             ([^="\']?)((?:https?|ftp|bf2|):\/\/[^<> \n\r]+)
         )/iex',
       '/<a([^>]*)target="?[^"\']+"?/i',
       '/<a([^>]+)>/i',
       '/(^|\s)(www.[^<> \n\r]+)/iex',
       '/(([_A-Za-z0-9-]+)(\\.[_A-Za-z0-9-]+)*@([A-Za-z0-9-]+)
       (\\.[A-Za-z0-9-]+)*)/iex'
       ),
     array(
       "stripslashes((strlen('\\2')>0?'\\1<a href=\"\\2\">\\2</a>\\3':'\\0'))",
       '<a\\1',
       '<a\\1 target="_blank">',
       "stripslashes((strlen('\\2')>0?'\\1<a href=\"http://\\2\">\\2</a>\\3':'\\0'))",
       "stripslashes((strlen('\\2')>0?'<a href=\"mailto:\\0\">\\0</a>':'\\0'))"
       ),
       $text
   );
}


function FillWithDelimiter($str)
{
   	$str = str_replace(" ", "-", $str);
	$str = str_replace("--", "-", $str);
	return str_replace("---", "-", $str);	
}
   
function RemoveNonAlphaNumericCharacters($str)
{  
  setlocale(LC_ALL, 'en_US.UTF8');

  
  $str = seola($str);
  $str = clearUTF($str);  
  $str= strtolower($str);
  $str = filter_data($str);  
   
  //return $str;
   
   return  preg_replace("/[^a-zA-Z0-9\s]/", " ", $str);
}

function seola($kelime){
        $smad= trim($kelime);
        $search = array('Ç','ç','G','g','i','I','Ö','ö','S','s','Ü','ü',' ','Ä±','Ã§','Ã¼','ÅŸ','Ä°','Ã¶','Åž','ÄŸ','Ã–','Ãœ','Ã‡');
        $replace = array('C','c','G','g','i','I','O','o','S','s','U','u','-','i','c','u','s','i','o','s','g','o','u','c');
        $smad= str_replace($search,$replace,$smad);
		$smad= preg_replace("/[^a-zA-Z0-9\s]/", "-", $smad);
        //$smad= preg_replace('[^a-z0-9]','-',$smad);
        $smad= strtolower($smad);
        return $smad;
    } 


function clearUTF($s)
{
    $r = '';
    $s1 = iconv('UTF-8', 'ASCII//TRANSLIT', $s);
    for ($i = 0; $i < strlen($s1); $i++)
    {
        $ch1 = $s1[$i];
        $ch2 = mb_substr($s, $i, 1);

        $r .= $ch1=='?'?$ch2:$ch1;
    }
    return $r;
}

function getMonth($month)
{
  $str = "";
  
  switch($month)
  {
    case '01':
	 	$str = "Ocak";
	 break;
	case '02':
	 	$str = utf8_encode("Subat");
	 break;
	case '03':
		$str = "Mart";
	 break;
	case '04':
		$str = "Nisan";
	 break;
	case '05':
		$str = utf8_encode("Mayis");
	 break;
	case '06':
		$str = "Haziran";
	 break;
	case '07':
		$str = "Temmuz";
	 break;
	case '08':
	 	$str = utf8_encode("Agustos");
	 break;
	case '09':
		$str = utf8_encode("Eylül");
	 break;
	case '10':
		$str = "Ekim";
	 break;
	case '11':
		$str = utf8_encode("Kasim");
	 break;
	case '12':
		$str = utf8_encode("Aralik");
	 break;
	}
	 
	return $str;
}


// date calculation function
// adds or subtracts a date based upon the input.
// $this_date is a string format of a valid date ie.. "2006/08/11"
// $num_days is the number of days that you would like to add (positive number) or subtract (negative number)

function fnc_date_calc($this_date,$num_days){
   
    $my_time = strtotime ($this_date); //converts date string to UNIX timestamp
    $timestamp = $my_time + ($num_days * 86400); //calculates # of days passed ($num_days) * # seconds in a day (86400)
     $return_date = date("Y/m/d",$timestamp);  //puts the UNIX timestamp back into string format
   
    return $return_date;//exit function and return string
}//end of function

// handle password by hashing it together with $username
// sandukchaahckudnas is the code to strengthen the password 
function hashPassword($_email,$_password)
{
	$password = sha1(md5($_email.'sandukchaahckudnas'.$_password));
	
	return $password;
}


//simple and best data validation, check out with below values
/*var_dump(validateDate('2012-02-28 12:12:12')); # true
var_dump(validateDate('2012-02-30 12:12:12')); # false
var_dump(validateDate('2012-02-28', 'Y-m-d')); # true
var_dump(validateDate('28/02/2012', 'd/m/Y')); # true
var_dump(validateDate('30/02/2012', 'd/m/Y')); # false*/
function validateDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}


// hash username first with md5 then  sha1, after that take the first 30 characters
// this is the same format used in activate page to retrieve the valid activation id
function hashActivateID($_username)
{
    $activateID = substr(sha1(md5($_username)), 0, 30);
	
	return $activateID;
}

function getDateTime($datetime,$timezone,$datediff=false){
	
	$dt = new DateTime($datetime);									
	$dt->setTimeZone(new DateTimeZone($timezone));
	$dt = $dt->format('Y-m-d H:i:s');
	
	return ($datediff)?GetDateDifference($dt,$timezone):$dt;
}

//This functions returns the difference between now and posted datetime
function GetDateDifference($fromTime,$timezone=NULL)
{		
	//Get the current time
	//date_default_timezone_set($timezone);
	//$toTime = mktime(date("H"), date("i"), date("s"), date("m")  , date("d"), date("Y"));
	
	$now = new DateTime();//now
	$now->setTimeZone(new DateTimeZone($timezone));
	$now = $now->format('Y-m-d H:i:s');
	$toTime = strtotime($now);

	//Get the posted time
	$fromTime = strtotime($fromTime);
			
	$distanceInSeconds = round(abs($toTime - $fromTime));
    $distanceInMinutes = round($distanceInSeconds / 60);
       
        if ( $distanceInMinutes <= 1 ) {
            if ( !$showLessThanAMinute ) {
                return ($distanceInMinutes == 0) ? utf8_encode('1 min ago') : utf8_encode('1 min ago');
            } else {
                if ( $distanceInSeconds < 5 ) {
                    return  utf8_encode('5 sec ago');
                }
                if ( $distanceInSeconds < 10 ) {
                    return utf8_encode('10 sec ago');
                }
                if ( $distanceInSeconds < 20 ) {
                    return utf8_encode('20 sec ago');
                }
                if ( $distanceInSeconds < 40 ) {
                    return utf8_encode('30 sec ago');
                }
                if ( $distanceInSeconds < 60 ) {
                    return utf8_encode('1 min ago');
                }
               
                return utf8_encode('1 min ago');
            }
        }
        if ( $distanceInMinutes < 45 ) {
            return $distanceInMinutes . utf8_encode(' min ago');
        }
        if ( $distanceInMinutes < 90 ) {
            return utf8_encode('1 hr ago');
        }
        if ( $distanceInMinutes < 1440 ) {
            return '' . round(floatval($distanceInMinutes) / 60.0) . utf8_encode(' hr ago');
        }
        if ( $distanceInMinutes < 2880 ) {
            return utf8_encode('1 dy ago');
        }
        if ( $distanceInMinutes < 43200 ) {
            return '' . round(floatval($distanceInMinutes) / 1440) . utf8_encode(' dy ago');
        }
        if ( $distanceInMinutes < 86400 ) {
            return utf8_encode('1 mo ago');
        }
        if ( $distanceInMinutes < 525600 ) {
            return round(floatval($distanceInMinutes) / 43200) . utf8_encode(' mo ago');
        }
        if ( $distanceInMinutes < 1051199 ) {
            return utf8_encode('1 yr ago');
        }
       
        return ' ' . round(floatval($distanceInMinutes) / 525600) . utf8_encode(' yr ago');
						
}

function TotalComments($newsId)
{
 	$strQuery = "SELECT * FROM TBLSTATUSCOMMENT WHERE FKSTATUSCONTENTID = '".$newsId."' ";
	$result = executeQuery($strQuery);
	$num_rows = mysql_num_rows($result);
		
	$totalComments = (string)$num_rows;
	return $totalComments;
}

function getPrivacyValue($privacyNo)
{
	$privacyValue;
	
	if($privacyNo == 1)
		$privacyValue = "Visibile to Everyone";
	else if($privacyNo == 2)
		$privacyValue = "Visible Only to my Contacts";
	else //if privacyNo is 0
		$privacyValue = "Visible Only to Me";
		
	return $privacyValue;
}

function isSelected($privacyNo,$num)
{
	$selectedValue = "";
	
	if($privacyNo == $num)
		$selectedValue = "selected";
		
	return $selectedValue;
}


//Get the related profile picture
 function GetProfilePicture($userId){
 	include("../Settings.php");
	
	//$query = "SELECT NAME,PHYSICALPATH,MAX(INSERTIONDATE) FROM TBLUPLOADEDPICTURES WHERE ISPROFILE=1 AND FKUSRID='".$userId."' ";
	
	/*$query = "SELECT P.PKPICTUREID, P.NAME, P.PHYSICALPATH, P.INSERTIONDATE ";
	$query .= "FROM TBLUPLOADEDPICTURES P,(SELECT MAX(INSERTIONDATE) AS MAX_DATE FROM TBLUPLOADEDPICTURES WHERE ISPROFILE=1 AND ";   
	$query .= "FKUSRID=$userId ) P2 WHERE P.ISPROFILE=1 AND P.FKUSRID=$userId AND P.INSERTIONDATE = P2.MAX_DATE";
	*/
	
	$query =  " SELECT PKPICTUREID, NAME, NAMETHUMB, PHYSICALPATH, PHYSICALPATHTHUMB FROM TBLUPLOADEDPICTURES ";
	$query .= " WHERE FKUSRID='".$userId."' AND ISPROFILE='1' ";
	$query .= " ORDER BY INSERTIONDATE DESC ";
	
	$result = executeQuery($query);
	$num_rows = mysql_num_rows($result);
	if($num_rows>0){
		$row = mysql_fetch_array($result);		
		
		if($row["NAME"] != NULL && trim($row["NAME"]) != '')
		{
			if(file_exists("/".$imageServer."/".$row['PHYSICALPATH']."/".$row['NAME']))
				return $imageReverseUrl."/".$row['PHYSICALPATH']."/".$row['NAME'];
			else
				return $sitepath."/images/profile.png";
		}		
		else if($row["NAMETHUMB"] != NULL && trim($row["NAMETHUMB"]) != '')
		{
			if(file_exists("/".$imageServer."/".$row['PHYSICALPATHTHUMB']."/".$row['NAMETHUMB']))
				return $imageReverseUrl."/".$row['PHYSICALPATHTHUMB']."/".$row['NAMETHUMB'];
			else
				return $sitepath."/images/profile.png";
		}
	} 

	
	return $sitepath."/images/profile.png";
}

//Get the profile picture thumb
function GetProfilePictureThumb($userId)
{
	include("../Settings.php");
	
	$query =  " SELECT PKPICTUREID, NAME, NAMETHUMB, NAMETHUMB2, PHYSICALPATH, PHYSICALPATHTHUMB, PHYSICALPATHTHUMB2 ";
	$query .= " FROM TBLUPLOADEDPICTURES ";
	$query .= " WHERE FKUSRID='".$userId."' AND ISPROFILE='1' ";
	$query .= " ORDER BY INSERTIONDATE DESC ";	
	
	
	
	$result = executeQuery($query);
	$num_rows = mysql_num_rows($result);
	if($num_rows>0){
		$row = mysql_fetch_array($result);		
		
		if($row["NAMETHUMB"] != NULL && trim($row["NAMETHUMB"]) != '')
		{
			if(file_exists("/".$imageServer."/".$row['PHYSICALPATHTHUMB']."/".$row['NAMETHUMB']))
				return $imageReverseUrl."/".$row['PHYSICALPATHTHUMB']."/".$row['NAMETHUMB'];
			else
				return $sitepath."/images/profile.png";
		}
		else if( $row["NAMETHUMB2"] != NULL && trim($row["NAMETHUMB2"]) != '')
		{
			if(file_exists("/".$imageServer."/".$row['PHYSICALPATHTHUMB2']."/".$row['NAMETHUMB2']))
				return $imageReverseUrl."/".$row['PHYSICALPATHTHUMB2']."/".$row['NAMETHUMB2'];
			else
				return $sitepath."/images/profile.png";
		}
	}
	
	
	return $sitepath."/images/profile.png";
}

//Get the profile picture thumb2 (the smallest one if exists)
function GetProfilePictureThumb2($userId,$myProfile = 0)
{
	include($_SERVER['DOCUMENT_ROOT']."/Settings.php");	 
	require_once($_SERVER['DOCUMENT_ROOT']."/class/UserInfo.class.php");
	
	$query =  " SELECT PKPICTUREID, NAMETHUMB, NAMETHUMB2, PHYSICALPATHTHUMB, PHYSICALPATHTHUMB2  FROM TBLUPLOADEDPICTURES ";
	$query .= " WHERE FKUSRID='".$userId."' AND ISPROFILE='1' ";
	$query .= " ORDER BY INSERTIONDATE DESC ";	
	

	$result = executeQuery($query);
	$num_rows = mysql_num_rows($result);
	
	$profile_picture = $sitepath."/images/profile.png";
	
	if($num_rows>0){
			
		$row = mysql_fetch_array($result);		
		
		if( $row["NAMETHUMB2"] != NULL && trim($row["NAMETHUMB2"]) != '')
		{
			if(file_exists("/".$imageServer."/".$row['PHYSICALPATHTHUMB2']."/".$row['NAMETHUMB2']))
				$profile_picture = $imageReverseUrl."/".$row['PHYSICALPATHTHUMB2']."/".$row['NAMETHUMB2'];
			else
				$profile_picture = $sitepath."/images/profile.png";
		}
		else if($row["NAMETHUMB"] != NULL && trim($row["NAMETHUMB"]) != '')
		{
			if(file_exists("/".$imageServer."/".$row['PHYSICALPATHTHUMB']."/".$row['NAMETHUMB']))
				$profile_picture = $imageReverseUrl."/".$row['PHYSICALPATHTHUMB']."/".$row['NAMETHUMB'];
			else
				$profile_picture = $sitepath."/images/profile.png";
		}
		
	}
	
	
	if($myProfile)
	{
		@session_start();

		if(isset($_SESSION['userObject']))
		{
			$userObject = $_SESSION['userObject'];
			//to do: show either facebook or twitter profile picture
			$_SESSION['MyProfilePicture'] = $profile_picture;
		}	
	}
	
	return $profile_picture;
	
}



//Get the related profile picture
 function GetProfilePictureID($userId){
 	include("../Settings.php");
		
	/*$query = "SELECT PKPICTUREID, NAME, PHYSICALPATH, P.INSERTIONDATE ";
	$query .= "FROM TBLUPLOADEDPICTURES P,(SELECT MAX(INSERTIONDATE) AS MAX_DATE FROM TBLUPLOADEDPICTURES WHERE ISPROFILE='1' AND ";  	$query .= "FKUSRID='".$userId."' ) P2 WHERE P.ISPROFILE='1' AND P.FKUSRID='".$userId."' AND P.INSERTIONDATE = P2.MAX_DATE";*/
	
	$query =  " SELECT PKPICTUREID, NAMETHUMB, PHYSICALPATHTHUMB FROM TBLUPLOADEDPICTURES ";
	$query .= " WHERE FKUSRID='".$userId."' AND ISPROFILE='1' ";
	$query .= " ORDER BY INSERTIONDATE DESC ";	
	
	$result = executeQuery($query);
	$num_rows = mysql_num_rows($result);
	if($num_rows>0){
		$row = mysql_fetch_array($result);
		
		$id = $row['PKPICTUREID'];
			
		return $id;
	}
	else
		return "-1";
}

function GetProfilePictureName($userId){
 	include("../Settings.php");
			
	$query = "SELECT PKPICTUREID, NAME, PHYSICALPATH, P.INSERTIONDATE ";
	$query .= "FROM TBLUPLOADEDPICTURES P,(SELECT MAX(INSERTIONDATE) AS MAX_DATE FROM TBLUPLOADEDPICTURES WHERE ISPROFILE='1' AND ";    
	$query .= "FKUSRID='".$userId."' ) P2 WHERE P.ISPROFILE='1' AND P.FKUSRID='".$userId."' AND P.INSERTIONDATE = P2.MAX_DATE";
	
	$result = executeQuery($query);
	$num_rows = mysql_num_rows($result);
	if($num_rows>0){
		$row = mysql_fetch_array($result);

		$pictureName = $row['NAME'];
			
		return $pictureName;	//$pictureName;//	
	}
	else
		return "";
}

function GetProfileImageUrl($userId)
{	
	include($_SERVER['DOCUMENT_ROOT']."/Settings.php");
	
	$imagePath='';
	
	$profilePictureName = GetProfilePictureName($userId);
	
	$profilePictureName2  = str_replace('Profile_','',$profilePictureName);
	
	$profileFeedId = GetProfilePictureFeedId($userId,$profilePictureName2);
	
	$imagePath = $imageSubFolder.'/'.$userId.'_'.GetUserName($userId).'/';
	
	if($profileFeedId == "-1")
		$imagePath = '../'.$imagePath.$profilePictureName;
	else
		$imagePath = $sitepath."/feed/Comment.php?feedId=".$profileFeedId;
	
	return $imagePath;
	
}

function GetProfilePictureFeedId($userId,$profileFeedName){
 	include("../Settings.php");
	
	//$query = "SELECT NAME,PHYSICALPATH,MAX(INSERTIONDATE) FROM TBLUPLOADEDPICTURES WHERE ISPROFILE=1 AND FKUSRID='".$userId."' ";
	
	$query = "SELECT PKPICTUREID, NAME, FKFEEDID, PHYSICALPATH, P.INSERTIONDATE ";
	$query .= "FROM TBLUPLOADEDPICTURES P,(SELECT MAX(INSERTIONDATE) AS MAX_DATE FROM TBLUPLOADEDPICTURES WHERE ISPROFILE=0 AND ";   
	$query .= "FKUSRID='".$userId."' ) P2 WHERE P.ISPROFILE='0' AND P.FKUSRID='".$userId."' AND P.NAME='".$profileFeedName."' AND P.INSERTIONDATE = P2.MAX_DATE";
	
	$result = executeQuery($query);
	$num_rows = mysql_num_rows($result);
	if($num_rows>0){
		$row = mysql_fetch_array($result);
		//$pictureName = $sitepath."/".$row['PHYSICALPATH'].$row['NAME'];
		$profileFeedId = $row['FKFEEDID'];
			
		return $profileFeedId;	//$pictureName;//	
	}
	else
		return "-1";
}

//Get the user name of the person 
function GetFullName($userId)
{
	$fullName = '';
	$query = "SELECT FULLNAME FROM TBLUSER WHERE PKUSERID='".$userId."' ";
	$result = executeQuery($query);
	$num_rows = mysql_num_rows($result);
	if($num_rows>0){
		$row = mysql_fetch_array($result);
		$fullName = $row['FULLNAME'];
		
		return $fullName;
	}
	
	
	return $fullName;		
}

//Get the user name of the person 
function GetUserEmail($userId)
{
	$email = '';
	$query = "SELECT EMAIL FROM TBLUSER WHERE PKUSERID='".$userId."' ";
	$result = executeQuery($query);
	$num_rows = mysql_num_rows($result);
	if($num_rows>0){
		$row = mysql_fetch_array($result);
		$email = $row['EMAIL'];
		
		return $email;
	}
	
	
	return $email;		
}

function GetUserName($userId)
{
	$userName = '';
	$query = "SELECT USERNAME FROM TBLUSER WHERE PKUSRID='".$userId."' ";
	$result = executeQuery($query);
	$num_rows = mysql_num_rows($result);
	if($num_rows>0){
		$row = mysql_fetch_array($result);
		$userName = $row['USERNAME'];
		
		return $userName;
	}
	
	
	return $userName;		
}


function utf8_urldecode($str) {
    $str = preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($str));
	//$str = preg_replace("/%u([0-9a-f]{3,4})/i","&x\\1;",urldecode($str));
    return html_entity_decode($str,ENT_COMPAT,'UTF-8');
 }
 
 function decode_utf8($string)
    {
        $accented = array(
            'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'A', 'A',
            'Ç', 'C', 'C', 'Œ',
            'D', 'Ð',
            'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'a', 'a',
            'ç', 'c', 'c', 'œ',
            'd', 'd',
            'È', 'É', 'Ê', 'Ë', 'E', 'E',
            'G',
            'Ì', 'Í', 'Î', 'Ï', 'I',
            'L', 'L', 'L',
            'è', 'é', 'ê', 'ë', 'e', 'e',
            'g',
            'ì', 'í', 'î', 'ï', 'i',
            'l', 'l', 'l',
            'Ñ', 'N', 'N',
            'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'O',
            'R', 'R',
            'S', 'S', 'Š',
            'ñ', 'n', 'n',
            'ò', 'ó', 'ô', 'ö', 'ø', 'o',
            'r', 'r',
            's', 's', 'š',
            'T', 'T',
            'Ù', 'Ú', 'Û', 'U', 'Ü', 'U', 'U',
            'Ý', 'ß',
            'Z', 'Z', 'Ž',
            't', 't',
            'ù', 'ú', 'û', 'u', 'ü', 'u', 'u',
            'ý', 'ÿ',
            'z', 'z', 'ž',
            '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?',
            '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?',
            '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?',
            '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?'
            );

        $replace = array(
            'A', 'A', 'A', 'A', 'A', 'A', 'AE', 'A', 'A',
            'C', 'C', 'C', 'CE',
            'D', 'D',
            'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'a', 'a',
            'c', 'c', 'c', 'ce',
            'd', 'd',
            'E', 'E', 'E', 'E', 'E', 'E',
            'G',
            'I', 'I', 'I', 'I', 'I',
            'L', 'L', 'L',
            'e', 'e', 'e', 'e', 'e', 'e',
            'g',
            'i', 'i', 'i', 'i', 'i',
            'l', 'l', 'l',
            'N', 'N', 'N',
            'O', 'O', 'O', 'O', 'O', 'O', 'O',
            'R', 'R',
            'S', 'S', 'S',
            'n', 'n', 'n',
            'o', 'o', 'o', 'o', 'o', 'o',
            'r', 'r',
            's', 's', 's',
            'T', 'T',
            'U', 'U', 'U', 'U', 'U', 'U', 'U',
            'Y', 'Y',
            'Z', 'Z', 'Z',
            't', 't',
            'u', 'u', 'u', 'u', 'u', 'u', 'u',
            'y', 'y',
            'z', 'z', 'z',
            'A', 'B', 'B', 'r', 'A', 'E', 'E', 'X', '3', 'N', 'N', 'K', 'N', 'M', 'H', 'O', 'N', 'P',
            'a', 'b', 'b', 'r', 'a', 'e', 'e', 'x', '3', 'n', 'n', 'k', 'n', 'm', 'h', 'o', 'p',
            'C', 'T', 'Y', 'O', 'X', 'U', 'u', 'W', 'W', 'b', 'b', 'b', 'E', 'O', 'R',
            'c', 't', 'y', 'o', 'x', 'u', 'u', 'w', 'w', 'b', 'b', 'b', 'e', 'o', 'r'
            );

        return str_replace($accented, $replace, $string);
    }




function GetGoogleMapKey()
{
// generated on web site http://code.google.com/apis/maps/signup.html with funtle.com
return "ABQIAAAAOBjGm334McXTYyP39TFbdhQXX8haAp-ACXWHHTr09g-2DtD9gxRdPXKwR9F13SlVD5pZlyNgGjvprQ";
}


function GetState($abbr)
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

function filter_data($val)
{
  //$val = htmlentities($val,ENT_QUOTES);
  $val = real_escape_string($val);
  //$val = addslashes($val);
  return $val;
}

function is_url_exist($url){
	$ch = curl_init($url);    
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
	$status = curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200? true: 0;
    curl_close($ch);
   	return $status;
}

?>