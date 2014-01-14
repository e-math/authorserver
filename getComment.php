<?php
include_once("database2.php");
include_once("updateClass.php");

class updateController {
    public $db;	

    public function __construct()
    {
        $this->db = new DatabaseManager();
    } 
    public function invoke()
    {
        $invoke_time = date("Y-m-d H:i:s");
        if (isset($_POST['sendPageComment'])){
            if (isset($_POST['courseid'])){
                if($this->db->checkCourseuser($_POST['username'],$_POST['userkey'],$_POST['courseid'])){
                    $version = isset($_POST['version']) ? $_POST['version'] : 'unknown';
                    $isId = $this->db->isAlreadyCourseCommented($_POST['tiddlerName'],$version);
                    if ($isId==-1){
                        $updates = $this->db->sendCoursePageComment($invoke_time,$_POST['username'],$_POST['tiddlerName'],$_POST['tiddlerData'],$_POST['bookId'],$version);
                    }else{
                        $updates = $this->db->updateCoursePageComment($invoke_time,$_POST['tiddlerData'],$isId);
                    }
                    if($updates){
                        $reply="Ok";
                    }else{
                        $reply="Error";
                    }
                }else{
                    $reply="Error";
                }
                include 'reply.php';
            }else{
                $reply="Error";
                include 'reply.php';
            }
        }else{
			$reply="<h1>It Works!</h1>";
            include 'reply.php';
        }
    }
}
?>
