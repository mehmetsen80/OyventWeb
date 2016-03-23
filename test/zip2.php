<?php 
	 	/*$files = array(
    'http://google.com/images/logo.png',
    'http://upload.wikimedia.org/wikipedia/commons/thumb/5/53/Wikipedia-logo-en-big.png/220px-Wikipedia-logo-en-big.png'
);*/


$files = array('https://s3-oy-vent-images-14.s3.amazonaws.com/12/c2d806170182453b-large.jpg');

# create new zip opbject
$zip = new ZipArchive();

# create a temp file & open it
$tmp_file = tempnam('.','');
$tmp_file = tempnam($_SERVER['DOCUMENT_ROOT'].'/tmp/','');

$zip->open($tmp_file, ZipArchive::CREATE);

$userID = 12;

$filepath = $_SERVER['DOCUMENT_ROOT'].'/tmp/'.$userID;
	if(!is_dir ($filepath)) 		
		mkdir($filepath);		
		
	if (!is_writable($filepath))
    	@chmod($filepath,777);	

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

# send the file to the browser as a download
/*header('Content-disposition: attachment; filename=download.zip');
header('Content-type: application/zip');
readfile($tmp_file);*/

header('Content-Description: File Transfer');
    header('Content-Type: application/force-download');
    header('Content-Disposition: attachment; filename='.basename($tmp_file));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($tmp_file));
    ob_clean();
    flush();
    readfile($tmp_file);

?>