<?php 

require_once($_SERVER['DOCUMENT_ROOT']."/db/DbConnection.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");

class Track{

	public $pkTrackID = NULL;
	public $fkUserID;
	public $userIP;	
	public $requestURI;
	public $referer;
	public $layouttype;
	public $layoutinfo;
	public $useragent;
	public $postdate;

	function __construct($userID,$ip,$uri,$ref,$ltype,$linfo,$agent)
  	{
		$this->fkUserID = $userID;
		$this->userIP = $ip;
		$this->requestURI = $uri;
		$this->referer = $ref;
		$this->layouttype = $ltype;
		$this->layoutinfo = $linfo;
		$this->useragent = $agent;
	}
	
	public function logUser(){
		
		try{
			
			beginTrans(); // transaction begins
			
			date_default_timezone_set('America/Chicago');
			$date = date('Y-m-d H:i:s');
			
			$query = "INSERT INTO TBLTRACK 
			(FKUSERID,USERIP,REQUESTURI,HTTPREFERER,LAYOUTTYPE,LAYOUTINFO,USERAGENT,POSTDATE) 
			 VALUES('".$this->fkUserID."','".$this->userIP."','".$this->requestURI."','".$this->referer."','".$this->layouttype."','".$this->layoutinfo."','".$this->useragent."', '".$date."' )";
			 
			$this->pkTrackID = executeInsertQueryForTrans($query);
			
			if(isset($this->pkTrackID))											
			{
				commitTrans(); // transaction is committed
			}	
			
		}
		catch(Exception $e){
			rollbackTrans(); // transaction rolls back		
			//return array('success'	=>	false, 'error' => 'Invalid Log Insertion!', 'pkTrackID' => '');	
		}
	}

}

?>