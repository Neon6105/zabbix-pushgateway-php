<?php
$PROFILE['isotime'] = array(
  "host_tag"=>"hostname",
  "key_prefix"=>"isotime.",
  "skip_keys"=>array("when"),
  "time_key"=>"when",
  "time_transform"=>function ($val) {
    $date = new DateTime($val);
    return $date->getTimestamp();
  }
);
?>