<?php

require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'csv.php';
$csvprofiles = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'csvprofiles.php';
if (is_file($csvprofiles)) {
    include $csvprofiles;
}

?>