<?php
include_once("updateClass.php");

class DatabaseManager {

	public function __construct()
	{
	mysql_connect("localhost", "databaseuser", "databasepassword") or die(mysql_error());
	// Set correct database user information!
	mysql_select_db("ebook") or die(mysql_error());
	}
    public function clear_containerLock(){       
        $timeLimit = date("Y-m-d H:i:s", strtotime("-1 hour"));
        return mysql_query("DELETE FROM `containerLock` WHERE `lockTime`<'$timeLimit'") or die(mysql_error());
    }
    public function firstCheckuser($username,$userkey,$courseid)
    {
        $result = mysql_query("SELECT id FROM `bookAuthor` WHERE `name`='".addslashes(strtolower($username))."' AND `userkey` = '".addslashes($userkey)."' ") or die(mysql_error());
        $res = array();
		while($row = mysql_fetch_array($result))
		{
				array_push($res, $row['id']);
		}
        if (empty($res)){
            if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
                {
                    $ip=$_SERVER['HTTP_CLIENT_IP'];
                }
            elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
            {
                $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            else
            {
                $ip=$_SERVER['REMOTE_ADDR'];
            }
            $invalidlog = mysql_query("INSERT INTO`loginfail` (username,userkey,info,ip) VALUES ('".addslashes($username)."','".addslashes($userkey)."','".addslashes($courseid)."','".addslashes($ip)."')") or die(mysql_error());
            return 0;
        }else{
            return 1;
        }
    }
    public function checkContainerLock($author,$bookname,$container)
    {
        $this->clear_containerLock();
        $result = mysql_query("SELECT id FROM `containerLock` WHERE `bookName`='$bookname' AND `container` = '$container' AND `Auothor` <> '$author'") or die(mysql_error());
        $res = array();
		while($row = mysql_fetch_array($result))
		{
				array_push($res, $row['id']);
		}
        if (empty($res)){return 0;}else{return 1;}
    }
    public function removeContainerLockAll($container,$author,$bookname)
    {  
        return mysql_query("DELETE FROM `containerLock` WHERE Auothor = '$author' AND container = '$container' AND bookName = '$bookname' LIMIT 1;") or die(mysql_error());
    }
    public function removeContainerLock($containerId)
    {  
        return mysql_query("DELETE FROM `containerLock` WHERE id = $containerId LIMIT 1;") or die(mysql_error());
    }
    public function setContainerLock($author,$bookname,$container)
    {  
        $this->clear_containerLock();
        $result = mysql_query("SELECT id FROM `containerLock` WHERE `bookName`='$bookname' AND `container` = '$container' AND `Auothor` = '$author'") or die(mysql_error());
        $res = array();
		while($row = mysql_fetch_array($result))
		{
				array_push($res, $row['id']);
		}
        if (empty($res)){
            $result = mysql_query("INSERT INTO`containerLock` (Auothor,bookName,container) VALUES ('".addslashes($author)."','".addslashes($bookname)."','".addslashes($container)."')") or die(mysql_error());
            return mysql_insert_id ();
        }else{return $res[0];}
    }
    public function isAlreadySomewhere($tiddler,$bookname)
    {
        $res = array();
		$result = mysql_query("SELECT id FROM `preUpdate` WHERE `tiddlerName`='$tiddler' AND preUpdate.bookName='$bookname' UNION SELECT id FROM `bookUpdates` WHERE `uppdatedTiddler`='$tiddler' AND `bookUpdates`.bookName='$bookname'") or die(mysql_error());
		while($row = mysql_fetch_array($result))
		{
				array_push($res, $row['id']);
		}
        if (empty($res)){return 0;}else{return 1;}
    }
    public function checkcommentAuthor($author)
    {
        $res = array();
		$result = mysql_query("SELECT id FROM bookAuthor WHERE name='".addslashes($author)."';") or die(mysql_error());
		while($row = mysql_fetch_array($result))
		{
				array_push($res, $row['id']);
		}
        if (empty($res)){return true;}else{return false;}
    }
    public function checkAuthor($author,$system)
    {
        
        if($system == 1){
            $systemAuthor = array();
            $result = mysql_query("SELECT name FROM bookAuthor WHERE role=2;") or die(mysql_error());
            while($row = mysql_fetch_array($result))
            {
                    array_push($systemAuthor, $row['name']);
            }
            if (!in_array ($author,$systemAuthor,TRUE)){
                return true;
            }
        }
        $res = array();
		$result = mysql_query("SELECT id FROM bookAuthor WHERE name='".addslashes(strtolower($author))."';") or die(mysql_error());
		while($row = mysql_fetch_array($result))
		{
				array_push($res, $row['id']);
		}
        if (empty($res)){return true;}else{return false;}
    }
    public function validateUser($author,$key)
    {
        $res = array();
		$result = mysql_query("SELECT id,role,userkey FROM bookAuthor WHERE name='".addslashes(strtolower($author))."';") or die(mysql_error());
		while($row = mysql_fetch_array($result,MYSQL_ASSOC))
		{
				array_push($res, array($row['id'], $row['role'], $row['userkey']));
		}
        if (empty($res)){
            if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
                {$ip=$_SERVER['HTTP_CLIENT_IP'];}
            elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
                {$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];}
            else
            {$ip=$_SERVER['REMOTE_ADDR'];}
            $invalidlog = mysql_query("INSERT INTO`loginfail` (username,userkey,info,ip) VALUES ('".addslashes($username)."','".addslashes($userkey)."','".addslashes($courseid)."','".addslashes($ip)."')") or die(mysql_error());
            return false;
        }else{
            if(count($res)>1 || $res[0][2] != $key ){
                if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
                    {$ip=$_SERVER['HTTP_CLIENT_IP'];}
                elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
                    {$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];}
                else
                {$ip=$_SERVER['REMOTE_ADDR'];}
                $invalidlog = mysql_query("INSERT INTO`loginfail` (username,userkey,info,ip) VALUES ('".addslashes($username)."','".addslashes($userkey)."','".addslashes($courseid)."','".addslashes($ip)."')") or die(mysql_error());
                return false;
            }else{
                return $res[0][1];
            }
        }
    }
    public function getAdminList()
    {
        $res = array();
		$result = mysql_query("SELECT name FROM bookAuthor WHERE role=2;") or die(mysql_error());
		while($row = mysql_fetch_array($result))
		{
				array_push($res, $row['name']);
		}
        return $res;
    }
    public function isAlready($tiddler,$author)
    {
        $res = array();
		$result = mysql_query("SELECT id FROM preUpdate WHERE tiddlerName ='$tiddler' AND author='".addslashes($author)."';") or die(mysql_error());
		while($row = mysql_fetch_array($result))
		{
				array_push($res, $row['id']);
		}
        if (empty($res)){return -1;}else{return $res[0];}
    }
    public function isAlreadyCourseCommented($tiddlerName)
    {
        $res = array();
        $result = mysql_query("SELECT id FROM pageCommentCourse WHERE tiddlerName ='$tiddlerName';") or die(mysql_error());
        
		while($row = mysql_fetch_array($result))
		{
				array_push($res, $row['id']);
		}
        if (empty($res)){return -1;}else{return $res[0];}
    }
    public function isAlreadyCommented($tiddlerName)
    {
        $res = array();
        $result = mysql_query("SELECT id FROM pageComment WHERE tiddlerName ='$tiddlerName';") or die(mysql_error());
        
		while($row = mysql_fetch_array($result))
		{
				array_push($res, $row['id']);
		}
        if (empty($res)){return -1;}else{return $res[0];}
    }
    public function isAlreadyUpdate($tiddler,$bookName,$system)
    {
        $res = array();
        if($system == 1 || $system == 4){
               $result = mysql_query("SELECT id FROM bookUpdates WHERE uppdatedTiddler ='$tiddler';") or die(mysql_error());
        }else{
            $result = mysql_query("SELECT id FROM bookUpdates WHERE bookName = '$bookName' AND uppdatedTiddler ='$tiddler';") or die(mysql_error());
        }
		while($row = mysql_fetch_array($result))
		{
				array_push($res, $row['id']);
		}
        if (empty($res)){return -1;}else{return $res[0];}
    }
 	public function getCourseComments($lastUpdatetime,$bookId,$author)
	{
		$res = array();
            if(substr($lastUpdatetime,4,1) != "-"){
                $lastUpdatetime = substr($lastUpdatetime,0,4)."-".substr($lastUpdatetime,4,2)."-".substr($lastUpdatetime,6,2)." ".substr($lastUpdatetime,9,2).":".substr($lastUpdatetime,11,2).":".substr($lastUpdatetime,13,2);
            }
            $result = mysql_query("SELECT * FROM pageCommentCourse WHERE author != '$author' AND bookId = '".$bookId."' AND time > '".$lastUpdatetime."' ORDER BY time") or die(mysql_error());
            while($row = mysql_fetch_array($result))
            {
                    array_push($res, new bookUpdate($row['id'], $row['tiddlerName'], $row['time'], $row['tiddlerData'], $row['bookId'], 0));
            }
		return $res;
	}
 	public function getpageComments($lastUpdatetime,$bookId,$author)
	{
        $bookIdList = explode(",", $bookId);
        $lastUpdatetimeList = explode(",", $lastUpdatetime);
		$res = array();
        for ($i=0;$i<count($bookIdList);$i++){
            if(substr($lastUpdatetimeList[$i],4,1) != "-"){
                $lastUpdatetimeList[$i] = substr($lastUpdatetimeList[$i],0,4)."-".substr($lastUpdatetimeList[$i],4,2)."-".substr($lastUpdatetimeList[$i],6,2)." ".substr($lastUpdatetimeList[$i],9,2).":".substr($lastUpdatetimeList[$i],11,2).":".substr($lastUpdatetimeList[$i],13,2);
            }
            $result = mysql_query("SELECT * FROM pageComment WHERE bookId = '".$bookIdList[$i]."' AND time > '".$lastUpdatetimeList[$i]."' ORDER BY time") or die(mysql_error());
            while($row = mysql_fetch_array($result))
            {
                    array_push($res, new bookUpdate($row['id'], $row['tiddlerName'], $row['time'], $row['tiddlerData'], $row['bookId'], 0));
            }
        }
		return $res;
		
	}
 	public function getUpdates($lastUpdatetime,$bookName)
	{
        $res = array();
        if($lastUpdatetime=="0"){
            $result = mysql_query("SELECT * FROM bookUpdates WHERE systemUpdate = 0 AND bookName = '".$bookName."' ORDER BY times") or die(mysql_error());
        }else{
            if(substr($lastUpdatetime,4,1) != "-"){
                $lastUpdatetime = substr($lastUpdatetime,0,4)."-".substr($lastUpdatetime,4,2)."-".substr($lastUpdatetime,6,2)." ".substr($lastUpdatetime,9,2).":".substr($lastUpdatetime,11,2).":".substr($lastUpdatetime,13,2);
            }
		
            $result = mysql_query("SELECT * FROM bookUpdates WHERE systemUpdate = 0 AND bookName = '".$bookName."' AND times > '".$lastUpdatetime."' ORDER BY times") or die(mysql_error());
        }
		while($row = mysql_fetch_array($result))
		{
				array_push($res, new bookUpdate($row['id'], $row['uppdatedTiddler'], $row['times'], $row['data'], $row['bookName'], $row['systemUpdate']));
		}
		return $res;
		
	}
 	public function getModelsolutions($lastUpdatetime,$bookName)
	{
        $res = array();
        if($lastUpdatetime=="0"){
            $result = mysql_query("SELECT * FROM bookUpdates WHERE systemUpdate = 3 AND bookName = '".$bookName."' ORDER BY times") or die(mysql_error());
        }else{
            if(substr($lastUpdatetime,4,1) != "-"){
                $lastUpdatetime = substr($lastUpdatetime,0,4)."-".substr($lastUpdatetime,4,2)."-".substr($lastUpdatetime,6,2)." ".substr($lastUpdatetime,9,2).":".substr($lastUpdatetime,11,2).":".substr($lastUpdatetime,13,2);
            }
		
            $result = mysql_query("SELECT * FROM bookUpdates WHERE systemUpdate = 3 AND bookName = '".$bookName."' AND times > '".$lastUpdatetime."' ORDER BY times") or die(mysql_error());
        }
		while($row = mysql_fetch_array($result))
		{
				array_push($res, new bookUpdate($row['id'], $row['uppdatedTiddler'], $row['times'], $row['data'], $row['bookName'], $row['systemUpdate']));
		}
		return $res;
		
	}
 	public function CourseUpdatesSendOk($sendTime, $bookName, $type)
	{
        $lastSend = mysql_query("SELECT id FROM CourseUpdatesSend WHERE type = ".$type." AND bookName = '".$bookName."';") or die(mysql_error());
        $resNro = mysql_num_rows($lastSend);
        if($resNro == 0){
            $result = mysql_query("INSERT INTO CourseUpdatesSend (lastSend , BookName ,type ) VALUES ('".$sendTime."','".$bookName."',".$type.");") or die(mysql_error());
        }else{
            $dbId = mysql_fetch_array($lastSend);
            $result = mysql_query("UPDATE CourseUpdatesSend SET lastSend = '".$sendTime."' WHERE id = ".$dbId['id'].";") or die(mysql_error());
        }
    }
    public function getCourseUpdatesTosend($bookName , $type)
	{
        $lastSend = mysql_query("SELECT lastSend FROM CourseUpdatesSend WHERE type = ".$type." AND bookName = '".$bookName."';") or die(mysql_error());
        $resNro = mysql_num_rows($lastSend);
        $res = array();
        if($resNro == 0){
            $result = mysql_query("SELECT * FROM bookUpdates WHERE systemUpdate = ".$type." AND bookName = '".$bookName."' ORDER BY times;") or die(mysql_error());
        }else{
            $lastUpdatetime = mysql_fetch_array($lastSend);
            $lastUpdatetime = $lastUpdatetime['lastSend'];
            $result = mysql_query("SELECT * FROM bookUpdates WHERE systemUpdate = ".$type." AND bookName = '".$bookName."' AND times > '".$lastUpdatetime."' ORDER BY times;") or die(mysql_error());
        }
		while($row = mysql_fetch_array($result))
		{
            $oneUpdate = new bookUpdate($row['id'], $row['uppdatedTiddler'], $row['times'], $row['data'], $row['bookName'], $row['systemUpdate']);
            $oneUpdate -> elemlang = $row['language'];
				array_push($res, $oneUpdate );
		}
		return $res;
		
	}

 	public function getContentUpdates($lastUpdatetime,$bookName)
	{
    
        $booklist = explode(",",$bookName);
        $checksList = explode(",",$lastUpdatetime);
		$res = array();
		$queryCondition = array(); 
        for($i = 0; $i < count($booklist); $i++) {
            array_push($queryCondition , " (bookName = '".$booklist[$i].($checksList[$i] =='0'? '':"' AND times > '".$checksList[$i])."' )");
        }
        $queryStr = "SELECT * FROM bookUpdates WHERE systemUpdate = 0 AND (".implode(' OR ', $queryCondition).")  ORDER BY times";
		$result = mysql_query($queryStr) or die(mysql_error());
		while($row = mysql_fetch_array($result))
		{
				array_push($res, new bookUpdate($row['id'], $row['uppdatedTiddler'], $row['times'], $row['data'], $row['bookName'], $row['systemUpdate']));
		}
		return $res;
		
	}
 	public function getModelsolutionsUpdates($lastUpdatetime,$bookName)
	{
    
        $booklist = explode(",",$bookName);
        $checksList = explode(",",$lastUpdatetime);
		$res = array();
		$queryCondition = array(); 
        for($i = 0; $i < count($booklist); $i++) {
            array_push($queryCondition , " (bookName = '".$booklist[$i].($checksList[$i] =='0'? '':"' AND times > '".$checksList[$i])."' )");
        }
        $queryStr = "SELECT * FROM bookUpdates WHERE systemUpdate = 3 AND (".implode(' OR ', $queryCondition).")  ORDER BY times";
		$result = mysql_query($queryStr) or die(mysql_error());
		while($row = mysql_fetch_array($result))
		{
				array_push($res, new bookUpdate($row['id'], $row['uppdatedTiddler'], $row['times'], $row['data'], $row['bookName'], $row['systemUpdate']));
		}
		return $res;
		
	}
    public function setPreUpdate($author,$tiddlerName,$tiddlerData,$bookName,$systemUpdate,$sendTime,$newTiddler,$elemlang)
    {
        $result = mysql_query("INSERT INTO preUpdate (author, tiddlerName, tiddlerData, bookName, systemUpdate, sendTime, newTiddler,language) VALUES ('".addslashes($author)."','".addslashes($tiddlerName)."','".addslashes($tiddlerData)."','".addslashes($bookName)."', '$systemUpdate', '".$sendTime."','$newTiddler' ,'$elemlang');") or die(mysql_error());
        return $result;
    }
        public function setnewEbook($bookid,$description,$type,$langs,$creator)
    {
        
        $result = mysql_query("INSERT INTO ebooks (bookid, description, type, langs, creator) VALUES ('".addslashes($bookid)."','".addslashes($description)."',".addslashes($type).",'".addslashes($langs)."','".addslashes($creator)."');") or die(mysql_error());
        return $result;
    }
    public function sendCoursePageComment($sendTime,$author,$tiddlerName,$tiddlerData,$bookName)
    {
        $result = mysql_query("INSERT INTO pageCommentCourse (tiddlerName, tiddlerData, bookId, author, time) VALUES ('".addslashes($tiddlerName)."','".addslashes($tiddlerData)."','".addslashes($bookName)."', '".addslashes($author)."', '".$sendTime."');") or die(mysql_error());
        return $result;
    }
    public function sendPageComment($sendTime,$author,$tiddlerName,$tiddlerData,$bookName)
    {
        $result = mysql_query("INSERT INTO pageComment (tiddlerName, tiddlerData, bookId, author, time) VALUES ('".addslashes($tiddlerName)."','".addslashes($tiddlerData)."','".addslashes($bookName)."', '".addslashes($author)."', '".$sendTime."');") or die(mysql_error());
        return $result;
    }
    
    public function updateCoursePageComment($sendTime,$tiddlerData,$id)
    {
        $result = mysql_query("UPDATE pageCommentCourse SET  tiddlerData='".addslashes($tiddlerData)."', time='".$sendTime."' WHERE id=$id;") or die(mysql_error());
        return $result;
    }
    public function updatePageComment($sendTime,$tiddlerData,$id)
    {
        $result = mysql_query("UPDATE pageComment SET  tiddlerData='".addslashes($tiddlerData)."', time='".$sendTime."' WHERE id=$id;") or die(mysql_error());
        return $result;
    }
 
 
 
    public function setDirectUpdate($author,$tiddlerName,$tiddlerData,$bookName,$systemUpdate,$sendTime,$elemlang)
    {
        if($elemlang !=""){
            $result = mysql_query("INSERT INTO bookUpdates (uppdatedTiddler, data, bookName, systemUpdate, times , language) VALUES ('".addslashes($tiddlerName)."','".addslashes($tiddlerData)."','".addslashes($bookName)."', '$systemUpdate', '".$sendTime."','$elemlang');") or die(mysql_error());
        }else{
            $result = mysql_query("INSERT INTO bookUpdates (uppdatedTiddler, data, bookName, systemUpdate, times) VALUES ('".addslashes($tiddlerName)."','".addslashes($tiddlerData)."','".addslashes($bookName)."', '$systemUpdate', '".$sendTime."');") or die(mysql_error());
        }
        return $result;
    }
 
    public function updatePreUpdate($author,$tiddlerName,$tiddlerData,$bookName,$systemUpdate,$sendTime,$id,$elemlang)
    {
        if($elemlang !=""){
            $result = mysql_query("UPDATE preUpdate SET newTiddler=0, author='".addslashes($author)."', tiddlerName='".addslashes($tiddlerName)."', tiddlerData='".addslashes($tiddlerData)."', bookName='".addslashes($bookName)."', systemUpdate='$systemUpdate', sendTime='".$sendTime."' , language ='".addslashes($elemlang)."' WHERE id=$id;") or die(mysql_error());
        }else{
            $result = mysql_query("UPDATE preUpdate SET newTiddler=0, author='".addslashes($author)."', tiddlerName='".addslashes($tiddlerName)."', tiddlerData='".addslashes($tiddlerData)."', bookName='".addslashes($bookName)."', systemUpdate='$systemUpdate', sendTime='".$sendTime."' , language = NULL WHERE id=$id;") or die(mysql_error());
        }
        return $result;
    }
 
    public function updateDirectUpdate($author,$tiddlerName,$tiddlerData,$bookName,$systemUpdate,$sendTime,$id,$elemlang)
    {
        if($elemlang !=""){
            $result = mysql_query("UPDATE bookUpdates SET  uppdatedTiddler='".addslashes($tiddlerName)."', data='".addslashes($tiddlerData)."', bookName='".addslashes($bookName)."', systemUpdate='$systemUpdate', times='".$sendTime."', language ='".addslashes($elemlang)."' WHERE id=$id;") or die(mysql_error());
        }else{
            $result = mysql_query("UPDATE bookUpdates SET  uppdatedTiddler='".addslashes($tiddlerName)."', data='".addslashes($tiddlerData)."', bookName='".addslashes($bookName)."', systemUpdate='$systemUpdate', times='".$sendTime."' WHERE id=$id;") or die(mysql_error());
        }
        return $result;
    }
 
	public function getAdminUpdates($lastUpdatetime)
	{
		$res = array();
        if(intval($lastUpdatetime) == 0){
            $result = mysql_query("SELECT * FROM bookUpdates WHERE systemUpdate = 4 ORDER BY times") or die(mysql_error());
        }else{
            if(substr($lastUpdatetime,4,1) != "-"){
                $lastUpdatetime = substr($lastUpdatetime,0,4)."-".substr($lastUpdatetime,4,2)."-".substr($lastUpdatetime,6,2)." ".substr($lastUpdatetime,9,2).":".substr($lastUpdatetime,11,2).":".substr($lastUpdatetime,13,2);
            }
            $result = mysql_query("SELECT * FROM bookUpdates WHERE systemUpdate = 4 AND times > '".$lastUpdatetime."' ORDER BY times") or die(mysql_error());
        }
		while($row = mysql_fetch_array($result))
		{
				array_push($res, new bookUpdate($row['id'], $row['uppdatedTiddler'], $row['times'], $row['data'], $row['bookName'], $row['systemUpdate']));
		}
		return $res;
		
	}
		
	public function getSystemUpdates($lastUpdatetime)
	{
		$res = array();
        if(substr($lastUpdatetime,4,1) != "-"){
            $lastUpdatetime = substr($lastUpdatetime,0,4)."-".substr($lastUpdatetime,4,2)."-".substr($lastUpdatetime,6,2)." ".substr($lastUpdatetime,9,2).":".substr($lastUpdatetime,11,2).":".substr($lastUpdatetime,13,2);
        }
		$result = mysql_query("SELECT * FROM bookUpdates WHERE systemUpdate = 1 AND times > '".$lastUpdatetime."' ORDER BY times") or die(mysql_error());
		while($row = mysql_fetch_array($result))
		{
				array_push($res, new bookUpdate($row['id'], $row['uppdatedTiddler'], $row['times'], $row['data'], $row['bookName'], $row['systemUpdate']));
		}
		return $res;
	}
}
?>
