<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: group.php 2012-03-31 09:59Z duty $
*/

include_once('./common.php');

//是否关闭站点
checkclose();

//需要登录
checklogin();

//允许动作
$dos = array('home','project', 'people', 'discussion', 'attachment', 'document', 'download', 'invite', 'ajax', 'search', 'trash', 'account_project', 'todos', 'todoslist', 'todoslist_completed');

//获取变量
$do = (!empty($_GET['do']) && in_array($_GET['do'], $dos))?$_GET['do']:'home';

//获取空间
$groupid = $_SGLOBAL['member']['group_id'];
$group = getgroup($groupid);

if($group) {
	
	$_SGLOBAL['group_is_time_end'] = 0;

	//验证空间是否被锁定
	if($group['flag'] == 1) {
		showmessage('group_has_been_locked');
	}
} else{
	showmessage('group_not_allowed_to_visit');
}

//更新活动session
if($_SGLOBAL['supe_uid']) {
	updatetable('session', array('lastactivity' => $_SGLOBAL['timestamp']), array('uid'=>$_SGLOBAL['supe_uid']));
}

//处理
include_once(S_ROOT."./source/group_{$do}.php");

?>