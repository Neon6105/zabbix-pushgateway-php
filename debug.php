<!DOCTYPE html>
<html>
<head>
  <title>Zabbix Pushgateway Debugger</title>
  <style>
    html, body, input, select {
      background-color: #1e1e1e;
      color: #ffffff;
      font-family: monospace;
    }
  </style>
</head>
<body>

<?php
  require 'functions.php';
  include 'csv/csv.php';
  include 'csv/csvprofiles.php';
  include 'json/json.php';
  include 'json/jsonprofiles.php';
?>

<?php
echo "Zabbix API: ". $ZPG["apiURL"] . "<br />";
$serverURL = 'http://localhost' . dirname($_SERVER['PHP_SELF']);
echo "Pushgateway: " . $serverURL;
?>

<script language="javascript">
    function getPushDetails() {
        var pushMethod = document.getElementById('pushMethod').value;
        document.getElementById('csvdetails').style.display = "none";
        document.getElementById('influxdetails').style.display = "none";
        document.getElementById('influxdetails1').style.display = "none";
        document.getElementById('jsondetails').style.display = "none";
        document.getElementById(pushMethod + 'details').style.display = "block";
        document.getElementById(pushMethod + 'details1').style.display = "block";
    }
</script>

<form method="post">
<table>
  <tr>
    <td><label for="zabbixHost">Host: </label></td>
    <td><input type="text" id="zabbixHost" name="zabbixHost" required placeholder="zabbix" size=32 /></td>
  </tr><tr>
    <td><label for="zabbixKey">Key: </label></td>
    <td><input type="text" id="zabbixKey" name="zabbixKey" required placeholder="key.name" size=32 /></td>
  </tr><tr>
    <td><label for="zabbixVal">Value: </label></td>
    <td><input type="text" id="zabbixVal" name="zabbixVal" required placeholder="42" size=32/></td>
  </tr><tr>
    <td><label for="pushMethod">Method: </label></td>
    <td>
      <select name="pushMethod" id="pushMethod" required onChange="getPushDetails()">
        <option value="direct">Direct (no method)</option>
        <option value="csv">CSV</option>
        <option value="influx">InfluxDB line protocol</option>
        <option value="json">JSON line format</option>
      </select>
    </td>
  </tr>
</table>
<table>
  <tr name="csvdetails" id="csvdetails" style="display:none;">
    <td>CSV</td><td>???</td>
  </tr>
  <tr name="influxdetails" id="influxdetails" style="display:none;">
    <td>Use measurement prefix?</td>
    <td>
        <input type="radio" name="useMeasurementPrefix" value="true" id="influxprefixtrue">
        <label for="influxprefixtrue">Yes</label>
        <input type="radio" name="useMeasurementPrefix" value="false" id="influxprefixfalse" checked>
        <label for="influxprefixfalse">No</label>
    </td>
  </tr>
  <tr name="influxdetails1" id="influxdetails1" style="display:none;">
    <td><label for="measurementPrefix">Measurement: </label></td>
    <td><td><input type="text" id="measurementPrefix" name="measurementPrefix" placeholder="testdata" size=24/></td>
  </tr>
  <tr name="jsondetails" id="jsondetails" style="display:none;">
    <td><label for="jsonprofile">JSON Profile: </label></td>
    <td><input type="text" id="jsonprofile" name="jsonprofile" placeholder="default" /></td>
  </tr>
  <tr><td colspan=2><br /></td></tr>
</table>
<input type="submit">
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = $_POST['zabbixHost'];
    $key = $_POST['zabbixKey'];
    $val = $_POST['zabbixVal'];
    $how = $_POST['pushMethod'];

    $params = zabbixParamify($host, $key, $val, time());
    echo "<br /><br />Received parameters:<br />";
    var_dump($params);
    echo "<br /><br />";

    switch ($how) {
        case 'csv':
            $useMethod = 'CSV';
            $modpath = '/csv';
            $headers = ['Content-type: text/csv'];
            break;
        case 'influx':
            $useMethod = 'InfluxDB line protocol';
            $modpath = '/api/v2/write';
            if ($_POST['useMeasurementPrefix'] == 'true') {
                $modpath .= '?org=profile&bucket=measurement';
            }
            $headers = ['Content-type: application/octet-stream'];
            if ($_POST['measurementPrefix']) {
                $data = $_POST['measurementPrefix'];
            } else {
                $data = "testdata";
            }
            $data .= ",host=$host $key=$val " . floor(microtime(true) * 1000);
            break;
        case 'json':
            $useMethod = 'JSON line format';
            $modpath = '/json/';
            if ($_POST['jsonprofile']) {
                $modpath .= '?profile=' . $_POST['jsonprofile'];
                $profile = $_POST['jsonprofile'];
            } else {
                $profile = 'default';
            }
            $headers = ['Content-type: application/json'];
            $data = json_encode(array(
                $PROFILE[$profile]['hostKey']=>$host,
                $key=>$val,
                $PROFILE[$profile]['timekey']=>time()
            ), JSON_FORCE_OBJECT);
            break;
        default:
            $useMethod = 'Direct';
            break;
    }
    echo 'Method: ' . $useMethod . "<br />";
    if (isset($modpath)) {
        echo 'URL: ' . $serverURL . $modpath . '<br />';
        echo 'Data: ' . $data . '<br />';
        echo '<br />';
        $curl = curl_init($serverURL . $modpath);

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        //curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);

        $result = curl_exec($curl);
        curl_close($curl);

        var_dump($result);
    } else {
        zabbixHistoryPush($params);
    }
}
?>

</body>
</html>