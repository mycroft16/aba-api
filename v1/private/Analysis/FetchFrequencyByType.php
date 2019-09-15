<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, HEAD, OPTIONS, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Authorization, Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Expose-Headers: Access-Control-Allow-Headers, Origin, Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers");

ini_set('display_errors', 1);
error_reporting(E_ALL);

function mapBehaviorNames($behavior) {
    $behaviorNames = [
        'Access' => 'ac',
        'Aggression (Physical)' => 'agp',
        'Aggression (Verbal)' => 'agv',
        'Attention Seeking (Physical)' => 'atp',
        'Attention Seeking (Verbal)' => 'atv',
        'Escape' => 'es',
        'Self-Stim' => 'ss'
    ];
    return $behaviorNames[$behavior];
}

if ($_SERVER['REQUEST_METHOD'] != "OPTIONS") {

    require_once('../../config.inc');

    $defaultArray = [
        'ac' => 0,
        'agp' => 0,
        'agv' => 0,
        'atp' => 0,
        'atv' => 0,
        'es' => 0,
        'ss' => 0
    ];

    $student = '';
    if ($_GET['studentId'] != 'All') {
        $student = "`student` = '".$_GET['studentId']."' AND";
    }

    $query = "SELECT * FROM `behaviors` WHERE ".$student." `start` BETWEEN '".$_GET['start']."' AND '".$_GET['end']."' ORDER BY `start` ASC";
    if ($res = $mysql->query($query)) {

        $hoursWithData = [];
        $return = [];

        while($row = $res->fetch_assoc()) {
            $mappedBehaviorName = mapBehaviorNames($row['behavior']);
            $hour = substr($row['start'], 0, 13).":00:00.000Z";
            if (!isset($hoursWithData[$hour])) {
                $hoursWithData[$hour] = $defaultArray;
            }
            $hoursWithData[$hour][$mappedBehaviorName]++;
        }

        foreach($hoursWithData as $key => $value) {
            $return[] = array(
                "date" => $key,
                "values" => $value
            );
        }
        http_response_code(200);

    } else {

        http_response_code(500);
        $return['error'] = $mysql->error;

    }

    echo json_encode($return, JSON_HEX_APOS);
    
}
?>