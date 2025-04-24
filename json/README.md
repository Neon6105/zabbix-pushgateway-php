## JSON Quickstart  
Push arbitrary JSON to Zabbix  
  
### Demo Environment  
  - Base URL: https://zabbix.exmple.com  
  - Directory: /push/  
  - Device Profile: iot  
  
### JSON-specific setup  
1. Create `jsonprofiles.php` in this directory and add custom profile data (if needed)  
1. Configure your device to push JSON to https://zabbix.example.com/push/json/  
1. If your device uses a profile, use https://zabbix.example.com/push/json/?profile=iot instead  
  
For detailed setup directions, please consult the [wiki for JSON](https://github.com/Neon6105/zabbix-pushgateway-php/wiki/JSON)  