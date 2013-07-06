<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: do_register.php 13111 2009-08-12 02:39:58Z liguode $
*/

if(!defined('IN_TEAMDOTA')) {
	exit('Access Denied');
}

if($_SGLOBAL['supe_uid']) {
	showmessage('do_success', "group.php?do=home", 0);
}

if($_SCONFIG['closeregister']) {
	showmessage('not_open_registration');
}

//是否关闭站点
checkclose();

if(submitcheck('registersubmit')) {

	$password = $_POST['password'];
	$fullname = $_POST['fullname'];
	$fullnametrim = trim($fullname);
	$group_name = $_POST['group_name'];
	$group_nametrim = trim($group_name);
	$email = isemail($_POST['email'])? strtolower(trim($_POST['email'])) : '';
	$username = $email;
	if(empty($fullnametrim)) {
		showmessage('fullname_format_is_wrong', 'do.php?ac=register');
	}
	if(empty($email)) {
		showmessage('email_format_is_wrong', 'do.php?ac=register');
	}
	if(empty($password)) {
		showmessage('password_format_is_wrong', 'do.php?ac=register');
	}
	if(!$password || $password != addslashes($password)) {
		showmessage('profile_passwd_illegal', 'do.php?ac=register');
	}
	if(empty($group_nametrim)) {
		$group_name = $fullname;
	}
	//检查用户名
	//if($count = getcount('member', array('username'=>$username))) {
		//showmessage('user_name_already_exists', 'do.php?ac=register');
	//}
	//检查邮件
	if($count = getcount('member', array('email'=>$email))) {
		showmessage('email_has_been_registered', 'do.php?ac=register');
	}
	$onlineip = getonlineip();
	$setgrouparr = array(
		'group_name' => $group_name,
		'uid' => 0,
		'gtype' => 0,
		'useip' => $onlineip,
		'attachsize' => 0,
		'maxattachsize' => 0,
		'logtime' => $_SGLOBAL['timestamp'],
		'flag' => 0,
		'all_project_num' => 0,
		'project_num' => 0,
		'maxattachsize' => $_SCONFIG['group_attachsize']['0'],
		'endtime' => $_SGLOBAL['timestamp']+$_SCONFIG['freetrial']*24*3600
	); 
	//插入账户库
	$newgroupid = inserttable('group', $setgrouparr, 1);
	if($newgroupid <= 0) {
		showmessage('register_error', 'do.php?ac=register');
	} else {
		$salt = substr(uniqid(rand()), -6);
		$mpwd = md5(md5($password).$salt);
		$setmemberarr = array(
			'group_id' => $newgroupid,
			'uid' => $newuid,
			'username' => $username,
			'password' => $mpwd,
			'email' => $email,
			'fullname' => $fullname,
			'ntype' => 1,
			'regip' => $onlineip,
			'regdate' => $_SGLOBAL['timestamp'],
			'lastloginip' => 0,
			'lastlogintime' => $_SGLOBAL['timestamp'],
			'lastactivity' => $_SGLOBAL['timestamp'],
			'status' => 0,
			'salt' => $salt,
			'is_create_project' => 1,
			'timeoffset' => 8,
		);
		//更新本地用户库
		$newuid = inserttable('member', $setmemberarr, 1);
		updatetable('group', array('uid'=>$newuid), array('group_id'=>$newgroupid));

		//在线session
		$session = array('uid' => $newuid, 'username' => $username, 'password' => $mpwd);
		insertsession($session);

		//设置cookie
		ssetcookie('auth', authcode("$session[password]\t$session[uid]", 'ENCODE'), 2592000);
		ssetcookie('_refer', '');

		showmessage('registered', 'group.php?do=home',0);
	}

}

include template('do_register');
?>