<?php

require_once 'inc/functions.inc.php';

$zabServer = "10.20.0.1";
$zabUser = "admin";
$zabPass = "zabbix";
$zabURL = "http://10.20.0.1/zabbix/";

$useridlang = get_userid(CWebUser::getSessionCookie());
$lang = get_user_lang($useridlang);

$version = '1.1.9"

//Translate option: en_US or pt_BR
$labels = include_once 'locales/'.$lang.'.php';


?>