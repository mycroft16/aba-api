<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, HEAD, OPTIONS, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Authorization, Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Expose-Headers: Access-Control-Allow-Headers, Origin, Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers");

ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] != "OPTIONS") {

    require_once('../../config.inc');

    $post = json_decode(file_get_contents('php://input'));

    $insert = "INSERT INTO `behaviors`
    SET
        `behavior`  = '".$mysql->real_escape_string(trim($post->behavior))."',
        `duration`  = '".trim($post->duration)."',
        `effective` = '".$post->effective."',
        `end`       = '".trim($post->end)."',
        `start`     = '".trim($post->start)."',
        `student`   = '".trim($post->studentId)."',
        `response`  = '".$mysql->real_escape_string(trim($post->response))."',
        `user`      = '".$userId."'";
    if ($mysql->query($insert)) {

        http_response_code(200);
        $return = true;

    } else {

        http_response_code(500);
        $return['error'] = $mysql->error;

    }

    echo json_encode($return, JSON_HEX_APOS);
    
}
?>