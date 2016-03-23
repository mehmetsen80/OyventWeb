<?php 

if (!ini_get('display_errors')) {
    ini_set('display_errors', '1');
}

//require_once("../lib/aws/config.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/class/UUID.class.php');
require_once($_SERVER['DOCUMENT_ROOT']."/class/Picture.class.php");

/*$result = $s3Client->listBuckets();

foreach ($result['Buckets'] as $bucket) {
    // Each Bucket value will contain a Name and CreationDate
    echo "{$bucket['Name']} - {$bucket['CreationDate']}\n";	
}

$iterator = $s3Client->getIterator('ListObjects', array(
    'Bucket' => $bucketName
));

//this prints all the objects
foreach ($iterator as $object) {
    echo $object['Key'] . "\n";
}*/

include($_SERVER['DOCUMENT_ROOT']."/lib/aws/awsfnc.php");	

$keyprefix = UUID::generate(UUID::UUID_RANDOM, UUID::FMT_STRING,"02481965");
			$keylarge = $keyprefix.'-large';
			
$largeurl = "http://scontent-a.cdninstagram.com/hphotos-xpf1/t51.2885-15/1171981_732967146773535_26588184_a.jpg";

			$piclarge = new Picture();
			$piclarge->load($largeurl,"JPG");
			$width = $piclarge->getWidth();		
			if($width > 720)
				$width = 720;		
			$piclarge->resizeToWidth($width);
			$newfilelarge = '/tmp/'.$keylarge.'.jpg';
			$piclarge->save($newfilelarge,70,0777);					
			$resultlarge = createObject(6, $keylarge,$newfilelarge);
			$objurllarge = $resultlarge['ObjectURL'];
			$piclarge_size = $piclarge->getFileSize();
			$piclarge->destroy_buffer();
			if(file_exists($newfilelarge))
				unlink($newfilelarge);

echo "size:".$piclarge_size;
echo "<br>object url large:".$objurllarge;
echo "<br>";
print_r($resultlarge);




 /*function createObject($foldername, $keyname, $newfilename){	
	require_once($_SERVER['DOCUMENT_ROOT']."/lib/aws/config.php");	
	
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
	
}*/





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