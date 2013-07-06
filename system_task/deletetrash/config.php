<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: config.php 2012-03-31 09:59Z duty $
*/

//配置参数
$_SC = array();
$_SC['dbhost']  		= '127.0.0.1'; //服务器地址
$_SC['dbuser']  		= 'teamdota_www'; //用户
$_SC['dbpw'] 	 		= '63uzew8_h6l_4uah'; //密码
$_SC['dbcharset'] 		= 'utf8'; //字符集
$_SC['pconnect'] 		= 0; //是否持续连接
$_SC['dbname']  		= 'teamdota_www'; //数据库
$_SC['tablepre'] 		= 'e_'; //表名前缀

$_SC['deletetime'] 		= 1296000; //15天之后，回收站里的东西将被系统永久删除 15*24*3600
$_SC['deletewaittime'] 		= 3600; //应用执行的时间间隔 1小时 60*60
$_SC['timeoffset'] 	= '8'; //时区
?>