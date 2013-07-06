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

$_SC['template'] 	= 'default'; //默认模板路径

$_SC['charset'] 		= 'utf-8'; //页面字符集
$_SC['sitename'] 		= 'TeamDota'; //站点名称
$_SC['timeoffset'] 	= '8'; //时区

$_SC['siteurl']			= 'http://www.teamdota.com/'; //站点的访问URL地址
$_SC['sitekey']			= '7bab576rlR7El700'; //站点密钥

$_SC['mail']=Array
	(
	'from' => 'notifications@teamdota.com'
	);
	
$_SC['deletewaittime'] 		= 86400; //应用执行的时间间隔 24小时 60*60*24

//httpsqs队列配置
$_SC['httpsqs']=Array
	(
	'server' => '127.0.0.1',
	'port' => 1218,
	'charset' => 'utf-8',
	'datakey' => array('sendmail'=>'teamdota_sendmail_data','senddiscussion'=>'teamdota_senddiscussion_data','sendattachment'=>'teamdota_sendattachment_data')
	);
?>