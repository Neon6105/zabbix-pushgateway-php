<!DOCTYPE html>
<html>
<head>
  <title>ZPG Debugger</title>
  <style>
    html, body, input, select, textarea {
      background-color: #1e1e1e;
      color: #ffffff;
      font-family: monospace;
    }
  </style>
</head>
<body>

<?php require 'functions.php'; ?>

<?php
echo "Zabbix API: ". $ZPG["apiURL"] . "<br />";
$serverURL = 'http://localhost' . dirname($_SERVER['PHP_SELF']);
echo "Pushgateway: " . $serverURL;
?>

<script language="javascript">
    function getPushDetails(pushMethod) {
        //var pushMethod = document.getElementById('pushMethod').value;
        document.getElementById("apidetails").style.display = "none";
        document.getElementById("csvdetails").style.display = "none";
        document.getElementById("influxdetails").style.display = "none";
        document.getElementById("jsondetails").style.display = "none";
        document.getElementById(pushMethod + "details").style.display = "block";
    }
</script>

<form method="post">
  <p><label for="pushMethod">Method: </label>
  <select name="pushMethod" id="pushMethod" required onChange="getPushDetails(this.value)">
    <option value="api">Direct (no method)</option>
    <option value="csv">CSV</option>
    <option value="influx">InfluxDB line protocol</option>
    <option value="json">JSON line format</option>
  </select></p>

  <div id="apidetails" name="apidetails">
    <table>
    <tr>
      <td><label for="zabbixHost">Host: </label></td>
      <td><input type="text" id="zabbixHost" name="zabbixHost" placeholder="zabbix" size=32 /></td>
    </tr><tr>
      <td><label for="zabbixKey">Key: </label></td>
      <td><input type="text" id="zabbixKey" name="zabbixKey" placeholder="key.name" size=32 /></td>
    </tr><tr>
      <td><label for="zabbixVal">Value: </label></td>
      <td><input type="text" id="zabbixVal" name="zabbixVal" placeholder="42" size=32/></td>
    </tr>
    </table>
  </div>

  <div id="csvdetails" name="csvdetails" style="display:none;">
    <label for="csvprofle">Profile: </label>
    <select name="csvprofile" id="csvprofile">
        <option value="">None (default)</option>
      <?php
      foreach ($CSVPROFILE as $k=>$v) {
        echo '<option value="' . $k . '">' . $k . '</option>';
      }
      ?>
    </select><br /><br />
    <textarea id="csvupload" name="csvupload" rows="8" cols="48" />
host,metric1,metric2,metric3,time
zabbix,42,potato,99,<?php echo trim(date('c')); ?></textarea>
  </div>

  <div id="influxdetails" name="influxdetials" style="display:none;">
    <p>Use measurement prefix?
      <input type="radio" name="useMeasurementPrefix" value="true" id="influxprefixtrue">
      <label for="influxprefixtrue">Yes</label>
      <input type="radio" name="useMeasurementPrefix" value="false" id="influxprefixfalse" checked>
      <label for="influxprefixfalse">No</label>
    </p><p>
      <label for="influxdatapoint">Data Point: </label>
      <input type="text" id="influxdatapoint" name="influxdatapoint" placeholder="testdata,host=zabbix key=value <?php echo floor(microtime(true) * 1000) ?>" size=48/>
    </p>
  </div>

  <div id="jsondetails" name="jsondetails"  style="display:none;">
    <label for="jsonprofle">Profile: </label>
    <select name="jsonprofile" id="jsonprofile">
        <option value="">None (default)</option>
      <?php
      foreach ($JSONPROFILE as $k=>$v) {
        echo '<option value="' . $k . '">' . $k . '</option>';
      }
      ?>
    </select><br /><br />
    <textarea id="jsonupload" name="jsonupload" rows="8" cols="48" />
{
    "host": "zabbix",
    "key.name": "42",
    "timestamp": "<?php echo date('c'); ?>"
}
    </textarea>
  </div>
<br />
<input type="submit">
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $how = $_POST['pushMethod'];
    echo "<br /><br />";
    switch ($how) {
        case 'csv':
            $useMethod = 'CSV';
            $modpath = '/csv/';
            $csvprofile = $_POST['csvprofile'];
            if ($csvprofile) {
                $modpath .= '?profile=' . $csvprofile;
            }
            $headers = ['Content-type: text/csv'];
            $data = $_POST['csvupload'];
            break;

        case 'influx':
            $useMethod = 'InfluxDB line protocol';
            $modpath = '/api/v2/write';
            if ($_POST['useMeasurementPrefix'] == 'true') {
                $modpath .= '?org=profile&bucket=measurement';
            }
            $headers = ['Content-type: application/octet-stream'];
            if ($_POST['influxdatapoint']) {
                $data = $_POST['influxdatapoint'];
            } else {
                $data = "testdata,host=zabbix key=value " . floor(microtime(true) * 1000);
            }
            break;

        case 'json':
            $useMethod = 'JSON line format';
            $modpath = '/json/';
            $jsonprofile = $_POST['jsonprofile'];
            if ($jsonprofile) {
                $modpath .= '?profile=' . $jsonprofile;
            }
            $headers = ['Content-type: application/json'];
            $data = $_POST['jsonupload'];
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
        $host = $_POST['zabbixHost'] != '' ? $_POST['zabbixHost'] : 'zabbix';
        $key = $_POST['zabbixKey'] != '' ? $_POST['zabbixKey'] : 'key.name';
        $val = $_POST['zabbixVal'] != '' ? $_POST['zabbixVal'] : '42';
        $params = zabbixParamify($host, $key, $val, time());
        echo "Received parameters:<br />";
        var_dump($params);
        echo "<br /><br />";
        echo "<br />";
        zabbixHistoryPush($params);
    }
}
?>

</body>
</html>