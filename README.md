# ZabDash


![](https://repository-images.githubusercontent.com/189693207/0b4ecb00-85ec-11e9-8c44-8aa5c1fb7d94)

- View all graphs of host
- View all graphs os hosts group
- Use the Item field for show specific items graphs like CPU, Disk, network traffic, ping, etc, for one or multiple hosts
- Use search bar to filter hosts
- Location Map for hosts (add latitude and longitude at host inventory)


![](https://user-images.githubusercontent.com/7674445/58826320-74dfd100-860e-11e9-90e2-ca2c09816038.png)


## Installation

1 - Copy zabdash folder to Zabbix folder (/usr/share/zabbix);

2 - Copy config.php.sample to config.php;

3 - Edit config.php with your server settings;

4 - Set Automatic Hosts inventory in Zabbix;

5 - Access URL http://<your zabbix server>/zabbix/zabdash;



#### To add a menu item for ZabDash see README.txt file in menu folder:

- Make a copy of /usr/share/zabbix/include/menu.inc.php file.

- Copy the file "menu/menu.inc.php" for your zabbix include folder (/usr/share/zabbix/include).

![](https://user-images.githubusercontent.com/7674445/58826988-fa17b580-860f-11e9-83ab-af72303cb9fa.png)




#### Zabbix API Needs php-posix:

apt-get install php-common - ubuntu/debian

yum install php-process - redhat/centos

zypper install php-posix - OpenSuse


