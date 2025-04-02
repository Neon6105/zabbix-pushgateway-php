<?php

include 'pushgateway.conf.php';

//Convert POSTed JSON into Zabbix JSON
function translate() {
    global $ZPG;
    global $PROFILE;
    $this_profile = $_GET["profile"] ?? "default";
    //Load profile variables as locals
    $host_tag = $PROFILE[$this_profile]["host_tag"] ?? "hostname";
    $skip_keys = $PROFILE[$this_profile]["skip_keys"] ?? array("hostname");
    //Combine universal prefix and profile prefix
    $key_prefix = $ZPG["key_prefix"] ?? "";
    $key_prefix .= $PROFILE[$this_profile]["key_prefix"] ?? "";

    //Get JSON data from POST
    $endData = file_get_contents('php://input');

    //Decode the JSON object
    $json = json_decode($endData, true);

    //Custom processing for ISO 8601 Timestamps
    if (array_key_exists("when", $json)) {
        $date = new DateTime($json["when"]);
        $timeStamp = $date->getTimestamp();
    } else {
        $timeStamp = time();
    }

    //Match Zabbix {HOST.NAME} via the profile's $host_tag
    $host = $json[$host_tag];

    //Loop through JSON entries and add to a list
    $list = array();
    foreach($json as $key => $value){
        //Omit keys listed in skip_keys
        if (in_array($key, $skip_keys)) {
            continue;
        }
    
        //Format each key/value pair for Zabbix API
        $item = zabbixParamify($host, $key_prefix . $key, (string)$value, $timeStamp);

        //Add to the list if it's not empty
        if($item){
            $list[] = $item;
        }
    } // end foreach($key=>$value)

    //Send fully formatted data list Zabbix
    zabbixPush($list);

} // end translate()


//Formats a key/value pair to what zabbix accepts
function zabbixParamify ($host, $key, $value=0, $clock=null, $ns=0){
    if($host && $key && $clock){
        return array(
            "host"=> $host,
            "key"=>$key,
            "value"=>$value,
            "clock"=> intval($clock),
            "ns"=>intval($ns),
        );
    } else {
        return null;
    }
} // end zabbixParamify()


//Send fully formatted data list Zabbix
function zabbixPush(array $list){
    global $ZPG;

    //We're sending JSON data using an API token for authorization
    $headers = ['Content-type: application/json', 'Authorization: Bearer ' . $ZPG['api_token']];
    
    //Get the current unix timestamp. Used for the transaction ID.
    $dateRaw = new DateTime("now");
    $dateID = $dateRaw->getTimeStamp();

    //Meta-formatting for Zabbix API
    $dataObj = array(
        "jsonrpc"=> "2.0",
        "method"=> "history.push",
        "params"=> $list,
        "id"=> $dateID
    );

    //Convert dataObj into a proper JSON object
    $dataObj_json = json_encode($dataObj, JSON_FORCE_OBJECT);

    //Perpare and perform the POST request to the Zabbix API using curl
    $curl = curl_init($ZPG['api_url']);
    
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $dataObj_json);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLINFO_HEADER_OUT, true);

    $output = curl_exec($curl);
    curl_close($curl);

    //Tell us about your experience!
    return $output;

} // end zabbixPush()

//Let's gooooo!
translate();

?>
