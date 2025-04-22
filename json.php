<?php

include "config.php";

//Convert POSTed JSON into Zabbix JSON
function translate() {
    global $ZPG;
    global $PROFILE;
    $thisProfile = $_GET["profile"] ?? "default";
    //Load profile variables as locals
    $host_key = $PROFILE[$thisProfile]["host_key"] ?? "host";
    $skipKey = $PROFILE[$thisProfile]["skipKey"] ?? array("host");
    $timeKey = $PROFILE[$thisProfile]["timeKey"] ?? "timestamp";
    //Combine universal prefix and profile prefix
    $keyPrefix = $ZPG["universalPrefix"] ?? "";
    $keyPrefix .= $PROFILE[$thisProfile]["keyPrefix"] ?? "";

    //Get JSON data from POST
    $postData = file_get_contents('php://input');

    //Decode the JSON object
    $json = json_decode($postData, true);

    //Custom processing for timestamps
    if (array_key_exists($timeKey, $json)) {
        $timeStamp = (new DateTime($json[$timeKey]))->getTimeStamp() ?? time();
    } else {
        $timeStamp = time();
    }

    //Match Zabbix {HOST.NAME} via the profile's $host_key
    $host = $json[$host_key];

    //Loop through JSON entries and add to a list
    $list = array();
    foreach($json as $key => $value){
        //Omit keys listed in skipKey
        if (in_array($key, $skipKey)) {
            continue;
        }
    
        //Format each key/value pair for Zabbix API
        $item = zabbixParamify($host, $keyPrefix . $key, (string)$value, $timeStamp);

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
    $headers = ["Content-type: application/json", "Authorization: Bearer " . $ZPG["apiToken"]];
    
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
    $curl = curl_init($ZPG["apiURL"]);
    
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
