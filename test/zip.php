
<?php

require_once($_SERVER['DOCUMENT_ROOT']."/Settings.php");

$zip = new ZipArchive();

$userID = 12;

//$filepath =  '/'.$imageServer.'/'.$imageSubFolder.'/'.$userID.'/';
$filepath = $_SERVER['DOCUMENT_ROOT']."/tmp/";

/*if(!is_dir ($filepath)) 		
	mkdir($filepath);		
		
if (!is_writable($filepath))
    @chmod($filepath,777);*/
	
//$destination .=  $filepath.'/'.$userID.'_'.time().'.zip';
//$destination =  $filepath.'/'.$userID.'.zip';
$destination = $_SERVER['DOCUMENT_ROOT'].'/tmp/test.zip';

//$files = array($filepath.'1403243782_large.jpg',$filepath.'1403243830_large.jpg',$filepath.'1403159079_large.jpg',$filepath.'1403158919_large.jpg');	

$files = array('https://s3-oy-vent-images-14.s3.amazonaws.com/12/c2d806170182453b-large.jpg');
	
$iscreated = create_zip($files,$destination,true,$userID);

if($iscreated)
{
	
}
else
{
	echo "Zip file could not be created, please try again!";
}

//echo "is created:".$iscreated;

/* creates a compressed zip file */
function create_zip($files = array(),$destination = '',$overwrite = false,$userID) {
	//if the zip file already exists and overwrite is false, return false
	if(file_exists($destination) && !$overwrite) { return false; }
	//vars
	$valid_files = array();
	//if files were passed in...
	if(is_array($files)) {
		//cycle through each file
		foreach($files as $file) {
			//make sure the file exists			
			//if(file_exists($file)) {
				$valid_files[] = $file;
			//}
		}
	}
	
	//if we have good files...
	if(count($valid_files)) {
		//create the archive
		$zip = new ZipArchive();
		if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			return false;
		}
		//add the files
		foreach($valid_files as $file) {
			$download_file = file_get_contents($file);//for url files i.e. http://google.com/images/logo.png
			
			$pathinfo = pathinfo($file);
        	$filename = $pathinfo['filename'];       
        	$ext = $pathinfo['extension'];
			$ext = strtolower($ext);
			echo "filename:".$filename."  ext:".$ext."<br>";
			$zip->addFile($download_file,$userID.'/'.$filename.'.'.$ext);
			//$zip->addFromString($userID.'/'.$filename.'.'.$ext,$download_file);
			//$zip->addFromString($download_file,$userID.'/'.$filename.'.'.$ext);
			//$zip->addFile($file,$file);
		}
		//debug
		echo 'The zip archive '.$zip->filename.' contains ',$zip->numFiles,' files with a status of ',$zip->status."<br>";
		
		//close the zip -- done!
		$zip->close();
		
		//check to make sure the file exists
		//if(file_exists($destination))
			//echo $destination;
		return file_exists($destination);
	}
	else
	{
		return false;
	}
	
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Untitled Document</title>
</head>

<body>
</body>
</html>