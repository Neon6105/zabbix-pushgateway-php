<?php
$PROFILE['isotime'] = array(
  "host_key"=>"hostname",
  "key_prefix"=>"isotime.",
  "skip_keys"=>array("when"),
  "time_key"=>"when",
  "time"=>function ($val) {
    $date = new DateTime($val);
    return $date->getTimestamp();
  }
);
?>