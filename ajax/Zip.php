<?php 



$processType= ($_GET['processType'])?$_GET['processType']:$_POST['processType'];

 switch($processType)
 {
 	case "DOWNLOADPHOTOS":		
		downloadPhotos();
		break;
 }
 
 function downloadPhotos(){
	 
	include($_SERVER['DOCUMENT_ROOT']."/Settings.php");	
	require_once($_SERVER['DOCUMENT_ROOT']."/class/Zip.class.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/db/DbConnection.php");
	include($_SERVER['DOCUMENT_ROOT']."/Tools.php");
	 
	$userID = $_GET["userID"];
	$userID = utf8_urldecode($userID);
	
	if(!isset($userID) || $userID == "")
		$userID = "12";
	
	$albumName = $_GET["albumName"];
	$albumName = stripslashes($albumName);
	$albumName = utf8_urldecode($albumName);
	
	$photos_array = $_GET['photos_array'];	
	$photos_array = utf8_urldecode($photos_array);
	$photos_array =  explode(",",$photos_array);
	$files = array();
	
	//$result= array("success"	=>	false,	"error"	=>	"", "path" => "test");	
	//echo json_encode($result); 
	//return;
	
	foreach($photos_array as $key => $value)
	{
		$query = "SELECT URLLARGE FROM TBLPHOTO WHERE PKPHOTOID='".$value."' ";
		$result = executeQuery($query);	
			
		if(mysql_num_rows($result) > 0)
		{
			while($row = mysql_fetch_array($result)){			
				$files[] = $row["URLLARGE"];
			}
		}			
	}
	
	$zipfilename = $albumName.'.zip';
	
	# create new zip opbject
	$zip = new ZipArchive();

	# create a temp file & open it
	//$tmp_file = tempnam('.','');
	
	$filepath = $_SERVER['DOCUMENT_ROOT'].'/tmp/'.$userID;
	if(!is_dir ($filepath)) 		
		mkdir($filepath);		
		
	if (!is_writable($filepath))
    	@chmod($filepath,777);	
	
	//$tmp_file = tempnam($_SERVER['DOCUMENT_ROOT'].'/tmp/','');
	
	$tmp_file = $filepath.'/'.$zipfilename;
	
	$zip->open($tmp_file, ZipArchive::OVERWRITE);
	
	# loop through each file
	foreach($files as $file){
		# get filename and extension
		$pathinfo = pathinfo($file);
    	$filename = $pathinfo['filename'];       
    	$ext = $pathinfo['extension'];
		$ext = strtolower($ext);

    	# download file
    	$download_file = file_get_contents($file);

    	#add it to the zip
    	//$zip->addFromString(basename($file),$download_file);
		$zip->addFromString($userID.'/'.$filename.'.'.$ext,$download_file);
	}

	# close zip
	$zip->close();
	//$result = array("success"	=>	true,	"error"	=>	"", "path" => "test2"); 

	# send the file to the browser as a download
	/*header('Content-disposition: attachment; filename={$zipfilename}');
	header('Content-type: application/zip');
	readfile($tmp_file);*/
	$dlink = 'http://'.$_SERVER["SERVER_NAME"].'/ajax/download.php?userID='.$userID.'&file='.$zipfilename;
	
	$result = array("success"	=>	true,	"error"	=>	"", "path" => $dlink); 
	
	echo json_encode($result);
 }
 
 /*function downloadPhotos_Old(){
	include($_SERVER['DOCUMENT_ROOT']."/Settings.php");	
	require_once($_SERVER['DOCUMENT_ROOT']."/class/Zip.class.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/db/DbConnection.php");
	include($_SERVER['DOCUMENT_ROOT']."/Tools.php");
	
	$userID = $_GET["userID"];
	$userID = utf8_urldecode($userID);
	
	if(!isset($userID) || $userID == "")
		$userID = "12";
	
	$albumName = $_GET["albumName"];
	$albumName = stripslashes($albumName);
	$albumName = utf8_urldecode($albumName);
	
	$photos_array = $_GET['photos_array'];	
	$photos_array = utf8_urldecode($photos_array);
	$photos_array =  explode(",",$photos_array);
	$files = array();
	
	foreach($photos_array as $key => $value)
	{
		$query = "SELECT URLLARGE FROM TBLPHOTO WHERE PKPHOTOID='".$value."' ";
		$result = executeQuery($query);	
			
		if(mysql_num_rows($result) > 0)
		{
			while($row = mysql_fetch_array($result)){			
				//$files[] = '/'.$imageServer.'/'.$row["PHYSICALPATH"].'/'.$row["NAMELARGE"];
				$files[] = $row["URLLARGE"];
			}
		}			
	}
	
	if(empty($files))
	{
		$result = array("success"	=>	false,	"error"	=>	"No photo found!", "link" => '');
	}
	else
	{	
		$result = array("success"	=>	false,	"error"	=>	"Invalid User!", "link" => '');
	
		//if(isset($userID))
		//{ 
 			//$filepath =  '/'.$imageServer.'/'.$imageSubFolder.'/'.$userID.'/';	
			//$filename = $userID.'_'.time().'.zip';		
			//$filename = $userID.'.zip';
			$filename = $albumName.'.zip';
			//$zipfile =  $filepath.'/'.$filename;
			$zipfile =  '/tmp/'.$filename;
			//$link = $sitepath.'/'.$imageSubFolder.'/'.$userID.'/'.$filename;
			$link = '';
			if(!is_dir ($filepath)) 		
				mkdir($filepath);		
		
			if (!is_writable($filepath))
    			@chmod($filepath,777);		

			//$files = array($filepath.'1403243782_large.jpg',$filepath.'1403243830_large.jpg',$filepath.'1403159079_large.jpg',$filepath.'1403158919_large.jpg');	
	
			$myzip = new Zip($userID);
			$my_array = $myzip->create_zip($files,$zipfile,true);
			list($success,$error) = array_values($my_array);
		
			if($success)
			{
				//$result = array('success'	=>	true, 'error' => false, 'link' => $link);
				$result = $zipfile;
			}
			else
			{
				$result = array('success' =>  false, 'error' => $error, 'link' => '');
			}		
		//}
	}
	
	echo json_encode($result); 
 }*/

?>