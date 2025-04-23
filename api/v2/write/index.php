<?php

include '../../../functions.php';

function main() {
  $params = array();

  $postData = file_get_contents("php://input");

  if ($_GET['org'] == 'profile' && $_GET['bucket'] == 'measurement') {
    $useMeasurementPrefix = true;
  } else {
    $useMeasurementPrefix = false;
  }
  
  foreach (explode('\n', $postData) as $point) {
    // Skip empty points. Usually when the string ends with '\n'.
    if ($point == '' ) {
      continue;
    }
    // Convert Line Protocol to strings and arrays
    [$metadata, $fieldset, $timestamp] = explode(" ", $point);
    [$measurement, $tags] = explode(",", $metadata, 2);
    $clock = substr($timestamp, 0, 10);
    $ns = substr($timestamp, 10, 4);
    $fieldset = bStringToArray($fieldset);
    $tags = bStringToArray($tags);

    // Should we include "$measurement." as a prefix to all keys?
    if ($useMeasurementPrefix) {
      $keyPrefix = $measurement . '.';
    } else {
      $keyPrefix = '';
    }

    // Get the host
    $host = $tags['host'];
    if (!$host) {
      die('Unable to read host from Influx Line Protocol');
    }

    // Paramify the fieldset
    foreach($fieldset as $key=>$val) {
      $param = zabbixParamify($host, $keyPrefix . $key, $val, $clock, $ns);
      if ($param) {
        $params[] = $param;
      }
    }
  }  // end foreach($point)
  zabbixHistoryPush($params);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  main();
} else {
  echo "influxdb ok";
}

?>