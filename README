#UNIFI VIDEO CONTROLLER ZABBIX INTEGRATION

Tested on UniFi Video Controller 3.10.5

##CONFIG
Example of config.json
```
{
    "unifi": {
         "host": "localhost",
         "port": 7443,
         "api" : "APIKEY"
    }
}
```
Make sure that the ZabbixUniFiVideo.php file is executable.

##ZABBIX CONFIGURATION
Zabbix userparameter config file use /opt/zabbix-unifi-video as path.
```
#Zabbix User Paramaters for UniFi Video Controller
UserParameter=unifinvr.discovery,/opt/zabbix-unifi-video/ZabbixUniFiVideo.php discovery
UserParameter=unifinvr.free,/opt/zabbix-unifi-video/ZabbixUniFiVideo.php free
UserParameter=unifinvr.used,/opt/zabbix-unifi-video/ZabbixUniFiVideo.php used
UserParameter=unifinvr.camera-status[*],/opt/zabbix-unifi-video/ZabbixUniFiVideo.php camera-status $1
UserParameter=unifinvr.camera-lastrec[*],/opt/zabbix-unifi-video/ZabbixUniFiVideo.php camera-lastrec $1
```
