<?php

 function openDb()
 {
    include($_SERVER['DOCUMENT_ROOT']."/Settings.php");
	
	$dbhost = 'localhost';
	$dbname = 'dbdata';	
	$dbuser = 'root';
	$dbpass = 'Memo3480$';
	
	if(!isset($conn))
    	$conn = mysql_connect($dbhost, $dbuser,$dbpass);
		
	//	mysql_query("SET character_set_results=utf8", $conn);
	//mb_language('uni');
	//mb_internal_encoding('UTF-8');
		
		//mysql_query("SET NAMES utf8");
		//mysql_query("SET collation_connection = utf8_bin;;");
		//mysql_query('SET CHARACTER SET utf8_general_ci');
		//mysql_query('SET COLLATE SQL_Latin1_General_CP1_CI_AI');
		//mysql_set_charset('utf8');
  
    if (!$conn)
    {
	  die('Could not connect: ' . mysql_error());
    }
	
  
    mysql_select_db($dbname) or die(mysql_error());
	//mysql_set_charset('utf8');
	
	//mysql_query("SET NAMES ISO-8859-1");
	//mysql_query("SET collation_connection = utf8_bin;");
	//mysql_query('SET CHARACTER SET utf8_general_ci');
 }
 
 
 function closeDb()
 {
  	//require_once("config.php");
  	mysql_close();
 }  

 function executeQuery($query)
 {  	
    openDb();
    $result = mysql_query( $query ) or die(mysql_error());     
   	closeDb();
	    
	return $result;
 }
 
  
   
  function executeInsertQuery($query)
  {  
	openDb();
    $result = mysql_query( $query );	
	//get the last inserted id
	$last_inserted_id = mysql_insert_id();      
	
   	closeDb();
	    
	return $last_inserted_id;
	
  }
   
  
 function getLastInsertedID()
 {
	openDb();	
	$last_inserted_id = -1;
	
	try
	{
		//get the last inserted id
		$last_inserted_id = mysql_insert_id();	
	}
	catch(Exception $ex)
	{
		$last_inserted_id = -1;
	}
	
	closeDb();
	
	return $last_inserted_id;
}  

 function executeQueryForTrans($query)
 {  	    
    $result = mysql_query( $query ) or die(mysql_error()); 
	       		    
	return $result;
 }
 
 function executeInsertQueryForTrans($query)
 {  	    
    $result = mysql_query( $query ) or die(mysql_error()); 
	
	$last_inserted_id = mysql_insert_id();      
	    
	return $last_inserted_id;       		    	
 }

 function beginTrans()
 {
 	openDb(); //connect to database		 	
	mysql_query("SET AUTOCOMMIT=0");
	mysql_query("BEGIN");	
 }

 function commitTrans()
 {
 	mysql_query("COMMIT");
	closeDb(); // close the database
 }

 function rollbackTrans()
 {
	mysql_query("ROLLBACK");
	closeDb(); // close the database
 }
 
 //This function fills a multidemensional array with both columns names and column indexes
 function mysql_fetch_rowsarr($result, $numass=MYSQL_BOTH)
 {
  	$j=0;
	$list=NULL;
	
	if(mysql_num_rows($result)>0)
	{
  		$keys=array_keys(mysql_fetch_array($result, $numass));
  		mysql_data_seek($result, 0);
    	
		while ($row = mysql_fetch_array($result, $numass)) {
      	
			foreach ($keys as $speckey) {
        		$list[$j][$speckey]=$row[$speckey];
      		}
    		
			$j++;
    	}
	}
  		
	return $list;
 }
 
 //implemented after moving to cloud server. 
 //A MySQL connection is required before using mysql_real_escape_string() otherwise an error of level E_WARNING is generated, and FALSE is returned. Source: http://php.net/manual/en/function.mysql-real-escape-string.php
 function real_escape_string($param)
 {
 	openDb();
	$param = mysql_real_escape_string($param);//commented after moving to cloud servers
	closeDb();
	
	return $param;
 }
  
  
   
   

    # Gets key / value pair into memcache ... called by mysql_query_cache()
    function getCache($key) {
        
		@session_start();
		
		$memcache = $_SESSION['memcache'];
		
		//$memcache = new Memcache;
		
        
		if($memcache)
		{
		//	$memcache->connect('localhost', 11211) or die ("Could not connect");
			$res = $memcache->get($key);
			//print_r($res);
			//echo "ddd";
		}
		else
		{
			$res = false;
		}
		
		return $res;
		
		//return ($memcache) ? $memcache->get($key) : false;
    }

    # Puts key / value pair into memcache ... called by mysql_query_cache()
    function setCache($key,$object,$timeout = 60) {
        //global $memcache;
		
		@session_start();
		
		$memcache = $_SESSION['memcache'];
		
		//$memcache = new Memcache;
		
		
		if($memcache)
		{
		  //$memcache->connect('localhost', 11211) or die ("Could not connect");
		  $res = $memcache->set($key,$object,MEMCACHE_COMPRESSED,$timeout);
		  echo "hi";
		}
		else
		{
			$res = false;
		}
		
		return $res;
		
       // return ($memcache) ? $memcache->set($key,$object,MEMCACHE_COMPRESSED,$timeout) : false;
    }

    # Caching version of mysql_query()
    function mysql_query_cache($sql, $keyName, $timeout = 60) {
	
		$memcache = new Memcache;
		$memcache->connect('localhost', 11211) or die ("Could not connect");
		
		
		$result = $memcache->get($keyName);
		
		if ($result == false) {				
			
			//$result = executeQuery($sql);
			openDb();
    		$res = mysql_query( $sql ) or die(mysql_error());     
   			closeDb();
			
			//$result = mysql_fetch_array($result);
			
			$result = array();
			
				
			$result = mysql_fetch_rowsarr($res);	
			
			$g = $memcache->set($keyName, $result, true, $timeout); 	
			
			//echo "g:".$g;
			
			//if (!setCache(md5($keyName),$result,$timeout)) {
				 # If we get here, there isn't a memcache daemon running or responding
			//}
			
		}				
		
		
		return $result;
		
	
       /*if (($result = getCache(md5("mysql_query" . $sql))) !== false) {
	   
	   		openDb();	   
            $cache = false;
            $result = ($linkIdentifier !== false) ? mysql_query($sql,$linkIdentifier) : mysql_query($sql);
            closeDb();
			
			if (is_resource($result) && (($rows = mysql_num_rows($result)) !== 0)) {
               
			   /for ($i=0;$i<$rows;$i++) {
                    $fields = mysql_num_fields($r);
                    $row = mysql_fetch_array($r);
                    for ($j=0;$j<$fields;$j++) {
                        if ($i === 0) {
                            $columns[$j] = mysql_field_name($r,$j);
                        }
                        $cache[$i][$columns[$j]] = $row[$j];
                    }
                }
				
                if (!setCache(md5("mysql_query" . $sql),$result,$timeout)) {
                    # If we get here, there isn't a memcache daemon running or responding
                }
            }
        }
		
        return $result;*/
   }
  
  
?>