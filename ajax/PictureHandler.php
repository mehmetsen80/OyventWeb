<?php 

include($_SERVER['DOCUMENT_ROOT']."/Settings.php");	
require_once($_SERVER['DOCUMENT_ROOT']."/class/Picture.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/Feed.class.php");

 $processType= ($_GET['processType'])?$_GET['processType']:$_POST['processType'];

 switch($processType)
 {
 	case "UPLOADDATA":
		
		//$file = $_POST["file"];
		//echo "file: ".$file;
		uploadData();
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
    private $sizeLimit = 6291456; // 6*1024*1024 6291456 bytes - 6 MB
    private $file;
	public $httpPath;
	public $physicalPath;	
	private $userID;
	private $username;
	
	function __construct(array $allowedExtensions = array(), $sizeLimit = 6291456, $userID,$username){        
        $allowedExtensions = array_map("strtolower", $allowedExtensions);
            
        $this->allowedExtensions = $allowedExtensions;        
        $this->sizeLimit = $sizeLimit;
		$this->userID = $userID;
		$this->username = $username;
		
		if (isset($_FILES['file'])) {
            	$this->file = new UploadFile();
        	} else {
            	$this->file = false; 
        	}
    }
	
	public function getFile()
	{
		return $this->file;
	}
	
	
 function uploadPictureDirectly($uploadDirectory,$feedId)
 {
	require_once($_SERVER['DOCUMENT_ROOT']."/class/Picture.class.php");
	include($_SERVER['DOCUMENT_ROOT']."/Settings.php");	
	require_once($_SERVER['DOCUMENT_ROOT']."/db/DbConnection.php");
	require_once($_SERVER['DOCUMENT_ROOT'].'/class/UUID.class.php');

	try{		
		beginTrans(); // transaction begins		
		
		
		//if directory not exists, then create one
		if(!is_dir ($uploadDirectory)) 		
			mkdir($uploadDirectory);		
		
		if (!is_writable($uploadDirectory)){
            @chmod($uploadDirectory,777);
			//return array('success'	=>	false, 'error' => "Server error. Upload directory isn't writable.");
        }
        
        if (!$this->file){
            return array('success'	=>	false, 'error' => 'No files were uploaded!'); 
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
			
            return array('success'	=>	false, 'error' => 'File has an invalid extension, it should be one of: '.$these.'.');         }		
		
		$tempFile =  $this->file->getFile();
		
		//$folderPath = $imageSubFolder.'/oyvent/'.$this->userID.'_'.$this->username;			
		//$picnewname =  $this->userID.'_'.time();
		$folderPath = $imageSubFolder.'/oyvent/'.$this->username;			
		$picnewname =  time();
		$newfile = $uploadDirectory.$picnewname.'.jpg';	
		$newfilethumb =  $uploadDirectory.$picnewname.'_Thumb.jpg';
		$newfilethumb2 =  $uploadDirectory.$picnewname.'_Thumb2.jpg';	
		
		$this->physicalPath = $newfile;
		$this->httpPath = $this->httpPath.$picnewname.'_Thumb2.'.strtolower($ext);
		
		
		$pic = new Picture();
		$pic->load($tempFile,strtoupper($ext));		
		$width = $pic->getWidth();		
		if($width > 720)
			$width = 720;		
		$pic->resizeToWidth($width);					
		$pic->save($newfile,80,0777);
		$pic_size = $pic->getFileSize();
		$pic->destroy_buffer();
		
		
		$picthumb = new Picture();				
		$picthumb->load($tempFile, strtoupper($ext));
		$width = $picthumb->getWidth();
		if($width > 360)
			$width = 360;
		
		$picthumb->resizeToWidth($width);
		$picthumb->save($newfilethumb,80,0777);
		$picthumb_size = $picthumb->getFileSize();
		$picthumb->destroy_buffer();
		
		
		$picthumb2 = new Picture();				
		$picthumb2->load($tempFile, strtoupper($ext));		
		/*$width = $picthumb2->getWidth();
		if($width > 150)
			$width = 150;*/
		$picthumb2->resize(150,150); //let's accept all the thumbnails as 150x150
		//$picthumb2->resizeToWidth($width);		
		$picthumb2->save($newfilethumb2,80,0777);
		$picthumb2_size = $picthumb2->getFileSize();
		$picthumb2->destroy_buffer();
				
		//date_default_timezone_set('America/Chicago');
		$insertionDate = date("Y-m-d H:i:s");							
		
		$picId = "-1";
		$picId = UUID::generate(UUID::UUID_RANDOM, UUID::FMT_STRING,"02181965");
		$picId = str_replace("-","",$picId);
		$ext = "jpg"; //we force every picture to be jpeg on saving to database				
							
		$query  = " INSERT INTO TBLUPLOADEDPICTURES ";
		$query .= " (PKPICTUREID,NAME,NAMETHUMB,NAMETHUMB2, TYPE, SIZE, SIZETHUMB, SIZETHUMB2, PHYSICALPATH, PHYSICALPATHTHUMB, PHYSICALPATHTHUMB2, FKUSRID,FKFEEDID,ISPROFILE,INSERTIONDATE) ";
		$query .= " VALUES('".$picId."','".$picnewname.".".strtolower($ext)."','".$picnewname."_Thumb.".strtolower($ext)."','".$picnewname."_Thumb2.".strtolower($ext)."','".strtoupper($ext)."','".$pic_size."','".$picthumb_size."','".$picthumb2_size."','".$folderPath."','".$folderPath."','".$folderPath."','".$this->userID."','".$feedId."','0','".$insertionDate."')";
																		
		executeInsertQueryForTrans($query);		
						
		if($picId != "-1")											
		{
			commitTrans(); // transaction is committed
			return array('success'	=>	true, 'error' => false);
		}
		else
		{
			rollbackTrans();
			return array('success'	=>	false, 'error' => 'Invalid Picture Execution!'); 
		}
		
		
		
	}//end of try
	catch(Exception $e)
	{
		rollbackTrans(); // transaction rolls back		
		return array('success'	=>	false, 'error' => 'Invalid Picture Addition Exception!');	
	}
 } //end of uploadPictureDirectly function
	
} //end of class UploadFileHandler
 
 
 
 function uploadData()
 {

	include($_SERVER['DOCUMENT_ROOT']."/Settings.php");			
	
		
	$allowedExtensions = array("jpg", "jpeg", "png", "gif", "png", "bmp", "wbmp");		
	$sizeLimit = 6*1024*1024; // max file size in bytes, 6 MB
	
	$username = $_POST["username"];
	$userID = $_POST["userID"];
	
	$results = array();
	$result = array("success"	=>	false,	"error"	=>	"System error, please try again!");
	
	if(isset($username) && isset($userID))
	{		
		$content = $_POST['content'];
		$content = utf8_urldecode($content);
		$content = addslashes($content);	
			
		$tag1 = $_POST['tag1'];	
		if(isset($tag1) && !empty($tag1))
		{
			$tag1 = utf8_urldecode($tag1);
			$tag1 = addslashes($tag1);
		}
		
		$tag2 = $_POST['tag2'];	
		if(isset($tag2) && !empty($tag2))
		{
			$tag2 = utf8_urldecode($tag2);
			$tag2 = addslashes($tag2);
		}
		
		$tag3 = $_POST['tag3'];	
		if(isset($tag3) && !empty($tag3))
		{
			$tag3 = utf8_urldecode($tag3);
			$tag3 = addslashes($tag3);
		}
		
		$tag4 = $_POST['tag4'];	
		if(isset($tag4) && !empty($tag4))
		{
			$tag4 = utf8_urldecode($tag4);
			$tag4 = addslashes($tag4);
		}
		
		$privacy = $_POST['privacy'];
		$isParent = $_POST['isParent'];
		$parentFeedId = $_POST['parentFeedId'];		
		
		
		//let's first create Feed class and insert feed ang related tags
		$feed = new Feed($username,$userID);	
		$my_array = $feed->insertFeed($content,$tag1,$tag2,$tag3,$tag4,$privacy,$isParent,$parentFeedId);
		list($success,$error) = array_values($my_array);
		
		//if feed inserted successfully
		if($success)
		{
			if($_POST['hasFile'] == "NO")
			{
				$result = array("success"	=>	true, "error" => "");
			}
			else
			{						
				//$targetFilePath = '/'.$imageServer.'/'.$imageSubFolder.'/oyvent/'.$userID.'_'.$username.'/';
				//$httpFilePath = $sitepath.'/'.$imageSubFolder.'/oyvent/'.$userID.'_'.$username.'/';			
				
				$targetFilePath = '/'.$imageServer.'/'.$imageSubFolder.'/oyvent/'.$username.'/';
				$httpFilePath = $sitepath.'/'.$imageSubFolder.'/oyvent/'.$username.'/';			
		
		
				if ($_FILES["file"]["error"] > 0)
  				{  			
					$result = array("success"	=>	false,	"error"	=>	"Hata: " . $_FILES["file"]["error"]);
  				}
				else
  				{
					//let's upload file
					$uploader = new UploadFileHandler($allowedExtensions, $sizeLimit,$userID,$username);
					$uploader->httpPath = $httpFilePath;		
		
					$my_array = $uploader->uploadPictureDirectly($targetFilePath,$feed->feedID);
					list($success,$error) = array_values($my_array);		
	
					if($success)
					{
						$filename = $uploader->getFile()->getName();
						$size = 0;
						if(file_exists($uploader->physicalPath))
						{
							$size = filesize($uploader->physicalPath);
							$size = $size / 1024; // size in KB					
							$size = round($size,2);
						}
			
						$ext = $uploader->getFile()->getExt();	
						$ext = "jpg";
					
						$result = array("success" => true, 
							"error" => "", 
							"username" => $username,
							"userID" => $userID,
							"filesize" => $size, // KB,
							"filename" => $filename, 
							"fileserverpath" => $targetFilePath,
							"filehttppath" => $httpFilePath);
					}
					else
					{
						$result = array("success" => $success, "error" => "Picture Upload Error:".$error);
					}							
  				}
			}
		}
		else
		{
			$result = array("success"	=>	false,	"error"	=>	"Content could not be added=  Error:".$error); 			
		}
	}
	else
	{
		$result = array("success"	=>	false,	"error"	=>	"Invalid username!");
	}
	
	array_push($results, $result);	
	echo htmlspecialchars(json_encode($results), ENT_NOQUOTES);		
	
 }

?>