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
        'Access' => 'Access',
        'Aggression (Physical)' => 'Aggression (P)',
        'Aggression (Verbal)' => 'Aggression (V)',
        'Attention Seeking (Physical)' => 'Attention (P)',
        'Attention Seeking (Verbal)' => 'Attention (V)',
        'Escape' => 'Escape',
        'Self-Stim' => 'Self-Stim'
    ];
    return $behaviorNames[$behavior];
}

if ($_SERVER['REQUEST_METHOD'] != "OPTIONS") {

    require_once('../../config.inc');

    $emptyResponseArray = array(
        'Assistance' => 0,
        'Change Location' => 0,
        'Change Staff' => 0,
        'Change Task' => 0,
        'Loss of Privileges' => 0,
        'Positive Reinforcement' => 0,
        'Verbal Redirection' => 0
    );

    $dataArray = array(
        "Access" => array(
            "value" => 0,
            "responses" => $emptyResponseArray
        ),
        "Aggression (P)" => array(
            "value" => 0,
            "responses" => $emptyResponseArray
        ),
        "Aggression (V)" => array(
            "value" => 0,
            "responses" => $emptyResponseArray
        ),
        "Attention (P)" => array(
            "value" => 0,
            "responses" => $emptyResponseArray
        ),
        "Attention (V)" => array(
            "value" => 0,
            "responses" => $emptyResponseArray
        ),
        "Escape" => array(
            "value" => 0,
            "responses" => $emptyResponseArray
        ),
        "Self-Stim" => array(
            "value" => 0,
            "responses" => $emptyResponseArray
        )
    );

    $student = '';
    if ($_GET['studentId'] != 'All') {
        $student = "`student` = '".$_GET['studentId']."' AND";
    }

    $query = "SELECT * FROM `behaviors` WHERE ".$student." `start` BETWEEN '".$_GET['start']."' AND '".$_GET['end']."' ORDER BY `start` ASC";
    if ($res = $mysql->query($query)) {

        while($row = $res->fetch_assoc()) {
            $behaviorName = mapBehaviorNames($row['behavior']);
            $dataArray[$behaviorName]['value']++;
            $dataArray[$behaviorName]['responses'][$row['response']]++;
        }
        http_response_code(200);
        $return = $dataArray;

    } else {

        http_response_code(500);
        $return['error'] = $mysql->error;

    }

    echo json_encode($return, JSON_HEX_APOS);
    
}
?>