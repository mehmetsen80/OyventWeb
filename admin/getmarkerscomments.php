
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

$photoID = $_GET["photoID"];
$photoLAT = $_GET["photoLAT"];
$photoLONG = $_GET["photoLONG"];

$query = "SELECT '' AS PKCOMMENTID, '".$photoID."' as FKPHOTOID, '".$photoLAT."' AS LATITUDE, 
'".$photoLONG."' AS  LONGITUDE, '' AS COMMENT, p.POSTDATE, u.FULLNAME, p.URLTHUMB, 'YES' AS ISFEED 
FROM TBLPHOTO p INNER JOIN TBLUSER u ON p.FKUSERID = u.PKUSERID WHERE p.PKPHOTOID='".$photoID."'"; 
$query .= " UNION ";
$query .= "SELECT c.PKCOMMENTID, c.FKPHOTOID, c.LATITUDE,c.LONGITUDE,c.COMMENT,c.POSTDATE,
			u.FULLNAME,p.URLTHUMB, 'NO' AS  ISFEED 
		  FROM TBLCOMMENT c INNER JOIN TBLUSER u ON u.PKUSERID = c.FKUSERID 
		  INNER JOIN TBLPHOTO p ON p.PKPHOTOID = c.FKPHOTOID 
		  WHERE c.FKPHOTOID='".$photoID."' AND c.LATITUDE != '' AND c.LONGITUDE != '' 
		  ORDER BY  POSTDATE DESC LIMIT 100 ";
		  //echo $query;
$result = executeQuery($query);
$rows = mysql_fetch_rowsarr($result);



header("Content-type: text/xml");


  /*$node = $doc->createElement("marker","");
  $newnode = $parnode->appendChild($node);
  $newnode->setAttribute("commentid", 'aa');
  $newnode->setAttribute("photoid", $photoID);
  $newnode->setAttribute("name",  ''); 
  $newnode->setAttribute("comment", 'aa');
  $newnode->setAttribute("address", '');
  $newnode->setAttribute("urlthumb", '');
  $newnode->setAttribute("lat", $photoLAT);
  $newnode->setAttribute("lng", $photoLONG);
  $newnode->setAttribute("postdate", '');  
  $newnode->setAttribute("isfeed", '');
  $newnode->setAttribute("type", 'bar');
  $newnode->setAttribute("sql",'');*/



// Iterate through the rows, adding XML nodes for each
$i = 0;
foreach( $rows as $row ){
 $i++;
  // ADD TO XML DOCUMENT NODE
  $node = $doc->createElement("marker","");
  $newnode = $parnode->appendChild($node);
    
//echo $row['CAPTION']." ". substr($row['CAPTION'],0,20)."<br>";
  $newnode->setAttribute("commentid", $row['PKCOMMENTID']);
  $newnode->setAttribute("photoid", $row['FKPHOTOID']);
  $newnode->setAttribute("name",  $row["FULLNAME"]);  
  $comment = str_replace("\n", "<br>\n", $row["COMMENT"]);	
  
  $newnode->setAttribute("comment", $comment);
  $newnode->setAttribute("address", '');
  $newnode->setAttribute("urlthumb", $row["URLTHUMB"]);
  $newnode->setAttribute("lat", $row['LATITUDE']);
  $newnode->setAttribute("lng", $row['LONGITUDE']);
  $newnode->setAttribute("postdate", $row['POSTDATE']);  
  $newnode->setAttribute("isfeed", $row['ISFEED']);
  $newnode->setAttribute("type", 'bar');
  $newnode->setAttribute("sql",$query);
    
  
}

$xmlfile = $doc->saveXML($parnode);
echo $xmlfile;


?>