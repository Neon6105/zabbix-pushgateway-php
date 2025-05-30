<?php

require '../functions.php';

// Convert arbitrary JSON into Zabbix JSON
function main() {
    $params = array();

    // Load data from POST, decode JSON to array
    $postData = file_get_contents("php://input");
    $json = json_decode($postData, true);

    $profile = getJsonProfile();
    $hostKey = $profile['hostKey'];
    $skipKey = $profile['skipKey'];
    $timeKey = $profile['timeKey'];
    $keyPrefix = $profile['keyPrefix'];

    $clock = (new DateTime($json[$timeKey]))->getTimeStamp() ?? (new DateTime('now'))->getTimeStamp();
    // Get host from ?host= first, then try the JSON data
    $host = $_GET['host'] ?? $json[$hostKey];
    if (!$host) {
        die('Host data not found but is required for history.push');
    }

    // Process each key/value pair to send to Zabbix
    foreach ($json as $key=>$val) {
        if (in_array($key, $skipKey)) {
            continue;
        }
        $param = zabbixParamify($host, $keyPrefix . $key, (string)$val, $clock);
        if ($param) {
            $params[] = $param;
        }
    }
    zabbixHistoryPush($params);
}  // end main()


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    main();
} else {
    http_response_code(200);
}

?>
