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

    $startHour = ltrim(substr($_GET['start'], 11, 2), '0');
    $startDate = substr($_GET['start'], 0, 11);
    $endDate = substr($_GET['end'], 0, 11);
    $defaultAll = [
        'Access' => 0,
        'Aggression (Physical)' => 0,
        'Aggression (Verbal)' => 0,
        'Attention Seeking (Physical)' => 0,
        'Attention Seeking (Verbal)' => 0,
        'Escape' => 0,
        'Self-Stim' => 0
    ];

    $hoursWithData = [];
    for($i = $startHour; $i <= 23; $i++) {
        $h = $i;
        if ($i < 10) {
            $h = "0".$i;
        }
        $key = $startDate.$h.":00:00.000Z";
        $hoursWithData[$key] = [];
    }
    for($i = 0; $i < $startHour; $i++) {
        $h = $i;
        if ($i < 10) {
            $h = "0".$i;
        }
        $key = $endDate.$h.":00:00.000Z";
        $hoursWithData[$key] = [];
    }
    $return['hwd'] = $hoursWithData;

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

        while($row = $res->fetch_assoc()) {
            $hour = substr($row['start'], 0, 14)."00:00.000Z";
            $hoursWithData[$hour][] = $row['duration'];
        }

    } else {

        http_response_code(500);
        $return['error'] = $mysql->error;

    }

    echo json_encode($return, JSON_HEX_APOS);
    
}
?>