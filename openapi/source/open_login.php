<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: open_login.php 2012-07-24 09:59Z duty $
*/
if(!defined('IN_TEAMDOTA')) {
	open_showmessage($_SGLOBAL['open_errorinfo'][1001]);
}
$password = $_POST['password'];
$email = strtolower(trim($_POST['email']));
$cookietime = intval($_POST['cookietime']);

$cookiecheck = $cookietime?' checked':'';

if(empty($email)) {
	$errmessage = 'email format is wrong';
} elseif(!isemail($email)) {
	$errmessage = 'email format is wrong';
} elseif(empty($password)) {
	$errmessage = 'password were not empty , please login again';
} else {
	$passwordmd5 = preg_match('/^\w{32}$/', $password) ? $password : md5($password);
	//检索当前用户
	$query = $_SGLOBAL['db']->query("SELECT password,salt,group_id,uid,email,status,username FROM ".tname('member')." WHERE `username`='{$email}' LIMIT 1");
	if($user = $_SGLOBAL['db']->fetch_array($query)) {
		if($user['status'] == 1){
			$errmessage = 'login failure,please login again';
		} elseif($user['password'] != md5($passwordmd5.$user['salt'])) {
			$errmessage = 'login failure,please login again';
		} else {
			$setarr = array(
				'uid' => $user['uid'],
				'username' => addslashes($user['username']),
				'password' => addslashes($user['password'])
			);
			//清理在线session
			insertsession($setarr);
			//返回token
			open_showmessage($_SGLOBAL['open_errorinfo'][0],'', array('token'=>authcode("$setarr[password]\t$setarr[uid]", 'ENCODE')));
		}
	} else {
		$errmessage = 'login failure,please login again';
	}
}
open_showmessage($_SGLOBAL['open_errorinfo'][1000],$errmessage);
?>