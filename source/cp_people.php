<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: cp_discussion.php 2012-03-31 09:59Z duty $
*/

if(!defined('IN_TEAMDOTA')) {
	exit('Access Denied');
}
$theurl = 'cp.php?ac=people&op=edit';
//添加编辑操作
if(submitcheck('peoplesubmit')) {
	$fullname = $_POST['fullname'];
	$password = $_POST['password'];
	$password_confirmation = $_POST['password_confirmation'];
	$isavatar = 0;
	if(empty($fullname)) {
		showmessage('fullname_format_is_wrong', $theurl);
	}
	if(!empty($password)) {
		if($password != addslashes($password)) {
			showmessage('profile_passwd_illegal', $theurl);
		}
		if($password != $password_confirmation) {
			showmessage('password_inconsistency', $theurl);
		}
		if($_FILES["signal_id_identity_avatar"]) {
			$avatar = avatar_save($_FILES["signal_id_identity_avatar"]);
			if($avatar && is_array($avatar)) {
				$isavatar = 1;
			} 
		}
		//更新用户密码
		$salt = substr(uniqid(rand()), -6);
		$mpwd = md5(md5($password).$salt);
		if($isavatar) {
			updatetable('member', array('fullname'=>$fullname,'password'=>$mpwd,'salt'=>$salt,'isavatar'=>1), array('uid'=>$_SGLOBAL['supe_uid']));
		} else {
			updatetable('member', array('fullname'=>$fullname,'password'=>$mpwd,'salt'=>$salt), array('uid'=>$_SGLOBAL['supe_uid']));
		}
		//设置cookie
		ssetcookie('auth', authcode("$mpwd\t$_SGLOBAL[supe_uid]", 'ENCODE'));
	} else {
		if($_FILES["signal_id_identity_avatar"]) {
			$avatar = avatar_save($_FILES["signal_id_identity_avatar"]);
			if($avatar && is_array($avatar)) {
				$isavatar = 1;
			}
		}
		if($isavatar) {
			updatetable('member', array('fullname'=>$fullname,'isavatar'=>1), array('uid'=>$_SGLOBAL['supe_uid']));
		} else {
			updatetable('member', array('fullname'=>$fullname), array('uid'=>$_SGLOBAL['supe_uid']));
		}
	}
	showmessage('do_success',"group.php?do=people&uid={$_SGLOBAL[supe_uid]}");
}

if($_GET['op'] == 'edit') {
	include_once template("cp_people_edit");
}
?>