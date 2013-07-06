<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: do_login.php 13210 2009-08-20 07:09:06Z liguode $
*/

if(!defined('IN_TEAMDOTA')) {
	exit('Access Denied');
}

if($_SGLOBAL['supe_uid']) {
	showmessage('do_success', "group.php?do=home", 0);
}

$refer = empty($_GET['refer'])?rawurldecode($_SCOOKIE['_refer']):$_GET['refer'];
if(empty($refer)) {
	$refer = 'group.php?do=home';
}
$errmessage = '';
if(submitcheck('loginsubmit')) {

	$password = $_POST['password'];
	$username = strtolower(trim($_POST['username']));
	$cookietime = intval($_POST['cookietime']);
	
	$cookiecheck = $cookietime?' checked':'';
	
	if(empty($username)) {
		$errmessage = cplang('email_format_is_wrong');
	} elseif(!isemail($username)) {
		$errmessage = cplang('email_format_is_wrong');
	} elseif(empty($password)) {
		$errmessage = cplang('password_were_not_empty_please_re_login');
	} else {
		$passwordmd5 = preg_match('/^\w{32}$/', $password) ? $password : md5($password);
		//检索当前用户
		$query = $_SGLOBAL['db']->query("SELECT password,salt,group_id,uid,email,status,username FROM ".tname('member')." WHERE `username`='{$username}' LIMIT 1");
		if($user = $_SGLOBAL['db']->fetch_array($query)) {
			if($user['status'] == 1){
				$errmessage = cplang('login_failure_please_re_login');
			} elseif($user['password'] != md5($passwordmd5.$user['salt'])) {
				$errmessage = cplang('login_failure_please_re_login');
			} else {
				$setarr = array(
					'uid' => $user['uid'],
					'username' => addslashes($user['username']),
					'password' => addslashes($user['password'])
				);

				//清理在线session
				insertsession($setarr);
				
				//设置cookie
				ssetcookie('auth', authcode("$setarr[password]\t$setarr[uid]", 'ENCODE'), $cookietime);
				ssetcookie('_refer', '');

				if(empty($_POST['refer'])) {
					$_POST['refer'] = 'group.php?do=home';
				}
				showmessage('login_success',$_POST['refer'], 0);
			}
		} else {
			$errmessage = cplang('login_failure_please_re_login');
		}
	}
}
$cookiecheck = ' checked="checked"';

include template('do_login');

?>