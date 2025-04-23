<?php

//Required: Full URL to the Zabbix 7.x API Gateway
$ZPG['apiURL'] = 'http://127.0.0.1/zabbix/api_jsonrpc.php';

//Required: API Token (created in Zabbix Web UI)
$ZPG['apiToken'] = '';

//Future: Universal key prefix for all items collected via this gateway.
$ZPG['universalPrefix'] = '';

//Optional: Set fallback defaults for JSON profile processing
$PROFILE['default'] = array(
    'hostKey'   => 'host',
    'skipKey'   => array('host', 'timestamp'),
    'timeKey'   => 'timestamp',
    'keyPrefix' => ''
);

?>