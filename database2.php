<?php
include_once("updateClass.php");
class DatabaseManager {

	public function __construct()
	{
	mysql_connect("", "", "") or die(mysql_error());//Set database userinformation (URL,username,password)
	mysql_select_db("") or die(mysql_error());//Select database
	}
    public function checkCourseuser($user,$key,$course){
        function post_request($url, $data, $referer='') {
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
                fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
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
        // Submit those variables to the server
        $post_data = array(
            'type' =>'12',
            'username' => $user,
            'userkey' => $key,
            'courseid' => $course
        );   
        // Send a request to coursemanagementsystem
        $result = post_request('', $post_data);
        //Check if response is ok
        if ($result['status'] == 'ok'){
            if(strrpos ( $result['content'] ,"OK")){
                return true;
            }else{
                return false;
            }
        }
        else {
            return false;
        }

    }
    public function isAlreadyCourseCommented($tiddlerName,$version)
    {
        $res = array();
        $result = mysql_query("SELECT id FROM pageCommentCourse WHERE tiddlerName ='$tiddlerName' AND version ='$version';") or die(mysql_error());
        
		while($row = mysql_fetch_array($result))
		{
				array_push($res, $row['id']);
		}
        if (empty($res)){return -1;}else{return $res[0];}
    }
    public function sendCoursePageComment($sendTime,$author,$tiddlerName,$tiddlerData,$bookName,$version)
    {
        $result = mysql_query("INSERT INTO pageCommentCourse (tiddlerName, tiddlerData, bookId, author, time, version) VALUES ('".addslashes($tiddlerName)."','".addslashes($tiddlerData)."','".addslashes($bookName)."', '".addslashes($author)."', '".$sendTime."', '".$version."');") or die(mysql_error());
        return $result;
    }
    public function updateCoursePageComment($sendTime,$tiddlerData,$id)
    {
        $result = mysql_query("UPDATE pageCommentCourse SET  tiddlerData='".addslashes($tiddlerData)."', time='".$sendTime."' WHERE id=$id;") or die(mysql_error());
        return $result;
    }
}
?>
