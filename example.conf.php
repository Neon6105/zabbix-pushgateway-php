<?php

/* ZABBIX *********************/

//Required: Full URL to the Zabbix 7.x API Gateway
$ZPG["apiURL"] = "http://127.0.0.1/zabbix/api_jsonrpc.php";

//Required: API Token (created in Zabbix Web UI)
$ZPG["apiToken"] = "";

//Optional: Universal key prefix for all items collected via this gateway.
$ZPG["universalPrefix"] = "";

/* JSON ***********************/
$json_d = join(DIRECTORY_SEPARATOR, array(dirname(__FILE__), "json"));
foreach (scandir($json_d) as $json_profile) {
    include join(DIRECTORY_SEPARATOR, array($json_d, $json_profile));
}

?>