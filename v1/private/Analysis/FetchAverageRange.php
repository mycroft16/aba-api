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

    $hoursWithData = [];
    $averageRange = array(
        "average" => array(),
        "max" => array(),
        "min" => array()
    );

    $student = '';
    if ($_GET['studentId'] != 'All') {
        $student = "`student` = '".$_GET['studentId']."' AND";
    }

    $query = "SELECT * FROM `behaviors` WHERE ".$student." `start` BETWEEN '".$_GET['start']."' AND '".$_GET['end']."' ORDER BY `start` ASC";
    if ($res = $mysql->query($query)) {

        while ($row = $res->fetch_assoc()) {
            $hour = substr($row['start'], 0, 14)."00:00.000Z";
            $hoursWithData[$hour][] = $row['duration'];
        }

        foreach ($hoursWithData as $key => $value) {
            $max = max($value) / 60000;
            $min = min($value) / 60000;
            $avg = (array_sum($value) / count($value)) / 60000;

            $averageRange['average'][] = array($key, $avg);
            $averageRange['max'][] = array($key, $max);
            $averageRange['min'][] = array($key, $min);
        }

        $return = $averageRange;
        http_response_code(200);

    } else {

        http_response_code(500);
        $return['error'] = $mysql->error;

    }

    echo json_encode($return, JSON_HEX_APOS);
    
}
?>