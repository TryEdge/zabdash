<?php

require_once 'inc/functions.inc.php';

$zabServer = "zabbix.example.com";
$zabUser = "Admin";
$zabPass = "zabbix";
$zabURL = "https://zabbix.example.com/zabbix/";

//initial comma separated hosts groups ID - get IDs in Configuration -> Hosts Groups 
//ex:  $initgroups = "8,107,164";
$initgroups = "";

$useridlang = get_userid(CWebUser::getSessionCookie());
$lang = get_user_lang($useridlang);

$version = '1.2.2';

//Translate option: en_US or pt_BR
$labels = include_once 'locales/'.$lang.'.php';

?>
