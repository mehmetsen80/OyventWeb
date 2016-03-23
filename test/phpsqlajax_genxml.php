
<?php 

//error_reporting(-1);
//ini_set('display_errors', 'On');	
	
require_once($_SERVER['DOCUMENT_ROOT']."/db/DbConnection.php");

//if (PHP_VERSION>='5')
 //require_once($_SERVER['DOCUMENT_ROOT']."/class/domxml-php4-to-php5.php");
	
// Start XML file, create parent node

$doc = new DomDocument();
$node = $doc->createElement("markers","");
$parnode = $doc->appendChild($node);

//echo "hello world!<br>";

$albumID = $_GET["albumID"];

$query = "SELECT LATITUDE,LONGITUDE,CAPTION,POSTDATE FROM TBLPHOTO 
		  WHERE FKALBUMID=".$albumID." AND LATITUDE != '' AND LONGITUDE != '' 
		 ORDER BY POSTDATE DESC LIMIT 20 ";
$result = executeQuery($query);
$rows = mysql_fetch_rowsarr($result);



header("Content-type: text/xml");

// Iterate through the rows, adding XML nodes for each
$i = 0;
foreach( $rows as $row ){
 $i++;
  // ADD TO XML DOCUMENT NODE
  $node = $doc->createElement("marker","");
  $newnode = $parnode->appendChild($node);
    
//echo $row['CAPTION']." ". substr($row['CAPTION'],0,20)."<br>";
  $newnode->setAttribute("name",  "");
  $newnode->setAttribute("address", '');
  $newnode->setAttribute("lat", $row['LATITUDE']);
  $newnode->setAttribute("lng", $row['LONGITUDE']);
  $newnode->setAttribute("postdate", $row['POSTDATE']);
  $newnode->setAttribute("type", 'bar');
    
  
}

$xmlfile = $doc->saveXML($parnode);
echo $xmlfile;


?>