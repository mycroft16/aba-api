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

    $student = '';
    if ($_GET['studentId'] != 'All') {
        $student = "`student` = '".$_GET['studentId']."' AND";
    }

    $behavior = '';
    if ($_GET['behavior'] != 'All') {
        $student = "`behavior` = '".$_GET['behavior']."' AND";
    }

    $query = "SELECT * FROM `behaviors` WHERE ".$student." ".$behavior." `start` BETWEEN '".$_GET['start']."' AND '".$_GET['end']."' ORDER BY `start` ASC";
    if ($res = $mysql->query($query)) {



    } else {

        http_response_code(500);
        $return['error'] = $mysql->error;

    }
    
}
?>