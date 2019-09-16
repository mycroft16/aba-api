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

    $dataArray = [
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

        $return = [];
        
        while($row = $res->fetch_assoc()) {
            $mappedResponseName = mapResponseNames($row['response']);
            $dataArray[$mappedResponseName]['count'] += ($row['effective'] == 1) ? 1 : 0;
            $dataArray[$mappedResponseName]['total']++;
        }

        $as = ($dataArray['as']['total'] != 0) ? round(($dataArray['as']['count'] / $dataArray['as']['total']) * 100, 1) : 0;
        $cl = ($dataArray['cl']['total'] != 0) ? round(($dataArray['cl']['count'] / $dataArray['cl']['total']) * 100, 1) : 0;
        $cs = ($dataArray['cs']['total'] != 0) ? round(($dataArray['cs']['count'] / $dataArray['cs']['total']) * 100, 1) : 0;
        $ct = ($dataArray['ct']['total'] != 0) ? round(($dataArray['ct']['count'] / $dataArray['ct']['total']) * 100, 1) : 0;
        $lp = ($dataArray['lp']['total'] != 0) ? round(($dataArray['lp']['count'] / $dataArray['lp']['total']) * 100, 1) : 0;
        $pr = ($dataArray['pr']['total'] != 0) ? round(($dataArray['pr']['count'] / $dataArray['pr']['total']) * 100, 1) : 0;
        $vr = ($dataArray['vr']['total'] != 0) ? round(($dataArray['vr']['count'] / $dataArray['vr']['total']) * 100, 1) : 0;
        $return['data'] = array( $as, $cl, $cs, $ct, $lp, $pr, $vr );
        http_response_code(200);

    } else {

        http_response_code(500);
        $return['error'] = $mysql->error;

    }

    echo json_encode($return, JSON_HEX_APOS);
    
}
?>