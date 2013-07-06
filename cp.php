<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: cp.php 13003 2012-03-31 09:59Z duty $
*/

//通用文件
include_once('./common.php');

include_once(S_ROOT.'./source/function_cp.php');
//允许的方法
$acs = array('common', 'project','discussion', 'upload', 'document', 'attachment', 'post', 'invite', 'people_permissions', 'people', 'group', 'people_new', 'plans', 'people_view_permissions', 'people_settings', 'todos', 'todoslist');
$ac = (empty($_GET['ac']) || !in_array($_GET['ac'], $acs))?'profile':$_GET['ac'];
$op = empty($_GET['op'])?'':$_GET['op'];

//权限判断
if(empty($_SGLOBAL['supe_uid'])) {
	if($_SERVER['REQUEST_METHOD'] == 'GET') {
		ssetcookie('_refer', rawurlencode($_SERVER['REQUEST_URI']));
	} else {
		ssetcookie('_refer', rawurlencode('cp.php?ac='.$ac));
	}
	showmessage('to_login', 'do.php?ac=login');
}

//获取空间信息
$groupid = $_SGLOBAL['member']['group_id'];
$group = getgroup($groupid);
if(empty($group)) {
	showmessage('group_not_allowed_to_visit');
}

$_SGLOBAL['group_is_time_end'] = 0;

//是否关闭站点
if(!in_array($ac, array('common'))) {
	checkclose();
	//空间被锁定
	if($group['flag'] == 1) {
		showmessage('group_has_been_locked');
	}
}

include_once(S_ROOT.'./source/cp_'.$ac.'.php');

?>