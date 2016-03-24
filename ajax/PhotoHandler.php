<?php 

/*if (!ini_get('display_errors')) {
    ini_set('display_errors', E_ALL);
}*/

include($_SERVER['DOCUMENT_ROOT']."/Settings.php");	
require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Picture.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Album.class.php");


 //$processType = utf8_urldecode(

 $processType= ($_GET['processType'])?$_GET['processType']:$_POST['processType'];

 switch($processType)
 {
 	case "UPLOADPHOTO":	//upload Instagram Photo
		uploadPhoto();
		break;
	case "DELETEPHOTO":
		deletePhoto();
		break;
	case "DELETEPHOTOS":
		deletePhotos();
		break;	
 }
 
 function uploadPhoto()
 {

	include($_SERVER['DOCUMENT_ROOT']."/Settings.php");			
	require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");
	
		
	$allowedExtensions = array("jpg", "jpeg", "png", "gif", "png", "bmp", "wbmp");		
	$sizeLimit = 8*1024*1024; // max file size in bytes, 8 MB
	
	$userID = $_GET["userID"];
	$albumID = $_GET["albumID"];
	
	$result = array("success"	=>	false,	"error"	=>	"System error, please try again!", "pkPhotoID" => '');
 
 	if(isset($userID) && isset($albumID))
	{		
		$caption = $_GET['caption'];
		$caption = utf8_urldecode($caption);
		//$caption = addslashes($caption);
		$caption = real_escape_string($caption);
		
		$instagramid = $_GET['instagramid'];
		$instagramid = utf8_urldecode($instagramid);		
		
		$largeurl = $_GET['largeurl'];
		$largeurl = utf8_urldecode($largeurl);		
		
		$mediumurl = $_GET['mediumurl'];
		$mediumurl = utf8_urldecode($mediumurl);
		
		$smallurl = $_GET['smallurl'];
		$smallurl = utf8_urldecode($smallurl);
		
		$thumburl = $_GET['thumburl'];
		$thumburl = utf8_urldecode($thumburl);
		
		$ownedby = $_GET['ownedby'];
		$ownedby = utf8_urldecode($ownedby);		
		
		$likes = $_GET['likes'];
		$likes =  utf8_urldecode($likes);
		
		$tags = $_GET['tags'];
		$tags =  utf8_urldecode($tags);
		
		$contentlink = $_GET['contentlink'];
		$contentlink =  utf8_urldecode($contentlink);
		
		$contenttype = $_GET['contenttype'];
		$contenttype =  utf8_urldecode($contenttype);
		
		$latitude = $_GET['latitude'];
		$latitude =  utf8_urldecode($latitude);
		
		$longitude = $_GET['longitude'];
		$longitude =  utf8_urldecode($longitude);
		
		$createdtime = $_GET['createdtime'];
		$createdtime =  utf8_urldecode($createdtime);
		
		//let's upload file
		$uploader = new UploadFileHandler($allowedExtensions, $sizeLimit,$userID,$albumID,$ownedby,$instagramid, $caption, $largeurl,$mediumurl,$smallurl,$thumburl,$likes,$tags,$contentlink,$contenttype,$latitude,$longitude,$createdtime);
		
			
		$result = array('success'	=>	false, "error" => "bla bla", 'pkPhotoID' => "" );
		
		$my_array = $uploader->uploadFromInstagram();
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
	public $instagramid;
	public $caption;
	public $likes;
	public $tags;
	public $createdtime;
	public $longitude;
	public $latitude;
	public $contentlink;
	public $contenttype;
	public $fkInstagramID = NULL;
	
	function __construct(array $allowedExtensions = array(), $sizeLimit = 6291456, $userID,$albumID,$ownedby,$instagramid,$caption,$largeurl,$mediumurl=NULL,$smallurl=NULL,$thumburl=NULL,$likes,$tags,$contentlink,$contenttype,$latitude,$longitude,$createdtime){        
        $allowedExtensions = array_map("strtolower", $allowedExtensions);
            
        $this->allowedExtensions = $allowedExtensions;        
        $this->sizeLimit = $sizeLimit;
		$this->userID = $userID;
		$this->albumID = $albumID;		
		$this->ownedby = $ownedby;
		$this->instagramid = $instagramid;
		$this->caption = $caption;
		$this->largeurl = (isset($largeurl) && !empty($largeurl))?$largeurl:false;
		$this->mediumurl = $mediumurl;
		$this->smallurl = $smallurl;	
		$this->thumburl = $thumburl;
		$this->likes = (isset($likes) && is_numeric($likes) && !empty($likes))?$likes:0;
		$this->tags = $tags;
		$this->contentlink = $contentlink;
		$this->contenttype = $contenttype;
		$this->latitude =  isset($latitude) && is_numeric($latitude) && !empty($latitude)?$latitude:0;
		$this->longitude = isset($longitude ) && is_numeric($longitude) && !empty($longitude)?$longitude:0;
		$this->createdtime = $createdtime;	
    }
	
		
 	function uploadFromInstagram()
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
        
        	if (!$this->largeurl)
            	return array('success'	=>	false, 'error' => 'No files were uploaded!', 'pkPhotoID' => '');			
			
			$pathinfo = pathinfo($this->largeurl);
        	$filename = $pathinfo['filename'];       
        	$ext = $pathinfo['extension'];
			$ext = strtolower($ext);
			
//			if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
//            	$these = implode(', ', $this->allowedExtensions);
//            	return array('success'	=>	false, 'error' => 'File has an invalid extension:'.$ext.' It should be one of: '.$these.'.');
//			}
					
		
			$bucket = "s3-oy-vent-images-16";
			//$foldername = $this->userID;
			$foldername = isset($this->userID)?$this->userID:"common";			
			$keyprefix = UUID::generate(UUID::UUID_RANDOM, UUID::FMT_STRING,"92987943");			
			$keyprefix = str_replace("-","",$keyprefix);
			$keyprefix =  strlen($keyprefix)>=16?substr($keyprefix,0,16):$keyprefix;			
			
			//large picture
			$keylarge = $keyprefix.'-large.jpg';
			$piclarge = new Picture();

			$piclarge->load($this->largeurl,strtoupper($ext));		
			$width = $piclarge->getWidth() > 720?720:$piclarge->getWidth();					
			$piclarge->resizeToWidth($width);

			$newfilelarge = '/tmp/'.$keylarge.'.jpg';				
			$piclarge->save($newfilelarge,75,0777);
			//return array('success'	=>	false, 'error' => 'largeurl2: '.$this->largeurl);
			$resultlarge = createObject($foldername, $keylarge, $newfilelarge);
			$URLLARGE = $resultlarge['ObjectURL'];
			$piclarge_size = $piclarge->getFileSize();			      
			$piclarge->destroy_buffer();
			if(file_exists($newfilelarge))
				unlink($newfilelarge);

			//return array('success'	=>	false, 'error' => 'newfilelarge: '.$newfilelarge);
		
			//medium picture
			$keymedium = $keyprefix.'-medium.jpg';
			$picmedium = new Picture();						
			$picmedium->load($this->largeurl, strtoupper($ext));					
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
			$picsmall->load($this->largeurl, strtoupper($ext));			
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
			$picsthumb->load($this->largeurl, strtoupper($ext));			
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
				
				
			//first lookup Instagram table if previously inserted of the same Instagram photo id
			if(isset($this->instagramid) && !empty($this->instagramid) 
				&& isset($this->ownedby) && !empty($this->ownedby)){
				
				$InstIds = explode('_',$this->instagramid);
				
				$query = "";
				if(sizeof($InstIds) > 1)
				{										
					$query = "SELECT PKINSTAGRAMID FROM TBLINSTAGRAM WHERE  INSTAGRAMIDP1 = '".$InstIds[0]."' 
					AND INSTAGRAMIDP2 = '".$InstIds[1]."' ";
				}
				else 
				{
					$query = "SELECT PKINSTAGRAMID FROM TBLINSTAGRAM WHERE INSTAGRAMIDP1 = '".$this->instagramid."' ";
				}				
				
				$result = executeQueryForTrans($query);				
  
 	 			if(mysql_num_rows($result)>0)
 	 			{
					$row = mysql_fetch_row($result);
					$this->fkInstagramID =  $row[0];					
				}
				else
				{

					if(sizeof($InstIds) > 1)
					{					
						$query = " INSERT INTO TBLINSTAGRAM 
						(INSTAGRAMIDP1,INSTAGRAMIDP2,URLLARGE,URLMEDIUM,URLSMALL,CAPTION,TAGS,CONTENTLINK,
						CONTENTTYPE,CREATEDDATE,LATITUDE,LONGITUDE,LIKES,POSTDATE,OWNEDBY) 
					 	VALUES('".$InstIds[0]."','".$InstIds[1]."','".$this->largeurl."','".$this->mediumurl."',
					 	'".$this->smallurl."','".$this->caption."','".$this->tags."', '".$this->contentlink."',
						'".$this->contenttype."','".$this->createdtime."','".$this->latitude."','".$this->longitude."',
						'".$this->likes."', '".$insertionDate."', '".$this->ownedby."') ";
					}
					else
					{
						$query = " INSERT INTO TBLINSTAGRAM 
						(INSTAGRAMIDP1,URLLARGE,URLMEDIUM,URLSMALL,CAPTION,TAGS,CONTENTLINK,
						CONTENTTYPE,CREATEDDATE,LATITUDE,LONGITUDE,LIKES,POSTDATE,OWNEDBY) 
					 	VALUES('".$InstIds[0]."','".$this->largeurl."','".$this->mediumurl."',
					 	'".$this->smallurl."','".$this->caption."','".$this->tags."', '".$this->contentlink."',
						'".$this->contenttype."','".$this->createdtime."','".$this->latitude."','".$this->longitude."',
						'".$this->likes."', '".$insertionDate."', '".$this->ownedby."') ";
					}
					
					$this->fkInstagramID = executeInsertQueryForTrans($query);
				}
			}			
			
			//return array('success'	=>	false, 'error' => 'query:'.$query, 'pkPhotoID' =>'' );			
			$picUUID = "-1";
			$picUUID = UUID::generate(UUID::UUID_RANDOM, UUID::FMT_STRING,"02181965");
			$picUUID = str_replace("-","",$picUUID);
			$picUUID = strlen($picUUID)>=16?substr($picUUID,0,16):$picUUID;						
			
			$query = "SELECT PKPHOTOID FROM TBLPHOTO WHERE FKALBUMID='".$this->albumID."'
			 AND FKINSTAGRAMID='".$this->fkInstagramID."' ";
			 
			 $result = executeQueryForTrans($query);
			 if(mysql_num_rows($result)>0)
 	 		 {
				 $row = mysql_fetch_row($result);
				 $pkPhotoID = $row[0];
				 return array('success'	=>	false, 'error' => 'Photo already exists on that album!', 'pkPhotoID' => $pkPhotoID); 
			 }
			 else {			
			 
			 	$geo =  unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=".$_SERVER['REMOTE_ADDR']));
				$geolatitude = $geo["geoplugin_latitude"];
				$random = ((rand()*(0.0002/getrandmax()))-0.0001);
				$geolongitude = $geo["geoplugin_longitude"] + $random;
			 
				//we force every picture to be jpeg on saving to database			
				$query  = " INSERT INTO TBLPHOTO 
				(PHOTOUUID,FKUSERID,FKALBUMID,FKINSTAGRAMID,KEYMAIN, KEYLARGE, KEYMEDIUM, KEYSMALL, 
				KEYTHUMB,URLLARGE, URLMEDIUM, URLSMALL, URLTHUMB, SIZELARGE, SIZEMEDIUM, SIZESMALL ,SIZETHUMB, 
				FOLDER, BUCKET, IMAGETYPE,LATITUDE,LONGITUDE,CONTENTLINK,OWNEDBY,CREATEDDATE,POSTDATE,
				FIRSTINIP,LAYOUTTYPE, LAYOUTINFO, USERAGENT) 
			 	VALUES('".$picUUID."','".$this->userID."','".$this->albumID."','".$this->fkInstagramID."',
				'".$keyprefix."','".$keylarge."','".$keymedium."','".$keysmall."','".$keythumb."',
				'".$URLLARGE."','".$URLMEDIUM."','".$URLSMALL."','".$URLTHUMB."', '".$piclarge_size."', 
				'".$picmedium_size."','".$picsmall_size."','".$picsthumb_size."', '".$foldername."', 
				'".$bucket."', 'JPG', '".$geolatitude."', '".$geolongitude."', '".$this->contentlink."',
				'".$this->ownedby."', '".$this->createdtime."','".$insertionDate."','".$_SERVER['REMOTE_ADDR']."','".$_SESSION["layoutType"]."','".$_SESSION["layoutInfo"]."','".$_SESSION["userAgent"]."')";				
																		
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
 
 function deletePhotos(){
 	$userID = $_GET["userID"];
	$photos_array = $_GET['photos'];	
	$photos_array =  explode(",",$photos_array);
	
	//$result = array('success'=>true, "message" =>$photos_array);
	//echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
	//return;
	$album = new Album($userID);
	$result =  $album->deletePhotos($photos_array)?array('success'=>true, "message" => "Photos deleted successfully!"):array('success' => false, "message","Photos could not be deleted, please try again!");
	
	echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
 }
 

?>