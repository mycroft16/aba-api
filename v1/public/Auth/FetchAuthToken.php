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

    $email = trim($_$GET['email']);
    $password = trim($_GET['password']);

    $query = "SELECT * FROM `users` WHERE `email` = '".$email."' AND `password` = '".$password."'";
    if ($res = $mysql->query($query)) {

        if ($res->num_rows > 0) {

            $row = $res->fetch_assoc();
            $authToken = base64_encode(date('Y-m-d\TH:i:s').".000Z||".$row['id']."||".$row['type']);

            http_response_code(200);
            $return['authToken'] = "Bearer ".$authToken;
            $return['expires'] = 86400;

        } else {

            http_response_code(401);
            $return['error'] = 'No record matches username and password';

        }

    } else {

        http_response_code(401);
        $return['error'] = $mysql->error;

    }

    echo json_encode($return, JSON_HEX_APOS);
    
}
?>