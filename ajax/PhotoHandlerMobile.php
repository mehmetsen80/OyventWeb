<?php 

include($_SERVER['DOCUMENT_ROOT']."/Settings.php");	
require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Picture.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Album.class.php");

 $processType= ($_GET['processType'])?$_GET['processType']:$_POST['processType'];

 switch($processType)
 {
 	case "UPLOADPHOTO":	//upload Instagram Photo
		uploadPhoto();//upload content photo
		break;
	case "DELETEPHOTO":
		deletePhoto();//delete content photo
		break;	
	case "UPLOADPROFILEPHOTO":
		uploadProfilePhoto();
		break;
 }
 
 class UploadFile { 	

	function __construct()
	{		
	}

	/**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function saveFile($path,$ext="") {
        if(!move_uploaded_file($_FILES['file']['tmp_name'], $path)){
            return false;
        }
		
        return true;
    }
    public function getName() {
        return $_FILES['file']['name'];
    }
    
	public function getExt()
	{
		$pathinfo = pathinfo($this->getName());     
		return $pathinfo['extension'];
	}
	
	public function getSize() {
        return $_FILES['file']['size'];
    }
	
	public function getFile()
	{
		return $_FILES['file']['tmp_name'];
	}
	
}

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
 class UploadFileHandler {  
	
	private $allowedExtensions = array();
    private $sizeLimit = 8388608; // 8*1024*1024 8388608 bytes - 8 MB
    private $file;	
	private $userID;
	private $albumID;
	private $latitude;
	private $longitude;
	private $caption;
	
	function __construct(array $allowedExtensions = array(), $sizeLimit = 8388608, $userID,$albumID,$latitude,$longitude, $caption){        
        $allowedExtensions = array_map("strtolower", $allowedExtensions);            
        $this->allowedExtensions = $allowedExtensions;        
        $this->sizeLimit = $sizeLimit;
		$this->userID = $userID;
		$this->albumID = $albumID;
		$this->latitude = $latitude;
		$this->longitude = $longitude;
		$this->caption = $caption;
		$this->file = (isset($_FILES['file']))? new UploadFile():false;		
    }		
	
		
 	function uploadFromMobile()
 	{
		require_once($_SERVER['DOCUMENT_ROOT']."/class/Picture.class.php");
		include($_SERVER['DOCUMENT_ROOT']."/Settings.php");	
		require_once($_SERVER['DOCUMENT_ROOT']."/db/DbConnection.php");
		require_once($_SERVER['DOCUMENT_ROOT'].'/class/UUID.class.php');		
		include($_SERVER['DOCUMENT_ROOT']."/lib/aws/awsfnc.php");
		require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");
		
		try{		
			beginTrans(); // transaction begins	
			
			if (!$this->file)
            	return array('success'	=>	false, 'error' => 'File does not exist!', 'pkPhotoID' => ''); 
        	
			$size = $this->file->getSize();
			
			if ($size == 0)
            	return array('success'	=>	false,'error' => 'File is empty or too large to upload!', 'pkPhotoID' => '');        	        
        	if ($size > $this->sizeLimit)
            	return array('success'	=>	false,'error' => 'File is too large!', 'pkPhotoID' => '');        	
		
			$ext = strtolower($this->file->getExt());

        	if($this->allowedExtensions && !in_array($ext, $this->allowedExtensions)){
           	 $these = implode(', ', $this->allowedExtensions);			
            	return array('success'	=>	false, 'error' => 'File has an invalid extension, it should be one of: '.$these.'.', 'pkPhotoID' => '');         }		
		
			//let's start to prepare the variable keys
			$tempFile =  $this->file->getFile();			
			$bucket = "s3-oy-vent-images-14";			
			$foldername = isset($this->userID)?$this->userID:"common";			
			$keyprefix = UUID::generate(UUID::UUID_RANDOM, UUID::FMT_STRING,"97881743");			
			$keyprefix = str_replace("-","",$keyprefix);
			$keyprefix =  strlen($keyprefix)>=16?substr($keyprefix,0,16):$keyprefix;
			
			//large picture
			$keylarge = $keyprefix.'-large.jpg';
			$piclarge = new Picture();
			$piclarge->load($tempFile,strtoupper($ext));		
			$width = $piclarge->getWidth() > 720?720:$piclarge->getWidth();					
			$piclarge->resizeToWidth($width);	
			$newfilelarge = '/tmp/'.$keylarge.'.jpg';				
			$piclarge->save($newfilelarge,75,0777);			
			$resultlarge = createObject($foldername, $keylarge, $newfilelarge);
			$URLLARGE = $resultlarge['ObjectURL'];
			$piclarge_size = $piclarge->getFileSize();			      
			$piclarge->destroy_buffer();
			if(file_exists($newfilelarge))
				unlink($newfilelarge);
				
			//medium picture
			$keymedium = $keyprefix.'-medium.jpg';
			$picmedium = new Picture();						
			$picmedium->load($tempFile, strtoupper($ext));					
			$width = $picmedium->getWidth();
			if($width >= 250)		
				$picmedium->resize(250,250);
			else
				$picmedium->resizeToWidth($width);			
			$newfilemedium = '/tmp/'.$keymedium.'.jpg';	
			$picmedium->save($newfilemedium,75,0777);			
			$resultmedium = createObject($foldername, $keymedium, $newfilemedium);			
			$URLMEDIUM = $resultmedium['ObjectURL'];
			$picmedium_size = $picmedium->getFileSize();
			$picmedium->destroy_buffer();			
			if(file_exists($newfilemedium))
				unlink($newfilemedium);
		 	
					 
		
			//small picture
			$keysmall = $keyprefix.'-small.jpg';
			$picsmall = new Picture();				
			$picsmall->load($tempFile, strtoupper($ext));			
			$picsmall->resize(150,150); //let's accept all the smalls as 150x150
			$newfilesmall = '/tmp/'.$keysmall.'.jpg';				
			$picsmall->save($newfilesmall,75,0777);			
			$resultsmall = createObject($foldername, $keysmall, $newfilesmall);
			$URLSMALL = $resultsmall['ObjectURL'];
			$picsmall_size = $picsmall->getFileSize();
			$picsmall->destroy_buffer();
			if(file_exists($newfilesmall))
				unlink($newfilesmall);
			
			
			//thumb picture
			$keythumb = $keyprefix.'-thumb.jpg';
			$picsthumb = new Picture();				
			$picsthumb->load($tempFile, strtoupper($ext));			
			$picsthumb->resize(50,50); //let's accept all the thumbnails as 50x50	
			$newfilethumb = '/tmp/'.$keythumb.'.jpg';			
			$picsthumb->save($newfilethumb,75,0777);
			$resultthumb = createObject($foldername, $keythumb, $newfilethumb);
			$URLTHUMB = $resultthumb['ObjectURL'];
			$picsthumb_size = $picsthumb->getFileSize();
			$picsthumb->destroy_buffer();
			if(file_exists($newfilethumb))
				unlink($newfilethumb);
			
			date_default_timezone_set('America/Chicago');
			$insertionDate = date("Y-m-d H:i:s");
			
			$picUUID = "-1";
			$picUUID = UUID::generate(UUID::UUID_RANDOM, UUID::FMT_STRING,"02371965");
			$picUUID = str_replace("-","",$picUUID);
			$picUUID = strlen($picUUID)>=16?substr($picUUID,0,16):$picUUID;
			
			//we force every picture to be jpeg on saving to database			
			$query  = " INSERT INTO TBLPHOTO 
				(PHOTOUUID, FKUSERID, FKALBUMID, CAPTION, KEYMAIN, KEYLARGE, KEYMEDIUM, KEYSMALL, 
				KEYTHUMB,URLLARGE, URLMEDIUM, URLSMALL, URLTHUMB, SIZELARGE, SIZEMEDIUM, SIZESMALL ,SIZETHUMB, 
				FOLDER, BUCKET, IMAGETYPE, LATITUDE, LONGITUDE, POSTDATE, FIRSTINIP) 
			 	VALUES('".$picUUID."', '".$this->userID."', '".$this->albumID."', '".$this->caption."',
				'".$keyprefix."', '".$keylarge."', '".$keymedium."', '".$keysmall."', '".$keythumb."',
				'".$URLLARGE."', '".$URLMEDIUM."', '".$URLSMALL."', '".$URLTHUMB."', '".$piclarge_size."', 
				'".$picmedium_size."', '".$picsmall_size."', '".$picsthumb_size."', '".$foldername."', 
				'".$bucket."', 'JPG','".$this->latitude."', '".$this->longitude."', '".$insertionDate."', '".$_SERVER['REMOTE_ADDR']."')";						
																		
			$pkPhotoID = executeInsertQueryForTrans($query);
			
			if($picUUID != "-1")											
			 {
				commitTrans(); // transaction is committed
				return array('success'	=>	true, 'error' => '', 'pkPhotoID' => $pkPhotoID);				
			 }
			 else
			 {
				rollbackTrans();
				return array('success'	=>	false, 'error' => 'Invalid UUID!', 'pkPhotoID' => $pkPhotoID); 
			 }	
			
			
		}//end of try
		catch(Exception $e)
		{
			rollbackTrans(); // transaction rolls back		
			return array('success'	=>	false, 'error' => 'Invalid Picture Addition Exception!', 'pkPhotoID' => '');	
		}
 	}
	
	function uploadProfilePhoto(){ //ios8+
		
		require_once($_SERVER['DOCUMENT_ROOT']."/class/Picture.class.php");
		include($_SERVER['DOCUMENT_ROOT']."/Settings.php");	
		require_once($_SERVER['DOCUMENT_ROOT']."/db/DbConnection.php");
		require_once($_SERVER['DOCUMENT_ROOT'].'/class/UUID.class.php');		
		include($_SERVER['DOCUMENT_ROOT']."/lib/aws/awsfnc.php");
		require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");
		
		try{		
			beginTrans(); // transaction begins	
			
			 if (!$this->file){
           	 return array('success'	=>	false, 'error' => 'No image file was uploaded!');
        	}		
		
       		$size = $this->file->getSize();
			
        
        	if ($size == 0) {
            	return array('success'	=>	false,'error' => 'File is empty or too large to upload!');
        	}
        
        	if ($size > $this->sizeLimit) {
            	return array('success'	=>	false,'error' => 'File is too large!');
        	}
			
			$pathinfo = pathinfo($this->file->getName());
        	$filename = $pathinfo['filename'];       
        	$ext = $pathinfo['extension'];
			$ext = strtolower($ext);			

        	if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
            	$these = implode(', ', $this->allowedExtensions);				
            	return array('success'=>false, 'error'=>'File has an invalid extension, it should be one of '. $these);
        	}
			
			$tempFile =  $this->file->getFile();
			
					
			$bucket = "s3-oy-vent-images-14";			
			$foldername = isset($this->userID)?$this->userID:"common";			
			$keyprefix = UUID::generate(UUID::UUID_RANDOM, UUID::FMT_STRING,"97881743");			
			$keyprefix = str_replace("-","",$keyprefix);
			$keyprefix =  strlen($keyprefix)>=16?substr($keyprefix,0,16):$keyprefix;
			
				
				
			//large picture
			$keylarge = $keyprefix.'-large.jpg';
			$piclarge = new Picture();
			$piclarge->load($tempFile,strtoupper($ext));		
			$width = $piclarge->getWidth() > 720?720:$piclarge->getWidth();					
			$piclarge->resizeToWidth($width);	
			$newfilelarge = '/tmp/'.$keylarge.'.jpg';				
			$piclarge->save($newfilelarge,75,0777);			
			$resultlarge = createObject($foldername, $keylarge, $newfilelarge);
			$URLLARGE = $resultlarge['ObjectURL'];
			$piclarge_size = $piclarge->getFileSize();			      
			$piclarge->destroy_buffer();
			if(file_exists($newfilelarge))
				unlink($newfilelarge);
			
			
				
			//medium picture
			$keymedium = $keyprefix.'-medium.jpg';
			$picmedium = new Picture();						
			$picmedium->load($tempFile, strtoupper($ext));					
			$width = $picmedium->getWidth();
			if($width >= 250)		
				$picmedium->resize(250,250);
			else
				$picmedium->resizeToWidth($width);			
			$newfilemedium = '/tmp/'.$keymedium.'.jpg';	
			$picmedium->save($newfilemedium,75,0777);			
			$resultmedium = createObject($foldername, $keymedium, $newfilemedium);			
			$URLMEDIUM = $resultmedium['ObjectURL'];
			$picmedium_size = $picmedium->getFileSize();
			$picmedium->destroy_buffer();			
			if(file_exists($newfilemedium))
				unlink($newfilemedium);
		 	
				
			//small picture
			$keysmall = $keyprefix.'-small.jpg';
			$picsmall = new Picture();				
			$picsmall->load($tempFile, strtoupper($ext));			
			$picsmall->resize(150,150); //let's accept all the smalls as 150x150
			$newfilesmall = '/tmp/'.$keysmall.'.jpg';				
			$picsmall->save($newfilesmall,75,0777);			
			$resultsmall = createObject($foldername, $keysmall, $newfilesmall);
			$URLSMALL = $resultsmall['ObjectURL'];
			$picsmall_size = $picsmall->getFileSize();
			$picsmall->destroy_buffer();
			if(file_exists($newfilesmall))
				unlink($newfilesmall);
				
				
			
			//thumb picture
			$keythumb = $keyprefix.'-thumb.jpg';
			$picsthumb = new Picture();				
			$picsthumb->load($tempFile, strtoupper($ext));			
			$picsthumb->resize(50,50); //let's accept all the thumbnails as 50x50	
			$newfilethumb = '/tmp/'.$keythumb.'.jpg';			
			$picsthumb->save($newfilethumb,75,0777);
			$resultthumb = createObject($foldername, $keythumb, $newfilethumb);
			$URLTHUMB = $resultthumb['ObjectURL'];
			$picsthumb_size = $picsthumb->getFileSize();
			$picsthumb->destroy_buffer();
			if(file_exists($newfilethumb))
				unlink($newfilethumb);
			
			
			date_default_timezone_set('America/Chicago');
			$insertionDate = date("Y-m-d H:i:s");
			
			$picUUID = "-1";
			$picUUID = UUID::generate(UUID::UUID_RANDOM, UUID::FMT_STRING,"02371965");
			$picUUID = str_replace("-","",$picUUID);
			$picUUID = strlen($picUUID)>=16?substr($picUUID,0,16):$picUUID;
			
			$select = "SELECT PKPROFILEPHOTOID, FOLDER, KEYLARGE, KEYMEDIUM, KEYSMALL, KEYTHUMB,
						URLLARGE, URLMEDIUM, URLSMALL, URLTHUMB
						 FROM TBLPROFILEPHOTO WHERE FKUSERID = '".$this->userID."' AND ISMAINPHOTO='1' ";
			 
			 $result = executeQueryForTrans($select);
			 while($row = mysql_fetch_array($result)){
					
					$query = "DELETE FROM  TBLPROFILEPHOTO WHERE PKPROFILEPHOTOID = '".$row["PKPROFILEPHOTOID"]."' ";
					$delete = executeQueryForTrans($query);
				
					if($delete > 0)
					{						
						$isdeleted = is_url_exist($row["URLLARGE"])? deleteObject($row['FOLDER'],$row['KEYLARGE']):0;
						$isdeleted = is_url_exist($row["URLMEDIUM"])? deleteObject($row['FOLDER'],$row['KEYMEDIUM']):0;
						$isdeleted = is_url_exist($row["URLSMALL"])? deleteObject($row['FOLDER'],$row['KEYSMALL']):0;
						$isdeleted = is_url_exist($row["URLTHUMB"])? deleteObject($row['FOLDER'],$row['KEYTHUMB']):0;
					}
			 }
			
			//we force every picture to be jpeg on saving to database			
			$query  = " INSERT INTO TBLPROFILEPHOTO  
				(PHOTOUUID, FKUSERID, ISMAINPHOTO, CAPTION, KEYMAIN, KEYLARGE, KEYMEDIUM, KEYSMALL, 
				KEYTHUMB,URLLARGE, URLMEDIUM, URLSMALL, URLTHUMB, SIZELARGE, SIZEMEDIUM, SIZESMALL ,SIZETHUMB, 
				FOLDER, BUCKET, IMAGETYPE, POSTDATE, FIRSTINIP) 
			 	VALUES('".$picUUID."', '".$this->userID."', '1', '".$this->caption."',
				'".$keyprefix."', '".$keylarge."', '".$keymedium."', '".$keysmall."', '".$keythumb."',
				'".$URLLARGE."', '".$URLMEDIUM."', '".$URLSMALL."', '".$URLTHUMB."', '".$piclarge_size."', 
				'".$picmedium_size."', '".$picsmall_size."', '".$picsthumb_size."', '".$foldername."', 
				'".$bucket."', 'JPG', '".$insertionDate."', '".$_SERVER['REMOTE_ADDR']."')";						
			
			//return array('success'	=>	false,'error' => 'query:'.$query);
			
												
			$pkPhotoID = executeInsertQueryForTrans($query);
			//return array('success'	=>	false,'error' => 'query:'.$query.' pkPhotoID:'.$pkPhotoID);
			
			if(isset($pkPhotoID))											
			 {
			//test
			//return array('success'	=>	false,'error' => 'query:'.$query.' prevID:'.$prevID);
				
				commitTrans(); // transaction is committed
				return array('success'	=>	true, 'error' => '',  'urlthumb' => $URLTHUMB, 'urlmedium' => $URLMEDIUM, 'urllarge' => $URLLARGE, 'pkuserid' => doubleval($this->userID));				
			 }
			 else
			 {
				rollbackTrans();
				return array('success'	=>	false, 'error' => 'Invalid insertion, please try again!'); 
			 }	
			
			
		}//end of try
		catch(Exception $e)
		{
			rollbackTrans(); // transaction rolls back		
			return array('success'	=>	false, 'error' => 'Invalid Picture Addition Exception!');	
		}
	}
 }
 

 
 //upload content photo
 function uploadPhoto()
 {
	include($_SERVER['DOCUMENT_ROOT']."/Settings.php");			
	require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");	
		
	$allowedExtensions = array("jpg", "jpeg", "png", "gif", "png", "bmp", "wbmp");		
	$sizeLimit = 8*1024*1024; // max file size in bytes, 8 MB, 8388608 bytes
	
	$userID = $_POST["userID"];
	$albumID = $_POST["albumID"];
	$latitude = $_POST['latitude'];
	$longitude = $_POST['longitude'];
	$caption = $_POST['caption'];
	
	$result = array("success"	=>	false,	"error"	=>	"Invalid Parameter", "pkPhotoID" => '');
 
 	if(isset($userID) && isset($albumID) && isset($latitude) && isset($longitude))
	{		
		if ($_FILES["file"]["error"] > 0)
  		{
			$result = array("success"	=>	false,	"error"	=>	"Error: " .$_FILES["file"]["error"], 'pkPhotoID' => "" );
  		}
		else
  		{
			//let's upload file
			$uploader = new UploadFileHandler($allowedExtensions, $sizeLimit,$userID,$albumID,$latitude,$longitude,$caption);
			$my_array = $uploader->uploadFromMobile();
			list($success,$error,$pkPhotoID) = array_values($my_array);
	
			$result = ($success)?array('success'	=>	true, 'error' => '', 'pkPhotoID' => $pkPhotoID):array("success" => $success, "error" => $error, "pkPhotoID" => $pkPhotoID);			
				
		}		
	} 
	
	array_push($results, $result);	
	echo htmlspecialchars(json_encode($results), ENT_NOQUOTES);	
	//echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);	
 
 }
 
 function uploadProfilePhoto(){

	include($_SERVER['DOCUMENT_ROOT']."/Settings.php");			
	require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/UUID.class.php');
	
	$allowedExtensions = array("jpg", "jpeg", "png", "gif", "png", "bmp", "wbmp");		
	$sizeLimit = 8*1024*1024; // max file size in bytes, 8 MB, 8388608 bytes
	$result = array("success"=>false,"error"=>"System error with photo posting, please try again!");
	
	$userID = $_REQUEST['userID'];
	
	//test purposes
	//$result = array("success"=>false,"error"=>"userID:".$userID);

	if(isset($userID))
	{		
		//let's upload file
		$uploader = new UploadFileHandler($allowedExtensions, $sizeLimit,$userID,'','','','');
		$result = $uploader->uploadProfilePhoto();
		//list($success,$error,$urlthumb,$urlmedium,$urllarge, $pkuserid) = array_values($my_array);	
		
		//$result = ($success)?array('success'=>true, 'error' => "",'URLTHUMB' => $urlthumb, 'URLMEDIUM' => $urlmedium, 'URLLARGE' => $urllarge, 'PKUSERID' => $pkuserid):array("success" => $success, "error" => $error);
	
	}
	
	
	echo json_encode($result);
	
	
	
	
	
 }

?>