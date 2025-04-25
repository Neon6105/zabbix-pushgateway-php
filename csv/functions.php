<?php

require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'csv.php';
$csvprofiles = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'csvprofiles.php';
if (is_file($csvprofiles)) {
    include $csvprofiles;
}

function getCsvProfile() {
    global $CSVPROFILE;
    $profile = $_GET['profile'] ?? 'default';
    $hasHeader = $CSVPROFILE[$profile]['hasHeader'] ?? $CSVPROFILE['default']['hasHeader'] ?? true;
    $useHeader = $CSVPROFILE[$profile]['useHeader'] ?? $CSVPROFILE['default']['useHeader'] ?? array();
    $hostColumn = $CSVPROFILE[$profile]['hostColumn'] ?? $CSVPROFILE['default']['hostColumn'] ?? 'host';
    $timeColumn = $CSVPROFILE[$profile]['timeColumn'] ?? $CSVPROFILE['default']['timeColumn'] ?? 'time';
    $skipColumn = $CSVPROFILE[$profile]['skipColumn'] ?? $CSVPROFILE['default']['skipColumn'] ?? array('host', 'time');
    return array(
        'hasHeader'  => $hasHeader,
        'useHeader'  => $useHeader,
        'hostColumn' => $hostColumn,
        'timeColumn' => $timeColumn,
        'skipColumn' => $skipColumn
    );
}

?>