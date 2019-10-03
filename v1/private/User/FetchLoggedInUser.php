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

    $query = "SELECT * FROM `users` WHERE `id` = '".$userId."'";
    if ($res = $mysql->query($query)) {

        $row = $res->fetch_assoc();
        $row['active'] = ($row['active'] === 1) ? true : false;

        $queryUser = "SELECT `firstName`, `lastName` FROM `users` WHERE `id` = '".$row['createdBy']."'";
        $resUser = $mysql->query($queryUser);
        if ($resUser->num_rows > 0) {
            $rowUser = $resUser->fetch_assoc();
            $row['createdByName'] = $rowUser['firstName']." ".$rowUser['lastName'];
        } else {
            $row['createdByName'] = 'Unknown';
        }

        unset($row['password']);

        http_response_code(200);
        $return = $row;

    } else {

        http_response_code(500);
        $return['error'] = $mysql->error;

    }

    echo json_encode($return, JSON_HEX_APOS);
    
}
?>