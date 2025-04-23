<?php

require '../functions.php';

// Custom processing for Sage ENDEC devices
function main() {
    $params = array();

    $postData = file_get_contents("php://input");
    $json = json_decode($postData, true);

    $hostKey = 'facID';
    $skipKey = array('when');
    $timeKey = 'when';
    $keyPrefix = '';
    $msgData = array('srcInput', 'srcName', 'event', 'orig', 'stations');

    $clock = (new DateTime($json[$timeKey]))->getTimeStamp() ?? time();
    // Get host from ?host= first, then try the JSON data
    $host = $_GET['host'] ?? $json[$hostKey];
    if (!$host) {
        die('Host data not found but is required for history.push');
    }

    foreach ($json as $key=>$val) {
        if (in_array($key, $skipKey)) {
            continue;
        }
        if ($key == 'msg') {
            $key = 'msg' . (string)$json['msgType'];
            $val = json_encode($val);
        }
        $param = zabbixParamify($host, $keyPrefix . $key, (string)$val, $clock);
        if ($param) {
            $params[] = $param;
        }
    }

    if ($json['msgType'] == 4) {
        $event = $json['msg']['event'];
        $timeLogged = (new DateTime($json['msg']['timeLogged']))->getTimestamp();

        foreach ($json['msg'] as $key => $value) {
            if (!in_array($key, $msgData)) {
                continue;
            }
            if ($key == 'event') {
                $key = $value;
                $value = date('Y-m-d H:i:s', $timeLogged);
            }
            $param = zabbixParamify($host, $event . $key, (string)$value, $timeLogged);
            if ($param) {
                $params[] = $param;
            }
        }

    }
    zabbixHistoryPush($params);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    main();
} else {
    http_response_code(200);
}

?>
