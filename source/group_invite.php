<?php
/*
	[TEAMDOTA] (C) 2012 wanghui
	$Id: group_invite.php 2012-03-31 09:59Z duty $
*/

if(!defined('IN_TEAMDOTA')) {
	exit('Access Denied');
}
$project_id = empty($_GET['project_id']) ? 0 : intval($_GET['project_id']);
if(empty($project_id)){
	showmessage('project_not_allowed_to_visit','group.php?do=home');
}
$manageproject = checkproject($project_id);
if(!$manageproject) {
	showmessage('project_not_allowed_to_visit','group.php?do=home');
}
if($manageproject['status'] == 2) {//处于删除状态
	dheader("location: group.php?do=project&project_id={$project_id}");
}

$query = $_SGLOBAL['db']->query("SELECT m.fullname,m.email,m.uid,pm.type,pm.isactive,pm.logtime FROM ".tname('project_member')." pm,".tname('member')." m WHERE pm.uid=m.uid AND pm.project_id='{$project_id}'");
$members = array();
while($value = $_SGLOBAL['db']->fetch_array($query)) {
	$members[] = $value;
}
include_once template("group_invite");
?>