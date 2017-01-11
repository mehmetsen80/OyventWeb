<?php 

	include($_SERVER['DOCUMENT_ROOT']."/Settings.php");	
	require_once($_SERVER['DOCUMENT_ROOT']."/class/Zip.class.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/db/DbConnection.php");
	include($_SERVER['DOCUMENT_ROOT']."/Tools.php");
	
	$userID = $_POST['hdUserID'];
	$zipfilename =  $_POST['hdZipName'];
	$zipfilename = stripslashes($zipfilename).'.zip';
	$photos_array = $_POST['hdPhotos'];	
	$photos_array =  explode(",",$photos_array);
	$files = array();
	
	# get the large photo urls
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
	
	# create new zip opbject
	$zip = new ZipArchive();
	
	# create a temp file & open it
	$tmp_file = tempnam('.','');
	
	$zip->open($tmp_file, ZipArchive::CREATE);
	
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
	
	# now dumbp back as zip file
	header('Content-disposition: attachment; filename='.$zipfilename);
	header('Content-type: application/zip');
	readfile($tmp_file);
	

?>