<?php

require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'json.php';
$jsonprofiles = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'jsonprofiles.php';
if (is_file($jsonprofiles)) {
    include $jsonprofiles;
}

function getJsonProfile() {
    global $JSONPROFILE;
    $profile = $_GET['profile'] ?? 'default';
    $hostKey = $JSONPROFILE[$profile]['hostKey'] ?? $JSONPROFILE['default']['hostKey'] ?? 'host';
    $skipKey = $JSONPROFILE[$profile]['skipKey'] ?? $JSONPROFILE['default']['skipKey'] ?? array();
    $timeKey = $JSONPROFILE[$profile]['timeKey'] ?? $JSONPROFILE['default']['timeKey'] ?? 'timeStamp';
    $keyPrefix = $JSONPROFILE[$profile]['keyPrefix'] ?? $JSONPROFILE['default']['keyPrefix'] ?? '';
    return array(
        'hostKey' => $hostKey,
        'skipKey' => $skipKey,
        'timeKey' => $timeKey,
        'keyPrefix' => $keyPrefix
    );
}

?>