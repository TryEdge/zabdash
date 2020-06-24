# Announcement:

I'm suspending the development of my add-ons due the change new Zabbix 5.x to MVC. The new structure demands a large rewrite of code, which i can't do at moment.


Estou suspendendo o desenvolvimento dos meus complementos devido à mudança do novo Zabbix 5.x para o desenvolvimento MVC. A nova estrutura exige uma grande reescrita de código, o que não posso fazer no momento.



# ZabDash

![](https://repository-images.githubusercontent.com/70854481/87fda200-394e-11ea-932a-bd62b9b6b5d9)

- View all graphs of host
- View all graphs os hosts group
- Use the Item field for show specific items graphs like CPU, Disk, network traffic, ping, etc, for one or multiple hosts
- Use search bar to filter hosts
- Location Map for hosts (add latitude and longitude at host inventory)

![](https://user-images.githubusercontent.com/7674445/72847060-2acc5e00-3c78-11ea-9b45-3e3dba02a832.png)


## Installation

1 - Copy zabdash folder to Zabbix folder (/usr/share/zabbix);

2 - Copy config.php.sample to config.php;

3 - Edit config.php with your server settings;

4 - Set Automatic Hosts inventory in Zabbix;

5 - Access URL http://<your zabbix server>/zabbix/zabdash;



#### To add a menu item for ZabDash see README.txt file in menu folder:

- Make a copy of /usr/share/zabbix/include/menu.inc.php file.

- Copy the file "menu/menu.inc.php" for your zabbix include folder (/usr/share/zabbix/include).



#### Zabbix API Needs php-posix:

apt-get install php-common - ubuntu/debian

yum install php-process - redhat/centos

zypper install php-posix - OpenSuse


