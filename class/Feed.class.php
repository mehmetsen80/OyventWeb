<?php 

 require_once($_SERVER['DOCUMENT_ROOT']."/db/DbConnection.php");
	
	
class Feed
{
	public $username = NULL;
	public $userID = NULL;
	public $feedID = NULL;
	public $feedList =  array();
	public $block_num = 0;
	const LIST_INTERVAL = 100;	
	
	function __construct($username=NULL,$userID=NULL){
		$this->initUser($username,$userID);
	}
	
	public function initUser($username,$userID){
		$this->username = $username;
		$this->userID = $userID;
	}
	
	public function initFeedList($currentPage, $currentTagID, $userID, $lat, $lng, $albumID, $fkParentID)
   	{		
		/*$query  = " SELECT DISTINCT f.PKFEEDID, f.FKUSRID, f.CONTENT, f.POSTDATE, f.PRIVACY, f.ISPARENT, f.FKPARENTID ";		
		$query .= " ,p.PKPICTUREID, p.NAME, p.NAMETHUMB, p.NAMETHUMB2, p.PHYSICALPATH, p.PHYSICALPATHTHUMB, p.PHYSICALPATHTHUMB2, p.TYPE ";
		$query .= " , pr.NAMETHUMB2 as 'USERNAMETHUMB2', pr.PHYSICALPATHTHUMB2 as 'USERPHYSICALPATHTHUMB2' ";
		$query .= " , pr.NAMETHUMB as 'USERNAMETHUMB1', pr.PHYSICALPATHTHUMB as 'USERPHYSICALPATHTHUMB1' ";
		$query .= " , pr.NAME as 'USERNAMETHUMB0', pr.PHYSICALPATH as 'USERPHYSICALPATHTHUMB0' ";		
		$query .= " , usr.FULLNAME, usr.USERNAME "; 		
		$query .= " FROM TBLFEED f ";		
		$query .= " INNER JOIN TBLUPLOADEDPICTURES p ON f.PKFEEDID = p.FKFEEDID ";		
		$query .= " INNER JOIN TBLUSER usr ON f.FKUSRID = usr.PKUSRID ";
		$query .= " LEFT OUTER JOIN TBLUPLOADEDPICTURES pr ON usr.PKUSRID = pr.FKUSRID AND pr.ISPROFILE='1'";
		$query .= " WHERE usr.ISACTIVE = '1' AND  f.PRIVACY = '1' AND f.ISBLOCKED = '0' AND f.ISPARENT = '1' ";
		$query .= " ORDER BY f.POSTDATE DESC ";
 		$query .= " LIMIT ".$currentPage*self::LIST_INTERVAL.",".self::LIST_INTERVAL;  */
		
		
		$albumClause = "";
		$orderClause =  " ORDER BY p.POSTDATE DESC ,DISTANCE ASC ";
		if(isset($albumID) && $albumID > 0){
			if($fkParentID == 0){
				$albumClause = " AND b.FKPARENTID = '".$albumID."' OR p.FKALBUMID = '".$albumID."' "; //bring the parent
			}
			else{	
				$albumClause = " AND p.FKALBUMID = '".$albumID."' "; //bring the children
				$orderClause =  " ORDER BY DISTANCE ASC ";
			}
		}else{//near by
			$orderClause =  " ORDER BY DISTANCE ASC ";
		}
		
		$query = "SELECT p.PKPHOTOID, p.FKALBUMID, p.FKSUBJECTID, p.FKUSERID, s.TITLE,
		b.LATITUDE AS 'LAT1', b.LONGITUDE AS 'LONG1', p.LATITUDE AS 'LAT2', p.LONGITUDE AS 'LONG2',
		p.FKINSTAGRAMID, p.CONTENTLINK, p.CREATEDDATE, p.POSTDATE,
		p.FKTWITTERID, p.FKFACEBOOKID, p.URLTHUMB, p.URLMEDIUM, p.URLLARGE, p.URLSMALL, b.USERNAME, 
		p.CAPTION , 
		 p.OWNEDBY, b.NAME as 'ALBUMNAME', 	u.FULLNAME, u.ISVERIFIED, u.EMAIL, p.SIZELARGE, p.OY
		 , (SELECT COUNT(PKCOMMENTID) FROM TBLCOMMENT WHERE FKPHOTOID = p.PKPHOTOID) AS 'TOTALCOMMENTS' 
		 , (SELECT COUNT(PKOYSID) FROM TBLOYS WHERE FKPHOTOID = p.PKPHOTOID AND FKUSERID = '".$userID."') AS 'HASVOTED'
		 ,( 3959 * acos( cos( radians(".$lat.") ) * cos( radians( p.LATITUDE ) ) * cos( radians( p.LONGITUDE ) - radians(".$lng.") ) + sin( radians(".$lat.") ) * sin( radians( p.LATITUDE ) ) ) ) AS DISTANCE
		 
		FROM TBLPHOTO p LEFT OUTER JOIN TBLALBUM b ON b.PKALBUMID = p.FKALBUMID
		LEFT OUTER JOIN TBLUSER u ON u.PKUSERID = p.FKUSERID 
		LEFT OUTER JOIN TBLSUBJECT s ON s.PKSUBJECTID = p.FKSUBJECTID		
		WHERE p.ISACTIVE='1' AND p.PRIVACY='1' AND p.LATITUDE != '0' AND p.LONGITUDE != '0' ".$albumClause." 
		$orderClause 
		LIMIT ".$currentPage*self::LIST_INTERVAL.",".self::LIST_INTERVAL;   
		
		
		
		
		/*$query = "SELECT p.PKPHOTOID, p.CONTENTLINK, p.CREATEDDATE, p.POSTDATE, p.URLTHUMB, p.URLMEDIUM, 
				p.URLLARGE, p.URLSMALL, p.SIZELARGE  FROM TBLPHOTO p 
				WHERE p.PRIVACY='1' 
				ORDER BY p.POSTDATE DESC 
 				LIMIT ".$currentPage*self::LIST_INTERVAL.",".self::LIST_INTERVAL;  */
		
				
		$result = executeQuery($query);		
		
		if(mysql_num_rows($result)>0)
		{
			$this->feedList = mysql_fetch_rowsarr($result);//multidimensional array
			//$this->feedList = mysql_fetch_array($result);
		}		
   	}
}

?>