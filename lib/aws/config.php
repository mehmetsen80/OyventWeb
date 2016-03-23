<?php 

require '../vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
/*use Guzzle\Http\EntityBody;
use Guzzle\Service\Resource\Model;*/

// Instantiate the S3 client with your AWS credentials
$s3Client = S3Client::factory(array(
    'key'    => 'AKIAJEIPKTR64YRURJ6A',
    'secret' => 'x8vbYgQcXFHehQl6Lv7fkiiXgLJH3zjLM/Umm6/R',	
));

$bucketName = 's3-oy-vent-images-16';




?>