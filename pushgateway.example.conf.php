<?php

/* ZABBIX *********************/

//Required: Full URL to the Zabbix 7.x API Gateway
$ZPG["api_url"] = "http://127.0.0.1/zabbix/api_jsonrpc.php";

//Required: API Token (created in Zabbix Web UI)
$ZPG["api_token"] = "";

//Optional: Universal key prefix for all items collected via this gateway.
$ZPG["key_prefix"] = "";

/* The Zabbix item key must include both the 
 * $ZPG["key_prefix"] and $PROFILE[*]["key_prefix"], if set.
 * Example:
 * $ZPG["key_prefix"] = "pushed.";
 * $PROFILE[*]["key_prefix"] = "device.";
 * $KEY_FROM_JSON = "metric1";
 * Zabbix Item Key: pushed.device.metric1
*/

/* JSON ***********************/
$json_d = "json.d";
foreach (scandir($json_d) as $json_profile) {
    include $json_d . '/' . $json_profile;
}

?>