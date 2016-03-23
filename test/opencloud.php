<?php 

require '../vendor/autoload.php';

use OpenCloud\Rackspace;

$client = new Rackspace(Rackspace::US_IDENTITY_ENDPOINT, array(
    'username' => 'fatihsen',
    'apiKey'   => 'b597a08a9458f66824312824c718362f'
));

// 2. Obtain an Object Store service object from the client.
$region = 'ORD';
$objectStoreService = $client->objectStoreService(null, $region);
// 3. Get container.
$container = $objectStoreService->getContainer('albums');
/** @var $container OpenCloud\ObjectStore\Resource\Container **/
printf("Container name: %s\n", $container->getName());


// 4. Upload an object to the container.
/*$localFileName = 'http://oyvent.com/images/sh4.jpg';
$remoteFileName = '2.jpg';
$fileData = fopen($localFileName, 'r');
$container->uploadObject($remoteFileName, $fileData);*/


// 4. Get object.
$objectName = '2.jpg';
$object = $container->getObject($objectName);
// 5. Get object's publicly-accessible HTTP URL.
$httpUrl = $object->getPublicUrl();
printf("Object's publicly accessible HTTP URL: %s\n", $httpUrl);



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
</body>
</html>
