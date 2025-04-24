# Zabbix Pushgateway  
PHP Edition  
  
_Get the Go edition at https://github.com/Jonny-Burkholder/go-zabbix_  
_Get the Flask edition at https://github.com/Neon6105/zabbix-pushgateway-flask_  
  
## Installation  
1. Clone this repo to a web server that sits between your push devices and Zabbix (can colo with Zabbix)  
1. Rename `example.config.php` to `config.php`  
1. Edit `config.php` to set the apiURL and apiToken for Zabbix  
  
## Setup
1. [Arbitrary CSV](csv/)  
1. [InfluxDB](api/v2/write/)  
1. [Arbitrary JSON](json/)  
  
## Zabbix Setup
1. Create a host in Zabbix and set the Host name to match the value provided by the push device and method   
1. Create items for each piece of information that Zabbix will track, including optional prefixes (if used)  
  
For detailed setup directions, please consult the [wiki](https://github.com/Neon6105/zabbix-pushgateway-php/wiki/)