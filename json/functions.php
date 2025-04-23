<?php

function getJsonProfile() {
  global $PROFILE;
  $profile = $_GET['profile'] ?? 'default';
  $hostKey = $PROFILE[$profile]['hostKey'] ?? $PROFILE['default']['hostKey'] ?? 'host';
  $skipKey = $PROFILE[$profile]['skipKey'] ?? $PROFILE['default']['skipKey'] ?? array();
  $timeKey = $PROFILE[$profile]['timeKey'] ?? $PROFILE['default']['timeKey'] ?? 'timeStamp';
  $keyPrefix = $PROFILE[$profile]['keyPrefix'] ?? $PROFILE['default']['keyPrefix'] ?? '';
  return array(
    'hostKey' => $hostKey,
    'skipKey' => $skipKey,
    'timeKey' => $timeKey,
    'keyPrefix' => $keyPrefix
  );
}

?>