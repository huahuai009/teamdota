<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: cp_people_view_permissions 2012-03-31 09:59Z duty $
*/

if(!defined('IN_TEAMDOTA')) {
	exit('Access Denied');
}
if($_SGLOBAL['member']['ntype'] <= 0) {
	showmessage('no_privilege');
}

if($_GET['op'] == 'edit') {
	include_once(S_ROOT.'./source/function_delete.php');
	$uid = empty($_GET['uid']) ? 0 : intval($_GET['uid']);
	$project_id = empty($_GET['project_id']) ? 0 : intval($_GET['project_id']);
	$editvalue = empty($_GET['editvalue']) ? 0 : intval($_GET['editvalue']);//
	if(empty($uid) || empty($project_id)){
		showmessage('no_privilege_manage_group_members');
	}
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('member')." WHERE uid='$uid'");
	$emember = $_SGLOBAL['db']->fetch_array($query);
	if(!empty($emember)) {
		if($groupid != $emember['group_id']) {
			showmessage('no_privilege_manage_group_members');
		}
		if($emember['ntype'] == 1) {
			showmessage('no_privilege_manage_group_members');
		}
		if($editvalue == 0) {
			deleteprojects_member($project_id,$uid);
		} else {
			restored_projects_member($project_id,$uid);
		}
		showmessage('do_success','cp.php?ac=people_view_permissions');
	} else {
		showmessage('no_privilege_manage_group_members');
	}
} elseif($_GET['op'] == 'delete') {
	include_once(S_ROOT.'./source/function_delete.php');
	$uid = empty($_GET['uid']) ? 0 : intval($_GET['uid']);
	if(empty($uid)) {
		showmessage('no_privilege_manage_group_members');
	}
	if(delete_member($uid)) {
		showmessage('do_success','group.php?do=people',0);
	} else {
		showmessage('failed_to_delete_operation');
	}
}

$uid = empty($_GET['uid'])?0:intval($_GET['uid']);
if(empty($uid)) {
	showmessage('links_does_not_exist');
}
//用户信息
$query = $_SGLOBAL['db']->query("SELECT uid,group_id,fullname,ntype,is_create_project FROM ".tname('member')." WHERE uid='{$uid}' LIMIT 1");
if(!$member = $_SGLOBAL['db']->fetch_array($query)) {
	showmessage('links_does_not_exist');
}
if($groupid != $member['group_id']){
	showmessage('links_does_not_exist');
}
if($_SGLOBAL['supe_uid'] == $member['uid']){
	showmessage('no_privilege_manage_group_members');
}
if($member['ntype'] == 1) {
	showmessage('no_privilege_manage_group_members');
}
//读取活跃项目
$query_project = $_SGLOBAL['db']->query("SELECT p.project_id,p.group_id,p.name FROM ".tname('project')." p , ".tname('project_member')." pm WHERE p.project_id=pm.project_id AND p.group_id='{$groupid}' AND pm.uid='".$_SGLOBAL['supe_uid']."' AND p.status=0");
$projects = array();//活跃项目
$string_projects = '';
while($value = $_SGLOBAL['db']->fetch_array($query_project)) {
	$query_m = $_SGLOBAL['db']->query("SELECT id FROM ".tname('project_member')." WHERE project_id='{$value[project_id]}' AND uid='{$uid}' LIMIT 1");
	if($value_m = $_SGLOBAL['db']->fetch_array($query_m)) {
		$value['isexist'] = 1;
	} else {
		$value['isexist'] = 0;
	}
	$projects[] = $value;
	if($string_projects == '') {
		$string_projects = $value['project_id'];
	} else {
		$string_projects .= ','.$value['project_id'];
	}
}
include_once template("cp_people_view_permissions");

?>