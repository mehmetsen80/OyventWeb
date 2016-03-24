<?php 

include($_SERVER['DOCUMENT_ROOT']."/Settings.php");	
require_once($_SERVER['DOCUMENT_ROOT']."/class/Picture.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Album.class.php");

 $processType= ($_GET['processType'])?$_GET['processType']:$_POST['processType'];

 switch($processType)
 {
 	case "UPLOADTWITTERPHOTO":	//upload Instagram Photo
		uploadTwitterPhoto();
		break;
	case "DELETEPHOTO":
		deletePhoto();
		break;
	
 }
 
 function uploadTwitterPhoto()
 {

	include($_SERVER['DOCUMENT_ROOT']."/Settings.php");			
	
		
	$allowedExtensions = array("jpg", "jpeg", "png", "gif", "png", "bmp", "wbmp", "jpg:large","jpeg:large","jpg:small","jpeg:small","jpg:medium","jpeg:medium","jpg:thumb","jpeg:thumb","png:large","png:medium","png:small","png:thumb");		
	$sizeLimit = 8*1024*1024; // max file size in bytes, 8 MB
	
	$userID = $_GET["userID"];
	$albumID = $_GET["albumID"];
	
	$result = array("success"	=>	false,	"error"	=>	"System error, please try again!", "pkPhotoID" => '');
 
 	if(isset($userID) && isset($albumID))
	{		
		$caption = $_GET['caption'];		
		$twitterid = $_GET['twitterid'];		
		$largeurl = $_GET['largeurl'];		
		$mediumurl = $_GET['mediumurl'];
		$smallurl = $_GET['smallurl'];
		$thumburl = $_GET['thumburl'];
		$ownedby = $_GET['ownedby'];
		$contentlink = $_GET['contentlink'];
		$createdtime = $_GET['createdtime'];
		$latitude = $_GET['latitude'];		
		$longitude = $_GET['longitude'];		
		$favs = $_GET['favs'];
		$rts = $_GET['rts'];
			
		
		/*$targetFilePath = '/'.$imageServer.'/'.$imageSubFolder.'/'.$userID.'/';
		$httpFilePath = $sitepath.'/'.$imageSubFolder.'/'.$userID.'/';*/	
		
			
		
		//let's upload file
		$uploader = new UploadFileHandler($allowedExtensions, $sizeLimit,$userID,$albumID,$ownedby,$twitterid, $caption, $largeurl,$mediumurl,$smallurl,$thumburl,$contentlink,$createdtime,$latitude,$longitude,$favs,$rts);
		$uploader->httpPath = $httpFilePath;
		
		//$result = array('success'	=>	true, 'info' => "httpFilePath:".$httpFilePath );
		
		$my_array = $uploader->uploadFromTwitter();
		list($success,$error,$pkPhotoID) = array_values($my_array);
	
		if($success)
		{
			$result = array('success'	=>	true, 'error' => '', 'pkPhotoID' => $pkPhotoID);
		}
		else
		{
			$result = array("success" => $success, "error" => $error, "pkPhotoID" => $pkPhotoID);
		}
	}	
	
	echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);	
 
 }
 
 class UploadFileHandler {  
	
	private $allowedExtensions = array();
    private $sizeLimit = 6291456; // 6*1024*1024 6291456 bytes - 6 MB
	public $smallurl;
    public $largeurl;
	public $mediumurl;
	public $thumburl;
	public $httpPath;
	public $physicalPath;	
	public $userID;
	public $albumID;	
	public $ownedby;
	public $twitterid;
	public $caption;
	public $fkTwitterID = NULL;
	public $contentlink;
	public $createdtime;
	public $latitude;
	public $longitude;
	public $favs;
	public $rts;
	
	function __construct(array $allowedExtensions = array(), $sizeLimit = 6291456, $userID,$albumID,$ownedby,$twitterid,$caption,$largeurl,$mediumurl=NULL,$smallurl=NULL,$thumburl=NULL,$contentlink,$createdtime,$latitude,$longitude,$favs,$rts){        
        $allowedExtensions = array_map("strtolower", $allowedExtensions);
            
        $this->allowedExtensions = $allowedExtensions;        
        $this->sizeLimit = $sizeLimit;
		$this->userID = $userID;
		$this->albumID = $albumID;		
		$this->ownedby = $ownedby;
		$this->twitterid = $twitterid;
		$this->caption = $caption;
		$this->contentlink = $contentlink;
		$this->createdtime = $createdtime;
		$this->latitude =  isset($latitude) && is_numeric($latitude) && !empty($latitude)?$latitude:0;
		$this->longitude = isset($longitude ) && is_numeric($longitude) && !empty($longitude)?$longitude:0;	
		$this->favs = (isset($favs) && is_numeric($favs) && !empty($favs))?$favs:0;
		$this->rts = (isset($rts) && is_numeric($rts) && !empty($rts))?$rts:0;
		$this->largeurl = (isset($largeurl) && !empty($largeurl))?$largeurl:false;			
		$this->mediumurl = $mediumurl;
		$this->smallurl = $smallurl;
		$this->thumburl = $thumburl;		
    }
	
	
 	function uploadFromTwitter()
 	{
		require_once($_SERVER['DOCUMENT_ROOT']."/class/Picture.class.php");
		include($_SERVER['DOCUMENT_ROOT']."/Settings.php");	
		require_once($_SERVER['DOCUMENT_ROOT']."/db/DbConnection.php");
		require_once($_SERVER['DOCUMENT_ROOT'].'/class/UUID.class.php');
		include($_SERVER['DOCUMENT_ROOT']."/lib/aws/awsfnc.php");
		require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");
		
		try{		
		
			@session_start();
		
			beginTrans(); // transaction begins		
		
		
			//if directory not exists, then create one
			/*if(!is_dir ($uploadDirectory)) 		
				mkdir($uploadDirectory);		
		
			if (!is_writable($uploadDirectory))
            	@chmod($uploadDirectory,777);	*/		        	
        
        	if (!$this->largeurl)
            	return array('success'	=>	false, 'error' => 'No files were uploaded!', 'pkPhotoID' => '');			
			
			
			$pathinfo = pathinfo($this->largeurl);
        	$filename = $pathinfo['filename'];       
        	$ext = $pathinfo['extension'];
			$ext = strtolower($ext);
			$ext = "JPG";

//			if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
//            	$these = implode(', ', $this->allowedExtensions);
//            	return array('success'	=>	false, 'error' => 'File has an invalid extension:'.$ext.' It should be one of: '.$these.'.');
//			}
			
			
			/*$folderPath = $imageSubFolder.'/'.$this->userID;			
			$picnewname =  time();
			$newfilelarge = $uploadDirectory.$picnewname.'_large.jpg';	
			$newfilemedium =  $uploadDirectory.$picnewname.'_medium.jpg';
			$newfiletsmall =  $uploadDirectory.$picnewname.'_small.jpg';
			$newfilethumb =  $uploadDirectory.$picnewname.'_thumb.jpg';	
			$this->physicalPath = $newfile;
			$this->httpPath = $this->httpPath.$picnewname.'_Thumb2.'.strtolower($ext);*/
		
		
			$bucket = "s3-oy-vent-images-16";
			$foldername = isset($this->userID)?$this->userID:"common";			
			$keyprefix = UUID::generate(UUID::UUID_RANDOM, UUID::FMT_STRING,"93717953");			
			$keyprefix = str_replace("-","",$keyprefix);
			$keyprefix =  strlen($keyprefix)>=16?substr($keyprefix,0,16):$keyprefix;
		
			
			//large picture
			$keylarge = $keyprefix.'-large.jpg';
			$piclarge = new Picture();
			$piclarge->load($this->largeurl,strtoupper($ext));	
			$width = $piclarge->getWidth() > 720?720:$piclarge->getWidth();				
			$piclarge->resizeToWidth($width);	
			$newfilelarge = '/tmp/'.$keylarge.'.jpg';				
			$piclarge->save($newfilelarge,90,0777);
			$resultlarge = createObject($foldername, $keylarge, $newfilelarge);
			$URLLARGE = $resultlarge['ObjectURL'];
			$piclarge_size = $piclarge->getFileSize();			      
			$piclarge->destroy_buffer();
			if(file_exists($newfilelarge))
				unlink($newfilelarge);		
		
		 
			
			//medium picture
			$keymedium = $keyprefix.'-medium.jpg';
			$picmedium = new Picture();				
			$picmedium->load($this->largeurl, strtoupper($ext));
			$width = $picmedium->getWidth() > 250?250:$picmedium->getWidth();
			$picmedium->resizeToWidth($width);
			$newfilemedium = '/tmp/'.$keymedium.'.jpg';	
			$picmedium->save($newfilemedium,90,0777);
			$resultmedium = createObject($foldername, $keymedium, $newfilemedium);			
			$URLMEDIUM = $resultmedium['ObjectURL'];
			$picmedium_size = $picmedium->getFileSize();
			$picmedium->destroy_buffer();			
			if(file_exists($newfilemedium))
				unlink($newfilemedium);				
				
		 		 
			//small picture
			$keysmall = $keyprefix.'-small.jpg';
			$picsmall = new Picture();				
			$picsmall->load($this->thumburl, strtoupper($ext));	//Twitter thumb is exactly 150x150		
			$width = $picsmall->getWidth() > 150?150:$picsmall->getWidth();
			$picsmall->resizeToWidth($width);
			$newfilesmall = '/tmp/'.$keysmall.'.jpg';				
			$picsmall->save($newfilesmall,90,0777);
			$resultsmall = createObject($foldername, $keysmall, $newfilesmall);
			$URLSMALL = $resultsmall['ObjectURL'];
			$picsmall_size = $picsmall->getFileSize();
			$picsmall->destroy_buffer();
			if(file_exists($newfilesmall))
				unlink($newfilesmall);			
		
			//thumb picture
			$keythumb = $keyprefix.'-thumb.jpg';			
			$picsthumb = new Picture();				
			$picsthumb->load($this->thumburl, strtoupper($ext));			
			$picsthumb->resize(50,50); //let's accept all the thumbnails as 50x50		
			$newfilethumb = '/tmp/'.$keythumb.'.jpg';			
			$picsthumb->save($newfilethumb,90,0777);
			$resultthumb = createObject($foldername, $keythumb, $newfilethumb);
			$URLTHUMB = $resultthumb['ObjectURL'];
			$picsthumb_size = $picsthumb->getFileSize();
			$picsthumb->destroy_buffer();
			if(file_exists($newfilethumb))
				unlink($newfilethumb);			
			
			date_default_timezone_set('America/Chicago');
			$insertionDate = date("Y-m-d H:i:s");			
						
				
			//first lookup Instagram table if previously inserted of the same Instagram photo id
			if(isset($this->twitterid) && !empty($this->twitterid) 
				&& isset($this->ownedby) && !empty($this->ownedby)){
				
				$query = "SELECT PKTWITTERID FROM TBLTWITTER WHERE TWITTERID = '".$this->twitterid."' ";				
				$result = executeQueryForTrans($query);				
  
 	 			if(mysql_num_rows($result)>0)
 	 			{
					$row = mysql_fetch_row($result);
					$this->fkTwitterID =  $row[0];					
				}
				else
				{										
					$query = " INSERT INTO TBLTWITTER 
					(TWITTERID,URLLARGE,URLMEDIUM,URLSMALL,URLTHUMB,CAPTION,CONTENTLINK,
					CREATEDDATE,LATITUDE,LONGITUDE,FAVS,RTS,POSTDATE,OWNEDBY) 
					VALUES('".$this->twitterid."','".$this->largeurl."','".$this->mediumurl."',
					'".$this->smallurl."','".$this->thumburl."', '".$this->caption."','".$this->contentlink."', 
					'".$this->createdtime."', '".$this->latitude."','".$this->longitude."', '".$this->favs."',
					'".$this->rts."', '".$insertionDate."', '".$this->ownedby."') ";					
					$this->fkTwitterID = executeInsertQueryForTrans($query);
				}
			}		
			
			
			$picUUID = "-1";
			$picUUID = UUID::generate(UUID::UUID_RANDOM, UUID::FMT_STRING,"03178949");
			$picUUID = str_replace("-","",$picUUID);
			$picUUID = strlen($picUUID)>=16?substr($picUUID,0,16):$picUUID;	
			
			
			$query = "SELECT PKPHOTOID FROM TBLPHOTO WHERE FKALBUMID='".$this->albumID."'
			 AND FKTWITTERID='".$this->fkTwitterID."' ";
			 
			 $result = executeQueryForTrans($query);
			 if(mysql_num_rows($result)>0)
 	 		 {
				 $row = mysql_fetch_row($result);
				 $pkPhotoID = $row[0];
				 return array('success'	=>	false, 'error' => 'Photo already exists on that album!', 'pkPhotoID' => $pkPhotoID); 
			 }
			 else {			
				//we force every picture to be jpeg on saving to database			
				/*$query  = " INSERT INTO TBLPHOTO 
				(PHOTOUUID,FKUSERID,FKALBUMID,FKTWITTERID,NAMELARGE,NAMEMEDIUM,NAMESMALL,NAMETHUMB,SIZELARGE, 
			 	SIZEMEDIUM, SIZESMALL ,SIZETHUMB,PHYSICALPATH,IMAGETYPE,POSTDATE,FIRSTINIP) 
			 	VALUES('".$picUUID."','".$this->userID."','".$this->albumID."','".$this->fkTwitterID."',
			  	'".$picnewname."_large.jpg', '".$picnewname."_medium.jpg','".$picnewname."_small.jpg',
			  	'".$picnewname."_thumb.jpg', '".$piclarge_size."','".$picmedium_size."','".$picsmall_size."',
			  	'".$picsthumb_size."', '".$folderPath."','JPG','".$insertionDate."','".$_SERVER['REMOTE_ADDR']."')";	*/
				
				
				$geo =  unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=".$_SERVER['REMOTE_ADDR']));
				$geolatitude = $geo["geoplugin_latitude"];
				$geolongitude = $geo["geoplugin_longitude"];
				
				
				$query  = " INSERT INTO TBLPHOTO 
				(PHOTOUUID,FKUSERID, FKALBUMID, FKTWITTERID, KEYMAIN, KEYLARGE, KEYMEDIUM, KEYSMALL, 
				KEYTHUMB, URLLARGE, URLMEDIUM, URLSMALL, URLTHUMB, SIZELARGE, SIZEMEDIUM, SIZESMALL ,SIZETHUMB, 
				FOLDER,BUCKET,IMAGETYPE,LATITUDE,LONGITUDE,CONTENTLINK,OWNEDBY,CREATEDDATE,POSTDATE,FIRSTINIP
				,LAYOUTTYPE, LAYOUTINFO, USERAGENT) 
			 	VALUES('".$picUUID."','".$this->userID."','".$this->albumID."','".$this->fkTwitterID."',
				'".$keyprefix."','".$keylarge."','".$keymedium."','".$keysmall."','".$keythumb."',
				'".$URLLARGE."','".$URLMEDIUM."','".$URLSMALL."','".$URLTHUMB."', '".$piclarge_size."', 
				'".$picmedium_size."','".$picsmall_size."','".$picsthumb_size."', '".$foldername."', 
				'".$bucket."', 'JPG', '".$geolatitude."', '".$geolongitude."', '".$this->contentlink."',
				'".$this->ownedby."', '".$this->createdtime."','".$insertionDate."','".$_SERVER['REMOTE_ADDR']."'
				,'".$_SESSION["layoutType"]."','".$_SESSION["layoutInfo"]."','".$_SESSION["userAgent"]."')";
													
																		
			 	$pkPhotoID = executeInsertQueryForTrans($query);
			 }
						
			 if($picUUID != "-1")											
			 {
				commitTrans(); // transaction is committed
				return array('success'	=>	true, 'error' => false, 'pkPhotoID' => $pkPhotoID);	
				
			 }
			 else
			 {
				rollbackTrans();
				return array('success'	=>	false, 'error' => 'Invalid UUID!', 'pkPhotoID' => $pkPhotoID); 
			 }
			
			 	
			
		}
		catch(Exception $e){
			rollbackTrans(); // transaction rolls back		
			return array('success'	=>	false, 'error' => 'Invalid Picture Addition!');	
		}
 	}
 }
 
 function deletePhoto()
 {
	 
	$pkPhotoID = $_GET["pkPhotoID"];
	$userID = $_GET["userID"];
	
	//$result = array('success'=>false, 'message' =>"test:".$pkPhotoID."  ".$userID);
	
	$album = new Album($userID);
	$result =  $album->deletePhoto($pkPhotoID)?array('success'=>true, "message" => "Photo deleted successfully!"):array('success' => false, "message","Photo could not be deleted, please try again!");
	
	echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
	
 }
 

?>