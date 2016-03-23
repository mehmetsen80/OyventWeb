<?php
/**
* Class Regex
*
* @author Carlo Tasca
* @version 3.0
* @package util
* http://www.weberdev.com/get_example-4768.html
*/
/**
* Provides a set of static methods to test users inputs, $_POSTs and $_GETs
*
*/
class Regex
{
   /**
    * Class cannot be instantiated
    * Throws fatal error if trying to create Regex objects
    *
    */
   private function __construct()
   {
      trigger_error("Class Regex cannot be instantiated",E_USER_ERROR);
   }
   /**
     * Return 1 if anInt is anInteger, 0 otherwise
     * 0 is not valid data
     *
     * @param int $anInt
     * @return int
     */
   public static function isValidInteger ($anInt) {
      $result = 0;
      $ivi = "(^([1-9]{1})(([0-9]{1,10}))?)$";

      if (preg_match($ivi, $anInt)) {
         $result = 1;
      }
      return (int) $result;
   }

   /**
     * Return 1 if anInt is anInteger, 0 otherwise
     * 0 is valid data
     *
     * @param int $anInt
     * @return int
     */
   public static function isValidIntegerNull ($anInt) {
      $result = 0;
      $ivi = "(^([0-9]{1})(([0-9]{1,10}))?)$";

      if (preg_match($ivi, $anInt)) {
         $result = 1;
      }
      return (int) $result;
   }

   /**
     * Return 1 if $aDouble is double/float, 0 otherwise
     * format must be 0.0000
     *
     * @param double $aDouble
     * @return int
     */
   public static function isValidZeroPointFourDigitsDouble ($aDouble) {
      $result = 0;
      $ivd = "^(([0-9]{1,10})(\\.){1}$)([0-9]{1})([0-9]{1})([0-9]{1})([0-9]{1})$";

      if (preg_match($ivd, $aDouble)) {
         $result = 1;
      }
      return (int) $result;
   }

   /**
     * Return 1 if $aDouble is double/float, 0 otherwise
     * format must be 0.00
     *
     * @param double $aDouble
     * @return int
     */
   public static function isValidZeroPointTwoDigitsDouble ($aDouble) {
      $result = 0;
      $ivd = "^(0\\.)([0-9]{1})([0-9]{1})$";

      if (preg_match($ivd, $aDouble)) {
         $result = 1;
      }
      return (int) $result;
   }

   /**
     * Return 1 if $aDouble is double/float, 0 otherwise
     * format must be 0.00 and can be 1.23 (max is 999.99)
     *
     * @param double $aDouble
     * @return int
     */
   public static function isValidUsdFormat ($aDouble) {
      $result = 0;
      $ivusd = "^(([0-9]{1,9})(\\.)?)(([0-9]{0,1})([0-9]{0,1}))$";
      if (eregi($ivusd, $aDouble)) {
         $result = 1;
      }
      return (int) $result;
   }

   /**
     * Return 1 if $aDouble is double/float, 0 otherwise
     * format must be 0.00 and can be 1.23 or 1.2333 (max is 999.9999)
     *
     * @param double $aDouble
     * @return int
     */
   public static function isValidDouble ($aDouble) {
      $result = 0;
      $ivusd = "^(([0-9]{1,9})(\\.)?)(([0-9]{0,1})([0-9]{0,1})(([0-9]{0,1})([0-9]{0,1})?))$";
      if (eregi($ivusd, $aDouble)) {
         $result = 1;
      }
      return (int) $result;
   }
   /**
     * Check a string between 1 and 150 chars
     *
     * @param string $aString
     * @return int
     */
   public static function isValidString($aString)
   {
      $result = 0;
      $regEx = "^([[:alnum:]]){1,150}$";
      if (preg_match($regEx, $aString)) {
         $result = 1;
      }
      return $result;
   }
   
    /**
     * Check a string 
     *
     * @param string $aString
     * @return int
     */
   public static function isValidStringNoLimit($aString)
   {
      $result = 1;
      $regEx = "/\W/";
      if (preg_match($regEx, $aString)) {
         $result = 0;
      }
      return $result;
   }  
   
   
   /**
     * Check a string between 1 and 150 chars
     *
     * @param string $aString
     * @return int
     */
   public static function isValidStringNull($aString)
   {
      $result = 0;
      $regEx = "^([[:alnum:]]){0,150}$";
      if (preg_match($regEx, $aString)) {
         $result = 1;
      }
      return $result;
   }
   
  

   /**
     * Check a string between 1 and 150 chars
     * . char is allowed
     *
     * @param string $aString
     * @return int
     */
   public static function isValidStringAndDot($aString)
   {
      $result = 0;
      $regEx = "^([[:alnum:]]|.){1,150}$";
      if (preg_match($regEx, $aString)) {
         $result = 1;
      }
      return $result;
   }
   /**
     * Returns 0 if username is less than 4 or more than 40 characters, 1 otherwise (legal)
     * Also must be a letter or a char number. No special chars allowed
     *
     * @param string $aUsernameToCheck
     * @return int
     */
   public static function isUsernameLegal($aUsernameToCheck = "")
   {
      $result = 0;
      $regEx = "^[[:alnum:]]{4,20}$";
      if (@ereg($regEx, $aUsernameToCheck)) {
         $result = 1;
      }
      return $result;
   }
   /**
     * Returns 0 if username is an illegal name, 1 otherwise (legal)
     *
     * @param string $aUsernameToCheck
     * @return int
     */

   public static function isIllegalUsername($aUsernameToCheck = "")
   {
      $result = 1;
      $regEx = "^((root)|(bin)|(daemon)|(adm)|(lp)|(sync)|(shutdown)|
        (halt)|(mail)|(news)|(uucp)|(operator)|(games)|(mysql)|(httpd)|
        (nobody)|(dummy)|(www)|(sandukcha)|(cvs)|(shell)|(ftp)|(irc)|(debian)|(ns)|
        (download)|(sport)|(email)|(upload)|(music)|(cinema)|(linux)|(windows)
		|(macintosh)|(toyota)|(mcdonalds)|(media)|(http)|(help)|(computer|(television)))$";

      if (@ereg($regEx, $aUsernameToCheck)) {//added @ by mehmet to avoid depreciation
         $result = 0;
      }
      return $result;
   }
   /**
     * Returns 0 if password contains illegal characters or less than 4 or more than 20,
     * 1 otherwise (legal)
     *
     * @param string $aPasswordToCheck
     * @return int
     */
   public static function isIllegalPassword($aPasswordToCheck = "")
   {
      $result = 0;
      //$regEx = "^[[:alnum:]]{4,20}$";
	  $regEx = "^[[:alnum:]!@#$%&*<.>+=_();:\-]{4,20}$";
	  //at leat one numeric and one character, allow special characters
	  //$rexEx = "^((?=.*\d)(?=.*[a-zA-Z])[a-zA-Z0-9!@#$%&*<.>+=_();:\-]{4,20})$";  
	  //all alpha numerica and special characters allowed, between 4-20 characters
	  //$rexEx = "^[a-zA-Z0-9!@#$%&*<.>+=_();:\-]{4,20})$";
	  //$rexEx = "^[a-zA-Z0-9!@#$%&*]{4,20})$";
      if (@ereg($regEx, $aPasswordToCheck))
      {
         $result = 1;
      }
      return $result;
   }

   /**
     * Returns 1 if a name is valid (between 2 and 10 chars)
     * Can contain - or '. Returns 0 otherwise
     *
     * @param string $aNameToCheck
     * @return int
     */
   public static function fullNameChecker($aNameToCheck = "")
   {
      $result = 0;
      $regEx = "^([[:alpha:]]|-|'){2,10}$";
      if (@ereg($regEx, $aNameToCheck))
      {
         $result = 1;
      }
      return $result;
   }
   /**
     * Checks whether email address is valid
     * Returns 1 if it is, 0 otherwise
     *
     * @param string $aEmailToCheck
     * @return int
     */
   public static function isValidEmailAddress($aEmailToCheck = "")
   {
      $result = 0;
      //$regEx = '^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+' . '@' . '[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+\.' . '[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$';
	  $regEx = '/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])' .
'(([a-z0-9-])*([a-z0-9]))+' . '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i';
	  
      if (preg_match($regEx, $aEmailToCheck)) {
         $result = 1;
      }
      return $result;
   }
   /**
     * Compares two strings for equality
     * Must be same chars and be of same var type (string)
     * Returns 1 if valid, 0 otherwise
     *
     * @param string $aString
     * @param string $anOtherString
     * @return int
     */
   public static function compareTo($aString, $anOtherString)
   {
      $result = 0;
      if ($aString === $anOtherString) {
         $result = 1;
      }
      return $result;
   }

   /**
     * Compares two strings for equality (switched compared to compareTo())
     * Must be same chars and be of same var type (string)
     * Returns 0 if valid, 1 otherwise
     *
     * @param string $aString
     * @param string $anOtherString
     * @return int
     */
   public static function compareTwoStrings($aString, $anOtherString)
   {
      $result = 1;
      if ($aString === $anOtherString) {
         $result = 0;
      }
      return $result;
   }
   /**
    * Return string $ only and only if it doesnt contain any illegal characters
    * Characters to be made illegal should be defined in array $a
    * If illegal characters are found triggers a fatal error
    *
    * @param string $s string to check
    * @param array $a array of illegal characters
    * @return string or fatal error
    */
   public static function isSidValid($s, $a = array())
   {

      foreach ($a as $value)
      {
         if (strchr($s, $value))
         {
            $exception = "Session ID received contains illegal characters: ".$s;
            trigger_error($exception ,E_USER_ERROR);
         }
      }
      return $s;
   }

   /**
    * Overloaded version of isSidValid
    * Return string $s only and only if it doesnt contain any illegal characters
    * Characters to be made illegal should be defined in array $a
    * If illegal characters are found returns false
    *
    * @param string $s string to check
    * @param array $a array of illegal characters
    * @return mixed
    */
   public static function isVarCleanFrom($s, $a = array())
   {

      foreach ($a as $value)
      {
         if (strchr($s, $value))
         {
            return false;
         }
      }
      return $s;
   }
   /**
    * Returns array of illegal characters for SIDs
    *
    * @return array
    */
   public static function returnIllegalSIDchars()
   {
      return array("\"", "$", "#", "<", ">", "*", "(", ")", "{","}", "[", "]", ";", "~", "=");
   }
   /**
    * Returns array of illegal characters for global variable $_GET
    *
    * @return array
    */
   public static function returnIllegalGETchars()
   {
      return array("\"", "$", "<", ">", "*", "(", ")", "{","}", "[", "]", ";", "~", "=");
   }
   /**
    * Returns array of illegal characters for global variable $_POST
    *
    * @return array
    */
   public static function returnIllegalPOSTchars()
   {
      return array("\"", "$", "%","#", "<", ">", "*", "(", ")", "{","}", "[", "]", ";", "~", "=");
   }
   /**
    * Checks variables posted via $_POST
    * Returns a $_POST[$key] value only if free from illegal characters defined
    * in returnIllegalPOSTchars() method
    *
    * @return array
    */
   public static function checkGlobalPosts()
   {
      foreach ($_POST as $key => $value)
      {
         $_POST[$key] = Regex::isVarCleanFrom($value, Regex::returnIllegalPOSTchars());
      }
      return $_POST;
   }
   /**
    * Checks variables posted via $_GET
    * Returns a $_GET[$key] value only if free from illegal characters defined
    * in returnIllegalGETchars() method
    *
    * @return array
    */
   public static function checkGlobalGets()
   {
      foreach ($_GET as $key => $value)
      {
         $_GET[$key] = Regex::isVarCleanFrom($value, Regex::returnIllegalGETchars());
      }
      return $_GET;
   }
   /**
    * Triggers a fatal error if a page receives $_POST variables
    * This method can be called within pages that do not require to receive $_POSTs
    * Just an extra layer of security
    *
    */
   public static function isGettingGlobalPOST()
   {
      if (count($_POST) > 0)
      {
         trigger_error("File cannot receive posted data", E_USER_ERROR);
      }
   }   
   /**
    * Triggers a fatal error if a page receives $_GET variables
    * This method can be called within pages that do not require to receive $_GETs
    * Just an extra layer of security
    *
    */
   public static function isGettingGlobalGET()
   {
      if (count($_GET) > 0)
      {
         trigger_error("File cannot get data", E_USER_ERROR);
      }
   }
}
?>