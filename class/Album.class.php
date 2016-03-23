<?php 

 require_once($_SERVER['DOCUMENT_ROOT']."/db/DbConnection.php");
 require_once($_SERVER['DOCUMENT_ROOT'].'/class/UUID.class.php');
 
 
class Album
{
	public $userID = NULL;	
	public $albumID = NULL;
	public $albumName = NULL;
	public $datecreated = NULL;
	public $privacy = NULL;
	public $username = NULL;
	public $address = NULL;
	public $photosize = NULL;
	public $albumUserID = NULL;
	public $latitude = NULL;
	public $longitude = NULL;
	public $radious = NULL;
	public $urllarge = NULL;
	public $urlmedium = NULL;
	public $urlsmall = NULL;
	public $urlthumb = NULL;
	const LIST_INTERVAL = 100;

	
	function __construct($userID=NULL,$albumID=NULL){
		$this->userID = $userID;
		
		if(isset($albumID)){
			$this->albumID = $albumID;
			
			$query = "SELECT FKUSERID,NAME,POSTDATE,PRIVACY,USERNAME,ADDRESS,LATITUDE,LONGITUDE,RADIOUS
			,URLLARGE,URLMEDIUM,URLSMALL,URLTHUMB FROM TBLALBUM 
			WHERE PKALBUMID = '".$albumID."' ";
			$result = executeQuery($query);			
				
			if(mysql_num_rows($result)>0)
			{
				$row = mysql_fetch_array($result);
				$this->albumName = real_escape_string($row["NAME"]);
				$this->albumName = stripslashes($this->albumName);		
				$this->datecreated = $row["POSTDATE"];
				$this->privacy = $row["PRIVACY"];
				$this->username = $row["USERNAME"];	
				$this->address = $row["ADDRESS"];
				$this->albumUserID = $row["FKUSERID"];
				$this->latitude = $row["LATITUDE"];
				$this->longitude = $row["LONGITUDE"];
				$this->radious = $row["RADIOUS"];
				$this->urllarge = $row["URLLARGE"];
				$this->urlmedium = $row["URLMEDIUM"];
				$this->urlsmall = $row["URLSMALL"];
				$this->urlthumb = $row["URLTHUMB"];
			}
			
			$this->photosize = $this->getPhotoSize();
		}		
	}
	
	public function getAlbumList($sortby=NULL,$limit=NULL)
   	{		
		$sortbystr = "ORDER BY POSTDATE DESC";
		if(isset($sortby)) $sortbystr = "ORDER BY ".$sortby;
		
		$limitstr = "";
		if(isset($limit)) $limitstr = " LIMIT 0, $limit ";
		
		$query = "SELECT PKALBUMID,FKUSERID,NAME,USERNAME,ADDRESS,PRIVACY,POSTDATE FROM TBLALBUM 
		WHERE  PRIVACY='1' 	".$sortbystr." ".$limitstr;		
		
		$result = executeQuery($query);		
		return mysql_fetch_rowsarr($result);
	}
	
	public function getAlbumListAsDistance($sortby=NULL,$limit=NULL,$lat,$lng)
   	{		
		$sortbystr = "ORDER BY POSTDATE DESC";
		if(isset($sortby)) $sortbystr = "ORDER BY ".$sortby;
		
		$limitstr = "";
		if(isset($limit)) $limitstr = " LIMIT 0, $limit ";
		
		//To search by kilometers instead of miles, replace 3959 with 6371. 
		$query = "SELECT PKALBUMID,FKUSERID,NAME,USERNAME,ADDRESS,PRIVACY,POSTDATE,RADIOUS,
		URLLARGE, URLMEDIUM, URLSMALL, URLTHUMB,
		( 3959 * acos( cos( radians(".$lat.") ) * cos( radians( LATITUDE ) ) * cos( radians( LONGITUDE ) - radians(".$lng.") ) + sin( radians(".$lat.") ) * sin( radians( LATITUDE ) ) ) ) AS DISTANCE FROM TBLALBUM 
		WHERE FKPARENTID IS NULL AND   PRIVACY='1' 	".$sortbystr." ".$limitstr;		
		
		$result = executeQuery($query);		
		return mysql_fetch_rowsarr($result);
	}
	
	public function getAllAlbumListNearBy($currentPage,$lat,$lng,$pkAlbumID)
   	{		
		//To search by kilometers instead of miles, replace 3959 with 6371. 
		/*$query = "SELECT PKALBUMID,FKUSERID,FKPARENTID,FKCATEGORYID,NAME,USERNAME,ADDRESS,PRIVACY,POSTDATE,RADIOUS,
		( 3959 * acos( cos( radians(".$lat.") ) * cos( radians( LATITUDE ) ) * cos( radians( LONGITUDE ) - radians(".$lng.") ) + sin( radians(".$lat.") ) * sin( radians( LATITUDE ) ) ) ) AS DISTANCE FROM TBLALBUM 
		WHERE   PRIVACY='1' 	
		ORDER BY DISTANCE,FKCATEGORYID ASC	
		LIMIT ".$currentPage*self::LIST_INTERVAL.",".self::LIST_INTERVAL;   */
		
		$whereClause4PK = "";
		if(isset($pkAlbumID)){
			$whereClause4PK = " AND (b.FKPARENTID='".$pkAlbumID."' OR b.PKALBUMID='".$pkAlbumID."') ";
		}
		
		$totalPhotoSize = $this->getTotalPhotoSize($pkAlbumID);
		
		$query = "SELECT b.PKALBUMID,b.FKUSERID,b.FKPARENTID,b.FKCATEGORYID,b.NAME,b.USERNAME,b.ADDRESS,b.PRIVACY,b.POSTDATE,b.RADIOUS, p.NAME as 'PARENTNAME', (SELECT COUNT(PKPHOTOID) FROM TBLPHOTO WHERE FKALBUMID = b.PKALBUMID) as 'PHOTOSIZE', 
		b.URLLARGE, b.URLMEDIUM, b.URLSMALL, b.URLTHUMB, $totalPhotoSize AS 'TOTALPHOTOSIZE' ,
		( 3959 * acos( cos( radians(".$lat.") ) * cos( radians( b.LATITUDE ) ) * cos( radians( b.LONGITUDE ) - radians(".$lng.") ) + sin( radians(".$lat.") ) * sin( radians( b.LATITUDE ) ) ) ) AS DISTANCE FROM TBLALBUM b LEFT OUTER JOIN TBLALBUM p ON b.FKPARENTID = p.PKALBUMID 
		WHERE   b.PRIVACY='1' AND p.PRIVACY='1' ".$whereClause4PK."  
		ORDER BY DISTANCE,b.FKCATEGORYID ASC	
		LIMIT ".$currentPage*self::LIST_INTERVAL.",".self::LIST_INTERVAL; 
		
		$result = executeQuery($query);		
		return mysql_fetch_rowsarr($result);
	}
	
	public function getParentAlbumListNearBy($currentPage,$lat,$lng)
   	{		
		//we get here total photo size of each album
		//To search by kilometers instead of miles, replace 3959 with 6371. 
		$query = "SELECT b.PKALBUMID,b.FKUSERID,b.FKPARENTID,b.FKCATEGORYID,b.NAME,b.USERNAME,b.ADDRESS,b.PRIVACY,b.POSTDATE,b.RADIOUS, '' as 'PARENTNAME', b.FKPARENTID,
		(SELECT COUNT(p.PKPHOTOID) FROM TBLPHOTO p INNER JOIN TBLALBUM a ON a.PKALBUMID = p.FKALBUMID  WHERE (p.FKALBUMID = a.PKALBUMID OR p.FKALBUMID = a.FKPARENTID) AND (a.PKALBUMID = b.PKALBUMID OR a.FKPARENTID = b.PKALBUMID) ) as 
		'TOTALPHOTOSIZE', 0 as 'PHOTOSIZE',
		b.URLLARGE, b.URLMEDIUM, b.URLSMALL, b.URLTHUMB,
		( 3959 * acos( cos( radians(".$lat.") ) * cos( radians( b.LATITUDE ) ) * cos( radians( b.LONGITUDE ) - radians(".$lng.") ) + sin( radians(".$lat.") ) * sin( radians( b.LATITUDE ) ) ) ) AS DISTANCE FROM TBLALBUM b 
		WHERE   b.PRIVACY='1' AND b.FKPARENTID IS NULL  	
		ORDER BY DISTANCE,b.FKCATEGORYID ASC	
		LIMIT ".$currentPage*self::LIST_INTERVAL.",".self::LIST_INTERVAL; 
		
		$result = executeQuery($query);		
		return mysql_fetch_rowsarr($result);
	}
	
	public function getTotalPhotoSize($pkParentAlbumID){
		$query = "SELECT COUNT(p.PKPHOTOID) AS 'PHOTOSIZE' FROM TBLPHOTO p INNER JOIN TBLALBUM b ON b.PKALBUMID = p.FKALBUMID
		  WHERE b.PKALBUMID = '".$pkParentAlbumID."' OR b.FKPARENTID = '".$pkParentAlbumID."' ";
		$result = executeQuery($query);			
		
		$size = 0;	
		if(mysql_num_rows($result)>0)
		{
			$row = mysql_fetch_array($result);
			$size = $row["PHOTOSIZE"];
		}
		
		return $size;
	}
	
	public function getPhotoSize(){
		$query = "SELECT COUNT(PKPHOTOID) AS 'PHOTOSIZE' FROM TBLPHOTO WHERE FKALBUMID = '".$this->albumID."' ";
		$result = executeQuery($query);			
		
		$size = 0;	
		if(mysql_num_rows($result)>0)
		{
			$row = mysql_fetch_array($result);
			$size = $row["PHOTOSIZE"];
		}
		
		return $size;
	}
	
	public function getAlbumIDFromUsername($usrname,$albumID = NULL)
	{
		$albumIDStr = "";
		
		if(isset($albumID))
			$albumIDStr = " AND PKALBUMID != '".$albumID."' ";
			
		$query = "SELECT PKALBUMID FROM TBLALBUM WHERE USERNAME='".$usrname."' ".$albumIDStr;		
				
		$result = executeQuery($query);		
		if(mysql_num_rows($result)>0){
			$row = mysql_fetch_array($result);
			return $row["PKALBUMID"];
		}
		  
		  
		return NULL;
	}
	
	
	public function getLatestPhotoThumbs($limit = NULL){
		
		$albumstr = "";
		if(isset($this->albumID)) $albumstr = "AND p.FKALBUMID = '".$this->albumID."' ";
		
		$limitstr = "";
		if(isset($limit)) $limitstr = " LIMIT 0, $limit ";
		
		$query = "SELECT p.PKPHOTOID, b.PKALBUMID, 
		CASE WHEN bp.USERNAME IS NOT NULL THEN bp.USERNAME ELSE b.USERNAME END AS 'USERNAME', 
		b.LATITUDE AS 'LAT1', b.LONGITUDE AS 'LONG1', p.LATITUDE AS 'LAT2', p.LONGITUDE AS 'LONG2',
		 p.URLTHUMB, b.NAME as 'ALBUMNAME', b.FKPARENTID FROM TBLPHOTO p
		INNER JOIN TBLALBUM b ON b.PKALBUMID = p.FKALBUMID
		LEFT OUTER JOIN TBLALBUM bp ON b.FKPARENTID = bp.PKALBUMID
		WHERE p.ISACTIVE='1' AND  b.PRIVACY AND p.PRIVACY='1' ".$albumstr."  ORDER BY p.POSTDATE DESC ".$limitstr;
		
		
		$result = executeQuery($query);		
		return mysql_fetch_rowsarr($result);
	}
	
	public function getUniquePhoto($photoID){
	
		$query = "SELECT p.PKPHOTOID, b.PKALBUMID, b.USERNAME, p.FKSUBJECTID, p.FKUSERID, s.TITLE,
		b.LATITUDE AS 'LAT1', b.LONGITUDE AS 'LONG1', p.LATITUDE AS 'LAT2', p.LONGITUDE AS 'LONG2',
		p.FKINSTAGRAMID, p.CONTENTLINK, p.CREATEDDATE, p.POSTDATE, p.FKALBUMID,
		p.FKTWITTERID, p.FKFACEBOOKID, p.URLTHUMB, p.URLMEDIUM, p.URLLARGE, p.URLSMALL, b.USERNAME, p.CAPTION, 
		 p.OWNEDBY, b.NAME as 'ALBUMNAME', 	u.FULLNAME, u.EMAIL, p.SIZELARGE, p.OY 
		  , (SELECT COUNT(FKPHOTOID) FROM TBLOYS WHERE FKPHOTOID = p.PKPHOTOID) AS 'TOTALVOTES'
         , (SELECT COUNT(FKPHOTOID) FROM TBLOYS WHERE FKPHOTOID = p.PKPHOTOID AND OY='-1') AS 'VOTESDOWN'
         , (SELECT COUNT(FKPHOTOID) FROM TBLOYS WHERE FKPHOTOID = p.PKPHOTOID AND OY='1') AS 'VOTESUP'
		FROM TBLPHOTO p INNER JOIN TBLALBUM b ON b.PKALBUMID = p.FKALBUMID
		LEFT OUTER JOIN TBLUSER u ON u.PKUSERID = p.FKUSERID 
		LEFT OUTER JOIN TBLSUBJECT s ON s.PKSUBJECTID = p.FKSUBJECTID		
		WHERE p.PKPHOTOID = '".$photoID."' AND p.ISACTIVE='1' AND p.PRIVACY='1'  ";
						
		$result = executeQuery($query);					
			if(mysql_num_rows($result)>0)
				return mysql_fetch_array($result);
	
		return NULL;
	}
	
	public function getLatestPhotoMediums($rowat=0,$limit=100,$mine=false,$subjectID=NULL,$ismostrated=false){
		
		$albumstr = "";
		if(isset($this->albumID)) $albumstr = "AND p.FKALBUMID = '".$this->albumID."' ";		
		
		$limitstr = "";
		if(isset($limit)) $limitstr = " LIMIT $rowat, $limit ";	
		
		$minestr = "";
		if($mine) $minestr = "AND p.FKUSERID = '".$this->userID."' ";
		
		$subjectstr = "";
		//if($subjectID && !$ismostrated) $subjectstr = " AND p.FKSUBJECTID='".$subjectID."' ";
		if($subjectID && !$ismostrated) $albumstr = " AND p.FKALBUMID='".$subjectID."' ";
		
		
		/*$query = "SELECT p.PKPHOTOID, b.PKALBUMID, p.FKSUBJECTID, p.FKUSERID, s.TITLE,
		b.LATITUDE AS 'LAT1', b.LONGITUDE AS 'LONG1', p.LATITUDE AS 'LAT2', p.LONGITUDE AS 'LONG2',
		p.FKINSTAGRAMID, p.CONTENTLINK, p.CREATEDDATE, p.POSTDATE,
		p.FKTWITTERID, p.FKFACEBOOKID, p.URLTHUMB, p.URLMEDIUM, p.URLLARGE, p.URLSMALL, b.USERNAME, p.CAPTION, 
		 p.OWNEDBY, b.NAME as 'ALBUMNAME', 	u.FULLNAME, u.ISVERIFIED, p.SIZELARGE, p.OY
		 , (SELECT COUNT(PKCOMMENTID) FROM TBLCOMMENT WHERE FKPHOTOID = p.PKPHOTOID) AS 'TOTALCOMMENTS' 
		FROM TBLPHOTO p INNER JOIN TBLALBUM b ON b.PKALBUMID = p.FKALBUMID
		LEFT OUTER JOIN TBLUSER u ON u.PKUSERID = p.FKUSERID 
		LEFT OUTER JOIN TBLSUBJECT s ON s.PKSUBJECTID = p.FKSUBJECTID		
		WHERE p.ISACTIVE='1' AND p.PRIVACY='1' ".$albumstr."  ".$minestr." ".$subjectstr;*/
		
		
		$query = "SELECT p.PKPHOTOID, b.PKALBUMID, p.FKSUBJECTID, p.FKUSERID,
		b.LATITUDE AS 'LAT1', b.LONGITUDE AS 'LONG1', p.LATITUDE AS 'LAT2', p.LONGITUDE AS 'LONG2',
		p.FKINSTAGRAMID, p.CONTENTLINK, p.CREATEDDATE, p.POSTDATE,
		p.FKTWITTERID, p.FKFACEBOOKID, p.URLTHUMB, p.URLMEDIUM, p.URLLARGE, p.URLSMALL, b.USERNAME, p.CAPTION, 
		 p.OWNEDBY, b.NAME as 'ALBUMNAME', p.FKALBUMID, 	u.FULLNAME, u.ISVERIFIED, p.SIZELARGE, p.OY
		 , (SELECT COUNT(PKCOMMENTID) FROM TBLCOMMENT WHERE FKPHOTOID = p.PKPHOTOID) AS 'TOTALCOMMENTS' 
		FROM TBLPHOTO p INNER JOIN TBLALBUM b ON b.PKALBUMID = p.FKALBUMID
		LEFT OUTER JOIN TBLUSER u ON u.PKUSERID = p.FKUSERID 	
		WHERE p.ISACTIVE='1' AND p.PRIVACY='1' ".$albumstr."  ".$minestr." ".$subjectstr;
		
		
		
		$result = executeQuery($query);		
		$this->photosize = mysql_num_rows($result);
		
		if($ismostrated)
			$query .=  " ORDER BY p.OY DESC ".$limitstr;
		else		
			$query .=  " ORDER BY p.POSTDATE DESC ".$limitstr;	
		
									
		$result = executeQuery($query);		
		//return mysql_fetch_assoc($final_result);
		return mysql_fetch_rowsarr($result);
	}
	
	public function getCategories($fkParentID){
		
		$query = "SELECT b.PKALBUMID, b.NAME, 
					(SELECT COUNT(PKPHOTOID) FROM TBLPHOTO WHERE FKALBUMID = b.PKALBUMID) AS 'PHOTOS'  
					 FROM TBLALBUM b 
					WHERE b.PRIVACY='1' AND b.FKPARENTID = '".$fkParentID."' 
		ORDER BY b.NAME ASC";
		$result = executeQuery($query);
		return mysql_fetch_rowsarr($result);
	}
	
	//not used anymore
	public function hasVoted($fkPhotoID,$fkUserID){
	
		$query = "SELECT OY FROM TBLOYS WHERE FKPHOTOID='".$fkPhotoID."' AND FKUSERID='".$fkUserID."' ";
		$result = executeQuery($query);		
 		return mysql_num_rows($result) > 0;
	}
	
	public function createAlbum($albumname,$privacy,$username){	
				
		try{		
			beginTrans(); // transaction begins	
		
			$albumUUID = "-1";
			$albumUUID = UUID::generate(UUID::UUID_RANDOM, UUID::FMT_STRING,"02731468");
			$albumUUID = str_replace("-","",$albumUUID);
			if(strlen($albumUUID) >= 16)
				$albumUUID = substr($albumUUID,0,16);
			
			date_default_timezone_set('America/Chicago');
			$date = date('Y-m-d H:i:s');
					
			$query = "INSERT INTO TBLALBUM (ALBUMUUID,FKUSERID,NAME,USERNAME,PRIVACY,POSTDATE) 
			VALUES('".$albumUUID."','".$this->userID."','".$albumname."','".$username."','".$privacy."','".$date."') ";
		
			$pkAlbumID = executeInsertQueryForTrans($query);
			
			if($albumUUID != "-1" && isset($pkAlbumID))											
			 {
				commitTrans(); // transaction is committed
				return array('success' => true, 'error' => false, 'pkAlbumID' => $pkAlbumID);
				
			 }
			 else
			 {
				rollbackTrans();
				return array('success'	=>	false, 'error' => 'Invalid UUID!', 'pkAlbumID' => $pkAlbumID); 
			 }		
			
		}
		catch(Exception $e){
			rollbackTrans(); // transaction rolls back		
			return array('success'	=>	false, 'error' => 'Invalid Album Addition!', 'pkAlbumID' => '');	
		}
	
	}
	
	public function updateAlbum($albumname,$privacy,$username,$albumID){
		
		try{			
			$query = "UPDATE TBLALBUM 
			SET NAME='".$albumname."', USERNAME='".$username."', PRIVACY='".$privacy."' 
			WHERE PKALBUMID = '".$albumID."' AND FKUSERID='".$this->userID."' ";
			
			$result = executeQuery($query);		
			
			return array('success' => true, 'error' => false, 'pkAlbumID' => $albumID);
		}
		catch(Exception $e){			
			return array('success'	=>	false, 'error' => 'Invalid Album Update!', 'pkAlbumID' => '');	
		}
			
	}
	
	public function deleteAlbum(){
		
		try{		
				beginTrans(); // transaction begins
				$query = "DELETE FROM TBLALBUM WHERE PKALBUMID = '".$this->albumID."' AND FKUSERID='".$this->userID."' ";
				
				$delete = executeQueryForTrans($query);
				
				if($delete > 0)
				{
					commitTrans(); // transaction is committed
					return true;
				}	
				else{
					rollbackTrans(); // transaction rolls back						
				}			
		}
			catch(Exception $e){
				rollbackTrans(); // transaction rolls back		
				return false;
		}
		
		return false;
		
	}
	
	public function deletePhoto($pkPhotoID){
	
		include($_SERVER['DOCUMENT_ROOT']."/Settings.php");
		include($_SERVER['DOCUMENT_ROOT']."/lib/aws/awsfnc.php");
		
		if(isset($pkPhotoID) && $pkPhotoID != ""){		
	
			try{		
				beginTrans(); // transaction begins
				
				$query = "SELECT FKINSTAGRAMID, FKTWITTERID, FKFACEBOOKID, FOLDER, KEYLARGE, KEYMEDIUM, KEYSMALL, KEYTHUMB FROM TBLPHOTO
						  WHERE PKPHOTOID='".$pkPhotoID."' ";
				$result = executeQueryForTrans($query);			
			
				if(mysql_num_rows($result)>0)
				{
					$row = mysql_fetch_array($result);
					
					/*$pic_large = '/'.$imageServer.'/'.$row['PHYSICALPATH']."/".$row['NAMELARGE'];				
					$pic_medium = '/'.$imageServer.'/'.$row['PHYSICALPATH']."/".$row['NAMEMEDIUM'];
					$pic_small = '/'.$imageServer.'/'.$row['PHYSICALPATH']."/".$row['NAMESMALL'];
					$pic_thumb = '/'.$imageServer.'/'.$row['PHYSICALPATH']."/".$row['NAMETHUMB'];	*/
					
					$foldername = $row['FOLDER'];
					$keylarge = $row['KEYLARGE'];	
					$keymedium = $row['KEYMEDIUM'];	
					$keysmall = $row['KEYSMALL'];	
					$keythumb = $row['KEYTHUMB'];	
					$fkinstagramID = $row["FKINSTAGRAMID"];
					$fktwitterID = $row["FKTWITTERID"];
					$fkfacebookID = $row["FKFACEBOOKID"];
					
				
					$query = "DELETE FROM  TBLPHOTO WHERE PKPHOTOID = '".$pkPhotoID."' ";
					$delete = executeQueryForTrans($query);
				
					if($delete > 0)
					{						
						deleteObject($foldername,$keylarge);
						deleteObject($foldername,$keymedium);
						deleteObject($foldername,$keysmall);
						deleteObject($foldername,$keythumb);
						
						/*if(file_exists($pic_large))	 unlink($pic_large);
						if(file_exists($pic_medium)) unlink($pic_medium);
						if(file_exists($pic_small))	 unlink($pic_small);
						if(file_exists($pic_thumb))	 unlink($pic_thumb);*/
						
						if(isset($fkinstagramID)){
							$query = "DELETE FROM TBLINSTAGRAM WHERE PKINSTAGRAMID = '".$fkinstagramID."' ";
						}else if(isset($fktwitterID)){
							$query = "DELETE FROM TBLTWITTER WHERE PKTWITTERID = '".$fktwitterID."' ";
						}else if(isset($fkfacebookID)){
							$query = "DELETE FROM TBLFACEBOOK WHERE PKFACEBOOKID = '".$fkfacebookID."' ";
						}
						
						$delete = executeQueryForTrans($query);
						
						
						$query = "DELETE FROM TBLOYS WHERE FKPHOTOID='".$pkPhotoID."'";
						$delete = executeQueryForTrans($query);
						
						$query = "DELETE FROM TBLCOMMENT WHERE FKPHOTOID='".$pkPhotoID."'";
						$delete = executeQueryForTrans($query);
						
						commitTrans(); // transaction is committed
					
					}
					else{
						rollbackTrans(); // transaction rolls back						
					}
				}
				else
				{
					rollbackTrans();					
				}
				
			}
			catch(Exception $e){
				rollbackTrans(); // transaction rolls back		
				return false;
			}
		}
		
		return true;
	}
	
public function deletePhotos($photos_array){
	
	include($_SERVER['DOCUMENT_ROOT']."/Settings.php");
	include($_SERVER['DOCUMENT_ROOT']."/lib/aws/awsfnc.php");
		
	if(isset($photos_array)){		
	
		try{		
			beginTrans(); // transaction begins
				
			foreach($photos_array as $key => $value)
			{
				$pkPhotoID = $value;
				
				$query = "SELECT FKINSTAGRAMID, FKTWITTERID, FKFACEBOOKID, FOLDER, KEYLARGE, KEYMEDIUM, KEYSMALL, KEYTHUMB FROM TBLPHOTO
						  WHERE PKPHOTOID='".$pkPhotoID."' ";
				$result = executeQueryForTrans($query);			
			
				if(mysql_num_rows($result)>0)
				{
					$row = mysql_fetch_array($result);
					
					/*$pic_large = '/'.$imageServer.'/'.$row['PHYSICALPATH']."/".$row['NAMELARGE'];				
					$pic_medium = '/'.$imageServer.'/'.$row['PHYSICALPATH']."/".$row['NAMEMEDIUM'];
					$pic_small = '/'.$imageServer.'/'.$row['PHYSICALPATH']."/".$row['NAMESMALL'];
					$pic_thumb = '/'.$imageServer.'/'.$row['PHYSICALPATH']."/".$row['NAMETHUMB'];	*/
					
					$foldername = $row['FOLDER'];
					$keylarge = $row['KEYLARGE'];	
					$keymedium = $row['KEYMEDIUM'];	
					$keysmall = $row['KEYSMALL'];	
					$keythumb = $row['KEYTHUMB'];	
					$fkinstagramID = $row["FKINSTAGRAMID"];
					$fktwitterID = $row["FKTWITTERID"];
					$fkfacebookID = $row["FKFACEBOOKID"];
					
				
					$query = "DELETE FROM  TBLPHOTO WHERE PKPHOTOID = '".$pkPhotoID."' ";
					$delete = executeQueryForTrans($query);
				
					if($delete > 0)
					{						
						deleteObject($foldername,$keylarge);
						deleteObject($foldername,$keymedium);
						deleteObject($foldername,$keysmall);
						deleteObject($foldername,$keythumb);
						
						/*if(file_exists($pic_large))	 unlink($pic_large);
						if(file_exists($pic_medium)) unlink($pic_medium);
						if(file_exists($pic_small))	 unlink($pic_small);
						if(file_exists($pic_thumb))	 unlink($pic_thumb);*/
						
						if(isset($fkinstagramID)){
							$query = "DELETE FROM TBLINSTAGRAM WHERE PKINSTAGRAMID = '".$fkinstagramID."' ";
						}else if(isset($fktwitterID)){
							$query = "DELETE FROM TBLTWITTER WHERE PKTWITTERID = '".$fktwitterID."' ";
						}else if(isset($fkfacebookID)){
							$query = "DELETE FROM TBLFACEBOOK WHERE PKFACEBOOKID = '".$fkfacebookID."' ";
						}
						
						$delete = executeQueryForTrans($query);
						
						
						$query = "DELETE FROM TBLOYS WHERE FKPHOTOID='".$pkPhotoID."'";
						$delete = executeQueryForTrans($query);
						
						$query = "DELETE FROM TBLCOMMENT WHERE FKPHOTOID='".$pkPhotoID."'";
						$delete = executeQueryForTrans($query);
						
						
					
					}
					
				}
								
			}//foreach
				
		}//try
		catch(Exception $e){
				rollbackTrans(); // transaction rolls back		
				return false;
		}
	}
	
	commitTrans(); // transaction is committed
		
		return true;
}
	
	public function deletePhotoByUUID($picUUID){
	
		include($_SERVER['DOCUMENT_ROOT']."/Settings.php");
		include($_SERVER['DOCUMENT_ROOT']."/lib/aws/awsfnc.php");
		
		if(isset($picUUID) && $picUUID != ""){		
	
			try{		
				beginTrans(); // transaction begins
				
				$query = "SELECT PKPHOTOID, FKINSTAGRAMID, FKTWITTERID, FKFACEBOOKID, FOLDER, 
							KEYLARGE, KEYMEDIUM, KEYSMALL, KEYTHUMB FROM TBLPHOTO
						  WHERE PHOTOUUID = '".$picUUID."' ";
				$result = executeQueryForTrans($query);			
			
				if(mysql_num_rows($result)>0)
				{
					$row = mysql_fetch_array($result);
					
					/*$pic_large = '/'.$imageServer.'/'.$row['PHYSICALPATH']."/".$row['NAMELARGE'];				
					$pic_medium = '/'.$imageServer.'/'.$row['PHYSICALPATH']."/".$row['NAMEMEDIUM'];
					$pic_small = '/'.$imageServer.'/'.$row['PHYSICALPATH']."/".$row['NAMESMALL'];
					$pic_thumb = '/'.$imageServer.'/'.$row['PHYSICALPATH']."/".$row['NAMETHUMB'];	*/
					
					$pkPhotoID = $row['PKPHOTOID'];
					$foldername = $row['FOLDER'];
					$keylarge = $row['KEYLARGE'];	
					$keymedium = $row['KEYMEDIUM'];	
					$keysmall = $row['KEYSMALL'];	
					$keythumb = $row['KEYTHUMB'];	
					$fkinstagramID = $row["FKINSTAGRAMID"];
					$fktwitterID = $row["FKTWITTERID"];
					$fkfacebookID = $row["FKFACEBOOKID"];
					
				
					$query = "DELETE FROM  TBLPHOTO WHERE PKPHOTOID = '".$pkPhotoID."' ";
					$delete = executeQueryForTrans($query);
				
					if($delete > 0)
					{						
						deleteObject($foldername,$keylarge);
						deleteObject($foldername,$keymedium);
						deleteObject($foldername,$keysmall);
						deleteObject($foldername,$keythumb);
						
						/*if(file_exists($pic_large))	 unlink($pic_large);
						if(file_exists($pic_medium)) unlink($pic_medium);
						if(file_exists($pic_small))	 unlink($pic_small);
						if(file_exists($pic_thumb))	 unlink($pic_thumb);*/
						
						if(isset($fkinstagramID)){
							$query = "DELETE FROM TBLINSTAGRAM WHERE PKINSTAGRAMID = '".$fkinstagramID."' ";
						}else if(isset($fktwitterID)){
							$query = "DELETE FROM TBLTWITTER WHERE PKTWITTERID = '".$fktwitterID."' ";
						}else if(isset($fkfacebookID)){
							$query = "DELETE FROM TBLFACEBOOK WHERE PKFACEBOOKID = '".$fkfacebookID."' ";
						}
						
						$delete = executeQueryForTrans($query);
						
						
						$query = "DELETE FROM TBLOYS WHERE FKPHOTOID='".$pkPhotoID."'";
						$delete = executeQueryForTrans($query);
						
						commitTrans(); // transaction is committed
					
					}
					else{
						rollbackTrans(); // transaction rolls back						
					}
				}
				else
				{
					rollbackTrans();					
				}
				
			}
			catch(Exception $e){
				rollbackTrans(); // transaction rolls back		
				return false;
			}
		}
		
		return true;
	}
	
	function vote($fkUserID,$fkPhotoID,$voteas){		
	
		include($_SERVER['DOCUMENT_ROOT']."/Settings.php");
		require_once($_SERVER['DOCUMENT_ROOT']."/class/PHPMailerAutoload.php");	
		
		if(isset($fkPhotoID) && $fkPhotoID != "" && isset($fkUserID) && $fkUserID != ""){		
	
			try{		
				beginTrans(); // transaction begins
				
				$already = false;
				
				$query = "SELECT OY FROM TBLPHOTO WHERE PKPHOTOID = '".$fkPhotoID."' ";
				$result = executeQueryForTrans($query);
				if(mysql_num_rows($result) > 0)
				{
					$row = mysql_fetch_array($result);
					$oy = $row["OY"];
					$oy = $oy + $voteas;
					
					$query = "SELECT PKOYSID,OY FROM TBLOYS 
					WHERE FKPHOTOID='".$fkPhotoID."' AND FKUSERID='".$fkUserID."' ";
					$result = executeQueryForTrans($query);	
					
					if(mysql_num_rows($result) > 0)
					{
						$row = mysql_fetch_array($result);
						$already = true;
						
						/*if(($row["OY"] == 1 && $voteas == 1) || ($row["OY"] == -1 && $voteas == -1)){
							$already = true;
						}else{							
							$query = "UPDATE TBLOYS SET OY='".$voteas."' WHERE PKOYSID='".$row["PKOYSID"]."' ";							
							executeQueryForTrans($query);			
						}*/
					
					}else{
						
						date_default_timezone_set('America/Chicago');
						$date = date('Y-m-d H:i:s');
							
						//if no record insert one
						$oysUUID = UUID::generate(UUID::UUID_RANDOM, UUID::FMT_STRING,"02431388");
						$oysUUID = str_replace("-","",$oysUUID);
						$oysUUID = strlen($oysUUID) >= 16?substr($oysUUID,0,16):$oysUUID;				
						$query = "INSERT INTO TBLOYS (OYSUUID,FKPHOTOID,FKUSERID,OY,POSTDATE) 
						VALUES('".$oysUUID."','".$fkPhotoID."', '".$fkUserID."','".$voteas."','".$date."') ";				
						$pkOysID = executeInsertQueryForTrans($query);						
					}
					
					if(!$already){
						$query = "UPDATE TBLPHOTO SET OY='".$oy."' WHERE PKPHOTOID = '".$fkPhotoID."' ";
						executeQueryForTrans($query);
						commitTrans();						
						//$oys =  $oy == 0?$oy." oys":($oy < 0?"- ".$oy." oys":"+".$oy." oys");		
						$oys =  $oy <= 0 ?$oy." oys":"+".$oy." oys";	
						
						if($oy <= -3 && $oy >= -4){
							$subject = "New Oy Warning";
							$message = "New oy warning! Post recevied ".$oy." oys<br><br>-----------------------------------------------------------<br><br>Go to post:<br><a href='".$sitepath."/admin/feed.php?photoID=".$fkPhotoID."'>".$sitepath."/admin/feed.php?photoID=".$fkPhotoID."</a>";	
							$mail = new PHPMailer;
							$mail->isSMTP();
							$mail->Host = 'localhost'; 
							$mail->From = $noReplyEmail;
							$mail->FromName = 'Oyvent Warning Notification';
							$mail->addAddress('info@oyvent.com');							
							$mail->addAddress('afranke@oyvent.com');
							$mail->addAddress('ryoung@oyvent.com');				
							$mail->addAddress('adonald@oyvent.com');			
							$mail->addReplyTo($noReplyEmail, 'NOREPLY');
							$mail->isHTML(true);
							$mail->Subject = $subject;
							$mail->Body    = $message;
							$mail->send();
						}
													
						
						return array('success'	=>	true, 'error' => '', 'message' => $oys, 'already' => $already);
					}
					else{
						return array('success'	=>	false, 'error' => '', 'message' => 'You have already voted!', 'already' => $already);
					}
				}
				
				
				/*$query = "SELECT SUM(OY) AS 'OYS' FROM TBLOYS WHERE FKPHOTOID='".$fkPhotoID."'  ";
				$result = executeQueryForTrans($query);					
 				$row = mysql_fetch_array($result);
				$oys = $row["OYS"]>0?"+ ".$row["OYS"]." oys":$row["OYS"]. " oys";*/
				
				
			}
			catch(Exception $e){
				rollbackTrans(); // transaction rolls back		
				return array('success'	=>	false, 'error' => $e->getMessage(), 'message' => 'Invalid oying attempt', 'already' => $already);
			}
		}
		
		
	}
	
}

?>