<?php

require '../functions.php';

function main() {
    $params = array();

    $postData = file_get_contents("php://input");
    $handle = fopen('php://memory', 'r+');
    fwrite($handle, $postData);
    rewind($handle);

    $profile = getCsvProfile();
    $hasHeader = $profile['hasHeader'];
    $hostColumn = $profile['hostColumn'];
    $skipColumn = $profile['skipColumn'];
    $timeColumn = $profile['timeColumn'];

    $rows = [];
    while ($hasHeader) {
        $headerRow = fgetcsv($handle);
        if (!empty($headerRow[0])) {
            $hasHeader = false;
        }
    }

    if (!$hasHeader && !isset($headerRow)) {
        $headerRow = $profile['useHeader'];
    }

    while (($data = fgetcsv($handle)) !== FALSE) {
        $rows[] = array_combine($headerRow, $data);
    }

    foreach ($rows as $row) {
        // Housekeeping: trim whitespace
        foreach ($row as $key=>$val) {
            unset($row[$key]);
            $row[trim($key)] = trim($val);
        }
        $clock = (new DateTime($row[$timeColumn]))->getTimeStamp() ?? (new DateTime('now'))->getTimeStamp();
        $host = $row[$hostColumn];
        
        foreach ($row as $col=>$val) {
            if (in_array($col, $skipColumn)) {
                continue;
            }
            $param = zabbixParamify($host, $col, (string)$val, $clock);
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