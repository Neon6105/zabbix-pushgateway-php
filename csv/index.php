<?php

require '../functions.php';

function main() {
    $params = array();

    $postData = file_get_contents("php://input");
    $handle = fopen('php://memory', 'r+');
    fwrite($handle, $postData);
    rewind($handle);

    //$header_row = fgetcsv($handle);
    while (($data = fgetcsv($handle)) !== FALSE) {
        // Use the header row or csvprofile to zabbixParamify() each line
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    main();
} else {
    http_response_code(200);
}

?>