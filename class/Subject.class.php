<?php 

require_once($_SERVER['DOCUMENT_ROOT']."/db/DbConnection.php");

class Subject{

	public $pkSubjectID = NULL;
	public $fkAlbumID = NULL;
	public $title = NULL;
	public $postdate = NULL;
	public $privacy = NULL;	

	function __construct($pkSubjectID=NULL){
		
		if(isset($pkSubjectID)){
			$this->pkSubjectID = $pkSubjectID;
			$query = "SELECT PKSUBJECTID,FKALBUMID,TITLE,POSTDATE,PRIVACY 
			FROM TBLSUBJECT WHERE PKSUBJECTID=".$pkSubjectID;
			$result = executeQuery($query);			
				
			if(mysql_num_rows($result)>0)
			{
				$row = mysql_fetch_array($result);
				$this->pkSubjectID = $row["PKSUBJECTID"];
				$this->fkAlbumID = $row["FKALBUMID"];
				$this->title = real_escape_string($row["TITLE"]);
				$this->title = stripslashes($this->title);
				$this->postdate = $row["POSTDATE"];
				$this->privacy = $row["PRIVACY"];
			}
		}
	}
	
	public function getDefaultSubject($fkAlbumID){
		$query = "SELECT PKSUBJECTID,FKALBUMID,TITLE,POSTDATE,PRIVACY,ISDEFAULT 
		FROM TBLSUBJECT WHERE PRIVACY = '1' AND FKALBUMID=$fkAlbumID  AND ISDEFAULT='1' AND FKPARENTID IS NULL "; 
		$result = executeQuery($query);			
		
		$firstrow = NULL;	
		if(mysql_num_rows($result)>0)
		{
			$firstrow = mysql_fetch_assoc($result);
		}
		return $firstrow;
	}
	
	public function getDefaultSubjects($fkAlbumID){
		$query = "SELECT PKSUBJECTID,FKALBUMID,TITLE,POSTDATE,PRIVACY,ISDEFAULT 
		FROM TBLSUBJECT WHERE PRIVACY = '1' AND FKALBUMID=$fkAlbumID  AND ISDEFAULT='1' AND FKPARENTID IS NULL "; 
		$result = executeQuery($query);		
		return 	 mysql_fetch_rowsarr($result);		
	}
	
	public function getSubCategories($fkAlbumID,$fkParentID){				
		$query = "SELECT PKSUBJECTID,FKALBUMID,TITLE,POSTDATE,PRIVACY FROM TBLSUBJECT 
		WHERE FKALBUMID=$fkAlbumID AND PRIVACY='1' AND FKPARENTID=$fkParentID AND ISDEFAULT != '1' 
		ORDER BY  TITLE ASC ";
		$result = executeQuery($query);	
		return 	 mysql_fetch_rowsarr($result);		
	}
	
	public function getCategories($fkAlbumID){
		$query = "SELECT PKSUBJECTID,FKALBUMID,TITLE,POSTDATE,PRIVACY FROM TBLSUBJECT 
		WHERE FKALBUMID=$fkAlbumID AND PRIVACY='1' AND FKPARENTID IS NULL AND ISDEFAULT != '1' 
		ORDER BY TITLE ASC ";
		$result = executeQuery($query);	
		return 	 mysql_fetch_rowsarr($result);
	}
}

?>