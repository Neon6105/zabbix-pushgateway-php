<?php

define('ABSPATH', dirname(__FILE__));
define('DIR_SEP', DIRECTORY_SEPARATOR);

// require 'config.php';
require join(DIR_SEP, array(ABSPATH, 'config.php'));

// include functions.php from each module
$modules = array(
  join(DIR_SEP, array(ABSPATH, 'api', 'v2', 'write')),
  join(DIR_SEP, array(ABSPATH, 'csv')),
  join(DIR_SEP, array(ABSPATH, 'json'))
);

foreach ($modules as $module) {
  $functions = join(DIR_SEP, array($module, 'functions.php'));
  if (is_file($functions)) {
    include_once $functions;
  }
}

// Return an array formatted for Zabbix "params" in history.push
function zabbixParamify($host, $key, $val=0, $clock=null, $ns=0) {
  if ($host && $key && $clock) {
      return array(
          'host'=> $host,
          'key'=>$key,
          'value'=>$val,
          'clock'=> intval($clock),
          'ns'=>intval($ns),
      );
  } else {
      return null;
  }
}  // end zabbixAddParam()


// Load "params" into the formatted JSON then send it to the Zabbix API
function zabbixHistoryPush(array $params) {
  global $ZPG;

  //We're sending JSON data using an API token for authorization
  $headers = ['Content-type: application/json', 'Authorization: Bearer ' . $ZPG['apiToken']];
  
  //Get the current unix timestamp. Used for the transaction ID.
  $transactionID = (new DateTime('now'))->getTimeStamp();

  //Meta-formatting for Zabbix API
  $dataObj = array(
      'jsonrpc'=> '2.0',
      'method'=> 'history.push',
      'params'=> $params,
      'id'=> $transactionID
  );

  //Convert dataObj into a proper JSON object
  $dataObj_json = json_encode($dataObj, JSON_FORCE_OBJECT);

  //Perpare and perform the POST request to the Zabbix API using curl
  $curl = curl_init($ZPG['apiURL']);
  
  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $dataObj_json);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLINFO_HEADER_OUT, true);

  $output = curl_exec($curl);
  curl_close($curl);

  //Tell us about your experience!
  var_dump($output);
  return $output;
}  // end zabbixPush()

?>