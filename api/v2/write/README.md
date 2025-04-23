## InfluxDB Line Protocol  
Accept data from Influx-compatible devices  
  
## Zabbix Setup  
1. Create a new host in Zabbix and set the host name to match the value provided by the `host` tag
1. Create an Item for the host for each field of each point's field set.

## Device Setup
1. Configure your device to use the following InfluxDB setup:
  Server: $your_server_name_or_IP  
  Port and Protocol: 80, HTTP (or 443, HTTPS)  
  Token: (none or default)  
  API Path Prefix: (only needed if /api and /health are not on the web root)  
  Organization and Bucket: (unused)

Note: If you set Organization to "profile" and Bucket to "measurement" then the InfluxDB measurement name will be added as a dotted prefix to each key before it's sent to Zabbix.  
  
Example: `cpustat,object=nodes,host=ubuntu1 cpu=0.031 1234567890123456789`  
  
?org=profile&bucket=measurement  
Zabbix Key: cpustat.cpu  
  
?org=ExampleCorp&bucket=noc-east  
Zabbix Key: cpu
  
## Apache
It may be necessary to add the following configuration to your Apache2 site configuration:
Note: The presumes that the repository was extracted to the root of your web server.
```
RewriteEngine On
RewriteRule "^/api/v2/write$" "/api/v2/write/index.php" [PT]
```