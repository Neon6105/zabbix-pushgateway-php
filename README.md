# Zabbix Push Gateway
_PHP Edition_
  
## Installation
1. Clone this repo to a web server that sits between your push devices and Zabbix (can colo with Zabbix)
1. Rename `pushgateway.example.conf.php` to `pushgateway.conf.php`
1. Edit `pushgateway.conf.php` to set the api_url and api_token for Zabbix  
  
## Device Profiles
1. For each device type, create a _deviceprofile_.php in `json.d/`
1. A device profile currently looks like:
```php
< json.d/deviceprofile.php >

<?php
$PROFILE["deviceprofile"] = array(
  "host_key"=>"hostname",
  "key_prefix"=>"",
  "skip_keys"=>array("hostname"),
);
?>
```
`deviceprofile` must be unique and should match the file name without the '.php' extension  
`host_key` is the key from the pushed JSON that contains the technical host name in Zabbix  
`key_prefix` is a string that will be prefixed to each JSON key before sending it to Zabbix  
`skip_keys` is an array of strings containing the JSON keys to ignore (will not be sent to Zabbix)  
  
## Zabbix Setup
1. Create a new host in Zabbix and set the host name to match the value provided by the `host_tag` key in the pushed JSON
1. Create an Item for the host for each additional JSON key and set the type to "Zabbix Trapper".  
  The item key must include the `$ZPG["key_prefix"]` and the `$PROFILE[*]["key_prefix"]`, if set.
```php
< pushgateway.conf.php >

...
$ZPG["key_prefix"] = "pushed.";
...
```
```php
< json.d/deviceprofile.php >

...
$PROFILE["deviceprofile"]["key_prefix"] = "device.";
...
```
If the key from the pushed JSON is `metric1` then the Zabbix item key must be "pushed.device.metric1"  
To omit any portion of the prefix, simply set the value to an empty string  

## Device Setup
1. Configure your device to push its JSON file to $your_server_url/json.php?profile=deviceprofile
  
