## Device Profiles  
Note: Since 'json.php' is distributed with the repository, it is recommended to create a separate file for custom profiles.  
By default, if a file named 'jsonprofiles.php' exists in this directory, it is included automatically.  
  
A device profile currently looks like:  
```php
< json/jsonprofiles.php >

<?php
$PROFILE['deviceprofile'] = array(
  'hostKey'   => 'hostname',
  'skipKey'   => array('hostname', 'timestamp'),
  'timeKey'   => 'timestamp',
  'keyPrefix' => '',
);
?>
```
`deviceprofile` must be unique. Used to choose a profile via query string.  
`hostKey` is the key from the pushed JSON that contains the technical host name in Zabbix  
`skipKey` is an array of strings containing the JSON keys to ignore (will not be sent to Zabbix)  
`timeKey` is the key from the pushed JSON that contains the time that the data was recorded  
`keyPrefix` is a string that will be prefixed to each JSON key before sending it to Zabbix  
  
If a device requires more complex processing, it is recommended to create a separate file (see 'endec.php' as an example).  
  
## Zabbix Setup
1. Create a new host in Zabbix and set the host name to match the value provided by `hostKey` in the pushed JSON
1. Create an Item for the host for each additional JSON key and set the type to "Zabbix Trapper"  
  The item key must include `$PROFILE[*]['keyPrefix']`, if set.
```php
< json/jsonprofiles.php >

...
$PROFILE['deviceprofile']['keyPrefix'] = 'device.';
...
```
Given the profile above, if the key from the pushed JSON is `metric1` then the Zabbix Item's key must be `device.metric1`  
To omit the prefix, simply set the value to an empty string (e.g. `'keyPrefix' => ''`)  

## Device Setup
1. Configure your device to push its JSON file to $your_server_url/json/?profile=deviceprofile
  
