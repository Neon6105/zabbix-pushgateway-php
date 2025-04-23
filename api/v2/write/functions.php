<?php

function bStringToArray($bString, $pairby=',', $assigner='=') {
  $newArray = array();
  foreach (explode($pairby, $bString) as $item) {
    [$key, $val] = explode($assigner, $item);
    $newArray[$key] = $val;
  }
  return $newArray;
}

?>