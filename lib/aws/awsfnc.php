<?php 

function createObject($foldername, $keyname, $newfilename){	
	include($_SERVER['DOCUMENT_ROOT']."/lib/aws/config.php");	
	
	$result = array();
	
	try {

		$result = $s3Client->putObject(array(
    		'Bucket'       => $bucketName,
    		'Key'          => $foldername .'/'.$keyname,    		
    		'ContentType'  => 'image/jpeg',			
			'SourceFile' => $newfilename,
    		'ACL'          => 'public-read',
    		'StorageClass' => 'REDUCED_REDUNDANCY'
		));
 		
	} catch (S3Exception $e) {
    	echo $e->getMessage() . "\n";
	}
	
	return $result;
	
}

function getObject($keyname){

	include($_SERVER['DOCUMENT_ROOT']."/lib/aws/config.php");

	$result = array();
	
	try {
		
		$result = $s3->getObject(array(
    		'Bucket' => $bucketName,
    		'Key'    => $keyname
		));
			
	} catch (S3Exception $e) {
    	echo $e->getMessage() . "\n";
	}
	
	return $result;

}

function deleteObject($foldername, $keyname){
	include($_SERVER['DOCUMENT_ROOT']."/lib/aws/config.php");
	
	$result = array();
	
	try{
		$result = $s3Client->deleteObject(array(
    		'Bucket' => $bucketName,
    		'Key'    => $foldername .'/'.$keyname
		)); 
	}catch(S3Exception $e){
		echo $e->getMessage() ."\n";
	}
	
	return $result;
}

?>