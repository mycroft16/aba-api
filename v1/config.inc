<?php 
//-- DEFAULT PHP TO USE UTC
date_default_timezone_set('UTC');

define('DB_HOST', 'localhost');
define('DB_USER', 'oneequality_aba_main_user');
define('DB_PASS', 'Admina000509458)(!@');
define('DB_DB', 'oneequality_aba');

$mysql = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_DB);

//-- DEFAULT MYSQLI CONNECTION TIMEZONE TO UTC
$query = "SET time_zone = '+00:00'";
$mysql->query($query);

//-- STRING ENCRYPTION
define('KEY', 'app713d b3havi0ra7 ana7y515');
function encryptIt($string) {
    return hash_hmac('sha256', $string, KEY);
}

//-- GET AUTHORIZATION HEADER INFORMATION
function getAuthToken() {
     return explode("||", base64_decode(str_replace("Bearer ", "", $_SERVER['HTTP_AUTHORIZATION'])));
}
// $userId = 1;
if (isset($_SERVER['HTTP_AUTHORIZATION']) && !empty($_SERVER['HTTP_AUTHORIZATION']) && $_SERVER['HTTP_AUTHORIZATION'] != "") {
    $token = getAuthToken();
    $createdTime    = $token[0];
    $userId         = $token[1];
    $accessType     = $token[2];
}
?>