<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: do_login.php 13210 2009-08-20 07:09:06Z liguode $
*/

if(!defined('IN_TEAMDOTA')) {
	exit('Access Denied');
}

if(submitcheck('emailsubmit')) {

	$email_address = strtolower(trim($_POST['email_address']));
	
	if(empty($email_address)) {
		showmessage('email_format_is_wrong', 'do.php?ac=forgot_password');
	}
	if(!isemail($email_address)) {
		showmessage('email_format_is_wrong', 'do.php?ac=forgot_password');
	}
	//检索当前用户
	$query = $_SGLOBAL['db']->query("SELECT uid,status,email,fullname,username FROM ".tname('member')." WHERE `email`='{$email_address}' LIMIT 1");
	if($member = $_SGLOBAL['db']->fetch_array($query)) {
		//if($member['status'] == 1){
		//	showmessage('getpasswd_email_notmatch', 'do.php?ac=forgot_password');
		//}
		//发送电子邮件
		//获取唯一code
		$code = strtolower(md5(microtime().random(6)));
		$setarr = array(
			'uid' => $member['uid'],
			'code' => $code,
			'email' => $member['email'],
			'logtime' => $_SGLOBAL['timestamp'],
			'useip' => getonlineip(),
			'type' =>  1
		);
		$logid = inserttable('log_forgot_password', $setarr, 1);
		if($logid) {
			$reseturl = getsiteurl()."password_reset.php?{$logid}{$code}";
			$mail_subject = cplang('get_passwd_subject');
			$mail_message = cplang('get_passwd_message', array($member['fullname'],$member['username'],$member['username'],$reseturl,$reseturl));

			include_once(S_ROOT.'./source/function_cp.php');
			smail($member['email'], $mail_subject, $mail_message);
			showmessage('getpasswd_send_succeed','do.php?ac=forgot_password',5);
		} else {
			showmessage('getpasswd_send_error','/',5);
		}
		
	} else {
		showmessage('getpasswd_email_notmatch', 'do.php?ac=forgot_password');
	}
}

include template('do_forgot_password');

?>