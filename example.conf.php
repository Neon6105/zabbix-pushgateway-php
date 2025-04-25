<?php

//Required: Full URL to the Zabbix 7.x API Gateway
$ZPG['apiURL'] = 'http://127.0.0.1/zabbix/api_jsonrpc.php';

//Required: API Token (created in Zabbix Web UI)
$ZPG['apiToken'] = '';

//Future: Universal key prefix for all items collected via this gateway.
$ZPG['universalPrefix'] = '';

//Optional: Set fallback defaults for JSON profile processing
$JSONPROFILE['default'] = array(
    // Name of JSON key containing the Host name from Zabbix
    'hostKey'   => 'host',
    // Array of JSON keys to ignore (not sent to Zabbix)
    'skipKey'   => array('host', 'timestamp'),
    // Name of JSON key with the timestamp
    'timeKey'   => 'timestamp',
    // Optional: add a prefix to each key before sending it to Zabbix
    'keyPrefix' => ''
);

// Optional: Set fallback defaults for CSV profile processing
$CSVPROFILE['default'] = array(
    // The CSV file has a header row
    'hasHeader'  => true,
    // If 'hasHeader' == false, use a custom header row
    'useHeader'  => array('host','key','value','timestamp'),
    // Name of column containing the Host name from Zabbix
    'hostColumn' => 'host',
    // Array of columns to ignore (not sent to Zabbix)
    'skipColumn' => array('host', 'time'),
    // Name of column with the timestamp
    'timeColumn' => 'time'
);

?>