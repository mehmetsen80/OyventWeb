<?php 

require_once($_SERVER['DOCUMENT_ROOT']."/db/DbConnection.php");

class Profile{

	public $pkUserID = '';
 	public $fullname = '';
	public $urllarge = '';
	public $urlmedium = '';
	public $urlsmall = '';
	public $urlthumb = '';
	
	function __construct($pkUserID=NULL) {
		if(isset($pkUserID)){
			$this->pkUserID = $pkUserID;
		}
	}
	
	function getProfilePhoto(){
		if(isset($this->pkUserID)){
			
			$query = "SELECT u.PKUSERID, u.FULLNAME, pr.ISMAINPHOTO,
			 pr.URLLARGE, pr.URLMEDIUM, pr.URLSMALL, pr.URLTHUMB FROM TBLUSER u 
			LEFT OUTER JOIN TBLPROFILEPHOTO pr ON u.PKUSERID = pr.FKUSERID AND pr.ISMAINPHOTO = '1'
			WHERE u.PKUSERID = '".$this->pkUserID."'  ";
			$result = executeQuery($query);			
				
			if(mysql_num_rows($result)>0)
			{
				$row = mysql_fetch_array($result);
				$this->pkUserID = $row["PKUSERID"];
				$this->fullname = $row["FULLNAME"];
				$this->urllarge = $row["URLLARGE"];
				$this->urlmedium = $row["URLMEDIUM"];
				$this->urlsmall = $row["URLSMALL"];
				$this->urlthumb = $row["URLTHUMB"];
			}
		}
	}
}

?>