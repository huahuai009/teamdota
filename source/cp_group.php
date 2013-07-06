<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: cp_discussion.php 2012-03-31 09:59Z duty $
*/

if(!defined('IN_TEAMDOTA')) {
	exit('Access Denied');
}
if($_SGLOBAL['supe_uid'] != $group['uid']) {
	showmessage('links_does_not_exist');
}
//编辑帐户名
if(submitcheck('groupsubmit')) {
	$new_group_name = trim($_POST['account_name']);
	if(empty($new_group_name)) {
		showmessage('group_format_is_wrong', 'cp.php?ac=group');
	}
	updatetable('group', array('group_name'=>$new_group_name), array('group_id'=>$groupid));
	showmessage('do_success','cp.php?ac=group',0);
}
//编辑帐户所有者
if(submitcheck('groupownersubmit')) {
	$owner_id = empty($_POST['owner_id']) ? 0 : intval($_POST['owner_id']);
	if(empty($owner_id)) {
		showmessage('links_does_not_exist','cp.php?ac=group');
	}
	if($owner_id == $group['uid']) {
		showmessage('do_success','cp.php?ac=group',0);
	}
	//用户信息
	$query = $_SGLOBAL['db']->query("SELECT uid,group_id FROM ".tname('member')." WHERE uid='{$owner_id}' LIMIT 1");
	if(!$member = $_SGLOBAL['db']->fetch_array($query)) {
		showmessage('links_does_not_exist');
	}
	if($groupid != $member['group_id']){
		showmessage('links_does_not_exist');
	}
	updatetable('group', array('uid'=>$owner_id), array('group_id'=>$groupid));
	updatetable('member', array('ntype'=>1), array('uid'=>$owner_id));
	updatetable('member', array('ntype'=>2), array('uid'=>$_SGLOBAL['supe_uid']));
	showmessage('do_success','group.php?do=home',0);
}
//读取成员
$query = $_SGLOBAL['db']->query("SELECT uid,fullname FROM ".tname('member')." WHERE group_id='{$groupid}' ORDER BY regdate ASC");
$list = array();
while($value = $_SGLOBAL['db']->fetch_array($query)) {
	$list[] = $value;
}
include_once template("cp_group");
?>