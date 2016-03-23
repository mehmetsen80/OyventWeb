<?php 

require_once($_SERVER['DOCUMENT_ROOT']."/db/DbConnection.php");
require_once($_SERVER['DOCUMENT_ROOT']."/class/UserInfo.class.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Tools.php");

class Picture {
   
   private $image;
   private $image_type;
   private $newimage;
   private $filename;
   private $fullserverpath;
   private $folderpath;
   private $feedId;
   private $userObject = NULL;
   
   
   const IMAGE_GIF = 'GIF';
   const IMAGE_BMP = 'BMP';
   const IMAGE_WBMP = 'WBMP';
   const IMAGE_JPEG = 'JPEG';
   const IMAGE_JPEG_LARGE = 'JPEG:LARGE';
   const IMAGE_JPEG_SMALL = 'JPEG:SMALL';
   const IMAGE_JPEG_MEDIUM = 'JPEG:MEDIUM';
   const IMAGE_JPEG_THUMB = 'JPEG:THUMB';
   const IMAGE_JPG = 'JPG';
   const IMAGE_JPG_LARGE = 'JPG:LARGE';
   const IMAGE_JPG_SMALL = 'JPG:SMALL';
   const IMAGE_JPG_MEDIUM = 'JPG:MEDIUM';
   const IMAGE_JPG_THUMB = 'JPG:THUMB';
   const IMAGE_PNG = 'PNG'; 
   const IMAGE_PNG_LARGE = 'PNG:LARGE';
   const IMAGE_PNG_SMALL = 'PNG:SMALL';
   const IMAGE_PNG_MEDIUM = 'PNG:MEDIUM';
   const IMAGE_PNG_THUMB = 'PNG:THUMB';
   
   function __construct() {
   	$argv = func_get_args();
	switch( func_num_args() )
	{
		default:
		case 0:	
			self::__construct1();
			break;
		case 1:
			self::__construct2( $argv[0]);
			break;
	}  
     	
   }  
   
   
   function __construct1() {
   		$this->initUser();	
   }
   
   function __construct2($feedId) {
   		$this->feedId = $feedId;
  		$this->initUser();		
   }
   
   function initUser()
   {
   	 @session_start();	
   	 if(isset($_SESSION['userObject']))
	 {
	  	$this->userObject = $_SESSION['userObject'];
	 }
   }
   
   function getImage()
   {
   	 return $this->image;
   }

   function load($filename,$image_type="JPEG") {
   
   	  $this->filename = $filename; 	
	  $this->image_type = $image_type;		
	  	  
      if( $this->image_type ==  self::IMAGE_JPEG || $this->image_type == self::IMAGE_JPG || 
	  $this->image_type == self::IMAGE_JPEG_LARGE || $this->image_type == self::IMAGE_JPEG_MEDIUM || 
	  $this->image_type == self::IMAGE_JPEG_SMALL || $this->image_type == self::IMAGE_JPEG_THUMB || 
	  $this->image_type == self::IMAGE_JPG_LARGE || $this->image_type == self::IMAGE_JPG_MEDIUM || 
	  $this->image_type == self::IMAGE_JPG_SMALL || $this->image_type == self::IMAGE_JPG_THUMB || 
	  $this->image_type == self::IMAGE_PNG_LARGE || $this->image_type == self::IMAGE_PNG_MEDIUM ||
	  $this->image_type == self::IMAGE_JPG_SMALL || $this->image_type == self::IMAGE_PNG_THUMB) {
         $this->image = @imagecreatefromjpeg($this->filename);
      } elseif( $this->image_type == self::IMAGE_GIF ) {	  
         $this->image = @imagecreatefromgif($this->filename);		
      } elseif( $this->image_type == self::IMAGE_PNG ) {
         $this->image = @imagecreatefrompng($this->filename);
      } elseif( $this->image_type == self::IMAGE_BMP || $this->image_type == self::IMAGE_WBMP ) {
	  	 $this->image = @imagecreatefromwbmp($this->filename);
	  }
	  
	  
   }
   
   function destroy_buffer()
   {
   		// destroy the images
		if(isset($this->newimage))
        	imagedestroy($this->newimage);
        
		if(isset($this->image))
			imagedestroy($this->image);
   }
   
   function save($filename, $compression=100, $permissions=null) {
      
	   $this->filename = $filename;
	  
	  //ob_start();
	  
	   /*use  onlythis if you decide to use JPG only*/
	   $this->image_type == self::IMAGE_JPG;//let's save any image in jpeg	  
	   @imagejpeg($this->newimage,$this->filename,$compression);



	  /*uncomment this if you decide to use JPG only*/
      /*if( $this->image_type == self::IMAGE_JPEG || $this->image_type == self::IMAGE_JPG ) {
         @imagejpeg($this->newimage,$this->filename,$compression);		
      } elseif( $this->image_type == self::IMAGE_GIF ) {
         @imagegif($this->newimage,$this->filename);         
      } elseif( $this->image_type == self::IMAGE_PNG ) {
         @imagepng($this->newimage,$this->filename);
      } else if( $this->image_type == self::IMAGE_BMP || $this->image_type == self::IMAGE_WBMP ){
	  	 @imagewbmp($this->newimage,$this->filename);
	  }*/
      
	  //ob_end_clean();
	  
	  //if( $permissions != null) {
         //@chmod( $this->filename,$permissions);
      //}
   }  
   
   
   function output() {
      if( $this->image_type == self::IMAGE_JPEG || $this->image_type == self::IMAGE_JPG  ) {
         @imagejpeg($this->image);
      } elseif( $this->image_type == self::IMAGE_GIF ) {
         @imagegif($this->image);         
      } elseif( $this->image_type == self::IMAGE_PNG ) {
         @imagepng($this->image);
      } else if( $this->image_type == self::IMAGE_BMP || $this->image_type == self::IMAGE_WBMP ){
	  	 @imagewbmp($this->image);
	  }
   }   
   
   function getWidth() {
   	  if($this->image != NULL)
      	return imagesx($this->image);
		//return imagesx($this->filename);
   }
   function getHeight() {
     if($this->image != NULL)
       return imagesy($this->image);
	  // return imagesy($this->filename);
   }
   
   function getFileSize()
   {
   	 // remember that default is in bytes
	 if($this->filename != NULL)
	 {
   	  	if(file_exists($this->filename))
	  		return filesize($this->filename);	
	 }
   }
  
   
   function resizeToHeight($height) {
   	 if($this->getHeight() != 0 && $this->getHeight() != NULL)
	 {
       $ratio = $height / $this->getHeight();
       $width = $this->getWidth() * $ratio;
       $this->resize($width,$height);
	 }
   }
   
   function resizeToWidth($width) {
   	
	  if($this->getWidth() != 0 && $this->getWidth() != NULL)
	  {
      	$ratio = $width / $this->getWidth();
      	$height = $this->getheight() * $ratio;
      	$this->resize($width,$height);
	  }
   }
   
   function scale($scale) {
      $width = $this->getWidth() * $scale/100;
      $height = $this->getheight() * $scale/100; 
      $this->resize($width,$height);
   }
   
   function resize($width=NULL,$height=NULL) {
   
   	 if($this->image != NULL)
   	 {	 	
	 	$width = $width == NULL ? $this->getWidth() : $width;
		$height = $height == NULL ? $this->getHeight() : $height;
			
      	$new_image = imagecreatetruecolor($width, $height);
      	imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
		
      	//$this->image = $new_image; 
		$this->newimage = $new_image;		 
	 }
   }  
     
}


?>
