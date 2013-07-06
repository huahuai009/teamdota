<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: common.php 2012-03-31 09:59Z duty $
*/

@define('IN_TEAMDOTA', TRUE);
define('D_BUG', '1');

D_BUG?error_reporting(7):error_reporting(0);
set_magic_quotes_runtime(0);

$_SGLOBAL = $_SCONFIG = $_SCOOKIE = $_SN = $group = array();

//程序目录
define('S_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR);

//基本文件
include_once(S_ROOT.'./config.php');
include_once(S_ROOT.'./source/function_common.php');

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

//检查浏览器
checkUSER_AGENT();

//COOKIE
$prelength = strlen($_SC['cookiepre']);
foreach($_COOKIE as $key => $val) {
	if(substr($key, 0, $prelength) == $_SC['cookiepre']) {
		$_SCOOKIE[(substr($key, $prelength))] = empty($magic_quote) ? saddslashes($val) : $val;
	}
}

//CC攻击检查
if ($_SCONFIG['db_loadavg'] && sloadAvg($_SCONFIG['db_loadavg'])) {
	$_SCONFIG['db_cc'] = 2;
}
if ($_SCONFIG['db_cc']) {
	sDefendCc($_SCONFIG['db_cc']);
}

//启用GIP
if ($_SC['gzipcompress'] && function_exists('ob_gzhandler')) {
	ob_start('ob_gzhandler');
} else {
	ob_start();
}

//初始化
$_SGLOBAL['supe_uid'] = 0;
$_SGLOBAL['supe_username'] = '';
$_SGLOBAL['inajax'] = empty($_GET['inajax'])?0:intval($_GET['inajax']);
$_SGLOBAL['refer'] = empty($_SERVER['HTTP_REFERER'])?'':$_SERVER['HTTP_REFERER'];

//整站风格
if(empty($_SCONFIG['template'])) {
	$_SCONFIG['template'] = 'default';
}

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
checkauth();
$_SGLOBAL['uhash'] = md5($_SGLOBAL['supe_uid']."\t".substr($_SGLOBAL['timestamp'], 0, 6));

?>