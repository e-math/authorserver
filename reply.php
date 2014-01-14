<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: X-Requested-With");
    header("Content-Type: text/html; charset=utf-8");
    print $reply;
?>