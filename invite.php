<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: invite.php 2012-03-31 09:59Z duty $
*/

include_once('./common.php');

//是否关闭站点
checkclose();

$invite = array();
$get = array();
//参数
$get = empty($_SERVER['QUERY_STRING'])?'':$_SERVER['QUERY_STRING'];
$invite = getinvite($get);
$theurl = "invite.php?$get";

if(submitcheck('invitesubmit')) {
	$fullname = $_POST['fullname'];
	$password = $_POST['password'];
	$password_confirmation = $_POST['password_confirmation'];
	if(empty($fullname)) {
		showmessage('fullname_format_is_wrong', $theurl);
	}
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
	if(invite_project($invite,$fullname,$password)) {
		if($invite['project_id'] == 0) {
			dheader('location:group.php?do=home');
		} else {
			dheader('location:group.php?do=project&project_id='.$invite['project_id']);
		}
		exit();
	} else {
		showmessage('invite_code_error');
	}
}

include_once template('invite');

function getinvite($invite) {
	global $_SGLOBAL;
	
	$id = 0;
	$code = '';

	$invite_len = strlen($invite);
	if($invite_len > 32) {
		$code = addslashes(substr($invite, -32));
		$id = intval(str_replace($code, '', $invite));
	}
	if(empty($id)) {
		showmessage('invite_code_error');
	}
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('invite')." WHERE id='$id' AND code='$code'");
	if(!$invite = $_SGLOBAL['db']->fetch_array($query)) {
		showmessage('invite_code_error');
	}
	if(!$invite['type']) {
		showmessage('invite_code_error');
	}
	//判断群组空间的状态
	checkgroup_status($invite['group_id']);
	
	return $invite;
}

?>