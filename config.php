<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: config.php 2012-03-31 09:59Z duty $
*/

//配置参数
$_SC = array();
$_SC['dbhost']  		= 'localhost'; //服务器地址
$_SC['dbuser']  		= 'root'; //用户
$_SC['dbpw'] 	 		= ''; //密码
$_SC['dbcharset'] 		= 'utf8'; //字符集
$_SC['pconnect'] 		= 0; //是否持续连接
$_SC['dbname']  		= 'teamdota_open'; //数据库
$_SC['tablepre'] 		= 'e_'; //表名前缀
$_SC['charset'] 		= 'utf-8'; //页面字符集

$_SC['gzipcompress'] 	= 0; //启用gzip

$_SC['cookiepre'] 		= '_teamdota_'; //COOKIE前缀
$_SC['cookiedomain'] 	= ''; //COOKIE作用域
$_SC['cookiepath'] 		= '/'; //COOKIE作用路径

$_SC['attachdir']		= './attachment/'; //附件本地保存位置(服务器路径, 属性 777, 必须为 web 可访问到的目录, 相对目录务必以 "./" 开头, 末尾加 "/")
$_SC['attachdirtemp']		= './attachmenttemp/'; //附件临时存放本地保存位置，目前在进行ftp文件下载时使用( 使用文件 group_downloas.php)
$_SC['attachurl']		= 'attachment/'; //附件本地URL地址(可为当前 URL 下的相对地址或 http:// 开头的绝对地址, 末尾加 "/")

//$_SC['siteurl']			= 'http://www.teamdota.com/'; //站点的访问URL地址(http:// 开头的绝对地址, 末尾加 "/")，为空的话，系统会自动识别。
$_SC['siteurl']			= 'http://60.191.221.82/'; //站点的访问URL地址(http:// 开头的绝对地址, 末尾加 "/")，为空的话，系统会自动识别。

?>