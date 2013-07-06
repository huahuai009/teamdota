<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: password_reset.php 2012-03-31 09:59Z duty $
*/

include_once('./common.php');

//是否关闭站点
checkclose();

$log = array();
$get = array();
//参数
$get = empty($_SERVER['QUERY_STRING'])?'':$_SERVER['QUERY_STRING'];
$log = getlog_code($get);
$theurl = "password_reset.php?$get";

if(submitcheck('resetsubmit')) {
	$password = $_POST['password'];
	$password_confirmation = $_POST['password_confirmation'];
	if(empty($password)) {
		showmessage('password_format_is_wrong', $theurl);
	}
	if(!$password || $password != addslashes($password)) {
		showmessage('profile_passwd_illegal', $theurl);
	}
	if($password != $password_confirmation) {
		showmessage('password_inconsistency', $theurl);
	}
	include_once(S_ROOT.'./source/function_cp.php');
	if(password_reset($log,$password)) {
		showmessage('getpasswd_succeed', 'do.php?ac=login',3);
	} else {
		showmessage('getpasswd_illegal');
	}
}

include_once template('password_reset');

function getlog_code($log_code) {
	global $_SGLOBAL;
	
	$id = 0;
	$code = '';

	$code_len = strlen($log_code);
	if($code_len > 32) {
		$code = addslashes(substr($log_code, -32));
		$id = intval(str_replace($code, '', $log_code));
	}
	if(empty($id)) {
		showmessage('getpasswd_illegal');
	}
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('log_forgot_password')." WHERE id='$id' AND code='$code'");
	if(!$log = $_SGLOBAL['db']->fetch_array($query)) {
		showmessage('getpasswd_illegal');
	}
	if(!$log['type']) {
		showmessage('getpasswd_illegal');
	}
	if($log['logtime'] < $_SGLOBAL['timestamp'] - 86400 * 3) {
		showmessage('getpasswd_illegal');
	}
	
	return $log;
}

?>