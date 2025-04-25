<?php

require '../functions.php';

function main() {
    // Empty array for zabbixHistoryPush($params)
    $params = array();

    // Read data from POST, load CSV to memory
    $postData = file_get_contents("php://input");
    $handle = fopen('php://memory', 'r+');
    fwrite($handle, $postData);
    rewind($handle);

    // ?profile= or use 'default'
    $profile = getCsvProfile();
    $hasHeader = $profile['hasHeader'];
    $hostColumn = $profile['hostColumn'];
    $skipColumn = $profile['skipColumn'];
    $timeColumn = $profile['timeColumn'];

    // Prep to convert CSV into Associative Array
    $rows = [];

    // Get the header row, ignoring blank lines
    while ($hasHeader) {
        $headerRow = fgetcsv($handle);
        if (!empty($headerRow[0])) {
            $hasHeader = false;
        }
    }

    // Fallback to 'useHeader' array from the profile
    if (!$hasHeader && !isset($headerRow)) {
        $headerRow = $profile['useHeader'];
    }

    // Loop through non-blank lines and add them to the Associative Array
    while (($data = fgetcsv($handle)) !== FALSE) {
        $rows[] = array_combine($headerRow, $data);
    }

    // Loop through each "row" of the array
    foreach ($rows as $row) {
        // Housekeeping: trim whitespace
        foreach ($row as $key=>$val) {
            unset($row[$key]);
            $row[trim($key)] = trim($val);
        }
        // Set clock and hostname for each row
        $clock = (new DateTime($row[$timeColumn]))->getTimeStamp() ?? (new DateTime('now'))->getTimeStamp();
        // Get host from ?host= first, then try the CSV data
        $host = $_GET['host'] ?? $row[$hostColumn];
        if (!$host) {
            die('Host data not found but is required for history.push');
        }
        
        // Process each key/value pair to send to Zabbix
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