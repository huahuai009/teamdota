<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: openapi/common.php 2012-07-24 09:59Z duty $
*/

@define('IN_TEAMDOTA', TRUE);

error_reporting(0);
set_magic_quotes_runtime(0);

$_SGLOBAL = $_SCONFIG = $group = array();

//程序目录
define('S_ROOT', substr(dirname(__FILE__), 0, -7));

//基本文件
include_once(S_ROOT.'./config.php');
include_once(S_ROOT.'./source/function_common.php');
include_once(S_ROOT.'./openapi/open_function_common.php');

//时间
$mtime = explode(' ', microtime());
$_SGLOBAL['timestamp'] = $mtime[1];
$_SGLOBAL['supe_starttime'] = $_SGLOBAL['timestamp'] + $mtime[0];

//GPC过滤
$magic_quote = get_magic_quotes_gpc();
if(empty($magic_quote)) {
	$_GET = saddslashes($_GET);
	$_POST = saddslashes($_POST);
}

//链接数据库
dbconnect();

//缓存文件
include_once(S_ROOT.'./data/data_config.php');

//初始化
$_SGLOBAL['supe_uid'] = 0;
$_SGLOBAL['supe_username'] = '';

//处理REQUEST_URI
if(!isset($_SERVER['REQUEST_URI'])) {  
	$_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'];
	if(isset($_SERVER['QUERY_STRING'])) $_SERVER['REQUEST_URI'] .= '?'.$_SERVER['QUERY_STRING'];
}
if($_SERVER['REQUEST_URI']) {
	$temp = urldecode($_SERVER['REQUEST_URI']);
	if(strexists($temp, '<') || strexists($temp, '"')) {
		$_GET = shtmlspecialchars($_GET);//XSS
	}
}

//判断用户登录状态
open_checkauth();


//以下错误字典
$_SGLOBAL['open_errorinfo'][0] = array('code'=>0,'content'=>'success');
$_SGLOBAL['open_errorinfo'][1000] = array('code'=>1000,'content'=>'fail');
$_SGLOBAL['open_errorinfo'][1001] = array('code'=>1001,'content'=>'Auth failure1');
$_SGLOBAL['open_errorinfo'][1002] = array('code'=>1002,'content'=>'group not allowed to visit');
$_SGLOBAL['open_errorinfo'][1003] = array('code'=>1003,'content'=>'group has been locked');
?>