<?php 

class Zip
{
	public $userID = NULL;	
	
	function __construct($userID=NULL){
		$this->userID = $userID;		
	}
	
	/* creates a compressed zip file */
	function create_zip($files = array(),$zipfile = '',$overwrite = false) {
		//if the zip file already exists and overwrite is false, return false
		if(file_exists($zipfile) && !$overwrite) { return false; }
		//vars
		$valid_files = array();
		//if files were passed in...
		if(is_array($files)) {
			//cycle through each file
			foreach($files as $file) {
				//make sure the file exists
				if(file_exists($file)) {
					$valid_files[] = $file;
				}
			}
		}
	
		//if we have good files...
		if(count($valid_files)) {
			//create the archive
			$zip = new ZipArchive();
			if($zip->open($zipfile,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
				return false;
			}
			//add the files
			foreach($valid_files as $file) {
				$pathinfo = pathinfo($file);
        		$filename = $pathinfo['filename'];       
        		$ext = $pathinfo['extension'];
				$ext = strtolower($ext);
				$zip->addFile($file,$filename.'.'.$ext);
				//$zip->addFile($file,$file);
			}
			//debug
			//echo 'The zip archive '.$zip->filename.' contains ',$zip->numFiles,' files with a status of ',$zip->status;
		
			//close the zip -- done!
			$zip->close();
		
			//check if the zip file exists
			if(file_exists($zipfile))
				return array('success'	=>	true, 'error' => '');
			else
				return array('success'	=>	false, 'error' => 'Zip file does not exist!');
		}
		else
		{
			return array('success'	=>	false, 'error' => 'No valid files to zip!');
		}	
		
		return array('success'	=>	false, 'error' => 'Zip process could not been started, please try again!');
	}
}
?>