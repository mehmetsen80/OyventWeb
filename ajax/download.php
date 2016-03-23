<?php 

$file = $_GET["file"];
$userID = $_GET["userID"];

/*header('Content-disposition: attachment; filename={$file}');
header('Content-type: application/zip');
readfile($tmp_file);*/


if ($file) {
  //Perform security checks
  //.....check user session/role/whatever
  $res = $_SERVER['DOCUMENT_ROOT'].'/tmp/'.$userID.'/'.$file;
  if (file_exists($res)) {
    /*header('Content-Description: File Transfer');
    header('Content-Type: application/force-download');
    header('Content-Disposition: attachment; filename='.basename($res));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($res));
    ob_clean();
    flush();
    readfile($res);*/
    //@unlink($res);
	
	header('Content-disposition: attachment; filename={$file}');
	header('Content-type: application/zip');
	readfile($res);
  }
  else{
	  echo "File does not exist!";
  }

}

?>