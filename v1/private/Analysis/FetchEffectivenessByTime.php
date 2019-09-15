<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, HEAD, OPTIONS, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Authorization, Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Expose-Headers: Access-Control-Allow-Headers, Origin, Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers");

ini_set('display_errors', 1);
error_reporting(E_ALL);

function mapResponseNames($response) {
    $responseNames = [
        'Assistance' => 'as',
        'Change Location' => 'cl',
        'Change Staff' => 'cs',
        'Change Task' => 'ct',
        'Loss of Privileges' => 'lp',
        'Positive Reinforcement' => 'pr',
        'Verbal Redirection' => 'vr'
    ];
    return $responseNames[$response];
}

if ($_SERVER['REQUEST_METHOD'] != "OPTIONS") {

    require_once('../../config.inc');

    $defaultArray = [
        'as' => array('count' => 0, 'total' => 0),
        'cl' => array('count' => 0, 'total' => 0),
        'cs' => array('count' => 0, 'total' => 0),
        'ct' => array('count' => 0, 'total' => 0),
        'lp' => array('count' => 0, 'total' => 0),
        'pr' => array('count' => 0, 'total' => 0),
        'vr' => array('count' => 0, 'total' => 0)
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
            $mappedResponseName = mapResponseNames($row['response']);
            $hour = substr($row['start'], 0, 13).":00:00.000Z";
            if (!isset($hoursWithData[$hour])) {
                $hoursWithData[$hour] = $defaultArray;
            }
            $hoursWithData[$hour][$mappedResponseName]['count'] += ($row['effective'] == 1) ? 1 : 0;
            $hoursWithData[$hour][$mappedResponseName]['total']++;
        }

        foreach($hoursWithData as $key => $value) {
            $values = array(
                'as' => ($value['as']['total'] != 0) ? round(($value['as']['count'] / $value['as']['total']) * 100, 1) : 0,
                'cl' => ($value['cl']['total'] != 0) ? round(($value['cl']['count'] / $value['cl']['total']) * 100, 1) : 0,
                'cs' => ($value['cs']['total'] != 0) ? round(($value['cs']['count'] / $value['cs']['total']) * 100, 1) : 0,
                'ct' => ($value['ct']['total'] != 0) ? round(($value['ct']['count'] / $value['ct']['total']) * 100, 1) : 0,
                'lp' => ($value['lp']['total'] != 0) ? round(($value['lp']['count'] / $value['lp']['total']) * 100, 1) : 0,
                'pr' => ($value['pr']['total'] != 0) ? round(($value['pr']['count'] / $value['pr']['total']) * 100, 1) : 0,
                'vr' => ($value['vr']['total'] != 0) ? round(($value['vr']['count'] / $value['vr']['total']) * 100, 1) : 0
            );
            $return[] = array(
                "date" => $key,
                "values" => $values
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