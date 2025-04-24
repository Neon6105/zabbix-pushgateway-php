## InfluxDB Quickstart  
Push InfluxDB Line Protocol points to Zabbix  
  
### Demo Environment  
  - Base URL: https://zabbix.exmple.com  
  - Directory: /push/  

### InfluxDB-specific setup
1. Configure a server-side rewrite rule that silently rewrites /write to /write/index.php  
1. Set the InfluxDB Server to "zabbix.example.com", port to "443", and API prefix to "push"
1. To include the measurement as a dotted prefix to each key, set the Organization to "profile" and the Bucket to "measurement"  
  
< Example : Apach2 >  
```
RewriteEngine On
RewriteRule "^/push/api/v2/write$" "/push/api/v2/write/index.php" [PT]
```  
< Example : nginx >  
```
location ~ /push/api/v2/write {
    rewrite ^/push/api/v2/write$ /push/api/v2/write/index.php break;
}
```  
  
For detailed setup directions, please consult the [wiki for InfluxDB Line Protocol](https://github.com/Neon6105/zabbix-pushgateway-php/wiki/InfluxDB)  