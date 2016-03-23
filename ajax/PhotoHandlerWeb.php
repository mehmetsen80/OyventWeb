<?php 

include($_SERVER['DOCUMENT_ROOT']."/Settings.php");	
require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Picture.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Album.class.php");

//$_POST['processType'] = utf8_urldecode($_POST['processType']);

/*$post = $_POST['processType'];
$result = array("success"	=>	false,	"error"	=>$post);
echo json_encode($result);
return;

if($_POST['processType'] == "UPLOADIOSPHOTO")
	$result = array("success"	=>	false,	"error"	=>	"System error, please try again!", "pkPhotoID" => '');
else
	$result = array("success"	=>	false,	"error"	=>	"222", "pkPhotoID" => '');
echo json_encode($result);
return;*/

 $processType= ($_GET['processType'])?$_GET['processType']:$_POST['processType'];

 switch($processType)
 {	
 	case "UPLOADPHOTO":	//upload Instagram Photo
		uploadPhoto();
		break;
	case "UPLOADIOSPHOTO": //mobile for ios8 photo
		uploadIOSPhoto();
		break;
	case "UPLOADIOSPHOTOONLYTEXT": //mobile for ios8 text that is converted into photo
		uploadIOSPhotoOnlyText();
		break;
	case "DELETEFEED":
		deletePhoto();
		break;	
	case "UPLOADTEMPPHOTO":
		uploadTempPhoto();
		break;
	case "DELETETEMPPHOTO":
		deleteTempPhoto();
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
 
 class UploadFileHandler { 
 
 	private $allowedExtensions = array();
    private $sizeLimit = 8388608; // 8*1024*1024 8388608 bytes - 8 MB   
	private $userID;
	private $albumID;
	private $latitude;
	private $longitude;
	private $caption; 
	private $subject;
	private $file;
	private $picUUID;
	private $hasphoto;
	
	function __construct(array $allowedExtensions = array(), $sizeLimit = 8388608, $userID,$albumID,$latitude,$longitude, $caption,$subject,$picUUID='-1',$hasphoto='0'){        
        $allowedExtensions = array_map("strtolower", $allowedExtensions);            
        $this->allowedExtensions = $allowedExtensions;        
        $this->sizeLimit = $sizeLimit;
		$this->userID = $userID;
		$this->albumID = $albumID;
		$this->latitude = $latitude;
		$this->longitude = $longitude;
		$this->caption = $caption;	
		$this->subject = $subject;	
		$this->picUUID = $picUUID;
		$this->hasphoto = $hasphoto;
		
	}
	
	function uploadTempPhoto(){
		require_once($_SERVER['DOCUMENT_ROOT']."/class/Picture.class.php");
		include($_SERVER['DOCUMENT_ROOT']."/Settings.php");	
		require_once($_SERVER['DOCUMENT_ROOT']."/db/DbConnection.php");
		require_once($_SERVER['DOCUMENT_ROOT'].'/class/UUID.class.php');		
		include($_SERVER['DOCUMENT_ROOT']."/lib/aws/awsfnc.php");
		require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");
		
		try{
			
			@session_start();
			
			beginTrans(); // transaction begins
			
			if (isset($_FILES['file'])) {
            	$this->file = new UploadFile();
        	} else {
            	$this->file = false; 
        	}
			
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
			
			
			
			//$tempFile = $this->file;
			$bucket = "s3-oy-vent-images-16";
			$foldername = isset($this->userID)?$this->userID:"common";			
			$keyprefix = UUID::generate(UUID::UUID_RANDOM, UUID::FMT_STRING,"96527393");			
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
			
			$query = "DELETE FROM TBLPHOTO WHERE PHOTOUUID='".$this->picUUID."' ";
			$delete = executeQueryForTrans($query);
			
			//we force every picture to be jpeg on saving to database			
			$query  = " INSERT INTO TBLPHOTO 
				(PHOTOUUID, FKUSERID, FKALBUMID, FKSUBJECTID, CAPTION, KEYMAIN, KEYLARGE, KEYMEDIUM, KEYSMALL, 
				KEYTHUMB,URLLARGE, URLMEDIUM, URLSMALL, URLTHUMB, SIZELARGE, SIZEMEDIUM, SIZESMALL ,SIZETHUMB, 
				FOLDER, BUCKET, IMAGETYPE, LATITUDE, LONGITUDE, POSTDATE, FIRSTINIP, ISACTIVE, 
				LAYOUTTYPE, LAYOUTINFO, USERAGENT) 
			 	VALUES('".$this->picUUID."', '".$this->userID."', '".$this->albumID."','".$this->subject."', '".$this->caption."',
				'".$keyprefix."', '".$keylarge."', '".$keymedium."', '".$keysmall."', '".$keythumb."',
				'".$URLLARGE."', '".$URLMEDIUM."', '".$URLSMALL."', '".$URLTHUMB."', '".$piclarge_size."', 
				'".$picmedium_size."', '".$picsmall_size."', '".$picsthumb_size."', '".$foldername."', 
				'".$bucket."', 'JPG','".$this->latitude."', '".$this->longitude."', '".$insertionDate."', '".$_SERVER['REMOTE_ADDR']."','1','".$_SESSION["layoutType"]."','".$_SESSION["layoutInfo"]."','".$_SESSION["userAgent"]."') ";						
				
				//return array('success' => true, 'error' => 'size:'.$size.' filename:'.$filename.' ext:'.$ext.' '.$query);	
																		
			$pkPhotoID = executeInsertQueryForTrans($query);
			commitTrans(); // transaction is committed
			return array('success'	=>	true, 'error' => false, 'urlthumb' => $URLTHUMB);			
			
			
		}//end of try
		catch(Exception $e)
		{
			rollbackTrans(); // transaction rolls back		
			return array('success'	=>	false, 'error' => 'Invalid Picture Addition Exception!'.$e->getMessage(), 'urlthumb' => '');	
		}
		
	}
	
	function uploadFromWeb()
 	{
		require_once($_SERVER['DOCUMENT_ROOT']."/class/Picture.class.php");
		include($_SERVER['DOCUMENT_ROOT']."/Settings.php");	
		require_once($_SERVER['DOCUMENT_ROOT']."/db/DbConnection.php");
		require_once($_SERVER['DOCUMENT_ROOT'].'/class/UUID.class.php');		
		include($_SERVER['DOCUMENT_ROOT']."/lib/aws/awsfnc.php");
		require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");
		require_once($_SERVER['DOCUMENT_ROOT']."/class/Text2Image.class.php");
		
		try{			
			
			@session_start();
			
			if($this->hasphoto == '1')
			{
				beginTrans(); // transaction begins
				/*$query = "UPDATE TBLPHOTO SET FKSUBJECTID='".$this->subject."', 
							CAPTION='".$this->caption."', ISACTIVE='1' 
						  WHERE PHOTOUUID = '".$this->picUUID."' ";*/
				$query = "UPDATE TBLPHOTO SET FKALBUMID='".$this->subject."', 
							CAPTION='".$this->caption."', ISACTIVE='1' 
						  WHERE PHOTOUUID = '".$this->picUUID."' ";
				executeQueryForTrans($query);
				commitTrans();
				return array('success'	=>	true, 'error' => false, 'pkPhotoID' => '');
				
			}
			else{
				beginTrans(); // transaction begins
				$picoriginal = new Text2Image(400,400,$this->caption,"picoriginal");
				$this->file = $picoriginal->createImage();
				$ext = strtolower("JPG");
				
				$captiontext = $this->caption;
				$captiontext = str_replace("\'","'",$captiontext);
				$captiontext = str_replace('\\"','"',$captiontext);
				$captiontext = str_replace("\\n","\n",$captiontext);
				//$captiontext = stripslashes($captiontext);			
				
			
				$bucket = "s3-oy-vent-images-16";
				$foldername = isset($this->userID)?$this->userID:"common";			
				$keyprefix = UUID::generate(UUID::UUID_RANDOM, UUID::FMT_STRING,"96581783");			
				$keyprefix = str_replace("-","",$keyprefix);
				$keyprefix =  strlen($keyprefix)>=16?substr($keyprefix,0,16):$keyprefix;
			 						
			
				//large picture
				$keylarge = $keyprefix.'-large.jpg';
				$newfilelarge = '/tmp/'.$keylarge.'.jpg';
				$piclarge = new Text2Image(720,720,$captiontext,$newfilelarge);	
				$piclarge->createImage();					
				$resultlarge = createObject($foldername, $keylarge, $newfilelarge);
				$URLLARGE = $resultlarge['ObjectURL'];
				$piclarge_size = $piclarge->getFileSize();		      
				$piclarge->destroy_buffer();
				if(file_exists($newfilelarge))
					unlink($newfilelarge);
				
			
				//medium picture
				$keymedium = $keyprefix.'-medium.jpg';		
				$newfilemedium = '/tmp/'.$keymedium.'.jpg';						
				$picmedium = new Text2Image(250,250,$captiontext,$newfilemedium);	
				$picmedium->createImage();					
				$resultmedium = createObject($foldername, $keymedium, $newfilemedium);		
				$URLMEDIUM = $resultmedium['ObjectURL'];
				$picmedium_size = $picmedium->getFileSize();
				$picmedium->destroy_buffer();			
				if(file_exists($newfilemedium))
					unlink($newfilemedium);
				
				//small picture
				$keysmall = $keyprefix.'-small.jpg';
				$newfilesmall = '/tmp/'.$keysmall.'.jpg';	
				$picsmall = new Text2Image(150,150,$captiontext,$newfilesmall);	
				$picsmall->createImage();					
				$resultsmall = createObject($foldername, $keysmall, $newfilesmall);
				$URLSMALL = $resultsmall['ObjectURL'];
				$picsmall_size = $picsmall->getFileSize();
				$picsmall->destroy_buffer();
				if(file_exists($newfilesmall))
					unlink($newfilesmall);
			
			
				//thumb picture
				$keythumb = $keyprefix.'-thumb.jpg';
				$newfilethumb = '/tmp/'.$keythumb.'.jpg';
				$picsthumb = new Text2Image(50,50,$captiontext,$newfilethumb);	
				$picsthumb->createImage();			
				$resultthumb = createObject($foldername, $keythumb, $newfilethumb);
				$URLTHUMB = $resultthumb['ObjectURL'];
				$picsthumb_size = $picsthumb->getFileSize();
				$picsthumb->destroy_buffer();
				if(file_exists($newfilethumb))
					unlink($newfilethumb);
			
				date_default_timezone_set('America/Chicago');
				$insertionDate = date("Y-m-d H:i:s");
			
				/*$picUUID = "-1";
				$picUUID = UUID::generate(UUID::UUID_RANDOM, UUID::FMT_STRING,"02337979");
				$picUUID = str_replace("-","",$picUUID);
				$picUUID = strlen($picUUID)>=16?substr($picUUID,0,16):$picUUID;	*/
				
				//we force every picture to be jpeg on saving to database			
				$query  = " INSERT INTO TBLPHOTO 
				(PHOTOUUID, FKUSERID, FKALBUMID, FKSUBJECTID, CAPTION, KEYMAIN, KEYLARGE, KEYMEDIUM, KEYSMALL, 
				KEYTHUMB,URLLARGE, URLMEDIUM, URLSMALL, URLTHUMB, SIZELARGE, SIZEMEDIUM, SIZESMALL ,SIZETHUMB, 
				FOLDER, BUCKET, IMAGETYPE, LATITUDE, LONGITUDE, POSTDATE, FIRSTINIP,LAYOUTTYPE, LAYOUTINFO, USERAGENT) 
			 	VALUES('".$this->picUUID."', '".$this->userID."', '".$this->subject."','".$this->subject."', '".$this->caption."',
				'".$keyprefix."', '".$keylarge."', '".$keymedium."', '".$keysmall."', '".$keythumb."',
				'".$URLLARGE."', '".$URLMEDIUM."', '".$URLSMALL."', '".$URLTHUMB."', '".$piclarge_size."', 
				'".$picmedium_size."', '".$picsmall_size."', '".$picsthumb_size."', '".$foldername."', 
				'".$bucket."', 'JPG','".$this->latitude."', '".$this->longitude."', '".$insertionDate."', '".$_SERVER['REMOTE_ADDR']."','".$_SESSION["layoutType"]."','".$_SESSION["layoutInfo"]."','".$_SESSION["userAgent"]."')";						
																		
				$pkPhotoID = executeInsertQueryForTrans($query);
				commitTrans(); // transaction is committed
				return array('success'	=>	true, 'error' => false, 'pkPhotoID' => $pkPhotoID);				
			 	
				$picoriginal->destroy_buffer();
			}
			
			
		}//end of try
		catch(Exception $e)
		{
			rollbackTrans(); // transaction rolls back		
			return array('success'	=>	false, 'error' => 'Invalid Picture Addition Exception!'.$e->getMessage(), 'pkPhotoID' => '');	
		}
 	}
  
 }
 
 function uploadPhoto(){
	 
	include($_SERVER['DOCUMENT_ROOT']."/Settings.php");			
	require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");
	
	$allowedExtensions = array("jpg", "jpeg", "png", "gif", "png", "bmp", "wbmp");		
	$sizeLimit = 8*1024*1024; // max file size in bytes, 8 MB, 8388608 bytes
	
	$userID = $_POST["userID"];
	$albumID = $_POST["albumID"];
	$latitude = $_POST['latitude'];
	$longitude = $_POST['longitude'];
	$caption = $_POST['caption'];	
	$caption = utf8_urldecode($caption);	
	$caption = real_escape_string($caption);
	$subject = $_POST['subject'];
	$picUUID = $_POST["picUUID"];
	$hasphoto = $_POST['hasphoto'];
	
	$result = array("success"	=>	false,	"error"	=>	"System error, please try again!", "pkPhotoID" => '');
 
 	if(isset($userID) && isset($albumID) && isset($latitude) && isset($longitude))
	{		
		$uploader = new UploadFileHandler($allowedExtensions, $sizeLimit,$userID,$albumID,$latitude,$longitude,$caption,$subject,$picUUID,$hasphoto);
		$my_array = $uploader->uploadFromWeb();
		list($success,$error,$pkPhotoID) = array_values($my_array);
		$result = ($success)?array('success'	=>	true, 'error' => '', 'pkPhotoID' => $pkPhotoID):array("success" => $success, "error" => $error, "pkPhotoID" => $pkPhotoID);
				
	} 
	
	echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
 }
 
 function uploadIOSPhoto(){//posting photo
 	include($_SERVER['DOCUMENT_ROOT']."/Settings.php");			
	require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/UUID.class.php');
	
	$allowedExtensions = array("jpg", "jpeg", "png", "gif", "png", "bmp", "wbmp");		
	$sizeLimit = 8*1024*1024; // max file size in bytes, 8 MB, 8388608 bytes
	
	$result = array("success"=>false,"error"=>"Himmm!","urlthumb"=>'');
	
	$userID = $_REQUEST['userID'];
	$albumID = $_REQUEST['albumID'];
	$latitude = $_REQUEST['latitude'];
	$longitude = $_REQUEST['longitude'];
	$caption = $_REQUEST['caption'];
	$caption = utf8_urldecode($caption);	
	$caption = real_escape_string($caption);
	$subject = '0';
	$hasphoto = '0';
	
	$picUUID = UUID::generate(UUID::UUID_RANDOM, UUID::FMT_STRING,"09947184");
	$picUUID = str_replace("-","",$picUUID);
	$picUUID = strlen($picUUID)>=16?substr($picUUID,0,16):$picUUID;
	
 	
	$result = array("success"=>false,"error"=>"System error with photo posting, please try again!","urlthumb"=>'');
	
	///$result = array("success"=>false,"error"=>"userID:".$userID." albumID:".$albumID." latitude:".$latitude." longitude:".$longitude);
	
	if(isset($userID) && isset($albumID) && isset($latitude) && isset($longitude))
	{		
		$uploader = new UploadFileHandler($allowedExtensions, $sizeLimit,$userID,$albumID,$latitude,$longitude,$caption,$subject,$picUUID, $hasphoto);
		$my_array = $uploader->uploadTempPhoto();//post photo
		list($success,$error,$urlthumb) = array_values($my_array);		
		$result = ($success)?array('success'=>true, 'error' => '','urlthumb' => $urlthumb):array("success" => $success, "error" => $error, "urlthumb" => $urlthumb);	
				
	}
	
	echo json_encode($result);
 }
 
 function uploadIOSPhotoOnlyText(){
 	
	include($_SERVER['DOCUMENT_ROOT']."/Settings.php");			
	require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/UUID.class.php');
	
	$allowedExtensions = array("jpg", "jpeg", "png", "gif", "png", "bmp", "wbmp");		
	$sizeLimit = 8*1024*1024; // max file size in bytes, 8 MB, 8388608 bytes
	
	$userID = $_POST["userID"];
	$albumID = $_POST["albumID"];
	$latitude = $_POST['latitude'];
	$longitude = $_POST['longitude'];
	$caption = $_POST['caption'];	
	$caption = utf8_urldecode($caption);	
	$caption = real_escape_string($caption);
	$subject = '0';
	$hasphoto = '0';
	
	$picUUID = UUID::generate(UUID::UUID_RANDOM, UUID::FMT_STRING,"02347184");
	$picUUID = str_replace("-","",$picUUID);
	$picUUID = strlen($picUUID)>=16?substr($picUUID,0,16):$picUUID;
	
	
	$result = array("success"	=>	false,	"error"	=>	"System error, please try again!", "pkPhotoID" => '');
 
 	if(isset($userID) && isset($albumID) && isset($latitude) && isset($longitude))
	{		
		$uploader = new UploadFileHandler($allowedExtensions, $sizeLimit,$userID,$albumID,$latitude,$longitude,$caption,$subject,$picUUID,$hasphoto);
		$my_array = $uploader->uploadFromWeb();//this is actually only text photo
		list($success,$error,$pkPhotoID) = array_values($my_array);
		$result = ($success)?array('success'	=>	true, 'error' => '', 'pkPhotoID' => $pkPhotoID):array("success" => $success, "error" => $error, "pkPhotoID" => $pkPhotoID);
				
	}
	
	echo json_encode($result);
 }
 
 function deletePhoto(){
	 
	 
 }
 
 function deleteTempPhoto()
 {	 
	$picUUID = $_POST["picUUID"];
	$userID = $_POST["userID"];
	
	$album = new Album($userID);
	$result =  $album->deletePhotoByUUID($pkPhotoID)?array('success'=>true, "message" => "Photo deleted successfully!"):array('success' => false, "message","Photo could not be deleted, please try again!");
	
	echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
	
 }

 
 //adding a file directly but in passive mode
 function uploadTempPhoto(){
	 
	include($_SERVER['DOCUMENT_ROOT']."/Settings.php");			
	require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");
	
	$allowedExtensions = array("jpg", "jpeg", "png", "gif", "png", "bmp", "wbmp");		
	$sizeLimit = 8*1024*1024; // max file size in bytes, 8 MB, 8388608 bytes
	
	$userID = ($_POST['userID'])?$_POST['userID']:$_GET['userID'];
	$albumID = ($_POST['albumID'])?$_POST['albumID']:$_GET['albumID'];
	$latitude = ($_POST['latitude'])?$_POST['latitude']:$_GET['latitude'];
	$longitude = ($_POST['longitude'])?$_POST['longitude']:$_GET['longitude'];
	$picUUID = $_GET["picUUID"];
 	
	$result = array("success"=>false,"error"=>"System error with file uploading, please try again!","urlthumb"=>'');
	
	//$result = array("success"=>false,"error"=>"userID:".$userID." albumID:".$albumID." latitude:".$latitude." longitude:".$longitude);
	
	if(isset($userID) && isset($albumID) && isset($latitude) && isset($longitude))
	{		
		$uploader = new UploadFileHandler($allowedExtensions, $sizeLimit,$userID,$albumID,$latitude,$longitude,'','0',$picUUID,'1');
		$my_array = $uploader->uploadTempPhoto();
		list($success,$error,$urlthumb) = array_values($my_array);		
		$result = ($success)?array('success'=>true, 'error' => $error,'urlthumb' => $urlthumb):array("success" => $success, "error" => $error, "urlthumb" => $urlthumb);	
				
	}
	
	echo json_encode($result);
	
 }

?>