<?php
include_once("database.php");
include_once("updateClass.php");

class updateController {
    public $db;	
    private function post_ssl($url, $data, $referer='') 
    {
        // Convert the data array into URL Parameters like a=b&foo=bar etc.
        $data = http_build_query($data);
        // parse the given URL
        $url = parse_url($url);
        if ($url['scheme'] != 'http' && $url['scheme'] != 'https') { 
            die('Error: Only HTTP request are supported !');
        }
        // extract host and path:
        $host = $url['host'];
        $path = $url['path'];
        // open a socket connection on port 80 - timeout: 30 sec
        $fp = fsockopen('ssl://'.$host, 443, $errno, $errstr, 30);
        if ($fp){
            // send the request headers:
            fputs($fp, "POST $path HTTP/1.1\r\n");
            fputs($fp, "Host: $host\r\n");
            if ($referer != '')
                fputs($fp, "Referer: $referer\r\n");
            fputs($fp, "Content-type: application/x-www-form-urlencoded; charset=UTF-8\r\n");
            // fputs($fp, "Content-Type: text/html; charset=UTF-8\r\n");
            fputs($fp, "Content-length: ". strlen($data) ."\r\n");
            fputs($fp, "Connection: close\r\n\r\n");
            fputs($fp, $data);
            $result = ''; 
            while(!feof($fp)) {
                // receive the results of the request
                $result .= fgets($fp, 128);
            }
        }
        else { 
            return array(
                'status' => 'err', 
                'error' => "$errstr ($errno)"
            );
        }
        // close the socket connection:
        fclose($fp);
        // split the result header from the content
        $result = explode("\r\n\r\n", $result, 2);
        $header = isset($result[0]) ? $result[0] : '';
        $content = isset($result[1]) ? $result[1] : '';
        // return as structured array:
        return array(
            'status' => 'ok',
            'header' => $header,
            'content' => $content
        );
    }
    public function __construct()
    {
        $this->db = new DatabaseManager();
    } 
    public function hasIlligalKeys($content,$author)
    {
        //admin functionality
        $systemAuthor = $this->db->getAdminList();
        if (in_array ($author,$systemAuthor,TRUE)){
            return false;
        }

        $illegalKeys = array('systemConfig','&lt;html&gt;');
        foreach( $illegalKeys as $value){
            if(substr_count($content,$value) > 0 ){
                return true;
            }
        }
        return false;
    }
    public function test()
    {
        // test function
    }
    public function invoke()
    {
        if (!(isset($_POST['username'])&& isset($_POST['userkey']))){
            $reply = "Error1";
            include 'reply.php';
            return true;
        }
        if (!($userRole = $this->db->validateUser($_POST['username'],$_POST['userkey']))){
            $reply = "Error2 ";
            include 'reply.php';
            return true;
        }
        $invoke_time = date("Y-m-d H:i:s");
		if (isset($_POST['setContainerlock'])){
            //$lockedBook = $this->db->getLockedBooks(); TODO!
            $lockedBook = array();
            if (in_array ($_POST['bookName'],$lockedBook,TRUE)){
                $reply = "-2";
            }else{
                if ($this->db->checkContainerLock($_POST['author'],$_POST['bookName'],$_POST['setContainerlock'])==0){
                    $reply = $this->db->setContainerLock($_POST['author'],$_POST['bookName'],$_POST['setContainerlock']);
                } else{
                    $reply = "-1";
                }
            }
            include 'reply.php';
        }else if (isset($_POST['removeContainerlock'])){
            if ($_POST['removeContainerlock']=="all"){
                $reply = $this->db->removeContainerLockAll($_POST['container'],$_POST['author'],$_POST['bookName'])? "ok" : "not";
            }else{
                $reply = $this->db->removeContainerLock($_POST['removeContainerlock'])? "ok" : "not";
            }
            include 'reply.php';
        }else if (isset($_POST['type'])){
            if ($_POST['type'] =="12"){
                $reply = $this->db->firstCheckuser($_POST['username'],$_POST['userkey'],$_POST['courseid'])? "OK" : "not";
            }else{
                $reply = "not";
            }
            include 'reply.php';
        }else if (isset($_POST['getPageComments'])){
            $updates = $this->db->getpageComments($_POST['getPageComments'],$_POST['bookId'],$_POST['author']);
            include 'printUpdate.php';        
        }else if (isset($_POST['getCourseComments'])){
            if($this->db->firstCheckuser($_POST['username'],$_POST['userkey'],"coursecomments") == 0)
            {
                $reply = "Login Error";
                include 'reply.php';
                return true;
            }
            $updates = $this->db->getCourseComments($_POST['getCourseComments'],$_POST['bookid'],$_POST['username']);
            include 'printUpdate.php';
        }else if (isset($_POST['getUpdate'])){
            $updates = $this->db->getUpdates($_POST['getUpdate'],$_POST['bookName']);
            $modelsolutionUpdates = $this->db->getModelsolutions($_POST['getUpdate'],$_POST['bookName']);
            $updates = array_merge($updates,$modelsolutionUpdates);
            include 'printUpdate.php';
        }else if (isset($_POST['sendPageComment'])){
                $isId = $this->db->isAlreadyCommented($_POST['tiddlerName']);
                if ($isId==-1){
                    $updates = $this->db->sendPageComment($invoke_time,$_POST['author'],$_POST['tiddlerName'],$_POST['tiddlerData'],$_POST['bookId']);
                }else{
                    $updates = $this->db->updatePageComment($invoke_time,$_POST['tiddlerData'],$isId);              
                }
                if($updates){
                    $reply="Ok";
                }else{
                    $reply="Error3";
                }
                include 'reply.php';
        }else if (isset($_POST['updateCourseMaterial'])){
            if(in_array ($_POST['username'],$this->db->getAdminList(),TRUE)){
                $noSendError = true;
                $mallit = $this->db->getCourseUpdatesTosend($_POST['bookid'],$_POST['elemtype']);
                for($i=0;$i<count($mallit);$i++){
                    $post_data = array(
                            'type' =>'11',
                            'username' => 'imped_user',
                            'userkey' => 'Cbu_gUBE8g6t',
                            'bookid' => $_POST['bookid'],
                            'data' => $mallit[$i]-> data,
                            'updatetype' => $_POST['updatetype'],
                            'lang' => $mallit[$i]-> elemlang
                        );   
                    // Set coursemanagementsystem URL
                    $result = $this -> post_ssl('', $post_data);
                    if($result['status'] != "ok"){
                        $noSendError = false;
                        break;
                    }
                }
                if($noSendError && count($mallit) > 0){
                    $this->db->CourseUpdatesSendOk( $invoke_time , $_POST['bookid'],$_POST['elemtype']);
                }
                if($noSendError){
                    $reply = "Ok";
                    include 'reply.php';
                    return true;
                }else{
                    $reply = "SendError";
                    include 'reply.php';
                    return true;
                }
            }else{
                $reply = "Error4";
                include 'reply.php';
                return true; 
            }
        }else if (isset($_POST['getSystemupdates'])){
            $updates = $this->db->getSystemUpdates($_POST['getSystemupdates']);
            if(in_array ($_POST['username'],$this->db->getAdminList(),TRUE)){
                $adminUpdates = $this->db->getAdminUpdates($_POST['getSystemupdates']);
                $updates =array_merge($updates,$adminUpdates);
            }               
            include 'printUpdate.php';
        }else if (isset($_POST['getContentUpdates'])){
            $updates = $this->db->getContentUpdates($_POST['getContentUpdates'],$_POST['bookName']);
            $modelsolUpdates = $this->db->getModelsolutionsUpdates($_POST['getContentUpdates'],$_POST['bookName']);
            $updates =array_merge($updates,$modelsolUpdates);
            include 'printUpdate.php';
        }else if (isset($_POST['sendUpdate'])){
            if($this->db->checkAuthor($_POST['author'],$_POST['systemUpdate'])){
                $reply = "Error5";
                include 'reply.php';
                return true;
            }

            if ($_POST['systemUpdate'] != 1 && $this->hasIlligalKeys($_POST['tiddlerData'],$_POST['author'])){
                $reply = "Error6";
                include 'reply.php';
                return true;
            }
            if ($_POST['systemUpdate'] == 2){
                $_POST['systemUpdate'] = 1;
            }

            if (isset($_POST['newTiddler'])){
                $newTiddler=1;
            }else{
                $newTiddler=0;
            }
            $isId = $this->db->isAlreadyUpdate($_POST['tiddlerName'],$_POST['bookName'],$_POST['systemUpdate']);
            if(isset($_POST['elemlanguage'])){
                $elemlang  = $_POST['elemlanguage'];
            }else{
                $elemlang  = "";
            }
            $updates = $this->db->setPreUpdate($_POST['author'],$_POST['tiddlerName'],$_POST['tiddlerData'],$_POST['bookName'],$_POST['systemUpdate'],$invoke_time,$newTiddler,$elemlang);
            if ($isId==-1){
                $updates = $this->db->setDirectUpdate($_POST['author'],$_POST['tiddlerName'],$_POST['tiddlerData'],$_POST['bookName'],$_POST['systemUpdate'],$invoke_time,$elemlang);
            }else{
                $updates = $this->db->updateDirectUpdate($_POST['author'],$_POST['tiddlerName'],$_POST['tiddlerData'],$_POST['bookName'],$_POST['systemUpdate'],$invoke_time,$isId,$elemlang);
            }
            if($_POST['systemUpdate']==1 && !isset($_POST['nocourseupdate'])){
                if(isset($_POST['updatetype'])){
                    $courseUpdatetype = $_POST['updatetype'];
                } else{
                    $courseUpdatetype = '2';
                }
                //Set userinformation from coursemanagementsystem username and userkey
                $post_data = array(
                    'type' =>'9',
                    'username' => '',
                    'userkey' => '',
                    'data' => $_POST['tiddlerData'],
                    'updatetype' => $courseUpdatetype
                );
                //Set coursemanagementsystem's URL
                $result = $this -> post_ssl('', $post_data);
            
            }
            if($updates){
                $reply="Ok";
            }else{
                $reply="Error7";
            }
            include 'reply.php';
        }else if (isset($_POST['setnewbook'])){
            //TODO create new book for authoring
            $reply ="Book set as: ". $this->db->setnewEbook($_POST['bookid'],$_POST['description'],$_POST['booktype'],$_POST['langs'],$_POST['author']);
            include 'reply.php';
        }else if (isset($_POST['testi'])){
            //HERE'S CODE TO TEST
            //begin_test
            //end_test
			$reply="<h1>It works!</h1>";
            include 'reply.php';
        }else{
			$reply="<h1>It works!</h1>";
            include 'reply.php';
        }
    }
}
?>