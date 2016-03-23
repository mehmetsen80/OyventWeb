<?php 

class Text2Image 
{
	var $font = '../fonts/arial.ttf'; //default font. directory relative to script directory.
	var $font_size = 16;
	var $font_color =  0x000000;
	var $width = 0;
	var	$height = 0;
	var $image = NULL;
	var $text = "";
	var $filename= "";
	var $newimage = NULL;
	
	function __construct($width=250,$height=250,$text="",$filename=""){
		
		$this->width = $width;
		$this->height = $height;
		$this->text = $text;
		$this->text = $this->wrap($this->font_size,0,$this->font,$this->text,$this->width);
		$this->filename = $filename;
				
	}
	
	public function getImage(){
		return $this->image;
	}
	
	public function createImage(){
		
		// Create the image
		$this->image = imagecreatetruecolor($this->width, $this->height);

		// Create some colors
		$white = imagecolorallocate($this->image, 255, 255, 255);
		//$grey = imagecolorallocate($this->image, 128, 128, 128);
		$black = imagecolorallocate($this->image, 0, 0, 0);
		imagefilledrectangle($this->image, 0, 0, $this->width+2, $this->height+2, $white);		

		// Add some shadow to the text
		imagettftext($this->image, $this->font_size, 0, 3, 21, $grey, $this->font, $this->text);

		// Add the text
		imagettftext($this->image, $this->font_size, 0, 2, 20, $black, $this->font, $this->text);

		$this->save();
		
		// Using imagepng() results in clearer text compared with imagejpeg()
		// imagepng($this->image);
		//imagedestroy($this->image);
		
		return $this->image;
	}
	
	function save($compression=100, $permissions=null) {      
	     @imagejpeg($this->image,$this->filename,$compression);	
		 
		 //if( $permissions != null) {
         		//@chmod( $this->filename,$permissions);
      	  //}
	}
	
	 function output() {
         @imagejpeg($this->image);      
	 }
	function getWidth() {
   	  if($this->image != NULL)
      	return imagesx($this->image);		
   	}
  
   function getHeight() {
     if($this->image != NULL)
       return imagesy($this->image);	  
   }
	
	function destroy_buffer()
    {
   		// destroy the images
		if(isset($this->image))
        	imagedestroy($this->image);		
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
   
   function getFileSize()
   {
   	 // remember that default is in bytes
	 if($this->filename != NULL)
	 {
   	  	if(file_exists($this->filename))
	  		return filesize($this->filename);	
	 }
   }
	
	public function wrap($fontSize, $angle, $fontFace, $string, $width){
		
		$ret = "";
   
    	$arr = explode(' ', $string);
   
    	foreach ( $arr as $word ){
   
        	$teststring = $ret.' '.$word;
        	$testbox = imagettfbbox($fontSize, $angle, $fontFace, $teststring);
        	if ( $testbox[2] > ($width) ){
            	$ret.=($ret==""?"":"\n").$word;
        	} else {
            	$ret.=($ret==""?"":' ').$word;
        	}
    	}
   
    	return $ret;
	
	}
}

?>