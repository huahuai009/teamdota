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

$_SC['charset'] 		= 'utf-8'; //页面字符集
$_SC['sitename'] 		= 'TeamDota'; //站点名称
$_SC['gzipcompress'] 	= 0; //启用gzip
$_SC['template'] 	= 'default'; //默认模板路径
$_SC['timeoffset'] 	= '8'; //默认模板路径
$_SC['mail']=Array
	(
	'mailsend' => 1,
	'maildelimiter' => '0',
	'mailusername' => 1,
	'server' => 'smtp.126.com',
	'port' => 25,
	'auth' => 1,
	'from' => 'teamdota@126.com',
	'replyfrom' => 'do-not-reply@teamdota.com',
	'auth_username' => 'teamdota@126.com',
	'auth_password' => '19840309huiwei'
	);

//httpsqs队列配置
$_SC['httpsqs']=Array
	(
	'server' => '127.0.0.1',
	'port' => 1218,
	'charset' => 'utf-8',
	'datakey' => 'teamdota_sendmail_data'
	);
?>