## CSV Quickstart  
Push arbitrary JSON to Zabbix  
  
### Demo Environment  
  - Base URL: https://zabbix.exmple.com  
  - Directory: /push/  
  - Device Profile: iot  
  
### CSV-specific setup  
1. Create `csvprofiles.php` in this directory and add custom profile data (if needed)  
1. Configure your device to push CSV to https://zabbix.example.com/push/csv/  
1. If your device uses a profile, use https://zabbix.example.com/push/csv/?profile=iot instead  
  
For detailed setup directions, please consult the [wiki for CSV](https://github.com/Neon6105/zabbix-pushgateway-php/wiki/CSV)  