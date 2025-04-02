<?php

/* ZABBIX *********************/

//Required: Full URL to the Zabbix 7.x API Gateway
$ZPG["api_url"] = "http://127.0.0.1/zabbix/api_jsonrpc.php";

//Required: API Token (created in Zabbix Web UI)
$ZPG["api_token"] = "";

//Optional: Universal key prefix for all items collected via this gateway.
$ZPG["key_prefix"] = "";

/* NOTE: The Zabbix Item's key must equal 
 * $ZPG["key_prefix"] . $PROFILE[*]["key_prefix"] . $PUSHED_JSON["key_name"]
 * Example:
 * $ZPG["key_prefix"] = "zpg_";
 * $PROFILE[*]["key_prefix"] = "json_";
 * $KEY_FROM_JSON = "measurement1";
 * Zabbix Key: zpg_json_measurement1
*/

/* JSON ***********************/
$json_d = 'json.d';
foreach (scandir($json_d) as $json_profile) {
    include $json_d . '/' . $json_profile;
}

?>